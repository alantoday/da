<?php
# USAGE: da.digitalaltitude.co/crons/calc_comms.php?debug=1&inf_payment_id=291

define ("DEBUG", $_GET['debug']);

require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_cp.php");
require_once("../includes/functions_twilio.php");

$inf_payment_id_sql = "";
if (isset($_GET['inf_payment_id'])) {
	$inf_payment_id_sql = "OR inf_payment_id={$_GET['inf_payment_id']}";
#	$query = "DELETE FROM commissions 
#			WHERE inf_payment_id = {$_GET['inf_payment_id']}";
#	if (DEBUG) EchoLn($query);
#	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
}

# Get all payments to process
$query = "SELECT p.inf_payment_id, p.inf_contact_id, p.inf_invoice_id, p.pay_amt, p.pay_date
				, m.member_id, m.sponsor_id, m.sponsor_unknown
				, mc.coach_id_startup, mc.coach_id_scale
				, f.fee_type, f.fee_type_amt
			FROM inf_payments p
			LEFT JOIN commissions c USING (inf_payment_id)
			JOIN members m USING (inf_contact_id)
			LEFT JOIN member_coaches mc ON mc.member_id=m.member_id
				AND mc.start_date <= p.pay_date
				AND (mc.end_date IS NULL OR mc.end_date >= p.pay_date)			
			LEFT JOIN fees f ON f.pay_type = p.pay_type
				AND f.start_date <= p.pay_date 
				AND (f.end_date IS NULL OR f.end_date >= p.pay_date)
			WHERE (c.inf_payment_id IS NULL
					$inf_payment_id_sql
				  )
 				  AND p.pay_type <> 'Credit'
			ORDER BY inf_payment_id ASC
			LIMIT 500";
//				, i.synced AS invoice_synced, i.product_sold
//				, ip.skip_commission
if (DEBUG) EchoLn($query);       
$result = mysqli_query($db, $query) or die(mysqli_error($db));
while($row = mysqli_fetch_assoc($result)){
	
	if (DEBUG) EchoLn("<br>Processing inf_payment_id: ".$row['inf_payment_id']);
#	$trans_amt = $row['trans_amt'] - $row['trans_amt_refunded'];
	
	$sa_aff_id = $row['coach_id_startup'];
	$products_pc = _GetProductPc($db, $row['inf_invoice_id']);
	if (DEBUG) WriteArray($products_pc);
	
	foreach ($products_pc as $inf_order_item_id => $product_details) {
		$product_amount = RoundDown($row['pay_amt']*(float)$product_details['pc']/100);
		if ($row['fee_type']=='percent') {
			$fee_amt = round($product_amount * $row['fee_type_amt']/100, 2);	
		} else {
			$fee_amt = round($row['fee_type_amt']*(float)$product_details['pc']/100, 2);	
		}
		# Only pay commissions on after fee amount
		$product_amount -= $fee_amt;
		
		# Strip anything after the optional "#", eg, ape#10 (for special 10% down product)
		$product_type = strtok($product_details['product_type'], '#');
		
		_InsertCommissions($db, $inf_order_item_id, $row['inf_payment_id'], $product_amount, $row['pay_date'], $product_type
				, $row['member_id'], $row['sponsor_unknown'], $row['sponsor_id']
				, $product_details['product_name'], $product_details['sa_option'], $product_details['default_cp_type'], $sa_aff_id, $fee_amt);
	}
}
if (DEBUG) EchoLn("<br>=================================<br>DONE", GREEN);


#######################################################
# Get the list of products related to a payment and their relative percentages
# RETURN: array($pc => $pc, 'sa_option' => $sa_option)
# $sa_option, ie, is there an option for Sales Assist with this product?
function _GetProductPc($db, $inf_invoice_id) {

	# Get all the products/items in that order so we can divide the payment into the products
	# NOTE: The JOIN to inf_products will auto remove all -ve promo amount
	$query = "SELECT io.inf_order_item_id, io.inf_product_id, io.qty, io.ppu AS amt
				, p.product_type, p.sa_option, p.product_name, p.default_cp_type
			FROM inf_order_items io
			JOIN inf_products p USING (inf_product_id)
			JOIN inf_invoices i ON i.inf_invoice_id = io.inf_order_id
			WHERE io.inf_order_id = '$inf_invoice_id'";
	if (DEBUG) EchoLn($query);       
	$result = mysqli_query($db, $query) or die(mysqli_error($db));

	$res = array();
	$invoice_total = 0;
	$products = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$invoice_total += $row['amt'];
		$products[$row['inf_order_item_id']] = $row['amt'];
		$res[$row['inf_order_item_id']]['product_type'] = $row['product_type'];
		$res[$row['inf_order_item_id']]['sa_option'] = $row['sa_option'];
		$res[$row['inf_order_item_id']]['product_name'] = $row['product_name'];
		$res[$row['inf_order_item_id']]['default_cp_type'] = $row['default_cp_type'];
	}
	foreach ($products as $product_type => $amt) {
		$res[$product_type]['pc'] = floor($amt*100/$invoice_total);
	}

	return $res;
}

#######################################################
# 
function _GetTier1AffId($db, $member_id, $product_type, $date) {
	
	# Just in case a full product type is input eg, bas#10
	$product_type = strtok($product_type, '#');

	$query = "SELECT tier1_aff_id
			FROM member_ranks mr
			WHERE mr.member_id = $member_id
			AND mr.product_type = '$product_type'
			AND mr.start_date <= '$date'
				AND (mr.end_date IS NULL OR mr.end_date >= '$date')";
	if (DEBUG) EchoLn($query);       
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	If ($row = mysqli_fetch_assoc($result)) {
		return 	$row['tier1_aff_id'];
	} else {
		// In case their is not a record then calculate it on the spot
		return CPGetTier1AffId($db, $sponsor_row, $product_type, $date);
	}
}
	

#######################################################
# INSERT (or replace) commission for a transaction
# INPUT: sa_option is Sales Assist and option for this product
function _InsertCommissions($db, $inf_order_item_id, $inf_payment_id, $trans_amt, $trans_date, $product_type, $member_id, $sponsor_unknown, $sponsor_id, $product_name, $sa_option, $default_cp_type, $sa_aff_id, $fee_amt) {

	if (DEBUG) EchoLn("_InsertCommission(member_id=$member_id, product_type=$product_type");       

	# Find out who their Sponsor Locked Tier 1 sponsor is
	$tier1_aff_id = CPGetTier1AffId($db, $sponsor_id, $product_type, $trans_date);
	
	$tier1_details = _GetTierAffComms($db, $tier1_aff_id, $trans_date, $product_type, $default_cp_type, 1); // Tier1
if (DEBUG) WriteArray($tier1_details);
	$tier2_details = _GetTierAffComms($db, $tier1_details['next_aff_id'], $trans_date, $product_type, $default_cp_type, 2); // Tier2
	$tier3_details = _GetTierAffComms($db, $tier2_details['next_aff_id'], $trans_date, $product_type, $default_cp_type, 3); // Tier3

	// Look for any rollup, ie, if we've paid out less than 3% on Tier 1
	$tier1_up_aff_id = 0;
	$tier1_up_amt = 0;
	if ($tier1_details['comms_pc'] <= CP_MIN_ROLLUP_PC) {
		# Give to Tier2 if qualifed; must be Active Aff, Own Product and ASPIRE Climber who owns that product
		if ($tier2_details['active_aff'] && $tier2_details['own_product']==true && inarray($tier2_details['aspire_level'], array("asp-h","asp-c"))) {
			$tier1_up_aff_id = $tier2_details['aff_id'];
		}

		# Otherwise give to Tier3 if qualifed
		elseif ($tier3_details['active_aff'] && $tier3_details['own_product']==true && inarray($tier3_details['aspire_level'], array("asp-c"))) {
			$tier1_up_aff_id = $tier3_details['aff_id'];
		}
		# Otherwise it goes unpaid
		$tier1_up_amt = CP_MAX_ROLLUP_PC - $tier1_details['comms_pc'];
	}
if (DEBUG) EchoLn("AMT: ".$trans_amt."-".$tier1_details['comms_pc']);
	// Is Sales Assist and option for this product
	if ($sa_option) {
		$sa_amt = RoundDown($tier1_details['sa_pc']/100 * $trans_amt);
		// Handle case when comms_pc is only 3% (ie, aff only). Then don't take away sa_pc
		if ($tier1_details['comms_pc'] > $tier1_details['sa_pc']) {
			$tier1_amt = RoundDown(($tier1_details['comms_pc']-$tier1_details['sa_pc'])/100 * $trans_amt);
		} else {
			// eg, for 3% Aff only commissions
			$tier1_amt = RoundDown(($tier1_details['comms_pc'])/100 * $trans_amt);			
		}
		$tier1_up_amt = 0;
	} else {
		$sa_aff_id = 0;
		$sa_amt = 0;
		$tier1_amt = RoundDown(($tier1_details['comms_pc'])/100 * $trans_amt);
		$tier1_up_amt = 0;
	}

	$tier2_amt = round($tier2_details['comms_pc']/100 * $trans_amt, 2);
	$tier3_amt = round($tier3_details['comms_pc']/100 * $trans_amt, 2);
	$company_amt = round($tier1_details['company_pc']/100 * $trans_amt, 2);
	$unpaid_amt = round($tier1_details['unpaid_pc']/100 * $trans_amt, 2);
	$trans_amt = round($trans_amt, 2);
	
	$tier1_unpaid_amt = $tier2_unpaid_amt = $tier3_unpaid_amt = 0;
	# Final test
	# If you are getting tier1_up_amt commissions then you don't also get Tier 2 or Tier 3.
	if ($tier1_up_amt) {
		// TODO Give to company in a better way
		if ($tier1_up_aff_id == $tier2_details['aff_id']) {		
			$tier2_details['aff_id'] = 0;
			$tier2_unpaid_amt = $tier2_amt;
			$tier2_amt = 0; 
		} elseif ($tier1_up_aff_id == $tier3_details['aff_id']) {
			$tier3_details['aff_id'] = 0;			
			$tier3_unpaid_amt = $tier3_amt;
			$tier3_amt = 0; 
		}
	}
	$overpaid_amt = round(-1 * ($trans_amt - $sa_amt - $tier1_amt - $tier2_amt - $tier3_amt - $company_amt - $unpaid_amt), 2);
	
	# NOTE: Primary Key is inf_payment_id + Inf_order_item_id
	$query = "REPLACE INTO commissions
				SET inf_payment_id=$inf_payment_id
				, inf_order_item_id	=$inf_order_item_id
				, sponsor_id	=$sponsor_id
				, trans_amt		=$trans_amt
				, trans_date	='$trans_date'
				, product_type	='$product_type'
				, fee_amt		=$fee_amt
				, sa_amt		=$sa_amt
				, sa_aff_id 	='$sa_aff_id'
				, tier1_amt		=$tier1_amt
				, tier2_amt		=$tier2_amt
				, tier3_amt		=$tier3_amt
				, tier1_up_amt	=$tier1_up_amt
				, tier1_aff_id	={$tier1_details['aff_id']}
				, tier2_aff_id	={$tier2_details['aff_id']}
				, tier3_aff_id	={$tier3_details['aff_id']}
				, tier1_up_aff_id=$tier1_up_aff_id
				, company_amt	=$company_amt
				, unpaid_amt	=$unpaid_amt
				, tier1_unpaid_amt	=$tier1_unpaid_amt
				, tier2_unpaid_amt	=$tier2_unpaid_amt
				, tier3_unpaid_amt	=$tier3_unpaid_amt
				, overpaid_amt	=$overpaid_amt
				, tier1_cp_id	='{$tier1_details['cp_id']}'
				, tier2_cp_id	='{$tier2_details['cp_id']}'
				, tier3_cp_id	='{$tier3_details['cp_id']}'
				, create_date	=NOW()";            
	if (DEBUG) EchoLn("<br>---------------------------<br>$query");       
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	
	# Send commission emails to Affialites (if Sponsor Known). <br />
	# And only if transaction is for today (so we don't blast sponsor for old commissions
	# TODO (MAYBE) Log in separate table which inf_payment_id (and $product_type) we have already emailed
	# so we dont' re-email/text them if we do bulk re-calc
	if (!DEBUG && !$sponsor_unknown && substr($trans_date,1,10) == date("Y-m-d")) {
		_SendCommissionEmail($db, $member_id, $tier1_aff_id, $product_name, 1, $amount);
		_SendCommissionEmail($db, $member_id, $tier2_aff_id, $product_name, 2, $amount);
		_SendCommissionEmail($db, $member_id, $tier3_aff_id, $product_name, 3, $amount);
		if ($sa_amt > 0) {
#			_SendCommissionEmail($db, $member_id, $tier3_aff_id, $product_name, "COACH", $amount);	
		}
	}
}

########################################
# Look up the sponsor tree until we find an active sponsor who is qualified
# for that product on that tier
# 0. Is product commissionable - if not then send to company
# 1. Keep looking to find out if they an an affiliate (that's all they need to qualify)
# 2. If they are an affiliate: Do they own the product or not?
# 3. If they own the product: Are they Walker Hiker Climber?
# 4. What sales assist % should we use (if any)
# RETURNS: array($aff_id, $comms_pc, $sa_pc, $company_pc);
# - Note: $company_pc, $sa_pc will always be blank for tiers 2 & 3.
function _GetTierAffComms($db, $sponsor_id, $trans_date, $full_product_type, $default_cp_type, $tier) {

	if (DEBUG) EchoLn("<br>TIER $tier: sponsor_id=$sponsor_id, trans_date=$trans_date, full_product_type=$full_product_type, default_cp_type=$default_cp_type");
	$next_aff_id = $sponsor_id;
	$comms_pc = 0;
	# Strip anything after the optional "#", eg, ape#10 (for special 10% down product)
	$product_type = strtok($full_product_type, '#');
	
	$active_aff = 0;
	$own_product = 0;
	$aspire_level = "";

	# If it's non-commissionsable then skip the rest
	if ($default_cp_type == 0) {
		$res['aff_id'] = 0;  // Michael Force
		$res['next_aff_id'] = 0;
		$res['comms_pc'] = 0;
		$res['cp_id'] = 1;
		$res['active_aff'] = $active_aff;
		$res['own_product'] = $own_product;
		$res['aspire_level'] = $aspire_level;
		if ($tier == 1) {
			$res['sa_pc'] = 0;
			$res['company_pc'] = 100;
			$res['unpaid_pc'] = 0;
			$res['up_aff_id'] = 0;
		}
		return $res;
	}
		
	# Default: CP Type (eg, if they don't own the product we still know what CP)
	$cp_type_sql = "AND cp.cp_type = '$default_cp_type'";

	# Repeat until we find a sponsor that is qualified, ie, that has a commission value for that tier
	#
	# TODO: Ideally they should have at least that rank (and aff status) now - as well as when
	# the member below them joined, ie, they lock in their roll up commission people for life
	# Ie, the data the member below them purchased
	#
		$this_aff_id = $next_aff_id;
		if (DEBUG) EchoLn("1. Is Affiliate (sponsor_id=$sponsor_id, product_type=$product_type, tier=$tier, this_aff_id=$this_aff_id, default_cp_type=$default_cp_type)");
		
		# Default: Don't own ASPIRE Hiker/Walker/Climber
		$aspire_level_sql = "AND ((walker = 0 AND hiker = 0 AND climber = 0) OR (walker IS NULL))";
		# Default: They don't own product
		$own_product_sql2 = "AND (own_product = 0 OR own_product IS NULL)";

		# 1. IS THE SPONSOR AN ACTIVE AFFILIATE
		if (WriteAffStatus($db, $this_aff_id, $bool = true, $trans_date)) {
			$active_aff = 1;
			if (DEBUG) EchoLn(" ==> Yes Affiliate", GREEN);
			# 2. DO THEY OWN THE PRODUCT (or higher version of product, eg, ASPIRE Climber "owns" Walker
			# (and if so what cp_type do they have)
			
			# 2. a) For APSIRE Walker/Hiker/Climber products
			if ($full_product_type == "asp-w") {
				$own_product_sql = "AND mr.product_type IN ('asp-w','asp-h','asp-c')";
			} elseif ($full_product_type == "asp-h") {
				$own_product_sql = "AND mr.product_type IN ('asp-h','asp-c')";
			} elseif ($full_product_type == "asp-c") {
				$own_product_sql = "AND mr.product_type IN ('asp-c')";
			} else {
				$own_product_sql = "AND mr.product_type = '$full_product_type'";
			}
			$query = "SELECT mr.product_type, mr.cp_type
						FROM members m
						JOIN member_ranks mr ON m.member_id = mr.member_id 
							$own_product_sql
							AND mr.start_date <= '$trans_date' 
							AND (mr.end_date IS NULL OR mr.end_date >= '$trans_date')
						WHERE m.member_id='$this_aff_id'";
			if (DEBUG) EchoLn("2. Do they own product ($product_type): $query");
			$result = mysqli_query($db, $query) or die($query . mysqli_error($db));			

			if ($own_aspire_row = mysqli_fetch_assoc($result)) {
				# They own product
				if (DEBUG) EchoLn(" ==> Yes Own ($product_type)", GREEN);

				$own_product_sql2 = "AND (own_product = 1 OR own_product IS NULL)";
				$cp_type_sql = "AND cp.cp_type = '{$own_aspire_row['cp_type']}'";

				# 3. IS THE SPONSOR A ASPIRE Walker, Hiker, Climber?
				$query = "SELECT mr.product_type
							FROM members m
							JOIN member_ranks mr ON m.member_id = mr.member_id 
								AND substring(mr.product_type,1,3)='asp'
								AND mr.start_date <= '$trans_date' 
								AND (mr.end_date IS NULL OR mr.end_date >= '$trans_date')
							JOIN inf_products p USING (product_type)
							WHERE m.member_id='$this_aff_id'
							ORDER BY p.product_order DESC";
				if (DEBUG) EchoLn("3. Is Sponsor ASPIRE Walker, Hiker, Climber: $query");
	
				$result = mysqli_query($db, $query) or die($query . mysqli_error($db));
				
				if ($row = mysqli_fetch_assoc($result)) {
					# Own APIRE Hiker/Walker/Climber
					if ($row['product_type']=="asp-w") {
						if (DEBUG) EchoLn(" ==> ASPIRE Walker", GREEN);			
						$aspire_level = "asp-w";		
						$aspire_level_sql = "AND (walker = 1 OR walker IS NULL)";
					} elseif ($row['product_type']=="asp-h") {
						if (DEBUG) EchoLn(" ==> ASPIRE Hiker", GREEN);					
						$aspire_level = "asp-h";		
						$aspire_level_sql = "AND (hiker = 1 OR hiker IS NULL)";
					} elseif ($row['product_type']=="asp-c") {
						if (DEBUG) EchoLn(" ==> ASPIRE Climber", GREEN);					
						$aspire_level = "asp-c";		
						$aspire_level_sql = "AND (climber = 1 OR climber IS NULL)";
					}
				} else {
					if (DEBUG) EchoLn(" ==> Not ASPIRE H/C/W", RED);					
				}
			} else {
				if (DEBUG) EchoLn(" ==> Don't Own ($product_type)", RED);
			}
		} else {
			if (DEBUG) EchoLn(" ==> Not Affiliate", RED);
			# Not an Affiliate - keep looking up
			# TODO: Not any more it should go to corporate.
#			continue;	
		}

		# Get the sponsor's details (use the default Sales Assist % unless they have on set in member_log_sa
		$query = "SELECT m.sponsor_id AS next_aff_id, m.top
						, cp.*
					FROM members m
					JOIN comp_plan_v2 cp ON cp.status = 1 
						AND cp.start_date <= '$trans_date' 
						AND (cp.end_date IS NULL OR cp.end_date >= '$trans_date')
						$cp_type_sql
						AND aff = $active_aff
						$aspire_level_sql
						$own_product_sql2
					WHERE m.member_id='$this_aff_id'
					ORDER BY cp.priority
					LIMIT 1";
		if (DEBUG) EchoLn("4. Get Comp Plan: $query");
		$result = mysqli_query($db, $query) or die($query . mysqli_error($db));
		$cp_row = mysqli_fetch_assoc($result);
		if (!$cp_row) {
			if (DEBUG) EchoLn("COMP PLAN ERROR $query", RED);
			else SendEmail($db, "alantoday@gmail.com", "Comp Plan Error", "$query", "Digital Altitude <support@digitalaltitude.co>");	
			exit;
		}
		$next_aff_id = $cp_row['next_aff_id'];

		# Default: Sales Assist for ALL Tiers (it's only paid on Tier 1)
		$sa_pc = 0;
		if ($tier == 1) {
			# Default: Sales Assist for Tier 1
			$sa_pc = $cp_row['sa_pc_default'];
	
			# 5. What custom sales assist % should we use (if any)
			$query = "SELECT *
						FROM member_log_sa sa
						WHERE sa.start_date <= '$trans_date' 
						AND (sa.end_date IS NULL OR sa.end_date >= '$trans_date')
						AND sa.member_id='$this_aff_id'
						ORDER BY sa_pc
						LIMIT 1";
			if (DEBUG) EchoLn("5. What sales assist % should we use: $query");
			$result = mysqli_query($db, $query) or die($query . mysqli_error($db));
			if ($sa_row = mysqli_fetch_assoc($result)) {
				if (DEBUG) EchoLn(" ==> Warning: Something Custom ($sa_pc%)", RED);
				$sa_pc = $sa_row["sa_pc"];
			} else {
				if (DEBUG) EchoLn(" ==> Nothing Custom ($sa_pc%)", GREEN);				
			}
		}
		$next_aff_id = $cp_row['next_aff_id'];

	// TODO???? What if they are just an affiliate - does SQL above work? 
	
	$res['aff_id'] = $this_aff_id;
	$res['next_aff_id'] = $next_aff_id;
	$res['comms_pc'] = $cp_row["tier{$tier}_pc"];
	$res['cp_id'] = $cp_row["cp_id"];
	$res['active_aff'] = $active_aff;
	$res['own_product'] = $own_product;
	$res['aspire_level'] = $aspire_level;
	if ($tier == 1) {
		$res['sa_pc'] = $sa_pc;
		$res['company_pc'] = $cp_row["company_pc"];
		$res['unpaid_pc'] = $cp_row["unpaid_pc"];
		$res['up_aff_id'] = 0;
	}
	return $res;
}

#######################################################
# Get the list of products related to a payment and their relative percentages
# RETURN: array($pc => $pc, 'sa_option' => $sa_option)
# $sa_option, ie, is there an option for Sales Assist with this product?
function _SendCommissionEmail($db, $member_id, $aff_id, $product_name, $tier, $amt) {

	$member_row = GetRowMember($db, $member_id);
	$aff_row = GetRowMember($db, $aff_id);
	$aff_firstname = WriteFirstName($aff_row['name']);
	$amt_string = WriteDollarCents($amt);
	# Email Affiliate Unless the $amt is too low of they have notifications turned off
	if ($aff_row['notify_comms'] && $amt >= $aff_row['notify_comms_min']) {
		$subject = "You're making money! Celebrate a new Tier $tier $product_name sale";
		$msg = "Congratulations $aff_firstname,
	
Your Digital Altitude system is working with you and for you.
Today you can celebrate making some more money with this latest
Tier $tier $product_name Commission.

Name: {$member_row['name']}
Username: {$member_row['username']} 

Your Commission: $amt_string

You can find more details about your commissions here in the
My Business > My Earnings section of your Digital Altitude back office:

http://my.digitalaltitude.co/my-business/my-earnings.php

Keep up the GREAT work!

~Michael Force
Founder


UNSUBSCRIBE: You can choose to unsubscribe or adjust your subscription to 
these notifications in your Digital Altitude account here:
http://my.digitalaltitude.co/my-account/my-notifications.php


DISCLAIMER: We aim to send you accurate information in these emails, however, please
note that they are system generated and may have errors from time-to-time. So please don't
consider this email as a guarantee of any sales or commissions.
";
		SendEmail($db, $sponsor_row['email'], $subject, $msg, "Digital Altitude <support@digitalaltitude.co>");
#		mail($sponsor_row['email'], $subject, $msg, $header);
	}
}

?>

<?php
# USAGE: da.digitalaltitude.co/crons/calc_ranks_inf.php?debug=1&inf_payment_id=321

require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_cp.php");
require_once("../includes/functions_inf.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (!isset($_GET['test'])) {
#	exit;
}

/*
// MANUAL ADD SUBSCRIPTION
$inf_contact_id = 359;
$cards_array = InfGetCreditCards($inf_contact_id);	
# Find most recent cards
$card_id = max(array_keys($cards_array));

$days_till_charge = 14;
$inf_sub_plan_id = 23; // $37 a month
$price = 37;
$inf_sub_id = InfCreateRecurringOrder($inf_contact_id, $inf_sub_plan_id, $price, $card_id, $days_till_charge);
exit;
*/
/*
$inf_contact_id = 353;
WriteArray(InfGetRecurringOrders ($inf_contact_id, $inf_product_id=0));
exit;
*/

if (isset($_GET['reset'])) {
    if (DEBUG) EchoLn("RESET");
    # Get all payments to process
    # TODO - Handle refunds and voids elswhere ??
    $query = "UPDATE inf_payments
                SET member_rank_updated = 0";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
}
if (!empty($_GET['inf_payment_id'])) {
    $query = "UPDATE inf_payments
                SET member_rank_updated = 0
				WHERE inf_payment_id = {$_GET['inf_payment_id']}";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
}

# Get all payments to process
# TODO - Handle refunds and voids elswhere ??
$query = "SELECT s.cycle, s.frequency
				, pd.sub_product_id, pd.product_type, pd.inf_product_id
				, m.member_id, m.inf_contact_id
				, p.inf_payment_id, p.pay_date, o.*
            FROM inf_payments p
            JOIN members m USING (inf_contact_id)
            JOIN inf_order_items o ON o.inf_order_id = p.inf_invoice_id
            JOIN inf_products pd ON o.inf_product_id = pd.inf_product_id
			LEFT JOIN inf_sub_plans s on s.inf_sub_plan_id = o.subscription_plan_id
            WHERE p.pay_type NOT IN ('Void', 'Refund')                        
            AND p.member_rank_updated = 0";
# Allow $0 payments to count - so support can add them in INF
# AND p.pay_amt > 0
if (DEBUG) EchoLn ($query);      
$result = mysqli_query($db, $query) or die(mysqli_error($db));
while($row = mysqli_fetch_assoc($result)){

	# Add rank to match the product
	$new_status = _AddRank($db, $row, $row['product_type']);
	
	# Update payments record as having "member_rank_updated"
	$query = "UPDATE inf_payments
				SET member_rank_updated=$new_status
				WHERE inf_payment_id={$row['inf_payment_id']}
				";

	if (DEBUG) EchoLn ($query);      
	$result2 = mysqli_query($db, $query) or die(mysqli_error($db));
}
if (DEBUG) EchoLn ("Done");


# RETURN Status Code:
# 	1 = Successful rank updated
#   2 = Okay/Skip - Rank record already exists
#   9 = Error/Skip - No comp plan for that produc
function _AddRank($db, $payment_row, $product_type) {
	
	# Strip up until the #, eg, ap3#10 or asp-w#1
	$pos = strpos($product_type, "#");
	if ($pos) {
		$product_type = substr($product_type, 0, $pos);
	}
		
	# For non-subscriptions (one-time) products
	# Only need one member_rank record
	if (!$payment_row['sub_product_id']) {
		# Test if they already have that rank or not
		$query = "SELECT member_id
					FROM member_ranks
					WHERE member_id  = {$payment_row['member_id']}
					AND product_type = '$product_type'
					AND start_date  <='{$payment_row['pay_date']}'      
					AND (end_date IS NULL 
						OR end_date >='{$payment_row['pay_date']}')";
		if (DEBUG) EchoLn ($query);      
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		if($row = mysqli_fetch_assoc($result)){
			return 2; // Already have an active rank for that product
		}
	}

	# Put in an paid_thru_date and end_date for subscription products only
	$paid_thru_date_sql = "";
	$end_date_sql = "";
	if ($payment_row['sub_product_id']) {

		if (DEBUG) echo "inf_contact_d={$payment_row['inf_contact_id']}, sub_product_id = {$payment_row['sub_product_id']}";	
		$recurring_orders = InfGetRecurringOrders ($payment_row['inf_contact_id'],$payment_row['sub_product_id']);

		if (DEBUG) WriteArray($recurring_orders);	
		foreach($recurring_orders as $index => $recurring_order) {
			# Paid through date may be zerod
			$expire_date = (isset($recurring_order['PaidThruDate']) && $recurring_order['PaidThruDate']<>$recurring_order['StartDate'])  ? $recurring_order['PaidThruDate'] : $recurring_order['NextBillDate'];
			$paid_thru_date = date("Y-m-d H:i:s", strtotime($expire_date));
			$paid_thru_date_sql = ", paid_thru_date ='$paid_thru_date'";
	
			# Give them an extra 2 weeks of Grace
			$end_date = date("Y-m-d H:i:s", strtotime("+2 WEEK", strtotime($expire_date)));
			$end_date_sql = ", end_date ='$end_date'";
			break;
		}

/*		# Calc end date based on subscription
		$month_year = $payment_row['cycle']==1 ? "YEAR" : "MONTH";
		$frequency = $payment_row['frequency'];
		$end_date = date("Y-m-d H:i:s", strtotime("+$frequency $month_year", strtotime($payment_row['pay_date'])));
		$end_date_sql = ", end_date	='$end_date'";
*/
	}
	
    # Get comp plan that existing when transaction was produced (not when ordered).
	$query = "SELECT default_cp_type
				FROM inf_products
				WHERE product_type  = '$product_type'
				AND '{$payment_row['pay_date']}' >= start_date 
				AND (end_date IS NULL OR '{$payment_row['pay_date']}' <= end_date)";      
	if (DEBUG) EchoLn ($query);      
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if($row = mysqli_fetch_assoc($result)){		
		$default_cp_type = $row['default_cp_type'];
		$query = "SELECT member_rank_id, manual_adjustment
					FROM member_ranks
					WHERE inf_payment_id  	= {$payment_row['inf_payment_id']}
					AND member_id       	= {$payment_row['member_id']}
					AND product_type		='$product_type'";
		if (DEBUG) EchoLn ($query);      
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		$manual_adjustment = false;
		$member_rank_id_sql = "";
		if($row = mysqli_fetch_assoc($result)){
			$manual_adjustment = $row['manual_adjustment'];
			$member_rank_id_sql = ", member_rank_id = ".$row['member_rank_id'];
		}

		if (!$manual_adjustment) {
			# Get's member's sponsor_id so that we can...
			$member_row = GetRowMember($db, $payment_row['member_id']);

			# Calculate who the Product Sponsor should be (based on who ownes the product), and lock that in for "Sponsor Lock"
			$tier1_aff_id = CPGetTier1AffId($db, $member_row['sponsor_id'], $product_type, $payment_row['pay_date']);

			# Insert new ranks record
			# TODO - maybe think about deleting new records if they exist (or as a cron).
			$query = "REPLACE INTO member_ranks
						SET member_id       ={$payment_row['member_id']}
						, inf_payment_id    ={$payment_row['inf_payment_id']}
						, product_type      ='$product_type'
						, tier1_aff_id      ='$tier1_aff_id'
						, cp_type			='$default_cp_type'
						, start_date        ='{$payment_row['pay_date']}'
						$end_date_sql
						$paid_thru_date_sql
						, create_date       =NOW()
						$member_rank_id_sql
						";
			if (DEBUG) EchoLn ($query);      
			$result3 = mysqli_query($db, $query) or die(mysqli_error($db));
			return 1;
		} else {
			return 8;	
		}
	} else {
		# No comp plan for that product	
		return 9;
	}
}
?>

<?php

// Include the SDK
require_once('config.php');
require_once('functions.php');
require_once(PATH.'scripts/Infusionsoft/infusionsoft.php');
require_once(PATH.'scripts/Infusionsoft/examples/object_editor_all_tables.php');
require_once('functions_gr.php');
require_once('functions_twilio.php');
require_once('functions_email.php');
require_once('functions_track.php');

# TEST USAGE: http://da.digitalaltitude.co/includes/functions_inf.php?debug=1&TestEmails=1
if (isset($_GET['TestEmails'])) {
	$member_id=328;
	$coach_id_startup = 234;
	$member_row = GetRowMember($db, $member_id);
	
	# Email Member, Welcome Coach, S1 Coach and Sponsor 
#	EmailNewMember($db, $member_id);
#	EmailNewMemberCoach($db, "Welcome", $coach_id_setup, $member_row);
	EmailNewMemberCoach($db, "S1", $coach_id_startup, $member_row);
#	EmailNewMemberSponsor($db, $sponsor_row, $member_row, $inf_product_id);
}



#INPUT: $cycle = 2 > Monthly, 1 > Yearly
function InfWriteCycleName($cycle){
	if ($cycle==1) {
		return "year";
	} elseif ($cycle==2) {
		return "month";
	} else {
		return "unknown";	
	}
}

########################################################################
# Add Tag to Contact
 
function InfAddTag($inf_contact_id, $inf_tag_id) {
	return Infusionsoft_ContactService::addToGroup($inf_contact_id, $inf_tag_id);		
}

########################################################################
# Get Contact Record Details
 
function InfGetEmailTemplate($template_id) {
    $template = Infusionsoft_APIEmailService::getEmailTemplate($template_id);
	return $template;
}

########################################################################
# Get Contact Record Details
 
function InfGetContactDetails($inf_contact_id) {
    $object_type = "Contact";
    $class_name = "Infusionsoft_" . $object_type;
    $object = new $class_name();
    $objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => $inf_contact_id));

    $contact_array = array();
    foreach ($objects as $i => $object) {
        $contact_array[$i] = $object->toArray();
    }
	return $contact_array[0];
}

########################################################################
# Get Contact Referral Information

function InfGetReferralInfo($inf_contact_id) {

    $object_type = "Referral";
    $class_name = "Infusionsoft_" . $object_type;
    $object = new $class_name();
    // Order by most recent IP
#	$objects = Infusionsoft_DataService::query(new $class_name(), array('ContactId' => $payment['ContactId']));
    $objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('ContactId' => $inf_contact_id), 'DateSet', false);

    $referral_array = array();
    foreach ($objects as $i => $object) {
        $referral_array[$i] = $object->toArray();
    }

    $aff_ip_array = array();
    $j = 0;
    foreach ($referral_array as $i => $referral) {
        if (!in_array($referral['IPAddress'], $aff_ip_array)) {
            $j++;
            $aff_ip_array[$j]['ip'] = $referral['IPAddress'];
            $aff_ip_array[$j]['inf_aff_id'] = $referral['AffiliateId'];
        }
    }
    return $aff_ip_array;
}

########################################################################
# Try to find Sponsor via IP
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_inf.php?debug=1&inf_contact_id=609

if (isset($_GET['inf_contact_id'])) {
	WriteArray(InfGetSponsorDetails($db, $_GET['inf_contact_id']));
}

function InfGetSponsorDetails($db, $inf_contact_id, $contact_array = array()) {
	
	$sponsor['sponsor_unknown'] = false;
	$sponsor['sponsor_id'] = 0;
	$sponsor['tracking'] = "";
	$sponsor['inf_aff_id'] = "";
	$ip = '';

	# First check their shipping_city and shipping_state
	if (empty($contact_array)) {
		$contact_array = InfGetContactDetails($inf_contact_id);
	}
#	WriteArray($contact_array);	
	# The State2 field is shipping_state (passed from ClickFunnels and contains
	# ?t=TRACKING
	if (substr($contact_array['State2'],0,3)=="&t=") {
		$sponsor['tracking'] = str_replace("&t=", "", $contact_array['State2']);
	}

	# The City2 field is shipping_city (passed from ClickFunnels and contains
	# ?da=USERNAME
	if (substr($contact_array['City2'],0,4)=="?da=") {
		$sponsor_username = str_replace("?da=", "", $contact_array['City2']);
		
		if ($sponsor_username<>"") {
		
			# SPONSOR FOUND
			$query = "SELECT member_id, inf_aff_id
						FROM members
						WHERE username='$sponsor_username'";
			$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
			if ($row = mysqli_fetch_assoc($result)) {
				$sponsor['sponsor_id'] = $row['member_id'];
				$sponsor['inf_aff_id'] = $row['inf_aff_id'];
				return $sponsor;
			}
		}
	}
	
	# OTHERWISE NOT FOUND TRY TO TRACK VIA VISITS TABLE 
	$sponsor = TrackGetSponsorDetails($db, $contact_array['Email']);

/*	INF DOES NOT TRACK IP VERY WELL

	$aff_ip_array = InfGetReferralInfo($inf_contact_id);

	foreach ($aff_ip_array as $i => $value) {
		$ip = $value['ip'];
		// See if we know the inf_aff_id
#		WriteArray($value);            
		if ($value['inf_aff_id']) {
#			WriteArray($value['inf_aff_id']);
			// We know who the affiliate is
			$query = "SELECT member_id 
						FROM members
						WHERE inf_aff_id='{$value['inf_aff_id']}'";
			if (DEBUG) EchoLn($query);
			$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
			if ($row = mysqli_fetch_assoc($result)) {
				$sponsor['sponsor_id'] = $row['member_id'];
				// Lookup tracking using IP
				$query = "SELECT m.member_id, v.da, v.t 
							FROM members m
							JOIN visits v on v.da = m.username AND v.da <> ''
							WHERE v.ip='{$value['ip']}'
							ORDER BY v.create_date DESC
							LIMIT 1";
				if (DEBUG) EchoLn($query);
				$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
				if ($row = mysqli_fetch_assoc($result)) {
# Already set this above
#                    $sponsor['sponsor_id'] = $row['member_id'];
					$sponsor['tracking'] = $row['t'];
				}
				break;
			}
		} else {
			// Try to lookup by IP
			# See if we can find the sponsor based on the join IP
			$query = "SELECT m.member_id, v.da, v.t 
						FROM members m
						JOIN visits v on v.da = m.username AND v.da <> ''
						WHERE v.ip='{$value['ip']}'
						ORDER BY v.create_date DESC
						LIMIT 1";
			if (DEBUG) EchoLn($query);
			$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
			if ($row = mysqli_fetch_assoc($result)) {
				$sponsor['sponsor_id'] = $row['member_id'];
				$sponsor['tracking'] = $row['t'];
				break;
			}
		}
	}
*/
	// If sponsor not found - send to force, but keep them still as sponsor_unknow
	if (!$sponsor['sponsor_id']) {
		$sponsor['sponsor_id'] = CP_DEFAULT_SPONSOR_ID;
		$sponsor['sponsor_unknown'] = true;
	}
	return $sponsor;
}

########################################################################
# Tries to find Inf Contact I (most recent) based on email 

function InfGetContactId($email) {
	
	$object_type = "Contact";
    $class_name = "Infusionsoft_" . $object_type;
    $object = new $class_name();
    // Order by most recent contact with that Email
#	$objects = Infusionsoft_DataService::query(new $class_name(), array('ContactId' => $payment['ContactId']));
    $objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('Email' => $email), 'DateCreated', false);
    $contact_array = array();
    foreach ($objects as $i => $object) {
        $contact_array[$i] = $object->toArray();
    }
    foreach ($contact_array as $i => $contact) {
		return $contact['Id'];
	}
	return "";
}

########################################################################
# Insert Contact into DA (if does not already exist)
# And assign them to their four coaches
# Also try to find their sponsor & send them a Welcome Email
# Returns their new username and password
# If $sponsor_id=0 then try to find it via tracking

function InfInsertMember($db, $inf_contact_id, $sponsor_id = false, $inf_product_id = false, $inf_payment_id = false, $inf_invoice_id = false) {

	if (DEBUG) EchoLn("ContactId: $inf_contact_id");

	$contact = InfGetContactDetails($inf_contact_id);

	# Look up if the member already exists or not
	$query = "SELECT m.member_id 
				FROM members m
				WHERE inf_contact_id='{$contact['Id']}'
				LIMIT 1";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
	if ($row = mysqli_fetch_assoc($result)) {
		# FAILURE - Already exists
		return array("member_id" => "", "username" => "", "passwd" => "", "email" => "", "name" => "");
	}

	$member_id = false;

	# See if the customer (email) is an existing member - by checking email
	$query = "SELECT member_id, inf_contact_id
				FROM members m
				LEFT JOIN member_emails me USING (member_id)
				WHERE m.email='{$contact['Email']}'
				OR me.alternate_email='{$contact['Email']}'
				ORDER BY m.create_date ASC, me.create_date DESC";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
	if ($row = mysqli_fetch_assoc($result)) {
		$member_id = $row['member_id'];
		# If the member was found and didn't have an inf_contact_id, then give it to them.
		if (!$row['inf_contact_id']) {
			$query = "UPDATE members 
						SET inf_contact_id='{$payment['ContactId']}'
						WHERE member_id='$member_id";
			if (DEBUG) EchoLn($query);
			$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
			continue;
		}
	}
	
	if ($sponsor_id) {
		$sponsor_array['sponsor_id'] = $sponsor_id;
		$sponsor_array['tracking'] = "";
		$sponsor_array['sponsor_unknown'] = 0;
	} else {
		$sponsor_array = InfGetSponsorDetails($db, $inf_contact_id, $contact);
	}

	# Create new username for the member
	# Keep creating them randomly until we find one not being used
	do {
		$username = substr(str_shuffle("abcdefghijkmnopqrstuvwxyz"), 0, 2).substr(str_shuffle("2346789"), 0, 3);
		$query = "SELECT member_id 
					FROM members
					WHERE username='$username'";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
	} while (mysqli_fetch_assoc($result));

	$passwd = substr(str_shuffle("2346789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ"), 0, 5);
	
	$sponsor_row = GetRowMemberDetails($db, $sponsor_array['sponsor_id']);
	
	# Assign their coaches 
	$coach_id_setup = GetCoachId ($db, "setup");
	if (isset($sponsor_row['team_leader_id']) && $sponsor_row['team_leader_id'] <> 0) {
		// Special case for 173 (he's team leader closing strategy sales himself
		$coach_id_scale = $sponsor_row['team_leader_id'];
	} else {
		$coach_id_scale = GetCoachId ($db, "scale");
	}
	$coach_id_traffic = GetCoachId ($db, "traffic");
	$coach_id_success = GetCoachId ($db, "success");
	
	// If they are coming in at RISE and above then open up step 8 for them
	$step_sql = '';
	if (in_array($inf_product_id, array(INF_PRODUCT_ID_BAS_RIS_H, INF_PRODUCT_ID_BAS_RIS_C))) {
		$step_sql = ", steps_completed = 1.6
					, step_unlocked = 2.2";	
		$coach_id_startup = 0;
		EmailNewMemberCoach($db, "S2", $coach_id_setup, $member_row);
	} else {
		EmailNewMemberCoach($db, "S1", $coach_id_startup, $member_row);
		$coach_id_startup = GetCoachId ($db, "startup");		
	}

	$date = date("Y-m-d H:i:s", strtotime($contact['DateCreated']));
//                    , inf_aff_id	='$inf_aff_id'
	$query = "INSERT INTO members
				SET inf_contact_id	='{$contact['Id']}'
				, sponsor_id		='{$sponsor_array['sponsor_id']}'
				, team_leader_id	='{$sponsor_row['team_leader_id']}'
				, username			='$username'
				, passwd			='$passwd'
				, name			='{$contact['FirstName']} {$contact['LastName']}'
				, email			='{$contact['Email']}'
				, phone			='{$contact['Phone1']}'
				, first_name	='{$contact['FirstName']}'
				, last_name		='{$contact['LastName']}'
				, address		='{$contact['StreetAddress1']}'
				, city			='{$contact['City']}'
				, state			='{$contact['State']}'
				, zip			='{$contact['PostalCode']}'
				, country		='{$contact['Country']}'
				, t				='{$sponsor_array['tracking']}'
				, ip			='{$_SERVER['REMOTE_ADDR']}'
				$step_sql
				, sponsor_unknown	='{$sponsor_array['sponsor_unknown']}'
				, join_date			='$date'
				, create_date		=NOW()";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
	$member_id = mysqli_insert_id($db);

	_InfGiveBonuses ($db, $inf_contact_id, $inf_product_id, $inf_payment_id, $inf_invoice_id, $card_id);
	
	# SUCCESS
	$query = "INSERT INTO member_coaches
				SET member_id	='$member_id'
				, coach_id_startup	='$coach_id_startup'
				, coach_id_setup	='$coach_id_setup'
				, coach_id_scale	='$coach_id_scale'
				, coach_id_traffic	='$coach_id_traffic'
				, coach_id_success	='$coach_id_success'
				, start_date		='$date'";
    if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);

	# Remove from GetResponse
	GRMoveContactCampaign($contact['Email']);

	$member_row = GetRowMember($db, $member_id);
	
	# Email Member, Welcome Coach, S1 Coach and Sponsor 
	EmailNewMember($db, $member_id);
	EmailNewMemberCoach($db, "Welcome", $coach_id_setup, $member_row);
	EmailNewMemberSponsor($db, $sponsor_row, $member_row, $inf_product_id);
				
	return $member_row;
}

########################################################################
# This function does the EXTRA stuff that needs to happen with an order
# eg, Sign them up for a subscriptions, or give them extra products.

# TODO: Perhaps this could be DB driven - though maybe they'll just think of extra bonuses
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_inf.php?debug=1&TestGiveBonuses=1
if (isset($_GET['TestGiveBonuses'])) {
#	_InfGiveBonuses ($db, 789, INF_PRODUCT_ID_BAS_RIS_H, 0, 1465, );
}

function _InfGiveBonuses($db, $inf_contact_id, $inf_product_id, $inf_payment_id, $inf_invoice_id, $card_id) {
	
	$reclac_commissions = false;

	# IF this is $1 trial order then create a subscription to start 14 days later.
	if ($inf_product_id == INF_PRODUCT_ID_ASP_W_TRIAL) { // 14 day trial asp-w#1
		$cards_array = InfGetCreditCards($inf_contact_id);	
		# Find most recent cards
		$card_id = max(array_keys($cards_array));

		$inf_sub_id = InfCreateRecurringOrder($inf_contact_id, INF_SUB_PLAN_ID_ASP_W, INF_SUB_PRICE_ASP_W, $card_id, $days_till_charge = INF_ASP_W_TRIAL_DAYS);
	}

	# If they joined with RISE+BASE+HIKER
	elseif ($inf_product_id == INF_PRODUCT_ID_BAS_RIS_H) { // BASE+RISE+Hiker
		# Add-On BASE $0
		$result = Infusionsoft_InvoiceService::addOrderItem($inf_invoice_id, INF_PRODUCT_ID_BAS, INF_ORDER_TYPE, 
			INF_PRODUCT_PRICE_BONUS_BAS, INF_QUANTITY, "Bonus BASE", $notes = '');
		if (DEBUG) EchoLn("Hiker: Result 1: ".$result);

		# Add-On Aspire Hiker $67
		$result = Infusionsoft_InvoiceService::addOrderItem($inf_invoice_id, INF_PRODUCT_ID_ASP_H, INF_ORDER_TYPE, 
			INF_PRODUCT_PRICE_BONUS_ASP_H, INF_QUANTITY, "Add-On ASPIRE Hiker", $notes = '');
		if (DEBUG) EchoLn("Hiker: Result 2: ".$result);

		# Create ASPIRE Climber subscription
		$inf_sub_id = InfCreateRecurringOrder($inf_contact_id, INF_SUB_PLAN_ID_ASP_H, INF_SUB_PRICE_ASP_H, $card_id, $days_till_charge = 30);
			
		$reclac_commissions = true;		
		if (DEBUG) EchoLn("inf_sub_id: ".$inf_sub_id);
	}

	# If they joined with RISE+BASE+HIKER
	elseif ($inf_product_id == INF_PRODUCT_ID_BAS_RIS_C) { // BASE+RISE+Hiker
		# Add-On BASE $0
		$result = Infusionsoft_InvoiceService::addOrderItem($inf_invoice_id, INF_PRODUCT_ID_BAS, INF_ORDER_TYPE, 
			INF_PRODUCT_PRICE_BONUS_BAS, INF_QUANTITY, "Bonus BASE", $notes = '');
		if (DEBUG) EchoLn("Climber: Result 1: ".$result);

		# Add-On Aspire Climber $127
		$result = Infusionsoft_InvoiceService::addOrderItem($inf_invoice_id, INF_PRODUCT_ID_ASP_C, INF_ORDER_TYPE, 
			INF_PRODUCT_PRICE_BONUS_ASP_C, INF_QUANTITY, "Add-On ASPIRE Climber", $notes = '');
		if (DEBUG) EchoLn("Climber: Result 2: ".$result);

		# Create ASPIRE Climber subscription
		$inf_sub_id = InfCreateRecurringOrder($inf_contact_id, INF_SUB_PLAN_ID_ASP_C, INF_SUB_PRICE_ASP_H, $card_id, $days_till_charge = 30);
		if (DEBUG) EchoLn("inf_sub_id: ".$inf_sub_id);
			
		$reclac_commissions = true;		
	}
	
	if ($reclac_commissions && $inf_payment_id) {		
		$query = "DELETE FROM commissions 
			WHERE inf_payment_id = $inf_payment_id";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	}
}

########################################################################
# Create a new Contact in Infusionsoft
function InfAddContact($FirstName, $LastName, $Email) {

	$contact = new Infusionsoft_Contact();
	$contact->FirstName = $FirstName;
	$contact->LastName = $LastName;
	$contact->Email = $Email;
	
	# Return contact id
	return $contact->save();
}

########################################################################
# 
function InfDoesContactExist($inf_contact_id) {
	$object_type = "Contact";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => $inf_contact_id));
	$res = false;
	foreach($objects as $i => $object){
		$res = true;
	}
	return $res;
}

########################################################################
#eg $from_field = "ContactId" | "AffCode"
function InfGetAffId($from_field, $field_value) {
	$object_type = "Affiliate";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array($from_field => $field_value));

	$res = 0;
    $affiliates_array = array();
    foreach ($objects as $i => $object) {
        $affiliates_array[$i] = $object->toArray();
    }
    foreach ($affiliates_array as $i => $affiliate) {
        $res = $affiliate['Id'];
    }
	return $res;
}

########################################################################
# Get Recurring Order/Subscrpion details for a members
# INPUT: inf_contact_id, $inf_product_id (optional)
function InfGetRecurringOrders($inf_contact_id, $inf_product_id = 0) {
	$object_type = "RecurringOrder";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	
	$search_data['ContactId'] = $inf_contact_id;
	$search_data['Status'] = "Active";
	if ($inf_product_id) {
		$search_data['ProductId'] = $inf_product_id;
	}
	
	$objects = Infusionsoft_DataService::query(new $class_name(), $search_data);

    $subscriptions_array = array();
    foreach ($objects as $i => $object) {
		// Give it a userful index, ie, inf_sub_id
		$array = $object->toArray();
        $subscriptions_array[$array['Id']] = $array;
    }
	return $subscriptions_array;
}

/*
# DOES NOT APPEAR TO WORK - have not tested with Status=Active
# PROBABLY also not tupdate NextBillDate (perhaps via Infusionsoft_InvoiceService::updateJobRecurringNextBillDate

########################################################################
# Update Credit Card's billing Details
function InfUpdateRecurringOrder($inf_sub_id, $SubscriptionPlanId, $BillingAmt, $inf_card_id) {

	$credit_card = new Infusionsoft_RecurringOrder();
	$credit_card->Id = $inf_sub_id;
	$credit_card->SubscriptionPlanId = $SubscriptionPlanId;
	$credit_card->BillingAmt = $BillingAmt;
	$credit_card->CC1 = $inf_card_id;
	$credit_card->Status = "Active";
	return $credit_card->save(); // Update Subscription in Infusionsoft
}
*/
########################################################################
# ie, update the INF Referral Partner Username/Aff Code
function InfUpdateRecurringOrderCC($inf_sub_id, $cc1_id, $cc2_id) {

	$recurring_order = new Infusionsoft_RecurringOrder($inf_sub_id);
	$recurring_order->CC1 = $cc1_id;
#	$recurring_order->CC2 = $cc2_id;
	return $recurring_order->save(); // Update cc's on recurring order in Infusionsoft
}

########################################################################
# Create Recurring Order
function InfCreateRecurringOrder($inf_contact_id, $inf_sub_plan_id, $price, $cc_id, $days_till_charge) {

	# Create a suscription
	// addReccuringOrder($contactId, $allowDuplicate, $cProgramId, $qty, $price, $allowTax, $merchantAccountId, $creditCardId, $affiliateId, $daysTillCharge, Infusionsoft_App $app = null)
	$inf_sub_id = Infusionsoft_InvoiceService::addRecurringOrder($inf_contact_id, false, $inf_sub_plan_id, INF_QUANTITY, $price, false, INF_MERCHANT_ID, $cc_id, 0, $days_till_charge);
	
	// Paid_thru_date is getting set to start day instead of NextBillDate (give them 10 grace
#	$recurring_order = new Infusionsoft_RecurringOrder($inf_sub_id);
#	$recurring_order->PaidThruDate = date("Y-m-d H:m:s", strtotime("+$days_till_charge DAY", time()));
	return $inf_sub_id;	
}


########################################################################
# Get invoices (Paid or Unpaid) for a member
# INPUT: 
# 	$inf_contact_id 
# 	$pay_status: 1=Paid, 0=Unpaid
function InfGetInvoices($inf_contact_id, $pay_status = 0) {
	$object_type = "Invoice";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array('ContactId' => $inf_contact_id, 'PayStatus' => $pay_status));

    $invoices_array = array();
    foreach ($objects as $i => $object) {
		// Give it a userful index, ie, inf_invoice_id
		$array = $object->toArray();
        $invoices_array[$array['Id']] = $array;
    }
	return $invoices_array; 
}

########################################################################
# Get particular invoice for a member
# INPUT: 
# 	$inf_contact_id 
# 	$inf_invoice_id
function InfGetInvoice($inf_contact_id, $inf_invoice_id) {
	$object_type = "Invoice";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array('ContactId' => $inf_contact_id, 'Id' => $inf_invoice_id));

    $invoice_details = false;
    foreach ($objects as $i => $object) {
		$array = $object->toArray();
        $invoice_details = $array;
    }
	return $invoice_details;
}


########################################################################
# CREDIT CARDS
########################################################################

########################################################################
# Get Credit Cards details for a members
# INPUT: inf_contact_id
# STATUS VALUES:
# 4 = Card has been made inactive on Infusionsoft
# 3 = Card is Ok (valid)
# 2 = Card has been deleted(contact record for this card has been deleted)
# 1 = Card is invalid
# 0 = Unknown
function InfGetCreditCards($inf_contact_id) {
	$object_type = "CreditCard";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$object->removeRestrictedFields();  // Remove CreditCard and CVV
	$objects = Infusionsoft_DataService::query(new $class_name(), array('ContactId' => $inf_contact_id, 'Status' => 3));

    $cards_array = array();
    foreach ($objects as $i => $object) {
		// Give it a userful index, ie, card_id
		$array = $object->toArray();
        $cards_array[$array['Id']] = $array;
    }
	return $cards_array; // Maybe multiple cards
}

########################################################################
# Get Credit Cards details for a members
# INPUT: Credit Card Id
# STATUS VALUES:
# 4 = Card has been made inactive on Infusionsoft
# 3 = Card is Ok (valid)
# 2 = Card has been deleted(contact record for this card has been deleted)
# 1 = Card is invalid
# 0 = Unknown
function InfGetCreditCard($inf_contact_id, $inf_card_id) {
	$object_type = "CreditCard";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$object->removeRestrictedFields();  // Remove CreditCard and CVV
	$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => $inf_card_id, 'ContactId' => $inf_contact_id, 'Status' => 3));

    $cards_array = array();
    foreach ($objects as $i => $object) {
        $cards_array = $object->toArray();
    }
	return $cards_array;  // Should only be one card
}

########################################################################
# Get Credit Cards details for a members
# INPUT: Contact ID and Credit Card Id
# RETURN: 1 for SUCESS
function InfDeleteCreditCard($inf_card_id) {
	$object_type = "Product";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$res = Infusionsoft_ProductService::deactivateCreditCard($inf_card_id);
	
	return $res;
}

########################################################################
# Update Credit Card's billing Details
function InfUpdateCreditCard($inf_card_id, $CardNumber='', $ExpirationMonth, $ExpirationYear, $NameOnCard, $BillAddress1, $BillCity, $BillState, $BillZip, $BillCountry) {

	$credit_card = new Infusionsoft_CreditCard();
	$credit_card->Id = $inf_card_id;
	if ($CreditCard<>"") {
		$credit_card->CardNumber = $CardNumber;
	}
	$credit_card->ExpirationMonth = $ExpirationMonth;
	$credit_card->ExpirationYear = $ExpirationYear;
	$credit_card->NameOnCard = $NameOnCard;
	$credit_card->BillAddress1 = $BillAddress1;
	$credit_card->BillCity = $BillCity;
	$credit_card->BillState = $BillState;
	$credit_card->BillZip = $BillZip;
	$credit_card->BillCountry = $BillCountry;
	return $credit_card->save(); // Update Card in Infusionsoft
}

########################################################################
# Update Credit Card's CVV (just before charing it)
function InfUpdateCreditCardCVV($inf_card_id, $CVV2) {

	$credit_card = new Infusionsoft_CreditCard();
	$credit_card->Id = $inf_card_id;
	$credit_card->CVV2 = $CVV2;
	return $credit_card->save(); // Update Card in Infusionsoft
}

########################################################################
# 
# RETURN: Examples:
# a) array(2) { ["Message"]=> string(19) "Validated 12/6/2015" ["Valid"]=> string(4) "true" }
# b) array(2) { ["Message"]=> string(51) "First four digits, 6111, indicate unknown card type" ["Valid"]=> string(5) "false" }
function InfValidateCreditCard($CardNumber, $ExpirationMonth, $ExpirationYear) {

	$card = array("CardNumber" => $CardNumber
		, "ExpirationMonth" => $ExpirationMonth #must be MM
		, "ExpirationYear" => $ExpirationYear #must be YYYY
	);
	return Infusionsoft_InvoiceService::validateCreditCardData($card);
}

########################################################################
# Create new Credit Card and add ot a Contact
function InfAddCreditCard($inf_contact_id, $CardNumber, $ExpirationMonth, $ExpirationYear, $CVV2, $BillName, $BillAddress1, $BillCity, $BillState, $BillZip, $BillCountry) {

	$credit_card = new Infusionsoft_CreditCard();
	$credit_card->ContactId = $inf_contact_id;
	$credit_card->CardNumber = $CardNumber;
	$credit_card->CVV2 = $CVV2;
	$credit_card->CardType = WriteCardType($CardNumber);
#	$credit_card->Status = 3; //0: Unknown, 1: Invalid, 2: Deleted, 3: Valid/Good, 4: Inactive
	$credit_card->ExpirationMonth = $ExpirationMonth;
	$credit_card->ExpirationYear = $ExpirationYear;
	$credit_card->BillName = $BillName;
	$credit_card->BillAddress1 = $BillAddress1;
	$credit_card->BillCity = $BillCity;
	$credit_card->BillState = $BillState;
	$credit_card->BillZip = $BillZip;
	$credit_card->BillCountry = $BillCountry;
	
	# Return card id
	return $credit_card->save();
}

########################################################################
# ie, update the INF Referral Partner Username/Aff Code
function InfUpdateAffCode($inf_aff_id, $inf_aff_code) {

	$affiliate = new Infusionsoft_Affiliate();
	$affiliate->Id = $inf_aff_id;
	$affiliate->AffCode = $inf_aff_code;
	$affiliate->save(); // Update affiliate in Infusionsoft
}

########################################################################
# Update Contact's Email in Infusionsoft
function InfUpdateContactEmail($inf_contact_id, $new_email) {

	$affiliate = new Infusionsoft_Contact();
	$affiliate->Id = $inf_contact_id;
	$affiliate->Email = $new_email;
	$affiliate->save(); // Update Contact's Email in Infusionsoft
	
	// Flag this as "optin" so they are marketable.
	Infusionsoft_APIEmailService::optIn($new_email, 'Member changed their email in my.digitalaltitude.co profile.');
}

?>

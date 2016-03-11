<?php
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_inf.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$inf_contact_ids = array(321,319);

foreach($inf_contact_ids as $inf_contact_id) {
	EchoLn("[$inf_contact_id]");

/*	
    $query = "SELECT *
				FROM members m
				WHERE inf_contact_id = $inf_contact_id";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
	if($row = mysqli_fetch_assoc($result)){
*/		
/*		$query = "SELECT pay_date
					FROM inf_payments p
					WHERE inf_contact_id = $inf_contact_id";
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		if($row = mysqli_fetch_assoc($result)){
			$pay_date = $row['pay_date'];
		} else {
			EchoLn("PAYMENT NOT FOUND");	
		}
*/		

		$cards_array = InfGetCreditCards($inf_contact_id);
		
		# Find most recent cards
		$card_id = max(array_keys($cards_array));
#		WriteArray($cards_array);
#		exit;

		# Create a suscription
		// addReccuringOrder($contactId, $allowDuplicate, $cProgramId, $qty, $price, $allowTax, $merchantAccountId, $creditCardId, $affiliateId, $daysTillCharge, Infusionsoft_App $app = null)
		$days_till_charge = 14;
		$inf_sub_plan_id = 23; // $37 a month
		$price = 37;
#		$card_id = $row[''];
		if ($card_id) {
			$inf_sub_id = InfCreateRecurringOrder($inf_contact_id, $inf_sub_plan_id, $price, $card_id, $days_till_charge);
		}
		EchoLn("[$inf_contact_id] Subscription Added: $inf_sub_id ($card_id)");
#	}
}

?>

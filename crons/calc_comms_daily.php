<?php
# USAGE: da.digitalaltitude.co/crons/calc_comms_daily.php?debug=1

require_once("../includes/config.php");
require_once("../includes/functions.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

# Decide which date to start recacluations from, ie, today or since last update
$query = "SELECT MAX(create_date) as from_date
				FROM commissions_daily";
$result = mysqli_query($db, $query) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($result);
$from_date = min(date("Y-m-d"), $row['from_date']);

# Go through all transactions for the day and add up commission for each person and store in commissions_daily table
# Redo the last day on record (in case it's today) and any new days since then
$query = "SELECT DATE(p.pay_date) as pay_date, c.*
			FROM commissions c
			JOIN inf_payments p USING (inf_payment_id)
			WHERE (DATE(p.pay_date) >= '$from_date'
				OR DATE(p.pay_date) IN 
					(SELECT DISTINCT create_date
					FROM commissions_daily
					WHERE recalc = 1)
			)
			";
if (DEBUG) EchoLn($query);      
$result = mysqli_query($db, $query) or die(mysqli_error($db));
$member_day = array();
while($row = mysqli_fetch_assoc($result)){
	if (isset($member_day[$row['pay_date']][$row['tier1_aff_id']])) {
		$member_day[$row['pay_date']][$row['tier1_aff_id']] += $row['tier1_amt'];
		$member_day[$row['pay_date']][$row['tier1_up_aff_id']] += $row['tier1_up_amt'];
		$member_day[$row['pay_date']][$row['tier2_aff_id']] += $row['tier2_amt'];
		$member_day[$row['pay_date']][$row['tier3_aff_id']] += $row['tier3_amt'];
	} else {
		$member_day[$row['pay_date']][$row['tier1_aff_id']] = $row['tier1_amt'];
		$member_day[$row['pay_date']][$row['tier1_up_aff_id']] = $row['tier1_up_amt'];
		$member_day[$row['pay_date']][$row['tier2_aff_id']] = $row['tier2_amt'];
		$member_day[$row['pay_date']][$row['tier3_aff_id']] = $row['tier3_amt'];		
	}
}
foreach($member_day as $date => $members) {
	# Delete any existing records for $date (in case they are out of date)
	_DeleteCommissionsDaily($db, $date);
	foreach($members as $member_id => $amount) {
		if ($amount <> 0) {
			_InsertCommissionsDailyRow($db, $member_id, $amount, $date);
		}
	}
}
if (DEBUG) EchoLn("DONE",GREEN);

function _InsertCommissionsDailyRow($db, $member_id, $amount, $date) {
	# Test if they already have that rank or not
	$query = "REPLACE INTO commissions_daily
				SET member_id = '$member_id'
				, commissions = '$amount'
				, create_date = '$date'
				, recalc = 0
				";      
	if (DEBUG) EchoLn($query);      
	$result2 = mysqli_query($db, $query) or die(mysqli_error($db));
}

function _DeleteCommissionsDaily($db, $date) {
	# Test if they already have that rank or not
	$query = "DELETE FROM commissions_daily
				WHERE create_date = '$date'";      
	if (DEBUG) EchoLn($query);      
	$result2 = mysqli_query($db, $query) or die(mysqli_error($db));
}
?>

<?php
# da.digitalaltitude.co/inf/get_recurring_orders.php?debug=1

require_once("../includes/config.php");
require_once("../includes/functions.php");

// Include the SDK
require_once('../scripts/Infusionsoft/infusionsoft.php');
require_once('../scripts/Infusionsoft/examples/object_editor_all_tables.php');

// tables:
// - Products: Total paid and list of products, eg, http://digialti.com/inf/example.php?object=Invoice

########################################################################
# Insert Missing Products
$object_type = "RecurringOrder";
$class_name = "Infusionsoft_" . $object_type;
$object = new $class_name();

$objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('Id' => '%'), 'Id', false);
#$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => '%'));

foreach($objects as $i => $object){
	$recurrings_array[$i] = $object->toArray();
}
foreach($recurrings_array as $i => $recurring){ 
	$query = "REPLACE INTO inf_recurring_orders
				SET inf_recurring_order_id	='{$recurring['Id']}'
				, inf_contact_id	= '{$recurring['ContactId']}'
				, inf_originating_order_id	='{$recurring['OriginatingOrderId']}'
				, inf_program_id	='{$recurring['ProgramId']}'
				, inf_product_id	='{$recurring['ProductId']}'
				, inf_subscription_plan_id	='{$recurring['SubscriptionPlanId']}'
				, start_date		="._ConvertINFDate($recurring['StartDate'])."
				, end_date			="._ConvertINFDate($recurring['EndDate'])."
				, last_bill_date	="._ConvertINFDate($recurring['LastBillDate'])."
				, next_bill_date	="._ConvertINFDate($recurring['NextBillDate'])."
				, paid_thru_date	="._ConvertINFDate($recurring['PaidThruDate'])."
				, billing_cycle		='{$recurring['BillingCycle']}'
				, frequency			='{$recurring['Frequency']}'
				, billing_amt		='{$recurring['BillingAmt']}'
				, status			='{$recurring['Status']}'
				, reason_stopped	='{$recurring['ReasonStopped']}'
				, auto_charge		='{$recurring['AutoCharge']}'
				, cc1				='{$recurring['CC1']}'
				, cc2				='{$recurring['CC2']}'
				, create_date		=NOW()
				";  
	if (DEBUG) EchoLn($query);          
	$result = mysqli_query($db, $query) or die(mysqli_error($db));			
}

function _ConvertINFDate($inf_date) {
	if (empty($inf_date)) {
		return 'NULL';
	} else {
		return "'".date("Y-m-d H:i:s", strtotime(substr($inf_date,0,8)))."'";
	}
}
?>

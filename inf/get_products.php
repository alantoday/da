<?php
# da.digitalaltitude.co/inf/get_products.php?debug=1

require_once("../includes/config.php");
require_once("../includes/functions.php");

// Include the SDK
require_once('../scripts/Infusionsoft/infusionsoft.php');
require_once('../scripts/Infusionsoft/examples/object_editor_all_tables.php');

// tables:
// - Products: Total paid and list of products, eg, http://digialti.com/inf/example.php?object=Invoice

########################################################################
# Insert Missing Products
$object_type = "Product";
$class_name = "Infusionsoft_" . $object_type;
$object = new $class_name();

$objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('Id' => '%'), 'Id', false);
#$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => '%'));

foreach($objects as $i => $object){
	$products_array[$i] = $object->toArray();
}
foreach($products_array as $i => $product){ 
	$query = "SELECT *
				FROM inf_products
				WHERE inf_product_id	='{$product['Id']}'
				";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if($row = mysqli_fetch_assoc($result)) {
		$query = "UPDATE inf_products
					SET product_name	='{$product['ProductName']}'
					, product_price		='{$product['ProductPrice']}'
					, short_description	='{$product['ShortDescription']}'
					, create_date		=NOW()
					WHERE inf_product_id='{$product['Id']}'
					";            
	} else {
		$query = "INSERT INTO inf_products
					SET inf_product_id	='{$product['Id']}'
					, product_name		='{$product['ProductName']}'
					, product_price		='{$product['ProductPrice']}'
					, short_description	='{$product['ShortDescription']}'
					, create_date		=NOW()
					";
	}
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db));			
}

########################################################################
# Insert Missing Subscription Plans
$object_type = "SubscriptionPlan";
$class_name = "Infusionsoft_" . $object_type;
$object = new $class_name();

// Get 100 Most recent
$objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('Id' => '%'), 'Id', false);
#$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => '%'));

foreach($objects as $i => $object){
	$sub_plans_array[$i] = $object->toArray();
}
foreach($sub_plans_array as $i => $sub_plan){ 
	$query = "SELECT *
				FROM inf_sub_plans
				WHERE inf_sub_plan_id ='{$sub_plan['Id']}'
				";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if($row = mysqli_fetch_assoc($result)) {
		$query = "UPDATE inf_sub_plans
					SET inf_product_id	='{$sub_plan['ProductId']}'
					, cycle				='{$sub_plan['Cycle']}'
					, frequency			='{$sub_plan['Frequency']}'
					, pre_authorize_amount ='{$sub_plan['PreAuthorizeAmount']}'
					, prorate			='{$sub_plan['Prorate']}'
					, active			='{$sub_plan['Active']}'
					, plan_price		='{$sub_plan['PlanPrice']}'
					, create_date		=NOW()
					WHERE inf_sub_plan_id	='{$sub_plan['Id']}'
					";            
	} else {
		$query = "INSERT INTO inf_sub_plans
					SET inf_sub_plan_id	='{$sub_plan['Id']}'
					, inf_product_id	='{$sub_plan['ProductId']}'
					, cycle				='{$sub_plan['Cycle']}'
					, frequency			='{$sub_plan['Frequency']}'
					, pre_authorize_amount ='{$sub_plan['PreAuthorizeAmount']}'
					, prorate			='{$sub_plan['Prorate']}'
					, active			='{$sub_plan['Active']}'
					, plan_price		='{$sub_plan['PlanPrice']}'
					, create_date		=NOW()
					";            
	}
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
}
echo "Done";
exit;

?>

<?php
# da.digitalaltitude.co/inf/get_contact_groups.php?debug=1

require_once("../includes/config.php");
require_once("../includes/functions.php");

// Include the SDK
require_once('../scripts/Infusionsoft/infusionsoft.php');
require_once('../scripts/Infusionsoft/examples/object_editor_all_tables.php');

// tables:
// - Products: Total paid and list of products, eg, http://digialti.com/inf/example.php?object=Invoice

########################################################################
# Insert Missing Products
$object_type = "ContactGroup";
$class_name = "Infusionsoft_" . $object_type;
$object = new $class_name();

$objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('Id' => '%'), 'Id', false);
#$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => '%'));

foreach($objects as $i => $object){
	$contact_groups_array[$i] = $object->toArray();
}
foreach($contact_groups_array as $i => $contact_group){ 
	$query = "REPLACE INTO inf_contact_group
				SET inf_contact_group_id	='{$contact_group['Id']}'
				, group_name		='".addslashes($contact_group['GroupName'])."'
				, group_category_id	='{$contact_group['GroupCategoryId']}'
				, group_description	='".addslashes($contact_group['GroupDescription'])."'
				, create_date		=NOW()
				";  
	if (DEBUG) EchoLn($query);          
	$result = mysqli_query($db, $query) or die(mysqli_error($db));			
}
?>

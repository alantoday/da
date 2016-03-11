<?php
$_GET['debug'] = 1;
require_once("../../includes/config.php");
require_once("../../includes/functions.php");
// Include the SDK
require_once('../../includes/Infusionsoft/infusionsoft.php');
require_once('../../includes/Infusionsoft/examples/object_editor_all_tables.php');
$object_type = "Contact";
$class_name = "Infusionsoft_" . $object_type;
$object = new $class_name();

# Who needs an inf_contact_id
$query = "SELECT member_id, email_username
			FROM members
			WHERE inf_contact_id = 0";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
while($row = mysqli_fetch_assoc($result)){
	EchoLn($row['email_username']);
	$objects = Infusionsoft_DataService::query(new $class_name(), array('Email' => $row['email_username']));
	$contact_array = array();
	foreach($objects as $i => $object){
		$contact_array[$i] = $object->toArray();
	}
	foreach($contact_array as $i => $contact){ 
		$query = "UPDATE members 
					SET inf_contact_id 	= '{$contact['Id']}' 
					WHERE member_id 	= '{$row['member_id']}'";
		EchoLn($query);
		$result2 = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);	
		break;	
	}
}

?>
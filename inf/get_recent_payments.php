<?php
$_GET['debug'] = 1;
require_once("../includes/config.php");
require_once("../includes/functions.php");

// Include the SDK
require_once('../scripts/Infusionsoft/infusionsoft.php');
include('../scripts/Infusionsoft/examples/object_editor_all_tables.php');

if (!isset($_GET['contact_id'])) $_GET['contact_id'] = 67;

	$object_type = "CreditCard";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();	
        // Order by most recent IP
        $objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('ContactId' => $_GET['contact_id']), 'DateSet', false);
	
	$referral_array = array();
	foreach($objects as $i => $object){
		$referral_array[$i] = $object->toArray();
	}
	WriteArray($referral_array);
	exit;
	$aff_ip_array= array();
	foreach($referral_array as $i => $referral){ 

                EchoLn($inf_aff_id.", $date, ". $ip);
	}

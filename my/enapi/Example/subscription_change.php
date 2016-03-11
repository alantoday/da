<?php
//---------------------------------------------------------------
// Example usage of the client API which shows how we can
// Enable/Unsubscribe to/from a list
//
// Made by: Nikolay Kolev
// Date: 09 January 2013
// Modified: April 8th 2015
//----------------------------------------------------------------

//include the configuration
include './Inc/config.inc.php';

//include the client API class
include './../Lib/ClientLibrary.class.php';

//check if we have a lead passed to view
if (!isset($_GET['list']) || !isset($_GET['status'])) {
	header('location: subscription_info.php');
	exit;
	}

//init the client API
$api = new \EmpowerNetwork\API\ClientLibrary(ACCESS_ID, API_KEY);

//Uncomment this line to disable verifying the SSL certificate peer
//$api->sslVerifyPeer(false);

//prepare the data for the leads
$api->prepareEmailSubscriptionChange($_GET['list'], ($_GET['status'] == 1 ? true:false));

$subscription_record = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

//if the operation was not a success
if ($subscription_record['code'] == 417) {
	//add some error handling code here
	}
	
header('location: subscription_info.php');
exit;
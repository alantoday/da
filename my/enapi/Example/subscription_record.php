<?php
//---------------------------------------------------------------
// Example usage of the client API which information for a specific
// email subscription
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
if (!isset($_GET['id'])) {
	header('location: subscription_info.php');
	exit;
	}

//init the client API
$api = new \EmpowerNetwork\API\ClientLibrary(ACCESS_ID, API_KEY);

//Uncomment this line to disable verifying the SSL certificate peer
//$api->sslVerifyPeer(false);

//prepare the data for the leads
$api->prepareEmailSubscriptionRecord($_GET['id']);

$subscription_record = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

//if no such record was found go back to the list
if (empty($subscription_record['data'])) {
	header('location: subscription_info.php');
	exit;
	}

include './Inc/header.inc.php';
?>

<h1 align="center">Subscription Record</h1>

<a href="./subscription_info.php">Back to List</a>
<br /><br />

Response status code: <strong><?php echo $subscription_record['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Date</strong></td>
		<td><strong>Status</strong></td>
	</tr>
	
	<tr>
		<td><?php echo $subscription_record['data']['info']['date']; ?></td>
		<td><?php echo $subscription_record['data']['info']['status']; ?></td>
	</tr>
</table>

<?php
include './Inc/footer.inc.php';
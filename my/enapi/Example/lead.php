<?php
//---------------------------------------------------------------
// Example usage of the client API which extracts the leads
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
	header('location: leads.php');
	exit;
	}

//init the client API
$api = new \EmpowerNetwork\API\ClientLibrary(ACCESS_ID, API_KEY);

//Uncomment this line to disable verifying the SSL certificate peer
//$api->sslVerifyPeer(false);

//prepare the data for the leads
$api->prepareLead($_GET['id']);

$leads_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

//if no such lead was found go back to the list
if ($leads_data['code'] != 200) {
	header('location: leads.php');
	exit;
	}

include './Inc/header.inc.php';
?>

<h1 align="center">Lead Record</h1>

<a href="./leads.php">Back to List</a>
<br /><br />

Response status code: <strong><?php echo $leads_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Source</strong></td>
		<td><strong>Tracking</strong></td>
		<td><strong>First Name</strong></td>
		<td><strong>Last Name</strong></td>
		<td><strong>E-mail</strong></td>
		<td><strong>Date Joined</strong></td>
		<td><strong>Status</strong></td>
	</tr>
	
	<tr>
		<td><?php echo $leads_data['data']['lead']['source']; ?>&nbsp;</td>
		<td><?php echo $leads_data['data']['lead']['tracking']; ?>&nbsp;</td>
		<td><?php echo $leads_data['data']['lead']['firstname']; ?></td>
		<td><?php echo $leads_data['data']['lead']['lastname']; ?></td>
		<td><?php echo $leads_data['data']['lead']['email']; ?></td>
		<td><?php echo $leads_data['data']['lead']['date']; ?></td>
		<td><?php echo $leads_data['data']['lead']['status']; ?> (<?php echo $leads_data['data']['lead']['status_description']; ?>)</td>
	</tr>
</table>

<?php
include './Inc/footer.inc.php';
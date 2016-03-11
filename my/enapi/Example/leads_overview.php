<?php
//---------------------------------------------------------------
// Example usage of the client API which extracts and shows the number
// of leads with the different statuses
//
// Made by: Nikolay Kolev
// Date: 03 January 2013
// Modified: April 8th 2015
//----------------------------------------------------------------

//include the configuration
include './Inc/config.inc.php';

//include the client API class
include './../Lib/ClientLibrary.class.php';

//init the client API
$api = new \EmpowerNetwork\API\ClientLibrary(ACCESS_ID, API_KEY);

//Uncomment this line to disable verifying the SSL certificate peer
//$api->sslVerifyPeer(false);

//Prepare the data for the leads
$api->prepareLeadsOverview();

$leads_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

include './Inc/header.inc.php';
?>

<h1 align="center">Leads Overview</h1>

Response status code: <strong><?php echo $leads_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Status</strong></td>
		<td><strong>Count</strong></td>
	</tr>
	
	<?php foreach($leads_data['data']['info'] AS $status_data): ?>
	<tr>
		<td><?php echo $status_data['status']; ?></td>
		<td><?php echo $status_data['count']; ?></td>
	</tr>
	<?php	endforeach; ?>
	
</table>

<?php
include './Inc/footer.inc.php';
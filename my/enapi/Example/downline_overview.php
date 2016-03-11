<?php
//---------------------------------------------------------------
// Example usage of the client API which shows how to extract
// the general downline information for the logged in member
// and display it
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

//prepare the data for the leads
$api->prepareDownlineOverview();

$downline_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

include './Inc/header.inc.php';
?>

<h1 align="center">Downline Overview</h1>

Response status code: <strong><?php echo $downline_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Program ID</strong></td>
		<td><strong>Program Name</strong></td>
		<td><strong>Unpaid</strong></td>
		<td><strong>Paid</strong></td>
	</tr>
	
	<?php foreach($downline_data['data']['info'] AS $program_id=>$program_data): ?>
	<tr>
		<td><?php echo $program_id; ?></td>
		<td><?php echo $program_data['program_name']; ?></td>
		<td><?php echo $program_data['unpaid']; ?></td>
		<td><?php echo $program_data['paid']; ?></td>
	</tr>
	<?php	endforeach; ?>
	
</table>

<?php
include './Inc/footer.inc.php';
<?php
//---------------------------------------------------------------
// Example usage of the client API which shows information for the
// member in the different programs
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
$api->prepareProgramStats();

$program_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

include './Inc/header.inc.php';
?>

<h1 align="center">Programs Statistics</h1>

Response status code: <strong><?php echo $program_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Program</strong></td>
		<td><strong>Status</strong></td>
		<td><strong>Action</strong></td>
	</tr>
	
	<?php foreach($program_data['data']['info'] AS $data): ?>
	<tr>
		<td><?php echo $data['name']; ?></td>
		<td><?php echo $data['status']; ?></td>
		<td><a href="./program_record.php?id=<?php echo $data['id']; ?>">View</a></td>
	</tr>
	<?php	endforeach; ?>
	
</table>

<?php
include './Inc/footer.inc.php';
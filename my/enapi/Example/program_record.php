<?php
//---------------------------------------------------------------
// Example usage of the client API which shows information for
// member in a specific program
//
// Made by: Nikolay Kolev
// Date: 09 January 2013
// Modified: April 8th 2015
//----------------------------------------------------------------

//include the configuration
include './Inc/config.inc.php';

//include the client API class
include './../Lib/ClientLibrary.class.php';

//check if we have a program passed to view
if (!isset($_GET['id'])) {
	header('location: program_stats.php');
	exit;
	}

//init the client API
$api = new \EmpowerNetwork\API\ClientLibrary(ACCESS_ID, API_KEY);

//Uncomment this line to disable verifying the SSL certificate peer
//$api->sslVerifyPeer(false);

//Prepare the data for the leads
$api->prepareProgram($_GET['id']);

$program_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

//if no such lead was found go back to the list
if (empty($program_data['data'])) {
	header('location: program_stats.php');
	exit;
	}

include './Inc/header.inc.php';
?>

<h1 align="center">Program Information</h1>

<a href="./program_stats.php">Back to List</a>
<br /><br />

Response status code: <strong><?php echo $program_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Name</strong></td>
		<td><strong>Status</strong></td>
	</tr>
	
	<tr>
		<td><?php echo $program_data['data']['info']['name']; ?></td>
		<td><?php echo $program_data['data']['info']['status']; ?></td>
	</tr>
</table>

<?php
include './Inc/footer.inc.php';
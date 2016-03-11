<?php
//---------------------------------------------------------------
// Example usage of the client API which gets the username of the logged in member
//
// Made by: Nikolay Kolev
// Date: 09 January 2013
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

//Get the username
$api->prepareUsername();

$username_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

include './Inc/header.inc.php';
?>

	<h1 align="center">Username</h1>

	Response status code: <strong><?php echo $username_data['code']; ?></strong> <br /><br />

	<table>
		<tr>
			<td><strong>Username</strong></td>
		</tr>

		<tr>
			<td><?php echo $username_data['data']['username']; ?></td>
		</tr>
	</table>

<?php
include './Inc/footer.inc.php';
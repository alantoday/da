<?php
//---------------------------------------------------------------
// Example usage of the client API which shows the information 
// for a member from the powerlines
//
// Made by: Nikolay Kolev
// Date: 09 January 2013
// Modified: April 8th 2015
//----------------------------------------------------------------

//include the configuration
include './Inc/config.inc.php';

//include the client API class
include './../Lib/ClientLibrary.class.php';

//check if we have a member passed to view
if (!isset($_GET['id'])) {
	header('location: downline.php');
	exit;
	}

//init the client API
$api = new \EmpowerNetwork\API\ClientLibrary(ACCESS_ID, API_KEY);

//Uncomment this line to disable verifying the SSL certificate peer
//$api->sslVerifyPeer(false);

//prepare the data for the member
$api->prepareDownlineMember($_GET['id']);

$member_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

//if no such lead was found go back to the list
if (empty($member_data['data'])) {
	header('location: downline.php');
	exit;
	}

include './Inc/header.inc.php';
?>

<h1 align="center">Member Information</h1>

<a href="./downline.php">Back to List</a>
<br /><br />

Response status code: <strong><?php echo $member_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>ID</strong></td>
		<td><strong>Username</strong></td>
		<td><strong>First Name</strong></td>
		<td><strong>Last Name</strong></td>
		<td><strong>E-mail</strong></td>
		<td><strong>Phone</strong></td>
		<td><strong>Language</strong></td>
		<td><strong>Date Signup</strong></td>
	</tr>
	
	<tr>
		<td><?php echo $member_data['data']['info']['id']; ?></td>
		<td><?php echo $member_data['data']['info']['username']; ?></td>
		<td><?php echo $member_data['data']['info']['firstname']; ?></td>
		<td><?php echo $member_data['data']['info']['lastname']; ?></td>
		<td><?php echo $member_data['data']['info']['email']; ?></td>
		<td><?php echo $member_data['data']['info']['phone']; ?></td>
		<td><?php echo $member_data['data']['info']['language']; ?></td>
		<td><?php echo $member_data['data']['info']['date']; ?></td>
	</tr>
</table>

<h2 align="center">Program Information</h2>

<table>
	<tr>
		<td><strong>Product ID</strong></td>
		<td><strong>Product Name</strong></td>
		<td><strong>Status</strong></td>
		<td><strong>Active Date</strong></td>
		<td><strong>Lockdown Date</strong></td>
	</tr>
	
	<?php foreach($member_data['data']['info']['program'] AS $program_data): ?>
	<tr>
		<td><?php echo $program_data['program_id']; ?></td>
		<td><?php echo $program_data['program_name']; ?></td>
		<td><?php echo $program_data['status']; ?></td>
		<td><?php echo $program_data['active_date']; ?></td>
		<td><?php echo $program_data['lockdown_date']; ?></td>
	</tr>
	<?php	endforeach; ?>
</table>

<?php
include './Inc/footer.inc.php';
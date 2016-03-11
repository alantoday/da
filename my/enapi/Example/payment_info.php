<?php
//---------------------------------------------------------------
// Example usage of the client API which shows a specific payment
//
// Made by: Nikolay Kolev
// Date: 11 January 2013
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

if (!isset($_GET['id']) || !ctype_digit((string)$_GET['id'])) {
	header('location: payment_list.php');
	exit;
	}

//prepare the data for the payment
$api->preparePaymentInfo($_GET['id']);

$payment_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

//if there is no such record
if (empty($payment_data['data'])) {
	header('location: payment_list.php');
	exit;
	}

include './Inc/header.inc.php';
?>

<h1 align="center">Payment Information</h1>

<a href="./payment_list.php">Back to list</a> <br /><br />

Response status code: <strong><?php echo $payment_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Payment ID</strong></td>
		<td><strong>Amount</strong></td>
		<td><strong>Fee Amount</strong></td>
		<td><strong>Commission Amount</strong></td>
		<td><strong>Payment Status</strong></td>
		<td><strong>Date</strong></td>
		<td><strong>Information</strong></td>
		<td><strong>Extra</strong></td>
	</tr>
	
	<tr>
		<td><?php echo (int)$_GET['id']; ?></td>
		<td><?php echo $payment_data['data']['record']['amount']; ?></td>
		<td><?php echo $payment_data['data']['record']['fee_amount']; ?></td>
		<td><?php echo $payment_data['data']['record']['commission_amount']; ?></td>
		<td><?php echo $payment_data['data']['record']['payment_status']; ?></td>
		<td><?php echo $payment_data['data']['record']['date']; ?></td>
		<td><?php echo implode('<br />', $payment_data['data']['record']['information']); ?></td>
		<td>Payer: <?php echo $payment_data['data']['record']['extra']['payer_username']; ?><br />Program: <?php echo $payment_data['data']['record']['extra']['program_name']; ?></td>
	</tr>
</table>

<?php
include './Inc/footer.inc.php';
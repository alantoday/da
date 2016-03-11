<?php
//---------------------------------------------------------------
// Example usage of the client API which shows how to extract
// the payment statistics
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
$api->preparePaymentStats();

$payment_stats = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

include './Inc/header.inc.php';
?>

<h1 align="center">Payment Statistics</h1>

Response status code: <strong><?php echo $payment_stats['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Earned</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['earned']; ?></td>
	</tr>
	<tr>
		<td><strong>Earned Legacy</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['legacy']; ?></td>
	</tr>
	<tr>
		<td><strong>Fee</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['fee']; ?></td>
	</tr>
	<tr>
		<td><strong>Pending</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['pending']; ?></td>
	</tr>
	<tr>
		<td><strong>Holding</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['holding']; ?></td>
	</tr>
	<tr>
		<td><strong>Holdback</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['holdback']; ?></td>
	</tr>
	<tr>
		<td><strong>Refund</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['refund']; ?></td>
	</tr>
	<tr>
		<td><strong>Chargeback</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['chargeback']; ?></td>
	</tr>
	<tr>
		<td><strong>Owe</strong></td>
		<td>$<?php echo $payment_stats['data']['statistics']['owe']; ?></td>
	</tr>
	
</table>

<?php
include './Inc/footer.inc.php';
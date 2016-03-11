<?php
//---------------------------------------------------------------
// Example usage of the client API which extracts the payments
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

//set the paging params
$page = false;
$id = false;
$hash = false;

if (!empty($_GET['page'])) {
	$page = $_GET['page'];
	}

if (!empty($_GET['id'])) {
	$id = $_GET['id'];
	}

if (!empty($_GET['hash'])) {
	$hash = $_GET['hash'];
	}

//prepare the data for the payments
$records_per_page = 20;
$pages = 10;

$api->preparePaymentList($records_per_page, $pages, $id, $page, $hash);

$payments = array('data' => $api->getResponse(), 'code' => $api->getStatusCode(), 'next_page' => $api->getNextLink(), 'prev_page' => $api->getPrevLink());

include './Inc/header.inc.php';
?>

<h1 align="center">Payment List</h1>

Response status code: <strong><?php echo $payments['code']; ?></strong> <br /><br />

<?php foreach($payments['data']['records'] AS $payment_data): ?>
<table border="1">
	<tr>
		<td>
<table>
	<tr>
		<td><strong>Payment ID</strong></td>
		<td><strong>Total Amount</strong></td>
		<td><strong>Fee Amount</strong></td>
		<td><strong>Commission Amount</strong></td>
		<td><strong>Date</strong></td>
		<td><strong>Status</strong></td>
		<td><strong>Information</strong></td>
		<td><strong>Extra</strong></td>
		<td><strong>Operation</strong></td>
	</tr>
	
	<tr>
		<td><?php echo $payment_data['id']; ?></td>
		<td><?php echo $payment_data['amount']; ?></td>
		<td><?php echo $payment_data['fee_amount']; ?></td>
		<td><?php echo $payment_data['commission_amount']; ?></td>
		<td><?php echo $payment_data['date']; ?></td>
		<td><?php echo $payment_data['payment_status']; ?></td>
		<td><?php echo implode('<br />', $payment_data['information']); ?></td>
		<td>Payer: <?php echo $payment_data['extra']['payer_username']; ?><br />Program: <?php echo $payment_data['extra']['program_name']; ?></td>
		<td><a href="payment_info.php?id=<?php echo $payment_data['id']; ?>">View</a></td>
	</tr>
</table>
	</td></tr></table>
<br /><br />
<?php	endforeach; ?>

<?php if ($payments['code'] == 206): ?>
	<table>
	<tr>
		<td align="right">
			<?php if ($payments['prev_page'] !== false): ?>
			<a href="payment_list.php?<?php echo 'id='.$payments['data']['pagination']['previous']['id'].'&page='.$payments['data']['pagination']['previous']['page'].'&hash='.$payments['data']['pagination']['previous']['hash']; ?>">Previous Page</a>
			<?php	endif; ?>

			<?php if ($payments['next_page'] !== false): ?>
			<a href="payment_list.php?<?php echo 'id='.$payments['data']['pagination']['next']['id'].'&page='.$payments['data']['pagination']['next']['page'].'&hash='.$payments['data']['pagination']['next']['hash']; ?>">Next Page</a>
			<?php	endif; ?>
		</td>
	</tr>
	</table>
	<?php	endif; ?>

<?php if ($payments['code'] == 206): ?>
Alternative Pagnation Solution:
<?php if (!empty($payments['data']['pagination']['first'])): ?>
	<a href="./payment_list.php?id=first">First Page</a>
<?php	endif; ?>

<?php if (!empty($payments['data']['pagination']['previous'])): ?>
	<a href="./payment_list.php?page=<?php echo $payments['data']['pagination']['previous']['page']; ?>&id=<?php echo $payments['data']['pagination']['previous']['id']; ?>&hash=<?php echo $payments['data']['pagination']['previous']['hash']; ?>">Previous Page</a>
<?php	endif; ?>

<?php if (!empty($payments['data']['pagination']['pages'])): ?>
	<?php foreach($payments['data']['pagination']['pages'] AS $page_p=>$page_data): ?>
		<?php if ($page_p != $page): ?><a href="./payment_list.php?page=<?php echo $page_p; ?>&id=<?php echo $page_data['id']; ?>&hash=<?php echo $page_data['hash']; ?>"><?php endif; ?><?php echo $page_p; ?><?php if ($page_p != $page): ?></a><?php endif; ?>
	<?php	endforeach; ?>
<?php	endif; ?>

<?php if (!empty($payments['data']['pagination']['next'])): ?>
	<a href="./payment_list.php?page=<?php echo $payments['data']['pagination']['next']['page']; ?>&id=<?php echo $payments['data']['pagination']['next']['id']; ?>&hash=<?php echo $payments['data']['pagination']['next']['hash']; ?>">Next Page</a>
<?php	endif; ?>

<?php if (!empty($payments['data']['pagination']['last'])): ?>
	<a href="./payment_list.php?id=last">Last Page</a>
<?php	endif; ?>
<?php endif;?>

<?php
include './Inc/footer.inc.php';
<?php
//---------------------------------------------------------------
// Example usage of the client API which extracts the downline
//
// Made by: Nikolay Kolev
// Date: 10 January 2013
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
$page = 1;
$records_to_pull_per_page = 20;

if (isset($_GET['page'])) {
	$page = (int)$_GET['page'];
	}

//prepare the data for the downline
$product_id = 1; //Available 1, 2, 3, 4 and 5
$status = 'active'; //Available 'active', 'unpaid'
$downline_type = 'direct'; //Available 'direct' and 'passup'

$api->prepareDownline($product_id, $status, $page, $records_to_pull_per_page, $downline_type);

$downline_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode(), 'next_page' => $api->getNextLink(), 'prev_page' => $api->getPrevLink());

include './Inc/header.inc.php';
?>

<h1 align="center">Downline</h1>

Response status code: <strong><?php echo $downline_data['code']; ?></strong> <br /><br />

<h2 align="center">Direct Downline</h2>
<table>
	<?php if ($downline_data['code'] == 206): ?>
	<tr>
		<td colspan="10" align="right">
			<?php if ($downline_data['prev_page'] !== false): ?>
			<a href="./downline.php?page=<?php echo $page-1; ?>">Previous Page</a>
			<?php	endif; ?>

			<?php if ($downline_data['next_page'] !== false): ?>
			<a href="./downline.php?page=<?php echo $page+1; ?>">Next Page</a>
			<?php	endif; ?>
		</td>
	</tr>
	<?php	endif; ?>
	<tr>
		<td><strong>ID</strong></td>
		<td><strong>First Name</strong></td>
		<td><strong>Last Name</strong></td>
		<td><strong>E-mail</strong></td>
		<td><strong>Phone</strong></td>
		<td><strong>Language</strong></td>
		<td><strong>Signup Date</strong></td>
		<td><strong>Active Date</strong></td>
		<td><strong>Status</strong></td>
		<td><strong>Action</strong></td>
	</tr>
	
	<?php foreach($downline_data['data']['info']['direct'] AS $downline_data_val): ?>
	<tr>
		<td><?php echo $downline_data_val['id']; ?></td>
		<td><?php echo $downline_data_val['firstname']; ?></td>
		<td><?php echo $downline_data_val['lastname']; ?></td>
		<td><?php echo $downline_data_val['email']; ?></td>
		<td><?php echo $downline_data_val['phone']; ?></td>
		<td><?php echo $downline_data_val['language']; ?></td>
		<td><?php echo $downline_data_val['signup_date']; ?></td>
		<td><?php echo $downline_data_val['active_date']; ?></td>
		<td><?php echo $downline_data_val['status']; ?></td>
		<td><a href="./downline_member.php?id=<?php echo $downline_data_val['id']; ?>">View</a></td>
	</tr>
	<?php	endforeach; ?>


<?php if (!empty($downline_data['data']['info']['powerlines'])):?>
</table>
<h2 align="center">Powerlines</h2>
<table>
	
	<?php foreach($downline_data['data']['info']['powerlines'] AS $level_num=>$level_data): ?>
	<tr>
		<td colspan="10" align="center"><h3>Level <?php echo $level_num; ?></h3></td>
	</tr>
	
	<tr>
		<td><strong>ID</strong></td>
		<td><strong>First Name</strong></td>
		<td><strong>Last Name</strong></td>
		<td><strong>E-mail</strong></td>
		<td><strong>Phone</strong></td>
		<td><strong>Language</strong></td>
		<td><strong>Signup Date</strong></td>
		<td><strong>Active Date</strong></td>
		<td><strong>Status</strong></td>
		<td><strong>Action</strong></td>
	</tr>
	<?php foreach($level_data AS $downline_data_val): ?>
	<tr>
		<td><?php echo $downline_data_val['id']; ?></td>
		<td><?php echo $downline_data_val['firstname']; ?></td>
		<td><?php echo $downline_data_val['lastname']; ?></td>
		<td><?php echo $downline_data_val['email']; ?></td>
		<td><?php echo $downline_data_val['phone']; ?></td>
		<td><?php echo $downline_data_val['language']; ?></td>
		<td><?php echo $downline_data_val['signup_date']; ?></td>
		<td><?php echo $downline_data_val['active_date']; ?></td>
		<td><?php echo $downline_data_val['status']; ?></td>
		<td><a href="./downline_member.php?id=<?php echo $downline_data_val['id']; ?>">View</a></td>
	</tr>
	<?php	endforeach; ?>
	<?php	endforeach; ?>

<?php endif;?>

<?php if ($downline_data['code'] == 206): ?>
	<tr>
		<td colspan="10" align="right">
			<?php if ($downline_data['prev_page'] !== false): ?>
			<a href="./downline.php?page=<?php echo $page-1; ?>">Previous Page</a>
			<?php	endif; ?>

			<?php if ($downline_data['next_page'] !== false): ?>
			<a href="./downline.php?page=<?php echo $page+1; ?>">Next Page</a>
			<?php	endif; ?>
		</td>
	</tr>
	<?php	endif; ?>
</table>

<?php
include './Inc/footer.inc.php';
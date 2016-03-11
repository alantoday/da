<?php
//---------------------------------------------------------------
// Example usage of the client API which extracts the leads
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

//Set the paging params
$results_per_page = 25;
$pages = 10;

$page = (isset($_GET['page']) && ctype_digit((string)$_GET['page']) ? $_GET['page']:'');
$page_id = (isset($_GET['id']) ? $_GET['id']:'first');
$page_hash = (isset($_GET['hash']) ? $_GET['hash']:'');

//prepare the data for the leads
$api->prepareLeads($results_per_page, $pages, $page_id, $page, $page_hash);

$response = $api->getResponse();

$leads_data = array('data' => $response, 'code' => $api->getStatusCode(), 'next_page' => $api->getNextLink(), 'prev_page' => $api->getPrevLink());

include './Inc/header.inc.php';
?>

<h1 align="center">Leads</h1>

Response status code: <strong><?php echo $leads_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Source</strong></td>
		<td><strong>Tracking</strong></td>
		<td><strong>First Name</strong></td>
		<td><strong>Last Name</strong></td>
		<td><strong>E-mail</strong></td>
		<td><strong>Date Joined</strong></td>
		<td><strong>Status</strong></td>
		<td><strong>Action</strong></td>
	</tr>
	
	<?php foreach($leads_data['data']['records'] AS $leads_data_val): ?>
	<tr>
		<td><?php echo $leads_data_val['source']; ?>&nbsp;</td>
		<td><?php echo $leads_data_val['tracking']; ?>&nbsp;</td>
		<td><?php echo $leads_data_val['firstname']; ?>&nbsp;</td>
		<td><?php echo $leads_data_val['lastname']; ?>&nbsp;</td>
		<td><?php echo $leads_data_val['email']; ?></td>
		<td><?php echo $leads_data_val['date']; ?></td>
		<td><?php echo ucfirst($leads_data_val['status']); ?> (<?php echo $leads_data_val['status_description']; ?>)</td>
		<td><a href="./lead.php?id=<?php echo $leads_data_val['id']; ?>">View</a></td>
	</tr>
	<?php	endforeach; ?>
	
	<?php if ($leads_data['code'] == 206): ?>
	<tr>
		<td colspan="8" align="right">
			<?php if ($leads_data['prev_page'] !== false): ?>
			<a href="leads.php?<?php echo 'id='.$leads_data['data']['pagination']['previous']['id'].'&page='.$leads_data['data']['pagination']['previous']['page'].'&hash='.$leads_data['data']['pagination']['previous']['hash']; ?>">Previous Page</a>
			<?php	endif; ?>
			
			<?php if ($leads_data['next_page'] !== false): ?>
			<a href="leads.php?<?php echo 'id='.$leads_data['data']['pagination']['next']['id'].'&page='.$leads_data['data']['pagination']['next']['page'].'&hash='.$leads_data['data']['pagination']['next']['hash']; ?>">Next Page</a>
			<?php	endif; ?>
		</td>
	</tr>
	<?php	endif; ?>
</table>

<?php if ($leads_data['code'] == 206): ?>
Alternative Pagnation Solution:
<?php if (!empty($leads_data['data']['pagination']['first'])): ?>
	<a href="./leads.php?id=first">First Page</a>
<?php	endif; ?>

<?php if (!empty($leads_data['data']['pagination']['previous'])): ?>
	<a href="./leads.php?page=<?php echo $leads_data['data']['pagination']['previous']['page']; ?>&id=<?php echo $leads_data['data']['pagination']['previous']['id']; ?>&hash=<?php echo $leads_data['data']['pagination']['previous']['hash']; ?>">Previous Page</a>
<?php	endif; ?>

<?php if (!empty($leads_data['data']['pagination']['pages'])): ?>
	<?php foreach($leads_data['data']['pagination']['pages'] AS $page_p=>$page_data): ?>
		<?php if ($page_p != $page): ?><a href="./leads.php?page=<?php echo $page_p; ?>&id=<?php echo $page_data['id']; ?>&hash=<?php echo $page_data['hash']; ?>"><?php endif; ?><?php echo $page_p; ?><?php if ($page_p != $page): ?></a><?php endif; ?>
	<?php	endforeach; ?>
<?php	endif; ?>

<?php if (!empty($leads_data['data']['pagination']['next'])): ?>
	<a href="./leads.php?page=<?php echo $leads_data['data']['pagination']['next']['page']; ?>&id=<?php echo $leads_data['data']['pagination']['next']['id']; ?>&hash=<?php echo $leads_data['data']['pagination']['next']['hash']; ?>">Next Page</a>
<?php	endif; ?>

<?php if (!empty($leads_data['data']['pagination']['last'])): ?>
	<a href="./leads.php?id=last">Last Page</a>
<?php	endif; ?>
<?php endif;?>

<?php
include './Inc/footer.inc.php';
<?php
//---------------------------------------------------------------
// Example usage of the client API which extracts and shows the information
// for the subscription
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

//prepare the data for the subscription information
$api->prepareEmailSubscriptionInfo();

$subscription_data = array('data' => $api->getResponse(), 'code' => $api->getStatusCode());

include './Inc/header.inc.php';
?>

<h1 align="center">Subscription Info</h1>

Response status code: <strong><?php echo $subscription_data['code']; ?></strong> <br /><br />

<table>
	<tr>
		<td><strong>Global status</strong></td>
		<td><strong>Sponsor Emails</strong></td>
		<td><strong>System Emails</strong></td>
	</tr>
	<tr>
		<td><?php echo $subscription_data['data']['subscriptions']['global_status']; ?></td>
		<td><?php echo ($subscription_data['data']['subscriptions']['sponsor_emails'] === true ? "Yes":"No"); ?></td>
		<td><?php echo ($subscription_data['data']['subscriptions']['system_emails'] === true ? "Yes":"No"); ?></td>
	</tr>
	
	<tr>
		<td colspan="3" align="center"><strong>Lists</strong></td>
	</tr>
	
	<tr>
		<td><strong>List Id</strong></td>
		<td><strong>List</strong></td>
		<td><strong>Status</strong></td>
	</tr>
	<?php foreach($subscription_data['data']['subscriptions']['lists'] AS $lists): ?>
	<tr>
		<td><a href="./subscription_record.php?id=<?php echo $lists['id']; ?>"><?php echo $lists['id']; ?></a></td>
		<td><?php echo $lists['list']; ?></td>
		<td>
			<?php echo $lists['status']; ?>
			<?php 
			
			//if the status is Enabled or Unsubscribed we can change the subscription
			if ($lists['status'] == 'Enabled') {
				echo '(<a href="./subscription_change.php?list='.$lists['id'].'&status=0">Unsubscribe</a>)';
				}
			elseif ($lists['status'] == 'Unsubscribed') {
				echo '(<a href="./subscription_change.php?list='.$lists['id'].'&status=1">Enable</a>)';
				}
			
			?>
		</td>
	</tr>
	<?php	endforeach; ?>
	
</table>

<?php
include './Inc/footer.inc.php';
<?php include("../includes_my/header.php"); ?>
<?php include(PATH."/includes/functions_inf.php"); ?>
<?php
if (isset($_GET['action']) && $_GET['action']=="delete") {
	# Let them delete a CC if it his not linked to any live subscriptions
	# Validate: Double check that it's their card and it's not linked to an active subscription.
	$cards_array = InfGetCreditCards($mrow['inf_contact_id']);
	if (empty($cards_array[$_GET['card_id']])) {
		$error[] = "INVALID: The card id '{$_GET['card_id']}' that you wish to delete is not a valid card in your account";
		WriteArray($cards_array);
	}
	if (empty($error)) {
		$subscriptions_array = InfGetRecurringOrders($mrow['inf_contact_id']);
		foreach ($subscriptions_array as $sub_id => $subscription_row) {
			if (in_array($_GET['card_id'], array($subscription_row['CC1'],$subscription_row['CC2']))) {
				$error[] = "INVALID: The card id '{$_GET['card_id']}' can not be deleted because it is linked to an active subscription";
				break;
			}
		}
	}
	if (empty($error)) {
		# Flag card as deleted
		if(InfDeleteCreditCard ($_GET['card_id'])) {
			$msg[] = "SUCCESS: Your card has been deleted.";
		} else {
			$error[] = "ERROR: Unexpected error encounted while trying to delete your card.";			
		}
	}
}
?>
<?php echo MyWriteMidSection("MY CARDS", "Manage Your Credit Cards",
	"Make sure your billing details are all up to date",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($_GET['msg'])) $msg[] = $_GET['msg']; ?>
<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<p style="float:right"><a href="my-card.php">+ Add a card</a></p>
<div style="float:; padding-bottom:10px;"><img src="/images/icons/ccoptions.png" alt="" width="250" align="absmiddle" /></span></div>
<?php /*(<p>You can add backup cards to your account to help prevent your system and ad links being interupted because of a payment issue. If your Primary card fails, we will attempt to use your backup card(s) instead.</p>
*/ ?>
<?php  
	$table_head = "<table class='daTable' width='100%'><thead><tr>"
	. WriteTH("#")
	. WriteTH("Card Type")
	. WriteTH("Card Number")
	. WriteTH("Expiration Date")
	. WriteTH("Name on Card")
	. WriteTH("Billing Address")
	. WriteTH("Active<br>Subscriptions")
#	. WriteTH("Subscriptions<br>Backup Card")
	. WriteTH("Action")
	."</tr></thead>";
	$table_foot = "</table>";
// Get cards from INF
$cards_array = InfGetCreditCards($mrow['inf_contact_id']);
#WriteArray($cards_array);
#exit;
$subscriptions_array = InfGetRecurringOrders($mrow['inf_contact_id']);

#if ($mrow['username']=="sample") {
#	WriteArray($subscriptions_array);	
#}
foreach ($subscriptions_array as $sub_id => $subscription_row) {
	$cc1 = $cc2 = "";
	if ($subscription_row['CC1']) {
		$cards_array[$subscription_row['CC1']]['Primary'][] = WriteProductName($db, $subscription_row['ProductId']);
	}
	if ($subscription_row['CC2']) {
		$cards_array[$subscription_row['CC1']]['Backup'][] = WriteProductName($db, $subscription_row['ProductId']);
	}
}

$card_count = $cards_array;
$j = 0;
$table_rows = "";
foreach ($cards_array AS $card_id => $card_row) {
	$j++;
	$billing_address = sprintf("%s<br>%s %s %s<br>%s",
	 	$card_row['BillAddress1'], $card_row['BillCity'], $card_row['BillState'], $card_row['BillZip'], $card_row['BillCountry']);
	// If not already primary
	$delete_action = "";
	$primary_action = "";
	$primary = "";
	$type = ""; // eg, Primary or Backup
/*	if ($card_row['Id']<>$row['card_id']) {
		$primary_action = "<br> <a href='?card_id=$card_id&action=primary'>Make Primary</a>";  
		$type = "<font color=grey>Backup</font><br>"; 
		// If more than one row
		if ($card_count > 1) {
		$card_num = WriteCardNum($card_row['Last4']);
		$delete_action = "<br> <a onclick=\"return confirm('Are you SURE you want to delete the card: $card_num?')\" href='?card_id=$card_id&action=delete'>Delete</a>";   
		}
	} else {
		$type = "<font color=green><b>Primary</b></font><br>"; 
	}
*/
	$card_num = WriteCardNum($card_row['Last4']);
	if (empty($card_row['Primary'])) {
		$delete_action = "- <a onclick=\"return confirm('Are you SURE you want to delete the card: $card_num?')\" href='?card_id=$card_id&action=delete'>Delete</a>";   
	}
	$action = "<a href='my-card.php?card_id=$card_id'>Edit</a> $delete_action $primary_action";
	// If cc has expired
	if ($card_row['ExpirationYear'] . $card_row['ExpirationMonth'] < date("Ym")) {
		$bgcolor = "#FFDDDD";
		$expired = "<br><font color=red><b>Expired</b></font>";
	} else {
		$bgcolor = '';
		$expired = "";
	}
	$months_left = $card_row['ExpirationYear'].$card_row['ExpirationMonth'] - date("Ym");
	$MONTH_DAYS = array("01"=>31,"02"=>29,"03"=>31,"04"=>30,"05"=>31,"06"=>30,"07"=>31,"08"=>31,"09"=>30,"10"=>31,"11"=>30,"12"=>31);
	if ($months_left<0) {
		$bgcolor = "lightgrey";
		$expired = "<br><font color=red><b>Expired</b></font>";
	} elseif (!$months_left) {
		$days_left = $MONTH_DAYS[date("m")]-date("d");
		$expired = "<br><font color=red><b>Expires in $days_left days</b></font>";
	} else {
		$bgcolor = '';
		$expired = "";
	}
	$table_rows .= "<tr bgcolor='$bgcolor' valign=top>"
		.WriteTD($j)
		.WriteTD($card_row['CardType'])
		.WriteTD(WriteCardNum($card_row['Last4']))
		.WriteTD($card_row['ExpirationMonth'] ."/". $card_row['ExpirationYear'] . $expired)
		.WriteTD($card_row['NameOnCard'])
		.WriteTD($billing_address)
		.WriteTD(isset($card_row['Primary']) ? implode("<br>",$card_row['Primary']) : "")
#		.WriteTD(isset($card_row['Backup']) ? implode("<br>",$card_row['Backup']) : "")
		.WriteTD($action)
		."</tr>";
}
if (empty($table_rows)) {
	echo "<br><font color=grey> - You have no valid credit cards on file - </font>";
} else {
	echo $table_head . $table_rows . $table_foot;
}
?>
<?php include(INCLUDES_MY."footer.php"); ?>

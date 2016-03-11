<?php include("../includes_my/header.php"); ?>
<?php include(PATH."/includes/functions_inf.php"); ?>
<?php
if (isset($_POST['submit'])) {
				
	// Get subscriptions from INF
	if ($subscriptions_array = InfGetRecurringOrders($mrow['inf_contact_id'])) {
		foreach ($subscriptions_array as $sub_id => $subscription_row) {
			
			# Validation
/**			if ($_POST["card_id_cc1_$sub_id"]==$_POST["card_id_cc2_$sub_id"]) {
				$error[] = "DUPLICATE: Your Backup Card should be different from your Primary Card for your ".WriteProductName($db, $subscription_row['ProductId'])." subscription";
			}
*/			if (empty($error)) {
				$update_cc1 = $update_cc2 = false;
				if ($_POST["card_id_cc1_$sub_id"]!=$subscription_row['CC1'] && $_POST["card_id_cc1_$sub_id"]<>0) {
					$update_cc1 = true;	
					$msg[] = "SUCCESS: Your Primary Card for your '".WriteProductName($db, $subscription_row['ProductId'])."' subscription is updated";
				}
/**				if ($_POST["card_id_cc2_$sub_id"]!=$subscription_row['CC2'] && $_POST["card_id_cc2_$sub_id"]<>0) {
					$update_cc2 = true;	
					$msg[] = "SUCCESS: Your Backup Card for your '".WriteProductName($db, $subscription_row['ProductId'])."' subscription is updated";
				}
*/				if ($update_cc1 || $update_cc2) {
					InfUpdateRecurringOrderCC($sub_id, $_POST["card_id_cc1_$sub_id"], $_POST["card_id_cc2_$sub_id"]);	
				}
			}
		}
	} else {
		$error[] = "ERROR: There are no Active subscriptions to update";
	}
	// If no updated/change
	if (empty($error) && empty($msg)) {
		$error[] = "NO CHANGE: You don't appear to have made any changes";	
	}
}
?>
<?php echo MyWriteMidSection("MY SUBSCRIPTIONS", "Review Your Subscriptions Here",
	"Review and manage your account subscriptions",
	"MY PROFILE","/my-business/my-profile.php",
	"MY STATUS", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<? echo isset($_GET['msg']) ? "<h4><b><font color=green>{$_GET['msg']}</font></b></h4><br>" : "";?>
<style>
	select {
		width:200px;	
	}
</style>
<form method="POST">
<table class="daTable" width='100%'>
  <thead><tr>
    <td>#</td>
    <td>Product</td>
    <td align='right'>Amount</td>
    <td>Next Bill Date</td>
    <td>Previous Bill Date</td>
    <td>Primary Card</td>
<?php /*    <td>Backup Card</td>
    <td>Action</td> */ ?>
  </tr></thead>
  <?
// Get cards from INF
if ($subscriptions_array = InfGetRecurringOrders($mrow['inf_contact_id'])) {
	// Get all their CC Details - some possibly linked to these subscriptions
	$cards_array = InfGetCreditCards($mrow['inf_contact_id']);
}
$j = 0;
foreach ($subscriptions_array as $sub_id => $subscription_row) {
	$j++;
	$cc1 = $cc2 = "";
	if (isset($cards_array[$subscription_row['CC1']]['Last4'])) {
		$cc1 = WriteCardNum($cards_array[$subscription_row['CC1']]['Last4']);
		$_POST["card_id_cc1_$sub_id"] = $subscription_row['CC1'];
	}
	if (isset($cards_array[$subscription_row['CC2']]['Last4'])) {
		$cc2 = WriteCardNum($cards_array[$subscription_row['CC2']]['Last4']);
		$_POST["card_id_cc2_$sub_id"] = $subscription_row['CC2'];
	}
	$action = "";
#	$action = "<a onclick=\"return confirm('Are you SURE you want to delete the card: $card_num?')\" href='?subscriptions.php?subscription_id=$subscription_id&action=cancel'>Cancel</a>";   

	echo "<tr valign=top>"
		.WriteTD($j)
#		.WriteTD(WriteDate($subscription_row['StartDate']))
		.WriteTD(WriteProductName($db, $subscription_row['ProductId']))
		.WriteTD(WriteDollarCents($subscription_row['BillingAmt']), TD_RIGHT)
		.WriteTD(WriteDate($subscription_row['NextBillDate']))
		.WriteTD(WriteDate($subscription_row['LastBillDate']))
		.WriteTD(_WriteCardSelect($mrow['inf_contact_id'], $sub_id,"cc1", false))
#		.WriteTD(_WriteCardSelect($mrow['inf_contact_id'], $sub_id,"cc2", true))
#		.WriteTD($action)
		."</tr>";
}
?>
</table>
<?
if ($j==0) {
	echo "<br><font color=grey> - You have no active subscriptions - </font>";
} else {
	echo "<p align=right><br>".WriteButton("Save Changes")."</p>";
}
echo "</form>";

function _WriteCardSelect ($inf_contact_id, $sub_id, $number, $include_blank) {	
	return 	"<select name='card_id_{$number}_$sub_id' id='cards_$sub_id'>
            " . WriteSelect(isset($_POST["card_id_{$number}_$sub_id"]) ? $_POST["card_id_{$number}_$sub_id"] : "", WriteCardOptions($inf_contact_id, $include_blank), false, false) ."
        </select></div>";
}
?>
<?php include(INCLUDES_MY."footer.php"); ?>

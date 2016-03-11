<?php include_once("../../includes/functions_inf.php"); ?>
<?php
if (empty($_SESSION['member_id'])) {
	header("location: /?action=logout&pg=".urlencode($_SERVER['REQUEST_URI']));	
} 
# Enforce https on this page
if ($_SERVER['HTTP_HOST'] == "my.digitalaltitude.co") {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ) {
		header("Location: "."https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
}
$mrow = GetRowMember($db, $_SESSION['member_id']); 
// For now don't let them ADD a card (redirect to summary page)
$msg = '';
$error = '';

$edit_add = empty($_GET['card_id']) ? "Add" : "Edit";

if(isset($_POST['submit'])) {
	// Test for MISSING data
	if ($edit_add=="Add") {
		$_POST['CardNumber'] = str_replace(" ","",$_POST['CardNumber']);
		$_POST['CardNumber'] = str_replace("-","",$_POST['CardNumber']);
		if(!$_POST['CardNumber']) { $error[] = "MISSING: Your Card Number."; }
		elseif(strlen($_POST['CardNumber']) < 15) { $error[] = "INVALID: Your Card Number is too short."; }
		elseif(!ValidCreditCardNumberFormat($_POST['CardNumber'])) {
			$error[] = "INVALID: Your Card Number is not valid. Please try again.";
		}
	}
	//  if(!$bill_card_code) { $error[] = "MISSING: Your Credit Verification Code."; }
	if(!$_POST['ExpirationMonth']) { $error[] = "MISSING: The credit card Expiration Month."; }
	if(!$_POST['ExpirationYear']) { $error[] = "MISSING: Your credit card Expiration Year."; }
	if($_POST['ExpirationMonth'] && $_POST['ExpirationYear']) {
		if ($_POST['ExpirationYear'].$_POST['ExpirationMonth'] < date("Ym")) {
			$error[] = "EXPIRED: Your credit card has expired.";
		}
	}
	if ($edit_add=="Add") {
		if(!$_POST['CVV2']) { $error[] = "MISSING: Your Card CVV."; }
		elseif(strlen($_POST['CVV2']) < 3) { $error[] = "INVALID: Your Card CVV should be 3 to 4 digits long."; }
	}
	if(!trim($_POST['NameOnCard'])) { $error[] = "MISSING: Your Name on Card."; }
	if(!trim($_POST['BillAddress1'])) { $error[] = "MISSING: Your Billing Address."; }
	if(!trim($_POST['BillCity'])) { $error[] = "MISSING: Your Billing City."; }
	if(!trim($_POST['BillState'])) { $error[] = "MISSING: Your Billing State/Province."; }
	if(!trim($_POST['BillZip'])) { $error[] = "MISSING: Your Billing Zip/Postcode."; }
	if(!$_POST['BillCountry']) { $error[] = "MISSING: Your Billing Country."; }
	if(isset($_POST['BillCountry']) && isset($_POST['BillZip']) 
		&& $_POST['BillCountry']=="United States" 
		&& trim($_POST['BillZip']) != "" 
		&& strlen(trim($_POST['BillZip'])) < 5) { $error[] = "INVALID: Invalid ZIP Code for United States."; }
	# Create new card	
	if (!isset($error[0]) && $edit_add=="Add") {
		$card_id = InfAddCreditCard ($mrow['inf_contact_id'],$_POST['CardNumber'],$_POST['ExpirationMonth'],$_POST['ExpirationYear'],$_POST['CVV2'],$_POST['NameOnCard'],$_POST['BillAddress1'],$_POST['BillCity'],$_POST['BillState'],$_POST['BillZip'],$_POST['BillCountry']);
		
	    if ($_POST['skip_h']) {	    	
			$exp_date = $_POST['ExpirationMonth']."/". $_POST['ExpirationYear'];
			$card_description = WriteCardType($_POST['CardNumber']).": ".WriteCardNum(substr($_POST['CardNumber'], -4));
			echo "<script>\ntop.frame_popup_callback_AddCardIframe(['".$card_id."','".$card_description."']);\n</script>\n";
			exit;
		}
  			
		header ("Location: http://".$_SERVER['HTTP_HOST']."/my-account/my-cards.php?msg=SUCCESS: Your new card has been added.");
		#WriteArray($add_res);
	}// Update CC Details (no cc number change
	if (!isset($error[0]) && $edit_add=="Edit") {
		InfUpdateCreditCard ($_GET['card_id'],"",$_POST['ExpirationMonth'],$_POST['ExpirationYear'],$_POST['NameOnCard'],$_POST['BillAddress1'],$_POST['BillCity'],$_POST['BillState'],$_POST['BillZip'],$_POST['BillCountry']);
		header ("Location: http://".$_SERVER['HTTP_HOST']."/my-account/my-cards.php?msg=SUCCESS: Your card charges have been saved.");
	}
	
################################################################################################
# Process ERROR

}
?>
<?php
		include("../includes_my/header.php");
		//echo "<script>\ntop.frame_popup_callback_AddCardIframe(['999','this is test card']);\n</script>\n";
		
?>
<?
$show_form = true;
if(!isset($_POST['submit'])) {
	if (!empty($_GET['card_id'])) {
		if ($card_row = InfGetCreditCard($mrow['inf_contact_id'], $_GET['card_id'])) {
			$_POST['NameOnCard'] = $card_row['NameOnCard'];
			$_POST['BillAddress1'] = $card_row['BillAddress1'];
			$_POST['BillCity'] = $card_row['BillCity'];
			$_POST['BillState'] = $card_row['BillState'];
			$_POST['BillZip'] = $card_row['BillZip'];
			$_POST['BillCountry'] = $card_row['BillCountry'];
			$_POST['CardNumber'] = WriteCardNum($card_row['Last4']);
			$_POST['ExpirationMonth'] = $card_row['ExpirationMonth'];
			$_POST['ExpirationYear'] = $card_row['ExpirationYear'];
		} else {
			// Invalid Card or not linked to that member(for that member)
			$error[] = "INVALID: Card ID";
			$show_form = false;
		}
	}
}
?>

<?php
	if(!$skip_header){ 
		echo MyWriteMidSection("$edit_add MY CARD", "$edit_add Your Credit Card Here",
		"Make sure your billing details are all up to date",
		"MY RANK","/my-business/my-rank.php",
		"MY TEAM", "/my-business/my-team.php");
	} 
?>
<?php 
	if(!$skip_header){
		include("my-account_menu.php"); 
	 	echo MyWriteMainSectionTop(30);
	}
?> 
<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>
<?php /*<div style="float:right"><p><a href="my-cards.php">Return to My Cards summary page.</a></p></div> */ ?>
<div style="float:; padding-bottom:10px;"><img src="/images/icons/ccoptions.png" width="250" /></div>
<? if(empty($_GET['popup'])){ ?>
<? } ?>
<style>
input {
	display:block;
}
</style>
<? if ($show_form) { ?>
<form method="post">
    <? if(isset($_GET['popup'])){ ?><input type="hidden" name="popup" value="<? echo $_GET['popup']?>" /><? } ?>
    <? if($skip_header) { ?>
    	<input type="hidden" name="skip_h" value="1">
    <? } ?>	
    <input type="hidden" name="CardNumberOrig" value="<? echo $g?>" />
    <table><tr>
    <td>Card Number:</td>
    <td>
	<?php if ($edit_add!="Add") { ?>
    <input name="CardNumber" type="hidden" id="CardNumber"  value="<?=isset($_POST['CardNumber']) ? $_POST['CardNumber'] : ""?>"/>
	<?php } ?>
    <input name="CardNumber" type="text" id="CardNumber" <?php if ($edit_add!="Add") echo "DISABLED"; ?> value="<?=isset($_POST['CardNumber']) ? $_POST['CardNumber'] : ""?>" maxlength="20"/>
    <? //=WriteFieldCardNumber($bill_card_old, "bill_card_new")?>
    </td>
    </tr><tr>
    <td>Expiration Month:</td>
    <td><?=WriteFieldCardExpMonth(isset($_POST['ExpirationMonth']) ? $_POST['ExpirationMonth'] : "", "ExpirationMonth")?></td>
    </tr><tr>
    <td>Expiration Year:</td>
    <td><?=WriteFieldCardExpYear(isset($_POST['ExpirationYear']) ? $_POST['ExpirationYear'] : "", "ExpirationYear")?>
    <? //=WriteFieldCardExpYear($ExpirationMonth, $ExpirationYear, "ExpirationMonth", "ExpirationYear")?></td>
<?php if ($edit_add=="Add") { ?>
    </tr><tr>
    <td>CVV:</td>
    <td><input maxlength="4" name="CVV2" type="text" value="<?=isset($_POST['CVV2']) ? $_POST['CVV2'] : ""?>"/></td>
<?php } ?>
    </tr><tr>
    <td>Name on Card:</td>
    <td><input maxlength="50" name="NameOnCard" type="text" value="<?=isset($_POST['NameOnCard']) ? $_POST['NameOnCard'] : ""?>"/></td>
    </tr><tr>
    <td>Country:</td>
    <td><?=WriteFieldCountry(isset($_POST['BillCountry']) ? $_POST['BillCountry'] : "", "BillCountry")?></td>
    </tr><tr>
    <td>Billing Address:</td>
    <td><input name="BillAddress1" type="text" id="BillAddress1" value="<?=isset($_POST['BillAddress1']) ? $_POST['BillAddress1'] : ""?>" maxlength="60"/></td>
    </tr><tr>
    <td>City:</td>
    <td><input name="BillCity" type="text" id="BillCity" value="<?=isset($_POST['BillCity']) ? $_POST['BillCity'] : ""?>" maxlength="40"/></td>
    </tr><tr>
    <td>State/Province:</td>
    <td><input name="BillState" type="text" id="BillState" value="<?=isset($_POST['BillState']) ? $_POST['BillState'] : ""?>" maxlength="40"/></td>
    <? //=WriteFieldState($BillState, "BillState")?>
    </tr><tr>
    <td>Zip/Postcode:</td>
    <td><input name="BillZip" type="text" id="BillZip" value="<?=isset($_POST['BillZip']) ? $_POST['BillZip'] : ""?>" maxlength="20"/></td>
    </tr><tr>
    <td>&nbsp;</td>
    <td><?=WriteButton("Save Changes")?></td>
    </table>
    
</form>
<? } ?>
<?php 
if(!$skip_header){ 
include(INCLUDES_MY."footer.php"); 
}
?>


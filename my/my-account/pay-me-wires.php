<?php
# Enforce https on this page
if ($_SERVER['HTTP_HOST'] == "my.digitalaltitude.co") {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ) {
		header("Location: "."https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
}
include("../includes_my/header.php"); 

$query = "SELECT * FROM pay WHERE member_id = {$_SESSION['member_id']} AND pay_type='Wire'";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$pay_row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
	$pay_by = $_POST['pay_by'];
	$pay_address = trim($_POST['pay_address']);
	$pay_city = trim($_POST['pay_city']);
	$pay_state = trim($_POST['pay_state']);
	$pay_zip = trim($_POST['pay_zip']);
	$pay_name = trim($_POST['pay_name']);
	$pay_account = trim($_POST['pay_account']);
	$pay_account_check = trim($_POST['pay_account_check']);
	$pay_country = $_POST['pay_country'];
	$pay_bank_country = $_POST['pay_bank_country'];
	$pay_routing = $_POST['pay_routing'];
	$pay_bank_name = $_POST['pay_bank_name'];
	$is_iban = $_POST['is_iban'];
	$routing_error = $_POST['routing_error'];
	if (!$pay_address) { $error[] = "Missing: Your Address"; }
	if (!$pay_city) { $error[] = "Missing: Your City"; }
	if (!$pay_state) { $error[] = "Missing: Your State"; }
	if (!$pay_zip) { $error[] = "Missing: Your Zip"; }
	if (!$pay_name) { $error[] = "Missing: Your Account Name"; }
	if (!$pay_bank_country) { $error[] = "Missing: Your Bank Country"; }
	if (!$pay_account) { $error[] = "Missing: Your Account/IBAN Number"; }
	if (!$pay_account_check) { $error[] = "Missing: Your Re-enter Account/IBAN Number"; }
	if ($pay_account && $pay_account_check && ($pay_account!=$pay_account_check)) { $error[] = "Invalid: Your Account Number and Re-Enter Account Number do not match."; }

	if (!$pay_routing) { $error[] = "Missing: Your SWIFT Code"; }
# Temporarily remove until SWIFT check is working again
#	elseif (!$pay_bank_name || $pay_bank_name=='TEST BANK' || $pay_bank_name=='Invalid: SWIFT Code.<BR>') {
#   		$error[] = (($routing_error)?$routing_error:"Invalid SWIFT Code.");
#	}

//   if (preg_match('/[^0-9]/', $pay_routing)) { $error[] = "Your Routing Number Must Contain 9 Digits"; }
   	if (empty($error)) {
		$update = $insert = false;
		if ($pay_row) {
			$sql_type = "UPDATE";
			$update = true;
			$sql_end = "WHERE member_id={$_SESSION['member_id']} AND pay_type='Wire'";
		} else {
			$sql_type = "INSERT INTO";
			$insert = true;
			$sql_end = ", member_id={$_SESSION['member_id']}";
		}
		$query = "$sql_type pay SET
			".(($insert)?"pay_type		='Wire',":'')."
			pay_name		='".addslashes($pay_name)."',
			pay_address		='".addslashes($pay_address)."',
			pay_city		='".addslashes($pay_city)."',
			pay_state		='".addslashes($pay_state)."',
			pay_zip			='".addslashes($pay_zip)."',
			pay_bank_name	='".addslashes($pay_bank_name)."',
			".(($is_iban=='IBAN')?"pay_iban":'pay_account')."='$pay_account',
			pay_routing		='$pay_routing',
			pay_country		='$pay_country',
			pay_bank_country='$pay_bank_country',
			pay_date		=now()
			$sql_end";
		 $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
		 $msg[] = "SUCCESS: Your changes are saved.";			

		 if($insert){
			$query = "UPDATE members SET pay_by='Wire' WHERE member_id={$_SESSION['member_id']}";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		 	$msg[] = "ALSO: Your Earnings Payout Default has been set to Wire.";
		 }
	
		 // send notification of ACH Change
		 $msg2member = "This is an email notification of your recent change to your
Wire Account Details today, ".WriteDate(date('Y-m-d h:i:sa',time()))." in your back office.

You can review the current settinga via 
http://my.digitalaltitude.co/my-account/my-wires.php
	
	
	
	".CANSPAM_ADDRESS;
	
#		 mail($mrow['email'], "DA Wire Details Change Notification", "$msg2member", "From: Digital Altitude <noreply@myprou.co>");
	}
}

if (empty($_POST['submit'])) {
	$is_iban		='Account';
	if (!empty($pay_row['pay_iban'])) $is_iban='IBAN';
	$pay_name 		= $pay_row['pay_name'];
	$pay_bank_name 	= $pay_row['pay_bank_name'];
	$pay_account 	= (($pay_row['pay_iban'])?$pay_row['pay_iban']:$pay_row['pay_account']);
	$pay_routing 	= $pay_row['pay_routing'];
	$pay_date 		= $pay_row['pay_date'];
	$pay_address 	= $pay_row['pay_address'];
	$pay_city 		= $pay_row['pay_city'];
	$pay_state 		= $pay_row['pay_state'];
	$pay_zip 		= $pay_row['pay_zip'];
	$pay_country 	= $pay_row['pay_country'];
	$pay_bank_country = $pay_row['pay_bank_country'];
}

?>
<?php echo MyWriteMidSection("PAY ME", "Manage Your Commission Payout Details",
	"Select how you'd like to receive your commissions",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>


<div style="float:right; text-align:right"><? if (isset($pay_row['pay_date'])) echo "(Last Updated: ".WriteDate($pay_row['pay_date']).")";?></div>
<h4> International: Wire Transfers - ($35 fee per payment)</h4>
<br><p>This page allows you to specify the details of a bank account  for receiving earnings payments via direct deposit. You will receive transfers via Wire.    </p>
    <form  method="post">
      <table width="100%" border="0" cellspacing="3" cellpadding="0">
      		<tr>
              <td align="right" width="200px">Receive Earnings Via:</td>
              <td valign='top'>
              <select name='pay_by'>
                <option value="Wire" <? if ($mrow['pay_by']=='Wire') echo 'SELECTED' ?>>Wire</option>
                <option value="Check" <? if ($mrow['pay_by']=='Check') echo 'SELECTED' ?>>Check</option>
                <? if (!in_array($mrow['country'], array('', 'United States', 'Canada')) || ($mrow['pay_by']=='Paypal')) { ?>
                <option value="Paypal" <? if ($mrow['pay_by']=='Paypal') echo 'SELECTED' ?>>Paypal</option>
                <? } ?>
                <option value="ACH" <? if ($mrow['pay_by']=='ACH') echo 'SELECTED' ?>>Direct Deposit</option>
              </select>
              </td>
            </tr>
            <tr>
            	<td colspan="2">&nbsp;</td>
            </tr>
      <tr>
        <td align="right">Beneficiary/Account Name: </td>
        <td><input type="text" name="pay_name" value="<?=stripslashes($pay_name)?>" /></td>
      </tr>
       <tr>
        <td align="right">Beneficiary Street Address: </td>
        <td><input name="pay_address" type="text" value="<?=$pay_address?>" /></td>
      </tr>
      <tr>
        <td align="right">City: </td>
        <td><input name="pay_city" type="text" value="<?=$pay_city?>" /></td>
      </tr>
      <tr>
        <td align="right">Country: </td>
        <td><?=WriteFieldCountry($pay_country,'pay_country',0,1)?></td>
      </tr>
      <tr>
        <td align="right">State: </td>
        <td><?=WriteFieldState($pay_state,'pay_state')?></td>
      </tr>
      <tr>
        <td align="right">Zip: </td>
        <td><input name="pay_zip" type="text" size="15" value="<?=$pay_zip?>" /></td>
      </tr>
      <tr>
      	<td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td align="right">SWIFT Number:</td>
        <td><input name="pay_routing" id="pay_routing" type="text" value="<?=$pay_routing?>" size="15" maxlength="15" /><? // onblur="bankNameFromRouting();" ?>
          <? if (isset($routing_error)) echo "<span id='bank_name' style='color:red;'>$routing_error</span>";?>
          <input type="hidden" name='routing_error' id='routing_error' value="" /> </td>
      </tr>
      <tr>
        <td align="right">Bank Name: </td>
        <td> <? //disabled="disabled"           <input type="hidden" name="pay_bank_name" id="pay_bank_name" value="" readonly="readonly"/></td>  ?>
        <? //<input type="text" id="pay_bank_name_display" name="pay_bank_name_display" value="" style="width:300px;border:0px;background-color:#ffffff;"/>  ?>
          <input type="text" name="pay_bank_name" id="pay_bank_name" value="<?=stripslashes($pay_bank_name)?>" /></td>
      </tr>
      <tr>
        <td align="right">Bank's Country: </td>
        <td><?=WriteFieldCountry($pay_bank_country,'pay_bank_country')?></td>
      </tr>
      <tr>
        <td align="right">Account Type: </td>
        <td><select name='is_iban'><?=WriteSelect($is_iban,array('IBAN','Account'))?></select></td>
      </tr>
      <tr>
        <td align="right">Account / IBAN Number:</font></td>
        <td><input type="text" name="pay_account" id="pay_account" size="35" value="<?=$pay_account?>" /></td>
      </tr>
      <tr>
        <td align="right"> Re-Enter  Account Number:</font></td>
        <td><input type="text" id="pay_account_check" name="pay_account_check" size="35" value="<?=isset($pay_account_check)?$pay_account_check:$pay_account?>" /></td>
      </tr>


      <!--<tr>
        <td align="right">Comment (optional):</td>
        <td><input type="text" name="pay_comment" value="<?=$pay_comment?>" />
        eg, Chase Account</td>
      </tr>-->
      <tr>
        <td width="34%" align="right">&nbsp;</td>
        <td width="66%"><?=WriteButton("Save Changes")?></td></tr>
    </table>
     <script>
function IsNumeric(input)
{
    return (input - 0) == input && input.length > 0;
}

    function bankNameFromRouting(){
    	routing_number = $('#pay_routing').val();
				$.post("../scripts/ajax/wire_getData.php", {routing_number: routing_number}, function(db)
				{
					if(db.bank_name){
						$("#bank_name").css({"color":"#00B800"});
						$("#bank_name").html('SWIFT Code validated');
						$("#pay_bank_name_display").val(db.bank_name);
						$("#pay_bank_name").val(db.bank_name);
					}else{
						$("#bank_name").css({"color":"#FF0000"});
						$("#bank_name").html('Invalid SWIFT Code');
						$("#routing_error").val('Invalid SWIFT Code.<BR>');
						$("#pay_bank_name_display").val('');
						$("#pay_bank_name").val('');
					}
				},"json");
		}
    </script>

  </form>

<?php include(INCLUDES_MY."footer.php"); ?>

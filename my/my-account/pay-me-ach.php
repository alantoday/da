<?php
# Enforce https on this page
if ($_SERVER['HTTP_HOST'] == "my.digitalaltitude.co") {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ) {
		header("Location: "."https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
}
include("../includes_my/header.php"); 

$query = "SELECT * FROM pay WHERE member_id = {$_SESSION['member_id']} AND pay_type='ACH'";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$pay_row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
	$pay_address = trim($_POST['pay_address']);
	$pay_city = trim($_POST['pay_city']);
	$pay_state = trim($_POST['pay_state']);
	$pay_zip = trim($_POST['pay_zip']);
	$pay_name = trim($_POST['pay_name']);
	$pay_account = trim($_POST['pay_account']);
	$pay_account_check = trim($_POST['pay_account_check']);
	$pay_account_type = trim($_POST['pay_account_type']);
	$pay_class = trim($_POST['pay_class']);
	$pay_country = $_POST['pay_country'];
	$pay_bank_country = $_POST['pay_bank_country'];
	$pay_routing = $_POST['pay_routing'];
#	$pay_bank_name = $_POST['pay_bank_name'];
	$routing_error = $_POST['routing_error'];
	if (!$pay_address) { $error[] = "Missing: Your Address"; }
	if (!$pay_city) { $error[] = "Missing: Your City"; }
	if (!$pay_state) { $error[] = "Missing: Your State"; }
	if (!$pay_zip) { $error[] = "Missing: Your Zip"; }
	if (!$pay_bank_country) { $error[] = "Missing: Your Bank Country"; }
	if (!$pay_name) { $error[] = "Missing: Your Account Name"; }
	if (!$pay_account_type) { $error[] = "Missing: Your Account Type"; }
	if (!$pay_class) { $error[] = "Missing: Your Account Class"; }
	if (!$pay_account) { $error[] = "Missing: Your Account Number"; }
	if (!$pay_account_check) { $error[] = "Missing: Your Re-enter Account Number"; }
	if ($pay_account && $pay_account_check && ($pay_account!=$pay_account_check)) { $error[] = "Invalid: Your Account Number and Re-Enter Account Number do not match."; }
	
	if (!$pay_routing) { $error[] = "Missing: Your Routing Number"; }
	elseif ($pay_account && !is_numeric($pay_account)) { $error[] = "Invalid: Your Account Number Must Contain Only Digits"; }
	elseif (strlen($pay_routing)<>9 || !is_numeric($pay_routing)) { $error[] = "Invalid: Your Routing Number Must Contain 9 Digits"; }
#	elseif (!$pay_bank_name || $pay_bank_name=='TEST BANK' || $pay_bank_name=='Invalid: Routing Number<BR>') { $error[] = (($routing_error)?$routing_error:"Invalid Routing Number."); }
	
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
			".(($insert)?"pay_type ='ACH',":'')."
			pay_name		='".addslashes($pay_name)."',
			pay_address		='".addslashes($pay_address)."',
			pay_city		='".addslashes($pay_city)."',
			pay_state		='".addslashes($pay_state)."',
			pay_zip			='".addslashes($pay_zip)."',
			pay_account_type='$pay_account_type',
			pay_class		='$pay_class',
			pay_account		='$pay_account',
			pay_routing		='$pay_routing',
			pay_country		='$pay_country',
			pay_bank_country ='$pay_bank_country',
			pay_date		=now()
			$sql_end";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
			$msg[] = "SUCCESS: Your changes are saved.";			
			
			if($insert){
				$query = "UPDATE members SET pay_by='ACH' WHERE member_id={$_SESSION['member_id']}";
				$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
				$msg[] = "ALSO: Your Earnings Payout Default has been set to Wire.";
			}


	 		// send notification of ACH Change

	 		$msg2member = "This is an email notification of your recent change to your Direct Deposit Account Details today, ".WriteDate(date('Y-m-d h:i:sa',time()))." in your back office.

You can review the current settinga via 
http://my.digitalaltitude.co/my-account/my-wires.php
	
	
	
".CANSPAM_ADDRESS;

#		 mail($mrow['email'], "DA ACH Details Change Notification", "$msg2member", "From: Digital Altitude <noreply@myprou.co>");
	//	$pay_date = date('Y-m-d');
   }
}
if (empty($_POST['submit'])) {	
	$pay_name 		= $pay_row['pay_name'];
	$pay_account_type 	= $pay_row['pay_account_type'];
	$pay_class 		= $pay_row['pay_class'];
	$pay_bank_name 	= $pay_row['pay_bank_name'];
	$pay_account 	= $pay_row['pay_account'];
	$pay_routing 	= $pay_row['pay_routing'];
	$pay_comment 	= $pay_row['pay_comment'];
	$pay_date 		= $pay_row['pay_date'];
	$pay_address 	= $pay_row['pay_address'];
	$pay_city 		= $pay_row['pay_city'];
	$pay_state 		= $pay_row['pay_state'];
	$pay_zip 		= $pay_row['pay_zip'];
	$pay_bank_country 	= $pay_row['pay_bank_country'];
	$pay_country 	= $pay_row['pay_country'];
}
?>
<?php echo MyWriteMidSection("MY PROFILE", "Manage Your Account Profile Here",
	"Manage your photo and welcome message to your new team as needed",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<div style="float:right; text-align:right">
<? if (isset($pay_row['pay_date'])) echo "<i>Last Updated: ".WriteDate($pay_row['pay_date'])."</i>";?></div>

<h4 id='page_title'> US: Direct Deposit Details</h4>
<p>This page allows you to specify the details of a United States bank account  for receiving earnings payments via direct deposit. You will receive transfers via electronic ACH (automated clearning house) deposit - this is NOT a Wire Transfer.</p>
    <form method="post">
      <table width="100%" border="0" cellspacing="3" cellpadding="0">
            <tr>
              <td align="right" width="200px">Receive Earnings Via:</td>
              <td valign='top'>
                <select name='pay_by'>
                    <option value="ACH" <? if ($mrow['pay_by']=='ACH') echo 'SELECTED' ?>>Direct Deposit</option>
                    <option value="Check" <? if ($mrow['pay_by']=='Check') echo 'SELECTED' ?>>Check</option>
                    <? if (!in_array($mrow['pay_country'], array('United States', 'Canada',''))) { ?>
                    <option value="Paypal" <? if ($mrow['pay_by']=='Paypal') echo 'SELECTED' ?>>Paypal</option>
                    <? } ?>
                    <option value="Wire" <? if ($mrow['pay_by']=='Wire') echo 'SELECTED' ?>>Wire</option>
                </select>
              </td>
            </tr>
            <tr>
            	<td colspan="2">&nbsp;</td>
            </tr>

      <tr>
        <td align="right">Beneficiary/Account Name: </td>
        <td><input type="text" name="pay_name" value="<?=stripslashes($pay_name)?>" />
        , eg, John Smith</td>
      </tr>
       <tr>
        <td align="right"> Beneficiary Street Address: </td>
        <td><input name="pay_address" type="text" value="<?=$pay_address?>" /></td>
      </tr>
      <tr>
        <td align="right">City: </td>
        <td><input name="pay_city" type="text" value="<?=$pay_city?>" /></td>
      </tr>
      <tr>
        <td align="right">Country: </td>
        <td><?=WriteFieldCountry($pay_country,'pay_country')?></td>
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
        <td align="right">Account Type: </td>
        <td><select name="pay_account_type"><?=WriteSelect($pay_account_type,array(''=>'- Select -','Checking'=>'Checking','Savings'=>'Savings'))?></select></td>
      </tr>
      <tr>
        <td align="right">Account Class: </td>
        <td><select name="pay_class"><?=WriteSelect($pay_class,array(''=>'- Select -','Personal'=>'Personal','Corporate'=>'Business'))?></select></td>
      </tr>
      <tr>
        <td align="right">Routing Number:</td>
        <td><input name="pay_routing" id="pay_routing" type="text" value="<?=$pay_routing?>" maxlength="9" onblur="bankNameFromRouting();"/>
          <?=(isset($routing_error)?"<span id='bank_name' style='color:red;'>$routing_error</span>":"<span id='bank_name'><i>See sample in check image below</i></span>")?><input type="hidden" name='routing_error' id='routing_error' value="" /> </td>
      </tr>
<?php /*      <tr>
        <td align="right">Bank Name: </td>
        <td><input type="text" id="pay_bank_name_display" name="pay_bank_name_display" value="<?=stripslashes($pay_bank_name)?>" disabled="disabled" style="width:300px;border:0px;background-color:#ffffff;"/>
          <input type="hidden" name="pay_bank_name" id="pay_bank_name" value="<?=stripslashes($pay_bank_name)?>" readonly/></td>
      </tr>
*/ ?>      <tr>
        <td align="right">Bank Country: </td>
        <td><?=WriteFieldCountry('US','pay_country_display',0,1,1)?>
        <input type="hidden" name="pay_bank_country" value="United States"/></td>
      </tr>
      <tr>
        <td align="right">  Account Number:</font></td>
        <td><input type="text" name="pay_account" id="pay_account" size="15" value="<?=$pay_account?>" /></td>
      </tr>
      <tr>
        <td align="right"> Re-Enter  Account Number:</font></td>
        <td><input type="text" id="pay_account_check" name="pay_account_check" size="15" value="<?=(isset($pay_account_check)?$pay_account_check:$pay_account)?>" /></td>
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
	    	if(routing_number.length < 9 || !IsNumeric(routing_number)){
	    		$("#bank_name").css({"color":"#FF0000"});
				$("#bank_name").html('Must contain 9 digits exactly');
				$("#pay_bank_name_display").val('');
				$("#pay_bank_name").val('');
	    	}else{
				$.post("../scripts/ajax/pay_getData.php", {routing_number: routing_number}, function(db)
				{
					if(db.bank_name){
						$("#bank_name").css({"color":"#00B800"});
						$("#bank_name").html('Routing Number validated');
						$("#pay_bank_name_display").val(db.bank_name);
						$("#pay_bank_name").val(db.bank_name);
					}else{
						$("#bank_name").css({"color":"#FF0000"});
						$("#bank_name").html('Invalid Routing Number');
						$("#routing_error").val('Invalid Routing Number<BR>');
						$("#pay_bank_name_display").val('');
						$("#pay_bank_name").val('');
					}
				},"json");
			}
		}
    </script>

  </form>
  <br>
<p align="center"><img src="/images/icons/achcheck.jpg" /></p>

<?php include(INCLUDES_MY."footer.php"); ?>

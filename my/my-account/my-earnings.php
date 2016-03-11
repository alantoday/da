<?php include("../includes_my/header.php"); ?>
<?php

$query = "SELECT * FROM pay WHERE member_id = {$_SESSION['member_id']} AND pay_type='Check'";
$result = mysqli_query($db, $query) or die($query . mysqli_error($db));
$check_row = mysqli_fetch_assoc($result);

$query = "SELECT * FROM pay WHERE member_id={$_SESSION['member_id']} AND pay_type='ACH'";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$ach_row = mysqli_fetch_assoc($result);

$query = "SELECT * FROM pay WHERE member_id={$_SESSION['member_id']} AND pay_type='Wire'";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$wire_row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
	$pay_by = $_POST['pay_by'];
	$pay_address = trim($_POST['pay_address']);
	$pay_city = trim($_POST['pay_city']);
	$pay_state = trim($_POST['pay_state']);
	$pay_zip = trim($_POST['pay_zip']);
	$pay_name = trim($_POST['pay_name']);
	$pay_country = $_POST['pay_country'];
	$pay_out_min = $_POST['pay_out_min'];
	
	if($_POST['pay_by']=='ACH'){
		if($check_row['pay_type']!='ACH'){
			$error[] ='Invalid: Please <a href="/my-account/my-ach.php">add your Direct Deposit details</a> before selecting as your payment method.';
		}
	}elseif($pay_by=='Wire'){
		if($wire_row['pay_type']!='Wire'){
			$error[] ='Invalid: Please <a href="/my-account/my-wires.php">add your Wire details</a> before selecting as your payment method.';
		}
	}elseif ($pay_by=="Paypal") {
	  if(!$email_masspay) { $error[] = "Missing: Your Paypal Email Address (if you select to be paid by Paypal)"; }
	  elseif(!ereg("@",$email_masspay)) { $error[] = "Invalid: Your Paypal Email Address"; }
	  elseif(!ereg("\.",$email_masspay)) { $error[] = "Invalid: Your Paypal Email Address"; }
	}
	if(!$pay_name) { $error[] = "Missing: Your Payment Name"; }
	if(!$pay_address) { $error[] = "Missing: Your Mailing Address"; }
	if(!$pay_city) { $error[] = "Missing: Your Mailing City"; }
	if(!$pay_state) { $error[] = "Missing: Your Mailing State"; }
	if(!$pay_zip) { $error[] = "Missing: Your  Mailing Zip"; }
	if(!$pay_country) { $error[] = "Missing: Your Mailing Country"; }

	if (empty($error)) {
		$update = $insert = false;
		if ($check_row) {
			$sql_type = "UPDATE";
			$update = true;
			$sql_end = "WHERE member_id={$_SESSION['member_id']} AND pay_type='Wire'";
		} else {
			$sql_type = "INSERT INTO";
			$insert = true;
			$sql_end = ", member_id={$_SESSION['member_id']}";
		}
		$query = "$sql_type pay SET
			".(($insert)?"pay_type		='Check',":'')."
			pay_name		='$pay_name',
			pay_address		='$pay_address',
			pay_city		='$pay_city',
			pay_state		='$pay_state',
			pay_zip			='$pay_zip',
			pay_country		='$pay_country',
			pay_date		=now()
			$sql_end";
		 $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
		 $msg[] = "SUCCESS: Your changes are saved.";			

		 $query = "UPDATE members SET pay_by='$pay_by', pay_out_min='$pay_out_min'					
					WHERE member_id={$_SESSION['member_id']}";
		 $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");		
	} else {
	  $mrow['pay_by'] = $pay_by;
	  $mrow['email_masspay'] = $email_masspay;
	  $mrow['pay_name'] = stripslashes($pay_name);
	  $mrow['pay_address'] = stripslashes($pay_address);
	  $mrow['pay_city'] = stripslashes($pay_city);
	  $mrow['pay_state'] = stripslashes($pay_state);
	  $mrow['pay_zip'] = $pay_zip;
	  $mrow['pay_country'] = stripslashes($pay_country);
	  $mrow['sub_offset'] = $sub_offset;
	}
}

?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<form method="post">
        <input type="hidden" name="event" value="update-payment" />
        <input type="hidden" name="elite" value='<?=$elite?>'>
<h3>Pay My Commissions</h3>
<br>
			<table border="0" cellpadding="4" cellspacing="0">
            <tr>
              <td align="right" width="200px">Pay My Commissions Via:</td>
              <td valign='top'>
              <select name='pay_by'>
              <option value="Check" <? if ($mrow['pay_by']=='Check') echo 'SELECTED' ?>>Check</option>
<? if (0 && !in_array($mrow['pay_country'], array('US',''))) { ?>
              <option value="Paypal" <? if ($mrow['pay_by']=='Paypal') echo 'SELECTED' ?>>Paypal</option>
<? } ?>
			  <option value="ACH" <? if ($mrow['pay_by']=='ACH') echo 'SELECTED' ?>>Direct Deposit</option>
             <option value="Wire" <? if ($mrow['pay_by']=='Wire') echo 'SELECTED' ?>>Wire</option>

              </select>&nbsp; <i>Add/edit details at the bottom of this page</i>
              </td>
            </tr>
            <tr>
              <td align="right"><nobr>Minimum Payout :</nobr></td>
              <td>$
                <select name='pay_out_min'>
				<?=WriteSelect($mrow['pay_out_min'], array(100, 250, 500, 1000, 2500, 5000, 1000000), true, false)?>
              </select></td>
            </tr>
<?php /*            <tr>
              <td align="right" valign="top">Subscription Offsets:</td>
              <td>
			  <input type="checkbox" name="sub_offset" value="1" <?=($mrow['sub_offset']=="1")?"Checked":""?> />
			  Yes.
                If  large enough, apply my Earnings Balance to my monthly membership payment instead of using Account&gt;<a href="/shop/bizcards.php">My Payment Card(s)</a>.</td>
            </tr>
*/ ?> 
			<tr>
              <td align="right">&nbsp;</td>
              <td align="left"><i>&nbsp;<?=WriteButton("Save Changes")?>
              </i></td>
            </tr>
            <tr>
              <td colspan="2" align="center">&nbsp;</td>
            </tr>
            </table>

<script>
$(function() {
	$( "#tabs" ).tabs();
});
</script>
<br>
<div id="tabs">
	<ul>
		<li><a href="#tabs-check">Checks</a></li>
<? if (0 && !in_array($check_row['pay_country'], array('US',''))) { ?>
		<li><a href="#tabs-paypal">Paypal</a></li>
<? } ?>
		<li><a href="#tabs-ach">Direct Deposits</a></li>    
		<li><a href="#tabs-wire">Wires</a></li>    
	</ul>

<div id="tabs-check">
<h4>Check Payment ($3 fee per payment)</h4>
<table width="100%" border="0" cellpadding="4" cellspacing="0">
           <tr>
              <td align="right" width="200px">Make Earnings Checks Out To:</td>
              <td align="left">
                <i><input type="text" name="pay_name" size="20" value="<?=$check_row['pay_name'];?>" />&nbsp; eg, John Smith or Better Ventures LLC.</i></td>
            </tr>
            <tr>
              	<td align="right"> Mailing Address:</td>
        		<td align="left"><i><input type="TEXT" name="pay_address" size="20" value="<?=$check_row['pay_address'];?>" />&nbsp; eg, 69 Waller St</i></td>
      		</tr>
            <tr>
              <td align="right">Mailing City:</td>
              <td align="left">
                <i><input type="text" name="pay_city" size="20" value="<?=$check_row['pay_city'];?>" />&nbsp; eg, San Diego</i></td>
            </tr>
            <tr>
              <td align="right">Mailing Country:</td>
              <td align="left"><?=WriteFieldCountry($check_row['pay_country'], "pay_country")?></td>
            </tr>
            <tr>
              <td align="right">Mailing State:</td>
              <td align="left"><?=WriteFieldState($check_row['pay_state'], "pay_state")?></td>
            </tr>
            <tr>
              <td align="right">Mailing Zip:</td>
              <td align="left">
                <i><input type="text" name="pay_zip" size="20" value="<?=$check_row['pay_zip'];?>" />&nbsp; eg, 80302 </i></td>
            </tr>
            <tr>
              <td align="right">&nbsp;</td>
              <td align="left"><i>&nbsp;<?=WriteButton("Save Changes")?>
              </i></td>
            </tr>
			</table>
</div>

<? if (0 && !in_array($check_row['pay_country'], array('US',''))) { ?>
<div id="tabs-paypal">
<h4>PayPal Payment Details (2% fee up to $20 per payment)</h4>
<p>Fee Policy Update: On Jan 24, 2012 PayPal changed their fee structure for sending money  (see <a href="https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&amp;content_ID=ua/upcoming_policies_full&amp;locale.x=en_US" target="_blank">PayPal Policy Update</a>). Their new fees are 2% of the amount up to a maximum of $20 per transaction. These fees will be taken from the amount we send you to cover our costs. To reduce percentage of fees you incur, you can set your minimum payment amount above to be greater than $1000.</p>
<table width="100%" border="0" cellpadding="4" cellspacing="0">
            <tr>
              <td align="right" width="200px">My <a href="http://paypal.com" target="_blank">PayPal</a> email is:</td>
              <td valign='top'><input type="text" name="email_masspay" size="30" value="<?=$mrow['email_masspay'];?>" /></td>
            </tr>
            <tr>
              <td align="right">&nbsp;</td>
              <td align="left"><?=WriteButton("Save Changes")?></td>
            </tr>
</table>
</div>
<? } ?>

<div id="tabs-ach">
<h4>US Bank Direct Deposits ($3 fee per payment)</h4>

<table width="100%" border="0" cellpadding="4" cellspacing="0">
<? if(isset($ach_row)){?>
	<tr>
		<td align="right" width="200">Beneficiary/Account Name:</td>
		<td align="left"><?=$ach_row['pay_name']?></td>
	</tr>
	<tr>
		<td align="right" valign="top">Beneficiary Street Address:</td>
		<td align="left"><?=$ach_row['pay_address']?><br><?=$ach_row['pay_city']?>, <?=$ach_row['pay_state']?> <?=$ach_row['pay_zip']?><br><?=$ach_row['pay_country']?></td>
	</tr>
	<tr>
		<td align="right">Account Type:</td>
		<td align="left"><?=$ach_row['pay_type']?></td>
	</tr>
	<tr>
		<td align="right">Account Class:</td>
		<td align="left"><?=$ach_row['pay_class']=="Corporate" ? "Business" : $ach_row['pay_class']?></td>
	</tr>
	<tr>
		<td align="right">Bank Name:</td>
		<td align="left"><?=$ach_row['pay_bank_name']?></td>
	</tr>
	<tr>
		<td align="right">Routing Number:</td>
		<td align="left"><?=$ach_row['pay_routing']?></td>
	</tr>
	<tr>
	  <td align="right">Account Number:</td>
	  <td align="left"><?=WriteCardNum($ach_row['pay_account'])?></td>
	  </tr>

	<tr>
	  <td align="right">&nbsp;</td>
	  <td align="left">[ <a href="/my-account/my-ach.php">Edit your Direct Deposit Details</a> ]</td>
	  </tr>
<? } else { ?>
	<tr>
	  <td colspan="2" align="left"><a href="/my-account/my-ach.php">+ Add your Direct Deposit Details</a></td>
	  </tr>
<? } ?>
</table>

</div>


<div id="tabs-wire">
<h4>International Wire Transfers ($35 fee per payment)</h4>

<table width="100%" border="0" cellpadding="4" cellspacing="0">
<? if($wire_row){?>
	<tr>
		<td align="right" width="200">Beneficiary/Account Name::</td>
		<td align="left"><?=$wire_row['pay_name']?></td>
	</tr>
	<tr>
		<td align="right" valign="top">Beneficiary Street Address:</td>
		<td align="left"><?=$wire_row['pay_address']?><br><?=$wire_row['pay_city']?>, <?=$wire_row['pay_state']?> <?=$wire_row['pay_zip']?><br><?=$wire_row['pay_country']?></td>
	</tr>
	<tr>
		<td align="right">Bank Name:</td>
		<td align="left"><?=$wire_row['pay_bank_name']?></td>
	</tr>
	<tr>
		<td align="right">SWIFT Code:</td>
		<td align="left"><?=$wire_row['pay_routing']?></td>
	</tr>
	<tr>
	  <td align="right">Account Number:</td>
	  <td align="left"><?=WriteCardNum(($wire_row['pay_account'])?$wire_row['pay_account']:$wire_row['pay_iban'])?></td>
	  </tr>

	<tr>
	  <td align="right">&nbsp;</td>
	  <td align="left">[ <a href="/my-account/my-wires.php">Edit your Wire Details</a> ]</td>
	  </tr>
<? } else { ?>
	<tr>
	  <td colspan="2" align="left"><a href="/my-account/my-wires.php">+ Add your Wire Details</a></td>
	  </tr>
<? } ?>
</table>
</div>


</div>


</form>
</div>

<?php include(INCLUDES_MY."footer.php"); ?>

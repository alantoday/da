<?php include("../includes_my/header.php"); ?>
<?php include(PATH."includes/functions_gr.php"); ?>
<?php
$mrow = GetRowMember($db, $_SESSION['member_id']);

if (isset($_POST['submit']) && $_POST['submit'] == "Save") {
	$gr_api_key = trim($_POST['gr_api_key']);
	
	# Let them enter in blank Key (if they already have one)
	if (empty($mrow['gr_api_key']) || !empty($gr_api_key)) {
		if (empty($gr_api_key)) $error[] = "MISSING: Please enter Your GetResponse API Key";
		// Test for missing data
		elseif (!GRValidAPIKey($gr_api_key)) {
			$error[] = "INVALID: Please enter a valid GetResponse API Key";
		}
	}
	if(empty($error)) {
		$query = "UPDATE members 
					SET gr_api_key='$gr_api_key' 
					WHERE member_id={$_SESSION['member_id']}";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$msg[] = "SUCCESS: Your API Key is saved";
		$mrow['gr_api_key'] = $gr_api_key;
	}
} elseif (isset($_POST['submit']) && $_POST['submit'] == "Load") {
	if ($_POST['from_campaign_id']=="-1") {
		$error[] = "";
	} else {
		list ($success, $message) = GRLoadEmails($mrow['member_id'], GETRESPONSE_CAMPAIGN_C1, $mrow['gr_api_key'], $_POST['from_campaign_id'], $_POST['to_campaign_id']);
		if ($success) {
			$msg[] = $message;
		} else {
			$error[] = $message;			
		}
	}
#	EchoLn("From: ".$_POST['from_campaign_id']);
#	EchoLn("To: ".$_POST['to_campaign_id']);
} else {
	$_POST['gr_api_key'] = $mrow['gr_api_key'];
}

if (!empty($mrow['gr_api_key'])) {
	$campaigns = GRGetCampaigns($mrow['gr_api_key']);
	$to_campaign_options[] = array("-1" => " - Select -");
	$to_campaign_options[] = array("0" => " - Create New Campaign -");
	foreach ($campaigns as $campaign_id => $details) {
		$to_campaign_options[] = array($campaign_id => $details['name']);
	}
} else {
	$to_campaign_options[] = " - Save Your API Key First -";	
}
$from_campaign_options[] = " - Select -";	
$from_campaign_options[] = array("ASPIRE Sale Funnel Emails", GETRESPONSE_CAMPAIGN_C1);	


?>
<?php echo MyWriteMidSection("MY AUTORESPONDER", "Manage Your Autoresponder Here",
	"Enter your personal Get Response API Key to integrate into the system",
	"MY CAMPAIGNS","/my-marketing/my-campaigns.php",
	"MY LINKS", "/my-marketing/my-links.php"); ?>
<?php include("my-marketing_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<div style="float:right"><img width="250px" src="https://s3.amazonaws.com/public.digitalaltitude.co/images/icons/getresponse.png"></div>
<h4>Step 1. Sign up for GetResponse</h4>
        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td width='300px' align="right">If you don't already have an account:</td>
            <td align="left">
              <a target="_blank" class="btn" href="http://www.getresponse.com/index/5start">Signup for GetResponse >>></a>
            </td>
          </tr>
</table>
<br>
<hr>
<h4>Step 2. Link my GetResponse account </h4>
        <form method="post">
        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td width='300px' align="right"> Enter Your GetResponse API Key:</td>
            <td align="left">
              <input type="text" name="gr_api_key" value="<?php echo $_POST['gr_api_key']; ?>" style="width:230px" /> 
              		&nbsp; <a target="blank" href='https://support.getresponse.com/faq/where-i-find-api-key'>Where can I find this?</a></td>
          </tr>
          <tr>
        	<td align="right"></td>
    	    <td align="left">
          	<?=WriteButton("Save")?>
	      </tr>
        </table>
</form>
<br>
<hr>
<h4>Step 3. Load Your GetResponse Campaign With Our Pre-Written Emails</h4>
        <form method="post">
        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td width='300px' align="right"> Select Your GetResponse Campaign:</td>
            <td><select name="to_campaign_id" <?php if (!empty($_POST['inf_invoice_id'])) echo "DISABLED"; ?>>
              <?php echo WriteSelect($_POST['to_campaign_id'], $to_campaign_options, false, false); ?>
            </select></td>
          </tr>
          <tr>
            <td width='300px' align="right"> Select Our Pre-Written Emails:</td>
            <td><select name="from_campaign_id" <?php if (!empty($_POST['inf_invoice_id'])) echo "DISABLED"; ?>>
              <?php echo WriteSelect($_POST['from_campaign_id'], $from_campaign_options, false, false); ?>
            </select></td>
          </tr>
          <tr>
        	<td align="right"></td>
    	    <td align="left">
          	<?=WriteButton("Load")?>
	      </tr>
        </table>
</form>

<?php include(INCLUDES_MY."footer.php"); ?>

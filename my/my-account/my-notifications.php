<?php include("../includes_my/header.php"); ?>
<?php include(PATH."/includes/functions_inf.php"); ?>
<?php
if (isset($_POST['submit'])) {
	$phone_cell = trim($_POST['phone_cell']);
	
	# Validate phone number formats
	// Make sure it contains exactly 10 numbers one separators are stripped
	if ($_POST['phone_cell_country']=="US" && $phone_cell<>"" && !preg_match("/^[2-9]{1}[0-9]{9}$/",preg_replace("/[\.\-() ]/", "", $phone_cell))) {
	  $error[] = "Invalid: Your US Cell Phone Number should have format: ###-###-####<br>and can't begin with a '1' or '0'.<br>";
	}
	// Canada
	elseif ($_POST['phone_cell_country']=="CA" && $phone_cell<>"" && !preg_match("/^[2-9]{1}[0-9]{9}$/",preg_replace("/[\.\-() ]/", "", $phone_cell))) {
	  $error[] = "Invalid: Your Canada Cell Phone Number should have format: ###-###-####<br>and can't begin with a '1' or '0'.<br>";
	}
	// Australian
	elseif ($_POST['phone_cell_country']=="AU" && $phone_cell<>"" && !preg_match("/^[4]{1}[0-9]{8}$/",preg_replace("/[\.\-() ]/", "", $phone_cell))) {
	  $error[] = "Invalid: Your Australian Mobile Phone Number should have format: ###-###-###<br>and should start with 4 (do NOT include the leading '0').<br>";
	}
	// New Zealand
	elseif ($_POST['phone_cell_country']=="NZ" && $phone_cell<>"" && !preg_match("/^[2]{1}[0-9]{7,9}$/",preg_replace("/[\.\-() ]/", "", $phone_cell))) {
	  $error[] = "Invalid: Your New Zealand Cell Phone Number should have format: #-###-####<br>and must begin with '2' (do NOT include the leading '0').<br>";
	}
	// UK
	elseif ($_POST['phone_cell_country']=="UK" && $phone_cell<>"" && !preg_match("/^[7]{1}[0-9]{9}$/",preg_replace("/[\.\-() ]/", "", $phone_cell))) {
	  $error[] = "Invalid: Your UK Cell Phone Number should have format: ##-####-####<br>and must begin with a '7' (do NOT include the leading '0').<br>";
	}
	if ($_POST['phone_cell_country']=="" && $phone_cell<>"") {
	  $error[] = "Missing: Please enter a country prefix for your Cell/Mobile Phone.<br>";
	}

	if (empty($error)) {
		// Test if they already have a member details record or no
		$query = "SELECT member_id
					FROM member_details
					WHERE member_id	='{$_SESSION['member_id']}'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$set_sql_md = "sms_members='".isset($_POST['sms_members'])."'
					, sms_comms='".isset($_POST['sms_comms'])."'
					, sms_comms_min='".isset($_POST['sms_comms_min'])."'
					, notify_leads='".isset($_POST['notify_leads'])."'
					, notify_members='".isset($_POST['notify_members'])."'
					, notify_comms='".isset($_POST['notify_comms'])."'
					, notify_comms_min='".isset($_POST['notify_comms_min'])."'";
		$set_sql_m = "phone_cell='$phone_cell'
					, phone_cell_country='{$_POST['phone_cell_country']}'";
		if ($row = mysqli_fetch_assoc($result)) {
			$query = "UPDATE member_details md
						JOIN members m USING (member_id)
						SET $set_sql_md
						, $set_sql_m
						WHERE member_id	='{$_SESSION['member_id']}'";
		} else {
			$query = "UPDATE members
						SET $set_sql_m
						WHERE member_id	='{$_SESSION['member_id']}'";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			$query = "INSERT INTO member_details
						SET $set_sql_md
					   , member_id	='{$_SESSION['member_id']}'";
		}
		#EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
					
		$msg[] = "Your Notification Setttings are updated.";
		$mrow = GetRowMemberDetails($db, $_SESSION['member_id']);	
	}
} else {
	$mrow = GetRowMemberDetails($db, $_SESSION['member_id']);	
	$_POST['phone_cell'] = $mrow['phone_cell'];
	$_POST['phone_cell_country'] = $mrow['phone_cell_country'];	

	$_POST['notify_leads'] = $mrow['notify_leads'];	
	$_POST['notify_members'] = $mrow['notify_members'];	
	$_POST['notify_comms'] = $mrow['notify_comms'];	

	$_POST['sms_members'] = $mrow['sms_members'];	
	$_POST['sms_comms'] = $mrow['sms_comms'];	
}
$sms_min_amt_options[1] = array(".1"=>"10 cents");
$sms_min_amt_options[] = array(".25"=>"25 cents");
$sms_min_amt_options[] = array(".5"=>"50 cents");
$sms_min_amt_options[] = array("1"=>"$1");
$sms_min_amt_options[] = array("5"=>"$5");
$sms_min_amt_options[] = array("10"=>"$10");
$sms_min_amt_options[] = array("50"=>"$50");
$sms_min_amt_options[] = array("100"=>"$100");
$sms_min_amt_options[] = array("500"=>"$500");
$sms_min_amt_options[] = array("1000"=>"$1,000");

$notify_min_amt_options = array_merge(array(".01"=>"1 cent"),$sms_min_amt_options);

?>
<?php echo MyWriteMidSection("MY NOTIFICATIONS", "Set Your Email and Text Message Preferences Here",
	"Review and manage your notifications settings",
	"MY PROFILE","/my-business/my-profile.php",
	"MY STATUS", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<?php echo isset($_GET['msg']) ? "<h4><b><font color=green>{$_GET['msg']}</font></b></h4><br>" : "";?>
<div style="float:"><font color=red><b>NOTICE: You can adjust your settings today, but please be patient, fully operational notifications are still under construction.</b></font></div><br>
<h4>Yes, Email Me...</h4>
<form method="POST">
    <br><input type="checkbox" name="notify_leads" value="1" <?=(isset($_POST['notify_leads']) && $_POST['notify_leads']==1) ? "Checked" : ""?> />
    Yes, for each new Lead
    <br><input type="checkbox" name="notify_members" value="1" <?=(isset($_POST['notify_members']) && $_POST['notify_members']==1) ? "Checked" : ""?> />
    Yes, for each new Member
    <br><input type="checkbox" name="notify_comms" value="1" <?=(Isset($_POST['notify_comms']) && $_POST['notify_comms']==1) ? "Checked" : ""?> />
    Yes, for each new Commission over
        <select name="notify_comms_min" style="display:inline; width:90px">
            <?php echo WriteSelect(isset($_POST['notify_comms_min']) ? $_POST['notify_comms_min'] : "", $notify_min_amt_options, false, false); ?>
        </select>
    <br><br>
    <i>Add support@digitalaltitude.co to your address book so the notifications won't go to spam.</i>
    <br><br>

    <br>
    <h4>Yes, SMS/Text Message Me...</h4>
    <br>
    <input type="checkbox" name="sms_members" value="1" <?=(isset($_POST['sms_members']) && $_POST['sms_members']==1) ? "Checked" : ""?> />
    Yes, for each new Member
    <br />
    <input type="checkbox" name="sms_comms" value="1" <?=(isset($_POST['sms_comms']) && $_POST['sms_comms']==1) ? "Checked" : ""?> />
    Yes, for each new Commission over
        <select name="sms_comms_min" style="display:inline; width:90px">
            <?php echo WriteSelect(isset($_POST['sms_comms_min']) ? $_POST['sms_comms_min'] : "", $sms_min_amt_options, false, false); ?>
        </select>
    <br>
    <table>
    <tr>
      <td align="">Cell/Mobile Phone:</td>
      <td align="left"><?
// Get prefix based on country
$query = "SELECT country, country_abbr, prefix 
			FROM countries 
			WHERE prefix<>'' 
			ORDER BY country";
$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);


$phone_prefix_list = "<option value=''>- Select Country Dialing Prefix -</option>\n";
// Put populare ones up top
$phone_prefix_list .= "<option value='US'>+1 United States</option>\n";
$phone_prefix_list .= "<option value='CA'>+1 Canada</option>\n";
$phone_prefix_list .= "<option value=''>---------------</option>\n";
while ($country_row = mysqli_fetch_assoc($result)) {
	$select = ($_POST['phone_cell_country']==$country_row['country_abbr']) ? " SELECTED" : "";
	$phone_prefix_list .= "<option$select value='{$country_row['country_abbr']}'>{$country_row['prefix']} {$country_row['country']}</option>\n";
}
?>
        <select name="phone_cell_country" style="width:100px">
          <?=$phone_prefix_list?>
        </select>
        </td>
        <td align="left"><input type="text" name="phone_cell" maxlength="14" value="<?=$_POST['phone_cell'];?>" placeholder="Cell Phone Number" />
		</td>
    </tr>
    </table>
    <p><?=WriteButton("Save Changes")?></p>
</form>

<?php include(INCLUDES_MY."footer.php"); ?>

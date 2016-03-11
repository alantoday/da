<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../../includes/functions_inf.php");
include_once("../../includes/functions_email.php");
$member_details = 1;
include_once("../includes_admin/include_menu.php");
?>
<?php
if(empty($_GET['member_id'])){
	echo "Missing: member_id";
	exit();
}
$member_row = GetRowMemberCoaches($db, $_GET['member_id']);
$sponsor_row = GetRowMember($db, $member_row['sponsor_id']);

# If they are coach - validate that they have access to view this member's record
if (in_array($mrow['admin_security_id'], array(ACCESS_COACH))) {
	if (DEBUG) EchoLn($mrow['member_id']);
#	if (DEBUG) EchoLn($member_row['coach_id_setup']);
	if (DEBUG) WriteArray($mrow);
	if (DEBUG) WriteArray($member_row);
	if (!in_array($mrow['member_id'], array($member_row['coach_id_startup'],$member_row['coach_id_setup'],$member_row['coach_id_scale'], $member_row['coach_id_traffic'], $member_row['coach_id_success']))) {
		echo "You do not have permission to access this member.";
		exit;
	}
}

$yes_no_options = array(
    array("0"=>"No")
    , array("1"=>"Yes")
	);	

# Get Progress Status dropdown options
$query = "SELECT *
			FROM progress_statuses
			WHERE active = 1";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$progress_status_options = array();
while ($row = mysqli_fetch_assoc($result)) {
	$progress_status_options[$row['progress_status_id']] = $row['progress_status'];
}

# Get Steps dropdown options
$query = "SELECT *
			FROM steps
			WHERE active = 1";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$step_options[] = array("0.0"=>" - Please select -");
while ($row = mysqli_fetch_assoc($result)) {
	if ($row['unlock_checkpoint']) {
		$star = " <-Checkpoint";
	} else {
		$star = "";		
	}
	$step_options[] = array($row['step_number'] => WriteStepType($row['step_type']). (!empty($row['step_type']) ? ": " : "") . $row['step_name'].$star);
}

if (isset($_POST['passwd']) && !$member_row['admin_security_id']) {
	if ($_POST['passwd']<>$member_row['passwd']) {
		$query = "UPDATE members 
					SET passwd = '{$_POST['passwd']}'
					WHERE member_id = '{$member_row['member_id']}'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		LogAdminChange($db, $_GET['member_id'], "members", $_GET['member_id'], "passwd", $member_row['passwd'], $_POST['passwd'], $_SESSION['member_id'], "member.php");
		$msg[] = "SUCCESS: Password Reset";
	} else {
		$error[] = "NO CHANGE: New password is same as the old password.";	
	}
}

if (isset($_POST['save_lock_note'])) {
	if (trim($_POST['note'])=="") {
		$error[] = "Progress note can not be blank.";
	}
	if (empty($error)) {	
		$progress_log = isset($_POST['coach']) ? "1" : "0";
		$query = "INSERT INTO member_notes 
					SET progress_status_id = '{$_POST['progress_status_id']}' 
					, note = '".addslashes($_POST['note'])."' 
					, member_id = '{$_GET['member_id']}'
					, author_id = '{$_SESSION['member_id']}'
					, step_unlocked = '{$_POST['step_unlocked']}'
					, create_date = NOW()
					";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$msg[] = "Progress Note Saved";
		
		// Save note to Infusionsoft too.
		# TODO: Add to functions.
		$contactAction = new Infusionsoft_ContactAction();
		$contactAction->ContactId = $member_row['inf_contact_id'];
#		$contactAction->UserID = $infusionSoftUser->Id;
		$contactAction->CompletionDate = date('Ymj\TG:i:s');
		$contactAction->ActionDescription = "From AD. Author ID: {$_SESSION['member_id']}";
		$contactAction->CreationNotes = $_POST['note'];
		$contactAction->save();

		# If lock updated save and log that change
		if ($_POST['step_unlocked']<>$member_row['step_unlocked']) {
			$query = "UPDATE members 
						SET step_unlocked = '{$_POST['step_unlocked']}' 
						WHERE member_id = '{$_GET['member_id']}'";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			$msg[] = "Next Unlocked Step Updated: '".get_step_name_by_step_id($step_options, $_POST['step_unlocked'])."'";
			
			# If they are being promoted from 1.X to 2.1
			if ($member_row['step_unlocked'] < 2 && $_POST['step_unlocked'] >= 2 && $_POST['step_unlocked'] < 3) {
				# Add INF Tag
				InfAddTag($member_row['inf_contact_id'], INF_TAG_STEP_2_1_UUNLOCKED);
				# Send Email/Text Notifications to Scale
				EmailNewMemberCoach($db, "S2", $member_row['$coach_id_setup'], $member_row['member_id']);
			}
		}
	}
}

if(!isset($_POST['step_unlocked'])){        
	$_POST['step_unlocked'] = $member_row['step_unlocked'];	
}

if (!isset($_POST['submit'])) {
	$_POST['name'] = $member_row['name'];
	$_POST['email'] = $member_row['email'];
	$_POST['sponsor_id'] = $member_row['sponsor_id'];
	$_POST['passwd'] = $member_row['passwd'];
	$_POST['coach_id_startup'] = $member_row['coach_id_startup'];
	$_POST['coach_id_setup'] = $member_row['coach_id_setup'];
	$_POST['coach_id_scale'] = $member_row['coach_id_scale'];
	$_POST['coach_id_traffic'] = $member_row['coach_id_traffic'];
	$_POST['coach_id_success'] = $member_row['coach_id_success'];
}

#################################################################################
if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>";
if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>";
?>
<style>
h2 {
	border-top:thin solid #CCC;	
	margin-top:30px;
}
</style>
<?php if (in_array($mrow['admin_security_id'], array(ACCESS_ADMIN, ACCESS_SUPERADMIN))) { ?>
<div style="float:right"><a href="<?php echo str_replace("member.php","member_update.php", $_SERVER['REQUEST_URI']);?>">Member Update</a></div>
<?php } ?>

<h1 id="page_title"><?php echo $member_row['name']?> (<?php echo $member_row['member_id']?>: <?php echo $member_row['username']?>)</h1>
<?php // End search Applications
##############################################################################################
##############################################################################################
?>

<table>
<tr><td valign='top'>
  <table>
    <tr>
        <td align="right">Joined:</td>
        <td><?php echo WriteDateTime($member_row['create_date'])?> (Tag: <?php echo $member_row["t"]<>'' ? $member_row["t"] : "<font color=grey>-</font>"?>)
        </b></td>
    </tr>
    <tr>
      <td align="right">Infusionsoft ID:</td>
      <td><?php echo $member_row['inf_contact_id']?>  (Aff ID: <?php echo $member_row["inf_aff_id"]?>)</td>
    </tr>
    <tr>
      <td align="right">Email:</td>
      <td><?php echo $member_row['email']?></td>
    </tr>
    <tr>
      <td align="right">Phone:</td>
      <td><?php echo $member_row["phone"]?></td>
    </tr>
    <tr>
      <td align="right">Address:</td>
      <td><?php echo $member_row['address'].", ".$member_row['city'].", ".$member_row['state']." ".$member_row['zip']?></td>
    </tr>
    <tr>
      <td align="right">Country:</td>
      <td><?php echo $member_row['country']?></td>
    </tr>
    <tr>
      <td align="right">Sponsor:</td>
      <td><?php echo $sponsor_row['name']?>  (<?php echo $sponsor_row['member_id']?>: <?php echo $sponsor_row['username']?>)</td>
    </tr>
  </table>
</td>
<td valign='top'>
<form action="member.php?member_id=<?php echo $_GET['member_id']; ?>" method="POST">
  <table>
    <tr>
      <td align="right">Welcome Coach:</td>
      <td><?php echo $member_row['coach_name_setup']?> (<?php echo $member_row['coach_id_setup']?>: <?php echo $member_row['coach_username_setup']?>)</td>
    </tr>
    <tr>
      <td align="right">S1 Coach:</td>
      <td><?php echo $member_row['coach_name_startup']?> (<?php echo $member_row['coach_id_startup']?>: <?php echo $member_row['coach_username_startup']?>)</td>
    </tr>
    <tr>
      <td align="right">S2 Coach:</td>
      <td><?php echo $member_row['coach_name_scale']?> (<?php echo $member_row['coach_id_scale']?>: <?php echo $member_row['coach_username_scale']?>)</td>
    </tr>
    <tr>
      <td align="right">Traffic Coach:</td>
      <td><?php echo $member_row['coach_name_traffic']?> (<?php echo $member_row['coach_id_traffic']?>: <?php echo $member_row['coach_username_traffic']?>)</td>
    </tr>
    <tr>
      <td align="right">Success Coach:</td>
      <td><?php echo $member_row['coach_name_success']?> (<?php echo $member_row['coach_id_success']?>: <?php echo $member_row['coach_username_success']?>)</td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td align="right">New Password:</td>
      <td><input type="text" name="passwd" placeholder="Leave blank if unchanged" value="" /></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
      <td><?php echo WriteButton("Password Reset");?></td>
    </tr>
  </table>
</form>
</td></tr>
</table>

<h2>Send Member An Email</h2>
<?php // <p><font color=red>Please let amoore@digitalaltitude.co know if you'd like the wording in the emails adjusted.</font></p> ?>
<?php
$to = $member_row['email'];
$member_firstname = WriteFirstName($member_row['name']);
$subject_message = "I just left you a message $member_firstname";
$subject_message = "I just left you a message $member_firstname";
$subject_busy = "I just called you $member_firstname";
$subject_unavail = "I just called you $member_firstname";
$subject_invalid = "I just called you $member_firstname";
$subject_time = "Hi $member_firstname - I am looking at your Application";
$subject_spoken = "Nice Speaking with You $member_firstname";

$book_call = "";
if (trim($mrow['book_call']) <> "") {
	$book_call = "
You can book a call with me here:
".urlencode($mrow['book_call']);
}
$special_footer = "";
if ($mrow['member_id']=='234') { // Special Link for Ash
	$special_footer="Daily Calls: http://www.blogtalkradio.com/digitalaltitude";
}


$body = "Hi $member_firstname,

#MSG#

Be sure to and review all the startup steps text, 
videos and worksheets.

http://my.aspiresystem.co

I look forward to speaking with you again soon
and helping you though discovering the power of
this system.
$book_call

Best regards,

{$mrow['name']}
Phone: {$mrow['phone']}
Skype: {$mrow['skype']}
$special_footer
";
$body_spoken = "Hi $member_firstname,

It was nice speaking with you earlier about getting
setup right with your new ASPIRE System.

Just to re-cap, be sure to sign in at

http://my.aspiresystem.co

... and review all the startup steps text, videos
and worksheets.

I look forward to speaking with you again soon
and helping you though discovering the power of
this system.
$book_call

Best regards,

{$mrow['name']}
Phone: {$mrow['phone']}
Skype: {$mrow['skype']}
$special_footer
";

$body = str_replace("
", "%0A", $body);
$body_spoken = str_replace("
", "%0A", $body_spoken);

$mailto_message = "$to?subject=$subject_message&body=$body";
$mailto_busy = "$to?subject=$subject_busy&body=$body";
$mailto_unavail = "$to?subject=$subject_unavail&body=$body";
$mailto_invalid = "$to?subject=$subject_invalid&body=$body";
$mailto_time = "$to?subject=$subject_time&body=$body";
$mailto_spoken = "$to?subject=$subject_spoken&body=$body_spoken";

$mailto_message = str_replace("#MSG#", "I just left a message for you about getting%0Asetup right with your new ASPIRE System.", $mailto_message);
$mailto_busy = str_replace("#MSG#", "I've been attempting to contact you, however,%0Ayour telephone has been busy.", $mailto_busy);
$mailto_unavail = str_replace("#MSG#", "I've been attempting to contact you using %0Athe details in your ASPIRE System account, %0Ahowever, I've been unable to reach you.", $mailto_unavail);
$mailto_invalid = str_replace("#MSG#", "I've been attempting to contact you using %0Athe details in your ASPIRE System account, %0Ahowever, the number does not seems to be valid for you.", $mailto_invalid);
$mailto_time =  str_replace("#MSG#", "It's kind of late to be calling you, since we live%0Ain different parts of the world but I did want to%0Atouch base with you quickly.", $mailto_time);
?>

<p><a href="mailto:<?=$mailto_message?>" target='_blank'>Left Msg</a> 
  - <a href="mailto:<?=$mailto_busy?>" target='_blank'>Busy</a> 
  - <a href="mailto:<?=$mailto_unavail?>" target='_blank' >Unavailable</a> 
  - <a href="mailto:<?=$mailto_invalid?>" target='_blank' >Invalid</a> 
  - <a href="mailto:<?=$mailto_time?>" target='_blank'>It's Late</a> 
  - <a href="mailto:<?=$mailto_spoken?>" target='_blank'>Spoken</a> &nbsp; &nbsp;&lt;--
<i><a href="http://blog.hubspot.com/marketing/set-gmail-as-browser-default-email-client-ht" target="_blank">Link these to Gmail</a></i></span>
</p>

<h2>Progress Locks/Notes</h2>
<?php $table_head = "<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Date</td>
    <td>Author</td>
    <td>Status</td>
    <td>Locked Step</td>
    <td>Note</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
	
# Then get other Core products
$query = "SELECT a.name as author_name, ps.progress_status, s.step_type, s.step_name, mn.*
			FROM members a
			JOIN member_notes mn ON mn.author_id = a.member_id
			LEFT JOIN progress_statuses ps USING (progress_status_id)
			LEFT JOIN steps s ON s.step_number = mn.step_unlocked
			WHERE mn.member_id = {$_GET['member_id']}
			ORDER BY mn.create_date DESC";
#EchoLn($query);
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD(WriteDate($row ['create_date']))
   	.WriteTD($row['author_name'])
   	.WriteTD($row['progress_status'])
   	.WriteTD(WriteStepType($row['step_type']). (!empty($row['step_type']) ? ": " : "") . $row['step_name'])
   	."<td maxwidth='450px'>".$row['note']."</td>"
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot."<br>";
} else {
	echo "<center><i><font color=red>No progress notes yet.</font></i></center><br>";
}
?>
<form method="post">
<table border="0" cellspacing="0" cellpadding="2" class="">
    <tr>
        <td align=right>Last Step Completed:</td>
        <td><b><?php echo WriteStepNumber($member_row['steps_completed']); ?></b> of 18</td>
    </tr>
    <tr>
        <td align=right>Next Unlocked Step:</td>
        <td><select name="step_unlocked"><?php echo WriteSelect($_POST['step_unlocked'], $step_options, false, false); ?></select></td>
    </tr>
    <tr>
        <td align=right>Progress Status:</td>
        <td><select name="progress_status_id"><?php echo WriteSelect(isset($_POST['progress_status_id']) ? $_POST['progress_status_id'] : "", $progress_status_options, false, false)?></select></td>
    </tr>
    <tr>
        <td align=right>Progress Note:</td>
        <td><input type='text' name='note' size='75' value='<?php echo isset($_POST['notes']) ? $_POST['notes'] : "" ?>' /> &nbsp; <?php echo WriteButton("Save Lock/Note","save_lock_note");?></td>
    </tr>
</table>
</form>



<h2>Product/Rank History</h2>
<?php $table_head = "<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Product/Rank</td>
    <td>Status</td>
    <td>Start Date</td>
    <td>End Date</td>
    <td>Notes</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
	
# Then get other Core products
$query = "SELECT p.product_name, mr.*
			FROM member_ranks mr
			JOIN inf_products p USING(product_type)
			WHERE  mr.member_id={$_GET['member_id']}
			ORDER by p.product_order";
#EchoLn($query);
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $prow = mysqli_fetch_assoc($result); $i++){
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD($prow['product_name'])
   	.WriteTD("<font color=green>ACTIVE</font>")
   	.WriteTD(WriteDate($prow['start_date']))
   	.WriteTD(WriteDate($prow['end_date']))
   	.WriteTD($prow['notes'])
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	echo "<font color=red>No ranks at this time.</font>";
}
?>

<h2>Login History - Last 5</h2>
<?php $table_head = "<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Date</td>
    <td>IP</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
	
# Then get other Core products
$query = "SELECT *
			FROM member_logins
			WHERE member_id = {$_GET['member_id']}
			ORDER BY create_date DESC
			LIMIT 5";
#EchoLn($query);
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD(WriteDate($row['create_date']))
   	.WriteTD($row['ip'])
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	echo "<font color=red>Never logged in.</font>";
	echo "<br><font color=red>Initial Password: {$member_row['passwd']}</font>";
}
?>


<?php $table_head = "<h2>Sales Assist History</h2>
<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Sales Assist</td>
    <td>Start Date</td>
    <td>End Date</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
$query = "SELECT *
			FROM member_log_sa
			WHERE member_id='{$_GET['member_id']}' 
			ORDER BY start_date DESC";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	if ($row['sa_pc']) {
		$sales_assist = "<font color=green>Yes, ".WriteNum($row['sa_pc'])."%</font>";
	} else {
		$sales_assist = "<font color=red>No</font>";
	}
	$sales_assit =
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD($sales_assist)
   	.WriteTD(WriteDate($row['start_date']))
   	.WriteTD(WriteDate($row['end_date']))
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	// Nothing. echo "<font color=green>Yes, 20% by default.</font><br>";
}
?>

<?php $table_head = "<h2>Alternative Emails</h2>
	<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Date</td>
    <td>Email</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
$query = "SELECT *
			FROM member_emails
			WHERE member_id='{$_GET['member_id']}' 
			ORDER BY create_date DESC";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD(WriteDate($row['create_date']))
   	.WriteTD($row['alternate_email'])
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	// Nothing;
}
?>



<?php $table_head = "<h2>Admin Change Log</h2>
<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Date</td>
    <td>Table</td>
    <td>Record ID</td>
    <td>Field</td>
    <td>Old Value</td>
    <td>New Value</td>
    <td>Changed By</td>
    <td>Location</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
$query = "SELECT a.name as admin_name, c.* 
			FROM change_log c 
			LEFT JOIN admin a ON a.admin_id=changer_admin_id 
			WHERE member_id='{$_GET['member_id']}' 
			ORDER BY create_date DESC";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	if ($row['item_field']=="passwd") {
		$row['old_value'] = 'hidden';
		$row['new_value'] = 'hidden';
	}
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD(WriteDate($row['create_date']))
   	.WriteTD($row['item_table'])
   	.WriteTD($row['item_id'])
   	.WriteTD($row['item_field'])
   	.WriteTD($row['old_value'])
   	.WriteTD($row['new_value'])
   	.WriteTD($row['admin_name'])
   	.WriteTD($row['change_location'])
	."</tr>";
 }
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	// echo "<font color=grey>No changes logged.</font><br>";
}

?>

<?php //include("../include_footer.php"); ?>
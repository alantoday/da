<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../includes_admin/include_menu.php");
?>
<?php $PAGE['width'] = 750; ?>
<?php $PAGE['permissions'] = array("sample"); ?>
<?php $PAGE['compress'] = false; ?>
<?php

if (isset($_POST['member_id'])) {
	$member_id = $_POST['member_id'];
} elseif(isset($_GET['member_id'])) {
	$member_id = $_GET['member_id'];
}

if (isset($_POST['sponsor_id'])) {
	$sponsor_id = $_POST['sponsor_id'];
}
if(empty($member_id)){
	echo "Missing: member_id";
	exit();
}
$row = GetRowMember($db, $member_id);
$sponsor_row = GetRowMember($db, $row['sponsor_id']);

if (isset($_POST['submit'])) {
	$query = "UPDATE members SET 
				sponsor_id='$sponsor_id'
				WHERE member_id='$member_id'";
				echo $query;
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	
	if ($sponsor_id<>$row['sponsor_id']) {
		$query = "INSERT INTO change_log 
					SET item_table='members'
					, member_id='$member_id'
					, item_id='$member_id'
					, item_field='sponsor_id'
					, old_value='".$row['sponsor_id']."'
					, new_value='$sponsor_id'
					, changer_admin_id='{$_SESSION['admin_id']}'
					, change_location='member.php'
					, create_date=NOW()";
				echo $query;
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	}
  
	$msg[] = "Your Changes Are Saved";
	# Get fresh copy after the save
	$row = GetRowMember($db, $member_id);
	$sponsor_row = GetRowMember($db, $row['sponsor_id']);
}


#################################################################################
if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br>";
if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br>";
?>
<h1 id="page_title">Member Details (<?php echo $row['name']?>)</h1>
<p>
<?php // End search Applications
##############################################################################################
##
##############################################################################################
?>

<?php echo WriteBoxTop()?>
<form method="post">
<input type='hidden' value='<?php echo $member_id?>' name="member_id" />
      <table border="0" cellspacing="0" cellpadding="2" class="daTable">
      <thead>
        <tr>
          <td width="207" align="right">Name:</td>
          <td width="537"><?php echo $row['name']?> (<b>ID</b>: <?php echo $row['member_id']?>) </td>
        </tr>
        </thead>
        <tr>
          <td align="right"> Infustionsoft:</td>
          <td><b>Contact ID</b>: <?php echo $row["inf_contact_id"]?> &nbsp; <b>Referral Partner ID</b>: <?php echo $row["inf_aff_id"]?>
            </b></td>
        </tr>
        <tr>
          <td align="right"> Tracking:</td>
          <td><?php echo $row["t"]?></b></td>
        </tr>
        <tr>
        	<td align="right">Start Date</td>
        	<td><?php echo WriteDateTime($row['create_date'])?></td>
        </tr>
        <tr>
          <td align="right">Sponsor:</td>
          <td><?php echo $sponsor_row['name']?> (<b>ID</b>:<?php echo $sponsor_row['member_id']?>)
             &nbsp; <b>Username</b>:<?php echo $sponsor_row['email_username']?> &nbsp; <b>Email</b>:<?php echo $sponsor_row['email']?></td>
        </tr>
        <tr>
          <td align="right">Username:</td>
          <td><?php echo $row['email_username']?></td>
        </tr>
        <tr>
          <td align="right">Phone:</td>
          <td><?php echo $row['phone']?></td>
        </tr>
        <tr>
          <td align="right">Email:</td>
          <td><?php echo $row['email']?></td>
        </tr>
        <tr>
          <td align="right">Address:</td>
          <td><?php echo $row['address'].", ".$row['city'].", ".$row['state']." ".$row['zip'].", ".$row['country']?></td>
        </tr>
        <tr>
          <td align="right">Sponsor ID: </td>
          <td><input type="text" name="sponsor_id" value="<?php echo $row['sponsor_id']?>" id="field_sponsor_id" />
          <?php if( in_array($row['email_username'], array('sample')) ){ ?>
		  <span id="span_advisor_name"></span> <?php echo WriteFramePopup("search", "23searchall.php", "document.getElementById('field_sponsor_id').value=parameters[0]; document.getElementById('span_advisor_name').innerHTML=parameters[1];", 540, 450, "Search")?>
		  <?php } ?>
          </td>
        <tr>
          <td align="right">Affilate Start Date: </td>
          <td><input type="text" name="aff_start_date" value="<?php echo $row['sponsor_id']?>" id="field_sponsor_id" />
          <?php if( in_array($row['email_username'], array('sample')) ){ ?>
		  <span id="span_advisor_name"></span>
		  <?php } ?>
          </td>
        </tr>
        <tr>
          <td></td>
          <td><?php echo WriteButton("Save Changes");?>
          </td>
        </tr>
      </table>
</form>

<?php echo WriteBoxBottom()?>

<?php /*=writecontainertop("$ Earnings Payment History")?>

  <table id="table" border="0" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
            <tr <?php echo $HEADER_FORMAT?>>
              <td>#</td>
              <td>MP ID</td>
              <td>For Month</td>
              <td>Payment Sent Dt</td>
              <td>Amount</td>
              <td>Cancel Check</td>
              <td>Comment</td>
            </tr>
  <?php
	$query = "SELECT * FROM masspay m WHERE member_id='" . $row['member_id'] . "'";
	$query .= " AND mp_type='Monthly Payment' ORDER BY create_date DESC";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$total_payments = 0;
	for($j=1; $row = mysqli_fetch_assoc($result); $j++){
	   $total_payments += $earn_row['mp_amount'];
	   $cancel = "";
	   if(substr($earn_row['mp_comment'], 0, 11) == "Check Sent:"){
	     $cancel = "<a href=\"javascript:cancel_check('".$earn_row['mp_id']."');\">Cancel Check</a>";
	   }
    	$table_rows .= "<tr>"
	   	.WriteTD($j)
	   	.WriteTD($earn_row['mp_id'])
	   	.WriteTD(WriteDate($earn_row['create_date']))
	   	.WriteTD(WriteDate($earn_row['paid_date']))
	   	.WriteTD(WriteDollarCents($earn_row['mp_amount']), $TD_RIGHT)
	   	.WriteTD($cancel)
	   	.WriteTD($earn_row['mp_comment'])
		."</td>";
     }
?>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td align="right">TOTAL</td>
              <td align="right"><?php echo WriteDollarCents($total_payments)?></td>
              <td></td>
              <td></td>
            </tr>
        </table>


<?php echo writecontainerBottom() */ ?>

<?php echo writecontainertop("Ranks")?>
<?php $table_head = "<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Product/Rank</td>
    <td>Start Date</td>
    <td>End Date</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
$query = "SELECT mr.*, p.product_name
			FROM member_ranks mr
			LEFT JOIN products p USING (product_type) 
			WHERE mr.member_id='$member_id' 
			ORDER BY p.product_order";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD($row['product_name'])
   	.WriteTD(WriteDate($row['start_date']))
   	.WriteTD(WriteDate($row['end_date']))
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	echo "<font color=red>No ranks at this time.</font>";
}
?>
<?php echo writecontainerBottom();?>

<?php echo writecontainertop("Alternative Emails")?>
<?php $table_head = "<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Date</td>
    <td>Email</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
$query = "SELECT *
			FROM member_emails
			WHERE member_id='$member_id' 
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
	echo "No other emails.";
}
?>
<?php echo writecontainerBottom();?>

<?php echo writecontainertop("Affiliate Status Log")?>
<?php $table_head = "<table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Affiliate Status</td>
    <td>Start Date</td>
    <td>End Date</td>
    <td>Notes</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
$query = "SELECT *
			FROM member_log_aff
			WHERE member_id='$member_id' 
			ORDER BY start_date DESC";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	$sales_assit =
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD("<font color=green>Active Affiliate</font>")
   	.WriteTD(WriteDate($row['start_date']))
   	.WriteTD(WriteDate($row['end_date']))
   	.WriteTD($row['notes'])
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	echo "<font color=red>Was never an Active Affiliate.</font>";
}
?>
<?php echo writecontainerBottom();?>

<?php echo writecontainertop("Sales Assist Log")?>
<?php $table_head = "<table class='daTable'>
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
			WHERE member_id='$member_id' 
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
	echo "<font color=green>Yes, 20% by default.</font>";
}
?>
<?php echo writecontainerBottom();?>

<?php echo writecontainertop("Admin Change Log")?>
<?php $table_head = "<table class='daTable'>
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
			WHERE member_id='$member_id' 
			ORDER BY create_date DESC";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
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
	echo "<font color=grey>No changes logged.</font>";
}
?>
<?php echo writecontainerBottom();?>

<?php /*=writecontainertop("Rank Log")?>

<table id="table" border="0" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC" bgcolor="#FFFFFF">
  <tr <?php echo $HEADER_FORMAT?>>
    <td>#</td>
    <td>Date</td>
    <td>Old Level</td>
    <td>New Level</td>
</tr>
<?php
$query2 = "SELECT * FROM levels WHERE member_id='$id' ORDER BY create_date DESC";
include("../db2.php");
for ($j=1; $log_row = mysql_fetch_array($result2); $j++) {
	$table_rows .= "<tr>";
	echo WriteTD($j);
	echo WriteTD(WriteDate($log_row['create_date']));
	echo WriteTD($log_row['old_level']);
	echo WriteTD($log_row['new_level']);
	echo "</tr>";
}
?>
</table>
<?php echo writecontainerBottom() */?>


<?php //include("../include_footer.php"); ?>
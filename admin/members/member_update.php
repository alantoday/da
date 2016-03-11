<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../../includes/functions_inf.php");
include_once("../../includes/functions_cp.php");
$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
include_once("../includes_admin/include_menu.php");
?>
<?php
$member_row = GetRowMemberCoaches($db, $_GET['member_id']);
$sponsor_row = GetRowMember($db, $member_row['sponsor_id']);

$coach_options = array(
    array("0"=>"No")
    , array("1"=>"Start Up Coach (S1)")
    , array("2"=>"Set Up & Scale Up Coach (S2)")
    , array("3"=>"Welcome Coach")
	);	
$product_options = array(
    array(""=>" - Select -")
    , array("aff"=>"Affiliate")
    , array("asp-w"=>"ASIRE Walker")
    , array("asp-h"=>"ASIRE Hiker")
    , array("asp-c"=>"ASIRE Climber")
    , array("bas"=>"BASE")
    , array("ris"=>"RISE")
    , array("asc"=>"ASCEND")
    , array("pea"=>"PEAK")
    , array("ape"=>"APEX")
	);	
$fields_array = array ("name"
				, "initials"
				, "email"
				, "sponsor_id"
				, "t"
				, "sponsor_unknown"
				, "coach"
				, "passwd"
				);

if (isset($_POST['save_details'])) {
	
	#TODO: Validation
	$changes_made = false;
	foreach ($fields_array as $field) {
		if ($_POST[$field]<>$member_row[$field]) {
			if ($field=="passwd" && $_POST[$field]=="") {
				continue;	
			}
			$changes_made = true;
			$query = "UPDATE members 
						SET $field='".addslashes($_POST[$field])."'
						WHERE member_id='{$_GET['member_id']}'";
			#EchoLn($query);
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			LogAdminChange($db, $_GET['member_id'], "members", $_GET['member_id'], $field, $member_row[$field], $_POST[$field], $_SESSION['member_id'], "member_update.php");
		}
	}
	// If Coach Record changed
	if ($_POST['coach'] <> $member_row['coach']) {
		// Update coaches members.admin_security_id if they didn't have one before (or take it away if they just had Coach Access before)
		if ($_POST['coach'] && !$member_row['admin_security_id']) {
			$query = "UPDATE members 
						SET admin_security_id = ".ACCESS_COACH."
						WHERE member_id='{$_GET['member_id']}'";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		} elseif (!$_POST['coach'] && $member_row['admin_security_id']==ACCESS_COACH) {
			$query = "UPDATE members 
						SET admin_security_id = 0
						WHERE member_id='{$_GET['member_id']}'";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		}
	}
	// If Sponsor Record changed
	if ($_POST['sponsor_id'] <> $member_row['sponsor_id']) {
		// Change team_leader_id
		$query = "UPDATE members 
					SET team_leader_id = '{$sponsor_row['team_leader_id']}'
					WHERE member_id='{$_GET['member_id']}'";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		
		_RecalcCommissions($db, $_GET['member_id']);
	}
	
	// Update email in Infusionsoft if it has changed
	if ($member_row['email']<>$_POST['email']) {
		InfUpdateContactEmail ($member_row['inf_contact_id'],$_POST['email']);
	}
	if ($changes_made) {
		$member_row = GetRowMemberCoaches($db, $_GET['member_id']);
		$sponsor_row = GetRowMember($db, $member_row['sponsor_id']);
		
		$msg[] = "SUCCESS: Your Member Details changes are saved";
		# Get fresh copy after the save
	} else {
		$error[] = "NO CHANGE: You don't appear to have made any Member Details changes";			
	}
}

if(isset($_POST['save_end_history'])){
	if(isset($_POST['end_date_edit']) && $_POST['end_date_edit'] != ""){
		$query = "UPDATE member_ranks SET end_date = '".$_POST['end_date_edit']."' WHERE member_id = '".$_GET['member_id']."' AND product_type = '".$_POST['product_history_to_change']."'";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$msg[] = "SUCCESS: Product ".$_POST['product_history_to_change']." end date saved";
	}else{
		$query = "UPDATE member_ranks SET end_date = NULL WHERE member_id = '".$_GET['member_id']."' AND product_type = '".$_POST['product_history_to_change']."'";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$msg[] = "SUCCESS: Product ".$_POST['product_history_to_change']." end date saved (To be Null)";		
	}
	
}

if (isset($_POST['save_coaches'])) {
	
	$changes_made = false;
	if ($_POST['coach_id_startup'] <> $member_row['coach_id_startup']
		|| $_POST['coach_id_setup'] <> $member_row['coach_id_setup']
		|| $_POST['coach_id_scale'] <> $member_row['coach_id_scale']
		|| $_POST['coach_id_traffic'] <> $member_row['coach_id_traffic']
		|| $_POST['coach_id_success'] <> $member_row['coach_id_success']) {

		#TODO: Validation
			
		# Flag the existing records(s) as ended.
		$query = "UPDATE member_coaches
					SET end_date = CURDATE()
					WHERE member_id='{$_GET['member_id']}'
					AND (end_date IS NULL
						OR end_date >= CURDATE())";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		
		# Insert new records with new details and new start date
		$query = "INSERT INTO member_coaches 
					SET member_id='{$_GET['member_id']}'
					, coach_id_startup = {$_POST['coach_id_startup']}
					, coach_id_setup = {$_POST['coach_id_setup']}
					, coach_id_scale = {$_POST['coach_id_scale']}
					, coach_id_traffic = {$_POST['coach_id_traffic']}
					, coach_id_success = {$_POST['coach_id_success']}
					, start_date = CURDATE()";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$member_row = GetRowMemberCoaches($db, $_GET['member_id']);
		$sponsor_row = GetRowMember($db, $member_row['sponsor_id']);
		
		$msg[] = "SUCCESS: Your Mmeber Coaches changes are saved";
	} else {
		$error[] = "NO CHANGE: You don't appear to have made any Member Coaches changes";			
	}
}

if (!isset($_POST['submit'])) {
	$_POST['name'] = $member_row['name'];
	$_POST['initials'] = $member_row['initials'];
	$_POST['email'] = $member_row['email'];
	$_POST['sponsor_id'] = $member_row['sponsor_id'];
	$_POST['t'] = $member_row['t'];
	$_POST['sponsor_unknown'] = $member_row['sponsor_unknown'];
	$_POST['passwd'] = $member_row['passwd'];
	$_POST['coach'] = $member_row['coach'];
	$_POST['coach_id_startup'] = $member_row['coach_id_startup'];
	$_POST['coach_id_setup'] = $member_row['coach_id_setup'];
	$_POST['coach_id_scale'] = $member_row['coach_id_scale'];
	$_POST['coach_id_traffic'] = $member_row['coach_id_traffic'];
	$_POST['coach_id_success'] = $member_row['coach_id_success'];
}

if (isset($_POST['give_rank'])) {
	# NOTE: FOllowing repeated in calc_ranks_inf.php
	
	# Basic Validation
	if(!$_POST['product_type']) { $error[] = "MISSING: The Product Type."; }
	if(!$_POST['start_date']) { $error[] = "MISSING: The Start Date."; }
	
	#TODO: Add date validation (with calendar selecter
	
    # Get comp plan that existing when transaction was produced (not when ordered).
	$query = "SELECT default_cp_type
				FROM inf_products
				WHERE product_type  = '{$_POST['product_type']}'
				AND now() >= start_date 
				AND (end_date IS NULL OR now() <= end_date)";      
	#EchoLn ($query);      
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if ($row = mysqli_fetch_assoc($result)) {
		
		if (!empty($_POST['end_date'])) {
			$end_date_sql = ", end_date ='{$_POST['end_date']}'";
		} else {
			$end_date_sql = "";	
		}
		// Calculate who the Product Sponsor should be (based on who ownes the product), and lock that in for "Sponsor Lock"
		$tier1_aff_id = CPGetTier1AffId($db, $sponsor_row, $_POST['product_type'], $_POST['start_date']);
				
		$query = "INSERT INTO member_ranks
					SET member_id       ='{$_GET['member_id']}'
					, inf_payment_id    =0
					, product_type      ='{$_POST['product_type']}'
					, tier1_aff_id      ='$tier1_aff_id'
					, cp_type           ='{$row['default_cp_type']}'
					, start_date        ='{$_POST['start_date']}'
					$end_date_sql
					, create_date       =NOW()
					, notes       		='Comped by: {$_SESSION['member_id']}'
					";
		#EchoLn ($query);      
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		
		$msg[] = "SUCCESS: Your changes are saved";
	} else {
		$error[] = "ERROR: Something went wrong - could not find the product's details";	
	}
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
<div style="float:right"><a href="<?php echo str_replace("member_update.php","member.php", $_SERVER['REQUEST_URI']);?>">Member Notes</a></div>
<?php } ?>
<h1 id="page_title"><?php echo $member_row['name']?> (<?php echo $member_row['member_id']?>: <?php echo $member_row['username']?>)</h1>
<p>

<h2> Member Details</h2>
<form method="POST">
<table>
	<tr><td>
	Name:
    </td><td>
    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ""; ?>" />
    </td></tr>
	<tr><td>
	Email:
    </td><td>
    <input type="text" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ""; ?>" />
    </td></tr>
	<tr><td>
	Is Coach?
    </td><td>
        <select name="coach">
            <?php echo WriteSelect($_POST['coach'], $coach_options, false, false); ?>
        </select>
        &nbsp; Coach Initials: <input type="text" name="initials" maxlength="2" size="3" value="<?php echo isset($_POST['initials']) ? $_POST['initials'] : ""; ?>" />
    </td></tr>
    <tr><td>
    Sponsor's Id:
    </td><td>
    <input type="text" name="sponsor_id" id="field_sponsor_id" value="<?php echo isset($_POST['sponsor_id']) ? $_POST['sponsor_id'] : ""; ?>" />
        <span id="span_advisor_name"></span> 
		<?php echo WriteFramePopup("search", "search.php", 
				"document.getElementById('field_sponsor_id').value=parameters[0]; document.getElementById('span_advisor_name').innerHTML=parameters[1];"
				, 570, 440, "Search")?>
    </td></tr>
	<tr><td>
    Sponsor:
    </td><td>
        <select name="sponsor_unknown">
            <?php echo WriteSelect($_POST['sponsor_unknown'], array(array(0=>"Known"),array(1=>"Unknown")), false, false); ?>
        </select>
    </td></tr>
	<tr><td>
    Tag:
    </td><td>
    <input type="text" name="t" value="<?php echo isset($_POST['t']) ? $_POST['t'] : ""; ?>" />
    </td></tr>
	<tr><td>
	Password:
    </td><td>
    <input type="text" name="passwd" placeholder="Leave blank if unchanged" value="<?php echo isset($_POST['password']) ? $_POST['password'] : ""; ?>" />
    </td></tr>
    </td><td>&nbsp;
    </td><td>
    <?php echo WriteButton("Save Member Details", "save_details");?>
    </td></tr>
</table>
</form>

<h2>Member Coaches</h2>
<form method="POST">
<table>
	<tr>
    <td>
	Welcome Coach Id:
    </td><td>
    <input type="text" name="coach_id_setup" id="coach_id_setup" value="<?php echo isset($_POST['coach_id_setup']) ? $_POST['coach_id_setup'] : ""; ?>" /> 
        <span id="span_coach_id_setup"><?php echo $member_row['coach_name_setup']?></span> 
	  	<?php // echo WriteFramePopup("search", "search.php", "document.getElementById('coach_id_setup').value=parameters[0];", 570, 440, "Search")?>
		<span style='display:none;'>(<?php echo $member_row['coach_id_setup']?>: <?php echo $member_row['coach_username_setup']?>)</span>
		<?php echo WriteFramePopup("search_coach_id_setup", "search.php", 
				"document.getElementById('coach_id_setup').value=parameters[0]; document.getElementById('span_coach_id_setup').innerHTML=parameters[1];"
				, 570, 440, "Search")?>
		
    </td></tr>
	<tr><td>
	Start Up Coach Id:
    </td><td>
    <input type="text" name="coach_id_startup" id="coach_id_startup" value="<?php echo isset($_POST['coach_id_startup']) ? $_POST['coach_id_startup'] : ""; ?>" /> 
        <span id="span_coach_id_startup"><?php echo $member_row['coach_name_startup']?></span> 
	  	<?php // echo WriteFramePopup("search", "search.php", "document.getElementById('coach_id_setup').value=parameters[0];", 570, 440, "Search")?>
		<span style='display:none;'>(<?php echo $member_row['coach_id_startup']?>: <?php echo $member_row['coach_username_startup']?>)</span>
		<?php echo WriteFramePopup("search_coach_id_startup", "search.php", 
				"document.getElementById('coach_id_startup').value=parameters[0]; document.getElementById('span_coach_id_startup').innerHTML=parameters[1];"
				, 570, 440, "Search")?>    	    
    </td></tr>
	<tr>
    <td>
	Set Up &amp; Scale Up Coach Id:
    </td><td>
    <input type="text" name="coach_id_scale" id="coach_id_scale" value="<?php echo isset($_POST['coach_id_scale']) ? $_POST['coach_id_scale'] : ""; ?>" /> 
        <span id="span_coach_id_scale"><?php echo $member_row['coach_name_scale']?></span> 
	  	<?php // echo WriteFramePopup("search", "search.php", "document.getElementById('coach_id_setup').value=parameters[0];", 570, 440, "Search")?>
		<span style='display:none;'>(<?php echo $member_row['coach_id_scale']?>: <?php echo $member_row['coach_username_scale']?>)</span>
		<?php echo WriteFramePopup("search_coach_id_scale", "search.php", 
				"document.getElementById('coach_id_scale').value=parameters[0]; document.getElementById('span_coach_id_scale').innerHTML=parameters[1];"
				, 570, 440, "Search")?>       	    	    	
    </td></tr>
	<tr><td>
	Traffic Coach Id:
    </td><td>
    <input type="text" name="coach_id_traffic" id="coach_id_traffic" value="<?php echo isset($_POST['coach_id_traffic']) ? $_POST['coach_id_traffic'] : ""; ?>" /> 
        <span id="span_coach_id_traffic"><?php echo $member_row['coach_name_traffic']?></span> 
	  	<?php // echo WriteFramePopup("search", "search.php", "document.getElementById('coach_id_setup').value=parameters[0];", 570, 440, "Search")?>
		<span style='display:none;'>(<?php echo $member_row['coach_id_traffic']?>: <?php echo $member_row['coach_username_traffic']?>)</span>
		<?php echo WriteFramePopup("search_coach_id_traffic", "search.php", 
				"document.getElementById('coach_id_traffic').value=parameters[0]; document.getElementById('span_coach_id_traffic').innerHTML=parameters[1];"
				, 570, 440, "Search")?>           	    	
    </td></tr>
	<tr><td>
	Success Coach Id:
    </td><td>
    <input type="text" name="coach_id_success" id="coach_id_success" value="<?php echo isset($_POST['coach_id_success']) ? $_POST['coach_id_success'] : ""; ?>" /> 
        <span id="span_coach_id_success"><?php echo $member_row['coach_name_success']?></span> 
	  	<?php // echo WriteFramePopup("search", "search.php", "document.getElementById('coach_id_setup').value=parameters[0];", 570, 440, "Search")?>
		<span style='display:none;'>(<?php echo $member_row['coach_id_success']?>: <?php echo $member_row['coach_username_success']?>)</span>
		<?php echo WriteFramePopup("search_coach_id_success", "search.php", 
				"document.getElementById('coach_id_success').value=parameters[0]; document.getElementById('span_coach_id_success').innerHTML=parameters[1];"
				, 570, 440, "Search")?>    
    </td></tr>
    </td><td>&nbsp;
    </td><td>
    <?php echo WriteButton("Save Member Coaches", "save_coaches");?>
    </td></tr>
</table>
</form>

<h2>Product/Rank History</h2>
<?php $table_head = "<form method='post'><input type='hidden' id='product_to_change' name='product_name' value=''><table class='daTable'>
    <thead><tr>
    <td>#</td>
    <td>Product/Rank</td>
    <td>Status</td>
    <td>Start Date</td>
    <td>End Date</td>
    <td>Notes</td>
</tr></thead>";
$table_rows = "";
$table_foot = "</table></form>";
	
# Then get other Core products
$query = "SELECT p.product_name, mr.*
			FROM member_ranks mr
			JOIN inf_products p USING(product_type)
			WHERE  mr.member_id={$_GET['member_id']}
			AND mr.enabled = 1
			ORDER by p.product_order";
#EchoLn($query);
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $prow = mysqli_fetch_assoc($result); $i++){
	$end_edit_box = WriteDate($prow['end_date']);
	if(isset($_GET['action']) && isset($_GET['product'])){
		if($_GET['product'] == $prow['product_type']){
			$end_edit_box = "<form method='post'><input type='hidden' name='product_history_to_change' value='".$prow['product_type']."'><input type='text' name='end_date_edit' style='width:100px;' id='end_date_picker' value='".$prow['end_date']."'>".WriteButton("Save", "save_end_history")."</form>";
		}
	}						
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD($prow['product_name'])
   	.WriteTD("<font color=green>ACTIVE</font>")
   	.WriteTD(WriteDate($prow['start_date']))
   	.WriteTD("<a href='member_update.php?member_id=".$_GET['member_id']."&action=edit_history_end&product=".$prow['product_type']."'><img src='http://admin.digitalaltitude.co/images/icons/icon_edit.png'></a>".$end_edit_box)
   	.WriteTD($prow['notes'])
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	echo "<font color=red>No ranks at this time.</font>";
}
?>
<script>
	$( "#end_date_picker" ).datepicker({
  dateFormat: "yy-mm-dd"
});
</script>
<h2>Give Ranks</h2>
<form method="POST">
<table>
	<tr><td>
	Product Rank:
    </td><td>
        <select name="product_type">
            <?php echo WriteSelect(isset($_POST['product_type']) ? $_POST['product_type'] : "", $product_options, false, false); ?>
        </select>
    </td></tr>
	<tr><td>
	Start Date:
    </td><td>
    <input type="text" name="start_date" value="<?php echo isset($_POST['start_date']) ? $_POST['start_date'] : date("Y-m-d"); ?>" />
    </td></tr>
	<tr><td>
	End Date:
    </td><td>
    <input type="text" name="end_date" value="<?php echo isset($_POST['end_date']) ? $_POST['end_date'] : ""; ?>" /> Leave blank if does not expire
    </td></tr>
    </td><td>&nbsp;
    </td><td>
    <?php echo WriteButton("Give Rank", "give_rank");?>
    </td></tr>
</table>
</form>

<?php
function _RecalcCommissions($db, $member_id) {
	
	// First find out which days will be affect (so we flag to have daily status updated
	$query = "SELECT DISTINCT DATE(trans_date) AS trans_date
				FROM commissions c
				JOIN inf_payments p USING (inf_payment_id)
				JOIN members m USING (inf_contact_id)
				WHERE m.member_id='$member_id'";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$trans_dates = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$trans_dates[] = $row['trans_date'];
	}
	// Delete (so it can be recalculate the commissions on payments to this member has made
	// TODO: Keep a start/end date for sponsor (so in the really do change on a date we can still calc commissions correctly in the past
	$query = "DELETE c
				FROM commissions c
				JOIN inf_payments p USING (inf_payment_id)
				JOIN members m USING (inf_contact_id)
				WHERE m.member_id='$member_id'";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	
	if (!empty($trans_dates)) {
		# Flag the daily stats to be recalculated (on those days affected)
		$query = "UPDATE commissions_daily 
				SET recalc = 1
				WHERE create_date IN ('".implode("','",$trans_dates)."')";
		if (DEBUG) EchoLn("$query");
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);							
	}
}

?>

<?php //include("../include_footer.php"); ?>
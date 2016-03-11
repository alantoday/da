<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");

if(isset($_GET['term'])){
	$term = trim(strip_tags($_GET['term']));
	$query = "SELECT * FROM members WHERE name LIKE '%$term%'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	while ($row = mysqli_fetch_assoc($result)) { //loop through the retrieved values 
			$row['value']=htmlentities(stripslashes($row['name']));
			$row['member_id']=(int)$row['member_id'];
			$row_set[] = $row;//build an array
	}
	if (isset($row_set)) echo json_encode($row_set);	
	exit;
}

$mrow = GetRowMember($db, $_SESSION['member_id']);
$popup = isset($_GET['popup']) ? true : false;
if(isset($_GET['popup']) && $_GET['popup'] != 1){
 	$popup_id = $_GET['popup'];
}
if(isset($_GET['popup_id'])){
	$popup_id = $_GET['popup_id'];
}

$msg_color='#339933';
$search_str = isset($_GET['search_str']) ? $_GET['search_str'] : "";
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : "";
$contains = isset($_GET['contains']) ? $_GET['contains'] : "";
$product_type = isset($_GET['product_type']) ? $_GET['product_type'] : "";
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : "";
$hide = isset($_GET['hide']) ? $_GET['hide'] : "";

$steps_completed_default = 0;
$step_unlocked_default = 0;
if ($mrow['coach'] == 2) { // S2 Set Up & Scale Up Coach
	$steps_completed_default = 6;
	$step_unlocked_default = 2.1;
}
$steps_completed = isset($_GET['steps_completed']) ? $_GET['steps_completed'] : $steps_completed_default;
$step_unlocked = isset($_GET['step_unlocked']) ? $_GET['step_unlocked'] : $step_unlocked_default;
if ($popup) {
	$limit = isset($_GET['limit']) ? $_GET['limit'] : "";
	if ($search_field == "") $search_field = "Username";
} else {
	$limit = isset($_GET['limit']) ? $_GET['limit'] : "100";	
}
$submit = isset($_GET['submit']) ? $_GET['submit'] : "";


if (preg_match("/@/",$search_str)) {
	$search_field = "Email";	
}

if(!$popup) {
	include_once("../includes_admin/include_menu.php");
	if (in_array($mrow['admin_security_id'], array(ACCESS_ADMIN, ACCESS_SUPERADMIN))) {
		echo "<div style='float:right'><a href='search.php?missing_ranks=1'>Members with missing ranks</a></div>";
	}
	echo '<h1 id="page_title">Search...</h1>';
} else {
?>
	<html xmlns="http://www.w3.org/1999/xhtml"><head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<title>DA Admin</title>
	<head>
	    <link href="/scripts/bubblepopup/jquery.bubblepopup.v2.1.5.css" rel="stylesheet" type="text/css" />
		<script type='text/javascript' src='http://my.digitalaltitude.co/js/jquery-1.11.3.min.js'></script>
	
	    <script src="/scripts/bubblepopup/jquery.bubblepopup.v2.1.5.min.js" type="text/javascript"></script>
	            
	    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
	    <link rel="stylesheet" type="text/css" href="/css/style.css">
	    <link rel="stylesheet" type="text/css" href="/css/admin.css">
	    <link rel="stylesheet" type="text/css" href="http://my.digitalaltitude.co/js/qtip/jquery.qtip.min.css">
	    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
	    <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
	    <script type="text/javascript" language="javascript" src="/scripts/jquery.dropdownPlain.js"></script> 
	    <link rel="stylesheet" href="http://my.digitalaltitude.co/js/editor/themes/default/css/umeditor.css">
	    <script type="text/javascript" src="http://my.digitalaltitude.co/js/editor/umeditor.config.js"></script>
	    <script type="text/javascript" src="http://my.digitalaltitude.co/js/editor/umeditor.js"></script>
	    <script type="text/javascript" src="http://my.digitalaltitude.co/js/editor/lang/en/en.js"></script>         
	    <script type="text/javascript" src="http://my.digitalaltitude.co/js/qtip/jquery.qtip.min.js"></script>      
	</head>
	<body style="padding:0; margin:0;">
	
	<style>
	.tooltiptext{
	    display: none;
	}
	</style>
<?	
}

# If they are coach - let them only see their own records
$coach_sql = "";
if (in_array($mrow['admin_security_id'], array(ACCESS_COACH))) {
	if ($mrow['coach'] == 1) { // S1 Start Up Coach
		$coach_sql = "AND {$_SESSION['member_id']} IN (mc.coach_id_startup)";
	} elseif ($mrow['coach'] == 2) { // S2 Scale Coach
		$coach_sql = "AND {$_SESSION['member_id']} IN (mc.coach_id_scale)";
	} elseif ($mrow['coach'] == 3) { // Welcome Coach
		$coach_sql = "AND {$_SESSION['member_id']} IN (mc.coach_id_setup)";		
	} else {
		$coach_sql = "AND {$_SESSION['member_id']} IN (mc.coach_id_startup, mc.coach_id_setup, mc.coach_id_scale, mc.coach_id_traffic, mc.coach_id_success)";		
	}
}

?>
<form method="GET">
<?
if (!empty($popup)) {
	echo "<input type='hidden' name='popup' value='$popup'>";
}
if (!empty($popup_id)) {
	echo "<input type='hidden' name='popup' value='$popup_id'>";
}
?>	
<table><tr><td>
    Search:
    <select name="search_field">
    <?php
    $search_field_options = array("Name", "Email", "Username", "INF Contact Id", "Phone", "Member Id", "Sponsor Id", "Team Leader Id", "Tracking", "Status", "IP");
    echo WriteSelect($search_field, $search_field_options, false, false);
    ?>
    </select>
</td>
<td>
    Records: 
        <select name='limit'><?php echo WriteSelect($limit,array(12,25,50,100,500,'All'))?></select>
</td>
<? if (!$popup) { ?>
<td>
    Step Done: 
        <select name='steps_completed'><?php echo WriteSelect($steps_completed,array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18))?></select>
</td>
<td>
    Unlock Step:
        <select name='step_unlocked'><?php echo WriteSelect($step_unlocked,array(
		array("0.0"=>0, "1.0"=>1),array("1.1"=>1),array("1.2"=>2),array("1.3"=>3),array("1.4"=>4),array("1.5"=>5),array("1.6"=>6),array("2.1"=>7),array("2.2"=>8),array("2.3"=>"9"),array("2.4"=>"10+")))?></select>
</td>
<td>
    Has Rank:
        <select name='product_type'><?php echo WriteSelect($product_type,array(
		array(""=>"ANY"),array("aff"=>"Affiliate"),array("asp"=>"ASPIRE"),array("bas"=>"BASE"),array("ris"=>"RISE"),array("asc"=>"ASCEND"),array("pea"=>"PEAK"),array("ape"=>"APEX")))?></select>
</td>
<td>
    Hide:
        <select name='hide'><?php echo WriteSelect($hide,array("None","Touch Today"))?></select>
</td>
<?php /*
<td>
    Order By:
        <select name='order_by'><?php echo WriteSelect($order_by,array("Join Date","Last Touch"))?></select>
</td>
*/ ?>
<? } ?>
<td>
<style>
.styled-select select {	
	font-size: 14pt;
}
</style>
	<div class="styled-select">
    <select name='contains'><?php echo WriteSelect($contains,array('Starts With','Contains'))?></select>:
    <input type="text" name="search_str" id="search_str" value="<?php echo stripslashes($search_str)?>" placeholder="Leave blank for all"/>
    </div>
<script>
	$("#search_str").autocomplete({
	source: "search.php",
	minLength: 2
	});	
</script>    
</td><td>
<?php echo WriteButton("Search");?>
</td></tr>
</table>
</form>
<?php
$search_str = preg_replace("/^ /", '%', $search_str);
$search_str=trim($search_str);
if ($contains=="Contains") $search_str = "%".$search_str."%";

# Special Case - Find members that have a missing rank
if (isset($_GET['missing_ranks'])) {// && $search_str != "") {
	$query = "SELECT member_id 
				FROM member_ranks mr
				JOIN inf_products p ON mr.product_type = p.product_type
				WHERE p.core_level > 0
				GROUP BY member_id
				HAVING MAX( core_level ) - COUNT( DISTINCT core_level ) >0
				ORDER BY MAX( core_level ) - COUNT( DISTINCT core_level ) DESC";
	if (DEBUG) EchLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	while($row = mysqli_fetch_assoc($result)){
		$special_ids[] = $row['member_id'];	
	}
}

if ($submit || !empty($special_ids)) {// && $search_str != "") {

	if (strlen($search_str)<3) {
#	  echo "<p><font color=red>Search string too short, please try again.</p>";
#	  exit();
	}
		
	if ($search_field=="Name") {
	  $search_str = str_replace(" ", '%', $search_str);
	}
	
	$search_sql = "";  
	if (!empty($special_ids)) {
		 $search_sql .= "m.member_id IN (".implode(",", $special_ids).")";
	} elseif ( $search_field=="Name") {
		 $search_sql .= "(m.name LIKE '$search_str%' OR m.username = '$search_str')";
	} elseif ($search_field == "Email") {
		 $search_sql .= "(m.email LIKE '$search_str%')";
	} elseif ($search_field == "Member Id") {
		 $search_sql .= "m.member_id='$search_str'";
	} elseif ($search_field == "Sponsor Id") {
		 $search_sql .= "m.sponsor_id = '$search_str'";
	} elseif ($search_field == "Team Leader Id") {
		 $search_sql .= "m.team_leader_id = '$search_str'";
	} elseif ($search_field == "INF Contact Id") {
		 $search_sql .= "m.inf_contact_id='$search_str'";
	} elseif ($search_field == "Username") {
		 $search_sql .= "m.username LIKE '$search_str%'";
	} elseif ($search_field == "Tracking") {
		 $search_sql .= "m.t LIKE '$search_str%'";
	} elseif ($search_field == "Status") {
		 $search_sql .= "m.status LIKE '$search_str%'";
	} elseif ($search_field == "Phone") {
		 $search_sql .= "m.phone LIKE '$search_str'";
	} else {
		 $search_sql .= "1";
	}
	$sql_limit = "";			
	if ($limit != "All") {
		$sql_limit = "LIMIT $limit";
	}
	$product_type_sql = "";			
	if ($product_type == "asp") {
		$product_type_sql = "JOIN member_ranks mr_has ON mr_has.member_id = m.member_id AND mr_has.product_type LIKE '$product_type%'";
	} elseif ($product_type != "") {
		$product_type_sql = "JOIN member_ranks mr_has ON mr_has.member_id = m.member_id AND mr_has.product_type = '$product_type'";
	}
	if ($hide=="Touch Today") {
		$hide_sql = "AND m.member_id NOT IN (
				SELECT member_id 
				FROM member_notes mn 
				WHERE mn.member_id=m.member_id
				AND DATE(create_date) = CURDATE())";		
	} else {
		$hide_sql = "";		
	}        
/*		if ($order_by=="Last Touch") {
		$order_by_sql = "last_touch_date DESC";		
	} else {
		$order_by_sql = "m.create_date DESC";		
	}
*/        
	$query = "SELECT m.*
				, mc.coach_id_setup, coach_startup.initials as coach_startup_initials
				, coach_scale.initials as coach_scale_initials
				, coach_traffic.initials as coach_traffic_initials, coach_success.initials as coach_success_initials
				, s.email as sponsor_email, s.username AS sponsor_username, s.name as sponsor_name
				, md.skype
				, GROUP_CONCAT(DISTINCT p.initials ORDER BY p.product_order) as products
				, (SELECT MAX(create_date) 
					FROM member_notes mn 
					WHERE mn.member_id=m.member_id) AS last_touch_date
			FROM members m
			$product_type_sql	
			LEFT JOIN member_details md ON m.member_id = md.member_id
			LEFT JOIN members s ON s.member_id = m.sponsor_id
			LEFT JOIN member_ranks mr ON mr.member_id = m.member_id
				AND mr.enabled = 1
				AND (mr.end_date > NOW() OR mr.end_date IS NULL)
			LEFT JOIN inf_products p ON p.product_type = mr.product_type
			LEFT JOIN member_coaches mc ON mc.member_id = m.member_id
				AND mc.start_date <= CURDATE()
				AND (mc.end_date IS NULL OR mc.end_date >= CURDATE())
			JOIN members coach_startup ON coach_startup.member_id = mc.coach_id_startup
			JOIN members coach_setup ON coach_setup.member_id = mc.coach_id_setup
			JOIN members coach_scale ON coach_scale.member_id = mc.coach_id_scale
			JOIN members coach_traffic ON coach_traffic.member_id = mc.coach_id_traffic
			JOIN members coach_success ON coach_success.member_id = mc.coach_id_success
			WHERE $search_sql
			AND m.steps_completed >= 1+$steps_completed/10
			AND m.step_unlocked >= $step_unlocked
			$coach_sql	
			$hide_sql				
			GROUP BY m.member_id
			ORDER BY m.create_date DESC
			$sql_limit";        
	if (DEBUG) EchoLn($query);
	$table_head = "<table width='600px' class='daTable'><thead><tr>";
	if($popup){
		$table_head .= WriteTD("Action");			
	}		
	$table_head .= WriteTH("#")	
	. WriteTH("Start Date")	
	. WriteTH("ID")	
	. WriteTH("Username")	
	. WriteTH("Name")	
	. WriteTH("");	
	if (!$popup) { 
		$table_head .= WriteTH("Step<br>Done")	
		. WriteTH("Unlock<br>Step");
	}
	$table_head .= WriteTH("Products")	
	. WriteTH("Sponsor")
	. WriteTH("S1,S2<br>Coaches")
	. WriteTH("Last Touch");
	if(!$popup){
		$table_head .= WriteTH("Action");			
	}		
	$table_head .= "</tr></thead>";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	$table_foot = "</table>";
	for($i=1; $row = mysqli_fetch_assoc($result); $i++){
		$details = WriteNotesPopup(WriteVcard(),"Email: {$row['email']} <br>Skype: {$row['skype']}");
		if (!$row['member_id']) break;
		$login_link = "";
		$upd_link = "";
		if (in_array($mrow['admin_security_id'], array(ACCESS_SUPERADMIN, ACCESS_ADMIN))) {
			$login_link = "- <a target='_blank' href='member_login.php?member_id={$row['member_id']}'>Login</a>";
			$upd_link = "- <a href='member_update.php?member_id={$row['member_id']}'>Upd</a>";
		}
		$table_rows .= "<tr>";
		if($popup) {
			$table_rows.= WriteTD("<a href=\"javascript:top.frame_popup_callback_".$popup_id."(['".$row['member_id']."', '".str_replace("'", "\'", $row['name'])."', '".$row['username']."']);\">Select</a>");
		}
		$sponsor_color = ($row['sponsor_unknown'] && $row['sponsor_username']=="index") ? "red" : "";
		
		$table_rows .= WriteTD($i)	
		. WriteTD(WriteDate($row['create_date']))	
		. WriteTD($row['member_id'], TD_RIGHT)	
		. WriteTD($row['username'])	
		. WriteTD($row['name'])	
		. WriteTD($details);
		if (!$popup) { 
			$table_rows .= WriteTD(WriteNum(WriteStepNumber($row['steps_completed'])))	
			. WriteTD(WriteStepUnlocked($row['step_unlocked']));
		}
		$table_rows .= WriteTD(_WriteProducts(strtoupper($row['products'])))	
		. WriteTD(WriteNotesPopup("<font color=$sponsor_color>".$row['sponsor_username']."</font>", $row['sponsor_name']))	
		. WriteTD($row['coach_startup_initials'].",".$row['coach_scale_initials']);
#			. WriteTD($row['coach_startup_initials'].",".$row['coach_scale_initials'].",".$row['coach_traffic_initials'].",".$row['coach_success_initials']);
		$table_rows.= WriteTD(WriteDate($row['last_touch_date']));
		if(!$popup) {
			$table_rows.= WriteTD("<a href='member.php?member_id={$row['member_id']}'>Notes</a> $upd_link $login_link");
		}
		$table_rows .= "</tr>";
	}
	if ($i>1) {
		echo $table_head . $table_rows. $table_foot;
	} else {
		echo "<font color=grey>No records found.</font>";
	}

	if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
}

# eg Remove "ASP-W" if they have "ASP-C"
function _WriteProducts ($products_csv) {
	
	if (strpos($products_csv, "AS-C") !== false) {
		$products_csv = str_replace(array("AS-W,", "AS-C,"), array("",""), $products_csv);
	} elseif (strpos($products_csv, "AS-H") !== false) {
		$products_csv = str_replace("AP-W", "", $products_csv);
	}
	if (strpos($products_csv, "AP") !== false) {
		$products_csv = str_replace(array("AP", "PE,", "AC,", "RI,", "BA,"), array("APEX","","","",""), $products_csv);
	} elseif (strpos($products_csv, "PE") !== false) {
		$products_csv = str_replace(array("PE", "AC,", "RI,", "BA,"), array("PEAK","","",""), $products_csv);
	} elseif (strpos($products_csv, "AC") !== false) {
		$products_csv = str_replace(array("AC", "RI,", "BA,"), array("ASCEND","",""), $products_csv);
	} elseif (strpos($products_csv, "RI") !== false) {
		$products_csv = str_replace(array("RI", "BA,"), array("RISE",""), $products_csv);
	} elseif (strpos($products_csv, "BA") !== false) {
		$products_csv = str_replace(array("BA"), array("BASE"), $products_csv);
	}
	
	return $products_csv;
}
?>

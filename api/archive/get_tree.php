<?
#ini_set('max_execution_time', 400);
ini_set('max_execution_time', 5);
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_tokens.php");

# Validates email (and token) and gets $member_id and $email and $token
if (!isset($_SESSION['member_id']) || !isset($_SESSION['mm_id'])) {
	$use_mm_id = true;
	include("include_authorize.php");	
	$_SESSION['member_id'] = $member_id;
	$_SESSION['mm_id'] = $mm_id;
	if (!TokenValidate($db, $member_id, $token)) {
		echo "ERROR: Invalid or expired token ($mm_id, $token)";
		exit;		
	}
} else {
	$member_id = $_SESSION['member_id'];
	$mm_id = $_SESSION['mm_id'];
}

$order_by = empty($_GET['order_by']) ? '' : $order_by='A to Z';
$status = empty($_GET['status']) ? '' : $status='Hide';

// TREE DETAILS ORIGINATE FROM /members/koolPHP/treedetails.php being called via jquery/ajax POST by the script below.
?>
<link rel="stylesheet" type="text/css" href="http://digialti.com/css/style.css">

<script>
function reCalc(populatorID, populateeID, dbKey,reqMemId){
	$("#content").css({ opacity: 0.3 });
	$("#content_overlay").css("top",$("#content").height()/-1);
	$("#content_overlay").css({ opacity: 1.0 });
	getMemberDetails(populatorID, populateeID, dbKey, 1, reqMemId);
}
function getMemberDetails(populatorID, populateeID, dbKey, refresh, req_mem_id) {
          if(populatorID == null || populateeID == null || dbKey == null || populatorID == "" || populateeID == "" || dbKey == "") {
            alert("populateSelect(): missing parameters");
            return;
          }
          var passedData = new Object();
          passedData[dbKey] = populatorID;
          if(req_mem_id){
          	passedData['req_mem_id']=req_mem_id;
          }
          if(refresh){
          	passedData['recalc'] = refresh;
          }
          $.post("treedetails.php", passedData,
            function (data) {
		      var selectObj = null;
		      Obj = document.getElementById(populateeID);
		      Obj.innerHTML = data;
		      $("#content_overlay").css({ opacity: 0.0 });
		      $("#content").css({ opacity: 1.0 });
            }
          );
        }
</script>
<table cellpadding="0px" cellspacing="0px" width='740px'>
<tr><td>
<form action="get_tree.php" name="tree_form" id="tree_form" method="GET">
<?php /*<fieldset style="overflow:auto;border:1px solid gray;
		   -moz-border-radius-bottomleft: 7px;
		   -moz-border-radius-bottomright: 7px;
		   -moz-border-radius-topleft: 5px;
		   -moz-border-radius-topright: 7px;
		   -webkit-border-radius: 7px;
		   border-radius: 3px;padding:0px;padding-left:10px;">
<legend><b>Search</b></legend>
*/ ?>
<table cellpadding="0px" cellspacing="0px" border=0>
	<tr>
		<td align="right"><b>Show:</b> &nbsp; </td>
		<!--<td><select name='status'><?//=WriteSelect($status,array('All'=>'All','PRO'=>'PRO','Student'=>'Student','Cancelled'=>'Cancelled'))?></select></td>-->
		<td><select name='status'><?=WriteSelect($status,array('All'=>'All Members',"Hide"=>"Only Active or with Active team"))?></select></td>
		<td align="right">  &nbsp;   &nbsp;   &nbsp; <b>Sort:</b>  &nbsp; </td>
		<td><select name='order_by'><?=WriteSelect($order_by,array('Most Recent','A to Z'))?></select></td>
		<td colspan="2" align="right">  &nbsp;   &nbsp; 
        <input class="btn" id="" type="submit" name="submit" value="Search" style="font-size:13px">
	</tr>
</table>
</fieldset>
</form>
<?
$KoolControlsFolder= "../scripts/koolPHP/KoolControls";
require $KoolControlsFolder."/KoolTreeView/kooltreeview.php";

// reiteration to populate downline
function populate($mem_row,$members,$treeview){
	global $db, $status;
	if($members)
	foreach($members as $member_id => $nothing){
		$query = "SELECT m.*
		FROM members m 
		WHERE member_id='$member_id'
		LIMIT 1";
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		$mem_row2 = mysqli_fetch_assoc($result);

		if($status=='Hide' && $mem_row2['status']=='Cancelled'){
			$query = "SELECT COUNT(member_id) as count 
					FROM members 
					WHERE top = 0
					AND sponsor_id=$member_id";
			$result2 = mysqli_query($db, $query) or die(mysqli_error($db));
			$team_res = mysqli_fetch_assoc($result);
			$team_count = $team_res['count'];
			if(!$team_count && is_array($members))continue;
		}


		if(!is_array($members) && $mem_row2['status']=='Cancelled' && $status=='Hide'){
			unset($tree[$member_id]);
			continue;
		}

		$icon = "workerS_new.gif";
#		if($mem_row2['sub_failed'])$icon = "ball_yellowS.gif";
#		if($mem_row2['status']=='Student') $icon = "square_boxS.gif";
#		if($mem_row2['status']=='Cancelled')$icon = "square_blackS.gif";

#		if($mem_row2['sub_cancel_date']!="0000-00-00 00:00:00"){
#			if(strtotime(date("Y-m-d")) < strtotime(substr($mem_row2['sub_cancel_date'],0,10))){
#				$icon = "triangle_redS.gif";
#			}
#		}
		// adds/attaches member node to sponsor
		$node = $treeview->Add($mem_row['member_id'],$mem_row2['member_id'],$mem_row2['name'],false,$icon,"");
		// adds an ID node to facilitate retrieving member details
		$node->addData("member_id",$mem_row2['member_id']);
		if(is_array($nothing)){
			populate($mem_row2,$nothing,$treeview);
		}
	}
	return $treeview;
}

// sets up the root tree and frontline - reiterates through downline by calling populate()
function displayTeam($tree,$treeview){
	global $node_depth,$status, $db;
	$node_depth++;
	if(is_array($tree))
	foreach($tree as $member_id => $members){

		$query = "SELECT m.member_id, m.name 
		FROM members m 
		WHERE member_id='$member_id' 
		LIMIT 1";
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		$mem_row = mysqli_fetch_assoc($result);

		if($status=='Hide' && $mem_row['status']=='Cancelled'){
			$query = "SELECT COUNT(member_id) as count 
					FROM members 
					WHERE top = 0
					AND sponsor_id=$member_id";
			$result = mysqli_query($db, $query) or die(mysqli_error($db));
			$team_res = mysqli_fetch_assoc($result);
			$team_count = $team_res['count'];
			if(!$team_count && is_array($members))continue;
		}

		if(!is_array($members) && $mem_row['status']=='Cancelled' && $status=='Hide'){
			unset($tree[$member_id]);
			continue;
		}
		$icon = "workerS_new.gif";
#		if($mem_row['sub_failed'])$icon = "ball_yellowS.gif";
#		if($mem_row['status']=='Student') $icon = "square_boxS.gif";
#		if($mem_row['status']=='Cancelled')$icon = "square_blackS.gif";

#		if($mem_row['sub_cancel_date']!="0000-00-00 00:00:00"){
//				echo strtotime(date("Y-m-d"))." :: ".strtotime(substr($mem_row2['sub_cancel_date'],0,10))."<br>";
#			if(strtotime(date("Y-m-d")) < strtotime(substr($mem_row['sub_cancel_date'],0,10))){
#				$icon = "triangle_redS.gif";
#			}
#		}

		// adds/attaches member node to sponsor (if any)
		$node = $treeview->Add((($node_depth<2)?"root":$mem_row['member_id']),$mem_row['member_id'],$mem_row['name'],false,$icon,"");
		// adds an ID node to facilitate retrieving member details
		$node->addData("member_id",$mem_row['member_id']);
		// if there is any downline members, populate them
		if(is_array($members)){
			$treeview = populate($mem_row,$members,$treeview);
		}
	}
	return($treeview);
}

// Instantiate new kooltreeview object
$treeview = new KoolTreeView("treeview");
$treeview->scriptFolder = $KoolControlsFolder."/KoolTreeView";
$treeview->imageFolder=$KoolControlsFolder."/KoolTreeView/icons";

// Instantiate root node for object
$root = $treeview->getRootNode();
// root member's name
$root->text = $row['name'];
// set to expand root's members
$root->expand=true;
// Root member image
$root->image="workerS_new.gif";

$root->addData("member_id",$row['member_id']);
if($order_by){
	switch($order_by){
		case'Most Recent':
		$order_by = 'create_date ASC';
		break;
		case'A to Z':
		default:
		$order_by = 'name';
		break;
	}
}else{
	$order_by = "name";
//	$order_by = "create_date ASC";
}
//$status=false;
//if(!$status)$status='All';
if($status && $status=='All'){
	$status = false;
}elseif($status=='Hide'){

}
$tree = CreateMemberTree2($member_id, $status,$order_by);
#exit();
$treeview = displayTeam($tree,$treeview);
// display tree map lines
$treeview->showLines = true;

// style stuff
$style_select = "default";
$treeview->styleFolder=$style_select;
?>

<table cellpadding="0px" cellspacing="0px" width="<?=$PAGE['width']?>">
	<tr>
		<td valign="top" width="50%" style="text-align:left;">
<?php /*		<fieldset style="height:100%;overflow:scroll;border:1px solid gray;
		   -moz-border-radius-bottomleft: 7px;
		   -moz-border-radius-bottomright: 7px;
		   -moz-border-radius-topleft: 5px;
		   -moz-border-radius-topright: 7px;
		   -webkit-border-radius: 7px;
		   border-radius: 3px;">
		<legend><b>My Team</b></legend>
*/ ?>		<div style="text-align:left;padding-bottom:10px;">
<span id="expand" style="display:block;"><a href="javascript:void();" onclick="treeview.expandAll();MoreLess('expand','collapse');";><font color=grey size='-1'>Expand</font></a></span>
<span id="collapse" style="display:none;"><a href="javascript:void();" onclick="treeview.collapseAll();MoreLess('expand','collapse');">Collapse</a></span>
</div>

<div style="padding:0px;height:700px;overflow:auto;vertical-align:top;top:0px;">
			<? echo $treeview->Render();?>
			</div>
		</fieldset>
		</td>
		<td valign="top" width="50%" style="text-align:left;">
<?php /*		<fieldset style="overflow:auto;border:1px solid gray;
		   -moz-border-radius-bottomleft: 7px;
		   -moz-border-radius-bottomright: 7px;
		   -moz-border-radius-topleft: 5px;
		   -moz-border-radius-topright: 7px;
		   -webkit-border-radius: 7px;
		   border-radius: 3px;padding:0px;padding-left:10px;" align='left'>
		<legend><b>Member Legend</b></legend>
			<table>
				<tr>
					<td><img src="../scripts/koolPHP/KoolControls/KoolTreeView/icons/workerS_new.gif"></td>
					<td>Active</td>
					<td>&nbsp;</td>
					<td><img src="../scripts/koolPHP/KoolControls/KoolTreeView/icons/square_blackS.gif"></td>
					<td>Cancelled</td>
					<td>&nbsp;</td>
					<td><img src="../scripts/koolPHP/KoolControls/KoolTreeView/icons/ball_yellowS.gif"></td>
					<td>Recent CC Fail</td>
					<td>&nbsp;</td>
					<td><img src="../scripts/koolPHP/KoolControls/KoolTreeView/icons/triangle_redS.gif"></td>
					<td>Pending Cancellation</td>
				</tr>
				<tr>
					<td><img src="../scripts/koolPHP/KoolControls/KoolTreeView/icons/square_boxS.gif"></td>
					<td>Student</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</table>
		</fieldset><br />
*/ ?>
<?php /*		<fieldset id="content_top" style="width:320px; height: auto; border:1px solid gray;
		   -moz-border-radius-bottomleft: 7px;
		   -moz-border-radius-bottomright: 7px;
		   -moz-border-radius-topleft: 5px;
		   -moz-border-radius-topright: 7px;
		   -webkit-border-radius: 7px;
		   border-radius: 3px;
		">
			<legend><b>Details</b></legend>
			<div id='content' style="position:relative;z-index:0;height:678px;">Click each Member's name to view details.</div>
*/ ?>
<?php /*			<divm.status id='content_overlay' style="position:relative;z-index:1;text-align:center;"><img src="/images/ajax-loader.gif"></div>
*/ ?>


		</fieldset>
		</td>
	</tr>
</table>

</td></tr>
</table>
<div style="clear:both;"></div>

<?
	// script below initiates the call to the jquery/ajax script above.
?>
<script type="text/javascript">
    function nodeSelect_handle(sender,arg){
		var treenode = treeview.getNode(arg.NodeId);
		var mem_id = treenode.getData("member_id");
			getMemberDetails(mem_id,'content','mem_id',0,<?=$row['member_id']?>);
		$("#content").css({ opacity: 0.3 });
		$("#content_overlay").css("top",$("#content").height()/-1);
		$("#content_overlay").css({ opacity: 1.0 });
    }
    treeview.registerEvent("OnSelect",nodeSelect_handle);
    $("#content_overlay").css({ opacity: 0.0 });
</script>

<? ini_set('max_execution_time', 30); // restore default ?>

<? include("../mm/include_footer.php"); ?>

<?
###############################
# MEMBER TREE FUNCTIONS
###############################

function GetMemberTeam2($sponsor_ids=false, $status=false, $order_by=false){
	global $db;

	$member_teams = false;

	$sql_where = "";
	if($sponsor_ids !== false){
		$sponsor_ids = ForceArray($sponsor_ids);
		$sql_where .= " sponsor_id='".implode("' OR sponsor_id='", $sponsor_ids)."'";
	}

	if($status=='Hide'){
		$hide_teamless = true;
		$status = false;
	}

	$sql_where= $sql_where ? $sql_where : 1;
	
	$query = "SELECT member_id, sponsor_id, name, create_date 
		FROM members 
		WHERE top = 0
		AND $sql_where 
		ORDER BY ".(($order_by)?$order_by:"member_id"); //status!='Cancelled' AND status LIKE 'PRO%'
//status!='Cancelled' AND status LIKE 'PRO%'
#echo $query."<br>";
#		AND (status LIKE 'PRO%' OR status IN ('Student','Cancelled') ) 
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	while($mem_row=mysqli_fetch_assoc($result)){
		$member_teams[$mem_row['member_id']] = 1;	
	}
#		var_dump($member_teams);
#		var_dump($mem_row);
#		exit;
	return $member_teams;
}


function CreateMemberTree2($starting_advisor=false,$status=false,$order_by=false){

	$tree = array();
	$teams = GetMemberTeam2($starting_advisor,$status,$order_by);
	if(is_array($teams)){
		$tree = $teams;
	}

	if(is_array($teams)){
		foreach($teams as $mem_id => $unused){
			$tree2 = CreateMemberTree2($mem_id,$status,$order_by);
			$tree[$mem_id] = $tree2;
		}
	}
//	ksort($tree);
	return $tree;
}
?>
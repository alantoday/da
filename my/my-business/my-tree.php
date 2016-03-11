<?php include("../includes_my/header.php"); ?>
<?php
$order_by = empty($_GET['order_by']) ? '' : $order_by='A to Z';
$status = empty($_GET['status']) ? '' : $status=$_GET['status'];

// TREE DETAILS ORIGINATE FROM /koolPHP/treedetails.php being called via jquery/ajax POST by the script below.

$KoolControlsFolder= "http://da.digitalaltitude.co/scripts/koolPHP/KoolControls";
require PATH."scripts/koolPHP/KoolControls/KoolTreeView/kooltreeview.php";

// reiteration to populate downline
function populate($mem_row,$members,$treeview){
	global $db, $status;
	if($members)
	foreach($members as $member_id => $nothing){
		$mem_row2 = GetRowMember($db, $member_id);
		$mem_row2['rank'] = WriteRank($db, $mem_row2['member_id']);		 		
		$mem_row2['aspire_level'] = WriteAspireLevel($db, $mem_row2['member_id']);

		if($status=='Hide' /* && $mem_row2['status']=='Cancelled' */){
			$query = "SELECT COUNT(member_id) as count 
					FROM members 
					WHERE top = 0
					AND sponsor_id=$member_id";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			$team_res = mysqli_fetch_assoc($result);
			$team_count = $team_res['count'];
			if(!$team_count && is_array($members))continue;
		}


		if(!is_array($members) && $mem_row2['status']=='Cancelled' && $status=='Hide'){
			unset($tree[$member_id]);
			continue;
		}
		//icon setup 
		$icon = "icon_none.png";
		
		if($mem_row2['rank'] == "AFFILIATE") $icon = "icon_none.png";
		if($mem_row2['aspire_level']) $icon = "icon_aspire.png";
		if($mem_row2['rank'] == "BASE") $icon = "icon_base.png";
		if($mem_row2['rank'] == "RISE") $icon = "icon_rise.png";
		if($mem_row2['rank'] == "ASCEND") $icon = "icon_ascend.png";
		if($mem_row2['rank'] == "PEAK") $icon = "icon_peak.png";
		if($mem_row2['rank'] == "APEX") $icon = "icon_apex.png";
		if($mem_row2['rank'] == "APEX Climber") $icon = "icon_apex.png";
		

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

		$mem_row = GetRowMember($db, $member_id);
		$mem_row['rank'] = WriteRank($db, $mem_row['member_id']);		 		
		$mem_row['aspire_level'] = WriteAspireLevel($db, $mem_row['member_id']);
		if($status=='Hide' /*&& $mem_row['status']=='Cancelled' */){
			$query = "SELECT COUNT(member_id) as count 
					FROM members 
					WHERE top = 0
					AND sponsor_id=$member_id";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			$team_res = mysqli_fetch_assoc($result);
			$team_count = $team_res['count'];
			if(!$team_count && is_array($members))continue;
		}

		if(!is_array($members) && $mem_row['status']=='Cancelled' && $status=='Hide'){
			unset($tree[$member_id]);
			continue;
		}
		//icon setup 
		$icon = "icon_none.png";		
		if($mem_row['rank'] == "AFFILIATE") $icon = "icon_none.png";
		if($mem_row['aspire_level']) $icon = "icon_aspire.png";
		if($mem_row['rank'] == "BASE") $icon = "icon_base.png";
		if($mem_row['rank'] == "RISE") $icon = "icon_rise.png";
		if($mem_row['rank'] == "ASCEND") $icon = "icon_ascend.png";
		if($mem_row['rank'] == "PEAK") $icon = "icon_peak.png";
		if($mem_row['rank'] == "APEX") $icon = "icon_apex.png";
		
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
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	while($mem_row=mysqli_fetch_assoc($result)){
		$member_teams[$mem_row['member_id']] = 1;	
	}
#		var_dump($member_teams);
#		var_dump($mem_row);
#		exit;
	return $member_teams;
}


function CreateMemberTree2($starting_advisor=false,$status=false,$order_by=false, $level = 1){

	global $_tree_levels;

	$tree = array();
	$teams = GetMemberTeam2($starting_advisor,$status,$order_by);
	if(is_array($teams)){
		$tree = $teams;
	}

	if(is_array($teams) && $level < 3){
		$level++;
		foreach($teams as $mem_id => $unused){
			$tree2 = CreateMemberTree2($mem_id,$status,$order_by, $level);
			$tree[$mem_id] = $tree2;
		}
	}
#	if ($order_by == "Most Recent") {
#		ksort($tree);
#	}
	return $tree;
}
?>

<?php echo MyWriteMidSection("MY TEAM", "Monitor Your Team's Duplication",
	"Keep an eye on your team's climb up the mountain. Reach out to them and give them a hand up.",
	"MY CAMPAIGNS","/my-business/my-campaigns.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-business_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<script>
function reCalc(populatorID, populateeID, dbKey,reqMemId){
	$("#content").css({ opacity: 0.3 });
	$("#content_overlay").css("top",$("#content").height()/-1);
	$("#content_overlay").css({ opacity: 1.0 });
	getMemberDetails(populatorID, populateeID, dbKey, 1, reqMemId);
}
function toggle_type(show_type){
	if(show_type == "expand"){
		$("#expand").hide();
		$("#collapse").show();
		treeview.expandAll();
	}else{
		$("#expand").show();
		$("#collapse").hide();
		treeview.collapseAll();				
	}	
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
	        passedData['action'] = "GetMemberDetail";
	        var social_str = "";
	        $.post("http://my.digitalaltitude.co/tools/getData.php", passedData,
	            function (data) {
	            	social_str = "";
	            	$("#member_detail").hide();
	            	$("#member_name").html(data['name']);
	            	$("#member_skype").html(data['skype']);
	            	$("#member_phone").html(data['phone']);
	            	if(data['facebook'] != '' && data['facebook'] != null){
	            		social_str = social_str + "<a href='" + data['facebook'] + "'><img src='/images/socialicon/facebook.png' height='50px'></a>" 	
	            	}
	            	if(data['twitter'] != '' && data['twitter'] != null){
	            		social_str = social_str + "<a href='" + data['twitter'] + "'><img src='/images/socialicon/twitter.png' height='50px'></a>" 	
	            	}
	            	//if(data['blog'] != '' && data['blog'] != null){
	            	//	social_str = social_str + "<a href='" + data['blog'] + "'><img src='/images/socialicon/blogger.png' height='50px'></a>" 	
	            	//}
	            	$("#social_media").html(social_str);
	            	$("#member_email").html(data['email']);
	            	$("#member_rank").html(data['rank'] + " " + data['aspire_level']);
	            	$("#member_gravatar").attr({src: data['gravatar_url']});
	            	$("#member_detail").show();
	            	/*
	                var selectObj = null;
	                Obj = document.getElementById(populateeID);
	                Obj.innerHTML = data;
	                $("#content_overlay").css({opacity: 0.0});
	                $("#content").css({opacity: 1.0});
	                */
	            },"json"
	        );
        }
</script>
<table cellpadding="0px" cellspacing="0px" width='880px'>
<tr><td>
<form name="tree_form" id="tree_form" method="GET">
<?php  /*<fieldset style="overflow:auto;border:1px solid gray;
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
		<!--<td><select name='status'><?php//=WriteSelect($status,array('All'=>'All','PRO'=>'PRO','Student'=>'Student','Cancelled'=>'Cancelled'))?></select></td>-->
		<td><select name='status'><?php echo WriteSelect($status,array('All'=>'All Members',"Hide"=>"Only Active or with Active team"))?></select></td>
		<td align="right">  &nbsp;   &nbsp;   &nbsp; <b>Sort:</b>  &nbsp; </td>
		<td><select name='order_by'><?php echo WriteSelect($order_by,array('Most Recent','A to Z'))?></select></td>
		<td colspan="2" align="right">  &nbsp;   &nbsp; 
        <input class="btn" id="" type="submit" name="submit" value="Search">
	</tr>
</table>
</fieldset>
</form>

<?php
// Instantiate new kooltreeview object
$treeview = new KoolTreeView("treeview");
$treeview->scriptFolder = $KoolControlsFolder."/KoolTreeView";
$treeview->imageFolder=$KoolControlsFolder."/KoolTreeView/icons";

// Instantiate root node for object
$root = $treeview->getRootNode();
// root member's name
$root->text = $mrow['name'];
// set to expand root's members
$root->expand=true;
// Root member image

//icon setup 
$def_icon = "workerS_new.gif";		
if($mrow['rank'] == "AFFILIATE") $def_icon = "workerS_new.png";
if($mrow['aspire_level']) $def_icon = "icon_aspire.png";
if($mrow['rank'] == "BASE") $def_icon = "icon_base.png";
if($mrow['rank'] == "RISE") $def_icon = "icon_rise.png";
if($mrow['rank'] == "ASCEND") $def_icon = "icon_ascend.png";
if($mrow['rank'] == "PEAK") $def_icon = "icon_peak.png";
if($mrow['rank'] == "APEX") $def_icon = "icon_apex.png";
if($mrow['rank'] == "APEX Climber") $def_icon = "icon_apex.png";


$root->image= $def_icon;

$root->addData("member_id",$_SESSION['member_id']);
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
//	$order_by = "name";
	$order_by = "create_date ASC";
}
//$status=false;
//if(!$status)$status='All';
if($status && $status=='All'){
	$status = false;
}elseif($status=='Hide'){

}
$tree = CreateMemberTree2($_SESSION['member_id'], $status, $order_by);
#exit();
$treeview = displayTeam($tree, $treeview);
// display tree map lines
$treeview->showLines = true;

// style stuff
$style_select = "default";
$treeview->styleFolder=$style_select;
?>

<table cellpadding="0px" cellspacing="0px" width="100%">
	<tr>
		<td width="50%" style="text-align:left;vertical-align:top;">
<?php  /*		<fieldset style="height:100%;overflow:scroll;border:1px solid gray;
		   -moz-border-radius-bottomleft: 7px;
		   -moz-border-radius-bottomright: 7px;
		   -moz-border-radius-topleft: 5px;
		   -moz-border-radius-topright: 7px;
		   -webkit-border-radius: 7px;
		   border-radius: 3px;">
		<legend><b>My Team</b></legend>
*/ ?>
			<div style="padding:0px;overflow:auto;vertical-align:top;top:0px;border:0px solid #999;border-radius:5px;width:100%">
                <div style="text-align:left;padding-bottom:10px;padding-left:5px;">
                <span id="expand" style="display:block;"><a href="javascript:void();" onclick="toggle_type('expand');";><font color=blue size='-1'>Expand All</font></a></span>
                <span id="collapse" style="display:none;"><a href="javascript:void();" onclick="toggle_type('collapse');"><font color=blue size='-1'>Collapse</font></a></span>
                </div>
				<?php echo $treeview->Render();?>
			</div>						
		</fieldset>
		</td>
		<td valign="top" width="50%" style="text-align:left;vertical-align:top;">
        <style>
			td {
				padding:5px;	
			}
		</style>
			<div id="member_legend" style="padding:0px;vertical-align:top;border:0px solid #999;background-color:#;border-radius:0px;display:block;">
				<p><b>Member Legend</b></p>
				<table>
					<tr>
						<td><img src="http://da.digitalaltitude.co/scripts/koolPHP/KoolControls/KoolTreeView/icons/icon_none.png"></td>
						<td>NONE</td>						
						<td><img src="/images/tree_icons/affiliate.png"></td>
						<td>AFFILIATE</td>
						<td><img src="http://da.digitalaltitude.co/scripts/koolPHP/KoolControls/KoolTreeView/icons/icon_aspire.png"></td>
						<td>ASPIRE</td>
						<td><img src="http://da.digitalaltitude.co/scripts/koolPHP/KoolControls/KoolTreeView/icons/icon_base.png"></td>
						<td>BASE</td>
					</tr>
					<tr>	
						<td><img src="http://da.digitalaltitude.co/scripts/koolPHP/KoolControls/KoolTreeView/icons/icon_rise.png"></td>
						<td>RISE</td>
						<td><img src="http://da.digitalaltitude.co/scripts/koolPHP/KoolControls/KoolTreeView/icons/icon_ascend.png"></td>
						<td>ASCEND</td>
						<td><img src="http://da.digitalaltitude.co/scripts/koolPHP/KoolControls/KoolTreeView/icons/icon_peak.png"></td>
						<td>PEAK</td>
						<td><img src="http://da.digitalaltitude.co/scripts/koolPHP/KoolControls/KoolTreeView/icons/icon_apex.png"></td>
						<td>APEX</td>

					</tr>
				</table>
                <hr>
			</div>	
            <font color=grey>Select a member in your tree to see their details below.</font>
			
			<div id="member_detail" style="margin-top:10px;padding:5px;overflow:auto;vertical-align:top;top:0px;border:0px solid #999;border-radius:5px;display:none;">
				<table>
					<tr>
						<td>&nbsp;</td>
						<td>
							<img src="" id="member_gravatar" style="border-radius:50%;">							
						</td>
					</tr>					
					<tr>
						<td><b>Name:</b></td>
						<td id="member_name"></td>
					</tr>
					<tr>
						<td><b>Rank:</b></td>
						<td id="member_rank"></td>
					</tr>
					<tr>
						<td><b>Email:</b></td>
						<td id="member_email"></td>
					</tr>
					<tr>
						<td><b>Phone:</b></td>
						<td id="member_phone"></td>
					</tr>
					<tr>
						<td><b>Skype:</b></td>
						<td id="member_skype"></td>
					</tr>
					<tr>
						<td colspan=2 id="social_media"></td>
					</tr>
				</table>				
			</div>						
	
<?php  /*		<fieldset style="overflow:auto;border:1px solid gray;
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
<?php  /*		<fieldset id="content_top" style="width:320px; height: auto; border:1px solid gray;
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
<?php  /*			<divm.status id='content_overlay' style="position:relative;z-index:1;text-align:center;"><img src="/images/ajax-loader.gif"></div>
*/ ?>
		</fieldset>
		</td>
	</tr>
</table>

</td></tr>
</table>
<div style="clear:both;"></div>

<?php // script below initiates the call to the jquery/ajax script above. ?>
<script type="text/javascript">
    function nodeSelect_handle(sender,arg){
		var treenode = treeview.getNode(arg.NodeId);
		var mem_id = treenode.getData("member_id");
			getMemberDetails(mem_id,'content','mem_id',0,<?php echo $_SESSION['member_id']?>);
		$("#content").css({ opacity: 0.3 });
		$("#content_overlay").css("top",$("#content").height()/-1);
		$("#content_overlay").css({ opacity: 1.0 });
    }
    treeview.registerEvent("OnSelect",nodeSelect_handle);
    $("#content_overlay").css({ opacity: 0.0 });
</script>

<?php include(INCLUDES_MY."footer.php"); ?>
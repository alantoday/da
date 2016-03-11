<?php
include_once("../../includes/config.php");
include_once(PATH."includes/functions.php");

if(!isset($_SESSION['member_id'])){
	$redirect_url = "/index.php?error=Please Login First&pg=".$_SERVER['REQUEST_URI'];
	header ("Location: $redirect_url");
	$redirected = true;
	exit;
}
if(isset($admin_page) && (!$_SESSION['super_admin'] && !$_SESSION['sample'])){
	$redirect_url = "home.php";
	header ("Location: $redirect_url");
	$redirected = true;
	exit;
}
if (isset($member_details)) {
	$mrow = GetRowMemberDetails($db, $_SESSION['member_id']);
} else {
	$mrow = GetRowMember($db, $_SESSION['member_id']);	
}

if (!empty($_SESSION['coach']) && $mrow['admin_security_id'] > 0) {
	// Speical case for Force to see what Coaches see
	$mrow['admin_security_id'] = ACCESS_COACH;
}

?>
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta charset="UTF-8">
	<title>DA Admin</title>
<head>
    <link href="/scripts/bubblepopup/jquery.bubblepopup.v2.1.5.css" rel="stylesheet" type="text/css" />
	<script type='text/javascript' src='http://my.digitalaltitude.co/js/jquery-1.11.3.min.js'></script>

    <script src="/scripts/bubblepopup/jquery.bubblepopup.v2.1.5.min.js" type="text/javascript"></script>
           
    <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css" />
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <link rel="stylesheet" type="text/css" href="/css/admin.css">
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script src="http://code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
    <script type="text/javascript" language="javascript" src="/scripts/jquery.dropdownPlain.js"></script> 
    <link rel="stylesheet" href="http://my.digitalaltitude.co/js/editor/themes/default/css/umeditor.css">
    <script type="text/javascript" src="http://my.digitalaltitude.co/js/editor/umeditor.config.js"></script>
    <script type="text/javascript" src="http://my.digitalaltitude.co/js/editor/umeditor.js"></script>
    <script type="text/javascript" src="http://my.digitalaltitude.co/js/editor/lang/en/en.js"></script>         
    <script>
        $(function() {
         $( document ).tooltip({
		        content: function () {
		              return $(this).prop('title');
		          },         	
			  hide: {
			    effect: "", // fadeOut
			  },
			  close: function( event, ui ) {
			    ui.tooltip.hover(
			        function () {
			            $(this).stop(true).fadeTo(400, 1); 
			            //.fadeIn("slow"); // doesn't work because of stop()
			        },
			        function () {
			            $(this).fadeOut("400", function(){ $(this).remove(); })
			        }
			    );
			  }         	
         });
       });  
       function MoreLess(span01, span02){
       		$("#" + span01).toggle();
       		$("#" + span02).toggle();
       }   
	</script>         
</head>
<body style="padding:0; margin:0;">

<style>
.tooltiptext{
    display: none;
}
</style>
<?php if(empty($_GET['nomenu'])){ ?>
<div width="" style="height:75px;background-color:#51A7F9; padding-top:0px;">
<div style="width:900px; margin:auto">
<div align="right" style="padding-top:5px"><?php echo $mrow['name']; ?></div>
</div>
    <table align="center" cellpadding="0" cellspacing="0" width="900px">
        <tr>
            <td halign="left" valign="top" style='font-size:30px;'>
             <a href="/"><img height="40px" src="/images/DAlogowhite.png" border=0 /></a>
             <a href="/" style="text-decoration:none;"><span style="font-size:15px;color:#FFF;font-family:Raleway, sans-serif;">Admin</span></a>
             </td>
        </td>
        <td align="right">
            <div style="width:;text-align:left;">
                <ul class="dropdown">
                    <li><a href="/members/search.php">Members</a>
                        <ul class="sub_menu">
                             <li><a href="/members/search.php">Search</a></li>
<?php if (in_array($mrow['admin_security_id'], array(ACCESS_SUPERADMIN, ACCESS_ADMIN))) { ?>
                             <li><a href="/members/member_add.php">Add Member</a></li>
                             <li><a href="/members/search_visits.php">Search Funnel Visits</a></li>
<?php } ?>
                        </ul>
                    </li>
                    <li><a href="/inf/payments.php">Orders</a>
                        <ul class="sub_menu">
                             <li><a href="/inf/payments.php">Search</a></li>
                        </ul>
                    </li>
<?php if (in_array($mrow['admin_security_id'], array(ACCESS_SUPERADMIN, ACCESS_ADMIN))) { ?>
                    <li><a href="/commissions/search.php">Commissions</a>
                        <ul class="sub_menu">
                             <li><a href="/commissions/search.php">Search</a></li>
                        </ul>
                    </li>
<?php } ?>
                    <li><a href="/coaches/profiles.php">Coaches</a>
                        <ul class="sub_menu">
                             <li><a href="/coaches/welcome.php">Welcome</a></li>
                             <li><a href="/coaches/resources.php">Coach Resources</a></li>
<?php if (in_array($mrow['admin_security_id'], array(ACCESS_SUPERADMIN, ACCESS_ADMIN))) { ?>
                             <li><a href="/coaches/profiles.php">Coach Profiles</a></li>
<?php } else { ?>
                             <li><a href="/coaches/profiles.php">My Coach Profile</a></li>
<?php } ?>
                             <li><a href="/coaches/earnings.php">My Earnings</a></li>
<?php if (in_array($mrow['admin_security_id'], array(ACCESS_SUPERADMIN, ACCESS_ADMIN))) { ?>
                             <li><a href="/coaches/rotators.php">Coach Rotators</a></li>
<?php } ?>
                        </ul>
                    </li>
<?php if (in_array($mrow['admin_security_id'], array(ACCESS_SUPERADMIN, ACCESS_ADMIN))) { ?>
                    <li><a href="/content/edit.php">Content</a>
                        <ul class="sub_menu">
                             <li><a href="/content/edit.php">Text Sections</a></li>
                             <li><a href="/content/lessons.php">Lessons</a></li>
                             <li><a href="/content/tv.php">Live TV</a></li>
                             <li><a href="/content/recordings.php">TV Recordings</a></li>
                        </ul>
                    </li>
<?php } ?>
                    <li><a href="/index.php?action=logout">Logout</a></li>
                </ul>
            </div>
        </td>
        </tr></table>
</div>
<br>
<table align=center width="900px" border=0><tr><td>
<?php 
} 
if (isset($security_array)) {
	if (!in_array($mrow['admin_security_id'], $security_array)) {
		echo "You don't have permission to view this page";
		exit;
	}
}
?>


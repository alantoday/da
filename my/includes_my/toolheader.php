<?php 
include_once("config.php");
include_once(PATH."includes/functions.php");
include_once(INCLUDES_MY."myfunctions.php");
if (empty($_SESSION['member_id'])) {
	header("location: /?action=logout");	
}
if(isset($_POST['update_profile']) && isset($_POST['gravatar'])){
    if($_POST['update_profile'] && $_POST['gravatar'] != ''){
        $_SESSION['gravatar'] = $_POST['gravatar'];
    }
}
if($_SESSION['member_id'] == 1){
	$admin_mode = 1;
}
if(!$admin_mode){
	echo "no access!";
	exit;
}
$mrow = GetRowMember($db, $_SESSION['member_id']); 
//get gravatar
$email = $mrow['gravatar'];
$gravatar_url = get_gravatar($email);
$setup_complete = get_page_access_status($db,$_SESSION['member_id'],"menu access");

if(isset($_GET['skip_setup'])) {
    $setup_complete = true;
}
if($mrow['step_unlocked'] >= 5) {
    $setup_complete = true;
}
?>

<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Digital Altitude - Learn To Build A Digital Business And Elevate Your Income &mdash; Digital Altitude</title>
	<script type='text/javascript' src='/js/jquery-1.11.3.min.js'></script>
	<script type='text/javascript' src='/js/jqueryui/jquery-ui.min.js'></script>
	<script type='text/javascript' src='/js/my.js'></script>

    <link rel='stylesheet' id='formidable-css'  href='/js/jqueryui/jquery-ui.min.css' type='text/css' media='all' />
    <link rel='stylesheet' id='formidable-css'  href='/css/formidablepro.css?ver=2.0.14' type='text/css' media='all' />
    <link rel='stylesheet' id='optimizepress-page-style-css'  href='/css/style_wp.min.css?ver=2.5.1.1' type='text/css' media='all' />
    <link rel='stylesheet' id='optimizepress-default-css'  href='/css/default.min.css?ver=2.5.1.1' type='text/css' media='all' />

    <link rel="stylesheet" type="text/css" href="http://da.digitalaltitude.co/css/style.css"> 
    <link rel="stylesheet" type="text/css" href="/css/fa/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/useanyfont.css">
    <link rel="stylesheet" type="text/css" href="/css/my.css">
</head>
<body class="page op-theme">
        <div id="content_area" class="">
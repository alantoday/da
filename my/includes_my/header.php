<?php 
include_once("config.php");
include_once(PATH."includes/functions.php");
include_once(INCLUDES_MY."myfunctions.php");

$skip_header = 0;
if (isset($_GET['skip_h']) || isset($_POST['skip_h'])) {
	$skip_header = 1;
}

if (empty($_SESSION['member_id'])) {
	header("location: /?action=logout&pg=".urlencode($_SERVER['REQUEST_URI']));	
	exit();
}
if (isset($security)) {
	MyValidateAccess($security,"/products/$product");
}

if (empty($mrow)) {
	$mrow = GetRowMember($db, $_SESSION['member_id']); 
}

// Force them to complete steps in order - redirect then to earliest completed step
if (isset($lesson) && isset($product) && in_array($product, array("start-up","setup","scale"))) {
	
	$current_step_number = WriteStepNumber($lesson, $product);

	$completed_step_number = WriteStepNumber($mrow['steps_completed']);
	if ($current_step_number > ($completed_step_number + 1) && $completed_step_number < 18) {
		$next_lesson_url = WriteStepURL($completed_step_number + 1); 
#		if (DEBUG) EchoLn($completed_step_number);
#		if (DEBUG) EchoLn($next_lesson_url);
#		if (DEBUG) exit;
		header("location: $next_lesson_url?not-complete=1");	
		exit();		
	}
}

//get gravatar
$email = $mrow['gravatar'];
$gravatar_url = get_gravatar($email);
#$COMPLETE['setup'] = get_page_access_status($db,$_SESSION['member_id'],"menu access");

$COMPLETE['start-up'] = ($mrow['step_unlocked'] >= 2);
$COMPLETE['setup'] = ($mrow['step_unlocked'] >= 3);
$COMPLETE['scale'] = ($mrow['step_unlocked'] >= 4);
$COMPLETE['training'] = ($mrow['step_unlocked'] >= 5);

if(isset($_GET['skip_setup'])) {
    $COMPLETE['setup'] = true;
}

$product_color = isset($menu_color['top'][$product]) ? $menu_color['top'][$product] : $menu_color['top']['default'];
$mid_menu_color = isset($menu_color['mid'][$product]) ? $menu_color['mid'][$product] : $menu_color['mid']['default'];
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en-US"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en-US"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en-US"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en-US">
<!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<title>Digital Altitude - Learn To Build A Digital Business And Elevate Your Income &mdash; Digital Altitude</title>
<script type='text/javascript' src='/js/jquery-1.11.3.min.js'></script>
<script type='text/javascript' src='/js/jqueryui/jquery-ui.min.js'></script>
<!-- fancybox -->
<script type="text/javascript" src="/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>
<link rel="stylesheet" href="/js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="/js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

<!-- Optionally add helpers - button, thumbnail and/or media -->
<link rel="stylesheet" href="/js/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
<script type="text/javascript" src="/js/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
<script type="text/javascript" src="/js/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
<link rel="stylesheet" href="/js/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
<script type="text/javascript" src="/js/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
<script>
		function showedit_section(name){
			var offset = $("#section_edit_" + name).offset();
			//var top_position = offset.top - 265;
			//$("#edit_iframe_" + name).css("position","relative");
			//$("#edit_iframe_" + name).css("top", top_position + "px");
			$("#edit_iframe_" + name).show();
			$("#section_edit_" + name).hide();
		}
		function doneEdit(name){
			$("#edit_iframe_" + name).hide();
			$.post("http://my.digitalaltitude.co/tools/getData.php", {action: "GetSectionDetail", name: name, mid: <?=$mrow['member_id']?>}, function(db)
			{
				$("#section_content_" + name).html(db.section);
				$("#section_edit_" + name).show();
			},"json");	
		}   		
	</script>
<link rel='stylesheet' id='formidable-css'  href='/js/jqueryui/jquery-ui.min.css' type='text/css' media='all' />
<link rel='stylesheet' id='formidable-css'  href='/css/formidablepro.css?ver=2.0.14' type='text/css' media='all' />
<link rel='stylesheet' id='optimizepress-page-style-css'  href='/css/style_wp.min.css?ver=2.5.1.1' type='text/css' media='all' />
<link rel='stylesheet' id='optimizepress-default-css'  href='/css/default.min.css?ver=2.5.1.1' type='text/css' media='all' />
<link rel="stylesheet" type="text/css" href="/css/fa/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/css/useanyfont.css">
<style type="text/css" id="op_header_css">
p, .single-post-content li, #content_area li, .op-popup-button .default-button {
	font-family:"Ubuntu", sans-serif
}
a, blockquote {
	font-family:"Ubuntu", sans-serif
}
h1, .main-content h1, .single-post-content h1, .full-width.featured-panel h1, .latest-post .the-title {
	font-family:"Ubuntu", sans-serif
}
h2, .main-content h2, .single-post-content h2, .op-page-header h2, .featured-panel h2, .featured-posts .post-content h2, .featured-posts .post-content h2 a, .latest-post h2 a {
	font-family:"Ubuntu", sans-serif
}
h3, .main-content h3, .single-post-content h3 {
	font-family:"Ubuntu", sans-serif
}
h4, .main-content h4, .single-post-content h4, .older-post h4 a {
	font-family:"Ubuntu", sans-serif
}
h5, .main-content h5, .single-post-content h5 {
	font-family:"Ubuntu", sans-serif
}
h6, .main-content h6, .single-post-content h6 {
	font-family:"Ubuntu", sans-serif
}
.site-title, .site-title a {
	font-family:"Ubuntu", sans-serif
}
.site-description {
	font-family:"Ubuntu", sans-serif
}
.banner .site-description {
	font-family:"Ubuntu", sans-serif
}
a.sub_menu, a.sub_menu:visited, a.sub_menu:link {
	color:#fff
}
a.sub_menu:hover {
	color:rgb(48, 48, 48)
}
a, a:visited {
	text-decoration:none
}
a:hover, a:hover {
	color:rgb(48, 48, 48)
}
a:hover {
	text-decoration:none
}
.footer-navigation ul li a {
	color:#bababa;
	text-decoration:none
}
.footer-navigation ul li a:hover {
	color:#dbdbdb;
	text-decoration:none
}
.footer a {
	color:#bababa;
	text-decoration:none
}
.footer a:hover {
	color:#dbdbdb;
	text-decoration:none
}
.footer small.footer-copyright a {
	color:#bababa;
	text-decoration:none
}
.footer small.footer-copyright a:hover {
	color:#dbdbdb;
	text-decoration:none
}
.footer small.footer-disclaimer a {
	color:#bababa;
	text-decoration:none
}
.footer small.footer-disclaimer a:hover {
	text-decoration:none;
	color:#dbdbdb
}
body .container .include-nav .navigation ul li:hover > a, body .container .include-nav .navigation ul a:focus {
	color:#fff
}
div.include-nav .navigation ul li a {
	color:#ffffff
}
.nav-bar-below.op-page-header {
	background: #303030;
	background: -moz-linear-gradient(top, #303030 0%, #303030 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #303030), color-stop(100%, #303030));
	background: -webkit-linear-gradient(top, #303030 0%, #303030 100%);
	background: -o-linear-gradient(top, #303030 0%, #303030 100%);
	background: -ms-linear-gradient(top, #303030 0%, #303030 100%);
	background: linear-gradient(top, #303030 0%, #303030 100%));
 filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#303030', endColorstr='#303030', GradientType=0 )
}
.nav-bar-below ul li:hover, .nav-bar-below ul li:hover > a {
 background: <?php echo $product_color;
?>;
 background: -moz-linear-gradient(top, <?php echo $product_color;
?> 0%, <?php echo $product_color;
?> 100%);
 background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, <?php echo $product_color;
?>), color-stop(100%, <?php echo $product_color;
?>));
 background: -webkit-linear-gradient(top, <?php echo $product_color;
?> 0%, <?php echo $product_color;
?> 100%);
 background: -o-linear-gradient(top, <?php echo $product_color;
?> 0%, <?php echo $product_color;
?> 100%);
 background: -ms-linear-gradient(top, <?php echo $product_color;
?> 0%, <?php echo $product_color;
?> 100%);
 background: linear-gradient(top, <?php echo $product_color;
?> 0%, <?php echo $product_color;
?> 100%));
 filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $product_color;?>', endColorstr='<?php echo $product_color;?>', GradientType=0 )
}
body .container .nav-bar-below .navigation ul ul li {
	background-color:#303030 !important
}
</style>
<style id="op_custom_css">
    body .container .navigation ul {
        margin: 0 0 0 5em;
    }
    </style>
<style type="text/css">
	r/* CUSTOM CODE REQUIRED FOR THIS TEMPLATE DO NOT TOUCH */
    body{-webkit-font-smoothing:auto;}

    .icons-top a, .icons-top a:hover{color:#222 !important;font-weight:bold !important;}

    .navigation-sidebar-7 li a {padding-left:0px;}

    .testimonial-style-4 {margin: 25px auto;}

    .banner .logo img{width:200px;}

    .banner{padding:10px 0;}

    /* END CUSTOM CODE REQUIRED FOR THIS TEMPLATE DO NOT TOUCH */

    .footer-navigation {float:none;}
    .footer-copyright {float:none;padding-top:5px;}
    .op-promote {float:none;padding-top:5px;}
    .footer small {margin:0}
    .footer-disclaimer {padding:0!important;}
    .footer p {
        color: #bababa;
    }
    .footer small.footer-disclaimer a:hover {color:#53585F;}
    .footer, .footer p, .op-promote a, .footer .footer-copyright, .footer .footer-disclaimer {color:#bababa;}
</style>
<link href="//fonts.googleapis.com/css?family=Shadows Into Light:r|Raleway:300,r,b|Ubuntu:300,r,b,i,bi" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/css/my.css">
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
</script>
<!-- Calendar: tockify-button-start -->
<? if(!$skip_header){ ?>
<script type="text/javascript">
    // <![CDATA[
    var _tkf_opta=_tkf_opta||[];_tkf_opta.push({"v":2,"name":"digitalaltitude","position":"top","text":"Training Calendar","size":"small","color":"dark","offset":120,"mobile":{"background":"dark"},"anchor":"right"});
    (function() {var d=document;var tk=d.createElement("script");tk.type="text/javascript";tk.async="true";tk.id="tkf_embed";tk.src="https://tockify.com/_tockify.embed.js";var s = d.getElementsByTagName("script")[0];s.parentNode.insertBefore(tk, s);})();
    // ]]>
</script>
<? } ?>
<!-- Calendar: tockify-button-end -->
</head>
<body class="page op-theme">
<div class="container main-content">
<? if(!$skip_header) { ?>
<div class="include-nav" style="height:98px;padding-top:10px;background-color:<?php echo $product_color;?>">
    <div class="fixed-width cf">
        <div class="eight columns">
		<?php if (preg_match('/aspiresystem.co/', $_SERVER['SERVER_NAME'])) { ?>
            <img style="width:260px;padding-top:18px;" src="//s3.amazonaws.com/public.digitalaltitude.co/Aspire-White-Header-Logo.png" alt="Aspire System" />
        <?php } else { ?>
            <img style="width:260px;padding-top:18px;" src="/images/DA-Logo-ALL-White-300x5811.png" alt="Digital Altitude" />
        <?php } ?>
        </div>
        <div class="sixteen columns" style="float:none;">
           <style>
			.banner .navigation a{
				font-family: "Raleway", sans-serif;font-size: 14px;text-shadow: none;
			}
			body .container .navigation ul ul li a {
				min-width:80px;
			}
		  </style>
            <nav class="navigation fly-to-left">
                <ul id="navigation-alongside" style="margin:0px;float:right;">
                    <?php
					echo WriteMenu("My Coaches","/my-coach");
					echo WriteMenu("My Business","/my-business", $COMPLETE['start-up'], "Start Up");
					if (WriteAffStatus($db, $mrow['member_id'])!="Active") {
						echo WriteMenu("My Marketing","/my-marketing/intro.php", false, "You need to be an Active Affiliate first", "", "/order.php?product=aff");
					} else {
						echo WriteMenu("My Marketing","/my-marketing/intro.php", $COMPLETE['training'], "Training");								
					}
					if (empty($mrow['rank'])) {
						$rank = "ASPIRE" . " " . $mrow['aspire_level'];						
					} else {
						$rank = $mrow['rank'] . " " . $mrow['aspire_level'];
					}
					if (trim($rank) != "") {
						echo WriteMenu($rank, "/my-business/my-rank.php", $COMPLETE['start-up'], "Start Up","background-color:#303030;padding:10px;margin-top:7px;margin-left:12px;margin-right:4px;");
					}
					?>
                    <li class="menu-item" style="padding-left:20px;">
                        <div id="profile_pic_click" style="margin-right:40px;min-width:40px;height:50px;width:50px;float:right;border-radius:100px;background:url('<?=$gravatar_url?>');background-size:cover;background-position:center;"></div>
                        <ul style="position:absolute;top:50px;">
                            <li style='background:#303030;'><a href="/my-account/my-profile.php" style="color:#fff;">My Profile</a></li>
                            <li style='background:#303030;'><a href="/my-account/my-cards.php" style="color:#fff;">Billing</a></li>
                            <li style='background:#303030;'><a target="_blank" href="https://digitalaltitude.zendesk.com/" style="color:#fff;">Support</a></li>
                            <?php if ($mrow['admin_security_id'] == ACCESS_COACH) { ?>
                            <li style='background:#303030;'><a target="_blank" href="/dashboard/admin_login.php" style="color:#fff;">Coach Login &raquo;</a></li>
                            <?php } elseif ($mrow['admin_security_id'] && !$_SESSION['admin_login']) { ?>
                            <?php if (in_array($mrow['member_id'], array(1,100,104))) { ?>
                            <li style='background:#303030;'><a target="_blank" href="/dashboard/admin_login.php?coach=1" style="color:#fff;">Coach Login &raquo;</a></li>
                            <?php } ?>
                            <li style='background:#303030;'><a target="_blank" href="/dashboard/admin_login.php" style="color:#fff;">Admin Login &raquo;</a></li>
                            <?php } ?>
                            <li style='background:#303030;'><a href="/?action=logout" style="color:#fff;"><span style='font-size:9px'><?php echo $mrow['name']; ?><br></span>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<script>
	$( "#profile_pic_click" ).click(function() {
		  window.location.href='/my-account/my-profile.php';
	});
</script>
<div class="nav-bar-below op-page-header cf">
    <div class="fixed-width">
        <div class="twentyfour columns">
            <style>
				.op-page-header .navigation #navigation-below a{
					font-family: "Raleway", sans-serif;font-size: 14px;text-shadow: none;
				}
			</style>
            <nav class="navigation">
                <ul id="navigation-below">
                    <?php
					echo WriteMenu("Home","/dashboard",$COMPLETE['training'], "Training");
					echo WriteMenu("Start Up","/dashboard/start-up");
					echo WriteMenu("Set Up","/dashboard/setup", $COMPLETE['start-up'], "Start Up");
					echo WriteMenu("Scale Up","/dashboard/scale", $COMPLETE['setup'], "Set Up");
					echo WriteMenu("Training","/dashboard/training", $COMPLETE['scale'], "Scale Up");
					echo WriteMenu("Products","/products", $COMPLETE['training'], "Training");
					echo WriteMenu("Solutions","/dashboard/solutions", false, "Coming soon"); // Turned off for all until finished
					echo WriteMenu("Community","/community.php", $COMPLETE['training'], "Training");
					echo WriteMenu("Leaderboard","/leaderboard.php", $COMPLETE['training'], "Training");
					echo WriteMenu("TV","/tv", $COMPLETE['training'], "Training");
					?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<? } ?>
<div id="content_area" class="">
<?php
function WriteMenu($name, $link, $unlocked=true, $prerequisite='Start Up', $style="", $redirect_url="#") {
	# <li class="menu-item current-menu-item page_item current_page_item"><a href="/dashboard" style="padding-left:12px;padding-right:12px;">Home</a></li>
	$link = "http://".$_SERVER['HTTP_HOST'].$link;
	if ($unlocked) {
		return "<li class='menu-item'><a style='font-family:ubuntu !important;padding-left:12px;padding-right:12px;$style' href='$link'>$name</a></li>";
	} elseif (in_array($prerequisite, array("Start Up","Set Up","Scale Up","Training"))) {
		return "<li class='menu-item'><a style='font-family:ubuntu !important;color:#aaa;padding-right:12px;padding-right:7px;$style' href='$redirect_url' class='lock_popup' title='You must complete your $prerequisite steps first.'>
			$name <i class='fa fa-lock'></i></a></li>";		
	} else {
		return "<li class='menu-item'><a style='font-family:ubuntu !important;color:#aaa;padding-right:12px;padding-right:7px;$style' href='$redirect_url' class='lock_popup' title='$prerequisite'>
			$name <i class='fa fa-lock'></i></a></li>";		
	}
}
?>

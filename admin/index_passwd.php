<?php 
include_once("../includes/config.php");
include_once("../includes/functions.php");
$action = "no";
if(isset($_SERVER['QUERY_STRING'])) {
	parse_str($_SERVER['QUERY_STRING']);
} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Forgot Username/Password</title>
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
<br />
<br />
<table align="center">
<?php if($action == "no"){ ?>
	<form action="index_passwd.php?action=findit<?php echo ((isset($_GET['popup'])) ? "&popup=true" : "")?>" method="post">
	<tr><td>My <select name="value">
		<option value="username">Username </option>
		<option value="email">Email Address</option>
	           </select> is <input type="text" name="data" size="25"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td align="center"><input class="button" type="submit" name="submit" value="Send Me My Login Information"></td></tr>
	</form>
<?php } elseif($action == "findit"){
	if($_POST['data'] != ""){	
		$query = "SELECT name, email, username, passwd 
					FROM members 
					WHERE " . $_POST['value'] . " = '" . $_POST['data'] . "'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if($row = mysqli_fetch_array($result)){
			$message = "Your Admin login details are.\n\nhttp://admin.digitalalitude.co\nUsername: " . $row['username'] . "\nPassword: " . $row['passwd'] . "\n\nPlease do not respond to this email.";
			SendEmail($db, $row['email'],"Lost Password", $message, "Digital Altitude <support@digitalalitude.co>");
#			mail($row['email'],"Lost Password",$message,"From: Digital Altitude Admin <no-reply@digitalaltitude.co>");
?>
			<tr><td align="center">Your log in information has been sent to: <br /><?php echo $row['email'];?><br /><font>Be sure to check your Spam folder.</font></td></tr>
	 <?php	} else { ?>
			<tr><td align="center">We could not locate your login details with the data that you entered. Please try again. <br><br> <a href="javascript:history.go(-1)"  onMouseOver="self.status=document.referrer;return true">Go Back</a></td></tr>
	<?php } ?>
<?php } else {?>
		<tr><td align="center">You must include text to search for. <br><br> <a href="javascript:history.go(-1)"  onMouseOver="self.status=document.referrer;return true">Go Back</a></td></tr>
	<?php } ?>	 
<?php } ?>
</table>
</body>
</html>

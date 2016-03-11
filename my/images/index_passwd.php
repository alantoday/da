<?php 
include_once("../includes_my/config.php");
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
<link rel='stylesheet' id='formidable-css'  href='http://www.members.digitalaltitude.co/wp-content/uploads/formidable/css/formidablepro.css?ver=2.0.14' type='text/css' media='all' />
<link rel='stylesheet' id='membermouse-jquery-css-css'  href='//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css?ver=1.11.4' type='text/css' media='all' />
<link rel='stylesheet' id='membermouse-main-css'  href='http://www.members.digitalaltitude.co/wp-content/plugins/membermouse/resources/css/common/mm-main.css?ver=2.2.3' type='text/css' media='all' />
<link rel='stylesheet' id='membermouse-buttons-css'  href='http://www.members.digitalaltitude.co/wp-content/plugins/membermouse/resources/css/common/mm-buttons.css?ver=2.2.3' type='text/css' media='all' />
<link rel='stylesheet' id='membermouse-font-awesome-css'  href='//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css?ver=4.3.1' type='text/css' media='all' />
<link rel='stylesheet' id='uaf_client_css-css'  href='http://www.members.digitalaltitude.co/wp-content/uploads/useanyfont/uaf.css?ver=1443739764' type='text/css' media='all' />
<link rel='stylesheet' id='optimizepress-page-style-css'  href='http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/pages/membership/1/style.min.css?ver=2.5.1.1' type='text/css' media='all' />
<link rel='stylesheet' id='optimizepress-default-css'  href='http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/default.min.css?ver=2.5.1.1' type='text/css' media='all' />
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
			$message = "Your Admin login details are.\n\nhttp://my.digialti.com\nUsername: " . $row['username'] . "\nPassword: " . $row['passwd'] . "\n\nPlease do not respond to this email.";
			mail($row['email'],"Lost Password",$message,"From: Digital Altitude <no-reply@digialti.com>");
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

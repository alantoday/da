<?php
include_once("../includes_my/config.php");
include_once(PATH."includes/functions.php");
include_once(PATH."includes/functions_tokens.php");
if (empty($_SESSION['member_id'])) {
	header("location: /?action=logout&pg=".urlencode($_SERVER['REQUEST_URI']));	
}

$mrow = GetRowMember($db, $_SESSION['member_id']); 
$token = TokenCreate($db, $_SESSION['member_id']);

// Check they have admin access (and its not an Admin persona logged in a someone else
if ($mrow['admin_security_id'] > 1) {
	if ($_SESSION['admin_login'] && $mrow['admin_security_id'] <> 3) {
		$url = "http://admin.digitalaltitude.co/index.php?note=CanNotAutoLoginFromSomeoneElsesAccount";
	} else {
		$coach_param = "";
		if ($_GET['coach']) {
			$coach_param = "&coach=1";	
		}
		$url = "http://admin.digitalaltitude.co/index.php?action=logout&member_id={$_SESSION['member_id']}&token=$token$coach_param";
	}
} else {
	// Redirect to dashboard with a message
	$url = "http://my.digitalaltitude.co/dashboard/?note=PermissionDenied";	
}
header("location: $url");
exit;
?>
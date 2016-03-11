<?php
include_once("../../includes/config.php");
include_once(PATH."includes/functions.php");
include_once(PATH."includes/functions_tokens.php");

if (empty($_SESSION['member_id'])) {
	header("location: /?action=logout&pg=".urlencode($_SERVER['REQUEST_URI']));	
}

$mrow = GetRowMember($db, $_SESSION['member_id']);
if ($_GET['member_id'] <> $mrow['member_id']) {
	$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
	if (!in_array($mrow['admin_security_id'], $security_array)) {
		echo "<center><br>You don't have permission to login as a member.</center>";
		exit;
	}
}
$token = TokenCreate($db, $_SESSION['member_id']);

$url = "http://my.digitalaltitude.co/index.php?action=logout&member_id={$_GET['member_id']}&token=$token&pg={$_GET['pg']}";
header("location: $url");
exit;
?>
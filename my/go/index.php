<?php
$redirect = "/";
if (!empty($_GET['go'])) {
	$redirect = "https://bl279.infusionsoft.com/app/orderForms/".$_GET['go'];
}
header("location: $redirect");
exit;
?>
<?php
require_once("/home/digital/public_html/da/includes/config.php");
require_once(PATH."includes/functions.php");
require_once(PATH."includes/functions_inf.php");
require_once(PATH."includes/functions_twilio.php");

	// Testing
	$msg = "Congratulations! You have a new$aspire Member:
{$contact['FirstName']} {$contact['LastName']}
{$contact['Email']}
{$contact['Phone']}

~Michael Force
Founder
";
	echo TwilioSendText("+17202919897", $msg);		

?>
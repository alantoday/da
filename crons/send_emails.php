<?php
# USAGE: da.digitalaltitude.co/crons/send_emails.php?debug=1

require_once("../includes/config.php");
require_once("../includes/functions.php");

$query = "SELECT *
		FROM email_queue
		WHERE success = 0
		AND attempts < 1
		LIMIT 5";
if (DEBUG) EchoLn($query);
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
if ($row = mysqli_fetch_assoc($result)) {
	$success = 0;
	if (mail($row['email_to'],$row['subject'],$row['msg'],"From: ".$row['email_from'] . "\r\n" . "BCC: mydabcc@gmail.com")) {
		$success = 1;
	}
	$query = "UPDATE email_queue
		SET attempts = attempts + 1
		, attempt_date = NOW()	
		, success = $success
		WHERE email_queue_id = {$row['email_queue_id']}";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
} else {
	if (DEBUG) EchoLn("No queued emails found");
}
?>

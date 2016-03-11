<?php
# USAGE: da.digitalaltitude.co/crons/send_text_msgs.php?debug=1

require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_twilio.php");

$query = "SELECT tmq.*, c.prefix
		FROM text_msg_queue tmq
		JOIN countries c ON c.country_abbr = tmq.country_to
		WHERE tmq.success = 0
		AND tmq.attempts < 1
		LIMIT 5";
if (DEBUG) EchoLn($query);
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
if ($row = mysqli_fetch_assoc($result)) {
	$success = 0;
	if (TwilioSendText($row['prefix'].$row['phone_to'], $row['msg'])) {
		$success = 1;
	}
	$query = "UPDATE text_msg_queue
		SET attempts = attempts + 1
		, attempt_date = NOW()	
		, success = $success
		WHERE text_msg_queue_id = {$row['text_msg_queue_id']}";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
} else {
	if (DEBUG) EchoLn("No queued text messages found.");
}
?>

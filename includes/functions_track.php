<?php

require_once('config.php');
require_once('functions.php');

########################################################################
# Looks up Sponsor using new member using visits table
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_track.php?email=pierre@digitalaltitude.co&debug=1
if (isset($_GET['email'])) {
	WriteArray(TrackGetSponsorDetails($db, $_GET['email']));
}
 
function TrackGetSponsorDetails($db, $email) {

	$sponsor['sponsor_unknown'] = false;
	$sponsor['sponsor_id'] = 0;
	$sponsor['tracking'] = "";
	$sponsor['inf_aff_id'] = "";
	$ip = '';
	
	$query = "SELECT v.ip, v.t, m.member_id
				FROM visits v
				LEFT JOIN members m ON v.da = m.username
				WHERE v.url LIKE '%&email=$email%'
				ORDER BY v.create_date DESC";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
	while ($row = mysqli_fetch_assoc($result)) {

		if (!empty($row['username'])) {
			$sponsor['sponsor_id'] = $row['member_id'];
			$sponsor['tracking'] = $row['t'];
			break;			
		}
		$ip = $row['ip'];
		$query = "SELECT m.member_id, v.da, v.t 
				FROM members m
				JOIN visits v on v.da = m.username AND v.da <> ''
				WHERE v.ip='$ip'
				ORDER BY v.create_date DESC
				LIMIT 1";
		if (DEBUG) EchoLn($query);
		$result2 = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
		if ($row2 = mysqli_fetch_assoc($result2)) {
			$sponsor['sponsor_id'] = $row2['member_id'];
			$sponsor['tracking'] = $row2['t'];
			break;
		}
	}

	// If sponsor not found - send to force, but keep them still as sponsor_unknow
	if (!$sponsor['sponsor_id']) {
		$sponsor['sponsor_id'] = CP_DEFAULT_SPONSOR_ID;
		$sponsor['sponsor_unknown'] = true;
	}
	return $sponsor;
}
?>

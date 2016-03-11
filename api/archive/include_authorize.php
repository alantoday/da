<?
if (isset($use_mm_id)) {
	# Validates mm)d (and token) and gets $member_id and $mm_id and $token
	if (empty($_GET['mm_id'])) {
		echo "ERROR: Missing ID ($mm_id, $request)";
		exit();
	}
	$mm_id = $_GET['mm_id'];
	
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
	} else {
		# Break email tinto email and token parts (not all emails have tokens on them)
		$mm_id_parts = explode("~token~", $mm_id);
		$mm_id = $email_parts[0];
		$token = isset($mm_id_parts[1]) ? $mm_id_parts[1] : false;
	}

	$member_id = GetMemberIdFromMMId($db, $mm_id);
	if (!$member_id) {
		echo "ERROR: Invalid ID ($mm_id, $request)";
		exit();		
	}
} else {
	# Validates email (and token) and gets $member_id and $email and $token
	if (empty($_GET['email'])) {
		echo "ERROR: Missing email ($email, $request)";
		exit();
	}
	$email = $_GET['email'];
	
	if (isset($_GET['token'])) {
		$token = $_GET['token'];
	} else {
		# Break email tinto email and token parts (not all emails have tokens on them)
		$username_parts = explode("~token~", $email);
		$email = $email_parts[0];
		$token = isset($username_parts[1]) ? $username_parts[1] : false;
	}

	$member_id = GetMemberIdFromEmail($db, $email);
	if (!$member_id) {
		echo "ERROR: Invalid email ($email, $request)";
		exit();		
	}
}

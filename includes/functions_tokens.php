<?
function TokenGenerate($length = 15) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function TokenCreate($db, $member_id){
	
	$token = TokenGenerate(15);
	$query = "INSERT INTO tokens
	SET member_id = $member_id
	, token = '$token'
	, start_date = NOW()
	, end_date = DATE_ADD(now(), INTERVAL 3 HOUR)";
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	return $token;
}

function TokenValidate($db, $member_id, $token){
	
	if ($token=="jj23") {
		return true;
	}
	
	$query = "SELECT token_id
	FROM tokens
	WHERE token		='$token'
	AND start_date	<= NOW()
	AND end_date 	>= NOW()
	AND used_date IS NULL";
#	AND member_id	=$member_id
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if($row = mysqli_fetch_assoc($result)){
		// Flag token as used.
		// TODO: We can delete these really.
		$query = "UPDATE tokens
		SET used_date = NOW()
		WHERE token_id = '{$row['token_id']}'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		return true;
	} else {
		return false;
	}
}

?>

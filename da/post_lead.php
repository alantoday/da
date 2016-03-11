<?
$_GET['debug'] = 1;
require_once("../includes/config.php");
require_once("../includes/functions.php");

$inf_contact_id		=urlencode(isset($_POST['inf_contact_id']) ? $_POST['inf_contact_id'] : "");
$first_name			=urlencode(isset($_POST['first_name']) ? $_POST['first_name'] : "");
$last_name			=urlencode(isset($_POST['last_name']) ? $_POST['last_name'] : "");
$email				=urlencode(isset($_POST['email']) ? $_POST['email'] : "");

#TODO Need to write this

if ($email <> "" || $_GET['test']) {

	# Look for a duplicate lead for the same member within last 3 hours
	$query = "SELECT lead_id
				FROM leads
				WHERE email		='$email'
				AND member_id	=$sponsor_id
				AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(create_date)) > (3*60*60)
				LIMIT 1";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	if($row = mysqli_fetch_assoc($result)) {
		// Do nothing, it's a duplicate
	} else {
		# Store IP, just in case they have cookies disabled
		# List commissions earned by a member
		$query = "INSERT INTO leads
					SET member_id		='$sponsor_id'
					, email				='{$_GET['email']}'
					, name				='{$_GET['firstname']}'
					, da				='{$_SESSION['da']}'
					, ont_ip			='{$_GET['ip_addy']}'
					, ip				='{$_SERVER['REMOTE_ADDR']}'
					, t					='{$_SESSION['t']}'
					, url_ref			='{$_SERVER['HTTP_REFERER']}'
					, create_date		=NOW()";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	}
}
?>

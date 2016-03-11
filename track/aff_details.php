<?php header('Access-Control-Allow-Origin: *'); ?>
<?php
require_once('../includes/config.php');
require_once('../includes/functions_inf.php');

if (!empty($_GET['da'])) {
	$_SESSION['da'] = $_GET['da'];
} elseif (empty($_SESSION['da'])) {
	if (!empty($_COOKIE['da'])) {
		$_SESSION['da'] = $_COOKIE['da'];
	} else {
		$_SESSION['da'] = "";  // Or we could rotate or give to the company here.		
	}
}

if (!empty($_GET['t'])) {
	$_SESSION['t'] = $_GET['t'];
# If no sponsor or sponsor change don't use cookies for t
} elseif ($_SESSION['da']=="" || (!empty($_COOKIE['da']) && $_COOKIE['da'] != $_SESSION['da'])) {
	$_SESSION['t'] = '';
} else {
	$_SESSION['t'] = isset($_COOKIE['t']) ? $_COOKIE['t'] : "";
}

if ($_SESSION['da'] == "") {
	// First look for a visit from same IP with a sponsor
	$query = "SELECT da, t 
			FROM visits
			WHERE ip='{$_SERVER['REMOTE_ADDR']}'
			AND da <> ''
			ORDER BY visit_id DESC
			LIMIT 1";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	if($row = mysqli_fetch_assoc($result)) {
		$_SESSION['da'] = $row['da'];
		if ($_SESSION['t'] == '') {
			$_SESSION['t'] = $row['t'];	
		}
	}
}
echo $_SESSION['da'].",".$_SESSION['t'];
?>

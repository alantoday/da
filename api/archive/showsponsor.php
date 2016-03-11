<?
require_once("../includes/config.php");
require_once("../includes/functions.php");

# Uses cookies and IP to work out (using same
# alorithm as comp plan) who the current sponsor is/will be

# First look for cookies
if (!empty($_GET['da'])) {
	$_SESSION['da'] = $_GET['da'];
} elseif (empty($_SESSION['da'])) {
	if (!empty($_COOKIE['da'])) {
		$_SESSION['da'] = $_COOKIE['da'];
	} else {
		$_SESSION['da'] = "";  // Check by IP further down.		
	}
}

# Now look for sponsor by ip if we don't already know who the sponsor is
# We could potential limit how far we look back
if (empty($_SESSION['da'])) {	
	$query = "SELECT da 
			FROM visits
			WHERE ip='{$_SERVER['REMOTE_ADDR']}'
			AND da<>''
			ORDER BY visit_id DESC
			LIMIT 1";
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	if($row = mysqli_fetch_assoc($result)) {
		$_SESSION['da'] = $row['da'];
	} else {
		// Or we could rotate or give to the company here.
	}
}

# Get their details and find out they are still active 
if (!empty($_SESSION['da'])) {

	$activate_aff = false;
	do {
		$query = "SELECT m.sponsor_id, s.name AS sponsor_name, s.member_link_id AS sponsor_link_id, a.aff_start_date, m.top
				FROM members m
				JOIN members s ON m.sponsor_id = s.member_id
				LEFT JOIN aff_status a ON m.member_id = a.member_id
				WHERE m.member_link_id='{$_SESSION['da']}'
				AND a.aff_end_date IS NULL
				ORDER BY aff_status_id DESC
				LIMIT 1";
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		if($row = mysqli_fetch_assoc($result)) {
			$activate_aff = true;
		} else {
			# We con roll up to their sponsor or give to company or something	
			$_SESSION['da'] = $row['sponsor_id'];
		}
	} while (!$activate_aff && !$row['top']);
} else {
	# We con roll up to their sponsor or give to company or something	
}

if (empty($_SESSION['da'])) {	
	echo "Referring Affiliate: Michael Force";
} else {
	echo "Referring Affiliate: {$row['sponsor_name']} ({$row['sponsor_link_id']})";	
}
?>

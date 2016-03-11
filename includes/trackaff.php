<?php
#$debug = 1;

# EXAMPLES
# Cpature Page: https://aspir.link/cp1?da=sample&t=123
# Thankyou Page: https://aspir.link/s1?cf_uvid=101a282a104734451fc1fc69585d3f01&email=alantoday@gmail.com

require_once('config.php');
require_once("functions_inf.php");


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
$url_ref = isset($_GET['url_ref']) ? urldecode($_GET['url_ref']) : "";

setcookie('da', '', time() - 3600, "/"); //Clear it first, just in case, else we might encounter specific browser bugs
setcookie('da', $_SESSION['da'], time() + (60 * 60 * 24 * 365), "/");  // 365 days
setcookie('t', '', time() - 3600, "/"); //Clear it first, just in case, else we might encounter specific browser bugs
setcookie('t', $_SESSION['t'], time() + (60 * 60 * 24 * 365), "/");  // 365 days

$_SERVER['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "";

if (empty($_SERVER['HTTP_REFERER']) && preg_replace("/^www\./","",$_SERVER['HTTP_HOST']) == "digialti.com" && in_array($_SERVER['PHP_SELF'], array("/pixel.php","/includes/trackaff.php"))) {
	# use referring ID instead of current ID for the current URL.
	$_SERVER['HTTP_REFERER'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}
# parse referring URL into host and page
if (!empty($_SERVER['HTTP_REFERER'])) { // check there was a referrer
	$uri = parse_url($_SERVER['HTTP_REFERER']); // use the parse_url() function to create an array containing information about the domain
	$uri['host'] = preg_replace("/^www\./","",$uri['host']); // echo the host
} else {
	$uri['path'] = '';
	$uri['host'] = '';
	$uri['query'] = '';
}

if (!empty($url_ref)) { // check there was a referrer
	$uri_ref = parse_url($url_ref); // use the parse_url() function to create an array containing information about the domain
	$uri_ref['host'] = preg_replace("/^www\./","",$uri_ref['host']); // echo the host
} else {
	$uri_ref['path'] = '';
	$uri_ref['host'] = '';
	$uri_ref['query'] = '';
}

# Get 8i7 (or create one if it does not already exist
$query = "SELECT url_id 
			FROM urls
			WHERE url_host 	='{$uri['host']}'
			AND url_path	='{$uri['path']}'";
$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
if($row = mysqli_fetch_assoc($result)) {
	$url_id = $row['url_id'];
} else {
	# Create URL
	$query = "INSERT INTO urls
				SET url_host 	='{$uri['host']}'
				, url_path		='{$uri['path']}'
				, create_date 	=CURDATE()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	$url_id = mysqli_insert_id($db);	
}

# Get url_id (or create one if it does not already exist
$query = "SELECT url_ref_id 
			FROM urls_ref
			WHERE url_host 	='{$uri_ref['host']}'
			AND url_path	='{$uri_ref['path']}'";
$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
if($row = mysqli_fetch_assoc($result)) {	
	$url_ref_id = $row['url_ref_id'];
} else {
	# Create URL
	$query = "INSERT INTO urls_ref
				SET url_host 	='{$uri_ref['host']}'
				, url_path		='{$uri_ref['path']}'
				, create_date 	=CURDATE()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	$url_ref_id = mysqli_insert_id($db);	
}

$known_sponsor_id = 0; // No need to give visit to Force
$known_sponsor_t = "";
$sponsor_id = 100; // Assign to Force (or rotate) but flag as "Unknown"
$sponsor_unknown = true;
if (!empty($_SESSION['da'])) {
	# Try to find the affilate's member_id
	$query = "SELECT member_id 
				FROM members
				WHERE username='{$_SESSION['da']}'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	if($row = mysqli_fetch_assoc($result)) {
		$sponsor_id = $row['member_id'];
		$known_sponsor_id = $row['member_id'];
		$sponsor_unknown = false;
		$known_sponsor_t = $_SESSION['t']; // Only use this if sponsor found
	}
}

# Store IP, just in case they have cookies disabled
# List commissions earned by a member
$query = "SELECT visit_id 
			FROM visits
			WHERE da='{$_SESSION['da']}'
			AND ip='{$_SERVER['REMOTE_ADDR']}'
			AND t='".addslashes($_SESSION['t'])."'
			AND url='{$_SERVER['HTTP_REFERER']}'
			AND substring(create_date,1,10)=CURDATE()";
$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
$duplicate_visit = false;
if(mysqli_fetch_assoc($result)) {
	// Do nothing, it's a duplicate
	$duplicate_visit = true;
} else {
	# Store IP, just in case they have cookies disabled
	# List commissions earned by a member
	$url_ref_sql = !empty($url_ref) ? 
				", url_ref_id='$url_ref_id'
				, url_ref='$url_ref'"
#				, url_ref_query='".addslashes($uri_ref['query'])."'" 
				: "";
	$query = "INSERT INTO visits
				SET member_id='$known_sponsor_id'
				, da		='{$_SESSION['da']}'
				, ip		='{$_SERVER['REMOTE_ADDR']}'
				, t			='".addslashes($known_sponsor_t)."'
				, url_id	='$url_id'
				, url		='".addslashes($_SERVER['HTTP_REFERER'])."'
				, url_query	='".addslashes($uri['query'])."'
				$url_ref_sql
				, create_date=NOW()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
}

####################
# UPDATE STATS

# update stats
$visit_stats_id = 0;
$query = "SELECT visit_stats_id 
			FROM visit_stats
			WHERE member_id='$known_sponsor_id'
			AND t='".addslashes($_SESSION['t'])."'
			AND url_id='$url_id'
			AND substring(create_date,1,10)=CURDATE()";
$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
if($row = mysqli_fetch_assoc($result)) {
	$unique_visits_sql = "";
	if (!$duplicate_visit) {
		$unique_visits_sql = ", unique_visits = unique_visits + 1";
	}
	$visits_stats_id = $row['visit_stats_id'];
	$query = "UPDATE visit_stats
				SET visits		= visits + 1
				$unique_visits_sql
				WHERE visit_stats_id ='$visits_stats_id'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
} else {
	$query = "INSERT INTO visit_stats
				SET member_id	='$known_sponsor_id'
				, t				='".addslashes($_SESSION['t'])."'
				, url_id		='$url_id'
				, visits		= 1
				, unique_visits	= 1
				, create_date=NOW()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	$visit_stats_id = mysqli_insert_id($db);
}

//inf_field_Email
if (!empty($_GET['email']) || !empty($_GET['inf_field_Email'])) {
	
	$email = !empty($_GET['inf_field_Email']) ? $_GET['inf_field_Email'] :  $_GET['email'];

	
/*	// Get contact id from email
	$inf_contact_id = InfGetContactId ($email);
	
	if ($sponsor_unknown) {
		// Get sponsor details from contact id and ip
		$sponsor = InfGetSponsorDetails($db, $inf_contact_id);
		$known_sponsor_id = $sponsor['sponsor_id'];
		$known_sponsor_t = $sponsor['tracking'];
		$sponsor_unknown = false;
	}
*/	
	# Look for a duplicate lead for the same member within last 3 hours
	$query = "SELECT lead_id
				FROM leads
				WHERE email		='$email'
				AND member_id	='$known_sponsor_id'
				AND (UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(create_date)) < (3*60*60)
				LIMIT 1";
	if (!empty($debug)) echo "<br>$query";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	if($row = mysqli_fetch_assoc($result)) {
		// Do nothing, it's a duplicate
		if (!empty($debug)) echo "<br>Dup email ($email) - ignored";	
	} else {
		# Store Lead
		$query = "INSERT INTO leads
					SET member_id		='$known_sponsor_id'
					, email				='".str_replace(" ","+",$email)."'
					, da				='{$_SESSION['da']}'	
					, ip				='{$_SERVER['REMOTE_ADDR']}'
					, t					='".addslashes($known_sponsor_t)."'
					, url_id			='$url_id'
					, visit_stats_id	='$visit_stats_id'
					, uri				='".addslashes(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "")."'
					, url_ref			='".addslashes(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "")."'
					, create_date		=NOW()";
//					, inf_contact_id	='$inf_contact_id'
		if (!empty($debug)) echo "<br>$query";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
		
		# Increment lead count of visit_stats
		# TODO: Perhaps pass lead capture page URL so we can keep track of where lead was generated (not the thankyou page)
		$query = "UPDATE visit_stats
				SET leads = leads + 1
					, query = concat(query,'
'".addslashes($query)."')
				WHERE visit_stats_id ='$visits_stats_id'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);

	}
}
?>

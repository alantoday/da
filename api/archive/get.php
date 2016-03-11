<?php
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_tokens.php");

if (isset($_GET['debug'])) {
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);	
}
if (empty($_GET['request'])) {echo "Missing: request"; exit();}
$request = $_GET['request'];

if($request=="leaderboards") {
	echo GetLeaderboards($db);
	exit;
} 

# Validate input $email
if (!in_array($request, array("leaderboards"))) {
	
	# Validates email (and token) and gets $member_id and $email and $token
	include("include_authorize.php");	
}

#########################
# Handle Cases that require Email but do NOT need tokens

if($request=="aff_id") {
	echo GetAffId($db, $email);
	exit;
}
if($request=="token") {
	echo TokenCreate($db, $member_id);
	exit;
}
if ($request=="sponsors_name") {
	echo GetSponsor ($db, $email, false);
	exit;
}
if ($request=="sponsors_details") {
	echo GetSponsor ($db, $email, true);
	exit;
}

# $requet NOT FOUND
echo "ERROR: Invalid request";	
exit;



function GetAffId($db, $email) {
	
	# List commissions earned by a member
	$query = "SELECT m.member_link_id
				FROM members m
				WHERE m.email_username='$email'";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$table_rows = false;
	if($row = mysqli_fetch_assoc($result)) {
		$res = $row['member_link_id'];
	} else {
		$res = "INVALID_AFF_ID";
	}
	
	return $res;
}


function GetSponsor($db, $email, $details=false) {
	
	# Get sponsors details
	$query = "SELECT s.name, s.email, s.phone
				FROM members m
				JOIN members s ON m.sponsor_id = s.member_id
				WHERE m.email_username='$email'";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$table_rows = false;
	if($row = mysqli_fetch_assoc($result)) {
		if (!$details) {
			$res = $row['name'];
		} else {
			$res = "<h3>{$row['name']}</h3>";
			$res .= "<span style='width:100px'>Email:</span> {$row['email']}";
			$res .= "<br><span style='width:100px'>Phone:</span> {$row['phone']}";
		}
	} else {
		$res = ERROR_MISSING_SPONSOR . "(Account: $email)";
	}
	
	return $res;
}

function GetLeaderboards($db) {
			
	# List commissions earned by a member
	$query = "SELECT m.member_id, m.name, SUM(tier1_amt + tier2_amt + tier3_amt) AS sum_commissions
				FROM commissions c
				JOIN transactions t USING (trans_id)
				JOIN members m ON m.member_id = t.member_id
				WHERE m.member_id > 100
				GROUP BY m.member_id
				ORDER BY sum_commissions DESC
				LIMIT 5";
				// Add in date ranges
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$table_rows = false;
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$table_rows .= "<tr>"
		. WriteTD($i)	
#		. WriteTD(WriteDate($row['member_id']))	
		. WriteTD($row['name'])	
#		. WriteTD(WriteDollars($row['sum_commissions']), TD_RIGHT)	
		. "</tr>";
	}
	
	if (!$table_rows) {
		$res = "There are no members with commissions at this time.";
	} else {
		$table_header = "<tr><thead>"
		. WriteTH("Rank")	
		. WriteTH("Member")	
#		. WriteTH("Member ID")	
#		. WriteTH("Commissions", TD_RIGHT)	
		. "</tr></thead>";
		$res = '<link rel="stylesheet" type="text/css" href="http://digialti.com/css/style.css">';
		$res .= "<table width='300px' class='daTable'>";
		$res .= $table_header;
		$res .= $table_rows;
		$res .= "</table>";
}
	$res = "<h3>All Time Top Income Earners</h3>".$res;
	return $res;
}
?>

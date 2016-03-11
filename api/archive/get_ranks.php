<?
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_tokens.php");

# Validates email (and token) and gets $member_id and $email and $token
if (!isset($_SESSION['member_id']) || !isset($_SESSION['mm_id'])) {
	$use_mm_id = true;
	include("include_authorize.php");	
	$_SESSION['member_id'] = $member_id;
	$_SESSION['mm_id'] = $mm_id;
	if (!TokenValidate($db, $member_id, $token)) {
		echo "ERROR: Invalid or expired token ($mm_id, $token)";
		exit;		
	}
} else {
	$member_id = $_SESSION['member_id'];
	$mm_id = $_SESSION['mm_id'];
}

?>
<link rel="stylesheet" type="text/css" href="http://digialti.com/css/style.css">
<?
echo _GetRanksTable($db, $member_id);


function _GetRanksTable($db, $member_id) {
	
	# List member ranks
	$query = "SELECT mr.product_type, mr.start_date, mr.end_date, p.product_name, cp.cp_tiers
					, CASE WHEN sa.sa_pc IS NULL THEN cp.sa_pc_default ELSE sa.sa_pc END as sa_pc
				FROM member_ranks mr
				LEFT JOIN member_log_sa sa USING (member_id)
				JOIN products p USING (product_type)
				JOIN comp_plan cp USING (cp_id)
				WHERE mr.member_id=$member_id
				ORDER by p.product_order";
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$table_rows = '';
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$asp_note = "";
		if ($row['product_type']=="asp") {
			$asp_note = ($row['cp_tiers']==1) ? " (Hiker)" : " (Climber)";
		}
		$table_rows .= "<tr>"
		. WriteTD($i)
		. WriteTD($row['product_name'].$asp_note)	
		. WriteTD($row['sa_pc'] ? "<font color=green>Yes {$row['sa_pc']}%</font>" : "-",TD_CENTER)	
		. WriteTD(WriteDateTime($row['start_date']))	
		. WriteTD(WriteDateTime($row['end_date']))	
		. "</tr>";
	}
	if (!$table_rows) {
		$table_rows = "<tr><td colspan='8'><i><font color=grey>You have no active product ranks.</font></i></td></tr>";
	}
	$table_head = "<table width='550px' class='daTable'><thead><tr>"
	. WriteTH("#")	
	. WriteTH("Rank Status")
	. WriteTH("Sales Assist")
	. WriteTH("Start Date")	
	. WriteTH("End Date")	
	. "</tr></thead>";
	$table_foot = "</table>";
	if (!$table_rows) {
		$rest = "<i><font color=red>You have no history of any Active Product Ranks.</font></i>";
	} else {
		$res = $table_head . $table_rows . $table_foot;
	}
	return $res;
}
?>
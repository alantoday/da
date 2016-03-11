<?php include("../includes_my/header.php"); ?>
<?php
function _GetRanksTable($db, $member_id) {
	
	$table_rows = '';

	# Determine their affiliate status
	$aff_status = WriteAffStatus($db, $member_id);
	if (preg_match("/inactive/i",$aff_status)) {
		$active_aff = false;
		$aff_color = 'red';
		$aff_upgrade = "<a href='/my-account/order.php?product=aff'>ACTIVATE NOW</a>";
	} else {
		$active_aff = true;
		$aff_color='green';	
		$aff_upgrade = '';
	}
	$table_head = "<table width='100%' class='daTable'><thead><tr>"
	. WriteTH("Affiliate Program")
	. WriteTH("Status")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("Action")	
	. "</tr></thead>";
	$table_rows .= "<tr>"
	. WriteTD("AFFILIATE")	
	. WriteTD("<font color=$aff_color><b>".strtoupper($aff_status)."</b></font>")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTD($aff_upgrade)	
	. "</tr>";
	$table_rows .= "<tr>"
	. WriteTD("&nbsp;") . WriteTD("")	. WriteTH("") . WriteTH("")	. WriteTH("") . WriteTD("")	
	. "</tr>";
	$table_rows .= "<thead><tr>"
	. WriteTH("ASPIRE Level")
	. WriteTH("Status")	
	. WriteTH("Commission<br>Tier 1", TD_CENTER)	
	. WriteTH("Tier 2", TD_CENTER)	
	. WriteTH("Tier 3", TD_CENTER)	
	. WriteTH("Action")	
	. "</tr></thead>";

	# Determine their ASPIRE status
	# Write their Aspire Level
	$aspire_level = WriteAspireLevel($db, $member_id);
	$status['asp-w'] = $status['asp-h'] = $status['asp-c'] = "<font color=green>ACTIVE</font>";
	$upgrade['asp-w'] = $upgrade['asp-h'] = $upgrade['asp-c'] = '';
	$asp_level = '';
	if ($aspire_level=="") {
		$status['asp-w'] = $status['asp-h'] = $status['asp-c'] = "<font color=red>INACTIVE</font>";
		$upgrade['asp-w'] = "Upgrade Now to: <a href='/my-account/order.php?product=asp-w'>ASPIRE Walker</a>";
		$upgrade['asp-h'] = "Upgrade Now to: <a href='/my-account/order.php?product=asp-h'>ASPIRE Walker & Hiker</a>";
		$upgrade['asp-c'] = "Upgrade Now to: <a href='/my-account/order.php?product=asp-c'>ASPIRE Walker, Hiker & Climber</a>";
	} elseif (preg_match("/walker/i",$aspire_level)) {
		$asp_level = "asp-w";
		$status['asp-w'] = "<font color=green>ACTIVE</font>";
		$status['asp-h'] = $status['asp-c'] = "<font color=red>INACTIVE</font>";
		$upgrade['asp-h'] = "Upgrade Now to: <a href='/my-account/order.php?product=asp-h'>ASPIRE Hiker</a>";
		$upgrade['asp-c'] = "Upgrade Now to: <a href='/my-account/order.php?product=asp-c'>ASPIRE Hiker & Climber</a>";
	} elseif (preg_match("/hiker/i",$aspire_level)) {
		$asp_level = 'asp-h';
		$status['asp-w'] = "<font color=green>INCLUDED</font>";
		$status['asp-h'] = "<font color=green>ACTIVE</font>";
		$status['asp-c'] = "<font color=red>INACTIVE</font>";
		$upgrade['asp-c'] = "Upgrade Now to: <a href='/my-account/order.php?product=asp-c'>ASPIRE Climber</a>";
	} else {
		$asp_level = 'asp-c';
		$status['asp-w'] = $status['asp-h'] = "<font color=green>INCLUDED</font>";
		$status['asp-c'] = "<font color=green>ACTIVE</font>";
	}
	// Work out their commission level based on
	// a) Are they an active affiliate
	// b) What ASPIRE Level are they 
	// c) Do they own the product
	$comms_row['asp-w'] = $comms_row['asp-h'] = $comms_row['asp-c'] = 0;
	$comms_row['base'] = $comms_row['rise'] = $comms_row['ascend'] = $comms_row['peak'] = $comms_row['apex'] = 0;
	if ($active_aff) {
		$comms_row['asp-w'] = _GetCommissionRow($db, $_SESSION['member_id'], "asp-w", $asp_level);				
		$comms_row['asp-h'] = _GetCommissionRow($db, $_SESSION['member_id'], "asp-h", $asp_level);				
		$comms_row['asp-c'] = _GetCommissionRow($db, $_SESSION['member_id'], "asp-c", $asp_level);				
		$comms_row['base'] = _GetCommissionRow($db, $_SESSION['member_id'], "bas", $asp_level);				
		$comms_row['rise'] = _GetCommissionRow($db, $_SESSION['member_id'], "ris", $asp_level);				
		$comms_row['ascend'] = _GetCommissionRow($db, $_SESSION['member_id'], "asc", $asp_level);				
		$comms_row['peak'] = _GetCommissionRow($db, $_SESSION['member_id'], "pea", $asp_level);				
		$comms_row['apex'] = _GetCommissionRow($db, $_SESSION['member_id'], "ape", $asp_level);				
	}
	$aspire_levels = array("ASPIRE Walker" => "asp-w"
						, "ASPIRE Hiker" => "asp-h"
						, "ASPIRE Climber" => "asp-c");
	foreach($aspire_levels as $name => $aspire_level) {
		$table_rows .= "<tr>"
		. WriteTD($name)	
		. WriteTD("<b>{$status[$aspire_level]}</b>")	
		. WriteTD(_WritePercentRankTier1($db, $_SESSION['member_id'], $comms_row[$aspire_level]['tier1_pc']), TD_CENTER)
		. WriteTD(_WritePercentRank($comms_row[$aspire_level]['tier2_pc']), TD_CENTER)
		. WriteTD(_WritePercentRank($comms_row[$aspire_level]['tier3_pc']), TD_CENTER)
		. WriteTD($upgrade[$aspire_level])	
		. "</tr>";
	}
	$table_rows .= "<tr>"
	. WriteTD("&nbsp;") . WriteTD("") . WriteTH("") . WriteTH("") . WriteTH("") . WriteTD("")	
	. "</tr>";

	$table_rows .= "<thead><tr>"
	. WriteTH("Rank")
	. WriteTH("Status")	
	. WriteTH("Commission<br>Tier 1", TD_CENTER)
	. WriteTH("Tier 2", TD_CENTER)
	. WriteTH("Tier 3", TD_CENTER)
	. WriteTH("Action")	
	. "</tr></thead>";
	
	$core_products = array("base","rise","ascend","peak","apex");
	foreach($core_products as $product) {
		$access_course_link = "";
		if (in_array($product, array("base","rise")) && !empty($comms_row[$product]['own_product'])) {
			$access_course_link = "<a href='/products/$product/course'>ACCESS COURSE</a>";
		}
		$table_rows .= "<tr>"
			. WriteTD(strtoupper($product))	
			. WriteTD("<b>" . ($comms_row[$product]['own_product'] ? "<font color=green>ACTIVE</font>" : "<font color=red>INACTIVE</font>") . "<b>")	
			. WriteTD(_WritePercentRankTier1($db, $_SESSION['member_id'], $comms_row[$product]['tier1_pc']), TD_CENTER)
			. WriteTD(_WritePercentRank($comms_row[$product]['tier2_pc']), TD_CENTER)
			. WriteTD(_WritePercentRank($comms_row[$product]['tier3_pc']), TD_CENTER)
			. WriteTD($access_course_link)
			. "</tr>";
	}

/*	# List member Core Product ranks
	$query = "SELECT p.product_name,
					IF(mr.member_rank_id IS NULL, '<font color=red><b>INACTIVE</b></font>','<font color=green><b>ACTIVE<b></font>') as product_status
				FROM inf_products p 
				LEFT JOIN member_ranks mr ON mr.product_type = p.product_type 
					AND mr.member_id=$member_id
					AND mr.start_date < NOW()
					AND (mr.end_date IS NULL 
						OR mr.end_date > NOW())
				WHERE p.core_level > 0 
				ORDER by p.core_level";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$asp_note = "";
		$access_course_link = "";
		if (in_array($row['product_name'], array("BASE","RISE")) && !preg_match("/INACTIVE/",$row['product_status'])) {
			$access_course_link = "<a href='/products/".strtolower($row['product_name'])."/course'>ACCESS COURSE</a>";
		}
		$table_rows .= "<tr>"
		. WriteTD($row['product_name'])	
		. WriteTD($row['product_status'])	
//		. WriteTD($commission_level)	
		. WriteTD($access_course_link)
		. "</tr>";
	}
*/
	$table_foot = "</table>";
	$res = $table_head . $table_rows . $table_foot;
	return $res;
}

function _WritePercentRankTier1 ($db, $member_id, $num) {
	
	global $sa_pc, $calculated;
if (DEBUG) echo "calc: $calculated";
	// Only calculate it once per page load
	if (!$calculated) {
		$calculated = true;
		$sa_pc = 20;
if (DEBUG) echo "num: $num";
	
		# What custom sales assist % should we use (if any)
		$query = "SELECT *
					FROM member_log_sa sa
					WHERE sa.start_date <= NOW() 
					AND (sa.end_date IS NULL OR sa.end_date >= NOW())
					AND sa.member_id='$member_id'
					ORDER BY sa_pc
					LIMIT 1";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die($query . mysqli_error($db));
		if ($sa_row = mysqli_fetch_assoc($result)) {
			$sa_pc = $sa_row["sa_pc"];
		}
	}
	
	if ($sa_pc > 0 && $num > $sa_pc) {
		return _WritePercentRank($num) . " (-".WritePercent($sa_pc)." coach)";			
	} else {
		return _WritePercentRank($num);
	}
}

function _WritePercentRank ($num) {
	if ($num <= 3) {
		return "<font color='#777'>".WritePercent($num)."</font>";	
	} else {
		return WritePercent($num);
	}
}

# Assumes they are an Active Aff
function _GetCommissionRow($db, $member_id, $product_type, $asp_level) {
	
	// Do they own the product (or a higer one for ASPIRE
	if ($product_type == "asp-w") {
		$own_product_sql = "p.product_type IN ('asp-w','asp-h','asp-c')";
	} elseif ($product_type == "asp-h") {
		$own_product_sql = "p.product_type IN ('asp-h','asp-c')";
	} elseif ($product_type == "asp-c") {
		$own_product_sql = "p.product_type IN ('asp-c')";
	} else {
		$own_product_sql = "p.product_type = '$product_type'";		
	}

	$query = "SELECT if (mr.cp_type IS NULL, p.default_cp_type, mr.cp_type) AS cp_type 
					, if (mr.cp_type IS NULL, 0, 1) AS own_product
			FROM inf_products p 
			LEFT JOIN member_ranks mr ON mr.product_type = p.product_type 
				AND mr.member_id=$member_id
				AND mr.start_date < NOW()
				AND (mr.end_date IS NULL 
					OR mr.end_date > NOW())
			WHERE $own_product_sql
			ORDER BY p.product_order DESC
			";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	// If they own the product
	$own_product = 0;
	$cp_type = 0;
	if ($row = mysqli_fetch_assoc($result)) {
		$cp_type = $row['cp_type'];
		$own_product = $row['own_product'];
	}
	$asp_level_sql = "";
	// If it's not just going to the company
	if ($cp_type && $own_product) {
		$asp_level_sql = "AND walker = 0 AND hiker = 0 AND climber = 0";
		if ($asp_level == "asp-w") {
			$asp_level_sql = "AND walker = 1 AND hiker = 0 AND climber = 0";
		} elseif ($asp_level == "asp-h") {
			$asp_level_sql = "AND walker = 0 AND hiker = 1 AND climber = 0";
		} elseif ($asp_level == "asp-c") {
			$asp_level_sql = "AND walker = 0 AND hiker = 0 AND climber = 1";
		}
	}
	$query = "SELECT cp.*
				FROM comp_plan_v2 cp 
				WHERE cp.status = 1 
					AND cp.start_date <= NOW() 
					AND (cp.end_date IS NULL OR cp.end_date >= NOW())
					AND cp_type = $cp_type
					$asp_level_sql
					AND own_product = $own_product
				ORDER BY cp.priority
				LIMIT 1
				";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$row = mysqli_fetch_assoc($result);
	$row['own_product'] = $own_product;
	return $row;
}
?>
<?php echo MyWriteMidSection("MY RANK", "Monitor Your Affiliate/Product Ranks.",
	"Tracking your status here and keep climbing up the mountain. Set goals and reach higher everyday",
	"MY CAMPAIGNS","/my-business/my-campaigns.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-business_menu.php"); ?>
<?php echo MyWriteMainSectionTop(50); ?>
<?php echo _GetRanksTable($db, $mrow['member_id']); ?>
<br><hr>
<?php echo WriteIncludeHTML($db, "my-rank", LESSON_AUTHOR); ?>
<?php include(INCLUDES_MY."footer.php"); ?>
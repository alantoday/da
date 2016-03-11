<?php include("../includes_my/header.php"); ?>
<?php
function _GetRanksTable($db, $member_id) {
	
	$table_rows = '';

	# Determine their affiliate status
	$active_aff = WriteAffStatus($db, $member_id, $bool = true);
	if (!$active_aff) {
		$aff_color = 'red';
		$aff_upgrade = "<a href='/order.php?product=aff'>ACTIVATE NOW</a>";
	} else {
		$aff_color='green';	
		$aff_upgrade = '';
	}
	$table_head = "<table width='100%' class='daTable'><thead><tr>"
	. WriteTH("Affiliate Program")
	. WriteTH("Status")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("")	
	. "</tr></thead>";
	$table_rows .= "<tr>"
	. WriteTD("AFFILIATE")	
	. WriteTD("<font color=$aff_color><b>".($active_aff ? "ACTIVE" : "INACTIVE")."</b></font>")	
	. WriteTD($aff_upgrade)	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("")	
	. WriteTH("")	
	. "</tr>";
	$table_rows .= "<tr>"
	. WriteTD("&nbsp;") . WriteTD("")	. WriteTH("") . WriteTH("")	. WriteTH("") . WriteTD("")	
	. "</tr>";
	$table_rows .= "<thead><tr>"
	. WriteTH("ASPIRE Level")
	. WriteTH("Status")	
	. WriteTH("Commission<br>TOTAL", TD_CENTER)
	. WriteTH("Tier 1", TD_CENTER)	
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
		$upgrade['asp-w'] = "Upgrade Now to: <a href='/order.php?product=asp-w'>ASPIRE Walker</a>";
		$upgrade['asp-h'] = "Upgrade Now to: <a href='/order.php?product=asp-h'>ASPIRE Walker & Hiker</a>";
		$upgrade['asp-c'] = "Upgrade Now to: <a href='/order.php?product=asp-c'>ASPIRE Walker, Hiker & Climber</a>";
	} elseif (preg_match("/walker/i",$aspire_level)) {
		$asp_level = "asp-w";
		$status['asp-w'] = "<font color=green>ACTIVE</font>";
		$status['asp-h'] = $status['asp-c'] = "<font color=red>INACTIVE</font>";
		$upgrade['asp-h'] = "Upgrade Now to: <a href='/order.php?product=asp-h'>ASPIRE Hiker</a>";
		$upgrade['asp-c'] = "Upgrade Now to: <a href='/order.php?product=asp-c'>ASPIRE Hiker & Climber</a>";
	} elseif (preg_match("/hiker/i",$aspire_level)) {
		$asp_level = 'asp-h';
		$status['asp-w'] = "<font color=green>INCLUDED</font>";
		$status['asp-h'] = "<font color=green>ACTIVE</font>";
		$status['asp-c'] = "<font color=red>INACTIVE</font>";
		$upgrade['asp-c'] = "Upgrade Now to: <a href='/order.php?product=asp-c'>ASPIRE Climber</a>";
	} else {
		$asp_level = 'asp-c';
		$status['asp-w'] = $status['asp-h'] = "<font color=green>INCLUDED</font>";
		$status['asp-c'] = "<font color=green>ACTIVE</font>";
	}
	// Work out their commission level based on
	// a) Are they an active affiliate
	// b) What ASPIRE Level are they 
	// c) Do they own the product
	
	$comms_row['asp-w'] = $comms_row['asp-h'] = $comms_row['asp-c'] = array();
	$comms_row['base'] = $comms_row['rise'] = $comms_row['ascend'] = $comms_row['peak'] = $comms_row['apex'] = array();
	$now = date("Y-m-d H:i:s");
	$comms_row['asp-w'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "asp-w", $asp_level, $active_aff);				
	$comms_row['asp-h'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "asp-h", $asp_level, $active_aff);				
	$comms_row['asp-c'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "asp-c", $asp_level, $active_aff);				
	$comms_row['base'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "bas", $asp_level, $active_aff);
	$comms_row['rise'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "ris", $asp_level, $active_aff);
	$comms_row['ascend'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "asc", $asp_level, $active_aff);	
	$comms_row['peak'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "pea", $asp_level, $active_aff);
	$comms_row['apex'] = _GetCompPlanRow($db, $now, $_SESSION['member_id'], "ape", $asp_level, $active_aff);
	$aspire_levels = array("ASPIRE Walker" => "asp-w"
						, "ASPIRE Hiker" => "asp-h"
						, "ASPIRE Climber" => "asp-c");
	foreach($aspire_levels as $name => $aspire_level) {
		$table_rows .= "<tr>"
		. WriteTD($name)	
		. WriteTD("<b>{$status[$aspire_level]}</b>")	
		. WriteTD(_WritePercentRank($comms_row[$aspire_level]['tier1_pc'] + $comms_row[$aspire_level]['tier2_pc'] + $comms_row[$aspire_level]['tier3_pc']), TD_CENTER)
		. WriteTD(_WritePercentRank($comms_row[$aspire_level]['tier1_pc']), TD_CENTER)
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
	. WriteTH("Commission<br>TOTAL", TD_CENTER)
	. WriteTH("Tier 1", TD_CENTER)
	. WriteTH("Tier 2", TD_CENTER)
	. WriteTH("Tier 3", TD_CENTER)
	. WriteTH("Action")	
	. "</tr></thead>";
	
	$core_products = array("base","rise","ascend","peak","apex");
	foreach($core_products as $product) {
		$access_course_link = $upgrade_link = "";
		if (in_array($product, array("base","rise")) && !empty($comms_row[$product]['own_product'])) {
			$access_course_link = "<a href='/products/$product/course'>ACCESS COURSE</a>";
		} elseif (empty($comms_row[$product]['own_product'])) {
			$upgrade_link = "Upgrade Now to: <a href='/order.php?product=".substr($product,0,3)."'>".strtoupper($product)."</a>";
		}
		$table_rows .= "<tr>"
			. WriteTD(strtoupper($product))	
			. WriteTD("<b>" . ($comms_row[$product]['own_product'] ? "<font color=green>ACTIVE</font>" : "<font color=red>INACTIVE</font>") . "<b>")	
			. WriteTD(_WritePercentRankTier1($db, $_SESSION['member_id'], $comms_row[$product]['tier1_pc'] + $comms_row[$product]['tier2_pc'] + $comms_row[$product]['tier3_pc']), TD_CENTER)
			. WriteTD(_WritePercentRankTier1($db, $_SESSION['member_id'], $comms_row[$product]['tier1_pc']), TD_CENTER)
			. WriteTD(_WritePercentRank($comms_row[$product]['tier2_pc']), TD_CENTER)
			. WriteTD(_WritePercentRank($comms_row[$product]['tier3_pc']), TD_CENTER)
			. WriteTD($access_course_link.$upgrade_link)
			. "</tr>";
	}

	$table_foot = "</table>";
	$res = $table_head . $table_rows . $table_foot;
	return $res;
}

function _WritePercentRankTier1 ($db, $member_id, $num) {
	
	global $sa_pc, $calculated;

	// Only calculate it once per page load
	if (empty($calculated)) {
		$calculated = true;
		$sa_pc = 20;
	
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
		return _WritePercentRank($num) . "*"; // " (-".WritePercent($sa_pc)." coach)";			
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

# Get comp_plan
function _GetCompPlanRow($db, $date, $member_id, $product_type, $asp_level, $active_aff = 0) {
	
	// Do they own the product (or a higher one for ASPIRE
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
				AND mr.enabled = 1
				AND mr.start_date < '$date'
				AND (mr.end_date IS NULL 
					OR mr.end_date > '$date')
			WHERE $own_product_sql
			ORDER BY own_product DESC
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
	if ($active_aff) {
		// If it's not just going to the company
		if ($cp_type && $own_product) {
			$asp_level_sql = "AND walker = 0 AND hiker = 0 AND climber = 0";
			if ($asp_level == "asp-w") {
				$asp_level_sql = "AND (walker = 1 OR walker IS NULL)";
			} elseif ($asp_level == "asp-h") {
				$asp_level_sql = "AND (hiker = 1 OR hiker IS NULL)";
			} elseif ($asp_level == "asp-c") {
				$asp_level_sql = "AND (climber = 1 OR climber IS NULL)";
			}
		}
		$query = "SELECT cp.*
					FROM comp_plan_v2 cp 
					WHERE cp.status = 1 
						AND cp.start_date <= '$date' 
						AND (cp.end_date IS NULL OR cp.end_date >= '$date')
						AND cp_type = $cp_type
						AND (aff = $active_aff OR aff IS NULL)
						$asp_level_sql
						AND (own_product = $own_product OR own_product IS NULL)
					ORDER BY cp.priority
					LIMIT 1
					";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$row = mysqli_fetch_assoc($result);
	} else {
		$row['tier1_pc'] = $row['tier2_pc'] = $row['tier3_pc'] = 0;
		$row['company_pc'] = $row['sa_pc_default'] = $row['cp_tiers'] = 0;
	}
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
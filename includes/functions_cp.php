<?php
# Comp Plan Functions

require_once("functions.php");

# Returns if member was qualified in rank on $date
function CPActiveInRank($db, $member_id, $product_type, $date) {
	
	# Just in case a full product type is input eg, bas#10
	$product_type = strtok($product_type, '#');
	
	$query = "SELECT member_id
			FROM member_ranks mr
			WHERE mr.member_id = $member_id
			AND mr.product_type = '$product_type'
			AND mr.start_date <= '$date'
				AND (mr.end_date IS NULL OR mr.end_date >= '$date')";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if ($row = mysqli_fetch_assoc($result)) {
		return true;
	} else {
		return false;
	}	
}

# Keep looking up the sponsor tree until we find someone that is active in the Product Rank (on that date)
# INPUT: $sponsor_row can be $sponsor_id or $sponsor_row
# Only applied to BASE, RISE, ASCEND, etc CORE PRODUCTS
function CPGetTier1AffId($db, $sponsor_row, $product_type, $date) {
	
	# Get sponsor row details (if not passed in)
	if (!is_array($sponsor_row)) {
		$sponsor_row = GetRowMember($db, $sponsor_row);  // $sponsor_row = $sponsor_id if it's not an array	
	}
	
	$tier1_aff_id = $sponsor_row['member_id'];
	$active_in_rank = false;
	if (in_array($product_type, array("bas","ris","asc","pea","ape"))) {
		while (!$sponsor_row['top'] && !$active_in_rank) {
			$active_in_rank = CPActiveInRank($db, $sponsor_row['member_id'], $product_type, $date);	
			# Keep looking up the tree		
			$sponsor_row = GetRowMember($db, $sponsor_row['sponsor_id']);
		}
	}
	return $tier1_aff_id;
}
?>

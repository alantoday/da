<?php
$_GET['debug'] = 1;
require_once("../../includes/config.php");
require_once("../../includes/functions.php");
require_once("../../includes/functions_cp.php");

# See if the customer (email) is an existing member
//$members = array(102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119);
$members = array(126,127,128);
foreach($members as $member_id) {
	$product_types = array("asp","bas","ris");
	foreach ($product_types as $product_type) {
		$query = "INSERT INTO member_ranks
					SET member_id = '$member_id' 
					, product_type='$product_type'
				, start_date='2010-10-01'";
//					, cp_type='$cp_type'
			$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);	
	}
}
echo "Done";
?>
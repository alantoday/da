<?
require_once("includes/config.php");
require_once("includes/functions.php");

define ("DEBUG", 1);

function GetCommissions($member_id) {
		
	# List commissions earned by a member
	$query = "SELECT t.create_date, m.member_id, m.name, p.product_name, t.order_name, c.*
				FROM commissions c
				JOIN transactions t USING (trans_id)
				JOIN members m ON m.member_id = t.member_id
				JOIN products p ON p.product_type = c.product_type
				WHERE $member_id IN (c.sa_aff_id, c.tier1_aff_id, c.tier2_aff_id, c.tier3_aff_id)";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$table_rows = false;
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD(WriteDate($row['create_date']))	
		. WriteTD($row['member_id'], TD_RIGHT)	
		. WriteTD($row['name'])	
		. WriteTD($row['product_name'])	
		. WriteTD(WriteDollars($row['trans_amt']), TD_RIGHT)	
		. WriteTD(WriteDollars($row['tier1_amt']), TD_RIGHT)	
		. WriteTD(WriteDollars($row['tier1_amt']), TD_RIGHT)	
		. WriteTD(WriteDollars($row['tier1_amt']), TD_RIGHT)	
		. "</tr>";
	}
	
	if (!$table_rows) {
		$res['success'] = 0;
		$res['data'] = "There are no matching records results";
	} else {
		$table_header = "<tr>"
		. WriteTH("#")	
		. WriteTH("Order Date")	
		. WriteTH("Member ID", TD_RIGHT)	
		. WriteTH("Member Name")	
		. WriteTH("Product")	
		. WriteTH("Trans Amt", TD_RIGHT)	
		. WriteTH("Tier 1", TD_RIGHT)	
		. WriteTH("Tier 2", TD_RIGHT)	
		. WriteTH("Tier 3", TD_RIGHT)	
		. "</tr>";
		$res['success'] = 1;
		$res['data'] = '<link rel="stylesheet" type="text/css" href="http://digialti.com/css/style.css">';
		$res['data'] .= "<table width='800px' class='daTable'>";
		$res['data'] .= $table_header;
		$res['data'] .= $table_rows;
		$res['data'] .= "</table>";
	}
	return $res;
}

?>

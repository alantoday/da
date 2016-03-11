<?
#ini_set('max_execution_time', 400);
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
/*
$email = $_GET['email'];
$order_by = empty($_GET['order_by']) ? '' : $order_by='A to Z';
$status = empty($_GET['status']) ? '' : $status='Hide';

$from_date_placeholder =  WriteDate("last week");
$to_date_placeholder = WriteDate("today");

#Default values
if (empty($_GET['from_date'])) {
	$_GET['from_date'] = $from_date_placeholder;
}
if (empty($_GET['to_date'])) {
	$_GET['to_date'] = $to_date_placeholder;
}
*/
?>

<? /*
<form action="stats.php" id="tree_form" method="GET">
<input type="hidden" name="email" value="<?=$email?>" />
<table cellpadding="0px" cellspacing="0px" border=0>
	<tr>
		<td align="right"><b>From Date:</b> </td>
		<td><input type="text" name='from_date' placeholder="<?=$from_date_placeholder?>" value="<?=$_GET['from_date'];?>"></td>
		<td align="right">  &nbsp;   &nbsp;   &nbsp; <b>To Date:</b> </td>
		<td><input type="text" name='to_date' placeholder="<?=$to_date_placeholder?>" value="<?=$_GET['to_date'];?>"></td>
		<td colspan="2" align="right">  &nbsp;   &nbsp; 
        <input class="btn" id="" type="submit" name="submit" value="Search" style="font-size:13px">
	</tr>
</table>
</fieldset>
</form>
*/ ?>
<?
echo GetCommissionsTable($db, $email_username);


function GetCommissionsTable($db, $email_username) {
		
	# List commissions earned by a member
	$query = "SELECT m_aff.member_id as member_id_aff, t.create_date, m_order.member_id as member_id_order, m_order.name, p.product_name, t.order_name, c.*
				FROM commissions c
				JOIN transactions t USING (trans_id)
				JOIN members m_order ON m_order.member_id = t.member_id
				JOIN members m_aff ON m_aff.member_id IN (c.sa_aff_id, c.tier1_aff_id, c.tier2_aff_id, c.tier3_aff_id)
				LEFT JOIN products p ON p.product_type = c.product_type
				WHERE m_aff.email_username = '$email_username'";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$table_rows = '';
	$totals['trans_amt'] = 0;
	$totals['tier1_amt'] = 0;
	$totals['tier2_amt'] = 0;
	$totals['tier3_amt'] = 0;
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$tier1_amt = ($row['tier1_aff_id']==$row['member_id_aff']) ? WriteDollarCents($row['tier1_amt']) : WriteNum(0);
		$tier2_amt = ($row['tier2_aff_id']==$row['member_id_aff']) ? WriteDollarCents($row['tier2_amt']) : WriteNum(0);
		$tier3_amt = ($row['tier3_aff_id']==$row['member_id_aff']) ? WriteDollarCents($row['tier3_amt']) : WriteNum(0);
		$totals['trans_amt'] += $row['trans_amt'];
		$totals['tier1_amt'] += $row['tier1_amt'];
		$totals['tier2_amt'] += $row['tier2_amt'];
		$totals['tier3_amt'] += $row['tier3_amt'];
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD(WriteDate($row['create_date']))	
#		. WriteTD($row['member_id_order'], TD_RIGHT)	
		. WriteTD($row['name'])	
		. WriteTD($row['product_name'])	
		. WriteTD(WriteDollarCents($row['trans_amt']), TD_RIGHT)	
		. WriteTD($tier1_amt, TD_RIGHT)	
		. WriteTD($tier2_amt, TD_RIGHT)	
		. WriteTD($tier3_amt, TD_RIGHT)	
		. "</tr>";
	}
	if (!$table_rows) {
		$table_rows = "<tr><td colspan='8'><i><font color=grey>You have not earned any commissions so far</font></i></td></tr>";
	}
	$table_header = "<thead><tr>"
	. WriteTH("#")	
	. WriteTH("Order Date")	
#	. WriteTH("Member ID", TD_RIGHT)	
	. WriteTH("Member Name")	
	. WriteTH("Product")	
	. WriteTH("Trans Amt", TD_RIGHT)	
	. WriteTH("Tier 1", TD_RIGHT)	
	. WriteTH("Tier 2", TD_RIGHT)	
	. WriteTH("Tier 3", TD_RIGHT)	
	. "</tr></thead>";
	$table_footer = "<tfoot><tr><th colspan='4'>Totals:</th>"
	. WriteTH(WriteDollarCents($totals['trans_amt']), TD_RIGHT)	
	. WriteTH(WriteDollarCents($totals['tier1_amt']), TD_RIGHT)	
	. WriteTH(WriteDollarCents($totals['tier2_amt']), TD_RIGHT)	
	. WriteTH(WriteDollarCents($totals['tier3_amt']), TD_RIGHT)	
	."</tr></tfoot>";
	$res = "<table width='600px' class='daTable'>";
	$res .= $table_header;
	$res .= $table_rows;
	$res .= $table_footer;
	$res .= "</table>";
	
	return $res;
}
?>
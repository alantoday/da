<?php

require_once('config.php');
require_once('functions.php');


########################################################################
# Returns Member's Earning History
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_comms.php?debug=1&CommsWriteEarningsTable=1
if (isset($_GET['CommsWriteEarningsTable'])) {
	WriteArray(CommsWriteEarningsTable(GETRESPONSE_API_KEY));
}
 
function CommsWriteEarningsTable($db, $member_id, $from_date, $to_date, $coach = false, $min_pay_amt = 0) {
	
	$member_sql = '';
	if ($member_id != 'All') {
		$member_sql = "AND m_aff.member_id = '$member_id'";
	}
	$min_pay_sql = '';
	if ($min_pay_amt) {
		$min_pay_sql = "AND ABS(p.pay_amt) >= $min_pay_amt";
	}
		
	# List commissions earned by a member
	$query = "SELECT m_aff.member_id as member_id_aff, m_order.member_id as member_id_order, m_order.name, pd.product_name, c.*
				FROM commissions c
				JOIN inf_payments p USING (inf_payment_id)
				JOIN members m_order ON m_order.inf_contact_id = p.inf_contact_id
				JOIN members m_aff ON m_aff.member_id IN (c.tier1_aff_id, c.tier1_up_aff_id, c.tier2_aff_id, c.tier3_aff_id, c.sa_aff_id)
				LEFT JOIN inf_products pd ON pd.product_type = c.product_type
				WHERE substring(p.pay_date,1,10) BETWEEN '$from_date' AND '$to_date'
				$member_sql
				$min_pay_sql
				ORDER BY p.pay_date DESC";  
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	$totals['trans_amt'] = 0;
	$totals['tier1_amt'] = 0;
	$totals['tier1_up_amt'] = 0;
	$totals['tier2_amt'] = 0;
	$totals['tier3_amt'] = 0;
	$totals['sa_amt'] = 0;
	$totals['total_amt'] = 0;
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$tier1_amt = ($row['tier1_aff_id']==$row['member_id_aff']) ? $row['tier1_amt'] : 0;
		$tier1_up_amt = ($row['tier1_up_aff_id']==$row['member_id_aff']) ? $row['tier1_up_amt'] : 0;
		$tier2_amt = ($row['tier2_aff_id']==$row['member_id_aff']) ? $row['tier2_amt'] : 0;
		$tier3_amt = ($row['tier3_aff_id']==$row['member_id_aff']) ? $row['tier3_amt'] : 0;
		$sa_amt = ($row['sa_aff_id']==$row['member_id_aff']) ? $row['sa_amt'] : 0;
		$total_amt = $tier1_amt + $tier1_up_amt + $tier2_amt + $tier3_amt;
		$totals['trans_amt'] += $row['trans_amt']+$row['fee_amt'];
		$totals['tier1_amt'] += $tier1_amt;
		$totals['tier1_up_amt'] += $tier1_up_amt;
		$totals['tier2_amt'] += $tier2_amt;
		$totals['tier3_amt'] += $tier3_amt;
		$totals['sa_amt'] += $sa_amt;
		$totals['total_amt'] += $total_amt;
		$sa_row = "";
		if ($coach) $sa_row = WriteTD(WriteDollarCents($sa_amt), TD_RIGHT);	
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD(WriteDate($row['trans_date']))	
		. WriteTD($row['name'])	
		. WriteTD($row['product_name'])	
		. WriteTD(WriteDollarCents($row['trans_amt']+$row['fee_amt']), TD_RIGHT)	
		. WriteTD(WriteDollarCents($tier1_amt+$tier1_up_amt), TD_RIGHT)	
		. WriteTD(WriteDollarCents($tier2_amt), TD_RIGHT)	
		. WriteTD(WriteDollarCents($tier3_amt), TD_RIGHT)
		. $sa_row
		. WriteTD("<b>".WriteDollarCents($total_amt)."<b>", TD_RIGHT)	
		. "</tr>";
	}
	$sa_th = "";
	if ($coach) $sa_th = WriteTH("<br>Coach", TD_RIGHT);	
	$table_head = "<table width='100%' class='daTable'><thead><tr>"
	. WriteTH("#")	
	. WriteTH("Order Date")	
	. WriteTH("Customer")	
	. WriteTH("Product")	
	. WriteTH("Payment", TD_RIGHT, 'padding: 5px 5px;')	
	. WriteTH("Commission<br>Tier 1", TD_RIGHT)	
	. WriteTH("<br>Tier 2", TD_RIGHT)
	. WriteTH("<br>Tier 3", TD_RIGHT)
	. $sa_th
	. WriteTH("TOTAL", TD_RIGHT)	
	. "</tr></thead>";
	$sa_td = "";
	if ($coach) $sa_td = WriteTD("<b>".WriteDollarCents($totals['sa_amt']."</b>"), TD_RIGHT);

	$table_foot = "<tfoot><tr>"
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD("<b>".WriteDollarCents($totals['trans_amt']."</b>"), TD_RIGHT)	
		. WriteTD("<b>".WriteDollarCents($totals['tier1_amt']+$totals['tier1_up_amt']."</b>"), TD_RIGHT)	
		. WriteTD("<b>".WriteDollarCents($totals['tier2_amt']."</b>"), TD_RIGHT)	
		. WriteTD("<b>".WriteDollarCents($totals['tier3_amt']."</b>"), TD_RIGHT)
		. $sa_td	
		. WriteTD("<b>".WriteDollarCents($totals['total_amt']."</b>"), TD_RIGHT)	
		. "</tr></tfoot></table>";
	$table_style = "<style>
	.daTable .daTableClear th, td {
    	padding: 5px 5px;
	}
	</style>";
	if (!$table_rows) {
		$res = "You have not earned any commissions within that date range.";
	} else {
		$res = $table_style . $table_head . $table_rows . $table_foot;
	}
	
	return $res;
}

########################################################################
# Returns Member's Earning History
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_comms.php?debug=1&CommsWriteEarningsTableAll=1
if (isset($_GET['CommsWriteEarningsTableAll'])) {
	WriteArray(CommsWriteEarningsTableAll(GETRESPONSE_API_KEY));
}

function CommsWriteEarningsTableAll($db, $member_id, $from_date, $to_date, $coach = false, $min_pay_amt = 0) {
	
	$member_sql = '';
	if ($member_id != 'All') {
		$member_sql = "AND m_aff.member_id = '$member_id'";
	}
	$min_pay_sql = '';
	if ($min_pay_amt) {
		$min_pay_sql = "AND ABS(p.pay_amt) >= $min_pay_amt";
	}
		
	# List commissions earned by a member
	$query = "SELECT m_order.member_id as member_id_order, m_order.name
					, m_tier1.username AS tier1_username, m_tier1.name AS tier1_name
					, m_tier2.username AS tier2_username, m_tier2.name AS tier2_name
					, m_tier3.username AS tier3_username, m_tier3.name AS tier3_name
					, pd.product_name, c.*
				FROM commissions c
				JOIN inf_payments p USING (inf_payment_id)
				JOIN members m_order ON m_order.inf_contact_id = p.inf_contact_id
				JOIN members m_tier1 ON m_order.sponsor_id = m_tier1.member_id
				JOIN members m_tier2 ON m_tier1.sponsor_id = m_tier2.member_id
				JOIN members m_tier3 ON m_tier2.sponsor_id = m_tier3.member_id
				LEFT JOIN inf_products pd ON pd.product_type = c.product_type
				WHERE substring(p.pay_date,1,10) BETWEEN '$from_date' AND '$to_date'
				$member_sql
				$min_pay_sql
				ORDER BY p.pay_date DESC";  
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	$totals['trans_amt'] = 0;
	$totals['tier1_amt'] = 0;
	$totals['tier1_up_amt'] = 0;
	$totals['tier2_amt'] = 0;
	$totals['tier3_amt'] = 0;
	$totals['sa_amt'] = 0;
	$totals['total_amt'] = 0;
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$tier1_amt = $row['tier1_amt'];
		$tier1_up_amt = $row['tier1_up_amt'];
		$tier2_amt = $row['tier1_amt'];
		$tier3_amt = $row['tier1_amt'];
		$sa_amt = $row['sa_amt'];
		$total_amt = $tier1_amt + $tier1_up_amt + $tier2_amt + $tier3_amt;
		$totals['trans_amt'] += $row['trans_amt']+$row['fee_amt'];
		$totals['tier1_amt'] += $tier1_amt;
		$totals['tier1_up_amt'] += $tier1_up_amt;
		$totals['tier2_amt'] += $tier2_amt;
		$totals['tier3_amt'] += $tier3_amt;
		$totals['sa_amt'] += $sa_amt;
		$totals['total_amt'] += $total_amt;
		$sa_row = "";
		if ($coach) $sa_row = WriteTD(WriteDollarCents($sa_amt), TD_RIGHT);	
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD(WriteDate($row['trans_date']))	
		. WriteTD($row['name'])	
		. WriteTD(WriteNotesPopup($row['tier1_username'], $row['tier1_name']))	
		. WriteTD(WriteNotesPopup($row['tier2_username'], $row['tier2_name']))	
		. WriteTD(WriteNotesPopup($row['tier3_username'], $row['tier3_name']))	
		. WriteTD($row['product_name'])	
		. WriteTD(WriteDollarCents($row['trans_amt']+$row['fee_amt']), TD_RIGHT)	
		. WriteTD(WriteDollarCents($tier1_amt+$tier1_up_amt), TD_RIGHT)	
		. WriteTD(WriteDollarCents($tier2_amt), TD_RIGHT)	
		. WriteTD(WriteDollarCents($tier3_amt), TD_RIGHT)
		. $sa_row
		. WriteTD("<b>".WriteDollarCents($total_amt)."<b>", TD_RIGHT)	
		. "</tr>";
	}
	$sa_th = "";
	if ($coach) $sa_th = WriteTH("<br>Coach", TD_RIGHT);	
	$table_head = "<table width='100%' class='daTable'><thead><tr>"
	. WriteTH("#")	
	. WriteTH("Order Date")	
	. WriteTH("Customer")	
	. WriteTH("Tier 1")	
	. WriteTH("Tier 2")	
	. WriteTH("Tier 3")	
	. WriteTH("Product")	
	. WriteTH("Payment", TD_RIGHT, 'padding: 5px 5px;')	
	. WriteTH("Commission<br>Tier 1", TD_RIGHT)	
	. WriteTH("<br>Tier 2", TD_RIGHT)
	. WriteTH("<br>Tier 3", TD_RIGHT)
	. $sa_th
	. WriteTH("TOTAL", TD_RIGHT)	
	. "</tr></thead>";
	$sa_td = "";
	if ($coach) $sa_td = WriteTD("<b>".WriteDollarCents($totals['sa_amt']."</b>"), TD_RIGHT);

	$table_foot = "<tfoot><tr>"
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD("<b>".WriteDollarCents($totals['trans_amt']."</b>"), TD_RIGHT)	
		. WriteTD("<b>".WriteDollarCents($totals['tier1_amt']+$totals['tier1_up_amt']."</b>"), TD_RIGHT)	
		. WriteTD("<b>".WriteDollarCents($totals['tier2_amt']."</b>"), TD_RIGHT)	
		. WriteTD("<b>".WriteDollarCents($totals['tier3_amt']."</b>"), TD_RIGHT)
		. $sa_td	
		. WriteTD("<b>".WriteDollarCents($totals['total_amt']."</b>"), TD_RIGHT)	
		. "</tr></tfoot></table>";
	$table_style = "<style>
	.daTable .daTableClear th, td {
    	padding: 5px 5px;
	}
	</style>";
	if (!$table_rows) {
		$res = "You have not earned any commissions within that date range.";
	} else {
		$res = $table_style . $table_head . $table_rows . $table_foot;
	}
	
	return $res;
}


########################################################################
# Returns Payout History Table
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_comms.php?debug=1&CommsWritePayoutTable=1
if (isset($_GET['CommsWritePayoutTable'])) {
	WriteArray(CommsWritePayoutTable(GETRESPONSE_API_KEY));
}

function CommsWritePayoutTable($db, $member_id, $from_date, $to_date, $min_payout_amt = 0) {
		
	$member_sql = '';
	if ($member_id != 'All') {
		$member_sql = "AND p.member_id = '$member_id'";
	}
	$min_payout_sql = '';
	if ($min_payout_amt) {
		$min_payout_sql = "AND ABS(p.payout_amt) >= $min_payout_amt";
	}

	# List commissions earned by a member
	$query = "SELECT p.*
				FROM payouts p
				WHERE substring(p.payout_date,1,10) BETWEEN '$from_date' AND '$to_date'
				$member_sql
				$min_payout_sql
				ORDER BY p.payout_date DESC";  
	#EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	$totals['trans_amt'] = 0;
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$totals['trans_amt'] += $row['payout_amount'];
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD(WriteDate($row['payout_date']))	
		. WriteTD(WriteDollarCents($row['payout_amount']), TD_RIGHT)	
		. WriteTD($row['notes'])	
		. "</tr>";
	}
	$table_head = "<table width='100%' class='daTable'><thead><tr>"
	. WriteTH("#")	
	. WriteTH("Paytout Date")	
	. WriteTH("Amount")	
	. WriteTH("Notes")	
	. "</tr></thead>";
	$table_foot = "<tfoot><tr>"
		. WriteTD('')	
		. WriteTD('')	
		. WriteTD("<b>".WriteDollarCents($totals['trans_amt']."</b>"), TD_RIGHT)	
		. "</tr></tfoot></table>";
	if (!$table_rows) {
		$res = "You have not recieved any earnings payouts within that date range.";
	} else {
		$res = $table_head . $table_rows . $table_foot;
	}
	
	return $res;
}


########################################################################
# Returns Commission Summary Table
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_comms.php?debug=1&CommsWriteSummaryTable=1
if (isset($_GET['CommsWriteSummaryTable'])) {
	WriteArray(CommsWriteSummaryTable(GETRESPONSE_API_KEY));
}

function CommsWriteSummaryTable ($db, $member_id) {

	$member_sql = '1';
	if ($member_id != 'All') {
		$member_sql = "member_id = '$member_id'";
	}

	$query = "SELECT create_date, SUM(commissions) AS Total 
				FROM commissions_daily 
				WHERE $member_sql
				GROUP BY create_date 
				ORDER by create_date DESC";  
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	
	$comm_array['today'] = "0.00";
	$comm_array['yesterday'] = "0.00";
	$comm_array['last_7days'] = "0.00";
	$comm_array['last_30days'] = "0.00";
	$comm_array['last_365days'] = "0.00";
	$comm_array['all_time'] = "0.00";
	
	while($crow = mysqli_fetch_assoc($result)){
		if($crow['create_date'] == date("Y-m-d")) $comm_array['today'] = $crow['Total'];
		if($crow['create_date'] == date("Y-m-d", strtotime("-1 day"))) $comm_array['yesterday'] = $crow['Total'];
		if($crow['create_date'] >= date("Y-m-d", strtotime("-7 day"))) $comm_array['last_7days'] += $crow['Total'];
		if($crow['create_date'] >= date("Y-m-d", strtotime("-30 day"))) $comm_array['last_30days'] += $crow['Total'];
		if($crow['create_date'] >= date("Y-m-d", strtotime("-365 day"))) $comm_array['last_365days'] += $crow['Total'];
		$comm_array['all_time'] += $crow['Total'];
	}
	return "
<style>
	.summary_td{
		background:#303030;
		width:16.5%;
		text-align:center;
		border-left:1px solid #fff;
		padding:10px 10px 5px 10px;
		color:#fff;
		font-weight:bold;
		font-size:20px;
	}
	.summary_td_mute{
		font-size:13px;
	}
</style>
<table width='100%'>
	<tr>
		<td class='summary_td'>".WriteDollarCents($comm_array['today'])."<br><span class='summary_td_mute'>Today</span></td>
		<td class='summary_td'>".WriteDollarCents($comm_array['yesterday'])."<br><span class='summary_td_mute'>Yesterday</span></td>
		<td class='summary_td'>".WriteDollarCents($comm_array['last_7days'])."<br><span class='summary_td_mute'>Last 7 Days</span></td>
		<td class='summary_td'>".WriteDollarCents($comm_array['last_30days'])."<br><span class='summary_td_mute'>Last 30 Days</span></td>
		<td class='summary_td'>".WriteDollarCents($comm_array['last_365days'])."<br><span class='summary_td_mute'>Last 365 Days</span></td>
		<td class='summary_td'>".WriteDollarCents($comm_array['all_time'])."<br><span class='summary_td_mute'>All Time</span></td>
	</tr>
</table>
";
}
?>

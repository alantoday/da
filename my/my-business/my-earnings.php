<?php 
include("../includes_my/header.php"); 
include_once(PATH."includes/functions_comms.php");
?>
<?php 
$query = "SELECT create_date, SUM(commissions) AS Total 
			FROM commissions_daily 
			WHERE member_id = '{$_SESSION['member_id']}'
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

function _GetCommissionsTable($db, $member_id, $from_date, $to_date, $coach = false) {
		
	# List commissions earned by a member
	$query = "SELECT m_aff.member_id as member_id_aff, m_order.member_id as member_id_order, m_order.name, pd.product_name, c.*
				FROM commissions c
				JOIN inf_payments p USING (inf_payment_id)
				JOIN members m_order ON m_order.inf_contact_id = p.inf_contact_id
				JOIN members m_aff ON m_aff.member_id IN (c.tier1_aff_id, c.tier1_up_aff_id, c.tier2_aff_id, c.tier3_aff_id, c.sa_aff_id)
				LEFT JOIN inf_products pd ON pd.product_type = c.product_type
				WHERE m_aff.member_id = '$member_id'
				AND substring(p.pay_date,1,10) BETWEEN '$from_date' AND '$to_date'
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

function _GetPayoutTable($db, $member_id, $from_date, $to_date) {
		
	# List commissions earned by a member
	$query = "SELECT p.*
				FROM payouts p
				WHERE p.member_id = '$member_id'
				AND substring(p.payout_date,1,10) BETWEEN '$from_date' AND '$to_date'
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
?>

<?php echo MyWriteMidSection("MY EARNINGS", "Monitor Your Commissions Here",
	"Tracking your earnings here and keep climbing up the mountain. Set goals and reach higher everyday.",
	"MY CAMPAIGNS","/my-marketing/my-campaigns.php",
	"MY TEAM", "/my-business/my-team.php"); ?>

<?php include("my-business_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>
<?php
#Default values
$from_date_placeholder =  WriteDateFlat("3 months ago");
$to_date_placeholder = WriteDateFlat("today");
if (empty($_GET['from_date'])) $_GET['from_date'] = $from_date_placeholder;
if (empty($_GET['to_date'])) $_GET['to_date'] = $to_date_placeholder;
?>
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
<table width="100%">
	<tr>
		<td class='summary_td'><?=WriteDollarCents($comm_array['today'])?><br><span class="summary_td_mute">Today</span></td>
		<td class='summary_td'><?=WriteDollarCents($comm_array['yesterday'])?><br><span class="summary_td_mute">Yesterday</span></td>
		<td class='summary_td'><?=WriteDollarCents($comm_array['last_7days'])?><br><span class="summary_td_mute">Last 7 Days</span></td>
		<td class='summary_td'><?=WriteDollarCents($comm_array['last_30days'])?><br><span class="summary_td_mute">Last 30 Days</span></td>
		<td class='summary_td'><?=WriteDollarCents($comm_array['last_365days'])?><br><span class="summary_td_mute">Last 365 Days</span></td>
		<td class='summary_td'><?=WriteDollarCents($comm_array['all_time'])?><br><span class="summary_td_mute">All Time</span></td>
	</tr>
</table>
<br>
<form method="GET">
<table>
	<tr>
		<td align="right"><b>From Date:</b></td>
		<td><input type="text" id="from_date" name='from_date' placeholder="<?=$from_date_placeholder?>" value="<?=$_GET['from_date'];?>"></td>
		<td align="right">  &nbsp;   &nbsp;   &nbsp; <b>To Date:</b></td>
		<td><input type="text" id="to_date" name='to_date' placeholder="<?=$to_date_placeholder?>" value="<?=$_GET['to_date'];?>"></td>
		<td colspan="2" align="right">  &nbsp;   &nbsp; 
        <input class="btn" id="" type="submit" name="submit" value="Search">
	</tr>
</table>
</fieldset>
</form>
<script>
$(function() {
	$( "#from_date" ).datepicker({
  		dateFormat: "M dd, yy"
	});	
	$( "#to_date" ).datepicker({
	  	dateFormat: "M dd, yy"
	});
});
</script>
<?php
$db_from_date = WriteDBDate($_GET['from_date']);	
$db_to_date = WriteDBDate($_GET['to_date']);	
echo "<hr>";
?>
<script>
$(function() {
	$( "#tabs" ).tabs();
});
</script>
<div id="tabs">
	<ul>
		<li><a href="#tabs-comms">My Commmissions</a></li>
		<li><a href="#tabs-payouts">My Payouts</a></li>    
	</ul>
	<div id="tabs-comms">
		<?php echo _GetCommissionsTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date, $mrow['coach']); ?>
        <br>
	</div>
	<div id="tabs-payouts">
		<?php echo _GetPayoutTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date); ?>
        <br>
	</div>
</div>

<?php include(INCLUDES_MY."footer.php"); ?>
<?php include("../includes_my/header.php"); ?>

<?php echo MyWriteMidSection("My Campaigns", "Your Marketing Campaigns",
	"Keep an eye on your climb up the mountain with campaign stats, commissions and your genealogy. Set goals and reach higher everyday...",
	"MY CAMPAIGNS","/my-marketing/my-campaigns.php",
	"MY EARNINGS", "/my-business/my-earnings.php"); ?>

<?php include("my-marketing_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php
#Default values
$from_date_placeholder =  WriteDateFlat("last week");
$to_date_placeholder = WriteDateFlat("today");
if (empty($_GET['from_date'])) $_GET['from_date'] = $from_date_placeholder;
if (empty($_GET['to_date'])) $_GET['to_date'] = $to_date_placeholder;
?>
<form action="my-campaigns.php" id="tree_form" method="GET">
<table cellpadding="0px" cellspacing="0px" border=0>
	<tr>
		<td align="right"><b>From Date:</b></td>
		<td><input type="text" name='from_date' id="from_date" placeholder="<?=$from_date_placeholder?>" value="<?=$_GET['from_date'];?>"></td>
		<td align="right">  &nbsp;   &nbsp;   &nbsp; <b>To Date:</b></td>
		<td><input type="text" name='to_date' id="to_date" placeholder="<?=$to_date_placeholder?>" value="<?=$_GET['to_date'];?>"></td>
		<td colspan="2" align="right">  &nbsp;   &nbsp; 
        <input class="btn" id="" type="submit" name="submit" value="Search">
	</tr>
</table>
</fieldset>
</form>
<?php
$db_from_date = WriteDBDate($_GET['from_date']);	
$db_to_date = WriteDBDate($_GET['to_date']);	
?>
<script>
$( "#from_date" ).datepicker({
  dateFormat: "M dd, yy"
});
$( "#to_date" ).datepicker({
  dateFormat: "M dd, yy"
});


$(function() {
	$( "#tabs" ).tabs();
});
</script>
<br>
<div id="tabs">
	<ul>
		<li><a href="#tabs-leads">My Leads</a></li>
		<li><a href="#tabs-adv">My Conversions</a></li>
		<li><a href="#tabs-detail">My Page Views</a></li>    
		<li><a href="#tabs-tag">What is "Tag"?</a></li>    
	</ul>
	<div id="tabs-leads">
        <br>
		<?php echo _GetLeadsTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date); ?>
	</div>
	<div id="tabs-adv">
    	<br>
		<?php echo _GetTrackingStatsTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date); ?>
	</div>
	<div id="tabs-detail">
		<p style="padding: 15px 0px 10px;">See how many people are visiting each of your sales funnel pages for each ad link using a TAG</p>
		<?php echo _GetStatsTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date);?>	
	</div>
	<div id="tabs-tag">
		<p>Use a different "Tag" for each of your marketing campaigns so that you can track which are performing better than others.</p>
        <p>Track each separate ad link by including &t=TAG to the end of your link, eg, http://aspir.link/cp1?da=<?php echo $mrow['username']; ?>&amp;t=BANNER2</p>
	</div>
</div>	
<script type="text/javascript">
$('#tabs').css('opacity', 0);
$(window).load(function() {
	$('#tabs').css('opacity', 1);
});
</script>
<?
function _GetLeadsTable($db, $member_id, $db_from_date, $db_to_date) {
	
	$query = "SELECT l.*, u.url_host, u.url_path
				FROM leads l
				LEFT JOIN urls u USING (url_id)
				WHERE l.member_id = '{$_SESSION['member_id']}'
				AND substring(l.create_date,1,10) BETWEEN '$db_from_date' AND '$db_to_date'
				ORDER BY l.create_date DESC";            
#	EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$table_rows .= "<tr>"
		. WriteTD($i)
		. WriteTD(WriteDate($row['create_date']))	
		. WriteTD($row['email'])
		. WriteTD(WriteURL("http://".$row['url_host'].$row['url_path']))
		. WriteTD($row['t'])
		. WriteTD(WriteNotesPopup($row['ip'], WriteIPCountry($row['ip'])))
		. "</tr>";
	}
	if (!$table_rows) {
		$table_rows = "<tr><td colspan='20'><i><font color=grey>You have no leads for that date range.</font></i></td></tr>";
	}
	
	$table_header = "<thead><tr>"
	. WriteTH("#")	
	. WriteTH("Date")	
	. WriteTH("Email")
	. WriteTH("Sales Page")	
	. WriteTH("Tag")	
	. WriteTH("IP")	
	. "</tr></thead>";
	$res = "<table width='100%' class='daTable'>";
	$res .= $table_header;
	$res .= $table_rows;
	$res .= "</table>";
	return $res;
}

function _GetStatsTable($db, $member_id, $db_from_date, $db_to_date) {
	
	# List commissions earned by a member
	$query = "SELECT v.t, SUM(v.visits) as visits
					, SUM(v.unique_visits) AS unique_visits
					, u.url_host, u.url_path
				FROM visit_stats v
				LEFT JOIN members m ON m.sponsor_id = v.member_id AND m.t = v.t
				LEFT JOIN leads l ON l.member_id = v.member_id AND l.t = v.t
				LEFT JOIN urls u ON u.url_id=v.url_id
				WHERE v.member_id = '$member_id'
				AND v.create_date BETWEEN '$db_from_date' AND '$db_to_date'
				GROUP BY v.t, u.url_host, u.url_path
				ORDER BY v.t, u.url_host, u.url_path";            
#	EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		if ($row['url_host']=="") {
			$url = "Unknown";
		} else {
			$url = "<a target='_blank' href='http://{$row['url_host']}{$row['url_path']}'><font color=blue>{$row['url_host']}{$row['url_path']}<font></a>";
		}
		$table_rows .= "<tr>"
		. WriteTD($i)
		. WriteTD($row['t'])	
		. WriteTD($url)	
		. WriteTD($row['visits'], TD_CENTER)	
		. WriteTD($row['unique_visits'], TD_CENTER)
		. "</tr>";
	}
	if (!$table_rows) {
		$table_rows = "<tr><td colspan='20'><i><font color=grey>You have not sent any traffic to your unique marketing links yet.</font></i></td></tr>";
	}
	
	$table_header = "<thead><tr>"
	. WriteTH("#")	
	. WriteTH("Tag")	
	. WriteTH("Page")	
	. WriteTH("Visits", TD_CENTER)	
	. WriteTH("Unique Visits", TD_CENTER)
	. "</tr></thead>";
	$res = "<table width='100%' class='daTable'>";
	$res .= $table_header;
	$res .= $table_rows;
	$res .= "</table>";
	return $res;
}

function _GetTrackingStatsTable($db, $member_id, $db_from_date, $db_to_date) {
	
	# List commissions earned by a member
	$query = "SELECT v.t
					, SUM(v.visits) as visits
					, SUM(v.unique_visits) AS unique_visits
					, COUNT(l.lead_id) AS leads
					, COUNT(DISTINCT m.member_id) AS members
				FROM visit_stats v
				LEFT JOIN members m ON m.sponsor_id = v.member_id AND m.t = v.t AND m.create_date BETWEEN '$db_from_date' AND '$db_to_date'
				LEFT JOIN leads l ON l.member_id = v.member_id AND l.t = v.t AND l.create_date BETWEEN '$db_from_date' AND '$db_to_date'
				WHERE v.member_id = '$member_id'
				AND v.create_date BETWEEN '$db_from_date' AND '$db_to_date'
				GROUP BY v.t
				ORDER BY v.t";            
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD($row['t'])	
		. WriteTD(WriteNum($row['visits']), TD_CENTER)	
		. WriteTD(WriteNum($row['unique_visits']), TD_CENTER)
		. WriteTD(WriteNum($row['leads']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
/*		. WriteTD(WriteNum($row['members']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
		. WriteTD(WriteNum($row['members']), TD_CENTER)
*/		. "</tr>";
	}
	if (!$table_rows) {
		$table_rows = "<tr><td colspan='20'><i><font color=grey>You have not sent any traffic to your unique marketing links yet.</font></i></td></tr>";
	}
	
	$table_header = "<thead><tr>"
	. WriteTH("#")	
	. WriteTH("Tag")	
	. WriteTH("Visits", TD_CENTER)	
	. WriteTH("Unique Visits", TD_CENTER)	
	. WriteTH("Leads", TD_CENTER)
	. WriteTH("Members", TD_CENTER)	
/*	. WriteTH("AS-W", TD_CENTER)	
	. WriteTH("AS-H", TD_CENTER)	
	. WriteTH("AS-C", TD_CENTER)	
	. WriteTH("BA", TD_CENTER)	
	. WriteTH("RI", TD_CENTER)	
	. WriteTH("AC", TD_CENTER)	
	. WriteTH("PE", TD_CENTER)	
	. WriteTH("AP", TD_CENTER)	
*/	. "</tr></thead>";
	$res = "<table width='100%' class='daTable'>";
	$res .= $table_header;
	$res .= $table_rows;
	$res .= "</table>";
	return $res;
}
?>

<?php include(INCLUDES_MY."footer.php"); ?>
<?php
#ini_set('max_execution_time', 400);
require_once("../includes/config.php");
require_once("../includes/functions.php");

if (empty($_GET['email'])) {
	echo "Missing: email";
	exit();
}

$email = $_GET['email'];
$order_by = empty($_GET['order_by']) ? '' : $order_by='A to Z';
$status = empty($_GET['status']) ? '' : $status='Hide';

# Get all transactions to process
$query = "SELECT *
			FROM members
			WHERE email_username = '$email'";            
$result = mysqli_query($db, $query) or die(mysqli_error($db));
if ($row = mysqli_fetch_assoc($result)) {
	$member_id = $row['member_id'];
} else {
	echo ERROR_MISSING_MEMBER;	
	exit();
}

$from_date_placeholder =  WriteDate("last week");
$to_date_placeholder = WriteDate("today");

#Default values
if (empty($_GET['from_date'])) {
	$_GET['from_date'] = $from_date_placeholder;
}
if (empty($_GET['to_date'])) {
	$_GET['to_date'] = $to_date_placeholder;
}
?>
<link rel="stylesheet" type="text/css" href="http://digialti.com/css/style.css">

<form action="get_stats.php" id="tree_form" method="GET">
<input type="hidden" name="email" value="<?=$email?>" />
<table cellpadding="0px" cellspacing="0px" border=0>
	<tr>
		<td align="right"><b>From Date:</b></td>
		<td><input type="text" name='from_date' placeholder="<?=$from_date_placeholder?>" value="<?=$_GET['from_date'];?>"></td>
		<td align="right">  &nbsp;   &nbsp;   &nbsp; <b>To Date:</b></td>
		<td><input type="text" name='to_date' placeholder="<?=$to_date_placeholder?>" value="<?=$_GET['to_date'];?>"></td>
		<td colspan="2" align="right">  &nbsp;   &nbsp; 
        <input class="btn" id="" type="submit" name="submit" value="Search" style="font-size:13px">
	</tr>
</table>
</fieldset>
</form>
<?
echo GetStatsTable($db, $email);


function GetStatsTable($db, $email) {
	
	$db_from_date = WriteDBDate($_GET['to_date']);	
	$db_to_date = WriteDBDate($_GET['to_date']);	
	# List commissions earned by a member
	$query = "SELECT v.*, u.url_host, u.url_path
				FROM visit_stats v
				JOIN members m_aff USING (member_id)
				LEFT JOIN members m ON m.sponsor_id = m.member_id AND m.t = v.t
				LEFT JOIN urls u on u.url_id=v.url_id
				WHERE m_aff.email_username = '$email'
				AND v.create_date BETWEEN '$db_from_date' AND '$db_to_date'
				GROUP BY v.t
				ORDER BY v.t, u.url_host, u.url_path";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$table_rows = '';
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$table_rows .= "<tr>"
		. WriteTD($i)	
		. WriteTD(WriteDate($row['create_date']))	
		. WriteTD("<a target='_blank' href='http://{$row['url_host']}{$row['url_path']}'>{$row['url_host']}{$row['url_path']}</a>")	
		. WriteTD($row['t'])	
		. WriteTD($row['visits'], TD_RIGHT)	
		. WriteTD($row['unique_visits'], TD_RIGHT)
		. "</tr>";
	}
	if (!$table_rows) {
		$table_rows = "<tr><td colspan='8'><i><font color=grey>You have not sent any traffic so far to your unique marketing links yet.</font></i></td></tr>";
	}
	
	$table_header = "<thead><tr>"
	. WriteTH("#")	
	. WriteTH("Date")	
	. WriteTH("Page")	
	. WriteTH("Tracking")	
	. WriteTH("Visits", TD_RIGHT)	
	. WriteTH("Unique Visits", TD_RIGHT)	
	. "</tr></thead>";
	$res = "<table width='625px' class='daTable'>";
	$res .= $table_header;
	$res .= $table_rows;
	$res .= "</table>";
	return $res;
}
?>
<?php include("../includes_my/header.php"); ?>

<?php echo MyWriteMidSection("MY LEADS", "Track New Prospects",
	"Watch as new prospects enter their email address to learn more",
	"MY CAMPAIGNS","/my-marketing/my-campaigns.php",
	"MY EARNINGS", "/my-business/my-earnings.php"); ?>
<?php include("my-marketing_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php
#Default values
$from_date_placeholder =  WriteDateFlat("1 month ago");
$to_date_placeholder = WriteDateFlat("today");
if (empty($_GET['from_date'])) $_GET['from_date'] = $from_date_placeholder;
if (empty($_GET['to_date'])) $_GET['to_date'] = $to_date_placeholder;
?>
<form method="GET">
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
echo _GetLeadsTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date);

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
?>
<?php include(INCLUDES_MY."footer.php"); ?>
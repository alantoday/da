<?php include("../includes_my/header.php"); ?>

<?php echo MyWriteMidSection("MY TEAM", "Monitor Your Team's Duplication",
	"Keep an eye on your team's climb up the mountain. Reach out to them and give them a hand up.",
	"MY CAMPAIGNS","/my-business/my-campaigns.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-business_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php
#Default values
$from_date_placeholder =  WriteDateFlat("last year");
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
        <input class="btn" id="" type="submit" name="submit" value="Search"> &nbsp; <a href="/my-business/my-tree.php">View My Tree</a>
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
echo _GetTeamTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date);

function _GetTeamTable($db, $member_id, $db_from_date, $db_to_date) {
	
	# List commissions earned by a member
	$query = "SELECT m.*, GROUP_CONCAT(DISTINCT mr.product_type) as products
                    FROM members m
					LEFT JOIN member_ranks mr ON mr.member_id = m.member_id
						AND mr.enabled = 1
						AND (mr.end_date > NOW() OR mr.end_date IS NULL)
                    WHERE m.sponsor_id = '{$_SESSION['member_id']}'
					GROUP BY member_id
        			ORDER BY create_date DESC";            
#	EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	for($i=1; $row = mysqli_fetch_assoc($result); $i++) {

		$aspire_level = '<font color=grey>-</font>';
		if (preg_match("/asp-c/",$row['products'])) $aspire_level = 'Climber';
		elseif (preg_match("/asp-h/",$row['products'])) $aspire_level = 'Hiker';
		elseif (preg_match("/asp-w/",$row['products'])) $aspire_level = 'Walker';

		$core_level = '<font color=grey>-</font>';
		if (preg_match("/ape/",$row['products'])) $core_level = 'APEX';
		elseif (preg_match("/pea/",$row['products'])) $core_level = 'PEAK';
		elseif (preg_match("/asc/",$row['products'])) $core_level = 'ASCEND';
		elseif (preg_match("/ris/",$row['products'])) $core_level = 'RISE';
		elseif (preg_match("/bas/",$row['products'])) $core_level = 'BASE';

		$table_rows .= "<tr>"
		. WriteTD($i)
		. WriteTD(WriteDate($row['create_date']))	
		. WriteTD($row['username'])	
		. WriteTD($row['name'])	
		. WriteTD($row['email'])
		. WriteTD($row['t'])
		. WriteTD(WriteNum(WriteStepNumber($row['steps_completed'])))
		. WriteTD(WriteYesDash(preg_match("/aff/",$row['products'])), TD_CENTER)
		. WriteTD($aspire_level, TD_CENTER)
		. WriteTD($core_level, TD_CENTER)
		. "</tr>";
	}
	if (!$table_rows) {
		$table_rows = "<tr><td colspan='20'><i><font color=grey>You have no new team members for that date range.</font></i></td></tr>";
	}
	
	$table_header = "<thead><tr>"
	. WriteTH("#")	
	. WriteTH("Join Date")	
	. WriteTH("Username")	
	. WriteTH("Name")	
	. WriteTH("Email")
	. WriteTH("Tag")
	. WriteTH("Step")
	. WriteTH("Affiliate")
	. WriteTH("ASPIRE", TD_CENTER)	
	. WriteTH("Core Level", TD_CENTER)	
	. "</tr></thead>";
	$res = "<table width='100%' class='daTable'>";
	$res .= $table_header;
	$res .= $table_rows;
	$res .= "</table>";
	return $res;
}
?>
<?php include(INCLUDES_MY."footer.php"); ?>
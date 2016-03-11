<?php
include_once("../../includes/config.php");
include_once(PATH."includes/functions.php");
include_once(PATH."includes/functions_comms.php");
include_once(PATH."admin/includes_admin/include_menu.php");
?>
<?php 
$from_date_placeholder =  WriteDateFlat("3 months ago");
$to_date_placeholder = WriteDateFlat("today");
if (empty($_GET['from_date'])) $_GET['from_date'] = $from_date_placeholder;
if (empty($_GET['to_date'])) $_GET['to_date'] = $to_date_placeholder;
?>
<h1 id="page_title">My Earnings</h1>

<?php echo CommsWriteSummaryTable($db, $_SESSION['member_id']); ?>
<br />
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
		<?php echo CommsWriteEarningsTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date); ?>
        <br>
	</div>
	<div id="tabs-payouts">
		<?php echo CommsWritePayoutTable($db, $_SESSION['member_id'], $db_from_date, $db_to_date); ?>
        <br>
	</div>
</div>

<?php
include_once("../../includes/config.php");
include_once(PATH."includes/functions.php");
include_once(PATH."includes/functions_comms.php");
include_once(PATH."admin/includes_admin/include_menu.php");

$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : "";
$search_str = isset($_GET['search_str']) ? $_GET['search_str'] : "";
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : "";
$contains = isset($_GET['contains']) ? $_GET['contains'] : "";
$limit = isset($_GET['limit']) ? $_GET['limit'] : "25";
$min_amt = isset($_GET['min_amt']) ? $_GET['min_amt'] : 0;
$submit = isset($_GET['submit']) ? $_GET['submit'] : "";
?>

<h1 id="page_title">Search Commissions...</h1>
<p><font color=red>Not all search feature work just yet</font></p>

<?php
$search_str=trim($search_str);
if ($submit) {
		
	$sponsor_join_sql = '';
	#############################################################################
	# PAYMENTS
	
	if ( $search_type=="payments") { 
			  
		echo "<h2>Infusionsoft: Payments</h2>";

		if (empty($search_str)) $search_field = "";
		 
		switch($search_field) {
			case "Username": 		$search_sql = "m.username = '$search_str'"; break;
			case "Email": 			$search_sql = "m.email = '$search_str'"; break;
			case "Member ID": 		$search_sql = "m.member_id = '$search_str'"; break;
			case "Inf Contact Id":	$search_sql = "m.inf_contact_id = '$search_str'"; break;
			case "Inf Invoice Id":	$search_sql = "p.inf_invoice_id = '$search_str'"; break;
			case "Sponsor Username": 
				$search_sql = "s.username = '$search_str'";
				$sponsor_join_sql = 'LEFT JOIN members s ON m.sponsor_id = s.member_id';
				break;
			default: $search_sql = "1";
		}
		$sql_limit = ($limit != "All") ? "LIMIT $limit" : "";

		$min_amt_sql = "";
		if ($min_amt != 'All') {
			$min_amt_sql = "AND ABS(p.pay_amt) >= $min_amt";
		}

		$query = "SELECT c.*, m.username, m.name, m.member_id
					FROM commissions c
					LEFT JOIN members m USING (inf_contact_id)
					$sponsor_join_sql
					WHERE $search_sql
					$min_amt_sql
					ORDER BY p.pay_date DESC
					$sql_limit";
		$actions = "";
		if (in_array($mrow['admin_security_id'], array(ACCESS_ADMIN,ACCESS_SUPERADMIN))) {
			$actions = WriteTH("Actions", TD_CENTER);
		}
	
		if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}
}

#Default values
$from_date_placeholder =  WriteDateFlat("7 days ago");
$to_date_placeholder = WriteDateFlat("today");
if (empty($_GET['from_date'])) $_GET['from_date'] = $from_date_placeholder;
if (empty($_GET['to_date'])) $_GET['to_date'] = $to_date_placeholder;
?>

<?php echo CommsWriteSummaryTable($db, 'All'); ?>
<br>
<form method="GET">
<table>
	<tr>
		<td align="right">From:</td>
		<td><input type="text" id="from_date" name='from_date' placeholder="<?=$from_date_placeholder?>" value="<?=$_GET['from_date'];?>" style="width:85px;"></td>
		<td align="right">To:</td>
		<td><input type="text" id="to_date" name='to_date' placeholder="<?=$to_date_placeholder?>" value="<?=$_GET['to_date'];?>" style="width:85px;"></td>
		<td>Records: <select name='limit'><?php echo WriteSelect($limit,array(10,25,50,100,500,'All'))?></select>
		<td>Min Amt: <select name='min_amt'><?php echo WriteSelect($min_amt,array('All',array(10=>"$10"),array(100=>"$100"),array(500=>"$500"),array(1000=>"$1,000"),array(5000=>"$5,000"),array(10000=>"$10,000")))?></select></td>
		<td>Search: <select name="search_field">
			<?php
            $search_field_options = array("Username", "Member Id", "Email", "Inf Contact Id", "Inf Invoice Id", "Sponsor Username");
            echo WriteSelect($search_field, $search_field_options, false, false);
            ?>
    		</select></td>
    	<td><input type="text" name="search_str" placeholder="Search value" value="<?php echo stripslashes($search_str)?>" />
		<td colspan="2" align="right"><input class="btn" id="" type="submit" name="submit" value="Search"></td>
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
		<li><a href="#tabs-comms">All Commmissions</a></li>
		<li><a href="#tabs-payouts">All Payouts</a></li>    
	</ul>
	<div id="tabs-comms">
		<?php echo CommsWriteEarningsTableAll($db, 'All', $db_from_date, $db_to_date, true, $min_amt); ?>
        <br>
	</div>
	<div id="tabs-payouts">
		<?php echo CommsWritePayoutTable($db, 'All', $db_from_date, $db_to_date, $min_amt); ?>
        <br>
	</div>
</div>
<?php include("../includes_my/header.php"); ?>
<?php include(PATH."/includes/functions_inf.php"); ?>
<?php
if (isset($_POST['action'])) {
	
}
?>
<?php echo MyWriteMidSection("MY ORDERS", "Review and Manage Your Orders",
	"Review completed order and manage pending orders",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<?php
#Default values
$from_date_placeholder =  WriteDateFlat("1 year ago");
$to_date_placeholder = WriteDateFlat("today");
if (empty($_GET['from_date'])) $_GET['from_date'] = $from_date_placeholder;
if (empty($_GET['to_date'])) $_GET['to_date'] = $to_date_placeholder;
?>
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

$unpaid_orders = _WriteUnPaidOrders($db, $mrow['inf_contact_id']);
$paid_orders = _WritePaidOrders($db, $mrow['member_id'], $db_from_date, $db_to_date);
if (!$unpaid_orders) {
	echo "<hr>".$paid_orders;
} else {
?>
<script>
$(function() {
	$( "#tabs" ).tabs();
});
</script>
<br>
<div id="tabs">
	<ul>
		<li><a href="#tabs-unpaid">ACTION REQUIRED</a></li>
		<li><a href="#tabs-paid">History</a></li>
	</ul>
	<div id="tabs-unpaid">
		<?php echo $unpaid_orders ?>
	</div>
	<div id="tabs-paid">
		<?php echo $paid_orders; ?>
	</div>
</div>	
<script type="text/javascript">
$('#tabs').css('opacity', 0);
$(window).load(function() {
	$('#tabs').css('opacity', 1);
});
</script>
<?php } ?>

<?php 
function _WritePaidOrders($db, $member_id, $db_from_date, $db_to_date) {
	$query = "SELECT i.*
					, pd.inf_product_id, i.product_sold, pay_amt, i.* , p.pay_date, p.pay_amt, p.pay_type
					, GROUP_CONCAT(' ', pd.product_name) AS products
				FROM inf_payments p 
				LEFT JOIN inf_invoices i USING (inf_invoice_id) 
				LEFT JOIN inf_order_items ioi ON ioi.inf_order_id = i.inf_invoice_id 
				LEFT JOIN inf_products pd ON pd.inf_product_id = ioi.inf_product_id
				LEFT JOIN members m on m.inf_contact_id = p.inf_contact_id 
				WHERE member_id = $member_id
				AND DATE(p.pay_date) BETWEEN '$db_from_date' AND '$db_to_date' 
				GROUP BY p.inf_payment_id
				ORDER BY p.pay_date DESC
				";        
	if (DEBUG) EchoLn($query);
	$table_head = "<table width='100%' class='daTable'><thead><tr>"
	. WriteTH("#")
	. WriteTH("Date")	
	. WriteTH("Product(s)")	
	. WriteTH("Amount", TD_RIGHT)	
	. WriteTH("Payment")	
#	. WriteTH("Payment Status")	
#	. WriteTH("Notes")	
	. "</tr></thead>";
	$table_rows = '';
	$table_foot = "</table>";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	for($i=1; $row = mysqli_fetch_assoc($result); $i++){
		if ($row['refund_status']==2) {
			$status = "Voided";	
		} elseif ($row['refund_status']) {
			$status = "Refunded";	
		} else {
			$status = "-";			
		}
		$table_rows .= "<tr>"
		. WriteTD($i)
		. WriteTD(WriteDate($row['pay_date']))	
		. WriteTD(str_replace(",","<br>",$row['products']))	
		. WriteTD(WriteDollarCents($row['pay_amt']), TD_RIGHT)	
		. WriteTD($row['pay_type'])	
#		. WriteTD($row['pay_status'])	
#		. WriteTD("Payment")	
		. "</tr>";
	}
	if ($i>1) {
		return $table_head . $table_rows. $table_foot;
	} else {
		return "<font color=grey>No records found.</font>";
	}
}

function _WriteUnPaidOrders($db, $inf_contact_id) {
	$invoices_array = InfGetInvoices($inf_contact_id, false);
	#WriteArray($invoices_array);
	
	$table_head = "<table width='100%' class='daTable'><thead><tr>"
	. WriteTH("#")
	. WriteTH("Date")	
	. WriteTH("Product(s)")	
	. WriteTH("Price", TD_RIGHT)	
	. WriteTH("Total Due", TD_RIGHT)	
	. WriteTH("Action")	
	. "</tr></thead>";
	$table_rows = '';
	$table_foot = "</table>";
	$i=0;
	foreach($invoices_array as $inf_invoice_id => $invoice_details) {
		$i++;
		
		$table_rows .= "<tr>"
		. WriteTD($i)
		. WriteTD(WriteDate($invoice_details['DateCreated']))	
		. WriteTD(str_replace(",","<br>",WriteProductNames($db, $invoice_details['ProductSold'])))	
		. WriteTD(WriteDollarCents($invoice_details['InvoiceTotal']), TD_RIGHT)	
		. WriteTD("<font color=red>".WriteDollarCents($invoice_details['TotalDue']-$invoice_details['TotalPaid'])."</font>", TD_RIGHT)	
		. WriteTD("<a href='/my-account/payments.php?invoice=$inf_invoice_id' style='color:#2e82bc;'>Payment Instructions</a>")	
		. "</tr>";
	}
	if ($i>1) {
		return $table_head . $table_rows. $table_foot;
	} else {
		return false;
	}
}
?>
<?php include(INCLUDES_MY."footer.php"); ?>

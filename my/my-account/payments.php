<?php include_once("../../includes/functions_inf.php"); ?>
<?php
if (empty($_SESSION['member_id'])) {
	header("location: /?action=logout&pg=".urlencode($_SERVER['REQUEST_URI']));	
} 
?>
<?php include("../includes_my/header.php"); ?>
<?
if (!empty($_GET['invoice'])) { 
	#Validate that it's one of this member's invoices
	if ($invoice_details = InfGetInvoice($mrow['inf_contact_id'], $_GET['invoice'])) {
		$table_head = "<table width='100%' class='daTable'><thead><tr>"
		. WriteTH("Order Date")	
		. WriteTH("Product(s)")	
		. WriteTH("Order Total", TD_RIGHT)	
		. WriteTH("Total Due", TD_RIGHT)	
		. "</tr></thead>";
		$table_rows = '';
		$table_foot = "</table>";
		$table_rows .= "<tr>"
		. WriteTD(WriteDate($invoice_details['DateCreated']))	
		. WriteTD(str_replace(",","<br>",WriteProductNames($db, $invoice_details['ProductSold'])))	
		. WriteTD(WriteDollarCents($invoice_details['InvoiceTotal']), TD_RIGHT)	
		. WriteTD("<b><font color=red>".WriteDollarCents($invoice_details['TotalDue']-$invoice_details['TotalPaid'])."</font></b>", TD_RIGHT)	
		. "</tr>";
		$invoice_table = $table_head . $table_rows. $table_foot ."<br>";
	} else {
		$error[] = "The input Invoice Id '{$_GET['invoice']}' does not appear to be valid for you";
	}
}

?>
<?php echo MyWriteMainSectionTop(30); ?>
<?php if (!empty($_GET['note'])) echo "<b><font color='#339933' size='+1'>".$_GET['note']."</font></b><br><br>"; ?>

<div style="float:right"><a style="text-decoration:none;" href="javascript:void(0);" onclick="window.print();"><img src="/images/icons/icon_print.png" border=0 align="middle"> Print this page</a></div>

<p align=left style="font-size:45px;font-weight:300px;margin-bottom:20px;line-height:66px;font-family:da-font;color:#123">
		Payment Instructions</p>
<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>
<?php if (!empty($invoice_table)) echo $invoice_table; ?>

<script>
$(function() {
	$( "#tabs" ).tabs();
});
</script>
<br>
<div id="tabs">
	<ul>
		<li><a href="#tabs-wire">Wire/Direct Deposit Payments</a></li>
		<li><a href="#tabs-check">Check Payments</a></li>
	</ul>
	<div id="tabs-wire">
        <br>
		<?php echo WriteIncludeHTML($db, "payments_wire", LESSON_AUTHOR); ?>
	</div>
	<div id="tabs-check">
    	<br>
		<?php echo WriteIncludeHTML($db, "payments_check", LESSON_AUTHOR); ?>
	</div>
</div>	
<script type="text/javascript">
$('#tabs').css('opacity', 0);
$(window).load(function() {
	$('#tabs').css('opacity', 1);
});
</script>

<?php $HIDE_FOOTER_IMG = true; ?>
<?php include(INCLUDES_MY."footer.php"); ?>

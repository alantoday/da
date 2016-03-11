<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../includes_admin/include_menu.php");

$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : "";
$search_str = isset($_GET['search_str']) ? $_GET['search_str'] : "";
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : "";
$contains = isset($_GET['contains']) ? $_GET['contains'] : "";
$limit = isset($_GET['limit']) ? $_GET['limit'] : "25";
$min_amt = isset($_GET['min_amt']) ? $_GET['min_amt'] : "";
$submit = isset($_GET['submit']) ? $_GET['submit'] : "";
?>

<h1 id="page_title">Search Payments...</h1>

<form method="GET">
<table><tr>
<td>
    Search By:
    <select name="search_field">
    <?php
    $search_field_options = array("Username", "Member Id", "Email", "Inf Contact Id", "Inf Invoice Id", "Sponsor Username");
    echo WriteSelect($search_field, $search_field_options, false, false);
    ?>
    </select>
    <input type="text" name="search_str" placeholder="Search value" value="<?php echo stripslashes($search_str)?>" />
</td><td>
    Records: <select name='limit'><?php echo WriteSelect($limit,array(10,25,50,100,500,'All'))?></select>
</td>
<td>
    Min Amt: 
        <select name='min_amt'><?php echo WriteSelect($min_amt,array('All',array(10=>"$10"),array(100=>"$100"),array(500=>"$500"),array(1000=>"$1,000"),array(5000=>"$5,000"),array(10000=>"$10,000")))?></select>
</td><td>
<?php echo WriteButton("Search");?>
</td></tr>
</table>
</form>

<?php
$search_str=trim($search_str);
if ($submit) {
		
	$sponsor_join_sql = '';
	#############################################################################
	# PAYMENTS
	
	if ( $search_type=="payments" || 1) { 
			  
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

		$query = "SELECT p.*, m.username, m.name, m.member_id
					FROM inf_payments p
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
		#EchoLn($query);
		$table_head = "<table width='600px' class='daTable'><thead><tr>"
		. WriteTH("#")
#		. WriteTH("Contact Id")	
		. WriteTH("Username")	
		. WriteTH("Pmt Id")	
		. WriteTH("Pay Date")	
		. WriteTH("Inv. Id")	
		. WriteTH("Pay Amt")	
		. WriteTH("Pay Type")	
		. WriteTH("Pay Note")	
		. WriteTH("Refund Id")	
		. WriteTH("Charge Id", TD_RIGHT)	
		. WriteTH("Synced")	
		. WriteTH("Sync Date")	
		. WriteTH("Mem Rank Upd", TD_CENTER)	
		. $actions	
		. "</tr></thead>";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$table_rows = '';
		$table_foot = "</table>";
		$delete_option = "";
		for($i=1; $row = mysqli_fetch_assoc($result); $i++){
			if (in_array($mrow['admin_security_id'], array(ACCESS_ADMIN,ACCESS_SUPERADMIN))) {
				$delete_option = WriteTD("<a target='_blank' href='http://da.digitalaltitude.co/inf/get_payments.php?inf_invoice_id={$row['inf_invoice_id']}'>Reload</a>");
			}
			$table_rows .= "<tr>"
			. WriteTD($i)
#			. WriteTD($row['inf_contact_id'], TD_RIGHT)	
			. WriteTD(WriteNotesPopup($row['username'],$row['member_id'].": ".$row['name']))	
			. WriteTD($row['inf_payment_id'], TD_RIGHT)	
			. WriteTD(WriteDate($row['pay_date']))	
			. WriteTD($row['inf_invoice_id'], TD_RIGHT)	
			. WriteTD(WriteDollarCents($row['pay_amt']), TD_RIGHT)	
			. WriteTD($row['pay_type'])	
			. WriteTD(WriteNotesLong($row['pay_note'], 20))	
			. WriteTD($row['refund_id'], TD_RIGHT)	
			. WriteTD($row['charge_id'], TD_RIGHT)	
			. WriteTD(WriteYesNo($row['synced']), TD_CENTER)	
			. WriteTD(WriteDate($row['create_date']))	
			. WriteTD(WriteYesNo($row['member_rank_updated']), TD_CENTER)
			. $delete_option	
			. "</tr>";
		}
		if ($i>1) {
			echo $table_head . $table_rows. $table_foot;
		} else {
			echo "<font color=grey>No records found.</font>";
		}

		if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}
	
	#############################################################################
	# INVOICES	
	if ( $search_type=="Invoices" || 1) { 
	
		echo "<h2>Infusionsoft: Invoices</h2>";
			  
		switch($search_field) {
			case "Username": 		$search_sql = "m.username = '$search_str'"; break;
			case "Email": 			$search_sql = "m.email = '$search_str'"; break;
			case "Member ID": 		$search_sql = "m.member_id = '$search_str'"; break;
			case "Inf Contact Id":	$search_sql = "m.inf_contact_id = '$search_str'"; break;
			case "Inf Invoice Id":	$search_sql = "i.inf_invoice_id = '$search_str'"; break;
			case "Sponsor Username": 
				$search_sql = "s.username = '$search_str'";
				$sponsor_join_sql = 'LEFT JOIN members s ON m.sponsor_id = s.member_id';
				break;
			default: $search_sql = "1";
		}
		$sql_limit = ($limit != "All") ? "LIMIT $limit" : "";
		$min_amt_sql = "";
		if ($min_amt != 'All') {
			$min_amt_sql = "AND ABS(i.invoice_total) >= $min_amt";
		}

		$query = "SELECT i.*, m.username
					FROM inf_invoices i
					LEFT JOIN members m USING (inf_contact_id)
					$sponsor_join_sql
					WHERE $search_sql
					$min_amt_sql
					ORDER BY i.date_created DESC
					$sql_limit";
		#EchoLn($query);
		$table_head = "<table width='600px' class='daTable'><thead><tr>"
		. WriteTH("#")	
#		. WriteTH("Contact<br>Id", TD_RIGHT)	
		. WriteTH("Username")	
		. WriteTH("Inv. Id", TD_RIGHT)	
		. WriteTH("Date<br>Created")	
		. WriteTH("Job<br>Id", TD_RIGHT)	
		. WriteTH("Invoice<br>Total", TD_RIGHT)	
		. WriteTH("Total<br>Paid", TD_RIGHT)	
		. WriteTH("Total<br>Due", TD_RIGHT)	
		. WriteTH("Pay<br>Status", TD_CENTER)	
		. WriteTH("Credit<br>Status", TD_CENTER)	
		. WriteTH("Refund<br>Status", TD_CENTER)	
		. WriteTH("Pay Plan<br>Status", TD_CENTER)	
		. WriteTH("Aff<br>Id", TD_RIGHT)	
		. WriteTH("Lead<br>Aff Id", TD_RIGHT)	
		. WriteTH("Promo<br>Code")	
		. WriteTH("Invoice<br>Type")	
		. WriteTH("Description")	
		. WriteTH("Product<br>Sold", TD_RIGHT)	
		. WriteTH("Synced")	
		. WriteTH("Sync Date")	
		. "</tr></thead>";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$table_rows = '';
		$table_foot = "</table>";
		for($i=1; $row = mysqli_fetch_assoc($result); $i++){
			$table_rows .= "<tr>"
			. WriteTD($i)	
#			. WriteTD($row['inf_contact_id'], TD_RIGHT)	
			. WriteTD($row['username'])	
			. WriteTD($row['inf_invoice_id'], TD_RIGHT)	
			. WriteTD(WriteDate($row['date_created']))	
			. WriteTD($row['inf_job_id'], TD_RIGHT)	
			. WriteTD(WriteDollarCents($row['invoice_total']), TD_RIGHT)	
			. WriteTD(WriteDollarCents($row['total_paid']), TD_RIGHT)	
			. WriteTD(WriteDollarCents($row['total_due']), TD_RIGHT)	
			. WriteTD(WriteYesNo($row['pay_status']), TD_CENTER)	
			. WriteTD(WriteYesNo($row['credit_status']), TD_CENTER)	
			. WriteTD(WriteYesNo($row['refund_status']), TD_CENTER)	
			. WriteTD(WriteYesNo($row['pay_plan_status']), TD_CENTER)	
			. WriteTD($row['inf_affiliate_id'], TD_RIGHT)	
			. WriteTD($row['lead_affiliate_id'], TD_RIGHT)	
			. WriteTD($row['promo_code'])	
			. WriteTD($row['invoice_type'])	
			. WriteTD(WriteNotesLong($row['description'], 20))	
			. WriteTD($row['product_sold'], TD_RIGHT)	
			. WriteTD(WriteYesNo($row['synced']), TD_CENTER)	
			. WriteTD(WriteDate($row['create_date']))	
			. "</tr>";
		}
		if ($i>1) {
			echo $table_head . $table_rows. $table_foot;
		} else {
			echo "<font color=grey>No records found.</font>";
		}

		if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}

	#############################################################################
	# INVOICES ITEMS
	if ( $search_type=="Invoice Items" || 1) { 
	
		echo "<h2>Infusionsoft: Invoices Items</h2>";
			  
		switch($search_field) {
			case "Username": 		$search_sql = "m.username = '$search_str'"; break;
			case "Email": 			$search_sql = "m.email = '$search_str'"; break;
			case "Member ID": 		$search_sql = "m.member_id = '$search_str'"; break;
			case "Inf Contact Id":	$search_sql = "m.inf_contact_id = '$search_str'"; break;
			case "Inf Invoice Id":	$search_sql = "i.inf_invoice_id = '$search_str'"; break;
			case "Sponsor Username": 
				$search_sql = "s.username = '$search_str'";
				$sponsor_join_sql = 'LEFT JOIN members s ON m.sponsor_id = s.member_id';
				break;
			default: $search_sql = "1";
		}
		$sql_limit = ($limit != "All") ? "LIMIT $limit" : "";
		$min_amt_sql = "";
		if ($min_amt != 'All') {
			$min_amt_sql = "AND ABS(i.invoice_total) >= $min_amt";
		}

		$query = "SELECT ii.*, m.username
					FROM inf_invoice_items ii
					LEFT JOIN inf_invoices i USING (inf_invoice_id)
					LEFT JOIN members m USING (inf_contact_id)
					$sponsor_join_sql
					WHERE $search_sql
					$min_amt_sql
					ORDER BY ii.date_created DESC
					$sql_limit";        
		#EchoLn($query);
		$table_head = "<table width='600px' class='daTable'><thead><tr>"
		. WriteTH("#")	
		. WriteTH("Username")	
		. WriteTH("Inv. Id")	
		. WriteTH("Date Created")	
		. WriteTH("Date Modified")	
		. WriteTH("Order Item Id")	
		. WriteTH("Invoice Amt")	
		. WriteTH("Description")	
		. WriteTH("Commission Status")	
		. "</tr></thead>";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$table_rows = '';
		$table_foot = "</table>";
		for($i=1; $row = mysqli_fetch_assoc($result); $i++){
			$table_rows .= "<tr>"
			. WriteTD($i)	
			. WriteTD($row['username'])	
			. WriteTD($row['inf_invoice_id'], TD_RIGHT)	
			. WriteTD(WriteDate($row['date_created']))	
			. WriteTD(WriteDate($row['date_modified']))	
			. WriteTD($row['inf_order_item_id'], TD_RIGHT)	
			. WriteTD(WriteDollarCents($row['invoice_amt']), TD_RIGHT)	
			. WriteTD(WriteNotesLong($row['description'], 20))	
			. WriteTD(WriteYesNo($row['commission_status']), TD_CENTER)	
			. "</tr>";
		}
		if ($i>1) {
			echo $table_head . $table_rows. $table_foot;
		} else {
			echo "<font color=grey>No records found.</font>";
		}

		if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}

	#############################################################################
	# INVOICE PAYMENTS
	if ( $search_type=="Invoice Payments" || 1) { 
	
		echo "<h2>Infusionsoft: Payment Items</h2>";
			  
		switch($search_field) {
			case "Username": 		$search_sql = "m.username = '$search_str'"; break;
			case "Email": 			$search_sql = "m.email = '$search_str'"; break;
			case "Member ID": 		$search_sql = "m.member_id = '$search_str'"; break;
			case "Inf Contact Id":	$search_sql = "m.inf_contact_id = '$search_str'"; break;
			case "Inf Invoice Id":	$search_sql = "i.inf_invoice_id = '$search_str'"; break;
			case "Sponsor Username": 
				$search_sql = "s.username = '$search_str'";
				$sponsor_join_sql = 'LEFT JOIN members s ON m.sponsor_id = s.member_id';
				break;
			default: $search_sql = "1";
		}
		$sql_limit = ($limit != "All") ? "LIMIT $limit" : "";
		$min_amt_sql = "";
		if ($min_amt != 'All') {
			$min_amt_sql = "AND ABS(i.invoice_total) >= $min_amt";
		}

		$query = "SELECT ip	.*, m.username
					FROM inf_invoice_payments ip
					LEFT JOIN inf_invoices i USING (inf_invoice_id)
					LEFT JOIN members m USING (inf_contact_id)
					$sponsor_join_sql
					WHERE $search_sql
					$min_amt_sql
					ORDER BY ip.pay_date DESC
					$sql_limit";        
		#EchoLn($query);
		$table_head = "<table width='600px' class='daTable'><thead><tr>"
		. WriteTH("#")	
		. WriteTH("Username")	
		. WriteTH("Inv. Id")	
		. WriteTH("Pay Date")	
		. WriteTH("Amt")	
		. WriteTH("Pay Status")	
		. WriteTH("Payment Id")	
		. WriteTH("Skip Commission")	
		. WriteTH("Sync Date")	
		. "</tr></thead>";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$table_rows = '';
		$table_foot = "</table>";
		for($i=1; $row = mysqli_fetch_assoc($result); $i++){
			$table_rows .= "<tr>"
			. WriteTD($i)
			. WriteTD($row['username'])	
			. WriteTD($row['inf_invoice_id'], TD_RIGHT)	
			. WriteTD(WriteDate($row['pay_date']))	
			. WriteTD(WriteDollarCents($row['amt']), TD_RIGHT)	
			. WriteTD($row['pay_status'])	
			. WriteTD($row['inf_payment_id'], TD_RIGHT)	
			. WriteTD(WriteYesNo($row['skip_commission']), TD_CENTER)	
			. WriteTD(WriteDate($row['create_date']))	
			. "</tr>";
		}
		if ($i>1) {
			echo $table_head . $table_rows. $table_foot;
		} else {
			echo "<font color=grey>No records found.</font>";
		}

		if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}	
	
	#############################################################################
	# ORDER ITEMS
	if ( $search_type=="Order Items" || 1) { 
	
		echo "<h2>Infusionsoft: Order Items</h2>";
			  
		switch($search_field) {
			case "Username": 		$search_sql = "m.username = '$search_str'"; break;
			case "Email": 			$search_sql = "m.email = '$search_str'"; break;
			case "Member ID": 		$search_sql = "m.member_id = '$search_str'"; break;
			case "Inf Contact Id":	$search_sql = "m.inf_contact_id = '$search_str'"; break;
			case "Inf Invoice Id":	$search_sql = "i.inf_invoice_id = '$search_str'"; break;
			case "Sponsor Username": 
				$search_sql = "s.username = '$search_str'";
				$sponsor_join_sql = 'LEFT JOIN members s ON m.sponsor_id = s.member_id';
				break;
			default: $search_sql = "1";
		}
		$sql_limit = ($limit != "All") ? "LIMIT $limit" : "";
		$min_amt_sql = "";
		if ($min_amt != 'All') {
			$min_amt_sql = "AND ABS(i.invoice_total) >= $min_amt";
		}

		$query = "SELECT oi.*, m.username
					FROM inf_order_items oi
					LEFT JOIN inf_invoice_items ii USING (inf_order_item_id)
					LEFT JOIN inf_invoices i USING (inf_invoice_id)
					LEFT JOIN members m USING (inf_contact_id)
					$sponsor_join_sql
					WHERE $search_sql
					$min_amt_sql
					ORDER BY oi.inf_order_item_id DESC
					$sql_limit";        
		#EchoLn($query);
		$table_head = "<table width='600px' class='daTable'><thead><tr>"
		. WriteTH("#")	
		. WriteTH("Username")	
		. WriteTH("Order Item Id")	
		. WriteTH("Order Id")	
		. WriteTH("Product Id")	
		. WriteTH("Sub Plan Id")	
		. WriteTH("Item Name")	
		. WriteTH("Qty", TD_RIGHT)
		. WriteTH("CPU", TD_RIGHT)
		. WriteTH("PPU", TD_RIGHT)
		. WriteTH("Item Description")
		. WriteTH("Item Type")
		. WriteTH("Notes")
		. WriteTH("Sync Date")	
		. "</tr></thead>";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$table_rows = '';
		$table_foot = "</table>";
		for($i=1; $row = mysqli_fetch_assoc($result); $i++){
			$table_rows .= "<tr>"
			. WriteTD($i)	
			. WriteTD($row['username'])	
			. WriteTD($row['inf_order_item_id'], TD_RIGHT)	
			. WriteTD($row['inf_order_id'], TD_RIGHT)	
			. WriteTD($row['inf_product_id'], TD_RIGHT)	
			. WriteTD($row['subscription_plan_id'], TD_RIGHT)	
			. WriteTD($row['item_name'])	
			. WriteTD($row['qty'], TD_RIGHT)	
			. WriteTD(WriteDollarCents($row['cpu']), TD_RIGHT)	
			. WriteTD(WriteDollarCents($row['ppu']), TD_RIGHT)	
			. WriteTD($row['item_description'])	
			. WriteTD($row['item_type'], TD_RIGHT)	
			. WriteTD(WriteNotesLong($row['notes'], 20))	
			. WriteTD(WriteDate($row['create_date']))	
			. "</tr>";
		}
		if ($i>1) {
			echo $table_head . $table_rows. $table_foot;
		} else {
			echo "<font color=grey>No records found.</font>";
		}

		if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}	

}

?>
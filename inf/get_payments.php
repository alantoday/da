<?php
# MANUAL (Delete/Reset Payment) USAGE:
# da.digitalaltitude.co/inf/get_payments.php?debug=1&inf_invoice_id=xxx

require_once("/home/digital/public_html/da/includes/config.php");
require_once(PATH."includes/functions.php");
require_once(PATH."includes/functions_inf.php");

// Include the SDK
require_once(PATH."scripts/Infusionsoft/infusionsoft.php");
require_once(PATH."scripts/Infusionsoft/examples/object_editor_all_tables.php");

if (DEBUG) EchoLn(date("Y-m-d H:i:s")."<br>");

// tables:
// - Invoice: Total paid and list of products, eg, http://digialti.com/inf/example.php?object=Invoice
// - InvoiceItem: Amount per invoice, eg, http://digialti.com/inf/example.php?object=InvoiceItem
// - OrderItem: Amount per item, eg, http://digialti.com/inf/example.php?object=OrderItem
// - InvoicePayment: Shows what's been paid and how, eg, http://digialti.com/inf/example.php?object=InvoicePayment
// - * Better: Payment: Shows which invoices have been paid and how, eg, http://digialti.com/inf/example.php?object=Payment 
// NOTE: Can get IP from: Referral

// Start with Payment: get PayDate, PayAmt, ContactId, InvoiceId	
// For each InvoiceId (InvoiceItem): get OrderItemId, InvoiceAmt
// For each ContactId: 
$object_type = "Payment";
$class_name = "Infusionsoft_" . $object_type;
$object = new $class_name();

########################################################################
# If payment is being update delete all existing records



########################################################################
# Look for 500 payments made after the last one we have in our system

$id_array = array();

if (isset($_GET['inf_invoice_id'])) {
	$inf_invoice_id = $_GET['inf_invoice_id'];

	$query = "SELECT inf_payment_id, substring(pay_date,1,10) AS pay_date
				FROM inf_payments 
				WHERE inf_invoice_id = $inf_invoice_id";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	$pay_dates = array();
	for ($i=1; $row = mysqli_fetch_assoc($result); $i++) {
		$pay_dates[] = $row['pay_date'];
		$payment_ids[] = $row['inf_payment_id'];
	}
	if (!empty($payment_ids)) {
		$query = "DELETE FROM commissions 
				WHERE inf_payment_id IN (".implode(",",$payment_ids).")";
		if (DEBUG) EchoLn("$i. $query");
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	}

	if (!empty($pay_dates)) {
		# Flag the daily stats to be recalculated (on those days affected)
		$query = "UPDATE commissions_daily 
				SET recalc = 1
				WHERE create_date IN ('".implode("','",$pay_dates)."')";
		if (DEBUG) EchoLn("$query");
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);						
	}

	$query = "DELETE FROM inf_payments 
				WHERE inf_invoice_id = $inf_invoice_id";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	if (DEBUG) EchoLn($query);
	$query = "DELETE FROM inf_invoices 
				WHERE inf_invoice_id = $inf_invoice_id";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	$query = "DELETE FROM inf_invoice_items 
				WHERE inf_invoice_id = $inf_invoice_id";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	$query = "DELETE FROM inf_invoice_payments 
				WHERE inf_invoice_id = $inf_invoice_id";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	$query = "DELETE FROM inf_order_items 
				WHERE inf_order_id = $inf_invoice_id";
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);

    $objects = Infusionsoft_DataService::query(new $class_name(), array('InvoiceId' => $_GET['inf_invoice_id']));    
	EchoLn("PAYMENT RELOADED... Commissions will be updated in a couple minutes and Daily Stats will be refreshed in a few hours.", GREEN);
	EchoLn("<a target='_blank' href='http://da.digitalaltitude.co/crons/calc_comms.php'>Update Commissions Now</a>", GREEN);
	EchoLn("<a target='_blank' href='http://da.digitalaltitude.co/crons/calc_comms_daily.php'>Update Daily Commission Stats</a>", GREEN);
} else {

	# Get most recent inf_payment_id in our system
	$query = "SELECT inf_payment_id 
				FROM inf_payments
				WHERE 1
				ORDER BY inf_payment_id DESC
				LIMIT 1";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
	if($row = mysqli_fetch_assoc($result)) {
		// Build up list of 1000 next numbers
		// NOTE: Would be nice if we could just "search" by payment_id's greater than a number
		#$row['inf_payment_id'] = 1;
		for($i=1;$i<1000;$i++) {
			$id_array[] = ($row['inf_payment_id']+$i);
		}
	    #if (DEBUG) EchoLn("Getting list: ").WriteArray($id_array);
		$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => $id_array));    
	} else {
		// Just in case we have no paymets: Get 1000 most recent payments from INF
		#if (DEBUG) EchoLn("Getting 1000 most recent");
		$objects = Infusionsoft_DataService::queryWithOrderBy(new $class_name(), array('Id' => '%'), 'Id', false);
	#    public static function queryWithOrderBy($object, $queryData, $orderByField, $ascending = true, $limit = 1000, $page = 0, $returnFields = false, Infusionsoft_App $app = null)
	}
}

$payment_array = array();
foreach($objects as $i => $object){
	$payment_array[$i] = $object->toArray();
}
########################################################################
# Insert Payments
foreach($payment_array as $i => $payment){ 
	$pay_date = date("Y-m-d H:i:s", strtotime($payment['PayDate']));
	$query = "REPLACE INTO inf_payments
				SET inf_payment_id	='{$payment['Id']}'
				, inf_contact_id	='{$payment['ContactId']}'
				, inf_invoice_id	='{$payment['InvoiceId']}'
				, pay_amt			='{$payment['PayAmt']}'
				, pay_type			='{$payment['PayType']}'
				, pay_note			='".addslashes($payment['PayNote'])."'
				, refund_id			='{$payment['RefundId']}'
				, charge_id			='{$payment['ChargeId']}'
				, synced			='{$payment['Synced']}'
				, pay_date			='$pay_date'
				, create_date		=NOW()
				";            
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	
	# Flag payment as "Synced"
	$output = Infusionsoft_InvoiceService::setPaymentSyncStatus($payment['Id'], 1);
	#if (DEBUG) WriteArray($output);

	########################################################################
	# Insert Invoice and it's items
		
	$object_type = "Invoice";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array('Id' => $payment['InvoiceId']));

    $invoice_array = array();
	foreach($objects as $i => $object){
		$invoice_array[$i] = $object->toArray();
	}
	$order_items_array = array();
    #if (DEBUG) WriteArray($invoice_array);
	foreach($invoice_array as $i => $invoice){ 
		$date = date("Y-m-d H:i:s", strtotime($invoice['DateCreated']));
		$date_modified = $date;
		# FIrst item is invoice, others are invoice items.
		if ($i==0) {
			$query = "REPLACE INTO inf_invoices
						SET inf_invoice_id	='{$invoice['Id']}'
						, inf_contact_id	='{$invoice['ContactId']}'
						, inf_job_id		='{$invoice['JobId']}'
						, date_created		='$date'
						, invoice_total		='{$invoice['InvoiceTotal']}'
						, total_paid		='{$invoice['TotalPaid']}'
						, total_due			='{$invoice['TotalDue']}'
						, pay_status		='{$invoice['PayStatus']}'
						, credit_status		='{$invoice['CreditStatus']}'
						, refund_status		='{$invoice['RefundStatus']}'
						, pay_plan_status	='{$invoice['PayPlanStatus']}'
						, inf_affiliate_id	='{$invoice['AffiliateId']}'
						, lead_affiliate_id	='{$invoice['LeadAffiliateId']}'
						, promo_code		='{$invoice['PromoCode']}'
						, invoice_type		='{$invoice['InvoiceType']}'
						, description		='{$invoice['Description']}'
						, product_sold		='{$invoice['ProductSold']}'
						, synced			='{$invoice['Synced']}'
						, create_date 		= NOW()
						";     
            if (DEBUG) EchoLn($query);
			$result = mysqli_query($db, $query) or die(mysqli_error($db));
			# Flag Invoice as "Synced"
			$output = Infusionsoft_InvoiceService::setInvoiceSyncStatus($invoice['Id'], 1);
		} else {
			$order_items_array[] = $invoice['OrderItemId'];
			$query = "REPLACE INTO inf_invoice_items
						SET inf_invoice_id	='{$invoice['InvoiceId']}'
						, inf_order_item_id	='{$invoice['OrderItemId']}'
						, invoice_amt		='{$invoice['InvoiceAmt']}'
						, description		='{$invoice['Description']}'
						, commission_status	='{$invoice['CommissionStatus']}'
						, date_created		='$date'
						, date_modified 	= NOW()
						";     
            if (DEBUG) EchoLn($query);
			$result = mysqli_query($db, $query) or die(mysqli_error($db));			
		}
    }

	########################################################################
	# Insert Invoice and it's items
		
	$object_type = "InvoiceItem";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array('InvoiceId' => $payment['InvoiceId']));

    $invoice_item_array = array();
	foreach($objects as $i => $object){
		$invoice_item_array[$i] = $object->toArray();
	}
    if (DEBUG) EchoLn("INVOICE ITEMS: ");
    #WriteArray($invoice_array);
	foreach($invoice_item_array as $i => $invoice_item){ 
		$date = date("Y-m-d H:i:s", strtotime($invoice_item['DateCreated']));
		$query = "REPLACE INTO inf_invoice_items
					SET inf_invoice_id	='{$invoice_item['InvoiceId']}'
					, inf_order_item_id	='{$invoice_item['OrderItemId']}'
					, invoice_amt		='{$invoice_item['InvoiceAmt']}'
					, description		='{$invoice_item['Description']}'
					, commission_status	='{$invoice_item['CommissionStatus']}'
					, date_created		='$date'
					, date_modified 	= NOW()
					";     
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db));			
	}
        
	########################################################################
	# Insert Invoice Payments (get them all in one call)
	$object_type = "InvoicePayment";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array('InvoiceId' => $payment['InvoiceId']));
	if (DEBUG) EchoLn("INVOICE PAYMENT: ");
	$invoice_payment_id_array = array();
	foreach($objects as $i => $object){
		$invoice_payment_id_array[$i] = $object->toArray();
	}
	#WriteArray($invoice_payment_id_array);
	foreach($invoice_payment_id_array as $i => $invoice_payment){ 
		$pay_date = date("Y-m-d H:i:s", strtotime($invoice_payment['PayDate']));
		$query = "REPLACE INTO inf_invoice_payments
			SET inf_invoice_payment_id  ='{$invoice_payment['Id']}'
			, inf_invoice_id	='{$invoice_payment['InvoiceId']}'
			, amt               ='{$invoice_payment['Amt']}'
			, pay_date          ='$pay_date'
			, pay_status        ='{$invoice_payment['PayStatus']}'
			, inf_payment_id    ='{$invoice_payment['PaymentId']}'
			, skip_commission   ='{$invoice_payment['SkipCommission']}'
			, create_date       =NOW()
			";     
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db));			
	}
	
	########################################################################
	# Insert Order Items (get them all in one call)
	$object_type = "OrderItem";
	$class_name = "Infusionsoft_" . $object_type;
	$object = new $class_name();
	$objects = Infusionsoft_DataService::query(new $class_name(), array('OrderId' => $payment['InvoiceId']));
	if (DEBUG) EchoLn("ORDER_ITEMS: ");
	$order_item_array = array();
	foreach($objects as $i => $object){
		$order_item_array[$i] = $object->toArray();
	}
	$product_id = 0; // ASPIRE Walker Trial
	foreach($order_item_array as $i => $order_item){ 
		if ($order_item['ProductId'] == 9) {
			$product_id = 9;
		}
		$query = "REPLACE INTO inf_order_items
					SET inf_order_item_id='{$order_item['Id']}'
					, inf_order_id		='{$order_item['OrderId']}'
					, inf_product_id	='{$order_item['ProductId']}'
					, subscription_plan_id='{$order_item['SubscriptionPlanId']}'
					, item_name			='{$order_item['ItemName']}'
					, qty				='{$order_item['Qty']}'
					, cpu				='{$order_item['CPU']}'
					, ppu				='{$order_item['PPU']}'
					, item_description	='{$order_item['ItemDescription']}'
					, item_type			='{$order_item['ItemType']}'
					, notes				='{$order_item['Notes']}'
					, create_date       =NOW()
					";     
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db));			
	}
        
    ########################################################################
    # Insert Contact
    $member_row = InfInsertMember($db, $payment['ContactId'], 0, $product_id, $payment['Id'], $payment['InvoiceId']);
}
if (DEBUG) EchoLn("DONE", GREEN);
exit;
?>

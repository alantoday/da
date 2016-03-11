<?php include_once("../includes/functions_inf.php"); ?>
<?php
if (empty($_SESSION['member_id'])) {
	header("location: http://" . $_SERVER['HTTP_HOST']."/?action=logout&pg=".urlencode($_SERVER['REQUEST_URI']));	
} 

# Enforce https on this page
if ($_SERVER['HTTP_HOST'] == "my.digitalaltitude.co") {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ) {
		header("Location: "."https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
}

# Temporary (they get both as a bundle).
if ($_GET['product']=='ris') $_GET['product'] = 'ris-only';

$mrow = GetRowMember($db, $_SESSION['member_id']); 

$order_description = "";
$terms = '';
$inf_sub_id = 0;
# If a core product then force them to purchase the bundle
if (isset($_GET['product'])) {
	
	$sub_plan_options = array();
	$sub_plan_prices = array();
	$sub_plan = false;
	if (in_array($_GET['product'],array("aff", "asp-w", "asp-h", "asp-c"))) {
		$product_field = is_numeric($_GET['product']) ? "inf_product_id" : "product_type"; 

		$query = "SELECT s.inf_sub_plan_id, s.cycle, s.frequency, s.plan_price, s.active, s.bonus
					FROM inf_sub_plans s
					JOIN inf_products p USING (inf_product_id)
					WHERE active = 1
					AND p.$product_field='{$_GET['product']}'
					ORDER BY s.cycle DESC, s.frequency";
		if (DEBUG) EchoLn($query);
		$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);
		while ($row = mysqli_fetch_assoc($result)) {
			$sub_plan = true;
			if ($row['frequency']==1) {
				$frequency_name = "";
				$plural = "";
			} else {
				$frequency_name = " ".$row['frequency'];
				$plural = "s";
			}
			$bonus = "";
			if ($row['bonus']) {
				$bonus = " (". $row['bonus'] .")";
			}
			// eg, Write: "every 6 months" or "every year".
			$sub_plan_options[] = array($row['inf_sub_plan_id'] => WriteDollarCents($row['plan_price']). " every$frequency_name ".InfWriteCycleName($row['cycle']).$plural.$bonus);
			$sub_plan_prices[$row['inf_sub_plan_id']] = $row['plan_price'];
		}
	}
	# Do they already own this products - or products in this bundle?
	if ($order_items = _GetCoreProductBundle($db, $_GET['product'])) {
		if (count($order_items) == 1) {
			# One products
			foreach ($order_items as $inf_product_id => $product_details) {
				$product_title = $product_details['product_title'];
				$product_description = $product_details['product_description'];
				$product_img = $product_details['product_img'];
				$total_price = $product_details['product_price'];
			}
			if ($sub_plan) {
				$price_display = "";
			} else {
				$price_display = WriteDollars($product_details['product_price']);
			}
			if ($product_details['product_title']) {
				$order_description = $price_display . " ". $product_details['product_title'];
			} else {
				$order_description = $price_display . " ". $product_details['product_name'];				
			}
			$order_description = "<p style='font-size:16px;'>$order_description</p>";
		} elseif (count($order_items)) {
			# Mulitple products
			$res = _HandleMultipleProducts($order_items, $sub_plan);
			$order_description = $res['order_description'];
			$total_price = $res['total_price'];
			$product_title = ""; // Multiple
			$product_description = ""; // Multipe
			$product_img = ""; // Multiple
		} else {
			# No product
		}
	} else {
		if ($product_name = WriteProductName($db, $_GET['product'])) {
			$fatal_error[] = "You already own $product_name.";
		} else {
			$fatal_error[] = "INVALID: The product '{$_GET['product']}' is not valid or not for sale.";			
		}
	}
} else {
	# No product set	
	$fatal_error[] = "MISSING PRODUCT: You must select a product before you can place your order.";			
}
if(isset($_GET['product']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$invoice_title = "Member Order: ".WriteDollarCents($total_price);
	$notes = "";
	
	if(!$_POST['payment_type']) { $error[] = "MISSING: Your payment type."; }
	if ($_POST['payment_type']!="check_wire") {
		if(trim($_POST['cvv'])=="") { $error[] = "MISSING: Your CVV."; }
	}
	if(!isset($_POST['purchase_terms'])) { $error[] = "MISSING: You must agree to Purchase Agreement."; }		
	if (in_array($_GET['product'],array("aff"))) {
		if(!isset($_POST['affiliate_terms'])) { $error[] = "MISSING: You must agree to Affiliate Agreement."; }		
	}
	
	if (empty($error) && $_POST['payment_type']=="check_wire") {
		// Create a new Invoice
		$_POST['inf_invoice_id'] = _CreateInvoiceWithItems ($db, $error, $mrow['inf_contact_id'], $invoice_title, $order_items, $notes);

		if (empty($_POST['inf_invoice_id'])) {
			$error[] = "ERROR: Something went wrong while trying to create your order";
		} else {
			# ORDER CREATED SUCCESSFULLY
			header("location: http://" . $_SERVER['HTTP_HOST'] ."/my-account/payments.php?note=SUCCESS:+Your+order+was+created+successfully");
			exit();
		}
	} elseif (empty($error) && $_POST['payment_type']=="card") {
		if (empty($_POST['inf_invoice_id'])) {
			//Create a new Invoice
			if (!$sub_plan) {
				$_POST['inf_invoice_id'] = _CreateInvoiceWithItems ($db, $error, $mrow['inf_contact_id'], $invoice_title, $order_items, $notes);
			} else {
				# First see if a subsription plan already exists for that product
				if ($subscriptions_array = InfGetRecurringOrders($mrow['inf_contact_id'], _WriteProductId($db, $_GET['product']))) {
					foreach ($subscriptions_array as $sub_id => $subscription_row) {
						$inf_sub_id = $sub_id;
						break; // Just grab first on. TODO. What if they have more? 
					}
					if (DEBUG) WriteArray($_POST);
					if ($inf_sub_id) {
						$error[] = "ERROR: You already have an active Subscription Plan for this product.";
						
						#NOTE: FOLLOWING DOES NOT WORK - AND PROBABLY A BAD IDEA
						# Update the subscriptions to match their subscriptions, ie, Yearly vs Monthly & price
						##$result = InfUpdateRecurringOrder ($inf_sub_id, $_POST['inf_sub_plan_id'], $sub_plan_prices[$_POST['inf_sub_plan_id']], $_POST['card_id']);
						##echo "1.",$inf_sub_id."-".$_POST['inf_sub_plan_id']."-".$sub_plan_prices[$_POST['inf_sub_plan_id']]."-".$_POST['card_id'].WriteArray($result);
					} else {
						$error[] = "ERROR: Failed find existing Subscription Plan.";								
					}
					
				} else {
					# Create a suscription
					$days_till_charge = 0;
					$inf_sub_id = InfCreateRecurringOrder($mrow['inf_contact_id'], $_POST['inf_sub_plan_id'], $sub_plan_prices[$_POST['inf_sub_plan_id']], $_POST['card_id'], $days_till_charge);
					$msg[] = "Creating New Subscription: $inf_sub_id";	
				}
				if (!$inf_sub_id) {
					$error[] = "ERROR: Failed to create a Subscription Plan.";	
				}
				if (empty($error)) {
					// Create an invoice for the recurring order.
					$_POST['inf_invoice_id'] = Infusionsoft_InvoiceService::createInvoiceForRecurring($inf_sub_id);	
					if (!$_POST['inf_invoice_id']) {
						$error[] = "ERROR: Failed to create a Subscription Payment Invoice for Subscription '$inf_sub_id'.";	
					}
				}
			}
		}

		if (empty($error) && $_POST['cvv']<>"xxx") {
			# Update card with CVV before charging it
			$msg[] = "CVV passed to Card: ".InfUpdateCreditCardCVV ($_POST['card_id'], $_POST['cvv']);
		}
		if (empty($error)) {			
			if ($sub_plan) {
				$total_price = $sub_plan_prices[$_POST['inf_sub_plan_id']];
			}
			$charge_res = Infusionsoft_InvoiceService::chargeInvoice($_POST['inf_invoice_id'], "DA Member Order", $_POST['card_id'], INF_MERCHANT_ID, false);
#			$charge_res = Infusionsoft_InvoiceService::chargeInvoiceArbitraryAmount($mrow['inf_contact_id'], $_POST['inf_invoice_id'], $_POST['card_id'], $total_price, INF_MERCHANT_ID, $invoice_title);
			
			if (!$charge_res['Successful']) {
				$error[] = strtoupper($charge_res['Code']).": " .$charge_res['Message'];
				# Delete temporary invoice
				#Infusionsoft_InvoiceService::deleteInvoice($_POST['inf_invoice_id']);
				$_POST['inf_invoice_id'] = "";
				if (DEBUG) WriteArray($charge_res);
			} else {
				$msg[] = "SUCCESS: Your order has been processed successfully";	
				if (DEBUG) WriteArray($result);
			}	
		}
	}
}

if ($inf_sub_id && !empty($error)) {
	# Delete Subscription plan if one is created but there is an error
	Infusionsoft_InvoiceService::deleteSubscription($inf_sub_id);						
	if (DEBUG) $error[] = "UPDATE: Deleting temporary Subscription '$inf_sub_id'.";	
}
?>
<?php include("includes_my/header.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>
<div style="minHeight:580px;">
  <?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br>"; ?>
  <?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br>"; ?>
  <?php if (!empty($fatal_error)) {
	echo "<center><b><font color='red'>".implode("<br>",$fatal_error)."</font></b></center>"; 
	exit();
} ?>
<style>
#ccform td {
	padding: 5px 0px;
	font-size: 14px;
	color: #444;
}
label {
	text-align: left;
}
/*
thead {
	background: #51A7FA; //SteelBlue;
	color: #000;
	border-bottom:thin;
}
*/
th, td {
  padding: 10px 20px;
}

tbody tr:nth-child(odd) {
  background: #FFF;
}
tfoot {
	border-top:medium;
	font-weight:300;
	background: #DDD;
	color: #000;
}
</style>
  <br>
  <div class="row five-columns cf ui-sortable" id="le_body_row_3" data-style="">
    <div class="fixed-width">
      <div class="three-fifths column cols" id="le_body_row_3_col_1">
        <div class="element-container cf" data-style="" id="le_body_row_3_col_1_el_1">
          <div class="element">
            <div class="feature-box feature-box-23 feature-box-align-center" style="width: 500px;">
              <p class="box-title" style="font-size:25px;font-weight:300px;margin-bottom:20px;line-height:25px;color:#123">SECURE ORDER FORM</p>
              <div class="feature-box-content cf">
                <div class="row element-container cf"> <?php echo $order_description; ?><br>
                </div>
                <div class="row element-container cf">
                  <div class="image-caption" style="width:418px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;">
                    <center>
                      <div style="width:425px">
                        <?php _OutputOrderForm($db, $mrow['inf_contact_id'], $total_price, $sub_plan, $sub_plan_options); ?>
                      </div>
                    </center>
                  </div>
                </div>
              </div>
              <div class="row element-container cf">
                <center>
                <img alt="" src="https://s3.amazonaws.com/public.digitalaltitude.co/images/members/order/secure-payment.png" border="0" style="width:450px; padding-bottom:30px">
                </center>
              </div>
            </div>
          </div>
        </div>
      </div>
    
    <!-- RIGHT SIDE -->
    <div class="two-fifths column cols narrow" id="le_body_row_3_col_2">
      <div class="element-container cf" data-style="" id="le_body_row_3_col_2_el_1">
        <div class="element">
          <div class="image-caption" style="width:350px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;"><img alt="" src="<?php echo $product_img; ?>" border="0" class="full-width"></div>
        </div>
      </div>
      <div class="element-container cf" data-style="" id="le_body_row_3_col_2_el_2">
        <div class="element">
          <span style="font-size:32px;font-family:&quot;Shadows Into Light&quot;, sans-serif;font-weight:normal;color:#303030;letter-spacing:-1px;text-align:left;"><?php echo $product_title; ?></span>
          <p style="font-size:18px;font-family:&quot;Raleway&quot;, sans-serif;font-weight:300;color:#363636;line-height:28px;margin-top:20px;"><?php echo $product_description; ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include(INCLUDES_MY."footer.php"); ?>
<?php
# INPUT: $type = "Affilaite" | "Purchase"
function _WriteTerms($db, $type) {
	$query = "SELECT text
				FROM terms
				WHERE type = '$type'
				ORDER BY create_date DESC";		
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if ($row = mysqli_fetch_assoc($result)) {
		return html_entity_decode($row['text'], ENT_QUOTES, "UTF-8");  // DOES NOT WORK HERE
		// return str_replace(array('"','"',''',"''), array('"','"',"'","'"), $row['text']);
	} else {
		return "";	
	}
}

# INPUT: $product (could be inf_product_id or product_name
function _WriteProductId($db, $product){
	if (is_numeric($product)) {
		return $product;
	} else {
		$query = "SELECT inf_product_id
					FROM inf_products
					WHERE product_type = '$product'";		
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if ($row = mysqli_fetch_assoc($result)) {
			return $row['inf_product_id'];
		} else {
			return false;	
		}
	}
}

function _CreateInvoiceWithItems ($db, &$error, $inf_contact_id, $invoice_title, $order_items, $notes) {
	
	$inf_invoice_id = Infusionsoft_InvoiceService::createBlankOrder($inf_contact_id, $invoice_title, date('Ymd\TH:i:s'));
	if (DEBUG) EchoLn("createBlankOrder. Invoice Id: $inf_invoice_id");

	foreach ($order_items as $inf_product_id => $product_details) {
		$result = Infusionsoft_InvoiceService::addOrderItem($inf_invoice_id, $inf_product_id, INF_ORDER_TYPE, $product_details['product_price'], INF_QUANTITY, $product_details['product_name'], $notes);
		if (!$result) {
			$error[] = "ERROR: Something went wrong while trying to add item [$inf_product_id] to your order";
		}
	}
	return $inf_invoice_id;
	# Create blank order	
	#	Infusionsoft_InvoiceService::createBlankOrder($mrow['inf_contact_id'], "An Order", date('Ymd\TH:i:s'));
	#	Infusionsoft_InvoiceService::addOrderItem($invoiceId, $inf_product_id, 4, 3.99, 1, 'Order Item', '');
	#	Infusionsoft_InvoiceService::addManualPayment($invoiceId, 3.99, date('Ymd\TH:i:s'), 'API', 'A Manual Payment from the API');		
}

function _OutputOrderForm ($db, $inf_contact_id, $total_price, $sub_plan, $sub_plan_options) {
    if ($total_price <= 6500) {
        $check_wire_only = false;
        $check_wire_note = "";
        $payment_type_options[] = array("card"=>"Credit Card");
    } else {
        $check_wire_only = true;
        $check_wire_note = "<font color=red><i>Orders in excess of $6,500 can not be paid with a Credit Card. After you submit your 
below you will be directed to these <a target='_blank' href='/my-account/payments.php'>Payment Instructions</a> to complete your payment.</i></font>";
    }
    $payment_type_options[] = array("check_wire"=>"Check/Wire");
    ?>
<form method="post" id="order_form" onsubmit="disable_submit()">
  <input type="hidden" name='inf_invoice_id' value='<?php echo isset($_POST['inf_invoice_id']) ? $_POST['inf_invoice_id'] : ""; ?>'>
  <table width="100%" id="ccform">
    <?php if ($sub_plan) { ?>
    <tr>
      <td nowrap=nowrap>Subscription Terms:</td>
      <td><select name="inf_sub_plan_id" <?php if (!empty($_POST['inf_invoice_id'])) echo "DISABLED"; ?>>
          <?php echo WriteSelect(isset($_POST['inf_sub_plan_id']) ? $_POST['inf_sub_plan_id'] : "", $sub_plan_options, false, false); ?>
        </select></td>
    </tr>
    <?php } ?>
    <tr>
      <td nowrap=nowrap width='135px'>Payment Type:</td>
      <td><select name="payment_type" onChange="showCards(this)">
          <?php echo WriteSelect(isset($_POST['payment_type']) ? $_POST['payment_type'] : "", $payment_type_options, false, false); ?>
        </select></td>
    </tr>
    <?php if (!empty($check_wire_note)) { ?>
    <tr>
      <td colspan=2><?php echo $check_wire_note; ?></td>
    </tr>
    <?php } ?>
    <tr id='cards_select' <?php if ($check_wire_only) echo "style='display:none'"; ?>>
      <td width='135px'><?php
            $javascript_callback = '
            var card_id = parameters[0];
            var card_description = parameters[1];
            var select_cards = document.getElementById("card_id");
            var new_option = document.createElement("option");
            new_option.text = card_description;
            new_option.value = card_id;
            try {
              select_cards.add(new_option, null); // standards compliant; doesnt work in IE
            }
            catch(ex) {
              select_cards.add(new_option); // IE only
            }
            select_cards.selectedIndex = select_cards.options.length-1;
            ';
        ?>
        My Card:</td>
      <td nowrap=nowrap><select name="card_id" id="card_id">
          <?php echo WriteSelect(isset($_POST['card_id']) ? $_POST['card_id'] : "", WriteCardOptions($inf_contact_id), false, false); ?>
        </select>
        &nbsp;
        <?=WriteFramePopup("AddCardIframe", "/my-account/my-card.php?skip_h=1", $javascript_callback, 485, 200, "+ Add Card", "'', true");?></td>
    </tr>
    <tr id='cards_select_2' <?php if ($check_wire_only) echo "style='display:none'"; ?>>
      <td width='135px'>CVV:</td>
      <td><input type="text" name='cvv' maxlength='4' value='<?php echo isset($_POST['cvv']) ? $_POST['cvv'] : ""; ?>' style="width:40px"></td>
    </tr>
  </table>
  <br>
  <textarea name="textarea" disabled style="width:390px; height:75px"><?php echo _WriteTerms($db, "Purchase"); ?></textarea>
  <br>
  <input type="checkbox" id="purchase_terms-agree" name="purchase_terms" value="1" <?php echo !empty($_POST['purchase_terms']) ? "CHECKED" : ""?>>
  <span style="color:#444; font-size:14px;">I agree to the Purchase Agreement</span> <br>
  <?php if (in_array($_GET['product'],array("aff"))) { ?>
  <br>
  <textarea name="textarea" disabled style="width:390px; height:75px"><?php echo _WriteTerms($db, "Affiliate"); ?></textarea>
  <br>
  <input type="checkbox" id="affiliate-terms-agree" name="affiliate_terms" value="1" <?php echo !empty($_POST['affiliate_terms']) ? "CHECKED" : ""?>>
  I agree to the Affiliate Agreement <br>
  <?php } ?>
  <br>
  <?php /* <a href="" id="btn_gg1" class="css-button style-1" style="padding:15px 72px;background:#ffa035;">
        <span class="text" style="font-size:25px;color:#ffffff;">ORDER NOW</span><span class="hover"></span>
        <span class="active"></span>
      </a>
    */ ?>
  <input type='submit' class='btn' id='submit_btn' name='submit' value='ORDER NOW' style='font-size:25px;padding:10px 80px;background:#ffa035'>
</form>
<?php
}

# INPUT: $product = bas | ris | asc | pea | ape  (or could be inf_product_id
# RETURNS: Array of products contains $inf_product_id and any Core products below that they don't own
function _GetCoreProductBundle ($db, $product) {
	
	$product_field = is_numeric($product) ? "inf_product_id" : "product_type"; 
	
	# If it has -only, eg, ape-only then don't look for lower products
	$single_product_only = false;
	if (preg_match("/\-only$/", $product)) {
		$single_product_only = true;
		$product = preg_replace("/\-only$/", "", $product);
	}

	$core_product = false;
	$res = array();
		
	if (!$single_product_only) {
		$product_sql = "AND (
					p.core_level > 0
					AND p.core_level <=
						(SELECT core_level
							FROM inf_products
							WHERE $product_field = '$product') 
					) OR (
					p.$product_field = '$product'
					)";		
	} else {
		$product_sql = "AND p.$product_field = '$product'";				
	}
	# Get Core Product they don't own - up to $inf_product_id
	$query = "SELECT p.inf_product_id, p.product_name, p.product_price, p.terms
					, p.product_title, p.product_description, p.product_img
					, mr.start_date, mr.end_date
				FROM inf_products p 
				LEFT JOIN member_ranks mr ON mr.product_type = p.product_type 
					AND mr.member_id={$_SESSION['member_id']}
					AND mr.start_date < NOW()
					AND (mr.end_date IS NULL 
						OR mr.end_date > NOW())
				WHERE mr.member_rank_id IS NULL
				$product_sql
				ORDER by p.core_level";		
	if (DEBUG) EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$res = array();
	for($i=1; $row = mysqli_fetch_assoc($result); $i++){
		$res[$row['inf_product_id']]['product_name'] = $row['product_name'];
		$res[$row['inf_product_id']]['product_title'] = $row['product_title'];
		$res[$row['inf_product_id']]['product_description'] = $row['product_description'];
		$res[$row['inf_product_id']]['product_img'] = $row['product_img'];
		$res[$row['inf_product_id']]['product_price'] = $row['product_price'];
		$res[$row['inf_product_id']]['terms'] = $row['terms'];
	}	
	return $res;
}

function _HandleMultipleProducts($order_items, $sub_plan) {
	$table_rows = '';
	$i=0;
	$total_price = 0;
	foreach ($order_items as $inf_product_id => $product_details) {
		$i++;
		if (!empty($product_details['terms'])) {
			$terms .= $product_details['terms']."<br>";
		}
		$total_price += $product_details['product_price'];
		if (!$sub_plan) {
			if (in_array($_GET['product'],array("su-h","su-g","su-t"))) {
				$price = WriteDollarCents($product_details['product_price']);
				$price_display = "<font color=red><s>".WriteDollarCents($product_details['product_price']*2)."</s></font> $price";
			} else {
				$price = WriteDollarCents($product_details['product_price']);
				$price_display = $price;						
			}
			$price = WriteDollarCents($product_details['product_price']);
		} else {
			$price = "See options below";
			$price_display = $price;						
		}
		$table_rows .= "<tr>"
			.WriteTD($product_details['product_name'])
			.WriteTD($price_display, TD_RIGHT)
			."</tr>";		
	}
	if ($i>1) {
		if (!$sub_plan) {
			$total_price_display = WriteDollarCents($total_price);
		} else {
			$total_price = "See options below";
		}
		$table_foot = "<tfoot><tr>"
			.WriteTD("<b>TOTAL</b>", TD_RIGHT)
			.WriteTD("<b>$total_price_display</b>", TD_RIGHT)
			."</tr></table></center>";			
	} else {
		// Single product
		$table_foot = '</tr></table></center>';	
	}
	$table_head = "<center><table align='center' width='420px' class='daTable'>";
/*			$table_head .= "<thead><tr>"
	. WriteTH("Order Item")	
	. WriteTH("Price", TD_RIGHT)	
	. "</tr></thead>"; */
	$order_description = $table_head . $table_rows . $table_foot;
	if (!empty($terms) && !$sub_plan) {
		$order_description .= "<br><center><i>Subscription Terms: {$terms}You can cancel at anytime.</i></center>";	
	}
	$res['order_description'] = $order_description;
	$res['total_price'] = $total_price;
	return $res;
}
?>
<script>
function disable_submit(){
	$("#submit_btn").prop("disabled", true);
	$("#submit_btn").css("background", "#aaa");	
}


function showCards(elem){
   if(elem.value == 'check_wire') {
	  document.getElementById('cards_select').style.display = "none";
	  document.getElementById('cards_select_2').style.display = "none";
   } else {
	  document.getElementById('cards_select').style.display = "block";	   
	  document.getElementById('cards_select_2').style.display = "block";	   
   }
}
</script> 

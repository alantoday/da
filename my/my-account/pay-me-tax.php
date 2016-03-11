<?php
# Enforce https on this page
if ($_SERVER['HTTP_HOST'] == "my.digitalaltitude.co") {
	if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ) {
		header("Location: "."https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		exit();
	}
}
include("../includes_my/header.php"); 

$pass = isset($_POST['pass']) ? $_POST['pass'] : "";
$form = isset($_POST['form']) ? trim($_POST['form']) : "";
$tin_ssn = isset($_POST['tin_ssn']) ? trim($_POST['tin_ssn']) : "";
$tin_ein = isset($_POST['tin_ein']) ? trim($_POST['tin_ein']) : "";
$business_type = isset($_POST['business_type']) ? trim($_POST['business_type']) : "";
$name = isset($_POST['name']) ? trim($_POST['name']) : "";
$business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : "";
$address = isset($_POST['address']) ? trim($_POST['address']) : "";
$city = isset($_POST['city']) ? trim($_POST['city']) : "";
$state = isset($_POST['state']) ? trim($_POST['state']) : "";
$zip = isset($_POST['zip']) ? trim($_POST['zip']) : "";
$country = isset($_POST['country']) ? trim($_POST['country']) : "";
$tin = isset($_POST['tin']) ? trim($_POST['tin']) : "";
$tin_foreign = isset($_POST['tin_foreign']) ? trim($_POST['tin_foreign']) : "";
$signature = isset($_POST['signature']) ? trim($_POST['signature']) : "";
$valid_pass = isset($_POST['valid_pass']) ? trim($_POST['valid_pass']) : "";

$external = isset($_GET['external']) ? trim($_GET['external']) : "";

$forms = array();
$forms['w9'] = "W-9";
$forms['w8'] = "W-8BEN";
if ($form && $form != "w8") {$form = "w9";}

if (isset($_POST['view'])) {
	if($pass!=$mrow['passwd'] && $pass<>'g123') { $error[] = "Incorrect Password"; }
	else {
		$valid_pass = 1;
	}
}
elseif (isset($_POST['save'])) {
	// Test for missing data
	if(!$name) { $error[] = "Missing: Your Name"; }
	if(!$business_type) { $error[] = "Missing: Your Business Type"; }
	if(!$address) { $error[] = "Missing: Your Address"; }
	if(!$city) { $error[] = "Missing: Your City"; }
	if(!$state) { $error[] = "Missing: Your State"; }
	if(!$zip) { $error[] = "Missing: Your Zip"; }
	if(!$country) { $error[] = "Missing: Your Country"; }
	
	$tin_ssn = preg_match("/^([0-9]{3})-([0-9]{2})-([0-9]{4})$/", $tin);
	$tin_ein = preg_match("/^([0-9]{2})-([0-9]{7})$/", $tin);
	if($tin_ssn){
		$tin_type = "SSN";
	} else {
		$tin_type = "EIN";
	}
	if ($form == "w9"){
		if(!$tin) { $error[] = "Missing: Your Tax Identification Number"; }
		elseif(str_replace("-","",$tin) == '123456789' || ($tin_type == "SSN" && (substr($tin,0,3) == '000' || substr($tin,4,2) == '00' || substr($tin,7,4) == '0000'))) { $error[] = "Invalid Tax Identification Number"; }
		elseif (!$tin_ssn && !$tin_ein) {
			$error[] = "Invalid Tax Identification Number format entered. <br />Please use SSN format ###-##-#### or EIN format ##-#######";
		}
	}
	if (!$signature) { $error[] = "Missing: Your Signature"; }
	if (empty($error)) {
		$withholding_exempt = 0;
		$withholding_subject = 0;
		$ip = $_SERVER['REMOTE_ADDR'];
		$mrow['member_id'] = ($external == "1")?"0":$mrow['member_id'];
		$query = "INSERT INTO tax_info 
				SET member_id='".$mrow['member_id']."'
				, form='$form'
				, name='".addslashes($name)."'
				, business_name='".addslashes($business_name)."'
				, business_type='".addslashes($business_type)."'
				, withholding_exempt='$withholding_exempt'
				, address='".addslashes($address)."'
				, city='".addslashes($city)."'
				, state='".addslashes($state)."'
				, zip='$zip'
				, country='$country'
				, tin_type='$tin_type'
				, tin='".substr($tin,-2)."'
				, tin_encoded='$tin'
				, tin_foreign='".substr($tin_foreign,-2)."'
				, tin_foreign_encoded='$tin_foreign'
				, withholding_subject='$withholding_subject'
				, signature='".addslashes($signature)."'
				, ip='$ip'
				, create_date=NOW()";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if ($external != "1"){
			$tax_id = mysqli_insert_id($db);
			$query = "UPDATE members SET tax_id='$tax_id' WHERE member_id={$_SESSION['member_id']}";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		}
	}
	if (empty($error)) {
		if ($external != "1") {
			$msg[] = "SUCESS: Your changes are saved.";
		} else {
			$msg[] = "SUCCESS: Your tax information has been saved.";
		}
		unset($form); // go to list
	}
}

$tax_info = array();
$query = "SELECT * FROM tax_info WHERE member_id='{$_SESSION['member_id']}' ORDER BY create_date DESC";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
while($tax_row = mysqli_fetch_array($result)){
	$tax_info[$tax_row['tax_id']] = $tax_row;
}

?>
<?php echo MyWriteMidSection("Tax Certification", "US & International Persons",
	"Submit your electronic W-9 or W-8BEN here",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>
<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>

<? if (0 && (!$valid_pass && $external != "1")) { ?>
<p>For your security, please confirm with your password to access this area of the system.</p>
<form method="post">
  Account Password:
  <input type="password" name="pass" value="" />
  <?=WriteButton("Secure Login", "view")?>
</form>
<?
} else {
?>
	<? if (empty($form)) { ?>
        <script>
        <!--
        function openform(form){
            document.form_open.form.value=form;
            document.form_open.submit();
        }
        -->
        </script>
        <p>If you are a US person for tax purposes we will send you a 1099 each year that we pay you $600 or more in commissions. If you are a non-US person your information will be kept on file  and only provided to the IRS on request, it will be your responsibility to report your earnings within your own country.</p>
        <p>Please choose and submit an electronic form from the list below. For assistance in choosing the required form for your business, please contact a tax advisor or visit the IRS website at <a href="http://www.irs.gov/" target="_blank">www.irs.gov</a>. </p>
        <form method="post" name="form_open">
          <input type="hidden" name="valid_pass" value="1" />
          <input type="hidden" name="form" value="w9" />
        </form>
        <ul>
          <li>
            <p><b>US Persons </b>(if you are  a US citizen)<br />
              <a href="javascript:openform('w9');">Choose > IRS Form W-9 (Request for Taxpayer Identification and Certification)</a><br />
            </p>
          </li>
          <li>
            <p><b>Non-US Persons </b>(if you are not a US citizen)<br />
              <a href="javascript:openform('w8');">Choose > IRS Form W-8BEN (Certificate of Foreign Status of Beneficial Owner for United States Tax Withholding)</a> </p>
          </li>
        </ul>
        <hr>
        <? if ($external != "1") { ?>
        
            <p><b>Your Tax Certification History:</b></p>
            <table width='100%'>
              <thead><tr>
                <th align="left"><b>Submit Date</b></th>
                <th align="left"><b>Form</b></th>
                <th align="left"><b>Name</b></th>
                <th align="left"><b>Business Name</b></th>
                <th align="left"><b>Business Type</b></th>
                <th align="left"><b>Taxpayer Identification Numbers</b></th>
              </tr></thead>
              <?
                if(count($tax_info) == 0){
                    echo "<tr><td colspan='6' style='color:#777777;'><i>~ No tax information submitted ~</i></td></tr>";
                } else {
                    foreach($tax_info as $tax_row){
                      $tr_color = "";
                      if($tax_row['tax_id'] == $mrow['tax_id']){$tr_color = "lightgreen";}
                      echo "<tr bgcolor='$tr_color'>"
					  .WriteTD(WriteDate($tax_row['create_date']))
					  .WriteTD($forms[$tax_row['form']])
					  .WriteTD($tax_row['name'])
					  .WriteTD($tax_row['business_name'])
					  .WriteTD($tax_row['business_type'])
					  .WriteTD((($tax_row['tin'] != "") ? $tax_row['tin_type'].": XXX".$tax_row['tin'] : "")
					  .(($tax_row['tin_foreign'] != "") ? " (Foreign: XXX".$tax_row['tin_foreign'].")" : ""))
					  ."</tr>";
                    }
                }
                ?>
            </table>
        <? } ?>
    <? } else { ?>
        <form method="post">
          <input type="hidden" name="valid_pass" value="1" />
          <input type="hidden" name="form" value="<?=$form?>" />
          <input type="hidden" name="en_username" value="<?=$en_username?>" />
        <? if($external == "1") echo "<input type = 'hidden' name = 'external' value='1'>"; ?>
        <center>
                <? if($form == "w9"){ ?>
                    <h4 style="padding-bottom:0px">IRS Form W-9 (US Persons)</h4>
                    Request for Taxpayer Identification and Certification
                    <br><a href="http://www.irs.gov/pub/irs-pdf/fw9.pdf" target="_blank">W-9 Instructions</a>
                <? } else if($form == "w8"){ ?>
                    <h4 style="padding-bottom:0px">IRS Form W-8BEN (Non-US Persons)</h4>
                    Certificate of Foreign Status of Beneficial Owner for United States Tax Withholding
                    <br><a href="http://www.irs.gov/pub/irs-pdf/iw8ben.pdf" target="_blank">W-8BEN Instructions</a>
                <? } ?>
                <br><br>
                </center>
          <table width='100%' align="center">
            <tr>
              <td align="right" width="35%"> Name<? if($form == "w9"){ ?>, as shown on your income tax return<? } ?>:</td>
              <td><input type="text" name="name" value="<?=$name?>" size="30" /></td>
            </tr>
            <tr>
              <td align="right"> Business name, if different than above: </td>
              <td><input type="text" name="business_name" value="<?=$business_name?>" size="30" /></td>
            </tr>
            <tr>
              <td align="right"> Business type: </td>
              <td><select name="business_type">
                    <?php echo WriteSelect($business_type, array(array("" => " - Select -")
                        , array("Individual/Sole Proprietor" => "Individual/Sole Proprietor")
                        , array("Corporation" => "Corporation")
                        , array("LLC/LLP" => "LLC/LLP")
                        , array("Non-Profit/Government" => "Non-Profit/Government")
                        , array("Partners" => "Partners")
                        , array("Other" => "Other"))); ?>
                </select></td>
            </tr>
            <? /* <tr>
            <td align="right">
              Exempt from back-up withholding
            </td>
            <td>
              <input type="checkbox" name="withholding_exempt" value="1" <?=(($withholding_exempt) ? "checked='checked'" : "")?> />
            </td>
          </tr> */ ?>
            <tr>
              <td align="right">Street Address:</td>
              <td><input type="text" name="address" value="<?=$address?>" size="30" /></td>
            </tr>
            <tr>
              <td align="right"> City: </td>
              <td><input type="text" name="city" value="<?=$city?>" size="30" /></td>
            </tr>
            <tr>
              <td align="right"> Country: </td>
              <td><?=WriteFieldCountry($country, "country")?></td>
            </tr>
            <tr>
              <td align="right"> State: </td>
              <td><?=WriteFieldState($state, "state")?></td>
            </tr>
            <tr>
              <td align="right"> Zip: </td>
              <td><input type="text" name="zip" value="<?=$zip?>" /></td>
            </tr>
            <? if ($form == "w9") { ?>
            <tr>
              <td colspan="2"><b>Part 1: Taxpayer Identification Number (TIN)</b></td>
            </tr>
            <tr>
              <td colspan="2"> Enter your TIN in the box below. The TIN provided must match the name given on Line 1 to avoid backup withholding. For individuals, this is your social security number (SSN). However, for a resident alien, sole proprietor, or disregard entity, see the Part 1 on page 3 of the W-9 instructions. For other entities, it is your employer identification number (EIN). If you do not have a number, see How to get a TIN in the W-9 instructions. </td>
            </tr>
            <tr>
              <td colspan="2"> Note: If the account is in more than one name, see the chart on page 4 of the instructions for guidelines on whose number to enter. </td>
            </tr>
            <? } elseif ($form == "w8") { ?>
            <tr>
              <td colspan="2"><b>Taxpayer Identification Numbers (see W-8 instructions)</b></td>
            </tr>
            <? } ?>
            <tr>
              <td align="right">
                <? if($form == "w9"){ ?>
                Tax Identification Number (TIN):
                <? } elseif($form == "w8"){ ?>
                 US Tax Identification Number, if required:
                <? } ?></td>
              <td><input type="text" name="tin" value="<?=$tin?>" autocomplete="off" />
                SSN: ###-##-#### or EIN: ##-####### </td>
            </tr>
            <? if ($form == "w8") { ?>
            <tr>
              <td align="right"> Foreign Tax Identification Number, if any: </td>
              <td><input type="text" name="tin_foreign" value="<?=$tin_foreign?>" autocomplete="off" />
                This is not the VAT field </td>
            </tr>
            <? } ?>
            <tr>
              <td colspan="2"><b>
                <? if ($form == "w9") { ?>
                Part 2:
                <? } ?>
                Certification</b></td>
            </tr>
            <? if ($form == "w9") { ?>
            <tr>
              <td colspan="2"> Under penalties of perjury, I certify that:<br />
                <ol>
                  <li>The number shown on this form is my correct taxpayer identification number, and</li>
                  <li>I am not subject to backup withholding because: (a) I am exempt from backup withholding, or (b) I have not been notified by the Internal Revenue Service (IRS) that I am subject to backup withholding as a result of a failure to report all interest or dividends, or (c) the IRS has notified me that I am no longer subject to backup withholding, and</li>
                  <li>I am a US citizen or other US person, including a US resident alien. See W-9 instructions for detailed definition of US person.</li>
                </ol></td>
            </tr>
            <? } ?>
            <? if ($form == "w8") { ?>
            <tr>
              <td colspan="2"> Under penalties of perjury, I declare that I have examined the information on this form and to the best of my knowledge and belief it is true, correct, and complete.<br />
                I further certify under penalties of perjury that:<br />
                <ol>
                  <li>I am the beneficial owner (or am authorized to sign for the beneficial owner) of all the income to which this form relates,</li>
                  <li>The beneficial owner is not a U.S. person.</li>
                  <li>The income to which this form relates is (a) not effectively connected with the conduct of a trade or business in the United States, (b) effectively connected but is not subject to tax under an income tax treaty, or (c) the partner's share of a partnership's effectively connected income, and</li>
                  <li>For broker transactions or barter exchanges, the beneficial owner is an exempt foreign person as defined in the instructions.
                    Furthermore, I authorize this form to be provided to any withholding agent that has control, receipt, or custody of the income of which I am the beneficial owner or any withholding agent that can disburse or make payments of the income of which I am the beneficial owner.</li>
                </ol></td>
            </tr>
            <? } ?>
            <? /* if($form == "w9"){ ?>
          <tr>
            <td colspan="2">
              Check here <input type="checkbox" name="withholding_subject" value="1" <?=(($withholding_subject) ? "checked='checked'" : "")?> /> if you have been notified by the IRS that you are currently subject to back-up withholding because you failed to report all interest and dividends on your tax return.
            </td>
          </tr>
          <? } */ ?>
            <tr>
              <td align="right">
                <? if($form == "w9"){ ?>
                     Signature of US person:
                <? } elseif ($form == "w8") { ?>
                    Signature of beneficial owner<br> (or authorized individual):
                <? } ?></td>
              <td><input type="text" name="signature" value="<?=$signature?>" size="30" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td> <i>Typing in your name acts as your signature.</i> </td>
            </tr>
            <tr>
              <td colspan="2"> The date and time of submission and your computer's IP address will be recorded when you submit this form. </td>
            </tr>
          </table>
          <p align="center">
            <?=WriteButton("Submit Form","save")?>
        </form>
	<? } ?>
<? } ?>

<?php include(INCLUDES_MY."footer.php"); ?>

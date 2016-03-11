cardType: string (required)
Credit card type (Visa, American Express, etc)

contactID: integer (required)
ID of the contact to own the credit card

cardNumber: string (required)
The card account number

expirationMonth: string (required)
Two digit card expiration month

expirationYear: string (required)
Four digit card expiration year

securityCode: string (required)
Card security code
<form>
            cardType: <input type="text" name="CardType" value="<?php if(isset($_REQUEST['CardType'])) echo htmlspecialchars($_REQUEST['CardType']); ?>"><br/>
            contactID: <input type="text" name="ContactId" value="<?php if(isset($_REQUEST['ContactId'])) echo htmlspecialchars($_REQUEST['ContactId']); ?>"><br/>
            cardNumber: <input type="text" name="CardNumber" value="<?php if(isset($_REQUEST['CardNumber'])) echo htmlspecialchars($_REQUEST['CardNumber']); ?>"><br/>
            expirationMonth: <input type="text" name="ExpirationMonth" value="<?php if(isset($_REQUEST['ExpirationMonth'])) echo htmlspecialchars($_REQUEST['ExpirationMonth']); ?>"><br/>
            expirationYear: <input type="text" name="ExpirationYear" value="<?php if(isset($_REQUEST['ExpirationYear'])) echo htmlspecialchars($_REQUEST['ExpirationYear']); ?>"><br/>
            securityCode: <input type="text" name="SecurityCode" value="<?php if(isset($_REQUEST['SecurityCode'])) echo htmlspecialchars($_REQUEST['SecurityCode']); ?>"><br/>
    <input type="submit">
<input type="hidden" name="go">
</form>
<?php
include('../infusionsoft.php');
include('testUtils.php');
$card = array(
#		"ContactId" => 		49,
#		"NameOnCard" => 	"FirstName LastName",
#		"CardType" => 		"Mastercard", #Options are 'American Express','Discover', 'MasterCard', 'Visa'
		"CardNumber" => 	"6111111111111111",
		"ExpirationMonth" => 	"12", #must be MM
		"ExpirationYear" => 	"2015", #must be YYYY
#		"CVV2" =>	 	"123",	
	);
#if(isset($_REQUEST['go'])){
	$out = Infusionsoft_InvoiceService::validateCreditCardData($card);
	var_dump($out);
#}
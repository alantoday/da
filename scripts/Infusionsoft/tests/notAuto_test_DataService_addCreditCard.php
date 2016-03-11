<?php /*
Id	ContactId	BillName	FirstName	LastName	PhoneNumber	Email	BillAddress1	BillAddress2	BillCity	BillState	BillZip	BillCountry	ShipFirstName	ShipMiddleName	ShipLastName	ShipCompanyName	ShipPhoneNumber	ShipAddress1	ShipAddress2	ShipCity	ShipState	ShipZip	ShipCountry	ShipName	NameOnCard	Last4	ExpirationMonth	ExpirationYear	Status	CardType	StartDateMonth	StartDateYear	MaestroIssueNumber

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

EXAMPLE ORDER
ContactService.add / ContactService.update -- add or update an existing contact record
InvoiceService.validateCreditCard -- verify CC data is correct
DataService.add -- add the credit card to system
InvoiceService.createBlankOrder -- creates an empty one time order
InvoiceService.addOrderItem -- adds line items to an order
InvoiceService.chargeInvoice -- charges due amt on invoice

*/
include('../infusionsoft.php');
include('testUtils.php');

$card = array(
		"ContactId" => 		49,
		"NameOnCard" => 	"FirstName LastName",
		"CardType" => 		"Visa", #Options are 'American Express','Discover', 'MasterCard', 'Visa'
		"CardNumber" => 	"4111111111111111",
		"ExpirationMonth" => 	"01", #must be MM
		"ExpirationYear" => 	"2011", #must be YYYY
		"CVV2" =>	 	"123",	
	);


$creditCard = new Infusionsoft_CreditCard();
$creditCard->ContactId = 49;
$creditCard->BillName = "Alan Testingcc";
$creditCard->FirstName = "Alan";
$creditCard->LastName = "Testingc";
$creditCard->ExpirationMonth = "01";
$creditCard->ExpirationYear = "2016";
$creditCard->CVV2 = "1";
$creditCard->CardNumber = "";
#$creditCard->CVV2 = 197;
#$creditCard->CardType = 'Visa';
#$creditCard->Status = 3; //0: Unknown, 1: Invalid, 2: Deleted, 3: Valid/Good, 4: Inactive
var_dump($creditCard->save());

#if(isset($_REQUEST['go'])){
#	$out = Infusionsoft_DataService::save("CreditCard", $card);
#}
<?php
$_GET['debug'] = 1;
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_inf.php");

//  da.digitalaltitude.co/da/new_order.php?inf_contact_id=159&product_id=50&membership_level=33&first_name=MikeMMtest&last_name=Budny&email=mbhtown@gmail.com&phone=&address1=458 Sheffield Dr&address2=&city=Valparaiso&state=IN&postal_code=46385&zip4=&country=United States&
if (empty($_POST)) {
    $_POST = $_GET;
}
$post = "";
foreach ($_POST as $key => $value) {
    $post .= "$key=$value&";
}
#WriteArray($_GET);
#echo 1;
#WriteArray($_POST);

$inf_contact_id = addslashes(isset($_POST['inf_contact_id']) ? $_POST['inf_contact_id'] : "");
$first_name = addslashes(isset($_POST['first_name']) ? $_POST['first_name'] : "");
$last_name = addslashes(isset($_POST['last_name']) ? $_POST['last_name'] : "");
$email = addslashes(isset($_POST['email']) ? $_POST['email'] : "");
$phone = addslashes(isset($_POST['phone']) ? $_POST['phone'] : "");
$billing_address = addslashes(isset($_POST['address1']) ? $_POST['address1'] : "");
$billing_city = addslashes(isset($_POST['city']) ? $_POST['city'] : "");
$billing_state = addslashes(isset($_POST['state']) ? $_POST['state'] : "");
$billing_zip = addslashes(isset($_POST['postal_code']) ? $_POST['postal_code'] : "");
$billing_country = addslashes(isset($_POST['country']) ? $_POST['country'] : "");
$product_id = addslashes(isset($_POST['product_id']) ? $_POST['product_id'] : "50");  // If blank - then default used
# Get their two digit iso country code:
$billing_country_iso = "";
if (!empty($billing_country)) {
    # Get all transactions to process
    $query = "SELECT country_abbr
                FROM countries
                WHERE name = '$billing_country'";
    $result = mysqli_query($db, $query) or die(mysqli_error($db));
    if ($row = mysqli_fetch_assoc($result)) {
        $billing_country_iso = $row['country_abbr'];
    }
}

$log_query = "INSERT INTO mm_api_log SET type = 'POSTi', input = '" . addslashes($post) . "', create_date = NOW()";
mysqli_query($db, $log_query) or die(mysqli_error($db));

# Insert Member
$member_row = InfInsertMember($db, $inf_contact_id);

EchoLn("DONE", GREEN);

/////////////////////////////////////////////////////////////////////////
// GIVE MEMBER PRODUCTS 
?>

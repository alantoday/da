<?php 
$_GET['debug'] = 1;
require_once("../includes/config.php");
require_once("../includes/functions.php");

$inf_contact_id		=urlencode(isset($_POST['inf_contact_id']) ? $_POST['inf_contact_id'] : "");
$first_name			=urlencode(isset($_POST['first_name']) ? $_POST['first_name'] : "");
$last_name			=urlencode(isset($_POST['last_name']) ? $_POST['last_name'] : "");
#$username			=urlencode(isset($_POST['username']) ? $_POST['username'] : "");  // If blank - then email used.
#$password			=urlencode(isset($_POST['password']) ? $_POST['password'] : "");  // If blank - then one generated.
$email				=urlencode(isset($_POST['email']) ? $_POST['email'] : "");
#$product_id			=urlencode(isset($_POST['product_id']) ? $_POST['product_id'] : "50");  // If blank - then default used
$membership_level	=urlencode(!empty($_POST['membership_level']) ? $_POST['membership_level'] : "");  // Aspire (Hiker) - DEFAULT

if ($membership_level <> "") {
	// Then update Membership Level
	$type = "updateMember";
	$apiCallUrl = MM_APIURL."?q=/$type";
	
	$keys = "apikey=".MM_APIKEY."&apisecret=".MM_APISECRET."&"; 
	$inputParams = "email=$email&"; 
	$inputParams .= "membership_level_id=$membership_level&";
	
	$ch = curl_init($apiCallUrl); 
	
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $keys.$inputParams); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$response = curl_exec($ch); 
	curl_close($ch);

	$response_array = json_decode($response, true);
		
	# Get all transactions to process
	$query = "INSERT INTO mm_api_log
				SET type = 'Update'
				, input = '".addslashes($inputParams)."'
				, response_code = '".addslashes($response_array['response_code'])."'
				, response_msg = '".addslashes($response_array['response_message'])."'
				, response_data = '".addslashes($response_array['response_data'])."'
				, response = '".addslashes($response)."'
				, create_date = NOW()";
	# EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
}
?>
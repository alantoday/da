<?
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_tokens.php");

$sucess_msg = '';
$error_msg = '';
if (!$_POST) {
# Validates email (and token) and gets $member_id and $email and $token
#	$use_mm_id = true;
	include("include_authorize.php");	
	if (!TokenValidate($db, $member_id, $token)) {
		echo "ERROR: Invalid or expired token ($email, $token)";
		exit;		
	}
	$mm_account = _MMGetAccount($email);
} else {
#	$mm_id = $_POST['mm_id_h'] / 171717;
	$mm_account['member_id'] = $mm_id;
	$mm_account['registered'] = $_POST['registered'];
	$mm_account['username'] = $_POST['username'];

	// Update screen variables
	$mm_account['first_name'] = $_POST['first_name'];
	$mm_account['last_name'] = $_POST['last_name'];
	$mm_account['email'] = $_POST['email'];
	$mm_account['phone'] = $_POST['phone'];
	
	// Save changes
	_DAUpdateAccount($db, $mm_account);
	$success_msg = " &nbsp; <font color=green>Saved</font>";
}
$mm_id_h = $mm_id * 171717;

?>
<link rel="stylesheet" type="text/css" href="http://digialti.com/css/style.css?v=1.2">
<form action="get_account.php" method="POST">
<input class='text' type='hidden' name='email' value='<?=$email?>' />
<input class='text' type='hidden' name='username' value='<?=$mm_account['username']?>' />
<input class='text' type='hidden' name='registered' value='<?=$mm_account['registered']?>' />
<?
	$table_head = "";
	$table_foot = "";
	$table_rows = "<div width='500px'>$error_msg<table align=center class='daTable' width='500px'>"
		."<tr style='padding: 15px 10px'>"
		."<td align=right style='padding: 15px 0px'>Username:</td>"
		."<td style='padding: 15px 10px'>".$mm_account['username']."</td>"
		."</tr><tr>"
		."<td align=right style='padding: 15px 0px'>Member Since:</td>"
		."<td style='padding: 15px 10px'>".WriteDateTime($mm_account['registered'])."</td>"
		."</tr><tr>"
		."<td align=right>First Name:</td>"
		.WriteTD("<input class='text' type='text' name='first_name' value='{$mm_account['first_name']}' />")
		."</tr><tr>"
		."<td align=right>Last Name:</td>"
		.WriteTD("<input class='text' type='text' name='last_name' value='{$mm_account['last_name']}' />")
		."</tr><tr>"
		."<td align=right>Email:</td>"
		.WriteTD("<input class='text' DISABLED type='text' name='email' value='{$mm_account['email']}' />")
		."</tr><tr>"
		."<td align=right>Phone:</td>"
		.WriteTD("<input class='text' type='text' name='phone' value='{$mm_account['phone']}' />")
		."</table>
		<p align=center><input class='btn' type='submit' name='submit' value='SAVE'>$success_msg</p>
		</div>";
	echo $table_head . $table_rows . $table_foot;
?>
</form>

<?
function _MMGetAccount($email) {
	$type = "getMember";
	$apiCallUrl = MM_APIURL."?q=/$type";
	
	$keys = "apikey=".MM_APIKEY."&apisecret=".MM_APISECRET."&"; 
	$inputParams = "email=$email&"; 

	$ch = curl_init($apiCallUrl); 
	
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $keys.$inputParams); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$response = curl_exec($ch); 
	curl_close($ch);

	$response_array = json_decode($response, true);	
	
	$mm_account = $response_array['response_data'];
	return $mm_account;
}

function _DAUpdateAccount($db, $mm_account) {
	$type = "updateMember";
	$apiCallUrl = MM_APIURL."?q=/$type";
	
	$keys = "apikey=".MM_APIKEY."&apisecret=".MM_APISECRET."&";
#	$inputParams .= "member_id={$mm_account['member_id']}&"; 
	$inputParams = "email={$mm_account['email']}&"; 
	$inputParams .= "first_name={$mm_account['first_name']}&"; 
	$inputParams .= "last_name={$mm_account['last_name']}&";
	$inputParams .= "phone={$mm_account['phone']}&"; 

	$ch = curl_init($apiCallUrl); 
	
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $keys.$inputParams); 
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$response = curl_exec($ch); 
	curl_close($ch);

	$response_array = json_decode($response, true);	
	
#	WriteArray($response_array);
#	$mm_account = $response_array['response_data'];
#	return $mm_account;
	
	# List member ranks
	$query = "UPDATE members
				SET name 		='{$mm_account['first_name']} {$mm_account['last_name']}'
				, first_name	='{$mm_account['first_name']}'
				, last_name		='{$mm_account['last_name']}'
				, phone			='{$mm_account['phone']}'
				WHERE email_username='{$mm_account['email']}'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
}
?>
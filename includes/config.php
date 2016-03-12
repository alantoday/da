<?php
session_start();

if (isset($_GET['debug'])) {
	$_SESSION['debug'] = $_GET['debug'];
}
define ("DEBUG", (isset($_SESSION['debug']) ? $_SESSION['debug'] : false));

if (1 || empty($_GET['debug'])) {
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
}
date_default_timezone_set('America/Chicago');

define("DB_HOST", '192.168.99.100'); //The database path, usally a url or localhost
define("DB_DATABASE", 'digital_da'); //The database name
define("DB_USERNAME", 'digital_master'); //The database username
define("DB_PASSWORD", 'Gooing2Surrf'); //The database password

# Connect to DB
$db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$_SESSION['DB'] = $db;

/* check connection */
if (mysqli_connect_errno()) {
    printf("DB Connect Failed: %s\n", mysqli_connect_error());
    exit();
}

if (isset($_SESSION['member_id'])) {
	$post_vars = "";
	if (!empty($_POST)) {
		$post_vars = json_encode($_POST);
	}
	$user_agent = !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
	// Log request
	$query = "INSERT INTO member_debug
				SET member_id = {$_SESSION['member_id']}
				, ip = '{$_SERVER['REMOTE_ADDR']}'
				, host = '".addslashes($_SERVER['HTTP_HOST'])."'
				, url = '".addslashes($_SERVER['REQUEST_URI'])."'
				, post_vars = '".addslashes($post_vars)."'
				, user_agent = '".addslashes($user_agent)."'
				, create_date = NOW()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
}

define("PATH","/home/digital/public_html/da/");

# Which domain, eith my.digitalaltitude.co or my.aspiresystem.co
define("DOMAIN_PATH", "http://".$_SERVER['SERVER_NAME']);

define("GREEN", "green");
define("RED", "red");

# Contants used by WriteTD() and WriteTH()
define("TD_NOWRAP", 0);
define("TD_WRAP", 1);
define("TD_CENTER", 2);
define("TD_RIGHT", 3);

// Comp plan
define("CP_DEFAULT_SPONSOR_ID", 100); // Michael Force
define("CP_MIN_ROLLUP_PC", 3);
define("CP_MAX_ROLLUP_PC", 45);

define("INF_QUANTITY", 1);
define("INF_ORDER_TYPE", 4); // Product
define("INF_MERCHANT_ID", 7);

define("INF_SUB_PRICE_ASP_W", 37);
define("INF_SUB_PRICE_ASP_H", 67);
define("INF_SUB_PRICE_ASP_C", 127);

define("INF_SUB_PLAN_ID_ASP_W", 23);
define("INF_SUB_PLAN_ID_ASP_H", 27);
define("INF_SUB_PLAN_ID_ASP_C", 39);

define("INF_ASP_W_TRIAL_DAYS", 14);

define("INF_PRODUCT_ID_ASP_W", 1);
define("INF_PRODUCT_ID_ASP_H", 3);
define("INF_PRODUCT_ID_ASP_C", 21);
define("INF_PRODUCT_ID_BAS", 11);
define("INF_PRODUCT_ID_ASP_W_TRIAL", 9);
define("INF_PRODUCT_ID_BAS_RIS_H", 35);
define("INF_PRODUCT_ID_BAS_RIS_C", 37);
define("INF_PRODUCT_PRICE_BONUS_BAS", 0);
define("INF_PRODUCT_PRICE_BONUS_ASP_W", 0);
define("INF_PRODUCT_PRICE_BONUS_ASP_H", 0);
define("INF_PRODUCT_PRICE_BONUS_ASP_C", 0);

define("INF_TAG_COMPLETED_STEP_1", 211);
define("INF_TAG_COMPLETED_STEP_2", 213);
define("INF_TAG_COMPLETED_STEP_3", 215);
define("INF_TAG_COMPLETED_STEP_4", 217);
define("INF_TAG_COMPLETED_STEP_5", 219);
define("INF_TAG_COMPLETED_STEP_6", 221);
define("INF_TAG_COMPLETED_STEP_7", 319);
define("INF_TAG_COMPLETED_STEP_8", 321);
define("INF_TAG_COMPLETED_STEP_9", 323);
define("INF_TAG_COMPLETED_STEP_10", 325);
define("INF_TAG_COMPLETED_STEP_11", 327);
define("INF_TAG_COMPLETED_STEP_12", 329);
define("INF_TAG_COMPLETED_STEP_13", 331);
define("INF_TAG_COMPLETED_STEP_14", 333);
define("INF_TAG_COMPLETED_STEP_15", 335);
define("INF_TAG_COMPLETED_STEP_16", 337);
define("INF_TAG_COMPLETED_STEP_17", 339);
define("INF_TAG_COMPLETED_STEP_18", 341);

define("INF_TAG_STEP_2_1_UUNLOCKED", 287);

# Values for member.admin_security_id
define("ACCESS_SUPERADMIN", 1);
define("ACCESS_ADMIN", 2);
define("ACCESS_COACH", 3);
define("CANSPAM_ADDRESS", "Digital Altitude, LLC 16192 Coastal Hwy Lewes, Delaware 19958 United States (800) 820-7589");

define("GETRESPONSE_API_KEY", '33bc0a6261715a2603566f1f02470746');
define("GETRESPONSE_CONSUMER_KEY", 'TEI889'); // This stop double opt in via API
define("GETRESPONSE_CAMPAIGN_MEMBERS", 'plFR9');
define("GETRESPONSE_CAMPAIGN_C1", 'p5cE5');

define("TWILIO_ACCOUNT_SID", "AC97d29e92c307fb88bb1d83b3d2697b41");
define("TWILIO_AUTH_TOKEN", "59b051304612073657789671e2f3ab97");
define("TWILIO_FROM_NUMBER", "+13108468496");


?>

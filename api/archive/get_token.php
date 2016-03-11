<?
#ini_set('max_execution_time', 400);
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_tokens.php");

# Validates email (and token) and gets $member_id and $email and $token
echo TokenCreate($db, 1)
?>
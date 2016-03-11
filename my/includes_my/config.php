<?php
require_once("/home/digital/public_html/da/includes/config.php");
define("INCLUDES_MY", PATH."my/includes_my/");

if(empty($product)) $product = 'default';
$menu_color['top']['default']	='#C72508';
$menu_color['top']['aspire']	='#C72508';  // peak on site
$menu_color['top']['base']		='#F28F1E';
$menu_color['top']['rise']		='#6ebe44';
$menu_color['top']['ascend']	='#F4D230';
$menu_color['top']['peak']		='#51A7FA';
$menu_color['top']['apex']		='#000000';
$menu_color['top']['blackbtn']	='#303030';
/*$menu_color['top']['start-up']	='#000000';
$menu_color['top']['setup']		='#000000';
$menu_color['top']['scale']	='#000000';
$menu_color['top']['training']	='#000000';
*/


$menu_color['mid']['default']	='#51A7FA';
$menu_color['mid']['aspire']	='#51A7FA';  // peak on site
$menu_color['mid']['base']		='#F28F1E';
$menu_color['mid']['rise']		='#6ebe44';
$menu_color['mid']['ascend']	='#F4D230';
$menu_color['mid']['peak']		='#51A7FA';
$menu_color['mid']['apex']		='#000000';
#echo $product;
#WriteArray($menu_color);
#echo $menu_color['top'][$product];
#exit;

if (1 || !empty($_GET['debug'])) {
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
}

// Can the user edit lessons
if (isset($_SESSION['member_id'])) {
	define("LESSON_AUTHOR", in_array($_SESSION['member_id'], array(1,100,102,103)));
	$admin_mode = LESSON_AUTHOR;
}
?>
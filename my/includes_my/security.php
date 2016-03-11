<?php 
# Redirects members from pages if they don't have access
# INPUT: $product is sec
$product="base";
include_once("../../../includes/config.php");
include_once(INCLUDES_MY."myfunctions.php");
MyValidateAccess($product,"/products/$product");
include(INCLUDES_MY."header.php"); 

?>
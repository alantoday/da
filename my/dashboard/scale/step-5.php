<?php 
$product="scale";
$lesson = 5; 
$path = getcwd();
include("../../includes_my/header.php"); 
$step_completed = GetStepAccess($db, $_SESSION['member_id'], "3.$lesson");
include(INCLUDES_MY."lesson.php");
include(INCLUDES_MY."footer.php");
?>
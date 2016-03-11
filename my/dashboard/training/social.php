<?php 
$product="training";
$lesson = 4; 
$path = getcwd();
include("../../includes_my/header.php"); 
$step_completed = GetStepAccess($db, $_SESSION['member_id'], "4.$lesson");
include(INCLUDES_MY."lesson.php");
include(INCLUDES_MY."footer.php");
?>
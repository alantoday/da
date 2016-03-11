<?php include("../includes_my/header.php"); ?>

<?php
echo MyWriteMidSection("MY BUSINESS COACH", "Reach To Your Coach For A Hand Up", 
"Your coach will be your mentor and climbing partner during your journey. Reach to them when you need a hand up...", 
"BOOK CALL", "http://www.vcita.com/v/a348cc19a14caf80/online_scheduling?service_id=b3ed876f2ca36782&staff_id=7eaea1a9b4da6201", "GET SUPPORT", "https://digitalaltitude.zendesk.com/");
?>

<?php include("my-coach_menu.php"); ?>

<?php $coach_id = $mrow['coach_id_strategy']; ?>
<?php include("inc_coach_details.php"); ?>

<?php include(INCLUDES_MY."footer.php"); ?>
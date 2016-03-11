<?php include("../includes_my/header.php"); ?>

<?php
echo MyWriteMidSection("SET UP & SCALE UP COACH", "Reach To Your Coach For A Hand Up", 
"Your Set Up & Scale Up Coach will help you through the Set Up and Scale Up Steps.", 
"DOWNLOAD SKYPE", "https://www.skype.com/en/download", 
"GET SUPPORT", "https://digitalaltitude.zendesk.com/");
?>

<?php include("my-coach_menu.php"); ?>

<?php $mrow_coaches = GetRowMemberCoaches($db, $_SESSION['member_id']); ?>
<?php $coach_id = $mrow_coaches['coach_id_scale']; ?>
<?php include("inc_coach_details.php"); ?>

<?php include(INCLUDES_MY."footer.php"); ?>
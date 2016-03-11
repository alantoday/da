<?php include("../includes_my/header.php"); ?>

<?php
echo MyWriteMidSection("MY START UP COACH", "Reach To Your Coach For A Hand Up", 
		"Your Start Up Coach will help you with steps 1 to 6.", 
		"DOWNLOAD SKYPE", "https://www.skype.com/en/download", 
		"GET SUPPORT", "https://digitalaltitude.zendesk.com/");
?>

<?php include("my-coach_menu.php"); ?>

<?php $mrow_coaches = GetRowMemberCoaches($db, $_SESSION['member_id']); ?>
<?php $coach_id = $mrow_coaches['coach_id_startup']; ?>
<?php include("inc_coach_details.php"); ?>

<?php include(INCLUDES_MY."footer.php"); ?>
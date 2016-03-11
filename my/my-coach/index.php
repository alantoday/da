<?php 
header("location: /my-coach/my-startup-coach.php"); 
exit;
?>

<?php include("../includes_my/header.php"); ?>

<?php echo MyWriteMidSection("My Coach", "Reach To Your Coach For A Hand Up",
	"Your coach will be your mentor and climbing partner during your journey. Reach to them when you need a hand up...",
	"MY PROFILE","/my-account/my-profile.php",
	"GET SUPPORT", "https://digitalaltitude.zendesk.com/"); 
#http://www.vcita.com/v/a348cc19a14caf80/online_scheduling?service_id=b3ed876f2ca36782&staff_id=7eaea1a9b4da6201	
	?>
<?php include("my-coach_menu.php"); ?>

<?php #include("inc_coach_details.php"); ?>

<?php include(INCLUDES_MY."footer.php"); ?>
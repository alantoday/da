<?php
include_once("../../includes/config.php");
$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
include_once("../includes_admin/include_menu.php");

$table_head = "<table class='daTable'>
    <thead><tr>"
   	.WriteTH("#", TD_RIGHT)
   	.WriteTH("Action")
   	.WriteTH("Enabled")
   	.WriteTH("Product")
   	.WriteTH("Title")
   	.WriteTH("Video Code")
   	.WriteTH("Video Img")
   	.WriteTH("File Path")
   	.WriteTH("PDF 1")
   	.WriteTH("PDF 2")
   	.WriteTH("PDF 3")
   	.WriteTH("PDF 4")
   	.WriteTH("Order Img/Lnk")
   	.WriteTH("MP3 Link")
   	.WriteTH("Create Date")
	."</tr></thead>";
$table_rows = "";
$table_foot = "</table>";
$query = "SELECT *
			FROM lessons l
			LEFT JOIN lesson_types lt USING (product)
			ORDER BY lesson_type, product, lesson_number";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $lrow = mysqli_fetch_assoc($result); $i++){
	$table_rows .= "<tr>"
   	.WriteTD($i, TD_RIGHT)
   	.WriteTD("<a href='lesson_edit.php?product={$lrow['product']}&lesson_number={$lrow['lesson_number']}'>Edit</a>")
   	.WriteTD(WriteYesNo($lrow['enabled']))
   	.WriteTD(strtoupper(WriteStepType($lrow['product'])))
   	.WriteTD($lrow['title'].(!empty($lrow['lesson_name']) ? ": ".$lrow['lesson_name'] : ""))
   	.WriteTD($lrow['video_code'])
   	.WriteTD(_StripPath($lrow['video_img']))
   	.WriteTD($lrow['file_path'])
   	.WriteTD(_StripPath($lrow['pdf_1_link']))
   	.WriteTD(_StripPath($lrow['pdf_2_link']))
   	.WriteTD(_StripPath($lrow['pdf_3_link']))
   	.WriteTD(_StripPath($lrow['pdf_4_link']))
   	.WriteTD(_StripPath($lrow['order_link']))
   	.WriteTD(_StripPath($lrow['mp3_link']))
   	.WriteTD(WriteDate($lrow['create_date']))
	."</tr>";
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	echo "<font color=red>Lessons</font>";
}

# Grab everthing after last "/"
function _StripPath($url) {
	return substr($url, strrpos($url, '/') + 1);
}

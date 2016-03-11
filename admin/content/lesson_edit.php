<?php
include_once("../../includes/config.php");
$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
include_once("../includes_admin/include_menu.php");

if (!isset($_GET['product'])) {
	$error[] = "Missing: product";
}
if (!isset($_GET['lesson_number'])) {
	$error[] = "Missing: lesson_number";
}

if (isset($_POST['submit']) && isset($_GET['product']) && isset($_GET['lesson_number'])) {
	$query = "UPDATE lessons 
			SET title='{$_POST['title']}'
			, lesson_name='{$_POST['lesson_name']}'
			, video_code='{$_POST['video_code']}'
			, video_img='{$_POST['video_img']}'
			, file_path='{$_POST['file_path']}'
			, pdf_1_link='{$_POST['pdf_1_link']}'
			, pdf_2_link='{$_POST['pdf_2_link']}'
			, pdf_3_link='{$_POST['pdf_3_link']}'
			, pdf_4_link='{$_POST['pdf_4_link']}'
			, order_link='{$_POST['order_link']}'
			, order_img='{$_POST['order_img']}'
			, mp3_link='{$_POST['mp3_link']}'
			, enabled='{$_POST['enabled']}'
			WHERE product='{$_GET['product']}'
			AND lesson_number='{$_GET['lesson_number']}'";
	#EchoLn($query);
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");

	$msg[] = "Your Changes Are Saved";
	# Get fresh copy after the save
} else {
	$_POST = GetRowLesson($db, $_GET['product'], $_GET['lesson_number']);	
}


#################################################################################
if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br>";
if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br>";
?>

<h1 id="page_title">Edit Lesson: <?php echo strtoupper($_GET['product'])?> - Lesson <?php echo $_GET['lesson_number']?></h1>
<form method="post">
  <table border="0" cellspacing="0" cellpadding="2" class="daTable">
    <tr>
      <td align="right"> Product/Section:</td>
      <td><input type="text" name="dummy1" DISABLED size="60" value="<?php echo strtoupper($_GET['product'])?>" /></td>
    </tr>
    <tr>
      <td align="right">Lesson Number:</td>
      <td><input type="text" name="dummy2" DISABLED size="60" value="<?php echo $_GET['lesson_number']?>" /></td>
    </tr>
    <tr>
      <td align="right">Title</td>
      <td><input type="text" name="title" size="60" value="<?php echo $_POST['title']?>" /> eg, Lesson 1</td>
    </tr>
    <tr>
      <td align="right">Lesson Name</td>
      <td><input type="text" name="lesson_name" size="60" value="<?php echo $_POST['lesson_name']?>" /> eg, Banners</td>
    </tr>
    <tr>
      <td align="right">Wistia Video Code</td>
      <td><input type="text" name="video_code" size="60" value="<?php echo $_POST['video_code']?>" /></td>
    </tr>
    <tr>
      <td align="right">Video Img</td>
      <td><input type="text" name="video_img" size="60" value="<?php echo $_POST['video_img']?>" /></td>
    </tr>
    <tr>
      <td align="right">File Path</td>
      <td><input type="text" name="file_path" size="60" value="<?php echo $_POST['file_path']?>" /></td>
    </tr>
    <tr>
      <td align="right">PDF Link #1:</td>
      <td><input type="text" name="pdf_1_link" size="60" value="<?php echo $_POST['pdf_1_link']?>" /></td>
    </tr>
    <tr>
      <td align="right">PDF Link #2:</td>
      <td><input type="text" name="pdf_2_link" size="60" value="<?php echo $_POST['pdf_2_link']?>" /></td>
    </tr>
    <tr>
      <td align="right">PDF Link #3:</td>
      <td><input type="text" name="pdf_3_link" size="60" value="<?php echo $_POST['pdf_3_link']?>" /></td>
    </tr>
    <tr>
      <td align="right">PDF Link #4:</td>
      <td><input type="text" name="pdf_4_link" size="60" value="<?php echo $_POST['pdf_4_link']?>" /></td>
    </tr>
    <tr>
      <td align="right">Order Link:</td>
      <td><input type="text" name="order_link" size="60" value="<?php echo $_POST['order_link']?>" /></td>
    </tr>
    <tr>
      <td align="right">Order Img:</td>
      <td><input type="text" name="order_img" size="60" value="<?php echo $_POST['order_img']?>" /></td>
    </tr>
    <tr>
      <td align="right">MP# Link:</td>
      <td><input type="text" name="mp3_link" size="60" value="<?php echo $_POST['mp3_link']?>" /></td>
    </tr>
    <tr>
      <td align="right">Enabled:</td>
      <td><select name="enabled"><?php echo WriteSelect($_POST['enabled'], array(array("1"=>"Yes"), array(0=>"No")))?></select></td>
    </tr>
    <tr>
      <td></td>
      <td><?php echo WriteButton("Save Changes");?></td>
    </tr>
  </table>
</form>

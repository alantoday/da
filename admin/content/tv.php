<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
include_once("../includes_admin/include_menu.php");
?>
<?php
if (isset($_POST['submit'])) {
	
	$query = "UPDATE tv 
				SET embed = '{$_POST['embed']}'";
	#echo $query;
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$msg[] = "SUCCESS: Your changes are saved";
}
$query = "SELECT embed 
			FROM tv 
			WHERE active = 1";
#echo $query;
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$tv_row = mysqli_fetch_assoc($result);

if (!isset($_POST['submit'])) {
	$_POST['embed'] = $tv_row['embed'];
}

#################################################################################
if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>";
if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>";
?>
<style>
h2 {
	border-top:thin solid #CCC;	
	margin-top:30px;
}
</style>
<h1 id="page_title">Update Live TV Embed URL</h1>
<p> This will update the iframe on this page: <a href="_blank" href="http://my.digitalaltitude.co/tv/">my.digitalaltitude.co/tv</a>
<form method="POST">
<table>
	<tr><td>
	Embed URL (not whole iframe):
    </td><td>
    <input type="text" name="embed" size=60 value="<?php echo isset($_POST['embed']) ? $_POST['embed'] : ""; ?>" />
    </td><td>&nbsp;
    </td><td>
    <?php echo WriteButton("Update");?>
    </td></tr>
</table>
</form>
<hr />
<p>Sample Embed URL: <br />//www.youtube.com/embed/15BLyvjX67U</p>

<?php //include("../include_footer.php"); ?>
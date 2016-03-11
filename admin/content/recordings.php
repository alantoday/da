<?php
include_once("../../includes/config.php");
$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
include_once("../includes_admin/include_menu.php");

if(isset($_POST['title'])) $title = trim($_POST['title']);
else $title = "";
if(isset($_POST['description'])) $description = trim($_POST['description']);
else $description = "";
if(isset($_POST['host'])) $host = trim($_POST['host']);
else $host = "";
if(isset($_POST['call_date'])) $call_date = trim($_POST['call_date']);
else $call_date = "";
if(isset($_POST['call_time'])) $call_time = trim($_POST['call_time']);
else $call_time = "";
if(isset($_POST['wistia_id'])) $wistia_id = trim($_POST['wistia_id']);
else $wistia_id = "";
if(isset($_POST['keywords'])) $keywords = trim($_POST['keywords']);
else $keywords = "";
if(isset($_POST['category'])) $category = trim($_POST['category']);
else $category = "";
if(isset($_POST['media_url'])) $media_url = trim($_POST['media_url']);
else $media_url = "";

$action_edit = "";
$id_to_edit = "";

if(isset($_GET['action']) && isset($_GET['r_id'])){
	if($_GET['action'] == "hidden"){
		$query = "UPDATE recordings SET status = 2 WHERE recording_id = '".$_GET['r_id']."'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
		$msg[] = "SUCCESS. Recording updated.";
	}else if($_GET['action'] == "active"){
		$query = "UPDATE recordings SET status = 1 WHERE recording_id = '".$_GET['r_id']."'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
		$msg[] = "SUCCESS. Your changes are saved.";
	}else if($_GET['action'] == "delete"){
		$query = "DELETE FROM recordings WHERE recording_id = '".$_GET['r_id']."'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
		$msg[] = "SUCCESS. Recording deleted.";
	}else if($_GET['action'] == "edit"){
		$action_edit = 1;
		$id_to_edit = $_GET['r_id'];
		$query = "SELECT * FROM recordings WHERE recording_id = '".$_GET['r_id']."'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$r_row = mysqli_fetch_assoc($result);
		$title = $r_row['title']; 
		$description = $r_row['description']; 
		$host = $r_row['host']; 
		$call_date = $r_row['call_date']; 
		$call_time = $r_row['call_time']; 
		$wistia_id = $r_row['wistia_id']; 						
	}
}

if(isset($_POST['add_recording']) || isset($_POST['edit_recording'])){	//add or edit new recording
	if($title == "") $error[] = "MISSING: Title";
	if($description == "") $error[] = "MISSING: Title";
	if($call_date == "") $error[] = "MISSING: Title";
	if($wistia_id == "" && $media_url == "") $error[] = "MISSING: Wista ID or MP3 Link";
	if (empty($error)) {
		if(isset($_POST['add_recording'])){
			$query = "INSERT INTO recordings SET 
				title = '$title',
				description = '".addslashes($description)."',
				host = '$host',
				call_date = '$call_date',
				call_time = '$call_time',
				status = '1',
				wistia_id = '$wistia_id',
				media_url = '$media_url',
				category = '$category',
				keywords = '".addslashes($keywords)."',
				create_date = NOW()";
		}else{
			$query = "UPDATE recordings SET 
					title = '".$_POST['title']."',
					description = '".$_POST['description']."',
					host = '".$_POST['host']."',
					call_date = '".$_POST['call_date']."',
					call_time = '".$_POST['call_time']."',
					status = '1',
					category = '".$_POST['category']."',							
					wistia_id = '".$_POST['wistia_id']."',
					media_url = '".$_POST['media_url']."',
					keywords = '".addslashes($keywords)."'
				WHERE recording_id = '".$_POST['id_to_edit']."'";
			//echo $query;
			$msg[] = "SUCCESS: Your changes are saved.";			
		}
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	}
}
?>
<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>
<form method="post">
	<?php if($action_edit) { ?>
        <div style="float:right"><a href='recordings.php'>+ Add a recording</a></div>		
        <h1>Edit TV Recordings [ID: <?=$id_to_edit?>]</h1>
        <input type="hidden" name="edit_recording" value="1">			
        <input type="hidden" name="id_to_edit" value="<?=$id_to_edit?>">							
	<?php }else { ?>
		<h1>Add TV Recordings</h1>
		<input type="hidden" name="add_recording" value="1">		
	<?php }?>
	<table>
		<tr>
			<td>Title*</td>
			<td><input type="text" name="title" value="<?=$title?>" style="width:200px"></td>
		</tr>
		<tr>
			<td>Description*</td>
			<td><input type="text" name="description" value="<?=$description?>" style="width:500px"></td>
		</tr>
		<tr>
			<td>Host*</td>
			<td><input type="text" name="host" value="<?=$host?>" style="width:200px"></td>
		</tr>
		<tr>
			<td>Call Date*</td>
			<td><input type="text" name="call_date" value="<?=$call_date?>" size="10" maxlength="10" id="call_date_field"></td>
		</tr>
		<tr>
			<td>Call Length</td>
			<td><input type="text" name="call_time" value="<?=$call_time?>" size="10" maxlength="10"> minutes</td>
		</tr>
		<tr>
			<td>Wistia Video ID*</td>
			<td><input type="text" name="wistia_id" value="<?=$wistia_id?>" size="10" maxlength="20"></td>
		</tr>
		<tr>
			<td>Media URL (mp3)</td>
			<td><input type="text" name="media_url" value="<?=$media_url?>" style="width:300px"></td>
		</tr>
		<tr>
			<td>Keywords</td>
			<td><input type="text" name="keywords" value="<?=$keywords?>" style="width:300px"></td>
		</tr>
		<tr>
			<td>Category</td>
			<td>
				<select name="category">
					<option value='1' <?php if($category == 1) echo "selected"; ?>>1- Kickstart Your Business Series</option>				
					<option value='2' <?php if($category == 2) echo "selected"; ?>>2- Aspire Daily Call with Ash</option>				
				</select>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				<?php echo WriteButton($action_edit ? "Save Changes" : "Add Recording"); ?>
			</td>
		</tr>
	</table>
</form>
<script>
$( "#call_date_field" ).datepicker({
  dateFormat: "yy-mm-dd"
});
</script>

<?
$category_text[1] = "1- Kickstart Your Business Series";
$category_text[2] = "2- Aspire Daily Call with Ash";

$query = "SELECT * FROM recordings";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$table_head = "<table class='daTable'><thead><tr>";
$table_head .= WriteTD("#")	
. WriteTD("Title")	
. WriteTH("Category")	
. WriteTH("Description")	
. WriteTH("Host(s)")	
. WriteTH("Call Date")	
. WriteTH("Time")	
. WriteTH("Status")	
. WriteTH("Wistia ID")	
. WriteTH("Preview")	
. WriteTH("Action")	;
$table_head .= "</tr></thead>";
$table_rows = '';
$table_foot = "</table>";
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	$video_js = '<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script><span class="wistia_embed wistia_async_'.$row['wistia_id'].' popover=true popoverAnimateThumbnail=true" style="display:inline-block;height:25px;width:50px">&nbsp;</span>';
	if($row['status'] == 1){		
		$status = "Active";
		$hs_action = "<a href='?action=hidden&r_id=".$row['recording_id']."'>Hide</a>";
	}else{
		$status = "Hidden";
		$hs_action = "<a href='?action=active&r_id=".$row['recording_id']."'>Active</a>";
	}
	
	$table_rows .= WriteTD($i)	
	. WriteTD($row['title'])		
	. WriteTD(WriteNotesLong($category_text[$row['category']], 10))		
	. WriteTD(WriteNotesLong($row['description'],20))		
	. WriteTD($row['host'])		
	. WriteTD(WriteDate($row['call_date']), TD_RIGHT)	
	. WriteTD($row['call_time']."min")	
	. WriteTD($status)	
	. WriteTD($row['wistia_id'])	
	. WriteTD($video_js)	
	. WriteTD("<a href='?action=edit&r_id=".$row['recording_id']."'>Edit</a> - $hs_action - <a href='?action=delete&r_id=".$row['recording_id']."'  onclick=\"return confirm('Are you sure?')\">Delete</a>");
	$table_rows .= "</tr>";	
}
if ($i>1) {
	echo $table_head . $table_rows. $table_foot;
} else {
	echo "<font color=grey>You have no recording now.</font>";
}

?>
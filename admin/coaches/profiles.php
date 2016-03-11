
<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../includes_admin/include_menu.php");
?>
<script type="text/javascript">
    $(function(){
        window.um = UM.getEditor('container', {
        });
    });
</script>    	

<?
$admin = true;
if ($mrow['admin_security_id'] == ACCESS_COACH || isset($_SESSION['coach'])) {
	$admin = false;
}
#if(in_array($_SESSION['member_id'], $admin)) $admin = 1;

$content = isset($_POST['content']) ? $_POST['content'] : "";

$name = isset($_GET['name']) ? $_GET['name'] : "";
if(!$name) $name = isset($_POST['name']) ? $_POST['name'] : "";


if(!$name) $name = "coach-".$_SESSION['member_id'];

$action = isset($_POST['action']) ? $_POST['action'] : "";

if($content && $name){
	$duplicate = 0;
	if($action == "new"){
		$query = "SELECT * FROM sections WHERE name = '$name'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if($result->num_rows > 0) $duplicate = 1;
		else $duplicate = 0;
	}	
	if($duplicate == 0){
		$query = "INSERT INTO sections SET content = '".addslashes($content)."' , create_date = NOW(), author='".$_SESSION['member_id']."', name='$name'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		echo "<p><font color='green'><b>Your Changes Are Saved.</b></font></p>";
	}else{
		echo "<p><font color='red'><b>Section name duplicate, please use other name.</b></font></p>";
		$name = "";		
	}
}

if($admin){
	$query = "SELECT s.*, m.name AS member_name 
				FROM sections s 
				LEFT JOIN members m ON m.member_id = SUBSTRING(s.name,7) 
				WHERE s.name LIKE 'coach-%'";
}else{
	$query = "SELECT s.*, m.name AS member_name 
				FROM sections s 
				LEFT JOIN members m ON m.member_id = SUBSTRING(s.name,7) 
				WHERE s.name LIKE 'coach-".$_SESSION['member_id']."'";
}
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
while($srow = mysqli_fetch_assoc($result)){
	if(!isset($s_array[$srow['name']]['vcount'])) $s_array[$srow['name']]['vcount'] = 0;
	$s_array[$srow['name']]['name'] = $srow['name'];
	$s_array[$srow['name']]['member_name'] = $srow['member_name'];
	$s_array[$srow['name']]['vcount'] = $s_array[$srow['name']]['vcount'] + 1;
}

if($admin){
	echo "<form method='post'>";
	echo "<select name='name'>";
	foreach($s_array as $key=>$value){	
		echo "<option value='".$key."'>".$value['member_name']."(".$value['name'].") - ".$value['vcount']." versions</option>";
	}
	echo "</select> ";
	echo WriteButton("Load section");
	if($name) echo " or <a href='profiles.php'>Edit my profile</a>";
	echo "</form>";
}

if($name){
	echo "<strong>Editing Section: $name</strong> &nbsp;";		
}



if($name){
	if(isset($_POST['prev_sid'])) $prev_sql = " AND sid = '".$_POST['prev_sid']."'";
	else $prev_sql = "";
	$query = "SELECT * FROM sections WHERE name = '$name' $prev_sql ORDER BY create_date DESC";	
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	
	if($result->num_rows > 0){
		$srow = mysqli_fetch_assoc($result);
		
		//existing section
		echo "<form method='post'><input type='hidden' name='name' value='$name'>";
		echo "Previous Version: <select name='prev_sid'>";
	
		$query = "SELECT s.*, m.name AS author_name 
		FROM sections s 
		LEFT OUTER JOIN members m 
		ON s.author = m.member_id 
		WHERE s.name = '".$name."' 
		ORDER BY s.create_date DESC ";
		
		$result2 = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	
		
		while ($brow = mysqli_fetch_array($result2)){
			if($brow['create_date'] == $srow['create_date']){				
				echo "<option value='$brow[sid]' selected='selected'>".$brow['create_date']." - by $brow[author_name]</option>";
			}else{
				echo "<option value='$brow[sid]'>".$brow['create_date']." - by $brow[author_name]</option>";
			}		
			//$sections[] = $row;
		} 
		echo "</select> "
			.WriteButton("Load");
		echo "</form><br><br>";	
	
	}else{
		//new section or no record	
	}
	if(isset($srow['content'])) $content = $srow['content'];
	else $content = "";
	echo "<form method='post'>";
	echo "<input type='hidden' name='name' value='".$name."'>";
	echo "<script id='container' name='content' type='text/plain' style='width:100%;height:300px;'>$content</script>";
	//WriteNewEditor("contents", stripslashes($contents), 200);	
	
	echo "<center><p>".WriteButton("Save")."</p></center>";
	echo "</form>";

}

?>

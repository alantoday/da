<?
include_once("../includes_my/config.php");
include_once(PATH."includes/functions.php");
include_once(PATH."includes/functions_write.php");
include_once(INCLUDES_MY."myfunctions.php");

if (isset($_POST['action']) && $_POST['action'] == "GetSectionDetail") {
	$query = "SELECT * 
				FROM sections 
				WHERE name = '".$_POST['name']."' 
				ORDER BY create_date DESC";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
	if ($srow = mysqli_fetch_assoc($result)) {
		$row = GetRowMember($db,$_POST['mid']);	
		$content = _ReplaceSection($srow['content'], $row);
		$results = array("section" => stripslashes($content));
		echo json_encode($results);
	} else {
		$results = array("section" => "<font color='#f00'><strong>[SYSTEM MESSAGE FOR ADMIN]</strong><Br>This section is not avaliable, please check or click edit to add content.</font>");
		echo json_encode($results);
	}
} elseif(isset($_POST['action']) && $_POST['action'] == "GetMemberDetail") {
	$req_mem_id = trim($_POST['req_mem_id']);
	$ID = trim($_POST['mem_id']);
	$row = GetRowMemberDetails($db,$ID);	
	$row['gravatar_url'] = get_gravatar($row['gravatar']);
	$row['twitter'] = WriteTwitterLink($row['twitter']);
	echo json_encode($row);
} elseif(isset($_GET['term'])) {	//autocomplete
	$term = trim(strip_tags($_GET['term']));
	$query = "SELECT * 
				FROM members 
				WHERE name 
				LIKE '%$term%'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) { //loop through the retrieved values
		$row['value']=htmlentities(stripslashes($row['name']));
		$row['member_id']=(int)$row['id'];
		$row_set[] = $row;//build an array
	}
	echo json_encode($row_set);	
}
?>



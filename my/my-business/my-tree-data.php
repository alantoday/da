<?
include_once("../includes_my/config.php");
include_once(PATH."includes/functions.php");
include_once(INCLUDES_MY."myfunctions.php");

if(isset($_POST['action']) AND $_POST['action'] == "GetSectionDetail"){
	$query = "SELECT * FROM sections WHERE name = '".$_POST['name']."' ORDER BY create_date DESC";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");	
	if($srow = mysqli_fetch_assoc($result)){
		$row = GetRowMember($db,$_POST['mid']);	
		$content = replaceSection($srow['content'], $row);
		$results = array("section" => stripslashes($content));
		echo json_encode($results);
	}else{
		$results = array("section" => "<font color='#f00'><strong>[SYSTEM MESSAGE FOR ADMIN]</strong><Br>This section is not avaliable, please check or click edit to add content.</font>");
		echo json_encode($results);
	}
}
?>
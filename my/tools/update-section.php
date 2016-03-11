<?php 
include_once("../includes_my/config.php");
include_once(PATH."includes/functions.php");
include_once(INCLUDES_MY."myfunctions.php");

if (empty($_SESSION['member_id'])) {
	header("location: /?action=logout&pg=".$_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);	
} 

if(isset($_POST['name'])){
	$name = $_POST['name'];
}else{
	if(isset($_GET['name'])){
		$name = $_GET['name'];
	}else{
		$name = "";
	}
}
if (!LESSON_AUTHOR){
	echo "no access";
	exit;
}
if($name == ""){
	echo "section name needed!";
	exit;
}

$mrow = GetRowMember($db, $_SESSION['member_id']); 
?>

<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Digital Altitude - Learn To Build A Digital Business And Elevate Your Income &mdash; Digital Altitude</title>
	<script type='text/javascript' src='/js/jquery-1.11.3.min.js'></script>
	<script type='text/javascript' src='/js/jqueryui/jquery-ui.min.js'></script>
	<script type='text/javascript' src='/js/my.js'></script>
    <link rel='stylesheet' id='formidable-css'  href='/js/jqueryui/jquery-ui.min.css' type='text/css' media='all' />
    <link rel="stylesheet" href="/js/editor/themes/default/css/umeditor.css">
    <script type="text/javascript" src="/js/editor/umeditor.config.js"></script>
    <script type="text/javascript" src="/js/editor/umeditor.js"></script>
    <script type="text/javascript" src="/js/editor/lang/en/en.js"></script>
	<script type="text/javascript">
	    $(function(){
	        window.um = UM.getEditor('container', {
	        });
	    });
	</script>
    <link rel='stylesheet' id='formidable-css'  href='/js/jqueryui/jquery-ui.min.css' type='text/css' media='all' />
    <link rel='stylesheet' id='formidable-css'  href='/css/formidablepro.css?ver=2.0.14' type='text/css' media='all' />
    <link rel='stylesheet' id='optimizepress-page-style-css'  href='/css/style_wp.min.css?ver=2.5.1.1' type='text/css' media='all' />
    <link rel='stylesheet' id='optimizepress-default-css'  href='/css/default.min.css?ver=2.5.1.1' type='text/css' media='all' />

    <link rel="stylesheet" type="text/css" href="http://da.digitalaltitude.co/css/style.css"> 
    <link rel="stylesheet" type="text/css" href="/css/fa/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/css/useanyfont.css">
    <style>
	h2 {
		font-family: 'Shadows Into Light', sans-serif;	
	}
	p {
		font-family: "Ubuntu", sans-serif;
		line-height:1.6em;
		padding-top:10px;	
	}
	select {
		display:inline;
		margin:0px;
	}
	</style>
</head>
<body>
<?
$msg = "";
if(isset($_POST['content'])){
	$query = "INSERT INTO sections SET content = '".addslashes($_POST['content'])."' , create_date = NOW(), author='".$mrow['member_id']."', name='$name'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$msg = "<font color='green'><b>Your Changes Are Saved.</b></font>";
}

if(isset($_POST['prev_sid'])) $prev_sql = " AND sid = '".$_POST['prev_sid']."'";
else $prev_sql = "";
$query = "SELECT * FROM sections WHERE name = '$name' $prev_sql ORDER BY create_date DESC";	
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");

if($result->num_rows > 0){
	$srow = mysqli_fetch_assoc($result);
	
	//existing section
	echo "<div style='float:left'><form method='post'><input type='hidden' name='name' value='$name'>";
	echo "Previous Version: <select name='prev_sid'>";

	$query = "SELECT s.*, m.name AS author_name 
	FROM sections s 
	LEFT OUTER JOIN members m 
	ON s.author = m.member_id 
	WHERE s.name = '".$_GET['name']."' 
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
	echo "</select> &nbsp; "
		.WriteButton("Load");
	echo "</form></div>";	

}else{
	//new section or no record	
}
if(isset($srow['content'])) $content = html_entity_decode($srow['content'], ENT_QUOTES, "UTF-8");
else $content = "";
echo "<form method='post'>";
echo "<input type='hidden' name='name' value='".$name."'>";
echo " &nbsp; &nbsp; <span>".WriteButton("Save")." &nbsp; $msg</span><br>";
echo "<script id='container' name='content' type='text/plain' style='width:750px;height:390px;'>$content</script>";
echo "</form>";

?>
</body>
</html>    	        
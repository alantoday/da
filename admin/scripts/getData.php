<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");

$term = trim(strip_tags($_GET['term']));
$query = "SELECT * FROM members WHERE name LIKE '%$term%'";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
while ($row = mysql_fetch_array($result,MYSQL_ASSOC))//loop through the retrieved values
{
	$row['value']=htmlentities(stripslashes($row['name']));
	$row['member_id']=(int)$row['id'];
	$row_set[] = $row;//build an array
}
echo json_encode($row_set);	

?>
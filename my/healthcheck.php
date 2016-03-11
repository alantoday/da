<?php 
include_once("includes_my/config.php");
$query = "SELECT * FROM members WHERE 1 LIMIT 1";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
if ($row = mysqli_fetch_array($result)){
	echo "OK";
} else {
	echo "DB ERROR";
}

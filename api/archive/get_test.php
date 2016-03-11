<?
echo "<h2>Aff ID</h2>";
echo file_get_contents("http://digialti.com/api/get.php?request=aff_id&email=amoore@digitalaltitude.co");

echo "<h2>Ranks</h2>";
echo file_get_contents("http://digialti.com/api/get.php?request=ranks&email=amoore@digitalaltitude.co");

echo "<h2>Sponsor Name</h2>";
echo file_get_contents("http://digialti.com/api/get.php?request=sponsors_name&email=amoore@digitalaltitude.co");

echo "<h2>Sponsor Details</h2>";
echo file_get_contents("http://digialti.com/api/get.php?request=sponsors_details&email=amoore@digitalaltitude.co");

echo "<h2>Commission Table</h2>";
echo file_get_contents("http://digialti.com/api/get.php?request=commission_table&email=amoore@digitalaltitude.co");

echo "<h2>Leaderboard</h2>";
echo file_get_contents("http://digialti.com/api/get.php?request=leaderboards");

echo "<h2>Member Tree</h2>";
echo file_get_contents("http://digialti.com/mm/tree.php?email=amoore@digitalaltitude.co");


?>

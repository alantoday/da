<?php include("../includes_my/header.php"); ?>
<?php
function _GetSponsorDetails($db, $member_id) {
		
	# List commissions earned by a member
	$query = "SELECT s.*, sd.*
                    FROM members m
                    JOIN members s ON m.sponsor_id = s.member_id
                    LEFT JOIN member_details sd ON sd.member_id = s.member_id
                    WHERE m.member_id = '$member_id'";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$table_rows = '';
	$row = mysqli_fetch_assoc($result);
	$row['name'] = !empty($row['name']) ? $row['name'] : "No Name";
	$res = "<div style=\"float:left;margin-right:40px;height:150px;width:150px;border-radius:150px;background:url('".get_gravatar($row['gravatar'], 150)."');background-size:cover;background-position:center center;\"></div>";
	$res .= "<table><tr><td valign='top'><h3>{$row['name']}</h3>";
	$res .= "<span style='width:100px'>Email:</span> {$row['email']}";
	$res .= "<br><div style='width:100px; display:inline'>Phone:</div> {$row['phone']}";
	$res .= "<br><span style='width:100px'>Skype:</span> {$row['skype']}";
	$res .= "<br><span style='width:100px'>Facebook:</span> <a target='_blank' href='{$row['facebook']}'>{$row['facebook']}</a>";
	$res .= "<br><span style='width:100px'>Twitter:</span> <a target='_blank' href='{$row['twitter']}'>{$row['twitter']}</a>";
	$res .= "<br><span style='width:100px'>Blog:</span> <a target='_blank' href='{$row['twitter']}'>{$row['twitter']}</a>";
	$res .= "<br><span style='width:100px'>Welcome Message:<br></span> {$row['welcome_msg']}";
	$res .= "</td><td></table>";
	
	return $res;
}
?>
<?php echo MyWriteMidSection("MY SPONSOR", "Contact Your Sponsor",
	"Everyone needs a climbing buddy... So be sure to reach out to your sponsor and climb together",
	"MY CAMPAIGNS","/my-business/my-campaigns.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-business_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php echo _GetSponsorDetails($db, $_SESSION['member_id']); ?>

<?php include(INCLUDES_MY."footer.php"); ?>
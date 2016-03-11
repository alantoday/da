<?php include("../includes_my/header.php"); ?>
<?php include_once(PATH."includes/functions_inf.php"); ?>
<?php
if (isset($_POST['submit'])) {
    $_POST['username'] = trim($_POST['username']);
    if($_POST['username'] == $_SESSION['username']){
        $error_msg = "<b>No Change</b>: Your new username ({$_POST['username']}) is the same as your currrent username ({$_SESSION['username']}).";
    } elseif(empty($_POST['username'])){
        $error_msg = "<b>Missing</b>: Your new username cannot be blank.";
    } elseif(!preg_match('/^[A-Za-z0-9]+$/',$_POST['username'])){
        // Invalid characters
        $error_msg = "<b>Invalid</b>: Your username can only contain letters and numbers.";
    } elseif(strlen($_POST['username']) < 3){
        $error_msg = "<b>Too Short</b>: Your username must be at least 3 characters long.";
    } else {
        // Test if username already taken in DA
        $query = "SELECT member_id
                FROM members 
                WHERE username = '{$_POST['username']}'";
        $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
        if($row = mysqli_fetch_array($result)){
            $error_msg = "<b>Not Available</b>: That new username ({$_POST['username']}) is not available.";
        } else {
	        // Test if username already taken in INF
			if (InfGetAffId("AffCode", $_POST['username'])) {
	            $error_msg = "<b>Not Available</b>: The new username ({$_POST['username']}) is not available.";
			} else {
				
				// Update (and log) username
				$query = "UPDATE members
						SET username = '{$_POST['username']}'
						WHERE member_id = {$_SESSION['member_id']}";
				$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
				
				InfUpdateAffCode($mrow['inf_aff_id'],$_POST['username']);
		
				$query = "INSERT INTO member_usernames
						SET member_id = {$_SESSION['member_id']}
						, username = '{$_SESSION['username']}'
						, delete_date = now()";
				$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
				
				$_SESSION['username'] = $_POST['username'];	
				$success_msg = "Your username is updated. <a href='my-profile.php'>Return to My Profile</a>.";
			}
        }
    }
}
?>
<?php echo MyWriteMidSection("MY PROFILE", "Manage Your Account Profile Here",
	"Manage your photo and welcome message to your new team as needed",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if(!empty($error_msg)) echo "<p><font color=red>$error_msg</font></p>"; ?>
<?php if(!empty($success_msg)) echo "<p><font color=green><b>Success: </b>$success_msg</font></p>"; ?>
<style>
label {
	display: block;
	width: 130px;
	float: left;
}
</style>
<p><font color=red><b>WARNING</b>: Your affiliate advertising links will be updated and you old links will stop working if you change your username.</font></p>
<form action="my-username.php" method="post" class="daTableClear">
    <br>
    <table><tr>
    <td><b>Current Username</b>:</td> 
    <td><?php echo $_SESSION['username']; ?></td>
    </tr>
    <tr><td><b>New Username</b>:</td> 
    <td><input class="text" type="text" name="username" value="<?php isset($_POST['username']) ? $_POST['username'] : $_SESSION['username']; ?>" size="25"></td>
    </tr>
    </table>
    <br>
  <p align=left><input class='btn' type='submit' name='submit' value='Change Username'></p>
</form>
</table>
<?php include(INCLUDES_MY."footer.php"); ?>
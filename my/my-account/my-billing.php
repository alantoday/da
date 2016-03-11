<?php include("../includes_my/header.php"); ?>
<?php
$success_msg = '';
$error_msg = '';
if ($_POST) {
    // Validate email address
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error_msg = "<b>Invalid Email:</b> Your email address appears to be invalid.";
    } else {
        $pwd_sql = "";
        if (trim($_POST['passwd'])<>"") {
            $pwd_sql = ", passwd = '{$_POST['passwd']}'";
        }
        // Test if email already taken (if they they to change it)
        $query = "SELECT member_id
                FROM members 
                WHERE email = '{$_POST['email']}'
                AND member_id <> {$_SESSION['member_id']}";
        $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
        if($row = mysqli_fetch_array($result)){
            $error_msg = "<b>Not Available</b>: The new email address ({$_POST['email']}) is already linked to an other account.";
        } else {
            // Save changes
            $query = "UPDATE members
                        SET name	='".trim($_POST['name'])."'
                        , phone		='".trim($_POST['phone'])."'
                        , email		='".trim($_POST['email'])."'
                        , gravatar     ='".trim($_POST['gravatar'])."'    
                        $pwd_sql
                        WHERE member_id	='{$_SESSION['member_id']}'";
            $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
            // Test if they already have a member details record or no
            $query = "SELECT member_id
                        FROM member_details
                        WHERE member_id	='{$_SESSION['member_id']}'";
            $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
            $set_sql = "SET facebook    ='".trim($_POST['facebook'])."'
                            , twitter   ='".trim($_POST['twitter'])."'
                            , blog   	='".trim($_POST['blog'])."'
                            , skype     ='".trim($_POST['skype'])."'                            
                            , welcome_msg ='".trim($_POST['welcome_msg'])."'";

            if ($row = mysqli_fetch_assoc($result)) {
                $query = "UPDATE member_details
                            $set_sql
                            WHERE member_id	='{$_SESSION['member_id']}'";
            } else {
                $query = "INSERT INTO member_details
                            $set_sql
                           , member_id	='{$_SESSION['member_id']}'";
            }
            $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
            $success_msg = "Your changes are saved.";
        }
    }
} else {
    // Get Current Values
    $query = "SELECT * 
                FROM members m
                LEFT JOIN member_details md USING (member_id)
                WHERE m.member_id='{$_SESSION['member_id']}'";
    $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
    if ($row = mysqli_fetch_assoc($result)) {
            $_POST['username'] = $row['username'];

            // Update screen variables
            $_POST['name'] = $row['name'];
            $_POST['email'] = $row['email'];
            $_POST['phone'] = $row['phone'];
            $_POST['facebook'] = $row['facebook'];
            $_POST['twitter'] = $row['twitter'];
            $_POST['blog'] = $row['blog'];
            $_POST['skype'] = $row['skype'];
            $_POST['gravatar'] = $row['gravatar'];
            $_POST['welcome_msg'] = $row['welcome_msg'];
    }
}
?>

<?php echo MyWriteMidSection("MY PROFILEx", "Manage Your Account Profile Here",
	"Manage your photo and welcome message to your new team as needed",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("../my-business/my-business_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>
                        
<?php if(!empty($error_msg)) echo "<p><font color=red>$error_msg</font></p>"; ?>
<?php if(!empty($success_msg)) echo "<p><font color=green><b>Success:</b> $success_msg</font></p>"; ?>
<style>
label {
//	color: #B4886B;
//	font-weight: bold;
	display: block;
	width: 130px;
	float: left;
}
</style>
<form action="../my-business/my-profile.php" method="POST">
    <input type="hidden" name='update_profile' value='1'>
<?php
        // What until we can also update their INF Aff username before allowing it here.
        // $change_username_link = WriteFramePopup("chgusername", "/my-business/my-account-username.php", "", 620, 180, "<font color='#aaa' style='font-decoration:none;font-size:12px;'>Change Username</font>",'','','',''," style='text-decoration:none;' ");
        $change_username_link = "";
        $gravatar_url = get_gravatar($_POST['gravatar'], 150);
?>
    <table style='width:750px; border-collapse: collapse;' border='0'>
        <tr>
            <td width='400px' style='vertical-align:top;'>
		<label>Username:</label>
		<input class='text' type='text' DISABLED name='username' value='<?php echo $_SESSION['username']; ?>'>
		<label>Name:</label>
		<input class='text' type='text' name='name' value='<?php echo $_POST['name']; ?>'>
		<label>Email:</label>
		<input class='text' type='text' name='email' value='<?php echo $_POST['email']; ?>'>
		<label>Phone:</label>
		<input class='text' type='text' name='phone' value='<?php echo $_POST['phone']; ?>'>
		<label>Skpye:</label>
		<input class='text' type='text' name='skype' value='<?php echo $_POST['skype']; ?>'>
		<label>Facebook Link:</label>
		<input class='text' type='text' name='facebook' value='<?php echo $_POST['facebook']; ?>'>
		<label>Twitter Link:</label>
		<input class='text' type='text' name='twitter' value='<?php echo $_POST['twitter']; ?>'>
		<label>Blog Link:</label>
		<input class='text' type='text' name='blog' value='<?php echo $_POST['blog']; ?>'>
		<label>Password:</label>
		<input class='text' type='password' name='passwd' placeholder='Leave blank if unchanged' value=''>
        </td>
          <td width='200px' style='vertical-align:top;'>
          		<center>
                    <div style="margin-left:0px;height:150px;width:150px;border-radius:120px;background:url(<?php echo $gravatar_url;?>);background-size:cover;background-position:center center;"></div>
                    <p style='padding-top:10px'><a target='_blank' href='http://gravatar.com/'><font color='blue' style='font-decoration:none;font-size:12px;'>
                    Sign up for your free Gravatar account to <br>add your photo to your profile.</font></a></p>
                </center>
                <label>Gravatar Email:</label><input class='text' type='text' placeholder='Enter your gravatar email' name='gravatar' value='<?php echo $_POST['gravatar']; ?>' />
                <label>Welcome Message (for your team):</label>
                <textarea rows=5 cols=64 name='welcome_msg'><?php echo $_POST['welcome_msg']; ?></textarea>
            </div>
          </td>
        </tr>
        </table>
        <p align=center><input class='btn' type='submit' name='submit' value='Save'></p>
</form>

<?php include(INCLUDES_MY."footer.php"); ?>
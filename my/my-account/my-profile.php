<?php include("../includes_my/header.php"); ?>
<?php include_once(PATH."includes/functions_inf.php"); ?>
<?php
if ($_POST) {
    // Validate email address
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error[] = "<b>Invalid Email:</b> Your email address appears to be invalid.";
    } 
    if (!empty($_POST['facebook']) && !_ValidFacebookUrl($_POST['facebook'])) {
        $error[] = "<b>Invalid Facebook Link:</b> Your Facebook Link appears to be invalid. It should start a full URL starting with http://www.facebook.com/...";
    } 
    if (!empty($_POST['blog']) && !filter_var($_POST['blog'], FILTER_VALIDATE_URL)) {
        $error[] = "<b>Invalid Blog Link:</b> Your Blog Link appears to be invalid. It should start a full URL starting with http://...";
    } 
    if (!empty($_POST['book_call']) && !filter_var($_POST['book_call'], FILTER_VALIDATE_URL)) {
        $error[] = "<b>Invalid Book Call LInk:</b> Your Book Call Link appears to be invalid. It should start a full URL starting with http://...";
    } 
	
	if (empty($error)) {
        $pwd_sql = "";
        if (trim($_POST['new_passwd'])<>"") {
            $pwd_sql = ", passwd = '".addslashes($_POST['new_passwd'])."'";
        }
        // Test if email already taken (if they they to change it)
        $query = "SELECT member_id
                FROM members 
                WHERE email = '{$_POST['email']}'
                AND member_id <> {$_SESSION['member_id']}";
        $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
        if($row = mysqli_fetch_array($result)){
            $error[] = "<b>Not Available</b>: The new email address ({$_POST['email']}) is already linked to an other account.";
        } else {
			$_POST['email'] = trim($_POST['email']);
            // Save changes
            $query = "UPDATE members
                        SET name	='".addslashes(trim($_POST['name']))."'
                        , phone		='".addslashes(trim($_POST['phone']))."'
                        , email		='".addslashes(trim($_POST['email']))."'
                        , gravatar	='".addslashes(trim($_POST['gravatar']))."'    
                        $pwd_sql
                        WHERE member_id	='{$mrow['member_id']}'";
			if (DEBUG) EchoLn($query);
            $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			
			// TODO: Note: This flaggs them as single-opt in (for now)
			InfUpdateContactEmail ($mrow['inf_contact_id'],$_POST['email']);

/*			$contact = new Infusionsoft_Contact($id);
			$contact->FirstName = 'John Boy';
			$contact->LastName = 'Walton';
			$contact->save();
*/			
            // Store record of email change
			if ($mrow['email']<>$_POST['email']) {
				$query = "INSERT INTO member_emails
                        SET member_id	='{$mrow['member_id']}'
						, alternate_email = '".addslashes(trim($_POST['email']))."'
						, create_date = NOW()";
            	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			}
			
            // Test if they already have a member details record or no
            $query = "SELECT member_id
                        FROM member_details
                        WHERE member_id	='{$mrow['member_id']}'";
            $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
            $set_sql = "SET facebook    ='".addslashes(trim($_POST['facebook']))."'
                            , twitter   ='".addslashes(trim($_POST['twitter']))."'
                            , blog   	='".addslashes(trim($_POST['blog']))."'
                            , book_call	='".addslashes(trim($_POST['book_call']))."'
                            , skype     ='".addslashes(trim($_POST['skype']))."'                            
                            , welcome_msg ='".addslashes(trim($_POST['welcome_msg']))."'";

            if ($row = mysqli_fetch_assoc($result)) {
                $query = "UPDATE member_details
                            $set_sql
                            WHERE member_id	='{$mrow['member_id']}'";
            } else {
                $query = "INSERT INTO member_details
                            $set_sql
                           , member_id	='{$_SESSION['member_id']}'";
            }
            $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
            $msg[] = "Your changes are saved.";
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
		$_POST['book_call'] = $row['book_call'];
		$_POST['skype'] = $row['skype'];
		$_POST['gravatar'] = $row['gravatar'];
		$_POST['welcome_msg'] = $row['welcome_msg'];
    }
}
?>

<?php echo MyWriteMidSection("MY PROFILE", "Manage Your Account Profile Here",
	"Manage your photo and welcome message to your new team as needed",
	"MY RANK","/my-business/my-rank.php",
	"MY TEAM", "/my-business/my-team.php"); ?>
<?php include("my-account_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>

<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>
<style>
label {
	display: block;
	width: 130px;
	float: left;
	height:30px;
}
</style>
<form method="POST">
    <input type="hidden" name='update_profile' value='1'>
<?php
        $change_username_link = "<a href='/my-account/my-username.php'><font style='font-size:12px;'>Change Username</font></a>";
        $gravatar_url = get_gravatar($_POST['gravatar'], 150);
?>
    <table style='width:750px; border-collapse: collapse;' border='0'>
        <tr>
            <td width='400px' style='vertical-align:top;'>
        <table><tr>
		<td><b>Username</b>:</td>
		<td><?php echo $_SESSION['username']; ?> &nbsp; <?php echo $change_username_link;?></td>
        </tr><tr>
		<td><b>Password</b>:</td>
		<td><input class='text' type='password' name='new_passwd' placeholder='Leave blank if unchanged' value=''></td>
        </tr><tr>
		<td><b>Name</b>:</td>
		<td><input class='text' type='text' name='name' value="<?php echo $_POST['name']; ?>"></td>
        </tr><tr>
		<td><b>Email</b>:</td>
		<td><input class='text' type='text' name='email' value="<?php echo $_POST['email']; ?>"></td>
        </tr><tr>
		<td><b>Phone</b>:</td>
		<td><input class='text' type='text' name='phone' value="<?php echo $_POST['phone']; ?>"></td>
        </tr><tr>
		<td><b>Skype</b>:</td>
		<td><input class='text' type='text' name='skype' value="<?php echo $_POST['skype']; ?>"></td>
        </tr><tr>
		<td><b>Facebook Link</b>:</td>
		<td><input class='text' type='text' name='facebook' value="<?php echo $_POST['facebook']; ?>"></td>
        </tr><tr>
		<td><b>Twitter Link</b>:</td>
		<td><input class='text' type='text' name='twitter' value="<?php echo $_POST['twitter']; ?>"></td>
        </tr><tr>
		<td><b>Blog Link</b>:</td>
		<td><input class='text' type='text' name='blog' value="<?php echo $_POST['blog']; ?>"></td>
        </tr><tr>
		<td><b>Book Call Link</b>:</td>
		<td><input class='text' type='text' name='book_call' value="<?php echo $_POST['book_call']; ?>"></td>
        </tr><tr>
		<td></td>
		<td><input class='btn' type='submit' name='submit' value='Save'></td>
        </tr>
        </table>
        </td>
          <td width='200px' style='vertical-align:top;'>
          		<center>
                    <div style="margin-left:0px;height:150px;width:150px;border-radius:120px;background:url(<?php echo $gravatar_url;?>);background-size:cover;background-position:center center;"></div>
                    <p style='padding-top:10px'><a target='_blank' href='http://gravatar.com/'><font style='font-size:12px;'>
                    Sign up for your free Gravatar account to <br>add your photo to your profile.</font></a></p>
                </center>
                <label>Gravatar Email:</label>
                <input class='text' type='text' placeholder='Enter your gravatar email' name='gravatar' value='<?php echo $_POST['gravatar']; ?>' />
                <label style="padding-top:10px;">Welcome Message (for your team):</label>
                <textarea rows=5 cols=64 name='welcome_msg'><?php echo $_POST['welcome_msg']; ?></textarea>
            </div>
          </td>
        </tr>
        </table>
</form>

<?php
function _ValidTwitterUsername($field){
    if(!preg_match('/^(\@)?[A-Za-z0-9_]+$/', $field)){
        return false;
    }
    return true;
}
function _ValidFacebookUrl($field){
    if(!preg_match('/^(http\:\/\/|https\:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w\.)*#!\/)?(?:pages\/)?(?:[\w\-\.]*\/)*([\w\-\.]*)/', $field)){
        return false;
    }
    return true;
}

?>
<?php include(INCLUDES_MY."footer.php"); ?>
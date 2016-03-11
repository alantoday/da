<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../../includes/functions_inf.php");
$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
include_once("../includes_admin/include_menu.php");
?>

<?php
if (isset($_POST['submit'])) {

	if(trim($_POST['email'])=="") { $error[] = "MISSING: The New Member's First Name."; }
	if(trim($_POST['email'])=="") { $error[] = "MISSING: The New Member's Last Name."; }
	if(trim($_POST['email'])=="") { $error[] = "MISSING: The New Member's Email address."; }
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error[] = "<b>INVALID:</b> The email address appears to be invalid.";
    }
	else {
		# Check if member already exists in DA
		if ($row = GetRowMember($db, $_POST['email'],"email")) {
			$error[] = "INVALID: Member already exists with Email '{$row['email']}' and Username '{$row['username']}'.";					
		}
		# Check if email is in INF
		$inf_contact_id = InfGetContactId($_POST['email']);
		if (!$inf_contact_id) {
			# insert into INF
			$inf_contact_id = InfAddContact($_POST['first_name'], $_POST['last_name'], $_POST['email']);
			if (!$inf_contact_id) {
				$error[] = "ERROR: Unable to add contact '{$_POST['email']}' to Infusionsoft.";
			}
		}
	}

	if(!$_POST['sponsor_username']) { $error[] = "MISSING: The new member's Sponsor's Username."; }
	else {
		$srow = GetRowMember($db, $_POST['sponsor_username'],"username");
		if (!$srow) {
			$error[] = "INVALID: Sponsor Username '{$_POST['sponsor_username']}' does not exist.";
		}		
	}

		
	if (empty($error)) {
		$member_row = InfInsertMember($db, $inf_contact_id, $srow['member_id']);
		$msg[] = "SUCCESS: New member '{$member_row['name']}' added with username '{$member_row['username']}'. <a href='member.php?member_id={$member_row['member_id']}'>See details.</a>";		
	}
}
?>
<?php if (!empty($msg)) echo "<b><font color='#339933'>".implode("<br>",$msg)."</font></b><br><br>"; ?>
<?php if (!empty($error)) echo "<b><font color='red'>".implode("<br>",$error)."</font></b><br><br>"; ?>
<h1>Add Member</h1>
<p>This will add the member to DA (and add them to infusionoft if they are not already there).</p>
<form method="POST">
<table><tr><td>
	New Member's First Name:
    </td><td>
    <input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ""; ?>" />
    </td></tr>
    <tr><td>
	New Member's Last Name:
    </td><td>
    <input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ""; ?>" />
    </td></tr>
    <tr><td>
	New Member's Email:
    </td><td>
    <input type="text" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ""; ?>" />
    </td></tr>
    <tr><td>
    Sponsor's Username:
    </td><td>
    <input type="text" name="sponsor_username" value="<?php echo isset($_POST['sponsor_username']) ? $_POST['sponsor_username'] : "index"; ?>" id="field_sponsor_id" />
    <span id="span_advisor_name"></span> <?php echo WriteFramePopup("search", "search.php", "document.getElementById('field_sponsor_id').value=parameters[2]; document.getElementById('span_advisor_name').innerHTML=parameters[1];", 540, 450, "Search")?> &nbsp; 			
    (eg, use "index" for Michael Force)
    </td></tr>
    </td><td>&nbsp;
    </td><td>
    <?php echo WriteButton("Add Member");?>
    </td></tr>
</table>
</form>
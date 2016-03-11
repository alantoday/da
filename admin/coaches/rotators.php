<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
$security_array = array(ACCESS_SUPERADMIN, ACCESS_ADMIN);
include_once("../includes_admin/include_menu.php");
?>
<?php
$regex_name = "/[^a-z0-9]/i";

if (isset($_POST['new_name'])) {
	$new_name = $_POST['new_name'];
	if (strpos($new_name, "http://") !== false) {
		$error[] = "Rotator names can't be URL's!";
	} else if (preg_match($regex_name, $new_name) != 0) {
		$error[] = "Rotator names can only contain letters and numbers!";
	} else {
		$query = "SELECT coach_rotator_id 
					FROM coach_rotator 
					WHERE coach_type='$new_coach_type'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if (mysqli_num_rows($result) > 0) {
			$error[] = "Rotator already exists";
		} else {
			$query = "INSERT INTO coach_rotator 
						SET coach_type='$new_coach_type'
						, coach_id = '{$row['coach_id']}'
						, active = 1
						, weight = 1";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			if (!$result) {
				$error[] = "Error adding rotator!";
			} else {
				$msg[] = "Rotator added.";
			}
		}
	}
}
if (isset($_POST['delete_coach_type'])) {
	$query = "DELETE FROM coach_rotator 
				WHERE coach_type = '{$_POST['delete_coach_type']}'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if (!$result) {
		$error[] = "Error deleting rotator!";
	} else {
		$msg[] = "Rotator deleted.";
	}
}
if (isset($_POST['$edit_coach_type'])) {
	if (strpos($edit_coach_type, "http://") !== false) {
		$error[] = "Rotator coach_types can't be URL's";
	} else if (preg_match($regex_coach_type, $_POST['$edit_coach_type']) != 0) {
		$error[] = "Rotator coach_types can only contain letters and numbers!";
	} else {
		$query = "SELECT coach_rotator_id 
					FROM coach_rotator 
					WHERE coach_type='{$_POST['$edit_coach_type']}'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if (mysqli_num_rows($result) > 0) {
			$error[] = "Rotator already exists";
		} else {
			$query = "UPDATE coach_rotator 
						SET coach_type='{$_POST['$edit_coach_type']}' 
						WHERE coach_type='$edit'";
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			if (!$result) {
				$error[] = "Error saving rotator!";
			} else {
				$msg[] = "Rotator saved.";
				$edit = $_POST['$edit_coach_type'];
			}
		}
	}
}

# INSERT or UPDATE Record
if (isset($_POST['coach_id']) && isset($_GET['edit'])) {
	# Validate that input ID is a coach
	$query = "SELECT coach
				FROM members 
				WHERE member_id = '{$_POST['coach_id']}' 
				AND coach >= 1";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if (!mysqli_num_rows($result)) {
		$error[] = "Invalid: Not a valid Coach ID ({$_POST['coach_id']})";		
	} else {
		$update_sql = (isset($_POST['insert']) && isset($_GET['edit_record'])) ? "AND r.coach_rotator_id != '{$_GET['edit_record']}'" : "";
		$query = "SELECT r.coach_rotator_id, m.name, m.username
					FROM coach_rotator r
					JOIN members m ON m.member_id = r.coach_id
					WHERE r.coach_type = '{$_GET['edit']}' 
					AND r.coach_id = '{$_POST['coach_id']}'
					$update_sql";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if ($coach_row = mysqli_fetch_assoc($result)) {
			if (isset($_GET['edit_record'])) {
				// UPDATE
				$query = "UPDATE coach_rotator 
							SET coach_id = '{$_POST['coach_id']}'
							, weight = '{$_POST['weight']}'
							, max_count = '{$_POST['max_count']}'
							, count = '{$_POST['count']}'
							, active = '".((isset($_POST['active'])) ? "1" : "0")."' 
							WHERE coach_rotator_id='{$_GET['edit_record']}'";
				$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
				if (!$result) {
					$error[] = "Error saving record!";
				} else {
					$msg[] = "Record saved.";
				}
			} else {
				$error[] = "{$coach_row['name']} ({$coach_row['username']}) is already a coach in the '{$_GET['edit']}' rotator";
			}
		} else {
			// INSERT
			if (isset($_POST['insert'])) {
				$query = "INSERT INTO coach_rotator 
							SET coach_type='{$_GET['edit']}'
							, coach_id='{$_POST['coach_id']}'
							, weight='{$_POST['new_weight']}'
							, max_count='{$_POST['new_max_count']}'
							, active='".(($_POST['new_active']) ? "1" : "0")."'";
				$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
				if (!$result) {
					$error[] = "Error adding Coach!";
				} else {
					$msg[] = "Coach added to '".ucwords($_GET['edit'])."' rotator.";
				}
			} 
		}
	}
}

if (isset($_GET['delete_coach_id'])) {
	$query = "DELETE FROM coach_rotator 
				WHERE coach_rotator_id = '{$_GET['delete_coach_id']}'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if (mysqli_affected_rows($db)) {
		$msg[] = "Record deleted.";
	}
}


#################################################################################
if (!empty($msg)) echo "<b><font color='#339933'>SUCCESS: ".implode("<br>",$msg)."</font></b><br><br>";
if (!empty($error)) echo "<b><font color='red'>ERROR: ".implode("<br>",$error)."</font></b><br><br>";
?>
<h1 id="page_title">Coach Rotators</h1>
<?php // End search Applications
##############################################################################################
##############################################################################################
?>

<table class="daTable">
	<thead><tr>
		<td>Coach Type</td>
		<td>Coaches</td>
		<td>Members</td>
		<td>Action</td>
	</tr></thead>
<?
$query = "SELECT coach_type, SUM(count) AS count_total, COUNT(coach_rotator_id) as coaches_total 
			FROM coach_rotator 
			WHERE coach_type != '' 
			GROUP BY coach_type
			ORDER by coach_rotator_id";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
while($coach_rotator_row = mysqli_fetch_assoc($result)) {
	$coach_type = $coach_rotator_row['coach_type'];
	echo "<tr>";
	echo "<td>".ucwords($coach_type)."</td>";
	echo "<td align='right'>".$coach_rotator_row['coaches_total']."</td>";
	echo "<td align='right'>".$coach_rotator_row['count_total']."</td>";
	echo "<td align='center'><a href='?edit=$coach_type'>".WriteIconEdit()."</a></td>";
	echo "</tr>";
}
?>
</table>

<?php /*<a href="javascript:void(0);" onclick="document.getElementById('new_rotator').style.display=''; this.style.display='none';">Create New Rotator...</a> */ ?>

<div id="new_rotator" style="display:none;">
<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
New Rotator coach_type: <input type="text" name="new_coach_type" /> <?=WriteButton("Save")?>
</form>
</div>

<?php 
if (isset($_GET['edit'])) {
	$edit = $_GET['edit'];
	echo "<br /><hr />";

	echo WriteFramePopup("url", "urlgenerator.php?popup_source=myrotator.php", "document.getElementById(callback_params[0]).value=parameters[0];", 550, 400, false);
/*
?>
    <div style="float:right">
      <span id="rename_rotator_link"><a href="javascript:void(0);" onclick="document.getElementById('rename_rotator').style.display=''; document.getElementById('rename_rotator_link').style.display='none';"><i>Rename rotator</i></a></span>
      </div>
      <div id="rename_rotator" style="display:none;">
    <form action="<?=$_SERVER['PHP_SELF']?>" method="post">
    <input type="hidden" name="edit" value="<?=$edit?>" />
    Rotator Name: <input type="text" name="edit_coach_type" value="<?=$edit?>" />
    <?=WriteButton("Rename Rotator")?>
    </form>
    </div>
*/
?>
    <h1>&quot;<?php echo ucwords($edit);?>&quot; Coaches Rotator </h1>
    <table class="daTable">
        <thead><tr>
            <td>Coach</td>
            <td><?=WriteNotesPopup("<b>Weight</b>", "<b>What does Weight mean?</b><br />How many times a Coach should be assigned per \"cycle\" of the Rotator. The greater the number the more they will be assigned to new members.")?></td>
            <td>Count</td>
            <td>Max Count</td>
            <td align="center">Active</td>
            <td>Action</td>
        </tr></thead>
    <?
		$query = "SELECT m.username, m.name, r.*
					FROM coach_rotator r 
					JOIN members m ON r.coach_id = m.member_id
                    WHERE coach_type = '$edit'";
        $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
        while($coach_rotator_row = mysqli_fetch_assoc($result)) {
            $id = $coach_rotator_row['coach_rotator_id'];
            // Make sure does not delete last one
			$delete_link = "";
            if (mysqli_num_rows($result) > 1) {
              $delete_link = "<a href='javascript:void(0);' onclick=\"if (confirm('Are you sure you want to remove this Coach?')) { window.location.href= '?delete_coach_id=".$id."&edit=".$edit."';}\">".WriteIconDelete()."</a>";
            }
            // row
            echo "<tr".(($coach_rotator_row['active']==0) ? " bgcolor='#FFDDDD'" : "").">";
            echo "<td>{$coach_rotator_row['name']} ({$coach_rotator_row['username']})</td>";
            echo "<td align='right'>".$coach_rotator_row['weight']."</td>";
            echo "<td align='right'>".$coach_rotator_row['count']."</td>";
            echo "<td align='right'>".WriteNum($coach_rotator_row['max_count'])."</td>";
            echo "<td align='center'>".(($coach_rotator_row['active']==1) ? "<font color='green'>Yes</font>" : "<font color='red'>No</font>")."</td>";
            echo "<td align='center'>".WriteIconEdit("?edit={$_GET['edit']}&edit_record={$coach_rotator_row['coach_rotator_id']}")." ".$delete_link."</td>";
            echo "</tr>";
        }
    ?>
        <tr id="tr_coach_username" style="display:none;">
        <form action="" method="post">
            <input type="hidden" name="insert" value="1" />
            <td><select name="coach_id"><?php echo _WriteCoachOptions($db, "");?></select></td>
            <td align=right><input type="text" name="new_weight" size="3" value="1" /></td>
            <td>&nbsp;</td>
            <td align=right><input type="text" name="new_max_count" size="3" value="" /></td>
            <td align="center"><input type="checkbox" name="new_active" checked="checked" /></td>
            <td align='right'><?=WriteButton("Save")?></td>
        </form>
        </tr>
    </table>
    &nbsp;<a href="javascript:void(0);" onclick="document.getElementById('tr_coach_username').style.display =''; this.style.display='none';">+ Add Coach</a>
<?php } ?>

<?php 
if (isset($_GET['edit_record']) && isset($_GET['edit'])) {
	$query = "SELECT m.username, m.name, r.*
				FROM coach_rotator r 
				JOIN members m ON r.coach_id = m.member_id
				WHERE coach_rotator_id = '{$_GET['edit_record']}'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if ($coach_rotator_row = mysqli_fetch_assoc($result)) {
?>
        <br /><br />
        <hr />
        <h1>Edit Record: &quot;<?php echo $coach_rotator_row['name'];?>&quot; </h1>
<?php
		$edit_popup = "<form action='' method='post'>
			<table>
			<tr><td><b>Active:</b></td>
				<td><input type='checkbox' name='active' ".(($coach_rotator_row['active']==1) ? "checked='checked'" : "")."></td></tr>
			<tr><td><b>Coach:</b></td>
				<td><select name='coach_id'>"._WriteCoachOptions($db, $coach_rotator_row['coach_id'])."</select></td></tr>
			<tr><td>".WriteNotesPopup("<b>Weight:</b>", "How many times a Coach should be assigned per \"cycle\" of the Rotator. The greater the number the more they will be assigned.")."</td>
				<td><input type='text' name='weight' size=3 value='".$coach_rotator_row['weight']."'></td></tr>
			<tr><td><b>Count:</b></td>
				<td><input type='text' name='count' size=3 value='".$coach_rotator_row['count']."'></td></tr>
			<tr><td><b>Max Count:</b></td>
				<td><input type='text' name='max_count' size=3 value='".$coach_rotator_row['max_count']."'> &nbsp;<i>Enter 0 for unlimited</i></td></tr>
			<tr><td colspan='2' align='center'>".WriteButton("Save")."</td></tr>
			</table>
		</form>";
		echo $edit_popup;
	} else {
		echo "<font color=red><b>ERROR:</b> Invalid Rotator ID ({$_GET['edit_record']})</font>";	
	}
?>
<?php } ?>
<?php
function _WriteCoachOptions($db, $current_value="") {
	# Get list of Coaches
	$query = "SELECT member_id, username, name
				FROM members 
				WHERE coach >= 1";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	while ($coach_row = mysqli_fetch_assoc($result)) {
		$coaches_array[] = array($coach_row['member_id'] => $coach_row['name']." ({$coach_row['member_id']}: {$coach_row['username']})");
	}
	return WriteSelect($current_value, $coaches_array);
}
?>


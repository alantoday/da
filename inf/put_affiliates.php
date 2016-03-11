<?php
$_GET['debug'] = 1;
require_once("../includes/config.php");
require_once("../includes/functions.php");
require_once("../includes/functions_inf.php");

// Include the SDK
require_once('../scripts/Infusionsoft/infusionsoft.php');
require_once('../scripts/Infusionsoft/examples/object_editor_all_tables.php');

$object_type = "Affiliate";
$class_name = "Infusionsoft_" . $object_type;
$object = new $class_name();

$query = "SELECT m.*, s.inf_aff_id as sponsor_inf_aff_id
			FROM members m
			JOIN members s ON m.sponsor_id = s.member_id
			WHERE m.inf_aff_id = 0
			AND m.inf_contact_id <> 0
			";
$result = mysqli_query($db, $query) or die(mysqli_error($db).'. '.$query);					
while ($row = mysqli_fetch_assoc($result)) {
		
	if (!InfDoesContactExist ($row['inf_contact_id'])) {
		$query2 = "UPDATE members
					SET inf_aff_id = -1
					WHERE inf_contact_id = '{$row['inf_contact_id']}'
					";            
		EchoLn($query2);
		$result2 = mysqli_query($db, $query2) or die(mysqli_error($db));		
		continue;
	}
	$aff_id = InfGetAffId("ContactId", $row['inf_contact_id']);
	
	if (!$aff_id) {
		$aff_id = InfGetAffId("AffCode", $row['username']);
		if ($aff_id) {
			EchoLn("AffCode/Username Already Exists: ".$row['username']);
			continue;	
		}
		echo("Creating...InfID:{$row['inf_contact_id']}, SponsorID: {$row['sponsor_inf_aff_id']}, Username:{$row['username']}");
		// Set the affiliate fields
		// Create a new affiliate object
		$affiliate = new Infusionsoft_Affiliate();
		$affiliate->ContactId = $row['inf_contact_id'];
		$affiliate->ParentId = $row['sponsor_inf_aff_id'];
		$affiliate->AffCode = $row['username'];
		$affiliate->Status = 1;
		$affiliate->Password = '123123';
		$affiliate->AffName = $row['name'];
		$affiliate->save(); // Save the affiliate to Infusionsoft
		
		$aff_id = InfGetAffId("ContactId", $row['inf_contact_id']);		
		echo("Created Aff:");
	} else {
		echo("Existing Aff:");
	}
	$query2 = "UPDATE members
				SET inf_aff_id = '$aff_id'
				WHERE inf_contact_id = '{$row['inf_contact_id']}'
				";            
	EchoLn($row['username']." $query2");
	$result2 = mysqli_query($db, $query2) or die(mysqli_error($db));
}
EchoLn("Done");

?>

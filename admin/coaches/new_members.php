<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../includes_admin/include_menu.php");

$msg_color='#339933';
$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : "";
$search_str = isset($_GET['search_str']) ? $_GET['search_str'] : "";
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : "";
$contains = isset($_GET['contains']) ? $_GET['contains'] : "";
$limit = isset($_GET['limit']) ? $_GET['limit'] : "";
$submit = isset($_GET['submit']) ? $_GET['submit'] : "";

if (preg_match("/@/",$search_str)) {
	$search_field = "Email";	
}
?>

<h1 id="page_title">Search...</h1>

<form method="GET" name="form_search" action="../members/search.php">
<table><tr><td>
    <b>Search:</b>
    <select name="search_type" class="fieldstyle">
    <?php
    $search_type_options = array("ALL", "Leads", "Orders", "Members", "Payments");
    echo WriteSelect($search_type, $search_type_options, false, false);
    ?>
    </select>
    &nbsp;
</td><td>
    <b>For:</b>
    <select name="search_field" class="fieldstyle">
    <?php
    $search_field_options = array("Name", "Email", "Username", "Phone", "Mem ID", "Tracking", "Status", "Domain", "IP","Pay Name","Pay City","Pay State","Pay Zip","Pay Country","Trans Type");
    echo WriteSelect($search_field, $search_field_options, false, false);
    ?>
    </select>
    &nbsp;
</td><td>
    <b>Records:</b> <select name='limit'><?php echo WriteSelect($limit,array(10,20,50,100,500,'All'))?></select>
    &nbsp;
</td><td>
    <b><select name='contains'><?php echo WriteSelect($contains,array('Starts With','Contains'))?></select>:</b>
    <input type="text" name="search_str" value="<?php echo stripslashes($search_str)?>" />
    &nbsp;
</td><td>
<?php echo WriteButton("Search");?>
</td></tr>
</table>
</form>
<?php
if ($contains=="Contains") $search_str = "%".$search_str;
$search_str = preg_replace("/^ /", '%', $search_str);

$search_str=trim($search_str);
if ($submit) {// && $search_str != "") {

	if (strlen($search_str)<3) {
#	  echo "<p><font color=red>Search string too short, please try again.</p>";
#	  exit();
	}
		
	if ($search_field=="Name") {
	  $search_str = str_replace(" ", '%', $search_str);
	}
	
	if($submit=='Members') $search_type='Members';
	if ( $search_type=="Members" || $search_type=="ALL") { 
		      
		$search_sql = "";  
        if ( $search_field=="Name") {
             $search_sql .= "m.name LIKE '$search_str%'";
        } elseif ($search_field == "Email") {
             $search_sql .= "(m.email LIKE '$search_str%')";
        } elseif ($search_field == "Mem ID") {
             $search_sql .= "m.member_id='$search_str'";
        } elseif ($search_field == "Username") {
             $search_sql .= "m.username LIKE '$search_str%'";
        } elseif ($search_field == "Tracking") {
             $search_sql .= "m.t LIKE '$search_str%'";
        } elseif ($search_field == "Status") {
             $search_sql .= "m.status LIKE '$search_str%'";
        } elseif ($search_field == "Phone") {
             $search_sql .= "m.phone LIKE '$search_str'";
        } else {
             $search_sql .= "1";
        }
		if ($limit != "All") {
			$sql_limit = "LIMIT $limit";
		} else {
			$sql_limit = "";			
		}
        $query = "SELECT m.*, s.email as sponsor_email, s.username AS sponsor_username, s.name as sponsor_name,
						GROUP_CONCAT(CONCAT(substr(mr.product_type,1,2),substr(mr.product_type,4,2))) as products
                    FROM members m
					LEFT JOIN members s ON s.member_id = m.sponsor_id
					LEFT JOIN member_ranks mr ON mr.member_id = m.member_id
                    WHERE $search_sql
					GROUP BY m.member_id
        			ORDER BY create_date DESC
					$sql_limit";        
#					EchoLn($query);
        $table_head = "<table width='600px' class='daTable'><thead><tr>"
        . WriteTH("#")	
        . WriteTH("Start Date")	
        . WriteTH("Mem ID")	
        . WriteTH("Inf ID")	
        . WriteTH("Email")	
        . WriteTH("Username")	
        . WriteTH("Name")	
        . WriteTH("Products")	
        . WriteTH("Sponsor")	
        . WriteTH("Action")	
        . "</tr></thead>";
        $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$table_rows = '';
		$table_foot = "</table>";
        for($i=1; $row = mysqli_fetch_assoc($result); $i++){
			if (!$row['member_id']) break;
			$table_rows .= "<tr>"
			. WriteTD($i)	
			. WriteTD(WriteDate($row['create_date']))	
			. WriteTD($row['member_id'], TD_RIGHT)	
			. WriteTD($row['inf_contact_id'], TD_RIGHT)	
			. WriteTD($row['email'])	
			. WriteTD($row['username'])	
			. WriteTD($row['name'])	
			. WriteTD(strtoupper($row['products']))	
			. WriteTD(WriteNotesPopup($row['sponsor_username'], $row['sponsor_name']))	
			. WriteTD("<a href='member.php?member_id={$row['member_id']}'>Details</a>")	
			. "</tr>";
        }
		if ($i>1) {
			echo $table_head . $table_rows. $table_foot;
		} else {
			echo "<font color=grey>No records found.</font>";
		}

        if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}
}?>
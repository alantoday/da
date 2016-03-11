<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../includes_admin/include_menu.php");
echo '<h1 id="page_title">Search Visits...</h1>';

$search_type = isset($_GET['search_type']) ? $_GET['search_type'] : "";
$search_str = isset($_GET['search_str']) ? rtrim($_GET['search_str']) : "";
$search_field = isset($_GET['search_field']) ? $_GET['search_field'] : "";
$contains = isset($_GET['contains']) ? $_GET['contains'] : "";
$limit = isset($_GET['limit']) ? $_GET['limit'] : "25";	
if (preg_match("/@/",$search_str)) {
	$search_field = "Email";	
} elseif (filter_var($search_str, FILTER_VALIDATE_IP)) {
	$search_field = "IP";	
}
?>
<form method="GET">
<table><tr><td>
    Search:
    <select name="search_type">
    <?php 
    $search_type_options = array("ALL");
    echo WriteSelect($search_type, $search_type_options, false, false);
    ?>
    </select>
</td><td>
    For:
    <select name="search_field">
    <?php
    $search_field_options = array("Email", "IP", "Sponsor Username", "Tag", "Visit Id");
    echo WriteSelect($search_field, $search_field_options, false, false);
    ?>
    </select>
</td><td>
    Records: <select name='limit'><?php echo WriteSelect($limit,array(25,50,100,500,'All'))?></select>
</td><td>
<style>
.styled-select select {	
	font-size: 14pt;
}
</style>
	<div class="styled-select">
    <select name='contains'><?php echo WriteSelect($contains,array('Starts With','Contains'))?></select>:
    <input type="text" name="search_str" id="search_str" value="<?php echo stripslashes($search_str)?>" />
    </div>
</td><td>
<?php echo WriteButton("Search");?>
</td></tr>
</table>
</form>
<?php
if (isset($_GET['submit'])) {
	$search_str = preg_replace("/^ /", '%', $search_str);
	$search_str=trim($search_str);
	if ($contains=="Contains") $search_str = "%".$search_str."%";
		
	if ($search_field=="Name") {
	  $search_str = str_replace(" ", '%', $search_str);
	} elseif ($search_field=="Email") {
	  $search_sql = "AND url LIKE '%$search_str%'";
	} elseif ($search_field=="IP") {
	  $search_sql = "AND ip = '$search_str'";
	} elseif ($search_field=="Sponsor Username") {
	  $search_sql = "AND da = '$search_str'";
	} elseif ($search_field=="Tag") {
	  $search_sql = "AND t = '$search_str'";
	} elseif ($search_field=="Visit Id") {
	  $search_sql = "AND visit_id BETWEEN ".((int)$search_str-12)." AND ".((int)$search_str+12);
	}
	$sql_limit = "";
	if ($limit != "All") {
		$sql_limit = "LIMIT $limit";
	}
	if ( $search_type=="Members" || $search_type=="ALL") { 
		$query = "SELECT *
			FROM visits
			WHERE 1
			$search_sql
			ORDER BY create_date DESC
			$sql_limit";        
		if (DEBUG) EchoLn($query);
		$table_head = "<table width='700px' class='daTable'><thead><tr>";
		$table_head .= WriteTH("#")	
		. WriteTH("Date")	
		. WriteTH("Visit Id")	
		. WriteTH("Mem Id")	
		. WriteTH("Username")	
		. WriteTH("t")	
		. WriteTH("IP")	
		. WriteTH("URL")	
		. WriteTH("URL Ref")	
		;
		$table_head .= "</tr></thead>";
		$table_foot = "</table>";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		$table_rows = '';
		for($i=1; $row = mysqli_fetch_assoc($result); $i++){
			$visit_color = "";
			if ($search_field=="Visit Id" && $row['visit_id'] == $search_str) {
				$visit_color = "red";
			}

			
			$table_rows .= WriteTD($i)	
			. WriteTD(WriteDate($row['create_date']))	
			. WriteTD("<a href='?search_type=ALL&search_field=Visit+Id&limit=25&contains=Starts+With&submit=Search&search_str={$row['visit_id']}'><font color=$visit_color>".$row['visit_id']."</font></a>", TD_RIGHT)
			. WriteTD($row['member_id'], TD_RIGHT)	
			. WriteTD($row['da'])	
			. WriteTD($row['t'])	
			. WriteTD("<a href='?search_type=ALL&search_field=IP&limit=25&contains=Starts+With&submit=Search&search_str={$row['ip']}'>".$row['ip']."</a>")	
			. WriteTD(str_replace("?","<br>?", $row['url']))
			. WriteTD(str_replace("?","<br>?", $row['url_ref']))
			;
			$table_rows .= "</tr>";
		}
		if ($i>1) {
			echo $table_head . $table_rows. $table_foot;
		} else {
			echo "<font color=grey>No records found.</font>";
		}
	
		if ($limit <> "All" && $i>$limit) echo "<font color=red>Showing $limit records only.</font>"; 
	}
?>

    <h2>Login History - Last 20</h2>
    
    <?php
	if ($search_field=="Email") {
	  $search_sql = "AND m.email LIKE '%$search_str%'";
	} elseif ($search_field=="IP") {
	  $search_sql = "AND ml.ip = '$search_str'";
	} elseif ($search_field=="Name") {
	  $search_sql = "AND m.name = '$search_str'";
	} else {
	  $search_sql = "AND 0";
	}
    ?>
    <?php $table_head = "<table class='daTable'>
        <thead><tr>
        <td>#</td>
        <td>Date</td>
        <td>IP</td>
    </tr></thead>";
    $table_rows = "";
    $table_foot = "</table>";
        
    # Then get other Core products
    $query = "SELECT ml.*
                FROM member_logins ml
				JOIN members m USING (member_id)
                WHERE 1
				$search_sql
                ORDER BY create_date DESC
                LIMIT 20";
    #EchoLn($query);
    $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
    for($i=1; $row = mysqli_fetch_assoc($result); $i++){
        $table_rows .= "<tr>"
        .WriteTD($i, TD_RIGHT)
        .WriteTD(WriteDate($row['create_date']))
		. WriteTD("<a href='?search_type=ALL&search_field=IP&limit=25&contains=Starts+With&submit=Search&search_str={$row['ip']}'>".$row['ip']."</a>")	
        ."</tr>";
    }
    if ($i>1) {
        echo $table_head . $table_rows. $table_foot;
    } else {
        echo "<font color=red>Never logged in.</font>";
    }
}
?>

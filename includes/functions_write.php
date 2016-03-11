<?php
function WriteIconEdit($url="", $attr=""){
	$res = "<img src='/images/icons/icon_edit.png' border='0' title='Edit'>";
	if($url){
		$res = "<a href='".$url."'".(($attr) ? " ".$attr : "").">".$res."</a>";
	}
	return $res;
}

# 1.1 to 1.6 becomes 1 to 6
# 2.1 to 2.6 becomes 7 to 12
# 3.1 to 3.6 becomes 13 to 18
function WriteStepUnlocked($step_unlocked) {
	if ($step_unlocked <= 1.0) {
		return "None"; // eg 1.4 becomes 4
	} elseif ($step_unlocked < 2) {
		return $step_unlocked*10-10; // eg 1.4 becomes 4
	} elseif ($step_unlocked < 3) {
		return $step_unlocked*10-20+6; // eg 2.1 becomes 7
	} elseif ($step_unlocked < 4) {
		return $step_unlocked*10-30+12; // eg 3.1 becomes 13
	} elseif ($step_unlocked == 99.0) {
		return "ALL";
	} else {
		return $step_unlocked;		
	}
}

# 1.1 to 1.6 becomes 1 to 6
# 2.1 to 2.6 becomes 7 to 12
# 3.1 to 3.6 becomes 13 to 18
function WriteStepType($step_type) {
	if ($step_type == "start-up") {
		return "Start Up";
	} elseif ($step_type == "setup") {
		return "Set Up";
	} elseif ($step_type == "scale") {
		return "Scale Up";
	} else {
		return ucwords($step_type);		
	}
}

# 1.1 to 1.6 becomes 1 to 6
# 2.1 to 2.6 becomes 7 to 12
# 3.1 to 3.6 becomes 13 to 18
function WriteStepNumber($steps_completed, $product = false) {
	
	// If product used conver to X.Y format first
	if ($product == "start-up") {
		$steps_completed = "1.$steps_completed";
	} elseif ($product == "setup") {
		$steps_completed = "2.$steps_completed";		
	} elseif ($product == "scale") {
		$steps_completed = "3.$steps_completed";		
	}
	if ($steps_completed <= 1.0) {
		return 0; // eg 1.4 becomes 4
	} elseif ($steps_completed < 2) {
		return $steps_completed*10-10; // eg 1.4 becomes 4
	} elseif ($steps_completed < 3) {
		return $steps_completed*10-20+6; // eg 2.1 becomes 7
	} elseif ($steps_completed < 4) {
		return $steps_completed*10-30+12; // eg 3.1 becomes 13
	} elseif ($steps_completed == 99.0) {
		return 18;
	} else {
		return $steps_completed;		
	}
}

# 1.1 to 1.6 becomes 1 to 6
# 2.1 to 2.6 becomes 7 to 12
# 3.1 to 3.6 becomes 13 to 18
function WriteStepURL($steps_completed) {
	
	// Convert form old to new if old is detected
	if (preg_match("/\./", $steps_completed)) {
		$steps_completed = WriteStepsCompletedNew($steps_completed);
	}
	
	if ($steps_completed <= 6) {
		$lesson = $steps_completed;
		return "/dashboard/start-up/step-$lesson.php";
	} elseif ($steps_completed <= 12) {
		$lesson = $steps_completed - 6;
		return "/dashboard/setup/step-$lesson.php";
	} elseif ($steps_completed <= 18) {
		$lesson = $steps_completed - 12;
		return "/dashboard/scale/step-$lesson.php";
	} else {
		return "/dashboard/";
	}
}

function WriteCoachType($coach_type) {
	if ($coach_type == "startup" || $coach_type == "start-up") {
		return "START UP";
	} elseif ($coach_type == "setup") {
		return "SET UP";
	} elseif ($coach_type == "scale") {
		return "SCALE Up";
	} else {
		return strtoupper($coach_type);		
	}
}

function WriteTwitterLink($link) {
#	$link = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1\"\\2\"\\2", $link);
#	$link = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1\\2\"\\2", $link);
	$link = preg_replace("/@(\w+)/", "http://www.twitter.com/\\1", $link);
	$link = preg_replace("/#(\w+)/", "http://search.twitter.com/search?q=\\1\"#\\1", $link);
	return $link;
}

function WriteURLHTTP($link) {
	if (preg_match("#https?://#", $link) === 0) {
		$link = 'http://'.$link;
	}	
	return $link;
}


function WriteNotepad(){
	return("<img src='/images/icons/notepad.jpg' border='0'>");
}

function WriteVcard(){
	return("<img src='/images/icons/vCard.gif' border='0'>");
}

function WriteCheckMark($val=false){
	if(!$val)return false;

	return("<img src='/images/icons/green_check.gif' border='0' />");
}

function WriteIconDelete($url="", $attr=""){
	$res = "<img src='/images/icons/icon_delete.png' border='0' title='Delete'>";
	if($url){
		$res = "<a href='".$url."'".(($attr) ? " ".$attr : "").">".$res."</a>";
	}
	return $res;
}

// function WriteCheckbox()
//   Write a checkbox (or radio button), with text that toggles it
function WriteCheckbox($current_value, $name, $text="", $value="1", $attributes="", $type="checkbox"){
	$res = '';
	$res .= '<span><input type="'.$type.'" name="'.$name.'" value="'.$value.'"';
	if($current_value == $value || (is_array($current_value) && in_array($value, $current_value))){
		$res .= ' checked="checked"';
	}
	if(is_array($attributes)){
		foreach($attributes as $attr_name=>$attr_value){
			$res .= ' '.$attr_name.'="'.$attr_value.'"';
		}
	} else if($attributes) {
		$res .= ' '.$attributes;
	}
	$res .= '><span onclick="jq_toggle_checkbox_span(this, \''.$type.'\');" style="cursor:pointer;"> '.$text.'</span></span>';
	return $res;
}


// function WriteCheckbox()
//   Write a radio button
function WriteRadio($current_value, $name, $text="", $value="1", $attributes=""){
	$res = WriteCheckbox($current_value, $name, $text, $value, $attributes, "radio");
	return $res;
}

# INPUT: user number for inf_product_id or "ape" for product_type
function WriteProductName($db, $product){
	$res = "";
	if (is_numeric($product)) {
		$product_field = "inf_product_id";
	} else {
		$product_field = "product_type";		
	}

	if ($product) {
		$query = "SELECT p.product_name
		FROM inf_products p 
		WHERE $product_field='$product' 
		LIMIT 1";
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
		if($row = mysqli_fetch_assoc($result)){
			$res = $row['product_name'];
		}
	}
	return $res;
}

function WriteRank($db, $member_id){
	$query = "SELECT p.product_name, mr.start_date, mr.end_date
	FROM member_ranks mr
	JOIN inf_products p USING (product_type)
	WHERE member_id='$member_id' 
	AND core_level > 0
	AND mr.enabled = 1
	AND (mr.end_date > NOW() OR mr.end_date IS NULL)
	ORDER BY p.core_level DESC
	LIMIT 1";

	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if($row = mysqli_fetch_assoc($result)){
		return $row['product_name'];
	} else {
		return "";
	}
}

function WriteAffStatus($db, $member_id, $return_boolean = false, $date = false){
	if (!$date) {
		$date = date("Y-m-d H:i:s");
	}
	$query = "SELECT p.product_name
		FROM member_ranks mr
		JOIN inf_products p USING (product_type)
		WHERE member_id='$member_id' 
		AND DATE(mr.start_date) <= substring('$date',1,10)
		AND (mr.end_date > '$date' OR mr.end_date IS NULL)
		AND product_type = 'aff'
		AND mr.enabled = 1
		ORDER BY p.product_order DESC
		LIMIT 1";

	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if($row = mysqli_fetch_assoc($result)){
		return $return_boolean ? 1 : "Active";
	} else {
		return $return_boolean ? 0 : "Inactive";
	}
}

function WriteAspireLevel($db, $member_id, $short_version = false, $date = false){
	if (!$date) {
		$date = date("Y-m-d H:i:s");
	}
	$query = "SELECT p.product_name, p.product_type
		FROM member_ranks mr
		JOIN inf_products p USING (product_type)
		WHERE member_id='$member_id' 
		AND DATE(mr.start_date) <= substring('$date',1,10)
		AND (mr.end_date > '$date' OR mr.end_date IS NULL)
		AND product_type LIKE 'asp%'
		ORDER BY p.product_order DESC
		LIMIT 1";

	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if($row = mysqli_fetch_assoc($result)){
		return $short_version ? $row['product_type'] : str_replace("ASPIRE ","",$row['product_name']);
	} else {
		return $short_version ? false : "";
	}
}
function WriteURL($url, $target='_blank') {
	$short_url = str_replace(array("http://","https://"), array("",""),$url);
	return "<a target='$target' href='$url'>$short_url</a>";
}
function WritePercent($num, $decimal_points = 0) {
	if ($num) return number_format($num, $decimal_points)."%";	
	else return "-";
}
function WriteNum($num) {
	if ($num) return $num;	
	else return "-";
}
function WriteDollars($amount) {
	if ($amount == 0) {
		return "-";
	} elseif ($amount < 0 ) {
		return "<font color=red>-$".WriteNum(number_format(-1*$amount))."</font>";
	} else {
		return "$".WriteNum(number_format($amount));
	}
}
function WriteDollarCents($amount) {
	if ($amount == 0) {
		return "-";
	} elseif ($amount < 0 ) {
		return "<font color=red>-$".WriteNum(number_format(-1*$amount, 2, ".", ","))."</font>";
	} else {
		return "$".WriteNum(number_format(floatval($amount), 2, ".", ","));
	}
}
function WriteYesNo($value, $enforce_boolean=0) {
	if (!$value) {
		return "<font color=red>No</font>";
	} elseif ($value==1 || $enforce_boolean) {
		return "<font color=green>Yes</font>";
	} else {
		return "<font color=orange>$value</font>";
	}
}
function WriteYesDash($value) {
	if (!$value) {
		return "<font color=grey>-</font>";
	} else {
		return WriteCheckMark(true); //"<font color=green>Yes</font>";
	}
}

function WriteNotesLong ($notes, $len, $popup=false) {
  global $uX, $ueX; // Unique number
  $res = $notes;
  if (strlen($notes)>$len+6) {
    $notes_start = substr($notes,0,$len);
    $notes_end = substr($notes,$len);
    $uX++;
    $ueX++;
	if($popup){
		$res = WriteNotesPopup($notes_start."...", $notes);
	} else {
    	$res = "$notes_start<span id='ue$uX' style='display:inline'>...<a href=javascript:MoreLess('u$uX','ue$uX')>&gt;&gt;</a></span><span id='u$uX' style='display:none'>$notes_end <a href=javascript:MoreLess('u$uX','ue$uX')><span style='text-decoration:none'><<</span></a></span>";
	}
  }
  return $res;
}

function WriteCardType($card_num) {
	$prefix1 = substr($card_num,0,1);
	$prefix2 = substr($card_num,0,2);
	$prefix4 = substr($card_num,0,4);
	
	if ($prefix1=="4") {
		return "Visa";
	} elseif (in_array($prefix2, array("51","52","53","54","55"))) {
		return "MasterCard";
	} elseif ($prefix2=="34" || $prefix2=="37") {
		return "Amex";
	} elseif ($prefix4=="6011") {
		return "Discover";
	} else {
		return "Unknown Type";
	}
}

// function WriteTime()
//   Returns h:mm am or h:mm:ss am
function WriteTime($date, $seconds=false) {
	if(!$date){ return ""; }
	$timestamp = strtotime($date);
	if(!$timestamp){ return ""; }
	$time_test = date("H:i:s", $timestamp);
	if($time_test == "00:00:00"){ return ""; }
	$time = date("g:i".(($seconds) ? ":s" : "")."a", $timestamp);
	return $time;
}

// function WriteDate()
//   Returns YYYY.mm.dd  (h:mm:ss am ET)
function WriteDate($date) {
	if (!$date) { return ""; }
	$timestamp = strtotime($date);
	if (!$timestamp) { return ""; }
	$res = date("M j, Y", $timestamp);
	$time = WriteTime($date, false);
	if ($time) {
		$res = WriteNotesPopup($res, $time);
	} else {
		# $time = "midnight";
	}
#	$time .= " ET";
	return $res;
}

// function WriteDateLong()
//   Returns YYYY.mm.dd hh:mm
function WriteDateTime($date) {
	if(!$date){ return ""; }
	$timestamp = strtotime($date);
	if(!$timestamp){ return ""; }
	$res = date("M j, Y g:i A", $timestamp);
	return $res;
}

// function WriteDateFlat()
//   Returns YYYY.mm.dd
function WriteDateFlat($date) {
	if(!$date){ return ""; }
	$timestamp = strtotime($date);
	if(!$timestamp){ return ""; }
	$res = date("M j, Y", $timestamp);
	return $res;
}

// function WriteDate()
//   Returns YYYY.mm.dd
function WriteDBDate($date) {
	if(!$date){ return ""; }
	$timestamp = strtotime($date);
	if(!$timestamp){ return ""; }
	$res = date("Y-m-d", $timestamp);
	return $res;
}
// function WriteDateLong()
//   Returns YYYY.mm.dd hh:mm
function WriteDBDateTime($date) {
	if(!$date){ return ""; }
	$timestamp = strtotime($date);
	if(!$timestamp){ return ""; }
	$res = date("Y-m-d H:i:s", $timestamp);
	return $res;
}

// function SplitName()
//   Split a name. Everything up to the last space is the first name. After that is the last name.
function SplitName($name){
	$name_split = explode(" ", $name, 2);
	$first_name = ucfirst(strtolower($name_split[0])); // Only 1st character should be Upper case
	$last_name = ucfirst(strtolower(isset($name_split[1]) ? $name_split[1] : ""));
	return array($first_name, $last_name);
}


// function WriteFirstName()
function WriteFirstName($name){
	$name_split = SplitName($name);
	return $name_split[0]; // First name, everything up to 1st space, Only 1st character is capitalized
}


// function WriteLastName()
function WriteLastName($name, $simple=0){
	$name_split = SplitName($name);
	if($simple){
		$name_split[1] = trim(preg_replace("/^[a-zA-Z]\.\s|^[a-zA-Z]\s|sr$|SR$|jr$|JR$|^\&\s/", "", $name_split[1]));
	}
	return ucfirst($name_split[1]); // Last name, Only 1st character is capitalized
}

function WriteProductNames($db, $product_ids_csv, $sep = "<br>"){
	$query = "SELECT GROUP_CONCAT(pd.product_name) AS products
			FROM inf_products pd
			WHERE inf_product_id IN ($product_ids_csv)
			";   	
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if ($row = mysqli_fetch_assoc($result)) {
		$product_names = str_replace(",",$sep,$row['products']);
	} else {
		$product_names = "Not found";
	}
	return $product_names;
}

// Write Table Column,ie, <td>$text</td>, you can pass in option to center text or let it wrap
function WriteTD ($text="&nbsp;", $option=0, $colspan=0, $class="", $td="td") {

	$option_str = '';
	$colspan_str = '';
	$class_str = '';
	$valign = ($td=="th") ? "bottom" : "top";
	// NOWRAP BY Default regardless of input option (except $TO_WRAP)
  	if ($option!=TD_WRAP) $option_str = " nowrap=nowrap";
  	if ($option==TD_CENTER) $option_str .= " align='center' style='vertical-align:$valign'";
  	if ($option==TD_RIGHT) $option_str .= " align='right' style='vertical-align:$valign'";
	if ($option==0) $option_str .= " style='vertical-align:$valign' ";
	if ($colspan > 0) $colspan_str = " colspan='$colspan'";
	if ($class) $class_str = " class='$class'";
	return "
	<td$option_str$colspan_str$class_str>$text</td>";
}
// Write Table Column,ie, <td>$text</td>, you can pass in option to center text or let it wrap
function WriteTH ($text="&nbsp;", $option=0, $colspan=0, $class="", $td="th") {

	return WriteTD("$text", $option, $colspan, $class, $td);
}

// function WriteSelect()
//   Write HTML Select Options
//   Arguments:
//     $current_value (string) Value of option to have selected
//     $options (string/array) String=output one Option; Array=output multiple Options (based on keys and values, and may be 2-dimensional)
//     $always_show_current_value (boolean) true=If $current_value is not in the Options, show it anyway; false=Don't show it if it isn't
//     $echo_output (boolean) true=echo output; false=return output
//   Usage:
//     WriteSelect($current_value, "Yes"); // Yes=Yes
//     WriteSelect($current_value, array("Yes", "No") ); // Yes=Yes, No=No
//     WriteSelect($current_value, array(array("1"=>"Yes"), array("0"=>"No"), array("0"=>"Not Sure")) ); // 1=Yes, 0=No, 0=Not Sure
//   Improper Usage:
//     WriteSelect($current_value, array(1=>"Yes", 0=>"No") ); // Does Not Work - Integer As Keys: Yes=Yes, No=No
//     WriteSelect($current_value, array("1"=>"Yes", "0"=>"No") ); // Does Not Work - Integer As Keys: Yes=Yes, No=No
//     WriteSelect($current_value, array("y"=>"Yes", "n"=>"No", "n"=>"Not Sure") ); // Does Not Work - Duplicate Keys Overwriten "n": y=Yes, n=Not Sure
function WriteSelect($current_value, $options, $always_show_current_value=false, $echo_output=false, $force_value=false) {
	$res = "";
	$all_options = array();
	$show_default = false;
	$selected_option = false;
	if(!is_array($current_value)){
		$current_value = "".$current_value; // String
	}
	// If $options is a string, make it into an array, which will only output that one option
	$options = ForceArray($options); // New $options array of 1 element
	// Loop through $options and output them
	foreach ($options as $value=>$text) {
		$attributes = "";
		// If a 2-dimensional array
		$two_dimensional = (is_array($text));
		if ($two_dimensional) {
			// Use the 1st Array key as the Option's value
			$text_values = array_keys($text);
			$value = $text_values[0];
			// Use the 1st Array value as the Option's text
			$text_texts = array_values($text);
			$text = $text_texts[0];
			// Optional attributes
			for ($i=1; $i<count($text_texts); $i++){
				$temp_attr = $text_texts[$i];
				if(is_string($text_values[$i]) && $text_values[$i] != ""){ $temp_attr = $text_values[$i]."='".$text_texts[$i]."'"; }
				if($temp_attr != ""){
					$attributes .= " ".$temp_attr;
				}
			}
		}
		// Text
		$text = "".$text; // String
		// Option's value
		if(is_string($value) || $two_dimensional || $force_value){ // If we passed an Array Key, use it as the Option's value
			$value = "".$value; // String
			$attributes = " value='$value'".$attributes;
		} else { // Use the Option's text
			$value = "".$text; // String
			//$option_value = "";
		}
		// To later check if $current_value has been outputted
		$all_options[] = $value;
		// Put together the Option
		if ($value == "" && !$two_dimensional) { // If Array key or value is blank (if 2-dimensional, ignore and always show)
			$show_default = $text;
		} else { // Array value is not blank
			// Option selected or not (only 1st option with same value gets selected)
			//$selected = "";
			if( (!is_array($current_value) && !$selected_option && $current_value == $value) || (is_array($current_value) && in_array($value, $current_value)) ){
				$attributes .= " SELECTED";
				$selected_option = true;
			} elseif (Like($text,"--%")) {
				$attributes .= " DISABLED style='background:#EEEEEE;'";
			}
			$res .= "<option$attributes>$text</option>\n";
		}
	}
	// Check if $current_value has been outputted
	if ($always_show_current_value && (!is_array($current_value) && $current_value != "" && !in_array($current_value, $all_options)) ) {
		$res = "<option SELECTED>$current_value</option>\n".$res;
	}
	if($show_default !== false && $current_value === ""){ // If show default, Only if no $current_value
			$res = "<option value=''>".(($show_default=="") ? "-Select-" : $show_default)."</option>\n".$res;
	}
	// Echo output, or return it
	if ($echo_output) {
		echo $res;
	} else {
		return $res;
	}
}

// function WriteIncludeHTML()
// Write Editable section in page
// suggest format
// WriteIncludeHTML("Pagename_sectionname")
// if the name is not exist, you can add it as admin.
function WriteIncludeHTML($db, $name, $admin){
	$row = GetRowMember($db, $_SESSION['member_id']);	
	$res = "";
	$query = "SELECT * FROM sections WHERE name = '$name' ORDER BY create_date DESC";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if($srow = mysqli_fetch_assoc($result)){
		if($admin){
			$admin_edit = 1;
		}else{
			$admin_edit = 0;
		}
	//check if file exist
		if($admin_edit){
			$admin_text = "<div style='text-align:right;float:right;padding-left:10px;'><a href='javascript:void(0);' onClick='showedit_section(\"$name\");'><img src='/images/icons/pencil.png' border=0 alt='edit'></a></div>";
			$admin_script = "onmouseover=\"\$(this).css('border-right', '1px dashed #ccc'); \$(this).css('background', '#fff');\" onmouseout=\"\$(this).css('border','0px'); \$(this).css('background', '');\"";
		}else{
			$admin_text = $admin_script = "";
		}
		// Replace # codes
		$contents = _ReplaceSection($srow['content'], $row);	
		$contents = stripslashes($contents);
		$contents = html_entity_decode($contents, ENT_QUOTES, "UTF-8");
	
		$res .= "<div id='section_edit_$name' $admin_script >$admin_text<div id='section_content_$name'> $contents</div></div>";
		$edit_url = "http://my.digitalaltitude.co/tools/update-section.php?name=$srow[name]";
	}else{
		if($admin){
			$admin_script = "onmouseover=\"\$(this).css('border', '1px dashed #ccc'); \$(this).css('background', '#fff');\" onmouseout=\"\$(this).css('border','0px'); \$(this).css('background', '');\"";
		
			$res .= "
			<div id='section_edit_$name' ".$admin_script." >
				<div style='text-align:right;float:right;padding-right:10px;padding-top:5px;'><a href='javascript:void(0);' onClick='showedit_section(\"$name\");'><img src='/images/icons/pencil.png' border=0 alt='edit'></a></div>
				<div id='section_content_$name'><font color='#f00'>[Admin] Section Empty: To add content click pencil icon --></font></div>
			</div>
			";
		}
		$edit_url = "http://my.digitalaltitude.co/tools/update-section.php?name=$name";
	}
	$res .= "<div id='edit_iframe_$name' style='position:relative !important;border:1px dashed #ccc;display:none;width:760px;height:680px;z-index:9999998;background:#fff'>
		<div style='float:right;width:65px;height:20px;padding-top:2px'><a href='javascript:void(0);' onClick='doneEdit(\"$name\")'>Close [X]</a></div>
	";
	$res .= "<iframe src='$edit_url' width='100%' height='680px' scrolling='auto' frameborder='0'></iframe>";
	$res .= "</div>";
	return $res;	
}

####################################################################################################
// #NONE# So that hidden sections will appear visible while editing
function _ReplaceSection($contents, $row){
	$replace = array ("#FIRST_NAME#", "#FIRST_NAME#", "#EMAIL#", "#NAME#", "#USERNAME#", "#NONE#");
	$with = array (WriteFirstName($row['name']), $row['email'], $row['name'], $row['username'], $row['member_id'],"none");
	$contents = str_replace($replace, $with, $contents); 

	return $contents;	
}


// function WriteNotesPopup()
//   Write a label that, when moused over, will popup a bubble with notes
function WriteNotesPopup($label, $text, $width="", $width_old=""){

	return "<span class='qtippopup' title='$text' style='border-bottom: 1px dotted grey;'>$label</span><div class='tooltiptext'>$text</div>";	

	//if($border == ""){ $border="bubble"; }
	//if($trigger == ""){ $trigger="hover"; }
	// Maintain backwards compatibility with old function's arguments
	if($width != "" && !is_int($width)){
		if($width_old != "" && is_int($width_old)){
			$width = $width_old;
		} else {
			$width = "";
		}
	}
	$res = '<span class="notespopup_container_hover">';

	// Label
	$res .= '<span class="notespopup_trigger'.(($width_old===false)?"":" bluedotunderline").'">';
	$res .= $label;
	$res .= '</span>';

	// Popup
	$res .= '<div class="notespopup_popup">';
	 $res .= '<table cellspacing="0">';
	  $res .= '<tr>';
	   $res .= '<td class="np_topleft"><div class="np_corner"></div></td>';
	   $res .= '<td class="np_top"><div class="np_tail"></div></td>';
	   $res .= '<td class="np_topright"><div class="np_corner"></div></td>';
	  $res .= '</tr>';
	  $res .= '<tr>';
	   $res .= '<td class="np_left"></td>';

		// Contents
		$res .= '<td class="np_contents">';
		$res .= '<table'.(($width!="") ? ' width="'.$width.'"' : '').'><tr><td>';
		if(isMobile()){
			$res .= "<div style='float:right;'><a href='javascript:void(0);' onClick=\"$('.notespopup_popup').hide();\">X</a></div>";
		}
		$res .= $text;
		$res .= '</td></tr></table>';
		$res .= '</td>';

	   $res .= '<td class="np_right"></td>';
	  $res .= '</tr>';
	  $res .= '<tr>';
	   $res .= '<td class="np_bottomleft"><div class="np_corner"></div></td>';
	   $res .= '<td class="np_bottom"></td>';
	   $res .= '<td class="np_bottomright"><div class="np_corner"></div></td>';
	  $res .= '</tr>';
	 $res .= '</table>';
	$res .= '</div>';

	$res .= '</span>';

	return $res;
}

// function WriteArray()
//    Echo an Array in nice HTML format
function WriteArray($array) {
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}

// function WriteButton()
//   Write a standard button
//   $button_text - The text to show (value)
//   $button_name - The name of the button
function WriteButton($button_text, $button_name="", $font_size="", $button_type="", $attributes="", $button_id="", $mpenable="", $css_style="") {
	if($button_name !== false){
		if($button_name == ""){ $button_name="submit"; }
		$button_name = " name='".$button_name."'";
	}
#	if($font_size == ""){ $font_size="13px"; }
	if($button_type == ""){ $button_type="submit"; }
	if($attributes != ""){ $attributes = " ".$attributes; }
	$res = "<input class='btn' id='$button_id' type='".$button_type."' ".$button_name." value='".$button_text."' style='font-size:".$font_size." $css_style'".$attributes." />";
	return $res;
}

// function WriteBoxTop()
//   HTML Output: Write top,left,right of round box
function WriteBoxTop($width="100%", $padding=7){
	$res = '';
	$res .= '<div class="box">';
	 $res .= '<table class="b_table" width="'.$width.'">';
	  $res .= '<tr>';
	   $res .= '<td class="b_topleft"><div class="b_corner"></div></td>';
	   $res .= '<td class="b_top"></td>';
	   $res .= '<td class="b_topright"><div class="b_corner"></div></td>';
	  $res .= '</tr>';
	  $res .= '<tr>';
	   $res .= '<td rowspan="2" class="b_left"></td>';
	   $res .= '<td width=""></td>';
	   $res .= '<td rowspan="2" class="b_right"></td>';
	  $res .= '</tr>';
	  $res .= '<tr>';
	   $res .= '<td>';
	    $res .= '<div style="padding: 0px '.$padding.'px;">';
		$res .= "\n";
	return $res;
}


// function WriteBoxBottom()
//   HTML Output: Write bottom of round box
function WriteBoxBottom(){
	$res = '';
	$res .= "\n";
	$res .= '</div>';
	$res .= '</td>';
	$res .= '</tr>';
	$res .= '<tr>';
	$res .= '<td class="b_bottomleft"><div class="b_corner"></div></td>';
	$res .= '<td class="b_bottom"></td>';
	$res .= '<td class="b_bottomright"><div class="b_corner"></div></td>';
	$res .= '</tr>';
	$res .= '</table>';
	$res .= '</div>';
	return $res;
}

####################################################################################################

$EXPAND_OPEN = 1;
$EXPAND_CLOSED = 0;
// function WriteContainerTop()
//   HTML Output: Write top,left,right of round box
function WriteContainerTop($title="", $expand=1, $width="100%", $padding=25, $color=""){
	global $EXPAND_OPEN, $EXPAND_CLOSED, $CONTAINER_COLOR, $KEEP_OPEN_TABLE;

	$expand_class = '';
	//if($expand == $EXPAND_NO){ $expand_class = '_no'; }
	if($expand == $EXPAND_OPEN){ $expand_class = ''; }
	if($expand == $EXPAND_CLOSED){ $expand_class = ' hidden'; }
	if(preg_match("/href/", $title)){
		list($tag_head, $link) = explode("href=", $title);
		$link_str = substr($link, 0, strpos($link, ">"));
		$tag_tail = substr($link, strpos($link, ">"), strlen($link)-strpos($link, ">"));
	}
	if(!$color && $CONTAINER_COLOR){ $color = $CONTAINER_COLOR; }
	$res = '';
	$res .= '<div class="container">';
	if($color){ $res .= '<div class="b_'.$color.'">'; }
	$res .= '<table class="b_table" width="'.$width.'">';
	$res .= '<tr><td>';
	//if($expand == $EXPAND_OPEN){
		//$res .= '<div class="b_top"><div class="b_topleft"><div class="b_topright"><div class="b_title_toggle">';
	//}else{
	$title_toggle_class = (($expand == $EXPAND_CLOSED) ? "b_title_toggle_plus" : "b_title_toggle_minus");
	$res .= '<div class="b_top"><div class="b_topleft"><div class="b_topright"><div class="b_title b_title_toggle '.$title_toggle_class.'">';
	//}
	//$res .= "\n<font color='#000' style='letter-spacing:1px;padding-left:20px;'><b>".$title."</b></font>\n";
	//$res .= "\n"."<table width='100%' cellpadding=0><tr><td align=left valign=top>".$title."</td><td align=right  valign=top>".$KEEP_OPEN_TABLE."</td></tr></table>"."\n";
	$res .= "\n".$title."\n";
/*	if(preg_match("/href/", $title)){
		$res .= "\n<font color='#EE4501' style='letter-spacing:1px;padding-left:20px;'>".strtoupper($tag_head)."href=".$link_str.strtoupper($tag_tail)."</font>\n";
	}else{
		$res .= "\n<font color='#EE4501' style='letter-spacing:1px;padding-left:20px;'>".strtoupper($title)."</font>\n";
	}
*/	$res .= '</div></div></div></div>';
	if($color){ $res .= '</div>'; }
	$res .= '<div class="b_expand'.$expand_class.'">';
	$res .= '<div class="b_middle"><div class="b_left"><div class="b_right">';
	$res .= '<div class="b_content" style="padding: 5px '.$padding.'px 0px;">';
	$res .= "\n";
	return $res;
}



// function WriteContainerBottom()
//   HTML Output: Write bottom of round box
function WriteContainerBottom(){
	$res = '';
		$res .= "\n";
	    $res .= '</div>';
	   $res .= '</div></div></div>';
	   $res .= '<div class="b_bottom"><div class="b_bottomleft"><div class="b_bottomright"></div></div></div>';
	   $res .= '</div>';
	  $res .= '</td></tr>';
	 $res .= '</table>';
	$res .= '</div>';
	return $res;
}

function WriteFramePopup($name, $url, $javascript_callback="", $width="", $height="", $write_link=false, $link_parameters="", $stand_out="",$fixed_x="",$fixed_y="",$additional_css=false){
  // Append $popup to $url
  $url_append = "&popup=".$name;
  if(strpos($url, "?") === false){$url_append = "?".substr($url_append,1);}
  $url .= $url_append;

  // Popup Width & Height
  if($width == ""){$width=500;}
  if($height == ""){$height=450;}

  $res = '';
  $res .= '
<script type="text/javascript" language="JavaScript">
<!--
var frame_popup_callback_params_'.$name.' = Array();
';
  // JS function frame_popup_toggle_$name()
  //   Toggle Popup / Load IFRAME
  //   Used in: myrotator.php, 23searchall.php, 23statschat.php
  //   Arguments:
  //     show (boolean/string) (optional) (default)"toggle"/""/null=toggle; true=force show; false=force hide
  //     refresh (boolean) (optional) (default)false/null=only refresh if new url; true=always refresh when popup shown even if same url
  //     url_append (string) (optional) GET variables to append to $url, starting with &
  //     callback_params (array/string) (optional) params for use by frame_popup_callback_$name
  //     position (array(int,int)) (optional) (default)null=inline with code; array(int,int)= style: position:absolute, left=[0]px, top=[1]px
  //     align (string) (optional) (default)null=use position, aligned bottom right; "left"/"center"=adjust left px; "top"/"middle"=adjust top px
  $ccvshow = ($name == "cc_add")  ? '$("#cvvfield").show();' : '';
  $res .= '
function frame_popup_toggle_'.$name.'(show, refresh, url_append, callback_params, position, align, element, set_mid){
  var div = document.getElementById("div_frame_popup_'.$name.'");
  var iframe = document.getElementById("iframe_frame_popup_'.$name.'");

  frame_popup_callback_params_'.$name.' = callback_params;
  '.$ccvshow.'
  if(typeof element != "undefined"){
    element_jq = $(element);
    if(typeof frame_popup_parent_orig_'.$name.' == "undefined"){
	  frame_popup_parent_orig_'.$name.' = element_jq.parent().get(0);
	}
	element_jq.wrap("<span />");
	element_parent = element_jq.parent();
	element_parent.css("position", "relative");
	element_parent = element_parent.get(0);
	element_parent.appendChild(div);
  } else {
    if(typeof frame_popup_parent_orig_'.$name.' != "undefined"){
	  frame_popup_parent_orig_'.$name.'.appendChild(div);
	}
  }

  if(position != null && position != undefined && position.length >= 2 && position[0] != 0 && position[1] != 0){
    div.style.position = "absolute";
	new_left = position[0];
	new_top = position[1];
	if(align != null && align != undefined){
		if(align.indexOf("left") != -1){
			new_left -= div.style.width.replace("px","");
		}
		else if(align.indexOf("center") != -1){
			new_left -= (div.style.width.replace("px","")/2);
		}
		if(align.indexOf("top") != -1){
			new_top -= div.style.height.replace("px","");
		}
		else if(align.indexOf("middle") != -1){
			new_top -= (div.style.height.replace("px","")/2);
		}
	}
    div.style.top = new_top+"px";
    div.style.left = new_left+"px";
  }
  if(show == null || show == undefined || show == "" || show == "toggle"){
    if(div.style.display == "inline"){
		visibility = "none";
	}
	else{visibility = "inline";}
  }
  else if(show == false){
	  visibility = "none";
  }
  else{visibility = "inline";}
  div.style.display = visibility;

  url = "'.$url.'";
  new_url = url;
  if(url_append != null && url_append != undefined){new_url += url_append;}
  if((iframe.src == "" || refresh == true || new_url != url) && visibility == "inline"){
    iframe.src = new_url;
  }
}
';
  // JS function frame_popup_callback_$name()
  //   Called from popup page to return parameters back to main page
  //   Used in: mycreditcard.php, myurlgenerator.php, 23searchall.php
  //   Arguments:
  //     parameters (array) array of parameters to pass back
  //     hide (boolean) (optional) (default)null/true=hide popup after callback; false=leave popup open
  $res .= '
function frame_popup_callback_'.$name.'(parameters, hide){
  callback_params = frame_popup_callback_params_'.$name.';
  '.$javascript_callback.'
  if(hide != false){
    frame_popup_toggle_'.$name.'(false);
  }
}';
  $s_header = '';
  $s_footer = '';
  if($stand_out == 1){
	  $s_header = "<div id=\"side_effect\" style=\"padding-left:105px; position:absolute;top:50px; \">";
	  $s_footer = "</div>";
  }

  $res .= '
-->
</script>
'.$s_header.'
<div name="div_frame_popup_'.$name.'" id="div_frame_popup_'.$name.'" style="width:'.$width.'px; height:'.$height.'px; display:none; position:absolute; background:#DDDDDD; margin: 15px 0px 0px 5px; z-index:2000;">
  <iframe id="iframe_frame_popup_'.$name.'" width="100%" height="100%" style="padding:10px;border:1px solid black; background:#FFFFFF;"></iframe>
  <div style="position:absolute; top:10px; right:15px;">
    <a href="javascript:void(0);" onclick="frame_popup_toggle_'.$name.'(false);" style="color:#444444; text-decoration:none;" class="atranslate">Close [x]</a>
  </div>
</div>
'.$s_footer;
  if($stand_out == 1){
	  $res .= "<script>\$(function() {\$(\"#side_effect\").expose({color:'#000'});});</script>";
  }
  if($write_link != false && $write_link != ""){
    $res .= '<a href="javascript:void(0);" onclick="frame_popup_toggle_'.$name.'('.$link_parameters.');" '.$additional_css.'>'.$write_link.'</a>';
  }

  return $res;
}

function WriteIPCountry($ip) {
##  include_once("/var/www/vhosts/secureonlinecart.net/template/zmasterT/data/geoip.inc");
  include_once(PATH."scripts/maxmind/lib_geoip.php");
  $gi = geoip_open(PATH."scripts/maxmind/GeoIP.dat",GEOIP_STANDARD);
  $country = geoip_country_name_by_addr($gi,$ip);
  geoip_close($gi);
  return $country;
}

function WriteIPCountryID($ip) {
##  include_once("/var/www/vhosts/secureonlinecart.net/template/zmasterT/data/geoip.inc");
  include_once(PATH."scripts/maxmind/lib_geoip.php");
  $gi = geoip_open(PATH."scripts/maxmind/GeoIP.dat",GEOIP_STANDARD);
  $country = geoip_country_code_by_addr($gi,$ip);
  geoip_close($gi);
  //return"<img src='http://oneyearplan.net/template/images/flags/".strtolower(WriteIPCountryID($_SERVER['REMOTE_ADDR'])).".png' />";
  return $country;
}


function WriteIPCountryImg($ip) {
##  include_once("/var/www/vhosts/secureonlinecart.net/template/zmasterT/data/geoip.inc");
  include_once(PATH."scripts/maxmind/lib_geoip.php");
  $gi = geoip_open(PATH."scripts/maxmind/GeoIP.dat",GEOIP_STANDARD);
  $country = geoip_country_code_by_addr($gi,$ip);
  geoip_close($gi);
  return"<img src='/images/flags/".strtolower(WriteIPCountryID($_SERVER['REMOTE_ADDR'])).".png' />";
  //return $country;
}

function WriteCardNum($last_4){
	return "XXXX-$last_4";
}

// function WriteFieldCardNumber()
//   Write Credit Card Number text field and JavaScript validation
function WriteCardNumber($last4){
	if (!empty($last4)) {
		return "XXXX-$last4";
	} else {
		return "";	
	}
}

// Returns Member's Credit Card <select> options 
function WriteCardOptions($inf_contact_id, $include_blank = false) {
	# incase function is called twice or more
	global $global_card_array;

	if (!isset($global_card_array)) {
		$global_card_array = InfGetCreditCards($inf_contact_id);
	}
	$card_options = array();
	#WriteArray($cards_array);
	if ($include_blank) {
		$card_options[] = array(0 => " - Select - ");		
	}
	foreach ($global_card_array as $card_id => $card_row) {
		$card_type = str_replace("American Express","Amex", $card_row['CardType']);
#		$card_type = str_replace("MasterCard","MC",$card_type);
		$card_options[] = array($card_id => "$card_type: ".WriteCardNum($card_row['Last4']));
	}
	return $card_options;
}

####################################################################################################
#
# Write Form Fields
#
##################################################


// function WriteFieldCardNumber()
//   Write Credit Card Number text field and JavaScript validation
function WriteFieldCardNumber($bill_card="", $name="bill_card",$disabled=false){
	if($disabled)$disabled = 'readonly="readonly"';
	$check_cc = (($bill_card != "") ? "check_ccnumber();" : "");
	$res = '
<input autocomplete="off" type="text" maxlength="25" name="'.$name.'" id="bill_card" value="'.$bill_card.'" onchange="check_ccnumber();" '.$disabled.'/>&nbsp;<span id="div_cc_error"></span>
<script type="text/javascript" language="JavaScript" src="/scripts/creditcard.js"></script>
<script type="text/javascript" language="JavaScript">
<!--
function check_ccnumber(){
	var ccnumber = document.getElementById("bill_card").value;
	if(ccnumber.substring(0,4) != "XXXX"){
		var ccnumber = ccnumber.replace(/[^0-9,\-,\s]/g, "");
		document.getElementById("bill_card").value = ccnumber;
		var msg = "";
		var cardtype = checkCreditCard(ccnumber);
		if(cardtype != false){
			msg = "<font color=\"green\">"+cardtype+"</font>";
		}
		else{
			msg = "<font color=\"red\">"+ccErrors[ccErrorNo]+"</font> <a href=\"javascript:void(0);\" onclick=\"alert(ccErrorsLong["+ccErrorNo+"]+ccErrorsMsg);\" style=\"text-decoration:none; border-bottom:dotted 1px; padding-bottom: 1px; color:blue;\">Why?</a>";
		}
		document.getElementById("div_cc_error").innerHTML = msg;
	}
}
'.(($bill_card != "") ? "check_ccnumber();" : "").'
-->
</script>
';
	//$res = '<input autocomplete="off" type="text" maxlength="25" name="'.$name.'" id="bill_card" value="'.$bill_card.'" />';
	return $res;
}


// function WriteFieldCardExp()
function WriteFieldCardExp($bill_exp_month="", $bill_exp_year="", $name_month="bill_exp_month", $name_year="bill_exp_year",$disabled=false){
	if($disabled)$disabled = 'DISABLED';
	$res = "";

	$res .= "<select name='".$name_month."' $disabled>";
	if($bill_exp_month == ""){$res .= "<option value='' ".$selected."></option>";}
	for($i=1; $i<=12; $i++){
		$value = (($i<10) ? "0" : "").$i;
		$selected = "";
		if($bill_exp_month == $value){
			$selected = "selected='selected'";
		}
		$res .= "<option value='".$value."' ".$selected.">".$value."</option>";
	}
	$res .= "</select> ";

	$year = date("Y");
	$res .= "<select name='".$name_year."' $disabled>";
	if($bill_exp_year == ""){$res .= "<option value='' ".$selected."></option>";}
	for($i=0; $i<=11; $i++){
		$value = $year+$i;
		$selected = "";
		if($bill_exp_year == $value){
			$selected = "selected='selected'";
		}
		$res .= "<option value='".$value."' ".$selected.">".$value."</option>";
	}
	$res .= "</select>";

	return $res;
}

function WriteFieldCardExpMonth($bill_exp_month="", $name_month="bill_exp_month",$disabled=false){
	if($disabled)$disabled = 'DISABLED';
	$res = "";

	$res .= "<select name='".$name_month."' $disabled>";
	if($bill_exp_month == ""){$res .= "<option value=''></option>";}
	for($i=1; $i<=12; $i++){
		$value = (($i<10) ? "0" : "").$i;
		$selected = "";
		if($bill_exp_month == $value){
			$selected = "selected='selected'";
		}
		$res .= "<option value='".$value."' ".$selected.">".$value."</option>";
	}
	$res .= "</select> ";

	return $res;
}

function WriteFieldCardExpYear($bill_exp_year="", $name_year="bill_exp_year",$disabled=false){
	if($disabled)$disabled = 'DISABLED';
	$res = "";

	$year = date("Y");
	$res .= "<select name='".$name_year."' $disabled>";
	if($bill_exp_year == ""){$res .= "<option value=''></option>";}
	for($i=0; $i<=11; $i++){
		$value = $year+$i;
		$selected = "";
		if($bill_exp_year == $value){
			$selected = "selected='selected'";
		}
		$res .= "<option value='".$value."' ".$selected.">".$value."</option>";
	}
	$res .= "</select>";

	return $res;
}


// function WriteFieldCountry()
//   HTML Output: Select field for countries or country phone prefixes
//   Make sure to also call WriteFieldState() on the same page, after calling this if for countries
function WriteFieldCountry($current_value="", $name="bill_country", $num="0", $abbr="0",$disabled=false, $width=75, $extra_style=""){
	global $db;

	if($disabled)$disabled='DISABLED';
	if(preg_match('/ship/',$name)){
 		$type='ship';
 	}else{
 		$type='bill';
 	}
	$prefix = (($num === true) ? true : false);
	if($current_value == ""){ $country = WriteIPCountry($_SERVER['REMOTE_ADDR']); }
	if($current_value == "" && !$prefix){ $current_value = $country; }
	$current_value = trim($current_value);

	$res = "";
	//$res .= WriteOnce('js_ie-select-width-fix');
	//$res .= "<span class='select-box'>";
	$res .= "<select name='".$name."' ".((!$prefix) ? "id='${type}_country_".$num."' onchange=\"check_${type}_country_".$num."('".$num."');\"" : "").(($prefix) ? " style='width:".$width."px;$extra_style'" : "")." $disabled>";

	$options = "";
	$selected_true = false;
	$selected_true_prefix = false;

	$queries = array();
    $queries[] = "SELECT * FROM countries WHERE priority > 0 ".(($prefix) ? "AND prefix<>''" : "")." ORDER BY priority, country";
    $queries[] = "SELECT * FROM countries ".(($prefix) ? "WHERE prefix<>''" : "")." ORDER BY country";

	$options = array();
	if($prefix){
		$options[""] = "- Select Country Dialing Prefix -";
		//$options = "<option value='' selected='selected'>- Select Country Dialing Prefix -</option>".$options;
	}

	foreach($queries as $i=>$query){
		if($i != 0){
			//$options .= "<option value='' disabled='disabled'>-------------------------</option>";
			$options[] = array("-"=>"-------------------------", "disabled");
		}
		$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	    while($country_row = mysqli_fetch_assoc($result)){
			$value = (($prefix) ? $country_row['prefix']." " : "").$country_row['country'];
			if($abbr){
				$options[] = array($country_row['country_abbr']=>$value);
			}else{
				$options[] = $value;
			}
			if($current_value == "" && $prefix && $country == $country_row['country']){ $current_value = $value; }
	    }
	}

	$query = "SELECT * FROM countries WHERE country = '".addslashes($current_value)."' OR country_abbr = '".addslashes($current_value)."'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$c_row = mysqli_fetch_assoc($result);
	if($abbr){
		$res .= WriteSelect($c_row['country_abbr'], $options, true, false);
	}else{
		$res .= WriteSelect($current_value, $options, true, false);
	}
	$res .= "</select>";

	return $res;
}


// function WriteFieldState($seleced_value)
//   write select fields and text field for states
//   make sure to also call WriteFieldCountry() on the same page, before calling this
function WriteFieldState($state="", $name="bill_state", $num="0", $disabled=false){
	global $db;
	
	if($disabled)$disabled='DISABLED';
	$state = trim($state);
 	if(preg_match('/ship/',$name)){
 		$type='ship';
 	}else{
 		$type='bill';
 	}
	$res = "";

	//$res .= WriteOnce('js_ie-select-width-fix');

	$last_country_id = -1;

	$query = "SELECT s.*, c.country 
				FROM states s
				JOIN countries c USING(country_id)
				ORDER BY s.country_id asc, s.state_name asc";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	while($array = mysqli_fetch_assoc($result)){
		$selected = "";

		if($last_country_id != $array['country_id']){
			if($last_country_id != -1){
				if($selected_true == false){
					$options = "<option value='' selected='selected'> </option>".$options;
				}
			    $res .= $options;
				$res .= "</select></span>";
				$res .= $last_js_fix_ie;
			}

$last_js_fix_ie = '<script type="text/javascript">
<!--
//var '.$type.'_state_'.$num.'_'.$array['country_id'].' = new YAHOO.Hack.FixIESelectWidth("'.$type.'_state_'.$num.'_'.$array['country'].'", -15);
-->
</script>';
			$res .= "<span id=\"span_${type}_state_".$num."_".$array['country']."\" class=\"select-box\"  style=\"display:none;\"><select name=\"".$name."_xxx\" id=\"${type}_state_".$num."_".$array['country']."\" style=\"display:none;\" $disabled>";
			$options = "";
			$selected_true = false;
			$last_country_id = $array['country_id'];
		}

		if($state == $array['state_abbr'] || $state == $array['state_name']){
			$selected = "selected=\"selected\"";
			$selected_true = true;
		}
		$options .= "<option value=\"".$array['state_abbr']."\" ".$selected.">".$array['state_name']."</option>";
	}
	if($selected_true == false){
		$options = "<option value=\"\" selected=\"selected\"> </option>".$options;
	}
	$res .= $options;
	$res .= "</select></span>";
	$res .= $last_js_fix_ie;

    $res = "<span id=\"span_${type}_state_".$num."_Other\" style=\"display:none;\"><input type=\"text\" name=\"".$name."_xxx\" id=\"${type}_state_".$num."_Other\" value=\"".(($selected_true == false) ? $state : "")."\" style=\"display:none;\" ".(($disabled)?"readonly='readonly' $disabled":'')."></span>".$res;
	if($num == 0){
		$script_str = "var country_selected = new Array();";
	}else{
		$script_str = "";
	}
	if(1){
		$res .= '
<script type="text/javascript" language="JavaScript">
<!--
'.$script_str.'
function check_'.$type.'_country_'.$num.'(num){
	if(num == null || num == ""){num=0;}
	//alert("'.$type.'_state_"+num+"_"+country_selected[num]);
	var country = document.getElementById("'.$type.'_country_"+num).options[document.getElementById("'.$type.'_country_"+num).selectedIndex].text;

	if(country_selected[num] != country){

		if(country_selected[num] != "" ){
			//if(num == 0) alert(country_selected[num]);
			document.getElementById("'.$type.'_state_"+num+"_"+country_selected[num]).style.display = "none";
			document.getElementById("'.$type.'_state_"+num+"_"+country_selected[num]).disabled = true;
			document.getElementById("span_'.$type.'_state_"+num+"_"+country_selected[num]).style.display = "none";
			document.getElementById("'.$type.'_state_"+num+"_"+country_selected[num]).setAttribute("name","'.$name.'_xxx");
			//$("#'.$type.'_state_"+num+"_"+country_selected[num]).attr("name","'.$name.'_xxx");
		}

		if(country == ""){
			country_selected[num] = "";
		}
		else{
			if(document.getElementById("'.$type.'_state_"+num+"_"+country) != null){
				country_selected[num] = country;
			}
			else{
				country_selected[num] = "Other";
			}
			document.getElementById("'.$type.'_state_"+num+"_"+country_selected[num]).style.display = "inline";
			document.getElementById("'.$type.'_state_"+num+"_"+country_selected[num]).disabled = '.(($disabled)?'true':'false').';
			document.getElementById("span_'.$type.'_state_"+num+"_"+country_selected[num]).style.display = "inline";
			document.getElementById("'.$type.'_state_"+num+"_"+country_selected[num]).setAttribute("name","'.$name.'");
			//$("#'.$type.'_state_"+num+"_"+country_selected[num]).attr("name","'.$name.'");
		}
	}
	//check_country_'.$num.'();
}';
	}
	if($num == 0){
	}


$res .= '
function get_state(num){
	var field = document.getElementById("'.$type.'_state_"+num+"_"+country_selected[num]);
	//return field.options[field.selectedIndex].value;
	return $(field).val();
}
-->
</script>
';
	$res .= '
<script type="text/javascript" language="JavaScript">
<!--
country_selected["'.$num.'"] = "";
check_'.$type.'_country_'.$num.'("'.$num.'");
-->
</script>
';

	return $res;
}


####################################################################################################
?>

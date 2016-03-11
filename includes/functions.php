<?php

require_once("functions_write.php");

function EchoLn($str, $color=false) {
	if ($color) {
		echo "<font color=$color>$str<br></font>";
	} else {
		echo $str."<br>";		
	}
}

# Rounds down to 2 decimal places, eg, 1.978 becomes 1.97.
function RoundDown($num) {
	return floor($num * 100) / 100;
}


# Actually puts it in email_queue to be sent
function SendEmail($db, $email_to, $subject, $msg, $email_from, $email_template = 0) {
	$query = "INSERT INTO email_queue 
				SET email_to='".addslashes($email_to)."'
				, subject='".addslashes($subject)."'
				, msg='".addslashes($msg)."'
				, email_from='".addslashes($email_from)."'
				, email_template='$email_template'
				, create_date=NOW()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
}

# Actually puts it in sms_queue to be sent
function SendTextMsg($db, $phone_to, $country_to, $msg, $media_url = "") {
	$query = "INSERT INTO text_msg_queue 
				SET phone_to='".addslashes($phone_to)."'
				, country_to='".addslashes($country_to)."'
				, msg='".addslashes($msg)."'
				, media_url='".addslashes($media_url)."'
				, create_date=NOW()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
}

function LogAdminChange($db, $member_id, $table, $item_id, $item_field, $old_value, $new_value, $changer_id, $change_location) {
	
	$query = "INSERT INTO change_log 
				SET item_table='$table'
				, member_id='$member_id'
				, item_id='$item_id'
				, item_field='$item_field'
				, old_value='".addslashes($old_value)."'
				, new_value='".addslashes($new_value)."'
				, changer_admin_id='$changer_id'
				, change_location='$change_location'
				, create_date=NOW()";
	#echo $query;
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
}

function GetMemberIdFromEmail($db, $email) {
	
	# List commissions earned by a member
	$query = "SELECT m.member_id
				FROM members m
				WHERE m.email_username='$email'";            
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if ($row = mysqli_fetch_assoc($result)) {
		return $row['member_id'];
	} else {
		return false;
	}
}

# Retuns details about a product, eg, if owned it/were qualified on $date
function ActiveInRank($db, $member_id, $product_type, $date) {
	$query = "SELECT member_id
			FROM member_ranks mr
			WHERE mr.member_id = $member_id
			AND mr.product_type = '$product_type'
			AND mr.start_date <= '$date'
				AND (mr.end_date IS NULL OR mr.end_date >= '$date')";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if ($row = mysqli_fetch_assoc($result)) {
		return true;
	} else {
		return false;
	}	
}

function GetRowMember($db, $value, $field="member_id"){
	if(empty($value)){ return false; }
	if(trim($field) == ""){ $field = "member_id"; }

	$query = "SELECT *
	FROM members 
	WHERE $field='$value' 
	LIMIT 1";

	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if($row = mysqli_fetch_assoc($result)){
		$row['rank'] = WriteRank($db, $row['member_id']); 			
		$row['aspire_level'] = WriteAspireLevel($db, $row['member_id']);				
		return $row;
	} else {
		return false;
	}
}

function GetRowMemberCoaches($db, $member_id) {
	$query = "SELECT m.*
				, sc.name AS coach_name_startup, sc.username AS coach_username_startup
				, su.name AS coach_name_setup, su.username AS coach_username_setup
				, st.name AS coach_name_scale, st.username AS coach_username_scale
				, su.name AS coach_name_setup, su.username AS coach_username_setup
				, tr.name AS coach_name_traffic, tr.username AS coach_username_traffic
				, ss.name AS coach_name_success, ss.username AS coach_username_success
				, mc.coach_id_startup, mc.coach_id_setup, mc.coach_id_scale, mc.coach_id_traffic, mc.coach_id_success
			FROM members m
			LEFT JOIN member_coaches mc on mc.member_id = m.member_id
				AND mc.start_date <= CURDATE()
				AND (mc.end_date IS NULL OR mc.end_date >= CURDATE())
			LEFT JOIN members sc on sc.member_id = mc.coach_id_startup 
			LEFT JOIN members su on su.member_id = mc.coach_id_setup 
			LEFT JOIN members st on st.member_id = mc.coach_id_scale 
			LEFT JOIN members tr on tr.member_id = mc.coach_id_traffic 
			LEFT JOIN members ss on ss.member_id = mc.coach_id_success
			WHERE m.member_id = $member_id
			ORDER BY mc.member_coaches_id DESC
			LIMIT 1";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	return mysqli_fetch_assoc($result);
}

function GetRowLesson($db, $product, $lesson_number){
	$query = "SELECT lt.lesson_type, lt.coach_type, l.*
	FROM lessons l
	LEFT JOIN lesson_types lt USING (product)
	WHERE product='$product' 
	AND lesson_number='$lesson_number'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if($row = mysqli_fetch_assoc($result)){
		return $row;
	} else {
		return false;
	}
}

function GetRowMemberDetails($db, $value, $field="member_id"){
	if(empty($value)){ return false; }
	if(trim($field) == ""){ $field = "member_id"; }
	$query = "SELECT m.* 
				, md.skype, md.facebook, md.twitter, md.pintrest, md.blog, md.book_call, md.welcome_msg, md.welcome_video
				, md.notify_leads, notify_members, md.notify_comms, md.notify_comms_min
				, md.sms_members, md.sms_comms, md.sms_comms_min
			FROM members m
			LEFT JOIN member_details md USING (member_id)
			WHERE m.$field = '$value'
			LIMIT 1";

	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	if($row = mysqli_fetch_assoc($result)){
		//echo $query;
		//print_r($row);
		$row['rank'] = WriteRank($db, $row['member_id']); 			
		$row['aspire_level'] = WriteAspireLevel($db, $row['member_id']);		
		return $row;
	} else {
		return false;
	}
}

/* Luhn algorithm number checker - (c) 2005-2008 shaman - www.planzero.org *
 * This code has been released into the public domain, however please      *
 * give credit to the original author where possible.                      */
function ValidCreditCardNumberFormat($number) {

  // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
  $number=preg_replace('/\D/', '', $number);

  // Set the string length and parity
  $number_length=strlen($number);
  $parity=$number_length % 2;

  // Loop through each digit and do the maths
  $total=0;
  for ($i=0; $i<$number_length; $i++) {
    $digit=$number[$i];
    // Multiply alternate digits by two
    if ($i % 2 == $parity) {
      $digit*=2;
      // If the sum is two digits, add them together (in effect)
      if ($digit > 9) {
        $digit-=9;
      }
    }
    // Total up the digits
    $total+=$digit;
  }
  // If the total mod 10 equals 0, the number is valid
  return ($total % 10 == 0) ? true : false;
}

// function isMobile()
//   Check if a browser/device is mobile
//   Used in: index.php, m/notmobile.php
function isMobile($debug=false, $server_vars=array()){
	if($server_vars === array()){$server_vars = $_SERVER;}
	$ua = strtolower(isset($server_vars['HTTP_USER_AGENT']) ? $server_vars['HTTP_USER_AGENT'] : "");
	$ac = strtolower(isset($server_vars['HTTP_ACCEPT']) ? $server_vars['HTTP_ACCEPT'] : "");
	$ah = strtolower(isset($server_vars['ALL_HTTP']) ? $server_vars['ALL_HTTP'] : "");
	$wap = isset($server_vars['HTTP_X_WAP_PROFILE']) ? $server_vars['HTTP_X_WAP_PROFILE'] : "";
	$wap2 = isset($server_vars['HTTP_PROFILE']) ? $server_vars['HTTP_PROFILE'] : "";
	$opera1 = isset($server_vars['HTTP_X_OPERAMINI_PHONE']) ? $server_vars['HTTP_X_OPERAMINI_PHONE'] : "";
	$opera2 = isset($server_vars['HTTP_X_OPERAMINI_PHONE_UA']) ? $server_vars['HTTP_X_OPERAMINI_PHONE_UA'] : "";
	$skyfire = isset($server_vars['HTTP_X_SKYFIRE_PHONE']) ? $server_vars['HTTP_X_SKYFIRE_PHONE'] : "";
	$xnt = strtolower(isset($server_vars['HTTP_X_NETWORK_TYPE']) ? $server_vars['HTTP_X_NETWORK_TYPE'] : "");
	$uaos = strtolower(isset($server_vars['HTTP_UA_OS']) ? $server_vars['HTTP_UA_OS'] : "");

	if(strpos($ac, '.wap') !== false || strpos($ac, 'wap.') !== false){ if($debug){ return 'HTTP_ACCEPT='.$ac; } return true; }
	// strpos($ac, 'application/vnd.wap.xhtml+xml') !== false
	if($wap != ''){ if($debug){ return 'HTTP_X_WAP_PROFILE='.$wap; } return true; }
	if($wap2 != ''){ if($debug){ return 'HTTP_PROFILE='.$wap2; } return true; }
	if($opera1 != ''){ if($debug){ return 'HTTP_X_OPERAMINI_PHONE='.$opera1; } return true; }
	if($opera2 != ''){ if($debug){ return 'HTTP_X_OPERAMINI_PHONE_UA='.$opera2; } return true; }
	if($skyfire != ''){ if($debug){ return 'HTTP_X_SKYFIRE_PHONE='.$skyfire; } return true; }
	if(strpos($ah, 'operamini') !== false){ if($debug){ return 'ALL_HTTP='.$ah; } return true; }
	if(strpos($uaos, 'pocket pc') !== false){ if($debug){ return 'HTTP_UA_OS='.$uaos; } return true; }
	if(strpos($xnt, 'evdo') !== false){ if($debug){ return 'HTTP_X_NETWORK_TYPE='.$xnt; } return true; }

	$mobile_ua = array('wap', 'phone', 'up.browser', 'up.link', 'blackberry',	"applewebkit/525","applewebkit/532", 'opera mini', 'mobile', 'j2me', 'midp', 'symbian', 'cldc', 'mmp', 'windows ce', 'iemobile', 'pocket', 'palm', 'blazer', 'webos', 'netfront', 'wireless', 'hand', 'mobi', 'ipaq', 'java', 'sony',  'nokia', 'samsung', 'epoc', 'nitro', 'mot', 'audiovox', 'ericsson,', 'panasonic', 'philips', 'sanyo', 'sharp', 'sie-', 'portalmmm', 'avantgo', 'danger', 'series60', 'rover', 'au-mic,', 'alcatel', 'ericy', 'vodafone/', 'avantg', 'docomo', 'novarra', '240x320', 'opwv', 'chtml', 'mib/', 'cdm', 'up.b', 'audio', 'sec-', 'htc', 'mot-', 'mitsu', 'sagem', 'lg', 'erics', 'vx', 'mmm', 'xx', 'sch', 'benq', 'pg', 'vox', 'amoi', 'compal', 'kg', 'voda', 'sany', 'kdd', 'dbt', 'sendo', 'sgh', 'gradi', 'jb', 'moto');

	foreach($mobile_ua as $value){
		if(strpos($ua, $value) !== false){
			if($debug){ return 'HTTP_USER_AGENT=('.$value.')='.$ua.''; } return true;
		}
	}

	/*$mobile_ua = substr($ua, 0, 4);
	array('w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac', 'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno', 'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-', 'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-', 'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox', 'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar', 'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-', 'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp', 'wapr', 'webc', 'winw', 'winw', 'xda ', 'xda-');
	in_array($mobile_ua, $mobile_agents);*/

	if($debug){ return 'Not Mobile'; }
	return false;
}

// function ForceArray()
//   If the variable is not an array, return an array with that variable as the only element.
function ForceArray($var){
	// return (array) $var;
	if(!is_array($var)){
		$var = array($var);
	}
	return $var;
}

// function PregPattern()
//   Create a PCRE pattern (automatically adding the delimiters, etc)
function PregPattern($pattern, $modifiers="", $delimiter="/"){
	$new_pattern = $delimiter.str_replace($delimiter, "\\".$delimiter, $pattern).$delimiter.$modifiers;
	return $new_pattern;
}


// function Like()
//   Mimic SQL's LIKE
function Like($subject, $search, $case_insensitive=false){
	$modifiers = (($case_insensitive) ? "i" : "");
	$pattern = PregPattern("^".$search."$", $modifiers);
	$pattern = str_replace("%", ".*", $pattern);
	$match = preg_match($pattern, $subject);
	return $match;
}

function doCURL($url=false,$fields=false,$method='post'){
	$method = strtolower($method);

	$allowed_methods = array('get','post');

	if(!$fields || !$url || !in_array($method,$allowed_methods)) {
		return false;
	}

	foreach ($fields as $k => $v) {
		$fields_enc[] = $k . "=" . urlencode($v);
	}

	$fields_enc = implode("&", $fields_enc);

	if($method == 'get')
		$url = $url."?".$fields_enc;

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	if($method == 'post'){
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_enc);
	}
 	// curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response

	$res = curl_exec($curl);
	curl_close($curl);

	return (($res)?$res:false);
}

###############################
# XML Processing
###############################

function xmlstr_to_array($xmlstr) {
  $doc = new DOMDocument();
  $doc->loadXML($xmlstr);
  $root = $doc->documentElement;
  $output = domnode_to_array($root);
  $output['@root'] = $root->tagName;
  return $output;
}

function domnode_to_array($node) {
  $output = array();
  switch ($node->nodeType) {
    case XML_CDATA_SECTION_NODE:
    case XML_TEXT_NODE:
      $output = trim($node->textContent);
    break;
    case XML_ELEMENT_NODE:
      for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
        $child = $node->childNodes->item($i);
        $v = domnode_to_array($child);
        if(isset($child->tagName)) {
          $t = $child->tagName;
          if(!isset($output[$t])) {
            $output[$t] = array();
          }
          $output[$t][] = $v;
        }
        elseif($v || $v === '0') {
          $output = (string) $v;
        }
      }
      if($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
        $output = array('@content'=>$output); //Change output into an array.
      }
      if(is_array($output)) {
        if($node->attributes->length) {
          $a = array();
          foreach($node->attributes as $attrName => $attrNode) {
            $a[$attrName] = (string) $attrNode->value;
          }
          $output['@attributes'] = $a;
        }
        foreach ($output as $t => $v) {
          if(is_array($v) && count($v)==1 && $t!='@attributes') {
            $output[$t] = $v[0];
          }
        }
      }
    break;
  }
  return $output;
}


/*
* Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
    $url = 'https://www.gravatar.com/avatar/';
    $url .= md5( strtolower( trim( $email ) ) );
    $url .= "?s=$s&d=$d&r=$r";
    if ( $img ) {
        $url = '<img src="' . $url . '"';
        foreach ( $atts as $key => $val )
            $url .= ' ' . $key . '="' . $val . '"';
        $url .= ' />';
    }
    return $url;
}

/*function get_page_access_status($db, $member_id, $page) {
    $query = "SELECT * FROM member_access WHERE access = '$page' AND member_id = '$member_id'";
    $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
    if($row = mysqli_fetch_assoc($result)) {
    	return true;
    } else {
        return false;
    }
}

function grant_page_access($db, $member_id, $page) {
    $query = "INSERT INTO member_access 
				SET access = '$page'
				, member_id ='$member_id'
				, access_status = 1
				, create_date = NOW()";
    $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
}
*/

# INPUT: $page_step, eg, 1.2, 3.2
function UnlockedStep($db, $member_id, $page_step, $step_unlocked) {
	$page_step = (float)$page_step;
	if ($page_step < 1) {
		return true; // No restriction on these pages	
	}
    if(!empty($_GET['unlock'])) {
        _GrantStepAccess($db, $member_id, $page_step);
        return true;
    }
    if(!empty($_GET['preview'])) return true;

    $row = GetRowMember($db, $member_id); 
    if($page_step > $row['step_unlocked']) {
		return false;    
	} else {
		return true;
	}
}

function GetStepAccess($db, $member_id, $page_step) {
    $row = GetRowMember($db, $member_id);  
    if(!empty($_GET['unlock'])) {
        _GrantStepAccess($db, $member_id, $page_step);
        return true;
    }
    if(!empty($_GET['preview'])) return true;
#    if($row['steps_completed'] < 1.2) $row['steps_completed'] = 1.1;    
    if($page_step > $row['step_unlocked']) return false;    
    else return true;
}

function _GrantStepAccess($db, $member_id, $step) {
    $query = "UPDATE members 
				SET step_unlocked = '$step' 
				WHERE member_id ='$member_id' 
				AND step_unlocked <= '$step'";
    $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");    
}

function get_step_name_by_step_id($step_array, $step) {
    foreach($step_array as $value) {
        if(isset($value[$step])) {
            return $value[$step];
        }
    }
}

########################################################################
# Increments coach_rotator and return Coach ID for new members
# INPUT: $coach_type = "setup" | "business" | "traffic" | "tech"
function GetCoachId ($db, $coach_type = "setup") {
	$r_loop = 0;
	$coach_rotator_row = false;
	$res = 100; // Force is default (for now)
	while (!$coach_rotator_row && $r_loop < 2) { // $r_loop would only be 1 if the counts have to be reset to 0
		$query = "SELECT * 
					FROM coach_rotator 
					WHERE active=1 
					AND count_cycle < weight 
					AND coach_type='$coach_type' 
					ORDER BY count_cycle/weight";
        $result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
		if (mysqli_num_rows($result) == 0) { // loop again
			$query = "UPDATE coach_rotator 
						SET count_cycle=0 
						WHERE active=1 	
						AND coach_type='$coach_type'";
        	$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
			$r_loop++;
			continue;
		} 
		// get next url
		$coach_rotator_row = mysqli_fetch_assoc($result);
		$coach_rotator_id = $coach_rotator_row['coach_rotator_id'];
		$res = $coach_rotator_row['coach_id'];
		
		// check max members/count
		if ($coach_rotator_row['max_count'] > 0 
			&& $coach_rotator_row['count'] >= $coach_rotator_row['max_count']) { // reached max
			// deactivate this coach_id
			$query = "UPDATE coach_rotator 
						SET active=0 
						WHERE coach_rotator_id='$coach_rotator_id'";
			$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);
			$coach_rotator_row = false;
			continue; // get a different coach_id
		}
		// Next Coach Found: Update Count
		$query = "UPDATE coach_rotator 
					SET count = count + 1, 
					count_cycle = count_cycle + 1 
					WHERE coach_rotator_id = '$coach_rotator_id'";
		$result = mysqli_query($db, $query) or die(mysqli_error($db) . '. ' . $query);

		return $res;
		break;
	}
	return $res;  // Should not get to here if rotator is working.
}
?>

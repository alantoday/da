<?php
include_once('../includes/config.php');
include_once(PATH.'includes/trackaff.php');
include_once(PATH.'includes/functions.php');
include_once(PATH.'includes/functions_gr.php');

# TEST USAGE: go.digitalaltitude.co?debug=1&test_email=ggg
if (DEBUG && $_GET['test_email']) {
	$_POST['email'] = $_GET['test_email'];
}

if (isset($_POST['email'])) {
	$query = "INSERT INTO debug
			SET notes = 'da:{$_SESSION['da']}, t:{$_SESSION['t']}, email:{$_POST['email']}'
			, create_date = NOW()";
	$result = mysqli_query($db, $query) or die(mysqli_error($db));

	// Get the GR Autoresponder Setting
	if (!empty($_SESSION['da'])) {
		$mrow = GetRowMember($db, $_SESSION['da'], "username");
		$query = "INSERT INTO debug
				SET notes = '{$mrow['gr_api_key']}'
				, create_date = NOW()";
		$result = mysqli_query($db, $query) or die(mysqli_error($db));
		if ($mrow['gr_api_key']<>"") {
			list ($success, $data) = GRAddContact($mrow['gr_api_key'], $name = "", $_POST['email'], $campaign_id = "pxmlb");
			if ($success) {
				$gr_api_res = 1;
			} else {
				$gr_api_res = $data;	
			}
			$query = "INSERT INTO leads_gr
					SET member_id={$mrow['member_id']}
					, email='".addslashes($_POST['email'])."'
					, da='".addslashes($_SESSION['da'])."'
					, t='".addslashes($_SESSION['t'])."'
					, ip='{$_SERVER['REMOTE_ADDR']}'
					, gr_api_res='".addslashes($gr_api_res)."'
					, create_date = NOW()";
		$query2 = "INSERT INTO debug
				SET notes = '".addslashes($query)."'
				, create_date = NOW()";
		$result = mysqli_query($db, $query2) or die(mysqli_error($db));
			$result = mysqli_query($db, $query) or die(mysqli_error($db));
		}
	}
}

$html = file_get_contents("templates/c1.tpl.php");

$replace_array = ("No Referral Details");
$with_array = ("Referral: {$_SESSION['da']}");

$replace_array = ("/cdn-cgi");
$with_array = ("//aspire.link/cdn-cgi");


#$html = str_replace($replace_array, $with_array, $html);

$html = str_replace("</body>",'
<script src="/js/jquery-1.12.1.min.js?v=1"></script>
<script src="/js/submit_lead.js?v=2"></script>
</body>', $html);

echo $html;
?>
<?php

// Include the SDK
require_once('config.php');
require_once('functions.php');

require_once(PATH.'scripts/GetResponse/GetResponseAPI.class.php');

########################################################################
# Gets list/array of Compaigns and their details
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_gr.php?debug=1&GRValidAPIKey=1
if (isset($_GET['GRValidAPIKey'])) {
	WriteArray(GRValidAPIKey(GETRESPONSE_API_KEY));
}
 
function GRValidAPIKey($api_key) {
	$gr_api = new GetResponse($api_key);

	return ($gr_api->ping() == "pong") ? true : false;
}

########################################################################
# Add Contact
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_gr.php?debug=1&GRAddContact=1
if (isset($_GET['GRAddContact'])) {
	WriteArray(GRAddContact(GETRESPONSE_API_KEY,"Alan Testing", "test@test.co", GETRESPONSE_CAMPAIGN_C1));
}
 
function GRAddContact($api_key, $name, $email, $campaign_id = GETRESPONSE_CAMPAIGN_C1) {
	$gr_api = new GetResponse($api_key);

	$response = $gr_api->addContact($campaign_id, $name = '', $email, $action = 'standard', $cycle_day = 0, $customs = array());
	if (DEBUG) WriteArray($response);
	$success = isset($response->queued) && $response->queued == 1;
	$data = isset($response->code) ? $response->code .": ".$response->message : ""; 
	return array($success, $data);
}


########################################################################
# Move Contact from one GetResponse Campaign to Another.
 
function GRMoveContactCampaign($email, $campaign_id = 'plFR9') {
	$gr_api = new GetResponse(GETRESPONSE_API_KEY);

	// Find the contact ID by using email ID 
	$contact_details = (array)$gr_api->getContactsByEmail($email);
#	WriteArray($contactEmail);
	$contact_id_array = array_keys($contact_details);
#	WriteArray($contactEmailID);
	if (!empty($contact_id_array[0])) {
		return $api->setContactCampaign($contact_id_array[0], $campaign_id);  
	}
}

########################################################################
# Gets list/array of Compaigns and their details
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_gr.php?debug=1&GRGetCampaigns=1
if (isset($_GET['GRGetCampaigns'])) {
	WriteArray(GRGetCampaigns(GETRESPONSE_API_KEY));
}
 
function GRGetCampaigns($api_key) {
	$gr_api = new GetResponse($api_key);

	$campaigns 	 = (array)$gr_api->getCampaigns();
	$campaigns = json_decode(json_encode($campaigns), true);
	return $campaigns;
}

########################################################################
# Subscribes new contact to Campaign
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_gr.php?debug=1&GRGetMessages=1
if (isset($_GET['GRGetMessages'])) {
	WriteArray(GRGetMessages(GETRESPONSE_API_KEY, GETRESPONSE_CAMPAIGN_C1));
}
 
function GRGetMessages($api_key, $campaigns = null, $type = null, $operator = 'CONTAINS', $comparison = '%') {
	$gr_api = new GetResponse($api_key);
	
	$messages = $gr_api->getMessages($campaigns = null, $type = null, $operator = 'CONTAINS', $comparison = '%');
	$messages = json_decode(json_encode($messages), true);
	return $messages;
}

########################################################################
# Subscribes new contact to Campaign
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_gr.php?debug=1&GRGetMessageContents=1
if (isset($_GET['GRGetMessageContents'])) {
	WriteArray(GRGetMessageContents(GETRESPONSE_API_KEY, 'QpRpv'));
}
 
function GRGetMessageContents($api_key, $message_id) {
	$gr_api = new GetResponse($api_key);
	
	$message = $gr_api->getMessageContents($message_id);
#	$message = json_decode(json_encode($message), true);
	return $message;
}

	
########################################################################
# Create New Campaign
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_gr.php?debug=1&_CRCreateCampaign=1
if (isset($_GET['_CRCreateCampaign'])) {
	WriteArray(_CRCreateCampaign(GETRESPONSE_API_KEY, 'NEW'));
}
 
function _CRCreateCampaign($gr_api, $name, $description = null, $from = null, $reply_to = null, $subject = null, $body = null, $lang = null) {
#	$gr_api = new GetResponse($api_key);

	// here we are going to get everything we need in order to create the campaign, then we are going to actually create that campaign
	if ($name == '') {
		return null;
	}
	$name = strtolower($name);

	// error checking complete.  We must have a name in order to continue, everything else is technically optional since we'll get the info for the rest of the request
	$description = $name;

	// lets get the from field using the latest email address
	if ($from == NULL) {
		$result = $gr_api->getAccountFromFields();
		foreach ($result as $key => $value) {
			$from_field_id = $key;
		}
	} else {
		$from_field_id = $from;
	}

	if ($reply_to == null) {
		$reply_field_id = $from_field_id;
	} else {
		$reply_field_id = $reply_to;
	}

	if ($subject == null) {
		$result = $gr_api->get_confirmation_subjects();
		$confirmation_subject_id = 'TfU6';  // "Please verify your subscription."
	} else {
		$confirmation_subject_id = $subject;
	}

	if ($body == null) {
		$result = $gr_api->get_confirmation_bodies();
		$confirmation_body_id = 'TfNR';  // Standard confirmation email in GR
	} else {
		$confirmation_body_id = $body;
	}
	$result = $gr_api->addCampaign($name, $description, $from_field_id, $reply_field_id, $confirmation_subject_id, $confirmation_body_id, 'EN');
	$result = json_decode(json_encode($result), true);
	return $result;
}


########################################################################
# Subscribes new contact to Campaign
# TEST USAGE: http://da.digitalaltitude.co/includes/functions_gr.php?debug=1&GRLoadEmails=1
if (isset($_GET['GRLoadEmails'])) {
	WriteArray(GRLoadEmails(1, GETRESPONSE_API_KEY, GETRESPONSE_API_KEY, GETRESPONSE_CAMPAIGN_C1, 'pxmlb'));
}
 
function GRLoadEmails($member_id, $from_api_key, $to_api_key, $from_campaign_id, $to_campaign_id = 0) {
	$from_gr_api = new GetResponse($from_api_key);
	$to_gr_api = new GetResponse($to_api_key);

	$success = false;
	
	# In case they have changed their API KEy and not updated in DA
	if ($from_gr_api->ping() == "pong") {

		$from_messages = GRGetMessages ($from_api_key, $from_campaign_id);
#		$messages = $from_gr_api->getMessages(GETRESPONSE_CAMPAIGN_C1, 'autoresponder');

		// Create to campaign if they don't have one
		if (!$to_campaign_id) {
			$leads_listname = 'daleads_' . $member_id .'_' . rand(100, 999);
			$response = _CRCreateCampaign($from_gr_api, $leads_listname, 'ASPIRE Funnel Leads');
	if (DEBUG) WriteArray($response);
			if(!$response['added']) {
				$msg = 'ERROR: An error occurred while trying to create your new campaign. Please try again later.';
				return array($success, $msg);
			}
			$to_campaign_id = $response['CAMPAIGN_ID'];
			$success_msg = "SUCCESS: Our pre-written emails have been loaded into your GetResponse campaign: $leads_listname";
			if (DEBUG) EchoLn("New Campaign ID: " . $to_campaign_id);
		} else {
			$success_msg = "SUCCESS: Our pre-written emails have been loaded into your exiting GetResponse campaign";			
		}

		foreach($from_messages as $message_id => $m) {
			$content = $from_gr_api->getMessageContents($message_id);
			if (empty($m['day_of_cycle'])) {
				$m['day_of_cycle'] = 0;
			}
			if (empty($content['plain'])) {
				$content['plain'] = '';
			}
			if (empty($content['html'])) {
				$content['html'] = '';
			}
#	public function addAutoresponder($campaign, $subject, $cycle_day, $html = null, $plain = null, $from_field = null, $name, $flags = null)
			$response = $to_gr_api->addAutoresponder($to_campaign_id, $m['subject'], $m['day_of_cycle'], $content['html'], $content['plain'], $from_field = null, $m['name'], array('clicktrack', 'openrate'));
#			if (DEBUG) WriteArray($response);
#			exit();
		}
		$success = true;
		$msg = $success_msg;
	} else {
		$msg = 'ERROR: Your GetResponse API Key does not appear to be valid any more';
	}
		
	return array($success, $msg);
}

?>

<?php
require_once('config.php');
require_once('functions.php');

require(PATH.'scripts/twilio-php/Services/Twilio.php'); 

########################################################################
# Sent Text Message
# Example to_number format: +17202919897
# MediaURL should be: if .gif, .png and .jpeg URL
# $msg is limited to 1600 characters
 
function TwilioSendText($to_number, $msg, $media_url="") {
	// this line loads the library 
	
	# TODO - Do better job of validating it
	if (trim($to_number) <> "") {
		$client = new Services_Twilio(TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN);  
		
		// Strip any unecessary character, eg . -, () spaces, 
		$to_number = preg_replace("/[\.\-() ]/","", $to_number);
		if (!preg_match("/^\+/",$to_number)) {
			// And make sure the + at the start.
			$to_number = "+".$to_number;	
		}
			
		if ($media_url != "") {
			$send_array = array( 
			'To' => $to_number, 
			'From' => TWILIO_FROM_NUMBER, 
			'Body' => $msg, 
			'MediaUrl' => $media_url
			);
		} else {
			$send_array = array( 
			'To' => $to_number, 
			'From' => TWILIO_FROM_NUMBER, 
			'Body' => $msg 
			);
		}
		return $client->account->messages->create($send_array);
	} else {
		return false;	
	}
}
?>

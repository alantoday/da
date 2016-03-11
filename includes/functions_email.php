<?php

####################################################################################################

function EmailNewMember($db, $member_id_row) {

	// Get the member_row if the only the id is passed in; otherwise just use the member_row
	if (is_array($member_id_row)) {
		$member_row = $member_id_row;
	} else {
		$member_row = GetRowMember($db, $member_id_row);		
	}

	$subject = "Welcome To The ASPIRE System";
	$msg = "Hello {$member_row['name']},
	
Let me be the first to congratulate you on your decision to join us.
You're in for an exciting ride... or should I say an Exciting Climb!

Your journey starts here:
http://my.aspiresystem.co

You can login with your
Email: {$member_row['email']}
Username: {$member_row['username']}
Password: {$member_row['passwd']}

Your success,

~Michael Force
Founder
";
	SendEmail($db, $member_row['email'], $subject, $msg, "ASPIRE System <support@aspiresystem.co>");
}

####################################################################################################

function EmailNewMemberSponsor($db, $sponsor_id_row, $member_id_row, $inf_product_id = 0) {

	$aspire = "";
	if ($inf_product_id == 9) { // 14 day trial asp-w#1
		$aspire = " ASPIRE";
	}

	// Get the member_row if the only the id is passed in; otherwise just use the member_row
	if (is_array($member_id_row)) {
		$member_row = $member_id_row;
	} else {
		$member_row = GetRowMember($db, $member_id_row);		
	}

	// Get the sponsor_row if the only the id is passed in; otherwise just use the member_row
	if (is_array($sponsor_id_row)) {
		$sponsor_row = $sponsor_id_row;
	} else {
		$sponsor_row = GetRowMemberDetails($db, $sponsor_id_row);		
	}

	if (!$member_row['sponsor_unknown'] && $sponsor_row['notify_members']) {
	
		$contact_phone = empty($member_row['phone']) ? "" : "
Phone: {$member_row['phone']}";
	
		$subject = "Your team is growing! New Member: {$member_row['name']}";
		$msg = "Hello {$sponsor_row['name']},

The following new member just joined your Digital Altitude team:

Name: {$member_row['name']} 
Email: {$member_row['email']} $contact_phone

You can find more details about this member and your commissions in
the My Business section of your Digital Altitude back office:

Login: http://my.digitalaltitude.co
Email: {$sponsor_row['email']}

Excellent!

~Michael Force
Founder


UNSUBSCRIBE: You can choose to unsubscribe or adjust your subscription to 
these notifications in your Digital Altitude account here:
http://my.digitalaltitude.co/my-account/my-notifications.php



DISCLAIMER: We aim to send you accurate information in these emails, however, please
note that they are system generated and may have errors from time-to-time. So please don't
consider this email as a guarantee of any sales or commissions.

";
		SendEmail($db, $sponsor_row['email'], $subject, $msg, "Digital Altitude <support@digitalaltitude.co>");	
	}

	#Sent Text Messages to Sponsor
	if (!$member_row['sponsor_unknown'] && $sponsor_row['sms_members'] && $sponsor_row['phone_cell'] <> "") {
		$msg = "Congrats! You have a new$aspire Member:
{$member_row['name']}
{$member_row['email']}$contact_phone

~Michael Force
Founder
";
		SendTextMsg($db, $sponsor_row['phone_cell'], $sponsor_row['phone_cell_country'], $msg);
	}
}


####################################################################################################

function EmailNewMemberCoach($db, $coach_type, $coach_id_row, $member_id_row) {

	// Get the member_row if the only the id is passed in; otherwise just use the member_row
	if (is_array($member_id_row)) {
		$member_row = $member_id_row;
	} else {
		$member_row = GetRowMember($db, $member_id_row);		
	}
	// Get the sponsor_row if the only the id is passed in; otherwise just use the member_row
	if (is_array($coach_id_row)) {
		$coach_row = $coach_id_row;
	} else {
		$coach_row = GetRowMemberDetails($db, $coach_id_row);		
	}

	if ($coach_row['notify_members']) {
		
		$contact_phone = empty($member_row['phone']) ? "" : "
Phone: {$member_row['phone']}";
	
		# Send Email
		$subject = "[$coach_type Coach] New Member: {$member_row['name']}";
		$msg = "Hello {$coach_row['name']},

You have been assigned the the follow member as their '$coach_type Coach':
Please call them and get them going on in their new ASPIRE system!

Name: {$member_row['name']} 
Email: {$member_row['email']} $contact_phone

You can find more details about this member, store/review notes and help them unlock
their startup steps via the Digital Altitude Admin panel:

Direct Link: http://admin.digitalaltitude.co/members/member.php?member_id={$member_row['member_id']}
Your Email: {$coach_row['email']}

Onward and upward!

~Michael Force
Founder


UNSUBSCRIBE: You can choose to unsubscribe or adjust your subscription to 
these notifications in your Digital Altitude account here:
http://my.digitalaltitude.co/my-account/my-notifications.php



DISCLAIMER: We aim to send you accurate information in these emails, however, please
note that they are system generated and may have errors from time-to-time. So please don't
consider this email as a guarantee of anything.
";
		SendEmail($db, $coach_row['email'], $subject, $msg, "Digital Altitude <support@digitalaltitude.co>");
	
	}

	# Send Text	
	if ($coach_row['sms_members'] && $coach_row['phone_cell'] <> "") {
		$msg = "Congrats $coach_type Coach! You have a new Member:
{$member_row['name']}
{$member_row['email']}$contact_phone

~Michael Force
Founder
";
		SendTextMsg($db, $coach_row['phone_cell'], $coach_row['phone_cell_country'], $msg);
	}
}
?>

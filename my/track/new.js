if (window.location.search) {
	document.writeln("<img src='//my.digitalaltitude.co/track/new.php",window.location.search,"&url_ref=",encodeURIComponent(document.referrer),"' border='0'>");
} else {
	document.writeln("<img src='//my.digitalaltitude.co/track/new.php?url_ref=",encodeURIComponent(document.referrer),"' border='0'>");
}
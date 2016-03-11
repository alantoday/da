if (window.location.search) {
	document.writeln("<img src='//my.digitalaltitude.co/track/pixel.php",window.location.search,"&url_ref=",encodeURIComponent(document.referrer),"' border='0'>");
} else {
	document.writeln("<img src='//my.digitalaltitude.co/track/pixel.php?url_ref=",encodeURIComponent(document.referrer),"' border='0'>");
}
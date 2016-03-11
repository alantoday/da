function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
var getQueryString = function (field, url) {
    var href = url ? url : window.location.href;
    var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
    var string = reg.exec(href);
    return string ? string[1] : '';
}
if (window.location.search) {
	document.writeln("<img src='//track.digitalaltitude.co/pixel.php",window.location.search,"&url_ref=",encodeURIComponent(document.referrer),"' border='0'>");
} else {
	document.writeln("<img src='//track.digitalaltitude.co/pixel.php?url_ref=",encodeURIComponent(document.referrer),"' border='0'>");
}
var da_get = getQueryString('da');
if (da_get != '') {
	setCookie('da',da_get,365);
}
var t_get = getQueryString('t');
if (t_get != '') {
	setCookie('t',t_get,365);
}
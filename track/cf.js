function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}
var getQueryString = function (field, url) {
    var href = url ? url : window.location.href;
    var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
    var string = reg.exec(href);
    return string ? string[1] : '';
}
var da_get = getQueryString('da');
if (da_get != "") {
	setCookie('da',da_get,365);
	document.writeln('<input name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da_get + '">');
} else {
	da_cookie = getCookie('da');
	if (da_cookie != "") {
		document.writeln('<input name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da_cookie + '" readonly="readonly">');
	} else {
		var xhttp = new XMLHttpRequest();
		xhttp.open("GET", "//track.digitalaltitude.co/aff_details.php", false);
		xhttp.send();
		// Split result $da,$t
		var res = xhttp.responseText;
		var res_array = res.split(",");
		var da_remote = res_array[0];
		if (typeof res_array[1] != 'undefined') {
			var t_remote = res_array[1];
		}
		document.writeln('<input name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da_remote + '" readonly="readonly">');
	}
}
var t_get = getQueryString('t');
if (t_get != "") {
	setCookie('t',t_get,365);
	document.writeln('<input name="shipping_state" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="&t=' + t_get + '">');
} else {
	t_cookie = getCookie('t');
	if (t_cookie != "") {
		document.writeln('<input name="shipping_state" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="&t=' + t_cookie + '" readonly="readonly">');
	} else {
		document.writeln('<input name="shipping_state" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="&t=' + t_remote + '" readonly="">');
	}
}

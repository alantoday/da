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

var t_remote;

var da_get = getQueryString('da');
if (da_get) {
	setCookie('da',da_get,365);
	var da = da_get;
} else {
	var da_cookie = getCookie('da');
	if (da_cookie) {
		var da = da_cookie;
	} else {
		var xhttp;
		if (window.XMLHttpRequest) {
			xhttp = new XMLHttpRequest();
		} else {
			xhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		xhttp.open("GET", "//my.digitalaltitude.co/track/aff_details.php", false);
		xhttp.send();
		// Split result $da,$t
		var res = xhttp.responseText;
		var res_array = res.split(",");
		var da_remote = res_array[0];
		if (res_array[1]) {
			var t_remote = res_array[1];
		}
		var da = da_remote;
	}
}
var t_get = getQueryString('t');
if (t_get) {
	setCookie('t',t_get,365);
	var t = t_get;
} else {
	var t_cookie = getCookie('t');
	if (t_cookie) {
		var t = t_cookie;
	} else {
		var t = t_remote;
	}
}
//document.writeln('<input name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da + '">');
//document.writeln('<input name="shipping_state" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="&t=' + t + '">');
document.writeln('<input name="shipping_city" class="elInput elInput100 elInputBG1 elInputBR5 elInputI0 required0" data-type="extra" type="hidden" value="?da=' + da + '">');
document.writeln('<input name="shipping_state" class="elInput elInput100 elInputBG1 elInputBR5 elInputI0 required0" data-type="extra" type="hidden" value="&t=' + t + '">');


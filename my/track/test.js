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

function myJQueryCode() {
	$.get("//my.digitalaltitude.co/track/aff_details.php", function(data, status){		
		var data_array = data.split(",");
		var da_remote = data_array[0];
		if (typeof data_array[1] == 'undefined') {
			t_remote = '';
		} else {
			t_remote = data_array[1];			
		}
		alert(da_remote);
		document.writeln('<input name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da_remote + '">');
		document.writeln(' name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da_remote + '">');
	});
}

var da_get = getQueryString('da');
if (da_get != "") {
	setCookie('da',da_get,365);
	document.writeln('<input name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da_get + '">');
} else {
	da_cookie = getCookie('da');
	if (da_cookie != "") {
		document.writeln('<input name="shipping_city" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="?da=' + da_cookie + '">');
	} else {
		// Load jquery if not already loaded
		if (typeof jQuery=='undefined') {
			var headTag = document.getElementsByTagName("head")[0];
			var jqTag   = document.createElement('script');
			jqTag.type = 'text/javascript';
			jqTag.onload = function() {
				myJQueryCode();   
			}
			jqTag.src  = '//ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js';
			headTag.appendChild(jqTag);
		} else {
			myJQueryCode();
		}
	}
}
var t_get = getQueryString('t');
if (t_get != "") {
	setCookie('t',t_get,365);
	document.writeln('<input name="shipping_state" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="&t=' + t_get + '">');
} else {
	t_cookie = getCookie('t');
	if (t_cookie != "") {
		document.writeln('<input name="shipping_state" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="&t=' + t_cookie + '">');
	} else {
		document.writeln('<input name="shipping_state" class="elInput required0 garlic-auto-save" data-type="extra" type="hidden" value="&t=' + t_remote + '">');
	}
}

var getQueryString = function (field, url) {
    var href = url ? url : window.location.href;
    var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
    var string = reg.exec(href);
    return string ? string[1] : '';
};
if (window.location.search) {document.writeln("<img src='//digitalaltitude.co/da/pixel.php",window.location.search,"&url_ref=",encodeURIComponent(document.referrer),"' border='0'>");
} else {document.writeln("<img src='//digitalaltitude.co/da/pixel.php?url_ref",encodeURIComponent(document.referrer),"' border='0'>");	
} document.writeln("<iframe src='https://bl279.isrefer.com/go/pixel/",getQueryString('da'),"/' width='1' height='1' frameborder='0'>");	

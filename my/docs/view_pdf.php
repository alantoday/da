<?php 
if (isset($_GET['pdf'])) {
	$_GET['pdf'] = str_replace("https:","http:", $_GET['pdf']);
	$http_domain = preg_match("/http:/",$_GET['pdf']) ? "" : "http://my.digitalaltitude.co";
	echo "<iframe src='http://docs.google.com/gview?embedded=true&url=$http_domain{$_GET['pdf']}' 
style='width:820px; height:730px;' frameborder='0'>";
} else {
	echo "<iframe src='http://docs.google.com/gview?embedded=true&url=http://my.digitalaltitude.co/docs/aspire_example.pdf' 
style='width:820px; height:730px;' frameborder='0'>";
}
?>


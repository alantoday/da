<?
#echo $_SERVER['HTTP_HOST']."<br>".$_SERVER['PHP_SELF'];
#echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<?php
	$uri = parse_url($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); // use the parse_url() function to create an array containing information about the domain
	$url_query = $uri['query'];
?>
<a href="test.php?<?php echo $url_query;?>">Click Here (GET params passed)</a>

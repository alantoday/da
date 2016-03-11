<?php
require_once("includes/config.php");
require_once("includes/functions.php");

$default_redirect = "http://www.digitalaltitude.co";
$redirect_url = !empty($_GET['redirect']) ? $_GET['redirect'] : $default_redirect; 

if (!empty($_GET['test'])) { // for testing
	$delay = 20000;
} else {
	$delay = 1000;	
}
?>
<html><head><title>Processing</title>
</head>
<body onLoad="timer=setTimeout(function(){ window.location='<?php echo $redirect_url; ?>';}, <?php echo $delay; ?>)">
<br><br>
<center>
<script src='//digialti.com/pixel.js'></script>
<br />
<p><img src='images/processing.gif'></p>
</center>
</body>
</html>

<?php
/*
<html>
<body onload="timer=setTimeout(function(){ window.location='http://stackoverflow.com';}, 3000)">
<p>You will be redirected in 3 seconds</p>
</body>
</html>
*/
?>
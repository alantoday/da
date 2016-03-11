<?php
/* USAGE: <script src='//track.digitalaltitude.co/pixel.js'></script>
USAGE: <img src='//track.digitalaltitude.co/track.php'>
*/
error_reporting(0);
header('Content-Type: image/gif');
echo "\x47\x49\x46\x38\x37\x61\x1\x0\x1\x0\x80\x0\x0\xfc\x6a\x6c\x0\x0\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x44\x1\x0\x3b";
include '../../includes/trackaff.php';
exit;
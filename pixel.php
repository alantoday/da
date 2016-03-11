<?php
include 'includes/config.php';
include 'includes/trackaff.php';
// Return Transparent 1x1 GIF
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
exit();

###############################
# Usage: <script>document.write("<img src='//digialti.com/pixel.php",window.location.search,"' border='0'>");</script>
# Usage: <script src='//digialti.com/pixel.js'></script>
###############################

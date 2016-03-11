<?php
if (isset($_GET['mp3_link'])) {
	// Download PDF file
	header("Content-type:application/force-download");
	
	// The name of the file it wil be called.
	header("Content-Disposition:attachment;filename='{$_GET['mp3_link']}'");
	
	// The PDF source is in original.pdf
	readfile($_GET['mp3_link']);
} else { ?>
	<center>Missing: Input PDF File Path</center>
<?	
}
?>
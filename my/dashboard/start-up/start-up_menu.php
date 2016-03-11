<div style='background:<?php echo $mid_menu_color;?>;padding-top:15px;height:45px;'>
    <div class="fixed-width">
        <p style='font-size:16px;font-family:"Raleway", sans-serif;color:#ffffff;'>
        <?php $padding = 28; ?>  
		<?php echo MyWriteActiveStep("START UP","/dashboard/start-up/", $padding); ?>
        <?php 
        if (!empty($lesson)) {
            echo "     &gt;".MyWriteActiveStep("STEP $lesson", $lrow['file_path'], $padding, "1.$lesson", $mrow['step_unlocked'], $mrow['steps_completed']); 
        }
		if (isset($lesson) && $lesson == 5) {
			echo "<a style='padding-left:82px' target='_blank' href='https://rightsignature.com/forms/DigitalAltitudeND-e67b19/token/a5a40e33a52'><span style='color:#FFF'><b>Non-Disclosure Agreement (NDA) >>></b></span></a>";	
		} elseif (isset($lesson) && $lesson == 6) {
?>			
<a id="show_buynow"style='padding-left:100px' onclick="showBuyNow()" href='#'><span style='color:#FFF'><b>Click Here after watching the video >>></b></span></a>
<a id="buynow" style='padding-left:100px; display:none' target='_blank' href='/order.php?product=ris'><span style='color:#FFF'><b>I'm ready to upgrade to RISE! >>></b></span></a>
<?php            
		}
		?>
        </p>
    </div>
</div>
<script>
$(document).ready(function(){
    $("#show_buynow").click(function(){
        $("#show_buynow").hide();
        $("#buynow").show();
    });
});
</script>

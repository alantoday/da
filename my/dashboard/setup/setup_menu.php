<div style='background:<?php echo $mid_menu_color;?>;padding-top:15px; height:45px;'>
    <div class="fixed-width">
        <p style='font-size:16px;font-family:"Raleway", sans-serif;color:#ffffff;'>
        <?php $padding = 28; ?>  
		<?php echo MyWriteActiveStep("SET UP","/dashboard/setup/"); ?>
        <?php 
        if (!empty($lesson)) {
            echo "     &gt;".MyWriteActiveStep("STEP " . (6+$lrow['lesson_number']), $lrow['file_path'], $padding, "2.$lesson", $mrow['step_unlocked'], $mrow['steps_completed']); 
        }
		?>
        </p>
    </div>
</div>

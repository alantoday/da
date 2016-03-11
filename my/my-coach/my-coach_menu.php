<div style='background:<?php echo $mid_menu_color;?>;padding-top:15px; height:45px;'>
    <div class="fixed-width">
        <p style='font-size:16px;font-family:"Raleway", sans-serif;font-style:normal;font-weight:normal;color:#ffffff;'>
        <?php $padding = "24"; ?>
        <?php echo MyWriteActiveStep("MY COACHES","/my-coach", $padding); ?> &nbsp; &nbsp; &gt;
        <?php echo str_replace("<a","<a title='Welcomes you and helps you get familar with the system'", MyWriteActiveStep("WELCOME","/my-coach/my-setup-coach.php", $padding)); ?>
        <?php echo str_replace("<a","<a title='Helps you with steps 1 to 6'", MyWriteActiveStep("START UP","/my-coach/my-startup-coach.php", $padding)); ?>
        <?php echo str_replace("<a","<a title='Helps you with steps 7 to 18'", MyWriteActiveStep("SET UP & SCALE UP","/my-coach/my-scale-coach.php", $padding, 2.1, $mrow['step_unlocked'], $mrow['steps_completed'])); ?>
        <?php echo str_replace("<a","<a title='Helps you with traffic'", MyWriteActiveStep("TRAFFIC","/my-coach/my-traffic-coach.php", $padding, 3, $mrow['step_unlocked'], $mrow['steps_completed'])); ?>
        <?php echo str_replace("<a","<a title='Helps you succeed'", MyWriteActiveStep("SUCCESS","/my-coach/my-success-coach.php", $padding, 3, $mrow['step_unlocked'], $mrow['steps_completed'])); ?>
        </p>
    </div>
</div>
                    

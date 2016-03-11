<div style='background:<?php echo $mid_menu_color;?>;padding-top:17px;height: 43px;' class="row one-column cf ui-sortable">
    <div class="fixed-width">
        <p style='font-size:16px;font-family:"Raleway", sans-serif;font-style:normal;font-weight:normal;color:#ffffff;'>
		<?php echo MyWriteActiveStep("PRODUCTS","/products"); ?>     &gt;
        <?php echo MyWriteActiveStep("BASE COURSE","/products/base/course"); ?>
        <?php 
        if (!empty($lesson)) {
            echo "     &gt;" . MyWriteActiveStep("LESSON $lesson","/products/base/course/lesson-$lesson.php"); 
        }
        ?>      
        </p> 
    </div>
</div>

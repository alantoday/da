<?php
# Get coaches details
$crow = GetRowMemberDetails($db, $coach_id);
?>

<div class="fixed-width">
	<div style="padding-top:50px;padding-bottom:50px;" class="row four-columns cf ui-sortable" id="le_body_row_3">
    	<div class="one-third column cols narrow" id="le_body_row_3_col_1">
            <div style="margin-left:15px;height:150px;width:150px;border-radius:150px;background:url('<?php echo get_gravatar($crow['gravatar'], 150);?>');background-size:cover;background-position:center center;"></div>
            <?php if($crow['book_call'] != "") { ?>
            <p align=left style="padding-top:15px;">
                &nbsp;  &nbsp;  &nbsp; <a align=left href="<?=$crow['book_call']?>" target="_blank" class="btn">
                <font color=white>&nbsp; BOOK CALL &nbsp;</font></a>
            </p>
			<? } ?>
            <div style="padding-left:0px; padding-top:15px;">
            <p>Name:
            <b><?php echo $crow['name']; ?></b>
            <p>Phone:
            <b><?php echo $crow['phone']; ?></b>
            <br>Email:
            <b><?php echo $crow['email']; ?></b>
            <br>Skype:
            <b><?php echo $crow['skype']; ?></b><br>
            <?php if($crow['facebook'] != "") { ?>
            	<a href="<?=$crow['facebook']?>" target="_blank"><img src="/images/socialicon/facebook.png" height="50px"></a>
            <?php } ?>
            <?php if($crow['facebook'] != "") { ?>
            	<a href="<?=$crow['facebook']?>" target="_blank"><img src="/images/socialicon/facebook.png" height="50px"></a>
            <?php } ?>
            <?php if($crow['blog'] != "") { ?>
            	<a href="<?=$crow['blog']?>" target="_blank"><img src="/images/socialicon/blogger.png" height="50px"></a>
            <?php } ?>
            </p>
            </div>
        </div>
        <div class="two-thirds column cols" id="le_body_row_3_col_2">
            <?php echo WriteIncludeHTML($db, "coach-$coach_id", LESSON_AUTHOR); ?>
        </div>
	</div> 
</div>
<?php include(PATH."/includes/functions_inf.php"); ?>
<?php
# INPUTS: $db, 
# $product eg, base
# $lesson eg, 1
# $path = getcwd()

$mrow = GetRowMemberCoaches($db, $_SESSION['member_id']);
$lrow = GetRowLesson($db, $product, $lesson);
$unlocked_step = UnlockedStep($db, $mrow['member_id'], "{$lrow['lesson_type']}.$lesson", $mrow['step_unlocked']);

$locked_step_msg = "";
if (!$unlocked_step) {
	$locked_step_msg = "Please contact your ".WriteCoachType($lrow['coach_type'])." coach<br>to get access to this step.";
}
echo MyWriteMidSectionVideo($lrow['video_code'], $locked_step_msg);

include($path."/{$product}_menu.php");

$text_color = isset($menu_color['top'][$product]) ? $menu_color['top'][$product] : $menu_color['top']['default'];
?>
<?php if ($unlocked_step) { ?>

<?php
# Have user sign-off each step (1 to 18) in these sections
if (in_array($product, array("start-up", "setup", "scale"))) {
	$confirm_step_name = "$product-$lesson";
}
if (isset($confirm_step_name)) {
	$query = "SELECT * 
		FROM member_steps 
		WHERE step_name='$confirm_step_name' 
		AND member_id='{$_SESSION['member_id']}'";
	$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
	$member_steps_row = mysqli_fetch_assoc($result);
	 
	if (isset($_POST['confirm_step'])) {
		if (!empty($_POST['signature'])) {
			if (!$member_steps_row) {
				$query = "REPLACE INTO member_steps 
					SET step_name = '{$_POST['confirm_step']}'
					, step_completed = 1
					, member_id = {$_SESSION['member_id']}
					, create_date = now()";
			} else {
				$query = "UPDATE member_steps
					SET step_completed = 1
					WHERE member_step_id='{$member_steps_row['member_step_id']}'";
			}
			$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			$confirm_msg = "<font color=#51A7FA><b>SUCCESS: You've signed this lesson off as complete and can now move onto the next lesson.</b></font><br><br>";
			$member_steps_row['create_date'] = date("Y-m-d H:m:i");
			
			// Update members table too
			$steps_completed = "{$lrow['lesson_type']}.$lesson";
			# Flag this step as complete in DA
			if ($mrow['steps_completed'] <= $steps_completed) {
				$query = "UPDATE members
							SET steps_completed = $steps_completed
							WHERE member_id = {$_SESSION['member_id']}";
				$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
			}
			# Conver X.Y step number to 1 to 18 number
			$step_number = WriteStepNumber($steps_completed);
			
			# Flag this step as complete in INF
			
			$inf_tag_id = false;
			switch($step_number) {
				case 1: $inf_tag_id = INF_TAG_COMPLETED_STEP_1; break;	
				case 2: $inf_tag_id = INF_TAG_COMPLETED_STEP_2; break;	
				case 3: $inf_tag_id = INF_TAG_COMPLETED_STEP_3; break;	
				case 4: $inf_tag_id = INF_TAG_COMPLETED_STEP_4; break;	
				case 5: $inf_tag_id = INF_TAG_COMPLETED_STEP_5; break;	
				case 6: $inf_tag_id = INF_TAG_COMPLETED_STEP_6; break;	
				case 7: $inf_tag_id = INF_TAG_COMPLETED_STEP_7; break;	
				case 8: $inf_tag_id = INF_TAG_COMPLETED_STEP_8; break;	
				case 9: $inf_tag_id = INF_TAG_COMPLETED_STEP_9; break;	
				case 10: $inf_tag_id = INF_TAG_COMPLETED_STEP_10; break;	
				case 11: $inf_tag_id = INF_TAG_COMPLETED_STEP_11; break;	
				case 12: $inf_tag_id = INF_TAG_COMPLETED_STEP_12; break;	
				case 13: $inf_tag_id = INF_TAG_COMPLETED_STEP_13; break;	
				case 14: $inf_tag_id = INF_TAG_COMPLETED_STEP_14; break;	
				case 15: $inf_tag_id = INF_TAG_COMPLETED_STEP_15; break;	
				case 16: $inf_tag_id = INF_TAG_COMPLETED_STEP_16; break;	
				case 17: $inf_tag_id = INF_TAG_COMPLETED_STEP_17; break;	
				case 18: $inf_tag_id = INF_TAG_COMPLETED_STEP_18; break;	
			}
			if ($inf_tag_id) {
				InfAddTag($mrow['inf_contact_id'], $inf_tag_id);
			}
		} else {
			$confirm_msg = "<font color=yellow><b>Missing: Your name.</b></font><br>";
		}
	}
}
?>
<div style="padding-top:30px;" class="row five-columns cf ui-sortable" id="le_body_row_3">
    <div class="fixed-width">
<?php 
if (!empty($_GET['not-complete']) && !empty($confirm_step_name)) { ?>
		<a name="confirm-step1"></a>
		<div align=center>
		<form action="#confirm-step1" method="post">
			<input type="hidden" name="confirm_step" value="<?php echo $confirm_step_name; ?>" />
	<div style="margin-top:20px;padding-bottom:0px;" class="row one-column cf ui-sortable" id="le_body_row_4">
		<div class="fixed-width">
			<div class="one-column column cols" id="le_body_row_4_col_1"><div class="element-container cf" id="le_body_row_4_col_1_el_1">
				<div class="element"> 
				<table width="100%" border="1" cellspacing="" bordercolor="#000000" bgcolor="yellow">
				<tr>
					<td style="padding:20px">
					<center>
						<span style="font-size:18px"><font color=#000>You must <b>sign off this step as complete</b> before moving onto the next step.<br /><br />
						<?php if (!empty($confirm_msg)) echo $confirm_msg; ?>
						YES! I have completed this lesson:
						<?php 
						if (empty($member_steps_row['create_date'])) {
							echo '<input type="text" name="signature" value="" style="display:inline" placeholder="Enter your name"/> &nbsp; ';
							echo WriteButton("Submit");
						} else {
							printf("<font color=''><b>%s</b></font>", WriteDate($member_steps_row['create_date']));
						} 
						?></font>
						</center>
					</td>
				</tr>
			</table>
				</div>
			</div>
		</div>
	</div>
		</form>
		</div>
<br><br />
<?php } ?>
        <div class="three-fifths column cols" id="le_body_row_3_col_1">
          <div class="element-container cf" id="le_body_row_3_col_1_el_1">
            <div class="element">
              <h2 style="font-size:50px;font-style:normal;font-weight:300;color:<? echo $text_color; ?>;text-align:left;"> <span style="font-family: da-font;"><?php echo $lrow['title']; ?>:</span> </h2>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_1_el_3">
            <div class="element">
              <div class="op-text-block" style="width:100%;text-align: left;padding-top:0px;"><?php echo WriteIncludeHTML($db, "$product-lesson-$lesson", LESSON_AUTHOR); ?></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_1_el_5">
            <div class="element">
              <div class="image-caption" style="width:100px;margin-top:0px;float: left;"> <img alt="" src="http://www.members.digitalaltitude.co/wp-content/uploads/2015/11/MICHAEL-FORCE.png" border="0" class="full-width"> </div>
            </div>
          </div>
        </div>
<!-- LEFT SECTION -->

<!-- RIGHT SECTION -->
    	<style type="text/css">
        #download_button .text {
            font-size:14px;
            color:#ffffff;
            font-family:Ubuntu;
            font-weight:normal;
        }
        #download_button {
            width:100%;
            padding:10px 0;
            background:#53585F;
        }
        </style>
        <div class="one-fifth column cols narrow" id="le_body_row_3_col_2">
          <div class="element-container cf" id="le_body_row_3_col_2_el_1">
            <div class="element">
              <div style="height:50px;" class="arrow-center"> <img src="/images/lesson_icons/arrow.png" class="arrows" alt="arrow"> </div>
            </div>
          </div>
          <div class="element-container cf" data-style="" id="le_body_row_3_col_2_el_2">
          	<div class="element"> 
          		<h2 style="font-size:24px;font-family:&quot;Shadows Into Light&quot;, sans-serif;text-align:center;">Overview PDF</h2> 
          	</div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_2_el_3">
            <div class="element">
              <div class="image-caption" style="height:224px;"><?php echo _WriteAttachmentImg($lrow['pdf_1_img'],$lrow['pdf_1_link']) ?></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_2_el_4">
            <div class="element">
              <div style="text-align:center"> <?php echo _WriteAttachmentButton("DOWNLOAD",$lrow['pdf_1_link']) ?></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_2_el_5">
            <div class="element">
              <div style="height:60px"> </div>
            </div>
          </div>
			<div class="element-container cf" data-style="" id="le_body_row_3_col_2_el_7">
				<div class="element"> 
					<h2 style="font-size:24px;font-family:&quot;Shadows Into Light&quot;, sans-serif;text-align:center;">Checklist PDF</h2> 
				</div>
			</div>          
          <div class="element-container cf" id="le_body_row_3_col_2_el_6">
            <div class="element">
              <div class="image-caption" style="height:224px;"><?php echo _WriteAttachmentImg($lrow['pdf_3_img'],$lrow['pdf_3_link']) ?></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_2_el_7">
            <div class="element">
              <div style="text-align:center"><?php echo _WriteAttachmentButton("DOWNLOAD",$lrow['pdf_3_link']) ?></div>
            </div>
          </div>
<?php if (trim($lrow['order_link']) <> "") { ?>
          <div class="element-container cf" id="le_body_row_3_col_2_el_8">
            <div class="element">
              <div style="height:60px"></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_2_el_9">
            <div class="element">
              <div class="image-caption" style="height:224px;"><?php echo _WriteAttachmentImg($lrow['order_img'],$lrow['order_link']) ?></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_2_el_10">
            <div class="element">
              <div style="text-align:center"><?php echo _WriteAttachmentButton("ORDER",$lrow['order_link']) ?></div>
            </div>
          </div>
<?php } ?>
        </div>
        
        <div class="one-fifth column cols narrow" id="le_body_row_3_col_3">
          <div class="element-container cf" id="le_body_row_3_col_3_el_1">
            <div class="element">
              <div style="height:50px"></div>
            </div>
          </div>
			<div class="element-container cf" data-style="" id="le_body_row_3_col_3_el_2">
				<div class="element"> 
					<h2 style="font-size:24px;font-family:&quot;Shadows Into Light&quot;, sans-serif;text-align:center;">Study Guide PDF</h2> 
				</div>
			</div>
          <div class="element-container cf" id="le_body_row_3_col_3_el_3">
            <div class="element">
              <div class="image-caption" style="height:224px;"><?php echo _WriteAttachmentImg($lrow['pdf_2_img'],$lrow['pdf_2_link']) ?></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_3_el_4">
            <div class="element">
              <div style="text-align:center"><?php echo _WriteAttachmentButton("DOWNLOAD",$lrow['pdf_2_link']) ?></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_3_el_5">
            <div class="element">
              <div style="height:60px"></div>
            </div>
          </div>
			<div class="element-container cf" data-style="" id="le_body_row_3_col_3_el_7">
				<div class="element"> 
					<h2 style="font-size:24px;font-family:&quot;Shadows Into Light&quot;, sans-serif;text-align:center;">Lesson Audio</h2> 
				</div>
			</div>          
          <div class="element-container cf" id="le_body_row_3_col_3_el_6">
            <div class="element">
              <div class="image-caption" style="height:224px;"><img alt="" src="/images/lesson_icons/mp3.png" border="0" class="full-width"></div>
<?php /*              <div class="image-caption" style="height:224px;"><?php echo _WriteAttachmentImg($lrow['pdf_4_img'],$lrow['pdf_4_link']) ?></a></div> */ ?>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_3_el_7">
            <div class="element">
<?php /*              <div style="text-align:center"><?php echo _WriteAttachmentButton("DOWNLOAD",$lrow['pdf_4_link']) ?></div> */ ?>
              <div style="text-align:center"><?php echo _WriteAttachmentButton("MP3",$lrow['mp3_link']) ?></div>
            </div>
          </div>
<?php /*          <div class="element-container cf" id="le_body_row_3_col_3_el_8">
            <div class="element">
              <div style="height:60px"></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_3_el_9">
            <div class="element">
              <div class="image-caption" style="height:224px;"><img alt="" src="/images/lesson_icons/mp3.png" border="0" class="full-width"></div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_3_col_3_el_10">
            <div class="element">
              <div style="text-align:center"><?php echo _WriteAttachmentButton("MP3",$lrow['mp3_link']) ?></div>
            </div>
          </div>
*/ ?>
        </div>
</div></div>

<!-- CONFIRMATION SIGNATURE -->
<?php if (!empty($confirm_step_name)) { ?>
    <a name="confirm-step"></a>
    <div align=center>
	<form action="#confirm-step" method="post">
		<input type="hidden" name="confirm_step" value="<?php echo $confirm_step_name; ?>" />
<div style="margin-top:20px;padding-bottom:0px;" class="row one-column cf ui-sortable" id="le_body_row_4">
	<div class="fixed-width">
		<div class="one-column column cols" id="le_body_row_4_col_1"><div class="element-container cf" id="le_body_row_4_col_1_el_1">
			<div class="element"> 
                    <table width="100%" border="1" cellspacing="" bordercolor="#000000" bgcolor="#C82506">
            <tr>
                <td style="padding:20px">
                <center>
                    <span style="font-size:18px"><font color=#FFF><b>Submit your name</b> (as your digital signature) after you have completed this step.<br /><br />
					<?php if (!empty($confirm_msg)) echo $confirm_msg; ?>
                    YES! I have completed this lesson:
					<?php 
					if (empty($member_steps_row['create_date'])) {
                    	echo '<input type="text" name="signature" value="" style="display:inline"  placeholder="Enter your name" /> &nbsp; ';
                    	echo WriteButton("Submit");
                    } else {
						printf("<font color=''><b>%s</b></font>", WriteDate($member_steps_row['create_date']));
					} 
					?></font>
                    </center>
	            </td>
            </tr>
        </table>
			</div>
		</div>
	</div>
</div>
	</form>
    </div>
<?php } ?>

<!-- FEEDBACK BUTTON -->
<div style="margin-top:50px;padding-bottom:50px;" class="row one-column cf ui-sortable" id="le_body_row_4">
	<div class="fixed-width">
		<div class="one-column column cols" id="le_body_row_4_col_1"><div class="element-container cf" id="le_body_row_4_col_1_el_1">
			<div class="element"> 
				<style type="text/css">#btn_1_e7841850dfacab76419ddc2dc315344d .text {font-size:30px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_e7841850dfacab76419ddc2dc315344d .subtext {font-size:20px;color:#ffffff;font-weight:normal;}#btn_1_e7841850dfacab76419ddc2dc315344d {width:100%;padding:30px 0;border-radius:0px;background:#42bd1f;box-shadow:none;}</style>
				<a href="http://www.surveygizmo.com/s3/2443569/How-Can-We-Do-Better" target="_blank" id="btn_1_e7841850dfacab76419ddc2dc315344d" class="css-button style-1">
				<span class="text">How Can We Do Better With This Lesson?</span><span class="subtext">Click Here To Answer Anonymously And Honestly...</span><span class="hover"></span><span class="active"></span></a>
			</div>
		</div>
	</div>
</div>
<?php } else { ?>
<div style="padding-top:30px;" class="row five-columns cf ui-sortable" id="le_body_row_3">
    <div class="fixed-width">
<?php 
	echo "<h2>$locked_step_msg</h2><hr>";
	$coach_id = $mrow["coach_id_{$lrow['coach_type']}"];
	include(PATH."my/my-coach/inc_coach_details.php");
?>
	</div>
</div>
<?php } ?>


<!-- LESSON FOOTER -->
<?php
echo _WriteLessonFooter($product, $lesson);

# Looks for next Enabled Lesson - if it's not found it returns 0;
function _NextLessonDetails ($db, $product, $lesson) {
	do {
		$lesson++;		
	} while (($lrow = GetRowLesson($db,$product,$lesson)) && !$lrow['enabled']);
	
	if (isset($lrow['enabled']) && $lrow['enabled']) {
		return array("number"=>$lesson
					, "link"=>$lrow['file_path']
					, "video_img"=>$lrow['video_img']
					);
	} else {
		return 0;	
	}	
}

# Looks for next Enabled Lesson - if it's not found it returns 0;
function _PrevLessonDetails ($db, $product, $lesson) {
	do {
		$lesson--;		
	} while (($lrow = GetRowLesson($db, $product, $lesson)) && !$lrow['enabled']);
	
	if (isset($lrow['enabled']) && $lrow['enabled']) {
		return array("number"=>$lesson
					, "link"=>$lrow['file_path']
					, "video_img"=>$lrow['video_img']
					);
	} else {
		return 0;	
	}	
}
/*
# Return thumbnail for PDF
function _CreatePDFThumbnail2 ($pdf) {
	echo $pdf;
	$im = new Imagick();
	$im->setResolution(300, 300);
	$im->readImage($pdf.'[0]');
	$im->setImageFormat('jpg');
	header('Content-Type: image/jpeg');
	echo $im;
}
*/
function _WriteAttachmentButton ($button_name, $button_link, $download = true) {
	$force_download = $download ? "download" : "";
	if ($button_link == "#" || $button_link == "") {
		return "<a href='javascript:void(0)' onclick=\"alert('Coming soon.');\" id='download_button' class='css-button style-1'> <span class='text'>COMING SOON</span><span class='hover'></span><span class='active'></span></a>";		
	} else {
		return "<a href='$button_link' $force_download id='download_button' class='css-button style-1'> <span class='text'>$button_name</span><span class='hover'></span><span class='active'></span></a>";
	}
}
/*
function _WriteMP3Button ($button_name, $button_link, $download = true) {
	$force_download = $download ? "download" : "";
	if ($button_link == "#" || $button_link == "") {
		return "<a href='javascript:void(0)' onclick=\"alert('Coming soon.');\" id='download_button' class='css-button style-1'> <span class='text'>COMING SOON</span><span class='hover'></span><span class='active'></span></a>";		
	} else {
		return "<a href='/docs/mp3/download_mp3.php?mp3_link=$button_link' id='download_button' class='css-button style-1'> <span class='text'>$button_name</span><span class='hover'></span><span class='active'></span></a>";
	}
}
*/
# Note: "download" in the <a> field makes it download instead of opens a new tab
function _WriteAttachmentImg ($img_path, $img_link) {
	global $fancy_box_count;
	if(!isset($fancy_box_count)) {
		$fancy_box_count = 1;
	} else {
		$fancy_box_count++;
	}
	if ($img_link == "#" || $img_link == "") {
		return "<a href='javascript:void(0)' onclick=\"alert('Coming soon.');\"><img alt='' src='$img_path' border='0' class='full-width'></a>";		
	} else {
		return "<a class='fancybox_$fancy_box_count' rel='group' data-fancybox-type='iframe' href='/docs/view_pdf.php?pdf=$img_link'><img alt='View PDF' src='$img_path' border='0' class='full-width'></a>
        <script>
            $(document).ready(function() {
                $('.fancybox_$fancy_box_count').fancybox(
                    {minWidth:828,minHeight:735,
                     maxWidth:828,maxHeight:735
                    }
                );
            });
		</script>";
	}      
}

function _WriteLessonFooter ($product, $lesson) {
	global $menu_color, $db;

    $button_color = isset($menu_color['top'][$product]) ? $menu_color['top'][$product] : $menu_color['top']['default'];

	$prev_lesson = _PrevLessonDetails($db, $product, $lesson);
	$next_lesson = _NextLessonDetails($db, $product, $lesson);
	if ($prev_lesson) {
		$prev_lesson_link = $prev_lesson['link'];
		$prev_lesson_text = "PREVIOUS LESSON";
		$prev_img = $prev_lesson['video_img'];
	} else {
		if (in_array($product, array("base", "rise"))) {
			$prev_lesson_link = "/products/$product/course/";	
		} else {
			$prev_lesson_link = "/dashboard/$product/";	
		}
		$prev_lesson_text = "COURSE OVERVIEW";
		$prev_img = str_replace(2,0,$next_lesson['video_img']);
	}
	if ($next_lesson) {
		$next_lesson_link = $next_lesson['link'];
		$next_img = $next_lesson['video_img'];
	} else {
		$next_lesson_link = "";
		$next_img = ""; // Blank		
	}
	
	if ($prev_img <> "") $prev_img = "<img alt='' src='$prev_img' border='0' class='full-width'>";
	if ($next_img <> "") $next_img = "<img alt='' src='$next_img' border='0' class='full-width'>";

	$res = "";	
    $res = <<<EOF
</div>
<div style="background:#303030;padding-top:50px;padding-bottom:50px;" class="row two-columns cf ui-sortable" id="le_body_row_5">
	<div class="fixed-width">
		<div class="one-half column cols" id="le_body_row_5_col_1">
			<div class="element-container cf" id="le_body_row_5_col_1_el_1">
				<div class="element"> 
					<div class="image-caption" style="width:1041px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;">
						$prev_img
					</div>
				</div>
			</div>
		<div class="element-container cf" id="le_body_row_5_col_1_el_2"><div class="element">
			<div style="text-align:left">
				<style type="text/css">#btn_1_e846393c68ca9c80eae8c4d98901afcd .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_e846393c68ca9c80eae8c4d98901afcd {width:100%;padding:20px 0;border-radius:0px;background:$button_color;box-shadow:none;}</style>
				<a href="$prev_lesson_link" id="btn_1_e846393c68ca9c80eae8c4d98901afcd" class="css-button style-1"><span class="text">← $prev_lesson_text</span><span class="hover"></span><span class="active"></span></a>
			</div>
		</div>
	</div>
</div>
<div class="one-half column cols" id="le_body_row_5_col_2"><div class="element-container cf" id="le_body_row_5_col_2_el_1">
	<div class="element"> 
		<div class="image-caption" style="width:748px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;">
			$next_img
		</div>
	</div>
</div>
EOF;
	if ($next_lesson) {
		$res .= <<<EOF
<div class="element-container cf" id="le_body_row_5_col_2_el_2">
	<div class="element"> 
		<div style="text-align:left">
			<style type="text/css">#btn_1_fbcd5a5b4c34fb5c81124dd55930533a .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_fbcd5a5b4c34fb5c81124dd55930533a {width:100%;padding:20px 0;border-radius:0px;background:$button_color;box-shadow:none;}</style>
			<a href="$next_lesson_link" id="btn_1_fbcd5a5b4c34fb5c81124dd55930533a" class="css-button style-1"><span class="text">NEXT LESSON →</span><span class="hover"></span><span class="active"></span></a>
		</div> 
	</div>
</div>
EOF;
	}
	return $res."</div></div></div>";
}
?>

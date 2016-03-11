<?php
include_once("../../includes/config.php");
include_once("../../includes/functions.php");
include_once("../includes_admin/include_menu.php");

$mrow = GetRowMemberDetails($db, $_SESSION['member_id']);
$force_row = GetRowMemberDetails($db, 100);
?>
<h1 id="page_title">Onboarding For Coaches</h1>

<h2>Step 1: Watch The Onboarding Video </h2>
<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script><span class="wistia_embed wistia_async_1ouhuwuet3 popover=true popoverAnimateThumbnail=true" style="display:inline-block;height:169px;width:300px">&nbsp;</span>

<h2>Step 2: Setup Your Photo &amp; Contact Details/Links [<a target='_blank' href='/members/member_login.php?member_id=<?php echo $_SESSION['member_id']; ?>&pg=/my-account/my-profile.php'><strong>Log into the Member's Area</strong></a>]</h2>
<p>The members that you have been assigned to you will see your details/links in their My Coaches page. Don't forget to add a "Book Call" Link.</p>

<h2>Step 3: Update Your Coach Profile [<a href='/coaches/profiles.php'><strong>Edit Your Profile Now</strong></a>]</h2>
<p>See how your profile will appear to your members below. Compare it to the example at the bottom.</p>

<h2>Step 4: Review Coach Resources [<a href="/coaches/resources.php">Review Now</a>]</h2>

<br /><hr />
<p><strong>Your Current Coach Profile [<a href='/coaches/profiles.php'>Edit</a>]</strong></p>

<?php _OutputCoachProfile($db, $mrow); ?>
<hr />
<p><strong>Example Coach Profile</strong></p>
<?php _OutputCoachProfile($db, $force_row); ?>

<?php function _OutputCoachProfile($db, $coach_row) { ?>
<table>
    <tr><td valign="top">
<div class="fixed-width">
	<div style="padding-top:0px;padding-bottom:50px;" class="row four-columns cf ui-sortable" id="le_body_row_3">
    	<div class="one-third column cols narrow" id="le_body_row_3_col_1" style="width:230px;">
            <div style="margin-left:15px;height:150px;width:150px;border-radius:150px;background:url('<?php echo get_gravatar($coach_row['gravatar'], 150);?>');background-size:cover;background-position:center center;"></div>
            <?php if($coach_row['book_call'] != "") { ?>
            <p align=left style="padding-top:15px;">
                &nbsp;  &nbsp;  &nbsp; <a align=left href="<?=$coach_row['book_call']?>" target="_blank" class="btn">
                <font color=white>&nbsp; BOOK CALL &nbsp;</font></a>
            </p>
			<? } ?>
            <div style="padding-left:0px; padding-top:15px;">
            <p>Name:
            <b><?php echo $coach_row['name']; ?></b>
            <p>Phone:
            <b><?php echo $coach_row['phone']; ?></b>
            <br>Email:
            <b><?php echo $coach_row['email']; ?></b>
            <br>Skype:
            <b><?php echo $coach_row['skype']; ?></b><br>
            <?php if($coach_row['facebook'] != "") { ?>
            	<a href="<?=$coach_row['facebook']?>" target="_blank"><img src="//my.digitalaltitude.co/images/socialicon/facebook.png" height="50px"></a>
            <?php } ?>
            <?php if($coach_row['twitter'] != "") { ?>
            	<a href="<?=WriteTwitterLink($coach_row['twitter'])?>" target="_blank"><img src="//my.digitalaltitude.co/images/socialicon/twitter.png" height="50px"></a>
            <?php } ?>
            <?php if($coach_row['blog'] != "") { ?>
            	<a href="<?=WriteURLHTTP($coach_row['blog'])?>" target="_blank"><img src="//my.digitalaltitude.co/images/socialicon/blogger.png" height="50px"></a>
            <?php } ?>
            </p>
            </div>
        </div>
        <div class="two-thirds column cols" id="le_body_row_3_col_2">
        </div>
	</div> 
</div>
</td><td valign="top">
        <div class="two-thirds column cols" id="le_body_row_3_col_2">
            <?php echo WriteIncludeHTML($db, "coach-{$coach_row['member_id']}", false); ?>
	</div>
</td></td>
</table>  
<?php } ?>
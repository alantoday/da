<?php include("../includes_my/header.php"); ?>
<?php /*
<div style='background-image:url(//s3.amazonaws.com/public.digitalaltitude.co/images/members/AspireCallAsh.jpg); background-repeat:no-repeat; background-size:cover; height:345px;' class="row five-columns cf ui-sortable section">
  <div class="fixed-width">
    <div class="four-fifths column cols" id="le_body_row_1_col_1">
      <div class="element-container cf" id="le_body_row_1_col_1_el_1">
      </div>
    </div>
  </div>
</div>
*/  ?>
<?php echo MyWriteMidSection("LIVE FEED", "Live Expert Trainings Weekly",
	"We teach digital entrepreneurs how to start and grow a profitable business with our unique products and live events.",
	"CALL COACH", "/my-coach",
	"GET SUPPORT","https://digitalaltitude.zendesk.com/"); ?>
<?php include("tv_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>
<?php
$query = "SELECT embed
			FROM tv
			WHERE active = 1";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
$row = mysqli_fetch_assoc($result);
?>
<div style="padding-top:40px;padding-bottom:50px;" class="row one-column cf ui-sortable" id="le_body_row_3">
<center>
<?php if ($row['embed']<>'') { ?>
<iframe width="900" height="505" src="<?php echo $row['embed']?>" frameborder="0" allowfullscreen></iframe>
<?php } ?>
</center>
</div>
<?php /*
<div style="padding-top:40px;padding-bottom:50px;" class="row one-column cf ui-sortable" id="le_body_row_3" data-style="eyJwYWRkaW5nVG9wIjoiNTAiLCJwYWRkaW5nQm90dG9tIjoiNTAiLCJib3JkZXJUb3BXaWR0aCI6IiIsImJvcmRlclRvcENvbG9yIjoiIiwiYm9yZGVyQm90dG9tV2lkdGgiOiIiLCJib3JkZXJCb3R0b21Db2xvciI6IiIsImFkZG9uIjp7fX0=">
                        <div class="fixed-width"><div class="one-column column cols" id="le_body_row_3_col_1"><div class="element-container cf" id="le_body_row_3_col_1_el_1"><div class="element"> <div class="op-custom-html-block"><iframe src="http://www.ustream.tv/embed/21311518?html5ui" style="border: 0 none transparent;" webkitallowfullscreen="" allowfullscreen="" frameborder="no" width="900" height="538"></iframe><br></div> </div></div><div class="element-container cf" id="le_body_row_3_col_1_el_2"><div class="element"> <div class="op-custom-html-block"><iframe src="http://www.ustream.tv/socialstream/21311518" style="border: 0 none transparent;" webkitallowfullscreen="" allowfullscreen="" frameborder="no" width="900" height="302"></iframe><br></div> </div></div></div></div></div>
*/ ?>
<?php $footer_image = true; ?>
<?php include(INCLUDES_MY."footer.php"); ?>
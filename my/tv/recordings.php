<?php include("../includes_my/header.php"); ?>
<?php /* <div style='background-image:url(//s3.amazonaws.com/public.digitalaltitude.co/images/members/AspireCallAsh.jpg); background-repeat:no-repeat; background-size:cover; height:345px;' class="row five-columns cf ui-sortable section">
  <div class="fixed-width">
    <div class="four-fifths column cols" id="le_body_row_1_col_1">
      <div class="element-container cf" id="le_body_row_1_col_1_el_1">
      </div>
    </div>
  </div>
</div>
*/ ?>
<?php echo MyWriteMidSection("RECORDINGS", "Expert Training Archives",
	"We teach digital entrepreneurs how to start and grow a profitable business with our unique products and live events.",
	"CALL COACH", "/my-coach",
	"GET SUPPORT","https://digitalaltitude.zendesk.com/"); ?>
<?php include("tv_menu.php"); ?>
<?php echo MyWriteMainSectionTop(30); ?>
<?php

$query = "SELECT * FROM recordings WHERE status = 1 ORDER BY call_date DESC";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
for($i=1; $row = mysqli_fetch_assoc($result); $i++){
	$video_js = '<div style="padding-top:5px"><script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script><span class="wistia_embed wistia_async_'.$row['wistia_id'].' popover=true" style="display:inline-block;height:75px;width:135px">&nbsp;</span></div>';	
?>
    <table width="100%">
    <tr style="border-bottom:1px solid #CCC;">
    <td width="150px"><?=$video_js?></td>
    <td style="vertical-align:top">
            <p><b><?=WriteDate($row['call_date'])?></b> - <b><?=$row['title']?></b>
            <br><?=$row['host']<>"" ? "Host: ".$row['host'] : ""?>	
            <br><?=$row['description']?></p>
    </td>
    </tr>
    </table>
<?php	
}
?>

<?php include(INCLUDES_MY."footer.php"); ?>
<?php 
include_once("../includes_my/config.php");
include_once(PATH."includes/functions.php");
$mrow = GetRowMember($db, $_SESSION['member_id']); 
// Don't let them see Dashboard until they completed step 1.6
// Force them to complete steps in order - redirect then to earliest completed step
if ($mrow['steps_completed'] < "1.6") {
	$redirect_lesson = 10 * ($mrow['steps_completed'] - 1) + 1; // eg, if they started at 1.2, take them to 1.3, ie, $lesson 3 for "start-up"
	header("location: /dashboard/start-up/step-$redirect_lesson.php");	
	exit();
}
?>
<?php include("../includes_my/header.php"); ?>
<?php echo MyWriteMidSection("LET'S CLIMB...", "Welcome To Our Member's Area",
	"Welcome to our 118-page members area that is bursting with training, expertise, community, tools and resources to ensure your success while climbing to the top.",
	"DOWNLOAD SKYPE","http://www.skype.com/go/downloading",
	"CALL COACH", "/my-coach"); ?>
<?php include("../products/products_menu.php"); ?>
<div style='padding-top:50px;padding-bottom:50px;'  class="row one-column cf ui-sortable section learn" id="le_body_row_3">
  <div class="fixed-width">
    <div class="one-column column cols" id="le_body_row_3_col_1">
      <div class="element-container cf icons-top" id="le_body_row_3_col_1_el_1">
        <div class="element">
          <ul class="feature-block feature-block-style-4 feature-block-four-col cf">
            <li><a class="feature-block-link" href="/dashboard/start-up" style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>
              <div> <span class="feature-block-4-img-container" style="background-color: #c70000;"><img class="feature-block-4" src="/images/271-alt.png" /></span>
                <h4 style='text-align:center;padding-top:10px;font-family:"Raleway", sans-serif;font-weight:bold;color:#2b2b2b;'>START UP</h4>
                <p style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>Learn simple and easy start-up steps to build a profitable, global online business.</p>
              </div>
              </a></li>
            <li><a class="feature-block-link" href="/dashboard/setup" style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>
              <div> <span class="feature-block-4-img-container" style="background-color: #f99e01;"><img class="feature-block-4" src="/images/126-alt.png" /></span>
                <h4 style='text-align:center;padding-top:10px;font-family:"Raleway", sans-serif;font-weight:bold;color:#2b2b2b;'>SET UP</h4>
                <p style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>Automate your online business systems and processes to increase profits.</p>
              </div>
              </a></li>
            <li><a class="feature-block-link" href="/dashboard/scale" style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>
              <div> <span class="feature-block-4-img-container" style="background-color: #6EBE44;"><img class="feature-block-4" src="/images/318-alt.png" /></span>
                <h4 style='text-align:center;padding-top:10px;font-family:"Raleway", sans-serif;font-weight:bold;color:#2b2b2b;'>SCALE UP</h4>
                <p style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>Learn how to scale your online business and increase your customers&#8217; lifetime value.</p>
              </div>
              </a></li>
            <li><a class="feature-block-link" href="/dashboard/training" style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>
              <div> <span class="feature-block-4-img-container" style="background-color: #F4D230;"><img class="feature-block-4" src="/images/303-alt.png" /></span>
                <h4 style='text-align:center;padding-top:10px;font-family:"Raleway", sans-serif;font-weight:bold;color:#2b2b2b;'>TRAINING</h4>
                <p style='font-family:"Ubuntu", sans-serif;font-style:normal;font-weight:normal;color:#6e6e6e;'>Learn the fundamental traffic strategies and what&#8217;s working now by the leading marketers.</p>
              </div>
              </a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<div style='background:#f7f7f7;padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable section" id="le_body_row_4">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_4_col_1">
      <div class="element-container cf" id="le_body_row_4_col_1_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Welcome</span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_4_col_1_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'><span style="font-weight: 400;">Message From Founder</span></h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_4_col_1_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>How do you go from startup to profits and beyond, in just 90 days? As you're about to find out in this video, it all starts with the "five essential elements" of online success... the 5 components that make up a digital business that can be <em>massively leveraged</em> for instant profit growth.</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_4_col_100"></div>
      <div class="split-half column cols subcol" id="le_body_row_4_col_100">
        <div class="element-container cf" id="le_body_row_4_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
    <div class="one-half column cols" id="le_body_row_4_col_2">
      <div class="split-half column cols subcol" id="le_body_row_4_col_100"></div>
      <div class="split-half column cols subcol" id="le_body_row_4_col_100"></div>
      <div class="clearcol"></div>
      <div class="element-container cf" id="le_body_row_4_col_2_el_1">
        <div class="element">
          <div class="op-custom-html-block">
            <center>
              <div class="wistia_responsive_padding" style="padding:56.25% 0 0 0;position:relative;">
                <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                  <iframe src="//fast.wistia.net/embed/iframe/x1n3z6pi1p?videoFoam=true" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen="" mozallowfullscreen="" webkitallowfullscreen="" oallowfullscreen="" msallowfullscreen="" width="100%" height="100%"></iframe>
                </div>
              </div>
              <script src="//fast.wistia.net/assets/external/E-v1.js" async></script>
            </center>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div style='padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable section" id="le_body_row_5">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_5_col_1">
      <div class="element-container cf" id="le_body_row_5_col_1_el_1">
        <div class="element">
          <div class="image-caption" style='width:400px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;'><img alt="" src="/images/imac_1486x1064-11-1024x733.png" border="0" class="full-width" /></div>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_5_col_1_el_2">
        <div class="element"> </div>
      </div>
    </div>
    <div class="one-half column cols" id="le_body_row_5_col_2">
      <div class="element-container cf" id="le_body_row_5_col_2_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Start Up<br />
            </span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_5_col_2_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>Learn About Our Start Up Course</h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_5_col_2_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>A foundational "business basics to advanced mastery" course that provides shortcuts, tools and resources to fast track your digital business' success, focused heavily on tactics and strategy around sustaining the first 3 months of your business.</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_5_col_100">
        <div class="element-container cf" id="le_body_row_5_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_5_col_101">
        <div class="element-container cf" id="le_body_row_5_col_101_el_1">
          <div class="element">
            <div style="text-align:left">
              <style type="text/css">
#btn_1_95281a62df6a9b649c5e1bb62ec7ad74 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_95281a62df6a9b649c5e1bb62ec7ad74 {width:100%;padding:20px 0;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#51A7FA;box-shadow:none;}
</style>
              <a href="/dashboard/start-up" id="btn_1_95281a62df6a9b649c5e1bb62ec7ad74" class="css-button style-1"><span class="text">WATCH VIDEO</span><span class="hover"></span><span class="active"></span></a></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
  </div>
</div>
<div style='background:#f7f7f7;padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable section" id="le_body_row_6">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_6_col_1">
      <div class="element-container cf" id="le_body_row_6_col_1_el_1">
        <div class="element">
          <div class="image-caption" style='width:400px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;'><img alt="" src="/images/imac_1486x1064-14-1024x733.png" border="0" class="full-width" /></div>
        </div>
      </div>
    </div>
    <div class="one-half column cols" id="le_body_row_6_col_2">
      <div class="element-container cf" id="le_body_row_6_col_2_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Set Up<br />
            </span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_6_col_2_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>Learn About Our Set Up Steps</h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_6_col_2_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>A powerful business marketing mastery course that provides basic and advanced shortcuts, traffic, tools and resources to fast track your digital business’s success and prepare your  mind and plan your route.</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_6_col_100">
        <div class="element-container cf" id="le_body_row_6_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_6_col_101">
        <div class="element-container cf" id="le_body_row_6_col_101_el_1">
          <div class="element">
            <div style="text-align:left">
              <style type="text/css">
#btn_1_86bd8e1b712d408d4936ab255f38cef2 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_86bd8e1b712d408d4936ab255f38cef2 {width:100%;padding:20px 0;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#51A7FA;box-shadow:none;}
</style>
              <a href="/dashboard/setup" id="btn_1_86bd8e1b712d408d4936ab255f38cef2" class="css-button style-1"><span class="text">WATCH VIDEO</span><span class="hover"></span><span class="active"></span></a></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
  </div>
</div>
<div style='padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable" id="le_body_row_7">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_7_col_1">
      <div class="element-container cf" id="le_body_row_7_col_1_el_1">
        <div class="element">
          <div class="image-caption" style='width:400px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;'><img alt="" src="/images/imac_1486x1064-17-1024x733.png" border="0" class="full-width" /></div>
        </div>
      </div>
    </div>
    <div class="one-half column cols" id="le_body_row_7_col_2">
      <div class="element-container cf" id="le_body_row_7_col_2_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Scale Up<br />
            </span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_7_col_2_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>Learn About Our Scale Up Steps</h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_7_col_2_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>A 3-Day all inclusive retreat for two that brings the world’s leading thought leaders in business success, and leadership directly to you to learn from, focussing on branding, positioning, conversions and leveraging 3rd party help...</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_7_col_100">
        <div class="element-container cf" id="le_body_row_7_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_7_col_101">
        <div class="element-container cf" id="le_body_row_7_col_101_el_1">
          <div class="element">
            <div style="text-align:left">
              <style type="text/css">
#btn_1_716f443dff247d5c404baf46a0f7b3a6 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_716f443dff247d5c404baf46a0f7b3a6 {width:100%;padding:20px 0;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#51A7FA;box-shadow:none;}
</style>
              <a href="/dashboard/scale" id="btn_1_716f443dff247d5c404baf46a0f7b3a6" class="css-button style-1"><span class="text">WATCH VIDEO</span><span class="hover"></span><span class="active"></span></a></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
  </div>
</div>
<div style='background:#f7f7f7;padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable section" id="le_body_row_8">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_8_col_1">
      <div class="element-container cf" id="le_body_row_8_col_1_el_1">
        <div class="element">
          <div class="image-caption" style='width:450px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;'><img alt="" src="/images/9c-1024x583.png" border="0" class="full-width" /></div>
        </div>
      </div>
    </div>
    <div class="one-half column cols" id="le_body_row_8_col_2">
      <div class="element-container cf" id="le_body_row_8_col_2_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Products<br />
            </span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_8_col_2_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>Learn About Our Products</h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_8_col_2_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>A 5-Day all inclusive retreat for two that brings the world’s top thought leaders in business success, management, and leadership directly to you to learn from, you've got your business off the ground, now you'll learn how to take it to the next level.</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_8_col_100">
        <div class="element-container cf" id="le_body_row_8_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_8_col_101">
        <div class="element-container cf" id="le_body_row_8_col_101_el_1">
          <div class="element">
            <div style="text-align:left">
              <style type="text/css">
#btn_1_c581ab928f6aeb161212d7a0116623bd .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_c581ab928f6aeb161212d7a0116623bd {width:100%;padding:20px 0;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#51A7FA;box-shadow:none;}
</style>
              <a href="/products" id="btn_1_c581ab928f6aeb161212d7a0116623bd" class="css-button style-1"><span class="text">WATCH VIDEO</span><span class="hover"></span><span class="active"></span></a></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
  </div>
</div>
<div style='padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable" id="le_body_row_9">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_9_col_1">
      <div class="element-container cf" id="le_body_row_9_col_1_el_1">
        <div class="element">
          <div class="image-caption" style='width:400px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;'><img alt="" src="/images/imac_1486x1064-9-1024x733.png" border="0" class="full-width" /></div>
        </div>
      </div>
    </div>
    <div class="one-half column cols" id="le_body_row_9_col_2">
      <div class="element-container cf" id="le_body_row_9_col_2_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Trainings<br />
            </span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_9_col_2_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>Learn About Our Trainings</h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_9_col_2_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>A 7 day all-inclusive retreat for two that brings the world’s leading experts in wealth building, investing, real estate, and asset management to raise your financial thermostat and teach you to leave a lasting legacy.</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_9_col_100">
        <div class="element-container cf" id="le_body_row_9_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_9_col_101">
        <div class="element-container cf" id="le_body_row_9_col_101_el_1">
          <div class="element">
            <div style="text-align:left">
              <style type="text/css">
#btn_1_21edf1710370c535bf84c49db3f70ec2 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_21edf1710370c535bf84c49db3f70ec2 {width:100%;padding:20px 0;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#51A7FA;box-shadow:none;}
</style>
              <a href="/dashboard/training" id="btn_1_21edf1710370c535bf84c49db3f70ec2" class="css-button style-1"><span class="text">WATCH VIDEO</span><span class="hover"></span><span class="active"></span></a></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
  </div>
</div>
<div style='background:#f7f7f7;padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable section" id="le_body_row_10">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_10_col_1">
      <div class="element-container cf" id="le_body_row_10_col_1_el_1">
        <div class="element">
          <div class="image-caption" style='width:300px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;'><img alt="" src="/images/Snap-surveys-solutions.png" border="0" class="full-width" /></div>
        </div>
      </div>
    </div>
    <div class="one-half column cols" id="le_body_row_10_col_2">
      <div class="element-container cf" id="le_body_row_10_col_2_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Solutions<br />
            </span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_10_col_2_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>Learn About Our Solutions</h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_10_col_2_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>A 5-Day all inclusive retreat for two that brings the world’s top thought leaders in business success, management, and leadership directly to you to learn from, you've got your business off the ground, now you'll learn how to take it to the next level.</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_10_col_100">
        <div class="element-container cf" id="le_body_row_10_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_10_col_101">
        <div class="element-container cf" id="le_body_row_10_col_101_el_1">
          <div class="element">
            <div style="text-align:left">
              <style type="text/css">
#btn_1_e9901f3d540177bc0c00457ba58d2ebd .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_e9901f3d540177bc0c00457ba58d2ebd {width:100%;padding:20px 0;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#51A7FA;box-shadow:none;}
</style>
              <a href="#<?php // /dashboard/solutions ?>" id="btn_1_e9901f3d540177bc0c00457ba58d2ebd" class="css-button style-1"><span class="text"><font color=#CCC>WATCH VIDEO <i class='fa fa-lock'></i></font></span><span class="hover"></span><span class="active"></span></a></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
  </div>
</div>
<div style='padding-top:100px;padding-bottom:100px;'  class="row two-columns cf ui-sortable" id="le_body_row_11">
  <div class="fixed-width">
    <div class="one-half column cols" id="le_body_row_11_col_1">
      <div class="element-container cf" id="le_body_row_11_col_1_el_1">
        <div class="element">
          <div class="image-caption" style='width:400px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;'><img alt="" src="/images/macbookair_880x500-1.png" border="0" class="full-width" /></div>
        </div>
      </div>
    </div>
    <div class="one-half column cols" id="le_body_row_11_col_2">
      <div class="element-container cf" id="le_body_row_11_col_2_el_1">
        <div class="element">
          <h6 style='font-size:45px;font-style:normal;font-weight:300;color:#C72508;text-align:left;'><span style="font-family: da-font;">Community<br />
            </span></h6>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_11_col_2_el_2">
        <div class="element">
          <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>Learn About Our Community</h2>
        </div>
      </div>
      <div class="element-container cf" id="le_body_row_11_col_2_el_3">
        <div class="element">
          <div class="op-text-block" style="width:100%;text-align: left;">
            <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>You'll get access to The ASPIRE private members only Facebook group where we share ideas, feedback, what's working now. The ultimate community for new &amp; aspiring digital entrepreneurs.</p>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_11_col_100">
        <div class="element-container cf" id="le_body_row_11_col_100_el_1">
          <div class="element">
            <div class="arrow-center"><img src="/images/arrow-7-1.png" class="arrows" alt="arrow" /></div>
          </div>
        </div>
      </div>
      <div class="split-half column cols subcol" id="le_body_row_11_col_101">
        <div class="element-container cf" id="le_body_row_11_col_101_el_1">
          <div class="element">
            <div style="text-align:left">
              <style type="text/css">
#btn_1_1c813f75d5b2abc9c29dd1c30c306026 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_1c813f75d5b2abc9c29dd1c30c306026 {width:100%;padding:20px 0;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#51A7FA;box-shadow:none;}
</style>
              <a href="/community.php" id="btn_1_1c813f75d5b2abc9c29dd1c30c306026" class="css-button style-1"><span class="text">WATCH VIDEO</span><span class="hover"></span><span class="active"></span></a></div>
          </div>
        </div>
      </div>
      <div class="clearcol"></div>
    </div>
  </div>
</div>
<?php include(INCLUDES_MY."footer.php"); ?>

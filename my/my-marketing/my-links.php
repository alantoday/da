<?php include("../includes_my/header.php"); ?>
    <?php
echo MyWriteMidSection("My Links", "Access Your Affiliate Links", "We teach digital entrepreneurs how to start and grow a profitable business with our unique products and live events.", 
"MY CAMPAIGNS", "/my-marketing/my-campaigns.php", 
"CALL COACH", "/my-coach");
?>
    <?php include("my-marketing_menu.php"); ?>
    <?php echo MyWriteMainSectionTop(20); ?>
    <?php
# Do bulk load of content data, get most recent revision of each
$sections_array['swipe-f1-d01'] = "";
$sections_array['swipe-f1-d02'] = "";
$sections_array['swipe-f1-d03'] = "";
$sections_array['swipe-f1-d04'] = "";
$sections_array['swipe-f1-d05'] = "";
$sections_array['swipe-f1-d06'] = "";
$sections_array['swipe-f1-d07'] = "";
$sections_array['swipe-f1-d08'] = "";
$sections_array['swipe-f1-d09'] = "";
$sections_array['swipe-f1-d10'] = "";
$sections_array['swipe-f1-d11'] = "";
$sections_array['swipe-f1-d12'] = "";
$sections_array['swipe-f1-d13'] = "";
$sections_array['swipe-f1-d14'] = "";
$sections_array['swipe-f1-text'] = "";
$sections_array['swipe-f1-social'] = "";
$sections_array['swipe-f1-headlines'] = "";
$sections_array['swipe-f1-braodcasts'] = "";
$query = "SELECT * 
  			FROM sections 
 			WHERE sid IN (
            	SELECT MAX(sid) 
            	FROM sections 
				WHERE name LIKE 'swipe%'
            	GROUP BY name
            )";
$result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
while ($row = mysqli_fetch_array($result)){        
	$sections_array[$row['name']] = $row['content'];
}
?>
    <script>
    $(function () {
        $("#tabs").tabs();
        $("#tabs-f1").tabs();
	    $("#tabs_swipe").tabs();
	    $("#tabs_swipe_other").tabs();        
    });
</script>
    <style>
.container .one-half.column {
	width:400px;	
}
.container .one-half {
	padding-top:50px;	
}
. tabs-1.2 .tabs_swipe {
	padding-top:30px;
}
</style>
    <div id="tabs" style="width:1000px;">
      <ul>
        <li class="tabs"><a href="#tabs-1">Sales Funnel 1</a></li>
        <li class="tabs"><a href="#tabs-2"><font color=grey>Sales Funnel 2 <i class='fa fa-lock'></i></font></a></li>
        <li class="tabs"><a href="#tabs-3"><font color=grey>Sales Funnel 3 <i class='fa fa-lock'></i></font></a></li>
      </ul>
      <div id="tabs-1">
        <div id="tabs-f1" style="width:1000px;">
          <ul>
            <li class="tabs"><a href="#tabs-1.1">My Links</a></li>
            <li class="tabs"><a href="#tabs-1.2">My Banners</a></li>
            <li class="tabs"><a href="#tabs-1.3">My Email Swipe</a></li>
            <li class="tabs"><a href="#tabs-1.4">My Other Swipe</a></li>
          </ul>
          <div id="tabs-1.1">
            <div class="one-half column cols" id="le_body_row_3_col_1">
              <div class="element-container cf" id="le_body_row_3_col_1_el_1">
                <div class="element">
                  <div class="image-caption" style="width:350px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;"> <img alt="" src="https://s3.amazonaws.com/public.digitalaltitude.co/ecovers/ASPIREcap.png" border="0" class="full-width"> </div>
                </div>
              </div>
            </div>
            <div class="one-half column cols" id="le_body_row_3_col_2">
              <div class="element-container cf" id="le_body_row_3_col_2_el_1">
                <div class="element">
                  <h6 style="font-size:45px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#C72508;text-align:left;">CAPTURE</h6>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_3_col_2_el_2">
                <div class="element">
                  <h2 style="font-size:30px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;">Capture Page Affiliate Link</h2>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_3_col_2_el_3">
                <div class="element">
                  <div class="op-text-block" style="width:100%;text-align: left;">
                    <p style="font-size:15px;font-family:'Ubuntu', sans-serif;text-align: justify;">Use the "80/20" rule of marketing to rapidly increase your leverage leading to massive results.</p>
                  </div>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_3_col_2_el_4">
                <div class="element">
                  <div class="arrow-center"> <img src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/arrows/arrow-9-3.png" class="arrows" alt="arrow"> </div>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_3_col_2_el_5">
                <div class="element">
                  <div class="op-text-block" style="width:100%;text-align: left;">
                    <p><b>Copy and paste the link below</b></p>
                    <p><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><span style="color: blue;">http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=</span></a></p>
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <div class="one-half column cols" id="le_body_row_4_col_1">
              <div class="element-container cf" id="le_body_row_4_col_1_el_1">
                <div class="element">
                  <div class="image-caption" style="width:350px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;"> <img alt="" src="https://s3.amazonaws.com/public.digitalaltitude.co/ecovers/ASPIREsales.png" border="0" class="full-width"> </div>
                </div>
              </div>
            </div>
            <div class="one-half column cols" id="le_body_row_4_col_2">
              <div class="element-container cf" id="le_body_row_4_col_2_el_1">
                <div class="element">
                  <h6 style="font-size:45px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#C72508;text-align:left;">SALES</h6>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_4_col_2_el_2">
                <div class="element">
                  <h2 style="font-size:30px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;">Sales&nbsp;Page Affiliate Link</h2>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_4_col_2_el_3">
                <div class="element">
                  <div class="op-text-block" style="width:100%;text-align: left;">
                    <p style="font-size:15px;font-family:'Ubuntu', sans-serif;text-align: justify;"></p>
                  </div>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_4_col_2_el_4">
                <div class="element">
                  <div class="arrow-center"> <img src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/arrows/arrow-9-3.png" class="arrows" alt="arrow"> </div>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_4_col_2_el_5">
                <div class="element">
                  <div class="op-text-block" style="width:100%;text-align: left;">
                    <p> <b>Copy and paste the link below</b> </p>
                    <p> <a href="http://aspir.link/vsl1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><span style="color: blue;">http://aspir.link/vsl1/?da=<?php echo $_SESSION['username']; ?>&amp;t=</span> </a> </p>
                  </div>
                </div>
              </div>
            </div>
            <hr>
            <div class="one-half column cols" id="le_body_row_5_col_1">
              <div class="element-container cf" id="le_body_row_5_col_1_el_1">
                <div class="element">
                  <div class="image-caption" style="width:350px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;"> 
                  <img alt="" src="https://s3.amazonaws.com/public.digitalaltitude.co/ecovers/ASPIREstep1.png" border="0" class="full-width">
                  </div>
                </div>
              </div>
            </div>
            <div class="one-half column cols" id="le_body_row_5_col_2">
              <div class="element-container cf" id="le_body_row_5_col_2_el_1">
                <div class="element">
                  <h6 style="font-size:45px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#C72508;text-align:left;">STEP 1</h6>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_5_col_2_el_2">
                <div class="element">
                  <h2 style="font-size:30px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;">Step 1 Page Affiliate Link</h2>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_5_col_2_el_3">
                <div class="element">
                  <div class="op-text-block" style="width:100%;text-align: left;">
                    <p style="font-size:15px;font-family:'Ubuntu', sans-serif;text-align: justify;"></p>
                  </div>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_5_col_2_el_4">
                <div class="element">
                  <div class="arrow-center"> <img src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/arrows/arrow-9-3.png" class="arrows" alt="arrow"> </div>
                </div>
              </div>
              <div class="element-container cf" id="le_body_row_5_col_2_el_5">
                <div class="element">
                  <div class="op-text-block" style="width:100%;text-align: left;">
                    <p> <b>Copy and paste the link below</b> </p>
                    <p> <a href="https://aspir.link/step1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><span style="color: blue;">https://aspir.link/step1/?da=<?php echo $_SESSION['username']; ?>&amp;t=</span> </a> </p>
                  </div>
                </div>
              </div>
            </div>
          <hr>
          <div class="one-half column cols" id="le_body_row_5_col_1">
            <div class="element-container cf" id="le_body_row_5_col_1_el_1">
              <div class="element">
                <div class="image-caption" style="width:350px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;"> <img alt="" src="https://s3.amazonaws.com/public.digitalaltitude.co/ecovers/ASPIREorder.png" border="0" class="full-width"> </div>
              </div>
            </div>
          </div>
          <div class="one-half column cols" id="le_body_row_5_col_2">
            <div class="element-container cf" id="le_body_row_5_col_2_el_1">
              <div class="element">
                <h6 style="font-size:45px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#C72508;text-align:left;">ORDER</h6>
              </div>
            </div>
            <div class="element-container cf" id="le_body_row_5_col_2_el_2">
              <div class="element">
                <h2 style="font-size:30px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;">Order&nbsp;Page Affiliate Link</h2>
              </div>
            </div>
            <div class="element-container cf" id="le_body_row_5_col_2_el_3">
              <div class="element">
                <div class="op-text-block" style="width:100%;text-align: left;">
                  <p style="font-size:15px;font-family:'Ubuntu', sans-serif;text-align: justify;"></p>
                </div>
              </div>
            </div>
            <div class="element-container cf" id="le_body_row_5_col_2_el_4">
              <div class="element">
                <div class="arrow-center"> <img src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/arrows/arrow-9-3.png" class="arrows" alt="arrow"> </div>
              </div>
            </div>
            <div class="element-container cf" id="le_body_row_5_col_2_el_5">
              <div class="element">
                <div class="op-text-block" style="width:100%;text-align: left;">
                  <p> <b>Copy and paste the link below</b> </p>
                  <p> <a href="https://aspir.link/oft1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><span style="color: blue;">https://aspir.link/oft1/?da=<?php echo $_SESSION['username']; ?>&amp;t=</span> </a> </p>
                </div>
              </div>
            </div>
          </div>
        <hr>
        <div class="one-half column cols" id="le_body_row_5_col_1">
          <div class="element-container cf" id="le_body_row_5_col_1_el_1">
            <div class="element">
              <div class="image-caption" style="width:350px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;"> 
              <img alt="" src="https://s3.amazonaws.com/public.digitalaltitude.co/ecovers/ASPIREthanks.png" border="0" class="full-width">
              </div>
            </div>
          </div>
        </div>
        <div class="one-half column cols" id="le_body_row_5_col_2">
          <div class="element-container cf" id="le_body_row_5_col_2_el_1">
            <div class="element">
              <h6 style="font-size:45px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#C72508;text-align:left;">THANKYOU</h6>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_5_col_2_el_2">
            <div class="element">
              <h2 style="font-size:30px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;">Thankyou Page</h2>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_5_col_2_el_3">
            <div class="element">
              <div class="op-text-block" style="width:100%;text-align: left;">
                <p style="font-size:15px;font-family:'Ubuntu', sans-serif;text-align: justify;"></p>
              </div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_5_col_2_el_4">
            <div class="element">
              <div class="arrow-center"> <!-- <img src="" class="arrows" alt="arrow">--> </div>
            </div>
          </div>
          <div class="element-container cf" id="le_body_row_5_col_2_el_5">
            <div class="element">
              <div class="op-text-block" style="width:100%;text-align: left;"> </div>
            </div>
          </div>
        </div>
      </div>
      
      <div id="tabs-1.2" style="padding-top: 30px;">
        <div id="300x250c" class="banner_p">300 x 250<br>
          <table>
            <tr>
              <td><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/6S6F_300.250.jpg"></td>
              <td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/6S6F_300.250.jpg"></a></textarea></td>
            </tr>
          </table>
        </div>
        <br>
        <div id="720x90c" class="banner_p">720 x 90<br>
          <table>
            <tr>
              <td><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/6S6F_720.90.jpg"></td>
              <td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/6S6F_720.90.jpg"></a></textarea></td>
            </tr>
          </table>
        </div>
        <?php /*      	<script>
      		function switch_banner(){
      			$(".banner_p").hide();
      			$("#" + $("#banner_size").val()).show();
      		}
      	</script>
      	Banner size:
      	<select id="banner_size" onchange="switch_banner()">
      		<option value="120x600c">120 x 600 Coach</option>
      		<option value="160x600c">160 x 600 Coach</option>
      		<option value="300x250c">300 x 250 Coach</option>
      		<option value="250x250c">250 x 250 Coach</option>
      		<option value="728x90c">728 x 90 Coach</option>
      		<option value="120x600s">120 x 600 Stage</option>
      		<option value="225x250s">225 x 250 Stage</option>
      		<option value="300x250s">300 x 250 Stage</option>
      		<option value="728x90s">728 x 90 Stage</option>
      	</select>
        <div id="120x600c" class="banner_p">120 x 600<br>
        	<table>
        		<tr>
        			<td>
        			<img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_120.600.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_120.600.gif"></a></textarea></td>
        		</tr>
        	</table>	
        </div>	
        <div id="160x600c" class="banner_p">160 x 600<br>
        	<table>
        		<tr>
        			<td>
        			<img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_160.600.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_160.600.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	
        </div>
        <div id="300x250c" class="banner_p">300 x 250<br>
        	<table>
        		<tr>
        			<td>
        			<img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_300.250.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_300.250.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        	
        </div>
        <div id="250x250c" class="banner_p">250 x 250<br>
        	<table>
        		<tr>
        			<td>
        			<img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_250.250.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="//s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_250.250.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        			        
        </div>
        <div id="728x90c" class="banner_p">728 x 90<br>
        	<table>
        		<tr>
        			<td>
        			<img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_728.90.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/ES_728.90.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        			
        </div>
        <div id="120x600s" class="banner_p">120 x 600<br>
        	<table>
        		<tr>
        			<td>
        			<img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_120.600.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_120.600.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        			        
        </div>
        <div id="225x250s" class="banner_p">160 x 600<br>
        	<table>
        		<tr>
        			<td>
        			<img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_160.600.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_160.600.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        			
        </div>
        <div id="225x250s" class="banner_p">225 x 250<br>
        	<table>
        		<tr>
        			<td>
        			<img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_250.250.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_250.250.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        			
        </div>
        <div id="300x250s" class="banner_p">300 x 250<br>
        	<table>
        		<tr>
        			<td>
        			<img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_300.250.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_300.250.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        			
        </div>
        <div id="728x90s" class="banner_p">728 x 90<br>
        	<table>
        		<tr>
        			<td>
        			<img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_728.90.gif">
        			</td>
        			<td valign="top" style="vertical-align:top;width:300px"><textarea onclick="this.focus();this.select()" style="width:100%; height:100px;"><a href="http://aspir.link/c1/?da=<?php echo $_SESSION['username']; ?>&amp;t=" target="_blank"><img src="https://s3.amazonaws.com/public.digitalaltitude.co/zanners/10K_728.90.gif"></a></textarea></td>
        		</tr>
        	</table>        	        	        			        
        </div>
*/ ?>
      </div>
      <div id="tabs-1.3" style="padding-top: 30px;">
        <div class="element">
          <div id="tabs_swipe">
            <ul>
              <li><a href="#tabs_swipe-1">D1</a></li>
              <li><a href="#tabs_swipe-2">D2</a></li>
              <li><a href="#tabs_swipe-3">D3</a></li>
              <li><a href="#tabs_swipe-4">D4</a></li>
              <li><a href="#tabs_swipe-5">D5</a></li>
              <li><a href="#tabs_swipe-6">D6</a></li>
              <li><a href="#tabs_swipe-7">D7</a></li>
              <li><a href="#tabs_swipe-8">D8</a></li>
              <li><a href="#tabs_swipe-9">D9</a></li>
              <li><a href="#tabs_swipe-10">D10</a></li>
              <li><a href="#tabs_swipe-11">D11</a></li>
              <li><a href="#tabs_swipe-12">D12</a></li>
              <li><a href="#tabs_swipe-13">D13</a></li>
            </ul>
            <div id="tabs_swipe-1"> <?php echo $sections_array['swipe-f1-d01'] ?> </div>
            <div id="tabs_swipe-2"> <?php echo $sections_array['swipe-f1-d02'] ?> </div>
            <div id="tabs_swipe-3"> <?php echo $sections_array['swipe-f1-d03'] ?> </div>
            <div id="tabs_swipe-4"> <?php echo $sections_array['swipe-f1-d04'] ?> </div>
            <div id="tabs_swipe-5"> <?php echo $sections_array['swipe-f1-d05'] ?> </div>
            <div id="tabs_swipe-6"> <?php echo $sections_array['swipe-f1-d06'] ?> </div>
            <div id="tabs_swipe-7"> <?php echo $sections_array['swipe-f1-d07'] ?> </div>
            <div id="tabs_swipe-8"> <?php echo $sections_array['swipe-f1-d08'] ?> </div>
            <div id="tabs_swipe-9"> <?php echo $sections_array['swipe-f1-d09'] ?> </div>
            <div id="tabs_swipe-10"> <?php echo $sections_array['swipe-f1-d10'] ?> </div>
            <div id="tabs_swipe-11"> <?php echo $sections_array['swipe-f1-d11'] ?> </div>
            <div id="tabs_swipe-12"> <?php echo $sections_array['swipe-f1-d12'] ?> </div>
            <div id="tabs_swipe-13"> <?php echo $sections_array['swipe-f1-d13'] ?> </div>
          </div>
        </div>
      </div>
      <div id="tabs-1.4" style="padding-top: 30px;">
        <div class="element">
          <div id="tabs_swipe_other">
            <ul>
              <li><a href="#tabs_swipe_o-1">Text</a></li>
              <li><a href="#tabs_swipe_o-2">Social</a></li>
            </ul>
            <div id="tabs_swipe_o-1"> <?php echo $sections_array['swipe-f1-text'] ?> </div>
            <div id="tabs_swipe_o-2"> <?php echo $sections_array['swipe-f1-social'] ?> </div>
          </div>
        </div>
      </div>
    </div>
    <!--tab1funnel--> 
  </div>
  <!--tab1-->
  <div id="tabs-2">
    <h2 style="font-size:30px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;">Locked</h2>
  </div>
  <div id="tabs-3">
    <h2 style="font-size:30px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;">Locked</h2>
  </div>
</div>
<script type="text/javascript">
$('#tabs').css('opacity', 0);
$(window).load(function() {
  $('#tabs').css('opacity', 1);
});
switch_banner();
</script>
<?php include(INCLUDES_MY . "footer.php"); ?>

<?php 

function MyUpdateSessionRanks($db, $member_id) {
    $query = "SELECT mr.* 
            FROM member_ranks mr
            WHERE member_id='".$member_id."'
            AND mr.end_date IS NULL";
    $result = mysqli_query($db, $query) or die(mysqli_error($db).". $query");
    $_SESSION['asp'] = false;
    $_SESSION['bas'] = false;
    $_SESSION['ris'] = false;
    $_SESSION['asc'] = false;
    $_SESSION['pea'] = false;
    $_SESSION['ape'] = false;
    $_SESSION['pro'] = false; // "pro" is being base or above.
    $basic_member = 1;  // "Basic member if they don't have anything higher than asp
    while ($row = mysqli_fetch_array($result)){        
        $_SESSION[substr($row['product_type'],0,3)] = true;
        if ($row['product_type']<>"asp") {
            $_SESSION['pro'] = true;
        }
    }
}

function MyValidateAccess($product,$redirect="/dashboard") {
    if ($_SESSION[substr($product,0,3)] && !isset($_GET["no_$product"])) {
        return true;
    } else {
        header("location:$redirect");
        exit;
    }
}

function MyWriteBigButton ($text, $type, $link="") {
	global $menu_color;
	$button_color = isset($menu_color['top'][$type]) ? $menu_color['top'][$type] : $menu_color['top']['default'];

    if ($link=="") {
        $href = 'onclick="alert(\'Coming soon.\');" href="javascript:void(0)"';
    } else {
        $href = 'href="'.$link.'"';
    }
	return <<<EOF
		<div class="split-half column cols subcol" id="le_body_row_1_col_100">
			<div class="element-container cf" id="le_body_row_1_col_100_el_1">
				<div class="element">
					<div style="text-align:left">
						<a $href style="font-size:22px;color:#ffffff;font-family:Ubuntu;font-weight:normal;width:100%;padding:20px 0;background:$button_color;" class="css-button style-1">
							<span class="text">$text</span>
							<span class="hover"></span>
							<span class="active"></span></a>
					</div>
				</div>
			</div>
		</div>
EOF;
}

function MyWriteFeedbackButton () {
    return <<<EOF
</div></div>
<div style="margin-top:50px;padding-bottom:50px;" class="row one-column cf ui-sortable" id="le_body_row_4">
	<div class="fixed-width">
		<div class="one-column column cols" id="le_body_row_4_col_1"><div class="element-container cf" id="le_body_row_4_col_1_el_1">
			<div class="element"> 
				<style type="text/css">#btn_1_e7841850dfacab76419ddc2dc315344d .text {font-size:30px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_e7841850dfacab76419ddc2dc315344d .subtext {font-size:20px;color:#ffffff;font-weight:normal;}#btn_1_e7841850dfacab76419ddc2dc315344d {width:100%;padding:30px 0;background:#42bd1f;box-shadow:none;}</style>
				<a href="http://www.surveygizmo.com/s3/2443569/How-Can-We-Do-Better" target="_blank" id="btn_1_e7841850dfacab76419ddc2dc315344d" class="css-button style-1">
				<span class="text">How Can We Do Better With This Lesson?</span><span class="subtext">Click Here To Answer Anonymously And Honestly...</span><span class="hover"></span><span class="active"></span></a>
			</div>
		</div>
	</div>
</div>
EOF;
}			 
			 
function MyWriteTrailClosed ($product, $b1_txt = 'UPGRADE', $b1_link="", $b2_txt="DASHBOARD", $b2_link="/dashboard", $product) {
    if ($link=="") {
        $href = 'onclick="alert(\'Coming soon.\');" href="javascript:void(0)"';
    } else {
        $href = 'href="'.$b1_link.'"';
    }
    return <<<EOF
    <div style="background-image:url(http://www.members.digitalaltitude.co/wp-content/uploads/2015/09/shutterstock_273662216-e1443584521337.jpg);background-repeat:no-repeat;background-size:cover;padding-top:100px;padding-bottom:175px;border-top-width:px;border-top-style:solid;border-top-color:;border-bottom-width:px;border-bottom-style:solid;border-bottom-color:;" class="row five-columns cf ui-sortable section" id="le_body_row_1">
			<div class="fixed-width"><div class="three-fifths column cols" id="le_body_row_1_col_1"><div class="element-container cf" id="le_body_row_1_col_1_el_1"><div class="element"> 
	<div class="image-caption" style="width:523px;margin-top:0px;margin-bottom:px;margin-right:px;margin-left:px;float: left;"><img alt="" src="http://www.members.digitalaltitude.co/wp-content/uploads/2015/09/TRAIL-CLOSED-WHT.png" border="0" class="full-width"></div>
 </div></div><div class="element-container cf" id="le_body_row_1_col_1_el_2"><div class="element"> <h2 style="font-size:40px;font-family:&quot;Shadows Into Light&quot;, sans-serif;font-style:normal;font-weight:normal;color:#ffffff;text-align:left;">Upgrade To Access This&nbsp;Page</h2> </div></div><div class="element-container cf" id="le_body_row_1_col_1_el_3"><div class="element"> <h4 style="font-size:20px;font-family:&quot;Raleway&quot;, sans-serif;font-style:normal;font-weight:300;color:#ffffff;text-align:left;line-height:28px;margin-bottom:30px;">To access this content or page you need to upgrade to the next product. Good news is you increase your commissions and learn alot more!</h4> </div></div><div class="split-half column cols subcol" id="le_body_row_1_col_100"><div class="element-container cf" id="le_body_row_1_col_100_el_1"><div class="element"><div style="text-align:left"><style type="text/css">#btn_1_cd5951aa830374684008938e4e7e2c60 .text {font-size:22px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_cd5951aa830374684008938e4e7e2c60 {padding:20px 51px;background:#C72508;box-shadow:none;}</style>
	 <a $href id="btn_1_cd5951aa830374684008938e4e7e2c60" class="css-button style-1"><span class="text">$b1_txt</span><span class="hover"></span><span class="active"></span></a></div></div></div></div><div class="split-half column cols subcol" id="le_body_row_1_col_101"><div class="element-container cf" id="le_body_row_1_col_101_el_1"><div class="element"><div style="text-align:left"><style type="text/css">#btn_1_2d4c6e1f03b48f37516074f53742b733 .text {font-size:22px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_2d4c6e1f03b48f37516074f53742b733 {padding:20px 50px;border-color:#ffffff;border-width:3px;}</style>
		 <a href="$b2_link" id="btn_1_2d4c6e1f03b48f37516074f53742b733" class="css-button style-1"><span class="text">$b2_txt</span><span class="hover"></span><span class="active"></span></a></div></div></div></div><div class="clearcol"></div></div><div class="two-fifths column cols narrow" id="le_body_row_1_col_2"></div></div></div>
EOF;
}

function MyWriteActiveStep($step_label, $step_link, $padding_left = 24, $step = 1, $step_unlocked = 99, $steps_completed = 99) {
	if ($step == "locked" || $step > $step_unlocked) {
		$step_label = "<font color='#CCC'>$step_label <i class='fa fa-lock'></i></font>";
		$step_link = "#";
	}
	$current_page = "";
	$active_color = preg_match("/apex/",$step_link) ? "#FFF" : "rgb(48, 48, 48)";
    if (str_replace("/index.php","",$_SERVER['PHP_SELF']) == $step_link) {
	    return "<a href='$step_link' style='padding-left:{$padding_left}px'><span style='color:$active_color'><b>$step_label</b></span></a>";
    } else {
	    return "<a href='$step_link' style='padding-left:{$padding_left}px;' class='sub_menu'>$step_label</a>";	
	}
}

function MyWriteTTT() {
    return <<<EOF
<div style="padding-top:25px;" class="row three-columns cf ui-sortable section" id="le_body_row_3">
		<div class="fixed-width"><div class="one-third column cols narrow" id="le_body_row_3_col_1"><div class="element-container cf" id="le_body_row_3_col_1_el_1"><div class="element"> <h6 style="font-size:35px;font-style:normal;font-weight:300;color:#51a7fa;text-align:center;">TRAINING</h6> </div></div><div class="element-container cf" id="le_body_row_3_col_1_el_2"><div class="element"> <div style="height:25px"></div> </div></div><div class="element-container cf" id="le_body_row_3_col_1_el_3"><div class="element"> <ul class="feature-block feature-block-style-4 feature-block-one-col cf">
	<li><div>
				<span class="feature-block-4-img-container" style="background-color: #C72508;"><img class="feature-block-4" src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/feature_block/icons/151-alt.png"></span>
			</div></li>
 </ul> </div></div></div><div class="one-third column cols narrow" id="le_body_row_3_col_2"><div class="element-container cf" id="le_body_row_3_col_2_el_1"><div class="element"> <h6 style="font-size:35px;font-style:normal;font-weight:300;color:#51a7fa;text-align:center;">TOOLS</h6> </div></div><div class="element-container cf" id="le_body_row_3_col_2_el_2"><div class="element"> <div style="height:25px"></div> </div></div><div class="element-container cf" id="le_body_row_3_col_2_el_3"><div class="element"> <ul class="feature-block feature-block-style-4 feature-block-one-col cf">
	<li><div>
				<span class="feature-block-4-img-container" style="background-color: #51a7fa;"><img class="feature-block-4" src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/feature_block/icons/258-alt.png"></span>
			</div></li>
 </ul> </div></div></div><div class="one-third column cols narrow" id="le_body_row_3_col_3"><div class="element-container cf" id="le_body_row_3_col_3_el_1"><div class="element"> <h6 style="font-size:35px;font-style:normal;font-weight:300;color:#51a7fa;text-align:center;">RESOURCES</h6> </div></div><div class="element-container cf" id="le_body_row_3_col_3_el_2"><div class="element"> <div style="height:25px"></div> </div></div><div class="element-container cf" id="le_body_row_3_col_3_el_3"><div class="element"> <ul class="feature-block feature-block-style-4 feature-block-one-col cf">
	<li><div>
				<span class="feature-block-4-img-container" style="background-color: #6EBE44;"><img class="feature-block-4" src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/feature_block/icons/154-alt.png"></span>
			</div></li>
 </ul> </div></div></div></div></div>
EOF;
}
function MyWriteTwoButtons($b1_txt, $b1_link, $b2_txt, $b2_link, $product="default", $btn1_access = true, $btn2_access = true) {
        global $menu_color, $product;
        $button_color = isset($menu_color['top'][$product]) ? $menu_color['top'][$product] : $menu_color['top']['default'];
        if(!$btn1_access){
            $b1_link = "#";
            $b1_disable = "style='background:#aaa'";
        }else{
            $b1_disable = "";
        }
        if(!$btn2_access){
            $b2_link = "#";
            $b2_disable = "style='background:#aaa'";
        }else{
            $b2_disable = "";
        }
	return <<<EOF
<div style="padding-top:50px;padding-bottom:75px;" class="row two-columns cf ui-sortable" id="le_body_row_3">
	<div class="fixed-width"><div class="one-half column cols" id="le_body_row_3_col_1">
            <div class="element-container cf" id="le_body_row_3_col_1_el_1">
                <div class="element"> 
                    <div style="text-align:right">
                        <style type="text/css">#btn_1_0ee12559a41674b1a8c909eb9de76472 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_0ee12559a41674b1a8c909eb9de76472 {padding:20px 20px;background:#51A7F9;box-shadow:none;}</style>
                        <a href="$b1_link" id="btn_1_0ee12559a41674b1a8c909eb9de76472" class="css-button style-1" $b1_disable><span class="text">$b1_txt</span><span class="hover"></span><span class="active"></span></a>
                    </div> 
                </div>
            </div>
        </div>
        <div class="one-half column cols" id="le_body_row_3_col_2">
            <div class="element-container cf" id="le_body_row_3_col_2_el_1">
                <div class="element"> 
                    <div style="text-align:left">
                        <style type="text/css">#btn_1_b2cd523c21c441c6967d73a451946802 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_b2cd523c21c441c6967d73a451946802 {padding:20px 20px;background:$button_color;box-shadow:none;}</style>
			<a href="$b2_link" id="btn_1_b2cd523c21c441c6967d73a451946802" class="css-button style-1" $b2_disable><span class="text">$b2_txt</span><span class="hover"></span><span class="active"></span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>	
EOF;
}
function MyWriteOneButtons($b1_txt, $b1_link, $product="default") {
        global $menu_color;
        $button_color = isset($menu_color['top'][$product]) ? $menu_color['top'][$product] : $menu_color['top']['default'];
	return <<<EOF
                    <style type="text/css">#btn_1_0ee12559a41674b1a8c909eb9de76472 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_0ee12559a41674b1a8c909eb9de76472 {padding:20px 20px;background:#51A7F9;box-shadow:none;}</style>
                    <center><div style="padding:20px;"><a href="$b1_link" id="btn_1_0ee12559a41674b1a8c909eb9de76472" class="css-button style-1"><span class="text">$b1_txt</span><span class="hover"></span><span class="active"></span></a></div></center>
    
EOF;
}
function MyWriteModuleComplete($text, $link) {
	return <<<EOF
<div class="row one-column cf ui-sortable" id="le_body_row_3">
	<div class="fixed-width"><div class="one-column column cols" id="le_body_row_3_col_1">
		<div class="element-container cf" id="le_body_row_3_col_1_el_1">
			<div class="element"> 
				<h2 style="font-size:32px;font-family:'Raleway', sans-serif;font-style:normal;font-weight:normal;color:#C72508;text-align:center;">Module Complete! Great Work!</h2> 
				</div>
			</div>
			<div class="element-container cf" id="le_body_row_3_col_1_el_2"><div class="element"> 
				<div style="height:5px"></div> 
			</div>
		</div>
		<div class="element-container cf" id="le_body_row_3_col_1_el_3">
			<div class="element"> 
				<div style="text-align:center">
					<style type="text/css">#btn_1_bfad5e1e1b3fed1cfe18efa0b71cd5d6 .text {font-size:25px;color:#ffffff;font-family:Raleway;font-weight:normal;}#btn_1_bfad5e1e1b3fed1cfe18efa0b71cd5d6 {padding:20px 35px;background:#C72508;box-shadow:none;}</style>
					<a href="$link" id="btn_1_bfad5e1e1b3fed1cfe18efa0b71cd5d6" class="css-button style-1"><span class="text">$text</span><span class="hover"></span><span class="active"></span></a>
				</div> 
</div></div></div></div></div>
EOF;
}

function MyWriteMidSection($h1_txt, $h2_txt, $h3_txt, $b1_txt, $b1_link, $b2_txt, $b2_link, $right_h1_txt="", $right_h2_txt="") {
	global $menu_color, $product;
	$h1_color = (isset($product) && $product<>"default" && isset($menu_color['top'][$product])) ? $menu_color['top'][$product] : "#FFF";

	$target_1 = (preg_match("/^http/",$b1_link)) ? "target='_blank'" : '';
	$target_2 = (preg_match("/^http/",$b2_link)) ? "target='_blank'" : '';
	if (strlen($h1_txt) > 18) {
		$h1_font_size = "48px";
	} elseif (strlen($h1_txt) > 14) {
		$h1_font_size = "54px";
	} else {
		$h1_font_size = "60px";
	}
	return <<<EOF
		<div style='background-image:url(/images/middle_bg.jpg); background-repeat:no-repeat; background-size:cover; height:345px;' class="row five-columns cf ui-sortable section">
			<div class="fixed-width">
				<div class="four-fifths column cols" id="le_body_row_1_col_1">
					<div class="element-container cf" id="le_body_row_1_col_1_el_1">
						<div class="element"> 
		<span style="font-size:$h1_font_size;font-weight:300px;line-height:66px;font-family:da-font;color:$h1_color">
		$h1_txt</span>
						</div>
					</div>
				</div>
				<div class="three-fifths column cols" id="le_body_row_1_col_1">
					<div class="element-container cf" id="le_body_row_1_col_1_el_2">
						<div class="element"> 
		<h2 style='font-size:40px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>
		$h2_txt</h2> 
						</div>
					</div>
					<div class="element-container cf" id="le_body_row_1_col_1_el_3">
						<div class="element"> 
		
		<h4 style='font-size:20px;font-family:"Raleway", sans-serif;font-style:normal;font-weight:300;color:#303030;text-align:left;line-height:28px;margin-bottom:30px;'>
		$h3_txt</h4> 
						</div>
					</div>
					<div class="split-half column cols subcol" id="le_body_row_1_col_100">
						<div class="element-container cf" id="le_body_row_1_col_100_el_1">
							<div class="element">
								<div style="text-align:left">
									<style type="text/css">#btn_1_6ffd752c0f486e0e6a69ba5f744e3209 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_6ffd752c0f486e0e6a69ba5f744e3209 {width:100%;padding:20px 0;background:#C82506;box-shadow:none;}</style>
		
		<a href="$b1_link" $target_1 id="btn_1_6ffd752c0f486e0e6a69ba5f744e3209" class="css-button style-1"><span class="text">
		$b1_txt</span><span class="hover"></span><span class="active"></span></a>
								</div>
							</div>
						</div>
					</div>
					<div class="split-half column cols subcol" id="le_body_row_1_col_101">
						<div class="element-container cf" id="le_body_row_1_col_101_el_1">
							<div class="element">
								<div style="text-align:left">
									<style type="text/css">#btn_1_26d45ae13ce9b32d54f2881583e419e5 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_26d45ae13ce9b32d54f2881583e419e5 {padding:20px 30px;border-color:#ffffff;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#303030;box-shadow:none;}</style>
		<a href="$b2_link" $target_2 id="btn_1_26d45ae13ce9b32d54f2881583e419e5" class="css-button style-1"><span class="text">
		$b2_txt</span><span class="hover"></span><span class="active"></span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="two-fifths column cols narrow" id="le_body_row_1_col_2"><div class="element-container cf" id="le_body_row_1_col_2_el_1"><div class="element"> 
				<h6 style="font-size:66px;font-style:normal;font-weight:300;color:#ffffff;text-align:left;"><span style="font-family: da-font;">
				$right_h1_txt</span></h6> </div></div><div class="element-container cf" id="le_body_row_1_col_2_el_2"><div class="element"> 
				<h2 style="font-size:40px;font-family:&quot;Shadows Into Light&quot;, sans-serif;font-style:normal;font-weight:normal;color:#ffffff;letter-spacing:-1px;text-align:left;">
				$right_h2_txt</h2> </div></div></div>
		</div>
</div>
EOF;
}

function MyWriteMidSectionRise($h1_txt, $h2_txt, $h3_txt, $b1_txt, $b1_link) {
	$target_1 = (preg_match("/^http/",$b1_link)) ? "target='_blank'" : '';
	$target_2 = (preg_match("/^http/",$b2_link)) ? "target='_blank'" : '';
	return <<<EOF
		<div style='background-image:url(/images/middle_bg.jpg); background-repeat:no-repeat; background-size:cover; padding-top:75px; padding-bottom:75px' class="row five-columns cf ui-sortable section">
			<div class="fixed-width">
				<div class="three-fifths column cols" id="le_body_row_1_col_1">
					<div class="element-container cf" id="le_body_row_1_col_1_el_1">
						<div class="element"> 
								
		<h6 style="font-size:55px;font-family:'Shadows Into Light', sans-serif;font-style:normal;font-weight:300;color:#fff;text-align:left;">
                    <span style="font-family: da-font;">$h1_text</span></h6> 
						</div>
					</div>
					<div class="element-container cf" id="le_body_row_1_col_1_el_2">
						<div class="element"> 
		<h2 style='font-size:40px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>
		$h2_txt</h2> 
						</div>
					</div>
					<div class="element-container cf" id="le_body_row_1_col_1_el_3">
						<div class="element"> 
		<h4 style='font-size:20px;font-family:"Raleway", sans-serif;font-style:normal;font-weight:300;color:#303030;text-align:left;line-height:28px;margin-bottom:30px;'>
		$h3_txt</h4> 
						</div>
					</div>
					<div class="split-half column cols subcol" id="le_body_row_1_col_100">
						<div class="element-container cf" id="le_body_row_1_col_100_el_1">
							<div class="element">
								<div style="text-align:left">
									<style type="text/css">#btn_1_6ffd752c0f486e0e6a69ba5f744e3209 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_6ffd752c0f486e0e6a69ba5f744e3209 {width:100%;padding:20px 0;background:#C82506;box-shadow:none;}</style>
		<a href="$b1_link" $target_1 id="btn_1_6ffd752c0f486e0e6a69ba5f744e3209" class="css-button style-1"><span class="text">
		$b1_txt</span><span class="hover"></span><span class="active"></span></a>
								</div>
							</div>
						</div>
					</div>
					<div class="split-half column cols subcol" id="le_body_row_1_col_101">
						<div class="element-container cf" id="le_body_row_1_col_101_el_1">
							<div class="element">
								<div style="text-align:left">
									<style type="text/css">#btn_1_26d45ae13ce9b32d54f2881583e419e5 .text {font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;}#btn_1_26d45ae13ce9b32d54f2881583e419e5 {padding:20px 30px;border-color:#ffffff;border-width:0px;-moz-border-radius:0px;-webkit-border-radius:0px;border-radius:0px;background:#303030;box-shadow:none;}</style>
		<a href="$b2_link" $target_2 id="btn_1_26d45ae13ce9b32d54f2881583e419e5" class="css-button style-1"><span class="text">
		$b2_txt</span><span class="hover"></span><span class="active"></span></a>
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
</div>
EOF;
}

function MyWriteMidSectionVideo($video_code, $locked_msg = "") {
	global $product;
	if ($product == "base") {
		$middle_img = "middle_bg_base.jpg";
	} elseif ($product == "rise") {
		$middle_img = "middle_bg_rise.jpg";
	} else {
		$middle_img = "middle_bg_sm.jpg";		
	}
	if(!$locked_msg){	
		if ($video_code == "XXX") {
			return <<<EOF
	<div style="background-image:url(/images/$middle_img);background-repeat:no-repeat;background-size:cover;height:345px;" class="row one-column cf ui-sortable section" id="le_body_row_1">
				<div class="fixed-width"><div class="one-column column cols" id="le_body_row_1_col_1"><div class="element-container cf" id="le_body_row_1_col_1_el_1"><div class="element"> 
		<div class="image-caption" style="width:501px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;">
		<br><h6 align=center style="font-size:48px">Bonus Content Coming Soon</h6></div>
	 </div></div></div></div></div>		
EOF;
		} else {
		return <<<EOF
	<div style='background-image:url(/images/$middle_img); background-repeat:no-repeat; background-size:cover; height:395px;' class="row five-columns cf ui-sortable section">
		<div class="fixed-width">
			<center>
			<iframe src="//fast.wistia.net/embed/iframe/$video_code" allowtransparency="true" frameborder="0" scrolling="no" class="wistia_embed" name="wistia_embed" allowfullscreen mozallowfullscreen webkitallowfullscreen oallowfullscreen msallowfullscreen width="720" height="405"></iframe>
<script src="//fast.wistia.net/assets/external/E-v1.js" async></script>
			</center>
	 </div></div></div>
EOF;
		}
	} else {
		return <<<EOF
	<div style='background-image:url(/images/$middle_img);background-repeat:no-repeat;background-size:cover;height:345px;'>
		<div class="fixed-width"><div class="one-column column cols" id="le_body_row_1_col_1"><div class="element-container cf" id="le_body_row_1_col_1_el_1">
        	<div class="element"> <div class="op-custom-html-block">
            <center>
			<div style='padding:20px;height:200px;'><p style='font-size:24px;padding-top:35px;'>$locked_msg</p></div>
			</center>
    </div> </div></div></div></div></div>
EOF;
	}
}

function MyWriteMainSectionTop($padding_top = "10") {
	return <<<EOF
	<div style='padding-top:{$padding_top}px;' class="row one-column cf ui-sortable" id="le_body_row_3">
		<div class="fixed-width">
			<div class="one-column column cols" id="le_body_row_3_col_1">
				<div class="element-container cf" id="le_body_row_3_col_1_el_1">
EOF;
}

function MyWriteBottomTestimonials ($video_code_1, $video_code_2) {
	return <<<EOF
<div style="background:#303030;padding-top:40px;padding-bottom:40px;" class="row two-columns cf ui-sortable" id="le_body_row_5">
	<div align=center style="color:#EFEFEF; font-size:28px;padding-bottom:40px"><b>See What Some Of Our Excited Members Are Saying About The System</b><br /></div>
	<div class="fixed-width">
		<div class="one-half column cols" id="le_body_row_5_col_1">
			<div class="element-container cf" id="le_body_row_5_col_1_el_1">
				<div class="element"> 
					<div class="image-caption" style="width:1041px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;">
<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script><div class="wistia_embed wistia_async_$video_code_1" style="height:259px;width:460px">&nbsp;</div>
					</div>
				</div>
			</div>
	</div>
</div>
<div class="one-half column cols" id="le_body_row_5_col_2"><div class="element-container cf" id="le_body_row_5_col_2_el_1">
	<div class="element"> 
		<div class="image-caption" style="width:748px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;">
<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/E-v1.js" async></script><div class="wistia_embed wistia_async_$video_code_2" style="height:259px;width:460px">&nbsp;</div>
		</div>
	</div>
</div>    
EOF;
}

function MyWriteOverviewStep($product, $step, $step_unlocked, $img, $title, $sub_title, $description, $button_text, $button_url) {

	$bg_color = "";
	if (($step*10) % 2) $bg_color = "#F7F7F7";

    // Test if step is locked
    $lock = "";
    $button_color = "#51A7FA";
    if ($step > $step_unlocked) {
	    $lock = '<i class="fa fa-lock"></i>';  
        $button_url = "#";
        $button_color = "grey";  
    }
        
	return <<<EOF
<div style='background:$bg_color;padding-top:75px;padding-bottom:75px;'  class="row two-columns cf ui-sortable section" id="le_body_row_4">
    <div class="fixed-width">
        <div class="one-half column cols" id="le_body_row_4_col_1">
            <div class="element-container cf" id="le_body_row_4_col_1_el_1">
                <div class="element">
                    <div class="image-caption" style='width:400px;margin-top:0px;margin-bottom:px;margin-right:auto;margin-left:auto;'><img alt="" src="$img" border="0" class="full-width" /></div>
                </div>
            </div>
        </div>
        <div class="one-half column cols" id="le_body_row_4_col_2">
            <div class="element-container cf" id="le_body_row_4_col_2_el_1">
                <div class="element">
                    <h6 style='font-size:45px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#C72508;text-align:left;'>$title</h6>
                </div>
            </div>
            <div class="element-container cf" id="le_body_row_4_col_2_el_2">
                <div class="element">
                    <h2 style='font-size:30px;font-family:"Shadows Into Light", sans-serif;font-style:normal;font-weight:normal;color:#404040;text-align:left;'>
                        <!--td {border: 1px solid #ccc;}br {mso-data-placement:same-cell;}-->
                    <span data-sheets-value="[null,2,'How To Create a 6-Figure Digital Online Business In 90-Days Or Less']" data-sheets-userformat="[null,null,961,[null,0],null,null,null,null,null,1,1,4,0]">$sub_title</span></h2>
                </div>
            </div>
            <div class="element-container cf" id="le_body_row_4_col_2_el_3">
                <div class="element">
                    <div class="op-text-block" style="width:100%;text-align: left;">
                        <p style='font-size:15px;font-family:"Ubuntu", sans-serif;text-align: justify;'>$description</p>
                    </div>
                </div>
            </div>
            <div class="split-half column cols subcol" id="le_body_row_4_col_100">
                <div class="element-container cf" id="le_body_row_4_col_100_el_1">
                    <div class="element">
                        <div class="arrow-center"><img src="http://www.members.digitalaltitude.co/wp-content/themes/optimizePressTheme/lib/assets/images/arrows/arrow-7-1.png" class="arrows" alt="arrow" /></div>
                    </div>
                </div>
            </div>
            <div class="split-half column cols subcol" id="le_body_row_4_col_101">
                <div class="element-container cf" id="le_body_row_4_col_101_el_1">
                    <div class="element">
                        <div style="text-align:left">
                            <a href="$button_url" id="btn_1_7" class="css-button style-1" style="font-size:20px;color:#ffffff;font-family:Ubuntu;font-weight:normal;padding:20px 20px;background:$button_color;><span class="text">$button_text $lock</span><span class="hover"></span><span class="active"></span></a></div>
                    </div>
                </div>
            </div>
            <div class="clearcol"></div>
        </div>
    </div>
</div>    
EOF;
}
?>
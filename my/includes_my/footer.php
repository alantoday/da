</div></div></div></div></div></div>
<?php 
if (preg_match('/aspiresystem.co/', $_SERVER['SERVER_NAME'])) {
	$main_domain = "aspiresystem.co";
} else {
	$main_domain = "digitalaltitude.co";
}

if (Like($_SERVER['PHP_SELF'],"%/course%")) {
	$top_padding = 0;
} else {
	$top_padding = 100;
}
?>
<? if(!$skip_header){ ?>
<div style="height:<?php echo $top_padding?>px"></div>

<?php if (empty($HIDE_FOOTER_IMG) && !Like($_SERVER['PHP_SELF'],"%/dashboard/index.php") 
			&& !Like($_SERVER['PHP_SELF'],"%/start-up%") 
			&& !Like($_SERVER['PHP_SELF'],"%/setup%")
			&& !Like($_SERVER['PHP_SELF'],"%/scale%")
			&& !Like($_SERVER['PHP_SELF'],"%/training%")
			) { ?>
    <img width="100%" src="/images/footer_bg_sm.jpg">
<?php } ?>
        </div>
    </div>        
    <div class="full-width footer small-footer-text">
        <div class="row">
            <div class="fixed-width">
                <style>
                    .footer-navigation ul li a,
                    .footer-navigation ul li a:hover{
                        font-family: "Raleway", sans-serif;font-size: 15px;text-shadow: none;font-weight: 300;
                    }
                    .footer,
                    .footer p,
                    .op-promote a,
                    .footer .footer-copyright,
                    .footer .footer-disclaimer{
                        font-family: "Raleway", sans-serif;text-shadow: none;font-weight: 300;
                    }
                    .footer p{ font-size: 15px; }
                </style>
    
                <small class="footer-disclaimer">
                <a href="http://<?php echo $main_domain; ?>/terms" target="_blank">Terms of Service</a> 
                | <a href="http://<?php echo $main_domain; ?>/disclaimer" target="_blank">Earnings Disclaimer</a> 
                | <a href="http://<?php echo $main_domain; ?>/affiliate-policies-and-procedures" target="_blank">Affiliate Policies & Procedures</a> 
                | <a href="http://<?php echo $main_domain; ?>/privacy-policy" target="_blank">Privacy Policy</a> 
                | <a href="http://<?php echo $main_domain; ?>/refund-policy" target="_blank">Refund Policy</a></small>
                <p class="footer-copyright">© <?php echo "2015-".date("Y"); ?> Digital Altitude LLC All Rights Reserved. Ph#: (800) 820-7589 Email: support@digitalaltitude.co</p>
            </div>
        </div>
    </div>
<? } ?>
</div><!-- container -->

<link href="//fonts.googleapis.com/css?family=Raleway:300,r,b" rel="stylesheet" type="text/css" />
<script type='text/javascript' src='/js/common/jquery.blockUI.js?ver=2.2.3'></script>
<script type='text/javascript' src='/js/op-front-all.min.js?ver=2.5.1.1'></script>
<script type='text/javascript' src='/js/menus.min.js?ver=2.5.1.1'></script>
<? if(!$skip_header){ ?>
<!-- Start of digitalaltitude Zendesk Widget script -->
<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("//assets.zendesk.com/embeddable_framework/main.js","digitalaltitude.zendesk.com");/*]]>*/</script>
<!-- End of digitalaltitude Zendesk Widget script -->
<? } ?>
</body>
</html>
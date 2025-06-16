<?php
global $theme_shortname, $google_ecommerce_order_data;
if (get_option($theme_shortname."_google_use_analytics") == 'U' && get_option($theme_shortname."_google_trackingid") ){
?>
<!-- GOOGLE ANALYTICS -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', '<?php echo get_option($theme_shortname."_google_trackingid")?>', 'auto');
<?php if (get_option($theme_shortname."_google_option1") == 'on'){?>
//인구통계 및 관심분야 보고서 사용
ga('require', 'displayfeatures');
<?php }?>
<?php if (get_option($theme_shortname."_google_option2") == 'on'){?>
//향상된 링크 기여 사용
ga('require', 'linkid', 'linkid.js');
<?php }?>
ga('send', 'pageview');
<?php if(get_option($theme_shortname."_google_option3") == 'on' && $google_ecommerce_order_data != "") {?>
//GOOGLE ANALYTICS ECOMMERCE
ga('require', 'ecommerce');
<?php echo $google_ecommerce_order_data;?>
ga('ecommerce:send');
<?php }?>
</script>
<?php }?>
<?php if (get_option($theme_shortname."_naver_analytics_use") == 'U' && get_option($theme_shortname."_naver_analytics_id")){?>
<!-- NAVER ANALYTICS  -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script>
<script type="text/javascript">
if(!wcs_add) var wcs_add = {};
wcs_add["wa"] = "<?php echo get_option($theme_shortname."_naver_analytics_id")?>";
wcs_do();
</script>
<?php }?>
<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname,$bbseSiteType;
?>
  <div id="skyBanner2">
    <div class="side_btn_wrap">
    	<ul>
    		<li class="close"><a href="#"><span class="dashicons dashicons-arrow-right-alt2"></span></a></li>
    		<li class="open hide"><a href="#"><span class="dashicons dashicons-arrow-left-alt2"></span></a></li>
    		
    		<?php if(get_option($theme_shortname.'_shopsidebar_use_sns_menu') == 'U' && !empty(get_option($theme_shortname.'_shopsidebar_sns_kakao'))) :?>
    		<li class="kakaotalk"><a href="<?php echo get_option($theme_shortname.'_shopsidebar_sns_kakao'); ?>"></a></li>
    		<?php endif; ?>
    		<?php if(get_option($theme_shortname.'_shopsidebar_use_sns_menu') == 'U' && !empty(get_option($theme_shortname.'_shopsidebar_sns_naver'))) :?>
    		<li class="naver"><a href="<?php echo get_option($theme_shortname.'_shopsidebar_sns_naver'); ?>"></a></li>
    		<?php endif; ?>
    		<?php if(get_option($theme_shortname.'_shopsidebar_use_sns_menu') == 'U' && !empty(get_option($theme_shortname.'_shopsidebar_sns_insta'))) :?>
    		<li class="instagram"><a href="<?php echo get_option($theme_shortname.'_shopsidebar_sns_insta'); ?>"></a></li>
    		<?php endif; ?>
    		<?php if(get_option($theme_shortname.'_shopsidebar_use_sns_menu') == 'U' && !empty(get_option($theme_shortname.'_shopsidebar_sns_facebook'))) :?>
    		<li class="facebook"><a href="<?php echo get_option($theme_shortname.'_shopsidebar_sns_facebook'); ?>"></a></li>
    		<?php endif; ?>
    		
    		<li class="go_top">
    			<a href="#">
    				<span class="top_text">TOP</span>
    				<span class="dashicons dashicons-arrow-up-alt"></span>
    			</a>
    		</li>
    	<?php
    	//<li class="go_bottom"><a href="#"><span class="dashicons dashicons-arrow-down-alt2"></span></a></li>
    	?>	
    	</ul>
    	
    </div>
    <div class="skyContent">
    	<?php if(get_option($theme_shortname.'_shopsidebar_top_banner') == 'U') :?>
    	<div id="side_banner">
    		<h4>이벤트</h4>
    		<ul>
    			<?php
    				for ($i=1; $i < 3; $i++) {
    					if(!empty(get_option($theme_shortname.'_shopsidebar_top_banner_img_'.$i))){
    			?>
    			<li>
    				<a target="<?php echo get_option($theme_shortname.'_shopsidebar_top_banner_url_'.$i.'_window');?>" href="<?php echo get_option($theme_shortname.'_shopsidebar_top_banner_url_'.$i); ?>">
    					<img src="<?php echo get_option($theme_shortname.'_shopsidebar_top_banner_img_'.$i); ?>" />
    				</a>
				</li>
    			<?php
    					} 
				?>
				<?php
					}
    			?>
    		</ul>
    	</div>
    	<?php endif; ?>
    	<?php if(get_option($theme_shortname.'_shopsidebar_use_quick_menu') == 'U') :?>
    	<div id="side_quick">
    		<h4>퀵메뉴</h4>
    		<ul>
    			<?php
    				for ($i=1; $i < 4; $i++) {
    					if(!empty(get_option($theme_shortname.'_shopsidebar_quick_menu_img_'.$i))){
    			?>
    			<li>
    				<a target="<?php echo get_option($theme_shortname.'_shopsidebar_quick_menu_url_'.$i.'_window');?>" href="<?php echo get_option($theme_shortname.'_shopsidebar_quick_menu_url_'.$i); ?>">
    					<img src="<?php echo get_option($theme_shortname.'_shopsidebar_quick_menu_img_'.$i); ?>" />
    					<div class="menu_name"><?php echo get_option($theme_shortname.'_shopsidebar_quick_menu_text_'.$i); ?></div>
    				</a>
				</li>
    			<?php
    					} 
				?>
				<?php
					}
    			?>
    		</ul>
    	</div>
    	<?php endif; ?>
    	
<?php if(plugin_active_check('BBSe_Commerce')){?>
	<div id="side_today">
		<h4>오늘본상품</h4>
		<ul>
		<?php 
			$rCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_recent WHERE remote_ip='".$_SERVER['REMOTE_ADDR']."'");
		?>
		<?php echo bbse_commerce_get_recent_view_goods2($_SERVER['REMOTE_ADDR']);?>
		</ul>
		<div class="slider_btn_wrap">
			<div class="today_control" id="today_prev"></div>
			<div class="today_control" id="today_next"></div>
		</div>
	</div>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">
	  	<script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>
	
	  	<script>
		    jQuery(document).ready(function($){
		      	$('#side_today ul').bxSlider({
		      		pager: false,
		      		controls: true,
		      		prevSelector: '#today_prev',
		      		nextSelector: '#today_next',
		      		nextText: '<span class="dashicons dashicons-arrow-right-alt2"></span>',
		      		prevText: '<span class="dashicons dashicons-arrow-left-alt2"></span>',
		      		touchEnabled : (navigator.maxTouchPoints > 0),
		      	});
		    });
  		</script>
<?php }else{?>
	  <div style="height:25px;"></div>
<?php }?>
    </div>

    <script>
    jQuery(document).ready(function($){
    	if(jQuery('#coupon_apply_wrap').length > 0){
			$('#skyBanner2').remove();
		}
    	$('.go_top').click(function(){
    		$('html,body').animate({
    			scrollTop:0
    		},500);
    	});
    	$('.go_bottom').click(function(){
    		$('html,body').animate({
    			scrollTop:$(document).height()
    		},500);
    	});
    	$('.close').click(function(){
    		$(this).addClass('hide');
    		$('.open').removeClass('hide');
    		$('#skyBanner2').css('right','-100px');
    		return false;
    	});
    	$('.open').click(function(){
    		$(this).addClass('hide');
    		$('.close').removeClass('hide');
    		$('#skyBanner2').css('right','0');
    		return false;
    	});
    });
	var remove_recent = function(rIdx,remoteIp){

		var tMode="removeRecent"
		var apiUrl=common_var.goods_template_url+"/proc/index-banner-right.exec.php";

		jQuery.ajax({
			type: 'post', 
			async: true, 
			url: apiUrl, 
			data: {tMode:tMode, rIdx:rIdx, remoteIp:remoteIp}, 
			success: function(data){
				var result = data.split("|||"); 

				if(result[0] == "success"){
					jQuery('.skyTitle > em').text("("+result[1]+")");
					jQuery("#skyBannerList-"+rIdx).remove();

					var skyRecentSwiper = new Swiper('.skyRecentList .swiper-container',{
						direction           : 'vertical',
						loop                : false,
						autoplay            : false,
						speed               : 300,
						slidesPerView       : 3,
						nextButton          : '.skyRecentList .swiper-button-next',
						prevButton          : '.skyRecentList .swiper-button-prev',
					});

				}
				else if(result[0] == "emptyRecent"){
					jQuery('.skyRecentList .list').html("");
					jQuery('.skyTitle > em').text("(0)");
				}
				else{
					alert("서버와의 통신이 실패했습니다.   ");
				}
			}, 
			error: function(data, status, err){
				alert("서버와의 통신이 실패했습니다.   ");
			}
		});	

	};
    </script>
<style>
#skyBanner2{
	position: fixed;
    background: #fff;
    width: 100px;
    right: 0;
    height: 100%;
    top: 0;
    border-left: 1px solid #ccc;
    z-index: 1111111;
    padding: 50px 10px 50px 9px;
    box-sizing: border-box;
    text-align: center;
    transition: right 1s;
    <?php
    if(!empty(get_option($theme_shortname."_color_sidebar_background"))){
   		echo 'background:'.get_option($theme_shortname."_color_sidebar_background");
    }
    ?>
}
#skyBanner2 .side_btn_wrap{
    position: absolute;
    left: -41px;
    top: 50%;
}
#skyBanner2 .side_btn_wrap ul{
    box-sizing: border-box;
}
#skyBanner2 .side_btn_wrap ul li{
	width: 40px;
    height: 40px;
    text-align: center;
    line-height: 40px;
    background: #fff;
    
    box-sizing: border-box;
}
#skyBanner2 .side_btn_wrap ul li a{
	display: block;
	width: 100%;
	height: 100%;
}
#skyBanner2 .side_btn_wrap ul li a .dashicons{
	line-height: 40px;
    width: 40px;
    height: 40px;
}
#skyBanner2 .side_btn_wrap ul li.close{
    background: #222;
}
#skyBanner2 .side_btn_wrap ul li.close a,
#skyBanner2 .side_btn_wrap ul li.open a{
	color: #fff;
}
#skyBanner2 .side_btn_wrap ul li.open{
	background: #222;
}
#skyBanner2 .hide,
.goOnTop{
	display: none;
}
#skyBanner2 .side_btn_wrap ul li.facebook{
	background-image: url('<?php echo get_template_directory_uri();?>/images/sns.jpg');
	border-bottom: none;
}
#skyBanner2 .side_btn_wrap ul li.instagram{
	background-image: url('<?php echo get_template_directory_uri();?>/images/sns.jpg');
	border-bottom: none;
	background-position-y: 120px;
}
#skyBanner2 .side_btn_wrap ul li.kakaotalk{
	background-image: url('<?php echo get_template_directory_uri();?>/images/sns.jpg');
	border-bottom: none;
	background-position-y: 80px;
}
#skyBanner2 .side_btn_wrap ul li.naver{
	background-image: url('<?php echo get_template_directory_uri();?>/images/sns.jpg');
	border-bottom: none;
	background-position-y: 40px;
}
#skyBanner2 .side_btn_wrap ul li.go_top{
	border-bottom: 1px solid #222;
    border-left: 1px solid #222;
    background: #222;
    line-height: 1;
    padding: 7px 0 0;
}
#skyBanner2 .side_btn_wrap ul li.go_top .top_text{
	color: #fff;
    font-weight: 500;
    font-size: 12px;
    line-height: 1;
    display: block;
}
#skyBanner2 .side_btn_wrap ul li.go_top a .dashicons{
	display: block;
    line-height: 1;
    color: #fff;
    font-size: 12px;
    width: auto;
    height: auto;
    margin: 2px 0 0;
}
#skyBanner2 .side_btn_wrap ul li.go_bottom{
	border-bottom: 1px solid #ccc;
	border-left: 1px solid #ccc;
}
#skyBanner2 #side_banner{
	margin: 0;
}
#skyBanner2 #side_quick{
	margin: 20px 0;
}
#skyBanner2 #side_today{
	margin: 0;
}
#skyBanner2 .skyContent{
	margin: 0;
}
#skyBanner2 .skyContent h4{
	font-weight: 500;
    font-size: 15px;
	<?php
    if(!empty(get_option($theme_shortname."_color_sidebar_title"))){
   		echo 'color:'.get_option($theme_shortname."_shopcolor_sidebar_title");
    }
    ?>
    margin: 0 0 5px;
}
#skyBanner2 .skyContent h4:after{
	content: none;
    height: 2px;
    background: #222;
    width: 25px;
    display: block;
    margin: 5px auto 20px;
    <?php
    if(!empty(get_option($theme_shortname."_color_sidebar_title"))){
   		echo 'background:'.get_option($theme_shortname."_color_sidebar_title");
    }
    ?>
}
#skyBanner2 .bx-wrapper{
	margin: 0;
	border: none;
	box-shadow: none;
}
#skyBanner2 .bx-wrapper img{
	margin: 0 auto;
}
#skyBanner2 .bx-wrapper .pname{
	padding: 5px;
    line-height: 1.2;
    font-size: 11px;
}
#skyBanner2 .bx-wrapper .price{
    font-size: 13px;
    padding: 0 5px 5px;
}
.slider_btn_wrap{
	margin: 10px 0 0;
}
.slider_btn_wrap .today_control{
	display: inline-block;
    vertical-align: middle;
    margin: 0 8px;
}
#skyBanner2 ul{
	padding: 0;
	margin: 0;
}
#skyBanner2 li{
	list-style: none;
}
#skyBanner2 #side_banner li{
	margin: 0 0 5px;
}
#skyBanner2 #side_banner li img{
	max-width: 100%;
}
#skyBanner2 #side_quick ul{
	font-size: 0;
    border-bottom: 1px solid #ccc;
}
#skyBanner2 #side_quick ul li{
	display: inline-block;
    vertical-align: middle;
    width: 100%;
    border: 1px solid #ccc;
    box-sizing: border-box;
    border-bottom: none;
}
#skyBanner2 #side_quick ul li a{
	display: block;
    padding: 15px 0;
}
#skyBanner2 #side_quick ul li a img{
	
}
#skyBanner2 #side_quick ul li a .menu_name{
	font-size: 12px;
    color: #222;
    letter-spacing: -1px;
    font-weight: 300;
    margin: 3px 0 0;
}
</style>
  </div>
  <?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;

if(get_option($theme_shortname."_display_use_mobile_icon_line")=='U'){
	$lineType=(get_option($theme_shortname."_display_mobile_icon_line_type")=='dot')?"dotted":"solid";
	$lineStyle="border-right:1px ".$lineType." ".get_option($theme_shortname."_display_mobile_icon_line_color").";";
}
else $lineStyle="";

if(get_option($theme_shortname."_display_use_mobile_icon_bgcolor")=='U'){
	$iconColor=get_option($theme_shortname."_display_mobile_icon_color");
	$iconBgColor=get_option($theme_shortname."_display_mobile_icon_bgcolor");
	$iconClass="type_iconBg";
}
else{
	$iconColor=get_option($theme_shortname."_display_mobile_icon_menu_color");
	$iconBgColor=get_option($theme_shortname."_display_mobile_icon_menu_bgcolor");
	$iconClass="type_icon";
}

if(get_option($theme_shortname."_display_use_mobile_icon_shadow")=='U'){
	$shadowColor=get_option($theme_shortname."_display_mobile_icon_shadow_color");
	$shadowAlpha=get_option($theme_shortname."_display_mobile_icon_shadow_alpha");
	$shadowStyle="box-shadow: 0 2px 9px ".bbse_hex_to_rgba($shadowColor,$shadowAlpha).";";
}
else $shadowStyle="";

if(get_option($theme_shortname."_display_use_mobile_icon_position")=='bottom') $fixedPosition="position:fixed;";
else $fixedPosition=""; 
?>
<style type="text/css">
    .mobileSwipe_wrap{position:relative;opacity:0;display:none;margin:0;padding:0;width:100%;background:<?php echo get_option($theme_shortname."_display_mobile_icon_menu_bgcolor");?>;z-index:100;<?php echo $shadowStyle;?>}
    .mobileSwipe_wrap.show{opacity:1;}
    .mobileSwipe_wrap.mobileSwipe_footer_wrap{<?php echo $fixedPosition;?>bottom:0;left:0;}
    .mobileSwipe_wrap .mobileSwipe{position:relative;margin:0;padding:0;width:100%;height:0;overflow:hidden;}
    .mobileSwipe_wrap .mobileSwipe ul{margin:0;padding:0;list-style:none;width:100%;height:80px;}
    .mobileSwipe_wrap .mobileSwipe ul li{position:relative;float:left;margin:0;padding:0;width:25%;height:80px;<?php echo $lineStyle;?>}
    .mobileSwipe_wrap .mobileSwipe ul li a{display:block;width:100%;height:100%;text-decoration:none;}
    .mobileSwipe_wrap .mobileSwipe ul li a .ms_thumbnail{display:block;margin:0;padding:0;width:100%;height:60px;line-height:60px;text-align:center;}

    .mobileSwipe_wrap .mobileSwipe.type_iconBg ul li a .ms_thumbnail .thumbnail_bg{display:inline-block;width:40px;height:40px;line-height:40px;border-radius:10px;text-align:center;background:<?php echo $iconBgColor;?>;vertical-align:middle;}
    .mobileSwipe_wrap .mobileSwipe.type_iconBg ul li a .ms_thumbnail .thumbnail_bg i{font-size:30px;color:<?php echo $iconColor;?>;vertical-align:middle;}
    .mobileSwipe_wrap .mobileSwipe.type_icon ul li a .ms_thumbnail .thumbnail_bg{display:block;margin:0;padding:0;width:100%;height:100%;}
    .mobileSwipe_wrap .mobileSwipe.type_icon ul li a .ms_thumbnail .thumbnail_bg i{font-size:30px;color:<?php echo $iconColor;?>;vertical-align:middle;}

    .mobileSwipe_wrap .mobileSwipe ul li a .ms_txt{display:block;margin:0 auto;width:83%;font-size:11px;font-weight:bold;color:<?php echo get_option($theme_shortname."_display_mobile_icon_menu_color");?>;text-align:center;box-sizing:border-box;white-space:nowrap;overflow:hidden;}
    .mobileSwipe_wrap .mobileSwipe ul .mobileSwipe_btn{position:absolute;margin:0;padding:0;top:0;width:30px;height:80px;border:0;outline:0;cursor:pointer;z-index:1;transition:all 0.4s ease;}
    .mobileSwipe_wrap .mobileSwipe ul .mobileSwipe_btn_prev{left:0;background:rgba(255,255,255,0.5) url(<?php bloginfo('template_url')?>/images/m_arrow_prev.png) no-repeat 50% 50%;}
    .mobileSwipe_wrap .mobileSwipe ul .mobileSwipe_btn_next{right:0;background:rgba(255,255,255,0.5) url(<?php bloginfo('template_url')?>/images/m_arrow_next.png) no-repeat 50% 50%;}
    .mobileSwipe_wrap .mobileSwipe ul .mobileSwipe_btn.slick-disabled{opacity:0;}
<?php if(!$fixedPosition){?>
	.mobile_action .mobileSwipe_wrap.mobileSwipe_footer_wrap {
		left: 250px;
	 }
<?php }?>
    @media screen and (max-width:980px){
      .mobileSwipe_wrap{display:block;}
    }
    </style>

    <script type="text/javascript">
		jQuery(document).ready(function(){
		  jQuery('.mobileSwipe > ul').slick({
			dots: false,
			infinite: false,
			swipeToSlide: true,
			slidesToShow: 8,
			prevArrow: '<button type="button" data-role="none" class="mobileSwipe_btn mobileSwipe_btn_prev" aria-label="previous"></button>',
			nextArrow: '<button type="button" data-role="none" class="mobileSwipe_btn mobileSwipe_btn_next" aria-label="next"></button>',
			responsive: [
				{
				  breakpoint: 600,
				  settings: {
					dots: false,
					infinite: false,
					swipeToSlide: true,
					slidesToShow: 4,
				  }
				}
			  ]
		  });

		  jQuery('.mobileSwipe_wrap').addClass('show');
		  jQuery('.mobileSwipe_wrap .mobileSwipe').css('height','80px');

<?php
	if($fixedPosition){
?>
		  jQuery('.mobileIconFooterMargin').css('height','80px');
		  jQuery('.goOnTop').css('margin-bottom','80px');
<?php
}
else{
?>
		  jQuery('#siteNav-type2').css('z-index','110');
<?php
}
?>
		}); 
    </script>
    <div class="mobileSwipe_wrap mobileSwipe_footer_wrap">
      <div class="mobileSwipe <?php echo $iconClass;?>"><!--type_iconBg : 배경색, type_icon:아이콘 배경 사용안함-->
        <ul>
		<?php
		for($z=1;$z<9;$z++){
			if(get_option($theme_shortname."_display_mobile_icon_icon_".$z) && get_option($theme_shortname."_display_mobile_icon_title_".$z)){
		?>
          <li>
            <a href="<?php echo get_option($theme_shortname."_display_mobile_icon_url_".$z);?>" target="<?php echo get_option($theme_shortname."_display_mobile_icon_url_".$z."_window");?>" title="<?php echo stripslashes(get_option($theme_shortname."_display_mobile_icon_title_".$z));?>">
              <div class="ms_thumbnail">
                <span class="thumbnail_bg"><i class="fa <?php echo get_option($theme_shortname."_display_mobile_icon_icon_".$z);?>"></i></span>
              </div>
              <span class="ms_txt"><?php echo stripslashes(get_option($theme_shortname."_display_mobile_icon_title_".$z));?></span>
            </a>
          </li>
		<?php
			}
		}
		?>
        </ul>
      </div>
    </div>
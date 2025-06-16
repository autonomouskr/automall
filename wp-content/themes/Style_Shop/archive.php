<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

get_header();
GLOBAL $theme_shortname;
?>

<hr />
		<div id="sidebar">
      <?php get_sidebar('main')?>
		</div><!--//#sidebar-->

		<div id="content">
			<?php
			#로케이션
			get_template_part('part/sub', 'location');
			?>

			<div class="page_cont">
				<h2 class="page_title"><?php bbse_page_type_title()?></h2>
			</div>
			<?php if(get_option($theme_shortname.'_display_use_bottom_banner')=="U"){?>
			<div class="main_section main_bnn">
				<?php if(get_option($theme_shortname."_display_middle_banner_image_1")!="") {?>
				<a href="<?php echo get_option($theme_shortname."_display_bottom_banner_link_1")?get_option($theme_shortname."_display_bottom_banner_link_1"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_display_bottom_banner_link_1_window")?>"><img src="<?php echo get_option($theme_shortname."_display_bottom_banner_image_1")?>" alt="하단배너" style="border:1px solid #efefef;" /></a>
				<?php }?>
			</div><!--//광고 배너-->
			<?php }?>
		    <?php get_template_part('content', 'list-default'); ?>
		    <?php echo bbse_page_nav()?>
		</div><!--//#content -->
<hr />
<?php get_footer();

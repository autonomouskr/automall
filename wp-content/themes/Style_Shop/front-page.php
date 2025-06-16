<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

if(get_option($theme_shortname."_intro_use")=='U' && $_SESSION['bbseIntro']!='skip'){
	$skinname = (get_option($theme_shortname."_config_skin_name"))?get_option($theme_shortname."_config_skin_name"):"basic";
	get_template_part('skin/'.$skinname.'/index','intro');
}
else{
	get_header();
?>
	<hr />
	<div id="sidebar">
		<?php get_sidebar('main')?>
	</div><!--//#sidebar-->

	<div id="content" class="main_cont">
	<?php
	global $theme_shortname;
	#롤링배너
	get_template_part('part/index', 'rolling-banner');

	#추천상품 (우선순위에 따른 처리)
	if(get_option($theme_shortname."_goodsplace_sort_1")=="1") get_template_part('part/index', 'goods-recommend');
	if(get_option($theme_shortname."_goodsplace_sort_2")=="1") get_template_part('part/index', 'goods-best');
	if(get_option($theme_shortname."_goodsplace_sort_3")=="1") get_template_part('part/index', 'goods-md');
	if(get_option($theme_shortname."_goodsplace_sort_4")=="1") get_template_part('part/index', 'goods-new');
	if(get_option($theme_shortname."_goodsplace_sort_5")=="1") get_template_part('part/index', 'goods-review');

	#중간배너
	get_template_part('part/index', 'banner-middle');

	#베스트 상품 (우선순위에 따른 처리)
	if(get_option($theme_shortname."_goodsplace_sort_1")=="2") get_template_part('part/index', 'goods-recommend');
	if(get_option($theme_shortname."_goodsplace_sort_2")=="2") get_template_part('part/index', 'goods-best');
	if(get_option($theme_shortname."_goodsplace_sort_3")=="2") get_template_part('part/index', 'goods-md');
	if(get_option($theme_shortname."_goodsplace_sort_4")=="2") get_template_part('part/index', 'goods-new');
	if(get_option($theme_shortname."_goodsplace_sort_5")=="2") get_template_part('part/index', 'goods-review');

	#하단배너
	get_template_part('part/index', 'banner-bottom');

	#MD 기획상품 (우선순위에 따른 처리)
	if(get_option($theme_shortname."_goodsplace_sort_1")=="3") get_template_part('part/index', 'goods-recommend');
	if(get_option($theme_shortname."_goodsplace_sort_2")=="3") get_template_part('part/index', 'goods-best');
	if(get_option($theme_shortname."_goodsplace_sort_3")=="3") get_template_part('part/index', 'goods-md');
	if(get_option($theme_shortname."_goodsplace_sort_4")=="3") get_template_part('part/index', 'goods-new');
	if(get_option($theme_shortname."_goodsplace_sort_5")=="3") get_template_part('part/index', 'goods-review');

	#신상품 (우선순위에 따른 처리)
	if(get_option($theme_shortname."_goodsplace_sort_1")=="4") get_template_part('part/index', 'goods-recommend');
	if(get_option($theme_shortname."_goodsplace_sort_2")=="4") get_template_part('part/index', 'goods-best');
	if(get_option($theme_shortname."_goodsplace_sort_3")=="4") get_template_part('part/index', 'goods-md');
	if(get_option($theme_shortname."_goodsplace_sort_4")=="4") get_template_part('part/index', 'goods-new');
	if(get_option($theme_shortname."_goodsplace_sort_5")=="4") get_template_part('part/index', 'goods-review');

	#베스트 상품평 (우선순위에 따른 처리)
	if(get_option($theme_shortname."_goodsplace_sort_1")=="5") get_template_part('part/index', 'goods-recommend');
	if(get_option($theme_shortname."_goodsplace_sort_2")=="5") get_template_part('part/index', 'goods-best');
	if(get_option($theme_shortname."_goodsplace_sort_3")=="5") get_template_part('part/index', 'goods-md');
	if(get_option($theme_shortname."_goodsplace_sort_4")=="5") get_template_part('part/index', 'goods-new');
	if(get_option($theme_shortname."_goodsplace_sort_5")=="5") get_template_part('part/index', 'goods-review');

	#공지사항과 최근글
	get_template_part('part/index', 'notice_cs');
	?>
	</div><!--//#content -->

	<hr />
	<?php get_footer();
}
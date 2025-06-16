<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

get_header(); 
?>
	<hr />
	<div id="sidebar">
	<?php
		$current_page_info = get_queried_object();
		$current_page_id = $wp_query->get_queried_object_id();
		if(get_option('bbse_commerce_login_page') == $current_page_info->ID) {
			get_sidebar('member');
		}else if(get_option('bbse_commerce_join_page') == $current_page_info->ID) {
			if(is_user_logged_in()) get_sidebar('mypage');
			else get_sidebar('member');
		}else if(get_option('bbse_commerce_id_search_page') == $current_page_info->ID) {
			get_sidebar('member');
		}else if(get_option('bbse_commerce_pass_search_page') == $current_page_info->ID) {
			get_sidebar('member');
		}else if(get_option('bbse_commerce_delete_page') == $current_page_info->ID) {
			get_sidebar('mypage');
		}else{
			get_sidebar('main');
		}
	?>
	</div><!--//#sidebar-->

	<div id="content">
        <?php
        #로케이션
        get_template_part('part/sub', 'location');
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class('page_cont'); ?>>
        <?php
        #컨텐츠
        if(have_posts()){
          while(have_posts()){
            the_post();
            get_template_part('content');
          }//endwhile
        }// endif
        ?>
		</article>
	</div><!--//#content -->
<hr />
<?php get_footer();

<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$current_page_info = get_queried_object();
?>
<div class="side_section side-nav general">
	<h2>회원서비스</h2>
	<ul id="menu-side_member" class="">
		<!--
		<li class="menu-item menu-item-has-children">
			<a href="#">로그인</a>
			<ul class="sub-menu">
				<li class="menu-item menu-item-has-children">
					<a href="">더미2</a>
					<ul class="sub-menu">
						<li class="menu-item"><a href="#">더미3</a></li>
					</ul>
				</li>
				<li class="menu-item"><a href="#">더미2</a></li>
			</ul>
		</li>
		-->
		<li class="menu-item<?php echo (get_option($theme_shortname.'_login_page') == $current_page_info->ID)?" active":""?>"><a href="<?php echo get_permalink(get_option($theme_shortname.'_login_page'));?>">로그인</a></li>
		<!-- <li class="menu-item<?php echo (get_option($theme_shortname.'_join_page') == $current_page_info->ID)?" active":""?>"><a href="<?php echo get_permalink(get_option($theme_shortname.'_join_page'));?>">회원가입</a></li>
		<li class="menu-item<?php echo (get_option($theme_shortname.'_id_search_page') == $current_page_info->ID)?" active":""?>"><a href="<?php echo get_permalink(get_option($theme_shortname.'_id_search_page'));?>">아이디찾기</a></li>
		<li class="menu-item<?php echo (get_option($theme_shortname.'_pass_search_page') == $current_page_info->ID)?" active":""?>"><a href="<?php echo get_permalink(get_option($theme_shortname.'_pass_search_page'));?>">비밀번호찾기</a></li> -->
	<?php if(get_option($theme_shortname.'_memberpage_agreement_leave')>'0'){?>
		<li class="menu-item"><a href="<?php echo get_permalink(get_option($theme_shortname.'_memberpage_agreement'))?>">이용약관</a></li>
	<?php }?>
	<?php if(get_option($theme_shortname.'_memberpage_agreement_leave')>'0'){?>
		<li class="menu-item"><a href="<?php echo get_permalink(get_option($theme_shortname.'_memberpage_private_1'))?>">개인정보 취급방침</a></li>
	<?php }?>
	</ul>
</div>

<!--
예제
<div class="side_section side-nav general">
  <h2>회원서비스</h2>
  <?php /*
  wp_nav_menu(
    array(
      'menu'         => 'menu-side_member',
      'container'    => false,
      'menu_id'      => '',
      'menu_class'   => '',
      'depth'        =>  3,
    )
  );
  */ ?>
</div>
 -->
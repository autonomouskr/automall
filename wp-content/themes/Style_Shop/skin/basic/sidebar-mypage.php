<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname,$current_user;
$current_page_info = get_queried_object();
$screen_code = $wpdb->get_row("select screen_code from bbse_commerce_membership where user_id = '".$current_user->user_login."' and leave_yn != 1");
?>
<div class="mypage-left-menu side_section side-nav general">
	<h2>마이페이지</h2>
	<ul id="menu-side_member" class="">
	<?php if(is_user_logged_in() || ($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData'])) {?>
		<li class="menu-item<?php echo ($_GET['bbseMy']=="order-list" || $_GET['bbseMy']=="order-detail")?" active":"";?>"><a href="<?php echo home_url();?>/?bbseMy=order-list">주문/배송조회</a></li>
		<li class="menu-item<?php echo ($_GET['bbseMy']=="refund" || $_GET['bbseMy']=="refund-detail")?" active":"";?>"><a href="<?php echo home_url();?>/?bbseMy=refund">취소/반품신청조회</a></li>
		<li class="menu-item<?php echo ($_GET['bbseMy']=="interest")?" active":"";?>"><a href="<?php echo home_url();?>/?bbseMy=interest">관심상품(찜)</a></li>
		<li class="menu-item<?php echo ($_GET['bbseMy']=="coupon")?" active":"";?>"><a href="/?bbseMy=coupon">쿠폰내역</a></li>
		<?php if(is_user_logged_in()){?>
<!-- 			<li class="menu-item<?php echo ($_GET['bbseMy']=="point")?" active":"";?>"><a href="<?php echo home_url();?>/?bbseMy=point">적립금내역</a></li> -->
		<?php }?>
		<li class="menu-item<?php echo ($_GET['bbseMy']=="man2man")?" active":"";?>"><a href="<?php echo home_url();?>/?bbseMy=man2man">나의1:1문의</a></li>
		<?php if($screen_code->screen_code == "GU"){?>
		<li class="menu-item<?php echo ($_GET['bbseMy']=="myInven")?" active":"";?>"><a href="<?php echo home_url();?>/?bbseMy=myInven">재고관리</a></li>
		<?php }?>
		<?php
		$oCnfCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='order'");
		if($oCnfCnt>'0'){
			$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
			$orderInfo=unserialize($confData->config_data);
		}
		if($orderInfo['soldout_notice_use']=='on'){
		?>
		  <li class="menu-item<?php echo ($_GET['bbseMy']=="soldoutNotice")?" active":"";?>"><a href="<?php echo home_url();?>?bbseMy=soldoutNotice">품절상품 입고알림</a></li>
		<?php }?>

	    <!-- <?php if(is_user_logged_in()){?>
			<li class="menu-item menu-item-has-children<?php echo (get_option($theme_shortname.'_join_page') == $current_page_info->ID || get_option($theme_shortname.'_delete_page') == $current_page_info->ID)?" active":""?>">
				<a href="#">회원정보</a>
				<ul class="sub-menu">
					<li class="menu-item"><a href="<?php echo get_option($theme_shortname.'_join_page')?get_permalink(get_option($theme_shortname.'_join_page')):"#"; ?>">회원정보 수정</a></li>
					<li class="menu-item"><a href="<?php echo get_option($theme_shortname.'_delete_page')?get_permalink(get_option($theme_shortname.'_delete_page')):"#"; ?>">회원탈퇴</a></li>
				</ul>		
			</li>
		<?php }?>
		
		-->
	<?php }?>
	</ul>
</div>
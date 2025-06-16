<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/*
My page
*/
get_header();
global $theme_shortname;
$bbseMy=get_query_var( 'bbseMy' );

if(!is_user_logged_in() && ($_SESSION['snsLogin']!='Y' || !$_SESSION['snsLoginData'])){
	if($bbseMy=="order-detail" && $_POST['order_no']!="" && $_POST['order_name']!="") {
	}else{
		echo "<script type='text/javascript'>location.href='".get_permalink(get_option($theme_shortname.'_login_page'))."';</script></div></div></body></html>";
		exit;
	}
}
?>
	<hr />

	<div id="sidebar">
		<?php get_sidebar('mypage')?>
	</div><!--//#sidebar-->

	<div id="content">
        <?php
        #로케이션
        get_template_part('part/sub', 'location');
		?>
		<div class="page_cont"  id="bbseMy<?php echo $bbseMy?>">
		<?php
			#마이페이지 메인 :mypage
			#주문/배송조회 : order
			#주문 상세정보 : order-detail
			#취소/반품신청조회 : refund
			#관심상품 : interest
			#쿠폰내역 : coupon
			#적립금내역 : point
			#나의1:1문의 : man2man
		    #재고관리 : myInven
		    #재고관리 항목추가 : inven-detail
			$myMneuItem = array('mypage', 'order-list', 'order-detail','refund', 'refund-detail', 'interest', 'coupon', 'point', 'man2man', 'soldoutNotice', 'myInven', 'inven-detail');
			if(in_array($bbseMy, $myMneuItem)==true) {
				$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
				get_template_part('skin/'.$skinname.'/mypage', $bbseMy);
			}else{
				echo "존재하지 않는 페이지 입니다.";
			}
		?>	
		</div>
	</div><!--//#content -->
<?php get_footer();
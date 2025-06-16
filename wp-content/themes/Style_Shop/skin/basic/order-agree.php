<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* Cart */
get_header();

global $current_user,$theme_shortname,$currentSessionID;

if(is_user_logged_in()){
	echo "<script>location.href='".home_url()."/';</script>";
	exit;
}

wp_get_current_user();
$bbsePage=get_query_var( 'bbsePage' );

$V = $_POST;
?>
<script language="javascript">
jQuery(document).ready(function() {
	jQuery("#agreeAll").click(function() {
		if(jQuery("input:checkbox[id='agreeAll']").is(":checked") == true) {
			jQuery("input:checkbox[id='agreeChk1']").prop("checked", true);
			jQuery("input:checkbox[id='agreeChk4']").prop("checked", true);
		<?php if(!get_option($theme_shortname."_member_agreement_nonmember_view_yn") || get_option($theme_shortname."_member_agreement_nonmember_view_yn")=='U'){?>
			jQuery("input:checkbox[id='agreeChk3']").prop("checked", true);
		<?php }?>
		}
		else{
			jQuery("input:checkbox[id='agreeChk1']").prop("checked", false);
			jQuery("input:checkbox[id='agreeChk4']").prop("checked", false);
		<?php if(!get_option($theme_shortname."_member_agreement_nonmember_view_yn") || get_option($theme_shortname."_member_agreement_nonmember_view_yn")=='U'){?>
			jQuery("input:checkbox[id='agreeChk3']").prop("checked", false);
		<?php }?>
		}
	});

	jQuery(".btn-action-agree").click(function() {
		if(jQuery("#agreeChk1").is(":checked")==false) {
			alert("이용약관에 동의하셔야 합니다.");
			jQuery("#agreeChk1").focus();
			return; 
		}
		if(!jQuery("input[name='agreeChk4']").is(":checked")) {
			alert("개인정보 수집 및 이용에 대한 안내에 대한 안내에 동의하셔야 합니다.");
			jQuery("#agreeChk4").focus();
			return;
		}
<?php if(!get_option($theme_shortname."_member_agreement_nonmember_view_yn") || get_option($theme_shortname."_member_agreement_nonmember_view_yn")=='U'){?>
		if(!jQuery("input[name='agreeChk3']").is(":checked")) {
			alert("전자금융거래 이용약관에 동의하셔야 합니다.");
			jQuery("#agreeChk3").focus();
			return;
		}
<?php }?>
		jQuery("#agreeFrm").attr("action",common_var.home_url+"/?bbsePage=order").submit();
	});
});
</script>

	<hr />

	<div id="content">

        <?php
        #로케이션
        get_template_part('part/sub', 'location');

		if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData'] && !$V['goods_info'] && sizeof($V['gidx'])<='0'){
			$tGoodsInfo=base64_encode(serialize($V));
		}
		else $tGoodsInfo=$V['goods_info'];
        ?>

		<div class="page_cont"  id="bbsePage<?php echo $bbsePage?>">

			<h2 class="page_title"><?php echo ($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData'])?"소셜(간편)로그인":"비회원";?> 구매 동의</h2><br/>

			<form name="agreeFrm" id="agreeFrm" method="post" >
			<input type="hidden" name="goods_info" id="goods_info" value="<?php echo $tGoodsInfo; ?>">

		<?php for($c=0;$c<sizeof($V['gidx']);$c++){?>
			<input type="hidden" name="gidx[]" id="gidx[]" value="<?php echo $V['gidx'][$c]; ?>">
		<?php }?>
			<div class="article">
				<div class="bd_box">
					<div class="agreebox">
						<h3>이용약관</h3>
						<div class="req">
							<a href="<?php echo shortcode_url("theme_member_agreement", array("type"=>"agree"))?>" class="bb_more">자세히보기</a>
						</div>
						<div class="agree_page">
							<?php echo nl2br(stripslashes(get_option($theme_shortname."_member_agreement")))?>
						</div>
						<p class="chk_box">
							<label for="agreeChk1"><input type="checkbox" name="agreeChk1" id="agreeChk1" /> 위의 이용약관에 동의합니다.</label>
						</p>
					</div>
				</div>
			</div><br/>

			<div class="article">
				<div class="bd_box">
					<div class="agreebox">
						<h3>개인정보 수집 및 이용에 대한 안내</h3>
					<?php if(get_option($theme_shortname.'_memberpage_private_1')>'0'){?>
						<div class="req">
							<a href="<?php echo shortcode_url("theme_member_agreement", array("type"=>"private"))?>" class="bb_more">자세히보기</a>
						</div>
					<?php }?>
						<div class="agree_page">
							<?php echo nl2br(stripslashes(get_option($theme_shortname."_member_private_2"))); ?>
						</div>
						<p class="chk_box">
							<label for="agreeChk4"><input type="checkbox" name="agreeChk4" id="agreeChk4" /> 위의 개인정보 수집 및 이용에 대한 안내에 동의합니다.</label>
						</p>
					</div>
				</div>
			</div>
<?php if(!get_option($theme_shortname."_member_agreement_nonmember_view_yn") || get_option($theme_shortname."_member_agreement_nonmember_view_yn")=='U'){?>
			<br/>
			<div class="article">
				<div class="bd_box">
					<div class="agreebox">
						<h3>전자금융거래 이용약관</h3>
						<div class="req">
							<a href="<?php echo shortcode_url("theme_member_agreement", array("type"=>"order"))?>" class="bb_more">자세히보기</a>
						</div>
						<div class="agree_page">
							<?php echo nl2br(stripslashes(get_option($theme_shortname."_member_agreement_order")))?>
						</div>
						<p class="chk_box">
							<label for="agreeChk3"><input type="checkbox" name="agreeChk3" id="agreeChk3" /> 위의 전자금융거래 이용약관에 동의합니다.</label>
						</p>
					</div>
				</div>
			</div>
<?php }?>
			<br/>
			<p class="chk_box">
				<label for="agreeAll"><input type="checkbox" name="agreeAll" id="agreeAll" /> 위의 전체약관에 동의합니다.</label>
			</p>
			<br/>

			<div class="article agree_btn_area">
				<button type="button" class="bb_btn cus_fill w150 btn-action-agree"><strong class="big">확인</strong></button>
				<button type="button" class="bb_btn  w150 btn-action-cancel"><strong class="big">취소</strong></button>
			</div>
			</form>
		</div>

	</div><!--//#content -->



<?php get_footer();


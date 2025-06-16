<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
?>
<script type="text/javascript">
function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
}
function login_check(ajax_act){
	if(jQuery("#upass").val()){
		jQuery("#upass").val(b64EncodeUnicode(jQuery("#upass").val()))
	}

	jQuery.ajax({
		url: ajax_act,
		type: "post",
		dataType: "jsonp",
		//async: false,
		jsonp: "callback",
		//jsonpCallback: 'myCallback',
		data: jQuery('#login_frm').serialize(),
		crossDomain: true,
		beforeSend: function(){},
		complete: function(){},
		success: response_json,
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
		}
	});
}

function response_json(json){
	var list_cnt = json.length;
    var joinPage="<?php echo get_permalink($page_setting['join_page']);?>";
	if(list_cnt > 0){
		jQuery.each(json, function(index, entry){
			var res = entry['result'];
			if(res == "success"){
				if(jQuery("#goods_info").val() == "") {
					if(jQuery("#redirect_to").val() == "" || jQuery("#redirect_to").val() == joinPage) {
						window.location.href = common_var.home_url;
					}else{
						window.location.href = jQuery("#redirect_to").val();
					}
				}else{
					jQuery("#redirectFrm").attr("action",jQuery("#redirect_to").val()).submit();
				}
			}else if(res == "empty id"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 아이디를 입력해주세요.");
				jQuery("#uid").focus();
			}else if(res == "id_fail"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 아이디/비밀번호가 올바르지 않습니다.");
				jQuery("#uid").focus();
			}else if(res == "empty pass"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 비밀번호를 입력해주세요.");
				jQuery("#upass").focus();
			}else if(res == "pass_fail"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 아이디/비밀번호가 올바르지 않습니다.");
				jQuery("#upass").focus();
			}else if(res == "login_fail"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 로그인 오류입니다.");
				jQuery("#uid").focus();
			}
		});
	} 
}

window.onload = function(){document.login_frm.uid.focus();}
jQuery(function() {
	jQuery(".btn-action-join").click(function() {//회원가입 페이지 이동
		location.href="<?php if(!empty($page_setting['join_page'])){echo get_permalink($page_setting['join_page']);}?>";
	});
	jQuery(".btn-action-idfind").click(function() {//아이디 찾기 페이지 이동
		location.href="<?php if(!empty($page_setting['id_search_page'])){echo get_permalink($page_setting['id_search_page']);}?>";
	});
	jQuery(".btn-action-pwfind").click(function() {//비밀번호 찾기 페이지 이동
		location.href="<?php if(!empty($page_setting['pass_search_page'])){echo get_permalink($page_setting['pass_search_page']);}?>";
	});
	jQuery(".btn-guest-order").click(function () {//비회원 약관페이지 이동
		jQuery("#redirectFrm").attr("action",common_var.home_url+"/?bbsePage=order-agree").submit();
	});
});

function guest_login(act) {
	if(jQuery("input[name='order_name']").val() == "") {
		jQuery("#guest_error_box").show();
		jQuery("#guest_error_box").html("에러 : 주문자를 입력해주세요.");
		jQuery("input[name='order_name']").focus();
		return;
	}
	if(jQuery("input[name='order_no']").val() == "") {
		jQuery("#guest_error_box").show();
		jQuery("#guest_error_box").html("에러 : 주문번호를 입력해주세요.");
		jQuery("input[name='order_no']").focus();
		return;
	}

	jQuery.ajax({
		url: act,
		type: "post",
		dataType: "jsonp",
		//async: false,
		jsonp: "callback",
		data: jQuery('#guest_frm').serialize(),
		crossDomain: true,
		success: guest_order_check,
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
		}
	});
}

function guest_order_check(json){
	var list_cnt = json.length;
	if(list_cnt > 0){
		jQuery.each(json, function(index, entry){
			var res = entry['result'].split("|||");
			if(res['0'] == "success"){
				jQuery("#guest_frm").attr("action",res['1']).submit();
			}else if(res['0'] == "notExist"){
				jQuery("#guest_error_box").show();
				jQuery("#guest_error_box").html("에러 : 주문정보가 존재하지 않습니다.");
			}
		});
	}
}

</script>

<h2 class="page_title">로그인</h2>
<div class="page_concept">
	로그인하시면 다양하고 편리한 기능을 이용하실 수 있습니다.
</div>
<?php 
$guestOrder = $wpdb->get_var("select guest_order from bbse_commerce_membership_config");
$guestOrderView = $wpdb->get_var("select guest_order_view from bbse_commerce_membership_config");

if($output['bbseGoods'] > 0 && $_POST['tMode'] != ""){
?>
	<!-- 주문시 로그인/비회원구매 화면-->
	<div class="article">
		<div class="bd_box">
			<form method="post" name="login_frm" id="login_frm">
			<input type="hidden" name="redirect_to" id="redirect_to" value="<?php if(!empty($_GET['redirect_to'])) echo $_GET['redirect_to']; else echo wp_get_referer(); ?>" />
			<div class="bb_section<?php echo ($guestOrder!="Y")?" single":"";?>">
				<h3>회원 로그인</h3>
				<p>가입하신 아이디와 비밀번호를 입력해주세요.</p>
				<div class="bb_login_wrap">
					<div class="login_left">
						<label for="uid">아이디</label><input type="text" name="uid" id="uid" onfocus="this.value='';return true;" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}" tabindex="1"/>
						<label for="upass">비밀번호</label><input type="password" name="upass" id="upass" onfocus="this.value='';return true;" tabindex="2" onkeypress="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}if(event.keyCode == 13) login_check('<?php echo $action_url?>/proc/login.exec.php');"/>
					</div>
					<button type="button" class="bb_submit" tabindex="3" onclick="login_check('<?php echo $action_url?>/proc/login.exec.php');"><strong>로그인</strong></button>
				</div>
				<p class="btm_area">
<!-- 					<button type="button" class="bb_btn shadow btn-action-join"><strong class="mid c_point">회원가입</strong></button>
					<button type="button" class="bb_btn shadow btn-action-idfind"><span class="mid">아이디찾기</span></button>
					<button type="button" class="bb_btn shadow btn-action-pwfind"><span class="mid">비밀번호찾기</span></button> -->
				</p>
				<!-- alert  -->
				<p id="error_box" class="open_alert" style="display:none;"></p>
				<!--//alert  -->
			</div><!--//회원로그인 -->
			</form>
	<?php if($guestOrder=="Y"){?>
			<form method="post" name="guest_frm" id="guest_frm">
			<div class="bb_section">
				<h3>비회원 구매</h3>
				<p>비회원으로 상품을 구입이 가능합니다.</p>
				<div class="bb_login_wrap">
					<p class="n_mb_buy">
						<strong>비회원</strong>으로 구매하시겠습니까?
					</p>
					<button type="button" class="bb_submit btn-guest-order"><strong>비회원<br />구매</strong></button>
				</div>

				<div class="btm_area">
					<ul class="bb_dot_list">
						<li>비회원 주문시 사이트에서 제공하는 다양한 회원혜택에서<br />제외될 수 있습니다.</li>
					</ul>
				</div>
			</div><!--//비회원구매 -->
			</form>
	<?php }?>
		</div>
	</div>
	<form name="redirectFrm" id="redirectFrm" method="post">
	<input type="hidden" name="goods_info" id="goods_info" value="<?php echo $goods_info; ?>" />
	</form>
	<!-- //주문시 로그인/비회원구매 화면-->
<?php }else{?>
<!--  로그인 화면 -->
<div class="article">
	<div class="bd_box">
		<form method="post" name="login_frm" id="login_frm">
		<input type="hidden" name="redirect_to" id="redirect_to" value="<?php if(!empty($_GET['redirect_to'])) echo $_GET['redirect_to']; else echo wp_get_referer(); ?>" />
		<div class="bb_section<?php echo ($guestOrder!="Y" && $guestOrderView!='Y')?" single":"";?>">
			<h3>회원 로그인</h3>
			<p>가입하신 아이디와 비밀번호를 입력해주세요.</p>
			<div class="bb_login_wrap">
				<div class="login_left">
					<label for="uid">아이디</label><input type="text" name="uid" id="uid" onfocus="this.value='';return true;" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}" tabindex="1"/>
					<label for="upass">비밀번호</label><input type="password" name="upass" id="upass" onfocus="this.value='';return true;" tabindex="2" onkeypress="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}if(event.keyCode == 13) login_check('<?php echo $action_url?>/proc/login.exec.php');"/>
				</div>
				<button type="button" class="bb_submit" tabindex="3" onclick="login_check('<?php echo $action_url?>/proc/login.exec.php');"><strong>로그인</strong></button>
			</div>
			<p class="btm_area">
<!-- 				<button type="button" class="bb_btn shadow btn-action-join"><strong class="mid c_point">회원가입</strong></button>
				<button type="button" class="bb_btn shadow btn-action-idfind"><span class="mid">아이디찾기</span></button>
				<button type="button" class="bb_btn shadow btn-action-pwfind"><span class="mid">비밀번호찾기</span></button> -->
			</p>
			<!-- alert  -->
			<p id="error_box" class="open_alert" style="display:none;"></p>
			<!--//alert  -->
		</div><!--//회원로그인 -->
		</form>
	<?php if($guestOrder=="Y" || ($guestOrder=="N" && $guestOrderView=='Y')){?>
		<form method="post" name="guest_frm" id="guest_frm">
		<div class="bb_section">
			<h3>비회원 주문조회</h3>
			<p>비회원으로 상품을 구입하신 분은 아래 정보를 입력 해 주세요.</p>
			<div class="bb_login_wrap">
				<div class="login_left">
					<label for="order_name">주문자</label><input type="text" name="order_name" id="order_name" onfocus="this.value='';return true;" onkeydown="if(jQuery(this).val() != ''){jQuery('#guest_error_box').empty().hide();}" />
					<label for="order_no">주문번호</label><input type="text" name="order_no" id="order_no" onfocus="this.value='';return true;" onkeydown="if(jQuery(this).val() != ''){jQuery('#guest_error_box').empty().hide();}" />
				</div>
				<button type="button" class="bb_submit" onclick="guest_login('<?php echo $action_url?>/proc/order.check.php');"><strong>비회원<br />조회</strong></button>
			</div>
			<!-- alert  -->
			<p id="guest_error_box" class="open_alert" style="display:none;"></p>
			<!--//alert  -->
			<div class="btm_area">
				<ul class="bb_dot_list">
					<li>주문번호를 모르신다면, 고객센터로 문의해 주세요.</li>
				</ul>
			</div>
		</div><!--//비회원 주문조회-->
		</form>
	<?php }?>
	</div>
</div>
<form name="redirectFrm" id="redirectFrm" method="post">
<input type="hidden" name="goods_info" id="goods_info" value="<?php echo $goods_info; ?>" />
</form>
<!-- //로그인 화면 -->
<?php }?>

<?php
$socialHtml="";
$socialCnf=bbse_get_social_login_config();
$socialCnt=0;
if(sizeof($socialCnf)>'0' && ($socialCnf['naver']['naver_use_yn']=='Y' || $socialCnf['facebook']['facebook_use_yn']=='Y' || $socialCnf['google']['google_use_yn']=='Y' || $socialCnf['daum']['daum_use_yn']=='Y' || $socialCnf['kakao']['kakao_use_yn']=='Y' || $socialCnf['twitter']['twitter_use_yn']=='Y')){
	$socialHtml .="<ul style=\"margin:0 auto;margin-top:30px;text-align:center;\">";

	if($socialCnf['naver']['naver_use_yn']=='Y' && $socialCnf['naver']['naver_client_id'] && $socialCnf['naver']['naver_client_secret']){
		$socialHtml .="<li style=\"display:inline-block;position:relative;margin:0 10px;height:43px;\"><img src=\"".BBSE_THEME_WEB_URL."/images/social_login_naver.png\" onclick=\"socialLognPopup('naver',445,520);\" style=\"height:38px;width:auto;cursor:pointer;\" alt=\"네이버 아이디로 소셜(간편)로그인\"/></li>";
		$socialCnt++;
	}
	if($socialCnf['facebook']['facebook_use_yn']=='Y' && $socialCnf['facebook']['facebook_app_id'] && $socialCnf['facebook']['facebook_app_secret']){
		$socialHtml .="<li style=\"display:inline-block;position:relative;margin:0 10px;height:43px;\"><img src=\"".BBSE_THEME_WEB_URL."/images/social_login_facebook.png\" onclick=\"socialLognPopup('facebook',982,640);\" style=\"height:38px;width:auto;cursor:pointer;\" alt=\"페이스북 아이디로 소셜(간편)로그인\"/></li>";
		$socialCnt++;
	}
	if($socialCnf['google']['google_use_yn']=='Y' && $socialCnf['google']['google_client_id'] && $socialCnf['google']['google_client_secret']){
		$socialHtml .="<li style=\"display:inline-block;position:relative;margin:0 10px;height:43px;\"><img src=\"".BBSE_THEME_WEB_URL."/images/social_login_google.png\" onclick=\"socialLognPopup('google',445,520);\" style=\"height:38px;width:auto;cursor:pointer;\" alt=\"구글 아이디로 소셜(간편)로그인\"/></li>";
		$socialCnt++;
	}
	if($socialCnf['daum']['daum_use_yn']=='Y' && $socialCnf['daum']['daum_client_id'] && $socialCnf['daum']['daum_client_secret']){
		$socialHtml .="<li style=\"display:inline-block;position:relative;margin:0 10px;height:43px;\"><img src=\"".BBSE_THEME_WEB_URL."/images/social_login_daum.png\" onclick=\"socialLognPopup('daum',445,590);\" style=\"height:38px;width:auto;cursor:pointer;\" alt=\"다음 아이디로 소셜(간편)로그인\"/></li>";
		$socialCnt++;
	}
	if($socialCnf['kakao']['kakao_use_yn']=='Y' && $socialCnf['kakao']['kakao_rest_api_key']){
		$socialHtml .="<li style=\"display:inline-block;position:relative;margin:0 10px;height:43px;\"><img src=\"".BBSE_THEME_WEB_URL."/images/social_login_kakao.png\" onclick=\"socialLognPopup('kakao',464,615);\" style=\"height:38px;width:auto;cursor:pointer;\" alt=\"카카오 아이디로 소셜(간편)로그인\"/></li>";
		$socialCnt++;
	}
	if($socialCnf['twitter']['twitter_use_yn']=='Y' && $socialCnf['twitter']['twitter_api_key'] && $socialCnf['twitter']['twitter_api_secret']){
		$socialHtml .="<li style=\"display:inline-block;position:relative;margin:0 10px;height:43px;\"><img src=\"".BBSE_THEME_WEB_URL."/images/social_login_twitter.png\" onclick=\"socialLognPopup('twitter',460,520);\" style=\"height:38px;width:auto;cursor:pointer;\" alt=\"트위터 아이디로 소셜(간편)로그인\"/></li>";
		$socialCnt++;
	}

	if($socialCnt>1 && $socialCnt%2=='1') $socialHtml .="<li style=\"display:inline-block;position:relative;margin:0 10px;height:43px;width:177px;\">&nbsp;</li>";

	$socialHtml .="</ul>";
?>
<br />
<div class="article">
	<div class="bd_box">
		<form method="post" name="social_frm" id="social_frm">
		<input type="hidden" name="redirect_to" id="redirect_to" value="<?php if(!empty($_GET['redirect_to'])) echo $_GET['redirect_to']; else echo wp_get_referer(); ?>" />
		<div class="bb_section" style="width:100%;text-align:center;">
			<h3>소셜(간편) 로그인</h3>
			<p>회원가입 없이 간편하게 로그인 하실 수 있습니다.</p>
			<div class="bb_login_wrap" style="width:60%;margin:0 auto;min-height:20px;">
				<?php echo $socialHtml;?>
			</div>
		</div>
		</form>
	</div>
</div>
<?php
}
?>
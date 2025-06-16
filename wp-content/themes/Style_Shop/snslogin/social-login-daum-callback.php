<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $theme_shortname;
$pointColor = get_option($theme_shortname."_color_main_theme")?get_option($theme_shortname."_color_main_theme"):"#e22a40";

$snsConfig=bbse_get_social_login_config();

if($snsConfig['daum']['daum_use_yn'] != "Y"){
	echo "<script>alert('다음 소셜(간편) 로그인을 이용하실 수 없습니다.   ');self.close();</script>";
	exit;
}

 // 다음 정보
$dum_ClientID=$snsConfig['daum']['daum_client_id'];
$dum_ClientSecret=$snsConfig['daum']['daum_client_secret'];
$dum_RedirectURL=BBSE_THEME_WEB_URL."/snslogin/social-login-daum-callback.php";

$rtnFlag='ok';

require_once ('./class/daum-oauth.class.php');
$request = new DaumOAuthRequest( $dum_ClientID, $dum_ClientSecret, $dum_RedirectURL );
$request->get_accesstoken($_REQUEST['code']); // Access token 받기
$userInfo=$request->get_user_info(); // token을 이용한 사용자 정보 받아오기

if($userInfo['sns_id']){ // SESSION 저장
	$_SESSION['snsLogin']="Y";
	$_SESSION['snsLoginData']=serialize($userInfo);

	$checkData=bbse_put_social_login_log($userInfo);
}
else $rtnFlag="codeError";
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel='stylesheet' href='<?php bloginfo('template_url')?>/style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php bloginfo('template_url')?>/js/jquery-1.10.2.min.js'></script>
	<style>
		.bd_box .bb_section .bb_login_wrap .bb_submit {background-color:<?php echo $pointColor?>;}
		.page_cont {width:100%;margin-bottom:30px;padding:0;}
		.page_cont .page_title {width:100%;padding-top: 15px;height:35px;text-align:center;color:#ffffff;background-color:<?php echo $pointColor?>;font-size: 19px;}
		.socialLogin_msg {width:100%;background-color:#EEEEEE;line-height:20px;margin-bottom:40px;border-radius: 2em 0 2em 0;}
		.socialLogin_msg .socialMsg_title {border:2px solid <?php echo $pointColor?>;color:<?php echo $pointColor?>;margin:0 auto;margin-top:10px;margin-bottom:20px;font-size:15px;text-align:center;width:150px;height:25px;font-weight:700;padding-top:10px;}
		.socialLogin_msg .socialMsg_content {width:90%;margin:0 auto;padding-bottom:20px;font-size:13px;}
		.socialLogin_msg .socialLogin_ok {color:<?php echo $pointColor?>;font-weight:700;font-size:13px;}
		.bb_btn.cus_fill {
			border: 1px solid <?php echo $pointColor?> /*customer color */;
			background-color: <?php echo $pointColor?> /*customer color */;
		}
		.integration_yn {text-align:center;font-size:13px;}

		.bb_btn > .big {height:35px;width:150px;line-height:40px;}
		.socialLoginNext {margin-top:30px;width:100%;text-align:center;}
		.socialLoginNext .article {margin-top:30px;}
		.socialLoginNext .article .bd_box {padding:10px;text-align:left;line-height:25px;font-size:11px;}

		.integrationLogin {margin-top:30px;display:none;}
		.integrationLogin .article .bd_box {padding:20px 0 20px 0;}
		.bd_box .bb_section .open_alert {margin:15px 0 0 0;}
	</style>
	<script>
		jQuery( document ).ready(function() {
			window.resizeTo(480, 630); //460, 550
		});

		function check_integration(tType){
			if(tType=='Y'){
				jQuery(".socialLoginNext").hide();
				jQuery(".integrationLogin").show();
			}
			else{
				jQuery(".socialLoginNext").show();
				jQuery(".integrationLogin").hide();
			}
		}

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
			if(list_cnt > 0){
				jQuery.each(json, function(index, entry){
					var res = entry['result'];
					if(res == "success"){
						opener.response_json(json);
						self.close();
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

		function parent_redirect(tIntegrate){
			var joinPage="<?php echo get_permalink($page_setting['join_page']);?>";
			var home_url="<?php echo home_url();?>";
			if(tIntegrate=='N' && jQuery(opener.document).find("#goods_info").val()){
				jQuery(opener.document).find("#redirectFrm").attr("action",home_url+"/?bbsePage=order-agree").submit();
			}
			else{
				if(jQuery(opener.document).find("#goods_info").val() == "") {
					if(jQuery(opener.document).find("#redirect_to").val() == "" || jQuery(opener.document).find("#redirect_to").val() == joinPage) {
						opener.location.href = home_url;
					}else{
						opener.location.href = jQuery(opener.document).find("#redirect_to").val();
					}
				}else{
					jQuery(opener.document).find("#redirectFrm").attr("action",jQuery(opener.document).find("#redirect_to").val()).submit();
				}
			}

			self.close();
		}
	</script>
</head>
<body>
	<div style="width:100%;">
<?php 
if($checkData['view']=='N'){
	echo "<script>parent_redirect('".$checkData['integrate']."');</script>";
	exit;
}

if($rtnFlag=='ok'){ // 정상적인 소셜로그인
?>
		<div class="page_cont">
			<div class="page_title">소셜(간편) 로그인이 완료되었습니다.</div>
		</div>
		<div style="width:95%;margin:0 auto;">
			<div class="socialLogin_msg">
				<div style="height:5px;width:100%;"></div>
				<div class="socialMsg_title">
				기존 회원 통합
				</div>
				<div class="socialMsg_content">
					<?php if($checkData['dupCnt']<='0'){?>
						이미 회원이신가요?<br />
						회원이시라면 "예"를 선택하신 후 로그인을 진행해 주시고, 회원이 아니실 경우, "아니오" 선택 후 확인 버튼을 클릭해주세요.
					<?php }else{?>
						현재 로그인 하신 <span class="socialLogin_ok">[ 다음 ]</span>의 이메일 주소와 일치하는 회원정보가 존재합니다.<br />
						회원통합을 원하시면 "예", 회원통합을 원하지 않으시면 "아니오"를 선택하신 후 확인 버튼을 클릭해주세요.
					<?php }?>
				</div>
			</div>

			<form method="post" name="login_frm" id="login_frm">
				<input type="hidden" name="snsLogin" id="snsLogin" value='ok' />
				<div class="integration_yn">
					<input type="radio" name="socialLogin_Integration" onClick="check_integration('Y');" value="Y" />예&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="socialLogin_Integration" onClick="check_integration('N');" value="Y" /><span style="color:red;">아니요</span>
				</div>
				<div class="socialLoginNext">
					<button type="button" onclick="parent_redirect('N');" class="bb_btn cus_fill"><strong class="big">확인</strong></button>
					<div class="article">
						<div class="bd_box">
						* 회원통합 후 소셜(간편) 로그인 시 회원과 동일한 혜택이 제공되며, 더욱 다양하고 편리하게 이용하실 수 있습니다.
						</div>
					</div>
				</div>

				<div class="integrationLogin">
					<div class="article">
						<div class="bd_box">
							<div class="bb_section">
								<h3>회원 로그인</h3>
								<p>가입하신 아이디와 비밀번호를 입력해주세요.</p>
								<div class="bb_login_wrap">
									<div class="login_left">
										<label for="uid">아이디</label><input type="text" name="uid" id="uid" onfocus="this.value='';return true;" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}" tabindex="1">
										<label for="upass">비밀번호</label><input type="password" name="upass" id="upass" onfocus="this.value='';return true;" tabindex="2" onkeypress="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}if(event.keyCode == 13) login_check('<?php echo BBSE_THEME_WEB_URL;?>/proc/login.exec.php');">
									</div>
									<button type="button" class="bb_submit" tabindex="3" onclick="login_check('<?php echo BBSE_THEME_WEB_URL;?>/proc/login.exec.php');"><strong>로그인</strong></button>
								</div>
								<!-- alert  -->
								<p id="error_box" class="open_alert" style="display:none;"></p>
								<!--//alert  -->
							</div><!--//회원로그인 -->
						</div>
					</div>
				</div>
			</form>

				<?php
				/*
				echo "[Daum Callback Result]<br /><br /><pre>";
				print_r($userInfo);
				echo "</pre>";
				*/
				?>
		</div>
<?php
}
elseif($rtnFlag=='codeError'){ // 에러
	echo "<script>alert('다음 아이디로 로그인에 실패하였습니다.');self.close();</script>";
}
?>
	</div>
</body>
</html>

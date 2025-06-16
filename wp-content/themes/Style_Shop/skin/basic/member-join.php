<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname, $deviceType;
if(empty($rows->user_id)) $mode = "write";
else $mode = "edit";

if(!empty($rows->jumin)) $jumin_arr = explode("-", $rows->jumin);
if(!empty($rows->birth)) $birth_arr = explode("-", $rows->birth);
if(!empty($rows->phone)) $phone_arr = explode("-", $rows->phone);
if(!empty($rows->hp)) $hp_arr = explode("-", $rows->hp);
if(!empty($rows->zipcode)) $zipcode = explode("-",$rows->zipcode);
?>
<script type="text/javascript">
function zipcode_search(){
	window.open("<?php echo BBSE_COMMERCE_THEME_WEB_URL?>/zipcode.php", "zipcode_search", "width=400,height=400,scrollbars=yes");
}

function id_change(){
	var frm = document.join_frm;
	frm.id_checked.value = "";
	jQuery('#error_user_id').html("");
}

function id_check(tUrl){
	var id_check = document.join_frm.user_id.value;
	var Alpha = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	var Digit = "1234567890";
	var i;
	
	jQuery('#error_user_id').show();

	if(id_check == ""){
		jQuery('#error_user_id').html("에러 : 아이디를 입력해주세요.");
		document.join_frm.user_id.focus();
		return;
	}
	<?php if($config->id_min_len > 0){?>
	if(id_check.length < <?php echo $config->id_min_len?>){
		jQuery('#error_user_id').html("에러 : 아이디는 <?php echo $config->id_min_len?>자 이상으로 입력해주세요.");
		document.join_frm.user_id.focus();
		return false; 
	}
	<?php }?>
	if(id_check.length > 16){
		jQuery('#error_user_id').html("에러 : 아이디는 16자 이하로 입력해주세요.");
		document.join_frm.user_id.focus();
		return false; 
	}

	munja = Alpha + Digit;
	if(munja.length > 1){
		for(i = 0; i < id_check.length; i++){
			if(Alpha.indexOf(id_check.charAt(0)) == -1){
				jQuery('#error_user_id').html("에러 : 아이디의 첫글자는 영문자만 가능합니다.");
				document.join_frm.user_id.value = "";
				document.join_frm.user_id.focus();
				return;
			}
			if(munja.indexOf(id_check.charAt(i)) == -1){
				jQuery('#error_user_id').html("에러 : 영문자와 숫자만 가능합니다.");
				document.join_frm.user_id.value = "";
				document.join_frm.user_id.focus();
				return;
			}
		}
	}
	jQuery.ajax({
		type: "post",
		async: false,
		url: tUrl,
		data: "user_id=" + id_check,
		success: function(data){
			var result = data.split("|||");
			var response = result[0];  
	
			// 분기 처리
			if(response == "success"){
				if(result[1] == "y") jQuery('#id_checked').val("y");
				else jQuery('#id_checked').val("n");
				jQuery('#error_user_id').html(result[2]);
			}else if(response == "empty id"){  
				jQuery('#error_user_id').html("에러 : 아이디를 입력해주세요.");
			}else if(response == "short id"){  
				jQuery('#error_user_id').html("에러 : 아이디는 <?php echo $config->id_min_len?>자 이상으로 입력해주세요.");
			}else if(response == "long id"){  
				jQuery('#error_user_id').html("에러 : 아이디는 16자 이하로 입력해주세요.");
			}else if(response == "error id"){  
				jQuery('#error_user_id').html("에러 : 아이디는 영문 또는 숫자만 사용해주세요.");
			}else if(response == "join not id"){  
				jQuery('#error_user_id').html("에러 : 가입이 불가능한 아이디입니다.");
			}else{
				jQuery('#error_user_id').html("에러 : 서버와의 통신이 실패했습니다.");
			}
		},
		error: function(data, status, err){
			jQuery('#error_user_id').html("에러 : 서버와의 통신이 실패했습니다.");
		}
	});
}

function email_check(tUrl){
	var email_check = document.join_frm.email.value;
	var i;
	
	jQuery('#error_email').show();

	if(email_check == ""){
		jQuery('#error_email').html("에러 : 이메일을 입력해주세요.");
		document.join_frm.email.focus();
		return;
	}
	jQuery.ajax({
		type: "post",
		async: false,
		url: tUrl,
		data: "email=" + email_check,
		success: function(data){
			var result = data.split("|||");
			var response = result[0];  
			// 분기 처리
			if(response == "success"){
				if(result[1] == "y") jQuery('#email_checked').val("y");
				else jQuery('#email_checked').val("n");
				jQuery('#error_email').html(result[2]);
			}else if(response == "empty email"){  
				jQuery('#error_email').html("에러 : 이메일을 입력해주세요.");
			}else if(response == "error email"){  
				jQuery('#error_email').html("에러 : 올바른 이메일 주소를 입력해주세요.");
			}else if(response == "join not email"){  
				jQuery('#error_email').html("에러 : 가입이 불가능한 이메일입니다.");
			}else{
				jQuery('#error_email').html("에러 : 서버와의 통신이 실패했습니다.");
			}
		},
		error: function(data, status, err){
			jQuery('#error_email').html("에러 : 서버와의 통신이 실패했습니다.");
		}
	});
}

function success_msg(){
	jQuery("#success_box").show();
	jQuery("#success_box").html("정보를 정상적으로 저장하였습니다.");
	jQuery("input[type='password']").val("");
	jQuery("span[id^='error_']").hide();
	scroll(0, 0);
}

// 회원가입/수정
function write_check(check_url){
	jQuery.ajax({
		url: check_url,
		type: "post",
		dataType: "jsonp",
		jsonp: "callback",
		data: jQuery('#join_frm').serialize(),
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
			var data = entry['result'];
			var data_arr = data.split("|||");
			var res = data_arr[0];
			if(res == "success"){
				if(jQuery("#mode").val() == "write"){
					jQuery("#ok_info").val(data_arr[1]);
					jQuery("#success_frm").submit();
				}else{
					setTimeout(function(){success_msg()}, 100);
				}
			}else if(res == "exist email"){
				jQuery("#error_email").show();
				jQuery("#error_email").html("에러 : 입력하신 이메일주소는 이미 사용중입니다.");
				jQuery("span[id^='error_']:not(#error_email)").hide();
				jQuery("#success_box").hide();
				jQuery("#email").focus();
			}else if(res == "empty id"){
				jQuery("#error_user_id").show();
				jQuery("#error_user_id").html("에러 : 아이디를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "short id"){
				jQuery("#error_user_id").show();
				jQuery("#error_user_id").html("에러 : 아이디는 <?php echo $config->id_min_len?>자 이상으로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "long id"){
				jQuery("#error_user_id").show();
				jQuery("#error_user_id").html("에러 : 아이디는 16자 이하로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "exist id"){
				jQuery("#error_user_id").show();
				jQuery("#error_user_id").html("에러 : 입력하신 아이디는 이미 사용중인 아이디입니다.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "check id"){
				jQuery("#error_user_id").show();
				jQuery("#error_user_id").html("에러 : 아이디 중복확인을 해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "error id"){
				jQuery("#error_user_id").show();
				jQuery("#error_user_id").html("에러 : 아이디는 영문 또는 숫자만 사용해주세요.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "join not id"){
				jQuery("#error_user_id").show();
				jQuery("#error_user_id").html("에러 : 입력하신 아이디는 가입이 불가능한 아이디입니다.");
				jQuery("span[id^='error_']:not(#error_user_id)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_id").focus();
			}else if(res == "empty pass"){
				jQuery("#error_pass").show();
				jQuery("#error_pass").html("에러 : 비밀번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "short pass"){
				jQuery("#error_pass").show();
				jQuery("#error_pass").html("에러 : 비밀번호는 <?php echo $config->pass_min_len?>자 이상으로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "long pass"){
				jQuery("#error_pass").show();
				jQuery("#error_pass").html("에러 : 비밀번호는 16자 이하로 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "empty repass"){
				jQuery("#error_repass").show();
				jQuery("#error_repass").html("에러 : 비밀번호 확인을 위해 다시 한번더 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_repass)").hide();
				jQuery("#success_box").hide();
				jQuery("#repass").focus();
			}else if(res == "password mismatch"){
				jQuery("#error_pass").show();
				jQuery("#error_pass").html("에러 : 비밀번호가 일치하지 않습니다.");
				jQuery("span[id^='error_']:not(#error_pass)").hide();
				jQuery("#pass").val("");
				jQuery("#repass").val("");
				jQuery("#pass").focus();
				jQuery("#success_box").hide();
			}else if(res == "empty name"){
				jQuery("#error_name").show();
				jQuery("#error_name").html("에러 : 이름을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_name)").hide();
				jQuery("#success_box").hide();
				jQuery("#user_name").focus();
			}else if(res == "empty birth_year"){
				jQuery("#error_birth").show();
				jQuery("#error_birth").html("에러 : 생년월일(년)을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_year").focus();
			}else if(res == "incorrect birth_year"){
				jQuery("#error_birth").show();
				jQuery("#error_birth").html("에러 : 생년월일(년)을 바르게 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_year").focus();
			}else if(res == "empty birth_month"){
				jQuery("#error_birth").show();
				jQuery("#error_birth").html("에러 : 생년월일(월)을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_month").focus();
			}else if(res == "incorrect birth_month"){
				jQuery("#error_birth").show();
				jQuery("#error_birth").html("에러 : 생년월일(월)을 바르게 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_month").focus();
			}else if(res == "empty birth_day"){
				jQuery("#error_birth").show();
				jQuery("#error_birth").html("에러 : 생년월일(일)을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_day").focus();
			}else if(res == "incorrect birth_day"){
				jQuery("#error_birth").show();
				jQuery("#error_birth").html("에러 : 생년월일(일)을 바르게 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_birth)").hide();
				jQuery("#success_box").hide();
				jQuery("#birth_day").focus();
			}else if(res == "empty sex"){
				jQuery("#error_sex").show();
				jQuery("#error_sex").html("에러 : 성별을 선택해주세요.");
				jQuery("span[id^='error_']:not(#error_sex)").hide();
				jQuery("#success_box").hide();
				frm.sex[0].focus();
			}else if(res == "empty zipcode"){
				jQuery("#error_addr").show();
				jQuery("#error_addr").html("에러 : 우편번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_addr)").hide();
				jQuery("#success_box").hide();
				jQuery("#zipcode").focus();
			}else if(res == "empty addr1"){
				jQuery("#error_addr").show();
				jQuery("#error_addr").html("에러 : 주소를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_addr)").hide();
				jQuery("#success_box").hide();
				jQuery("#addr1").focus();
			}else if(res == "empty addr2"){
				jQuery("#error_addr").show();
				jQuery("#error_addr").html("에러 : 나머지주소를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_addr)").hide();
				jQuery("#success_box").hide();
				jQuery("#addr2").focus();
			}else if(res == "empty phone_1"){
				jQuery("#error_phone").show();
				jQuery("#error_phone").html("에러 : 전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_phone)").hide();
				jQuery("#success_box").hide();
				jQuery("#phone_1").focus();
			}else if(res == "empty phone_2"){
				jQuery("#error_phone").show();
				jQuery("#error_phone").html("에러 : 전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_phone)").hide();
				jQuery("#success_box").hide();
				jQuery("#phone_2").focus();
			}else if(res == "empty phone_3"){
				jQuery("#error_phone").show();
				jQuery("#error_phone").html("에러 : 전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_phone)").hide();
				jQuery("#success_box").hide();
				jQuery("#phone_3").focus();
			}else if(res == "empty hp_1"){
				jQuery("#error_hp").show();
				jQuery("#error_hp").html("에러 : 휴대전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_hp)").hide();
				jQuery("#success_box").hide();
				jQuery("#hp_1").focus();
			}else if(res == "empty hp_2"){
				jQuery("#error_hp").show();
				jQuery("#error_hp").html("에러 : 휴대전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_hp)").hide();
				jQuery("#success_box").hide();
				jQuery("#hp_2").focus();
			}else if(res == "empty hp_3"){
				jQuery("#error_hp").show();
				jQuery("#error_hp").html("에러 : 휴대전화번호를 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_hp)").hide();
				jQuery("#success_box").hide();
				jQuery("#hp_3").focus();
			}else if(res == "empty sms_reception"){
				jQuery("#error_sms_reception").show();
				jQuery("#error_sms_reception").html("에러 : SMS 수신여부를 선택해주세요.");
				jQuery("span[id^='error_']:not(#error_sms_reception)").hide();
				jQuery("#success_box").hide();
				frm.sms_reception[1].focus();
			}else if(res == "empty email"){
				jQuery("#error_email").show();
				jQuery("#error_email").html("에러 : 이메일을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_email)").hide();
				jQuery("#success_box").hide();
				jQuery("#email").focus();
			}else if(res == "not form email"){
				jQuery("#error_email").show();
				jQuery("#error_email").html("에러 : 이메일 형식이 올바르지 않습니다.");
				jQuery("span[id^='error_']:not(#error_email)").hide();
				jQuery("#success_box").hide();
				jQuery("#email").focus();
			}else if(res == "check email"){
				jQuery("#error_email").show();
				jQuery("#error_email").html("에러 : 이메일 중복확인을 해주세요.");
				jQuery("span[id^='error_']:not(#error_email)").hide();
				jQuery("#success_box").hide();
				jQuery("#email").focus();
			}else if(res == "empty job"){
				jQuery("#error_job").show();
				jQuery("#error_job").html("에러 : 직업을 입력해주세요.");
				jQuery("span[id^='error_']:not(#error_job)").hide();
				jQuery("#success_box").hide();
				jQuery("#job").focus();
			}else if(res == "empty agree_check1"){
				jQuery("#error_agree1").show();
				jQuery("#error_agree1").html("에러 : 이용약관에 동의해주세요.");
				jQuery("span[id^='error_']:not(#error_agree1)").hide();
				jQuery("#success_box").hide();
				jQuery("#agree_check1").focus();
			}else if(res == "empty agree_check2"){
				jQuery("#error_agree2").show();
				jQuery("#error_agree2").html("에러 : 개인정보 수집 및 이용에 대한 안내에 동의해주세요.");
				jQuery("span[id^='error_']:not(#error_agree2)").hide();
				jQuery("#success_box").hide();
				jQuery("#agree_check2").focus();
			}else if(res == "nonData"){
				alert("정상적인 접근이 아닙니다.");
				location.href = "<?php echo $curUrl?>";
			}
		});
	}
}
<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */?>
function openDaumPostcode(){
	new daum.Postcode({
		oncomplete: function(data){
			if(data.userSelectedType === 'R'){

                // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
                var extraRoadAddr = ''; // 도로명 조합형 주소 변수

                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    extraRoadAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if(data.buildingName !== '' && data.apartment === 'Y'){
                   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if(extraRoadAddr !== ''){
                    extraRoadAddr = ' (' + extraRoadAddr + ')';
                }
                // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
                if(fullRoadAddr !== ''){
                    fullRoadAddr += extraRoadAddr;
                }

				document.getElementById('zipcode').value = data.zonecode;
				document.getElementById('addr1').value = fullRoadAddr;
			}
			else{
				document.getElementById('zipcode').value = data.postcode1+"-"+data.postcode2;
				document.getElementById('addr1').value = data.jibunAddress;
			}
			document.getElementById('addr2').focus();
		}
	}).open();
}
<?php }?>
// 로그인 체크
function login_recheck(check_url){
	jQuery.ajax({
		url: check_url,
		type: "post",
		dataType: "jsonp",
		jsonp: "callback",
		data: jQuery('#join_frm').serialize(),
		crossDomain: true,
		success: login_check_json,
		error: function(data, status, err){
			var errorMessage = err || data.statusText;
			alert(errorMessage);
		}
	});
}
function login_check_json(json){
	var list_cnt = json.length;
	if(list_cnt > 0){
		jQuery.each(json, function(index, entry){
			var res = entry['result'];
			if(res == "success"){
				jQuery("#login_check").val(res);
				jQuery("#join_frm").submit();
			}else if(res == "empty pass"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 비밀번호를 입력해주세요.");
				jQuery("#upass").focus();
			}else if(res == "pass_fail"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 비밀번호가 올바르지 않습니다.");
				jQuery("#upass").focus();
			}else if(res == "login_fail"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 로그인 오류입니다.");
				jQuery("#uid").focus();
			}
		});
	}
}

jQuery(document).ready(function(){
	jQuery("span[id^='error_']").hide();
	jQuery(".btn-action-agree").click(function() {
		if(!jQuery("input[name='agreeChk1']").is(":checked")) {
			alert("이용약관에 동의하셔야 합니다.");
			jQuery("#agreeChk1").focus();
			return;
		}
		if(!jQuery("input[name='agreeChk2']").is(":checked")) {
			alert("개인정보 수집 및 이용에 대한 안내에 동의하셔야 합니다.");
			jQuery("#agreeChk2").focus();
			return;
		}
<?php if(!get_option($theme_shortname."_member_agreement_order_view_yn") || get_option($theme_shortname."_member_agreement_order_view_yn")=='U'){?>
		if(!jQuery("input[name='agreeChk3']").is(":checked")) {
			alert("전자금융거래 이용약관에 동의하셔야 합니다.");
			jQuery("#agreeChk3").focus();
			return;
		}
<?php }?>
		jQuery("#join_frm").attr("action","<?php echo get_permalink($page_setting['join_page'])?>");
		jQuery("#join_frm").submit();
	});

	jQuery(".btn-action-auth").click(function() {
		window.name ="Parent_window";
		window.open('', 'BBSeCommerceAuth', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
		document.authCheckFrm.action = "https://nice.checkplus.co.kr/CheckPlusSafeModel/checkplus.cb";
		document.authCheckFrm.target = "BBSeCommerceAuth";
		document.authCheckFrm.submit();
	});

	jQuery('#upass').bind("keyup keypress", function(e) {
		var code = e.keyCode || e.which;
		if (code  == 13) {
			jQuery(".pass-find-btn").click();
			e.preventDefault();
			return false;
		}
	});

	jQuery("#agreeAll").click(function() {
		if(jQuery("input:checkbox[name='agreeAll']").is(":checked") == true){
			jQuery("input:checkbox[name='agreeChk1']").prop("checked", true);
			jQuery("input:checkbox[name='agreeChk2']").prop("checked", true);
		<?php if(!get_option($theme_shortname."_member_agreement_order_view_yn") || get_option($theme_shortname."_member_agreement_order_view_yn")=='U'){?>
			jQuery("input:checkbox[name='agreeChk3']").prop("checked", true);
		<?php }?>
		}
		else{
			jQuery("input:checkbox[name='agreeChk1']").prop("checked", false);
			jQuery("input:checkbox[name='agreeChk2']").prop("checked", false);
		<?php if(!get_option($theme_shortname."_member_agreement_order_view_yn") || get_option($theme_shortname."_member_agreement_order_view_yn")=='U'){?>
			jQuery("input:checkbox[name='agreeChk3']").prop("checked", false);
		<?php }?>
		}
	});
});
</script>
<?php 
if($config->table_align == "C") $table_align = "margin:0 auto;";
else if($config->table_align == "L") $table_align = "float:left;";
else if($config->table_align == "R") $table_align = "float:right;";
$step = (!$_POST['step'])?1:$_POST['step'];
?>
<?php if(empty($_POST['success_mode'])){?>
<form method="post" name="join_frm" id="join_frm">
<input type="hidden" name="mode" id="mode" value="<?php echo $mode?>" />
<input type="hidden" name="user_no" id="user_no" value="<?php echo $rows->user_no?>" />
<input type="hidden" name="id_checked" id="id_checked" value="" />
<input type="hidden" name="email_checked" id="email_checked" value="" />
<input type="hidden" name="skin" id="skin" value="<?php echo $skin?>" />
<?php
	if(!is_user_logged_in()){
?>
<h2 class="page_title">회원가입</h2>
<div class="page_concept" style="width:<?php echo $config->table_width?>%;<?php echo $table_align?>">
	회원가입을 환영합니다.
	<ol class="bb_join_step">
		<li<?php if($step=="1"){?> class="active"<?php }?>>01 STEP1</li>
		<li<?php if($step=="2"){?> class="active"<?php }?>>02 STEP2</li>
		<li<?php if($step=="3"){?> class="active"<?php }?>>03 STEP3</li>
		<li<?php if($step=="4"){?> class="active"<?php }?>>04 STEP4</li>
	</ol>
</div>
<?php
	}else if($_POST['login_check']!="success"){
?>
<input type="hidden" name="step" id="step" value="3" />
<input type="hidden" name="login_check" id="login_check" value="" />
<h2 class="page_title">회원정보변경</h2>
<div class="article">
	<ul class="bb_dot_list">
		<li style="line-height:40px;margin-top:15px;">고객님의 소중한 개인정보를 보호하기 위해 비밀번호를 다시 한번 확인합니다.</li>
	</ul>
</div>
<div class="article">
	<div class="bd_box">
		<div class="re_password">
			<h3>비밀번호 재확인</h3>
			<p>비밀번호 입력시 노출되지 않도록 주의하시기 바랍니다.</p>
			<div class="re_pwd">
				<dl>
					<dt>아이디</dt>
					<dd><strong><?php echo $rows->user_id?></strong></dd>
					<dt><label for="upass">비밀번호</label></dt>
					<dd><input type="password" name="upass" id="upass" size="20" onkeypress="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}"/></dd>
				</dl>
				<br />
				<p id="error_box" class="open_alert" style="display:none;"></p>
			</div>
		</div>
	</div>
</div>
<br />
<div class="article agree_btn_area">
	<button type="button" class="bb_btn cus_fill w150 pass-find-btn" onclick="login_recheck('<?php echo $action_url?>/proc/login.recheck.exec.php');"><strong class="big">확인</strong></button>
	<button type="button" class="bb_btn w150" onclick="history.back();"><strong class="big">취소</strong></button>
</div>
<?php
	}
?>
<?php if($step=="1" && !is_user_logged_in()){?>
<?php if($config->certification_yn == "Y") {?>
<input type="hidden" name="step" id="step" value="2" />
<?php }else{?>
<input type="hidden" name="step" id="step" value="3" />
<?php }?>
<div class="article">
	<div class="bd_box">
		<div class="agreebox">
			<h3>이용약관</h3>
		<?php if(get_option($theme_shortname.'_memberpage_agreement')>'0'){?>
			<div class="req">
				<a href="<?php echo get_permalink(get_option($theme_shortname.'_memberpage_agreement'))?>" class="bb_more">자세히보기</a>
			</div>
		<?php }?>
			<div class="agree_page"><?php echo nl2br(stripslashes(get_option($theme_shortname."_member_agreement")))?></div>
			<p class="chk_box">
				<label for="agreeChk1"><input type="checkbox" name="agreeChk1" id="agreeChk1" /> 위의 이용약관에 동의합니다.</label>
			</p>
		</div>
	</div>
</div>
<br />
<div class="article">
	<div class="bd_box">
		<div class="agreebox">
			<h3>개인정보 수집 및 이용에 대한 안내</h3>
		<?php if(get_option($theme_shortname.'_memberpage_private_1')>'0'){?>
			<div class="req">
				<a href="<?php echo get_permalink(get_option($theme_shortname.'_memberpage_private_1'))?>" class="bb_more">자세히보기</a>
			</div>
		<?php }?>
			<div class="agree_page">
				<?php 
				if(get_option($theme_shortname."_member_private_2")){
					echo nl2br(stripslashes(get_option($theme_shortname."_member_private_2")));
				}
				else{
					echo nl2br(stripslashes(get_option($theme_shortname."_member_private_1")));
				}
				?>
			</div>
			<p class="chk_box">
				<label for="agreeChk2"><input type="checkbox" name="agreeChk2" id="agreeChk2" /> 위의 개인정보 수집 및 이용에 대한 안내에 동의합니다.</label>
			</p>
		</div>
	</div>
</div>
<?php if(!get_option($theme_shortname."_member_agreement_order_view_yn") || get_option($theme_shortname."_member_agreement_order_view_yn")=='U'){?>
<br />
<div class="article">
	<div class="bd_box">
		<div class="agreebox">
			<h3>전자금융거래 이용약관</h3>
		<?php if(get_option($theme_shortname.'_memberpage_agreement_order')>'0'){?>
			<div class="req">
				<a href="<?php echo get_permalink(get_option($theme_shortname.'_memberpage_agreement_order'))?>" class="bb_more">자세히보기</a>
			</div>
		<?php }?>
			<div class="agree_page"><?php echo nl2br(stripslashes(get_option($theme_shortname."_member_agreement_order")))?></div>
			<p class="chk_box">
				<label for="agreeChk3"><input type="checkbox" name="agreeChk3" id="agreeChk3" /> 위의 전자금융거래 이용약관에 동의합니다.</label>
			</p>
		</div>
	</div>
</div>
<?php }?>
<br />
<p class="chk_box">
	<label id="agreeAllLabel" for="agreeAll"><input type="checkbox" name="agreeAll" id="agreeAll" /> 위의 전체약관에 동의합니다.</label>
</p>
<br />
<div class="article agree_btn_area">
	<button type="button" class="bb_btn cus_fill w150 btn-action-agree"><strong class="big">회원가입</strong></button>
</div>

<?php }else if($step=="2" && !is_user_logged_in()){?>
<?php
    //**************************************************************************************************************
    //NICE평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED
    
    //서비스명 :  체크플러스 - 안심본인인증 서비스
    //페이지명 :  체크플러스 - 메인 호출 페이지
    
    //보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 
    //**************************************************************************************************************
    $sitecode = $config->certification_id;				// NICE로부터 부여받은 사이트 코드
    $sitepasswd = $config->certification_pass;			// NICE로부터 부여받은 사이트 패스워드
    $cb_encode_path = BBSE_COMMERCE_THEME_ABS_PATH."/auth/CheckPlusSafe/CPClient";		// NICE로부터 받은 암호화 프로그램의 위치 (절대경로+모듈명)
    $authtype = "M";      	// 없으면 기본 선택화면, X: 공인인증서, M: 핸드폰, C: 카드
	$popgubun 	= "N";		//Y : 취소버튼 있음 / N : 취소버튼 없음
	if($deviceType == "mobile") {
		$customize 	= "Mobile";			//없으면 기본 웹페이지 / Mobile : 모바일페이지
	}else{
		$customize 	= "";			//없으면 기본 웹페이지 / Mobile : 모바일페이지
	}

    if(extension_loaded('CPClient')) {
		$reqseq = get_cprequest_no($sitecode); // => CPClint.so 모듈 
	}else{
		$reqseq = `$cb_encode_path SEQ $sitecode`;// 요청 번호, 이는 성공/실패후에 같은 값으로 되돌려줌 => CPClient 모듈
	}
    
    // CheckPlus(본인인증) 처리 후, 결과 데이타를 리턴 받기위해 다음예제와 같이 http부터 입력합니다.
	$returnurl = BBSE_COMMERCE_THEME_WEB_URL."/auth/CheckPlusSafe/checkplus_success.php";	// 성공시 이동될 URL
    $errorurl = BBSE_COMMERCE_THEME_WEB_URL."/auth/CheckPlusSafe/checkplus_fail.php";		// 실패시 이동될 URL
	
    // reqseq값은 성공페이지로 갈 경우 검증을 위하여 세션에 담아둔다.
    
    $_SESSION["REQ_SEQ"] = $reqseq;

    // 입력될 plain 데이타를 만든다.
    $plaindata =  "7:REQ_SEQ" . strlen($reqseq) . ":" . $reqseq .
			    			  "8:SITECODE" . strlen($sitecode) . ":" . $sitecode .
			    			  "9:AUTH_TYPE" . strlen($authtype) . ":". $authtype .
			    			  "7:RTN_URL" . strlen($returnurl) . ":" . $returnurl .
			    			  "7:ERR_URL" . strlen($errorurl) . ":" . $errorurl .
			    			  "11:POPUP_GUBUN" . strlen($popgubun) . ":" . $popgubun .
			    			  "9:CUSTOMIZE" . strlen($customize) . ":" . $customize ;
    
	if(extension_loaded('CPClient')) {
		$enc_data = get_encode_data($sitecode, $sitepasswd, $plaindata);
	}else{
		$enc_data = `$cb_encode_path ENC $sitecode $sitepasswd $plaindata`;
	}

    if( $enc_data == -1 )
    {
        $returnMsg = "암/복호화 시스템 오류입니다.";
        $enc_data = "";
    }
    else if( $enc_data== -2 )
    {
        $returnMsg = "암호화 처리 오류입니다.";
        $enc_data = "";
    }
    else if( $enc_data== -3 )
    {
        $returnMsg = "암호화 데이터 오류 입니다.";
        $enc_data = "";
    }
    else if( $enc_data== -9 )
    {
        $returnMsg = "입력값 오류 입니다.";
        $enc_data = "";
    }
?>
<input type="hidden" name="step" id="step" value="3" />
<input type="hidden" name="auth_result" id="auth_result" value="" />
<div class="article">
	<div class="bd_box">
		<div class="bb_confirm single">
			<h3>본인인증</h3>
			<p>원하시는 인증방법을 선택하시고 안전한 회원가입을 진행해주세요.</p>
			<ul>
				<!-- <li>
					<div class="confirm_icon ic_ipin">
						아이핀(i-PIN)으로 본인 인증
						<button class="bb_btn shadow"><strong class="mid">아이핀 인증</strong></button>
					</div>
				</li> -->
				<li>
					<div class="confirm_icon ic_phone">
						휴대폰으로 본인 인증
						<button type="button" class="bb_btn shadow btn-action-auth"><strong class="mid">휴대폰 인증</strong></button>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>


<?php }else if($step=="3" || ($_POST['login_check']=="success" && is_user_logged_in())){?>
<?php
if($mode == "write" && $config->certification_yn == "Y" && $_POST['auth_result']=="") {
	echo "<script>alert('잘못된 접근입니다.');history.back();</script>";
	exit;
}
?>
<input type="hidden" name="step" id="step" value="4" />
<input type="hidden" name="auth_result" id="auth_result" value="<?php echo $_POST['auth_result']?>" />
<div class="article">
	<div id="success_box" style="font-size:15px;font-weight:bold;padding:10px 0 10px 0;color:#7ba8ea;display:none;"></div>
	<div class="bd_box">
		<div class="bb_join">
			<?php 
				if($mode == "write") $member_tit = "입력";
				else $member_tit = "변경";

				if($mode == "write" && $config->certification_yn == "Y") {// 본인인증 사용시 처리
					$authResult = unserialize(base64_decode($_POST['auth_result']));
				}
			?>
			<h3>회원정보<?php echo $member_tit?></h3>
			<p><?php echo ($mode == "write")?"회원가입을 위해 아래의 양식에 있는 내용을 입력해 주세요.":"변경할 정보를 입력해 주세요."?></p>
			<p class="att_desc">
				<em>*</em> 은 필수입력 항목입니다.
			</p>
			<div class="tb_wt">
				<table>
					<caption>회원정보입력의 표</caption>
					<colgroup>
						<col style="width:20%;">
						<col style="width:auto;">
					</colgroup>
					<tbody>
						<tr>
							<th scope="row"><label for="user_id">아이디</label> <em>*</em></th>
							<td>
							<div>아이디 <em>*</em></div>
							<?php if(empty($rows->user_id)){?>
								<input type="text" name="user_id" id="user_id" size="20" onkeydown="id_change();" maxlength="16"/>
								<button type="button" class="bb_btn shadow" onclick="id_check('<?php echo BBSE_COMMERCE_THEME_WEB_URL?>/proc/id.check.php');"><em class="mid">아이디중복체크</em></button>
								<div id="error_user_id" class="open_alert"></div>
							<?php }else{?>
							<?php echo $rows->user_id?>
							<?php }?>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pass">비밀번호</label> <em>*</em></th>
							<td>
								<div>비밀번호<em>*</em></div>
								<input type="password" name="pass" id="pass" size="20" maxlength="16" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_pass').empty().hide();}"/> <?php if(!empty($rows->user_id)){?>변경시에만 입력<?php }?>
								<div id="error_pass" class="open_alert"></div>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="repass">비밀번호 확인</label> <em>*</em></th>
							<td>
								<div>비밀번호 확인<em>*</em></div>
								<input type="password" name="repass" id="repass" size="20" maxlength="16" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_repass').empty().hide();}"/>
								<div id="error_repass" class="open_alert"></div>
							</td>
						</tr>
						<?php if($config->use_name == "1"){?>
						<tr>
							<th scope="row"><label for="user_name">이름</label> <?php if($config->validate_name == 1){?><em>*</em><?php }?></th>
							<td>
								<div>이름<em>*</em></div>
								<?php if($mode == "write" && $config->certification_yn == "Y") {?>
								<input type="text" name="user_name" id="user_name" size="20" value="<?php echo $authResult['NAME']?>" readonly/>
								<?php }else{?>
								<input type="text" name="user_name" id="user_name" size="20" value="<?php if(!empty($rows->name)) echo $rows->name?>" maxlength="20" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_name').empty().hide();}"/>
								<?php }?>
								<div id="error_name" class="open_alert"></div>
							</td>
						</tr>
						<?php }?>
						<tr>
							<th scope="row"><label for="email">이메일</label> <em>*</em></th>
							<td>
								<div>이메일<em>*</em></div>
								<input type="text" name="email" id="email" size="40" value="<?php if(!empty($rows->email)) echo $rows->email?>" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_email').empty().hide();}"/>
								<button type="button" class="bb_btn shadow" onclick="email_check('<?php echo BBSE_COMMERCE_THEME_WEB_URL?>/proc/email.check.php');"><em class="mid">이메일 중복체크</em></button>
								<label for="email_reception"><input type="checkbox" name="email_reception" id="email_reception" value="1" <?php if($rows->email_reception=="1") echo "checked";?>/>정보메일 수신동의</label>
								<div id="error_email" class="open_alert"></div>
							</td>
						</tr>
						<?php if($config->use_birth == "1"){?>
						<tr>
							<th scope="row"><label for="birth_year">생년월일</label> <?php if($config->validate_birth == 1){?><em>*</em><?php }?></th>
							<td>
								<div>생년월일<em>*</em></div>
								<?php if($mode == "write" && $config->certification_yn == "Y") {?>
								<input type="text" name="birth_year" id="birth_year" value="<?php echo substr($authResult['BIRTHDATE'],0,4)?>" style="min-width:70px;width:10%;" class="" readonly/> 년 
								<input type="text" name="birth_month" id="birth_month" value="<?php echo substr($authResult['BIRTHDATE'],4,2)?>" style="min-width:70px;width:10%;" class="" readonly/> 월
								<input type="text" name="birth_day" id="birth_day" value="<?php echo substr($authResult['BIRTHDATE'],6,2)?>" style="min-width:70px;width:10%;" class="" readonly/> 일
								<?php }else{?>
								<input type="text" name="birth_year" id="birth_year" value="<?php if(!empty($birth_arr[0])) echo $birth_arr[0]?>" maxlength="4" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_birth').empty().hide();}" style="min-width:70px;width:10%;" class="" /> 년 
								<input type="text" name="birth_month" id="birth_month" value="<?php if(!empty($birth_arr[1])) echo $birth_arr[1]?>" maxlength="2" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_birth').empty().hide();}" style="min-width:70px;width:10%;" class="" /> 월
								<input type="text" name="birth_day" id="birth_day" value="<?php if(!empty($birth_arr[2])) echo $birth_arr[2]?>" maxlength="2" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_birth').empty().hide();}" style="min-width:70px;width:10%;" class="" /> 일
								<?php }?>
								<div id="error_birth" class="open_alert"></div>
							</td>
						</tr>
						<?php }?>
						<?php if($config->use_sex == "1"){?>
						<tr>
							<th scope="row"><label for="sex">성별</label> <?php if($config->validate_sex == 1){?><em>*</em><?php }?></th>
							<td>
								<div>성별<em>*</em></div>
								<?php if($mode == "write" && $config->certification_yn == "Y") {?>
								<input type="hidden" name="sex" value="<?php echo ($authResult['GENDER']=="0")?"2":"1"?>">
								<?php echo ($authResult['GENDER']=="0")?"여자":"남자"?>
								<?php }else{?>
								<input type="radio" name="sex" value="1"<?php if(empty($rows->sex) || $rows->sex == "1") echo " checked";?> /> 남자 &nbsp;
								<input type="radio" name="sex" value="2"<?php if(!empty($rows->sex) && $rows->sex == "2") echo " checked";?> /> 여자
								<?php }?>
								<div id="error_sex" class="open_alert"></div>
							</td>
						</tr>
						<?php }?>
						<?php if($config->use_phone == "1"){?>
						<tr>
							<th scope="row"><label for="phone_1">전화번호</label> <?php if($config->validate_phone == 1){?><em>*</em><?php }?></th>
							<td>
								<div>전화번호<em>*</em></div>
								<input type="text" name="phone_1" id="phone_1" title="전화번호 국번을 선택해주세요." size="5" value="<?php if(!empty($phone_arr[0])) echo $phone_arr[0]?>" maxlength="4" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_phone').empty().hide();}">
								-
								<input type="text" name="phone_2" id="phone_2" title="전화번호 중간자리를 입력해주세요." size="5" value="<?php if(!empty($phone_arr[1])) echo $phone_arr[1]?>" maxlength="4" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_phone').empty().hide();}"/>
								-
								<input type="text" name="phone_3" id="phone_3" title="전화번호 마지막자리를 입력해주세요." size="5" value="<?php if(!empty($phone_arr[2])) echo $phone_arr[2]?>" maxlength="4" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_phone').empty().hide();}"/>
								<div id="error_phone" class="open_alert"></div>
							</td>
						</tr>
						<?php }?>
						<?php if($config->use_hp == "1"){?>
						<tr>
							<th scope="row"><label for="hp_1">휴대전화번호</label> <?php if($config->validate_hp == 1){?><em>*</em><?php }?></th>
							<td>
								<div>휴대전화번호<em>*</em></div>
								<?php 
								if($mode == "write" && $config->certification_yn == "Y") {
									if(strlen($authResult['MOBILE_NO']) == 11) {
										$hp_arr[0] = substr($authResult['MOBILE_NO'], 0,3);
										$hp_arr[1] = substr($authResult['MOBILE_NO'], 3,4);
										$hp_arr[2] = substr($authResult['MOBILE_NO'], 7,4);
									}else if(strlen($authResult['MOBILE_NO']) == 10) {
										$hp_arr[0] = substr($authResult['MOBILE_NO'], 0,3);
										$hp_arr[1] = substr($authResult['MOBILE_NO'], 3,3);
										$hp_arr[2] = substr($authResult['MOBILE_NO'], 6,4);
									}
								?>
								<input type="text" name="hp_1" id="hp_1" title="휴대전화번호 국번을 선택해주세요." size="5" value="<?php if(!empty($hp_arr[0])) echo $hp_arr[0]?>" <?php if(!empty($hp_arr[0])) echo "readonly";?>/> -
								<input type="text" name="hp_2" id="hp_2" title="휴대전화번호 중간자리를 입력해주세요." size="5" value="<?php if(!empty($hp_arr[1])) echo $hp_arr[1]?>" <?php if(!empty($hp_arr[1])) echo "readonly";?>/> -
								<input type="text" name="hp_3" id="hp_3" title="휴대전화번호 마지막자리를 입력해주세요." size="5" value="<?php if(!empty($hp_arr[2])) echo $hp_arr[2]?>" <?php if(!empty($hp_arr[2])) echo "readonly";?>/>
								<?php }else{?>
								<input type="text" name="hp_1" id="hp_1" title="휴대전화번호 국번을 선택해주세요." size="5" value="<?php if(!empty($hp_arr[0])) echo $hp_arr[0]?>" maxlength="4" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_hp').empty().hide();}"/> -
								<input type="text" name="hp_2" id="hp_2" title="휴대전화번호 중간자리를 입력해주세요." size="5" value="<?php if(!empty($hp_arr[1])) echo $hp_arr[1]?>" maxlength="4" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_hp').empty().hide();}"/> -
								<input type="text" name="hp_3" id="hp_3" title="휴대전화번호 마지막자리를 입력해주세요." size="5" value="<?php if(!empty($hp_arr[2])) echo $hp_arr[2]?>" maxlength="4" onkeydown="checkForNumber();if(jQuery(this).val() != ''){jQuery('#error_hp').empty().hide();}"/>
								<?php }?>
								<label for="sms_reception"><input type="checkbox" name="sms_reception" id="sms_reception" value="1" <?php if($rows->sms_reception=="1") echo "checked";?>/>정보문자 수신동의</label>
								<div id="error_hp" class="open_alert"></div>
							</td>
						</tr>
						<?php }?>
						<?php if($config->use_job == "1"){?>
						<tr>
							<th scope="row"><label for="job">직업</label> <?php if($config->validate_job == 1){?><em>*</em><?php }?></th>
							<td>
								<div>직업<em>*</em></div>
								<input type="text" name="job" id="job" value="<?php if(!empty($rows->job)) echo $rows->job?>" class="nor" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_job').empty().hide();}" />
								<div id="error_job" class="open_alert"></div>
							</td>
						</tr>
						<?php }?>
						<?php if($config->use_addr == "1"){?>
						<tr>
							<th scope="row"><label for="addr1">주소</label> <?php if($config->validate_addr == 1){?><em>*</em><?php }?></th>
							<td>
								<div>주소<em>*</em></div>
								<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */?>
								<input type="text" name="zipcode" id="zipcode" title="우편번호 첫자리를 입력해주세요." value="<?php if(!empty($rows->zipcode)) echo $rows->zipcode;?>" style="width:70px;text-align:center;" readonly/>
								<button type="button" class="bb_btn shadow" onclick="openDaumPostcode()"><em class="mid">우편번호 찾기</em></button>
								<p>
									<input type="text" name="addr1" id="addr1" title="주소를 입력해주세요." size="30" value="<?php if(!empty($rows->addr1)) echo $rows->addr1?>" readonly/>
									<input type="text" name="addr2" id="addr2" title="나머지 주소를 입력해주세요." size="30" value="<?php if(!empty($rows->addr2)) echo $rows->addr2?>" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_addr').empty().hide();}"/>
								</p>
								<?php }else{?>
								<input type="text" name="zipcode1" id="zipcode1" title="우편번호 첫자리를 입력해주세요." size="5" value="<?php if(!empty($rows->zipcode)) echo $zipcode[0]?>" readonly/>
								-
								<input type="text" name="zipcode2" id="zipcode2" title="우편번호 마지막자리를 입력해주세요." size="5" value="<?php if(!empty($rows->zipcode)) echo $zipcode[1]?>" readonly/>
								<button type="button" class="bb_btn shadow" onclick="zipcode_search();"><em class="mid">우편번호 찾기</em></button>
								<p>
									<input type="text" name="addr1" id="addr1" title="주소를 입력해주세요." size="30" value="<?php if(!empty($rows->addr1)) echo $rows->addr1?>" readonly/>
									<input type="text" name="addr2" id="addr2" title="나머지 주소를 입력해주세요." size="30" value="<?php if(!empty($rows->addr2)) echo $rows->addr2?>" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_addr').empty().hide();}"/>
								</p>
								<?php }?>
								<div id="error_addr" class="open_alert"></div>
							</td>
						</tr>
						<?php }?>
					</tbody>
				</table>
			</div><!--//.tb_wt -->
			<br />
			<ul class="bb_dot_list">
				<li>정확한 정보를 입력하지 않으면, 홈페이지 서비스 이용이 제한 될 수 있습니다.</li>
				<li>회원정보는 개인정보처리방침에 따라 보호되며 동의 없이 공개 또는 제 3자에게 제공되지 않습니다.</li>
			</ul>
		</div><!--//.bb_join -->
	</div>
</div>
<br />
<!--// 비회원구매 끝 -->
<div class="article agree_btn_area">
	<button type="button" class="bb_btn cus_fill w150" onclick="write_check('<?php echo $action_url?>/proc/join.exec.php');"><strong class="big"><?php if($mode == "write") echo "가입완료"; else if($mode == "edit") echo "회원정보수정";?></strong></button>
</div>
<?php }?>
<?php
}else if($_POST['success_mode'] == "join"){
	/* 회원가입 SMS 발송 */
	$blogname = get_option('blogname');

	$sms_content = $config->sms_join_msg;
	$sms_content = str_replace("#user_id#", $_POST['success_id'], $sms_content);
	$sms_content = str_replace("#cp_name#", $blogname, $sms_content);
	$sms_adm_content = $config->sms_admin_msg;
	$sms_adm_content = str_replace("#user_id#", $_POST['success_id'], $sms_adm_content);
	$sms_adm_content = str_replace("#cp_name#", $blogname, $sms_adm_content);
	
	// 가입회원에게 발송
	if((!empty($config->sms_use_yn) && $config->sms_use_yn == "Y") && (!empty($config->sms_join_yn) && $config->sms_join_yn == "Y") && (!empty($_POST['success_hp']) && (!empty($_POST['sms_send']) && $_POST['sms_send'] == 1))){
		bbse_ezsms_send($_POST['success_hp'], $config->sms_callback_tel, $sms_content, "SMS", "D", "");
	}
	// 관리자에게 발송
	if((!empty($config->sms_use_yn) && $config->sms_use_yn == "Y") && (!empty($config->sms_admin_yn) && $config->sms_admin_yn == "Y") && !empty($config->sms_admin_tel)){ 
		bbse_ezsms_send($config->sms_admin_tel, $config->sms_callback_tel, $sms_adm_content, "SMS", "D", "");
	}
	$ok_info = explode("|||",base64_decode($_POST['ok_info']));
?>
<div class="article">
	<div class="bd_box bold_bd">
		<p class="bb_leave_text">
			<strong><?php bloginfo('name')?></strong> 회원으로 가입해주셔서 감사합니다.
		</p>
		<p class="bb_leave_user">
			<strong><?php echo $ok_info[3]?></strong> 회원님의 아이디는 <strong><?php echo $ok_info[0]?></strong>이며, 비밀번호는 <strong><?php echo $ok_info[5].str_repeat("*",$ok_info[4]-2)?></strong> 입니다.
		</p>
		<!--<p class="bb_result_text">
			주문번호 <strong>12312343415-123908123</strong>
		</p>-->
	</div>
</div>
<br />
<div class="article agree_btn_area">
	<button type="button" class="bb_btn cus_fill w150" onclick="location.href='<?php echo get_permalink($page_setting['login_page'])?>';"><strong class="big">로그인</strong></button>
</div>
<?php }?>


</form>
<form method="post" name="success_frm" id="success_frm">
<input type="hidden" name="success_mode" id="success_mode" value="join" />
<input type="hidden" name="success_id" id="success_id" value="" />
<input type="hidden" name="success_hp" id="success_hp" value="" />
<input type="hidden" name="sms_send" id="sms_send" value="" />
<input type="hidden" name="ok_info" id="ok_info" value="" />
</form>

<?php if($step=="2" && !is_user_logged_in()){?>
<form name="authCheckFrm" method="post">
<input type="hidden" name="m" value="checkplusSerivce">	
<input type="hidden" name="EncodeData" value="<?php echo $enc_data ?>">
<input type="hidden" name="param_r1" value="">
<input type="hidden" name="param_r2" value="">
<input type="hidden" name="param_r3" value="">
</form>
<?php }?>
<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */?>
<div id="commerceZipcodeLayer" style="display:none;border:5px solid;position:fixed;width:320px;height:500px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script><!--https-->
<script>
    var element = document.getElementById('commerceZipcodeLayer');
    function closeDaumPostcode() {
        element.style.display = 'none';
    }
</script> 
<?php }?>
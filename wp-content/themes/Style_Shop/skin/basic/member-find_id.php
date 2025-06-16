<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
?>
<script type="text/javascript">
function id_search(ajax_act){
	jQuery.ajax({
		url: ajax_act,
		type: "post",
		dataType: "jsonp",
		jsonp: "callback",
		data: jQuery('#search_frm').serialize(),
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
				if(data_arr[2] != "") jQuery(".idpwFindBox").empty().html(data_arr[2]);
			}else if(res == "empty email"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 이메일을 입력해주세요.");
				jQuery("#email").focus();
			}else if(res == "not form email"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 이메일 형식이 올바르지 않습니다.");
				jQuery("#email").focus();
			}
		});
	}
}
</script>
<form method="post" name="search_frm" id="search_frm">
<h2 class="page_title">아이디찾기</h2>
<div class="article">
	<div class="bd_box">
		<div class="idpwFindBox">
			<p>회원가입 할 때 등록한 이메일 주소를 입력하십시오.</p>
			<div class="inputContainer">
				<label for="email">이메일</label><input type="text" name="email" id="email" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}" />
			</div>
			
			<p class="btm_area">
				<span id="error_box" class="open_alert" style="display:none;"></span><br /><br />
				<button type="button" class="bb_btn cus_fill shadow" onclick="id_search('<?php echo $action_url?>/proc/id.search.php');"><span class="mid">아이디찾기</span></button>
			</p>
		</div><!--//회원로그인 -->
	</div>
</div>
</form>
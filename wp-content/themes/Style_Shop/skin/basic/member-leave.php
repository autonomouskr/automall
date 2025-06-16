<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
?>
<script type="text/javascript">
function user_delete(ajax_act){
	jQuery.ajax({
		url: ajax_act,
		type: "post",
		dataType: "jsonp",
		jsonp: "callback",
		data: jQuery('#delete_frm').serialize(),
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
				jQuery(".memberLeaveBox").empty().html(data_arr[2]);
			}else if(res == "not login"){
				jQuery(".memberLeaveBox").empty().html("에러 : 로그인 후 이용해주세요.");
			}else if(res == "empty agree"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 탈퇴약관에 동의해주세요.");
				jQuery("#leave_agree").focus();
			}else if(res == "empty pass"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 비밀번호를 입력해주세요.");
				jQuery("#pass").focus();
			}else if(res == "empty repass"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 비밀번호 확인을 입력해주세요.");
				jQuery("#repass").focus();
			}else if(res == "password mismatch"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 비밀번호가 일치하지 않습니다.");
			}else if(res == "empty reason"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 탈퇴사유를 선택해주세요.");
				jQuery("#leave_reason").focus();
			}else if(res == "notinput reason"){
				jQuery("#error_box").show();
				jQuery("#error_box").html("에러 : 탈퇴사유를 입력해주세요.");
				jQuery("#input_reason").focus();
			}
		});
	}
}
</script>
<h2 class="page_title">회원탈퇴</h2>
<form method="post" name="delete_frm" id="delete_frm">
<div class="article">
	<ul class="bb_dot_list">
		<li style="margin-top:10px;">회원탈퇴시 탈퇴한 아이디는 재사용 및 복구가 불가하오니 신중하게 선택하시기 바랍니다.</li>
		<li style="line-height:20px;">보유하고 게신 적립금/쿠폰은 모두 소멸되며, 재가입하셔도 복구하실 수 없습니다.</li>
	</ul>
</div>

<div class="article">
	<div class="memberLeaveBox bd_box">
		<div class="bb_join agreebox">
			<h3>탈퇴약관동의</h3>
		<?php if(get_option($theme_shortname.'_memberpage_agreement_leave')>'0'){?>
			<div class="req">
				<a href="<?php echo get_permalink(get_option($theme_shortname.'_memberpage_agreement_leave'))?>" class="bb_more">자세히보기</a>
			</div>
		<?php }?>
			<div class="agree_page"><?php echo nl2br(get_option($theme_shortname."_member_agreement_leave"))?></div>
			<p class="chk_box">
				<label for="leave_agree"><input type="checkbox" name="leave_agree" id="leave_agree" value="y"/> 위의 탈퇴약관 및 안내사항을 모두 확인하였으며, 이에 동의합니다.</label>
			</p>

			<h3 class="mt50">회원정보</h3>
			<div class="tb_wt">
				<table>
					<caption>회원정보 표</caption>
					<colgroup>
						<col style="width:20%;">
						<col style="width:auto;">
					</colgroup>
					<tbody>
						<tr>
							<th scope="row">아이디</th>
							<td>
								<div>아이디</div>
								<?php echo $rows->user_id?>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="repass">비밀번호</label></th>
							<td>
								<div><label for="pass">비밀번호</label></div>
								<input type="password" name="pass" id="pass"  size="20" />
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="repass">비밀번호 확인</label></th>
							<td>
								<div><label for="repass">비밀번호 확인</label></div>
								<input type="password" name="repass" id="repass"  size="20" />
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="leave_reason">탈퇴사유</label></th>
							<td>
								<div><label for="leave_reason">탈퇴사유</label></div>
								<select name="leave_reason" id="leave_reason" title="탈퇴사유를 선택해주세요." onchange="if(this.selectedIndex==4) jQuery('#input_reason').show(); else jQuery('#input_reason').hide();">
									<option value="">탈퇴사유를 선택해주세요</option>
								<?php
									foreach(unserialize(BBSE_COMMERCE_LEAVE_REASON) as $key=>$val) {
										echo '<option value="'.$key.'">'.$val.'</option>';
									}
								?>
								</select>
								<input type="text" name="input_reason" id="input_reason"  style="width:50%;display:none;" />
							</td>
						</tr>
					</tbody>
				</table>
			</div><!--//.tb_wt -->

			<br />
			<ul class="bb_dot_list">
				<li>탈퇴관련 기타 문의사항등이 계시면 고객센터 또는 1:1문의를 이용해 주세요.</li>
			</ul>
		</div><!--//.bb_join -->

		<div class="article agree_btn_area">
			<span id="error_box" class="open_alert" style="display:none;"></span><br /><br />
			<button type="button" class="bb_btn cus_fill w150" onclick="user_delete('<?php echo $action_url?>/proc/user.delete.php');"><strong class="big">탈퇴확인</strong></button>
			<button type="button" class="bb_btn w150" onclick="location.href='<?php echo BBSE_COMMERCE_SITE_URL?>';"><strong class="big">취소</strong></button>
		</div>

	</div>
</div>
</form>
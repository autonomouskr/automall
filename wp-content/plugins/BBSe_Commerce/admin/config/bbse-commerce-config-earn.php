<?php
$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$cType."'");

if($cnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='".$cType."'");
	$data=unserialize($confData->config_data);
}
?>
<script language="javascript">
	function change_earn(tType,tVal){
		if(tType=='earn_pay_info'){
			for(z=1;z<=7;z++){
				if(tVal=='on'){
					if(z==6) jQuery("#earn_pay_info_"+z).css("display","block");
					else jQuery("#earn_pay_info_"+z).css("display","table-row");
				}
				else{
					jQuery("#earn_pay_info_"+z).css("display","none");
				}
			}
		}
		else{
			if(tVal=='on'){
				jQuery("#earn_"+tType).css("display","inline");
			}
			else{
				jQuery("#earn_"+tType).css("display","none");
			}
		}
	}

	function config_submit(){
		if(jQuery("input[id='earn_member_use']:checked").val()=='on' && jQuery("#earn_member_point").val()<=0){
			alert("회원가입 축하 적립금을 입력해 주세요.     ");
			jQuery("#earn_member_point").focus();
			return;
		}
		if(jQuery("input[id='earn_birth_use']:checked").val()=='on' && jQuery("#earn_birth_point").val()<=0){
			alert("생일 축하 적립금을 입력해 주세요.     ");
			jQuery("#earn_birth_point").focus();
			return;
		}
		if(jQuery("input[id='earn_review_use']:checked").val()=='on' && jQuery("#earn_review_point").val()<=0){
			alert("상품후기 작성 적립금을 입력해 주세요.     ");
			jQuery("#earn_review_point").focus();
			return;
		}

		if(jQuery("input[id='earn_pay_use']:checked").val()=='on'){
			if(jQuery("#earn_hold_point").val()<=0){
				alert("사용 가능 적립금 보유금액을 입력해주세요.     ");
				jQuery("#earn_hold_point").focus();
				return;
			}
			if(jQuery("#earn_order_pay").val()<=0){
				alert("주문 합계액 기준금액을 입력해주세요.     ");
				jQuery("#earn_order_pay").focus();
				return;
			}
			if(jQuery("#earn_min_point").val()<=0){
				alert("적립금 최소 사용금액을 입력해주세요.     ");
				jQuery("#earn_min_point").focus();
				return;
			}
			if(jQuery("#earn_max_percent").val()<=0){
				alert("적립금 최대 사용금액을 입력해주세요.     ");
				jQuery("#earn_max_percent").focus();
				return;
			}
			if(jQuery("#earn_use_unit").val()<=0){
				alert("적립금 사용 단위를 입력해주세요.     ");
				jQuery("#earn_use_unit").focus();
				return;
			}
		}

		if(confirm('적립금 설정을 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('적립금 설정을 정상적으로 저장하였습니다.   ');
						go_config('earn');
					}
					else if(result=='DbError'){
						alert('[Error !] DB 오류 입니다.   ');
					}
					else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}
</script>
	<div>
		<form name="cnfFrm" id="cnfFrm">
		<input type="hidden" name="cType" id="cType" value="earn" />
			<div class="titleH5" style="margin:20px 0 10px 0; ">부가 적립금 적립 설정</div>

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>회원가입 축하 적립금</th>
					<td><input type="radio" name="earn_member_use" id="earn_member_use" value="on" onClick="change_earn('member','on');" <?php echo ($data['earn_member_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="earn_member_use" id="earn_member_use" value="off" onClick="change_earn('member','off');" <?php echo (!$data['earn_member_use'] || $data['earn_member_use']=='off')?"checked='checked'":"";?> /> 사용안함&nbsp;&nbsp;&nbsp;&nbsp;<span id="earn_member" style="display:<?php echo ($data['earn_member_use']=='on')?"inline":"none";?>;"><input type="text" name="earn_member_point" id="earn_member_point" onkeydown="check_number();" value="<?php echo $data['earn_member_point'];?>" style="width:100px;" />원 (최초 1회 : 즉시 지급)</span></td>
				</tr>
				<tr>
					<th>생일 축하 적립금</th>
					<td><input type="radio" name="earn_birth_use" id="earn_birth_use" value="on" onClick="change_earn('birth','on');" <?php echo ($data['earn_birth_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="earn_birth_use" id="earn_birth_use" value="off" onClick="change_earn('birth','off');" <?php echo (!$data['earn_birth_use'] || $data['earn_birth_use']=='off')?"checked='checked'":"";?> /> 사용안함&nbsp;&nbsp;&nbsp;&nbsp;<span id="earn_birth" style="display:<?php echo ($data['earn_birth_use']=='on')?"inline":"none";?>;"><input type="text" name="earn_birth_point" id="earn_birth_point" onkeydown="check_number();" value="<?php echo $data['earn_birth_point'];?>" style="width:100px;" />원 (회원 로그인 시 지급 : 생일 전후 15일 중 1회)</span></td>
				</tr>
				<tr>
					<th>상품후기 작성 적립금</th>
					<td><input type="radio" name="earn_review_use" id="earn_review_use" value="on" onClick="change_earn('review','on');" <?php echo ($data['earn_review_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="earn_review_use" id="earn_review_use" value="off" onClick="change_earn('review','off');" <?php echo (!$data['earn_review_use'] || $data['earn_review_use']=='off')?"checked='checked'":"";?> /> 사용안함&nbsp;&nbsp;&nbsp;&nbsp;<span id="earn_review" style="display:<?php echo ($data['earn_review_use']=='on')?"inline":"none";?>;"><input type="text" name="earn_review_point" id="earn_review_point" onkeydown="check_number();" value="<?php echo $data['earn_review_point'];?>" style="width:100px;" />원 (관리자 승인 시 지급)</span></td>
				</tr>
			</table>

			<div class="clearfix"></div>
			<div class="titleH5" style="margin:30px 0 10px 0; ">적립금 사용 설정</div>
			
		    <div class="clearfix"></div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>결제 시 적립금 사용여부</th>
					<td>
						<input type="radio" name="earn_pay_use" id="earn_pay_use" value="on" onClick="change_earn('earn_pay_info','on');" <?php echo ($data['earn_pay_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="earn_pay_use" id="earn_pay_use" value="off" onClick="change_earn('earn_pay_info','off');" <?php echo (!$data['earn_pay_use'] || $data['earn_pay_use']=='off')?"checked='checked'":"";?> /> 사용안함
					</td>
				</tr>
				<tr id="earn_pay_info_1" style="display:<?php echo ($data['earn_pay_use']=='on')?"table-row":"none";?>;">
					<th>사용 가능 적립금 보유액</th>
					<td>
						보유 적립금이 <input type="text" name="earn_hold_point" id="earn_hold_point" onkeydown="check_number();" value="<?php echo $data['earn_hold_point'];?>" style="width:100px;" />원 이상일때 사용가능 합니다.
					</td>
				</tr>
				<tr id="earn_pay_info_2" style="display:<?php echo ($data['earn_pay_use']=='on')?"table-row":"none";?>;">
					<th>주문 합계액 기준 ⓐ</th>
					<td>
						주문 합계액이 <input type="text" name="earn_order_pay" id="earn_order_pay" onkeydown="check_number();" value="<?php echo $data['earn_order_pay'];?>" style="width:100px;" />원 이상일때 사용가능 합니다.
					</td>
				</tr>
				<tr id="earn_pay_info_3" style="display:<?php echo ($data['earn_pay_use']=='on')?"table-row":"none";?>;">
					<th>적립금 최소 사용금액 ⓑ</th>
					<td>
						적립금은 최소 <input type="text" name="earn_min_point" id="earn_min_point" onkeydown="check_number();" value="<?php echo $data['earn_min_point'];?>" style="width:100px;" />원 부터 사용가능합니다.
					</td>
				</tr>
				<tr id="earn_pay_info_4" style="display:<?php echo ($data['earn_pay_use']=='on')?"table-row":"none";?>;">
					<th>적립금 최대 사용금액 ⓒ</th>
					<td>
						주문 합계액 기준 최대 <input type="text" name="earn_max_percent" id="earn_max_percent" onkeydown="check_number();" value="<?php echo $data['earn_max_percent'];?>" style="width:100px;" />% 까지 사용가능합니다.
					</td>
				</tr>
				<tr id="earn_pay_info_5" style="display:<?php echo ($data['earn_pay_use']=='on')?"table-row":"none";?>;">
					<th>적립금 사용 단위 ⓓ</th>
					<td>
						적립금은 <input type="text" name="earn_use_unit" id="earn_use_unit" onkeydown="check_number();" value="<?php echo $data['earn_use_unit'];?>" style="width:100px;" />원 단위로 사용이 가능합니다.
					</td>
				</tr>
				<tr id="earn_pay_info_7" style="display:<?php echo ($data['earn_pay_use']=='on')?"table-row":"none";?>;">
					<th>적립금 초기화</th>
					<td>
						<input type="radio" name="earn_reset_use" id="earn_reset_use" value="on" <?php echo ($data['earn_reset_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="earn_reset_use" id="earn_reset_use" value="off" <?php echo (!$data['earn_reset_use'] || $data['earn_reset_use']=='off')?"checked='checked'":"";?> /> 사용안함&nbsp;&nbsp;&nbsp;(마지막 적립금 지급 이후 1년 동안 사용하지 않은 적립금은 소멸)
					</td>
				</tr>

			</table>
			<div id="earn_pay_info_6" class="prd-desc" style="display:<?php echo ($data->earn_pay_use=='on')?"block":"none";?>;">* ⓑⓒⓓ 금액이 ⓐ 보다 큰 경우 고객이 적립금을 사용할 수 없는 상황이 발생되오니 고려하여 설정해 주세요.</div>

			<div class="clearfix" style="margin-top:30px;"></div>

			<div class="borderBox">
				* 회원전용 (비회원 혜택 없음)<br/>
				<div class="titleH6" style="margin:10px 0 5px 0;">[적립금]</div>
				- 구매에 따른 적립금 적용 : 구매확정 시<br/>
				- 적립금 소멸 기간 : 마지막 적립금 지급 이후 1년 동안 사용하지 않은 적립금은 소멸 (소멸 예정월 1일 기준)
			</div>

			<div class="clearfix"></div>
		</form>
	</div>


	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>
	</div>

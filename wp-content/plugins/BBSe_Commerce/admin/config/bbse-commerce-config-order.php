<?php
$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$cType."'");

if($cnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='".$cType."'");
	$data=unserialize($confData->config_data);
}

$mConfig = $wpdb->get_row("SELECT * FROM bbse_commerce_membership_config LIMIT 1");
?>
<script language="javascript">
	function config_submit(){
		if(!jQuery("#total_pay_unit").val()){
			alert("주문 총 금액의 단위를 입력해 주세요.     ");
			jQuery("#total_pay_unit").focus();
			return;
		}
		if(jQuery("input[name=soldout_notice_use]").eq(0).is(":checked") && !jQuery("input[name=soldout_notice_sms]").is(":checked") && !jQuery("input[name=soldout_notice_email]").is(":checked")){
			alert("품절상품 입고알림 방법을 선택해 주세요.     ");
			jQuery("#soldout_notice_sms").focus();
			return;
		}

		if(confirm('주문설정을 저장하시겠습니까?     ')){
			var formData = new FormData(jQuery('#cnfFrm')[0]);
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				//data: jQuery("#cnfFrm").serialize(),
				data: formData, 
				processData: false,
				contentType : false,
				success: function(data){
					//alert(data);
					var result = data;
					if(result=='success'){
						alert('주문설정을 정상적으로 저장하였습니다.   ');
						go_config('order');
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
		<form name="cnfFrm" id="cnfFrm" >
		<input type="hidden" name="cType" id="cType" value="order" />
			<style>
				.esti_use label{
					width: 100px;
				    display: inline-block;
				    vertical-align: middle;
				}
			</style>
			
			<div class="titleH5" style="margin:20px 0 10px 0; ">품절상품 입고알림</div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>사용여부</th>
					<td>
						<input type="radio" name="soldout_notice_use" id="soldout_notice_use" onClick="jQuery('.soldoutNoticeUse').show();" value="on" <?php echo ($data['soldout_notice_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="soldout_notice_use" id="soldout_notice_use" onClick="jQuery('.soldoutNoticeUse').hide();" value="off" <?php echo (!$data['soldout_notice_use'] || $data['soldout_notice_use']=='off')?"checked='checked'":"";?> /> 사용안함
					</td>
				</tr>
				<tr class="soldoutNoticeUse" style="display:<?php echo ($data['soldout_notice_use']=='on')?"table-row":"none";?>;">
					<th>알림방법</th>
					<td>
						<input type="checkbox" name="soldout_notice_sms" id="soldout_notice_sms" value="sms" <?php echo ($mConfig->sms_use_yn=="Y" && $data['soldout_notice_sms']=='sms')?"checked='checked'":"";?> <?php echo ($mConfig->sms_use_yn!="Y")?"disabled":"";?> /> SMS&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="soldout_notice_email" id="soldout_notice_email" value="email" <?php echo ($data['soldout_notice_email']=='email')?"checked='checked'":"";?> /> E-mail
						<?php echo ($mConfig->sms_use_yn!="Y")?"<br /><span class='emRed'>※ SMS 알림 불가 : SMS 설정이 사용안한 상태입니다. (회원관리 - SMS관리)</span>":"";?>
					</td>
				</tr>
			</table>

			<div class="titleH5" style="margin:20px 0 10px 0; ">주문자 전화번호</div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>주문자 전화번호</th>
					<td><input type="radio" name="order_phone_use" id="order_phone_use" value="U" <?php echo (!$data['order_phone_use'] || $data['order_phone_use']=='U')?"checked='checked'":"";?> /> 필수 입력&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="order_phone_use" id="order_phone_use" value="C" <?php echo ($data['order_phone_use']=='C')?"checked='checked'":"";?> /> 선택 입력&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="order_phone_use" id="order_phone_use" value="N" <?php echo ($data['order_phone_use']=='N')?"checked='checked'":"";?> /> <span class="emRed">사용 안함</span>
					<br/><span class="emRed">* 주문서 작성시 주문자의 전화번호를 필수/선택 입력, 또는 사용여부를 설정합니다.</span></td>
				</tr>
			</table>

			<div class="titleH5" style="margin:20px 0 10px 0; ">재고삭감</div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>재고 삭감 기준</th>
					<td><input type="radio" name="count_cutback" id="count_cutback" value="order" <?php echo (!$data['count_cutback'] || $data['count_cutback']=='order')?"checked='checked'":"";?> /> 주문완료 시&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="count_cutback" id="count_cutback" value="deposit" <?php echo ($data['count_cutback']=='deposit')?"checked='checked'":"";?> /> 입금완료 시</td>
				</tr>
			</table>

			<div class="clearfix"></div>
			<div class="titleH5" style="margin:30px 0 10px 0; ">장바구니 상품 보관 설정</div>

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>장바구니 상품보관 기간설정</th>
					<td>장바구니에 보관 된 상품은 <select name="cart_empty_cycle" id="cart_empty_cycle" style="height:29px;line-height:29px; width:50px;">
							<?php for($c=1;$c<31;$c++){
							if(!$data['cart_empty_cycle'] && $c=='7' || $data['cart_empty_cycle']==$c) $cycleSelected="selected='selected'";
							else  $cycleSelected="";

							echo "<option value='".$c."' ".$cycleSelected.">".$c."</option>";
							}
							?>
						</select>일 까지 보관 후 자동삭제</td>
				</tr>
			</table>

			<div class="clearfix"></div>
			<div class="titleH5" style="margin:30px 0 10px 0; ">주문상태 자동변경 설정</div>

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<!-- <tr>
					<th>주문취소 기간 설정</th>
					<td>주문완료 후 <select name="order_cancel_day" id="order_cancel_day" style="width:50px;">
						<?php
						for($i=1;$i<8;$i++){
							if((!$data['order_cancel_day'] && $i=='3') || $data['order_cancel_day']>'0' && $data['order_cancel_day']==$i) $cancelSelected="selected='selected'";
							else $cancelSelected="";

							echo "<option value=\"".$i."\" ".$cancelSelected.">".$i."</option>";
						}
						?>
						</select>일 이내에 구매자가 입금을 하지 않는 경우, 자동으로 주문취소(취소완료) 처리</td>
				</tr> -->
				<tr>
					<th>배송완료 기간 설정</th>
					<td>배송중으로 상태를 변경한 후 <select name="delivery_end_day" id="delivery_end_day" style="width:50px;">
						<?php
						for($j=1;$j<8;$j++){
							if((!$data['delivery_end_day'] && $j=='3') || $data['delivery_end_day']>'0' && $data['delivery_end_day']==$j) $deliveryEndSelected="selected='selected'";
							else $deliveryEndSelected="";

							echo "<option value=\"".$j."\" ".$deliveryEndSelected.">".$j."</option>";
						}
						?>
						</select>일 이후 자동으로 배송완료 처리</td>
				</tr>
				<!-- 
				<tr>
					<th>구매확정 기간 설정</th>
					<td>배송완료 후 <select name="order_end_day" id="order_end_day" style="width:50px;">
						<?php
						for($k=1;$k<16;$k++){
							if((!$data['order_end_day'] && $k=='7') || $data['order_end_day']>'0' && $data['order_end_day']==$k) $orderEndSelected="selected='selected'";
							else $orderEndSelected="";

							echo "<option value=\"".$k."\" ".$orderEndSelected.">".$k."</option>";
						}
						?>
						</select>일 이내에 구매자가 구매확정을 하지 않은 경우, 자동으로 구매확정 처리</td>
				</tr>
				 -->
			</table>


			<div class="clearfix"></div>
			<div class="titleH5" style="margin:30px 0 10px 0; ">주문 총 금액 설정</div>

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>주문 총 금액 설정</th>
					<td>주문 총 금액은 <input type="text" name="total_pay_unit" id="total_pay_unit" onkeydown="check_number();" value="<?php echo $data['total_pay_unit'];?>" style="width:100px;" />원 단위로 
					<select name="total_pay_round" id="total_pay_round" style="width:50px;">
						<option value="down" <?php echo (!$data['total_pay_round'] || $data['total_pay_round']=='down')?"selected='selected'":"";?>>절삭</option><option value="up" <?php echo ($data['total_pay_round']=='up')?"selected='selected'":"";?>>올림</option></select></td>
				</tr>
			</table>
			<div class="clearfix"></div>
			<div class="titleH5" style="margin:20px 0 10px 0; ">견적서 사용 여부</div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>사용여부</th>
					<td>
						<input onClick="jQuery('.esti_use').show();" type="radio" name="esti_use" id="esti_use" value="on" <?php echo ($data['esti_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;
						<input onClick="jQuery('.esti_use').hide();" type="radio" name="esti_use" id="esti_use" value="off" <?php echo (!$data['esti_use'] || $data['esti_use']=='off')?"checked='checked'":"";?> /> 사용안함
					</td>
				</tr>
				<tr class="esti_use" style="display:<?php echo ($data['esti_use']=='on') ? "table-row" : "none";?>;">
					<th>사업자정보</th>
					<td>
						<div style="">
							<label>상호</label>
							<input type="text" style="min-width: 300px;" name="esti_company" value="<?php echo $data['esti_company']; ?>" />
						</div>
						<div style="">
							<label>대표</label>
							<input type="text" style="min-width: 300px;" name="esti_ceo" value="<?php echo $data['esti_ceo']; ?>" />
						</div>
						<div style="">
							<label>전화</label>
							<input type="text" style="min-width: 300px;" name="esti_tel" value="<?php echo $data['esti_tel']; ?>" />
						</div>
						<div style="">
							<label>팩스</label>
							<input type="text" style="min-width: 300px;" name="esti_fax" value="<?php echo $data['esti_fax']; ?>" />
						</div>
						<div style="">
							<label>주소</label>
							<input type="text" style="min-width: 300px;" name="esti_addr" value="<?php echo $data['esti_addr']; ?>" />
						</div>
						<div style="">
							<label>담당자명</label>
							<input type="text" style="min-width: 300px;" name="esti_manager" value="<?php echo $data['esti_manager']; ?>" />
						</div>
						<div style="">
							<label>담당자 연락처</label>
							<input type="text" style="min-width: 300px;" name="esti_manager_tel" value="<?php echo $data['esti_manager_tel']; ?>" />
						</div>
						<div style="">
							<label>담당자 이메일</label>
							<input type="text" style="min-width: 300px;" name="esti_manager_email" value="<?php echo $data['esti_manager_email']; ?>" />
						</div>
						<div style="">
							<label>사업자등록번호</label>
							<input type="text" style="min-width: 300px;" name="esti_num" value="<?php echo $data['esti_num']; ?>" />
						</div>
						<div style="">
							<label>업태/종목</label>
							<input type="text" style="min-width: 300px;" name="esti_service" value="<?php echo $data['esti_service']; ?>" />
						</div>
						<div style="">
							<label>견적유효기간</label>
							<input type="text" style="min-width: 300px;" name="esti_period" value="<?php echo $data['esti_period']; ?>" />
						</div>
						<div style="">
							<label>계약조건</label>
							<input type="text" style="min-width: 300px;" name="esti_condi" value="<?php echo $data['esti_condi']; ?>" />
						</div>
						<div style="">
							<label>입금계좌정보</label>
							<input type="text" style="min-width: 300px;" name="esti_account" value="<?php echo $data['esti_account']; ?>" />
						</div>
						<div style="">
							<label>로고파일</label>
							<input type="file" name="esti_logo" value="" />
							<?php
								echo (!empty($data['esti_logo']) ? '<img style="height: 50px;" src="'.$data['esti_logo'].'" />':'');
							?>
							<input type="hidden" name="esti_logo_url" value="<?php echo $data['esti_logo']; ?>" />
						</div>
						<div style="">
							<label>인감도장파일</label>
							<input type="file" name="esti_file" value="" />
							<?php
								echo (!empty($data['esti_file']) ? '<img style="height: 50px;" src="'.$data['esti_file'].'" />':'');
							?>
							<input type="hidden" name="esti_file_url" value="<?php echo $data['esti_file']; ?>" />
						</div>
					</td>
				</tr>
			</table>
			<div class="titleH5" style="margin:20px 0 10px 0; ">개인통관번호 사용여부</div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>사용여부</th>
					<td>
						<input type="radio" name="pass_num_use" id="pass_num_use" value="on" <?php echo ($data['pass_num_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="pass_num_use" id="pass_num_use" value="off" <?php echo (!$data['pass_num_use'] || $data['pass_num_use']=='off')?"checked='checked'":"";?> /> 사용안함
					</td>
				</tr>
			</table>
		</form>
	</div>


	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>
	</div>

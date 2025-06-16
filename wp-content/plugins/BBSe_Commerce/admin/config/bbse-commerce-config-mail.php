<?php
$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$cType."'");

if($cnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='".$cType."'");
	$data=unserialize($confData->config_data);
}
?>
<script language="javascript">
	function config_submit(){
		if(confirm('메일설정을 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('메일설정을 정상적으로 저장하였습니다.   ');
						go_config('mail');
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
	<div class="titleH5" style="margin:30px 0 10px 0; ">메일설정</div>
	<div>
		<form name="cnfFrm" id="cnfFrm">
			<input type="hidden" name="cType" id="cType" value="mail" />
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>회원가입</th>
					<td><input type="radio" name="member_mail_use" id="member_mail_use" value="on" <?php echo (!$data['member_mail_use'] || $data['member_mail_use']=='on')?"checked='checked'":"";?> /> 메일발송&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="member_mail_use" id="member_mail_use" value="off" <?php echo ($data['member_mail_use']=='off')?"checked='checked'":"";?> /> 메일발송 안함<span style="margin-left:50px;"><input type="checkbox" name="blog_olny_mail_join" id="blog_olny_mail_join" value="Y" <?php echo ($data['blog_olny_mail_join']=='Y')?"checked='checked'":"";?> /> 블로그 전용 회원가입 폼으로 메일 발송</span></td>
				</tr>
				<tr>
					<th>비밀번호 찾기</th>
					<td><input type="radio" name="findpw_mail_use" id="findpw_mail_use" value="on" checked='checked' /> 메일발송 <font color='#ED1C24'>(필수사항)</font><span style="margin-left:105px;"><input type="checkbox" name="blog_olny_mail_idpw" id="blog_olny_mail_idpw" value="Y" <?php echo ($data['blog_olny_mail_idpw']=='Y')?"checked='checked'":"";?> /> 블로그 전용 비밀번호 찾기 폼으로 메일 발송</span></td>
				</tr>
				<tr>
					<th>입금대기</th>
					<td><input type="radio" name="order_mail_use" id="order_mail_use" value="on" <?php echo (!$data['order_mail_use'] || $data['order_mail_use']=='on')?"checked='checked'":"";?> /> 메일발송&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="order_mail_use" id="order_mail_use" value="off" <?php echo ($data['order_mail_use']=='off')?"checked='checked'":"";?> /> 메일발송 안함</td>
				</tr>
				<tr>
					<th>결제완료</th>
					<td><input type="radio" name="input_mail_use" id="input_mail_use" value="on" <?php echo (!$data['input_mail_use'] || $data['input_mail_use']=='on')?"checked='checked'":"";?> /> 메일발송&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="input_mail_use" id="input_mail_use" value="off" <?php echo ($data['input_mail_use']=='off')?"checked='checked'":"";?> /> 메일발송 안함</td>
				</tr>
				<tr>
					<th>상품 발송완료(배송중)</th>
					<td><input type="radio" name="shipment_mail_use" id="shipment_mail_use" value="on" <?php echo (!$data['shipment_mail_use'] || $data['shipment_mail_use']=='on')?"checked='checked'":"";?> /> 메일발송&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="shipment_mail_use" id="shipment_mail_use" value="off" <?php echo ($data['shipment_mail_use']=='off')?"checked='checked'":"";?> /> 메일발송 안함</td>
				</tr>
				<tr>
					<th>취소완료</th>
					<td><input type="radio" name="cancel_mail_use" id="cancel_mail_use" value="on" <?php echo (!$data['cancel_mail_use'] || $data['cancel_mail_use']=='on')?"checked='checked'":"";?> /> 메일발송&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="cancel_mail_use" id="cancel_mail_use" value="off" <?php echo ($data['cancel_mail_use']=='off')?"checked='checked'":"";?> /> 메일발송 안함</td>
				</tr>
				<tr>
					<th>반품완료</th>
					<td><input type="radio" name="refund_mail_use" id="refund_mail_use" value="on" <?php echo (!$data['refund_mail_use'] || $data['refund_mail_use']=='on')?"checked='checked'":"";?> /> 메일발송&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="refund_mail_use" id="refund_mail_use" value="off" <?php echo ($data['refund_mail_use']=='off')?"checked='checked'":"";?> /> 메일발송 안함</td>
				</tr>
			</table>
			<div class="clearfix"></div>
		</form>

		<div class="clearfix" style="margin-top:30px;"></div>

		<div class="borderBox">
			<div class="titleH6" style="margin-bottom:5px;">[자동 메일 발송]</div>
			&nbsp;&nbsp;- 회원가입 메일 : 회원가입 시 발송<br>
			&nbsp;&nbsp;- 비밀번호 찾기 메일 : 회원이 비밀번호 찾기 시 발송 (필수사항)<br>
			&nbsp;&nbsp;- 입금대기 메일 : 주문완료 시 발송(결제 방법이 무통장입금/가상계좌 인 경우)<br>
			&nbsp;&nbsp;- 결제완료 메일 : 결제 방법에 관계없이 결제 완료 시 발송 (결제 방법이 무통장입금/가상계좌인 경우 관리자가 입금확인 시 발송)<br>
			&nbsp;&nbsp;- 상품 발송완료(배송중) 메일 : 주문 상품을 택배사 지정 및 송장번호 입력 후 발송중 처리 시 발송<br>	
			&nbsp;&nbsp;- 취소완료(배송중) : 배송중 전 취소 시 발송(기타 : 접수된 취소신청내역을 관리자가 승인 시 발송)<br>
			&nbsp;&nbsp;- 반품완료 : 접수된 반품신청내역을 관리자가 승인 시 발송<br>
		</div>
	</div>

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>
	</div>

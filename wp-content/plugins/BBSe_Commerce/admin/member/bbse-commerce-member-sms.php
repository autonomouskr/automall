<script language="javascript">
var tUri = "<?php echo $tUrl?>";

function resetForm(action_url){
	var frm = document.frmInsert;
	if(confirm("문자(SMS/LMS) 환경설정을 초기화 하시겠습니까?")){
		frm.tMode.value = "reset";
		frm.action = action_url;
		frm.submit();
	}
}

function submitForm(action_url){
	var frm = document.frmInsert;
	if(!frm.sms_use_yn[0].checked && !frm.sms_use_yn[1].checked){
		alert('SMS/LMS 사용여부를 선택해주세요.');
		frm.sms_use_yn[0].focus();
		return false;
	}
	if(frm.sms_use_yn[0].checked){
		if(!frm.sms_id.value){
			alert('SMS/LMS 아이디를 입력해주세요.');
			frm.sms_id.focus();
			return false;
		}
		if(!frm.sms_key.value){
			alert('SMS/LMS KEY를 입력해주세요.');
			frm.sms_key.focus();
			return false;
		}
		if(!frm.sms_callback_tel.value){
			alert('발신자 번호를 입력해주세요.');
			frm.sms_callback_tel.focus();
			return false;
		}
		if(!frm.sms_admin_tel.value){
			alert('관리자 수신번호를 입력해주세요.');
			frm.sms_admin_tel.focus();
			return false;
		}
	}

	if(confirm("문자(SMS/LMS) 환경설정을 저장 하시겠습니까?")){
		frm.tMode.value = "save";
		frm.action = action_url;
		frm.submit();
	}
}

function popupResultHide(){
	jQuery("#layerResult").css({'display':'none'});
	jQuery("#layerResult").html("");
}

function popupKeyGend(){
	var str = "<table border='0' cellpadding='0' cellspacing='0' width='530'><tr><td align='right' height='25' valign='top' style='padding-top:1px;'><button type='button' class='button-small blue' style='height:25px;' onclick='popupResultHide();'>닫기</button></td></tr></table>";
	str += "<table align='center' cellpadding='4' cellspacing='1' width='530' bgcolor='#e1e1e1'>"
	str += "<tr>"
	str += "<td bgcolor='#f7f7f7'><b>SMS 인증키 발급(Ver 0.1)</b></td>"
	str += "</tr>"
	str += "</table>"
	str += "<br>"
	str += "<form name='keygenForm' id='keygenForm' method='post' onsubmit='keygenSubmit();return;'>"
	str += "<input type='hidden' name='tMode' value='keygen'>"
	str += "<table align='center' cellpadding='3' width='530' cellspacing='1' bgcolor='#e1e1e1'>"
	str += "<tr bgcolor='#f7f7f7'>"
	str += "<td width='150'><div align='center' class='style4'>SMS ID</div></td>"
	str += "<td align='left'><span class='style4'><input name='smsid' type='text' id='uid'> ex) ezsms </span></td>"
	str += "</tr>"
	str += "<tr bgcolor='#f7f7f7'>"
	str += "<td width='150'><div align='center' class='style4'>SMS Password </div></td>"
	str += "<td align='left'><span class='style4'><input name='smspwd' type='password' id='pwd'> ex) ******** </span></td>"
	str += "</tr>"
	str += "<tr bgcolor='#f7f7f7'>"
	str += "<td width='150'><div align='center' class='style4'>SMS 적용 도메인</div></td>"
	str += "<td align='left'><span class='style4'><input name='smsdomain' type='text' id='smsdomain' size='40%'> ex) www.ezsms.kr </span></td>"
	str += "</tr>"
	str += "</table>"
	str += "<table border='0' align='center' cellpadding='3' cellspacing='0' width='530'>"
	str += "<tr>"
	str += "<td colspan='2' height='50' align='center'><button type='button' class='button-small blue' style='height:25px;' onclick='keygenSubmit();'>발급받기</button></td>"
	str += "</tr>"
	str += "</form>";
	str += "</table>"

	//var px = (screen.width / 2) - 540;
	var px = 204;
	var py = 390; 

	jQuery("#layerResult").html(str);
	jQuery("#layerResult").css({
		'z-index':"9999",
		'background-color':'#ffffff',
		'border':'1px solid #2781C4',
		'width':'540px',
		'height':'240px',
		'padding':'10px',
		'display':'block',
		'top':py + 'px',
		'left':px + 'px'
	});
}

function keygenSubmit(){
	var frm = document.keygenForm;
	var frm2 = document.frmInsert;

	if(!frm.smsid.value){
		alert("SMS ID를 입력해주세요.");
		frm.smsid.focus();
		return false;
	}else if(!frm.smspwd.value){
		alert("SMS Password를 입력해주세요.");
		frm.smspwd.focus();
		return false;
	}else if(!frm.smsdomain.value){
		alert("SMS 적용 도메인을 입력해주세요.");
		frm.smsdomain.focus();
		return false;
	}else{
		jQuery.ajax({
			type: 'post'
			, async: false
			, url: tUri + 'admin/proc/bbse-commerce-member-sms.exec.php'
			, data: jQuery("#keygenForm").serialize()
			, success: function(data){
				//alert("success forward : "+data);  // 메세지 출력
				var response = data.split("|||");// 첫번째 배열 
				
				// 분기 처리
				if(response[0] == "success"){
					alert("SMS Key 발급이 정상적으로 완료되었습니다.");
					frm2.sms_id.value = frm.smsid.value;
					frm2.sms_key.value = response[1];
					frm2.sms_keydate.value = response[2];
					popupResultHide();
				}else if(response[0] == "loginError"){
					alert("ID 또는 Password 를 확인하신후 이용하시기 바랍니다.");
				}else{
					alert('서버와의 통신이 실패했습니다.');
				}
			}
			, error: function(data, status, err){
				//alert("success forward : "+data);  // 메세지 출력
				alert('서버와의 통신이 실패했습니다.');
			}
		});	
	}
}

function smsIdInfo(){
	var frm = document.frmInsert;
	if(frm.sms_id.value){
		alert("SMS ID 변경은 SMS 키를 재발급 받으셔야 합니다.");
		return false;
	}
	if(!frm.sms_id.value){
		popupKeyGend();
		return false;
	}
}

function checkPoint(){
	var frm = document.frmInsert;
	var sms_id = frm.sms_id.value;
	var sms_key = frm.sms_key.value;
	var tMode = "smsPointCheck";

	if(!sms_id || !sms_key){
		jQuery("#pointResult").html("<font class=guide style='padding-left:0;color:#ff6701;'>SMS 아이디 및 인증키를 등록해주세요.</font>");
		return false;
	}else{
		jQuery.ajax({
			type: 'post'
			, async: false
			, url: tUri + 'admin/proc/bbse-commerce-member-sms.exec.php'
			, data: {tMode:tMode, sms_id:sms_id, sms_key:sms_key}
			, success: function(data){
				//alert("success forward : "+data);  // 메세지 출력
				var response = data.split("|||");// 첫번째 배열 
				
				// 분기 처리
				if(response[0] == "success"){
					var pointConf = response[2].split(",");
					var smsCnt = parseInt(parseInt(response[1]) / parseInt(pointConf[0]));
					var lmsCnt = parseInt(parseInt(response[1]) / parseInt(pointConf[1]));

					if(response[1]<=1000) var noticeMsg = "&nbsp;&nbsp;&nbsp;<font class=guide style='padding-left:0;color:#ff6701;'><= 포인트 충전이 필요합니다.</font>";
					else var noticeMsg = "";

					jQuery("#pointResult").html("<font class=guide style='padding-left:0;color:#0092e0;'>" + response[1] + " 포인트 (SMS : " + smsCnt + "건, LMS : " + lmsCnt + "건 발송 가능)</font>" + noticeMsg);
				}else if(response[0] == "loginError"){
					jQuery("#pointResult").html("<font class=guide style='padding-left:0;color:#ff6701;'>SMS ID 또는 인증키를 재설정 하신후 이용하시기 바랍니다.</font>");
				}else{
					jQuery("#pointResult").html("<font class=guide style='padding-left:0;color:#0092e0;'>서버와의 통신이 실패했습니다.</font>&nbsp;&nbsp;<button type='button' class='button-small blue' style='height:25px;' onClick='checkPoint();'>새로고침</button>");
				}
			}
			, error: function(data, status, err){
				//alert("success forward : "+data);  // 메세지 출력
				jQuery("#pointResult").html("<font class=guide style='padding-left:0;color:#0092E0;'>서버와의 통신이 실패했습니다.</font>");
			}
		});	
	}
}
</script>
<div>
	<?php
	if($saveFlag == 'save') echo '<div id="message" class="updated fade"><p><strong>SMS(문자)설정을 저장하였습니다.</strong></p></div>';
	elseif($saveFlag == 'reset') echo '<div id="message" class="updated fade"><p><strong>SMS(문자)설정을 초기화 하였습니다.</strong></p></div>';
	?>
	<div class="titleH5" style="margin:20px 0 10px 0; ">SMS/LMS(문자)설정 <a href="admin.php?page=bbse_commerce_member&cType=sms&mode=send" class="add-new-h2 member-sms-send">개별SMS발송</a></div>

	<div id="layerResult" style="position:absolute;display:none;"></div>
	<!--본문 내용(시작)-->
	<form name="frmInsert" id="frmInsert" method="post">
	<input type="hidden" name="tMode" value="">
	<input type="hidden" name="sms_keydate" value="<?php echo get_option('bbse_commerce_sms_keydate')?>">


	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2">페이지설정</th>
		</tr>
		<tr>
			<th>SMS/LMS 사용 여부</th>
			<td>
				<input type='radio' name='sms_use_yn' value='Y' style="border:0px;"<?php if($config['sms_use_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='sms_use_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_use_yn']) && $config['sms_use_yn'] == "N") echo " checked"?>>사용안함			
			</td>
		</tr>
		<tr>
			<th>SMS/LMS 시스템</th>
			<td>
				EZSMS&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.open('http://www.ezsms.kr');" class="button-small red" style="height:25px;">충전하기</button>&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.open('http://www.ezsms.kr');" class="button-small blue" style="height:25px;">발송내역</button>
			</td>
		</tr>
		<tr>
			<th>SMS/LMS 아이디</th>
			<td>
				<input type="text" name="sms_id" style="width:150px;" value="<?php echo get_option('bbse_commerce_sms_id')?>" readonly onclick="smsIdInfo();">
			</td>
		</tr>
		<tr>
			<th>SMS/LMS KEY</th>
			<td>
				<input type="text" name="sms_key" style="width:250px;" value="<?php echo get_option('bbse_commerce_sms_key')?>" readonly onclick="popupKeyGend();">&nbsp;&nbsp;&nbsp;<button type="button" class="button-small red" style="height:25px;" onclick="popupKeyGend();">키발급받기</button>
			</td>
		</tr>
		<tr>
			<th>SMS/LMS 충전금액</th>
			<td>
				<span id="pointResult">&nbsp;</span>
			</td>
		</tr>
		<tr>
			<th>발신자번호</th>
			<td>
				<input type="text" name="sms_callback_tel" value="<?php if(!empty($config['sms_callback_tel'])) echo $config['sms_callback_tel']?>" maxlength="15" style="width:150px;">&nbsp;&nbsp;&nbsp;&nbsp;<span class="guide" style="padding-left:0;color:#0092e0;">고객에게 보이는 번호입니다.</span>
			</td>
		</tr>
		<tr>
			<th>관리자수신번호</th>
			<td>
				<input type="text" name="sms_admin_tel" value="<?php if(!empty($config['sms_admin_tel'])) echo $config['sms_admin_tel']?>" style="width:300px;">&nbsp;&nbsp;&nbsp;&nbsp;<span class="guide" style="padding-left:0;color:#0092e0;">알림 문자를 받을 관리자 번호입니다. 번호를 2개이상 설정하실 경우 콤마(,)로 구분해 주세요.</span>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div class="titleH6" style="margin:10px 0 5px 0;">[SMS/LMS 안내]</div>
					- www.ezsms.kr 사이트의 회원가입을 하신 후 이용이 가능합니다.<br/>
					- 발송되는 문자의 길이에 의해 자동으로 SMS와 LMS로 변환되어 전송됩니다.<br/>
					&nbsp;&nbsp;(SMS와 LMS의 건별 요금을 참조해 주세요.)<br/>
					- SMS : 80Byte 이하(영문,숫자,공백 : 1Byte, 한글,영문대문자,특수문자:2Byte), LMS : 80Byte 초과
				</div>
			</td>
		</tr>
		<tr>
			<th colspan="2">SMS문구 치환변수&nbsp;&nbsp;&nbsp;&nbsp;<span class="guide" style="padding-left:0;color:#0092e0;">[각 변수는 SMS문구에 치환되어 발송됩니다]</th>
		</tr>
		<tr>
			<td colspan="2">
				<table cellpadding="5" cellspacing="0" width="100%">
				<?php
					foreach($replaceVars as $k=>$val) {
						echo "<tr><td width='150' style='border-bottom:1px dotted #cccccc;'>".$k."</td><td style='border-bottom:1px dotted #cccccc;'>".$val."</td></tr>";									
					}
				?>
				</table>
			</td>
		</tr>
		<tr>
			<th colspan="2">회원가입 SMS/LMS문구 설정</th>
		</tr>
		<tr>
			<td colspan="2">
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">회원가입 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_join_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_join_yn']) && $config['sms_join_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_join_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_join_yn']) && $config['sms_join_yn'] == "N") echo " checked"?>>사용안함</div>
					<div style="width:150px;float:left;padding-top:40px;">관리자 알림 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_join_admin_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_join_admin_yn']) && $config['sms_join_admin_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_join_admin_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_join_admin_yn']) && $config['sms_join_admin_yn'] == "N") echo " checked"?>>사용안함</div>
				</div>
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">회원가입시 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_join_msg" style="width:190px;height:90px;"><?php echo (!empty($config['sms_join_msg']))?$config['sms_join_msg']:$defaultUserMsg['join']?></textarea></div>
					<div style="width:150px;float:left;padding-top:40px;">회원가입시 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_join_admin_msg" style="width:190px;height:90px;" class='input_st'><?php echo (!empty($config['sms_join_admin_msg']))?$config['sms_join_admin_msg']:$defaultAdminMsg['join']?></textarea></div>
				</div>			
			</td>
		</tr>
		<tr>
			<th colspan="2">입금대기 SMS/LMS문구 설정</th>
		</tr>
		<tr>
			<td colspan="2">
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">입금대기 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_order_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_order_yn']) && $config['sms_order_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_order_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_order_yn']) && $config['sms_order_yn'] == "N") echo " checked"?>>사용안함</div>
					<div style="width:150px;float:left;padding-top:40px;">관리자 알림 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_order_admin_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_order_admin_yn']) && $config['sms_order_admin_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_order_admin_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_order_admin_yn']) && $config['sms_order_admin_yn'] == "N") echo " checked"?>>사용안함</div>
				</div>
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">입금대기 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_order_msg" style="width:190px;height:90px;"><?php echo (!empty($config['sms_order_msg']))?$config['sms_order_msg']:$defaultUserMsg['order']?></textarea></div>
					<div style="width:150px;float:left;padding-top:40px;">입금대기 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_order_admin_msg" style="width:190px;height:90px;" class='input_st'><?php echo (!empty($config['sms_order_admin_msg']))?$config['sms_order_admin_msg']:$defaultAdminMsg['order']?></textarea></div>
				</div>			
			</td>
		</tr>
		<tr>
			<th colspan="2">결제완료 SMS/LMS문구 설정</th>
		</tr>
		<tr>
			<td colspan="2">
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">결제완료 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_pay_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_pay_yn']) && $config['sms_pay_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_pay_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_pay_yn']) && $config['sms_pay_yn'] == "N") echo " checked"?>>사용안함</div>
					<div style="width:150px;float:left;padding-top:40px;">관리자 알림 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_pay_admin_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_pay_admin_yn']) && $config['sms_pay_admin_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_pay_admin_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_pay_admin_yn']) && $config['sms_pay_admin_yn'] == "N") echo " checked"?>>사용안함</div>
				</div>
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">결제완료 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_pay_msg" style="width:190px;height:90px;"><?php echo (!empty($config['sms_pay_msg']))?$config['sms_pay_msg']:$defaultUserMsg['pay']?></textarea></div>
					<div style="width:150px;float:left;padding-top:40px;">결제완료 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_pay_admin_msg" style="width:190px;height:90px;" class='input_st'><?php echo (!empty($config['sms_pay_admin_msg']))?$config['sms_pay_admin_msg']:$defaultAdminMsg['pay']?></textarea></div>
				</div>		
			</td>
		</tr>
		<tr>
			<th colspan="2">배송중 SMS/LMS문구 설정</th>
		</tr>
		<tr>
			<td colspan="2">
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">배송중 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_delivery_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_delivery_yn']) && $config['sms_delivery_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_delivery_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_delivery_yn']) && $config['sms_delivery_yn'] == "N") echo " checked"?>>사용안함</div>
					<div style="width:150px;float:left;padding-top:40px;">관리자 알림 문자&nbsp;</div>
					<div style="width:250px;float:left;padding-top:40px;"><input type='radio' name='sms_delivery_admin_yn' value='Y' style="border:0px;"<?php if(!empty($config['sms_delivery_admin_yn']) && $config['sms_delivery_admin_yn'] == "Y") echo " checked"?>>사용함&nbsp;&nbsp;<input type='radio' name='sms_delivery_admin_yn' value='N' style="border:0px;"<?php if(!empty($config['sms_delivery_admin_yn']) && $config['sms_delivery_admin_yn'] == "N") echo " checked"?>>사용안함</div>
				</div>
				<div style="width:100%;height:100px;vertical-align:middle;">
					<div style="width:150px;float:left;padding-top:40px;">배송중 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_delivery_msg" style="width:190px;height:90px;"><?php echo (!empty($config['sms_delivery_msg']))?$config['sms_delivery_msg']:$defaultUserMsg['delivery']?></textarea></div>
					<div style="width:150px;float:left;padding-top:40px;">배송중 즉시 발송&nbsp;</div>
					<div style="width:250px;float:left;"><textarea name="sms_delivery_admin_msg" style="width:190px;height:90px;" class='input_st'><?php echo (!empty($config['sms_delivery_admin_msg']))?$config['sms_delivery_admin_msg']:$defaultAdminMsg['delivery']?></textarea></div>
				</div>			
			</td>
		</tr>
	</table>
	</form>

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onclick="submitForm('admin.php?page=bbse_commerce_member&cType=sms'); return false;" style="width:150px;"> 저장 </button>
		<button type="button" class="button-bbse blue" onclick="resetForm('admin.php?page=bbse_commerce_member&cType=sms'); return false;" style="width:150px;"> 초기화 </button>
	</div>
	<div class="clearfix" style="height:20px;"></div>


	<!--본문 내용(끝)-->

</div>
<script language="javascript">
checkPoint();
</script>
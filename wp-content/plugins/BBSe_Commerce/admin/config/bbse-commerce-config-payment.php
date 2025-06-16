<?php
$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$cType."'");

if($cnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='".$cType."'");
	$data=unserialize($confData->config_data);
}

if(in_array('curl', get_loaded_extensions())) $curlInstalled="installed";
else $curlInstalled="";
?>
<script language="javascript">
	function open_pg_info(){
		/*
		var tbWidth = window.innerWidth * .35;
		var tbHeight = window.innerHeight * .65;
		*/
		var tbTitle="전자결제(PG) 서비스 안내 및 신청";
		var tbWidth = 800;
		var tbHeight = 700;
		tb_show(tbTitle, "http://www.bbsetheme.com/wp-content/themes/bbse_themeshop/part/category/category-bbse-commerce-pg_content1.php?width="+tbWidth+"&#38;height="+tbHeight+"&#38;TB_iframe=true");
		return false;
	}

	function close_pg_info(){
		tb_remove();
	}

	function config_submit(){
		if(!jQuery("input:checkbox[id='payment_card']").is(":checked") && !jQuery("input:checkbox[id='payment_bank']").is(":checked")
			&& !jQuery("input:checkbox[id='payment_trans']").is(":checked") && !jQuery("input:checkbox[id='payment_vbank']").is(":checked")){
			alert("결제수단을 선택해주세요.           ");
			return;
		}
		if(jQuery("input:checkbox[id='payment_card']").is(":checked") || jQuery("input:checkbox[id='payment_trans']").is(":checked") && jQuery("input:checkbox[id='payment_vbank']").is(":checked")){
			if(jQuery("#payment_agent").val()=='allthegate'){
				if(!jQuery("#payment_id").val()){
					alert("올더게이트의 상점 아이디를 입력해주세요.           ");
					jQuery("#payment_id").focus();
					return;
				}
			}
			else if(jQuery("#payment_agent").val()=='INIpay50'){
				if(!jQuery("#payment_id").val()){
					alert("KG 이시시스의 상점 아이디를 입력해주세요.           ");
					jQuery("#payment_id").focus();
					return;
				}
				if(jQuery("input[id='payment_inicis_nonActiveX_use']").eq(0).is(":checked")){
					if(!jQuery("#payment_key_path").val()){
						alert("KG 이시시스의 키(Key) 파일 경로를 입력해주세요.           ");
						jQuery("#payment_key_path").focus();
						return;
					}
					if(!jQuery("#payment_key_pw").val()){
						alert("KG 이시시스의 키(Key) 패스워드를 입력해주세요.           ");
						jQuery("#payment_key_pw").focus();
						return;
					}
				}
				else{
					if(!jQuery("#payment_sign_key").val()){
						alert("KG 이시시스의 가맹점 Sign Key를 입력해주세요.           ");
						jQuery("#payment_sign_key").focus();
						return;
					}
				}
				if(jQuery("input[id='payment_inicis_escorw_use']").eq(0).is(":checked")){
					if(!jQuery("#payment_inicis_escorw_id").val()){
						alert("KG 이시시스 에스크로 상점 아이디를 입력해주세요.           ");
						jQuery("#payment_inicis_escorw_id").focus();
						return;
					}
					if(jQuery("input[id='payment_inicis_escrow_nonActiveX_use']").eq(0).is(":checked")){
						if(!jQuery("#payment_inicis_escorw_key_path").val()){
							alert("KG 이시시스 에스크로 키(Key) 파일 경로를 입력해주세요.           ");
							jQuery("#payment_inicis_escorw_key_path").focus();
							return;
						}
						if(!jQuery("#payment_inicis_escorw_key_pw").val()){
							alert("KG 이시시스 에스크로 키(Key) 패스워드를 입력해주세요.           ");
							jQuery("#payment_inicis_escorw_key_pw").focus();
							return;
						}
					}
					else{
						if(!jQuery("#payment_escrow_sign_key").val()){
							alert("KG 이시시스의 에스크로 Sign Key를 입력해주세요.           ");
							jQuery("#payment_escrow_sign_key").focus();
							return;
						}
					}
					if(!jQuery("input:checkbox[id='payment_inicis_escorw_trans']").is(":checked") && !jQuery("input:checkbox[id='payment_inicis_escorw_vbank']").is(":checked")){
						alert("KG 이시시스 에스크로 적용 결제수단을 선택해주세요.           ");
						jQuery("#payment_inicis_escorw_trans").focus();
						return;
					}
				}
			}
		}

		if(confirm('결제모듈 설정을 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					var result = data; 
					if(result=='success'){
						alert('결제모듈 설정을 정상적으로 저장하였습니다.   ');
						go_config('payment');
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

	function change_agent(aType){
		var curlInstalled="<?php echo $curlInstalled;?>";
		if(aType=='allthegate'){
			jQuery("#key_path").hide();
			jQuery("#key_password").hide();
			jQuery("#inipay_escorw").hide();
			jQuery("#key_file_info").hide();
			jQuery("#mert_key").hide();
			jQuery("#mert_type").hide();
			jQuery("#curl_installed").hide();
			jQuery("#sign_key").hide();
			jQuery("#setEscor").show();
			jQuery("#lguplusXPay_nonActiveX").hide();
			jQuery("#inicis_nonActiveX").hide();
			jQuery("#alltheGate_nonActiveX").show();
		}
		else if(aType=='INIpay50'){
			jQuery("#inipay_escorw").show();
			jQuery("#key_file_info").show();
			jQuery("#mert_key").hide();
			jQuery("#mert_type").hide();
			jQuery("#curl_installed").hide();
			if(jQuery("input[id='payment_inicis_nonActiveX_use']").eq(0).is(":checked")){
				jQuery("#key_path").show();
				jQuery("#key_password").show();
				jQuery("#sign_key").hide();
			}
			else{
				jQuery("#key_path").hide();
				jQuery("#key_password").hide();
				jQuery("#sign_key").show();
			}
			jQuery("#setEscor").hide();
			jQuery("#lguplusXPay_nonActiveX").hide();
			jQuery("#inicis_nonActiveX").show();
			jQuery("#alltheGate_nonActiveX").hide();
		}
		else if(aType=='nicepg'){
			jQuery("#inipay_escorw").hide();
			jQuery("#key_file_info").hide();
		}
	}

	function inicis_escrow_view(tUse){
		if(tUse=='on'){
			jQuery(".inicisEscrow").show();
		}
		else{
			jQuery(".inicisEscrow").hide();
		}
	}

	function inicis_signKey(tUse){
		if(tUse=='Y'){
			jQuery("#sign_key").show();
			jQuery("#key_path").hide();
			jQuery("#key_password").hide();
		}
		else{
			jQuery("#sign_key").hide();
			jQuery("#key_path").show();
			jQuery("#key_password").show();
		}
	}

	function inicis_escrow_signKey(tUse){
		if(tUse=='Y'){
			jQuery("#inicisEscorw_sign_key").show();
			jQuery("#inicisEscorw_key_path").hide();
			jQuery("#inicisEscorw_key_pw").hide();
		}
		else{
			jQuery("#inicisEscorw_sign_key").hide();
			jQuery("#inicisEscorw_key_path").show();
			jQuery("#inicisEscorw_key_pw").show();
		}
	}

</script>
	<div style="margin:0 6px;">
		<div class="borderBox" style="margin:0 0 20px;">
			<table style="width:100%;">
			<tr>
				<td>
					- 인터넷 전자결제는(Pament Gateway) KG 이니시스(KG Inicis)를 사용합니다.<br/>
					- 인터넷 전자결제(Pament Gateway) 서비스 회사의 홈페이지에서 서비스 신청 및 아이디/키(Key) 발급을 받으신 후 사용이 가능합니다.<br/>
					&nbsp;&nbsp;KG 이니시스(KG Inicis) : <a href="http://www.inicis.com" target="_blank" title="KG 이니시스(KG Inicis) 홈페이지">http://www.inicis.com</a>
					<span style="color:#ae0000;">- 현금결제(계좌이체 및 가상계좌)를 사용하실 경우 에스크로를 의무적으로 적용하여야 합니다.</span>
				</td>
				<td width="200">
					<button type="button" class="button-bbse blue" onClick="open_pg_info();" style="width:170px;height:50px;line-height:17px;cursor:pointer;">전자결제(PG) 서비스<br>안내 및 신청</button>
				</td>
			</tr>
			</table>

		</div>
	</div>

	<div class="titleH5" style="margin:30px 0 10px 0; ">결제설정</div>
	<form name="cnfFrm" id="cnfFrm">
	<input type="hidden" name="cType" id="cType" value="payment" />
	<div>
		<table class="dataTbls overWhite collapse">
			<colgroup><col width="15%"><col width=""></colgroup>
			<tr>
				<th>결제수단</th>
				<td>
				<input type="checkbox" name="payment_card" id="payment_card" value="card" <?php echo ($data['payment_card']=='card')?"checked='checked'":"";?> /> 신용카드&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="payment_bank" id="payment_bank" value="bank" <?php echo ($data['payment_bank']=='bank')?"checked='checked'":"";?> /> 무통장입금&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="payment_trans" id="payment_trans" value="trans" <?php echo ($data['payment_trans']=='trans')?"checked='checked'":"";?> /> 실시간계좌이체&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="payment_vbank" id="payment_vbank" value="vbank" <?php echo ($data['payment_vbank']=='vbank')?"checked='checked'":"";?> /> 가상계좌<br/>
				<div style="color:#ae0000;line-height:22px;">
				※ 아직 KG 이니시스의 전자결제 시스템을 신청하지 않으셨다면 우측 상단 "전자결제시스템 안내 및 신청"에서 신청하시기 바랍니다.<br/>
				※ 무통장 입금을 사용하시려면 입금계좌설정을 하여야 합니다.
				</div>
				</td>
			</tr>
			<tr>
				<th>결제설정</th>
				<td>
					<div>
						<span style="width: 100px;display: inline-block;">결제대행사 선택 </span>
						<select name="payment_agent" id="payment_agent" onChange="change_agent(this.value);" style="margin-left:76px; width:100px;">
							<option value="INIpay50" <?php echo ($data['payment_agent']=="INIpay50")?"selected":""?>>KG 이니시스</option>
							<option value="nicepg" <?php echo ($data['payment_agent']=="nicepg")?"selected":""?>>나이스페이</option>
							<option value="lguplusXPay" <?php echo ($data['payment_agent']=="lguplusXPay")?"selected":""?>>LG 유플러스</option>
							<option value="allthegate" <?php echo ($data['payment_agent']=="allthegate")?"selected":""?>>올더게이트</option>
						</select>
					</div>
					
					<!--div id="mert_type" style="margin-left: 6px; display:<?php echo (!$data['payment_agent'] || $data['payment_agent']=="lguplusXPay")?"block":"none";?>;">
						- 결제형태 <input type="radio" name="payment_mert_type" value="service" <?php echo (!$data['payment_mert_type'] || $data['payment_mert_type']=='service')?"checked='checked'":"";?> style="margin-left:110px;" /> 실 서비스&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="payment_mert_type" value="test" <?php echo ($data['payment_mert_type']=='test')?"checked='checked'":"";?> /> 테스트<br>
					</div-->
					
					<div style="margin-left:6px;">
						<span style="width: 100px;display: inline-block;">- 상점 아이디 </span>
						<input type="text" name="payment_id" id="payment_id" value="<?php echo $data['payment_id']?>" 
							style="width:150px;margin-left:70px;" />&nbsp;
							<span class="prd-desc" style="margin-left:5px;">*  대/소문자를 정확히 입력해 주세요</span>
						</div>

					<input type="hidden" name="payment_inicis_nonActiveX_use" id="payment_inicis_nonActiveX_use" value="Y" />
					<div id="sign_key" style="margin-left:6px;">
						<span style="width: 100px;display: inline-block;">- <?php echo ( ($data['payment_agent'] != "nicepg") ? '가맹점 Sign Key':'상점 MertKey'); ?> </span>
						<input type="text" name="payment_sign_key" id="payment_sign_key" value="<?php echo $data['payment_sign_key']?>" 
							style="width:350px; margin-left:70px;" />
					</div>
					
					<div id="mert_key" style="margin-left:6px;display:<?php echo (!$data['payment_agent'] || $data['payment_agent']=="lguplusXPay")?"block":"none";?>;">
						- 상점 MertKey <input type="text" name="payment_mert_key" id="payment_mert_key" value="<?php echo $data['payment_mert_key']?>" style="width:280px;margin-left:80px;" /><span class="prd-desc" style="margin-left:5px;">*  LG U+의 상점관리자 페이지 -> 계약정보 -> 상점정보관리에서 확인하실 수 있습니다.</span><br />
					</div>


					<!--div id="key_path" style="margin-left:6px;">
						- 키(Key) 파일 경로 <input type="text" name="payment_key_path" id="payment_key_path" value="<?php echo $data['payment_key_path']?>" style="width:350px;margin-left:60px;" />/key<br />
					</div>

					<div id="key_password" style="margin-left:6px;">
						- 키 패스워드 <input type="text" name="payment_key_pw" id="payment_key_pw" value="<?php echo $data['payment_key_pw']?>" style="width:150px;margin-left:90px;" /><span class="prd-desc" style="margin-left:5px;">*  패스워드는 숫자 4자리로 Key 파일 생성 시의 패스워드입니다.</span>
					</div-->
					
					<div id="inipay_escorw" style="margin:30px 0 30px 6px; display: <?php echo ($data['payment_agent'] != "nicepg" ? 'block':'none') ?>" >
						
						<p style="font-weight:700;">[KG 이니시스 에스크로 설정]</p>
						<div style="margin-left:6px;">
							- 에스크로 사용여부 <input type="radio" name="payment_inicis_escorw_use" id="payment_inicis_escorw_use" onClick="inicis_escrow_view('on');" value="on" <?php echo ($data['payment_inicis_escorw_use']=="on")?"checked":""?> style="margin-left:55px;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="payment_inicis_escorw_use" id="payment_inicis_escorw_use" onClick="inicis_escrow_view('off');" value="off" <?php echo (!$data['payment_inicis_escorw_use'] || $data['payment_inicis_escorw_use']=="off")?"checked":""?> /> <span style="color:#ED1C24;">사용안함</span>
						</div>

						<div class="inicisEscrow" style="margin-left:6px;display:<?php echo ($data['payment_inicis_escorw_use']=="on")?"block":"none";?>;">
							<div>
								- 에스크로 상점 아이디 <input type="text" name="payment_inicis_escorw_id" id="payment_inicis_escorw_id" value="<?php echo $data['payment_inicis_escorw_id']?>" style="width:150px;margin-left:40px;" />&nbsp;<span class="prd-desc" style="margin-left:5px;">*  대/소문자를 정확히 입력해 주세요</span>
							</div>
							<input type="hidden" name="payment_inicis_escrow_nonActiveX_use" id="payment_inicis_escrow_nonActiveX_use"  value="Y" />
							<div id="inicisEscorw_sign_key" style="">
								- 에스크로 Sign Key <input type="text" name="payment_escrow_sign_key" id="payment_escrow_sign_key" value="<?php echo $data['payment_escrow_sign_key']?>" style="width:350px;margin-left:58px;" />
							</div>
							<div class="inicisEscrow">
								- 에스크로 적용 결제수단 <input type="checkbox" name="payment_inicis_escorw_trans" id="payment_inicis_escorw_trans" <?php echo ($data['payment_inicis_escorw_trans']=='on')?"checked":"";?> value="on" style="margin-left:30px;" /> 실시간계좌이체&nbsp;&nbsp;&nbsp;<input type="checkbox" name="payment_inicis_escorw_vbank" id="payment_inicis_escorw_vbank" <?php echo ($data['payment_inicis_escorw_vbank']=='on')?"checked":"";?> value="on" /> 가상계좌
							</div>
						</div>
					</div>

					<div id="key_file_info" style="margin-left:6px;display: <?php echo ($data['payment_agent'] != "nicepg" ? 'block':'none') ?>">
						<div style="margin-top:10px;line-height:22px;">
							<strong>[KG 이니시스 전자결제 환경설정]</strong><br />
						</div>
						<div style="margin:20px 0;line-height:18px;">
							&nbsp;&nbsp;<strong>웹표준 방식 : 가맹점 Sign Key</strong><br /><br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#ae0000;line-height:22px;">※ 웹표준 방식: 상품구매를 위해 필수로 설치해야 하는 ActiveX 모듈을 제거하고 웹표준 기술을 도입하여 신규 브라우져인 엣지 및 익스플로러(IE8 이상), 크롬, 파이어폭스에서 편리하게 결제를 제공할 수 있습니다.</span><br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;※ signKey 생성 조회: KG 이니시스 가맹점 관리자 접속 후 '상점정보' - '계약정보' - 좌측 '부가정보' 메뉴의 웹결제 signKey 생성 조회를 통해 발급받으신 signKey를 입력해 주세요.<br />
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; signKey 생성 조회를 사용하실 수 없는 경우 KG 이니시스(<a href="http://www.inicis.com" target="_blank" title="KG 이니시스(KG Inicis) 홈페이지">http://www.inicis.com</a>)로 문의해 주세요.
						</div>
					</div>
				</td>
			</tr>
		</table>
		<div class="clearfix"></div>
	</div>

	<div id="setEscor">
		<div class="titleH5" style="margin:30px 0 10px 0; ">에스크로 설정</div>
		<div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>에스크로 사용여부</th>
					<td>
						<input type="radio" name="payment_escrow_use" id="payment_escrow_use" value="on" <?php echo ($data['payment_escrow_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="payment_escrow_use" id="payment_escrow_use" value="off" <?php echo (!$data['payment_escrow_use'] || $data['payment_escrow_use']=='off')?"checked='checked'":"";?> /> 사용안함<br/>
						<span style="color:#ae0000;">
						※ 현금결제시 의무적으로 에스크로 결제를 허용해야 합니다.<br/>
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div>
	</form>

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>
	</div>


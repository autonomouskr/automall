<div>
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
		function open_ezpay_info(){
			/*
			var tbWidth = window.innerWidth * .35;
			var tbHeight = window.innerHeight * .65;
			*/
			var tbTitle="간편결제 서비스 안내 및 신청";
			var tbWidth = 800;
			var tbHeight = 700;
			tb_show(tbTitle, "http://www.bbsetheme.com/wp-content/themes/bbse_themeshop/part/category/category-bbse-commerce-easypay_content1.php?width="+tbWidth+"&#38;height="+tbHeight+"&#38;TB_iframe=true");
			return false;
		}


		function config_submit(){
			if(jQuery("input[id='paynow_use_yn']").eq(0).is(":checked")){
				if(!jQuery("#paynow_mert_id").val()){
					alert("[Paynow] 상점 아이디를 입력해 주세요.   ");
					jQuery("#paynow_mert_id").focus();
					return;
				}
				if(!jQuery("#paynow_mert_key").val()){
					alert("[Paynow] 상점 MertKey를 입력해 주세요.   ");
					jQuery("#paynow_mert_key").focus();
					return;
				}
			}
			if(jQuery("input[id='kakaopay_use_yn']").eq(0).is(":checked")){
				if(!jQuery("#kakaopay_mert_id").val()){
					alert("[KakaoPay] MID (상점 아이디)를 입력해 주세요.   ");
					jQuery("#kakaopay_mert_id").focus();
					return;
				}
				if(!jQuery("#kakaopay_auth_enckey").val()){
					alert("[KakaoPay] 어드민키를 입력해 주세요.   ");
					jQuery("#kakaopay_auth_enckey").focus();
					return;
				}
				/*
				if(!jQuery("#kakaopay_auth_hashkey").val()){
					alert("[KakaoPay] 인증요청용 HashKey를 입력해 주세요.   ");
					jQuery("#kakaopay_auth_hashkey").focus();
					return;
				}
				if(!jQuery("#kakaopay_mert_key").val()){
					alert("[KakaoPay] 상점키를 입력해 주세요.   ");
					jQuery("#kakaopay_mert_key").focus();
					return;
				}
				*/
			}
			/*
			if(jQuery("input[id='payco_use_yn']").eq(0).is(":checked")){
				if(!jQuery("input[id='payco_easy_buy']").eq(0).is(":checked") && !jQuery("input[id='payco_easy_pay']").eq(0).is(":checked")){
					alert("[PAYCO] 서비스 종류를 선택해 주세요.   ");
					jQuery("#payco_easy_buy").focus();
					return;
				}
				if(!jQuery("#payco_mert_id").val()){
					alert("[PAYCO] 상점 ID를 입력해 주세요.   ");
					jQuery("#payco_mert_id").focus();
					return;
				}
				if(!jQuery("#payco_mert_code").val()){
					alert("[PAYCO] 가맹점코드를 입력해 주세요.   ");
					jQuery("#payco_mert_code").focus();
					return;
				}
			}
			*/
			if(jQuery("input[id='kpay_use_yn']").eq(0).is(":checked")){
				if(!jQuery("#kpay_mert_id").val()){
					alert("[KPAY] 상점 아이디를 입력해 주세요.   ");
					jQuery("#kpay_mert_id").focus();
					return;
				}
				if(!jQuery("#kpay_key_path").val()){
					alert("[KPAY] 키(Key) 파일 경로를 입력해 주세요.   ");
					jQuery("#kpay_key_path").focus();
					return;
				}
				if(!jQuery("#kpay_key_pw").val()){
					alert("[KPAY] 키 패스워드를 입력해 주세요.   ");
					jQuery("#kpay_key_pw").focus();
					return;
				}
				if(jQuery("input[id='kpay_escrow_yn']").eq(0).is(":checked")){
					if(!jQuery("#kpay_escrow_mert_id").val()){
						alert("[KPAY] 에스크로 상점 아이디를 입력해 주세요.   ");
						jQuery("#kpay_escrow_mert_id").focus();
						return;
					}
					if(!jQuery("#kpay_escrow_key_path").val()){
						alert("[KPAY] 에스크로 키(Key) 파일 경로를 입력해 주세요.   ");
						jQuery("#kpay_escrow_key_path").focus();
						return;
					}
					if(!jQuery("#kpay_escrow_key_pw").val()){
						alert("[KPAY] 에스크로 키 패스워드를 입력해 주세요.   ");
						jQuery("#kpay_escrow_key_pw").focus();
						return;
					}
				}
			}

			if(confirm('간편결제 설정을 저장하시겠습니까?     ')){
				jQuery.ajax({
					type: 'post', 
					async: false, 
					url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
					data: jQuery("#cnfFrm").serialize(), 
					success: function(data){
						//alert(data);
						var result = data; 
						if(result=='success'){
							alert('간편결제 설정을 정상적으로 저장하였습니다.   ');
							go_config('ezpay');
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

		function inicis_escrow_view(tUse){
			if(tUse=='Y') jQuery(".kpayEscrowInfo").show();
			else jQuery(".kpayEscrowInfo").hide();
		}
	</script>	

	<!--본문 내용(시작)-->
	<div style="margin:0 6px;">
		<div class="borderBox" style="margin:0 0 20px;">
			<table style="width:100%;">
			<tr>
				<td>
					* 카카오페이 홈페이지에서 서비스 신청 및 아이디/키(Key) 발급을 받으신 후 사용이 가능합니다.<br />
					KakaoPay(Daum Kakao Corp) : <a href="https://developers.kakao.com/product/kakaoPay" target="_blank" title="KakaoPay(Daum Kakao Corp) 홈페이지">https://developers.kakao.com/product/kakaoPay</a>
				</td>
				<td width="200">
					<button type="button" class="button-bbse blue" onClick="open_ezpay_info();" style="width:170px;height:50px;line-height:17px;cursor:pointer;">간편결제 서비스<br>안내 및 신청</button>
				</td>
			</tr>
			</table>
		</div>
	</div>

	<form name="cnfFrm" id="cnfFrm">
	<input type="hidden" name="cType" id="cType" value="ezpay" />

	<!--table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/ezpay_paynow.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="Paynow(LG U+)"/></th>
		</tr>
		<tr>
			<th>Paynow 사용여부</th>
			<td>
				<input type='radio' name='paynow_use_yn' id='paynow_use_yn' value='Y' <?php if($data['paynow']['paynow_use_yn'] == "Y") echo " checked"?> style="border:1px solid #ddd;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='paynow_use_yn' id='paynow_use_yn' value='N' <?php if(empty($data['paynow']['paynow_use_yn']) || $data['paynow']['paynow_use_yn'] == "N") echo " checked"?> style="border:1px solid #ddd;" /> 사용안함			
			</td>
		</tr>
		<tr>
			<th>결제형태</th>
			<td>
				<input type="radio" name="paynow_mert_type" value="service" <?php echo (!$data['paynow']['paynow_mert_type'] || $data['paynow']['paynow_mert_type']=='service')?"checked='checked'":"";?> /> 실 서비스&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="paynow_mert_type" value="test" <?php echo ($data['paynow']['paynow_mert_type']=='test')?"checked='checked'":"";?> /> 테스트
			</td>
		</tr>

		<tr>
			<th>상점 아이디</th>
			<td>
				<input type="text" name="paynow_mert_id" id="paynow_mert_id" style="width:500px;" value="<?php echo $data['paynow']['paynow_mert_id'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;">*  대/소문자를 정확히 입력해 주세요</span>
			</td>
		</tr>
		<tr>
			<th>상점 MertKey</th>
			<td>
				<input type="text" name="paynow_mert_key" id="paynow_mert_key" style="width:500px;" value="<?php echo $data['paynow']['paynow_mert_key'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;">*  LG U+의 상점관리자 페이지 -> 계약정보 -> 상점정보관리에서 확인하실 수 있습니다.</span>
			</td>
		</tr>
		<tr>
			<th>에스크로 사용여부</th>
			<td>
				<input type='radio' name='paynow_escrow_yn' id='paynow_escrow_yn' value='Y' <?php if($data['paynow']['paynow_escrow_yn'] == "Y") echo " checked"?> style="border:1px solid #ddd;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='paynow_escrow_yn' id='paynow_escrow_yn' value='N' <?php if(empty($data['paynow']['paynow_escrow_yn']) || $data['paynow']['paynow_escrow_yn'] == "N") echo " checked"?> style="border:1px solid #ddd;" /> 사용안함			
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div style="margin-top:10px;line-height:22px;">
						<strong>[Paynow(LG U+) 환경설정]</strong><br />
						<span style="color:#ae0000;">* 인터넷 전자결제(Pament Gateway) 서비스가 LG 유플러스(LG U+)인 경우, 인터넷 전자결제(Pament Gateway) 서비스와 동일한 상점아이디와  상점 MertKey를 입력해 주세요.</span><br />
						<span style="color:#ae0000;">* 인터넷 전자결제(Pament Gateway) 서비스가 LG 유플러스(LG U+)가 아닌 경우, LG 유플러스(LG U+)와 Paynow 서비스 계약을 완료하신 후 LG 유플러스(LG U+)로 부터 발급받은  상점아이디와  상점 MertKey를 입력해 주세요.</span><br /><br />
						*  테스트 완료 후 실 서비스를 하시는 경우, '실 서비스'를 선택해 주셔야 정상적인 결제가 진행됩니다.<br />
						*  LG 유플러스(LG U+) 상점아이디는 실 서비스 / 테스트 모두 <span style="color:#ae0000;">실 서비스용 상점아이디를 입력해 주세요.</span><br />
						<?php if(!$curlInstalled){?>
						<span style="color:#ae0000;">- CURL 을 사용할 수 없습니다.</span><br />
						- LG 유플러스(LG U+) 전자결제를 위해 CURL 컴포넌트를 설치 및 활성화 시켜 주세요.<br/>
						<?php }?>
					</div>
				</div>
			</td>
		</tr>
	</table-->

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/ezpay_kakaopay.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="KakaoPay(Daum Kakao Corp)"/></th>
		</tr>
		<tr>
			<th>KakaoPay 사용여부</th>
			<td>
				<input type='radio' name='kakaopay_use_yn' id='kakaopay_use_yn' value='Y' <?php if($data['kakaopay']['kakaopay_use_yn'] == "Y") echo " checked"?> style="border:1px solid #ddd;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='kakaopay_use_yn' id='kakaopay_use_yn' value='N' <?php if(empty($data['kakaopay']['kakaopay_use_yn']) || $data['kakaopay']['kakaopay_use_yn'] == "N") echo " checked"?> style="border:1px solid #ddd;" /> 사용안함			
			</td>
		</tr>
		<tr>
			<th>CID(가맹점 코드)</th>
			<td>
				<input type="text" name="kakaopay_mert_id" id="kakaopay_mert_id" style="width:500px;" value="<?php echo $data['kakaopay']['kakaopay_mert_id'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;">*  대/소문자를 정확히 입력해 주세요</span>
			</td>
		</tr>
		<tr>
			<th>Admin Key(앱키)</th>
			<td>
				<input type="text" name="kakaopay_auth_enckey" id="kakaopay_auth_enckey" style="width:500px;" value="<?php echo $data['kakaopay']['kakaopay_auth_enckey'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;"></span>
			</td>
		</tr>
		<!--tr>
			<th>인증요청용 HashKey</th>
			<td>
				<input type="text" name="kakaopay_auth_hashkey" id="kakaopay_auth_hashkey" style="width:500px;" value="<?php echo $data['kakaopay']['kakaopay_auth_hashkey'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;"></span>
			</td>
		</tr-->
		<!--tr>
			<th>가맹점 코드 인증키</th>
			<td>
				<input type="text" name="kakaopay_mert_key" id="kakaopay_mert_key" style="width:700px;" value="<?php echo $data['kakaopay']['kakaopay_mert_key'];?>">
			</td>
		</tr-->
		<!--tr>
			<th>에스크로 사용여부</th>
			<td>
				<input type='hidden' name='kakaopay_escrow_yn' id='kakaopay_escrow_yn' value='N' /> <span style="color:#ae0000;">* KakaoPay는 에스크로 서비스를 지원하지 않습니다.</span>
			</td>
		</tr-->
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div style="margin-top:10px;line-height:22px;">
						<strong>[카카오페이 환경설정]</strong><br />
						<span style="color:#ae0000;">* KakaoPay(Daum Kakao Corp)로 부터 발급받은  CID, Admin 키값을 입력 후 저장해주세요.</span><br />
						<a href="https://developers.kakao.com/" target="_blank">Kakao developers 어플리케이션 등록하고 키값받기</a>
					</div>
				</div>
			</td>
		</tr>
	</table>
	<br />	<br />

	<!--table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/ezpay_payco.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="PAYCO(NHN Entertainment)"/></th>
		</tr>
		<tr>
			<th>PAYCO 사용여부</th>
			<td>
				<input type='radio' name='payco_use_yn' id='payco_use_yn' value='Y' <?php if($data['payco']['payco_use_yn'] == "Y") echo " checked"?> style="border:1px solid #ddd;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='payco_use_yn' id='payco_use_yn' value='N' <?php if(empty($data['payco']['payco_use_yn']) || $data['payco']['payco_use_yn'] == "N") echo " checked"?> style="border:1px solid #ddd;" /> 사용안함			
			</td>
		</tr>
		<tr>
			<th>서비스 선택</th>
			<td>
				<input type="checkbox" name="payco_easy_buy" id="payco_easy_buy" value="Y" style="border:1px solid #ddd;" <?php echo($data['payco']['payco_easy_buy']=='Y')?"checked":"";?> /> 간편구매&nbsp;&nbsp;&nbsp;<input type="checkbox" name="payco_easy_pay" id="payco_easy_pay" value="Y" style="border:1px solid #ddd;" <?php echo($data['payco']['payco_easy_pay']=='Y')?"checked":"";?> /> 간편결제
			</td>
		</tr>
		<tr>
			<th>상점 ID</th>
			<td>
				<input type="text" name="payco_mert_id" id="payco_mert_id" style="width:500px;" value="<?php echo $data['payco']['payco_mert_id'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;">*  대/소문자를 정확히 입력해 주세요</span>
			</td>
		</tr>
		<tr>
			<th>가맹점코드</th>
			<td>
				<input type="text" name="payco_mert_code" id="payco_mert_code" style="width:500px;" value="<?php echo $data['payco']['payco_mert_code'];?>">
			</td>
		</tr>
		<tr>
			<th>에스크로 사용여부</th>
			<td>
				<input type='radio' name='payco_escrow_yn' id='payco_escrow_yn' value='Y' <?php if($data['payco']['payco_escrow_yn'] == "Y") echo " checked"?> style="border:1px solid #ddd;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='payco_escrow_yn' id='payco_escrow_yn' value='N' <?php if(empty($data['payco']['payco_escrow_yn']) || $data['payco']['payco_escrow_yn'] == "N") echo " checked"?> style="border:1px solid #ddd;" /> 사용안함			
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div style="margin-top:10px;line-height:22px;">
						<strong>[PAYCO(NHN Entertainment) 환경설정]</strong><br />
						<span style="color:#ae0000;">* PAYCO(NHN Entertainment)로 부터 발급받은  상점 ID와  상점 가맹점코드를 입력해 주세요.</span><br />
						* 간편구매/ 간편결제 : 간편구매(상품상세 페이지에 'PAYCO 구매' 버튼 노출), 간편결제(주문서 작성 시 간편결제 방식으로 PAYCO 노출)<br />
					</div>
				</div>
			</td>
		</tr>
	</table>
	<br />	<br /-->

	<!--table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/ezpay_kpay.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="KPAY(KG Inicis)"/></th>
		</tr>
		<tr>
			<th>KPAY 사용여부</th>
			<td>
				<input type='radio' name='kpay_use_yn' id='kpay_use_yn' value='Y' <?php if($data['kpay']['kpay_use_yn'] == "Y") echo " checked"?> style="border:1px solid #ddd;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='kpay_use_yn' id='kpay_use_yn' value='N' <?php if(empty($data['kpay']['kpay_use_yn']) || $data['kpay']['kpay_use_yn'] == "N") echo " checked"?> style="border:1px solid #ddd;" /> 사용안함			
			</td>
		</tr>
		<tr>
			<th>상점 아이디</th>
			<td>
				<input type="text" name="kpay_mert_id" id="kpay_mert_id" style="width:500px;" value="<?php echo $data['kpay']['kpay_mert_id'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;">*  대/소문자를 정확히 입력해 주세요</span>
			</td>
		</tr>
		<tr>
			<th>키(Key) 파일 경로</th>
			<td>
				<input type="text" name="kpay_key_path" id="kpay_key_path" value="<?php echo $data['kpay']['kpay_key_path']?>" style="width:350px;" />/key
			</td>
		</tr>
		<tr>
			<th>키 패스워드</th>
			<td>
				<input type="text" name="kpay_key_pw" id="kpay_key_pw" value="<?php echo $data['kpay']['kpay_key_pw']?>" style="width:150px;" /><span class="prd-desc" style="margin-left:5px;">*  패스워드는 숫자 4자리로 Key 파일 생성 시의 패스워드입니다.</span>
			</td>
		</tr>
		<tr>
			<th>에스크로 사용여부</th>
			<td>
				<input type='radio' name='kpay_escrow_yn' id='kpay_escrow_yn' onClick="inicis_escrow_view('Y');" value='Y' <?php if($data['kpay']['kpay_escrow_yn'] == "Y") echo " checked"?> style="border:1px solid #ddd;" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='kpay_escrow_yn' id='kpay_escrow_yn' onClick="inicis_escrow_view('N');" value='N' <?php if(empty($data['kpay']['kpay_escrow_yn']) || $data['kpay']['kpay_escrow_yn'] == "N") echo " checked"?> style="border:1px solid #ddd;" /> 사용안함			

				<div class="kpayEscrowInfo" style="margin-top:5px;margin-left:6px;display:<?php echo ($data['kpay']['kpay_escrow_yn']=="Y")?"block":"none";?>;">
					- 에스크로 상점 아이디&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="kpay_escrow_mert_id" id="kpay_escrow_mert_id" style="width:500px;" value="<?php echo $data['kpay']['kpay_escrow_mert_id'];?>">&nbsp;<span class="prd-desc" style="margin-left:5px;">*  대/소문자를 정확히 입력해 주세요</span>
				</div>

				<div class="kpayEscrowInfo" style="margin-left:6px;display:<?php echo ($data['kpay']['kpay_escrow_yn']=="Y")?"block":"none";?>;">
					- 에스크로 키(Key) 파일 경로&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="kpay_escrow_key_path" id="kpay_escrow_key_path" value="<?php echo $data['kpay']['kpay_escrow_key_path']?>" style="width:350px;" />/key
				</div>

				<div class="kpayEscrowInfo" style="margin-left:6px;display:<?php echo ($data['kpay']['kpay_escrow_yn']=="Y")?"block":"none";?>;">
					- 에스크로 키 패스워드&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="kpay_escrow_key_pw" id="kpay_escrow_key_pw" value="<?php echo $data['kpay']['kpay_escrow_key_pw']?>" style="width:150px;" /><span class="prd-desc" style="margin-left:5px;">*  패스워드는 숫자 4자리로 Key 파일 생성 시의 패스워드입니다.</span>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div style="margin-top:10px;line-height:22px;">
						<strong>[KPAY(KG Inicis) 환경설정]</strong><br />
						<span style="color:#ae0000;">* 인터넷 전자결제(Pament Gateway) 서비스가 KG 이니시스(KG Inicis)인 경우, 인터넷 전자결제(Pament Gateway) 서비스와 동일한 상점아이디와  키(Key) 파일 경로, 키 패스워드를 입력해 주세요.</span><br />
						<span style="color:#ae0000;">* 인터넷 전자결제(Pament Gateway) 서비스가 KG 이니시스(KG Inicis)가 아닌 경우, KG 이니시스(KG Inicis)와 Kpay 서비스 계약을 완료하신 후 KG 이니시스(KG Inicis)로 부터 발급받은  상점아이디와  키(Key) 파일 경로, 키 패스워드를 입력해 주세요.</span><br /><br />

						- 키 패스워드는 KG 이니시스의 상점관리자 패스워드가 아니며, 키 패스워드를 확인하시려면 상점측에 발급 된 키파일 안의 readme.txt 파일을 참조해 주세요.<br/><br />
						<span style="color:#ae0000;">- Web으로 접근할수없는 경로에 inicis라는 디렉토리를 생성하신 후, inicis 디렉토리 내에 key 디렉토리와 log 디렉토리를 생성해 주세요.</span><br />
						&nbsp;&nbsp;예) 계정의 루트 디렉토리(www, html, public_html 등) 밖에 inicis 디렉토리 생성 -> inicis 디렉토리 내에 key 디렉토리와 log 디렉토리 생성<br /><br />
						- key 디렉토리 : chmod –R 755 key 접근/읽기/쓰기 권한을 가지도록 조정<br/>
						- log 디렉토리 : chmod –R 777 log 접근/읽기/쓰기 권한을 가지도록 조정<br/>
						<span style="color:#ae0000;">- KG 이니시스로 부터 발급 받은 키파일의 압축을 푸시면, KG 이니시스 ID 로 된 디렉토리가 나타납니다.<br/>
						&nbsp;&nbsp;이 KG 이니시스 ID 로 된 디렉토리 전체를 생성하신 inicis/key 디렉토리에 업로드 해 주세요.</span><br /><br />
						- 키 파일 업로드 완료 후, '키(Key) 파일 경로' 입력란에 키 파일이 설치 된 서버의 절대경로를 입력해 주세요.(경로 입력 시 /key 제외)<br/>
						&nbsp;&nbsp;예) /home/abcde/inicis/key 인 경우 /home/abcde/inicis 만 입력<br /><br/>
						<span style="color:#ae0000;">- [pgcert.pem], [rndseed.binary] 2개의 파일을 key폴더 내 업로드 합니다.</span><br />
						&nbsp;&nbsp;해당 파일은 이니시스 계약 시 발급받으실 수 있으며, <br />
						&nbsp;&nbsp;발급받지 못했을 경우 이니시스에 문의하시거나 아래 [파일 다운로드]를 클릭하시어 다운받은 후 압축해제 하시어 업로드 하시기 바랍니다. &nbsp;&nbsp;<a href="http://www.bbsetheme.com/wp-content/themes/bbse_themeshop/inicis_pem_binary/inicis_pem_binary.zip" title="이니시스 pem 및 binary 파일"><button type="button" class="button-small blue" style="width:80px;height:25px;">다운로드</button></a> <br /><br />
						<span style="color:#ae0000;">- 에스크로 사용 설정 시 위와 같은 방법으로 추가 설정합니다.</span><br />
						&nbsp;&nbsp;(이니시스의 경우 에스크로를 위한 아이디, 키가 별도 발급됩니다.)<br />
					</div>
				</div>
			</td>
		</tr>
	</table-->

	</form>
	<br />

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onclick="config_submit(); return false;" style="width:150px;"> 등록/저장 </button>
	</div>
	<div class="clearfix" style="height:20px;"></div>


	<!--본문 내용(끝)-->

</div>

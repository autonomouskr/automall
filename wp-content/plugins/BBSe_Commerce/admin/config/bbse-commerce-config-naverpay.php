<?php
$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$cType."'");

if($cnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='".$cType."'");
	$data=unserialize($confData->config_data);
}

$goodsCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE goods_display<>'hidden'");
?>
<script language="javascript">
	function change_use_naverpay(tUse){
		if(tUse=='on') jQuery(".naverPayOn").css("display","table-row");
		else jQuery(".naverPayOn").css("display","none");
	}

	function config_submit(){
		if(jQuery("input[id='naver_pay_use']:checked").val()=='on'){
			if(!jQuery("#naver_pay_id").val()){
				alert("네이버 페이 ID를 입력해 주세요.     ");
				jQuery("#naver_pay_id").focus();
				return;
			}
			if(!jQuery("#naver_pay_auth_key").val()){
				alert("가맹점 인증키를 입력해 주세요.     ");
				jQuery("#naver_pay_auth_key").focus();
				return;
			}
			if(!jQuery("#naver_pay_button_key").val()){
				alert("버튼 인증키를 입력해 주세요.     ");
				jQuery("#naver_pay_button_key").focus();
				return;
			}
			if(!jQuery("#naver_pay_common_key").val()){
				alert("네이버 공통 인증키를 입력해 주세요.     ");
				jQuery("#naver_pay_common_key").focus();
				return;
			}
		}

		if(confirm('네이버페이(체크아웃) 설정을 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('네이버페이(체크아웃) 설정을 정상적으로 저장하였습니다.   ');
						go_config('naverpay');
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

	function change_goods(){
		if(confirm("전체 상품을 '네이버페이 적용'으로 수정하시겠습니까?     ")){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: {cType:"applynaverpay"}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert("전체 상품을 '네이버페이 적용'으로 수정하였습니다.   ");
						go_config('naverpay');
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
		<input type="hidden" name="cType" id="cType" value="naverpay" />
			<div class="titleH5" style="margin:20px 0 10px 0; ">네이버 페이(체크아웃) 설정</div>

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>네이버페이 주문형 사용여부</th>
					<td><input type="radio" name="naver_pay_use" id="naver_pay_use" onClick="change_use_naverpay('on');" value="on" <?php echo ($data['naver_pay_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="naver_pay_use" id="naver_pay_use" onClick="change_use_naverpay('off');" value="off" <?php echo (!$data['naver_pay_use'] || $data['naver_pay_use']=='off')?"checked='checked'":"";?> /> 사용안함</td>
				</tr>
				<tr class="naverPayOn" style="display:<?php echo ($data['naver_pay_use']=='on' && $goodsCnt>'0')?"table-row":"none";?>;">
					<th>전체 상품 일괄 적용</th>
					<td><button type="button" class="button-small red" onclick="change_goods();" style="width:160px;height:25px;">전체 상품 사용함 적용</button></td>
				</tr>
				<tr>
					<th>비회원 장바구니</th>
					<td>
						<input type="radio" name="guest_cart_use" id="guest_cart_use" value="on" <?php echo ($data['guest_cart_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="guest_cart_use" id="guest_cart_use" value="off" <?php echo (!$data['guest_cart_use'] || $data['guest_cart_use']=='off')?"checked='checked'":"";?> /> 사용안함<br />
						<span style="color:#ae0000;">
						※ 네이버페이(체크아웃) 이용 시 비회원 장바구니를 사용함으로 설정하셔야 합니다.
						</span>
					</td>
				</tr>
				<tr class="naverPayOn" style="display:<?php echo ($data['naver_pay_use']!='on')?"none":"table-row";?>;">
					<th>회원 네이버페이 노출여부</th>
					<td><input type="radio" name="member_naver_pay_use" id="member_naver_pay_use" value="on" <?php echo ($data['member_naver_pay_use']=='on')?"checked='checked'":"";?> /> 노출함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="member_naver_pay_use" id="member_naver_pay_use"  value="off" <?php echo (!$data['member_naver_pay_use'] || $data['member_naver_pay_use']=='off')?"checked='checked'":"";?> /> 노출안함<br />
						<span style="color:#ae0000;">
						※ 네이버페이(체크아웃) 결제 버튼을 회원에게도 노출을 원하시는 경우 노출함으로 설정해 주세요.
						</span></td>
				</tr>
				<tr class="naverPayOn" style="display:<?php echo ($data['naver_pay_use']!='on')?"none":"table-row";?>;">
					<th>실적용 여부</th>
					<td><input type="radio" name="naver_pay_type" id="naver_pay_type" value="test" <?php echo (!$data['naver_pay_type'] || $data['naver_pay_type']=='test')?"checked='checked'":"";?> /> 테스트&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="naver_pay_type" id="naver_pay_type" value="real" <?php echo ($data['naver_pay_type']=='real')?"checked='checked'":"";?> /> 실적용</td>
				</tr>
				<tr class="naverPayOn" style="display:<?php echo ($data['naver_pay_use']!='on')?"none":"table-row";?>;">
					<th>네이버 페이 ID</th>
					<td><input type="text" name="naver_pay_id" id="naver_pay_id" value="<?php echo $data['naver_pay_id'];?>" style="width:300px;" /></td>
				</tr>
				<tr class="naverPayOn" style="display:<?php echo ($data['naver_pay_use']!='on')?"none":"table-row";?>;">
					<th>가맹점 인증키</th>
					<td><input type="text" name="naver_pay_auth_key" id="naver_pay_auth_key" value="<?php echo $data['naver_pay_auth_key'];?>" style="width:300px;" /></td>
				</tr>
				<tr class="naverPayOn" style="display:<?php echo ($data['naver_pay_use']!='on')?"none":"table-row";?>;">
					<th>버튼 인증키</th>
					<td><input type="text" name="naver_pay_button_key" id="naver_pay_button_key" value="<?php echo $data['naver_pay_button_key'];?>" style="width:300px;" /></td>
				</tr>
				<tr class="naverPayOn" style="display:<?php echo ($data['naver_pay_use']!='on')?"none":"table-row";?>;">
					<th>네이버 공통 인증키</th>
					<td><input type="text" name="naver_pay_common_key" id="naver_pay_common_key" value="<?php echo $data['naver_pay_common_key'];?>" style="width:300px;" /></td>
				</tr>
				
				<tr>
					<th>네이버페이 결제형 사용여부</th>
					<td><input type="radio" name="naver_pay_use2" id="naver_pay_use2" value="on" <?php echo ($data['naver_pay_use2']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="naver_pay_use2" id="naver_pay_use2" value="off" <?php echo (!$data['naver_pay_use2'] || $data['naver_pay_use2']=='off')?"checked='checked'":"";?> /> 사용안함</td>
				</tr>
				<tr class="npay_client_id">
					<th>간편결제 CLIENT ID</th>
					<td><input type="text" name="npay_client_id" id="npay_client_id" value="<?php echo get_option('bbse_npay_client_id');?>" style="width:300px;" /></td>
				</tr>
				<tr class="npay_client_secret">
					<th>간편결제 CLIENT SECRET</th>
					<td><input type="text" name="npay_client_secret" id="npay_client_secret" value="<?php echo get_option('bbse_npay_client_secret');?>" style="width:300px;" /></td>
				</tr>
			</table>

			<div class="clearfix"></div>
		</form>
		<div class="clearfix" style="margin-top:30px;"></div>
		<div class="borderBox">
			<div class="titleH6" style="margin:10px 0 5px 0;">[네이버 페이(체크아웃) 승인심사 안내]</div>
			<span class="emRed">* 버튼미노출 상품 중 일부 상품은 (렌탈/정기결제/분할배송/동영상,강좌강의등 비실물)은 버튼미노출과 상세페이지에 "네이버페이 구매불가" 문구를 넣어주셔야됩니다.</span><br /><br />
			- 실적용 여부 : 네이버 페이의 승인 심사는' 테스트' 상태에서 이루어 지며, 네이버 페이 최종승인 완료 후 서비스 오픈시 '실적용'을 선택해 주세요.<br />
			- 인터넷 전자결제는(Pament Gateway) 신용카드, 실시간계좌이체, 가상계좌 등을 이용할 수 있으며,  실시간계좌이체, 가상계좌는 에스크로(구매자보호서비스)가 적용되어 있어야 합니다.<br />
			- 또한, 에스크로(구매자보호서비스) 수수료는 판매자(쇼핑몰) 측에서 부담하여야 합니다. (PG 사에 전화연락으로 설정 변경)<br />
			- 결제 방법 별 에스크로(구매자보호서비스) 계약 및 에스크로(구매자보호서비스) 수수료 부담자 변경은 인터넷 전자결제(Pament Gateway)사에 직접 연락하셔서 설정을 변경하셔야 합니다.<br /><br />

			- 위의 기본 심사가 완료 되면 네이버페이로 부터 신청자 이메일로 가맹점 인증키, 버튼 인증키, 네이버 공통 인증키를 발급 받게됩니다 .<br />
			- 발급 받으신 가맹점 인증키, 버튼 인증키, 네이버 공통 인증키를 입력하신 후 테스트 상태로 최종 승인을 위한 심사를 받게됩니다 .<br /><br />

			<span class="emRed">* 네이버 페이 구매하기/찜 버튼은 비회원(로그아웃) 상태에서 만 노출되며, 회원 로그인 또는 품절상품인 경우에는 노출되지 않습니다 .</span><br /><br />

			- 테스트 상태에서는 네이버 페이 버튼이 보이지 않습니다. 테스트 상태에서 네이버 페이 버튼을 확인 하기 위해서는, 페이지 URL 뒤에 npayTest=true 라는 파라미터를 붙여 주셔야 합니다.<br />
			&nbsp;&nbsp;이는 테스트 상태에서는 쇼핑몰 이용 고객에게 네이버 페이관련 버튼을 노출하지 않기 위함이며, 실적용 시에는 해당 파라미터 없이도 네이버 페이 버튼이 보이게 됩니다.<br />
			&nbsp;&nbsp;예) <?php echo home_url();?>/?bbseGoods=1&npayTest=true<br /><br />

			* 자세한 사항은 <a href="https://admin.pay.naver.com/" target="_blank">'네이버 페이 센터'</a>를 방문하셔서 확인 해 주세요.<br /><br />

			<div class="titleH6" style="margin:10px 0 5px 0;">[네이버 페이(체크아웃) 최종승인 안내]</div>
			<span class="emRed">* 가맹점 인증키, 버튼 인증키, 네이버 공통 인증키를 발급 받으신 후, 최종 승인을 위해 아래의 내용을 복사하신 후 네이버 페이(체크아웃) 담당자에게 메일로 보내주시기 바랍니다.</span><br /><br />

			1. 상품정보 요청 xml URL : <?php echo home_url();?><br />
			&nbsp;&nbsp;&nbsp;&nbsp;(1) 예 => <?php echo home_url();?>/?ITEM_ID=1415950427-1&ITEM_ID=1427620667-1<br /><br />
			  
			2. 테스트환경 파라미터 : 테스트 환경인 경우 페이지 URL 뒤에 npayTest=true 라는 파라미터를 붙여 주시면 네이버 페이 버튼 및 유입경로 소스(header 에 포함 됨)를 확인할 수 있습니다.<br />
			&nbsp;&nbsp;&nbsp;&nbsp;이는 테스트 상태에서는 쇼핑몰 이용 고객에게 네이버 페이관련 버튼을 노출하지 않기 위함이며, 실적용 시에는 해당 파라미터 없이도 네이버 페이 버튼이 보이게 됩니다.<br /><br />
			&nbsp;&nbsp;&nbsp;&nbsp;(1) 메인 페이지 예제 : <?php echo home_url();?>/?npayTest=true<br />
			&nbsp;&nbsp;&nbsp;&nbsp;(2) 상품상세 페이지 예제 : <?php echo home_url();?>/?bbseGoods=6&npayTest=true<br />
			&nbsp;&nbsp;&nbsp;&nbsp;(3) 장바구니 페이지 예제 : <?php echo home_url();?>/?bbsePage=cart&npayTest=true<br /><br />
		</div>
	</div>


	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>
	</div>

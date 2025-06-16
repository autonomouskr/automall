<?php
$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$cType."'");

if($cnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='".$cType."'");
	$data=unserialize($confData->config_data);
}

$goodsCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE goods_display<>'hidden'");
?>
<script language="javascript">
	function change_use_navershop(tType){
		var goodsCnt="<?php echo $goodsCnt;?>";
		if(tType=='on' && parseInt(goodsCnt)>0){
			jQuery(".navershop_allprod").css("display","table-row");
		}
		else{
			jQuery(".navershop_allprod").css("display","none");
		}
	}

	function config_submit(){
		if(confirm('네이버 지식쇼핑 설정을 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('네이버 지식쇼핑 설정을 정상적으로 저장하였습니다.   ');
						go_config('navershop');
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
		if(confirm("전체 상품을 '지식쇼핑 적용'으로 수정하시겠습니까?     ")){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: {cType:"applynavershop"}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert("전체 상품을 '지식쇼핑 적용'으로 수정하였습니다.   ");
						go_config('navershop');
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
		<input type="hidden" name="cType" id="cType" value="navershop" />
			<div class="titleH5" style="margin:20px 0 10px 0; ">네이버 지식쇼핑 설정</div>

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>사용여부</th>
					<td><input type="radio" name="naver_shop_use" id="naver_shop_use"<?php echo ($goodsCnt>'0')?" onClick=\"change_use_navershop('on');\"":""?> value="on" <?php echo ($data['naver_shop_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="naver_shop_use" id="naver_shop_use" onClick="change_use_navershop('off');" value="off" <?php echo (!$data['naver_shop_use'] || $data['naver_shop_use']=='off')?"checked='checked'":"";?> /> 사용안함</td>
				</tr>
				<tr class="navershop_allprod" style="display:<?php echo ($data['naver_shop_use']=='on' && $goodsCnt>'0')?"table-row":"none";?>;">
					<th style="line-height:20px;">지식쇼핑 EP 버전</th>
					<td style="line-height:20px;">
						<?php
						$chkEP_version=($data['naver_shop_ep_version']=='3.0' || ((!$data['naver_shop_use'] || $data['naver_shop_use']=='off') && !$data['naver_shop_ep_version']))?"3.0":"2.0";
						?>
						<select name="naver_shop_ep_version">
							<option value="3.0"<?php echo ($chkEP_version=='3.0')?" selected='selected'":"";?>>3.0 (신규 버전)</option>
							<option value="2.0"<?php echo ($chkEP_version=='2.0')?" selected='selected'":"";?>>2.0 (구 버전)</option>
						</select>
					</td>
				</tr>
				<tr class="navershop_allprod" style="display:<?php echo ($data['naver_shop_use']=='on' && $goodsCnt>'0')?"table-row":"none";?>;">
					<th style="line-height:20px;">지식쇼핑 EP(Engine Page) URL<br/>= 쇼핑몰 상품DB URL</th>
					<td style="line-height:20px;">1. 전체 EP URL : <?php echo home_url();?>/?bbseDBurl=total<br />2. 요약 EP URL : <?php echo home_url();?>/?bbseDBurl=summary</td>
				</tr>
				<tr class="navershop_allprod" style="display:<?php echo ($data['naver_shop_use']=='on' && $goodsCnt>'0')?"table-row":"none";?>;">
					<th>전체 상품 일괄 적용</th>
					<td><button type="button" class="button-small red" onclick="change_goods();" style="width:160px;height:25px;">전체 상품 사용함 적용</button></td>
				</tr>
			</table>

			<div class="clearfix"></div>
		</form>
		<div class="clearfix" style="margin-top:30px;"></div>
		<div class="borderBox">
			<div class="titleH6" style="margin:10px 0 5px 0;">[네이버 지식쇼핑]</div>
				* 네이버 지식쇼핑 2.0 : 전체와 요약 EP URL을 모두 사용합니다. <span class='emRed'>(서비스 종료)</span><br /><br />
				* 네이버 지식쇼핑 3.0 : CPC 몰은 전체 EP URL만 사용하며, CPS 몰은 전체와 요약 EP URL을 모두 사용합니다.<br />
				<span class='emRed'>&nbsp;&nbsp;지식쇼핑 EP 버전을 2.0(구 버전)에서 3.0(신규 버전)으로 변경하시는 경우, 
				네이버 쇼핑파트너존 - 상품관리 - 상품정보 수신 현황에서 재요청을 통해 재심사를 요청하신 후 승인을 받으셔야 합니다.</span> <br /><br />
			

				* 네이버 지식쇼핑의 사용여부가 '사용함'인 경우만 지식쇼핑 상품목록을 생성하며, 지식쇼핑 상품목록 생성 시 등록 된 상품 중 '지식쇼핑 적용'에 체크 된 상품 만 목록으로 생성합니다. <br />
				<span class='emRed'>* 옵션 별 추가가격이 발생하는 상품을 네이버 지식쇼핑에 적용하는 것은 네이버 지식쇼핑의 정책에 위배되어 불이익이 발생할 수 있습니다. <br />
				&nbsp;&nbsp;(옵션이 없거나 옵션 별 추가가격이 발생하지 않는 상품을 지식쇼핑에 적용해 주세요.)</span><br /><br />
				1. 전체 상품 목록 : 01시 (일 1회)<br />
				&nbsp;&nbsp;&nbsp;- 서비스중인(품절이 아니면서 구매가 가능한) 모든 상품 수신<br />
				<br />
				2. 업데이트 상품 목록 (일 최대 13회)<br />
				&nbsp;&nbsp;&nbsp;- 신규/수정/품절된 상품 (신규 상품 등록, 상품 정보 업데이트, 품절 상품 서비스 중지, 품절 복구 상품 서비스 재개)<br />
				<br />
				&nbsp;&nbsp;&nbsp;1) 08시 : 전체 상품 목록 생성 후 업데이트 된 데이터<br />
				&nbsp;&nbsp;&nbsp;2) 10시 : 08시 업데이트 상품 목록 + 08~10시 업데이트 데이터<br />
				&nbsp;&nbsp;&nbsp;3) 12시 : 10시 업데이트 상품 목록 + 10~12시 업데이트 데이터<br />
				&nbsp;&nbsp;&nbsp;4) 14시 : 12시 업데이트 상품 목록 + 12~14시 업데이트 데이터<br />
				&nbsp;&nbsp;&nbsp;5) 16시 : 14시 업데이트 상품 목록 + 14~16시 업데이트 데이터<br />
				&nbsp;&nbsp;&nbsp;6) 18시 : 16시 업데이트 상품 목록 + 16~18시 업데이트 데이터<br />
				&nbsp;&nbsp;&nbsp;7) 20시 : 18시 업데이트 상품 목록 + 18~20시 업데이트 데이터<br/><br/>
			&nbsp;&nbsp;&nbsp;<button type="button" class="button-small green" onclick="window.open('http://join.shopping.naver.com/index.nhn');" style="width:190px;height:25px;">네이버 지식쇼핑 입점신청</button>
		</div>
	</div>


	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>
	</div>

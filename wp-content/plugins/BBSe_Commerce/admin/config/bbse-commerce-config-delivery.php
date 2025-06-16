<?php
global $deliveryCompanyList;
$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='".$cType."'");

if($cnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='".$cType."'");
	$data=unserialize($confData->config_data);
}

$deliveryArray=$deliverySelectOption="";
foreach ($deliveryCompanyList as $key => $value) {
	$deliveryArray .="'".$key."':'".$value."',";
	$deliverySelectOption .="<option value='".$key."'>".$key."</option>";
}
?>
<script language="javascript">
	function change_charge(tType,tVal){
		if(tType=='charge'){
			if(tVal=='charge'){
				jQuery("#charge_info_1").css("display","table-row");
				jQuery("#charge_info_2").css("display","table-row");
			}
			else{
				jQuery("#charge_info_1").css("display","none");
				jQuery("#charge_info_2").css("display","none");
			}
		}
		else if(tType=='condition'){
			if(tVal=='on'){
				jQuery("#condition_free_info").css("display","block");
			}
			else{
				jQuery("#condition_free_info").css("display","none");
			}
		}
	}

	function local_option(tStatus){
		var localCnt=jQuery("#localCnt").val();
		var str="";

		if(tStatus=='add'){
			localCnt++;

			if(localCnt>5){
				alert("지역별 배송료 정책은 최대 5개까지 추가하실 수 있습니다.   ");
				return;
			}

			str +="<div id=\"local_charge_"+localCnt+"\">"
					+	"<div class=\"clearfix\" style=\"height:10px;\"></div>"
					+	"<table class=\"dataTbls overWhite collapse\">"
					+	"		<colgroup><col width=\"15%\"><col width=\"\"></colgroup>"
					+	"		<tr><th colspan=\"2\">지역별 배송료 정책 ("+localCnt+")</th></tr>"
					+	"		<tr>"
					+	"			<th>사용여부</th>"
					+	"			<td>"
					+	"				<input type=\"radio\" name=\"local_charge_"+localCnt+"_use\" id=\"local_charge_"+localCnt+"_use\" value=\"on\" checked='checked' /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"local_charge_"+localCnt+"_use\" id=\"local_charge_"+localCnt+"_use\" value=\"off\" /> 사용안함"
					+	"			</td>"
					+	"		</tr>"
					+	"		<tr>"
					+	"			<th rowspan=\"2\">지역별 배송료</th>"
					+	"			<td>"
					+	"				해당 지역의 배송비에 <input type=\"text\" name=\"local_charge_pay_"+localCnt+"\" id=\"local_charge_pay_"+localCnt+"\" onkeydown=\"check_number();\" value=\"\" style=\"width:100px;\" />원을 추가합니다. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type=\"button\"class=\"button-small red\" onClick=\"local_list_popup("+localCnt+");\" style=\"height:25px;\">지역검색</button>"
					+	"			</td>"
					+	"		</tr>"
					+	"		<tr>"
					+	"			<td>"
					+	"				<div class=\"local_charge_area\">"
					+	"					<ul id=\"local_charge_list_"+localCnt+"\">"
					+	"					</ul>"
					+	"				</div>"
					+	"			</td>"
					+	"		</tr>"
					+	"	</table>"
					+	"</div>";

			jQuery("#local_charge_table").append(str);
		}
		else if(tStatus=='remove'){
			if(localCnt<2){
				alert("지역별 배송료 정책은 최소 1개 이상 필요합니다.   ");
				return;
			}

			jQuery("#local_charge_"+localCnt).remove();
			localCnt--;
		}

		jQuery("#localCnt").val(localCnt);
	}

	function local_charge_remove(pTarget,tIdx){
		jQuery("#local_charge_list_"+pTarget+"_add"+tIdx).remove();
	}

	// 주소 팝업
	function local_list_popup(pTarget){
		var popupTitle="지역검색";
		var localCnt=jQuery("#localCnt").val();

		var chkList="";
		var tbHeight = window.innerHeight * .4;

		for(t=1;t<=localCnt;t++){
			var tCnt=jQuery("input[name=local_charge_list_"+t+"_idx\\[\\]]").size();

			for(i=0;i<tCnt;i++){
				if(chkList) chkList +=",";
				chkList +=jQuery("input[name=local_charge_list_"+t+"_idx\\[\\]]").eq(i).val();
			}
		}

		tb_show("지역별 배송료 ("+popupTitle+")", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-local-list.php?pTarget="+pTarget+"&#38;chkList="+chkList+"&#38;height="+tbHeight+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	function deliveryChange(tNo,tVal){
		var deliveryCompanyList = new Array();
		deliveryCompanyList ={<?php echo $deliveryArray;?>};

		if(tVal=='직접입력'){
			jQuery("#delivery_company_"+tNo+"_select").val("");
			jQuery("#delivery_company_"+tNo+"_name").prop("readonly",false);
			jQuery("#delivery_company_"+tNo+"_name").val("");
			jQuery("#delivery_company_"+tNo+"_name").focus();
			jQuery("#delivery_company_"+tNo+"_url").prop("readonly",false);
			jQuery("#delivery_company_"+tNo+"_url").val("");
		}
		else{
			jQuery("#delivery_company_"+tNo+"_select").val("select");
			jQuery("#delivery_company_"+tNo+"_name").prop("readonly",true);
			jQuery("#delivery_company_"+tNo+"_name").val(tVal);
			jQuery("#delivery_company_"+tNo+"_url").prop("readonly",true);
			jQuery("#delivery_company_"+tNo+"_url").val(deliveryCompanyList[tVal]);
		}
	}

	function delivery_company_option(tStatus){
		var deliveryCompanyCnt=jQuery("#deliveryCompanyCnt").val();
		var str="";
		var deliverySelectOption="<?php echo $deliverySelectOption;?>";

		if(tStatus=='add'){
			deliveryCompanyCnt++;

			if(deliveryCompanyCnt>5){
				alert("택배사 설정은 최대 5개까지 추가하실 수 있습니다.   ");
				return;
			}

			str +="<div id=\"delivery_company_"+deliveryCompanyCnt+"\">"
					+	"<div class=\"clearfix\" style=\"height:10px;\"></div>"
					+	"	<table class=\"dataTbls overWhite collapse\">"
					+	"	<colgroup><col width=\"15%\"><col width=\"\"></colgroup>"
					+	"		<tr><th colspan=\"2\">택배사 ("+deliveryCompanyCnt+")</th></tr>"
					+	"		<tr>"
					+	"			<th>사용여부</th>"
					+	"			<td>"
					+	"				<input type=\"radio\" name=\"delivery_company_"+deliveryCompanyCnt+"_use\" id=\"delivery_company_"+deliveryCompanyCnt+"_use\" value=\"on\" checked='checked' /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"delivery_company_"+deliveryCompanyCnt+"_use\" id=\"delivery_company_"+deliveryCompanyCnt+"_use\" value=\"off\" /> 사용안함"
					+	"			</td>"
					+	"		</tr>"

					+	"		<tr>"
					+	"			<th rowspan=\"2\">택배사 정보</th>"
					+	"			<td>	"
					+	"				<select name=\"company_select_list_"+deliveryCompanyCnt+"\" id=\"company_select_list_"+deliveryCompanyCnt+"\" onChange=\"deliveryChange("+deliveryCompanyCnt+",this.value);\">"
					+	"					<option value=\"직접입력\" selected='selected'>직접입력</option>"
					+						deliverySelectOption
					+	"				</select>"
					+	"				<input type=\"hidden\" name=\"delivery_company_"+deliveryCompanyCnt+"_select\" id=\"delivery_company_"+deliveryCompanyCnt+"_select\" value=\"\" />"
					+	"				<br />"
					+	"				택배사 명&nbsp;&nbsp;&nbsp;&nbsp;: <input type=\"text\" name=\"delivery_company_"+deliveryCompanyCnt+"_name\" id=\"delivery_company_"+deliveryCompanyCnt+"_name\" value=\"\" style=\"width:50%;\" />"
					+	"			</td>"
					+	"		</tr>"
					+	"		<tr>"
					+	"			<td>	"
					+	"				택배사 URL : <input type=\"text\" name=\"delivery_company_"+deliveryCompanyCnt+"_url\" id=\"delivery_company_"+deliveryCompanyCnt+"_url\" value=\"\" style=\"width:80%;\" />"
					+	"			</td>"
					+	"		</tr>"
					+	"		</table>"
					+	"</div>";

			jQuery("#delivery_company_table").append(str);
		}
		else if(tStatus=='remove'){
			if(deliveryCompanyCnt<2){
				alert("택배사 설정은 최소 1개 이상 필요합니다.   ");
				return;
			}

			jQuery("#delivery_company_"+deliveryCompanyCnt).remove();
			deliveryCompanyCnt--;
		}

		jQuery("#deliveryCompanyCnt").val(deliveryCompanyCnt);
	}

	function config_submit(){
		var localCnt=jQuery("#localCnt").val();
		var deliveryCompanyCnt=jQuery("#deliveryCompanyCnt").val();
		var deliveryChargePayment=jQuery("select[name=delivery_charge_payment").val();

		if(jQuery("#delivery_charge_type").val()=='charge'){
			if(deliveryChargePayment != 'cashOnDelivery'){
    			if(jQuery("#delivery_charge").val()<=0){
    				alert("기본 배송료를 입력해 주세요.     ");
    				jQuery("#delivery_charge").focus();
    				return;
    			}
    			if(jQuery("input[id='condition_free_use']:checked").val()=='on' && jQuery("#total_pay").val()<=0){
    				alert("총 구매금액을 입력해 주세요.     ");
    				jQuery("#total_pay").focus();
    				return;
    			}
			}
		}

		for(c=1;c<=localCnt;c++){
			if(jQuery("input[id='local_charge_"+c+"_use']:checked").val()=='on'){
				var tCnt=jQuery("input[name=local_charge_list_"+c+"_idx\\[\\]]").size();
				if(!jQuery("#local_charge_pay_"+c).val()){
					alert("지역별 배송료 정책 ("+c+")의 추가 배송비를 입력해 주세요.    ");
					jQuery("#local_charge_pay_"+c).focus();
					return;
				}
				if(tCnt<=0){
					alert("지역별 배송료 정책 ("+c+")의 지역을 선택해 주세요.    ");
					return;
				}
			}
		}

		for(d=1;d<=deliveryCompanyCnt;d++){
			if(jQuery("input[id='delivery_company_"+d+"_use']:checked").val()=='on'){
				if(!jQuery("#delivery_company_"+d+"_name").val()){
					alert("택배사 ("+d+")의 이름을 입력해 주세요.    ");
					jQuery("#delivery_company_"+d+"_name").focus();
					return;
				}
				if(!jQuery("#delivery_company_"+d+"_url").val()){
					alert("택배사 ("+d+")의 URL을 입력해 주세요.    ");
					jQuery("#delivery_company_"+d+"_url").focus();
					return;
				}
			}
		}

		switchEditors.go('mall_delivery_info', 'tmce');

		var ed = tinyMCE.get('mall_delivery_info');
		jQuery("#mall_delivery_info").val(ed.getContent({format : 'raw'}));  // raw(비쥬얼) / text(텍스트)

		var tmpDetail=jQuery("#mall_delivery_info").val().replace('<p><br data-mce-bogus=\"1\"></p>', '');
		tmpDetail=tmpDetail.replace('<p><br></p>', '');

		if(!tmpDetail){
			alert("배송/취소/교환 안내를 입력해 주세요.     ");
			tinyMCE.get('mall_delivery_info').focus()
			return
		}

		if(confirm('배송비 설정을 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('배송비 설정을 정상적으로 저장하였습니다.   ');
						go_config('delivery');
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
		<input type="hidden" name="cType" id="cType" value="delivery" />
		<input type="hidden" name="localCnt" id="localCnt" value="<?php echo ($data['localCnt']<='0')?"1":$data['localCnt'];?>" />
		<input type="hidden" name="deliveryCompanyCnt" id="deliveryCompanyCnt" value="<?php echo ($data['deliveryCompanyCnt']<='0')?"1":$data['deliveryCompanyCnt'];?>" />
			<div class="titleH5" style="margin:20px 0 10px 0; ">기본 배송료 정책</div>

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>배송료 정책</th>
					<td>
						<select name="delivery_charge_type" id="delivery_charge_type" onChange="change_charge('charge',this.value);" style="width:100px;"><option value="charge" <?php echo ($data['delivery_charge_type']=='charge')?"selected='selected'":"";?>>유료배송</option><option value="free" <?php echo (!$data['delivery_charge_type'] || $data['delivery_charge_type']=='free')?"selected='selected'":"";?>>무료배송</option></select>
					</td>
				</tr>
				<tr id="charge_info_1" style="display:<?php echo ($data['delivery_charge_type']=='charge')?"table-row":"none";?>;">
					<th>기본 배송료</th>
					<td><select name="delivery_charge_payment" id="delivery_charge_payment" style="width:100px;" >
						<option value="advance" <?php echo (!$data['delivery_charge_payment'] || $data['delivery_charge_payment']=='advance')?"selected='selected'":"";?>>선불결제</option>
						<option value="deferred" <?php echo ($data['delivery_charge_payment']=='deferred')?"selected='selected'":"";?>>후불결제</option>
						<option value="cashOnDelivery" <?php echo ($data['delivery_charge_payment']=='cashOnDelivery')?"selected='selected'":"";?>>착불결제</option>
						</select>&nbsp;&nbsp;&nbsp;
						<input type="text" name="delivery_charge" id="delivery_charge" onkeydown="check_number();" value="<?php echo $data['delivery_charge'];?>" style="width:100px;" />원
					</span>
					</td>
				</tr>
				<tr id="charge_info_2" style="display:<?php echo ($data['delivery_charge_type']=='charge')?"table-row":"none";?>;">
					<th>조건부 무료배송</th>
					<td>
						<input type="radio" name="condition_free_use" id="condition_free_use" value="on" onClick="change_charge('condition','on');" <?php echo ($data['condition_free_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="condition_free_use" id="condition_free_use" value="off" onClick="change_charge('condition','off');" <?php echo (!$data['condition_free_use'] || $data['condition_free_use']=='off')?"checked='checked'":"";?> /> 사용안함
						<span id="condition_free_info" style="display:<?php echo ($data['condition_free_use']=='on')?"block":"none";?>;">
							<select name="delivery_feee_terms" id="delivery_feee_terms" style="width:160px;"><option value="goods_price" <?php echo (!$data['delivery_feee_terms'] || $data['delivery_feee_terms']=='goods_price')?"selected='selected'":"";?>>주문 상품의 판매가 기준</option></select> 총 구매금액이<input type="text" name="total_pay" id="total_pay" onkeydown="check_number();" value="<?php echo $data['total_pay'];?>" style="width:100px;" />원 이상인 경우 무료 배송
						</span>
					</td>
				</tr>
			</table>
			<div class="clearfix"></div>
			<div class="titleH5" style="margin:30px 0 10px 0; ">지역별 배송료 정책<div style="float:right;"><button type="button"class="button-small blue" onClick="local_option('add');" style="height:25px;">추가</button>&nbsp;&nbsp;<button type="button"class="button-small red" onClick="local_option('remove');" style="height:25px;">삭제</button></div></div>
			
		    <div class="clearfix"></div>
			<div id="local_charge_table">
			<?php
			if($data['localCnt']<='0') $localCnt='1';
			else $localCnt=$data['localCnt'];
			
			for($k=1;$k<=$localCnt;$k++){ 
			?>
				<div id="local_charge_<?php echo $k;?>">
					<?php if($k>1){?><div class="clearfix" style="height:10px;"></div><?php }?>
					<table class="dataTbls overWhite collapse">
						<colgroup><col width="15%"><col width=""></colgroup>
						<tr><th colspan="2">지역별 배송료 정책 (<?php echo $k;?>)</th></tr>
						<tr>
							<th>사용여부</th>
							<td>
								<input type="radio" name="local_charge_<?php echo $k;?>_use" id="local_charge_<?php echo $k;?>_use" value="on" <?php echo ($data['local_charge_'.$k.'_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="local_charge_<?php echo $k;?>_use" id="local_charge_<?php echo $k;?>_use" value="off" <?php echo (!$data['local_charge_'.$k.'_use'] || $data['local_charge_'.$k.'_use']=='off')?"checked='checked'":"";?> /> 사용안함
							</td>
						</tr>
						<tr>
							<th rowspan="2">지역별 배송료</th>
							<td>
								해당 지역의 배송비에 <input type="text" name="local_charge_pay_<?php echo $k;?>" id="local_charge_pay_<?php echo $k;?>"onkeydown="check_number();" value="<?php echo $data['local_charge_pay_'.$k];?>" style="width:100px;" />원을 추가합니다. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="button"class="button-small red" onClick="local_list_popup('<?php echo $k;?>');" style="height:25px;">지역검색</button>
							</td>
						</tr>
						<tr>
							<td>
								<div class="local_charge_area">
									<ul id="local_charge_list_<?php echo $k;?>">
										<?php
										$aCnt=sizeof($data['local_charge_list_'.$k.'_idx']);
										for($c=0;$c<$aCnt;$c++){
										?>
										<li id="local_charge_list_<?php echo $k;?>_add<?php echo $data['local_charge_list_'.$k.'_idx'][$c];?>"><?php echo $data['local_charge_list_'.$k.'_name'][$c];?> <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png" onClick="local_charge_remove(<?php echo $k;?>,<?php echo $data['local_charge_list_'.$k.'_idx'][$c];?>);" class="deleteBtn" alt="이미지 제거" title="이미지 제거" /><input type="hidden" name="local_charge_list_<?php echo $k;?>_idx[]" value="<?php echo $data['local_charge_list_'.$k.'_idx'][$c];?>" /><input type="hidden" name="local_charge_list_<?php echo $k;?>_name[]" value="<?php echo $data['local_charge_list_'.$k.'_name'][$c];?>" />
										</li>
										<?php
										}
										?>
									</ul>
								</div>
							</td>
						</tr>
					</table>
				</div>
			<?php
			}
			?>
			</div>

			<div class="clearfix"></div>
			<div class="titleH5" style="margin:30px 0 10px 0; ">택배사 설정<div style="float:right;"><button type="button"class="button-small blue" onClick="delivery_company_option('add');" style="height:25px;">추가</button>&nbsp;&nbsp;<button type="button"class="button-small red" onClick="delivery_company_option('remove');" style="height:25px;">삭제</button></div></div>
			
		    <div class="clearfix"></div>
			<div id="delivery_company_table">
			<?php
			if($data['deliveryCompanyCnt']<='0') $deliveryCompanyCnt='1';
			else $deliveryCompanyCnt=$data['deliveryCompanyCnt'];
			
			for($y=1;$y<=$deliveryCompanyCnt;$y++){
			?>
				<div id="delivery_company_<?php echo $y;?>">
					<?php if($y>1){?><div class="clearfix" style="height:10px;"></div><?php }?>
					<table class="dataTbls overWhite collapse">
						<colgroup><col width="15%"><col width=""></colgroup>
						<tr><th colspan="2">택배사 (<?php echo $y;?>)</th></tr>
						<tr>
							<th>사용여부</th>
							<td>
								<input type="radio" name="delivery_company_<?php echo $y;?>_use" id="delivery_company_<?php echo $y;?>_use" value="on" <?php echo ($data['delivery_company_'.$y.'_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="delivery_company_<?php echo $y;?>_use" id="delivery_company_<?php echo $y;?>_use" value="off" <?php echo (!$data['delivery_company_'.$y.'_use'] || $data['delivery_company_'.$y.'_use']=='off')?"checked='checked'":"";?> /> 사용안함
							</td>
						</tr>
						<tr>
							<th rowspan="2">택배사 정보</th>
							<td>	
								<select name="company_select_list_<?php echo $y;?>" id="company_select_list_<?php echo $y;?>" onChange="deliveryChange(<?php echo $y;?>,this.value);">
									<option value="직접입력" <?php echo (!$data['delivery_company_'.$y.'_select'])?"selected='selected'":"";?>>직접입력</option>
								<?php
									foreach ($deliveryCompanyList as $key => $value) {
										if($data['delivery_company_'.$y.'_select']=='select' && $data['delivery_company_'.$y.'_name']==$key) $deliverySelected="selected='selected'";
										else $deliverySelected="";

										echo "<option value='".$key."' ".$deliverySelected.">".$key."</option>";
									}
								?>
								</select>
								<input type="hidden" name="delivery_company_<?php echo $y;?>_select" id="delivery_company_<?php echo $y;?>_select" value="<?php echo $data['delivery_company_'.$y.'_select'];?>" />
								<br />
								택배사 명&nbsp;&nbsp;&nbsp;&nbsp;: <input type="text" name="delivery_company_<?php echo $y;?>_name" id="delivery_company_<?php echo $y;?>_name" value="<?php echo $data['delivery_company_'.$y.'_name'];?>" style="width:50%;" <?php echo ($data['delivery_company_'.$y.'_select']=='select')?"readonly":"";?> />
							</td>
						</tr>
						<tr>
							<td>	
								택배사 URL : <input type="text" name="delivery_company_<?php echo $y;?>_url" id="delivery_company_<?php echo $y;?>_url" value="<?php echo $data['delivery_company_'.$y.'_url'];?>" style="width:80%;" <?php echo ($data['delivery_company_'.$y.'_select']=='select')?"readonly":"";?> />
							</td>
						</tr>
					</table>
				</div>
			<?php
			}
			?>
			</div>

			<div class="clearfix" style="margin-top:30px"></div>
			
		    <div class="clearfix"></div>
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>배송/취소/교환 안내</th>
					<td>
						<?php 
						wp_editor(html_entity_decode($confData->config_editor), "mall_delivery_info", $settings=array('textarea_name'=>'mall_delivery_info', 'textarea_rows'=>'7')); 
						?> 
					</td>
				</tr>
			</table>

			<div class="clearfix"></div>
		</form>
	</div>

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>
	</div>

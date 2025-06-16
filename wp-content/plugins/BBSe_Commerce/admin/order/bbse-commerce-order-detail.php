<?php
$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$tData."'");

$config = $wpdb->get_row("select * from bbse_commerce_membership_config limit 1");

if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){
	$zipcodeScript2 = "openDaumPostcode('receive');";
}else{
	$zipcodeScript2 = "zipcode_search('".BBSE_COMMERCE_PLUGIN_WEB_URL."','receive');";
}

?>
<script language="javascript">
	function user_view(tData){
		var tbHeight = 268;
		var tbWidth=450;
		tb_show("회원정보 - "+tData, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-user-info.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	function social_view(tData){
		var tbHeight = 268;
		var tbWidth=450;
		tb_show("소셜로그인 정보", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-social-info.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	function delivery_info_view(tIdx,tMode){
		var tbHeight = 500;
		var tbWidth=500;
		var titStr="";

		if(tMode=='order') titStr="주문 시 배송비 정책";
		else titStr="배송지 변경 시 배송비 정책";
		tb_show("배송비 추가정보 - "+titStr, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-delivery-info-view.php?tMode="+tMode+"&tIdx="+tIdx+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	function submit_status(tIdx){
		var oStatus=jQuery("#order_status").val();
		var deliveryNo=jQuery("#delivery_no").val();
		var deliveryCompany=jQuery("#delivery_company").val();
		var tMode="changeStatus";
		var refundBankName="";
		var refundBankNo="";
		var refundBankOwner="";
		var refundReason="";
		var refundBankInfo="";
		var refundFees="";
		var refundTotal="";
	
		if(oStatus!="PR" && oStatus!="PE" && oStatus!="CA" && oStatus!="CE" && oStatus!="DR" && oStatus!="TR" && oStatus!="restore"){
			/*
			if(!deliveryCompany){
				alert("택배사를 선택해 주세요.        ");
				jQuery("#delivery_company").focus();
				return;
			}
			if(!deliveryNo){
				alert("송장번호를 입력해 주세요.        ");
				jQuery("#delivery_no").focus();
				return;
			}
			*/
		}

		if(jQuery("#order_status_now").val()!='PR'){
			if(oStatus=="CA" || oStatus=="CE" || oStatus=="RA" || oStatus=="RE"){
				refundBankName=jQuery("#refund_bank_name").val();
				refundBankNo=jQuery("#refund_bank_no").val();
				refundBankOwner=jQuery("#refund_bank_owner").val();
				refundReason=jQuery("#refund_reason").val();
				refundBankInfo=refundBankName+"|||"+refundBankNo+"|||"+refundBankOwner;
				refundFees=jQuery("#refund_fees").val();
				refundTotal=jQuery("#refund_total").val();

				/*if(!refundBankName){
					alert("환불 은행명을 입력해 주세요.        ");
					jQuery("#refund_bank_name").focus();
					return;
				}
				if(!refundBankNo){
					alert("환불은행의 계좌번호를 입력해 주세요.        ");
					jQuery("#refund_bank_no").focus();
					return;
				}
				if(!refundBankOwner){
					alert("환불은행의 예금주를 입력해 주세요.        ");
					jQuery("#refund_bank_owner").focus();
					return;
				}
				if(!refundReason){
					alert("환불 사유를 입력해 주세요.        ");
					jQuery("#refund_reason").focus();
					return;
				}*/
			}
		}

		if(confirm('주문 상태를 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-order.exec.php', 
				data: {tMode:tMode,tIdx:tIdx,oStatus:oStatus,deliveryNo:deliveryNo,deliveryCompany:deliveryCompany,refundBankInfo:refundBankInfo,refundReason:refundReason,refundFees:refundFees,refundTotal:refundTotal}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('주문 상태를 정상적으로 저장하였습니다.   ');
						go_order(tIdx);
					}
					else if(result=='notExistOrder'){
						alert('주문정보가 존재하지 않습니다.   ');
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

	function submit_receive(tIdx){
		var tMode="changeReceive";
		var receiveName=jQuery("#receive_name").val();
		var receivePhone=jQuery("#receive_phone").val();
		var receiveHp=jQuery("#receive_hp").val();
		var receiveZip=jQuery("#receive_zip").val();
		var receiveAddr1=jQuery("#receive_addr1").val();
		var receiveAddr2=jQuery("#receive_addr2").val();

		if(!receiveName){
			alert("수신자명을 입력해 주세요.        ");
			jQuery("#receive_name").focus();
			return;
		}
		if(!receivePhone){
			alert("수신자 전화번호를 입력해 주세요.        ");
			jQuery("#receive_phone").focus();
			return;
		}
		if(!receiveHp){
			alert("수신자 휴대전화 번호를 입력해 주세요.        ");
			jQuery("#receive_hp").focus();
			return;
		}
		if(!receiveZip){
			alert("수신자 우편번호를 입력해 주세요.        ");
			jQuery("#receive_zip").focus();
			return;
		}
		if(!receiveAddr1){
			alert("수신자 주소를 입력해 주세요.        ");
			jQuery("#receive_addr1").focus();
			return;
		}
		if(!receiveAddr2){
			alert("수신자 상세주소를 입력해 주세요.        ");
			jQuery("#receive_addr2").focus();
			return;
		}

		if(confirm('배송정보를 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-order.exec.php', 
				data: {tMode:tMode,tIdx:tIdx,receiveName:receiveName,receivePhone:receivePhone,receiveHp:receiveHp,receiveZip:receiveZip,receiveAddr1:receiveAddr1,receiveAddr2:receiveAddr2}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('배송정보를 정상적으로 저장하였습니다.   ');
						go_order(tIdx);
					}
					else if(result=='notExistOrder'){
						alert('주문정보가 존재하지 않습니다.   ');
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

	function submit_memo(tIdx){
		var oMemo=jQuery("#admin_comment").val();
		var tMode="changeMemo";

		if(confirm('관리자 메모를 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-order.exec.php', 
				data: {tMode:tMode,tIdx:tIdx,oMemo:oMemo}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('관리자 메모를 정상적으로 저장하였습니다.   ');
						go_order(tIdx);
					}
					else if(result=='notExistOrder'){
						alert('주문정보가 존재하지 않습니다.   ');
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

	function change_status(oStatus){
		if((oStatus!="PR" && oStatus!="PE" && oStatus!="CA" && oStatus!="CE" && oStatus!="DR" && oStatus!="TR" && oStatus!="restore") || (oStatus=="TR" && (jQuery("#delivery_info").val() || jQuery("#delivery_no").val()))){
			jQuery("#delivery_info").css("display","inline");
		}
		else jQuery("#delivery_info").css("display","none");

		if(jQuery("#order_status_now").val()!='PR'){
			if(oStatus=="CA" || oStatus=="CE"){
				jQuery("#refund_info_title").text("주문취소");
				jQuery("#refund_reason_title").text("취소");
				jQuery("#refund_info").css("display","block");
			}
			else if(oStatus=="RA" || oStatus=="RE"){
				jQuery("#refund_info_title").text("반품신청");
				jQuery("#refund_reason_title").text("반품");
				jQuery("#refund_info").css("display","block");
			}
			else if(oStatus!='restore' && oStatus!='TR'){
				jQuery("#refund_info").css("display","none");
			}
		}
	}

	function go_order(tIdx){
		if(tIdx) var goStr="admin.php?page=bbse_commerce_order&tMode=detail&tData="+tIdx;
		else var goStr="admin.php?page=bbse_commerce_order";
		window.location.href =goStr;
	}

	// 숫자만 입력
	function check_number(){
		var key = event.keyCode;
		if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
			event.returnValue = false;
		}
	}

	function refund_calculation(){
		var refundCostTotal=jQuery("#refund_cost_total").val();
		var refundFees=jQuery("#refund_fees").val();

		if(!refundFees) refundFees='0';

		var refundTotal=parseInt(refundCostTotal)-parseInt(refundFees);
		jQuery("#refund_total_view").text("(-) "+GetCommaValue(refundTotal)+"원");
		jQuery("#refund_total").val(refundTotal);
	}

	//세자리 콤마
	function GetCommaValue(num) {
		var len, point, str;  
		num = num + "";  
		point = num.length % 3  
		len = num.length;  

		str = num.substring(0, point);  
		while (point < len) {  
			if (str != "") str += ",";  
			str += num.substring(point, point + 3);  
			point += 3;  
		}  
		return str;  
  }
</script>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>주문관리(상세정보)<button type="button" class="button-fill blue" onClick="go_order('');" style="margin-left:50px;"> 목록보기 </button></h2>
		<hr>
	</div>

	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">기본정보</div>

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="24%"><col width=""></colgroup>
		<tr>
			<th>주문번호</th>
			<td>
				<span class="titleH5"><?php echo $oData->order_no;?></span>
				<?php if($oData->order_device=='tablet' || $oData->order_device=='mobile'){?>
					&nbsp;&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-mobile.png" align="absmiddle" style="margin-top:-5px;" title="모바일 주문" />
				<?php }else{?>
					&nbsp;&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-desktop.png" align="absmiddle" style="margin-top:-5px;" title="데스크탑 주문" />
				<?php }?>
			</td>
		</tr>
		<tr>
			<th>주문상태</th>
			<td><input type="hidden" name="order_status_now" id="order_status_now" value="<?php echo $oData->order_status;?>" />
				<select name="order_status" id="order_status" onChange="change_status(this.value);"  style="width:100px;">
					<?php echo bbse_commerce_get_order_status($oData->idx);?>
				</select>
				<span id="delivery_info" style="display:<?php echo ($oData->order_status=='DI' || $oData->order_status=='DE' || $oData->order_status=='RA' || $oData->order_status=='RE' || $oData->order_status=='OE' || ($oData->order_status=='TR' && ($oData->delivery_company || $oData->delivery_no)))?"inline":"none";?>;">
				<select name="delivery_company" id="delivery_company" >
					<?php
					if($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE' || $oData->order_status=='TR'){
						echo "<option selected=\"selected\" value=\"".$oData->delivery_company."|||".$oData->delivery_url."\">".$oData->delivery_company."</option>";
					}
					else{
						echo "<option value=\"\">택배사 선택</option>";

						$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
						$delvData=unserialize($confData->config_data);

						for($y=1;$y<=$delvData['deliveryCompanyCnt'];$y++){
							if($delvData['delivery_company_'.$y.'_use']=='on'){
								if($oData->delivery_company==$delvData['delivery_company_'.$y.'_name']) $deliverySelected="selected=\"selected\"";
								else $deliverySelected="";

								echo "<option ".$deliverySelected." value=\"".$delvData['delivery_company_'.$y.'_name']."|||".$delvData['delivery_company_'.$y.'_url']."\">".$delvData['delivery_company_'.$y.'_name']."</option>";
							}
						}
					}
					?>
				</select>
				<input type="text" name="delivery_no" id="delivery_no" value="<?php echo $oData->delivery_no;?>" style="width:200px;" placeholder="송장번호를 입력하세요" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> />
				</span>
			</td>
		</tr>
		<tr>
			<th>주문일</th>
			<td><?php echo date("Y.m.d H:i:s",$oData->order_date);?></td>
		</tr>
		<?php echo bbse_commerce_get_date_by_order_status($oData->idx);?>

	</table>
<?php
if($oData->refund_bank_info) $refundBankInfo=explode("|||",$oData->refund_bank_info);
?>
	<div id="refund_info" style="display:<?php echo ($oData->order_status_pre!='PR' && ($oData->order_status=='CA' || $oData->order_status=='CE' || $oData->order_status=='RA' || $oData->order_status=='RE' || ($oData->order_status=='TR' && ($oData->order_status_pre=='CA' || $oData->order_status_pre=='CE' || $oData->order_status_pre=='RA' || $oData->order_status_pre=='RE')) || (!$oData->order_status_pre && $refundBankInfo['0'] && $refundBankInfo['1'] && $refundBankInfo['2'])))?"block":"none";?>">
		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0; "><span id="refund_info_title"><?php echo ($oData->order_status=='CA' || $oData->order_status=='CE')?"주문취소":"반품신청";?></span> 정보</div>
		<table class="dataTbls overWhite collapse">
			<colgroup><col width="24%"><col width=""></colgroup>
			<tr>
				<th>환불계좌</th>
				<td><input type="text" name="refund_bank_name" id="refund_bank_name" value="<?php echo $refundBankInfo['0'];?>" style="width:150px;text-align:center;" placeholder="은행명" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> /> <input type="text" name="refund_bank_no" id="refund_bank_no" value="<?php echo $refundBankInfo['1'];?>" style="width:200px;text-align:center;" placeholder="계좌번호" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> /> <input type="text" name="refund_bank_owner" id="refund_bank_owner" value="<?php echo $refundBankInfo['2'];?>" style="width:200px;text-align:center;" placeholder="예금주" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> /></td>
			</tr>
			<tr>
				<th><span id="refund_reason_title"><?php echo ($oData->order_status=='CA' || $oData->order_status=='CE')?"취소":"반품";?></span>사유</th>
				<td><textarea name="refund_reason" id="refund_reason" style="width:100%;height:50px;font-size:12px;" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?>><?php echo stripslashes($oData->refund_reason);?></textarea></td>
			</tr>
		</table>

		<div class="clearfix" style="height:10px;"></div>
		<table class="dataTbls overWhite collapse">
			<colgroup><col width="24%"><col width=""></colgroup>
			<tr>
				<th>최종 결제금액</th>
				<th>환불 수수료</th>
				<th>최종 환불 금액 (최종 결제금액-환불 수수료)</th>
			</tr>
			<tr>
				<td style="text-align:center;"><span class="titleH4 emBlue"><?php echo number_format($oData->cost_total);?>원</span><input type="hidden" name="refund_cost_total" id="refund_cost_total" value="<?php echo $oData->cost_total;?>" style="width:100px;" /></td>
				<td style="text-align:center;"><span class="titleH5 emBlue">(+)</span> <input type="text" name="refund_fees" id="refund_fees" value="<?php echo ($oData->refund_fees)?$oData->refund_fees:"0";?>" class="feesInput" style="color:#00A2E8;" onkeydown="check_number();" onKeyup="refund_calculation();" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> /><span class="titleH5 emBlue">원</span></td>
				<td style="text-align:center;"><span id="refund_total_view" class="titleH4 emRed">(-) <?php echo ($oData->refund_total>'0')?number_format($oData->refund_total):number_format($oData->cost_total);?>원</span><input type="hidden" name="refund_total" id="refund_total" value="<?php echo ($oData->refund_total>'0')?$oData->refund_total:$oData->cost_total;?>" style="width:100px;" /></td>
			</tr>
		</table>
		<div class="clearfix" style="height:10px;"></div>
	</div><!--#refund_info-->
	<div class="clearfix" style="height:10px;"></div>
	<div style="float:right;">
		<button type="button"class="button-bbse blue" onClick="submit_status(<?php echo $oData->idx;?>);" style="width:150px;">상태 및 정보 저장</button>
	</div>

	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">주문상품</div>
	<table class="dataTbls collapse">
		<colgroup><col width="25%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
		<tr>
			<th>상품명</th>
			<th>상품정보</th>
			<th>수량</th>
			<th>적립금</th>
			<!-- <th>합계</th> -->
		</tr>
		<?php 
			$gResult  = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."' ORDER BY idx ASC");
			foreach($gResult as $j=>$gData) {
				unset($basicOpt);
				unset($addOpt);
				unset($basicImg);

				if($gData->goods_basic_img) 	$basicImg = wp_get_attachment_image_src($gData->goods_basic_img);

				if(!$basicImg['0']){
					$goodsAddImg=$wpdb->get_var("SELECT goods_add_img FROM bbse_commerce_goods WHERE idx='".$gData->goods_idx."'"); 

					$imageList=explode(",",$goodsAddImg);
					if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
					else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
				}
		?>
		<tr>
			<td>
				<table class="dataNormalTbls">
					<colgroup><col width="130px"><col width=""></colgroup>
					<tr>
						<td style="vertical-align:top;border-bottom:0px;">
							<div style="width:102px;margin-left:10px;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $gData->goods_idx;?>" target="_blank"><img src="<?php echo $basicImg['0'];?>" class="list-goods-img"></a></div>
							<!-- <div style="background:#F0F0F0;border:1px solid #DFDFDF;width:101px;text-align:center;margin-left:10px;">￦ <?php echo number_format($gData->goods_price);?>원</div> -->
						</td>
						<td style="vertical-align:top;border-bottom:0px;">
							<div class="clearfix" style="height:10px;"></div>

							<?php 
								echo "<div class=\"titleH5\"><a href=\"".esc_url( home_url( '/' ) )."?bbseGoods=".$gData->goods_idx."\" target=\"_blank\">".$gData->goods_name."</a></div><br />";
								$basicOpt=unserialize($gData->goods_option_basic);
								$basicOptCnt='0';
 								for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
									/* if($basicOpt['goods_option_title'][$b]=="단일상품") echo "<div style='width:100%'>&nbsp;<div style='float:right;margin-right:10px;'>".number_format($gData->goods_price)."원 * ".$basicOpt['goods_option_count'][$b]."개</div></div>";
									else echo "<div style='width:100%'>".$basicOpt['goods_option_title'][$b]." <span class=\"textFont-11 emBlue\">(+ ".number_format($basicOpt['goods_option_overprice'][$b])."원)</span><div style='float:right;margin-right:10px;'>".number_format($gData->goods_price+$basicOpt['goods_option_overprice'][$b])."원 * ".$basicOpt['goods_option_count'][$b]."개</div></div>";
 */
									$basicOptCnt +=$basicOpt['goods_option_count'][$b];
								} 

								$addOpt=unserialize($gData->goods_option_add);
								$addOptCnt='0';

								if(sizeof($addOpt['goods_add_title'])>'0') echo "<hr />";
								for($a=0;$a<sizeof($addOpt['goods_add_title']);$a++){
									echo "<div style='width:100%'>".$addOpt['goods_add_title'][$a]." <span class=\"textFont-11 emBlue\">(".number_format($addOpt['goods_add_overprice'][$a])."원)</span><div style='float:right;margin-right:10px;'>".number_format($addOpt['goods_add_overprice'][$a])."원 * ".$addOpt['goods_add_count'][$a]."개</div></div>";

									$addOptCnt +=$addOpt['goods_add_count'][$a];
								}
							?>
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align:center;">

			<?php 
			if(!$gData->goods_unique_code && !$gData->goods_barcode && !$gData->goods_location_no){
				echo "-";
			}else{
			?>
				<table class="normalTbls" align="center">
					<colgroup><col width="34px"><col width=""></colgroup>
				<?php if($gData->goods_unique_code){?><tr><td><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_unique_code.png" align="absmiddle" title="고유번호" /></td><td><?php echo $gData->goods_unique_code;?></td></tr><?php }?>
				<?php if($gData->goods_barcode){?><tr><td><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_barcode.png" align="absmiddle" title="바코드" /></td><td><?php echo $gData->goods_barcode;?></td></tr><?php }?>
				<?php if($gData->goods_location_no){?><tr><td><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_location_no.png" align="absmiddle" title="위치정보" /></td><td><?php echo $gData->goods_location_no;?></td></tr><?php }?>
				</table>
			<?php
			}
			?>
			</td>
			<td style="text-align:center;"><?php echo number_format($basicOptCnt+$addOptCnt);?></td>
			<td style="text-align:center;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_earn.png" align="absmiddle" title="구매확정 후 예상 누적 적립금" /> <?php echo number_format($gData->goods_earn*$basicOptCnt);?>원</td>
<!-- 			<td style="text-align:center;"><div class="titleH5 emBlue"><?php echo number_format($gData->goods_basic_total+$gData->goods_add_total);?>원</div></td> -->
		</tr>
		<?php
			}
		?>			
	</table>

	<?php 
	$deliveryInfoStr="";
	$deliveryChargePayment="advance";
	if($oData->order_config){
		$deliveryData=unserialize($oData->order_config);

		if(!$deliveryData['delivery_charge_payment']) $deliveryChargePayment="advance";
		else $deliveryChargePayment=$deliveryData['delivery_charge_payment'];

		if(!$deliveryData['delivery_charge_type'] || $deliveryData['delivery_charge_type']=='free') $deliveryInfoStr .="-배송비 정책 : 무료배송 상품입니다.<br />";
		else{
			$deliveryInfoStr .="-배송비 정책 : 유료배송 상품입니다.<br />";
			 if($deliveryChargePayment=='advance') $deliveryInfoStr .="-기본배송비 : 선불 ".number_format($deliveryData['delivery_charge'])."원<br />";
			 else $deliveryInfoStr .="-기본배송비 : 후불 ".number_format($deliveryData['delivery_charge'])."원<br />";
		}

		if($deliveryData['delivery_charge_type']=='charge' && $deliveryData['condition_free_use']=='on') $deliveryInfoStr .="-조건부 무료배송 : 주문 상품의 판매가 기준 총구매금액이 ".number_format($deliveryData['total_pay'])."원 이상인 경우 무료입니다.<br />";

		if(!$oData->delivery_add) $delivery_add='0';
		else $delivery_add=$oData->delivery_add;

		$deliveryInfoStr .="지역별 추가배송비 : ".number_format($delivery_add)."원 <span class=\"emOblique emBlue\">(".$oData->delivery_add_addr.")</span> <img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_question.png\" onClick=\"delivery_info_view(".$oData->idx.",'order')\" align=\"absmiddle\" style=\"cursor:pointer;\" />";
	?>
	<div class="clearfix" style="height:20px;"></div>
	<div class="borderBox">
		<div class="titleH6" style="margin:5px 0 5px 0;">[주문 시 배송비 정책 - <?php echo date("Y.m.d",$oData->order_date);?> 기준]</div>
		 <?php echo $deliveryInfoStr;?>
	</div>
	<?php
	}

	$deliveryChangeInfoStr="";
	if(($oData->delivery_add>'0' || $oData->delivery_add_change>'0') && $oData->delivery_add!=$oData->delivery_add_change && $oData->delivery_add_change_date>'0'){
		$changeAddr1=explode(" ",$oData->receive_addr1);

		$deliveryChangeInfoStr .="지역별 추가배송비 : ".number_format($oData->delivery_add_change)."원 <span class=\"emOblique emBlue\">(".$changeAddr1['0']." ".$changeAddr1['1'].")</span> <img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_question.png\" onClick=\"delivery_info_view(".$oData->idx.",'change')\" align=\"absmiddle\" style=\"cursor:pointer;\" />";
	?>
	<div class="borderBox">
		<div class="titleH6" style="margin:5px 0 5px 0;">[배송지 변경 시 배송비 정책 - <?php echo date("Y.m.d",$oData->delivery_add_change_date);?> 기준]</div>
		 <?php echo $deliveryChangeInfoStr;?>
	</div>
	<?php
	}
	?>
	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">결제정보</div>
	<table class="dataTbls overWhite collapse">
		<colgroup><col width="24%"><col width=""></colgroup>
		<tr>
			<th>결제방법</th>
			<td>
				<?php
					if($oData->ezpay_how=='EPN') $payType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_paynow.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />) - ".$payHow[$oData->pay_how];
					elseif($oData->ezpay_how=='EKA') $payType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kakaopay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />) - ".$payHow[$oData->pay_how];
					elseif($oData->ezpay_how=='EPC') $payType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_payco.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />) - ".$payHow[$oData->pay_how];
					elseif($oData->ezpay_how=='EKP') $payType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kpay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />) - ".$payHow[$oData->pay_how];
					elseif($oData->ezpay_how=='NPAY') $payType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/icon_npay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />) - ".$payHow[$oData->pay_how];
					elseif($oData->ezpay_how=='NICE') $payType="나이스페이 - ".$payHow[$oData->pay_how];
					else $payType=$payHow[$oData->pay_how];

					echo $payType;
				?>
			</td>
		</tr>
<?php
if($oData->pay_how=='B'){
?>
		<tr>
			<th>입금자명</th>
			<td><?php echo ($oData->input_name)?$oData->input_name:$oData->order_name;?></td>
		</tr>
		<tr>
			<th>입금계좌</th>
			<td>
				<?php
				$bankInfo=unserialize($oData->pay_info);
				echo $bankInfo['bank_name']." ".$bankInfo['bank_no'];
				?>
			</td>
		</tr>
<?php 
}
else{
	// 결제모듈 설정
	$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
	if(!$paymentConfig) {
		echo "<script>alert('관리자에서 결제모듈 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
		exit;
	}

	if($oData->ezpay_how){
		if($oData->ezpay_how=='EPN')$pg_kind = "lguplusXPay";
		elseif($oData->ezpay_how=='EKA')$pg_kind = "kakaopay";
		elseif($oData->ezpay_how=='EPA')$pg_kind = "";
		elseif($oData->ezpay_how=='EKP')$pg_kind = "INIpay50";
		elseif($oData->ezpay_how=='NICE')$pg_kind = "nice";
	}
	else{
		$payCFG = unserialize($paymentConfig);
		$pg_kind = ($payCFG['payment_agent'])?$payCFG['payment_agent']:"allthegate";
	}

	if($pg_kind=='allthegate'){
		$agsData=$wpdb->get_row("SELECT * FROM bbse_commerce_pg_agspay WHERE rOrdNo='".$oData->order_no."'");
	}
	elseif($pg_kind=='INIpay50'){
		$iniData = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_inicis WHERE MOID='".$oData->order_no."'");
	}
	elseif($pg_kind=='lguplusXPay'){
		$lguData = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_uplus WHERE LGD_OID='".$oData->order_no."'");
	}
	elseif($pg_kind=='kakaopay'){
		$kakData = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_kakaopay WHERE order_no='".$oData->order_no."'");
	}
	elseif($pg_kind=='nice'){
		$pay_info = json_decode(base64_decode($oData->pay_info),true);
		//print_r($oData->pay_info);
	}

	if($oData->pay_how=='C'){
		if($pg_kind=='allthegate'){
			$cardName=bbse_commerce_get_card_name($agsData->rCardCd*1,'allthegate'); // 카드종류
			$appNo=$agsData->rApprNo;// 승인번호
			$dealNo=$agsData->rDealNo;//거래번호
			$applyTime=substr($agsData->rApprTm,0,4).".".substr($agsData->rApprTm,4,2).".".substr($agsData->rApprTm,6,2)." ".substr($agsData->rApprTm,8,2).":".substr($agsData->rApprTm,10,2).":".substr($agsData->rApprTm,12,2);

			if($agsData->ES_SENDNO) $escroNo=", 에스크로 주문번호 : ".$agsData->ES_SENDNO; // 에스크로 번호
			else $escroNo="";
		}
		elseif($pg_kind=='INIpay50'){
			$cardName=bbse_commerce_get_card_name($iniData->CARD_Code,'INIpay50'); // 카드종류
			$appNo=$iniData->ApplNum;// 승인번호
			$dealNo="";
			$applyTime=substr($iniData->ApplDate,0,4).".".substr($iniData->ApplDate,4,2).".".substr($iniData->ApplDate,6,2)." ".substr($iniData->ApplTime,0,2).":".substr($iniData->ApplTime,2,2).":".substr($iniData->ApplTime,4,2);
			$escroNo="";
		}
		elseif($pg_kind=='lguplusXPay'){
			$cardName=$lguData->LGD_FINANCENAME; // 카드종류
			$appNo=$lguData->LGD_FINANCEAUTHNUM;// 승인번호
			$dealNo="";
			$applyTime=substr($lguData->LGD_PAYDATE,0,4).".".substr($lguData->LGD_PAYDATE,4,2).".".substr($lguData->LGD_PAYDATE,6,2)." ".substr($lguData->LGD_PAYDATE,0,2).":".substr($lguData->LGD_PAYDATE,2,2).":".substr($lguData->LGD_PAYDATE,4,2);
			$escroNo="";
		}
		elseif($pg_kind=='kakaopay'){
			$cardName=bbse_commerce_get_card_name($kakData->cardCode,'kakaopay'); // 카드종류
			$appNo=$kakData->authCode;// 승인번호
			$dealNo="";
			$applyTime="20".substr($kakData->authDate,0,2).".".substr($kakData->authDate,2,2).".".substr($kakData->authDate,4,2)." ".substr($kakData->authDate,6,2).":".substr($kakData->authDate,8,2).":".substr($kakData->authDate,10,2);
			$escroNo="";
		}
		elseif($pg_kind=='nice'){
			$cardName=$pay_info['CardName']; // 카드종류
			$appNo=$pay_info['AuthCode'];// 승인번호
			$dealNo="";
			$applyTime=$pay_info['AuthDate'];
			$escroNo="";
		}
?>
		<tr>
			<th>결제정보</th>
			<td><span class="titleH6 emBlue">
				결제(승인)완료</span> <span class="emBlue">(<?php echo $cardName."카드";?>, 승인번호 : <?php echo $appNo;?>
				<?php if($dealNo){echo ", 거래번호 : ".$dealNo;}
				if($escroNo){echo ", 에스크로 주문번호 : ".$escroNo;}?>)
			</span></td>
		</tr>
		<tr>
			<th>승인완료 시간</th>
			<td><?php echo $applyTime;?></td>
		</tr>
<?php
	}
	elseif($oData->pay_how=='K'){
		if($pg_kind=='allthegate'){
			if($agsData->ES_SENDNO) $escroNo="(에스크로 주문번호".$agsData->ES_SENDNO.")"; // 에스크로 번호
			else $escroNo="";
		}
		elseif($pg_kind=='INIpay50'){ // 이니시스의 경우 에스크로 주문번호 대신 은행명 출력
			if($iniData->ACCT_BankCode!="") {
				$escroNo=bbse_commerce_get_vbank_name($iniData->ACCT_BankCode,"INIpay50"); 
			}
		}
		elseif($pg_kind=='lguplusXPay'){ 
			if($lguData->LGD_FINANCENAME!="") {
				$escroNo=$lguData->LGD_FINANCENAME; 
			}
		}
		elseif($pg_kind=='nice'){ 
			$escroNo=iconv('euc-kr', 'utf-8',$pay_info['BankName']); 
		}
?>
		<tr>
			<th>결제정보</th>
			<td><span class="titleH6 emBlue">결제(이체)완료</span> <span class="emBlue"><?php echo $escroNo;?></span></td>
		</tr>
<?php
	}
	elseif($oData->pay_how=='V'){
		if($pg_kind=='allthegate'){
			$bankName=bbse_commerce_get_vbank_name($agsData->VIRTUAL_CENTERCD,"allthegate"); // 가상계좌 은행명
			$bankNo=$agsData->rVirNo;

			if($agsData->ES_SENDNO) $escroNo=$agsData->ES_SENDNO; // 에스크로 번호
			else $escroNo="";
			$applyTime=substr($agsData->rApprTm,0,4).".".substr($agsData->rApprTm,4,2).".".substr($agsData->rApprTm,6,2)." ".substr($agsData->rApprTm,8,2).":".substr($agsData->rApprTm,10,2).":".substr($agsData->rApprTm,12,2);
			$endInputTime=mktime('23','59','59',substr($agsData->rApprTm,4,2),substr($agsData->rApprTm,6,2)+5,substr($agsData->rApprTm,0,4));
			$endInputTime=date("Y.m.d",$endInputTime);
		}
		elseif($pg_kind=='INIpay50'){
			$bankName=bbse_commerce_get_vbank_name($iniData->VACT_BankCode,"INIpay50"); // 가상계좌 은행명
			$bankNo=$iniData->VACT_Num;
			$escroNo="";

			$applyTime=substr($iniData->ApplDate,0,4).".".substr($iniData->ApplDate,4,2).".".substr($iniData->ApplDate,6,2)." ".substr($iniData->ApplTime,0,2).":".substr($iniData->ApplTime,2,2).":".substr($iniData->ApplTime,4,2);
			$endInputTime=substr($iniData->VACT_Date,0,4).".".substr($iniData->VACT_Date,4,2).".".substr($iniData->VACT_Date,6,2);
		}
		elseif($pg_kind=='lguplusXPay'){
			$bankName=$lguData->LGD_FINANCENAME; // 가상계좌 은행명
			$bankNo=$lguData->LGD_ACCOUNTNUM;
			$escroNo="";

			$applyTime=substr($lguData->LGD_PAYDATE,0,4).".".substr($lguData->LGD_PAYDATE,4,2).".".substr($lguData->LGD_PAYDATE,6,2)." ".substr($lguData->LGD_PAYDATE,8,2).":".substr($lguData->LGD_PAYDATE,10,2).":".substr($lguData->LGD_PAYDATE,12,2);
			$endInputTime=substr($lguData->LGD_CLOSEDATE,0,4).".".substr($lguData->LGD_CLOSEDATE,4,2).".".substr($lguData->LGD_CLOSEDATE,6,2)." ".substr($lguData->LGD_CLOSEDATE,8,2).":".substr($lguData->LGD_CLOSEDATE,10,2).":".substr($lguData->LGD_CLOSEDATE,12,2);

			$inputName=$lguData->LGD_PAYER;
		}
		elseif($pg_kind=='nice'){
			$bankName=iconv('euc-kr', 'utf-8',$pay_info['VbankBankName']); // 가상계좌 은행명
			$bankNo=$pay_info['VbankNum'];
			$escroNo="";

			$applyTime= $pay_info['AuthDate'];
			$endInputTime=$pay_info['VbankExpDate'] ." ".$pay_info['VbankExpTime'];
			$inputName=$oData->input_name;
		}
?>
		<tr>
			<th>계좌정보</th>
			<td><span class="titleH6 emBlue"><?php echo $bankName;?> (계좌번호 : <?php echo $bankNo;?><?php echo ($inputName)?", 입금자명:".$inputName:"";?>)</span></td>
		</tr>
		<tr>
			<th>가상계좌 발급일자</th>
			<td><?php echo $applyTime;?> 
					<?php if($endInputTime){?>
						<span class="emBlue">(입금예정 기한 : <?php echo $endInputTime;?>)</span>
					<?php }?>
			</td>
		</tr>
	<?php if($escroNo){?>
		<tr>
			<th>에스크로 주문번호</th>
			<td><span class="emBlue"><?php echo $escroNo;?></span></td>
		</tr>
	<?php }?>
<?php
	}
	elseif($oData->pay_how=='H'){
		$telecomName=$agsData->rHP_COMPANY; // 통신사명(SKT,KTF,LGT)
		$mobileNO=$agsData->rHP_HANDPHONE; // 핸드폰 번호
		$mobileTID=$agsData->rHP_TID; // 핸드폰 결제 TID
		$applyTime=substr($agsData->rHP_DATE,0,4).".".substr($agsData->rHP_DATE,4,2).".".substr($agsData->rHP_DATE,6,2); // 핸드폰 결제일
?>
		<tr>
			<th>결제정보</th>
			<td><span class="titleH6 emBlue"><?php echo $telecomName;?></span> <span class="emBlue">(휴대전화 : <?php echo $mobileNO;?>, TID : <?php echo $mobileTID;?>)</span></td>
		</tr>
		<tr>
			<th>결제일자</th>
			<td><?php echo $applyTime;?></td>
		</tr>
<?php
	 }
}
?>
	</table>

	<div class="clearfix" style="height:10px;"></div>
	<table class="dataTbls overWhite collapse">
		<colgroup><col width="24%"><col width=""></colgroup>
		<tr>
			<th>총 상품금액</th>
			<td><span class="titleH4"><?php echo number_format($oData->goods_total);?>원</span></td>
		</tr>

		<tr>
			<th>적립금 사용</th>
			<td><span class="emRed">(-) <?php echo number_format($oData->use_earn);?>원</span></td>
		</tr>
		<tr>
			<th>배송비</th>
			<td>
			<?php
			if($deliveryChargePayment=='advance'){
				if($oData->delivery_total>'0') $deliveryTotal="<span class=\"emBlue\">(+) ".number_format($oData->delivery_total)."원 </span><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_delivery_payment_advance.png\"align=\"absmiddle\" style=\"margin-top:-2px;\" title=\"배송비 선불 결제\" />";
				else $deliveryTotal="<span class=\"emBlue\">(+) 0 원 </span>";
			}
			else if($deliveryChargePayment=='deferred'){
				if(($oData->delivery_total+$oData->delivery_add_change)>'0') $deliveryTotal="<span class=\"emRed\">".number_format($oData->delivery_total+$oData->delivery_add_change)."원 </span><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_delivery_payment_after.png\"align=\"absmiddle\" style=\"margin-top:-2px;\" title=\"배송비 후불 결제\" />";
				else $deliveryTotal="<span class=\"emRed\">0 원 </span>";
			}else{
			    $deliveryTotal="";
			}
			?>
			<?php echo $deliveryTotal;?>
			</td>
		</tr>
		<tr>
			<th>최종 결제금액</th>
			<td><span class="titleH4 emBlue"><?php echo number_format($oData->cost_total);?>원</span></td>
		</tr>
		<tr>
			<th>예상 적립금</th>
			<td><?php echo number_format($oData->add_earn);?>원</td>
		</tr>
	<?php
	if($deliveryChargePayment=='advance' && ($oData->delivery_add>'0' || $oData->delivery_add_change>'0') && $oData->delivery_add!=$oData->delivery_add_change && $oData->delivery_add_change_date>'0'){
		if(!$oData->delivery_add) $delivery_add='0';
		else $delivery_add=$oData->delivery_add;

		if(!$oData->delivery_add_change) $delivery_add_change='0';
		else $delivery_add_change=$oData->delivery_add_change;

		$deliveryAddChange=$delivery_add_change-$delivery_add;

		if($deliveryAddChange>'0') $deliveryChangeStr="<span class=\"titleH4 emBlue\">(+) ".number_format(abs($deliveryAddChange))."원</span>";
		else $deliveryChangeStr="<span class=\"titleH4 emRed\">(-) ".number_format(abs($deliveryAddChange))."원</span>";
	?>
		<tr>
			<th>배송비 변경사항</th>
			<td><?php echo $deliveryChangeStr;?><span class="prd-desc">* 배송지 수정으로 인해 배송비가 변경되었습니다.&nbsp;&nbsp;(＋) 구매고객으로 부터 받을 금액, (－) 구매고객에게 돌려 줄 금액</span></td>
		</tr>
	<?php
	}
	?>
	</table>

	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">주문자정보</div>
	<table class="dataTbls overWhite collapse">
		<colgroup><col width="24%"><col width=""></colgroup>
		<tr>
			<th>회원 정보</th>
			<td>
			<?php 
			if($oData->user_id){
				$mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$oData->user_id."'");

				if($mData->name){
					echo "<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_member.png\" align=\"absmiddle\" title=\"회원\"> ".$mData->name." <span style=\"color:#00A2E8;\">(<span onClick=\"user_view('".$mData->user_id."');\" style=\"cursor:pointer;\">".$mData->user_id."</span>)</span></div>";
				}
				else echo "<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_notextist_member.png\" title=\"회원 정보 없음\"></div>";
			}
			elseif($oData->sns_id && $oData->sns_idx){
				echo "<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_social.png\" onClick=\"social_view('".$oData->sns_idx."');\" style=\"cursor:pointer;\" title=\"소셜로그인 주문\"></div>";
			}
			else echo "<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_nomember.png\" title=\"비회원 주문\"></div>";
			?>
			</td>
		</tr>
		<tr>
			<th>주문자명</th>
			<td><?php echo $oData->order_name;?></td>
		</tr>
		<tr>
			<th>연락처</th>
			<td>
			<?php 
			$contecInfo="";
			if(strlen($oData->order_phone)>'9') $contecInfo .="전화번호 : ".$oData->order_phone;
			if($contecInfo) $contecInfo .=" / ";
			if($oData->order_hp) $contecInfo .="휴대폰 : ".$oData->order_hp;

			echo $contecInfo;
			?>
			</td>
		</tr>
		<tr>
			<th>이메일</th>
			<td><?php echo $oData->order_email;?></td>
		</tr>
		<?php
        	$pass_num = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order' order by idx asc");
			$pass_num = unserialize($pass_num);
			$pass_num = $pass_num['pass_num_use'];
			if($pass_num == 'on'):
        ?>
		<tr>
			<th>개인통관번호</th>
			<td><?php echo $oData->order_pass_num;?></td>
		</tr>
		<?php
			endif;
        ?>
		<tr>
			<th>남기실 말씀</th>
			<td><?php echo nl2br(stripslashes($oData->order_comment));?></td>
		</tr>
	</table>

	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">배송정보</div>
	<table class="dataTbls overWhite collapse">
		<colgroup><col width="24%"><col width=""></colgroup>
		<tr>
			<th>수신자명</th>
			<td><input type="text" name="receive_name" id="receive_name" value="<?php echo $oData->receive_name;?>" style="width:300px;" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='DI' || $oData->order_status=='DE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> /></td>
		</tr>
	<?php if(strlen($oData->receive_phone)>'9'){?>
		<tr>
			<th>연락처</th>
			<td><input type="text" name="receive_phone" id="receive_phone" value="<?php echo $oData->receive_phone;?>" style="width:200px;" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='DI' || $oData->order_status=='DE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> /></td>
		</tr>
	<?php }?>
		<tr>
			<th>핸드폰</th>
			<td><input type="text" name="receive_hp" id="receive_hp" value="<?php echo $oData->receive_hp;?>" style="width:200px;" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='DI' || $oData->order_status=='DE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> /></td>
		</tr>
		<tr>
			<th>주소</th>
			<td>
			<?php $receiveZip=$oData->receive_zip;?>
				<input type="text" name="receive_zip" id="receive_zip" value="<?php echo $receiveZip;?>" style="width:70px;text-align:center;" readonly /> <input type="text" name="receive_addr1" id="receive_addr1" value="<?php echo $oData->receive_addr1;?>" style="width:400px;" readonly /> <button type="button"class="button-small blue" onClick="<?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='DI' || $oData->order_status=='DE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"alert('".$orderStatus[$oData->order_status]." 상태에서는 배송지 변경이 불가능합니다.   ');":$zipcodeScript2;?>" style="height:25px;">우편번호</button><br>
				<input type="text" name="receive_addr2" id="receive_addr2" value="<?php echo $oData->receive_addr2;?>" style="width:600px;" <?php echo ($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='DI' || $oData->order_status=='DE' || $oData->order_status=='OE' || $oData->order_status=='TR')?"readonly":"";?> />
			</td>
		</tr>
	</table>
	<div class="clearfix" style="height:10px;"></div>
	<div style="float:right;">
		<button type="button"class="button-bbse blue" onClick="submit_receive(<?php echo $oData->idx;?>);" style="width:150px;">배송정보 수정</button>
	</div>

	<div class="clearfix"></div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">관리자 메모</div>
	<table class="dataTbls overWhite collapse">
		<colgroup><col width="24%"><col width=""></colgroup>
		<tr>
			<th>관리자 메모</th>
			<td><textarea name="admin_comment" id="admin_comment" style="width:100%;height:50px;font-size:12px;"><?php echo stripslashes($oData->admin_comment);?></textarea></td>
		</tr>
	</table>
	<div class="clearfix" style="height:10px;"></div>
	<div style="float:right;">
		<button type="button"class="button-bbse blue" onClick="submit_memo(<?php echo $oData->idx;?>);" style="width:150px;">메모저장</button>
	</div>

</div>

<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */?>
<div id="commerceZipcodeLayer" style="display:none;border:5px solid;position:fixed;width:320px;height:500px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script><!--https-->
<script>
    function closeDaumPostcode() {
        jQuery("#commerceZipcodeLayer").css("display","none");
    }
	function openDaumPostcode(fieldTitle){
		new daum.Postcode({
			oncomplete: function(data){
				if(data.userSelectedType === 'R'){

					// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
					// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
					var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
					var extraRoadAddr = ''; // 도로명 조합형 주소 변수

					// 법정동명이 있을 경우 추가한다. (법정리는 제외)
					// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
					if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
						extraRoadAddr += data.bname;
					}
					// 건물명이 있고, 공동주택일 경우 추가한다.
					if(data.buildingName !== '' && data.apartment === 'Y'){
					   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
					if(extraRoadAddr !== ''){
						extraRoadAddr = ' (' + extraRoadAddr + ')';
					}
					// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
					if(fullRoadAddr !== ''){
						fullRoadAddr += extraRoadAddr;
					}

					jQuery('#'+fieldTitle+'_zip').val(data.zonecode);
					jQuery('#'+fieldTitle+'_addr1').val(fullRoadAddr);
				}
				else{
					jQuery('#'+fieldTitle+'_zip').val(data.postcode1+"-"+data.postcode2);
					jQuery('#'+fieldTitle+'_addr1').val(data.jibunAddress);
				}

				jQuery('#'+fieldTitle+'_addr2').focus();
			}
		}).open();
	}
</script> 
<?php }else{?>
<script>
	function zipcode_search(aURl,fieldTitle){
		window.open(aURl+"/zipcode.php?oUser=admin&&fieldTitle="+fieldTitle, "zipcode_search", "width=400,height=400,scrollbars=yes");
	}
</script> 
<?php }?>
<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) || !$_REQUEST['tData']){echo "nonData";exit;}


$tmpIdx=explode(",",$_REQUEST['tData']);

$sOption="";
for($z=0;$z<sizeof($tmpIdx);$z++){
	if($sOption) $sOption .=" OR ";
	$sOption .="idx='".$tmpIdx[$z]."'";
}

$total = $wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order WHERE idx<>'' AND order_status='DR' AND (".$sOption.")");    // 총 상품수
?>
<!DOCTYPE html>
<html>
<head>
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script language="javascript">
		function print_submit(){
			jQuery("#layer_fixed").hide();
			window.print();
			setTimeout(function() {
				  jQuery("#layer_fixed").show();
			}, 1000);
		}
	</script>
    <style type="text/css">
        #layer_fixed{text-align:right;margin-top:568px;width:100%;color: #555;font-size:12px;position:fixed;z-index:999;top:0px;left:0px;}
		#layer_fixed img{
			margin-right:5px;
			opacity: .4;
			filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=40);
			-ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=40)';
		}
		#layer_fixed img:hover{
			opacity: 1;
			filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=100);
			-ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=100)';
		}

    </style>
</head>
<body>
    <div id="layer_fixed">
		<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_print.png" onClick="print_submit();" style="cursor:pointer;width:42px;" alt="주문서 인쇄" title="주문서 인쇄" />
	</div>

<?php 
if($total>'0'){
	$result  = $wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE idx<>'' AND order_status='DR' AND (".$sOption.") ORDER BY idx DESC");
	foreach($result as $i=>$oData) {
?>
		<div style="<?php echo ($i>'0')?"page-break-before:always;":"";?>width:97%;min-height:950px;margin:10px;border:1px solid #BABABA;font-size:12px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td valign="top">
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="97%">
							<colgroup><col width=""></colgroup>
							<tr><td style="height:35px;font-size:14px;">관리자보관용</td></tr>
						</table>

						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="13%"><col width="37%"><col width="13%"><col width="37%"></colgroup>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">주문번호</td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;"><?php echo $oData->order_no;?></td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">주문일</td>
								<td style="height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;"><?php echo date("Y.m.d H:i:s",$oData->order_date);?></td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:0 10px 0 10px;background:#dfdfdf;">주문자</td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:5px 10px;line-height:20px;"><?php echo $oData->order_name;?> (<?php echo ($oData->user_id)?$oData->user_id:"비회원";?>)<br /><?php echo $oData->order_phone;?> / <?php echo $oData->order_hp;?><br />[<?php echo $oData->order_zip;?>]<br /><?php echo $oData->order_addr1." ".$oData->order_addr2;?></td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:0 0 0 20px;background:#dfdfdf;">수령자</td>
								<td style="height:30px;border:1px solid #303030;padding:5px 10px;line-height:20px;"><?php echo $oData->receive_name;?><br /><?php echo $oData->receive_phone;?> / <?php echo $oData->receive_hp;?><br />[<?php echo $oData->receive_zip;?>]<br /><?php echo $oData->receive_addr1." ".$oData->receive_addr2;?></td>
							</tr>
						</table>
						<br />
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="52%"><col width="26%"><col width="7%"><col width="15%"></colgroup>
							<tr>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;background:#dfdfdf;">상품/옵션 정보</td>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;background:#dfdfdf;">상품정보</td>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;background:#dfdfdf;">수량</td>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-bottom:0px;background:#dfdfdf;">합계</td>
							</tr>

						<?php 
						$gResult  = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."' ORDER BY idx ASC");
						foreach($gResult as $j=>$gData){
						?>
							<tr>
								<td style="height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>border-right:0px;padding:5px 10px 5px 10px;">
									<?php echo $gData->goods_name;?><br /><br />
									<?php
									$basicOpt=unserialize($gData->goods_option_basic);
									$optCount=0;
									for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
										if($basicOpt['goods_option_title'][$b]=="단일상품") echo "단일상품&nbsp;&nbsp;*&nbsp;&nbsp;".$basicOpt['goods_option_count'][$b]."개<br />";
										else echo $basicOpt['goods_option_title'][$b]." * ".$basicOpt['goods_option_count'][$b]."개<br />";
										$optCount++;
									}

									$addOpt=unserialize($gData->goods_option_add);
									if(sizeof($addOpt['goods_add_title'])>'0') echo "<hr style='border: 0;border-bottom: 1px dashed #cccccc;background: #999999;' />";
									for($a=0;$a<sizeof($addOpt['goods_add_title']);$a++){
										echo $addOpt['goods_add_title'][$a]."&nbsp;&nbsp;*&nbsp;&nbsp;".$addOpt['goods_add_count'][$a]."개<br />";
										$optCount++;
									}
								?>
								</td>
								<td style="height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>border-right:0px;padding:5px 10px;line-height:20px;font-size:11px;">
									<?php echo ($gData->goods_location_no)?$gData->goods_location_no." (위치)<br />":"";?>
									<?php echo ($gData->goods_barcode)?$gData->goods_barcode." (바코드)<br />":"";?>
									<?php echo ($gData->goods_unique_code)?$gData->goods_unique_code." (고유)":"";?>
								</td>
								<td style="height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>border-right:0px;padding:0 0 0 20px;"><?php echo number_format($optCount);?></td>
								<td style="text-align:right;height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>padding:5px 10px;line-height:20px;"><?php echo number_format($gData->goods_basic_total+$gData->goods_add_total);?>원</td>
							</tr>
						<?php
						}
						?>
						</table>
						<br />
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="78%"><col width="22%"></colgroup>
							<tr>
								<td colspan="2" style="height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">결제정보</td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;">총 상품금액</td>
								<td style="text-align:right;height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;"><?php echo number_format($oData->goods_total);?>원</td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;">적립금 사용</td>
								<td style="text-align:right;height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;">(-) <?php echo number_format($oData->use_earn);?>원</td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:0 10px 0 10px;">최종 결제금액</td>
								<td style="text-align:right;height:30px;border:1px solid #303030;padding:5px 10px;line-height:20px;padding:0 10px 0 10px;"><?php echo number_format($oData->cost_total);?>원</td>
							</tr>
						</table>
						<br />
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="100%"></colgroup>
							<tr>
								<td colspan="2" style="height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">남기실 말씀</td>
							</tr>
							<tr>
								<td style="height:60px;border:1px solid #303030;padding:5px 10px;line-height:20px;"><?php echo $oData->order_comment;?></td>
							</tr>
						</table>
						<br />
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="100%"></colgroup>
							<tr>
								<td colspan="2" style="height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">관리자 메모</td>
							</tr>
							<tr>
								<td style="height:60px;border:1px solid #303030;padding:5px 10px;line-height:20px;"></td>
							</tr>
						</table>

					</td>
				</tr>
				<tr><td style="height:30px;text-align:center;"><?php echo ($i+1)."/".$total;?></td></tr>
			</table>
		</div>

		<div style="page-break-before:always;width:97%;height:950px;margin:10px;border:1px solid #BABABA;font-size:12px;">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr>
					<td valign="top">
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="97%">
							<colgroup><col width=""></colgroup>
							<tr><td style="height:35px;font-size:14px;">고객 보관용</td></tr>
						</table>

						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="13%"><col width="37%"><col width="13%"><col width="37%"></colgroup>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">주문번호</td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;"><?php echo $oData->order_no;?></td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">주문일</td>
								<td style="height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;"><?php echo date("Y.m.d H:i:s",$oData->order_date);?></td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:0 10px 0 10px;background:#dfdfdf;">주문자</td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:5px 10px;line-height:20px;"><?php echo $oData->order_name;?> (<?php echo ($oData->user_id)?$oData->user_id:"비회원";?>)<br /><?php echo $oData->order_phone;?> / <?php echo $oData->order_hp;?><br />[<?php echo $oData->order_zip;?>]<br /><?php echo $oData->order_addr1." ".$oData->order_addr2;?></td>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:0 0 0 20px;background:#dfdfdf;">수령자</td>
								<td style="height:30px;border:1px solid #303030;padding:5px 10px;line-height:20px;"><?php echo $oData->receive_name;?><br /><?php echo $oData->receive_phone;?> / <?php echo $oData->receive_hp;?><br />[<?php echo $oData->receive_zip;?>]<br /><?php echo $oData->receive_addr1." ".$oData->receive_addr2;?></td>
							</tr>
						</table>
						<br />
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="52%"><col width="26%"><col width="7%"><col width="15%"></colgroup>
							<tr>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;background:#dfdfdf;">상품/옵션 정보</td>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;background:#dfdfdf;">상품정보</td>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;background:#dfdfdf;">수량</td>
								<td style="text-align:center;height:30px;border:1px solid #303030;border-bottom:0px;background:#dfdfdf;">합계</td>
							</tr>

						<?php 
						$gResult  = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."' ORDER BY idx ASC");
						foreach($gResult as $j=>$gData){
						?>
							<tr>
								<td style="height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>border-right:0px;padding:5px 10px 5px 10px;">
									<?php echo $gData->goods_name;?><br /><br />
									<?php
									$basicOpt=unserialize($gData->goods_option_basic);
									$optCount=0;
									for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
										if($basicOpt['goods_option_title'][$b]=="단일상품") echo "단일상품&nbsp;&nbsp;*&nbsp;&nbsp;".$basicOpt['goods_option_count'][$b]."개<br />";
										else echo $basicOpt['goods_option_title'][$b]." * ".$basicOpt['goods_option_count'][$b]."개<br />";
										$optCount++;
									}

									$addOpt=unserialize($gData->goods_option_add);
									if(sizeof($addOpt['goods_add_title'])>'0') echo "<hr style='border: 0;border-bottom: 1px dashed #cccccc;background: #999999;' />";
									for($a=0;$a<sizeof($addOpt['goods_add_title']);$a++){
										echo $addOpt['goods_add_title'][$a]."&nbsp;&nbsp;*&nbsp;&nbsp;".$addOpt['goods_add_count'][$a]."개<br />";
										$optCount++;
									}
								?>
								</td>
								<td style="height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>border-right:0px;padding:5px 10px;line-height:20px;">
									<?php echo ($gData->goods_location_no)?$gData->goods_location_no."(위치)<br />":"";?>
									<?php echo ($gData->goods_barcode)?$gData->goods_barcode."(바코드)<br />":"";?>
									<?php echo ($gData->goods_unique_code)?$gData->goods_unique_code."(고유)":"";?>
								</td>
								<td style="height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>border-right:0px;padding:0 0 0 20px;"><?php echo number_format($optCount);?></td>
								<td style="text-align:right;height:30px;border:1px solid #303030;<?php echo ($j>'0')?"border-top:0px;":"";?>padding:5px 10px;line-height:20px;"><?php echo number_format($gData->goods_basic_total+$gData->goods_add_total);?>원</td>
							</tr>
						<?php
						}
						?>
						</table>
						<br />
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="78%"><col width="22%"></colgroup>
							<tr>
								<td colspan="2" style="height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">결제정보</td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;">총 상품금액</td>
								<td style="text-align:right;height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;"><?php echo number_format($oData->goods_total);?>원</td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;border-bottom:0px;padding:0 10px 0 10px;">적립금 사용</td>
								<td style="text-align:right;height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;">(-) <?php echo number_format($oData->use_earn);?>원</td>
							</tr>
							<tr>
								<td style="height:30px;border:1px solid #303030;border-right:0px;padding:0 10px 0 10px;">최종 결제금액</td>
								<td style="text-align:right;height:30px;border:1px solid #303030;padding:5px 10px;line-height:20px;padding:0 10px 0 10px;"><?php echo number_format($oData->cost_total);?>원</td>
							</tr>
						</table>
						<br />
						<table border="0" cellpadding="0" align="center" cellspacing="0" width="95%">
							<colgroup><col width="100%"></colgroup>
							<tr>
								<td colspan="2" style="height:30px;border:1px solid #303030;border-bottom:0px;padding:0 10px 0 10px;background:#dfdfdf;">남기실 말씀</td>
							</tr>
							<tr>
								<td style="height:60px;border:1px solid #303030;padding:5px 10px;line-height:20px;"><?php echo $oData->order_comment;?></td>
							</tr>
						</table>

					</td>
				</tr>
				<tr><td style="height:30px;text-align:center;"><?php echo ($i+1)."/".$total;?></td></tr>
			</table>
		</div>
<?php
	}
?>
<?php
}
else{
	echo "<div style='text-align:center;'>주문 데이터가 존재하지 않습니다.</P>";
}
?>
</body>
</html>

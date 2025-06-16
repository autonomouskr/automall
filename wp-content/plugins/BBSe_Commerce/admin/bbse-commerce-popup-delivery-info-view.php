<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
</head>
<body>
<?php
	$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$_REQUEST['tIdx']."'");

	if($_REQUEST['tMode']=='order') {
		$deliveryData=unserialize($oData->order_config);
		$stDate="* 주문일자 : ".date("Y.m.d",$oData->order_date)." 기준";
		$tAddr=$oData->delivery_add_addr;
	}
	else{
		$deliveryData=unserialize($oData->change_config);
		$stDate="* 배송지 변경일자 : ".date("Y.m.d",$oData->delivery_add_change_date)." 기준";
		$tmpAddr=explode(" ",$oData->receive_addr1);
		$tAddr=$tmpAddr['0']." ".$tmpAddr['1'];
	}
?>

	<div class="wrap">
		<div id="popup-goods-list">
			<div class="clearfix" style="height:30px;text-align:right;padding-top:10px;"><span class="emBlue" style="font-size:12px;"><?php echo $stDate;?></span></div>
		<?php
		$cnfData['localCnt']=$deliveryData['localCnt'];                                                       // 지역별 배송비 설정 개수 (최대 5개)
		if($cnfData['localCnt']>'0'){
			for($i=1;$i<=$deliveryData['localCnt'];$i++){
				if($deliveryData['local_charge_'.$i.'_use']=='on'){
		?>
			<table class="dataTbls collapse overWhite">
			<colgroup><col width="100px"><col width=""></colgroup>
				<tr>
					<td colspan="2" style="line-height:25px;">
					<?php
					$tSize=sizeof($deliveryData['local_charge_list_'.$i.'_name']);
					$viewStr="";
					for($j=0;$j<$tSize;$j++){
						if($viewStr) $viewStr .=", ";

						if($tAddr==trim($deliveryData['local_charge_list_'.$i.'_name'][$j])) $viewStr .="<span class=\"titleH5 emOblique emBlue\" style=\"font-size:13px;\">".$deliveryData['local_charge_list_'.$i.'_name'][$j]."</span>";
						else $viewStr .=$deliveryData['local_charge_list_'.$i.'_name'][$j];
					}
					echo $viewStr;
					?>
					</td>
				</tr>
				<tr>
					<td style="border-bottom:0px;"><span class="titleH5 emBlue" style="font-size:13px;">추가배송비</span></td>
					<td style="text-align:right;border-bottom:0px;"><span class="titleH5 emBlue" style="font-size:13px;"><?php echo number_format($deliveryData['local_charge_pay_'.$i]);?>원</span></td>
				</tr>
			</table>
			<div style="width:100%;background:#4C99BA;height:2px;"></div>
			<div class="clearfix" style="height:20px;"></div>
		<?php
				}
			}
		}
		else{
		?>
			<table class="dataTbls collapse overWhite">
			<colgroup><col width=""></colgroup>
				<tr>
					<td style="text-align:center;">추가 배송비 목록이 존재하지 않습니다.</td>
				</tr>
			</table>
		<?php
		}
		?>

			<div class="clearfix" style="height:30px;"></div>
		</div>
	</div>
</body>
</html>

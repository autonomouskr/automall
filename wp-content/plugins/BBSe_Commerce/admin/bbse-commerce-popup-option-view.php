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
	$data=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$_REQUEST['tData']."'");

?>

	<div class="wrap">
		<div id="popup-goods-list">
			<div class="clearfix" style="height:30px;"></div>
			<table class="dataTbls collapse">
			<colgroup><col width="7%"><col width=""><col width="18%"><col width="15%"><col width="10%"></colgroup>
				<tr>
					<th>번호</th>
					<th>옵션명</th>
					<th>추가가격</th>
					<th>재고</th>
					<th>품절체크</th>
				</tr>
			<?php
				$totalCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$_REQUEST['tData']."'");    // 총 옵션 수
				$optTotalCnt=0;

				$optResult = $wpdb->get_results("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$_REQUEST['tData']."' ORDER BY goods_option_item_rank ASC");
				foreach($optResult as $i=>$optData) {

					$no=$totalCnt-$i;
					$style_color=$gCountFlag="";

					if($optData->goods_option_item_overprice>'0') $addPrice=number_format($optData->goods_option_item_overprice);
					else $addPrice='0';

					if($optData->goods_option_item_count>'0') $optCnt=number_format($optData->goods_option_item_count);
					else $optCnt='0';

					if($data->goods_count_flag=='option_count' && (!$optData->goods_option_item_count || $optData->goods_option_item_count<='0' || $optData->goods_option_item_soldout=='soldout')){
						$style_color="color:#ED1C24;";
					}
			?>
				<tr>
					<td style="text-align:center;<?php echo $style_color;?>"><?php echo $no;?></td>
					<td style="padding-left:30px;<?php echo $style_color;?>"><?php echo $optData->goods_option_title;?><?php echo $gCountFlag;?></td>
					<td style="text-align:center;<?php echo $style_color;?>"><?php echo $addPrice;?> 원</td>
					<td style="text-align:center;<?php echo $style_color;?>"><?php echo $optCnt;?></td>
					<td style="text-align:center;"><?php echo ($optData->goods_option_item_soldout=='soldout')?"<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_checked_red_16.png' style='width:12px;height:12px;' alt='품절체크 된 옵션입니다.' />":"";?></td>
				</tr>
			<?php
					$optTotalCnt +=$optCnt;
				}
		?>
				<tr>
					<td style="background-color:#F0F0F0;text-align:center;">합계</td>
					<td style="background-color:#F0F0F0;text-align:center;">&nbsp;</td>
					<td style="background-color:#F0F0F0;text-align:center;">&nbsp;</td>
					<td style="background-color:#F0F0F0;text-align:center;"><?php echo number_format($optTotalCnt);?></td>
					<td style="background-color:#F0F0F0;text-align:center;">&nbsp;</td>
				</tr>
			</table>

			<div class="clearfix" style="height:30px;"></div>
		</div>
	</div>
</body>
</html>

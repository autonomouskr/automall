<?
$page=$_REQUEST['page'];
$s_period_1= (!$_REQUEST['s_period_1'])?date("Y-m-d",mktime(0,0,0,1,1,date("Y"))):$_REQUEST['s_period_1'];  // 한 페이지에 표시될 목록수   
$s_period_2= (!$_REQUEST['s_period_2'])?date("Y-m-d",mktime(0,0,0,date("m"),date("t"),date("Y"))):$_REQUEST['s_period_2'];  // 한 페이지에 표시될 목록수   
?>
<script language="javascript">
	jQuery(document).ready(function() {
		jQuery('#s_keyword').keyup(function(e) {
			if (e.keyCode == 13) search_submit();       
		});

		// 날짜(datepicker) initialize (1)
		jQuery.datepicker.regional['ko']= {
			closeText:'닫기',
			prevText:'이전달',
			nextText:'다음달',
			currentText:'오늘',
			monthNames:['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUM)','7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
			monthNamesShort:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames:['일','월','화','수','목','금','토'],
			dayNamesShort:['일','월','화','수','목','금','토'],
			dayNamesMin:['일','월','화','수','목','금','토'],
			weekHeader:'Wk',
			dateFormat:'yy-mm-dd',
			firstDay:0,
			isRTL:false,
			showMonthAfterYear:true,
			yearSuffix:''
		};
		jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ko']);

		// 날짜(datepicker) initialize (2)
		jQuery(".datepicker").datepicker(jQuery.datepicker.regional["ko"]);
		jQuery('.datepicker').datepicker('option', {dateFormat:'yy-mm-dd'});
	});

	
	function search_submit(){
		var page="<?php echo $page;?>";
		var cType="<?php echo $cType;?>";
		var s_period_1=jQuery("#s_period_1").val();
		var s_period_2=jQuery("#s_period_2").val();

		var stDate=s_period_1.replace(/-/gi,"");
		var enDate=s_period_2.replace(/-/gi,"");

		if(stDate>enDate){
			alert("시작일을 종료일 보다 작거나 같게 선택해 주세요.      ");
			return;
		}

		if(parseInt(enDate)-parseInt(stDate)>10000){
			alert("검색기간을 1년 이내로  선택해 주세요.      ");
			return;
		}

		var strPara="cType="+cType+"&page="+page;

		if(s_period_1) strPara +="&s_period_1="+s_period_1;
		if(s_period_2) strPara +="&s_period_2="+s_period_2;

		window.location.href ="admin.php?"+strPara;
	}
</script>
<?php
$sOption="";

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND order_date>='".$s_period_1_time."'";
}

if($s_period_2){
	$tmp_2_priod=explode("-",$s_period_2);
	$s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
	$sOption .=" AND order_date<='".$s_period_2_time."'";
}

$s_total = $wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order  WHERE idx<>''".$sOption);    // 총 상품수
?>

	<div class="clearfix"></div>
	<div style="margin-top:30px;">
		<ul class='title-sub-desc none-content'>
			<li style="vertical-align:top;">
				<select name="st_year" id="s_type"><option value="order_date">주문일</option></select>
			</li>
			<li>
				<input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />
			</li>
			<li style="vertical-align:top;">
				<input type="submit" name="search-query-submit" id="search-query-submit" onClick="search_submit();" class="button apply" value="검색"  />		
			</li>
		</ul>
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:20px;">
		<form name="goodsFrm" id="goodsFrm">
		<input type="hidden" name="tMode" id="tMode" value="">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="7%"><col width="11%"><col><col width="11%"><col width="11%"><col width="11%"><col width="11%"><col width="11%"></colgroup>
				<tr>
					<th>번호</th>
					<th>이미지</th>
					<th>상품명</th>
					<th>상품정보</th>
					<th>주문건수</th>
					<th>기본상품 결제금액</th>
					<th>추가상품 결제금액</th>
					<th>총 결제금액</th>
				</tr>
		<?php 
		if($s_total>'0'){
			$result = $wpdb->get_results("SELECT order_no FROM bbse_commerce_order WHERE idx<>''".$sOption." ORDER BY idx DESC");

			$dQuery="";
			foreach($result as $i=>$data) {
				if($dQuery) $dQuery .=",";
				$dQuery .="'".$data->order_no."'";
			}

			$dResult = $wpdb->get_results($wpdb->prepare("SELECT order_no,goods_idx,goods_name,goods_unique_code,goods_barcode,goods_location_no,goods_basic_img,count(*) AS cnt, sum(goods_basic_total) AS goods_basic_total, sum(goods_add_total) AS goods_add_total FROM bbse_commerce_order_detail WHERE idx<>'' AND order_no IN (".$dQuery.") GROUP BY goods_idx ORDER BY cnt DESC LIMIT %d",Array('100')));

			foreach($dResult as $d=>$dData) {
				unset($basicImg);

				if($dData->goods_basic_img) 	$basicImg = wp_get_attachment_image_src($dData->goods_basic_img);

				if(!$basicImg['0']){
					$goodsAddImg=$wpdb->get_var("SELECT goods_add_img FROM bbse_commerce_goods WHERE idx='".$dData->goods_idx."'"); 

					$imageList=explode(",",$goodsAddImg);
					if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
					else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
				}

		?>
				<tr>
					<td style="text-align:center;"><?php echo ($d+1)?></td>
					<td style="text-align:center;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $dData->goods_idx;?>" target="_blank"><img src="<?php echo $basicImg['0'];?>" class="list-goods-img2"></a></td>
					<td><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $dData->goods_idx;?>" target="_blank"><?php echo $dData->goods_name;?></a></td>
					<td style="padding-left:15px;">
						<?php if($dData->goods_unique_code){?><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_unique_code.png" align="absmiddle" title="고유번호" /> <?php echo $dData->goods_unique_code;?><br /><?php }?>
						<?php if($dData->goods_barcode){?><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_barcode.png" align="absmiddle" title="바코드" /> <?php echo $dData->goods_barcode;?><br /><?php }?>
						<?php if($dData->goods_location_no){?><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_location_no.png" align="absmiddle" title="위치정보" /> <?php echo $dData->goods_location_no;?><br /><?php }?>
					</td>
					<td style="text-align:center;"><span class="titleH5 emBlue"><?php echo $dData->cnt;?>건</span></td>
					<td style="text-align:center;"><span class="emBlue">(+) <?php echo number_format($dData->goods_basic_total);?>원</span></td>
					<td style="text-align:center;"><span class="emRed">(-) <?php echo number_format($dData->goods_add_total);?>원</span></td>
					<td style="text-align:center;"><span class="emBlue">(+) <?php echo number_format($dData->goods_basic_total+$dData->goods_add_total);?>원</span></td>
				</tr>
	<?php
			}
		}
		else{
	?>
				<tr>
					<td style="height:30px;text-align:center;" colspan="8">주문정보가 존재하지 않습니다.</td>
				</tr>
	<?php 
	}
	?>
			</table>
		</div>
	
		</form>
	</div>

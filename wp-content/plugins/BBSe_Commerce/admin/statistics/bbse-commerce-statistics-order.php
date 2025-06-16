<?php
$page=$_REQUEST['page'];
$s_period_1= (!$_REQUEST['s_period_1'])?date("Y-m-d",mktime(0,0,0,1,1,date("Y"))):$_REQUEST['s_period_1'];  // 한 페이지에 표시될 목록수   
$s_period_2= (!$_REQUEST['s_period_2'])?date("Y-m-d",mktime(0,0,0,date("m"),date("t"),date("Y"))):$_REQUEST['s_period_2'];  // 한 페이지에 표시될 목록수
$s_period_3= (!$_REQUEST['s_period_3'])?date("Y-m-d",mktime(0,0,0,1,1,date("Y"))):$_REQUEST['$s_period_3'];  // 한 페이지에 표시될 목록수
$s_period_4= (!$_REQUEST['s_period_4'])?date("Y-m-d",mktime(0,0,0,1,1,date("Y"))):$_REQUEST['$s_period_4'];  // 한 페이지에 표시될 목록수
$s_period_5= (!$_REQUEST['s_period_5'])?date("Y-m-d",mktime(0,0,0,1,1,date("Y"))):$_REQUEST['$s_period_5'];  // 한 페이지에 표시될 목록수
$bzName= (!$_REQUEST['bzName'])?"":$_REQUEST['bzName'];  // 한 페이지에 표시될 목록수

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
		
		let checkOption = jQuery('#order_select option:selected').val();
        
		let options = {
			pattern: 'yyyy-mm',
			selectedYear:2024,
			startYear:2024,
			finalYear:2040,
			monthNames: ['1월(JAN)', '2월(FEB)', '3월(MAR)', '4월(APR)', '5월(MAY)', '6월(JUN)',
                '7월(JUL)', '8월(AUG)', '9월(SEP)', '10월(OCT)', '11월(NOV)', '12월(DEC)'],
            monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
           	openOnFocus:true,
			firstDay:0,
			isRTL:false,
			showMonthAfterYear:true,
			yearSuffix:'',
			changeMonth: true, 
			changeYear: true
		};
		
		//jQuery('.datapicker_2').monthpicker(options);

        /* if(checkOption == 'order_month'){
        	jQuery('#s_period_1').hide(); //주문일 start
        	jQuery('#s_period_2').hide(); //주문일 end
        	jQuery('#s_period_4').hide(); //년도
        	jQuery('#s_period_4').hide(); //년도
        }else if(checkOption == 'order_year'){
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_2_list').hide(); //년도
        	jQuery('#s_period_3_list').hide(); //월
        }else if(checkOption == 'order_day'){ // order_day
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_2_list').hide(); //년도
        }else{
        
        } */
	});

	
	function search_submit(){
		var page="<?php echo $page;?>";
		var s_period_1=jQuery("#s_period_1").val();
		var cType="<?php echo $cType;?>";
		var s_period_2=jQuery("#s_period_2").val();
		var bzName = jQuery("#bzName").val();

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
		if(bzName!=null && bzName != "") strPara +="&bzName="+bzName;

		window.location.href ="admin.php?"+strPara;
	}
	
	function user_list(){
		var popupTitle="회원조회";
		var bzNm = jQuery("#bzName").val();

		var tbHeight = window.innerHeight * .60;
		var tbWidth = window.innerWidth * .35;
		tb_show(popupTitle, "<?php echo bloginfo('template_url')?>/admin/bbse-commerce-coupon-userInfo-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;bzNm="+bzNm+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-coupon-userInfo-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;bzNm="+bzNm+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}
	
	//상품 등록 팝업 닫기
	function remove_popup(){
		tb_remove();
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

/* if($s_period_3){
    $tmp_2_priod=explode("-",$s_period_3);
    $s_period_3_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
    $sOption .=" AND order_date<='".$s_period_3_time."'";
}

if($s_period_4){
    $tmp_2_priod=explode("-",$s_period_4);
    $s_period_4_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
    $sOption .=" AND order_date<='".$s_period_4_time."'";
}

if($s_period_5){
    $tmp_2_priod=explode("-",$s_period_5);
    $s_period_5_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
    $sOption .=" AND order_date<='".$s_period_5_time."'";
} */

if($bzName){
    $sOption .=" AND input_name like '%".$bzName."%'";
    
}

$s_total = $wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order  WHERE idx<>''".$sOption);    // 총 상품수
?>

	<div class="clearfix"></div>
	<div style="margin-top:30px;">
		<ul class='title-sub-desc none-content'>
			<li style="vertical-align:top;">
				<select name="st_year" onchange="changeSelect();">
    				<option id="s_type" value="order_date">주문일</option>
<!--     				<option id="order_month" value="order_month">월별</option>
    				<option id="order_year" value="order_year">년도별</option>
    				<option id="order_day" value="order_day">일별</option> -->
				</select>
			</li>
			<li>
				<input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
				<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />
			</li>
		<li style="vertical-align:top;">
				<input type="text" name="bzName[]" id="bzName" style="height: 28px;margin: 0 4px 0 0;width:150px;cursor:pointer;background:#ffffff;text-align:center;" value = "<?php echo $bzName;?>" placeholder = "업체명 입력"/>
				<button type="button"class="button-small green" onClick="user_list();" style="height:30px;">회원조회</button>
			</li>
			
<!--						<li style="vertical-align:top;" id="s_period_5">
				<input type="text" name="s_period_5" id="s_period_5" class="datepicker" value="<?php echo s_period_5;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;
				<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_5').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;
			</li>
       	<li style="vertical-align:top;" id="s_period_3">
				<input type="text" name="s_period_3[]" id="s_period_3" class="datapicker_2" value="2024-01" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly="">
        		<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_3').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;
        	</li>
			<li style="vertical-align:top;" id="s_period_4">
				<select name="s_period_4[]" id="s_period_4[]" style="background:#ffffff;text-align:center;">
				<?php
				    $startYear = 2024;
				    $endYear = $startYear + 10;
				    for($i=$startYear; $i<=$endYear; $i++){
                ?>
				<option value="order_year"><?php echo $i;?></option>
				<?php 
				    }
				?>
        		</select>
			</li>
			<li style="vertical-align:top;">
				<input type="text" name="bzName[]" id="bzName" style="height: 28px;margin: 0 4px 0 0;width:150px;cursor:pointer;background:#ffffff;text-align:center;" value = "<?php echo $bzName;?>" placeholder = "업체명 입력"/>
				<button type="button"class="button-small green" onClick="user_list();" style="height:30px;">회원조회</button>		
			</li> -->
			<li style="vertical-align:top;">
				<input type="submit" name="search-query-submit" id="search-query-submit" onClick="search_submit();" class="button apply" value="검색"  />		
			</li>
			<br/>
			<br/>
			 
			<li style="vertical-align:top;">
				<label style="margin-top: 10px; font-size: 15px;"><strong>주문건수</strong>&nbsp;:&nbsp;&nbsp;</label><label name = "orderCount[]" id="orderCount" style="font-size: 15px; margin-top: 10px;"><strong><?php echo $s_total;?></strong> &nbsp;&nbsp;건</label>
			</li>
			
		</ul>
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:20px;">
		<form name="goodsFrm" id="goodsFrm">
		<input type="hidden" name="tMode" id="tMode" value="">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="12%"><col width="11%"><col width="11%"><col width="11%"><col width="11%"><col width="11%"><col width="11%"><col width="12%"><col width="12%"></colgroup>
				<tr>
					<th>주문기간</th>
					<th>총주문</th>
					<th>입금대기</th>
					<th>결제완료</th>
					<th>배송준비</th>
					<th>배송중</th>
					<th>배송완료</th>
					<th>구매확정</th>
					<th>취소/반품</th>
				</tr>
	<?php 
	if($s_total>'0'){

		for($y=$tmp_2_priod['0'];$y>=$tmp_1_priod['0'];$y--){
			if($tmp_2_priod['0']==$tmp_1_priod['0']){
				$mStart=$tmp_2_priod['1'];
				$mEnd=$tmp_1_priod['1'];
			}
			else{
				if($y==$tmp_2_priod['0']){
					$mStart=$tmp_2_priod['1'];
					$mEnd=1;
				}
				elseif($y==$tmp_1_priod['0']){
					$mStart=12;
					$mEnd=$tmp_1_priod['1'];
				}
				else{
					$mStart=12;
					$mEnd=1;
				}
			}

			
			for($m=$mStart;$m>=$mEnd;$m--){
				$stDate = mktime(0, 0 , 0, $m, 1, $y);
				$end_day = date("t", $stDate);
				$enDate = mktime(23, 59 , 59, $m, $end_day, $y);
				$addOption = "";
				if($bzName != null && $bzName != ""){
				    $addOption = "AND input_name like '%".$bzName."%'";
				}
				$totalData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." order_status<>'TR' AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");
				$prData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." AND order_status='PR' AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");

				$peData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." AND order_status='PE' AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");
				$drData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." AND order_status='DR' AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");

				$diData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." AND order_status='DI' AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");
				$deData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." AND order_status='DE' AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");

				$oeData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." AND order_status='OE' AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");
				$caData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$addOption." AND (order_status='CA' OR order_status='CE' OR order_status='RA' OR order_status='RE') AND order_date>='".$stDate."' AND order_date<='".$enDate."' ORDER BY idx DESC");
?>
				<tr>
					<td style="text-align:center;"><?php echo date("Y년 m월",$stDate);?></td>
					<td style="text-align:center;"><?php echo number_format($totalData->total);?>원 (<?php echo number_format($totalData->cnt);?>)</td>
					<td style="text-align:center;"><?php echo number_format($prData->total);?>원 (<?php echo number_format($prData->cnt);?>)</td>
					<td style="text-align:center;"><?php echo number_format($peData->total);?>원 (<?php echo number_format($peData->cnt);?>)</td>
					<td style="text-align:center;"><?php echo number_format($drData->total);?>원 (<?php echo number_format($drData->cnt);?>)</td>
					<td style="text-align:center;"><?php echo number_format($diData->total);?>원 (<?php echo number_format($diData->cnt);?>)</td>
					<td style="text-align:center;"><?php echo number_format($deData->total);?>원 (<?php echo number_format($deData->cnt);?>)</td>
					<td style="text-align:center;"><span class="titleH5 emBlue">(+) <?php echo number_format($oeData->total);?>원 (<?php echo number_format($oeData->cnt);?>)</span></td>
					<td style="text-align:center;"><span class="titleH5 emRed">(-) <?php echo number_format($caData->total);?>원 (<?php echo number_format($caData->cnt);?>)</span></td>
				</tr>
<?php
			}
		}
	}
	else{
	?>
				<tr>
					<td style="height:30px;text-align:center;" colspan="9">주문정보가 존재하지 않습니다.</td>
				</tr>
	<?php 
	}
	?>
			</table>
		</div>
		</form>
	</div>

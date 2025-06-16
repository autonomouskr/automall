<?php
$page=$_REQUEST['page'];
$year = date("Y");
$month = date("m");
$day = date("d");
$s_period_1= (!$_REQUEST['s_period_1'])?date("Y-m-d",mktime(0,0,0,$month,1,date("Y"))):$_REQUEST['s_period_1']; //기간 시작  
$s_period_2= (!$_REQUEST['s_period_2'])?date("Y-m-d",mktime(0,0,0,$month,date("d"),date("Y"))):$_REQUEST['s_period_2']; //기간 끝
if(!$_REQUEST['s_period_1']){
    $s_period_3  = date("Y-m-t",mktime(0,0,0,$month-1,1,date("Y")));
}else{
    $tmp_1=explode("-",$s_period_1);
    $s_period_3  = date("Y-m-t",mktime(0,0,0,$tmp_1[1]-1,1,date("Y")));
}
$s_period_4= (!$_REQUEST['s_period_4'])?date("Y-m-d",mktime(0,0,0,$month,1,date("Y"))):$_REQUEST['s_period_4']; //년도
?>
<script src="/wp-content/plugins/BBSe_Commerce/js/datepicker/jquery.mtz.monthpicker.js"></script>
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
			yearSuffix:'',
			changeMonth: true, 
			changeYear: true
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
		
		jQuery('.datapicker_2').monthpicker(options);

        /* if(checkOption == 'order_month'){
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_2_list').hide(); //년도
        }else if(checkOption == 'order_year'){
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_3_list').hide(); //월
        }else{ // order_day
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_2_list').hide(); //년도
        } */
        
	});

	
	function search_submit(){
		var page="<?php echo $page;?>";
		var cType="<?php echo $cType;?>";
		var s_period_1=jQuery("#s_period_1").val();
		var s_period_2=jQuery("#s_period_2").val();
		var s_bzName=jQuery("#bzName").val();

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
		if(s_bzName!=null && s_bzName != "") strPara +="&s_bzName="+s_bzName;

		window.location.href ="admin.php?"+strPara;
	}
	
	function changeSelect(){
		let langSelect = document.getElementById("order_select");
	    let selectValue = langSelect.options[langSelect.selectedIndex].value;
    	
        if(selectValue == 'order_month'){
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_2_list').hide(); //년도
        	jQuery('#s_period_3_list').show(); //월
        }else if(selectValue == 'order_year'){
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_3_list').hide(); //월
        	jQuery('#s_period_2_list').show(); //년도
        }else{ // order_day
        	jQuery('#s_period_3_list').hide(); //월
        	jQuery('#s_period_2_list').hide(); //년도
        	jQuery('#s_period_1_list').show(); //일
        }
	}
	
	function user_list(){
		var popupTitle="회원조회";
		var s_bzName = jQuery("#bzName").val();

		var tbHeight = window.innerHeight * .60;
		var tbWidth = window.innerWidth * .35;
		tb_show(popupTitle, "<?php echo bloginfo('template_url')?>/admin/bbse-commerce-coupon-userInfo-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;bzNm="+s_bzName+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-coupon-userInfo-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;bzNm="+s_bzName+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}
	
	//상품 등록 팝업 닫기
	function remove_popup(){
		tb_remove();
	}
	
</script>
<?php
$sOption = "";
$bzName = $_REQUEST['s_bzName'];
$sQuery = " group by order_name";
$noPaydate = "";


if($bzName != null && $bzName != ''){
    $sOption .= " AND order_name like '%".$bzName."%'";
}else{
    $sOption = "";
}

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND order_date>='".$s_period_1_time."'";
	$sReOption .=" AND order_date>='".$s_period_1_time."'";
}

if($s_period_2){
    $tmp_2_priod=explode("-",$s_period_2);
    $s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
    $sOption .=" AND order_date<='".$s_period_2_time."'";
    $sReOption .=" AND order_date<='".$s_period_2_time."'";
}

if($s_period_3){
    $tmp_3_priod=explode("-",$s_period_3);
    $s_period_3_time=mktime('23','59','59',$tmp_3_priod['1'],$tmp_3_priod['2'],$tmp_3_priod['0']);
    $sNoPayOption .=" AND order_date<='".$s_period_3_time."'";
}


$s_total = $wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order  WHERE idx<>''".$sOption.$sQuery);    // 총 상품수
?>

	<div class="clearfix"></div>
	<div class="borderBox">
		<a href="http://license.bbsetheme.com/download_zip/bbse_goods_csv_sample.zip"></a>
		*  배송완료는 월말결제 회원의 입금 전 단계 상태입니다. <br />
		*  <span class="emRed">미수금액</span> 선택한 기간의 <span class="emBlue">배송완료</span>상태의 금액의 합입니다.<br />
		*  <span class="emRed">총구매금액</span>은  <span class="emBlue">기간별 배송완료금액 + 기간별 반품/취소금액 + 기간별 구매확정금액의 </span> 총합입니다.<br />
		*  <span class="emRed">총청구금액</span>은  <span class="emBlue">총구매금액+미수금액-반품/취소금액의</span> 총합입니다.<br />
	</div>

	<div style="margin-top:30px;">
		<ul class='title-sub-desc none-content'>
			
			<li style="vertical-align:top;">
				<select name="order_select" id="order_select" onchange="changeSelect();">
				<!-- <option id="order_month" value="order_month">월별</option>
				<option id="order_year" value="order_year">년도별</option> -->
				<option id="order_day" value="order_day">기간별</option></select>
			</li>
			<li style="vertical-align:top;" id="s_period_1_list">
				<input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
				<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />
			</li>
			<!-- <li style="vertical-align:top;" id="s_period_2_list">
				<select name="s_period_2" id="s_period_2" style="background:#ffffff;text-align:center;">
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
        	<li style="vertical-align:top;" id="s_period_3_list">
				<input type="text" name="s_period_3" id="s_period_3" class="datapicker_2" value="<?php echo $s_period_3;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly="">
        		<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_3').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;
        	</li>
        	 -->
			<li style="vertical-align:top;">
        		<input type="text" name="bzName[]" id="bzName" style="height: 28px;margin: 0 4px 0 0;width:150px;cursor:pointer;background:#ffffff;text-align:center;" value = "<?php echo $bzName;?>" placeholder = "업체명 입력"/>
        		<button type="button"class="button-small green" onClick="user_list();" style="height:30px;">회원조회</button>
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
				<colgroup><col width="8%"><col width="11%"><col width="11%"><col width="11%"><col width="11%"><col width="11%"><col width="11%"></colgroup>
				<tr>
					<th>번호</th>
					<th>회원명</th>
					<th>주문건수</th>
					<th>총구매금액</th>
					<th>미수금액</th> <!--배송완료건-->
					<th>반품/취소금액</th>
					<th>총청구금액</th>
				</tr>
	<?php 
	if($s_total>'0'){
        $result = $wpdb->get_results("SELECT user_id,count(*) AS cnt,sum(goods_total) AS goods_total,sum(use_earn) AS use_earn,sum(cost_total) AS cost_total FROM bbse_commerce_order WHERE idx<>''".$sOption." and order_status <> 'TR' GROUP BY user_id ORDER BY cnt");
        foreach($result as $i=>$data) {
            if($data->user_id){
                $mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$data->user_id."'");
                $memberInfo=$mData->name." <span style=\"color:#00A2E8;\">(<span onClick=\"user_view('".$mData->user_id."');\" style=\"cursor:pointer;\">".$mData->user_id."</span>)</span>";
                $query = "select count(*) AS cnt, sum(cost_total) total from bbse_commerce_order where	(order_status = 'CA'		OR order_status = 'CE'		OR order_status = 'RA'		OR order_status = 'RE'		or order_status = 'DE'		or order_status = 'OE') ".$sOption." and user_id = '".$data->user_id."' group by user_id";
                $totalPay = $wpdb->get_row("select count(*) AS cnt, sum(cost_total) total from bbse_commerce_order where	(order_status = 'CA'		OR order_status = 'CE'		OR order_status = 'RA'		OR order_status = 'RE'		or order_status = 'DE'		or order_status = 'OE') ".$sOption." and user_id = '".$data->user_id."' group by user_id");
                
                $query2 = "SELECT count(*) AS cnt, sum(cost_total) AS cost_total FROM bbse_commerce_order WHERE idx<>'' ".$sNoPayOption." and order_status = 'DE' and user_id='".$data->user_id."' GROUP BY user_id ORDER BY cnt DESC";
                $nopayTotal = $wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS cost_total FROM bbse_commerce_order WHERE idx<>'' ".$sNoPayOption." and order_status = 'DE' and user_id='".$data->user_id."' GROUP BY user_id ORDER BY cnt DESC");
                $prData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND order_status='PR' and user_id='".$data->user_id."' ORDER BY idx DESC"); //입금대기
                $peData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND order_status='PE' and user_id='".$data->user_id."' ORDER BY idx DESC"); //결제완료
                $drData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND order_status='DR' and user_id='".$data->user_id."' ORDER BY idx DESC"); //배송준비
                $diData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND order_status='DI' and user_id='".$data->user_id."' ORDER BY idx DESC"); //배송중
                $deData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND order_status='DE' and user_id='".$data->user_id."' ORDER BY idx DESC"); //배송완료
                $oeData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND order_status='OE' and user_id='".$data->user_id."' ORDER BY idx DESC"); //구매확정
                $query = "SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND (order_status='CA' OR order_status='CE' OR order_status='RA' OR order_status='RE') and user_id='".$data->user_id."' ORDER BY idx DESC";
                $caData=$wpdb->get_row("SELECT count(*) AS cnt, sum(cost_total) AS total FROM bbse_commerce_order WHERE idx<>'' ".$sOption." AND (order_status='CA' OR order_status='CE' OR order_status='RA' OR order_status='RE') and user_id='".$data->user_id."' ORDER BY idx DESC"); //취소신청,취소완료,반품신청,반품완료
                
                $t = $totalPay->total;
                $n = $nopayTotal->cost_total;
                if(empty($n)){
                    $n = 0;
                }
                $ca = $caData->total;
                if(empty($ca)){
                    $ca= 0;
                }
                $total = $t+$n-$ca;
            }
            
            ?>
			<tr>
				<td style="text-align:center;"><?php echo ($i+1)?></td>
				<td style="text-align:center;"><?php echo $memberInfo;?></td>
				<td style="text-align:center;"><span class="titleH5 emBlue"><?php echo $data->cnt;?>건</span></td>
				<td style="text-align:center;"><span class="emRed">(+) <?php echo number_format($totalPay->total);?>원</span></td>
				<td style="text-align:center;"><span class="emRed">(+) <?php echo number_format($nopayTotal->cost_total);?>원</span></td><!-- 미수: 당월 이전 배송완료건에 있는 값 -->
				<td style="text-align:center;"><span class="emBlue">(-) <?php echo number_format($caData->total);?>원</span></td> <!-- 취소신청,취소완료,반품신청,반품완료 -->
				<td style="text-align:center;"><span class=""><?php echo number_format($total);?>원</span></td>
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

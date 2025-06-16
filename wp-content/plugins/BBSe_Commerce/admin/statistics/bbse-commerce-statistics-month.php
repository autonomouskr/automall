<?php
$page=$_REQUEST['page'];
$s_period_1= (!$_REQUEST['s_period_1'])?date("Y-m-d",mktime(0,0,0,1,1,date("Y"))):$_REQUEST['s_period_1'];  // 한 페이지에 표시될 목록수   
$s_period_3= (!$_REQUEST['s_period_3'])?date("Y-m-d",mktime(0,0,0,1,1,date("Y"))):$_REQUEST['$s_period_3'];  // 한 페이지에 표시될 목록수   
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

        if(checkOption == 'order_month'){
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_2_list').hide(); //년도
        }else if(checkOption == 'order_year'){
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_3_list').hide(); //월
        }else{ // order_day
        	jQuery('#s_period_1_list').hide(); //일
        	jQuery('#s_period_2_list').hide(); //년도
        }
        
	});

	
	function search_submit(){
		var page="<?php echo $page;?>";
		var s_period_1=jQuery("#s_period_1").val();

		var stDate=s_period_1.replace(/-/gi,"");

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
	
</script>
<?php
$sOption="";

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND order_date>='".$s_period_1_time."'";
}

$s_total = $wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order  WHERE idx<>''".$sOption);    // 총 상품수
?>

	<div class="clearfix"></div>
	<div style="margin-top:30px;">
		<ul class='title-sub-desc none-content'>
			<li style="vertical-align:top;">
				<select name="order_select" id="order_select" onchange="changeSelect();"><option id="order_month" value="order_month">월별</option><option id="order_year" value="order_year">년도별</option><option id="order_day" value="order_day">일별</option></select>
			</li>
			<li style="vertical-align:top;" id="s_period_1_list">
				<input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;
				<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;
			</li>
			<li style="vertical-align:top;" id="s_period_2_list">
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
				<input type="text" name="s_period_3" id="s_period_3" class="datapicker_2" value="2024-01" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly="">
        		<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_3').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;
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
				<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup>
				<tr>
					<th>회원아이디</th>
					<th>회원등급</th>
					<th>주문기간</th>
					<th>주문건수</th>
					<th>청구총금액</th>
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
    ?>
    
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

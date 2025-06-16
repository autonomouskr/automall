<?php
$page=$_REQUEST['page'];
$s_keyword=$_REQUEST['s_keyword'];

$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치
?>

<style>

select{
    width:120px
}
.search{
    border: 1px solid
}
.modal_btn {
    display: block;
    margin: 40px auto;
    padding: 10px 20px;
    background-color: royalblue;
    border: none;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
    transition: box-shadow 0.2s;
}
.modal_btn:hover {
    box-shadow: 3px 4px 11px 0px #00000040;
}

/*모달 팝업 영역 스타일링*/
.modal {
/*팝업 배경*/
	display: none; /*평소에는 보이지 않도록*/
    position: absolute;
    top:0;
    left: 0;
    width: 100%;
    height: 100vh;
    overflow: hidden;
    background: rgba(0,0,0,0.5);
}
.modal .modal_popup {
/*팝업*/
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background: #ffffff;
    border-radius: 20px;
}
.modal .modal_popup .close_btn {
    display: block;
    padding: 10px 20px;
    background-color: rgb(116, 0, 0);
    border: none;
    border-radius: 5px;
    color: #fff;
    cursor: pointer;
    transition: box-shadow 0.2s;
}

.modal.on {
    display: block;
}

.apply{
    width: 125px;
}

.tab-container { width: 100%; margin: auto; border:1px solid;}
.tabs { display: flex; cursor: pointer; border-bottom: 2px solid #ddd; }
.tab { flex: 1; padding: 10px; text-align: center; background: #f1f1f1; }
.tab.active { background: #fff; border-bottom: 2px solid #3498db; }
.tab-content { display: none; padding: 15px; border: 1px solid #ddd; }
.tab-content.active { display: block; }
        
</style>
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

	function view_list(sList){
		var goUrl="";
		var per_page=jQuery("#per_page").val();
		if(sList=='all') goUrl="admin.php?page=bbse_commerce_account&per_page="+per_page;
		else goUrl="admin.php?page=bbse_commerce_account&s_list="+sList+"&per_page="+per_page;
		window.location.href =goUrl;
	}
	
	function search_submit(){
		var page="<?php echo $page;?>";
		var s_list="<?php echo $s_list;?>";
		var per_page=jQuery("#per_page").val();
		var s_keyword=jQuery("#s_keyword").val();
		var s_period_1=jQuery("#s_period_1").val();
		var s_period_2=jQuery("#s_period_2").val();
		var s_type=jQuery("#s_type").val();

		var strPara="page="+page+"&s_list="+s_list+"&per_page="+per_page;

		if(s_keyword) strPara +="&s_keyword="+s_keyword;
		if(s_period_1) strPara +="&s_period_1="+s_period_1;
		if(s_period_2) strPara +="&s_period_2="+s_period_2;
		if(s_type) strPara +="&s_type="+s_type;

		window.location.href ="admin.php?"+strPara;
	}

	function checkAll(){
		
		if(jQuery("#check_all").is(":checked")){
			jQuery("input[name=check\\[\\]]").attr("checked",true);
		}		
		else{
			jQuery("input[name=check\\[\\]]").attr("checked",false);
		}
	}

	function excel_down(){
		var s_period_1=jQuery("#s_period_1").val();
		var s_period_2=jQuery("#s_period_2").val();
		var s_type=jQuery("#s_type").val();
		var s_keyword=jQuery("#s_keyword").val();
		var s_list="<?php echo $s_list;?>";

		var tbHeight = 600;
		var tbWidth=890;
		tb_show("주문정보 - 엑셀 다운로드", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-order-excel-down.php?s_period_1="+s_period_1+"&#38;s_period_2="+s_period_2+"&#38;s_type="+s_type+"&#38;s_keyword="+s_keyword+"&#38;s_list="+s_list+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//tb_show("주문정보 - 엑셀 다운로드", "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-popup-order-excel-down.php?s_period_1="+s_period_1+"&#38;s_period_2="+s_period_2+"&#38;s_type="+s_type+"&#38;s_keyword="+s_keyword+"&#38;s_list="+s_list+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}
	
	function search_goods(){

		var popupTitle="제품조회";
		
		var tbHeight = window.innerHeight * .60;
		var tbWidth = window.innerWidth * .35;
		tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-invenInout-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-invenInout-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		return false;
		
	}
	

</script>
<?php
$sOption="";

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$total = count($wpdb->get_results("select sum(cost_total) cost_total, sum(delivery_total) delivery_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info, year(FROM_UNIXTIME(order_date)) order_year, month(FROM_UNIXTIME(order_date)) order_month,
                                                  year(FROM_UNIXTIME(order_date))+1 pay_year, month(DATE_ADD(FROM_UNIXTIME(order_date),INTERVAL +1 month)) pay_month
                                             from bbse_commerce_order O
                                            where order_status in ('OE') ".$sOption." and payMode = '01' and order_date < (select LAST_DAY(now()-interval 1 month) + interval 1 day from dual) and pay_status = 'PW' group by user_id"));



$s_total_sql  = $wpdb->prepare("SELECT count(DISTINCT(O.idx)) FROM bbse_commerce_order AS O, bbse_commerce_order_detail AS G WHERE O.idx<>''".$sOption, $prepareParm);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

$total_pages = ceil($groupBylength / $per_page);   // 총 페이지수

$month = $wpdb->get_results("select sum(cost_total) cost_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info from bbse_commerce_order O where order_status in ('OE')  AND (pay_status='PW' or pay_status='EN' or pay_status='AR')  and payMode = '01' group by user_id");
$caseby = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and order_status in ('OE')  AND (pay_status='PW' or pay_status='EN' or pay_status='AR') ");

/* Query String */
$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);
?>
<div style="margin-top:5px;">
	<div style="float:left">
    	<button class="button-bbse blue">입고</button>
        <button class="button-bbse blue">출고</button>
        <button class="button-bbse blue">재고조사</button>
        <button class="button-bbse blue">목록초기화</button>
	</div>
	<div style="float:right">
        <button type="button" class="button-bbse blue" onClick="search_goods();">제품추가</button>
        <button class="button-bbse blue">제품삭제</button>
	</div>
	<br>
<!--	<br>
<!--	<ul class='' style="float:right;">
<!-- 		<li> -->
<!-- 			<select name='per_page' id='per_page' onChange="search_submit();"> -->
<!--				<option <?php echo ($per_page=='10')?"selected='selected'":"";?> value='10'>10개씩 보기</option>
<!--				<option <?php echo ($per_page=='20')?"selected='selected'":"";?> value='20'>20개씩 보기</option>
<!--				<option <?php echo ($per_page=='30')?"selected='selected'":"";?> value='30'>30개씩 보기</option>
<!--				<option <?php echo ($per_page=='40')?"selected='selected'":"";?> value='40'>40개씩 보기</option>
<!--				<option <?php echo ($per_page=='50')?"selected='selected'":"";?> value='50'>50개씩 보기</option>
<!-- 			</select> -->
<!-- 		</li> -->
<!-- 	</ul> -->
</div>
<br>
<div style="margin-top:20px;">
	<form name="goodsFrm" id="goodsFrm">
	<input type="hidden" name="tMode" id="tMode" value="">
	<div style="width:100%;">
		<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="5%"><col width="20%"><col width="15%"><col width="10%"><col width="10%"><col width="10%"><col width="14%"></colgroup>
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
				<th>제품명</th>
				<th>제품코드</th>
				<th>현재수량</th>
				<th>처리수량</th>
				<th>위치</th>
				<th>처리일시</th>
			</tr>
        	<?php 
        	
        	if($s_total>'0'){
        	    $checkMode = $wpdb->get_results("select * from bbse_commerce_order O  where payMode  = '01'".$sOption."");
            	if(sizeof($checkMode) > '0'){
            	    $totalByIdCost = $wpdb->get_results("select	accounts_receivable,	sum(cost_total) cost_total,	count(1) cnt,	order_name ,	input_name ,	user_id ,	payMode,	pay_status,	pay_info,	year(FROM_UNIXTIME(order_date)) order_year from	bbse_commerce_order O where	order_status in ('OE')  AND O.order_status<>'TR' AND (pay_status='PW' or pay_status='EN' or pay_status='AR') group by	user_id	");
             	    $groupByIdMonth = $wpdb->get_results("select sum(cost_total) cost_total, sum(delivery_total) delivery_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info, year(FROM_UNIXTIME(order_date)) order_year, month(FROM_UNIXTIME(order_date)) order_month
                                                               ,  year(FROM_UNIXTIME(order_date))+1 pay_year
                                                               , month(DATE_ADD(FROM_UNIXTIME(order_date),INTERVAL +1 month)) pay_month
                                                               , idx
                                                               , sum(accounts_receivable) accounts_receivable
                                                            from bbse_commerce_order O
                                                           where order_status in ('OE','DE') ".$sOption." and payMode = '01' and pay_status = 'PW' group by user_id");
            	    $num = 1;
            	    foreach($groupByIdMonth as $j=>$data){
                	    $idxs = $wpdb->get_results("select idx from bbse_commerce_order where payMode = '01' and user_id = '".$data->user_id."'");
                	    foreach ($idxs as $k=>$idx){
                	        $idxArr .= $idx->idx;
                	        
                	        if($k < sizeof($idxs)-1){
                	            $idxArr .= ",";
                	        }
                	    }
            	        $payInfo = unserialize($data->pay_info);
            	        if($data->pay_status=='PW') $btnColor="red";
            	        else $btnColor="black";
                    ?>
            				<tr>
            					<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $idxArr;?>"></td>
            					<td style="text-align:center;"><?php echo $num;?></td>
            					<td style="text-align:center;"><?php echo $data->order_name;?></td>
            					<td style="text-align:center;"><?php echo $data->input_name;?></td>
            					<td style="text-align:center;"><?php echo $data->input_name;?></td>
            					<td style="text-align:center;"><?php echo $data->input_name;?></td>
            					<td style="text-align:center;"><div><?php echo $payInfo['bank_name'],"/", $payInfo['bank_no'], "/",$payInfo['bank_owner'];?></div></td>
            				</tr>
                    <?php 
                    $num++;
            	    }
            	}
        	}else{
        	?>
        				<tr>
        					<td style="height:130px;text-align:center;" colspan="9">등록 된 주문정보가 존재하지 않습니다.</td>
        				</tr>
        	<?php 
        	}
        	?>
        			</table>
        		</div>
			</form>
		</div>

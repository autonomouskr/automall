<?php
$page=$_REQUEST['page'];
$s_list=(!$_REQUEST['s_list'])?"all":$_REQUEST['s_list'];  // 전체, 노출, 비노출, 노출품절, 휴지통
$s_period_1=$_REQUEST['s_period_1'];
$s_period_2=$_REQUEST['s_period_2'];
$s_type=$_REQUEST['s_type'];
$s_keyword=$_REQUEST['s_keyword'];

$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치
$idxArr = "";
?>

<style>

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

	function change_trash(tMode,tStatus,tData){
		if(!tStatus){
			alert("일괄 작업을 선택해 주세요.     ");
			return;
		}
		
		var idxs = "<?php echo $idxArr; ?>";
		
		if(!tData){
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var s_list="<?php echo $s_list;?>";

			for(i=0;i<chked;i++){
				if(tData) tData +=",";
				tData +=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val();
			}

			if(chked<=0 || !tData) {
				alert("일괄 작업을 실행 할 주문정보를 선택해주세요.");
				return;
			}

			if(tStatus=='trash'){
				if(!confirm('선택한 주문정보를 “휴지통”으로 이동하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='restore'){
				if(!confirm('선택한 주문정보를 이전 상태로 복원하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='empty-trash'){
				if(!confirm('영구삭제 시 주문정보는 완전삭제되어 복구되지 않습니다.\n선택한 주문정보를 "영구적으로 삭제" 하시겠습니까?       ')){
					return;
				}
			}
		}
		else if(tStatus=='trash' && tData){
			if(!confirm('해당 주문정보를 “휴지통”으로 이동하시겠습니까?   ')){
				return;
			}
		}
		else if(tData=='empty'){
			if(!confirm('휴지통을 비울 시 주문정보는 완전삭제되어 복구되지 않습니다.\n휴지통을 비우시겠습니까?       ')){
				return;
			}
		}
		else if(tData && tStatus=='empty-trash'){
			if(!confirm('영구삭제 시 주문정보는 완전삭제되어 복구되지 않습니다.\n해당 주문정보를 "영구적으로 삭제" 하시겠습니까?          ')){
				return;
			}
		}

		if(tStatus=='print-order'){
			var tbHeight = 600;
			var tbWidth=750;
			tb_show("주문서인쇄 - 총 "+chked+"개 주문", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-order-print.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
			//thickbox_resize();
			return false;
		}
		else{
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-order.exec.php',
				//url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-order.exec.php',
				data: {tMode:tMode, tStatus:tStatus, tData:tData, idxs:idxs}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						if(tStatus=='copy'){
							alert('상품이 정상적으로 복사되었습니다.   \n복사 목록에서 상품의 상태값을 수정하신 후 사용해 주세요.   ');
						}
						search_submit();
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}

	function checkAll(){
		
		if(jQuery("#check_all").is(":checked")){
			jQuery("input[name=check\\[\\]]").attr("checked",true);
		}		
		else{
			jQuery("input[name=check\\[\\]]").attr("checked",false);
		}
	}

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
	
	
	function openModal(userId){
		var tbHeight = 600;
		var tbWidth=500;
		tb_show("업체별 입금금액", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/account/bbse-commerce-account-detail-popup.php?s_userId="+userId+"&#38");
		//tb_show("업체별 입금금액", "http://localhost/wp-content/plugins/BBSe_Commerce/admin/account/bbse-commerce-account-detail-popup.php?s_userId="+userId+"&#38");
		//thickbox_resize();
		return false;
	}


</script>
<?php
$sOption="";

if($s_list){
	//if($s_list=='all') $sOption .=" AND O.order_status<>'TR' AND (order_status='PR' or order_status='EN') ";
    if($s_list=='all') $sOption .=" AND O.order_status<>'TR' AND (pay_status='PW' or pay_status='EN' or pay_status='AR') ";
	else $sOption .=" AND O.pay_status='".$s_list."'";
}

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND O.order_date>='".$s_period_1_time."'";
}

if($s_period_2){
	$tmp_2_priod=explode("-",$s_period_2);
	$s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
	$sOption .=" AND O.order_date<='".$s_period_2_time."'";
}

if($s_keyword){

	if($s_type){
		$sOption .=" AND ".$s_type." LIKE %s";
		$prepareParm[]="%".like_escape($s_keyword)."%";
	}
	else{
		$sOption .=" AND (O.order_no LIKE %s OR O.order_name LIKE %s OR O.receive_name LIKE %s OR O.input_name LIKE %s OR O.user_id LIKE %s)";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
	}
}

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$groupBylength = count($wpdb->get_results("select sum(cost_total) cost_total, sum(delivery_total) delivery_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info, year(FROM_UNIXTIME(order_date)) order_year, month(FROM_UNIXTIME(order_date)) order_month,
                                                  year(FROM_UNIXTIME(order_date))+1 pay_year, month(DATE_ADD(FROM_UNIXTIME(order_date),INTERVAL +1 month)) pay_month
                                             from bbse_commerce_order O
                                            where order_status in ('OE') ".$sOption." and payMode = '01' and order_date < (select LAST_DAY(now()-interval 1 month) + interval 1 day from dual) and pay_status = 'PW' group by user_id"));



$s_total_sql  = $wpdb->prepare("SELECT count(DISTINCT(O.idx)) FROM bbse_commerce_order AS O, bbse_commerce_order_detail AS G WHERE O.idx<>''".$sOption, $prepareParm);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

$total_pages = ceil($groupBylength / $per_page);   // 총 페이지수

$month = $wpdb->get_results("select sum(cost_total) cost_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info from bbse_commerce_order O where order_status in ('OE')  AND (pay_status='PW' or pay_status='EN' or pay_status='AR')  and payMode = '01' group by user_id");
$caseby = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and order_status in ('OE')  AND (pay_status='PW' or pay_status='EN' or pay_status='AR') ");


$monthPW = $wpdb->get_results("select sum(cost_total) cost_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info from bbse_commerce_order O where order_status in ('OE')  and O.pay_status = 'PW' and payMode = '01' group by user_id");
$casebyPW = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and order_status in ('OE') and pay_status = 'PW' ");

$totalPW = count($monthPW) + count($casebyPW);
$monthEN = $wpdb->get_results("select sum(cost_total) cost_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info from bbse_commerce_order O where order_status in ('OE')  and O.pay_status = 'EN' and payMode = '01' group by user_id");
$casebyEN = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and order_status in ('OE') and pay_status = 'EN' ");


$totolEN = sizeof($monthEN) + sizeof($casebyEN); 

$monthAR = $wpdb->get_results("select sum(cost_total) cost_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info from bbse_commerce_order O where order_status in ('OE')  and O.pay_status = 'AR' and payMode = '01' group by user_id");
$casebyAR = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and order_status in ('OE') and pay_status = 'AR' ");
$totolAR = sizeof($monthAR) + sizeof($casebyAR); 

$total_All = sizeof($month) + sizeof($caseby);


/* Query String */
$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);
?>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>입금관리</h2>
		<hr>
		<ul class='title-sub-desc'>
		    <li <?php echo ($s_list=='all')?"class=\"current\"":"";?>><a title='전체 상품 보기' href="javascript:view_list('all');">전체(<?php echo $total_All;?>)</a></li>
 			
 			<li <?php echo ($s_list=='PW')?"class=\"current\"":"";?>><a title='노출 상품 보기' href="javascript:view_list('PW');">정산대기(<?php echo $totalPW;?>)</a></li>
			<li <?php echo ($s_list=='EN')?"class=\"current\"":"";?>><a title='비노출 상품 보기' href="javascript:view_list('EN');">정산완료(<?php echo $totolEN;?>)</a></li>
			<!-- <li <?php echo ($s_list=='AR')?"class=\"current\"":"";?>><a title='비노출 상품 보기' href="javascript:view_list('AR');">미수(<?php echo $totolAR;?>)</a></li> -->
		</ul>
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:60px;">
	
<!--		<ul class='title-sub-desc none-content' style="float:right;">-->
<!-- 			<li> -->
<!--				<button type="button" class="button-bbse blue" onclick="openFileSystem();">정산파일템플릿다운로드</button> -->
<!-- 			</li> -->
<!-- 			<li> -->
<!-- 		        정산파일업로드<input id="my-input" class="button-bbse red" type="file"/> -->
<!-- 			</li> -->
<!-- 		</ul> -->
		
		<ul class='title-sub-desc none-content'>
			<li>
				<select name='bulk_action' id='bulk_action'>
					<option value=''>일괄작업</option>
					<option value='PW'>정산대기</option>
					<option value='EN'>정산완료</option>
					<option value='EN'>미수</option>
				<?php if($s_list=='TR'){?>
					<option value='restore'>이전 상태로 복원</option>
					<option value='empty-trash'>영구적으로 삭제하기</option>
				<?php }else{?>
					<?php if($s_list=='DR'){?>
					<option value='print-order'>주문서인쇄</option>
					<?php }?>
				<?php }?>
				</select>
				<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_trash(jQuery('#bulk_action').val(),jQuery('#bulk_action').val(),'');" value="적용"  />
			</li>
			<li><label style="margin-right:4px;"><strong>주문기간</strong></label><input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />		
			</li>
			<li>
				<select name='s_type' id='s_type'>
					<option value=''>통합검색</option>
					<option <?php echo ($s_type=='O.order_no')?"selected=\"selected\"":"";?> value='O.order_no'>주문번호</option>
					<option <?php echo ($s_type=='O.order_name')?"selected=\"selected\"":"";?> value='O.order_name'>주문자명</option>
					<option <?php echo ($s_type=='O.receive_name')?"selected=\"selected\"":"";?> value='O.receive_name'>수령자명</option>
					<option <?php echo ($s_type=='O.input_name')?"selected=\"selected\"":"";?> value='O.input_name'>입금자명</option>
				</select>
			</li>
			<li>
				<input type="text" name="s_keyword" id="s_keyword" value="<?php echo $s_keyword;?>" />
				<input type="submit" name="search-query-submit" id="search-query-submit" onClick="search_submit();" class="button apply" value="검색"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</li>
		</ul>
		<ul class='title-sub-desc none-content' style="float:right;">
			<li>
				<select name='per_page' id='per_page' onChange="search_submit();">
					<option <?php echo ($per_page=='10')?"selected='selected'":"";?> value='10'>10개씩 보기</option>
					<option <?php echo ($per_page=='20')?"selected='selected'":"";?> value='20'>20개씩 보기</option>
					<option <?php echo ($per_page=='30')?"selected='selected'":"";?> value='30'>30개씩 보기</option>
					<option <?php echo ($per_page=='40')?"selected='selected'":"";?> value='40'>40개씩 보기</option>
					<option <?php echo ($per_page=='50')?"selected='selected'":"";?> value='50'>50개씩 보기</option>
				</select>
			</li>
		</ul>
<!--		<ul class='title-sub-desc none-content' style="float:right;"> -->
<!--     		<li> -->
<!--    			<button type="button" onclick="checkList();" class="button-bbse red" style="width:180;height:30px;"> 엑셀 다운로드 </button> -->
<!--     		</li> -->
<!-- 		</ul> -->
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:20px;">
		<form name="goodsFrm" id="goodsFrm">
		<input type="hidden" name="tMode" id="tMode" value="">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="3%"><col width="3%"><col width="8%"><col width="8%"><col width="15%"><col width="15%"><col width="8%"><col width="8%"><col width="8%"><col width="7%"></colgroup>
				<tr>
					<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
					<th>번호</th>
					<th>업체명</th>
<!-- 					<th>주문월</th> -->
					<!-- <th>정산월</th> -->
					<th>입금자명</th>
					<th>입금계좌</th>
					<!-- <th>당월청구금액</th> -->
					<th>총청구금액</th>
					<th>입금금액</th>
					<th>잔여청구금액</th>
					<th>결제방식</th>
					<th>정산상태</th>
				</tr>
	<?php 
	
	if($s_total>'0'){
	    $checkMode = $wpdb->get_results("select * from bbse_commerce_order O  where payMode  = '01'".$sOption."");
    	if(sizeof($checkMode) > '0'){
    	    $totalByIdCost = $wpdb->get_results("select	accounts_receivable,	sum(cost_total) cost_total,	count(1) cnt,	order_name ,	input_name ,	user_id ,	payMode,	pay_status,	pay_info,	year(FROM_UNIXTIME(order_date)) order_year from	bbse_commerce_order O where	order_status in ('OE')  AND O.order_status<>'TR' AND (pay_status='PW' or pay_status='EN' or pay_status='AR') group by	user_id	");
/*     	    $groupByIdMonth = $wpdb->get_results("select sum(cost_total) cost_total, sum(delivery_total) delivery_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info, year(FROM_UNIXTIME(order_date)) order_year, month(FROM_UNIXTIME(order_date)) order_month,
                                        year(FROM_UNIXTIME(order_date))+1 pay_year
	                                   , month(DATE_ADD(FROM_UNIXTIME(order_date),INTERVAL +1 month)) pay_month
                                       , idx
                             from bbse_commerce_order O 
                    where order_status in ('OE') ".$sOption." and payMode = '01' and order_date < (select LAST_DAY(now()-interval 1 month) + interval 1 day from dual) and pay_status = 'PW' group by user_id,month(FROM_UNIXTIME(order_date))"); */
    	    
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
    					<!-- <td style="text-align:center;"><?php echo $data->order_year, "/",$data->order_month , "월";?></td> -->
<!--     					<td style="text-align:center;"><?php echo $data->pay_year, "/",$data->pay_month, "월";?></td> -->
    					<td style="text-align:center;"><?php echo $data->input_name;?></td>
    					<td style="text-align:center;"><div><?php echo $payInfo['bank_name'],"/", $payInfo['bank_no'], "/",$payInfo['bank_owner'];?></div></td>
<!--   					<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td> -->
    					<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td>
    					<td style="text-align:center;"><span class="titleH5 emBlue"><?php echo number_format($data->accounts_receivable);?>원</span>&nbsp;&nbsp;&nbsp;<button type="button" style="width:40px; height: 30px;" onclick="openModal('<?php echo $data->user_id;?>')" >상세</button></td>
    					<td style="text-align:center;"><span class="titleH5 emRed"><?php echo number_format($data->cost_total - $data->accounts_receivable);?>원</span></td>
    					<td style="text-align:center;"><strong><span><?php if($data->payMode == '01'){ echo "월말결제"; }?></span></strong></td>
    					<td style="text-align:center;">
    					<?php if($data->pay_status == 'PW'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산대기";?></span></strong></button>
    					<?php }else if($data->pay_status == 'EN'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산완료";?></span></strong></button>
    					<?php }else if($data->pay_status == 'AR'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "미수";?></span></strong></button>
						<?php }?>
    					</td>
    				</tr>
            <?php 
            $num++;
    	    }
    	}
    	
	    $checkMode = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and pay_status = 'PW' ".$sOption."");
        if(sizeof($checkMode) > '0'){
	        foreach($checkMode as $j=>$data){
    	        $payInfo = unserialize($data->pay_info);
    	        if($data->pay_status=='PW') $btnColor="red";
    	        else $btnColor="black";
    	        ?>
    				<tr>
    					<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
    					<td style="text-align:center;"><?php echo $num;?></td>
    					<td style="text-align:center;"><?php echo $data->order_name;?></td>
    					<!-- <td style="text-align:center;"><?php echo $data->order_year, "/",$data->order_month , "월";?></td> -->
    					<!--     					<td style="text-align:center;"><?php echo $data->pay_year, "/",$data->pay_month, "월";?></td> -->
    					<td style="text-align:center;"><?php echo $data->input_name;?></td>
    					<td style="text-align:center;"><div><?php echo $payInfo['bank_name'],"/", $payInfo['bank_no'], "/",$payInfo['bank_owner'];?></div></td>
    					<!--   					<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td> -->
    					
						<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td>
    					<td style="text-align:center;"><a><button  type="button" onclick="openModal('<?php echo $data->user_id;?>')" ><span class="titleH5 emBlue"><?php echo number_format($data->accounts_receivable);?>원</span></button></a></td>
    					<td style="text-align:center;"><span class="titleH5 emRed"><?php echo number_format($data->cost_total - $data->accounts_receivable);?>원</span></td>
    					<td style="text-align:center;"><strong><span><?php if($data->payMode == '02'){ echo "건별결제"; }?></span></strong></td>
    					<td style="text-align:center;">
    					<?php if($data->pay_status == 'PW'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산대기";?></span></strong></button>
    					<?php }else if($data->pay_status == 'EN'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산완료";?></span></strong></button>
    					<?php }else if($data->pay_status == 'AR'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "미수";?></span></strong></button>
						<?php }?>
    					</td>
    				</tr>
            
            <?php 
            $num++;
	        }
    	}
    	
      	$checkMode = $wpdb->get_results("select * from bbse_commerce_order O  where payMode  = '01'".$sOption."");
    	if(sizeof($checkMode) > '0'){
    	    $groupByIdMonth = $wpdb->get_results("select idx, sum(cost_total) cost_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info, year(FROM_UNIXTIME(order_date)) order_year, month(FROM_UNIXTIME(order_date)) order_month from bbse_commerce_order O where order_status in ('OE') ".$sOption." and payMode = '01' and pay_status = 'EN' group by user_id ,month(FROM_UNIXTIME(order_date))");
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
    					<!-- <td style="text-align:center;"><?php echo $data->order_year, "/",$data->order_month , "월";?></td> -->
    					<!--     					<td style="text-align:center;"><?php echo $data->pay_year, "/",$data->pay_month, "월";?></td> -->
    					<td style="text-align:center;"><?php echo $data->input_name;?></td>
    					<td style="text-align:center;"><div><?php echo $payInfo['bank_name'],"/", $payInfo['bank_no'], "/",$payInfo['bank_owner'];?></div></td>
    					<!--   					<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td> -->
						<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td>
    					<td style="text-align:center;"><a><button  type="button" onclick="openModal('<?php echo $data->user_id;?>')" ><span class="titleH5 emBlue"><?php echo number_format($data->accounts_receivable);?>원</span></button></a></td>
    					<td style="text-align:center;"><span class="titleH5 emRed"><?php echo number_format($data->cost_total - $data->accounts_receivable);?>원</span></td>
    					<td style="text-align:center;"><strong><span><?php if($data->payMode == '01'){ echo "월말결제"; }?></span></strong></td>
    					<td style="text-align:center;">
    					<?php if($data->pay_status == 'PW'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산대기";?></span></strong></button>
    					<?php }else if($data->pay_status == 'EN'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산완료";?></span></strong></button>
    					<?php }else if($data->pay_status == 'AR'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "미수";?></span></strong></button>
						<?php }?>
    					</td>
    				</tr>
            
            <?php 
            $num++;
    	    }
    	}
    	
    	$checkMode = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and pay_status = 'EN' ".$sOption."");
    	if(sizeof($checkMode) > '0'){
    	    foreach($checkMode as $j=>$data){
    	        $payInfo = unserialize($data->pay_info);
    	        if($data->pay_status=='PW') $btnColor="red";
    	        else $btnColor="black";
    	        ?>
    				<tr>
    					<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
    					<td style="text-align:center;"><?php echo $num;?></td>
    					<td style="text-align:center;"><?php echo $data->order_name;?></td>
    					<!-- <td style="text-align:center;"><?php echo $data->order_year, "/",$data->order_month , "월";?></td> -->
    					<!--     					<td style="text-align:center;"><?php echo $data->pay_year, "/",$data->pay_month, "월";?></td> -->
    					<td style="text-align:center;"><?php echo $data->input_name;?></td>
    					<td style="text-align:center;"><div><?php echo $payInfo['bank_name'],"/", $payInfo['bank_no'], "/",$payInfo['bank_owner'];?></div></td>
    					<!--   					<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td> -->
						<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td>
    					<td style="text-align:center;"><a><button  type="button" onclick="openModal('<?php echo $data->user_id;?>')" ><span class="titleH5 emBlue"><?php echo number_format($data->accounts_receivable);?>원</span></button></a></td>
    					<td style="text-align:center;"><span class="titleH5 emRed"><?php echo number_format($data->cost_total - $data->accounts_receivable);?>원</span></td>
    					<td style="text-align:center;"><strong><span><?php if($data->payMode == '02'){ echo "건별결제"; }?></span></strong></td>
    					<td style="text-align:center;">
    					<?php if($data->pay_status == 'PW'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산대기";?></span></strong></button>
    					<?php }else if($data->pay_status == 'EN'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산완료";?></span></strong></button>
    					<?php }else if($data->pay_status == 'AR'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "미수";?></span></strong></button>
						<?php }?>
    					</td>
    				</tr>
            
            <?php 
            $num++;
	        }
    	}
    	
    	
    	$checkMode = $wpdb->get_results("select * from bbse_commerce_order O  where payMode  = '01'".$sOption."");
    	if(sizeof($checkMode) > '0'){
    	    $groupByIdMonth = $wpdb->get_results("select idx, accounts_receivable, sum(cost_total) cost_total, count(1) cnt, order_date , order_name , input_name , user_id , payMode, pay_status, pay_info, year(FROM_UNIXTIME(order_date)) order_year, month(FROM_UNIXTIME(order_date)) order_month from bbse_commerce_order O where order_status in ('OE') ".$sOption." and payMode = '01' and pay_status = 'AR' group by user_id ,month(FROM_UNIXTIME(order_date))");
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
    					<!-- <td style="text-align:center;"><?php echo $data->order_year, "/",$data->order_month , "월";?></td> -->
    					<!--     					<td style="text-align:center;"><?php echo $data->pay_year, "/",$data->pay_month, "월";?></td> -->
    					<td style="text-align:center;"><?php echo $data->input_name;?></td>
    					<td style="text-align:center;"><div><?php echo $payInfo['bank_name'],"/", $payInfo['bank_no'], "/",$payInfo['bank_owner'];?></div></td>
    					<!--   					<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td> -->
						<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td>
    					<td style="text-align:center;"><a><button  type="button" onclick="openModal('<?php echo $data->user_id;?>')" ><span class="titleH5 emBlue"><?php echo number_format($data->accounts_receivable);?>원</span></button></a></td>
    					<td style="text-align:center;"><span class="titleH5 emRed"><?php echo number_format($data->cost_total - $data->accounts_receivable);?>원</span></td>
    					<td style="text-align:center;"><strong><span><?php if($data->payMode == '01'){ echo "월말결제"; }?></span></strong></td>
    					<td style="text-align:center;">
    					<?php if($data->pay_status == 'PW'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산대기";?></span></strong></button>
    					<?php }else if($data->pay_status == 'EN'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산완료";?></span></strong></button>
    					<?php }else if($data->pay_status == 'AR'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "미수";?></span></strong></button>
						<?php }?>	
    					</td>
    				</tr>
            
            <?php 
            $num++;
    	    }
    	}
    	
    	$checkMode = $wpdb->get_results("select * from bbse_commerce_order O where payMode  = '02' and pay_status = 'AR' ".$sOption."");
    	if(sizeof($checkMode) > '0'){
    	    foreach($checkMode as $j=>$data){
    	        $payInfo = unserialize($data->pay_info);
    	        if($data->pay_status=='PW') $btnColor="red";
    	        else $btnColor="black";
    	        ?>
    				<tr>
    					<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
    					<td style="text-align:center;"><?php echo $num;?></td>
    					<td style="text-align:center;"><?php echo $data->order_name;?></td>
    					<!-- <td style="text-align:center;"><?php echo $data->order_year, "/",$data->order_month , "월";?></td> -->
    					<!--     					<td style="text-align:center;"><?php echo $data->pay_year, "/",$data->pay_month, "월";?></td> -->
    					<td style="text-align:center;"><?php echo $data->input_name;?></td>
    					<td style="text-align:center;"><div><?php echo $payInfo['bank_name'],"/", $payInfo['bank_no'], "/",$payInfo['bank_owner'];?></div></td>
    					<!--   					<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td> -->
						<td style="text-align:center;"><span class="titleH5"><?php echo number_format($data->cost_total);?>원</span></td>
    					<td style="text-align:center;"><a><button  type="button" onclick="openModal('<?php echo $data->user_id;?>')" ><span class="titleH5 emBlue"><?php echo number_format($data->accounts_receivable);?>원</span></button></a></td>
    					<td style="text-align:center;"><span class="titleH5 emRed"><?php echo number_format($data->cost_total - $data->accounts_receivable);?>원</span></td>
    					<td style="text-align:center;"><strong><span><?php if($data->payMode == '02'){ echo "건별결제"; }?></span></strong></td>
    					<td style="text-align:center;">
    					<?php if($data->pay_status == 'PW'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산대기";?></span></strong></button>
    					<?php }else if($data->pay_status == 'EN'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "정산완료";?></span></strong></button>
    					<?php }else if($data->pay_status == 'AR'){?>
    					<button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><strong><span><?php echo "미수";?></span></strong></button>
						<?php }?>
    					</td>
    				</tr>
            
            <?php 
            $num++;
	        }
    	}
    	
    	
	}
	else{
	?>
				<tr>
					<td style="height:130px;text-align:center;" colspan="9">등록 된 주문정보가 존재하지 않습니다.</td>
				</tr>
	<?php 
	}
	?>
			</table>
		</div>

		<div style="margin-top:20px;">
			<ul class='title-sub-desc none-content'>
				<li>
					<select name='bulk_action2' id='bulk_action2'>
					<option value=''>일괄작업</option>
				<?php if($s_list=='TR'){?>
					<option value='restore'>이전 상태로 복원</option>
					<option value='empty-trash'>영구적으로 삭제하기</option>
				<?php }else{?>
					<?php if($s_list=='DR'){?>
					<option value='print-order'>주문서인쇄</option>
					<?php }?>
					<option value='trash'>휴지통</option>
				<?php }?>
					</select>
					<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_trash('chPayStatus',jQuery('#bulk_action2').val(),'');" value="적용"  />
				</li>

				
			</ul>
		
		</div>

		<table align="center">
		<colgroup><col width=""></colgroup>
			<tr>
				<td>
					<?php echo bbse_commerce_get_pagination($paged, $total_pages, $add_args);?>
				</td>
			</tr>
		</table>
		
		</form>
	</div>
</div>
<div id="excelDown"></div>

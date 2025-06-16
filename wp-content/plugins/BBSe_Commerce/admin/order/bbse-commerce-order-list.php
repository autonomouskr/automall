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

	function view_list(sList){
		var goUrl="";
		var per_page=jQuery("#per_page").val();
		if(sList=='all') goUrl="admin.php?page=bbse_commerce_order&per_page="+per_page;
		else goUrl="admin.php?page=bbse_commerce_order&s_list="+sList+"&per_page="+per_page;
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
				data: {tMode:tMode, tStatus:tStatus, tData:tData}, 
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
		if(jQuery("#check_all").is(":checked")) jQuery("input[name=check\\[\\]]").attr("checked",true);
		else jQuery("input[name=check\\[\\]]").attr("checked",false);
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
	
	let arr = [];
	function checkList(){
 		let check = jQuery("input[name='check\\[\\]']:checked");
 		
 		if(check.length > 1){
 			alert("거래명세서 다운로드는 1건씩 가능합니다.");
 			return;
 		}
 		
 		if(check.length < 1){
 			alert("다운로드 받을 거래명세서를 선택해주세요.");
 			return;
 		}
 		
 		jQuery(check).each(function(){
 			arr.push(jQuery(this).val());
 		});
 		
 		window.open("<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-order-transaction-excel-down.php?s_checkList="+arr);
 		//window.open("http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-popup-order-transaction-excel-down.php?s_checkList="+arr);
 		
 		location.reload();

	}
	
</script>
<?php
$sOption="";

if($s_list){
	if($s_list=='all') $sOption .=" AND O.order_status<>'TR'";
	else if($s_list == 'PW' || $s_list == 'EN') $sOption .=" AND (O.order_status='".$s_list."' or O.pay_status='".$s_list."') AND O.order_status<>'TR'";
	else $sOption .=" AND (O.order_status='".$s_list."' or O.pay_status='".$s_list."')";
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

$sql  = $wpdb->prepare("SELECT DISTINCT(O.idx) AS idx FROM bbse_commerce_order AS O, bbse_commerce_order_detail AS G WHERE O.idx<>''".$sOption." ORDER BY O.idx DESC LIMIT %d, %d", $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("SELECT count(DISTINCT(O.idx)) FROM bbse_commerce_order AS O, bbse_commerce_order_detail AS G WHERE O.idx<>''".$sOption, $prepareParm);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

$total_pages = ceil($s_total / $per_page);   // 총 페이지수


/* List Query  */
$total = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status<>'TR'"));    // 총 상품수

$total_PR = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='PR'"));    // 입금대기 수
$total_PE = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='PE'"));    // 결제완료 수
$total_DR = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='DR'"));    // 배송준비 수
$total_CA = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='CA'"));    // 취소신청 수
$total_CE = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='CE'"));    // 취소완료 수
$total_DI = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='DI'"));    // 배송중 수
$total_DE = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='DE'"));    // 배송완료 수
$total_RA = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='RA'"));    // 반품신청 수
$total_RE = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='RE'"));    // 반품완료 수
$total_OE = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='OE'"));    // 구매확정 수
$total_PW = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status <> 'TR' AND pay_status='PW'"));     // 정산대기 수
$total_EN = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status <> 'TR' AND pay_status='EN'"));      // 정산완료 수
$total_TR = count($wpdb->get_results("SELECT idx FROM bbse_commerce_order WHERE idx<>'' AND order_status='TR'"));    // 휴지통 수

/* Query String */
$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);
?>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>주문관리</h2>
		<hr>
		<ul class='title-sub-desc'>
			<li <?php echo ($s_list=='all')?"class=\"current\"":"";?>><a title='전체 상품 보기' href="javascript:view_list('all');">전체(<?php echo $total;?>)</a></li>
			<li <?php echo ($s_list=='PR')?"class=\"current\"":"";?>><a title='노출 상품 보기' href="javascript:view_list('PR');">입금대기(<?php echo $total_PR;?>)</a></li>
			<li <?php echo ($s_list=='PE')?"class=\"current\"":"";?>><a title='비노출 상품 보기' href="javascript:view_list('PE');">결제완료(<?php echo $total_PE;?>)</a></li>
			<li <?php echo ($s_list=='DR')?"class=\"current\"":"";?>><a title='노출품절 상품 보기' href="javascript:view_list('DR');">배송준비(<?php echo $total_DR;?>)</a></li>
			<li <?php echo ($s_list=='CA')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('CA');">취소신청(<?php echo $total_CA;?>)</a></li>
			<li <?php echo ($s_list=='CE')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('CE');">취소완료(<?php echo $total_CE;?>)</a></li>
			<li <?php echo ($s_list=='DI')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('DI');">배송중(<?php echo $total_DI;?>)</a></li>
			<li <?php echo ($s_list=='DE')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('DE');">배송완료(<?php echo $total_DE;?>)</a></li>
			<li <?php echo ($s_list=='RA')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('RA');">반품신청(<?php echo $total_RA;?>)</a></li>
			<li <?php echo ($s_list=='RE')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('RE');">반품완료(<?php echo $total_RE;?>)</a></li>
			<li <?php echo ($s_list=='OE')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('OE');">구매확정(<?php echo $total_OE;?>)</a></li>
			<li <?php echo ($s_list=='PW')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('PW');">정산대기(<?php echo $total_PW;?>)</a></li>
			<li <?php echo ($s_list=='EN')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('EN');">정산완료(<?php echo $total_EN;?>)</a></li>

			<li <?php echo ($s_list=='TR')?"class=\"current\"":"";?>><a title='휴지통 상품 보기' href="javascript:view_list('TR');">휴지통(<?php echo $total_TR;?>)</a>
			<?php if($total_TR>0){?>&nbsp;&nbsp;<button type="button" class="button-small-fill orange" onClick="change_trash(jQuery('#bulk_action').val(),'empty-trash','empty');" align="absmiddle"> 비우기 </button><?php }?></li>
		</ul>
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:60px;">
		<ul class='title-sub-desc none-content'>
			<li>
				<select name='bulk_action' id='bulk_action'>
					<option value=''>일괄작업</option>
					<option value='PR'>입금대기</option>
					<option value='PE'>결제완료</option>
					<option value='DR'>배송준비</option>
					<option value='CA'>취소신청</option>
					<option value='DI'>배송중</option>
					<option value='DE'>배송완료</option>
					<option value='RA'>반품신청</option>
					<option value='RE'>반품완료</option>
					<option value='OE'>구매확정</option>
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
				<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_trash(jQuery('#bulk_action').val(),jQuery('#bulk_action').val(),'');" value="적용"  />
			</li>
			<li><input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />		
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
				<?php if($s_list!='TR'){?>
				<button type="button" onclick="checkList();" class="button-bbse red" style="width:180;height:30px;"> 거래명세서 다운로드 </button>
				<button type="button" class="button-bbse blue" onClick="excel_down();" style="width:230px;height:30px;"> 엑셀파일 다운로드 / 송장등록 </button>
				<?php }?>
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
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:20px;">
		<form name="goodsFrm" id="goodsFrm">
		<input type="hidden" name="tMode" id="tMode" value="">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="2%"><col width="3%"><col width="5%"><col width="10%"><col width="5%"><col width="5%"><col width="7%"><col width="7%"><col width="8%"><col width="8%"><col width="8%"><col width="8%"></colgroup>
				<tr>
					<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
					<th>번호</th>
					<th>주문번호</th>
					<th>상품명</th>
					<th>배송비</th>
					<th>쿠폰사용여부</th>
					<th>최종 결제금액</th>
					<th>결제방식</th>
					<th>주문일자</th>
					<th>주문자 정보</th>
					<th>진행상태</th>
					<th>정산상태</th>
				</tr>
	<?php 
	if($s_total>'0'){
		foreach($result as $i=>$data) {
			$num = ($s_total-$start_pos) - $i; //번호
			$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$data->idx."'"); 

			$coupon_log	= $wpdb->get_results('SELECT * FROM bbse_commerce_coupon_log WHERE order_id = "'.$data->idx.'"');
			if($oData->order_status=='CA' || $oData->order_status=='CE' || $oData->order_status=='RA'|| $oData->order_status=='RE') $btnColor="red";
			else $btnColor="black";
			if($oData->pay_status=='EN') $paybtnColor="blue";
			else if($oData->pay_status=='PW') $paybtnColor="green";
			else if($oData->pay_status=='AR') $paybtnColor="red";

			if($oData->user_id){
				$mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$oData->user_id."'");

				if($mData->name){
					$memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_member.png\" title=\"회원\"><br>".$mData->name."<br /><span style=\"color:#00A2E8;\">(<span onClick=\"user_view('".$mData->user_id."');\" style=\"cursor:pointer;\">".$mData->user_id."</span>)</span></div>";
				}
				else $memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_notextist_member.png\" title=\"회원 정보 없음\"></div>";
			}
			elseif($oData->sns_id && $oData->sns_idx){
				$memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_social.png\" onClick=\"social_view('".$oData->sns_idx."');\" style=\"cursor:pointer;\" title=\"소셜로그인 주문\"></div>";
			}
			else $memberStr="<div style=\"line-height:20px;\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_order_nomember.png\" title=\"비회원 주문\"></div>";
	?>
			<tr>
				<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
				<td style="text-align:center;"><?php echo $num;?></td>
				<td style="text-align:center;">
					<a href="admin.php?page=bbse_commerce_order&tMode=detail&tData=<?php echo $oData->idx;?>" target="_self" title="주문 상세정보 보기"><span class="titleH5 emBlue"><?php echo $oData->order_no;?></span></a><br />
					<?php if($oData->order_device=='tablet' || $oData->order_device=='mobile'){?>
						<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-mobile.png" title="모바일 주문" />
					<?php }else{?>
						<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-desktop.png" title="데스크탑 주문" />
					<?php }?>
				</td>
				<td>
					<div class="clearfix" style="height:5px;"></div>
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

						$deliveryData=unserialize($oData->order_config);
						if($oData->delivery_total>'0'){ 
							$deveryAdvance=(!$deliveryData['delivery_charge_payment'] || $deliveryData['delivery_charge_payment']=='advance')?"<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_delivery_payment_advance.png\" title=\"배송비 선불 결제\" /><br />":"<span class=\"emRed\"><br /><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_delivery_payment_after.png\" title=\"배송비 후불 결제\" /></span><br />";
						}
						else $deveryAdvance="착불";
						
				?>
					<table class="dataNormalTbls" style="border:1px solid #DFDFDF;">
						<colgroup><col width="130px"><col width=""></colgroup>
						<tr>
							<td style="vertical-align:top;">
								<div style="width:102px;margin-left:10px;"><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $gData->goods_idx;?>" target="_blank"><img src="<?php echo $basicImg['0'];?>" class="list-goods-img"></a></div>
								<!-- <div style="background:#F0F0F0;border:1px solid #DFDFDF;width:101px;text-align:center;margin-left:10px;">￦ <?php echo number_format($gData->goods_price);?>원</div> -->
							</td>
							<td style="vertical-align:top;">
								<div class="clearfix" style="height:10px;"></div>
								<?php 
										echo "<div class=\"titleH5\"><a href=\"".esc_url( home_url( '/' ) )."?bbseGoods=".$gData->goods_idx."\" target=\"_blank\">".$gData->goods_name."</a></div><br />";
										$basicOpt=unserialize($gData->goods_option_basic);
/* 										for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
											if($basicOpt['goods_option_title'][$b]=="단일상품") echo "<div style='width:100%'>&nbsp;<div style='float:right;margin-right:10px;'>".number_format($gData->goods_price)."원 * ".$basicOpt['goods_option_count'][$b]."개</div></div>";
											else echo "<div style='width:100%'>".$basicOpt['goods_option_title'][$b]." <span class=\"textFont-11 emBlue\">(+ ".number_format($basicOpt['goods_option_overprice'][$b])."원)</span><div style='float:right;margin-right:10px;'>".number_format($gData->goods_price+$basicOpt['goods_option_overprice'][$b])."원 * ".$basicOpt['goods_option_count'][$b]."개</div></div>";
										} */

										$addOpt=unserialize($gData->goods_option_add);
										if(sizeof($addOpt['goods_add_title'])>'0') echo "<hr />";
										for($a=0;$a<sizeof($addOpt['goods_add_title']);$a++){
											echo "<div style='width:100%'>".$addOpt['goods_add_title'][$a]." <span class=\"textFont-11 emBlue\">(".number_format($addOpt['goods_add_overprice'][$a])."원)</span><div style='float:right;margin-right:10px;'>".number_format($addOpt['goods_add_overprice'][$a])."원 * ".$addOpt['goods_add_count'][$a]."개</div></div>";
										}
									?>
							</td>
						</tr>
					</table>
					<div class="clearfix" style="height:5px;"></div>
				<?php
					}
				?>
				</td>
				<td style="text-align:center;"><?php echo $deveryAdvance;?>(+) <?php echo number_format($oData->delivery_total);?>원</span></td>
				<?php if(sizeof($coupon_log) >'0' ){?>
				<td style="text-align:center;"><span style="color:red;"><strong>사용</strong></span></td>
				<?php }else{?>
				<td style="text-align:center;"><span style="color:blue;"><strong>미사용</strong></span></td>
				<?php }?>
						
				<?php
				$payType=$payHow[$oData->pay_how];

				if($oData->ezpay_how=='EPN') $ezpayType="(<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_paynow.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
				elseif($oData->ezpay_how=='EKA') $ezpayType="(<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kakaopay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
				elseif($oData->ezpay_how=='EPC') $ezpayType="(<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_payco.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
				elseif($oData->ezpay_how=='EKP') $ezpayType="(<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kpay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
				elseif($oData->ezpay_how=='NPAY') $ezpayType="(<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/icon_npay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
				else $ezpayType="";

				if($ezpayType) $payType="<div style='margin-top:3px;line-height:15px;'>".$payType."<br />".$ezpayType."</div>";
				?>
				<td style="text-align:center;"><a href="admin.php?page=bbse_commerce_order&tMode=detail&tData=<?php echo $oData->idx;?>" target="_self" title="주문 상세정보 보기"><span class="titleH5 emBlue"><?php echo number_format($oData->cost_total);?>원</span></a><br /><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_earn_red.png" align="absmiddle" title="결제에 사용한 적립금" /> <span class="emRed">(-) <?php echo number_format($oData->use_earn);?>원<br /><?php echo $payType;?></td>
				<td style="text-align:center;"><strong><span><?php if($oData->payMode == '01'){ echo "월말결제"; }?></span><span style="color: red;"><?php if($oData->payMode == '02'){echo "건별결제";}?></span></strong></td>
				<td style="text-align:center;"><?php echo date("Y.m.d H:i:s",$oData->order_date);?></td>
				<td style="text-align:center;">
					<div><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_person_send.png" align="absmiddle" />&nbsp;&nbsp;<?php echo $oData->order_name;?></div>
					<div><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_person_receive.png" align="absmiddle" />&nbsp;&nbsp;<?php echo $oData->receive_name;?></div>
					<div class="clearfix" style="height:20px;"></div>
					<div><?php echo $memberStr;?></div>
					<?php
			        	$pass_num = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order' order by idx asc");
						$pass_num = unserialize($pass_num);
						$pass_num = $pass_num['pass_num_use'];
						if($pass_num == 'on'):
			        ?>
					<div style="background: #d00;color: #fff;border-radius: 4px;"><?php echo $oData->order_pass_num; ?></div>
					<?php
						endif;
			        ?>
				</td>
				<td style="text-align:center;"><button type="button" class="button-small-fill <?php echo $btnColor;?> default-cursor"><?php echo $orderStatus[$oData->order_status];?></button></td>
				<td style="text-align:center;"><button type="button" class="button-small-fill <?php echo $paybtnColor;?> default-cursor"><?php echo $orderStatus[$oData->pay_status];?></button></td>
			</tr>
	<?php
		}
	}
	else{
	?>
				<tr>
					<td style="height:130px;text-align:center;" colspan="11">등록 된 주문정보가 존재하지 않습니다.</td>
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
					<option value='PR'>입금대기</option>
					<option value='PE'>결제완료</option>
					<option value='DR'>배송준비</option>
					<option value='CA'>취소신청</option>
					<option value='CE'>취소완료</option>
					<option value='DI'>배송중</option>
					<option value='DE'>배송완료</option>
					<option value='RA'>반품신청</option>
					<option value='RE'>반품완료</option>
					<option value='OE'>구매확정</option>
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
					<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_trash(jQuery('#bulk_action').val(),jQuery('#bulk_action2').val(),'');" value="적용"  />
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

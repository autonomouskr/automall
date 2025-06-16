<?php
/* Search Vars */
$page=$_REQUEST['page'];
$s_list=(!$_REQUEST['s_list'])?"all":$_REQUEST['s_list'];  // 전체, 노출, 비노출, 노출품절, 휴지통
$search1 = $_REQUEST['search1'];
$keyword = $_REQUEST['keyword'];
$search2 = $_REQUEST['search2'];

$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$addQuery2 = "";
$addQuery3 = "";
if($currUserID != "autopole3144"){
    $addQuery2 .= " AND manager_id = '".$currUserID."'";
    $storage = $wpdb->get_row("select * from autopole3144.tbl_storage where 1=1 ".$addQuery2." and delete_yn != 'Y'");
    
    $addQuery3 .= " AND storage_code = '".$storage->storage_code."'";
}

/* Add Query */
$addQuery = "";
if($search1) $addQuery .= " AND current_count < notice_count";
if($search2) $addQuery .= " AND storage_code ='".$search2."'";
if($keyword) $addQuery .=" AND (goods_name LIKE '%".$keyword."%')";

$sql = $wpdb->prepare("select * from autopole3144.tbl_inven where 1=1 and delete_yn != 'Y' ".$addQuery3.$addQuery." ORDER BY idx DESC LIMIT %d, %d" , $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("select count(*) from autopole3144.tbl_inven where 1=1 and delete_yn != 'Y' ".$addQuery3.$addQuery);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

$total_pages = ceil($s_total / $per_page);   // 총 페이지수

$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);

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
	
	
function checkExtend(frm, objName){
	var frm = eval('document.' + frm);
	var checkOk = 0;

	for(var i = 0; i < frm.elements.length; i++){
		if(frm.elements[i].name == objName && frm.elements[i].checked == true){
			checkOk += 1;
		}
	}	  
	return checkOk;
}

function checkAll(){
	
	if(jQuery("#check_all").is(":checked")){
		jQuery("input[name=check\\[\\]]").attr("checked",true);
	}		
	else{
		jQuery("input[name=check\\[\\]]").attr("checked",false);
	}
}

function searchSubmit() {
	jQuery("#inven_state_frm").submit();
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
	
</script>

<form id="inven_state_frm" name="inven_state_frm" method="post">
		<input type="hidden" name="strun" id="mvrun" value="" />
		<input type="hidden" name="delNo" id="delNo" value="">
		<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">
		<ul class='title-sub-desc none-content' style="float:right; margin-bottom: 10px;">
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
		
		<!-- Search content -->
		<table cellspacing="0" cellpadding="5" border="0" style="border:2px solid #4C99BA;width:100%;background-color:#f9f9f9;">
		<thead>
		<tr>
			<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">제품명</th>
			<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
				<input type="text" id="keyword" name="keyword" value="<?=$keyword?>"/>
			</td>
			<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">재고부족</th>
			<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
				<select id='search1' name='search1' style="float:left; width:120px;">
					<option value="">전체</option>
					<option value="invenStatus" <?=($search1=="invenStatus")?"selected":""?>>부족</option>
				</select>
			</td>
			<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">위치</th>
			<td scope="col" class="manage-column"  colspan="4">
				<select id='search2' name='search2' style="float:left; width:120px;">
					<option value="">전체</option>
			<?php $storage_result = $wpdb->get_results("select * from autopole3144.tbl_storage where delete_yn != 'Y' and storage_code = '".$storage->storage_code."'");
                foreach($storage_result as $i => $data){
			?>
					<option value="<?php echo $data->storage_code?>" <?=($search2==$data->storage_code)?"selected":""?>><?php echo $data->storage_name;?></option>
			<?php }?>
				</select>
			</td>
			<td scope="col" class="manage-column"  colspan="4">
				<div>
 					<button type="button" onclick="location.href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'/admin/bbse-commerce-invenlist-download.php'; ?>';" class="button-bbse red" style="float:right">엑셀파일다운로드</button>
<!-- 					<button type="button" onclick="location.href='http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-invenlist-download.php'" class="button-bbse red" style="float:right">엑셀파일다운로드</button> -->	
    				<button type="button" class="button-bbse blue" onclick="searchSubmit();" style="float:right">조회하기</button>
				</div>
			</td>
		</tr>
		</thead>
		</table>
		<!--// Search content -->
		<div class="clearfix" style="height:20px;"></div>

		<!-- 내용 start -->

		<table class="dataTbls normal-line-height collapse">
		<colgroup>
			<col width="3%">
    		<col width="3%">
    		<col width="25%">
    		<col width="10%">
    		<col width="8%">
    		<col width="8%">
    		<col width="8%">
    		<col width="8%">
    		<col width="8%">
    		<!-- <col width="10%"> -->
		</colgroup>
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
				<th>번호</th>
				<th>제품명</th>
				<th>제품코드</th>
				<th>재고부족</th>
				<th>재고수량</th>
				<th>알림설정</th>
				<th>위치</th>
				<th>담당자</th>
			</tr>
		<?php
		if(count($result) < 1){
		?>
		<tr valign="middle">
			<td colspan="10" align="center">재고 현황 목록이 없습니다.</td>
		</tr>
		<?php
		}else{
			foreach($result as $i => $data){
			    
			    //$storage = $wpdb->get_row("select * from autopole3144.tbl_storage where storage_code = '".$data->storage_code."' and delete_yn != 'Y'");
			    //$manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id ='".$storage->user_id."'");
			    $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id ='".$storage->manager_id."'");
		?>
			<tr>
				<input type="hidden" name="idx" id="idx" value="<?php echo $data->idx;?>" />
				<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $idxArr;?>"></td>
				<td style="text-align:center;"><?php echo $i+1;?></td>
				<td style="text-align:center;"><?php echo $data->goods_name;?></td>
				<td style="text-align:center;"><?php echo $data->goods_code;?></td>
				<td style="text-align:center;"><?php if($data->current_count < $data->notice_count){echo "부족";} else { echo "-";}?></td>
				<td style="text-align:center;"><span style="font-weight: bold; color:blue;"><?php echo $data->current_count;?>&nbsp;EA</span></td>
				<td style="text-align:center;"><span style="font-weight: bold; color:red;"><?php echo $data->notice_count;?>&nbsp;EA</span></td>
				<td style="text-align:center;"><?php echo $storage->storage_name;?></td>
				<td style="text-align:center;"><?php echo $manager->manager_name;?></td>
<!--  				<td style="text-align: center;"><?php echo date("Y-m-d",$data->reg_date),"/",date("Y-m-d",$data->update_date);?></td>-->
			</tr>
		<?php 
			}
		}
		?>
		</table>

		<table align="center">
		<colgroup><col width=""></colgroup>
			<tr>
				<td>
					<?php echo bbse_commerce_get_pagination($paged, $total_pages, $add_args);?>
				</td>
			</tr>
		</table>
	</form>
    <!-- 내용 end -->
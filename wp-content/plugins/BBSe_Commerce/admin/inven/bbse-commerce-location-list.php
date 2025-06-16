<?php
/* Search Vars */
$page = $_REQUEST['page'];
$search1 = $_REQUEST['search1'];
$keyword = $_REQUEST['keyword'];

$per_page = (! $_REQUEST['per_page']) ? 10 : $_REQUEST['per_page']; // 한 페이지에 표시될 목록수
$paged = (! $_REQUEST['paged']) ? 1 : intval($_REQUEST['paged']); // 현재 페이지
$start_pos = ($paged - 1) * $per_page; // 목록 시작 위치

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

/* Add Query */
$addQuery = "";
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$addQuery2 = "";
if($currUserID != "autopole3144"){
    $addQuery2 .= " AND storage_code in (select storage_code from tbl_storage where manager_id = '".$currUserID."')";
}
if($search1=="storageName"){
    $addQuery .=" AND storage_code in (select storage_code from tbl_storage where storage_name LIKE '%".like_escape($keyword)."%') ";
}else if($search1=="storageCode"){
    $addQuery .=" AND storage_code LIKE '%".like_escape($keyword)."%' ";
}else if($search1=="locationCode"){
    $addQuery .= " AND concat(rack_code,location_x,location_y) LIKE '%".like_escape($keyword)."%'";
}else{
    $addQuery .="";
}

$sql = $wpdb->prepare("select * from autopole3144.tbl_locations where 1=1 and delete_yn <> 'Y' ".$addQuery.$addQuery2." ORDER BY idx DESC LIMIT %d, %d" , $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("select count(*) from autopole3144.tbl_locations where 1=1 and delete_yn <> 'Y' ".$addQuery);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

$total_pages = ceil($s_total / $per_page);   // 총 페이지수

$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);

?>
<script language="javascript">

function checkAll(){
	
	if(jQuery("#check_all").is(":checked")){
		jQuery("input[name=check\\[\\]]").attr("checked",true);
	}		
	else{
		jQuery("input[name=check\\[\\]]").attr("checked",false);
	}
}

function deleteStorage(){

	chkarr = [];
	var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();

 	const checkboxes = document.querySelectorAll('input[name=check\\[\\]]:checked');
 	
	for(i=0;i<chked;i++){
		chkarr.push(jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val());
	}
	
	if(chkarr.length < 1){
		alert("체크된 건이 없습니다.");
		return;
	}
	
	if(confirm("선택된 " + chkarr.length + "건을 삭제하시겠습니까?")){
		goods_frm.idx.value = chkarr;
		goods_frm.strun.value = "del_proc";
		goods_frm.action = 'admin.php?page=bbse_commerce_location';
		goods_frm.submit();
    		
	}else{
		alert("삭제가 취소되었습니다.");
		return;
	}
}
	
function searchSubmit() {
	jQuery("#location_frm").submit();
}

function inven_upload_excel(){
	var tbHeight = window.innerHeight * .65;
	var tbWidth = window.innerWidth * .30;
	tb_show("제품정보-엑셀업로드", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/dbinsert/bbse-commerce-location-dbinsert-csv.php?height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	//tb_show("제품정보-엑셀업로드", "http://localhost/wp-content/plugins/BBSe_Commerce/admin/dbinsert/bbse-commerce-location-dbinsert-csv.php?height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	return false;
}

</script>

<form id="location_frm" name="goods_frm" method="post">
	<input type="hidden" name="strun" id="strun" value="" />
	<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">

	<!-- Search content -->
	<table cellspacing="0" cellpadding="5" border="0"
		style="border: 2px solid #4C99BA; width: 100%; background-color: #f9f9f9;">
		<thead>
			<tr>
				<th scope="col" width="60" class="manage-column" style="border-bottom: 1px dotted #cccccc;">검색</th>
				<td scope="col" class="manage-column" colspan="5" style="border-bottom: 1px dotted #cccccc;">
					<select id='search1' name='search1' style="float: left">
						<option value="">선택</option>
						<option value="storageName" <?=($search1=="storageName")?"selected":""?>>창고명</option>
						<option value="storageCode" <?=($search1=="storageCode")?"selected":""?>>창고코드</option>
						<option value="locationCode"<?=($search1=="locationCode")?"selected":""?>>물류창고위치</option>
					</select>
					<p class="">
						<input type="text" id="keyword" name="keyword" value="<?=$keyword?>" />
					</p></td>
				<td scope="col" class="manage-column" colspan="4">
					<div>
						<button type="button" class="button-bbse blue"
							onclick="searchSubmit();" style="float: left">조회하기</button>
						<button type="button" onclick="location.href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'/admin/bbse-commerce-location-download.php'; ?>';" class="button-bbse blue" style="float:right;">물류창고목록다운로드</button>
						<!-- <button type="button" onclick="inven_upload_excel();" class="button-bbse blue" style="float: right;">제품목록업로드</button> -->
						<button type="button" onclick="location.href='admin.php?page=bbse_commerce_location&strun=batch_proc';" class="button-bbse blue" style="float: right;">물류창고목록업로드</button>
						<button type="button" onclick="deleteStorage();"
							class="button-bbse red" style="float: right;">삭제</button>
						<button type="button"
							onclick="location.href='admin.php?page=bbse_commerce_location&mode=add';"
							style="float: right;" class="button-bbse red">추가</button>
					</div>
				</td>
			</tr>
		</thead>
	</table>
	<!--// Search content -->
	<div class="clearfix" style="height: 20px;"></div>

	<!-- 내용 start -->

	<table class="dataTbls normal-line-height collapse">
		<colgroup>
			<col width="3%">
			<col width="3%">
			<col width="10%">
			<col width="8%">
			<col width="8%">
			<col width="10%">
		</colgroup>
		<tr>
			<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
			<th>번호</th>
			<th>창고명</th>
			<th>창고코드</th>
			<th>섹션명</th>
			<th>섹션상세위치</th>
		</tr>
		<?php
  if (count($result) < 1) {
    ?>
		<tr valign="middle">
			<td style="height:130px;text-align:center;" colspan="9">물류창고 목록이 존재하지 않습니다.</td>
		</tr>
		
		<?php
} else {
    foreach ($result as $i => $data) {
        $storage = $wpdb->get_row("select * from tbl_storage where delete_yn <> 'Y' and storage_code = '".$data->storage_code."'");
        ?>
		<tr id="tr_<?php echo $data->id;?>">
        	<td style="display: none;"><input type="hidden" class="idx" name = "idx" id="idx" value="<?php echo $data->idx;?>"></td>
			<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
			<td style="text-align: center;"><?php echo $i+1;?></td>
			<td style="text-align: center;"><?php echo $storage->storage_name;?></td>        
			<td style="text-align: center;"><?php echo $storage->storage_code;?></td>
			<td style="text-align: center;"><?php echo $data->rack_code;?></td>
			<td style="text-align: center;"><?php echo $data->rack_code,$data->location_x,$data->location_y;?></td>
		</tr>
		<?php
    }
}
?>
		</table>

	<table align="center">
		<colgroup>
			<col width="">
		</colgroup>
		<tr>
			<td>
					<?php echo bbse_commerce_get_pagination($paged, $total_pages, $add_args);?>
				</td>
		</tr>
	</table>
</form>
<!-- 내용 end -->
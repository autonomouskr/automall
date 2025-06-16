<?php
/* Search Vars */
$page = $_REQUEST['page'];
$search1 = $_REQUEST['search1'];
$search2 = $_REQUEST['search2'];
$keyword = $_REQUEST['keyword'];
$keyword2 = $_REQUEST['keyword2'];

$per_page = (! $_REQUEST['per_page']) ? 10 : $_REQUEST['per_page']; // 한 페이지에 표시될 목록수
$paged = (! $_REQUEST['paged']) ? 1 : intval($_REQUEST['paged']); // 현재 페이지
$start_pos = ($paged - 1) * $per_page; // 목록 시작 위치

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$addQuery3 = "";
if($currUserID != "autopole3144"){
    $addQuery3 .= " AND manager_id = '".$currUserID."'";
}

$addQuery4 = "";
if($currUserID != "autopole3144"){
    $storage = $wpdb->get_row("select * from autopole3144.tbl_storage where 1=1 ".$addQuery3." and delete_yn != 'Y'");
    $addQuery4 .= " AND storage_code = '".$storage->storage_code."'";
}

/* Add Query */
$addQuery = "";
if($search1=="code"){
    $addQuery .= " AND (goods_code LIKE '%".like_escape($keyword)."%')";
}else if($search1=="name"){
    $addQuery .=" AND (goods_name LIKE '%".like_escape(trim($keyword))."%') ";
}else{
    $addQuery .=" and (goods_code LIKE '%".like_escape($keyword)."%' or goods_name LIKE '%".like_escape(trim($keyword))."%')";
}

$addQuery2 = "";
if($search2=="storageCode"){
    $addQuery2 .= " AND (storage_code LIKE '%".trim($keyword2)."%')";
}else if($search2=="storageName"){
    $addQuery2 .= " AND (storage_code in (select storage_code from tbl_storage where storage_name LIKE '%".like_escape($keyword2)."%' and delete_yn != 'Y'))";
}else{
    $addQuery2 .=" and (storage_code LIKE '%".trim($keyword2)."%'  or storage_code in (select storage_code from tbl_storage where storage_name LIKE '%".like_escape($keyword2)."%' and delete_yn != 'Y')) ";
}

$sql = $wpdb->prepare("select * from autopole3144.tbl_inven where 1=1 and delete_yn != 'Y' ".$addQuery.$addQuery2.$addQuery4." ORDER BY idx DESC LIMIT %d, %d" , $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("select count(*) from autopole3144.tbl_inven where 1=1 and delete_yn != 'Y' ".$addQuery.$addQuery2.$addQuery4);
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

	let chkarr = [];
	let goodsCodeArr = [];
	let storageArr = [];
	
	var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();

 	const checkboxes = document.querySelectorAll('input[name=check\\[\\]]:checked');
 	
	checkboxes.forEach(checkbox => {
        const row = checkbox.closest('tr'); // 해당 체크박스가 속한 행
        const cells = row.querySelectorAll('td');
    
        const siblingData = [];
    
        cells.forEach((cell, index) => {
          if (cell.contains(checkbox)) return; // 체크박스가 있는 셀은 제외
          siblingData.push(cell.innerText.trim());
        });
    
    	goodsCodeArr.push(siblingData[3]);
    	storageArr.push(siblingData[4]);
	});
  
 
	for(i=0;i<chked;i++){
		chkarr.push(jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val());
	}
	
	if(chkarr.length < 1){
		alert("선택된 제품이 없습니다.");
		return;
	}
	
	if(confirm("선택된 제품 " + chkarr.length + "건을 삭제하시겠습니까?")){
		goods_frm.idx.value = chkarr;
		goods_frm.strun.value = "del_proc";
		goods_frm.goodsCodeArr.value = goodsCodeArr;
		goods_frm.storageArr.value = storageArr;
		goods_frm.action = 'admin.php?page=bbse_commerce_inven';
		goods_frm.submit();
	}else{
		alert("제품 삭제가 취소되었습니다.");
		return;
	}
}
	
function searchSubmit() {
	jQuery("#goods_frm").submit();
}

function inven_upload_excel(){
	var tbHeight = window.innerHeight * .65;
	var tbWidth = window.innerWidth * .30;
	tb_show("제품정보-엑셀업로드", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/dbinsert/bbse-commerce-inven-dbinsert-csv.php?height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	//tb_show("제품정보-엑셀업로드", "http://localhost/wp-content/plugins/BBSe_Commerce/admin/dbinsert/bbse-commerce-inven-dbinsert-csv.php?height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	return false;
}

</script>

<form id="goods_frm" name="goods_frm" method="post">
	<input type="hidden" name="strun" id="strun" value="" />
	<input type="hidden" name="goodsCodeArr" id="goodsCodeArr" value="" />
	<input type="hidden" name="storageArr" id="storageArr" value="" />
	<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">

	<!-- Search content -->
	<table cellspacing="0" cellpadding="5" border="0"
		style="border: 2px solid #4C99BA; width: 100%; background-color: #f9f9f9;">
		<thead>
			<tr>
				<th scope="col" width="100" class="manage-column" style="border-bottom: 1px dotted #cccccc;">검색</th>
				<td scope="col" width="200" class="manage-column" colspan="5" style="border-bottom: 1px dotted #cccccc;">
					<select id='search1' name='search1' style="float: left">
						<option value="">선택</option>
						<option value="name" <?=($search1=="goods_name")?"selected":""?>>제품명</option>
						<option value="code"<?=($search1=="goods_code")?"selected":""?>>제품코드</option>
					</select>
					<p class="" >
						<input type="text" id="keyword" name="keyword" value="<?=$keyword?>" />
					</p>
				</td>
				<td scope="col" width="200" class="manage-column" colspan="5" style="border-bottom: 1px dotted #cccccc;">
					<select id='search2' name='search2' style="float: left">
						<option value="">선택</option>
						<option value="storageName" <?=($search2=="storage_name")?"selected":""?>>창고명</option>
						<option value="storageCode"<?=($search2=="storage_code")?"selected":""?>>창고코드</option>
					</select>
					<p class="">
						<input type="text" id="keyword2" name="keyword2" value="<?=$keyword2?>" />
					</p>
				</td>
				<td scope="col" class="manage-column" colspan="4">
					<div>
						<button type="button" class="button-bbse blue"
							onclick="searchSubmit();" style="float: left">조회하기</button>
						<button type="button" onclick="location.href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'/admin/bbse-commerce-inven-download.php'; ?>';" class="button-bbse blue" style="float:right;">제품목록다운로드</button>
						<!-- <button type="button" onclick="inven_upload_excel();" class="button-bbse blue" style="float: right;">제품목록업로드</button> -->
						<button type="button" onclick="location.href='admin.php?page=bbse_commerce_inven&strun=batch_proc';" class="button-bbse blue" style="float: right;">제품목록업로드</button> 
						<button type="button" onclick="deleteStorage();"
							class="button-bbse red" style="float: right;">제품삭제</button>
						<button type="button"
							onclick="location.href='admin.php?page=bbse_commerce_inven&mode=add';"
							style="float: right;" class="button-bbse red">제품등록</button>
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
			<col width="15%">
			<col width="8%">
			<col width="8%">
			<col width="8%">
			<col width="10%">
			<col width="8%">
			<col width="8%">
			<col width="9%">
			<col width="9%">
		</colgroup>
		<tr>
			<th><input type="checkbox" name="check_all" id="check_all"
				onClick="checkAll();"></th>
			<th>번호</th>
			<th>제품명</th>
			<th>제품코드</th>
			<th>창고코드</th>
			<th>위치</th>
			<th>상세위치</th>
			<th>주소</th>
			<th>담당자</th>
			<th>등록일</th>
			<th>수정일</th>
		</tr>
		<?php
  if (count($result) < 1) {
    ?>
		<tr valign="middle">
			<td style="height:130px;text-align:center;" colspan="10">제품 목록이 존재하지 않습니다.</td>
		</tr>
		
		<?php
} else {
    foreach ($result as $i => $data) {
        $storage2 = $wpdb->get_row("select * from tbl_storage where delete_yn <> 'Y' and storage_code = '" . $data->storage_code . "'");
        $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id = '".$storage2->manager_id."'");
        
        ?>
		<tr id="tr_<?php echo $data->idx;?>">
        	<td style="display: none;"><input type="hidden" class="idx" name = "idx" id="idx" value="<?php echo $data->idx;?>"></td>
			<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
			<td style="text-align: center;"><?php echo $i+1;?></td>
			<td style="text-align: center;"><a style="text-decoration: underline;" ondblclick="location.href='admin.php?page=bbse_commerce_inven&mode=edit&goods_code=<?php echo $data->goods_code?>&storage_code=<?php echo $data->storage_code?>'"><?php echo $data->goods_name;?></a></td>
			<td style="text-align: center;"><?php echo $data->goods_code;?></td>
			<td style="text-align: center;"><?php echo $storage2->storage_code;?></td>
			<td style="text-align: center;"><?php echo $storage2->storage_name;?></td>
			<td style="text-align: center;"><?php echo $data->rack_code;?></td>        
			<td style="text-align: center;"><?php echo $storage2->addr1,$storage->addr2;?></td>
			<td style="text-align: center;"><?php echo $manager->manager_name;?></td>
			<td style="text-align: center;"><?php echo $data->reg_date; ?></td>
			<td style="text-align: center;"><?php echo $data->update_date;?></td>
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


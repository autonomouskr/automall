<?php
/* Search Vars */
$page=$_REQUEST['page'];
$search1 = $_REQUEST['search1'];
$keyword = $_REQUEST['keyword'];
$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$per_page = (! $_REQUEST['per_page']) ? 10 : $_REQUEST['per_page']; // 한 페이지에 표시될 목록수
$paged = (! $_REQUEST['paged']) ? 1 : intval($_REQUEST['paged']); // 현재 페이지
$start_pos = ($paged - 1) * $per_page; // 목록 시작 위치

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$addQuery2 = "";
if($currUserID != "autopole3144"){
    $addQuery2 .= " AND manager_id = '".$currUserID."'";
}

/* Add Query */
$addQuery = "";
if($search1=="code"){
    $addQuery .= " AND (storage_code LIKE '%".like_escape(trim($keyword))."%')";
}else if($search1=="name"){
    $addQuery .=" AND (storage_name LIKE '%".like_escape($keyword)."%') ";
}else{
    $addQuery .="AND (storage_name LIKE '%".like_escape($keyword)."%' or storage_code LIKE '%".like_escape($keyword)."%') ";
}

$sql = $wpdb->prepare("select * from autopole3144.tbl_storage where delete_yn <> 'Y' ".$addQuery2.$addQuery." ORDER BY idx DESC LIMIT %d, %d" , $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("select count(*) from autopole3144.tbl_storage where delete_yn <> 'Y' ".$addQuery);
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

function deleteStorage(){

	chkarr = [];
	
	var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
	
	for(i=0;i<chked;i++){
		chkarr.push(jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val());
	}
	
	if(chkarr.length < 1){
		alert("선택된 창고가 없습니다.");
		return;
	}
	
	if(confirm("선택된 창고 " + chkarr.length + "건을 삭제하시겠습니까?")){
	
		frm.idx.value = chkarr;
		frm.strun.value = "del_proc";
		frm.action = 'admin.php?page=bbse_commerce_storage';
		frm.submit();
    		
	}else{
		alert("창고 삭제가 취소되었습니다.");
		return;
	}
}
	
function searchSubmit() {
	jQuery("#frm").submit();
}

function storage_upload_excel(){
	var tbHeight = window.innerHeight * .65;
	var tbWidth = window.innerWidth * .30;
	tb_show("창고정보-엑셀업로드", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-storages-upload-insertdb.php?height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	//tb_show("창고정보-엑셀업로드", "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-storage-upload-insertdb.php?height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	return false;
}


</script>

	<form id="frm" name="frm" method="post">
		<input type="hidden" name="strun" id="mvrun" value="" />
		<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">
		
		<!-- Search content -->
		<table cellspacing="0" cellpadding="5" border="0" style="border:2px solid #4C99BA;width:100%;background-color:#f9f9f9;">
		<thead>
		<tr>
			<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">창고검색</th>
			<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
				<select id='search1' name='search1' style="float:left">
					<option value="">선택</option>
					<option value="name" <?=($search1=="storage_name")?"selected":""?>>창고명</option>
					<option value="code" <?=($search1=="storage_code")?"selected":""?>>창고코드</option>
				</select>
				<p class="">
					<input type="text" id="keyword" name="keyword" value="<?=$keyword?>"/>
				</p>
			</td>
			<td scope="col" class="manage-column"  colspan="4">
				<div>
    				<button type="button" class="button-bbse blue" onclick="searchSubmit();" style="float:left">조회하기</button>
					<button type="button" onclick="location.href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'/admin/bbse-commerce-storage-download.php'; ?>';" class="button-bbse blue" style="float:right;">창고DB다운로드</button>
    				<button type="button" onclick="location.href='admin.php?page=bbse_commerce_storage&strun=batch_proc';" class="button-bbse blue" style="float: right;">창고목록업로드</button>
    				<button type="button" onclick="deleteStorage();" class="button-bbse red" style="float:right;" >창고삭제</button>
    				<button type="button" onclick="location.href='admin.php?page=bbse_commerce_storage&mode=add';" style="float:right;" class="button-bbse red">창고등록</button>
				</div>
			</td>
		</tr>
		</thead>
		</table>
		<!--// Search content -->
		<div class="clearfix" style="height:20px;"></div>

		<!-- 내용 start -->

		<table class="dataTbls normal-line-height collapse">
		<colgroup><col width="5%"><col width="5%"><col width="15%"><col width="10%"><col width="28%"><col width="8%"><col width="10%"><col width="10%"><col width="10%"></colgroup>
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
				<th>번호</th>
				<th>창고명</th>
				<th>창고코드</th>
				<th>주소</th>
				<th>담당자</th>
				<th>연락처</th>
				<th>등록일</th>
				<th>수정일</th>
			</tr>
		<?php
		if(count($result) < 1){
		?>
		<tr valign="middle">
			<td colspan="10" align="center">창고 목록이 존재하지 않습니다.</td>
		</tr>
		<?php
		}else{
			foreach($result as $i => $data){
			    $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id = '".$data->manager_id."'");
		?>
			<tr>
				<td style="display: none;"><input type="hidden" class="idx" id="idx" value="<?php echo $data->idx;?>"></td>
				<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo  $data->idx;?>"></td>
				<td style="text-align:center;"><?php echo $i+1;?></td>
				<td style="text-align:center;"><a style="text-decoration: underline;" ondblclick="location.href='admin.php?page=bbse_commerce_storage&mode=edit&storage_code=<?php echo $data->storage_code?>'"><?php echo $data->storage_name;?></a></td>
				<td style="text-align:center;"><?php echo $data->storage_code;?></td>
				<td style="text-align:center;"><?php echo $data->addr1,$data->addr2;?></td>
				<td style="text-align:center;"><?php echo $manager->manager_name;?></td>
				<td style="text-align:center;"><?php echo $manager->hp;?></td>
				<td style="text-align:center;"><?php echo $data->reg_date;?></td>
				<td style="text-align:center;"><?php echo $data->update_date;?></td>
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

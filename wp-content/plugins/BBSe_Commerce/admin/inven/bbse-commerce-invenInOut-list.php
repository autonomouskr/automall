<?php
/* Search Vars */
$page=$_REQUEST['page'];
$search1 = $_REQUEST['search1'];
$search2 = $_REQUEST['search2'];
$search3 = $_REQUEST['$search3'];
$manager_name = $_REQUEST['manager_name'];
$goods_name = $_REQUEST['goods_name'];
$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치
$cType = ($_REQUEST['cType'])?$_REQUEST['cType']:"list";

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$addQuery3 = "";
if($currUserID != "autopole3144"){
    $addQuery3 .= " AND manager_id = '".$currUserID."'";
}

/* Add Query */
$addQuery = "";
$addQuery2 = "";
if($search1) $addQuery .= " AND status ='".$search1."'";
if($search2) $addQuery .= " AND storage_code ='".$search2."'";
if($manager_name) $addQuery .= " AND manager_id = (select user_id from bbse_commerce_membership where manager_name LIKE '%".$manager_name."%')";
if($goods_name) $addQuery .=" AND goods_code in (select goods_code from tbl_inven bcg  where goods_name LIKE '%".$goods_name."%')";
if($search3 == "deleteY"){
    $addQuery2 .= "AND delete_yn = 'Y' ";
}else if($search3 == "deleteN"){
    $addQuery2 .= "AND delete_yn != 'Y' ";
}else{
    $addQuery2 .= "";
}

$sql = "select * from autopole3144.tbl_inout where 1=1 ".$addQuery3.$addQuery2.$addQuery." and status != 'DE'";

$result = $wpdb->get_results($sql)
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

function checkAll(){
	
	if(jQuery("#check_all").is(":checked")){
		jQuery("input[name=check\\[\\]]").attr("checked",true);
	}		
	else{
		jQuery("input[name=check\\[\\]]").attr("checked",false);
	}
}
	
function searchSubmit() {
	jQuery("#inoutFrm").submit();
}
	

</script>

<form name="inoutFrm" id="inoutFrm" method="post">
<!-- Search content -->
<table cellspacing="0" cellpadding="5" border="0" style="border:2px solid #4C99BA;width:100%;background-color:#f9f9f9;">
<thead>
<tr>
	<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">제품명</th>
	<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
		<input type="text" id="goods_name" name="goods_name" value="<?=$goods_name?>"/>
	</td>
	<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">구분</th>
	<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
		<select id='search1' name='search1' style="float:left; width:120px;">
			<option value="">전체</option>
			<option value="IN" <?=($search1=="IN")?"selected":""?>>입고</option>
			<option value="OT" <?=($search1=="OT")?"selected":""?>>출고</option>
			<option value="IV" <?=($search1=="IV")?"selected":""?>>재고현황</option>
		</select>
	</td>
	<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">위치</th>
	<td scope="col" class="manage-column"  colspan="4">
		<select id='search2' name='search2' style="float:left; width:120px;">
			<option value="">전체</option>
	<?php $storage_result = $wpdb->get_results("select * from autopole3144.tbl_storage where delete_yn != 'Y'");
        foreach($storage_result as $i => $data){
	?>
			<option value="<?php echo $data->storage_code?>" <?=($search2==$data->storage_code)?"selected":""?>><?php echo $data->storage_name;?></option>
	<?php }?>
		</select>
	</td>
	<!-- <th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">처리자</th>
	<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
		<input type="text" id="manager_name" name="manager_name" value="<?=$manager_name?>"/>
	</td>
	 -->
	<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">제품삭제여부</th>
	<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
		<select id='$search3' name='$search3' style="float:left; width:120px;">
			<option value="">전체</option>
			<option value="deleteY" <?=($search3=="deleteY")?"selected":""?>>제품삭제</option>
			<option value="deleteN" <?=($search3=="deleteN")?"selected":""?>>제품미삭제</option>
		</select>
	</td>
	<td scope="col" class="manage-column"  colspan="4">
		<div>
			<button type="button" onclick="location.href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'/admin/bbse-commerce-inoutlist-download.php'; ?>';" style="float: right;" class="button-bbse red">엑셀파일다운로드</button>
			<button type="button" class="button-bbse blue" onclick="searchSubmit();" style="float:right">조회하기</button>
		</div>
	</td>
</tr>
</thead>
</table>
<!--// Search content -->
<!--<div class="clearfix" style="height:20px;"></div>
<!--	<ul class='title-sub-desc none-content' style="float:right;">
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
<!-- <br> -->

<br>
	<input type="hidden" name="tMode" id="tMode" value="">
	<div style="width:100%;">
		<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="3%"><col width="3%"><col width="5%"><col width="25%"><col width="15%"><col width="8%"><col width="8%"><col width="8%"><col width="10%"><col width="10%"></colgroup>
			<tr>
				<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
				<th>번호</th>
				<th>구분</th>
				<th>제품명</th>
				<th>제품코드</th>
				<th>수량</th>
				<th>위치</th>
				<th>상세위치</th>
				<th>처리자</th>
				<th>처리일시</th>
			</tr>
	<?php 
	
	if(count($result)>'0'){
	    $num = 1;
	    foreach ($result as $i => $data) {
	        
	        $storage = $wpdb->get_row("select storage_name from autopole3144.tbl_storage where storage_code = '".$data->storage_code."'");
	        $goods = $wpdb->get_row("select * from autopole3144.tbl_inven where goods_code = '".$data->goods_code."'");
	        $location = $wpdb->get_row("select * from autopole3144.tbl_inven where goods_code = '".$data->goods_code."' and storage_code = '".$data->storage_code."'");
	        $manager = $wpdb->get_row("select * from autopole3144.bbse_commerce_membership where user_id = '".$data->manager_id."'");
            ?>
				<tr>
					<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $idxArr;?>"></td>
					<td style="text-align:center;"><?php echo $num;?></td>
					<?php if($data->status == 'IN'){
					    $status_name = "입고";
					    $textColor = "blue";
					}else if($data->status == 'OT'){
					    $status_name = "출고";
					    $textColor = "red";
					}else if($data->status == 'IV'){
					    $status_name = "재고현황";
					}
					?>
					<td style="text-align:center; color: <?php echo $textColor;?>"><span style="font-weight: bold;"><?php echo $status_name;?></span></td>
					<td style="text-align:center;"><?php echo $goods->goods_name;?></td>
					<td style="text-align:center;"><?php echo $data->goods_code;?></td>
					<td style="text-align:center;"><span style="font-weight: bold;"><?php echo $data->quantity;?>&nbsp;EA</span></td>
					<td style="text-align:center;"><?php echo $storage->storage_name;?></td>
					<td style="text-align:center;"><?php echo $location->rack_code;?></td>
					<td style="text-align:center;"><?php echo $manager->manager_name;?></td>
					<td style="text-align:center;"><?php echo $data->processing_date;?></td>
				</tr>
            <?php 
                $num++;
    	}
	}else{
	?>
				<tr>
					<td style="height:130px;text-align:center;" colspan="10">처리내역이 존재하지 않습니다.</td>
				</tr>
	<?php 
	}
	?>
			</table>
		</div>
	</form>

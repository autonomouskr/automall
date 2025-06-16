<?php
/* Search Vars */
$page=$_REQUEST['page'];
$search1 = $_REQUEST['search1'];
$keyword = $_REQUEST['keyword'];
$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

/* Add Query */
$addQuery = "";
if($search1=="code"){
    $addQuery .= " AND (goods_code LIKE '%".trim($keyword)."%')";
}

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$storage = $wpdb->get_row("select * from autopole3144.tbl_storage where manager_id = '".$currUserID."' and delete_yn != 'Y'");

//업체에서 태그발행기 사용시 조건절에 창고코드 넣어주기
//$result = $wpdb->get_results("select * from autopole3144.tbl_rfid_serial where 1=1 and delete_yn != 'Y' and storage_code ='".$storage->storage_code."' ".$addQuery." order by date desc, goods_code desc ");
//$s_total_sql  = $wpdb->prepare("select count(*) from autopole3144.tbl_rfid_serial where 1=1 delete_yn != 'Y' and storage_code ='".$storage->storage_code."' ".$addQuery);
$result = $wpdb->get_results("select * from autopole3144.tbl_rfid_serial where 1=1 and delete_yn != 'Y' ".$addQuery." order by date desc, goods_code desc ");
$s_total_sql  = $wpdb->prepare("select count(*) from autopole3144.tbl_rfid_serial where 1=1 delete_yn != 'Y' ".$addQuery);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

$total_pages = ceil($s_total / $per_page);   // 총 페이지수

$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);

$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

$total_pages = ceil($s_total / $per_page);   // 총 페이지수

$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);

?>
<script language="javascript">

jQuery(function(){
	//jQuery('#edit').show();
	//jQuery('#save').hide();
});

const chkarr = [];

function checkAll(){
	
	if(jQuery("#check_all").is(":checked")){
		jQuery("input[name=check\\[\\]]").attr("checked",true);
	}		
	else{
		jQuery("input[name=check\\[\\]]").attr("checked",false);
	}
}

function deleteSerial(){

	if(!confirm('선택한 제품코드의 일련번호를 삭제 하시겠습니까?')){
		return;
	}
	
	let quantity = [];
	let idxArr = [];
	
	for(let i=0;i<chkarr.length;i++){
		
		let idx = "#idx_" + chkarr[i];
		let value3 = jQuery(idx).val();
		
		let quanty = "#quantity_" + chkarr[i];
		let value4 = jQuery(quanty).val();
		
		quantity.push(value4);
		idxArr.push(value3);
	}
	
	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-serial.exec.php',
		//url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-serial.exec.php',
		data: {quantity:quantity, idxArr:idxArr}, 
		success: function(data){
			var result = data.split("|||"); 
			if(result['0'] == "success"){
				alert(result['1']+ "건이 삭제되었습니다.   ");
				location.reload();
				
			}else{
				alert("서버와의 통신이 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
	
}

function changeCheckbox(req, i, code){

	if(!req.checked){
		let id = "#goods_code_" + i;
		jQuery(id).prop("disabled", true);
		chkarr.pop(i);
        
	}else{
		let id = "#goods_code_" + i;
		jQuery(id).prop("disabled", false);
		chkarr.push(i); 
	}
}

function searchSubmit() {
	jQuery("#frm").submit();
}

</script>

<div class="borderBox">
	* 일련번호관리는 태그 발행기에서 요청한 일련번호를 관리하는 화면입니다.<br />
	* 태그 발행기에서 요청한 일련번호가 잘못 요청한 일련번호인 경우 일련번호 회수처리를 합니다. <br />
	&nbsp;&nbsp;1. 태그발행기에서 잘못 요청한 제품코드와 요청수량, 발행일자를 확인 후 체크박스 체크를 합니다. <br />
	&nbsp;&nbsp;2. 삭제 버튼을 클릭하고 삭제를 요청합니다.<br />
	&nbsp;&nbsp;3. 발행된 태그 일련번호 목록에서 삭제됩니다.<br />
</div>


<form id="frm" name="frm" method="post">
	<input type="hidden" name="strun" id="mvrun" value="" /> 
	<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">

	<!-- Search content -->
	<table cellspacing="0" cellpadding="5" border="0"
		style="border: 2px solid #4C99BA; width: 100%; background-color: #f9f9f9;">
		<thead>
			<tr>
				<th scope="col" width="100" class="manage-column" style="border-bottom: 1px dotted #cccccc;">일련번호조회</th>
				<td scope="col" class="manage-column" colspan="5" style="border-bottom: 1px dotted #cccccc;">
					<select id='search1' name='search1' style="float: left">
						<option value="">선택</option>
						<option value="code"<?=($search1=="goods_code")?"selected":""?>>제품코드</option>
					</select>
					<p class="">
						<input type="text" id="keyword" name="keyword" value="<?=$keyword?>" />
    					<button type="button" class="button-bbse blue" onclick="searchSubmit();">조회하기</button>
					</p>
				</td>
				<td scope="col" class="manage-column" colspan="4">
					<button type="button" class="button-bbse red" onclick="deleteSerial();" style="float: right">삭제</button>
				</td>
			</tr>
		</thead>
	</table>
	<!--// Search content -->
	<div class="clearfix" style="height: 20px;"></div>

	<!-- 내용 start -->

	<table class="dataTbls normal-line-height collapse">
		<colgroup>
			<col width="5%">
			<col width="10%">
			<col width="10%">
			<col width="25%">
			<col width="10%">
			<col width="15%">
			<col width="10%">
		</colgroup>
		<tr>
			<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
			<th>발행일자</th>
			<th>제품코드</th>
			<th>제품명</th>
			<th>요청수량</th>
			<th>마지막일련번호</th>
			<th>사용여부</th>
		</tr>
    	<?php
        if (count($result) < 1) {
        ?>
        		<tr valign="middle">
        			<td style="height:130px;text-align:center;" colspan="7">목록이 존재하지 않습니다.</td>
        		</tr>
        		
		<?php
        } else {
            foreach ($result as $i=>$data){
                $goods = $wpdb->get_row("select * from tbl_inven where goods_code = '".$data->goods_code."' and delete_yn != 'Y'");
        ?>
        		<tr>
        			<td style="text-align: center;" class="item">
        				<input type="hidden" name="idx[]" id="idx_<?php echo $i;?>" value="<?php echo $data->id;?>" disabled="disabled" />
        				<input type="checkbox" name="check[]" id="check[]" class="check" onchange="changeCheckbox(this,'<?php echo $i;?>','<?php echo $goods->goods_code;?>');">
					</td>
        			<td style="text-align: center;"><input type="text" id = "publishDate_<?php echo $i;?>" value="<?php echo $data->date;?>" disabled="disabled" /></td>
        			<td style="text-align: center;"><input type="text" id = "goods_code_<?php echo $i;?>" value="<?php echo $goods->goods_code;?>" disabled="disabled" /></td>
        			<td style="text-align: center;"><?php echo $goods->goods_name;?></td>
        			<td style="text-align: center;"><input type="text" id = "quantity_<?php echo $i;?>" value="<?php echo $data->quantity;?>" disabled="disabled" /></td>
        			<td style="text-align: center;"><?php echo $data->serial_number;?></td>
        			<?php if($data->delete_yn != 'Y'){?>
        			<td style="text-align: center;"><span style="font-weight: 300;"><?php echo "사용완료";?></span></td>
        			<?php }else{?>
        			<td style="text-align: center;"><span style="color:blue; font-weight: 900;"><?php echo "사용가능";?></span></td>
        			<?php }?>
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
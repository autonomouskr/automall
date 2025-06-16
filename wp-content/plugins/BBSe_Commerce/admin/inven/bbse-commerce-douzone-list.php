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
    $addQuery .= " AND (goods_code LIKE '%".like_escape($keyword)."%')";
}else if($search1=="name"){
    $addQuery .=" AND (goods_name LIKE '%".like_escape(trim($keyword))."%') ";
}else{
    $addQuery .="AND (goods_name LIKE '%".like_escape(trim($keyword))."%' or goods_code LIKE '%".like_escape($keyword)."%') ";
}

/*
$sql = $wpdb->prepare("select * from (
select goods_code as code , goods_name as name, idx as idx  from bbse_commerce_goods 
 where goods_display != 'trash'
UNION
select goods_code as code, goods_name as name , idx as idx from tbl_inven where delete_yn != 'Y'
UNION 
select goods_option_item_unique_code as code, goods_option_title as name, goods_idx as idx from bbse_commerce_goods_option 
 where goods_idx in (select idx  from bbse_commerce_goods where goods_display != 'trash')
) b where 1=1 ".$addQuery." ORDER BY b.code DESC LIMIT %d, %d", $prepareParm);

$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("select count(1) from (
select goods_code as code , goods_name as name, idx as idx  from bbse_commerce_goods 
 where goods_display != 'trash'
UNION 
select goods_option_item_unique_code as code, goods_option_title as name, goods_idx as idx from bbse_commerce_goods_option 
 where goods_idx in (select idx  from bbse_commerce_goods where goods_display != 'trash')) b
where 1=1 ".$addQuery );

*/

$s_total_sql = $wpdb->prepare("select count(1) from tbl_inven where delete_yn != 'Y' ".$addQuery." ORDER BY goods_code DESC LIMIT %d, %d", $prepareParm);

$sql = $wpdb->prepare("select  distinct goods_code as goods_code, goods_name as goods_name, goods_option_title as goods_option_title  from autopole3144.tbl_inven where delete_yn != 'Y' ".$addQuery." ORDER BY goods_code DESC LIMIT %d, %d", $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("select count(*) from autopole3144.tbl_inven where 1=1 and delete_yn <> 'Y' ".$addQuery);
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


function saveData(){

	if(!confirm('선택한 정보를 변경 하시겠습니까?')){
		return;
	}
	
	let dzArr = [];
	
	for(let i=0;i<chkarr.length;i++){
		let id = "#douzone_code_" + chkarr[i];
		let value = jQuery(id).val();
		
		dzArr.push(value); 
	}
	
	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-douzone.exec.php',
		//url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-douzone.exec.php',
		data: {tIdx:chkarr, dzArr:dzArr}, 
		success: function(data){
			var result = data.split("|||"); 
			if(result['0'] == "success"){
				alert(result['1']+ "건이 저장되었습니다.   ");
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

function editDouzoneCode(data) {
    const rows = document.querySelectorAll('.item');
    rows.forEach(row => {
        let checkbox = row.querySelector("input[name='check[]']");
        const textInput = row.querySelector("input[name='idx[]']");
        
        if (checkbox.checked) {
          chkarr.push(textInput.value);
        }
    });
	
	if(chkarr.length < 1){
		alert("선택된 체크박스가 없습니다.");
		return;
	}
	
	for(let i=0;i<chkarr.length;i++){
		let id = "#douzone_code_" + chkarr[i];
		jQuery(id).prop("disabled", false); // disabled 속성 해제
	}
	
	//jQuery('#edit').hide();
	//jQuery('#save').show();
}

function changeCheckbox(req, code){

	if(!req.checked){
		let id = "#douzone_code_" + code;
		jQuery(id).prop("disabled", true);
		chkarr.pop(code);
        
	}else{
		let id = "#douzone_code_" + code;
		jQuery(id).prop("disabled", false);
		chkarr.push(code); 
	}
}

function searchSubmit() {
	jQuery("#goods_frm").submit();
}

</script>

<div class="borderBox">
	* 1. 더존코드 수정을 하시려면 체크박스를 클릭해주십시오.<br />
	&nbsp;&nbsp;2. 더존코드 입력박스가 활성화되면 더존코드번호를 입력 후 저장 버튼을 클릭해주십시오.<br />
	* 체크박스가 체크된 상태의 목록은 저장버튼 클릭 시 모두 업데이트 됩니다.
</div>

<form id="goods_frm" name="goods_frm" method="post">
	<input type="hidden" name="strun" id="mvrun" value="" /> 
	<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">

	<!-- Search content -->
	<table cellspacing="0" cellpadding="5" border="0"
		style="border: 2px solid #4C99BA; width: 100%; background-color: #f9f9f9;">
		<thead>
			<tr>
				<th scope="col" width="100" class="manage-column" style="border-bottom: 1px dotted #cccccc;">제품검색</th>
				<td scope="col" class="manage-column" colspan="5" style="border-bottom: 1px dotted #cccccc;">
					<select id='search1' name='search1' style="float: left">
						<option value="">선택</option>
						<option value="name" <?=($search1=="goods_name")?"selected":""?>>제품명</option>
						<option value="code"<?=($search1=="goods_code")?"selected":""?>>제품코드</option>
					</select>
					<p class="">
						<input type="text" id="keyword" name="keyword" value="<?=$keyword?>" />
    					<button type="button" class="button-bbse blue" onclick="searchSubmit();">조회하기</button>
					</p>
				</td>
				<td scope="col" class="manage-column" colspan="4">
<!-- 					<button type="button" class="button-bbse blue" onclick="editDouzoneCode(this);" id="edit" style="float: right">편집</button> -->
					<button type="button" class="button-bbse red" onclick="saveData();" id="save" style="float: right">저장</button>
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
			<col width="25%">
			<col width="20%">
			<col width="15%">
			<col width="15%">
		</colgroup>
		<tr>
			<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
			<th>제품명</th>
			<th>옵션</th>
			<th>상품코드</th>
			<th>더존코드</th>
		</tr>
    	<?php
        if (count($result) < 1) {
        ?>
        		<tr valign="middle">
        			<td style="height:130px;text-align:center;" colspan="5">목록이 존재하지 않습니다.</td>
        		</tr>
        		
		<?php
        } else {
            foreach ($result as $i=>$data){
                $dzInfo = $wpdb->get_row("select * from tbl_douzone_code where goods_code ='".$data->goods_code."'");        
        ?>
        		<tr>
        			<td style="text-align: center;" class="item">
        				<input type="hidden" name="idx[]" id="idx" value="<?php echo $data->goods_code;?>"  />
        				<input type="checkbox" name="check[]" id="check[]" class="check" onchange="changeCheckbox(this,'<?php echo $data->goods_code;?>');">
					</td>
        			<td style="text-align: center;"><?php echo $data->goods_name;?></td>
        			<td style="text-align: center;"><?php echo $data->goods_option_title;?></td>
        			<td style="text-align: center;"><?php echo $data->goods_code;?></td>
        			<?php if(sizeof($dzInfo) > 0){?>
        				<td style="text-align: center;"><input type="text" id = "douzone_code_<?php echo $data->goods_code;?>" value= "<?php echo $dzInfo->goods_douzone_code;?>" disabled="disabled" /></td>
        			<?php }else{ ?>
        				<td style="text-align: center;"><input type="text" id = "douzone_code_<?php echo $data->goods_code;?>" value= "" disabled="disabled" /></td>
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

<?php 
    $config = $wpdb->get_row("select * from `bbse_commerce_membership_config`");
    $goods_code = $_REQUEST['goods_code'];
    $storage_code = $_REQUEST['storage_code'];
?>

<style>
    .check-container::before{
        content: "✔";
        color:red;
        font-size: 20px;
        font-weight:bold;
        margin-right: 8px;
    }
</style>

<script language="javascript">

	function checkGoodsCode(mode){
		
		let goodsCode = jQuery("#goods_code").val();
		
		let data = {};
		if(goodsCode == "") {
			alert("상품코드를 입력해주세요.");
			return;
		}
		
		if(goodsCode.length < 5){
			alert("상품코드는 5자리를 코드를 입력해주세요.");
			return;
		}
		
		data = {"goodsCode":goodsCode, "mode": mode}
	   	jQuery.ajax({
			type: 'post'
			, async: false
			, url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-goodsCode-check.php'
			//, url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-goodsCode-check.php'
			, data: data
    		, success: function(data){
    			var response = data.split("|||");
    			if(jQuery.trim(response[0]) == "ok"){
    				alert("사용가능한 상품코드 입니다.");
    				jQuery("#check_flag").val(response[0].trim());
    			}else if(jQuery.trim(response[0]) == "exist"){
    				alert("이미 등록된 상품코드 입니다.");
    				jQuery("#goods_code").select();
    			}else{
    				alert('서버와의 통신이 실패했습니다.');
    			}
    		}
    		, error: function(data, status, err){
    			alert('서버와의 통신이 실패했습니다.');
    		}
		});
	}
	
	function search_goods(){

		var popupTitle="제품조회";
		var tbHeight = window.innerHeight * .75;
		var tbWidth = window.innerWidth * .40;
		tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-inven-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-inven-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		return false;
		
	}
	
	function search_storage(){
	
		
		var popupTitle="창고조회";
		var tbHeight = window.innerHeight * .70;
		var tbWidth = window.innerWidth * .40;
		tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-storage-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-storage-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		return false;
			
	}
	
	function search_location(){
	
		var storageCode = jQuery("#storage_code").val();
		var popupTitle="물류창고위치조회";
		var tbHeight = window.innerHeight * .70;
		var tbWidth = window.innerWidth * .40;
		tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-location-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;p_storageCode="+storageCode+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-location-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;p_storageCode="+storageCode+"&#38;TB_iframe=true");
		return false;		
	}
	
    function save_inven(action_url){
    
    	var frm = document.inven_frm;
    	<?php if($_REQUEST['mode']=="add"){?>
    	
        	if(frm.goods_name.value == "") {
        		alert("제품명 입력해주세요.");
        		frm.goods_name.focus();
        		return false; 
        	}
        	
			/*if(frm.goods_code.value == "") {
        		alert("제품코드를 입력해주세요.");
        		frm.goods_code.focus();
        		return false; 
        	}
        	*/
        	
        	/*if(frm.douzone_code.value == "") {
        		alert("더존코드를 입력해주세요.");
        		frm.douzone_code.focus();
        		return false; 
        	}
        	*/
        	
        	if(frm.addr1.value == "") {
        		alert("주소를 입력해주세요.");
        		frm.addr1.focus();
        		return false; 
        	}
        	
        	if(frm.addr2.value == "") {
        		alert("주소를 입력해주세요.");
        		frm.addr1.focus();
        		return false; 
        	}
        	
        	if(frm.notice_count.value == "") {
        		alert("알람 수량을 입력해주세요.");
        		frm.alarm_qty.focus();
        		return false; 
        	}
        	
/*         	if(frm.manager_id.value == "") {
        		alert("담당자 이름 입력해주세요.");
        		frm.manager_name.focus();
        		return false; 
        	} */
        	
    	<?php }?>
    	
    	<?php if($_REQUEST['mode']=="edit"){?>
        	if(confirm("정보를 수정하시겠습니까?")){
        		frm.action = action_url;
        		frm.submit();
        	}
    	<?php }else{?>
    		frm.action = action_url;
    		frm.submit();
    	<?php }?>
    	
    }

</script>
	
<?php

$result = "";
if($_REQUEST['mode']=="add") {
	$mode_tit = "제품추가";
	$strun = "add_proc";
	$button_name = "등록";
}else{
	$mode_tit = "제품정보";
	$strun = "edit_proc";
	$button_name = "수정";
	$result = $wpdb->get_row("select * from autopole3144.tbl_inven where goods_code = '".$goods_code."' and storage_code = '".$storage_code."' and delete_yn != 'Y' ");
	$storage = $wpdb->get_row("select * from autopole3144.tbl_storage where storage_code = '".$result->storage_code."'");
}

$USER_ACTION_URL = "admin.php?page=bbse_commerce_inven&mode=".$_REQUEST['mode'];

?>
	<div class="wrap">
		<div id="popup-register-inven">
			<form method="post" id="inven_frm" name="inven_frm" enctype="multipart/form-data">
			<div class="titleH5" style="margin:20px 0px 10px 0; "><?php echo $mode_tit?>
				<button style="float:right; margin-bottom: 5px" type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="location.href='admin.php?page=bbse_commerce_inven&mode=list'" style="width:100px;"> 목록 </button>
			</div>
			<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']?>" />
			<input type="hidden" name="strun" id="strun" value="<?php echo $strun?>" />
			<input type="hidden" name="delete_yn" id="delete_yn" value="N" />
			<input type="hidden" name="goods_idx" id="goods_idx" style="width:180px;" value="" />
			<input type="hidden" name="storageIdx" id="storageIdx" style="width:180px;" value="" />
    			<table class="dataTbls normal-line-height collapse">
            		<tr>
            			<th class="check-container">제품명</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="goods_name" id="goods_name" style="width:150px;" value="<?php echo $result->goods_name;?>"  />
            				<input type="text" name="goods_option_title" id="goods_option_title" style="width:250px;" value="<?php echo $result->goods_option_title;?>"  />
						<?php }else{?>
            				<input type="text" name="goods_name" id="goods_name" style="width:250px;" value=""  /><input type="text" name="goods_option_title" id="goods_option_title" style="width:250px;" value=""  />
						<?php }?>
						<button type="button" class="button-small green" onClick="search_goods();" style="height: 30px;">제품찾기</button>
            			</td>
            		</tr>
            		<tr>
            			<th class="check-container">제품코드</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="goods_code" id="goods_code" style="width:350px;" value="<?php echo $result->goods_code;?>" readonly/>
						<?php }else{?>
            				<input type="text" name="goods_code" id="goods_code" style="width:350px;" value="" / readonly placeholder = "제품코드는 자동생성 됩니다.">
            				<!-- <button type="button" class="button-small gray" onClick="checkGoodsCode();" style="height: 30px;">중복확인</button> -->
						<?php }?>
            			</td>
            		</tr>
            		<tr>
            			<th class="check-container">더존코드</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){
            			    $douzone = $wpdb->get_row("select * from tbl_douzone_code where goods_code = '".$result->goods_code."' and delete_yn != 'Y'");
                        ?>
            				<input type="text" name="douzone_code" id="douzone_code" style="width:350px;" value="<?php echo $douzone->goods_douzone_code;?>"  />
						<?php }else{?>
            				<input type="text" name="douzone_code" id="douzone_code" style="width:350px;" value=""  />
						<?php }?>
            			</td>
            		</tr>
            		<tr>
            			<th class="check-container">현재수량</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="number" name="current_count" id="current_count" style="width:180px;" value="<?php echo $result->current_count;?>" />
            			<?php }else{?>
            				<input type="number" name="current_count" id="current_count" style="width:180px;" value="" />
						<?php }?>
            			</td>
            		</tr>
            		<tr>
            			<th class="check-container">알림수량</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="number" name="notice_count" id="notice_count" style="width:180px;" value="<?php echo $result->notice_count;?>" />
            			<?php }else{?>
            				<input type="number" name="notice_count" id="notice_count" style="width:180px;" value="" />
						<?php }?>
            			</td>
            		</tr>
<!-- 					<tr> -->
<!--             			<th>시리얼번호</th> -->
<!--             			<td> -->
<!--            			<?php if($_REQUEST['mode']=="edit"){?>
<!--            				<input type="text" name="serial_number" id="serial_number" style="width:180px;" value="<?php echo $result->serial_number;?>"  readonly />
<!--            			<?php }else{?>
<!--            				<input type="text" name="serial_number" id="serial_number" style="width:180px;" placeholder="자동생성"  value=""  readonly />
<!--<?php }?>
<!--             			</td> -->
<!--             		</tr> -->
<!-- 					<tr> -->
<!--             			<th>태그ID</th> -->
<!--             			<td> -->
<!--            			<?php if($_REQUEST['mode']=="edit"){?>
<!--            				<input type="text" name="tag_id" id="tag_id" style="width:180px;" value="<?php echo $result->tag_id;?>"  readonly />
<!--            			<?php }else{?>
<!--            				<input type="text" name="tag_id" id="tag_id" style="width:180px;" placeholder="자동생성"  value=""  readonly />
<!--						<?php }?>
<!--             			</td> -->
<!--             		</tr> -->
            		<tr>
            			<th class="check-container">창고명</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="storage_name" id="storage_name" style="width:180px;" value="<?php echo $storage->storage_name;?>"  readonly />
            				<input type="text" name="storage_code" id="storage_code" style="width:180px;" value="<?php echo $result->storage_code;?>"  readonly />
            			<?php }else{?>
            				<input type="text" name="storage_name" id="storage_name" style="width:180px;" value=""  readonly />
            				<input type="text" name="storage_code" id="storage_code" style="width:180px;" value=""  readonly />
						<?php }?>
        				<button type="button" class="button-small green" onClick="search_storage();" style="height: 30px;">창고검색</button>
            			</td>
            		</tr>
            		<tr>
            			<th class="check-container">창고주소</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
							<input type="text" name="addr1" id="addr1" style="width:350px;" value="<?php echo $storage->addr1;?>"  readonly/><br/>
            				<input type="text" name="addr2" id="addr2" style="width:350px;" value="<?php echo $storage->addr2;?>"  readonly/>
            			<?php }else{?>
            				<input type="text" name="addr1" id="addr1" style="width:350px;" value=""  readonly/><br/>
            				<input type="text" name="addr2" id="addr2" style="width:350px;" value=""  readonly/>
						<?php }?>
            			</td>
            		</tr>
            		<tr>
            			<th class="check-container">상세위치</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
							<input type="text" name="rack_code" id="rack_code" style="width:180px;" value="<?php echo $result->rack_code;?>"  readonly/><br/>
            			<?php }else{?>
            				<input type="text" name="rack_code" id="rack_code" style="width:180px;" value=""  readonly/><br/>
						<?php }?>
						<button type="button" class="button-small green" onClick="search_location();" style="height: 30px;">위치검색</button>
            			</td>
            		</tr>
<!-- 					<tr> -->
<!--             			<th class="check-container">창고담당자</th> -->
<!--             			<td>            				 -->
<!--            			<?php if($_REQUEST['mode']=="edit"){?>  -->
<!--							<select name="manager_id" id ="manager_id" style="width:180px"> -->
<!--            					<?php $manager_list = $wpdb->get_results("select * from autopole3144.bbse_commerce_membership where user_class = 1");
           					foreach($manager_list as $data){?> -->
<!--            						<option value="<?php echo $data->user_id;?>" <?php if($data->user_id==$result->manager_id) echo 'selected';?> ><?php echo $data->name?></option> -->
<!--            					<?php }?> -->
<!--             				</select> -->
<!--            			<?php }else{?> -->
<!--            				<select name="manager_id" id ="manager_id" style="width:180px">
<!--             					<option value="">선택</option> -->
<!--            					<?php $manager_list = $wpdb->get_results("select * from autopole3144.bbse_commerce_membership where user_class = 1");
          					foreach($manager_list as $data){?>
<!--            						<option value="<?php echo $data->user_id;?>"><?php echo $data->name?></option> -->
<!--            					<?php }?> -->
<!--             				</select> -->
<!--						<?php }?> -->
<!--             			</td> -->
<!--             		</tr> -->
            		
            		<?php if($_REQUEST['mode']=="edit"){?>
            		<tr>
            			<th>등록일자</th>
            			<td>
            				<?php echo $result->reg_date?>
            			</td>
            		</tr>
            		<tr>
            			<th>수정일자</th>
            			<td>
            				<?php echo $result->update_date?>
            			</td>
            		</tr>
            		<?php }?>
    			</table>
			</form>
			<div class="clearfix" style="height:20px;"></div>

			<div style="text-align: center;">
				<button style="float:right;" type="button" name="productSelect" id="productSelect" class="button-bbse red" onClick="save_inven('<?php echo $USER_ACTION_URL?>');" style="width:100px;"> <?php echo $button_name;?> </button>
			</div>
		</div>
	</div>
<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */?>
<div id="commerceZipcodeLayer" style="display:none;border:5px solid;position:fixed;width:320px;height:500px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script><!--https-->
<script>
    function closeDaumPostcode() {
        jQuery("#commerceZipcodeLayer").css("display","none");
    }
</script> 
<?php }?>

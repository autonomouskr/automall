
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
	
	function search_goods(){

		var popupTitle="적재물품조회";
		var tbHeight = window.innerHeight * .60;
		var tbWidth = window.innerWidth * .35;
		tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-location-items-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-location-items-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		return false;
		
	}
	
	function search_storage(){
	
		var popupTitle="창고조회";
		var tbHeight = window.innerHeight * .60;
		var tbWidth = window.innerWidth * .35;
		tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-storage-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-storage-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		return false;
			
	}
	
    function save_location(action_url){
    
    	var frm = document.location_frm;
    	<?php if($_REQUEST['mode']=="add"){?>
    	
        	if(frm.rack_code.value == "") {
        		alert("Rack명을 입력해주세요.");
        		frm.rack_code.focus();
        		return false; 
        	}
        	
			if(frm.location_y.value == "") {
        		alert("열 위치를 입력해주세요.");
        		frm.location_y.focus();
        		return false; 
        	}
        	
			if(frm.location_x.value == "") {
        		alert("행 위치를 입력해주세요.");
        		frm.location_x.focus();
        		return false; 
        	}
        	
			if(frm.storage_code.value == "") {
        		alert("창고코드를 입력해주세요.");
        		frm.storage_code.focus();
        		return false; 
        	}
        	
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
	$mode_tit = "물류위치추가";
	$strun = "add_proc";
	$button_name = "등록";
}else{
	$mode_tit = "물류위치정보";
	$strun = "edit_proc";
	$button_name = "수정";
	$result = $wpdb->get_row("select * from autopole3144.tbl_inven where goods_code = '".$goods_code."' and storage_code = '".$storage_code."' and delete_yn != 'Y' ");
	$storage = $wpdb->get_row("select * from autopole3144.tbl_storage where storage_code = '".$result->storage_code."'");
}

$USER_ACTION_URL = "admin.php?page=bbse_commerce_location&mode=".$_REQUEST['mode'];

?>
	<div class="wrap">
		<div id="popup-register-inven">
			<form method="post" id="location_frm" name="location_frm" enctype="multipart/form-data">
			<div class="titleH5" style="margin:20px 0px 10px 0; "><?php echo $mode_tit?>
				<button style="float:right; margin-bottom: 5px" type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="location.href='admin.php?page=bbse_commerce_inven&mode=list'" style="width:100px;"> 목록 </button>
			</div>
			<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']?>" />
			<input type="hidden" name="strun" id="strun" value="<?php echo $strun?>" />
			<input type="hidden" name="delete_yn" id="delete_yn" value="N" />
			<input type="hidden" name="storageIdx" id="storageIdx" value="" />
    			<table class="dataTbls normal-line-height collapse">
            		<tr>
            			<th class="check-container">Rack명</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="rack_code" id="rack_code" style="width:268px;" value="<?php echo $result->rack_code;?>"  />
						<?php }else{?>
            				<input type="text" name="rack_code" id="rack_code" style="width:268px;" value=""  />
						<?php }?>
            			</td>
            		</tr>
            		<tr>
            			<th class="check-container">위치</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<span style="color:blue; font-weight: bold; margin-right: 10px;">행:</span><input type="text" name="location_x" id="location_x" style="width:100px;" value="<?php echo $result->location_x;?>" readonly/>
            				<span style="color:blue; font-weight: bold; margin-right: 10px;">열:</span><input type="text" name="location_y" id="location_y" style="width:100px;" value="<?php echo $result->location_x;?>" readonly/>
						<?php }else{?>
            				<span style="color:blue; font-weight: bold; margin-right: 10px;">행:</span><input type="text" name="location_x" id="location_x" style="width:100px;" value="" />
            				<span style="color:blue; font-weight: bold; margin-right: 10px;">열:</span><input type="text" name="location_y" id="location_y" style="width:100px;" value="" />
						<?php }?>
            			</td>
            		</tr>
            		<!-- 
            		<tr>
            			<th class="check-container">적재물품</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="items" id="items" style="width:268px;" value="<?php echo $result->items;?>" />
            			<?php }else{?>
            				<input type="text" name="items" id="items" style="width:268px;" value="" />
						<?php }?>
						<button type="button" class="button-small green" onClick="search_goods();" style="height: 30px;">제품찾기</button>
            			</td>
            		</tr>
            		 -->
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
				<button style="float:right;" type="button" name="productSelect" id="productSelect" class="button-bbse red" onClick="save_location('<?php echo $USER_ACTION_URL?>');" style="width:100px;"> <?php echo $button_name;?> </button>
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

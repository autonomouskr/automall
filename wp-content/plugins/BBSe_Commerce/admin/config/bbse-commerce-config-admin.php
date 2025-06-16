<script language="javascript">
	function config_submit(){
		var cType=jQuery("#cType").val();
		var tMode=jQuery("#tMode").val();
		var tIdx=jQuery("#tIdx").val();

		if(!jQuery("#admin_id").val()){
			alert("운영자 ID를 입력해 주세요.     ");
			jQuery("#admin_id").focus();
			return;
		}
		if(!jQuery("#admin_name").val()){
			alert("운영자 이름을 입력해 주세요.     ");
			jQuery("#admin_name").focus();
			return;
		}
		if(!jQuery("#admin_menu_theme").is(":checked")
		 && !jQuery("#admin_menu_category").is(":checked") 
		 && !jQuery("#admin_menu_member").is(":checked") 
		 && !jQuery("#admin_menu_goods").is(":checked") 
		 && !jQuery("#admin_menu_maindisplay").is(":checked") 
		 && !jQuery("#admin_menu_order").is(":checked") 
		 && !jQuery("#admin_menu_statistics").is(":checked")
		 && !jQuery("#admin_menu_payment").is(":checked")  
		 && !jQuery("#admin_menu_config").is(":checked") 
		 && !jQuery("#admin_menu_qna").is(":checked")
		 && !jQuery("#admin_menu_inven").is(":checked")
		 && !jQuery("#bbse_commerce_invenInOut").is(":checked")
		 && !jQuery("#bbse_commerce_inven").is(":checked")
		 && !jQuery("#bbse_commerce_storage").is(":checked")
		 && !jQuery("#bbse_commerce_douzone").is(":checked")
		 && !jQuery("#bbse_commerce_serial").is(":checked")
		 
		 ){
			alert("운영자 메뉴 등록을 선택해 주세요.     ");
			jQuery("#admin_menu_theme").focus();
			return;
		}

		if(confirm('운영자 정보를 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('운영자 정보를 정상적으로 저장하였습니다.   ');
						if(tMode=='add') go_config('admin');
						else go_config_option(cType,tMode,tIdx);
					}
					else if(result=='notExist'){
						alert('존재하지 않는 입금계좌 정보입니다.   ');
					}
					else if(result=='DbError'){
						alert('[Error !] DB 오류 입니다.   ');
					}
					else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}

	function go_admin_remove(tIdx){
		if(confirm('운영자 정보를 삭제하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: {cType:'admin',tMode:'remove',tIdx:tIdx}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('운영자 정보를 정상적으로 삭제하였습니다.   ');
						go_config('admin');
					}
					else if(result=='notExist'){
						alert('존재하지 않는 입금계좌 정보입니다.   ');
					}
					else if(result=='DbError'){
						alert('[Error !] DB 오류 입니다.   ');
					}
					else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}

	// 회원리스트 팝업
	function member_list_popup(pTarget){
		var popupTitle="회원검색";

		var existAdmin=jQuery("#existAdmin").val();
		var tbWidth = window.innerWidth * .3;
		var tbHeight = window.innerHeight * .4;

		tb_show("운영자관리 ("+popupTitle+")", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-admin-list.php?existAdmin="+existAdmin+"&#38;width=600&#38;height=450&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}
</script>

	<div class="titleH5" style="margin:30px 0 10px 0; ">운영자관리<?php if(!$_REQUEST['tMode']){?><div style="float:right;"><button type="button"class="button-small blue" onClick="go_config_option('<?php echo $cType;?>','add');" style="height:25px;">운영자 추가</button></div><?php }?></div>

<?php 
$s_total = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='admin' ORDER BY idx DESC");



if($_REQUEST['tMode']=='add' || $_REQUEST['tMode']=='modify'){
	if($_REQUEST['tMode']=='modify' && $_REQUEST['tMode']>'0'){
		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE idx='".$_REQUEST['tIdx']."' AND config_type='".$cType."'");
		$data=unserialize($confData->config_data);
	}

	$existAdmin="";
	if($s_total>'0'){
		$result = $wpdb->get_results("SELECT * FROM bbse_commerce_config WHERE config_type='admin' ORDER BY idx DESC");

		$i='0';
		foreach($result as $i=>$rstData) {
			$num = $s_total-$i; //번호
			$serData=unserialize($rstData->config_data);

			if($existAdmin) $existAdmin .=",";
			$existAdmin .=$serData['admin_id'];
		}
	}
?>

	<div>
		<form name="cnfFrm" id="cnfFrm">
			<input type="hidden" name="cType" id="cType" value="admin" />
			<input type="hidden" name="tMode" id="tMode" value="<?php echo $_REQUEST['tMode'];?>" />
			<input type="hidden" name="tIdx" id="tIdx" value="<?php echo $_REQUEST['tIdx'];?>" />
			<input type="hidden" name="existAdmin" id="existAdmin" value="<?php echo $existAdmin;?>" />

			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>사용여부</th>
					<td><input type="radio" name="admin_info_use" id="admin_info_use" value="on" <?php echo ($data['admin_info_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="admin_info_use" id="admin_info_use" value="off" <?php echo (!$data['admin_info_use'] || $data['admin_info_use']=='off')?"checked='checked'":"";?> /> 사용안함</td>
				</tr>
				<tr>
					<th>운영자 ID</th>
					<td><input type="text" name="admin_id" id="admin_id" value="<?php echo $data['admin_id'];?>" style="width:200px;background:#ffffff;" readonly />&nbsp;&nbsp;&nbsp;<button type="button"class="button-small blue" onClick="member_list_popup();" style="height:25px;">검색</button></td>
				</tr>
				<tr>
					<th>이름</th>
					<td><input type="text" name="admin_name" id="admin_name" value="<?php echo $data['admin_name'];?>" style="width:200px;background:#ffffff;" readonly /></td>
				</tr>
				<tr>
					<th style="line-height:16px;">운영자 메뉴 등록<br><span style="font-weight:normal;font-size:11px;">(메뉴 접근 권한 체크)</span></th>
					<td><input type="checkbox" name="admin_menu_member" id="admin_menu_member" value="on" <?php echo ($data['admin_menu_member']=='on')?"checked='checked'":"";?> /> 
					회원관리&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="admin_menu_goods" id="admin_menu_goods" value="on" <?php echo ($data['admin_menu_goods']=='on')?"checked='checked'":"";?> /> 
					상품관리 (카테고리관리/상품관리/상품등록)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="admin_menu_order" id="admin_menu_order" value="on" <?php echo ($data['admin_menu_order']=='on')?"checked='checked'":"";?> /> 
					주문관리&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="admin_menu_statistics" id="admin_menu_statistics" value="on" <?php echo ($data['admin_menu_statistics']=='on')?"checked='checked'":"";?> /> 
					통계정산관리&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="admin_menu_config" id="admin_menu_config" value="on" <?php echo ($data['admin_menu_config']=='on')?"checked='checked'":"";?> /> 
					상점관리&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" name="admin_menu_qna" id="admin_menu_qna" value="on" <?php echo ($data['admin_menu_qna']=='on')?"checked='checked'":"";?> /> 문의관리 (상품문의/고객상품평)
					<input type="checkbox" name="admin_menu_payment" id="admin_menu_payment" value="on" <?php echo ($data['admin_menu_payment']=='on')?"checked='checked'":"";?> /> 입금관리 <br>
					<input type="checkbox" name="admin_menu_inven" id="admin_menu_inven" value="on" <?php echo ($data['admin_menu_inven']=='on')?"checked='checked'":"";?> /> 재고관리 
					<input type="checkbox" name="bbse_commerce_invenInOut" id="bbse_commerce_invenInOut" value="on" <?php echo ($data['bbse_commerce_invenInOut']=='on')?"checked='checked'":"";?> /> 입출고관리
					<input type="checkbox" name="bbse_commerce_inven" id="bbse_commerce_inven" value="on" <?php echo ($data['bbse_commerce_inven']=='on')?"checked='checked'":"";?> /> 제품관리
					<input type="checkbox" name="bbse_commerce_storage" id="bbse_commerce_storage" value="on" <?php echo ($data['bbse_commerce_storage']=='on')?"checked='checked'":"";?> /> 창고관리
					<input type="checkbox" name="bbse_commerce_douzone" id="bbse_commerce_douzone" value="on" <?php echo ($data['bbse_commerce_douzone']=='on')?"checked='checked'":"";?> /> 더존코드관리
					<input type="checkbox" name="bbse_commerce_serial" id="bbse_commerce_serial" value="on" <?php echo ($data['bbse_commerce_serial']=='on')?"checked='checked'":"";?> /> 일련번호관리
					<input type="checkbox" name="bbse_commerce_location" id="bbse_commerce_location" value="on" <?php echo ($data['bbse_commerce_location']=='on')?"checked='checked'":"";?> /> 로케이션관리
				</tr>
			</table>
			<div class="clearfix"></div>
		</form>
	</div>

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onClick="config_submit();" style="width:150px;"> 등록/저장 </button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="button-bbse red" onClick="go_config_option('<?php echo $cType;?>','','');" style="width:150px;"> 목록 </button>
	</div>

<?php } else{?>

	<div>
		<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="5%"><col width="20%"><col width="10%"><col width=""><col width="10%"><col width="20%"></colgroup>
			<tr>
				<th>번호</th>
				<th>아이디</th>
				<th>이름</th>
				<th>메뉴접근권한</th>
				<th>사용여부</th>
				<th>관리</th>
			</tr>
	<?php 
	if($s_total>'0'){
		$result = $wpdb->get_results("SELECT * FROM bbse_commerce_config WHERE config_type='admin' ORDER BY idx DESC");

		$i='0';
		foreach($result as $i=>$rstData) {
			$num = $s_total-$i; //번호
			$data=unserialize($rstData->config_data);

			$strPermission="";
			if($data['admin_menu_member']=='on') {
				if($strPermission) $strPermission .=", ";
				$strPermission .="회원관리";
			}
			if($data['admin_menu_goods']=='on') {
				if($strPermission) $strPermission .=", ";
				$strPermission .="상품관리";
			}
			if($data['admin_menu_order']=='on') {
				if($strPermission) $strPermission .=", ";
				$strPermission .="주문관리";
			}
			if($data['admin_menu_statistics']=='on') {
				if($strPermission) $strPermission .=", ";
				$strPermission .="통계정산관리";
			}
			if($data['admin_menu_config']=='on') {
				if($strPermission) $strPermission .=", ";
				$strPermission .="상점관리";
			}
			if($data['admin_menu_qna']=='on') {
				if($strPermission) $strPermission .=", ";
				$strPermission .="문의관리";
			}
			if($data['admin_menu_payment']=='on') {
			    if($strPermission) $strPermission .=", ";
			    $strPermission .="입금관리";
			}
			if($data['admin_menu_inven']=='on') {
			    if($strPermission) $strPermission .=", ";
			    $strPermission .="재고관리";
			}
?>
			<tr>
				<td style="text-align:center;"><?php echo $num;?></td>
				<td style="text-align:center;"><?php echo $data['admin_id'];?></td>
				<td style="text-align:center;"><?php echo $data['admin_name'];?></td>
				<td style="text-align:center;"><?php echo $strPermission;?></td>
				<td style="text-align:center;"><?php echo ($data['admin_info_use']=='on')?"사용함":"<font color='#ED1C24'>사용안함</font>";?></td>
				<td style="text-align:center;"><button type="button"class="button-small blue" onClick="go_config_option('<?php echo $cType;?>','modify','<?php echo $rstData->idx;?>');" style="height:25px;">수정</button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button"class="button-small red" onClick="go_admin_remove('<?php echo $rstData->idx;?>');" style="height:25px;">삭제</button></td>
			</tr>
	<?php
			$i++;
		}
	}
	else{
	?>
			<tr>
				<td style="height:35px;text-align:center;" colspan="9">등록 된 운영자 정보가 존재하지 않습니다.</td>
			</tr>
	<?php 
	}
	?>
		</table>
	</div>

<?php }?>
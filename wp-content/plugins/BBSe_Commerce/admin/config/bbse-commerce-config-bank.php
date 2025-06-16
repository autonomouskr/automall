<script language="javascript">
	function config_submit(){
		var cType=jQuery("#cType").val();
		var tMode=jQuery("#tMode").val();
		var tIdx=jQuery("#tIdx").val();

		if(!jQuery("#bank_name").val()){
			alert("은행명을 입력해 주세요.     ");
			jQuery("#bank_name").focus();
			return;
		}
		if(!jQuery("#bank_account_number").val()){
			alert("은행 계좌번호를 입력해 주세요.     ");
			jQuery("#bank_name").focus();
			return;
		}
		if(!jQuery("#bank_owner_name").val()){
			alert("은행 예금주를 입력해 주세요.     ");
			jQuery("#bank_name").focus();
			return;
		}

		if(confirm('입금계좌 정보를 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('입금계좌 정보를 정상적으로 저장하였습니다.   ');
						if(tMode=='add') go_config('bank');
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

	function go_bank_remove(tIdx){
		if(confirm('입금계좌 정보를 삭제하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: {cType:'bank',tMode:'remove',tIdx:tIdx}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('입금계좌 정보를 정상적으로 삭제하였습니다.   ');
						go_config('bank');
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
</script>

	<div class="titleH5" style="margin:30px 0 10px 0; ">입금계좌<?php if(!$_REQUEST['tMode']){?><div style="float:right;"><button type="button"class="button-small blue" onClick="go_config_option('<?php echo $cType;?>','add');" style="height:25px;">입금 계좌 추가</button></div><?php }?></div>

<?php 
if($_REQUEST['tMode']=='add' || $_REQUEST['tMode']=='modify'){
	if($_REQUEST['tMode']=='modify' && $_REQUEST['tMode']>'0'){
		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE idx='".$_REQUEST['tIdx']."' AND config_type='".$cType."'");
		$data=unserialize($confData->config_data);
	}
?>

	<div>
		<form name="cnfFrm" id="cnfFrm">
			<input type="hidden" name="cType" id="cType" value="bank" />
			<input type="hidden" name="tMode" id="tMode" value="<?php echo $_REQUEST['tMode'];?>" />
			<input type="hidden" name="tIdx" id="tIdx" value="<?php echo $_REQUEST['tIdx'];?>" />
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>계좌 사용여부</th>
					<td><input type="radio" name="bank_info_use" id="bank_info_use" value="on" <?php echo ($data['bank_info_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="bank_info_use" id="bank_info_use" value="off" <?php echo (!$data['bank_info_use'] || $data['bank_info_use']=='off')?"checked='checked'":"";?> /> 사용안함</td>
				</tr>
				<tr>
					<th>은행명</th>
					<td><input type="text" name="bank_name" id="bank_name" value="<?php echo $data['bank_name'];?>" style="width:50%;" /></td>
				</tr>
				<tr>
					<th>계좌번호</th>
					<td><input type="text" name="bank_account_number" id="bank_account_number" value="<?php echo $data['bank_account_number'];?>" style="width:50%;" /></td>
				</tr>
				<tr>
					<th>예금주</th>
					<td><input type="text" name="bank_owner_name" id="bank_owner_name" value="<?php echo $data['bank_owner_name'];?>" style="width:50%;" /></td>
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
			<colgroup><col width="5%"><col width="20%"><col width=""><col width="15%;"><col width="10%"><col width="20%"></colgroup>
			<tr>
				<th>번호</th>
				<th>은행명</th>
				<th>계좌번호</th>
				<th>예금주</th>
				<th>사용여부</th>
				<th>관리</th>
			</tr>
	<?php 
	$s_total = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='bank' ORDER BY idx DESC");

	if($s_total>'0'){
		$result = $wpdb->get_results("SELECT * FROM bbse_commerce_config WHERE config_type='bank' ORDER BY idx DESC");

		$i='0';
		foreach($result as $i=>$data) {
			$num = $s_total-$i; //번호
			$serData=unserialize($data->config_data);
?>
			<tr>
				<td style="text-align:center;"><?php echo $num;?></td>
				<td style="text-align:center;"><?php echo $serData['bank_name'];?></td>
				<td style="text-align:center;"><?php echo $serData['bank_account_number'];?></td>
				<td style="text-align:center;"><?php echo $serData['bank_owner_name'];?></td>
				<td style="text-align:center;"><?php echo ($serData['bank_info_use']=='on')?"사용함":"<font color='#ED1C24'>사용안함</font>";?></td>
				<td style="text-align:center;"><button type="button"class="button-small blue" onClick="go_config_option('<?php echo $cType;?>','modify','<?php echo $data->idx;?>');" style="height:25px;">수정</button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button"class="button-small red" onClick="go_bank_remove('<?php echo $data->idx;?>');" style="height:25px;">삭제</button></td>
			</tr>
	<?php
			$i++;
		}
	}
	else{
	?>
			<tr>
				<td style="height:35px;text-align:center;" colspan="9">등록 된 입금계좌 정보가 존재하지 않습니다.</td>
			</tr>
	<?php 
	}
	?>
		</table>
	</div>

<?php }?>
<script language="javascript">
<?php if($_REQUEST['tMode']){?>
	jQuery(document).ready(function() {
		jQuery('#s_keyword').keyup(function(e) {
			if (e.keyCode == 13) search_submit();       
		});

		// 날짜(datepicker) initialize (1)
		jQuery.datepicker.regional['ko']= {
			closeText:'닫기',
			prevText:'이전달',
			nextText:'다음달',
			currentText:'오늘',
			monthNames:['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUM)','7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
			monthNamesShort:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames:['일','월','화','수','목','금','토'],
			dayNamesShort:['일','월','화','수','목','금','토'],
			dayNamesMin:['일','월','화','수','목','금','토'],
			weekHeader:'Wk',
			dateFormat:'yy-mm-dd',
			firstDay:0,
			isRTL:false,
			showMonthAfterYear:true,
			yearSuffix:''
		};
		jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ko']);

		// 날짜(datepicker) initialize (2)
		jQuery(".datepicker").datepicker(jQuery.datepicker.regional["ko"]);
		jQuery('.datepicker').datepicker('option', {dateFormat:'yy-mm-dd'});
	});

	function config_submit(){
		var cType=jQuery("#cType").val();
		var tMode=jQuery("#tMode").val();
		var tIdx=jQuery("#tIdx").val();

		if(!jQuery("#popup_title").val()){
			alert("창 제목을 입력해 주세요.     ");
			jQuery("#popup_title").focus();
			return;
		}
		if(!jQuery("#s_period_1").val()){
			alert("사용기간(시작)을 입력해 주세요.     ");
			jQuery("#s_period_1").focus();
			return;
		}
		if(!jQuery("#s_period_2").val()){
			alert("사용기간(종료)을 입력해 주세요.     ");
			jQuery("#s_period_2").focus();
			return;
		}

		if(jQuery("input[id='popup_use']:checked").val()=='on'){
			if(!jQuery("#popup_width").val()){
				alert("창 크기(가로)를 입력해 주세요.     ");
				jQuery("#popup_width").focus();
				return;
			}
			if(!jQuery("#popup_height").val()){
				alert("창 크기(세로)를 입력해 주세요.     ");
				jQuery("#popup_height").focus();
				return;
			}
			if(!jQuery("#popup_top").val()){
				alert("창 위치(위)를 입력해 주세요.     ");
				jQuery("#popup_top").focus();
				return;
			}
			if(!jQuery("#popup_left").val()){
				alert("창 위치(왼쪽)를 입력해 주세요.     ");
				jQuery("#popup_left").focus();
				return;
			}
			if(!jQuery("input[id='popup_scrollbar']:checked").val()){
				alert("팝업창 스크롤바를 선택해 주세요.     ");
				jQuery("#popup_scrollbar").eq(0).focus();
				return;
			}
			if(!jQuery("input[id='popup_window']:checked").val()){
				alert("창 종류를 선택해 주세요.     ");
				jQuery("#popup_window").eq(0).focus();
				return;
			}
		}

		switchEditors.go('popup_contents', 'tmce');

		var ed = tinyMCE.get('popup_contents');
		jQuery("#popup_contents").val(ed.getContent({format : 'raw'}));  // raw(비쥬얼) / text(텍스트)

		var tmpDetail=jQuery("#popup_contents").val().replace('<p><br data-mce-bogus=\"1\"></p>', '');
		tmpDetail=tmpDetail.replace('<p><br></p>', '');

		if(!tmpDetail){
			alert("팝업 내용을 입력해 주세요.     ");
			tinyMCE.get('popup_contents').focus()
			return
		}

		if(confirm('팝업 설정을 저장하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: jQuery("#cnfFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('팝업 설정을 정상적으로 저장하였습니다.   ');
						if(tMode=='add') go_config('popup');
						else go_config_option(cType,tMode,tIdx);
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
<?php }?>

	function go_popup_remove(tIdx){
		if(confirm('팝업 정보를 삭제하시겠습니까?     ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-config.exec.php', 
				data: {cType:'popup',tMode:'remove',tIdx:tIdx}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('팝업 정보를 정상적으로 삭제하였습니다.   ');
						go_config('popup');
					}
					else if(result=='notExist'){
						alert('존재하지 않는 팝업 정보입니다.   ');
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

	<div class="titleH5" style="margin:30px 0 10px 0; ">팝업관리<?php if(!$_REQUEST['tMode']){?><div style="float:right;"><button type="button"class="button-small blue" onClick="go_config_option('<?php echo $cType;?>','add');" style="height:25px;">팝업 추가</button></div><?php }?></div>

<?php
if($_REQUEST['tMode']=='add' || $_REQUEST['tMode']=='modify'){
	if($_REQUEST['tMode']=='modify' && $_REQUEST['tMode']>'0'){
		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE idx='".$_REQUEST['tIdx']."' AND config_type='".$cType."'");
		$data=unserialize($confData->config_data);

		$s_period_1=date("Y-m-d",$data['s_period_1']);
		$s_period_2=date("Y-m-d",$data['s_period_2']);
	}
?>

	<div>
		<form name="cnfFrm" id="cnfFrm">
			<input type="hidden" name="cType" id="cType" value="popup" />
			<input type="hidden" name="tMode" id="tMode" value="<?php echo $_REQUEST['tMode'];?>" />
			<input type="hidden" name="tIdx" id="tIdx" value="<?php echo $_REQUEST['tIdx'];?>" />
			<table class="dataTbls overWhite collapse">
				<colgroup><col width="15%"><col width=""></colgroup>
				<tr>
					<th>팝업 사용여부</th>
					<td><input type="radio" name="popup_use" id="popup_use" value="on" <?php echo ($data['popup_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="popup_use" id="popup_use" value="off" <?php echo (!$data['popup_use'] || $data['popup_use']=='off')?"checked='checked'":"";?> /> 사용안함</td>
				</tr>
				<tr>
					<th>창 제목</th>
					<td><input type="text" name="popup_title" id="popup_title" value="<?php echo $data['popup_title'];?>" style="width:50%;" /></td>
				</tr>
				<tr>
					<th>사용기간</th>
					<td><input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" /></td>
				</tr>
				<tr>
					<th>창 크기</th>
					<td>가로 <input type="text" name="popup_width" id="popup_width" onkeydown="check_number();" value="<?php echo $data['popup_width'];?>" style="width:50px;" />(pixel)&nbsp;&nbsp;X&nbsp;&nbsp;세로 <input type="text" name="popup_height" id="popup_height" onkeydown="check_number();" value="<?php echo $data['popup_height'];?>" style="width:50px;" />(pixel)</td>
				</tr>
				<tr>
					<th>창 위치</th>
					<td>위에서 <input type="text" name="popup_top" id="popup_top" onkeydown="check_number();" value="<?php echo $data['popup_top'];?>" style="width:50px;" />(pixel) 만큼, 왼쪽에서 <input type="text" name="popup_left" id="popup_left" onkeydown="check_number();" value="<?php echo $data['popup_left'];?>" style="width:50px;" />(pixel) 만큼 위치</td>
				</tr>
				<tr>
					<th>팝업창 스크롤 바</th>
					<td><input type="radio" name="popup_scrollbar" id="popup_scrollbar" value="on" <?php echo ($data['popup_scrollbar']=='on')?"checked='checked'":"";?> /> 있음&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="popup_scrollbar" id="popup_scrollbar" value="off" <?php echo (!$data['popup_scrollbar'] || $data['popup_scrollbar']=='off')?"checked='checked'":"";?> /> 없음</td>
				</tr>
				<tr>
					<th>창 종류</th>
					<td><input type="radio" name="popup_window" id="popup_window" value="window" <?php echo ($data['popup_window']=='window')?"checked='checked'":"";?> /> 팝업&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="popup_window" id="popup_window" value="layer" <?php echo (!$data['popup_window'] || $data['popup_window']=='layer')?"checked='checked'":"";?> /> 레이어</td>
				</tr>
				<tr>
					<th>팝업내용</th>
					<td>
						<?php 
						wp_editor(html_entity_decode($confData->config_editor), "popup_contents", $settings=array('textarea_name'=>'popup_contents', 'textarea_rows'=>'7')); 
						?> 
					</td>
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
			<colgroup><col width="5%"><col width="30%"><col width=""><col width="15%;"><col width="10%"><col width="20%"></colgroup>
			<tr>
				<th>번호</th>
				<th>제목</th>
				<th>기간</th>
				<th>등록일</th>
				<th>사용여부</th>
				<th>관리</th>
			</tr>
	<?php 
	$s_total = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='popup' ORDER BY idx DESC");

	if($s_total>'0'){
		$result = $wpdb->get_results("SELECT * FROM bbse_commerce_config WHERE config_type='popup' ORDER BY idx DESC");

		$i='0';
		foreach($result as $i=>$data) {
			$num = $s_total-$i; //번호
			$serData=unserialize($data->config_data);
	?>
			<tr>
				<td style="text-align:center;"><?php echo $num;?></td>
				<td style="text-align:center;"><?php echo $serData['popup_title'];?></td>
				<td style="text-align:center;"><?php echo date("Y.m.d",$serData['s_period_1']);?> ~ <?php echo date("Y.m.d",$serData['s_period_2']);?></td>
				<td style="text-align:center;"><?php echo date("Y.m.d",$data->config_reg_date);?></td>
				<td style="text-align:center;"><?php echo ($serData['popup_use']=='on')?"사용함":"<font color='#ED1C24'>사용안함</font>";?></td>
				<td style="text-align:center;"><button type="button"class="button-small blue" onClick="go_config_option('<?php echo $cType;?>','modify','<?php echo $data->idx;?>');" style="height:25px;">수정</button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button"class="button-small red" onClick="go_popup_remove('<?php echo $data->idx;?>');" style="height:25px;">삭제</button></td>
			</tr>
	<?php
			$i++;
		}
	}
	else{
	?>
			<tr>
				<td style="height:35px;text-align:center;" colspan="9">등록 된 팝업 설정이 존재하지 않습니다.</td>
			</tr>
	<?php 
	}
	?>
		</table>
	</div>

<?php }?>
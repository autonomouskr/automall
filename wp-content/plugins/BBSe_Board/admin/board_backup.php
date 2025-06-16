<script type="text/javascript">
function backup_check(mode){
	if(confirm("DB 백업을 진행하시겠습니까?")){
		location.href = "<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/xml_backup.php";
	}
}

function restoration_check(){
	if(jQuery("#bbse_board_db_file").val() == ""){alert("복구할 파일을 선택해주세요.");return false;}
	if(confirm("DB 복구를 진행하시겠습니까?")){
		var jQfrm = jQuery("#db_frm");
		jQfrm.attr("action", "<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/xml_import.php");
		jQfrm.ajaxForm({
			type:"POST",
			async:true,
			success:function(data, state){
				var data_arr = data.split("|||");
				var res = data_arr[0];

				if(res == "success"){
					document.action_frm.submit();
				}else if(res == "error file_type"){
					alert("파일형식이 올바르지 않습니다.");
					location.reload();
				}else{
					alert("DB 복구에 실패하였습니다.");
					location.reload();
				}
			}
		});
		jQfrm.submit();
	}
}
</script>
<div class="wrap">
	<?php
	if(!empty($edit_mode) && $edit_mode == "edit"){
		echo '<div id="message" class="updated fade"><p><strong>DB 복구에 성공하였습니다.</strong></p></div>';
	}
	?>
	<div id="bbse_box">
		<div class="inner">
			<div class="guide_top">
				<span class="tl"></span><span class="tr"></span><span class="manual_btn"><a href="http://manual.bbsetheme.com/bbse-board" target="_blank"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>images/btn_manual.png" /></a></span>
				<a href="#"><span class="logo">BBS</span><span class="logo_board">e-Board</span><span class="logo_version"><?php echo BBSE_BOARD_VER?></span></a>
			</div>
			<div id="content">
				<form method="post" name="db_frm" id="db_frm" enctype="multipart/form-data">
				<input type="hidden" name="save_check" id="save_check" value="1" />
				<div class="tit">데이터 백업/복구</div>
				<ul class="form_ul1">
					<li>
						<div class="form_stitle2">
							<div class="small_title">데이터 백업받기(다운로드)</div>
							현재까지 게시판에 등록된 데이터 DB를 다운로드 받을 수 있습니다.<br />
							(첨부파일(이미지, 문서 등)은 복구되지 않습니다.)<br /><br />
							플러그인이 제공하는 데이터관리 기능은 백업된 데이터에 대해 어떠한 보증을하지 않으므로<br />
							사용자는 반드시 제공하는 기능 이외에 별도의 수단으로 직접 데이터베이스를 안전하게 백업을 하여야 합니다.
						</div>
						<div class="form_content">
							<button type="button" class="b _c1" onclick="backup_check();">다운로드(XML 방식)</button>
							<div class="sub_info">
								* 첨부파일 경로안내<br />
								BBS e-Board에 등록된 모든 첨부파일은 아래 경로에서 관리합니다.<br />
								워드프레스설치경로/wp-content/uploads/bbse-board/<br />
								데이터 백업 시 첨부파일은 포함되지 않으니 반드시 위 경로에서 다운받아 놓으시기 바랍니다.
							</div>
						</div>
					</li>
					<li>
						<div class="form_stitle2">
							<div class="small_title">데이터 복구하기(업로드)</div>
							백업받은 데이터 파일을 업로드하여 복구할 수 있습니다. 데이터 복구시 기존 데이터는 전부 초기화 됩니다.<br />
							(첨부파일(이미지, 문서 등)은 복구되지 않습니다.)
						</div>
						<div class="form_content">
							<input type="file" style="width:300px;" name="bbse_board_db_file" id="bbse_board_db_file" />
							<button type="button" class="b _c1" onclick="restoration_check();">업로드(XML 방식)</button>
						</div>
					</li>
				</ul>
				</form>
			</div>
			<div class="guide_bottom"><span class="lb"></span><span class="rb"></span></div>
		</div>
	</div>
	<?php global $noticeBoxComment; echo $noticeBoxComment;?>
</div>
<form method='post' name='action_frm' action='<?php echo BBSE_BOARD_BACKUP_URL?>'>
<input type='hidden' name='save_check' value='1' />
</form>
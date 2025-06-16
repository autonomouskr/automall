<script type="text/javascript">
// 게시물 저장 및 파일업로드
function write_check(ajax_act){
	var jQfrm = jQuery('#bbswrite');

	<?php if(!empty($boardInfo->editor) && $boardInfo->editor == "W"){?>
	jQuery("#bbswrite").on("submit", function(form){
		// save TinyMCE instances before serialize

		switchEditors.go('content1', 'tmce');
		var ed = tinyMCE.get('content1');
		jQuery("#content").val(ed.getContent({format : 'raw'}));  // raw(비쥬얼) / text(텍스트)

		tinyMCE.triggerSave();
	});
	<?php }?>

	jQfrm.attr("action", ajax_act);
	jQfrm.ajaxForm({
		type:"POST",
		async:true,
		crossDomain:true,
		xhrFields:{withCredentials:true},
		success:function(data, state){
			data = data.trim();
			var data_arr = data.split("|||");
			var res = jQuery.trim(data_arr[0]);

			if(res == "success"){
				if(data_arr[1] == "modify"){
					<?php if(!empty($tMode) && $tMode == "modify"){?>
					location.href = "<?php echo $curUrl.$link_add?>nType=<?php echo bbse_board_parameter_encryption($bname, 'view', $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'], $brdData->ref)?>";
					<?php }else{?>
					location.href = "<?php echo $curUrl?>";
					<?php }?>
				}else{
					location.href = "<?php echo $curUrl?>";
				}
			}else if(res == "empty title"){
				jQuery("#error_box").show().html("에러 : 제목을 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#title").focus();
			}else if(res == "empty category"){
				jQuery("#error_box").show().html("에러 : 카테고리를 선택해주세요.");
				jQuery("#success_box").hide();
				jQuery("#category").focus();
			}else if(res == "empty writer"){
				jQuery("#error_box").show().html("에러 : 작성자를 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#writer").focus();
			}else if(res == "long writer"){
				jQuery("#error_box").show().html("에러 : 작성자는 16자 이하로 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#writer").focus();
			}else if(res == "empty pass"){
				jQuery("#error_box").show().html("에러 : 비밀번호를 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "long pass"){
				jQuery("#error_box").show().html("에러 : 비밀번호는 16자 이하로 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#pass").focus();
			}else if(res == "empty content"){
				jQuery("#error_box").show().html("에러 : 내용을 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#content").focus();
			}else if(res == "empty image_file"){
				jQuery("#error_box").show().html("에러 : 대표이미지를 선택해주세요.");
				jQuery("#success_box").hide();
			}else if(res == "empty string"){
				jQuery("#error_box").show().html("에러 : 자동생성방지 숫자를 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#string").focus();
			}else if(res == "auth error"){
				jQuery("#error_box").show().html("에러 : 자동생성방지 숫자를 정확히 입력해주세요.");
				jQuery("#success_box").hide();
				jQuery("#string").focus();
			}else if(res == "empty agree1"){
				jQuery("#error_box").show().html("에러 : 개인정보수집안내에 동의해주세요.");
				jQuery("#success_box").hide();
				jQuery("#agree1").focus();
			}else if(res == "filter error"){
				jQuery("#error_box").show().html("에러 : 제목 또는 내용에 사용할 수 없는 단어가 포함되어 있습니다.");
				jQuery("#success_box").hide();
			}else if(res == "image_file type error"){
				jQuery("#error_box").show().html("에러 : 선택하신 파일의 확장자는 업로드가 제한되어 있습니다. (대표이미지)");
				jQuery("#success_box").hide();
			}else if(res == "image_file byte error"){
				jQuery("#error_box").show().html("에러 : " + jQuery("#upload_size").val() + "MB 이상의 파일은 업로드 하실 수 없습니다. (대표이미지)");
				jQuery("#success_box").hide();
			}else if(res == "file type error"){
				jQuery("#error_box").show().html("에러 : 선택하신 파일의 확장자는 업로드가 제한되어 있습니다. (첨부파일)");
				jQuery("#success_box").hide();
			}else if(res == "file byte error"){
				jQuery("#error_box").show().html("에러 : " + jQuery("#upload_size").val() + "MB 이상의 파일은 업로드 하실 수 없습니다. (첨부파일)");
				jQuery("#success_box").hide();
			}else if(res == "password error"){
				jQuery("#error_box").show().html("에러 : 비밀번호가 일치하지 않습니다.");
				jQuery("#success_box").hide();
			}else{
				jQuery("#error_box").show().html("에러 : 게시물 저장에 실패하였습니다.");
				jQuery("#success_box").hide();
			}
		}
	}); 
	jQfrm.submit(); 
}

// 에러 메시지 초기화
function empty_error(){
	jQuery('#error_box').empty().hide();
} 
</script>
<form name="bbswrite" id="bbswrite" method="post" enctype="multipart/form-data">
<input type="hidden" name="page_id" id="page_id" value="<?php echo $page_id?>" />
<input type="hidden" name="bname" id="bname" value="<?php echo $bname?>" />
<input type="hidden" name="mode" id="mode" value="<?php echo $_VAR['mode']?>" />
<input type="hidden" name="upload_size" id="upload_size" value="<?php echo (int)$boardInfo->upload_size?>" />
<input type="hidden" name="search_chk" id="search_chk" value="<?php echo $_VAR['search_chk']?>" />
<input type="hidden" name="keyfield" id="keyfield" value="<?php echo $_VAR['keyfield']?>" />
<input type="hidden" name="keyword" id="keyword" value="<?php echo $_VAR['keyword']?>" />
<input type="hidden" name="cate" id="cate" value="<?php echo $_VAR['cate']?>" />
<input type="hidden" name="page" id="page" value="<?php echo $_VAR['page']?>" />
<input type="hidden" name="editor" id="editor" value="<?php echo $boardInfo->editor?>" />
<input type="hidden" name="sess_id" id="sess_id" value="<?php echo $currentSessionID?>" />
<?php if(!empty($tMode) && $tMode == "reply"){?>
<input type="hidden" name="ref" id="ref" value="<?php echo $brdData->ref?>" />
<input type="hidden" name="re_step" id="re_step" value="<?php echo $brdData->re_step?>" />
<input type="hidden" name="re_level" id="re_level" value="<?php echo $brdData->re_level?>" />
<input type="hidden" name="no" id="no" value="<?php echo $_VAR['no']?>" />
<?php }?>
<?php if(!empty($tMode) && $tMode == "modify"){?>
<input type="hidden" name="no" id="no" value="<?php echo $_VAR['no']?>" />
<?php }?>
<?php if(!empty($boardInfo->category_list) && count($category_arr) > 0){?>
<input type="hidden" name="use_category" id="use_category" value="1" />
<?php }?>
<?php 
if(empty($curUserPermision) || $curUserPermision == 'all'){
	if($tMode != "reply" || ($tMode == "reply" && $brdData->use_secret != "1")){
?>
<input type="hidden" name="validate_pass" id="validate_pass" value="1" />
<?php
	}
}
?>
<div id="bbse_board" style="width:<?php echo $table_width?>;<?php echo $table_align?>">
	<div id="success_box" style="font-size:15px;font-weight:bold;padding:10px 0 10px 0;color:#7ba8ea;display:none;"></div>
	<fieldset>
		<legend>Legend</legend>
		<div class="form_table">
			<table border="1" cellspacing="0" summary="게시판의 글쓰기">
			<colgroup>
				<col width="120" /><col />
			</colgroup>
			<tbody>
			<tr>
				<th scope="row">제목</th>
				<td>
					<div class="item">
						<input name="title" id="title" value="<?php if(!empty($title)) echo stripslashes($title)?>" title="제목" class="i_text" type="text" style="width:80%;" onkeyup="tag_check(this);empty_error();" />
					</div>
				</td>
			</tr>
			<?php if(!empty($boardInfo->category_list) && count($category_arr) > 0){?>
			<tr>
				<th scope="row">카테고리</th>
				<td>
					<div class="item">
						<fieldset class="cate">
							<legend>카테고리영역</legend>
							<?php echo $select_category?>
						</fieldset>
					</div>
				</td>
			</tr>
			<?php }?>
			<?php if((!empty($boardInfo->use_secret) && $boardInfo->use_secret == 1) || (!empty($boardInfo->use_notice) && $boardInfo->use_notice == 1)){?>
			<tr>
				<th scope="row">선택사항</th>
				<td>
					<div class="item">
						<?php if( !empty($boardInfo->use_secret) && $boardInfo->use_secret == 1 ){?>
						<input class="i_check" name="use_secret" id="use_secret" type="checkbox" value="1"<?php if((!empty($brdData->use_secret) && $brdData->use_secret == 1) && ($tMode == "modify" || $tMode == "reply")) echo " checked";?> /><label for="use_secret">비밀글</label> 
						<?php }?>
						<?php if((empty($curUserPermision) || $curUserPermision == 'administrator') && !empty($boardInfo->use_notice) && $boardInfo->use_notice == 1){?>
						<input class="i_check" name="use_notice" id="use_notice" type="checkbox" value="1"<?php if(!empty($brdData->use_notice) && $brdData->use_notice == 1) echo " checked";?> /><label for="use_notice">공지</label>
						<?php }?>
					</div>
				</td>
			</tr>
			<?php }?>
			<tr>
				<th scope="row">작성자</th>
				<td>
					<div class="item">
						<?php if(!empty($curUserPermision) && $curUserPermision == "author"){?>
						<?php echo $writer?>
						<?php }else{?>
						<input name="writer" id="writer" value="<?php if(!empty($writer)) echo $writer?>" title="작성자" class="i_text" type="text" onkeyup="tag_check(this);empty_error();" />
						<?php }?>
					</div>
				</td>
			</tr>
			<?php 
			if(empty($curUserPermision) || $curUserPermision == 'all'){
				if($tMode != "reply" || ($tMode == "reply" && $brdData->use_secret != "1")){
			?>
			<tr>
				<th scope="row">비밀번호</th>
				<td>
					<div class="item">
						<input name="pass" id="pass" title="비밀번호" class="i_text" type="password" onkeyup="tag_check(this);empty_error();" />
					</div>
				</td>
			</tr>
			<?php 
				}
			}else{
				if((!empty($curUserPermision) && $curUserPermision == 'administrator') && $tMode == "modify" && $brdData->memnum == 0){
			?>
			<tr>
				<th scope="row">비밀번호</th>
				<td>
					<div class="item">
						<input name="pass" id="pass" title="비밀번호" class="i_text" type="password" onkeyup="tag_check(this);" /> 변경시에만 입력
					</div>
				</td>
			</tr>
			<?php 
				}
			}
			?>
			<?php if(!empty($boardInfo->use_email) && $boardInfo->use_email == 1){?>
			<tr>
				<th scope="row">이메일</th>
				<td>
					<div class="item">
						<input name="email" id="email" value="<?php if(!empty($brdData->email)) echo stripslashes($brdData->email)?>" title="이메일" class="i_text" style="width:50%;" type="text" onkeyup="tag_check(this);" />
					</div>
				</td>
			</tr>
			<?php }?>
			<?php if(!empty($boardInfo->use_phone) && $boardInfo->use_phone == 1){?>
			<tr>
				<th scope="row">연락처</th>
				<td>
					<div class="item">
						<input name="phone" id="phone" value="<?php if(!empty($brdData->phone)) echo stripslashes($brdData->phone)?>" title="연락처" class="i_text" type="text" onkeyup="tag_check(this);" />
					</div>
				</td>
			</tr>
			<?php }?>
			<tr>
				<td colspan="2">
					<div class="item">
						<?php if(!empty($boardInfo->editor) && $boardInfo->editor == "N"){?>
						<textarea name="content" id="content" title="내용" class="i_text" style="width:95%;height:150px;" onkeyup="tag_check(this);empty_error();"><?php if(!empty($content)) echo stripslashes($content)?></textarea>
						<?php }else if(!empty($boardInfo->editor) && $boardInfo->editor == "W"){?>
						<?php
						if ( current_user_level() == 'administrator') $mediaBtn = true;
						else                                          $mediaBtn = false;
						$quicktags_settings = array('buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close');
						$editor_args = array(
							'textarea_name' => 'content',
							'textarea_rows' => 20,
							'media_buttons' => $mediaBtn,
							'quicktags'     => $quicktags_settings,
							'tinymce'       => array( 
									'content_css' => BBSE_BOARD_PLUGIN_WEB_URL.'css/editor-style.css'
							)
						);
						$wp_content = (!empty($content))? stripslashes($content) :"";
						wp_editor($wp_content, 'content1', $editor_args);
						?>
						<?php }?>
					</div>
				</td>
			</tr>
			<?php if(!empty($boardInfo->use_pds) && $boardInfo->use_pds == 1){?>
			<tr>
				<th scope="row">첨부파일1</th>
				<td>
					<div class="item">
						<input name="file1" id="file1" title="첨부파일1" type="file" />
						<?php if(!empty($view_file1)) echo $view_file1?>
					</div>
				</td>
			</tr>
			<tr>
				<th scope="row">첨부파일2</th>
				<td>
					<div class="item">
						<input name="file2" id="file2" title="첨부파일2" type="file" />
						<?php if(!empty($view_file2)) echo $view_file2?>
					</div>
				</td>
			</tr>
			<?php }?>
      <?php if(!empty($boardInfo->using_subAutoWtype) && $boardInfo->using_subAutoWtype != 'NO'){?>
			<tr>
				<th scope="row">자동생성방지</th>
				<td class="preventSpam">
					<?php if($boardInfo->using_subAutoWtype == 'GD'){?>
						<img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>class/auth_img.php?sid=<?php echo $currentSessionID?>" style="float:left" ondrag="this.blur();" alt="자동생성방지" />
						<div class="capchaBox">
						자동생성방지를 위해 왼쪽에 보이는 숫자를 입력하세요.<br><br>
						<input title="보안번호" class="i_text" type="text" name="string" id="string" value="" onfocus="this.value='';return true;" onkeyup="tag_check(this);empty_error();" />
						</div>
					<?php } else {
						echo '자동생성방지를 위해'.$fontColorType.'숫자를 입력하세요';
					?>
						<div class="capchaBox">
							<span><?php echo $auth_fulltxt;?></span>
							<input title="보안번호" class="i_text" type="text" name="string" id="string" value="" onfocus="this.value='';return true;" onkeyup="tag_check(this);empty_error();" />
						</div>
					<?php }?>
				</td>
			</tr>
			<?php }?>
			<?php if((empty($curUserPermision) || $curUserPermision == 'all') && (empty($config->use_private) || $config->use_private == "Y")){?>
			<tr>
				<th scope="row">개인정보 수집 및<br />활용동의</th>
				<td>
					<div class="item">
						<div class="agree1"><?php if(!empty($prvCnfData->cnf_contents)) echo nl2br($prvCnfData->cnf_contents)?></div>
						<input type="checkbox" name="agree1" id="agree1" value="1" class="i_check" onclick="if(this.checked == true) empty_error();" />&nbsp;<label for="agree1">개인정보수집안내를 읽었으며 내용에 동의합니다.</label>
					</div>
				</td>
			</tr>
			<?php }?>
			</tbody>
			</table>
		</div>
	</fieldset>
	<p class="open_meg" id="error_box" style="display:none;"></p>
	<div class="bbse_board_foot">
		<?php require_once(BBSE_BOARD_PLUGIN_ABS_PATH."skin/".$boardInfo->skinname."/powered.php");?>
		<div class="btn">
			<a class="btn_big" href="javascript:;" onclick="write_check('<?php echo $action_url?>proc/write.exec.php');"><strong><?php if(!empty($tMode) && $tMode == "modify") echo "수정";else echo "글쓰기";?></strong></a>		
			<a class="btn_big" href="<?php echo $curUrl?>"><span>목록</span></a>
		</div>
	</div>
</div>
</form>
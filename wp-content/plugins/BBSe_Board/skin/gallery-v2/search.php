<script type="text/javascript">
// 게시물 저장 및 파일업로드 이벤트
function search_check(tUrl){
	var jQfrm = jQuery("#board_search_frm"); 

	jQfrm.attr("action", tUrl);
	jQfrm.ajaxForm(
		function(data, state){
			if(state == "success"){
				location.href = "<?php echo $curUrl.$link_add?>nType=" + data;
			}else{
				alert("게시물 검색에 실패하였습니다.");
				return false;
			}
		}
	); 
	jQfrm.submit(); 
}

function key_chk(val){
	var key = event.keyCode;
	if(key == 13){
		search_check(val);
	}
}
</script>
<div id="bbse_board" style="width:<?php echo $table_width?>;<?php echo $table_align?>">
	<div class="list_top">
		<fieldset class="cate">
			<legend>카테고리영역</legend>
			<?php echo $select_category?>
		</fieldset>
		<fieldset class="srch">
			<legend>검색영역</legend>
			<form action="" method="post" name="board_search_frm" id="board_search_frm" onsubmit="return false;">
			<input type="hidden" name="bname" value="<?php echo $bname?>" />
			<input type="hidden" name="page" value="<?php echo $_VAR['page']?>" />
			<input type="hidden" name="cate" value="<?php echo $_VAR['cate']?>" />
			<input type="hidden" name="search_chk" value="1" />
			<select name="keyfield">
				<option value="title"<?php if($_VAR['keyfield'] == "title") echo " selected";?>>제목</option>
				<option value="content"<?php if($_VAR['keyfield'] == "content") echo " selected";?>>내용</option>
				<option value="writer"<?php if($_VAR['keyfield'] == "writer") echo " selected";?>>작성자</option>
			</select>
			<input title="검색어" class="keyword" accesskey="s" type="text" name="keyword" value="<?php echo stripslashes(esc_html($_VAR['keyword']))?>" onkeypress="key_chk('<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/search.exec.php');" />
			<a href="javascript:;" onclick="search_check('<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/search.exec.php');"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>skin/<?php echo $boardInfo->skinname?>/images/btn_search.gif" alt="검색" border="0" align="absmiddle" /></a>
			</form>
		</fieldset>
	</div>
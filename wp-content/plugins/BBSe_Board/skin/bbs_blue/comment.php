	<script type="text/javascript">
	function comment_check(ajax_act, mode){
		var jQfrm = jQuery("#comment_frm");

		if(mode != "edit" && mode != "reply") jQuery("#cno").val("");
		else jQuery("#cno").val(jQuery("#sub_cno").val());

		if(mode) jQuery("#cmode").val(mode);

		jQfrm.attr("action", ajax_act);
		jQfrm.ajaxForm({
			type:"POST",
			async:true,
			crossDomain:true,
			xhrFields:{withCredentials:true},
			success:function(data, state){
				var data_arr = data.split("|||");
				var res = data_arr[0];
				var type = data_arr[1];

				if(type == 1){
					var error_box = "#error_box";
					var cname = "#cname";
					var cpass = "#cpass";
					var cstring = "#string";
					var cmemo = "#cmemo";
				}else if(type == 2){
					var error_box = "#reply_error_box";
					var cname = "#reply_cname";
					var cpass = "#reply_cpass";
					var cstring = "#reply_string";
					var cmemo = "#reply_cmemo";
				}else if(type == 3){
					var error_box = "#edit_error_box";
					var cname = "#edit_cname";
					var cpass = "#edit_cpass";
					var cstring = "#edit_string";
					var cmemo = "#edit_cmemo";
				}

				if(res == "success"){
					jQuery("#pass_frm").submit();
				}else if(res == "not permission"){
					jQuery(error_box).show().html("에러 : 댓글쓰기 권한이 없습니다.");
				}else if(res == "fail string"){
					jQuery(error_box).show().html("에러 : 입력된 인증번호가 유효하지 않습니다.");
				}else if(res == "empty cname"){
					jQuery(error_box).show().html("에러 : 작성자를 입력해주세요.");
					jQuery(cname).focus();
				}else if(res == "long cname"){
					jQuery(error_box).show().html("에러 : 작성자는 16자 이하로 입력해주세요.");
					jQuery(cname).focus();
				}else if(res == "empty cpass"){
					jQuery(error_box).show().html("에러 : 비밀번호를 입력해주세요.");
					jQuery(cpass).focus();
				}else if(res == "long cpass"){
					jQuery(error_box).show().html("에러 : 비밀번호는 16자 이하로 입력해주세요.");
					jQuery(cpass).focus();
				}else if(res == "empty string"){
					jQuery(error_box).show().html("에러 : 자동생성방지 숫자를 입력해주세요.");
					jQuery(cstring).focus();
				}else if(res == "empty cmemo"){
					jQuery(error_box).show().html("에러 : 내용을 입력해주세요.");
					jQuery(cmemo).focus();
				}else{
					jQuery(error_box).show().html("에러 : 댓글 저장에 실패하였습니다.");
				}
			}
		});
		jQfrm.submit();
	}

	// 에러 메시지 초기화
	function empty_error(id){
		if(id == 1){
			jQuery('#error_box').empty().hide();
		}else if(id == 2){
			jQuery('#reply_error_box').empty().hide();
		}else if(id == 3){
			jQuery('#edit_error_box').empty().hide();
		}
	}
	</script>
	<div class="comment_group">
		<form name="comment_frm" id="comment_frm" method="post">
		<input type="hidden" name="page_id" id="page_id" value="<?php echo $page_id?>" />
		<input type="hidden" name="bname" id="bname" value="<?php echo $bname?>" />
		<input type="hidden" name="mode" id="mode" value="<?php echo $_VAR['mode']?>" />
		<input type="hidden" name="search_chk" id="search_chk" value="<?php echo $_VAR['search_chk']?>" />
		<input type="hidden" name="keyfield" id="keyfield" value="<?php echo $_VAR['keyfield']?>" />
		<input type="hidden" name="keyword" id="keyword" value="<?php echo $_VAR['keyword']?>" />
		<input type="hidden" name="cate" id="cate" value="<?php echo $_VAR['cate']?>" />
		<input type="hidden" name="page" id="page" value="<?php echo $_VAR['page']?>" />
		<input type="hidden" name="no" id="no" value="<?php echo $_VAR['no']?>" />
		<input type="hidden" name="cmode" id="cmode" value="" />
		<input type="hidden" name="cno" id="cno" value="" />
		<input type="hidden" name="sub_cno" id="sub_cno" value="" />
		<input type="hidden" name="sess_id" id="sess_id" value="<?php echo $currentSessionID?>" />
		<?php echo $cert?>
		<fieldset>
			<p class='total'>전체 <span class="bold red"><?php echo number_format($comment_cnt)?></span> 개</p>

			<div class="comment_box">
				<div class="input_left">
					<ul>
						<?php if(empty($curUserPermision) || $curUserPermision == 'all'){?>
						<li><input title="작성자" class="name" type="text" name="cname" id="cname" value="" onkeyup="tag_check(this);empty_error(1);" placeholder="작성자" /></li>
						<li><input title="비밀번호" class="name" type="password" name="cpass" id="cpass" value="" onkeyup="tag_check(this);empty_error(1);" placeholder="비밀번호" /></li>
						<?php }else{?>
						<li class="haveName"><?php if(!empty($current_user->user_firstname) && !empty($current_user->user_lastname)) echo $current_user->user_lastname." ".$current_user->user_firstname; else echo $current_user->user_login;?></li>
						<?php }?>
					</ul>
				</div>
				<?php if(!empty($boardInfo->using_subAutoWtype) && $boardInfo->using_subAutoWtype != 'NO'){?>
				<div class="preventSpam">
					<?php if($boardInfo->using_subAutoWtype == 'GD'){?>
						<img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>class/auth_img.php?sid=<?php echo $currentSessionID?>" style="float:left" ondrag="this.blur();" alt="자동생성방지" />
						<div class="capchaBox">
						자동생성방지를 위해 왼쪽에 보이는 숫자를 입력하세요.
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
				</div>
				<?php }?>
				<div class="input_comment">
					<textarea class="comment" name="cmemo" id="cmemo" rows="5" cols="65" onkeyup="tag_check(this);empty_error(1);"></textarea><input title="입력" class="submit" type="button" value="입력" onclick="comment_check('<?php echo $action_url?>proc/comment_write.exec.php', '');" />
				</div>
				<p class="open_meg" id="error_box" style="display:none;"></p>
			</div>

			<!-- 댓글 목록 -->
			<?php if(!empty($comment_cnt) && $comment_cnt > 0){?>
			<?php echo $comment_list?>
			<?php }else{?>
			<br />
			<?php }?>
		</fieldset>
		</form>
	</div>
	<form name="pass_frm" id="pass_frm" method="post">
	<input type="hidden" name="passcheck" value="1" />
	</form>
	<?php
	/* 댓글 수정 입력폼 */
	$comment_edit_view = "<div class='comment_box'><div class='input_left'><ul>";

	if(!empty($curUserPermision) && $curUserPermision == 'administrator'){
		$comment_edit_view .= "<li><input title='작성자' class='name' type='text' name='edit_cname' id='edit_cname' value='' onkeyup='tag_check(this);empty_error(3);' placeholder='작성자' /></li>";
	}else{
		$comment_edit_view .= "<li id='edit_cname'></li>";
	}

	$comment_edit_view .= "</ul></div>";

	if(!empty($boardInfo->using_subAutoWtype) && $boardInfo->using_subAutoWtype != 'NO'){
		$comment_edit_view .= "<div class='preventSpam'>";

		if($boardInfo->using_subAutoWtype == 'GD'){
			$comment_edit_view .= "자동생성방지를 위해 왼쪽에 보이는 숫자를 입력하세요.";
		}else if($boardInfo->using_subAutoWtype == 'TXT'){
			$comment_edit_view .= "자동생성방지를 위해 ".$fontSubColorType." 숫자를 입력하세요.";
		}

		$comment_edit_view .= "<div class=\"capchaBox\"><span>";

		if($boardInfo->using_subAutoWtype == 'GD'){
			$comment_edit_view .= "<img src='".BBSE_BOARD_PLUGIN_WEB_URL."class/auth_img_sub.php?sid=".$currentSessionID."' style='float:left' ondrag='this.blur();' alt='자동생성방지' />";
		}else if($boardInfo->using_subAutoWtype == 'TXT'){
			$comment_edit_view .= $auth_sub_fulltxt;
		}

		$comment_edit_view .= "</span><input title='보안번호' class='name' name='edit_string' id='edit_string' type='text' value='' onkeyup='tag_check(this);empty_error(3);' /></div></div>";
	}

	$comment_edit_view .= "<div class='input_comment'><textarea class='comment' name='edit_cmemo' id='edit_cmemo' rows='5' cols='65' onkeyup='tag_check(this);empty_error(3);'></textarea><input title='입력' class='submit' type='button' value='입력' onclick=\"comment_check('".$action_url."proc/comment_write.exec.php', 'edit');\" /></div><p class='open_meg' id='edit_error_box' style='display:none;'></p></div>";

	$comment_edit_view = str_replace('"', '\"', $comment_edit_view);

	/* 댓글의 댓글 입력폼 */
	$comment_reply_view = "<div class='comment_box'><div class='input_left'><ul>";

	if(empty($curUserPermision) || $curUserPermision == 'all'){
		$comment_reply_view .= "<li><input title='작성자' class='name' type='text' name='reply_cname' id='reply_cname' value='' onkeyup='tag_check(this);empty_error(2);' placeholder='작성자' /></li><li><input title='비밀번호' class='name' type='password' name='reply_cpass' id='reply_cpass' value='' onkeyup='tag_check(this);empty_error(2);' placeholder='비밀번호' /></li>";
	}else{
		$comment_reply_view .= "<li>";
		if(!empty($current_user->user_firstname) && !empty($current_user->user_lastname)){
			$comment_reply_view .= $current_user->user_lastname.' '.$current_user->user_firstname;
		}else{
			$comment_reply_view .= $current_user->user_login;
		}
		$comment_reply_view .= "</li>";
	}

	$comment_reply_view .= "</ul></div>";

	if(!empty($boardInfo->using_subAutoWtype) && $boardInfo->using_subAutoWtype != 'NO'){
		$comment_reply_view .= "<div class='input_right'><p>";

		if($boardInfo->using_subAutoWtype == 'GD'){
			$comment_reply_view .= "<img src='".BBSE_BOARD_PLUGIN_WEB_URL."class/auth_img_sub.php?sid=".$currentSessionID."' style='float:left' ondrag='this.blur();' alt='자동생성방지' />";
		}else if($boardInfo->using_subAutoWtype == 'TXT'){
			$comment_reply_view .= "<div style='float:left;background:#ebebeb;padding:10px 5px 10px 5px;'>".$auth_sub_fulltxt."</div>";
		}

		$comment_reply_view .= "</p><p class='txt'>";
		if($boardInfo->using_subAutoWtype == 'GD'){
			$comment_reply_view .= "자동생성방지를 위해 왼쪽에 보이는 숫자를 입력하세요.";
		}else if($boardInfo->using_subAutoWtype == 'TXT'){
			$comment_reply_view .= "자동생성방지를 위해 ".$fontSubColorType." 숫자를 입력하세요.";
		}
		$comment_reply_view .= "</p><p><input title='보안번호' class='name' name='reply_string' id='reply_string' type='text' value='' onkeyup='tag_check(this);empty_error(2);' /></p></div>";
	}

	$comment_reply_view .= "<div class='input_comment'><textarea class='comment' name='reply_cmemo' id='reply_cmemo' rows='5' cols='65' onkeyup='tag_check(this);empty_error(2);'></textarea><input title='입력' class='submit' type='button' value='입력' onclick=\"comment_check('".$action_url."proc/comment_write.exec.php', 'reply');\" /></div><p class='open_meg' id='reply_error_box' style='display:none;'></p></div>";

	$comment_reply_view = str_replace('"', '\"', $comment_reply_view);
	?>
	<script type="text/javascript">
	// 회원 or 비회원 : 비회원이 쓴 댓글 비밀번호 확인
	function pass_confirm(obj, cno, mode){
		jQuery("#cno").val(cno);
		jQuery("#sub_cno").val(cno);
		var obj_id = obj.attr("id");

		// 댓글 영역
		obj.parent().parent().children("div").remove();
		obj.parent().parent().siblings("li").children("div").remove();

		// 삭제 영역
		obj.parent().parent().children("p").children("div").remove();
		obj.parent().parent().siblings("li").children("p").children("div").remove();

		if(mode == "delete"){
			obj.parent().append("<div class='del_input'><span class='tit'>비밀번호입력 </span><input title='비밀번호입력' type='password' name='comment_del_pass' id='comment_del_pass' value='' onkeydown=\"jQuery('#meg').empty().hide();\" /><a href='javascript:;' onclick=\"comment_all_delete_check('<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/delete_comment.exec.php');\"><span class='btn_ok'>확인</span></a><p class='meg' id='meg' style='display:none;'></p></div>");
		}else if(mode == "edit"){
			obj.parent().append("<div class='del_input'><span class='tit'>비밀번호입력 </span><input title='비밀번호입력' type='password' name='comment_edit_pass' id='comment_edit_pass' value='' onkeydown=\"jQuery('#meg').empty().hide();\" /><a href='javascript:;' onclick=\"comment_edit_check('<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/comment_pass_check.exec.php', " + cno + ", '" + obj_id + "');\"><span class='btn_ok'>확인</span></a><p class='meg' id='meg' style='display:none;'></p></div>");
		}
	}

	// 회원 or 비회원 : 비회원이 쓴 댓글 수정
	function comment_edit_check(ajax_act, cno, obj){
		jQuery.ajax({
			url: ajax_act,
			type: "post",
			data: {bname:jQuery("#bname").val(), cno:jQuery("#cno").val(), pwd:jQuery("#comment_edit_pass").val()},
			beforeSend: function(){},
			complete: function(){},
			success: function(data){
				var data_arr = data.split("|||");
				var res = data_arr[0];

				if(res == "success"){
					comment_edit2(cno, obj);
				}else if(res == "empty password"){
					jQuery('#meg').show().html("에러 : 비밀번호를 입력해주세요.");
				}else if(res == "password error"){
					jQuery('#meg').show().html("에러 : 비밀번호가 일치하지 않습니다.");
				}else{
					jQuery('#meg').show().html("에러 : 불러오기 실패");
				}
			},
			error: function(data, status, err){
				var errorMessage = err || data.statusText;
				alert(errorMessage);
			}
		});
	}

	// 회원 or 비회원 : 비회원이 쓴 댓글 삭제
	function comment_all_delete_check(ajax_act){
		jQuery.ajax({
			url: ajax_act,
			type: "post",
			data: {bname:jQuery("#bname").val(), cno:jQuery("#cno").val(), pwd:jQuery("#comment_del_pass").val()},
			beforeSend: function(){},
			complete: function(){},
			success: function(data){
				var data_arr = data.split("|||");
				var res = data_arr[0];

				if(res == "success"){
					jQuery("#pass_frm").submit();
				}else if(res == "empty password"){
					jQuery('#meg').show().html('에러 : 비밀번호를 입력해주세요.');
				}else if(res == "password error"){
					jQuery('#meg').show().html('에러 : 비밀번호가 일치하지 않습니다.');
				}else{
					jQuery('#meg').show().html('에러 : 삭제에 실패하였습니다.');
				}
			},
			error: function(data, status, err){
				var errorMessage = err || data.statusText;
				alert(errorMessage);
			}
		});
	}

	// 관리자 or 작성자 : 댓글 삭제
	function comment_user_delete_check(ajax_act, cno, obj){
		jQuery("#cno").val(cno);
		jQuery("#sub_cno").val(cno);

		// 삭제 영역
		obj.parent().parent().children("p").children("div").remove();
		obj.parent().parent().siblings("li").children("p").children("div").remove();

		// 댓글 영역
		obj.parent().parent().children("div").remove();
		obj.parent().parent().siblings("li").children("div").remove();

		if(confirm("삭제하시겠습니까?")){
			jQuery.ajax({
				url: ajax_act,
				type: "post",
				data: {bname:jQuery("#bname").val(), cno:jQuery("#cno").val()},
				beforeSend: function(){},
				complete: function(){},
				success: function(data){
					var data_arr = data.split("|||");
					var res = data_arr[0];

					if(res == "success"){
						jQuery("#pass_frm").submit();
					}else{
						obj.parent().parent().children("p:last").append("<div class='del_input'><p class='meg' id='meg'>에러 : 삭제에 실패하였습니다.</p></div>");
					}
				},
				error: function(data, status, err){
					var errorMessage = err || data.statusText;
					alert(errorMessage);
				}
			});
		}
	}

	// 대댓글 입력폼 활성
	function comment_reply(cno, obj){
		jQuery("#cno").val(cno);
		jQuery("#sub_cno").val(cno);
		var reply_view = "<?php echo $comment_reply_view?>";

		// 삭제 영역
		obj.parent().parent().children("p").children("div").remove();
		obj.parent().parent().siblings("li").children("p").children("div").remove();

		// 댓글 영역
		obj.parent().parent().children("div").remove();
		obj.parent().parent().siblings("li").children("div").remove();

		obj.parent().parent().append(reply_view);
	}

	// 댓글 수정폼 활성
	function comment_edit1(cno, obj){
		jQuery("#cno").val(cno);
		jQuery("#sub_cno").val(cno);
		var edit_view = "<?php echo $comment_edit_view?>";

		// 삭제 영역
		obj.parent().parent().children("p").children("div").remove();
		obj.parent().parent().siblings("li").children("p").children("div").remove();

		// 댓글 영역
		obj.parent().parent().children("div").remove();
		obj.parent().parent().siblings("li").children("div").remove();

		jQuery.ajax({
			url: "<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/comment_load.exec.php",
			type: "post",
			data: {bname:jQuery("#bname").val(), cno:jQuery("#cno").val()},
			beforeSend: function(){},
			complete: function(){},
			success: function(data){
				var data_arr = data.split("|||");
				var res = data_arr[0];

				if(res == "success"){
					obj.parent().parent().append(edit_view);
					<?php if(!empty($curUserPermision) && $curUserPermision == 'administrator'){?>
					jQuery("#edit_cname").val(data_arr[1]);
					<?php }else{?>
					jQuery("#edit_cname").html(data_arr[1]);
					<?php }?>
					jQuery("#edit_cmemo").val(data_arr[2]);

				}else{
					obj.parent().parent().children("p:last").append("<div class='del_input'><p class='meg' id='meg'>에러 : 불러오기 실패</p></div>");
				}
			},
			error: function(data, status, err){
				var errorMessage = err || data.statusText;
				alert(errorMessage);
			}
		});
	}

	// 댓글 수정폼 활성 (비밀번호 입력후)
	function comment_edit2(cno, obj){
		jQuery("#cno").val(cno);
		jQuery("#sub_cno").val(cno);
		var edit_view = "<?php echo $comment_edit_view?>";
		if(!obj) return;
		else obj = jQuery("#" + obj);

		// 삭제 영역
		obj.parent().parent().children("p").children("div").remove();
		obj.parent().parent().siblings("li").children("p").children("div").remove();

		// 댓글 영역
		obj.parent().parent().children("div").remove();
		obj.parent().parent().siblings("li").children("div").remove();

		jQuery.ajax({
			url: "<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/comment_load.exec.php",
			type: "post",
			data: {bname:jQuery("#bname").val(), cno:jQuery("#cno").val()},
			beforeSend: function(){},
			complete: function(){},
			success: function(data){
				var data_arr = data.split("|||");
				var res = data_arr[0];

				if(res == "success"){
					obj.parent().parent().append(edit_view);
					<?php if(!empty($curUserPermision) && $curUserPermision == 'administrator'){?>
					jQuery("#edit_cname").val(data_arr[1]);
					<?php }else{?>
					jQuery("#edit_cname").html(data_arr[1]);
					<?php }?>
					jQuery("#edit_cmemo").val(data_arr[2]);

				}else{
					obj.parent().parent().children("p:last").append("<div class='del_input'><p class='meg' id='meg'>에러 : 불러오기 실패</p></div>");
				}
			},
			error: function(data, status, err){
				var errorMessage = err || data.statusText;
				alert(errorMessage);
			}
		});
	}

	// 댓글 에러
	function comment_fail(obj, mode){
		// 삭제 영역
		obj.parent().parent().children("p").children("div").remove();
		obj.parent().parent().siblings("li").children("p").children("div").remove();

		// 댓글 영역
		obj.parent().parent().children("div").remove();
		obj.parent().parent().siblings("li").children("div").remove();

		if(mode == "reply") var error_msg = "댓글의 댓글까지만 등록 가능합니다.";
		else if(mode == "edit") var error_msg = "사용권한이 없습니다.";
		else if(mode == "delete") var error_msg = "삭제권한이 없습니다.";
		else if(mode == "child") var error_msg = "댓글이 있을 경우 삭제가 불가능합니다.";
		obj.parent().parent().children("p:last").append("<div class='del_input'><p class='meg' id='meg'>에러 : " + error_msg + "</p></div>");
	}
	</script>
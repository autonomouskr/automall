<?php /*
추가필드 사용내역
ext_var1 : v1.5.0/160811, 지정 사용자 비밀글 열람 허용기능
ext_var2 :
ext_var3 :
ext_var4 :
ext_var5 :
ext_var6 :
ext_var7 :
ext_var8 :
ext_var9 :
ext_var10 :
*/ ?>
<style>
.board_add {background:#fff;padding:15px;}
.board_add td {border-bottom:1px dotted #ccc;padding:10px 0;}
.board_add_title{padding:10px !important;color:#11568b;background:#f7f7f7;border-top:3px solid #8fa6c6;border-bottom:1px solid #ddd !important;}
.info_btn {
	display:inline-block;
	padding:1px 5px 0;
	height:20px;
	line-height:20px;
	font-weight:normal;
	color:#777777;
	cursor:pointer;
	text-shadow:1px 1px 0px #ffffff;
	border:1px solid #dcdcdc;
	border-radius:3px;
	background : -webkit-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(224, 224, 224) 100%);
	background : -moz-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(224, 224, 224) 100%);
	background : -o-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(224, 224, 224) 100%);
	background : -ms-linear-gradient(top, rgb(255, 255, 255) 00%, rgb(224, 224, 224) 100%);
	background : -linear-gradient(top, rgb(255, 255, 255) 00%, rgb(224, 224, 224) 100%);
}
.info_btn:hover {color:#000000 !important;border:1px solid #666666;}
</style>
<script>
// 설정 제한
function set_limits(formtype){
	if(!formtype){
		return;
	}else{
		// 기본형
		if(formtype == "1"){

			jQuery("#use_reply").attr("disabled", false);














		}else{

			jQuery("#use_reply").attr("checked", false);
			jQuery("#use_reply").attr("disabled", true);


		}
	}
}

// 형태/스킨 선택시 select 자동 변경
function btype_check(mode){
	if(!mode){
		return;
	}else{
		if(mode == "formtype"){
			if(jQuery("#formtype").val() == "1"){
				if(jQuery("#skinname option[value='bbs_gray']").length > 0)  jQuery("#skinname").val("bbs_gray");
			}else if(jQuery("#formtype").val() == "2"){
				if(jQuery("#skinname option[value='webzine']").length > 0)  jQuery("#skinname").val("webzine");
			}else if(jQuery("#formtype").val() == "3"){
				if(jQuery("#skinname option[value='gallery']").length > 0)  jQuery("#skinname").val("gallery");
			}
			set_limits(jQuery("#formtype").val());



		}else if(mode == "skinname"){
			var skinname_arr = jQuery("#skinname").val().split("_");

			if(skinname_arr[0] == "bbs"){
				jQuery("#formtype").val("1");
			}else if(skinname_arr[0] == "webzine"){
				jQuery("#formtype").val("2");
			}else if(skinname_arr[0] == "gallery"){
				jQuery("#formtype").val("3");
			}


			set_limits(jQuery("#formtype").val());
		}
	}
}

// display 노출/숨김
function styleView(name){
	var fieldName = eval('document.setup.' + name);
	var styleName = eval(name + 'Style.style');
	if(fieldName.checked == true){
		styleName.display = '';
	}else{
		styleName.display = 'none';
	}
}






























// 카카오톡 사용함 선택시 카카오 앱키 입력 활성
function use_kakaotalk_change(){
	var frm = document.setup;
	if(frm.use_kakaotalk.checked == true){
		document.getElementById("kakao_app_key_tr").style.display = "";
	}else{
		document.getElementById("kakao_app_key_tr").style.display = "none";
	}
}

function setup_submit(dbType, action_url){
	var frm = document.setup;
	var chkUrl = "<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>admin/board_config_check.php";
	var tMode = "chkBoardName";
	var tBoardName = jQuery("#boardname").val();
	var tBoardNo = jQuery("#board_no").val();
	var dbTypeName = "";
	var space = /\s/;
	var Alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var Digit = '1234567890';
	var namecheck = 0;
	var i;
	var munja = Alpha + Digit;

	if(dbType == 'insert') dbTypeName = "추가";
	else dbTypeName = "수정";

	if(!tBoardName){
		alert("게시판 이름을 입력해주세요.");
		jQuery("#boardname").focus();
		return false;
	}
	if(tBoardName.length > 20){
		alert("게시판 이름을 20자 이내로 입력해주세요.");
		jQuery("#boardname").focus();
		return false;
	}
	if(tBoardName == "admin"){
		alert("'admin'은 게시판 이름으로 사용할수 없습니다.");
		jQuery("#boardname").focus();
		return false;
	}
	if(space.exec(tBoardName)){
		alert("게시판 이름에는 공백을 사용할 수 없습니다.");
		jQuery("#boardname").focus();
		return false;
	}
	for(i = 0; i < tBoardName.length; i++){
		if(munja.indexOf(tBoardName.charAt(i)) == -1){
			namecheck = namecheck + 1;
			break;
		}
	}
	if(namecheck > 0){
		alert("게시판 이름은 영문자와 숫자만 가능합니다.");
		jQuery("#boardname").focus();
		return false;
	}
	jQuery.ajax({
		type: 'post',
		async: false,
		url: chkUrl,
		data: {tMode:tMode, tBoardName:tBoardName, tBoardNo:tBoardNo},
		success: function(data){
			data = data.trim();
			var response = data;

			// 분기 처리
			if(response == "success"){
				if(!frm.formtype.value){
					alert("형태 설정을 선택해주세요.");
					frm.formtype.focus();
					return false;
				}
				if(!frm.skinname.value){
					alert("스킨 설정을 선택해주세요.");
					frm.skinname.focus();
					return false;
				}
				if(!frm.table_width.value){
					alert("가로 사이즈를 입력해주세요.");
					frm.table_width.focus();
					return false;
				}
				if(frm.table_align[0].checked == false && frm.table_align[1].checked == false && frm.table_align[2].checked == false){
					alert("정렬 위치를 선택해주세요.");
					return false;
				}
				if(frm.formtype.value == "bbs" || frm.formtype.value == "webzine"){
					if(!frm.page_size.value){
						alert("페이지당 목록 수를 입력해주세요.");
						frm.page_size.focus();
						return false;
					}
				}
				if(frm.use_pds.checked == true){
					if(!frm.upload_size.value){
						alert("첨부파일 용량을 입력해주세요.");
						frm.upload_size.focus();
						return false;
					}
					if(frm.upload_size.value == 0 || frm.upload_size.value > 5){
						alert("첨부파일 용량은 1 ~ 5 사이의 숫자만 입력가능합니다.");
						frm.upload_size.focus();
						return false;
					}
				}
				if(frm.use_filter.checked == true){
					if(!frm.filter_list.value){
						alert("필터링목록을 입력해주세요.");
						frm.filter_list.focus();
						return false;
					}
				}
				if(frm.use_kakaotalk.checked == true){
					if(!frm.kakao_app_key.value){
						alert("카카오 앱키를 입력해주세요.");
						frm.kakao_app_key.focus();
						return false;
					}
				}

















				if(confirm("게시판을 " + dbTypeName + "하시겠습니까?")){
					frm.action = action_url;
					frm.submit();
				}
			}else if(response == "existName"){
				alert("이미 등록된 게시판 이름입니다. 다른 이름을 사용해주세요.");
				jQuery("#boardname").focus();
			}else if(response == "usedAdmin"){
				alert("'admin'은 게시판 이름으로 사용할 수 없습니다.");
				jQuery("#boardname").focus();
			}else if(response == "usedBlank"){
				alert("게시판 이름에는 공백을 사용할 수 없습니다.");
				jQuery("#boardname").focus();
			}else if(response == "over20"){
				alert("게시판 이름을 20자 이내로 입력해주세요.");
				jQuery("#boardname").focus();
			}else{
				alert("서버와의 통신이 실패했습니다.");
			}
		},
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.");
		}
	});
}

jQuery(document).ready(function(){
	jQuery(".only_num").css("ime-mode", "disabled").keypress(function(event){
		if(event.which && (event.which < 48 || event.which > 57)){
			event.preventDefault();
        }
    }).keyup(function(){
		if(jQuery(this).val() != null && jQuery(this).val() != ""){
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ""));
        }
    });
});
</script>
<?php
if(empty($board_no)) $mode = "추가";
else $mode = "수정";
?>
<div class="wrap">
	<?php
	if(!empty($tMode)){
		echo '<div id="message" class="updated fade"><p><strong>게시판 정보를 정상적으로 저장하였습니다.</strong></p></div>';
	}
	?>
	<div id="bbse_box">
		<div class="inner">
			<div class="guide_top">
				<span class="tl"></span><span class="tr"></span><span class="manual_btn"><a href="http://manual.bbsetheme.com/bbse-board" target="_blank"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>images/btn_manual.png" /></a></span>
				<a href="#"><span class="logo">BBS</span><span class="logo_board">e-Board</span><span class="logo_version"><?php echo BBSE_BOARD_VER?></span></a>
			</div>
			<div id="content">
				<!-- 내용 start -->
				<form name="setup" method="post">
				<input type="hidden" name="board_no" id="board_no" value="<?php if(!empty($board_no)) echo $board_no?>">
				<div class="tit">게시판 <?php echo $mode?></div>
				<ul class="form_ul1">
					<li>
						<div class="form_stitle">기본설정</div>
						<div class="form_content">
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">게시판 이름&nbsp;</div>
								<div style="float:left;">
									<?php
									if(!empty($data->boardname) && !empty($board_no)){
										echo "<b>".$data->boardname."</b><input type='hidden' name='boardname' id='boardname' value='".$data->boardname."'>";

									}else{
										echo "<input type='text' name='boardname' id='boardname' style='width:200px;' maxlength='20' value=''>";
									}
									?>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">형태 설정&nbsp;</div>
								<div style="float:left;">
									<select name="formtype" id="formtype" onchange="btype_check('formtype');">
										<option value="1"<?php if(empty($data->formtype) || $data->formtype == 1) echo " selected";?>>기본형(텍스트 목록형)</option>
										<option value="2"<?php if(!empty($data->formtype) && $data->formtype == 2) echo " selected";?>>웹진형(이미지+텍스트 목록형)</option>
										<option value="3"<?php if(!empty($data->formtype) && $data->formtype == 3) echo " selected";?>>갤러리형(이미지 목록형)</option>

									</select>
									<p>대표이미지를 사용하지 않는 갤러리 게시판을 원하시면 <b>기본형(텍스트 목록형)</b>으로 선택하세요.</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">카테고리목록&nbsp;</div>
								<div style="float:left;">
									<input type="text" name="category_list" style="width:450px;" value="<?php if(!empty($data->category_list)) echo $data->category_list;?>">
									<p>카테고리를 설정하려면 입력하세요. 여러 값 입력시 공백없이 콤마(,)로 구분</p>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="form_stitle">디자인설정</div>
						<div class="form_content">
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">스킨 설정&nbsp;</div>
								<div style="float:left;">
									<select name="skinname" id="skinname" onchange="btype_check('skinname');">
										<?php
										$skin_path = BBSE_BOARD_PLUGIN_ABS_PATH."skin/";
										$files = array();

										if($dh = opendir($skin_path)){
											while(($read = readdir($dh)) !== false){
												if(is_dir($path.$read)) continue;
												$files[] = $read;
											}
											closedir($dh);
										}
										sort($files);

										foreach($files as $name){
											if($name == $data->skinname) $sSelect = "selected style='color:#ff0000;'";
											else $sSelect = "";
											echo "<option value='$name' $sSelect>$name</option>";
										}
										?>
									</select>
									<p>대표이미지를 사용하지 않는 갤러리 게시판을 원하시면 <b>gallery-v2</b> 스킨으로 선택하세요.</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">썸네일 비율&nbsp;</div>
								<div style="float:left;">
									<select name="thumb_ratio" id="thumb_ratio">
										<?php
										$thumbnail_ratio = array(
											1 => '1:1',
											2 => '2:1',
											3 => '3:2',
											4 => '4:3',
											5 => '16:9',
										);
										echo "<option value=''>갤러리 비율선택</option>";
										foreach($thumbnail_ratio as $key=>$ratio){
											if($key == $data->thumb_ratio) $sSelect = "selected style='color:#ff0000;'";
											else $sSelect = "";
											echo "<option value='$key' $sSelect>$ratio</option>";
										}
										?>
									</select>
									<p>가로:세로 (3:2권장, 사용중인 테마의 환경에 따라 선택한 비율로 표시되지 않을 수 있습니다.)</p>
								</div>
							</div>
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">가로 사이즈&nbsp;</div>
								<div style="float:left;">
									<input type="text" name="table_width" style="width:100px;" value="<?php if(empty($data->table_width)) echo "100"; else echo $data->table_width;?>" class="only_num">
									픽셀(100 이하일 경우 %로 적용)
								</div>
							</div>
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">정렬&nbsp;</div>
								<div style="float:left;">
									<label><input type="radio" name="table_align" value="C"<?php if(!empty($data->table_align) && $data->table_align == "C") echo " checked"?> style='border:0px;' /> 중앙 </label>&nbsp;
									<label><input type="radio" name="table_align" value="L"<?php if(empty($data->table_align) || $data->table_align == "L") echo " checked"?> style='border:0px;' /> 왼쪽 </label>&nbsp;
									<label><input type="radio" name="table_align" value="R"<?php if(!empty($data->table_align) && $data->table_align == "R") echo " checked"?> style='border:0px;' /> 오른쪽 </label>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">페이지당 목록 수&nbsp;</div>
								<div style="float:left;">
									<input type="text" name="page_size" style="width:100px;" value="<?php if(empty($data->page_size)) echo "20"; else echo $data->page_size;?>" class="only_num"> 개
									<p>1page에 보여질 목록 수</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">조회수 비공개&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="hidden_hit" value="1"<?php if(!empty($data->hidden_hit) && $data->hidden_hit == 1) echo " checked";?> /> 비공개</label>
									<p>리스트/뷰 페이지에 조회수를 공개하지 않습니다.</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">작성자 비공개&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="hidden_writer" value="1"<?php if(!empty($data->hidden_writer) && $data->hidden_writer == 1) echo " checked";?> /> 비공개</label>
									<p>리스트/뷰 페이지에 작성자를 공개하지 않습니다.</p>
								</div>
							</div>
						</div>
					</li>
					<li>
						<div class="form_stitle">기능설정</div>
						<div class="form_content">
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">댓글 기능&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_comment" id="use_comment" value="1"<?php if(!empty($data->use_comment) && $data->use_comment == 1) echo " checked";?>> 사용함</label>
								</div>
							</div>
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">답글 기능&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_reply" id="use_reply" value="1"<?php if(!empty($data->use_reply) && $data->use_reply == 1) echo " checked";?>> 사용함(기본형 선택시 사용가능)</label>
								</div>
							</div>
							<div style="height:35px;vertical-align:middle;">
								<div style="width:200px;float:left;">비밀글 기능&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_secret" id="use_secret" value="1" <?php if(!empty($data->use_secret) && $data->use_secret == 1) echo "checked";?>> 사용함</label>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">지정사용자 비밀글 열람&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="ext_var1" id="ext_var1" value="1" <?php if(!empty($data->ext_var1) && $data->ext_var1 == 1) echo "checked";?>> 허용</label>
									<p style="color:blue;font-weight:bold;">허용으로 체크하면 글보기 권한이 지정사용자인 경우에 한해 설정된 사용자는 비밀글을 열람할 수 있습니다.</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">공지 기능&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_notice" value="1" <?php if(!empty($data->use_notice) && $data->use_notice == 1) echo "checked";?>> 사용함</label>
									<p>공지 체크시 목록의 최상단에 글이 등록됩니다.</p>
								</div>
							</div>
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">연락처 정보받기&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_phone" value="1" <?php if(!empty($data->use_phone) && $data->use_phone == 1) echo "checked";?>> 사용함(관리자만 확인가능)</label>
								</div>
							</div>
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">이메일 정보받기&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_email" value="1" <?php if(!empty($data->use_email) && $data->use_email == 1) echo "checked";?>> 사용함(관리자만 확인가능)</label>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">첨부파일 기능&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_pds" value="1" <?php if(!empty($data->use_pds) && $data->use_pds == 1) echo "checked";?>> 사용함</label>
									<p>첨부파일은 2개까지 업로드 가능합니다.</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">첨부파일 용량&nbsp;</div>
								<div style="float:left;">
									<input type="text" name="upload_size" style='width:100px;' value="<?php if(!empty($data->upload_size)) echo $data->upload_size;?>" class="only_num"> MB
									<p>파일1개당 업로드 용량제한(최대 5MB)</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">에디터 설정&nbsp;</div>
								<div style="float:left;">
									<label><input type="radio" name="editor" value="N" <?php if(empty($data->editor) || $data->editor == 'N') echo "checked";?>>사용안함</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<label><input type="radio" name="editor" value="W" <?php if(!empty($data->editor) && $data->editor == 'W') echo "checked";?>>워드프레스 내장 에디터</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<p>게시물 작성시 사용할 에디터를 선택합니다.</p>
								</div>
							</div>
							<div style="height:120px;vertical-align:middle;">
								<div style="width:200px;float:left;">자동글 등록방지기능<br><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>images/gd_caution_2.png">&nbsp;</div>
								<div style="float:left;">
									<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td width="140">
											<table border="0" cellspacing="0" cellpadding="0" width="140">
											<tr>
												<td>
													<label><input type='radio' name='using_subAutoWtype' value='TXT' onfocus="this.blur();" <?php if(empty($data->using_subAutoWtype) || $data->using_subAutoWtype == 'TXT') echo "checked";?>>텍스트 방식</label>&nbsp;&nbsp;
													<table border="0" cellspacing="0" cellpadding="0" width="105" height="65" style="border:1px solid #cccccc;">
													<tr>
														<td bgcolor="#f5f5f5" align='center'><?php echo $auth_fulltxt?></td>
													</tr>
													</table>
												</td>
											</tr>
											</table>
										</td>
										<td width="150">
											<table border="0" cellspacing="0" cellpadding="0" width="140">
											<tr>
												<td>
													<label><input type='radio' name='using_subAutoWtype' value='GD' onfocus="this.blur();" <?php if(!empty($data->using_subAutoWtype) && $data->using_subAutoWtype == 'GD') echo "checked";?>>GD라이브러리 방식 </label>
													<table border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td align='center'><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>class/auth_img.php" style="border:1px solid #cccccc;"></td>
													</tr>
													</table>
												</td>
											</tr>
											</table>
										</td>
										<td>
											<table border="0" cellspacing="0" cellpadding="0" width="140">
											<tr>
												<td>
													<label><input type='radio' name='using_subAutoWtype' value='NO' onfocus="this.blur();" <?php if(!empty($data->using_subAutoWtype) && $data->using_subAutoWtype == 'NO') echo "checked";?>>사용 안함</label>
													<table border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td align='center' height="65"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>images/no_csrf.gif"></td>
													</tr>
													</table>
												</td>
											</tr>
											</table>
										</td>
									</tr>
									</table>
									<p>스팸글 방지기능으로 'GD라이브러리 방식' 또는 '텍스트 방식'의 사용을 권해 드립니다.</p>
								</div>
							</div>
							<div style="height:30px;vertical-align:middle;">
								<div style="width:200px;float:left;">필터링&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_filter" value="1" onclick="styleView(this.name);" <?php if(!empty($data->use_filter) && $data->use_filter == 1) echo "checked";?>> 사용함</label>
								</div>
							</div>
							<div id="use_filterStyle" style="height:110px;vertical-align:middle;display:<?php if(!empty($data->use_filter) && $data->use_filter == 1) echo ""; else echo "none";?>;">
								<div style="width:200px;float:left;">필터링목록&nbsp;</div>
								<div style="float:left;">
									<textarea name="filter_list" style="width:495px;height:60px;"><?php if($data->cnf_contents) echo $data->cnf_contents; else{ echo "8억,새끼,개새끼,소새끼,병신,지랄,씨팔,십팔,찌랄,지랄,쌍년,쌍놈,빙신,좆까,니기미,좆같은게,잡놈,벼엉신,바보새끼,씹새끼,씨팔,시벌,씨벌,떠그랄,좆밥,추천인,추천id,추천아이디,추천id,추천아이디,추/천/인,쉐이,등신,싸가지,미친놈,미친넘,찌랄,죽습니다,님아,님들아,씨밸넘";}?></textarea>
									<p>여러값 등록시 콤마(,)로 구분</p>
								</div>
							</div>
							<div style="height:110px;vertical-align:middle;">
								<div style="width:200px;float:left;margin:10px 0;">SNS&nbsp;</div>
								<div style="float:left;margin:10px 0;">
									<p style="line-height:20px;color:#1e8cbe;">
										* 체크된 서비스의 공유 버튼이 본문 보기 페이지 하단에 노출됩니다.<br />
										* 카카오톡 공유 기능을 위해서는 <a href="https://developers.kakao.com/" class="info_btn" title="개발자 등록" target="_blank">개발자등록</a>/앱생성 후 자바스크립트 앱키를 아래에 입력하세요.<br />
										* 카카오톡과 카카오스토리는 모바일 환경에서 사용자의 단말기가 지원하는(앱이 설치된) 경우에만 동작합니다.<br />
										* 카카오 앱키는 생성된 게시판 중 1개에만 등록하여도 모든 게시판에 적용됩니다.
									</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">&nbsp;</div>
								<div style="float:left;">
									<label><input type="checkbox" name="use_facebook" id="use_facebook" value="1"<?php if(!empty($data->use_facebook) && $data->use_facebook == 1) echo " checked";?>> Facebook</label>
									<label><input type="checkbox" name="use_twitter" id="use_twitter" value="1"<?php if(!empty($data->use_twitter) && $data->use_twitter == 1) echo " checked";?>> Twitter</label>
									<label><input type="checkbox" name="use_hms" id="use_hms" value="1"<?php if(!empty($data->use_hms) && $data->use_hms == 1) echo " checked";?>> HMS</label>
									<label><input type="checkbox" name="use_kakaotalk" id="use_kakaotalk" value="1" onclick="use_kakaotalk_change();"<?php if(!empty($data->use_kakaotalk) && $data->use_kakaotalk == 1) echo " checked";?>> 카카오톡</label>
									<label><input type="checkbox" name="use_kakaostory" id="use_kakaostory" value="1"<?php if(!empty($data->use_kakaostory) && $data->use_kakaostory == 1) echo " checked";?>> 카카오스토리</label>
									<p>내용을 SNS로 공유할 수 있습니다.(FAQ형 선택시 페이스북 사용불가)</p>
								</div>
							</div>
							<div id="kakao_app_key_tr" style="height:30px;vertical-align:middle;display:<?php if(!empty($data->use_kakaotalk) && $data->use_kakaotalk == 1) echo ""; else echo "none";?>;">
								<div style="width:200px;float:left;">카카오 앱키&nbsp;</div>
								<div style="float:left;">
									<input type="text" name="kakao_app_key" style="width:250px;" maxlength="40" value="<?php if(!empty($kakao_app_key)) echo $kakao_app_key;?>">
								</div>
							</div>








































						</div>
					</li>
					<li>
						<div class="form_stitle">권한설정 (v1.5부터 워드프레스 사용자권한을 따릅니다.)</div>
						<div class="form_content">
						<?php
						$auth_list = array('l_list'=>'목록보기권한', 'l_read'=>'글보기권한', 'l_write'=>'글쓰기권한', 'l_comment'=>'댓글쓰기권한', 'l_reply'=>'답글쓰기권한' );
						foreach($auth_list as $k=>$v){
						?>
							<div style="height:35px;vertical-align:middle;">
								<div style="width:200px;float:left;"><?php echo $v?>&nbsp;</div>
								<div style="float:left;">
									<select name="<?php echo $k?>">
										<option value='all'<?php if(empty($data->{$k}) || $data->{$k} == 'all') echo " selected";?>>비회원</option>
										<option value='subscriber'<?php if(!empty($data->{$k}) && $data->{$k} == 'subscriber') echo " selected ";?>>구독자</option>
										<option value='contributor'<?php if(!empty($data->{$k}) && $data->{$k} == 'contributor') echo " selected ";?>>기여자</option>
										<option value='author'<?php if(!empty($data->{$k}) && $data->{$k} == 'author') echo " selected ";?>>글쓴이</option>
										<option value='editor'<?php if(!empty($data->{$k}) && $data->{$k} == 'editor') echo " selected ";?>>편집자</option>
										<option value='administrator'<?php if(!empty($data->{$k}) && $data->{$k} == 'administrator') echo " selected";?>>관리자</option>
										<option value='private'<?php if(!empty($data->{$k}) && $data->{$k} == 'private') echo " selected";?>>지정사용자</option>
									</select>
								</div>
							</div>
						<?php }?>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">지정사용자&nbsp;</div>
								<div style="float:left;">
									<input type="text" name="user_list" style="width:450px;" value="<?php if(!empty($data->user_list)) echo $data->user_list;?>">
									<p>아이디를 입력하세요. 여러 아이디 입력시 공백없이 콤마(,)로 구분</p>
								</div>
							</div>
							<div style="height:60px;vertical-align:middle;">
								<div style="width:200px;float:left;">권한없음 페이지 URL&nbsp;</div>
								<div style="float:left;">
									<input type="text" name="no_permission_url" style="width:450px;" value="<?php if(!empty($data->no_permission_url)) echo $data->no_permission_url;?>">
									<p>* 미입력시 메인 페이지로 이동합니다.</p>
								</div>
							</div>
						</div>
					</li>
				</ul>
				<div class="btn">
					<button type="button" class="b _c1" onclick="setup_submit('<?php if(empty($board_no)) echo "insert"; else echo "modify";?>','<?php echo BBSE_BOARD_SETUP_URL?>');">저장</button>
				</div>
				</form>
				<!-- 내용 end -->
			</div>
			<div class="guide_bottom"><span class="lb"></span><span class="rb"></span></div>
		</div>
	</div>
	<?php global $noticeBoxComment; echo $noticeBoxComment;?>
</div>
<script type="text/javascript">
<?php if(!empty($board_no) && !empty($data->formtype)){?>
set_limits("<?php echo $data->formtype?>");
<?php }?>
</script>
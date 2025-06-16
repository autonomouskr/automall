<script type="text/javascript">
function upload_img(formfield){
	jQuery('#upload_target_img').val(formfield);
	tb_show('', 'media-upload.php?type=image&TB_iframe=true&width=640&height=450&modal=false');
	return false;
}

function send_to_editor(html){
	var div = document.createElement('div');
	jQuery(div).html(html);
	var src = jQuery(div).find("img:first").attr("src");
	var uTarget = jQuery('#upload_target_img').val();
	jQuery('#' + uTarget).val(src);
	tb_remove();
	jQuery('#upload_target_img').val("");
}

function write_check(action_url){
	var frm = document.write_frm;

	if(confirm("정보를 수정하시겠습니까?    ")){
		frm.action = action_url;
		frm.submit();
	}
}

function guestOrder_change(tVal){
	if(tVal=='Y'){
		jQuery("input:radio[name='guest_order_view']:radio[value='Y']").attr("checked",true);
		jQuery("input:radio[name='guest_order_view']:radio[value='N']").attr("disabled",true);
	}
	else{
		jQuery("input:radio[name='guest_order_view']:radio[value='N']").attr("disabled",false);
	}
}
</script>
<div>
	<?php
	if(!empty($edit_mode) && $edit_mode == "edit"){
		echo '<div id="message" class="updated fade"><p><strong>정보를 정상적으로 저장하였습니다.</strong></p></div>';
	}
	?>

	<div class="borderBox">
		* 회원관리 환경을 설정할수 있습니다.
	</div>

	<form method="post" name="write_frm" enctype="multipart/form-data">
	<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2">페이지 설정<th>
		</tr>
		<tr>
			<th>로그인</th>
			<td>
				<?php
				$login_page = get_option("bbse_commerce_login_page");
				?>
				<select name="login_page">
					<option value=""<?php if(empty($login_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					$pageData = get_custom_list('page');

					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($login_page) && ($pageData[$z]['id'] == $login_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php
					}
					?>
				</select>			
			</td>
		</tr>
		<tr>
			<th>아이디 찾기</th>
			<td>
				<?php
				$id_search_page = get_option("bbse_commerce_id_search_page");
				?>
				<select name="id_search_page">
					<option value=""<?php if(empty($id_search_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					$pageData = get_custom_list('page');

					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($id_search_page) && ($pageData[$z]['id'] == $id_search_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php
					}
					?>
				</select>			
			</td>
		</tr>

		<tr>
			<th>회원가입/수정</th>
			<td>
				<?php
				$join_page = get_option("bbse_commerce_join_page");
				?>
				<select name="join_page">
					<option value=""<?php if(empty($join_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					$pageData = get_custom_list('page');

					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($join_page) && ($pageData[$z]['id'] == $join_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php
					}
					?>
				</select>
			</td>
		</tr>

		<tr>
			<th>비밀번호 찾기</th>
			<td>
				<?php
				$pass_search_page = get_option("bbse_commerce_pass_search_page");
				?>
				<select name="pass_search_page">
					<option value=""<?php if(empty($pass_search_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					$pageData = get_custom_list('page');

					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($pass_search_page) && ($pageData[$z]['id'] == $pass_search_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php
					}
					?>
				</select>		
			</td>
		</tr>
		<tr>
			<th>회원탈퇴</th>
			<td>
				<?php
				$delete_page = get_option("bbse_commerce_delete_page");
				?>
				<select name="delete_page">
					<option value=""<?php if(empty($delete_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					$pageData = get_custom_list('page');

					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($delete_page) && ($pageData[$z]['id'] == $delete_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php
					}
					?>
				</select>			
			</td>
		</tr>
		<tr>
			<th colspan="2">본인인증(휴대폰인증) 설정<th>
		</tr>
		<tr>
			<th>사용여부</th>
			<td>
				<label><input type="radio" name="certification_yn" value="Y"<?php if($config['certification_yn'] == "Y") echo " checked"?> /> 사용</label> &nbsp;
				<label><input type="radio" name="certification_yn" value="N"<?php if($config['certification_yn'] == "N") echo " checked"?> /> 사용안함</label>
			</td>
		</tr>
		<tr>
			<th>본인인증 대행사</th>
			<td>
				나이스평가정보
				<select name="certification_company">
					<option value="">선택</option>
					<option value="A" <?php if($config['certification_company']=='A') echo " selected";?>>나이스평가정보</option>
				</select>
				<button type="button" onclick="window.open('http://www.bbsetheme.com/hosting-certify');" class="button-small blue" style="height:25px;" onclick="">본인인증 서비스 신청</button>
			</td>
		</tr>
		<tr>
			<th>설정값</th>
			<td>
				사이트 코드 : <input type="text" name="certification_id" style="width:100px;" value="<?php echo $config['certification_id'];?>">
				패스워드 : <input type="text" name="certification_pass" style="width:100px;" value="<?php echo $config['certification_pass'];?>">
				<!-- KEY : <input type="text" name="certification_key" style="width:300px;" value="<?php echo $config['certification_key'];?>"> --><br />
				<?php if(!extension_loaded('CPClient')) { ?>
					<span style="width:100%;background-color:#cf5656;color:#ffffff;padding:5px;">
					서버에 본인인증 모듈(CPClient.so)이 설치되어 있지 않습니다. 본인인증 서비스 사용자 메뉴얼을 확인하셔서 설치해주시기 바랍니다.
					</span>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<th colspan="2">비회원구매설정<th>
		</tr>
		<tr>
			<th>비회원구매</th>
			<td>
				<label><input type="radio" name="guest_order" onClick="guestOrder_change('Y');" value="Y"<?php if($config['guest_order'] == "Y") echo " checked"?> /> 사용</label> &nbsp;
				<label><input type="radio" name="guest_order" onClick="guestOrder_change('N');" value="N"<?php if($config['guest_order'] == "N") echo " checked"?> /> 사용안함</label>
			</td>
		</tr>
		<tr>
			<th>비회원 주문조회</th>
			<td>
			<?php
			$checkedY=$checkedN="";
			if($config['guest_order'] == "Y"){
				$checkedY="checked";
				$checkedN="disabled";
			}
			else{
				if($config['guest_order_view'] == "Y") $checkedY="checked";
				else $checkedN="checked";
			}
			?>
				<label><input type="radio" name="guest_order_view" value="Y" <?php echo $checkedY;?> /> 사용</label> &nbsp;
				<label><input type="radio" name="guest_order_view" value="N" <?php echo $checkedN;?> /> 사용안함</label>
			</td>
		</tr>

		<tr>
			<th colspan="2">회원가입 항목설정<th>
		</tr>
		<tr>
			<th>이름</th>
			<td>
				<input type="checkbox" name="use_name" value="1"<?php if($config['use_name'] == 1) echo " checked"?> /> 사용함 &nbsp;
				<input type="checkbox" name="validate_name" value="1"<?php if($config['validate_name'] == 1) echo " checked"?> /> 필수입력사항
			</td>
		</tr>
		<tr>
			<th>주소</th>
			<td>
				<input type="checkbox" name="use_addr" value="1"<?php if($config['use_addr'] == 1) echo " checked"?> /> 사용함 &nbsp;
				<input type="checkbox" name="validate_addr" value="1"<?php if($config['validate_addr'] == 1) echo " checked"?> /> 필수입력사항<br />
				<input type="hidden" name="use_zipcode_api" value="1" /> <!--도로명주소검색 사용(v1.4.9 late)-->
				<input type="hidden" name="zipcode_api_module" value="2" /><!--도로명주소검색 시 Daum 만 사용(v1.4.9 late)--> 
				<span style="width:100%;background-color:#cf5656;color:#ffffff;padding:5px;">
					BBS e-Commerce 버전 1.4.9 이상에서는 Daum 주소검색(지번, 도로명 모두 가능) 만 지원합니다.
				</span>
			</td>
		</tr>
		<tr>
			<th>생년월일</th>
			<td>
				<input type="checkbox" name="use_birth" value="1"<?php if($config['use_birth'] == 1) echo " checked"?> /> 사용함 &nbsp;
				<input type="checkbox" name="validate_birth" value="1"<?php if($config['validate_birth'] == 1) echo " checked"?> /> 필수입력사항<br />
			</td>
		</tr>
		<tr>
			<th>전화번호</th>
			<td>
				<input type="checkbox" name="use_phone" value="1"<?php if($config['use_phone'] == 1) echo " checked"?> /> 사용함 &nbsp;
				<input type="checkbox" name="validate_phone" value="1"<?php if($config['validate_phone'] == 1) echo " checked"?> /> 필수입력사항<br />
			</td>
		</tr>
		<tr>
			<th>휴대전화번호</th>
			<td>
				<input type="checkbox" name="use_hp" value="1"<?php if($config['use_hp'] == 1) echo " checked"?> /> 사용함 &nbsp;
				<input type="checkbox" name="validate_hp" value="1"<?php if($config['validate_hp'] == 1) echo " checked"?> /> 필수입력사항<br />
			</td>
		</tr>
		<tr>
			<th>성별</th>
			<td>
				<input type="checkbox" name="use_sex" value="1"<?php if($config['use_sex'] == 1) echo " checked"?> /> 사용함 &nbsp;
				<input type="checkbox" name="validate_sex" value="1"<?php if($config['validate_sex'] == 1) echo " checked"?> /> 필수입력사항<br />
			</td>
		</tr>
		<tr>
			<th>직업</th>
			<td>
				<input type="checkbox" name="use_job" value="1"<?php if($config['use_job'] == 1) echo " checked"?> /> 사용함 &nbsp;
				<input type="checkbox" name="validate_job" value="1"<?php if($config['validate_job'] == 1) echo " checked"?> /> 필수입력사항<br />
			</td>
		</tr>
		<tr>
			<th colspan="2">기타 환경설정<th>
		</tr>
		<tr>
			<th>아이디 최소길이</th>
			<td>
				<input type="text" name="id_min_len" style="width:100px;" value="<?php echo $config['id_min_len'];?>" onkeydown="checkForNumber();"> 자 이상 (0 입력시 제한없음)	
			</td>
		</tr>
		<tr>
			<th>비밀번호 최소길이</th>
			<td>
				<input type="text" name="pass_min_len" style="width:100px;" value="<?php echo $config['pass_min_len'];?>" onkeydown="checkForNumber();"> 자 이상 (0 입력시 제한없음)
			</td>
		</tr>
		<tr>
			<th>가입불가 아이디</th>
			<td>
				<input type="text" name="join_not_id" style="width:500px;" value="<?php echo $config['join_not_id']?>" /><br />
				여러 아이디 등록시 콤마(,)로 구분하여 입력하세요.
			</td>
		</tr>
		<tr>
			<th>회원가입시 기본등급</th>
			<td>
				<select id="user_class" name="join_default_class">
				<?php
					if(!$config['join_default_class']) $joinDefaultClass='2';
					else $joinDefaultClass=$config['join_default_class'];

					$mclass_rlt = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class ORDER BY no ASC");
					foreach($mclass_rlt as $i => $mclass){
						echo '<option value="'.$mclass->no.'"'.(($mclass->no == $joinDefaultClass)? ' selected' : '').'>'.$mclass->class_name.'</option>';
					}
				?>
				</select>

			</td>
		</tr>
	</table>
	</form>

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onclick="write_check('<?php echo $EDIT_CONFIG_URL?>');" style="width:150px;"> 저장 </button>
	</div>
	<div class="clearfix" style="height:20px;"></div>

</div>
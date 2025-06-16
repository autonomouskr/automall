<script type="text/javascript">
function write_check(action_url){
	var frm = document.join_frm;
	var idMinLength = <?php echo $config['id_min_len']?>;
	<?php if($_REQUEST['mode']=="add"){?>
	if(frm.user_id.value == "") {
		alert("아이디를 입력해주세요.");
		frm.user_id.focus();
		return false; 
	}
	if(frm.user_id.value.length < idMinLength) {
		alert("아이디는 "+idMinLength+"자 이상 입력해주세요.");
		frm.user_id.focus();
		return false;
	}
	if(frm.check_flag.value!="ok") {
		alert("아이디 중복확인을 해주세요.");
		return false;
	}
	if(frm.check_flag_email.value!="ok") {
		alert("이메일 중복확인을 해주세요.");
		return false;
	}
	<?php }?>
	if(frm.pass.value != ""){
		<?php if($config['pass_min_len'] > 0){?>
		if(frm.pass.value.length < <?php echo $config['pass_min_len']?>){
			alert("비밀번호는 <?php echo $config['pass_min_len']?>자 이상으로 입력해주세요.");
			frm.pass.focus();
			return false;
		}
		<?php }?>
		if(frm.pass.value.length > 16){
			alert("비밀번호는 16자 이하로 입력해주세요.");
			frm.pass.focus();
			return false; 
		}
		if(frm.repass.value == ""){
			alert("비밀번호 확인을 위해 다시 한번더 입력해주세요.");
			frm.repass.focus();
			return false; 
		}
		if(frm.repass.value != frm.pass.value){
			alert("비밀번호가 일치하지 않습니다.");
			frm.repass.focus();
			return false; 
		}
	}

	<?php if($config['use_name'] == 1 && $config['validate_name'] == 1){?>
	if(!frm.name.value){
		alert("이름을 입력해주세요.");
		frm.name.focus();
		return false;
	}
	<?php }?>
	<?php if($config['use_birth'] == 1 && $config['validate_birth'] == 1){?>
	if(!frm.birth_year.value){
		alert("생년월일(년)을 입력해주세요.");
		frm.birth_year.focus();
		return false;
	}
	if(frm.birth_year.value < 1900 || frm.birth_year.value > <?php echo date("Y")?>){
		alert("생년월일(년)을 바르게 입력해주세요.");
		frm.birth_year.focus();
		frm.birth_year.value = "";
		return false; 
	}
	if(!frm.birth_month.value){
		alert("생년월일(월)을 입력해주세요.");
		frm.birth_month.focus();
		return false;
	}
	if(frm.birth_month.value > 12 || frm.birth_month.value < 1){
		alert("생년월일(월)을 바르게 입력해주세요.");
		frm.birth_month.focus();
		frm.birth_month.value = "";
		return false; 
	}
	if(!frm.birth_day.value){
		alert("생년월일(일)을 입력해주세요.");
		frm.birth_day.focus();
		return false;
	}
	if(frm.birth_day.value > 31 || frm.birth_day.value < 1){
		alert("생년월일(일)을 바르게 입력해주세요.");
		frm.birth_day.focus();
		frm.birth_day.value = "";
		return false; 
	}
	<?php }?>
	<?php if($config['[payMode'] == 1 && $config['validate_payMode'] == 1){?>
	if(frm.payMode[0].checked == false && frm.payMode[1].checked == false){
		alert("결제방식을 선택해주세요.");
		frm.payMode[0].focus();
		return false;
	}
	<?php }?>
	<?php if($config['use_sex'] == 1 && $config['validate_sex'] == 1){?>
	if(frm.sex[0].checked == false && frm.sex[1].checked == false){
		alert("성별을 선택해주세요.");
		frm.sex[0].focus();
		return false;
	}
	<?php }?>
	<?php if($config['use_addr'] == 1 && $config['validate_addr'] == 1){?>
	if(!frm.zipcode.value){
		alert("우편번호를 입력해주세요.");
		return false;
	}
	if(!frm.addr1.value){
		alert("주소를 입력해주세요.");
		return false;
	}
	if(!frm.addr2.value){
		alert("나머지주소를 입력해주세요.");
		frm.addr2.focus();
		return false;
	}
	<?php }?>
	<?php if($config['use_phone'] == 1 && $config['validate_phone'] == 1){?>
	if(!frm.phone_1.value){
		alert("전화번호를 입력해주세요.");
		frm.phone_1.focus();
		return false;
	}
	if(!frm.phone_2.value){
		alert("전화번호를 입력해주세요.");
		frm.phone_2.focus();
		return false;
	}
	if(!frm.phone_3.value){
		alert("전화번호를 입력해주세요.");
		frm.phone_3.focus();
		return false;
	}
	<?php }?>
	<?php if($config['use_hp'] == 1 && $config['validate_hp'] == 1){?>
	if(!frm.hp_1.value){
		alert("휴대전화번호를 입력해주세요.");
		frm.hp_1.focus();
		return false;
	}
	if(!frm.hp_2.value){
		alert("휴대전화번호를 입력해주세요.");
		frm.hp_2.focus();
		return false;
	}
	if(!frm.hp_3.value){
		alert("휴대전화번호를 입력해주세요.");
		frm.hp_3.focus();
		return false;
	}
	<?php }?>
	if(!frm.email.value){
		alert("이메일을 입력해주세요.");
		frm.email.focus();
		return false;
	}
	if(!ChkMail(frm.email.value)){
		alert("이메일 형식이 올바르지 않습니다.");
		return false;
	}
	<?php if($config['use_job'] == 1 && $config['validate_job'] == 1){?>
	if(!frm.job.value){
		alert("직업을 입력해주세요.");
		frm.job.focus();
		return false;
	}
	<?php }?>
	<?php if($_REQUEST['mode']=="edit"){?>
	if(confirm("정보를 수정하시겠습니까?    ")){
		frm.action = action_url;
		frm.submit();
	}
	<?php }else{?>
		frm.action = action_url;
		frm.submit();
	<?php }?>
}
function zipcode_search(){
	window.open("<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>zipcode.php", "zipcode_search", "width=400,height=400,scrollbars=yes");
}
<?php if($config['use_zipcode_api'] == 1 && $config['zipcode_api_module'] == 2){  /* Daum 우편번호 API */?>
function openDaumPostcode(){
	new daum.Postcode({
		oncomplete: function(data){
			if(data.userSelectedType === 'R'){
                // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
                var extraRoadAddr = ''; // 도로명 조합형 주소 변수

                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    extraRoadAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if(data.buildingName !== '' && data.apartment === 'Y'){
                   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if(extraRoadAddr !== ''){
                    extraRoadAddr = ' (' + extraRoadAddr + ')';
                }
                // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
                if(fullRoadAddr !== ''){
                    fullRoadAddr += extraRoadAddr;
                }

				document.getElementById('zipcode').value = data.zonecode;
				document.getElementById('addr1').value = fullRoadAddr;
			}
			else{
				document.getElementById('zipcode').value = data.postcode1+"-"+data.postcode2;
				document.getElementById('addr1').value = data.jibunAddress;
			}

			document.getElementById('addr2').focus();
		}
	}).open();
}
<?php }?>

function id_check() {
	if(jQuery("#user_id").val()=="") {
		alert("아이디를 입력해주세요.");
		jQuery("#user_id").focus();
		return;
	}
	jQuery("#user_id").val()


	jQuery.ajax({
		type: 'post'
		, async: false
		, url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-id-check.php'
		//, url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-id-check.exec.php'
		, data: {user_id:jQuery("#user_id").val()}
		, success: function(data){
			var response = data.split("|||");
			if(response[0] == "ok"){
				alert("사용 가능한 아이디입니다.");
				jQuery("#check_flag").val(response[0]);
			}else if(response[0] == "exist"){
				alert("이미 가입된 아이디입니다.");
				jQuery("#user_id").select();
			}else if(response[0] == "minlen"){
				alert( response[1]+"자 이상 입력해주세요.");
				jQuery("#user_id").select();
			}else{
				alert('서버와의 통신이 실패했습니다.');
			}
		}
		, error: function(data, status, err){
			alert('서버와의 통신이 실패했습니다.');
		}
	});
}

function email_check() {
	if(jQuery("#email").val()=="") {
		alert("이메일을 입력해주세요.");
		jQuery("#email").focus();
		return;
	}
	jQuery("#email").val()


	jQuery.ajax({
		type: 'post'
		, async: false
		, url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-email-check.php'
		, data: {email:jQuery("#email").val()}
		, success: function(data){
			var response = data.split("|||");
			if(response[0] == "ok"){
				alert("사용 가능한 이메일입니다.");
				jQuery("#check_flag_email").val(response[0]);
			}else if(response[0] == "exist"){
				alert("이미 존재하는 이메일입니다.");
				jQuery("#email").select();
			}else{
				alert('서버와의 통신이 실패했습니다.');
			}
		}
		, error: function(data, status, err){
			alert('서버와의 통신이 실패했습니다.');
		}
	});
}

	function social_view(tData){
		var tbHeight = 268;
		var tbWidth=450;
		tb_show("소셜로그인 정보", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-social-info.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

</script>
<?php
global $earnType;
if($_REQUEST['mode']=="add") {
	$mode_tit = "회원추가";
	$mvrun = "add_proc";
}else{
	$mode_tit = "회원정보";
	$mvrun = "edit_proc";
}
if(!empty($result->jumin)) $jumin_arr = explode("-", $result->jumin);
if(!empty($result->birth)) $birth_arr = explode("-", $result->birth);
if(!empty($result->phone)) $phone_arr = explode("-", $result->phone);
if(!empty($result->hp)) $hp_arr = explode("-", $result->hp);

parse_str($_SERVER[QUERY_STRING]);
$add_args = array("paged"=>$paged,"per_page"=>$per_page, "search1"=>$search1, "search2"=>$search2, "orderby"=>$orderby, "order"=>$order, "keyword"=>$keyword, "search_date1"=>$search_date1, "search_date2"=>$search_date2, "user_class"=>$user_class);
$queryString = http_build_query($add_args);

$USER_ACTION_URL = "admin.php?page=bbse_commerce_member&mode=".$_REQUEST['mode']."&".$queryString;
if($_REQUEST['mode']=="edit"){
	$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_earn_log WHERE user_id='".$result->user_id."'"); //총 목록수
	$earnRow = $wpdb->get_results("SELECT * FROM bbse_commerce_earn_log WHERE user_id='".$result->user_id."' ORDER BY idx DESC");
	$mileage = $wpdb->get_var("SELECT mileage FROM bbse_commerce_membership WHERE user_id='".$result->user_id."'");
}
?>
<div>
	<?php if($edit_mode == "edit"){echo '<div id="message" class="updated fade"><p><strong>정보를 정상적으로 저장하였습니다.</strong></p></div>';}?>
	<form method="post" id="join_frm" name="join_frm" enctype="multipart/form-data">
	<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']?>" />
	<input type="hidden" name="user_no" id="user_no" value="<?php echo $_REQUEST['user_no']?>" />
	<input type="hidden" name="check_flag" id="check_flag" value="" />
	<input type="hidden" name="check_flag_email" id="check_flag_email" value="" />
	<input type="hidden" name="mvrun" id="mvrun" value="<?php echo $mvrun?>" />
	<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">


	<div class="titleH5" style="margin:20px 0 10px 0; "><?php echo $mode_tit?></div>

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th>회원등급</th>
			<td>
				<select id="user_class" name="user_class" style="width:180px;">
				<?php
					$mclass_rlt = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class ORDER BY no ASC");
					foreach($mclass_rlt as $i => $mclass){
						echo '<option value="'.$mclass->no.'"'.(($result->user_class == $mclass->no || ($_REQUEST['mode']=="add" && $mclass->no==2))? ' selected' : '').'>'.$mclass->class_name.'</option>';
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<th>아이디</th>
			<td>
			<?php if($_REQUEST['mode']=="add"){?>
				<input type="text" name="user_id" id="user_id" style="width:180px;" value="" /> <button type="button" class="button-small gray" style="height:25px;" onclick="id_check()">중복확인</button>
			<?php }else{?>
				<?php echo $result->user_id?>
				<input type="hidden" name="user_id" id="user_id" value="<?php echo $result->user_id?>" />
			<?php }?>
			</td>
		</tr>
		<tr>
			<th>비밀번호</th>
			<td>
				<input type="password" name="pass" id="pass" style="width:180px;" value="" /> <?php if($_REQUEST['mode']!="add"){?>(변경시에만 입력)<?php }?>
			</td>
		</tr>
		<tr>
			<th>비밀번호 확인</th>
			<td>
				<input type="password" name="repass" id="repass" style="width:180px;" value="" />
			</td>
		</tr>
		<tr>
			<th>업체명</th>
			<td>
				<input type="text" name="name" id="name" style="width:180px;" value="<?php echo $result->name?>" />
			</td>
		</tr>
		<tr>
			<th>담당자명</th>
			<td>
				<input type="text" name="manager_name" id="manager_name" style="width:180px;" value="<?php echo $result->manager_name?>" />
			</td>
		</tr>
		<tr>
			<th>재고관리권한</th>
			<td>
				<select id="screen_code" name="screen_code" style="width:180px;">
				<option value="">사용안함</option>
				<?php
					$auth_list = $wpdb->get_results("SELECT * FROM bbse_commerce_member_pad_screen");
					foreach($auth_list as $i => $auth){
					    echo '<option value="'.$auth->screen_code.'"'.(($result->screen_code == $auth->screen_code)? ' selected' : '').'>'.$auth->screen_name.'</option>';
					}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<th>결제방식</th>
			<td>
				<input type="radio" name = "payMode" value = "01" <?php if($result->payMode == "01" || empty($result->payMode)) echo " checked";?>/> 월말결제 &nbsp;
				<input type="radio" name = "payMode" value = "02" <?php if($result->payMode == "02") echo " checked";?>/> 건별결제 &nbsp;
			</td>
		</tr>
		<tr>
			<th>이메일</th>
			<td>
			<?php if($_REQUEST['mode']=="add"){?>
				<input type="text" name="email" id="email" style="width:350px;" value="" /> <button type="button" class="button-small gray" style="height:25px;" onclick="email_check()">중복확인</button>
			<?php }else{?>
				<input type="text" name="email" id="email" style="width:350px;" value="<?php echo $result->email?>" />
			<?php }?>
				<input type="checkbox" name="email_reception" value="1"<?php if($result->email_reception == "1") echo " checked";?>> 정보메일 수신
			</td>
		</tr>
		<tr>
			<th>전화번호</th>
			<td>
				<input type="text" name="phone_1" id="phone_1" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phone_arr[0]?>" /> - 
				<input type="text" name="phone_2" id="phone_2" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phone_arr[1]?>" /> - 
				<input type="text" name="phone_3" id="phone_3" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phone_arr[2]?>" />
			</td>
		</tr>
		<tr>
			<th>휴대전화번호</th>
			<td>
				<input type="text" name="hp_1" id="hp_1" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hp_arr[0]?>" /> - 
				<input type="text" name="hp_2" id="hp_2" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hp_arr[1]?>" /> - 
				<input type="text" name="hp_3" id="hp_3" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hp_arr[2]?>" />
				<input type="checkbox" name="sms_reception" value="1"<?php if($result->sms_reception == "1") echo " checked";?>> 문자서비스 수신
			</td>
		</tr>
		<tr>
			<th>우편번호</th>
			<td>
				<?php
				if($config['use_zipcode_api'] == 1 && $config['zipcode_api_module'] == 2){  /* Daum 우편번호 API */
					$zipOpenScript = "openDaumPostcode();";
				}else{
					$zipOpenScript = "zipcode_search();";
				}
				?>
				<input type="text" name="zipcode" id="zipcode" style="width:70px;text-align:center;" value="<?php echo $result->zipcode?>" readonly />
				<a href="javascript:;" onclick="<?php echo $zipOpenScript;?>" hidefocus="true"><button type="button" class="button-small gray" style="height:25px;">우편번호찾기</button></a></span>
			</td>
		</tr>
		<tr>
			<th>주소</th>
			<td>
				<input type="text" name="addr1" id="addr1" style="width:350px;" value="<?php echo $result->addr1?>" /><br />
				<input type="text" name="addr2" id="addr2" style="width:350px;" value="<?php echo $result->addr2?>" />
			</td>
		</tr>
		<tr>
			<th>생년월일</th>
			<td>
				<input type="text" name="birth_year" id="birth_year" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $birth_arr[0]?>" /> 년 &nbsp;
				<input type="text" name="birth_month" id="birth_month" style="width:80px;" maxlength="2" onkeydown="checkForNumber();" value="<?php echo $birth_arr[1]?>" /> 월 &nbsp;
				<input type="text" name="birth_day" id="birth_day" style="width:80px;" maxlength="2" onkeydown="checkForNumber();" value="<?php echo $birth_arr[2]?>" /> 일 &nbsp;
			</td>
		</tr>
		<tr>
			<th>성별</th>
			<td>
				<input type="radio" name="sex" value="1"<?php if($result->sex == 1 || empty($result->sex)) echo " checked";?>> 남자 &nbsp;
				<input type="radio" name="sex" value="2"<?php if($result->sex == 2) echo " checked";?>> 여자
			</td>
		</tr>
		<tr>
			<th>직업</th>
			<td>
				<input type="text" name="job" id="job" style="width:180px;" value="<?php echo $result->job?>" />
			</td>
		</tr>
		<?php if($_REQUEST['mode']=="edit"){?>
		<tr>
			<th>회원가입일</th>
			<td>
				<?php echo date("Y-m-d H:i:s",$result->reg_date)?>
			</td>
		</tr>
		<tr>
			<th>최종 로그인일</th>
			<td>
				<?php echo ($result->last_login)?date("Y-m-d H:i:s",$result->last_login):"-"?>
			</td>
		</tr>
		<?php }?>
		<tr>
			<th>적립금</th>
			<td>
				<input type="text" name="mileage" id="mileage" style="width:180px;" onkeydown="checkForNumber();" value="<?php echo $result->mileage?>" />
				<?php if($_REQUEST['mode']=="edit"){?>
				<a href="#TB_inline?width=700&height=550&inlineId=modal-member-mileage" class="thickbox" title="적립금사용내역"><button type="button" class="button-small gray" style="height:25px;">적립금사용내역</button></a>
				<?php }?>
			</td>
		</tr>
		<tr>
			<th>소셜로그인 통합</th>
			<td>
			<?php
				$snsArray=Array("naver","facebook","google","daum","kakao","twitter");
				$prtStr="";
				for($i=0;$i<sizeof($snsArray);$i++){
					$sData=$wpdb->get_row("SELECT * FROM bbse_commerce_social_login WHERE member_id='".$result->user_id."' AND sns_type='".$snsArray[$i]."' AND integrate_yn='Y'");
					if($sData->sns_id){
						switch($snsArray[$i]){
							case "naver" : 
								$snsId=explode("@",$sData->sns_id);
								$snsUrl="http://blog.naver.com/".$snsId['0'];
								break;
							case "facebook" : 
								$snsUrl="https://www.facebook.com/".$sData->sns_id;
								break;
							case "google" : 
								$snsUrl="https://plus.google.com/".$sData->sns_id."/posts";
								break;
							case "twitter" : 
								$snsId=explode("-",$sData->sns_id);
								$snsUrl="https://twitter.com/".$snsId['1'];
								break;			
							default :
								$snsUrl="";
								break;

						}

						$prtStr .="&nbsp;<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/social_login_".$snsArray[$i].".png\" onClick=\"social_view('".$sData->idx."');\" align=\"absmiddle\" style=\"cursor:pointer;height:20px;width:auto;margin-right:10px;\" alt=\"".strtoupper($sData->sns_type)." 회원 통합\"/></span>&nbsp;&nbsp;";

						$snsCnt++;
					}
				}

				if(!$prtStr) echo "정보없음";
				else echo $prtStr;
			?>
			</td>
		</tr>
		<tr>
			<th>관리자 메모</th>
			<td>
				<textarea name="admin_log" id="admin_log" style="width:500px;height:80px;"><?php echo $result->admin_log?></textarea>
			</td>
		</tr>
	</table>
	</form>

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onclick="write_check('<?php echo $USER_ACTION_URL?>');" style="width:150px;"> 저장 </button>
		<button type="button" class="button-bbse blue" onclick="location.href = 'admin.php?page=bbse_commerce_member&cType=<?php echo $EDIT_PAGE.$queryString?"&".$queryString:""?>';" style="width:150px;"> 목록 </button>
	</div>
	<div class="clearfix" style="height:20px;"></div>


</div>


<?php if($_REQUEST['mode']=="edit"){?>
<div id="modal-member-mileage" style="display:none;">
	<div style="padding:10px;color:red;">현재잔액 : <?php echo number_format($mileage); ?>원</div>
	<table class="wp-list-table widefat fixed posts" cellspacing="0">
	<thead>
	<tr>
		<th scope="col" width="150" class="manage-column">일자</th>
		<th scope="col" width="" class="manage-column">적립/차감 내용</th>
		<th scope="col" width="100" class="manage-column">금액</th>
		<th scope="col" width="100" class='manage-column'>잔액</th>
	</tr>
	</thead>
	<tbody id="the-list">
	<?
	if($total > 0) {
		foreach($earnRow as $i=>$earn) {
			$num = $total-$i; //목록 번호
			if($i % 2 == 0) $alternate_class = "class=\"alternate\"";
			else $alternate_class = "";

			$earnMode = array("IN"=>"적립", "OUT"=>"차감");
			$earnSign = array("IN"=>"+", "OUT"=>"-");
			
			if($earn->earn_mode == "IN") {
				$current_point = $earn->old_point + $earn->earn_point;
			}else{
				$current_point = $earn->old_point - $earn->earn_point;
			}
	?>
	<tr <?php echo $alternate_class?> valign="middle">
		<td><?php echo date("Y-m-d H:i:s",$earn->reg_date); ?></td>
		<td><?php echo $earnType[$earn->earn_type]." ".$earnMode[$earn->earn_mode]; ?> <?php if($earn->earn_type=="order"){?>(<?php echo $earn->etc_idx?>)<?php }?></td>
		<td><?php echo $earnSign[$earn->earn_mode]; ?> <?php echo number_format($earn->earn_point); ?>원</td>
		<td><?php echo number_format($current_point); ?>원</td>
	</tr>
	<?
		}
	}else{
		echo "<tr><td colspan='4' align='center'>적립금 내역이 없습니다</td></tr>";
	}
	?>
	</tbody>
	</table>

</div>
<?php }?>
<?php if($config['use_zipcode_api'] == 1 && $config['zipcode_api_module'] == 2){  /* Daum 우편번호 API */?>
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

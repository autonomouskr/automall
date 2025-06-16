<?php
if(!$_REQUEST['cType']) $cType='list';
else $cType=$_REQUEST['cType'];

$BBSeCommerceMember = new BBSeCommerceMember;

$confingTitle=array("list"=>"회원목록","config"=>"환경설정","social"=>"소셜(간편)로그인","leave"=>"탈퇴/삭제내역","level"=>"회원등급관리","sms"=>"SMS관리","mail"=>"메일관리","page"=>"페이지추가안내");
$confingInfo=array("list"=>"","config"=>"","social"=>"","leave"=>"","level"=>"","sms"=>"","mail"=>"","page"=>"");
?>

<script language="javascript">
	function go_config(tStatus){
		var goUrl="admin.php?page=bbse_commerce_member&cType="+tStatus;
		window.location.href =goUrl;
	}

	function check_number(){
		var key = event.keyCode;
		if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
			event.returnValue = false;
		}
	}

	function go_config_option(cType,tMode,tIdx){
		var goStr="admin.php?page=bbse_commerce_member&cType="+cType;

		if(tMode) goStr +="&tMode="+tMode;
		if(tIdx) goStr +="&tIdx="+tIdx;

		window.location.href =goStr;
	}

	function remove_popup(){
		tb_remove();
	}
	
</script>

<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>회원관리</h2>
		<hr>
	</div>
	<div class="clearfix"></div>

	<div class="tabWrap">
	  <ul class="tabList">
		<li <?php echo ($cType=='list')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=list">회원목록</a></li>
		<li <?php echo ($cType=='config')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=config">환경설정</a></li>
		<li <?php echo ($cType=='social')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=social">소셜(간편)로그인</a></li>
		<li <?php echo ($cType=='leave')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=leave">탈퇴/삭제내역</a></li>
		<li <?php echo ($cType=='level')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=level">회원등급관리</a></li>
		<li <?php echo ($cType=='sms')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=sms">SMS관리</a></li>
		<li <?php echo ($cType=='mail')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=mail">메일관리</a></li>
		<li <?php echo ($cType=='page')?"class=\"active\"":"";?>><a href="admin.php?page=bbse_commerce_member&cType=page">페이지추가안내</a></li>
	  </ul>
	  <div class="clearfix"></div>
	</div>

	<div class="clearfix" style="margin-top:30px"></div>

	<?php 

		if($cType == 'list') {

			$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`", ARRAY_A);

			if($_POST['mvrun']=="del_proc"){
				$V = $_POST;
				$timeStamp=current_time('timestamp');

				// 체크박스 선택 삭제
				if((!empty($V['check']) && count($V['check'])) > 0 && ((!empty($V['tBatch1']) && $V['tBatch1'] == 'remove') or (!empty($V['tBatch2']) && $V['tBatch2'] == 'remove'))){
					for($i = 0; $i < count($V['check']); $i++){
						$cno = $V['check'][$i];
						if($cno){
							$rows1 = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_no`='".$cno."'", ARRAY_A);
							$rows2 = $wpdb->get_row("select * from `".$wpdb->users."` where `user_login`='".$rows1['user_id']."'", ARRAY_A);
							$wpdb->query("update `bbse_commerce_membership` set admin_del='1', leave_yn='1', leave_date='".$timeStamp."' where `user_no`='".$cno."'");
						}
					}
				
				// 개별 삭제
				}else{
					if(!empty($_POST['delNo'])){
						$cno = trim($_POST['delNo']);
						if($cno){
							$rows1 = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_no`='".$cno."'", ARRAY_A);
							$rows2 = $wpdb->get_row("select * from `".$wpdb->users."` where `user_login`='".$rows1['user_id']."'", ARRAY_A);
							$wpdb->query("update `bbse_commerce_membership` set admin_del='1', leave_yn='1', leave_date='".$timeStamp."' where `user_no`='".$cno."'");
						}
					}
				}
				echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_member&cType=list';</script>";
				exit;

			}else if($_POST['mvrun']=="email_proc"){

				$V = $_POST;
				$member_list = array();
				if((!empty($V['check']) && count($V['check'])) > 0 && ((!empty($V['tBatch1']) && $V['tBatch1'] == 'email') or (!empty($V['tBatch2']) && $V['tBatch2'] == 'email'))){

					echo "
						<form method='post' name='action_frm' action='admin.php?page=bbse_commerce_member&cType=mail'>
						<input type='hidden' name='member_list' value='".base64_encode(serialize($V['check']))."' />
						</form>
						<script type='text/javascript'>document.action_frm.submit();</script>";
					exit;

				}

			}else if($_POST['mvrun']=="sms_proc"){

				$V = $_POST;
				$member_list = array();
				if((!empty($V['check']) && count($V['check'])) > 0 && ((!empty($V['tBatch1']) && $V['tBatch1'] == 'sms') or (!empty($V['tBatch2']) && $V['tBatch2'] == 'sms'))){

					echo "
						<form method='post' name='action_frm' action='admin.php?page=bbse_commerce_member&cType=sms&mode=send'>
						<input type='hidden' name='member_list' value='".base64_encode(serialize($V['check']))."' />
						</form>
						<script type='text/javascript'>document.action_frm.submit();</script>";
					exit;

				}

			}else if($_POST['tPayMode']=="01" || $_POST['tPayMode']=="02"){
			    $V = $_POST;
			    $cnt = sizeof($V['check']);
			    $userArr = "";
			    if($cnt > '0'){
			    for($k=0;$k<$cnt;$k++){
			        $userArr .= "'";
			        $userArr .= $V['check'][$k];
			        $userArr .= "'";
			        if($k < $cnt-1){
			         $userArr .= ",";
			        }
			    }
			    $arr = explode(",", $userArr);
			    $query = "update `bbse_commerce_membership` set `payMode` = '".$V['tPayMode']."'  where user_no in ( $userArr ) ";
			    $wpdb->query("update `bbse_commerce_membership` set `payMode` = '".$V['tPayMode']."'  where user_no in ( $userArr ) ");
			    echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_member&cType=list';</script>";
			    exit;
			    }
			}
			else{
				$V = $_REQUEST;

				if($V['mode']=="add" && $V['mvrun'] == "add_proc"){
					$wCnt = $wpdb->get_var("select count(*) from `bbse_commerce_membership` where `user_id`='".trim($V['user_id'])."'");
					$eCnt = $wpdb->get_var("select count(*) from `bbse_commerce_membership` where `email`='".trim($V['email'])."'");

					if($wCnt > 0){echo "<script type='text/javascript'>alert('이미 존재하는 아이디입니다.');history.back();</script>";exit;}
					if(email_exists(trim($V['email']))==true || $eCnt > 0){echo "<script type='text/javascript'>alert('이미 존재하는 이메일입니다.');history.back();</script>";exit;}
					if(!empty($V['birth_year']) && !ctype_digit($V['birth_year'])){echo "<script type='text/javascript'>alert('생년월일(년)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['birth_month']) && !ctype_digit($V['birth_month'])){echo "<script type='text/javascript'>alert('생년월일(월)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['birth_day']) && !ctype_digit($V['birth_day'])){echo "<script type='text/javascript'>alert('생년월일(일)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_1']) && !ctype_digit($V['phone_1'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_2']) && !ctype_digit($V['phone_2'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_3']) && !ctype_digit($V['phone_3'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_1']) && !ctype_digit($V['hp_1'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_2']) && !ctype_digit($V['hp_2'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_3']) && !ctype_digit($V['hp_3'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}

					$fields1 = array();
					$qry1 = "INSERT INTO `bbse_commerce_membership` set ";

					if(isset($V['user_class'])) $fields1[] = "`user_class`='".$V['user_class']."'";
					if(!empty($V['pass'])){
						if($V['pass'] != $V['repass']){
							echo "<script type='text/javascript'>alert('비밀번호가 일치하지 않습니다.');history.back();</script>";
							exit;
						}
						$fields1[] = "`user_pass`=password('".$V['pass']."')";
					}
					$V['sms_reception'] = ($V['sms_reception'])?$V['sms_reception']:"0";
					$V['email_reception'] = ($V['email_reception'])?$V['email_reception']:"0";
					if(isset($V['user_id'])) $fields1[] = "`user_id`='".$V['user_id']."'";
					if(isset($V['name'])) $fields1[] = "`name`='".$V['name']."'";
					if(strlen($V['birth_month']) == 1) $V['birth_month'] = "0".$V['birth_month'];
					if(strlen($V['birth_day']) == 1) $V['birth_day'] = "0".$V['birth_day'];
					if(isset($V['birth_year']) && isset($V['birth_month']) && isset($V['birth_day'])) $V['birth'] = $V['birth_year']."-".$V['birth_month']."-".$V['birth_day'];
					if(isset($V['birth'])) $fields1[] = "`birth`='".$V['birth']."'";
					if(isset($V['sex'])) $fields1[] = "`sex`='".$V['sex']."'";
					if(isset($V['zipcode'])) $fields1[] = "`zipcode`='".$V['zipcode']."'";
					if(isset($V['addr1'])) $fields1[] = "`addr1`='".$V['addr1']."'";
					if(isset($V['addr2'])) $fields1[] = "`addr2`='".$V['addr2']."'";
					if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
					if(isset($V['phone'])) $fields1[] = "`phone`='".$V['phone']."'";
					if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
					if(isset($V['hp'])) $fields1[] = "`hp`='".$V['hp']."'";
					if(isset($V['sms_reception'])) $fields1[] = "`sms_reception`='".$V['sms_reception']."'";
					if(isset($V['email_reception'])) $fields1[] = "`email_reception`='".$V['email_reception']."'";
					if(isset($V['email'])) $fields1[] = "`email`='".$V['email']."'";
					if(isset($V['job'])) $fields1[] = "`job`='".$V['job']."'";
					if(isset($V['mileage'])) $fields1[] = "`mileage`='".$V['mileage']."'";
					if(isset($V['payMode'])) $fields1[] = "`payMode`='".$V['payMode']."'";
					if(isset($V['manager_name'])) $fields1[] = "`manager_name`='".$V['manager_name']."'";
					if(isset($V['screen_code'])) $fields1[] = "`screen_code`='".$V['screen_code']."'";
					
					$add_fields1 = implode(", ", $fields1);

					$qry1 .= $add_fields1;
					$current_time = current_time('timestamp');
					$wpdb->query($qry1.", reg_date='".$current_time."'");

					/* 워드프레스 사용자 추가 start (wp_users, wp_usermeta) */
					$wp_user['user_login'] = $V['user_id'];
					$wp_user['user_pass'] = $V['pass'];
					$wp_user['user_email'] = $V['email'];
					$wp_user_no = wp_create_user($wp_user['user_login'], $wp_user['user_pass'], $wp_user['user_email']);

					if($V['user_class']=='1') $uRole='administrator';
					else $uRole='subscriber';
					wp_update_user( array ('ID' => $wp_user_no, 'role' => $uRole ) ) ;
					/* 워드프레스 사용자 추가 end (wp_users, wp_usermeta) */

					echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_member&cType=list';</script>";
					exit;

				}else if($V['mode']=="edit" && $V['mvrun'] == "edit_proc"){

					if(!empty($V['birth_year']) && !ctype_digit($V['birth_year'])){echo "<script type='text/javascript'>alert('생년월일(년)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['birth_month']) && !ctype_digit($V['birth_month'])){echo "<script type='text/javascript'>alert('생년월일(월)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['birth_day']) && !ctype_digit($V['birth_day'])){echo "<script type='text/javascript'>alert('생년월일(일)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_1']) && !ctype_digit($V['phone_1'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_2']) && !ctype_digit($V['phone_2'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_3']) && !ctype_digit($V['phone_3'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_1']) && !ctype_digit($V['hp_1'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_2']) && !ctype_digit($V['hp_2'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_3']) && !ctype_digit($V['hp_3'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}

					$edit_mode = "edit";
					$rows = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_no`='".$V['user_no']."'", ARRAY_A);
				
					if(is_array($rows)){
						/* bbse_commerce_membership 수정 */
						$fields1 = array();
						$qry1 = "update `bbse_commerce_membership` set ";

						if(isset($V['user_class'])) $fields1[] = "`user_class`='".$V['user_class']."'";
						if(!empty($V['pass'])){
							if($V['pass'] != $V['repass']){
								echo "<script type='text/javascript'>alert('비밀번호가 일치하지 않습니다.');history.back();</script>";
								exit;
							}
							$fields1[] = "`user_pass`=password('".$V['pass']."')";
						}
						$V['sms_reception'] = ($V['sms_reception'])?$V['sms_reception']:"0";
						$V['email_reception'] = ($V['email_reception'])?$V['email_reception']:"0";
						if(isset($V['name'])) $fields1[] = "`name`='".$V['name']."'";
						if(strlen($V['birth_month']) == 1) $V['birth_month'] = "0".$V['birth_month'];
						if(strlen($V['birth_day']) == 1) $V['birth_day'] = "0".$V['birth_day'];
						if(isset($V['birth_year']) && isset($V['birth_month']) && isset($V['birth_day'])) $V['birth'] = $V['birth_year']."-".$V['birth_month']."-".$V['birth_day'];
						if(isset($V['birth'])) $fields1[] = "`birth`='".$V['birth']."'";
						if(isset($V['sex'])) $fields1[] = "`sex`='".$V['sex']."'";
						if(isset($V['zipcode'])) $fields1[] = "`zipcode`='".$V['zipcode']."'";
						if(isset($V['addr1'])) $fields1[] = "`addr1`='".$V['addr1']."'";
						if(isset($V['addr2'])) $fields1[] = "`addr2`='".$V['addr2']."'";
						if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
						if(isset($V['phone'])) $fields1[] = "`phone`='".$V['phone']."'";
						if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
						if(isset($V['hp'])) $fields1[] = "`hp`='".$V['hp']."'";
						if(isset($V['sms_reception'])) $fields1[] = "`sms_reception`='".$V['sms_reception']."'";
						if(isset($V['email_reception'])) $fields1[] = "`email_reception`='".$V['email_reception']."'";
						if(isset($V['email'])) $fields1[] = "`email`='".$V['email']."'";
						if(isset($V['job'])) $fields1[] = "`job`='".$V['job']."'";
						if(isset($V['mileage'])) $fields1[] = "`mileage`='".$V['mileage']."'";
						if(isset($V['admin_log'])) $fields1[] = "`admin_log`='".$V['admin_log']."'";
						if(isset($V['payMode'])) $fields1[] = "`payMode`='".$V['payMode']."'";
						if(isset($V['manager_name'])) $fields1[] = "`manager_name`='".$V['manager_name']."'";
						if(isset($V['screen_code'])) $fields1[] = "`screen_code`='".$V['screen_code']."'";

						$edit_fields1 = implode(", ", $fields1);

						$qry1 .= $edit_fields1." where `user_no`='".$rows['user_no']."'";
						
						//적립금 수정 시 내역 추가
						$mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_no='".$rows['user_no']."'");
						$old_point 	=$mData->mileage;
						//다를 경우만 추가
						if($mData->mileage != $V['mileage']){
							$new_point 	= $V['mileage'];
							$earn_point = $V['mileage'] - $mData->mileage;
							$user_id	= $mData->user_id;
							$user_name	= $V['name'];
							$etc_idx	= 'admin';
							$reg_date=current_time('timestamp');
							$earn_mode = "";							
							if($earn_point > 0){
								$earn_mode = "IN";	
							}
							else{
								$earn_mode = "OUT";
								$earn_point *= -1;
							}
							
							$res2 = $wpdb->query("INSERT INTO 
								bbse_commerce_earn_log (earn_mode,earn_type,earn_point,old_point,user_id,user_name,etc_idx,reg_date) 
								VALUE('".$earn_mode."','admin','".$earn_point."','".$old_point."','".$user_id."','".$user_name."','".$etc_idx."','".$reg_date."')");	
						}
						
						$wpdb->query($qry1);
						

						/* 워드프레스 사용자 정보수정 start (wp_users, wp_usermeta) */
						$wp_user_no = $wpdb->get_var("select `ID` from ".$wpdb->users." where `user_login`='".$rows['user_id']."'");
						$wp_user['ID'] = $wp_user_no;
						$wp_user['user_login'] = $rows['user_id'];
						$wp_user['user_email'] = $V['email'];
						if($V['user_class']=='1') $wp_user['role']='administrator';
						else $wp_user['role']='subscriber';
						if(!empty($V['pass'])) $wp_user['user_pass'] = $V['pass'];
						
						// 일반 쿼리문으로 비밀번호 수정시 로그아웃 처리되므로 반드시 wp_update_user 함수를 사용
						wp_update_user($wp_user);
						/* 워드프레스 사용자 정보수정 end (wp_users, wp_usermeta) */
					}

					$result = $BBSeCommerceMember->getMemberView($V['user_no']);
					$EDIT_PAGE = "list";
					require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-member-detail.php");
				
				}else{

					if($_GET['mode'] == "add" || $_GET['mode'] == "edit") {

						if($_GET['user_no']) {
							$result = $BBSeCommerceMember->getMemberView($_GET['user_no']);
						}
						$EDIT_PAGE = "list";
						require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-member-detail.php");
					}elseif($_GET['mode'] == "download"){
						require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-member-download.php");
					}else{

						if(!empty($_GET['orderby'])) $orderby = $_GET['orderby'];
						else $orderby = "";
						if(!empty($_GET['order'])) $order = $_GET['order'];
						else $order = "";

						require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-member-list.php");
					}

				}

			}

		}else if($cType == "config") {

			$config = $wpdb->get_row("select * from bbse_commerce_membership_config", ARRAY_A);
			$EDIT_CONFIG_URL = "admin.php?page=bbse_commerce_member&cType=config&mode=edit";

			if(!empty($_GET['mode']) && $_GET['mode'] == "edit"){
				$V = $_POST;

				if(empty($V['use_name'])) $V['use_name'] = 0;
				if(empty($V['validate_name'])) $V['validate_name'] = 0;
				if(empty($V['use_addr'])) $V['use_addr'] = 0;
				if(empty($V['validate_addr'])) $V['validate_addr'] = 0;
				if(empty($V['use_zipcode_api'])) $V['use_zipcode_api'] = 0;
				if(empty($V['zipcode_api_module'])) $V['zipcode_api_module'] = 0;
				if($V['zipcode_api_module'] == 0) $V['zipcode_api_key'] = "";
				if(empty($V['use_birth'])) $V['use_birth'] = 0;
				if(empty($V['validate_birth'])) $V['validate_birth'] = 0;
				if(empty($V['use_phone'])) $V['use_phone'] = 0;
				if(empty($V['validate_phone'])) $V['validate_phone'] = 0;
				if(empty($V['use_hp'])) $V['use_hp'] = 0;
				if(empty($V['validate_hp'])) $V['validate_hp'] = 0;
				if(empty($V['use_sex'])) $V['use_sex'] = 0;
				if(empty($V['validate_sex'])) $V['validate_sex'] = 0;
				if(empty($V['use_job'])) $V['use_job'] = 0;
				if(empty($V['validate_job'])) $V['validate_job'] = 0;
				if(empty($V['use_join_email'])) $V['use_join_email'] = 0;
				if(empty($V['payMode'])) $V['payMode'] = 0;
				
				if(!ctype_digit($V['id_min_len'])){echo "<script type='text/javascript'>alert('아이디 최소길이는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				if(!ctype_digit($V['pass_min_len'])){echo "<script type='text/javascript'>alert('비밀번호 최소길이는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				

				if(is_array($config)){
					if(!empty($V['mail_logo'])) $logo_query = ", `mail_logo`='".$V['mail_logo']."'";
					else $logo_query = "";
				
					if(!empty($V['mail_logo_del']) && $V['mail_logo_del'] == 1) $logo_query = ", `mail_logo`=''";

					$wpdb->query("update `bbse_commerce_membership_config` set `use_name`='".$V['use_name']."', `validate_name`='".$V['validate_name']."', `use_addr`='".$V['use_addr']."', `validate_addr`='".$V['validate_addr']."', `use_zipcode_api`='".$V['use_zipcode_api']."', `use_birth`='".$V['use_birth']."', `validate_birth`='".$V['validate_birth']."', `use_phone`='".$V['use_phone']."', `validate_phone`='".$V['validate_phone']."', `use_hp`='".$V['use_hp']."', `validate_hp`='".$V['validate_hp']."', `use_sex`='".$V['use_sex']."', `validate_sex`='".$V['validate_sex']."', `use_job`='".$V['use_job']."', `validate_job`='".$V['validate_job']."', `zipcode_api_module`='".$V['zipcode_api_module']."', `zipcode_api_key`='".$V['zipcode_api_key']."', `id_min_len`='".$V['id_min_len']."', `pass_min_len`='".$V['pass_min_len']."', `join_not_id`='".$V['join_not_id']."', `use_join_email`='".$V['use_join_email']."', `from_email`='".$V['from_email']."', `from_name`='".$V['from_name']."', `join_email_title`='".$V['join_email_title']."', `join_email_content`='".$V['join_email_content']."', `join_agreement`='".$V['join_agreement']."', `join_private`='".$V['join_private']."', `certification_yn`='".$V['certification_yn']."', `certification_company`='".$V['certification_company']."', `certification_id`='".$V['certification_id']."', `certification_pass`='".$V['certification_pass']."', `certification_key`='".$V['certification_key']."', `guest_order`='".$V['guest_order']."', `guest_order_view`='".$V['guest_order_view']."', `join_default_class`='".$V['join_default_class']."'".$logo_query);
				}else{
					if(!empty($V['mail_logo'])){ 
						$logo['key'] = ", `mail_logo`";
						$logo['val'] = ", '".$V['mail_logo']."'`";
					}else{ 
						$logo['key'] = "";
						$logo['val'] = "";
					}

					if($V['mail_logo_del'] == 1){
						$logo['key'] = ", `mail_logo`";
						$logo['val'] = ", ''";
					}

					$wpdb->query("insert into `bbse_commerce_membership_config` (`use_name`, `validate_name`, `use_addr`, `validate_addr`, `use_zipcode_api`, `use_birth`, `validate_birth`, `use_phone`, `validate_phone`, `use_hp`, `validate_hp`, `use_sex`, `validate_sex`, `use_job`, `validate_job`, `zipcode_api_module`, `zipcode_api_key`, `id_min_len`, `pass_min_len`, `join_not_id`, `use_join_email`, `from_email`, `from_name`, `join_email_title`, `join_email_content`, `join_agreement`, `join_private`, `certification_yn`, `certification_company`, `certification_id`, `certification_pass`, `certification_key`, `guest_order`, `guest_order_view`, `join_default_class`".$logo['key'].") values ('".$V['use_name']."', '".$V['validate_name']."', '".$V['use_addr']."', '".$V['validate_addr']."', '".$V['use_zipcode_api']."', '".$V['use_birth']."', '".$V['validate_birth']."', '".$V['use_phone']."', '".$V['validate_phone']."', '".$V['use_hp']."', '".$V['validate_hp']."', '".$V['use_sex']."', '".$V['validate_sex']."', '".$V['use_job']."', '".$V['validate_job']."', '".$V['zipcode_api_module']."', '".$V['zipcode_api_key']."', '".$V['id_min_len']."', '".$V['pass_min_len']."', '".$V['join_not_id']."', '".$V['use_join_email']."', '".$V['from_email']."', '".$V['from_name']."', '".$V['join_email_title']."', '".$V['join_email_content']."', '".$V['join_agreement']."', '".$V['join_private']."'".$logo['val'].", '".$V['certification_yn']."', '".$V['certification_company']."', '".$V['certification_id']."' ,'".$V['certification_pass']."' ,'".$V['certification_key']."' ,'".$V['guest_order']."', '".$V['guest_order_view']."','".$V['join_default_class']."'".$logo['val'].")");

				}

				update_option("bbse_commerce_login_page", $V['login_page']);
				update_option("bbse_commerce_join_page", $V['join_page']);
				update_option("bbse_commerce_id_search_page", $V['id_search_page']);
				update_option("bbse_commerce_pass_search_page", $V['pass_search_page']);
				update_option("bbse_commerce_delete_page", $V['delete_page']);
				
				echo "
					<form method='post' name='action_frm' action='admin.php?page=bbse_commerce_member&cType=config'>
					<input type='hidden' name='save_check' value='1' />
					</form>
					<script type='text/javascript'>document.action_frm.submit();</script>";
			}

			if(!empty($_POST['save_check']) && $_POST['save_check'] == "1") $edit_mode = "edit";
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH.'admin/member/bbse-commerce-member-config.php');


		}else if($cType == "leave") {

			if($_POST['mvrun']=="leave_proc"){
				
				$V = $_POST;

				// 체크박스 선택 삭제
				if((!empty($V['check']) && count($V['check'])) > 0 && ((!empty($V['tBatch1']) && $V['tBatch1'] == 'leave') or (!empty($V['tBatch2']) && $V['tBatch2'] == 'leave'))){
					for($i = 0; $i < count($V['check']); $i++){
						$cno = $V['check'][$i];
						if($cno){
							//$wpdb->query("update `bbse_commerce_membership` set admin_del='1', leave_yn='1' where `user_no`='".$cno."'");
							$rows1 = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_no`='".$cno."'", ARRAY_A);
							$rows2 = $wpdb->get_row("select * from `".$wpdb->users."` where `user_login`='".$rows1['user_id']."'", ARRAY_A);
							wp_delete_user($rows2['ID']);
							$wpdb->query("delete from `bbse_commerce_membership` where `user_no`='".$cno."'");
							
						}
					}
				
				// 개별 삭제
				}else{
					if(!empty($_POST['delNo'])){
						$cno = trim($_POST['delNo']);
						if($cno){
							//$wpdb->query("update `bbse_commerce_membership` set admin_del='1', leave_yn='1' where `user_no`='".$cno."'");
							$rows1 = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_no`='".$cno."'", ARRAY_A);
							$rows2 = $wpdb->get_row("select * from `".$wpdb->users."` where `user_login`='".$rows1['user_id']."'", ARRAY_A);
							wp_delete_user($rows2['ID']);
							$wpdb->query("delete from `bbse_commerce_membership` where `user_no`='".$cno."'");
						}
					}
				}
				echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_member&cType=leave';</script>";
				exit;

			}else if($_POST['mvrun']=="restore_proc"){

				$V = $_POST;

				// 체크박스 선택 삭제
				if((!empty($V['check']) && count($V['check'])) > 0 && ((!empty($V['tBatch1']) && $V['tBatch1'] == 'restore') or (!empty($V['tBatch2']) && $V['tBatch2'] == 'restore'))){
					for($i = 0; $i < count($V['check']); $i++){
						$cno = $V['check'][$i];
						if($cno){
							$wpdb->query("update `bbse_commerce_membership` set leave_yn='0', leave_date='0', leave_reason='', admin_del='0' where `user_no`='".$cno."'");
						}
					}
				
				// 개별 삭제
				}else{
					if(!empty($_POST['delNo'])){
						$cno = trim($_POST['delNo']);
						if($cno){
							$wpdb->query("update `bbse_commerce_membership` set leave_yn='0', leave_date='0', leave_reason='', admin_del='0' where `user_no`='".$cno."'");
						}
					}
				}
				echo "<script type='text/javascript'>location.href='admin.php?page=bbse_commerce_member&cType=leave';</script>";
				exit;
			
			}else{

				$V = $_POST;
				$BBSeCommerceMember = new BBSeCommerceMember;
				$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`", ARRAY_A);

				if(!empty($V['mode']) && $V['mode'] == "edit"){
					if(!empty($V['birth_year']) && !ctype_digit($V['birth_year'])){echo "<script type='text/javascript'>alert('생년월일(년)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['birth_month']) && !ctype_digit($V['birth_month'])){echo "<script type='text/javascript'>alert('생년월일(월)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['birth_day']) && !ctype_digit($V['birth_day'])){echo "<script type='text/javascript'>alert('생년월일(일)은 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_1']) && !ctype_digit($V['phone_1'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_2']) && !ctype_digit($V['phone_2'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['phone_3']) && !ctype_digit($V['phone_3'])){echo "<script type='text/javascript'>alert('전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_1']) && !ctype_digit($V['hp_1'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_2']) && !ctype_digit($V['hp_2'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
					if(!empty($V['hp_3']) && !ctype_digit($V['hp_3'])){echo "<script type='text/javascript'>alert('휴대전화번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}

					$edit_mode = "edit";
					$rows = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_no`='".$_GET['user_no']."'", ARRAY_A);
				
					if(is_array($rows)){
						/* bbse_commerce_membership 수정 */
						$fields1 = array();
						$qry1 = "update `bbse_commerce_membership` set ";

						if(!empty($V['pass'])){
							if($V['pass'] != $V['repass']){
								echo "<script type='text/javascript'>alert('비밀번호가 일치하지 않습니다.');history.back();</script>";
								exit;
							}
							$fields1[] = "`user_pass`=password('".$V['pass']."')";
						}
						if(isset($V['name'])) $fields1[] = "`name`='".$V['name']."'";
						if(strlen($V['birth_month']) == 1) $V['birth_month'] = "0".$V['birth_month'];
						if(strlen($V['birth_day']) == 1) $V['birth_day'] = "0".$V['birth_day'];
						if(isset($V['birth_year']) && isset($V['birth_month']) && isset($V['birth_day'])) $V['birth'] = $V['birth_year']."-".$V['birth_month']."-".$V['birth_day'];
						if(isset($V['birth'])) $fields1[] = "`birth`='".$V['birth']."'";
						if(isset($V['sex'])) $fields1[] = "`sex`='".$V['sex']."'";
						if(isset($V['zipcode'])) $fields1[] = "`zipcode`='".$V['zipcode']."'";
						if(isset($V['addr1'])) $fields1[] = "`addr1`='".$V['addr1']."'";
						if(isset($V['addr2'])) $fields1[] = "`addr2`='".$V['addr2']."'";
						if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
						if(isset($V['phone'])) $fields1[] = "`phone`='".$V['phone']."'";
						if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
						if(isset($V['hp'])) $fields1[] = "`hp`='".$V['hp']."'";
						if(isset($V['sms_reception'])) $fields1[] = "`sms_reception`='".$V['sms_reception']."'";
						if(isset($V['email'])) $fields1[] = "`email`='".$V['email']."'";
						if(isset($V['job'])) $fields1[] = "`job`='".$V['job']."'";
						if(isset($V['payMode'])) $fields1[] = "`payMode`='".$V['payMode']."'";
						if(isset($V['manager_name'])) $fields1[] = "`manager_name`='".$V['manager_name']."'";

						$edit_fields1 = implode(", ", $fields1);

						$qry1 .= $edit_fields1." where `user_no`='".$rows['user_no']."'";
						$wpdb->query($qry1);
						

						/* 워드프레스 사용자 정보수정 start (wp_users, wp_usermeta) */
						$wp_user_no = $wpdb->get_var("select `ID` from ".$wpdb->users." where `user_login`='".$rows['user_id']."'");
						$wp_user['ID'] = $wp_user_no;
						$wp_user['user_login'] = $rows['user_id'];
						$wp_user['user_email'] = $V['email'];
						if(!empty($V['pass'])) $wp_user['user_pass'] = $V['pass'];
						
						// 일반 쿼리문으로 비밀번호 수정시 로그아웃 처리되므로 반드시 wp_update_user 함수를 사용
						wp_update_user($wp_user);
						/* 워드프레스 사용자 정보수정 end (wp_users, wp_usermeta) */
					}

					$result = $BBSeCommerceMember->getMemberView($_GET['user_no']);
					$EDIT_PAGE = "leave";
					require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-member-detail.php");

				}else{
					$edit_mode = "";
					require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-member-leave.php");
				}

			}

		}else if($cType == 'level') {

			$BBSeCommerceMember = new BBSeCommerceMember;
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH.'admin/member/bbse-commerce-member-level.php');

		}else if($cType == 'sms') {

			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."lib/nusoap.php");

			$V = $_POST;
			$saveFlag = "";
			$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`", ARRAY_A);
			$tUrl = BBSE_COMMERCE_PLUGIN_WEB_URL;
			if(!empty($_GET['mode']) && $_GET['mode'] == "send"){//개별SMS발송

				$V = $_POST;
				if($V['mvrun']=="send_proc") {
					$V['rcvNumber'] = trim($V['rcvNumber']);
					$listCount = 0;
					$okCount = 0;
					$failCount = 0;
					$sendList = explode("\r\n",$V['rcvNumber']);
					if($config['sms_admin_tel']=="") {
						$alertMsg = "발신자번호를 먼저 설정해주세요.";
					}else{
						$adminTel = $config['sms_callback_tel'];
						for($i=0;$i<count($sendList);$i++) {
							if($sendList[$i]!="") {
								$ret = bbse_commerce_ezsms_send($sendList[$i],$adminTel, $V['msg'],"SMS","D","");
								if($ret=="success") {
									$okCount++;
								}else{
									$failCount++;
								}
								$listCount++;
							}
						}
						$alertMsg = "총 ".number_format($listCount)."건 발송(성공:".number_format($okCount)."건 / 실패:".number_format($failCount)."건)";
						echo "<script type='text/javascript'>alert('".$alertMsg."');location.href='admin.php?page=bbse_commerce_member&cType=sms&mode=send';</script>";
						exit;
					}
				}
				$BBSeCommerceMember = new BBSeCommerceMember;
				require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH.'admin/member/bbse-commerce-member-sms-send.php');

			}else{
				$replaceVars = array(
					"[user_id]"=>"회원아이디",
					"[cp_name]"=>"사이트명",
					"[goods]"=>"상품명",
					"[order_no]"=>"주문번호",
					"[delivery_name]"=>"택배사명",
					"[delivery_no]"=>"운송장번호",
				);
				$defaultUserMsg = array(
					"join"=>"[user_id]님 회원가입을 축하드립니다. 감사합니다.\n[cp_name]",
					"order"=>"[[goods]] 상품을(주문번호:[order_no])을 구매해주셔서 감사합니다.\n[cp_name]",
					"pay"=>"[[goods]] 상품이(주문번호:[order_no]) 결제완료되었습니다. 감사합니다.\n[cp_name]",
					"delivery"=>"[[goods]] 상품이 배송되었습니다. [delivery_name]([delivery_no]) [cp_name]"
				);
				$defaultAdminMsg = array(
					"join"=>"[user_id]님이 신규회원으로 가입되었습니다.",
					"order"=>"[user_id]님의 주문건이 접수되었습니다. [[goods]] (주문번호:[order_no])",
					"pay"=>"[user_id]님의 주문건이 결제왼료되었습니다. [[goods]] (주문번호:[order_no])",
					"delivery"=>"[user_id]님의 주문건을 배송하였습니다. [[goods]] (주문번호:[order_no]) [delivery_name]([delivery_no])"
				);

				if(empty($V['sms_use_yn'])) $V['sms_use_yn'] = "N";
				if(empty($V['sms_080_yn'])) $V['sms_080_yn'] = "N";

				if(empty($V['sms_join_yn'])) $V['sms_join_yn'] = "N";
				if(empty($V['sms_join_admin_yn'])) $V['sms_join_admin_yn'] = "N";

				if(empty($V['sms_order_yn'])) $V['sms_order_yn'] = "N";
				if(empty($V['sms_order_admin_yn'])) $V['sms_order_admin_yn'] = "N";

				if(empty($V['sms_delivery_yn'])) $V['sms_delivery_yn'] = "N";
				if(empty($V['sms_delivery_admin_yn'])) $V['sms_delivery_admin_yn'] = "N";

				if(empty($V['sms_pay_yn'])) $V['sms_pay_yn'] = "N";
				if(empty($V['sms_pay_admin_yn'])) $V['sms_pay_admin_yn'] = "N";


				if(!empty($V['tMode']) && $V['tMode'] == "save"){
					if(!empty($V['sms_id'])) update_option("bbse_commerce_sms_id", $V['sms_id']);								// SMS ID
					if(!empty($V['sms_key'])) update_option("bbse_commerce_sms_key", $V['sms_key']);						// SMS key
					if(!empty($V['sms_keydate'])) update_option("bbse_commerce_sms_keydate", $V['sms_keydate']);	// SMS key 생성일자

					if(is_array($config)){
						$wpdb->query("update `bbse_commerce_membership_config` set `sms_use_yn`='".$V['sms_use_yn']."', `sms_080_yn`='".$V['sms_080_yn']."', `sms_callback_tel`='".$V['sms_callback_tel']."', `sms_admin_tel`='".$V['sms_admin_tel']."', `sms_join_yn`='".$V['sms_join_yn']."', `sms_join_msg`='".$V['sms_join_msg']."', `sms_join_admin_yn`='".$V['sms_join_admin_yn']."', `sms_join_admin_msg`='".$V['sms_join_admin_msg']."',`sms_order_yn`='".$V['sms_order_yn']."', `sms_order_msg`='".$V['sms_order_msg']."', `sms_order_admin_yn`='".$V['sms_order_admin_yn']."', `sms_order_admin_msg`='".$V['sms_order_admin_msg']."',`sms_delivery_yn`='".$V['sms_delivery_yn']."', `sms_delivery_msg`='".$V['sms_delivery_msg']."', `sms_delivery_admin_yn`='".$V['sms_delivery_admin_yn']."', `sms_delivery_admin_msg`='".$V['sms_delivery_admin_msg']."',`sms_pay_yn`='".$V['sms_pay_yn']."', `sms_pay_msg`='".$V['sms_pay_msg']."', `sms_pay_admin_yn`='".$V['sms_pay_admin_yn']."', `sms_pay_admin_msg`='".$V['sms_pay_admin_msg']."' ");
					}
					$saveFlag = "save";
				}else if(!empty($V['tMode']) && $V['tMode'] == "reset"){
					delete_option("bbse_commerce_sms_id");
					delete_option("bbse_commerce_sms_key");
					delete_option("bbse_commerce_sms_keydate");
					if(is_array($config)){
						$wpdb->query("update `bbse_commerce_membership_config` set `sms_use_yn`='', `sms_080_yn`='', `sms_callback_tel`='', `sms_admin_tel`='', `sms_join_yn`='', `sms_join_msg`='', `sms_join_admin_yn`='', `sms_join_admin_msg`='',`sms_order_yn`='', `sms_order_msg`='', `sms_order_admin_yn`='', `sms_order_admin_msg`='',`sms_delivery_yn`='', `sms_delivery_msg`='', `sms_delivery_admin_yn`='', `sms_delivery_admin_msg`='',`sms_pay_yn`='', `sms_pay_msg`='', `sms_pay_admin_yn`='', `sms_pay_admin_msg`=''");
					}
					$saveFlag = "reset";
				}
				$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`", ARRAY_A);
				$BBSeCommerceMember = new BBSeCommerceMember;
				require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH.'admin/member/bbse-commerce-member-sms.php');
			}

		}else if($cType == 'mail') {

			$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`", ARRAY_A);

			$V = $_POST;

			if($V['mvrun'] == "email_proc") {
				require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH.'admin/member/bbse-commerce-member-mail-send.php');
			}else{
				require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH.'admin/member/bbse-commerce-member-mail.php');
			}

		}else if($cType == "social") {

			$V = $_POST;

			if(!empty($V['tMode']) && $V['tMode'] == "save"){
				$socialArray=Array();
				$socialArray['integration']['integration_use_yn']=$V['integration_use_yn'];
				$socialArray['integration']['integration_view_type']=$V['integration_view_type'];
				$socialArray['integration']['integration_period']=$V['integration_period'];
				$socialArray['integration']['sns_icon_use_yn']=$V['sns_icon_use_yn'];

				$socialArray['naver']['naver_use_yn']=$V['naver_use_yn'];
				$socialArray['naver']['naver_client_id']=trim($V['naver_client_id']);
				$socialArray['naver']['naver_client_secret']=trim($V['naver_client_secret']);

				$socialArray['facebook']['facebook_use_yn']=$V['facebook_use_yn'];
				$socialArray['facebook']['facebook_app_id']=trim($V['facebook_app_id']);
				$socialArray['facebook']['facebook_app_secret']=trim($V['facebook_app_secret']);

				$socialArray['daum']['daum_use_yn']=$V['daum_use_yn'];
				$socialArray['daum']['daum_client_id']=trim($V['daum_client_id']);
				$socialArray['daum']['daum_client_secret']=trim($V['daum_client_secret']);

				$socialArray['google']['google_use_yn']=$V['google_use_yn'];
				$socialArray['google']['google_client_id']=trim($V['google_client_id']);
				$socialArray['google']['google_client_secret']=trim($V['google_client_secret']);

				$socialArray['kakao']['kakao_use_yn']=$V['kakao_use_yn'];
				$socialArray['kakao']['kakao_rest_api_key']=trim($V['kakao_rest_api_key']);

				$socialArray['twitter']['twitter_use_yn']=$V['twitter_use_yn'];
				$socialArray['twitter']['twitter_api_key']=trim($V['twitter_api_key']);
				$socialArray['twitter']['twitter_api_secret']=trim($V['twitter_api_secret']);

				$socialConf=serialize($socialArray);
				$wpdb->query("update `bbse_commerce_membership_config` set `social_login`='".$socialConf."'");
				$saveFlag = "save";
			}
			else if(!empty($V['tMode']) && $V['tMode'] == "reset"){
				$wpdb->query("update `bbse_commerce_membership_config` set `social_login`=''");
				$saveFlag = "reset";
			}

			$cnfData=$wpdb->get_row("select social_login from `bbse_commerce_membership_config`", ARRAY_A);
			$config=unserialize($cnfData['social_login']);
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH.'admin/member/bbse-commerce-member-social.php');

		}else{

			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/member/bbse-commerce-member-".$cType.".php");

		}
	?>
	<div class="clearfix" style="height:20px;"></div>
</div>


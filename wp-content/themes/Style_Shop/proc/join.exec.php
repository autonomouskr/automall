<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

//Set a cookie now to see if they are supported by the browser.
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if(SITECOOKIEPATH != COOKIEPATH)
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

function check_email($str){
	$email_match = "/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i";
	if(preg_match($email_match, $str)){
		return false;
	}else{
		return true;
	}
}

if(!function_exists("json_encode")){ 
    function json_encode($a=false){ 
        if(is_null($a)) return 'null'; 
        if($a === false) return 'false'; 
        if($a === true) return 'true'; 
        if(is_scalar($a)){ 
            if(is_float($a)) return floatval(str_replace(",", ".", strval($a))); 
            if(is_string($a)){ 
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')); 
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"'; 
            } else return $a; 
        } 
        $isList = true; 
        for($i=0, reset($a); $i<count($a); $i++, next($a)){ 
            if(key($a) !== $i){ 
                $isList = false; 
                break; 
            } 
        } 
        $result = array(); 
        if($isList){ 
            foreach($a as $v) $result[] = json_encode($v); 
            return '[' . join(',', $result) . ']'; 
        } else{ 
            foreach($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v); 
            return '{' . join(',', $result) . '}'; 
        } 
    } 
}

$V = $_GET;
$json_data = array();
$reg_date = current_time('timestamp');
if(mb_strlen($V['birth_month']) == 1) $V['birth_month'] = "0".$V['birth_month'];
if(mb_strlen($V['birth_day']) == 1) $V['birth_day'] = "0".$V['birth_day'];
if(isset($V['birth_year']) && isset($V['birth_month']) && isset($V['birth_day'])) $V['birth'] = $V['birth_year']."-".$V['birth_month']."-".$V['birth_day'];
else $V['birth'] = "";
if(isset($V['phone_1']) && isset($V['phone_2']) && isset($V['phone_3'])) $V['phone'] = $V['phone_1']."-".$V['phone_2']."-".$V['phone_3'];
else $V['phone'] = "";
if(isset($V['hp_1']) && isset($V['hp_2']) && isset($V['hp_3'])) $V['hp'] = $V['hp_1']."-".$V['hp_2']."-".$V['hp_3'];
else $V['hp'] = "";
if(!isset($V['sms_reception'])) $V['sms_reception'] = 0;
if(!isset($V['email_reception'])) $V['email_reception'] = 0;

$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`", ARRAY_A);

if(!empty($config['join_not_id'])){
	$not_id_arr = explode(",", $config['join_not_id']);
	for($i = 0; $i < count($not_id_arr); $i++){
		$not_id_arr[$i] = str_replace(" ", "", $not_id_arr[$i]);
	}
}

if(!empty($V['email'])){
	$users1 = $wpdb->get_row("select * from `bbse_commerce_membership` where `email`='".$V['email']."'");
	$users2 = $wpdb->get_row("select * from `".$wpdb->users."` where `user_email`='".$V['email']."'");
	
	if($V['mode'] == "edit"){
		$current_user = wp_get_current_user();	
		if($current_user->user_login != $users1->user_id){
			if(!empty($users1->email) || !empty($users2->user_email)){echo $V['callback']."([";$json_data['result'] = "exist email";echo json_encode($json_data);echo "])";exit;}
		}
	}else{
		if(!empty($users1->email) || !empty($users2->user_email)){echo $V['callback']."([";$json_data['result'] = "exist email";echo json_encode($json_data);echo "])";exit;}
	}
}

if($V['mode'] == "write"){
	// 중복 저장 방지
	$wCnt = $wCnt = $wpdb->get_var("select count(*) from `bbse_commerce_membership` where `user_id`='".$V['user_id']."'");
	if($wCnt <= 0){
		if(empty($V['user_id'])){echo $V['callback']."([";$json_data['result'] = "empty id";echo json_encode($json_data);echo "])";exit;}
		if($config['id_min_len'] > 0){if(mb_strlen($V['user_id']) < $config['id_min_len']){echo $V['callback']."([";$json_data['result'] = "short id";echo json_encode($json_data);echo "])";exit;}}
		if(mb_strlen($V['user_id']) > 16){echo $V['callback']."([";$json_data['result'] = "long id";echo json_encode($json_data);echo "])";exit;}
		if($V['id_checked'] != "y"){if($V['id_checked'] == "n"){echo $V['callback']."([";$json_data['result'] = "exist id";echo json_encode($json_data);echo "])";exit;}else{echo $V['callback']."([";$json_data['result'] = "check id";echo json_encode($json_data);echo "])";exit;}}
		if(!ctype_alnum($V['user_id'])){echo $V['callback']."([";$json_data['result'] = "error id";echo json_encode($json_data);echo "])";exit;}
		if(isset($not_id_arr)){
			if(in_array($V['user_id'], $not_id_arr)){echo $V['callback']."([";$json_data['result'] = "join not id";echo json_encode($json_data);echo "])";exit;}
		}
		if(empty($V['user_name']) && ($config['use_name'] == 1 && $config['validate_name'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty name";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['email'])){echo $V['callback']."([";$json_data['result'] = "empty email";echo json_encode($json_data);echo "])";exit;}
		if(check_email($V['email']) == true){echo $V['callback']."([";$json_data['result'] = "not form email";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['pass'])){echo $V['callback']."([";$json_data['result'] = "empty pass";echo json_encode($json_data);echo "])";exit;}
		if($config['pass_min_len'] > 0){if(mb_strlen($V['pass']) < $config['pass_min_len']){echo $V['callback']."([";$json_data['result'] = "short pass";echo json_encode($json_data);echo "])";exit;}}
		if(mb_strlen($V['pass']) > 16){echo $V['callback']."([";$json_data['result'] = "long pass";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['repass'])){echo $V['callback']."([";$json_data['result'] = "empty repass";echo json_encode($json_data);echo "])";exit;}
		if($V['pass'] != $V['repass']){echo $V['callback']."([";$json_data['result'] = "password mismatch";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['birth_year']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty birth_year";echo json_encode($json_data);echo "])";exit;}
		if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_year'] < 1900 || $V['birth_year'] > date("Y"))){echo $V['callback']."([";$json_data['result'] = "incorrect birth_year";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['birth_month']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty birth_month";echo json_encode($json_data);echo "])";exit;}
		if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_month'] > 12 || $V['birth_month'] < 1)){echo $V['callback']."([";$json_data['result'] = "incorrect birth_month";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['birth_day']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty birth_day";echo json_encode($json_data);echo "])";exit;}
		if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_day'] > 31 || $V['birth_day'] < 1)){echo $V['callback']."([";$json_data['result'] = "incorrect birth_day";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['sex']) && ($config['use_sex'] == 1 && $config['validate_sex'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty sex";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['zipcode']) && $config['use_addr'] == 1 && $config['validate_addr'] == 1){echo $V['callback']."([";$json_data['result'] = "empty zipcode";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['addr1']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty addr1";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['addr2']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty addr2";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['phone_1']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty phone_1";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['phone_2']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty phone_2";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['phone_3']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty phone_3";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['hp_1']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty hp_1";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['hp_2']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty hp_2";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['hp_3']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty hp_3";echo json_encode($json_data);echo "])";exit;}
		//if(!isset($V['sms_reception']) && $config['use_hp'] == 1){echo $V['callback']."([";$json_data['result'] = "empty sms_reception";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['job']) && ($config['use_job'] == 1 && $config['validate_job'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty job";echo json_encode($json_data);echo "])";exit;}
		//if($V['agree_check1'] != 1){echo $V['callback']."([";$json_data['result'] = "empty agree_check1";echo json_encode($json_data);echo "])";exit;}
		//if($V['agree_check2'] != 1){echo $V['callback']."([";$json_data['result'] = "empty agree_check2";echo json_encode($json_data);echo "])";exit;}
		
		//current_time("timestamp")
		$current_time = current_time('timestamp');

		if($V['auth_result']!="") {
			$authResult = unserialize(base64_decode($V['auth_result']));
			$auth_type = "M";
			$auth_ci = $authResult['CI'];
			$auth_di = $authResult['DI'];
			$auth_yn = "Y";
		}else{
			$auth_type = "";
			$auth_ci = "";
			$auth_di = "";
			$auth_yn = "";
		}

		$updateField = "";$updateValue="";

		if(plugin_active_check('BBSe_Commerce')) {//적립금 처리
			$earnRow = $wpdb->get_row("select * from bbse_commerce_config where config_type='earn'");
			$earnCfg = unserialize($earnRow->config_data);
			if($earnCfg['earn_member_use']=="on") {
				$updateField = ", `mileage`";
				$updateValue = ", '".$earnCfg['earn_member_point']."'";
				// 적립금 로그 저장
				$wpdb->query("INSERT INTO bbse_commerce_earn_log SET earn_mode='IN', earn_type='member', earn_point='".$earnCfg['earn_member_point']."', old_point='0', user_id='".$V['user_id']."', user_name='".$V['user_name']."', reg_date='".$current_time."'");
			}
		}

		$qry1 = "insert into `bbse_commerce_membership` (`user_class`, `user_id`, `user_pass`,`auth_type`, `auth_ci`, `auth_di`, `auth_yn`, `name`, `birth`, `sex`, `zipcode`, `addr1`, `addr2`, `email`, `phone`, `hp`, `sms_reception`, `email_reception`, `job`, `reg_date`".$updateField.") values ('".$config['join_default_class']."', '".$V['user_id']."', password('".$V['pass']."'), '".$auth_type."', '".$auth_ci."','".$auth_di."','".$auth_yn."', '".$V['user_name']."', '".$V['birth']."', '".$V['sex']."', '".$V['zipcode']."', '".$V['addr1']."', '".$V['addr2']."', '".$V['email']."', '".$V['phone']."', '".$V['hp']."', '".$V['sms_reception']."', '".$V['email_reception']."', '".$V['job']."', '".$current_time."'".$updateValue.")";
		$wpdb->query($qry1);

		/* 워드프레스 사용자 추가 start (wp_users, wp_usermeta) */
		$wp_user['user_login'] = $V['user_id'];
		$wp_user['user_pass'] = $V['pass'];
		$wp_user['user_email'] = $V['email'];
		$wp_user_no = wp_create_user($wp_user['user_login'], $wp_user['user_pass'], $wp_user['user_email']);
		/* 워드프레스 사용자 추가 end (wp_users, wp_usermeta) */
		
		$blogname = get_option('blogname');
		
		if(plugin_active_check('BBSe_Commerce')) {//회원가입 메일 발송
			// 메일 발송 요청 (시작)
			$mail_type="join";
			$uni_data=$V['user_id'];
			$etc_data="";
			$msResult=bbse_commerce_mail_send($mail_type,$uni_data,$etc_data);
			// 메일 발송 요청 (끝)

			bbse_commerce_sms_send("join", $V['user_id']);// 회원가입 SMS 발송
		}
		$ok_info = base64_encode($V['user_id']."|||".$V['hp']."|||".$V['sms_reception']."|||".$V['user_name']."|||".strlen($V['pass'])."|||".substr($V['pass'],0,2));
		echo $V['callback']."([";$json_data['result'] = "success|||".$ok_info;echo json_encode($json_data);echo "])";exit;
	}else{echo $V['callback']."([";$json_data['result'] = "exist id";echo json_encode($json_data);echo "])";exit;}
	


}else if($V['mode'] == "edit"){
	$rows = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_no`='".$V['user_no']."'", ARRAY_A);

	if(empty($rows['user_id'])){echo $V['callback']."([";$json_data['result'] = "nonData";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['user_name']) && ($config['use_name'] == 1 && $config['validate_name'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty name";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['email'])){echo $V['callback']."([";$json_data['result'] = "empty email";echo json_encode($json_data);echo "])";exit;}
	if(check_email($V['email']) == true){echo $V['callback']."([";$json_data['result'] = "not form email";echo json_encode($json_data);echo "])";exit;}
	if(!empty($V['pass'])){
		if($config['pass_min_len'] > 0){if(mb_strlen($V['pass']) < $config['pass_min_len']){echo $V['callback']."([";$json_data['result'] = "short pass";echo json_encode($json_data);echo "])";exit;}}
		if(mb_strlen($V['pass']) > 16){echo $V['callback']."([";$json_data['result'] = "long pass";echo json_encode($json_data);echo "])";exit;}
		if(empty($V['repass'])){echo $V['callback']."([";$json_data['result'] = "empty repass";echo json_encode($json_data);echo "])";exit;}
		if($V['pass'] != $V['repass']){echo $V['callback']."([";$json_data['result'] = "password mismatch";echo json_encode($json_data);echo "])";exit;}
	}
	if(empty($V['birth_year']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty birth_year";echo json_encode($json_data);echo "])";exit;}
	if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_year'] < 1900 || $V['birth_year'] > date("Y"))){echo $V['callback']."([";$json_data['result'] = "incorrect birth_year";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['birth_month']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty birth_month";echo json_encode($json_data);echo "])";exit;}
	if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_month'] > 12 || $V['birth_month'] < 1)){echo $V['callback']."([";$json_data['result'] = "incorrect birth_month";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['birth_day']) && ($config['use_birth'] == 1 && $config['validate_birth'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty birth_day";echo json_encode($json_data);echo "])";exit;}
	if($config['use_birth'] == 1 && !empty($V['birth_year']) && ($V['birth_day'] > 31 || $V['birth_day'] < 1)){echo $V['callback']."([";$json_data['result'] = "incorrect birth_day";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['sex']) && ($config['use_sex'] == 1 && $config['validate_sex'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty sex";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['zipcode']) && $config['use_addr'] == 1 && $config['validate_addr'] == 1){echo $V['callback']."([";$json_data['result'] = "empty zipcode";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['addr1']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty addr1";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['addr2']) && ($config['use_addr'] == 1 && $config['validate_addr'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty addr2";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['phone_1']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty phone_1";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['phone_2']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty phone_2";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['phone_3']) && ($config['use_phone'] == 1 && $config['validate_phone'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty phone_3";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['hp_1']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty hp_1";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['hp_2']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty hp_2";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['hp_3']) && ($config['use_hp'] == 1 && $config['validate_hp'] == 1)){echo $V['callback']."([";$json_data['result'] = "empty hp_3";echo json_encode($json_data);echo "])";exit;}
	if(!isset($V['sms_reception']) && $config['use_hp'] == 1){echo $V['callback']."([";$json_data['result'] = "empty sms_reception";echo json_encode($json_data);echo "])";exit;}
	if(empty($V['job']) && ($config['use_job'] == 1 && $config['validate_job'] == 1)){}
	
	if(!empty($V['pass'])){
		$edit_pass = ", `user_pass`=password('".$V['pass']."')";
	}else{
		$edit_pass = "";
	}

	$wpdb->query("update `bbse_commerce_membership` set `name`='".$V['user_name']."', `birth`='".$V['birth']."', `sex`='".$V['sex']."', `zipcode`='".$V['zipcode']."', `addr1`='".$V['addr1']."', `addr2`='".$V['addr2']."', `email`='".$V['email']."', `phone`='".$V['phone']."', `hp`='".$V['hp']."', `sms_reception`='".$V['sms_reception']."', `email_reception`='".$V['email_reception']."', `job`='".$V['job']."'".$edit_pass." where `user_no`='".$rows['user_no']."'");
	
	/* 워드프레스 사용자 정보수정 start (wp_users, wp_usermeta) */
	$wp_user_no = $wpdb->get_var("select `ID` from ".$wpdb->users." where `user_login`='".$rows['user_id']."'");
	$wp_user['ID'] = $wp_user_no;
	$wp_user['user_login'] = $rows['user_id'];
	$wp_user['user_email'] = $V['email'];
	if(!empty($V['pass'])) $wp_user['user_pass'] = $V['pass'];
	
	// 일반 쿼리문으로 비밀번호 수정시 로그아웃 처리되므로 반드시 wp_update_user 함수를 사용
	wp_update_user($wp_user);
	/* 워드프레스 사용자 정보수정 end (wp_users, wp_usermeta) */
	
	echo $V['callback']."([";$json_data['result'] = "success";echo json_encode($json_data);echo "])";exit;
}else{
	echo $V['callback']."([";$json_data['result'] = "nonData";echo json_encode($json_data);echo "])";exit;
}
?>
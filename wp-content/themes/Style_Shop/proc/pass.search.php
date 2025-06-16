<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_GET;

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

$json_data = array();
$page_setting = array();
$page_setting['login_page'] = get_option("bbse_commerce_login_page");
$page_setting['id_search_page'] = get_option("bbse_commerce_id_search_page");
$page_setting['join_page'] = get_option("bbse_commerce_join_page");
$page_setting['pass_search_page'] = get_option("bbse_commerce_pass_search_page");
$page_setting['delete_page'] = get_option("bbse_commerce_delete_page");

if(empty($V['user_id'])){echo $V['callback']."([";$json_data['result'] = "empty id";echo json_encode($json_data);echo "])";exit;}
if(empty($V['email'])){echo $V['callback']."([";$json_data['result'] = "empty email";echo json_encode($json_data);echo "])";exit;}
if(check_email($V['email']) == true){echo $V['callback']."([";$json_data['result'] = "not form email";echo json_encode($json_data);echo "])";exit;}

$rows = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_id`='".$V['user_id']."' and `email`='".$V['email']."' and `leave_yn`<>'1'");
$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`", ARRAY_A);

if(!empty($rows->user_no)){
	$ipwd = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
	$pwd = ""; 
	for($i = 0; $i < 8; $i++){ 
		$pwd .= $ipwd[rand(0, 35)]; 
	}
	
	$wpdb->query("update `bbse_commerce_membership` set `user_pass`=password('".$pwd."') where `user_no`='".$rows->user_no."'");

	/* 워드프레스 사용자 정보수정 start (wp_users, wp_usermeta) */
	$wp_user_no = $wpdb->get_var("select `ID` from ".$wpdb->users." where `user_login`='".$rows->user_id."'");
	$wp_user['ID'] = $wp_user_no;
	$wp_user['user_login'] = $rows->user_id;
	$wp_user['user_pass'] = $pwd;
	wp_update_user($wp_user);
	/* 워드프레스 사용자 정보수정 end (wp_users, wp_usermeta) */
	
	if(plugin_active_check('BBSe_Commerce')) {
		// 메일 발송 요청 (시작)
		$mail_type="find-pw";
		$uni_data=$rows->user_id;
		$etc_data=$pwd;
		$msResult=bbse_commerce_mail_send($mail_type,$uni_data,$etc_data);
		// 메일 발송 요청 (끝)
	}

	echo $V['callback']."([";$json_data['result'] = "success|||y|||<p>비밀번호 찾기 결과</p><div class=\"inputContainer\">고객님의 이메일 <strong>".$rows->email."</strong>로 임시비밀번호를 발송해드렸습니다.<br />로그인 후 비밀번호를 재설정하세요.</div><p class=\"btm_area\"><button type=\"button\" class=\"bb_btn cus_fill shadow\" onclick=\"location.href='".str_replace("https", "http", get_permalink($page_setting['pass_search_page']))."';\"><span class=\"mid\">확인</span></button></p>";echo json_encode($json_data);echo "])";exit;
}else{ 
	echo $V['callback']."([";$json_data['result'] = "success|||n|||<p>비밀번호 찾기 결과</p><div class=\"inputContainer\">일치하는 회원정보가 없습니다.</div><p class=\"btm_area\"><button type=\"button\" class=\"bb_btn cus_fill shadow\" onclick=\"location.href='".str_replace("https", "http", get_permalink($page_setting['pass_search_page']))."';\"><span class=\"mid\">다시 찾기</span></button></p>";echo json_encode($json_data);echo "])";exit;
}
?>
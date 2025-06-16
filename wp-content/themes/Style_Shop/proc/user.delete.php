<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";
require (ABSPATH .'/wp-admin/includes/user.php');

$V = $_GET;

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

if(empty($V['leave_agree'])){echo $V['callback']."([";$json_data['result'] = "empty agree";echo json_encode($json_data);echo "])";exit;}
if(empty($V['pass'])){echo $V['callback']."([";$json_data['result'] = "empty pass";echo json_encode($json_data);echo "])";exit;}
if(empty($V['repass'])){echo $V['callback']."([";$json_data['result'] = "empty repass";echo json_encode($json_data);echo "])";exit;}
if($V['leave_reason']=="D") {
	if(empty($V['input_reason'])){echo $V['callback']."([";$json_data['result'] = "notinput reason";echo json_encode($json_data);echo "])";exit;}
	$leave_reason = $V['input_reason'];
}else{
	if(empty($V['leave_reason'])){echo $V['callback']."([";$json_data['result'] = "empty reason";echo json_encode($json_data);echo "])";exit;}
	$reason = unserialize(BBSE_COMMERCE_LEAVE_REASON);
	$leave_reason = $reason[$V['leave_reason']];
}
if($V['pass'] != $V['repass']){echo $V['callback']."([";$json_data['result'] = "password mismatch";echo json_encode($json_data);echo "])";exit;}

$current_user = wp_get_current_user();	
$user_id = $current_user->user_login;

$page_setting = array();
$page_setting['login_page'] = get_option("bbse_commerce_login_page");
$page_setting['id_search_page'] = get_option("bbse_commerce_id_search_page");
$page_setting['join_page'] = get_option("bbse_commerce_join_page");
$page_setting['pass_search_page'] = get_option("bbse_commerce_pass_search_page");
$page_setting['delete_page'] = get_option("bbse_commerce_delete_page");

if(empty($current_user->ID)){
	echo $V['callback']."([";$json_data['result'] = "not login";echo json_encode($json_data);echo "])";exit;
}else{
	$rows = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_id`='".$current_user->user_login."'");
	if(!empty($rows->user_no)){
		$pass_check = $wpdb->get_var("select count(*) from `bbse_commerce_membership` where `user_id`='".$current_user->user_login."' and `user_pass`=password('".$V['pass']."')");
		if($pass_check > 0){
			$leave_date = current_time('timestamp');
			$wpdb->query("update `bbse_commerce_membership` set leave_reason='".$leave_reason."', leave_yn='1', leave_date='".$leave_date."' where `user_id`='".$current_user->user_login."'");
			$wpdb->query("update `bbse_commerce_social_login` 
				set 	`member_id` = '',member_no = '0',integrate_yn = 'N'
				where `member_id`='".$current_user->user_login."'");
			//$wpdb->query("delete from `bbse_commerce_membership` where `user_id`='".$current_user->user_login."'");
			wp_logout(); // 탈퇴완료 시 로그아웃
			//워드프레스 사용자 삭제
			wp_delete_user($current_user->ID);
			echo $V['callback']."([";$json_data['result'] = "success|||y|||<div class=\"bb_join agreebox\"><h3>회원탈퇴 결과</h3><div class=\"inputContainer\">아이디 <strong>".$current_user->user_login."</strong> 정상적으로 탈퇴 처리 되었습니다.</div><br /><br /><div class=\"article agree_btn_area\"><button type=\"button\" class=\"bb_btn cus_fill w150\" onclick=\"location.href='".BBSE_COMMERCE_SITE_URL."';\"><strong class=\"big\">확인</strong></button></div></div>";echo json_encode($json_data);echo "])";exit;
		}else{
			echo $V['callback']."([";$json_data['result'] = "success|||n|||<div class=\"bb_join agreebox\"><h3>회원탈퇴 결과</h3><div class=\"inputContainer\">회원 비밀번호가 일치하지 않습니다.</div><br /><br /><div class=\"article agree_btn_area\"><button type=\"button\" class=\"bb_btn cus_fill w150\" onclick=\"location.href='".remove_ssl_url(get_permalink($page_setting['delete_page']))."';\"><strong class=\"big\">다시 입력</strong></button></div></div>";echo json_encode($json_data);echo "])";exit;
		}
	}else{
		echo $V['callback']."([";$json_data['result'] = "success|||n|||<div class=\"bb_join agreebox\"><h3>회원탈퇴 결과</h3><div class=\"inputContainer\">일반회원이 아닙니다.</div><br /><br /><div class=\"article agree_btn_area\"><button type=\"button\" class=\"bb_btn cus_fill w150\" onclick=\"location.href='".BBSE_COMMERCE_SITE_URL."';\"><strong class=\"big\">확인</strong></button></div></div>";echo json_encode($json_data);echo "])";exit;
	}
}
?>
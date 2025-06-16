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

if(empty($V['email'])){echo $V['callback']."([";$json_data['result'] = "empty email";echo json_encode($json_data);echo "])";exit;}
if(check_email($V['email']) == true){echo $V['callback']."([";$json_data['result'] = "not form email";echo json_encode($json_data);echo "])";exit;}

$user_id = $wpdb->get_var("select `user_id` from `bbse_commerce_membership` where `email`='".$V['email']."' and `leave_yn`<>'1'");

if(!empty($user_id)){
	echo $V['callback']."([";$json_data['result'] = "success|||y|||<p>아이디 찾기 결과</p><div class=\"inputContainer\">고객님의 아이디는 <strong>".$user_id."</strong> 입니다</div><p class=\"btm_area\"><button type=\"button\" class=\"bb_btn cus_fill shadow\" onclick=\"location.href='".str_replace("https", "http", get_permalink($page_setting['login_page']))."';\"><span class=\"mid\">로그인</span></button></p>";echo json_encode($json_data);echo "])";exit;
}else{ 
	echo $V['callback']."([";$json_data['result'] = "success|||n|||<p>아이디 찾기 결과</p><div class=\"inputContainer\"><strong>".$V['email']."</strong> 이메일은 가입되지 않은 이메일입니다.</div><p class=\"btm_area\"><button type=\"button\" class=\"bb_btn cus_fill shadow\" onclick=\"location.href='".str_replace("https", "http", get_permalink($page_setting['id_search_page']))."';\"><span class=\"mid\">다시찾기</span></button></p>";echo json_encode($json_data);echo "])";exit;
}
?>
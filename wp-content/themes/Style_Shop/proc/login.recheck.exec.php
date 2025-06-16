<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

//Set a cookie now to see if they are supported by the browser.
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if(SITECOOKIEPATH != COOKIEPATH)
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

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
if(empty($V['upass'])){echo $V['callback']."([";$json_data['result'] = "empty pass";echo json_encode($json_data);echo "])";exit;}

$current_user = wp_get_current_user();	
$bbse_user = $wpdb->get_var("select count(*) from bbse_commerce_membership where user_id='".$current_user->user_login."'");
$wp_user = $wpdb->get_var("select count(*) from ".$wpdb->users." where user_login='".$current_user->user_login."'");

if($bbse_user > 0 || $wp_user > 0){
	$bbse_login = $wpdb->get_var("select count(*) from bbse_commerce_membership where user_id='".$current_user->user_login."' and user_pass=password('".$V['upass']."')");
	$wp_login = apply_filters('authenticate', null, $current_user->user_login, $V['upass']);  // wp_users 테이블 검색

	if($wp_login->ID > 0){
		$creds = array();
		$creds['user_login'] = $current_user->user_login;
		$creds['user_password'] = $V['upass'];
		if(isset($V['remember'])) $creds['remember'] = true;
		$user = wp_signon($creds, false);

		if(is_wp_error($user)){echo $V['callback']."([";$json_data['result'] = "login_fail";echo json_encode($json_data);echo "])";exit;}
		echo $V['callback']."([";$json_data['result'] = "success";echo json_encode($json_data);echo "])";exit;
	}else{
		echo $V['callback']."([";$json_data['result'] = "pass_fail";echo json_encode($json_data);echo "])";exit;
	}
}
?>
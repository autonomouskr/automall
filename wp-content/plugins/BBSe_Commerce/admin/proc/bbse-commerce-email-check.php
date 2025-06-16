<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

$eCnt = $wpdb->get_var("select count(*) from `bbse_commerce_membership` where `email`='".trim($V['email'])."'");
if(email_exists(trim($V['email']))==true || $eCnt > 0){
	echo "exist";
	exit;
}
else{
	echo "ok";
	exit;
}
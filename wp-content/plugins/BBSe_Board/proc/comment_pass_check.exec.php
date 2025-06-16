<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user     = wp_get_current_user();  // 현재 회원의 정보 추출

$V = $_POST;

if(empty($V['bname'])){echo "empty bname";exit;}
if(empty($V['cno'])){
	echo "empty cno";
	exit;
}else{
	passNumer($V['cno'], "Illegal cno");
}

$prepare = NULL;
$prepare = $wpdb->prepare( "SELECT pass FROM {$wpdb->prefix}bbse_{$V['bname']}_comment WHERE no = %d", array( $V['cno'] ) );
$pass    = $wpdb->get_var( $prepare );

if(empty($V['pwd'])){echo "empty password";exit;}

$prepare  = NULL;
$prepare  = $wpdb->prepare( "SELECT password( %s )", array( $V['pwd'] ) );
$mem_pass = $wpdb->get_var( $prepare );

if(!empty($pass) && !empty($mem_pass) && $pass == $mem_pass){
	echo "success";exit;
}else{
	echo "password error";exit;
}
?>
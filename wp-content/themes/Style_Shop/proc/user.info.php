<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

if($V['user_id'] && is_user_logged_in()){

	$row = $wpdb->get_row("select * from `bbse_commerce_membership` where `user_id`='".$V['user_id']."'");
	$user_info = array();
	$user_info['name'] = $row->name;
	$user_info['zipcode'] =  $row->zipcode;
	$user_info['addr1'] =  $row->addr1;
	$user_info['addr2'] =  $row->addr2;
	$user_info['phone'] =  $row->phone;
	$user_info['hp'] =  $row->hp;
	$user_info['email'] =  $row->email;

	echo "success|||".implode("|||", $user_info);
	exit;

}else{
	echo "fail";
	exit;
}
?>
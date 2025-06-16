<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

function check_email($str){
	$email_match = "/([0-9a-z]([-_\.]?[0-9a-z])*@[0-9a-z]([-_\.]?[0-9a-z])*\.[a-z]{2,4})/i";
	if(preg_match($email_match, $str)){
		return false;
	}else{
		return true;
	}
}

$V = $_POST;

$config = $wpdb->get_row("select * from `bbse_commerce_membership_config` limit 1");

if(!empty($config->join_not_id)){
	$not_id_arr = explode(",", $config->join_not_id);
	for($i = 0; $i < count($not_id_arr); $i++){
		$not_id_arr[$i] = str_replace(" ", "", $not_id_arr[$i]);
	}
}

if($V['email']){
	if(empty($V['email'])){echo "empty email";exit;}
	if(check_email($V['email']) == true){echo "error email";exit;}
	if(isset($not_id_arr)){
		if(in_array($V['email'], $not_id_arr)){echo "join not email";exit;}
	}
	$rows1 = $wpdb->get_row("select count(*) from `bbse_commerce_membership` where `email`='".$V['email']."'", ARRAY_N);
	$rows2 = $wpdb->get_row("select count(*) from `".$wpdb->users."` where `user_email`='".$V['email']."'", ARRAY_N);

	if($rows1[0] == 0 && $rows2[0] == 0){
		echo "success|||y|||<font style='color:#0090ff;'>사용가능</font>";
	}else{
		echo "success|||n|||<font style='color:#ff0000;'>사용불가</font>";
	}
	exit;

}else{
	echo "fail";
	exit;
}
?>
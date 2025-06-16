<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$config = $wpdb->get_row("select * from `bbse_commerce_membership_config` limit 1");

if(!empty($config->join_not_id)){
	$not_id_arr = explode(",", $config->join_not_id);
	for($i = 0; $i < count($not_id_arr); $i++){
		$not_id_arr[$i] = str_replace(" ", "", $not_id_arr[$i]);
	}
}

if($V['user_id']){
	if(empty($V['user_id'])){echo "empty id";exit;}
	if($config->id_min_len > 0){if(mb_strlen($V['user_id']) < $config->id_min_len){echo "short id";exit;}}
	if(mb_strlen($V['user_id']) > 16){echo "long id";exit;}
	if(!ctype_alnum($V['user_id'])){echo "error id";exit;}
	if(isset($not_id_arr)){
		if(in_array($V['user_id'], $not_id_arr)){echo "join not id";exit;}
	}
	$rows1 = $wpdb->get_row("select count(*) from `bbse_commerce_membership` where `user_id`='".$V['user_id']."'", ARRAY_N);
	$rows2 = $wpdb->get_row("select count(*) from `".$wpdb->users."` where `user_login`='".$V['user_id']."'", ARRAY_N);

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
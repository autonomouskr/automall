<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

if(!$V['order_no']){
	echo "fail";
	exit;
}
$sid = session_id();

$midx = $wpdb->get_var("SELECT idx FROM bbse_commerce_cart WHERE user_id='".$V['order_no']."'"); 

if($midx > '0'){
	//update
	$wpdb->get_row("UPDATE bbse_commerce_cart SET sid='".$sid."', goods_option_basic='".base64_encode(serialize($V))."', remote_ip='".$_SERVER['REMOTE_ADDR']."' WHERE idx='".$midx."'");
	$idx = $midx;
}else{
	//insert
	$reg_date = current_time('timestamp');
	$wpdb->query("INSERT INTO bbse_commerce_cart SET user_id='".$V['order_no']."', sid='".$sid."', goods_option_basic='".base64_encode(serialize($V))."', remote_ip='".$_SERVER['REMOTE_ADDR']."', reg_date='".$reg_date."'");
	$idx = $wpdb->insert_id;
}
echo "success|||".$idx;
exit;
?>
<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

if($V['receive_addr1']){
	$delivery_price = bbse_commerce_get_delivery_add($V['receive_addr1']);
	$delivery_price = ($delivery_price)?$delivery_price:0;
	echo "success|||".$delivery_price;
	exit;

}else{
	echo "success|||0";
	exit;
}
?>
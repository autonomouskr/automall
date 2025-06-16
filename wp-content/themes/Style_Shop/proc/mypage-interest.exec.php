<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

$currUserID=$current_user->user_login;
$Loginflag='member';
$currSnsIdx="";

if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsLoginData=unserialize($_SESSION['snsLoginData']);
		$currUserID=$snsLoginData['sns_id'];
	}
}

if(!$currUserID){
	echo "loginError";
	exit;
}

if($V['tMode']=="removeWishlist"){
	if(!$V['tIdx']){
		echo "DataError";
		exit;
	}

	$wCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_cart WHERE idx='".$V['tIdx']."' AND cart_kind='W' AND user_id='".$currUserID."'");

	if($wCnt<='0'){
		echo "notExistWishlist";
		exit;
	}

	$wpdb->query("DELETE FROM bbse_commerce_cart WHERE idx='".$V['tIdx']."' AND cart_kind='W' AND user_id='".$currUserID."'");

	$newCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_cart WHERE cart_kind='W' AND user_id='".$currUserID."'");

	echo "success|||".$newCnt;
	exit;
}
elseif($V['tMode']=="removeCheckWishlist"){
	if(!$V['tIdx']){
		echo "DataError";
		exit;
	}

	$wIdx=explode(",",$V['tIdx']);
	$dQuery="";
	for($i=0;$i<sizeof($wIdx);$i++){
		if($dQuery) $dQuery .=" OR ";
		$dQuery .="idx='".$wIdx[$i]."'";
	}

	if($dQuery){
		$wpdb->query("DELETE FROM bbse_commerce_cart WHERE (".$dQuery.") AND cart_kind='W' AND user_id='".$currUserID."'");

		$newCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_cart WHERE cart_kind='W' AND user_id='".$currUserID."'");

		echo "success|||".$newCnt;
		exit;
	}
	else{
		echo "DataError";
		exit;
	}
}
else{
	echo "fail";
	exit;
}

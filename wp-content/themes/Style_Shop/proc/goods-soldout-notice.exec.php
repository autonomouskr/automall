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

if(!$currUserID){
	echo "loginError";
	exit;
}

if($V['tMode']=="insertNotice"){
	if(!$V['tIdx']){
		echo "DataError";
		exit;
	}

	$gCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$V['tIdx']."'");
	if($gCnt<='0'){
		echo "DataError";
		exit;
	}

	$nCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE goods_idx='".$V['tIdx']."' AND user_id='".$currUserID."' AND sms_yn='N' AND email_yn='N'");
	if($nCnt>'0'){
		echo "existSoldoutNotice";
		exit;
	}

	$oCnfCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='order'");
	if($oCnfCnt>'0'){
		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
		$orderInfo=unserialize($confData->config_data);
	}

	if($orderInfo['soldout_notice_use']!='on'){
		echo "notUseSoldoutNotice";
		exit;
	}

	$mData = $wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$currUserID."'");
	$memHp=trim(str_replace("-","",$mData->hp));
	$memEmail=trim($mData->email);

	if($orderInfo['soldout_notice_sms']=='sms' && $orderInfo['soldout_notice_email']=='email' && !$memHp && !$memEmail){
		echo "memberInfoNull";
		exit;
	}
	elseif($orderInfo['soldout_notice_sms']=='sms' && $orderInfo['soldout_notice_email']!='email'){
		$memEmail="";
		if(!$memHp){
			echo "memberHpNull";
			exit;
		}
	}
	elseif($orderInfo['soldout_notice_email']=='email' && $orderInfo['soldout_notice_sms']!='sms'){
		$memHp="";
		if(!$memEmail){
			echo "memberEmailNull";
			exit;
		}
	}

	$currTime=current_time('timestamp');
	$wpdb->query("INSERT INTO bbse_commerce_soldout_notice (goods_idx, user_id, hp, email, reg_date) VALUES ('".$V['tIdx']."', '".$currUserID."', '".$memHp."', '".$memEmail."', '".$currTime."')");

	echo "success";
	exit;
}
elseif($V['tMode']=="removeNotice"){
	if(!$V['tIdx']){
		echo "DataError";
		exit;
	}

	$nCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE idx='".$V['tIdx']."' AND user_id='".$currUserID."'");

	if($nCnt<='0'){
		echo "notExistNoticeist";
		exit;
	}

	$wpdb->query("DELETE FROM bbse_commerce_soldout_notice WHERE idx='".$V['tIdx']."' AND user_id='".$currUserID."'");

	$newCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE user_id='".$currUserID."'");

	echo "success|||".$newCnt;
	exit;
}
elseif($V['tMode']=="removeCheckNoticelist"){
	if(!$V['tIdx']){
		echo "DataError";
		exit;
	}

	$nIdx=explode(",",$V['tIdx']);
	$dQuery="";
	for($i=0;$i<sizeof($nIdx);$i++){
		if($dQuery) $dQuery .=" OR ";
		$dQuery .="idx='".$nIdx[$i]."'";
	}

	if($dQuery){
		$wpdb->query("DELETE FROM bbse_commerce_soldout_notice WHERE (".$dQuery.") AND user_id='".$currUserID."'");

		$newCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE user_id='".$currUserID."'");

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

<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $current_user, $theme_shortname, $currentSessionID;

wp_get_current_user();
$currUserID=$current_user->user_login;

$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사
if($nPayData['guest_cart_use']=='on' && !$currUserID) $currUserID=$_SERVER['REMOTE_ADDR'];
elseif($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsData=unserialize($_SESSION['snsLoginData']);
		$currUserID=$snsData['sns_id'];
	}
}

$V = $_POST;

if($V['cart_mode'] == "remove") {

	$mChk = $wpdb->get_var("select count(*) from bbse_commerce_cart where user_id='".$currUserID."' and idx='".$V['cart_idx']."'");
	if($mChk > 0) {
		$wpdb->query("delete from bbse_commerce_cart where idx='".$V['cart_idx']."'");
		wp_redirect(BBSE_COMMERCE_SITE_URL."/?bbsePage=cart");
	}else{
		wp_redirect(get_permalinke(get_option($theme_shortname."_login_page")));
	}
}else if($V['cart_mode'] == "select_remove") {

	if(count($V['gidx']) > 0) {
		for($i=0;$i<count($V['gidx']);$i++) {
			$mChk = $wpdb->get_var("select count(*) from bbse_commerce_cart where user_id='".$currUserID."' and idx='".$V['gidx'][$i]."'");
			if($mChk > 0) {
				$wpdb->query("delete from bbse_commerce_cart where idx='".$V['gidx'][$i]."'");
			}
		}
		wp_redirect(BBSE_COMMERCE_SITE_URL."/?bbsePage=cart");
	}else{
		wp_redirect(home_url());
	}

}else{
	$cart_kind = (!$V['cart_kind'])?"cart":$V['cart_kind'];
	$remote_ip = $_SERVER['REMOTE_ADDR'];
	$user_id = $currUserID;
	$goods_idx = "";
	$goods_option_basic = "";
	$goods_option_add = "";
	$currTime=current_time('timestamp');
	//$wpdb->query("INSERT INTO bbse_commerce_cart SET user_id='".$user_id."', sid='".$currentSessionID."', cart_kind='".$cart_kind."', goods_idx='".$goods_idx."', goods_option_basic='".$goods_option_basic."', goods_option_add='".$goods_option_add."', remote_ip='".$remote_ip."', reg_date='".$currTime."'");
}
?>
<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

/*
error_reporting(E_ALL);
ini_set("display_errors", 1);
*/

$snsConfig=bbse_get_social_login_config();

if($snsConfig['naver']['naver_use_yn'] != "Y"){
	echo "<script>alert('네이버 소셜(간편) 로그인을 이용하실 수 없습니다.   ');self.close();</script>";
	exit;
}

// 네이버 정보
$nvr_ClientID=$snsConfig['naver']['naver_client_id'];
$nvr_ClientSecret=$snsConfig['naver']['naver_client_secret'];
$nvr_RedirectURL=BBSE_THEME_WEB_URL."/snslogin/social-login-naver-callback.php";

require_once ('./class/naver-oauth.class.php');
$request = new NaverOAuthRequest( $nvr_ClientID, $nvr_ClientSecret, $nvr_RedirectURL );
$request -> set_state();
$request -> request_auth();
?>

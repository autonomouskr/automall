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

if($snsConfig['kakao']['kakao_use_yn'] != "Y"){
	echo "<script>alert('카카오 소셜(간편) 로그인을 이용하실 수 없습니다.   ');self.close();</script>";
	exit;
}

 // 카카오 정보
$kko_ClientID=$snsConfig['kakao']['kakao_rest_api_key'];
$kko_RedirectURL=BBSE_THEME_WEB_URL."/snslogin/social-login-kakao-callback.php";

require_once ('./class/kakao-oauth.class.php');
$request = new KakaoOAuthRequest( $kko_ClientID, $kko_RedirectURL );
$request -> request_auth();
?>

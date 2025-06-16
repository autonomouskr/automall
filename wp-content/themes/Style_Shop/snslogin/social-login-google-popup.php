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

if($snsConfig['google']['google_use_yn'] != "Y"){
	echo "<script>alert('구글 소셜(간편) 로그인을 이용하실 수 없습니다.   ');self.close();</script>";
	exit;
}

 // 구글 정보
$ggl_ClientID=$snsConfig['google']['google_client_id'];
$ggl_ClientSecret=$snsConfig['google']['google_client_secret'];
$ggl_RedirectURL=BBSE_THEME_WEB_URL."/snslogin/social-login-google-callback.php";

require_once ('./class/google-oauth.class.php');
$request = new GoogleOAuthRequest( $ggl_ClientID, $ggl_ClientSecret, $ggl_RedirectURL );
$request -> request_auth();
?>

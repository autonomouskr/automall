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

if($snsConfig['facebook']['facebook_use_yn'] != "Y"){
	echo "<script>alert('페이스북 소셜(간편) 로그인을 이용하실 수 없습니다.   ');self.close();</script>";
	exit;
}

 // 페이스북 정보
$fcb_ClientID=$snsConfig['facebook']['facebook_app_id'];
$fcb_ClientSecret=$snsConfig['facebook']['facebook_app_secret'];
$fcb_RedirectURL=BBSE_THEME_WEB_URL."/snslogin/social-login-facebook-callback.php";

require_once ('./class/facebook-oauth.class.php');
$request = new FacebookOAuthRequest( $fcb_ClientID, $fcb_ClientSecret, $fcb_RedirectURL );
$request -> request_auth();
?>

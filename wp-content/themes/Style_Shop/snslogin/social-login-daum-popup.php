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

if($snsConfig['daum']['daum_use_yn'] != "Y"){
	echo "<script>alert('다음 소셜(간편) 로그인을 이용하실 수 없습니다.   ');self.close();</script>";
	exit;
}

 // 구글 정보
$dum_ClientID=$snsConfig['daum']['daum_client_id'];
$dum_ClientSecret=$snsConfig['daum']['daum_client_secret'];
$dum_RedirectURL=BBSE_THEME_WEB_URL."/snslogin/social-login-daum-callback.php";

require_once ('./class/daum-oauth.class.php');
$request = new DaumOAuthRequest( $dum_ClientID, $dum_ClientSecret, $dum_RedirectURL );
$request -> request_auth();
?>

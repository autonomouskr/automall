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

if($snsConfig['twitter']['twitter_use_yn'] != "Y"){
	echo "<script>alert('트위터 소셜(간편) 로그인을 이용하실 수 없습니다.   ');self.close();</script>";
	exit;
}

// 트위터 정보
$twt_Config['consumerKey']=$snsConfig['twitter']['twitter_api_key'];
$twt_Config['consumerSecret']=$snsConfig['twitter']['twitter_api_secret'];

require_once ("./class/twitter-oauth.class.php");

$twitteroauth = new TwitterOAuth($twt_Config['consumerKey'], $twt_Config['consumerSecret']);
$request_token = $twitteroauth->getRequestToken(BBSE_THEME_WEB_URL."/snslogin/social-login-twitter-callback.php");

//토큰을 세션에 저장
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

if ($twitteroauth->http_code == 200) {
    // Let's generate the URL and redirect
    $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
    header('Location: ' . $url);
}
else{
	echo "<script>alert('트위터 아이디로 로그인에 실패하였습니다.');self.close();</script>";
	exit;
}
?>

<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once $includeURL[0]."/wp-load.php";
if ( !is_user_logged_in() )
{
  echo 'DENIED';
  exit;
}

global $theme_shortname;
/*
---------------------------------------------------------------------------------------------------------------------
파라미터	      설명	           	  비고
---------------------------------------------------------------------------------------------------------------------
access_token	엑세스 토큰
targetUrl     조회할 티스토리 주소	http://xxx.tistory.com 일경우 xxx 만 입력, 2차도메인일 경우 http://제거한 url 입력
output  		  출력 포맷		        json: JSON출력, xml: XML출력, 그외: XML출력
---------------------------------------------------------------------------------------------------------------------
*/
$requestURL  = 'https://www.tistory.com/apis/category/list';
$requestURL .= '?targetUrl='.get_option($theme_shortname."_tistory_url");
$requestURL .= '&output=json';
$requestURL .= '&access_token='.get_option($theme_shortname."_tistory_accesstoken");

$response    = wp_remote_post($requestURL);
if(is_wp_error($response))
  echo 'FAIL';
else
{
  date_default_timezone_set ( get_option('timezone_string') );
  $returnStr = wp_remote_retrieve_body($response);
  $decoded   = json_decode($returnStr);
  if ($decoded->tistory->status == 200){
	//인코딩 문제로 JSON_UNESCAPED_UNICODE 옵션 추가
    //update_option($theme_shortname."_tistory_category",      json_encode($decoded->tistory->item->categories, JSON_UNESCAPED_UNICODE));
	$json_encoded      = json_encode($decoded->tistory->item->categories);
	$unicode_unescaped = preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
		return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
	}, $json_encoded);

	update_option($theme_shortname."_tistory_category", $unicode_unescaped);
    update_option($theme_shortname."_tistory_category_time", date("Y-m-d H:i:s",current_time('timestamp')));
    echo 'SUCCESS';
  }
  else
    echo 'FAIL';
}
exit;
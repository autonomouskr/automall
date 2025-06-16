<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once $includeURL[0]."/wp-load.php";
global $theme_shortname;
/*
---------------------------------------------------------------------------------------------------------------------
파라미터	      설명	           	  비고
---------------------------------------------------------------------------------------------------------------------
access_token	엑세스 토큰
targetUrl     조회할 티스토리 주소	http://xxx.tistory.com 일경우 xxx 만 입력, 2차도메인일 경우 http://제거한 url 입력
title	게시글   제목
visibility    글가시성	        	0:비공개, 1: 보호, 2: 공개, 3: 발행, 생략시 비공개
published   	발행시간		        UNIX_TIMESTAMP() 값을 넣을경우, 해당 날짜에 예약발행 처리
category  	  카테고리 아이디	    생략시 0(분류없음)
content 	  	글 내용
slogan    		문자 주소
tag     	  	태그			          ,로 구분하며 이어서 입력
output  		  출력 포맷		        json: JSON출력, xml: XML출력, 그외: XML출력
---------------------------------------------------------------------------------------------------------------------
*/

$requestURL  = 'https://www.tistory.com/apis/post/write';
$requestURL .= '?targetUrl='.esc_url(get_option($theme_shortname."_tistory_url"));
$requestURL .= '&title=';
$requestURL .= '&visibility='.$_REQUEST['visibility'];
$requestURL .= '&published='.$_REQUEST['published'];
$requestURL .= '&category='.$_REQUEST['category'];
$requestURL .= '&content='.$_REQUEST['content'];
$requestURL .= '&slogan='.$_REQUEST['slogan'];
$requestURL .= '&tag='.$_REQUEST['tag'];
$requestURL .= '&output=json';
$requestURL .= '&access_token='.get_option($theme_shortname."_tistory_accesstoken");

$response = wp_remote_post($requestURL);
if(is_wp_error($response))
  echo 'FAILL';
else
{
  date_default_timezone_set ( get_option('timezone_string') );
  echo $returnStr = wp_remote_retrieve_body($response);
  $decoded   = json_decode($returnStr);
  if ($decoded->tistory->status == 200)
  {
    update_option($theme_shortname."_tistory_category", json_encode($decoded->tistory->item->categories));
    update_option($theme_shortname."_tistory_category_time", date("Y-m-d H:i:s",current_time('timestamp')));
    echo 'SUCCESS';
  }
  else
    echo 'FAILL';
}
exit;
<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once $includeURL[0]."/wp-load.php";

if(!current_user_can('manage_options') || !$_REQUEST['apiUrl'] || ($_REQUEST['apiBlog'] && !$_REQUEST['apiId']) || !$_REQUEST['apiPw']){
	echo "fail";
}
else{
	if($_REQUEST['apiType']=='category'){
		$rtnPing=bbse_get_blog_user($_REQUEST['apiUrl'],$_REQUEST['apiId'],$_REQUEST['apiPw']);
		if($rtnPing){
			$rtnCategory=bbse_get_blog_category($_REQUEST['apiUrl'],$_REQUEST['apiId'],$_REQUEST['apiPw']);
			$rtnDate=current_time('timestamp');
			$rtnDateScript=date("Y-m-d H:i:s",$rtnDate);

			echo "success|||".$rtnCategory."|||".$rtnDate."|||".$rtnDateScript;
		}
		else echo "fail";
	}
	elseif($_REQUEST['apiType']=='ping'){
		$rtnPing=bbse_get_blog_user($_REQUEST['apiUrl'],$_REQUEST['apiId'],$_REQUEST['apiPw']);

		if($rtnPing) echo "success";
		else echo "fail";
	}
}
exit;

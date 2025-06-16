<?php
/*
[�׸� ���� �� ���ǻ���]
1. ����������(Wordpress)�� ������Ʈ ����� ���� �׸�/�÷������� ���� �� �� �缳ġ �ϴ� ����Դϴ�.
   ������Ʈ �� ��� ���� ������ �ʱ�ȭ �ǹǷ� �׸��� �����Ͻô� ���, ���ϵ��׸�(Child Theme) ����� �̿��� �ֽñ� �ٶ��ϴ�.
2. ���ϵ��׸�(Child Theme)�� �̿��� ���� ��� : https://codex.wordpress.org/ko:Child_Themes
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

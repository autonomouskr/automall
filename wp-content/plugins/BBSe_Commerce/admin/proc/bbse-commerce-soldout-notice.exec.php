<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb;

$V = $_POST;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

if($V['tMode']=='removeNotice'){ // 품절상품 알림목록 삭제
	if(!$V['tIdx']){
		echo "fail";
		exit;
	}

	$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE idx='".$V['tIdx']."'");

	if($cnt2<='0'){
		echo "notExistNotice";
		exit;
	}

	$wpdb->query("DELETE FROM bbse_commerce_soldout_notice WHERE idx='".$V['tIdx']."'");

	echo "success";
	exit;
}
elseif($V['tMode']=='bulkAction'){ // 문의글 삭제
	if(!$V['tData']){
		echo "fail";
		exit;
	}

	if($V['tStatus']=='remove'){
		$checkIdx=explode(",",$V['tData']);
		$dCnt='0';
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>'0'){
				$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE idx='".$checkIdx[$i]."'");
				if($cnt2>'0'){
					$res = $wpdb->query("DELETE FROM bbse_commerce_soldout_notice WHERE idx='".$checkIdx[$i]."'");
					if($res) $dCnt++;
				}
			}
		}

		if($dCnt>'0'){
			echo "success";
			exit;
		}
		else{
			echo "dbError";
			exit;
		}
	}
}
else{
	echo "nonData";
	exit;
}
?>
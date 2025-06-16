<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
if(!class_exists('idna_convert')){
	include BBSE_BOARD_PLUGIN_ABS_PATH."class/idna_convert.class.php";
}
header("Content-Type: text/html; charset=UTF-8");

$idn_version = isset($_POST['idn_version']) && $_POST['idn_version'] == 2003 ? 2003 : 2008;
$IDN = new idna_convert(array('idn_version' => $idn_version));
$encoded_http_referer = $IDN->encode($_SERVER['HTTP_REFERER']);
$encoded_http_host = $IDN->encode($_SERVER['HTTP_HOST']);

if(!stristr($encoded_http_referer, $encoded_http_host)){echo "nonData";exit;}

$tMode = $_POST['tMode'];
$tBoardName = $_POST['tBoardName'];
if(!empty($_POST['tBoardNo'])) $tBoardNo = $_POST['tBoardNo'];

if($tMode == 'chkBoardName'){  // 게시판 명 검사
	if($tBoardName == 'admin'){echo "usedAdmin";exit;}

	$blank = explode(" ", $tBoardName);
	if(empty($tBoardName) || count($blank) > 1){echo "usedBlank";exit;}
	if(strlen($tBoardName) > 20){echo "over20";exit;}
	if(empty($tBoardNo)){
		$boardchk = $wpdb->get_var("select count(*) from `".$wpdb->prefix."bbse_board` where `boardname`='".$tBoardName."'");
		if($boardchk > 0){echo "existName";exit;}
		else{echo "success";exit;}
	}else{
		$boardchk = $wpdb->get_var("select count(*) from `".$wpdb->prefix."bbse_board` where `board_no`='".$tBoardNo."'");
		if($boardchk <= 0){echo "nonExist";exit;}
		else{echo "success";exit;}
	}
}else{echo "nonData";exit;}
?>
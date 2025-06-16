<?php 
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$V = $_POST;
if(empty($V['bname'])){
	echo "fail";
	exit;	
}else{
	if(empty($V['page']))       $V['page']       = 1;
	if(empty($V['keyfield']))   $V['keyfield']   = NULL;
	if(empty($V['keyword']))    $V['keyword']    = NULL;
	if(empty($V['search_chk'])) $V['search_chk'] = NULL;
	if(empty($V['cate']))       $V['cate']       = NULL;
	echo bbse_board_parameter_encryption($V['bname'], 'list', '', $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate']);exit;
}
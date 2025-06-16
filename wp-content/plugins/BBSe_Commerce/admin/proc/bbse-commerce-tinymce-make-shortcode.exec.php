<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$V = $_POST;

if($V['selectOption']=='youtube'){ // youtube Shortcode 생성
	if(!$V['pluginUrl'] || !$V['op_url'] || !$V['op_width'] || !$V['op_auto']){
		echo "fail";
		exit;
	}

	if($V['op_auto']=='Y') $optAuto='1';
	else $optAuto='0';

	if($V['op_height']>0) $opHeight=" height=\"".$V['op_height']."\"";
	else $opHeight="";

	$scData="[bbse_commerce_youtube src=\"".$V['op_url']."\" width=\"".$V['op_width']."\"".$opHeight." autoplay=\"".$optAuto."\"]";
	echo "success|||".$scData;
	exit;
}
else{
	echo "nonData";
	exit;
}
?>
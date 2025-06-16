<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if (!empty($_GET['target'])){
	//check
	$uploaded_file =  $_GET['target'];
	if ( ( count(explode('.', $uploaded_file)) > 1 ) || ( !preg_match("/^[1-9]+/", $uploaded_file) ) ){
		echo "Illegal try"; exit;
	}

	//prepare
	list($board_no, $article_no, $file_no) = explode('_', $uploaded_file);

	$prepare    = NULL;
	$prepare    = $wpdb->prepare( "SELECT boardname FROM {$wpdb->prefix}bbse_board WHERE board_no = %d", array( $board_no ) );
	$board_name = $wpdb->get_var( $prepare );

	$prepare    = NULL;
	if ($file_no == 'image'){
		$prepare = $wpdb->prepare( "SELECT image_file as filename FROM {$wpdb->prefix}bbse_{$board_name}_board WHERE no = %d", array( $article_no ) );
	} elseif ($file_no == 're1' || $file_no == 're2'){
		$article_date = date("Y-m-d H:i:s", $article_no);
		if ($file_no == 're1')     $file_no = 1;
		elseif ($file_no == 're2') $file_no = 2;

		$prepare = $wpdb->prepare( "SELECT file%d as filename FROM {$wpdb->prefix}bbse_{$board_name}_board WHERE write_date = %s", array( $file_no, $article_date ) );
	} else {
		$prepare = $wpdb->prepare( "SELECT file%d as filename FROM {$wpdb->prefix}bbse_{$board_name}_board WHERE no = %d", array( $file_no, $article_no ) );
	}

	$file_name  = $wpdb->get_var( $prepare );
	$file_ext   = explode('.', $file_name);

	//set
	$file    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$uploaded_file.'.'.end($file_ext);
	$dnurl   = iconv("UTF-8", "EUC-KR", $file_name);
	$dn      = '1';
	$dn_yn   = ($dn)?'attachment':'inline';
	$bin_txt = '1';
	$bin_txt = ($bin_txt)?'r':'rb';

	//download
	if(is_file($file)){
		if(preg_match("/(MSIE 5.5|MSIE 6.0)/", $_SERVER['HTTP_USER_AGENT'])){
			header("Content-type: application/octet-stream");
			header("Content-Length: ".filesize("$file"));
			header("Content-Disposition: $dn_yn; filename=$dnurl");
			header("Content-Transfer-Encoding: binary");
			header("Pragma: no-cache");
			header("Expires: 0");
		}else{
			header("Content-type: file/unknown");
			header("Content-Length: ".filesize("$file"));
			header("Content-Disposition: $dn_yn; filename=$dnurl");
			header("Content-Description: PHP3 Generated Data");
			header("Pragma: no-cache");
			header("Expires: 0");
		}
		$fp = fopen($file, $bin_txt);
		if(!fpassthru($fp)) fclose($fp);
	}else{
		echo "해당 파일이나 경로가 존재하지 않습니다.";
		exit;
	}
}
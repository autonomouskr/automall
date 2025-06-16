<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

set_time_limit (0);

require_once(BBSE_BOARD_PLUGIN_ABS_PATH."class/backup.class.php");

$bbse_backup = new BBSeBoardBackup();

$xml_file = BBSE_BOARD_UPLOAD_BASE_PATH.basename($_FILES['bbse_board_db_file']['name']);
if(move_uploaded_file($_FILES['bbse_board_db_file']['tmp_name'], $xml_file)){
	$file_ext = explode(".", $xml_file);
	if(end($file_ext) == "xml"){
		$bbse_backup->xml_import($xml_file);
		echo "success";
	}else{
		echo "error file_type";
	}
	unlink($xml_file);
	exit;

}else{
	echo "fail";
	exit;
}
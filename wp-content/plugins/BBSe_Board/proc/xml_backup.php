<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

require_once(BBSE_BOARD_PLUGIN_ABS_PATH."class/backup.class.php");

$bbse_backup = new BBSeBoardBackup();
$tbls = $bbse_backup->get_tables();
$xml_data = "";
foreach($tbls as $key => $value){
	$xml_data .= $bbse_backup->get_xml($value);
}

$bbse_backup->xml_download($xml_data);
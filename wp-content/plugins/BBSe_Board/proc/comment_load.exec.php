<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$V = $_POST;

if(empty($V['bname'])){echo "empty bname";exit;}
if(empty($V['cno'])){
	echo "empty cno";
	exit;
}else{
	//cno 체크
	passNumer($V['cno'], "Illegal cno");
}

$prepare = NULL;
$prepare = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}bbse_{$V['bname']}_comment WHERE no = %d", array( $V['cno'] ));
$data    = $wpdb->get_row( $prepare );

if(!empty($data->no)){
	echo "success|||".$data->writer."|||".$data->content;
}else{
	echo "no data";exit;
}
?>
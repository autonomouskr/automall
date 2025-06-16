<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	$V = $_POST;
	
	if($V['selectedValue'] != null && $V['selectedValue'] != ""){
	    $result = $wpdb->get_results("select * from tbl_inven where storage_code = '".$V['selectedValue']."' and delete_yn != 'Y'" );
	    
	    if(sizeof($result) > '0'){
	        echo json_encode($result);
		}
	}
?>
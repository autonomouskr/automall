<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$V = bbse_board_parameter_decryption($_GET['nType']);

$check      = $_POST['check'];
$page       = empty($V['page'])       ? NULL : $V['page'];
$keyfield   = empty($V['keyfield'])   ? NULL : $V['keyfield'];
$keyword    = empty($V['keyword'])    ? NULL : $V['keyword'];
$search_chk = empty($V['search_chk']) ? NULL : $V['search_chk'];
$cate       = empty($V['cate'])       ? NULL : $V['cate'];




$tblBoard   = $wpdb->prefix.'bbse_board';
$tblBname   = $wpdb->prefix.'bbse_'.$V['bname'].'_board';
$tblComment = $wpdb->prefix.'bbse_'.$V['bname'].'_comment';

$prepare = NULL;
$prepare = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE boardname = %s ", array( $V['bname'] ) );
$config  = $wpdb->get_row( $prepare );

$delete_title = $delele_no = array();
$re = 0;
for($i = 0; $i < count($check); $i++){
	$prepare = NULL;
	$prepare = $wpdb->prepare( "SELECT * FROM {$tblBname} WHERE no = %d", array( $check[$i] ) );
	$board   = $wpdb->get_row( $prepare);

	$prepare   = NULL;
	$prepare   = $wpdb->prepare( "SELECT count(*) FROM {$tblBname} WHERE ref=%d and re_step=%d and re_level=%d", array( $board->ref, ($board->re_step+1), ($board->re_level+1) ) );
	$reply_cnt = $wpdb->get_var( $prepare );
	if($reply_cnt > 0){
		$re++;
	}else{
		$delete_title[] = $board->title;
		$delele_no[]    = $check[$i];

		if(!empty($board->write_date)){
			$tmp1   = explode(" ", $board->write_date);
			$tmp2   = explode("-", $tmp1[0]);
			$tmp3   = explode(":", $tmp1[1]);
			$unique = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
		}

		if($board->re_level != "0") $pict = $unique.'_re';
		else $pict = $check[$i].'_';

		if(!empty($board->image_file)){
			$imgkind = substr($board->image_file, -3);
			$imgname = BBSE_BOARD_UPLOAD_BASE_PATH."bbse-board/".$config->board_no.'_'.$pict.'image.'.$imgkind;
			if ( is_file($imgname) ){
				unlink($imgname);
			}
		}
		if(!empty($board->file1)){
			$filekind1 = substr($board->file1, -3);
			$filename1 = BBSE_BOARD_UPLOAD_BASE_PATH."bbse-board/".$config->board_no.'_'.$pict.'1.'.$filekind1;
			if ( is_file($filename1) ){
				unlink($filename1);
			}
		}
		if(!empty($board->file2)){
			$filekind2 = substr($board->file2, -3);
			$filename2 = BBSE_BOARD_UPLOAD_BASE_PATH."bbse-board/".$config->board_no.'_'.$pict.'2.'.$filekind2;
			if ( is_file($filename2) ){
				unlink($filename2);
			}
		}
	}
}















// 실제 삭제
for($i = 0; $i < count($delele_no); $i++){
	$prepare = NULL;
	$prepare = $wpdb->prepare( "DELETE FROM {$tblBname} WHERE no= %d", array( $delele_no[$i] ) );
	$wpdb->query( $prepare );

	$prepare = NULL;
	$prepare = $wpdb->prepare( "DELETE FROM {$tblComment} WHERE parent= %d", array( $delele_no[$i] ) );
	$wpdb->query( $prepare );

	$prepare = NULL;
	$prepare = $wpdb->prepare( "UPDATE {$tblBoard} SET list_count=list_count-1 WHERE boardname= %s", array( $V['bname'] ) );
	$wpdb->query( $prepare);
}







echo "<script type='text/javascript'>";
if($re > 0){
	echo "alert('답글이 있을 경우 삭제가 불가능합니다.');";
}
echo "location.href = '".BBSE_BOARD_SITE_URL."?page_id=".$_GET['page_id']."&nType=".bbse_board_parameter_encryption($V['bname'], "list", "", $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])."';";
echo "</script>";
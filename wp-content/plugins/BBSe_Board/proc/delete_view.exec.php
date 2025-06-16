<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

error_reporting(E_ALL);
ini_set("display_errors", 1);

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user     = wp_get_current_user();  // 현재 회원의 정보 추출

$V = $_POST;

if(empty($V['bname']))      { echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>"; exit; }
if(empty($V['no']))         { echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>"; exit; }
if(empty($V['skin']))       { echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>"; exit; }
if(empty($V['page']))       { $V['page']       = 1; }
if(empty($V['keyfield']))   { $V['keyfield']   = NULL; }
if(empty($V['keyword']))    { $V['keyword']    = NULL; }
if(empty($V['search_chk'])) { $V['search_chk'] = NULL; }
if(empty($V['page_id']))    { $V['page_id']    = NULL; }
if(empty($V['mode']))       { $V['mode']       = NULL; }
if(empty($V['cate']))       { $V['cate']       = NULL; }

$tblBoard   = $wpdb->prefix.'bbse_board';
$tblBname   = $wpdb->prefix.'bbse_'.$V['bname'].'_board';
$tblComment = $wpdb->prefix.'bbse_'.$V['bname'].'_comment';

if($curUserPermision == "all" && empty($V['pwd'])){
	echo "
		<script type='text/javascript'>
		location.href = '".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$V['skin']."/delete_view.php?page_id=".$V['page_id']."&nType=".bbse_board_parameter_encryption($V['bname'], $V['mode'], $V['no'], $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])."&wrong=1';
		</script>";
	exit;
}else{
	$prepare = NULL;
	$prepare = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE `boardname`=%s" , array( $V['bname'] ) );
	$config  = $wpdb->get_row( $prepare );

	$prepare = NULL;
	$prepare = $wpdb->prepare( "SELECT * FROM {$tblBname} WHERE `no`=%d", array( $V['no'] ) );
	$board   = $wpdb->get_row( $prepare );
	


	



	$prepare   = NULL;
	$prepare   = $wpdb->prepare( "SELECT count(*) FROM {$tblBname} WHERE `ref`=%d AND `re_step`= %d AND `re_level`=%d", array( $board->ref, ($board->re_step+1), ($board->re_level+1) ) );
	$reply_cnt = $wpdb->get_var( $prepare );

	if($reply_cnt > 0){echo "<script type='text/javascript'>alert('답글이 있을 경우 삭제가 불가능합니다.');history.back();</script>";exit;}

	if(!empty($board->write_date)){
		$tmp1 = explode(" ", $board->write_date);
		$tmp2 = explode("-", $tmp1[0]);
		$tmp3 = explode(":", $tmp1[1]);
		$unique = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
	}

	if($board->re_level != "0") $pict = $unique.'_re';
	else $pict = $V['no']."_";

	if($curUserPermision == "administrator"){
		if(!empty($board->image_file)){
			$imgkind = substr($board->image_file, -3);
			$imgname = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$config->board_no.'_'.$pict.'image.'.$imgkind;
			if ( is_file($imgname) ){
				unlink($imgname);
			}
		}
		if(!empty($board->file1)){
			$filekind1 = substr($board->file1, -3);
			$filename1 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$config->board_no.'_'.$pict.'1.'.$filekind1;
			if ( is_file($filename1) ){
				unlink($filename1);
			}
		}
		if(!empty($board->file2)){
			$filekind2 = substr($board->file2, -3);
			$filename2 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$config->board_no.'_'.$pict.'2.'.$filekind2;
			if ( is_file($filename2) ){
				unlink($filename2);
			}
		}

















		$prepare = NULL;
		$prepare = $wpdb->prepare( "DELETE FROM {$tblBname} WHERE `no`= %d", array( $V['no'] ) );
		$wpdb->query( $prepare );

		$prepare = NULL;
		$prepare = $wpdb->prepare( "DELETE FROM {$tblComment} WHERE `parent`=%d", array( $V['no'] ) );
		$wpdb->query( $prepare );

		$prepare = NULL;
		$prepare = $wpdb->prepare( "UPDATE {$tblBoard} SET `list_count`=`list_count`-1 WHERE `boardname`=%s", array( $V['bname'] ) );
		$wpdb->query( $prepare );

	}else{

		$prepare = NULL;
		$prepare = $wpdb->prepare( "SELECT `pass` FROM {$tblBname} WHERE `no`= %d", array( $V['no'] ) );
		$pass    = $wpdb->get_var( $prepare );
		
		if(!empty($current_user->ID)){
			$prepare  = NULL;
			$prepare  = $wpdb->prepare( "SELECT `user_pass` FROM {$wpdb->users} WHERE `ID`=%s", array( $current_user->ID ) );
			$mem_pass = $wpdb->get_var( $prepare );
		}else{
			$prepare  = NULL;
			$prepare  = $wpdb->prepare( "SELECT password( %s )", array( $V['pwd'] ) );
			$mem_pass = $wpdb->get_var( $prepare );
		}
		
		if($pass == $mem_pass){
			if($board->image_file != ""){
				$imgkind = substr($board->image_file, -3);
				$imgname = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$config->board_no.'_'.$pict.'image.'.$imgkind;
				if ( is_file($imgname) ){
					unlink($imgname);
				}
			}
			if($board->file1 != ""){
				$filekind1 = substr($board->file1, -3);
				$filename1 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$config->board_no.'_'.$pict.'1.'.$filekind1;
				if ( is_file($filename1) ){
					unlink($filename1);
				}
			}
			if($board->file2 != ""){
				$filekind2 = substr($board->file2, -3);
				$filename2 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$config->board_no.'_'.$pict.'2.'.$filekind2;
				if ( is_file($filename2) ){
					unlink($filename2);
				}
			}

















			$prepare = NULL;
			$prepare = $wpdb->prepare( "DELETE FROM {$tblBname} WHERE `no`= %d", array( $V['no'] ) );
			$wpdb->query( $prepare );

			$prepare = NULL;
			$prepare = $wpdb->prepare( "DELETE FROM {$tblComment} WHERE `parent`=%d", array( $V['no'] ) );
			$wpdb->query( $prepare );

			$prepare = NULL;
			$prepare = $wpdb->prepare( "UPDATE {$tblBoard} SET `list_count`=`list_count`-1 WHERE `boardname`=%s", array( $V['bname'] ) );
			$wpdb->query( $prepare );
		}else{
			echo "
				<script type='text/javascript'>
				location.href = '".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$V['skin']."/delete_view.php?page_id=".$V['page_id']."&nType=".bbse_board_parameter_encryption($V['bname'], $V['mode'], $V['no'], $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])."&wrong=2';
				</script>";
			exit;
		}
	}

	echo "
		<script type='text/javascript'>
		top.location.href = '".BBSE_BOARD_SITE_URL."?page_id=".$V['page_id']."&nType=".bbse_board_parameter_encryption($V['bname'], "list", "", $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])."';
		</script>";
	exit;
}
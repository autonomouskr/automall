<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$V = $_POST;

$V['page_id']    = empty($V['page_id'])    ? NULL : $V['page_id'];
$V['bname']      = empty($V['bname'])      ? NULL : $V['bname'];
$V['skin']       = empty($V['skin'])       ? NULL : $V['skin'];
$V['page']       = empty($V['page'])       ? NULL : $V['page'];
$V['keyfield']   = empty($V['keyfield'])   ? NULL : $V['keyfield'];
$V['keyword']    = empty($V['keyword'])    ? NULL : $V['keyword'];
$V['search_chk'] = empty($V['search_chk']) ? NULL : $V['search_chk'];
$V['check']      = empty($V['check'])      ? NULL : $V['check'];
$V['cate']       = empty($V['cate'])       ? NULL : $V['cate'];
$V['mode']       = empty($V['mode'])       ? NULL : $V['mode'];
$V['moveboard']  = empty($V['moveboard'])  ? NULL : $V['moveboard'];

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user     = wp_get_current_user(); // 현재 회원의 정보 추출

if($curUserPermision != "administrator"){
	echo "
		<script type='text/javascript'>
		alert('권한이 없습니다.');
		self.close();
		</script>";
	exit;
}

if(empty($V['record'])) $V['record'] = 0;
if( !empty($V['bname']) && !empty($V['moveboard']) ){
	$tblBoard    = $wpdb->prefix.'bbse_board';
	$tblBname    = $wpdb->prefix.'bbse_'.$V['bname'].'_board';
	$tblComment  = $wpdb->prefix.'bbse_'.$V['bname'].'_comment';

	$tblMBname   = $wpdb->prefix.'bbse_'.$V['moveboard'].'_board';
	$tblMComment = $wpdb->prefix.'bbse_'.$V['moveboard'].'_comment';
} else {
	echo "
		<script type='text/javascript'>
		alert('권한이 없습니다.');
		self.close();
		</script>";
	exit;
}
$prepare   = NULL;
$prepare   = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE `boardname`= %s", array( $V['bname'] ) );
$boardInfo = $wpdb->get_row( $prepare );

$prepare  = NULL;
$prepare  = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE `boardname`= %s", array( $V['moveboard'] ) );
$moveInfo = $wpdb->get_row( $prepare );

if(!empty($boardInfo->board_no) && !empty($moveInfo->board_no) && $boardInfo->board_no != $moveInfo->board_no){
	$check = explode("_", $V['check']);

	for($i = 0; $i < count($check) - 1; $i++){
		/* 게시물 옮기기 start */
		$prepare = NULL;
		$prepare = $wpdb->prepare( "SELECT * FROM {$tblBname} WHERE `no`= %d", array( $check[$i] ) );
		$board   = $wpdb->get_row( $prepare, ARRAY_A );

		// 일반게시판 게시물
		if($boardInfo->formtype == 1){
			$add_fields = NULL;
			$add_values = NULL;
		// 웹진 or 갤러리게시판 게시물
		}else if($boardInfo->formtype == 2 || $boardInfo->formtype == 3){
			$add_fields = ", `image_file`, `image_file_alt`, `use_content_image` ";
			if($moveInfo->formtype == 2 || $moveInfo->formtype == 3){  // 이동/복사될 게시판이 웹진 or 갤러리게시판일때..
				$add_values = $wpdb->prepare( ", %s, %s, %s ", array( $board['image_file'], $board['image_file_alt'], $board['use_content_image'] ));  ;
			}else{
				$add_values = ", '', '', '' ";
			}
		}
		$prepare   = NULL;
		$prepare   = $wpdb->prepare(
			"INSERT INTO {$tblMBname}
				(`memnum`,     `memlevel`,   `writer`,  `pass`,  `email`,    `phone`,
				 `write_date`, `title`,      `content`, `hit`,   `category`, `use_notice`,
				 `use_html`,   `use_secret`, `file1`,   `file2`, `re_step`,  `re_level`,
				 `ip` {$add_fields})
			VALUES
				(%d, %s, %s, %s, %s, %s,
				 %s, %s, %s, %d, %s, %s,
				 %s, %s, %s, %s, %d, %d,
				 %s  {$add_values})",
			array(
					$board['memnum'],     $board['memlevel'],   $board['writer'],  $board['pass'],  $board['email'],    $board['phone'],
				  $board['write_date'], $board['title'],      $board['content'], $board['hit'],   $board['category'], $board['use_notice'],
				  $board['use_html'],   $board['use_secret'], $board['file1'],   $board['file2'], $board['re_step'],  $board['re_level'],
				  $board['ip']
			)
		);

		$wpdb->query($prepare);
		$ref_board = $wpdb->get_row("SELECT max(`no`) FROM {$tblMBname}", ARRAY_N);

		if($board['listnum'] == "0"){
			$idx       = $ref_board[0];
			$ref       = $board['ref'];
			$listnum   = 0;
			$movecheck = 0;
		}else{
			$idx       = $ref_board[0];
			$ref       = $ref_board[0];
			$listnum   = 1;
			$movecheck = $boardInfo->board_no.'_'.$board['ref']."_1";
		}

		$prepare   = NULL;
		$prepare   = $wpdb->prepare( "UPDATE {$tblMBname} SET `ref`= %d, `listnum`= %s, `movecheck`= %s WHERE `no`= %d", array( $ref, $listnum, $movecheck, $idx) );
		$wpdb->query( $prepare );

		$prepare   = NULL;
		$prepare   = $wpdb->prepare( "UPDATE {$tblBoard} SET `list_count`=`list_count`+1 WHERE `boardname`= %s" , array( $V['moveboard'] ) );
		$wpdb->query( $prepare );
		/* 게시물 옮기기 end */

		/* 첨부파일 이동하기 start */
		if($board['image_file'] != ""){
			$exploded    = explode(".", $board['image_file']);
			$fexe        = NULL;
			$fexe        = end( $exploded );
			$imgfilename = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$board['no'].'_image.'.$fexe;
			$imgnewfile  = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$moveInfo->board_no.'_'.$idx.'_image.'.$fexe;

			if( ($boardInfo->formtype == 2 || $boardInfo->formtype == 3) && ($moveInfo->formtype == 2 || $moveInfo->formtype == 3)){
				if ( is_file($imgfilename) ) {
					copy($imgfilename, $imgnewfile);
				}
			}
		}
		if($board['file1'] != ""){
			$fexe      = NULL;
			$fexe      = substr($board['file1'], -3);
			$filename1 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$board['no'].'_1.'.$fexe;
			$newfile1  = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$moveInfo->board_no.'_'.$idx."_1.".$fexe;
			if ( is_file($filename1) ) {
				copy($filename1, $newfile1);
			}
		}
		if($board['file2'] != ""){
			$fexe      = NULL;
			$fexe      = substr($board['file2'], -3);
			$filename2 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$board['no'].'_2.'.$fexe;
			$newfile2  = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$moveInfo->board_no.'_'.$idx."_2.".$fexe;
			if ( is_file($filename2) ) {
				copy($filename2, $newfile2);
			}
		}
		/* 첨부파일 이동하기 end */

		/* 코멘트 옮기기 start */
		$prepare = NULL;
		$prepare = $wpdb->prepare( "SELECT * FROM {$tblComment} WHERE `parent`=%d order by `no` asc", array( $check[$i] ) );
		$cresult = $wpdb->get_results( $prepare );

		$prepare = NULL;
		$prepare = $wpdb->prepare( "SELECT count(*) FROM {$tblComment} WHERE `parent`= %d", array( $check[$i] ) );
		$listcnt = $wpdb->get_var( $prepare );

		if($listcnt == 0){
		}else{
			foreach($cresult as $comment){

				$prepare = NULL;
				$prepare = $wpdb->prepare(
					"INSERT INTO {tblMComment}
						(`parent`, `comm_parent`, `memnum`, `writer`, `pass`, `content`, `ip`, `depth`, `move_no`, `write_date`)
					VALUES
						(%d, %d, %d, %s, %s, %s, %s, %d, %d, %s)",
					array(
						$idx,              0,            $comment->memnum, $comment->writer,      $comment->pass,
						$comment->content, $comment->ip, $comment->depth,  $comment->comm_parent, $comment->write_date
					)
				);
				$wpdb->query($prepare);

				$comment_id = $wpdb->insert_id;

				if(empty($comment->depth) || $comment->depth == 0){
					$prepare = NULL;
					$prepare = $wpdb->prepare( "UPDATE {tblMComment} SET `comm_parent`= %d WHERE `no`= %d", array( $comm_parent, $comment_id ) );
					$wpdb->query( $prepare );
				}else if($comment->depth == 1){
					$prepare     = NULL;
					$prepare     = $wpdb->prepare( "SELECT `no` FROM {tblMComment} WHERE `move_no`=%d AND depth='0'", array( $comment->comm_parent ) );
					$comm_parent = $wpdb->get_var( $prepare );

					$prepare = NULL;
					$prepare = $wpdb->prepare( "UPDATE {tblMComment} SET `comm_parent`= %d WHERE `no`= %d", array( $comm_parent, $comment_id ) );
					$wpdb->query( $prepare );
				}
			}
		}
		/* 코멘트 옮기기 end */

		/* 이동일때 : 기존 게시물 삭제 or 이동내역 기록 start */
		if($V['mode'] == "move"){

			if(isset($imgfilename)) unlink($imgfilename);
			if(isset($filename1))   unlink($filename1);
			if(isset($filename2))   unlink($filename2);

			if($V['record'] == '1'){
				$prepare = NULL;
				$prepare = $wpdb->prepare( "
					UPDATE {$tblBname} SET `title`=%s, `content`=%s, `file1`='', `file2`='' WHERE `no`= %d",
					array(
						'본 게시물은 '.$current_user->user_login.'님에 의해 '.$V['moveboard'].' 게시판으로 이동되었습니다.',
						'본 게시물은 '.$current_user->user_login.'님에 의해 '.$V['moveboard'].' 게시판으로 이동되었습니다.',
						$check[$i]
					) );
				$wpdb->query($prepare);
			}else{
				$prepare = NULL;
				$prepare = $wpdb->prepare( "DELETE FROM {$tblBname} WHERE `no`= %d", array( $check[$i] ) );
				$wpdb->query( $prepare );

				$prepare = NULL;
				$prepare = $wpdb->prepare( "UPDATE {$tblBoard} SET `list_count`=`list_count`-1 WHERE `boardname`= %s", array( $V['bname'] ) );
				$wpdb->query( $prepare );
			}
			$prepare = NULL;
			$prepare = $wpdb->prepare( "DELETE FROM {$tblComment} WHERE `parent`= %d", array( $check[$i] ) );
			$wpdb->query( $prepare );
		}
		/* 이동일때 : 기존 게시물 삭제 or 이동내역 기록 end */
	}

	/* 답변 게시물일 경우 부모게시물의 유무 확인 start */
	$result  = $wpdb->get_results("SELECT `no`, `ref` FROM {$tblMBname} WHERE `movecheck`='0'");
	$listcnt = $wpdb->get_var("SELECT count(*) FROM {$tblMBname} WHERE `movecheck`='0'");

	if($listcnt == 0){
	}else{
		foreach($result as $list){
			$prepare = NULL;
			$prepare = $wpdb->prepare( "SELECT `no`, `movecheck` FROM {$tblMBname} WHERE `movecheck`='%s  AND `listnum`=1", array( $boardInfo->board_no.'_'.$list->ref.'_1' ) );
			$check   = $wpdb->get_row($prepare);

			if(!empty($check->no)){
				$prepare = NULL;
				$prepare = $wpdb->prepare( "update {$tblMBname} SET `ref`=%d, `movecheck`=%s WHERE `no`=%d", array( $check->no, $check->movecheck, $list->no ) );
				$wpdb->query( $prepare );
			}
		}
	}
	/* 답변 게시물일 경우 부모게시물의 유무 확인 end */

	echo "
		<script type='text/javascript'>
		location.href = '".BBSE_BOARD_SITE_URL."?page_id=".$V['page_id']."&nType=".bbse_board_parameter_encryption($V['bname'], "list", "", $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])."';
		</script>";
	exit;
}else{
	echo "
		<script type='text/javascript'>
		alert('정상적인 접근이 아닙니다.');
		history.back();
		</script>";
	exit;
}
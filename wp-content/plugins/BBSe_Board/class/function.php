<?php
/*-------------------------------------------------------------------
 * 관리자 페이지 생성 함수 start
 ------------------------------------------------------------------*/
// 게시판 목록
if(!function_exists('bbse_board_list')){
	function bbse_board_list(){
		bbse_board_create_tables();

		global $wpdb;
		wp_enqueue_style('bbse-board-admin-ui', BBSE_BOARD_PLUGIN_WEB_URL.'css/admin-style.css');

		if(!empty($_POST['check']))   $check   = $_POST['check'];
		if(!empty($_POST['tBatch1'])) $tBatch1 = trim($_POST['tBatch1']);
		if(!empty($_POST['tBatch2'])) $tBatch2 = trim($_POST['tBatch2']);

//

		// 선택삭제
		if((!empty($check) && count($check) > 0) && ($tBatch1 == 'remove' or $tBatch2 == 'remove')){
			$delete_type = "select";

			$delete_no = array();
			for($i = 0; $i < count($check); $i++){
				$board_no = $check[$i];
				$oldData = $wpdb->get_row("SELECT `boardname`, `use_syndi` FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`='".$board_no."'");

//

				if(!empty($board_no) && $oldData->boardname){
					$wpdb->query("DELETE FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`='".$board_no."'");

					$cntBoard = $wpdb->get_var("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_name='".$wpdb->prefix."bbse_".$oldData->boardname."_board'");
					if($cntBoard > 0){
						$wpdb->query("DROP TABLE `".$wpdb->prefix."bbse_".$oldData->boardname."_board`");
					}

					$cntComment = $wpdb->get_var("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_name='".$wpdb->prefix."bbse_".$oldData->boardname."_comment'");
					if($cntComment > 0){
						$wpdb->query("DROP TABLE `".$wpdb->prefix."bbse_".$oldData->boardname."_comment`");
					}
				}
			}

		// 개별삭제
		}else{
			$delete_type = "one";
			if(!empty($_POST['delNo'])){
				$board_no = trim($_POST['delNo']);
				$oldData = $wpdb->get_row("SELECT `boardname`, `use_syndi` FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`='".$board_no."'");

//

				if(!empty($board_no) && $oldData->boardname){
					$wpdb->query("DELETE FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`='".$board_no."'");

					$cntBoard = $wpdb->get_var("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_name='".$wpdb->prefix."bbse_".$oldData->boardname."_board'");
					if($cntBoard > 0){
						$wpdb->query("DROP TABLE `".$wpdb->prefix."bbse_".$oldData->boardname."_board`");
					}

					$cntComment = $wpdb->get_var("SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_name='".$wpdb->prefix."bbse_".$oldData->boardname."_comment'");
					if($cntComment > 0){
						$wpdb->query("DROP TABLE `".$wpdb->prefix."bbse_".$oldData->boardname."_comment`");
					}
				}
			}
		}

		$BBSeBoard = new BBSeBoard();
		require_once(BBSE_BOARD_PLUGIN_ABS_PATH."admin/board_list.php");
	}
}

// 게시판 생성
if(!function_exists('bbse_board_config')){
	function bbse_board_config(){
		bbse_board_create_tables();

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		wp_enqueue_style('bbse-board-admin-ui', BBSE_BOARD_PLUGIN_WEB_URL.'css/admin-style.css');

		$V = $_POST;
		$tMode = "";
		$syndi_use_val = get_option("bbse_board_syndi_use");

		if(empty($V['hidden_hit']))     $V['hidden_hit']     = 0;
		if(empty($V['hidden_writer']))  $V['hidden_writer']  = 0;
		if(empty($V['use_comment']))    $V['use_comment']    = 0;
		if(empty($V['use_reply']))      $V['use_reply']      = 0;
		if(empty($V['use_secret']))     $V['use_secret']     = 0;
		if(empty($V['use_notice']))     $V['use_notice']     = 0;
		if(empty($V['use_phone']))      $V['use_phone']      = 0;
		if(empty($V['use_email']))      $V['use_email']      = 0;
		if(empty($V['use_pds']))        $V['use_pds']        = 0;
		if(empty($V['use_filter']))     $V['use_filter']     = 0;
		if(empty($V['use_facebook']))   $V['use_facebook']   = 0;
		if(empty($V['use_twitter']))    $V['use_twitter']    = 0;
//
		if(empty($V['use_kakaotalk']))  $V['use_kakaotalk']  = 0;
		if(empty($V['use_kakaostory'])) $V['use_kakaostory'] = 0;
		if(empty($V['use_seo']))        $V['use_seo']        = 0;
		if(empty($V['use_syndi']))      $V['use_syndi']      = 0;
//
		if($V['use_filter'] == 0){
			$V['filter_list'] = "";
		}
		if($V['use_seo'] == 0){
			$V['seo_title'] = $V['seo_description'] = $V['seo_keywords'] = "";
		}
//
//
//
//
//

		if(!empty($V['use_pds'])){
			if(empty($V['upload_size'])){echo "<script type='text/javascript'>alert('첨부파일 용량을 입력해주세요.');history.back();</script>";exit;}
			if($V['upload_size'] > 5){echo "<script type='text/javascript'>alert('첨부파일 용량은 1 ~ 5 사이의 숫자만 입력가능합니다.');history.back();</script>";exit;}
		}
		if(!empty($V['table_width'])){if(!ctype_digit($V['table_width'])){echo "<script type='text/javascript'>alert('가로사이즈는 숫자만 입력가능합니다.');history.back();</script>";exit;}}
		if(!empty($V['page_size'])){if(!ctype_digit($V['page_size'])){echo "<script type='text/javascript'>alert('페이지당 목록 수는 숫자만 입력가능합니다.');history.back();</script>";exit;}}
		if(!empty($V['upload_size'])){
			if(!ctype_digit($V['upload_size'])){echo "<script type='text/javascript'>alert('첨부파일 용량은 숫자만 입력가능합니다.');history.back();</script>";exit;}
		}

		if(!empty($V['kakao_app_key'])) update_option("bbse_board_kakao_app_key", trim($_POST['kakao_app_key']));

		/* 추가 / 수정 */
		if(!empty($V['boardname']) && !empty($V['skinname'])){
			$V['reg_date'] = current_time('timestamp');

			$existCnt = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."bbse_board` WHERE `boardname`='".$V['boardname']."'");

			if(empty($V['use_seo']) || $V['use_seo'] == 0){
				$V['seo_title'] = "";
				$V['seo_description'] = "";
				$V['seo_keywords'] = "";
			}

//
//
//
//
//

			// 추가
			if(empty($V['board_no']) and $existCnt <= 0){
				$sql = "INSERT INTO `".$wpdb->prefix."bbse_board` (
					`boardname`, `skinname`, `formtype`, `editor`, `table_width`, `table_align`, `page_size`, `category_list`, `use_filter`, `filter_list`, `use_pds`, `upload_size`, `use_comment`, `use_reply`, `use_secret`, `use_notice`, `using_subAutoWtype`, `l_read`, `l_list`, `l_comment`, `l_reply`, `l_write`, `use_seo`, `seo_title`, `seo_description`, `seo_keywords`, `use_syndi`, `hidden_hit`, `hidden_writer`, `use_phone`, `use_email`, `use_facebook`, `use_twitter`, `use_kakaotalk`, `use_kakaostory`, `user_list`, `no_permission_url`, `list_count`, `reg_date`, `thumb_ratio`,
					`ext_var1`, `ext_var2`, `ext_var3`, `ext_var4`, `ext_var5`, `ext_var6`, `ext_var7`, `ext_var8`, `ext_var9`, `ext_var10`
				) VALUES (
					'".$V['boardname']."', '".$V['skinname']."', '".$V['formtype']."', '".$V['editor']."', '".$V['table_width']."', '".$V['table_align']."', '".$V['page_size']."', '".htmlentities(sanitize_text_field($V['category_list']),ENT_QUOTES)."', '".$V['use_filter']."', '".$V['filter_list']."', '".$V['use_pds']."', '".$V['upload_size']."', '".$V['use_comment']."', '".$V['use_reply']."', '".$V['use_secret']."', '".$V['use_notice']."', '".$V['using_subAutoWtype']."', '".$V['l_read']."', '".$V['l_list']."', '".$V['l_comment']."', '".$V['l_reply']."', '".$V['l_write']."', '".$V['use_seo']."', '".$V['seo_title']."', '".$V['seo_description']."', '".$V['seo_keywords']."', '".$V['use_syndi']."', '".$V['hidden_hit']."', '".$V['hidden_writer']."', '".$V['use_phone']."', '".$V['use_email']."', '".$V['use_facebook']."', '".$V['use_twitter']."', '".$V['use_kakaotalk']."', '".$V['use_kakaostory']."', '".htmlentities(sanitize_text_field($V['user_list']),ENT_QUOTES)."', '".$V['no_permission_url']."', 0, '".$V['reg_date']."', '".$V['thumb_ratio']."',
					'".$V['ext_var1']."', '".$V['ext_var2']."', '".$V['ext_var3']."', '".$V['ext_var4']."', '".$V['ext_var5']."',
					'".$V['ext_var6']."', '".$V['ext_var7']."', '".$V['ext_var8']."', '".$V['ext_var9']."', '".$V['ext_var10']."'
				)";
				$wpdb->query($sql);

				$tmpBoardData = $wpdb->get_row("SELECT `board_no` FROM `".$wpdb->prefix."bbse_board` WHERE `boardname`='".$V['boardname']."' AND `skinname`='".$V['skinname']."'");
				$board_no     = $tmpBoardData->board_no;
				$tMode        = "insert";

				if(!empty($board_no) and $V['boardname']){
					/* bbse_$boardname_board 보드 테이블 생성 */
					dbdelta_bbse_x_board($V['boardname']);
				}

			// 수정
			}else{
				if(!empty($V['board_no'])){
					$sql = "UPDATE `".$wpdb->prefix."bbse_board` SET `skinname`='".$V['skinname']."', `formtype`='".$V['formtype']."', `editor`='".$V['editor']."', `table_width`='".$V['table_width']."', `table_align`='".$V['table_align']."', `page_size`='".$V['page_size']."', `category_list`='".htmlentities(sanitize_text_field($V['category_list']),ENT_QUOTES)."', `use_filter`='".$V['use_filter']."', `filter_list`='".$V['filter_list']."', `use_pds`='".$V['use_pds']."', `upload_size`='".$V['upload_size']."', `use_comment`='".$V['use_comment']."', `use_reply`='".$V['use_reply']."', `use_secret`='".$V['use_secret']."', `use_notice`='".$V['use_notice']."', `using_subAutoWtype`='".$V['using_subAutoWtype']."', `l_read`='".$V['l_read']."', `l_list`='".$V['l_list']."', `l_comment`='".$V['l_comment']."', `l_reply`='".$V['l_reply']."', `l_write`='".$V['l_write']."', `use_seo`='".$V['use_seo']."', `seo_title`='".$V['seo_title']."', `seo_description`='".$V['seo_description']."', `seo_keywords`='".$V['seo_keywords']."', `use_syndi`='".$V['use_syndi']."', `hidden_hit`='".$V['hidden_hit']."', `hidden_writer`='".$V['hidden_writer']."', `use_phone`='".$V['use_phone']."', `use_email`='".$V['use_email']."', `use_facebook`='".$V['use_facebook']."', `use_twitter`='".$V['use_twitter']."', `use_kakaotalk`='".$V['use_kakaotalk']."', `use_kakaostory`='".$V['use_kakaostory']."',
					`user_list`='".htmlentities(sanitize_text_field($V['user_list']),ENT_QUOTES)."', `no_permission_url`='".$V['no_permission_url']."', `thumb_ratio`='".$V['thumb_ratio']."',
					`ext_var1` = '".$V['ext_var1']."', `ext_var2` = '".$V['ext_var2']."', `ext_var3` = '".$V['ext_var3']."',
					`ext_var4` = '".$V['ext_var4']."', `ext_var5` = '".$V['ext_var5']."',	`ext_var6` = '".$V['ext_var6']."',
					`ext_var7` = '".$V['ext_var7']."', `ext_var8` = '".$V['ext_var8']."', `ext_var9` = '".$V['ext_var9']."',
					`ext_var10`= '".$V['ext_var10']."' WHERE `board_no`='".$V['board_no']."'";
					$wpdb->query($sql);
					$board_no = $V['board_no'];
					$tMode = "modify";
				}
			}

		/* 보기 */
		}else if(!empty($_GET['board_no'])){
			$board_no = $_GET['board_no'];
		}

		if(!empty($board_no) and $board_no > 0){
			$data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`='".$board_no."'");
		}
		$kakao_app_key = get_option("bbse_board_kakao_app_key");

		require_once(BBSE_BOARD_PLUGIN_ABS_PATH."class/auth_txt.php");
		require_once(BBSE_BOARD_PLUGIN_ABS_PATH."admin/board_config.php");
	}
}

// 개인정보 취급방침
if(!function_exists('bbse_board_private')){
	function bbse_board_private(){
		bbse_board_create_tables();

		global $wpdb;
		wp_enqueue_style('bbse-board-admin-ui', BBSE_BOARD_PLUGIN_WEB_URL.'css/admin-style.css');

		$sMode = '';
		if(!empty($_POST['cnf_contents'])) $cnf_contents = $_POST['cnf_contents'];
		if(empty($_POST['use_private'])) $use_private = "Y";
		else $use_private = $_POST['use_private'];

		$chkconfig = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."bbse_board_config`");
		if($chkconfig > 1) $wpdb->query('DELETE FROM `'.$wpdb->prefix.'bbse_board_config` LIMIT '.($chkconfig-1));
		if(!empty($cnf_contents)){
			if($chkconfig > 0){
				$wpdb->query("UPDATE `".$wpdb->prefix."bbse_board_config` SET `cnf_contents`='".$cnf_contents."', `use_private`='".$use_private."'");
				$sMode = 'modify';
			}else{
				$wpdb->query("INSERT INTO `".$wpdb->prefix."bbse_board_config` (`cnf_contents`, `use_private`) VALUES ('".$cnf_contents."', '".$use_private."')");
				$sMode = 'insert';
			}
		}

		$data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bbse_board_config`");
		require_once(BBSE_BOARD_PLUGIN_ABS_PATH."admin/board_private.php");
	}
}

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//



// SSL(보안서버)설정
if(!function_exists('bbse_board_ssl')){
	function bbse_board_ssl(){
		bbse_board_create_tables();

		global $wpdb;

		wp_enqueue_style('bbse-board-admin-ui', BBSE_BOARD_PLUGIN_WEB_URL.'css/admin-style.css');

		$chkconfig = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."bbse_board_config`");
		if($chkconfig > 1) $wpdb->query('DELETE FROM `'.$wpdb->prefix.'bbse_board_config` LIMIT '.($chkconfig-1));

		$EDIT_CONFIG_URL = BBSE_BOARD_SSL_CONFIG_URL."&mode=edit";

		if(!empty($_GET['mode']) && $_GET['mode'] == "edit"){
			$V = $_POST;

			if(empty($V['use_ssl'])) $V['use_ssl'] = 0;
			if($V['use_ssl'] == 1 && !empty($V['ssl_domain'])){
				if(!empty($V['ssl_port'])){
					if(!ctype_digit($V['ssl_port'])){echo "<script type='text/javascript'>alert('포트번호는 숫자만 입력가능합니다.');history.back();</script>";exit;}
				}
			}

			if($chkconfig > 0){
				$wpdb->query("UPDATE `".$wpdb->prefix."bbse_board_config` SET `use_ssl`='".$V['use_ssl']."', `ssl_domain`='".$V['ssl_domain']."', `ssl_port`='".$V['ssl_port']."'");
			}else{
				$wpdb->query("INSERT INTO `".$wpdb->prefix."bbse_board_config` (`use_ssl`, `ssl_domain`, `ssl_port`) VALUES ('".$V['use_ssl']."', '".$V['ssl_domain']."', '".$V['ssl_port']."')");
			}

			echo "
				<form method='post' name='action_frm' action='".BBSE_BOARD_SSL_CONFIG_URL."'>
				<input type='hidden' name='save_check' value='1' />
				</form>
				<script type='text/javascript'>document.action_frm.submit();</script>";
		}

		if(!empty($_POST['save_check']) && $_POST['save_check'] == "1") $edit_mode = "edit";
		require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'admin/board_ssl.php');
	}
}

// 데이터 백업/복구
if(!function_exists('bbse_board_backup')){
	function bbse_board_backup(){
		bbse_board_create_tables();

		global $wpdb;

		wp_enqueue_style('bbse-board-admin-ui', BBSE_BOARD_PLUGIN_WEB_URL.'css/admin-style.css');

		if(!empty($_POST['save_check']) && $_POST['save_check'] == "1") $edit_mode = "edit";
		require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'admin/board_backup.php');
	}
}

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/*-------------------------------------------------------------------
 * 관리자 페이지 생성 함수 end
 ------------------------------------------------------------------*/

// GET 파라미터 복호화
if(!function_exists('bbse_board_parameter_decryption')){
	function bbse_board_parameter_decryption($destCode){
		$deCode = new BBSeSecretCode;
		$aCode = $deCode->dec(base64_decode($destCode));

		$bCode = explode("&", $aCode);
		$rtnCode = Array();

		for($i = 0; $i < count($bCode); $i++){
			$cCode = explode("=", $bCode[$i]);
			if(count($cCode) == 2){
				$rtnCode[$cCode[0]] = $cCode[1];
			}
		}
		return $rtnCode;
	}
}

// GET 파라미터 암호화
if(!function_exists('bbse_board_parameter_encryption')){
	function bbse_board_parameter_encryption($boardName, $mode, $no="", $page="", $keyfield="", $keyword="", $search_chk="", $category="", $ref="", $cno=""){
		$enCode = new BBSeSecretCode;
		$srcCode = "bname=".$boardName."&mode=".$mode."&no=".$no."&page=".$page."&keyfield=".$keyfield."&keyword=".$keyword."&search_chk=".$search_chk."&cate=".$category."&ref=".$ref."&cno=".$cno;
		$rstCode = $enCode->enc($srcCode);

		return base64_encode($rstCode);
		//return $srcCode;
	}
}

// 접속자 회원등급
if(!function_exists('current_user_level')){
	function current_user_level(){
		//관리자
		if(current_user_can('level_10') || current_user_can('level_9') || current_user_can('level_8') ){
			return "administrator";
		//편집자
		} else if(current_user_can('level_7') || current_user_can('level_6') || current_user_can('level_5') || current_user_can('level_4') || current_user_can('level_3') ){
			return "editor";
		//글쓴이
		} else if(current_user_can('level_2') ){
			return "author";
		//기여자
		} else if(current_user_can('level_1') ){
			return "contributor";
		//구독자
		}else if(current_user_can('level_0')){
			return "subscriber";
		}
		else return "all";  // 게스트
	}
}

// 문자열 자르기
if(!function_exists('cut_text')){
	function cut_text($string, $length, $more_text="…") {
		if ( defined("ENT_IGNORE") ) $content = html_entity_decode(strip_tags($string), ENT_QUOTES | ENT_IGNORE, "UTF-8");
		else                         $content = html_entity_decode(strip_tags($string), ENT_QUOTES, "UTF-8");

		if ( mb_strlen($content) > $length ) return stripslashes(esc_html(mb_substr($content, 0, $length).$more_text));
		else                                 return stripslashes(esc_html($content));
	}
}

if(!function_exists("bbse_board_create_paging")){
	function bbse_board_create_paging($paged, $total_pages, $add_args=false){
		/**
		 * $paged : 현재 페이지
		 * $total_pages : 총 페이지
		 * $add_args : 추가 전달값(쿼리스트링)
		 */

		$paging_css = "
			<style>
			.pagination{
				clear:both;
				padding:20px 0;
				position:relative;
				font-size:11px;
				line-height:13px;
			}
			.pagination span, .pagination a{
				display:block;
				float:left;
				margin: 2px 2px 2px 0;
				padding:6px 9px 5px 9px;
				text-decoration:none;
				width:auto;
				color:#fff !important;
				background: #6d6d6d !important;
			}
			.pagination a:hover{
				color:#fff !important;
				background: #3279bb !important;
			}
			.pagination .current{
				padding:6px 9px 5px 9px;
				background: #3279bb !important;
				color:#fff !important;
			}
			</style>";

		$paging = paginate_links(array(
			'base'     => '%_%',
			'format'   => '?paged=%#%',
			'current'  => max( 1, $paged ),
			'total'    => $total_pages,
			'mid_size' => 20,
			'add_args' => $add_args
		));

		return $paging_css."<div class=\"pagination\">".$paging."</div>";
	}
}

// 디렉토리 삭제(파일이 있을 경우도 가능)
if(!function_exists('rmdir_rf')){
	function rmdir_rf($dirname){
		if($dirHandle = opendir($dirname)){
			chdir($dirname);
			while($file = readdir($dirHandle)){
				if($file == '.' || $file == '..') continue;
				if(is_dir($file)) rmdir_rf($file);
				else unlink($file);
			}
			chdir('..');
			rmdir($dirname);
			closedir($dirHandle);
		}
	}
}

// 첫이미지를 가져옵니다.
if (!function_exists('bbse_board_first_image')){
	function bbse_board_first_image($data){

		if ( is_object($data) ){
			// 대표 이미지
			if ( $data->image_file ){
				return home_url().'/wp-content/uploads/bbse-board/'.$data->board_no.'_'.$data->no.'_image.'.array_pop(explode('.',$data->image_file));;
			// 업로드 이미지
			} elseif ( $data->file1 || $data->file2 ) {
				$ext   = array('.gif','.jpeg','.jpg','.png');
				$files = array($data->file1, $data->file2);

				for($i=0; $i<2; $i++){
					foreach($ext as $k => $needle){
						if( strpos(strtolower($files[$i]), $needle) != false){
							return home_url().'/wp-content/uploads/bbse-board/'.$data->board_no.'_'.$data->no.'_'.($i+1).$needle;
						}
					}
				}
			// 삽입 이미지
			} else {
				$data = $data->content;
			}
		}

		if ( !is_object($data) && !is_array($data) ){
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $data, $linked_image);
			if (isset($linked_image[1][0])){// 있으면
				return $linked_image[1][0];
			} else {
				return false;
			}
		}

		return false;
  }
}
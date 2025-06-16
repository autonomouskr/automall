<?php
$currentSessionID = $_POST['sess_id'];
session_id($currentSessionID);
session_start();

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';

$site_url_arr    = explode("/", home_url());
$site_domain_url = 'http://'.$site_url_arr[2];

header("Content-Type: text/html; charset=UTF-8");
header("Access-Control-Allow-Origin:".$site_domain_url);
header("Access-Control-Allow-Credentials: true");
header("X-Content-Type-Options:nosniff");
header("X-XSS-Protection:1; mode=block");

if(!function_exists("json_encode")){
	function json_encode($a=false){
		if(is_null($a))  return 'null';
		if($a === false) return 'false';
		if($a === true)  return 'true';
		if(is_scalar($a)){
			if(is_float($a)){
				return floatval(str_replace(",", ".", strval($a)));
			}

			if(is_string($a)){
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}else{
				return $a;
			}
		}
		$isList = true;
		for($i=0, reset($a); $i<count($a); $i++, next($a)){
			if(key($a) !== $i){
				$isList = false;
				break;
			}
		}
		$result = array();
		if($isList){
			foreach($a as $v) $result[] = json_encode($v);
			return '[' . join(',', $result) . ']';
		} else{
			foreach($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
			return '{' . join(',', $result) . '}';
		}
	}
}

$V         = $_POST;
$json_data = array();

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user     = wp_get_current_user();  // 현재 회원의 정보 추출

if(empty($V['page_id'])){
	echo 'empty page_id';
	exit;
}
if(empty($V['bname'])){
	echo 'empty bname';
	exit;
}

$tblBoard  = $wpdb->prefix.'bbse_board';
$tblBname  = $wpdb->prefix.'bbse_'.$V['bname'].'_board';

$config    = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bbse_board_config");
$prepare   = NULL;
$prepare   = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE boardname = %s ", array( $V['bname'] ) );
$boardInfo = $wpdb->get_row( $prepare );

/*---------- 필수입력 검증 start ----------*/
if(empty($V['title'])){
	echo 'empty title';
	exit;
}
if(!empty($V['use_category']) && $V['use_category'] == 1){
	if(empty($V['category'])){
		echo 'empty category';
		exit;
	}
}

//if(empty($curUserPermision) || $curUserPermision != "author"){
if(empty($curUserPermision) || ( $curUserPermision != 'subscriber' && $curUserPermision != 'contributor' && $curUserPermision != 'author' && $curUserPermision != 'editor' )){
	if(empty($V['writer'])){
		echo 'empty writer';
		exit;
	}

	if(mb_strlen($V['writer']) > 16){
		echo 'long writer';
		exit;
	}
}

if(!empty($V['validate_pass']) && $V['validate_pass'] == 1){
	if(empty($V['pass'])){
		echo 'empty pass';
		exit;
	}
	if(mb_strlen($V['pass']) > 16){
		echo 'long pass';
		exit;
	}
}
if(empty($V['content'])){
	echo 'empty content';
	exit;
}
if(!empty($boardInfo->formtype) && ($boardInfo->formtype == 2 || $boardInfo->formtype == 3)){
	if(empty($V['old_image'])){
		if(empty($_FILES['image_file']['name'])){
			echo 'empty image_file';
			exit;
		}
	}else{
		if(!empty($V['image_del']) && $V['image_del'] == 1){
			if(empty($_FILES['image_file']['name'])){
				echo 'empty image_file';
				exit;
			}
		}
	}
}
if(!empty($boardInfo->using_subAutoWtype) && $boardInfo->using_subAutoWtype != "NO"){
	if(empty($_SESSION['authKey']) || empty($V['string']) || $_SESSION['authKey'] != $V['string']){
		echo 'auth error';
		exit;
	}
}
if((empty($curUserPermision) || $curUserPermision == 'all') && (empty($config->use_private) || $config->use_private == "Y")){
	if(empty($V['agree1'])){
		echo 'empty agree1';
		exit;
	}
}
/*---------- 필수입력 검증 end ----------*/

/*---------- 입력항목 정의 start ----------*/
if(!empty($curUserPermision) && $curUserPermision != 'all'){
	if(!empty($current_user->ID))               $memnum   = $current_user->ID;
	if(!empty($curUserPermision))               $memlevel = $curUserPermision;
	if(!empty($current_user->user_pass))        $pass     = $current_user->user_pass;
	if(!empty($current_user->data->user_email)) $email    = $current_user->data->user_email;
}else{
	$memnum = "0";
	$memlevel = NULL;
	if(!empty($V['pass']))  $pass  = $V['pass'];
	if(!empty($V['email'])) $email = $V['email'];
}

if(!empty($V['category'])) $category = $V['category'];
else                       $category = NULL;

$unique     = current_time('timestamp');
$write_date = date("Y-m-d H:i:s", $unique);

if(!empty($V['use_notice'])) $use_notice = $V['use_notice'];
else                         $use_notice = '0';

if(!empty($V['editor']) && $V['editor'] != 'N'){
	$use_html = '2';
}else if(!empty($V['editor']) && $V['editor'] == 'N'){
	if(!empty($V['use_html'])) $use_html = $V['use_html'];
	else                       $use_html = '0';
}
if(!empty($V['use_secret'])) $use_secret = $V['use_secret'];
else                         $use_secret = '0';

if(!empty($V['email'])) $email = $V['email'];
else                    $email = NULL;

if(!empty($V['phone'])) $phone = $V['phone'];
else                    $phone = NULL;

if(!empty($V['content'])) $content = $V['content'];
else                      $content = NULL;

$file_cnt = 0;
if(!empty($_FILES['image_file']['name'])){
	$image_file = $_FILES['image_file']['name'];
	$file_cnt++;
}else{
	$image_file = NULL;
}

if(!empty($_FILES['file1']['name'])){
	$file1 = $_FILES['file1']['name'];
	$file_cnt++;
}else{
	$file1 = NULL;
}

if(!empty($_FILES['file2']['name'])){
	$file2 = $_FILES['file2']['name'];
	$file_cnt++;
}else{
	$file2 = NULL;
}

if($file_cnt > 0){
	if(!is_dir(BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/')){
		wp_mkdir_p(BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/');
	}
}

if(!empty($V['title'])) $title = $V['title'];
else                    $title = NULL;

$title = str_replace("[Re]", "", $title);
if(!empty($V['use_content_image'])) $use_content_image = $V['use_content_image'];
else                                $use_content_image = '0';

if(!empty($V['image_file_alt'])) $image_file_alt = $V['image_file_alt'];
else                             $image_file_alt = NULL;

$ip = $_SERVER['REMOTE_ADDR'];
/*---------- 입력항목 정의 end ----------*/

// 필터링 체크
if(!empty($boardInfo->use_filter) && $boardInfo->use_filter == "1"){
	$filtered = array();
	if(!empty($boardInfo->filter_list)){
		$fArr = explode(",", $boardInfo->filter_list);
		if(count($fArr) > 0){
			foreach($fArr as $filterWord){
				if(trim($filterWord) != ''){
					if(strpos($filterWord, $content))    $filtered[] = $filterWord;
					else if(strpos($filterWord, $title)) $filtered[] = $filterWord;
				}
			}
		}
	}
	if(count($filtered) > 0){
		echo "filter error";
		exit;
	}
}

if(!empty($V['upload_size'])) $upload_size = (int)$V['upload_size'];
else                          $upload_size = 2;

// 파일 체크
$img_files = array('jpg', 'gif', 'png');
if(!empty($_FILES['image_file']['tmp_name'])){
	$oFile = explode(".", $_FILES['image_file']['name']);
	if(!in_array(strtolower(end($oFile)), $img_files)){
		echo 'image_file type error';
		exit;
	}else{
		if(!empty($upload_size)){
			if($_FILES['image_file']['size'] > ($upload_size * 1048576)){
				echo 'image_file byte error';
				exit;
			}
		}
	}
}

$not_files = array('php', 'phpm', 'htm', 'html', 'shtm', 'ztx', 'dot', 'asp', 'cgi', 'pl', 'inc');
if(!empty($_FILES['file1']['tmp_name'])){
	$oFile1 = explode(".", $_FILES['file1']['name']);
	if(in_array(strtolower(end($oFile1)), $not_files)){
		echo 'file type error';
		exit;
	}else{
		if(!empty($upload_size)){
			if($_FILES['file1']['size'] > ($upload_size * 1048576)){
				echo 'file byte error';
				exit;
			}
		}
	}
}
if(!empty($_FILES['file2']['tmp_name'])){
	$oFile2 = explode(".", $_FILES['file2']['name']);
	if(in_array(strtolower(end($oFile2)), $not_files)){
		echo 'file type error';
		exit;
	}else{
		if(!empty($upload_size)){
			if($_FILES['file2']['size'] > ($upload_size * 1048576)){
				echo 'file byte error';
				exit;
			}
		}
	}
}

function check_content($text){
	$pattern = array("/<(\/?)(table|caption|thead|tbody|tfoot|tr|td|ol|ul|li|dl|dt|dd)([[:graph:] ]*)>([[:space:]]*)/i", "/<\/(table|ul|ol|dl)>/i");
	$replace = array("<\\1\\2\\3>", "</\\1>".PHP_EOL);
	return preg_replace($pattern, $replace, $text);
}
$content = check_content($content);
if(!empty($V['editor']) && $V['editor'] != "N") $content = nl2br($content);

/**************
 * 신규등록
 *************/
if((!empty($V['mode']) && $V['mode'] == 'write') && empty($V['no']) && empty($V['ref'])){
	if(!empty($V['writer'])) $writer = $V['writer'];
	else                     $writer = $current_user->user_login;

	if(!empty($curUserPermision) && $curUserPermision != 'all'){
		$pass = "'".esc_sql($pass)."'";
	}else{
		$pass = "password('".esc_sql($pass)."')";
	}

	$prepare = NULL;
	$prepare = $wpdb->prepare( "SELECT count(*) FROM {$tblBname} WHERE writer = %s AND write_date = %s AND title = %s", array( $writer, $write_date, $title ) );
	$wCnt    = $wpdb->get_var( $prepare );
	if($wCnt <= 0){
		$prepare = NULL;
		$prepare = $wpdb->prepare(
			"INSERT INTO {$tblBname}
				(`memnum`, `memlevel`, `writer`, `pass`, `email`, `phone`, `write_date`, `title`, `content`, `category`, `use_notice`, `use_html`, `use_secret`, `image_file`, `use_content_image`, `image_file_alt`, `file1`, `file2`, `ip`, `hit`, `listnum`)
			values
			( %d, %s, %s, {$pass}, %s, %s,
				%s, %s, %s, %s,      %s, %s,
				%s, %s, %s, %s,      %s, %s,
				%s, 0, 1)",
			array(
				$memnum,     $memlevel,                                 $writer,                             sanitize_email($email), sanitize_text_field($phone),
				$write_date, htmlentities($title, ENT_QUOTES, 'UTF-8'), $content,           $category,       $use_notice,            $use_html,
				$use_secret, $image_file,                               $use_content_image, $image_file_alt, $file1,                 $file2,
				$ip
			)
		);
		$wpdb->query($prepare);  // 중복 저장 방지
	}

	$ref = $wpdb->insert_id;


	$prepare = NULL;
	$prepare = $wpdb->prepare( "UPDATE {$tblBname} SET ref = %d , movecheck = %s WHERE no = %d ", array( $ref, $boardInfo->board_no.'_'.$ref.'_0', $ref ) );
	$wpdb->query( $prepare );

	$prepare = NULL;
	$prepare = $wpdb->prepare( "UPDATE {$tblBoard} SET list_count = `list_count`+1 WHERE boardname = %s", array( $V['bname'] ) );
	$wpdb->query( $prepare );

	if(!empty($_FILES['image_file']['tmp_name']) ){
		$upload_image_file = $boardInfo->board_no.'_'.$ref.'_image.'.end($oFile);
		$imgfilepath       = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_image_file;
		@move_uploaded_file($_FILES['image_file']['tmp_name'], $imgfilepath);
	}
	if(!empty($_FILES['file1']['tmp_name'])){
		$upload_file1 = $boardInfo->board_no.'_'.$ref.'_1.'.end($oFile1);
		$filepath1    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file1;
		@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
	}
	if(!empty($_FILES['file2']['tmp_name'])){
		$upload_file2 = $boardInfo->board_no.'_'.$ref.'_2.'.end($oFile2);
		$filepath2    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file2;
		@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
	}

	/* 신디케이션 연동 start */
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
	/* 신디케이션 연동 end */

	echo 'success|||write';
	exit;

/**************
 * 수정
 *************/
}else if((!empty($V['mode']) && $V['mode'] == 'write') && (!empty($V['no']) && $V['no'] > 0) && empty($V['ref'])){
	if(!empty($V['writer'])) $writer = $V['writer'];
	else                     $writer = NULL;

	$prepare = NULL;
	$prepare = $wpdb->prepare( "SELECT * FROM {$tblBname} WHERE no = %d ", array( $V['no'] ) );
	$rows    = $wpdb->get_row( $prepare );

	if(!empty($rows->write_date)){
		$tmp1      = explode(" ", $rows->write_date);
		$tmp2      = explode("-", $tmp1[0]);
		$tmp3      = explode(":", $tmp1[1]);
		$writetime = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
	}

	if($rows->re_level != "0") $pict = $writetime.'_re';
	else $pict = $V['no'].'_';

	$imgfilekind = substr($rows->image_file, -3);
	$filekind1   = substr($rows->file1, -3);
	$filekind2   = substr($rows->file2, -3);

	if($curUserPermision == 'subscriber' || $curUserPermision == 'contributor' || $curUserPermision == 'author' || $curUserPermision == 'editor' ){
		$write_pass = $pass;
	}else if($curUserPermision == 'all'){
		$prepare    = NULL;
		$prepare    = $wpdb->prepare( "SELECT password( %s )", array( $pass ) );
		$write_pass = $wpdb->get_var( $prepare );
	}

	if($curUserPermision != 'administrator' && $rows->pass != $write_pass){
		echo 'password error';
		exit;
	}

	$prepare  = NULL;
	$prepare .= "UPDATE {$tblBname} SET ";
	$prepare .= $wpdb->prepare(
		"title = %s, content = %s, email = %s, phone = %s, category = %s, use_notice = %s, use_html = %s, use_secret = %s",
		array( htmlentities($title, ENT_QUOTES, 'UTF-8'), $content, sanitize_email($email), sanitize_text_field($phone), $category, $use_notice, $use_html, $use_secret )
	);

	if(!empty($V['image_del']) && $V['image_del'] == 1){
		$imgdel_path = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$pict."image.".$imgfilekind;
		@unlink($imgdel_path);
		if(!empty($_FILES['image_file']['tmp_name'])){
			$upload_image_file = $boardInfo->board_no.'_'.$pict."image.".end($oFile);
			$imgfilepath       = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_image_file;
			@move_uploaded_file($_FILES['image_file']['tmp_name'], $imgfilepath);
			$prepare .= $wpdb->prepare( ", image_file= %s ", array( $image_file ) );
		}

	}else{
		if(!empty($_FILES['image_file']['tmp_name'])){
			if(!empty($imgfilekind)){
				$imgdel_path = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$pict."image.".$imgfilekind;
				@unlink($imgdel_path);
			}
			$upload_image_file = $boardInfo->board_no.'_'.$pict."image.".end($oFile);
			$imgfilepath       = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_image_file;
			@move_uploaded_file($_FILES['image_file']['tmp_name'], $imgfilepath);
			$prepare .= $wpdb->prepare( ", image_file= %s ", array( $image_file ) );
		}
	}
	if(!empty($use_content_image)) $prepare .= ", use_content_image='".$use_content_image."'";
	else                           $prepare .= ", use_content_image='0'";

	if(!empty($image_file_alt)) $prepare .= ", image_file_alt='".$image_file_alt."'";
	else                        $prepare .= ", image_file_alt=''";

	if(!empty($V['file_del1']) && $V['file_del1'] == "1"){
		$filedel_path1 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$pict."1.".$filekind1;
		@unlink($filedel_path1);
		if(!empty($_FILES['file1']['tmp_name'])){
			$upload_file1 = $boardInfo->board_no.'_'.$pict."1.".end($oFile1);
			$filepath1    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file1;
			@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
			$prepare .= $wpdb->prepare( ", file1= %s ", array( $file1 ) );
		}else{
			$prepare .= ", file1=''";
		}

	}else{
		if(!empty($_FILES['file1']['tmp_name'])){
			if(!empty($filekind1)){
				$filedel_path1 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$pict."1.".$filekind1;
				@unlink($filedel_path1);
			}
			$upload_file1 = $boardInfo->board_no.'_'.$pict."1.".end($oFile1);
			$filepath1    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file1;
			@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
			$prepare .= $wpdb->prepare( ", file1= %s ", array( $file1 ) );
		}
	}

	if(!empty($V['file_del2']) && $V['file_del2'] == "1"){
		$filedel_path2 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$pict."2.".$filekind2;
		@unlink($filedel_path2);
		if(!empty($_FILES['file2']['tmp_name'])){
			$upload_file2 = $boardInfo->board_no.'_'.$pict."2.".end($oFile2);
			$filepath2    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file2;
			@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
			$prepare .= $wpdb->prepare( ", file2= %s ", array( $file2 ) );
		}else{
			$prepare .= ", file2=''";
		}

	}else{
		if(!empty($_FILES['file2']['tmp_name'])){
			if(!empty($filekind2)){
				$filedel_path2 = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$boardInfo->board_no.'_'.$pict."2.".$filekind2;
				@unlink($filedel_path2);
			}
			$upload_file2 = $boardInfo->board_no.'_'.$pict."2.".end($oFile2);
			$filepath2    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file2;
			@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
			$prepare .= $wpdb->prepare( ", file2= %s ", array( $file2 ) );
		}
	}

	if(!empty($writer)) $prepare .= $wpdb->prepare( ", writer= %s ", array( $writer ) );
	if($curUserPermision == 'administrator'){
		if(!empty($V['pass'])) $prepare .= $wpdb->prepare( ", pass=password( %s ) ", array( trim($V['pass']) ) );
	}

	$prepare .= $wpdb->prepare( " WHERE no= %d ", array( $V['no'] ) );
	$wpdb->query($prepare);

	/* 신디케이션 연동 start */
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
	/* 신디케이션 연동 end */

	echo "success|||modify";
	exit;

/**************
 * 답글
 *************/
}else if((!empty($V['mode']) && $V['mode'] == 'write') && (!empty($V['no']) && $V['no'] > 0) && (!empty($V['ref']) && $V['ref'] > 0)){
	if(!empty($V['writer'])) $writer = $V['writer'];
	else                     $writer = $current_user->user_login;

	if(empty($V['re_step']))  $V['re_step']  = 0;
	if(empty($V['re_level'])) $V['re_level'] = 0;
	$prepare = NULL;
	$prepare = $wpdb->prepare( "UPDATE {$tblBname} SET re_step=re_step+1 WHERE ref = %d AND re_step > %d" , array( $V['ref'], $V['re_step'] ) );
	$wpdb->query( $prepare );

	if(!empty($use_secret) && $use_secret == "1"){
		$prepare = NULL;
		$prepare = $wpdb->prepare( "SELECT pass FROM {$tblBname} WHERE no=%d", array( $V['no'] ) );
		$pass    = $wpdb->get_var( $prepare );
		$pass    = "'".esc_sql($pass)."'";
	}else{
		if(!empty($curUserPermision) && $curUserPermision != 'all'){
			$pass = "'".esc_sql($pass)."'";
		}else{
			$pass = "password('".esc_sql($pass)."')";
		}
	}

	$re_step  = $V['re_step'] + 1;
	$re_level = $V['re_level'] + 1;

	$prepare = NULL;
	$prepare = $wpdb->prepare(
		"INSERT INTO {$tblBname}
			( `memnum`, `memlevel`, `writer`, `pass`, `email`, `phone`, `write_date`, `title`, `content`, `category`, `use_notice`, `use_html`, `use_secret`, `image_file`, `use_content_image`, `image_file_alt`, `file1`, `file2`, `ip`, `hit`, `ref`, `re_step`, `re_level`, `movecheck` )
		values
			( %d, %s, %s, {$pass}, %s, %s,
				%s, %s, %s, %s,      %s, %s,
				%s, %s, %s, %s,      %s, %s,
				%s, 0,  %d, %d,      %d, %s)",
		array(
			$memnum,     $memlevel,                                 $writer,                             sanitize_email($email), sanitize_text_field($phone),
			$write_date, htmlentities($title, ENT_QUOTES, 'UTF-8'), $content,           $category,       $use_notice,            $use_html,
			$use_secret, $image_file,                               $use_content_image, $image_file_alt, $file1,                 $file2,
			$ip,				    					                              $V['ref'],					$re_step, 			 $re_level,		           $boardInfo->board_no.'_'.$V['ref'].'_0'
		)
	);

	$wpdb->query($prepare);
	$ref     = $wpdb->insert_id;
	$prepare = NULL;
	$prepare = $wpdb->prepare( "UPDATE {$tblBoard} SET `list_count`=`list_count`+1 WHERE boardname = %s", array( $V['bname'] ) );
	$wpdb->query( $prepare );

	if(!empty($_FILES['image_file']['tmp_name'])){
		$upload_image_file = $boardInfo->board_no.'_'.$unique.'_reimage.'.end($oFile);
		$imgfilepath       = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_image_file;
		@move_uploaded_file($_FILES['image_file']['tmp_name'], $imgfilepath);
	}
	if(!empty($_FILES['file1']['tmp_name'])){
		$upload_file1 = $boardInfo->board_no.'_'.$unique.'_re1.'.end($oFile1);
		$filepath1    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file1;
		@move_uploaded_file($_FILES['file1']['tmp_name'], $filepath1);
	}
	if(!empty($_FILES['file2']['tmp_name'])){
		$upload_file2 = $boardInfo->board_no.'_'.$unique.'_re2.'.end($oFile2);
		$filepath2    = BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/'.$upload_file2;
		@move_uploaded_file($_FILES['file2']['tmp_name'], $filepath2);
	}

	/* 신디케이션 연동 start */
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
	/* 신디케이션 연동 end */

	echo 'success|||reply';
	exit;

}else{
	echo 'nonData';
	exit;
}
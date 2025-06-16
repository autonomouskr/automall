<?php
/**
 * Plugin Name: BBS e-Board
 * Plugin URI: http://www.bbsetheme.com
 * Description: BBS e-Board는 간단하게 게시판을 생성/관리 할 수 있습니다. 화면구성, 관리자 툴이 국내(한국)에 익숙한 형태로 제작되었습니다. 주요특징으로는 기본형, 웹진형, 갤러리형태의 스킨제공, 화면에 보여지는 폭과 정렬 조절, 댓글, 워드프레스 회원과 연동  접근/쓰기 권한 설정, 보안서버(SSL) 설정, 자동글등록방지 설정, 필터링 설정, SNS 공유입니다.
 * Version: 1.5.5
 * Author: BBS e-Theme
 * Author URI: http://www.bbsetheme.com
 * License: GNU General Public License, v2
 * License URI: http://www.gnu.org/licenses/gpl.html
 *
 * 본 플러그인은 워드프레스와 동일한 GPL 라이센스의 플러그인입니다. 임의대로 수정,삭제 후 이용하셔도 됩니다.
 * 단, 재배포 시 GPL 라이센스로 재배포 되어야 하며, 원 제작자의 표기를 해주시기 바랍니다.
 * 'BBS e-Board' WordPress Plugin, Copyright 2014 BBS e-Theme(http://www.bbsetheme.com)
 * 'BBS e-Board' is distributed under the terms of the GNU GPL
 */
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

define("BBSE_BOARD_VER",             'v1.5.5');
define("BBSE_BOARD_DB_VER",          '2016082900');
define("BBSE_BOARD_SITE_URL",        home_url());
define("BBSE_BOARD_CONTENT_URL",     content_url());
define("BBSE_BOARD_PLUGIN_ABS_PATH", plugin_dir_path(__FILE__));
define("BBSE_BOARD_PLUGIN_WEB_URL",  plugins_url().'/BBSe_Board/');
define("BBSE_BOARD_SETUP_URL",       admin_url('/admin.php?page=bbse_board_config'));
define("BBSE_BOARD_PRIVATE_URL",     admin_url('/admin.php?page=bbse_board_private'));
//
define("BBSE_BOARD_SSL_CONFIG_URL",  admin_url('/admin.php?page=bbse_board_ssl'));
define("BBSE_BOARD_BACKUP_URL",      admin_url('/admin.php?page=bbse_board_backup'));
//
$upload_dir = wp_upload_dir();
define("BBSE_BOARD_UPLOAD_BASE_PATH", $upload_dir['basedir'].'/');
define("BBSE_BOARD_UPLOAD_BASE_URL",  $upload_dir['baseurl'].'/');

require_once(plugin_dir_path(__FILE__).'class/function.php');					  // 기본 페이지 function
require_once(plugin_dir_path(__FILE__).'class/board.class.php');				// Board Class
//
require_once(plugin_dir_path(__FILE__).'class/encryption.class.php');		// $_GET parameter 암호화 Class
require_once(plugin_dir_path(__FILE__).'class/style.class.php');				// SKIN style Class

if(is_admin()){
	add_action('init', 'set_bbse_board_auto_update');

	if(!function_exists('set_bbse_board_auto_update')){
		function set_bbse_board_auto_update(){
			require_once(plugin_dir_path(__FILE__).'admin/bbse_plugin_update.php');
			$plugin_current_path =  __FILE__ ;
			$plugin_slug         = plugin_basename(__FILE__);

			new bbse_board_auto_update($plugin_current_path, $plugin_slug);
		}
	}
}

// session start
function bbse_board_session_start(){
	global $currentSessionID;
	if(!session_id()){
		session_start();
	}
	$currentSessionID = session_id();
}
add_action('init', 'bbse_board_session_start', 1);

// CSS 적용
if(!is_admin()){
	//
	$style_cls = new BBSeBoardStyle;
}

// 사용자 javascript  파일 호출
function bbse_board_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-form');
	wp_enqueue_script('bbse-board-js', BBSE_BOARD_PLUGIN_WEB_URL.'js/common.js');
	if( wp_is_mobile() && get_option('bbse_board_kakao_app_key') !='' ){
		wp_enqueue_script('kakao-link', BBSE_BOARD_PLUGIN_WEB_URL.'js/kakao.link.js');
		wp_enqueue_script('kakao-min', 'https://developers.kakao.com/sdk/js/kakao.min.js');
	}
}
add_action('wp_enqueue_scripts', 'bbse_board_scripts');

// 사용자 css  파일 호출
function bbse_board_styles(){
}
add_action('wp_enqueue_scripts', 'bbse_board_styles');

// 관리자 javascript 파일 호출
function bbse_board_admin_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-form');
	wp_enqueue_script('bbse-board-js', BBSE_BOARD_PLUGIN_WEB_URL.'js/common.js');
}
add_action('admin_enqueue_scripts', 'bbse_board_admin_scripts');

// 관리자 메뉴설정
function bbse_board_menu(){
	add_menu_page('BBS e-Board', 'BBS e-Board', 'administrator', 'bbse_board', 'bbse_board_list');
	add_submenu_page('bbse_board', '게시판 목록', '게시판 목록', 'administrator', 'bbse_board', 'bbse_board_list');
	add_submenu_page('bbse_board', '게시판 추가', '게시판 추가', 'administrator', 'bbse_board_config', 'bbse_board_config');
	add_submenu_page('bbse_board', '개인정보수집안내', '개인정보수집안내', 'administrator', 'bbse_board_private', 'bbse_board_private');
	//
	add_submenu_page('bbse_board', 'SSL(보안서버)설정', 'SSL(보안서버)설정', 'administrator', 'bbse_board_ssl', 'bbse_board_ssl');
	add_submenu_page('bbse_board', '데이터 백업/복구', '데이터 백업/복구', 'administrator', 'bbse_board_backup', 'bbse_board_backup');
	//
}
add_action('admin_menu', 'bbse_board_menu');

// 도움말 추가
function bbse_board_screen_help($contextual_help, $screen_id, $screen){
	global $page;
	if(!method_exists($screen, 'add_help_tab')) return $contextual_help;
	if(!empty($screen->id)){
		$thisSlug = explode("bbse_", $screen->id);
		switch(end($thisSlug)){
			case "board":
				$screen->add_help_tab(array(
					'id' => 'bbse_board_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Board 게시판 목록 페이지입니다.'
				));
				break;
			case "board_config":
				$screen->add_help_tab(array(
					'id' => 'bbse_board_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Board 게시판 추가 페이지입니다.'
				));
				break;
			case "board_private":
				$screen->add_help_tab(array(
					'id' => 'bbse_board_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Board 개인정보수집안내 페이지입니다.'
				));
				break;
			//
			//
			//
			//
			//
			//
			//
			case "board_ssl":
				$screen->add_help_tab(array(
					'id' => 'bbse_board_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Board SSL(보안서버)설정 페이지입니다.'
				));
				break;
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
		}
	}
	return $contextual_help;
}
add_filter('contextual_help', 'bbse_board_screen_help', 10, 3);

// bbse_board table create/alter
function dbdelta_bbse_board(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Version 1.0.1
	 * add field : use_hms(HMS 공유), hidden_by(제작사 노출여부)
	*/
	/**
	 * Version 1.0.8
	 * add field : l_reply(답글쓰기 권한), use_reply(답글 사용여부), use_kakaotalk(카카오톡 공유 사용여부), use_kakaostory(카카오스토리 공유 사용여부)
	*/
	/**
	 * Version 1.1.8
	 * add field : l_list(목록보기 권한), no_permission_url(권한없음 URL)
  */
	/**
	 * Version 1.4.0
	 * add field : thumb_ratio(썸네일 비율), ext_var1,ext_var2,ext_var3,ext_var4,ext_var5,ext_var6,ext_var7,ext_var8,ext_var9,ext_var10 -> 추가필드
	*/
	$query = "CREATE TABLE `".$wpdb->prefix."bbse_board` (
		`board_no` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`boardname` varchar(20) NOT NULL,
		`skinname` varchar(20) DEFAULT NULL,
		`formtype` char(1) NOT NULL DEFAULT '1',
		`editor` char(1) NOT NULL DEFAULT 'N',
		`table_width` varchar(5) NOT NULL DEFAULT '100',
		`table_align` char(1) NOT NULL DEFAULT 'L',
		`page_size` varchar(5) NOT NULL DEFAULT '20',
		`category_list` text,
		`use_filter` char(1) DEFAULT '0',
		`filter_list` text,
		`use_pds` char(1) DEFAULT '0',
		`upload_size` varchar(10) DEFAULT NULL,
		`use_comment` char(1) DEFAULT '0',
		`use_reply` char(1) DEFAULT '0',
		`use_secret` char(1) DEFAULT '0',
		`use_notice` char(1) DEFAULT '0',
		`using_subAutoWtype` enum('GD','TXT','NO') DEFAULT 'TXT',
		`l_list` varchar(20) NOT NULL DEFAULT 'all',
		`l_read` varchar(20) NOT NULL DEFAULT 'all',
		`l_comment` varchar(20) NOT NULL DEFAULT 'all',
		`l_write` varchar(20) NOT NULL DEFAULT 'all',
		`l_reply` varchar(20) NOT NULL DEFAULT 'all',
		`no_permission_url` varchar(100) DEFAULT NULL,
		`point_write` int(5) DEFAULT '10',
		`use_seo` char(1) NOT NULL DEFAULT '0',
		`seo_title` varchar(255) DEFAULT NULL,
		`seo_description` varchar(255) DEFAULT NULL,
		`seo_keywords` varchar(255) DEFAULT NULL,
		`use_syndi` char(1) NOT NULL DEFAULT '0',
		`hidden_hit` char(1) DEFAULT '0',
		`hidden_writer` char(1) DEFAULT '0',
		`use_phone` char(1) DEFAULT '0',
		`use_email` char(1) DEFAULT '0',
		`use_facebook` char(1) DEFAULT '0',
		`use_twitter` char(1) DEFAULT '0',
		`use_hms` char(1) DEFAULT '0',
		`use_kakaotalk` char(1) DEFAULT '0',
		`use_kakaostory` char(1) DEFAULT '0',
		`user_list` text,
		`list_count` int(11) DEFAULT NULL,
		`hidden_by` char(1) DEFAULT '0',
		`reg_date` int(11) DEFAULT NULL,
		`thumb_ratio` varchar(5) DEFAULT NULL,
		`ext_var1` varchar(255) DEFAULT NULL,
		`ext_var2` varchar(255) DEFAULT NULL,
		`ext_var3` varchar(255) DEFAULT NULL,
		`ext_var4` varchar(255) DEFAULT NULL,
		`ext_var5` varchar(255) DEFAULT NULL,
		`ext_var6` text,
		`ext_var7` text,
		`ext_var8` text,
		`ext_var9` text,
		`ext_var10` text,
		PRIMARY KEY  (`board_no`)
	) {$charset_collate}";
	dbDelta( $query );

	return false;
}

// bbse_board_config table create/alter
function dbdelta_bbse_board_config(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Version 1.1.6
	 * add field : use_private(개인정보수집안내 사용여부)
	 */
	/**
	 * Version 1.4.0
	 * add field : ver(db버전)
  */
	/**
	 * Version 1.5.0
	 * remove field : ver(db버전) // nomore use
  */
	$query = "CREATE TABLE `".$wpdb->prefix."bbse_board_config` (
		`cnf_contents` longtext,
		`use_private` char(1) NOT NULL DEFAULT 'Y',
		`use_ssl` char(1) NOT NULL DEFAULT '0',
		`ssl_domain` varchar(100) DEFAULT NULL,
		`ssl_port` varchar(10) DEFAULT NULL
	) {$charset_collate}";
	dbDelta( $query );

	$configchk = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."bbse_board_config`");
	if($configchk > 1) $wpdb->query('DELETE FROM `'.$wpdb->prefix.'bbse_board_config` LIMIT '.($configchk-1));

	return false;
}

// bbse_board_syndi_delete_content_log table create/alter
function dbdelta_bbse_board_syndi_delete_content_log(){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$query = "CREATE TABLE `".$wpdb->prefix."bbse_board_syndi_delete_content_log` (
		`content_id` bigint(11) unsigned NOT NULL,
		`bbs_id` varchar(50) NOT NULL,
		`title` text NOT NULL,
		`link_alternative` varchar(250) NOT NULL,
		`delete_date` varchar(14) NOT NULL,
		PRIMARY KEY  (`content_id`,`bbs_id`)
		) {$charset_collate}";
	dbDelta( $query );

	return false;
}

// bbse_x_board table create/alter
function dbdelta_bbse_x_board($name){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Version 1.0.1
	 * modify field : title(varchar -> text)
	*/
	/**
	 * Version 1.0.3
	 * modify field : content(text -> longtext)
	*/
	/**
	 * Version 1.4.2
	 * modify field : category(varchar(20) -> varchar(255)
	*/
	$query = "CREATE TABLE `".$wpdb->prefix."bbse_".$name."_board` (
		`no` int unsigned not null auto_increment,
		`memnum` int(11) not null default '0',
		`memlevel` varchar(20) default null,
		`writer` varchar(20) not null,
		`pass` varchar(50) not null,
		`email` varchar(50) default null,
		`phone` varchar(50) default null,
		`write_date` datetime default null,
		`title` text not null,
		`content` longtext not null,
		`hit` int default '0',
		`category` varchar(255) default null,
		`use_notice` char(1) default '0',
		`use_html` char(1) not null default '0',
		`use_secret` char(1) default '0',
		`image_file` varchar(100) default null,
		`image_file_alt` varchar(100) default null,
		`use_content_image` char(1) default '0',
		`file1` varchar(100) default null,
		`file2` varchar(100) default null,
		`ref` int default null,
		`re_step` int not null default '0',
		`re_level` int not null default '0',
		`listnum` char(1) not null default '0',
		`movecheck` varchar(10) default null,
		`ip` varchar(15) default null,
		PRIMARY KEY  (`no`)
		) {$charset_collate}";
	dbDelta( $query );

	/* bbse_$boardname_comment 보드 코멘트 테이블 생성 */
	$query = "CREATE TABLE `".$wpdb->prefix."bbse_".$name."_comment` (
		`no` int unsigned not null auto_increment,
		`parent` int not null,
		`comm_parent` int not null default '0',
		`memnum` int(11) not null default '0',
		`writer` varchar(20) default null,
		`pass` varchar(50) default null,
		`content` text default null,
		`ip` varchar(15) default null,
		`depth` int not null default '0',
		`move_no` int not null default '0',
		`write_date` datetime default null,
		PRIMARY KEY  (`no`)
		) {$charset_collate}";
	dbDelta( $query );
}

function update116(){
	global $wpdb;
	$old_tbls1 = $wpdb->get_row("SHOW TABLES WHERE Tables_in_".DB_NAME."='bbse_board'", ARRAY_N);
	if(!empty($old_tbls1[0])) $wpdb->query("RENAME TABLE `bbse_board` TO `".$wpdb->prefix."bbse_board`");

	$old_tbls2 = $wpdb->get_row("SHOW TABLES WHERE Tables_in_".DB_NAME."='bbse_board_config'", ARRAY_N);
	if(!empty($old_tbls2[0])) $wpdb->query("RENAME TABLE `bbse_board_config` TO `".$wpdb->prefix."bbse_board_config`");

	$old_tbls3 = $wpdb->get_row("SHOW TABLES WHERE Tables_in_".DB_NAME."='bbse_board_syndi_delete_content_log'", ARRAY_N);
	if(!empty($old_tbls3[0])) $wpdb->query("RENAME TABLE `bbse_board_syndi_delete_content_log` TO `".$wpdb->prefix."bbse_board_syndi_delete_content_log`");

	$old_board = $wpdb->get_results("SHOW TABLES WHERE Tables_in_".DB_NAME." LIKE 'bbse_%_board' AND Tables_in_".DB_NAME."<>'bbse_board'", ARRAY_N);
	foreach($old_board as $tbls) $wpdb->query("RENAME TABLE `".$tbls[0]."` TO `".$wpdb->prefix.$tbls[0]."`");

	$old_comment = $wpdb->get_results("SHOW TABLES WHERE Tables_in_".DB_NAME." LIKE 'bbse_%_comment'", ARRAY_N);
	foreach($old_comment as $tbls) $wpdb->query("RENAME TABLE `".$tbls[0]."` TO `".$wpdb->prefix.$tbls[0]."`");
}

// 플러그인 활성화시
function bbse_board_create_tables(){
	global $wpdb;

	$installed_version = get_option('bbseboard_db_version');
	$install_check_a   = $wpdb->get_row("SHOW TABLES WHERE Tables_in_".DB_NAME."='".$wpdb->prefix."bbse_board'", ARRAY_N);
	$install_check_b   = $wpdb->get_row("SHOW TABLES WHERE Tables_in_".DB_NAME."='".$wpdb->prefix."bbse_board_config'", ARRAY_N);
	$install_check_c   = $wpdb->get_row("SHOW TABLES WHERE Tables_in_".DB_NAME."='".$wpdb->prefix."board_syndi_delete_content_log'", ARRAY_N);

	if (!$installed_version || ($installed_version && $installed_version < BBSE_BOARD_DB_VER) ||
		  empty($install_check_a[0]) || empty($install_check_b[0]) || empty($install_check_c[0])){
		/**
		 * Version 1.1.6
		 * DB 테이블명 변경(테이블 생성 전에 변경해야 하므로 create 구문보다 상단에 위치)
		*/
		update116();

		//bbse_board 테이블 작성
		dbdelta_bbse_board();

		//bbse_board_config 테이블 작성
		dbdelta_bbse_board_config();

		//bbse_board_syndi_delete_content_log 테이블 작성
		dbdelta_bbse_board_syndi_delete_content_log();

		$boards = $wpdb->get_results("SELECT boardname FROM ".$wpdb->prefix."bbse_board");
		if ($boards){
			foreach($boards as $board){
				dbdelta_bbse_x_board($board->boardname);
			}
		}

		//
		//
		//
		//
		//
		//

		if(!is_dir(BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/')){
			wp_mkdir_p(BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/');
		}

		if (!$installed_version){
			add_option( 'bbseboard_db_version', BBSE_BOARD_DB_VER, '', 'no' );
		} else if ($installed_version && $installed_version < BBSE_BOARD_DB_VER){
			update_option( 'bbseboard_db_version', BBSE_BOARD_DB_VER, 'no' );
		}
	}
}
register_activation_hook(__FILE__, 'bbse_board_create_tables');

// 플러그인 비활성화시
function bbse_board_deactiv_proc(){
	// 필요시 내용 추가하여 사용
}
register_deactivation_hook(__FILE__, 'bbse_board_deactiv_proc');

// 플러그인 삭제시
function uninstall_bbse_board_plugin(){
	global $wpdb;

	$wpdb->query("DROP TABLE `".$wpdb->prefix."bbse_board`");
	$wpdb->query("DROP TABLE `".$wpdb->prefix."bbse_board_config`");
	$wpdb->query("DROP TABLE `".$wpdb->prefix."bbse_board_syndi_delete_content_log`");

	$tbls_qry = $wpdb->get_results("SHOW TABLES WHERE Tables_in_".DB_NAME." LIKE '".$wpdb->prefix."bbse_%_board' AND Tables_in_".DB_NAME."<>'".$wpdb->prefix."bbse_board'", ARRAY_N);
	foreach($tbls_qry as $tbls){
		$wpdb->query("DROP TABLE `".$tbls[0]."`");
	}

	$comm_tbls_qry = $wpdb->get_results("SHOW TABLES WHERE Tables_in_".DB_NAME." LIKE '".$wpdb->prefix."bbse_%_comment'", ARRAY_N);
	foreach($comm_tbls_qry as $comm_tbls){
		$wpdb->query("DROP TABLE `".$comm_tbls[0]."`");
	}

	if(is_dir(BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/')){
		rmdir_rf(BBSE_BOARD_UPLOAD_BASE_PATH.'bbse-board/');
	}

	delete_option( 'bbseboard_db_version');
}
register_uninstall_hook(__FILE__, 'uninstall_bbse_board_plugin');


// 게시판 shortcode 등록
function bbse_board_make_list($args){
	global $wpdb;

	if(!empty($_GET['nType'])) $_VAR = bbse_board_parameter_decryption($_GET['nType']);
	if(empty($args['bname'])){
		return '[환경설정 오류!] ShortCode의 게시판명은 필수 입력사항 입니다.';
	}
	if(!empty($_VAR['bname']) && $args['bname'] != $_VAR['bname']){
		return '[환경설정 오류!] 올바르지 않은 게시판 접근입니다.';
	}

	$boardInfo = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE BINARY(`boardname`)='".$args['bname']."'");

	if(!empty($boardInfo->skinname)){
		$board_cls = new BBSeBoard;
		ob_start();

		// 글목록
		if(empty($_VAR['mode']) || $_VAR['mode'] == 'list'){
			$board_cls->get_list($args['bname']);

		// 글쓰기 / 수정
		}else if(!empty($_VAR['mode']) && $_VAR['mode'] == 'write'){
			$board_cls->get_write($args['bname']);

		// 글보기
		}else if((!empty($_VAR['mode']) && $_VAR['mode'] == 'view') && (!empty($_VAR['no']) && $_VAR['no'] > 0)){
			$board_cls->get_view($args['bname']);
		}

		$ret_contents = ob_get_contents();
		ob_end_clean();
		return $ret_contents;

	}else{
		return "[환경설정 오류!] ".$args['bname']." 생성되지 않은 게시판입니다.";
	}
}

// Function to hook to "the_posts"(just edit the two variables)
function metashortcode_mycode($posts){
	$shortcode         = 'bbse_board';
	$callback_function = 'metashortcode_setmeta';

	return metashortcode_shortcode_to_wphead($posts, $shortcode, $callback_function);
}

// To execute when shortcode is found
function metashortcode_setmeta(){
	$board_cls = new BBSeBoard;
	$board_cls->sns_meta_add();
}

// look for shortcode in the content and apply expected behaviour(don't edit!)
function metashortcode_shortcode_to_wphead($posts, $shortcode, $callback_function){
	if(empty($posts)) return $posts;

	$found = false;
	foreach($posts as $post){
		if(stripos($post->post_content, '['.$shortcode) !== false){
			add_shortcode($shortcode, 'bbse_board_make_list');
			$found = true;
			break;
		}
	}

	if($found) add_action('wp_head', $callback_function);

	return $posts;
}

// Instead of creating a shortcode, hook to the_posts
add_action('the_posts', 'metashortcode_mycode');

function passNumer($var, $msg){
	//음수, 공백, 소숫점, 문자열0 허용안함
	if ( !is_int( (int)$var ) && !ctype_digit($var) ) {
		echo $msg;
		exit;
	} else {
		return true;
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
//
//

$member_role_name = array(
'-1'=> '비회원',
'0'=> '구독자',
'1'=> '기여자',
'2'=> '글쓴이',
'3'=> '편집자',
'4'=> '편집자',
'5'=> '편집자',
'6'=> '편집자',
'7'=> '편집자',
'8'=> '관리자',
'9'=> '관리자',
'10'=> '관리자',
'999'=> '지정사용자',
);

$member_order = array(
	'all'           => -1,
	'subscriber'    => 0,
	'contributor'   => 1,
	'author'        => 2,
	'editor'        => 3, //3,4,5,6,7
	'administrator' => 8, //8,9,10
	'private'       => 999
);

function bbse_role2level($role){
	global $member_order;
	return $member_order[$role];
}

function bbse_get_wp_role(){
	$wpuserinfo = wp_get_current_user();
	foreach($wpuserinfo->roles as $role) $cleanRoles[] = $role;
	return ($wpuserinfo->caps[$cleanRoles[0]] == 1 )?$cleanRoles[0]:'all';
}

function bbse_check_user_level(){
	return bbse_role2level(bbse_get_wp_role());
}

function bbse_private_user_check($boardObj, $auth_type){
	$current_user = wp_get_current_user();
	$private_auth = false;
	if (!empty($boardObj->user_list)){
		$private_user_arr = explode(",", str_replace(" ", "", $boardObj->user_list));
		if ( $boardObj->{$auth_type} == 'private' && in_array($current_user->user_login, $private_user_arr) ){
			$private_auth = true;
		}
	}
	return $private_auth;
}

$noticeBoxComment = '<div class="noticeBox">* 업데이트 후 플러그인이 비정상적으로 동작하는 경우 <b>플러그인을 비활성화 후 다시 활성화</b> 해보세요.</div>';
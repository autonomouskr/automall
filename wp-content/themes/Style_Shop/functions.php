<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;

/*** for License Key (Start)***/
 // [Get] the Theme info
if(!function_exists('bbse_theme_get_custom_theme_info')){
	 function bbse_theme_get_custom_theme_info($rType){ 
		 $rtnData="";
		$theme_data=wp_get_theme();
		if(is_child_theme()){
			if($rType=='Name') $rtnData=$theme_data->parent()->Name;
			elseif($rType=='Version') $rtnData=$theme_data->parent()->Version;
		}
		else $rtnData=$theme_data->get($rType);

		return $rtnData;
	}
}

define("BBSE_THEME_CURRENT_PATH", __FILE__);
define("BBSE_THEME_WEB_URL", get_template_directory_uri());

define("BBSE_THEME_NAME", bbse_theme_get_custom_theme_info('Name'));
define("BBSE_THEME_VERSION", bbse_theme_get_custom_theme_info('Version'));
define("BBSE_THEME_ABS_PATH", get_template_directory());
define("BBSE_BLOG_NAME", get_bloginfo('name'));
define("BBSE_BLOG_HOME", get_bloginfo('url'));
define("BBSE_BLOG_LANGUAGE", get_bloginfo('language'));
define("BBSE_BLOG_ADMIN_EMAIL", get_bloginfo('admin_email'));
define("BBSE_BLOG_SERVER_IP", ($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['LOCAL_ADDR']);

// [Admin] add a notification
add_action( 'admin_notices', 'bbse_theme_display_notice'); 
if(!function_exists('bbse_theme_display_notice')){
	function bbse_theme_display_notice($vars) {
		global $hook_suffix;
		if($hook_suffix=='themes.php' && !$_GET['action']){ // add action : notice (plugins.php page only)
			if(!bbse_theme_get_license_key()){ // Empty a licence key
				bbse_theme_notice_view('notice',array( 'type' => 'license'));
			}
		}
	}
}

// [Admin] print the notification
if(!function_exists('bbse_theme_notice_view')){
	function bbse_theme_notice_view( $name, array $args = array() ) {
		$args = apply_filters( 'BBSe_Theme_view_arguments', $args, $name );
		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}
		load_theme_textdomain(BBSE_THEME_NAME);
		require_once BBSE_THEME_ABS_PATH . '/admin/common/'. $name . '.php';
	}
}

// [Check] the license key
if(!function_exists('bbse_theme_get_license_key')){
	function bbse_theme_get_license_key() {
		global $theme_shortname,$varLcStatus,$varLcKey;
		$lecenseKey=(get_option($varLcKey))?get_option($varLcKey):"";
		if(!$lecenseKey) update_option($varLcStatus,'');

		return $lecenseKey;
	}
}

// [Certification] the license key
if(!function_exists('bbse_theme_license_check')){
	function bbse_theme_license_check(){
		global $theme_shortname,$varLcStatus;
		$tmpKey=bbse_theme_get_license_key();
		if($tmpKey){
			$license_status=get_option($varLcStatus);
			$pgInfo=bbse_commerce_pg_check();

			$keyResult=check_theme_license(BBSE_THEME_NAME,$tmpKey,BBSE_BLOG_HOME,BBSE_BLOG_SERVER_IP,BBSE_BLOG_NAME,$license_status,$pgInfo['payment_agent'],$pgInfo['payment_id']); // 라이센스키, 홈 URL, IP, 블로그명,이전 상태값
			update_option($varLcStatus,$keyResult);
			$rtn=($keyResult==='true')? true: false;
		}
		else $rtn=false;

		return $rtn;
	}
}

// [PG] check
if(!function_exists('bbse_commerce_pg_check')){
	function bbse_commerce_pg_check(){
		global $wpdb,$theme_shortname;
		$rtnPg=array();

		// config table check
		if($wpdb->get_var("SHOW TABLES LIKE 'bbse_commerce_config'") == 'bbse_commerce_config') {
			$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='payment'");

			if($cnt>'0'){
				$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='payment'");
				$data=unserialize($confData->config_data);

				$rtnPg['payment_agent']=$data['payment_agent'];
				$rtnPg['payment_id']=$data['payment_id'];
			}
		}
		return $rtnPg;
	}
}

// [Page] BBS e-Theme License
if(!function_exists('bbse_theme_License_manage')){
	function bbse_theme_License_manage($chkFlag=""){
		global $wpdb,$theme_shortname,$varLcStatus;
		if(!$chkFlag) $licenseFlag=bbse_theme_license_check();
		else $licenseFlag=get_option($varLcStatus);

		wp_enqueue_script('thickbox'); wp_enqueue_style('thickbox');
		require_once(BBSE_THEME_ABS_PATH."/admin/theme_option_license.php");
	}
}
/*** for License Key (End)***/


require_once('admin/class/bbse_sitemap.class.php');
require_once('admin/class/bbse_naver_syndication.php'); //네이버 신디케이션 연동
require_once('admin/class/functions-external-blog-link.php'); // 네비버 블로그 연동
require_once('admin/class/bbse_tistory_blog.php'); //티스토리 연동

// 옵션 가져오기
if (!function_exists('bbse_get_option')) {
	function bbse_get_option($option){
	  GLOBAL $theme_shortname;
	  $data = get_option($theme_shortname.'_'.$option);
	  return $data;
	}
}

// 테마를 셋업합니다.
if ( ! function_exists( 'bbse_theme_setup' ) )
{
  function bbse_theme_setup()
  {
    global $currentSessionID, $theme_shortname,$SITEMAPS;
    if(!session_id()){
      session_start();
    }
    $currentSessionID = session_id();

	// HTML5를 지원합니다.
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form' ) );

    //사이트맵
    if (bbse_get_option('google_use_webmaster') == 'U' || bbse_get_option('bing_use_webmaster') == 'U' || bbse_get_option('baidu_use_webmaster') == 'U')
    {
      $SITEMAPS = new BBSE_SITEMAP();
    }

    //네이버 신디케이션 활성
    if (get_option($theme_shortname.'_naver_use_webmaster') == 'U' && get_option($theme_shortname.'_naver_syndiToken')) {
      $syndication = new BBSE_NAVERSYNDICATION();
    }
    //티스토리 블로그 활성
    if (get_option($theme_shortname.'_tistory_use') == 'U' &&
        get_option($theme_shortname.'_tistory_url')        &&
        get_option($theme_shortname.'_tistory_callback')   &&
        get_option($theme_shortname.'_tistory_clientid')   &&
        get_option($theme_shortname.'_tistory_skey')       &&
        get_option($theme_shortname.'_tistory_accesstoken') ){
      $naverblog = new BBSE_TISTORYBLOG();
    }
	
  }
}
add_action( 'after_setup_theme', 'bbse_theme_setup' );

function webmasterOwnerCheck(){
  GLOBAL $theme_shortname;

  if (get_option($theme_shortname.'_naver_use_webmaster') == 'U' && get_option($theme_shortname.'_naver_owntag'))
    echo '<meta name="naver-site-verification" content="'.get_option($theme_shortname.'_naver_owntag').'" />'.PHP_EOL;

  if (get_option($theme_shortname.'_google_use_webmaster') == 'U' && get_option($theme_shortname.'_google_owntag'))
    echo '<meta name="google-site-verification" content="'.get_option($theme_shortname.'_google_owntag').'" />'.PHP_EOL;

  if (get_option($theme_shortname.'_bing_use_webmaster') == 'U' &&  get_option($theme_shortname.'_bing_owntag'))
    echo '<meta name="msvalidate.01" content="'.get_option($theme_shortname.'_bing_owntag').'" />'.PHP_EOL;

  if (get_option($theme_shortname.'_baidu_use_webmaster') == 'U' &&  get_option($theme_shortname.'_baidu_use_webmaster'))
    echo '<meta name="baidu-site-verification" content="'.get_option($theme_shortname.'_baidu_owntag').'" />'.PHP_EOL;
}
add_action( 'wp_head', 'webmasterOwnerCheck');


// 테마에 필요한 스크립트들을 등록합니다.
if ( ! function_exists( 'bbse_scripts' ) )
{
  function bbse_scripts() {

	global $current_user,$theme_shortname;
    wp_get_current_user();

    // 제네릭콘
    //wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.0.2' );
    //폰트어썸
    wp_enqueue_style( 'fortawesome', '//netdna.bootstrapcdn.com/font-awesome/4.6.0/css/font-awesome.min.css', array(), '3.0.2' );
	wp_enqueue_style( 'dashicons' );
    //기본 스타일
    wp_enqueue_style( 'bbse-style', get_stylesheet_uri(), array(), '0.0.1' );
    wp_deregister_script('jquery');

    //구글 CDN에서 가져와보고
    wp_register_script('jquery', ("//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"), false, '1.11.1');
    wp_enqueue_script('jquery');
    wp_register_script('jquery-ui', get_template_directory_uri().'/js/jquery-ui.js', false, '1.10.2');
    wp_enqueue_script('jquery-ui');
	wp_register_script('jquery-modal',get_template_directory_uri().'/js/jquery.modal.js');
	wp_enqueue_script('jquery-modal',get_template_directory_uri().'/js/jquery.modal.js', false, '0.5.5');
	wp_enqueue_script('jquery-form');
    wp_register_script('sparejquery', (get_template_directory_uri().'/js/sparejquery.js'), false, '1.0.0');
    wp_enqueue_script('sparejquery');
    //wp_register_script('cookiejquery', (get_template_directory_uri().'/js/jquery.cookie.js'), false, '1.4.1');
    //wp_enqueue_script('cookiejquery');

    //테마용 스크립트
    wp_register_script('theme-script1', get_template_directory_uri().'/js/jquery.flexslider.js', false, '0.0.1');
    wp_enqueue_script('theme-script1');
    wp_register_script('theme-script2', get_template_directory_uri().'/js/uicommon.js', false, '0.0.1');
	wp_localize_script( 'theme-script2', 'common_var', 
		array( 
			'home_url' => home_url(),
			'login_page' => get_permalink(get_option('bbse_commerce_login_page')),
			'u' => base64_encode($current_user->user_login),
			'goods_template_url' => get_template_directory_uri()
		)
	);

	if (/*is_single() &&*/ wp_is_mobile() ){
		if(get_option($theme_shortname.'_sns_share_kStory') == 'U' || (get_option($theme_shortname.'_sns_share_kTalk') == 'U' && get_option($theme_shortname.'_sns_share_kakao_js_appkey'))){
			wp_register_script('kakaoAPI12015', '//developers.kakao.com/sdk/js/kakao.min.js', false, '1.0.50');
			wp_enqueue_script('kakaoAPI12015');
		}
	}

	wp_enqueue_script('theme-script2');
    wp_register_script('theme-front-lightBox', get_template_directory_uri().'/lightbox/js/lightbox.min.js',array('jquery'));
	wp_enqueue_script('theme-front-lightBox');
	wp_register_script('commerce-theme-common', get_template_directory_uri().'/js/common.js');
	wp_enqueue_script('commerce-theme-common');

	//CSS
	wp_enqueue_style('theme-front-lightBox',get_template_directory_uri().'/lightbox/css/lightbox.css');
	wp_enqueue_style('jquery-ui-css', get_template_directory_uri().'/admin/js/datepicker/commerce/jquery-ui-1.10.4.custom.css');
	wp_enqueue_style('jquery-modal-css', get_template_directory_uri().'/js/jquery.modal.css');

	if (current_user_can('administrator')){
		wp_enqueue_style('minicolors-color-ui',get_template_directory_uri().'/admin/css/jquery.minicolors.css');
		wp_enqueue_script('minicolors-js',get_template_directory_uri().'/admin/js/jquery.minicolors.js',array('jquery'));
	}

    wp_register_script('theme-elevatezoom', get_template_directory_uri().'/js/jquery.elevatezoom.js',array('jquery'));
	wp_enqueue_script('theme-elevatezoom');

	$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사
	if($nPayData['naver_pay_use']=='on' && ($nPayData['naver_pay_type']=='real' || ($nPayData['naver_pay_type']=='test' && $_REQUEST['npayTest']==true))){
		wp_register_script('naver-nPaylog', "//wcs.naver.net/wcslog.js",array('jquery'));
		wp_enqueue_script('naver-nPaylog');
	}

    //slick - 터치 슬라이더
    wp_enqueue_style('slick-css', get_template_directory_uri().'/openSrc/slick-1.5.0/slick/slick-theme.css', array(), '1.5.0' );
    wp_enqueue_style('slick-css2', get_template_directory_uri().'/openSrc/slick-1.5.0/slick/slick.css', array(), '1.5.0' );
    wp_register_script('slick-js', get_template_directory_uri().'/openSrc/slick-1.5.0/slick/slick.min.js', false, '1.5.0');
    wp_enqueue_script('slick-js');

/*
    wp_register_script('theme-script', get_template_directory_uri().'/js/theme.js', false, '0.0.1');
    wp_enqueue_script('theme-script');*/
  }
}
add_action( 'wp_enqueue_scripts', 'bbse_scripts' );

require_once('admin/common/config.php');
require_once('admin/class/theme_seo.class.php');
require_once('admin/class/theme_paging.class.php');
require_once('admin/class/theme_encryption.class.php');
require_once('admin/class/theme_mobile.detect.class.php');
$theme_SEO= new Theme_MySEOClass;
$detect = new BBSeCommerceMobileDetect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop');

if (is_admin()){
	require_once('admin/bbse_theme_update.php');
}

function set_custom_theme_add_admin(){
    global $options,$theme_shortname,$SITEMAPS;

	wp_enqueue_style($theme_shortname.'-ui',get_template_directory_uri().'/admin/js/jquery-ui.min.css');
	wp_enqueue_script($theme_shortname.'-jsui',get_template_directory_uri().'/admin/js/jquery-ui.min.js',array('jquery'));

    if($_GET['page']==basename(__FILE__)){

		if($_REQUEST['option_type']){

			if($_REQUEST['action']=='save'){

				if($_REQUEST['option_type']=="intro"){
					/* 인증마크 및 하단배너 설정 */
					$banner = get_option($theme_shortname."_intro_19_use_bottom_banner_count");
					for($i=1;$i<=$banner;$i++) {
						delete_option($theme_shortname."_intro_19_bottom_banner_type_".$i);
						delete_option($theme_shortname."_intro_19_bottom_banner_img_".$i);
						delete_option($theme_shortname."_intro_19_bottom_banner_url_".$i);
						delete_option($theme_shortname."_intro_19_bottom_banner_url_".$i."_window");
						delete_option($theme_shortname."_intro_19_bottom_banner_mallid_".$i);
						delete_option($theme_shortname."_intro_19_bottom_banner_businessno_".$i);
					}

					$site_cnt = 1;$banner_cnt = 1;$site_item_cnt = 0;$banner_item_cnt = 0;
					foreach($_REQUEST as $key=>$val) {
						$expKey = explode("_", $key);
						if($expKey[2].$expKey[3].$expKey[4].$expKey[5]=="intro19bottombanner") {
							if($expKey[6]=="type") {
								update_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt, $val);
								$banner_item_cnt++;
							}
							if($expKey[6]=="img") {
								update_option($theme_shortname."_intro_19_bottom_banner_img_".$banner_cnt, $val);
								$banner_item_cnt++;
							}
							if($expKey[6]=="url" && $expKey[8]=="") {
								if(get_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt)=='normal'){
									update_option($theme_shortname."_intro_19_bottom_banner_url_".$banner_cnt, $val);
								}
								else delete_option($theme_shortname."_intro_19_bottom_banner_url_".$banner_cnt);
								$banner_item_cnt++;
							}
							if($expKey[6]=="url" && $expKey[8]=="window") {
								if(get_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt)=='normal'){
									update_option($theme_shortname."_intro_19_bottom_banner_url_".$banner_cnt."_window", $val);
								}
								else delete_option($theme_shortname."_intro_19_bottom_banner_url_".$banner_cnt."_window");
								$banner_item_cnt++;
							}
							if($expKey[6]=="mallid") {
								if(get_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt)=='lguplus' || get_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt)=='inicis' || get_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt)=='allthegate'){
									update_option($theme_shortname."_intro_19_bottom_banner_mallid_".$banner_cnt, $val);
								}
								else delete_option($theme_shortname."_intro_19_bottom_banner_mallid_".$banner_cnt);
								$banner_item_cnt++;
							}
							if($expKey[6]=="businessno") {
								if(get_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt)=='allthegate' || get_option($theme_shortname."_intro_19_bottom_banner_type_".$banner_cnt)=='fairtrade'){
									update_option($theme_shortname."_intro_19_bottom_banner_businessno_".$banner_cnt, $val);
								}
								else delete_option($theme_shortname."_intro_19_bottom_banner_businessno_".$banner_cnt);
								$banner_item_cnt++;
							}
							if($banner_item_cnt==6) {
								$banner_cnt++;
								$banner_item_cnt = 0;
							}
						}
					}
					if($site_cnt > 1) $site_cnt-=1;
					if($banner_cnt > 1) $banner_cnt-=1;

					update_option($theme_shortname."_intro_19_use_bottom_banner_count", $banner_cnt);
					update_option($theme_shortname."_intro_19_use_bottom_banner_last", $banner_cnt);
					/* //인트로 관계사 사이트, 인증마크 및 하단배너 설정 */
				}
				elseif($_REQUEST['option_type']=="basic") {

					/* 관계사 사이트, 인증마크 및 하단배너 설정 */
					$site = get_option($theme_shortname."_basic_use_footer_family_site_count");
					for($i=1;$i<=$site;$i++) {
						delete_option($theme_shortname."_basic_footer_family_site_".$i);
						delete_option($theme_shortname."_basic_footer_family_site_".$i."_url");
						delete_option($theme_shortname."_basic_footer_family_site_".$i."_window");
					}
					$banner = get_option($theme_shortname."_basic_use_bottom_banner_count");
					for($i=1;$i<=$banner;$i++) {
						delete_option($theme_shortname."_basic_bottom_banner_type_".$i);
						delete_option($theme_shortname."_basic_bottom_banner_img_".$i);
						delete_option($theme_shortname."_basic_bottom_banner_url_".$i);
						delete_option($theme_shortname."_basic_bottom_banner_url_".$i."_window");
						delete_option($theme_shortname."_basic_bottom_banner_mallid_".$i);
						delete_option($theme_shortname."_basic_bottom_banner_businessno_".$i);
					}

					$site_cnt = 1;$banner_cnt = 1;$site_item_cnt = 0;$banner_item_cnt = 0;
					foreach($_REQUEST as $key=>$val) {
						$expKey = explode("_", $key);
						if($expKey[2].$expKey[3].$expKey[4].$expKey[5]=="basicfooterfamilysite") {
							if($expKey[7]=="") {
								update_option($theme_shortname."_basic_footer_family_site_".$site_cnt, $val);
								$site_item_cnt++;
							}
							if($expKey[7]=="url") {
								update_option($theme_shortname."_basic_footer_family_site_".$site_cnt."_url", $val);
								$site_item_cnt++;
							}
							if($expKey[7]=="window") {
								update_option($theme_shortname."_basic_footer_family_site_".$site_cnt."_window", $val);
								$site_item_cnt++;
							}
							if($site_item_cnt==3) {
								$site_cnt++;
								$site_item_cnt = 0;
							}
						}else if($expKey[2].$expKey[3].$expKey[4]=="basicbottombanner") {
							if($expKey[5]=="type") {
								update_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt, $val);
								$banner_item_cnt++;
							}
							if($expKey[5]=="img") {
								update_option($theme_shortname."_basic_bottom_banner_img_".$banner_cnt, $val);
								$banner_item_cnt++;
							}
							if($expKey[5]=="url" && $expKey[7]=="") {
								if(get_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt)=='normal'){
									update_option($theme_shortname."_basic_bottom_banner_url_".$banner_cnt, $val);
								}
								else delete_option($theme_shortname."_basic_bottom_banner_url_".$banner_cnt);
								$banner_item_cnt++;
							}
							if($expKey[5]=="url" && $expKey[7]=="window") {
								if(get_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt)=='normal'){
									update_option($theme_shortname."_basic_bottom_banner_url_".$banner_cnt."_window", $val);
								}
								else delete_option($theme_shortname."_basic_bottom_banner_url_".$banner_cnt."_window");
								$banner_item_cnt++;
							}
							if($expKey[5]=="mallid") {
								if(get_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt)=='lguplus' || get_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt)=='inicis' || get_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt)=='allthegate'){
									update_option($theme_shortname."_basic_bottom_banner_mallid_".$banner_cnt, $val);
								}
								else delete_option($theme_shortname."_basic_bottom_banner_mallid_".$banner_cnt);
								$banner_item_cnt++;
							}
							if($expKey[5]=="businessno") {
								if(get_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt)=='allthegate' || get_option($theme_shortname."_basic_bottom_banner_type_".$banner_cnt)=='fairtrade'){
									update_option($theme_shortname."_basic_bottom_banner_businessno_".$banner_cnt, $val);
								}
								else delete_option($theme_shortname."_basic_bottom_banner_businessno_".$banner_cnt);
								$banner_item_cnt++;
							}
							if($banner_item_cnt==6) {
								$banner_cnt++;
								$banner_item_cnt = 0;
							}
						}
					}
					if($site_cnt > 1) $site_cnt-=1;
					if($banner_cnt > 1) $banner_cnt-=1;
					/*
					if($_REQUEST[$theme_shortname."_basic_use_footer_family_site"]=="U") {
					}else if($_REQUEST[$theme_shortname."_basic_use_bottom_banner"]=="U") {
					}
					*/
					update_option($theme_shortname."_basic_use_footer_family_site_count", $site_cnt);
					update_option($theme_shortname."_basic_use_footer_family_site_last", $site_cnt);		
					update_option($theme_shortname."_basic_use_bottom_banner_count", $banner_cnt);
					update_option($theme_shortname."_basic_use_bottom_banner_last", $banner_cnt);
					/* //관계사 사이트, 인증마크 및 하단배너 설정 */
				
				}else if($_REQUEST['option_type']=="display") {

					/* 왼쪽 배너설정, 오른쪽 배너설정, 부가메뉴 설정 */
					$left = get_option($theme_shortname."_display_use_left_banner_count");
					for($i=1;$i<=$left;$i++) {
						delete_option($theme_shortname."_display_left_banner_img_".$i);
						delete_option($theme_shortname."_display_left_banner_url_".$i);
						delete_option($theme_shortname."_display_left_banner_url_".$i."_window");
					}
					$right = get_option($theme_shortname."_display_use_right_banner_count");
					for($i=1;$i<=$right;$i++) {
						delete_option($theme_shortname."_display_right_banner_use_".$i);
						delete_option($theme_shortname."_display_right_banner_img_".$i);
						delete_option($theme_shortname."_display_right_banner_url_".$i);
						delete_option($theme_shortname."_display_right_banner_url_".$i."_window");
					}
					$opt = get_option($theme_shortname."_display_option_menu_count");
					for($i=1;$i<=$opt;$i++) {
						delete_option($theme_shortname."_display_option_menu_title_".$i);
						delete_option($theme_shortname."_display_option_menu_link_".$i);
						delete_option($theme_shortname."_display_option_menu_link_".$i."_window");
					}

					/* 모바일 아이콘 메뉴 */
					for($i=1;$i<=8;$i++) {
						delete_option($theme_shortname."_display_mobile_icon_type_".$i);
						delete_option($theme_shortname."_display_mobile_icon_icon_".$i);
						delete_option($theme_shortname."_display_mobile_icon_title_".$i);
						delete_option($theme_shortname."_display_mobile_icon_url_".$i);
						delete_option($theme_shortname."_display_mobile_icon_url_".$i."_window");
					}

					$left_cnt = 1;$right_cnt = 1;$opt_cnt = 1;$left_item_cnt=0;$right_item_cnt=0;$opt_item_cnt=0;$mobileIcon_cnt = 1;$mobileIcon_item_cnt = 0;

					foreach($_REQUEST as $key=>$val) {
						$expKey = explode("_", $key);
						if($expKey[2].$expKey[3].$expKey[4]=="displayleftbanner") {
							if($expKey[5]=="img") {
								update_option($theme_shortname."_display_left_banner_img_".$left_cnt, $val);
								$left_item_cnt++;
							}
							if($expKey[5]=="url" && $expKey[7]=="") {
								update_option($theme_shortname."_display_left_banner_url_".$left_cnt, $val);
								$left_item_cnt++;
							}
							if($expKey[5]=="url" && $expKey[7]=="window") {
								update_option($theme_shortname."_display_left_banner_url_".$left_cnt."_window", $val);
								$left_item_cnt++;
							}
							if($left_item_cnt==3) {
								$left_cnt++;
								$left_item_cnt = 0;
							}
						}else if($expKey[2].$expKey[3].$expKey[4]=="displayrightbanner") {
							if($expKey[5]=="use") {
								update_option($theme_shortname."_display_right_banner_use_".$right_cnt, $val);
								$right_item_cnt++;
							}
							if($expKey[5]=="img") {
								update_option($theme_shortname."_display_right_banner_img_".$right_cnt, $val);
								$right_item_cnt++;
							}
							if($expKey[5]=="url" && $expKey[7]=="") {
								update_option($theme_shortname."_display_right_banner_url_".$right_cnt, $val);
								$right_item_cnt++;
							}
							if($expKey[5]=="url" && $expKey[7]=="window") {
								update_option($theme_shortname."_display_right_banner_url_".$right_cnt."_window", $val);
								$right_item_cnt++;
							}
							if($right_item_cnt==4) {
								$right_cnt++;
								$right_item_cnt = 0;
							}
						}else if($expKey[2].$expKey[3].$expKey[4]=="displayoptionmenu") {

							if($expKey[5]=="title") {
								update_option($theme_shortname."_display_option_menu_title_".$opt_cnt, $val);
								$opt_item_cnt++;
							}
							if($expKey[5]=="link" && $expKey[7]=="") {
								update_option($theme_shortname."_display_option_menu_link_".$opt_cnt, $val);
								$opt_item_cnt++;
							}
							if($expKey[5]=="link" && $expKey[7]=="window") {
								update_option($theme_shortname."_display_option_menu_link_".$opt_cnt."_window", $val);
								$opt_item_cnt++;
							}
							if($opt_item_cnt==3) {
								$opt_cnt++;
								$opt_item_cnt = 0;
							}

						}
						elseif($expKey[2].$expKey[3].$expKey[4]=="".$saveType."displaymobileicon") { /* 모바일 아이콘 메뉴 */
							if($expKey[5]=="type") {
								update_option($theme_shortname."_".$saveType."display_mobile_icon_type_".$mobileIcon_cnt, $val);
								$mobileIcon_item_cnt++;
							}
							elseif($expKey[5]=="icon") {
								update_option($theme_shortname."_".$saveType."display_mobile_icon_icon_".$mobileIcon_cnt, $val);
								$mobileIcon_item_cnt++;
							}
							elseif($expKey[5]=="title") {
								update_option($theme_shortname."_".$saveType."display_mobile_icon_title_".$mobileIcon_cnt, $val);
								$mobileIcon_item_cnt++;
							}
							elseif($expKey[5]=="url" && $expKey[7]=="") {
								update_option($theme_shortname."_".$saveType."display_mobile_icon_url_".$mobileIcon_cnt, $val);
								$mobileIcon_item_cnt++;
							}
							elseif($expKey[5]=="url" && $expKey[7]=="window") {
								update_option($theme_shortname."_".$saveType."display_mobile_icon_url_".$mobileIcon_cnt."_window", $val);
								$mobileIcon_item_cnt++;
							}
							if($mobileIcon_item_cnt==5) {
								$mobileIcon_cnt++;
								$mobileIcon_item_cnt = 0;
							}
						}
					}
					if($left_cnt > 1) $left_cnt-=1;
					if($right_cnt > 1) $right_cnt-=1;
					if($opt_cnt > 1) $opt_cnt-=1;
					/*
					if($_REQUEST[$theme_shortname."_basic_use_footer_family_site"]=="U") {
					}else if($_REQUEST[$theme_shortname."_basic_use_bottom_banner"]=="U") {
					}
					*/
					update_option($theme_shortname."_display_use_left_banner_count", $left_cnt);
					update_option($theme_shortname."_display_use_left_banner_last", $left_cnt);		
					update_option($theme_shortname."_display_use_right_banner_count", $right_cnt);
					update_option($theme_shortname."_display_use_right_banner_last", $right_cnt);
					update_option($theme_shortname."_display_option_menu_count", $opt_cnt);
					update_option($theme_shortname."_display_option_menu_last", $opt_cnt);
					/* //왼쪽 배너설정, 오른쪽 배너설정, 부가메뉴 설정 */

					/* 모바일 아이콘 메뉴 */
					if($mobileIcon_cnt >1) $mobileIcon_cnt-=1;
					update_option($theme_shortname."_display_use_mobile_icon_count", $mobileIcon_cnt);
					update_option($theme_shortname."_display_use_mobile_icon_last", $mobileIcon_cnt);
				}
				elseif($_REQUEST['option_type']=="sitemap") {
					if ($_REQUEST[$theme_shortname.'_sitemap_usefile'] == 'yes'){
						if(is_object($SITEMAPS)) $SITEMAPS->bbse_makeSitemap('base-file');
					}
				}

				foreach($options[$_REQUEST['option_type']] as $value){
					if(isset($_REQUEST[$value['id']])){
						update_option($value['id'], $_REQUEST[$value['id']]);
					}
					else { 
						delete_option($value['id']);
					}
				}
				header("Location: themes.php?page=functions.php&optTtpe=".$_REQUEST['option_type']."&saved=true");
				die;
			}
			else if($_REQUEST['action']=='reset'){
				foreach($options[$_REQUEST['option_type']] as $value){
					delete_option($value['id']);
				}

				header("Location: themes.php?page=functions.php&optTtpe=".$_REQUEST['option_type']."&reseted=true");
				die;
			}
		}
    }

    add_theme_page("테마 환경설정", "테마 환경설정", 'edit_themes', basename(__FILE__), 'set_custom_theme_admin');
}

function set_custom_theme_admin(){
	global $theme_shortname,$options;

	/*** for License Key ***/
	/* if(!bbse_theme_license_check()) bbse_theme_License_manage('chekced');
	elseif($_REQUEST['optTtpe']=='license'){
		wp_enqueue_script('thickbox'); wp_enqueue_style('thickbox');
		require_once(BBSE_THEME_ABS_PATH."/admin/theme_option_license.php");
	}
	else{
		$optArray="";
		foreach ($options as $key => $val) {
			if($optArray) $optArray .=",";
			$optArray .="'".$key."'";
		}
		if($optArray) $optArray="[".$optArray."]";

		require_once('admin/theme_option.php');
	}
	*/
	
	$optArray="";
	foreach ($options as $key => $val) {
	    if($optArray) $optArray .=",";
	    $optArray .="'".$key."'";
	}
	if($optArray) $optArray="[".$optArray."]";
	
	require_once('admin/theme_option.php');
}
add_action('admin_menu', 'set_custom_theme_add_admin');

if(!function_exists("remove_ssl_url")) {
	function remove_ssl_url($h_url) {
		global $theme_shortname;
		$p_url = parse_url($h_url);
		if($p_url['scheme'] == "https") {
			$return_url = "http://".$p_url['host'];
			if($p_url['port'] != "" && $p_url['port'] != get_option($theme_shortname."_ssl_port")) {
				$return_url .= ":".$p_url['port'].($p_url['path']!=""?$p_url['path']:"").($p_url['query']!=""?"?".$p_url['query']:"");
			}else{
				$return_url .= ($p_url['path']!=""?$p_url['path']:"").($p_url['query']!=""?"?".$p_url['query']:"");
			}
		}else{
			$return_url = $h_url;
		}
		return $return_url;
	}
}
/*
#################################################################################
BBS e-Commerce Theme [START]
#################################################################################
*/
define("BBSE_COMMERCE_SITE_URL", remove_ssl_url(home_url()));
define("BBSE_COMMERCE_THEME_ABS_PATH", get_template_directory());
define("BBSE_COMMERCE_THEME_WEB_URL", get_template_directory_uri());
define("BBSE_COMMERCE_LEAVE_REASON",serialize(array("A"=>"서비스 미이용","B"=>"서비스 불만족","C"=>"다른아이디 재가입","D"=>"직접입력")));
$tblCnt = $wpdb->get_row("SHOW TABLES LIKE 'bbse_commerce_goods'");
//if(true){
if(count($tblCnt) > 0) {
	$priceSearchData = $wpdb->get_row("SELECT MIN(goods_price) as min_price, MAX(goods_price) AS max_price FROM bbse_commerce_goods");
	$sliderMinPrice = ($priceSearchData->min_price)?$priceSearchData->min_price:1000;
	$sliderMaxPrice = ($priceSearchData->max_price)?$priceSearchData->max_price:5000000;
}else{
	$sliderMinPrice = 1000;
	$sliderMaxPrice = 5000000;
}
define("BBSE_COMMERCE_SEARCH_MIN_PRICE", $sliderMinPrice);
define("BBSE_COMMERCE_SEARCH_MAX_PRICE", $sliderMaxPrice);

require_once('admin/class/theme_commerce.class.php');
require_once('admin/class/style.class.php');
require_once('admin/class/function.php');

// 관리자 메뉴설정
function bbse_commerce_theme_menu(){

	global $theme_shortname,$options;
	$optArray="";
	foreach ($options as $key => $val) {
		if($optArray) $optArray .=",";
		$optArray .="'".$key."'";
	}
	if($optArray) $optArray="[".$optArray."]";

	wp_enqueue_style($theme_shortname.'-admin-ui',get_template_directory_uri().'/admin/css/admin-style.css');

	wp_enqueue_style($theme_shortname.'-color-ui',get_template_directory_uri().'/admin/css/jquery.minicolors.css');
	wp_enqueue_script($theme_shortname.'-js',get_template_directory_uri().'/admin/js/jquery.minicolors.js',array('jquery'));

	wp_enqueue_style('theme-lightBox',get_template_directory_uri().'/lightbox/css/lightbox.css');
	wp_enqueue_script('theme-lightBox',get_template_directory_uri().'/lightbox/js/lightbox.min.js',array('jquery'));

	wp_register_script('jquery-ui-custom', get_template_directory_uri().'/admin/js/datepicker/jquery-ui-1.10.4.custom.min.js');
	wp_enqueue_script('jquery-ui-custom');
	wp_register_script('commerce-theme-common', get_template_directory_uri().'/js/common.js');
	wp_enqueue_script('commerce-theme-common');
	wp_enqueue_style('jquery-ui-css', get_template_directory_uri().'/admin/js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');

	wp_enqueue_style('thickbox');
	wp_enqueue_script( 'thickbox' );
}
add_action('admin_menu', 'bbse_commerce_theme_menu');


function bbse_commerce_theme_create_tables(){
	global $theme_shortname;

	//레이아웃 기본값 설정
	if(!get_option($theme_shortname."_layout_left_category")) update_option($theme_shortname."_layout_left_category", "Y");
	if(!get_option($theme_shortname."_layout_left_price_search")) update_option($theme_shortname."_layout_left_price_search", "Y");
	if(!get_option($theme_shortname."_layout_left_today_sale")) update_option($theme_shortname."_layout_left_today_sale", "Y");
	if(!get_option($theme_shortname."_layout_left_hot_item")) update_option($theme_shortname."_layout_left_hot_item", "Y");
	if(!get_option($theme_shortname."_layout_left_bank_info")) update_option($theme_shortname."_layout_left_bank_info", "Y");
	if(!get_option($theme_shortname."_layout_right_last_goods")) update_option($theme_shortname."_layout_right_last_goods", "Y");

	//상품배치 기본값 설정
	if(!get_option($theme_shortname."_goodsplace_sort_1"))  update_option($theme_shortname."_goodsplace_sort_1", "1");
	if(!get_option($theme_shortname."_goodsplace_use_1"))  update_option($theme_shortname."_goodsplace_use_1", "Y");
	if(!get_option($theme_shortname."_goodsplace_title_1"))  update_option($theme_shortname."_goodsplace_title_1", "추천상품");
	if(!get_option($theme_shortname."_goodsplace_description_1"))  update_option($theme_shortname."_goodsplace_description_1", "놓치면 안되는 제품");
	if(!get_option($theme_shortname."_goodsplace_url_1"))  update_option($theme_shortname."_goodsplace_url_1", "/?bbseCat=recommend");
	if(!get_option($theme_shortname."_goodsplace_url_1_window"))  update_option($theme_shortname."_goodsplace_url_1_window", "_self");
	if(!get_option($theme_shortname."_goodsplace_sort_2"))  update_option($theme_shortname."_goodsplace_sort_2", "2");
	if(!get_option($theme_shortname."_goodsplace_use_2"))  update_option($theme_shortname."_goodsplace_use_2", "Y");
	if(!get_option($theme_shortname."_goodsplace_title_2"))  update_option($theme_shortname."_goodsplace_title_2", "베스트상품");
	if(!get_option($theme_shortname."_goodsplace_description_2"))  update_option($theme_shortname."_goodsplace_description_2", "고객님들이 가장 많이 사랑하는 제품");
	if(!get_option($theme_shortname."_goodsplace_url_2"))  update_option($theme_shortname."_goodsplace_url_2", "/?bbseCat=best");
	if(!get_option($theme_shortname."_goodsplace_url_2_window"))  update_option($theme_shortname."_goodsplace_url_2_window", "_self");
	if(!get_option($theme_shortname."_goodsplace_sort_3"))  update_option($theme_shortname."_goodsplace_sort_3", "3");
	if(!get_option($theme_shortname."_goodsplace_use_3"))  update_option($theme_shortname."_goodsplace_use_3", "Y");
	if(!get_option($theme_shortname."_goodsplace_title_3"))  update_option($theme_shortname."_goodsplace_title_3", "MD기획상품");
	if(!get_option($theme_shortname."_goodsplace_description_3"))  update_option($theme_shortname."_goodsplace_description_3", "기획에 맞게 추천해드리는 상품");
	if(!get_option($theme_shortname."_goodsplace_line_3"))  update_option($theme_shortname."_goodsplace_line_3", "4");
	if(!get_option($theme_shortname."_goodsplace_sort_4"))  update_option($theme_shortname."_goodsplace_sort_4", "4");
	if(!get_option($theme_shortname."_goodsplace_use_4"))  update_option($theme_shortname."_goodsplace_use_4", "Y");
	if(!get_option($theme_shortname."_goodsplace_title_4"))  update_option($theme_shortname."_goodsplace_title_4", "신상품");
	if(!get_option($theme_shortname."_goodsplace_description_4"))  update_option($theme_shortname."_goodsplace_description_4", "새로 들어온 따끈따끈한 신상품");
	if(!get_option($theme_shortname."_goodsplace_sort_5"))  update_option($theme_shortname."_goodsplace_sort_5", "5");
	if(!get_option($theme_shortname."_goodsplace_use_5"))  update_option($theme_shortname."_goodsplace_use_5", "Y");
	if(!get_option($theme_shortname."_goodsplace_title_5"))  update_option($theme_shortname."_goodsplace_title_5", "베스트 상품평");
	if(!get_option($theme_shortname."_goodsplace_description_5"))  update_option($theme_shortname."_goodsplace_description_5", "쇼핑에 도움이 되는 베스트 상품평");
	if(!get_option($theme_shortname."_goodsplace_line_5"))  update_option($theme_shortname."_goodsplace_line_5", "4");
	if(!get_option($theme_shortname."_goodsplace_url_5"))  update_option($theme_shortname."_goodsplace_url_5", "/?bbsePage=review");
	if(!get_option($theme_shortname."_goodsplace_url_5_window"))  update_option($theme_shortname."_goodsplace_url_5_window", "_self");

	//서브화면 기본값 설정
	if(!get_option($theme_shortname."_sub_skin_name")) update_option($theme_shortname."_sub_skin_name", "basic");
	if(!get_option($theme_shortname."_sub_goods_view_use_left_sidebar")) update_option($theme_shortname."_sub_goods_view_use_left_sidebar", "U");

	//색 기본값 설정
	if(!get_option($theme_shortname."_color_main_theme")) update_option($theme_shortname."_color_main_theme", "#e22a40");
}
//register_activation_hook(__FILE__, 'bbse_commerce_theme_create_tables');
add_action("after_switch_theme", "bbse_commerce_theme_create_tables");

// 테마 삭제 시
function uninstall_bbse_commerce_theme(){
	global $wpdb;
}
//register_uninstall_hook(__FILE__, 'uninstall_bbse_commerce_theme');

// 로그인 페이지 쇼트코드
function bbse_commerce_membership_login_html($atts, $content=null){
	global $wpdb;
	ob_start();
	$current_user = wp_get_current_user();	
	$list_cls = new BBSeThemeCommerce;
	$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
	$list_cls->loginPage($skinname);
	$rtnContent = ob_get_contents();
	ob_end_clean();
	return $rtnContent;
}
add_shortcode('bbse_commerce_membership_login', 'bbse_commerce_membership_login_html');

// 회원가입 페이지 쇼트코드
function bbse_commerce_membership_join_html($atts, $content=null){
	global $wpdb;
	ob_start();
	$current_user = wp_get_current_user();	
	$list_cls = new BBSeThemeCommerce;
	$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
	$list_cls->joinPage($skinname);
	$rtnContent = ob_get_contents();
	ob_end_clean();
	return $rtnContent;
}
add_shortcode('bbse_commerce_membership_join', 'bbse_commerce_membership_join_html');

// 아이디찾기 페이지 쇼트코드
function bbse_commerce_membership_id_search_html($atts, $content=null){
	global $wpdb;
	ob_start();
	$current_user = wp_get_current_user();	
	$list_cls = new BBSeThemeCommerce;
	$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
	$list_cls->idSearch($skinname);
	$rtnContent = ob_get_contents();
	ob_end_clean();
	return $rtnContent;
}
add_shortcode('bbse_commerce_membership_id_search', 'bbse_commerce_membership_id_search_html');

// 비밀번호찾기 페이지 쇼트코드
function bbse_commerce_membership_pass_search_html($atts, $content=null){
	global $wpdb;
	ob_start();
	$current_user = wp_get_current_user();	
	$list_cls = new BBSeThemeCommerce;
	$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
	$list_cls->passSearch($skinname);
	$rtnContent = ob_get_contents();
	ob_end_clean();
	return $rtnContent;
}
add_shortcode('bbse_commerce_membership_pass_search', 'bbse_commerce_membership_pass_search_html');

// 회원탈퇴 페이지 쇼트코드
function bbse_commerce_membership_secession_html($atts, $content=null){
	global $wpdb;
	ob_start();
	$current_user = wp_get_current_user();	
	$list_cls = new BBSeThemeCommerce;
	$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
	$list_cls->deletePage($skinname);
	$rtnContent = ob_get_contents();
	ob_end_clean();
	return $rtnContent;
}
add_shortcode('bbse_commerce_membership_secession', 'bbse_commerce_membership_secession_html');

// 관리자 > 사용자 관리 숨김
function bbse_commerce_remove_menus(){
	//remove_submenu_page('users.php', 'user-new.php');
	//remove_submenu_page('users.php', 'users.php');
}
add_action('admin_menu', 'bbse_commerce_remove_menus');

//admin bar remove
function bbse_commerce_remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}
}
add_action('after_setup_theme', 'bbse_commerce_remove_admin_bar');
/*
#################################################################################
BBS e-Commerce Theme [END]
#################################################################################
*/


// 사용자정의사용 메뉴 제거
function remove_customize_menus(){
	remove_submenu_page( 'themes.php', 'customize.php' );
}
add_action( 'admin_menu', 'remove_customize_menus' );


if (!is_admin()){
	add_action('wp_enqueue_scripts', 'set_custom_default_js');
}

if (!function_exists('set_custom_default_js')) {
    function set_custom_default_js() {

    }
}

//커스컴 메뉴를 사용합니다.
register_nav_menus();

// 출력 파라미터 등록
if (!function_exists('bbse_commerce_add_query_category')) {
	function bbse_commerce_add_query_category($qvars) {
		$qvars[] = 'bbseCat';
		$qvars[] = 'bbseGoods';
		$qvars[] = 'bbsePage';
		$qvars[] = 'bbseMy';
		$qvars[] = 'review_idx';
		$qvars[] = 'review_page';
		$qvars[] = 'bbseDBurl'; // [네이버 지식쇼핑] DB URL 검사
		$qvars[] = 'ITEM_ID'; // [네이버 페이] 상품정보 XML
		return $qvars;
	}
	add_filter('query_vars', 'bbse_commerce_add_query_category');
}

// 출력 템플릿 등록
if (!function_exists('bbse_commerce_category_template')) {
	function bbse_commerce_category_template($template) {
		global $wp,$wpdb,$theme_shortname;

		if(get_option($theme_shortname."_intro_use")=='U'){ // 인트로 사용 시 체크
			bbse_get_intro_view_check();
		}

		$wp_bbseGoods=get_query_var('bbseGoods');
		$wp_bbseCat=get_query_var('bbseCat');
		$wp_bbsePage=get_query_var('bbsePage');
		$wp_bbseMy=get_query_var('bbseMy');
		$wp_bbseDBurl=get_query_var('bbseDBurl');
		$wp_ITEM_ID=get_query_var('ITEM_ID');


		if($wp_bbseGoods>'0') { // 상품 상세정보
			$goodsCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$wp_bbseGoods."' AND (goods_display='display' OR goods_display='soldout')"); 
			if($goodsCnt>'0'){
				$new_template = locate_template( array( 'part/goods-detail.php' ) );
				if ($new_template ) return $new_template;
			}
			else return $template;
		}
		elseif ($wp_bbseCat!="") { // 상품 리스트
			$goodsMainItem = array('recommend', 'best', 'md', 'new', 'today', 'hot', 'search');
			if(in_array($wp_bbseCat, $goodsMainItem)==true) {
				$new_template = locate_template( array( 'part/goods-list.php' ) );
				if ($new_template ) return $new_template;
			}else{
				$categoryCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_category WHERE idx='".$wp_bbseCat."' AND c_use='Y'"); 
				if($categoryCnt>'0'){
					$new_template = locate_template( array( 'part/goods-list.php' ) );
					if ($new_template ) return $new_template;
				}
				else return $template;
			}
		}
		else if($wp_bbsePage=='review') { // 베스트 상품평 리스트
			$new_template = locate_template( array( 'part/goods-review.php' ) );
			if ($new_template ) return $new_template;
		}
		else if($wp_bbsePage=='cart') { // 장바구니
			$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
			$new_template = locate_template( array( 'skin/'.$skinname.'/order-cart.php' ) );
			if ($new_template ) return $new_template;
		}
		else if($wp_bbsePage=='order-agree') { // 비회원 약관동의
			$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
			$new_template = locate_template( array( 'skin/'.$skinname.'/order-agree.php' ) );
			if ($new_template ) return $new_template;
		}
		else if($wp_bbsePage=='order') { // 주문서작성
			$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
			$new_template = locate_template( array( 'skin/'.$skinname.'/order-order.php' ) );
			if ($new_template ) return $new_template;
		}
		else if($wp_bbsePage=='order-payment') { // 주문서작성
			$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
			$new_template = locate_template( array( 'skin/'.$skinname.'/order-order-payment.php' ) );
			if ($new_template ) return $new_template;
		}
		else if($wp_bbsePage=='order-ok') { // 주문서접수완료
			$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
			$new_template = locate_template( array( 'skin/'.$skinname.'/order-order-ok.php' ) );
			if ($new_template ) return $new_template;
		}
		else if($wp_bbseMy!="") { // 마이페이지
			$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");
			$new_template = locate_template( array( 'skin/'.$skinname.'/mypage-list.php' ) );
			if ($new_template ) return $new_template;
		}
		elseif($wp_bbseDBurl) { // [네이버 지식쇼핑] DB URL 검사
			$nvrData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='navershop'");
			if($nvrData->idx){
				$nvrData=unserialize($nvrData->config_data);
				if($nvrData['naver_shop_use']=='on'){
					if($nvrData['naver_shop_ep_version']=='3.0') require_once(BBSE_THEME_ABS_PATH.'/nshop/bbse-nshop-summary-v30.php');
					else require_once(BBSE_THEME_ABS_PATH.'/nshop/bbse-nshop-summary.php');
				}
			}
		}
		elseif($wp_ITEM_ID) { // [네이버 페이] 상품정보 XML
			require_once(BBSE_THEME_ABS_PATH.'/npay/bbse-npay-goods.info.php');
		}
		else return $template;
	}
	add_filter('template_include', 'bbse_commerce_category_template');
}

// [네이버 지식쇼핑] 카테고리 체크
if(!function_exists('bbse_commerce_nshop_category')) {
	function bbse_commerce_nshop_category($cIdx) {
		global $wpdb;
		$tCateIdx=explode("|",$cIdx);
		$rtnArray=Array();

		$cData = $wpdb->get_row("SELECT * FROM bbse_commerce_category WHERE idx='".$tCateIdx['1']."'");
		if($cData->depth_1 > 0 && $cData->depth_2 > 0 && $cData->depth_3 > 0) {//소분류
			$depth1 = $wpdb->get_row("SELECT idx,c_name FROM bbse_commerce_category WHERE c_use='Y' AND depth_1='".$cData->depth_1."' AND depth_2=0 and depth_3=0");
			$rtnArray['idx_1']=$depth1->idx;
			$rtnArray['depth_1']=$depth1->c_name;

			$depth2 = $wpdb->get_row("SELECT idx,c_name FROM bbse_commerce_category WHERE c_use='Y' AND depth_1='".$cData->depth_1."' AND depth_2='".$cData->depth_2."' AND depth_3=0");
			$rtnArray['idx_2']=$depth2->idx;
			$rtnArray['depth_2']=$depth2->c_name;

			$depth3 = $wpdb->get_row("SELECT idx,c_name FROM bbse_commerce_category WHERE c_use='Y' AND depth_1='".$cData->depth_1."' AND depth_2='".$cData->depth_2."' AND depth_3='".$cData->depth_3."'");
			$rtnArray['idx_2']=$depth3->idx;
			$rtnArray['depth_2']=$depth3->c_name;

		}
		else if($cData->depth_1 > 0 && $cData->depth_2 > 0 && $cData->depth_3 == 0) {//중분류
			$depth1 = $wpdb->get_row("SELECT idx,c_name FROM bbse_commerce_category WHERE c_use='Y' AND depth_1='".$cData->depth_1."' AND depth_2=0 AND depth_3=0");
			$rtnArray['idx_1']=$depth1->idx;
			$rtnArray['depth_1']=$depth1->c_name;

			$depth2 = $wpdb->get_row("SELECT idx,c_name FROM bbse_commerce_category WHERE c_use='Y' AND depth_1='".$cData->depth_1."' AND depth_2='".$cData->depth_2."' AND depth_3=0");
			$rtnArray['idx_2']=$depth2->idx;
			$rtnArray['depth_2']=$depth2->c_name;
		}
		else if($cData->depth_1 > 0 && $cData->depth_2 == 0 && $cData->depth_3 == 0) {//대분류
			$depth1 = $wpdb->get_row("SELECT idx,c_name FROM bbse_commerce_category WHERE c_use='Y' AND depth_1='".$cData->depth_1."' AND depth_2=0 AND depth_3=0");
			$rtnArray['idx_1']=$depth1->idx;
			$rtnArray['depth_1']=$depth1->c_name;
		}

		return $rtnArray;
	}
}

// 사이트 로고를 출력합니다.
if (!function_exists('get_custom_logo')) {
	function get_custom_logo(){
		global $theme_shortname;

		switch(get_option($theme_shortname.'_basic_logo_type')){
			case 'text':
				if(!get_option($theme_shortname.'_basic_logo_color')) $txtColor="#ffffff";
				else $txtColor=get_option($theme_shortname.'_basic_logo_color');
				if(get_option($theme_shortname.'_basic_logo_text')) $top_logo_view="<a href='".home_url()."'><div style='font-size:".get_option($theme_shortname.'_basic_logo_text_size')."px;color:".$txtColor.";font-weight:bold;}'>".get_option($theme_shortname.'_basic_logo_text')."</div></a>";
				else $top_logo_view="";
			break;
			case 'image':
				if(get_option($theme_shortname.'_basic_logo_img')) $top_logo_view="<a href='".home_url()."'><img src='".get_option($theme_shortname.'_basic_logo_img')."' alt='logo' /></a>";
				else $top_logo_view="";

			break;
			default:

			break;
		}
		return $top_logo_view;
	}
}

if(!function_exists('table_exists')) {
	function table_exists($table) {
		global $wpdb;
		if(!$table) return false;

		$total = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.TABLES WHERE table_name='".$wpdb->prefix.$table."'");
		if($total>0) $tbName=$wpdb->prefix.$table;
		else $tbName=$table;

		$row = $wpdb->get_row("show tables like '".$tbName."'",ARRAY_N);
		if($row[0]) return 1;
		else return 0;
	}
}

//카테고리 출력
if (!function_exists('topCategoryView')) {
	function topCategoryView() {
		global $wpdb;
		$linkUrl = home_url();
		if(!table_exists('bbse_commerce_category')) {echo "BBS e-Commerce 플러그인을 먼저 설치해주세요.";return;}
		
		$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
		
		$currUserID=$current_user->user_login;
		$Loginflag='member';
		$member = $wpdb->get_results("SELECT user_class,user_id FROM bbse_commerce_membership='".$currUserID."'");
		$currUserClass = $member->user_class;
		
		$view = '<ul id="snv" class="navi_common">';
        //$cate1 = $wpdb->get_results("select * from bbse_commerce_category where c_use='Y' and depth_1>0 and depth_2=0 and depth_3=0 order by c_rank asc");
		$cate1 = $wpdb->get_results("select * from bbse_commerce_category where c_use='Y' and depth_1>0 and depth_2=0 and depth_3=0 and user_class ='".$currUserClass."' order by c_rank asc");
		
		if(cate1 != null && cate1!= ""){
    		foreach($cate1 as $i1=>$d1) {
    			$dep2_cnt = $wpdb->get_var("select count(*) from bbse_commerce_category where depth_1='".$d1->depth_1."' and depth_2>0 and depth_3=0 and c_use='Y'");
    			$view .= '<li class="menu-item menu-item-type-tax  onomy menu-item-object-category '.(($dep2_cnt>0)?'menu-item-has-children':'').' menu-item-'.$d1->idx.'"><a href="'.$linkUrl.'/?bbseCat='.$d1->idx.'">'.$d1->c_name.'</a>';
    			if($dep2_cnt > 0) {
    				$cate2 = $wpdb->get_results("select * from bbse_commerce_category where depth_1='".$d1->depth_1."' and depth_2>0 and depth_3=0 and c_use='Y' order by c_rank asc");
    				$view .= '<ul class="sub-menu">';
    				foreach($cate2 as $i2=>$d2) {
    					$dep3_cnt = $wpdb->get_var("select count(*) from bbse_commerce_category where depth_1='".$d2->depth_1."' and depth_2='".$d2->depth_2."' and depth_3>0 and c_use='Y'");
    					$view .= '<li class="menu-item menu-item-type-post_type menu-item-object-page '.(($dep3_cnt>0)?'menu-item-has-children':'').' menu-item-'.$d2->idx.'"><a href="'.$linkUrl.'/?bbseCat='.$d2->idx.'">'.$d2->c_name.'</a>';
    					if($dep3_cnt > 0) {
    						$view .= '<ul class="sub-menu">';
    						$cate3 = $wpdb->get_results("select * from bbse_commerce_category where depth_1='".$d2->depth_1."' and depth_2='".$d2->depth_2."' and depth_3>0 and c_use='Y' order by c_rank asc");
    						foreach($cate3 as $i3=>$d3) {
    							$view .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-'.$d2->idx.'"><a href="'.$linkUrl.'/?bbseCat='.$d3->idx.'">'.$d3->c_name.'</a></li>';
    						}
    						$view .= '</ul>';
    					}
    					$view .= '</li>';
    				}
    				$view .= '</ul>';
    			}
    			$view .= '</li>';
    		}
    		$view .= '</ul>';
    		echo $view;
		}
	}
}

//카테고리 출력
if (!function_exists('leftCategoryView')) {
	function leftCategoryView($tType) {
		global $wpdb;
	    
	    $current_user = wp_get_current_user();  // 현재 회원의 정보 추출
	    $currUserID=$current_user->user_login;
	    $role = $current_user->roles[0];
	    $member = "";
	    $currUserClass ="";
	    if($role != "administrator"){
    	    $member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id='".$currUserID."'");
    	    $currUserClass = $member[0]->user_class;
	    }else{
	        $member = $wpdb->get_results("SELECT user_class,user_id FROM bbse_commerce_membership");
	    }
    	    
		$linkUrl = home_url();
		if(!table_exists('bbse_commerce_category')) {echo "BBS e-Commerce 플러그인을 먼저 설치해주세요.";return;}
		if(wp_is_mobile()) $dataDevice="mobile";
		else $dataDevice="pc";
		$view = '<ul id="category-left-menu-'.$tType.'" data-device="'.$dataDevice.'" class="">';
		$cate1 = "";
		if($role != "administrator"){
		  $cate1 = $wpdb->get_results("select * from bbse_commerce_category where c_use='Y' and user_class='".$currUserClass."' and depth_1>0 and depth_2=0 and depth_3=0 order by c_rank asc");
		}
		else{
		    $cate1 = $wpdb->get_results("select * from bbse_commerce_category where c_use='Y' and depth_1>0 and depth_2=0 and depth_3=0 order by c_rank asc");
		}
		foreach($cate1 as $i1=>$d1) {
			$dep2_cnt = $wpdb->get_var("select count(*) from bbse_commerce_category where depth_1='".$d1->depth_1."' and depth_2>0 and depth_3=0 and c_use='Y'");
			if(wp_is_mobile() && $dep2_cnt > 0) $dep1Arrow="<span data-menu='".$d1->idx."' class='gnb_category_1_span'> ▼ </span>";
			else $dep1Arrow="";
			$view .= '<li class="menu-item menu-item-type-taxonomy menu-item-object-category menu-item-has-children '.(($dep2_cnt>0)?'found-sub':'no-sub').' menu-item-'.$d1->idx.'"><a href="'.$linkUrl.'/?bbseCat='.$d1->idx.'">'.$d1->c_name.'</a>'.$dep1Arrow;
			if($dep2_cnt > 0) {
				$cate2 = $wpdb->get_results("select * from bbse_commerce_category where depth_1='".$d1->depth_1."' and depth_2>0 and depth_3=0 and c_use='Y' order by c_rank asc");
				$view .= '<ul class="sub-menu">';
				foreach($cate2 as $i2=>$d2) {
					$dep3_cnt = $wpdb->get_var("select count(*) from bbse_commerce_category where depth_1='".$d2->depth_1."' and depth_2='".$d2->depth_2."' and depth_3>0 and c_use='Y'");
					if(wp_is_mobile() && $dep3_cnt > 0) $dep2Arrow="<span data-menu='".$d2->idx."' class='gnb_category_2_span'>▼</span>";
					else $dep2Arrow="";
					$view .= '<li class="menu-item menu-item-type-post_type menu-item-object-page '.(($dep3_cnt>0)?'menu-item-has-children found-sub':'no-sub').' menu-item-'.$d2->idx.'"><a href="'.$linkUrl.'/?bbseCat='.$d2->idx.'">'.$d2->c_name.'</a>'.$dep2Arrow;
					if($dep3_cnt > 0) {
						$view .= '<ul class="sub-menu">';
						$cate3 = $wpdb->get_results("select * from bbse_commerce_category where depth_1='".$d2->depth_1."' and depth_2='".$d2->depth_2."' and depth_3>0 and c_use='Y' order by c_rank asc");
						foreach($cate3 as $i3=>$d3) {
							$view .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-'.$d2->idx.'"><a href="'.$linkUrl.'/?bbseCat='.$d3->idx.'">'.$d3->c_name.'</a></li>';
						}
						$view .= '</ul>';
					}
					$view .= '</li>';
				}
				$view .= '</ul>';
			}else{
				$view .= '<ul class="sub-menu"></ul>';
			}
			$view .= '</li>';
		}
		$view .= '</ul>';
		echo $view;
	}
}


//카테고리 출력
if (!function_exists('goodsCategoryList')) {
	function goodsCategoryList($cate) {
		if(!$cate) return;
		global $wpdb;
		$linkUrl = home_url();
		if(!table_exists('bbse_commerce_category')) {echo "BBS e-Commerce 플러그인을 먼저 설치해주세요.";return;}
		
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
		$currUserID=$current_user->user_login;
		$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$currUserID."'");
		$currUserClass = $member[0]->user_class;
		$role = $current_user->roles[0];
		
		
		$cateChk = $wpdb->get_row("select idx,depth_1,depth_2,depth_3,c_use,c_name from bbse_commerce_category where idx='".$cate."'");
		
		$view = '<div class="article hideWhenMobile">';

		if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 > 0) {//소분류
			$depRes1 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 = '".$cateChk->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
			$dep2 = $wpdb->get_row("select idx,c_name from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2='".$cateChk->depth_2."' and depth_3=0");
			$view .= '<h3 class="lv2_title"><a href="'.$linkUrl.'/?bbseCat='.$dep2->idx.'">'.$dep2->c_name.'</a></h3>';
			if(count($depRes1) == 0) {
				$depRes1 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and c_use='Y' order by c_rank asc");
			}
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 == 0) {//중분류
			$depRes1 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 = '".$cateChk->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
			if(count($depRes1) == 0) {
				$depRes0 = $wpdb->get_row("select idx,c_name from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 = 0 and depth_3 = 0 order by idx desc limit 1");
				$view .= '<h3 class="lv2_title"><a href="'.$linkUrl.'/?bbseCat='.$depRes0->idx.'">'.$depRes0->c_name.'</a></h3>';
				$depRes1 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and c_use='Y' order by c_rank asc");
			}else{
				$view .= '<h3 class="lv2_title"><a href="'.$linkUrl.'/?bbseCat='.$cateChk->idx.'">'.$cateChk->c_name.'</a></h3>';
			}
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 == 0 && $cateChk->depth_3 == 0) {//대분류
			$depRes1 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and c_use='Y' order by c_rank asc");
			$view .= '<h3 class="lv2_title"><a href="'.$linkUrl.'/?bbseCat='.$cateChk->idx.'">'.$cateChk->c_name.'</a></h3>';
		}

		if(count($depRes1) > 0) {
			$view .= '<div class="lp_listing">';
			$view .= '<ul>';
			
			foreach($depRes1 as $i=>$dep1) {
				$goods = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE ".getCategoryQuery($dep1->idx)." AND (goods_display='display' OR goods_display='soldout') "); //총 목록수	
				//$goodsCount = $wpdb->get_var("select count(*) from bbse_commerce_goods where find_in_set('".$dep1->idx."' , replace(goods_cat_list,'|',',')) > 0");
				$goodsCnt = 0;
				if($role == 'administrator'){
				    $goodsCnt=sizeof($goods);
				}else{
    				for($i =0; $i<sizeof($goods); $i++){
    				    $totalGood=unserialize($goods[$i]->goods_member_price);
        			    for($j=0; $j<sizeof($totalGood); $j++){
        			        if($totalGood[goods_member_level][$j] == $currUserClass){
        			            if($totalGood[goods_member_price][$j] != '0'){
        			                $goodsCnt++;
        			            }
        			        }
        			    }
        			}
				}
				$addClass = ($dep1->idx==$cate)?' class="active"':'';
				$view .= '<li'.$addClass.'><a href="'.$linkUrl.'/?bbseCat='.$dep1->idx.'"><strong>'.$dep1->c_name.'</strong> ('.number_format($goodsCnt).')</a></li>';
			}
			$view .= '</ul>';
			$view .= '</div>';	
		}
		$view .= '</div>';

		echo $view;
	}
}

//카테고리2 출력
if (!function_exists('topCategorySelect')) {
	function topCategorySelect($cate) {
		if(!$cate) return;
		global $wpdb;
		$linkUrl = home_url();
		if(!table_exists('bbse_commerce_category')) {echo "BBS e-Commerce 플러그인을 먼저 설치해주세요.";return;}

		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
		$currUserID=$current_user->user_login;
		$member = $wpdb->get_results("SELECT user_class,user_id FROM bbse_commerce_membership where user_id ='".$currUserID."'");
		$currUserClass = $member[0]->user_class;
		
		$view = "";
		$cateChk = $wpdb->get_row("select idx,depth_1,depth_2,depth_3,c_use,c_name from bbse_commerce_category where idx='".$cate."' and user_class= '".$currUserClass."'");
		$depth1 = $wpdb->get_row("select idx,c_name from bbse_commerce_category where c_use='Y' and depth_1='".$cateChk->depth_1."' and depth_2=0 and depth_3=0 and user_class= '".$currUserClass."'");
		$depth2 = $wpdb->get_row("select idx,c_name from bbse_commerce_category where c_use='Y' and depth_1='".$cateChk->depth_1."' and depth_2='".$cateChk->depth_2."' and depth_3=0 and user_class= '".$currUserClass."' ");
		$depth3 = $wpdb->get_row("select idx,c_name from bbse_commerce_category where c_use='Y' and depth_1='".$cateChk->depth_1."' and depth_2='".$cateChk->depth_2."' and depth_3='".$cateChk->depth_3."' and user_class= '".$currUserClass."'");

		if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 > 0) {//소분류
			$depRes1 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1 > 0 and depth_2 = 0 and depth_3 = 0 and c_use='Y' and user_class= '".$currUserClass."' order by c_rank asc");
			$view .= '<li>';
			$view .= '	<a href="'.$linkUrl.'/?bbseCat='.$depth1->idx.'">'.$depth1->c_name.'</a>';
			$view .= '	<div class="sub_ly">';
			$view .= '		<button class="bb_lp"><span>더보기</span></button>';
			$view .= '		<ul>';
			foreach($depRes1 as $i=>$dep1) {
				$view .= '		<li><a href="'.$linkUrl.'/?bbseCat='.$dep1->idx.'">'.$dep1->c_name.'</a></li>';
			}
			$view .= '		</ul>';
			$view .= '	</div>';
			$view .= '</li>';
			$depRes2 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1 = '".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and c_use='Y' and user_class= '".$currUserClass."' order by c_rank asc");
			$view .= '<li>';
			$view .= '	<a href="'.$linkUrl.'/?bbseCat='.$depth2->idx.'">'.$depth2->c_name.'</a>';
			$view .=		'<div class="sub_ly">';
			$view .= '		<button class="bb_lp"><span>더보기</span></button>';
			$view .= '		<ul>';
			foreach($depRes2 as $i=>$dep2) {
				$view .= '		<li><a href="'.$linkUrl.'/?bbseCat='.$dep2->idx.'">'.$dep2->c_name.'</a></li>';
			}
			$view .= '		</ul>';
			$view .= '	</div>';
			$view .= '</li>';

			/*
			$depRes3 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1 = '".$cateChk->depth_1."' and depth_2 = '".$cateChk->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
			$view .= '<li>';
			if(!$cateChk->depth_3) {
				$view .= '	<a href="javascript:void(0);">분류선택</a>';
			}else{
				$view .= '	<a href="'.$linkUrl.'/?bbseCat='.$depth3->idx.'"><strong>'.$depth3->c_name.'</strong></a>';
			}
			$view .=		'<div class="sub_ly">';
			$view .= '		<button class="bb_lp"><span>더보기</span></button>';
			$view .= '		<ul>';
			$view .= '			<li><a href="javascript:void(0);">분류선택</a></li>';
			foreach($depRes3 as $i=>$dep3) {
				$view .= '		<li><a href="'.$linkUrl.'/?bbseCat='.$dep3->idx.'">'.$dep3->c_name.'</a></li>';
			}
			$view .= '		</ul>';
			$view .= '	</div>';
			$view .= '</li>';
			*/

			$view .= '<li><strong>'.$depth3->c_name.'</strong></li>';
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 == 0) {//중분류
			$depRes1 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1 > 0 and depth_2 = 0 and depth_3 = 0 and c_use='Y' and user_class= '".$currUserClass."' order by c_rank asc");
			$view .= '<li>';
			$view .= '	<a href="'.$linkUrl.'/?bbseCat='.$depth1->idx.'">'.$depth1->c_name.'</a>';
			$view .= '	<div class="sub_ly">';
			$view .= '		<button class="bb_lp"><span>더보기</span></button>';
			$view .= '		<ul>';
			foreach($depRes1 as $i=>$dep1) {
				$view .= '		<li><a href="'.$linkUrl.'/?bbseCat='.$dep1->idx.'">'.$dep1->c_name.'</a></li>';
			}
			$view .= '		</ul>';
			$view .= '	</div>';
			$view .= '</li>';
			
			$depRes2 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1 = '".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and user_class= '".$currUserClass."' and c_use='Y' order by c_rank asc");

			$dep3Cnt = $wpdb->get_var("select count(*) from bbse_commerce_category where depth_1 = '".$cateChk->depth_1."' and depth_2 = '".$cateChk->depth_2."' and depth_3 > 0 and user_class= '".$currUserClass."' and c_use='Y' order by c_rank asc");
			if($dep3Cnt > 0) {
				$view .= '<li>';
				$view .= '	<a href="'.$linkUrl.'/?bbseCat='.$depth2->idx.'">'.$depth2->c_name.'</a>';
				$view .= '	<div class="sub_ly">';
				$view .= '		<button class="bb_lp"><span>더보기</span></button>';
				$view .= '		<ul>';
				//$view .= '			<li><a href="javascript:void(0);">분류선택</a></li>';
				foreach($depRes2 as $i=>$dep2) {
					$view .= '		<li><a href="'.$linkUrl.'/?bbseCat='.$dep2->idx.'">'.$dep2->c_name.'</a></li>';
				}
				$view .= '		</ul>';
				$view .= '	</div>';
				$view .= '</li>';
			}else{
				$view .= '<li><strong>'.$cateChk->c_name.'</strong></li>';
			}
			
			/*
			$depRes3 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1 = '".$cateChk->depth_1."' and depth_2 = '".$cateChk->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
			$view .= '<li>';
			if(!$cateChk->depth_3) {
				$view .= '	<a href="javascript:void(0);">분류선택</a>';
			}else{
				$view .= '	<a href="'.$linkUrl.'/?bbseCat='.$depth3->idx.'"><strong>'.$depth3->c_name.'</strong></a>';
			}
			$view .=		'<div class="sub_ly">';
			$view .= '		<button class="bb_lp"><span>더보기</span></button>';
			$view .= '		<ul>';
			$view .= '			<li><a href="javascript:void(0);">분류선택</a></li>';
			foreach($depRes3 as $i=>$dep3) {
				$view .= '		<li><a href="'.$linkUrl.'/?bbseCat='.$dep3->idx.'">'.$dep3->c_name.'</a></li>';
			}
			$view .= '		</ul>';
			$view .= '	</div>';
			$view .= '</li>';
			*/
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 == 0 && $cateChk->depth_3 == 0) {//대분류
		    $depRes1 = $wpdb->get_results("select * from bbse_commerce_category where depth_1 > 0 and depth_2 = 0 and depth_3 = 0 and c_use='Y'  and user_class= '".$currUserClass."' order by c_rank asc");
			$view .= '<li>';
			$view .= '	<a href="'.$linkUrl.'/?bbseCat='.$depth1->idx.'">'.$depth1->c_name.'</a>';
			$view .= '	<div class="sub_ly">';
			$view .= '		<button class="bb_lp"><span>더보기</span></button>';
			$view .= '		<ul>';
			//$view .= '			<li><a href="javascript:void(0);">분류선택</a></li>';
			foreach($depRes1 as $i=>$dep1) {
				$view .=			'<li><a href="'.$linkUrl.'/?bbseCat='.$dep1->idx.'">'.$dep1->c_name.'</a></li>';
			}
			$view .= '		</ul>';
			$view .= '	</div>';
			$view .= '</li>';
			/*
			$depRes2 = $wpdb->get_results("select idx,c_name from bbse_commerce_category where depth_1 = '".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and c_use='Y' order by c_rank asc");
			$view .= '<li>';
			$view .= '	<a href="javascript:void(0);">분류선택</a>';
			$view .= '	<div class="sub_ly">';
			$view .= '		<button class="bb_lp"><span>더보기</span></button>';
			$view .= '		<ul>';
			$view .= '			<li><a href="javascript:void(0);">분류선택</a></li>';
			foreach($depRes2 as $i=>$dep2) {
				$view .= '		<li><a href="'.$linkUrl.'/?bbseCat='.$dep2->idx.'">'.$dep2->c_name.'</a></li>';
			}
			$view .= '		</ul>';
			$view .= '	</div>';
			$view .= '</li>';
			*/
		}
	
		echo $view;
	}
}
if (!function_exists('mobileCategorySelect')) {
	function mobileCategorySelect($cate="", $href=true) {
		//if(!$cate) return;
		global $wpdb;
		$linkUrl = home_url();
		if(!table_exists('bbse_commerce_category')) {echo "BBS e-Commerce 플러그인을 먼저 설치해주세요.";return;}

		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
		$currUserID=$current_user->user_login;
		$role = $current_user->roles[0];
		$member = $wpdb->get_results("SELECT user_class,user_id FROM bbse_commerce_membership where user_id='".$currUserID."'");
		$currUserClass = $member[0]->user_class;
		
		if($role == 'administrator'){
		    $cat_query = $wpdb->get_results("SELECT * FROM `bbse_commerce_category` WHERE `idx`>'1' AND c_use='Y' ORDER BY `c_rank` ASC", ARRAY_A);
		}
		else{
            $cat_query = $wpdb->get_results("SELECT * FROM `bbse_commerce_category` WHERE `idx`>'1' AND c_use='Y' and user_class = '".$currUserClass."' ORDER BY `c_rank` ASC", ARRAY_A);
		}
		if($href) {
			echo "<select name=\"s_category\" id=\"s_category\" onchange=\"location.href='".home_url()."/?bbseCat='+this.value;\">";
		}else{
			echo "<select name=\"s_category\" id=\"s_category\">";
			echo "<option value=\"\">카테고리 선택</option>";
		}
		foreach($cat_query as $c_row){
			if($c_row['depth_3']>'0'){
				if($cate==$c_row['idx']) $cSelected=" selected='selected'";
				else $cSelected="";
				echo "<option value='".$c_row['idx']."'".$cSelected.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ".$c_row['c_name']."</option>";
			}
			else if($c_row['depth_2']>'0'){
				if($cate==$c_row['idx']) $cSelected=" selected='selected'";
				else $cSelected="";
				echo "<option value='".$c_row['idx']."'".$cSelected.">&nbsp;&nbsp;- ".$c_row['c_name']."</option>";
			}
			else if($c_row['depth_1']>'0'){
				if($cate==$c_row['idx']) $cSelected=" selected='selected'";
				else $cSelected="";
				echo "<option value='".$c_row['idx']."'".$cSelected.">".$c_row['c_name']."</option>";
			}
		}
		echo "</select>";
	}
}
if (!function_exists('getCategoryQuery')) {
	function getCategoryQuery($cate) {
		if(!$cate) return;
		global $wpdb;
		$linkUrl = home_url();
		if(!table_exists('bbse_commerce_category')) {echo "BBS e-Commerce 플러그인을 먼저 설치해주세요.";return;}
		
		$cateChk = $wpdb->get_row("select idx,depth_1,depth_2,depth_3,c_use from bbse_commerce_category where idx='".$cate."'");
		$depList = array();
		if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 > 0) {//소분류
			$depList[] = "(find_in_set( '".$cateChk->idx."' , replace(goods_cat_list,'|',',')) > 0)";
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 == 0) {//중분류
			$depList[] = "(find_in_set( '".$cate."' , replace(goods_cat_list,'|',',')) > 0)";
			$depRes2 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 = '".$cateChk->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
			foreach($depRes2 as $i=>$dep2) {
				$depList[] = "(find_in_set( '".$dep2->idx."' , replace(goods_cat_list,'|',',')) > 0)";
				$depRes3 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2='".$dep2->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
				foreach($depRes3 as $j=>$dep3) {
					$depList[] = "(find_in_set( '".$dep3->idx."' , replace(goods_cat_list,'|',',')) > 0)";
				}
			}
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 == 0 && $cateChk->depth_3 == 0) {//대분류
			$depList[] = "(find_in_set( '".$cate."' , replace(goods_cat_list,'|',',')) > 0)";
			$depRes2 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and c_use='Y' order by c_rank asc");
			foreach($depRes2 as $i=>$dep2) {
				$depList[] = "(find_in_set( '".$dep2->idx."' , replace(goods_cat_list,'|',',')) > 0)";
				$depRes3 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2='".$dep2->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
				foreach($depRes3 as $j=>$dep3) {
					$depList[] = "(find_in_set( '".$dep3->idx."' , replace(goods_cat_list,'|',',')) > 0)";
				}
			}
		}
		return " ( ".implode(" or ", $depList)." ) ";
	}
}

// 메인메뉴 워커
class bbse_commerce_walker_main_nav extends Walker_Nav_Menu {
  // 하위 깊이를 위한 class를 추가합니다.
  var $found_parents = array();
  function start_el(&$output, $item, $depth=0, $args=Array(), $id = 0){

	global $theme_shortname, $wp_query;

	$parent_item_id = 0;
	$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	$class_names = $value = '';
	$classes = empty( $item->classes ) ? array() : (array) $item->classes;  
	$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
	$class_names = esc_attr( $class_names );

	if(strpos($class_names, 'current-menu-item') || strpos($class_names, 'current-menu-parent') || strpos($class_names, 'current-menu-ancestor') || (is_array($this->found_parents) && in_array( $item->menu_item_parent, $this->found_parents ))) $class_names = esc_attr( $class_names ) . ' active';
	else $class_names = esc_attr( $class_names );

	$class_names=str_replace("&quot;","",$class_names);
	$output .= $indent . '<li class="'.$class_names.'">';
	$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
	$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
	$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
	$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

	$item_output = $args->before;
	$item_output .= '<a'. $attributes .'>';
	$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
	$item_output .= '</a>';
	if(strpos($class_names, 'menu-item-has-children')) $item_output .= '<button class="open-children"><em>하위메뉴 열기</em></button>';
	$item_output .= $args->after;

	$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

  function end_el(&$output, $item, $depth=0, $args = Array()){
	$output .= "</li>\n";
  }

  function start_lvl(&$output, $depth = 0, $item = Array()) {
    // 깊이에 따른 class들
    $display_depth = ( $depth + 1);
    $classes = array(
      'sub-menu',
      ( $display_depth %  2 ? 'menu-odd'     : 'menu-even' ),
      ( $display_depth >= 2 ? 'sub-sub-menu' : '' ),
      'menu-depth-' . $display_depth
    );
    $class_names = implode( ' ', $classes );

    // HTML만들기 //두번째 깊이에만 추가로 엘리먼트를 추가합니다.
    if ($display_depth != 1) $output .= PHP_EOL.'<ul class="'.$class_names.'">'.PHP_EOL;
    else                     $output .= PHP_EOL.'<div class="menu-depth-'.$display_depth.'">'.PHP_EOL."\n".'<ul class="' . $class_names . '">'.PHP_EOL;
  }
  // 수동으로 추가한 엘리먼트를 닫습니다.
	function end_lvl(&$output, $depth = 0, $args = Array()){
    $display_depth = ( $depth + 1);
    //두번째 깊이에만
    if ($display_depth == 1) $output .= '</ul>'.PHP_EOL.'</div>'.PHP_EOL."\n";
    else $output .= '</ul>'.PHP_EOL;
	}
}



// 목록/검색 형식(archive)의 페이지에서 해당 제목을 출력합니다.
if(!function_exists('bbse_page_type_title')) {
	function bbse_page_type_title() {
    if (is_tag())    {echo"태그 : ";   single_tag_title();}
    if (is_day())    {echo"작성일 : "; the_time('Y년 F j일');}
    if (is_month())  {echo"작성일 : "; the_time('Y년 F');}
    if (is_year())   {echo"작성일 : "; the_time('Y년');}
    if (is_author()) {echo"작성자 : "; echo get_author_name();}
    //if (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "Blog Archives";}
    if (is_search() && $_GET['s']) {echo"검색어 : "; echo $_GET['s'];}
	}
}

//아카이브 결과 없음을 표시합니다.
if(!function_exists('bbse_the_no_result')) {
	function bbse_the_no_result() {
    if (is_tag())    {echo '<em>'.single_tag_title().'</em>'.PHP_EOL.'태그의 글이 없습니다.';}
    if (is_day())    {echo '<em>'.the_time('Y년 F j일').'</em>'.PHP_EOL.' 작성일의 글이 없습니다.';}
    if (is_month())  {echo '<em>'.the_time('Y년 F').'</em>'.PHP_EOL.' 작성일의 글이 없습니다.';}
    if (is_year())   {echo '<em>'.the_time('Y년').'</em>'.PHP_EOL.' 작성일의 글이 없습니다.';}
    if (is_author()) {echo '<em>'.get_author_name().'</em>'.PHP_EOL.' 작성자 글이 없습니다.';}
    //if (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "Blog Archives";}
    if (is_search() && $_GET['s']) {echo '<em>'.$_GET['s'].'</em>'.PHP_EOL.'검색결과가 없습니다.';}
	}
}

// 포스트에서 첫이미지를 가져옵니다.
if (!function_exists('bbse_post_first_image')) {
	function bbse_post_first_image() {
		global $post, $posts;
		$first_img = '';
		ob_start();
		ob_end_clean();
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if (empty($matches[1][0])) $first_img = "/images/default.jpg";
		else $first_img = $matches[1][0];

		return $first_img;
	}
}

// 페이지에서도 요약문을 입력 할 수 있도록 설정
add_post_type_support('page', 'excerpt');

// 말줄임 삭제
function bbse_excerpt_more(){
  return '';
}
// 요약문 길이 설정
function bbse_excerpt_length_500() {
	return 500;
}
// 요약문 자르기, 멀티 바이트로만 계산
function bbse_get_excerpt( $length ) {
  add_filter('excerpt_more', 'bbse_excerpt_more');
  add_filter('excerpt_length', 'bbse_excerpt_length_500', 999 );
  $content  = get_the_excerpt();
  if ( mb_strlen($content) > $length )
    return mb_substr($content,0,$length).'...';
  else
    return $content;
}
function bbse_the_excerpt( $length ) {
   echo bbse_get_excerpt( $length );
}

// 요약문 자르기, 게시판용
function bbse_get_excerpt_board( $string, $length ) {
  $content  = strip_tags($string);
  if ( mb_strlen($content) > $length )
    return mb_substr($content,0,$length).'...';
  else
    return $content;
}


//코멘트 출력함수
function bbse_list_comments_callback($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
  if ($depth>1) { ?>
  <li class="comment depth-<?php echo $depth?>" id="comment-<?php comment_ID()?>" >
  <?php } else { ?>
  <li class="comment depth-<?php echo $depth?> parent" id="comment-<?php comment_ID()?>" >
  <?php } ?>
  <div id="div-comment-<?php comment_ID()?>" class="comment-body">
    <div class="comment-author vcard clearfix">
      <div class="avatar-box">
        <?php echo get_avatar( $comment, $args['avatar_size'] ); ?><span class="avatar-overlay"></span>
      </div> <!-- end .avatar-box -->
      <div class="comment-wrap">
        <div class="comment-meta commentmetadata">
          <span class="fn"><?php //comment_author_link() ?><?php comment_author()?></span>&nbsp;&nbsp;<time class="comment-date" pubdate="pubdate" datetime="<?php the_time('Y-m-d')?>"><?php comment_date('Y/m/d') ?>&nbsp;&nbsp;<?php comment_time('H:i') ?>   </time>
        </div>
        <div class="comment-content"><?php comment_text() ?></div> <!-- end comment-content-->
        <div class="buttonBox">
          <?php comment_reply_link(array_merge( $args, array('reply_text' => $args['reply_text'],'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
          <?php //bbse_the_comment_delete_btn($comment)?>
          <?php //edit_comment_link('수정', '', ''); ?>
        </div>
      </div> <!-- end comment-wrap -->
      <div class="comment-arrow"></div>
    </div> <!-- end clearfix-->
  </div> <!-- end comment-body-->
  <?php
}
//코멘트 삭제버튼
function bbse_the_comment_delete_btn($comment){
  if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
    $url = clean_url(wp_nonce_url( "/wp-admin/comment.php?action=deletecomment&p=$comment->comment_post_ID&c=$comment->comment_ID", "delete-comment_$comment->comment_ID" ));
    echo "<a href='$url' class='delete:the-comment-list:comment-$comment->comment_ID delete'>삭제</a> ";
  }
}

// 아바타 ALT, TITLE tag 삽입
if (!function_exists('bbse_replace_avatar_alt')) {
	function bbse_replace_avatar_alt($text){
		$text = str_replace('alt=\'\'', 'alt=\'Avatar\' title=\'Gravata\'',$text);
		return $text;
	}
}
add_filter('get_avatar','bbse_replace_avatar_alt');

//코멘트 워커입니다.
class bbse_walker_comments_list extends Walker_Comment {
  // 하위 깊이를 위한 class를 추가합니다.
  function start_lvl(&$output, $depth = 0, $item = Array()) {
    // 깊이에 따른 class들
    $display_depth = ( $depth + 1);
    $classes = array(
      ( $display_depth %  2 ? 'comment-odd'     : 'comment-even' ),
      'comment-depth-' . $display_depth
    );
    $class_names = implode( ' ', $classes );

    // HTML만들기 //두번째 깊이에만 추가로 엘리먼트를 추가합니다.
    $output .= PHP_EOL.'<div class="comment depth-'.$display_depth.' children">'.PHP_EOL.'<span class="design-element"></span>'."\n".'<ol class="' . $class_names . '">'.PHP_EOL;
  }
  // 수동으로 추가한 엘리먼트를 닫습니다.
	function end_lvl(&$output, $depth = 0, $args = Array()){
    $display_depth = ( $depth + 1);
    //두번째 깊이에만
    if ($display_depth == 1) $output .= '</ol>'.PHP_EOL.'</div>'.PHP_EOL;
    else                     $output .= '</ol>'.PHP_EOL;
	}
}



// 페이지 페이징
if ( ! function_exists( 'bbse_page_nav' ) )
{
  function bbse_page_nav() {
    if ( $GLOBALS['wp_query']->max_num_pages < 2 ) return;

    $current    = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
    $link       = html_entity_decode(get_pagenum_link());
    $total      = $GLOBALS['wp_query']->max_num_pages;
    $query_args = array();
    $urlQuery   = explode( '?', $link );


    if ( isset( $urlQuery[1] ) ) {
      wp_parse_str( $urlQuery[1], $query_args );
    }

    $base = trailingslashit(remove_query_arg(array_keys($query_args),$link)).'%_%';

    $format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $base, 'index.php' ) ? 'index.php/' : '';
    $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

    $markup = paginate_links( array(
      'current'   => $current,
      'base'      => $base,
      'total'     => $total,
      'format'    => $format,
      'mid_size'  => 8,
      'add_args'  => array_map( 'urlencode', $query_args ),
      'prev_next' => True,
      'prev_text' => '&lt;',
      'next_text' => '&gt;',
    ) );

    if ( $markup ){
    ?>
     <div class="navigation paging-navigation">
      <h1 class="page-navigation-toggle">Page navigation</h1>
      <span class="pageCount">Page <b><?php echo $current;?></b> of <?php echo $total?></span>
      <div class="pagination loop-pagination">
        <a class="page-numbers firstPage" href="<?php echo html_entity_decode( get_pagenum_link() )?>" >&lt;&lt;</a>
        <?php echo $markup; ?>
        <a class="page-numbers lastPage" href="<?php echo html_entity_decode( get_pagenum_link() )?>&amp;paged=<?php echo $total?>" >&gt;&gt;</a>
      </div>
    </div>
     <?php
    }
  }
}


// 로케이션을 위한 빵조각 흘리기 원형
if(!function_exists('the_breadcrumb')) {
	function the_breadcrumb() {
		echo '<ul id="crumbs">';
		if (!is_home()) {
			if (is_category() || is_single()) {
				echo '<li>';
				the_category(' </li><li> ');
				if (is_single()) {
						echo "</li><li>";
						the_title();
						echo '</li>';
				}
			} elseif (is_page()) {
				echo '<li>';
				echo the_title();
				echo '</li>';
			}
		}
    if (is_tag())    {single_tag_title();}
		if (is_day())    {echo"<li>Archive for "; the_time('Y년 F j일'); echo'</li>';}
		if (is_month())  {echo"<li>Archive for "; the_time('Y년 F'); echo'</li>';}
		if (is_year())   {echo"<li>Archive for "; the_time('Y년'); echo'</li>';}
		if (is_author()) {echo"<li>Author Archive"; echo'</li>';}
		if (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
		if (is_search()) {echo"<li>Search Results"; echo'</li>';}
		echo '</ul>';
	}
}

// 로케이션을 위한 빵조각 흘리기
if(!function_exists('bbse_the_breadcrumb')) {
	function bbse_the_breadcrumb() {
		echo '<ul id="crumbs">';
		if (!is_home()) {
			echo '<li><a href="';
			echo get_option('home');
			echo '">';
			echo 'Home'; /* 1 */
			echo "</a></li>";
			if (is_category() || is_single()) {
				echo '<li>';
				the_category(', ');
				if (is_single()) {
						echo "</li><li>";
						the_title();
						echo '</li>';
				}
			} elseif (is_page()) {
				echo '<li>';
				echo the_title();
				echo '</li>';
			}
		}
		elseif (is_tag()) {single_tag_title();}
		elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';} /* 2 */
		elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
		elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
		elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
		elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
		elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
		echo '</ul>';
	}
}

//네이버 지도 (신규 버전)
// 주소 좌표 가져오기
if (!function_exists('get_navermap_geocode')) {
	function get_navermap_geocode($p_str_addr="", $mapVer, $showerror){
		global $theme_shortname, $wp_version;
		$errorCodes = array(
		  '401'=>'401(인증 실패)',
		  '404'=>'404(API 없음)',
		  '403'=>'403(지원하지 않는 프로토콜)',
		  '405'=>'405(메서드 허용 안함)',
		  '400'=>'400(요청 변수 확인)',
		  '500'=>'500(서버 오류)'
		);

		$openC  = '<!-- ';
		$closeC = ' -->';
		if ($showerror == 'y'){
			$openC  = '';
			$closeC = '';
		}

		$str_addr   = str_replace(" ", "", $p_str_addr);

		if ( $mapVer === 'new' ){
			  $dest_url = "https://openapi.naver.com/v1/map/geocode?encoding=utf-8&coord=latlng&output=json&query=".rawurlencode($str_addr);
			  $header   = array(
				'X-Naver-Client-Id: '.get_option($theme_shortname.'_map_client_id'),
				'X-Naver-Client-Secret: '.get_option($theme_shortname.'_map_client_secret')
			  );

			  $args     = array(
				'Accept'      => '*/*',
				'Host'        => 'openapi.naver.com',
				'user-agent'  => 'WordPress/' . $wp_version. '; '.home_url(),
				'headers'     => array(
									'Content-Type'          => 'application/json',
									'X-Naver-Client-Id'     => get_option($theme_shortname.'_map_client_id'),
									'X-Naver-Client-Secret' => get_option($theme_shortname.'_map_client_secret'),
								  ),
			  );
		}
		else if( $mapVer === 'old' ){
			$apiKey   = get_option($theme_shortname.'_map_key')?get_option($theme_shortname.'_map_key'):get_option($theme_shortname.'_map_key');
			$dest_url = "http://openapi.map.naver.com/api/geocode.php?key=".$apiKey."&encoding=utf-8&coord=LatLng&query=".rawurlencode($str_addr);
			$header   = array();
			$args     = array();
		}

		//by cURL
		if (function_exists('curl_init')){
		  $ch = curl_init();
		  curl_setopt($ch, CURLOPT_URL,            $dest_url);
		  curl_setopt($ch, CURLOPT_POST,           false);
		  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		  curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
		  $return_body = curl_exec ($ch);
		  $curlinfo    = curl_getinfo($ch);
		  curl_close($ch);

		  $return_code = $curlinfo['http_code'];

		// by wp_remote_get
		}
		else {
		  $return_data = wp_remote_get($dest_url, $args);
		  $return_body = wp_remote_retrieve_body($return_data);
		  $return_code = wp_remote_retrieve_response_code($return_data);
		}

		if ($return_code == 200){
			$obj_json = json_decode($return_body)->result->items[0];
			return array($obj_json->point->x, $obj_json->point->y);
		} else {
		  echo $openC.'네이버지도 오류 : '.$errorCodes[$return_code].'<br>'.$closeC;
		  return false;
		}
	}
}

// 네이버맵을 그립니다. MapAPI 2.0 (2016/02 newer api)
if (!function_exists('bbse_draw_naver_map')){
  function bbse_draw_naver_map($vars=array(), $useDirect=false){
    global $theme_shortname;

		//기본값 설정
		if ( empty($vars['level']) )	$vars['level']  = '12';
		if ( empty($vars['width']) )	$vars['width']  = '100%';
		if ( empty($vars['height']) ) $vars['height'] = '40vh';
		if ( $vars['showerror'] == 'y' ){
			$vars['openC']  = '';
			$vars['closeC'] = '';
		}else{
			$vars['openC'] = '<!-- ';
			$vars['closeC'] = ' -->';
		}

		//지도사용 버전 확인
		$mapVer = false;
		if ( get_option($theme_shortname.'_map_client_id') && get_option($theme_shortname.'_map_client_secret') ) $mapVer = 'new';
		else if ( get_option($theme_shortname.'_map_key') || get_option($theme_shortname.'_map_key') ){
			$mapVer = 'old';
			$vars['height'] = '60vh';
		}
		else{
			echo $vars['openC'].'네이버지도 오류 : API 설정안됨<br>'.$vars['closeC'];
			return false;
		}
	
		//주소확인
		if (get_option($theme_shortname.'_map_addr')) $str_addr = get_option($theme_shortname.'_map_addr');
		else{
			echo $vars['openC'].'네이버지도 오류 : 주소 설정안됨<br>'.$vars['closeC'];
			return false;
		}

		//좌표확인
		$geocodex==false;
		$geocodey= false;

		list($geocodex, $geocodey) = get_navermap_geocode( $str_addr, $mapVer, $vars['showerror'] );
		if ($geocodex == false || $geocodey == false){
			echo $vars['openC'].'네이버지도 오류 : 주소변환 오류<br>'.$vars['closeC'];
			return false;
		}

		//마커내용
		$mkr_name  = get_option($theme_shortname.'_map_mkr_name')?get_option($theme_shortname.'_map_mkr_name'):get_bloginfo('name');
		
		list($usec, $sec) = explode(" ", microtime());
		$uID = ($usec+$usec)*1000000;

		//지도 마크업 생성
		$mapHtml  = '';
		$mapHtml .= '<!-- 네이버지도 시작 -->';
		$mapHtml .= '<div class="naverMapWrap'.$uID.'">'.PHP_EOL;
		$mapHtml .= '	<div id="naver-map'.$uID.'" style="width:'.$vars['width'].';height:'.$vars['height'].'"></div>'.PHP_EOL;
		
		//버전에 따른 API 구분 로딩
		if ( $mapVer === 'new' ){
			$mapHtml .= '<script type="text/javascript" src="http://openapi.map.naver.com/openapi/v2/maps.js?clientId='.get_option($theme_shortname.'_map_client_id').'"></script>'.PHP_EOL;
		} else if ( $mapVer === 'old' ){
			$apiKey   = get_option($theme_shortname.'_map_key')?get_option($theme_shortname.'_map_key'):get_option($theme_shortname.'_map_key');
			$mapHtml .= '<script type="text/javascript" src="http://openapi.map.naver.com/openapi/naverMap.naver?ver=2.0&key='.$apiKey.'"></script>'.PHP_EOL;
		}
		
		//지도스크립트
		$mapHtml .= '<script>'.PHP_EOL;
		$mapHtml .= '  var oPoint = new nhn.api.map.LatLng('.$geocodey.', '.$geocodex.');'.PHP_EOL;
		$mapHtml .= '  nhn.api.map.setDefaultPoint(\'LatLng\');'.PHP_EOL;
		$mapHtml .= '  var markerCount = 0;'.PHP_EOL;
		$mapHtml .= '  oMap = new nhn.api.map.Map(\'naver-map'.$uID.'\', {'.PHP_EOL;
		$mapHtml .= '    point              : oPoint,'.PHP_EOL;
		$mapHtml .= '    zoom               : '.$vars['level'].','.PHP_EOL;
		$mapHtml .= '    enableWheelZoom    : true,'.PHP_EOL;
		$mapHtml .= '    enableDragPan      : true,'.PHP_EOL;
		$mapHtml .= '    enableDblClickZoom : false,'.PHP_EOL;
		$mapHtml .= '    mapMode            : 0,'.PHP_EOL;
		$mapHtml .= '    activateTrafficMap : false,'.PHP_EOL;
		$mapHtml .= '    activateBicycleMap : false,'.PHP_EOL;
		$mapHtml .= '    minMaxLevel        : [ 1, 14 ]'.PHP_EOL;
		$mapHtml .= '  });'.PHP_EOL;
	if ($useDirect == false ){
		$mapHtml .= '  mapTypeChangeButton = new nhn.api.map.MapTypeBtn();'.PHP_EOL;
		$mapHtml .= '  var mapZoom         = new nhn.api.map.ZoomControl();'.PHP_EOL;
		$mapHtml .= '  mapZoom.setPosition({left:10, top:10});'.PHP_EOL;
		$mapHtml .= '  oMap.addControl(mapZoom);'.PHP_EOL;
		$mapHtml .= '  mapTypeChangeButton.setPosition({top:10, left:50});'.PHP_EOL;
		$mapHtml .= '  oMap.addControl(mapTypeChangeButton);'.PHP_EOL;
	}
		$mapHtml .= '  var oSize   = new nhn.api.map.Size(28, 37);'.PHP_EOL;
		$mapHtml .= '  var oOffset = new nhn.api.map.Size(14, 37);'.PHP_EOL;
		$mapHtml .= '  var oIcon   = new nhn.api.map.Icon(\'http://static.naver.com/maps2/icons/pin_spot2.png\', oSize, oOffset);'.PHP_EOL;
		$mapHtml .= '  var oMarker = new nhn.api.map.Marker(oIcon, { title : \''.$mkr_name.'\' });'.PHP_EOL;
		$mapHtml .= '  oMarker.setPoint(oPoint);'.PHP_EOL;
		$mapHtml .= '  oMap.addOverlay(oMarker);'.PHP_EOL;
		$mapHtml .= '  var oLabel = new nhn.api.map.MarkerLabel();'.PHP_EOL;
		$mapHtml .= '  oMap.addOverlay(oLabel);'.PHP_EOL;
		$mapHtml .= '  oLabel.setVisible(true, oMarker); // 마커 라벨 보이기'.PHP_EOL;
		$mapHtml .= '</script>'.PHP_EOL;

		//지도 하단 내용
		$mapHtml  .= '</div>';
		$mapHtml  .= '<!-- //네이버지도 마침 -->';

		//출력
		echo $mapHtml;
		unset($mapHtml);
		return false;
  }
}
//네이버맵 숏코드를 사용합니다.
add_shortcode('naver_maps', 'bbse_draw_naver_map');

//다음지도
if (!function_exists('bbse_draw_daum_map')){
  function bbse_draw_daum_map($vars=array(), $useDirect=false){
		global $theme_shortname;
		
		//기본값확인/설정
		if ( empty($vars['level']) )	$vars['level']  = '3';
		if ( empty($vars['width']) )	$vars['width']  = '100%';
		if ( empty($vars['height']) ) $vars['height'] = '40vh';
        if ( empty($vars['showerror']) ) $vars['showerror'] = 'n';

		if ( $vars['showerror'] == 'y' ){
			$vars['openC']  = '';
			$vars['closeC'] = '';
		}else{
			$vars['openC'] = '<!-- ';
			$vars['closeC'] = ' -->';
		}

		//API 키 확인
        if (get_option($theme_shortname.'_map_daum_appkey') || get_option($theme_shortname.'_map_daum_key')){
            if (get_option($theme_shortname.'_map_daum_appkey')){
                $map_key  = get_option($theme_shortname.'_map_daum_appkey');
                $key_type = 'app';
            }else {
                $map_key  = get_option($theme_shortname.'_map_daum_key');
                $key_type = 'api';
            }
		}
		else{
			echo $vars['openC'].'다음맵 오류 : API 설정안됨<br>'.$vars['closeC'];
			return false;
		}

		//주소확인
		if (get_option($theme_shortname.'_map_daum_addr')) $str_addr = get_option($theme_shortname.'_map_daum_addr');
		else{
			echo $vars['openC'].'다음맵 오류 : 주소 설정안됨<br>'.$vars['closeC'];
			return false;
		}

		//마커내용
		$mkr_name  = get_option($theme_shortname.'_map_daum_mkr_name')?get_option($theme_shortname.'_map_daum_mkr_name'):get_bloginfo('name');
		$marker  = false;
		$marker .= '<div style="padding:3px 5px 5px;line-height:inherit;text-align:center;font-weight:bold;font-size:12px;box-sizing:border-box">';
		$marker .= $mkr_name;
		$marker .= '</div><br>';
		list($usec, $sec) = explode(" ", microtime());
		$uID = ($usec+$usec)*1000000;

        //지도 마크업 생성
        $mapHtml  = '';
        $mapHtml .= '<!-- 다음맵 시작 -->'.PHP_EOL;
        $mapHtml .= '<div class="daumMapWrap'.$uID.'">'.PHP_EOL;
        if($useDirect == false){
        $mapHtml .= '   <style scoped>'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' {position:relative;overflow:hidden;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .radius_border{border:1px solid #919191;border-radius:5px;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol {position:absolute;top:10px;right:10px;overflow:hidden;width:132px;height:30px;margin:0;padding:0;z-index:3;font-size:12px;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol span {display:block;width:65px;height:30px;line-height:30px !important;float:left;text-align:center;line-height:30px;cursor:pointer;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .btn {background:#fff;background:linear-gradient(#fff,  #e6e6e6);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .btn:hover {background:#f5f5f5;background:linear-gradient(#f5f5f5,#e3e3e3);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .btn:active {background:#e6e6e6;background:linear-gradient(#e6e6e6, #fff);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .selected_btn {color:#fff !important;background:#425470;background:linear-gradient(#425470, #5b6d8a);}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_typecontrol .selected_btn:hover {color:#fff;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol {position:absolute;top:50px;right:10px;width:36px;height:80px;overflow:hidden;z-index:3;background-color:#f5f5f5;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol span {display:block;width:36px;height:40px;text-align:center;cursor:pointer;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol span img {width:15px;height:15px;padding:12px 0;border:none;}'.PHP_EOL;
        $mapHtml .= '       .daumMapWrap'.$uID.' .custom_zoomcontrol span:first-child{border-bottom:1px solid #bfbfbf;}'.PHP_EOL;
        $mapHtml .= '   </style>'.PHP_EOL;
        $mapHtml .= '   <div class="custom_typecontrol radius_border">'.PHP_EOL;
        $mapHtml .= '       <span id="btnRoadmap" class="toggleType selected_btn" data-maptype="roadmap">지도</span>'.PHP_EOL;
        $mapHtml .= '       <span id="btnSkyview" class="toggleType btn" data-maptype="skyview">스카이뷰</span>'.PHP_EOL;
        $mapHtml .= '   </div>'.PHP_EOL;
        $mapHtml .= '   <div class="custom_zoomcontrol radius_border"> '.PHP_EOL;
        $mapHtml .= '       <span class="zoomIn"><img src="http://i1.daumcdn.net/localimg/localimages/07/mapapidoc/ico_plus.png" alt="확대"></span> '.PHP_EOL;
        $mapHtml .= '       <span class="zoomOut"><img src="http://i1.daumcdn.net/localimg/localimages/07/mapapidoc/ico_minus.png" alt="축소"></span>'.PHP_EOL;
        $mapHtml .= '   </div>'.PHP_EOL;
        }
        $mapHtml .= '   <div id="daum-map'.$uID.'" style="'.$vars['width'].';height:'.$vars['height'].'"></div>'.PHP_EOL;
        if ($key_type == 'app'){
        $mapHtml .= '   <script src="//dapi.kakao.com/v2/maps/sdk.js?appkey='.$map_key.'&libraries=services"></script>'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '   <script src="//apis.daum.net/maps/maps3.js?apikey='.$map_key.'&libraries=services"></script>'.PHP_EOL;
        }
        $mapHtml .= '   <script>'.PHP_EOL;
        $mapHtml .= '   var geocoder = new daum.maps.services.Geocoder();'.PHP_EOL;
        $mapHtml .= '   var callback = function(result, status) {'.PHP_EOL;
        $mapHtml .= '           var mapContainer = document.getElementById(\'daum-map'.$uID.'\');'.PHP_EOL;
        if ($key_type == 'app'){
        $mapHtml .= '           var coords       = new daum.maps.LatLng(result[0].y, result[0].x);'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '           var coords       = new daum.maps.LatLng(result.addr[0].lat, result.addr[0].lng);'.PHP_EOL;
        }
        $mapHtml .= '           var mapOption = {'.PHP_EOL;
        if ($key_type == 'app'){
        $mapHtml .= '               center: new daum.maps.LatLng(result[0].y, result[0].x),'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '               center: new daum.maps.LatLng(result.addr[0].lat, result.addr[0].lng),'.PHP_EOL;
        }
        $mapHtml .= '               level: '.$vars['level'].''.PHP_EOL;
        $mapHtml .= '           };'.PHP_EOL;
        $mapHtml .= '           var map    = new daum.maps.Map(mapContainer, mapOption);'.PHP_EOL;
        $mapHtml .= '           var marker = new daum.maps.Marker({'.PHP_EOL;
        $mapHtml .= '               map: map,'.PHP_EOL;
        $mapHtml .= '               position: coords'.PHP_EOL;
        $mapHtml .= '           });'.PHP_EOL;
        $mapHtml .= '           var infowindow = new daum.maps.InfoWindow({'.PHP_EOL;
        $mapHtml .= '               content: \''.$marker.'\''.PHP_EOL;
        $mapHtml .= '           });'.PHP_EOL;
        $mapHtml .= '           infowindow.open(map, marker);'.PHP_EOL;
        if($useDirect == false){
        $mapHtml .= '           jQuery(\'.toggleType\').on(\'click\', function(){'.PHP_EOL;
        $mapHtml .= '               var roadmapControl = jQuery(\'#btnRoadmap\');'.PHP_EOL;
        $mapHtml .= '               var skyviewControl = jQuery(\'#btnSkyview\');'.PHP_EOL;
        $mapHtml .= '               var $maptype = jQuery(this).data(\'maptype\');'.PHP_EOL;
        $mapHtml .= '               if ($maptype === \'roadmap\') {'.PHP_EOL;
        $mapHtml .= '                   map.setMapTypeId(daum.maps.MapTypeId.ROADMAP);    '.PHP_EOL;
        $mapHtml .= '                   roadmapControl.removeClass(\'btn\').addClass(\'selected_btn\');'.PHP_EOL;
        $mapHtml .= '                   skyviewControl.removeClass(\'selected_btn\').addClass(\'btn\');'.PHP_EOL;
        $mapHtml .= '                   jQuery(\'.custom_typecontrol\').data(\'type\');'.PHP_EOL;
        $mapHtml .= '               } else {'.PHP_EOL;
        $mapHtml .= '                   map.setMapTypeId(daum.maps.MapTypeId.HYBRID);    '.PHP_EOL;
        $mapHtml .= '                   roadmapControl.removeClass(\'selected_btn\').addClass(\'btn\');'.PHP_EOL;
        $mapHtml .= '                   skyviewControl.removeClass(\'btn\').addClass(\'selected_btn\');'.PHP_EOL;
        $mapHtml .= '               }'.PHP_EOL;
        $mapHtml .= '           });'.PHP_EOL;
        $mapHtml .= '           jQuery(\'.zoomIn\').on(\'click\', function(){'.PHP_EOL;
        $mapHtml .= '               map.setLevel(map.getLevel() - 1);'.PHP_EOL;
        $mapHtml .= '           });'.PHP_EOL;
        $mapHtml .= '           jQuery(\'.zoomOut\').on(\'click\', function(){'.PHP_EOL;
        $mapHtml .= '                map.setLevel(map.getLevel() + 1);'.PHP_EOL;
        $mapHtml .= '           });'.PHP_EOL;
        }
        if ( $vars['showerror'] == 'y' ){
        $mapHtml .= '       } else {'.PHP_EOL;
        $mapHtml .= '           alert(\'다음맵 오류 : 주소변환 오류.\');'.PHP_EOL;
        }
        $mapHtml .= '   }'.PHP_EOL;

        if ($key_type == 'app'){
        $mapHtml .= '   var callbackWrap = function(result, status) {'.PHP_EOL;
        $mapHtml .= '       if (status === daum.maps.services.Status.OK) {'.PHP_EOL;
        $mapHtml .= '           callback(result, status)'.PHP_EOL;
        $mapHtml .= '       }'.PHP_EOL;
        $mapHtml .= '   }'.PHP_EOL;
        $mapHtml .= '   geocoder.addressSearch(\''.$str_addr.'\', callback);'.PHP_EOL;
        }elseif ($key_type == 'api'){
        $mapHtml .= '   geocoder.addr2coord(\''.$str_addr.'\', function(status, result) {'.PHP_EOL;
        $mapHtml .= '       if (status === daum.maps.services.Status.OK) {'.PHP_EOL;
        $mapHtml .= '           callback(result, status)'.PHP_EOL;
        $mapHtml .= '       }'.PHP_EOL;
        $mapHtml .= '   });'.PHP_EOL;
        }
        $mapHtml .= '   </script>'.PHP_EOL;
        $mapHtml .= '</div>'.PHP_EOL;

        if ( get_option($theme_shortname.'_map_daum_infomation') && $useDirect == false ){
        $mapHtml .= '<br><div>'.get_option($theme_shortname.'_map_daum_infomation').'</div>'.PHP_EOL;
        $mapHtml .= '<!-- //다음맵 마침 -->'.PHP_EOL;
        }

        //출력
        if ($useDirect == false){
          return $mapHtml;
        } else {
          echo $mapHtml;
          return false;
        }
	}
}
//다음맵 숏코드를 사용합니다.
add_shortcode('daum_maps', 'bbse_draw_daum_map');

// 특성 이미지를 이용할 수 있도록 합니다.
add_theme_support('post-thumbnails');


if (!function_exists('get_custom_link_url')) {
	function get_custom_link_url($cData){
		unset($customOut);
		if($cData){
			preg_match_all("/<a[^>]*href=[\"']?([^>\"']+)[\"']?[^>]*>/i", $cData, $customOut, PREG_PATTERN_ORDER);
		}

		return $customOut[1][0];
	}
}

if (!function_exists('bbse_theme_member_agreement')) {
	function bbse_theme_member_agreement() {
		global $theme_shortname;
		$contentData="";

		$contentInfo=shortcode_parse_atts(get_the_content());
		if($contentInfo['type']=='agree') $contentData="<textarea name='theme_member_agree' class='agreement' style='width:".$contentInfo['width'].";height:".$contentInfo['height']."'>".stripslashes(get_option($theme_shortname.'_member_agreement'))."</textarea>";
		else if($contentInfo['type']=='private') $contentData="<textarea name='theme_member_private_1' class='agreement' style='width:".$contentInfo['width'].";height:".$contentInfo['height']."'>".stripslashes(get_option($theme_shortname.'_member_private_1'))."</textarea>";
		else if($contentInfo['type']=='email') $contentData="<textarea name='theme_member_email_reject' class='agreement' style='width:".$contentInfo['width'].";height:".$contentInfo['height']."'>".stripslashes(get_option($theme_shortname.'_member_email_reject'))."</textarea>";
		else if($contentInfo['type']=='order') $contentData="<textarea name='theme_member_agree_order' class='agreement' style='width:".$contentInfo['width'].";height:".$contentInfo['height']."'>".stripslashes(get_option($theme_shortname.'_member_agreement_order'))."</textarea>";
		else if($contentInfo['type']=='leave') $contentData="<textarea name='theme_member_agree_leave' class='agreement' style='width:".$contentInfo['width'].";height:".$contentInfo['height']."'>".stripslashes(get_option($theme_shortname.'_member_agreement_leave'))."</textarea>";
		return $contentData;
	}
	add_shortcode( 'theme_member_agreement', 'bbse_theme_member_agreement' );
}

if (!function_exists('bbse_get_custom_list')) {
	function bbse_get_custom_list($tType){
		switch($tType){
			case "category" :
				$args = array(
					'show_option_all'    => '',
					'orderby'            => 'name',
					'order'              => 'ASC',
					'style'              => 'none',
					'hide_empty'         => 0,
					'use_desc_for_title' => 1,
					'hierarchical'       => 1,
					'title_li'           => __( 'Categories' ),
					'show_option_none'   => __('No categories'),
					'number'             => null,
					'echo'               => 0,
					'taxonomy'           => 'category',
					'walker'             => null
				);

				$get_wp_cate=wp_list_categories( $args );
				$list_cate=explode("\n",$get_wp_cate);

				$cate_data=Array();
				$cate_cnt='0';
				for($s=0;$s<sizeof($list_cate);$s++){
					$getCustomLink="";
					if(strip_tags($list_cate[$s])){
						$getCustomLink=get_custom_link_url($list_cate[$s]);
						if($getCustomLink){
							$tmp_cateId=explode("cat=",$getCustomLink);
							$cate_data[$cate_cnt]['id']=$tmp_cateId['1'];
							$cate_data[$cate_cnt]['link']=$getCustomLink;
							$cate_data[$cate_cnt]['name']=strip_tags($list_cate[$s]);

							if(!trim($cate_data[$cate_cnt]['id'])){
								$cate_data[$cate_cnt]['id']=get_cat_ID(trim($cate_data[$cate_cnt]['name']));
							}

							$cate_cnt++;
						}
					}
				}
				return $cate_data;
			break;
			case "page" :
				$args = array(
					'authors'      => '',
					'child_of'     => 0,
					'date_format'  => get_option('date_format'),
					'depth'        => 0,
					'echo'         => 0,
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'sort_column'  => 'menu_order, post_title',
					'title_li'     => '',
					'walker'       => ''
				);

				$get_wp_page=wp_list_pages( $args );
				$list_page=explode("\n",$get_wp_page);

				$page_data=Array();
				$page_cnt='0';

				for($s=0;$s<sizeof($list_page);$s++){
					$getCustomLink="";
					if(strip_tags($list_page[$s])){
						$getCustomLink=get_custom_link_url($list_page[$s]);
						if($getCustomLink){
							$tmp_pageId=explode("page_id=",$getCustomLink);
							$page_data[$page_cnt]['id']=$tmp_pageId['1'];
							$page_data[$page_cnt]['link']=$getCustomLink;
							$page_data[$page_cnt]['name']=strip_tags($list_page[$s]);

							if(!$page_data[$page_cnt]['id']) $page_data[$page_cnt]['id']=url_to_postid($page_data[$page_cnt]['link']);

							$page_cnt++;
						}
					}
				}
				return $page_data;
			break;
			default :

			break;
		}
	}
}


if (!function_exists('set_custom_login_redirect')) {
	function set_custom_login_redirect( $redirect_to){
		global $user;
		if( isset( $user->roles ) && is_array( $user->roles ) ) {
			if ( count( array_diff( $user->roles, array( 'administrator', 'editor', 'author', 'contributor' ) ) ) !== count( $user->roles ) ){
				return admin_url();
			} else {
				if($redirect_to) return $redirect_to;
				else return home_url();
			}
		}
		else {
			return home_url();
		}
	}
	add_filter("login_redirect", "set_custom_login_redirect", 10, 1);
}


if (!function_exists('set_custom_logout_redirect')) {
	function set_custom_logout_redirect($logouturl) {
		return $logouturl . '&amp;redirect_to=' . urlencode(home_url());
	}
	add_filter('logout_url', 'set_custom_logout_redirect', 10, 1);
}


if (!function_exists('get_custom_check_member_plugin')) {
	function get_custom_check_member_plugin($pName,$pAuth){
		global $wpdb;

		if(strpos($_SERVER['PHP_SELF'],"/wp-admin/") === false){
			require_once (ABSPATH .'/wp-admin/includes/plugin.php');
		}

		$apl=get_option('active_plugins');
		$plugins=get_plugins();

		$rtn_data['flag']=false;
		$rtn_data['login_page']=$rtn_data['join_page']=$rtn_data['id_search_page']=$rtn_data['id_search_page']="";

		foreach ($apl as $p){
			if(isset($plugins[$p]) && strpos($plugins[$p]['Name'], $pName) !== false && strpos($plugins[$p]['Author'], $pAuth) !== false){
				$rtn_data['flag']=true;
			}
		}

		if($rtn_data['flag']){
			if(get_option('bbse_commerce_login_page') and get_option('bbse_commerce_join_page') and get_option('bbse_commerce_id_search_page') and get_option('bbse_commerce_pass_search_page') and get_option('bbse_commerce_delete_page')){
				$rtn_data['login_page']=get_option('bbse_commerce_login_page');
				$rtn_data['join_page']=get_option('bbse_commerce_join_page');
				$rtn_data['id_search_page']=get_option('bbse_commerce_id_search_page');
				$rtn_data['id_search_page']=get_option('bbse_commerce_pass_search_page');
				$rtn_data['delete_page']=get_option('bbse_commerce_delete_page');
			}
			else $rtn_data['flag']=false;
		}

		return $rtn_data;
	}
}



if (!function_exists('get_custom_sub_menu_count')) {
	function get_custom_sub_menu_count($tMenuArray,$tMenuName){
		$find_flag='n';
		$rtnCount='0';
		for($i=0;$i<count($tMenuArray);$i++){
			$tData=trim($tMenuArray[$i]);

			if($i>'0'){
				if(($find_flag=='y' && trim($tMenuArray[($i-1)])=="</ul>" && $tData=="</li>") || substr($tData,0,3)=="<li" && trim($tMenuArray[($i-1)])==$tMenuName) $find_flag='n';
			}

			if(substr($tData,0,3)=="<li"){
				if($find_flag=='y'){
					$rtnCount++;
				}
				else{
					if($tData==$tMenuName) $find_flag='y';
					else $find_flag='n';
				}
			}
		}

		return $rtnCount;
	}
}


if (!function_exists('bbse_check_view_comment')) {
	function bbse_check_view_comment($pID=""){
		global $theme_shortname;
		if($pID){
			$displayViewPage=explode(",",get_option($theme_shortname."_sub_comment_enable_page"));
			if(in_array($pID,$displayViewPage)) return true;
			else return false;
		}
		else return false;
	}
}


if (!function_exists('get_custom_menu_list')) {
	function bbse_find_first_menu_title(){
		global $theme_shortname;
		$menuData=explode("\n",wp_nav_menu(array('container'=> 'false', 'menu'=>get_option($theme_shortname.'_basic_topmenu_name'),'echo'=> false,'items_wrap'=> '%3$s')));
		$f_flag='0';
		$sub_count='1';
		$find_top='N';

		$printMenu="";
		for($i=0;$i<count($menuData);$i++){
			$tData=trim($menuData[$i]);

			if($tData){
				$pos = strpos($tData,"current-menu-ancestor");
				if($pos!==false) $find_top="Y";

				if($find_top=="Y"){
					if(substr($tData,0,3)=="<li"){
						if($f_flag=='0'){
							$printMenu .="<h2 class=\"side-title\">".strip_tags($tData)."</h2>\n";
						}
						else{
							$tCount=get_custom_sub_menu_count($menuData,$tData);
							if($tCount>0){
								//$clickAction="href=\"javascript:onclick=view_submenu(".$sub_count.");\"";
								$clickAction="data-value=\"".$sub_count."\"";
								$endTad_li="";
								$childCheckClass="menu-item-has-children";
							}
							else{
								$clickAction="href=\"".get_custom_link_url($tData)."\"";
								$endTad_li="</li>";
								$childCheckClass="";
							}
							$currCheckPos_ancestor = strpos($tData,"current-menu-ancestor");
							$currCheckPos_item = strpos($tData,"current-menu-item");

							($currCheckPos_ancestor!==false or $currCheckPos_item!==false)?$currCheckClass="active":$currCheckClass="";
							($currCheckClass or $childCheckClass )?$currClass=" class=\"".$currCheckClass." ".$childCheckClass."\"":$currClass="";

							$printMenu .="<li".$currClass."><a ".$clickAction.">".strip_tags($tData)."</a>".$endTad_li."\n";
						}
					}
					elseif($tData=='<ul class="sub-menu">'){
						if($f_flag=='0') {
							$printMenu .="<ul class=\"side-menu\">\n";
						}
						else{
							$printMenu .="<ul id=\"side_sub_".$sub_count."\"class=\"\">\n";
							$sub_count++;
						}
						$f_flag++;
					}
					elseif($tData=="</ul>"){
						$printMenu .="</ul>"."\n";
						$f_flag--;
					}
					elseif($tData=="</li>"){
						if($f_flag>'0')	$printMenu .="</li>"."\n";
						else $find_top="N";
					}
				}
			}
		}

		//if(!$printMenu) echo get_the_ID();

		return $printMenu;

	}
}

if (!function_exists('bbse_cut_string')) {
	function bbse_cut_string($str, $len, $checkmb=false, $tail='') {
		preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);
		$m    = $match[0];
		$slen = strlen($str);
		$tlen = strlen($tail);
		$mlen = count($m); 
		
		if ($slen <= $len) return $str;
		if (!$checkmb && $mlen <= $len) return $str;
		
		$ret   = array();
		$count = 0;
		
		for ($i=0; $i < $len; $i++) {
			$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
			if ($count + $tlen > $len) break;
			$ret[] = $m[$i];
		}
		return join('', $ret).$tail;
	}
}

if (!function_exists('bbse_nl2br_markup')) {
	function bbse_nl2br_markup($str) {
		$rtnStr=str_replace("<br>","<br />",nl2br(stripcslashes($str)));
		return $rtnStr;
	}
}

// 포스트 뷰 카운트를 표시합니다.
if (!function_exists('bbse_get_hit_count')) {
	function bbse_get_hit_count($postID){
	  $count_key = 'hits_count';
	  $count     = get_post_meta($postID, $count_key, true);
	  if($count==''){
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
		return "0";
	  }
	  return $count;
	}
	function bbse_the_hit_count($postID){
	  echo bbse_get_hit_count($postID);
	}
}

// 포스트의 뷰를 카운팅합니다. (single.php에 코드 삽입)
if (!function_exists('bbse_counting_hit')) {
	function bbse_counting_hit($postID) {
	  $count_key = 'hits_count';
	  $count     = get_post_meta($postID, $count_key, true);
	  if($count==''){
		$count = 0;
		delete_post_meta($postID, $count_key);
		add_post_meta($postID, $count_key, '0');
	  }else{
		$count++;
		update_post_meta($postID, $count_key, $count);
	  }
	}
}

// 관리자  글목록에 조회수 컬럼을 추가합니다.
add_filter('manage_posts_columns', 'posts_column_views');
add_action('manage_posts_custom_column', 'posts_custom_column_views',5,2);
if (!function_exists('posts_column_views')) {
	function posts_column_views($defaults){
		$defaults['hits_count'] = '조회수';
		return $defaults;
	}
}

if (!function_exists('posts_custom_column_views')) {
	function posts_custom_column_views($column_name, $id){
		if($column_name === 'hits_count'){
			echo bbse_get_hit_count(get_the_ID());
		}
	}
}

// 포스트 메타 정보를 표시합니다.
// 시간, 작성자
if ( ! function_exists( 'bbse_the_posted_on' ) ){

  function bbse_the_posted_on() {
    printf('<span class="entry-author">By <a href="%1$s">%2$s</a></span><span class="entry-date" ><a href="%3$s" rel="bookmark">%4$s</a></span>',
      esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
      get_the_author(),
      esc_url( site_url().'/?m='.get_the_date('Ymd') ),
      esc_html( get_the_date() )
    );
    if (get_the_category_list()) printf('<span class="entry-category">%1$s</span>', get_the_category_list( ', ' ));
    echo '<span class="entry-comment">';
    comments_popup_link('Leave comment', '1 Comment', '% Comments' );
    echo '</span>';
  }
}

if ( ! function_exists( 'plugin_active_check' ) ){
	function plugin_active_check($plugin_name) {
		$required_plugin = $plugin_name.'/'.$plugin_name.'.php';
		$plugins = get_option('active_plugins');
		$bbse_commerce_active = false;
		if ( in_array( $required_plugin , $plugins ) ) {
			$bbse_commerce_active = true;
		}
		return $bbse_commerce_active;
	}
}

if ( ! function_exists( 'date_convert' ) ){
	function date_convert($format, $timestamp) {
		$timestamp += get_option( 'gmt_offset' ) * 3600;
		//return date_i18n($format, $timestamp);
		return date($format, $timestamp);
	}
}

if(!function_exists("bbse_show_user_id")) { // User_id Masking(*)
	function bbse_show_user_id($tStr,$vCnt){
		$len = strlen($tStr);
		if($len>12) $len=12;
		$pre_tStr = substr($tStr,'0',$vCnt);

		for($i=0;$i<($len-$vCnt);$i++){
			$bak_tStr .="*";
		}

		$rtnStr = $pre_tStr.$bak_tStr;
		return $rtnStr;
	}
}

// 접속자 회원등급
if(!function_exists('bbse_get_current_user_level')){
	function bbse_get_current_user_level(){
		if(current_user_can('level_5')){  // 관리자
			return "administrator";
		
		}else if(current_user_can('level_0')){  // 로그인 회원
			return "author";
		}
		else return "all";  // 게스트
	}
}

if(!function_exists("bbse_get_goods_qna_list")) { // Q&A 리스트 추출
	function bbse_get_goods_qna_list($funcName, $goods_idx, $paged=1, $qna_per_page=10, $pageBlock=10){
		global $wpdb;

		$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

		$currUserID=$current_user->user_login;
		$Loginflag='member';

		if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
			if($_SESSION['snsLoginData']){
				$snsLoginData=unserialize($_SESSION['snsLoginData']);

				$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
				if($snsData->idx){
					$Loginflag='social';
					$currUserID=$snsLoginData['sns_id'];
					$currSnsIdx=$snsData->idx;
				}
			}
		}

		$start_pos = ($paged-1) * $qna_per_page;  // 목록 시작 위치

		$rtnStr="";
		$qnaTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND goods_idx='".$goods_idx."' AND q_type='Q' ORDER BY idx DESC"); // 총 Q&A 수
		$qna_sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_qna WHERE idx<>'' AND goods_idx='".$goods_idx."' AND q_type='Q' ORDER BY idx DESC LIMIT %d, %d", array($start_pos,$qna_per_page));
		$qna_result = $wpdb->get_results($qna_sql);

		if($qnaTotal>'0'){
			foreach($qna_result as $i=>$qnaData) {
				$ansCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND goods_idx='".$goods_idx."' AND q_type='A' AND q_parent='".$qnaData->idx."' ORDER BY idx DESC"); // 답변 Q&A 수
				if($ansCnt>'0'){
					$ansData = $wpdb->get_row("SELECT * FROM bbse_commerce_qna WHERE idx<>'' AND goods_idx='".$goods_idx."' AND q_type='A' AND q_parent='".$qnaData->idx."'");
					$q_status="답변완료";
				}
				else $q_status="답변중";

				if($curUserPermision=="administrator" || $qnaData->q_secret!='on' || ($currUserID && $Loginflag=='member' && $qnaData->q_secret=='on' && $currUserID==$qnaData->user_id) || ($currUserID && $Loginflag=='social' && $qnaData->q_secret=='on' && $currUserID==$qnaData->sns_id && $currSnsIdx==$qnaData->sns_idx)){
					$ansScript="view_answer(".$qnaData->idx.");";
				}
				else $ansScript="alert('비밀글 입니다.       ');";

					$rtnStr .="<dt onclick=\"".$ansScript."\">
										<div class=\"cmt_subject\">
											<span class=\"q_icon\">Q</span>
											".$qnaData->q_subject;

											if($qnaData->q_secret=='on'){
												$rtnStr .="<em class=\"bb_lock\">잠김 글</em>";
											}
					if($qnaData->user_id) $viewId=bbse_show_user_id($qnaData->user_id,3);
					else $viewId="소셜로그인";
					$rtnStr .="		</div>
										<div class=\"cell_question\">".$q_status."</div>
										<div class=\"cell_id\">
											".$viewId."
										</div>
										<div class=\"cell_date\">".date("Y-m-d",$qnaData->write_date)."</div>
									</dt>";


				if($curUserPermision=="administrator" || $qnaData->q_secret!='on' || ($currUserID && $Loginflag=='member' && $qnaData->q_secret=='on' && $currUserID==$qnaData->user_id) || ($currUserID && $Loginflag=='social' && $qnaData->q_secret=='on' && $currUserID==$qnaData->sns_id && $currSnsIdx==$qnaData->sns_idx)){
						$rtnStr .="<dd id=\"qna-".$qnaData->idx."\" style=\"display:none;\">
											<div class=\"cmt_answer\">
												".bbse_nl2br_markup($qnaData->q_contents);

							if($ansCnt<='0' && (($currUserID && $Loginflag=='member' && $currUserID==$qnaData->user_id) || ($currUserID && $Loginflag=='social' && $currUserID==$qnaData->sns_id && $currSnsIdx==$qnaData->sns_idx))){
								$rtnStr .="<p class=\"cmt_btn_set\">
													<button type=\"button\" onClick=\"qna_remove(".$qnaData->idx.");\" class=\"bb_btn shadow\"><span class=\"sml\">삭제</span></button>
													<button type=\"button\" onClick=\"qna_modify(".$qnaData->idx.");\" class=\"bb_btn shadow\"><span class=\"sml\">수정</span></button>
												</p>";
							}

								$rtnStr .="</div>";

							if($ansCnt>'0'){
								$rtnStr .="<div class=\"qu_answer\">
													<strong>A</strong>".bbse_nl2br_markup($ansData->q_contents)."
													<p class=\"cmt_btn_set\">
														<span class=\"bb_date\">".date("Y-m-d",$ansData->write_date)."</span>
													</p>
												</div>
											</dd>";
							}
				}
			}
		}
		else{
			$rtnStr .="<dd style=\"padding: 6px 0;text-align:center;\">
								등록 된 상품문의가 존재하지 않습니다.
						</dd>";
		}

		if($qnaTotal<$paged) $paged=$qnaTotal;

		if($funcName){
			$page_param = array();           
			$page_param['page_row'] = $qna_per_page;
			$page_param['page_block'] = $pageBlock;
			$page_param['total_count'] = $qnaTotal;
			$page_param['current_page'] = $paged;
			$page_param['func'] = $funcName;

			$page_class = new themePaging(); 
			$page_class->initPaging($page_param);

			$rtnPaging=$page_class->getPaging();
			return $rtnStr."|||".$rtnPaging."|||".$qnaTotal;
		}
		else return $rtnStr;
	}
}


if(!function_exists("bbse_get_goods_review_list")) { // 상품평 리스트 추출
	function bbse_get_goods_review_list($funcName, $goods_idx, $paged=1, $review_per_page=10, $pageBlock=10){
		global $wpdb;

		$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

		$currUserID=$current_user->user_login;
		$Loginflag='member';

		if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
			if($_SESSION['snsLoginData']){
				$snsLoginData=unserialize($_SESSION['snsLoginData']);

				$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
				if($snsData->idx){
					$Loginflag='social';
					$currUserID=$snsLoginData['sns_id'];
					$currSnsIdx=$snsData->idx;
				}
			}
		}

		$start_pos = ($paged-1) * $review_per_page;  // 목록 시작 위치

		$rtnStr="";
		$reviewTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$goods_idx."' ORDER BY idx DESC"); // 총 Q&A 수
		$review_sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$goods_idx."' ORDER BY idx DESC LIMIT %d, %d", array($start_pos,$review_per_page));
		$review_result = $wpdb->get_results($review_sql);

		if($reviewTotal>'0'){
			foreach($review_result as $i=>$reviewData) {
				$rtnStr .="<dt onClick=\"view_review(".$reviewData->idx.");\">
								<div class=\"cmt_subject\">";

				if($reviewData->r_attach_org && $reviewData->r_attach_new){
					$rtnStr .="	<span class=\"cmt_file\">이미지 있음</span>";
				}


				if($reviewData->user_id) $viewId=bbse_show_user_id($reviewData->user_id,3);
				else $viewId="소셜로그인";

				$rtnStr .=		$reviewData->r_subject."
								</div>
								<div class=\"cell_result\">
									<span class=\"bb_cmt_star cmt".$reviewData->r_value."\">별점 ".$reviewData->r_value."점/5점</span>
								</div>
								<div class=\"cell_id\">
									".$viewId."
								</div>
								<div class=\"cell_date\">".date("Y-m-d",$reviewData->write_date)."</div>
							</dt>
							<dd id=\"review-".$reviewData->idx."\" style=\"display:none;\">
								<div class=\"cmt_answer\">";
				if($reviewData->r_attach_org && $reviewData->r_attach_new){
					$rtnStr .="<img src=\"".BBSE_COMMERCE_UPLOAD_BASE_URL."bbse-commerce/".$reviewData->r_attach_new."\" alt=\"첨부이미지\" />";
				}
								
				$rtnStr .=bbse_nl2br_markup($reviewData->r_contents);

				if((($currUserID && $Loginflag=='member' && $currUserID==$reviewData->user_id) || ($currUserID && $Loginflag=='social' && $currUserID==$reviewData->sns_id && $currSnsIdx==$reviewData->sns_idx)) && $reviewData->r_earn_paid=='N' && $reviewData->r_earn_point<='0'){
					$rtnStr .="<p class=\"cmt_btn_set\">
										<button type=\"button\" onClick=\"review_remove(".$reviewData->idx.");\" class=\"bb_btn shadow\"><span class=\"sml\">삭제</span></button>
										<button type=\"button\" onClick=\"review_modify(".$reviewData->idx.");\" class=\"bb_btn shadow\"><span class=\"sml\">수정</span></button>
									</p>";
				}
				$rtnStr .="	</div>
							</dd>";
			}
		}
		else{
			$rtnStr .="<dd style=\"padding: 6px 0;text-align:center;\">
								등록 된 상품평이 존재하지 않습니다.
						</dd>";
		}

		if($reviewTotal<$paged) $paged=$reviewTotal;

		if($funcName){
			$page_param = array();           
			$page_param['page_row'] = $review_per_page;
			$page_param['page_block'] = $pageBlock;
			$page_param['total_count'] = $reviewTotal;
			$page_param['current_page'] = $paged;
			$page_param['func'] = $funcName;

			$page_class = new themePaging(); 
			$page_class->initPaging($page_param);

			$rtnPaging=$page_class->getPaging();
			return $rtnStr."|||".$rtnPaging."|||".$reviewTotal;
		}
		else return $rtnStr;
	}
}

if(!function_exists("bbse_get_goods_qna_mypage_list")) { // Q&A 리스트 추출
	function bbse_get_goods_qna_mypage_list($funcName, $paged=1, $qna_per_page=10, $pageBlock=10){
		global $wpdb;

		$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

		$currUserID=$current_user->user_login;
		$Loginflag='member';
		$currSnsIdx="";

		if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
			if($_SESSION['snsLoginData']){
				$snsLoginData=unserialize($_SESSION['snsLoginData']);

				$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
				if($snsData->idx){
					$Loginflag='social';
					$currUserID=$snsLoginData['sns_id'];
					$currSnsIdx=$snsData->idx;
				}
			}
		}

		$start_pos = ($paged-1) * $qna_per_page;  // 목록 시작 위치

		$rtnStr="";

		if($Loginflag=='member'){
			$qnaTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE user_id='".$currUserID."' AND idx<>'' AND q_type='Q' ORDER BY idx DESC"); // 총 Q&A 수
			$qna_sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_qna WHERE user_id='".$currUserID."' AND idx<>'' AND q_type='Q' ORDER BY idx DESC LIMIT %d, %d", array($start_pos,$qna_per_page));
		}
		else if($Loginflag=='social' && $currSnsIdx>'0'){
			$qnaTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE user_id='' AND sns_id='".$currUserID."' AND sns_idx='".$currSnsIdx."' AND idx<>'' AND q_type='Q' ORDER BY idx DESC"); // 총 Q&A 수
			$qna_sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_qna WHERE user_id='' AND sns_id='".$currUserID."' AND sns_idx='".$currSnsIdx."' AND idx<>'' AND q_type='Q' ORDER BY idx DESC LIMIT %d, %d", array($start_pos,$qna_per_page));
		}

		$qna_result = $wpdb->get_results($qna_sql);

		if($qnaTotal>'0'){
			foreach($qna_result as $i=>$qnaData) {
				$ansCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND q_type='A' AND q_parent='".$qnaData->idx."' ORDER BY idx DESC"); // 답변 Q&A 수
				if($ansCnt>'0'){
					$ansData = $wpdb->get_row("SELECT * FROM bbse_commerce_qna WHERE idx<>'' AND q_type='A' AND q_parent='".$qnaData->idx."'");
					$q_status="답변완료";
				}
				else $q_status="답변중";

				$ansScript="mypage_view_answer(".$qnaData->idx.");";

				$rtnStr .="<dt onclick=\"".$ansScript."\">
									<span class=\"subject\">".$qnaData->q_subject."</span>
									<span class=\"status\">".$q_status."</span>
									<span class=\"date\">".date("Y-m-d",$qnaData->write_date)."</span>
								</dt>
								<dd id=\"qna-".$qnaData->idx."\" style=\"display:none;\">
									<div class=\"btns\">";
									if($ansCnt<='0') {
										$rtnStr .="
										<button class=\"bb_btn shadow\" onClick=\"mypage_qna_remove(".$qnaData->idx.");\"><span class=\"sml\">삭제</span></button>
										<button class=\"bb_btn shadow\" onClick=\"mypage_qna_modify(".$qnaData->idx.");\"><span class=\"sml\">수정</span></button><br>
										<button class=\"bb_btn shadow\" onClick=\"location.href='".home_url()."/?bbseGoods=".$qnaData->goods_idx."';\" style=\"width:90px;\"><span class=\"sml\">상품바로가기</span></button>
										";
									}
				$rtnStr .="	</div>
									<div class=\"enquiry\"><p>".bbse_nl2br_markup($qnaData->q_contents)."</p></div>";
				if($ansCnt>'0'){
					$rtnStr .="<div class=\"answer\"><p>".bbse_nl2br_markup($ansData->q_contents)."<span class=\"date\">".date("Y-m-d",$ansData->write_date)."</span></p></div>";
				}
				$rtnStr .="</dd>";

			}
		}
		else{
			$rtnStr .="<dd style=\"padding: 6px 0;text-align:center;\">
								등록 된 상품문의가 존재하지 않습니다.
						</dd>";
		}

		if($qnaTotal<$paged) $paged=$qnaTotal;

		if($funcName){
			$page_param = array();           
			$page_param['page_row'] = $qna_per_page;
			$page_param['page_block'] = $pageBlock;
			$page_param['total_count'] = $qnaTotal;
			$page_param['current_page'] = $paged;
			$page_param['func'] = $funcName;

			$page_class = new themePaging(); 
			$page_class->initPaging($page_param);

			$rtnPaging=$page_class->getPaging();
			return $rtnStr."|||".$rtnPaging."|||".$qnaTotal;
		}
		else return $rtnStr;
	}
}

if(!function_exists("bbse_get_unhtmlspecialchars")) {
	function bbse_get_unhtmlspecialchars($string) { 
        $string = str_replace ( '&amp;', '&', $string ); 
        $string = str_replace ( '&#039;', '\'', $string ); 
        $string = str_replace ( '&quot;', '\"', $string ); 
        $string = str_replace ( '&lt;', '<', $string ); 
        $string = str_replace ( '&gt;', '>', $string ); 
      
        return $string; 
    }
}

if(!function_exists("shortcode_url")) {
	function shortcode_url($shortcode, $attr='') {
		if(empty($shortcode))return false;
		global $wpdb;
		$parseItem = array();
		$retp = $wpdb->get_results("select ID, post_content from wp_posts where post_content REGEXP '\\\\[".$shortcode."' and post_status='publish' order by ID desc");
		foreach($retp as $i=>$row) {
			$exp1 = explode("[".$shortcode, $row->post_content);
			$exp2 = explode(" ",$exp1[1]);
			
			$parseItem[$i]['ID'] = $row->ID;
			$parseItem[$i]['shortcode'] = $shortcode;
			$parseItem[$i]['post_content'] = $row->post_content;

			if($exp2[0]=="") {
				array_shift($exp2);
				array_pop($exp2);
				foreach($exp2 as $set) {
					parse_str($set, $out);
					$parseItem[$i][key($out)] = stripslashes(str_replace("'","",$out[key($out)]));
				}
			}
		}

		if(count($parseItem) > 1) {
			for($i=0;$i<count($parseItem);$i++) {
				if($attr[key($attr)] == $parseItem[$i][key($attr)]) return get_permalink($parseItem[$i]['ID']);
			}
		}else{
			return get_permalink($parseItem[0]['ID']);
		}

	}
}

if(!function_exists("goodsSoldoutCheck")) {
	function goodsSoldoutCheck($goods) {
		global $wpdb;

		if($goods->goods_display=='soldout' || ($goods->goods_count_flag=='goods_count' && $goods->goods_count <= 0)) {
			return true;
		}else if($goods->goods_count_flag=='option_count') {
			$optTotal_count = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_item_count>'0' AND goods_option_item_soldout<>'soldout' AND goods_option_item_display='view'"); 
			if($optTotal_count<='0') return true;
			else return false;
		}else{
			return false;
		}

	}
}

if(!function_exists("emptyCart")) {
	function emptyCart($user_id="") {
		global $wpdb;
		$cfgRes = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order' limit 1");
		$cfg = unserialize($cfgRes);
		$cfg['cart_empty_cycle'] = (!$cfg['cart_empty_cycle'])?7:$cfg['cart_empty_cycle'];
		$delete_time = 86400 * $cfg['cart_empty_cycle'];//장바구니 보관 시간 지정
		$currTime=current_time('timestamp');
		$wpdb->query("delete from bbse_commerce_cart where reg_date < (".$currTime." -".$delete_time.")");
	}
}

if(!function_exists("updateCart")) {
	function updateCart($user_id) {
		if(!$user_id) return false;
		global $wpdb;

		$result = $wpdb->get_results("SELECT C.idx AS cart_idx, C.user_id, C.sid, C.goods_option_basic AS cart_option_basic, C.goods_option_add AS cart_option_add, C.remote_ip, C.reg_date, G.* FROM bbse_commerce_cart AS C, bbse_commerce_goods AS G WHERE C.goods_idx=G.idx AND C.cart_kind='C' AND C.user_id='".$user_id."' ORDER BY C.idx ASC");
		foreach($result as $cart) {

			$option_basic = $cart->cart_option_basic?unserialize($cart->cart_option_basic):"";
			$option_add = $cart->cart_option_add?unserialize($cart->cart_option_add):"";
			$goods_option_add = $cart->goods_option_add?unserialize($cart->goods_option_add):"";

			 if(count($option_basic['goods_option_title']) > 0) {//옵션선택

				for($i=0 ; $i<count($option_basic['goods_option_title']) ; $i++) {

					if(goodsSoldoutCheck($cart) == true) $wpdb->query("delete from bbse_commerce_cart where idx='".$cart->cart_idx."'");//품절이면 삭제

					if($option_basic['goods_option_title'][$i]!="단일상품") {
						if($cart->goods_count_flag=="goods_count" &&$cart->goods_count<='0'){
							$wpdb->query("delete from bbse_commerce_cart where idx='".$cart->cart_idx."'");
						}
						elseif($cart->goods_count_flag=="option_count"){
							$optCnt = $wpdb->get_var("select count(*) from bbse_commerce_goods_option where goods_option_title='".$option_basic['goods_option_title'][$i]."' and goods_idx='".$cart->idx."' and goods_option_item_count>'0' AND goods_option_item_soldout<>'soldout' AND goods_option_item_display='view'");

							if($optCnt == 0) $wpdb->query("delete from bbse_commerce_cart where idx='".$cart->cart_idx."'");
						}
					}
				}

			 }

			 if(count($option_add['goods_add_title']) > 0) {//추가상품
				//$option_add['goods_add_title'][0]
				$addChk = 0;
				for($i=0;$i<count($option_add['goods_add_title']);$i++) {
					for($j=1;$j<=$goods_option_add['goods_add_option_count'];$j++) {
						for($k=0;$k<count($goods_option_add['goods_add_'.$j.'_item']);$k++) {
							if($goods_option_add['goods_add_'.$j.'_item'][$k]==$option_add['goods_add_title'][$i]) {
								$addChk++;
							}
						}
					}
				}

				if(count($option_add['goods_add_title']) > $addChk)  $wpdb->query("delete from bbse_commerce_cart where idx='".$cart->cart_idx."'");

			 }

		}

		return true;

	}
}

// 회원정보 추출 & 레벨명/할인적용여부
if(!function_exists("bbse_get_user_information")) {
	function bbse_get_user_information($user_id=""){
		global $wpdb,$current_user;
		if($user_id!="") {
			$uid = $user_id;
		}else{
			$uid = $current_user->user_login;
		}
		$bbseMember = $wpdb->get_row("SELECT A.*,B.class_name,B.use_sale FROM bbse_commerce_membership AS A, bbse_commerce_membership_class AS B WHERE A.user_id='".$uid."' AND A.user_class=B.no");
		return $bbseMember;
	}
}

// 최근본 상품 등록
if(!function_exists("bbse_commerce_set_recent_goods")) {
	function bbse_commerce_set_recent_goods($goods_idx,$remote_ip){
		global $wpdb;
		$reg_date=current_time('timestamp');
		$gCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$goods_idx."'");
		if($gCnt>'0'){
			$wpdb->query("DELETE FROM bbse_commerce_recent WHERE goods_idx='".$goods_idx."' AND remote_ip='".$remote_ip."'");
			$wpdb->query("INSERT INTO bbse_commerce_recent (goods_idx,remote_ip,reg_date) VALUES ('".$goods_idx."','".$remote_ip."','".$reg_date."')");
			$idx = $wpdb->insert_id;
			if($idx) return true;
			else return false;
		}
		else return false;
	}
}

// 최근본 상품 보기
if(!function_exists("bbse_commerce_get_recent_view_goods")) {
	function bbse_commerce_get_recent_view_goods($remoteIp){
		global $wpdb;

		$csTable=$wpdb->get_var("SHOW TABLES LIKE 'bbse_commerce_recent'");
		$rtnStr="";

		if($csTable=='bbse_commerce_recent'){
			$tTime=current_time('timestamp')-(60*60*24);
			$wpdb->query("DELETE FROM bbse_commerce_recent WHERE reg_date<='".$tTime."'");

			$tRegDate = $wpdb->get_var("SELECT reg_date FROM bbse_commerce_recent WHERE remote_ip='".$remoteIp."' ORDER BY reg_date DESC LIMIT 20,1");
			if($tRegDate){
				$wpdb->query("DELETE FROM bbse_commerce_recent WHERE remote_ip='".$remoteIp."' AND reg_date<='".$tRegDate."'");
			}
			
			$rResult = $wpdb->get_results("SELECT * FROM bbse_commerce_recent WHERE remote_ip='".$remoteIp."' ORDER BY reg_date DESC LIMIT 3");
			foreach($rResult as $i=>$rData) {
				$rGoods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$rData->goods_idx."'");

				$rSoldout = goodsSoldoutCheck($rGoods); //품절체크
				if($rSoldout) continue;
				$imgSizeKind="goodsimage1";

				if($rGoods->goods_basic_img) $basicImg = wp_get_attachment_image_src($rGoods->goods_basic_img,$imgSizeKind);
				else{
					$imageList=explode(",",$rGoods->goods_add_img);
					if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
					else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
				}

				$rtnStr .="<li>
								<span onClick=\"remove_recent(".$rGoods->idx.",'".$remoteIp."');\" class=\"recent_remove\" title=\"최근본 상품 삭제\"></span>
								<a href=\"".home_url()."/?bbseGoods=".$rGoods->idx."\">
									<img src=\"".$basicImg['0']."\" alt=\"".$rGoods->goods_name."\" />
									<div class=\"hover\">
										<span>
											<em>".$rGoods->goods_name."</em>
											<span><strong>".number_format($rGoods->goods_price)."</strong>원</span>
										</span>
									</div>
								</a>
							</li>";
			}
		}
		return $rtnStr;
	}
}

if(!function_exists("bbse_commerce_get_recent_view_goods2")) {
	function bbse_commerce_get_recent_view_goods2($remoteIp){
		global $wpdb;

		$csTable=$wpdb->get_var("SHOW TABLES LIKE 'bbse_commerce_recent'");
		$rtnStr="";

		if($csTable=='bbse_commerce_recent'){
			$tTime=current_time('timestamp')-(60*60*24);
			$wpdb->query("DELETE FROM bbse_commerce_recent WHERE reg_date<='".$tTime."'");

			$tRegDate = $wpdb->get_var("SELECT reg_date FROM bbse_commerce_recent WHERE remote_ip='".$remoteIp."' ORDER BY reg_date DESC LIMIT 20,1");
			if($tRegDate){
				$wpdb->query("DELETE FROM bbse_commerce_recent WHERE remote_ip='".$remoteIp."' AND reg_date<='".$tRegDate."'");
			}

			$rResult = $wpdb->get_results("SELECT * FROM bbse_commerce_recent WHERE remote_ip='".$remoteIp."' ORDER BY reg_date DESC");
			foreach($rResult as $i=>$rData) {
				$rGoods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$rData->goods_idx."'");

				$rSoldout = goodsSoldoutCheck($rGoods); //품절체크
				if($rSoldout) continue;
				$imgSizeKind="goodsimage2";

				if($rGoods->goods_basic_img) $basicImg = wp_get_attachment_image_src($rGoods->goods_basic_img,$imgSizeKind);
				else{
					$imageList=explode(",",$rGoods->goods_add_img);
					if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
					else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
				}
				$url = home_url()."/?bbseGoods=".$rGoods->idx;
				$target = '';
				if($rGoods->goods_external_link_tf == 'view'){
					$url = $rGoods->goods_external_link;
					$target = '_blank'; 
				}
				$rtnStr .="<li id=\"skyBannerList-".$rGoods->idx."\" class=\"swiper-slide\">
								<a target='".$target."' href=\"".$url."\">
								  <img src=\"".$basicImg['0']."\" alt=\"".$rGoods->goods_name."\">
								  <div class='pname'>".$rGoods->goods_name."</div>
								  <div class='price'>".number_format($rGoods->goods_price)."</div>
							  	</a>
						</li>";
			}
		}
		return $rtnStr;
	}
}



if(!function_exists('goodsOptionSerialize')) {
	function goodsOptionSerialize($opt) {
		$goods_option_basicArray['goods_option_title']=$opt['goods_basic_title'];
		$goods_option_basicArray['goods_option_count']=$opt['goods_basic_count'];
		$goods_option_addArray['goods_add_title']=$opt['goods_add_title'];
		$goods_option_addArray['goods_add_count']=$opt['goods_add_count'];
		$goods_option_basic=serialize($goods_option_basicArray);
		$goods_option_add=serialize($goods_option_addArray);
		return array('goods_option_basic'=>$goods_option_basic, 'goods_option_add'=>$goods_option_add);
	}
}

// 적립금 처리
if (!function_exists('bbse_commerce_mileage_insert')) {
	function bbse_commerce_mileage_insert($mode='', $type='', $point='', $etc='') {
		global $wpdb, $current_user;
		$myInfo=bbse_get_user_information();
		if($mode == "" || $type == "" || $point == "") return false;

		$currTime=current_time('timestamp');
		$wpdb->query("INSERT INTO bbse_commerce_earn_log SET earn_mode='".$mode."', earn_type='".$type."', earn_point='".$point."', old_point='".$myInfo->mileage."', user_id='".$myInfo->user_id."', user_name='".$myInfo->name."', etc_idx='".$etc."', reg_date='".$currTime."'");

		if($mode=="OUT") {
			$mileage = $myInfo->mileage - $point;
		}else{
			$mileage = $myInfo->mileage + $point;
		}

		$wpdb->query("UPDATE bbse_commerce_membership SET mileage='".$mileage."' WHERE user_id='".$myInfo->user_id."'");
	}
}


// 글 - 관련 상품 노출
function bbse_commerce_goods_relation_add_post_meta_boxes() {
	global $theme_shortname;
	if(get_option($theme_shortname."_goodsrelation_use")=='U' && plugin_active_check('BBSe_Commerce') == true ){
		add_meta_box('bbse-commerce-goods-relation-meta', __('관련 상품 노출', 'bbse_meta_box_commerce_goods_relation'), 'bbse_meta_box_commerce_goods_relation', 'post', 'normal', 'high');
	}
}
add_action( 'add_meta_boxes', 'bbse_commerce_goods_relation_add_post_meta_boxes' );


function bbse_post_goods_relation_save($post_id, $post, $update) {
	global $post, $theme_shortname;

    if ( isset( $_REQUEST['goods_relation_use'] ) ) {
        update_post_meta( $post_id, 'post_goods_relation_use', sanitize_text_field( $_REQUEST['goods_relation_use'] ) );
    }else{
        update_post_meta( $post_id, 'post_goods_relation_use', sanitize_text_field( '' ) );
	}

    if ( isset( $_REQUEST['goods_post_list'] ) ) {
        update_post_meta( $post_id, 'post_goods_relation', sanitize_text_field( serialize($_REQUEST['goods_post_list']) ) );
	}
	else{
        update_post_meta( $post_id, 'post_goods_relation', sanitize_text_field( '' ) );
	}

}
add_action( 'save_post', 'bbse_post_goods_relation_save', 10, 3 );

function bbse_meta_box_commerce_goods_relation( $post, $box ) {
	global $wpdb, $theme_shortname;

	$dType = "post";

	$display_goods = get_post_meta($post->ID, 'post_goods_relation', true);
	$display_goods_use = get_post_meta($post->ID, 'post_goods_relation_use', true);

	if($display_goods_use == "1") $useChecked='checked="checked"';
	else $useChecked = "";
	ob_start();

	if($display_goods){
		$dsplList=unserialize($display_goods);
		$dspCnt='1';
	}
	else $dspCnt='1';
?>
		<div style="color:red;">10개까지만 등록가능합니다.</div>
		<div style="float:right;margin-right:10px;">
			<a class="button" href="javascript:void(0);" onClick="goods_list_popup('<?php echo $dType;?>','');">진열상품추가</a>
		</div>
		 <div class="clearfix-display"></div>

		<div class="clearfix-display" style="height:40px;"></div>
		
		<input type="hidden" name="dType" id="dType" value="<?php echo $dType;?>" />

		<div class="borderBox-gray">
			<div class="goods-gallery-display">
				<ul id="goods-<?php echo $dType;?>-ul-list_1">
			<?php
				for($t=0;$t<sizeof($dsplList);$t++){
					$tData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$dsplList[$t]."'");
					if($tData->goods_name){
						if($tData->goods_basic_img) $basicImg = wp_get_attachment_image_src($tData->goods_basic_img);
						else{
							$imageList=explode(",",$tData->goods_add_img);
							if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
							else $basicImg['0']=bloginfo('template_url')."/admin/images/image_not_exist.jpg";
						}
			?>
					<li id="goods_img_<?php echo $dType;?>_list_<?php echo $tData->idx;?>" title="<?php echo $tData->goods_name;?>">
						<table style="margin-left:14px;padding:0px;">
							<tr>
								<td style="border-bottom:0px;padding:0px;">
									<div class="thumb">
										<img src="<?php echo $basicImg['0'];?>" alt="상품이미지" />
									</div>
								</td>
								<td style="border-bottom:0px;padding:0px;">
									<span id="popup-li-list-<?php echo $tData->idx;?>"><img src="<?php echo bloginfo('template_url')?>/admin/images/btn_delete.png" onClick="goods_img_link_remove('<?php echo $dType;?>','<?php echo $tData->idx;?>','')" class="deleteBtn" alt="상품 삭제" title="상품 삭제" /></span>
								</td>
							</tr>
						</table>
						<div class="goodsname"><?php echo $tData->goods_name;?><input type="hidden" name="goods_<?php echo $dType;?>_list[]" value="<?php echo $tData->idx;?>" /></div>
					</li>
			<?php
					}
				}
			?>
				</ul>
			</div>
		</div>

<script language="javascript">
	jQuery(function () {
		var dType="<?php echo $dType;?>";
		var dspCnt="<?php echo $dspCnt;?>";

		for(i=1;i<=dspCnt;i++){
			jQuery("#goods-"+dType+"-ul-list_"+i).sortable({
				start: function (event, ui) {
						ui.item.toggleClass("move-highlight");
				},
				stop: function (event, ui) {
						ui.item.toggleClass("move-highlight");
				}
			});
			jQuery("#goods-"+dType+"-ul-list_"+i).disableSelection();
		}
	});

	// 상품 이미지 삭제 시
	function goods_img_link_remove(pTarget,tId,tNo){
		if(tNo && tNo>0) var tNoStr="_"+tNo;
		else var tNoStr="";

		jQuery("#goods_img_"+pTarget+"_list"+tNoStr+"_"+tId).remove();
	}

	// 상품 등록 팝업
	function goods_list_popup(pTarget,tNo){

		var chkList="";

		if(tNo && tNo>0) var tNoStr="_"+tNo;
		else var tNoStr="";

		var tCnt=jQuery("input[name=goods_"+pTarget+"_list"+tNoStr+"\\[\\]]").size();

		for(i=0;i<tCnt;i++){
			if(chkList) chkList +=",";
			chkList +=jQuery("input[name=goods_"+pTarget+"_list"+tNoStr+"\\[\\]]").eq(i).val();
		}
		var tbHeight = window.innerHeight * .85;
		tb_show("상품목록", "<?php echo bloginfo('template_url')?>/admin/theme_option_maingoods-popup-goods-list.php?pTarget="+pTarget+"&#38;height="+tbHeight+"&#38;tNo="+tNo+"&#38;chkList="+chkList+"&#38;TB_iframe=true");
		return false;
	}

	// 상품 등록 팝업 닫기
	function remove_popup(){
		tb_remove();
	}
	</script>


<?php
	$rtnContent = ob_get_contents();
	ob_end_clean();

	echo '
      <table width="100%">
				<tbody>
					<tr valign="top">
						<td style="width:125px;"><label for="goods_relation_use">사용여부 : </label></td>
						<td><input name="goods_relation_use" type="checkbox" id="goods_relation_use" value="1" '.$useChecked.'/> 사용</td>
					</tr>
					<tr valign="top">
						<td style="width:125px;">관련상품 : </td>
						<td>
							'.$rtnContent.'
						</td>
					</tr>
				</tbody>
			</table>';
}
if(!function_exists("bbse_post_goods_display")) {
	function bbse_post_goods_display($post_id) {
		global $wpdb, $theme_shortname;
		if(get_option($theme_shortname."_goodsrelation_use") == "U" && plugin_active_check('BBSe_Commerce') == true) {

			$display_goods = get_post_meta($post_id, 'post_goods_relation', true);
			$display_goods_use = get_post_meta($post_id, 'post_goods_relation_use', true);
			$dsplList=unserialize($display_goods);

			if($display_goods_use == "1" && count($dsplList) >'0' && $dsplList['0']>'0') {
				$dspCnt='1';
				$orderby = "\nORDER BY CASE idx\n";
				foreach($dsplList as $k => $val){
					$orderby .= 'WHEN ' . $val . ' THEN ' . ($k+1) . "\n";
				}
				$orderby .= 'END ';
				$addQuery = " AND idx IN(".implode(",",$dsplList).") ";
				$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE (goods_display='display' OR goods_display='soldout') ".$addQuery.$orderby);
				ob_start();
				?>
				<div class="main_cont post_relation">
					<div class="main_section best_item" style="background:#f7f7f7">
						<h3 class="lv3_title">관련상품</h3>
						<div class="basic_list">
							<ul style="margin:0 0 0 -45px;">
					<?php
							foreach($result as $goods){

								$soldout = goodsSoldoutCheck($goods); //품절체크
								$imgSizeKind = "goodsimage3";

								$imageList=explode(",",$goods->goods_add_img);
								$firstImg=$secondImg="";
								if($goods->goods_basic_img){
									$basicImg = wp_get_attachment_image_src($goods->goods_basic_img,$imgSizeKind);
									$firstImg=$basicImg['0'];
								}
								else{
									if(sizeof($imageList)>'0'){
										$basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
										$firstImg=$basicImg['0'];
									}
									else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
								}

								if($firstImg){
									for($zk=0;$zk<sizeof($imageList);$zk++){
										unset($tmpImg);
										$tmpImg = wp_get_attachment_image_src($imageList[$zk],$imgSizeKind);
										if($imageList[$zk]>'0' && $tmpImg && $tmpImg['0']!=$firstImg){
											$secondImg=$tmpImg['0'];
											break;
										}
									}
								}
					?>

								<li>
									<a href="javascript:void(0);" onClick="go_detail('<?php echo home_url()."/?bbseGoods=".$goods->idx?>');" title="상품상세보기">
										<div class="img_view">
											<img src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
										</div>
										<strong class="subj"><?php echo $goods->goods_name?></strong>
									</a>
								</li>
					<?php
							}
					?>
							</ul>
						</div>
					</div>
				</div>

				<?php
				$rtnContent = ob_get_contents();
				ob_end_clean();
				echo $rtnContent;

			}
		}
	}
}


// 구글 애널리틱스 - 전자상거래 결제정보
function getTransactionJs(&$trans) {
  return <<<HTML
ga('ecommerce:addTransaction', {
  'id'          : '{$trans['id']}',
  'affiliation' : '{$trans['affiliation']}',
  'revenue'     : '{$trans['revenue']}',
  'shipping'    : '{$trans['shipping']}',
  'tax'         : '{$trans['tax']}',
  'currency' : 'KRW'
});

HTML;
}

// 구글 애널리틱스 - 전자상거래 제품정보
function getItemJs(&$transId, &$item) {
  return <<<HTML
ga('ecommerce:addItem', {
  'id'       : '$transId',
  'name'     : '{$item['name']}',
  'sku'      : '{$item['sku']}',
  'category' : '{$item['category']}',
  'price'    : '{$item['price']}',
  'quantity' : '{$item['quantity']}',
  'currency' : 'KRW'

});

HTML;
}

// remove https, Redirect after posting a comment
add_filter('comment_post_redirect', 'bbse_redirect_after_comment');
if (!function_exists('bbse_redirect_after_comment')) {
	function bbse_redirect_after_comment($location){
		global $theme_shortname;

		$ssl_enable=get_option($theme_shortname.'_ssl_enable');
		$ssl_domain=get_option($theme_shortname.'_ssl_domain');

		if($ssl_enable=='U' && $ssl_domain){
			$ckDomain=$ssl_domain;
			$parMd5=md5("http://".$ssl_domain);

			$ckAuthor = get_comment_author( $comment_ID );
			$ckEmail = get_comment_author_email( $comment_ID );
			$ckURL = get_comment_author_url( $comment_ID );

			setcookie("comment_author_".$parMd5, $ckAuthor, strtotime('+3 day'), "/", $ckDomain);
			setcookie("comment_author_email_".$parMd5, $ckEmail, strtotime('+3 day'), "/",$ckDomain);
			setcookie("comment_author_url_".$parMd5, $ckURL, strtotime('+3 day'), "/", $ckDomain);
			$location=str_replace("https","http",$location);
		}

		return $location;
	}
}

if (!function_exists('bbse_get_mallid')) {
	function bbse_get_mallid(){// 쇼핑몰 ID 추출
		global $wpdb;
		$rtnMallid="";
		$tableExits = $wpdb->get_var("show tables like 'bbse_commerce_config'");
		if($tableExits){
			$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='payment'");
			$data=unserialize($confData->config_data);
			$rtnMallid=$data['payment_id'];
		}
		return $rtnMallid;
	}
}

// 네이버 페이 및 비회원 장바구니 사용여부 검사
if(!function_exists("bbse_nPay_check")){
    function bbse_nPay_check(){ 
		global $wpdb;

		$rtnArray=Array();
		$nCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='naverpay'");
		if($nCnt>'0'){
			$nPayConf=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='naverpay' ORDER BY idx DESC LIMIT 1");
			$rtnArray=unserialize($nPayConf->config_data);
		}

		return $rtnArray;
	}
}		

//장바구니 (IP -> ID, 네이버 페이)
if(!function_exists("bbse_cart_IP_to_ID")){
    function bbse_cart_IP_to_ID($uId){ 
		global $wpdb;
		$userIP=$_SERVER['REMOTE_ADDR'];

		$nCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='naverpay'");
		if($nCnt>'0'){
			$nPayConf=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='naverpay'");
			$nPayData=unserialize($nPayConf->config_data);

			$cartCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_cart WHERE  cart_kind='C' AND user_id='".$userIP."'");
			if($nPayData['guest_cart_use']=='on' && $cartCnt>'0'){
				$wpdb->query("UPDATE bbse_commerce_cart SET user_id='".$uId."' WHERE cart_kind='C' AND user_id='".$userIP."'");
			}
		}
	}
}

// GET 파라미터 만들기
if(!function_exists('bbse_append_get_params')){
	function bbse_append_get_params($array, $parent=''){
		$params = array();
		foreach ($array as $k => $v){
			if (is_array($v)){
				$params[] = append_params($v, (empty($parent) ? urlencode($k) : $parent . '[' . urlencode($k) . ']'));
			}
			else{
				$params[] = (!empty($parent) ? $parent . '[' . urlencode($k) . ']' : urlencode($k)) . '=' . urlencode($v);
			}
		}

		return implode('&', $params);
	}
}

// 소셜로그인 환경설정 받아오기
if (!function_exists('bbse_get_social_login_config')) {
	function bbse_get_social_login_config(){
		global $wpdb;
		$returnData=Array();
		$socialData = $wpdb->get_row("select social_login from bbse_commerce_membership_config");
		if($socialData->social_login){
			$returnData=unserialize($socialData->social_login);
		}
		return $returnData;
	}
}

// 통합회원 자동로그인
if (!function_exists('bbse_commerce_auto_login')) {
	function bbse_commerce_auto_login( $username ) {
		ob_start();
		 // log in automatically
		 if ( !is_user_logged_in() ) {
			 $user = get_userdatabylogin( $username );
			 $user_id = $user->ID;
			 wp_set_current_user( $user_id, $user_login );
			 wp_set_auth_cookie( $user_id );
			 do_action( 'wp_login', $user_login );
		 } 
		 ob_end_clean();
	}
}

// 소셜로그인 정보저장
if (!function_exists('bbse_put_social_login_log')) {
	function bbse_put_social_login_log($userInfo){
		global $wpdb;
		$integration['view']="Y";
		$integration['dupCnt']='0';
		$currentLoginDate=current_time('timestamp');

		$snsData=$wpdb->get_row("SELECT * FROM bbse_commerce_social_login WHERE sns_type='".$userInfo['sns_type']."' AND sns_id='".$userInfo['sns_id']."' ORDER BY idx DESC LIMIT 1");
		//$snsCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_social_login WHERE sns_type='".$userInfo['sns_type']."' AND sns_id='".$userInfo['sns_id']."'"); // echo $snsCnt;

		if($userInfo['sns_email']){
			$dupCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_membership WHERE email='".$userInfo['sns_email']."'");
			$integration['dupCnt']=$dupCnt;
		}

		if($snsData->sns_id){
			if($snsData->sns_name) $sqlAdd=", sns_name='".$userInfo['sns_name']."', sns_email='".$userInfo['sns_email']."', sns_gender='".$userInfo['sns_gender']."'";
			else $sqlAdd="";

			$inQuery="UPDATE bbse_commerce_social_login SET login_date='".$currentLoginDate."'".$sqlAdd." WHERE idx='".$snsData->idx."'";
			$wpdb->query($inQuery);
		}
		else{
			$inQuery="INSERT INTO bbse_commerce_social_login (sns_id, sns_type, integrate_yn, login_date) VALUES ('".$userInfo['sns_id']."','".$userInfo['sns_type']."','N','".$currentLoginDate."')";
			$wpdb->query($inQuery);
			$idx = $wpdb->insert_id;
		}

		$preSocialLoginDate=$snsData->login_date;
		$socialCnf=bbse_get_social_login_config();
		$preLoginDate=mktime(date('h',$preSocialLoginDate),date('i',$preSocialLoginDate),date('s',$preSocialLoginDate),date('m',$preSocialLoginDate)+$socialCnf['integration']['integration_period'],date('d',$preSocialLoginDate),date('Y',$preSocialLoginDate));

		if($snsData->integrate_yn=='Y' && $snsData->member_no>'0' && $snsData->member_id){
			$integration['view']="N";
			$integration['integrate']="Y";

			// wp 로그인
			bbse_commerce_auto_login($snsData->member_id);

			$_SESSION['snsLogin']="";
			$_SESSION['snsLoginData']="";
		}
		elseif($socialCnf['integration']['integration_use_yn']=='N' || ($socialCnf['integration']['integration_use_yn']=='Y' && $socialCnf['integration']['integration_view_type']=='P' && $currentLoginDate<=$preLoginDate)){
			$integration['view']="N";
			$integration['integrate']="N";
		}

		return $integration;
	}
}

// 소셜로그인 회원 통합
if (!function_exists('bbse_integrate_social_login')) {
	function bbse_integrate_social_login($memNo, $memId,$currTime){
		global $wpdb;
		$snsLogin=$_SESSION['snsLogin'];
		if($snsLogin=='Y'){
			if($_SESSION['snsLoginData']) $snsLoginData=unserialize($_SESSION['snsLoginData']);
			if($snsLoginData['sns_id']){
				$snsData=$wpdb->get_row("SELECT * FROM bbse_commerce_social_login WHERE sns_id='".$snsLoginData['sns_id']."' AND sns_type='".$snsLoginData['sns_type']."' AND integrate_yn='N'");
				if($snsData->idx>'0'){
					$wpdb->query("UPDATE bbse_commerce_social_login SET integrate_yn='Y', sns_name='".$snsLoginData['sns_name']."', sns_email='".$snsLoginData['sns_email']."', sns_gender='".$snsLoginData['sns_gender']."', member_no='".$memNo."', member_id='".$memId."', integrate_date='".$currTime."' WHERE sns_id='".$snsLoginData['sns_id']."' AND sns_type='".$snsLoginData['sns_type']."' AND integrate_yn='N'");

					$wpdb->query("UPDATE bbse_commerce_cart SET user_id='".$memId."' WHERE user_id='".$snsLoginData['sns_id']."'");
					$wpdb->query("UPDATE bbse_commerce_order SET user_id='".$memId."' WHERE user_id='' AND sns_id='".$snsData->sns_id."' AND sns_idx='".$snsData->idx."'");
					$wpdb->query("UPDATE bbse_commerce_review SET user_id='".$memId."' WHERE user_id='' AND sns_id='".$snsData->sns_id."' AND sns_idx='".$snsData->idx."'");
					$wpdb->query("UPDATE bbse_commerce_qna SET user_id='".$memId."' WHERE user_id='' AND sns_id='".$snsData->sns_id."' AND sns_idx='".$snsData->idx."'");

					$_SESSION['snsLogin']="";
					$_SESSION['snsLoginData']="";
				}
			}
		}
	}
}

// 로그아웃 시 소셜로그인 정보 삭제
if (!function_exists('bbse_logout_social_check')) {
	function bbse_logout_social_check(){
		$_SESSION['snsLogin']="";
		$_SESSION['snsLoginData']="";
		$_SESSION['bbseIntro']=""; // 로그아웃 시 인트로 적용
		$_SESSION['bbseIntroAuth']=""; // 로그아웃 시 인증정보 초기화
	}
}  
add_action('wp_logout','bbse_logout_social_check');

// 소셜로그인 개인정보 저장
if (!function_exists('bbse_insert_social_login')) {
	function bbse_insert_social_login(){
		global $wpdb;

		$snsLogin=$_SESSION['snsLogin'];
		if($snsLogin=='Y'){
			if($_SESSION['snsLoginData']) $snsLoginData=unserialize($_SESSION['snsLoginData']);
			if($snsLoginData['sns_id']){
				$snsCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_social_login WHERE sns_id='".$snsLoginData['sns_id']."' AND sns_type='".$snsLoginData['sns_type']."' AND integrate_yn='N'");
				if($snsCnt>'0'){
					$wpdb->query("UPDATE bbse_commerce_social_login SET sns_name='".$snsLoginData['sns_name']."', sns_email='".$snsLoginData['sns_email']."', sns_gender='".$snsLoginData['sns_gender']."' WHERE sns_id='".$snsLoginData['sns_id']."' AND sns_type='".$snsLoginData['sns_type']."' AND integrate_yn='N'");
				}
			}
		}
	}
}

// SNS 아이콘 표시여부 체크
if (!function_exists('bbse_sns_icon_view')) {
	function bbse_sns_icon_view(){
		$socialCnf=bbse_get_social_login_config();
		if(sizeof($socialCnf)>'0' && ($socialCnf['naver']['naver_use_yn']=='Y' || $socialCnf['facebook']['facebook_use_yn']=='Y' || $socialCnf['google']['google_use_yn']=='Y' || $socialCnf['kakao']['kakao_use_yn']=='Y' || $socialCnf['twitter']['twitter_use_yn']=='Y')){
			if($socialCnf['integration']['sns_icon_use_yn']=='Y') return true;
			else return false;
		}
		else return false;
	}
}

// [Check] intro view
if(!function_exists('bbse_get_intro_view_check')){
	function bbse_get_intro_view_check(){
		global $wp,$wpdb,$theme_shortname;
		$jumpUrl=Array();
		$rtnFlag='intro';

		$refUrl=str_replace("http://","",$_SERVER['HTTP_REFERER']);
		$refUrl=str_replace("https://","",$refUrl);
		$refDom=explode("?",$refUrl);
		$domArr=explode("/",$refDom['0']);
		$tDomain=$domArr['0'];

		if(get_option($theme_shortname."_intro_19_subpage_blocking_check")!='checked'){ // 서브페이지 접근 차단 기능추가로 인한 초기값 설정
			update_option($theme_shortname."_intro_19_subpage_blocking_use",'U');
			update_option($theme_shortname."_intro_19_subpage_blocking_check",'checked');
		}

		if(!is_user_logged_in() && $_SESSION['bbseIntro']!='skip'){
			if($tDomain){
				$jumpUrlArray=explode("\r\n",stripslashes(get_option($theme_shortname."_intro_19_jump_url")));
				for($i=0;$i<sizeof($jumpUrlArray);$i++){
					unset($jumpAst);
					if(strpos($jumpUrlArray[$i],"*.")!==false){
						$jumpAst=explode("*.",trim($jumpUrlArray[$i]));
						if(trim($jumpAst['1']) && preg_match("/".trim($jumpAst['1'])."/i", $tDomain)){
							$rtnFlag='skip';
							break;
						}
					}
					else{
						if(trim($jumpUrlArray[$i]) && preg_match("/".$tDomain."/i", $jumpUrlArray[$i])){
							$rtnFlag='skip';
							break;
						}
					}
				}
			}
		}
		else $rtnFlag='skip';

		if(!is_user_logged_in()){
			$currentUrl=bbse_current_page_url();
			$homeUrl=home_url();
			if(substr($homeUrl,-1)=='/') $homeUrl=substr($homeUrl,0,strlen($homeUrl)-1);
			if($currentUrl == $homeUrl && $_SESSION['bbseIntro']!='skip') $rtnFlag='';
			elseif(get_option($theme_shortname."_intro_19_subpage_blocking_use")!='U') $rtnFlag='skip';
		}

		$_SESSION['bbseIntro']=$rtnFlag;

		if($rtnFlag!='skip' && sizeof($wp->query_vars)>'0'){
			echo "<script language='javascript'>window.location.href='".home_url()."';</script>";
			exit;
		}
	}
}

if(!function_exists('bbse_current_page_url')){
	function bbse_current_page_url() {
		$pageURL = 'http';
		if( isset($_SERVER["HTTPS"]) ) {
			if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		if(substr($pageURL,-1)=='/') $pageURL=substr($pageURL,0,strlen($pageURL)-1);
		return $pageURL;
	}
}

// 관리자 로그인 : 메세지 출력
if(!function_exists('bbse_custom_login_message')){
	function bbse_custom_login_message() {
		global $theme_shortname;
		$txt 	= get_option($theme_shortname.'_wpadmin_txt'); 
		
		$message = "<div style='width:100%;text-align:center;'>".(empty($txt) ? '- 한국형 워드프레스 보부상 e-commerce -':$txt)."</div>";
		return $message;
	}
}
add_filter('login_message', 'bbse_custom_login_message');

// HEX를 RGBA로 변환
if (!function_exists('bbse_hex_to_rgba')) {
	function bbse_hex_to_rgba($color, $opacity = false) {
		$default = 'rgb(0,0,0)';
		if(empty($color)) return $default;

		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
				return $default;
		}

		$rgb =  array_map('hexdec', $hex);

		if($opacity){
			if(abs($opacity) > 1) $opacity = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}
		return $output;
	}
}

// RGBA를 HEX로 변환
if (!function_exists('bbse_htr_gradation')) {
	function bbse_htr_gradation($color, $offset = false) {
		$default = 'rgb(0,0,0)';
		if(empty($color)) return $default;

		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}

		if (strlen($color) == 6) {
				$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
				$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
				return $default;
		}

		$rgb =  array_map('hexdec', $hex);

		if($offset){
			for($i=0; $i<3; $i++){
				$rgb[$i] += $offset;
				if ($rgb[$i] < 0)   $rgb[$i] = 0;
				if ($rgb[$i] > 255) $rgb[$i] = 255;
			}
			$output = 'rgb('.implode(",",$rgb).')';
		}
    $output = 'rgb('.implode(",",$rgb).')';

		return $output;
	}
}

//미디어 링크 URL 자동입력
if(!function_exists('bbse_library_imagelink_setup')){
	function bbse_library_imagelink_setup() {
		$image_set = get_option( 'image_default_link_type' );
		if ($image_set != 'file') {
			update_option('image_default_link_type', 'file');
		}
	}
}
add_action('admin_init', 'bbse_library_imagelink_setup', 10);


// 현재 상품의 카테고리 idx, 카테고리명 추출 (hproduct용)
if (!function_exists('bbse_goods_current_category')) {
	function bbse_goods_current_category($cList){
		global $wpdb;
		$rtnArray=Array();

		$gCate=explode("|",$cList);
		for($c=0;$c<sizeof($gCate);$c++){
			if($gCate[$c]<='0') continue;

			$catData = $wpdb->get_row("SELECT idx,c_name FROM bbse_commerce_category WHERE c_use='Y' AND idx='".$gCate[$c]."'");
			if($catData->idx && $catData->c_name){
				$rtnArray['cIdx']=$catData->idx;
				$rtnArray['cName']=$catData->c_name;
				break;
			}
		}
		return $rtnArray;
	}
}
function my_login_logo() {
	global $theme_shortname;
	$logo 	= get_option($theme_shortname.'_wpadmin_logo_img'); 
	$txt 	= get_option($theme_shortname.'_wpadmin_txt'); 
	$bg 	= get_option($theme_shortname.'_wpadmin_bg'); 
	if(!empty($logo)):
?>
<style>
#login h1 a, .login h1 a {
	background-image: url(<?php echo $logo; ?>);
	width: auto;
    height: 100px;
    background-size: auto;
}
</style>
<?php
	endif; 
	if(!empty($bg)):
?>
<style>
	body{
		background: <?php echo $bg; ?> !important;
	}
</style>
<?php		
	endif;
}
add_action( 'login_enqueue_scripts', 'my_login_logo' ,100);

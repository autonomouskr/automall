<?php
/**
 * Plugin Name: BBS e-Commerce
 * Plugin URI: http://www.bbsetheme.com/plugin-list
 * Description: BBS e-Commerce는 BBS e-theme에서 제작한 쇼핑몰 플러그인 입니다. 회원관리, 상점관리, 상품관리, 상품별 SEO, 주문관리, 한국형결제시스템, 본인인증, 이용후기, 적립금, 메일링, SMS 등 국내 쇼핑몰 환경에 맞춘 풍부한 기능이 포함되어 있습니다.
 * Version: 2.2.5
 * Author: BBS e-Theme
 * Author URI: http://www.bbsetheme.com
 * License: GNU General Public License, v2
 * License URI: http://www.gnu.org/licenses/gpl.html

본 플러그인은 워드프레스와 동일한 GPL 라이센스의 플러그인입니다. 임의대로 수정,삭제 후 이용하셔도 됩니다.
단, 재배포 시 GPL 라이센스로 재배포 되어야 하며, 원 제작자의 표기를 해주시기 바랍니다.
‘BBS e-Commerce' WordPress Plugin, Copyright 2014 BBS e-Theme(http://www.bbsetheme.com)
‘BBS e-Commerce' is distributed under the terms of the GNU GPL
 */

define("BBSE_COMMERCE_VER", "v1.7.4");
define("BBSE_COMMERCE_URL", site_url());
define("BBSE_COMMERCE_PLUGIN_WEB_URL", plugins_url()."/BBSe_Commerce/");
define("BBSE_COMMERCE_PLUGIN_ABS_PATH", plugin_dir_path(__FILE__));

$commerce_upload_dir = wp_upload_dir();
define("BBSE_COMMERCE_UPLOAD_BASE_PATH", $commerce_upload_dir['basedir']."/");
define("BBSE_COMMERCE_UPLOAD_BASE_URL", $commerce_upload_dir['baseurl']."/");

$earnType=Array("review"=>"상품후기","birth"=>"생일축하","member"=>"회원가입","order"=>"상품구매","delete"=>"미사용적립금","cancel"=>"구매취소","admin"=>"관리자 직접");
$payHow=Array("B"=>"무통장입금","C"=>"카드결제","K"=>"실시간계좌이체","V"=>"가상계좌","H"=>"핸드폰결제","EPN"=>"Paynow","EKA"=>"KakaoPay","EPA"=>"PAYCO","EKP"=>"KPAY");
$orderStatus=Array("PR"=>"입금대기","PE"=>"결제완료","DR"=>"배송준비","DI"=>"배송중","DE"=>"배송완료","OE"=>"구매확정","CA"=>"취소신청","CE"=>"취소완료","RA"=>"반품신청","RE"=>"반품완료","EN"=>"정산완료","PW"=>"정산대기","AR"=>"미수","TR"=>"휴지통");
$orderDevice=Array("desktop"=>"데스크탑","tablet"=>"태블릿","mobile"=>"모바일");
$deliveryCompanyList=Array("우체국택배"=>"http://service.epost.go.kr/trace.RetrieveRegiPrclDeliv.postal?sid1=", "CJ대한통운택배"=>"http://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no=", 
											"현대택배"=>"http://www.hlc.co.kr/hydex/jsp/tracking/trackingViewCus.jsp?InvNo=", "한진택배"=>"http://www.hanjin.co.kr/Delivery_html/inquiry/result_waybill.jsp?wbl_num=", 
											"로젠택배"=>"http://d2d.ilogen.com/d2d/delivery/invoice_tracesearch_quick.jsp?slipno=", "경동택배"=>"http://kdexp.com/sub3_shipping.asp?stype=1&yy=&mm=&p_item=", 
											"대신택배"=>"http://home.daesinlogistics.co.kr/daesin/jsp/d_freight_chase/d_general_process2.jsp?billno1=", "이노지스택배"=>"http://www.innogis.net/trace02.asp?invoice=", 
											"편의점택배"=>"http://www.doortodoor.co.kr/jsp/cmn/TrackingCVS.jsp?pTdNo=", "KGB 택배"=>"http://www.kgbls.co.kr//sub5/trace.asp?f_slipno=", 
											"합동택배"=>"http://www.hdexp.co.kr/parcel/order_result_t.asp?stype=1&p_item=", "일양로지스"=>"http://www.ilyanglogis.com/functionality/card_form_waybill.asp?hawb_no=", 
											"천일 택배"=>"http://www.cyber1001.co.kr/kor/taekbae/HTrace.jsp?transNo=", "용마로지스"=>"http://yeis.yongmalogis.co.kr/trace/etrace_yongma.asp?OrdCode=", 
											"GTX로지스"=>"http://www.gtxlogis.co.kr/tracking/default.asp?awblno=", "고려 택배"=>"http://www.klogis.kr/03_business/01_tracking_detail_bcno.asp?bcno=", 
											"다젠"=>"http://www.dazen.co.kr/admin/search/trace_view.asp?buy_no=", "퀵퀵닷컴"=>"http://www.quickquick.com/q/MTrack.php?hawb=", 
											"KG로지스"=>"http://www.kglogis.co.kr/delivery/delivery_result.jsp?item_no= ", 
											"WARPEX"=>"http://packing.warpex.com/api/warpexTrack?wbl=", "WIZWA"=>"http://www.wizwa.co.kr/tracking_exec.php?invoice_no=", 
											"EMS"=>"http://service.epost.go.kr/trace.RetrieveEmsTrace.postal?ems_gubun=E&POST_CODE=", "DHL"=>"http://www.dhl.co.kr/ko/express/tracking.html?brand=DHL&AWB=", 
											"FEDEX"=>"http://www.fedex.com/Tracking?cntry_code=kr&language=korean&action=track&tracknumbers=", "FHL Express"=>"http://www.cjusa.net/sub5/delivery_pod.asp?s_no=", 
											"TNT Express"=>"http://www.tnt.com/webtracker/tracking.do?respCountry=kr&respLang=ko&searchType=CON&cons=", "UPS Korea"=>"http://www.ups.com/WebTracking/track?loc=ko_KR&InquiryNumber1=", 
											"USPS"=>"https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1=");

$sidoEn=Array("서울특별시"=>"SE","부산광역시"=>"BS","대구광역시"=>"DG","인천광역시"=>"IC","광주광역시"=>"GJ","대전광역시"=>"DJ","울산광역시"=>"US","세종특별자치시"=>"SJ","경기도"=>"GG","강원도"=>"GW","충청북도"=>"CB","충청남도"=>"CN","전라북도"=>"JB","전라남도"=>"JN","경상북도"=>"GB","경상남도"=>"GN","제주특별자치도"=>"JJ");
$sigunguEn=Array("종로구"=>"DJ","중구"=>"DJ","용산구"=>"DY","성동구"=>"DS","광진구"=>"DG","동대문구"=>"DD","중랑구"=>"DJ","성북구"=>"DS","강북구"=>"DK","도봉구"=>"DB","노원구"=>"DN","은평구"=>"DE","서대문구"=>"DS","마포구"=>"DM","양천구"=>"DY","강서구"=>"DK","구로구"=>"DG"
    ,"금천구"=>"DG","영등포구"=>"DY","동작구"=>"DD","관악구"=>"DG","서초구"=>"DS","강남구"=>"DK","송파구"=>"DS","강동구"=>"DK","서구"=>"DS","동구"=>"DD","영도구"=>"DY","부산진구"=>"DB","동래구"=>"DD","남구"=>"DN","북구"=>"DB","해운대구"=>"DH","사하구"=>"DS","금정구"=>"DG"
    ,"연제구"=>"DY","수영구"=>"DS","사상구"=>"DS","기장군"=>"GK","수성구"=>"DS","달성군"=>"GD","남동구"=>"DN","부평구"=>"DB","계양구"=>"DG"
    ,"강화군"=>"DJ","옹진군"=>"DJ","광산구"=>"DJ","유성구"=>"DJ","대덕구"=>"DJ","울주군"=>"DJ","조치원읍"=>"DJ","연기면"=>"DJ","연동면"=>"DJ","부강면"=>"DJ","금남면"=>"DJ","달서구"=>"DJ","미추홀구"=>"DJ","연수구"=>"DJ","수원시"=>"SS","의정부시"=>"SU","안양시"=>"SA","성남시"=>"SN"
    ,"부천시"=>"SB","광명시"=>"SG","평택시"=>"SP","동두천시"=>"SD","안산시"=>"SA","고양시"=>"SG","과천시"=>"SC","구리시"=>"SG","남양주시"=>"SN","오산시"=>"SO","시흥시"=>"SH","군포시"=>"SG","의왕시"=>"SW","하남시"=>"SH","용인시"=>"SY","파주시"=>"SP","이천시"=>"SI","안성시"=>"SA"
    ,"김포시"=>"SK","화성시"=>"SH","광주시"=>"SG","양주시"=>"SY","포천시"=>"SP","여주시"=>"SY","춘천시"=>"SC","원주시"=>"SW","강릉시"=>"SK","동해시"=>"SD","태백시"=>"ST","속초시"=>"SS","삼척시"=>"SM","청주시"=>"SC","충주시"=>"SC","제천시"=>"SJ","천안시"=>"SC","공주시"=>"SG"
    ,"보령시"=>"SB","아산시"=>"SA","서산시"=>"SS","논산시"=>"SN","계룡시"=>"SK","전주시"=>"SJ","군산시"=>"SG","익산시"=>"SI","정읍시"=>"SJ","남원시"=>"SN","김제시"=>"SK","목포시"=>"SM","여수시"=>"SY","순천시"=>"SS","나주시"=>"SN","광양시"=>"SG","포항시"=>"SP","경주시"=>"SG"
    ,"김천시"=>"SK","안동시"=>"SA","구미시"=>"SG","영천시"=>"SY","상주시"=>"SS","문경시"=>"SM","경산시"=>"SG","창원시"=>"SC","진주시"=>"SJ","통영시"=>"ST","사천시"=>"SS","김해시"=>"SK","밀양시"=>"SM","거제시"=>"SG","양산시"=>"SY","영주시"=>"SY","당진시"=>"SD"
);

require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."class/function.php");					// 기본 페이지 function
if(!class_exists("nusoap_base")) require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."lib/nusoap.php");

if(is_admin()){
	add_action('init', 'set_bbse_commerce_auto_update');
	add_action('admin_enqueue_scripts', 'set_bbse_commerce_admin_default_js');
	add_action( 'admin_init', 'bbse_commerce_tinymce_shorcode' ); // TinyMCE Buttons 추가

	if(!function_exists('set_bbse_commerce_auto_update')){
		function set_bbse_commerce_auto_update(){
			require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-plugin-update.php");
			$plugin_current_path =  __FILE__ ;
			$plugin_slug = plugin_basename(__FILE__);

			new bbse_commerce_auto_update($plugin_current_path, $plugin_slug);
		}
	}
}

if (!function_exists('set_bbse_commerce_admin_default_js')) {
    function set_bbse_commerce_admin_default_js() {
		wp_register_script('bbse-commerce-admin-script', BBSE_COMMERCE_PLUGIN_WEB_URL.'js/admin-tinymce-common.js', false, '0.0.1');
		wp_enqueue_script('bbse-commerce-admin-script');
		
		wp_enqueue_script('tablesort-js', BBSE_COMMERCE_PLUGIN_WEB_URL.'js/jquery.tablesorter.min.js', array('jquery'));
		wp_enqueue_script('TableDnD', 'https://cdnjs.cloudflare.com/ajax/libs/TableDnD/0.9.1/jquery.tablednd.js');
		
		wp_enqueue_script('jquery-form', BBSE_COMMERCE_PLUGIN_WEB_URL.'js/jquery.form.js', array('jquery'));
		
		wp_localize_script( 'bbse-commerce-admin-script', 'bbse_commerce_var',
			array(
				'home_url' => BBSE_COMMERCE_URL,
				'plugin_url' => BBSE_COMMERCE_PLUGIN_WEB_URL
			)
		);
    }
}

// sesstion start
function bbse_commerce_session_start() {
    global $currentSessionID;
	if(!session_id()){
		session_start();
		$currentSessionID = session_id();
	}
}
add_action('init', 'bbse_commerce_session_start', 1);


// 관리자 메뉴설정
function bbse_commerce_menu(){
	global $wpdb,$current_user;

	bbse_commerce_update_work(); // [최신 DB 체크] DB Field Update Check
	$nLogin_id=$current_user->user_login;
	$menuPerm=Array("member"=>"on","goods"=>"on","order"=>"on","statistics"=>"on","config"=>"on","qna"=>"on","payment"=>"on","inven"=>"on");
	$s_total = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='admin'");

	if($s_total>'0'){
		$result = $wpdb->get_results("SELECT * FROM bbse_commerce_config WHERE config_type='admin' ORDER BY idx DESC");
		foreach($result as $i=>$rstData) {
			$data=unserialize($rstData->config_data);
			if($data['admin_info_use']=='on' && $data['admin_id']==$nLogin_id){
				$menuPerm['member']=$data['admin_menu_member'];
				$menuPerm['goods']=$data['admin_menu_goods'];
				$menuPerm['order']=$data['admin_menu_order'];
				$menuPerm['statistics']=$data['admin_menu_statistics'];
				$menuPerm['config']=$data['admin_menu_config'];
				$menuPerm['qna']=$data['admin_menu_qna'];
				$menuPerm['payment']=$data['admin_menu_payment'];
				$menuPerm['inven']=$data['admin_menu_inven'];
			}
		}
	}

	add_menu_page('BBS e-Commerce', 'BBS e-Commerce', 'administrator', 'BBSe_commerce', 'bbse_commerce_dashboard');
	
	add_submenu_page('BBSe_commerce', '대시보드', '대시보드', 'administrator', 'BBSe_commerce', 'bbse_commerce_dashboard');
	
	if($menuPerm['config']=='on'){
		add_submenu_page('BBSe_commerce', '상점관리', '상점관리', 'administrator', 'bbse_commerce_config', 'bbse_commerce_config');
	}
	if($menuPerm['member']=='on'){
		add_submenu_page('BBSe_commerce', '회원관리', '회원관리', 'administrator', 'bbse_commerce_member', 'bbse_commerce_member');
	}
	if($menuPerm['goods']=='on'){
		add_submenu_page('BBSe_commerce', '상품카테고리관리', '상품카테고리관리', 'administrator', 'bbse_commerce_category', 'bbse_commerce_category');
		add_submenu_page('BBSe_commerce', '상품관리', '상품관리', 'administrator', 'bbse_commerce_goods', 'bbse_commerce_goods');
		add_submenu_page('BBSe_commerce', '상품등록', '상품등록', 'administrator', 'bbse_commerce_goods_add', 'bbse_commerce_goods_add');
		add_submenu_page('BBSe_commerce', '상품순서변경', '상품순서변경', 'administrator', 'bbse_commerce_goods_order', 'bbse_commerce_goods_order');
		
		add_submenu_page('BBSe_commerce', '쿠폰관리', '쿠폰관리', 'administrator', 'bbse_commerce_coupon', 'bbse_commerce_coupon');
	}
	if($menuPerm['order']=='on'){
		add_submenu_page('BBSe_commerce', '주문관리', '주문관리', 'administrator', 'bbse_commerce_order', 'bbse_commerce_order');
	}
	if($menuPerm['qna']=='on'){
		add_submenu_page('BBSe_commerce', '상품문의', '상품문의', 'administrator', 'bbse_commerce_qna', 'bbse_commerce_qna');
		add_submenu_page('BBSe_commerce', '고객상품평', '고객상품평', 'administrator', 'bbse_commerce_review', 'bbse_commerce_review');
	}
	if($menuPerm['statistics']=='on'){
		add_submenu_page('BBSe_commerce', '통계정산관리', '통계정산관리', 'administrator', 'bbse_commerce_statistics', 'bbse_commerce_statistics');
	}
	if($menuPerm['config']=='on'){
		add_submenu_page('BBSe_commerce', '품절상품입고알림목록', '품절상품입고알림목록', 'administrator', 'bbse_commerce_soldout_notice', 'bbse_commerce_soldout_notice');
	}
	if($menuPerm['statistics']=='on'){
		add_submenu_page('BBSe_commerce', '대량 상품등록(CSV)', '대량 상품등록(CSV)', 'administrator', 'bbse_commerce_goods_dbinsert', 'bbse_commerce_goods_dbinsert');
	}
	if($menuPerm['statistics']=='on'){
		add_submenu_page('BBSe_commerce', '회원 DB 이전', '회원 DB 이전', 'administrator', 'bbse_commerce_member_dbinsert', 'bbse_commerce_member_dbinsert');
	}
	if($menuPerm['payment']=='on'){
	    add_submenu_page('BBSe_commerce', '입금확인', '입금확인', 'administrator', 'bbse_commerce_account', 'bbse_commerce_account');
	}
	
	//add inventory menu
	//add_menu_page('BBS e-Inventory', '재고관리', 'administrator', 'BBSe_inventory', 'bbse_commerce_invenState');
	
	if($menuPerm['inven']=='on'){
        add_submenu_page('BBSe_commerce', '재고현황', '재고현황', 'administrator', 'bbse_commerce_invenState', 'bbse_commerce_invenState');
	    add_submenu_page('BBSe_commerce', '입출고관리', '입출고관리', 'administrator', 'bbse_commerce_invenInOut', 'bbse_commerce_invenInOut');
	    add_submenu_page('BBSe_commerce', '제품관리', '제품관리', 'administrator', 'bbse_commerce_inven', 'bbse_commerce_inven');
	    add_submenu_page('BBSe_commerce', '창고관리', '창고관리', 'administrator', 'bbse_commerce_storage', 'bbse_commerce_storage');
	    add_submenu_page('BBSe_commerce', '더존코드관리', '더존코드관리', 'administrator', 'bbse_commerce_douzone', 'bbse_commerce_douzone');
	    add_submenu_page('BBSe_commerce', '일련번호관리', '일련번호관리', 'administrator', 'bbse_commerce_serial', 'bbse_commerce_serial');
	    add_submenu_page('BBSe_commerce', '로케이션관리', '로케이션관리', 'administrator', 'bbse_commerce_location', 'bbse_commerce_location');
	}

}
add_action('admin_menu', 'bbse_commerce_menu');

// 도움말 추가
function bbse_commerce_screen_help($contextual_help, $screen_id, $screen){
	if(!method_exists($screen, 'add_help_tab')) return $contextual_help;
	if(!empty($screen->id)){
		$thisSlug = explode("bbse_", $screen->id);
		switch(end($thisSlug)){
			case "contact_form":
				$screen->add_help_tab(array(
					'id' => 'bbse_contact_form_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Contact Form 폼 리스트 페이지입니다.'
				));
				break;
			case "contact_form_add":
				$screen->add_help_tab(array(
					'id' => 'bbse_contact_form_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Contact Form 폼 추가하기 페이지입니다.'
				));
				break;
			case "contact_form_post":
				$screen->add_help_tab(array(
					'id' => 'bbse_contact_form_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Contact Form 모든글보기 페이지입니다.'
				));
				break;
			case "contact_form_config":
				$screen->add_help_tab(array(
					'id' => 'bbse_contact_form_help_1',
					'title' => '안내',
					'content' => '<br />BBS e-Contact Form 환경설정 페이지입니다.'
				));
				break;
		}
	}
	return $contextual_help;
}
add_filter('contextual_help', 'bbse_commerce_screen_help', 10, 3);

// 상품이미지 비율,사이즈 추출
if(!function_exists('bbse_commerce_theme_headers')){
	function bbse_commerce_theme_headers($headers){
		if (!in_array('Image Rate', $headers))
			$headers[] = 'Image Rate';

		if (!in_array('Image Size', $headers))
			$headers[]='Image Width';
		return $headers;
	}
}
add_filter('extra_theme_headers','bbse_commerce_theme_headers');

// 이미지 사이즈 추가
if ( function_exists( 'add_image_size' ) ) {
		add_image_size( 'goodsimage1', 100);
		add_image_size( 'goodsimage2', 150);
		add_image_size( 'goodsimage3', 200);
		add_image_size( 'goodsimage4', 250);
		add_image_size( 'goodsimage5', 300);
		add_image_size( 'goodsimage6', 400);
		add_image_size( 'goodsimage7', 500);
		add_image_size( 'goodsimage8', 600);
}

function bbse_commerce_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'goodsimage1' => __('상품이미지 1'),
        'goodsimage2' => __('상품이미지 2'),
        'goodsimage3' => __('상품이미지 3'),
        'goodsimage4' => __('상품이미지 4'),
        'goodsimage5' => __('상품이미지 5'),
        'goodsimage6' => __('상품이미지 6'),
        'goodsimage7' => __('상품이미지 7'),
        'goodsimage8' => __('상품이미지 8')
    ) );
}
add_filter( 'image_size_names_choose', 'bbse_commerce_custom_sizes' );

// 이미지 URL 로 DB id 받아오기
if(!function_exists( 'get_attachment_id_from_src' ) ) {
	function get_attachment_id_from_src ($src) {
		global $wpdb;

		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$src'";
		$id = $wpdb->get_var($query);

		if(!$id){
			$reg = "/-[0-9]+x[0-9]+?.(JPG|jpg|JPEG|jpeg|PNG|png|GIF|gif)$/i";
			$src1 = preg_replace($reg,'',$src);

			if($src1 != $src){
				$ext = pathinfo($src, PATHINFO_EXTENSION);
				$src = $src1 . '.' .$ext;
			}

			$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$src'";
			$id = $wpdb->get_var($query);
		}

		return $id;
	}
}

// 글메뉴 숨기기
function bbse_commerce_remove_admin_menus(){
  remove_menu_page( 'edit.php' );                   //Posts
}
//add_action( 'admin_menu', 'bbse_commerce_remove_admin_menus' );

// Plugin 활성화 시
function bbse_commerce_create_tables(){
	global $wpdb;

	$createTable1 = "
		create table if not exists `bbse_commerce_category` (
		`idx` int(10) unsigned NOT NULL auto_increment,
		`depth_1` int(4) default '0',
		`depth_2` int(4) default '0',
		`depth_3` int(4) default '0',
		`c_use` enum('Y','N') default 'N',
		`c_code` varchar(50) default null,
		`c_name` varchar(255) default null,
		`c_rank` int(4) default  '0',
		PRIMARY KEY (`idx`),
		KEY `c_code` (`c_code`)
		) default charset=utf8";
	$wpdb->query($createTable1);

	$existCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`='1'");

	if($existCnt<='0'){
		$wpdb->query("INSERT `bbse_commerce_category`  SET `depth_1`='1', `c_name`='미분류', `c_use`='N', `c_code`='1K1F0S0T', `c_rank`='1'");
	}

	// [네이버 지식쇼핑] add field : goods_naver_shop
	$createTable2 = "
		create table if not exists `bbse_commerce_goods` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`goods_code` varchar(50) default NULL,
			`goods_name` varchar(255) default NULL,
			`goods_display` varchar(20) default NULL,
			`goods_naver_shop` enum('on','off') default 'off',
			`goods_naver_pay` enum('on','off') default 'off',
			`goods_cat_list` varchar(255) default NULL,
			`goods_add_img_cnt` int(10) default NULL,
			`goods_add_img` varchar(255) default NULL,
			`goods_basic_img` varchar(20) default NULL,
			`goods_icon_new` varchar(10) default NULL,
			`goods_icon_best` varchar(10) default NULL,
			`goods_description` text default NULL,
			`goods_detail` text default NULL,
			`goods_unique_code` varchar(255) default NULL,
			`goods_unique_code_display` varchar(20) default NULL,
			`goods_barcode` varchar(100) default NULL,
			`goods_barcode_display` varchar(20) default NULL,
			`goods_location_no` varchar(100) default NULL,
			`goods_company` varchar(100) default NULL,
			`goods_company_display` varchar(10) default NULL,
			`goods_local` varchar(100) default NULL,
			`goods_local_display` varchar(10) default NULL,
			`goods_cprice_display` varchar(10) default NULL,
			`goods_consumer_price` int(10) default NULL,
			`goods_price` int(10) default NULL,
			`goods_tax` int(10) default NULL,
			`goods_tax_display` varchar(10) default NULL,
			`goods_member_price` text default NULL,
			`goods_count_flag` varchar(20) default NULL,
			`goods_count` int(10) default NULL,
			`goods_count_view` enum('on','off') default NULL,
			`goods_add_field` varchar(255) default NULL,
			`goods_option_basic` mediumtext default NULL,
			`goods_option_add` mediumtext default NULL,
			`goods_recommend_use` enum('on','off') default NULL,
			`goods_recommend_list` varchar(255) default NULL,
			`goods_relation_use` enum('on','off') default NULL,
			`goods_relation_list` varchar(255) default NULL,
			`goods_seo_use` enum('on','off') default NULL,
			`goods_seo_title` text default NULL,
			`goods_seo_description` text default NULL,
			`goods_seo_keyword` text default NULL,
			`goods_earn_use` enum('on','off') default NULL,
			`goods_earn` int(10) default NULL,
			`goods_update_date` int(10) default NULL,
			`goods_reg_date` int(10) default NULL,
			PRIMARY KEY (`idx`),
			KEY `goods_code` (`goods_code`),
			KEY `goods_name` (`goods_name`),
			KEY `goods_cat_list` (`goods_cat_list`),
			KEY `goods_reg_date` (`goods_reg_date`)
		) default charset=utf8";
	$wpdb->query($createTable2);

	$createTable4 = "
		create table if not exists `bbse_commerce_goods_option` (
			`goods_idx` int(10) default NULL,
			`goods_option_title` varchar(255) default NULL,
			`goods_option_item_overprice` int(10) default NULL,
			`goods_option_item_count` int(10) default NULL,
			`goods_option_item_unique_code` int(10) default NULL,
			`goods_option_item_display` varchar(10) default NULL,
			`goods_option_item_soldout` varchar(10) default NULL,
			`goods_option_item_rank` int(4) default NULL,
			KEY `goods_idx` (`goods_idx`),
			KEY `goods_option_title` (`goods_option_title`),
			KEY `goods_option_item_count` (`goods_option_item_count`),
			KEY `goods_option_item_soldout` (`goods_option_item_soldout`)
		) default charset=utf8";
	$wpdb->query($createTable4);

	$createTable4 = "
		create table if not exists `bbse_commerce_display` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`display_type` varchar(50) default NULL,
			`display_goods` text default NULL,
			PRIMARY KEY (`idx`),
			KEY `display_type` (`display_type`)
		) default charset=utf8";
	$wpdb->query($createTable4);

	$createTable5 = "
		create table if not exists `bbse_commerce_qna` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`user_id` varchar(50) default null,
			`sns_id` varchar(100) default NULL,
			`sns_idx` int(10) default NULL,
			`user_name` varchar(50) default null,
			`goods_idx` int(10) default '0',
			`goods_name` varchar(255) default null,
			`q_type` enum('Q','A') default 'Q',
			`q_parent` int(10) default '0',
			`q_secret` enum('on','off') default 'off',
			`q_status` enum('ready','answer') default 'ready',
			`q_subject` varchar(255) default  null,
			`q_contents` text default  null,
			`write_date` int(10) default  '0',
			PRIMARY KEY (`idx`),
			KEY `user_id` (`user_id`),
			KEY `goods_idx` (`goods_idx`),
			KEY `q_parent` (`q_parent`)
		) default charset=utf8";
	$wpdb->query($createTable5);

	$createTable6 = "
		create table if not exists `bbse_commerce_review` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`user_id` varchar(50) default null,
			`sns_id` varchar(100) default NULL,
			`sns_idx` int(10) default NULL,
			`user_name` varchar(50) default null,
			`goods_idx` int(10) default '0',
			`goods_name` varchar(255) default null,
			`r_value` int(1) default '0',
			`r_best` enum('Y','N') default 'N',
			`r_subject` varchar(255) default  null,
			`r_contents` text default  null,
			`r_attach_org` varchar(255) default  null,
			`r_attach_new` varchar(255) default  null,
			`r_earn_paid` enum('P','N') default  'N',
			`r_earn_point` int(10) default  null,
			`write_date` int(10) default  '0',
			PRIMARY KEY (`idx`),
			KEY `user_id` (`user_id`),
			KEY `goods_idx` (`goods_idx`)
		) default charset=utf8";
	$wpdb->query($createTable6);

	if(!is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/")){
		@mkdir(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/", 0777);
	}
	if(is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/")){
		@chmod(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/", 0777);
	}

	// 대량 상품등록 이미지 경로
	if(!is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/")){
		@mkdir(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/", 0777);
	}
	if(is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/")){
		@chmod(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/", 0777);
	}

	$createTable7 = "
		create table if not exists `bbse_commerce_config` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`config_type` varchar(20) default null,
			`config_data` mediumtext default null,
			`config_editor` text default  null,
			`config_reg_date` int(10) default  '0',
			PRIMARY KEY (`idx`),
			KEY `config_type` (`config_type`)
		) default charset=utf8";
	$wpdb->query($createTable7);

	$createTable8 = "
		create table if not exists `bbse_commerce_earn_log` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`earn_mode` enum('IN','OUT','OUT-READY') default null,
			`earn_type` varchar(50) default null,
			`earn_point` int(10) default null,
			`old_point` int(10) default null,
			`user_id` varchar(50) default null,
			`user_name` varchar(50) default null,
			`etc_idx` varchar(50) default null,
			`reg_date` int(10) default null,
			PRIMARY KEY (`idx`),
			KEY `earn_mode` (`earn_mode`),
			KEY `earn_type` (`earn_type`),
			KEY `user_id` (`user_id`),
			KEY `etc_idx` (`etc_idx`)
		) default charset=utf8";
	$wpdb->query($createTable8);

	$createTable9 = "
		create table if not exists `bbse_commerce_cart` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`user_id` varchar(100) default NULL,
			`sid` varchar(200) default NULL,
			`cart_kind` enum('C','W') default 'C',
			`goods_idx` int(10) unsigned NOT NULL,
			`goods_option_basic` mediumtext,
			`goods_option_add` mediumtext,
			`remote_ip` varchar(50) default NULL,
			`reg_date` int(10) NOT NULL,
			PRIMARY KEY  (`idx`),
			KEY `user_id` (`user_id`),
			KEY `remote_ip` (`remote_ip`)
		) default charset=utf8";
	$wpdb->query($createTable9);

	$createTable10 = "
		create table if not exists `bbse_commerce_recent` (
			`goods_idx` int(10) unsigned NOT NULL,
			`remote_ip` varchar(50) default NULL,
			`reg_date` int(10) NOT NULL,
			KEY `remote_ip` (`remote_ip`),
			KEY `reg_date` (`reg_date`)
		) default charset=utf8";
	$wpdb->query($createTable10);

	$createTable11 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_membership` (
			`user_no` int(10) unsigned NOT NULL auto_increment,
			`user_class` int(10) default '2',
			`user_id` varchar(20) NOT NULL,
			`user_pass` varchar(255) NOT NULL,
			`auth_type` char(1) NOT NULL,
			`auth_ci` varchar(100) default NULL,
			`auth_di` varchar(100) default NULL,
			`auth_yn` char(1) default NULL,
			`name` varchar(30) default NULL,
			`birth` varchar(10) default NULL,
			`sex` char(1) NOT NULL default '1',
			`zipcode` varchar(10) default NULL,
			`addr1` varchar(80) default NULL,
			`addr2` varchar(50) default NULL,
			`email` varchar(50) default NULL,
			`phone` varchar(20) default NULL,
			`hp` varchar(20) default NULL,
			`job` varchar(20) default NULL,
			`email_reception` char(1) NOT NULL default '0',
			`sms_reception` char(1) NOT NULL default '0',
			`mileage` int(10) default '0',
			`reg_date` int(11) default NULL,
			`last_login` int(11) default NULL,
			`leave_yn` char(1) default '0',
			`leave_date` int(10) default NULL,
			`leave_reason` text,
			`admin_del` char(1) NOT NULL default '0',
			`admin_log` text,
		primary key  (`user_no`)) default charset=utf8";
	$wpdb->query($createTable11);

	$createTable12 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_membership_config` (
			`skinname` varchar(20) default NULL,
			`table_width` varchar(5) NOT NULL default '100',
			`table_align` char(1) NOT NULL default 'L',
			`use_name` char(1) NOT NULL default '1',
			`validate_name` char(1) NOT NULL default '0',
			`use_addr` char(1) NOT NULL default '1',
			`validate_addr` char(1) NOT NULL default '0',
			`use_birth` char(1) NOT NULL default '0',
			`validate_birth` char(1) NOT NULL default '0',
			`use_phone` char(1) NOT NULL default '0',
			`validate_phone` char(1) NOT NULL default '0',
			`use_hp` char(1) NOT NULL default '0',
			`validate_hp` char(1) NOT NULL default '0',
			`use_sex` char(1) NOT NULL default '0',
			`validate_sex` char(1) NOT NULL default '0',
			`use_job` char(1) NOT NULL default '0',
			`validate_job` char(1) NOT NULL default '0',
			`use_zipcode_api` char(1) not null default '0',
			`zipcode_api_module` char(1) not null default '0',
			`zipcode_api_key` varchar(100) default null,
			`id_min_len` int(11) NOT NULL default '5',
			`pass_min_len` int(11) NOT NULL default '5',
			`join_not_id` text,
			`use_join_email` char(1) NOT NULL default '0',
			`from_email` varchar(50) default NULL,
			`from_name` varchar(30) default NULL,
			`join_email_title` varchar(100) default NULL,
			`join_email_content` text,
			`mail_logo` varchar(100) default NULL,
			`join_agreement` text,
			`join_private` text,
			`join_accept` text,
			`use_ssl` char(1) NOT NULL default '0',
			`ssl_domain` varchar(100) default NULL,
			`ssl_port` varchar(10) default NULL,
			`sms_use_yn` char(1) default NULL,
			`sms_080_yn` char(1) default NULL,
			`sms_callback_tel` varchar(50) default NULL,
			`sms_admin_tel` varchar(100) default NULL,
			`sms_join_yn` char(1) default NULL,
			`sms_join_msg` text,
			`sms_join_admin_yn` char(1) default NULL,
			`sms_join_admin_msg` text,
			`sms_order_yn` char(1) default NULL,
			`sms_order_msg` text,
			`sms_order_admin_yn` char(1) default NULL,
			`sms_order_admin_msg` text,
			`sms_delivery_yn` char(1) default NULL,
			`sms_delivery_msg` text,
			`sms_delivery_admin_yn` char(1) default NULL,
			`sms_delivery_admin_msg` text,
			`sms_pay_yn` char(1) default NULL,
			`sms_pay_msg` text,
			`sms_pay_admin_yn` char(1) default NULL,
			`sms_pay_admin_msg` text,
			`certification_yn` enum('Y','N') default 'N',
			`certification_company` char(1),
			`certification_id` varchar(50),
			`certification_pass` varchar(50),
			`certification_key` varchar(100),
			`guest_order` enum('Y','N') default 'Y',
			`guest_order_view` enum('Y','N') default 'Y',
			`join_default_class` int(2) default '2'
		) default charset=utf8";
	$wpdb->query($createTable12);

	$result = $wpdb->get_var("SELECT COUNT(*) FROM `bbse_commerce_membership_config`");
	if(!$result){ // 다음 주소만 사용 가능 (v1.4.9 late)
		$wpdb->query("INSERT INTO `bbse_commerce_membership_config` (`skinname`, `table_width`, `table_align`, `use_name`, `use_addr`, `id_min_len`, `pass_min_len`, `use_zipcode_api`, `zipcode_api_module`) VALUES ('basic', '100', 'L', '1', '1', '5', '5', '1', '2')");
	}

	$createTable13 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_membership_class` (
			`no` int(10) unsigned NOT NULL auto_increment,
			`class_name` varchar(50) NOT NULL,
			`use_sale` enum('Y','N') default 'N' NOT NULL,
			`default` enum('Y','N') default 'N' NOT NULL,
			PRIMARY KEY  (`no`)
		) default charset=utf8";
	$wpdb->query($createTable13);

	$result = $wpdb->get_var("SELECT COUNT(*) FROM `bbse_commerce_membership_class`");
	if(!$result){
		$wpdb->query("INSERT INTO bbse_commerce_membership_class VALUES (1, '관리자', 'N', 'Y')");
		$wpdb->query("INSERT INTO bbse_commerce_membership_class VALUES (2, '일반회원', 'N', 'Y')");
		$wpdb->query("INSERT INTO bbse_commerce_membership_class VALUES (3, '특별회원', 'Y', 'Y')");
	}

	$createTable14 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_pg_agspay` (
			`no` int(10) unsigned NOT NULL auto_increment,
			`AuthTy` varchar(20) default NULL,
			`SubTy` varchar(20) default NULL,
			`rStoreId` varchar(50) default NULL,
			`rOrdNo` varchar(100) default NULL,
			`ProdNm` varchar(150) default NULL,
			`rAmt` int(10) default NULL,
			`OrdNm` varchar(100) default NULL,
			`AGS_HASHDATA` varchar(200) default NULL,
			`rSuccYn` char(1) default NULL,
			`rResMsg` varchar(200) default NULL,
			`rApprTm` varchar(20) default NULL,
			`rBusiCd` varchar(50) default NULL,
			`rApprNo` varchar(50) default NULL,
			`rCardCd` varchar(10) default NULL,
			`rDealNo` varchar(50) default NULL,
			`rCardNm` varchar(50) default NULL,
			`rMembNo` varchar(100) default NULL,
			`rAquiCd` varchar(50) default NULL,
			`rAquiNm` varchar(50) default NULL,
			`ICHE_OUTBANKNAME` varchar(100) default NULL,
			`ICHE_OUTBANKMASTER` varchar(100) default NULL,
			`ICHE_AMOUNT` varchar(100) default NULL,
			`rHP_HANDPHONE` varchar(20) default NULL,
			`rHP_COMPANY` varchar(50) default NULL,
			`rHP_TID` varchar(50) default NULL,
			`rHP_DATE` varchar(20) default NULL,
			`rARS_PHONE` varchar(20) default NULL,
			`rVirNo` varchar(50) default NULL,
			`VIRTUAL_CENTERCD` varchar(20) default NULL,
			`ES_SENDNO` varchar(50) default NULL,
			PRIMARY KEY (`no`)
		) default charset=utf8";
	$wpdb->query($createTable14);

	$createTable15 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_order` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`order_no` varchar(50) default NULL,
			`pay_how` varchar(10) default NULL,
			`ezpay_how` varchar(10) default NULL,
			`pay_info` varchar(255) default NULL,
			`input_name` varchar(100) default NULL,
			`add_earn` int(10) default NULL,
			`order_device` varchar(20) default NULL,
			`order_status` varchar(20) default NULL,
			`order_stock_check` enum('y','n') default 'n',
			`order_status_pre` varchar(20) default NULL,
			`user_id` varchar(50) default NULL,
			`sns_id` varchar(100) default NULL,
			`sns_idx` int(10) default NULL,
			`order_name` varchar(100) default NULL,
			`order_zip` varchar(20) default NULL,
			`order_addr1` varchar(255) default NULL,
			`order_addr2` varchar(255) default NULL,
			`order_phone` varchar(20) default NULL,
			`order_hp` varchar(20) default NULL,
			`order_email` varchar(150) default NULL,
			`receive_name` varchar(100) default NULL,
			`receive_zip` varchar(20) default NULL,
			`receive_addr1` varchar(255) default NULL,
			`receive_addr2` varchar(255) default NULL,
			`receive_phone` varchar(20) default NULL,
			`receive_hp` varchar(20) default NULL,
			`order_comment` text default NULL,
			`goods_total` int(10) default NULL,
			`order_config` mediumtext default null,
			`change_config` mediumtext default null,
			`delivery_add_addr` mediumtext default null,
			`delivery_basic` int(10) default NULL,
			`delivery_add` int(10) default NULL,
			`delivery_total` int(10) default NULL,
			`delivery_add_change` int(10) default NULL,
			`delivery_add_change_date` int(10) default NULL,
			`use_earn` int(10) default NULL,
			`cost_total` int(10) default NULL,
			`admin_comment` text default NULL,
			`order_date` int(10) default NULL,
			`input_date` int(10) default NULL,
			`delivery_company` varchar(20) default NULL,
			`delivery_url` varchar(255) default NULL,
			`delivery_no` varchar(25) default NULL,
			`delivery_ing_date` int(10) default NULL,
			`delivery_end_date` int(10) default NULL,
			`order_end_date` int(10) default NULL,
			`refund_reason` text default NULL,
			`refund_bank_info` varchar(255) default NULL,
			`refund_fees` int(10) default NULL,
			`refund_total` int(10) default NULL,
			`refund_apply_date` int(10) default NULL,
			`refund_end_date` int(10) default NULL,
			PRIMARY KEY (`idx`),
			KEY `order_no` (`order_no`),
			KEY `user_id` (`user_id`),
			KEY `order_name` (`order_name`),
			KEY `cost_total` (`cost_total`)
		) default charset=utf8";
	$wpdb->query($createTable15);

	$createTable16 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_order_detail` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`order_no` varchar(50) default NULL,
			`goods_idx` int(10) default NULL,
			`goods_name` varchar(255) default NULL,
			`goods_unique_code` varchar(255) default NULL,
			`goods_barcode` varchar(100) default NULL,
			`goods_location_no` varchar(100) default NULL,
			`goods_earn` int(10) default NULL,
			`goods_price` int(10) default NULL,
			`goods_basic_img` varchar(20) default NULL,
			`goods_option_basic` mediumtext default NULL,
			`goods_option_add` mediumtext default NULL,
			`goods_basic_total` int(10) default NULL,
			`goods_add_total` int(10) default NULL,
			PRIMARY KEY (`idx`),
			KEY `order_no` (`order_no`),
			KEY `goods_idx` (`goods_idx`),
			KEY `goods_name` (`goods_name`),
			KEY `goods_basic_total` (`goods_basic_total`)
		) default charset=utf8";
	$wpdb->query($createTable16);

	// 이니시스 결제 정보 저장
	$createTable17 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_pg_inicis` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`PayMethod` varchar(20) default NULL,
			`TID` varchar(30) default NULL,
			`ResultCode` varchar(10) default NULL,
			`ResultErrorCode` varchar(10) default NULL,
			`ResultMsg` varchar(255) default NULL,
			`MOID` varchar(100) default NULL,
			`TotPrice` int(10) default NULL,
			`ApplDate` varchar(15) default NULL,
			`ApplTime` varchar(15) default NULL,
			`ApplNum` varchar(20) default NULL,
			`CARD_Quota` varchar(5) default NULL,
			`CARD_Interest` varchar(5) default NULL,
			`CARD_Code` varchar(5) default NULL,
			`CSHR_ResultCode` varchar(30) default NULL,
			`CSHR_Type` varchar(30) default NULL,
			`VACT_Num` varchar(30) default NULL,
			`VACT_BankCode` varchar(5) default NULL,
			`VACT_Date` varchar(15) default NULL,
			`VACT_InputName` varchar(100) default NULL,
			`VACT_Name` varchar(100) default NULL,
			PRIMARY KEY (`idx`),
			KEY `MOID` (`MOID`),
			KEY `ApplNum` (`ApplNum`)
		) default charset=utf8";
	$wpdb->query($createTable17);

	// LGU+ 결제 정보 저장
	$createTable18 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_pg_uplus` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`LGD_RESPCODE` varchar(30) default NULL,
			`LGD_PAYTYPE` varchar(20) default NULL,
			`LGD_PAYNOW_TRANTYPE` varchar(3) default NULL,
			`LGD_RESPMSG` varchar(255) default NULL,
			`LGD_PAYKEY` varchar(30) default NULL,
			`LGD_TID` varchar(30) default NULL,
			`LGD_HASHDATA` varchar(100) default NULL,
			`LGD_PAYDATE` varchar(20) default NULL,
			`LGD_MID` varchar(30) default NULL,
			`LGD_OID` varchar(100) default NULL,
			`LGD_PRODUCTINFO` varchar(255) default NULL,
			`LGD_AMOUNT` int(10) default NULL,
			`LGD_BUYER` varchar(20) default NULL,
			`LGD_BUYERID` varchar(30) default NULL,
			`LGD_BUYERIP` varchar(80) default NULL,
			`LGD_BUYERPHONE` varchar(20) default NULL,
			`LGD_BUYEREMAIL` varchar(100) default NULL,
			`LGD_TRANSAMOUNT` int(10) default NULL,
			`LGD_FINANCECODE` varchar(15) default NULL,
			`LGD_FINANCENAME` varchar(50) default NULL,
			`LGD_CARDACQUIRER` varchar(20) default NULL,
			`LGD_PCANCELFLAG` varchar(3) default NULL,
			`LGD_FINANCEAUTHNUM` varchar(50) default NULL,
			`LGD_VANCODE` varchar(20) default NULL,
			`LGD_CARDNUM` varchar(30) default NULL,
			`LGD_ISPKEY` varchar(50) default NULL,
			`LGD_AFFILIATECODE` varchar(50) default NULL,
			`LGD_CASHRECEIPTNUM` varchar(30) default NULL,
			`LGD_CASHRECEIPTSELFYN` varchar(5) default NULL,
			`LGD_CASHRECEIPTKIND` varchar(5) default NULL,
			`LGD_ACCOUNTNUM` varchar(30) default NULL,
			`LGD_CASTAMOUNT` int(10) default NULL,
			`LGD_CASCAMOUNT` int(10) default NULL,
			`LGD_CASFLAG` varchar(5) default NULL,
			`LGD_CASSEQNO` varchar(30) default NULL,
			`LGD_CLOSEDATE` varchar(30) default NULL,
			`LGD_PAYER` varchar(30) default NULL,
			`LGD_ESCROWYN` varchar(5) default NULL,
			PRIMARY KEY (`idx`),
			KEY `LGD_OID` (`LGD_OID`),
			KEY `LGD_FINANCEAUTHNUM` (`LGD_FINANCEAUTHNUM`)
		) default charset=utf8";
	$wpdb->query($createTable18);

	// 소셜 로그인 정보 저장
	$createTable19 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_social_login` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`sns_id` varchar(50) default NULL,
			`sns_type` varchar(20) default NULL,
			`sns_name` varchar(100) default NULL,
			`sns_email` varchar(150) default NULL,
			`sns_gender` enum('M','F') default NULL,
			`integrate_yn` enum('Y','N') default 'N',
			`member_no` int(10) default NULL,
			`member_id` varchar(50) default NULL,
			`integrate_date` int(10) default NULL,
			`login_date` int(10) default NULL,
			PRIMARY KEY (`idx`),
			KEY `sns_id` (`sns_id`),
			KEY `integrate_yn` (`integrate_yn`),
			KEY `member_id` (`member_id`),
			KEY `login_date` (`login_date`)
		) default charset=utf8";
	$wpdb->query($createTable19);

	// 대량 상품등록 CSV 임시 테이블
	$createTable20 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_csv_goods` (
			`idx` int(10) unsigned NOT NULL auto_increment,
			`goods_idx` int(10) default NULL,
			`goods_code` varchar(50) default NULL,
			`goods_img` varchar(255) default NULL,
			PRIMARY KEY (`idx`),
			KEY `goods_idx` (`goods_idx`),
			KEY `goods_code` (`goods_code`)
		) default charset=utf8";
	$wpdb->query($createTable20);

	// 카카오페이 로그 저장
	$createTable21 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_pg_kakaopay` (                                        
			`idx` int(10) unsigned NOT NULL auto_increment,
			`resultCode` varchar(10) default NULL,
			`resultMsg` varchar(100) default NULL,
			`order_no` varchar(100) default NULL,
			`authDate` varchar(20) default NULL,
			`authCode` varchar(50) default NULL,
			`payMethod` varchar(10) default NULL,
			`mid` varchar(20) default NULL,
			`goodsName` varchar(200) default NULL,
			`buyerName` varchar(50) default NULL,
			`amt` varchar(20) default NULL,
			`tid` varchar(50) default NULL,
			`moid` varchar(100) default NULL,
			`cardName` varchar(100) default NULL,
			`cardQuota` varchar(10) default NULL,
			`cardCode` varchar(10) default NULL,
			`cardInterest` varchar(5) default NULL,
			`cardCl` varchar(5) default NULL,
			`cardBin` varchar(10) default NULL,
			`cardPoint` varchar(5) default NULL,
			`nonRepToken` text default NULL,
			PRIMARY KEY  (`idx`),                                             
			KEY `order_no` (`order_no`)                                              
		) default charset=utf8";
	$wpdb->query($createTable21);

	// 품절상품입고알림
	$createTable22 = "
		CREATE TABLE IF NOT EXISTS `bbse_commerce_soldout_notice` (                                        
			`idx` int(10) unsigned NOT NULL auto_increment,
			`goods_idx` int(10) default NULL,
			`user_id` varchar(100) default NULL,
			`hp` varchar(30) default NULL,
			`sms_yn` enum('Y','N') default 'N',
			`sms_send_date` int(10) default NULL,
			`email` varchar(100) default NULL,
			`email_yn` enum('Y','N') default 'N',
			`email_send_date` int(10) default NULL,
			`reg_date` int(20) default NULL,
			PRIMARY KEY  (`idx`),
			KEY `goods_idx` (`goods_idx`),
			KEY `sms_yn` (`sms_yn`),
			KEY `sms_send_date` (`sms_send_date`),
			KEY `email_yn` (`email_yn`),
			KEY `email_send_date` (`email_send_date`)
		) default charset=utf8";
	$wpdb->query($createTable22);
}
register_activation_hook(__FILE__, 'bbse_commerce_create_tables');

// [네이버 지식쇼핑] DB Field Update Check
function bbse_commerce_update_work(){
	global $wpdb;
	
	//쿠폰 테이블 생성
	$coupon_table = "bbse_commerce_coupon";
	if($wpdb->get_var("SHOW TABLES LIKE '".$coupon_table."'") != $coupon_table) {
		$create_coupon_table = "
			CREATE TABLE IF NOT EXISTS `".$coupon_table."` (
				`idx` int(10) unsigned NOT NULL auto_increment,
				`name` varchar(50) default NULL,
				`sdate` date default NULL,
				`edate` date default NULL,
				`alldate` varchar(20) default NULL,
				
				`min_money` int(10) default NULL,
				`discount_type` varchar(20) default NULL,
				`discount_sel` varchar(20) default NULL,
				`discount` int(10) default NULL,
				
				`product_type` varchar(200) default NULL,
				`product` varchar(200) default NULL,
				`thumb` varchar(100) default NULL,
				
				PRIMARY KEY (`idx`)
			) default charset=utf8";
		$wpdb->query($create_coupon_table);
	}
	$coupon_table = "bbse_commerce_paper_coupon";
	if($wpdb->get_var("SHOW TABLES LIKE '".$coupon_table."'") != $coupon_table) {
		$create_coupon_table = "
			CREATE TABLE IF NOT EXISTS `".$coupon_table."` (
				`idx` int(10) unsigned NOT NULL auto_increment,
				
				`code` varchar(50) default NULL,
				`min_money` int(10) default NULL,
				`discount` int(10) default NULL,
				
				`user` varchar(50) default NULL,
				`status` varchar(50) default NULL,
				
				PRIMARY KEY (`idx`)
			) default charset=utf8";
		$wpdb->query($create_coupon_table);
	}
	$coupon_table = "bbse_commerce_coupon_log";
	if($wpdb->get_var("SHOW TABLES LIKE '".$coupon_table."'") != $coupon_table) {
		$create_coupon_table = "
			CREATE TABLE IF NOT EXISTS `".$coupon_table."` (
				`idx` int(10) unsigned NOT NULL auto_increment,
				
				`order_id` int(10) default NULL,
				`coupon_id` int(10) default NULL,
				`user` varchar(100) default NULL,
				`date` datetime default NULL,
				
				PRIMARY KEY (`idx`)
			) default charset=utf8";
		$wpdb->query($create_coupon_table);
	}
	//1회 최대 개수 추가
	$gb_max_cnt = $wpdb->get_row("describe `bbse_commerce_goods` `max_cnt`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `max_cnt` int after `goods_earn`");
	}
	//이니시스 TID 저장공간 변경
	$gb_product = $wpdb->get_row("DESC bbse_commerce_pg_inicis TID");
	if($gb_product->Type == 'varchar(30)'){
		$wpdb->query("alter table `bbse_commerce_pg_inicis` change `TID` `TID` varchar(50)");
	}
	//주문정보에 할인금액 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_order` `coupon_discount`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_order` add `coupon_discount` int after `use_earn`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_order` `user_discount`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_order` add `user_discount` int after `use_earn`");
	}
	
	//회원등급 정보 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_membership_class` `auto_total`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_membership_class` add `auto_total` int after `use_sale`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_membership_class` `auto_cnt`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_membership_class` add `auto_cnt` int after `use_sale`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_membership_class` `discount`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_membership_class` add `discount` int after `use_sale`");
	}
	
	//상품순서 필드 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `list_order`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `list_order` int(100) after `goods_reg_date`");
	}
	//상품 개별배송비 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_ship_price`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_ship_price` int(100) after `list_order`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_ship_tf`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_ship_tf` varchar(10) after `goods_ship_price`");
	}
	//외부링크 설정 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_external_link`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_external_link` varchar(255) after `goods_ship_tf`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_external_link_tf`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_external_link_tf` varchar(10) after `goods_external_link`");
	}
	//구매별도문의 설정 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_buy_inquiry`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_buy_inquiry` varchar(255) after `goods_external_link_tf`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_buy_inquiry_tf`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_buy_inquiry_tf` varchar(10) after `goods_buy_inquiry`");
	}
	
	//개인통관번호 필드 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_order_detail` `order_pass_num`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_order_detail` add `order_pass_num` varchar(100) after `goods_add_total`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_order` `order_pass_num`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_order` add `order_pass_num` varchar(100) after `refund_end_date`");
	}
	
	//2.0.0 DB업데이트 추가
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_cprice_display`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_cprice_display` text after `goods_local_display`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_tax`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_tax` int(10) after `goods_price`");
	}
	$gb_product = $wpdb->get_row("describe `bbse_commerce_goods` `goods_tax_display`", ARRAY_N);
	if(empty($gb_product[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_tax_display` varchar(10) after `goods_tax`");
	}
	
	$naver_shop = $wpdb->get_row("describe `bbse_commerce_goods` `goods_naver_shop`", ARRAY_N);
	if(empty($naver_shop[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_naver_shop` enum('on','off') default 'off' after `goods_display`");
	}

	$naver_pay = $wpdb->get_row("describe `bbse_commerce_goods` `goods_naver_pay`", ARRAY_N);
	if(empty($naver_pay[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_naver_pay` enum('on','off') default 'off' after `goods_display`");

		$nCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='naverpay'");
		$nPayData=Array();
		if($nCnt>'0'){
			$nPayConf=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='naverpay' ORDER BY idx DESC LIMIT 1");
			$nPayData=unserialize($nPayConf->config_data);
		}

		if($nPayData['naver_pay_use']=='on'){
			$wpdb->query("UPDATE `bbse_commerce_goods` SET goods_naver_pay='on'");
		}
	}

	$naver_shop2 = $wpdb->get_row("describe `bbse_commerce_goods` `goods_update_date`", ARRAY_N);
	if(empty($naver_shop2[0])){
		$wpdb->query("alter table `bbse_commerce_goods` add `goods_update_date` int(10) default NULL after `goods_earn`");
	}

	$social_login = $wpdb->get_row("describe `bbse_commerce_membership_config` `social_login`", ARRAY_N); // 소셜 로그인 환경설정 정보 저장
	if(empty($social_login[0])){
		$wpdb->query("alter table `bbse_commerce_membership_config` add `social_login` text default NULL after `guest_order`");
	}

	$social_order_idx = $wpdb->get_row("describe `bbse_commerce_order` `sns_idx`", ARRAY_N); // 소셜 로그인  주문 정보 저장 (소셜 로그인  idx) 
	if(empty($social_order_idx[0])){
		$wpdb->query("alter table `bbse_commerce_order` add `sns_idx` int(10) default NULL after `user_id`");
	}

	$social_order_id = $wpdb->get_row("describe `bbse_commerce_order` `sns_id`", ARRAY_N); // 소셜 로그인  주문 정보 저장 (소셜 로그인  id) 
	if(empty($social_order_id[0])){
		$wpdb->query("alter table `bbse_commerce_order` add `sns_id` varchar(100) default NULL after `user_id`");
	}

	$social_qna_idx = $wpdb->get_row("describe `bbse_commerce_qna` `sns_idx`", ARRAY_N); // 소셜 로그인  QnA 저장 (소셜 로그인  idx) 
	if(empty($social_order_idx[0])){
		$wpdb->query("alter table `bbse_commerce_qna` add `sns_idx` int(10) default NULL after `user_id`");
	}

	$social_order_id = $wpdb->get_row("describe `bbse_commerce_qna` `sns_id`", ARRAY_N); // 소셜 로그인  QnA 저장 (소셜 로그인  id) 
	if(empty($social_order_id[0])){
		$wpdb->query("alter table `bbse_commerce_qna` add `sns_id` varchar(100) default NULL after `user_id`");
	}

	$social_qna_idx = $wpdb->get_row("describe `bbse_commerce_review` `sns_idx`", ARRAY_N); // 소셜 로그인  Review 저장 (소셜 로그인  idx) 
	if(empty($social_order_idx[0])){
		$wpdb->query("alter table `bbse_commerce_review` add `sns_idx` int(10) default NULL after `user_id`");
	}

	$social_order_id = $wpdb->get_row("describe `bbse_commerce_review` `sns_id`", ARRAY_N); // 소셜 로그인  Review 저장 (소셜 로그인  id) 
	if(empty($social_order_id[0])){
		$wpdb->query("alter table `bbse_commerce_review` add `sns_id` varchar(100) default NULL after `user_id`");
	}

	$order_ezpay_flag = $wpdb->get_row("describe `bbse_commerce_order` `ezpay_how`", ARRAY_N); // 간편결제,  일반결제 구별
	if(empty($order_ezpay_flag[0])){
		$wpdb->query("alter table `bbse_commerce_order` add `ezpay_how` varchar(10) default NULL after `pay_how`");
	}

	$uplus_ezpay_log = $wpdb->get_row("describe `bbse_commerce_pg_uplus` `LGD_PAYNOW_TRANTYPE`", ARRAY_N); // 간편결제,  일반결제 구별
	if(empty($uplus_ezpay_log[0])){
		$wpdb->query("alter table `bbse_commerce_pg_uplus` add `LGD_PAYNOW_TRANTYPE` varchar(3) default NULL after `LGD_PAYTYPE`");
	}

	$guest_order_view = $wpdb->get_row("describe `bbse_commerce_membership_config` `guest_order_view`", ARRAY_N); // 비회원 주문조회
	if(empty($guest_order_view[0])){
		$wpdb->query("alter table `bbse_commerce_membership_config` add `guest_order_view` enum('Y','N') default 'Y' after `guest_order`");
	}

	// 이니시스 결제 정보 저장
	$newTableName_1 = "bbse_commerce_pg_inicis";
	if($wpdb->get_var("SHOW TABLES LIKE '".$newTableName_1."'") != $newTableName_1) {
		$createTable17 = "
			CREATE TABLE IF NOT EXISTS `bbse_commerce_pg_inicis` (
				`idx` int(10) unsigned NOT NULL auto_increment,
				`PayMethod` varchar(20) default NULL,
				`TID` varchar(30) default NULL,
				`ResultCode` varchar(10) default NULL,
				`ResultErrorCode` varchar(10) default NULL,
				`ResultMsg` varchar(255) default NULL,
				`MOID` varchar(100) default NULL,
				`TotPrice` int(10) default NULL,
				`ApplDate` varchar(15) default NULL,
				`ApplTime` varchar(15) default NULL,
				`ApplNum` varchar(20) default NULL,
				`CARD_Quota` varchar(5) default NULL,
				`CARD_Interest` varchar(5) default NULL,
				`CARD_Code` varchar(5) default NULL,
				`CSHR_ResultCode` varchar(30) default NULL,
				`CSHR_Type` varchar(30) default NULL,
				`VACT_Num` varchar(30) default NULL,
				`VACT_BankCode` varchar(5) default NULL,
				`VACT_Date` varchar(15) default NULL,
				`VACT_InputName` varchar(100) default NULL,
				`VACT_Name` varchar(100) default NULL,
				PRIMARY KEY (`idx`),
				KEY `MOID` (`MOID`),
				KEY `ApplNum` (`ApplNum`)
			) default charset=utf8";
		$wpdb->query($createTable17);
	}

	// 유플러스 결제 정보 저장
	$newTableName_2 = "bbse_commerce_pg_uplus";
	if($wpdb->get_var("SHOW TABLES LIKE '".$newTableName_2."'") != $newTableName_2) {
		$createTable18 = "
			CREATE TABLE IF NOT EXISTS `bbse_commerce_pg_uplus` (
				`idx` int(10) unsigned NOT NULL auto_increment,
				`LGD_RESPCODE` varchar(30) default NULL,
				`LGD_PAYTYPE` varchar(20) default NULL,
				`LGD_PAYNOW_TRANTYPE` varchar(3) default NULL,
				`LGD_RESPMSG` varchar(255) default NULL,
				`LGD_PAYKEY` varchar(30) default NULL,
				`LGD_TID` varchar(30) default NULL,
				`LGD_HASHDATA` varchar(100) default NULL,
				`LGD_PAYDATE` varchar(20) default NULL,
				`LGD_MID` varchar(30) default NULL,
				`LGD_OID` varchar(100) default NULL,
				`LGD_PRODUCTINFO` varchar(255) default NULL,
				`LGD_AMOUNT` int(10) default NULL,
				`LGD_BUYER` varchar(20) default NULL,
				`LGD_BUYERID` varchar(30) default NULL,
				`LGD_BUYERIP` varchar(80) default NULL,
				`LGD_BUYERPHONE` varchar(20) default NULL,
				`LGD_BUYEREMAIL` varchar(100) default NULL,
				`LGD_TRANSAMOUNT` int(10) default NULL,
				`LGD_FINANCECODE` varchar(15) default NULL,
				`LGD_FINANCENAME` varchar(50) default NULL,
				`LGD_CARDACQUIRER` varchar(20) default NULL,
				`LGD_PCANCELFLAG` varchar(3) default NULL,
				`LGD_FINANCEAUTHNUM` varchar(50) default NULL,
				`LGD_VANCODE` varchar(20) default NULL,
				`LGD_CARDNUM` varchar(30) default NULL,
				`LGD_ISPKEY` varchar(50) default NULL,
				`LGD_AFFILIATECODE` varchar(50) default NULL,
				`LGD_CASHRECEIPTNUM` varchar(30) default NULL,
				`LGD_CASHRECEIPTSELFYN` varchar(5) default NULL,
				`LGD_CASHRECEIPTKIND` varchar(5) default NULL,
				`LGD_ACCOUNTNUM` varchar(30) default NULL,
				`LGD_CASTAMOUNT` int(10) default NULL,
				`LGD_CASCAMOUNT` int(10) default NULL,
				`LGD_CASFLAG` varchar(5) default NULL,
				`LGD_CASSEQNO` varchar(30) default NULL,
				`LGD_CLOSEDATE` varchar(30) default NULL,
				`LGD_PAYER` varchar(30) default NULL,
				`LGD_ESCROWYN` varchar(5) default NULL,
				PRIMARY KEY (`idx`),
				KEY `LGD_OID` (`LGD_OID`),
				KEY `LGD_FINANCEAUTHNUM` (`LGD_FINANCEAUTHNUM`)
			) default charset=utf8";
		$wpdb->query($createTable18);
	}

	// 소셜 로그인 정보 저장
	$newTableName_3 = "bbse_commerce_social_login";
	if($wpdb->get_var("SHOW TABLES LIKE '".$newTableName_3."'") != $newTableName_3) {
		$createTable19 = "
			CREATE TABLE IF NOT EXISTS `bbse_commerce_social_login` (
				`idx` int(10) unsigned NOT NULL auto_increment,
				`sns_id` varchar(50) default NULL,
				`sns_type` varchar(20) default NULL,
				`sns_name` varchar(100) default NULL,
				`sns_email` varchar(150) default NULL,
				`sns_gender` enum('M','F') default NULL,
				`integrate_yn` enum('Y','N') default 'N',
				`member_no` int(10) default NULL,
				`member_id` varchar(50) default NULL,
				`integrate_date` int(10) default NULL,
				`login_date` int(10) default NULL,
				PRIMARY KEY (`idx`),
				KEY `sns_id` (`sns_id`),
				KEY `integrate_yn` (`integrate_yn`),
				KEY `member_id` (`member_id`),
				KEY `login_date` (`login_date`)
			) default charset=utf8";
		$wpdb->query($createTable19);
	}

	if(!is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/")){
		@mkdir(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/", 0777);
	}
	if(is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/")){
		@chmod(BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/", 0777);
	}

	// 대량 상품등록 이미지 경로
	if(!is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/")){
		@mkdir(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/", 0777);
	}
	if(is_dir(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/")){
		@chmod(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/", 0777);
	}

	// 대량 상품등록 CSV 임시 테이블
	$newTableName_4 = "bbse_commerce_csv_goods";
	if($wpdb->get_var("SHOW TABLES LIKE '".$newTableName_4."'") != $newTableName_4) {
		$createTable20 = "
			CREATE TABLE IF NOT EXISTS `bbse_commerce_csv_goods` (
				`idx` int(10) unsigned NOT NULL auto_increment,
				`goods_idx` int(10) default NULL,
				`goods_code` varchar(50) default NULL,
				`goods_img` varchar(255) default NULL,
				PRIMARY KEY (`idx`),
				KEY `goods_idx` (`goods_idx`),
				KEY `goods_code` (`goods_code`)
			) default charset=utf8";
		$wpdb->query($createTable20);
	}

	// 카카오페이 로그 저장
	$newTableName_5 = "bbse_commerce_pg_kakaopay";
	if($wpdb->get_var("SHOW TABLES LIKE '".$newTableName_5."'") != $newTableName_5) {
		$createTable21 = "
			CREATE TABLE IF NOT EXISTS `bbse_commerce_pg_kakaopay` (                                        
				`idx` int(10) unsigned NOT NULL auto_increment,
				`resultCode` varchar(10) default NULL,
				`resultMsg` varchar(100) default NULL,
				`order_no` varchar(100) default NULL,
				`authDate` varchar(20) default NULL,
				`authCode` varchar(50) default NULL,
				`payMethod` varchar(10) default NULL,
				`mid` varchar(20) default NULL,
				`goodsName` varchar(200) default NULL,
				`buyerName` varchar(50) default NULL,
				`amt` varchar(20) default NULL,
				`tid` varchar(50) default NULL,
				`moid` varchar(100) default NULL,
				`cardName` varchar(100) default NULL,
				`cardQuota` varchar(10) default NULL,
				`cardCode` varchar(10) default NULL,
				`cardInterest` varchar(5) default NULL,
				`cardCl` varchar(5) default NULL,
				`cardBin` varchar(10) default NULL,
				`cardPoint` varchar(5) default NULL,
				`nonRepToken` text default NULL,
				PRIMARY KEY  (`idx`),                                             
				KEY `order_no` (`order_no`)                                              
			) default charset=utf8";
		$wpdb->query($createTable21);
	}

	// 품절상품입고알림
	$newTableName_6 = "bbse_commerce_soldout_notice";
	if($wpdb->get_var("SHOW TABLES LIKE '".$newTableName_6."'") != $newTableName_6) {
		$createTable22 = "
			CREATE TABLE IF NOT EXISTS `bbse_commerce_soldout_notice` (                                        
				`idx` int(10) unsigned NOT NULL auto_increment,
				`goods_idx` int(10) default NULL,
				`user_id` varchar(100) default NULL,
				`hp` varchar(30) default NULL,
				`sms_yn` enum('Y','N') default 'N',
				`sms_send_date` int(10) default NULL,
				`email` varchar(100) default NULL,
				`email_yn` enum('Y','N') default 'N',
				`email_send_date` int(10) default NULL,
				`reg_date` int(20) default NULL,
				PRIMARY KEY  (`idx`),
				KEY `goods_idx` (`goods_idx`),
				KEY `sms_yn` (`sms_yn`),
				KEY `sms_send_date` (`sms_send_date`),
				KEY `email_yn` (`email_yn`),
				KEY `email_send_date` (`email_send_date`)
			) default charset=utf8";
		$wpdb->query($createTable22);
	}

	// 다음 주소만 사용 가능 (v1.4.9 late)
	$chData=$wpdb->get_row("SELECT * FROM `bbse_commerce_membership_config`");
	if($chData->use_zipcode_api != '1' || $chData->zipcode_api_module != '2'){
		$wpdb->query("UPDATE `bbse_commerce_membership_config` SET use_zipcode_api='1', zipcode_api_module='2'");
	}

	$join_default_class = $wpdb->get_row("describe `bbse_commerce_membership_config` `join_default_class`", ARRAY_N); // 회원가입시 기본 등급 설정 (v1.4.9 late)
	if(empty($join_default_class[0])){
		$wpdb->query("alter table `bbse_commerce_membership_config` add `join_default_class` int(2) default '2'");
	}
}

// Plugin 비활성화 시
function bbse_commerce_deactivate(){
	// 필요 시 내용 추가하여 사용



}
register_deactivation_hook(__FILE__, 'bbse_commerce_deactivate');

// Plugin 삭제 시
function uninstall_bbse_commerce_plugin(){
	global $wpdb;
	$wpdb->query("drop table `bbse_commerce_category`");

	/*
	$wpdb->query("drop table `bbse_contact_form`");
	$wpdb->query("drop table `bbse_contact_form_input`");

	$post_tbls_qry = $wpdb->get_results("show tables where Tables_in_".DB_NAME." like 'bbse_contact_form_%_post'", ARRAY_N);
	foreach($post_tbls_qry as $tbls){
		$wpdb->query("drop table `".$tbls[0]."`");
	}
	*/
}
register_uninstall_hook(__FILE__, 'uninstall_bbse_commerce_plugin');

// shortcode 등록
function bbse_commerce_html($atts, $content = null){
	/*
	if(empty($atts['formname'])){
		return '[환경설정 오류!] ShortCode의 폼이름은 필수 입력사항 입니다.';
	}
	$BBSeContactForm = new BBSeContactForm;
	$BBSeContactForm->contactWritePage($atts['formname']);
	*/
}
add_shortcode('bbse_contact_form', 'bbse_commerce_html');

// tinyMCE 비주얼을 기본 선택으로 사용
add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );


/* TinyMCE Buttons  추가 (시작) */
if (!function_exists('bbse_commerce_tinymce_shorcode')) {
	function bbse_commerce_tinymce_shorcode() {
		 if ($_REQUEST['page']=='bbse_commerce_goods_add') { // 글, 페이지 제외
			  add_filter( 'mce_buttons', 'bbse_commerce_register_shortcode_button' ); // 버튼 추가
			  add_filter( 'mce_external_plugins', 'bbse_commerce_add_shortcode_plugin' ); // 버튼의 기능 추가 (Plugin)
		 }
	}
}

if (!function_exists('bbse_commerce_register_shortcode_button')) {
	function bbse_commerce_register_shortcode_button( $buttons ) {
		 array_push( $buttons, "bbse_commerce_shortcode_button" );  // 버튼 선언 (플러그인 .js 에 기능 정의)
		 return $buttons;
	}
}

if (!function_exists('bbse_commerce_add_shortcode_plugin')) {
	function bbse_commerce_add_shortcode_plugin( $plugin_array ) {
		 $plugin_array['bbse_commerce_shortcode_script'] = BBSE_COMMERCE_PLUGIN_WEB_URL.'js/admin-tinymce-shortcode.js'; // 플러그인 :  plugins_url( '/mybuttons.js', __FILE__ ) ;
		 return $plugin_array;
	}
}

// shortcode content 추출
if (!function_exists('bbse_commerce_tinymce_shortcord_parse')){
	function bbse_commerce_tinymce_shortcord_parse($content=null){
		$uTubeCnt=preg_match_all("/\[bbse_commerce_youtube .*?\]/is",$content,$uTubeList);
		if($uTubeCnt>'0'){
			$content=bbse_commerce_get_youtube_oembed($content,$uTubeList);
		}
		return trim ( $content );
	}
}
/* TinyMCE Buttons  추가 (끝) */

// [Parse] Youtube url
if (!function_exists('bbse_commerce_get_youtube_oembed')){
	function bbse_commerce_get_youtube_oembed($pContents,$tUrl){
		$ptn_src="/src=[\"'].*?[\"']/is";
		$ptn_width="/width=[\"'].*?[\"']/is";
		$ptn_height="/height=[\"'].*?[\"']/is";
		$ptn_autoplay="/autoplay=[\"'].*?[\"']/is";

		for($u=0;$u<sizeof($tUrl['0']);$u++){
			unset($uScr);
			unset($uWidth);
			unset($uHeight);
			unset($uAutoplay);
			$uEmbed="";

			preg_match($ptn_src,$tUrl['0'][$u],$uScr);
			preg_match($ptn_width,$tUrl['0'][$u],$uWidth);
			preg_match($ptn_height,$tUrl['0'][$u],$uHeight);
			preg_match($ptn_autoplay,$tUrl['0'][$u],$uAutoplay);

			$uScr['0']=str_replace("\"","",$uScr['0']);
			$uScr['0']=str_replace("'","",$uScr['0']);
			$uScr['0']=str_replace("src=","",$uScr['0']);
			$uScr['0']=str_replace("SRC=","",$uScr['0']);

			$uWidth['0']=str_replace("\"","",$uWidth['0']);
			$uWidth['0']=str_replace("'","",$uWidth['0']);
			$uWidth['0']=str_replace("px","",strtolower($uWidth['0']));
			$uWidth['0']=str_replace("width=","",strtolower($uWidth['0']));

			$uHeight['0']=str_replace("\"","",$uHeight['0']);
			$uHeight['0']=str_replace("'","",$uHeight['0']);
			$uHeight['0']=str_replace("px","",strtolower($uHeight['0']));
			$uHeight['0']=str_replace("height=","",strtolower($uHeight['0']));

			$uAutoplay['0']=str_replace("\"","",$uAutoplay['0']);
			$uAutoplay['0']=str_replace("'","",$uAutoplay['0']);
			$uAutoplay['0']=str_replace("autoplay=","",strtolower($uAutoplay['0']));

			if($uScr['0'] && $uWidth['0']){
				//$uEmbed=wp_oembed_get($uScr['0'], Array('width'=>$uWidth['0'], 'height'=>$uHeight['0']));

				$uEmbed=bbse_commerce_parse_youtube_url($uScr['0'],'embed', $uWidth['0'],$uHeight['0'],0,$uAutoplay['0']);
				if($uEmbed) $pContents=str_replace($tUrl['0'][$u],$uEmbed,$pContents);
			}
		}

		return $pContents;
	}
}

// [Make] iframe for youtube url
if (!function_exists('bbse_commerce_parse_youtube_url')){
	function bbse_commerce_parse_youtube_url($url,$return='embed',$width='',$height='',$rel=0,$autoplay=0){
		if($width<='0') $width='560';

		$urls = parse_url($url);
		//url is http://youtu.be/xxxx
		if($urls['host'] == 'youtu.be'){
			$id = ltrim($urls['path'],'/');
		}
		//url is http://www.youtube.com/embed/xxxx
		else if(strpos($urls['path'],'embed') == 1){
			$id = end(explode('/',$urls['path']));
		}
		 //url is xxxx only
		else if(strpos($url,'/')===false){
			$id = $url;
		}
		else{
			parse_str($urls['query']);
			$id = $v;
			if(!empty($feature)){
				$id = end(explode('v=',$urls['query']));
			}
		}
		//return embed iframe
		if($return == 'embed'){
			if($id && ($width || $height)){
				 if($height>'0')  $widthCSS="width:".$width."px;";
				 else $widthCSS="";

				 if($height>'0')  $heightCSS="height:".$height."px;";
				 else $heightCSS="";

				return '<iframe src="'.esc_url('http://www.youtube.com/embed/'.$id.'?rel='.$rel.'&autoplay='.$autoplay).'" allowfullscreen style="'.$widthCSS.$heightCSS.'z-index:3333;border:none"></iframe>';
			}
			else return "";
		}
		//return normal thumb
		else if($return == 'thumb'){
			if($id) return 'http://i1.ytimg.com/vi/'.$id.'/default.jpg';
			else return "";
		}
		//return hqthumb
		else if($return == 'hqthumb'){
			if($id) return 'http://i1.ytimg.com/vi/'.$id.'/hqdefault.jpg';
			else return "";
		}
		// else return id
		else{
			if($id) return $id;
			else return "";
		}
	}
}

// 모든 사용자 - 사용자 편집 시 BBSe-Commerce의 membership 데이터 변경
if (!function_exists('bbse_commerce_updated_user_profile')){
	function bbse_commerce_updated_user_profile( $user_id, $old_user_data ){
		global $wpdb;

		$upSql="";
		$user = get_userdata( $user_id );

		// 모든 사용자 - 사용자 편집 : 비밀번호 변경이 이루어진 경우 만 $_POST['pass1'] 값이 전달 됨
		if (isset($_POST['pass1']) && $_POST['pass1']) $upSql .="user_pass=password('".$_POST['pass1']."')";

		if (isset($_POST['email']) && $_POST['email']){
			if($upSql) $upSql .=", ";
			$upSql .="email='".$_POST['email']."'";
		}

		if($upSql){
			$wpdb->query("UPDATE bbse_commerce_membership SET ".$upSql." WHERE user_id='".$user->user_login."'");
		}
	}
}
add_action( 'profile_update', 'bbse_commerce_updated_user_profile', 10, 2 );

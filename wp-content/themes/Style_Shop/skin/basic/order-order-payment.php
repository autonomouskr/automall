<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* Order */
get_header();

global $current_user,$theme_shortname;

$customPriceView=(get_option($theme_shortname."_config_goods_consumer_price_view"))?get_option($theme_shortname."_config_goods_consumer_price_view"):"U"; // 소비자가 노출여부

wp_get_current_user();
$currUserID=$current_user->user_login;
$Loginflag='memer';

$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사
if($nPayData['guest_cart_use']=='on' && !$currUserID){
	$Loginflag='guest';
	$currUserID=$_SERVER['REMOTE_ADDR'];
}
elseif($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsData=unserialize($_SESSION['snsLoginData']);
		$Loginflag='guest';
		$currUserID=$snsData['sns_id'];
	}
}

$bbsePage=get_query_var( 'bbsePage' );

emptyCart($currUserID); //보관일 지난 상품 삭제
updateCart($currUserID); //장바구니 업데이트

//공통 작업 (시작)
$V = $_POST;

if(!$V['pay_how'] || !$V['order_no'] || ($V['goods_option_price']+$V['goods_add_price']+$V['delivery_price']+$V['delivery_add_price'])<='0'){
	echo "<script>alert('잘못 된 접근입니다.    ');location.href='".home_url()."/';</script>";
	exit;
}


$gidx=unserialize(base64_decode($V['gidx']));

if(is_user_logged_in() && $V['goods_info']!="" || !is_user_logged_in() && $V['goods_info']!="") {//회원,비회원 바로구매 처리 (로그인 전)
	$goods_info = unserialize(base64_decode($V['goods_info']));
	$goodsInfo = $V['goods_info'];
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE idx='".$goods_info['goods_idx']."'");
	$orderType = "direct";
}else if(is_user_logged_in() && $V['goods_idx'] > 0) {//회원 바로구매 처리 (로그인 후)
	$goodsInfo = base64_encode(serialize($V));
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE idx='".$V['goods_idx']."'");
	
}else if(!is_user_logged_in() && $V['goods_info']=="" && count($gidx)<='0') {
	echo "<script>location.href='".home_url()."/';</script>";
	exit;
}else{
	if(count($gidx) > 0) {//회원 장바구니 구매 처리
		$result = $wpdb->get_results("SELECT C.idx AS cart_idx, C.user_id, C.sid, C.goods_option_basic AS cart_option_basic, C.goods_option_add AS cart_option_add, C.remote_ip, C.reg_date, G.* FROM bbse_commerce_cart AS C, bbse_commerce_goods AS G WHERE C.goods_idx=G.idx AND C.cart_kind='C' AND C.user_id='".$currUserID."' AND C.idx IN (".implode(",",$gidx).") ORDER BY C.idx DESC");
		$orderType = "cart";
	}else{
		echo "<script>location.href='".home_url()."/';</script>";
		exit;
	}
}

if(count($result) < 1) {
	echo "<script>location.href='".home_url()."/';</script>";
	exit;
}

$orderList=bbse_append_get_params($V, "");
?>
<script language="javascript">
	jQuery(document).ready(function() {
		jQuery(".layer_delivery").hide();
		jQuery("#delivery_info_view").bind('click', function () {
			jQuery(".layer_delivery").show();
		});

		jQuery(".layer_delivery .layer_del").bind('click', function () {
			jQuery(".layer_delivery").hide();
		});

		jQuery(".back-action").click(function() {//이전페이지
			//history.back();
			location.href=common_var.home_url;
		});
	});
</script>
<?php
//공통 작업 (끝)


$ezpayArray=Array("EPN","EKA","EPA","EKP","NPAY");

if(in_array($V['pay_how'],$ezpayArray)){ // 간편결제 : Paynow, KakaoPay, PAYCO, KPAY
	$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='ezpay'");
	if(!$paymentConfig) {
		echo "<script>alert('관리자에서 간편결제 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
		exit;
	}

	$payCFG = unserialize($paymentConfig);

	if($V['pay_how']=='EPN' && $payCFG['paynow']['paynow_use_yn']=='Y'){
		$StoreId = $payCFG['paynow']['paynow_mert_id'];     //상점아이디
		$escrowYN = ($payCFG['paynow']['paynow_escrow_yn']=='Y')?"Y":"N";     //에스크로 사용여부
		$XpayMertType = $payCFG['paynow']['paynow_mert_type'];     //테스트 여부(LG U+ 전용)
		$XpayMertKey = $payCFG['paynow']['paynow_mert_key'];     //상점 MertKey(LG U+ 전용)
		$shopLogo = (get_option($theme_shortname."_basic_logo_type_top")=="image" && get_option($theme_shortname."_basic_logo_img_top"))?get_option($theme_shortname."_basic_logo_img_top"):"";     //상점로고

		require_once("order-order-payment-lguplusXPay.php"); // 해당 PG 사의 연동 모듈 페이지 호출
	}
	elseif($V['pay_how']=='EKA' && $payCFG['kakaopay']['kakaopay_use_yn']=='Y'){
		//인증,결제 및 웹 경로
		$CNSPAY_WEB_SERVER_URL = "https://kmpay.lgcns.com:8443";
		$targetUrl = "https://kmpay.lgcns.com:8443";
		$msgName = "/merchant/requestDealApprove.dev";
		$CnsPayDealRequestUrl = "https://pg.cnspay.co.kr:443";

		$MID =$payCFG['kakaopay']['kakaopay_mert_id'];     //상점아이디 (테스트 : INIpayTest)
		$cancelPwd = $payCFG['kakaopay']['kakaopay_cancel_pw'];     //거래취소 비밀번호
		$merchantEncKey = $payCFG['kakaopay']['kakaopay_auth_enckey'];     //인증요청용 EncKey
		$merchantHashKey = $payCFG['kakaopay']['kakaopay_auth_hashkey'];     //인증요청용 HashKey
		$merchantKey = $payCFG['kakaopay']['kakaopay_mert_key'];     //상점키

		require_once("order-order-payment-KakaoPay.php"); // 해당 PG 사의 연동 모듈 페이지 호출
	}
	elseif($V['pay_how']=='NPAY'){
		require_once("order-order-payment-NPay.php"); // 해당 PG 사의 연동 모듈 페이지 호출
	}
	elseif($V['pay_how']=='EPA' && $payCFG['payco']['payco_use_yn']=='Y'){

	}
	elseif($V['pay_how']=='EKP' && $payCFG['kpay']['kpay_use_yn']=='Y'){
		$StoreId = $payCFG['kpay']['kpay_mert_id'];     //상점아이디 (테스트 : INIpayTest)
		require_once("order-order-payment-INIpay50.php"); // 해당 PG 사의 연동 모듈 페이지 호출
	}
	else{
		echo "<script>alert('관리자에서 간편결제 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
		exit;
	}
}
elseif($V['pay_how']=='C' || $V['pay_how']=='K' || $V['pay_how']=='V'){ // 신용카드, 계좌이체, 가상계좌
	// 결제모듈 설정
	$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
	if(!$paymentConfig) {
		echo "<script>alert('관리자에서 결제모듈 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
		exit;
	}

	$payCFG = unserialize($paymentConfig);
	$pg_kind = ($payCFG['payment_agent'])?$payCFG['payment_agent']:"allthegate";
	if($pg_kind){
		$StoreId = $payCFG['payment_id'];     //상점아이디 (테스트 : INIpayTest)
		$escrowYN = ($payCFG['payment_escrow_use']=='on')?"Y":"N";     //에스크로 사용여부
		$XpayMertType = $payCFG['payment_mert_type'];     //테스트 여부(LG U+ 전용)
		$XpayMertKey = $payCFG['payment_mert_key'];     //상점 MertKey(LG U+ 전용)

		$shopLogo = (get_option($theme_shortname."_basic_logo_type_top")=="image" && get_option($theme_shortname."_basic_logo_img_top"))?get_option($theme_shortname."_basic_logo_img_top"):"";     //상점로고

		if($payCFG['payment_agent']=='INIpay50'){
			$inicisStandardFlag='N'; // 웹 표준 적용
			if($V['deviceType']=='desktop'){
				if($V['pay_how']=='C' && $payCFG['payment_inicis_nonActiveX_use']=='Y') $inicisStandardFlag='Y'; // 신용카드 && nonActiveX
				elseif($V['pay_how']=='K'){// 실시간계좌이체
					if($payCFG['payment_inicis_escorw_use']=='on'){ // 에스크로 사용함
						if($payCFG['payment_inicis_escrow_nonActiveX_use']=='Y' && $payCFG['payment_inicis_escorw_trans']=='on') $inicisStandardFlag='Y'; // 에스크로 nonActiveX && 실시간계좌이체 에스크로 적용선택
					}
					else{ // 에스크로 사용안함
						if($payCFG['payment_inicis_nonActiveX_use']=='Y') $inicisStandardFlag='Y'; // nonActiveX
					}
				}
				elseif($V['pay_how']=='V'){// 가상계좌
					if($payCFG['payment_inicis_escorw_use']=='on'){ // 에스크로 사용함
						if($payCFG['payment_inicis_escrow_nonActiveX_use']=='Y' && $payCFG['payment_inicis_escorw_vbank']=='on') $inicisStandardFlag='Y'; // 에스크로 nonActiveX && 가상계좌 에스크로 적용선택
					}
					else{ // 에스크로 사용안함
						if($payCFG['payment_inicis_nonActiveX_use']=='Y') $inicisStandardFlag='Y'; // nonActiveX
					}
				}
			}
		}

		if($payCFG['payment_agent']=='INIpay50' && $inicisStandardFlag=='Y'){ // 이니시스 & 웹표준(데스크탑 만 사용가능)
			require_once("order-order-payment-INIpayStandard.php"); // 이니스트 웹표준 적용
		}
		else{
			require_once("order-order-payment-".$pg_kind.".php"); // 해당 PG 사의 연동 모듈 페이지 호출
		}
	}
}
else{
	echo "<script>alert('관리자에서 결제모듈 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
	exit;
}

get_footer();

<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='ezpay'");
$payCFG = unserialize($paymentConfig);

$admin_key 	= $payCFG['kakaopay']['kakaopay_auth_enckey'];
$secret		= $payCFG['kakaopay']['kakaopay_mert_key'];
$CID		= $payCFG['kakaopay']['kakaopay_mert_id'];

$ready_url = 'https://kapi.kakao.com/v1/payment/approve';
$response = wp_remote_post( $ready_url, array(
	'method' 	=> 'POST',
	'headers' 	=> array(
		'Authorization'=> 'KakaoAK '.$admin_key,
	),
	'body' 		=> array(
		'cid'						=> $CID,
		'cid_secret'				=> $secret,
		'tid'						=> $_COOKIE['kakao_tid'],
		'partner_order_id'			=> $_COOKIE['kakao_order_id'],
		'partner_user_id'			=> get_current_user_id(),
		
		'pg_token'					=> $_GET['pg_token'],
		//'payload'					=> '',
		//'total_amount'			=> '',
	),
    )
);
$result = json_decode($response['body'],TRUE);
//성공
if($response['response']['code'] != 200){
?>
<script>
	alert("<?php echo $result['extras']['method_result_message'] ?>");
	var childWindow = window.parent;
     //var parentWindow = childWindow.opener;
     //parentWindow.parent.location.replace("");
     childWindow.close();
</script>
<?php	
	exit;
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body onload="javascript:frmLGCNS_pay_ing.submit();">
<form name="frmLGCNS_pay_ing" method="post" action="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/proc/order.exec.php">
<!-- 각 결제 공통 사용 변수 -->
<input type="hidden" name="resultCode" value="3001">		<!-- 결과코드 : 3001=>결제성공 -->
<input type="hidden" name="resultMsg" value="카카오결제성공">		<!-- 결과메세지 : 카드 결제 성공-->
<input type="hidden" name="authDate" value="<?php echo $result['approved_at'];?>">		<!-- 승인일시 : 151029103244 -->
<input type="hidden" name="authCode" value="<?php echo $result['aid'];?>">		<!-- 승인번호 : 55997745 -->
<input type="hidden" name="payMethod" value="<?php echo $result['payment_method_type'];?>">		<!-- 결제 수단 : CARD -->
<input type="hidden" name="mid" value="<?php echo $result['cid'];?>">		<!-- 가맹점ID -->
<input type="hidden" name="goodsName" value="<?php echo $result['item_name'];?>">		<!-- 상품명 -->
<input type="hidden" name="buyerName" value="<?php echo $result['partner_user_id'];?>">		<!-- 구매자명 -->
<input type="hidden" name="amt" value="<?php echo $result['amount']['total']?>">		<!-- 금액 : 6330 -->
<input type="hidden" name="tid" value="<?php echo $result['tid']?>">		<!-- 거래아이디 : KHNY00000m01011510291032447090 -->
<input type="hidden" name="moid" value="<?php echo $result['partner_order_id']?>">		<!-- 주문번호 : 1320151029013153 -->

<!--CARD 결제 정보-->
<input type="hidden" name="cardName" value="<?php echo $result['card_info']['purchase_corp'];?>">		<!-- 카드사명 : 비씨 -->
<input type="hidden" name="cardQuota" value="<?php echo $result['card_info']['install_month'];?>">		<!-- 할부개월 : 00 -->
<input type="hidden" name="cardCode" value="<?php echo $result['card_info']['purchase_corp_code'];?>">		<!-- 카드사 코드 : 01 -->
<input type="hidden" name="cardInterest" value="<?php echo $result['card_info']['interest_free_install'];?>">		<!-- 무이자 여부 : 0 : 일반, 1: 무이자 -->
<input type="hidden" name="cardCl" value="<?php echo $result['card_info']['card_type'];?>">		<!-- 체크카드여부 : 0 : 신용카드, 1: 체크카드 -->
<input type="hidden" name="cardBin" value="<?php echo $result['card_info']['bin'];?>">		<!-- 카드BIN번호 : 카드번호 앞8자리  -->
<input type="hidden" name="cardPoint" value="-1">		<!-- 카드사포인트사용여부 : 0 : 사용안함, 1: 사용함  -->
<input type="hidden" name="nonRepToken" value="<?php echo $result['card_info']['approved_id'];?>">		<!-- 부인방지토큰  -->

<!--추가정보-->
<input type="hidden" name="orderList" value="<?php echo $_COOKIE['kakao_order_list']; ?>">		<!-- 상품목록 -->
<input type="hidden" name='pay_how' id='pay_how' value='EKA' />	<!-- 결제방법 -->
</form>
</body>  
</html>
<?php
//쿠키삭제
$_COOKIE['kakao_tid'] = '';
$_COOKIE['kakao_order_id'] = '';
$_COOKIE['kakao_order_list'] = '';
?>
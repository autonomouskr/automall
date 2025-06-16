<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='ezpay'");
$payCFG = unserialize($paymentConfig);

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

$LogDir = "";

require_once "./lib/lgcns_CNSpay.php";

// 로그 저장 위치 지정
$connector = new CnsPayWebConnector($LogDir);
$connector->CnsActionUrl($CnsPayDealRequestUrl);
$connector->CnsPayVersion($phpVersion);

// 요청 페이지 파라메터 셋팅
$connector->setRequestData($_REQUEST);

// 추가 파라메터 셋팅
$connector->addRequestData("actionType", "PY0");  						// actionType : CL0 취소, PY0 승인, CI0 조회
$connector->addRequestData("MallIP", $_SERVER['REMOTE_ADDR']);	// 가맹점 고유 ip
$connector->addRequestData("CancelPwd", $cancelPwd);

//가맹점키 셋팅 (MID 별로 틀림)
$connector->addRequestData("EncodeKey", $merchantKey);

// 4. CNSPAY Lite 서버 접속하여 처리
$connector->requestAction();

// 5. 결과 처리
$buyerName = $_REQUEST["BuyerName"];   						// 구매자명
$goodsName = $_REQUEST["GoodsName"]; 						// 상품명
// $buyerName = iconv("euc-kr", "utf-8", $connector->getResultData("BuyerName"));		// 구매자명
// $goodsName = iconv("euc-kr", "utf-8", $connector->getResultData("GoodsName"));		// 상품명

$resultCode = $connector->getResultData("ResultCode"); 		// 결과코드 (정상 :3001 , 그 외 에러)
$resultMsg = $connector->getResultData("ResultMsg");   		// 결과메시지
$authDate = $connector->getResultData("AuthDate");   			// 승인일시 YYMMDDHH24mmss
$authCode = $connector->getResultData("AuthCode");   		// 승인번호
$payMethod = $connector->getResultData("PayMethod");  		// 결제수단
$mid = $connector->getResultData("MID");  						// 가맹점ID
$tid = $connector->getResultData("TID");  							// 거래ID
$moid = $connector->getResultData("Moid");  					// 주문번호
$amt = $connector->getResultData("Amt");  						// 금액
$cardCode = $connector->getResultData("CardCode");			// 카드사 코드
$cardName = $connector->getResultData("CardName");  	 	// 결제카드사명
$cardQuota = $connector->getResultData("CardQuota"); 		// 00:일시불,02:2개월
$cardInterest = $connector->getResultData("CardInterest"); 		// 무이자 여부 (0:일반, 1:무이자)
$cardCl = $connector->getResultData("CardCl");             		// 체크카드여부 (0:일반, 1:체크카드)
$cardBin = $connector->getResultData("CardBin");           		// 카드BIN번호
$cardPoint = $connector->getResultData("CardPoint");       		// 카드사포인트사용여부 (0:미사용, 1:포인트사용, 2:세이브포인트사용)
$paySuccess = false;													// 결제 성공 여부

$nonRepToken =$_REQUEST["NON_REP_TOKEN"];		//부인방지토큰값
    

$resultMsg = iconv("euc-kr", "utf-8", $resultMsg);
$cardName = iconv("euc-kr", "utf-8", $cardName);

/** 위의 응답 데이터 외에도 전문 Header와 개별부 데이터 Get 가능 */
if($payMethod == "CARD"){	//신용카드
	if($resultCode == "3001") $paySuccess = true;				// 결과코드 (정상 :3001 , 그 외 에러)
}
if($paySuccess) {
   // 결제 성공시 DB처리 하세요.
}else{
   // 결제 실패시 DB처리 하세요.
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body onload="javascript:frmLGCNS_pay_ing.submit();">
<form name="frmLGCNS_pay_ing" method="post" action="<?php echo $_POST["BBSE_COMMERCE_THEME_WEB_URL"]; ?>/proc/order.exec.php">
<!-- 각 결제 공통 사용 변수 -->
<input type="hidden" name="resultCode" value="<?php echo $resultCode;?>">		<!-- 결과코드 : 3001=>결제성공 -->
<input type="hidden" name="resultMsg" value="<?php echo $resultMsg;?>">		<!-- 결과메세지 : 카드 결제 성공-->
<input type="hidden" name="authDate" value="<?php echo $authDate;?>">		<!-- 승인일시 : 151029103244 -->
<input type="hidden" name="authCode" value="<?php echo $authCode;?>">		<!-- 승인번호 : 55997745 -->
<input type="hidden" name="payMethod" value="<?php echo $payMethod;?>">		<!-- 결제 수단 : CARD -->
<input type="hidden" name="mid" value="<?php echo $mid;?>">		<!-- 가맹점ID -->
<input type="hidden" name="goodsName" value="<?php echo $goodsName;?>">		<!-- 상품명 -->
<input type="hidden" name="buyerName" value="<?php echo $buyerName;?>">		<!-- 구매자명 -->
<input type="hidden" name="amt" value="<?php echo $amt;?>">		<!-- 금액 : 6330 -->
<input type="hidden" name="tid" value="<?php echo $tid;?>">		<!-- 거래아이디 : KHNY00000m01011510291032447090 -->
<input type="hidden" name="moid" value="<?php echo $moid;?>">		<!-- 주문번호 : 1320151029013153 -->

<!--CARD 결제 정보-->
<input type="hidden" name="cardName" value="<?php echo $cardName;?>">		<!-- 카드사명 : 비씨 -->
<input type="hidden" name="cardQuota" value="<?php echo $cardQuota;?>">		<!-- 할부개월 : 00 -->
<input type="hidden" name="cardCode" value="<?php echo $cardCode;?>">		<!-- 카드사 코드 : 01 -->
<input type="hidden" name="cardInterest" value="<?php echo $cardInterest;?>">		<!-- 무이자 여부 : 0 : 일반, 1: 무이자 -->
<input type="hidden" name="cardCl" value="<?php echo $cardCl;?>">		<!-- 체크카드여부 : 0 : 신용카드, 1: 체크카드 -->
<input type="hidden" name="cardBin" value="<?php echo $cardBin;?>">		<!-- 카드BIN번호 : 카드번호 앞8자리  -->
<input type="hidden" name="cardPoint" value="<?php echo $cardPoint;?>">		<!-- 카드사포인트사용여부 : 0 : 사용안함, 1: 사용함  -->
<input type="hidden" name="nonRepToken" value="<?php echo $nonRepToken;?>">		<!-- 부인방지토큰  -->

<!--추가정보-->
<input type="hidden" name="orderList" value="<?php echo $_POST['orderList']; ?>">		<!-- 상품목록 -->
<input type="hidden" name='pay_how' id='pay_how' value='EKA' />	<!-- 결제방법 -->
<input type="hidden" name='Column2' id='Column2' value="<?php echo $_POST['Column2']; ?>" />	<!-- 디바이스 정보 -->
<input type="hidden" name='Column3' id='Column3' value='<?php echo $_POST['Column3']; ?>' />	<!-- $ordr_idxx -->
</form>
</body> 
</html>
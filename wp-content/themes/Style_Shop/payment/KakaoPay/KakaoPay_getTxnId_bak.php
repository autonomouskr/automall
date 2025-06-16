<?php
/**
 * 2014.12.02 : 인증요청 송신 전문 외 항목 제거
 */
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";


	$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='ezpay'");
	$payCFG = unserialize($paymentConfig);
	
	$admin_key 	= $payCFG['kakaopay']['kakaopay_auth_enckey'];
	$secret		= $payCFG['kakaopay']['kakaopay_mert_key'];
	$CID		= $payCFG['kakaopay']['kakaopay_mert_id'];
	
	$ready_url = 'https://kapi.kakao.com/v1/payment/ready';
	
	$response = wp_remote_post( $ready_url, array(
		'method' 	=> 'POST',
		'headers' 	=> array(
			'Authorization'=> 'KakaoAK '.$admin_key,
		),
		'body' 		=> array(
			'cid'					=> $CID, 
			//'cid_secret'			=> $secret,
			'partner_order_id'		=> '1',
			'partner_user_id'		=> '1',
			'item_name'				=> 'test1',
			
			//'item_code'				=> '',
			'quantity'				=> '1',
			'total_amount'			=> '100',
			'tax_free_amount'		=> '0',
			//'vat_amount'			=> '',
			
			'approval_url'			=> BBSE_COMMERCE_THEME_WEB_URL.'/payment/KakaoPay/KakaoPay_pay_ing.php',
			'cancel_url'			=> BBSE_COMMERCE_THEME_WEB_URL.'/payment/KakaoPay/KakaoPay_pay_ing.php',
			'fail_url'				=> BBSE_COMMERCE_THEME_WEB_URL.'/payment/KakaoPay/KakaoPay_pay_ing.php',
			//'available_cards'		=> '',
			//'payment_method_type'	=> '',
			
			//'install_month'			=> '',
			//'custom_json'			=> '',
		),
	    )
	);
	$return = json_decode($response['body'],TRUE);
	print_r($return);
	die();
	
	//인증,결제 및 웹 경로
	$CNSPAY_WEB_SERVER_URL 	= "https://kmpay.lgcns.com:8443";
	$targetUrl 				= "https://kmpay.lgcns.com:8443";
	$msgName 				= "/merchant/requestDealApprove.dev";
	$CnsPayDealRequestUrl 	= "https://pg.cnspay.co.kr:443";

	$MID 					=$payCFG['kakaopay']['kakaopay_mert_id'];     //상점아이디 (테스트 : INIpayTest)
	//$cancelPwd 			= $payCFG['kakaopay']['kakaopay_cancel_pw'];     //거래취소 비밀번호
	//$merchantEncKey 		= $payCFG['kakaopay']['kakaopay_auth_enckey'];     //인증요청용 EncKey
	//$merchantHashKey 		= $payCFG['kakaopay']['kakaopay_auth_hashkey'];     //인증요청용 HashKey
	$merchantKey 			= $payCFG['kakaopay']['kakaopay_mert_key'];     //상점키
	$LogDir = $payCFG['kakaopay']['kakaopay_log_dir'];     //로그 저장 경로

	require_once "./lib/lgcns_KMpay.php";

	function KMPayRequest($key) {
		return (isset($_REQUEST[$key])?$_REQUEST[$key]:"");
	}
	
    // 로그 저장 위치 지정
    $kmFunc = new kmpayFunc($LogDir);
    $kmFunc->setPhpVersion($phpVersion);

    // TXN_ID를 요청하기 위한 PARAMETERR
	$REQUESTDEALAPPROVEURL = KMPayRequest("requestDealApproveUrl");	//인증 요청 경로
	$PR_TYPE = KMPayRequest("prType");												//결제 요청 타입
	$MERCHANT_ID = KMPayRequest("MID");											//가맹점 ID
	$MERCHANT_TXN_NUM = KMPayRequest("merchantTxnNum");				//가맹점 거래번호
	$channelType = KMPayRequest("channelType");
	$PRODUCT_NAME = KMPayRequest("GoodsName");								//상품명
	$AMOUNT = KMPayRequest("Amt");												//상품금액(총거래금액) (총거래금액 = 공급가액 + 부가세 + 봉사료)

	$CURRENCY = KMPayRequest("currency");											//거래통화(KRW/USD/JPY 등)
	$RETURN_URL = KMPayRequest("returnUrl");										//결제승인결과전송URL
	$CERTIFIED_FLAG = KMPayRequest("CERTIFIED_FLAG");							//가맹점 인증 구분값 ("N","NC")
  
    $OFFER_PERIOD_FLAG = KMPayRequest("OFFER_PERIOD_FLAG");							//상품제공기간 플래그
    $OFFER_PERIOD = KMPayRequest("OFFER_PERIOD");							//상품제공기간
    
	//무이자옵션
	$NOINTYN = KMPayRequest("noIntYN");											//무이자 설정
	$NOINTOPT = KMPayRequest("noIntOpt");										//무이자 옵션
	$MAX_INT =KMPayRequest("maxInt");												//최대할부개월
	$FIXEDINT = KMPayRequest("fixedInt");												//고정할부개월
	$POINT_USE_YN = KMPayRequest("pointUseYn");								//카드사포인트사용여부
	$POSSICARD = KMPayRequest("possiCard");										//결제가능카드설정
	$BLOCK_CARD = KMPayRequest("blockCard");									//금지카드설정

	// ENC KEY와 HASH KEY는 가맹점에서 생성한 KEY 로 SETTING 한다.
	$merchantEncKey = KMPayRequest("merchantEncKey");
	$merchantHashKey = KMPayRequest("merchantHashKey");
        $hashTarget = $MERCHANT_ID.$MERCHANT_TXN_NUM.str_pad($AMOUNT,7,"0",STR_PAD_LEFT);
    
    // payHash 생성
    $payHash = strtoupper(hash("sha256", $hashTarget.$merchantHashKey, false));
    
    //json string 생성
    $strJsonString = new JsonString($LogDir);
    
    $strJsonString->setValue("PR_TYPE", $PR_TYPE);
    $strJsonString->setValue("channelType", $channelType);
    $strJsonString->setValue("MERCHANT_ID", $MERCHANT_ID);
    $strJsonString->setValue("MERCHANT_TXN_NUM", $MERCHANT_TXN_NUM);
    $strJsonString->setValue("PRODUCT_NAME", $PRODUCT_NAME);

    $strJsonString->setValue("AMOUNT", $AMOUNT);
    
    $strJsonString->setValue("CURRENCY", $CURRENCY);
    $strJsonString->setValue("CERTIFIED_FLAG", $CERTIFIED_FLAG);
    
    $strJsonString->setValue("OFFER_PERIOD_FLAG", $OFFER_PERIOD_FLAG);
    $strJsonString->setValue("OFFER_PERIOD", $OFFER_PERIOD);

    $strJsonString->setValue("NO_INT_YN", $NOINTYN);
    $strJsonString->setValue("NO_INT_OPT", $NOINTOPT);
    $strJsonString->setValue("MAX_INT", $MAX_INT);
    $strJsonString->setValue("FIXED_INT", $FIXEDINT);
    
    $strJsonString->setValue("POINT_USE_YN", $POINT_USE_YN);
    $strJsonString->setValue("POSSI_CARD", $POSSICARD);
    $strJsonString->setValue("BLOCK_CARD", $BLOCK_CARD);
    
    $strJsonString->setValue("PAYMENT_HASH", $payHash);
    
    // 결과값을 담는 부분
	$resultCode = "";
	$resultMsg = "";
	$txnId = "";
	$merchantTxnNum = "";
	$prDt = "";
	$strValid = "";
    
    // Data 검증
    $dataValidator = new KMPayDataValidator($strJsonString->getArrayValue());
    $strValid = $dataValidator->resultValid;
    if (strlen($strValid) > 0) {
    	$arrVal = explode(",", $strValid);
    	if (count($arrVal) == 3) {
    		$resultCode = $arrVal[1];
    		$resultMsg = $arrVal[2];
    	} else {
    		$resultCode = $strValid;
    		$resultMsg = $strValid;
    	}
    }

    // Data에 이상 없는 경우
    if (strlen($strValid) == 0) {
	    // CBC 암호화
	    $paramStr = $strJsonString->getJsonString();
	    $kmFunc->writeLog("Request");
	    $kmFunc->writeLog($paramStr);
	    $kmFunc->writeLog($strJsonString->getArrayValue());
	    $encryptStr = $kmFunc->parameterEncrypt($merchantEncKey, $paramStr);
	    $payReqResult = $kmFunc->connMPayDLP($REQUESTDEALAPPROVEURL, $MERCHANT_ID, $encryptStr);
	    $resultString = $kmFunc->parameterDecrypt($merchantEncKey, $payReqResult);
	    
	    $resultJSONObject = new JsonString($LogDir);
	    if (substr($resultString, 0, 1) == "{") {
	        $resultJSONObject->setJsonString($resultString);
	        $resultCode = $resultJSONObject->getValue("RESULT_CODE");
			$resultMsg = $resultJSONObject->getValue("RESULT_MSG");
			if ($resultCode == "00") {
	    		$txnId = $resultJSONObject->getValue("TXN_ID");
	    		$merchantTxnNum = $resultJSONObject->getValue("MERCHANT_TXN_NUM");
	    		$prDt = $resultJSONObject->getValue("PR_DT");
	    	}
	    }
	    $kmFunc->writeLog("Result");
	    $kmFunc->writeLog($resultString);
	    $kmFunc->writeLog($resultJSONObject->getArrayValue());
    }

	if($resultCode && $resultMsg && $txnId && $prDt){
		echo "success|||".$resultCode."|||".$resultMsg."|||".$txnId."|||".$prDt;
	}
	else{
		echo "fail";
	}
?>
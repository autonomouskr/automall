<?php
/* INIsecurepay.php
 *
 * 이니페이 플러그인을 통해 요청된 지불을 처리한다.
 * 지불 요청을 처리한다.
 * 코드에 대한 자세한 설명은 매뉴얼을 참조하십시오.
 * <주의> 구매자의 세션을 반드시 체크하도록하여 부정거래를 방지하여 주십시요.
 *  
 * http://www.inicis.com
 * Copyright (C) 2006 Inicis Co., Ltd. All rights reserved.
 */

  /****************************
   * 0. 세션 시작             *
   ****************************/
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

// 결제모듈 설정
$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
if(!$paymentConfig) {
	echo "<script>alert('관리자에서 결제모듈 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
	exit;
}

$payCFG = unserialize($paymentConfig);

//REQUEST ************************************
$INI_STATUS = $_REQUEST['P_STATUS'];
$INI_REQ_URL = $_REQUEST['P_REQ_URL'];
$INI_TID = $_REQUEST['P_TID'];
$INI_MID = $payCFG['payment_id'];     //상점아이디

/*
$P_STATUS = "00";
$P_REQ_URL = "https://drmobile.inicis.com/smart/pay_req_url.php";
$P_TID = "INIMX_AISPINIpayTest20150810113122361599";
$P_MID = "INIpayTest";
*/

$INI_STRING="P_TID=".$INI_TID."&P_MID=".$INI_MID;

if($INI_STATUS!="00"){
	echo "<script>alert('결제 처리중 오류가 발생하였습니다.\\n(".$V['ResultErrorCode']." : ".$V['ResultMsg'].")\\n\\n고객센터로 문의해주세요.');location.href='".home_url()."';</script>";
	exit;
}

$rtnBodyUtf8=$rtnData="";
$tmpUrl=explode("inicis.com",$INI_REQ_URL);
$addrUrl=str_replace("https://","",$tmpUrl['0']);
$addrUrl=str_replace("http://","",$addrUrl);

$req_addr = "ssl://".$addrUrl."inicis.com";
$req_host = $addrUrl."inicis.com";
$req_port = 443;
$queryString=$INI_STRING;

$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
if ($nc_sock) {
	$out = "POST ".$tmpUrl['1']." HTTP/1.1\r\n";
	$out .= "Host: ".$req_host.":".$req_port."\r\n";
	$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$out .= "Content-length: ".strlen($queryString)."\r\n";
	$out .= "Connection: Close\r\n\r\n";
	$out .= $queryString;
	fwrite($nc_sock, $out);
	
	while(!feof($nc_sock)){
		$rtnBodyUtf8 .=fgets($nc_sock,4096);
	}
	fclose($nc_sock);

	$rtnBodyUtf8=iconv("EUC-KR","UTF-8",$rtnBodyUtf8);
	$rtnData = trim(substr($rtnBodyUtf8, strpos($rtnBodyUtf8,"\r\n\r\n")+4));
}

if(!$rtnData){
	echo "<script>alert('결제 처리중 오류(통신장애)가 발생하였습니다.\\n고객센터로 문의해주세요.');location.href='".home_url()."';</script>";
	exit;
}

/*
P_STATUS=00&P_AUTH_DT=20150810131425&P_AUTH_NO=40462632&P_RMESG1=성공적으로 처리 하였습니다.&P_RMESG2=00&P_TID=INIMX_ISP_INIpayTest20150810131425231155&P_FN_CD1=11&P_AMT=2200&P_TYPE=CARD&P_UNAME=홍길동&P_MID=INIpayTest&P_OID=992572806-1439211966&P_NOTI=&P_NEXT_URL=http://bbseshop.bbsetheme.com/wp-content/themes/Style_Shop/payment/INIpay50/result/INI_pay_ing_mobile.php?P_MID=INIpayTest&P_MNAME=BBSE 쇼핑몰&P_NOTEURL=&P_CARD_MEMBER_NUM=&P_CARD_NUM=490612*********6&P_CARD_ISSUER_CODE=00&P_CARD_PURCHASE_CODE=11&P_CARD_PRTC_CODE=1&P_CARD_INTEREST=0&P_CARD_CHECKFLAG=0&P_CARD_ISSUER_NAME=비씨카드&P_CARD_PURCHASE_NAME=BC카드&P_FN_NM=BC카드&P_ISP_CARDCODE=040100123310121
*/

parse_str($rtnData);

if($P_STATUS!="00"){
	echo "<script>alert('결제 처리중 오류가 발생하였습니다.\\n고객센터로 문의해주세요.');location.href='".home_url()."';</script>";
	exit;
}

/*
P_STATUS : 00        =>          “00” 이외 실패, 주의 : 반드시 00 이외의 모든 결과는 실패로 처리하셔야 함
P_AUTH_DT=20150810131425        =>         승인일자, char(14) YYYYmmddHHmmss
P_AUTH_NO=40462632        =>         승인번호, char(30) 신용카드거래에서만 사용
P_RMESG1=성공적으로 처리 하였습니다.        =>         메시지1, char(500) 지불 결과 메시지
P_RMESG2=00        =>         주문정보, char(800) 주문정보에 입력한 값 반환
P_TID=INIMX_ISP_INIpayTest20150810131425231155        =>         거래번호 char(40)
P_FN_CD1=11        =>         카드코드, char(4)
P_AMT=2200        =>         거래금액, char(8)
P_TYPE=CARD        =>         지불수단, char(10), CARD(ISP,안심클릭,국민앱카드,케이페이) / HPMN(해피머니) / CULTURE(문화상품권) / MOBILE(휴대폰) / VBANK(가상계좌) / EWALLET(전자지갑) / ETC_(알리페이,페이팔 외 기타)
P_UNAME=홍길동        =>         주문자명, char(30)
P_MID=INIpayTest        =>         상점아이디, char(10)
P_OID=992572806-1439211966        =>         상점 주문번호, char(100)
P_NOTI=        =>         주문정보, char(800) 주문정보에 입력한 값 반환
P_NEXT_URL=http://bbseshop.bbsetheme.com/wp-content/themes/Style_Shop/payment/INIpay50/result/INI_pay_ing_mobile.php        =>         가맹점 전달 NEXT URL, 거래요청 시 입력한 값을 그대로 반환
P_MNAME=BBSE 쇼핑몰        =>         가맹점 이름, 주문정보에 입력한 값 반환
P_NOTEURL=        =>         가맹점 전달 NOTI URL, 거래요청 시 입력한 값을 그대로 반환

// 신용카드
P_CARD_MEMBER_NUM=        =>         가맹점번호, 자체 가맹점 일 경우만 해당
P_CARD_NUM=490612*********6        =>         카드번호, 계약관계에 따라 틀림
P_CARD_ISSUER_CODE=00        =>         발급사 코드, char(2)
P_CARD_PURCHASE_CODE=11        =>         매입사 코드, 자체 가맹점 일 경우만 해당
P_CARD_PRTC_CODE=1        =>         부분취소 가능여부, 부분취소가능 : 1 , 부분취소불가능 : 0
P_CARD_INTEREST=0        =>         무이자 할부여부, 0 : 일반, 1 : 무이자
P_CARD_CHECKFLAG=0        =>         체크카드 여부, 0 : 신용카드, 1 : 체크카드, 2 : 기프트카드
P_CARD_ISSUER_NAME=비씨카드        =>         카드 발급사 명
P_CARD_PURCHASE_NAME=BC카드        =>         카드 매입사 명
P_FN_NM=BC카드        =>         결제카드한글명, BC카드,
P_ISP_CARDCODE=040100123310121        =>         VP 카드코드

// 가상계좌
P_VACT_NUM        =>         입금할 계좌 번호, char(20)
P_VACT_DATE        =>         입금마감일자, char(8) : yyyymmdd
P_VACT_TIME        =>         입금마감시간, char(6) : hhmmss
P_VACT_NAME        =>         계좌주명
P_VACT_BANK_CODE        =>         은행코드, char(2)
*/
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body onload="javascript:ini_ing.submit();">
<form name="ini_ing" method="post" action="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/proc/order.exec.php">
	<!--1. 결제 결과-->
	<input type="hidden" name="PayMethod" value="<?php echo ($P_TYPE=='CARD')?"Card":"VBank";?>">		<!-- 결제 방법 (신용카드 : Card, ISP : VCard, 은행계좌 : DirectBank, 무통장입금 : VBank, 핸드폰 : HPP, 전화결제 (ars전화 결제) : Ars1588Bill, 전화결제 (받는전화결제) : PhoneBill, OK CASH BAG POINT : OCBPoint, 문화상품권 : Culture, K-merce 상품권: KMC_, 틴캐시 결제 : TEEN, 게임문화 상품권 : DGCL) -->
	<input type="hidden" name="TID" value="<?php echo $P_TID; ?>">		<!-- 거래번호 -->
	<input type="hidden" name="ResultCode" value="<?php echo $P_STATUS; ?>">		<!-- 결과코드 -->
	<input type="hidden" name="ResultErrorCode" value="<?php echo ($P_STATUS!="00")?$P_STATUS:""; ?>">		<!-- 에러코드(에러인 경우) -->
	<input type="hidden" name="ResultMsg" value="<?php echo $P_RMESG1; ?>">		<!-- 결과내용 -->
	<input type="hidden" name="MOID" value="<?php echo $P_OID; ?>" />		<!-- 상점주문번호 -->
	<input type="hidden" name="TotPrice" value="<?php echo $P_AMT; ?>" />		<!-- 결제완료금액 -->

	<!--2.신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제 결과 데이터-->
	<input type="hidden" name="ApplDate" value="<?php echo substr($P_AUTH_DT,0,8); ?>" />		<!-- 이니시스 승인날짜(YYYYMMDD) -->
	<input type="hidden" name="ApplTime" value="<?php echo substr($P_AUTH_DT,8,6); ?>" />		<!-- 이니시스 승인시각(HHMMSS) -->

	<!--3. 신용카드 결제 결과 데이터-->
	<input type="hidden" name="ApplNum" value="<?php echo $P_AUTH_NO; ?>" />		<!-- 신용카드 승인번호 -->
	<input type="hidden" name="CARD_Quota" value="" />		<!-- 할부기간 -->
	<input type="hidden" name="CARD_Interest" value="<?php echo $P_CARD_INTEREST; ?>" />		<!-- ("1"이면 무이자할부) -->
	<input type="hidden" name="CARD_Code" value="<?php echo $P_FN_CD1; ?>" />		<!-- 신용카드사 코드 (01:하나(외환), 03:롯데, 04:현대, 06:국민,11:비씨(BC), 12:삼성, 14:신한, 34:하나,41:NH(농협))-->

	<!--4. 실시간 계좌이체 결제 결과 데이터-->
	<input type="hidden" name="ACCT_BankCode" value="" />		<!-- 은행코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)-->
	<input type="hidden" name="CSHR_ResultCode" value="" />		<!-- 현금영수증 발행결과코드-->
	<input type="hidden" name="CSHR_Type" value="" />		<!-- 현금영수증 발행구분코드-->

	<!--5. 가상계좌(무통장입금) 결제 결과 데이터-->
	<input type="hidden" name="VACT_Num" value="<?php echo $P_VACT_NUM; ?>" />		<!-- 가상계좌 번호-->
	<input type="hidden" name="VACT_BankCode" value="<?php echo $P_VACT_BANK_CODE; ?>" />		<!-- 입금할 은행 코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)-->
	<input type="hidden" name="VACT_Date" value="<?php echo $P_VACT_DATE; ?>" />		<!-- 입금예정일 (YYYYMMDD)-->
	<input type="hidden" name="VACT_InputName" value="<?php echo $P_VACT_NAME; ?>" />		<!-- 송금자 명-->
	<input type="hidden" name="VACT_Name" value="<?php echo $P_VACT_NAME; ?>" />		<!-- 예금주 명-->

	<!-- 추가 전달 변수 -->
	<input type="hidden" name="ini_buyername" value="<?php echo $P_UNAME; ?>">		<!-- 주문자명 -->
	<input type="hidden" name='Column2' id='Column2' value='mobile' />	<!-- 디바이스 정보 -->
	<input type="hidden" name='Column3' id='Column3' value='<?php echo $P_OID; ?>' />	<!-- $ordr_idxx -->
</form>
</body>
</html>

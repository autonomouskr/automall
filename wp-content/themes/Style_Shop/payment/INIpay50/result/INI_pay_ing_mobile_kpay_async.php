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

if($_SESSION['P_UNAME'] && $_SESSION['P_OID']){
	$ordData = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_inicis WHERE MOID='".$_SESSION['P_OID']."'");
	if($ordData->MOID) {
?>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		</head>
		<body onload="javascript:ini_ing.submit();">
		<form name="ini_ing" method="post" action="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/proc/order.exec.php">
			<!--1. 결제 결과-->
			<input type="hidden" name="PayMethod" value="<?php echo $ordData->PayMethod;?>">		<!-- 결제 방법 (신용카드 : Card, ISP : VCard, 은행계좌 : DirectBank, 무통장입금 : VBank, 핸드폰 : HPP, 전화결제 (ars전화 결제) : Ars1588Bill, 전화결제 (받는전화결제) : PhoneBill, OK CASH BAG POINT : OCBPoint, 문화상품권 : Culture, K-merce 상품권: KMC_, 틴캐시 결제 : TEEN, 게임문화 상품권 : DGCL) -->
			<input type="hidden" name="TID" value="<?php echo $ordData->TID;?>">		<!-- 거래번호 -->
			<input type="hidden" name="ResultCode" value="<?php echo $ordData->ResultCode;?>">		<!-- 결과코드 -->
			<input type="hidden" name="ResultErrorCode" value="<?php echo $ordData->ResultErrorCode;?>">		<!-- 에러코드(에러인 경우) -->
			<input type="hidden" name="ResultMsg" value="<?php echo $ordData->ResultMsg;?>">		<!-- 결과내용 -->
			<input type="hidden" name="MOID" value="<?php echo $ordData->MOID;?>" />		<!-- 상점주문번호 -->
			<input type="hidden" name="TotPrice" value="<?php echo $ordData->TotPrice;?>" />		<!-- 결제완료금액 -->

			<!--2.신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제 결과 데이터-->
			<input type="hidden" name="ApplDate" value="<?php echo $ordData->ApplDate;?>" />		<!-- 이니시스 승인날짜(YYYYMMDD) -->
			<input type="hidden" name="ApplTime" value="<?php echo $ordData->ApplTime;?>" />		<!-- 이니시스 승인시각(HHMMSS) -->

			<!--3. 신용카드 결제 결과 데이터-->
			<input type="hidden" name="ApplNum" value="<?php echo $ordData->ApplNum;?>" />		<!-- 신용카드 승인번호 -->
			<input type="hidden" name="CARD_Quota" value="<?php echo $ordData->CARD_Quota;?>" />		<!-- 할부기간 -->
			<input type="hidden" name="CARD_Interest" value="<?php echo $ordData->CARD_Interest;?>" />		<!-- ("1"이면 무이자할부) -->
			<input type="hidden" name="CARD_Code" value="<?php echo $ordData->CARD_Code;?>" />		<!-- 신용카드사 코드 (01:하나(외환), 03:롯데, 04:현대, 06:국민,11:비씨(BC), 12:삼성, 14:신한, 34:하나,41:NH(농협))-->

			<!--4. 실시간 계좌이체 결제 결과 데이터-->
			<input type="hidden" name="ACCT_BankCode" value="" />		<!-- 은행코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)-->
			<input type="hidden" name="CSHR_ResultCode" value="<?php echo $ordData->CSHR_ResultCode;?>" />		<!-- 현금영수증 발행결과코드-->
			<input type="hidden" name="CSHR_Type" value="<?php echo $ordData->CSHR_Type;?>" />		<!-- 현금영수증 발행구분코드-->

			<!--5. 가상계좌(무통장입금) 결제 결과 데이터-->
			<input type="hidden" name="VACT_Num" value="<?php echo $ordData->VACT_Num;?>" />		<!-- 가상계좌 번호-->
			<input type="hidden" name="VACT_BankCode" value="<?php echo $ordData->VACT_BankCode;?>" />		<!-- 입금할 은행 코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)-->
			<input type="hidden" name="VACT_Date" value="<?php echo $ordData->VACT_Date;?>" />		<!-- 입금예정일 (YYYYMMDD)-->
			<input type="hidden" name="VACT_InputName" value="<?php echo $ordData->VACT_InputName;?>" />		<!-- 송금자 명-->
			<input type="hidden" name="VACT_Name" value="<?php echo $ordData->VACT_Name;?>" />		<!-- 예금주 명-->

			<!-- 추가 전달 변수 -->
			<input type="hidden" name="ini_buyername" value="<?php echo $_SESSION['P_UNAME']; ?>">		<!-- 주문자명 -->
			<input type="hidden" name='Column2' id='Column2' value='mobile' />	<!-- 디바이스 정보 -->
			<input type="hidden" name='Column3' id='Column3' value='<?php echo $_SESSION['P_OID']; ?>' />	<!-- $ordr_idxx -->
		</form>
		</body>
		</html>
<?php
		$_SESSION['P_OID'] = ""; //주문번호 저장
		$_SESSION['P_UNAME'] = ""; //구매자이름
	}
}
?>
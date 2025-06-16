<?php
    /*
     * [최종결제요청 페이지(STEP2-2)]
     *
     * LG유플러스으로 부터 내려받은 LGD_PAYKEY(인증Key)를 가지고 최종 결제요청.(파라미터 전달시 POST를 사용하세요)
     */


    /*
     *************************************************
     * 1.최종결제 요청 - BEGIN
     *  (단, 최종 금액체크를 원하시는 경우 금액체크 부분 주석을 제거 하시면 됩니다.)
     *************************************************
     */
    $CST_PLATFORM = $_POST["CST_PLATFORM"];
    $CST_MID = $_POST["CST_MID"];
    $LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;
    $LGD_PAYKEY = $_POST["LGD_PAYKEY"];
    $LGD_MERTKEY = $_POST["LGD_MERTKEY"];    // 상점MertKey : BBS e-Commerce 자체 필드 (from request page)
	$configPath = $_POST["CONFIG_PATH"];               //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정. 

    require_once("./lgdacom/XPayClient.php");
    $xpay = new XPayClient($configPath, $CST_PLATFORM);
	$xpay->config[$LGD_MID]=$LGD_MERTKEY;
    $xpay->Init_TX($LGD_MID);    
    
    $xpay->Set("LGD_TXNAME", "PaymentByKey");
    $xpay->Set("LGD_PAYKEY", $LGD_PAYKEY);
    
    //금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
	//$DB_AMOUNT = "DB나 세션에서 가져온 금액"; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
	//$xpay->Set("LGD_AMOUNTCHECKYN", "Y");
	//$xpay->Set("LGD_AMOUNT", $DB_AMOUNT);
	    
    /*
     *************************************************
     * 1.최종결제 요청(수정하지 마세요) - END
     *************************************************
     */

    /*
     * 2. 최종결제 요청 결과처리
     *
     * 최종 결제요청 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
     */

	$xpay->TX();
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body onload="javascript:frmLGUplus_pay_ing.submit();">
<form name="frmLGUplus_pay_ing" method="post" action="<?php echo $_POST["BBSE_COMMERCE_THEME_WEB_URL"]; ?>/proc/order.exec.php">

<!-- 각 결제 공통 사용 변수 -->
<input type="hidden" name="LGD_RESPCODE" value="<?php echo $xpay->Response("LGD_RESPCODE",0); ?>">		<!-- 결과코드 -->
<input type="hidden" name="LGD_PAYTYPE" value="<?php echo $xpay->Response("LGD_PAYTYPE",0); ?>">		<!-- 결제형태 -->
<input type="hidden" name="LGD_RESPMSG" value="<?php echo $xpay->Response("LGD_RESPMSG",0); ?>">		<!-- 결과메세지 -->
<input type="hidden" name="LGD_PAYKEY" value="<?php echo $xpay->Response("LGD_PAYKEY",0); ?>">		<!-- LG유플러스인증키 -->
<input type="hidden" name="LGD_TID" value="<?php echo $xpay->Response("LGD_TID",0); ?>">		<!-- LG유플러스 거래번호 -->
<input type="hidden" name="LGD_HASHDATA" value="<?php echo $xpay->Response("LGD_HASHDATA",0); ?>">		<!-- 해쉬 데이터 -->
<input type="hidden" name="LGD_PAYDATE" value="<?php echo $xpay->Response("LGD_PAYDATE",0); ?>">		<!-- 결제일시(yyyyMMddHHmmss 형식) -->
<input type="hidden" name="LGD_MID" value="<?php echo $xpay->Response("LGD_MID",0); ?>">		<!-- LG유플러스 상점아이디 -->
<input type="hidden" name="LGD_OID" value="<?php echo $xpay->Response("LGD_OID",0); ?>">		<!-- 주문번호 -->
<input type="hidden" name="LGD_PRODUCTINFO" value="<?php echo $xpay->Response("LGD_PRODUCTINFO",0); ?>">		<!-- 상품명 -->
<input type="hidden" name="LGD_AMOUNT" value="<?php echo $xpay->Response("LGD_AMOUNT",0); ?>">		<!-- 결제금액 -->
<input type="hidden" name="LGD_BUYER" value="<?php echo $xpay->Response("LGD_BUYER",0); ?>">		<!-- 구매자명 -->
<input type="hidden" name="LGD_BUYERID" value="<?php echo $xpay->Response("LGD_BUYERID",0); ?>">		<!-- 구매자ID -->
<input type="hidden" name="LGD_BUYERIP" value="<?php echo $_POST['LGD_BUYERIP']; ?>">		<!-- 구매자ID -->
<input type="hidden" name="LGD_BUYERPHONE" value="<?php echo $xpay->Response("LGD_BUYERPHONE",0); ?>">		<!-- 구매자 전화번호 -->
<input type="hidden" name="LGD_BUYEREMAIL" value="<?php echo $xpay->Response("LGD_BUYEREMAIL",0); ?>">		<!-- 구매자 이메일 -->
<input type="hidden" name="LGD_TRANSAMOUNT" value="<?php echo $xpay->Response("LGD_TRANSAMOUNT",0); ?>">		<!-- 환율적용금액 -->
<input type="hidden" name="LGD_FINANCECODE" value="<?php echo $xpay->Response("LGD_FINANCECODE",0); ?>">		<!-- 결제기관코드 -->
<input type="hidden" name="LGD_FINANCENAME" value="<?php echo $xpay->Response("LGD_FINANCENAME",0); ?>">		<!-- 결제기관명 -->

<input type="hidden" name="orderList" value="<?php echo $_POST['orderList']; ?>">		<!-- 상품목록 -->
<input type="hidden" name='pay_how' id='pay_how' value='<?php echo $_POST['pay_how']; ?>' />	<!-- 결제방법 -->
<input type="hidden" name='Column2' id='Column2' value="<?php echo $_POST['Column2']; ?>" />	<!-- 디바이스 정보 -->
<input type="hidden" name='Column3' id='Column3' value='<?php echo $xpay->Response("LGD_OID",0); ?>' />	<!-- $ordr_idxx -->

<!-- 신용카드 결제 사용 변수 -->
<input type="hidden" name="LGD_PAYNOW_TRANTYPE" value="<?php echo $xpay->Response("LGD_PAYNOW_TRANTYPE",0); ?>">		<!-- 페이나우 사용여부 : 사용 =>1, 사용안함 => '' -->

<!-- 신용카드 결제 사용 변수 -->
<input type="hidden" name="LGD_CARDACQUIRER" value="<?php echo $xpay->Response("LGD_CARDACQUIRER",0); ?>">		<!-- 신용카드매입사코드 -->
<input type="hidden" name="LGD_PCANCELFLAG" value="<?php echo $xpay->Response("LGD_PCANCELFLAG",0); ?>">		<!-- 신용카드부분취소가능여부 : 0: 부분취소불가능,  1: 부분취소가능 -->
<input type="hidden" name="LGD_FINANCEAUTHNUM" value="<?php echo $xpay->Response("LGD_FINANCEAUTHNUM",0); ?>">		<!-- 결제기관승인번호 -->
<input type="hidden" name="LGD_VANCODE" value="<?php echo $xpay->Response("LGD_VANCODE",0); ?>">		<!-- 밴사 코드 -->
<input type="hidden" name="LGD_CARDNUM" value="<?php echo $xpay->Response("LGD_CARDNUM",0); ?>">		<!-- 카드번호 -->
<input type="hidden" name="LGD_ISPKEY" value="<?php echo $xpay->Response("LGD_ISPKEY",0); ?>">		<!-- ISP 키, ISP만 제공됨 -->
<input type="hidden" name="LGD_AFFILIATECODE" value="<?php echo $xpay->Response("LGD_AFFILIATECODE",0); ?>">		<!-- 신용카드제휴코드, ISP만 제공됨 -->

<!-- 계좌이체 결제 사용 변수 -->
<input type="hidden" name="LGD_CASHRECEIPTNUM" value="<?php echo $xpay->Response("LGD_CASHRECEIPTNUM",0); ?>">		<!-- 현금영수증 승인번호(현금영수증 건이 아니거나 실패인경우 "0") -->
<input type="hidden" name="LGD_CASHRECEIPTSELFYN" value="<?php echo $xpay->Response("LGD_CASHRECEIPTSELFYN",0); ?>">	<!-- 현금영수증자진발급제유무(Y: 자진발급제 적용, 그외 : 미적용) -->
<input type="hidden" name="LGD_CASHRECEIPTKIND" value="<?php echo $xpay->Response("LGD_CASHRECEIPTKIND",0); ?>">					<!-- 현금영수증 종류(0: 소득공제용 , 1: 지출증빙용) -->

<!-- 가상계좌 결제 사용 변수 -->
<input type="hidden" name="LGD_ACCOUNTNUM" value="<?php echo $xpay->Response("LGD_ACCOUNTNUM",0); ?>">					<!-- 입금할 계좌번호 -->
<input type="hidden" name="LGD_CASTAMOUNT" value="<?php echo $xpay->Response("LGD_CASTAMOUNT",0); ?>">	<!--입금누적금액 -->
<input type="hidden" name="LGD_CASCAMOUNT" value="<?php echo $xpay->Response("LGD_CASCAMOUNT",0); ?>">	<!--현입금금액 -->
<input type="hidden" name="LGD_CASFLAG" value="<?php echo $xpay->Response("LGD_CASFLAG",0); ?>">	<!--거래종류(R:할당,I:입금,C:취소) -->
<input type="hidden" name="LGD_CASSEQNO" value="<?php echo $xpay->Response("LGD_CASSEQNO",0); ?>">	<!--가상계좌일련번호 -->
<input type="hidden" name="LGD_CLOSEDATE" value="<?php echo $_POST['LGD_CLOSEDATE']; ?>">	<!--가상계좌 결제마감기간 (yyyyMMddHHmmss) -->
<input type="hidden" name="LGD_PAYER" value="<?php echo $xpay->Response("LGD_PAYER",0); ?>">	<!--가상계좌 입금자명 -->

<!-- 에스크로 사용 변수 -->
<input type="hidden" name="LGD_ESCROWYN" value="<?php echo $xpay->Response("LGD_ESCROWYN",0); ?>">		<!-- 에스크로 적용여부(Y,N) -->

</form>
</body> 
</html>
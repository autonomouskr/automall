<?php
	session_start();

	/*
	// 관리자 설정 시 curl 체크
	function bbse_check_curl_installed(){
		if  (in_array  ('curl', get_loaded_extensions())) return true;
		else return false;
	}

	// Ouput text to user based on test
	if (bbse_check_curl_installed()) {
	  //echo "cURL is <span style=\"color:blue\">installed</span> on this server";
	} else {
	  //echo "cURL is NOT <span style=\"color:red\">installed</span> on this server";
	}

	// 오류표시
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	*/



    /*
     * [결제 인증요청 페이지(STEP2-1)]
     *
     * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.     
     */

    /*
     * 1. 기본결제 인증요청 정보 변경
     * 
     * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
     */
    $CST_PLATFORM = "test";                        //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
    $CST_MID = "X_nayana";           //상점아이디 : 관리자모드 설정, LGU+의 상점관리자 페이지(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
                                                                                 //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
    $LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;   //상점아이디(자동생성)
    $LGD_MERTKEY = "9e594ecd5c8758511a59075f8e1183ab";    // 상점MertKey : 관리자모드 설정, LGU+의 상점관리자 페이지(mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)



    $LGD_OID = "ORD_".date("YmdHis");                            //주문번호(상점정의 유니크한 주문번호를 입력하세요)
    $LGD_AMOUNT = "2000";                         //결제금액("," 를 제외한 결제금액을 입력하세요)
    $LGD_BUYER = "서재윤";                           //구매자명
    $LGD_PRODUCTINFO = "핸드폰 배터리";                     //상품명
    $LGD_BUYEREMAIL = "skqkfk@naver.com";                      //구매자 이메일
    $LGD_CUSTOM_FIRSTPAY = "SC0010";                 //상점정의 초기결제수단 (기본값 : SC0010)
    $LGD_TIMESTAMP = date("YmdHis");                                  //타임스탬프
    $LGD_CUSTOM_SKIN = "red";                                         //상점정의 결제창 스킨
    $LGD_CUSTOM_USABLEPAY = "SC0010";        	     // 디폴트 결제수단 (특정결제수단만 보이게 할 경우 사용 , 
	                                                                                                                         //신용카드:SC0010, 계좌이체:SC0030, 무통장입금(가상계좌):SC0040, 휴대폰:SC0060, 유선전화결제:SC0070, OK캐쉬백:SC0090,문화상품권: SC0111, 게임문화상품권:SC0112
	                                                                                                                         // 예)신용카드,계좌이체만 사용할 경우SC0010-SC0030)

    $LGD_WINDOW_VER = "2.5";										 //결제창 버젼정보
    $LGD_WINDOW_TYPE = "iframe";                //결제창 호출방식 (수정불가)
    $LGD_CUSTOM_SWITCHINGTYPE = "IFRAME";                //신용카드 카드사 인증 페이지 연동 방식 (수정불가)
    $LGD_CUSTOM_PROCESSTYPE = "TWOTR";                                       //수정불가
    /*
     * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. 
     */    
    $LGD_CASNOTEURL = "http://bbseshop.bbsetheme.com/wp-content/themes/Style_Shop/payment/lguplusXPay/cas_noteurl.php";    

    /*
     * LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
     */    
    $LGD_RETURNURL = "http://bbseshop.bbsetheme.com/wp-content/themes/Style_Shop/payment/lguplusXPay/returnurl.php";  
	
	
	
    
    /*
     *************************************************
     * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
     * 
     * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
     *************************************************
     *
     * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
     * LGD_MID          : 상점아이디
     * LGD_OID          : 주문번호
     * LGD_AMOUNT       : 금액
     * LGD_TIMESTAMP    : 타임스탬프
     * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
     *
     * MD5 해쉬데이터 암호화 검증을 위해
     * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
     */

	$LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$LGD_MERTKEY);

	$LGD_ENCODING=$LGD_ENCODING_NOTEURL=$LGD_ENCODING_RETURNURL="UTF-8"; // Character Set 선택
    /*
     *************************************************
     * 2. MD5 해쉬암호화 (수정하지 마세요) - END
     *************************************************
     */
	
    $payReqMap['CST_PLATFORM'] = $CST_PLATFORM;              // 테스트, 서비스 구분
    $payReqMap['LGD_WINDOW_TYPE'] = $LGD_WINDOW_TYPE;           // 수정불가
    $payReqMap['CST_MID'] = $CST_MID;                   // 상점아이디
    $payReqMap['LGD_MID'] = $LGD_MID;                   // 상점아이디
    $payReqMap['LGD_OID'] = $LGD_OID;                   // 주문번호
    $payReqMap['LGD_BUYER'] = $LGD_BUYER;            	   // 구매자
    $payReqMap['LGD_PRODUCTINFO'] = $LGD_PRODUCTINFO;     	   // 상품정보
    $payReqMap['LGD_AMOUNT'] = $LGD_AMOUNT;                // 결제금액
    $payReqMap['LGD_BUYEREMAIL'] = $LGD_BUYEREMAIL;            // 구매자 이메일
    $payReqMap['LGD_CUSTOM_SKIN'] = $LGD_CUSTOM_SKIN;           // 결제창 SKIN
    $payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;    // 트랜잭션 처리방식
    $payReqMap['LGD_TIMESTAMP'] = $LGD_TIMESTAMP;             // 타임스탬프
    $payReqMap['LGD_HASHDATA'] = $LGD_HASHDATA;              // MD5 해쉬암호값
    $payReqMap['LGD_RETURNURL'] = $LGD_RETURNURL;      	   // 응답수신페이지
    $payReqMap['LGD_VERSION'] = "PHP_2.5";		   // 버전정보 (삭제하지 마세요)
    $payReqMap['LGD_CUSTOM_USABLEPAY'] = $LGD_CUSTOM_USABLEPAY;	   // 디폴트 결제수단
	$payReqMap['LGD_CUSTOM_SWITCHINGTYPE'] = $LGD_CUSTOM_SWITCHINGTYPE;	       // 신용카드 카드사 인증 페이지 연동 방식
	$payReqMap['LGD_WINDOW_VER'] = $LGD_WINDOW_VER;
	$payReqMap['LGD_ENCODING'] = $LGD_ENCODING; // 결제창 호출 문자 인코딩방식
	$payReqMap['LGD_ENCODING_NOTEURL'] = $LGD_ENCODING_NOTEURL; // 결과수신페이지 호출 문자 인코딩방식
	$payReqMap['LGD_ENCODING_RETURNURL'] = $LGD_ENCODING_RETURNURL; // 결과수신페이지 호출 문자 인코딩방식
    
    // 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
    $payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // 가상계좌 NOTEURL

    //Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
    $payReqMap['LGD_RESPCODE'] = "";
    $payReqMap['LGD_RESPMSG'] = "";
    $payReqMap['LGD_PAYKEY'] = "";

    $_SESSION['PAYREQ_MAP'] = $payReqMap;
?>


<!DOCTYPE html>
<html lang="ko-KR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>LG유플러스 eCredit서비스 결제테스트</title>
<script language="javascript" src="http://xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js" type="text/javascript"></script>
<script type="text/javascript">

/*
* 수정불가.
*/
	var LGD_window_type = '<?php echo $LGD_WINDOW_TYPE ?>';
	
/*
* 수정불가
*/
function launchCrossPlatform(){
	lgdwin = openXpay(document.getElementById('LGD_PAYINFO'), '<?php echo $CST_PLATFORM ?>', LGD_window_type, null, "", "");
}
/*
* FORM 명만  수정 가능
*/
function getFormObject() {
        return document.getElementById("LGD_PAYINFO");
}

/*
 * 인증결과 처리
 */
function payment_return() {
	var fDoc;
		fDoc = lgdwin.contentWindow || lgdwin.contentDocument;
		
	if (fDoc.document.getElementById('LGD_RESPCODE').value == "0000") {
		
			document.getElementById("LGD_PAYKEY").value = fDoc.document.getElementById('LGD_PAYKEY').value;
			document.getElementById("LGD_PAYINFO").target = "_self";
			document.getElementById("LGD_PAYINFO").action = "payres.php";
			document.getElementById("LGD_PAYINFO").submit();
	} else {
		alert("LGD_RESPCODE (결과코드) : " + fDoc.document.getElementById('LGD_RESPCODE').value + "\n" + "LGD_RESPMSG (결과메시지): " + fDoc.document.getElementById('LGD_RESPMSG').value);
		closeIframe();
	}
}

</script>
</head>
<body>
<form method="post" name="LGD_PAYINFO" id="LGD_PAYINFO" action="payres.php">
<table>
    <tr>
        <td>구매자 이름 </td>
        <td><?php echo $LGD_BUYER ?></td>
    </tr>
    <tr>
        <td>상품정보 </td>
        <td><?php echo $LGD_PRODUCTINFO ?></td>
    </tr>
    <tr>
        <td>결제금액 </td>
        <td><?php echo $LGD_AMOUNT ?></td>
    </tr>
    <tr>
        <td>구매자 이메일 </td>
        <td><?php echo $LGD_BUYEREMAIL ?></td>
    </tr>
    <tr>
        <td>주문번호 </td>
        <td><?php echo $LGD_OID ?></td>
    </tr>
    <tr>
        <td colspan="2">* 추가 상세 결제요청 파라미터는 메뉴얼을 참조하시기 바랍니다.</td>
    </tr>
    <tr>
        <td colspan="2"></td>
    </tr>    
    <tr>
        <td colspan="2">
		<input type="button" value="인증요청" onclick="launchCrossPlatform();"/>         
        </td>
    </tr>    
</table>
<?php
  foreach ($payReqMap as $key => $value) {
    echo "<input type='hidden' name='".$key."' id='$key' value='".$value."'>";
  }
?>

<!--// BBS e-Commerce 자체 필드-->
<input type="hidden" name="LGD_MERTKEY" id="LGD_MERTKEY" value="<?php echo $LGD_MERTKEY;?>"> <!--상점MertKey -->

</form>
</body>
</html>


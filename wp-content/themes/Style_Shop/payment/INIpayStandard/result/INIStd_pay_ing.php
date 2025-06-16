<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");
error_reporting(E_ALL);
ini_set("display_errors", 1);

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
$payCFG = unserialize($paymentConfig);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>

<?php
require_once('../libs/INIStdPayUtil.php');
require_once('../libs/HttpClient.php');
$util = new INIStdPayUtil();


try {
	if(!$_REQUEST["resultCode"] || !$_REQUEST['merchantData']){
?>
		<body>
			<script language="javascript">
				var homeURL="<?php echo home_url();?>";
				alert("잘못된 접근입니다.   ");
				location.href=homeURL;
			</script>
		</body>
<?php
		exit;
	}
	//#############################
	// 인증결과 파라미터 일괄 수신
	//#############################
	//		$var = $_REQUEST["data"];
	//		System.out.println("paramMap : "+ paramMap.toString());
	//#####################
	// 인증이 성공일 경우만
	//#####################
	if (strcmp("0000", $_REQUEST["resultCode"]) == 0) {
		//echo "####인증성공/승인요청####";
		//echo "<br/>";

		//############################################
		// 1.전문 필드 값 설정(***가맹점 개발수정***)
		//############################################
		$orderList=base64_decode($_REQUEST['merchantData']);
		parse_str($orderList, $V);

		if($payCFG['payment_inicis_escorw_use']=='on'){
			if($V['pay_how']=='C') $escrowYN='N';
			elseif(($V['pay_how']=='K' && $payCFG['payment_inicis_escorw_trans']=='on') || 
				($V['pay_how']=='V' && $payCFG['payment_inicis_escorw_vbank']=='on') ) $escrowYN='Y';
			else $escrowYN='N';
		}
		else $escrowYN='N';

		if($escrowYN=='Y') $paymentSignKey=$payCFG['payment_escrow_sign_key'];
		else $paymentSignKey=$payCFG['payment_sign_key'];


		$mid = $_REQUEST["mid"];     // 가맹점 ID 수신 받은 데이터로 설정
		$signKey = $paymentSignKey; // 가맹점에 제공된 키(이니라이트키) (가맹점 수정후 고정) !!!절대!! 전문 데이터로 설정금지

		$timestamp = $util->getTimestamp();   // util에 의해서 자동생성
		$charset = "UTF-8";        // 리턴형식[UTF-8,EUC-KR](가맹점 수정후 고정)
		$format = "JSON";        // 리턴형식[XML,JSON,NVP](가맹점 수정후 고정)
		// 추가적 noti가 필요한 경우(필수아님, 공백일 경우 미발송, 승인은 성공시, 실패시 모두 Noti발송됨) 미사용 
		//String notiUrl	= "";

		$authToken = $_REQUEST["authToken"];   // 취소 요청 tid에 따라서 유동적(가맹점 수정후 고정)
		$authUrl = $_REQUEST["authUrl"];    // 승인요청 API url(수신 받은 값으로 설정, 임의 세팅 금지)
		$netCancel = $_REQUEST["netCancel"];   // 망취소 API url(수신 받은f값으로 설정, 임의 세팅 금지)
		$ackUrl = $_REQUEST["checkAckUrl"];   // 가맹점 내부 로직 처리후 최종 확인 API URL(수신 받은 값으로 설정, 임의 세팅 금지)

		//#####################
		// 2.signature 생성
		//#####################
		$signParam["authToken"] = $authToken;  // 필수
		$signParam["timestamp"] = $timestamp;  // 필수
		// signature 데이터 생성 (모듈에서 자동으로 signParam을 알파벳 순으로 정렬후 NVP 방식으로 나열해 hash)
		$signature = $util->makeSignature($signParam);


		//#####################
		// 3.API 요청 전문 생성
		//#####################
		$authMap["mid"] = $mid;   // 필수
		$authMap["authToken"] = $authToken; // 필수
		$authMap["signature"] = $signature; // 필수
		$authMap["timestamp"] = $timestamp; // 필수
		$authMap["charset"] = $charset;  // default=UTF-8
		$authMap["format"] = $format;  // default=XML
		//if(null != notiUrl && notiUrl.length() > 0){
		//	authMap.put("notiUrl"		,notiUrl);
		//}

		try {

			$httpUtil = new HttpClient();

			//#####################
			// 4.API 통신 시작
			//#####################

			$authResultString = "";
			if ($httpUtil->processHTTP($authUrl, $authMap)) {
				$authResultString = $httpUtil->body;
			} else {
				echo "Http Connect Error\n";
				echo $httpUtil->errormsg;

				throw new Exception("Http Connect Error");
			}

			//############################################################
			//5.API 통신결과 처리(***가맹점 개발수정***)
			//############################################################
			//echo "## 승인 API 결과 ##";

			$resultMap = json_decode($authResultString, true);

			if (strcmp("0000", $resultMap["resultCode"]) == 0) { // 거래성공
				/*                         * ***************************************************************************
				 * 여기에 가맹점 내부 DB에 결제 결과를 반영하는 관련 프로그램 코드를 구현한다.  

				  [중요!] 승인내용에 이상이 없음을 확인한 뒤 가맹점 DB에 해당건이 정상처리 되었음을 반영함
				  처리중 에러 발생시 망취소를 한다.
				 * **************************************************************************** */


				/*                         * ***************************************************************************
				  내부로직 처리가 정상적으로 완료 되면 ackUrl로 결과 통신한다.
				  만약 ACK통신중 에러 발생시(exeption) 망취소를 한다.
				 * **************************************************************************** */
				$checkMap["mid"] = $mid;        // 필수					
				$checkMap["tid"] = $resultMap["tid"];    // 필수					
				$checkMap["applDate"] = $resultMap["applDate"];  // 필수					
				$checkMap["applTime"] = $resultMap["applTime"];  // 필수					
				$checkMap["price"] = $resultMap["TotPrice"];   // 필수					
				$checkMap["goodsName"] = $resultMap["goodsname"];  // 필수				
				$checkMap["charset"] = $charset;  // default=UTF-8					
				$checkMap["format"] = $format;  // default=XML		

				$ackResultString = "";
				if ($httpUtil->processHTTP($ackUrl, $checkMap)) {
					$ackResultString = $httpUtil->body;
				} else {
					echo "Http Connect Error\n";
					echo $httpUtil->errormsg;

					throw new Exception("Http Connect Error");
				}

				$ackMap = json_decode($ackResultString);
			}
?>
			<body onload="javascript:iniStd_ing.submit();">
				<form name="iniStd_ing" method="post" action="<?php echo $V['action_url'];?>/proc/order.exec.php">
				<!--공통 부분-->
					<input type="hidden" name="TID" value="<?php echo $resultMap["tid"];?>" /><!--거래 번호-->
					<input type="hidden" name="PayMethod" value="<?php echo $resultMap["payMethod"];?>" /><!--결제방법(지불수단)-->
					<input type="hidden" name="ResultCode" value="<?php echo $resultMap["resultCode"];?>"><!-- 결과코드 -->
					<input type="hidden" name="ResultMsg" value="<?php echo  $resultMap["resultCode"];?>">		<!-- 결과내용 -->
					<input type="hidden" name="TotPrice" value="<?php echo $resultMap["TotPrice"];?>" />		<!-- 결제완료금액 -->
					<input type="hidden" name="MOID" value="<?php echo $resultMap["MOID"];?>" />		<!-- 상점주문번호 -->
					<!--신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제 결과 데이터-->
					<input type="hidden" name="ApplDate" value="<?php echo $resultMap["applDate"];?>" />		<!-- 이니시스 승인날짜(YYYYMMDD) -->
					<input type="hidden" name="ApplTime" value="<?php echo $resultMap["applTime"];?>" />		<!-- 이니시스 승인시각(HHMMSS) -->
				<!--신용카드-->
					<input type="hidden" name="ApplNum" value="<?php echo $resultMap["applNum"];?>" />		<!-- 신용카드 승인번호 -->
					<input type="hidden" name="CARD_Quota" value="<?php echo $resultMap["CARD_Quota"];?>" />		<!-- 할부기간 -->
					<input type="hidden" name="CARD_Interest" value="<?php echo $resultMap["CARD_Interest"];?>" />		<!-- ("1"이면 무이자할부) -->
					<input type="hidden" name="CARD_Code" value="<?php echo $resultMap["CARD_Code"];?>" />		<!-- 신용카드사 코드 (01:하나(외환), 03:롯데, 04:현대, 06:국민,11:비씨(BC), 12:삼성, 14:신한, 34:하나,41:NH(농협))-->
				<!--가상계좌(무통장입금) 결제 결과 데이터-->
					<input type="hidden" name="VACT_Num" value="<?php echo $resultMap["VACT_Num"];?>" />		<!-- 가상계좌 번호-->
					<input type="hidden" name="VACT_BankCode" value="<?php echo $resultMap["VACT_BankCode"];?>" />		<!-- 입금할 은행 코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)-->
					<input type="hidden" name="VACT_Date" value="<?php echo $resultMap["VACT_Date"];?>" />		<!-- 입금예정일 (YYYYMMDD)-->
					<input type="hidden" name="VACT_InputName" value="<?php echo $resultMap["VACT_InputName"];?>" />		<!-- 송금자 명-->
					<input type="hidden" name="VACT_Name" value="<?php echo $resultMap["VACT_Name"];?>" />		<!-- 예금주 명-->
				<!--실시간계좌이체 결제 결과 데이터-->
					<input type="hidden" name="ACCT_BankCode" value="<?php echo $resultMap["ACCT_BankCode"];?>" />		<!-- 은행코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)-->
					<input type="hidden" name="CSHR_ResultCode" value="<?php echo $resultMap["CSHRResultCode"];?>" />		<!-- 현금영수증 발행결과코드-->
					<input type="hidden" name="CSHR_Type" value="<?php echo $resultMap["CSHR_Type"];?>" />		<!-- 현금영수증 발행구분코드(0 - 소득공제용, 1 - 지출증빙용)-->
					<!-- 추가 전달 변수 -->
					<input type="hidden" name="ini_buyername" value="<?php echo $V['order_name'];?>" />		<!-- 주문자명 -->
					<input type="hidden" name='pay_how' id='pay_how' value='<?php echo $V['pay_how'];?>' />	<!-- 결제방법 -->
					<input type="hidden" name='Column2' id='Column2' value='desktop' />	<!-- 디바이스 정보 -->
					<input type="hidden" name="orderList" value="<?php echo $orderList;?>">		<!-- 상품목록 -->
				</form>
			</body>
<?php
			/*
			echo "</table>
			<span style='padding-left : 100px;'>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!--input type='button' value='거래취소' onclick='cancelTid()' style='width : 50px ; height : 40px; font-size= 10pt; margin : 0 auto;' /-->
			</span>
			<form name='frm' method='post'> 
			<input type='hidden' name='tid' value='" . $resultMap["tid"] . "'/>
			</form>				
			</pre>";
			*/

			// 수신결과를 파싱후 resultCode가 "0000"이면 승인성공 이외 실패
			// 가맹점에서 스스로 파싱후 내부 DB 처리 후 화면에 결과 표시
			// payViewType을 popup으로 해서 결제를 하셨을 경우
			// 내부처리후 스크립트를 이용해 opener의 화면 전환처리를 하세요
			//throw new Exception("강제 Exception");
		} catch (Exception $e) {
?>
			<body>
				<script language="javascript">
					var getMsg="<?php echo $e->getMessage();?>";
					var getCode="<?php echo $e->getCode();?>";
					alert("[통신오류]"+getMsg+" (오류코드:"+getCode+")");
				</script>
			</body>
<?php
			exit;

			//    $s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
			//####################################
			// 실패시 처리(***가맹점 개발수정***)
			//####################################
			//---- db 저장 실패시 등 예외처리----//
			//$s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
			//echo $s;

			//#####################
			// 망취소 API
			//#####################
			/*
			$netcancelResultString = ""; // 망취소 요청 API url(고정, 임의 세팅 금지)
			if ($httpUtil->processHTTP($netCancel, $authMap)) {
				$netcancelResultString = $httpUtil->body;
			} else {
				echo "Http Connect Error\n";
				echo $httpUtil->errormsg;

				throw new Exception("Http Connect Error");
			}

			echo "## 망취소 API 결과 ##";

			$netcancelResultString = str_replace("<", "&lt;", $$netcancelResultString);
			$netcancelResultString = str_replace(">", "&gt;", $$netcancelResultString);

			echo "<pre>", $netcancelResultString . "</pre>";
			*/
			// 취소 결과 확인
		}
	} else {
?>
			<body>
				<script language="javascript">
					alert("상점 인증(Sign Key)에 실패하였습니다.");
				</script>
			</body>
<?php
		exit;
		//#############
		// 인증 실패시
		//#############
		//echo "<br/>";
		//echo "####인증실패####";

		//echo "<pre>" . var_dump($_REQUEST) . "</pre>";
	}
} catch (Exception $e) {
?>
			<body>
				<script language="javascript">
					var getMsg="<?php echo $e->getMessage();?>";
					var getCode="<?php echo $e->getCode();?>";
					alert("[통신오류]"+getMsg+" (오류코드:"+getCode+")");
				</script>
			</body>
<?php
	exit;

	//$s = $e->getMessage() . ' (오류코드:' . $e->getCode() . ')';
	//echo $s;
}
?>
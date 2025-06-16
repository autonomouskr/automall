<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

//*******************************************************************************
// FILE NAME : mx_rnoti.php
// FILE DESCRIPTION :
// 이니시스 smart phone 결제 결과 수신 페이지 샘플
// 기술문의 : ts@inicis.com
// HISTORY 
// 2010. 02. 25 최초작성 
// 2010  06. 23 WEB 방식의 가상계좌 사용시 가상계좌 채번 결과 무시 처리 추가(APP 방식은 해당 없음!!)
// WEB 방식일 경우 이미 P_NEXT_URL 에서 채번 결과를 전달 하였으므로, 
// 이니시스에서 전달하는 가상계좌 채번 결과 내용을 무시 하시기 바랍니다.
//*******************************************************************************

  $PGIP = $_SERVER['REMOTE_ADDR'];
  
  if($PGIP == "211.219.96.165" || $PGIP == "118.129.210.25")	//PG에서 보냈는지 IP로 체크
  {
		// 이니시스 NOTI 서버에서 받은 Value
		$P_TID;				// 거래번호
		$P_MID;				// 상점아이디
		$P_AUTH_DT;			// 승인일자
		$P_STATUS;			// 거래상태 (00:성공, 01:실패)
		$P_TYPE;			// 지불수단
		$P_OID;				// 상점주문번호
		$P_FN_CD1;			// 금융사코드1
		$P_FN_CD2;			// 금융사코드2
		$P_FN_NM;			// 금융사명 (은행명, 카드사명, 이통사명)
		$P_AMT;				// 거래금액
		$P_UNAME;			// 결제고객성명
		$P_RMESG1;			// 결과코드
		$P_RMESG2;			// 결과메시지
		$P_NOTI;			// 노티메시지(상점에서 올린 메시지)
		$P_AUTH_NO;			// 승인번호
	

		$P_TID = $_REQUEST[P_TID];
		$P_MID = $_REQUEST[P_MID];
		$P_AUTH_DT = $_REQUEST[P_AUTH_DT];
		$P_STATUS = $_REQUEST[P_STATUS];
		$P_TYPE = $_REQUEST[P_TYPE];
		$P_OID = $_REQUEST[P_OID];
		$P_FN_CD1 = $_REQUEST[P_FN_CD1];
		$P_FN_CD2 = $_REQUEST[P_FN_CD2];
		$P_FN_NM = $_REQUEST[P_FN_NM];
		$P_AMT = $_REQUEST[P_AMT];
		$P_UNAME = $_REQUEST[P_UNAME];
		$P_RMESG1 = $_REQUEST[P_RMESG1];
		$P_RMESG2 = $_REQUEST[P_RMESG2];
		$P_NOTI = $_REQUEST[P_NOTI];
		$P_AUTH_NO = $_REQUEST[P_AUTH_NO];


		//WEB 방식의 경우 가상계좌 채번 결과 무시 처리
		//(APP 방식의 경우 해당 내용을 삭제 또는 주석 처리 하시기 바랍니다.)
		 if($P_TYPE == "VBANK")	//결제수단이 가상계좌이며
        	{
           	   if($P_STATUS != "02") //입금통보 "02" 가 아니면(가상계좌 채번 : 00 또는 01 경우)
           	   {
	              //echo "OK";
        	      //return;
           	   }
        	}

  		$PageCall_time = date("H:i:s");

		$value = array(
				"PageCall time" => iconv("euc-kr","utf-8",$PageCall_time),
				"P_TID"			=> iconv("euc-kr","utf-8",$P_TID),  
				"P_MID"     => iconv("euc-kr","utf-8",$P_MID),  
				"P_AUTH_DT" => iconv("euc-kr","utf-8",$P_AUTH_DT),      
				"P_STATUS"  => iconv("euc-kr","utf-8",$P_STATUS),
				"P_TYPE"    => iconv("euc-kr","utf-8",$P_TYPE),     
				"P_OID"     => iconv("euc-kr","utf-8",$P_OID),  
				"P_FN_CD1"  => iconv("euc-kr","utf-8",$P_FN_CD1),
				"P_FN_CD2"  => iconv("euc-kr","utf-8",$P_FN_CD2),
				"P_FN_NM"   => iconv("euc-kr","utf-8",$P_FN_NM),  
				"P_AMT"     => iconv("euc-kr","utf-8",$P_AMT),  
				"P_UNAME"   => iconv("euc-kr","utf-8",$P_UNAME),  
				"P_RMESG1"  => iconv("euc-kr","utf-8",$P_RMESG1),  
				"P_RMESG2"  => iconv("euc-kr","utf-8",$P_RMESG2),
				"P_NOTI"    => iconv("euc-kr","utf-8",$P_NOTI),  
				"P_AUTH_NO" => iconv("euc-kr","utf-8",$P_AUTH_NO)
				);

 		// 결제처리에 관한 로그 기록
		if($value['P_TYPE'] && $value['P_OID'] && $value['P_AMT']){
			$pgCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_pg_inicis WHERE MOID='".$value['P_OID']."'");
			if($pgCount <= 0) {
				$V=Array();

				// 1. 결제 결과
				$V['PayMethod']=($value['P_TYPE']=='CARD' || $value['P_TYPE']=='ISP')?"Card":"VBank";  // 결제 방법 (신용카드 : Card, ISP : VCard, 은행계좌 : DirectBank, 무통장입금 : VBank)

				$V['TID']=$value['P_TID'];
				$V['ResultCode']=$value['P_STATUS'];

				$V['ResultErrorCode']=($value['P_STATUS']!='00')?$value['P_STATUS']:"";  // 에러 메세지

				$V['ResultMsg']=$value['P_RMESG1'];
				$V['MOID']=$value['P_OID']; // 상점주문번호
				$V['TotPrice']=$value['P_AMT']; // 결제완료금액

				// 2.신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제 결과 데이터
				$V['ApplDate']=substr($value['P_AUTH_DT'],0,8); // 이니시스 승인날짜(YYYYMMDD)
				$V['ApplTime']=substr($value['P_AUTH_DT'],8,6); // 이니시스 승인시각(HHMMSS)

				// 3. 신용카드 결제 결과 데이터
				$V['ApplNum']=$value['P_AUTH_NO']; // 신용카드 승인번호
				$V['CARD_Quota']=""; // 할부기간
				$V['CARD_Interest']=""; // ("1"이면 무이자할부)
				$V['CARD_Code']=$value['P_FN_CD1']; // 신용카드사 코드 (01:하나(외환), 03:롯데, 04:현대, 06:국민,11:비씨(BC), 12:삼성, 14:신한, 34:하나,41:NH(농협))

				// 4. 실시간 계좌이체 결제 결과 데이터
				$V['ACCT_BankCode']=""; // 은행코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)
				$V['CSHR_ResultCode']=""; // 현금영수증 발행결과코드
				$V['CSHR_Type']=""; // 현금영수증 발행구분코드

				// 5. 가상계좌(무통장입금) 결제 결과 데이터
				$V['VACT_Num']=""; // 가상계좌 번호
				$V['VACT_BankCode']=""; // 입금할 은행 코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)
				$V['VACT_Date']=""; // 입금예정일 (YYYYMMDD)
				$V['VACT_InputName']=""; // 송금자 명
				$V['VACT_Name']="KPAY"; // 예금주 명

				$wpdb->query("INSERT INTO bbse_commerce_pg_inicis SET PayMethod='".$V['PayMethod']."', TID='".$V['TID']."', ResultCode='".$V['ResultCode']."', ResultErrorCode='".$V['ResultErrorCode']."', ResultMsg='".$V['ResultMsg']."', MOID='".$V['MOID']."', TotPrice='".$V['TotPrice']."', ApplDate='".$V['ApplDate']."', ApplTime='".$V['ApplTime']."', ApplNum='".$V['ApplNum']."', CARD_Quota='".$V['CARD_Quota']."', CARD_Interest='".$V['CARD_Interest']."', CARD_Code='".$V['CARD_Code']."', CSHR_ResultCode='".$V['CSHR_ResultCode']."', CSHR_Type='".$V['CSHR_Type']."', VACT_Num='".$V['VACT_Num']."', VACT_BankCode='".$V['VACT_BankCode']."', VACT_Date='".$V['VACT_Date']."', VACT_InputName='".$V['VACT_InputName']."', VACT_Name='".$V['VACT_Name']."'");
			}
		}
 
		/***********************************************************************************
		 ' 위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로 실패시는 "FAIL" 을
		 ' 리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
		 ' (주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
		 ' 기타 다른 형태의 echo "" 는 하지 않으시기 바랍니다
		'***********************************************************************************/
		$inCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_pg_inicis WHERE MOID='".$V['MOID']."'");
		if($inCount > 0) {
			echo "OK"; //절대로 지우지 마세요
		}
		else{
			echo "FAIL";
		}
  }

?>

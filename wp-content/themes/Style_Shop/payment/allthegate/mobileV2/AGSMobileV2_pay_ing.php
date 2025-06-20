<?
	///////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// 올더게이트 모바일 승인 페이지 (EUC-KR)
	//
	///////////////////////////////////////////////////////////////////////////////////////////////////
	
	require_once ("./lib/AGSMobile.php");

	$tracking_id = $_REQUEST["tracking_id"];
	$transaction = $_REQUEST["transaction"];
	$StoreId = $_REQUEST["StoreId"];
	$log_path = null; 
	// log파일 저장할 폴더의 경로를 지정합니다.
    // 경로의 값이 null로 되어있을 경우 "현재 작업 디렉토리의 /lib/log/"에 저장됩니다.
    
	$agsMobile = new AGSMobile($store_id,$tracking_id,$transaction, $log_path);
	$agsMobile->setLogging(false); //true : 로그기록, false : 로그기록안함.
	
	////////////////////////////////////////////////////////
	//
	// getTrackingInfo() 는 최초 올더게이트 페이지를 호출할 때 전달 했던 Form 값들이 Array()로 저장되어 있습니다. 
	//
	////////////////////////////////////////////////////////
	
	$info = $agsMobile->getTrackingInfo(); //$info 변수는 array() 형식입니다.
   
    /////////////////////////////////////////////////////////////////////////////////
    //  -- tracking_info에 들어있는 컬럼 --
    // 
    //	  결제방법 : AuthTy (card,hp,virtual)
    //	  서브결제방법 : SubTy (카드일 경우 세팅 : isp,visa3d)
    //
    //    회원아이디 : UserId
    //    구매자이름 : OrdNm  
    //    상점이름 : StoreNm
    //    결제방법 : Job 
    //    상품명 : ProdNm
    // 
    //    휴대폰번호 : OrdPhone
    //    수신자명 : RcpNm
    //    수신자연락처 : RcpPhone
    //    주문자주소 : OrdAddr
    //    주문번호 : OrdNo
    //    배송지주소 : DlvAddr
    //    상품코드 : ProdCode
    //    입금예정일 : VIRTUAL_DEPODT
    //    상품종류 : HP_UNITType
    //    성공 URL : RtnUrl
    //    상점아이디 : StoreId
    //    가격 : Amt
    //    이메일 : UserEmail
    //    상점URL : MallUrl
    //    취소 URL : CancelUrl
    //    통보페이지 : MallPage
    // 
    //    기타요구사항 : Remark
    //    추가사용필드1 : Column1
    //    추가사용필드1 : Column2
    //    추가사용필드1 : Column3
    //    CP아이디 : HP_ID
    //    CP비밀번호 :  HP_PWD
    //    SUB-CP아이디 : HP_SUBID
    //    상품코드 :  ProdCode
    //    결제정보 : DeviId ( 9000400001:일반결제, 9000400002:무이자결제)
    //    카드사선택 : CardSelect
    //    할부기간 :  QuotaInf
    //    무이자 할부기간: NointInf
    // 
    ////////////////////////////////////////////////////////////////////////////////////////////////
    
    // tracking_info의 정보들은 아래의 방법으로 가져오시면 됩니다 
    //
    //    print_r($info); //tracking_info
    //    echo "주문번호 : ".$info["OrdNo"]."</br>";
    //    echo "상품명 : ".$info["ProdNm"]."</br>";
    //    echo "결제방법 : ".$info["Job"]."</br>";
    //    echo "회원아이디 : ".$info["UserId"]."</br>";
    //    echo "구매자이름 : ".$info["OrdNm"]."</br>";  
	//
	/*
	echo "AuthTy : ".$info["AuthTy"]."</br>";
    echo "SubTy : ".$info["SubTy"]."</br>";  
    */
	
	$ret = $agsMobile->approve();
	
	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     //
     // 결제결과에 따른 상점DB 저장 및 기타 필요한 처리작업을 수행하는 부분입니다.
     // 아래의 결과값들을 통하여 각 결제수단별 결제결과값을 사용하실 수 있습니다.
     // 
     // $ret는 array() 형식으로 다음과 같은 구조를 가집니다.
     //
     // $ret = array (
     //        'status' => 'ok' | 'error' //승인성공일 경우 ok , 실패면 error
     //		  'message' => '에러일 경우 에러메시지'
     //		  'data' => 결제수단별 정보 array() //승인성공일 경우만 세팅됩니다.
     //	) 
     ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    
    $rSuccYn='n'; // 성공 여부(추가)
	$rApprTm=''; // 승인시간(추가)

	if ($ret['status'] == "ok") {
		/// 승인 성공 
        $rSuccYn='y'; // 성공 여부(추가)
		$rApprTm=$ret["data"]['AdmTime']; // 승인시간(추가)

		/*
        echo "성공여부: ".$ret['status']."<br/>";   //ok이면 성공..
        echo "결과메시지: ".$ret["message"]."<br/>";   
                
        //data 이하에 서버 응답 메시지가 있습니다.
		echo "업체ID : ".$ret["data"]["StoreId"]."<br/>";        
        echo "주문번호: ".$ret["data"]["OrdNo"]."<br/>";   
        echo "거래금액: ".$ret["data"]["Amt"]."원 <br/>"; 
		echo "tracking_id: ".$tracking_id."<br/>";          
		*/        
        
        if($ret["paytype"] == "card"){
            
            /// 카드 결제 후 받은 정보 
			/*
            echo "AuthTy : ".$ret["data"]["AuthTy"]."<br/>";        
            echo "SubTy: ".$ret["data"]["SubTy"]."<br/>";
            
            echo "업체ID : ".$ret["data"]["StoreId"]."<br/>";        
            echo "망취소ID : ".$ret["data"]["NetCancelId"]."<br/>";
        	echo "주문번호: ".$ret["data"]["OrdNo"]."<br/>";   
	        echo "거래금액: ".$ret["data"]["Amt"]."원 <br/>";
	        echo "에스크로여부 : ".$ret["data"]["EscrowYn"]."<br/>";  //y이면 escrow
	        echo "무이자여부: ".$ret["data"]["NoInt"]."<br/>";  //y이면 무이자
	        echo "에스크로전문번호 : ".$ret["data"]["EscrowSendNo"]."<br/>";  
	        	                                         
            echo "전문코드 : ".$ret["data"]["BusiCd"]."<br/>";        
            echo "거래번호: ".$ret["data"]["DealNo"]."<br/>";     
            echo "승인번호: ".$ret["data"]["AdmNo"]."<br/>";     
            echo "승인시각: ".$ret["data"]['AdmTime']."<br/>";    
            echo "카드사코드: ".$ret["data"]["CardCd"]."<br/>";     
            echo "카드사명: ".$ret["data"]["CardNm"]."<br/>";     
            echo "할부개월수: ".$ret["data"]["PartialMm"]."<br/>";     
            */
            /////////////////////////////////////////
            //
            // 카드 거래의 경우,
            // 상점 DB 및 기타 상점측 예외상황으로 결제를 바로 취소해야 한다면
            // 아래의 승인 이후 아래의 함수 호출로 취소가 가능합니다.
            //
            /////////////////////////////////////////
            
            // 아래 부분을 주석해제 하면 바로 강제 취소 할 수 있습니다. (카드 정상 승인 이후에만 가능)
            
			
			/*
			$cancelRet = $agsMobile->forceCancel();
    
			// 상점은 아래에서 처리하세요
			if ($cancelRet['status'] == "ok") {
				echo "취소 성공<br/>";
		        echo "업체ID : ".$cancelRet["data"]["StoreId"]."<br/>";     
		        echo "승인번호: ".$cancelRet["data"]["AdmNo"]."<br/>";   
		        echo "승인시각: ".$cancelRet["data"]["AdmTime"]."<br/>";   
		        echo "코드: ".$cancelRet["data"]['Code']."<br/>";   
			
			}else {
				//취소 통신 실패
				echo "취소 실패 : ".$cancelRet['message']; // 에러 메시지
			}
			*/
			//////////////////////////////////////////////
			//
			// 영수증 사용시 아래의 링크를 사용하시면 됩니다.
			//
			//////////////////////////////////////////////
			/*
			$url = "http://www.allthegate.com/customer/receiptLast3.jsp";
			$url .= "?sRetailer_id=".$ret["data"]["StoreId"];
			$url .= "?approve=".$ret["data"]["AdmNo"];
			$url .= "?send_no=".$ret["data"]["DealNo"];
			$url .= "?send_dt=".substr($ret["data"]["AdmTime"],0,8);
			*/
        }else if($ret["paytype"] == "hp"){
            /// 핸드폰 결제 후 받은 정보
			/*
            echo "AuthTy : ".$ret["data"]["AuthTy"]."<br/>";        
            echo "SubTy: ".$ret["data"]["SubTy"]."<br/>";
            
            echo "업체ID : ".$ret["data"]["StoreId"]."<br/>";        
            echo "망취소ID : ".$ret["data"]["NetCancelId"]."<br/>";
        	echo "주문번호: ".$ret["data"]["OrdNo"]."<br/>";   
	        echo "거래금액: ".$ret["data"]["Amt"]."원 <br/>";
	        
            echo "핸드폰통신사 : ".$ret["data"]["PhoneCompany"]."<br/>";      
            echo "핸드폰번호 : ".$ret["data"]["Phone"]."<br/>";      
            echo "핸드폰결제 TID : ".$ret["data"]["AdmTID"]."<br/>";    
            */
            /////////////////////////////////////////
            //
            // 휴대폰 거래의 경우,
            // 상점 DB 및 기타 상점측 예외상황으로 결제를 바로 취소해야 한다면
            // 아래의 승인 이후 아래의 함수 호출로 취소가 가능합니다.
            //
            /////////////////////////////////////////
            
            // 아래 부분을 주석해제 하면 바로 강제 취소 할 수 있습니다. (휴대폰 정상 승인 이후에만 가능)
            
//            $cancelRet = $agsMobile->forceCancel();
//    
//			// 상점은 아래에서 처리하세요
//			if ($cancelRet['status'] == "ok") {
//				
//		        echo "업체ID : ".$cancelRet["data"]["StoreId"]."<br/>";     
//		   		echo "핸드폰결제 TID : ".$cancelRet["data"]["AdmTID"]."<br/>";    
//				
//			}else {
//				//취소 통신 실패
//				echo "취소 실패 : ".$cancelRet['message']; // 에러 메시지
//			}
            
        }else if($ret["paytype"] == "virtual"){
			$rApprTm=$ret["data"]['SuccessTime']; // 승인시간(추가)

            /// 가상계좌 처리 후 받은 정보 ///
            
            ////////////////////////////////////////////////////////
            // 
            //   가상계좌의 결제성공은 가상계좌발급의 성공만을 의미하며 입금대기상태로 실제 고객이 입금을 완료한 것은 아닙니다.
            //   따라서 가상계좌 결제완료시 결제완료로 처리하여 상품을 배송하시면 안됩니다.
            //   결제후 고객이 발급받은 계좌로 입금이 완료되면 MallPage(상점 입금통보 페이지(가상계좌))로 입금결과가 전송되며
            //   이때 비로소 결제가 완료되게 되므로 결제완료에 대한 처리(배송요청 등)은  MallPage에 작업해주셔야 합니다.
            //   
            //   승인일자 : $ret["data"]["SuccessTime"]
            //   가상계좌번호 : $ret["data"]["VirtualNo"]
            //   입금은행코드 : $ret["data"]["BankCode"]
            // 
            ////////////////////////////////////////////////////////
            /*
            echo "AuthTy : ".$ret["data"]["AuthTy"]."<br/>";        
            echo "SubTy: ".$ret["data"]["SubTy"]."<br/>";
            
            
      		echo "업체ID : ".$ret["data"]["StoreId"]."<br/>";        
            echo "망취소ID : ".$ret["data"]["NetCancelId"]."<br/>";
        	echo "주문번호: ".$ret["data"]["OrdNo"]."<br/>";   
	        echo "거래금액: ".$ret["data"]["Amt"]."원 <br/>";
	        echo "에스크로여부 : ".$ret["data"]["EscrowYn"]."<br/>";  //y이면 escrow
	        echo "에스크로전문번호 : ".$ret["data"]["EscrowSendNo"]."<br/>";
	        
            echo "승인일자 : ".$ret["data"]["SuccessTime"]."<br/>";        
            echo "가상계좌번호 : ".$ret["data"]["VirtualNo"]."<br/>";         
            echo "입금은행코드 : ".$ret["data"]["BankCode"]."<br/>";         
            echo "입금기한 : ".$ret["data"]["DueDate"]."<br/>";
			*/
        }
        
		
	
	}else {
		/// 승인 실패 
		//echo "승인실패 : ".$ret['message']."<br/>"; // 에러 메시지
	}
?>

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body onload="javascript:frmAGS_pay_ing.submit();">
<form name="frmAGS_pay_ing" method="post" action="<?php echo $info['Column1']; ?>/proc/order.exec.php">
<!-- 각 결제 공통 사용 변수 -->
<input type="hidden" name="AuthTy" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["AuthTy"]); ?>">		<!-- 결제형태 -->
<input type="hidden" name="SubTy" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["SubTy"]); ?>">			<!-- 서브결제형태 -->
<input type="hidden" name="rStoreId" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["StoreId"]); ?>">	<!-- 상점아이디 -->
<input type="hidden" name="rOrdNo" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["OrdNo"]); ?>">		<!-- 주문번호 -->
<input type="hidden" name="rProdNm" value="<?php echo iconv("EUC-KR","UTF-8",$info["ProdNm"]); ?>">		<!-- 상품명 -->
<input type="hidden" name="rAmt" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["Amt"]); ?>">			<!-- 결제금액 -->
<input type="hidden" name="rOrdNm" value="<?php echo iconv("EUC-KR","UTF-8",$info["OrdNm"]); ?>">		<!-- 주문자명 -->
<input type="hidden" name="rSuccYn" value="<?php echo iconv("EUC-KR","UTF-8",$rSuccYn); ?>">	<!-- 성공여부 -->
<input type="hidden" name="rResMsg" value="<?php echo iconv("EUC-KR","UTF-8",$ret["message"]); ?>">	<!-- 결과메시지 -->
<input type="hidden" name="rApprTm" value="<?php echo iconv("EUC-KR","UTF-8",$rApprTm); ?>">	<!-- 승인시간 -->

<!-- 신용카드 결제 사용 변수 -->
<input type="hidden" name="rBusiCd" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["BusiCd"]); ?>">		<!-- (신용카드공통)전문코드 -->
<input type="hidden" name="rApprNo" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["AdmNo"]); ?>">		<!-- (신용카드공통)승인번호 -->
<input type="hidden" name="rCardCd" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["CardCd"]); ?>">	<!-- (신용카드공통)카드사코드 -->
<input type="hidden" name="rDealNo" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["DealNo"]); ?>">			<!-- (신용카드공통)거래번호 -->

<input type="hidden" name="rCardNm" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["CardNm"]); ?>">	<!-- 카드사명 -->
<input type="hidden" name="rMembNo" value="">	<!-- (안심클릭,일반사용)가맹점번호 -->
<input type="hidden" name="rAquiCd" value="">		<!-- (안심클릭,일반사용)매입사코드 -->
<input type="hidden" name="rAquiNm" value="">	<!-- (안심클릭,일반사용)매입사명 -->

<!-- 핸드폰 결제 사용 변수 -->
<input type="hidden" name="rHP_HANDPHONE" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["Phone"]); ?>">		<!-- 핸드폰번호 -->
<input type="hidden" name="rHP_COMPANY" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["PhoneCompany"]); ?>">			<!-- 통신사명(SKT,KTF,LGT) -->
<input type="hidden" name="rHP_TID" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["AdmTID"]); ?>">					<!-- 결제TID -->
<input type="hidden" name="rHP_DATE" value="<?php echo date("Y-m-d H:i:s"); ?>">				<!-- 결제일자 -->

<!-- 가상계좌 결제 사용 변수 -->
<input type="hidden" name="rVirNo" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["VirtualNo"]); ?>">					<!-- 가상계좌번호 -->
<input type="hidden" name="VIRTUAL_CENTERCD" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["BankCode"]); ?>">	<!--입금가상계좌은행코드(우리은행:20) -->

<!-- 이지스에스크로 결제 사용 변수 -->
<input type="hidden" name="ES_SENDNO" value="<?php echo iconv("EUC-KR","UTF-8",$ret["data"]["EscrowSendNo"]); ?>">				<!-- 이지스에스크로(전문번호) -->

<input type="hidden" name="Column2" value="<?php echo $info['Column2']; ?>"><!-- 디바이스 정보 -->
<input type="hidden" name="Column3" value="<?php echo $info['Column3']; ?>"><!-- $ordr_idxx -->
<input type="hidden" name="Remark" value="<?php echo $info['Remark']; ?>"><!-- cart의 idx, 사용안함 -->
</form>
</body> 
</html>
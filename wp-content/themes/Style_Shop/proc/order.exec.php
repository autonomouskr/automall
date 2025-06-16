<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $current_user,$theme_shortname;

wp_get_current_user();

$V = $_POST;
$remoteAddr=explode(".",$_SERVER['REMOTE_ADDR']);
$orderConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order'");
$orderCFG = unserialize($orderConfig);
$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
$paymentCFG = unserialize($paymentConfig);

if($V['pay_how']) $payHow=$V['pay_how'];
else{
	if($V['Column2'] == "mobile" || $V['Column2'] == "tablet") { // 모바일 카드,계좌이체,가상계좌 체크
		$mobileData = $wpdb->get_var("SELECT goods_option_basic FROM bbse_commerce_cart WHERE goods_idx='0' AND user_id='".$V['Column3']."'");
		$mData = unserialize(base64_decode($mobileData));
		$payHow=$mData['pay_how'];
	}
}
$pay_status = "PW";
//무통장입금
if($payHow == "B") {
	$bank = unserialize(base64_decode($V['pay_info']));
	$bankInfo['bank_name'] = $bank['bank_name'];
	$bankInfo['bank_no'] = $bank['bank_account_number'];
	$bankInfo['bank_owner'] = $bank['bank_owner_name'];
	$pay_info=serialize($bankInfo);
	$input_name=$V['input_name'];
	$order_status="PR";
	$sendType = "order-ready";
	$payMode = "01";
}else if($payHow == "E"){
    $bank = unserialize(base64_decode($V['pay_info']));
    $bankInfo['bank_name'] = $bank['bank_name'];
    $bankInfo['bank_no'] = $bank['bank_account_number'];
    $bankInfo['bank_owner'] = $bank['bank_owner_name'];
    $pay_info=serialize($bankInfo);
    $input_name=$V['input_name'];
    $order_status="PR";
    $sendType = "order-ready";
    $payMode = "02";
}else{
	if(($payHow=='C' || $payHow=='K' || $payHow=='V') && $paymentCFG['payment_agent']=='allthegate'){ // 올더게이트 결제완료
		$pay_info = "";
		$pgCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_pg_agspay WHERE rOrdNo='".$V['rOrdNo']."'");
		if($pgCount == 0) {
			$wpdb->query("INSERT INTO bbse_commerce_pg_agspay SET AuthTy='".$V['AuthTy']."',SubTy='".$V['SubTy']."',rStoreId='".$V['rStoreId']."',rOrdNo='".$V['rOrdNo']."',ProdNm='".$V['ProdNm']."',rAmt='".$V['rAmt']."',OrdNm='".$V['OrdNm']."',AGS_HASHDATA='".$V['AGS_HASHDATA']."',rSuccYn='".$V['rSuccYn']."',rResMsg='".$V['rResMsg']."',rApprTm='".$V['rApprTm']."',rBusiCd='".$V['rBusiCd']."',rApprNo='".$V['rApprNo']."',rCardCd='".$V['rCardCd']."',rDealNo='".$V['rDealNo']."',rCardNm='".$V['rCardNm']."',rMembNo='".$V['rMembNo']."',rAquiCd='".$V['rAquiCd']."',rAquiNm='".$V['rAquiNm']."',ICHE_OUTBANKNAME='".$V['ICHE_OUTBANKNAME']."',ICHE_OUTBANKMASTER='".$V['ICHE_OUTBANKMASTER']."',ICHE_AMOUNT='".$V['ICHE_AMOUNT']."',rHP_HANDPHONE='".$V['rHP_HANDPHONE']."',rHP_COMPANY='".$V['rHP_COMPANY']."',rHP_TID='".$V['rHP_TID']."',rHP_DATE='".$V['rHP_DATE']."',rARS_PHONE='".$V['rARS_PHONE']."',rVirNo='".$V['rVirNo']."',VIRTUAL_CENTERCD='".$V['VIRTUAL_CENTERCD']."',ES_SENDNO='".$V['ES_SENDNO']."'");
		}
		if($V['rSuccYn'] == "y") {// 결제성공

			if($V['AuthTy'] != "virtual") {
				$order_status="PE";
				$input_date = current_time('timestamp');
				$sendType = "order-input";
			}else{
				$order_status="PR";
				$sendType = "order-ready";
			}

			if($V['Column2'] == "mobile" || $V['Column2'] == "tablet") {
				$orderData = $wpdb->get_var("SELECT goods_option_basic FROM bbse_commerce_cart WHERE goods_idx='0' AND user_id='".$V['Column3']."'");
				$V = unserialize(base64_decode($orderData));
				$input_name=$_POST['order_name'];
			}else{
				$input_name=$_POST['order_name'];
				parse_str($V['orderList'], $V);
			}

		}else{// 결제실패
			echo "<script>alert('결제 처리중 오류가 발생하였습니다.\\n(".$V['rResMsg'].")\\n\\n고객센터로 문의해주세요.');location.href='".home_url()."';</script>";
			exit;
		}
	}
	elseif((($payHow=='C' || $payHow=='K' || $payHow=='V') && $paymentCFG['payment_agent']=='INIpay50') || $payHow=='EKP'){ // 이니시스 결제완료
		/*
			1. 결제 결과
			- PayMethod => 결제 방법 (신용카드 : Card, ISP : VCard, 은행계좌 : DirectBank, 무통장입금 : VBank, 핸드폰 : HPP, 전화결제 (ars전화 결제) : Ars1588Bill, 전화결제 (받는전화결제) : PhoneBill, OK CASH BAG POINT : OCBPoint, 문화상품권 : Culture, K-merce 상품권: KMC_, 틴캐시 결제 : TEEN, 게임문화 상품권 : DGCL)
			- TID => 거래번호
			- ResultCode => 결과코드
			- ResultErrorCode => 에러코드(에러인 경우)
			- ResultMsg => 결과내용
			- MOID => 상점주문번호
			- TotPrice => 결제완료금액

			2. 신용카드,ISP,핸드폰, 전화 결제, 은행계좌이체, OK CASH BAG Point 결제 결과 데이터
			- ApplDate => 이니시스 승인날짜(YYYYMMDD)
			- ApplTime => 이니시스 승인시각(HHMMSS)

			3. 신용카드 결제 결과 데이터
			- ApplNum => 신용카드 승인번
			- CARD_Quota => 할부기간
			- CARD_Interest => 1 이면 무이자할부
			- CARD_Code => 신용카드사 코드 (01:하나(외환), 03:롯데, 04:현대, 06:국민,11:비씨(BC), 12:삼성, 14:신한, 34:하나,41:NH(농협))

			4. 실시간 계좌이체 결제 결과 데이터
			- CARD_Code => 은행코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행
			- CSHR_ResultCode => 현금영수증 발행결과코드
			- CSHR_Type => 현금영수증 발행구분코드

			5. 가상계좌(무통장입금) 결제 결과 데이터
			- VACT_Num => 가상계좌 번호
			- VACT_BankCode => 입금할 은행 코드 (03:기업은행, 04:국민은행, 05:외환은행, 06:국민은행(구 주택은행), 07:수협중앙회, 11:농협중앙회, 12:단위농협, 20:우리은행, 21:조흥은행, 23:제일은행, 32:부산은행, 71:우체국, 81:하나은행, 88:신한은행)
			- VACT_Date => 입금예정일 (YYYYMMDD)
			- VACT_InputName => 송금자 명
			- VACT_Name => 예금주 명
		*/

		$pgCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_pg_inicis WHERE MOID='".$V['MOID']."'");
		if($pgCount == 0) {
			$wpdb->query("INSERT INTO bbse_commerce_pg_inicis SET PayMethod='".$V['PayMethod']."', TID='".$V['TID']."', ResultCode='".$V['ResultCode']."', ResultErrorCode='".$V['ResultErrorCode']."', ResultMsg='".$V['ResultMsg']."', MOID='".$V['MOID']."', TotPrice='".$V['TotPrice']."', ApplDate='".$V['ApplDate']."', ApplTime='".$V['ApplTime']."', ApplNum='".$V['ApplNum']."', CARD_Quota='".$V['CARD_Quota']."', CARD_Interest='".$V['CARD_Interest']."', CARD_Code='".$V['CARD_Code']."', CSHR_ResultCode='".$V['CSHR_ResultCode']."', CSHR_Type='".$V['CSHR_Type']."', VACT_Num='".$V['VACT_Num']."', VACT_BankCode='".$V['VACT_BankCode']."', VACT_Date='".$V['VACT_Date']."', VACT_InputName='".$V['VACT_InputName']."', VACT_Name='".$V['VACT_Name']."'");
		}

		if($V['ResultCode']=='00'){ // 결제성공
			if($payHow=='EKP'){
				if($V['PayMethod']=='Card' || $V['PayMethod']=='VCard') $payHow='C';
				elseif($V['PayMethod']=='DirectBank') $payHow='K';
				elseif($V['PayMethod']=='VBank') $payHow='V';
				else $payHow='C';

				$ezpayUse='EKP';
			}
			else{
				$ezpayUse='';
			}

			if($V['PayMethod'] != "VBank") {
				$order_status="PE";
				$input_date = current_time('timestamp');
				$sendType = "order-input";
			}else{
				$order_status="PR";
				$sendType = "order-ready";
			}

			if($V['Column2'] == "mobile" || $V['Column2'] == "tablet") {
				$orderData = $wpdb->get_var("SELECT goods_option_basic FROM bbse_commerce_cart WHERE goods_idx='0' AND user_id='".$V['Column3']."'");
				$V = unserialize(base64_decode($orderData));
				$input_name=$V['ini_buyername'];
			}else{
				$input_name=$V['ini_buyername'];
				parse_str($V['orderList'], $V);
			}
		}
		else{
			echo "<script>alert('결제 처리중 오류가 발생하였습니다.\\n(".$V['ResultErrorCode']." : ".$V['ResultMsg'].")\\n\\n고객센터로 문의해주세요.');location.href='".home_url()."';</script>";
			exit;
		}
	}
	//나이스페이 추가
	elseif($V['nicepg']=='NICE'){ // 이니시스 결제완료
		$payHow = $V['pay_how'];

		if($V['PayMethod'] != "VBANK") {
			$order_status="PE";
			$input_date = current_time('timestamp');
			$sendType = "order-input";
		}else{
			$order_status="PR";
			$sendType = "order-ready";
		}
		$input_name = $V['ini_buyername'];
		//결과값 저장
		$pay_info = $V['res'];
		
		parse_str($V['orderList'], $V);
		
		$ezpayUse = 'NICE';
	}
	elseif((($payHow=='C' || $payHow=='K' || $payHow=='V') && $paymentCFG['payment_agent']=='lguplusXPay') || $payHow=='EPN'){ // 유플러스, Paynow 결제완료
		/*
			1. 결제 결과
				LGD_RESPCODE : 결과코드
				LGD_PAYTYPE : 결제형태
				LGD_PAYNOW_TRANTYPE : 페이나우 사용여부 (사용 =>1, 사용안함 => '')
				LGD_RESPMSG : 결과메세지
				LGD_PAYKEY : LG유플러스인증키
				LGD_TID : LG유플러스 거래번호
				LGD_HASHDATA : 해쉬 데이터
				LGD_PAYDATE : 결제일시(yyyyMMddHHmmss 형식)
				LGD_MID : LG유플러스 상점아이디
				LGD_OID : 주문번호
				LGD_PRODUCTINFO : 상품명
				LGD_AMOUNT : 결제금액
				LGD_BUYER : 구매자명
				LGD_BUYERID : 구매자ID
				LGD_BUYERIP : 구매자ID
				LGD_BUYERPHONE : 구매자 전화번호
				LGD_BUYEREMAIL : 구매자 이메일
				LGD_TRANSAMOUNT : 환율적용금액
				LGD_FINANCECODE : 결제기관코드
				LGD_FINANCENAME : 결제기관명

			2. 신용카드 결제 결과 데이터
				LGD_CARDACQUIRER : 신용카드매입사코드
				LGD_PCANCELFLAG : 신용카드부분취소가능여부 : 0: 부분취소불가능,  1: 부분취소가능
				LGD_FINANCEAUTHNUM : 결제기관승인번호
				LGD_VANCODE : 밴사 코드
				LGD_CARDNUM : 카드번호
				LGD_ISPKEY : ISP 키, ISP만 제공됨
				LGD_AFFILIATECODE : 신용카드제휴코드, ISP만 제공됨

			3. 실시간 계좌이체 결제 결과 데이터
				LGD_CASHRECEIPTNUM : 현금영수증 승인번호(현금영수증 건이 아니거나 실패인경우 "0")
				LGD_CASHRECEIPTSELFYN : 현금영수증자진발급제유무(Y: 자진발급제 적용, 그외 : 미적용)
				LGD_CASHRECEIPTKIND : 현금영수증 종류(0: 소득공제용 , 1: 지출증빙용)

			4. 가상계좌(무통장입금) 결제 결과 데이터
				LGD_ACCOUNTNUM : 입금할 계좌번호
				LGD_CASTAMOUNT : 입금누적금액
				LGD_CASCAMOUNT : 현입금금액
				LGD_CASFLAG : 거래종류(R:할당,I:입금,C:취소)
				LGD_CASSEQNO : 가상계좌일련번호
				LGD_PAYER : 가상계좌 입금자명
			5. 에스크로 적용여부
				LGD_ESCROWYN : 에스크로 적용여부(Y,N)
		*/

		$pgCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_pg_uplus WHERE LGD_OID='".$V['LGD_OID']."'");
		if($pgCount == 0) {
			$wpdb->query("INSERT INTO bbse_commerce_pg_uplus SET LGD_RESPCODE='".$V['LGD_RESPCODE']."', LGD_PAYTYPE='".$V['LGD_PAYTYPE']."', LGD_PAYNOW_TRANTYPE='".$V['LGD_PAYNOW_TRANTYPE']."', LGD_RESPMSG='".$V['LGD_RESPMSG']."', LGD_PAYKEY='".$V['LGD_PAYKEY']."', LGD_TID='".$V['LGD_TID']."', LGD_HASHDATA='".$V['LGD_HASHDATA']."', LGD_PAYDATE='".$V['LGD_PAYDATE']."', LGD_MID='".$V['LGD_MID']."|||".$V['LGD_PAYNOW_TRANTYPE']."', LGD_OID='".$V['LGD_OID']."', LGD_PRODUCTINFO='".$V['LGD_PRODUCTINFO']."', LGD_AMOUNT='".$V['LGD_AMOUNT']."', LGD_BUYER='".$V['LGD_BUYER']."', LGD_BUYERID='".$V['LGD_BUYERID']."', LGD_BUYERIP='".$V['LGD_BUYERIP']."', LGD_BUYERPHONE='".$V['LGD_BUYERPHONE']."', LGD_BUYEREMAIL='".$V['LGD_BUYEREMAIL']."', LGD_TRANSAMOUNT='".$V['LGD_TRANSAMOUNT']."', LGD_FINANCECODE='".$V['LGD_FINANCECODE']."', LGD_FINANCENAME='".$V['LGD_FINANCENAME']."', LGD_CARDACQUIRER='".$V['LGD_CARDACQUIRER']."', LGD_PCANCELFLAG='".$V['LGD_PCANCELFLAG']."', LGD_FINANCEAUTHNUM='".$V['LGD_FINANCEAUTHNUM']."', LGD_VANCODE='".$V['LGD_VANCODE']."', LGD_CARDNUM='".$V['LGD_CARDNUM']."', LGD_ISPKEY='".$V['LGD_ISPKEY']."', LGD_AFFILIATECODE='".$V['LGD_AFFILIATECODE']."', LGD_CASHRECEIPTNUM='".$V['LGD_CASHRECEIPTNUM']."', LGD_CASHRECEIPTSELFYN='".$V['LGD_CASHRECEIPTSELFYN']."', LGD_CASHRECEIPTKIND='".$V['LGD_CASHRECEIPTKIND']."', LGD_ACCOUNTNUM='".$V['LGD_ACCOUNTNUM']."', LGD_CASTAMOUNT='".$V['LGD_CASTAMOUNT']."', LGD_CASCAMOUNT='".$V['LGD_CASCAMOUNT']."', LGD_CASFLAG='".$V['LGD_CASFLAG']."', LGD_CASSEQNO='".$V['LGD_CASSEQNO']."', LGD_CLOSEDATE='".$V['LGD_CLOSEDATE']."', LGD_PAYER='".$V['LGD_PAYER']."', LGD_ESCROWYN='".$V['LGD_ESCROWYN']."'");
		}

		if($V['LGD_RESPCODE']=='0000'){ // 결제성공
			if($payHow=='EPN'){ // paynow로 결제한 경우 : LGD_PAYNOW_TRANTYPE=1이 return 됨
				if($V['LGD_PAYTYPE']=='SC0010') $payHow='C';
				elseif($V['LGD_PAYTYPE']=='SC0030') $payHow='K';
				elseif($V['LGD_PAYTYPE']=='SC0040') $payHow='V';
				else $payHow='C';

				$ezpayUse='EPN';
			}
			else{
				$ezpayUse='';
			}

			if($V['LGD_PAYTYPE'] != "SC0040") { // 가상계좌가 아닌 경우
				$order_status="PE";
				$input_date = current_time('timestamp');
				$sendType = "order-input";
				$input_name=$V['LGD_BUYER'];
			}else{ // 가상계좌 인 경우
				$order_status="PR";
				$sendType = "order-ready";
				$input_name=$V['LGD_PAYER'];
			}

			if($V['Column2'] == "mobile" || $V['Column2'] == "tablet") {
				$orderData = $wpdb->get_var("SELECT goods_option_basic FROM bbse_commerce_cart WHERE goods_idx='0' AND user_id='".$V['Column3']."'");
				$V = unserialize(base64_decode($orderData));
			}else{
				parse_str($V['orderList'], $V);
			}
		}
		else{
			echo "<script>alert('결제 처리중 오류가 발생하였습니다.\\n(".$V['LGD_RESPCODE']." : ".$V['LGD_RESPMSG'].")\\n\\n고객센터로 문의해주세요.');location.href='".home_url()."';</script>";
			exit;
		}
	}
	elseif($payHow=='EKA'){ // KakaoPay 결제완료
		$pgCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_pg_kakaopay WHERE order_no='".$V['moid']."'");
		if($pgCount == 0) {
			$wpdb->query("INSERT INTO bbse_commerce_pg_kakaopay 
				SET resultCode='".$V['resultCode']."', resultMsg='".$V['resultMsg']."', 
				order_no='".$V['moid']."', authDate='".$V['authDate']."', authCode='".$V['authCode']."', payMethod='".$V['payMethod']."', mid='".$V['mid']."', goodsName='".$V['goodsName']."', buyerName='".$V['buyerName']."', amt='".$V['amt']."', tid='".$V['tid']."', moid='".$V['moid']."', cardName='".$V['cardName']."', cardQuota='".$V['cardQuota']."', cardCode='".$V['cardCode']."', cardInterest='".$V['cardInterest']."', cardCl='".$V['cardCl']."', cardBin='".$V['cardBin']."', cardPoint='".$V['cardPoint']."', nonRepToken='".$V['nonRepToken']."'");
		}

		if($V['resultCode']=='3001'){ // 결제성공
				$payHow='C';
				$ezpayUse='EKA';

				$order_status="PE";
				$input_date = current_time('timestamp');
				$sendType = "order-input";
				$input_name=$V['buyerName'];
				
				parse_str($V['orderList'], $V);
		}
		else{
			echo "<script>alert('결제 처리중 오류가 발생하였습니다.\\n(".$V['LGD_RESPCODE']." : ".$V['LGD_RESPMSG'].")\\n\\n고객센터로 문의해주세요.');location.href='".home_url()."';</script>";
			exit;
		}
	}


}

$order_device = $deviceType;
$pay_how=$payHow;
$ezpay_how=$ezpayUse;

$order_no=$V['order_no'];
$ordCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_order WHERE order_no='".$order_no."'");

if($ordCount > 0){
	$ordData=$wpdb->get_row("SELECT idx FROM bbse_commerce_order WHERE order_no='".$order_no."'");

	$result = base64_encode($order_no."|||".$ordData->idx);
	$parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
	if($parseurl['scheme'] == "https") {
		$okurl = "http://".$parseurl['host']."/?bbsePage=order-ok&result=".$result;
	}else{
		$okurl = home_url()."/?bbsePage=order-ok&result=".$result;
	}

	wp_redirect($okurl);
}
elseif($order_no && $pay_how){
	$add_earn=$V['add_earn'];
	$order_status_pre="";
	$user_id=$current_user->user_login;

	$sns_id="";
	$sns_idx="";

	if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
		if($_SESSION['snsLoginData']){
			$snsLoginData=unserialize($_SESSION['snsLoginData']);
			$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
			if($snsData->idx){
				$sns_id=$snsLoginData['sns_id'];
				$sns_idx=$snsData->idx;

				$wpdb->query("UPDATE bbse_commerce_social_login SET sns_name='".$snsLoginData['sns_name']."', sns_email='".$snsLoginData['sns_email']."', sns_gender='".$snsLoginData['sns_gender']."' WHERE idx='".$sns_idx."'");
			}
		}
	}

	$order_name=htmlspecialchars($V['order_name']);
	$order_zip=$V['order_zip'];
	$order_addr1=htmlspecialchars($V['order_addr1']);
	$order_addr2=htmlspecialchars($V['order_addr2']);
	$order_phone=htmlspecialchars($V['order_phone1'])."-".htmlspecialchars($V['order_phone2'])."-".htmlspecialchars($V['order_phone3']);
	$order_hp=htmlspecialchars($V['order_hp1'])."-".htmlspecialchars($V['order_hp2'])."-".htmlspecialchars($V['order_hp3']);
	$order_email=htmlspecialchars($V['order_email']);
	$receive_name=htmlspecialchars($V['receive_name']);
	$receive_zip=$V['receive_zip'];
	$receive_addr1=htmlspecialchars($V['receive_addr1']);
	$receive_addr2=htmlspecialchars($V['receive_addr2']);
	$receive_phone=htmlspecialchars($V['receive_phone1'])."-".htmlspecialchars($V['receive_phone2'])."-".htmlspecialchars($V['receive_phone3']);
	$receive_hp=htmlspecialchars($V['receive_hp1'])."-".htmlspecialchars($V['receive_hp2'])."-".htmlspecialchars($V['receive_hp3']);
	$order_comment=htmlspecialchars($V['order_comment']);
	$goods_total=$V['goods_option_price'] + $V['goods_add_price'];
	$order_config=bbse_commerce_get_delivery_info();    // 주문 당시의 배송비 설정 정보
	$delAddAddr=explode(" ",$receive_addr1);
	$delivery_add_addr=$delAddAddr['0']." ".$delAddAddr['1'];    // 주문 당시의 배송지
	$delivery_add=bbse_commerce_get_delivery_add($receive_addr1); // 주소에 따른 추가 배송비 계산
	$delivery_basic=$V['delivery_price'];

	$delivery_total=$delivery_basic+$delivery_add;
	$use_earn=(!$V['use_earn'])?0:$V['use_earn'];

	if($V['delivery_charge_payment'] == "advance") {
		$cost_total = $goods_total + $delivery_total - $use_earn;
	}else{
		$cost_total = $goods_total - $use_earn;
	}
	
	//쿠폰할인추가
	$cost_total -= intval($V['coupon_total']);
	//회원할인 추가
	$cost_total -= intval($V['user_discount']);
	
	if($orderCFG['total_pay_unit']) {
		if($orderCFG['total_pay_round'] == "down") {
			$cost_total = floor($cost_total / $orderCFG['total_pay_unit']) * $orderCFG['total_pay_unit']; //결제금액 절삭처리
		}else if($orderCFG['total_pay_round'] == "up") {
			$cost_total = ceil($cost_total / $orderCFG['total_pay_unit']) * $orderCFG['total_pay_unit']; //결제금액 올림처리
		}
	}

	$order_date=current_time('timestamp');

	$inQuery=$wpdb->prepare("INSERT INTO bbse_commerce_order (
						order_no, 
						pay_how, 
						ezpay_how,
						pay_info, 
						input_name, 
						add_earn, 
						order_device,
						order_status, 
						order_status_pre, 
						user_id, 
						sns_id, 
						sns_idx, 
						order_name, 
						order_zip, 
						order_addr1, 
						order_addr2, 
						order_phone, 
						order_hp, 
						order_email, 
						receive_name, 
						receive_zip, 
						receive_addr1, 
						receive_addr2, 
						receive_phone, 
						receive_hp, 
						order_comment, 
						goods_total, 
						order_config,
						delivery_add_addr,
						delivery_basic, 
						delivery_add, 
						delivery_total, 
						use_earn, 
						user_discount,
						coupon_discount,
						cost_total, 
						order_date,
						input_date,
						order_pass_num,
                        payMode,
                        pay_status
					) 
					VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s,%s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s)",
						$order_no,
						$pay_how,
						$ezpay_how,
						$pay_info,
						$input_name,
						$add_earn,
						$order_device,
						$order_status,
						$order_status_pre,
						$user_id,
						$sns_id,
						$sns_idx,
						$order_name,
						$order_zip,
						$order_addr1,
						$order_addr2,
						$order_phone,
						$order_hp,
						$order_email,
						$receive_name,
						$receive_zip,
						$receive_addr1,
						$receive_addr2,
						$receive_phone,
						$receive_hp,
						$order_comment,
						$goods_total,
						$order_config,
						$delivery_add_addr,
						$delivery_basic,
						$delivery_add,
						$delivery_total,
						$use_earn,
						$V['user_discount'],
						$V['coupon_total'],
						$cost_total,
						$order_date,
						$input_date,
						$V['order_pass_num'],
	                    $payMode,
	                    $pay_status
					 );
	$wpdb->query($inQuery);
	$idx = $wpdb->insert_id;
	//쿠폰 사용내역 추가
	
	$date = date('Y-m-d H:i:s');
	$date2 = new DateTime($date);
	$date2->setTimezone(new DateTimeZone('Asia/Seoul'));
	
	if(is_user_logged_in()) {
		$coupon = explode(',', $V['coupon']);
		$pcoupon = explode(',', $V['pcoupon']);
		if($coupon[0] != null && $coupon[0] != ""){
    		
    		foreach ($coupon as $key => $value) {
    			$wpdb->insert(
    				'bbse_commerce_coupon_log',
    				array(
    					'order_id'	=> $idx,
    					'coupon_id'	=> $value,
    					'user'		=> $user_id,
    					/*'date'		=> date('Y-m-d H:i:s') */
    				    'date' => $date2->format('Y-m-d H:i:s')
    				)
    			);	
    		}
		}
		if($pcoupon[0] != null && $pcoupon[0] != ""){
		    foreach ($pcoupon as $key => $value) {
		        $wpdb->query('
    				UPDATE bbse_commerce_paper_coupon
    				SET status = "used"
    				WHERE idx = "'.$value.'"
    			');
		    }
		}
	}
	if($V['orderType'] == "direct") {// 바로구매

		$optBasic = array();
		$optAdd = array();
		$goods_info = unserialize(base64_decode($V['goods_info']));
		$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$goods_info['goods_idx']."'");

		$goods_idx=$goods->idx;
		$goods_name=$goods->goods_name;
		$goods_unique_code=$goods->goods_unique_code;
		$goods_barcode=$goods->goods_barcode;
		$goods_location_no=$goods->goods_location_no;
		$goods_earn=$goods->goods_earn;
		$goods_price=$goods->goods_price;
		$goods_basic_img=$goods->goods_basic_img;

		if($goods_info['goods_basic_title'][0] == "단일상품") {
			$optBasic['goods_option_title'][]=$goods_info['goods_basic_title'][0];
			$optBasic['goods_option_overprice'][]="0";
			$optBasic['goods_option_count'][]=$goods_info['goods_basic_count'][0];
		}else{

			for($i=0;$i<count($goods_info['goods_basic_title']);$i++) {
				$goods_option_overprice = $wpdb->get_var("select goods_option_item_overprice from bbse_commerce_goods_option where goods_idx='".$goods_idx."' AND goods_option_title='".$goods_info['goods_basic_title'][$i]."'");
				$optBasic['goods_option_title'][]=$goods_info['goods_basic_title'][$i];
				$optBasic['goods_option_overprice'][]=$goods_option_overprice;
				$optBasic['goods_option_count'][]=$goods_info['goods_basic_count'][$i];
			}

		}

		for($i=0;$i<count($goods_info['goods_add_title']);$i++) {
			$optAdd['goods_add_title'][]=$goods_info['goods_add_title'][$i];
			$optAdd['goods_add_overprice'][]=$goods_info['goods_add_price'][$i];
			$optAdd['goods_add_count'][]=$goods_info['goods_add_count'][$i];
		}

		$goods_option_basic=serialize($optBasic);
		$goods_option_add=serialize($optAdd);
		$goods_basic_total=$V['goods_option_price'];
		$goods_add_total=$V['goods_add_price'];

		$inQuery2=$wpdb->prepare("INSERT INTO bbse_commerce_order_detail (
							order_no,
							goods_idx,
							goods_name,
							goods_unique_code,
							goods_barcode,
							goods_location_no,
							goods_earn,
							goods_price,
							goods_basic_img,
							goods_option_basic,
							goods_option_add,
							goods_basic_total,
							goods_add_total,
							order_pass_num
						) 
						VALUES (
							%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
						)",
							$order_no,
							$goods_idx,
							$goods_name,
							$goods_unique_code,
							$goods_barcode,
							$goods_location_no,
							$goods_earn,
							$goods_price,
							$goods_basic_img,
							$goods_option_basic,
							$goods_option_add,
							$goods_basic_total,
							$goods_add_total,
							$V['order_pass_num']
						 );
		$wpdb->query($inQuery2);
			

	}else{// 장바구니 구매


		$gidx = unserialize(base64_decode($V['gidx']));

		$results = $wpdb->get_results("SELECT * FROM bbse_commerce_cart WHERE idx IN (".implode(",",$gidx).")");

		$optBasic = array();
		$optAdd = array();
		foreach($results as $cart) {

			$goods_basic_total = 0;
			$goods_add_total = 0;
			$goods_basic_count = 0;
			$goods_add_count = 0;
			unset($optBasic);
			unset($optAdd);

			$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$cart->goods_idx."'");

			$goods_idx=$goods->idx;
			$goods_name=$goods->goods_name;
			$goods_unique_code=$goods->goods_unique_code;
			$goods_barcode=$goods->goods_barcode;
			$goods_location_no=$goods->goods_location_no;
			$goods_earn=$goods->goods_earn;
			$goods_price=$goods->goods_price;
			$goods_basic_img=$goods->goods_basic_img;

			$option_basic = unserialize($cart->goods_option_basic);
			$option_add = unserialize($cart->goods_option_add);

			if($option_basic['goods_option_title'][0] == "단일상품") {
				$optBasic['goods_option_title'][]=$option_basic['goods_option_title'][0];
				$optBasic['goods_option_overprice'][]=0;
				$optBasic['goods_option_count'][]=$option_basic['goods_option_count'][0];
				$goods_basic_total = $goods_price*$option_basic['goods_option_count'][0];
			}else{
				for($i=0;$i<count($option_basic['goods_option_title']);$i++) {
					$goods_option_overprice = $wpdb->get_var("SELECT goods_option_item_overprice FROM bbse_commerce_goods_option WHERE goods_idx='".$goods_idx."' AND goods_option_title='".$option_basic['goods_option_title'][$i]."'");
					$optBasic['goods_option_title'][]=$option_basic['goods_option_title'][$i];
					$optBasic['goods_option_overprice'][]=$goods_option_overprice;
					$optBasic['goods_option_count'][]=$option_basic['goods_option_count'][$i];
					$goods_basic_total += ( ($goods_price + $goods_option_overprice) * $option_basic['goods_option_count'][$i] );
					$goods_basic_count += $option_basic['goods_option_count'][$i];
				}
			}

			$goodsOptionAdd = unserialize($goods->goods_option_add);
			for($i=0;$i<count($option_add['goods_add_title']);$i++) {
				$optAdd['goods_add_title'][]=$option_add['goods_add_title'][$i];
				$optAdd['goods_add_count'][]=$option_add['goods_add_count'][$i];

				for($j=1;$j<=$goodsOptionAdd['goods_add_option_count'];$j++) {
					$add_price = 0;
					for($k=0;$k<count($goodsOptionAdd['goods_add_'.$j.'_item']);$k++) {
						if($goodsOptionAdd['goods_add_'.$j.'_item'][$k]==$option_add['goods_add_title'][$i]) {
							$add_price = $goodsOptionAdd['goods_add_'.$j.'_item_overprice'][$k];
							break;
						}
					}
					if($add_price > 0) break;
				}
				$optAdd['goods_add_overprice'][]=$add_price;
				$goods_add_total += ($add_price * $option_add['goods_add_count'][$i]);
				$goods_add_count += $option_add['goods_add_count'][$i];
			}

			$goods_option_basic=serialize($optBasic);
			$goods_option_add=serialize($optAdd);

			$inQuery2=$wpdb->prepare("INSERT INTO bbse_commerce_order_detail (
								order_no,
								goods_idx,
								goods_name,
								goods_unique_code,
								goods_barcode,
								goods_location_no,
								goods_earn,
								goods_price,
								goods_basic_img,
								goods_option_basic,
								goods_option_add,
								goods_basic_total,
								goods_add_total,
								order_pass_num
							) 
							VALUES (
								%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
							)",
								$order_no,
								$goods_idx,
								$goods_name,
								$goods_unique_code,
								$goods_barcode,
								$goods_location_no,
								$goods_earn,
								$goods_price,
								$goods_basic_img,
								$goods_option_basic,
								$goods_option_add,
								$goods_basic_total,
								$goods_add_total,
								$V['order_pass_num']
							 );
			$wpdb->query($inQuery2);
			$wpdb->query("DELETE FROM bbse_commerce_cart WHERE idx IN (".implode(",",$gidx).")");

			if($V['Column2'] == "mobile" || $V['Column2'] == "tablet") {
				$wpdb->query("DELETE FROM bbse_commerce_cart WHERE user_id='".$user_id."' AND goods_idx='".$goods_idx."'");
			}
		}
	}

	$result = base64_encode($order_no."|||".$idx);

	if($user_id != "" && $use_earn > 0) {
		bbse_commerce_mileage_insert('OUT', 'order', $use_earn, $order_no); //적립금 차감 처리
	}
	bbse_commerce_goods_stock_minus($order_no); //재고 처리

	if($sendType) bbse_commerce_mail_send($sendType, $order_no, '');// 메일발송
	if($sendType) bbse_commerce_sms_send($sendType, $order_no);// SMS 발송

	$parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
	if($parseurl['scheme'] == "https") {
		$okurl = "http://".$parseurl['host']."/?bbsePage=order-ok&result=".$result;
	}else{
		$okurl = home_url()."/?bbsePage=order-ok&result=".$result;
	}
	
	if($ezpayUse == 'EKA'){
		echo '
			<script>
				var childWindow = window.parent;
			    var parentWindow = childWindow.opener;
			    parentWindow.parent.location.replace("'.$okurl.'");
			    childWindow.close();
			</script>
		';
		exit;
	}
	else{
		wp_redirect($okurl);
	}
}
else{
	$okurl = home_url();
	wp_redirect($okurl);
	exit;
}
?>
<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

 /***************************************************************************************************************
 * 올더게이트로부터 가상계좌 입/출금 데이타를 받아서 상점에서 처리 한 후 
 * 올더게이트로 다시 응답값을 리턴하는 페이지입니다.
 * 상점 DB처리 부분을 업체에 맞게 수정하여 작업하시기 바랍니다.
***************************************************************************************************************/

/*********************************** 올더게이트로 부터 넘겨 받는 값들 시작 *************************************/
$trcode = iconv("euc-kr", "utf-8", trim($_POST["trcode"]));					    //거래코드
$service_id = iconv("euc-kr", "utf-8", trim($_POST["service_id"]));					//상점아이디
$orderdt = iconv("euc-kr", "utf-8", trim($_POST["orderdt"]));				    //승인일자
$virno = iconv("euc-kr", "utf-8", trim($_POST["virno"]));				        //가상계좌번호
$deal_won = iconv("euc-kr", "utf-8", trim($_POST["deal_won"]));					//입금액
$ordno = iconv("euc-kr", "utf-8", trim($_POST["ordno"]));                      //주문번호
$inputnm = iconv("euc-kr", "utf-8", trim($_POST["inputnm"]));					//입금자명

/*********************************** 올더게이트로 부터 넘겨 받는 값들 끝 *************************************/

/***************************************************************************************************************
 * 상점에서 해당 거래에 대한 처리 db 처리 등....
 *
 * trcode = "1" ☞ 일반가상계좌 입금통보전문
 * trcode = "2" ☞ 일반가상계좌 취소통보전문
 *
***************************************************************************************************************/
$rSuccYn = "n";
$pgLog = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_agspay WHERE rOrdNo='".$ordno."' AND rVirNo='".$virno."' AND rStoreId='".$service_id."' AND rApprTm='".$orderdt."' AND rAmt='".$deal_won."'");
if($pgLog->no > 0) {

	if($trcode == "1") {// 일반가상계좌 입금통보전문
		$input_date = current_time('timestamp');
		$wpdb->query("UPDATE bbse_commerce_order SET order_status='PE', input_name='".$inputnm."', input_date='".$input_date."' WHERE order_no='".$ordno."'");
		bbse_commerce_goods_stock_minus($ordno); //재고 처리
		bbse_commerce_mail_send("order-input", $ordno, '');// 메일발송
		bbse_commerce_sms_send("order-input", $ordno);// SMS 발송
		$rSuccYn = "y";
	}else if($trcode = "2") {// 일반가상계좌 취소통보전문

	}

}


/******************************************처리 결과 리턴******************************************************/
$rResMsg  = "";
//$rSuccYn  = "y";// 정상 : y 실패 : n

//정상처리 경우 거래코드|상점아이디|주문일시|가상계좌번호|처리결과|
$rResMsg .= $trcode."|";
$rResMsg .= $service_id."|";
$rResMsg .= $orderdt."|";
$rResMsg .= $virno."|";
$rResMsg .= $rSuccYn."|";

echo $rResMsg;
/******************************************처리 결과 리턴******************************************************/
?> 
<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $current_user,$theme_shortname;

$V = $_POST;
wp_get_current_user();

if(strpos(wp_get_referer(), $_SERVER['HTTP_HOST']) == false) {echo "BadAccess";exit;}

if($V['tMode'] == "receive_modify") {// 상품 받으시는곳 정보 변경

	if(!$V['order_no']) {echo "DataError";exit;}

	if(is_user_logged_in()) {
		$statusChk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM bbse_commerce_order WHERE order_no=%s AND order_status IN ('PR', 'PE', 'DR') AND user_id=%s", $V['order_no'], $current_user->user_login));
	}else{
		$statusChk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM bbse_commerce_order WHERE order_no=%s AND order_status IN ('PR', 'PE', 'DR')", $V['order_no']));
	}
	if($statusChk == 0) {echo "BadAccess";exit;}

	if(!$V['receive_name'] || !$V['receive_zip'] || !$V['receive_addr1'] || !$V['receive_addr2'] || !$V['receive_hp1'] || !$V['receive_hp2'] || !$V['receive_hp3']) {echo "DataError";exit;}

	$receive_zip = $V['receive_zip'];
	$receive_phone = $V['receive_phone1']."-".$V['receive_phone2']."-".$V['receive_phone3'];
	$receive_hp = $V['receive_hp1']."-".$V['receive_hp2']."-".$V['receive_hp3'];

	/* 배송정보 변경시 처리 S */
	$changeTime=current_time('timestamp');
	$oData=$wpdb->get_row("SELECT receive_addr1, delivery_add_addr FROM bbse_commerce_order WHERE order_no='".$V['order_no']."'");

	$oldReceiveAddr1=explode(" ",$oData->receive_addr1);
	$oAddr=trim($oldReceiveAddr1['0'])." ".trim($oldReceiveAddr1['1']);

	$newReceiveAddr1=explode(" ",$V['receive_addr1']);
	$nAddr=trim($newReceiveAddr1['0'])." ".trim($newReceiveAddr1['1']);
	if($oAddr!=$nAddr){
		if($nAddr==$oData->delivery_add_addr){
			$deliveryAddChange="";
			$deliveryAddChangeDate="";
			$changeConfig="";
		}
		else{
			$deliveryAddChange=bbse_commerce_get_delivery_add($V['receive_addr1']);
			$deliveryAddChangeDate=current_time('timestamp');
			$changeConfig=bbse_commerce_get_delivery_info();
		}
		$wpdb->query("UPDATE bbse_commerce_order SET change_config='".$changeConfig."',delivery_add_change='".$deliveryAddChange."',delivery_add_change_date='".$deliveryAddChangeDate."' WHERE order_no='".$V['order_no']."'");
	}
	/* 배송정보 변경시 처리 E */

	$sql = $wpdb->prepare(
		"UPDATE bbse_commerce_order SET 
			receive_name=%s,
			receive_zip=%s,
			receive_addr1=%s,
			receive_addr2=%s,
			receive_phone=%s,
			receive_hp=%s,
			order_comment=%s
		WHERE order_no=%s",
			htmlspecialchars($V['receive_name']), 
			$receive_zip,
			htmlspecialchars($V['receive_addr1']),
			htmlspecialchars($V['receive_addr2']),
			htmlspecialchars($receive_phone),
			htmlspecialchars($receive_hp),
			htmlspecialchars($V['order_comment']),
			$V['order_no']
		);
	$wpdb->query($sql);

	echo "success";
	exit;

}else if($V['tMode'] == "order_cancel") {// 주문취소 처리

	if(!is_user_logged_in()) {
		if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
			if(!$V['order_no']) {echo "error|||잘못된 접근입니다.";exit;}
		}
		else{
			if(!$V['order_no'] || !$V['order_name']) {echo "error|||잘못된 접근입니다.";exit;}
		}
	}else{
		if(!$V['order_no']) {echo "error|||잘못된 접근입니다.";exit;}
	}

	$order = $wpdb->get_row("SELECT order_no, order_status FROM bbse_commerce_order WHERE order_no='".$V['order_no']."'");
	parse_str($V['cancel_info'], $cancel_info);

	$refund_apply_date=$refund_end_date=current_time('timestamp');

	if($order->order_status == "PR") {// 상태가 입금대기일때
		$change_status = "CE";
		$refund_bank_info = "";
		//if($cancel_info['refund_reason'] == "") {echo "error|||취소사유를 입력해주세요.";exit;}
	}else{
		$change_status = "CA";
		//if($cancel_info['refund_bank_name'] == "") {echo "error|||은행명을 입력해주세요.";exit;}
		//if($cancel_info['refund_bank_no'] == "") {echo "error|||계좌번호를 입력해주세요.";exit;}
		//if($cancel_info['refund_bank_owner'] == "") {echo "error|||예금주를 입력해주세요.";exit;}
		//if($cancel_info['refund_reason'] == "") {echo "error|||취소사유를 입력해주세요.";exit;}
		$refund_bank_info = $cancel_info['refund_bank_name']."|||".$cancel_info['refund_bank_no']."|||".$cancel_info['refund_bank_owner'];
	}

	if($order->order_status == "CA" || $order->order_status == "CE") {
		echo "not";
		exit;
	}
	$wpdb->query("UPDATE bbse_commerce_order SET order_status='".$change_status."', order_status_pre='".$order->order_status."', refund_bank_info='".$refund_bank_info."', refund_reason='".$cancel_info['refund_reason']."', refund_apply_date='".$refund_apply_date."', refund_end_date='".$refund_end_date."' WHERE order_no='".$V['order_no']."'");
	if($order->order_status == "PR" && $change_status == "CE") {
		bbse_commerce_order_earn_check($order->order_no); // 적립금 환불
		bbse_commerce_mail_send("order-cancel", $order->order_no, '');// 취소완료 메일발송
	}
	echo "success";
	exit;


}else if($V['tMode'] == "order_refund") {// 반품신청 처리

	if(!is_user_logged_in()) {
		if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
			if(!$V['order_no']) {echo "error|||잘못된 접근입니다.";exit;}
		}
		else{
			if(!$V['order_no'] || !$V['order_name']) {echo "error|||잘못된 접근입니다.";exit;}
		}
	}else{
		if(!$V['order_no']) {echo "error|||잘못된 접근입니다.";exit;}
	}

	$order = $wpdb->get_row("SELECT order_status FROM bbse_commerce_order WHERE order_no='".$V['order_no']."'");
	parse_str($V['cancel_info'], $cancel_info);

	$change_status = "RA";
	//if($cancel_info['refund_bank_name'] == "") {echo "error|||은행명을 입력해주세요.";exit;}
	//if($cancel_info['refund_bank_no'] == "") {echo "error|||계좌번호를 입력해주세요.";exit;}
	//if($cancel_info['refund_bank_owner'] == "") {echo "error|||예금주를 입력해주세요.";exit;}
	//if($cancel_info['refund_reason'] == "") {echo "error|||반품사유를 입력해주세요.";exit;}
	$refund_bank_info = $cancel_info['refund_bank_name']."|||".$cancel_info['refund_bank_no']."|||".$cancel_info['refund_bank_owner'];

	if($order->order_status == "RA" || $order->order_status == "RE") {
		echo "not";
		exit;
	}
	$refund_apply_date = current_time('timestamp');
	$wpdb->query("UPDATE bbse_commerce_order SET order_status='".$change_status."', order_status_pre='".$order->order_status."', refund_bank_info='".$refund_bank_info."', refund_reason='".$cancel_info['refund_reason']."', refund_apply_date='".$refund_apply_date."' WHERE order_no='".$V['order_no']."'");

	echo "success";
	exit;


}else if($V['tMode'] == "order_confirm") {// 구매확정 처리

	if(!is_user_logged_in()) {
		if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
			if(!$V['order_no']) {echo "error|||잘못된 접근입니다.";exit;}
		}
		else{
			if(!$V['order_no'] || !$V['order_name']) {echo "error|||잘못된 접근입니다.";exit;}
		}
	}else{
		if(!$V['order_no']) {echo "error|||잘못된 접근입니다.";exit;}
	}

	$order = $wpdb->get_row("SELECT order_status, add_earn FROM bbse_commerce_order WHERE order_no='".$V['order_no']."'");
	parse_str($V['cancel_info'], $cancel_info);

	$change_status = "OE";
	if($order->order_status == "OE") {
		echo "not";
		exit;
	}
	$order_end_date = current_time('timestamp');
	$wpdb->query("UPDATE bbse_commerce_order SET order_status='".$change_status."', order_status_pre='".$order->order_status."', order_end_date='".$order_end_date."' WHERE order_no='".$V['order_no']."'");
	bbse_commerce_order_earn_check($V['order_no']); //적립금 차감 처리
	bbse_commerce_auto_upgrade_user($V['order_no']);
	
	echo "success";
	exit;


}
?>
<?php
/**
 * 2014.12.02 : 인증요청 송신 전문 외 항목 제거
 */
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";


	$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='ezpay'");
	$payCFG = unserialize($paymentConfig);
	
	$admin_key 	= $payCFG['kakaopay']['kakaopay_auth_enckey'];
	$secret		= $payCFG['kakaopay']['kakaopay_mert_key'];
	$CID		= $payCFG['kakaopay']['kakaopay_mert_id'];
	
	$ready_url = 'https://kapi.kakao.com/v1/payment/ready';
	
	$data = $_POST;
	//print_r($data['order_id']);
	$response = wp_remote_post( $ready_url, array(
		'method' 	=> 'POST',
		'headers' 	=> array(
			'Authorization'=> 'KakaoAK '.$admin_key,
		),
		'body' 		=> array(
			'cid'					=> $CID, 
			//'cid_secret'			=> $secret,
			'partner_order_id'		=>  $data['order_id'],
			'partner_user_id'		=> get_current_user_id(),
			'item_name'				=> $data['GoodsName'],
			
			//'item_code'				=> '',
			'quantity'				=> $data['GoodsCnt'],
			'total_amount'			=> $data['Amt'],
			'tax_free_amount'		=> '0',
			//'vat_amount'			=> '',
			
			'approval_url'			=> BBSE_COMMERCE_THEME_WEB_URL.'/payment/KakaoPay/KakaoPay_pay_ing.php',
			'cancel_url'			=> BBSE_COMMERCE_THEME_WEB_URL.'/payment/KakaoPay/KakaoPay_pay_ing.php',
			'fail_url'				=> BBSE_COMMERCE_THEME_WEB_URL.'/payment/KakaoPay/KakaoPay_pay_ing.php',
			//'available_cards'		=> '',
			//'payment_method_type'	=> '',
			
			//'install_month'			=> '',
			//'custom_json'			=> '',
		),
	    )
	);
	$return 		= json_decode($response['body'],TRUE);
	$redirect_url 	= $return['next_redirect_pc_url'];
	if(wp_is_mobile()){
		$redirect_url = $return['next_redirect_mobile_url'];
	}
	$TID			= $return['tid'];
	
	setcookie("kakao_tid", $TID);
    setcookie("kakao_order_id", $data['order_id']);
	setcookie("kakao_order_list", $data['orderList']);
	
	if(!empty($TID)){
		echo "success|||".$redirect_url."|||".$TID."|||".$data['order_id'];
	}
	else{
		echo "fail";
	}
?>
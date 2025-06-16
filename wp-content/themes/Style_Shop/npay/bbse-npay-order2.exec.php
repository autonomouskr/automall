<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $current_user,$theme_shortname,$wpdb;

wp_get_current_user();

$V = $_GET;
$remoteAddr=explode(".",$_SERVER['REMOTE_ADDR']);
$orderConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order'");
$orderCFG = unserialize($orderConfig);

//print_r($_GET);
$result = array($_GET['resultCode'],$_GET['paymentId'],$_GET['reserveId'],$_GET['resultMessage']);
if($result[0] == 'Fail'){
	if($result[3] == 'userCancel'){
		echo'
			<script>
				alert("취소하셨습니다.");
				history.go(-1);
			</script>
		';
	}
	else if($result[3] == 'webhookFail'){
		echo'
			<script>
				alert("호출응답실패(관리자에게 문의하세요.)");
				history.go(-1);
			</script>
		';
	}
	else if($result[3] == 'paymentTimeExpire'){
		echo'
			<script>
				alert("결제 시간이 초과하였습니다. 다시 시도해주세요.");
				history.go(-1);
			</script>
		';
	}
	else if($result[3] == 'OwnerAuthFail'){
		echo'
			<script>
				alert("네이버 로그인 계정과 동일한 명의자의 카드를 사용해야 합니다.");
				history.go(-1);
			</script>
		';
	}
	else{
		echo'
			<script>
				alert("'.$result[3].'");
				history.go(-1);
			</script>
		';
	}
}
else if($result[0] == 'Success'){
	//주문 생성하고 결제 승인 요청
	//실결제 주소는 https://apis.naver.com/naverpay-partner/naverpay/payments/v2.2/apply/payment
	$nPayData=bbse_nPay_check();
	$url = 'https://dev.apis.naver.com/naverpay-partner/naverpay/payments/v2.2/apply/payment';
	$response = wp_remote_post( $url, array(
		'method' 	=> 'POST',
		'headers' 	=> array(
			'X-Naver-Client-Id'=> $nPayData['naver_pay_id'],
			'X-Naver-Client-Secret'=> $nPayData['naver_pay_auth_key'],
		),
		'body' 		=> array(
			'paymentId'						=> $result[1],
		),
	    )
	);
	$p_result = json_decode($response['body'],TRUE);
	if(empty($p_result['error_code'])){
		echo'
			<script>
				alert("'.$p_result['message'].'");
				history.go(-1);
			</script>
		';
	}
	else if($response['code'] == 'Success'){
		//주문성공시 주문생성
		$order_device = 'desktop';
		if(wp_is_mobile()){
			$order_device = 'mobile';
		}
		$pay_how='C';
		if($p_result[detail][primaryPayMeans] == 'BANK'){
			$pay_how = "B";
		}
		$ezpay_how	= 'NPAY';
		$input_name	= $V['order_name'];
		$pay_info	= json_encode($p_result[detail],JSON_UNESCAPED_UNICODE);
		
		$order_status="PE";
		$input_date = current_time('timestamp');
		$sendType = "order-input";
		
		$order_no=$V['order_no'];
		$ordCount = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_order WHERE order_no='".$order_no."'");
		//print_r($ordCount);
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
								cost_total, 
								order_date,
								input_date,
                                payMode
							) 
							VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s,%s, %s, %s, %s, %s, %s,%s, %s)",
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
								$cost_total,
								$order_date,
								$input_date,
			                    $payMode,
							 );
			$wpdb->query($inQuery);
			$idx = $wpdb->insert_id;
		
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
									goods_add_total
								) 
								VALUES (
									%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
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
									$goods_add_total
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
										goods_add_total
									) 
									VALUES (
										%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s
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
										$goods_add_total
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
			wp_redirect($okurl);
		}
		else{
			$okurl = home_url();
			wp_redirect($okurl);
			exit;
		}
	}
}
else{
	echo'
		<script>
			alert("잘못된 접근입니다.");
			location.href="'.home_url().'";
		</script>
	';
}
?>
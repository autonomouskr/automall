<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $theme_shortname;

class bbse_nPay_ItemStack {
	var $id;
	var $name;
	var $tprice;
	var $uprice;
	var $option;
	var $count;
	//option이 여러 종류라면, 선택된 옵션을 슬래시(/)로 구분해서 표시하는 것을 권장한다.
	function bbse_nPay_ItemStack($_id, $_name, $_tprice, $_uprice, $_option, $_count) {
		$this->id = $_id;
		$this->name = $_name;
		$this->tprice = $_tprice;
		$this->uprice = $_uprice;
		$this->option = $_option;
		$this->count = $_count;
	}
	function makeQueryString() {
		$ret .= 'ITEM_ID=' . urlencode($this->id);
		$ret .= '&ITEM_NAME=' . urlencode($this->name);
		$ret .= '&ITEM_COUNT=' . $this->count;
		$ret .= '&ITEM_OPTION=' . urlencode($this->option);
		$ret .= '&ITEM_TPRICE=' . $this->tprice;
		$ret .= '&ITEM_UPRICE=' . $this->uprice;
		return $ret;
	}
};

if (!function_exists('bbse_nPay_shipping_data')) {
	function bbse_nPay_shipping_data($tPrice){
		global $wpdb;
		$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='delivery'");

		if($cnt>'0'){
			$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
			$data=unserialize($confData->config_data);

			if($data[delivery_charge_type]=='charge'){ // 유료
				if($data['delivery_charge_payment']=='deferred') $rtnArray['shippingType']="ONDELIVERY"; // 후불결제
				else $rtnArray['shippingType']="PAYED";  // 선불결제

				if($data['condition_free_use']=='on'){ // 조건부 무료 배송
					if($data['delivery_feee_terms']=='goods_price' && $tPrice>=$data['total_pay']){
						$rtnArray['shippingType']="FREE";
						$rtnArray['shippingPrice']="0";
					}
					else $rtnArray['shippingPrice']=$data['delivery_charge'];
				}
				else $rtnArray['shippingPrice']=$data['delivery_charge']; // 배송비
			}
			else{
				$rtnArray['shippingType']="FREE";
				$rtnArray['shippingPrice']="0";
			}
		}
		else{
			$rtnArray['shippingType']="FREE";
			$rtnArray['shippingPrice']="0";
		}

		return $rtnArray;
	}
}

$V = $_POST;

$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사

if(!$nPayData['naver_pay_id']){
	echo "emptyNpayId";
	exit;
}
elseif(!$nPayData['naver_pay_auth_key']){
	echo "emptyNpayKey";
	exit;
}
elseif($nPayData['naver_pay_use']=='on'){  // [naver_pay_type] => test [naver_pay_id] => [naver_pay_auth_key] => [naver_pay_button_key] =>
	//item data를 생성한다.
	$shopId = $nPayData['naver_pay_id'];
	$certiKey = $nPayData['naver_pay_auth_key'];

	$rtnPar=($nPayData['naver_pay_type']=='test')?"&npayTest=true":"";

	if($V['order_type']=='cart' && sizeof($V['gidx'])>0) $backUrl = home_url()."/?bbsePage=cart".$rtnPar;
	else $backUrl = home_url()."/?bbseGoods=".$V['goods_idx'].$rtnPar;

	$queryString = 'SHOP_ID='.urlencode($shopId);
	$queryString .= '&CERTI_KEY='.urlencode($certiKey);
	$queryString .= '&RESERVE1=&RESERVE2=&RESERVE3=&RESERVE4=&RESERVE5=';
	$queryString .= '&BACK_URL='.$backUrl;
	$queryString .= '&SA_CLICK_ID='.$_COOKIE["NVADID"]; //CTS
	// CPA 스크립트 가이드 설치 업체는 해당 값 전달
	$queryString .= '&CPA_INFLOW_CODE='.urlencode($_COOKIE["CPAValidator"]);
	$queryString .= '&NAVER_INFLOW_CODE='.urlencode($_COOKIE["NA_CO"]);

	$totalMoney = 0;
	//DB와 장바구니에서 상품 정보를 얻어 온다.

	if($V['order_type']=='cart' && sizeof($V['gidx'])>0){ // 장바구니
		$cartIdx=implode(",",$V['gidx']);
		$result = $wpdb->get_results("SELECT C.idx AS cart_idx, C.user_id, C.sid, C.goods_option_basic AS cart_option_basic, C.goods_option_add AS cart_option_add, C.remote_ip, C.reg_date, G.* FROM bbse_commerce_cart AS C, bbse_commerce_goods AS G WHERE C.goods_idx=G.idx AND C.cart_kind='C' AND C.idx IN (".$cartIdx.") ORDER BY C.idx DESC");

		foreach($result as $goods) {
			$goodsId=$goods->goods_code;
			$goodsName=$goods->goods_name;
			$goodsPrice=$goods->goods_price;

			$optBasic=unserialize($goods->cart_option_basic);

			for($g=0;$g<count($optBasic['goods_option_title']);$g++){
				$goodsOpt = $wpdb->get_row("SELECT goods_option_item_overprice FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title='".$optBasic['goods_option_title'][$g]."'"); 

				$id = $goodsId;
				$name = $goodsName;
				$uprice = $goodsPrice+$goodsOpt->goods_option_item_overprice;
				$count = $optBasic['goods_option_count'][$g];

				$tprice = $uprice * $count;
				$option = $optBasic['goods_option_title'][$g];
				$item = new bbse_nPay_ItemStack($id, $name, $tprice, $uprice, $option, $count);
				$totalMoney += $tprice;
				$queryString .='&EC_MALL_PID='.$goodsId;
				$queryString .= '&'.$item->makeQueryString();
			}

			$option_add = $goods->cart_option_add?unserialize($goods->cart_option_add):"";
			$goods_option_add = $goods->goods_option_add?unserialize($goods->goods_option_add):"";

			for($i=0;$i<count($option_add['goods_add_title']);$i++) {
				for($j=1;$j<=$goods_option_add['goods_add_option_count'];$j++) {
					$add_price = 0;
					for($k=0;$k<count($goods_option_add['goods_add_'.$j.'_item']);$k++) {
						if($goods_option_add['goods_add_'.$j.'_item'][$k]==$option_add['goods_add_title'][$i]) {
							$add_price = $goods_option_add['goods_add_'.$j.'_item_overprice'][$k];
							break;
						}
					}
					if($add_price > 0) break;
				}

				$id = $goodsId;
				$name = $goodsName;
				$uprice = $add_price;
				$count = $option_add['goods_add_count'][$i];

				$tprice = $uprice * $count;
				$option = $option_add['goods_add_title'][$i];
				$item = new bbse_nPay_ItemStack($id, $name, $tprice, $uprice, $option, $count);
				$totalMoney += $tprice;
				$queryString .='&EC_MALL_PID='.$goodsId;
				$queryString .= '&'.$item->makeQueryString();
			}
		}
	}
	else if($V['goods_idx']>0){
		$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$V['goods_idx']."'"); 
		if($goods->goods_name && !goodsSoldoutCheck($goods)){
			$goodsId=$goods->goods_code;
			$goodsName=$goods->goods_name;
			$goodsPrice=$goods->goods_price;
		}
		else{
			echo "emptyGoods";
			exit;
		}

		for($g=0;$g<count($V['goods_basic_title']);$g++){
			$goodsOpt = $wpdb->get_row("SELECT goods_option_item_overprice FROM bbse_commerce_goods_option WHERE goods_idx='".$V['goods_idx']."' AND goods_option_title='".$V['goods_basic_title'][$g]."'"); 

			$id = $goodsId;
			$name = $goodsName;
			$uprice = $goodsPrice+$goodsOpt->goods_option_item_overprice;
			$count = $V['goods_basic_count'][$g];

			$tprice = $uprice * $count;
			$option = $V['goods_basic_title'][$g];
			$item = new bbse_nPay_ItemStack($id, $name, $tprice, $uprice, $option, $count);
			$totalMoney += $tprice;
			$queryString .='&EC_MALL_PID='.$goodsId;
			$queryString .= '&'.$item->makeQueryString();
		}

		for($a=0;$a<count($V['goods_add_title']);$a++){
			$id = $goodsId;
			$name = $goodsName;
			$uprice = $V['goods_add_price'][$a];
			$count = $V['goods_add_count'][$a];

			$tprice = $uprice * $count;
			$option = $V['goods_add_title'][$a];
			$item = new bbse_nPay_ItemStack($id, $name, $tprice, $uprice, $option, $count);
			$totalMoney += $tprice;
			$queryString .='&EC_MALL_PID='.$goodsId;
			$queryString .= '&'.$item->makeQueryString();
		}
	}
	else{
		echo "emptyGoods";
		exit;
	}

	$deliveryData=bbse_nPay_shipping_data($totalMoney);
	$shippingType = $deliveryData['shippingType'];
	$shippingPrice = $deliveryData['shippingPrice'];

	$queryString .= '&SHIPPING_TYPE='.$shippingType;
	$queryString .= '&SHIPPING_PRICE='.$shippingPrice;

	if($shippingType=='ONDELIVERY') $totalPrice = (int)$totalMoney;
	else $totalPrice = (int)$totalMoney + (int)$shippingPrice;

	$queryString .= '&TOTAL_PRICE='.$totalPrice;

	//echo($queryString."<br>\n");
	//exit;

	$req_addr = ($nPayData['naver_pay_type']=='test')?"ssl://test-pay.naver.com":"ssl://pay.naver.com";
	$req_url = 'POST /customer/api/order.nhn HTTP/1.1'; // utf-8
	$req_host = ($nPayData['naver_pay_type']=='test')?"test-pay.naver.com":"pay.naver.com";
	$req_port = 443;
	$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
	if ($nc_sock) {
		fwrite($nc_sock, $req_url."\r\n" );
		fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
		fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n");
		fwrite($nc_sock, "Content-length: ".strlen($queryString)."\r\n");
		fwrite($nc_sock, "Accept: */*\r\n");
		fwrite($nc_sock, "\r\n");
		fwrite($nc_sock, $queryString."\r\n");
		fwrite($nc_sock, "\r\n");
		// get header
		while(!feof($nc_sock)){
			$header=fgets($nc_sock,4096);
			if($header=="\r\n"){
				break;
			} else {
				$headers .= $header;
			}
		}
		// get body
		while(!feof($nc_sock)){
			$bodys.=fgets($nc_sock,4096);
		}
		fclose($nc_sock);
		$resultCode = substr($headers,9,3);
		if ($resultCode == 200) {
			// success
			$orderId = $bodys;
			echo "success|||".$orderId."|||".$shopId."|||".$totalPrice;
			exit;
		} else {
			echo "regFail|||".$bodys;
			exit;
		}
	}
	else {
		echo "regFail|||$errstr ($errno)<br>\n";
		exit;
	}

/*  
	//워드프레스 wp_remote_request 사용 - 찜 연동 시 문제 발생
	$req_addr = ($nPayData['naver_pay_type']=='test')?"https://test-pay.naver.com/customer/api/order.nhn":"https://pay.naver.com/customer/api/order.nhn";

	$response = wp_remote_request( $req_addr."?".$queryString, array('timeout' => 10, 'headers' => array('Accept-Encoding' => ''), 'sslverify' => true));
	$str_body = wp_remote_retrieve_body($response);

	if($response['headers']['content-length']>'0' && $response['headers']['content-length']<='19' && strtoupper(substr($str_body,0,4))!='FAIL'){
		$orderId = $str_body;
		echo "success|||".$orderId."|||".$shopId."|||".$totalPrice;
		exit;
	} else {
		echo "regFail|||".$str_body;
		exit;
	}
*/
}
else {
	echo "offNpay";
	exit;
}
?>
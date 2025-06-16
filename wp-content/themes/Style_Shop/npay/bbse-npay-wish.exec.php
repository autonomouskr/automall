<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $theme_shortname;

class bbse_nPay_wish_ItemStack {
	var $id;
	var $name;
	var $uprice;
	var $image;
	var $thumb;
	var $url;

	function bbse_nPay_wish_ItemStack($_id, $_name, $_uprice, $_image, $_thumb, $_url) {
		$this->id = $_id;
		$this->name = $_name;
		$this->uprice = $_uprice;
		$this->image = $_image;
		$this->thumb = $_thumb;
		$this->url = $_url;
	}

	function makeQueryString() {
		$ret .= 'ITEM_ID=' . urlencode($this->id);
		$ret .= '&ITEM_NAME=' . urlencode($this->name);
		$ret .= '&ITEM_UPRICE=' . $this->uprice;
		$ret .= '&ITEM_IMAGE=' . urlencode($this->image);
		$ret .= '&ITEM_THUMB=' . urlencode($this->thumb);
		$ret .= '&ITEM_URL=' . urlencode($this->url);

		return $ret;
	}
};


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

	$queryString = 'SHOP_ID='.urlencode($shopId);
	$queryString .= '&CERTI_KEY='.urlencode($certiKey);
	$queryString .= '&RESERVE1=&RESERVE2=&RESERVE3=&RESERVE4=&RESERVE5=';

	if(!$V['goods_idx']){
		echo "emptyGoods";
		exit;
	}

	$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$V['goods_idx']."'"); 
	if($goods->goods_name && !goodsSoldoutCheck($goods)){
		$goodsId=$goods->goods_code;
		$goodsName=$goods->goods_name;
		$goodsPrice=$goods->goods_price;

		$rtnPar=($nPayData['naver_pay_type']=='test')?"&npayTest=true":"";
		$goodsUrl=home_url()."/?bbseGoods=".$goods->idx.$rtnPar;

		$imageList=explode(",",$goods->goods_add_img);
		$goodsImage=$goodsThumb="";
		if($goods->goods_basic_img){
			$imageImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage4");
			$thumbImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage2");
			$goodsImage=$imageImg['0'];
			$goodsThumb=$thumbImg['0'];
		}
		else{
			if(sizeof($imageList)>'0'){
				for($zk=0;$zk<sizeof($imageList);$zk++){
					unset($imageImg);
					$imageImg = wp_get_attachment_image_src($imageList[$zk],"goodsimage4");
					if($imageImg['0']){
						$thumbImg = wp_get_attachment_image_src($imageList[$zk],"goodsimage2");
						$goodsImage=$imageImg['0'];
						$goodsThumb=$thumbImg['0'];
						break;
					}
				}
			}
		}

		if(!$goodsImage){
			$goodsImage=$goodsThumb=esc_url( get_template_directory_uri() )."/images/image_not_exist.png";
		}

		$id = $goodsId;
		$name = $goodsName;
		$uprice = $goodsPrice;
		$image = $goodsImage;
		$thumb = $goodsThumb;
		$url = $goodsUrl;
		$item = new bbse_nPay_wish_ItemStack($id, $name, $uprice, $image, $thumb, $url);
		$queryString .= '&'.$item->makeQueryString();
	}
	else{
		echo "emptyGoods";
		exit;
	}

	//echo($queryString."<br>\n");
	//exit;

	$req_addr = ($nPayData['naver_pay_type']=='test')?"ssl://test-pay.naver.com":"ssl://pay.naver.com";
	$req_url = 'POST /customer/api/wishlist.nhn HTTP/1.1'; // utf-8
	$req_host = ($nPayData['naver_pay_type']=='test')?"test-pay.naver.com":"pay.naver.com";
	$req_port = 443;
	$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
	if ($nc_sock) {
		fwrite($nc_sock, $req_url."\r\n" );
		fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
		fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n"); // utf-8
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
			$itemId = $bodys;
			echo "success|||".$shopId."|||".$itemId;
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
	$req_addr = ($nPayData['naver_pay_type']=='test')?"https://test-pay.naver.com/customer/api/wishlist.nhn":"https://pay.naver.com/customer/api/wishlist.nhn";

	$response = wp_remote_get( $req_addr."?".$queryString, array('timeout' => 10, 'headers' => array('Accept-Encoding' => ''), 'sslverify' => true));
	$str_body = wp_remote_retrieve_body($response);

	if($response['headers']['content-length']>'0' && $response['headers']['content-length']<='19' && strtoupper(substr($str_body,0,4))!='FAIL'){
		$itemId = $str_body;
		echo "success|||".$itemId."|||".$shopId;
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
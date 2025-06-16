<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;
if($V['tMode']=="secondOption") {
	if(!$V['gIdx'] || !$V['oData']){
		echo "DataError";
		exit;
	}
	$rtnStr="";

	$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$V['gIdx']."'");

	if(!$goods->goods_name){
		echo "notExistGoods";
		exit;
	}

	$opt2Cnt = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$V['gIdx']."' AND goods_option_title LIKE %s AND goods_option_item_display='view'", array(like_escape($V['oData']." /")."%"))); 

	if($opt2Cnt<='0'){
		echo "notExistOption";
		exit;
	}

	$rtnStr .="<select name=\"basicOption_2\" id=\"basicOption_2\" onChange=\"detail_basicOptChange(2,2,'".$V['gIdx']."',this.value);\">
								  <option value=\"\">::: 옵션선택 :::</option>";

	$opt2_result = $wpdb->get_results($wpdb->prepare("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$V['gIdx']."' AND goods_option_title LIKE %s AND goods_option_item_display='view' ORDER BY goods_option_item_rank ASC", array(like_escape($V['oData']." /")."%")));
	foreach($opt2_result as $i=>$opt2Data) {
		$optValue=$optStrFlalg=$optCount="";
		$optName=explode(" / ",$opt2Data->goods_option_title);

		$optStrFlalg=" (+".number_format($opt2Data->goods_option_item_overprice)."원)";

		if($goods->goods_count_flag=='option_count'){
			if($opt2Data->goods_option_item_count<='0' || $opt2Data->goods_option_item_soldout=='soldout'){
				$optValue="disabled";
				$optStrFlalg .=" | 품절";
			}
			else{
				if($goods->goods_count_view=='on'){
					$optStrFlalg .=" | ".number_format($opt2Data->goods_option_item_count)."개 남음";
				}

				$optValue="data-overprice=\"".$opt2Data->goods_option_item_overprice."\" data-count=\"".$opt2Data->goods_option_item_count."\"";
			}
		}
		else{
			$optValue="data-overprice=\"".$opt2Data->goods_option_item_overprice."\"";
		}

		$rtnStr .="<option value=\"".$optName['1']."\" ".$optValue.">".$optName['1'].$optStrFlalg."</option>";
	}

	$rtnStr .="</select>";

	if($rtnStr){
		echo "success|||".$rtnStr;
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['tMode']=="addCart") {
	if(!$V['sType'] || !$V['goods_idx']){
		echo "DataError";
		exit;
	}

	$gCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$V['goods_idx']."'"); 

	if($gCnt<='0'){
		echo "notExistGoods";
		exit;
	}

	$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
	$user_id=$current_user->user_login;

	$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사
	if($nPayData['guest_cart_use']=='on' && !$user_id) $user_id=$_SERVER['REMOTE_ADDR'];
	elseif($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
		if($_SESSION['snsLoginData']){
			$snsData=unserialize($_SESSION['snsLoginData']);
			$user_id=$snsData['sns_id'];
		}
		else{
			echo "loginError";
			exit;
		}
	}
	else{
		if(!$user_id){
			echo "loginError";
			exit;
		}
	}

	$sid=session_id();

	if($V['sType']=="direct" || $V['sType']=="cart") $cart_kind="C";
	elseif($V['sType']=="wishlist") $cart_kind="W";
	else{
		echo "DataError";
		exit;
	}

    $goods_idx=$V['goods_idx'];
	$goods_option_basicArray['goods_option_title']=$V['goods_basic_title'];
	$goods_option_basicArray['goods_option_count']=$V['goods_basic_count'];

	$goods_option_addArray['goods_add_title']=$V['goods_add_title'];
	$goods_option_addArray['goods_add_count']=$V['goods_add_count'];

	$remote_ip=$_SERVER['REMOTE_ADDR'];
	$reg_date=current_time('timestamp');

	$cartCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_cart WHERE goods_idx='".$V['goods_idx']."' AND user_id='".$user_id."' AND cart_kind='".$cart_kind."'"); 
	if($cartCnt>'0'){
		if($V['sType']=="wishlist"){
			echo "existWishlist";
			exit;
		}

		$cartData = $wpdb->get_row("SELECT * FROM bbse_commerce_cart WHERE goods_idx='".$V['goods_idx']."' AND user_id='".$user_id."' AND cart_kind='".$cart_kind."'");

		$basicOpt=unserialize($cartData->goods_option_basic);
		$new_bCnt=sizeof($goods_option_basicArray['goods_option_title']);
		$bCnt=sizeof($basicOpt['goods_option_title']);
		for($i=0;$i<$new_bCnt;$i++){
			if($bCnt>'0'){
				if(in_array($goods_option_basicArray['goods_option_title'][$i],$basicOpt['goods_option_title'])){
					$bKey=array_keys($basicOpt['goods_option_title'], $goods_option_basicArray['goods_option_title'][$i]);
					$basicOpt['goods_option_count'][$bKey['0']] +=$goods_option_basicArray['goods_option_count'][$i];
				}
				else{
					$basicOpt['goods_option_title'][]=$goods_option_basicArray['goods_option_title'][$i];
					$basicOpt['goods_option_count'][]=$goods_option_basicArray['goods_option_count'][$i];
				}
			}
			else{
				$basicOpt['goods_option_title'][]=$goods_option_basicArray['goods_option_title'][$i];
				$basicOpt['goods_option_count'][]=$goods_option_basicArray['goods_option_count'][$i];
			}
		}

	   $goods_option_basic=serialize($basicOpt); // Serialize 처리

		$addOpt=unserialize($cartData->goods_option_add);
		$new_aCnt=sizeof($goods_option_addArray['goods_add_title']);
		$aCnt=sizeof($addOpt['goods_add_title']);
		for($i=0;$i<$new_aCnt;$i++){
			if($aCnt>'0'){
				if(in_array($goods_option_addArray['goods_add_title'][$i],$addOpt['goods_add_title'])){
					$aKey=array_keys($addOpt['goods_add_title'], $goods_option_addArray['goods_add_title'][$i]);

					$addOpt['goods_add_count'][$aKey['0']] +=$goods_option_addArray['goods_add_count'][$i];
				}
				else{
					$addOpt['goods_add_title'][]=$goods_option_addArray['goods_add_title'][$i];
					$addOpt['goods_add_count'][]=$goods_option_addArray['goods_add_count'][$i];
				}
			}
			else{
				$addOpt['goods_add_title'][]=$goods_option_addArray['goods_add_title'][$i];
				$addOpt['goods_add_count'][]=$goods_option_addArray['goods_add_count'][$i];
			}
		}

		$goods_option_add=serialize($addOpt); // Serialize 처리

		$wpdb->query("UPDATE bbse_commerce_cart SET sid='".$sid."',goods_option_basic='".$goods_option_basic."',goods_option_add='".$goods_option_add."' WHERE idx='".$cartData->idx."'");

		echo "success";
		exit;
	}
	else{
		$goods_option_basic=serialize($goods_option_basicArray); // Serialize 처리
		$goods_option_add=serialize($goods_option_addArray); // Serialize 처리

		if($V['sType']=="wishlist"){
			$goods_option_basic="";
			$goods_option_add="";
		}

		$inQuery="INSERT INTO bbse_commerce_cart (user_id, sid, cart_kind, goods_idx, goods_option_basic, goods_option_add, remote_ip, reg_date) VALUES ('".$user_id."', '".$sid."', '".$cart_kind."', '".$goods_idx."', '".$goods_option_basic."', '".$goods_option_add."', '".$remote_ip."', '".$reg_date."')";
		$wpdb->query($inQuery);
		$idx = $wpdb->insert_id;

		if($idx){
			echo "success|||".$idx;
			exit;
		}
		else{
			echo "DbError";
			exit;
		}
	}
}
else{
	echo "fail";
	exit;
}

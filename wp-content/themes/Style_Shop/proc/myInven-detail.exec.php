<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

if($V['tMode']=="addCart") {
	if(!$V['goods_idx']){
		echo "DataError";
		exit;
	}

	for($i=0; $i <count($V['goods_idx']); $i++){
    	//$gCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_cart WHERE goods_idx='".$V['goods_idx'][$i]."' and user_id = '".$V['userId']."' and cart_kind = 'C'"); 
    
    	//if($gCnt > '0'){
    	//	echo "exsitCart";
    	//	exit;
    	//}
    	
    
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
    	$cart_kind="C";
    	
    	$order = $wpdb->get_results("select * from bbse_commerce_order_detail where order_no  = '".$V['orderNo'][$i]."'");
    	$sql2 = "select * from bbse_commerce_goods_option where goods_idx = '".$V['goods_idx'][$i]."' and goods_option_title = '".$V['basicOption'][$i]."'";
    	$goodsOption = $wpdb->get_row($sql2);
    	
    	if(count($goodsOption) < 1){
    	    echo "notExsitGoods";
    	    exit;
    	}
    	
    	$goods_idx=$V['goods_idx'][$i];
    	//$inven = $wpdb->get_row("select * from tbl_inven where goods_idx = '".$goods_idx."' and delete_yn != 'Y' and goods_option_basic = '".$V['basicOption'][$i]."'");
    	//$basicOption = unserialize($inven->goods_option_basic);
    	//$addOption = unserialize($inven->goods_option_add);
    	
    	$basicOption = unserialize($order[0]->goods_option_basic);
    	$addOption = unserialize($order[0]->goods_option_add);
    	
    	/* $goods_option_basicArray[] = [
    	    'goods_option_title' => [ 0 => $basicOption['goods_option_title'][$i] ],
    	    'goods_option_overprice' => [ 0 => $goodsOption->goods_option_item_overprice ],
    	    'goods_option_count'=> [0 => $basicOption['goods_option_count'][$i] ]
    	];
    	*/
    	
    	$goods_option_basicArray['goods_option_title'][0]=$basicOption['goods_option_title'][$i];
    	$goods_option_basicArray['goods_option_overprice'][0]=$goodsOption->goods_option_item_overprice;
    	$goods_option_basicArray['goods_option_count'][0]=$basicOption['goods_option_count'][$i];
    	
    	$goods_option_addArray['goods_add_title']=$addOption['goods_add_title'];
    	$goods_option_addArray['goods_add_count']=$addOption['goods_add_count'];
    	
    	$remote_ip=$_SERVER['REMOTE_ADDR'];
    	$reg_date=current_time('timestamp');
    
    	$cartCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_cart WHERE goods_idx='".$V['goods_idx']."' AND user_id='".$user_id."' AND cart_kind='".$cart_kind."'");
    	if($cartCnt>'0'){
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
    	    $wpdb->query("DELETE FROM bbse_commerce_cart WHERE idx='".$V['cart_idx']."' AND user_id='".$user_id."' AND cart_kind='W'");
    	    
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
    	    
    	}
	}
	
    if($idx){
        echo "success|||".$idx;
        exit;
    }
    else{
        echo "DbError";
        exit;
    }
}
else if($V['tMode'] == "Change"){
    $update_date=current_time('timestamp');
    if(count($V['idxs']) > 0){
        for($i=0;$i<count($V['idxs']);$i++){
            $sql = "UPDATE tbl_inven SET notice_count='".$V['notice'][$i]."',update_date='".$update_date."' WHERE idx='".$V['idxs'][$i]."'";
            $result = $wpdb->query("UPDATE tbl_inven SET notice_count='".$V['notice'][$i]."',update_date='".$update_date."' WHERE idx='".$V['idxs'][$i]."'");
        }
    }
    
    if($result > 0){
        echo "success|||".$result;
        exit;
    }
}
else if($V['tMode'] == "delete"){
    $cnt = 0;
    $update_date=current_time('timestamp');
    if(count($V['invenIdxArr']) > 0){
        for($i=0;$i<count($V['invenIdxArr']);$i++){
            $sql = "UPDATE tbl_inven SET delete_yn = 'Y',update_date='".$update_date."' WHERE idx='".$V['invenIdxArr'][$i]."'";
            $result = $wpdb->query($sql);
            if($result > 0){
                $cnt++;
            }
        }
    }
    
    if($cnt > 0){
        echo "success|||".$cnt;
        exit;
    }
}
else{
	echo "fail";
	exit;
}

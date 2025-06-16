<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-config.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/wp-includes/wp-db.php');
	global $wpdb,$current_user;
	$V = $_POST;
	$idxs = explode(',',$V['g_idx']);
	$where = '';
	foreach($idxs AS &$idx){
		$checks = preg_replace('/[^0-9]*/s',"", $idx);
		if($where == ''){
			$where = "C.idx = '".$checks."'";
		}else{
			$where = " OR C.idx = '".$checks."'";
		}
	}
	$order_id = $V['oid'];
	
	wp_get_current_user();
	$currUserID=$current_user->user_login;
	$Loginflag='memer';
	$myInfo=$bbseMember = $wpdb->get_row("
		SELECT A.*,B.class_name,B.use_sale FROM bbse_commerce_membership AS A, bbse_commerce_membership_class AS B WHERE A.user_id='".$currUserID."' AND A.user_class=B.no");
	
	$name = $V['name'];
	$cham = $V['cham'];
	$hp = $V['hp'];
	$email = $V['email'];
	$phone = $V['phone'];
	
	if($name == ''){
		echo '로그인 이후 사용가능한 기능입니다.';
	}else{
		$result = $wpdb->get_results("
				SELECT 	C.idx AS cart_idx, C.goods_option_basic AS cart_option_basic,
						C.goods_option_add AS cart_option_add, G.* 
				FROM 	bbse_commerce_order_detail AS C,
						bbse_commerce_goods AS G 
				WHERE 	C.goods_idx=G.idx  
						AND C.order_no = '".$order_id."'
						ORDER BY C.idx DESC");
		if($result[0]->cart_idx == ''){
			echo '등록할 견적이 없습니다.'.'<br>';
		}else{
			$total_array = array();
			$g_info1 = '';
			
			$total_price = 0;
			$total_amount = 0;
			
			$m_name = $name;
			$m_ph = $hp;
			$m_email = $email;
			$g_info1 = '';
			$g_info2 = '';
			$g_info3 = '';
			$g_info4 = '';
			$g_info5 = '';
			$w_date = date('Y-m-d H:i:s');
			foreach($result as $cart) {
				$add_array = explode('|',$cart->goods_cat_list);
				$total_array = array_merge($total_array,$add_array);
				$amount_1 = explode("goods_option_count",$cart->cart_option_basic);
				$amount_2 = explode(":",$amount_1[1]);
				$last_num = $amount_2[count($amount_2)-1];
				$last_num = preg_replace("/[^0-9]*/s", "",$last_num); 
				
				$goods_total = 	$last_num * $cart->goods_price;
				if($g_info2 == ''){
					$g_info2 = $cart->goods_name;
					$g_info3 = $last_num;
					$g_info4 = $cart->goods_price;
					$g_info5 = $goods_total;
				}else{
					$g_info2 .= ','.$cart->goods_name;
					$g_info3 .= ','.$last_num;
					$g_info4 .= ','.$cart->goods_price;
					$g_info5 .= ','.$goods_total;
				}
			}
			if(count($total_array) > 0){
				$total_array = array_unique($total_array);
				$total_array = array_values(array_filter(array_map('trim',$total_array)));
				$where = '';
				foreach($total_array AS &$idx){
					if($where == ''){
						$where = "idx = '".$idx."'";
					}else{
						$where .= " OR idx = '".$idx."'";
					}
				}
				if($where != ''){
					$result2 = $wpdb->get_results("SELECT c_name FROM bbse_commerce_category WHERE (".$where.") AND depth_2 > 0");
					foreach($result2 AS $c_name){
						if($g_info1 == ''){
							$g_info1 = $c_name->c_name;
						}else{
							$g_info1 .= ','.$c_name->c_name;
						}
					}
				}
			}
			$result = $wpdb->query( $wpdb->prepare("INSERT INTO c_estimate(m_name,m_ph,m_email,g_info1,g_info2,g_info3,g_info4,g_info5,w_date) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)",$m_name,$m_ph,$m_email,$g_info1,$g_info2,$g_info3,$g_info4,$g_info5,$w_date) );
			if(!$result){
				echo 'db등록중 오류가 발생하였습니다. 관리자에게 문의해주세요.';
			}else{
				echo 'success';
			}
		}
	}
?>
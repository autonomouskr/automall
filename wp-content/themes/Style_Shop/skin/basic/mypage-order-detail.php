<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname, $current_user, $orderStatus, $payHow;
wp_get_current_user();

$currUserID=$current_user->user_login;
$Loginflag='member';
$currSnsIdx="";

if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsLoginData=unserialize($_SESSION['snsLoginData']);

		$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
		if($snsData->idx){
			$Loginflag='social';
			$currUserID=$snsLoginData['sns_id'];
			$currSnsIdx=$snsData->idx;
		}
	}
}

$V = $_REQUEST;

if(is_user_logged_in()) {
	$myInfo=bbse_get_user_information();

	$order_no = $V['ordno'];
	$sql = $wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE user_id=%s AND order_no=%s", $currUserID, $order_no);
	$order = $wpdb->get_row($sql);
	if(!$order->idx) {
		echo "<script>alert('잘못된 접근입니다.');location.href='". home_url() . "?bbseMy=mypage';</script>";
		exit;
	}
}else{
	if($Loginflag=='social'){
		$order_no = ($V['ordno'])?$V['ordno']:$V['order_no'];
		$sql = $wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE sns_id=%s AND order_no=%s", $currUserID, $order_no);
		$order = $wpdb->get_row($sql);
		if(!$order->idx) {
			echo "<script>alert('잘못된 접근입니다.');location.href='". home_url() . "?bbseMy=mypage';</script>";
			exit;
		}
	}
	else{
		if(!$V['order_no'] || !$V['order_name']) {
			echo "<script>alert('잘못된 접근입니다.');location.href='". home_url() . "';</script>";
			exit;
		}
		$order_no = $V['order_no'];
		$sql = $wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE user_id='' AND order_no=%s AND order_name=%s", $order_no, $V['order_name']);
		$order = $wpdb->get_row($sql);
	}
	if(!$order->idx) {
		echo "<script>alert('일치하는 주문 정보가 없습니다.');history.back();</script>";
		//location.href='". get_permalink(get_option($theme_shortname."_login_page")) . "';
		exit;
	}
}

if(plugin_active_check('BBSe_Commerce')) bbse_commerce_chk_order_status(); // 취소완료, 배송완료, 구매확정 처리

$orderConfigData = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order'");
$orderCFG = unserialize($orderConfigData);

$addQueryString =  "&page=".$V['page']."&per_page=".$V['per_page']."&search_date1=".$V['search_date1']."&search_date2=".$V['search_date2'];

$modStatusArr = array("CA", "CE", "RA", "RE");
if( in_array($order->order_status, $modStatusArr) == true) {
	$statusClass = " class=\"status_cancel\"";
}else{
	$statusClass = "";
}
$deliveryInfo = unserialize($order->order_config);

$config = $wpdb->get_row("select * from bbse_commerce_membership_config limit 1");

if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){
	$zipcodeScript2 = "openDaumPostcode('receive');";
}else{
	$zipcodeScript2 = "zipcode_search('receive');";
}

if($order->pay_how == "B") {
	$pay_info = unserialize($order->pay_info);
	$payType=$payHow[$order->pay_how];
}else{
	if($order->ezpay_how){
		if($order->ezpay_how=='EPN') $pg_kind='paynow';
		elseif($order->ezpay_how=='EKA') $pg_kind='kakaopay';
		elseif($order->ezpay_how=='EPC') $pg_kind='payco';
		elseif($order->ezpay_how=='EKP') $pg_kind='kpay';
		elseif($order->ezpay_how=='NICE') $pg_kind='nice';

		if(!$pg_kind) {
			echo "<script>alert('관리자에서 간편결제 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
			exit;
		}

		if($pg_kind=='paynow'){
			$pay_info = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_uplus WHERE LGD_OID='".$order->order_no."'");
		}
		elseif($pg_kind=='kakaopay'){
			$pay_info = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_kakaopay WHERE order_no='".$order->order_no."'");
		}
		elseif($pg_kind=='payco'){
			$pay_info = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_payco WHERE LGD_OID='".$order->order_no."'");
		}
		elseif($pg_kind=='kpay'){
			$pay_info = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_inicis WHERE MOID='".$order->order_no."'");
		}
		elseif($pg_kind=='nice'){
			$pay_info = json_decode(base64_decode($order->pay_info),true);
		}

		$payType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_".$pg_kind.".png' class='ezpay_icon' align='absmiddle' alt='".$payHow[$order->pay_how]." 결제' />) - ".$payHow[$order->pay_how];
		if($pg_kind=='nice'){
			$payType="나이스페이 - ".$payHow[$order->pay_how];
		}
	}
	else{
		// 결제모듈 설정
		$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
		if(!$paymentConfig) {
			echo "<script>alert('관리자에서 결제모듈 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
			exit;
		}

		$payCFG = unserialize($paymentConfig);
		$pg_kind = ($payCFG['payment_agent'])?$payCFG['payment_agent']:"allthegate";

		if($pg_kind=='allthegate'){
			$pay_info = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_agspay WHERE rOrdNo='".$order->order_no."'");
		}
		elseif($pg_kind=='INIpay50'){
			$pay_info = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_inicis WHERE MOID='".$order->order_no."'");
		}
		elseif($pg_kind=='lguplusXPay'){
			$pay_info = $wpdb->get_row("SELECT * FROM bbse_commerce_pg_uplus WHERE LGD_OID='".$order->order_no."'");
		}
		$payType=$payHow[$order->pay_how];
	}
}
?>
	  <h2 class="page_title">주문내역</h2>
      <div class="article">
        <ul class="bb_dot_list">
			<li<?php if($order->order_status == "PR"){echo " class=\"order-status-active\"";}?>>STEP.01 입금대기 : 주문이 완료되어 입금 전인 상태입니다. (<?php echo $orderCFG['order_cancel_day']?>일이내 미입금시 자동취소됩니다.)</li>
			<li<?php if($order->order_status == "PE"){echo " class=\"order-status-active\"";}?>>STEP.02 결제완료 : 고객님의 결제내역 확인이 완료되었습니다.</li>
			<li<?php if($order->order_status == "DR"){echo " class=\"order-status-active\"";}?>>STEP.03 배송준비 : 고객님께 상품을 보내드리기 위해 준비중입니다.</li>
			<li<?php if($order->order_status == "DI"){echo " class=\"order-status-active\"";}?>>STEP.04 배송중 : 택배사로 상품이 전달되었습니다.</li>
			<li<?php if($order->order_status == "DE"){echo " class=\"order-status-active\"";}?>>STEP.05 배송완료 : 고객님께 상품전달이 완료된 상태입니다.</li>
<!-- 			<li<?php if($order->order_status == "OE"){echo " class=\"order-status-active\"";}?>>STEP.06 구매확정 : 적립금이 적용된 상품은 적립금이 지급됩니다. (배송완료 후 <?php echo $orderCFG['order_end_day']?>일 이후까지 구매확정을 안한 경우 구매확정 처리됩니다.)</li> -->
        </ul>
      </div>

      <div class="orderDetailHeaderDetail">
        <span class="orderDate">주문일&nbsp;:&nbsp;<em><?php echo date("Y-m-d H:i:s", $order->order_date); ?></em></span>
        <span class="orderNo">주문번호&nbsp;:&nbsp;<em><?php echo $order->order_no; ?></em></span>
        <span class="status">주문상태&nbsp;:&nbsp;<em<?php echo $statusClass?>><?php echo $orderStatus[$order->order_status];?></em></span>
      </div>

        <h3 class="lv2_title">상품정보</h3>
		<div class="fakeTable orderDetail">
			<ul class="header">
				<li>상품명</li>
				<li>수량</li>
				<!-- <li>적립금</li> -->
				<li>합계</li>
			</ul>
			<?php
			$orderCnt = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_order_detail WHERE order_no='".$order->order_no."'");
			$detailRes = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$order->order_no."' ORDER BY idx ASC");
			$mileage_expect = 0;//예상 적립금
			$total_goods_price = 0;//상품금액 합계
			$total_sale_price = 0;//할인금액 합계
			$total_delivery_price = 0;//배송비 합계
			$total_price = 0;//전체합계
			if(count($detailRes) > 0) {
				$option_all_price = 0;
				$add_all_price = 0;
				foreach($detailRes as $index=>$detail) {
					$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$detail->goods_idx."'");
					$goods_total_price = 0;
					$goods_total_count = 0;
					$goods_option_price = 0;
					$goods_add_price = 0;
					$total_option_price = 0;
					$total_add_price = 0;
					$goods_total_cnt = 0;
					$option_cnt = 0;
					$memPrice=unserialize($goods->goods_member_price);

					if($myInfo->user_id && $myInfo->user_class>2 && $myInfo->use_sale=='Y'){
						$salePrice=$goods->goods_price;
						for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
							if($memPrice['goods_member_level'][$m]==$myInfo->user_class) $salePrice=$memPrice['goods_member_price'][$m];
						}
						$savePrice=$goods->goods_consumer_price-$salePrice;
						$myClassName="<span class=\"special_tag\">".$myInfo->class_name."</span>";
					}else{
						$salePrice=$goods->goods_price;
						$savePrice=$goods->goods_consumer_price-$goods->goods_price;
						$myClassName="";
					}

					if($goods->goods_basic_img) $basicImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage3");
					else{
						$imageList=explode(",",$goods->goods_add_img);
						if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage3");
						else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
					}

					$goods_option_add = $goods->goods_option_add?unserialize($goods->goods_option_add):"";
					$option_basic = $detail->goods_option_basic?unserialize($detail->goods_option_basic):"";
					$option_add = $detail->goods_option_add?unserialize($detail->goods_option_add):"";

					if($index == 0) $goods_name = $goods->goods_name;
			?>
			<ul>
				<li class="firstCell">
					<div class="goodsBaseInfo">
						<img src="<?php echo $basicImg['0']; ?>" title="<?php echo $goods->goods_name; ?>" />
						<a href="<?php echo home_url()."/?bbseGoods=".$goods->idx; ?>" class="subj"><?php echo $goods->goods_name; ?></a>
						<?php echo $myClassName; ?>
						<em class="bb_price_info">
							<del><?php echo number_format($goods->goods_consumer_price)?></del>
							<strong class="bb_price"><?php echo number_format($salePrice); ?><span>원</span></strong>
						</em>
					</div>
					<div class="optionList">
						<?php if(count($option_basic['goods_option_title']) > 0) { ?>
						<dl>
							<dt>기본옵션</dt>
							<?php
							for($i=0;$i<count($option_basic['goods_option_title']);$i++) {
								$option_price = 0;
								if($option_basic['goods_option_title'][$i]=="단일상품") {
									$option_basic['goods_option_title'][$i] = "";
									$option_price = $salePrice;
									$goods_option_price += $option_price * $option_basic['goods_option_count'][$i];
									$goods_option_overprice = 0;
									$total_sale_price += ($savePrice * $option_basic['goods_option_count'][$i]);
								}else{
									$goodsOption = $wpdb->get_row("select * from bbse_commerce_goods_option where goods_option_title='".$option_basic['goods_option_title'][$i]."' and goods_idx='".$goods->idx."'");
									$option_price = $salePrice+ $goodsOption->goods_option_item_overprice;
									$goods_option_price += $option_price * $option_basic['goods_option_count'][$i];
									$goods_option_overprice = $goodsOption->goods_option_item_overprice;
									$total_sale_price += ($savePrice * $option_basic['goods_option_count'][$i]);
								}
								$goods_total_cnt += $option_basic['goods_option_count'][$i];
								$option_cnt += $option_basic['goods_option_count'][$i];
							?>
							<dd><span class="optionInfo"><?php echo $option_basic['goods_option_title'][$i]; ?> <?php if($goods_option_overprice>0) echo "(+".number_format($goods_option_overprice).")"; ?></span><span class="each"><?php echo number_format($option_price); ?>원 x <?php echo $option_basic['goods_option_count'][$i]; ?>개</span></dd>
							<?php }?>
						</dl>
						<?php } ?>

						<?php if(count($option_add['goods_add_title']) > 0) { ?>
						<dl>
							<dt>기본옵션</dt>
							<?php
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

								$goods_add_price += $add_price * $option_add['goods_add_count'][$i];
								$goods_total_cnt += $option_add['goods_add_count'][$i];

							?>
							<dd><span class="optionInfo"><?php echo $option_add['goods_add_title'][$i]; ?></span><span class="each"><?php echo number_format($add_price); ?>원 x <?php echo $option_add['goods_add_count'][$i]; ?>개</span></dd>
							<?php }?>

						</dl>
						<?php } ?>
					</div>
				</li>
				<li>
					<span class="mobile-cell-title">수량</span>
					<span class="cell-data"><?php echo number_format($goods_total_cnt); ?></span>
				</li>
				<!-- <li>
					<span class="mobile-cell-title">적립금</span>
					<span class="cell-data">
					<?php
					if($goods->goods_earn_use=='on' && $goods->goods_earn>'0'){
						echo number_format($goods->goods_earn * $option_cnt)."원";
						$mileage_expect += ($goods->goods_earn * $option_cnt);
					}else{
						echo "-";
					}
					?>
					</span>
				</li>
				 -->
				<li>
					<span class="mobile-cell-title">합계</span>
					<span class="cell-data paidAmount"><?php echo number_format($goods_option_price+$goods_add_price); ?>원</span>
				</li>
			</ul>
			</ul>
			<?php
					$total_goods_price += ($goods_option_price+$goods_add_price);
					$option_all_price += $goods_option_price;
					$add_all_price  += $goods_add_price;
				}
				$total_price = $total_goods_price;

				if($deliveryInfo['delivery_charge_type']=='free' || ($deliveryInfo['delivery_charge_type']=='charge' && $deliveryInfo['condition_free_use']=='on' && $total_price>=$deliveryInfo['total_pay'])){
					$delivery_price = 0;
				}else{
					if($deliveryInfo['delivery_charge_payment'] == "advance") {
						$delivery_price = $deliveryInfo['delivery_charge'];
						$total_price += $delivery_price;
					}else{
						$delivery_price = $deliveryInfo['delivery_charge'];
					}
				
				}

				$mileage_flag = "";$mileage_info = "";
				if($earn_pay_use == "Y") {
					$usable_mileage = $total_price * ($earnCFG['earn_max_percent']/100);
					if($usable_mileage < $myInfo->mileage) {
						$real_use_mileage = $usable_mileage;
					}else{
						$real_use_mileage = $myInfo->mileage;
					}
					$real_use_mileage = floor($real_use_mileage / $earnCFG['earn_use_unit']) * $earnCFG['earn_use_unit'];

					if($myInfo->mileage < $earnCFG['earn_hold_point']) {// 사용가능 적립금 보유액
						$mileage_flag = "disabled";
					}
					if($total_price < $earnCFG['earn_order_pay']) {//주문 합계액 기준
						$mileage_flag = "disabled";
					}
					$mileage_info = "<span style='color:#0099e3;'>주문 합계액이 ".number_format($earnCFG['earn_order_pay'])."원 이상, 보유 적립금이 ".number_format($earnCFG['earn_hold_point'])."원 이상일 때 사용가능합니다.<br/>(최소 사용금액 : ".number_format($earnCFG['earn_min_point'])."원 / 최대 사용금액 : 주문금액의 ".number_format($earnCFG['earn_max_percent'])."% / 사용단위 : ".number_format($earnCFG['earn_use_unit'])."원)</span>";
				}else{
					$real_use_mileage = 0;
				}

			}

			$deliveryChargePayment="advance";
			if(!$deliveryInfo['delivery_charge_payment']) $deliveryChargePayment="advance";
			else $deliveryChargePayment=$deliveryInfo['delivery_charge_payment'];

			if($deliveryInfo['delivery_charge_type']=='free'){
				$charge_tit = "무료";
			}else{
				$charge_tit = "유료";
				$delivery_price = $deliveryInfo['delivery_charge'];
				if($deliveryInfo['delivery_charge_payment'] == "advance") {
					$delivery_tit = "선불";
				}else if($deliveryInfo['delivery_charge_payment'] == "deferred"){
					$delivery_tit = "후불";
				}else{
				    $delivery_tit = "착불";
				}
			}
		?>
		</div><!-- fakeTable -->

		<div class="clearFloat"></div>

		<br />
		<?php
		if(!$deliveryInfo['delivery_charge']) $deliveryInfo['delivery_charge']='0';
		if(!$deliveryInfo['total_pay']) $deliveryInfo['total_pay']='0';
		if(!$deliveryInfo['localCnt']) $deliveryInfo['localCnt']='0';
		?>
        <div class="payment_wrap " style="margin:30px 0;" >

          <h3 class="lv2_title">주문하시는 분</h3>
          <div class="tb_payment">
            <table>
            <caption>주문하시는 분</caption>
            <colgroup>
              <col style="width:20%;">
              <col style="width:auto;">
            </colgroup>
            <tbody>
            <tr>
              <th>이름</th>
              <td><?php echo $order->order_name; ?></td>
            </tr>
            <tr>
              <th>배송지 주소</th>
              <td>(<?php echo $order->order_zip?>) <?php echo $order->order_addr1." ".$order->order_addr2; ?></td>
            </tr>
            <tr>
              <th>연락처</th>
              <td class="contactInfo"><?php if(strlen($order->order_phone)>'9'){?><span>전화&nbsp;:&nbsp;<?php echo $order->order_phone; ?></span>,&nbsp;&nbsp;<?php }?><span>휴대전화&nbsp;:&nbsp;<?php echo $order->order_hp; ?></span></td>
            </tr>
            <tr>
              <th>이메일</th>
              <td><?php echo $order->order_email; ?></td>
            </tr>
             <?php
                	$pass_num = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order' order by idx asc");
					$pass_num = unserialize($pass_num);
					$pass_num = $pass_num['pass_num_use'];
					if($pass_num == 'on'):
                ?>
            <tr>
              <th>개인통관번호</th>
              <td><?php echo $order->order_pass_num; ?></td>
            </tr>
            <?php
					endif;
                ?>
            </tbody>
            </table>
          </div>
          <br /><br />

		  <form name="orderDetailFrm" id="orderDetailFrm" method="post">
		  <input type="hidden" name="action_url" id="action_url" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>">
		  <input type="hidden" name="order_no" id="order_no" value="<?php echo $order->order_no;?>">
		  <?php if(!is_user_logged_in()) {?><input type="hidden" name="order_name" id="order_name" value="<?php echo $order->order_name;?>"><?php }?>
		  <input type="hidden" name="tMode" id="tMode" value="receive_modify">
		  <input type="hidden" name="addQueryString" id="addQueryString" value="<?php echo $addQueryString; ?>">
		  <input type="hidden" name="page_kind" id="page_kind" value="detail">
		  <?php 
		  if($order->order_status=="PR" || $order->order_status=="PE" || $order->order_status=="DR"){
			$recieve_zip = $order->receive_zip;
			$receive_phone = explode("-",$order->receive_phone);
			$receive_hp = explode("-",$order->receive_hp);
		  ?>
          <h3 class="lv2_title">상품 받으시는 분</h3>
		  
          <div class="tb_payment mypage_detail">
            <table>
            <caption>상품 받으시는 분</caption>
            <colgroup>
              <col style="width:20%;">
              <col style="width:auto;">
            </colgroup>
            <tbody>
            <tr>
              <th>이름</th>
              <td>
			  	<input type="text" name="receive_name" id="receive_name" value="<?php echo $order->receive_name; ?>" title="이름을 입력해주세요."/>
			  </td>
            </tr>
            <tr>
              <th>배송지 주소</th>
              <td>
				<input type="text" name="receive_zip" id="receive_zip" style="width:70px;text-align:center;" value="<?php echo $recieve_zip; ?>" title="우편번호를 조회해주세요." readonly/>
				<button type="button" class="bb_btn shadow order-zipcode" onclick="<?php echo $zipcodeScript2; ?>"><span class="sml">우편번호검색</span></button>
				<div class="input_wrap address">
					<input type="text" name="receive_addr1" id="receive_addr1" value="<?php echo $order->receive_addr1; ?>" title="주소를 입력해주세요." />
					<input type="text" name="receive_addr2" id="receive_addr2" value="<?php echo $order->receive_addr2; ?>" title="나머지 주소를 입력해주세요." />
				</div>			  
			  </td>
            </tr>
		<?php if($receive_phone[0] && $receive_phone[1] && $receive_phone[2]){?>
            <tr>
              <th>전화번호</th>
              <td>
				<input type="text" name="receive_phone1" id="receive_phone1" style="width:30px;" value="<?php echo $receive_phone[0]; ?>"  title="전화번호 국번을 입력해주세요." maxlength="4" validate-number="true"/>
				-
				<input type="text" name="receive_phone2" id="receive_phone2" style="width:30px;" value="<?php echo $receive_phone[1]; ?>"  title="전화번호 중간번호를 입력해주세요." maxlength="4" validate-number="true"/>
				-
				<input type="text" name="receive_phone3" id="receive_phone3" style="width:30px;" value="<?php echo $receive_phone[2]; ?>"  title="전화번호 마지막번호를 입력해주세요." maxlength="4" validate-number="true"/>
			  </td>
            </tr>
		<?php }?>
            <tr>
              <th>휴대전화번호</th>
              <td>
				<input type="text" name="receive_hp1" id="receive_hp1" style="width:30px;" value="<?php echo $receive_hp[0]; ?>"  title="휴대폰번호 국번을 입력해주세요." maxlength="4" validate-number="true"/>
				-
				<input type="text" name="receive_hp2" id="receive_hp2" style="width:30px;" value="<?php echo $receive_hp[1]; ?>"  title="휴대폰번호 중간번호를 입력해주세요." maxlength="4" validate-number="true"/>
				-
				<input type="text" name="receive_hp3" id="receive_hp3" style="width:30px;" value="<?php echo $receive_hp[2]; ?>"  title="휴대폰번호 마지막번호를 입력해주세요." maxlength="4" validate-number="true"/>
			  </td>
            </tr>
            <tr>
              <th>남기실말씀</th>
              <td><textarea name="order_comment" id="order_comment" title="남기실말씀을 입력해주세요"><?php echo stripslashes($order->order_comment); ?></textarea></td>
            </tr>
            </tbody>
            </table>
          </div>
		  <div class="bb_btn_area">
			<button type="button" class="bb_btn cus_fill w150" onclick="modifyOrder();"><strong class="big">주문정보수정</strong></button>
		  </div>
          <br /><br />

		  <?php }else{?>

          <h3 class="lv2_title">상품 받으시는 분</h3>
          <div class="tb_payment">
            <table>
            <caption>배송 정보</caption>
            <colgroup>
              <col style="width:20%;">
              <col style="width:auto;">
            </colgroup>
            <tbody>
            <tr>
              <th>배송사</th>
              <td>
				<?php
					if($order->delivery_no!="") {
						echo $order->delivery_company." (".$order->delivery_no.")";

						if($order->delivery_url){
							if(substr($order->delivery_url,-1)=="=") $linkDelivery=$order->delivery_url.$order->delivery_no;
							else $linkDelivery=$order->delivery_url;
							echo "&nbsp;&nbsp;&nbsp;<button class=\"bb_btn cus_fill w150\" id='deliveryCheck' onclick=\"window.open('".$linkDelivery."');\" type=\"button\" style='padding:0 5px;line-height:18px;'><strong style='width:80px;'>배송조회</strong></button>";
						}
					}else echo "-";
				?>
			  </td>
            </tr>
            <tr>
              <th>받으시는 분</th>
              <td><?php echo $order->receive_name; ?></td>
            </tr>
            <tr>
              <th>배송지 주소</th>
              <td>(<?php echo $order->receive_zip?>) <?php echo $order->receive_addr1." ".$order->receive_addr2; ?></td>
            </tr>
            <tr>
              <th>연락처</th>
              <td class="contactInfo"><?php if(strlen($order->receive_phone)>'9'){?><span>전화&nbsp;:&nbsp;<?php echo $order->receive_phone; ?></span>,&nbsp;&nbsp;<?php }?><span>휴대전화&nbsp;:&nbsp;<?php echo $order->receive_hp; ?></span></td>
            </tr>
            <tr>
              <th>남기신 말씀</th>
              <td>
			  <?php echo nl2br(htmlspecialchars($order->order_comment)); ?>
			  </td>
            </tr>
            </tbody>
            </table>
          </div>
          <br /><br />
		  <?php }?>
		  </form>


		<div class="delivery_infobox">
			<dl>
				<dt><strong>배송비 정책안내</strong></dt>
				<dd>
					<ul>
						<li>배송비 정책 : <?php echo $charge_tit; ?>배송 상품입니다.</li>
					<?php if($deliveryInfo['delivery_charge_type']!='free'){?>
						<li>기본배송비 : <?php echo $delivery_tit ?>
						<?php if($deliveryInfo['delivery_charge_payment'] == "advance" || $deliveryInfo['delivery_charge_payment'] == "deferred") echo number_format($deliveryInfo['delivery_charge']); ?>원</li>
						  <?php if($deliveryInfo['condition_free_use']=='on'){?>
							<li>조건부 무료배송 : 주문상품의 판매가 기준 총 구매금액이 <?php echo number_format($deliveryInfo['total_pay']); ?>원 이상인 경우 무료입니다.</li>
						  <?php }?>
					<?php }?>
					</ul>
				</dd>
				<dt><strong>지역별 추가배송비 안내</strong> <a href="javascript:void(0);" id="delivery_info_view1"><i class="fa fa-question-circle"></i></a> </dt>
				<dd>
					<div class="layer_delivery" id="layer_delivery1" style="display:none;">
						<div>
							<strong>지역별 추가배송비 안내</strong>
							<dl style="display:block;">
								<?php for($i=1;$i<=$deliveryInfo['localCnt'];$i++) {
									if($deliveryInfo['local_charge_list_'.$i.'_name']) {
								?>
								<dt><?php echo implode(", ",$deliveryInfo['local_charge_list_'.$i.'_name']); ?></dt>
								<dd>ㄴ 추가배송비 : <strong><?php echo number_format($deliveryInfo['local_charge_pay_'.$i]); ?></strong>원</dd>
								<?php
									}
								}
								?>
							</dl>
							<a href="javascript:void(0);" class="layer_del" id="layer_del1"><span class="blind">닫기</span></a>
						</div>
					</div>

				</dd>
			</dl>
		</div>
		<?php
			$deliveryChangeInfoStr="";
			if(($order->delivery_add>'0' || $order->delivery_add_change>'0') && $order->delivery_add!=$order->delivery_add_change && $order->delivery_add_change_date>'0'){
				$changeDeliveryIno = unserialize($order->change_config);
				$changeAddr1=explode(" ",$order->receive_addr1);
		?>
		<br />
		<div class="delivery_infobox">
			<dl>
				<dt><strong>배송지 변경 시 배송비 정책</strong></dt>
				<dd>
					<ul>
						<li>지역별 추가 배송비 : <?php echo number_format($order->delivery_add_change); ?>원 (<?php echo $changeAddr1['0']." ".$changeAddr1['1'];?>)</li>
					</ul>
				</dd>
				<dt><strong>지역별 추가배송비 안내</strong> <a href="javascript:void(0);" id="delivery_info_view2"><i class="fa fa-question-circle"></i></a> </dt>
				<dd>
					<div class="layer_delivery" id="layer_delivery2" style="display:none;">
						<div>
							<strong>지역별 추가배송비 안내</strong>
							<dl style="display:block;">
								<?php for($i=1;$i<=$changeDeliveryIno['localCnt'];$i++) {
									if($changeDeliveryIno['local_charge_list_'.$i.'_name']) {
								?>
								<dt><?php echo implode(", ",$changeDeliveryIno['local_charge_list_'.$i.'_name']); ?></dt>
								<dd>ㄴ 추가배송비 : <strong><?php echo number_format($changeDeliveryIno['local_charge_pay_'.$i]); ?></strong>원</dd>
								<?php
									}
								}
								?>
							</dl>
							<a href="javascript:void(0);" class="layer_del" id="layer_del2"><span class="blind">닫기</span></a>
						</div>
					</div>

				</dd>
			</dl>
		</div>
		<?php
			}
		?>
		<br />

          <h3 class="lv2_title">결제 정보</h3>
          <div class="tb_payment">
            <table>
            <caption>결제 정보</caption>
            <colgroup>
              <col style="width:20%;">
              <col style="width:auto;">
            </colgroup>
            <tbody>
            <tr>
              <th>상품금액</th>
              <td><span class="amountWrap"><?php echo number_format($order->goods_total); ?>원</span></td>
            </tr>
<!--            <tr>
              <th>적립금 사용</th>
              <td><span class="amountWrap">-<?php echo number_format($order->use_earn); ?>원</span></td>
            </tr> --> 
            <tr>
              <th>쿠폰 할인</th>
              <td><span class="amountWrap">-<?php echo number_format($order->coupon_discount); ?>원</span></td>
            </tr>
            <tr>
              <th>회원 할인</th>
              <td><span class="amountWrap">-<?php echo number_format($order->user_discount); ?>원</span></td>
            </tr>
            <tr>
              <th>배송비</th>
              <td>
				<?php
				if($deliveryChargePayment=='advance'){
					if($order->delivery_total>'0') $deliveryTotal="+".number_format($order->delivery_total)."원";
					else $deliveryTotal="+0원";
				}
				else if($deliveryChargePayment=='deferred'){
					if(($order->delivery_total+$order->delivery_add_change)>'0') $deliveryTotal=number_format($order->delivery_total+$order->delivery_add_change)."원";
					else $deliveryTotal="0원";
				}else{
				    $deliveryTotal ="";
				}
				?>
				<span class="amountWrap"><?php echo $deliveryTotal;?></span> <?php echo $delivery_tit?"(".$delivery_tit.")":""?>
			  </td>
            </tr>
			<?php
			if($deliveryChargePayment=='advance' && ($order->delivery_add>'0' || $order->delivery_add_change>'0') && $order->delivery_add!=$order->delivery_add_change && $order->delivery_add_change_date>'0'){
				if(!$order->delivery_add) $delivery_add='0';
				else $delivery_add=$order->delivery_add;

				if(!$order->delivery_add_change) $delivery_add_change='0';
				else $delivery_add_change=$order->delivery_add_change;

				$deliveryAddChange=$delivery_add_change-$delivery_add;

				if($deliveryAddChange>'0') $deliveryChangeStr="+".number_format(abs($deliveryAddChange))."원";
				else $deliveryChangeStr="-".number_format(abs($deliveryAddChange))."원";
			?>
            <tr>
              <th>배송비 변경사항</th>
              <td><span class="amountWrap"><?php echo $deliveryChangeStr;?></td>
            </tr>
			<?php
			}
			?>
            <tr>
              <th>최종 결제</th>
              <td><span class="amountWrap"><em class="finalAmount"><?php echo number_format($order->cost_total); ?></em>원</span></td>
            </tr>
<!--             <tr>
              <th>적립금</th>
              <td><span class="amountWrap"><?php echo number_format($order->add_earn); ?>원</span></td>
            </tr> -->
            <tr>
              <th>결제방법</th>
              <td><?php echo $payType; ?></td>
            </tr>
			<?php if($order->pay_how == "B") {?>
            <tr>
              <th>입금자명</th>
              <td><?php echo $order->input_name; ?></td>
            </tr>
            <tr>
              <th>입금계좌</th>
              <td><?php echo $pay_info['bank_name']." : ".$pay_info['bank_no']." / 예금주 : ".$pay_info['bank_owner']; ?></td>
            </tr>
			<?php }else if($order->pay_how == "C"){?>
			<tr>
				<th scope="row">결제정보</th>
				<td>
				<?php 
				if($pg_kind=='allthegate'){
					$cardName=bbse_commerce_get_card_name($pay_info->rCardCd*1,'allthegate');
					echo "결제완료 (".$cardName."카드, 승인번호 : ".$pay_info->rApprNo.", 거래번호 : ".$pay_info->rDealNo.")";
				}
				elseif($pg_kind=='INIpay50' || $pg_kind=='kpay'){
					$cardName=bbse_commerce_get_card_name($pay_info->CARD_Code,"INIpay50");
					echo "결제완료 (".$cardName."카드, 승인번호 : ".$pay_info->ApplNum.")";
					$email = $order->order_email;
					echo "<a style='display: inline-block;
						    background: #0a0;
						    color: #fff;
						    padding: 0 6px;
						    margin: 3px 0 0;
						    border: 1px solid #383838;
						    background-color: #383838;
						    color: #fff;
						    font-weight: 600;
						    font-size: 12px;
						    padding: 2px 15px 0;
						    line-height: 25px;'
						   target='_blank'
						href=\"https://iniweb.inicis.com/app/publication/apReceipt.jsp?noTid=".$pay_info->TID."&noMethod=1\" >카드전표</a>";
				}
				elseif($pg_kind=='lguplusXPay' || $pg_kind=='paynow'){
					echo "결제완료 (".$pay_info->LGD_FINANCENAME."카드, 승인번호 : ".$pay_info->LGD_FINANCEAUTHNUM.")";
				}
				elseif($pg_kind=='kakaopay'){
					$cardName=bbse_commerce_get_card_name($pay_info->cardCode,"kakaopay");
					echo "결제완료 (".$cardName."카드, 승인번호 : ".$pay_info->authCode.")";
				}
				elseif($pg_kind=='payco'){
					echo "결제완료 (".$pay_info->LGD_FINANCENAME."카드, 승인번호 : ".$pay_info->LGD_FINANCEAUTHNUM.")";
				}
				elseif($pg_kind=='nice'){
					echo "결제완료 (".$pay_info['CardName']."카드, 승인번호 : ".$pay_info['AuthCode'].")";
				}
				?>
				</td>
			</tr>
			<?php }else if($order->pay_how == "K") {?>
			<tr>
				<th scope="row">결제정보</th>
				<td>
				<?php
				if($pg_kind=='allthegate'){
					if($pay_info->ES_SENDNO!="") {
						echo "결제완료 (에스크로 주문번호 : ".$pay_info->ES_SENDNO.")";
					}
				}
				elseif($pg_kind=='INIpay50' || $pg_kind=='kpay'){
					if($pay_info->ACCT_BankCode!="") {
						$bankName=bbse_commerce_get_vbank_name($pay_info->ACCT_BankCode,"INIpay50"); 
						echo "결제완료 (은행명 : ".$bankName.")";
					}
				}
				elseif($pg_kind=='lguplusXPay' || $pg_kind=='paynow'){
					if($pay_info->LGD_FINANCENAME!="") {
						echo "결제완료 (은행명 : ".$pay_info->LGD_FINANCENAME.")";
					}
				}
				elseif($pg_kind=='payco'){
					if($pay_info->LGD_FINANCENAME!="") {
						echo "결제완료 (은행명 : ".$pay_info->LGD_FINANCENAME.")";
					}
				}
				elseif($pg_kind=='nice'){
					echo "결제완료 (은행명 : ".iconv('euc-kr', 'utf-8',$pay_info['BankName']).")";
				}
				?>
				</td>
			</tr>
			<?php }else if($order->pay_how == "V") {?>
			<tr>
				<th scope="row">입금계좌</th>
				<td>
				<?php
				if($pg_kind=='allthegate'){
					$bankName=bbse_commerce_get_vbank_name($pay_info->VIRTUAL_CENTERCD,"allthegate"); // 가상계좌 은행명
					$endInputTime=mktime('23','59','59',substr($pay_info->rApprTm,4,2),substr($pay_info->rApprTm,6,2)+5,substr($pay_info->rApprTm,0,4));

					echo $bankName." : ".$pay_info->rVirNo." / 예금주 : (주)이지스엔터프라이즈 / 입금예정 기한 : ".date("Y.m.d H:i:s",$endInputTime);
					if($pay_info->ES_SENDNO!="") {
						echo " (에스크로 주문번호 : ".$pay_info->ES_SENDNO.")";
					}
				}
				elseif($pg_kind=='INIpay50' || $pg_kind=='kpay'){
					$bankName=bbse_commerce_get_vbank_name($pay_info->VACT_BankCode,"INIpay50"); // 가상계좌 은행명
					$endInputTime=substr($pay_info->VACT_Date,0,4).".".substr($pay_info->VACT_Date,4,2).".".substr($pay_info->VACT_Date,6,2);
					echo $bankName." : ".$pay_info->VACT_Num." / 입금예정 기한 : ".$endInputTime;
				}
				elseif($pg_kind=='lguplusXPay' || $pg_kind=='paynow'){
					$endInputTime=substr($pay_info->LGD_CLOSEDATE,0,4).".".substr($pay_info->LGD_CLOSEDATE,4,2).".".substr($pay_info->LGD_CLOSEDATE,6,2)." ".substr($pay_info->LGD_CLOSEDATE,8,2).":".substr($pay_info->LGD_CLOSEDATE,10,2).":".substr($pay_info->LGD_CLOSEDATE,12,2);
					echo $pay_info->LGD_FINANCENAME." : ".$pay_info->LGD_ACCOUNTNUM." / 입금자명 : ".$pay_info->LGD_PAYER." / 입금예정 기한 : ".$endInputTime;
				}
				elseif($pg_kind=='payco'){
					$endInputTime=substr($pay_info->LGD_CLOSEDATE,0,4).".".substr($pay_info->LGD_CLOSEDATE,4,2).".".substr($pay_info->LGD_CLOSEDATE,6,2)." ".substr($pay_info->LGD_CLOSEDATE,8,2).":".substr($pay_info->LGD_CLOSEDATE,10,2).":".substr($pay_info->LGD_CLOSEDATE,12,2);
					echo $pay_info->LGD_FINANCENAME." : ".$pay_info->LGD_ACCOUNTNUM." / 입금자명 : ".$pay_info->LGD_PAYER." / 입금예정 기한 : ".$endInputTime;
				}
				elseif($pg_kind=='nice'){
					echo iconv('euc-kr', 'utf-8',$pay_info['VbankBankName'])." : ".$pay_info['VbankNum']." / 입금자명 : ".$order->input_name." / 입금예정 기한 : ".$pay_info['VbankExpDate'] ." ".$pay_info['VbankExpTime'];
				}
				?>
				</td>
			</tr>
			<?php }?>

            </tbody>
            </table>
          </div>
        </div>
<!-- 
- 입금대기 -> 취소완료
- 결제완료,배송준비 -> 취소신청
- 배송중,배송완료 -> 반품신청
-->
        <div class="orderDetailBtnBox">
			<?php if($order->order_status == "PR" || $order->order_status == "PE" || $order->order_status == "DR"){//입금대기,결제완료,배송준비?>
			  <button type="button" class="bb_btn shadow orderCancel_open"><span class="mid">주문취소</span></button>
			<?php }else if($order->order_status == "DI"){//배송중?>
			  <button type="button" class="bb_btn cus_fill order_confirm"><span class="mid">구매확정</span></button>
			  <button type="button" class="bb_btn shadow openLayer dimd_btn" data-name="tracking" data-tracking="<?php echo $order->order_no;?>^<?php echo $goods_name; ?><?php if($orderCnt>1){echo " 외 ".number_format($orderCnt-1)."건";}?>^<?php echo $order->delivery_company;?>^<?php echo $order->delivery_no;?>^<?php echo esc_url($order->delivery_url);?>"><span class="mid">배송조회</span></button>
			  <button type="button" class="bb_btn shadow recall_open"><span class="mid">반품신청</span></button>
			<?php }else if($order->order_status == "DE"){//배송완료?>
			  <button type="button" class="bb_btn cus_fill order_confirm"><span class="mid">구매확정</span></button>
			  <button type="button" class="bb_btn shadow openLayer dimd_btn" data-name="tracking" data-tracking="<?php echo $order->order_no;?>^<?php echo $goods_name; ?><?php if($orderCnt>1){echo " 외 ".number_format($orderCnt-1)."건";}?>^<?php echo $order->delivery_company;?>^<?php echo $order->delivery_no;?>^<?php echo esc_url($order->delivery_url);?>"><span class="mid">배송조회</span></button>
			  <button type="button" class="bb_btn shadow recall_open"><span class="mid">반품신청</span></button>		
			<?php }else if($order->order_status == "OE"){//구매확정?>
			  <button type="button" class="bb_btn shadow openLayer dimd_btn" data-name="tracking" data-tracking="<?php echo $order->order_no;?>^<?php echo $goods_name; ?><?php if($orderCnt>1){echo " 외 ".number_format($orderCnt-1)."건";}?>^<?php echo $order->delivery_company;?>^<?php echo $order->delivery_no;?>^<?php echo esc_url($order->delivery_url);?>"><span class="mid">배송조회</span></button>
			<?php }?>
			<?php
				$esti_config = unserialize($wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'")->config_data);
				
				if($esti_config['esti_use'] == 'on' && is_user_logged_in()){
					echo '<button type="button" class="bb_btn estimate">
							<span class="mid">견적서</span>
							</button>
							<script>
								jQuery(function(){
									jQuery(".estimate").click(function(){
										window.open("'.get_template_directory_uri().'/estimate2.php?id='.$order->order_no.'","_blank", "width=750px,height=800");
									});
								});
							</script>
					';
				}
			?>
        </div>


		<br />

		<div class="bb_btn_area">
			<button type="button" class="bb_btn cus_solid w150" onclick="location.href='<?php echo home_url(); ?>';"><strong class="big">쇼핑계속하기</strong></button>
			<?php if(is_user_logged_in()) {?>
			<button type="button" class="bb_btn cus_fill w150" onclick="location.href='<?php echo home_url()."/?bbseMy=order-list".$addQueryString; ?>';"><strong class="big">확인</strong></button>
			<?php }?>
		</div>


      <div id="refundBlock" class="article recall_box">
        <h3 class="lv2_title">반품신청</h3>
        <ul class="bb_dot_list fz11 mt10">
          <li>반품사유를 입력해 주세요. 소중한 의견으로 참고 하겠습니다.</li>
        </ul>
        <p class="att_desc">
        <em>*</em> 은 필수입력 항목입니다.
        </p>
        <div class="tb_payment">
		<form name="orderRefundFrm" id="orderRefundFrm" method="post">
          <table>
          <caption>반품신청</caption>
          <colgroup>
          <col style="width:20%;">
          <col style="width:auto;">
          </colgroup>
          <tbody>
          <tr>
            <th scope="row">주문번호</th>
            <td><?php echo $order->order_no; ?></td>
          </tr>
          <tr>
            <th scope="row">상품명</th>
            <td>
			<?php echo $goods_name; ?><?php if($orderCnt>1){echo " 외 ".number_format($orderCnt-1)."건";}?>
			</td>
          </tr>
          </tr>
          <tr>
            <th scope="row">환불계좌 <em class="c_point">*</em></th>
            <td>
				<p class="my_att_desc"><em>*</em> 결제방법에 관계없이 PG사 취소 승인기간이 지났을 경우 필요한 내용입니다.</p>
				<table class="refund_input">
					<tr>
						<td width="70">은행명 : </td><td><div>은행명 : </div><input type="text" name="refund_bank_name" id="refund_bank_name" title="은행명을 입력해주세요." /></td>
					</tr>
					<tr>
						<td width="70">계좌번호 : </td><td><div>계좌번호 : </div><input type="text" name="refund_bank_no" id="refund_bank_no" title="계좌번호를 입력해주세요."/></td>
					</tr>
					<tr>
						<td width="70">예금주 :  </td><td><div>예금주 : </div><input type="text" name="refund_bank_owner" id="refund_bank_owner" title="예금주를 입력해주세요."/></td>
					</tr>
				</table>
			</td>
          </tr>
          <tr>
            <th scope="row">반품사유 <em class="c_point">*</em></th>
            <td><textarea name="refund_reason" id="refund_reason" cols="30" rows="5" style="width:97%;"></textarea></td>
          </tr>
          </tbody>
          </table>
        </div>
        <div class="bb_btn_area">
          <button type="button" class="bb_btn shadow orderRefund_submit"><strong class="mid c_point">반품신청</strong></button>
          <button type="button" class="bb_btn shadow recall_close"><strong class="mid">취소</strong></button>
        </div>
		</form>
      </div>
      <!--//반품신청-->

	  <div id="cancelBlock" class="article orderCancel_box">
        <h3 class="lv2_title">주문취소 신청</h3>
        <ul class="bb_dot_list fz11 mt10">
          <li>주문취소 사유를 입력해 주세요. 소중한 의견으로 참고 하겠습니다.</li>
        </ul>
        <p class="att_desc">
        <em>*</em> 은 필수입력 항목입니다.
        </p>
        <div class="tb_payment">
	    <form name="orderCancelFrm" id="orderCancelFrm" method="post">
          <table>
          <caption>취소신청</caption>
          <colgroup>
          <col style="width:20%;">
          <col style="width:auto;">
          </colgroup>
          <tbody>
          <tr>
            <th scope="row">주문번호</th>
            <td><?php echo $order->order_no; ?></td>
          </tr>
          <tr>
            <th scope="row">상품명</th>
            <td>
			<?php echo $goods_name; ?><?php if($orderCnt>1){echo " 외 ".number_format($orderCnt-1)."건";}?>
			</td>
          </tr>
          <tr <?php if($order->order_status == "PR"){echo "style='display:none;'";}?>>
            <th scope="row">환불계좌
            <td>
				<p class="my_att_desc"> 결제방법에 관계없이 PG사 취소 승인기간이 지났을 경우 필요한 내용입니다.</p>
				<table class="refund_input">
					<tr>
						<td width="70">은행명 : </td><td><div>은행명 : </div><input type="text" name="refund_bank_name" id="refund_bank_name" title="은행명을 입력해주세요." /></td>
					</tr>
					<tr>
						<td width="70">계좌번호 : </td><td><div>계좌번호 : </div><input type="text" name="refund_bank_no" id="refund_bank_no" title="계좌번호를 입력해주세요."/></td>
					</tr>
					<tr>
						<td width="70">예금주 :  </td><td><div>예금주 : </div><input type="text" name="refund_bank_owner" id="refund_bank_owner" title="예금주를 입력해주세요."/></td>
					</tr>
				</table>
			</td>
          </tr>
          <tr>
            <th scope="row">취소사유 <em class="c_point">*</em></th>
            <td><textarea name="refund_reason" id="refund_reason" cols="30" rows="5" style="width:97%;"></textarea></td>
          </tr>
          </tbody>
          </table>
		  </form>
        </div>
        <div class="bb_btn_area">
          <button type="button" class="bb_btn shadow orderCancel_submit"><strong class="mid c_point">취소신청</strong></button>
          <button type="button" class="bb_btn shadow orderCancel_close"><strong class="mid">취소</strong></button>
        </div>
      </div>
      <!--//취소신청-->

<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */?>
<div id="commerceZipcodeLayer" style="display:none;border:5px solid;position:fixed;width:320px;height:500px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script><!--https-->
<script>
	var element = document.getElementById('commerceZipcodeLayer');
	function closeDaumPostcode() {
		element.style.display = 'none';
	}
	function openDaumPostcode(fieldTitle){
		new daum.Postcode({
			oncomplete: function(data){
				if(data.userSelectedType === 'R'){
					// 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
					// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
					var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
					var extraRoadAddr = ''; // 도로명 조합형 주소 변수

					// 법정동명이 있을 경우 추가한다. (법정리는 제외)
					// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
					if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
						extraRoadAddr += data.bname;
					}
					// 건물명이 있고, 공동주택일 경우 추가한다.
					if(data.buildingName !== '' && data.apartment === 'Y'){
					   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
					if(extraRoadAddr !== ''){
						extraRoadAddr = ' (' + extraRoadAddr + ')';
					}
					// 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
					if(fullRoadAddr !== ''){
						fullRoadAddr += extraRoadAddr;
					}

					jQuery('#'+fieldTitle+'_zip').val(data.zonecode);
					jQuery('#'+fieldTitle+'_addr1').val(fullRoadAddr);
				}
				else{
					jQuery('#'+fieldTitle+'_zip').val(data.postcode1+"-"+data.postcode2);
					jQuery('#'+fieldTitle+'_addr1').val(data.jibunAddress);
				}
				jQuery('#'+fieldTitle+'_addr2').focus();
			}
		}).open();
	}
</script> 
<?php }?>
<script>
var zipcode_search = function(fieldTitle){
	window.open(jQuery("#action_url").val()+"/zipcode.php?fieldTitle="+fieldTitle+"&act=no", "zipcode_search", "width=400,height=400,scrollbars=yes");
}
var modifyOrder = function() {
	jQuery.ajax({
		url: jQuery("#action_url").val() + "/proc/mypage-order.exec.php"
		, type: 'post'
		, data: jQuery("#orderDetailFrm").serialize()
		, success: function(result) {
			if(result == "success") {
				alert("정상적으로 처리되었습니다.");
				if(common_var.u != "") {
					location.href = common_var.home_url + "/?bbseMy=order-detail&ordno=" + jQuery("#order_no").val() + jQuery("#addQueryString").val();
				}else{
					jQuery('#orderCancelFrm').attr("action", common_var.home_url + "/?bbseMy=order-detail");
					jQuery('#orderCancelFrm').append('<input type="hidden" name="order_no" id="order_no" value="'+jQuery("#order_no").val()+'">');
					jQuery('#orderCancelFrm').append('<input type="hidden" name="order_name" id="order_name" value="'+jQuery("#order_name").val()+'">');
					jQuery('#orderCancelFrm').submit();
				}
			}else if(result == "DataError") {
				alert("입력값을 확인해주세요.");
			}else if(result == "BadAccess") {
				alert("잘못된 접근입니다.");
			}else{
				alert("정보 수정중 오류가 발생하였습니다.\n\n잠시후에 시도해주세요.");
			}
		}
		, error: function(data, status, err) {
			alert('서버와의 통신이 실패했습니다.');
		}
	});
}
jQuery(document).ready(function() {
	jQuery("#layer_delivery1").hide();
	jQuery("#layer_delivery2").hide();
	jQuery("#delivery_info_view1").bind('click', function () {jQuery("#layer_delivery1").show();});
	jQuery("#delivery_info_view2").bind('click', function () {jQuery("#layer_delivery2").show();});
	jQuery("#layer_del1").bind('click', function () {jQuery("#layer_delivery1").hide();});
	jQuery("#layer_del2").bind('click', function () {jQuery("#layer_delivery2").hide();});
});

</script> 
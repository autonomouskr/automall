<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* Cart */
get_header();

global $current_user,$theme_shortname;
wp_get_current_user();

$bbsePage=get_query_var( 'bbsePage' );
$ref = wp_get_referer();
$purl = parse_url($ref);

if(!$_GET['result'] && $purl['query'] != "bbsePage=order") {
	echo "<script>alert('잘못된 접근입니다.');location.href='".home_url()."';</script>";
	exit;
}

$result = base64_decode($_GET['result']);
$expResult = explode("|||",$result);

$ordCnt = $wpdb->get_var($wpdb->prepare("
	SELECT COUNT(*) 
	FROM bbse_commerce_order 
	WHERE order_no=%s AND idx=%d", $expResult[0], $expResult[1]));
if($ordCnt < 1) {
	echo "<script>alert('잘못된 접근입니다.');location.href='".home_url()."';</script>";
	exit;
}

$order = $wpdb->get_row($wpdb->prepare("SELECT * FROM bbse_commerce_order WHERE order_no=%s AND idx=%d", $expResult[0], $expResult[1]));

if(is_user_logged_in()) {
	$ok_url =home_url()."/?bbseMy=order-list";
	$myInfo=bbse_get_user_information();
}
elseif($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']) $ok_url =home_url()."/?bbseMy=order-list";
	else $ok_url = get_permalink(get_option('bbse_commerce_login_page'));
}
else{
	$ok_url = get_permalink(get_option('bbse_commerce_login_page'));
}

$deliveryInfo = unserialize($order->order_config);

if($order->pay_how == "B") {
	$pay_info = unserialize($order->pay_info);
	$payType=$payHow[$order->pay_how];
}else{
	if($order->ezpay_how){
		if($order->ezpay_how=='EPN') $pg_kind='paynow';
		elseif($order->ezpay_how=='EKA') $pg_kind='kakaopay';
		elseif($order->ezpay_how=='EPC') $pg_kind='payco';
		elseif($order->ezpay_how=='EKP') $pg_kind='kpay';
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
			//print_r($pay_info);
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


if (get_option($theme_shortname."_google_use_analytics") == 'U' && get_option($theme_shortname."_google_trackingid") && get_option($theme_shortname."_google_option3") == 'on' ){
	// Google Analytics : Transaction Data
	$trans = array('id'=>$order->order_no, 'affiliation'=>get_bloginfo('name'), 'revenue'=>$order->cost_total, 'shipping'=>$order->delivery_total, 'tax'=>'');
}
?>
<script language="javascript">
	jQuery(document).ready(function() {
		jQuery(".layer_delivery").hide();
		jQuery("#delivery_info_view").bind('click', function () {
			jQuery(".layer_delivery").show();
		});

		jQuery(".layer_delivery .layer_del").bind('click', function () {
			jQuery(".layer_delivery").hide();
		});
	});
</script>
	<hr />

	<div id="content">

        <?php
        #로케이션
        get_template_part('part/sub', 'location');
        ?>

		<div class="page_cont"  id="bbsePage<?php echo $bbsePage?>">


			<h2 class="page_title">주문접수완료</h2>

			<div class="article">
				자세한 주문내역은 <strong>마이페이지 &gt; 주문/배송 메뉴</strong>를 이용해 주세요.
				<ol class="bb_join_step">
					<li>01 장바구니</li>
					<li>02 주문서작성/결제</li>
					<li class="active">03 주문접수완료</li>
				</ol>
			</div>

			<div class="article">
				<div class="bd_box bold_bd">
					<p class="bb_leave_text">
						<em>주문</em>이 정상적으로 <strong>완료</strong>되었습니다.
					</p>
					<p class="bb_leave_user">
						주문내역 및 상품 발송 정보는 <strong><?php echo $order->order_name; ?></strong>님의 이메일(<?php echo $order->order_email; ?>)로 다시 안내해 드리겠습니다.
					</p>
					<p class="bb_result_text">
						주문번호 <strong><?php echo $order->order_no; ?></strong>
					</p>
				</div>
			</div>



			<div class="fakeTable orderDetail order">
				<ul class="header">
					<li>상품명</li>
					<li>수량</li>
					<li>적립금</li>
					<li>합계</li>
				</ul>
				<?php
				$detailRes = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$expResult[0]."' ORDER BY idx ASC");
				$mileage_expect = 0;//예상 적립금
				$total_goods_price = 0;//상품금액 합계
				$total_sale_price = 0;//할인금액 합계
				$total_delivery_price = 0;//배송비 합계
				$total_price = 0;//전체합계
				if(count($detailRes) > 0) {
					$option_all_price = 0;
					$add_all_price = 0;
					$items = array();
					foreach($detailRes as $detail) {
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
							    if($memPrice['goods_member_level'][$m]==$myInfo->user_class) {
							        $memberPrice=$memPrice['goods_member_price'][$m];
							        $goods_vat=$memPrice['goods_vat'][$m];
							        $salePrice=$memberPrice+$goods_vat;
							    }
							}
							$savePrice=$goods->goods_consumer_price-$salePrice;
							$myClassName="<span class=\"special_tag\">".$myInfo->class_name."</span>";
						}else{
							$salePrice=$goods->goods_price;
							$savePrice=$goods->goods_consumer_price-$goods->goods_price;
							$myClassName="";
						}

						if($goods->goods_basic_img) $basicImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage2");
						else{
							$imageList=explode(",",$goods->goods_add_img);
							if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage2");
							else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
						}

						$goods_option_add = $goods->goods_option_add?unserialize($goods->goods_option_add):"";
						$option_basic = $detail->goods_option_basic?unserialize($detail->goods_option_basic):"";
						$option_add = $detail->goods_option_add?unserialize($detail->goods_option_add):"";

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
					<li>
						<span class="mobile-cell-title">적립금</span>
						<span class="cell-data">
						<?php
						if(is_user_logged_in() && $goods->goods_earn_use=='on' && $goods->goods_earn>'0'){
							echo number_format($goods->goods_earn * $option_cnt)."원";
							$mileage_expect += ($goods->goods_earn * $option_cnt);
						}else{
							echo "-";
						}
						?>
						</span>
					</li>
					<li>
						<span class="mobile-cell-title">합계</span>
						<span class="cell-data paidAmount"><?php echo number_format($goods_option_price+$goods_add_price); ?>원</span>
					</li>
				</ul>
				</ul>
				<?php
						if (get_option($theme_shortname."_google_use_analytics") == 'U' && get_option($theme_shortname."_google_trackingid") && get_option($theme_shortname."_google_option3") == 'on' ){
							// Google Analytics ecommerce : List of Items Purchased.
							$categoryArray = explode("|",$goods->goods_cat_list);
							$categoryName = $wpdb->get_var("select c_name from bbse_commerce_category where idx='".$categoryArray[1]."'");
							$items[] = array('sku'=>$detail->goods_idx, 'name'=>$goods->goods_name, 'category'=>$categoryName, 'price'=>($goods_option_price+$goods_add_price), 'quantity'=>$goods_total_cnt);
						}

						$total_goods_price += ($goods_option_price+$goods_add_price);
						$option_all_price += $goods_option_price;
						$add_all_price  += $goods_add_price;
					}
					//$total_goods_price += $total_sale_price;
					//$total_price = ($total_goods_price - $total_sale_price);
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

			<br />
			<?php
			if(!$deliveryInfo['delivery_charge']) $deliveryInfo['delivery_charge']='0';
			if(!$deliveryInfo['total_pay']) $deliveryInfo['total_pay']='0';
			if(!$deliveryInfo['localCnt']) $deliveryInfo['localCnt']='0';
			?>
			<div class="clearFloat"></div>
			<div class="delivery_infobox">
				<dl>
					<dt><strong>배송비 정책안내</strong></dt>
					<dd>
						<ul>
							<li>배송비 정책 : <?php echo $charge_tit; ?>배송 상품입니다.</li>
						<?php if($deliveryInfo['delivery_charge_type']!='free'){?>
							<li>기본배송비 : <?php echo $delivery_tit." ".number_format($deliveryInfo['delivery_charge']); ?>원</li>
						  <?php if($deliveryInfo['condition_free_use']=='on'){?>
							<li>조건부 무료배송 : 주문상품의 판매가 기준 총 구매금액이 <?php echo number_format($deliveryInfo['total_pay']); ?>원 이상인 경우 무료입니다.</li>
						  <?php }?>
						<?php }?>
						</ul>
					</dd>
					<dt><strong>지역별 추가배송비 안내</strong> <a href="javascript:void(0);" id="delivery_info_view"><i class="fa fa-question-circle"></i></a> </dt>
					<dd>
						<div class="layer_delivery" style="display:none;">
							<div>
								<strong>지역별 추가배송비 안내</strong>
								<dl style="display:block;">
									<?php for($i=1;$i<=$deliveryInfo['localCnt'];$i++) {
										if($deliveryInfo['local_charge_list_'.$i.'_name']){
									?>
										<dt><?php echo implode(", ",$deliveryInfo['local_charge_list_'.$i.'_name']); ?></dt>
										<dd>ㄴ 추가배송비 : <strong><?php echo number_format($deliveryInfo['local_charge_pay_'.$i]); ?></strong>원</dd>
									<?php 
										}
									}
									?>
								</dl>
								<a href="javascript:void(0);" class="layer_del"><span class="blind">닫기</span></a>
							</div>
						</div>
					</dd>
				</dl>
			</div>
			<br />

			<h3 class="orderTblTitle">상품 받으시는 분</h3>
			<div class="fakeTable orderPageCommon">
				<ul>
					<li class="bgHave">이름</li>
					<li><?php echo $order->receive_name; ?></li>
				</ul>
				<ul>
					<li class="bgHave">주소</li>
					<li><?php echo ($order->receive_zip)?"(".$order->receive_zip.") ":"";?><?php echo $order->receive_addr1." ".$order->receive_addr2; ?></li>
				</ul>
				<ul>
					<li class="bgHave">연락처</li>
					<li><?php if(strlen($order->receive_phone)>'9'){?>전화번호: <?php echo $order->receive_phone; ?> | <?php }?>휴대폰번호 : <?php echo $order->receive_hp; ?> </li>
				</ul>
				<ul>
					<li class="bgHave">남기실 말씀</li>
					<li>
						<?php echo nl2br(htmlspecialchars($order->order_comment)); ?>
					</li>
				</ul>
			</div><!-- fakeTable -->
			<div class="clearFloat"></div>

			<h3 class="orderTblTitle">결제 정보</h3>
			<table class="paymentInfo">
				<caption>결제 정보</caption>
				<colgroup>
					<col style="width:15%;">
					<col style="width:auto;">
				</colgroup>

				<tfoot>
					<tr>
						<th scope="row">최종결제금액</th>
						<td class="finalAmount"><em><?php
						
						if($order->delivery_total > 0){
						    $totalPrice = number_format($total_goods_price-$order->use_earn -$order->coupon_discount - $order->user_discount + $order->delivery_total);
						}else{
						    $totalPrice = number_format($total_goods_price-$order->use_earn -$order->coupon_discount - $order->user_discount);
						}
						echo $totalPrice;
        				?></em>원</td>
					</tr>
					<tr>
						<th scope="row">결제방법</th>
						<td><?php echo $payType; ?></td>
					</tr>
					<?php if($order->pay_how == "B") {?>
					<tr>
						<th scope="row">입금자명</th>
						<td><?php echo $order->input_name; ?></td>
					</tr>
					<tr>
						<th scope="row">입금계좌</th>
						<td><?php echo $pay_info['bank_name']." : ".$pay_info['bank_no']." / 예금주 : ".$pay_info['bank_owner']; ?></td>
					</tr>

					<?php }else if($order->pay_how == "C"){?>
					<tr>
						<th scope="row">결제정보</th>
						<td>
						<?php 
						if($pg_kind=='allthegate'){
							$cardName=bbse_commerce_get_card_name($pay_info->rCardCd*1,"allthegate");
							echo "결제완료 (".$cardName."카드, 승인번호 : ".$pay_info->rApprNo.", 거래번호 : ".$pay_info->rDealNo.")";
						}
						elseif($pg_kind=='INIpay50' || $pg_kind=='kpay'){
							$cardName=bbse_commerce_get_card_name($pay_info->CARD_Code,"INIpay50");
							echo "결제완료 (".$cardName."카드, 승인번호 : ".$pay_info->ApplNum.")";
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
							if($pay_info->LGD_FINANCENAME!="") {
								echo "결제완료 (은행명 : ".iconv('euc-kr', 'utf-8',$pay_info['BankName']).")";
							}
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
				</tfoot>



				<tbody>
					<tr>
						<th scope="row">상품금액</th>
						<td><strong><?php echo number_format($total_goods_price); ?>원</strong></td>
					</tr>
					<tr>
						<th scope="row">적립금사용</th>
						<td>-<?php echo number_format($order->use_earn); ?>원</td>
					</tr>
					<tr>
						<th scope="row">쿠폰할인</th>
						<td>-<?php echo number_format($order->coupon_discount); ?>원</td>
					</tr>
					<tr>
						<th scope="row">회원할인</th>
						<td>-<?php echo number_format($order->user_discount); ?>원</td>
					</tr>
					<tr>
						<th scope="row">합계</th>
						<td><?php echo number_format($total_goods_price-$order->use_earn -$order->coupon_discount - $order->user_discount); ?>원</td>
					</tr>
					<tr>
						<th scope="row">배송비</th>
						<td><?php 
						if($delivery_tit == "착불"){
						    echo ""; 
						}else{
                            echo number_format($order->delivery_total),'원'; 
						}?> <?php echo $delivery_tit?"(".$delivery_tit.")":""?></td>
					</tr>
				</tbody>
			</table>


			<div class="bb_btn_area">
				<button class="bb_btn cus_solid w150" onclick="location.href='<?php echo home_url(); ?>';"><strong class="big">쇼핑계속하기</strong></button>
				<button class="bb_btn cus_fill w150" onclick="location.href='<?php echo $ok_url; ?>';"><strong class="big">확인</strong></button>
			</div>





		</div>

	</div><!--//#content -->

<?php if (get_option($theme_shortname."_google_use_analytics") == 'U' && get_option($theme_shortname."_google_trackingid") && get_option($theme_shortname."_google_option3") == 'on' ){?>
<?php
// GOOGLE ANALYTICS ECOMMERCE
ob_start();
echo getTransactionJs($trans);
foreach ($items as &$item) {
  echo getItemJs($trans['id'], $item);
}
$google_ecommerce_order_data = ob_get_contents();
ob_end_clean();
?>
<?php }?>
<?php get_footer();

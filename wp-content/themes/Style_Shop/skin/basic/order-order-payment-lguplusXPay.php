<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* 
* LG U+ PG (유플러스) 연동
*/
if($V['pay_how']=='C') $escrowYN='N';

$LGD_ESCROW_USEYN=$escrowYN;

if($V['deviceType']=='mobile' || $V['deviceType']=='tablet'){
	if($V['pay_how']=='C'){
		$payHow="SC0010"; // 모바일 결제방법(신용카드:SC0010, 계좌이체:SC0030, 무통장입금(가상계좌):SC0040)
		$payHowTitle="신용카드";
		$LGD_ESCROW_USEYN="N";
	}
	elseif($V['pay_how']=='V'){
		$payHow="SC0040";
		$payHowTitle="가상계좌";
	}
	elseif($V['pay_how']=='EPN'){
		$LGD_ESCROW_USEYN='N';
		$payHow="SC0010";
		$payHowTitle="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_paynow.png' class='ezpay_icon' align='absmiddle' alt='Paynow 결제' />)";
	}
}
else{
	if($V['pay_how']=='C'){
		$payHow="SC0010"; // PC 결제방법(신용카드:SC0010, 계좌이체:SC0030, 무통장입금(가상계좌):SC0040)
		$payHowTitle="신용카드";
		$LGD_ESCROW_USEYN="N";
	}
	elseif($V['pay_how']=='K'){
		$payHow="SC0030";
		$payHowTitle="실시간 계좌이체";
	}
	elseif($V['pay_how']=='V'){
		$payHow="SC0040";
		$payHowTitle="가상계좌";
	}
	elseif($V['pay_how']=='EPN'){
		$LGD_ESCROW_USEYN='N';
		$payHow="SC0010";
		$payHowTitle="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_paynow.png' class='ezpay_icon' align='absmiddle' alt='Paynow 결제' />)";
	}
}

$ordr_idxx = $V['order_no'];

$goodsNames = array();

$goodsImage="";
$goodsIdx=$goodsCode="";
foreach($result as $gData) {
	$goodsNames[] = $gData->goods_name;

	if(!$goodsCode){ 
		$goodsIdx=$gData->idx;
		$goodsCode=$gData->goods_code;
	}

	if(!$goodsImage){
		$imageList=explode(",",$gData->goods_add_img);
		$goodsImage="";
		if($gData->goods_basic_img){
			$imageImg = wp_get_attachment_image_src($gData->goods_basic_img,"goodsimage4");
			$goodsImage=$imageImg['0'];
		}
		else{
			if(sizeof($imageList)>'0'){
				for($zk=0;$zk<sizeof($imageList);$zk++){
					unset($imageImg);
					$imageImg = wp_get_attachment_image_src($imageList[$zk],"goodsimage4");
					if($imageImg['0']){
						$goodsImage=$imageImg['0'];
						break;
					}
				}
			}
		}
	}
}

if(count($goodsNames) > 1) $addGoodsCnt = " 외 ".(count($goodsNames)-1);
else $addGoodsCnt = "";

$good_name = $goodsNames[0].$addGoodsCnt;

if($V['goods_option_price']=="") $V['goods_option_price']=0;
if($V['goods_add_price']=="") $V['goods_add_price']=0;
if($V['delivery_price']=="") $V['delivery_price']=0;
if($V['delivery_add_price']=="") $V['delivery_add_price']=0;
if($V['use_earn']=="") $V['use_earn']=0;

if($V['delivery_charge_type']!='free' &&  $V['delivery_charge_payment']=='deferred'){ // 후불
	$good_mny = ($V['goods_option_price']+$V['goods_add_price'])-$V['use_earn'];
}
else{
	$good_mny = ($V['goods_option_price']+$V['goods_add_price']+$V['delivery_price']+$V['delivery_add_price'])-$V['use_earn'];
}

$confOrder=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
$ordCfn=unserialize($confOrder->config_data);

if($ordCfn['total_pay_unit']>'0'){
	if($ordCfn['total_pay_round']=='down'){
		$good_mny=floor($good_mny/$ordCfn['total_pay_unit'])*$ordCfn['total_pay_unit'];
	}
	else{
		$good_mny=floor(($good_mny+($ordCfn['total_pay_unit']*0.9))/$ordCfn['total_pay_unit'])*$ordCfn['total_pay_unit'];
	}
}

$OrdNm=$V['order_name'];
$OrdPhone=$V['order_hp1']."-".$V['order_hp2']."-".$V['order_hp3'];
$UserEmail=$V['order_email'];

////고객정보
if(is_user_logged_in()) {
	$myInfo=bbse_get_user_information();
	$UserId = $myInfo->user_id;
	if(trim($myInfo->name)) 	$OrdNm = trim($myInfo->name);    //이름

	$OrdPhone = ($myInfo->hp)?$myInfo->hp:$OrdPhone;    //휴대폰 번호
	$UserEmail = $myInfo->email;    //메일
}

if($V['deviceType']=='mobile' || $V['deviceType']=='tablet') $Remark=$V['mobile_Remark']; 
else $Remark=$V['order_comment']; //기타요구사항
?>
	<hr />
	<div id="content">
        <?php
        #로케이션
        get_template_part('part/sub', 'location');
        ?>

		<div class="page_cont"  id="bbsePage<?php echo $bbsePage; ?>">

			<h2 class="page_title">주문확인/결제</h2>
			<div class="article">
				주문하실 상품 총 <strong><?php echo number_format(count($result)); ?></strong>개
				<ol class="bb_join_step">
					<li>01 장바구니</li>
					<li class="active">02 주문서작성/결제</li>
					<li>03 주문접수완료</li>
				</ol>
			</div>
			<?php 
			if(plugin_active_check('BBSe_Commerce')) {
			?>
			<div class="fakeTable orderDetail order">
				<ul class="header">
					<li>상품명</li>
					<li>수량</li>
					<li>적립금</li>
					<li>합계</li>
				</ul>
				<?php
				$mileage_expect = 0;//예상 적립금
				$total_goods_price = 0;//상품금액 합계
				$total_sale_price = 0;//할인금액 합계
				$total_delivery_price = 0;//배송비 합계
				$total_price = 0;//전체합계

				if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){
					$zipcodeScript1 = "openDaumPostcode('order');";
					$zipcodeScript2 = "openDaumPostcode('receive');";
				}else{
					$zipcodeScript1 = "zipcode_search('order');";
					$zipcodeScript2 = "zipcode_search('receive');";
				}

				if(count($result) > 0) {
					$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
					$deliveryInfo=unserialize($confData->config_data);
					$option_all_price = 0;
					$add_all_price = 0;
					$goodsNames = array();
					foreach($result as $cart) {

						$goods_total_price = 0;
						$goods_total_count = 0;
						$goods_option_price = 0;
						$goods_add_price = 0;
						$total_option_price = 0;
						$total_add_price = 0;
						$goods_total_cnt = 0;
						$option_cnt = 0;
						$memPrice=unserialize($cart->goods_member_price);

						if($myInfo->user_id && $myInfo->user_class>2 && $myInfo->use_sale=='Y'){
							$salePrice=$cart->goods_price;
							for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
								if($memPrice['goods_member_level'][$m]==$myInfo->user_class) $salePrice=$memPrice['goods_member_price'][$m];
							}
							$savePrice=$cart->goods_consumer_price-$salePrice;
							$myClassName="<span class=\"special_tag\">".$myInfo->class_name."</span>";
						}else{
							$salePrice=$cart->goods_price;
							$savePrice=$cart->goods_consumer_price-$cart->goods_price;
							$myClassName="";
						}

						if($cart->goods_basic_img) $basicImg = wp_get_attachment_image_src($cart->goods_basic_img,"goodsimage2");
						else{
							$imageList=explode(",",$cart->goods_add_img);
							if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage2");
							else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
						}

						$goods_option_add = $cart->goods_option_add?unserialize($cart->goods_option_add):"";
						if($orderType == "cart") {//장바구니
							$option_basic = $cart->cart_option_basic?unserialize($cart->cart_option_basic):"";
							$option_add = $cart->cart_option_add?unserialize($cart->cart_option_add):"";
						}else{//바로구매
							$directOption = ($V['goods_info']!="")?$goods_info:$V;
							$resultOption = goodsOptionSerialize($directOption);
							$option_basic = unserialize($resultOption['goods_option_basic']);
							$option_add = unserialize($resultOption['goods_option_add']);
						}
						$goodsNames[] = $cart->goods_name;
				?>
				<ul>
					<li class="firstCell">
						<div class="goodsBaseInfo">
							<img src="<?php echo $basicImg['0']; ?>" title="<?php echo $cart->goods_name; ?>" />
							<a href="<?php echo home_url()."/?bbseGoods=".$cart->idx; ?>" class="subj"><?php echo $cart->goods_name; ?></a>
							<?php echo $myClassName; ?>
							<em class="bb_price_info">
								<del><?php echo number_format($cart->goods_consumer_price)?></del>
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
										$goodsOption = $wpdb->get_row("select * from bbse_commerce_goods_option where goods_option_title='".$option_basic['goods_option_title'][$i]."' and goods_idx='".$cart->idx."'");
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
						if(is_user_logged_in() && $cart->goods_earn_use=='on' && $cart->goods_earn>'0'){
							echo number_format($cart->goods_earn * $option_cnt)."원";
							$mileage_expect += ($cart->goods_earn * $option_cnt);
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
						$total_goods_price += ($goods_option_price+$goods_add_price);
						$option_all_price += $goods_option_price;
						$add_all_price  += $goods_add_price;
					}
					//$total_goods_price += $total_sale_price;
					//$total_price = ($total_goods_price - $total_sale_price);
					$total_price = $total_goods_price;

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

					if($deliveryInfo['delivery_charge_type']=='free' || ($deliveryInfo['delivery_charge_type']=='charge' && $deliveryInfo['condition_free_use']=='on' && $total_price>=$deliveryInfo['total_pay'])){
						$delivery_price = 0;
						$delivery_tit = " (무료)";
					}else{
						if($deliveryInfo['delivery_charge_payment'] == "advance") {
							$delivery_price = $deliveryInfo['delivery_charge'];
							$delivery_tit = " (선불)";
							$total_price += $delivery_price;
						}else{
							$delivery_price = $deliveryInfo['delivery_charge'];
							$delivery_tit = " (후불)";
						}
					}

					if($deliveryInfo['delivery_charge_type']=='free'){
						$charge_tit = "무료";
					}else{
						$charge_tit = "유료";
						if($deliveryInfo['delivery_charge_payment'] == "advance") {
							$delivery_tit2 = "선불";
						}else{
							$delivery_tit2 = "후불";
						}
					}
					
					if($orderCFG['total_pay_unit']) {
						if($orderCFG['total_pay_round'] == "down") {
							$total_price = floor($total_price / $orderCFG['total_pay_unit']) * $orderCFG['total_pay_unit']; //결제금액 절삭처리
						}else if($orderCFG['total_pay_round'] == "up") {
							$total_price = ceil($total_price / $orderCFG['total_pay_unit']) * $orderCFG['total_pay_unit']; //결제금액 올림처리
						}
					}

				}
			?>
			</div><!-- fakeTable -->

			<div class="clearFloat"></div>
			<dl class="tb_foot">
				<dt><strong class="mb_hide">총합계금액</strong></dt>
				<dd>
					<div class="bb_left fz11">
						<span class="c_green">
							예상적립금 <strong><?php echo ($Loginflag=='guest')?"0":number_format($mileage_expect); ?></strong>원
						</span>
					</div>
					<div class="bb_right">
						<strong class="bb_price">상품합계 <span style="font-size:12px;font-weight:normal;">(배송비 미포함)</span> : <em><?php echo number_format($total_goods_price); ?></em>원</strong>
					</div>
				</dd>
			</dl>

			<?php
			if(!$deliveryInfo['delivery_charge']) $deliveryInfo['delivery_charge']='0';
			if(!$deliveryInfo['total_pay']) $deliveryInfo['total_pay']='0';
			if(!$deliveryInfo['localCnt']) $deliveryInfo['localCnt']='0';
			?>
			<br />
			<div class="clearFloat"></div>
			<div class="delivery_infobox">
				<dl>
					<dt><strong>배송비 정책안내</strong></dt>
					<dd>
						<ul>
							<li>배송비 정책 : <?php echo $charge_tit; ?>배송 상품입니다.</li>
						<?php if($deliveryInfo['delivery_charge_type']!='free'){?>
							<li>기본배송비 : <?php echo $delivery_tit2." ".number_format($deliveryInfo['delivery_charge']); ?>원</li>
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

			<div class="article">
				<div class="payment_wrap">
					<div class="bb_pay_left">
						<h3 class="orderTblTitle">주문하시는 분</h3>
						<div class="fakeTable orderPageCommon">
							<ul>
								<li class="bgHave">이름</li>
								<li><?php echo $V['order_name'];?></li>
							</ul>
							<ul>
								<li class="bgHave">주소</li>
								<li><?php echo $V['order_zip'];?> <?php echo $V['order_addr1'];?> <?php echo $V['order_addr2'];?></li>
							</ul>
						<?php if($V['order_phone1'] && $V['order_phone2'] && $V['order_phone3']){?>
							<ul>
								<li class="bgHave">전화번호</li>
								<li><?php echo $V['order_phone1'];?>-<?php echo $V['order_phone2'];?>-<?php echo $V['order_phone3'];?></li>
							</ul>
						<?php }?>
							<ul>
								<li class="bgHave">휴대전화번호</li>
								<li><?php echo $V['order_hp1'];?>-<?php echo $V['order_hp2'];?>-<?php echo $V['order_hp3'];?></li>
							</ul>
							<ul>
								<li class="bgHave">이메일</li>
								<li><?php echo $V['order_email'];?></li>
							</ul>
						</div><!-- fakeTable -->
						<div class="clearFloat"></div>

						<h3 class="orderTblTitle">상품 받으시는 분</h3>
						<div class="fakeTable orderPageCommon">
							<ul>
								<li class="bgHave">이름</li>
								<li><?php echo $V['receive_name'];?></li>
							</ul>
							<ul>
								<li class="bgHave">주소</li>
								<li><?php echo $V['receive_zip'];?> <?php echo $V['receive_addr1'];?> <?php echo $V['receive_addr2'];?></li>
							</ul>
						<?php if($V['receive_phone1'] && $V['receive_phone2'] && $V['receive_phone3']){?>
							<ul>
								<li class="bgHave">전화번호</li>
								<li><?php echo $V['receive_phone1'];?>-<?php echo $V['receive_phone2'];?>-<?php echo $V['receive_phone3'];?></li>
							</ul>
						<?php }?>
							<ul>
								<li class="bgHave">휴대전화번호</li>
								<li><?php echo $V['receive_hp1'];?>-<?php echo $V['receive_hp2'];?>-<?php echo $V['receive_hp3'];?></li>
							</ul>
							<ul>
								<li class="bgHave">남기실말씀</li>
								<li><?php echo nl2br($V['order_comment']);?></li>
							</ul>
						</div><!-- fakeTable -->
						<div class="clearFloat"></div>
					</div><!--//.bb_pay_left -->

					<div id="paymentFixedArea" class="bb_pay_right">
						<div id="paymentFixed">
							<div class="bb_inner">
								<h3 class="blind">총 상품 금액</h3>
								<ul>
									<li>
										<em>상품금액</em>
										<span><span id="payview_total_goods_price"><?php echo number_format($total_goods_price); ?></span>원</span>
									</li>
									<li>
										<em>적립금사용</em>
										<span>(-) <span id="payview_use_earn_price"><?php echo number_format($V['use_earn']);?></span>원</span>
									</li>
									<li>
										<em>합계</em>
										<span><span id="payview_total_goods_earn_price"><?php echo number_format($total_goods_price-$V['use_earn']); ?></span>원</span>
									</li>
								</ul>
								<ul>
									<li>
										배송비<label id="delivery_tit"><?php echo $delivery_tit;?></label>
										<span>+<span id="payview_delivery_price"><?php echo number_format($V['delivery_price']+$V['delivery_add_price']); ?></span>원</span>
									</li>
								</ul>
								<ul>
									<li>
										<em>결제금액</em>
										<span><strong><span id="payview_total_price"><?php echo number_format($good_mny); ?></span></strong>원</span>

									</li>
									<li>
										<em class="payHow-title" style="font-size:14px;font-weight:700;">결제수단</em>
										<span id="pay_how_view" class="payHow-view" style="font-size:14px;font-weight:700;">
											<?php echo ($escrowYN=='Y')?"<img src='".BBSE_THEME_WEB_URL."/images/icon_escrow.png' align='absmiddle' alt='에스크로(매매보호 서비스) 적용' title='에스크로(매매보호 서비스) 적용' /> ":"";?><?php echo $payHowTitle;?>
										</span>
									</li>
									<li>
										<em>예상적립금</em>
										<span><span id="payview_mileage_expect"><?php echo ($Loginflag=='guest')?"0":number_format($mileage_expect); ?></span>원</span>
									</li>
								</ul>
								<br />
								<div class="bb_btn_area">
									<button type="button" class="bb_btn cus_fill pay-action" onClick="pay();"><strong class="big">결제하기</strong></button>
									<button type="button" class="bb_btn cus_solid back-action"><strong class="big">주문취소</strong></button>
								</div>
							</div>
						</div>

					</div><!--//.bb_pay_right -->
				</div><!--//.payment_wrap -->
			</div>
			<?php }else{echo "BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.";} ?>
		</div>
	</div><!--//#content -->

<?php 
$CST_PLATFORM = $XpayMertType;      //LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
$CST_MID = $StoreId;           //상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요)
										   //테스트 아이디는 't'를 반드시 제외하고 입력하세요.
$LGD_MID = (("test" == $CST_PLATFORM)?"t":"").$CST_MID;  //상점아이디(자동생성)
$LGD_MERTKEY = $XpayMertKey;    // 상점MertKey : 관리자모드 설정, LGU+의 상점관리자 페이지(mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)

$LGD_OID = $ordr_idxx;           //주문번호(상점정의 유니크한 주문번호를 입력하세요)
$LGD_AMOUNT = $good_mny;        //결제금액("," 를 제외한 결제금액을 입력하세요)
$LGD_BUYER = $OrdNm;         //구매자명
$LGD_PRODUCTINFO = $good_name;   //상품명
$LGD_BUYEREMAIL = $UserEmail;    //구매자 이메일
$LGD_CUSTOM_FIRSTPAY = $payHow;    //상점정의 초기결제수단
$LGD_TIMESTAMP = date("YmdHis");                         //타임스탬프

/*추가 정보 (시작)*/
$LGD_BUYERID = $UserId;                           //구매자 ID
$LGD_BUYERIP=$_SERVER['REMOTE_ADDR']; //구매자 IP
$LGD_BUYERPHONE=$OrdPhone;	//구매자휴대폰번호
$LGD_BUYEREMAIL = $UserEmail;                      //구매자 이메일
$LGD_BUYERADDRESS = $V['order_addr1']."-".$V['order_addr2'];             //구매자주소  =>  (DB 저장 안함)

$LGD_PRODUCTCODE = $goodsCode;                                                                               //상품코드  =>  (DB 저장 안함)

$LGD_RECEIVER = $V['receive_name'];                                                                                //수취인  =>  (DB 저장 안함)
$LGD_RECEIVERPHONE = $V['receive_hp1']."-".$V['receive_hp2']."-".$V['receive_hp3'];             //수취인전화번호  =>  (DB 저장 안함)
$LGD_DELIVERYINFO = $V['receive_addr1']." ".$V['receive_addr2'];             //수취인주소  =>  (DB 저장 안함)

if($V['pay_how']=='V'){
	$tTime=current_time('timestamp')+(60*60*24*5);
	$LGD_CLOSEDATE=date("YmdHis",$tTime); // 가상계좌 결제마감기간 (yyyyMMddHHmmss)
}
else $LGD_CLOSEDATE="";

if($LGD_ESCROW_USEYN=='Y'){ // 에스크로를 사용하는 경우  =>  (DB 저장 안함)
	// 상품정보가 복수개일 때 에스크로상품번호 ~ 에스크로상품수량  필드를 중복해서 사용 (5개의 에스크로 필드를 반드시 한쌍으로 적용)
	$LGD_ESCROW_GOODID = $goodsIdx;                              	// 에스크로상품번호
	$LGD_ESCROW_GOODNAME = $LGD_PRODUCTINFO;            // 에스크로상품명
	$LGD_ESCROW_GOODCODE = $goodsCode;                        	//	에스크로상품코드
	$LGD_ESCROW_UNITPRICE = $total_goods_price;	                    // 에스크로상품 가격 (<= 상품합계금액으로 대체 함)
	$LGD_ESCROW_QUANTITY = '1';	            // 에스크로상품수량 (<= 수량을 무조건 1로 대체 함)

	$LGD_ESCROW_ZIPCODE = $V['receive_zip'];	   // 에스크로배송지우편번호
	$LGD_ESCROW_ADDRESS1 = $V['receive_addr1'];                     //	에스크로배송지주소동까지
	$LGD_ESCROW_ADDRESS2 = $V['receive_addr2'];	                //	에스크로배송지주소상세
	$LGD_ESCROW_BUYERPHONE = $LGD_BUYERPHONE;          //	에스크로구매자휴대폰번호
}

$LGD_ENCODING=$LGD_ENCODING_NOTEURL=$LGD_ENCODING_RETURNURL="UTF-8"; // Character Set 선택
/*추가 정보 (끝)*/

$CONFIG_PATH = BBSE_COMMERCE_THEME_ABS_PATH."/payment/lguplusXPay/lgdacom"; //LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정. 

/*
 *************************************************
 * 2. MD5 해쉬암호화 (수정하지 마세요) - BEGIN
 * 
 * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
 *************************************************
 *
 * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
 * LGD_MID          : 상점아이디
 * LGD_OID          : 주문번호
 * LGD_AMOUNT       : 금액
 * LGD_TIMESTAMP    : 타임스탬프
 * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
 *
 * MD5 해쉬데이터 암호화 검증을 위해
 * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
 */
$LGD_HASHDATA = md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_TIMESTAMP.$LGD_MERTKEY);
$LGD_CUSTOM_PROCESSTYPE = "TWOTR";
/*
 *************************************************
 * 2. MD5 해쉬암호화 (수정하지 마세요) - END
 *************************************************
 */

if($V['deviceType']=='mobile' || $V['deviceType']=='tablet'){

    /*
     * [결제 인증요청 페이지(STEP2-1)]
     *
     * 샘플페이지에서는 기본 파라미터만 예시되어 있으며, 별도로 필요하신 파라미터는 연동메뉴얼을 참고하시어 추가 하시기 바랍니다.     
     */

    /*
     * 1. 기본결제 인증요청 정보 변경
     * 
     * 기본정보를 변경하여 주시기 바랍니다.(파라미터 전달시 POST를 사용하세요)
     */
    $LGD_CUSTOM_SKIN = "SMART_XPAY2";                        //상점정의 결제창 스킨
    /*
     * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. 
     */    
    $LGD_CASNOTEURL = BBSE_COMMERCE_THEME_WEB_URL."/payment/lguplusXPay/Xpay_noteurl.php"; 

    /*
     * LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
     */    
    $LGD_RETURNURL = BBSE_COMMERCE_THEME_WEB_URL."/payment/lguplusXPay/Xpay_returnurl_mobile.php"; 
	
	$LGD_KVPMISPAUTOAPPYN="A"; // ISP 결제 처리방식 (동기/비동기) : Y : ISP 비동기 결제처리,  A: ISP 동기 결제처리,  N:  ISP 동기 결제처리(iOS Web-to-Web) 

    /*
	 * BBS e-Commerce의 경우 동기방식을 이용하므로 아래 3개의 파라미터는 무시한다.
     * ISP 카드결제 연동중 모바일ISP방식(고객세션을 유지하지않는 비동기방식)의 경우, LGD_KVPMISPNOTEURL/LGD_KVPMISPWAPURL/LGD_KVPMISPCANCELURL를 설정하여 주시기 바랍니다. 
     */    
    $LGD_KVPMISPNOTEURL = "";
    $LGD_KVPMISPWAPURL = "";   //ISP 카드 결제시, URL 대신 앱명 입력시, 앱호출함 
    $LGD_KVPMISPCANCELURL = "";
    
    $CST_WINDOW_TYPE = "submit";                                       // 수정불가
    $payReqMap['CST_PLATFORM'] = $CST_PLATFORM;              // 테스트, 서비스 구분
    $payReqMap['CST_WINDOW_TYPE'] = $CST_WINDOW_TYPE;           // 수정불가
    $payReqMap['CST_MID'] = $CST_MID;                   // 상점아이디
    $payReqMap['LGD_MID'] = $LGD_MID;                   // 상점아이디
    $payReqMap['LGD_OID'] = $LGD_OID;                   // 주문번호
    $payReqMap['LGD_BUYER'] = $LGD_BUYER;            	   // 구매자
    $payReqMap['LGD_PRODUCTINFO'] = $LGD_PRODUCTINFO;     	   // 상품정보
    $payReqMap['LGD_AMOUNT'] = $LGD_AMOUNT;                // 결제금액
    $payReqMap['LGD_BUYEREMAIL'] = $LGD_BUYEREMAIL;            // 구매자 이메일
    $payReqMap['LGD_CUSTOM_SKIN'] = $LGD_CUSTOM_SKIN;           // 결제창 SKIN
    $payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;    // 트랜잭션 처리방식
    $payReqMap['LGD_TIMESTAMP'] = $LGD_TIMESTAMP;             // 타임스탬프
    $payReqMap['LGD_HASHDATA'] = $LGD_HASHDATA;              // MD5 해쉬암호값
    $payReqMap['LGD_RETURNURL'] = $LGD_RETURNURL;      	   // 응답수신페이지
    $payReqMap['LGD_VERSION'] = "PHP_SmartXPay_1.0";		   // 버전정보 (삭제하지 마세요)
    $payReqMap['LGD_CUSTOM_FIRSTPAY'] = $LGD_CUSTOM_FIRSTPAY;	   // 디폴트 결제수단
	$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']  = "SUBMIT";	       // 신용카드 카드사 인증 페이지 연동 방식
	
    /*
    ****************************************************
    * 안드로이드폰 신용카드 ISP(국민/BC)결제에만 적용 (시작)*
    ****************************************************

    (주의)LGD_CUSTOM_ROLLBACK 의 값을  "Y"로 넘길 경우, LG U+ 전자결제에서 보낸 ISP(국민/비씨) 승인정보를 고객서버의 note_url에서 수신시  "OK" 리턴이 안되면  해당 트랜잭션은  무조건 롤백(자동취소)처리되고,
    LGD_CUSTOM_ROLLBACK 의 값 을 "C"로 넘길 경우, 고객서버의 note_url에서 "ROLLBACK" 리턴이 될 때만 해당 트랜잭션은  롤백처리되며  그외의 값이 리턴되면 정상 승인완료 처리됩니다.
    만일, LGD_CUSTOM_ROLLBACK 의 값이 "N" 이거나 null 인 경우, 고객서버의 note_url에서  "OK" 리턴이  안될시, "OK" 리턴이 될 때까지 3분간격으로 2시간동안  승인결과를 재전송합니다.

	* BBS e-Commerce의 경우 동기방식을 이용하므로 아래 4개의 파라미터는 무시한다.
    */

    $payReqMap['LGD_CUSTOM_ROLLBACK'] = "";			   	   				     // 비동기 ISP에서 트랜잭션 처리여부
    $payReqMap['LGD_KVPMISPNOTEURL'] = $LGD_KVPMISPNOTEURL;			   // 비동기 ISP(ex. 안드로이드) 승인결과를 받는 URL
    $payReqMap['LGD_KVPMISPWAPURL'] = $LGD_KVPMISPWAPURL;			   // 비동기 ISP(ex. 안드로이드) 승인완료후 사용자에게 보여지는 승인완료 URL
    $payReqMap['LGD_KVPMISPCANCELURL'] = $LGD_KVPMISPCANCELURL;		   // ISP 앱에서 취소시 사용자에게 보여지는 취소 URL

    /*
    ****************************************************
    * 안드로이드폰 신용카드 ISP(국민/BC)결제에만 적용    (끝) *
    ****************************************************
    */

    $payReqMap['LGD_KVPMISPAUTOAPPYN'] = $LGD_KVPMISPAUTOAPPYN; // ISP 결제 처리방식 (동기/비동기) : Y : ISP 비동기 결제처리,  A: ISP 동기 결제처리,  N:  ISP 동기 결제처리(iOS Web-to-Web) 

    // 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
    $payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // 가상계좌 NOTEURL

    //Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
    $payReqMap['LGD_RESPCODE'] = "";
    $payReqMap['LGD_RESPMSG'] = "";
    $payReqMap['LGD_PAYKEY'] = "";

	/*추가 정보 (시작)*/
	$payReqMap['LGD_BUYERID'] = $LGD_BUYERID;                   //구매자 ID
	$payReqMap['LGD_BUYERIP'] = $LGD_BUYERIP;            	    // 구매자 IP
	$payReqMap['LGD_BUYERPHONE']=$LGD_BUYERPHONE;   	//구매자휴대폰번호
	$payReqMap['LGD_BUYEREMAIL'] = $LGD_BUYEREMAIL;       //구매자 이메일
	$payReqMap['LGD_BUYERADDRESS'] = $LGD_BUYERADDRESS;             //구매자주소  =>  (DB 저장 안함)

	$payReqMap['LGD_PRODUCTCODE'] = $LGD_PRODUCTCODE;              //상품코드  =>  (DB 저장 안함)

	$payReqMap['LGD_RECEIVER'] = $LGD_RECEIVER;                               //수취인  =>  (DB 저장 안함)
	$payReqMap['LGD_RECEIVERPHONE'] = $LGD_RECEIVERPHONE;             //수취인전화번호  =>  (DB 저장 안함)
	$payReqMap['LGD_DELIVERYINFO'] = $LGD_DELIVERYINFO;             //수취인주소  =>  (DB 저장 안함)

	$payReqMap['LGD_CLOSEDATE'] = $LGD_CLOSEDATE;      // 가상계좌 결제마감기간 (yyyyMMddHHmmss)  =>  (DB 저장 안함)

	if($LGD_ESCROW_USEYN=='Y'){ // 에스크로를 사용하는 경우  =>  (DB 저장 안함)
		// 상품정보가 복수개일 때 에스크로상품번호 ~ 에스크로상품수량  필드를 중복해서 사용 (5개의 에스크로 필드를 반드시 한쌍으로 적용)
		$payReqMap['LGD_ESCROW_GOODID'] = $LGD_ESCROW_GOODID;                              	// 에스크로상품번호
		$payReqMap['LGD_ESCROW_GOODNAME'] = $LGD_ESCROW_GOODNAME;            // 에스크로상품명
		$payReqMap['LGD_ESCROW_GOODCODE'] = $LGD_ESCROW_GOODCODE;                        	//	에스크로상품코드
		$payReqMap['LGD_ESCROW_UNITPRICE'] = $LGD_ESCROW_UNITPRICE;	                    // 에스크로상품 가격 (<= 상품합계금액으로 대체 함)
		$payReqMap['LGD_ESCROW_QUANTITY'] = $LGD_ESCROW_QUANTITY;	            // 에스크로상품수량 (<= 수량을 무조건 1로 대체 함)

		$payReqMap['LGD_ESCROW_ZIPCODE'] = $LGD_ESCROW_ZIPCODE;	   // 에스크로배송지우편번호
		$payReqMap['LGD_ESCROW_ADDRESS1'] = $LGD_ESCROW_ADDRESS1;                     //	에스크로배송지주소동까지
		$payReqMap['LGD_ESCROW_ADDRESS2'] = $LGD_ESCROW_ADDRESS2;	                //	에스크로배송지주소상세
		$payReqMap['LGD_ESCROW_BUYERPHONE'] = $LGD_ESCROW_BUYERPHONE;          //	에스크로구매자휴대폰번호
	}

	if($V['pay_how']=='EPN'){ // 간편결제 (Paynow) 전용창 요청
		$payReqMap['LGD_EASYPAY_ONLY'] = "PAYNOW";
		$payReqMap['LGD_MONEPAYAPPYN'] = "N";
	}
	/*추가 정보 (끝)*/

	/*모바일 전용 추가 정보(시작)*/
    $payReqMap['LGD_MERTKEY'] = $LGD_MERTKEY;
    $payReqMap['Column2'] = $deviceType;
    $payReqMap['orderList'] = $orderList;
    $payReqMap['CONFIG_PATH'] = $CONFIG_PATH;
    $payReqMap['BBSE_COMMERCE_THEME_WEB_URL'] = BBSE_COMMERCE_THEME_WEB_URL;
	/*모바일 전용 추가 정보(끝)*/

    $_SESSION['PAYREQ_MAP'] = $payReqMap;
?>
		<script language="javascript" src="http://xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js" type="text/javascript"></script>
		<script type="text/javascript">

		/*
		* iframe으로 결제창을 호출하시기를 원하시면 iframe으로 설정 (변수명 수정 불가)
		*/
			var LGD_window_type = '<?php echo $CST_WINDOW_TYPE ?>'; 
		/*
		* 수정불가
		*/
		function pay(){
			  lgdwin = open_paymentwindow(document.getElementById('LGD_PAYINFO'), '<?php echo $CST_PLATFORM ?>', LGD_window_type);
		}
		/*
		* FORM 명만  수정 가능
		*/
		function getFormObject() {
				return document.getElementById("LGD_PAYINFO");
		}

		</script>
		<form method="post" name="LGD_PAYINFO" id="LGD_PAYINFO" accept-charset="euc-kr">
		<?php
		  foreach ($payReqMap as $key => $value) {
			echo "<input type='hidden' name='".$key."' id='".$key."' value='".$value."'>";
		  }
		?>
		</form>
<?php 
}
else{
	$LGD_CUSTOM_SKIN = "red";                                         //상점정의 결제창 스킨
	$LGD_CUSTOM_USABLEPAY = $payHow;        	     // 디폴트 결제수단 (특정결제수단만 보이게 할 경우 사용 , 
																			 //신용카드:SC0010, 계좌이체:SC0030, 무통장입금(가상계좌):SC0040, 휴대폰:SC0060, 유선전화결제:SC0070, OK캐쉬백:SC0090,문화상품권: SC0111, 게임문화상품권:SC0112
																			 // 예)신용카드,계좌이체만 사용할 경우SC0010-SC0030)

	$LGD_WINDOW_VER = "2.5";										 //결제창 버젼정보
	$LGD_WINDOW_TYPE = "iframe";                //결제창 호출방식 (수정불가)
	$LGD_CUSTOM_SWITCHINGTYPE = "IFRAME";                //신용카드 카드사 인증 페이지 연동 방식 (수정불가)
	/*
	 * 가상계좌(무통장) 결제 연동을 하시는 경우 아래 LGD_CASNOTEURL 을 설정하여 주시기 바랍니다. 
	 */    
	$LGD_CASNOTEURL = BBSE_COMMERCE_THEME_WEB_URL."/payment/lguplusXPay/Xpay_noteurl.php";    

	/*
	 * LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
	 */    
	$LGD_RETURNURL = BBSE_COMMERCE_THEME_WEB_URL."/payment/lguplusXPay/Xpay_returnurl.php";  

	$payReqMap['CST_PLATFORM'] = $CST_PLATFORM;              // 테스트, 서비스 구분
	$payReqMap['LGD_WINDOW_TYPE'] = $LGD_WINDOW_TYPE;           // 수정불가
	$payReqMap['LGD_ESCROW_USEYN'] = $LGD_ESCROW_USEYN;           // 에스크로 사용여부

	$payReqMap['CST_MID'] = $CST_MID;                   // 상점아이디
	$payReqMap['LGD_MID'] = $LGD_MID;                   // 상점아이디
	$payReqMap['LGD_OID'] = $LGD_OID;                   // 주문번호
	$payReqMap['LGD_BUYER'] = $LGD_BUYER;            	   // 구매자
	$payReqMap['LGD_BUYERID'] = $LGD_BUYERID;            	   // 구매자 ID
	$payReqMap['LGD_PRODUCTINFO'] = $LGD_PRODUCTINFO;     	   // 상품정보
	$payReqMap['LGD_AMOUNT'] = $LGD_AMOUNT;                // 결제금액
	$payReqMap['LGD_BUYEREMAIL'] = $LGD_BUYEREMAIL;            // 구매자 이메일
	$payReqMap['LGD_CUSTOM_SKIN'] = $LGD_CUSTOM_SKIN;           // 결제창 SKIN
	$payReqMap['LGD_CUSTOM_PROCESSTYPE'] = $LGD_CUSTOM_PROCESSTYPE;    // 트랜잭션 처리방식
	$payReqMap['LGD_TIMESTAMP'] = $LGD_TIMESTAMP;             // 타임스탬프
	$payReqMap['LGD_HASHDATA'] = $LGD_HASHDATA;              // MD5 해쉬암호값
	$payReqMap['LGD_RETURNURL'] = $LGD_RETURNURL;      	   // 응답수신페이지
	$payReqMap['LGD_VERSION'] = "PHP_2.5";		   // 버전정보 (삭제하지 마세요)
	$payReqMap['LGD_CUSTOM_USABLEPAY'] = $LGD_CUSTOM_USABLEPAY;	   // 디폴트 결제수단
	$payReqMap['LGD_CUSTOM_SWITCHINGTYPE'] = $LGD_CUSTOM_SWITCHINGTYPE;	       // 신용카드 카드사 인증 페이지 연동 방식
	$payReqMap['LGD_WINDOW_VER'] = $LGD_WINDOW_VER;
	$payReqMap['LGD_ENCODING'] = $LGD_ENCODING; // 결제창 호출 문자 인코딩방식
	$payReqMap['LGD_ENCODING_NOTEURL'] = $LGD_ENCODING_NOTEURL; // 결과수신페이지 호출 문자 인코딩방식
	$payReqMap['LGD_ENCODING_RETURNURL'] = $LGD_ENCODING_RETURNURL; // 결과수신페이지 호출 문자 인코딩방식

	// 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
	$payReqMap['LGD_CASNOTEURL'] = $LGD_CASNOTEURL;               // 가상계좌 NOTEURL

	//Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
	$payReqMap['LGD_RESPCODE'] = "";
	$payReqMap['LGD_RESPMSG'] = "";
	$payReqMap['LGD_PAYKEY'] = "";

	/*추가 정보 (시작)*/
	$payReqMap['LGD_BUYERID'] = $LGD_BUYERID;                   //구매자 ID
	$payReqMap['LGD_BUYERIP'] = $LGD_BUYERIP;            	    // 구매자 IP
	$payReqMap['LGD_BUYERPHONE']=$LGD_BUYERPHONE;   	//구매자휴대폰번호
	$payReqMap['LGD_BUYEREMAIL'] = $LGD_BUYEREMAIL;       //구매자 이메일
	$payReqMap['LGD_BUYERADDRESS'] = $LGD_BUYERADDRESS;             //구매자주소  =>  (DB 저장 안함)

	$payReqMap['LGD_PRODUCTCODE'] = $LGD_PRODUCTCODE;              //상품코드  =>  (DB 저장 안함)

	$payReqMap['LGD_RECEIVER'] = $LGD_RECEIVER;                               //수취인  =>  (DB 저장 안함)
	$payReqMap['LGD_RECEIVERPHONE'] = $LGD_RECEIVERPHONE;             //수취인전화번호  =>  (DB 저장 안함)
	$payReqMap['LGD_DELIVERYINFO'] = $LGD_DELIVERYINFO;             //수취인주소  =>  (DB 저장 안함)

	$payReqMap['LGD_CLOSEDATE'] = $LGD_CLOSEDATE;      // 가상계좌 결제마감기간 (yyyyMMddHHmmss)  =>  (DB 저장 안함)

	if($LGD_ESCROW_USEYN=='Y'){ // 에스크로를 사용하는 경우  =>  (DB 저장 안함)
		// 상품정보가 복수개일 때 에스크로상품번호 ~ 에스크로상품수량  필드를 중복해서 사용 (5개의 에스크로 필드를 반드시 한쌍으로 적용)
		$payReqMap['LGD_ESCROW_GOODID'] = $LGD_ESCROW_GOODID;                              	// 에스크로상품번호
		$payReqMap['LGD_ESCROW_GOODNAME'] = $LGD_ESCROW_GOODNAME;            // 에스크로상품명
		$payReqMap['LGD_ESCROW_GOODCODE'] = $LGD_ESCROW_GOODCODE;                        	//	에스크로상품코드
		$payReqMap['LGD_ESCROW_UNITPRICE'] = $LGD_ESCROW_UNITPRICE;	                    // 에스크로상품 가격 (<= 상품합계금액으로 대체 함)
		$payReqMap['LGD_ESCROW_QUANTITY'] = $LGD_ESCROW_QUANTITY;	            // 에스크로상품수량 (<= 수량을 무조건 1로 대체 함)

		$payReqMap['LGD_ESCROW_ZIPCODE'] = $LGD_ESCROW_ZIPCODE;	   // 에스크로배송지우편번호
		$payReqMap['LGD_ESCROW_ADDRESS1'] = $LGD_ESCROW_ADDRESS1;                     //	에스크로배송지주소동까지
		$payReqMap['LGD_ESCROW_ADDRESS2'] = $LGD_ESCROW_ADDRESS2;	                //	에스크로배송지주소상세
		$payReqMap['LGD_ESCROW_BUYERPHONE'] = $LGD_ESCROW_BUYERPHONE;          //	에스크로구매자휴대폰번호
	}

	if($V['pay_how']=='EPN'){ // 간편결제 (Paynow) 전용창 요청
		$payReqMap['LGD_EASYPAY_ONLY'] = "PAYNOW";
		$payReqMap['LGD_MONEPAYAPPYN'] = "N";
	}
	/*추가 정보 (끝)*/

	$_SESSION['PAYREQ_MAP'] = $payReqMap;
?>

		<script language="javascript" src="http://xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js" type="text/javascript"></script>
		<script type="text/javascript">

		/*
		* 수정불가.
		*/
			var LGD_window_type = '<?php echo $LGD_WINDOW_TYPE ?>';
			
		/*
		* 수정불가
		*/
		function pay(){
			lgdwin = openXpay(document.getElementById('LGD_PAYINFO'), '<?php echo $CST_PLATFORM ?>', LGD_window_type, null, "", "");
		}
		/*
		* FORM 명만  수정 가능
		*/
		function getFormObject() {
				return document.getElementById("LGD_PAYINFO");
		}

		/*
		 * 인증결과 처리
		 */
		function payment_return() {
			var actionUrl="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/payment/lguplusXPay/Xpay_pay_ing.php";
			var fDoc;
			fDoc = lgdwin.contentWindow || lgdwin.contentDocument;
				
			if (fDoc.document.getElementById('LGD_RESPCODE').value == "0000") {
					document.getElementById("LGD_PAYKEY").value = fDoc.document.getElementById('LGD_PAYKEY').value;
					document.getElementById("LGD_PAYINFO").target = "_self";
					document.getElementById("LGD_PAYINFO").action = actionUrl;
					document.getElementById("LGD_PAYINFO").submit();
			} else {
				alert("LGD_RESPCODE (결과코드) : " + fDoc.document.getElementById('LGD_RESPCODE').value + "\n" + "LGD_RESPMSG (결과메시지): " + fDoc.document.getElementById('LGD_RESPMSG').value);
				closeIframe();
			}
		}

		</script>
		<form method="post" name="LGD_PAYINFO" id="LGD_PAYINFO" action="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/payment/lguplusXPay/Xpay_pay_ing.php">
		<?php
		  foreach ($payReqMap as $key => $value) {
			echo "<input type='hidden' name='".$key."' id='".$key."' value='".$value."'>";
		  }
		?>
		<!--// BBS e-Commerce 자체 필드-->
		<input type="hidden" name="LGD_MERTKEY" id="LGD_MERTKEY" value="<?php echo $LGD_MERTKEY;?>"> <!--상점MertKey -->
		<input type="hidden" name='pay_how' id='pay_how' value='<?php echo $V['pay_how']; ?>' />	<!-- 결제방법 -->
		<input type="hidden" name='Column2' id='Column2' value='<?php echo $deviceType; ?>' />	<!-- 디바이스 정보 -->
		<input type="hidden" name="orderList" id="orderList" value="<?php echo $orderList;?>">		<!-- 상품목록 -->
		<input type="hidden" name="CONFIG_PATH" id="CONFIG_PATH" value="<?php echo $CONFIG_PATH;?>">		<!-- LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf,/conf/mall.conf") 위치 지정 -->
		<input type="hidden" name="BBSE_COMMERCE_THEME_WEB_URL" id="BBSE_COMMERCE_THEME_WEB_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL;?>">
		</form>

<?php }?>

<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* 
* 네이버페이 연동
*/

if($payCFG['kakaopay']['kakaopay_escrow_yn']=='Y')$escrowYN='Y';
else $escrowYN='N';

$payHowTitle="간편결제 (<img src='".BBSE_COMMERCE_THEME_WEB_URL."/images/icon_npay.png' class='ezpay_icon' align='absmiddle' alt='네이버페이 결제' />)";

$ordr_idxx = $V['order_no'];

$goodsNames = array();
$npay_product = array();
$goodsImage="";
foreach($result as $gData) {
	$goodsNames[] = $gData->goods_name;

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
?>
<hr class="refresher">
  <main id="pageBody">
    <div class="subPage solo widthLimiter">
          <?php
          #로케이션
          get_template_part('part/sub', 'location');
          ?>
      <div class="page_cont mainContent blogView"  id="bbsePage<?php echo $bbsePage?>">
  			<h2 class="page_title stopTOP">주문확인/결제</h2>
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
					$GoodsCnt=0; // KakaoPay 전송 : 상품수량
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
								<del><?php echo ($customPriceView=='U')?number_format($cart->goods_consumer_price):"";?></del>
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
						$GoodsCnt +=$goods_total_cnt;
						
						$npay_product[]=array(
							'uid'	=> $cart->idx,
							'name'	=> $cart->goods_name,
							'cnt'	=> $goods_total_cnt,
						);
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
								<?php
								//$nPayData=bbse_nPay_check();
								//print_r($npay_product);
								$npay_new = array();
								foreach ($npay_product as $key => $value) {
									$npay_new []= array(
											'categoryType'=> 'ETC',
										    'categoryId' => 'ETC',
										    'uid' => $value['uid'],
										    'name' => $value['name'],
										    'count' => $value['cnt'],
									);
								}
								//development
								?>
								<div class="bb_btn_area">
									<script src="https://nsp.pay.naver.com/sdk/js/naverpay.min.js"
									    data-client-id="<?php echo get_option('bbse_npay_client_id'); ?>"
									    data-mode="production"
									    data-merchant-user-key="<?php echo $UserId; ?>"
									    data-merchant-pay-key="<?php echo $ordr_idxx; ?>"
									    data-product-name="<?php echo $good_name; ?>"
									    data-total-pay-amount="<?php echo $good_mny; ?>"
									    data-tax-scope-amount="<?php echo $good_mny; ?>"
									    data-tax-ex-scope-amount="0"
									    data-return-url="<?php echo BBSE_COMMERCE_THEME_WEB_URL.'/npay/bbse-npay-order2.exec.php/?order_list='.$orderList; ?>"
									    data-product-items = '<?php echo json_encode($npay_new); ?>'>
									</script>
									<button style="width: 150px;margin: 5px;" type="button" class="bb_btn cus_solid back-action"><strong class="big">주문취소</strong></button>
								</div>
							</div>
						</div>

					</div><!--//.bb_pay_right -->
				</div><!--//.payment_wrap -->
			</div>
			<?php }else{echo "BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.";} ?>
		</div>
	</div><!--//#content -->
		
		<form name="payForm" id="payForm" action="<?php echo BBSE_COMMERCE_THEME_WEB_URL.'/payment/KakaoPay/KakaoPay_pay_ing.php';?>"  method="post" accept-charset = "">
			<input type="hidden" name='order_id' id='order_id' value='<?php echo $ordr_idxx; ?>' />
			<input type="hidden" name="PayMethod" id="kakaopay" value="<?php echo($PayMethod); ?>" /><!--(필수)결제방법 : KAKAOPAY-->
			<input type="hidden" name="OFFER_PERIOD_FLAG" value="N" /><!--(선택)상품 제공기간 사용여부 : Y/N-->
			<input type="hidden" name="OFFER_PERIOD" value="" /><!--(선택)상품 제공기간 : 2015.06.01~2015.07.07-->
			<input type="hidden" name="TransType" value='<?php echo ($escrowYN=='Y')?"0":"0";?>' /><!--(필수)일반결제:0, 에스크로:1 (kakaopay는 에스크로 서비스 불가)-->
			<input type="hidden" name="GoodsName" value="<?php echo $good_name;?>" /><!--(필수)상품명-->
			<input type="hidden" name="Amt" value="<?php echo($Amt); ?>" /><!--(필수)상품가격-->
			<input type="hidden" name="GoodsCnt" value="<?php echo $GoodsCnt;?>"/><!--(필수)상품갯수-->
			<input type="hidden" name="MID" value="<?php echo($MID); ?>" /><!--(필수)가맹점아이디-->
			<!--TXN_ID를 가져오기 위해 사용하는 필수 변수 목록-->
			<input type="hidden" name="CERTIFIED_FLAG" id="CERTIFIED_FLAG" value="CN" /><!--(필수)CN : 웹결제, N : 인앱결제-->
			<input type="hidden" name="AuthFlg" id="AuthFlg" value="<?php echo($AuthFlg); ?>" /><!--(필수)인증구분 , 01 : key-in, ... 10 : kakaoPay => 카카오는 10으로 고정-->
			<input type="hidden" name="currency" value="<?php echo($currency); ?>" /><!--(필수)거래통화-->
			<input type="hidden" name="merchantEncKey" value="<?php echo($merchantEncKey); ?>" /><!--(필수)가맹점 암호화키-->
			<input type="hidden" name="merchantHashKey" value="<?php echo($merchantHashKey); ?>" /><!--(필수)가맹점 해쉬키-->
			<input type="hidden" name="requestDealApproveUrl" value="<?php echo($targetUrl.$msgName); ?>" /><!--(필수)TXN_ID 요청URL-->
			<input type="hidden" name ="prType" value="<?php echo ($V['deviceType']=='mobile' || $V['deviceType']=='tablet')?"MPM":"WPM";?>" /><!--(필수)WPM:WEB 결제(PC결제), MPM:Mobile 결제-->
			<input type="hidden" name ="channelType" value="<?php echo ($V['deviceType']=='mobile' || $V['deviceType']=='tablet')?"2":"4";?>" /><!--(필수)채널타입 => 모바일웹결제:2, TMS 방식:4-->
	
			<input type="hidden" name="merchantTxnNum" id="merchantTxnNum" value="13<?php echo($merchantTxnNum)?>" /><!--(필수)가맹점 거래번호-->

			<!--할부결제때 사용되는 선택변수 목록. 옳은 값들을 넣지 않으면 무이자를 사용하지 않는것으로 한다.-->
			<!-- 결제가능카드설정 (결제가능한 수단 제한 가능) 금지카드설정X 공백 !!홈쇼핑에서 필수항목  -->
			<input type="hidden" name="possiCard" id="possiCard" value="" /> <!--(선택)카드선택=>'':나중에 선택하기,01:비씨,02:국민,03:외환,04:삼성,06:신한,07:현대,08:롯데,11:한미,11:씨티,12:NH채움(농협),13:수협,13:신협,15:우리,16:하나SK,18:주택,19:조흥(강원),21:광주,22:전북,23:제주,25:해외비자,26:해외마스터,27:해외다이너스,28:해외AMX,29:해외JCB,30:해외디스커버,34:은련-->

			<!-- 고정할부개월 (00,01 (일시불), 02 ~24, 36 해당숫자로 할부가능) -->
			<input type="hidden" name="fixedInt" id="fixedInt" value="" /><!--(선택)할부개월=>'':나중에 선택하기,00:일시불,01:1개월,02:2개월,03:3개월,04:4개월,05:5개월,06:6개월,07:7개월,08:8개월,09:9개월,10:10개월,11:11개월,12:12개월,13:13개월,14:14개월,15:15개월,16:16개월,17:17개월,18:18개월,19:19개월,20:20개월,21:21개월,22:22개월,23:23개월,24:24개월,36:36개월-->
			<!-- 최대 할부개월 "":전체개월 선택가능 , ex) 06 : 1~6 개월 선택 가능 -->
			<input type="hidden" name="maxInt" id="maxInt" value="" /><!--(선택)최대할부개월=>'':선택안함,01:1개월,02:2개월,03:3개월,04:4개월,05:5개월,06:6개월,07:7개월,08:8개월,09:9개월,10:10개월,11:11개월,12:12개월,13:13개월,14:14개월,15:15개월,16:16개월,17:17개월,18:18개월,19:19개월,20:20개월,21:21개월,22:22개월,23:23개월,24:24개월,36:36개월-->
			<input type="hidden" name="noIntYN" id="noIntYN" value="N" /><!--(선택)무이자 사용여부=>Y:사용,N:사용안함-->

			<!-- 결제수단코드 + 카드코드 + - + 무이자 개월 ex) CC01-02:03:05:09  -->
			<input type="hidden" name="noIntOpt" id="noIntOpt" value="" /><!--(선택)무이자 옵션-->
			<input type="hidden" name ="pointUseYn" value="N" /><!--(선택)카드사포인트사용여부=>N:카드사 포인트 사용안함, Y:카드사 포인트 사용-->
			<input type="hidden" name="blockCard" value=""/><!--(선택)금지카드설정-->


			<!--가맹점 내에서 활용할 기타 변수 목록-->
			<input type="hidden" name="BuyerEmail" value="<?php echo $UserEmail;?>"/><!--(선택)구매자 이메일-->
			<input type="hidden" name="BuyerName" value="<?php echo $OrdNm;?>"/><!--(필수)구매자명-->


			<!-- MPay에서 TXN_ID 를 가져 올 때 함께 받아오는 변수 목록 -->
			<input type="hidden" name="resultCode" id="resultCode" value=""/><!--(자동 저장)resultcod-->
			<input type="hidden" name="resultMsg" id="resultMsg" value=""/><!--(자동 저장)resultmsg-->
			<input type="hidden" name="txnId" id="txnId" value=""/><!--(자동 저장)txnId-->
			<input type="hidden" name="prDt" id="prDt" value=""/><!--(자동 저장)prDt-->

			<!-- TODO : DLP창으로부터 받은 결과값을 SETTING 할 INPUT LIST -->
			<input type="hidden" name="SPU" value=""/><!--(자동 저장)SPU-->
			<input type="hidden" name="SPU_SIGN_TOKEN" value=""/><!--(자동 저장)SPU_SIGN_TOKEN-->
			<input type="hidden" name="MPAY_PUB" value=""/><!--(자동 저장)MPAY_PUB-->
			<!-- 부인방지 토큰 / RESULT_CODE == 00일 때는 항상 들어오는 값. -->
			<!-- 해당값은 가군인증을 위해 돌려주는 값으로서, 가맹점과 카카오페이 양측에서 저장하고 있어야 한다. -->
			<input type="hidden" name="NON_REP_TOKEN" value=""/><!--(자동 저장)NON_REP_TOKEN-->

			<input type="hidden" name="EdiDate" value="<?php echo($ediDate); ?>"/>
			<input type="hidden" name="EncryptData" value="<?php echo($hash_String); ?>"/>

			<!-- 추가 전달 변수 -->
			<input type="hidden" name="orderList" id="orderList" value="<?php echo $orderList;?>">		<!-- 상품목록 -->
			<input type="hidden" name="BBSE_COMMERCE_THEME_WEB_URL" id="BBSE_COMMERCE_THEME_WEB_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>">
			<input type="hidden" name='Column2' id='Column2' value='<?php echo $deviceType; ?>' />	<!-- 디바이스 정보 -->
			<input type="hidden" name='Column3' id='Column3' value='<?php echo $ordr_idxx; ?>' />	<!-- $ordr_idxx -->
		</form>
		<!-- TODO :  LayerPopup의 Target DIV 생성 -->
		<div id="kakaopay_layer"  style="display: none"></div>

  </main>
<hr class="refresher">
<div style="clear:both;margin-bottom:100px;"></div>


<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* 
* KG Inicis PG (이니시스) 웹표준 연동 - 데스크탑 만 사용
*/

if($payCFG['payment_inicis_escorw_use']=='on'){
	if($V['pay_how']=='C') $escrowYN='N';
	elseif(($V['pay_how']=='K' && $payCFG['payment_inicis_escorw_trans']=='on') || 
		($V['pay_how']=='V' && $payCFG['payment_inicis_escorw_vbank']=='on') ) $escrowYN='Y';
	else $escrowYN='N';
}
else $escrowYN='N';

if($escrowYN=='Y'){
	$StoreId = $payCFG['payment_inicis_escorw_id'];     //에스크로 상점아이디
	$paymentSignKey=$payCFG['payment_escrow_sign_key'];
}
else $paymentSignKey=$payCFG['payment_sign_key'];

if($V['pay_how']=='C'){
	$payHow="Card"; // PC 결제방법(onlycard:신용카드 결제(전용메뉴) , onlydbank:실시간 은행계좌이체 (전용메뉴) , onlyvbank:무통장입금(가상계좌 전용메뉴)
	$payHowTitle="신용카드";
}
elseif($V['pay_how']=='K'){
	$payHow="DirectBank";
	$payHowTitle="실시간 계좌이체";
}
elseif($V['pay_how']=='V'){
	$payHow="Vbank";
	$payHowTitle="가상계좌";
}

$ordr_idxx = $V['order_no'];

$goodsNames = array();

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

$Remark=$V['order_comment']; //기타요구사항
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
						$delivery_price = $deliveryInfo['delivery_charge'];
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
									<button type="button" class="bb_btn cus_fill pay-action" onClick="pay(document.ini);"><strong class="big">결제하기</strong></button>
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
require_once(BBSE_COMMERCE_THEME_ABS_PATH.'/payment/INIpayStandard/libs/INIStdPayUtil.php');
$SignatureUtil = new INIStdPayUtil();
/*
  //*** 위변조 방지체크를 signature 생성 ***
  oid, price, timestamp 3개의 키와 값을
  key=value 형식으로 하여 '&'로 연결한 하여 SHA-256 Hash로 생성 된값
  ex) oid=INIpayTest_1432813606995&price=819000&timestamp=2012-02-01 09:19:04.004
 * key기준 알파벳 정렬
 * timestamp는 반드시 signature생성에 사용한 timestamp 값을 timestamp input에 그대로 사용하여야함
 */

//############################################
// 1.전문 필드 값 설정(***가맹점 개발수정***)
//############################################
// 여기에 설정된 값은 Form 필드에 동일한 값으로 설정
$mid = $StoreId;  // 가맹점 ID(가맹점 수정후 고정)					
//인증
$signKey = $paymentSignKey; // 가맹점에 제공된 웹 표준 사인키(가맹점 수정후 고정)
$timestamp = $SignatureUtil->getTimestamp();   // util에 의해서 자동생성

//$cardNoInterestQuota = "11-2:3:,34-5:12,14-6:12:24,12-12:36,06-9:12,01-3:4";  // 카드 무이자 여부 설정(가맹점에서 직접 설정)
$cardQuotaBase = "2:3:4:5:6:11:12:24:36";  // 가맹점에서 사용할 할부 개월수 설정

//###################################
// 2. 가맹점 확인을 위한 signKey를 해시값으로 변경 (SHA-256방식 사용)
//###################################
$mKey = $SignatureUtil->makeHash($signKey, "sha256");

$params = array(
    "oid" => $ordr_idxx,
    "price" => $good_mny,
    "timestamp" => $timestamp
);
$sign = $SignatureUtil->makeSignature($params, "sha256");

/* 기타 */
$siteDomain = BBSE_COMMERCE_THEME_WEB_URL."/payment/INIpayStandard/result"; //가맹점 도메인 입력
?>
	<!-- 이니시스 표준결제 js -->
	<script language="javascript" type="text/javascript" src="https://stdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script>
	<!--script language="javascript" type="text/javascript" src="HTTPS:/stgstdpay.inicis.com/stdjs/INIStdPay.js" charset="UTF-8"></script-->
	<script type="text/javascript">
		function pay(frm) {
			INIStdPay.pay('inicisFrm');
		}
	</script>
	<form name="inicisFrm" id="inicisFrm" method="post" action="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/payment/INIpay50/result/INI_pay_ing.php"> 
	<!-- 필수 -->

		<input type="hidden" name="version" value="1.0" />
		<input type="hidden" name="mid" value="<?php echo $mid;?>" />
		<input type="hidden" name="goodname" id="goodsname" value="<?php echo $good_name;?>" /><!--상 품 명-->
		<input type="hidden" name="oid" id="oid" value="<?php echo $ordr_idxx;?>" /><!--주문번호-->
		<input type="hidden" name="price" value="<?php echo $good_mny;?>" /><!--결제금액-->
		<input type="hidden" name="currency" value="WON" /><!-- 화폐단위 -->
		<input type="hidden" name="buyername" value="<?php echo $OrdNm;?>" /><!--성 명-->
		<input type="hidden" name="buyertel" value="<?php echo $OrdPhone;?>" /><!--이 동 전 화-->
		<input type="hidden" name="buyeremail" value="<?php echo $UserEmail;?>" /><!--전 자 우 편-->
		<input type="hidden" name="timestamp" value="<?php echo $timestamp;?>" /><!--시간 : timestamp-->
		<input type="hidden" name="signature" value="<?php echo $sign;?>" /><!--Signature : SHA256-->
		<input type="hidden" name="returnUrl" value="<?php echo $siteDomain;?>/INIStd_pay_ing.php" /><!--return url-->
		<input type="hidden"  name="mKey" value="<?php echo $mKey;?>" /><!--구) mertKey-->

	<?php if($escrowYN=='Y'){?>
		<input type="hidden" name="useescrow" value="useescrow" ><!--에스크로 적용여부-->
	<?php }?>
	<!-- 기본 옵션 -->
		<!--Card (계약 결제 수단이 존재하지 않을 경우 에러로 리턴) : 사용 가능한 입력 값 : Card,DirectBank,HPP,Vbank,kpay,Swallet,Paypin,EasyPay,PhoneBill,GiftCard,EWallet, onlypoint,onlyocb,onyocbplus,onlygspt,onlygsptplus,onlyupnt,onlyupntplus-->
		<input type="hidden" name="gopaymethod" value="<?php echo $payHow;?>" />
        <input type="hidden" name="offerPeriod" value="" /> <!--제공기간 ex)20150101-20150331, [Y2:년단위결제, M2:월단위결제, yyyyMMdd-yyyyMMdd : 시작일-종료일]-->
		<?php
		/*
		SKIN : 플러그인 스킨 칼라 변경 기능 - 6가지 칼라(ORIGINAL, GREEN, ORANGE, BLUE, KAKKI, GRAY)
		HPP : 컨텐츠 또는 실물 결제 여부에 따라 HPP(1)과 HPP(2)중 선택 적용(HPP(1):컨텐츠, HPP(2):실물).
		Card(0): 신용카드 지불시에 이니시스 대표 가맹점인 경우에 필수적으로 세팅 필요 ( 자체 가맹점인 경우에는 카드사의 계약에 따라 설정) - 자세한 내용은 메뉴얼  참조.
		OCB : OK CASH BAG 가맹점으로 신용카드 결제시에 OK CASH BAG 적립을 적용하시기 원하시면 "OCB" 세팅 필요 그 외에 경우에는 삭제해야 정상적인 결제 이루어짐.
		no_receipt : 은행계좌이체시 현금영수증 발행여부 체크박스 비활성화 (현금영수증 발급 계약이 되어 있어야 사용가능)
		*/
		?>
		<input type="hidden" name="acceptmethod" value="HPP(2):Card(0):OCB:receipt:cardpoint" />

	<!--표시 옵션-->
		<input type="hidden" name="languageView" value="ko" /><!--초기 표시 언어 : [ko|en] (default:ko)-->
		<input type="hidden" name="charset" value="UTF-8" /><!--리턴 인코딩 : [UTF-8|EUC-KR] (default:UTF-8)-->
		<input type="hidden" name="payViewType" value="overlay" ><!--결제창 표시방법:[overlay|popup] (default:overlay)-->
		<?php
		/*
		[closeUrl]
		payViewType='overlay','popup'시 취소버튼 클릭시 창닫기 처리 URL(가맹점에 맞게 설정) 
		close.php 샘플사용(생략가능, 미설정시 사용자에 의해 취소 버튼 클릭시 인증결과 페이지로 취소 결과를 보냄.)
		*/
		?>
		<input type="hidden" name="closeUrl" value="<?php echo $siteDomain;?>/close.php" />

		<?php
		/*
		[popupUrl]
		payViewType='popup'시 팝업을 띄울수 있도록 처리해주는 URL(가맹점에 맞게 설정)
        popup.php 샘플사용(생략가능,payViewType='popup'으로 사용시에는 반드시 설정)
		*/
		?>
		<input type="hidden" name="popupUrl" value="<?php echo $siteDomain;?>/popup.php" />
	
	<!--결제 수단별 옵션-->
		<input type="hidden" name="nointerest" value="<?php echo $cardNoInterestQuota;?>" /><!--카드 : 무이자 할부 개월 ex) 11-2:3:4,04-2:3:4-->
		<input type="hidden" name="quotabase" value="<?php echo $cardQuotaBase;?>" /><!--카드 : 할부 개월 ex) 2:3:4-->	
		<input type="hidden" name="vbankRegNo" value="" ><!--가상계좌 : 주민번호 설정 기능 : 13자리(주민번호),10자리(사업자번호),미입력시(화면에서입력가능)-->

	<!-- 추가 전달 변수 -->
		<input type="hidden" name="merchantData" value="<?php echo base64_encode($orderList);?>" /><!--상품목록 => 가맹점 관리데이터(1000byte) : 인증결과 리턴시 함께 전달됨-->
	</form>


  </main>
<hr class="refresher">
<div style="clear:both;margin-bottom:100px;"></div>


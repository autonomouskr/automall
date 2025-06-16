<?php
/*
나이스 PG 추가
*/
if($V['pay_how']=='C'){
	$payHow="CARD";
	$payHowTitle="신용카드";
}
elseif($V['pay_how']=='K'){
	$payHow="BANK";
	$payHowTitle="실시간 계좌이체";
}
elseif($V['pay_how']=='V'){
	$payHow="VBANK";
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
									<button type="button" class="bb_btn cus_fill pay-action" onClick="nicepayStart();"><strong class="big">결제하기</strong></button>
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
/*
*******************************************************
* <결제요청 파라미터>
* 결제시 Form 에 보내는 결제요청 파라미터입니다.
* 샘플페이지에서는 기본(필수) 파라미터만 예시되어 있으며, 
* 추가 가능한 옵션 파라미터는 연동메뉴얼을 참고하세요.
*******************************************************
*/  

$merchantKey = $payCFG['payment_sign_key']; // 상점키
$MID         = $payCFG['payment_id'];//"nicepay00m"; // 상점아이디

$goodsName   = $good_name; // 결제상품명
$price       = $good_mny; // 결제상품금액
$buyerName   = $OrdNm; // 구매자명 
$buyerTel	 = $OrdPhone; // 구매자연락처
$buyerEmail  = $V['order_email']; // 구매자메일주소        
$moid        = $ordr_idxx; // 상품주문번호                     
$returnURL	 = BBSE_COMMERCE_THEME_WEB_URL."/payment/nice/payResult_utf.php"; // 결과페이지(절대경로) - 모바일 결제창 전용

/*
*******************************************************
* <해쉬암호화> (수정하지 마세요)
* SHA-256 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
*******************************************************
*/ 
$ediDate = date("YmdHis");
$hashString = bin2hex(hash('sha256', $ediDate.$MID.$price.$merchantKey, true));
?>
<!-- 아래 js는 PC 결제창 전용 js입니다.(모바일 결제창 사용시 필요 없음) -->
<script src="https://web.nicepay.co.kr/v3/webstd/js/nicepay-3.0.js" type="text/javascript"></script>
<script type="text/javascript">
//결제창 최초 요청시 실행됩니다.
function nicepayStart(){
	if(checkPlatform(window.navigator.userAgent) == "mobile"){//모바일 결제창 진입
		document.payForm.action = "https://web.nicepay.co.kr/v3/v3Payment.jsp";
		document.payForm.submit();
	}else{//PC 결제창 진입
		goPay(document.payForm);
	}
}

//[PC 결제창 전용]결제 최종 요청시 실행됩니다. <<'nicepaySubmit()' 이름 수정 불가능>>
function nicepaySubmit(){
	document.payForm.submit();
}

//[PC 결제창 전용]결제창 종료 함수 <<'nicepayClose()' 이름 수정 불가능>>
function nicepayClose(){
	alert("결제가 취소 되었습니다");
}

//pc, mobile 구분(가이드를 위한 샘플 함수입니다.)
function checkPlatform(ua) {
	if(ua === undefined) {
		ua = window.navigator.userAgent;
	}
	
	ua = ua.toLowerCase();
	var platform = {};
	var matched = {};
	var userPlatform = "pc";
	var platform_match = /(ipad)/.exec(ua) || /(ipod)/.exec(ua) 
		|| /(windows phone)/.exec(ua) || /(iphone)/.exec(ua) 
		|| /(kindle)/.exec(ua) || /(silk)/.exec(ua) || /(android)/.exec(ua) 
		|| /(win)/.exec(ua) || /(mac)/.exec(ua) || /(linux)/.exec(ua)
		|| /(cros)/.exec(ua) || /(playbook)/.exec(ua)
		|| /(bb)/.exec(ua) || /(blackberry)/.exec(ua)
		|| [];
	
	matched.platform = platform_match[0] || "";
	
	if(matched.platform) {
		platform[matched.platform] = true;
	}
	
	if(platform.android || platform.bb || platform.blackberry
			|| platform.ipad || platform.iphone 
			|| platform.ipod || platform.kindle 
			|| platform.playbook || platform.silk
			|| platform["windows phone"]) {
		userPlatform = "mobile";
	}
	
	if(platform.cros || platform.mac || platform.linux || platform.win) {
		userPlatform = "pc";
	}
	
	return userPlatform;
}
</script>
<form name="payForm" method="post" action="<?php echo($returnURL)?>">
	<input type="hidden" name="PayMethod" value="<?php echo $payHow ; ?>"><!--결제수단 -->
	<input type="hidden" name="GoodsName" value="<?php echo($goodsName)?>"><!-- 결제 상품명 -->
	<input type="hidden" name="Amt" value="<?php echo($price)?>"><!--상품금액 -->
	<input type="hidden" name="MID" value="<?php echo($MID)?>"> <!--상점아이디-->
	
	<input type="hidden" name="Moid" value="<?php echo($moid)?>"><!--주문번호-->
	<input type="hidden" name="BuyerName" value="<?php echo($buyerName)?>"><!--주문자명-->
	<input type="hidden" name="BuyerEmail" value="<?php echo($buyerEmail)?>"><!--주문자이메일-->
	<input type="hidden" name="BuyerTel" value="<?php echo($buyerTel)?>"><!--주문자연락처-->
	<input type="hidden" name="ReturnURL" value="<?php echo($returnURL)?>"><!--결과URL-->
	<input type="hidden" name="VbankExpDate" value=""><!--가상계좌입금만료일-->
			
	<!-- 옵션 -->	 
	<input type="hidden" name="GoodsCl" value="1"/>						<!-- 상품구분(실물(1),컨텐츠(0)) -->
	<input type="hidden" name="TransType" value="0"/>					<!-- 일반(0)/에스크로(1) --> 
	<input type="hidden" name="CharSet" value="utf-8"/>				<!-- 응답 파라미터 인코딩 방식 -->
	
	<!-- 변경 불가능 -->
	<input type="hidden" name="EdiDate" value="<?php echo($ediDate)?>"/>			<!-- 전문 생성일시 -->
	<input type="hidden" name="SignData" value="<?php echo($hashString)?>"/>	<!-- 해쉬값 -->
	
	<input type="hidden" name="ReqReserved" value="<?php echo base64_encode($orderList);?>" />
</form>
<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* 
* KG Inicis PG (이니시스) 연동
*/

if($V['pay_how']=='EKP'){
	if($payCFG['kpay']['kpay_escrow_yn']=='Y'){
		$escrowYN='Y';
		$StoreId = $payCFG['kpay']['kpay_escrow_mert_id'];     //상점아이디 (테스트 : INIpayTest)
		$StorePw = $payCFG['kpay']['kpay_escrow_key_pw'];     //상점비밀번호 (테스트 : 1111)
		$KeyPath = $payCFG['kpay']['kpay_escrow_key_path'];     //키파일 경로
	}
	else{
		$escrowYN='N';
		$StorePw = $payCFG['kpay']['kpay_key_pw'];     //상점비밀번호 (테스트 : 1111)
		$KeyPath = $payCFG['kpay']['kpay_key_path'];     //키파일 경로
	}
}
else{
	if($payCFG['payment_inicis_escorw_use']=='on'){
		if($V['pay_how']=='C') $escrowYN='N';
		elseif(($V['pay_how']=='K' && $payCFG['payment_inicis_escorw_trans']=='on') || 
			($V['pay_how']=='V' && $payCFG['payment_inicis_escorw_vbank']=='on') ) $escrowYN='Y';
		else $escrowYN='N';
	}
	else $escrowYN='N';

	if($escrowYN=='Y'){
		$StoreId = $payCFG['payment_inicis_escorw_id'];     //상점아이디 (테스트 : INIpayTest)
		$StorePw = $payCFG['payment_inicis_escorw_key_pw'];     //상점비밀번호 (테스트 : 1111)
		$KeyPath = $payCFG['payment_inicis_escorw_key_path'];     //키파일 경로
	}else{
		$StorePw = $payCFG['payment_key_pw'];     //상점비밀번호 (테스트 : 1111)
		$KeyPath = $payCFG['payment_key_path'];     //키파일 경로
	}
}

if($V['deviceType']=='mobile' || $V['deviceType']=='tablet'){
	if($V['pay_how']=='C'){
		$payHow="wcard"; // 모바일 결제방법(wcard:신용카드 결제 , vbank:무통장입금(가상계좌)
		$payHowTitle="신용카드";
	}
	elseif($V['pay_how']=='V'){
		$payHow="vbank";
		$payHowTitle="가상계좌";
	}
	elseif($V['pay_how']=='EKP'){
		$payHow="onlykpay";
		$payHowTitle="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kpay.png' class='ezpay_icon' align='absmiddle' alt='KPAY 결제' />)";
	}
}
else{
	if($V['pay_how']=='C'){
		$payHow="onlycard"; // PC 결제방법(onlycard:신용카드 결제(전용메뉴) , onlydbank:실시간 은행계좌이체 (전용메뉴) , onlyvbank:무통장입금(가상계좌 전용메뉴)
		$payHowTitle="신용카드";
	}
	elseif($V['pay_how']=='K'){
		$payHow="onlydbank";
		$payHowTitle="실시간 계좌이체";
	}
	elseif($V['pay_how']=='V'){
		$payHow="onlyvbank";
		$payHowTitle="가상계좌";
	}
	elseif($V['pay_how']=='EKP'){
		$payHow="onlykpay";
		$payHowTitle="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kpay.png' class='ezpay_icon' align='absmiddle' alt='KPAY 결제' />)";
	}
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
if($V['deviceType']=='mobile' || $V['deviceType']=='tablet'){

	if($payHow=='wcard') $paraData="twotrs_isp=Y&block_isp=Y&twotrs_isp_noti=N";
	elseif($payHow=='onlykpay'){
		$paraData="d_kpay=Y"; // 동기방식
		$_SESSION['P_OID'] = $ordr_idxx; //주문번호 저장
		$_SESSION['P_UNAME'] = $OrdNm; //구매자이름
	}
	elseif($payHow=='vbank') $paraData="vbank_receipt=Y";
	else $paraData="";
?>
	<script type="application/x-javascript">
		addEventListener("load", function(){
			setTimeout(updateLayout, 0);
		}, false);
	 
		var currentWidth = 0;
		
		function updateLayout(){
			if (window.innerWidth != currentWidth)
			{
				currentWidth = window.innerWidth;
	 
				var orient = currentWidth == 320 ? "profile" : "landscape";
				document.body.setAttribute("orient", orient);
				setTimeout(function()
				{
					window.scrollTo(0, 1);
				}, 100);            
			}
		}
	 
		setInterval(updateLayout, 400);
	</script>

	<script language=javascript>
	function on_web(){
		var order_form = document.ini;
		var kpayCheck = "<?php echo $payHow;?>";
		var paymethod = "";
		if(kpayCheck=='onlykpay') paymethod = "wcard";
		else paymethod = order_form.paymethod.value;

		// 신용카드 : https://mobile.inicis.com/smart/wcard/, 계좌이체 : https://mobile.inicis.com/smart/bank/, 가상계좌 : https://mobile.inicis.com/smart/vbank/
		order_form.action = "https://mobile.inicis.com/smart/" + paymethod + "/";
		order_form.submit();
	}

	function pay(order_form){
		var inipaymobile_type = order_form.inipaymobile_type.value;
	  if( inipaymobile_type == "web" ){
			return on_web();
	  }
	}
	</script>
	<form name="ini" method="post" accept-charset="euc-kr" action="" >
		<input type="hidden" name="P_MID" value="<?php echo $StoreId;?>" /><!--상점아이디-->
		<input type="hidden" name="paymethod" id="paymethod" value="<?php echo $payHow;?>" /><!--결제방법(신용카드:wcard, 가상계좌:vbank)-->
		<input type="hidden" name="inipaymobile_type" id="inipaymobile_type" value="web" /><!--방식-->
		<input type="hidden" name="P_OID" id="P_OID" value="<?php echo $ordr_idxx; ?>" /><!--주문번호-->
		<input type="hidden" name="P_GOODS" id="P_GOODS" value="<?php echo $good_name;?>" /><!--상품명-->
		<input type="hidden" name="P_AMT" id="P_AMT" value="<?php echo $good_mny;?>" /><!--가격-->
		<input type="hidden" name="P_UNAME" id="P_UNAME" value="<?php echo $OrdNm; ?>" /><!--구매자이름-->
		<input type="hidden" name="P_MNAME" id="P_MNAME" value="<?php echo get_bloginfo('name'); ?>" /><!--상점이름-->
		<input type="hidden" name="P_MOBILE" id="P_MOBILE" value="<?php echo $OrdPhone; ?>" /><!--휴대폰번호-->
		<input type="hidden" name="P_EMAIL" id="P_EMAIL" value="<?php echo $UserEmail; ?>" /><!--이메일-->
		<input type="hidden" name="P_RESERVED" value="<?php echo $paraData;?>" /><!--신용카드 필수옵션, 가상계좌 현금영수증, Kpay 사용여부-->
	<?php if($payHow=='onlykpay'){?>
		<input type="hidden" name="P_NOTI_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL."/payment/INIpay50/result/INI_pay_ing_mobile_kpay_notice.php";?>" /><!--모바일에서 kpay로 결제하는 경우 결제정보 수신 URL(1 Transaction)-->
		<input type="hidden" name="P_RETURN_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL."/payment/INIpay50/result/INI_pay_ing_mobile_kpay_async.php";?>" /><!--모바일에서 kpay로 결제가 완료 된 경우 이동 페이지(1 Transaction)-->
	<?php }else{?>
		<?php if($payHow=='vbank'){?>
		<input type="hidden" name="P_NOTI_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL."/payment/INIpay50/result/INI_pay_notice_mobile.php";?>" /><!--모바일에서 결제하는 경우 결제정보 수신 URL(1 Transaction)-->
		<?php }?>
		<?php if($escrowYN=='Y'){?>
		<input type="hidden" name="P_NEXT_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL."/payment/INIpay50/result/INI_pay_ing_mobile_escrow.php";?>" /><!--사용자의 인증이 완료될 때, 이 Url 로 인증결과를 전달(2 transaction 방식)-->
		<?php }else{?>
		<input type="hidden" name="P_NEXT_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL."/payment/INIpay50/result/INI_pay_ing_mobile.php";?>" /><!--사용자의 인증이 완료될 때, 이 Url 로 인증결과를 전달(2 transaction 방식)-->
		<?php }?>
	<?php }?>
		<input type="hidden" name="P_HPP_METHOD" value="2" /><!--실물여부 구분 (컨텐츠 일 경우 : 1, 실물일 경우 : 2)  - 컨텐츠/실물 여부는 계약담당자에게 확인요-->
	 </form>
<?php 
}
else{

	/* * ************************
	 * 1. 라이브러리 인클루드 *
	 * ************************ */
	require(BBSE_COMMERCE_THEME_ABS_PATH."/payment/INIpay50/libs/INILib.php");

	/* * *************************************
	 * 2. INIpay50 클래스의 인스턴스 생성  *
	 * ************************************* */
	$inipay = new INIpay50;

	/* * ************************
	 * 3. 암호화 대상/값 설정 *
	 * ************************ */
	$inipay->SetField("inipayhome", $KeyPath);       // 이니페이 홈디렉터리(상점수정 필요)
	$inipay->SetField("type", "chkfake");      // 고정 (절대 수정 불가)
	$inipay->SetField("debug", "true");        // 로그모드("true"로 설정하면 상세로그가 생성됨.)
	$inipay->SetField("enctype", "asym");    //asym:비대칭, symm:대칭(현재 asym으로 고정)
	/* * ************************************************************************************************
	 * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
	 * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
	 * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
	 * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
	 * ************************************************************************************************ */
	$inipay->SetField("admin", $StorePw);     // 키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)
	$inipay->SetField("checkopt", "false");   //base64함:false, base64안함:true(현재 false로 고정)
	//필수항목 : mid, price, nointerest, quotabase
	//추가가능 : INIregno, oid
	//*주의* : 	추가가능한 항목중 암호화 대상항목에 추가한 필드는 반드시 hidden 필드에선 제거하고 
	//          SESSION이나 DB를 이용해 다음페이지(INIsecureresult.php)로 전달/셋팅되어야 합니다.
	$inipay->SetField("mid", $StoreId);            // 상점아이디
	$inipay->SetField("price", $good_mny);                // 가격
	$inipay->SetField("nointerest", "no");             //무이자여부(no:일반, yes:무이자)
	$inipay->SetField("quotabase", "선택:lumpsum:00:02:03:06"); //할부기간 : 일시불:2개월:3개월:6개월
	/* * ******************************
	 * 4. 암호화 대상/값을 암호화함 *
	 * ****************************** */
	$inipay->startAction();

	/* * *******************
	 * 5. 암호화 결과  *
	 * ******************* */
	if ($inipay->GetResult("ResultCode") != "00") {
?>
		<script>
			jQuery(document).ready(function() {
				var errorCode="<?php echo $inipay->GetResult('ResultCode');?>";

				alert("결제오류 ! [Error Code : "+errorCode+"]\n상점정보 > 환경설정 및 KG 이니시스 키관리 설정을 확인해 주세요.  ");
				location.href=common_var.home_url;
			});
		</script>
<?php
	}

	/* * *******************
	 * 6. 세션정보 저장  *
	 * ******************* */
	$_SESSION['INI_MID'] = $StoreId; //상점ID
	$_SESSION['INI_ADMIN'] = $StorePw;   // 키패스워드(키발급시 생성, 상점관리자 패스워드와 상관없음)
	$_SESSION['INI_PRICE'] = $good_mny;     //가격 
	$_SESSION['INI_RN'] = $inipay->GetResult("rn"); //고정 (절대 수정 불가)
	$_SESSION['INI_ENCTYPE'] = $inipay->GetResult("enctype"); //고정 (절대 수정 불가)
?>
	<!-------------------------------------------------------------------------------
	* 웹SITE 가 https를 이용하면 https://plugin.inicis.com/pay61_secunissl_cross.js 사용 
	* 웹SITE 가 Unicode(UTF-8)를 이용하면 http://plugin.inicis.com/pay61_secuni_cross.js 사용
	* 웹SITE 가 https, unicode를 이용하면 https://plugin.inicis.com/pay61_secunissl_cross.js 사용  
	-------------------------------------------------------------------------------->
	<script language=javascript src="http://plugin.inicis.com/pay61_secuni_cross.js"></script> 
	<script language=javascript>
		 StartSmartUpdate();
		var openwin;

		function pay(frm){
			// MakePayMessage()를 호출함으로써 플러그인이 화면에 나타나며, Hidden Field
			// 에 값들이 채워지게 됩니다. 일반적인 경우, 플러그인은 결제처리를 직접하는 것이
			// 아니라, 중요한 정보를 암호화 하여 Hidden Field의 값들을 채우고 종료하며,
			// 다음 페이지인 INIsecureresult.php로 데이터가 포스트 되어 결제 처리됨을 유의하시기 바랍니다.

			if (document.ini.clickcontrol.value == "enable"){

				if (ini_IsInstalledPlugin() == false){ //플러그인 설치유무 체크
					alert("\n이니페이 플러그인 128이 설치되지 않았습니다. \n\n안전한 결제를 위하여 이니페이 플러그인 128의 설치가 필요합니다. \n\n다시 설치하시려면 Ctrl + F5키를 누르시거나 메뉴의 [보기/새로고침]을 선택하여 주십시오.");
					return false;
				}
				else {
					if (MakePayMessage(frm)) {
						jQuery("#quick").css("z-index", "-1");
						var preload = '<div id="paying_view" class="modal"><p class="pay-process"><img src="'+common_var.goods_template_url+'/payment/INIpay50/result/img/progress.gif"><br/><br/>결제 처리중입니다. 잠시만 기다려주세요.</p></div>';
						jQuery("#content").append(preload);
						jQuery("#paying_view").modal({
							escapeClose: false,
							clickClose: false,
							showClose: false
						});

						frm.submit();
					} else {
						if (IsPluginModule()) {//plugin타입 체크
							alert("결제를 취소하셨습니다.");
						}
						return false;
					}
				}
			}
			else{
				return false;
			}
		}

		function enable_click(){
			document.ini.clickcontrol.value = "enable"
		}

		function disable_click(){
			document.ini.clickcontrol.value = "disable"
		}

		function focus_control(){
			if (document.ini.clickcontrol.value == "disable"){
				openwin.focus();
			}
		}
	</script>


	<script language="JavaScript" type="text/JavaScript">
		<!--
		function MM_reloadPage(init) {  //reloads the window if Nav4 resized
		if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
		document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
		else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
		}
		MM_reloadPage(true);

		function MM_jumpMenu(targ,selObj,restore){ //v3.0
		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
		if (restore) selObj.selectedIndex=0;
		}
		//-->
	</script>

	<form name="ini" method="post" action="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/payment/INIpay50/result/INI_pay_ing.php"> 
		<input type="hidden" name="gopaymethod" id="ini_gopaymethod" value="<?php echo $payHow;?>" /> <!--결 제 방 법(onlycard:신용카드 결제(전용메뉴) , onlydbank:실시간 은행계좌이체 (전용메뉴) , onlyvbank:무통장입금(전용메뉴) -->
		<input type="hidden" name="goodname" id="ini_goodname" value="<?php echo $good_name; ?>" /><!--상 품 명-->
		<input type="hidden" name="buyername" id="ini_buyername" value="<?php echo $OrdNm; ?>" /><!--성 명-->
		<input type="hidden" name="buyeremail" id="ini_buyeremail" value="<?php echo $UserEmail; ?>" /><!--전 자 우 편-->
		<input type="hidden" name='pay_how' id='pay_how' value='<?php echo $V['pay_how']; ?>' />	<!-- 결제방법 -->
		<input type="hidden" name='Column2' id='Column2' value='<?php echo $deviceType; ?>' />	<!-- 디바이스 정보 -->
		<?php
		/*
		※ 주의 ※
		보호자 이메일 주소입력 받는 필드는 소액결제(핸드폰 , 전화결제)
		중에  14세 미만의 고객 결제시에 부모 이메일로 결제 내용통보하라는 정통부 권고 사항입니다. 
		다른 결제 수단을 이용시에는 해당 필드(parentemail)삭제 하셔도 문제없습니다.
		*/
		?>
		<input type="hidden" name="parentemail" id="ini_parentemail" value="" /><!--보호자 전자우편--></td>
		<input type="hidden" name="buyertel" id="ini_buyertel" value="<?php echo $OrdPhone; ?>" /><!--이 동 전 화-->

		<!-- 기타설정 -->
		<input type="hidden" name="currency" value="WON" /><!-- 화폐단위 -->
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
		<?php
		/*
		상점 주문번호 : 무통장입금 예약(가상계좌 이체),전화결재 관련 필수필드로 반드시 상점의 주문번호를 페이지에 추가해야 합니다.
		결제수단 중에 은행 계좌이체 이용 시에는 주문 번호가 결제결과를 조회하는 기준 필드가 됩니다.
		상점 주문번호는 최대 40 BYTE 길이입니다.
		주의:절대 한글값을 입력하시면 안됩니다.
		*/
		?>
		<input type="hidden" name="oid" id="ini_oid" value="<?php echo $ordr_idxx; ?>" />
		<?php
		/*
		플러그인 좌측 상단 상점 로고 이미지 사용
		이미지의 크기 : 90 X 34 pixels
		플러그인 좌측 상단에 상점 로고 이미지를 사용하실 수 있으며,
		주석을 풀고 이미지가 있는 URL을 입력하시면 플러그인 상단 부분에 상점 이미지를 삽입할수 있습니다.
		*/
		?>
		<input type="hidden" name="ini_logoimage_url" id="ini_logoimage_url"  value="<?php echo $shopLogo;?>" />
		<?php
		/*
		좌측 결제메뉴 위치에 이미지 추가
		이미지의 크기 : 단일 결제 수단 - 91 X 148 pixels, 신용카드/ISP/계좌이체/가상계좌 - 91 X 96 pixels
		좌측 결제메뉴 위치에 미미지를 추가하시 위해서는 담당 영업대표에게 사용여부 계약을 하신 후
		주석을 풀고 이미지가 있는 URL을 입력하시면 플러그인 좌측 결제메뉴 부분에 이미지를 삽입할수 있습니다.
		*/
		?>
		<input type="hidden" name="ini_menuarea_url" value="<?php echo $goodsImage;?>" />

		<?php
		/*
		플러그인에 의해서 값이 채워지거나, 플러그인이 참조하는 필드들
		삭제/수정 불가
		uid 필드에 절대로 임의의 값을 넣지 않도록 하시기 바랍니다.
		*/
		?>
		<input type="hidden" name="ini_encfield" value="<?php echo($inipay->GetResult("encfield")); ?>" />
		<input type="hidden" name="ini_certid" value="<?php echo($inipay->GetResult("certid")); ?>" />
		<input type="hidden" name="quotainterest" value="" />
		<input type="hidden" name="paymethod" value="" />
		<input type="hidden" name="cardcode" value="" />
		<input type="hidden" name="cardquota" value="" />
		<input type="hidden" name="rbankcode" value="" />
		<input type="hidden" name="reqsign" value="DONE" />
		<input type="hidden" name="encrypted" value="" />
		<input type="hidden" name="sessionkey" value="" />
		<input type="hidden" name="uid" value="" /> 
		<input type="hidden" name="sid" value="" />
		<input type="hidden" name="version" value="4000" />
		<input type="hidden" name="clickcontrol" id="ini_clickcontrol" value="enable" />

		<!-- 추가 전달 변수 -->
		<input type="hidden" name="orderList" id="orderList" value="<?php echo $orderList;?>">		<!-- 상품목록 -->
		<input type="hidden" name="BBSE_COMMERCE_THEME_WEB_URL" id="BBSE_COMMERCE_THEME_WEB_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>">
		<input type="hidden" name="etc_path" id="etc_path" value="<?php echo base64_encode($KeyPath); ?>">
	</form>
<?php }?>

<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* 
* Allthegate PG (올더게이트) 연동
*/

if($V['pay_how']=='C') $escrowYN='N';

if($V['deviceType'] == "mobile" || $V['deviceType'] == "tablet") {
	if($V['pay_how']=='C'){
		$payHow="card"; // 결 제 방 법(card:신용카드,virtual:가상계좌, onlycardself:신용카드 결제(전용메뉴) , onlyicheself:실시간 은행계좌이체 (전용메뉴) , onlyvirtualself:무통장입금(전용메뉴)
		$payHowTitle="신용카드";
	}
	elseif($V['pay_how']=='V'){
		$payHow="virtual";
		$payHowTitle="가상계좌";
	}
}
else{
	if($V['pay_how'] == "C") {
		$payHow = "onlycardself";
		$payHowTitle = "신용카드";
	}
	else if($V['pay_how'] == "K") {
		$payHow = "onlyicheself";
		$payHowTitle = "실시간 계좌이체";
	}
	else if($V['pay_how'] == "V") {
		$payHow = "onlyvirtualself";
		$payHowTitle = "가상계좌";
	}
}

$ordr_idxx = $V['order_no']; // 주문코드

$goodsNames = array();
foreach($result as $gData) {
	$goodsNames[] = $gData->goods_name;
}

if(count($goodsNames) > 1) $addGoodsCnt = " 외 ".(count($goodsNames)-1);
else $addGoodsCnt = "";

$good_name = $goodsNames[0].$addGoodsCnt; // 상품명

if($V['goods_option_price']=="") $V['goods_option_price']=0;
if($V['goods_add_price']=="") $V['goods_add_price']=0;
if($V['delivery_price']=="") $V['delivery_price']=0;
if($V['delivery_add_price']=="") $V['delivery_add_price']=0;
if($V['use_earn']=="") $V['use_earn']=0;

if($V['delivery_charge_type']!='free' &&  $V['delivery_charge_payment']=='deferred'){ // 후불
	$good_mny = ($V['goods_option_price']+$V['goods_add_price'])-$V['use_earn']; // 결제금액
}
else{
	$good_mny = ($V['goods_option_price']+$V['goods_add_price']+$V['delivery_price']+$V['delivery_add_price'])-$V['use_earn']; // 결제금액
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

$OrdNm=$V['order_name']; // 주문자명
$OrdPhone=$V['order_hp1']."-".$V['order_hp2']."-".$V['order_hp3']; // 주문자전화번호
$UserEmail=$V['order_email']; // 주문자 이메일

////고객정보
if(is_user_logged_in()) {
	$myInfo=bbse_get_user_information();
	$UserId = $myInfo->user_id;
	if(trim($myInfo->name)) 	$OrdNm = trim($myInfo->name);    //이름

	$OrdPhone = ($myInfo->phone)?$myInfo->phone:$OrdPhone;    //전화번호
	$UserEmail = ($myInfo->email)?$myInfo->email:$UserEmail;    //메일
}

$OrdAddr=$V['order_addr1']." ".$V['order_addr2']; // 문자주소
$RcpNm=$V['receive_name']; //수신자명
$RcpPhone=$V['receive_hp1']."-".$V['receive_hp2']."-".$V['receive_hp3']; //수신자연락처
$DlvAddr=$V['receive_addr1']." ".$V['receive_addr2']; //배송지주소

if($V['deviceType']=='mobile' || $V['deviceType']=='tablet') $Remark=$V['mobile_Remark']; 
else $Remark=$V['order_comment']; //기타요구사항

$AGS_HASHDATA = md5($StoreId . $ordr_idxx . $good_mny); 
			
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [1] 일반/무이자 결제여부를 설정합니다.
//
// 할부판매의 경우 구매자가 이자수수료를 부담하는 것이 기본입니다. 그러나,
// 상점과 올더게이트간의 별도 계약을 통해서 할부이자를 상점측에서 부담할 수 있습니다.
// 이경우 구매자는 무이자 할부거래가 가능합니다.
//
// 예제)
// 	(1) 일반결제로 사용할 경우 : 9000400001
//
// 	(2) 무이자결제로 사용할 경우 : 9000400002
//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//일반결제와 무이자 결제는 여기서 설정해주십시요
$DeviId="9000400001";

			
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [2] 일반 할부기간을 설정합니다.
// 
// 일반 할부기간은 2 ~ 12개월까지 가능합니다.
// 0:일시불, 2:2개월, 3:3개월, ... , 12:12개월
// 
// 예제)
// 	(1) 할부기간을 일시불만 가능하도록 사용할 경우
// 	form.QuotaInf.value = "0";
//
// 	(2) 할부기간을 일시불 ~ 24개월까지 사용할 경우
//		form.QuotaInf.value = "0:3:4:5:6:7:8:9:10:11:12";
//
// 	(3) 결제금액이 일정범위안에 있을 경우에만 할부가 가능하게 할 경우
// 	if((parseInt(form.Amt.value) >= 100000) || (parseInt(form.Amt.value) <= 200000))
// 		form.QuotaInf.value = "0:2:3:4:5:6:7:8:9:10:11:12";
// 	else
// 		form.QuotaInf.value = "0";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($good_mny<50000) $QuotaInf="0"; //결제금액이 5만원 미만건을 할부결제로 요청할경우 결제실패
else $QuotaInf="0:2:3:4:5:6:7:8:9:10:11:12";


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
// [3] 무이자 할부기간을 설정합니다.
// (일반결제인 경우에는 본 설정은 적용되지 않습니다.)
// 
// 무이자 할부기간은 3 ~ 12개월까지 가능하며, 
// 올더게이트에서 제한한 할부 개월수까지만 설정해야 합니다.
// 
// 100:BC
// 200:국민
// 300:외환
// 400:삼성
// 500:엘지
// 600:신한
// 800:현대
// 900:롯데
// 
// 예제)
// 	(1) 모든 할부거래를 무이자로 하고 싶을때에는 ALL로 설정
// 	form.NointInf.value = "ALL";
//
// 	(2) 국민카드 특정개월수만 무이자를 하고 싶을경우 샘플(3:4:5:6개월)
// 	form.NointInf.value = "200-3:4:5:6";
//
// 	(3) 외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(3:4:5:6개월)
// 	form.NointInf.value = "300-3:4:5:6";
//
// 	(4) 국민,외환카드 특정개월수만 무이자를 하고 싶을경우 샘플(3:4:5:6개월)
// 	form.NointInf.value = "200-3:4:5:6,300-3:4:5:6";
//	
//		(5) 무이자 할부기간 설정을 하지 않을 경우에는 NONE로 설정
//		form.NointInf.value = "NONE";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($DeviId=="9000400002") $NointInf="ALL";
else $NointInf="NONE";
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
									<button type="button" class="bb_btn cus_fill pay-action" onClick="pay(document.frmAGS_pay);"><strong class="big">결제하기</strong></button>
									<button type="button" class="bb_btn cus_solid back-action"><strong class="big">주문취소</strong></button>
								</div>
							</div>
						</div>

					</div><!--//.bb_pay_right -->
				</div><!--//.payment_wrap -->
			</div>
			</form>
			<?php }else{echo "BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.";} ?>
		</div>
	</div><!--//#content -->
<?php if($V['deviceType']=='mobile' || $V['deviceType']=='tablet'){?>
<?php
// 모바일 ver.2 가상계좌 - 에스크로 적용
if($escrowYN=='Y' && $payHow=='virtual') $payHow='virtualescrow';
?>
	<script type="text/javascript" charset="euc-kr" src="https://www.allthegate.com/payment/mobilev2/csrf/csrf.real.js"></script> 
	<script type="text/javascript" charset="euc-kr">
		function pay(form){
			var goodsMny="<?php echo $good_mny;?>";
			jQuery("#Amt").val(goodsMny);
			AllTheGate.pay(document.form);
			return false;
		}
	</script>

	<form method="post" action="https://www.allthegate.com/payment/mobilev2/intro.jsp" name="form">
		<input type="hidden" name="Job" value="<?php echo $payHow;?>" /><!--결제방법(card:신용카드, cardnormal:신용카드만, cardescrow:신용카드(에스크로), virtual:가상계좌, virtualnormal:가상계좌만, virtualescrow:가상계좌(에스크로), hp:휴대폰)-->
		<input type="hidden" name="StoreId" maxlength="20" value="<?php echo $StoreId; ?>" /><!--상점아이디-->
		<input type="hidden" name="StoreNm"  value="<?php bloginfo('name'); ?>" /><!--상점이름-->
		<input type="hidden"  name="MallUrl" value="<?php echo home_url(); ?>" /><!--상점URL-->
		<input type="hidden" name="OrdNo" value="<?php echo $ordr_idxx; ?>" /><!--주문번호-->
		<input type="hidden" name="ProdNm"  value="<?php echo $good_name; ?>" /><!--상품명-->
		<input type="hidden" name="Amt" value="<?php echo $good_mny; ?>" /><!--가격-->
		<input type="hidden" name="DutyFree" value="0" /><!--면세금액(amt 중 면세 금액 설정)-->

		<input type="hidden" name="OrdNm"  value="<?php echo $OrdNm; ?>" /><!--구매자이름-->
		<input type="hidden" name="OrdPhone"  value="<?php echo $OrdPhone; ?>" /><!--휴대폰번호-->
		<input type="hidden" name="UserEmail"  value="<?php echo $UserEmail; ?>" /><!--이메일-->
		<input type="hidden"  name="UserId" maxlength="20" value="<?php echo ($UserId)?$UserId:"GuestUser"; ?>" /><!--회원아이디-->
		<input type="hidden"  name="OrdAddr" value="<?php echo $OrdAddr; ?>" /><!--주문자주소-->
		<input type="hidden"  name="RcpNm" value="<?php echo $RcpNm; ?>" /><!--수신자명-->
		<input type="hidden"  name="RcpPhone" value="<?php echo $RcpPhone; ?>" /><!--수신자연락처-->
		<input type="hidden"  name="DlvAddr" value="<?php echo $DlvAddr; ?>" /><!--배송지주소-->
		<input type="hidden"  name="Remark" value="" /><!--기타요구사항-->
		<input type="hidden"  name="CardSelect"  value="" />	<!-- 카드사선택 (특정카드만 표기기능) -->
		<input type="hidden"  name="RtnUrl" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/payment/allthegate/mobileV2/AGSMobileV2_pay_ing.php" /><!--성공 URL-->
		<input type="hidden"  name="AppRtnScheme" value="" /><!--앱 URL Scheme (독자앱일 경우) : AppRtnScheme + RtnUrl을 합친 값으로 다시 앱을 호출합니다. 독자앱이 아닌경우 빈값으로 세팅--><!--  네이버 예시 :  naversearchapp://inappbrowser?url= -->
		<input type="hidden"  name="CancelUrl" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/payment/allthegate/mobileV2/AGSMobileV2_user_cancel.php" /><!--취소 URL-->
		<input type="hidden"  name="Column1" maxlength="200" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>"><!--추가사용필드1-->
		<input type="hidden"  name="Column2" maxlength="200" value="<?php echo $deviceType; ?>" /><!--추가사용필드2-->
		<input type="hidden"  name="Column3" maxlength="200" value="<?php echo $ordr_idxx; ?>" /><!--추가사용필드3-->
		<!--가상계좌 결제 사용 변수(시작)-->
		<input type="hidden" name="MallPage" maxlength="100" value="<?php echo $parseUrl['path']; ?>/payment/allthegate/mobileV2/AGSMobileV2_VirAcctResult.php" /><!--가상계좌 통보페이지-->
		<input type="hidden" name="VIRTUAL_DEPODT" maxlength="8" value="" /><!-- 가상계좌 입금예정일 예) 20100120 -->
		<!--가상계좌 결제 사용 변수(끝)-->

		<!--핸드폰 결제 사용 변수(시작)-->
		<input type="hidden" name="HP_ID" maxlength="10" value="" /><!--CP아이디-->
		<input type="hidden" name="HP_PWD" maxlength="10" value="" /><!--CP비밀번호-->
		<input type="hidden" name="HP_SUBID" maxlength="10" value="" /><!--SUB-CP아이디-->
		<input type="hidden" name="ProdCode" maxlength="10" value=""><!--상품코드-->
		<input type="hidden" name="HP_UNITType" value=""><!--상품종류(디지털:1, 실물:2)-->
		<input type="hidden" name="SubjectData" value=""><!--상품제공기간 또는 결제창제목입력(금액;품명;2014.09.21~28)-->
		<!--핸드폰 결제 사용 변수(끝)-->

		<input type="hidden" name="DeviId" value="<?php echo $DeviId;?>"><!--(신용카드공통) 단말기아이디 (9000400001:일반결제, 무이자결제 : 9000400002)-->        
		<input type="hidden" name="QuotaInf" value="<?php echo $QuotaInf;?>"><!--(신용카드공통) 일반 할부개월 설정 변수-->
		<input type="hidden" name="NointInf" value="<?php echo $NointInf;?>"><!--(신용카드공통)	무이자 할부개월 설정 변수-->
		<!-- 스크립트 및 플러그인에서 값을 설정하는 Hidden 필드  !!수정을 하시거나 삭제하지 마십시오-->
	</form>
<?php }else{?>
<?php if($payCFG['payment_allthegate_nonActiveX_use']=='Y'){?>
	<script language="javascript">
		var $ = jQuery;
	</script>
	<script language=javascript src="http://www.allthegate.com/plugin/AGSWallet_New.js"></script>
<?php }else{?>
	<script language=javascript src="http://www.allthegate.com/plugin/AGSWallet_utf8.js"></script>
<?php }?>
	<form name='frmAGS_pay' id='frmAGS_pay' method='post' action='<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/payment/allthegate/AGS_pay_ing.php'>
		<input type="hidden" name='Job' id='Job' value='<?php echo $payHow;?>' /><!-- 지불방법 신용카드:card, 계좌이체:iche -->
		<input type="hidden" name='TempJob' id='TempJob' value='' /><!-- 지불방법 직접입력 -->
		<input type="hidden" name='OrdNo' id='OrdNo' value='<?php echo $ordr_idxx; ?>' />	<!-- 주문번호 -->
		<input type="hidden" name='StoreId' id='StoreId' value='<?php echo $StoreId; ?>' />	<!-- 상점아이디 -->
		<input type="hidden" name='Amt' id='Amt' value='<?php echo $good_mny; ?>' />	<!-- 금액 -->
		<input type="hidden" name='StoreNm' id='StoreNm' value='<?php bloginfo('name'); ?>' />	<!-- 상점명 -->
		<input type="hidden" name='ProdNm' id='ProdNm' value='<?php echo $good_name; ?>' />	<!-- 상품명 -->
		<input type="hidden" name='MallUrl' id='MallUrl' value='<?php echo home_url(); ?>' /><!-- 상점URL -->
		<input type="hidden" name='UserEmail' id='UserEmail' value='<?php echo $UserEmail; ?>' />	<!-- 주문자이메일 -->
		<input type="hidden" name='ags_logoimg_url' id='ags_logoimg_url' value='<?php echo $shopLogo;?>' />	<!-- 상점로고이미지 URL -->
		<input type="hidden" name='SubjectData' id='SubjectData' value='' />	<!-- 결제창제목입력 -->
		<input type="hidden" name='UserId' id='UserId' value='<?php echo ($UserId)?$UserId:"GuestUser"; ?>' />	<!--  -->
		<input type="hidden" name='OrdNm' id='OrdNm' value='<?php echo $OrdNm; ?>' />	<!-- 주문자명 -->
		<input type="hidden" name='OrdPhone' id='OrdPhone' value='<?php echo $OrdPhone; ?>' />	<!-- 주문자연락처 -->
		<!--가상계좌 경우 만 입력하여도 됨(시작)-->
		<input type="hidden" name='OrdAddr' id='OrdAddr' value='<?php echo $OrdAddr; ?>' />	<!-- 주문자주소 -->
		<input type="hidden" name='RcpNm' id='RcpNm' value='<?php echo $RcpNm; ?>' />	<!-- 수신자명 -->
		<input type="hidden" name='RcpPhone' id='RcpPhone' value='<?php echo $RcpPhone; ?>' />	<!-- 수신자연락처 -->
		<input type="hidden" name='DlvAddr' id='DlvAddr' value='<?php echo $DlvAddr; ?>' />	<!-- 배송지주소 -->
		<input type="hidden" name='Remark' id='Remark' value='' />	<!-- 기타요구사항 -->
		<!--가상계좌 경우 만 입력하여도 됨(끝)-->
		<input type="hidden" name='CardSelect' id='CardSelect' value='' />	<!-- 카드사선택 (특정카드만 표기기능) -->
		<!-- 핸드폰 결제 사용 변수 -->
		<input type="hidden" name='HP_ID' id='HP_ID' value='' />	<!-- CP아이디 -->
		<input type="hidden" name='HP_PWD' id='HP_PWD' value='' />	<!-- CP비밀번호 -->
		<input type="hidden" name='HP_SUBID' id='HP_SUBID' value='' />	<!-- SUB-CP아이디 -->
		<input type="hidden" name='ProdCode' id='ProdCode' value='' />	<!-- 상품코드 -->
		<input type="hidden" name='HP_UNITType' id='HP_UNITType' value='' />	<!-- 상품종류 (디지털(컨텐츠)일 경우 = 1, 실물(상품)일 경우 = 2) -->
		<!-- 가상계좌 결제 사용 변수 -->
		<?php $parseUrl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);?>
		<input type="hidden" name='MallPage' id='MallPage' value='<?php echo $parseUrl['path']; ?>/payment/allthegate/AGS_VirAcctResult.php' />	<!-- 통보페이지 -->
		<input type="hidden" name='VIRTUAL_DEPODT' id='VIRTUAL_DEPODT' value='' /> <!-- 가상계좌 입금예정일 예) 20100120 -->
		<!-- 추가 전달 변수 -->
		<input type="hidden" name='pay_how' id='pay_how' value='<?php echo $V['pay_how']; ?>' />	<!-- 결제방법 -->
		<input type="hidden" name="orderList" id="orderList" value="<?php echo $orderList;?>" />		<!-- 상품목록 -->
		<input type="hidden" name="BBSE_COMMERCE_THEME_WEB_URL" id="BBSE_COMMERCE_THEME_WEB_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>" />

		<!-- 스크립트 및 플러그인에서 값을 설정하는 Hidden 필드  !!수정을 하시거나 삭제하지 마십시오-->

		<!-- 각 결제 공통 사용 변수 -->
		<input type="hidden" name="Flag" id="Flag" value="enable" />				<!-- 스크립트결제사용구분플래그 -->
		<input type="hidden" name="AuthTy" id="AuthTy" value="" />			<!-- 결제형태 -->
		<input type="hidden" name="SubTy" id="SubTy" value="" />				<!-- 서브결제형태 -->
		<input type="hidden" name="AGS_HASHDATA" id="AGS_HASHDATA" value="<?php echo $AGS_HASHDATA?>" />	<!-- 암호화 HASHDATA -->

		<!-- 신용카드 결제 사용 변수 -->
		<input type="hidden" name="DeviId" id="DeviId" value="<?php echo $DeviId;?>" />			<!-- (신용카드공통)		단말기아이디 (9000400001:일반결제, 무이자결제 : 9000400002-->
		<input type="hidden" name="QuotaInf" id="QuotaInf" value="<?php echo $QuotaInf;?>" />			<!-- (신용카드공통)		일반할부개월설정변수 -->
		<input type="hidden" name="NointInf" id="NointInf" value="<?php echo $NointInf;?>" />		<!-- (신용카드공통)		무이자할부개월설정변수 -->
		<input type="hidden" name="AuthYn" id="AuthYn" value="" />			<!-- (신용카드공통)		인증여부 -->
		<input type="hidden" name="Instmt" id="Instmt" value="" />			<!-- (신용카드공통)		할부개월수 -->
		<input type="hidden" name="partial_mm" id="partial_mm" value="" />		<!-- (ISP사용)			일반할부기간 -->
		<input type="hidden" name="noIntMonth" id="noIntMonth" value="" />		<!-- (ISP사용)			무이자할부기간 -->
		<input type="hidden" name="KVP_RESERVED1" id="KVP_RESERVED1" value="" />		<!-- (ISP사용)			RESERVED1 -->
		<input type="hidden" name="KVP_RESERVED2" id="KVP_RESERVED2" value="" />		<!-- (ISP사용)			RESERVED2 -->
		<input type="hidden" name="KVP_RESERVED3" id="KVP_RESERVED3" value="" />		<!-- (ISP사용)			RESERVED3 -->
		<input type="hidden" name="KVP_CURRENCY" id="KVP_CURRENCY" value="" />		<!-- (ISP사용)			통화코드 -->
		<input type="hidden" name="KVP_CARDCODE" id="KVP_CARDCODE" value="" />		<!-- (ISP사용)			카드사코드 -->
		<input type="hidden" name="KVP_SESSIONKEY" id="KVP_SESSIONKEY" value="" />	<!-- (ISP사용)			암호화코드 -->
		<input type="hidden" name="KVP_ENCDATA" id="KVP_ENCDATA" value="" />		<!-- (ISP사용)			암호화코드 -->
		<input type="hidden" name="KVP_CONAME" id="KVP_CONAME" value="" />		<!-- (ISP사용)			카드명 -->
		<input type="hidden" name="KVP_NOINT" id="KVP_NOINT" value="" />			<!-- (ISP사용)			무이자/일반여부(무이자=1, 일반=0) -->
		<input type="hidden" name="KVP_QUOTA" id="KVP_QUOTA" value="" />			<!-- (ISP사용)			할부개월 -->
		<input type="hidden" name="CardNo" id="CardNo" value="" />			<!-- (안심클릭,일반사용)	카드번호 -->
		<input type="hidden" name="MPI_CAVV" id="MPI_CAVV" value="" />			<!-- (안심클릭,일반사용)	암호화코드 -->
		<input type="hidden" name="MPI_ECI" id="MPI_ECI" value="" />			<!-- (안심클릭,일반사용)	암호화코드 -->
		<input type="hidden" name="MPI_MD64" id="MPI_MD64" value="" />			<!-- (안심클릭,일반사용)	암호화코드 -->
		<input type="hidden" name="ExpMon" id="ExpMon" value="" />			<!-- (일반사용)			유효기간(월) -->
		<input type="hidden" name="ExpYear" id="ExpYear" value="" />			<!-- (일반사용)			유효기간(년) -->
		<input type="hidden" name="Passwd" id="Passwd" value="" />			<!-- (일반사용)			비밀번호 -->
		<input type="hidden" name="SocId" id="SocId" value="" />				<!-- (일반사용)			주민등록번호/사업자등록번호 -->

		<!-- 계좌이체 결제 사용 변수 -->
		<input type="hidden" name="ICHE_OUTBANKNAME" value="" />	<!-- 이체계좌은행명 -->
		<input type="hidden" name="ICHE_OUTACCTNO" value="" />	<!-- 이체계좌예금주주민번호 -->
		<input type="hidden" name="ICHE_OUTBANKMASTER" value="" /><!-- 이체계좌예금주 -->
		<input type="hidden" name="ICHE_AMOUNT" value="" />		<!-- 이체금액 -->

		<!-- 핸드폰 결제 사용 변수 -->
		<input type="hidden" name="HP_SERVERINFO" id="HP_SERVERINFO" value="" />		<!-- 서버정보 -->
		<input type="hidden" name="HP_HANDPHONE" id="HP_HANDPHONE" value="" />		<!-- 핸드폰번호 -->
		<input type="hidden" name="HP_COMPANY" id="HP_COMPANY" value="" />		<!-- 통신사명(SKT,KTF,LGT) -->
		<input type="hidden" name="HP_IDEN" id="HP_IDEN" value="" />			<!-- 인증시사용 -->
		<input type="hidden" name="HP_IPADDR" id="HP_IPADDR" value="" />			<!-- 아이피정보 -->

		<!-- ARS 결제 사용 변수 -->
		<input type="hidden" name="ARS_PHONE" id="ARS_PHONE" value="" />			<!-- ARS번호 -->
		<input type="hidden" name="ARS_NAME" id="ARS_NAME" value="" />			<!-- 전화가입자명 -->

		<!-- 가상계좌 결제 사용 변수 -->
		<input type="hidden" name="ZuminCode" id="ZuminCode" value="" />			<!-- 가상계좌입금자주민번호 -->
		<input type="hidden" name="VIRTUAL_CENTERCD" id="VIRTUAL_CENTERCD" value="" />	<!-- 가상계좌은행코드 -->
		<input type="hidden" name="VIRTUAL_NO" id="VIRTUAL_NO" value="" />		<!-- 가상계좌번호 -->

		<input type="hidden" name="mTId" id="mTId" value="" />	

		<!-- 에스크로 결제 사용 변수 -->
		<input type="hidden" name="ES_SENDNO" id="ES_SENDNO" value="" />			<!-- 에스크로전문번호 -->

		<!-- 계좌이체(소켓) 결제 사용 변수 -->
		<input type="hidden" name="ICHE_SOCKETYN" id="ICHE_SOCKETYN" value="" />		<!-- 계좌이체(소켓) 사용 여부 -->
		<input type="hidden" name="ICHE_POSMTID" id="ICHE_POSMTID" value="" />		<!-- 계좌이체(소켓) 이용기관주문번호 -->
		<input type="hidden" name="ICHE_FNBCMTID" id="ICHE_FNBCMTID" value="" />		<!-- 계좌이체(소켓) FNBC거래번호 -->
		<input type="hidden" name="ICHE_APTRTS" id="ICHE_APTRTS" value="" />		<!-- 계좌이체(소켓) 이체 시각 -->
		<input type="hidden" name="ICHE_REMARK1" id="ICHE_REMARK1" value="" />		<!-- 계좌이체(소켓) 기타사항1 -->
		<input type="hidden" name="ICHE_REMARK2" id="ICHE_REMARK2" value="" />		<!-- 계좌이체(소켓) 기타사항2 -->
		<input type="hidden" name="ICHE_ECWYN" id="ICHE_ECWYN" value="" />		<!-- 계좌이체(소켓) 에스크로여부 -->
		<input type="hidden" name="ICHE_ECWID" id="ICHE_ECWID" value="" />		<!-- 계좌이체(소켓) 에스크로ID -->
		<input type="hidden" name="ICHE_ECWAMT1" id="ICHE_ECWAMT1" value="" />		<!-- 계좌이체(소켓) 에스크로결제금액1 -->
		<input type="hidden" name="ICHE_ECWAMT2" id="ICHE_ECWAMT2" value="" />		<!-- 계좌이체(소켓) 에스크로결제금액2 -->
		<input type="hidden" name="ICHE_CASHYN" id="ICHE_CASHYN" value="" />		<!-- 계좌이체(소켓) 현금영수증발행여부 -->
		<input type="hidden" name="ICHE_CASHGUBUN_CD" id="ICHE_CASHGUBUN_CD" value="" />	<!-- 계좌이체(소켓) 현금영수증구분 -->
		<input type="hidden" name="ICHE_CASHID_NO" id="ICHE_CASHID_NO" value="" />	<!-- 계좌이체(소켓) 현금영수증신분확인번호 -->

		<!-- 텔래뱅킹-계좌이체(소켓) 결제 사용 변수 -->
		<input type="hidden" name="ICHEARS_SOCKETYN" id="ICHEARS_SOCKETYN" value="" />	<!-- 텔레뱅킹계좌이체(소켓) 사용 여부 -->
		<input type="hidden" name="ICHEARS_ADMNO" id="ICHEARS_ADMNO" value="" />		<!-- 텔레뱅킹계좌이체 승인번호 -->
		<input type="hidden" name="ICHEARS_POSMTID" id="ICHEARS_POSMTID" value="" />	<!-- 텔레뱅킹계좌이체 이용기관주문번호 -->
		<input type="hidden" name="ICHEARS_CENTERCD" id="ICHEARS_CENTERCD" value="" />	<!-- 텔레뱅킹계좌이체 은행코드 -->
		<input type="hidden" name="ICHEARS_HPNO" id="ICHEARS_HPNO" value="" />		<!-- 텔레뱅킹계좌이체 휴대폰번호 -->
	<!-- 스크립트 및 플러그인에서 값을 설정하는 Hidden 필드  !!수정을 하시거나 삭제하지 마십시오-->
	</form>
	<script language=javascript>
		StartSmartUpdate(); 
		function pay(form){
			var goodsMny="<?php echo $good_mny;?>";
			jQuery("#Amt").val(goodsMny);
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// MakePayMessage() 가 호출되면 올더게이트 플러그인이 화면에 나타나며 Hidden 필드
			// 에 리턴값들이 채워지게 됩니다.
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////

			if(jQuery("#Flag").val() == "enable"){
				// 올더게이트 플러그인 설치가 올바르게 되었는지 확인합니다.
				if(document.AGSPay == null || document.AGSPay.object == null){
					alert("플러그인 설치 후 다시 시도 하십시오.");
				}
				else{

<?php if($payCFG['payment_allthegate_nonActiveX_use']=='Y'){?>
					MakePayMessage(form);
<?php }else{?>
					if(MakePayMessage(form) == true){
						jQuery("#Flag").val("disable");
						jQuery("#quick").css("z-index", "-1");
						var preload = '<div id="paying_view" class="modal"><p class="pay-process"><img src="'+common_var.goods_template_url+'/payment/allthegate/image/progress.gif"><br/><br/>결제 처리중입니다. 잠시만 기다려주세요.</p></div>';
						jQuery("#content").append(preload);
						jQuery("#paying_view").modal({
							escapeClose: false,
							clickClose: false,
							showClose: false
						});
						
						jQuery("#frmAGS_pay").submit();
					}
					else{
						alert("지불에 실패하였습니다."); // 취소시 이동페이지 설정부분
					}
<?php }?>
				}
			}
		}
	</script>
<?php if($payCFG['payment_allthegate_nonActiveX_use']=='Y'){?>
	<script type="text/javascript" src="https://www.allthegate.com/payment/webPay/js/ATGClient_new.js"></script>
<?php }?>

<?php }?>


<?php 
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* Cart */
get_header();

global $current_user,$theme_shortname,$currentSessionID;

wp_get_current_user();
$currUserID=$current_user->user_login;
$Loginflag='member';

$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사
if($nPayData['guest_cart_use']=='on' && !$currUserID){
	$Loginflag='guest';
	$currUserID=$_SERVER['REMOTE_ADDR'];
}
elseif($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsData=unserialize($_SESSION['snsLoginData']);
		$Loginflag='guest';
		$currUserID=$snsData['sns_id'];
	}
	else{
		echo "<script>location.href='".get_permalink(get_option($theme_shortname."_login_page"))."';</script></div></div></body></html>";
		exit;
	}
}
else{
	if(!is_user_logged_in()){
		echo "<script>location.href='".get_permalink(get_option($theme_shortname."_login_page"))."';</script></div></div></body></html>";
		exit;
	}
}

$bbsePage=get_query_var( 'bbsePage' );

emptyCart($currUserID); //보관일 지난 상품 삭제
updateCart($currUserID); //장바구니 업데이트

$V = $_POST;
$result = $wpdb->get_results("SELECT C.idx AS cart_idx, C.user_id, C.sid, C.goods_option_basic AS cart_option_basic, C.goods_option_add AS cart_option_add, C.remote_ip, C.reg_date, G.* FROM bbse_commerce_cart AS C, bbse_commerce_goods AS G WHERE C.goods_idx=G.idx AND C.cart_kind='C' AND C.user_id='".$currUserID."' ORDER BY C.idx DESC");

$cartNaverPay='off';
?>
<script language="javascript">
	var Loginflag="<?php echo $Loginflag;?>";

	function cartAction(k, i) {
		var ordAction="";
		if(k=="order") {
			if(Loginflag=='guest') ordAction=common_var.home_url+"/?bbsePage=order-agree";
			else ordAction=common_var.home_url+"/?bbsePage=order";

			jQuery("[id^='gidx_']").prop("checked", false);
			jQuery("#cart_mode").val("order");
			jQuery("#gidx_"+i).prop("checked", true);
			jQuery("#orderFrm").attr("action",ordAction).submit();
		}else if(k=="remove") {
			if(confirm("해당 상품을 장바구니에서 삭제하시겠습니까?")) {
				jQuery("#cart_idx").val(i);
				jQuery("#cart_mode").val("remove");
				jQuery("#orderFrm").attr("action",jQuery("#action_url").val()+"/proc/cart.exec.php").submit();
			}
		}
	}
	jQuery(document).ready(function() {
		// 관심상품 이동
		var move_wishlist = function(){
			if(confirm("선택한 상품을 관심상품(찜)으로 이동하시겠습니까?    ")){
				var apiUrl=common_var.goods_template_url +"/proc/order-cart.exec.php";
				var homeUrl=common_var.home_url;
				jQuery.ajax({
					type: 'post', 
					async: false, 
					url: apiUrl, 
					data: {
						tMode: 'addWishlist',
						goods_list: jQuery("input[name='gidx[]']").serialize()
					}, 
					success: function(data){
						var result = data.split("|||"); 
						if(result[0] == "success"){
							if(confirm('상품이 찜리스트에 저장되었습니다.   \n찜리스트를 확인 하시겠습니까?')){
								window.location.href=homeUrl+"/?bbsePage=interest";
							}else{
								window.location.href=homeUrl+"/?bbsePage=cart";
							}
						}
						else if(result[0] == "loginError"){
							alert("회원전용 서비스 입니다. 로그인 후 이용해 주세요.   ");
						}
						else{
							alert("서버와의 통신이 실패했습니다.   ");
						}
					}, 
					error: function(data, status, err){
						alert("서버와의 통신이 실패했습니다.   ");
					}
				});
			}
		}

		jQuery("#cart-wishlist-btn").click(function() { //선택상품 찜
			if(jQuery("input[name='gidx[]']:checked").length == 0) {
				alert("찜할 상품을 선택해주세요.");
			}else{
				move_wishlist();
			}
		});

		jQuery("#cart-delete-btn").click(function() { //선택상품 삭제
			if(jQuery("input[name='gidx[]']:checked").length == 0) {
				alert("삭제할 상품을 선택해주세요.");
			}else{
				if(confirm("선택한 상품을 장바구니에서 삭제하시겠습니까?")) {
					jQuery("#cart_mode").val("select_remove");
					jQuery("#orderFrm").attr("action",jQuery("#action_url").val()+"/proc/cart.exec.php").submit();
				}
			}
		});

		jQuery("#cart-shopping-btn").click(function() { //쇼핑 계속하기
			location.href = common_var.home_url+"/";
		});

		jQuery("#cart-check-order-btn").click(function() { //선택상품 주문
			var ordAction="";
			if(Loginflag=='guest') ordAction=common_var.home_url+"/?bbsePage=order-agree";
			else ordAction=common_var.home_url+"/?bbsePage=order";

			if(jQuery("input[name='gidx[]']:checked").length == 0) {
				alert("주문할 상품을 선택해주세요.");
			}else{
				jQuery("#orderFrm").attr("action",ordAction).submit();
			}
		});
		jQuery("#cart-all-order-btn").click(function() { //전체 주문
			var ordAction="";
			if(Loginflag=='guest') ordAction=common_var.home_url+"/?bbsePage=order-agree";
			else ordAction=common_var.home_url+"/?bbsePage=order";

			jQuery("[id^='gidx_']").prop("checked", true);
			jQuery("#orderFrm").attr("action",ordAction).submit();
		});

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
			<h2 class="page_title">장바구니</h2>
			<div class="article  mobileHidden">
				<!-- 장바구니에 담은 상품을 더 오래 보관하시려면 찜하기를 이용하세요. -->
				<ol class="bb_join_step">
					<li class="active">01 장바구니</li>
					<li>02 주문서작성/결제</li>
					<li>03 주문접수완료</li>
				</ol>
				<br style="clear:both"/>
			</div>

			<div class="shoppingNoticeBox" >
				<ul class="bb_dot_list">
					<li>상품을 선택해 해당 상품을 주문/삭제/찜 할 수 있습니다.</li>
					<li>장바구니에 담은 상품을 더 오래 보관하시려면 선택상품 찜 버튼을 이용하세요.</li>
					<li class="desc_payAmount">결제금액은 주문서 작성페이지에서 각종 할인 및 추가 금액을 적용뒤 정확히 다시 계산됩니다.</li>
				</ul>
			</div>
			<?php if(plugin_active_check('BBSe_Commerce')) { ?>
			<div class="article">
				<form name="orderFrm" id="orderFrm" method="post">
				<input type="hidden" name="order_type" id="order_type" value="cart">
				<input type="hidden" name="cart_mode" id="cart_mode" value="">
				<input type="hidden" name="cart_idx" id="cart_idx" value="">
				<input type="hidden" name="action_url" id="action_url" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>">
			    <input type="hidden" name="login_url" id="login_url" value="<?php echo get_permalink(get_option($theme_shortname.'_login_page'));?>" />

				<div class="fakeTable cart">
					<ul class="header">
						<li><label class="blind" for="allChkSelect">전체선택</label><input type="checkbox" name="" id="allChkSelect" /></li>
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
					 $total_add_ship_price = 0;//추가 개별배송비 합계
		            $total_add_ship_cnt = 0;//추가 개별배송비 합계
		            $total_item_cnt = 0;//전체 상품 수
		            $goods_vat = 0; //부가세
					if(count($result) > 0) {
						$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
						$deliveryInfo=unserialize($confData->config_data);
						$myInfo=bbse_get_user_information();
						foreach($result as $cart) {

							$memPrice=unserialize($cart->goods_member_price);

							if($myInfo->user_id && $myInfo->user_class>2 && $myInfo->use_sale=='Y'){
								$salePrice=$cart->goods_price;
								for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
								    if($memPrice['goods_member_level'][$m]==$myInfo->user_class){
								        $memberPrice=$memPrice['goods_member_price'][$m];
								        $goods_vat=$memPrice['goods_vat'][$m];
								        $salePrice = $memberPrice+$goods_vat;
								    }
								}
								$savePrice=$cart->goods_consumer_price-$salePrice;
								$myClassName="<span class=\"special_tag\">".$myInfo->class_name."</span>";
							}else{
								$salePrice=$cart->goods_price;
								$savePrice=$cart->goods_consumer_price-$cart->goods_price;
								$myClassName="";
							}

							$goods_total_price = 0;
							$goods_total_count = 0;
							if($cart->goods_basic_img) $basicImg = wp_get_attachment_image_src($cart->goods_basic_img,"goodsimage2");
							else{
								$imageList=explode(",",$cart->goods_add_img);
								if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage2");
								else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
							}

							$option_basic = $cart->cart_option_basic?unserialize($cart->cart_option_basic):"";
							$option_add = $cart->cart_option_add?unserialize($cart->cart_option_add):"";
							$goods_option_add = $cart->goods_option_add?unserialize($cart->goods_option_add):"";

							$goods_option_price = 0;
							$goods_add_price = 0;
							$total_option_price = 0;
							$total_add_price = 0;
							$goods_total_cnt = 0;
							$option_cnt = 0;
							
							

							if($cart->goods_naver_pay=='on') $cartNaverPay='on';
							
							//개별배송비 추가
							$total_item_cnt++;
							if($cart->goods_ship_tf == 'view'){
								$total_add_ship_price += $cart->goods_ship_price;
								$total_add_ship_cnt++;	
							}
					?>
					<ul>
						<li>
							<label class="blind" for="gidx_<?php echo $cart->idx; ?>">선택</label><input type="checkbox" name="gidx[]" id="gidx_<?php echo $cart->idx; ?>" class="cartGoodsCheck" data-npay="<?php echo $cart->goods_naver_pay;?>" value="<?php echo $cart->cart_idx; ?>"/>
							<?php echo ($nPayData['naver_pay_use']=='on' && $cart->goods_naver_pay=='on')?"<img src='".BBSE_COMMERCE_THEME_WEB_URL."/images/icon_npay.png' class='npay-icon' />":"";?>
						</li>
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
							<span class="cell-data"><?php echo number_format($goods_total_cnt); ?> <button type="button" class="bb_btn shadow openLayer" data-name="goodsOptionChanger" data-ids="C^<?php echo $cart->cart_idx; ?>"><span class="sml">변경</span></button></span>
						</li>
						<li>
							<span class="mobile-cell-title">적립금</span>
							<span class="cell-data">
							<?php
							if($cart->goods_earn_use=='on' && $cart->goods_earn>'0'){
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
							<div class="cartBtns">
								<button type="button" class="bb_btn shadow" onclick="cartAction('order',<?php echo $cart->idx; ?>);"><span class="sml">주문</span></button>
								<button type="button" class="bb_btn shadow" onclick="cartAction('remove',<?php echo $cart->cart_idx; ?>);"><span class="sml">삭제</span></button>
							</div>
						</li>
					</ul>
					<?php
							$total_goods_price += ($goods_option_price+$goods_add_price);
						}
						//$total_goods_price += $total_sale_price;
						//$total_price = ($total_goods_price - $total_sale_price);
						$total_price = $total_goods_price;

						if($deliveryInfo['delivery_charge_type']=='free' || ($deliveryInfo['delivery_charge_type']=='charge' && $deliveryInfo['condition_free_use']=='on' && $total_price>=$deliveryInfo['total_pay'])){
							$delivery_price = 0;
						}else{
							if($deliveryInfo['delivery_charge_payment'] == "advance") {
								//$delivery_price = $deliveryInfo['delivery_charge'];
								$delivery_tit = "(선불)";
								//개별배송비 계산
			                  	$no_add_cnt = $total_item_cnt - $total_add_ship_cnt;
								if($no_add_cnt > 0){
									$delivery_price += intval($deliveryInfo['delivery_charge']);
								}
								$delivery_price += $total_add_ship_price;
								$total_price += $delivery_price;
							}else{
								$delivery_price = $deliveryInfo['delivery_charge'];
								$delivery_tit = "(후불)";
							}
						}
					}

					if($deliveryInfo['delivery_charge_type']=='free'){
						$charge_tit = "무료";
					}else{
						$charge_tit = "유료";
						if($deliveryInfo['delivery_charge_payment'] == "advance") {
							$delivery_tit = "선불";
						}else{
							$delivery_tit = "후불";
						}
					}
					?>
				</div><!-- fakeTable -->
				
				<?php if(count($result) == 0) { ?><div class="nodata">장바구니에 담겨 있는 상품이 없습니다.</div><?php } ?>

				<div class="clearFloat"></div>
				<dl class="tb_foot">
					<dt><strong class="mb_hide">총합계금액</strong></dt>
					<dd>
						<div class="bb_left fz11">
							<span class="c_green">
								예상적립금 <strong><span id="mileage_expect"><?php echo number_format($mileage_expect); ?></span></strong>원
							</span>
						</div>
						<div class="bb_right">
							<span>상품금액 <span id="total_goods_price"><?php echo number_format($total_goods_price); ?></span>원</span>
							<span>+ 배송비 <span id="delivery_price"><?php echo number_format($delivery_price); ?></span>원 <?php echo $delivery_tit; ?></span>
							<strong class="bb_price">= 전체합계: <em><span id="total_price"><?php echo number_format($total_price); ?></span></em>원</strong>
						</div>
					</dd>
				</dl>

				<div class="bb_btn_area">
					<div class="bb_left">
					<?php if($Loginflag=='member'){?>
						<button type="button" class="bb_btn shadow" id="cart-wishlist-btn"><span class="sml">선택상품 찜</span></button>
					<?php }?>
						<button type="button" class="bb_btn shadow" id="cart-delete-btn"><span class="sml">선택상품 삭제</span></button>
						<!-- <button type="button" class="bb_btn shadow cart-"><span class="sml">선택상품만 다시계산</span></button> -->
					</div>
					<div class="bb_right">
						<button type="button" class="bb_btn cus_fill w150" id="cart-shopping-btn"><strong class="big">쇼핑계속하기</strong></button>
						<button type="button" class="bb_btn cus_solid w150" id="cart-check-order-btn"><strong class="big">선택상품 주문</strong></button>
						<button type="button" class="bb_btn cus_solid w150" id="cart-all-order-btn"><strong class="big">전체 주문</strong></button>
						<?php
							$esti_config = unserialize($wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'")->config_data);
							
							if($esti_config['esti_use'] == 'on' && is_user_logged_in()){
								echo '<button type="button" class="bb_btn cus_solid w150 estimate">
										<strong class="big">견적서</strong>
										</button>
										<script>
											jQuery(function(){
												jQuery(".estimate").click(function(){
													window.open("'.get_template_directory_uri().'/estimate.php","_blank", "width=750px,height=800");
												});
											});
										</script>
								';
							}
						?>
					<?php 
					if(count($result) > 0 && (($nPayData['member_naver_pay_use']=='on' && $current_user->user_login) || !$current_user->user_login) && $nPayData['naver_pay_use']=='on' && $cartNaverPay=='on' && ($nPayData['naver_pay_type']=='real' || ($nPayData['naver_pay_type']=='test' && $_REQUEST['npayTest']==true))){
						  if(wp_is_mobile()) {
							  $nPayScript="mobile/"; 
							  $nPayButton="MA";
							  $nPayOrdUrl = ($nPayData['naver_pay_type']=='test')?"https://test-m.pay.naver.com/mobile/customer/order.nhn":"https://m.pay.naver.com/mobile/customer/order.nhn";
						  } // 모바일
						  else {
							  $nPayScript=""; 
							  $nPayButton="A";
							  $nPayOrdUrl = ($nPayData['naver_pay_type']=='test')?"https://test-pay.naver.com/customer/order.nhn":"https://pay.naver.com/customer/order.nhn";
						  }

						  if(wp_is_mobile()) {$nPayScript="mobile/"; $nPayButton="MA";} // 모바일
						  else {$nPayScript=""; $nPayButton="A";}

						  if($soldout) {$nPayEnable="N"; $nPayBuyButton="not_buy_nPay";} // 품절상품
						  else {$nPayEnable="Y"; $nPayBuyButton="buy_detailNpay";}
					  ?>
						<div class="cart_npay">
							<div class="cart_npay_btn">
								<script type="text/javascript" src="http://<?php echo ($nPayData['naver_pay_type']=='test')?"test-":"";?>pay.naver.com/customer/js/<?php echo $nPayScript;?>naverPayButton.js" charset="UTF-8"></script>
								<script type="text/javascript" >
								//<![CDATA[
									naver.NaverPayButton.apply({
										BUTTON_KEY: "<?php echo $nPayData['naver_pay_button_key'];?>", // 페이에서 제공받은 버튼 인증 키 입력
										TYPE: "<?php echo $nPayButton;?>", // 버튼 모음 종류 설정
										COLOR: 1, // 버튼 모음의 색 설정
										COUNT: 1, // 버튼 개수 설정. 구매하기 버튼만 있으면(장바구니 페이지) 1, 장바구니/찜하기 버튼도 있으면(상품 상세 페이지) 2를 입력.
										ENABLE: "<?php echo $nPayEnable;?>", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
										BUY_BUTTON_HANDLER:<?php echo $nPayBuyButton;?>, // 구매하기 버튼 이벤트 Handler 함수 등록, 품절인 경우 not_buy_nc 함수 사용
										WISHLIST_BUTTON_HANDLER: wishlist_nPay, // 찜하기 버튼 이벤트 Handler 함수 등록
										"":""
									});
								//]]>
								</script>
							</div>
						</div>
					<?php }?>
					</div>
				</div>
				</form>
			  <?php if((($nPayData['member_naver_pay_use']=='on' && $current_user->user_login) || !$current_user->user_login) && $nPayData['naver_pay_use']=='on' && $cartNaverPay=='on'){?>
				  <div style="display:none;">
						<form id="nPayOrderFrm" name="nPayOrderFrm" method="get" target="_top" action="<?php echo $nPayOrdUrl?>">
							<input type="hidden" id="nPay_order_orderId" name="ORDER_ID" value="">
							<input type="hidden" id="nPay_order_shopId" name="SHOP_ID" value="">
							<input type="hidden" id="nPay_order_totalPrice" name="TOTAL_PRICE" value="">
						</form>
					</div>
				<?php }?>
			</div>
		<?php
		if($total_price<='0'){
			$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
			$deliveryInfo=unserialize($confData->config_data);

			if($deliveryInfo['delivery_charge_type']=='free'){
				$charge_tit = "무료";
			}else{
				$charge_tit = "유료";
				$delivery_price = $deliveryInfo['delivery_charge'];
				if($deliveryInfo['delivery_charge_payment'] == "advance") {
					$delivery_tit = "선불";
				}else{
					$delivery_tit = "후불";
				}
			}
		}

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

			<?php }else{echo "BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.";} ?>
		</div>
	</div><!--//#content -->
<?php get_footer();
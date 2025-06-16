<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* Order */
get_header();

global $current_user,$theme_shortname,$currentSessionID;

$customPriceView=(get_option($theme_shortname."_config_goods_consumer_price_view"))?get_option($theme_shortname."_config_goods_consumer_price_view"):"U"; // 소비자가 노출여부

wp_get_current_user();
$currUserID=$current_user->user_login;
$Loginflag='memer';
$result = $wpdb->get_results("select payMode from bbse_commerce_membership where user_id = '".$currUserID."'");
$payMode = $result[0]->payMode;

if($currUserID) $ord_userId=$currUserID;
else $ord_userId="";

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
}

$bbsePage=get_query_var( 'bbsePage' );

emptyCart($currUserID); //보관일 지난 상품 삭제
updateCart($currUserID); //장바구니 업데이트

$V = $_POST;

if(is_user_logged_in() && $V['goods_info']!="" || !is_user_logged_in() && $V['goods_info']!="") {//회원,비회원 바로구매 처리 (로그인 전)
	$goods_info = unserialize(base64_decode($V['goods_info']));
	$goodsInfo = $V['goods_info'];
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE idx='".$goods_info['goods_idx']."'");
	$orderType = "direct";
}else if(is_user_logged_in() && $V['goods_idx'] > 0) {//회원 바로구매 처리 (로그인 후)
	$goodsInfo = base64_encode(serialize($V));
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE idx='".$V['goods_idx']."'");
	$orderType = "direct";
}else if(!is_user_logged_in() && $V['goods_info']=="" && count($V['gidx'])<='0') {
	echo "<script>location.href='".home_url()."/';</script>";
	exit;
}else{
	if(count($V['gidx']) > 0) {//회원 장바구니 구매 처리
		$result = $wpdb->get_results("SELECT C.idx AS cart_idx, C.user_id, C.sid, C.goods_option_basic AS cart_option_basic, C.goods_option_add AS cart_option_add, C.remote_ip, C.reg_date, G.* FROM bbse_commerce_cart AS C, bbse_commerce_goods AS G WHERE C.goods_idx=G.idx AND C.cart_kind='C' AND C.user_id='".$currUserID."' AND C.idx IN (".implode(",",$V['gidx']).") ORDER BY C.idx DESC");
		$orderType = "cart";
	}else{
		echo "<script>location.href='".home_url()."/';</script>";
		exit;
	}
}
if(count($result) < 1) {
	echo "<script>location.href='".home_url()."/';</script>";
	exit;
}

// 결제모듈 설정
$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
if(!$paymentConfig) {
	echo "<script>alert('관리자에서 결제모듈 설정을 먼저 해주세요');location.href='/';</script>";
	exit;
}
$payCFG = unserialize($paymentConfig);

// 적립금 설정
$earnConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='earn'");
$earnCFG =  unserialize($earnConfig);
if( !$earnConfig || $earnCFG['earn_pay_use'] != "on" || !is_user_logged_in() ) $earn_pay_use = "N";
else $earn_pay_use = "Y";

// 주문 설정
$orderConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order'");
$orderCFG = unserialize($orderConfig);

// 회원관리 환경설정
$config = $wpdb->get_row("select * from bbse_commerce_membership_config limit 1");

$order_no = rand(100000000,999999999)."-".current_time('timestamp');// 주문번호

if(is_user_logged_in()) {
	$myInfo=bbse_get_user_information();
	$phone = explode("-",$myInfo->phone);
	$hp = explode("-",$myInfo->hp);
	$zipcode = $myInfo->zipcode;
}
?>
	<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.js"></script><!--https-->

	<script>
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

<hr class="refresher">
  <main id="pageBody">
    <div class="subPage solo widthLimiter">
          <?php
          #로케이션
          get_template_part('part/sub', 'location');
          ?>

      <div class="page_cont mainContent blogView"  id="bbsePage<?php echo $bbsePage?>">
  			<h2 class="page_title stopTOP">주문서작성</h2>
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
          $config = $wpdb->get_row("select * from bbse_commerce_membership_config");
          $use_ssl = get_option("bbse_commerce_ssl_enable");
          $ssl_domain = get_option("bbse_commerce_ssl_domain");
          $ssl_port = get_option("bbse_commerce_ssl_port");
          if($use_ssl == "U" && !empty($ssl_domain)){
            $parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
            $action_url = "https://".$ssl_domain;
            if(!empty($ssl_port)) $action_url .= ":".$ssl_port;
            $action_url .= $parseurl['path'];
          }else{
            $action_url = BBSE_COMMERCE_THEME_WEB_URL;
            //$action_url = "http://localhost/wp-content/themes/Style_Shop";
          }
        ?>
        <form name="order_frm" id="order_frm" method="post"> 
        <input type="hidden" name="action_url" id="action_url" value="<?php echo $action_url; ?>">
        <input type="hidden" name="deviceType" id="deviceType" value="<?php echo $deviceType; ?>">
        <input type="hidden" name="orderType" id="orderType" value="<?php echo $orderType; ?>">
        <input type="hidden" name="gidx" id="gidx" value="<?php echo base64_encode(serialize($V['gidx'])); ?>">
        <input type="hidden" name="goods_info" id="goods_info" value="<?php echo $goodsInfo; ?>">
        <input type="hidden" name="payment_escrow_use" id="payment_escrow_use" value="<?php echo $payCFG['payment_escrow_use']; ?>"><!-- 에스크로 사용여부 -->
        <input type="hidden" name="order_no" id="order_no" value="<?php echo $order_no; ?>">
        <input type="hidden" name="earn_pay_use" id="earn_pay_use" value="<?php echo $earn_pay_use; ?>"><!-- 적립금 사용여부 -->
        <input type="hidden" name="earn_hold_point" id="earn_hold_point" value="<?php echo $earnCFG['earn_hold_point']; ?>"><!-- 사용 가능 적립금 보유액 -->
        <input type="hidden" name="earn_order_pay" id="earn_order_pay" value="<?php echo $earnCFG['earn_order_pay']; ?>"><!-- 주문 합계액 기준 -->
        <input type="hidden" name="earn_min_point" id="earn_min_point" value="<?php echo $earnCFG['earn_min_point']; ?>"><!-- 적립금 최소 사용금액 -->
        <input type="hidden" name="earn_max_percent" id="earn_max_percent" value="<?php echo $earnCFG['earn_max_percent']; ?>"><!-- 적립금 최대 사용금액 -->
        <input type="hidden" name="earn_use_unit" id="earn_use_unit" value="<?php echo $earnCFG['earn_use_unit']; ?>"><!-- 적립금 사용 단위 -->
        <input type="hidden" name="total_pay_unit" id="total_pay_unit" value="<?php echo $orderCFG['total_pay_unit']; ?>"><!-- 주문 총 금액 설정 -->
        <input type="hidden" name="total_pay_round" id="total_pay_round" value="<?php echo $orderCFG['total_pay_round']; ?>">
        <input type="hidden" name="mileage" id="mileage" value="<?php echo $myInfo->mileage; ?>">
		<input type="hidden" name="BBSE_COMMERCE_THEME_WEB_URL" id="BBSE_COMMERCE_THEME_WEB_URL" value="<?php echo BBSE_COMMERCE_THEME_WEB_URL;?>">
		<input type="hidden" name="mobile_Remark" id="mobile_Remark" value="">
		<input type="hidden" name="ord_userId" id="ord_userId" value="<?php echo $ord_userId;?>">
		<input type="hidden" name="pay_status" id="pay_status" value="PW">

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
          
			$total_add_ship_price = 0;//추가 개별배송비 합계
           	$total_add_ship_cnt = 0;//추가 개별배송비 합계
           	$total_item_cnt = 0;//전체 상품 수
           
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
                    if($memPrice['goods_member_level'][$m]==$myInfo->user_class) {
                        $memeberPrice=$memPrice['goods_member_price'][$m];
                        $goods_vat=$memPrice['goods_vat'][$m];
                        $salePrice= $memeberPrice+ $goods_vat;
                    }
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
			  
			  	//개별배송비 추가
				$total_item_cnt++;
				if($cart->goods_ship_tf == 'view'){
					$total_add_ship_price += $cart->goods_ship_price;
					$total_add_ship_cnt++;	
				}
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
                //$delivery_price = $deliveryInfo['delivery_charge'];
                $delivery_tit = " (선불)";
				//개별배송비 계산
              	$no_add_cnt = $total_item_cnt - $total_add_ship_cnt;
				if($no_add_cnt > 0){
					$delivery_price += intval($deliveryInfo['delivery_charge']);
				}
				$delivery_price += $total_add_ship_price;
				$total_price += number_format($delivery_price);
              }else if($deliveryInfo['delivery_charge_payment'] == "deferred"){
                $delivery_price = $deliveryInfo['delivery_charge'];
                if($delivery_price == null || $delivery_price == ""){
                    $delivery_price = 0;
                }
                $delivery_tit = " (후불)";
              }else{
                  $delivery_tit = " (착불)";
              }
            }

			if($deliveryInfo['delivery_charge_type']=='free'){
			  $charge_tit = "무료";
			}else{
			  $charge_tit = "유료";
			  if($deliveryInfo['delivery_charge_payment'] == "advance") {
				$delivery_tit2 = "선불";
			  }else if($deliveryInfo['delivery_charge_payment'] == "deferred"){
				$delivery_tit2 = "후불";
			  }else{
			    $delivery_tit2 = "착불";
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
        <input type="hidden" name="delivery_charge_type" id="delivery_charge_type" value="<?php echo $deliveryInfo['delivery_charge_type']; ?>">
        <input type="hidden" name="condition_free_use" id="condition_free_use" value="<?php echo $deliveryInfo['condition_free_use']; ?>">
        <input type="hidden" name="total_pay" id="total_pay" value="<?php echo $deliveryInfo['total_pay']; ?>">
        <input type="hidden" name="delivery_charge_payment" id="delivery_charge_payment" value="<?php echo $deliveryInfo['delivery_charge_payment']; ?>">
        <input type="hidden" name="delivery_charge" id="delivery_charge" value="<?php echo $delivery_price;//$deliveryInfo['delivery_charge']; ?>">

        <input type="hidden" name="goods_option_price" id="goods_option_price" value="<?php echo $option_all_price; ?>">
        <input type="hidden" name="goods_add_price" id="goods_add_price" value="<?php echo $add_all_price; ?>">
        <input type="hidden" name="delivery_price" id="delivery_price" value="<?php echo $delivery_price; ?>">
        <input type="hidden" name="delivery_add_price" id="delivery_add_price" value="0">
        <input type="hidden" name="usable_mileage" id="usable_mileage" value="<?php echo $real_use_mileage; ?>">
        <input type="hidden" name="add_earn" id="add_earn" value="<?php echo $mileage_expect; ?>">

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
                <li>기본배송비 : <?php echo $delivery_tit2 ?>
                <?php if($deliveryInfo['delivery_charge_payment'] == "advance" || $deliveryInfo['delivery_charge_payment'] == "deferred") echo number_format($deliveryInfo['delivery_charge']); ?>원</li>
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
                      <dd>추가배송비 : <strong><?php echo number_format($deliveryInfo['local_charge_pay_'.$i]); ?></strong>원</dd>
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
              <?php if(is_user_logged_in() && $earn_pay_use == "Y") {?>
              <h3 class="orderTblTitle"><!-- 쿠폰 및  -->적립금 사용</h3>
              <div class="fakeTable orderPageCommon">
                <ul>
                  <li class="bgHave">적립금</li>
                  <li>
                    <input type="text" name="use_earn" id="use_earn" class="ta_r" style="width:20%" validate-number="true" value="" <?php echo $mileage_flag; ?>/> 원
                    &nbsp;&nbsp;
                    <label for="use_all_earn"><input type="checkbox" name="use_all_earn" id="use_all_earn" <?php echo $mileage_flag; ?>/>모두사용</label>
                    (보유적립금 <strong><?php echo number_format($myInfo->mileage); ?></strong>원 / 사용가능 적립금 <strong><?php echo number_format($real_use_mileage); ?></strong>)
                  </li>
                </ul>
                <ul>
                  <li class="bgHave">사용안내</li>
                  <li><?php echo $mileage_info; ?></li>
                </ul>
              </div><!-- fakeTable -->
              <div class="clearFloat"></div>
              <?php } ?>
              
              <?php if(is_user_logged_in()) {?>
              	<?php
              	
              	//회원할인
              	$user_class = $wpdb->get_var('
                	SELECT	user_class
                	FROM	bbse_commerce_membership
                	WHERE	user_id = "'.$current_user->user_login.'"
                ');
              	
                  	$date = new DateTime();
                  	$date_result = $date->format('Y-m-d');
                  	
              		$item_id = array();
              		foreach($result as $cart) {
              			$item_id[]= $cart->idx;
					}
					$item_sql = '1 ';
					foreach ($item_id as $key => $value) {
						$item_sql .= " AND product LIKE '%\"".$value."\"%'";
					}
              		//사용가능 쿠폰
              		$sql = '
						SELECT	*
						FROM	bbse_commerce_coupon
						WHERE	product_type = "all"
                          AND   user_class = "'.$user_class.'"
                          AND edate > date_format("'.$date_result.'" , "%Y%m%d")
							OR
								(product_type="noall" 
									AND ('.$item_sql.'))
					';
              		              		
              		$coupons = $wpdb->get_results($sql);
              		
              		$avaliablecoupon = [];
              		foreach ($coupons as $key => $value) {
              		    $coupon = unserialize($value->user_ids);
              		    if(in_array($currUserID, $coupon)){
              		        array_push($avaliablecoupon,$value->idx);
              		    }
              		}
              		
              		$escaped = array_map(function($item) {
              		    return "'" . addslashes($item) . "'";
              		}, $avaliablecoupon);
              		    
          		    $in = implode(',', $escaped);
          		    
          		    $sqlUse = '
        				SELECT	*
        				FROM	bbse_commerce_coupon
        				WHERE	product_type = "all"
                          and idx in ('.$in.')
                          AND edate >= date_format("'.$date_result.'" , "%Y%m%d")
        					OR
        						(product_type="noall"
        							AND ('.$item_sql.'))
        			';
              		    
          		    $coupons_2 = $wpdb->get_results($sqlUse);
              		    
              		foreach ($coupons as $data){
              		    $user_coupon = unserialize($data->user_ids);
              		}
              		
					$use_coupons = $wpdb->get_results('
						SELECT	coupon_id
						FROM	bbse_commerce_coupon_log 
						WHERE	user = "'.$currUserID.'"
					');
					$use_coupons_arr = array();
					foreach ($use_coupons as $key => $value) {
						$use_coupons_arr[]= $value->coupon_id;
					}
					$coupon_cnt = 0;
					foreach ($coupons_2 as $key => $value) {
						if(in_array($value->idx, $use_coupons_arr)) continue;
						$coupon_cnt++;
					}
					//종이쿠폰
					$sql = '
						SELECT	*
						FROM	bbse_commerce_paper_coupon 
						WHERE	user = "'.$currUserID.'" AND status IS NULL AND min_money < "'.$total_goods_price.'"
					';
		      		$paper_coupons = $wpdb->get_results($sql);
		      		foreach ($paper_coupons as $key => $value) {
						$coupon_cnt++;
					}
              	?>
              <h3 class="orderTblTitle">쿠폰 사용</h3>
              <div class="fakeTable orderPageCommon">
                <ul>
                  <li class="bgHave">쿠폰 할인</li>
                  <li>
                  	<input type="hidden" name="coupon_discount" id="coupon_discount" value="" readonly="" />
                  	<span><span id="coupon_discount_1">0</span>원</span>
                    <input type="hidden" name="" value="" />
                    <button class="bb_btn shadow <?php echo ($coupon_cnt > 0 ? '':''); ?>" id="apply_coupon" >쿠폰사용</button>
                    <span>(사용가능 쿠폰: <?php echo $coupon_cnt; ?>장)</span>
                  </li>
                </ul>
                <?php

				$user_discount = $wpdb->get_var('
                	SELECT	discount
                	FROM	bbse_commerce_membership_class
                	WHERE	no = "'.$user_class.'"
                ');
				$user_discount = round($total_price * ($user_discount/100));
				//$total_goods_price -= $user_discount;
				$total_price -= $user_discount;
                ?>
                <input type="hidden" name="coupon_total" value="" />
                <input type="hidden" name="user_discount" value="<?php echo $user_discount; ?>"/>
                <input type="hidden" name="coupon_delivery_price" value="<?php echo $delivery_price; ?>"/>
				<input type="hidden" name="coupon" value="" />
				<input type="hidden" name="pcoupon" value="" />
              </div><!-- fakeTable -->
              <div class="clearFloat"></div>
              <script>
              	jQuery(document).ready(function($) {
              		$('#apply_coupon').click(function(){
              			if($(this).hasClass('disabled')){
              				return false;
              			}
              			window.open("<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'inc/coupon_popup.php/?total='.$total_goods_price.'&item='.implode(',',$item_id); ?>", "쿠폰적용", "width=500,height=400,left=100,top=100,location=no,toolbars=no,status=no");
              			//window.open("<?php echo 'http://localhost/wp-content/plugins/BBSe_Commerce/inc/coupon_popup.php/?total='.$total_goods_price.'&item='.implode(',',$item_id); ?>", "쿠폰적용", "width=500,height=400,left=100,top=100,location=no,toolbars=no,status=no");
              			return false;
              		});
              	});
              </script>
              <?php } ?>

              <h3 class="orderTblTitle">주문하시는 분</h3>
              <div class="fakeTable orderPageCommon">
                <ul>
                  <li class="bgHave">이름</li>
                  <li>
                    <input type="text" name="order_name" id="order_name" class="order_orderName" title="이름을 입력해주세요." value="<?php echo $myInfo->name; ?>"/>
                    <?php if(is_user_logged_in()) {?>
                    <label for="equal_memberinfo1"><input type="radio" name="equal_memberinfo" id="equal_memberinfo1" value="equal"/>회원정보 동일</label>
                    <label for="equal_memberinfo2"><input type="radio" name="equal_memberinfo" id="equal_memberinfo2" value="new"/>새로운 정보</label>
                    <?php }?>
                  </li>
                </ul>
                <ul>
                  <li class="bgHave">주소</li>
                  <li>
                    <input type="text" name="order_zip" id="order_zip" style="width:70px;text-align:center;" title="우편번호를 조회해주세요." readonly value="<?php echo $zipcode; ?>" />
                    <button type="button" class="bb_btn shadow order-zipcode" onclick="<?php echo $zipcodeScript1; ?>"><span class="sml">우편번호검색</span></button>
                    <div class="input_wrap">
                      <input type="text" name="order_addr1" id="order_addr1" style="min-width:310px;width:50%" title="주소를 입력해주세요." readonly value="<?php echo $myInfo->addr1; ?>"/>
                      <input type="text" name="order_addr2" id="order_addr2" style="min-width:250px;width:40%" title="나머지 주소를 입력해주세요." value="<?php echo $myInfo->addr2; ?>"/>
                    </div>
                  </li>
                </ul>
			<?php if($orderCFG['order_phone_use']!='N'){?>
                <ul>
                  <li class="bgHave">전화번호</li>
                  <li>
                    <input type="text" name="order_phone1" id="order_phone1" style="width:50px" maxlength="4" <?php echo ($orderCFG['order_phone_use']!='C')?"title='전화번호 국번을 입력해주세요.' validate-number='true'":"";?> value="<?php echo $phone[0]; ?>"/>
                    -
                    <input type="text" name="order_phone2" id="order_phone2" style="width:50px" maxlength="4" <?php echo ($orderCFG['order_phone_use']!='C')?"title='전화번호 중간번호를 입력해주세요.' validate-number='true'":"";?> value="<?php echo $phone[1]; ?>"/>
                    -
                    <input type="text" name="order_phone3" id="order_phone3" style="width:50px" maxlength="4" <?php echo ($orderCFG['order_phone_use']!='C')?"title='전화번호 마지막번호를 입력해주세요.' validate-number='true'":"";?> value="<?php echo $phone[2]; ?>"/>
                  </li>
                </ul>
			<?php }?>
                <ul>
                  <li class="bgHave">휴대전화번호</li>
                  <li>
                    <input type="text" name="order_hp1" id="order_hp1" style="width:50px" title="휴대폰번호 국번을 입력해주세요." maxlength="4" validate-number="true" value="<?php echo $hp[0]; ?>"/>
                    -
                    <input type="text" name="order_hp2" id="order_hp2" style="width:50px" title="휴대폰번호 중간번호를 입력해주세요." maxlength="4" validate-number="true" value="<?php echo $hp[1]; ?>"/>
                    -
                    <input type="text" name="order_hp3" id="order_hp3" style="width:50px" title="휴대폰번호 마지막번호를 입력해주세요." maxlength="4" validate-number="true" value="<?php echo $hp[2]; ?>"/>
                  </li>
                </ul>
                <ul>
                  <li class="bgHave">이메일</li>
                  <li>
                    <input type="text" name="order_email" id="order_email" style="width:50%" title="" value="<?php echo $myInfo->email; ?>"/>
                  </li>
                </ul>
                <?php
                	$pass_num = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order' order by idx asc");
					$pass_num = unserialize($pass_num);
					$pass_num = $pass_num['pass_num_use'];
					if($pass_num == 'on'):
                ?>
                <ul>
                  <li class="bgHave order_pass_num_li">개인통관번호</li>
                  <li class="order_pass_num_li">
                    <input type="text" name="order_pass_num" id="order_pass_num" style="width:50%" title="개인통관번호를 입력해주세요." value="<?php echo ''; ?>"/>    
                	<a href="https://unipass.customs.go.kr/csp/persIndex.do" target="_blank">개인통관번호 확인하기</a>
                  </li>
                </ul>
                <?php
					endif;
                ?>
              </div><!-- fakeTable -->
              <div class="clearFloat"></div>

              <h3 class="orderTblTitle">상품 받으시는 분</h3>
              <div class="fakeTable orderPageCommon">
                <ul>
                  <li class="bgHave">이름</li>
                  <li>
                    <input type="text" name="receive_name" id="receive_name" class="order_orderName" title="이름을 입력해주세요."/>
                    <label for="equal_orderinfo1"><input type="radio" name="equal_orderinfo" id="equal_orderinfo1" value="equal"/>주문하시는 분과 같은 주소</label>
                    <label for="equal_orderinfo2"><input type="radio" name="equal_orderinfo" id="equal_orderinfo2" value="new"/>새로운 주소</label>
                  </li>
                </ul>
                <ul>
                  <li class="bgHave">주소</li>
                  <li>
                    <input type="text" name="receive_zip" id="receive_zip" style="width:70px;text-align:center;" title="우편번호를 조회해주세요." readonly/>
                    <button type="button" class="bb_btn shadow order-zipcode" onclick="<?php echo $zipcodeScript2; ?>"><span class="sml">우편번호검색</span></button>
                    <div class="input_wrap">
                      <input type="text" name="receive_addr1" id="receive_addr1" style="min-width:310px;width:50%" title="주소를 입력해주세요." readonly/>
                      <input type="text" name="receive_addr2" id="receive_addr2" style="min-width:250px;width:40%" title="나머지 주소를 입력해주세요." />
                    </div>
                  </li>
                </ul>
			<?php if($orderCFG['order_phone_use']!='N'){?>
                <ul>
                  <li class="bgHave">전화번호</li>
                  <li>
                    <input type="text" name="receive_phone1" id="receive_phone1" style="width:50px" maxlength="4" <?php echo ($orderCFG['order_phone_use']!='C')?"title='전화번호 국번을 입력해주세요.' validate-number='true'":"";?> />
                    -
                    <input type="text" name="receive_phone2" id="receive_phone2" style="width:50px" maxlength="4" <?php echo ($orderCFG['order_phone_use']!='C')?"title='전화번호 중간번호를 입력해주세요.' validate-number='true'":"";?>/>
                    -
                    <input type="text" name="receive_phone3" id="receive_phone3" style="width:50px" maxlength="4" <?php echo ($orderCFG['order_phone_use']!='C')?"title='전화번호 마지막번호를 입력해주세요.' validate-number='true'":"";?>/>
                  </li>
                </ul>
			<?php }?>
                <ul>
                  <li class="bgHave">휴대전화번호</li>
                  <li>
                    <input type="text" name="receive_hp1" id="receive_hp1" style="width:50px" title="휴대폰번호 국번을 입력해주세요." maxlength="4" validate-number="true"/>
                    -
                    <input type="text" name="receive_hp2" id="receive_hp2" style="width:50px" title="휴대폰번호 중간번호를 입력해주세요." maxlength="4" validate-number="true"/>
                    -
                    <input type="text" name="receive_hp3" id="receive_hp3" style="width:50px" title="휴대폰번호 마지막번호를 입력해주세요." maxlength="4" validate-number="true"/>
                  </li>
                </ul>
                <ul>
                  <li class="bgHave">남기실말씀</li>
                  <li>
                    <textarea name="order_comment" id="order_comment" title="남기실말씀을 입력해주세요"></textarea>
                  </li>
                </ul>
              </div><!-- fakeTable -->
              <div class="clearFloat"></div>

              <h3 class="orderTblTitle">결제정보</h3>
              <div class="fakeTable orderPageCommon">
                <ul>
                  <li class="bgHave">결제방법</li>
                  <li id="payhow_content">
                    <?php if($payCFG['payment_card']=="card") {?><label for="pay_how1"><input type="radio" name="pay_how" id="pay_how1" value="C"/>신용카드</label><?php }?>
                    <?php if($payCFG['payment_bank']=="bank") {
                        if($payMode == '01'){
                    ?>
                    <label for="pay_how2"><input type="radio" name="pay_how" id="pay_how2" value="B"/>월말결제&nbsp;&nbsp;
                    <?php
                        }else if($payMode == '02'){
                    ?>
                    <label for="pay_how5"><input type="radio" name="pay_how" id="pay_how5" value="E"/>건별결제</label></label>
                    <?php 
                        }
                    }?>
                    <?php if($payCFG['payment_trans']=="trans" && $deviceType == "desktop") {?><label for="pay_how3"><input type="radio" name="pay_how" id="pay_how3" value="K"/>실시간 계좌이체</label><?php }?>
                    <?php if($payCFG['payment_vbank']=="vbank") {?><label for="pay_how4"><input type="radio" name="pay_how" id="pay_how4" value="V"/>가상계좌</label><?php }?>
                  </li>
                </ul>
			<?php
			// 간편결제 설정
			$ezpayConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='ezpay'");
			if($ezpayConfig) $ezpayCFG=unserialize($ezpayConfig);
			if($ezpayCFG['paynow']['paynow_use_yn']=='Y' || $ezpayCFG['kakaopay']['kakaopay_use_yn']=='Y' || $ezpayCFG['payco']['payco_use_yn']=='Y' || $ezpayCFG['kpay']['kpay_use_yn']=='Y'){
			?>
                <ul>
                  <li class="bgHave">간편결제</li>
                  <li id="ezpay_content">
                    <?php if($ezpayCFG['paynow']['paynow_use_yn']=='Y') {?><label for="pay_how5"><input type="radio" name="pay_how" id="pay_how5" value="EPN"/><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/ezpay_paynow.png" onClick='jQuery(":radio[value=EPN]").attr("checked", true);' class="ezpay_icon" align="absmiddle" alt="Paynow 결제" /></label><?php }?>
                    <?php if($ezpayCFG['kakaopay']['kakaopay_use_yn']=='Y') {?><label for="pay_how6"><input type="radio" name="pay_how" id="pay_how6" value="EKA"/><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/ezpay_kakaopay.png" onClick='jQuery(":radio[value=EKA]").attr("checked", true);' class="ezpay_icon"  align="absmiddle" alt="KakaoPay 결제" /></label><?php }?>
                	<?php if($nPayData['naver_pay_use2']=='on') {?><label for="npay"><input type="radio" name="pay_how" id="npay" value="NPAY"/><img src="<?php echo BBSE_COMMERCE_THEME_WEB_URL;?>/images/icon_npay.png" onClick='jQuery(":radio[value=NPAY]").attr("checked", true);' class="ezpay_icon"  align="absmiddle" alt="네이버페이 결제" /></label><?php }?>
                    <?php if($ezpayCFG['payco']['payco_use_yn']=='Y') {?><label for="pay_how7"><input type="radio" name="pay_how" id="pay_how7" value="EPA"/><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/ezpay_payco.png" onClick='jQuery(":radio[value=EPA]").attr("checked", true);' class="ezpay_icon"  align="absmiddle" alt="PAYCO 결제" /></label><?php }?>
                    <?php if($ezpayCFG['kpay']['kpay_use_yn']=='Y') {?><label for="pay_how8"><input type="radio" name="pay_how" id="pay_how8" value="EKP"/><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/ezpay_kpay.png" onClick='jQuery(":radio[value=EKP]").attr("checked", true);' class="ezpay_icon"  align="absmiddle" alt="KPAY 결제" /></label><?php }?>	
                  </li>
                </ul>
			<?php 
			}
			?>
                <ul id="payment_bank_info">
                  <li class="bgHave">입금계좌</li>
                  <li>
                    <select name="pay_info" id="pay_info">
                    <?php
                    $bankRes = $wpdb->get_results("select * from bbse_commerce_config where config_type='bank' order by idx asc");
                    foreach($bankRes as $banks) {
                      $bank = unserialize($banks->config_data);
                      if($bank['bank_info_use']=="on") {
                        echo "<option value=\"".base64_encode(serialize($bank))."\">".$bank['bank_name']." ".$bank['bank_account_number']." 예금주: ".$bank['bank_owner_name']."</option>";
                      }
                    }
                    ?>
                    </select>
                    입금자명 <input type="text" name="input_name" id="input_name" class="order_orderName" title="입금자명을 입력해주세요." />
                  </li>
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
                      <span>(-) <span id="payview_use_earn_price">0</span>원</span>
                    </li>
                    <li>
                      <em>쿠폰사용</em>
                      <span>(-) <span id="payview_coupon_price">0</span>원</span>
                    </li>
<!--                     <li>
                      <em>회원할인</em>
                      <span>(-) <span id="payview_user_discount"><?php echo number_format($user_discount); ?></span>원</span>
                    </li> -->
                    <li>
                      <em>합계</em>
                      <span><span id="payview_total_goods_earn_price"><?php echo number_format($total_goods_price - $user_discount); ?></span>원</span>
                    </li>
                  </ul>
                  <ul>
                    <li>
                      배송비<label id="delivery_tit"><?php echo $delivery_tit;?></label>
                      <span>+<span id="payview_delivery_price"><?php echo number_format($delivery_price); ?></span>원</span>
                    </li>
                  </ul>

                  <ul>
                    <li>
                      <em>결제금액</em>
                      <span><strong><span id="payview_total_price"><?php echo number_format($total_price + $delivery_price); ?></span></strong>원</span>
                      <input type="hidden" name="total_price" id="total_price" value="<?php echo $total_price + $delivery_price; ?>" />
                    </li>
                    <li>
                      <em>결제수단</em>
                      <span id="pay_how_view">-</span>
                    </li>
                    <li>
                      <em>예상적립금</em>
                      <span><span id="payview_mileage_expect"><?php echo ($Loginflag=='guest')?"0":number_format($mileage_expect); ?></span>원</span>
                    </li>
                  </ul>
                  <p>
                    주문하실 상품, 가격, 배송정보, 할인내역등을<br>
                    최종확인하였으며, 구매에 동의하시겠습니까?
                    <label for="orderPayConfim"><input type="checkbox" name="orderAgreeConfirm" id="orderAgreeConfirm" value="Y"/> <strong>동의합니다.</strong> <em>(전자상거래 제 8조 제2항)</em></label>
                  </p>
                  <div class="bb_btn_area">
                    <button type="button" class="bb_btn cus_fill pay-action"><strong class="big">주문하기</strong></button>
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

	<script language=javascript src="<?php echo BBSE_COMMERCE_THEME_WEB_URL;?>/js/order_check.js?ver=1.1"></script>

  </main>
<hr class="refresher">
<div style="clear:both;margin-bottom:100px;"></div>
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
				delivery_change('addrChang');
			}
		}).open();
	}
</script>
<?php }?>
<?php get_footer();
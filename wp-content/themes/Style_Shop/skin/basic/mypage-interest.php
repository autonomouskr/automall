<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* Wishlist */
get_header();

global $current_user,$theme_shortname,$currentSessionID;

wp_get_current_user();

if(is_user_logged_in()) {
    $myInfo=bbse_get_user_information();
}

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

if(!is_user_logged_in() && $Loginflag!='social'){
	echo "<script>location.href='".get_permalink(get_option($theme_shortname."_login_page"))."';</script>";
	exit;
}

$result = $wpdb->get_results("SELECT A.idx AS cart_idx, B.* FROM bbse_commerce_cart AS A, bbse_commerce_goods AS B WHERE A.goods_idx=B.idx AND A.cart_kind='W' AND A.user_id='".$currUserID."' ORDER BY A.idx DESC");
?>
		<h2 class="page_title">관심상품(찜)</h2>
		<div class="article">
			<ul class="bb_dot_list">
				<li>보관함에 담긴상품 <strong id="wishlistTotal" class="c_point"><?php echo number_format(count($result));?></strong>개가 있습니다.</li>
			</ul>
		</div>

		  <form name="wishlistFrm" id="wishlistFrm">
		  <input type="hidden" name="tMode" id="tMode" value="">
		  <input type="hidden" name="home_url" id="home_url" value="<?php echo home_url();?>" />
		  <input type="hidden" name="goods_template_url" id="goods_template_url" value="<?php echo bloginfo('template_url');?>" />

          <div class="fakeTable wishList">
            <ul class="header">
              <li><label class="blind" for="allChkSelectF">전체선택</label><input type="checkbox" name="" id="allChkSelect" /></li>
              <li>상품명</li>
              <li>상품금액</li>
              <li></li>
            </ul>
		<?php
		if(count($result) > 0) {
			foreach($result as $wData) {
			    if($wData->goods_basic_img){
			        $basicImg = wp_get_attachment_image_src($wData->goods_basic_img,"goodsimage2");
			        $salePrice=0;
			        $memPrice=unserialize($wData->goods_member_price);
			        for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
			            if($memPrice['goods_member_level'][$m]==$myInfo->user_class) {
			                $memberPrice=$memPrice['goods_member_price'][$m];
			                $goods_vat=$memPrice['goods_vat'][$m];
			                $salePrice=$memberPrice+$goods_vat;
			                $cPrice=$memPrice['goods_consumer_price'][$m];
			            }
			        }
			        $savePrice=$cPrice-$salePrice;
			    }
				else{
					$imageList=explode(",",$wData->goods_add_img);
					if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage2");
					else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
				}
		?>
            <ul id="wishlist_<?php echo $wData->cart_idx; ?>">
              <li><label class="blind" for="dummyID59">선택</label><input type="checkbox" name="gidx[]" id="gidx_<?php echo $wData->cart_idx; ?>" value="<?php echo $wData->cart_idx; ?>"/></li>
              <li class="firstCell">
                <div class="goodsBaseInfo">
                  <img src="<?php echo $basicImg['0']; ?>" title="<?php echo $cart->goods_name; ?>" />
                  <a href="<?php echo home_url()."/?bbseGoods=".$wData->idx; ?>" class="subj"><?php echo $wData->goods_name;?></a>
                </div>
              </li>
              <li>
                <span class="mobile-cell-title">상품금액</span>
                <span class="cell-data"><del><?php echo number_format($cPrice);?>원</del><br><?php echo number_format($salePrice);?>원</span>
              </li>
              <li class="wishListBtns">
				<button type="button" class="bb_btn shadow openLayer" data-name="goodsOptionChanger" data-ids="W^<?php echo $wData->cart_idx; ?>"><span class="sml">장바구니</span></button>
                <button type="button" onClick="remove_wishlist(<?php echo $wData->cart_idx; ?>);" class="bb_btn shadow"><span class="sml">삭제</span></button>
              </li>
            </ul>
		<?php
			}
		?>
          </div><!-- fakeTable -->
          <div class="nodata" id="emptyWishlist" style="display:none;">관심상품(찜)에 담겨 있는 상품이 없습니다.</div>
		<?php
		}
		else{
		?>
          </div><!-- fakeTable -->

          <div class="nodata">관심상품(찜)에 담겨 있는 상품이 없습니다.</div>
		<?php
		}
		?>




		  </form>
          <div class="clearFloat"></div>

        <div class="bb_btn_area tb_opt_chk">
          <div class="bb_left">
            <button class="bb_btn shadow" onClick="remove_wishlist('');"><span class="sml">선택상품 삭제</span></button>
          </div>
        </div>

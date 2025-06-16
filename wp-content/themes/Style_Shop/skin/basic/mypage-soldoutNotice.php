<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

/* Soldout Notice */
get_header();

global $current_user,$theme_shortname,$currentSessionID;

wp_get_current_user();

$currUserID=$current_user->user_login;

if(!is_user_logged_in()){
	echo "<script>location.href='".get_permalink(get_option($theme_shortname."_login_page"))."';</script>";
	exit;
}

$result = $wpdb->get_results("SELECT A.idx AS notice_idx, A.*, B.* FROM bbse_commerce_soldout_notice AS A, bbse_commerce_goods AS B WHERE A.goods_idx=B.idx AND A.user_id='".$currUserID."' ORDER BY A.idx DESC");
?>
		<h2 class="page_title">품절상품 입고알림</h2>
		<div class="article">
			<ul class="bb_dot_list">
				<li>품절상품 입고알림 목록이 <strong id="noticelistTotal" class="c_point"><?php echo number_format(count($result));?></strong>개가 있습니다.</li>
			</ul>
		</div>

		  <form name="noticeListFrm" id="noticeListFrm">
		  <input type="hidden" name="tMode" id="tMode" value="delete">
		  <input type="hidden" name="home_url" id="home_url" value="<?php echo home_url();?>" />
		  <input type="hidden" name="goods_template_url" id="goods_template_url" value="<?php echo bloginfo('template_url');?>" />

          <div class="fakeTable noticeList">
            <ul class="header">
              <li><label class="blind" for="allChkSelectF">전체선택</label><input type="checkbox" name="" id="allChkSelectNotice" /></li>
              <li>상품명</li>
              <li>상품금액</li>
              <li>알림신청 정보</li>
              <li>알림 신청일</li>
              <li>삭제</li>
            </ul>
		<?php
		if(count($result) > 0) {
			foreach($result as $nData) {
				if($nData->goods_basic_img) $basicImg = wp_get_attachment_image_src($nData->goods_basic_img,"goodsimage2");
				else{
					$imageList=explode(",",$nData->goods_add_img);
					if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage2");
					else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
				}

				$ntHp=trim(str_replace("-","",$nData->hp));
				$ntEmail=trim($nData->email);
		?>
            <ul id="noticelist_<?php echo $nData->notice_idx; ?>">
              <li style="text-align:center;"><label class="blind" for="dummyID59">선택</label><input type="checkbox" name="nidx[]" id="nidx_<?php echo $nData->notice_idx; ?>" value="<?php echo $nData->notice_idx; ?>"/></li>
              <li class="firstCell" style="width:30%;">
                <div class="goodsBaseInfo">
                  <img src="<?php echo $basicImg['0']; ?>" title="<?php echo $cart->goods_name; ?>" />
                  <a href="<?php echo home_url()."/?bbseGoods=".$nData->idx; ?>" class="subj"><?php echo $nData->goods_name;?></a>
                </div>
              </li>
              <li style="text-align:center;">
                <span class="mobile-cell-title">상품금액</span>
                <span class="cell-data"><del><?php echo number_format($nData->goods_consumer_price);?>원</del><br><?php echo number_format($nData->goods_price);?>원</span>
              </li>
              <li style="text-align:center;">
                <span class="mobile-cell-title">알림신청 정보</span>
                <span class="cell-data">
				<?php if($ntHp){?>
					<?php echo $ntHp;?> <span style="font-size:11px;"><?php echo ($nData->sms_yn=='Y')?"<font color='#00A2E8'>(알림완료)</font>":"<font color='ED1C24'>(알림전)</font>";?></span><br />
				<?php }?>
				<?php if($ntEmail){?>
					<?php echo $ntEmail;?> <span style="font-size:11px;"><?php echo ($nData->email_yn=='Y')?"<font color='#00A2E8'>(알림완료)</font>":"<font color='ED1C24'>(알림전)</font>";?></span>
				<?php }?>
				</span>
              </li>
              <li style="text-align:center;">
                <span class="mobile-cell-title">알림신청일</span>
                <span class="cell-data"><?php echo date("Y-m-d",$nData->reg_date);?></span>
              </li>

              <li class="noticeListBtns" style="text-align:center;">
                <button type="button" onClick="remove_noticeList(<?php echo $nData->notice_idx; ?>);" class="bb_btn shadow"><span class="sml">삭제</span></button>
              </li>
            </ul>
		<?php
			}
		?>
          </div><!-- fakeTable -->
          <div class="nodata" id="emptyNoticelist" style="display:none;">품절상품 입고알림 목록이 존재하지 않습니다.</div>
		<?php
		}
		else{
		?>
          </div><!-- fakeTable -->

          <div class="nodata">품절상품 입고알림 목록이 존재하지 않습니다.</div>
		<?php
		}
		?>

		  </form>
          <div class="clearFloat"></div>

        <div class="bb_btn_area tb_opt_chk">
          <div class="bb_left">
            <button class="bb_btn shadow" onClick="remove_noticeList('');"><span class="sml">선택 삭제</span></button>
          </div>
        </div>
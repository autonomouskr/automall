<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_layout_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='layout' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_layout_preOpen' id='<?php echo $theme_shortname?>_layout_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">메인화면 설정 - 레이아웃 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_layout_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">

<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">사이드바 설정 (왼쪽 사이드바 사용을 설정합니다.)</div>
		<div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p style="font-weight:bold;">*사이드바 구성내용</p>
				쇼핑카테고리 / 가격검색 / 오늘만의 특가 / 왼쪽배너 / 핫아이템 / 입금계좌정보가 구성되어 있습니다.
			</div>
			<br />
			<div style="color:#0173da;">* 전부 사용하지 않을 경우 공백으로 처리됩니다</div>
			<dl style="border-top:2px solid #4c99ba;">
			  <dt>왼쪽 쇼핑<br />카테고리 리스트</dt>
			  <dd>
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_category" id="<?php echo $theme_shortname?>_layout_left_category1" value='Y' <?php echo (get_option($theme_shortname."_layout_left_category")=='Y')?'checked="checked"':''?> style='border:0px;' onClick="jQuery('#layoutLeftCategory_bold').css('display','block');" /><label for="<?php echo $theme_shortname?>_layout_left_category1">사용</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_category" id="<?php echo $theme_shortname?>_layout_left_category2" value='N' <?php echo (get_option($theme_shortname."_layout_left_category")=='N')?'checked="checked"':''?> style='border:0px;' onClick="jQuery('#layoutLeftCategory_bold').css('display','none');" /><label for="<?php echo $theme_shortname?>_layout_left_category2">사용안함</label>

				<div id="layoutLeftCategory_bold" style="height:25px;display:<?php echo (get_option($theme_shortname."_layout_left_category")=='Y')?"block":"none";?>;">- 카테고리 글씨 : <input type="checkbox" name="<?php echo $theme_shortname?>_layout_left_category_bold" id="<?php echo $theme_shortname?>_layout_left_category_bold" value="bold" <?php echo (get_option($theme_shortname."_layout_left_category_bold")=='bold')?"checked='checked'":"";?>>진하게 표시</div>
			  </dd>
			  <dd><p class="desc2">사용안함 선택 시 : 주메뉴 하단에 카테고리가 적용됩니다. (1차 카테고리가 많은 경우 권장하지 않습니다.)</p></dd>
			</dl>
			

			<dl>
			  <dt>가격검색</dt>
			  <dd>
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_price_search" id="<?php echo $theme_shortname?>_layout_left_price_search1" value='Y' <?php echo (get_option($theme_shortname."_layout_left_price_search")=='Y')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_price_search1">사용</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_price_search" id="<?php echo $theme_shortname?>_layout_left_price_search2" value='N' <?php echo (get_option($theme_shortname."_layout_left_price_search")=='N')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_price_search2">사용안함</label>
			  </dd>
			</dl>

			<dl>
			  <dt>오늘만의 특가</dt>
			  <dd>
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_today_sale" id="<?php echo $theme_shortname?>_layout_left_today_sale1" value='Y' <?php echo (get_option($theme_shortname."_layout_left_today_sale")=='Y')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_today_sale1">사용</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_today_sale" id="<?php echo $theme_shortname?>_layout_left_today_sale2" value='N' <?php echo (get_option($theme_shortname."_layout_left_today_sale")=='N')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_today_sale2">사용안함</label>
			  </dd>
			</dl>

			<dl>
			  <dt>왼쪽배너</dt>
			  <dd>디스플레이 설정에서 설정해 주세요.</dd>
			</dl>

			<dl>
			  <dt>핫아이템</dt>
			  <dd>
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_hot_item" id="<?php echo $theme_shortname?>_layout_left_hot_item1" value='Y' <?php echo (get_option($theme_shortname."_layout_left_hot_item")=='Y')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_hot_item1">사용</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_hot_item" id="<?php echo $theme_shortname?>_layout_left_hot_item2" value='N' <?php echo (get_option($theme_shortname."_layout_left_hot_item")=='N')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_hot_item2">사용안함</label>
			  </dd>
			</dl>

			<dl>
			  <dt>입금계좌정보</dt>
			  <dd>
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_bank_info" id="<?php echo $theme_shortname?>_layout_left_bank_info1" value='Y' <?php echo (get_option($theme_shortname."_layout_left_bank_info")=='Y')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_bank_info1">사용</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_layout_left_bank_info" id="<?php echo $theme_shortname?>_layout_left_bank_info2" value='N' <?php echo (get_option($theme_shortname."_layout_left_bank_info")=='N')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_left_bank_info2">사용안함</label>
			  </dd>
			</dl>
			<dl>
			  <dd>
				<p class="desc2">아래 내용에는 제한됩니다.</p>
				&nbsp;- 사이드바 제공하지 않음 : 상품리스트 / 장바구니 / 주문서작성 / 주문완료 화면<br />
				&nbsp;- 별도 사이드 메뉴 제공함 : 로그인, 회원가입류 화면 / 마이페이지
			  </dd>
			</dl>
			<br />
			<div class="slideItemsTitle">최근 본 상품 설정 (오른쪽 사이드바 사용을 설정합니다.)</div>
			<dl>
			  <dt>최근 본 상품</dt>
			  <dd>
				<input type="radio" name="<?php echo $theme_shortname?>_layout_right_last_goods" id="<?php echo $theme_shortname?>_layout_right_last_goods1" value='Y' <?php echo (get_option($theme_shortname."_layout_right_last_goods")=='Y')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_right_last_goods1">사용</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_layout_right_last_goods" id="<?php echo $theme_shortname?>_layout_right_last_goods2" value='N' <?php echo (get_option($theme_shortname."_layout_right_last_goods")=='N')?'checked="checked"':''?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_layout_right_last_goods2">사용안함</label>
			  </dd>
			</dl>
			<dl>
			  <dt>오른쪽 배너</dt>
			  <dd>디스플레이 설정에서 설정해 주세요.</dd>
			</dl>
		</div>
      </li><!-- 레이아웃 -->
<!-- ******************************************************************************************************************************************************* -->
<li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">관리자 로그인 화면 설정</div>
            </div>
		</div>
		<div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p>(*) 관리자 로그인화면(/wp-admin) 접속시 나오는 로고, 배경색상, 텍스트 문구를 수정 할 수 있습니다. </p>
			</div>
			<br />
			<dl style="border-bottom: 0px;">
			  	<dt>로고(310 x 100px)</td>
				<dd>
				  	<input style="width: 70%;" type="text" name="<?php echo $theme_shortname?>_wpadmin_logo_img" id="<?php echo $theme_shortname?>_wpadmin_logo_img" value='<?php echo get_option($theme_shortname."_wpadmin_logo_img");?>' />
				  	<input class='button-secondary' onClick="upload_img('wpadmin_logo_img');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
			  	</dd>
			</dl>
			<dl style="border-bottom: 0px;">
			  	<dt>텍스트 문구</td>
				<dd>
				  	<input style="width: 100%;" type="text" name="<?php echo $theme_shortname?>_wpadmin_txt" id="<?php echo $theme_shortname?>_wpadmin_txt" value='<?php echo get_option($theme_shortname."_wpadmin_txt");?>' />
			  	</dd>
			</dl>
			<dl style="border-bottom: 0px;">
			  	<dt>배경색</td>
				<dd>
				  	<input type="text" placeholder="#ffffff" name="<?php echo $theme_shortname?>_wpadmin_bg" id="<?php echo $theme_shortname?>_wpadmin_bg" value='<?php echo get_option($theme_shortname."_wpadmin_bg");?>' />
			  	</dd>
			</dl>
		</div>
      </li><!-- /로그인화면설정-->
    </ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('layout');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('layout');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->


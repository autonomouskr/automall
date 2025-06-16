<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_display_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='display' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_display_preOpen' id='<?php echo $theme_shortname?>_display_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">메인화면 설정 - 디스플레이 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_display_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use   = get_option($theme_shortname."_display_use_mobile_icon")=='U'?'yes':'no';
		$show = get_option($theme_shortname."_display_use_mobile_icon")=='U'?'':'style="display:none"';
		$mobile_icon_count = get_option($theme_shortname."_display_use_mobile_icon_count")?get_option($theme_shortname."_display_use_mobile_icon_count"):0;
		$mobile_icon_last = get_option($theme_shortname."_display_use_mobile_icon_last")?get_option($theme_shortname."_display_use_mobile_icon_last"):0;
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">모바일 아이콘 메뉴</div>
		<div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p>
					(*) 모바일에서만 보여지는 아이콘 메뉴를 설정합니다. <span style="color:#ED1C24;">(최대 8개까지 추가 가능)</span><br />
					(*) 하단 메뉴 설정 시 경우 맨 위로 버튼과 동일하게 고정됩니다.<br />
					(*) 아이콘은 [Font Awesome] 에서 아이콘 이름을 입력하세요. (예, fa-bed, fa-frain)	
				</p>
			</div>
			<br />
			<div class="slideItemsTitle">사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_display_use_mobile_icon" data-target="displayUseMobileIcon" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_mobile_icon" id="<?php echo $theme_shortname?>_display_use_mobile_icon" value='<?php echo get_option($theme_shortname."_display_use_mobile_icon")?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_mobile_icon_count" id="<?php echo $theme_shortname?>_display_use_mobile_icon_count" value='<?php echo $mobile_icon_count?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_mobile_icon_last" id="<?php echo $theme_shortname?>_display_use_mobile_icon_last" value='<?php echo $mobile_icon_last?>'  />
				<span  style='float:right;'>
					<input class='button-secondary' onClick="mobileIcon('add',parseInt(jQuery('#<?php echo $theme_shortname?>_display_use_mobile_icon_last').val()));" type='button' value='추가' style='height:23px;font-size:11px;' />
				</span>
			</div>
			<div class="displayUseMobileIcon" <?php echo $show?>>
				<dl>
					<dt>메뉴 위치</dt>
					<dd>
						<input type='radio' id='<?php echo $theme_shortname?>_display_use_mobile_icon_position' name='<?php echo $theme_shortname?>_display_use_mobile_icon_position' value='top' <?php echo (get_option($theme_shortname."_display_use_mobile_icon_position")=='top')?"checked='checked'":"";?> />상단&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' id='<?php echo $theme_shortname?>_display_use_mobile_icon_position' name='<?php echo $theme_shortname?>_display_use_mobile_icon_position' value='bottom' <?php echo (!get_option($theme_shortname."_display_use_mobile_icon_position") || get_option($theme_shortname."_display_use_mobile_icon_position")=='bottom')?"checked='checked'":"";?> />하단
					</dd>
				</dl>
				<dl>
					<dt>메뉴 배경색</dt>
					<dd>
					  <?php $id_str= (get_option($theme_shortname."_display_mobile_icon_menu_bgcolor")!="") ? get_option($theme_shortname."_display_mobile_icon_menu_bgcolor") : "#ffffff"?>
					  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_menu_bgcolor' id='<?php echo $theme_shortname?>_display_mobile_icon_menu_bgcolor' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
					</dd>
				</dl>
				<dl>
					<dt>메뉴 글자색</dt>
					<dd>
					  <?php $id_str= (get_option($theme_shortname."_display_mobile_icon_menu_color")!="") ? get_option($theme_shortname."_display_mobile_icon_menu_color") : "#303030"?>
					  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_menu_color' id='<?php echo $theme_shortname?>_display_mobile_icon_menu_color' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
					</dd>
				</dl>
				<div class="slideItemsTitle sub">아이콘 구분선&nbsp;&nbsp;
					  <?php
						$useLine   = get_option($theme_shortname."_display_use_mobile_icon_line")=='U'?'yes':'no';
						$showLine = get_option($theme_shortname."_display_use_mobile_icon_line")=='U'?'':'style="display:none"';
					  ?>
						<span class="useCheck" data-use="<?php echo $useLine?>" data-container="<?php echo $theme_shortname?>_display_use_mobile_icon_line" data-target="displayUseMobileIconLine" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $useLine?>.png" /></span>
						<input type="hidden" name="<?php echo $theme_shortname?>_display_use_mobile_icon_line" id="<?php echo $theme_shortname?>_display_use_mobile_icon_line" value='<?php echo get_option($theme_shortname."_display_use_mobile_icon_line")?>'  />
				</div>
				<dl class="displayUseMobileIconLine" <?php echo $showLine?>>
					<dt>아이콘 구분선 종류</dt>
					<dd>
						<input type='radio' id='<?php echo $theme_shortname?>_display_mobile_icon_line_type' name='<?php echo $theme_shortname?>_display_mobile_icon_line_type' value='dot' <?php echo (!get_option($theme_shortname."_display_mobile_icon_line_type") || get_option($theme_shortname."_display_mobile_icon_line_type")=='dot')?"checked='checked'":"";?> />점선&nbsp;&nbsp;&nbsp;<input type='radio' id='<?php echo $theme_shortname?>_display_mobile_icon_line_type' name='<?php echo $theme_shortname?>_display_mobile_icon_line_type' value='line' <?php echo (get_option($theme_shortname."_display_mobile_icon_line_type")=='line')?"checked='checked'":"";?> />실선
					</dd>
				</dl>
				<dl class="displayUseMobileIconLine" <?php echo $showLine?>>
					<dt>아이콘 구분선 색</dt>
					<dd>
					  <?php $id_str= (get_option($theme_shortname."_display_mobile_icon_line_color")!="") ? get_option($theme_shortname."_display_mobile_icon_line_color") : "#afafaf"?>
					  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_line_color' id='<?php echo $theme_shortname?>_display_mobile_icon_line_color' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
					</dd>
				</dl>
				<div class="slideItemsTitle sub">아이콘 배경&nbsp;&nbsp;&nbsp;&nbsp;
					  <?php
						$useBackground   = get_option($theme_shortname."_display_use_mobile_icon_bgcolor")=='U'?'yes':'no';
						$showBackground = get_option($theme_shortname."_display_use_mobile_icon_bgcolor")=='U'?'':'style="display:none"';
					  ?>
						<span class="useCheck" data-use="<?php echo $useBackground?>" data-container="<?php echo $theme_shortname?>_display_use_mobile_icon_bgcolor" data-target="displayUseMobileIconBackground" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $useBackground?>.png" /></span>

						<input type="hidden" name="<?php echo $theme_shortname?>_display_use_mobile_icon_bgcolor" id="<?php echo $theme_shortname?>_display_use_mobile_icon_bgcolor" value='<?php echo get_option($theme_shortname."_display_use_mobile_icon_bgcolor")?>'  />
				</div>
				<dl class="displayUseMobileIconBackground" <?php echo $showBackground?>>
					<dt>아이콘색</dt>
					<dd>
					  <?php $id_str= (get_option($theme_shortname."_display_mobile_icon_color")!="") ? get_option($theme_shortname."_display_mobile_icon_color") : "#303030"?>
					  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_color' id='<?php echo $theme_shortname?>_display_mobile_icon_color' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
					</dd>
				</dl>
				<dl class="displayUseMobileIconBackground" <?php echo $showBackground?>>
					<dt>아이콘 배경색</dt>
					<dd>
					  <?php $id_str= (get_option($theme_shortname."_display_mobile_icon_bgcolor")!="") ? get_option($theme_shortname."_display_mobile_icon_bgcolor") : "#ffffff"?>
					  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_bgcolor' id='<?php echo $theme_shortname?>_display_mobile_icon_bgcolor' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
					</dd>
				</dl>
				<div class="slideItemsTitle sub">그림자 효과&nbsp;&nbsp;&nbsp;&nbsp;
					  <?php
						$useShadow   = get_option($theme_shortname."_display_use_mobile_icon_shadow")=='U'?'yes':'no';
						$showShadow = get_option($theme_shortname."_display_use_mobile_icon_shadow")=='U'?'':'style="display:none"';
					  ?>
						<span class="useCheck" data-use="<?php echo $useShadow?>" data-container="<?php echo $theme_shortname?>_display_use_mobile_icon_shadow" data-target="displayUseMobileIconShadow" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $useShadow?>.png" /></span>
						<input type="hidden" name="<?php echo $theme_shortname?>_display_use_mobile_icon_shadow" id="<?php echo $theme_shortname?>_display_use_mobile_icon_shadow" value='<?php echo get_option($theme_shortname."_display_use_mobile_icon_shadow")?>'  />
				</div>
				<dl class="displayUseMobileIconShadow" <?php echo $showShadow?>>
					<dt>그림자 색</dt>
					<dd>
					  <?php $id_str= (get_option($theme_shortname."_display_mobile_icon_shadow_color")!="") ? get_option($theme_shortname."_display_mobile_icon_shadow_color") : "#000000"?>
					  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_shadow_color' id='<?php echo $theme_shortname?>_display_mobile_icon_shadow_color' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
					</dd>
				</dl>
				<dl class="displayUseMobileIconShadow" <?php echo $showShadow?>>
					<dt>그림자 투명도</dt>
					<dd>
						<input id='<?php echo $theme_shortname?>_display_mobile_icon_shadow_alpha' class="slider" name='<?php echo $theme_shortname?>_display_mobile_icon_shadow_alpha' type="range"  min="0.1" max="1" step="0.01" value='<?php echo (get_option($theme_shortname."_display_mobile_icon_shadow_alpha"))?get_option($theme_shortname."_display_mobile_icon_shadow_alpha"):'0.5'?>' />
						<span class="sliderValue" style="display:inline-block;height:2.5em;line-height:2.5em;font-weight:bold;vertical-align:top;">
						  <?php echo (get_option($theme_shortname."_display_mobile_icon_shadow_alpha"))?get_option($theme_shortname."_display_mobile_icon_shadow_alpha"):'0.5'?>
						</span>
					</dd>
				</dl>
			<?php
			for ($i=1; $i<=$mobile_icon_count; $i++) {
				$optBannerType=get_option($theme_shortname."_display_mobile_icon_type_".$i);
				if(!$optBannerType) $optBannerType="normal";
			?>
				<div id="mobile_icon_item<?php echo $i?>">
					<div class="slideItemsTitle sub">항목<?php echo $i?> 
					<select name="<?php echo $theme_shortname?>_display_mobile_icon_type_<?php echo $i;?>" id="<?php echo $theme_shortname?>_display_mobile_icon_type_<?php echo $i;?>" style="margin-left:20px;line-height: 24px;height: 24px;font-weight: normal;margin-top: -3px;" onChange="mobileIcon_change_type(<?php echo $i;?>);">
						<option value='normal' <?php echo ($optBannerType=='normal')?"selected='selected'":"";?>>사용자 정의</option>
						<option value='cart' <?php echo ($optBannerType=='cart')?"selected='selected'":"";?>>장바구니</option>
						<option value='mypage' <?php echo ($optBannerType=='mypage')?"selected='selected'":"";?>>마이페이지</option>
						<option value='order' <?php echo ($optBannerType=='order')?"selected='selected'":"";?>>주문/배송조회</option>
						<option value='interest' <?php echo ($optBannerType=='interest')?"selected='selected'":"";?>>관심상품</option>
						<!-- <option value='point' <?php echo ($optBannerType=='point')?"selected='selected'":"";?>>적립금내역</option> -->
						<option value='man2man' <?php echo ($optBannerType=='man2man')?"selected='selected'":"";?>>나의1:1 문의</option>
						<option value='myInven' <?php echo ($optBannerType=='myInven')?"selected='selected'":"";?>>재고관리</option>
					</select>
					<input class='button-secondary' onClick="mobileIcon('del',<?php echo $i?>);" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>
					<dl>
						<dt>아이콘</dt>
						<dd>
							  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_icon_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_mobile_icon_icon_<?php echo $i?>' value="<?php echo stripslashes(get_option($theme_shortname."_display_mobile_icon_icon_".$i));?>" style="width:30%;">  <span style="color:#4c99ba;">=></span> <a href="http://fontawesome.io/icons/" target="_blank"><span style="font-weight:700;color:#4c99ba;">[Font Awesome]</span> <span style="color:#4c99ba;">Icon 목록 보기</span></a>
						</dd>
					</dl>
					<dl>
						<dt>타이틀</dt>
						<dd>
							  <input type='text' name='<?php echo $theme_shortname?>_display_mobile_icon_title_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_mobile_icon_title_<?php echo $i?>' value="<?php echo stripslashes(get_option($theme_shortname."_display_mobile_icon_title_".$i));?>" style="width:30%;">
						</dd>
					</dl>
					<dl>
						<dt>링크 URL</dt>
						<dd>
							<span id="mobileIcon_url_<?php echo $i;?>"><input type="text" name='<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_display_mobile_icon_url_".$i)?>' style="width:70%;" /></span>

							<span id="mobileIcon_window_<?php echo $i;?>"><input type="radio" name="<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_mobile_icon_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>_window_self">현재창</label>
							<input type="radio" name="<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_display_mobile_icon_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_mobile_icon_url_<?php echo $i?>_window_blank">새창</label></span>
						</dd>
					</dl>
				</div>
				<?php
				}
				?>
			</div>
		</div>
  		<script language="javascript">
			function mobileIcon(mode, i) {
				var createHtml = "";
				var siteCount = parseInt(jQuery("#<?php echo $theme_shortname?>_display_use_mobile_icon_count").val());
				var siteLast = parseInt(jQuery("#<?php echo $theme_shortname?>_display_use_mobile_icon_last").val());
				var mallId="<?php echo $mallID;?>";
				var themeShortname="<?php echo $theme_shortname;?>";
				if(mode=="add") {
					if((siteCount+1)>8){
						alert('모바일 아이콘은 8개 까지만 추가가 가능합니다.    ');
						return;
					}

					if(!i) {i = 1;}else{i += 1;}
					createHtml += "			<div id=\"mobile_icon_item"+i+"\">";
					createHtml += "				<div class=\"slideItemsTitle sub\">항목"+i;
					createHtml += "					<select name='"+themeShortname+"_display_mobile_icon_type_"+i+"' id='"+themeShortname+"_display_mobile_icon_type_"+i+"' style='margin-left:20px;line-height: 24px;height: 24px;font-weight: normal;margin-top: -3px;' onChange='mobileIcon_change_type("+i+");'>";
					createHtml += "						<option value='normal'>사용자 정의</option>";
					createHtml += "						<option value='cart'>장바구니</option>";
					createHtml += "						<option value='mypage'>마이페이지</option>";
					createHtml += "						<option value='order'>주문/배송조회</option>";
					createHtml += "						<option value='interest'>관심상품</option>";
					// createHtml += "						<option value='point'>적립금내역</option>";
					createHtml += "						<option value='man2man'>나의1:1 문의</option>";
					createHtml += "						<option value='myInven'>재고관리</option>";
					createHtml += "						<option value='coupon'>쿠폰내역</option>";
					createHtml += "					</select>";
					createHtml += "					<input class='button-secondary' onClick=\"mobileIcon('del',"+i+");\" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>";
					createHtml += "				<dl>"
					createHtml += "					<dt>아이콘</dt>"
					createHtml += "					<dd>"
					createHtml += "						<input type='text' name='"+themeShortname+"_display_mobile_icon_icon_"+i+"' id='"+themeShortname+"_display_mobile_icon_icon_"+i+"' value='' style='width:30%;'>  <span style='color:#4c99ba;'>=></span> <a href='http://fontawesome.io/icons/' target='_blank'><span style='font-weight:700;color:#4c99ba;'>[Font Awesome]</span> <span style='color:#4c99ba;'>Icon 목록 보기</span></a>";
					createHtml += "					</dd>";
					createHtml += "				</dl>";
					createHtml += "				<dl>";
					createHtml += "					<dt>타이틀</dt>";
					createHtml += "					<dd>";
					createHtml += "						<input type='text' name='"+themeShortname+"_display_mobile_icon_title_"+i+"' id='"+themeShortname+"_display_mobile_icon_title_"+i+"' value='' style='width:30%;'>";
					createHtml += "					</dd>";
					createHtml += "				</dl>";
					createHtml += "				<dl>";
					createHtml += "					<dt id='mobileIconInfo_title_"+i+"'>링크 URL</dt>";
					createHtml += "					<dd>";
					createHtml += "						<span id='mobileIcon_url_"+i+"' style='display:inline;'><input type='text' name='"+themeShortname+"_display_mobile_icon_url_"+i+"' id='"+themeShortname+"_display_mobile_icon_url_"+i+"' value='' style='width:70%;' /></span>";
					createHtml += "						<span id='mobileIcon_window_"+i+"' style='display:inline;'><input type=\"radio\" name=\""+themeShortname+"_display_mobile_icon_url_"+i+"_window\" id=\""+themeShortname+"_display_mobile_icon_url_"+i+"_window_self\" value='_self' style='border:0px;' checked='checked'><label for=\""+themeShortname+"_display_mobile_icon_url_"+i+"_window_self\">현재창</label>";
					createHtml += "						<input type=\"radio\" name=\""+themeShortname+"_display_mobile_icon_url_"+i+"_window\" id=\""+themeShortname+"_display_mobile_icon_url_<?php echo $i?>_window_blank\" value='_blank' style='border:0px;'><label for=\""+themeShortname+"_display_mobile_icon_url_"+i+"_window_blank\">새창</label></span>";
					createHtml += "					</dd>";
					createHtml += "				</dl>";
					createHtml += "			</div>";
					
					jQuery("#"+themeShortname+"_display_use_mobile_icon_last").val(i);
					jQuery("#"+themeShortname+"_display_use_mobile_icon_count").val(siteCount+1);
					jQuery(".displayUseMobileIcon").append(createHtml);
				}else{
					jQuery("#"+themeShortname+"_display_use_mobile_icon_count").val(siteCount-1)
					jQuery("#mobile_icon_item"+i).remove();

					if(jQuery("[id^='mobile_icon_item']").size()) {
						var last_item = jQuery("[id^='mobile_icon_item']").last().attr("id");
						i = parseInt(last_item.replace("mobile_icon_item",""));
					}else{
						i = 0;
					}
					jQuery("#"+themeShortname+"_display_use_mobile_icon_last").val(i);
				}
			}

			function mobileIcon_change_type(btmNo){
				var themeShortname="<?php echo $theme_shortname;?>";
				var selectType=jQuery("#"+themeShortname+"_display_mobile_icon_type_"+btmNo).val();
				var homeURL="<?php echo BBSE_COMMERCE_SITE_URL;?>/";
				var iconOption=new Array();
				iconOption['normal'] = {"icon":"", "title":"", "url":"", "window":true};
				iconOption['cart'] = {"icon":"fa-shopping-cart", "title":"장바구니", "url":homeURL+"?bbsePage=cart", "window":true};
				iconOption['mypage'] = {"icon":"fa-user", "title":"마이페이지", "url":homeURL+"?bbseMy=mypage", "window":true};
				iconOption['order'] = {"icon":"fa-truck", "title":"주문/배송조회", "url":homeURL+"?bbseMy=order-list", "window":true};
				iconOption['interest'] = {"icon":"fa-heart", "title":"관심상품", "url":homeURL+"?bbseMy=interest", "window":true};
				//iconOption['point'] = {"icon":"fa-krw", "title":"적립금내역", "url":homeURL+"?bbseMy=point", "window":true};
				iconOption['man2man'] = {"icon":"fa-question-circle", "title":"나의 1:1 문의", "url":homeURL+"?bbseMy=man2man", "window":true};
				iconOption['coupon'] = {"icon":"fa-question-circle", "title":"쿠폰내역", "url":homeURL+"?bbseMy=coupon", "window":true};
				iconOption['myInven'] = {"icon":"fa-question-circle", "title":"재고관리", "url":homeURL+"?bbseMy=myInven", "window":true};

				if(selectType){
					jQuery("#"+themeShortname+"_display_mobile_icon_icon_"+btmNo).val(iconOption[selectType]['icon']);
					jQuery("#"+themeShortname+"_display_mobile_icon_title_"+btmNo).val(iconOption[selectType]['title']);
					jQuery("#"+themeShortname+"_display_mobile_icon_url_"+btmNo).val(iconOption[selectType]['url']);
					jQuery("input:checkbox[id='"+themeShortname+"_display_mobile_icon_url_"+btmNo+"_window']").attr("checked", iconOption[selectType]['window']);
				}
			}
		</script>
      </li><!-- /모바일 아이콘 메뉴-->
<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use    = get_option($theme_shortname."_display_use_middle_banner")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_display_use_middle_banner")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_display_use_middle_banner")=='U'?'':'style="display:none"';
      ?>
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">메인화면 중간배너 설정</div>
		<div class="item">
			<div class="slideItemsTitle">
				사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_display_use_middle_banner" data-target="display_use_middle_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_middle_banner" id="<?php echo $theme_shortname?>_display_use_middle_banner" value='<?php echo get_option($theme_shortname."_display_use_middle_banner")?>'  />
			</div>
			<div class="display_use_middle_banner" <?php echo $show?>>
			<span class="desc2">이미지 사이즈는 가로 425px 입니다. (세로 제약은 없으나 배너 두개의 세로사이즈는 동일하여야 합니다.)</span><br />
			<?php for ($i=1; $i<=2; $i++) {?>
			  <div class="slideItemsTitle sub">메인 중간배너 <?php echo $i?></div>
			  <dl>
				<dt>이미지</dt>
				<dd>
				  <input id='<?php echo $theme_shortname?>_display_middle_banner_image_<?php echo $i?>' name='<?php echo $theme_shortname?>_display_middle_banner_image_<?php echo $i?>' type='text' value='<?php echo get_option($theme_shortname."_display_middle_banner_image_".$i)?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('display_middle_banner_image_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				  <span style="display:inline-block;">
				  <?php
					if(get_option($theme_shortname."_display_middle_banner_image_".$i))
					{
					  echo "<a href='".get_option($theme_shortname."_display_middle_banner_image_".$i)."' data-lightbox='display_middle_banner_image_".$i."'><img src='".get_option($theme_shortname."_display_middle_banner_image_".$i)."' class='fileimg'></a>";
					}
				  ?>
				  </span>
				</dd>
			  </dl>
			  <dl>
				<dt>링크</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_display_middle_banner_link_".$i)?>' style='width:70%;' >&nbsp;
				  <input type="radio" name="<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_middle_banner_link_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>_window_self">현재창</label>
				  <input type="radio" name="<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_display_middle_banner_link_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_middle_banner_link_<?php echo $i?>_window_blank">새창</label>
				</dd>
			  </dl>
			<?php }?>
			</div>
		</div>
      </li><!-- /메인 중간배너 -->
<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use    = get_option($theme_shortname."_display_use_bottom_banner")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_display_use_bottom_banner")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_display_use_bottom_banner")=='U'?'':'style="display:none"';
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">메인화면 하단배너 설정</div>
		<div class="item">
			<div class="slideItemsTitle">
				사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_display_use_bottom_banner" data-target="display_use_bottom_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_bottom_banner" id="<?php echo $theme_shortname?>_display_use_bottom_banner" value='<?php echo get_option($theme_shortname."_display_use_bottom_banner")?>'  />
			</div>
			<div class="display_use_bottom_banner" <?php echo $show?>>
			<span class="desc2">이미지 사이즈는 가로 860xp 입니다. (세로 제약은 없음)</span><br />
			<?php for ($i=1; $i<=1; $i++) {?>
			  <dl>
				<dt>이미지</dt>
				<dd>
				  <input id='<?php echo $theme_shortname?>_display_bottom_banner_image_<?php echo $i?>' name='<?php echo $theme_shortname?>_display_bottom_banner_image_<?php echo $i?>' type='text' value='<?php echo get_option($theme_shortname."_display_bottom_banner_image_".$i)?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('display_bottom_banner_image_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				  <span style="display:inline-block;">
				  <?php
					if(get_option($theme_shortname."_display_bottom_banner_image_".$i))
					{
					  echo "<a href='".get_option($theme_shortname."_display_bottom_banner_image_".$i)."' data-lightbox='display_bottom_banner_image_".$i."'><img src='".get_option($theme_shortname."_display_bottom_banner_image_".$i)."' class='fileimg'></a>";
					}
				  ?>
				  </span>
				</dd>
			  </dl>
			  <dl>
				<dt>링크</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_display_bottom_banner_link_".$i)?>' style='width:70%;'>&nbsp;
				  <input type="radio" name="<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_bottom_banner_link_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>_window_self">현재창</label>
				  <input type="radio" name="<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_display_bottom_banner_link_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_bottom_banner_link_<?php echo $i?>_window_blank">새창</label>
				</dd>
			  </dl>
			<?php }?>
			</div>
		</div>
      </li><!-- /메인 하단배너 설정 -->
<!-- ******************************************************************************************************************************************************* -->
	<script>
		function leftBanner(mode, i) {
			if(jQuery("[id^='left_banner_item']").size() > 4 && mode == "add") {
				alert("더이상 추가할수 없습니다.");
				return;
			}
			var createHtml = "";
			var siteCount = parseInt(jQuery("#<?php echo $theme_shortname?>_display_use_left_banner_count").val());
			var siteLast = parseInt(jQuery("#<?php echo $theme_shortname?>_display_use_left_banner_last").val());
			if(mode=="add") {
				if(!i) {i = 1;}else{i += 1;}
				createHtml += "			<div id=\"left_banner_item"+i+"\">";
				createHtml += "				<div class=\"slideItemsTitle sub\">항목"+i+" <input class='button-secondary' onClick=\"leftBanner('del',"+i+");\" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>";
				createHtml += "				<dl class=\"dl12\">";
				createHtml += "					<dt>이미지</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input id='<?php echo $theme_shortname?>_display_left_banner_img_"+i+"' name='<?php echo $theme_shortname?>_display_left_banner_img_"+i+"' type='text' value='' style='width:70%;' />&nbsp;<input class='button-secondary' onClick=\"upload_img('display_left_banner_img_"+i+"');\" type='button' value='찾아보기' style='height:23px;font-size:11px;' />";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "				<dl class=\"dl12\">";
				createHtml += "					<dt>링크 URL</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input type='text' name='<?php echo $theme_shortname?>_display_left_banner_url_"+i+"' id='<?php echo $theme_shortname?>_display_left_banner_url_"+i+"' value='' style='width:70%;'/>";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_left_banner_url_"+i+"_window\" id=\"<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window_self\" value='_self' style='border:0px;' checked='checked'><label for=\"<?php echo $theme_shortname?>_display_left_banner_url_"+i+"_window_self\">현재창</label>";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_left_banner_url_"+i+"_window\" id=\"<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window_blank\" value='_blank' style='border:0px;'><label for=\"<?php echo $theme_shortname?>_display_left_banner_url_"+i+"_window_blank\">새창</label>";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "			</div>";
				jQuery("#<?php echo $theme_shortname?>_display_use_left_banner_last").val(i);
				jQuery("#<?php echo $theme_shortname?>_display_use_left_banner_count").val(siteCount+1);
				jQuery(".display_use_left_banner").append(createHtml);
			}else{
				jQuery("#<?php echo $theme_shortname?>_display_use_left_banner_count").val(siteCount-1)
				jQuery("#left_banner_item"+i).remove();

				if(jQuery("[id^='left_banner_item']").size()) {
					var last_item = jQuery("[id^='left_banner_item']").last().attr("id");
					i = parseInt(last_item.replace("left_banner_item",""));
				}else{
					i = 0;
				}
				jQuery("#<?php echo $theme_shortname?>_display_use_left_banner_last").val(i);
			}
		}
	</script>

     <?php
        $use    = get_option($theme_shortname."_display_use_left_banner")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_display_use_left_banner")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_display_use_left_banner")=='U'?'':'style="display:none"';
		$left_banner_count = get_option($theme_shortname."_display_use_left_banner_count")?get_option($theme_shortname."_display_use_left_banner_count"):0;
		$left_banner_last = get_option($theme_shortname."_display_use_left_banner_last")?get_option($theme_shortname."_display_use_left_banner_last"):0;
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"> 왼쪽 배너설정</div>
		<div class="item">
			<div class="slideItemsTitle">
			   사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_display_use_left_banner" data-target="display_use_left_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_left_banner" id="<?php echo $theme_shortname?>_display_use_left_banner" value='<?php echo get_option($theme_shortname."_display_use_left_banner")?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_left_banner_count" id="<?php echo $theme_shortname?>_display_use_left_banner_count" value='<?php echo $left_banner_count?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_left_banner_last" id="<?php echo $theme_shortname?>_display_use_left_banner_last" value='<?php echo $left_banner_last?>'  />
				<input class='button-secondary' onClick="leftBanner('add',parseInt(jQuery('#<?php echo $theme_shortname?>_display_use_left_banner_last').val()));" type='button' value='추가' style='float:right;height:23px;font-size:11px;' />
			</div>

			<div class="display_use_left_banner" <?php echo $show?>>
				<div>
					* 최대 5개까지 설정이 가능합니다.<br />
					* 롤링배너 사용 : 
					<input type="radio" name="<?php echo $theme_shortname?>_display_use_left_rolling_banner" id="<?php echo $theme_shortname?>_display_use_left_rolling_banner1" value='Y' <?php echo (get_option($theme_shortname."_display_use_left_rolling_banner")=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_use_left_rolling_banner1">사용</label>
					<input type="radio" name="<?php echo $theme_shortname?>_display_use_left_rolling_banner" id="<?php echo $theme_shortname?>_display_use_left_rolling_banner2" value='N' <?php echo (get_option($theme_shortname."_display_use_left_rolling_banner")=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_use_left_rolling_banner2">사용안함</label>
					(사용안함 선택시 아래로 순차적으로 적용됩니다.)
				</div>
				<span class="desc2">이미지 : 가로(200px), 세로(280px) 사이즈로 사용해주세요.</span><br />
				<?php for ($i=1; $i<=$left_banner_count; $i++) {?>
				<div id="left_banner_item<?php echo $i?>">
					<div class="slideItemsTitle sub">왼쪽 배너<?php echo $i?> <input class='button-secondary' onClick="leftBanner('del',<?php echo $i?>);" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>
					<dl>
						<dt>이미지</dt>
						<dd>
							<input id='<?php echo $theme_shortname?>_display_left_banner_img_<?php echo $i?>' name='<?php echo $theme_shortname?>_display_left_banner_img_<?php echo $i?>' type='text' value='<?php echo get_option($theme_shortname."_display_left_banner_img_".$i)?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('display_left_banner_img_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
							<span style="display:inline-block;">
							<?php
							if(get_option($theme_shortname."_display_left_banner_img_".$i))
							{
							echo "<a href='".get_option($theme_shortname."_display_left_banner_img_".$i)."' data-lightbox='display_left_banner_img_".$i."'><img src='".get_option($theme_shortname."_display_left_banner_img_".$i)."' class='fileimg'></a>";
							}
							?>
							</span>
						</dd>
					</dl>
					<dl>
						<dt>링크 URL</dt>
						<dd>
							<input type='text' name='<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_display_left_banner_url_".$i)?>' style='width:70%;'/>

							<input type="radio" name="<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_left_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window_self">현재창</label>

							<input type="radio" name="<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_display_left_banner_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_left_banner_url_<?php echo $i?>_window_blank">새창</label>

						</dd>
					</dl>
				</div>
				<?php }?>
			</div>
		</div>
      </li><!-- /왼쪽 배너 설정 -->
<!-- ******************************************************************************************************************************************************* -->
	<script>
		function rightBanner(mode, i) {
			var createHtml = "";
			var siteCount = parseInt(jQuery("#<?php echo $theme_shortname?>_display_use_right_banner_count").val());
			var siteLast = parseInt(jQuery("#<?php echo $theme_shortname?>_display_use_right_banner_last").val());
			if(mode=="add") {
				if(jQuery("[id^='right_banner_item']").size() > 4) {
					alert("더이상 추가할수 없습니다.");
					return;
				}
				if(!i) {i = 1;}else{i += 1;}
				createHtml += "			<div id=\"right_banner_item"+i+"\">";
				createHtml += "				<div class=\"slideItemsTitle sub\">항목"+i+" <input class='button-secondary' onClick=\"rightBanner('del',"+i+");\" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>";
				createHtml += "				<dl class=\"dl12\">";
				createHtml += "					<dt>사용여부</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_right_banner_use_"+i+"\" id=\"<?php echo $theme_shortname?>_display_right_banner_use_"+i+"\" value='Y' style='border:0px;' checked='checked'><label for=\"<?php echo $theme_shortname?>_display_right_banner_use_"+i+"\">사용</label>";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_right_banner_use_"+i+"\" id=\"<?php echo $theme_shortname?>_display_right_banner_use_"+i+"\" value='N' style='border:0px;'><label for=\"<?php echo $theme_shortname?>_display_right_banner_use_"+i+"\">사용안함</label>";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "				<dl class=\"dl12\">";
				createHtml += "					<dt>이미지</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input id='<?php echo $theme_shortname?>_display_right_banner_img_"+i+"' name='<?php echo $theme_shortname?>_display_right_banner_img_"+i+"' type='text' value='' style='width:70%;' />&nbsp;<input class='button-secondary' onClick=\"upload_img('display_right_banner_img_"+i+"');\" type='button' value='찾아보기' style='height:23px;font-size:11px;' />";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "				<dl class=\"dl12\">";
				createHtml += "					<dt>링크 URL</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input type='text' name='<?php echo $theme_shortname?>_display_right_banner_url_"+i+"' id='<?php echo $theme_shortname?>_display_right_banner_url_"+i+"' value='' style='width:70%;' />";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_right_banner_url_"+i+"_window\" id=\"<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window_self\" value='_self' style='border:0px;' checked='checked'><label for=\"<?php echo $theme_shortname?>_display_right_banner_url_"+i+"_window_self\">현재창</label>";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_right_banner_url_"+i+"_window\" id=\"<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window_blank\" value='_blank' style='border:0px;'><label for=\"<?php echo $theme_shortname?>_display_right_banner_url_"+i+"_window_blank\">새창</label>";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "			</div>";
				jQuery("#<?php echo $theme_shortname?>_display_use_right_banner_last").val(i);
				jQuery("#<?php echo $theme_shortname?>_display_use_right_banner_count").val(siteCount+1);
				jQuery(".display_use_right_banner").append(createHtml);
			}else{
				jQuery("#<?php echo $theme_shortname?>_display_use_right_banner_count").val(siteCount-1)
				jQuery("#right_banner_item"+i).remove();

				if(jQuery("[id^='right_banner_item']").size()) {
					var last_item = jQuery("[id^='right_banner_item']").last().attr("id");
					i = parseInt(last_item.replace("right_banner_item",""));
				}else{
					i = 0;
				}
				jQuery("#<?php echo $theme_shortname?>_display_use_right_banner_last").val(i);
			}
		}
		function optionMenu(mode, i) {
			var createHtml = "";
			var optCount = parseInt(jQuery("#<?php echo $theme_shortname?>_display_option_menu_count").val());
			var optLast = parseInt(jQuery("#<?php echo $theme_shortname?>_display_option_menu_last").val());
			if(mode=="add") {
				if(jQuery("[id^='option_menu_item']").size() > 3) {
					alert("더이상 추가할수 없습니다.");
					return;
				}
				if(!i) {i = 1;}else{i += 1;}
				createHtml += "				<div id=\"option_menu_item"+i+"\">";
				createHtml += "				<div style='padding-top:20px;padding-bottom:10px;font-weight:bold;width:100%;border-bottom:1px dotted #ddd;'>부가메뉴 "+i+" <input class='button-secondary' onClick=\"optionMenu('del',"+i+");\" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>";
				createHtml += "				<dl class=\"dl12\">";
				createHtml += "					<dt>메뉴명</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input type='text' name='<?php echo $theme_shortname?>_display_option_menu_title_"+i+"' id='<?php echo $theme_shortname?>_display_option_menu_title_"+i+"' value=''>";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "				<dl class=\"dl12\">";
				createHtml += "					<dt>링크</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input type='text' name='<?php echo $theme_shortname?>_display_option_menu_link_"+i+"' id='<?php echo $theme_shortname?>_display_option_menu_link_"+i+"' value='' style='width:70%;'>&nbsp;";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_option_menu_link_"+i+"_window\" id=\"<?php echo $theme_shortname?>_display_option_menu_link_"+i+"_window_self\" value='_self' style='border:0px;' checked='checked'><label for=\"<?php echo $theme_shortname?>_display_option_menu_link_"+i+"_window_self\">현재창</label>";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_display_option_menu_link_"+i+"_window\" id=\"<?php echo $theme_shortname?>_display_option_menu_link_"+i+"_window_blank\" value='_blank' style='border:0px;'><label for=\"<?php echo $theme_shortname?>_display_option_menu_link_"+i+"_window_blank\">새창</label>";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				jQuery("#<?php echo $theme_shortname?>_display_option_menu_last").val(i);
				jQuery("#<?php echo $theme_shortname?>_display_option_menu_count").val(optCount+1);
				jQuery(".display_option_menu").append(createHtml);
			}else{
				jQuery("#<?php echo $theme_shortname?>_display_option_menu_count").val(optCount-1)
				jQuery("#option_menu_item"+i).remove();

				if(jQuery("[id^='option_menu_item']").size()) {
					var last_item = jQuery("[id^='option_menu_item']").last().attr("id");
					i = parseInt(last_item.replace("option_menu_item",""));
				}else{
					i = 0;
				}
				jQuery("#<?php echo $theme_shortname?>_display_option_menu_last").val(i);
			}
		}
	</script>

     <?php
        $use    = get_option($theme_shortname."_display_use_right_banner")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_display_use_right_banner")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_display_use_right_banner")=='U'?'':'style="display:none"';
		$right_banner_count = get_option($theme_shortname."_display_use_right_banner_count")?get_option($theme_shortname."_display_use_right_banner_count"):0;
		$right_banner_last = get_option($theme_shortname."_display_use_right_banner_last")?get_option($theme_shortname."_display_use_right_banner_last"):0;
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"> 오른쪽 배너설정</div>
		<div class="item">
			<div class="slideItemsTitle">
				사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_display_use_right_banner" data-target="display_use_right_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_right_banner" id="<?php echo $theme_shortname?>_display_use_right_banner" value='<?php echo get_option($theme_shortname."_display_use_right_banner")?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_right_banner_count" id="<?php echo $theme_shortname?>_display_use_right_banner_count" value='<?php echo $right_banner_count?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_right_banner_last" id="<?php echo $theme_shortname?>_display_use_right_banner_last" value='<?php echo $right_banner_last?>'  />
				<input class='button-secondary' onClick="rightBanner('add',parseInt(jQuery('#<?php echo $theme_shortname?>_display_use_right_banner_last').val()));" type='button' value='추가' style='float:right;height:23px;font-size:11px;' />
			</div>
			<div class="display_use_right_banner" <?php echo $show?>>
				<div>
					* 최대 5개까지 설정이 가능합니다.<br />
					* 롤링배너 사용 : 
					<input type="radio" name="<?php echo $theme_shortname?>_display_use_right_rolling_banner" id="<?php echo $theme_shortname?>_display_use_right_banner1" value='Y' <?php echo (get_option($theme_shortname."_display_use_right_rolling_banner")=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_use_right_banner1">사용</label>
					<input type="radio" name="<?php echo $theme_shortname?>_display_use_right_rolling_banner" id="<?php echo $theme_shortname?>_display_use_right_banner2" value='N' <?php echo (get_option($theme_shortname."_display_use_right_rolling_banner")=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_use_right_banner2">사용안함</label>
					(사용안함 선택시 아래로 순차적으로 적용됩니다.)
				</div>
				<span class="desc2">이미지 : 가로(80px), 세로(130px) 사이즈로 사용해주세요.</span><br />
				<?php for ($i=1; $i<=$right_banner_count; $i++) {?>
				<div id="right_banner_item<?php echo $i?>">
					<div class="slideItemsTitle sub">오른쪽 배너<?php echo $i?> <input class='button-secondary' onClick="rightBanner('del',<?php echo $i?>);" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>
					<dl>
						<dt>사용여부</dt>
						<dd>
							<input type="radio" name="<?php echo $theme_shortname?>_display_right_banner_use_<?php echo $i?>" id="<?php echo $theme_shortname?>_display_right_banner_use_<?php echo $i?>" value='Y' <?php echo (get_option($theme_shortname."_display_right_banner_use_".$i)=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_right_banner_use_<?php echo $i?>">사용</label>
							<input type="radio" name="<?php echo $theme_shortname?>_display_right_banner_use_<?php echo $i?>" id="<?php echo $theme_shortname?>_display_right_banner_use_<?php echo $i?>" value='N' <?php echo (get_option($theme_shortname."_display_right_banner_use_".$i)=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_right_banner_use_<?php echo $i?>">사용안함</label>
						</dd>
					</dl>
					<dl>
						<dt>이미지</dt>
						<dd>
							<input id='<?php echo $theme_shortname?>_display_right_banner_img_<?php echo $i?>' name='<?php echo $theme_shortname?>_display_right_banner_img_<?php echo $i?>' type='text' value='<?php echo get_option($theme_shortname."_display_right_banner_img_".$i)?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('display_right_banner_img_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
							<span style="display:inline-block;">
							<?php
							if(get_option($theme_shortname."_display_right_banner_img_".$i))
							{
							echo "<a href='".get_option($theme_shortname."_display_right_banner_img_".$i)."' data-lightbox='display_right_banner_img_".$i."'><img src='".get_option($theme_shortname."_display_right_banner_img_".$i)."' class='fileimg'></a>";
							}
							?>
							</span>
						</dd>
					</dl>
					<dl>
						<dt>링크 URL</dt>
						<dd>
							<input type='text' name='<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_display_right_banner_url_".$i)?>' style='width:70%;'/>

							<input type="radio" name="<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_right_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window_self">현재창</label>

							<input type="radio" name="<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_display_right_banner_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_right_banner_url_<?php echo $i?>_window_blank">새창</label>

						</dd>
					</dl>
				</div>
				<?php }?>
			</div>
		</div>
      </li><!-- /오른쪽 배너 설정 -->
<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use    = get_option($theme_shortname."_display_use_cs_center")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_display_use_cs_center")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_display_use_cs_center")=='U'?'':'style="display:none"';
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"> C/S CENTER 설정</div>
		<div class="item">
			<div class="slideItemsTitle">
				사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_display_use_cs_center" data-target="display_use_cs_center" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_display_use_cs_center" id="<?php echo $theme_shortname?>_display_use_cs_center" value='<?php echo get_option($theme_shortname."_display_use_cs_center")?>'  />
			</div>
			<div class="display_use_cs_center" <?php echo $show?>>

				<div class="slideItemsTitle sub">게시판 설정</div>
				<dl class="dl14">
					<dt>적용타입</dt>
					<dd>
					<?php
						$brd_b = "";$brd_c = "";
						if(!table_exists('bbse_board')) {
							$brd_b = "BBS e-Board 플러그인을 먼저 설치해주세요.";
						}else{
							$total = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.TABLES WHERE table_name='".$wpdb->prefix."bbse_board'");
							if($total>0) $tbName=$wpdb->prefix."bbse_board";
							else $tbName="bbse_board";

							$brd_res = $wpdb->get_results("select * from ".$tbName." order by board_no asc");
							if(count($brd_res) > 0){
								foreach($brd_res as $i=>$brd) {
									$no = $i+1;
									$brd_b .= "<input type='radio' name='".$theme_shortname."_display_cs_center_board_content' id='".$theme_shortname."_display_cs_center_board_content".$no."' value='".$brd->boardname."' ".((get_option($theme_shortname."_display_cs_center_board_content")==$brd->boardname)?'checked':'')." style='border:0px;'><label for='".$theme_shortname."_display_cs_center_board_content".$no."'>".$brd->boardname."</label>&nbsp;&nbsp;";
								}
							}else{
								$brd_b = "생성된 게시판이 없습니다.";
							}
						}

						$categories = get_categories( $args );
						if(count($categories) > 0){
							foreach ( $categories as $i=>$category ) {
								$no = $i+1;
								$brd_c .= "<input type='radio' name='".$theme_shortname."_display_cs_center_board_content' id='".$theme_shortname."_display_cs_center_board_content".$no."' value='".$category->term_id."' ".((get_option($theme_shortname."_display_cs_center_board_content")==$category->term_id)?'checked':'')." style='border:0px;'><label for='".$theme_shortname."_display_cs_center_board_content".$no."'>".$category->name."</label>&nbsp;&nbsp;";
							}
						}else{
							$brd_c = "생성된 카테고리가 없습니다.";
						}
					?>
					<script>
						function csLoadType(typ) {
							var cs_b="<?php echo $brd_b?>";
							var cs_c="<?php echo $brd_c?>";
							if(typ=="B") {
								jQuery("#csLoadTypeList").html(cs_b);
								jQuery("#cs_board_apply_page").show();
							} else if(typ=="C") {
								jQuery("#csLoadTypeList").html(cs_c);
								jQuery("#cs_board_apply_page").hide();
							}
						}
					</script>
						<input type="radio" name="<?php echo $theme_shortname?>_display_cs_center_board_type" id="<?php echo $theme_shortname?>_display_cs_center_board_type1" value='B' <?php echo (get_option($theme_shortname."_display_cs_center_board_type")=='B')?'checked="checked"':''?> style='border:0px;' onclick="csLoadType(this.value);"><label for="<?php echo $theme_shortname?>_display_cs_center_board_type1" >BBS e-Board</label>
					</dd>
				</dl>
				<dl>
					<dt>타이틀</dt>
					<dd>
						<input type='text' name='<?php echo $theme_shortname?>_display_cs_center_board_title' id='<?php echo $theme_shortname?>_display_cs_center_board_title' value='<?php echo get_option($theme_shortname."_display_cs_center_board_title")?>' style='width:70%;'>
					</dd>
				</dl>
				<dl>
					<dt>적용내용</dt>
					<dd>
						<div id="csLoadTypeList">
						<?php
							if(get_option($theme_shortname."_display_cs_center_board_type")=="B") {
								echo $brd_b;
								$brd_display = "";
							}else if(get_option($theme_shortname."_display_cs_center_board_type")=="C") {
								echo $brd_c;
								$brd_display = "none";
							}
						?>
						</div>
					</dd>
				</dl>
				<dl id="cs_board_apply_page" style="display:<?php echo $brd_display?>">
					<dt>적용페이지</dt>
					<dd>
						<select name="<?php echo $theme_shortname?>_display_cs_center_board_page">
							<option value="">페이지 선택</option>
							<?php 
							$pageData = get_custom_list('page');
							for($z = 0; $z < sizeof($pageData); $z++){
								if($pageData[$z]['id'] == get_option($theme_shortname."_display_cs_center_board_page")) $pageSelect = " selected";
								else $pageSelect = "";
							?>
							<option value="<?php echo $pageData[$z]['id'];?>"<?php echo $pageSelect;?>><?php echo $pageData[$z]['name'];?></option>
							<?php 
							}
							?>
						</select>
					</dd>
				</dl>

				<div class="slideItemsTitle sub">C/S CENTER 설정</div>
				<dl>
					<dt>타이틀</dt>
					<dd>
						<input type='text' name='<?php echo $theme_shortname?>_display_cs_center_title' id='<?php echo $theme_shortname?>_display_cs_center_title' value='<?php echo get_option($theme_shortname."_display_cs_center_title")?>' style='width:70%;'>
					</dd>
				</dl>
				<dl>
					<dt>전화번호</dt>
					<dd>
						<input type='text' name='<?php echo $theme_shortname?>_display_cs_center_phone' id='<?php echo $theme_shortname?>_display_cs_center_phone' value='<?php echo get_option($theme_shortname."_display_cs_center_phone")?>'>
					</dd>
				</dl>
				<dl class="dl14">
					<dt>내용</dt>
					<dd>
						<textarea name="<?php echo $theme_shortname?>_display_cs_center_content" id="<?php echo $theme_shortname?>_display_cs_center_content" rows="5" style="width:100%;"><?php echo stripcslashes(get_option($theme_shortname."_display_cs_center_content"))?></textarea>
					</dd>
				</dl>
				

				<?php
					$option_menu_count = get_option($theme_shortname."_display_option_menu_count")?get_option($theme_shortname."_display_option_menu_count"):0;
					$option_menu_last = get_option($theme_shortname."_display_option_menu_last")?get_option($theme_shortname."_display_option_menu_last"):0;
				?>
				<div class="slideItemsTitle sub">
					부가메뉴설정
					<input type="hidden" name="<?php echo $theme_shortname?>_display_option_menu_count" id="<?php echo $theme_shortname?>_display_option_menu_count" value='<?php echo $option_menu_count?>'  />
					<input type="hidden" name="<?php echo $theme_shortname?>_display_option_menu_last" id="<?php echo $theme_shortname?>_display_option_menu_last" value='<?php echo $option_menu_last?>'  />
					<input class='button-secondary' onClick="optionMenu('add',parseInt(jQuery('#<?php echo $theme_shortname?>_display_option_menu_last').val()));" type='button' value='추가' style='float:right;height:23px;font-size:11px;' />
				</div>
				<div class="desc2">최대 4개까지 설정이 가능합니다.</div>
				<div class="display_option_menu">
					<?php for ($i=1; $i<=$option_menu_count; $i++) {?>
					<div id="option_menu_item<?php echo $i?>">
						<div style='padding-top:20px;padding-bottom:10px;font-weight:bold;width:100%;border-bottom:1px dotted #ddd;'>부가메뉴 <?php echo $i?> <input class='button-secondary' onClick="optionMenu('del',<?php echo $i?>);" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>
						<dl>
							<dt>메뉴명</dt>
							<dd>
								<input type='text' name='<?php echo $theme_shortname?>_display_option_menu_title_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_option_menu_title_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_display_option_menu_title_".$i)?>' style='width:70%;'>
							</dd>
						</dl>
						<dl>
							<dt>링크</dt>
							<dd>
								<input type='text' name='<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>' id='<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_display_option_menu_link_".$i)?>' style='width:70%;'>&nbsp;
								<input type="radio" name="<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_option_menu_link_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>_window_self">현재창</label>
								<input type="radio" name="<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_display_option_menu_link_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_display_option_menu_link_<?php echo $i?>_window_blank">새창</label>
							</dd>
						</dl>
					</div>
					<?php }?>
				</div>
			</div>
		</div>
      </li><!-- /C/S CENTER 설정 -->
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('display');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('display');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
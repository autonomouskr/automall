<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname,$bbseLanguageCodes;
$preOpenValue = get_option($theme_shortname."_basic_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

$mallID=bbse_get_mallid();
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='basic' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_basic_preOpen' id='<?php echo $theme_shortname?>_basic_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">메인화면 설정 - 기본 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_basic_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>


<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
    	<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use    = get_option($theme_shortname."_sub_use_top_banner")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_sub_use_top_banner")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_sub_use_top_banner")=='U'?'':'style="display:none"';
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"> 상단 배너 설정</div>
		<div class="item">
			<div class="slideItemsTitle">사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_sub_use_top_banner" data-target="sub_use_top_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_sub_use_top_banner" id="<?php echo $theme_shortname?>_sub_use_top_banner" value='<?php echo get_option($theme_shortname."_sub_use_top_banner")?>'  />
			</div>
			<div class="sub_use_top_banner" <?php echo $show?>>
			<span class="desc2">이미지 사이즈는 가로 1903xp, 세로 300px이하 입니다.</span><br />
			  <dl>
				<dt>이미지</dt>
				<dd>
				  <input id='<?php echo $theme_shortname?>_sub_top_banner_image_1' name='<?php echo $theme_shortname?>_sub_top_banner_image_1' type='text' value='<?php echo get_option($theme_shortname."_sub_top_banner_image_1")?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('sub_top_banner_image_1');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				  <span style="display:inline-block;">
				  <?php
					if(get_option($theme_shortname."_sub_top_banner_image_1"))
					{
					  echo "<a href='".get_option($theme_shortname."_sub_top_banner_image_1")."' data-lightbox='sub_top_banner_image_1'><img src='".get_option($theme_shortname."_sub_top_banner_image_1")."' class='fileimg'></a>";
					}
				  ?>
				  </span>
				</dd>
			  </dl>
			  <dl>
				<dt>링크</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_sub_top_banner_link_1' id='<?php echo $theme_shortname?>_sub_top_banner_link_1' value='<?php echo get_option($theme_shortname."_sub_top_banner_link_1")?>' style='width:70%;'>&nbsp;
				  <input type="radio" name="<?php echo $theme_shortname?>_sub_top_banner_link_1_window" id="<?php echo $theme_shortname?>_sub_top_banner_link_1_window_self" value='_self' <?php echo (get_option($theme_shortname."_sub_top_banner_link_1_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sub_top_banner_link_1_window_self">현재창</label>
				  <input type="radio" name="<?php echo $theme_shortname?>_sub_top_banner_link_1_window" id="<?php echo $theme_shortname?>_sub_top_banner_link_1_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_sub_top_banner_link_1_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sub_top_banner_link_1_window_blank">새창</label>
				</dd>
			  </dl>
			</div>
		</div>
      </li><!-- /메인 상단배너 설정 -->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">언어 사이트 설정</div>
		<div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p>- 해당 사이트의 주 콘텐츠 표시에 사용되는 언어를 설정합니다.<br />
				- 언어 사이트 설정은 Google 등의 검색엔진의 검색결과에 적절한 언어 또는 지역 URL을 제공합니다.</p>
			</div>
			<br />
			<dl style="border-bottom: 0px;">
			<?php
			$use    = get_option($theme_shortname."_basic_hreflang_use")=='U'?'yes':'no';
			$show   = get_option($theme_shortname."_basic_hreflang_use")=='U'?'':'style="display:none"';
			?>
			  <dt>사용여부</td>
				<dd>
					<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_basic_hreflang_use" data-target="hrefLang_use" style="margin-left:35px;cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				  <input type="hidden" name="<?php echo $theme_shortname?>_basic_hreflang_use" id="<?php echo $theme_shortname?>_basic_hreflang_use" value='<?php echo get_option($theme_shortname."_basic_hreflang_use");?>' style="cursor:pointer" />
			  </dd>
			</dl>
			<div class="hrefLang_use" <?php echo $show;?>>
				<dl>
				  <dt>언어 선택</dt>
				  <dd>
					<select name="<?php echo $theme_shortname?>_basic_hreflang" id="<?php echo $theme_shortname?>_basic_hreflang">
					<?php
					$nowLang=(get_option($theme_shortname."_basic_hreflang"))?get_option($theme_shortname."_basic_hreflang"):"ko";
					asort($bbseLanguageCodes);
					foreach($bbseLanguageCodes as $key => $value){
					  if($nowLang == $key) $hrefLangSelected='selected="selected"';
					  else $hrefLangSelected="";
					  echo "<option value='".$key."' ".$hrefLangSelected.">".$value . "</option>";
					}
					?>
					</select>
				  </dd>
				</dl>
			</div>
		</div>
      </li><!-- 언어 사이트 설정 -->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">로고 설정(상단)</div>
		<div class="item">
			<p class="desc2">이미지 : 가로(210px), 세로(35px) 이내 사이즈로 사용해 주세요.</p>
			<dl>
			  <dt>로고 종류</dt>
			  <dd>
				<?php
				  $textTypeTop  = get_option($theme_shortname."_basic_logo_type_top") == 'text'  ? '':'style="display:none"';
				  $imageTypeTop = get_option($theme_shortname."_basic_logo_type_top") == 'image' ? '':'style="display:none"';
				?>
				<input type="radio" name="<?php echo $theme_shortname?>_basic_logo_type_top" id="<?php echo $theme_shortname?>_basic_logo_type_text_top1" onclick="jQuery('.imageLogoTop').css('display','none');jQuery('.textLogoTop').css('display','block');" value='text' <?php echo (get_option($theme_shortname."_basic_logo_type_top")=='text')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_logo_type_text_top1">텍스트 로고</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_basic_logo_type_top" id="<?php echo $theme_shortname?>_basic_logo_type_image_top2" onclick="jQuery('.imageLogoTop').css('display','block');jQuery('.textLogoTop').css('display','none');" value='image' <?php echo (get_option($theme_shortname."_basic_logo_type_top")=='image')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_logo_type_image_top2">이미지 로고</label>
			  </dd>
			</dl>

			  <dl class="textLogoTop" <?php echo $textTypeTop?>>
				<dt>타이틀</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_logo_text_top' id='<?php echo $theme_shortname?>_basic_logo_text_top' value='<?php echo get_option($theme_shortname."_basic_logo_text_top")?>'>
				</dd>
			  </dl>
			  <dl class="textLogoTop" <?php echo $textTypeTop?>>
				<dt>글꼴 색상</dt>
				<dd>
				  <?php $id_str= (get_option($theme_shortname."_basic_logo_color_top")!="") ? get_option($theme_shortname."_basic_logo_color_top") : "#ffffff"?>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_logo_color_top' id='<?php echo $theme_shortname?>_basic_logo_color_top' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>
			  </dl>
			  <dl class="textLogoTop" <?php echo $textTypeTop?>>
				<dt>글꼴크기</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_logo_text_size_top' id='<?php echo $theme_shortname?>_basic_logo_text_size_top' value='<?php echo get_option($theme_shortname."_basic_logo_text_size_top")?>' style="width:50px;">px
				  <p class="desc2">크기는 36px 내외가 적당합니다.</p>
				</dd>
			  </dl>

			  <dl class="imageLogoTop" <?php echo $imageTypeTop?>>
				<dt>이미지</dt>
				<dd>
				  <input id='<?php echo $theme_shortname?>_basic_logo_img_top' name='<?php echo $theme_shortname?>_basic_logo_img_top' type='text' value='<?php echo get_option($theme_shortname."_basic_logo_img_top")?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('basic_logo_img_top');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				  <span style="display:inline-block;">
				  <?php
					if(get_option($theme_shortname."_basic_logo_img_top"))
					{
					  echo "<a href='".get_option($theme_shortname."_basic_logo_img_top")."' data-lightbox='basic_logo_img_top'><img src='".get_option($theme_shortname."_basic_logo_img_top")."' class='fileimg'></a>";
					}
				  ?>
				  </span>
				</dd>
			  </dl>
			<dl>
				<dt>로고위치 (top)</dt>
				<dd>
					<input id='<?php echo $theme_shortname?>_basic_logo_top_margin' class="slider" name='<?php echo $theme_shortname?>_basic_logo_top_margin' type="range"  min="10" max="70" step="1" value='<?php echo (get_option($theme_shortname."_basic_logo_top_margin"))?get_option($theme_shortname."_basic_logo_top_margin"):'50'?>' />
					<span class="sliderValue" style="display:inline-block;height:2.5em;line-height:2.5em;font-weight:bold;vertical-align:top;">
					  <?php echo (get_option($theme_shortname."_basic_logo_top_margin"))?get_option($theme_shortname."_basic_logo_top_margin"):'50'?>
					</span>

					<p class="desc2">수치가 낮아질 수록 로고가 쇼핑몰 상단에 가까워 집니다. (추천 50)</p>
				</dd>
			</dl>
		</div>
      </li><!-- 사이트 로고 -->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">로고 설정(하단)</div>
		<div class="item">
			<dl>
			  <dt>로고 종류</dt>
			  <dd>
				<?php
				  $textTypeBottom  = get_option($theme_shortname."_basic_logo_type_bottom") == 'text'  ? '':'style="display:none"';
				  $imageTypeBottom = get_option($theme_shortname."_basic_logo_type_bottom") == 'image' ? '':'style="display:none"';
				?>
				<input type="radio" name="<?php echo $theme_shortname?>_basic_logo_type_bottom" id="<?php echo $theme_shortname?>_basic_logo_type_text_bottom1" onclick="jQuery('.imageLogoBottom').css('display','none');jQuery('.textLogoBottom').css('display','block');" value='text' <?php echo (get_option($theme_shortname."_basic_logo_type_bottom")=='text')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_logo_type_text_bottom1">텍스트 로고</label>
				&nbsp;&nbsp;&nbsp;
				<input type="radio" name="<?php echo $theme_shortname?>_basic_logo_type_bottom" id="<?php echo $theme_shortname?>_basic_logo_type_image_bottom2" onclick="jQuery('.imageLogoBottom').css('display','block');jQuery('.textLogoBottom').css('display','none');" value='image' <?php echo (get_option($theme_shortname."_basic_logo_type_bottom")=='image')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_logo_type_image_bottom2">이미지 로고</label>
			  </dd>
			</dl>

			  <dl class="textLogoBottom" <?php echo $textTypeBottom?>>
				<dt>타이틀</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_logo_text_bottom' id='<?php echo $theme_shortname?>_basic_logo_text_bottom' value='<?php echo get_option($theme_shortname."_basic_logo_text_bottom")?>'>
				</dd>
			  </dl>
			  <dl class="textLogoBottom" <?php echo $textTypeBottom?>>
				<dt>글꼴 색상</dt>
				<dd>
				  <?php $id_str= (get_option($theme_shortname."_basic_logo_color_bottom")!="") ? get_option($theme_shortname."_basic_logo_color_bottom") : "#ffffff"?>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_logo_color_bottom' id='<?php echo $theme_shortname?>_basic_logo_color_bottom' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>
			  </dl>
			  <dl class="textLogoBottom" <?php echo $textTypeBottom?>>
				<dt>글꼴크기</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_logo_text_size_bottom' id='<?php echo $theme_shortname?>_basic_logo_text_size_bottom' value='<?php echo get_option($theme_shortname."_basic_logo_text_size_bottom")?>' style="width:50px;">px
				  <p class="desc2">크기는 16px 내외가 적당합니다.</p>
				</dd>
			  </dl>

			  <dl class="imageLogoBottom" <?php echo $imageTypeBottom?>>
				<dt>이미지</dt>
				<dd>
				  <input id='<?php echo $theme_shortname?>_basic_logo_img_bottom' name='<?php echo $theme_shortname?>_basic_logo_img_bottom' type='text' value='<?php echo get_option($theme_shortname."_basic_logo_img_bottom")?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('basic_logo_img_bottom');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				  <span style="display:inline-block;">
				  <?php
					if(get_option($theme_shortname."_basic_logo_img_bottom"))
					{
					  echo "<a href='".get_option($theme_shortname."_basic_logo_img_bottom")."' data-lightbox='basic_logo_img_bottom'><img src='".get_option($theme_shortname."_basic_logo_img_bottom")."' class='fileimg'></a>";
					}
				  ?>
				  </span>

				  <div class="desc2"> 가로(200px), 세로(33px) 이내 사이즈로 사용해 주세요.</div>
				</dd>
			  </dl>

		</div>
      </li><!-- 사이트 로고 -->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">파비콘</div>
		<div class="item">
			<div class="desc2"> 주소창이나 탭에 조그만 아이콘으로 표시되는 즐겨찾기 아이콘으로 확장자는 ico입니다.</div>
			<div class="desc2"> 권장사이즈는 가로세로 16px, 정사각형입니다.</div>
			<div class="desc2"> 참고사이트 : <a href="http://favicon-generator.org/" target="_blank" >Favicon Generator</a></div>
			<dl>
			  <dt>아이콘</dt>
			  <dd>
				<input id='<?php echo $theme_shortname?>_basic_favorit_icon' name='<?php echo $theme_shortname?>_basic_favorit_icon' type='text' value='<?php echo get_option($theme_shortname."_basic_favorit_icon")?>' style='width:70%;' />&nbsp;<input  class='button-secondary' onClick="upload_img('basic_favorit_icon');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				<span style="display:inline-block;">
				<?php
				  if(get_option($theme_shortname."_basic_favorit_icon")) $img_view="<img src='".get_option($theme_shortname."_basic_favorit_icon")."'  class='fileimg'>";
				  else $img_view="";
				?>
				  <span id="faviconViewWrap" style=""><?php echo $img_view;?></span>
				</span>
			  </dd>
			</dl>
		</div>
      </li><!-- /파비콘 -->
<!-- ******************************************************************************************************************************************************* -->
     <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">메인 슬라이드</div>
		<div class="item">
			<span class="desc2"> 이미지 : 가로(860px), 세로(420px) 이내 사이즈로 사용해주세요.</span>
			<?php
			for ($i=1; $i<=3; $i++)
			{
				$use    = get_option($theme_shortname."_basic_main_slide_use_".$i)=='U'?'yes':'no';
				$show   = get_option($theme_shortname."_basic_main_slide_use_".$i)=='U'?'':'style="display:none"';
			?>
			<div class="slideItemsTitle">슬라이드<?php echo $i?>&nbsp;&nbsp;
			  <span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_basic_main_slide_use_<?php echo $i?>" data-target="main_slide<?php echo $i?>" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
			  <input type="hidden" name="<?php echo $theme_shortname?>_basic_main_slide_use_<?php echo $i?>" id="<?php echo $theme_shortname?>_basic_main_slide_use_<?php echo $i?>" value='<?php echo get_option($theme_shortname."_basic_main_slide_use_".$i)?>'  />
			</div>
			<div class="main_slide<?php echo $i?>" <?php echo $show?>>
			  <dl>
				<dt>탭 문구</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_main_slide_excerpt_<?php echo $i?>' id='<?php echo $theme_shortname?>_basic_main_slide_excerpt_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_basic_main_slide_excerpt_".$i)?>' style="width:40%;" />
				</dd>
			  </dl>
			  <dl>
				<dt>이미지</dt>
				<dd>
				  <input id='<?php echo $theme_shortname?>_basic_main_slide_img_<?php echo $i?>' name='<?php echo $theme_shortname?>_basic_main_slide_img_<?php echo $i?>' type='text' value='<?php echo get_option($theme_shortname."_basic_main_slide_img_".$i)?>' style='width:70%;' />&nbsp;<input  class='button-secondary' onClick="upload_img('basic_main_slide_img_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				  <span style="display:inline-block;">
				  <?php
					if(get_option($theme_shortname."_basic_main_slide_img_".$i))
					{
					  echo "<a href='".get_option($theme_shortname."_basic_main_slide_img_".$i)."' data-lightbox='basic_main_slide_img'><img src='".get_option($theme_shortname."_basic_main_slide_img_".$i)."' class='fileimg'></a>";
					}
				  ?>
				  </span>
				</dd>
			  </dl>
			  <dl>
				<dt>링크 URL</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>' id='<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_basic_main_slide_url_".$i)?>' style="width:70%;" />

				<input type="radio" name="<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_basic_main_slide_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>_window_self">현재창</label>

				<input type="radio" name="<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_basic_main_slide_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_main_slide_url_<?php echo $i?>_window_blank">새창</label>

				</dd>
			  </dl>
			</div>
			<?php }?>
		</div>
      </li><!-- /메인슬라이드-->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">하단 퀵링크</div>
		<div class="item">
			<?php for ($i=1; $i<6; $i++) {?>
			<div class="slideItemsTitle">항목<?php echo $i?></div>
			<dl>
			  <dt>타이틀</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>" id="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>" value='<?php echo get_option($theme_shortname."_basic_footer_quick_link_".$i)?>' style="width:40%;" />
			  </dd>
			</dl>
			<dl>
			  <dt>링크</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_url" id="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_url" value="<?php echo get_option($theme_shortname."_basic_footer_quick_link_".$i."_url")?>" style="width:70%;">

				<input type="radio" name="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_basic_footer_quick_link_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_window_self">현재창</label>

				<input type="radio" name="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_basic_footer_quick_link_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_footer_quick_link_<?php echo $i?>_window_blank">새창</label>
			  </dd>
			</dl>
			<?php }?>
		</div>
      </li><!-- /하단 퀵링크 -->
<!-- ******************************************************************************************************************************************************* -->
	<script>
		function familySite(mode, i) {
			var createHtml = "";
			var siteCount = parseInt(jQuery("#<?php echo $theme_shortname?>_basic_use_footer_family_site_count").val());
			var siteLast = parseInt(jQuery("#<?php echo $theme_shortname?>_basic_use_footer_family_site_last").val());
			if(mode=="add") {
				if(!i) {i = 1;}else{i += 1;}
				createHtml += "	<div id=\"family_site_item"+i+"\">";
				createHtml += "		<div class=\"slideItemsTitle sub\">항목"+i+" <input class='button-secondary' onClick=\"familySite('del',"+i+");\" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>";
				createHtml += "        <dl>";
				createHtml += "          <dt>타이틀</dt>";
				createHtml += "          <dd>";
				createHtml += "            <input type=\"text\" name=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"\" id=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"\" value=''>";
				createHtml += "          </dd>";
				createHtml += "        </dl>";
				createHtml += "        <dl>";
				createHtml += "          <dt>링크</dt>";
				createHtml += "          <dd>";
				createHtml += "            <input type=\"text\" name=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_url\" id=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_url\" value=\"\" style=\"width:70%;\">";
				createHtml += "            <input type=\"radio\" name=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_window\" id=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_window_self\" value='_self' style='border:0px;' checked='checked'><label for=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_window_self\">현재창</label>";
				createHtml += "            <input type=\"radio\" name=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_window\" id=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_window_blank\" value='_blank' style='border:0px;'><label for=\"<?php echo $theme_shortname?>_basic_footer_family_site_"+i+"_window_blank\">새창</label>";
				createHtml += "          </dd>";
				createHtml += "        </dl>";
				createHtml += "	</div>";
				jQuery("#<?php echo $theme_shortname?>_basic_use_footer_family_site_last").val(i);
				jQuery("#<?php echo $theme_shortname?>_basic_use_footer_family_site_count").val(siteCount+1);
				jQuery(".basic_use_footer_family_site").append(createHtml);
			}else{
				jQuery("#<?php echo $theme_shortname?>_basic_use_footer_family_site_count").val(siteCount-1)
				jQuery("#family_site_item"+i).remove();

				if(jQuery("[id^='family_site_item']").size()) {
					var last_item = jQuery("[id^='family_site_item']").last().attr("id");
					i = parseInt(last_item.replace("family_site_item",""));
				}else{
					i = 0;
				}
				jQuery("#<?php echo $theme_shortname?>_basic_use_footer_family_site_last").val(i);
			}
		}
		function bottomBanner(mode, i) {
			var createHtml = "";
			var siteCount = parseInt(jQuery("#<?php echo $theme_shortname?>_basic_use_bottom_banner_count").val());
			var siteLast = parseInt(jQuery("#<?php echo $theme_shortname?>_basic_use_bottom_banner_last").val());
			var mallId="<?php echo $mallID;?>";
			if(mode=="add") {
				if(!i) {i = 1;}else{i += 1;}
				createHtml += "			<div id=\"bottom_banner_item"+i+"\">";
				createHtml += "				<div class=\"slideItemsTitle sub\">항목"+i;
				createHtml += "					<select name='<?php echo $theme_shortname?>_basic_bottom_banner_type_"+i+"' id='<?php echo $theme_shortname?>_basic_bottom_banner_type_"+i+"' style='margin-left:20px;line-height: 24px;height: 24px;font-weight: normal;margin-top: -3px;' onChange='bottomBanner_change_type("+i+");'>";
				createHtml += "						<option value='normal'>일반 인증마크</option>";
				createHtml += "						<option value='lguplus'>LG U+ 에스크로 인증마크</option>";
				createHtml += "						<option value='inicis'>이니시스 에스크로 인증마크</option>";
				createHtml += "						<option value='allthegate'>올더게이트 에스크로 인증마크</option>";
				createHtml += "						<option value='fairtrade'>공정거래위원회 사업자조회</option>";
				createHtml += "					</select>";
				createHtml += "					<input class='button-secondary' onClick=\"bottomBanner('del',"+i+");\" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>";
				createHtml += "				<dl>";
				createHtml += "					<dt>이미지</dt>";
				createHtml += "					<dd>";
				createHtml += "						<input id='<?php echo $theme_shortname?>_basic_bottom_banner_img_"+i+"' name='<?php echo $theme_shortname?>_basic_bottom_banner_img_"+i+"' type='text' value='' style='width:70%;' />&nbsp;<input class='button-secondary' onClick=\"upload_img('basic_bottom_banner_img_"+i+"');\" type='button' value='찾아보기' style='height:23px;font-size:11px;' />";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "				<dl>";
				createHtml += "					<dt id='bottomBannerInfo_title_"+i+"'>링크 URL</dt>";
				createHtml += "					<dd>";
				createHtml += "						<span id='bottomBanner_mallId_"+i+"' style='display:none;'>상점 아이디 : <input type='text' name='<?php echo $theme_shortname?>_basic_bottom_banner_mallid_"+i+"' id='<?php echo $theme_shortname?>_basic_bottom_banner_mallid_"+i+"' value='"+mallId+"' /></span>";
				createHtml += "						<span id='bottomBanner_businessNo_"+i+"' style='display:none;'><span id='bottomBanner_businessNo_title_"+i+"' style='display:inline;'>사업자 등록번호 : </span><input type='text' name='<?php echo $theme_shortname?>_basic_bottom_banner_businessno_"+i+"' id='<?php echo $theme_shortname?>_basic_bottom_banner_businessno_"+i+"' value='' /></span>";
				createHtml += "						<span id='bottomBanner_url_"+i+"' style='display:inline;'><input type='text' name='<?php echo $theme_shortname?>_basic_bottom_banner_url_"+i+"' id='<?php echo $theme_shortname?>_basic_bottom_banner_url_"+i+"' value='' style='width:70%;' /></span>";
				createHtml += "						<span id='bottomBanner_window_"+i+"' style='display:inline;'><input type=\"radio\" name=\"<?php echo $theme_shortname?>_basic_bottom_banner_url_"+i+"_window\" id=\"<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window_self\" value='_self' style='border:0px;' checked='checked'><label for=\"<?php echo $theme_shortname?>_basic_bottom_banner_url_"+i+"_window_self\">현재창</label>";
				createHtml += "						<input type=\"radio\" name=\"<?php echo $theme_shortname?>_basic_bottom_banner_url_"+i+"_window\" id=\"<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window_blank\" value='_blank' style='border:0px;'><label for=\"<?php echo $theme_shortname?>_basic_bottom_banner_url_"+i+"_window_blank\">새창</label></span>";
				createHtml += "					</dd>";
				createHtml += "				</dl>";
				createHtml += "			</div>";
				
				jQuery("#<?php echo $theme_shortname?>_basic_use_bottom_banner_last").val(i);
				jQuery("#<?php echo $theme_shortname?>_basic_use_bottom_banner_count").val(siteCount+1);
				jQuery(".basic_use_bottom_banner").append(createHtml);
			}else{
				jQuery("#<?php echo $theme_shortname?>_basic_use_bottom_banner_count").val(siteCount-1)
				jQuery("#bottom_banner_item"+i).remove();

				if(jQuery("[id^='bottom_banner_item']").size()) {
					var last_item = jQuery("[id^='bottom_banner_item']").last().attr("id");
					i = parseInt(last_item.replace("bottom_banner_item",""));
				}else{
					i = 0;
				}
				jQuery("#<?php echo $theme_shortname?>_basic_use_bottom_banner_last").val(i);
			}
		}

		function bottomBanner_change_type(btmNo){
			var themeShortname="<?php echo $theme_shortname?>";
			var selectType=jQuery("#"+themeShortname+"_basic_bottom_banner_type_"+btmNo).val();
			var mallId="<?php echo $mallID;?>";
			if(selectType=='normal'){
				jQuery("#bottomBannerInfo_title_"+btmNo).html("링크 URL");
				jQuery("#bottomBanner_mallId_"+btmNo).css("display","none");
				jQuery("#bottomBanner_businessNo_"+btmNo).css("display","none");
				jQuery("#bottomBanner_businessNo_title_"+btmNo).css("display","inline");
				jQuery("#bottomBanner_url_"+btmNo).css("display","inline");
				jQuery("#bottomBanner_window_"+btmNo).css("display","inline");
			}
			else if(selectType=='lguplus'){
				jQuery("#bottomBannerInfo_title_"+btmNo).html("상점정보");
				jQuery("#bottomBanner_mallId_"+btmNo).css("display","inline");
				if(!jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val()){
					jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val(mallId);
				}
				jQuery("#bottomBanner_businessNo_"+btmNo).css("display","none");
				jQuery("#bottomBanner_businessNo_title_"+btmNo).css("display","none");
				jQuery("#bottomBanner_url_"+btmNo).css("display","none");
				jQuery("#bottomBanner_window_"+btmNo).css("display","none");
			}
			else if(selectType=='inicis'){
				jQuery("#bottomBannerInfo_title_"+btmNo).html("상점정보");
				jQuery("#bottomBanner_mallId_"+btmNo).css("display","inline");
				if(!jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val()){
					jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val(mallId);
				}
				jQuery("#bottomBanner_businessNo_"+btmNo).css("display","none");
				jQuery("#bottomBanner_businessNo_title_"+btmNo).css("display","none");
				jQuery("#bottomBanner_url_"+btmNo).css("display","none");
				jQuery("#bottomBanner_window_"+btmNo).css("display","none");
			}
			else if(selectType=='allthegate'){
				jQuery("#bottomBannerInfo_title_"+btmNo).html("상점정보");
				jQuery("#bottomBanner_mallId_"+btmNo).css("display","inline");
				if(!jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val()){
					jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val(mallId);
				}
				jQuery("#bottomBanner_businessNo_"+btmNo).css("display","inline");
				jQuery("#bottomBanner_businessNo_title_"+btmNo).css("display","inline");
				jQuery("#bottomBanner_url_"+btmNo).css("display","none");
				jQuery("#bottomBanner_window_"+btmNo).css("display","none");
			}
			else if(selectType=='fairtrade'){
				jQuery("#bottomBannerInfo_title_"+btmNo).html("사업자등록번호");
				jQuery("#bottomBanner_mallId_"+btmNo).css("display","none");
				jQuery("#bottomBanner_businessNo_"+btmNo).css("display","inline");
				jQuery("#bottomBanner_businessNo_title_"+btmNo).css("display","none");
				jQuery("#bottomBanner_url_"+btmNo).css("display","none");
				jQuery("#bottomBanner_window_"+btmNo).css("display","none");
			}
		}
	</script>
      <?php
        $use   = get_option($theme_shortname."_basic_use_footer_family_site")=='U'?'yes':'no';
		$show = get_option($theme_shortname."_basic_use_footer_family_site")=='U'?'':'style="display:none"';
		$family_site_count = get_option($theme_shortname."_basic_use_footer_family_site_count")?get_option($theme_shortname."_basic_use_footer_family_site_count"):0;
		$family_site_last = get_option($theme_shortname."_basic_use_footer_family_site_last")?get_option($theme_shortname."_basic_use_footer_family_site_last"):0;
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">관계사 사이트</div>
		<div class="item">
			<div class="slideItemsTitle">사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_basic_use_footer_family_site" data-target="basic_use_footer_family_site" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_basic_use_footer_family_site" id="<?php echo $theme_shortname?>_basic_use_footer_family_site" value='<?php echo get_option($theme_shortname."_basic_use_footer_family_site")?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_basic_use_footer_family_site_count" id="<?php echo $theme_shortname?>_basic_use_footer_family_site_count" value='<?php echo $family_site_count?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_basic_use_footer_family_site_last" id="<?php echo $theme_shortname?>_basic_use_footer_family_site_last" value='<?php echo $family_site_last?>'  />
				<input class='button-secondary' onClick="familySite('add',parseInt(jQuery('#<?php echo $theme_shortname?>_basic_use_footer_family_site_last').val()));" type='button' value='추가' style='float:right;height:23px;font-size:11px;' />
			</div>
			<div class="basic_use_footer_family_site" <?php echo $show?>>
			<?php for ($i=1; $i<=$family_site_count; $i++) {?>
				<div id="family_site_item<?php echo $i?>">
					<div class="slideItemsTitle sub">항목<?php echo $i?> <input class='button-secondary' onClick="familySite('del',<?php echo $i?>);" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>
					<dl>
					  <dt>타이틀</dt>
					  <dd>
						<input type="text" name="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>" id="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>" value='<?php echo get_option($theme_shortname."_basic_footer_family_site_".$i)?>' style="width:40%;" />
					  </dd>
					</dl>
					<dl>
					  <dt>링크</dt>
					  <dd>
						<input type="text" name="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_url" id="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_url" value="<?php echo get_option($theme_shortname."_basic_footer_family_site_".$i."_url")?>" style="width:70%;">

						<input type="radio" name="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_basic_footer_family_site_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_window_self">현재창</label>

						<input type="radio" name="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_basic_footer_family_site_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_footer_family_site_<?php echo $i?>_window_blank">새창</label>

					  </dd>
					</dl>
				</div>
			<?php }?>
			</div>
		</div>
      </li><!-- /하단 퀵링크 -->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">하단 사이트 정보</div>
		<div class="item">
			<dl>
			  <dt>정보</dt>
			  <dd>
				<textarea name="<?php echo $theme_shortname?>_basic_footer" id="<?php echo $theme_shortname?>_basic_footer" rows="5" cols="" style="width:100%;"><?php echo stripcslashes(get_option($theme_shortname."_basic_footer"))?></textarea>
			  </dd>
			</dl>
		</div>
      </li><!-- /하단사이트 정보-->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">하단 사이트 저작권 정보</div>
		<div class="item">
			<dl>
			  <dt>정보</dt>
			  <dd>
				<textarea name="<?php echo $theme_shortname?>_basic_footer_copyright" id="<?php echo $theme_shortname?>_basic_footer_copyright" rows="5" cols="" style="width:100%;"><?php echo stripcslashes(get_option($theme_shortname."_basic_footer_copyright"))?></textarea>
			  </dd>
			</dl>
		</div>
      </li><!-- /하단 사이트 저작권 정보 -->
<!-- ******************************************************************************************************************************************************* -->

      <?php
        $use   = get_option($theme_shortname."_basic_use_bottom_banner")=='U'?'yes':'no';
		$show = get_option($theme_shortname."_basic_use_bottom_banner")=='U'?'':'style="display:none"';
		$bottom_banner_count = get_option($theme_shortname."_basic_use_bottom_banner_count")?get_option($theme_shortname."_basic_use_bottom_banner_count"):0;
		$bottom_banner_last = get_option($theme_shortname."_basic_use_bottom_banner_last")?get_option($theme_shortname."_basic_use_bottom_banner_last"):0;
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">인증마크 및 하단배너 설정</div>
		<div class="item">
			<div class="slideItemsTitle">사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_basic_use_bottom_banner" data-target="basic_use_bottom_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_basic_use_bottom_banner" id="<?php echo $theme_shortname?>_basic_use_bottom_banner" value='<?php echo get_option($theme_shortname."_basic_use_bottom_banner")?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_basic_use_bottom_banner_count" id="<?php echo $theme_shortname?>_basic_use_bottom_banner_count" value='<?php echo $bottom_banner_count?>'  />
				<input type="hidden" name="<?php echo $theme_shortname?>_basic_use_bottom_banner_last" id="<?php echo $theme_shortname?>_basic_use_bottom_banner_last" value='<?php echo $bottom_banner_last?>'  />

				<span  style='float:right;'>
					<input class='button-secondary' onClick="bottomBanner('add',parseInt(jQuery('#<?php echo $theme_shortname?>_basic_use_bottom_banner_last').val()));" type='button' value='추가' style='height:23px;font-size:11px;' />
				</span>
			</div>
			<div class="basic_use_bottom_banner" <?php echo $show?>>
				<span class="desc2"> 이미지 : 가로(150px), 세로(50px) 이내 사이즈로 사용해주세요.</span>
			<?php
			for ($i=1; $i<=$bottom_banner_count; $i++) {
				$optBannerType=get_option($theme_shortname."_basic_bottom_banner_type_".$i);
				if(!$optBannerType) $optBannerType="normal";
			?>
				<div id="bottom_banner_item<?php echo $i?>">
					<div class="slideItemsTitle sub">항목<?php echo $i?> 
					<select name="<?php echo $theme_shortname?>_basic_bottom_banner_type_<?php echo $i;?>" id="<?php echo $theme_shortname?>_basic_bottom_banner_type_<?php echo $i;?>" style="margin-left:20px;line-height: 24px;height: 24px;font-weight: normal;margin-top: -3px;" onChange="bottomBanner_change_type(<?php echo $i;?>);">
						<option value='normal' <?php echo ($optBannerType=='normal')?"selected='selected'":"";?>>일반 인증마크</option>
						<option value='lguplus' <?php echo ($optBannerType=='lguplus')?"selected='selected'":"";?>>LG U+ 에스크로 인증마크</option>
						<option value='inicis' <?php echo ($optBannerType=='inicis')?"selected='selected'":"";?>>이니시스 에스크로 인증마크</option>
						<option value='allthegate' <?php echo ($optBannerType=='allthegate')?"selected='selected'":"";?>>올더게이트 에스크로 인증마크</option>
						<option value='fairtrade' <?php echo ($optBannerType=='fairtrade')?"selected='selected'":"";?>>공정거래위원회 사업자조회</option>
					</select>

					<input class='button-secondary' onClick="bottomBanner('del',<?php echo $i?>);" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /></div>
					<dl>
						<dt>이미지</dt>
						<dd>
							<input id='<?php echo $theme_shortname?>_basic_bottom_banner_img_<?php echo $i?>' name='<?php echo $theme_shortname?>_basic_bottom_banner_img_<?php echo $i?>' type='text' value='<?php echo get_option($theme_shortname."_basic_bottom_banner_img_".$i)?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('basic_bottom_banner_img_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
							<span style="display:inline-block;">
							<?php
							if(get_option($theme_shortname."_basic_main_slide_".$i))
							{
							echo "<a href='".get_option($theme_shortname."_basic_bottom_banner_img_".$i)."' data-lightbox='basic_bottom_banner_img_".$i."'><img src='".get_option($theme_shortname."_basic_bottom_banner_img_".$i)."' class='fileimg'></a>";
							}
							?>
							</span>
						</dd>
					</dl>
					<dl>
						<dt id="bottomBannerInfo_title_<?php echo $i;?>"><?php if($optBannerType=='lguplus' || $optBannerType=='inicis' || $optBannerType=='allthegate'){echo "상점정보";}elseif($optBannerType=='fairtrade'){echo "사업자등록번호";}else{echo "링크 URL";}?></dt>
						<dd>
							<span id="bottomBanner_mallId_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='lguplus' || $optBannerType=='inicis' || $optBannerType=='allthegate')?"inline":"none";?>;">상점 아이디 : <input type="text" name="<?php echo $theme_shortname?>_basic_bottom_banner_mallid_<?php echo $i;?>" id="<?php echo $theme_shortname?>_basic_bottom_banner_mallid_<?php echo $i;?>" value="<?php echo get_option($theme_shortname."_basic_bottom_banner_mallid_".$i)?>" /></span>
							
							<span id="bottomBanner_businessNo_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='allthegate' || $optBannerType=='fairtrade')?"inline":"none";?>;"><span id="bottomBanner_businessNo_title_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='inicis' || $optBannerType=='allthegate')?"inline":"none";?>;">사업자 등록번호 : </span><input type="text" name="<?php echo $theme_shortname?>_basic_bottom_banner_businessno_<?php echo $i;?>" id="<?php echo $theme_shortname?>_basic_bottom_banner_businessno_<?php echo $i;?>" value="<?php echo get_option($theme_shortname."_basic_bottom_banner_businessno_".$i)?>" /></span>

							<span id="bottomBanner_url_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='normal')?"inline":"none";?>;"><input type="text" name='<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>' id='<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_basic_bottom_banner_url_".$i)?>' style="width:70%;" /></span>

							<span id="bottomBanner_window_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='normal')?"inline":"none";?>;"><input type="radio" name="<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window_self" value='_self' <?php echo ($optBannerType=='normal' && get_option($theme_shortname."_basic_bottom_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window_self">현재창</label>
							<input type="radio" name="<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window_blank" value='_blank' <?php echo ($optBannerType!='normal' || get_option($theme_shortname."_basic_bottom_banner_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_basic_bottom_banner_url_<?php echo $i?>_window_blank">새창</label></span>
						</dd>
					</dl>
				</div>
				<?php
				}
				?>
			</div>
		</div>
  
      </li><!-- /인증마크 및 하단배너 설정-->
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>


		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('basic');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('basic');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
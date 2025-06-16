<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_color_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='color' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_color_preOpen' id='<?php echo $theme_shortname?>_color_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">색(Color) 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_color_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">상단바 색상</div>
		<div class="item">
			  <?php $baseColor = '#C46181'?>
			  <dl>
				<dt>색상&nbsp;</dt>
				<dd>
				  <?php $id_str = get_option($theme_shortname."_color_main_theme") ? get_option($theme_shortname."_color_main_theme") : $baseColor;?>
				  <span style='font-weight:normal;font-size:11px;color:<?php echo $baseColor?>;'>(기본값 : <?php echo $baseColor?>)</span>&nbsp;&nbsp;
				  <input type='text' name='<?php echo $theme_shortname?>_color_main_theme' id='<?php echo $theme_shortname?>_color_main_theme' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>

			  </dl>
		</div>
	</li>
	<li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">사이드바 배경색</div>
		<div class="item">
			  <?php $baseColor = '#fff'?>
			  <dl>
				<dt>사이드바 배경색&nbsp;</dt>
				<dd>
				  <?php $id_str = get_option($theme_shortname."_color_sidebar_background") ? get_option($theme_shortname."_color_sidebar_background") : $baseColor;?>
				  <span style='font-weight:normal;font-size:11px;color:<?php echo $baseColor?>;'>(기본값 : <?php echo $baseColor?>)</span>&nbsp;&nbsp;
				  <input type='text' name='<?php echo $theme_shortname?>_color_sidebar_background' id='<?php echo $theme_shortname?>_color_sidebar_background' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>

			  </dl>
		</div>
	</li>
	<li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">사이드바 타이틀</div>
		<div class="item">
			  <?php $baseColor = '#222'?>
			  <dl>
				<dt>사이드바 타이틀&nbsp;</dt>
				<dd>
				  <?php $id_str = get_option($theme_shortname."_color_sidebar_title") ? get_option($theme_shortname."_color_sidebar_title") : $baseColor;?>
				  <span style='font-weight:normal;font-size:11px;color:<?php echo $baseColor?>;'>(기본값 : <?php echo $baseColor?>)</span>&nbsp;&nbsp;
				  <input type='text' name='<?php echo $theme_shortname?>_color_sidebar_title' id='<?php echo $theme_shortname?>_color_sidebar_title' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>

			  </dl>
		</div>
      </li>
      
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">메인메뉴바 배경색</div>
		<div class="item">
			  <?php $baseColor = '#fff'?>
			  <dl>
				<dt>메인메뉴바 배경색&nbsp;</dt>
				<dd>
				  <?php $id_str = get_option($theme_shortname."_color_mainmenu_bg") ? get_option($theme_shortname."_color_mainmenu_bg") : $baseColor;?>
				  <span style='font-weight:normal;font-size:11px;color:<?php echo $baseColor?>;'>(기본값 : <?php echo $baseColor?>)</span>&nbsp;&nbsp;
				  <input type='text' name='<?php echo $theme_shortname?>_color_mainmenu_bg' id='<?php echo $theme_shortname?>_color_mainmenu_bg' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>

			  </dl>
		</div>
      </li>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">메인메뉴 폰트 색상</div>
		<div class="item">
			  <?php $baseColor = '#222'?>
			  <dl>
				<dt>메인메뉴 폰트 색상&nbsp;</dt>
				<dd>
				  <?php $id_str = get_option($theme_shortname."_color_mainmenu_font") ? get_option($theme_shortname."_color_mainmenu_font") : $baseColor;?>
				  <span style='font-weight:normal;font-size:11px;color:<?php echo $baseColor?>;'>(기본값 : <?php echo $baseColor?>)</span>&nbsp;&nbsp;
				  <input type='text' name='<?php echo $theme_shortname?>_color_mainmenu_font' id='<?php echo $theme_shortname?>_color_mainmenu_font' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>

			  </dl>
		</div>
      </li>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">메인메뉴 폰트 크기</div>
		<div class="item">
				<?php $baseColor = '14px'?>
			  <dl>
				<dt>메인메뉴 폰트 크기&nbsp;</dt>
				<dd>
				  <?php $id_str = get_option($theme_shortname."_color_mainmenu_font_size") ? get_option($theme_shortname."_color_mainmenu_font_size") : $baseColor;?>
				  <span style='font-weight:normal;font-size:11px;color:<?php echo $baseColor?>;'>(기본값 : <?php echo $baseColor?>)</span>&nbsp;&nbsp;
				  <input type='text' name='<?php echo $theme_shortname?>_color_mainmenu_font_size' id='<?php echo $theme_shortname?>_color_mainmenu_font_size' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>
				</dd>

			  </dl>
		</div>
      </li>
    </ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('color');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('color');">초기화</button>
		</div>
		</form>
	</div>
<!-- //contents 끝 -->

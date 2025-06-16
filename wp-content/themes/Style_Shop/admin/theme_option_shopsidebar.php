<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

if(!plugin_active_check('BBSe_Commerce')){
	echo "<script language='javascript'>window.location.href='themes.php?page=functions.php&optTtpe=blogbasic';</script>";
	exit;
}

global $theme_shortname,$bbseGoodsListType;
$preOpenValue = get_option($theme_shortname."_shopsidebar_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

?>

  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='sub' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_shopsidebar_preOpen' id='<?php echo $theme_shortname?>_shopsidebar_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">[쇼핑몰] 서브화면 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_shopsidebar_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
	<li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"><img src="<?php echo get_template_directory_uri()."/admin/images/icon-title-shop.png";?>" align="absmiddle" /> 우측 사이드바 설정</div>
		<div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p>(*) 웹사이트 우측에 따라다니는 사이드바를 설정합니다.</p>
				<p>(*) 상단 배너, 퀵메뉴, 최근본상품, SNS로 구성됩니다.</p>
			</div>
			<br />

			<div class="use_left_sidebar" <?php echo $show?>>
				<?php
				$use   = get_option($theme_shortname."_shopsidebar_top_banner")=='U'?'yes':'no';
				$show = get_option($theme_shortname."_shopsidebar_top_banner")=='U'?'':'style="display:none"';
				?>
				<div class="slideItemsTitle">상단 이벤트 배너&nbsp;
				  <span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_shopsidebar_top_banner" data-target="top_banner" style="margin-left:35px;cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="desc2" style="font-weight:normal;">(*) 추천사이즈 : 80px X 110px</span>
				  <input type="hidden" name="<?php echo $theme_shortname?>_shopsidebar_top_banner" id="<?php echo $theme_shortname?>_shopsidebar_top_banner" value='<?php echo get_option($theme_shortname."_shopsidebar_top_banner")?>' style="cursor:pointer" />
				</div>
				<div class="top_banner" <?php echo $show?>>
				<?php for($i=1;$i<3;$i++){?>
				  <dl>
					<dt style="min-width:5%;">배너<?php echo $i;?></dt>
					<dd>
					  <dl style="border:0px;">
						<dt style="min-width:5%;max-width:10%;">이미지</dt>
						<dd>
						  <input type="text" id='<?php echo $theme_shortname?>_shopsidebar_top_banner_img_<?php echo $i;?>' name='<?php echo $theme_shortname?>_shopsidebar_top_banner_img_<?php echo $i;?>' value='<?php echo get_option($theme_shortname."_shopsidebar_top_banner_img_".$i);?>' style='width:70%;' />&nbsp;<input  class='button-secondary' onClick="upload_img('shopsidebar_top_banner_img_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
						  
						  <span style="display:inline-block;">
						  <?php
							if(get_option($theme_shortname."_shopsidebar_top_banner_img_".$i))
							{
							  echo "<a href='".get_option($theme_shortname."_shopsidebar_top_banner_img_".$i)."' data-lightbox='shopsidebar_top_banner_img'><img src='".get_option($theme_shortname."_shopsidebar_top_banner_img_".$i)."' class='fileimg'></a>";
							}
						  ?>
						  </span>
						</dd>
					  </dl>
					  <dl style="border:0px;">
						<dt style="min-width:5%;max-width:10%;">링크 URL</dt>
						<dd>
						  <input type='text' name='<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>' id='<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>' value='<?php echo get_option($theme_shortname."_shopsidebar_top_banner_url_".$i)?>' style="width:70%;" />
						  <input type="radio" name="<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>_window" id="<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_shopsidebar_top_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>_window_self">현재창</label>
						  <input type="radio" name="<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>_window" id="<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_shopsidebar_top_banner_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_shopsidebar_top_banner_url_<?php echo $i;?>_window_blank">새창</label>
						</dd>
					  </dl>
					</dd>
				  </dl>
			    <?php }?>
				</div>

				<?php
				$use   = get_option($theme_shortname."_shopsidebar_use_quick_menu")=='U'?'yes':'no';
				$show = get_option($theme_shortname."_shopsidebar_use_quick_menu")=='U'?'':'style="display:none"';
				?>
				<div class="slideItemsTitle">퀵 메뉴 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				  <span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_shopsidebar_use_quick_menu" data-target="use_quick_menu" style="margin-left:35px;cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>&nbsp;&nbsp;&nbsp;&nbsp;<span class="desc2" style="font-weight:normal;">(*) 추천사이즈 : 20px X 20px</span>
				  <input type="hidden" name="<?php echo $theme_shortname?>_shopsidebar_use_quick_menu" id="<?php echo $theme_shortname?>_shopsidebar_use_quick_menu" value='<?php echo get_option($theme_shortname."_shopsidebar_use_quick_menu")?>' style="cursor:pointer" />
				</div>
				<div class="use_quick_menu" <?php echo $show?>>
				<?php for($i=1;$i<4;$i++){?>
				  <dl>
					<dt style="min-width:5%;">메뉴<?php echo $i;?></dt>
					<dd>
					  <dl style="border:0px;">
						<dt style="min-width:5%;">아이콘이미지</dt>
						<dd>
						  <input type="text" id='<?php echo $theme_shortname?>_shopsidebar_quick_menu_img_<?php echo $i;?>' name='<?php echo $theme_shortname?>_shopsidebar_quick_menu_img_<?php echo $i;?>' value='<?php echo get_option($theme_shortname."_shopsidebar_quick_menu_img_".$i);?>' style='width:70%;' />&nbsp;<input  class='button-secondary' onClick="upload_img('shopsidebar_quick_menu_img_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
						  <span style="display:inline-block;">
						  <?php
							if(get_option($theme_shortname."_shopsidebar_quick_menu_img_".$i))
							{
							  echo "<a href='".get_option($theme_shortname."_shopsidebar_quick_menu_img_".$i)."' data-lightbox='shopsidebar_quick_menu_img'><img src='".get_option($theme_shortname."_shopsidebar_quick_menu_img_".$i)."' class='fileimg'></a>";
							}
						  ?>
						  </span>
						</dd>
					  </dl>
					  <dl style="border:0px;">
						<dt style="min-width:5%;">메뉴명</dt>
						<dd>
						  <input type='text' name='<?php echo $theme_shortname?>_shopsidebar_quick_menu_text_<?php echo $i;?>' id='<?php echo $theme_shortname?>_shopsidebar_quick_menu_text_<?php echo $i;?>' value='<?php echo get_option($theme_shortname."_shopsidebar_quick_menu_text_".$i)?>' style="width:70%;" />
						</dd>
					  </dl>
					  <dl style="border:0px;">
						<dt style="min-width:5%;">링크 URL</dt>
						<dd>
						  <input type='text' name='<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>' id='<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>' value='<?php echo get_option($theme_shortname."_shopsidebar_quick_menu_url_".$i)?>' style="width:70%;" />
						  <input type="radio" name="<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>_window" id="<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>_window_self" value='_self' <?php echo (get_option($theme_shortname."_shopsidebar_quick_menu_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>_window_self">현재창</label>
						  <input type="radio" name="<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>_window" id="<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_shopsidebar_quick_menu_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_shopsidebar_quick_menu_url_<?php echo $i;?>_window_blank">새창</label>
						</dd>
					  </dl>
					</dd>
				  </dl>
			    <?php }?>
				</div>
				
				<?php
				$use   = get_option($theme_shortname."_shopsidebar_use_sns_menu")=='U'?'yes':'no';
				$show = get_option($theme_shortname."_shopsidebar_use_sns_menu")=='U'?'':'style="display:none"';
				?>
				<div class="slideItemsTitle">SNS링크 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				  <span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_shopsidebar_use_sns_menu" data-target="use_sns_menu" style="margin-left:35px;cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>&nbsp;&nbsp;&nbsp;&nbsp;
				  <input type="hidden" name="<?php echo $theme_shortname?>_shopsidebar_use_sns_menu" id="<?php echo $theme_shortname?>_shopsidebar_use_sns_menu" value='<?php echo get_option($theme_shortname."_shopsidebar_use_sns_menu")?>' style="cursor:pointer" />
				</div>
				<div class="use_sns_menu" <?php echo $show?>>
					<dl>
					<dt style="min-width:5%;">카카오톡</dt>
					<dd>
					 	<dl style="border:0px;">
						<dt style="min-width:5%;">링크 URL</dt>
						<dd>
						  <input type='text' name='<?php echo $theme_shortname?>_shopsidebar_sns_kakao' id='<?php echo $theme_shortname?>_shopsidebar_sns_kakao' value='<?php echo get_option($theme_shortname."_shopsidebar_sns_kakao")?>' style="width:70%;" />
						</dd>
					  </dl>
					</dd>
				  </dl>
				  <dl>
					<dt style="min-width:5%;">네이버톡</dt>
					<dd>
					 	<dl style="border:0px;">
						<dt style="min-width:5%;">링크 URL</dt>
						<dd>
						  <input type='text' name='<?php echo $theme_shortname?>_shopsidebar_sns_naver' id='<?php echo $theme_shortname?>_shopsidebar_sns_naver' value='<?php echo get_option($theme_shortname."_shopsidebar_sns_naver")?>' style="width:70%;" />
						</dd>
					  </dl>
					</dd>
				  </dl>
				  <dl>
					<dt style="min-width:5%;">인스타그램</dt>
					<dd>
					 	<dl style="border:0px;">
						<dt style="min-width:5%;">링크 URL</dt>
						<dd>
						  <input type='text' name='<?php echo $theme_shortname?>_shopsidebar_sns_insta' id='<?php echo $theme_shortname?>_shopsidebar_sns_insta' value='<?php echo get_option($theme_shortname."_shopsidebar_sns_insta")?>' style="width:70%;" />
						</dd>
					  </dl>
					</dd>
				  </dl>
				  <dl>
					<dt style="min-width:5%;">페이스북</dt>
					<dd>
					 	<dl style="border:0px;">
						<dt style="min-width:5%;">링크 URL</dt>
						<dd>
						  <input type='text' name='<?php echo $theme_shortname?>_shopsidebar_sns_facebook' id='<?php echo $theme_shortname?>_shopsidebar_sns_facebook' value='<?php echo get_option($theme_shortname."_shopsidebar_sns_facebook")?>' style="width:70%;" />
						</dd>
					  </dl>
					</dd>
				  </dl>
				  
				  
				</div>
			</div>
		</div>
	</li><!-- /사이드바 설정 -->
</ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('shopsidebar');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('shopsidebar');">초기화</button>
		</div>
	</div>
	</form>
	<!-- //contents 끝 -->


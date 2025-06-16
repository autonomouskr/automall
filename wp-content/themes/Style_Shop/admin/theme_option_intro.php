<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_intro_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='intro' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_intro_preOpen' id='<?php echo $theme_shortname?>_intro_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">테마 인트로 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_intro_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>


<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">테마 인트로 설정</div>
		<div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p style="font-weight:bold;">*테마 인트로 설정</p>
				&nbsp;- 메인화면 접속 시 보여질 인트로를 설정합니다.. 
				<p class="desc2">성인인증 인트로의 경우 반드시 BBS e-Commerce > 회원관리 > 환경설정 메뉴에서 [본인인증 서비스]를 신청하셔야 합니다.</p>
			</div>
			<br />
			<dl>
			  <dt>인트로 사용여부</dt>
				<?php
				$use   = get_option($theme_shortname."_intro_use")=='U'?'yes':'no';
				$show = get_option($theme_shortname."_intro_use")=='U'?'':'style="display:none"';
				?>
			  <dd>
				  <span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_intro_use" data-target="themeIntro-19-Priority" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				  <input type="hidden" name="<?php echo $theme_shortname?>_intro_use" id="<?php echo $theme_shortname?>_intro_use" value='<?php echo get_option($theme_shortname."_intro_use")?>' style="cursor:pointer" />
			  </dd>
			</dl>

			<span class="themeIntro-19-Priority" <?php echo $show;?>>
				<dl style="border-top:0px;">
					<dt>인트로 종류</dt>
					<dd>
						<input type="radio" name="<?php echo $theme_shortname?>_intro_type" id="<?php echo $theme_shortname?>_intro_type" value='adult' <?php echo (!get_option($theme_shortname."_intro_type") || get_option($theme_shortname."_intro_type")=='adult')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_type">성인인증</label>
					</dd>
				</dl>
				<dl style="border-top:0px;">
					<dt>인증 수단</dt>
					<dd>
					<?php
					$config = $wpdb->get_row("SELECT * FROM bbse_commerce_membership_config LIMIT 1");
					$authuse = $config->certification_yn;				// NICE 본인인증 사용여부
					$sitecode = $config->certification_id;				// NICE로부터 부여받은 사이트 코드
					$sitepasswd = $config->certification_pass;			// NICE로부터 부여받은 사이트 패스워드
					if($authuse=='Y' && $sitecode && $sitepasswd){
					?>
						<input type="radio" name="<?php echo $theme_shortname?>_intro_auth_kind" id="<?php echo $theme_shortname?>_intro_auth_kind" value='mobile' <?php echo (!get_option($theme_shortname."_intro_auth_kind") || get_option($theme_shortname."_intro_auth_kind")=='mobile')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_auth_kind_mobile">휴대폰 인증</label>
					<?php
					}
					else{
					?>
						<p class="desc2"> 휴대폰 본인인증을 위해 BBS e-Commerce > 회원관리 > 환경설정 페이지의 [본인인증 서비스] 정보를 입력해 주세요.</p>
					<?php 
					}
					?>
					</dd>
				</dl>
				<div class="themeIntro-19-Priority slideItemsTitle" <?php echo $show;?>>
					디자인 설정
				</div>

				<dl>
					<dt>로고</dt>
					<dd>
						<input id='<?php echo $theme_shortname?>_intro_19_logo' name='<?php echo $theme_shortname?>_intro_19_logo' type='text' value='<?php echo get_option($theme_shortname."_intro_19_logo")?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('intro_19_logo');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
						<span style="display:inline-block;">
						<?php
						if(get_option($theme_shortname."_intro_19_logo")){
						echo "<a href='".get_option($theme_shortname."_intro_19_logo")."' data-lightbox='intro_19_logo'><img src='".get_option($theme_shortname."_intro_19_logo")."' class='fileimg'></a>";
						}
						?>
						</span><br />
						<span class="desc2"> 권장 사이즈 : 가로(200px), 세로(80px)</span>
					</dd>
				</dl>
				<dl>
					<dt>상단 배경색</dt>
					<dd>
					  <?php 
					  $top_bg= (get_option($theme_shortname."_intro_19_color_top_background")!="") ? get_option($theme_shortname."_intro_19_color_top_background") : "#ffffff";
					  $top_bg_basic="#ffffff";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_top_background" id="<?php echo $theme_shortname?>_intro_19_color_top_background" class='colpick' value='<?php echo $top_bg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $top_bg_basic?>;'>(기본값 : <?php echo $top_bg_basic?>)</span>
					</dd>
				</dl>
				<dl>
					<dt>본문 배경색</dt>
					<dd>
					  <?php 
					  $middle_bg= (get_option($theme_shortname."_intro_19_color_middle_background")!="") ? get_option($theme_shortname."_intro_19_color_middle_background") : "#ffffff";
					  $middle_bg_basic="#ffffff";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_middle_background" id="<?php echo $theme_shortname?>_intro_19_color_middle_background" class='colpick' value='<?php echo $middle_bg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $middle_bg_basic?>;'>(기본값 : <?php echo $middle_bg_basic?>)</span>
					</dd>
				</dl>
				<dl>
					<dt>본문 글자색</dt>
					<dd>
					  <?php 
					  $middle_fg= (get_option($theme_shortname."_intro_19_color_middle_foreground")!="") ? get_option($theme_shortname."_intro_19_color_middle_foreground") : "#000000";
					  $middle_fg_basic="#000000";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_middle_foreground" id="<?php echo $theme_shortname?>_intro_19_color_middle_foreground" class='colpick' value='<?php echo $middle_fg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $middle_fg_basic?>;'>(기본값 : <?php echo $middle_fg_basic?>)</span>
					</dd>
				</dl>

				<dl>
					<dt>하단 배경색</dt>
					<dd>
					  <?php 
					  $bottom_bg= (get_option($theme_shortname."_intro_19_color_bottom_background")!="") ? get_option($theme_shortname."_intro_19_color_bottom_background") : "#222222";
					  $bottom_bg_basic="#222222";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_bottom_background" id="<?php echo $theme_shortname?>_intro_19_color_bottom_background" class='colpick' value='<?php echo $bottom_bg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $bottom_bg_basic?>;'>(기본값 : <?php echo $bottom_bg_basic?>)</span>
					</dd>
				</dl>
				<dl>
					<dt>하단 글자색</dt>
					<dd>
					  <?php 
					  $bottom_fg= (get_option($theme_shortname."_intro_19_color_bottom_foreground")!="") ? get_option($theme_shortname."_intro_19_color_bottom_foreground") : "#ffffff";
					  $bottom_fg_basic="#ffffff";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_bottom_foreground" id="<?php echo $theme_shortname?>_intro_19_color_bottom_foreground" class='colpick' value='<?php echo $bottom_fg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $bottom_fg_basic?>;'>(기본값 : <?php echo $bottom_fg_basic?>)</span>
					</dd>
				</dl>
				<dl>
					<dt>휴대폰인증 버튼 배경색</dt>
					<dd>
					  <?php 
					  $button_bg= (get_option($theme_shortname."_intro_19_color_button_background")!="") ? get_option($theme_shortname."_intro_19_color_button_background") : "#ffbf44";
					  $button_bg_basic="#ffbf44";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_button_background" id="<?php echo $theme_shortname?>_intro_19_color_button_background" class='colpick' value='<?php echo $button_bg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $button_bg_basic?>;'>(기본값 : <?php echo $button_bg_basic?>)</span>
					</dd>
				</dl>
				<dl>
					<dt>휴대폰인증 버튼 글자색</dt>
					<dd>
					  <?php 
					  $button_fg= (get_option($theme_shortname."_intro_19_color_button_foreground")!="") ? get_option($theme_shortname."_intro_19_color_button_foreground") : "#ffffff";
					  $button_fg_basic="#ffffff";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_button_foreground" id="<?php echo $theme_shortname?>_intro_19_color_button_foreground" class='colpick' value='<?php echo $button_fg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $button_fg_basic?>;'>(기본값 : <?php echo $button_fg_basic?>)</span>
					</dd>
				</dl>

				<dl>
					<dt>19세 미만 나가기 버튼 배경색</dt>
					<dd>
					  <?php 
					  $exitbutton_bg= (get_option($theme_shortname."_intro_19_color_exitbutton_background")!="") ? get_option($theme_shortname."_intro_19_color_exitbutton_background") : "#ff8c37";
					  $exitbutton_bg_basic="#ff8c37";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_exitbutton_background" id="<?php echo $theme_shortname?>_intro_19_color_exitbutton_background" class='colpick' value='<?php echo $exitbutton_bg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $exitbutton_bg_basic?>;'>(기본값 : <?php echo $exitbutton_bg_basic?>)</span>
					</dd>
				</dl>
				<dl>
					<dt>19세 미만 나가기 버튼 글자색</dt>
					<dd>
					  <?php 
					  $exitbutton_fg= (get_option($theme_shortname."_intro_19_color_exitbutton_foreground")!="") ? get_option($theme_shortname."_intro_19_color_exitbutton_foreground") : "#ffffff";
					  $exitbutton_fg_basic="#ffffff";
					  ?>
					  <input type="text" name="<?php echo $theme_shortname?>_intro_19_color_exitbutton_foreground" id="<?php echo $theme_shortname?>_intro_19_color_exitbutton_foreground" class='colpick' value='<?php echo $exitbutton_fg;?>' />&nbsp;&nbsp;<span style='margin-left:80px;font-weight:normal;font-size:11px;color:<?php echo $exitbutton_fg_basic?>;'>(기본값 : <?php echo $exitbutton_fg_basic?>)</span>
					</dd>
				</dl>
				<dl>
					<dt>상단 배너</dt>
					<dd>
						<input id='<?php echo $theme_shortname?>_intro_19_top_banner' name='<?php echo $theme_shortname?>_intro_19_top_banner' type='text' value='<?php echo get_option($theme_shortname."_intro_19_top_banner")?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('intro_19_top_banner');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
						<span style="display:inline-block;">
						<?php
						if(get_option($theme_shortname."_intro_19_top_banner")){
						echo "<a href='".get_option($theme_shortname."_intro_19_top_banner")."' data-lightbox='intro_19_top_banner'><img src='".get_option($theme_shortname."_intro_19_top_banner")."' class='fileimg'></a>";
						}
						?>
						</span><br />
						<span class="desc2"> 권장 사이즈 : 가로(1210px), 세로(300px)</span>
					</dd>
				</dl>

				<div class="slideItemsTitle">
					하단 배너 및 인증마크
				</div>
				<dl>
					<dt>사용여부</dt>
					<dd>
					  <?php
						$use   = get_option($theme_shortname."_intro_19_use_bottom_banner")=='U'?'yes':'no';
						$show = get_option($theme_shortname."_intro_19_use_bottom_banner")=='U'?'':'style="display:none"';
						$bottom_banner_count = get_option($theme_shortname."_intro_19_use_bottom_banner_count")?get_option($theme_shortname."_intro_19_use_bottom_banner_count"):0;
						$bottom_banner_last = get_option($theme_shortname."_intro_19_use_bottom_banner_last")?get_option($theme_shortname."_intro_19_use_bottom_banner_last"):0;
					  ?>
						<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_intro_19_use_bottom_banner" data-target="intro_19_use_bottom_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
						<input type="hidden" name="<?php echo $theme_shortname?>_intro_19_use_bottom_banner" id="<?php echo $theme_shortname?>_intro_19_use_bottom_banner" value='<?php echo get_option($theme_shortname."_intro_19_use_bottom_banner")?>'  />
						<input type="hidden" name="<?php echo $theme_shortname?>_intro_19_use_bottom_banner_count" id="<?php echo $theme_shortname?>_intro_19_use_bottom_banner_count" value='<?php echo $bottom_banner_count?>'  />
						<input type="hidden" name="<?php echo $theme_shortname?>_intro_19_use_bottom_banner_last" id="<?php echo $theme_shortname?>_intro_19_use_bottom_banner_last" value='<?php echo $bottom_banner_last?>'  />

						<span  style='float:right;'>
							<input class='button-secondary' onClick="bottomBanner('add',parseInt(jQuery('#<?php echo $theme_shortname?>_intro_19_use_bottom_banner_last').val()));" type='button' value='추가' style='height:23px;font-size:11px;' />
						</span>
					</dd>
				</dl>
				<dl class="intro_19_use_bottom_banner" <?php echo $show?>>
					<dt>하단 배너 정렬</dt>
					<dd>
						<input type="radio" name="<?php echo $theme_shortname?>_intro_19_bottom_banner_align" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_align" value='left' <?php echo (get_option($theme_shortname."_intro_19_bottom_banner_align")=='left')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_19_bottom_banner_align_left">왼쪽정렬</label>
						<input type="radio" name="<?php echo $theme_shortname?>_intro_19_bottom_banner_align" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_align" value='center' <?php echo (!get_option($theme_shortname."_intro_19_bottom_banner_align") || get_option($theme_shortname."_intro_19_bottom_banner_align")=='center')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_19_bottom_banner_align_center">중앙정렬</label>
						<input type="radio" name="<?php echo $theme_shortname?>_intro_19_bottom_banner_align" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_align" value='right' <?php echo (get_option($theme_shortname."_intro_19_bottom_banner_align")=='right')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_19_bottom_banner_align_right">오른쪽정렬</label>
					</dd>
				</dl>
				<dl class="intro_19_use_bottom_banner" <?php echo $show?> style="border-bottom:0px;">
					<dt></dt>
					<dd><span class="desc2"> 권장 사이즈 : 가로(250px), 세로(60px)</span></dd>
				</dl>
				<span id="intro19BottomBanner" class="intro_19_use_bottom_banner" <?php echo $show?>>

					<?php
					for ($i=1; $i<=$bottom_banner_count; $i++) {
						$optBannerType=get_option($theme_shortname."_intro_19_bottom_banner_type_".$i);
						if(!$optBannerType) $optBannerType="normal";
					?>
							<dl id="bottom_banner_item<?php echo $i?>">
								<dt>항목<?php echo $i?> </dt>
								<dd>
									<select name="<?php echo $theme_shortname?>_intro_19_bottom_banner_type_<?php echo $i;?>" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_type_<?php echo $i;?>" style="line-height: 24px;height: 24px;font-weight: normal;margin-top: -3px;" onChange="bottomBanner_change_type(<?php echo $i;?>);">
										<option value='normal' <?php echo ($optBannerType=='normal')?"selected='selected'":"";?>>일반 배너</option>
										<option value='lguplus' <?php echo ($optBannerType=='lguplus')?"selected='selected'":"";?>>LG U+ 에스크로 인증마크</option>
										<option value='inicis' <?php echo ($optBannerType=='inicis')?"selected='selected'":"";?>>이니시스 에스크로 인증마크</option>
										<option value='allthegate' <?php echo ($optBannerType=='allthegate')?"selected='selected'":"";?>>올더게이트 에스크로 인증마크</option>
										<option value='fairtrade' <?php echo ($optBannerType=='fairtrade')?"selected='selected'":"";?>>공정거래위원회 사업자조회</option>
									</select>

									<input class='button-secondary' onClick="bottomBanner('del',<?php echo $i?>);" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /><br />
									이미지 : <input id='<?php echo $theme_shortname?>_intro_19_bottom_banner_img_<?php echo $i?>' name='<?php echo $theme_shortname?>_intro_19_bottom_banner_img_<?php echo $i?>' type='text' value='<?php echo get_option($theme_shortname."_intro_19_bottom_banner_img_".$i)?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('intro_19_bottom_banner_img_<?php echo $i?>');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
									<span style="display:inline-block;">
									<?php
									if(get_option($theme_shortname."_intro_19_bottom_banner_img_".$i))
									{
									echo "<a href='".get_option($theme_shortname."_intro_19_bottom_banner_img_".$i)."' data-lightbox='intro_19_bottom_banner_img_".$i."'><img src='".get_option($theme_shortname."_intro_19_bottom_banner_img_".$i)."' class='fileimg'></a>";
									}
									?>
									</span><br />
									<span id="bottomBanner_mallId_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='lguplus' || $optBannerType=='inicis' || $optBannerType=='allthegate')?"inline":"none";?>;">상점 아이디 : <input type="text" name="<?php echo $theme_shortname?>_intro_19_bottom_banner_mallid_<?php echo $i;?>" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_mallid_<?php echo $i;?>" value="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_mallid_".$i)?>" /></span>
									
									<span id="bottomBanner_businessNo_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='allthegate' || $optBannerType=='fairtrade')?"inline":"none";?>;">사업자 등록번호 : <input type="text" name="<?php echo $theme_shortname?>_intro_19_bottom_banner_businessno_<?php echo $i;?>" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_businessno_<?php echo $i;?>" value="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_businessno_".$i)?>" /></span>

									<span id="bottomBanner_url_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='normal')?"inline":"none";?>;">링크 URL : <input type="text" name='<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>' id='<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>' value='<?php echo get_option($theme_shortname."_intro_19_bottom_banner_url_".$i)?>' style="width:70%;" /></span>

									<span id="bottomBanner_window_<?php echo $i;?>" style="display:<?php echo ($optBannerType=='normal')?"inline":"none";?>;"><input type="radio" name="<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>_window_self" value='_self' <?php echo ($optBannerType=='normal' && get_option($theme_shortname."_intro_19_bottom_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>_window_self">현재창</label>
									<input type="radio" name="<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>_window" id="<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>_window_blank" value='_blank' <?php echo ($optBannerType!='normal' || get_option($theme_shortname."_intro_19_bottom_banner_url_".$i."_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_19_bottom_banner_url_<?php echo $i?>_window_blank">새창</label></span>
								</dd>
							</dl>
					<?
					}
					?>
				</span>

				<script language="javascript">
					function bottomBanner(mode, i) {
						var createHtml = "";
						var siteCount = parseInt(jQuery("#<?php echo $theme_shortname?>_intro_19_use_bottom_banner_count").val());
						var siteLast = parseInt(jQuery("#<?php echo $theme_shortname?>_intro_19_use_bottom_banner_last").val());
						var mallId="<?php echo $mallID;?>";
						var themeShortname="<?php echo $theme_shortname;?>";
						if(mode=="add") {
							if(!i) {i = 1;}else{i += 1;}
							createHtml += "			<dl id='bottom_banner_item"+i+"'>";
							createHtml += "				<dt>항목"+i+"</dt>";
							createHtml += "				<dd><select name='"+themeShortname+"_intro_19_bottom_banner_type_"+i+"' id='"+themeShortname+"_intro_19_bottom_banner_type_"+i+"' style='line-height: 24px;height: 24px;font-weight: normal;margin-top: -3px;' onChange='bottomBanner_change_type("+i+");'>";
							createHtml += "						<option value='normal'>일반 배너</option>";
							createHtml += "						<option value='lguplus'>LG U+ 에스크로 인증마크</option>";
							createHtml += "						<option value='inicis'>이니시스 에스크로 인증마크</option>";
							createHtml += "						<option value='allthegate'>올더게이트 에스크로 인증마크</option>";
							createHtml += "						<option value='fairtrade'>공정거래위원회 사업자조회</option>";
							createHtml += "					</select>";
							createHtml += "					<input class='button-secondary' onClick=\"bottomBanner('del',"+i+");\" type='button' value='삭제' style='float:right;height:23px;font-size:11px;' /><br />";
							createHtml += "					이미지 : <input id='"+themeShortname+"_intro_19_bottom_banner_img_"+i+"' name='"+themeShortname+"_intro_19_bottom_banner_img_"+i+"' type='text' value='' style='width:70%;' />&nbsp;<input class='button-secondary' onClick=\"upload_img('intro_19_bottom_banner_img_"+i+"');\" type='button' value='찾아보기' style='height:23px;font-size:11px;' /><br />";
							createHtml += "						<span id='bottomBanner_mallId_"+i+"' style='display:none;'>상점 아이디 : <input type='text' name='"+themeShortname+"_intro_19_bottom_banner_mallid_"+i+"' id='"+themeShortname+"_intro_19_bottom_banner_mallid_"+i+"' value='"+mallId+"' /></span>";
							createHtml += "						<span id='bottomBanner_businessNo_"+i+"' style='display:none;'>사업자 등록번호 : <input type='text' name='"+themeShortname+"_intro_19_bottom_banner_businessno_"+i+"' id='"+themeShortname+"_intro_19_bottom_banner_businessno_"+i+"' value='' /></span>";
							createHtml += "						<span id='bottomBanner_url_"+i+"' style='display:inline;'>링크 URL : <input type='text' name='"+themeShortname+"_intro_19_bottom_banner_url_"+i+"' id='"+themeShortname+"_intro_19_bottom_banner_url_"+i+"' value='' style='width:70%;' /></span>";
							createHtml += "						<span id='bottomBanner_window_"+i+"' style='display:inline;'><input type=\"radio\" name=\""+themeShortname+"_intro_19_bottom_banner_url_"+i+"_window\" id=\""+themeShortname+"_intro_19_bottom_banner_url_"+i+"_window_self\" value='_self' style='border:0px;' checked='checked'><label for=\""+themeShortname+"_intro_19_bottom_banner_url_"+i+"_window_self\">현재창</label>";
							createHtml += "						<input type=\"radio\" name=\""+themeShortname+"_intro_19_bottom_banner_url_"+i+"_window\" id=\""+themeShortname+"_intro_19_bottom_banner_url_<?php echo $i?>_window_blank\" value='_blank' style='border:0px;'><label for=\""+themeShortname+"_intro_19_bottom_banner_url_"+i+"_window_blank\">새창</label></span>";
							createHtml += "				</dd>";
							createHtml += "			</dl>";
							
							jQuery("#"+themeShortname+"_intro_19_use_bottom_banner_last").val(i);
							jQuery("#"+themeShortname+"_intro_19_use_bottom_banner_count").val(siteCount+1);
							jQuery("#intro19BottomBanner").append(createHtml);
						}else{
							jQuery("#"+themeShortname+"_intro_19_use_bottom_banner_count").val(siteCount-1)
							jQuery("#bottom_banner_item"+i).remove();

							if(jQuery("[id^='bottom_banner_item']").size()) {
								var last_item = jQuery("[id^='bottom_banner_item']").last().attr("id");
								i = parseInt(last_item.replace("bottom_banner_item",""));
							}else{
								i = 0;
							}
							jQuery("#"+themeShortname+"_intro_19_use_bottom_banner_last").val(i);
						}
					}

					function bottomBanner_change_type(btmNo){
						var themeShortname="<?php echo $theme_shortname;?>";
						var selectType=jQuery("#"+themeShortname+"_intro_19_bottom_banner_type_"+btmNo).val();
						var mallId="<?php echo $mallID;?>";
						if(selectType=='normal'){
							jQuery("#bottomBanner_mallId_"+btmNo).css("display","none");
							jQuery("#bottomBanner_businessNo_"+btmNo).css("display","none");
							jQuery("#bottomBanner_url_"+btmNo).css("display","inline");
							jQuery("#bottomBanner_window_"+btmNo).css("display","inline");

						}
						else if(selectType=='lguplus'){
							jQuery("#bottomBanner_mallId_"+btmNo).css("display","inline");
							if(!jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val()){
								jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val(mallId);
							}
							jQuery("#bottomBanner_businessNo_"+btmNo).css("display","none");
							jQuery("#bottomBanner_url_"+btmNo).css("display","none");
							jQuery("#bottomBanner_window_"+btmNo).css("display","none");
						}
						else if(selectType=='inicis'){
							jQuery("#bottomBanner_mallId_"+btmNo).css("display","inline");
							if(!jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val()){
								jQuery("#"+themeShortname+"_basic_bottom_banner_mallid_"+btmNo).val(mallId);
							}
							jQuery("#bottomBanner_businessNo_"+btmNo).css("display","none");
							jQuery("#bottomBanner_url_"+btmNo).css("display","none");
							jQuery("#bottomBanner_window_"+btmNo).css("display","none");
						}
						else if(selectType=='allthegate'){
							jQuery("#bottomBanner_mallId_"+btmNo).css("display","inline");
							if(!jQuery("#"+themeShortname+"_intro_19_bottom_banner_mallid_"+btmNo).val()){
								jQuery("#"+themeShortname+"_intro_19_bottom_banner_mallid_"+btmNo).val(mallId);
							}
							jQuery("#bottomBanner_businessNo_"+btmNo).css("display","inline");
							jQuery("#bottomBanner_url_"+btmNo).css("display","none");
							jQuery("#bottomBanner_window_"+btmNo).css("display","none");
						}
						else if(selectType=='fairtrade'){
							jQuery("#bottomBanner_mallId_"+btmNo).css("display","none");
							jQuery("#bottomBanner_businessNo_"+btmNo).css("display","inline");
							jQuery("#bottomBanner_url_"+btmNo).css("display","none");
							jQuery("#bottomBanner_window_"+btmNo).css("display","none");
						}
					}
				</script>

				<div class="slideItemsTitle">
					19세 아이콘
				</div>
				<dl>
				  <dt>19세 아이콘</dt>
					<?php
					$show2 = get_option($theme_shortname."_intro_19_icon")=='upload'?'':'style="display:none"';
					?>
				  <dd>
						<input type="radio" name="<?php echo $theme_shortname?>_intro_19_icon" id="<?php echo $theme_shortname?>_intro_19_icon_basic" value='basic' onClick="jQuery('.intro19IconUpload').css('display','none');" <?php echo (!get_option($theme_shortname."_intro_19_icon")=='basic' || get_option($theme_shortname."_intro_19_icon")=='basic')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_19_icon_basic">기본 아이콘 사용</label>
						<input type="radio" name="<?php echo $theme_shortname?>_intro_19_icon" id="<?php echo $theme_shortname?>_intro_19_icon_upload" value='upload' onClick="jQuery('.intro19IconUpload').css('display','block');" <?php echo (get_option($theme_shortname."_intro_19_icon")=='upload')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_intro_19_icon_upload">이미지 업로드</label>
				  </dd>
				</dl>
				<dl class="intro19IconUpload" <?php echo $show2;?>>
				  <dt>아이콘 업로드</dt>
				  <dd>
						<input id='<?php echo $theme_shortname?>_intro_19_icon_img' name='<?php echo $theme_shortname?>_intro_19_icon_img' type='text' value='<?php echo get_option($theme_shortname."_intro_19_icon_img")?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('intro_19_icon_img');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
						<span style="display:inline-block;">
						<?php
						if(get_option($theme_shortname."_intro_19_icon_img")){
						echo "<a href='".get_option($theme_shortname."_intro_19_icon_img")."' data-lightbox='intro_19_icon_img'><img src='".get_option($theme_shortname."_intro_19_icon_img")."' class='fileimg'></a>";
						}
						?>
						</span>
				  </dd>
				</dl>

				<div class="slideItemsTitle">
					안내문구
				</div>
				<dl>
				  <dt>안내문구</dt>
				  <dd>
						<textarea type="textarea" name="<?php echo $theme_shortname?>_intro_19_introduce" id="<?php echo $theme_shortname?>_intro_19_introduce" style="width:70%;height:80px;" /><?php echo stripcslashes(get_option($theme_shortname."_intro_19_introduce"))?></textarea><br />
				  </dd>
				</dl>

				<div class="slideItemsTitle">
					19세미만 나가기
				</div>
				<dl>
				  <dt>사용여부</dt>
					<?php
					$use3  = get_option($theme_shortname."_intro_19_under_exit_use")=='U'?'yes':'no';
					$show3 = get_option($theme_shortname."_intro_19_under_exit_use")=='U'?'':'style="display:none"';
					?>
				  <dd>
					  <span class="useCheck" data-use="<?php echo $use3?>" data-container="<?php echo $theme_shortname?>_intro_19_under_exit_use" data-target="exit19Url" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use3?>.png" /></span>
					  <input type="hidden" name="<?php echo $theme_shortname?>_intro_19_under_exit_use" id="<?php echo $theme_shortname?>_intro_19_under_exit_use" value='<?php echo get_option($theme_shortname."_intro_19_under_exit_use")?>' style="cursor:pointer" />
				  </dd>
				</dl>
				<dl class="exit19Url" <?php echo $show3;?>>
				  <dt>이동 URL</dt>
				  <dd>
					<span style="margin-left:18px;font-weight:normal;"></span><span style="margin-left:33px;"><input type="text" name="<?php echo $theme_shortname?>_intro_19_under_exit_url" id="<?php echo $theme_shortname?>_intro_19_under_exit_url" value="<?php echo get_option($theme_shortname."_intro_19_under_exit_url")?>" style="width:70%;"></span>
				  </dd>
				</dl>

				<div class="slideItemsTitle">
					푸터정보
				</div>
				<dl>
				  <dt>푸터 내용</dt>
				  <dd>
						<textarea type="textarea" name="<?php echo $theme_shortname?>_intro_19_footer" id="<?php echo $theme_shortname?>_intro_19_footer" style="width:70%;height:80px;" /><?php echo stripcslashes(get_option($theme_shortname."_intro_19_footer"))?></textarea><br />
				  </dd>
				</dl>

				<div class="slideItemsTitle">
					회원로그인 설정
				</div>
				<dl>
				  <dt>회원로그인 사용여부</dt>
					<?php
					$use4   = get_option($theme_shortname."_intro_19_login_use")=='U'?'yes':'no';
					$show4 = get_option($theme_shortname."_intro_19_login_use")=='U'?'':'style="display:none"';
					?>
				  <dd>
					  <span class="useCheck" data-use="<?php echo $use4?>" data-container="<?php echo $theme_shortname?>_intro_19_login_use" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use4?>.png" /></span>
					  <input type="hidden" name="<?php echo $theme_shortname?>_intro_19_login_use" id="<?php echo $theme_shortname?>_intro_19_login_use" value='<?php echo get_option($theme_shortname."_intro_19_login_use")?>' style="cursor:pointer" />
				  </dd>
				</dl>
				<?php
				if(get_option($theme_shortname."_intro_19_subpage_blocking_check")!='checked'){ // 서브페이지 접근 차단 기능추가로 인한 초기값 설정
					update_option($theme_shortname."_intro_19_subpage_blocking_use",'U');
					update_option($theme_shortname."_intro_19_subpage_blocking_check",'checked');
				}
				?>
				<div class="slideItemsTitle">
					서브페이지 접근 차단
				</div>
				<dl>
				  <dt>사용여부</dt>
					<?php
					$use5  = get_option($theme_shortname."_intro_19_subpage_blocking_use")=='U'?'yes':'no';
					$show5 = get_option($theme_shortname."_intro_19_subpage_blocking_use")=='U'?'':'style="display:none"';
					?>
				  <dd>
					  <span class="useCheck" data-use="<?php echo $use5?>" data-container="<?php echo $theme_shortname?>_intro_19_subpage_blocking_use" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use5?>.png" /></span>
					  <input type="hidden" name="<?php echo $theme_shortname?>_intro_19_subpage_blocking_use" id="<?php echo $theme_shortname?>_intro_19_subpage_blocking_use" value='<?php echo get_option($theme_shortname."_intro_19_subpage_blocking_use")?>' style="cursor:pointer" />
					  <input type="hidden" name="<?php echo $theme_shortname?>_intro_19_subpage_blocking_check" id="<?php echo $theme_shortname?>_intro_19_subpage_blocking_check" value='<?php echo get_option($theme_shortname."_intro_19_subpage_blocking_check")?>' style="cursor:pointer" /><br />
						<span class="desc2"> 성인인증이 필요한 사이트의 경우 서브페이지 접근 차단의 사용을 권장합니다. <br />&nbsp;- 서브페이지 접근 차단을 사용하지 않음으로 인해 발생하는 문제는 본 사이트 소유주에게 귀책사유가 있습니다.</span>
				  </dd>
				</dl>
				<div class="slideItemsTitle">
					인트로 생략
				</div>
				<dl>
				  <dt>인트로 생략 Referer URL</dt>
				  <dd>
						<textarea type="textarea" name="<?php echo $theme_shortname?>_intro_19_jump_url" id="<?php echo $theme_shortname?>_intro_19_jump_url" style="width:70%;height:80px;" /><?php echo stripcslashes(get_option($theme_shortname."_intro_19_jump_url"))?></textarea><br />
						<span class="desc2"> 엔터(Enter)로 구분하여 입력해 주세요. (예 : http://www.bbsetheme.com)</span>
				  </dd>
				</dl>
			</span>
		</div>
      </li><!-- 사이트 운영 설정 -->
    </ul>
</div>


		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('intro');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('intro');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
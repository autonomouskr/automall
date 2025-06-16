<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_sns_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='sns' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_sns_preOpen' id='<?php echo $theme_shortname?>_sns_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">기능 설정 - SNS <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_sns_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">소셜 네트워크 공유 설정 (상품상세보기/포스트 글)</div>
		<div class="item">
            <ul class="descBox">
              <li class="desc">체크된 서비스의 공유 버튼이 본문 보기 페이지 하단에 노출됩니다.</li>
            </ul>
			<dl>
			  <dt>공유 할 SNS</dt>
			  <dd>
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_twitter" id="<?php echo $theme_shortname?>_sns_share_twitter" value='U' <?php echo (get_option($theme_shortname."_sns_share_twitter")=='U')?"checked":""?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sns_share_twitter">트위터 공유 사용</label>
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_facebook" id="<?php echo $theme_shortname?>_sns_share_facebook" value='U' <?php echo (get_option($theme_shortname."_sns_share_facebook")=='U')?"checked":""?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sns_share_facebook">페이스북 공유 사용</label>
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_naver" id="<?php echo $theme_shortname?>_sns_share_naver" value='U' <?php echo (get_option($theme_shortname."_sns_share_naver")=='U')?"checked":""?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sns_share_naver">네이버블로그 공유 사용</label>
				<?php
				/* 
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_hms" id="<?php echo $theme_shortname?>_sns_share_hms" value='U' <?php echo (get_option($theme_shortname."_sns_share_hms")=='U')?"checked":""?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sns_share_hms">HMS 공유 사용</label>
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_googleplus" id="<?php echo $theme_shortname?>_sns_share_googleplus" value='U' <?php echo (get_option($theme_shortname."_sns_share_googleplus")=='U')?"checked":""?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sns_share_googleplus">구글 플러스 공유 사용</label>
				*/
				?>
			  </dd>
			</dl>

		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">카카오톡/카카오스토리 공유 (상품상세보기/포스트 글)</div>
		<div class="item">
            <ul class="descBox">
              <li class="desc">체크된 서비스의 공유 버튼이 본문 보기 페이지 하단에 노출됩니다.</li>
              <li class="desc">카카오톡 공유 기능을 위해서는 <a class="infoBtn" href="https://developers.kakao.com/" title="개발자 등록" target="_blank">개발자등록</a>/앱생성 후 자바스크립트 앱키를 아래에 입력하세요</li>
              <li class="desc">카카오톡과 카카오스토리는 모바일 환경에서 사용자의 단말기가 지원하는(앱이 설치된) 경우에만 동작합니다.</li>
            </ul>
			<dl>
			  <dt>공유 할 SNS</dt>
			  <dd>
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_kTalk" id="_sns_share_kTalk" value='U' <?php echo (get_option($theme_shortname."_sns_share_kTalk")=='U')?"checked":""?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_sns_share_kTalk">카카오톡 공유 사용</label>
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_kStory" id="<?php echo $theme_shortname?>_sns_share_kStory" value='U' <?php echo (get_option($theme_shortname."_sns_share_kStory")=='U')?"checked":""?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_sns_share_kStory">카카오스토리 공유 사용</label>
				<p class="desc2">카카오톡, 카카오스토리 공유 : 모바일 화면에서만 공유버튼이 나타납니다.</p>
			  </dd>
			</dl>
            <dl class="appkey-js">
              <dt>카카오 앱키</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_sns_share_kakao_js_appkey" id="<?php echo $theme_shortname?>_sns_share_kakao_js_appkey" value="<?php echo get_option($theme_shortname."_sns_share_kakao_js_appkey")?>" style="width:50%;">
			  </dd>
            </dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
      <!--li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">핀터레스트 공유 설정 (상품상세보기/포스트 글)</div>
		<div class="item">
            <ul class="descBox">
              <li class="desc" style="height:60px;">
					<strong>핀터레스트 비즈니스 회원</strong><br />
						- 상품상세 : 고급핀(Rich Pins)의 상품 핀(Product Pins) 표시 (판매사이트명, 상품가격, 상품명, 상품사진, 판매자 URL 등 표시)<br />
						- 포스트(글) : 일반 핀으로 표시 (상품사진, 출처 URL 등 표시)<br />
			  </li>
			  <li class="desc" style="height:60px;">
					<strong>핀터레스트 일반 회원</strong><br />
						- 상품상세 : 일반 핀으로 표시 (상품사진, 출처 URL 등 표시)<br />
						- 포스트(글) : 일반 핀으로 표시 (상품사진, 출처 URL 등 표시)
			  </li>
			  <li class="desc" style="height:20px;">
					<strong>핀터레스트 분석 메타</strong><br />
						- 핀(Pins)의 노출 및 독자수, 리핀 등을 분석하기 위한 Meta Tag 입니다.
			  </li>
            </ul>
			<dl>
			  <dt>공유 할 SNS</dt>
			  <dd>
				<input type="checkbox" name="<?php echo $theme_shortname?>_sns_share_pinterest" id="_sns_share_pinterest" value='U' <?php echo (get_option($theme_shortname."_sns_share_pinterest")=='U')?"checked":""?> style='border:0px;' /><label for="<?php echo $theme_shortname?>_sns_share_pinterest">핀터레스트 공유 사용</label>
			  </dd>
			</dl>
		</div>
      </li-->
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">소셜 네트워크 아이콘 설정 (화면 하단)</div>
		<div class="item">
			<?php
			  $usetw    = get_option($theme_shortname."_sns_twitter_enable")=='U'?'yes':'no';
			  $buttontw = get_option($theme_shortname."_sns_twitter_enable")=='U'?'사용중':'비활성됨';
			  $showtw   = get_option($theme_shortname."_sns_twitter_enable")=='U'?'':'style="display:none"';
			?>
			<div class="slideItemsTitle">twitter&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $usetw?>" data-container="<?php echo $theme_shortname?>_sns_twitter_enable" data-target="snstw" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $usetw?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_sns_twitter_enable" id="<?php echo $theme_shortname?>_sns_twitter_enable" value='<?php echo get_option($theme_shortname."_sns_twitter_enable")?>'  />
			</div>

			<dl class="snstw" <?php echo $showtw?>>
			  <dt>링크</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_sns_twitter_url" id="<?php echo $theme_shortname?>_sns_twitter_url" value="<?php echo get_option($theme_shortname."_sns_twitter_url")?>" style="width:70%;">
			  </dd>
			</dl>
			<?php
			  $usefb    = get_option($theme_shortname."_sns_facebook_enable")=='U'?'yes':'no';
			  $buttonfb = get_option($theme_shortname."_sns_facebook_enable")=='U'?'사용중':'비활성됨';
			  $showfb   = get_option($theme_shortname."_sns_facebook_enable")=='U'?'':'style="display:none"';
			?>
			<div class="slideItemsTitle">facebook&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $usefb?>" data-container="<?php echo $theme_shortname?>_sns_facebook_enable" data-target="snsfb" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $usefb?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_sns_facebook_enable" id="<?php echo $theme_shortname?>_sns_facebook_enable" value='<?php echo get_option($theme_shortname."_sns_facebook_enable")?>'  />
			</div>

			<dl class="snsfb"<?php echo $showfb?>>
			  <dt>링크</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_sns_facebook_url" id="<?php echo $theme_shortname?>_sns_facebook_url" value="<?php echo get_option($theme_shortname."_sns_facebook_url")?>" style="width:70%;">
			  </dd>
			</dl>
			<?php
			  $usegp    = get_option($theme_shortname."_sns_google_enable")=='U'?'yes':'no';
			  $buttongp = get_option($theme_shortname."_sns_google_enable")=='U'?'사용중':'비활성됨';
			  $showgp   = get_option($theme_shortname."_sns_google_enable")=='U'?'':'style="display:none"';
			?>
			<div class="slideItemsTitle">google+&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $usegp?>" data-container="<?php echo $theme_shortname?>_sns_google_enable" data-target="snsgp" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $usegp?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_sns_google_enable" id="<?php echo $theme_shortname?>_sns_google_enable" value='<?php echo get_option($theme_shortname."_sns_google_enable")?>'  />
			</div>
			<dl class="snsgp"<?php echo $showgp?>>
			  <dt>링크</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_sns_google_url" id="<?php echo $theme_shortname?>_sns_google_url" value="<?php echo get_option($theme_shortname."_sns_google_url")?>" style="width:70%;">
			  </dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('sns');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('sns');">초기화</button>
		</div>
	</div>
	</form>
	<!-- //contents 끝 -->


<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_bing_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

$useWebmaster  = ( bbse_get_option("bing_use_webmaster") )  == 'U' ? 'on' : 'off';
$useAnalytics  = ( bbse_get_option("bing_use_analytics") )  == 'U' ? 'on' : 'off';
$useAuthorship = ( bbse_get_option("bing_use_authorship") )  == 'U' ? 'on' : 'off';
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='bing' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
    <input type='hidden' name='<?php echo $theme_shortname?>_bing_preOpen' id='<?php echo $theme_shortname?>_bing_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_bing_use_webmaster" id="<?php echo $theme_shortname?>_bing_use_webmaster" value='<?php echo get_option($theme_shortname."_bing_use_webmaster")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_bing_use_analytics" id="<?php echo $theme_shortname?>_bing_use_analytics" value='<?php echo get_option($theme_shortname."_bing_use_analytics")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_bing_use_authorship" id="<?php echo $theme_shortname?>_bing_use_authorship" value='<?php echo get_option($theme_shortname."_bing_use_authorship")?>' />


    <div class="tit">기능 설정 - 빙 연동 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_bing_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>
    <div class="accordionWrap"  data-disabled='false'>
      <ul class="displayAccordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $useWebmaster?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">빙 웹마스터</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_bing_use_webmaster"></div>
            </div>
          </div>
          <div class="item">
            <ol class="descBox">
              <li class="desc">빙 <a href="http://www.bing.com/toolbox/webmaster/" class="infoBtn" target="_blank" >웹마스터</a> 서비스에 지시에 따라 사이트를 등록합니다.</li>
              <li class="desc">사이트 소유자 확인시 다음과 같은 형식의 코드에서<br>&lt;meta name="msvalidate.01" content="<b>XXX....</b>" /&gt; content 항목의 내용을 아래에 입력하고 소유자로 인증받으십시오.</li>
              <li class="desc">아래 주소를 이용해 SITEMAP을 제출합니다.적용되는 시간은 짧게는 몇시간에 길게는 며칠이상 걸릴 수 있습니다.</li>
              <?php if ( get_option('permalink_structure') ) { ?>
              <li class="desc">추가주소의 경우 보완적인 주소로서 WP 설정항목 중 고유주소를 기본이외로 설정하고 .htaccess를 설정한 경우에만 해당되며 기본주소를 권장합니다.</li>
              <?php } ?>
              <li class="desc">빙 뿐 아니라 사이트맵을 필요로하는 대부분 서비스에는 공통적으로 사용가능합니다.</li>
            </ol>
            <dl>
              <dt>소유자 인증코드</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_bing_owntag' id='<?php echo $theme_shortname?>_bing_owntag'  value='<?php echo get_option($theme_shortname."_bing_owntag");?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>사이트맵 주소</dt>
              <dd>
                <a href="<?php echo esc_url( home_url().'/?bbsesitemap=base')?>" target="_blank"><b style="text-decoration:underline"><?php echo esc_url(home_url())?>/?bbasesitemap=base</b></a>
                <?php if ( get_option('permalink_structure') ) { ?>
                <br />추가 : <a href="<?php echo esc_url( home_url().'/sitemap.xml')?>" target="_blank"><b style="text-decoration:underline"><?php echo esc_url(home_url())?>/sitemap.xml</b></a><br>
                <?php } ?>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
      </ul>
    </div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('bing');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('bing');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_google_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

$useWebmaster  = ( bbse_get_option("google_use_webmaster") )  == 'U' ? 'on' : 'off';
$useAnalytics  = ( bbse_get_option("google_use_analytics") )  == 'U' ? 'on' : 'off';
$useAuthorship = ( bbse_get_option("google_use_authorship") )  == 'U' ? 'on' : 'off';
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='google' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
    <input type='hidden' name='<?php echo $theme_shortname?>_google_preOpen' id='<?php echo $theme_shortname?>_google_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_google_use_webmaster" id="<?php echo $theme_shortname?>_google_use_webmaster" value='<?php echo get_option($theme_shortname."_google_use_webmaster")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_google_use_analytics" id="<?php echo $theme_shortname?>_google_use_analytics" value='<?php echo get_option($theme_shortname."_google_use_analytics")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_google_use_authorship" id="<?php echo $theme_shortname?>_google_use_authorship" value='<?php echo get_option($theme_shortname."_google_use_authorship")?>' />


    <div class="tit">기능 설정 - 구글 연동 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_google_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>
    <div class="accordionWrap"  data-disabled='false'>
      <ul class="displayAccordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $useWebmaster?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">구글 웹마스터</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_google_use_webmaster"></div>
            </div>
          </div>
          <div class="item">
            <ol class="descBox">
              <li class="desc">구글 <a href="https://www.google.com/webmasters/" class="infoBtn" target="_blank" >웹마스터</a> 서비스에 사이트를 등록합니다.</li>
              <li class="desc">사이트 소유권 확인시 FTP등을 사용할 수 없는경우 대체방법-> HTML을 선택 후 <br>다음과 같은 형식의 코드에서&lt;meta name="google-site-verification" content="<b>XXX....</b>" /&gt; content 항목의 내용을 아래에 입력하고 저장한뒤 소유자로 인증받으십시오.<br>FTP나 기타 다른방법을 이용할 경우 이부분은 건너뛸 수 있습니다.</li>
              <li class="desc">등록된 사이트 관리에서 SITEMAP 추가/테스트에서 아래 주소를 추가 하고 제출합니다. 적용되는 시간은 짧게는 몇시간에 길게는 며칠이상 걸릴 수 있습니다.</li>
              <?php if ( get_option('permalink_structure') ) { ?>
              <li class="desc">추가주소의 경우 보완적인 주소로서 WP 설정항목 중 고유주소를 기본이외로 설정하고 .htaccess를 설정한 경우에만 해당되며 기본주소를 권장합니다.</li>
              <?php } ?>
              <li class="desc">구글 뿐 아니라 사이트맵을 필요로하는 대부분 서비스에는 공통적으로 사용가능합니다.</li>
            </ol>
            <dl>
              <dt>소유자 인증코드</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_google_owntag' id='<?php echo $theme_shortname?>_google_owntag'  value='<?php echo get_option($theme_shortname."_google_owntag");?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>사이트맵 주소</dt>
              <dd>
                <a href="<?php echo esc_url( home_url().'/?bbsesitemap=base')?>" target="_blank"><?php echo esc_url(home_url())?>/<b style="text-decoration:underline">?bbsesitemap=base</b></a>
                <?php if ( get_option('permalink_structure') ) { ?>
                <br />추가 : <a href="<?php echo esc_url( home_url().'/sitemap.xml')?>" target="_blank"><?php echo esc_url(home_url())?>/<b style="text-decoration:underline">sitemap.xml</b></a><br>
                <?php } ?>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $useAnalytics?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">구글 애널리틱스</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_google_use_analytics"></div>
            </div>
          </div>
          <div class="item">
            <ol class="descBox">
            <li class="desc">이 기능을 사용하기 위해서는 <a href="https://www.google.com/analytics" class="infoBtn" target="_blank">Analytics</a>서비스를 사용하고 있어야 하며 설정에 따라서는
            <a href="https://www.google.com/webmasters" class="infoBtn" target="_blank">webmaster</a>서비스 또는 다른 서비스를 사용하고 있어야 할 수 있습니다.</li>
              <li class="desc"><a href="https://www.google.com/analytics" class="infoBtn" target="_blank">Analytics</a>서비스의 속성-속성 설정의 추적ID 값을 입력하십시오.</li>
              <li class="desc">추가 옵션은 <a href="https://www.google.com/analytics" class="infoBtn" target="_blank">Analytics</a>서비스의 설정에 따라 활성화 또는 비활성화 하십시오.(Analytics 서비스에서 활성화 해야 정상적으로 동작합니다.)</li>
            </ol>
            <dl>
              <dt>추적-ID</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_google_trackingid' id='<?php echo $theme_shortname?>_google_trackingid'  value='<?php echo get_option($theme_shortname."_google_trackingid");?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>추가 옵션</dt>
              <dd>
                <input type="checkbox" name="<?php echo $theme_shortname?>_google_option1" value="on" id="<?php echo $theme_shortname?>_google_option1" <?php echo get_option($theme_shortname."_google_option1")=='on' ? 'checked="checked"':'' ?> />
                <label for="<?php echo $theme_shortname?>_google_option1">인구통계 및 관심분야 보고서 사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="<?php echo $theme_shortname?>_google_option2" value="on" id="<?php echo $theme_shortname?>_google_option2" <?php echo get_option($theme_shortname."_google_option2")=='on' ? 'checked="checked"':'' ?> />
                <label for="<?php echo $theme_shortname?>_google_option2">향상된 링크 기여 사용</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="checkbox" name="<?php echo $theme_shortname?>_google_option3" value="on" id="<?php echo $theme_shortname?>_google_option3" <?php echo get_option($theme_shortname."_google_option3")=='on' ? 'checked="checked"':'' ?> />
				<label for="<?php echo $theme_shortname?>_google_option3">전자상거래 사용</label>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $useAuthorship?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">구글+ 저작자 인증</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_google_use_authorship"></div>
            </div>
          </div>
          <div class="item">
            <ol class="descBox">
              <li class="desc">이 기능을 사용하기 위해서는 <a href="http://plus.google.com/" class="infoBtn" target="_blank">구글+</a>서비스를 사용하고 있어야 합니다.</li>
              <li class="desc">대상 <a href="http://plus.google.com/me" class="infoBtn" target="_blank">구글+</a>에 방문합니다.</li>
              <li class="desc">브라우저 주소창의 주소중 21자리 숫자까지만 아래 Google+ ID안에 입력하고 저장합니다.<br />
                예를 들어 <b>https://plus.google.com/xxxxxxxxxxxxxxxxxxxxx</b>/post와 같을때 굵은 글시체 부분만 입력하십시오.
              </li>
              <?php
              query_posts("post_status=publish&paged=1&post_type=post&showposts=1&orderby=date&order=DESC");
              if (have_posts())
                while(have_posts())
                {
                  the_post();
                  $myWp = urlencode( esc_url( get_permalink() ));
                }
              ?>
              <li class="desc"><a href="http://www.google.com/webmasters/tools/richsnippets?url=<?php echo $myWp?>&amp;Submit=Test+the+latest+post+or+page."  class="infoBtn" target="_blank">TEST</a>를 눌러 정상적으로 테스트가 되는지 하고 테스팅하십시오.</li>
            </ol>
            <dl>
              <dt>구글+ ID</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_google_plusurl' id='<?php echo $theme_shortname?>_google_plusurl'  value='<?php echo esc_url(get_option($theme_shortname."_google_plusurl"));?>' class="input-long" />
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
      </ul>
    </div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('google');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('google');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
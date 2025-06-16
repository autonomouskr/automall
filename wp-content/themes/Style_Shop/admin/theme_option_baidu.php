<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_baidu_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

$useWebmaster  = ( bbse_get_option("baidu_use_webmaster") )  == 'U' ? 'on' : 'off';
$useAnalytics  = ( bbse_get_option("baidu_use_analytics") )  == 'U' ? 'on' : 'off';
$useAuthorship = ( bbse_get_option("baidu_use_authorship") ) == 'U' ? 'on' : 'off';
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='baidu' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
    <input type='hidden' name='<?php echo $theme_shortname?>_baidu_preOpen' id='<?php echo $theme_shortname?>_baidu_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_baidu_use_webmaster" id="<?php echo $theme_shortname?>_baidu_use_webmaster" value='<?php echo get_option($theme_shortname."_baidu_use_webmaster")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_baidu_use_analytics" id="<?php echo $theme_shortname?>_baidu_use_analytics" value='<?php echo get_option($theme_shortname."_baidu_use_analytics")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_baidu_use_authorship" id="<?php echo $theme_shortname?>_baidu_use_authorship" value='<?php echo get_option($theme_shortname."_baidu_use_authorship")?>' />

    <div class="tit">기능 설정 - 바이두 연동 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_baidu_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>
    <div class="accordionWrap"  data-disabled='false'>
      <ul class="displayAccordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $useWebmaster?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">바이두 웹마스터</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_baidu_use_webmaster"></div>
            </div>
          </div>
          <div class="item">
            <ol class="descBox">
              <li class="desc"><a href="http://zhanzhang.baidu.com/site/siteadd" class="infoBtn" target="_blank" >바이두</a> 서비스에 사이트를 등록합니다.</li>
              <li class="desc">사이트 소유권 확인시 FTP등을 사용할 수 없는경우 대체방법-> HTML을 선택 후 <br>다음과 같은 형식의 코드에서&lt;meta name="baidu-site-verification" content="<b>XXX....</b>" /&gt; content 항목의 내용을 아래에 입력하고 저장한뒤 소유자로 인증받으십시오.<br>FTP나 기타 다른방법을 이용할 경우 이부분은 건너뛸 수 있습니다.</li>
              <li class="desc">바이두에 사이트맵을 제출 하고 사용하기 위해서는 WP 설정항목 중 고유주소를 기본 이외로 설정하고 .htaccess를 설정하여야만 합니다. <li>
              <li class="desc">SITEMAP이 제출되면 적용되는 시간은 짧게는 몇시간에 길게는 며칠이상 걸릴 수 있습니다.</li>
            </ol>
            <dl>
              <dt>소유자 인증코드</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_baidu_owntag' id='<?php echo $theme_shortname?>_baidu_owntag'  value='<?php echo get_option($theme_shortname."_baidu_owntag");?>' class="input-long" />
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
      </ul>
    </div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('baidu');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('baidu');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
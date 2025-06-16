<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_sitemap_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='sitemap' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />

    <input type='hidden' name='<?php echo $theme_shortname?>_sitemap_preOpen' id='<?php echo $theme_shortname?>_sitemap_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />

    <div class="tit">기능 설정 - 사이트맵 관리 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_sitemap_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>
    <div>
      <ul class="">
<!-- ******************************************************************************************************************************************************************/1 -->
        <li class="groupC">
          <div class="itemHeader <?php echo $preOpen2?>">사이트맵 관리</div>
          <div class="item">
            <ul class="descBox">
              <li class="desc">동적사이트맵을 사용하기 불가능한 경우 정적 사이트맵 사용하세요.</li>
              <!-- <li class="desc">정적 사이트맵을 사용하면 포스트 및 페이지의 생성/수정시 파일이 자동 업데이트 됩니다.</li> -->
              <?php if( get_option('permalink_structure') ){?>
              <li class="desc">동적 사이트맵의 보조 주소는 고유주소를 사용하며, .htaccess를 설정한 경우에만 사용됩니다.</li>
              <li class="desc">정적 사이트맵을 사용하는 경우 동적 사이트맵 보조 주소는 자동으로 대체 되며, 포스트/페이지의 작성/수정시 자동 업데이트 됩니다.</li>
              <li class="desc">정적 사이트맵을 사용하다 중단한 경우 업데이트 되지 않은 채 파일이 계속 남아 있게 되므로 원치 않을 경우 수동 삭제하여야합니다.</li>
              <?php }?>
              <li class="desc" style="color:#ED1C24;">정적 사이트맵은 구글, 빙, 바이두 중 하나 이상의 웹마스터를 사용중인 경우에 생성/수정 됩니다.</li>
              <li class="desc">정적 사이트맵의 작성을 시도하는 경우 서버의 환경에 따라 정상적으로 동작 하지 않을 수 있습니다.</li>
            </ul>
            <dl>
              <dt>정적 사이트맵작성</dt>
              <dd><input type="checkbox" name="<?php echo $theme_shortname?>_sitemap_usefile" value="yes" <?php echo get_option($theme_shortname.'_sitemap_usefile')=='yes'?'checked':''?>> <label>정적 사이트맵 파일의 생성을 시도합니다.</label>
              </dd>
            </dl>

            <dl>
              <dt>동적 사이트맵주소</dt>
              <dd><a href="<?php echo esc_url( home_url().'/?bbsesitemap=base')?>" target="_blank"><?php echo esc_url(home_url())?>/<b style="text-decoration:underline">?bbasesitemap=base</b></a> (기본 주소)<br>
              <?php if( get_option('permalink_structure') ){?>
              <a href="<?php echo esc_url( home_url().'/sitemap.xml')?>" target="_blank"><?php echo esc_url(home_url())?>/<b style="text-decoration:underline">sitemap.xml</b></a> (보조 주소)
              <?php }?>
              </dd>
            </dl>
            <dl>
              <dt>정적 사이트맵주소</dt>
              <dd><a href="<?php echo esc_url( home_url().'/sitemap.xml')?>" target="_blank"><?php echo esc_url(home_url())?>/<b style="text-decoration:underline">sitemap.xml</b></a></dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
      </ul>
    </div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('sitemap');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('sitemap');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_seo_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='seo' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_seo_preOpen' id='<?php echo $theme_shortname?>_seo_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">기능 설정 - SEO <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_seo_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
       <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">SEO 메타</div>
		<div class="item">
			<dl>
			  <dt>타이틀</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_seo_title" id="<?php echo $theme_shortname?>_seo_title" value="<?php echo get_option($theme_shortname."_seo_title")?>">
			  </dd>
			</dl>
			<dl>
			  <dt>설명</dt>
			  <dd>
				<textarea name="<?php echo $theme_shortname?>_seo_description" id="<?php echo $theme_shortname?>_seo_description" rows="5" cols=""><?php echo get_option($theme_shortname."_seo_description")?></textarea>
			  </dd>
			</dl>
			<dl>
			  <dt>키워드</dt>
			  <dd>
				<textarea name="<?php echo $theme_shortname?>_seo_keywords" id="<?php echo $theme_shortname?>_seo_keywords" rows="5" cols=""><?php echo get_option($theme_shortname."_seo_keywords")?></textarea>
			  </dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
       <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">구조화 데이터</div>
		<div class="item">
            <ol class="descBox">
            <li class="desc">검색엔진에서 검색 시 사이트를 보다 효율적으로 노출하기 위해 사이트를 구조화하여 데이터를 표시합니다.</li>
            <li class="desc">구조화 데이터는 상품, 글(포스트), 페이지, 리스트 페이지 등에 적용되어 검색 시 반영 됩니다.</li>
            <li class="desc">구글 구조화 테스트 : <a href="https://search.google.com/structured-data/testing-tool" class="infoBtn" target="_blank">https://search.google.com/structured-data/testing-tool</a></li>
            </ol>
			<dl>
			  <dt>구조화 데이터 방식</dt>
			  <dd>
				<select name="<?php echo $theme_shortname?>_structured_type" id="<?php echo $theme_shortname?>_structured_type">
					<option value="Json-ld" <?php echo (!get_option($theme_shortname."_structured_type") || get_option($theme_shortname."_structured_type")=='Json-ld')?"selected='selected'":"";?>>Json-LD</option>
					<!--option value="RDFa" <?php echo (get_option($theme_shortname."_structured_type")=='RDFa')?"selected='selected'":"";?>>RDFa</option>
					<option value="MetaTag" <?php echo (get_option($theme_shortname."_structured_type")=='MetaTag')?"selected='selected'":"";?>>Meta Tag</option-->
				</select>
			  </dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('seo');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('seo');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->


<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_ssl_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='ssl' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_ssl_preOpen' id='<?php echo $theme_shortname?>_ssl_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">기능 설정 - SSL(보안서버) <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_ssl_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use    = get_option($theme_shortname."_ssl_enable")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_ssl_enable")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_ssl_enable")=='U'?'':'style="display:none"';
      ?>
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">SSL(보안서버)</div>
		<div class="item">
			<div class="slideItemsTitle">
				사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_ssl_enable" data-target="ssl_enable" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_ssl_enable" id="<?php echo $theme_shortname?>_ssl_enable" value='<?php echo get_option($theme_shortname."_ssl_enable")?>'  />
			</div>
			<div class="ssl_enable" <?php echo $show?>>
			  <span class="desc2">대표 도메인과 SSL사용 <span style="text-decoration:underline">도메인이 서로 다를 경우, 충돌로 인한 오류가 발생</span>하게 됩니다.</span><br />
			  <span class="desc2">SSL사용 도메인을 변경하고자 하시는 경우, 대표도메인을 변경하시면 됩니다.</span><br />
			  <span class="desc2">SSL사용 도메인의 기본 포트 번호는 <span style="text-decoration:underline">443</span> 입니다.</span><br />
			  <span class="desc2">'서버접속주소/~계정아이디' 의 형식은 SSL을 적용할 수 없습니다.</span>
			  <dl>
				<dt>도메인</dt>
				<dd>
				  <input type="text" id="<?php echo $theme_shortname?>_ssl_domain" name="<?php echo $theme_shortname?>_ssl_domain" value="<?php echo get_option($theme_shortname."_ssl_domain")?>" style="width:40%">&nbsp;예)www.abc.co.kr
				</dd>
			  </dl>
			  <dl>
				<dt>포트번호</dt>
				<dd>
				  <input type="text" id="<?php echo $theme_shortname?>_ssl_port" name="<?php echo $theme_shortname?>_ssl_port" value="<?php echo get_option($theme_shortname."_ssl_port")?>" style="width:50px;">
				</dd>
			  </dl>
			</div>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('ssl');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('ssl');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->


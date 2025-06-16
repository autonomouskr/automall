<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_map_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>

	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='map' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_map_preOpen' id='<?php echo $theme_shortname?>_map_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">기능 설정 - 지도API (오시는길) <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_map_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

    
<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">지도 노출시 주소 및 연락처 표시 설정</div>
		<div class="item">
			<ul class="descBox">
				<li class="desc">네이버/다음 지도가 노출되는 페이지 및 글의 지도 하단에 주소와 연락처를 함께 표시할지의 여부를 설정합니다. </li>
			</ul>
			<dl>
			  <dt>주소/연락처 표시 여부</dt>
			  <dd><input type="radio" name="<?php echo $theme_shortname?>_map_addr_info_view" id="<?php echo $theme_shortname?>_map_addr_info_view_U" value="U" <?php echo (!get_option($theme_shortname."_map_addr_info_view") || get_option($theme_shortname."_map_addr_info_view")=='U')?"checked='checked'":"";?> />표시함&nbsp;&nbsp;&nbsp;<input type="radio" name="<?php echo $theme_shortname?>_map_addr_info_view" id="<?php echo $theme_shortname?>_map_addr_info_view_N" value="N" <?php echo (get_option($theme_shortname."_map_addr_info_view")=='N')?"checked='checked'":"";?> />표시안함
			  </dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
      <!--li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">네이버지도 설정</div>
		<div class="item">
			<span style="padding-left:10px;font-weight:bold;color:#f55555">지도API와 연동 할 수 있습니다. '오시는길' 템플릿을 선택한 페이지나 지도영역에 적용됩니다. </span>
			<ul class="descBox">
				<li class="desc">네이버 지도를 사용하기 위해선 앱등록이 필요합니다.</li>
				<li class="desc"><a href="https://developers.naver.com/register" class="infoBtn" target="_blank">애플리케이션 등록</a>에서 <b>비로그인 오픈API</b>, <b>웹서비스 URL</b>을 설정하여 <b>ID와 Secret을 발급</b> 받으세요.</li>
				<li class="desc">발급 후 반드시 <b>'API 권한관리' 탭</b>에서 <b>'비로그인 오픈 API' - '지도 API'를 체크</b> 해주세요.<br><br></li>
				<li class="desc">지도 페이지를 생성하신 후 아래 Shortcode를 복사/붙여넣기로 테스트 해보세요 &nbsp;<a href="<?php admin_url()?>post-new.php?post_type=page" class="infoBtn">페이지 만들기</a></li>
				<li style="height:25px;">예1 : <span class="shortCode">[naver_maps]</span> 기본설정된 기본값을 사용합니다.</li>
				<li>예2 : <span class="shortCode">[naver_maps level="11" width="100%" height="40vh", showerror="n"]</span> 기본값을 설정한 예 입니다.</li>
				<li class="desc">level : 기본 확대 배율입니다. (1~14)</li>
				<li class="desc">width : 가로 크기입니다. (반드시 단위포함, px, %등)</li>
				<li class="desc">height : 세로 크기입니다. (반드시 단위포함, px, %등)</li>
				<li class="desc">showerror : 에러 보이기, 문제발생시 해결에 도움이 됩니다. (y 또는 n)</li>
			</ul>
			<dl>
			  <dt>주소<em>(오시는길)</em></dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_map_addr" id="<?php echo $theme_shortname?>_map_addr" value="<?php echo get_option($theme_shortname."_map_addr")?>" style="width:70%;">
				<p class="desc">주소 검색 후 <a href="http://map.naver.com/" target="_blank"  class="infoBtn">네이버 맵</a> 페이지에서 확인 하실 수 있습니다.</p>
			  </dd>
			</dl>
			<dl>
				<dt>Client ID</dt>
				<dd>
					<input type="text" name="<?php echo $theme_shortname?>_map_client_id" id="<?php echo $theme_shortname?>_map_client_id" value="<?php echo get_option($theme_shortname."_map_client_id")?>"  class="input-long" />
				</dd>
			</dl>
			<dl>
				<dt>Client Secret</dt>
				<dd>
					<input type="text" name="<?php echo $theme_shortname?>_map_client_secret" id="<?php echo $theme_shortname?>_map_client_secret" value="<?php echo get_option($theme_shortname."_map_client_secret")?>"  class="input-long" />
				</dd>
			</dl>
			<dl>
				<dt>이름 (마커 표시용)</dt>
				<dd>
					<input type="text" name="<?php echo $theme_shortname?>_map_mkr_name" id="<?php echo $theme_shortname?>_map_mkr_name" value="<?php echo get_option($theme_shortname."_map_mkr_name")?>"  class="input-long" />
					<p class="desc">마커에 표시될 상호 또는 사이트 이름이며 입력하지 않으면 사이트 이름이 표시됩니다.</p>
				</dd>
			</dl>
			<dl>
			  <dt>네이버 지도 API 키 (구버전)</dt>
			  <dd>
				<input type="text" name="<?php echo $theme_shortname?>_map_key" id="<?php echo $theme_shortname?>_map_key" style="width:50%;" value="<?php echo get_option($theme_shortname."_map_key")?>" >
                <p class="desc">구버전은 2016년 2월부터 발급하지 않으며 2016년 까지 사용 가능하므로 2017년 이전에  Client ID, Client Secret을 사용하는 방식으로 전환하셔야합니다.<br>
				<b>구버전 API는 지번 주소만 이용가능합니다.</b></p>
			  </dd>
			</dl>
			<dl>
			  <dt>전화번호/팩스/이메일</dt>
			  <dd>
				 <input type="text" name="<?php echo $theme_shortname?>_map_infomation" id="<?php echo $theme_shortname?>_map_infomation" value="<?php echo get_option($theme_shortname."_map_infomation")?>" >
				 <p class="desc">지도하단에 표시되는 정보입니다. 입력하지 않으면 표시되지 않습니다.</p>
			  </dd>
			</dl>
		</div>
      </li-->
  <!-- ******************************************************************************************************************************************************* -->
  		<li class="group use-<?php echo $use?>">
			<div class="itemHeader <?php echo $preOpen2?>">카카오 지도 설정</div>
			<div class="item">
				<ul class="descBox">
					<li class="desc">카카오 지도를 사용하기 위해선 키를 발급받아야합니다.</li>
					<li class="desc"><a href="https://developers.kakao.com/" class="infoBtn" target="_blank">카카오 개발자사이트</a>에 접속</li>
					<li class="desc">개발자 등록 및 앱 생성</li>
					<li class="desc">웹 플랫폼 추가: 앱 선택 – [설정] – [일반] – [플랫폼 추가] – 웹 선택 후 추가</li>
					<li class="desc">사이트 도메인 등록: [웹] 플랫폼을 선택하고, [사이트 도메인] 을 등록합니다. (예: http://localhost:8080)</li>
					<li class="desc">페이지 상단의 [JavaScript 키]를 지도 API의 appkey로 사용합니다.<br><br></li>

					<li class="desc">지도 페이지를 생성하신 후 아래 Shortcode를 복사/붙여넣기로 테스트 해보세요 &nbsp;<a href="<?php admin_url()?>post-new.php?post_type=page" class="infoBtn">페이지 만들기</a></li>
					<li>예1 : <span class="shortCode">[daum_maps]</span> 기본설정된 기본값을 사용합니다.</li>
					<li>예2 : <span class="shortCode">[daum_maps level="3" width="100%" height="40vh" showerror="n"]</span> 기본값을 설정한 예 입니다.</li>
					<li class="desc">level : 기본 확대 배율입니다. (1~14)</li>
					<li class="desc">width : 가로 크기입니다. (반드시 단위포함, px, %등)</li>
					<li class="desc">height : 세로 크기입니다. (반드시 단위포함, px, %등)</li>
					<li class="desc">showerror : 에러 보이기, 문제발생시 해결에 도움이 됩니다. (y 또는 n)</li>
				</ul>

				<dl>
					<dt>카카오지도 APP키</dt>
					<dd>
						<input type="text" name="<?php echo $theme_shortname?>_map_daum_appkey" id="<?php echo $theme_shortname?>_map_daum_appkey" value="<?php echo get_option($theme_shortname."_map_daum_appkey")?>"  class="input-long" />
					</dd>
				</dl>
				<dl>
					<dt>주소 (지도 표시용)</dt>
					<dd>
						<input type="text" name="<?php echo $theme_shortname?>_map_daum_addr" id="<?php echo $theme_shortname?>_map_daum_addr" value="<?php echo get_option($theme_shortname."_map_daum_addr")?get_option($theme_shortname."_map_daum_addr"):(get_option($theme_shortname."_map_addr")?get_option($theme_shortname."_map_addr"):'')?>"  class="input-long" />
						<p class="desc">주소 검색 후 <a href="http://map.daum.net/" target="_blank"  class="infoBtn">다음지도</a> 페이지에서 확인 하실 수 있습니다.</p>
					</dd>
				</dl>

				<dl>
					<dt>이름 (마커 표시용)</dt>
					<dd>
						<input type="text" name="<?php echo $theme_shortname?>_map_daum_mkr_name" id="<?php echo $theme_shortname?>_map_daum_mkr_name" value="<?php echo get_option($theme_shortname."_map_daum_mkr_name")?>"  class="input-long" />
						<p class="desc">마커에 표시될 상호 또는 사이트 이름이며 입력하지 않으면 표시되지 않습니다.</p>
					</dd>
				</dl>
				<dl>
					<dt>전화번호/팩스/이메일</dt>
					<dd>
						<input type="text" name="<?php echo $theme_shortname?>_map_daum_infomation" id="<?php echo $theme_shortname?>_map_daum_infomation" value="<?php echo get_option($theme_shortname."_map_daum_infomation")?get_option($theme_shortname."_map_daum_infomation"):(get_option($theme_shortname."_map_infomation")?get_option($theme_shortname."_map_infomation"):'') ?>" class="input-long" />
						<p class="desc">지도하단에 표시되는 정보입니다. 입력하지 않으면 표시되지 않습니다.</p>
					</dd>
				</dl>
				<br><br>
				<dl>
					<dt>다음지도 API키(구형)</dt>
					<dd>
						<input type="text" name="<?php echo $theme_shortname?>_map_daum_key" id="<?php echo $theme_shortname?>_map_daum_key" value="<?php echo get_option($theme_shortname."_map_daum_key")?>"  class="input-long" />
						<span style="color:#f55555;">신규 API인 카카오 APP키를 발급받아 사용하시길 권장합니다.(폐기예정) 모두 등록된 경우 카카오 APP키를 사용합니다.</span>
					</dd>
				</dl>
			</div>
		</li>
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>
		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('map');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('map');">초기화</button>
		</div>
	</div>
	</form>
	<!-- //contents 끝 -->


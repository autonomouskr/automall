<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_naver_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

$webmaster = ( bbse_get_option("naver_use_webmaster") )  == 'U' ? 'on' : 'off';
$blog      = ( bbse_get_option("naver_blog_api_use") )  == 'U' ? 'on' : 'off';
$analytics = ( bbse_get_option("naver_analytics_use") )  == 'U' ? 'on' : 'off';
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='naver' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
    <input type='hidden' name='<?php echo $theme_shortname?>_naver_preOpen' id='<?php echo $theme_shortname?>_naver_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_naver_use_webmaster" id="<?php echo $theme_shortname?>_naver_use_webmaster" value='<?php echo get_option($theme_shortname."_naver_use_webmaster")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_naver_blog_api_use" id="<?php echo $theme_shortname?>_naver_blog_api_use" value='<?php echo get_option($theme_shortname."_naver_blog_api_use")?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_naver_analytics_use" id="<?php echo $theme_shortname?>_naver_analytics_use" value='<?php echo get_option($theme_shortname."_naver_analytics_use")?>' />


    <div class="tit">기능 설정 - 블로그 연동 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_naver_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>
    <div class="accordionWrap"  data-disabled='false'>
      <ul class="displayAccordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $webmaster?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">네이버 웹마스터/신디케이션</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_naver_use_webmaster"></div>
            </div>
          </div>
          <div class="item">
            <ol class="descBox">
              <li class="desc">네이버 <a href="http://webmastertool.naver.com/" class="infoBtn" target="_blank" >웹마스터</a> 서비스에 사이트를 등록합니다.</li>
              <li class="desc">사이트 소유권 확인시 다음과 같은 형식의 코드에서&lt;meta name="naver-site-verification" content="<b>XXX....</b>" /&gt;에서 <br>content 항목의 내용을 아래에 입력하고 저장한뒤 소유자로 인증받으십시오.<br>FTP나 기타 다른방법을 이용할 경우 이부분은 건너뛸 수 있습니다.</li>
              <li class="desc">서비스에 사이트 등록 후 발급되는 신디케이션 토큰을 입력하세요.</li>
            </ol>
            <dl>
              <dt>소유자 인증코드</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_naver_owntag' id='<?php echo $theme_shortname?>_naver_owntag'  value='<?php echo get_option($theme_shortname."_naver_owntag");?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>신디케이션 토큰</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_naver_syndiToken' id='<?php echo $theme_shortname?>_naver_syndiToken'  value='<?php echo get_option($theme_shortname."_naver_syndiToken");?>' class="input-long" />
              </dd>
            </dl>

          </div>
        </li><!-- /2단 -->
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $blog?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">네이버 블로그 연동</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_naver_blog_api_use"></div>
            </div>
          </div>
          <div class="item">
            <dl>
              <dt>API 연결 URL</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_naver_blog_api_url' id='<?php echo $theme_shortname?>_naver_blog_api_url'  value='<?php echo (get_option($theme_shortname."_naver_blog_api_url"))?get_option($theme_shortname."_naver_blog_api_url"):"https://api.blog.naver.com/xmlrpc";?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>API 연결 아이디</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_naver_blog_api_id' id='<?php echo $theme_shortname?>_naver_blog_api_id'  value='<?php echo get_option($theme_shortname."_naver_blog_api_id");?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>API 연결 암호</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_naver_blog_api_pw' id='<?php echo $theme_shortname?>_naver_blog_api_pw'  value='<?php echo get_option($theme_shortname."_naver_blog_api_pw");?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>출처 표시 여부</dt>
              <dd>
              <?php
                $useNaverOrigin    = get_option($theme_shortname."_naver_blog_api_origin")=='U'?'yes':'no';
              ?>
                <span class="useCheck" data-use="<?php echo $useNaverOrigin?>" data-container="<?php echo $theme_shortname?>_naver_blog_api_origin" style="cursor:pointer"><img src="<?php echo esc_url( get_template_directory_uri() )?>/admin/images/switch_<?php echo $useNaverOrigin?>.png" style="margin-bottom:-10px;" /></span>
                <input type="hidden" name="<?php echo $theme_shortname?>_naver_blog_api_origin" id="<?php echo $theme_shortname?>_naver_blog_api_origin" value='<?php echo get_option($theme_shortname."_naver_blog_api_origin")?>'  />
              </dd>
            </dl>
            <dl>
              <dt>카테고리</dt>
              <dd>
              <?php
                $naverCategory=get_option($theme_shortname."_naver_blog_api_category");
                $naverCategoryUpdateDate=get_option($theme_shortname."_naver_blog_api_category_update_date");
              ?>
                <!--img src="<?php echo esc_url( get_template_directory_uri() )?>/admin/images/get_category.png" class="getcategory" style="cursor:pointer;" />
                <div>* 최근 카테고리 가져오기 실행 :   <span id="<?php echo $theme_shortname?>_naver_blog_api_category_display_update_date" style="color:#ff5112;"><?php echo ($naverCategoryUpdateDate)?date("Y-m-d H:i:s",$naverCategoryUpdateDate):"등록 된 카테고리가 존재하지 않습니다.";?></span></div>
                <div id="<?php echo $theme_shortname?>_naver_blog_category_display_list" style="display:<?php echo ($naverCategory)?"block":"none";?>"><?php echo "* 카테고리 => ".$naverCategory;?></div-->
                <input type="text" name="<?php echo $theme_shortname?>_naver_blog_api_category" id="<?php echo $theme_shortname?>_naver_blog_api_category" value='<?php echo $naverCategory;?>' style="width:100%;" />
				<div>* 네이버 블로그내의 카테고리명을 띄어쓰기에 주의하셔서 입력해 주세요.(콤마로 구분)</div>

                <input type="hidden" name="<?php echo $theme_shortname?>_naver_blog_api_category_update_date" id="<?php echo $theme_shortname?>_naver_blog_api_category_update_date" value='<?php echo get_option($theme_shortname."_naver_blog_api_category_update_date")?>'  />
              </dd>
            </dl>
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<span style="font-weight:700;">[네이버 블로그 연동 정책 변경 안내]</span></br>
				&nbsp;1. 이전 (~2018년 4월 24일)</br>
				&nbsp;&nbsp;&nbsp;- 글쓰기api환경에서 새글 발행 가능, 내블로그에서 글 불러오기 가능, 내블로그에서 불러온 글 수정/저장하기(발행하기) 가능</br>
				&nbsp;<span style="color:red;">2. 변경 (2018년 4월 25일 이후)</br>
				&nbsp;&nbsp;&nbsp;- 글쓰기api 환경에서 새글 발행 가능, 내블로그의 카테고리 설정 불가, 내블로그에서 글 불러오기 실행 불가, 내블로그에서 불러온 글 수정/저장하기 불가</span></br>
				&nbsp;3. 네이버 공지사항 : <a href="https://section.blog.naver.com/Notice.nhn?docId=10000000000030660878" target="_blank">https://section.blog.naver.com/Notice.nhn?docId=10000000000030660878</a></br>
			</div>

          </div>
        </li><!-- /2단 -->
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $analytics?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">네이버 애널리틱스</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_naver_analytics_use"></div>
            </div>
          </div>
          <div class="item">
            <dl>
              <dt>발급 ID</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_naver_analytics_id' id='<?php echo $theme_shortname?>_naver_analytics_id'  value='<?php echo get_option($theme_shortname."_naver_analytics_id")?>' class="input-long" />
              </dd>
            </dl>

          </div>
        </li><!-- /2단 -->
<!-- ******************************************************************************************************************************************************* -->
      </ul>
    </div>
		<div class="btn">
      <button type="submit" class="b _c1" onClick="save_submit('naver');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('naver');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->
<script>

jQuery(document).ready(function(){

  jQuery('.getcategory').click(function(){
    var $apiUse  = jQuery('#<?php echo $theme_shortname?>_naver_blog_api_use').val();
    var $tUrl    = '<?php echo esc_url( get_template_directory_uri() )?>/admin/theme_option_blog.exec.php';
    var $apiType = 'category';
    var $apiUrl  = jQuery('#<?php echo $theme_shortname?>_naver_blog_api_url').val();
    var $apiBlog = 'naver';
    var $apiId   = jQuery('#<?php echo $theme_shortname?>_naver_blog_api_id').val();
    var $apiPw   = jQuery('#<?php echo $theme_shortname?>_naver_blog_api_pw').val();
    if (!$apiUse)
    {
      alert('먼저 블로그 연동을 활성화 하세요.');
      return false;
    }

    if (!$apiId)
    {
      alert('API 연결 아이디를 입력해주세요.');
      jQuery('#<?php echo $theme_shortname?>_naver_blog_api_id').focus();
      return false;
    }

    if (!$apiPw)
    {
      alert('API 연결 암호를 입력해주세요.');
      jQuery('#<?php echo $theme_shortname?>_naver_blog_api_pw').focus();
      return false;
    }

    jQuery.ajax({
      type  : "post",
      async : false,
      url   : $tUrl,
      data  : {
          apiType : $apiType,
          apiUrl  : $apiUrl,
          apiBlog : $apiBlog,
          apiId   : $apiId,
          apiPw   : $apiPw
      },
      success: function(data){
        var result = data.split("|||");
        if(result[0]=='success'){
          jQuery('#<?php echo $theme_shortname?>_naver_blog_api_category').val(result[1]);
          jQuery('#<?php echo $theme_shortname?>_naver_blog_category_display_list').css("display","block").html("* 카테고리 => "+result[1]);
          jQuery('#<?php echo $theme_shortname?>_naver_blog_api_category_update_date').val(result[2]);
          jQuery('#<?php echo $theme_shortname?>_naver_blog_api_category_display_update_date').html(result[3]);
          alert("카테고리 목록을 가져오는데 성공했습니다. 저장하십시오");
        }
        else{
          alert("[통신실패 !] API 연결 정보를 확인해 주세요.    ");
          event.preventDefault();
        }
      },
      error: function(data, status, err){
        alert("서버와의 통신이 실패했습니다.");
      }
    });
  });
});

</script>
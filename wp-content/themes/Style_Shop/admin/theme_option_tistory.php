<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_tistory_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

$use      = ( bbse_get_option("tistory_use") )  == 'U' ? 'on' : 'off';
?>
  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='tistory' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
    <input type='hidden' name='<?php echo $theme_shortname?>_tistory_preOpen' id='<?php echo $theme_shortname?>_tistory_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
    <input type="hidden" name="<?php echo $theme_shortname?>_tistory_use" id="<?php echo $theme_shortname?>_tistory_use" value='<?php echo get_option($theme_shortname."_tistory_use")?>' />

    <input type="hidden" name="<?php echo $theme_shortname?>_display_order" id="display_order" value='<?php echo get_option($theme_shortname."_display_order")?>' />

    <div class="tit">기능 설정 - 티스토리 연동 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_tistory_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>
    <div class="accordionWrap"  data-disabled='false'>
      <ul class="displayAccordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
        <li class="group use-<?php echo $use?>"><!-- 단의이름, 정렬에 사용함, 매우중요 -->
          <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">티스토리 기초정보/인증</div>
              <div class="switch" data-usefrom="<?php echo $theme_shortname?>_tistory_use"></div>
            </div>
          </div>
          <div class="item">
            <ol class="descBox">
              <li class="desc">TISTORY 클라이언트 <a class="infoBtn" href="http://www.tistory.com/guide/api/manage/register" title="등록 페이지" target="_blank">등록 페이지</a>에 접속해 클라이언트를 등록합니다. 이때 아래 Callback 경로를 복사해 <b>'CallBack 경로'</b> 항목에 입력하세요.</li>
              <li class="desc">등록 후 <a  class="infoBtn" href="http://www.tistory.com/guide/api/manage/list" title="클라이언트 목록" target="_blank">클라이언트 목록</a>에서 등록된 서비스의 <b>인증관리</b>버튼을 누릅니다.</li>
              <li class="desc">발급된 <b>Client ID</b>와 <b>Secrete Key</b>를 복사해 아래에 입력하고 일단 저장합니다.</li>
              <li class="desc"><span class="getcode infoBtn">인증 테스트</span> 버튼을 눌러 Access token을 정상적으로 발급받는지 확인합니다.</li>
              <li class="desc"><span class="getcategory infoBtn">카테고리 가져오기</span> TISTORY 카테고리를 가져옵니다.</li>
              <li class="desc">모든 과정이 끝나면 <span class="infoBtn" onClick="save_submit('tistory');">저장</span>버튼을 눌러 모든값을 저장 하십시오</li>
              <li class="desc"><a class="infoBtn" href="<?php home_url() ?>/wp-admin/post-new.php" title="새글쓰기" target="_blank">새글쓰기</a>에서 워드프레스의 포스트를 작성하고 <b>티스토리</b>의 등록 옵션을 선택 후 공개하면 티스토리에 같이 글이 등록됩니다.</li>
            </ol>
            <dl>
              <dt>티스토리 경로</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_tistory_url' id='<?php echo $theme_shortname?>_tistory_url'  value='<?php echo get_option($theme_shortname."_tistory_url");?>'  class="input-long" >
                <span>* http://xxx.tistory.com 일경우 xxx 만 입력, 2차도메인일 경우 http://제거한 url만 입력</span>
              </dd>
            </dl>
            <dl>
              <dt>Callback 경로</dt>
              <dd>
				<?php $dirArr = explode("/",dirname(__FILE__));?>
                <input type='hidden' name='<?php echo $theme_shortname?>_tistory_callback' id='<?php echo $theme_shortname?>_tistory_callback'  value='<?php echo esc_url(BBSE_COMMERCE_THEME_WEB_URL.'/proc/tistory-get-token.callback.php');?>'>
                <span class="input-long"><?php echo esc_url(home_url().'/wp-content/themes/'.$dirArr[count($dirArr)-2].'/proc/tistory-get-token.callback.php');?></span>
              </dd>
            </dl>
            <dl>
              <dt>Client ID</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_tistory_clientid' id='<?php echo $theme_shortname?>_tistory_clientid'  value='<?php echo get_option($theme_shortname."_tistory_clientid");?>' class="input-long" />
              </dd>
            </dl>
            <dl>
              <dt>Secrete Key</dt>
              <dd>
                <input type='text' name='<?php echo $theme_shortname?>_tistory_skey' id='<?php echo $theme_shortname?>_tistory_skey'  value='<?php echo get_option($theme_shortname."_tistory_skey");?>' class="input-long" />

              </dd>
            </dl>
            <dl>
              <dt>Access token</dt>
              <dd>
                <input type='hidden' name='<?php echo $theme_shortname?>_tistory_accesstoken' id='<?php echo $theme_shortname?>_tistory_accesstoken'  value='<?php echo get_option($theme_shortname."_tistory_accesstoken");?>' />
                <span class="input-long"><?php echo get_option($theme_shortname."_tistory_accesstoken");?></span><span class="getcode infoBtn">인증 테스트</span>
              </dd>
            </dl>
            <?php $jsonStr = get_option($theme_shortname."_tistory_category");?>
            <input type='hidden' name='<?php echo $theme_shortname?>_tistory_category' id='<?php echo $theme_shortname?>_tistory_category'  value='<?php echo get_option($theme_shortname."_tistory_category");?>' />
            <input type='hidden' name='<?php echo $theme_shortname?>_tistory_category_time' id='<?php echo $theme_shortname?>_tistory_category_time'  value='<?php echo get_option($theme_shortname."_tistory_category_time");?>' />
            <dl>
              <dt>카테고리 정보</dt>
              <dd class="catContainer">
                <span class="getcategory infoBtn">카테고리 가져오기</span>&nbsp;&nbsp;
                <?php if ($jsonStr){ ?>
                <span>갱신시간 : <?php echo  get_option($theme_shortname."_tistory_category_time")?></span>
                <?php }else{?>
                <span>카테고리를 가져와야합니다.</span>
                <?php }?>
              </dd>
            </dl>
          </div>
        </li><!-- /2단 -->
<!-- ******************************************************************************************************************************************************* -->
      </ul>
    </div>



		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('tistory');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('tistory');">초기화</button>
		</div>
		</form>
	</div>

  <div class="findIDContainer">
    <div id="formContainer"></div>

  </div>

	<!-- //contents 끝 -->
<script>

//Tistory access token 받기
function getTistoryToken($id, $callback) {
  var $target = 'https://www.tistory.com/oauth/authorize/';
  $target += '?client_id='+$id;
  $target += '&redirect_uri='+$callback;
  $target += '&response_type=code';
  $target += '&job_type=test';

  var popOption = "width=400, height=460, resizable=no, scrollbars=no, status=no;";
  var wp = window.open($target, 'get_token', popOption);

  if(wp){ wp.focus(); }
}

//Tistory access token 받기
function getTistoryCategory($token, $tisory) {
  var $target = 'https://www.tistory.com/apis/category/list/';
  $target += '?access_token='+$token;
  $target += '&targetUrl='+$tisory;

  var popOption = "width=400, height=400, resizable=no, scrollbars=no, status=no;";
  var wp = window.open($target, 'share_twitter', popOption);

  if(wp){ wp.focus(); }
}


jQuery(document).ready(function(){
  jQuery('.getcode').click(function(){
    var $id        = jQuery('#<?php echo $theme_shortname?>_tistory_clientid').val();
    var $callback  = jQuery('#<?php echo $theme_shortname?>_tistory_callback').val();

    getTistoryToken($id, $callback);
  });

  jQuery('.getcategory').click(function(){
    var tUrl = '<?php echo get_template_directory_uri()?>/proc/tistory-get-category.ajax.php';
    jQuery.ajax({
      type   : "post",
      async  : false,
      url    : tUrl,
      data   : {},
      success: function(data){
        if (data == 'SUCCESS')
        {
          alert("카테고리 목록을 가져오는데 성공했습니다.");
          location.reload(true);
        }
        else if (data == 'DENIED')
          alert("엑세스거부! 로그인하신 유저인가요?");
        else
          alert("요청실패, 재시도 바랍니다.");
      },
      error: function(data, status, err){
        alert("요청실패, 재시도 바랍니다..");
      }
    });
  });
});
</script>
<style>
.catList {line-height:1.2em;}
</style>
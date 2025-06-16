<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
?>

<script language="javascript">
/* jQuery 대신 $ 사용
(function($) {

})(jQuery);
*/
var theme_shortname="<?php echo $theme_shortname?>";

function upload_img(formfield){
	jQuery('#upload_target_img').val(formfield);
	tb_show('', 'media-upload.php?type=image&TB_iframe=true&width=640&height=450&modal=false');
	return false;
}

function send_to_editor(html){
	var div = document.createElement('div');
	jQuery(div).html(html);
	var src = jQuery(div).find("img:first").attr("src");
	var uTarget=jQuery('#upload_target_img').val();
	jQuery('#'+theme_shortname+'_'+uTarget).val(src);
	tb_remove();
	jQuery('#upload_target_img').val("");
}

jQuery(document).ready( function() {
	jQuery('.colpick').each( function() {
		jQuery('.colpick').minicolors({
			control: jQuery(this).attr('data-control') || 'hue',
			defaultValue: jQuery(this).attr('data-defaultValue') || '',
			inline: jQuery(this).attr('data-inline') === 'true',
			letterCase: jQuery(this).attr('data-letterCase') || 'lowercase',
			opacity: jQuery(this).attr('data-opacity'),
			position: jQuery(this).attr('data-position') || 'bottom left',
			change: function(hex, opacity) {
				var log;
				try {
					log = hex ? hex : 'transparent';
					if( opacity ) log += ', ' + opacity;
					console.log(log);
				} catch(e) {}
			},
			theme: 'default'
		});

	});

	jQuery('.siteType .dl12').each(function() {
		jQuery(this).find('> dd .bg-img-btn').bind('click', function() {
			jQuery('.siteType .dl12 dd .bg-img-btn').removeClass('active');
			jQuery(this).addClass('active');
			var $pattern = jQuery(this).find('.bg_pattern').attr('src');
			jQuery('#'+theme_shortname+'_basic_site_background').val($pattern);
			return false;
		})
	})

	/*jQuery('#_sns_share_kTalk').click(function(){
		if(jQuery(this).prop('checked') == true) {
		  jQuery('.appkey-js').css('display','block');
		}else{
		  jQuery('.appkey-js').css('display','none');
		}
	});*/
});

function menu_str(oType){
	switch (oType) {
	  case "layout" :
		return "메인화면 설정 - 레이아웃 설정";
  		break;
  	case "shopsidebar" :
		return "사이드바 설정";
  		break;
	  case "intro" :
		return "테마 인트로 설정";
  		break;
	  case "basic" :
		return "메인화면 설정 - 기본 설정";
  		break;
	  case "display" :
		return "메인화면 설정 - 디스플레이 설정";
  		break;
	  case "goodsplace" :
		return "메인화면 설정 - 상품배치 설정";
  		break;
	  case "maingoods" :
		return "메인화면 설정 - 메인 상품 진열";
  		break;
	  case "sub" :
		return "서브화면 설정";
  		break;
	  case "color" :
		return "색(Color) 설정";
  		break;
	  case "member" :
		return "기능 설정 - 회원가입/약관/방침";
  		break;
	  case "memberpage" :
		return "약관/방침 페이지설정";
  		break;
	  case "ssl" :
		return "기능 설정 - SSL(보안서버)";
  		break;
	  case "sns" :
		return "기능 설정 - SNS";
  		break;
	  case "map" :
		return "기능 설정 - 지도API (오시는길)";
  		break;
	  case "seo" :
		return "기능 설정 - SEO";
  		break;
	  case "naver" :
		return "기능 설정 - 네이버 연동";
  		break;
	  case "tistory" :
		return "기능 설정 - 티스토리 연동";
  		break;
	  case "google" :
		return "기능 설정 - 구글 연동";
  		break;
	  case "bing" :
		return "기능 설정 - 빙 연동";
  		break;
	  case "baidu" :
		return "기능 설정 - 바이두 연동";
  		break;
	  case "sitemap" :
		return "기능 설정 - 사이트맵 관리";
  		break;
	}
}

function save_submit(oType){
	var msgStr=menu_str(oType);

	if(confirm(msgStr+'을(를) 저장 하시겠습니까?    ')){
		jQuery("#action").val('save');
		jQuery("#option_type").val(oType);
		jQuery("#optionForm").submit();
	}
}

function reset_submit(oType){
	var msgStr=menu_str(oType);
	if(confirm(msgStr+'을(를) 초기화 하시겠습니까?    ')){
		jQuery("#action").val('reset');
		jQuery("#option_type").val(oType);
		jQuery("#optionForm").submit();
	}
}

function check_category(tArray,tSave){
	var check_list = '';

	jQuery("input:checkbox[name^="+tArray+"]:checked").each(function(){
		if(check_list) check_list +=",";
		check_list +=jQuery(this).val();
	});

	jQuery("#"+tSave).val(check_list);
}

function view_category(tType,tCheck,tId){ // 선택된 카테고리 ID 추출 (리스트형/갤러리형)
	var check_list = '';

	jQuery("input:checkbox[name^=category_view_"+tType+"]:checked").each(function(){
		if(check_list) check_list +=",";
		check_list +=jQuery(this).val();
	});
	jQuery("#"+theme_shortname+"_sub_category_view_"+tType).val(check_list);

	if(tCheck=='on'){
		if(tType=='gallery'){
			jQuery("input:checkbox[name^=category_view_list]:input[value="+tId+"]").each(function(){
				jQuery(this).attr('disabled',true);
			});
		}
		else{
			jQuery("input:checkbox[name^=category_view_gallery]:input[value="+tId+"]").each(function(){
				jQuery(this).attr('disabled',true);
			});
		}
	}
	else{
		if(tType=='gallery'){
			jQuery("input:checkbox[name^=category_view_list]:input[value="+tId+"]").each(function(){
				jQuery(this).attr('disabled',false);
			});
		}
		else{
			jQuery("input:checkbox[name^=category_view_gallery]:input[value="+tId+"]").each(function(){
				jQuery(this).attr('disabled',false);
			});
		}
	}
}
/* 모두 체크하기 */
var getAndPushValues = function(index, target){
  var $checkbox = jQuery('.checkall').eq(index).data('checkbox');

  var $count    = jQuery('input[type="checkbox"].'+$checkbox).size();
  var $values   = Array();
  for (var i = 0; i<$count; i++ )
    $values[i] = jQuery('input[type="checkbox"].'+$checkbox).eq(i).val();
  jQuery(target).val($values.join(','));
}
var readyToCheck = function(){
  var $count     = jQuery('.checkall').size();
  for (var i = 0; i<$count; i++ )
  {
    var $container = jQuery('.checkall').eq(i).data('container');
    var $status    = jQuery('#'+$container).val();
    if (!$status) jQuery('.checkall').eq(i).data('check', 'off').text('[모두 체크]');
    else          jQuery('.checkall').eq(i).data('check',  'on').text('[모두 해제]');
  }
}
jQuery(document).ready(function(){
  //사용함/사용안함
  jQuery('span.useCheck').click(function(){
    var $status    = jQuery(this).data('use');
    var $container = jQuery(this).data('container');
    var $target    = jQuery(this).data('target');
    var $type      = jQuery(this).data('type');

    if ($status == 'yes')// 활성이면 비활성시키고 TR 감춤
    {
      jQuery('#'+$container).val('');
      jQuery(this).data('use','no');
      var $btn = jQuery(this).find('img').attr('src').replace("yes", "no");
      jQuery(this).find('img').attr('src', $btn);
      jQuery('.'+$target).css('display','none');
    }
    else if ($status == 'no') // 비활성이면 활성시키고 TR 보여줌
    {
      jQuery('#'+$container).val('U');
      jQuery(this).data('use','yes');
      var $btn = jQuery(this).find('img').attr('src').replace("no.","yes.");
      jQuery(this).find('img').attr('src', $btn);


      if ( $type = 'div' )
        jQuery('.'+$target).css('display','block');
      else
        jQuery('.'+$target).css('display','table-row');
    }
  });

  // 모두 체크/해제 기능
  readyToCheck();
  jQuery('.checkall').click(function(){
    var $index     = jQuery('.checkall').index(jQuery(this));
    var $status    = jQuery(this).data('check');
    var $container = jQuery(this).data('container');
    var $checkbox  = jQuery(this).data('checkbox');
    if ($status == 'off')
    {
      getAndPushValues($index,'#'+$container);
      jQuery('input[type="checkbox"].'+$checkbox).attr('checked',true);
      jQuery(this).data('check','on').text('[모두 해제]');

    }
    else if ($status == 'on')
    {
      jQuery('#'+$container).val('');
      jQuery('input[type="checkbox"].'+$checkbox).attr('checked',false);
      jQuery(this).data('check','off').text('[모두 체크]');
    }
    return false;
  });

  //슬라이더
  jQuery('.slider').change(function(){
    var $index = jQuery('.slider').index(jQuery(this));
    var $value = jQuery('.slider').eq($index).val();
    jQuery('.sliderValue').text($value);
  });

  if ( jQuery('.accordionWrap ul.displayAccordion').hasClass('displayAccordion') )//general
  {
    sorting();
    //소팅
    jQuery('.accordionWrap ul.displayAccordion').sortable({
      axis: "y",
      handle: ".sortHandle",
      update: function( event, ui ) {
        sortPrepare();
        jQuery('.accordionWrap').data('disabled', false);
      }
    });
  }
  else //display
  {
    //열어두기/닫아두기
    allOpen();
    //아코디언 type1
    accordionType1();
    //내부아코디언
    accordionType2();
  }

});


/* SEASON2 COMMON */
var allOpen = function(){
  jQuery('.all_preOpen').click(function(){
    var target    = jQuery(this).data('target');
    var targetObj = jQuery('#'+target);
    var value     = targetObj.val();

    if (value == 'U')
    {
      jQuery('.accordion, .displayAccordion').removeClass('preOpen');
      jQuery('.group .itemHeader').removeClass('opened');
      jQuery(this).text('모두 열어두기');
      targetObj.val('N');
      jQuery('.group > .item').slideUp(50);
    }
    else
    {
      jQuery('.accordion, .displayAccordion').addClass('preOpen');
      jQuery('.group .itemHeader').addClass('opened');
      jQuery(this).text('모두 닫아두기');
      targetObj.val('U');
      jQuery(' .group > .item').slideDown(50);
    }
  });
}

var accordionType1 = function($itemGroup)
{
  if ($itemGroup)
  {
    var $disabled = jQuery('.accordionWrap').data('disabled');
    var $eventEl  = jQuery('> .itemHeader .title', $itemGroup);
    var $item     = jQuery('> .item', $itemGroup);
  }
  else
  {
    var $disabled = false;
    var $eventEl  = jQuery('.group .itemHeader');
    var $item     = jQuery('.group > .item');
  }

  $eventEl.click(function($itemGroup){
    if ($itemGroup) var index = $eventEl.index(jQuery(this));
    else            var index = jQuery('.title', $itemHeader).index(jQuery(this));

    if ($disabled == false )
    {
      if ( $item.eq(index).css('display') == 'none' )
      {
        $item.eq(index).slideDown(50);
        jQuery('.group .itemHeader').eq(index).addClass('opened');
      }
      else
      {
        $item.eq(index).slideUp(50);
        jQuery('.group .itemHeader').eq(index).removeClass('opened');
      }
    }
  });
}

var accordionType2 = function(){
  jQuery('.itemAccordion .itemGroup .title').click(function(){
    var index = jQuery('.itemAccordion .itemGroup .title').index(jQuery(this));
    jQuery('.itemAccordion .itemGroup .item').slideUp(50);
    jQuery('.itemAccordion .itemGroup .title').removeClass('opened');
    if (jQuery('.itemAccordion .itemGroup .item').eq(index).css('display') == 'none')
    {
      jQuery('.itemAccordion .itemGroup .item').eq(index).slideDown(50);
      jQuery(this).addClass('opened');
    }
  });
}

//아이템 정렬 준비 [display]
var sortPrepare = function(){
  jQuery('body').css('overflow-y','scroll');
  var sotedData = [];
  jQuery.each(jQuery('.group'), function(i){
    sotedData[i] = jQuery(this).attr('id');
  });
  var $sortOrderStr = sotedData.join(',');
  jQuery('#display_order').val($sortOrderStr);
  return $sortOrderStr;
}//sortPrepare

//아이템 정렬 & 아코디언 [display]
var sorting = function(){
  var $sortOrderTest = jQuery('#display_order').val();
  var $wrapObj       = jQuery('.accordionWrap');
  var $accordion     = jQuery('> .displayAccordion', $wrapObj);
  var $itemGroup     = jQuery('> .group', $accordion);
  var $itemHeader    = jQuery('> .itemHeader', $itemGroup);
  var $item          = jQuery('> .item', $itemGroup);
  if ($accordion.hasClass('preOpen')) var preOpened = true;
  else                                var preOpened = false;
  if (!$sortOrderTest || ($sortOrderTest.split(',').length != $itemGroup.size()))
    var $sortOrderStr = sortPrepare();
  else
  {
    var $sortOrder    = $sortOrderTest.split(',');
    jQuery('<ul />').addClass('accordionTemp').appendTo($wrapObj);

    for (var key in $sortOrder )
      $accordion.find('#'+$sortOrder[key]).clone().appendTo(jQuery('.accordionTemp'));

    $accordion.remove();
    if (preOpened == true)
      jQuery('.accordionTemp').addClass('displayAccordion preOpen').removeClass('accordionTemp');
    else
      jQuery('.accordionTemp').addClass('displayAccordion').removeClass('accordionTemp');

    //오브젝트틀 재설정
    $accordion     = jQuery('> .displayAccordion', $wrapObj);
    $itemGroup     = jQuery('> .group', $accordion);
    $itemHeader    = jQuery('> .itemHeader', $itemGroup);
    $item          = jQuery('> .item', $itemGroup);
  }
  jQuery('li',$wrapObj).fadeIn('slow');

  allOpen();
  //아코디언 type1
  accordionType1($itemGroup);
  //내부아코디언
  accordionType2();

  //사용함/안함
  jQuery('.switch').click(function(){
    $itemGroup     = jQuery('> .displayAccordion > .group', $wrapObj);

    var $formName = jQuery(this).data('usefrom');
    var index     = jQuery('.switch').index(jQuery(this));
    if ($itemGroup.eq(index).hasClass('use-off'))
    {
      jQuery('#'+$formName).val('U');
      $itemGroup.eq(index).removeClass('use-off').addClass('use-on');
    }
    else if ($itemGroup.eq(index).hasClass('use-on'))
    {
      jQuery('#'+$formName).val('N');
      $itemGroup.eq(index).removeClass('use-on').addClass('use-off');
    }
  });

  //소팅핸들에 오버하면..
  jQuery('.sortHandle').hover(
    function(){
      $wrapObj.data('disabled', true);
      jQuery(this).css('opacity',1);
    },
    function(){
      $wrapObj.data('disabled', false);
      jQuery(this).css('opacity',0.3);
    }
  ).mousedown(function(){

    $item.slideUp('fast');
    jQuery('.group .itemHeader').removeClass('opened');
  });

  //prepareColorPicker();
} //sorting
/* END SEASON2 COMMON */

</script>
<div class="wrap">
	<?php
	global $wpdb;
	$theme_base = get_template();            // 테마 디렉토리명
	$info=wp_get_theme($theme_base);         // 테마 정보

	$menuName=Array("layout"=>"메인화면 설정 - 레이아웃 설정", "shopsidebar"=> '사이드바 설정',"intro"=>"테마 인트로 설정", "basic"=>"메인화면 설정 - 기본 설정", "display"=>"메인화면 설정 - 디스플레이 설정", "goodsplace"=>"메인화면 설정 - 상품배치 설정","maingoods"=>"메인화면 설정 - 메인 상품 진열", "sub"=>"서브화면 설정", "color"=>"색(Color) 설정", "member"=>"기능 설정 - 회원가입/약관/방침", "memberpage"=>"기능 설정 - 약관/방침 페이지 설정", "ssl"=>"기능 설정 - SSL(보안서버)", "sns"=>"기능 설정 - SNS", "map"=>"기능 설정 - 지도API (오시는길)", "seo"=>"기능 설정 - SEO", "naver"=>"기능 설정 - 네이버 연동", "tistory"=>"기능 설정 - 티스토리 연동", "google"=>"기능 설정 - 구글 연동", "bing"=>"기능 설정 - 빙 연동");

	if(isset($_REQUEST['saved']) or isset($_REQUEST['reseted'])){
		if($_REQUEST['saved']=='true') echo '<div id="message" class="updated fade"><p><strong>테마의 '.$menuName[$_REQUEST['optTtpe']].'을(를) 저장하였습니다.</strong></p></div>';
		elseif($_REQUEST['reseted']=='true') echo '<div id="message" class="updated fade"><p><strong>테마의 '.$menuName[$_REQUEST['optTtpe']].'을(를) 초기화 하였습니다.</strong></p></div>';
	}
	if(!isset($_REQUEST['optTtpe'])) $_REQUEST['optTtpe']='layout';
	?>
	<div id="bbse_box">
	<div class="inner">
		<div class="guide_top">
			<span class="tl"></span><span class="tr"></span><span class="logo_bg"></span>
			<a href="#"><span class="logo">BBSe</span><span class="logo_theme">Theme</span><span class="logo_version">v<?php echo trim($info['Version'])?></span></a>
		</div>
		<div id="container" class="snb_bg">
			<div id="top_bg_stitch"></div><div id="top_bg_overlay"></div>


			<!--snb -->
			<div class="snb">

				<div class="leftmenu">
					<ul>
						<li class="<?php echo ($_REQUEST['optTtpe']=='layout' || $_REQUEST['optTtpe']=='basic' || $_REQUEST['optTtpe']=='display' || $_REQUEST['optTtpe']=='goodsplace')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=layout'>메인화면 설정<span></span></a>
							<ul>
								<li class="<?php echo ($_REQUEST['optTtpe']=='layout')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=layout'>레이아웃 설정</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='basic')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=basic'>기본 설정</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='display')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=display'>디스플레이 설정</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='goodsplace')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=goodsplace'>상품배치 설정</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='maingoods')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=maingoods'>메인 상품 진열</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='intro')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=intro'>테마 인트로 설정</a></li>
							</ul>
						</li>
						<li class="<?php echo ($_REQUEST['optTtpe']=='shopsidebar')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=shopsidebar'>사이드바 설정<span></span></a></li>
						<li class="<?php echo ($_REQUEST['optTtpe']=='sub')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=sub'>서브화면 설정<span></span></a></li>
						<li class="<?php echo ($_REQUEST['optTtpe']=='color')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=color'>색(Color) 설정<span></span></a></li>
						<li class="<?php echo ($_REQUEST['optTtpe']=='member' || $_REQUEST['optTtpe']=='ssl' || $_REQUEST['optTtpe']=='sns' || $_REQUEST['optTtpe']=='map' || $_REQUEST['optTtpe']=='seo' || $_REQUEST['optTtpe']=='naver' || $_REQUEST['optTtpe']=='tistory' || $_REQUEST['optTtpe']=='google' || $_REQUEST['optTtpe']=='bing')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=member'>기능 설정<span></span></a>
							<ul>
								<li class="<?php echo ($_REQUEST['optTtpe']=='member')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=member'>회원가입/약관/방침</a></li>
						<?php if(plugin_active_check('BBSe_Commerce')) {?>
								<li class="<?php echo ($_REQUEST['optTtpe']=='memberpage')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=memberpage'>약관/방침 페이지설정</a></li>
						<?php }?>
								<li class="<?php echo ($_REQUEST['optTtpe']=='ssl')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=ssl'>SSL(보안서버)</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='sns')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=sns'>SNS</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='map')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=map'>지도API</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='seo')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=seo'>SEO</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='naver')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=naver'>네이버 연동</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='tistory')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=tistory'>티스토리 연동</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='google')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=google'>구글 연동</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='bing')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=bing'>빙 연동</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='baidu')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=baidu'>바이두 연동</a></li>
								<li class="<?php echo ($_REQUEST['optTtpe']=='sitemap')?"on":""?>"><a href='themes.php?page=functions.php&optTtpe=sitemap'>사이트맵 관리</a></li>
							</ul>
						</li>
						<li class="on"><a href='themes.php?page=functions.php&optTtpe=license'>라이센스 키 관리</a></li>
						<li><a href="http://demo-styleshop.bbsecommerce.com/" target="_blank"><img src="<?php bloginfo('template_url')?>/admin/images/btn_demo.png" /></a></li>
						<li><a href="http://manual.bbsecommerce.com/ss_1/" target="_blank"><img src="<?php bloginfo('template_url')?>/admin/images/btn_manual.png" /></a></li>
					</ul>
				</div>

			</div>
			<!--//snb -->

			<?php require_once('theme_option_'.$_REQUEST['optTtpe'].'.php');?>

		</div><!-- // container -->
		<div class="guide_bottom"><span class="lb"></span><span class="rb"></span></div>
	</div>
	</div><!-- // bbse_box -->
</div>
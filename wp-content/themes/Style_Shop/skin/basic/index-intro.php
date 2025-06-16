<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$adultIcon=(get_option($theme_shortname."_intro_19_icon")=='basic' || !get_option($theme_shortname."_intro_19_icon_img"))?BBSE_THEME_WEB_URL."/images/thumb_adult_19.png":get_option($theme_shortname."_intro_19_icon_img");
$bgYellow=(get_option($theme_shortname."_intro_19_color_button_background"))?get_option($theme_shortname."_intro_19_color_button_background"):"#ffbf44";
$fgYellow=(get_option($theme_shortname."_intro_19_color_button_foreground"))?get_option($theme_shortname."_intro_19_color_button_foreground"):"#ffffff";
$bgOrange=(get_option($theme_shortname."_intro_19_color_exitbutton_background"))?get_option($theme_shortname."_intro_19_color_exitbutton_background"):"#ff8c37";
$fgOrange=(get_option($theme_shortname."_intro_19_color_exitbutton_foreground"))?get_option($theme_shortname."_intro_19_color_exitbutton_foreground"):"#ffffff";

//SEO 추가(시작)
$seo_outTitle       = get_option($theme_shortname."_seo_title");
$seo_outKeywords    = get_option($theme_shortname."_seo_keywords");
$seo_outDescription = get_option($theme_shortname."_seo_description");
$seo_outImage       = get_option($theme_shortname.'_basic_logo_img');
$seo_outUrl         = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

$seoOutput  = PHP_EOL;

$seoOutput .= '<meta name="generator" content="WordPress '.get_bloginfo('version').'" />'.PHP_EOL;

if (get_option($theme_shortname.'_naver_use_webmaster') == 'U' && get_option($theme_shortname.'_naver_owntag')){
  $seoOutput .= '<meta name="naver-site-verification" content="'.get_option($theme_shortname.'_naver_owntag').'" />'.PHP_EOL;
}

if (get_option($theme_shortname.'_google_use_webmaster') == 'U' && get_option($theme_shortname.'_google_owntag')){
  $seoOutput .= '<meta name="google-site-verification" content="'.get_option($theme_shortname.'_google_owntag').'" />'.PHP_EOL;
}

if (get_option($theme_shortname.'_bing_use_webmaster') == 'U' &&  get_option($theme_shortname.'_bing_owntag')){
  $seoOutput .= '<meta name="msvalidate.01" content="'.get_option($theme_shortname.'_bing_owntag').'" />'.PHP_EOL;
}

if (get_option($theme_shortname.'_baidu_use_webmaster') == 'U' &&  get_option($theme_shortname.'_baidu_use_webmaster')){
  $seoOutput .= '<meta name="baidu-site-verification" content="'.get_option($theme_shortname.'_baidu_owntag').'" />'.PHP_EOL;
}
$seoOutput .= PHP_EOL;

if ($seo_outUrl)  $seoOutput .= '<link rel="canonical" href="'.$seo_outUrl.'" />'.PHP_EOL;

$seoOutput .= "<link rel='alternate' hreflang='ko' href='".$seo_outUrl."' />".PHP_EOL;
$seoOutput .= '<meta property="og:locale:alternate" content="'.str_replace("-","_",get_bloginfo('language')).'" />'.PHP_EOL.
					  '<meta property="og:type" content="website" />'.PHP_EOL.
					  '<meta property="og:site_name" content="'.get_bloginfo('name').'" />'.PHP_EOL;
if($seo_outTitle) $seoOutput .= '<meta property="og:title" content="'.esc_attr($seo_outTitle).'" />'.PHP_EOL;
if($seo_outKeywords) $seoOutput .= '<meta property="og:keywords" content="'.$seo_outKeywords.'" />'.PHP_EOL.
														'<meta name="keywords" content="'.$seo_outKeywords.'" />'.PHP_EOL;
if($seo_outDescription) $seoOutput .= '<meta property="og:description" content="'.esc_attr($seo_outDescription).'" />'.PHP_EOL.
														'<meta name="description" content="'.esc_attr($seo_outDescription).'" />'.PHP_EOL;
if($seo_outImage) $seoOutput .= '<meta property="og:image" content="'.esc_url($seo_outImage).'" />'.PHP_EOL;
if($seo_outUrl) $seoOutput .= '<meta property="og:url" content="'.$seo_outUrl.'" />'.PHP_EOL.
											'<meta property="og:canonical" content="'.$seo_outUrl.'" />'.PHP_EOL;
//SEO 추가(끝)
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no, address=no, email=no">
<meta name="viewport" content="width=device-width, user-scalable=no">
<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
<script type='text/javascript' src='<?php bloginfo('template_url')?>/js/jquery-1.10.2.min.js'></script>
<style type="text/css">
body{font-size:1.3em;margin:0;padding:0;width:100%;font-family: 'Noto Sans', 'NanumGothic', 'Nanum Gothic', sans-serif;background:<?php echo get_option($theme_shortname."_intro_19_color_middle_background");?>;}
input{font-family: 'Noto Sans', 'NanumGothic', 'Nanum Gothic', sans-serif;}
.contLimiter{margin:0 auto;padding:0;max-width:1210px;}
.hidden{display:none;}

.siteHead{margin:0;padding:0;width:100%;min-height:70px;line-height:70px;background:<?php echo get_option($theme_shortname."_intro_19_color_top_background");?>;}
  .siteLogo{display:inline-block;margin:0;padding:0;}
    .siteLogo a{display:block;}
      .siteLogo a img{display:inline-block;max-width:100%;height:auto;border:none;}
.siteBody{margin:0;padding:0;width:100%;}
  .row1{margin:0 0 30px 0;padding:0;width:100%;min-height:100px;}
    .row1 img{margin:0 auto;display:block;width:auto;height:auto;border:none;}
  .row2{margin:0;padding:0;width:100%;}
    .row2 p{margin:0 auto 20px;padding:10px 0 20px 120px;width:58%;line-height:22px;font-size:15px;letter-spacing:1px;background:url(<?php echo $adultIcon;?>) no-repeat;0 50%;color:<?php echo get_option($theme_shortname."_intro_19_color_middle_foreground");?>;}
    .row2 .blockWrap{margin:0 0 80px 0;padding:0;width:100%;text-align:center;}
      .row2 .blockWrap a{display:inline-block;margin:0 3px 5px;padding:13px 30px;font-size:16px;font-weight:bold;text-decoration:none;}
      .row2 .blockWrap a.yellow{background:<?php echo $bgYellow;?>;color:<?php echo $fgYellow;?>;}
      .row2 .blockWrap a.orange{background:<?php echo $bgOrange;?>;color:<?php echo $fgOrange;?>;}
  .row3{margin:0;padding:0;width:100%;height:250px;}
    .row3 .loginWrap{position:relative;margin:0;padding:30px 0 0 38px;height:220px;text-align:center;}
	.row3 .loginWrap .open_alert {font-size:12px;margin:0 auto;margin-top:10px;color:<?php echo $bgOrange;?>;width:270px;text-align:center;}
      .row3 .loginWrap h2{margin:0 0 30px 0;padding:0 0 16px 0;font-size:24px;font-weight:bold;color:<?php echo get_option($theme_shortname."_intro_19_color_middle_foreground");?>;border-bottom:1px solid #ebebeb;}
      .row3 .loginWrap form{display:inline-block;margin:0 0 10px 30px;padding:0;}
      .row3 .loginWrap form:after{display:block;content:"";clear:both;}
        .row3 .loginWrap form .formLeft{float:left;margin:0 10px 0 0;width:270px;height:70px;}
          .row3 .loginWrap form .formLeft label{display:block;margin:0;padding:0;width:100%;height:30px;line-height:30px;color:<?php echo get_option($theme_shortname."_intro_19_color_middle_foreground");?>;}
          .row3 .loginWrap form .formLeft label:first-child{margin-bottom:10px;}
            .row3 .loginWrap form .formLeft label input{float:right;margin:0;padding:0 5px;width:76%;height:28px;border:1px solid #ebebeb;outline:none;}
        .row3 .loginWrap form .formRight{float:left;width:110px;height:70px;}
          .row3 .loginWrap form .formRight input{margin:0;padding:0;width:100%;height:100%;border:none;background:<?php echo $bgYellow;?>;font-size:18px;font-weight:bold;color:<?php echo $fgYellow;?>;outline:none;cursor:pointer;}

.row4{margin:0;padding:0;width:100%;min-height:80px;}
	.row4 .contLimiter {text-align:<?php echo (get_option($theme_shortname."_intro_19_bottom_banner_align"))?get_option($theme_shortname."_intro_19_bottom_banner_align"):"center";?>;}

.siteFoot{margin:0;padding:0;width:100%;min-height:40px;line-height:20px;background:<?php echo get_option($theme_shortname."_intro_19_color_bottom_background");?>;text-align:center;}
  .siteFoot p{margin:0;padding:20px 0;font-size:12px;color:<?php echo get_option($theme_shortname."_intro_19_color_bottom_foreground");?>;}

@media screen and (max-width:1209px){
    .row1 img{width:100%;height:auto;}
	.row4 .contLimiter {text-align:center;}
}

@media screen and (max-width:1024px){
.contLimiter{width:95%;}
}

@media screen and (max-width:780px){
.row3 .loginWrap{float:none;width:100%;padding:0;text-align:center;}
.row3 .loginWrap h2{text-align:left;}
.row3 .loginWrap form{margin:0 0 10px 0;}
}
@media screen and (max-width:430px){
.row2 p{padding:117px 0 0 0;width:90%;font-size:14px;background-position:50% 0;}
.row2 .blockWrap a{padding:13px 20px;font-size:15px;}

.row3{height:280px;}
	.row3 .loginWrap{height:250px;}
	.row3 .loginWrap form{width:100%;}
	.row3 .loginWrap form .formLeft{float:none;width:80%;display: inline-block;}
	.row3 .loginWrap form .formRight{float:none;width:80%;margin-top:20px;display: inline-block;}
}
</style>
<script>
<?php if(get_option($theme_shortname."_intro_19_login_use")=='U'){?>
	function b64EncodeUnicode(str) {
		return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
			return String.fromCharCode('0x' + p1);
		}));
	}

	function login_check(ajax_act){
		if(jQuery("#upass").val()){
			jQuery("#upass").val(b64EncodeUnicode(jQuery("#upass").val()))
		}

		jQuery.ajax({
			url: ajax_act,
			type: "post",
			dataType: "jsonp",
			//async: false,
			jsonp: "callback",
			//jsonpCallback: 'myCallback',
			data: jQuery('#login_frm').serialize(),
			crossDomain: true,
			beforeSend: function(){},
			complete: function(){},
			success: response_json,
			error: function(data, status, err){
				var errorMessage = err || data.statusText;
				alert(errorMessage);
			}
		});
	}

	function response_json(json){
		var list_cnt = json.length;
		var homeURL="<?php echo home_url();?>";
		if(list_cnt > 0){
			jQuery.each(json, function(index, entry){
				var res = entry['result'];
				if(res == "success"){
					window.location.href = homeURL;
				}else if(res == "empty id"){
					jQuery("#error_box").show();
					jQuery("#error_box").html("에러 : 아이디를 입력해주세요.");
					jQuery("#uid").focus();
				}else if(res == "id_fail"){
					jQuery("#error_box").show();
					jQuery("#error_box").html("에러 : 아이디/비밀번호가 올바르지 않습니다.");
					jQuery("#uid").focus();
				}else if(res == "empty pass"){
					jQuery("#error_box").show();
					jQuery("#error_box").html("에러 : 비밀번호를 입력해주세요.");
					jQuery("#upass").focus();
				}else if(res == "pass_fail"){
					jQuery("#error_box").show();
					jQuery("#error_box").html("에러 : 아이디/비밀번호가 올바르지 않습니다.");
					jQuery("#upass").focus();
				}else if(res == "login_fail"){
					jQuery("#error_box").show();
					jQuery("#error_box").html("에러 : 로그인 오류입니다.");
					jQuery("#uid").focus();
				}
			});
		}
	}
<?php }?>

	function open_nice_checkplus(){
		window.name ="Parent_window";
		window.open('', 'BBSeCommerceAuth', 'width=500, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no');
		document.authCheckFrm.action = "https://nice.checkplus.co.kr/CheckPlusSafeModel/checkplus.cb";
		document.authCheckFrm.target = "BBSeCommerceAuth";
		document.authCheckFrm.submit();
	}
</script>
<?php echo $seoOutput;?>
</head>
<body>
  <header class="siteHead">
    <div class="contLimiter">
      <h1 class="siteLogo"><img src="<?php echo get_option($theme_shortname."_intro_19_logo");?>" alt="<?php bloginfo('name');?>"><span class="hidden"><?php bloginfo('name');?></span></h1>
    </div>
  </header>
  <main class="siteBody">
    <div class="row1">
      <img src="<?php echo get_option($theme_shortname."_intro_19_top_banner");?>" alt="19세 미안의 청소년은 이용할 수 없습니다.">
    </div> 
    <div class="row2">
      <div class="contLimiter">
	<?php if(get_option($theme_shortname."_intro_19_introduce")){?>
        <p>
			<?php echo nl2br(stripcslashes(get_option($theme_shortname."_intro_19_introduce")));?>
        </p>
	<?php }?>
<?php
    //**************************************************************************************************************
    //NICE평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED

    //서비스명 :  체크플러스 - 안심본인인증 서비스
    //페이지명 :  체크플러스 - 메인 호출 페이지

    //보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다.
    //**************************************************************************************************************
	$config = $wpdb->get_row("SELECT * FROM bbse_commerce_membership_config LIMIT 1");
    $authuse = $config->certification_yn;				// NICE 본인인증 사용여부
    $sitecode = $config->certification_id;				// NICE로부터 부여받은 사이트 코드
    $sitepasswd = $config->certification_pass;			// NICE로부터 부여받은 사이트 패스워드

	if($authuse=='Y' && $sitecode && $sitepasswd){
		$cb_encode_path = BBSE_COMMERCE_THEME_ABS_PATH."/auth/CheckPlusSafe/CPClient";		// NICE로부터 받은 암호화 프로그램의 위치 (절대경로+모듈명)
		$authtype = "M";      	// 없으면 기본 선택화면, X: 공인인증서, M: 핸드폰, C: 카드
		$popgubun 	= "N";		//Y : 취소버튼 있음 / N : 취소버튼 없음
		if($deviceType == "mobile") {
			$customize 	= "Mobile";			//없으면 기본 웹페이지 / Mobile : 모바일페이지
		}else{
			$customize 	= "";			//없으면 기본 웹페이지 / Mobile : 모바일페이지
		}

		if(extension_loaded('CPClient')) {
			$reqseq = get_cprequest_no($sitecode); // => CPClint.so 모듈
		}else{
			$reqseq = `$cb_encode_path SEQ $sitecode`;// 요청 번호, 이는 성공/실패후에 같은 값으로 되돌려줌 => CPClient 모듈
		}

		// CheckPlus(본인인증) 처리 후, 결과 데이타를 리턴 받기위해 다음예제와 같이 http부터 입력합니다.
		$returnurl = BBSE_COMMERCE_THEME_WEB_URL."/auth/CheckPlusSafe/checkplus_success_intro.php";	// 성공시 이동될 URL
		$errorurl = BBSE_COMMERCE_THEME_WEB_URL."/auth/CheckPlusSafe/checkplus_fail.php";		// 실패시 이동될 URL (회원가입 시 인증 실패 URL과 동일)

		// reqseq값은 성공페이지로 갈 경우 검증을 위하여 세션에 담아둔다.

		$_SESSION["REQ_SEQ"] = $reqseq;

		// 입력될 plain 데이타를 만든다.
		$plaindata =  "7:REQ_SEQ" . strlen($reqseq) . ":" . $reqseq .
								  "8:SITECODE" . strlen($sitecode) . ":" . $sitecode .
								  "9:AUTH_TYPE" . strlen($authtype) . ":". $authtype .
								  "7:RTN_URL" . strlen($returnurl) . ":" . $returnurl .
								  "7:ERR_URL" . strlen($errorurl) . ":" . $errorurl .
								  "11:POPUP_GUBUN" . strlen($popgubun) . ":" . $popgubun .
								  "9:CUSTOMIZE" . strlen($customize) . ":" . $customize ;

		if(extension_loaded('CPClient')) {
			$enc_data = get_encode_data($sitecode, $sitepasswd, $plaindata);
		}else{
			$enc_data = `$cb_encode_path ENC $sitecode $sitepasswd $plaindata`;
		}

		if( $enc_data == -1 )
		{
			$returnMsg = "암/복호화 시스템 오류입니다.";
			$enc_data = "";
		}
		else if( $enc_data== -2 )
		{
			$returnMsg = "암호화 처리 오류입니다.";
			$enc_data = "";
		}
		else if( $enc_data== -3 )
		{
			$returnMsg = "암호화 데이터 오류 입니다.";
			$enc_data = "";
		}
		else if( $enc_data== -9 )
		{
			$returnMsg = "입력값 오류 입니다.";
			$enc_data = "";
		}
	}
?>

        <div class="blockWrap">
			<form name="authCheckFrm" method="post">
			<input type="hidden" name="m" value="checkplusSerivce">
			<input type="hidden" name="EncodeData" value="<?php echo  $enc_data ?>">
			<input type="hidden" name="param_r1" value="">
			<input type="hidden" name="param_r2" value="">
			<input type="hidden" name="param_r3" value="">
			</form>

          <a href="javascript:<?php echo ($authuse=='Y' && $sitecode && $sitepasswd)?"open_nice_checkplus()":"alert('휴대폰 본인인증 서비스 사용이 불가능합니다.    ')";?>;" class="yellow">휴대폰 인증</a>
		<?php if(get_option($theme_shortname."_intro_19_under_exit_use")=='U' && get_option($theme_shortname."_intro_19_under_exit_url")){?>
          <a href="<?php echo get_option($theme_shortname."_intro_19_under_exit_url");?>" target="_self" class="orange">19세 미만 나가기</a>
		<?php }?>
        </div>
      </div>
    </div>

<?php if(get_option($theme_shortname."_intro_19_login_use")=='U'){?>
    <div class="row3">
      <div class="contLimiter">
        <div class="loginWrap">
          <h2>MEMBER LOGIN</h2>
			<form method="post" name="login_frm" id="login_frm">
            <div class="formLeft">
              <label for="id">
                I&nbsp; D <input type="text" name="uid" id="uid" onfocus="this.value='';return true;" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}" tabindex="1" />
              </label>
              <label for="pw">
                P W <input type="password" name="upass" id="upass" onfocus="this.value='';return true;" tabindex="2" onkeypress="if(jQuery(this).val() != ''){jQuery('#error_box').empty().hide();}if(event.keyCode == 13) login_check('<?php echo BBSE_THEME_WEB_URL;?>/proc/login.exec.php');" />
              </label>
            </div>
            <div class="formRight">
              <label for="login" class="hidden">login</label>
              <input type="button" name="login" id="login" value="로그인" tabindex="3" onclick="login_check('<?php echo BBSE_THEME_WEB_URL;?>/proc/login.exec.php');">
            </div>
			<div style="clear:both;width:100%;"></div>
			<!-- alert  -->
			<p id="error_box" class="open_alert" style="display:none;"></p>
			<!--//alert  -->
          </form>
        </div>
      </div>
    </div>
<?php }?>

<?php
$lguplusCnt='0';
if(get_option($theme_shortname."_intro_19_use_bottom_banner")=="U"){
?>
    <div class="row4">
      <div class="contLimiter">
		<?php
			for($bottomBannerCnt=0;$bottomBannerCnt<=get_option($theme_shortname."_intro_19_use_bottom_banner_count");$bottomBannerCnt++) {
				if(get_option($theme_shortname."_intro_19_bottom_banner_img_".$bottomBannerCnt)!=""){

					if(get_option($theme_shortname."_intro_19_bottom_banner_type_".$bottomBannerCnt)=="lguplus"){
						$lguplusCnt ++;
			?>
					<img src="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="goValidEscrow('<?php echo trim(str_replace(" ","",get_option($theme_shortname."_intro_19_bottom_banner_mallid_".$bottomBannerCnt)));?>');" style="cursor:pointer;" />
			<?php
					}
					elseif(get_option($theme_shortname."_intro_19_bottom_banner_type_".$bottomBannerCnt)=="inicis"){
			?>
					<img src="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="window.open('https://mark.inicis.com/mark/escrow_popup.php?mid=<?php echo trim(str_replace(" ","",get_option($theme_shortname."_intro_19_bottom_banner_mallid_".$bottomBannerCnt)));?>','mark','scrollbars=no,resizable=no,width=565,height=683');" style="cursor:pointer;" />
			<?php
					}
					elseif(get_option($theme_shortname."_intro_19_bottom_banner_type_".$bottomBannerCnt)=="allthegate"){
			?>
					<img src="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="window.open('http://www.allthegate.com/hyosung/paysafe/escrow_check.jsp?service_id=<?php echo trim(str_replace(" ","",get_option($theme_shortname."_intro_19_bottom_banner_mallid_".$bottomBannerCnt)))?>&biz_no=<?php echo trim(str_replace("-","",str_replace(" ","",get_option($theme_shortname."_intro_19_bottom_banner_businessno_".$bottomBannerCnt))))?>','allthegate_window','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=410,height=320')" style="cursor:pointer;" />
			<?php
					}
					elseif(get_option($theme_shortname."_intro_19_bottom_banner_type_".$bottomBannerCnt)=="fairtrade"){
			?>
					<img src="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?php echo trim(str_replace("-","",str_replace(" ","",get_option($theme_shortname."_intro_19_bottom_banner_businessno_".$bottomBannerCnt))))?>','fairtrade_window','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=735,height=632')" style="cursor:pointer;" />
			<?php
					}
					else{
			?>
					<a href="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_url_".$bottomBannerCnt)?get_option($theme_shortname."_intro_19_bottom_banner_url_".$bottomBannerCnt):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_url_".$bottomBannerCnt."_window")?>"><img src="<?php echo get_option($theme_shortname."_intro_19_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" /></a>
			<?php
					}
				}
			}
		?>
	  </div>
	</div>
<?php
}
?>
  </main>
<?php if(get_option($theme_shortname."_intro_19_footer")){?>
  <footer class="siteFoot">
        <p>
			<?php echo nl2br(stripcslashes(get_option($theme_shortname."_intro_19_footer")));?>
        </p>
  </footer>
<?php }?>
</body>
</html>

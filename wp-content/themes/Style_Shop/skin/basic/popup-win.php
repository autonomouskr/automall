<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$popup = $wpdb->get_row("select * from bbse_commerce_config where idx='".$_GET['pidx']."'");

if(count($popup) < 1 || !plugin_active_check('BBSe_Commerce')) {
	echo "<script>alert('잘못된 접근입니다.');self.close();</script>";
	exit;
}
$pop = unserialize($popup->config_data);

?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $pop['popup_title']; ?></title>
<link rel='stylesheet' id='bbse-style-css'  href='<?php echo BBSE_COMMERCE_THEME_WEB_URL; ?>/style.css' type='text/css' media='all' />
<?php require_once(BBSE_COMMERCE_THEME_ABS_PATH.'/part/color-set.php'); ?>
<style>
/* POST RULES */
#BBSE-COMMERCE-CONTENT,
#BBSE-COMMERCE-CONTENT * {font-family: '나눔고딕','NanumGothic','Nanum Gothic','굴림',gulim,'돋움',Dotum,AppleGothic,Arial, Helvetica, sans-serif,FontAwesome;word-break:break-all}

#BBSE-COMMERCE-CONTENT h1 {clear:both;line-height:1.4em; font-size:2em;margin:0.67em 0}
#BBSE-COMMERCE-CONTENT h2 {clear:both;line-height:1.86em;font-size:1.5em;margin:0.60em 0}
#BBSE-COMMERCE-CONTENT h3 {clear:both;line-height:2.39em;font-size:1.17em;margin:0.53em 0}
#BBSE-COMMERCE-CONTENT h4 {clear:both;line-height:2.4em; font-size:1em;margin:0.46em 0}
#BBSE-COMMERCE-CONTENT h5 {clear:both;line-height:2.83em;font-size:0.83em;margin:0.39em 0}
#BBSE-COMMERCE-CONTENT h6 {clear:both;line-height:3.99em;font-size:0.7em;margin:0.32em 0}

#BBSE-COMMERCE-CONTENT h1:first-child,
#BBSE-COMMERCE-CONTENT h2:first-child,
#BBSE-COMMERCE-CONTENT h3:first-child,
#BBSE-COMMERCE-CONTENT h4:first-child,
#BBSE-COMMERCE-CONTENT h5:first-child,
#BBSE-COMMERCE-CONTENT h6:first-child { margin-top: 0; }

#BBSE-COMMERCE-CONTENT address {font-style:italic;margin-bottom:24px;}
#BBSE-COMMERCE-CONTENT strong {font-weight:700;}
#BBSE-COMMERCE-CONTENT em {font-style:italic;}
#BBSE-COMMERCE-CONTENT p {margin-bottom:24px;line-height:1.5}
#BBSE-COMMERCE-CONTENT pre {margin:20px 0;font-family:monospace, serif;-webkit-hyphens:none;-moz-hyphens:none;-ms-hyphens:none;hyphens:none;line-height:1.5;}

#BBSE-COMMERCE-CONTENT blockquote:before, #BBSE-COMMERCE-CONTENT blockquote:after {content:"";content:none;}
#BBSE-COMMERCE-CONTENT blockquote {margin:15px 15px 15px 0;padding:0 0 0 10px;color:#767676;font-style:italic;line-height:1.8em;border-left:4px solid #87CEEB}
#BBSE-COMMERCE-CONTENT blockquote em {font-style:normal;}
#BBSE-COMMERCE-CONTENT blockquote strong {font-weight:400;}

#BBSE-COMMERCE-CONTENT ul,
#BBSE-COMMERCE-CONTENT ol {margin:0 0 10px 20px;padding:0 0 0 20px;}
#BBSE-COMMERCE-CONTENT li > ul,
#BBSE-COMMERCE-CONTENT li > ol {margin:0 0 0 20px;padding:0 0 0 20px;}

#BBSE-COMMERCE-CONTENT ul,
#BBSE-COMMERCE-CONTENT ul > li {list-style:disc;line-height:1.5}

#BBSE-COMMERCE-CONTENT ol,
#BBSE-COMMERCE-CONTENT ol > li {list-style:decimal;line-height:1.5}

#BBSE-COMMERCE-CONTENT del {color:#767676;}
#BBSE-COMMERCE-CONTENT hr {background-color:rgba(0, 0, 0, 0.1);border:0;height:1px;margin-bottom:23px;}

#BBSE-COMMERCE-CONTENT .comment-content img,
#BBSE-COMMERCE-CONTENT .entry-content img,
#BBSE-COMMERCE-CONTENT .entry-summary img,
#BBSE-COMMERCE-CONTENT .wp-caption {max-width:100%;}

#BBSE-COMMERCE-CONTENT .comment-content img[height],
#BBSE-COMMERCE-CONTENT .entry-content img,
#BBSE-COMMERCE-CONTENT .entry-summary img{max-width:100%;height:auto;}

#BBSE-COMMERCE-CONTENT .comment-content img[height],
#BBSE-COMMERCE-CONTENT img[class*="align"],
#BBSE-COMMERCE-CONTENT img[class*="wp-image-"],
#BBSE-COMMERCE-CONTENT img[class*="attachment-"]{height:auto;max-width:100%;}

#BBSE-COMMERCE-CONTENT img.size-full,
#BBSE-COMMERCE-CONTENT img.size-large,
#BBSE-COMMERCE-CONTENT .wp-post-image,
#BBSE-COMMERCE-CONTENT .post-thumbnail img {height:auto;max-width:100%;}

#BBSE-COMMERCE-CONTENT embed,
#BBSE-COMMERCE-CONTENT iframe,
#BBSE-COMMERCE-CONTENT object,
#BBSE-COMMERCE-CONTENT video {margin-bottom:24px;max-width:100%;}

#BBSE-COMMERCE-CONTENT p > embed,
#BBSE-COMMERCE-CONTENT p > iframe,
#BBSE-COMMERCE-CONTENT p > object,
#BBSE-COMMERCE-CONTENT span > embed,
#BBSE-COMMERCE-CONTENT span > iframe,
#BBSE-COMMERCE-CONTENT span > object {margin-bottom:0;}
</style>
<script type="text/javascript" src="<?php echo home_url(); ?>/wp-includes/js/jquery/jquery.js"></script>
<script type="text/javascript">
function setCookie(name,value,expdate) {
	var expires = new Date();
	expires.setTime(expires.getTime() + 1000 * 3600 * 24 * 30);
	document.cookie = name + "=" + escape(value) +"; path=/; expires=" + expires.toGMTString();
}
function getCookie(keyname){
	cookies = document.cookie+';';
	index = cookies.indexOf(keyname,0);
	if(index!=-1){
		tmp = cookies.substring(index,cookies.length);
		searchidx1 = tmp.indexOf("=",0)+1;
        searchidx2 = tmp.indexOf(";",searchidx1);
		return (unescape(tmp.substring(searchidx1,searchidx2)));
	}
	else{
		return false;
	}
}
jQuery(document).ready(function(){

	//오늘 하루 보지 않기
	jQuery('input[name=noMoreShow]').change(function(){
		var name = jQuery(this).val();
		var check = getCookie(name);
		if(check != 'oneDayNoMore') {
			setCookie( name, "oneDayNoMore" , 1);
		}

		self.close();
	});

	//닫기
	jQuery('.layerClose').click(function() {
		self.close();
	});
});

</script>
</head>
<body>

<div class="layer-container forPopUp popupOn" style="display:block;">
	<div class="layerBox" style="margin:0;">
		<div class="boxTitle">
			<?php echo $pop['popup_title']; ?>
			<button class="layerClose"><span>닫기</span></button>
		</div>
		<div id="BBSE-COMMERCE-CONTENT" class="boxContent" style="width:100%;height:<?php echo $pop['popup_height']-70?>px;overflow-y:auto;"><?php echo $popup->config_editor; ?></div>
		<div class="noMoreShow">
			<input type="checkbox" name="noMoreShow" class="" value="winPopupName<?php echo $_GET['pidx']?>"> 오늘은 더 보고 싶지 않아요
		</div>
	</div>
</div>
</body>
</html>

<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

//if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$selectOption=$_REQUEST['selectOption'];
$selectTitle=$_REQUEST['selectTitle'];

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" class="wp-toolbar"  lang="ko-KR">
<head>
	<link rel='stylesheet' id='bbse-sc-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-tinymce-popup-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script type="text/javascript" src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL."js/admin-tinymce-common.js";?>"></script>
</script>
</head>
<body>
	<div id="bbse-tiny-option" class="scWrap">
		<div style="margin:10px 0 10px 20px;">
			<div class="titleH4">Shortcode 환경 설정 (<?php echo $selectTitle;?>)</div>
		</div>
		<div class="scBody" style="margin:0 auto;margin:0 20px 20px;">
			<form name="sc_opFrm" id="sc_opFrm">
				<input type="hidden" name="pluginUrl" id="pluginUrl" value="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>" />
				<input type="hidden" name="selectOption" id="selectOption" value="<?php echo $_REQUEST['selectOption'];?>" />
				<div class="borderBox" style="margin:0 0 20px;">
					- BBS e-Commerce Shortcode는 상품정보 작성에 필요한 디자인 형식을 제공합니다.<br/>
					- Shortcode의 종류에 따라 필요한 입력값을 정확히 입력하시면 다양한 디자인을 손쉽게 만드실 수 있습니다.<br/>
					<span style="color:#ED1C24;">- (*) 는 필수 입력사항으로 반드시 입력해 주시기 바랍니다.</span>
				</div>
				<?php require_once BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-tinymce-".$selectOption.".php";?>

			<div class="clearfix"></div>
			<div style="text-align:center;margin-top:40px;">
				<button type="button"class="button-bbse blue" onClick="shortcode_make();" style="width:150px;">Shortcode 추가</button></td>
			</div>
			</form>
		</div>
	</div>
</body>
</html>
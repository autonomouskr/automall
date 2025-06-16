<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$V = bbse_board_parameter_decryption($_GET['nType']);

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

$rows = $wpdb->get_row("select * from `".$wpdb->prefix."bbse_".$V['bname']."_board` where `no`='".$V['no']."'");

if(empty($rows->no)){
	echo "
		<script type='text/javascript'>
		alert('정상적인 접근이 아닙니다.');
		history.back();
		</script>";
	exit;
}

if($_GET['wrong'] == "1") $message = "에러 : 비밀번호를 바르게 입력해주세요.";
else if($_GET['wrong'] == "2") $message = "에러 : 비밀번호가 일치하지 않습니다.";

$boardInfo = $wpdb->get_row("select * from `".$wpdb->prefix."bbse_board` where `boardname`='".$V['bname']."'");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko-KR">
<head>
<meta http-equiv="imagetoolbar" content="no" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='viewport' content='width=device-width' />
<title>게시물 삭제</title>
<link rel="stylesheet" type="text/css" href="./style.css" />
<style>
body,p,h1,h2,h3,h4,h5,h6,ul,ol,li,dl,dt,dd,table,th,td,form,fieldset,legend,input,textarea,button,select{margin:0;padding:0}
body,input,textarea,select,button,table{font-family:'돋움',Dotum,AppleGothic,sans-serif;font-size:12px;color:#545454;}
img,fieldset{border:0}
ul,ol{list-style:none}
em,address{font-style:normal}
a{text-decoration:none;color:#666;}
a:hover,a:active,a:focus{text-decoration:underline;color:#666;}

.pw_box{position:relative;width:280px;margin:0;border:1px solid #dcdcdc;background:#f7f7f7;padding:60px;margin:0 auto;}
.pw_box fieldset{margin:0;padding:0;border:0}
.pw_box legend{visibility:hidden;position:absolute;top:0;left:0;width:1px;height:1px;font-size:0;line-height:0}
.pw_box h1 {padding:10px 0}
.pw_box .item{position:relative;}
.pw_box .i_label{display:none;display:block;position:static;top:9px;font:bold 11px Tahoma}
.pw_box .i_text{display:none;display:block;border:1px solid #b7b7b7;border-right-color:#e1e1e1;border-bottom-color:#e1e1e1;background:#fff;font:14px "돋움",Tahoma;height:44px;color:#767676;margin:3px 0;width:278px;vertical-align:middle;}
.pw_box .open_alert {color:#ed1c24;text-align:center;margin:15px 0;}
.pw_box .help{float:none;display:block;position:relative;margin:0;border:0;margin:15px 0; font-size:12px;}
.pw_box .help li{display:block;float:none;margin:0;padding:0 6px 0 8px;margin-bottom:5px; background:url(img/bl1.gif) no-repeat 0 5px;}
.pw_box .help a{display:block;float:none;padding:0;background:none;color:#767676;line-height:1;text-decoration:none}
.pw_box .help a:hover,
.pw_box .help a:active,
.pw_box .help a:focus{color:#333;text-decoration:underline}
.pw_btn_w {margin:15px 0;}
.pw_box_btn {
	cursor:pointer;
	font-size:14px;
	font-family:돋움;
	-moz-box-shadow:inset 0px 1px 0px 0px #bbdaf7;
	-webkit-box-shadow:inset 0px 1px 0px 0px #bbdaf7;
	box-shadow:inset 0px 1px 0px 0px #bbdaf7;
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #65adf5), color-stop(1, #378de5) );
	background:-moz-linear-gradient( center top, #65adf5 5%, #378de5 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#65adf5', endColorstr='#378de5');
	background-color:#65adf5;
	-webkit-border-top-left-radius:5px;
	-moz-border-radius-topleft:5px;
	border-top-left-radius:5px;
	-webkit-border-top-right-radius:5px;
	-moz-border-radius-topright:5px;
	border-top-right-radius:5px;
	-webkit-border-bottom-right-radius:5px;
	-moz-border-radius-bottomright:5px;
	border-bottom-right-radius:5px;
	-webkit-border-bottom-left-radius:5px;
	-moz-border-radius-bottomleft:5px;
	border-bottom-left-radius:5px;
	text-indent:0;
	border:1px solid #84bbf3;
	display:inline-block;
	color:#ffffff;
	font-weight:bold;
	font-style:normal;
	height:36px;
	line-height:36px;
	width:138px;
	margin-right:2px;
	text-decoration:none;
	text-align:center;
	text-shadow:1px 1px 0px #528ecc;
}
.pw_box_btn:hover {
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #378de5), color-stop(1, #65adf5) );
	background:-moz-linear-gradient( center top, #378de5 5%, #65adf5 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#378de5', endColorstr='#65adf5');
	background-color:#378de5;}
.pw_box_btn:active {
	position:relative;
	top:1px;
}
</style>
<script type="text/javascript" src="<?php echo includes_url()?>js/jquery/jquery.js"></script>
</head>
<body>
<?php if($rows->memnum == 0 && $curUserPermision != "administrator"){?>
<script type="text/javascript">
function check(){
	if(!jQuery("#pwd").val()){
		jQuery("#error_box").html("에러 : 비밀번호를 입력해주세요.");
		jQuery("#pwd").focus();
		return false;
	}
	if(confirm("삭제하시겠습니까?")){
		document.pwd_frm.submit();
	}
}
</script>

<form name="pwd_frm" method="post" action="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/delete_view.exec.php">
<input type="hidden" name="page_id" value="<?php echo $_GET['page_id']?>" />
<input type="hidden" name="bname" value="<?php echo $V['bname']?>" />
<input type="hidden" name="skin" value="<?php echo $boardInfo->skinname?>" />
<input type="hidden" name="mode" value="<?php echo $V['mode']?>" />
<input type="hidden" name="no" value="<?php echo $V['no']?>" />
<input type="hidden" name="page" value="<?php echo $V['page']?>" />
<input type="hidden" name="keyfield" value="<?php echo $V['keyfield']?>" />
<input type="hidden" name="keyword" value="<?php echo $V['keyword']?>" />
<input type="hidden" name="search_chk" value="<?php echo $V['search_chk']?>" />
<div>
	<div class="pw_box">
		<h1 class="tit">비밀번호 확인</h1>
		<fieldset>
			<legend>password</legend>
			<div class="item"><label><input name="pwd" id="pwd" type="password" class="i_text upw" value="" onfocus="this.value='';return true;" onkeypress="if(jQuery(this).val() != ''){jQuery('#error_box').empty();}" /></label></div>
			<p id="error_box" class="open_alert"><?php if(!empty($_GET['wrong'])) echo $message; else echo "비밀번호를 입력해주세요.";?></p>
			<p class="pw_btn_w"><a href="javascript:;" onclick="check();"><input title="확인" class="pw_box_btn" type="button" value="확인" /></a><a href="javascript:;" onclick="jQuery('#cancel_frm').submit();"><input title="취소" class="pw_box_btn" type="button" value="취소" /></a></p>
		</fieldset>
	</div>
</div>
</form>
<form name="cancel_frm" id="cancel_frm" method="post" action="<?php echo BBSE_BOARD_SITE_URL?>?page_id=<?php echo $_GET['page_id']?>&nType=<?php echo bbse_board_parameter_encryption($V['bname'], "view", $V['no'], $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])?>">
<input type="hidden" name="passcheck" value="1" />
</form>
<?php }else{?>
<form name="pwd_frm" method="post" action="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>proc/delete_view.exec.php">
<input type="hidden" name="page_id" value="<?php echo $_GET['page_id']?>" />
<input type="hidden" name="bname" value="<?php echo $V['bname']?>" />
<input type="hidden" name="skin" value="<?php echo $boardInfo->skinname?>" />
<input type="hidden" name="mode" value="<?php echo $V['mode']?>" />
<input type="hidden" name="no" value="<?php echo $V['no']?>" />
<input type="hidden" name="page" value="<?php echo $V['page']?>" />
<input type="hidden" name="keyfield" value="<?php echo $V['keyfield']?>" />
<input type="hidden" name="keyword" value="<?php echo $V['keyword']?>" />
<input type="hidden" name="search_chk" value="<?php echo $V['search_chk']?>" />
</form>
<script type="text/javascript">
if(confirm("삭제하시겠습니까?")){
	document.pwd_frm.submit();	
}else{
	location.href = "<?php echo BBSE_BOARD_SITE_URL?>?page_id=<?php echo $_GET['page_id']?>&nType=<?php echo bbse_board_parameter_encryption($V['bname'], $V['mode'], $V['no'], $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])?>";
}
</script>
<?php }?>
</body>
</html>
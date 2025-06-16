<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$img_size = getimagesize(BBSE_BOARD_UPLOAD_BASE_PATH."bbse-board/".$_GET['file']);
$img_width = $img_size[0];
$img_height = $img_size[1];

if($img_width >= 700){
	$img_width = 700;
	$width_per = round(700 / $img_size[0], 2);

}else{
	$img_width = $img_size[0];
	$width_per = 1;
}
$img_height = $img_height * $width_per;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko-KR">
<head>
<meta http-equiv="imagetoolbar" content="no" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>이미지 보기</title>
<script>
window.resizeTo(<?php echo $img_width + 11?>, <?php echo $img_height + 60?>);
</script>
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td align="center" valign="middle"><a href="javascript:;" onclick="self.close();"><img src="<?php echo BBSE_BOARD_UPLOAD_BASE_URL?>bbse-board/<?php echo $_GET['file']?>" border="0" width=<?php echo $img_width?> height=<?php echo $img_height?> alt="첨부파일" /></a></td>
</tr>
</table>
</body>
</html>
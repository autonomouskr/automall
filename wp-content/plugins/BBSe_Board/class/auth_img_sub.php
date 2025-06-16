<?php
if(!empty($_REQUEST['sid'])){
	session_id($_REQUEST['sid']);
}
session_start();

if(empty($_REQUEST['sid']) && empty($_SESSION['authKeySub'])){
	$_SESSION['authKeySub'] = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
}

header("Content-type: image/png");

$wdth        = 110;
$hght        = 65;
$img         = imagecreate($wdth, $hght);
$clr_bckgrnd = imagecolorallocate($img, 255, 255, 255);
$clr_frgrnd1 = imagecolorallocate($img, rand(10, 255), rand(10, 255), rand(10, 255));
$clr_frgrnd2 = imagecolorallocate($img, rand(10, 255), rand(10, 255), rand(10, 255));
$clr_frgrnd3 = imagecolorallocate($img, rand(10, 255), rand(10, 255), rand(10, 255));
$clr_frgrnd4 = imagecolorallocate($img, rand(10, 255), rand(10, 255), rand(10, 255));

imagefilledrectangle($img, 0, 0, $wdth, $hght, $clr_bckgrnd);

$clr_black = imagecolorallocate($img, 200, 200, 200);

// 원그리기 시작위치y 시작위치x 크기x 크기y
for($i = 0; $i <= $wdth; $i += 20){  // 가로 시작점
	$f = rand(0,$hght);  // 세로 시작점
	$b = rand(1, 3);  // 원의 크기
	ImageArc($img, $i, $f, $b, $b, 0, 360, $clr_black);  // 원 그리기
	ImageFill($img, $i, $f, $clr_black);  // 그린원에 색채우기
}

$num = 5;
for($i = $num; $i <= $wdth; $i += 18){  // 가로 선
	imageline($img, $i, 0, $i, $hght, $clr_black);
}
for($i = $num; $i <= $hght + 10; $i += 18){  // 세로 선
	imageline($img, 0, $i, $wdth, $i, $clr_black);
}

imagettftext($img, rand(20, 40), rand(0, 10),  5, rand(40, 50), $clr_frgrnd1, './arial.ttf', substr($_SESSION['authKeySub'], 0, 1));
imagettftext($img, rand(20, 40), rand(0, 10), 30, rand(40, 50), $clr_frgrnd2, './arial.ttf', substr($_SESSION['authKeySub'], 1, 1));
imagettftext($img, rand(20, 40), rand(0, 10), 55, rand(40, 50), $clr_frgrnd3, './arial.ttf', substr($_SESSION['authKeySub'], 2, 1));
imagettftext($img, rand(20, 40), rand(0, 10), 80, rand(40, 50), $clr_frgrnd4, './arial.ttf', substr($_SESSION['authKeySub'], 3, 1));

imagepng($img);
imagedestroy($img);
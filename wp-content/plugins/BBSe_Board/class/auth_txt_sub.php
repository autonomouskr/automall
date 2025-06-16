<?php
if(empty($_SESSION['authKeySub'])){
	$_SESSION['authKeySub'] = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
}
$auth_sub_txt1 = substr($_SESSION['authKeySub'], 0, 1);
$auth_sub_txt2 = substr($_SESSION['authKeySub'], 1, 1);
$auth_sub_txt3 = substr($_SESSION['authKeySub'], 2, 1);
$auth_sub_txt4 = substr($_SESSION['authKeySub'], 3, 1);

// 더미문자
$tmpSubVal = array("a", rand(0, 9), "b", "", "c", rand(0, 9), "d", rand(0, 9), "e", "g", "f", "", rand(0, 9), "", "", "", "", rand(0, 9));
$tmpSubVal2 = array_rand($tmpSubVal, 8);

// 색상설정
$fontSubColor = array("#CA0000", "#005DAB", "#019901", "#8801F8", "#FF8A01", "#000000");
$fontSubColorName = array("빨간색", "파란색", "녹색", "보라색", "노란색", "진한검정색");
//$fontSubColor2 = array_rand($fontSubColor, sizeof($fontSubColor));
$fontSubColor2 = array("0");

$auth_sub_fulltxt = $tmpSubVal[$tmpSubVal2[0]].$tmpSubVal[$tmpSubVal2[1]]."<font style='font-size:16px' color='".$fontSubColor[$fontSubColor2[0]]."'><b>".$auth_sub_txt1."</b></font>".$tmpSubVal[$tmpSubVal2[2]]."<font style='font-size:16px' color='".$fontSubColor[$fontSubColor2[0]]."'><b>".$auth_sub_txt2."</b></font>".$tmpSubVal[$tmpSubVal2[3]].$tmpSubVal[$tmpSubVal2[4]]."<font style='font-size:16px' color='".$fontSubColor[$fontSubColor2[0]]."'><b>".$auth_sub_txt3."</b></font>".$tmpSubVal[$tmpSubVal2[5]]."<font style='font-size:16px' color='".$fontSubColor[$fontSubColor2[0]]."'><b>".$auth_sub_txt4."</b></font>".$tmpSubVal[$tmpSubVal2[6]].$tmpSubVal[$tmpSubVal2[7]];

for($ii = 0; $ii < sizeof($fontSubColor); $ii++){
	if($fontSubColor[$fontSubColor2[0]] == $fontSubColor[$ii]){
		$fontSubColorType = "<font style='font-size:12px' color='".$fontSubColor[$fontSubColor2[0]]."'><b>".$fontSubColorName[$ii]."</b></font>";
	}
}
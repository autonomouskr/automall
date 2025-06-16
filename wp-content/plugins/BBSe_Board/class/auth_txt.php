<?php
if(empty($_SESSION['authKey'])){
	$_SESSION['authKey'] = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
}
$auth_txt1 = substr($_SESSION['authKey'], 0, 1);
$auth_txt2 = substr($_SESSION['authKey'], 1, 1);
$auth_txt3 = substr($_SESSION['authKey'], 2, 1);
$auth_txt4 = substr($_SESSION['authKey'], 3, 1);

// 더미문자
$tmpVal  = array('a', rand(0, 9), 'b', '', 'c', rand(0, 9), 'd', rand(0, 9), 'e', 'g', 'f', '', rand(0, 9), '', '', '', '', rand(0, 9));
$tmpVal2 = array_rand($tmpVal, 8);

// 색상설정
$fontColor     = array('#CA0000', '#005DAB', '#019901', '#8801F8', '#FF8A01', '#000000');
$fontColorName = array('빨간색',   '파란색',   '녹색',     '보라색',   '노란색',   '진한검정색');
$fontColor2    = array("0");

$auth_fulltxt = $tmpVal[$tmpVal2[0]].$tmpVal[$tmpVal2[1]].
'<span style="font-size:16px;color:'.$fontColor[$fontColor2[0]].'"><b>'.$auth_txt1.'</b></span>'.$tmpVal[$tmpVal2[2]].
'<span style="font-size:16px;color:'.$fontColor[$fontColor2[0]].'"><b>'.$auth_txt2.'</b></span>'.$tmpVal[$tmpVal2[3]].$tmpVal[$tmpVal2[4]].
'<span style="font-size:16px;color:'.$fontColor[$fontColor2[0]].'"><b>'.$auth_txt3.'</b></span>'.$tmpVal[$tmpVal2[5]].
'<span style="font-size:16px;color:'.$fontColor[$fontColor2[0]].'"><b>'.$auth_txt4.'</b></span>'.$tmpVal[$tmpVal2[6]].$tmpVal[$tmpVal2[7]];

for($ii = 0; $ii < sizeof($fontColor); $ii++){
	if($fontColor[$fontColor2[0]] == $fontColor[$ii]){
		$fontColorType = '<span style="font-size:12px;color:"'.$fontColor[$fontColor2[0]].'"><b>'.$fontColorName[$ii].'</b></span>';
	}
}
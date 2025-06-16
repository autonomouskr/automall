<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$zipline = file(BBSE_STYLESHOP_THEME_ABS_PATH."/postcode.dat");

while(list($key, $val) = each($zipline)){
	$varray = explode("|", $val);
	$string = $varray[4].$varray[5];
	if(preg_match("/(".$V['keyword'].")/", $string)) $zip[$key] = $val;
}

$i = 0;
echo "<ul>\n";
if(sizeof($zip) > 0){
	while(list(, $value) = each($zip)){
		$ziparray = explode("|", $value);
		$address[$i] = $ziparray[2]." ".$ziparray[3]." ".$ziparray[4];
		if(preg_match("/~/", trim($ziparray[5]))){
			$addr4[$i] = "";
		}else{
			$addr4[$i] = trim($ziparray[5]);
		}
		$view_addre[$i] = trim($ziparray[5]);
		$zipcode1[$i] = substr($ziparray[1], 0, 3);
		$zipcode2[$i] = substr($ziparray[1], 4, 3);
		$post_code[] = '\t<li data-code=\''.$zipcode1[$i].'|'.$zipcode2[$i].'\'>'.$address[$i].' '.$view_addre[$i].'</li>\n';
	}
	sort($post_code);  // 배열을 거꾸로..
	for($i = 0; $i < count($post_code); $i++) echo $post_code[$i];
}else{
	echo '\t<li>검색된 결과가 없습니다.</li>\n';
}
echo "</ul>";

?>
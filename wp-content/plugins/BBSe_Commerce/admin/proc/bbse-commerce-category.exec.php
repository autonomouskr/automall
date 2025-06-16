<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$V = $_POST;

if($V['tMode']=='insert' && trim($V['cUse'])  && trim($V['cName']) && trim($V['cUClass'])){
	$cName=trim($V['cName']);
	$jData=trim($V['jData']);
	$cUse=trim($V['cUse']);
	$cUClass=trim($V['cUClass']);
	$Cm="";

	$maxData = $wpdb->get_row("SELECT max(`depth_1`) AS max_depth1, max(`c_rank`) AS max_rank FROM `bbse_commerce_category`");
	$maxDepth1=$maxData->max_depth1+1;
	$maxRank=$maxData->max_rank+1;

	
	//$result = $wpdb->query("INSERT `bbse_commerce_category` SET `depth_1`='".$maxDepth1."', `c_name`='".$cName."', `c_use`='".$cUse."', `c_rank`='".$maxRank."'");
	$result = $wpdb->query("INSERT `bbse_commerce_category` SET `depth_1`='".$maxDepth1."', `c_name`='".$cName."', `c_use`='".$cUse."', `c_rank`='".$maxRank."' , `user_class`='".$cUClass."'");
	if($result){
		$data = $wpdb->get_row("SELECT `idx` FROM `bbse_commerce_category` WHERE  `depth_1`='".$maxDepth1."' AND `c_name`='".$cName."' AND `c_use`='".$cUse."' AND `c_rank`='".$maxRank."'");
		$newID = $data->idx;

		$cCode=$newID."K".$maxDepth1."F0S0T";
		$wpdb->query("UPDATE `bbse_commerce_category` SET `c_code`='".$cCode."' WHERE `idx`='".$newID."'");

		$str="<li class=\"dd-item\" data-id=\"".$newID."\">
			<div class=\"dd-handle\">".stripslashes($cName)."</div>
			<div class=\"select_btn\">
				<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon5.png\" onclick=\"select_input('".$newID."','".$cName."','".$cUse."','".$cUClass."');\" width=\"18\" height=\"18\" alt=\"선택\" title=\"선택\" style=\"cursor:pointer;\">&nbsp;&nbsp;<a href=\"".esc_url( home_url( '/' ) )."?bbseCat=".$newID."\" target=\"_blank\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon4.png\" width=\"18\" height=\"18\" alt=\"".$cName." 카테고리 미리보기\" title=\"".$cName." 카테고리 미리보기\"></a>
			</div>
		</li>";


		$jData=str_replace("\\","",$jData);
		
		if(!$jData) $jData="[]";
		elseif($jData!='[]') $Cm=",";

		$jData=substr($jData,0,(strlen($jData)-1)).$Cm."{\"id\":".$newID."}]";

		echo "success|||".$str."|||".$jData;
		exit;
	}
	else{
		echo "dbError";
		exit;
	}
}
elseif(($V['tMode']=='rank' || $V['tMode']=='rank-init') && trim($V['jData'])){
	$V['jData']=str_replace("\\","",str_replace("[]","",trim($V['jData'])));

	if($V['jData']){
		$jData=json_decode($V['jData']);

		$d1Num='0';
		$d2Num='0';
		$d3Num='0';
		$rankNum='0';

		for($i=0;$i<sizeof($jData);$i++){
			$d1Num++;
			$rankNum++;
			$cCode=$jData[$i]->id."K".$d1Num."F".$d2Num."S".$d3Num."T";
			$res_1 = $wpdb->query("UPDATE `bbse_commerce_category` SET `depth_1`='".$d1Num."', `depth_2`='".$d2Num."', `depth_3`='".$d3Num."', `c_code`='".$cCode."', `c_rank`='".$rankNum."' WHERE `idx`='".$jData[$i]->id."'");

			if(sizeof($jData[$i]->children)>0){
				for($j=0;$j<sizeof($jData[$i]->children);$j++){
					$d2Num++;
					$rankNum++;
					$cCode=$jData[$i]->children[$j]->id."K".$d1Num."F".$d2Num."S".$d3Num."T";
					$res_2 = $wpdb->query("UPDATE `bbse_commerce_category` SET `depth_1`='".$d1Num."', `depth_2`='".$d2Num."', `depth_3`='".$d3Num."', `c_code`='".$cCode."', `c_rank`='".$rankNum."' WHERE `idx`='".$jData[$i]->children[$j]->id."'");

					if(sizeof($jData[$i]->children[$j]->children)>0){
						for($k=0;$k<sizeof($jData[$i]->children[$j]->children);$k++){
							$d3Num++;
							$rankNum++;
							$cCode=$jData[$i]->children[$j]->children[$k]->id."K".$d1Num."F".$d2Num."S".$d3Num."T";
							$res_2 = $wpdb->query("UPDATE `bbse_commerce_category` SET `depth_1`='".$d1Num."', `depth_2`='".$d2Num."', `depth_3`='".$d3Num."', `c_code`='".$cCode."', `c_rank`='".$rankNum."' WHERE `idx`='".$jData[$i]->children[$j]->children[$k]->id."'");
						}
						$d3Num='0';

					}
				}
				$d2Num='0';

			}
		}

		echo "success";
		exit;
	}
	else{
		echo "nonData";
		exit;
	}
}
elseif($V['tMode']=='modify' && trim($V['tIdx']) && trim($V['cUse'])  && trim($V['cName']) && trim($V['cUClass'])){
	$tIdx=trim($V['tIdx']);
	$cName=trim($V['cName']);
	$cUse=trim($V['cUse']);
	$cUClass=trim($V['cUClass']);

	$nCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`='".$tIdx."'");
	if($nCnt < '1'){
		echo "dbError";
		exit;
	}

	$res = $wpdb->query("UPDATE `bbse_commerce_category` SET `c_name`='".$cName."', `c_use`='".$cUse."', `user_class`='".$cUClass."' WHERE `idx`='".$tIdx."'");

	$data = $wpdb->get_row("SELECT `idx`,`c_code` FROM `bbse_commerce_category` WHERE `idx`='".$tIdx."'");

	$str="<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon5.png\" onclick=\"select_input('".$tIdx."','".$cName."','".$cUse."','".$cUClass."');\" width=\"18\" height=\"18\" alt=\"선택\" title=\"선택\" style=\"cursor:pointer;\">&nbsp;&nbsp;<a href=\"".esc_url( home_url( '/' ) )."?bbseCat=".$data->idx."\" target=\"_blank\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon4.png\" width=\"18\" height=\"18\" alt=\"".$cName." 카테고리 미리보기\" title=\"".$cName." 카테고리 미리보기\"></a>";

	echo "success|||".$str;
	exit;
}
elseif($V['tMode']=='delete' && trim($V['tIdx'])){
	$tIdx=trim($V['tIdx']);
	$nCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`='".$tIdx."'");
	if($nCnt < '1'){
		echo "dbError";
		exit;
	}

	$data = $wpdb->get_row("SELECT * FROM `bbse_commerce_category` WHERE `idx`='".$tIdx."'");

	if($data->depth_1>'0' and $data->depth_2>'0' and $data->depth_3>'0'){
		$res = $wpdb->query("DELETE FROM `bbse_commerce_category` WHERE `depth_1`='".$data->depth_1."' AND `depth_2`='".$data->depth_2."' AND `depth_3`='".$data->depth_3."'");
	}
	elseif($data->depth_1>'0' and $data->depth_2>'0'){
		$res = $wpdb->query("DELETE FROM `bbse_commerce_category` WHERE `depth_1`='".$data->depth_1."' AND `depth_2`='".$data->depth_2."'");
	}
	elseif($data->depth_1>'0'){
		$res = $wpdb->query("DELETE FROM `bbse_commerce_category` WHERE `depth_1`='".$data->depth_1."' AND `idx`>'1'");
	}

	$nJson=bbse_commerce_get_category_json();

	if($res){
		echo "success|||".$nJson;
		exit;
	}
	else{
		echo "dbError";
		exit;
	}
}
else{
	echo "nonData";
	exit;
}
?>
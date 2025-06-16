<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

if($V['tMode']=="removeRecent") {
	if(!$V['rIdx'] || !$V['remoteIp']){
		echo "DataError";
		exit;
	}

	$remoteIp=$V['remoteIp'];

	$tCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_recent WHERE goods_idx='".$V['rIdx']."' AND remote_ip='".$V['remoteIp']."'");
	
	if($tCnt<='0'){
		echo "notExistRecent";
		exit;
	}

	$wpdb->query("DELETE FROM bbse_commerce_recent WHERE goods_idx='".$V['rIdx']."' AND remote_ip='".$V['remoteIp']."'");

	$rtnStr="";
	$rResult = $wpdb->get_results("SELECT * FROM bbse_commerce_recent WHERE remote_ip='".$remoteIp."' ORDER BY reg_date DESC LIMIT 3");
	$nCnt='0';
	foreach($rResult as $i=>$rData) {
		$rGoods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$rData->goods_idx."'");

		$rSoldout = goodsSoldoutCheck($rGoods); //품절체크
		if($rSoldout) continue;

		if($rGoods->goods_basic_img) $basicImg = wp_get_attachment_image_src($rGoods->goods_basic_img,$imgSizeKind);
		else{
			$imageList=explode(",",$rGoods->goods_add_img);
			if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
			else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
		}

		$rtnStr .="<li id=\"resent_list_".$rGoods->idx."\">
						<span onClick=\"remove_recent(".$rGoods->idx.",'".$remoteIp."');\" class=\"recent_remove\" title=\"최근본 상품 삭제\"></span>
						<a href=\"".home_url()."/?bbseGoods=".$rGoods->idx."\">
							<img src=\"".$basicImg['0']."\" alt=\"".$rGoods->goods_name."\" />
							<div class=\"hover\">
								<span>
									<em>".$rGoods->goods_name."</em>
									<span><strong>".number_format($rGoods->goods_price)."</strong>원</span>
								</span>
							</div>
						</a>
					</li>";

		$nCnt++;
	}

	if($rtnStr){
		echo "success|||".$nCnt."|||".$rtnStr;
		exit;
	}
	else{
		echo "emptyRecent";
		exit;
	}
}
else{
	echo "fail";
	exit;
}

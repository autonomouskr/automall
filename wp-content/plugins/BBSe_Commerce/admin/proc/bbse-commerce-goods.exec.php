<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb, $SITEMAPS;

$V = $_POST;

if($V['tMode']=='chStatus'){ // 일괄작업 && 휴지통 && 복사
	if(!$V['tStatus'] || ($V['tStatus']!='display' && $V['tStatus']!='hidden' && $V['tStatus']!='soldout' && $V['tStatus']!='trash' && $V['tStatus']!='nshopin' && $V['tStatus']!='nshopout' && $V['tStatus']!='npayin' && $V['tStatus']!='npayout' && $V['tStatus']!='empty-trash' && $V['tStatus']!='copy') || !$V['tData']){
		echo "fail";
		exit;
	}

	$goods_update_date=current_time('timestamp');

	if($V['tData']=='empty'){
		$sql="`goods_display`='trash'";
		$sqlOpt="`goods_display`='trash'";
	}
	else if($V['tStatus']=='copy' && $V['tData']){
		$data=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$V['tData']."'");
		$goods_reg_date=current_time('timestamp');
		$goods_code=$goods_reg_date."-".$V['tData'];

		$sql="'".$goods_code."', '[복사] ".addslashes($data->goods_name)."', '".$V['tStatus']."', '".$data->goods_cat_list."', '".$data->goods_add_img_cnt."', '".$data->goods_add_img."', '".$data->goods_basic_img."', '".$data->goods_icon_new."', '".$data->goods_icon_best."', '".addslashes($data->goods_description)."', '".addslashes($data->goods_detail)."', '".$data->goods_unique_code."', '".$data->goods_unique_code_display."', '".$data->goods_barcode."', '".$data->goods_barcode_display."', '".$data->goods_company."', '".$data->goods_company_display."', '".$data->goods_local."', '".$data->goods_local_display."', '".$data->goods_consumer_price."', '".$data->goods_price."', '".$data->goods_member_price."', '".$data->goods_count_flag."', '".$data->goods_count."', '".$data->goods_add_field."', '".$data->goods_option_basic."', '".$data->goods_option_add."', '".$data->goods_recommend_use."', '".$data->goods_recommend_list."', '".$data->goods_relation_use."', '".$data->goods_relation_list."', '".$data->goods_seo_use."', '".addslashes($data->goods_seo_title)."', '".addslashes($data->goods_seo_description)."', '".addslashes($data->goods_seo_keyword)."', '".$data->goods_earn_use."', '".$data->goods_earn."', '".$goods_update_date."', '".$goods_reg_date."'";
	}
	else{
		$checkIdx=explode(",",$V['tData']);
		$sql="";
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>0){
				if($sql) $sql .=" OR ";
				$sql .="idx='".$checkIdx[$i]."'";
			}
		}
	}

	if(!$sql){
		echo "fail";
		exit;
	}

	if($V['tStatus']=='empty-trash'){
		$sResult = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE ".$sql);
		$delSql_1=$delSql_2="";

		foreach($sResult as $i=>$sData) {
			if($delSql_1) $delSql_1 .=" OR ";
			$delSql_1 .="idx='".$sData->idx."'";

			if($delSql_2) $delSql_2 .=" OR";
			$delSql_2 .="goods_idx='".$sData->idx."'";
		}

		if($delSql_1){
			$res = $wpdb->query("DELETE FROM `bbse_commerce_goods` WHERE ".$delSql_1);
		}

		if($delSql_2){
			$res2 = $wpdb->query("DELETE FROM `bbse_commerce_goods_option` WHERE ".$delSql_2);
		}
	}
	else if($V['tStatus']=='copy'){
		$res = $wpdb->query("INSERT INTO bbse_commerce_goods (goods_code, goods_name, goods_display, goods_cat_list, goods_add_img_cnt, goods_add_img, goods_basic_img, goods_icon_new, goods_icon_best, goods_description, goods_detail, goods_unique_code, goods_unique_code_display, goods_barcode, goods_barcode_display, goods_company, goods_company_display, goods_local, goods_local_display, goods_consumer_price, goods_price, goods_member_price, goods_count_flag, goods_count, goods_add_field, goods_option_basic, goods_option_add, goods_recommend_use, goods_recommend_list, goods_relation_use, goods_relation_list, goods_seo_use, goods_seo_title, goods_seo_description, goods_seo_keyword, goods_earn_use, goods_earn, goods_update_date, goods_reg_date) VALUES (".$sql.")");

		$idx = $wpdb->insert_id;
		if($idx>'0'){
			$optResult = $wpdb->get_results("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$V['tData']."' ORDER BY goods_option_item_rank ASC");
			foreach($optResult as $i=>$optData) {
				$wpdb->query("INSERT INTO bbse_commerce_goods_option (goods_idx, goods_option_title, goods_option_item_overprice, goods_option_item_count, goods_option_item_unique_code, goods_option_item_display, goods_option_item_soldout,goods_option_item_rank) VALUES ('".$idx."','".addslashes($optData->goods_option_title)."','".$optData->goods_option_item_overprice."','".$optData->goods_option_item_count."','".$optData->goods_option_item_unique_code."','".$optData->goods_option_item_display."','".$optData->goods_option_item_soldout."','".$optData->goods_option_item_rank."')");
			}
		}
	}
	else if($V['tStatus']=='nshopin'){
		$res = $wpdb->query("UPDATE `bbse_commerce_goods` SET `goods_naver_shop`='on', `goods_update_date`='".$goods_update_date."' WHERE ".$sql);
	}
	else if($V['tStatus']=='nshopout'){
		$res = $wpdb->query("UPDATE `bbse_commerce_goods` SET `goods_naver_shop`='off', `goods_update_date`='".$goods_update_date."' WHERE ".$sql);
	}
	else if($V['tStatus']=='npayin'){
		$res = $wpdb->query("UPDATE `bbse_commerce_goods` SET `goods_naver_pay`='on' WHERE ".$sql);
	}
	else if($V['tStatus']=='npayout'){
		$res = $wpdb->query("UPDATE `bbse_commerce_goods` SET `goods_naver_pay`='off' WHERE ".$sql);
	}
	else{
		$res = $wpdb->query("UPDATE `bbse_commerce_goods` SET `goods_display`='".$V['tStatus']."', `goods_update_date`='".$goods_update_date."' WHERE ".$sql);
	}

	echo "success";
	if(is_object($SITEMAPS)) $SITEMAPS->tryWriteMapFile();
	exit;
}
else{
	echo "nonData";
	exit;
}
?>
<?php
header("Content-Type: text/html; charset=UTF-8");

if(!function_exists('bbse_nshop_strreplace')){
	function bbse_nshop_strreplace($str){
		$rtnStr="";
		if($str){
			$rtnStr=preg_replace('/\r\n|\r|\n/','',str_replace('"','',strip_tags(stripslashes($str))));
		}
		return $rtnStr;
	}
}

$nShopType=$wp->query_vars['bbseDBurl'];

$devCfgRes = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='delivery' limit 1");
$devCfg = unserialize($devCfgRes);

if($nShopType=='total'){ // 전체 EP : Engine Page
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE idx<>'' AND goods_naver_shop='on' AND goods_display<>'trash' AND goods_display<>'hidden' AND goods_display<>'soldout'");
	$gCnt='0';

	$feed=Array();
	$feed[]=array("id","title","price_pc","price_mobile","normal_price","link","mobile_link","image_link","add_image_link","category_name1","category_name2","category_name3","category_name4","naver_category","naver_product_id","condition","import_flag","parallel_import","order_made","product_flag","adult","goods_type","barcode","manufacture_define_number","model_number","brand","maker","origin","card_event","event_words","coupon","partner_coupon_download","interest_free_event","point","installation_costs","search_tag","group_id","vendor_id","coordi_id","minimum_purchase_quantity","review_count","shipping","delivery_grade","delivery_detail","attribute","option_detail","seller_id","age_group","gender");

	foreach($result as $i=>$goods) {
		$tmpFeed=array();
		$rSoldout = goodsSoldoutCheck($goods); //품절체크
		if($rSoldout) continue;

		$imageList=explode(",",$goods->goods_add_img);
		$imgUrl="";

		if($goods->goods_basic_img){
			$basicImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage7");
			$tmpImgUrl=explode("/",$basicImg['0']);
			$tmpImgUrl[sizeof($tmpImgUrl)-1]=urlencode($tmpImgUrl[sizeof($tmpImgUrl)-1]);
			$imgUrl=implode("/", $tmpImgUrl);;
		}
		else{
			if(sizeof($imageList)>'0'){
				$basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
				$tmpImgUrl=explode("/",$basicImg['0']);
				$tmpImgUrl[sizeof($tmpImgUrl)-1]=urlencode($tmpImgUrl[sizeof($tmpImgUrl)-1]);
				$imgUrl=implode("/", $tmpImgUrl);;
			}
			else $imgUrl=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
		}

		$goodsCate=bbse_commerce_nshop_category($goods->goods_cat_list);
		
		/*id : 상품 코드*/
		$tmpFeed[]=$goods->goods_code;
		
		/*title : 상품명*/
		$tmpFeed[]=bbse_nshop_strreplace($goods->goods_name);

		/*price_pc : 상품 가격*/
		$tmpFeed[]=$goods->goods_price;

		/*price_mobile : 모바일 상품가격*/
		$tmpFeed[]=$goods->goods_price;

		/*normal_price : 정가 (3.0)*/
		$tmpFeed[]=$goods->goods_consumer_price;

		/*link : 상품 URL*/
		$tmpFeed[]=home_url('/')."?bbseGoods=".$goods->idx;

		/*mobile_link : 상품모바일URL (3.0)*/
		$tmpFeed[]=home_url('/')."?bbseGoods=".$goods->idx;

		/*image_link : 상품 이미지 URL*/
		$tmpFeed[]=str_replace("https","http",$imgUrl);

		/*add_image_link : 추가이미지URL  (3.0)*/
		$tmpFeed[]="";

		/*category_name : 상품 카테고리 명 1 ~ 4*/
		$tmpFeed[]=($goodsCate['depth_1'])?bbse_nshop_strreplace($goodsCate['depth_1']):"";
		$tmpFeed[]=($goodsCate['depth_2'])?bbse_nshop_strreplace($goodsCate['depth_2']):"";
		$tmpFeed[]=($goodsCate['depth_3'])?bbse_nshop_strreplace($goodsCate['depth_3']):"";
		$tmpFeed[]="";

		/*naver_category : 네이버 카테고리 (3.0)*/
		$tmpFeed[]="";

		/*naver_product_id : 가격비교페이지ID (3.0)*/
		$tmpFeed[]="";

		/*condition : 상품상태 (3.0) : 신상품,  중고, 리퍼, 전시, 반품, 스크래치 텍스트값만 허용*/
		$tmpFeed[]="신상품";

		/*import_flag : 해외구매대행여부 (3.0)*/
		$tmpFeed[]="";

		/*parallel_import : 병행수입여부 (3.0)*/
		$tmpFeed[]="";

		/*order_made : 주문제작상품여부 (3.0)*/
		$tmpFeed[]="";

		/*product_flag : 판매방식구분 (3.0) : 도매, 렌탈, 대여, 할부, 예약판매, 구매대행으로표기하며, 해당 사항이 없는 경우 생략 */
		$tmpFeed[]="";

		/*adult : 미성년자구매불가상품여부 (3.0)*/
		$tmpFeed[]="";

		/*goods_type : 상품구분 : 백화점상품-DP, 홈쇼핑상품-HS, 면세점상품-DF, 마트상품-MA */
		$tmpFeed[]="";

		/*barcode : 바코드*/
		$tmpFeed[]=$goods->goods_barcode;

		/*manufacture_define_number  : 제품코드*/
		$tmpFeed[]=$goods->goods_code;

		/*model_number : 모델명*/
		$tmpFeed[]="";

		/*brand  : 브랜드명*/
		$tmpFeed[]="";

		/*maker : 제조사*/
		$tmpFeed[]=bbse_nshop_strreplace($goods->goods_company);

		/*origin : 원산지*/
		$tmpFeed[]=bbse_nshop_strreplace($goods->goods_local);

		/*card_event : 카드명/카드할인가격 (1개만 표시)*/
		$tmpFeed[]="";

		/*event_words : 이벤트*/
		$tmpFeed[]="";

		/*coupon : 일반/제휴쿠폰 */
		$tmpFeed[]="";

		/*partner_coupon_download : 쿠폰다운로드필요 여부*/
		$tmpFeed[]="";

		/*interest_free_event : 카드무이자할부정보*/
		$tmpFeed[]="";

		/*point : 포인트*/
		if($goods->goods_earn_use=='on' && $goods->goods_earn>'0') $tmpFeed[]=$goods->goods_earn;
		else $tmpFeed[]="";

		/*installation_costs  : 별도설치비유무*/
		$tmpFeed[]="";

		/*search_tag : 검색태그 (3.0) : 띄어쓰기 없이 | (Vertical bar)로 구분하여 입력*/
		if($goods->goods_seo_use=='on' && $goods->goods_seo_keyword){
			$tmpTag=explode(",",str_replace("|",",",str_replace(" ","",bbse_nshop_strreplace($goods->goods_seo_keyword))));
			$tmpFeed[]=implode("|",$tmpTag);
		}
		else $tmpFeed[]="";

		/*group_id : Group ID (3.0)*/
		$tmpFeed[]="";

		/*vendor_id : 제휴사상품ID  (3.0)*/
		$tmpFeed[]="";

		/*coordi_id : 코디상품ID  (3.0)*/
		$tmpFeed[]="";

		/*minimum_purchase_quantity : 최소구매수량 (3.0)*/
		$tmpFeed[]="";

		/*review_count : 상품평 개수*/
		$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE goods_idx='".$goods->idx."'");
		$tmpFeed[]=$reviewCnt;

		/*shipping : 배송비 (착불->-1, 선불->배송비, 조건부 조건금액 이상->0, 조건부 조건금액 미만->배송비)*/
		if($devCfg['delivery_charge_type']=='free') $tmpFeed[]="0"; // 무료 배송
		else{
			if(!$devCfg['delivery_charge_payment'] || $devCfg['delivery_charge_payment']=='advance'){ // 선불결제
				if(!$devCfg['condition_free_use'] || $devCfg['condition_free_use']=='off') $tmpFeed[]=$devCfg['delivery_charge']; // 조건부 무료배송 : 사용안함
				else{// 조건부 무료배송 : 사용함
					if($goods->goods_price>=$devCfg['total_pay']) $tmpFeed[]="0"; // 상품가격 >= 조건금액
					else $tmpFeed[]=$devCfg['delivery_charge']; // 상품가격 < 조건금액
				}
			}
			else $tmpFeed[]="-1"; // 후불(착불)결제
		}

		/*delivery_grade : 차등배송비 여부*/
		/*delivery_detail : 차등배송비 내용*/
		if($devCfg['localCnt']<='0') $localCnt='0';
		else $localCnt=$devCfg['localCnt'];
		$localChargeFlag=false;
		$localCharge="";
		for($k=1;$k<=$localCnt;$k++){
			if($devCfg['local_charge_'.$k.'_use']=='on' && $devCfg['local_charge_pay_'.$k]>'0' && $devCfg['local_charge_list_'.$k.'_idx']){
				$localChargeFlag=true;
				if($localCharge) $localCharge .="/";

				$aCnt=sizeof($devCfg['local_charge_list_'.$k.'_idx']);
				$localName="";
				for($c=0;$c<$aCnt;$c++){
					if($localName) $localName .=",";
					$localName .=bbse_nshop_strreplace($devCfg['local_charge_list_'.$k.'_name'][$c]);
				}

				$localCharge .=$localName." ".$devCfg['local_charge_pay_'.$k]."원 추가";
			}
		}

		if($localChargeFlag==true && $localCharge){
			$tmpFeed[]="Y";
			$tmpFeed[]=$localCharge;
		}
		else{
			$tmpFeed[]="";
			$tmpFeed[]="";
		}

		/*attribute : 상품속성 (3.0)*/
		$tmpFeed[]="";

		/*option_detail : 구매옵션*/
		$tmpFeed[]="";

		/*seller_id : 셀러 ID(오픈마켓에 한함)*/
		$tmpFeed[]="";

		/*age_group : 주이용고객층 (3.0) : 유아, 아동, 청소년, 성인 이외의 값은 처리되지 않으며, 값이없는 경우에는 ‘성인’으로 처리*/
		$tmpFeed[]="";

		/*gender : 성별 (3.0) : 남성, 여성, 남녀공용 값만 허용되며, 성별을 정할 필요가 없는 상품(가전제품등)에는 생략 가능*/
		$tmpFeed[]="";

		$feed[]=$tmpFeed;

		$gCnt++;
	}

	if($gCnt>'0'){
		//header('Content-type: text/tab-separated-values');
		//header("Content-Disposition: attachment;filename=bingproductfeed.txt");

		foreach ($feed as $fields) {
			$fields=str_replace('"','',$fields);
			echo implode("\t",$fields)."\n";
		}
	}
}
elseif($nShopType=='summary'){ // 요약 EP : Engine Page
	$currentTime=current_time('timestamp');
	$tHour=date("H",$currentTime);

	if($tHour<='08'){ // 1) 08시 요약 EP(요약 EP 첫 번째 수신 ) : 전체 EP 생성 후 업데이트 된 데이터
		$startTime=mktime('20','00','01',date("m",$currentTime),date("d",$currentTime)-1,date("Y",$currentTime));
	}
	elseif($tHour>'08' && $tHour<='10'){ // 2) 10시 요약 EP : 08시 요약EP + 08~10시 업데이트 데이터
		$startTime=mktime('08','00','01',date("m",$currentTime),date("d",$currentTime),date("Y",$currentTime));
	}
	elseif($tHour>'10' && $tHour<='12'){ // 3) 12시 요약 EP : 10시 요약EP + 10~12시 업데이트 데이터
		$startTime=mktime('10','00','01',date("m",$currentTime),date("d",$currentTime),date("Y",$currentTime));
	}
	elseif($tHour>'12' && $tHour<='14'){ // 4) 14시 요약 EP : 12시 요약EP + 12~14시 업데이트 데이터
		$startTime=mktime('12','00','01',date("m",$currentTime),date("d",$currentTime),date("Y",$currentTime));
	}
	elseif($tHour>'14' && $tHour<='16'){ // 5) 16시 요약 EP : 14시 요약EP + 14~16시 업데이트 데이터
		$startTime=mktime('14','00','01',date("m",$currentTime),date("d",$currentTime),date("Y",$currentTime));
	}
	elseif($tHour>'16' && $tHour<='18'){ // 6) 18시 요약 EP : 16시 요약EP + 16~18시 업데이트 데이터
		$startTime=mktime('16','00','01',date("m",$currentTime),date("d",$currentTime),date("Y",$currentTime));
	}
	elseif($tHour>'16' && $tHour<='18'){ // 7) 20시 요약 EP : 18시 요약EP + 18~20시 업데이트 데이터
		$startTime=mktime('18','00','01',date("m",$currentTime),date("d",$currentTime),date("Y",$currentTime));
	}

	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE idx<>'' AND goods_naver_shop='on' AND goods_display<>'trash' AND goods_update_date>='".$startTime."' AND goods_update_date<='".$currentTime."'");

	$gCnt='0';

	$feed=Array();
	$feed[]=array("id","title","price_pc","price_mobile","normal_price","link","mobile_link","image_link","add_image_link","category_name1","category_name2","category_name3","category_name4","naver_category","naver_product_id","condition","import_flag","parallel_import","order_made","product_flag","adult","goods_type","barcode","manufacture_define_number","model_number","brand","maker","origin","card_event","event_words","coupon","partner_coupon_download","interest_free_event","point","installation_costs","search_tag","group_id","vendor_id","coordi_id","minimum_purchase_quantity","review_count","shipping","delivery_grade","delivery_detail","attribute","option_detail","seller_id","age_group","gender","class","update_time");

	foreach($result as $i=>$goods) {
		$tmpFeed=array();

		$imageList=explode(",",$goods->goods_add_img);
		$imgUrl="";

		if($goods->goods_basic_img){
			$basicImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage7");
			$tmpImgUrl=explode("/",$basicImg['0']);
			$tmpImgUrl[sizeof($tmpImgUrl)-1]=urlencode($tmpImgUrl[sizeof($tmpImgUrl)-1]);
			$imgUrl=implode("/", $tmpImgUrl);;
		}
		else{
			if(sizeof($imageList)>'0'){
				$basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
				$tmpImgUrl=explode("/",$basicImg['0']);
				$tmpImgUrl[sizeof($tmpImgUrl)-1]=urlencode($tmpImgUrl[sizeof($tmpImgUrl)-1]);
				$imgUrl=implode("/", $tmpImgUrl);;
			}
			else $imgUrl=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
		}

		$goodsCate=bbse_commerce_nshop_category($goods->goods_cat_list);

		/*id : 상품 코드*/
		$tmpFeed[]=$goods->goods_code;

		/*title : 상품명*/
		$tmpFeed[]=bbse_nshop_strreplace($goods->goods_name);

		/*price_pc : 상품 가격*/
		$tmpFeed[]=$goods->goods_price;

		/*price_mobile : 모바일 상품가격*/
		$tmpFeed[]=$goods->goods_price;

		/*normal_price : 정가 (3.0)*/
		$tmpFeed[]=$goods->goods_consumer_price;

		/*link : 상품 URL*/
		$tmpFeed[]=home_url('/')."?bbseGoods=".$goods->idx;

		/*mobile_link : 상품모바일URL (3.0)*/
		$tmpFeed[]=home_url('/')."?bbseGoods=".$goods->idx;

		/*image_link : 상품 이미지 URL*/
		$tmpFeed[]=str_replace("https","http",$imgUrl);

		/*add_image_link : 추가이미지URL  (3.0)*/
		$tmpFeed[]="";

		/*category_name : 상품 카테고리 명 1 ~ 4*/
		$tmpFeed[]=($goodsCate['depth_1'])?bbse_nshop_strreplace($goodsCate['depth_1']):"";
		$tmpFeed[]=($goodsCate['depth_2'])?bbse_nshop_strreplace($goodsCate['depth_2']):"";
		$tmpFeed[]=($goodsCate['depth_3'])?bbse_nshop_strreplace($goodsCate['depth_3']):"";
		$tmpFeed[]="";

		/*naver_category : 네이버 카테고리 (3.0)*/
		$tmpFeed[]="";

		/*naver_product_id : 가격비교페이지ID (3.0)*/
		$tmpFeed[]="";

		/*condition : 상품상태 (3.0) : 신상품,  중고, 리퍼, 전시, 반품, 스크래치 텍스트값만 허용*/
		$tmpFeed[]="신상품";

		/*import_flag : 해외구매대행여부 (3.0)*/
		$tmpFeed[]="";

		/*parallel_import : 병행수입여부 (3.0)*/
		$tmpFeed[]="";

		/*order_made : 주문제작상품여부 (3.0)*/
		$tmpFeed[]="";

		/*product_flag : 판매방식구분 (3.0) : 도매, 렌탈, 대여, 할부, 예약판매, 구매대행으로표기하며, 해당 사항이 없는 경우 생략 */
		$tmpFeed[]="";

		/*adult : 미성년자구매불가상품여부 (3.0)*/
		$tmpFeed[]="";

		/*goods_type : 상품구분 : 백화점상품-DP, 홈쇼핑상품-HS, 면세점상품-DF, 마트상품-MA */
		$tmpFeed[]="";

		/*barcode : 바코드*/
		$tmpFeed[]=$goods->goods_barcode;

		/*manufacture_define_number  : 제품코드*/
		$tmpFeed[]=$goods->goods_code;

		/*model_number : 모델명*/
		$tmpFeed[]="";

		/*brand  : 브랜드명*/
		$tmpFeed[]="";

		/*maker : 제조사*/
		$tmpFeed[]=bbse_nshop_strreplace($goods->goods_company);

		/*origin : 원산지*/
		$tmpFeed[]=bbse_nshop_strreplace($goods->goods_local);

		/*card_event : 카드명/카드할인가격 (1개만 표시)*/
		$tmpFeed[]="";

		/*event_words : 이벤트*/
		$tmpFeed[]="";

		/*coupon : 일반/제휴쿠폰 */
		$tmpFeed[]="";

		/*partner_coupon_download : 쿠폰다운로드필요 여부*/
		$tmpFeed[]="";

		/*interest_free_event : 카드무이자할부정보*/
		$tmpFeed[]="";

		/*point : 포인트*/
		if($goods->goods_earn_use=='on' && $goods->goods_earn>'0') $tmpFeed[]=$goods->goods_earn;
		else $tmpFeed[]="";

		/*installation_costs  : 별도설치비유무*/
		$tmpFeed[]="";

		/*search_tag : 검색태그 (3.0) : 띄어쓰기 없이 | (Vertical bar)로 구분하여 입력*/
		if($goods->goods_seo_use=='on' && $goods->goods_seo_keyword){
			$tmpTag=explode(",",str_replace("|",",",str_replace(" ","",bbse_nshop_strreplace($goods->goods_seo_keyword))));
			$tmpFeed[]=implode("|",$tmpTag);
		}
		else $tmpFeed[]="";

		/*group_id : Group ID (3.0)*/
		$tmpFeed[]="";

		/*vendor_id : 제휴사상품ID  (3.0)*/
		$tmpFeed[]="";

		/*coordi_id : 코디상품ID  (3.0)*/
		$tmpFeed[]="";

		/*minimum_purchase_quantity : 최소구매수량 (3.0)*/
		$tmpFeed[]="";

		/*review_count : 상품평 개수*/
		$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE goods_idx='".$goods->idx."'");
		$tmpFeed[]=$reviewCnt;

		/*shipping : 배송비 (착불->-1, 선불->배송비, 조건부 조건금액 이상->0, 조건부 조건금액 미만->배송비)*/
		if($devCfg['delivery_charge_type']=='free') $tmpFeed[]="0"; // 무료 배송
		else{
			if(!$devCfg['delivery_charge_payment'] || $devCfg['delivery_charge_payment']=='advance'){ // 선불결제
				if(!$devCfg['condition_free_use'] || $devCfg['condition_free_use']=='off') $tmpFeed[]=$devCfg['delivery_charge']; // 조건부 무료배송 : 사용안함
				else{// 조건부 무료배송 : 사용함
					if($goods->goods_price>=$devCfg['total_pay']) $tmpFeed[]="0"; // 상품가격 >= 조건금액
					else $tmpFeed[]=$devCfg['delivery_charge']; // 상품가격 < 조건금액
				}
			}
			else $tmpFeed[]="-1"; // 후불(착불)결제
		}

		/*delivery_grade : 차등배송비 여부*/
		/*delivery_detail : 차등배송비 내용*/
		if($devCfg['localCnt']<='0') $localCnt='0';
		else $localCnt=$devCfg['localCnt'];
		$localChargeFlag=false;
		$localCharge="";
		for($k=1;$k<=$localCnt;$k++){
			if($devCfg['local_charge_'.$k.'_use']=='on' && $devCfg['local_charge_pay_'.$k]>'0' && $devCfg['local_charge_list_'.$k.'_idx']){
				$localChargeFlag=true;
				if($localCharge) $localCharge .="/";

				$aCnt=sizeof($devCfg['local_charge_list_'.$k.'_idx']);
				$localName="";
				for($c=0;$c<$aCnt;$c++){
					if($localName) $localName .=",";
					$localName .=bbse_nshop_strreplace($devCfg['local_charge_list_'.$k.'_name'][$c]);
				}

				$localCharge .=$localName." ".$devCfg['local_charge_pay_'.$k]."원 추가";
			}
		}

		if($localChargeFlag==true && $localCharge){
			$tmpFeed[]="Y";
			$tmpFeed[]=$localCharge;
		}
		else{
			$tmpFeed[]="";
			$tmpFeed[]="";
		}

		/*attribute : 상품속성 (3.0)*/
		$tmpFeed[]="";

		/*option_detail : 구매옵션*/
		$tmpFeed[]="";

		/*seller_id : 셀러 ID(오픈마켓에 한함)*/
		$tmpFeed[]="";

		/*age_group : 주이용고객층 (3.0) : 유아, 아동, 청소년, 성인 이외의 값은 처리되지 않으며, 값이없는 경우에는 ‘성인’으로 처리*/
		$tmpFeed[]="";

		/*gender : 성별 (3.0) : 남성, 여성, 남녀공용 값만 허용되며, 성별을 정할 필요가 없는 상품(가전제품등)에는 생략 가능*/
		$tmpFeed[]="";

		/*class : 상품품절여부 : I  (신규상품) / U (업데이트상품) / D (품절상품) */
		$rSoldout = goodsSoldoutCheck($goods); //품절체크

		if($rSoldout || $goods->goods_display=='hidden'){
			$tmpFeed[]="D"; // 품절
			/*update_time : 상품정보 생성 시각*/
			$tmpFeed[]=date("Y-m-d H:i:s",$currentTime);
		}
		else{
			if($goods->goods_reg_date==$goods->goods_update_date) $tmpFeed[]="I";
			else $tmpFeed[]="U";

			/*utime : 상품정보 생성 시각*/
			$tmpFeed[]=date("Y-m-d H:i:s",$currentTime);
		}

		$feed[]=$tmpFeed;
		$gCnt++;
	}

	if($gCnt>'0'){
		//header('Content-type: text/tab-separated-values');
		//header("Content-Disposition: attachment;filename=bingproductfeed.txt");

		foreach ($feed as $fields) {
			$fields=str_replace('"','',$fields);
			echo implode("\t",$fields)."\n";
		}
	}
}
?>
<?php
header("Content-Type: text/html; charset=EUC-KR");

$nShopType=$wp->query_vars['bbseDBurl'];

$devCfgRes = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='delivery' limit 1");
$devCfg = unserialize($devCfgRes);

if($nShopType=='total'){ // 전체 EP : Engine Page
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE idx<>'' AND goods_naver_shop='on' AND goods_display<>'trash' AND goods_display<>'hidden' AND goods_display<>'soldout'");
	$printStr="";
	$gCnt='0';
	foreach($result as $i=>$goods) {
		$rSoldout = goodsSoldoutCheck($goods); //품절체크
		if($rSoldout) continue;

		$imageList=explode(",",$goods->goods_add_img);
		$imgUrl="";
		if($goods->goods_basic_img){
			$basicImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage7");
			$tmpImgUrl=explode("/",$basicImg['0']);
			$tmpImgUrl[sizeof($tmpImgUrl)-1]=urlencode(iconv("UTF-8","EUC-KR",$tmpImgUrl[sizeof($tmpImgUrl)-1]));
			$imgUrl=implode("/", $tmpImgUrl);;
		}
		else{
			if(sizeof($imageList)>'0'){
				$basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
				$tmpImgUrl=explode("/",$basicImg['0']);
				$tmpImgUrl[sizeof($tmpImgUrl)-1]=urlencode(iconv("UTF-8","EUC-KR",$tmpImgUrl[sizeof($tmpImgUrl)-1]));
				$imgUrl=implode("/", $tmpImgUrl);;
			}
			else $imgUrl=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
		}

		$goodsCate=bbse_commerce_nshop_category($goods->goods_cat_list);
		
		/*begin : 상품 시작*/
		$printStr .="<<<begin>>>\n";

		/*mapid : 상품 코드*/
		$printStr .="<<<mapid>>>".iconv("UTF-8","EUC-KR",$goods->goods_code)."\n";
		
		/*pname : 상품명*/
		$printStr .="<<<pname>>>".iconv("UTF-8","EUC-KR",$goods->goods_name)."\n";

		/*price : 상품 가격*/
		$printStr .="<<<price>>>".iconv("UTF-8","EUC-KR",$goods->goods_price)."\n";

		/*pgurl : 상품 URL*/
		$printStr .="<<<pgurl>>>".home_url('/')."?bbseGoods=".$goods->idx."\n";

		/*igurl : 상품 이미지 URL*/
		$printStr .="<<<igurl>>>".$imgUrl."\n";

		/*cate : 상품 카테고리 명 1 ~ 4*/
		$printStr .="<<<cate1>>>".iconv("UTF-8","EUC-KR",$goodsCate['depth_1'])."\n";
		$printStr .="<<<cate2>>>".iconv("UTF-8","EUC-KR",$goodsCate['depth_2'])."\n";
		$printStr .="<<<cate3>>>".iconv("UTF-8","EUC-KR",$goodsCate['depth_3'])."\n";
		$printStr .="<<<cate4>>>\n";

		/*caid : 상품 카테고리 ID 1 ~ 4*/
		$printStr .="<<<caid1>>>".iconv("UTF-8","EUC-KR",$goodsCate['idx_1'])."\n";
		$printStr .="<<<caid2>>>".iconv("UTF-8","EUC-KR",$goodsCate['idx_2'])."\n";
		$printStr .="<<<caid3>>>".iconv("UTF-8","EUC-KR",$goodsCate['idx_3'])."\n";
		$printStr .="<<<caid4>>>\n";

		/*model : 모델명*/
		$printStr .="<<<model>>>\n";

		/*model : 브랜드명*/
		$printStr .="<<<brand>>>\n";

		/*maker : 제조사*/
		$printStr .="<<<maker>>>".iconv("UTF-8","EUC-KR",$goods->goods_company)."\n";

		/*origi : 원산지*/
		$printStr .="<<<origi>>>".iconv("UTF-8","EUC-KR",$goods->goods_local)."\n";

		/*deliv : 배송비 (착불->-1, 선불->배송비, 조건부 조건금액 이상->0, 조건부 조건금액 미만->배송비)*/
		if($devCfg['delivery_charge_type']=='free') $printStr .="<<<deliv>>>0\n"; // 무료 배송
		else{
			if(!$devCfg['delivery_charge_payment'] || $devCfg['delivery_charge_payment']=='advance'){ // 선불결제
				if(!$devCfg['condition_free_use'] || $devCfg['condition_free_use']=='off') $printStr .="<<<deliv>>>".iconv("UTF-8","EUC-KR",$devCfg['delivery_charge'])."\n"; // 조건부 무료배송 : 사용안함
				else{// 조건부 무료배송 : 사용함
					if($goods->goods_price>=$devCfg['total_pay']) $printStr .="<<<deliv>>>0\n"; // 상품가격 >= 조건금액
					else $printStr .="<<<deliv>>>".$devCfg['delivery_charge']."\n"; // 상품가격 < 조건금액
				}
			}
			else $printStr .="<<<deliv>>>-1\n"; // 후불(착불)결제
		}

		/*event : 이벤트*/
		$printStr .="<<<event>>>\n";

		/*coupo : 쿠폰*/
		$printStr .="<<<coupo>>>\n";

		/*pcard : 카드 무이자 할부 정보*/
		$printStr .="<<<pcard>>>\n";

		/*point : 포인트*/
		if($goods->goods_earn_use=='on' && $goods->goods_earn>'0') $printStr .="<<<point>>>".iconv("UTF-8","EUC-KR",$goods->goods_earn)."\n";
		else $printStr .="<<<point>>>0\n";

		/*mvurl : 동영상 상품 여부*/
		$printStr .="<<<mvurl>>>N\n"; 

		/*selid : 셀러 ID(오픈마켓에 한함)*/
		$printStr .="<<<selid>>>\n";

		/*barcode : 바코드*/
		$printStr .="<<<barcode>>>".iconv("UTF-8","EUC-KR",$goods->goods_barcode)."\n";

		/*cardn : 이벤트 진행중이 카드(1개만 표시)*/
		$printStr .="<<<cardn>>>\n";

		/*cardp : 카드 할인가격*/
		$printStr .="<<<cardp>>>\n";

		/*mpric : 모바일 상품가격*/
		$printStr .="<<<mpric>>>".iconv("UTF-8","EUC-KR",$goods->goods_price)."\n";

		/*revct : 상품평 개수*/
		$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE goods_idx='".$goods->idx."'");
		$printStr .="<<<revct>>>".$reviewCnt."\n";

		/*ecoyn : 친환경 인증여부*/
		$printStr .="<<<ecoyn>>>\n";

		/*econm : 친환경 인증분류*/
		$printStr .="<<<econm>>>\n";

		/*gtype : 상품 구분(해외쇼핑 상품: FS, 백화점 상품: DP, 홈쇼핑 상품: HS, 면세점 상품: DF, 마트 상품: MA)*/
		$printStr .="<<<gtype>>>\n";

		/*branc : 백화점 지점명*/
		$printStr .="<<<branc>>>\n";

		/*pcpdn : 쿠폰다운로드필요 여부*/
		$printStr .="<<<pcpdn>>>\n";

		/*dlvga : 차등배송비 여부*/
		/*dlvdt : 차등배송비 내용*/
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
					$localName .=$devCfg['local_charge_list_'.$k.'_name'][$c];
				}

				$localCharge .=$localName." ".$devCfg['local_charge_pay_'.$k]."원 추가";
			}
		}

		if($localChargeFlag==true && $localCharge){
			$printStr .="<<<dlvga>>>Y\n";
			$printStr .="<<<dlvdt>>>".iconv("UTF-8","EUC-KR",$localCharge)."\n";
		}
		else{
			$printStr .="<<<dlvga>>>\n";
			$printStr .="<<<dlvdt>>>\n";
		}


		/*insco : 별도 설치비 유무*/
		$printStr .="<<<insco>>>\n";

		/*ftend : 상품 끝*/
		$printStr .="<<<ftend>>>\n";

		$gCnt++;
	}
	if($gCnt>'0'){
		echo "<<<tocnt>>>".$gCnt."\n";
		echo $printStr;
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
	$printStr="";
	$gCnt='0';
	foreach($result as $i=>$goods) {
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

		/*begin : 상품 시작*/
		$printStr .="<<<begin>>>\n";

		/*mapid : 상품 코드*/
		$printStr .="<<<mapid>>>".iconv("UTF-8","EUC-KR",$goods->goods_code)."\n";

		/*class : 상품품절여부*/
		$rSoldout = goodsSoldoutCheck($goods); //품절체크

		if($rSoldout || $goods->goods_display=='hidden'){
			$printStr .="<<<class>>>D\n"; // 품절
			/*utime : 상품정보 생성 시각*/
			$printStr .="<<<utime>>>".date("Y-m-d H:i:s",$currentTime)."\n";
		}
		else{
			if($goods->goods_reg_date==$goods->goods_update_date) $printStr .="<<<class>>>I\n";
			else $printStr .="<<<class>>>U\n";

			/*utime : 상품정보 생성 시각*/
			$printStr .="<<<utime>>>".date("Y-m-d H:i:s",$currentTime)."\n";

			/*pname : 상품명*/
			$printStr .="<<<pname>>>".iconv("UTF-8","EUC-KR",$goods->goods_name)."\n";

			/*price : 상품 가격*/
			$printStr .="<<<price>>>".iconv("UTF-8","EUC-KR",$goods->goods_price)."\n";

			/*pgurl : 상품 URL*/
			$printStr .="<<<pgurl>>>".home_url('/')."?bbseGoods=".$goods->idx."\n";

			/*igurl : 상품 이미지 URL*/
			$printStr .="<<<igurl>>>".$imgUrl."\n";

			/*cate : 상품 카테고리 명 1 ~ 4*/
			$printStr .="<<<cate1>>>".iconv("UTF-8","EUC-KR",$goodsCate['depth_1'])."\n";
			$printStr .="<<<cate2>>>".iconv("UTF-8","EUC-KR",$goodsCate['depth_2'])."\n";
			$printStr .="<<<cate3>>>".iconv("UTF-8","EUC-KR",$goodsCate['depth_3'])."\n";
			$printStr .="<<<cate4>>>\n";

			/*caid : 상품 카테고리 ID 1 ~ 4*/
			$printStr .="<<<caid1>>>".iconv("UTF-8","EUC-KR",$goodsCate['idx_1'])."\n";
			$printStr .="<<<caid2>>>".iconv("UTF-8","EUC-KR",$goodsCate['idx_2'])."\n";
			$printStr .="<<<caid3>>>".iconv("UTF-8","EUC-KR",$goodsCate['idx_3'])."\n";
			$printStr .="<<<caid4>>>\n";

			/*model : 모델명*/
			$printStr .="<<<model>>>\n";

			/*model : 브랜드명*/
			$printStr .="<<<brand>>>\n";

			/*maker : 제조사*/
			$printStr .="<<<maker>>>".iconv("UTF-8","EUC-KR",$goods->goods_company)."\n";

			/*origi : 원산지*/
			$printStr .="<<<origi>>>".iconv("UTF-8","EUC-KR",$goods->goods_local)."\n";

			/*deliv : 배송비 (착불->-1, 선불->배송비, 조건부 조건금액 이상->0, 조건부 조건금액 미만->배송비)*/
			if($devCfg['delivery_charge_type']=='free') $printStr .="<<<deliv>>>0\n"; // 무료 배송
			else{
				if(!$devCfg['delivery_charge_payment'] || $devCfg['delivery_charge_payment']=='advance'){ // 선불결제
					if(!$devCfg['condition_free_use'] || $devCfg['condition_free_use']=='off') $printStr .="<<<deliv>>>".iconv("UTF-8","EUC-KR",$devCfg['delivery_charge'])."\n"; // 조건부 무료배송 : 사용안함
					else{// 조건부 무료배송 : 사용함
						if($goods->goods_price>=$devCfg['total_pay']) $printStr .="<<<deliv>>>0\n"; // 상품가격 >= 조건금액
						else $printStr .="<<<deliv>>>".$devCfg['delivery_charge']."\n"; // 상품가격 < 조건금액
					}
				}
				else $printStr .="<<<deliv>>>-1\n"; // 후불(착불)결제
			}

			/*event : 이벤트*/
			$printStr .="<<<event>>>\n";

			/*coupo : 쿠폰*/
			$printStr .="<<<coupo>>>\n";

			/*pcard : 카드 무이자 할부 정보*/
			$printStr .="<<<pcard>>>\n";

			/*point : 포인트*/
			if($goods->goods_earn_use=='on' && $goods->goods_earn>'0') $printStr .="<<<point>>>".iconv("UTF-8","EUC-KR",$goods->goods_earn)."\n";
			else $printStr .="<<<point>>>0\n";

			/*mvurl : 동영상 상품 여부*/
			$printStr .="<<<mvurl>>>N\n"; 

			/*selid : 셀러 ID(오픈마켓에 한함)*/
			$printStr .="<<<selid>>>\n";

			/*barcode : 바코드*/
			$printStr .="<<<barcode>>>".iconv("UTF-8","EUC-KR",$goods->goods_barcode)."\n";

			/*cardn : 이벤트 진행중이 카드(1개만 표시)*/
			$printStr .="<<<cardn>>>\n";

			/*cardp : 카드 할인가격*/
			$printStr .="<<<cardp>>>\n";

			/*mpric : 모바일 상품가격*/
			$printStr .="<<<mpric>>>".iconv("UTF-8","EUC-KR",$goods->goods_price)."\n";

			/*revct : 상품평 개수*/
			$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE goods_idx='".$goods->idx."'");
			$printStr .="<<<revct>>>".$reviewCnt."\n";

			/*ecoyn : 친환경 인증여부*/
			$printStr .="<<<ecoyn>>>\n";

			/*econm : 친환경 인증분류*/
			$printStr .="<<<econm>>>\n";

			/*gtype : 상품 구분(해외쇼핑 상품: FS, 백화점 상품: DP, 홈쇼핑 상품: HS, 면세점 상품: DF, 마트 상품: MA)*/
			$printStr .="<<<gtype>>>\n";

			/*branc : 백화점 지점명*/
			$printStr .="<<<branc>>>\n";

			/*pcpdn : 쿠폰다운로드필요 여부*/
			$printStr .="<<<pcpdn>>>\n";

			/*dlvga : 차등배송비 여부*/
			/*dlvdt : 차등배송비 내용*/
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
						$localName .=$devCfg['local_charge_list_'.$k.'_name'][$c];
					}

					$localCharge .=$localName." ".$devCfg['local_charge_pay_'.$k]."원 추가";
				}
			}

			if($localChargeFlag==true && $localCharge){
				$printStr .="<<<dlvga>>>Y\n";
				$printStr .="<<<dlvdt>>>".iconv("UTF-8","EUC-KR",$localCharge)."\n";
			}
			else{
				$printStr .="<<<dlvga>>>\n";
				$printStr .="<<<dlvdt>>>\n";
			}


			/*insco : 별도 설치비 유무*/
			$printStr .="<<<insco>>>\n";
		}

		/*ftend : 상품 끝*/
		$printStr .="<<<ftend>>>\n";

		$gCnt++;
	}
	if($gCnt>'0'){
		echo $printStr;
	}
}
?>
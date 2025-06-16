<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb, $SITEMAPS;

$V = $_POST;

if($V['tMode']=='imgUrl' && $V['tURL']){ // 이미지 URL 구하기

	$image_id=get_attachment_id_from_src($V['tURL']);

	// 작은 사진(썸네일)=>thumbnail, 보통=>medium, 최대 크기=>large, 전체 크기=>full, 상품이미지 1=>goodsimage1, 상품이미지 2=>goodsimage2, 상품이미지 3=>goodsimage3, 상품이미지 4=>goodsimage4
	$image_g1_url = wp_get_attachment_image_src($image_id, "goodsimage1");
	$image_g4_url = wp_get_attachment_image_src($image_id, "goodsimage4");
	// wp_get_attachment_image_src :  [0] => url, [1] => width, [2] => height, [3] => boolean: true if $url is a resized image, false if it is the original.

	if($image_id || $image_g1_url['0'] || $image_g4_url['0']){
		echo "success|||".$image_id."|||".$image_g1_url['0']."|||".$image_g4_url['0'];
		exit;
	}
	else{
		echo "fail";
		exit;
	}
}
elseif($V['tMode']=='insert' && trim($V['goods_name']) && sizeof($V['goods_cat_sub_list'])>'0' && sizeof($V['goods_add_img'])>'0'){
	//$goods_code=$V['goods_code']; // 상품코드
    $goods_code=""; // 상품코드
	$goods_icon_new=$V['goods_icon_new']; // 신상품 아이콘 표시 (view / NULL)
	$goods_icon_best=$V['goods_icon_best']; // 베스트상품 아이콘 표시 (view / NULL)
	$goods_name=$V['goods_name']; // 상품명
	$goods_description=$V['goods_description']; // 상품 간단설명
	$goods_detail=$V['goods_detail']; // 상품상세설명
    $goods_unique_code=$V['goods_unique_code']; // 고유번호
    $goods_unique_code_display=$V['goods_unique_code_display']; // 고유번호 노출여부 (view / NULL)
    $goods_barcode=$V['goods_barcode']; // 바코드
    $goods_barcode_display=$V['goods_barcode_display']; // 바코드 노출여부 (view / NULL)
    $goods_location_no=$V['goods_location_no']; // 위치정보
	$goods_company=$V['goods_company']; // 제조사
    $goods_company_display=$V['goods_company_display']; // 제조사 노출여부 (view / NULL)
    $goods_local=$V['goods_local']; // 원산지
    $goods_local_display=$V['goods_local_display']; // 원산지 노출여부 (view / NULL)
	//$goods_consumer_price=$V['goods_consumer_price']; // 소비자가
	//$goods_cprice_display=$V['goods_cprice_display']; // 소비자가 노출여부 (view / NULL)
	//$goods_tax=$V['goods_tax']; // 부가세
	$goods_tax_display=$V['goods_tax_display']; // 부가세 노출여부 (view / NULL)
	$goods_ship_price=$V['goods_ship_price']; //개별배송비
	$goods_ship_tf=$V['goods_ship_tf'];	//개별배송비 적용여부
	$goods_external_link=$V['goods_external_link']; //외부링크 설정
	$goods_external_link_tf=$V['goods_external_link_tf']; //외부링크 설정
	$goods_buy_inquiry=$V['goods_buy_inquiry']; //별도문의 설정
	$goods_buy_inquiry_tf=$V['goods_buy_inquiry_tf']; //별도문의 설정
	
	if(!$V['goods_price'] || $V['goods_price']<='0') $goods_price=$V['goods_consumer_price']; // 판매가가 Null 또는 '0' 인 경우 소비자가로 적용
	else $goods_price=$V['goods_price']; // 판매가

/*     if(sizeof($V['goods_member_level'])>'0' && sizeof($V['goods_member_price'])>'0'){
		$goods_member_priceArray['goods_member_level']=$V['goods_member_level'];

		for($m=0;$m<sizeof($V['goods_member_level']);$m++){
			if(!$V['goods_member_price'][$m] || $V['goods_member_price'][$m]<='0') $V['goods_member_price'][$m]=$goods_price;
		}

		$goods_member_priceArray['goods_member_price']=$V['goods_member_price'];
		$goods_member_price=serialize($goods_member_priceArray); // Serialize 처리
	}
	else $goods_member_price=""; */

	$goods_count_flag=$V['goods_count_flag']; // 재고설정 : unlimit=>무제한, goods_count=>재고수량, option_count=>상품옵션 재고로 설정
	$goods_count=$V['goods_count']; // 재고수량

	if($V['goods_count_flag'] =='unlimit') $goods_count_view='off';
	else $goods_count_view=$V['goods_count_view']; // 재고수량 노출

	if(($V['goods_add_field_title']['0'] && $V['goods_add_field_description']['0']) || ($V['goods_add_field_title']['1'] && $V['goods_add_field_description']['1'])){
		$goods_add_fieldArray['goods_add_field_title']=$V['goods_add_field_title'];
		$goods_add_fieldArray['goods_add_field_description']=$V['goods_add_field_description'];
		$goods_add_field=serialize($goods_add_fieldArray); // Serialize 처리
	}
	else $goods_add_field="";
/* 등급별 소비자가 숨김 (시작) */
	if($V['goods_cprice_display']>'0'){
	    $goods_cprice_display_Array['goods_cprice_display']=$V['goods_cprice_display']; // 등급별 금액 개수
	    for($h=0; $h<sizeof($V['goods_cprice_display']);$h++){
	        $goods_cprice_display_Array['goods_cprice_display']=$V['goods_cprice_display'];
	    }
	    $goods_cprice_display = serialize($goods_cprice_display_Array);
	}

/* 등급별 소비자가 숨김 (끝) */
	
/* 등급별 금액 설정 (시작) */
	if($V['goods_member_level']>'0'){
	    
	    $goods_usergrade_price_Array['goods_member_level']=$V['goods_member_level']; // 등급별 금액 개수
	    $goods_option_add_usergrade_price_count=sizeof($V['goods_option_add_usergrade_price_count']);
	    
	    for($h=0; $h<$goods_option_add_usergrade_price_count;$h++){
	        
	        if($V['goods_member_price'] == null || $V['goods_member_price']<='0'){
	            $goods_member_price=$V['goods_consumer_price']; // 판매가가 Null 또는 '0' 인 경우 소비자가로 적용
	            $goods_vat=$goods_member_price*0.1; // 판매가가 Null 또는 '0' 인 경우 소비자가로 적용
	        }
	        else $goods_member_price=$V['goods_member_price']; // 판매가
	        
	        if($V['goods_member_price'] == null || $V['goods_member_price']<='0') $goods_vat='0'; // 판매가가 0이거나 null 인경우 0으로 설정
	        else $goods_vat=$V['goods_vat']; // 부가세
	        
	        $goods_usergrade_price_Array['goods_member_level']=$V['goods_member_level'];
	        $goods_usergrade_price_Array['goods_consumer_price']=$V['goods_consumer_price'];
	        $goods_usergrade_price_Array['goods_member_price']=$V['goods_member_price'];
	        $goods_usergrade_price_Array['goods_vat']=$V['goods_vat'];
	        $goods_usergrade_price_Array['goods_cat_sub_list']=$V['goods_cat_sub_list'];
	    }
	    $goods_usergrade_price=serialize($goods_usergrade_price_Array); // Serialize 처리
	    
	}
	else{
	    $goods_usergrade_price="";
	}
/* 등급별 금액 설정 (끝) */
    
/*  상품옵션 (시작) */
	if($V['goods_option_1_count']>'0' || $V['goods_option_2_count']>'0'){
		$goods_option_basicArray['goods_option_1_count']=$V['goods_option_1_count']; // 상품옵션1의 옵션값 개수
		$goods_option_basicArray['goods_option_1_title']=$V['goods_option_1_title']; // 상품옵션1의 옵션명
		$goods_option_basicArray['goods_option_1_item']=$V['goods_option_1_item']; // 상품옵션1의 옵션값
		$goods_option_basicArray['goods_option_2_count']=$V['goods_option_2_count']; // 상품옵션2의 옵션값 개수
		$goods_option_basicArray['goods_option_2_title']=$V['goods_option_2_title']; // 상품옵션2의 옵션명
		$goods_option_basicArray['goods_option_2_item']=$V['goods_option_2_item']; // 상품옵션2의 옵션값

		$goods_option_basic=serialize($goods_option_basicArray); // Serialize 처리
	}
	else $goods_option_basic="";
/*  상품옵션 (끝) */

/*  추가옵션 (시작) */
	if($V['goods_add_option_count']>'0'){
		$goods_option_addArray['goods_add_option_count']=$V['goods_add_option_count']; // 추가 옵션 개수
		$goods_add_option_count=$V['goods_add_option_count']; // 추가 옵션 개수
		for($h=1;$h<=$goods_add_option_count;$h++){
			if($V['goods_add_'.$h.'_item_count']>'0'){
				$goods_option_addArray['goods_add_'.$h.'_use']=$V['goods_add_'.$h.'_use']; // 추가 옵션 사용여부 (on / off )
				$goods_option_addArray['goods_add_'.$h.'_choice']=$V['goods_add_'.$h.'_choice']; // 선택/필수 적용 : selection=>선택항목, required=>필수항목
				$goods_option_addArray['goods_add_'.$h.'_title']=$V['goods_add_'.$h.'_title'];
				$goods_option_addArray['goods_add_'.$h.'_item_count']=$V['goods_add_'.$h.'_item_count'];
				$goods_option_addArray['goods_add_'.$h.'_item']=$V['goods_add_'.$h.'_item']; // 추가 옵션의 옵션값

				if(!$V['goods_add_'.$h.'_item_overprice'] || $V['goods_add_'.$h.'_item_overprice']<'0') $goods_option_addArray['goods_add_'.$h.'_item_overprice']='0'; // 추가 옵션의 옵션값 별 추가가격
				else $goods_option_addArray['goods_add_'.$h.'_item_overprice']=$V['goods_add_'.$h.'_item_overprice'];

				$goods_option_addArray['goods_add_'.$h.'_item_unique_code']=$V['goods_add_'.$h.'_item_unique_code']; // 추가 옵션의 옵션값 별 고유번호
				$goods_option_addArray['goods_add_'.$h.'_item_display']=$V['goods_add_'.$h.'_item_display']; // 추가 옵션의 옵션값 별 노출 여부 (view / NULL)
				$goods_option_addArray['goods_add_'.$h.'_item_soldout']=$V['goods_add_'.$h.'_item_soldout']; // 추가 옵션의 옵션값 별 품절 여부 (soldout / NULL)
			}
		}

		$goods_option_add=serialize($goods_option_addArray); // Serialize 처리
	}
	else $goods_option_add="";
/*  추가옵션 (끝) */

	$goods_recommend_use=$V['goods_recommend_use']; // 추천상품 사용여부 (on / off )
	$goods_recommend_list_cnt=sizeof($V['goods_recommend_list']); // 추천상품 index 목록
	$goods_recommend_list="";
	for($p=0;$p<$goods_recommend_list_cnt;$p++){
		if($goods_recommend_list) $goods_recommend_list .=",";
		$goods_recommend_list .=$V['goods_recommend_list'][$p];
	}

	$goods_relation_use=$V['goods_relation_use']; // 관련상품 사용여부 (on / off )
	$goods_relation_list_cnt=sizeof($V['goods_relation_list']); // 관련상품 index 목록
	$goods_relation_list="";
	for($q=0;$q<$goods_relation_list_cnt;$q++){
		if($goods_relation_list) $goods_relation_list .=",";
		$goods_relation_list .=$V['goods_relation_list'][$q];
	}

	$goods_seo_use=$V['goods_seo_use']; // SEO 사용여부 (on / off )
	$goods_seo_title=$V['goods_seo_title']; // SEO 타이틀
	$goods_seo_description=$V['goods_seo_description']; // SEO 설명
	$goods_seo_keyword=$V['goods_seo_keyword']; // SEO 키워드
	$goods_earn_use=$V['goods_earn_use']; // 적립금 사용여부 (on / off )
	$goods_earn=$V['goods_earn']; // 개별 적립금
	$goods_max_cnt=$V['goods_max_cnt']; // 1회 최대수량

	$goods_display=$V['goods_display']; // 상품노출여부 : display=>노출, hidden=>비노출, soldout=>품절

	if($V['goods_naver_shop']=='on') $goods_naver_shop='on'; // 네이버지식쇼핑 : on/off
	else $goods_naver_shop='off';

	if($V['goods_naver_pay']=='on') $goods_naver_pay='on'; // 네이버페이 : on/off
	else $goods_naver_pay='off';

/*  	$goods_cat_list_cnt=sizeof($V['goods_cat_list']); // 카테고리 목록
	if($goods_cat_list_cnt>'0'){
		$goods_cat_list="|";
		for($r=0;$r<$goods_cat_list_cnt;$r++){
			$goods_cat_list .=$V['goods_cat_list'][$r]."|";
		}
	}
	else $goods_cat_list=""; */
	
	$cPrice['goods_consumer_price']=$V['goods_consumer_price']; // 등급별 금액 개수
	$csListe['goods_cat_sub_list']=$V['goods_cat_sub_list']; // 등급별 금액 개수     
	if(sizeof($cPrice)>'0'){ 
	    $goods_cat_list.="|";
	    for($i=0; $i<sizeof($csListe[goods_cat_sub_list]); $i++){
	        $cate = $csListe[goods_cat_sub_list][$i];
	        $depth = explode('$',$cate);
	        
	        $depth1Idx = $wpdb->get_results("select idx from bbse_commerce_category where depth_1 = '".$depth[0]."' and depth_2 = 0");
	        $depth2Idx = $wpdb->get_results("select idx from bbse_commerce_category where depth_1 = '".$depth[0]."' and depth_2 = '".$depth[1]."' and depth_3 = 0");
	        
	        if($depth[2] != 0){
	            $depth3Idx = $wpdb->get_results("select idx from bbse_commerce_category where depth_1 = '".$depth[0]."' and depth_2 = '".$depth[1]."' and depth_3 = '".$depth[2]."'");
	        }else{
	            $depth3Idx[0]->idx = '0';
	        }
	        $goods_cat_list.=$depth1Idx[0]->idx."$".$depth2Idx[0]->idx."$".$depth3Idx[0]->idx."|";
	    }
	}
	else $goods_cat_list="";
	
	$goods_usergrade_price_Array['goods_cat_sub_list']=$goods_cat_list;
    $goods_usergrade_price=serialize($goods_usergrade_price_Array); // Serialize 처리

	$goods_add_img_cnt=sizeof($V['goods_add_img']); // 상품이미지 ID 리스트
	$goods_add_img="";
	for($s=0;$s<$goods_add_img_cnt;$s++){
		if($goods_add_img) $goods_add_img .=",";
		$goods_add_img .=$V['goods_add_img'][$s];
	}

	$goods_basic_img=$V['goods_basic_img']; // 상품이미지 중 대표이미지
	$goods_add_img_count=$V['goods_add_img_count']; // 상품이미지 개수

	$goods_reg_date=current_time('timestamp');
	$goods_update_date=$goods_reg_date;
	
	$goods_douzone_code=$V['goods_douzone_code'];

	if($V['goods_count_flag'] =='goods_count') $goods_option_basic="";

	$inQuery="INSERT INTO bbse_commerce_goods (
						goods_code,
						goods_name,
						goods_display,
						goods_naver_shop,
						goods_naver_pay,
						goods_cat_list,
						goods_add_img_cnt,
						goods_add_img,
						goods_basic_img,
						goods_icon_new,
						goods_icon_best,
						goods_description,
						goods_detail,
						goods_unique_code,
						goods_unique_code_display,
						goods_barcode,
						goods_barcode_display,
						goods_location_no,
						goods_company,
						goods_company_display,
						goods_local,
						goods_local_display,
						goods_cprice_display,
						goods_tax,
						goods_tax_display,
						goods_consumer_price,
						goods_price,
						goods_member_price,
						goods_count_flag,
						goods_count,
						goods_count_view,
						goods_add_field,
						goods_option_basic,
						goods_option_add,
						goods_recommend_use,
						goods_recommend_list,
						goods_relation_use,
						goods_relation_list,
						goods_seo_use,
						goods_seo_title,
						goods_seo_description,
						goods_seo_keyword,
						goods_earn_use,
						goods_earn,
						max_cnt,
						goods_update_date,
						goods_reg_date,
						goods_ship_price,
						goods_ship_tf,
						goods_external_link,
						goods_external_link_tf,
						goods_buy_inquiry,
						goods_buy_inquiry_tf
					) 
					VALUES (
						'".$goods_code."',
						'".$goods_name."',
						'".$goods_display."',
						'".$goods_naver_shop."',
						'".$goods_naver_pay."',
						'".$goods_cat_list."',
						'".$goods_add_img_cnt."',
						'".$goods_add_img."',
						'".$goods_basic_img."',
						'".$goods_icon_new."',
						'".$goods_icon_best."',
						'".$goods_description."',
						'".$goods_detail."',
						'".$goods_unique_code."',
						'".$goods_unique_code_display."',
						'".$goods_barcode."',
						'".$goods_barcode_display."',
						'".$goods_location_no."',
						'".$goods_company."',
						'".$goods_company_display."',
						'".$goods_local."',
						'".$goods_local_display."',
						'".$goods_cprice_display."',
						'".$goods_tax."',
						'".$goods_tax_display."',
						'".$goods_consumer_price."',
						'".$goods_price."',
						'".$goods_usergrade_price."',
						'".$goods_count_flag."',
						'".$goods_count."',
						'".$goods_count_view."',
						'".$goods_add_field."',
						'".$goods_option_basic."',
						'".$goods_option_add."',
						'".$goods_recommend_use."',
						'".$goods_recommend_list."',
						'".$goods_relation_use."',
						'".$goods_relation_list."',
						'".$goods_seo_use."',
						'".$goods_seo_title."',
						'".$goods_seo_description."',
						'".$goods_seo_keyword."',
						'".$goods_earn_use."',
						'".$goods_earn."',
						'".$goods_max_cnt."',
						'".$goods_update_date."',
						'".$goods_reg_date."',
						'".$goods_ship_price."',
						'".$goods_ship_tf."',
						'".$goods_external_link."',
						'".$goods_external_link_tf."',
						'".$goods_buy_inquiry."',
						'".$goods_buy_inquiry_tf."'
					 )";
	$wpdb->query($inQuery);
	$idx = $wpdb->insert_id;
	
	
	for($s=0;$s<$goods_add_img_cnt;$s++){
	    if($goods_add_img) $goods_add_img .=",";
	    $goods_add_img .=$V['goods_add_img'][$s];
	}
	
	if($idx>'0'){
		$goods_code=$goods_reg_date."-".$idx;
		$result=$wpdb->query("UPDATE bbse_commerce_goods SET goods_code='".$goods_code."' WHERE idx='".$idx."'");
		
		//update 241029 start
		$inQuery="INSERT INTO bbse_goods_user_grade_price (goods_code, goods_usergrade_price) VALUES ('".$goods_code."','".$goods_usergrade_price."')";
		$result=$wpdb->query($inQuery);
		//update 241029 end
		
		//modified by hyeyoon 250403
		//$inQuery2="INSERT INTO tbl_douzone_code (goods_idx, goods_code, goods_douzone_code) VALUES ('".$idx."', '".$goods_code."','".$goods_douzone_code."')";
		//$result=$wpdb->query($inQuery2);
		//modified by hyeyoon 250403 end
		
		if($V['goods_count_flag'] !='goods_count'){
			//옵션 테이블 추가
			if($V['goods_option_1_count']>'0' && $V['goods_option_2_count']>'0') $optCnt=$V['goods_option_1_count']*$V['goods_option_2_count'];
			elseif($V['goods_option_1_count']>'0') $optCnt=$V['goods_option_1_count'];
			elseif($V['goods_option_2_count']>'0') $optCnt=$V['goods_option_2_count'];
			else $optCnt=$V['goods_option_1_count']*$V['goods_option_2_count'];

			$inCnt=1;
			for($op=0;$op<$optCnt;$op++){
				if($V['goods_count_flag']!='option_count'){
					$newOptCount='0';
					$newOptDisplay='view';
					$newOptSoldout='';
				}
				else {
					$newOptCount=$V['goods_option_item_count'][$op];
					$newOptDisplay=$V['goods_option_item_display'][$op];
					$newOptSoldout=$V['goods_option_item_soldout'][$op];
				}
				$wpdb->query("INSERT INTO bbse_commerce_goods_option (goods_idx, goods_option_title, goods_option_item_overprice, goods_option_item_count, goods_option_item_unique_code, goods_option_item_display, goods_option_item_soldout,goods_option_item_rank) VALUES ('".$idx."','".$V['goods_option_title'][$op]."','".$V['goods_option_item_overprice'][$op]."','".$newOptCount."','".$V['goods_option_item_unique_code'][$op]."','".$newOptDisplay."','".$newOptSoldout."','".$inCnt."')");
			}
		}
	}

	if($result){
		echo "success|||".$idx;
		if(is_object($SITEMAPS)) $SITEMAPS->tryWriteMapFile();
		exit;
	}
	else{
		echo "dbError|||".("UPDATE bbse_commerce_goods SET goods_code='".$goods_code."' WHERE idx='".$idx."'").$inQuery;
		exit;
	}
}
elseif($V['tMode']=='modify' && trim($V['tData']) && trim($V['goods_name'])){
	$tData=trim($V['tData']); // 상품 인텍스

	//기존 상품정보 추출 : 품절 -> 정상판매 체크
	$oCnfCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='order'");
	if($oCnfCnt>'0'){
		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
		$orderInfo=unserialize($confData->config_data);
	}
	if($orderInfo['soldout_notice_use']=='on' && ($orderInfo['soldout_notice_sms']=='sms' || $orderInfo['soldout_notice_email']=='email') && $V['goods_display']=='display'){
		$oldGdata = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$tData."'");
		$oldSoldout=bbse_commerce_goodsSoldoutCheck($oldGdata);
	}

	$total = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$tData."'");    // 총 상품수

	if($total<='0'){
		echo "notExits";
		exit;
	}

	$goods_code = $V['goods_code'];
	//$goods_douzone_code=$V['goods_douzone_code'];
	$goods_icon_new=$V['goods_icon_new']; // 신상품 아이콘 표시 (view / NULL)
	$goods_icon_best=$V['goods_icon_best']; // 베스트상품 아이콘 표시 (view / NULL)
	$goods_name=$V['goods_name']; // 상품명
	$goods_description=$V['goods_description']; // 상품 간단설명
	$goods_detail=$V['goods_detail']; // 상품상세설명
    $goods_unique_code=$V['goods_unique_code']; // 고유번호
    $goods_unique_code_display=$V['goods_unique_code_display']; // 고유번호 노출여부 (view / NULL)
    $goods_barcode=$V['goods_barcode']; // 바코드
    $goods_barcode_display=$V['goods_barcode_display']; // 바코드 노출여부 (view / NULL)
    $goods_location_no=$V['goods_location_no']; // 위치정보
	$goods_company=$V['goods_company']; // 제조사
    $goods_company_display=$V['goods_company_display']; // 제조사 노출여부 (view / NULL)
    $goods_local=$V['goods_local']; // 원산지
    $goods_local_display=$V['goods_local_display']; // 원산지 노출여부 (view / NULL)
	//$goods_consumer_price=$V['goods_consumer_price']; // 소비자가
	//$goods_cprice_display=$V['goods_cprice_display']; // 소비자가 노출여부 (view / NULL)
	//$goods_tax=$V['goods_tax']; // 부가세
	$goods_tax_display=$V['goods_tax_display']; // 부가세 노출여부 (view / NULL)
	$goods_ship_price=$V['goods_ship_price']; //개별배송비
	$goods_ship_tf=$V['goods_ship_tf'];	//개별배송비 적용여부
	$goods_external_link=$V['goods_external_link']; //외부링크 설정
	$goods_external_link_tf=$V['goods_external_link_tf']; //외부링크 설정
	$goods_buy_inquiry=$V['goods_buy_inquiry']; //별도문의 설정
	$goods_buy_inquiry_tf=$V['goods_buy_inquiry_tf']; //별도문의 설정
	
/* 	if(!$V['goods_price'] || $V['goods_price']<='0') $goods_price=$V['goods_consumer_price']; // 판매가가 Null 또는 '0' 인 경우 소비자가로 적용
	else $goods_price=$V['goods_price']; // 판매가

	if(sizeof($V['goods_member_level'])>'0' && sizeof($V['goods_member_price'])>'0'){
		$goods_member_priceArray['goods_member_level']=$V['goods_member_level'];

		for($m=0;$m<sizeof($V['goods_member_level']);$m++){
			if(!$V['goods_member_price'][$m] || $V['goods_member_price'][$m]<='0') $V['goods_member_price'][$m]=$goods_price;
		}

		$goods_member_priceArray['goods_member_price']=$V['goods_member_price'];
		$goods_member_price=serialize($goods_member_priceArray); // Serialize 처리
	}
	else $goods_member_price=""; */
	
	if($V['goods_cprice_display']>'0'){
	    $goods_cprice_display_Array['goods_cprice_display']=$V['goods_cprice_display']; // 등급별 금액 개수
	    for($h=0; $h<sizeof($V['goods_cprice_display']);$h++){
	        $goods_cprice_display_Array['goods_cprice_display']=$V['goods_cprice_display'];
	    }
	    $goods_cprice_display = serialize($goods_cprice_display_Array);
	}
	/* 등급별 금액 설정 (시작) */
	if($V['goods_member_level']>'0'){
	    
	    $goods_usergrade_price_Array['goods_member_level']=$V['goods_member_level']; // 등급별 금액 개수
	    $goods_option_add_usergrade_price_count=sizeof($V['goods_option_add_usergrade_price_count']);
	    
	    for($h=0; $h<$goods_option_add_usergrade_price_count;$h++){
	        
	        if($V['goods_member_price'] == null || $V['goods_member_price']<='0'){
	            $goods_member_price=$V['goods_consumer_price']; // 판매가가 Null 또는 '0' 인 경우 소비자가로 적용
	            $goods_vat=$goods_member_price*0.1; // 판매가가 Null 또는 '0' 인 경우 소비자가로 적용
	        }
	        else $goods_member_price=$V['goods_member_price']; // 판매가
	        
	        if($V['goods_member_price'] == null || $V['goods_member_price']<='0') $goods_vat='0'; // 판매가가 0이거나 null 인경우 0으로 설정
	        else $goods_vat=$V['goods_vat']; // 부가세
	        
	        $goods_usergrade_price_Array['goods_member_level']=$V['goods_member_level'];
	        $goods_usergrade_price_Array['goods_consumer_price']=$V['goods_consumer_price'];
	        $goods_usergrade_price_Array['goods_member_price']=$V['goods_member_price'];
	        $goods_usergrade_price_Array['goods_vat']=$V['goods_vat'];
	        $goods_usergrade_price_Array['goods_cat_sub_list']=$V['goods_cat_sub_list'];
	    }
	    $goods_usergrade_price=serialize($goods_usergrade_price_Array); // Serialize 처리
	    
	}
	else{
	    $goods_usergrade_price="";
	}
	/* 등급별 금액 설정 (끝) */

	$goods_count_flag=$V['goods_count_flag']; // 재고설정 : unlimit=>무제한, goods_count=>재고수량, option_count=>상품옵션 재고로 설정
	$goods_count=$V['goods_count']; // 재고수량

	if($V['goods_count_flag'] =='unlimit') $goods_count_view='off';
	else $goods_count_view=$V['goods_count_view']; // 재고수량 노출

	if(($V['goods_add_field_title']['0'] && $V['goods_add_field_description']['0']) || ($V['goods_add_field_title']['1'] && $V['goods_add_field_description']['1'])){
		$goods_add_fieldArray['goods_add_field_title']=$V['goods_add_field_title'];
		$goods_add_fieldArray['goods_add_field_description']=$V['goods_add_field_description'];
		$goods_add_field=serialize($goods_add_fieldArray); // Serialize 처리
	}
	else $goods_add_field="";


/*  상품옵션 (시작) */
	if($V['goods_option_1_count']>'0' || $V['goods_option_2_count']>'0'){
		$goods_option_basicArray['goods_option_1_count']=$V['goods_option_1_count']; // 상품옵션1의 옵션값 개수
		$goods_option_basicArray['goods_option_1_title']=$V['goods_option_1_title']; // 상품옵션1의 옵션명
		$goods_option_basicArray['goods_option_1_item']=$V['goods_option_1_item']; // 상품옵션1의 옵션값
		$goods_option_basicArray['goods_option_2_count']=$V['goods_option_2_count']; // 상품옵션2의 옵션값 개수
		$goods_option_basicArray['goods_option_2_title']=$V['goods_option_2_title']; // 상품옵션2의 옵션명
		$goods_option_basicArray['goods_option_2_item']=$V['goods_option_2_item']; // 상품옵션2의 옵션값

		$goods_option_basic=serialize($goods_option_basicArray); // Serialize 처리
	}
	else $goods_option_basic="";
/*  상품옵션 (끝) */

/*  추가옵션 (시작) */
	if($V['goods_add_option_count']>'0'){
		$goods_option_addArray['goods_add_option_count']=$V['goods_add_option_count']; // 추가 옵션 개수
		$goods_add_option_count=$V['goods_add_option_count']; // 추가 옵션 개수
		for($h=1;$h<=$goods_add_option_count;$h++){
			if($V['goods_add_'.$h.'_item_count']>'0'){
				$goods_option_addArray['goods_add_'.$h.'_use']=$V['goods_add_'.$h.'_use']; // 추가 옵션 사용여부 (on / off )
				$goods_option_addArray['goods_add_'.$h.'_choice']=$V['goods_add_'.$h.'_choice']; // 선택/필수 적용 : selection=>선택항목, required=>필수항목
				$goods_option_addArray['goods_add_'.$h.'_title']=$V['goods_add_'.$h.'_title'];
				$goods_option_addArray['goods_add_'.$h.'_item_count']=$V['goods_add_'.$h.'_item_count'];
				$goods_option_addArray['goods_add_'.$h.'_item']=$V['goods_add_'.$h.'_item']; // 추가 옵션의 옵션값

				if(!$V['goods_add_'.$h.'_item_overprice'] || $V['goods_add_'.$h.'_item_overprice']<'0') $goods_option_addArray['goods_add_'.$h.'_item_overprice']='0'; // 추가 옵션의 옵션값 별 추가가격
				else $goods_option_addArray['goods_add_'.$h.'_item_overprice']=$V['goods_add_'.$h.'_item_overprice'];

				$goods_option_addArray['goods_add_'.$h.'_item_unique_code']=$V['goods_add_'.$h.'_item_unique_code']; // 추가 옵션의 옵션값 별 고유번호
				$goods_option_addArray['goods_add_'.$h.'_item_display']=$V['goods_add_'.$h.'_item_display']; // 추가 옵션의 옵션값 별 노출 여부 (view / NULL)
				$goods_option_addArray['goods_add_'.$h.'_item_soldout']=$V['goods_add_'.$h.'_item_soldout']; // 추가 옵션의 옵션값 별 품절 여부 (soldout / NULL)
			}
		}

		$goods_option_add=serialize($goods_option_addArray); // Serialize 처리
	}
	else $goods_option_add="";
/*  추가옵션 (끝) */
	
	$goods_recommend_use=$V['goods_recommend_use']; // 추천상품 사용여부 (on / off )
	$goods_recommend_list_cnt=sizeof($V['goods_recommend_list']); // 추천상품 index 목록
	$goods_recommend_list="";
	for($p=0;$p<$goods_recommend_list_cnt;$p++){
		if($goods_recommend_list) $goods_recommend_list .=",";
		$goods_recommend_list .=$V['goods_recommend_list'][$p];
	}

	$goods_relation_use=$V['goods_relation_use']; // 관련상품 사용여부 (on / off )
	$goods_relation_list_cnt=sizeof($V['goods_relation_list']); // 관련상품 index 목록
	$goods_relation_list="";
	for($q=0;$q<$goods_relation_list_cnt;$q++){
		if($goods_relation_list) $goods_relation_list .=",";
		$goods_relation_list .=$V['goods_relation_list'][$q];
	}

	$goods_seo_use=$V['goods_seo_use']; // SEO 사용여부 (on / off )
	$goods_seo_title=$V['goods_seo_title']; // SEO 타이틀
	$goods_seo_description=$V['goods_seo_description']; // SEO 설명
	$goods_seo_keyword=$V['goods_seo_keyword']; // SEO 키워드
	$goods_earn_use=$V['goods_earn_use']; // 적립금 사용여부 (on / off )
	$goods_earn=$V['goods_earn']; // 개별 적립금
	$goods_max_cnt=$V['goods_max_cnt']; // 1회 최대 수량

	$goods_display=$V['goods_display']; // 상품노출여부 : display=>노출, hidden=>비노출, soldout=>품절

	if($V['goods_naver_shop']=='on') $goods_naver_shop='on'; // 네이버지식쇼핑 : on/off
	else $goods_naver_shop='off';

	if($V['goods_naver_pay']=='on') $goods_naver_pay='on'; // 네이버페이 : on/off
	else $goods_naver_pay='off';
	
	
	$cPrice['goods_consumer_price']=$V['goods_consumer_price']; // 등급별 금액 개수
	$csListe['goods_cat_sub_list']=$V['goods_cat_sub_list']; // 등급별 금액 개수
	if(sizeof($cPrice)>'0'){
	    $goods_cat_list.="|";
	    $goods_cat_list2.="|";
	    for($i=0;$i<sizeof($cPrice['goods_consumer_price']); $i++){
    	    if($cPrice['goods_consumer_price'][$i] > '0'){
    	        $cate = $csListe['goods_cat_sub_list'][$i];
    	        $depth = explode('$',$cate);
    	        
    	        $depth1Idx = $wpdb->get_results("select idx from bbse_commerce_category where depth_1 = '".$depth[0]."' and depth_2 = 0");
    	        $depth2Idx = $wpdb->get_results("select idx from bbse_commerce_category where depth_1 = '".$depth[0]."' and depth_2 = '".$depth[1]."' and depth_3 = 0");
    	        
    	        if($depth[2] != 0){ 
    	            $depth3Idx = $wpdb->get_results("select idx from bbse_commerce_category where depth_1 = '".$depth[0]."' and depth_2 = '".$depth[1]."' and depth_3 = '".$depth[2]."'");
    	        }else{
    	            $depth3Idx[0]->idx = '0';
    	        }
    	        $goods_cat_list.=$depth1Idx[0]->idx."$".$depth2Idx[0]->idx."$".$depth3Idx[0]->idx."|";
    	        $goods_cat_list2.=$depth1Idx[0]->idx."$".$depth2Idx[0]->idx."$".$depth3Idx[0]->idx."|";
    	    }else{
    	        $goods_cat_list2.='0$0$0|';
    	    }
	    }       
	}
	else {
	    $goods_cat_list="";
	    $goods_cat_list2 = "";
	}
	
	$goods_usergrade_price_Array['goods_cat_list']=$goods_cat_list2;
    $goods_usergrade_price=serialize($goods_usergrade_price_Array); // Serialize 처리

	/* $goods_cat_list_cnt=sizeof($V['goods_cat_list']); // 카테고리 목록
	if($goods_cat_list_cnt>'0'){
		$goods_cat_list="|";
		for($r=0;$r<$goods_cat_list_cnt;$r++){
			$goods_cat_list .=$V['goods_cat_list'][$r]."|";
		}
	}
	else $goods_cat_list=""; */
	

	$goods_add_img_cnt=sizeof($V['goods_add_img']); // 상품이미지 ID 리스트
	$goods_add_img="";
	for($s=0;$s<$goods_add_img_cnt;$s++){
		if($goods_add_img) $goods_add_img .=",";
		$goods_add_img .=$V['goods_add_img'][$s];
	}

	$goods_basic_img=$V['goods_basic_img']; // 상품이미지 중 대표이미지
	$goods_add_img_count=$V['goods_add_img_count']; // 상품이미지 개수

	if($V['goods_count_flag'] =='goods_count') $goods_option_basic="";

	$goods_update_date=current_time('timestamp');

	$upQuery="UPDATE bbse_commerce_goods SET 
						goods_name='".$goods_name."',
						goods_display='".$goods_display."',
						goods_naver_shop='".$goods_naver_shop."',
						goods_naver_pay='".$goods_naver_pay."',
						goods_cat_list='".$goods_cat_list."',
						goods_add_img_cnt='".$goods_add_img_cnt."',
						goods_add_img='".$goods_add_img."',
						goods_basic_img='".$goods_basic_img."',
						goods_icon_new='".$goods_icon_new."',
						goods_icon_best='".$goods_icon_best."',
						goods_description='".$goods_description."',
						goods_detail='".$goods_detail."',
						goods_unique_code='".$goods_unique_code."',
						goods_unique_code_display='".$goods_unique_code_display."',
						goods_barcode='".$goods_barcode."',
						goods_barcode_display='".$goods_barcode_display."',
						goods_location_no='".$goods_location_no."',
						goods_company='".$goods_company."',
						goods_company_display='".$goods_company_display."',
						goods_local='".$goods_local."',
						goods_local_display='".$goods_local_display."',
						goods_cprice_display='".$goods_cprice_display."',
						goods_consumer_price='".$goods_consumer_price."',
						goods_price='".$goods_price."',
						goods_tax='".$goods_tax."',
						goods_tax_display='".$goods_tax_display."',
                        goods_member_price='".$goods_usergrade_price."',
						goods_count_flag='".$goods_count_flag."',
						goods_count='".$goods_count."',
						goods_count_view='".$goods_count_view."',
						goods_add_field='".$goods_add_field."',
						goods_option_basic='".$goods_option_basic."',
						goods_option_add='".$goods_option_add."',
						goods_recommend_use='".$goods_recommend_use."',
						goods_recommend_list='".$goods_recommend_list."',
						goods_relation_use='".$goods_relation_use."',
						goods_relation_list='".$goods_relation_list."',
						goods_seo_use='".$goods_seo_use."',
						goods_seo_title='".$goods_seo_title."',
						goods_seo_description='".$goods_seo_description."',
						goods_seo_keyword='".$goods_seo_keyword."',
						goods_earn_use='".$goods_earn_use."',
						goods_earn='".$goods_earn."', 
						max_cnt='".$goods_max_cnt."', 
						goods_ship_price='".$goods_ship_price."',
						goods_ship_tf='".$goods_ship_tf."',
						goods_external_link='".$goods_external_link."',
						goods_external_link_tf='".$goods_external_link_tf."',
						goods_buy_inquiry='".$goods_buy_inquiry."',
						goods_buy_inquiry_tf='".$goods_buy_inquiry_tf."',
						goods_update_date='".$goods_update_date."' WHERE idx='".$tData."'";

	$result=$wpdb->query($upQuery);
	
    $upQuery = "update bbse_goods_user_grade_price SET
                        goods_code='".$goods_code."',
                        goods_usergrade_price='".$goods_usergrade_price."' WHERE goods_idx='".$tData."'";
    
    $result=$wpdb->query($upQuery);
    
	
	//옵션 테이블 추가
	$wpdb->query("DELETE FROM bbse_commerce_goods_option  WHERE goods_idx='".$tData."'"); // 기존 옵션 삭제
    //$wpdb->query("DELETE FROM bbse_commerce_goods WHERE goods_option_check='option-$tData'"); // 기존 옵션 삭제
    //$wpdb->query("DELETE FROM tbl_douzone_code WHERE goods_idx='".$tData."'"); // 기존 옵션 삭제
    //$sql = "INSERT INTO tbl_douzone_code (goods_code, goods_douzone_code, goods_idx) VALUES ('".$goods_code."','".$goods_douzone_code."','$tData')";
    //$result=$wpdb->query($sql);
    
	if($V['goods_count_flag'] !='goods_count'){
		if($V['goods_option_1_count']>'0' && $V['goods_option_2_count']>'0') $optCnt=$V['goods_option_1_count']*$V['goods_option_2_count'];
		elseif($V['goods_option_1_count']>'0') $optCnt=$V['goods_option_1_count'];
		elseif($V['goods_option_2_count']>'0') $optCnt=$V['goods_option_2_count'];
		else $optCnt=$V['goods_option_1_count']*$V['goods_option_2_count'];

		$inCnt=1;
		for($op=0;$op<$optCnt;$op++){
			if($V['goods_count_flag']!='option_count'){
				$newOptCount='0';
				$newOptDisplay='view';
				$newOptSoldout='';
			}
			else {
				$newOptCount=$V['goods_option_item_count'][$op];
				$newOptDisplay=$V['goods_option_item_display'][$op];
				$newOptSoldout=$V['goods_option_item_soldout'][$op];
			}

			$wpdb->query("INSERT INTO bbse_commerce_goods_option (goods_idx, goods_option_title, goods_option_item_overprice, goods_option_item_count, goods_option_item_unique_code, goods_option_item_display, goods_option_item_soldout,goods_option_item_rank) VALUES ('".$tData."','".$V['goods_option_title'][$op]."','".$V['goods_option_item_overprice'][$op]."','".$newOptCount."','".$V['goods_option_item_unique_code'][$op]."','".$newOptDisplay."','".$newOptSoldout."','".$inCnt."')");
			//$wpdb->query("INSERT INTO bbse_commerce_goods (goods_code, goods_name, goods_option_check) VALUES ('".$V['goods_option_item_unique_code'][$op]."','".$V['goods_option_title'][$op]."','option-'.$tData.");
			//$sql = "INSERT INTO bbse_commerce_goods (goods_code, goods_name, goods_option_check) VALUES ('".$V['goods_option_item_unique_code'][$op]."','".$V['goods_option_title'][$op]."','option-$tData')";
			//$wpdb->query($sql);
			
			//$sql = "INSERT INTO tbl_douzone_code (goods_code, goods_douzone_code, goods_idx) VALUES ('".$V['goods_option_item_unique_code'][$op]."','".$V['goods_option_item_douzone_code'][$op]."','$tData')";
			//$wpdb->query($sql);
			
			$inCnt++;
		}
	}

	//신규 상품정보 추출 : 품절 -> 정상판매 체크
	if($orderInfo['soldout_notice_use']=='on' && ($orderInfo['soldout_notice_sms']=='sms' || $orderInfo['soldout_notice_email']=='email') && $V['goods_display']=='display'){
		$newGdata = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$tData."'");
		$newSoldout=bbse_commerce_goodsSoldoutCheck($newGdata);

		if(($oldSoldout==true || $oldGdata->goods_display=='hidden') && $newSoldout==false){ // 품절 -> 정상판매인 경우 sms, email 알림 발송
			bbse_commerce_soldout_noticeSend($tData);
		}
	}

	echo "success|||".$idx;
	if(is_object($SITEMAPS)) $SITEMAPS->tryWriteMapFile();
	exit;
}
elseif($V['tMode']=='delete' && trim($V['tIdx'])){
	/*
	for($i=0;$i<count($V[check]);$i++){
			$vlu = $V[check][$i];

			if($i>0) $where.=" OR ";
			$where .= " idx='".$V[check][$i]."'";

	}
	*/
	if(is_object($SITEMAPS)) $SITEMAPS->tryWriteMapFile();
}
else{
	echo "nonData";
	exit;
}
?>
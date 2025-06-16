<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$V = $_POST;

if($V['mType']=='step01'){
	$now_time=current_time('timestamp');

	if(!$_FILES['dbFile']['tmp_name'] || !is_uploaded_file($_FILES['dbFile']['tmp_name'])){
		echo "errorFileName";
		exit;
	}

	$checkArr=Array("csv","CSV"); // Array("csv","CSV","xls","XLS","xlsx","XLSX");
	$file_arr = explode(".", $_FILES['dbFile']['name']);
	$file_type = strtolower($file_arr[count($file_arr)-1]);
	if(!in_array($file_type,$checkArr)) {
		echo "errorFileExtend";
		exit;
	}

	$r_file_new="bbse_goods_data_".date("YmdHis",$now_time).".".$file_type;
	$r_attach_new=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_file_new;

	if(!@move_uploaded_file($_FILES['dbFile']['tmp_name'],$r_attach_new)){ 
		echo "errorFileUpload";
		exit;
	}

	if(!class_exists('idna_convert')){
		include BBSE_COMMERCE_PLUGIN_ABS_PATH."class/idna_convert.class.php";
	}

	$IDN = new idna_convert(array('idn_version' => '2008'));
	$blogHomeUrl = $IDN->encode(home_url());
	$fileUpBaseUrl=str_replace(home_url(),$blogHomeUrl,BBSE_COMMERCE_UPLOAD_BASE_URL);

	$str_result = wp_remote_get($fileUpBaseUrl."bbse-commerce/"."bbse_goods_data_".date("YmdHis",$now_time).".".$file_type);

	if($str_result['response']['code']!='200'){
		echo "errorFileUpload";
		exit;
	}

	$dbData=bbse_commerce_csv_check($r_file_new, ',');

	if($dbData['fieldCnt']!='15'){
		echo "errorFieldCount";
		exit;
	}

	echo "success|||".$_FILES['dbFile']['name']."|||".$r_file_new."|||".$dbData['rowCnt']."|||".$dbData['fieldCnt'];
}
elseif($V['mType']=='step02'){
	$r_category=$V['dbCategory'];

	$r_file_new=$V['cvsUploadFile'];
	$r_attach_new=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_file_new;

	$dbData=bbse_commerce_csv_to_array($r_file_new, ',');
	if(sizeof($dbData)<='0'){
		echo "errorDataEmpty";
		exit;
	}

	$inResult=bbse_commerce_goods_array_in_db($dbData,$r_category);

	@unlink($r_attach_new);

	$failCount=$inResult['errCnt_goods_name']+$inResult['errCnt_zero_consumer_price']+$inResult['errCnt_zero_goods_price']+$inResult['errCnt_low_goods_price']+$inResult['errCnt_null_goods_img']; // 실패 개수
	
	if($failCount>0) $failData=base64_encode(serialize($inResult));

	$sucCount=$V['dbTotalRow']-$failCount; // 성공개수 개수
	$catName = $wpdb->get_var("SELECT c_name FROM bbse_commerce_category WHERE idx='".$r_category."' LIMIT 1");

	echo "success|||".$V['cvsOriginalFile']."|||".$catName."|||".$V['dbTotalRow']."|||".$sucCount."|||".$failCount."|||".$inResult['errCnt_goods_name']."|".$inResult['errCnt_zero_consumer_price']."|".$inResult['errCnt_zero_goods_price']."|".$inResult['errCnt_low_goods_price']."|".$inResult['errCnt_null_goods_img']."|||".$failData;
}
elseif($V['mType']=='failCsvMake'){
	$failData=unserialize(base64_decode($V['failData']));

	$failCount=$failData['errCnt_goods_name']+$failData['errCnt_zero_consumer_price']+$failData['errCnt_zero_goods_price']+$failData['errCnt_low_goods_price']+$failData['errCnt_null_goods_img']; // 실패 개수
	$tArray=Array();
	for($i=0;$i<$failCount;$i++){
		$tArray[]=$failData[$i];
	}

	$tFile=str_replace(".CSV",".csv",$V['cvsMakeOriginalFile']);
	$tFileName=explode(".csv",$tFile);
	$downTargetFile=$tFileName['0']."-오류 데이터.csv";

	$header=Array("상품명(필수)","고유번호","바코드","위치정보","제조사","원산지","재고수량","소비자가(필수)","판매가(필수)","적립금","상품이미지(필수)","SEO 타이틀","SEO 설명","SEO 키워드","상품상세설명");
	array_unshift($tArray, $header);

	if($failCount>0) bbse_commerce_array_to_csv_download($tArray,$downTargetFile,",");
}
elseif($V['mType']=='imgConverting'){
	$tIdx=$V['tIdx'];

	$row = $wpdb->get_row("SELECT * FROM bbse_commerce_csv_goods WHERE idx='".$tIdx."'");
	if($row->goods_idx && $row->goods_code){

		if(!class_exists('idna_convert')){
			include BBSE_COMMERCE_PLUGIN_ABS_PATH."class/idna_convert.class.php";
		}

		$IDN = new idna_convert(array('idn_version' => '2008'));
		$blogHomeUrl = $IDN->encode(home_url());
		$fileUpBaseUrl=str_replace(home_url(),$blogHomeUrl,BBSE_COMMERCE_UPLOAD_BASE_URL);

		$imgWebUrl=$fileUpBaseUrl."goods-images/".$row->goods_img;
		$imgAbsUrl=BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/".$row->goods_img;
		if(file_exists($imgAbsUrl)){
			$imgId=bbse_commerce_external_image_sideload($imgWebUrl);
			if($imgId>0){
				$wpdb->query("UPDATE bbse_commerce_goods SET goods_add_img_cnt='1', goods_basic_img='".$imgId."', goods_add_img='".$imgId."' WHERE idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
				$wpdb->query("DELETE FROM bbse_commerce_csv_goods WHERE idx='".$row->idx."' AND goods_idx='".$row->goods_idx."' AND goods_code='".$row->goods_code."'");
				@unlink(BBSE_COMMERCE_UPLOAD_BASE_PATH."goods-images/".$row->goods_img);

				echo "success|||".$tIdx."|||[이미지 변환 완료]";
				exit;
			}
			else{
				echo "success|||".$tIdx."|||<span style='color: #ED1C24;'>[실패!] 이미지 변환 오류</span>";
				exit;
			}
		}
		else{
			echo "success|||".$tIdx."|||<span style='color: #ED1C24;'>[실패!] 이미지 없음</span>";
			exit;
		}
	}
	else{
		echo "success|||".$tIdx."|||<span style='color: #ED1C24;'>[실패!] 정보 없음</span>";
		exit;
	}
}
?>
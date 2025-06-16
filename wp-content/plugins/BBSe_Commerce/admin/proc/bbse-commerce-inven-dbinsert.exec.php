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

	$r_file_new="bbse_inven_data_".date("YmdHis",$now_time).".".$file_type;
	$r_attach_new=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_file_new;

	if(!@move_uploaded_file($_FILES['dbFile']['tmp_name'],$r_attach_new)){ 
		echo "errorFileUpload";
		exit;
	}

	$str_result = wp_remote_get(BBSE_COMMERCE_UPLOAD_BASE_URL."/bbse-commerce/"."bbse_inven_data_".date("YmdHis",$now_time).".".$file_type);

	if($str_result['response']['code']!='200'){
		echo "errorFileUpload";
		exit;
	}

	$dbData=bbse_commerce_csv_check($r_file_new, ',');
 
	if($dbData['fieldCnt']!='6'){
		echo "errorFieldCount";
		exit;
	}

	echo "success|||".$_FILES['dbFile']['name']."|||".$r_file_new."|||".$dbData['rowCnt']."|||".$dbData['fieldCnt'];
}
elseif($V['mType']=='step02'){

	$r_file_new=$V['cvsUploadFile'];
	$r_attach_new=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_file_new;

	$dbData=bbse_commerce_csv_to_array($r_file_new, ',');
	if(sizeof($dbData)<='0'){
		echo "errorDataEmpty";
		exit;
	}
	
	$inResult=bbse_commerce_inven_array_in_db($dbData);
	@unlink($r_attach_new);

	//$failCount=$inResult['errCnt_null_id']+$inResult['errCnt_duplicate_id']+$inResult['errCnt_null_goodsCode']+$inResult['errCnt_null_currnetCount']+$inResult['errCnt_null_noticeCount']+$inResult['errCnt_null_managerId']+$inResult['$errCnt_null_storageCode']; // 실패 개수
	//$failCount=$inResult['errCnt_null_id']+$inResult['errCnt_duplicate_id']+$inResult['errCnt_null_currnetCount']+$inResult['errCnt_null_noticeCount']+$inResult['errCnt_null_managerId']+$inResult['$errCnt_null_storageCode']; // 실패 개수
	$failCount=$inResult['errCnt_null_id']+$inResult['errCnt_duplicate_id']+$inResult['errCnt_null_douzoneCode']+$inResult['errCnt_null_currnetCount']+$inResult['errCnt_null_noticeCount']+$inResult['errCnt_null_managerId']+$inResult['$errCnt_null_storageCode']; // 실패 개수
	if($failCount>0) $failData=base64_encode(serialize($inResult));

	$sucCount=$V['dbTotalRow']-$failCount; // 성공개수 개수

	echo "success|||".$V['cvsOriginalFile']."|||".$V['dbTotalRow']."|||".$sucCount."|||".$failCount."|||".$inResult['errCnt_null_id']."|".$inResult['errCnt_duplicate_id']."|".$inResult['errCnt_null_goodsCode']."|".$inResult['errCnt_null_currnetCount']."|".$inResult['errCnt_null_noticeCount']."|||".$inResult['errCnt_null_managerId']."|||".$inResult['errCnt_null_storageCode']."|||".$failData;
}
elseif($V['mType']=='failCsvMake'){ 
	$failData=unserialize(base64_decode($V['failData']));

	$failCount=$failData['errCnt_null_id']+$failData['errCnt_duplicate_id']+$failData['errCnt_null_goodsCode']+$failData['errCnt_null_currnetCount']+$failData['errCnt_null_noticeCount']+$failData['errCnt_null_managerId']+$failData['errCnt_null_storageCode']; // 실패 개수
	$tArray=Array();
	for($i=0;$i<$failCount;$i++){
		$tArray[]=$failData[$i];
	}

	$tFile=str_replace(".CSV",".csv",$V['cvsMakeOriginalFile']);
	$tFileName=explode(".csv",$tFile);
	$downTargetFile=$tFileName['0']."-오류 데이터.csv";

	$header=Array("제품명","제품코드","현재수량","알림수량","창고코드");
	array_unshift($tArray, $header);

	if($failCount>0) bbse_commerce_array_to_csv_download($tArray,$downTargetFile,",");
}
?>
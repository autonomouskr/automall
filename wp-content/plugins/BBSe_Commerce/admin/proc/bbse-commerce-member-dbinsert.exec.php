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

	$r_file_new="bbse_member_data_".date("YmdHis",$now_time).".".$file_type;
	$r_attach_new=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_file_new;

	if(!@move_uploaded_file($_FILES['dbFile']['tmp_name'],$r_attach_new)){ 
		echo "errorFileUpload";
		exit;
	}

	$str_result = wp_remote_get(BBSE_COMMERCE_UPLOAD_BASE_URL."/bbse-commerce/"."bbse_member_data_".date("YmdHis",$now_time).".".$file_type);

	if($str_result['response']['code']!='200'){
		echo "errorFileUpload";
		exit;
	}

	$dbData=bbse_commerce_csv_check($r_file_new, ',');

	if($dbData['fieldCnt']!='16'){
		echo "errorFieldCount";
		exit;
	}

	echo "success|||".$_FILES['dbFile']['name']."|||".$r_file_new."|||".$dbData['rowCnt']."|||".$dbData['fieldCnt'];
}
elseif($V['mType']=='step02'){
	$pwType=$V['pwType'];
	$pwDirect=$V['pwDirect'];

	if($pwType=='direct'){
		if(!trim($pwDirect)){
			echo "errorNullDirectPassword";
			exit;
		}
		elseif(strlen($pwDirect)>16){
			echo "errorLongDirectPassword";
			exit;
		}
	}

	$r_file_new=$V['cvsUploadFile'];
	$r_attach_new=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_file_new;

	$dbData=bbse_commerce_csv_to_array($r_file_new, ',');
	if(sizeof($dbData)<='0'){
		echo "errorDataEmpty";
		exit;
	}

	$inResult=bbse_commerce_member_array_in_db($dbData,$pwType,$pwDirect);
	@unlink($r_attach_new);

	$failCount=$inResult['errCnt_null_id']+$inResult['errCnt_duplicate_id']+$inResult['errCnt_null_name']+$inResult['errCnt_null_password']+$inResult['errCnt_long_password']; // 실패 개수
	
	if($failCount>0) $failData=base64_encode(serialize($inResult));

	$sucCount=$V['dbTotalRow']-$failCount; // 성공개수 개수

	echo "success|||".$V['cvsOriginalFile']."|||".$pwType.",".$pwDirect."|||".$V['dbTotalRow']."|||".$sucCount."|||".$failCount."|||".$inResult['errCnt_null_id']."|".$inResult['errCnt_duplicate_id']."|".$inResult['errCnt_null_name']."|".$inResult['errCnt_null_password']."|".$inResult['errCnt_long_password']."|||".$failData;
}
elseif($V['mType']=='failCsvMake'){
	$failData=unserialize(base64_decode($V['failData']));

	$failCount=$failData['errCnt_null_id']+$failData['errCnt_duplicate_id']+$failData['errCnt_null_name']+$failData['errCnt_null_password']+$failData['errCnt_long_password']; // 실패 개수
	$tArray=Array();
	for($i=0;$i<$failCount;$i++){
		$tArray[]=$failData[$i];
	}

	$tFile=str_replace(".CSV",".csv",$V['cvsMakeOriginalFile']);
	$tFileName=explode(".csv",$tFile);
	$downTargetFile=$tFileName['0']."-오류 데이터.csv";

	$header=Array("회원아이디(필수)","비밀번호","이름(필수)","이메일","이메일 수신여부(수신:1)","전화번호(02-123-1234)","휴대전화번호(010-123-1234)","SMS 수신여부(수신:1)","우편번호(123-456)","주소","상세주소","생년월일(1900-01-01)","성별(남자:1,여자:2)","직업","적립금","관리자메모");
	array_unshift($tArray, $header);

	if($failCount>0) bbse_commerce_array_to_csv_download($tArray,$downTargetFile,",");
}
?>
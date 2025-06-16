<?php

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';

header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = storagelist.xls" ); 
header( "Content-Description: PHP7 Generated Data" );

global $wpdb;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$addQuery = "";
if($currUserID != "autopole3144"){
    $addQuery .= " AND manager_id = '".$currUserID."' ";
}

$storages = $wpdb->get_results("
	SELECT	*
	FROM	tbl_storage
	WHERE 1=1
    ".$addQuery."
      AND delete_yn != 'Y'
	ORDER BY idx DESC
");
$total = $wpdb->get_var("
	SELECT	COUNT(*)
	FROM	tbl_storage
	WHERE 1=1
    ".$addQuery."
      AND delete_yn != 'Y'
	ORDER BY idx DESC
");
$result = '
	<table border="1">
		<tr>
			<th>번호</th>
			<th>창고명</th>
			<th>창고코드</th>
			<th>담당자ID</th>
			<th>담당자명</th>
            <th>담당자연락처</th>
			<th>우편번호</th>
			<th>주소</th>
			<th>상세주소</th>
		</tr>
';
$index = 0;
foreach ($storages as $key => $value) {
    
    $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id = '".$value->manager_id."' and leave_yn != '1'");
    
	$result .= '
		<tr>
			<td>'.($total - $index++).'</td>
			<td>'.$value->storage_name.'</td>
			<td>'.$value->storage_code.'</td>
            <td>'.$value->manager_id.'</td>
			<td>'.$manager->manager_name.'</td>
			<td>'.$manager->hp.'</td>
			<td>'.$value->zipcode.'</td>
			<td>'.$value->addr1.'</td>
			<td>'.$value->addr2.'</td>
			
		</tr>
	';
}
$result .= '</table>';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">";
echo $result;
?>
<?php

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';

header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = locationlist.xls" ); 
header( "Content-Description: PHP7 Generated Data" );

global $wpdb;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;

$addQuery = "";
$addQuery2 = "";
if($currUserID != "autopole3144"){
    $addQuery .= " AND manager_id = '".$currUserID."' ";
    $storage = $wpdb->get_row("select * from tbl_storage where 1=1 ".$addQuery." and delete_yn != 'Y'");
    
    $addQuery2 .= " AND storage_code = '".$storage->storage_code."' ";
}

$items = $wpdb->get_results("
	SELECT	*
	FROM	tbl_locations
	WHERE	delete_yn != 'Y' ".$addQuery2." ORDER BY idx DESC");

$total = $wpdb->get_var("
	SELECT	COUNT(*)
	FROM	tbl_locations
	WHERE	delete_yn != 'Y' ".$addQuery2." ORDER BY idx DESC");

$result = '
	<table border="1">
		<tr>
			<th>번호</th>
			<th>창고명</th>
			<th>창고코드</th>
			<th>섹션명</th>
            <th>섹션위치</th>
		</tr>
';
$index = 0;
foreach ($items as $key => $value) {
    
    $storage = $wpdb->get_row("select * from tbl_storage where storage_code = '".$value->storage_code."' and delete_yn != 'Y'");
    
	$result .= '
		<tr>
			<td>'.($index++).'</td>
			<td>'.$storage->storage_name.'</td>
            <td>'.$storage->storage_code.'</td>
            <td>'.$value->rack_code.'</td>
            <td>'.$value->rack_code.$value->location_x.$value->location_y.'</td>
		</tr>
	';
}
$result .= '</table>';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">";
echo $result;
?>
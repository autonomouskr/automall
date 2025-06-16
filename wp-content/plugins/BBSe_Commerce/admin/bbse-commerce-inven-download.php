<?php

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';

header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = invenlist.xls" ); 
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
	FROM	tbl_inven
	WHERE	delete_yn != 'Y' ".$addQuery2." ORDER BY idx DESC");

$total = $wpdb->get_var("
	SELECT	COUNT(*)
	FROM	tbl_inven
	WHERE	delete_yn != 'Y' ".$addQuery2." ORDER BY idx DESC");

$result = '
	<table border="1">
		<tr>
			<th>번호</th>
			<th>제품명</th>
			<th>제품코드</th>
			<th>창고위치</th>
            <th>창고코드</th>
            <th>우편번호</th>
			<th>주소</th>
            <th>상세주소</th>
			<th>담당자명</th>
            <th>담당자ID</th>
            <th>등록일</th>
		</tr>
';
$index = 0;
foreach ($items as $key => $value) {
    
    $storage = $wpdb->get_row("select * from tbl_storage where storage_code = '".$value->storage_code."' and delete_yn != 'Y'");
    $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id = '".$storage->manager_id."' and leave_yn != '1'");
    
	$result .= '
		<tr>
			<td>'.($total - $index++).'</td>
			<td>'.$value->goods_name.'</td>
			<td>'.$value->goods_code.'</td>
            <td>'.$storage->storage_code.'</td>
			<td>'.$storage->storage_name.'</td>
			<td>'.$storage->zipcode.'</td>
			<td>'.$storage->addr1.'</td>
			<td>'.$storage->addr2.'</td>
            <td>'.$manager->user_id.'</td>
			<td>'.$manager->manager_name.'</td>
            <td>'.$value->reg_date.'</td>
		</tr>
	';
}
$result .= '</table>';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">";
echo $result;
?>
<?php

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';

header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = inven-list.xls" ); 
header( "Content-Description: PHP7 Generated Data" );

global $wpdb;
$invens = $wpdb->get_results('
	SELECT	*
	FROM	tbl_inven
	WHERE	delete_yn != "Y"
	ORDER BY idx DESC
');
$total = $wpdb->get_var('
	SELECT	COUNT(*)
	FROM	tbl_inven
	WHERE	delete_yn != "Y"
	ORDER BY idx DESC
');
$result = '
	<table border="1">
		<tr>
			<th>번호</th>
			<th>제품명</th>
			<th>제품코드</th>
			<th>재고수량</th>
			<th>알림설정수량</th>
			<th>창고코드</th>
            <th>창고명</th>
			<th>우편번호</th>
			<th>주소</th>
			<th>상세주소</th>
			<th>담당자명</th>
			<th>담당자연락처</th>
		</tr>
';
$index = 0;

foreach ($invens as $key => $value) {
    $storage = $wpdb->get_row("select * from tbl_storage where storage_code = '".$value->storage_code."'");
    $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id = '".$storage->user_id."'");
    
    $result .= '
		<tr>
			<td>'.($total - $index++).'</td>
			<td>'.$value->goods_name.'</td>
			<td>'.$value->goods_code.'</td>
			<td>'.$value->current_count.'</td>
			<td>'.$value->notice_count.'</td>
			<td>'.$storage->storage_code.'</td>
            <td>'.$storage->storage_name.'</td>
            <td>'.$storage->zipcode.'</td>
			<td>'.$storage->addr1.'</td>
			<td>'.$storage->addr2.'</td>
			<td>'.$manager->manager_name.'</td>
            <td>'.$manager->hp.'</td>
		</tr>
	';
}
$result .= '</table>';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">";
echo $result;
?>
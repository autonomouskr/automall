<?php

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
 include $includeURL['0'].'/wp-load.php';

header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = inout-list.xls" ); 
header( "Content-Description: PHP7 Generated Data" );

global $wpdb;
$inoutList = $wpdb->get_results('
	SELECT	*
	FROM	tbl_inout
	WHERE	delete_yn != "Y"
	ORDER BY id DESC
');
$total = $wpdb->get_var('
	SELECT	COUNT(*)
	FROM	tbl_inout
	WHERE	delete_yn != "Y"
	ORDER BY id DESC
');
$result = '
	<table border="1">
		<tr>
			<th>번호</th>
			<th>구분</th>
			<th>제품명</th>
			
			<th>제품코드</th>
			<th>수량</th>
			<th>창고위치</th>
			
			<th>우편번호</th>
			<th>주소</th>
			<th>상세주소</th>
			<th>처리자</th>
			<th>처리일시</th>
		</tr>
';
$index = 0;
foreach ($inoutList as $key => $value) {
    $storage = $wpdb->get_row("select * from tbl_storage where storage_code = '".$value->storage_code."'");
    $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id = '".$value->manager_id."'");
    $good = $wpdb->get_row("select * from bbse_commerce_goods where goods_code = '".$value->goods_code."'");
    
    if($value->status == "IN"){
        $status_name = "입고";
    }else if($value->status == "OT"){
        $status_name = "출고";
    }else if($value->status == "IV"){
        $status_name = "재고현황";
    }
    
	$result .= '
		<tr>
			<td>'.($total - $index++).'</td>
			<td>'.$status_name.'</td>
			<td>'.$good->goods_name.'</td>
			
			<td>'.$value->goods_code.'</td>
			<td>'.$value->quantity.'</td>
			<td>'.$storage->storage_name.'</td>
			
			<td>'.$storage->zipcode.'</td>
			<td>'.$storage->addr1.'</td>
			<td>'.$storage->addr2.'</td>
			<td>'.$manager->name.'</td>
			<td>'.$value->processing_date.'</td>
		</tr>
	';
}
$result .= '</table>';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">";
echo $result;
?>
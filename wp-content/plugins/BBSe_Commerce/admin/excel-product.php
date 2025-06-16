<?php
if(!defined(ABSPATH)){
    $pagePath = explode('/wp-content/', dirname(__FILE__));
    include_once(str_replace('wp-content/' , '', $pagePath[0] . '/wp-load.php'));
}

global $wpdb;
$products = $wpdb->get_results('SELECT * FROM bbse_commerce_goods ORDER BY idx DESC');

header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = products_".current_time("YmdHis").".xls" ); 
header( "Content-Description: PHP7 Generated Data" );

$output = '
<table border="1">
	<tr>
		<th>번호</th>
		<th>카테고리</th>
		<th>상품코드</th>
		<th>상품명</th>
		
		<th>상품설명</th>
		
		<th>상품옵션</th>
		
		<th>고유번호</th>
		<th>바코드</th>
		
		<th>위치정보</th>
		<th>제조사</th>
		<th>원산지</th>
		
		<th>소비자가</th>
		<th>판매가</th>
		<th>부가세</th>
		
		<th>1회구매가능 개수</th>
		<th>개별배송비</th>
		<th>개별적립금</th>
	</tr>
';
$index = 1;
foreach ($products as $key => $value) {
	$cats = explode('|', $value->goods_cat_list);
	$cat_name = array();
	foreach ($cats as $k => $v) {
		if(empty($v)) continue;
		$cat_name[]= $wpdb->get_var('SELECT c_name FROM bbse_commerce_category WHERE idx = "'.$v.'"');
	}
	$option = unserialize($value->goods_option_basic);
	$option_name = '';
	if(!empty($option['goods_option_1_count'])){
		$option_name .= $option['goods_option_1_title'].': ';
		$t = array();
		foreach ($option['goods_option_1_item'] as $k => $v) {
			$t[]= $v;
		}
		$option_name .= implode(',', $t);
	}
	if(!empty($option['goods_option_2_count'])){
		$option_name .= ' '.$option['goods_option_2_title'].': ';
		$t = array();
		foreach ($option['goods_option_2_item'] as $k => $v) {
			$t[]= $v;
		}
		$option_name .= implode(',', $t);
	}
	$output .='
		<tr>
			<td>'.$index++.'</td>
			<td>'.implode(',', $cat_name).'</td>
			<td>'.$value->goods_code.'</td>
			<td>'.$value->goods_name.'</td>
			
			<td>'.$value->goods_description.'</td>
			
			<td>'.$option_name.'</td>
			
			<td>'.$value->goods_unique_code.'</td>
			<td>'.$value->goods_barcode.'</td>
			
			<td>'.$value->goods_location_no.'</td>
			<td>'.$value->goods_company.'</td>
			<td>'.$value->goods_local.'</td>
			
			<td>'.$value->goods_consumer_price.'</td>
			<td>'.$value->goods_price.'</td>
			<td>'.$value->goods_tax.'</td>
			
			<td>'.$value->max_cnt.'</td>
			<td>'.$value->goods_ship_price.'</td>
			<td>'.$value->goods_earn.'</td>
		</tr>
	';
}
$output .='
</table>
';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">";
echo $output;
?>
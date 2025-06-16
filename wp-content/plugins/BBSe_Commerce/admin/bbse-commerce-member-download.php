<?php

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';

header( "Content-type: application/vnd.ms-excel" ); 
header( "Content-type: application/vnd.ms-excel; charset=utf-8");
header( "Content-Disposition: attachment; filename = members.xls" ); 
header( "Content-Description: PHP7 Generated Data" );

global $wpdb;
$members = $wpdb->get_results('
	SELECT	*
	FROM	bbse_commerce_membership
	WHERE	leave_yn != "1"
	ORDER BY user_no DESC
');
$total = $wpdb->get_var('
	SELECT	COUNT(*)
	FROM	bbse_commerce_membership
	WHERE	leave_yn != "1"
	ORDER BY user_no DESC
');
$result = '
	<table border="1">
		<tr>
			<th>번호</th>
			<th>아이디</th>
			<th>이름</th>
			
			<th>이메일</th>
			<th>전화번호</th>
			<th>휴대전화번호</th>
			
			<th>우편번호</th>
			<th>주소</th>
			<th>상세주소</th>
			<th>생년월일</th>
			<th>성별</th>
			<th>직업</th>
			
			<th>적립금</th>
		</tr>
';
$index = 0;
foreach ($members as $key => $value) {
	$result .= '
		<tr>
			<td>'.($total - $index++).'</td>
			<td>'.$value->user_id.'</td>
			<td>'.$value->name.'</td>
			
			<td>'.$value->email.'</td>
			<td>'.$value->phone.'</td>
			<td>'.$value->hp.'</td>
			
			<td>'.$value->zipcode.'</td>
			<td>'.$value->addr1.'</td>
			<td>'.$value->addr2.'</td>
			
			<td>'.$value->birth.'</td>
			<td>'.(empty($value->sex) ? '':($value->sex == 1 ? '남자':'여자')).'</td>
			<td>'.$value->job.'</td>
			<td>'.$value->mileage.'</td>
		</tr>
	';
}
$result .= '</table>';

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">";
echo $result;
?>
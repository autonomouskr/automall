<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
</head>
<body>
<?php
	$mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$_REQUEST['tData']."'");
	$cData = $wpdb->get_row("SELECT * FROM bbse_commerce_membership_class WHERE no='".$mData->user_class."'");
?>

	<div class="wrap">
		<div class="clearfix" style="height:4px;"></div>
		<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="100px;"><col width=""></colgroup>
			<tr>
				<th>아이디 : </th>
				<td><?php echo $mData->user_id;?></td>
			</tr>
			<tr>
				<th>이름 : </th>
				<td><?php echo $mData->name;?></td>
			</tr>
			<tr>
				<th>등급 : </th>
				<td><?php echo $cData->class_name."(".$mData->user_class.")";?></td>
			</tr>
			<tr>
				<th>이메일 : </th>
				<td><?php echo $mData->email;?></td>
			</tr>
			<tr>
				<th>전화번호 : </th>
				<td><?php echo $mData->phone;?></td>
			</tr>
			<tr>
				<th>휴대폰 : </th>
				<td><?php echo $mData->hp;?></td>
			</tr>
			<tr>
				<th>적립금 : </th>
				<td><?php echo number_format($mData->mileage)."원";?></td>
			</tr>
		</table>
	</div>
</body>
</html>

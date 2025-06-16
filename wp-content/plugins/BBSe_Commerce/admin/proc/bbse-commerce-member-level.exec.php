<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

if($curUserPermision != "administrator"){
	wp_die("접근 권한이 없습니다.");
}else{
	 
	if($V['tMode']=="add") {
		if(!$V['class_name']) wp_die("잘못된 접근입니다.");
		else{
			$wpdb->query("INSERT INTO 	bbse_commerce_membership_class SET class_name='".$V['class_name']."'");
			wp_redirect("/wp-admin/admin.php?page=bbse_commerce_member&cType=level");
		}
	}else if($V['tMode']=="mod") {
		if(!$V['procNo'] || !$V['class_name'.$V['procNo']] || !$V['use_sale'.$V['procNo']]) wp_die("잘못된 접근입니다.");
		else{
			$wpdb->query("
				UPDATE 	bbse_commerce_membership_class 
				SET 	class_name='".$V['class_name'.$V['procNo']]."', 
						use_sale='".$V['use_sale'.$V['procNo']]."',
						discount = '".intval($V['mem_discount'.$V['procNo']])."',
						auto_cnt = '".intval($V['mem_auto_cnt'.$V['procNo']])."',
						auto_total = '".intval($V['mem_auto_total'.$V['procNo']])."'
				WHERE no='".$V['procNo']."'");
			wp_redirect("/wp-admin/admin.php?page=bbse_commerce_member&cType=level");
		}
	}else if($V['tMode']=="del") {
		if(!$V['procNo']) wp_die("잘못된 접근입니다.");
		else{
			$wpdb->query("DELETE FROM bbse_commerce_membership_class WHERE no='".$V['procNo']."'");
			wp_redirect("/wp-admin/admin.php?page=bbse_commerce_member&cType=level");
		}
	}
}
exit;
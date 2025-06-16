<?php
//주문목록
$tMode=$_REQUEST['tMode'];
$tData=$_REQUEST['tData'];

if(!$tMode) $tMode="list";

bbse_commerce_chk_order_status(); // 취소완료, 배송완료, 구매확정 처리

require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/order/bbse-commerce-order-".$tMode.".php");
//require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dumy_data.php");

?>

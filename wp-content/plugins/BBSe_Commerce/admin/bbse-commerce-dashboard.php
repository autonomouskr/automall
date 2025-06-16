<?php
bbse_commerce_chk_order_status(); // 취소완료, 배송완료, 구매확정 처리
?>

<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>대시보드</h2>
		<hr>
	</div>

	<div style="width:100%;">
		<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dashboard/bbse-commerce-dashboard-order.php");?>
	</div>

	<div class="clearfix"></div>
	<div style="width:99%;">
		<div style="width:49%;float:left;">
			<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dashboard/bbse-commerce-dashboard-qna.php");?>
		</div>
		<div style="width:2%;float:left;">&nbsp;</div>
		<div style="width:49%;float:left;">
			<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dashboard/bbse-commerce-dashboard-review.php");?>
		</div>
	</div>

	<div class="clearfix"></div>
	<div style="width:99%;">
		<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dashboard/bbse-commerce-dashboard-statistics.php");?>
	</div>
</div>
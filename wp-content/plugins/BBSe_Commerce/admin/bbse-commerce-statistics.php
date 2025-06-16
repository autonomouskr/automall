<?php
$cType=($_REQUEST['cType'])?$_REQUEST['cType']:"user";
?>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>통계정산관리</h2>
		<hr />
	</div>

	<div class="tabWrap">
	  <ul class="tabList">
	  	<li <?php echo ($cType=='user')?"class=\"active\"":"";?> style="width:180px;"><a href="admin.php?page=bbse_commerce_statistics&cType=user">업체별청구금액</a></li>
		<li <?php echo ($cType=='order')?"class=\"active\"":"";?> style="width:180px;"><a href="admin.php?page=bbse_commerce_statistics&cType=order">기간별</a></li>
		<li <?php echo ($cType=='goods')?"class=\"active\"":"";?> style="width:180px;"><a href="admin.php?page=bbse_commerce_statistics&cType=goods">판매상품 (Best 100)</a></li>
		<li <?php echo ($cType=='member')?"class=\"active\"":"";?> style="width:180px;"><a href="admin.php?page=bbse_commerce_statistics&cType=member">구매회원 (Best 100)</a></li>
	  </ul>
	  <div class="clearfix"></div>
	</div>

	<div class="clearfix" style="margin-top:30px"></div>

	<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/statistics/bbse-commerce-statistics-".$cType.".php");?>
</div>




<?php
$cType=($_REQUEST['cType'])?$_REQUEST['cType']:"csv";
?>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>제품정보 일괄 등록</h2>
		<hr />
	</div>

	<div class="clearfix" style="margin-top:30px"></div>

	<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dbinsert/bbse-commerce-inven-dbinsert-".$cType.".php");?>
</div>




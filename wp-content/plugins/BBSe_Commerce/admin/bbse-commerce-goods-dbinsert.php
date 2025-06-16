<?php
$cType=($_REQUEST['cType'])?$_REQUEST['cType']:"csv";
?>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>상품 대량등록(CSV)</h2>
		<hr />
	</div>

	<div class="tabWrap">
	  <ul class="tabList">
		<li <?php echo ($cType=='csv')?"class=\"active\"":"";?> style="width:180px;"><a href="admin.php?page=bbse_commerce_goods_dbinsert&cType=csv">CSV 업로드/ DB 저장</a></li>
		<li <?php echo ($cType=='image')?"class=\"active\"":"";?> style="width:180px;"><a href="admin.php?page=bbse_commerce_goods_dbinsert&cType=image">이미지 변환</a></li>
	  </ul>
	  <div class="clearfix"></div>
	</div>

	<div class="clearfix" style="margin-top:30px"></div>

	<?php require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/dbinsert/bbse-commerce-goods-dbinsert-".$cType.".php");?>
</div>




<script language="javascript">
	jQuery(document).ready(function($) {
		$("#sort_table").tablesorter({});
		$("#sort_table").tableDnD();
	});
</script>
<?php
global $wpdb;
$products  = $wpdb->get_results('
	SELECT 		*
	FROM		bbse_commerce_goods
	WHERE		'.getCategoryQuery($_GET['cat']).'
	ORDER BY	list_order ASC, idx DESC
');
$cats = $wpdb->get_results('SELECT * FROM bbse_commerce_category WHERE c_name != "미분류" AND depth_2 = 0 AND depth_3 = 0');
if($_POST){
	$index = 0;
	foreach ($_POST['id'] as $key => $value) {
		$wpdb->query("
			UPDATE 	bbse_commerce_goods
			SET		list_order = '".++$index."'
			WHERE	idx = '".$value."' 
		");
	}
	echo '
		<script>
			location.href = document.referrer;
		</script>
	';
	die();
}
?>
<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>상품순서변경</h2>
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:20px;">
		<form method="get">
			<input type="hidden" name="page" value="bbse_commerce_goods_order" />
			<select name="cat">
				<?php
					foreach ($cats as $key => $value) {
						echo '
							<option value="'.$value->idx.'" '.($value->idx == $_GET['cat'] ? 'selected':'').'>'.$value->c_name.'</option>
						';
					}
				?>
				<input type="submit" name="" value="검색" class="button" style="margin: 0 0 5px 5px;" />
			</select>
		</form>
		<form name="goodsFrm" id="goodsFrm" method="post">
			<input type="hidden" name="tMode" id="tMode" value="">
			<input type="submit" name="" value="순서저장" class="button" style="margin: 0 0 5px;" />
			<div class="des" style="display: inline-block;vertical-align: middle;margin: 5px 5px 0;">
				<p>
					마우스 드래그하여 위아래로 이동하셔서 순서 변경하시기 바랍니다.
					<strong style="color: #d00;">1차 카테고리에 속한 상품에 대해서만 순서 변경이 가능합니다.</strong>
				</p>
			</div>
			<div style="width:100%;">
				<table class="dataTbls normal-line-height collapse tablesorter" id="sort_table">
					<thead>
						<tr>
							<th style="width: 5%">순서</th>
							<th style="width: 10%">이미지</th>
							<th style="width: 10%">카테고리</th>
							<th>상품명</th>
							<th>상태</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($_GET['cat'])){
								echo '
								<tr>
									<td colspan="5" align="center" >1차 카테고리를 검색해주세요.</td>
								</tr>
								';
							}
							else{
								$status_arr = array(
									'display'	=> '노출',
									'hidden'	=> '비노출',
									'soldout'	=> '노출(품절)',
									'copy'		=> '복사',
									'trash'		=> '휴지통',
								);
								$index = 0;
								foreach ($products as $key => $value) {
									$basicImg = '';
									if($value->goods_basic_img) $basicImg = wp_get_attachment_image_src($value->goods_basic_img);
									else{
										$imageList=explode(",",$value->goods_add_img);
										if(sizeof($imageList)>'0') $basicImg = wp_get_attachment_image_src($imageList['0']);
										else $basicImg['0'] = BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
									}
									
									echo '
										<tr>
											<td style="text-align:center">
												'.++$index.'
												<input type="hidden" name="id[]" value="'.$value->idx.'" />
											</td>
											<td style="text-align:center"><img style="height: 40px;" src="'.$basicImg[0].'" /></td>
											<td></td>
											<td>'.$value->goods_name.'</td>
											<td>'.$status_arr[$value->goods_display].'</td>
										</tr>
									';
								}
							}
						?>
					</tbody>
				</table>
			</div>
		
		</form>
	</div>
</div>

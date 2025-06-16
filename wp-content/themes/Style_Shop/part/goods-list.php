<?php 
/*
[CP customize]
 - Goods list
*/
get_header();
$bbseCat=get_query_var( 'bbseCat' );
?>
	<hr />
	<div id="content">
        <?php
        #로케이션
        get_template_part('part/sub', 'location');
		?>
		<div class="page_cont"  id="bbseCat<?php echo $bbseCat?>">
		<?php
			#추천상품 :recommend
			#베스트상품 : best
			#MD기획상품 : md
			#신상품 : new
			#오늘의 특가 :today
			#핫아이템 :hot
			#상품검색 : search
			$goodsMainItem = array('recommend', 'best', 'md', 'new', 'today', 'hot', 'search');
			if(in_array($bbseCat, $goodsMainItem)==true) {
				get_template_part('part/goods', $bbseCat.'-list');
			}else{
				#상품목록
				get_template_part('part/goods', 'category-list');
			}
		?>	
		</div>
	</div><!--//#content -->
<?php get_footer();
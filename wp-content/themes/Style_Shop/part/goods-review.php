<?php 
/*
[CP customize]
 - Best goods list view by 'bbsePage=review'
*/

get_header();

$bbsePage=get_query_var( 'bbsePage' );

global $theme_shortname;

$V = $_GET;
$sort_page = (!$V['sort_page'])?"write_date":$V['sort_page'];

/* Search Vars */
$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];//한 페이지에 표시될 목록수
$page = (count($_POST)>0 || !$_REQUEST['page'])?1:intval($_REQUEST['page']);//현재 페이지
$start_pos = ($page-1) * $per_page; //목록 시작 위치

if($sort_page=="write_date") {
	$orderby = " ORDER BY write_date ";
	$sort = "DESC";
}else if($sort_page=="r_value") {
	$orderby = " ORDER BY r_value ";
	$sort = "DESC";
}

/* List Query  */
$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_review AS R, bbse_commerce_goods AS G WHERE R.goods_idx=G.idx AND R.r_best='Y' "); //총 목록수
$total_pages = ceil($total / $per_page); //총 페이지수
$result = $wpdb->get_results("SELECT R.*, G.goods_basic_img, G.goods_add_img FROM bbse_commerce_review AS R, bbse_commerce_goods AS G WHERE R.goods_idx=G.idx AND R.r_best='Y' ".$orderby.$sort." LIMIT ".$start_pos.", ".$per_page);

/* Query String */
$add_args = array("per_page"=>$per_page,"sort_page"=>$sort_page);
$curURL =  home_url()."/?bbsePage=".$V['bbsePage']."&per_page=".$per_page;

/* 페이징 처리 정의 */	
$page_param = array();           
$page_param['page_row'] = $per_page;
$page_param['page_block'] = 10;      
$page_param['total_count'] = $total; 
$page_param['current_page'] = $page; 
$page_param['link_url'] = home_url()."/?bbsePage=".$V['bbsePage']."&".http_build_query($add_args);  
$page_class = new themePaging(); 
$page_class->initPaging($page_param); 


?>
	<style>
	.bestReviewListWrap {float:left;margin:50px 0 0 0;width:100%;}
	.bestReviewListWrap .sorting_select {float:right;width:auto;margin:0 0 10px;;}
	.bestReviewListWrap ul.bestReviewList {float:left;width:100%;border-top:2px solid #666666;}
	.bestReviewListWrap ul.bestReviewList li {float:left;padding:15px 0;width:100%;border-bottom:1px solid #666}
	ul.bestReviewList li > div {float:left}
	ul.bestReviewList li .thumbnail {width:86px;height:86px}
	ul.bestReviewList li .thumbnail img {width:86px;height:86px}
	ul.bestReviewList li .subjects {padding:0 1%;width:58%;height:86px;line-height:1.5em;}
	ul.bestReviewList li .subjects h3 {font-weight:bold;height: 20px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;}
	ul.bestReviewList li .subjects .mobileBlock {display:none}
	ul.bestReviewList li .subjects p {margin:1.3em  0 0;width:100%;height:4.5em;overflow:hidden;}
	ul.bestReviewList li .stars {width:10%;text-align:center;}
	ul.bestReviewList li .author {width:10%;text-align:center;}
	ul.bestReviewList li .date {float:right;width:10%;text-align:center;}
	@media only screen and (max-width: 1023px) {
		ul.bestReviewList li .subjects {padding:0 2%;width:69%;}
		ul.bestReviewList li .subjects .mobileBlock {display:block;}
		ul.bestReviewList li .subjects p {height:3em;}
		ul.bestReviewList li .stars,
		ul.bestReviewList li .author,
		ul.bestReviewList li .date {display:none}
	}
	</style>

	<hr />

	<div id="content">

        <?php
        #로케이션
        get_template_part('part/sub', 'location');
        ?>

		<div class="page_cont"  id="bbsePage<?php echo $bbsePage?>">

			<br />
			<h2 class="page_title"><?php echo get_option($theme_shortname."_goodsplace_title_5"); ?></h2>
			<form id="reviewListForm" name="reviewListForm" method="get">
			<input type="hidden" name="bbsePage" id="bbsePage" value="<?php echo $V['bbsePage']?>">
			<input type="hidden" name="sort_page" id="sort_page" value="<?php echo $sort_page?>">
			<div class="bestReviewListWrap">

				<div class="basic_tabs">
					<ul class="sorting_tabs"><!--N: 활성화는 .active -->
						<li<?php if($sort_page=="write_date"){?> class="active"<?php }?>><button type="button" onclick="location.href='<?php echo $curURL?>&sort_page=write_date';"><span>최신등록순</span></button></li>
						<li<?php if($sort_page=="r_value"){?> class="active"<?php }?>><button type="button" onclick="location.href='<?php echo $curURL?>&sort_page=r_value';"><span>평점순</span></button></li>
					</ul>
					<p class="sorting_select">
						<select name="per_page" id="per_page" title="리스트 갯수를 선택해주세요." onchange="jQuery('#reviewListForm').submit();">
							<option value="10" <?php echo ($per_page=="10")?"selected":""?>>10개씩 보기</option>
							<option value="20" <?php echo ($per_page=="20")?"selected":""?>>20개씩 보기</option>
							<option value="50" <?php echo ($per_page=="50")?"selected":""?>>50개씩 보기</option>
						</select>
					</p>
				</div>

				<ul class="bestReviewList">
				<?php
				if(plugin_active_check('BBSe_Commerce')) {
					if($total > 0) {
						foreach($result as $review){

							if($review->goods_basic_img) $basicImg = wp_get_attachment_image_src($review->goods_basic_img,"goodsimage3");
							else{
								$imageList=explode(",",$review->goods_add_img);
								if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage3");
								else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
							}
							
							$reviewRow = 10;
							$reviewTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$review->goods_idx."'"); // 총 Q&A 수
							$reviewTotalPage = ceil($reviewTotal / $reviewRow);
							$findPage = "";
							for($b=1;$b<=$reviewTotalPage;$b++) {
								$start_block = ($b-1) * $reviewRow;
								$findFlag = $wpdb->get_var("select count(*) from bbse_commerce_review where goods_idx='".$review->goods_idx."' and idx='".$review->idx."' limit ".$start_block.", ".$reviewRow);
								if($findFlag> 0) {
									$findPage = $b;
									break;
								}
							}
				?>
					<li>
						<div class="thumbnail">
							<a href="<?php echo home_url()."/?bbseGoods=".$review->goods_idx; ?>"><img src="<?php echo $basicImg['0']; ?>" alt="<?php echo $review->goods_name; ?>" /></a>
						</div>
						<div class="subjects">
							<h3><a href="<?php echo home_url()."/?bbseGoods=".$review->goods_idx; ?>&review_idx=<?php echo $review->idx; ?>&review_page=<?php echo $findPage; ?>#bbProductDetail2"><?php echo $review->r_subject; ?></a></h3>
							<div class="mobileBlock">
								<span class="bb_cmt_star cmt<?php echo $review->r_value; ?>">별점 <?php echo $review->r_value; ?>점/5점</span> <?php echo bbse_show_user_id($review->user_id,3); ?> <?php echo date("Y-m-d",$review->write_date);?>
							</div>
							<p><?php echo $review->r_contents; ?></p>
						</div>
						<div class="stars"><span class="bb_cmt_star cmt<?php echo $review->r_value; ?>">별점 <?php echo $review->r_value; ?>점/5점</span></div>
						<div class="author"><?php echo bbse_show_user_id($review->user_id,3); ?></div>
						<div class="date"><?php echo date("Y-m-d",$review->write_date);?></div>
					</li>
				<?php 
						}
					}else{
						echo "<li style='width:100%;text-align:center;'>등록된 베스트 상품평이 없습니다.</li>";
					}
				}else{echo "<li style='width:100%;color:red;'>BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.</li>";}
				?>
				</ul>

			</div>
			</form>


		</div>

		<?php echo $page_class->getPaging();?>

	</div><!--//#content -->



<?php get_footer();
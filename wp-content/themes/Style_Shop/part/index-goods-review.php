				<?php
				global $theme_shortname;
				if(get_option($theme_shortname.'_goodsplace_use_5')=="Y") {
				?>
				<div class="main_section best_review">
					<h3 class="lv3_title"><?php echo (get_option($theme_shortname."_goodsplace_title_5")!="")?get_option($theme_shortname."_goodsplace_title_5"):"베스트 상품평"?> <span><?php echo get_option($theme_shortname."_goodsplace_description_5")?></span></h3>
					<?php
					if(plugin_active_check('BBSe_Commerce')) {
						$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_review AS R, bbse_commerce_goods AS G WHERE R.goods_idx=G.idx AND R.r_best='Y' "); //총 목록수
						if($total > 0) {
					?>
					<div class="basic_list">
						<ul class="">
							<?php
								$orderby = "order by R.idx desc";
								$limit = "";
								$lineCount = get_option($theme_shortname."_goodsplace_line_5");
								if($lineCount) $limit = " limit ".($lineCount * 3);

								$result = $wpdb->get_results("SELECT R.*, G.goods_basic_img, G.goods_add_img,goods_icon_new,goods_icon_best FROM bbse_commerce_review AS R, bbse_commerce_goods AS G WHERE R.goods_idx=G.idx AND R.r_best='Y' ".$orderby.$limit);

									foreach($result as $review){
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

										if($review->goods_basic_img) $basicImg = wp_get_attachment_image_src($review->goods_basic_img,"goodsimage3");
										else{
											$imageList=explode(",",$review->goods_add_img);
											if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage3");
											else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
										}
							?>
							<li>
								<a href="<?php echo home_url()."/?bbseGoods=".$review->goods_idx; ?>&review_idx=<?php echo $review->idx; ?>&review_page=<?php echo $findPage; ?>#bbProductDetail2">
									<span class="tag">
										<?php if ($review->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
										<?php if ($review->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
									</span>
									<img src="<?php echo $basicImg['0']?>" alt="<?php echo $review->goods_name?>" />
									<strong class="subj"><?php echo $review->r_subject?></strong>
									<span class="desc"><?php echo $review->r_contents?></span>
								</a>
							</li>
							<?php
								}
							?>
						</ul>
					</div>
					<a href="<?php echo get_option($theme_shortname."_goodsplace_url_5")?get_option($theme_shortname."_goodsplace_url_5"):home_url()."/?bbsePage=review"?>" target="<?php echo get_option($theme_shortname."_goodsplace_url_5_window")?>" class="more">더보기</a>
					<?php
						}
					}else{
						echo "<div :id=\"newItemList\" class=\"basic_list\" style='padding:10px;'>BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.</div>";
					}
					?>
				</div><!--//베스트상품평 -->
				<?php
				}
				?>
				<?php
				global $theme_shortname;
				if(get_option($theme_shortname.'_goodsplace_use_4')=="Y") {
				?>
				<div class="main_section mainRoller2">
					<h3 class="lv3_title"><?php echo (get_option($theme_shortname."_goodsplace_title_4")!="")?get_option($theme_shortname."_goodsplace_title_4"):"신상품"?> <span><?php echo get_option($theme_shortname."_goodsplace_description_4")?></span></h3>
					<?php
					if(plugin_active_check('BBSe_Commerce')) {
						$new= $wpdb->get_var("select display_goods from bbse_commerce_display where display_type='new'");
						if($new){
							$goodsList = unserialize($new);
					?>
					<div id="newItemList" class="basic_list">
						<ul class="slides">
							<?php
								$orderby = "\norder by case idx\n";
								foreach($goodsList as $k => $val){
									$orderby .= 'when ' . $val . ' then ' . ($k+1) . "\n";
								}
								$orderby .= 'end ';
								$limit = "";
								$lineCount = get_option($theme_shortname."_goodsplace_line_4");
								if($lineCount) $limit = " limit ".($lineCount * 5);
								$goods_res = $wpdb->get_results("select * from bbse_commerce_goods where (goods_display='display' OR goods_display='soldout') and idx in (".implode(",",$goodsList).") ".$orderby.$limit);
								foreach($goods_res as $n=>$goods){
									
									$soldout = goodsSoldoutCheck($goods); //품절체크

									$imgSizeKind = "goodsimage2";

									$imageList=explode(",",$goods->goods_add_img);
									$firstImg=$secondImg="";
									if($goods->goods_basic_img){
										$basicImg = wp_get_attachment_image_src($goods->goods_basic_img,$imgSizeKind);
										$firstImg=$basicImg['0'];
									}
									else{
										if(sizeof($imageList)>'0'){
											$basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
											$firstImg=$basicImg['0'];
										}
										else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
									}

									if($firstImg){
										for($zk=0;$zk<sizeof($imageList);$zk++){
											unset($tmpImg);
											$tmpImg = wp_get_attachment_image_src($imageList[$zk],$imgSizeKind);
											if($imageList[$zk]>'0' && $tmpImg && $tmpImg['0']!=$firstImg){
												$secondImg=$tmpImg['0'];
												break;
											}
										}
									}
									$url = home_url()."/?bbseGoods=".$goods->idx;
									$target = '';
									if($goods->goods_external_link_tf == 'view'){
										$url = $goods->goods_external_link;
										$target = '_blank'; 
									}
							?>
							<li>
								<a href="<?php echo $url?>" target="<?php echo $target;?>">
									<span class="tag">
										<?php if ($goods->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
										<?php if ($goods->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
										<?php if ($soldout){ ?><span class="soldout_tag"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>&nbsp;
									</span>
									<div  class="img_view">
										<img src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
									</div>
									<span class="subj"><?php echo $goods->goods_name?></span>
									<strong class="bb_price"><?php echo number_format($goods->goods_price)?>원</strong>
								</a>
							</li>
							<?php
								}
							?>
						</ul>
						<script type="text/javascript">
						$(function () {
							$(document).ready(function(){
								$('#newItemList').flexslider({
									animation: "slide",
									slideshow: false,
									animationLoop: true,
									controlNav: true,
									directionNav: false,
									itemWidth: 120,
									itemMargin: 50
								});
							});
						});
						</script>
					</div>
						<?php
						}

					}else{
						echo "<div :id=\"newItemList\" class=\"basic_list\" style='padding:10px;'>BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.</div>";
					}
					?>
				</div><!--//신상품 -->
				<?php
				}
				?>
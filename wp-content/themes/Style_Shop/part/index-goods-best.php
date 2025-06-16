				<?php
 				global $theme_shortname;
				$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
				$currUserID=$current_user->user_login;
				$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$currUserID."'");
				$currUserClass = $member[0]->user_class;
				$role = $current_user->roles[0];
				if(get_option($theme_shortname.'_goodsplace_use_2')=="Y") {
				?>
				<?php if(is_user_logged_in()) { ?>
				<!-- 양쪽 크기가 다른 3개짜리 상품 목록 -->
				<div class="main_section best_item">
					<h3 class="lv3_title"><?php echo (get_option($theme_shortname."_goodsplace_title_2")!="")?get_option($theme_shortname."_goodsplace_title_2"):"베스트상품"?> <span><?php echo get_option($theme_shortname."_goodsplace_description_2")?></span></h3>
					<div class="best_list">
						<ol>

						<?php
						 if(plugin_active_check('BBSe_Commerce')) {
							$best = $wpdb->get_var("select display_goods from bbse_commerce_display where display_type='best'");
							if($best) {
								$goodsList = unserialize($best);
								$goods_type_list = $goodsList['goods_type_list'];
								
								$orderby = "\norder by case idx\n";
								foreach($goods_type_list as $k => $v){
									$orderby .= 'when ' . $v . ' then ' . ($k+1) . "\n";
								}
								$orderby .= 'end ';

								$goods_res = $wpdb->get_results("select * from bbse_commerce_goods where (goods_display='display' OR goods_display='soldout') and idx in (".implode(",",$goods_type_list).") order by idx");
								$rank = 0;
								foreach($goods_res as $i=>$goods){
									$soldout = goodsSoldoutCheck($goods); //품절체크

									if($i==0) $imgSizeKind = "goodsimage4";
									else $imgSizeKind = "goodsimage2";

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
									
								    $memPrice=unserialize($goods->goods_member_price);
								    $display = unserialize($goods->goods_cprice_display);
 								    $cpDisplay = $display[goods_cprice_display];
								    
 								    if($role == 'administrator'){?>
										<li class="best<?php echo ($rank+1);?>">
            								<a href="<?php echo $url?>" target="<?php echo $target;?>">
            									<div class="img_view">
            										<span class="tag_best tag<?php echo ($rank+1);?>">BEST <?php echo ($rank+1);?>위</span>
            										<img src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
            										<div class="product_info">
            											<strong class="subj"><?php echo $goods->goods_name?></strong>
            											<span class="desc"><?php echo $goods->goods_description?></span>
            										</div>
            									</div>
            								</a>
            							</li>		
 								    
 								    <?php
 								    $rank++;
 								    }
								    for($j=0; $j<sizeof($memPrice['goods_member_level']); $j++){
								        if($memPrice['goods_member_level'][$j] == $currUserClass){
    								        $cPrice = $memPrice['goods_consumer_price'][$j]; 
    								        
    								        if($cPrice > '0'){
    								            
    								            $mPrice = $memPrice['goods_member_price'][$j];
    								            $vat = $memPrice['goods_vat'][$j];
    								            $salePrice=round((1-($cPrice/($mPrice+$vat)))*100,1);
						?>
                    							<li class="best<?php echo ($rank+1);?>">
                    								<a href="<?php echo $url?>" target="<?php echo $target;?>">
                    									<div class="img_view">
                    										<span class="tag_best tag<?php echo ($rank+1);?>">BEST <?php echo ($rank+1);?>위</span>
                    										<img src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
                    										<div class="product_info">
                    											<strong class="subj"><?php echo $goods->goods_name?></strong>
                    											<span class="desc"><?php echo $goods->goods_description?></span>
                    											<em class="bb_price_info">
                    												<?php 
                    												if(sizeof($cpDisplay) >'0'){
    																	for($z=0;$z<sizeof($cpDisplay);$z++){
    																	    if($cpDisplay[$z] == $currUserClass){
                                                                        ?>
                            												<em class="bb_price"><?php echo number_format($mPrice+$vat)?>원</em>
                            												
                        										    	<?php
                        										    	       break;
                        										            }
    																	}
                        										        ?>
                        										        <?php 
                    												}else{
                    										        ?>
																			<span class="sale_per"><strong><?php echo $salePrice;?></strong>%</span>
                                											<em class="bb_price"><?php echo number_format($cPrice)?>원</em>
                                											<span class="bb_sale"><?php echo $salePrice?></span>
                    										        <?php 
                    												}
                    										        ?>
                    											</em>
                    										</div>
                    									</div>
                    								</a>
                    							</li>						
						<?php
								            
						                      $rank++;
    								    
    								        }
							            }
								    }
								}
							}
						}else{
							echo "<li style='width:90%;color:red;'>BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.</li>";
						}
						?>
						</ol>
						<a href="<?php echo get_option($theme_shortname."_goodsplace_url_2")?get_option($theme_shortname."_goodsplace_url_2"):home_url()."/?bbseCat=best"?>" target="<?php echo get_option($theme_shortname."_goodsplace_url_2_window")?>" class="more">더보기</a>
					</div>
				</div><!--//베스트상품 -->
				<?php
				    }
				}

				?>
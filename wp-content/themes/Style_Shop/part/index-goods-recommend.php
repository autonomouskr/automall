				<?php
				global $theme_shortname;
				

				$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
				$currUserID=$current_user->user_login;
				$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$currUserID."'");
				$currUserClass = $member[0]->user_class;
				$role = $current_user->roles[0];
				if(get_option($theme_shortname.'_goodsplace_use_1')=="Y") {
				?>
				
				<!-- 양쪽 화살표 4열 롤링타입 상품 목록 -->
				<?php if(is_user_logged_in()) { ?>
				<div class="main_section mainRoller1">
					<h3 class="lv3_title"><?php echo (get_option($theme_shortname."_goodsplace_title_1")!="")?get_option($theme_shortname."_goodsplace_title_1"):"추천상품"?> <span><?php echo get_option($theme_shortname."_goodsplace_description_1")?></span></h3>
					<div id="recmdList" class="list_box recmd_list">
						<a href="<?php echo get_option($theme_shortname."_goodsplace_url_1")?get_option($theme_shortname."_goodsplace_url_1"):home_url()."/?bbseCat=recommend"?>" target="<?php echo get_option($theme_shortname."_goodsplace_url_1_window")?>" class="more">더보기</a>
						<ul class="">
						<?php
						 if(plugin_active_check('BBSe_Commerce')) {
							$recommend = $wpdb->get_var("select display_goods from bbse_commerce_display where display_type='recommend'");
							if($recommend) {
								$goodsList = unserialize($recommend);
								$goods_type_list = $goodsList['goods_type_list'];
								$orderby = "\norder by case idx\n";
								foreach($goods_type_list as $k => $v){
									$orderby .= 'when ' . $v . ' then ' . ($k+1) . "\n";
								}
								$orderby .= 'end ';
								$goods_res = $wpdb->get_results("select * from bbse_commerce_goods where (goods_display='display' OR goods_display='soldout') and idx in (".implode(",",$goods_type_list).")".$orderby);
								$rank = 0;
								foreach($goods_res as $goods){
								    $memPrice=unserialize($goods->goods_member_price);
								    $display = unserialize($goods->goods_cprice_display);
								    $cpDisplay = $display[goods_cprice_display];
								    for($i=0; $i<sizeof($memPrice['goods_member_level']); $i++){
								        if($memPrice['goods_member_level'][$i] == $currUserClass || $role == 'administrator'){
								            $cPrice = $memPrice['goods_consumer_price'][$i];
								            if($cPrice > '0'){
								                $mPrice = $memPrice['goods_member_price'][$i];
								                $vat = $memPrice['goods_vat'][$i];
								                
            									$soldout = goodsSoldoutCheck($goods); //품절체크
            
            									$imgSizeKind = "goodsimage2";
            
            									$imageList=explode(",",$goods->goods_add_img);
            									$firstImg=$secondImg="";
            									if($goods->goods_basic_img){
            										$basicImg = wp_get_attachment_image_src($goods->goods_basic_img,$imgSizeKind);
            										$firstImg=$basicImg['0'];
            									}
            									else{
            										if(sizeof($imageList)>'0') {
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
                    									<div class="img_view">
                    										<img src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
                    									</div>
                    									<em class="subj"><?php echo $goods->goods_name?></em>
                    									<span class="bb_price">
                    										<?php 
                    										    if(in_array($currUserClass, $cpDisplay)){
                    										    ?>
                    										    <strong><?php echo number_format($mPrice+$vat)?>원</strong>
                    										    <?php 
                    										    }else{
                    										    ?>
                    											<del><?php echo number_format($cPrice)?></del>
                    											<strong><?php echo number_format($mPrice+$vat)?>원</strong>
                    											<?php
                    										    }
                    										    ?>
                    									</span>
                    								</a>
                    							</li>
						<?php 
								            }}}
						?>
						<?php
								}
							}else echo "등록된 추천상품이 없습니다.";
						?>
						<?php
						}else{
							echo "<li style='width:90%;color:red;'>BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.</li>";
						}
						?>
						</ul>
						
						<p class="control_btn">
							<button type ="button" class="prev"><span>이전</span></button>
							<button type ="button" class="next"><span>다음</span></button>
							<span class="count">
								<strong class="view"></strong> / <span class="max"></span>
							</span>
						</p>
					</div>
				</div><!--//추천 상품-->
				<?php
				}
				}
				?>
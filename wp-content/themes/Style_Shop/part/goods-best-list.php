<?php
global $theme_shortname,$current_user;


$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;
$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$currUserID."'");
$currUserClass = $member[0]->user_class;
$role = $current_user->roles[0];

?>
<div class="article">
	<h1 class="pageMainTitle"><?php echo get_option($theme_shortname."_goodsplace_title_2");//베스트상품?> <span class="pageSubTitle"><em><?php echo current_time('Y.m.d')?></em> <?echo get_option($theme_shortname."_goodsplace_description_2");?></span></h1>
	<div class="lp_list bb_best_list top5">
		<ul>
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
    
				$goods_res = $wpdb->get_results("select * from bbse_commerce_goods where (goods_display='display' OR goods_display='soldout') and idx in (".implode(",",$goods_type_list).") order by idx ");
				
				$rank = 0;
				foreach($goods_res as $i=>$goods){
                    
				    $memPrice=unserialize($goods->goods_member_price);

					$soldout = goodsSoldoutCheck($goods); //품절체크
					if($i==0) {
						$imgSizeKind = "goodsimage6";
						$liClass = "best_big";
						$liImgClass = "img_view_big";
					}else{
						$imgSizeKind = "goodsimage3";
						$liClass = "best_sml";
						$liImgClass = "img_view";
					}

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
					
				    if($role == 'administrator'){ ?>
						<li class="<?php echo $liClass?>">
        				<div class="hover">
        					<a href="javascript:void(0);" onClick="go_detail('<?php echo $url?>');"><button class="bb_detail"><span>상세보기</span></button></a>
        					<a href="javascript:void(0);" onClick="<?php echo ($current_user->user_login)?"go_wishlist(".$goods->idx.");":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');";?>"><button class="찜하기"><span>찜하기</span></button></a>
        				</div>
        				<a href="<?php echo $url?>" target="<?php echo $target;?>">
        					<span class="tag_best">
        						<?php echo ($rank+1);?><em>위</em>
        					</span>
        					<div class="<?php echo $liImgClass;?>">
        						<img class="bb_thumb" src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
        					</div>
        					<div class="tag">
        						<?php if ($goods->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
        						<?php if ($goods->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
        						<?php if ($soldout){ ?><span class="soldout_tag"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>
        					</div>
        					
        					<?php if($i==0){?>
        					<strong class="subj"><?php echo $goods->goods_name?></strong>
        					<div class="excerpt"><?php echo $goods->goods_description?></div>
        					<div class="bb_price_info forPC">
        						<br />
        						<?php
        						$cDisplay = unserialize($goods->goods_cprice_display);
    						    ?>
        					</div>
        					<div class="bb_price_info forMobile">
        						<?php
        						$cDisplay = unserialize($goods->goods_cprice_display);
    						    ?>
        					</div>
        					<?php }else{?>
        					<h2 class="subj"><?php echo $goods->goods_name?></h2>
        					<div class="bb_price_info">
        						<?php
        						$cDisplay = unserialize($goods->goods_cprice_display);
    						    ?>
        					</div>
        					<?php }?>
        
        				</a>
        			</li>
				    
				    <?php
				    $rank++;
				    }
				    
					for($j=0; $j<sizeof($memPrice['goods_member_level']); $j++){
					    if($memPrice['goods_member_level'][$j] == $currUserClass){
    					    $mPrice = $memPrice['goods_member_price'][$j];
    					    $vat = $memPrice['goods_vat'][$j];
					        $cPrice = $memPrice['goods_consumer_price'][$j];
					        if($cPrice > '0'){
            					$salePrice=round((1-($cPrice/($mPrice+$vat)))*100,1);
            					?>
                    			<li class="<?php echo $liClass?>">
                    				<div class="hover">
                    					<a href="javascript:void(0);" onClick="go_detail('<?php echo $url?>');"><button class="bb_detail"><span>상세보기</span></button></a>
                    					<a href="javascript:void(0);" onClick="<?php echo ($current_user->user_login)?"go_wishlist(".$goods->idx.");":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');";?>"><button class="찜하기"><span>찜하기</span></button></a>
                    				</div>
                    				<a href="<?php echo $url?>" target="<?php echo $target;?>">
                    					<span class="tag_best">
                    						<?php echo ($rank+1);?><em>위</em>
                    					</span>
                    					<div class="<?php echo $liImgClass;?>">
                    						<img class="bb_thumb" src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
                    					</div>
                    					<div class="tag">
                    						<?php if ($goods->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
                    						<?php if ($goods->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
                    						<?php if ($soldout){ ?><span class="soldout_tag"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>
                    					</div>
                    					
                    					<?php if($i==0){?>
                    					<strong class="subj"><?php echo $goods->goods_name?></strong>
                    					<div class="excerpt"><?php echo $goods->goods_description?></div>
                    					<div class="bb_price_info forPC">
                    						<br />
                    						<?php
                    						$cDisplay = unserialize($goods->goods_cprice_display);
                    						if(in_array($currUserClass, $cDisplay[goods_cprice_display])){
                    						?>
                    						<em class="bb_price"><?php echo number_format($mPrice+$vat)?><span>원</span></em>
                    						<?php
                    						}else{
                    						?>
                    						<em class="bb_saleRate"><?php echo $salePrice;?><span>%</span></em>
                    						<del><?php echo number_format($mPrice+$vat)?>원</del>
                    						<em class="bb_price"><?php echo number_format($cPrice)?><span>원</span></em>
                    						<?php 
                						    }
                						    ?>
                    					</div>
                    					<div class="bb_price_info forMobile">
                    						<?php
                    						$cDisplay = unserialize($goods->goods_cprice_display);
                    						if(in_array($currUserClass, $cDisplay[goods_cprice_display])){
                    						    ?>
                    						<em class="bb_price"><?php echo number_format($mPrice+$vat)?><span>원</span></em>
                    						<?php
                    						}else{
                    						?>
                    						<em class="bb_saleRate"><?php echo $salePrice;?><span>%</span></em>
                    						<del><?php echo number_format($mPrice+$vat)?>원</del>
                    						<em class="bb_price"><?php echo number_format($cPrice)?><span>원</span></em>
                    						<?php 
                						    }
                						    ?>
                    					</div>
                    					<?php }else{?>
                    					<h2 class="subj"><?php echo $goods->goods_name?></h2>
                    					<div class="bb_price_info">
                    						<?php
                    						$cDisplay = unserialize($goods->goods_cprice_display);
                    						if(in_array($currUserClass, $cDisplay[goods_cprice_display])){
                    						    ?>
                    						<em class="bb_price"><?php echo number_format($mPrice+$vat)?><span>원</span></em>
                    						<?php
                    						}else{
                    						?>
                    						<del><?php echo number_format($mPrice+$vat)?>원</del>
                    						<span class="bb_sale"><?php echo $salePrice;?>%</span>
                    						<em class="bb_price"><?php echo number_format($cPrice)?>원</em>
                    						<?php 
                						    }
                						    ?>
                    					</div>
                    					<?php }?>
                    
                    				</a>
                    			</li>
                			<?php 
                			$rank++;
                        }
					}
				}
		    }
		}

		?>
		</ul>
	</div>


	<!-- <div class="lp_list bb_best_list">
		<ul>
			<?php
			$best = $wpdb->get_var("select display_goods from bbse_commerce_display where display_type='best'");
			if($best) {
				$goodsList = unserialize($best);
				
				$goods_type_list = $goodsList['goods_type_list'];
				
				$orderby = "\norder by case idx\n";
				foreach($goods_type_list as $k => $v){
					$orderby .= 'when ' . $v . ' then ' . ($k+1) . "\n";
				}
				$orderby .= 'end ';

				//$goods_res = $wpdb->get_results("select * from bbse_commerce_goods where (goods_display='display' OR goods_display='soldout') and idx in (".implode(",",$goods_type_list).") ".$orderby." limit 5, 10");
				foreach($goods_res as $i=>$goods){
				    
				    if($goods->goods_display=="soldout") {//품절 체크
				        $soldout = true;
				    }else{
				        $soldout = false;
				    }
				    
				    $memPrice=unserialize($goods->goods_member_price);
				    
				    for($j=0; $j<sizeof($memPrice['goods_member_level']); $j++){
				        if($memPrice['goods_member_level'][$j] == $currUserClass){
				            $cPrice = $memPrice['goods_consumer_price'][$j];
				            if($cPrice > '0'){
				                $mPrice = $memPrice['goods_member_price'][$j];
				                $vat = $memPrice['goods_vat'][$j];
				                $soldout = goodsSoldoutCheck($goods); //품절체크
				                $imgSizeKind = "goodsimage3";
				                
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
				                
				                $salePrice=round((1-($cPrice/$mPrice))*100,1);
				            
				            ?>
                			<li>
                				<div class="hover">
                					<a href="javascript:void(0);" onClick="go_detail('<?php echo $url?>');"><button class="bb_detail"><span>상세보기</span></button></a>
                					<a href="javascript:void(0);" onClick="<?php echo ($current_user->user_login)?"go_wishlist(".$goods->idx.");":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');";?>"><button class="button"><span>찜하기</span></button></a>
                				</div>
                				<a href="<?php echo $url?>" target="<?php echo $target;?>">
                					<span class="tag_best">
                						<?php echo ($rank+1);?><!-- <em>위</em> -->
                					<!-- </span>
                					<div class="img_view">
                						<img class="bb_thumb" src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
                					</div>
                					<div class="tag">
                						<?php if ($goods->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
                						<?php if ($goods->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
                						<?php if ($soldout){ ?><span class="soldout_tag glist"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>
                					</div>
                					<h2 class="subj"><?php echo $goods->goods_name?></h2>
                					<div class="bb_price_info">
                						<?php
                						if($goods -> goods_cprice_display == 'view'):
                						?>
                						<del><?php echo number_format($mPrice)?>원</del>
                						<span class="bb_sale"><?php echo $salePrice;?>%</span>
                						<?php
                						endif;
                						?>
                						<em class="bb_price"><?php echo number_format($cPrice)?>원</em>
                					</div>
                				</a>
                			</li>
				            
				            <?php
				            $rank++;
				            }
				        }
				    }
			?>

			<?php
					if($i==4) echo "<div style='clear:both;'></div>";
				}
			}

		}else{
			echo "<li style='width:90%;color:red;'>BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.</li>";
		}
		?>
		</ul>
	</div><!--//.lp_list -->
	
</div><!--//.article -->
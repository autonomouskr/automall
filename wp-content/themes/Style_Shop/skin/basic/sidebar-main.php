				<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

				global $theme_shortname;

				?>
					<div class="side_section side-nav category">
				<?php if(get_option($theme_shortname."_layout_left_category")=="Y"){
					if(!is_user_logged_in() && ($_SESSION['snsLogin']!='Y' || !$_SESSION['snsLoginData'])){
					   
					}else{
                ?>
					<h2>CATEGORIES</h2>
				<?php 
					    leftCategoryView('side');
					}
                ?>
				<?php }?>
				</div><!--//CATEGORY -->

				<?php if(get_option($theme_shortname."_layout_left_price_search")=="Y"){?>
				<div class="side_section slide_price">
					<form id="sidebarSearchFrm" name="sidebarSearchFrm" method="get" action="<?php echo home_url()?>">
					<input type="hidden" name="bbseCat" id="bbseCat" value="search">
					<input type="hidden" name="sort_page" id="sort_page" value="reg_date">
					<h2>가격검색</h2>
					<div class="price_slider_wrap">
						<div id="sliderPrice"></div>
						<!--div class="sliderPrice_bg"></div-->
						<div class="price_info">
							<div class="price_limit">
								<span class="blind"><!-- value로 받아올때-->
									<label class="blind" for="startSearchPrice">시작가격</label><input type="text" id="startSearchPrice" name="startSearchPrice" class="price_min_input priceComma" value="<?php //echo $startSearchPrice;?>" /> 원
									~
									<label class="blind" for="endSearchPrice">끝 가격</label><input type="text" id="endSearchPrice" name="endSearchPrice" class="price_max_input priceComma" value="<?php //echo $endSearchPrice;?>" /> 원
									<!--N: 최초 가격 세팅시 js파일 내에서 최소가격과 최대가격을 input의 value값과 span에 표시될 가격을 맞추어 준다.-->
								</span>
								<span class="price_min priceComma"><?php echo BBSE_COMMERCE_SEARCH_MIN_PRICE; ?></span> ~ <span class="price_max priceComma"><?php echo BBSE_COMMERCE_SEARCH_MAX_PRICE; ?></span>원
								<!--N: 최초 가격 세팅시 js파일 내에서 최소가격과 최대가격을 input의 value값과 span에 표시될 가격을 맞추어 준다.-->
							</div>
							<button type="button" class="bb_btn shadow"><span class="sml">검색</span></button>
						</div><!--//.price_info -->
					</div><!--//.price_slider_wrap -->
					</form>
					<script type="text/javascript">
						$(function () {
							$(document).ready(function(){
								//최소~최대 가격 슬라이드
								$('.priceComma').each(function(index){$(this).text($(this).text().split(/(?=(?:\d{3})+(?:\.|$))/g).join(','));});
								$( "#sliderPrice" ).slider({
									range: true,
									step: 1000,									//슬라이드할때 가격 단위
									min: <?php echo BBSE_COMMERCE_SEARCH_MIN_PRICE; ?>,									//가격 최소 값
									max: <?php echo BBSE_COMMERCE_SEARCH_MAX_PRICE; ?>,								//가격 최대 값
									values: [ <?php echo BBSE_COMMERCE_SEARCH_MIN_PRICE; ?>, <?php echo BBSE_COMMERCE_SEARCH_MAX_PRICE; ?> ],				//최초 세팅 최소가격~최대가격
									slide: function( event, ui ) {
										var pirceMin = ui.values[ 0 ],		//셋팅 된 최소가격
											pirceMax = ui.values[ 1 ];		//셋팅 된 최대가격
										// value로 받아올때
										$('.price_info').find('.price_min_input').val(pirceMin);
										$('.price_info').find('.price_max_input').val(pirceMax);
										//text로 뿌려주고 3자리 콤마
										$('.price_info').find('.price_min').text(pirceMin);
										$('.price_info').find('.price_max').text(pirceMax);
										$('.priceComma').each(function(index){$(this).text($(this).text().split(/(?=(?:\d{3})+(?:\.|$))/g).join(','));});
									}
								});

								//value 테스트
								$('.slide_price button.bb_btn').bind('click', function() {
									jQuery('#sidebarSearchFrm').submit();
								})
							})
						})
					</script>
				</div><!--//가격검색 -->
				<?php }?>

				<?php
				if(get_option($theme_shortname.'_layout_left_today_sale')=="Y") {
					$today= $wpdb->get_var("select display_goods from bbse_commerce_display where display_type='today'");
					if($today){
						$goodsList = unserialize($today);
				?>
				<div class="side_section sideRoller1">
					<h2>오늘만 특가</h2>
					<div id="specialList" class="roll-list">
						<ul>
							<?php
								$orderby = "\norder by case idx\n";
								foreach($goodsList as $k => $val){
									$orderby .= 'when ' . $val . ' then ' . ($k+1) . "\n";
								}
								$orderby .= 'end ';
								if($lineCount) $limit = " limit ".($lineCount * 5);
								$goods_res = $wpdb->get_results("select * from bbse_commerce_goods where (goods_display='display' OR goods_display='soldout') and idx in (".implode(",",$goodsList).") ".$orderby);
								foreach($goods_res as $n=>$goods){
									if($goods->goods_display=="soldout") {//품절 체크
										$soldout = true;
									}else{
										$soldout = false;
									}

									$imgSizeKind = "goodsimage4";

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

									$salePrice=round((1-($goods->goods_price/$goods->goods_consumer_price))*100,1);
							?>
							<li>
								<span class="tag_sale">
									<em><?php echo $salePrice?></em>%
									<strong>SALE</strong>
								</span>
								<a href="<?php echo home_url()."/?bbseGoods=".$goods->idx?>">
									<div class="img_view">
										<img src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
									</div>
									<div class="tag">
										<?php if ($goods->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
										<?php if ($goods->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
										<?php if ($soldout){ ?><span class="soldout_tag"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>&nbsp;
									</div>
									<em class="subj"><?php echo $goods->goods_name?></em>
									<strong class="bb_price">
										<del><?php echo number_format($goods->goods_consumer_price)?></del> <?php echo number_format($goods->goods_price)?>원
									</strong>
								</a>
							</li>
							<?php
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
				</div><!--//오늘만 특가 -->
				<?php
					}
				}
				?>
				<div class="side_section sideRoller2">
					<h2 class="blind">광고배너</h2>
					  <?php if(get_option($theme_shortname."_display_use_left_banner")=="U"){?>
					  <!-- 관리자에서 롤링 선택. rollit 클래스 유무로 구분  -->
					  <div class="side-banners <?php  if(get_option($theme_shortname."_display_use_left_rolling_banner")=="Y"){echo "rollit";}?>">
						<ul class="slides">
							<?php
							for($leftBannerCnt=0;$leftBannerCnt<=get_option($theme_shortname."_display_use_left_banner_count");$leftBannerCnt++) {
								if(get_option($theme_shortname."_display_left_banner_img_".$leftBannerCnt)!=""){
							?>
							<li><a href="<?php echo get_option($theme_shortname."_display_left_banner_url_".$leftBannerCnt)?get_option($theme_shortname."_display_left_banner_url_".$leftBannerCnt):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_display_left_banner_url_".$leftBannerCnt."_window")?>"><img src="<?php echo get_option($theme_shortname."_display_left_banner_img_".$leftBannerCnt)?>" alt="왼쪽 배너" /></a></li>
							<?php
								}
							}
							?>
						</ul>
					</div>
					<?php }?>
					<script type="text/javascript">
						$(function () {
							$(document).ready(function(){
								$('.sideRoller2 .rollit').flexslider({
									animation: "fade",
									animationLoop: true,
									slideshow: false,
									controlNav: true,
									directionNav: false
								});
							});
						});
					</script>

				</div><!--//광고배너 -->
				<?php
				if(get_option($theme_shortname.'_layout_left_hot_item')=="Y") {
					$hot= $wpdb->get_var("select display_goods from bbse_commerce_display where display_type='hot'");
					if($hot){
						$goodsList = unserialize($hot);
				?>
				<div class="side_section sideRoller1">
					<h2>핫아이템</h2>
					<div id="hotList" class="roll-list hot_list">
						<ul>
							<?php
								$orderby = "\norder by case idx\n";
								foreach($goodsList as $k => $val){
									$orderby .= 'when ' . $val . ' then ' . ($k+1) . "\n";
								}
								$orderby .= 'end ';
								if($lineCount) $limit = " limit ".($lineCount * 5);
								$goods_res = $wpdb->get_results("select * from bbse_commerce_goods where (goods_display='display' OR goods_display='soldout') and idx in (".implode(",",$goodsList).") ".$orderby);
								foreach($goods_res as $n=>$goods){
									if($goods->goods_display=="soldout") {//품절 체크
										$soldout = true;
									}else{
										$soldout = false;
									}

									$imgSizeKind = "goodsimage1";

									if($goods->goods_basic_img) $basicImg = wp_get_attachment_image_src($goods->goods_basic_img,$imgSizeKind);
									else{
										$imageList=explode(",",$goods->goods_add_img);
										if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],$imgSizeKind);
										else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
									}
							?>
							<li>
								<a href="<?php echo home_url()."/?bbseGoods=".$goods->idx?>">
									<img src="<?php echo $basicImg['0']?>" alt="<?php echo $goods->goods_name?>" />
									<em class="subj"><?php echo $goods->goods_name?></em>
									<span class="desc"><?php echo $goods->goods_description?></span>
									<strong class="bb_price">
										<del><?php echo number_format($goods->goods_consumer_price)?></del> <?php echo number_format($goods->goods_price)?>원
									</strong>
								</a>
							</li>
							<?php
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
				</div><!--//핫 아이템 -->
				<?php
					}
				}
				if(get_option($theme_shortname.'_layout_left_bank_info')=="Y" && plugin_active_check('BBSe_Commerce')) {
				?>
				<div class="side_section bb_banking">
					<h2>입금계좌정보</h2>
					<ul class="bb_dot_list">
					<?php
					$bankRes = $wpdb->get_results("select * from bbse_commerce_config where config_type='bank' order by idx asc");
					foreach($bankRes as $banks) {
						$bank = unserialize($banks->config_data);
						if($bank['bank_info_use']=="on") {
							echo "<li><strong>".$bank['bank_name']."</strong><br>".$bank['bank_account_number']."<br>예금주: ".$bank['bank_owner_name']."</li>";
						}
					}
					?>
					</ul>
				</div><!--//입금 계좌정보-->
				<?php }?>
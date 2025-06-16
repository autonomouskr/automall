<?php 
/*
[CP customize]
 - Goods view
*/
get_header();
global $theme_shortname,$current_user;

$bbseGoods=get_query_var( 'bbseGoods' );
$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$bbseGoods."'");

$soldout = goodsSoldoutCheck($goods); //품절체크

$imageList=explode(",",$goods->goods_add_img);

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;
$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$currUserID."'");
$currUserClass = $member[0]->user_class;
$role = $current_user->roles[0];

if($goods->goods_basic_img) {
	$basicBigImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage");
}
else{
	if(sizeof($imageList)>'0') $basicBigImg=wp_get_attachment_image_src($imageList['0'],"goodsimage8");
	else $basicBigImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
}

if(is_user_logged_in()) {
	$myInfo=bbse_get_user_information();
	$memPrice=unserialize($goods->goods_member_price);
}

$salePrice=0;
$consumerPrice=0;
if($myInfo->user_id && $myInfo->user_class>2 && $myInfo->use_sale=='Y'){
	for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
	    if($memPrice['goods_member_level'][$m]==$myInfo->user_class) {
	        $salePrice=$memPrice['goods_member_price'][$m];
	        $consumerPrice= $memPrice['goods_consumer_price'][$m];
    		$vat=$memPrice['goods_vat'][$m];
    		$savePrice=$memPrice['goods_consumer_price'][$m]-$salePrice-$vat;
    		$totalPrice=$salePrice+$vat;
	    }
	}
	$myClassName="<span class=\"special_tag\">".$myInfo->class_name."</span>";
}else if($role == 'administrator'){
    $rows = $memPrice[goods_cat_list];
    $clArr = explode("|", substr(substr($rows, 1), 0, -1));
    for($k=0;$k<sizeof($clArr); $k++){
        if($clArr[$k] != '0$0$0'){
            $salePrice=$memPrice['goods_member_price'][$k];
            $consumerPrice= $memPrice['goods_consumer_price'][$k];
            $vat=$memPrice['goods_vat'][$k];
            $savePrice=$memPrice['goods_consumer_price'][$k]-$salePrice-$vat;
            $totalPrice=$salePrice+$vat;
        }
    }
}else{
	$salePrice=$goods->goods_price;
	$savePrice=$goods->goods_consumer_price-$goods->goods_price;
	$myClassName="";
}

$salePercent=round((1-($salePrice/$goods->goods_consumer_price))*100,1);

if($goods->goods_company_display=='view' && $goods->goods_company){
	$compayNlocalLabel="제조사";
	$compayNlocalValue=$goods->goods_company;
}
if($goods->goods_local_display=='view' && $goods->goods_local){
	if($compayNlocalLabel) $compayNlocalLabel .="/";
	if($compayNlocalValue) $compayNlocalValue .="/";
	$compayNlocalLabel .="원산지";
	$compayNlocalValue .=$goods->goods_local;
}

$addFields=unserialize($goods->goods_add_field);

$optBasic=unserialize($goods->goods_option_basic);

$optAdd=unserialize($goods->goods_option_add);
$optAddFlag='0';
for($b=1;$b<=$optAdd['goods_add_option_count'];$b++){
	if($optAdd['goods_add_'.$b.'_use']=='on') $optAddFlag++;
}

$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
$deliveryInfo=unserialize($confData->config_data);

if(!$soldout) echo bbse_commerce_set_recent_goods($goods->idx,$_SERVER['REMOTE_ADDR']); // 최근 본 상품에 등록

$goodsCategory=bbse_goods_current_category($goods->goods_cat_list); // 현재 상품의 카테고리 idx, 카테고리명 추출 (hproduct용)


?>
	<hr />
<?php if(get_option($theme_shortname."_sub_goods_view_use_left_sidebar")=='U'){?>
	<div id="sidebar">
		<?php get_sidebar('main');?>
	</div><!--//#sidebar-->
<?php }?>
	<div id="content">
        <?php
        #로케이션
        get_template_part('part/sub', 'location');
		?>
		<div class="page_cont"  id="bbseGoods<?php echo $bbseGoods?>">

			<!--  상위 템플릿 <div class="page_cont"></div> 사이에 불려감 -->
			<div class="product_detail">
			  <div class="product_detail_info">
				<div class="title_area">
				  <h3 class="bb_detail_title">
					<?php echo $goods->goods_name;?>
				<?php if($goods->goods_icon_new=='view' || $goods->goods_icon_best=='view'){?>
					<em class="tag">
					<?php if($goods->goods_icon_new=='view'){?>
					  <span class="new_tag">NEW</span>
					<?php }?>
					<?php if($goods->goods_icon_best=='view'){?>
					  <span class="best_tag">BEST</span>
					<?php }?>
					<?php if($soldout){?>
					  <span class="soldout_tag_icon">SOLDOUT</span>
					<?php }?>
					</em>
				<?php }?>
				  </h3>
				  <?php echo ($goods->goods_description)?"<p>".$goods->goods_description."</p>":"";?>
				</div><!--//.title_area -->

				<div class="detail_area">
				  <div class="bb_thumbnail">
					<p class="bb_thumb">
					<?php if($salePrice>0){?>
					  <span class="tag_sale">
						<em><?php echo $salePercent;?></em>%
						<strong>SALE</strong>
					  </span>
					<?php }?>
					  <span id="goods-img-zoom"><img class="bb_thumbnail_big" src="<?php echo $basicBigImg['0'];?>" data-zoom-image="<?php echo $basicBigImg['0'];?>" alt="상품이미지 최대 사이즈" /></span>
					</p>
					<div class="bb_opt">
					  <dl class="bb_sns">
					<?php if(get_option($theme_shortname.'_sns_share_twitter')  == 'U' || get_option($theme_shortname.'_sns_share_facebook') == 'U' || get_option($theme_shortname.'_sns_share_hms')   == 'U' || get_option($theme_shortname.'_sns_share_kTalk')   == 'U' || get_option($theme_shortname.'_sns_share_kStory')   == 'U' || get_option($theme_shortname.'_sns_share_googleplus')   == 'U' ){?>
						<dt>상품공유</dt>
						<?php if(get_option($theme_shortname.'_sns_share_twitter')  == 'U'){?>
						<dd class="twitter">
							<a class="share-btn twitter" data-sns="twitter" href="//twitter.com/share?url=<?php echo home_url()."/?bbseGoods=".$goods->idx;?>&amp;text=<?php echo $goods->goods_name;?>" target="_blank" title="트위터에 공유하기">
								<img src="<?php echo BBSE_THEME_WEB_URL.'/images/sns/twitter.png'; ?>" />
							</a>
						</dd>
						<?php }?>
						<?php if(get_option($theme_shortname.'_sns_share_facebook')  == 'U'){?>
						<dd class="facebook"><a class="share-btn facebook" data-sns="facebook" href="//www.facebook.com/sharer.php?u=<?php echo home_url()."/?bbseGoods=".$goods->idx;?>&amp;p[title]=<?php echo $goods->goods_name;?>" target="_blank" title="페이스북에 공유하기">
							<img src="<?php echo BBSE_THEME_WEB_URL.'/images/sns/facebook.png'; ?>" />
						</a></dd>
						<?php }?>
						<?php if(get_option($theme_shortname.'_sns_share_naver')  == 'U'){?>
						<dd class="naver">
							<span>
								<script type="text/javascript" src="https://ssl.pstatic.net/share/js/naver_sharebutton.js"></script>
								<script type="text/javascript">
								new ShareNaver.makeButton({"type": "c"});
								</script>
							</span>
						</dd>
						<?php }?>
						<?php /*if(get_option($theme_shortname.'_sns_share_hms')  == 'U'){?>
						<dd class="hms"><a class="share-btn hms" data-sns="hms" href="//hyper-message.com/hmslink/sendurl?url=<?php echo home_url()."/?bbseGoods=".$goods->idx;?>" target="_blank" title="HMS에 공유하기">hms</a></dd>
						<?php }?>
						<?php if(get_option($theme_shortname.'_sns_share_googleplus')  == 'U'){?>
						<dd class="googleplus"><a class="share-btn googleplus" data-sns="googleplus" href="https://plus.google.com/share?url=<?php echo home_url()."/?bbseGoods=".$goods->idx;?>&t=<?php echo $goods->goods_name;?>" target="_blank" title="구글플러스에 공유하기">구글플러스</a></dd>
						<?php }?>
						<?php if(get_option($theme_shortname.'_sns_share_pinterest')  == 'U'){?>
						<dd class="pinterest"><a class="share-btn pinterest" data-sns="pinterest" data-surl="<?php echo home_url()."/?bbseGoods=".$goods->idx;?>" data-simg="<?php echo $basicBigImg['0'];?>" data-stxt="<?php echo $goods->goods_name;?>" href="#" title="핀터레스트에 공유하기">핀터레스트</a></dd>
						<?php }*/?>

					<?php if(wp_is_mobile()){?>
						<?php if(get_option($theme_shortname.'_sns_share_kTalk')=='U' && get_option($theme_shortname.'_sns_share_kakao_js_appkey')){?>
						<dd class="mobile kakaotalk">
							<a class="mobile kakaotalk kakaotalk-link" href="javascript:;"  data-key="<?php echo get_option($theme_shortname.'_sns_share_kakao_js_appkey')?>" data-domain="<?php echo home_url(); ?><?php echo $_SERVER['REQUEST_URI']?>" data-lable="<?php bloginfo('name'); ?>" 
								data-image="<?php echo $basicBigImg['0'];?>" data-msg="<?php echo $goods->goods_name;?>" title="카카오톡 공유하기">
								<img src="<?php echo BBSE_THEME_WEB_URL.'/images/sns/kakaot.png'; ?>" />
							</a></dd>
						<?php }
						if(get_option($theme_shortname.'_sns_share_kStory')=='U'){?>
						<dd class="mobile kakaostory">
							<a class="mobile kakaostory kakaostory-link" href="javascript:onclick=shareKakaoStory('<?php echo home_url(); ?><?php echo $_SERVER['REQUEST_URI']?>', '<?php echo $goods->goods_name;?>');" title="카카오스토리 공유하기">
								<img src="<?php echo BBSE_THEME_WEB_URL.'/images/sns/kakaos.png'; ?>" />
							</a></dd>
						<?php }?>
					<?php }?>
					<?php }?>
					  </dl>
					</div>

					<div class="bb_thumb_control">
					  <ul class="slide-list">
						<?php
						for($i=0;$i<sizeof($imageList);$i++){
							if($imageList[$i]==$goods->goods_basic_img) $basicActive="class=\"active\"";
							else $basicActive="";

							$goodsSmallImg=wp_get_attachment_image_src($imageList[$i],"goodsimage1");
							$goodsBigImg=wp_get_attachment_image_src($imageList[$i],"goodsimage8");

							echo "<li ".$basicActive."><a href=\"".$goodsBigImg['0']."\"><img src=\"".$goodsSmallImg['0']."\" alt=\"상품이미지 ".($i+1)."\" /></a></li>";
						}
						?>
					  </ul>
					  <p class="control_btn">
						<button type ="button" class="prev"><span>이전</span></button>
						<button type ="button" class="next"><span>다음</span></button>
					  </p>
					</div>
				  </div><!--//.bb_thumbnail -->

				  <form name="goodsFrm" id="goodsFrm">
				  <input type="hidden" name="tMode" id="tMode" value="">
				  <input type="hidden" name="sType" id="sType" value="">
				  <input type="hidden" name="goods_idx" id="goods_idx" value="<?php echo $goods->idx;?>">
				  <input type="hidden" name="home_url" id="home_url" value="<?php echo home_url();?>" />
				  <input type="hidden" name="goods_template_url" id="goods_template_url" value="<?php echo bloginfo('template_url');?>" />
				  <input type="hidden" name="login_url" id="login_url" value="<?php echo get_permalink(get_option($theme_shortname.'_login_page'));?>" />
				  <input type="hidden" name="goods_count_flag" id="goods_count_flag" value="<?php echo $goods->goods_count_flag;?>" />
				  <input type="hidden" name="goods_price" id="goods_price" value="<?php echo $salePrice;?>" />
				  <input type="hidden" name="goods_total_price" id="goods_total_price" value="<?php echo ($goods->goods_option_basic && ($optBasic['goods_option_1_count']>'0' || $optBasic['goods_option_2_count']>'0'))?"0":$totalPrice;?>" />
				  
				  <div class="product_info">
					<?php
						$goodsReviewTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$bbseGoods."'"); // 총 리뷰 수
						$goodsReviewStar = $wpdb->get_var("SELECT avg(r_value) FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$bbseGoods."'"); // 리뷰 평점
						$goodsQnaCount = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND goods_idx='".$bbseGoods."'"); // 총 Q&A 수
						$goodsOfferedCount = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_order_detail WHERE idx<>'' AND goods_idx='".$bbseGoods."'"); // 총 주문 수

						if($goodsReviewStar<'1') $goodsReviewStar='1';
						else $goodsReviewStar=ceil($goodsReviewStar);
					?>
					<?php if(!get_option($theme_shortname."_structured_type") || get_option($theme_shortname."_structured_type")=='Json-ld'){?>
					  <script type="application/ld+json">
						{
							  "@context": "http://schema.org/",
							  "@type": "Product",
							  "name": "<?php echo ($goods->goods_seo_use=='on' && $goods->goods_seo_title)?esc_html(strip_tags(stripslashes($goods->goods_seo_title))):esc_html(strip_tags(stripslashes($goods->goods_name)));?>",
							  "image": "<?php echo $basicBigImg['0'];?>",
							  "description": "<?php echo ($goods->goods_seo_use=='on' && $goods->goods_seo_description)?esc_html(strip_tags(stripslashes($goods->goods_seo_description))):esc_html(strip_tags(stripslashes($goods->goods_description)));?>",
							  "category": "<?php echo $goodsCategory['cName'];?>",
							  "mpn": "<?php echo $goods->goods_unique_code;?>",
							  "brand": {
								"@type": "Thing",
								"name": "<?php echo $goods->goods_company;?>"
							  },
							  "aggregateRating": {
								"@type": "AggregateRating",
								"ratingValue": "<?php echo $goodsReviewStar;?>",
								"reviewCount": "<?php echo $goodsReviewTotal;?>"
							  },
							  "offers": {
								"@type": "Offer",
								"priceCurrency": "KRW",
								"price": "<?php echo $goods->goods_price;?>",
								"itemCondition": "http://schema.org/NewCondition",
								"availability": "http://schema.org/<?php echo ($soldout)?'OutOfStock':'InStock';?>",
								"interactionCount": "<?php echo $goodsQnaCount;?>",
								"itemOffered":"Orders <?php echo $goodsOfferedCount;?>",
								"seller": {
								  "@type": "Organization",
								  "name": "<?php bloginfo('name'); ?>"
								}
							  }
							}
					  </script>
					<?php }elseif(get_option($theme_shortname."_structured_type")=='RDFa'){?>
					  <div class="blind" vocab="http://schema.org/" typeof="Product">
							<span property="brand"><?php echo $goods->goods_company;?></span>
							<span property="name"><?php echo ($goods->goods_seo_use=='on' && $goods->goods_seo_title)?esc_html(strip_tags(stripslashes($goods->goods_seo_title))):esc_html(strip_tags(stripslashes($goods->goods_name)));?></span>
							<span property="category"><a href="<?php echo home_url()."/?bbseCat=".$goodsCategory['cIdx'];?>"><?php echo $goodsCategory['cName'];?></a></span>
							<img propertyu="image" src="<?php echo $basicBigImg['0'];?>" alt="<?php echo $goods->goods_name;?>" />
							<span property="description"><?php echo ($goods->goods_seo_use=='on' && $goods->goods_seo_description)?esc_html(strip_tags(stripslashes($goods->goods_seo_description))):esc_html(strip_tags(stripslashes($goods->goods_description)));?></span>
							상품번호 #: <span property="mpn"><?php echo $goods->goods_unique_code;?></span>
							<span property="aggregateRating" typeof="AggregateRating">
								평점 : <span property="ratingValue"><?php echo $goodsReviewStar;?></span> 점, 총 <span property="reviewCount"><?php echo $goodsReviewTotal;?></span> 개의 리뷰
							</span>

							<span property="offers" typeof="Offer">
								소비자가 : <?php echo $goods->goods_consumer_price;?>
								<meta property="priceCurrency" content="KRW" />
								판매가 : <span property="price"><?php echo $goods->goods_price;?></span>
								<span property="seller" typeof="Organization">
									<span property="name"><?php bloginfo('name'); ?><?php wp_title(); ?></span>
								</span>
								<link property="itemCondition" href="http://schema.org/UsedCondition"/><?php echo ($soldout)?"품절".PHP_EOL:"판매중".PHP_EOL;?>
								<link property="availability" href="http://schema.org/InStock"/><?php echo ($soldout)?"품절된 상품입니다!".PHP_EOL:"지금 주문하세요!".PHP_EOL;?>
							</span>
					  </div>
					<?php }else{?>
					  <div class="blind" itemscope="true" itemtype="http://schema.org/Product">
							<meta itemprop="brand" content="<?php echo $goods->goods_company;?>" />
							<meta itemprop="name" content="<?php echo ($goods->goods_seo_use=='on' && $goods->goods_seo_title)?esc_html(strip_tags(stripslashes($goods->goods_seo_title))):esc_html(strip_tags(stripslashes($goods->goods_name)));?>" />
							<meta itemprop="category" content="<?php echo $goodsCategory['cName'];?>" />
							<meta itemprop="image" content="<?php echo $basicBigImg['0'];?>" alt="<?php echo esc_html(strip_tags(stripslashes($goods->goods_name)));?>" />
							<meta itemprop="description" content="<?php echo ($goods->goods_seo_use=='on' && $goods->goods_seo_description)?esc_html(strip_tags(stripslashes($goods->goods_seo_description))):esc_html(strip_tags(stripslashes($goods->goods_description)));?>" />
							<meta itemprop="mpn" content="<?php echo $goods->goods_unique_code;?>" />
							<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
								<meta itemprop="price" content="<?php echo $goods->goods_price;?>" />
								<meta itemprop="priceCurrency" content="KRW" />
								<meta property="itemCondition" href="http://schema.org/UsedCondition"/>
								<meta itemprop="availability" content="<?php echo ($soldout)?"OutOfStock":"InStock";?>" />
								<meta itemprop="interactionCount" content="<?php echo $goodsQnaCount;?>" />
								<meta itemprop="itemOffered" content="Orders <?php echo $goodsOfferedCount;?>" />
								<div itemprop="seller" itemscope itemtype="http://schema.org/Organization">
									<meta itemprop="name" content="<?php bloginfo('name'); ?><?php wp_title(); ?>" />
								</div>
							</div>
							<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
								<meta itemprop="ratingValue" content="<?php echo $goodsReviewStar;?>" />
								<meta itemprop="ratingCount" content="<?php echo $goodsReviewTotal;?>" />
							</div>
					  </div>
					<?php }?>
					<table class="">
					  <caption>상품 정보 표</caption>
					  <colgroup>
						<col style="width:110px;">
						<col style="width:auto;">
					  </colgroup>
					  <tbody>
					  <?php if($goods->goods_unique_code && $goods->goods_unique_code_display=='view'){?>
						<tr>
						  <th scope="row">고유번호</th>
						  <td><?php echo $goods->goods_unique_code;?></td>
						</tr>
					  <?php }?>
					  <?php if($goods->goods_barcode && $goods->goods_barcode_display=='view'){?>
						<tr class="bottom_line">
						  <th scope="row">바코드</th>
						  <td><?php echo $goods->goods_barcode;?></td>
						</tr>
					  <?php }?>
					  <?php 
					  //if($goods -> goods_cprice_display != 'view' && $consumerPrice>'0'){
					  
					  $cDisplay = unserialize($goods->goods_cprice_display);
					  $goodsPrice = unserialize($goods->goods_member_price);

					  
					  if(in_array($currUserClass, $cDisplay[goods_cprice_display])){
					      if($role != 'administrator'){
					  ?>

						<tr <?php echo ($goods->goods_tax_display!='view' && ($goods->goods_earn_use!='on' || $goods->goods_earn<='0'))?"class=\"bottom_line\"":"";?>>
						  <!-- <th scope="row">판매가격 <?php echo $myClassName;?></th> -->
						  <th scope="row">회원가격 <?php echo $myClassName;?></th>
						  <td>
							<span class="bb_price"><strong><?php echo number_format($salePrice);?></strong>원</span>
							<?php if(!in_array($currUserClass, $cDisplay[goods_cprice_display])){?>
							<span class="c_green"><?php echo ($savePrice>0)?"(".number_format($savePrice)."원 절약)":"";?></span>
							<?php }?>
						  </td>
						</tr>
					  <?php
                          }
                          else{
                      ?>
						<tr>
						  <th scope="row">회원등급</th>
						  <?php for($j=0; $j<sizeof($goodsPrice); $j++){
                                    $level = $goodsPrice['goods_member_level'][$j];
                                    $className=$wpdb->get_var("SELECT class_name FROM bbse_commerce_membership_class WHERE no=".$level);
						      ?>
						  <td><?php echo $className; ?></td>
						  <?php }?>
						</tr>
						<tr <?php echo ($goods->goods_tax_display!='view' && ($goods->goods_earn_use!='on' || $goods->goods_earn<='0'))?"class=\"bottom_line\"":"";?>>
						  <!-- <th scope="row">판매가격 <?php echo $myClassName;?></th> -->
						  <th scope="row">회원가격 <?php echo $myClassName;?></th>
							<?php 
							for($j=0; $j<sizeof($goodsPrice['goods_member_price']); $j++){
						       $mPrice = $goodsPrice['goods_member_price'][$j];
						       ?>
						       <td><?php echo number_format($mPrice),"원"; ?></td>
						       <?php 
						   }?>
						</tr>
					  <?php 
                              
                          }
					  }else{
					      if($role != 'administrator'){
					          ?>
						<tr>
						  <th scope="row">소비자가격</th>
						  <!-- <td class="bb_price_del"><del><?php echo number_format($goods->goods_consumer_price);?></del>원</td> -->
						  <td class="bb_price_del"><del><?php echo number_format($consumerPrice);?></del>원
						</tr>
						<tr <?php echo ($goods->goods_tax_display!='view' && ($goods->goods_earn_use!='on' || $goods->goods_earn<='0'))?"class=\"bottom_line\"":"";?>>
						  <!-- <th scope="row">판매가격 <?php echo $myClassName;?></th> -->
						  <th scope="row">회원가격 <?php echo $myClassName;?></th>
						  <td>
							<span class="bb_price"><strong><?php echo number_format($salePrice);?></strong>원</span>
							<?php if(!in_array($currUserClass, $cDisplay[goods_cprice_display])){?>
							<span class="c_green"><?php echo ($savePrice>0)?"(".number_format($savePrice)."원 절약)":"";?></span>
							<?php }?>
						  </td>
						</tr>
					  <?php
                          }
                          else{
                      ?>
						<tr>
						  <th scope="row">회원등급</th>
						  <?php for($j=0; $j<sizeof($goodsPrice); $j++){
                                    $level = $goodsPrice['goods_member_level'][$j];
                                    $className=$wpdb->get_var("SELECT class_name FROM bbse_commerce_membership_class WHERE no=".$level);
						      ?>
						  <td><?php echo $className; ?></td>
						  <?php }?>
						</tr>
						<tr>
						  <th scope="row">소비자가격</th>
						  <!-- <td class="bb_price_del"><del><?php echo number_format($goods->goods_consumer_price);?></del>원</td> -->
						  
						  <?php 
						  for($j=0; $j<sizeof($goodsPrice['goods_consumer_price']); $j++){
						       $cPrice = $goodsPrice['goods_consumer_price'][$j];
						       ?>
						       <td class="bb_price_del"><del><?php echo number_format($cPrice),"원";
						   }?></del></td>
						</tr>
						<tr <?php echo ($goods->goods_tax_display!='view' && ($goods->goods_earn_use!='on' || $goods->goods_earn<='0'))?"class=\"bottom_line\"":"";?>>
						  <!-- <th scope="row">판매가격 <?php echo $myClassName;?></th> -->
						  <th scope="row">회원가격 <?php echo $myClassName;?></th>
							<?php 
							for($j=0; $j<sizeof($goodsPrice['goods_member_price']); $j++){
						       $mPrice = $goodsPrice['goods_member_price'][$j];
						       ?>
						       <td><?php echo number_format($mPrice),"원"; ?></td>
						       <?php 
						   }?>
						</tr>
					  <?php 
                          }
					  }
					  ?>

						<?php if($goods->goods_ship_tf=='view' && $goods->goods_ship_price>'0'){?>
					  	<tr>
						  <th scope="row">개별배송비</th>
						  <td><span><?php echo number_format($goods->goods_ship_price);?>원</span></td>
						</tr>
					  <?php }?>
					<?php 
					if($role != 'administrator'){
    					if($goods->goods_tax_display=='view' && $vat > 0){?>
    					<tr class="bottom_line">
    					  <th scope="row">부가세</th>
    					  <!-- <td><span><?php echo number_format($goods->goods_tax);?>원</span></td> -->
    					  <td><span><?php echo number_format($vat);?>원</span></td>
    					</tr>
    				  <?php }
                    }
                    else{
                    ?>
						<tr class="bottom_line">
    					  <th scope="row">부가세</th>
    					  <!-- <td><span><?php echo number_format($goods->goods_tax);?>원</span></td> -->
						<?php for($j=0; $j<sizeof($goodsPrice['goods_vat']); $j++){
						       $vat = $goodsPrice['goods_vat'][$j];
						       ?>                   
    					  <td><span><?php echo number_format($vat);?>원</span></td>
    					  <?php }?>
    					</tr>
                    <?php 
                    }?>
					  <?php if($goods->goods_earn_use=='on' && $goods->goods_earn>'0'){?>
						<tr class="bottom_line">
						  <th scope="row">적립금</th>
						  <td><span class="c_blue"><?php echo number_format($goods->goods_earn);?>원</span> <span class="c_green">(구매확정 시 지급)</span></td>
						</tr>
					  <?php }?>
						<tr>
						  <th scope="row"><?php echo $compayNlocalLabel;?></th>
						  <td><?php echo $compayNlocalValue;?></td>
						</tr>
					  <?php if($deliveryInfo['delivery_charge_type']=='free' || ($deliveryInfo['delivery_charge_type']=='charge' && $deliveryInfo['condition_free_use']=='on' && $salePrice>=$deliveryInfo['total_pay'])){?>
						<tr class="bottom_line">
						  <th scope="row">배송비</th>
						  <td>무료배송</td>
						</tr>
					  <?php }?>
					  <?php if(sizeof($addFields['goods_add_field_title'])>'0'){
							for($a=0;$a<sizeof($addFields['goods_add_field_title']);$a++){
								if((sizeof($addFields['goods_add_field_title'])-1)==$a) $addFieldsClass="class=\"bottom_line\""; 
					  ?>
									<tr <?php echo $addFieldsClass;?>>
									  <th scope="row"><?php echo $addFields['goods_add_field_title'][$a];?></th>
									  <td><?php echo $addFields['goods_add_field_description'][$a];?></td>
									</tr>
					  <?php
							}
					  }
					  ?>
					  <?php if($goods->goods_count_flag=='goods_count' && $goods->goods_count_view=='on'){?>
						<tr class="bottom_line">
						  <th scope="row">재고수량</th>
						  <td><?php echo number_format($goods->goods_count);?>개 남음</td>
						</tr>
					  <?php }?>
					<?php
					if($soldout){
						$oCnfCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='order'");
						if($oCnfCnt>'0'){
							$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
							$orderInfo=unserialize($confData->config_data);
						}
					?>
					  </tbody>
					</table><!--//상품 정보 표 -->
					<div style="margin-top:50px;text-align:center;">
						<div class="bb_order_btn">
						  <button type="button" class="bb_btn cus_fill" style="background-color:#F26570;border-color:#E35561;"><strong class="big">품절된 상품입니다.</strong></button>
						</div><!--//결제 관련 버튼 -->

						<?php if($orderInfo['soldout_notice_use']=='on' && ($orderInfo['soldout_notice_sms']=='sms' || $orderInfo['soldout_notice_email']=='email')){?>
						<div class="bb_order_btn" style="width:100%;">
							<button type="button" onClick="<?php echo ($current_user->user_login)?"soldout_notice(".$goods->idx.",'insertNotice');":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');";?>" class="bb_btn cus_fill" title="품절상품 입고알림 신청" style="width:100% !important;height:53px;"><strong class="big">품절상품 입고알림 신청</strong></button>
							<div style="text-align:left;width:100%;font-weight:100;margin-top:10px;font-size:12px;">
							<?php
							if($orderInfo['soldout_notice_sms']=='sms' && $orderInfo['soldout_notice_email']=='email') $noticeMsg="휴대전화번호 및 E-mail ";
							elseif($orderInfo['soldout_notice_sms']=='sms') $noticeMsg="휴대전화번호";
							elseif($orderInfo['soldout_notice_email']=='email') $noticeMsg="E-mail ";
							?>
								※ 본 상품의 입고완료 시 <span style="color:#ED1C24;">'회원정보의 <?php echo $noticeMsg;?>'</span>로 입고알림을 보내드립니다.
							</div>
						</div>
						<?php }?>


					</div>

				<?php }else{?>
					  <?php if($goods->goods_option_basic && ($optBasic['goods_option_1_count']>'0' || $optBasic['goods_option_2_count']>'0')){?>
							<tr style="<?php echo ($goods->goods_buy_inquiry_tf == 'view' ? 'display:none;':''); ?>">
							  <th scope="col" colspan="2" class="s_title"><strong>옵션선택</strong></th>
							</tr>
						  <?php if($optBasic['goods_option_1_count']>'0' && $optBasic['goods_option_2_count']>'0'){?>
							<tr style="<?php echo ($goods->goods_buy_inquiry_tf == 'view' ? 'display:none;':''); ?>">
							  <th scope="row"><label for="bbOpt11"><?php echo $optBasic['goods_option_1_title'];?></label></th>
							  <td><input type="hidden" name="optCnt" id="optCnt" value="2" />
								<select name="basicOption_1" id="basicOption_1" onChange="detail_basicOptChange(2,1,'<?php echo $goods->idx;?>',this.value);">
									<option value="">::: 옵션선택 :::</option>
							  <?php
									for($p=0;$p<$optBasic['goods_option_1_count'];$p++){
										$optValue=$optStrFlalg="";
										$displayFlalg=true;

										if($goods->goods_count_flag=='option_count'){
											$optTotal_1_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s AND goods_option_item_display='view'", array(like_escape($optBasic['goods_option_1_item'][$p]." /")."%"))); 

											if($optTotal_1_count<='0') $displayFlalg=false;

											$optTotal_2_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s AND goods_option_item_count>'0' AND goods_option_item_soldout<>'soldout' AND goods_option_item_display='view'", array(like_escape($optBasic['goods_option_1_item'][$p]." /")."%"))); 

											if($optTotal_2_count<='0'){
												$optValue .="disabled";
												$optStrFlalg .=" | 품절";
											}
										}

										if($displayFlalg) echo "<option value=\"".$optBasic['goods_option_1_item'][$p]."\" ".$optValue.">".$optBasic['goods_option_1_item'][$p].$optStrFlalg."</option>";
									}
							  ?>
								</select>
							  </td>
							</tr>
							<tr>
							  <th scope="row"><label for="bbOpt12"><?php echo $optBasic['goods_option_2_title'];?></label></th>
							  <td>
								<span id="basicOption_2_List">
									<select name="basicOption_2" id="basicOption_2">
									  <option value="">::: 옵션선택 :::</option>
									</select>
								</span>
							  </td>
							</tr>
					  <?php
						  }
						  elseif($optBasic['goods_option_1_count']>'0'){
					  ?>
							<tr style="<?php echo ($goods->goods_buy_inquiry_tf == 'view' ? 'display:none;':''); ?>">
							  <th scope="row"><label for="bbOpt11"><?php echo $optBasic['goods_option_1_title'];?></label></th>
							  <td>
								<select name="basicOption_1" id="basicOption_1" onChange="detail_basicOptChange(1,1,'<?php echo $goods->idx;?>',this.value);">
									<option value="">::: 옵션선택 :::</option>
							  <?php
									for($p=0;$p<$optBasic['goods_option_1_count'];$p++){
										$optValue=$optStrFlalg=$optValue="";

										$optTotal_1_Data = $wpdb->get_row($wpdb->prepare("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s", array(like_escape($optBasic['goods_option_1_item'][$p])))); 

										if($optTotal_1_Data->goods_option_item_display=='view'){
											$optStrFlalg .=" (+".number_format($optTotal_1_Data->goods_option_item_overprice)."원)";

											if($goods->goods_count_flag=='option_count'){
												if($optTotal_1_Data->goods_option_item_count<='0' || $optTotal_1_Data->goods_option_item_soldout=='soldout'){
													$optValue .="disabled";
													$optStrFlalg .=" | 품절";
												}
												else{
													if($goods->goods_count_view=='on'){
														$optStrFlalg .=" | ".number_format($optTotal_1_Data->goods_option_item_count)."개 남음";
													}

													$optValue .="data-overprice=\"".$optTotal_1_Data->goods_option_item_overprice."\" data-count=\"".$optTotal_1_Data->goods_option_item_count."\"";
												}
											}
											else{
												$optValue .="data-overprice=\"".$optTotal_1_Data->goods_option_item_overprice."\"";
											}

											echo "<option value=\"".$optBasic['goods_option_1_item'][$p]."\" ".$optValue.">".$optBasic['goods_option_1_item'][$p].$optStrFlalg."</option>";
										}
									}
							  ?>
								</select>
							  </td>
							</tr>
					  <?php
						  }
						  elseif($optBasic['goods_option_2_count']>'0'){
					  ?>
							<tr style="<?php echo ($goods->goods_buy_inquiry_tf == 'view' ? 'display:none;':''); ?>">
							  <th scope="row"><label for="bbOpt11"><?php echo $optBasic['goods_option_2_title'];?></label></th>
							  <td>
								<select name="basicOption_2" id="basicOption_2" onChange="detail_basicOptChange(1,2,'<?php echo $goods->idx;?>',this.value);">
									<option value="">::: 옵션선택 :::</option>
							  <?php
									for($p=0;$p<$optBasic['goods_option_2_count'];$p++){
										$optValue=$optStrFlalg="";

										$optTotal_2_Data = $wpdb->get_row($wpdb->prepare("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s", array(like_escape($optBasic['goods_option_2_item'][$p])))); 

										if($optTotal_2_Data->goods_option_item_display=='view'){
											$optStrFlalg .=" (+".number_format($optTotal_2_Data->goods_option_item_overprice)."원)";

											if($goods->goods_count_flag=='option_count'){
												if($optTotal_2_Data->goods_option_item_count<='0' || $optTotal_2_Data->goods_option_item_soldout=='soldout'){
													$optValue .="disabled";
													$optStrFlalg .=" | 품절";
												}
												else{
													if($goods->goods_count_view=='on'){
														$optStrFlalg .=" | ".number_format($optTotal_2_Data->goods_option_item_count)."개 남음";
													}

													$optValue .="data-overprice=\"".$optTotal_2_Data->goods_option_item_overprice."\" data-count=\"".$optTotal_2_Data->goods_option_item_count."\"";
												}
											}
											else{
												$optValue .="data-overprice=\"".$optTotal_2_Data->goods_option_item_overprice."\"";
											}

											echo "<option value=\"".$optBasic['goods_option_2_item'][$p]."\" ".$optValue.">".$optBasic['goods_option_2_item'][$p].$optStrFlalg."</option>";
										}
									}
							  ?>
								</select>
							  </td>
							</tr>
					  <?php
						  }
					  }
					  ?>

					  <?php if($goods->goods_option_add && $optAdd['goods_add_option_count']>'0' && $optAddFlag>'0'){?>
							<tr style="<?php echo ($goods->goods_buy_inquiry_tf == 'view' ? 'display:none;':''); ?>">
							  <th scope="col" colspan="2" class="s_title"><strong>추가상품</strong></th>
							</tr>
						
							<?php
							for($t=1;$t<=$optAdd['goods_add_option_count'];$t++){
								  if($optAdd['goods_add_'.$t.'_use']=='on'){
							?>
									<tr>
									  <th scope="row"><label for="bbOpt21"><?php echo $optAdd['goods_add_'.$t.'_title'];?> <?php echo ($optAdd['goods_add_'.$t.'_choice']=='selection')?"(선택)":"(필수)";?></label></th>
									  <td>
										<select name="addOption_<?php echo $t;?>" id="addOption_<?php echo $t;?>" data-choice="<?php echo $optAdd['goods_add_'.$t.'_choice'];?>" data-title="<?php echo $optAdd['goods_add_'.$t.'_title'];?>" onChange="detail_addOptChange(<?php echo $t;?>,this.value);">
										  <option value="">::: 상품선택 :::</option>
										  <?php 
										  for($z=0;$z<$optAdd['goods_add_'.$t.'_item_count'];$z++){
											  $optAddValue=$optAddStrFlalg="";

											  if(!$optAdd['goods_add_'.$t.'_item_overprice'][$z]) $goods_add_item_overprice='0';
											  else $goods_add_item_overprice=$optAdd['goods_add_'.$t.'_item_overprice'][$z];

											  $optAddStrFlalg .="(".number_format($goods_add_item_overprice)."원)";
											  if($optAdd['goods_add_'.$t.'_item_display'][$z]=='view'){
												  if($optAdd['goods_add_'.$t.'_item_soldout'][$z]=='soldout'){
													  $optAddValue .="disabled";
													  $optAddStrFlalg .=" | 품절";
												  }
												  else $optAddValue .="data-price=\"".$goods_add_item_overprice."\"";

												  echo "<option value=\"".$optAdd['goods_add_'.$t.'_item'][$z]."\" ".$optAddValue.">".$optAdd['goods_add_'.$t.'_item'][$z].$optAddStrFlalg."</option>";
											  }
										  }
										  ?>
										</select> 
									  </td>
									</tr>						
					<?php
								  }
							}
					  }
					  ?>
							  </tbody>
							</table><!--//상품 정보 표 -->

							<h4 class="selected_item">선택한 상품</h4>

							<ul id="goods_basic_option_list" class="selected_opt_list" style="display:<?php echo (!$goods->goods_option_basic || ($optBasic['goods_option_1_count']<='0' && $optBasic['goods_option_2_count']<='0'))?"block":"none";?>">
							<?php 
							if(!$goods->goods_option_basic || ($optBasic['goods_option_1_count']<='0' && $optBasic['goods_option_2_count']<='0')){
								$rndId=rand(1000000,9999999);
							?>
							<li class="add_opt" id="sOpt_<?php echo $rndId;?>" data-stock="<?php echo $goods->goods_count;?>">
								<div class="bb_opt_name">상품수량<input type="hidden" name="goods_basic_title[]" id="goods_basic_title[]" value="단일상품" /></div>
								<div class="bb_opt_price">
								  <div class="bb_count">
									<button type="button" onClick="option_changeCount('basic','minus','<?php echo $rndId;?>',<?php echo $totalPrice;?>);" class="bb_minus"><span>수량감소</span></button>
									<button type="button" onClick="option_changeCount('basic','plus','<?php echo $rndId;?>',<?php echo $totalPrice;?>);" class="bb_plus"><span>수량증가</span></button>
									<label class="blind" for="goods_basic_count[]">수량</label><input type="text" name="goods_basic_count[]" id="goods_basic_count[]" value="1" readonly />
								  </div>
								  <em id="sOpt_unit_<?php echo $rndId;?>" class="bb_pri"><?php echo number_format($totalPrice);?>원</em>
								  <button type="button" class="bb_opt_del" onClick="alert('기본 상품수량은 삭제가 불가능합니다.     ');"><span>선택옵션 삭제</span></button>
								</div>
							  </li>
							<?php }?>

							</ul><!--//선택상품 리스트 -->
							<ul id="goods_add_option_list" class="selected_opt_list" style="display:none;">
							</ul><!--//선택상품 리스트 -->
							
							<?php
								//쿠폰존재여부
								$coupons = $wpdb->get_results('
									SELECT	*
									FROM	bbse_commerce_coupon
									WHERE	product_type = "all"
										OR
											(product_type="noall" AND product LIKE "%'.$bbseGoods.'%")
								');
								$coupon_cnt = 0;
								foreach ($coupons as $key => $value) {
									$use_coupon = $wpdb->get_var('
										SELECT	coupon_id
										FROM	bbse_commerce_coupon_log
										WHERE	user = "'.$current_user->user_login.'"	 AND coupon_id = "'.$value->idx.'"
									');
									if(empty($use_coupon)){
										$coupon_cnt ++;
									}
								}
								if(is_user_logged_in() && $coupon_cnt > 0){
									echo '
										<div class="coupon_wrap" style="margin: 10px 0 0;">
											<!--<h3 style="font-weight: bold;margin: 0 0 5px;">사용가능한 쿠폰</h3>-->
									';
									foreach ($coupons as $key => $value) {
										$use_coupon = $wpdb->get_var('
											SELECT	coupon_id
											FROM	bbse_commerce_coupon_log
											WHERE	user = "'.$current_user->user_login.'"	 AND coupon_id = "'.$value->idx.'"
										');
										if(!empty($use_coupon)){
											continue;
										}
										echo '
											<!--<div class="coupon_item">
												<img style="max-height: 60px;max-width: 150px;" src="'.$value->thumb.'" />
											</div> -->
										';
									}
									echo'
										</div>
									';
								}
							?>
							<p class="bb_total_price" id="view_total_price" style="display:<?php echo (!$goods->goods_option_basic || ($optBasic['goods_option_1_count']<='0' && $optBasic['goods_option_2_count']<='0'))?"block":"none";?>;">
							  총 합계금액 <strong><?php echo number_format($totalPrice);?>원</strong>원
							  
							</p><!--//총 금액-->
							<input type="hidden" name="max_cnt" id="max_cnt" value="<?php echo $goods->max_cnt; ?>" />
							<?php
				          	if($goods->goods_buy_inquiry_tf == 'view'){
				          		echo'<div class="inquiry_btn" style="margin: 20px 0 0;font-size: 16px;background: #999;display: inline-block;vertical-align: middle;padding: 10px 30px;color: #fff;">'.$goods->goods_buy_inquiry.'</div>';
				          	}
						  	?>
							<div class="bb_order_btn" style="<?php echo ($goods->goods_buy_inquiry_tf == 'view' ? 'display:none;':''); ?>">
								<div class="goods_max_cnt_des">
					          		<p style="font-weight: 500;margin:10px 0 0;letter-spacing: -1px;font-size: 17px;">
					          			<?php
					          			if($goods->max_cnt > 0):
					          			?>
					          			이 상품은 1회 최대 <?php echo $goods->max_cnt; ?>개까지 구입가능합니다.
					          			<?php
										endif;
										?>
					          		</p>
					          	</div>
							  <button type="button" onClick="go_buy('<?php echo ($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData'])?"social-direct":"direct";?>');" class="bb_btn cus_fill"><strong class="big">바로구매</strong></button>

							<?php
							$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사

							if($nPayData['guest_cart_use']=='on'){
							?>
							  <button type="button" onClick="go_buy('cart');" class="bb_btn cus_solid"><strong class="big">장바구니담기</strong></button>
							<?php }else{?>
							  <button type="button" onClick="<?php echo ($current_user->user_login || ($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']))?"go_buy('cart');":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');go_login();";?>" class="bb_btn cus_solid"><strong class="big">장바구니담기</strong></button>
							<?php }?>
							  <button type="button" onClick="<?php echo ($current_user->user_login || ($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']))?"go_buy('wishlist');":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');go_login();";?>" class="bb_btn basic bb_wish bb_wish_check"><strong class="big">찜</strong></button><!--N: 찜 체크 시 .bb_wish_check 추가-->

							  <div style="clear: both;margin-top:20px;"></div>
							  <?php 
							  if(!$soldout && (($nPayData['member_naver_pay_use']=='on' && $current_user->user_login) || !$current_user->user_login) && $nPayData['naver_pay_use']=='on' && $goods->goods_naver_pay=='on' && ($nPayData['naver_pay_type']=='real' || ($nPayData['naver_pay_type']=='test' && $_REQUEST['npayTest']==true))){ // [naver_pay_type] => test [naver_pay_id] => [naver_pay_auth_key] => [naver_pay_button_key] =>
								  if(wp_is_mobile()) {
									  $nPayScript="mobile/"; 
									  $nPayButton="MA";
									  $nPayWishUrl = ($nPayData['naver_pay_type']=='test')?"https://test-m.pay.naver.com/mobile/customer/wishList.nhn ":"https://m.pay.naver.com/mobile/customer/wishList.nhn ";
									  $nPayOrdUrl = ($nPayData['naver_pay_type']=='test')?"https://test-m.pay.naver.com/mobile/customer/order.nhn":"https://m.pay.naver.com/mobile/customer/order.nhn";
								  } // 모바일
								  else {
									  $nPayScript=""; 
									  $nPayButton="A";
									  $nPayWishUrl = ($nPayData['naver_pay_type']=='test')?"https://test-pay.naver.com/customer/wishlistPopup.nhn":"https://pay.naver.com/customer/wishlistPopup.nhn";
									  $nPayOrdUrl = ($nPayData['naver_pay_type']=='test')?"https://test-pay.naver.com/customer/order.nhn":"https://pay.naver.com/customer/order.nhn";
								  }

								  if($soldout) {$nPayEnable="N"; $nPayBuyButton="not_buy_nPay";} // 품절상품
								  else {$nPayEnable="Y"; $nPayBuyButton="buy_detailNpay";}
							  ?>
								<script type="text/javascript" src="//<?php echo ($nPayData['naver_pay_type']=='test')?"test-":"";?>pay.naver.com/cust2omer/js/<?php echo $nPayScript;?>naverPayButton.js" charset="UTF-8"></script>
								<script type="text/javascript" >
								//<![CDATA[
									naver.NaverPayButton.apply({
										BUTTON_KEY: "<?php echo $nPayData['naver_pay_button_key'];?>", // 페이에서 제공받은 버튼 인증 키 입력
										TYPE: "<?php echo $nPayButton;?>", // 버튼 모음 종류 설정
										COLOR: 1, // 버튼 모음의 색 설정
										COUNT: 2, // 버튼 개수 설정. 구매하기 버튼만 있으면(장바구니 페이지) 1, 장바구니/찜하기 버튼도 있으면(상품 상세 페이지) 2를 입력.
										ENABLE: "<?php echo $nPayEnable;?>", // 품절 등의 이유로 버튼 모음을 비활성화할 때에는 "N" 입력
										BUY_BUTTON_HANDLER:<?php echo $nPayBuyButton;?>, // 구매하기 버튼 이벤트 Handler 함수 등록, 품절인 경우 not_buy_nc 함수 사용
										WISHLIST_BUTTON_HANDLER: wishlist_nPay, // 찜하기 버튼 이벤트 Handler 함수 등록
										"":""
									});
								//]]>
								</script>
							  <?php
							  }
							  ?>
							</div><!--//결제 관련 버튼 -->
			<?php }?>
					
				  </div><!--//.product_info -->
				  </form>
			  <?php if((($nPayData['member_naver_pay_use']=='on' && $current_user->user_login) || !$current_user->user_login) && $nPayData['naver_pay_use']=='on' && $goods->goods_naver_pay=='on'){ // [naver_pay_type] => test [naver_pay_id] => [naver_pay_auth_key] => [naver_pay_button_key] =>?>
				  <div id="nPayResult" data-wish="<?php echo $nPayWishUrl;?>" data-agent="<?php echo (wp_is_mobile())?"mobile":"pc";?>" style="display:none;">
						<form id="nPayOrderFrm" name="nPayOrderFrm" method="get" target="_top" action="<?php echo $nPayOrdUrl;?>">
							<input type="hidden" id="nPay_order_orderId" name="ORDER_ID" value="">
							<input type="hidden" id="nPay_order_shopId" name="SHOP_ID" value="">
							<input type="hidden" id="nPay_order_totalPrice" name="TOTAL_PRICE" value="">
						</form>
					</div>
				<?php }?>
				</div>

			<?php
			if(get_option($theme_shortname."_sub_goods_view_use_left_sidebar")=='U'){
				if(($goods->goods_recommend_use=='on' && $goods->goods_recommend_list) || ($goods->goods_relation_use=='on' && $goods->goods_relation_list)){
					$recList=explode(",",$goods->goods_recommend_list);
					$relList=explode(",",$goods->goods_relation_list);
			?>
					<div class="main_section md_item">
						  <div style="height:30px;"></div>
						  <div class="md_wrap" style="border-top:1px solid #CECECE;">
							  <div class="tab_nav">
								  <ul class="tabs">
								  <?php if($goods->goods_recommend_use=='on' && sizeof($recList)>0){?>
									  <li><a href="#md1">추천상품</a></li>
								  <?php }?>
								  <?php if($goods->goods_relation_use=='on' && sizeof($relList)>0){?>
									  <li><a href="#md2">관련상품</a></li>
								  <?php }?>
								  </ul>
							  </div>
						  <?php if($goods->goods_recommend_use=='on' && sizeof($recList)>0){?>
							  <div class="tab-cont" id="md1">
								  <h4 class="blind">추천상품</h4>
								  <div class="basic_list">
									  <ul class="">
									  <?php 
									  if(sizeof($recList)>4) $recCnt='4';
									  else $recCnt=sizeof($recList);

									  for($rc=0;$rc<$recCnt;$rc++){
											$rData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$recList[$rc]."' AND (goods_display='display' OR goods_display='soldout')");
											if($rData->goods_name){
												$rSoldout=goodsSoldoutCheck($rData); //품절체크		

												$imgSizeKind = "goodsimage2";

												$imageList=explode(",",$rData->goods_add_img);
												$firstImg=$secondImg="";
												if($rData->goods_basic_img){
													$basicImg = wp_get_attachment_image_src($rData->goods_basic_img,$imgSizeKind);
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
									  ?>
										  <li>
											  <a href="<?php echo home_url()."/?bbseGoods=".$rData->idx?>">
												  <span class="tag">
														<?php if($rData->goods_icon_new=='view'){?>
														  <span class="new_tag">NEW</span>
														<?php }?>
														<?php if($rData->goods_icon_best=='view'){?>
														  <span class="best_tag">BEST</span>
														<?php }?>
														<?php if ($rSoldout){ ?><span class="soldout_tag"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>
												  </span>
												  <div class="img_view">
													  <img src="<?php echo $basicImg['0'];?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $rData->goods_name;?> 상품 이미지" />
												  </div>
												  <span class="subj"><?php echo $rData->goods_name;?></span>
												  <strong class="bb_price"><?php echo number_format($rData->goods_price);?>원</strong>
											  </a>
										  </li>
										<?php
											}
										}
										?>
									  </ul>
								  </div>
							  </div>
							  <!--//탭1 -->
							<?php
							}
							?>
						  <?php if($goods->goods_relation_use=='on' && sizeof($relList)>0){?>
							  <div class="tab-cont" id="md2">
								  <h4 class="blind">관련상품</h4>
								  <div class="basic_list">
									  <ul class="">
									  <?php 
									  if(sizeof($relList)>4) $relCnt='4';
									  else $relCnt=sizeof($relList);

									  for($re=0;$re<$relCnt;$re++){
											$reData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$relList[$re]."' AND (goods_display='display' OR goods_display='soldout')");
											if($reData->goods_name){
												$reSoldout=goodsSoldoutCheck($reData); //품절체크		
												$imgSizeKind = "goodsimage2";

												$imageList=explode(",",$reData->goods_add_img);
												$firstImg=$secondImg="";
												if($reData->goods_basic_img){
													$basicImg = wp_get_attachment_image_src($reData->goods_basic_img,$imgSizeKind);
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
									  ?>
										  <li>
											  <a href="<?php echo home_url()."/?bbseGoods=".$reData->idx?>">
												  <span class="tag">
														<?php if($reData->goods_icon_new=='view'){?>
														  <span class="new_tag">NEW</span>
														<?php }?>
														<?php if($reData->goods_icon_best=='view'){?>
														  <span class="best_tag">BEST</span>
														<?php }?>
														<?php if ($reSoldout){ ?><span class="soldout_tag"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>
												  </span>
												  <div class="img_view">
													  <img src="<?php echo $basicImg['0'];?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $reData->goods_name;?> 상품 이미지" />
												  </div>
												  <span class="subj"><?php echo $reData->goods_name;?></span>
												  <strong class="bb_price"><?php echo number_format($reData->goods_price);?>원</strong>
											  </a>
										  </li>
										<?php
											}
										}
										?>
									  </ul>
								  </div>
							  </div>
							  <!--//탭2 -->
							<?php
							}
							?>
						  </div>
					</div>
					<!--//연계상품 -->
		    <?php
				  }
			}
		    ?>
			  </div><!--//.product_detail_info -->
		  <?php
		 if(get_option($theme_shortname."_sub_goods_view_use_left_sidebar")!='U'){
		  ?>
  			  <div class="product_detail_side">
			<?php
				$rvTotal = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$goods->idx."' ORDER BY idx DESC"); // 총 상품평 수
				$rv_sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_review WHERE idx<>'' AND goods_idx='".$goods->idx."' ORDER BY idx DESC LIMIT %d, %d", array('0','5'));
				$rv_result = $wpdb->get_results($rv_sql);

				if($rvTotal>'0'){
			  ?>
					<div class="article best_cmt">
						<h3>고객상품평</h3>
						<p class="req">
						<a href="#bbProductDetail2" class="bb_more">더보기</a>
						</p>
						<ul class="bb_dot_list">
						<?php foreach($rv_result as $i=>$rvData) {?>
							<li><a href="javascript:void(0);" onclick="quickView_review(<?php echo $rvData->idx;?>);"><?php echo $rvData->r_subject;?></a></li>
						<?php }?>
						</ul>
					</div>
			<?php }else{?>
					<div class="article best_cmt">
						<h3>고객상품평</h3>
						<p class="req">
						<a href="#bbProductDetail2" class="bb_more">더보기</a>
						</p>
						<ul class="bb_dot_list">
							<li>상품평이 존재하지 않습니다.</li>
						</ul>
					</div>
			<?php }?>

			<?php
			if(($goods->goods_recommend_use=='on' && $goods->goods_recommend_list) || ($goods->goods_relation_use=='on' && $goods->goods_relation_list)){
				$recList=explode(",",$goods->goods_recommend_list);
				$relList=explode(",",$goods->goods_relation_list);
			?>
					<div class="article side_product_list">
					  <div class="basic_tabs">
						<ul class="tabs">
					  <?php if($goods->goods_recommend_use=='on' && sizeof($recList)>0){?>
						  <li class="active"><a href="#bbsideTab1">추천상품</a></li>
					  <?php }?>
					  <?php if($goods->goods_relation_use=='on' && sizeof($relList)>0){?>
						  <li><a href="#bbsideTab2">관련상품</a></li>
					  <?php }?>
						</ul>
					  </div>
			  <?php if($goods->goods_recommend_use=='on' && sizeof($recList)>0){?>
					  <div id="bbsideTab1" class="tab-cont">
						<ul>
				  <?php 
				  if(sizeof($recList)>4) $recCnt='4';
				  else $recCnt=sizeof($recList);

				  for($rc=0;$rc<$recCnt;$rc++){
						$rData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$recList[$rc]."' AND (goods_display='display' OR goods_display='soldout')");
						if($rData->goods_name){
							$imgSizeKind = "goodsimage1";

							$imageList=explode(",",$rData->goods_add_img);
							$firstImg=$secondImg="";
							if($rData->goods_basic_img){
								$basicImg = wp_get_attachment_image_src($rData->goods_basic_img,$imgSizeKind);
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
				  ?>
							  <li>
								<a href="<?php echo home_url()."/?bbseGoods=".$rData->idx?>">
								  <div class="img_view">
									<img src="<?php echo $basicImg['0'];?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $rData->goods_name;?> 상품 이미지" />
								  </div>
								  <span class="subj"><?php echo $rData->goods_name;?></span>
								  <strong class="bb_price"><?php echo number_format($rData->goods_price);?>원</strong>
								</a>
							  </li>
				<?php
						}
					}
				?>
					    </ul>
					  </div>
			<?php
			}
			?>
		  <?php if($goods->goods_relation_use=='on' && sizeof($relList)>0){?>
					  <div id="bbsideTab2" class="tab-cont">
						<ul>
					  <?php 
					  if(sizeof($relList)>4) $relCnt='4';
					  else $relCnt=sizeof($relList);

					  for($re=0;$re<$relCnt;$re++){
							$reData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$relList[$re]."' AND (goods_display='display' OR goods_display='soldout')");
							$imgSizeKind = "goodsimage1";

							$imageList=explode(",",$reData->goods_add_img);
							$firstImg=$secondImg="";
							if($reData->goods_name){
								if($reData->goods_basic_img){
									$basicImg = wp_get_attachment_image_src($reData->goods_basic_img,$imgSizeKind);
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
					  ?>
								<li>
									<a href="<?php echo home_url()."/?bbseGoods=".$reData->idx?>">
									  <div class="img_view">
									    <img src="<?php echo $basicImg['0'];?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $reData->goods_name;?> 상품 이미지" />
									  </div>
									  <span class="subj"><?php echo $reData->goods_name;?></span>
									  <strong class="bb_price"><?php echo number_format($reData->goods_price);?>원</strong>
									</a>
								</li>
					<?php
						}
					}
					?>
						</ul>
					  </div>
		  <!--//탭2 -->
			<?php
			}
			?>
					</div>
				  </div><!--//.product_detail_side -->
	<?php 
		  }
	  }
	 ?>
			</div><!--//.product_detail -->

		    <div style="clear: both;"></div>

			<div class="product_content">
			  <div class="detail_article">
				<div id="bbProductDetail1" class="basic_tabs">
				  <ul class="detail_tabs">
					<li class="active"><a href="#bbProductDetail1">상품상세설명</a></li>
					<li><a href="#bbProductDetail2">고객상품평</a></li>
					<li><a href="#bbProductDetail3">상품문의</a></li>
					<li><a href="#bbProductDetail4">배송/취소/교환 안내</a></li>
				  </ul>
				</div>
				<div class="product_content_detail">
					<?php if(get_option($theme_shortname."_sub_google_cse_use")=='U' && get_option($theme_shortname."_sub_google_cse_goodsUse")=='U' && get_option($theme_shortname."_sub_google_cse_code")){?>
					  <div id="bbseGoogleCse" style="float:<?php echo (get_option($theme_shortname."_sub_google_cse_align"))?get_option($theme_shortname."_sub_google_cse_align"):"right";?>;width:280px;margin-right:5px;">
						<style>
						#bbseGoogleCse .cse .gsc-control-cse, .gsc-control-cse{padding:1em 0;}
						</style>
						<?php echo stripslashes(get_option($theme_shortname."_sub_google_cse_code"));?>
					  </div>
					  <div style="display:block;clear:both;height:1px;content:'';"></div>
					<?php }?>

				  <?php echo bbse_commerce_tinymce_shortcord_parse(stripslashes($goods->goods_detail));?>
				</div>
			  </div><!--//상품상세설명 -->

			  <div class="detail_article">
				<div id="bbProductDetail2" class="basic_tabs">
				  <ul class="detail_tabs">
					<li><a href="#bbProductDetail1">상품상세설명</a></li>
					<li class="active"><a href="#bbProductDetail2">고객상품평</a></li>
					<li><a href="#bbProductDetail3">상품문의</a></li>
					<li><a href="#bbProductDetail4">배송/취소/교환 안내</a></li>
				  </ul>
				</div>
				<div class="product_content_detail">
				  <?php
				  #상품평
				  get_template_part('part/embed', 'goods_review');
				  ?>
				</div>
			  </div><!--//고객상품평 -->
			  <div style="margin-top:60px;"></div>

			  <div class="detail_article">
				<div id="bbProductDetail3" class="basic_tabs">
				  <ul class="detail_tabs">
					<li><a href="#bbProductDetail1">상품상세설명</a></li>
					<li><a href="#bbProductDetail2">고객상품평</a></li>
					<li class="active"><a href="#bbProductDetail3">상품문의</a></li>
					<li><a href="#bbProductDetail4">배송/취소/교환 안내</a></li>
				  </ul>
				</div>
				<div class="product_content_detail">
				  <?php
				  #상품문의
				  get_template_part('part/embed', 'goods_enquiry');
				  ?>
				</div>
			  </div><!--//상품문의 -->
			  <div style="margin-top:60px;"></div>

			  <div class="detail_article">
				<div id="bbProductDetail4" class="basic_tabs">
				  <ul class="detail_tabs">
					<li><a href="#bbProductDetail1">상품상세설명</a></li>
					<li><a href="#bbProductDetail2">고객상품평</a></li>
					<li><a href="#bbProductDetail3">상품문의</a></li>
					<li class="active"><a href="#bbProductDetail4">배송/취소/교환 안내</a></li>
				  </ul>
				</div>
				<div class="product_content_detail">
				<?php
					$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
					echo $confData->config_editor;
				?>
				</div>
			  </div><!--//배송/취소/교환 안내 -->
			</div><!--//.product_content -->
		</div>
	</div><!--//#content -->

	<?php if(wp_is_mobile() && get_option($theme_shortname.'_sns_share_kTalk')=='U' && get_option($theme_shortname.'_sns_share_kakao_js_appkey')){?>
	<script>
		jQuery(document).ready(function(){
			if (jQuery('.kakaotalk-link').size() > 0){
			  var $key    = jQuery('.kakaotalk-link').data('key');
			  var $domain = jQuery('.kakaotalk-link').data('domain');
			  var $lable  = jQuery('.kakaotalk-link').data('lable');
			  var $image  = jQuery('.kakaotalk-link').data('image');
			  var $msg    = jQuery('.kakaotalk-link').data('msg');

			  Kakao.init($key);
			  shareKakaoTalk($lable, $image, $msg, $domain);
			}
		});
	</script>
	<?php }?>
<?php get_footer();
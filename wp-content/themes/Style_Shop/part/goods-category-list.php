<?php
global $theme_shortname,$current_user;
$V = $_GET;
$startSearchPrice = (!$V['startSearchPrice'])?BBSE_COMMERCE_SEARCH_MIN_PRICE:$V['startSearchPrice'];
$endSearchPrice = (!$V['endSearchPrice'])?BBSE_COMMERCE_SEARCH_MAX_PRICE:$V['endSearchPrice'];
$sort_page = (!$V['sort_page'])?"reg_date":$V['sort_page'];

/* Search Vars */
#$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];//한 페이지에 표시될 목록수
$per_page = 50;
$page = (count($_POST)>0 || !$_REQUEST['page'])?1:intval($_REQUEST['page']);//현재 페이지
$start_pos = ($page-1) * $per_page; //목록 시작 위치
if($sort_page=="reg_date") {
	//$orderby = "goods_reg_date ";
	//$sort = "DESC";
	$orderby = "list_order ";
	$sort = "ASC";
}else if($sort_page=="price_asc") {
	$orderby = "goods_price ";
	$sort = "ASC";
}else if($sort_page=="price_desc") {
	$orderby = "goods_price ";
	$sort = "DESC";
}
 
/* Add Query */

$bbseCat = $V['bbseCat'];
if($V['bbseCat']=="all") $cateQuery = "";
else $cateQuery = getCategoryQuery($V['bbseCat'])." AND";
$addQuery = " AND goods_price >= ".$startSearchPrice." AND goods_price <= ".$endSearchPrice;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;
$member = $wpdb->get_results("SELECT * FROM bbse_commerce_membership where user_id = '".$currUserID."'");
$currUserClass = $member[0]->user_class;
$role = $current_user->roles[0];

/* List Query  */
//$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_goods WHERE ".$cateQuery." (goods_display='display' OR goods_display='soldout') ".$addQuery); //총 목록수
//$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_goods WHERE ".$cateQuery." (goods_display='display' OR goods_display='soldout') "); //총 목록수
$quer = "SELECT * FROM bbse_commerce_goods WHERE ".$cateQuery." (goods_display='display' OR goods_display='soldout') ORDER BY ".$orderby.$sort." LIMIT ".$start_pos.", ".$per_page;
$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE ".$cateQuery." (goods_display='display' OR goods_display='soldout') ORDER BY ".$orderby.$sort." LIMIT ".$start_pos.", ".$per_page);
$totalResult = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE ".$cateQuery." (goods_display='display' OR goods_display='soldout') ORDER BY ".$orderby.$sort);
$total = 0;

if($role == 'administrator'){
    $total = sizeof($totalResult);
}else{
    for($i =0; $i<sizeof($totalResult); $i++){
        $totalGood=unserialize($totalResult[$i]->goods_member_price);
        for($j=0; $j<sizeof($totalGood); $j++){
            if($totalGood[goods_member_level][$j] == $currUserClass) {
                if($totalGood[goods_member_price][$j] != '0'){
                    $total++;
                }
            }
        }
    }
}
$total_pages = ceil($total / $per_page); //총 페이지수
//$result = $wpdb->get_results("SELECT * FROM bbse_commerce_goods WHERE ".$cateQuery." (goods_display='display' OR goods_display='soldout') ".$addQuery." ORDER BY ".$orderby.$sort." LIMIT ".$start_pos.", ".$per_page);

/* Query String */
$add_args = array("per_page"=>$per_page,"startSearchPrice"=>$startSearchPrice, "endSearchPrice"=>$endSearchPrice, "sort_page"=>$sort_page);
$curURL =  home_url()."/?bbseCat=".$V['bbseCat']."&per_page=".$per_page."&startSearchPrice=".$startSearchPrice."&endSearchPrice=".$endSearchPrice;

/* 페이징 처리 정의 */	
$page_param = array();           
$page_param['page_row'] = $per_page;
$page_param['page_block'] = 10;      
$page_param['total_count'] = $total; 
$page_param['current_page'] = $page; 
$page_param['link_url'] = home_url()."/?bbseCat=".$V['bbseCat']."&".http_build_query($add_args);  
$page_class = new themePaging(); 
$page_class->initPaging($page_param); 

?>
<?php goodsCategoryList($_GET['bbseCat'])?>
<div class="article mobileCatSelectList">
	<h3 class="lv2_title">상품 카테고리</h3>
	<?php echo mobileCategorySelect($_GET['bbseCat']);?>
</div>

<!-- 상품 목록 -->
<div class="article">
	<form id="goodsListForm" name="goodsListForm" method="get">
	<input type="hidden" name="bbseCat" id="bbseCat" value="<?php echo $V['bbseCat']?>">
	<input type="hidden" name="sort_page" id="sort_page" value="<?php echo $sort_page?>">
	<!-- 상품 정렬 -->
	<div class="basic_tabs">
		<!-- <ul class="sorting_tabs"><!--N: 활성화는 .active -->
		<!--	<li<?php if($sort_page=="reg_date"){?> class="active"<?php }?>><button type="button" onclick="location.href='<?php echo $curURL?>&sort_page=reg_date';"><span>최신등록순</span></button></li>
			<li<?php if($sort_page=="price_asc"){?> class="active"<?php }?>><button type="button" onclick="location.href='<?php echo $curURL?>&sort_page=price_asc';"><span>낮은가격순</span></button></li>
			<li<?php if($sort_page=="price_desc"){?> class="active"<?php }?>><button type="button" onclick="location.href='<?php echo $curURL?>&sort_page=price_desc';"><span>높은가격순</span></button></li>
		</ul>
		 -->
<!-- 		<p class="sorting_select"> -->
<!-- 			<select name="per_page" id="per_page" title="리스트 갯수를 선택해주세요." onchange="jQuery('#goodsListForm').submit();">
				<option value="10" <?php echo ($per_page=="10")?"selected":""?>>10개씩 보기</option>
				<option value="20" <?php echo ($per_page=="20")?"selected":""?>>20개씩 보기</option>
				<option value="50" <?php echo ($per_page=="50")?"selected":""?>>50개씩 보기</option>
<!-- 			</select> -->
<!-- 		</p> -->
	</div>

	<div class="srp_search">
		<div class="fl_sort_count">
			현재 카테고리에 총 <strong><?php echo number_format($total)?></strong>개의 상품이 있습니다.
		</div>
		<!-- <div class="fr_search">
			<h4 class="bb_title">가격검색</h4>
			<div class="price_slider_wrap">
				<div class="price_info">
					<div class="bb_price_text"><span class="price_min priceComma"><?php echo $startSearchPrice;?></span> 원</div>
					<div class="ver_wrap">
						<div id="sliderPrice"></div>
						<!-- div class="sliderPrice_bg"></div -->
					<!-- </div>
					<div class="bb_price_text"><span class="price_max priceComma"><?php echo $endSearchPrice;?></span> 원</div>
					<button type="button" class="bb_btn gradient" onclick="jQuery('#goodsListForm').submit();"><span class="sml">검색</span></button>
					<!-- value로 받아올때-->
					<!-- <div class="price_limit">
						<label class="blind" for="startSearchPrice">시작가격</label><input type="text" id="startSearchPrice" name="startSearchPrice" class="price_min_input priceComma" value="<?php echo $startSearchPrice;?>" /> 원
						~
						<label class="blind" for="endSearchPrice">끝 가격</label><input type="text" id="endSearchPrice" name="endSearchPrice" class="price_max_input priceComma" value="<?php echo $endSearchPrice;?>" /> 원
						<!--N: 최초 가격 세팅시 js파일 내에서 최소가격과 최대가격을 input의 value값과 span에 표시될 가격을 맞추어 준다.-->
					<!-- </div>
				</div><!--//.price_info -->
			<!-- </div> --> <!--//.price_slider_wrap -->
			
			<script type="text/javascript">
				$(function () {
					$(document).ready(function(){
						//최소~최대 가격 슬라이드
						$('.priceComma').each(function(index){$(this).text($(this).text().split(/(?=(?:\d{3})+(?:\.|$))/g).join(','));});
						$( "#sliderPrice" ).slider({
							range: true,
							step: 1000,									//슬라이드할때 가격 단위
							min: <?php echo BBSE_COMMERCE_SEARCH_MIN_PRICE;?>,									//가격 최소 값
							max: <?php echo BBSE_COMMERCE_SEARCH_MAX_PRICE;?>,								//가격 최대 값
							values: [ <?php echo $startSearchPrice;?>, <?php echo $endSearchPrice;?> ],				//최초 세팅 최소가격~최대가격
							slide: function( event, ui ) {
								var pirceMin = ui.values[ 0 ],		//셋팅 된 최소가격
									pirceMax = ui.values[ 1 ];		//셋팅 된 최대가격
								// value로 받아올때
								$('.price_limit').find('.price_min_input').val(pirceMin);
								$('.price_limit').find('.price_max_input').val(pirceMax);
								//text로 뿌려주고 3자리 콤마
								$('.price_info').find('.price_min').text(pirceMin);
								$('.price_info').find('.price_max').text(pirceMax);
								$('.priceComma').each(function(index){$(this).text($(this).text().split(/(?=(?:\d{3})+(?:\.|$))/g).join(','));});
							}
						});
					});
				});
			</script>
		</div>
	</div><!--//.srp_search -->


	<!-- 상품목록 : 5열 x X행 기본 상품 목록 -->
	<div class="lp_list">
		<ul class="">
		<?php
		
		if(plugin_active_check('BBSe_Commerce')) {
			if($total > 0) {
				foreach($result as $goods){
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
				    
				    //$salePrice=round((1-($goods->goods_price/$goods->goods_consumer_price))*100,1);
				    //if($salePrice>100) $salePrice .="%";
				    //else $salePrice .="%";
				    
				    $url = home_url()."/?bbseGoods=".$goods->idx;
				    $target = '';
				    if($goods->goods_external_link_tf == 'view'){
				        //$url = $goods->goods_external_link;
				        $target = '_blank';
				    }
					
					$memPrice=unserialize($goods->goods_member_price);
					$cPrice = "";
					$mPrice = "";
					$vat = "";
				    if($role != 'administrator'){
    					$rows = $memPrice[goods_member_level];
    					for($i=0; $i<sizeof($rows); $i++){
    					    if($rows[$i] == $currUserClass){
    					        $cPrice = $memPrice[goods_consumer_price][$i];
    					        $mPrice = $memPrice[goods_member_price][$i];
    					        $vat = $memPrice[goods_vat][$i];
    
        					    if($cPrice !=0 && $cPrice != null){
        					        
        					        $salePrice=round((1-($cPrice/($mPrice+$vat)))*100,1);
        					        if($salePrice>100) $salePrice .="%";
        					        else $salePrice .="%";
        					    ?>
        
                    			<li>
                    				<div class="hover">
                    					<a href="javascript:void(0);" onClick="go_detail('<?php echo $url?>');"><button type="button" class="bb_detail"><span>상세보기</span></button></a>
                    					<a href="javascript:void(0);" onClick="<?php echo ($current_user->user_login)?"go_wishlist(".$goods->idx.");":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');";?>"><button type="button"><span>찜하기</span></button></a>
                    				</div>
                    				<a href="<?php echo $url?>" target="<?php echo $target;?>">
                    					<div class="img_view">
                    						<img class="bb_thumb" src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
                    					</div>
                    					<div class="tag">
                    						<?php if ($goods->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
                    						<?php if ($goods->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
                    						<?php if ($soldout){ ?><span class="soldout_tag glist"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>&nbsp;
                    					</div>
                    					<strong class="subj"><?php echo $goods->goods_name?></strong>
                    					<div class="bb_price_info">
                    						<?php
                    						//if($goods -> goods_cprice_display != 'view'):
                    						$cDisplay = unserialize($goods->goods_cprice_display);
                    						
                    						if(in_array($currUserClass, $cDisplay[goods_cprice_display])){
                    						?>
                    						<em class="bb_price"><?php echo number_format($mPrice+$vat)?>원</em>
                    						<?php
                    						}else{
                    						?>
            									<span class="bb_price_del"><del><?php echo number_format($cPrice);?></del>원</span>
            									<span class="bb_sale"><?php echo $mPrice+$vat;?>원</span>
            									<em class="bb_price"><?php echo number_format($mPrice+$vat);?>원</em>
                    						<?php 
                    						}
                    						?>
                    					</div>
                    				</a>
                    			</li>
    		<?php
    			               }
    					    }
                        }
                    
				    }else if($role == 'administrator'){  
   				        $rows = $memPrice[goods_cat_list];
				        $clArr = explode("|", substr(substr($rows, 1), 0, -1));
			            for($k=0;$k<sizeof($clArr); $k++){
			                $clArr2 = explode("$",$clArr[$k]);
			                for($z=0;$z<sizeof($clArr2); $z++){
			                    if($bbseCat == $clArr2[$z]){
                                    $cPrice = $memPrice[goods_consumer_price][$k];
                                    $mPrice = $memPrice[goods_member_price][$k];
                                    $vat = $memPrice[goods_vat][$k];
    		                    }
			                }
			            }
                            if($cPrice > 0){
        ?>
            			<li>
            				<div class="hover">
            					<a href="javascript:void(0);" onClick="go_detail('<?php echo $url?>');"><button type="button" class="bb_detail"><span>상세보기</span></button></a>
             					<a href="javascript:void(0);" onClick="<?php echo ($current_user->user_login)?"go_wishlist(".$goods->idx.");":"alert('회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ');";?>"><button type="button"><span>찜하기</span></button></a>
            				</div>
            				<a href="<?php echo $url?>" target="<?php echo $target;?>">
            					<div class="img_view">
            						<img class="bb_thumb" src="<?php echo $basicImg['0']?>" data-firstimg="<?php echo $firstImg;?>" data-secondimg="<?php echo $secondImg;?>" alt="<?php echo $goods->goods_name?>" />
            					</div>
            					<div class="tag">
            						<?php if ($goods->goods_icon_new=='view'){ ?><span class="new_tag">NEW</span><?php }?>
            						<?php if ($goods->goods_icon_best=='view'){ ?><span class="best_tag">BEST</span><?php }?>
            						<?php if ($soldout){ ?><span class="soldout_tag glist"><img src="<?php bloginfo('template_url')?>/images/icon_soldout.png" alt="SOLDOUT"/></span><?php }?>&nbsp;
            					</div>
            					<strong class="subj"><?php echo $goods->goods_name?></strong>
            					<div class="bb_price_info">
            						<?php
            						//if($goods -> goods_cprice_display != 'view'):
            						$cDisplay = unserialize($goods->goods_cprice_display);
            						
            						if(in_array($currUserClass, $cDisplay[goods_cprice_display])){
            						    ?>
									<span class="bb_sale"><?php echo $mPrice?></span>
            						<?php
            						}else{
            						?>
            									<del><?php echo number_format($cPrice)?>원</del>
            									<span class="bb_sale"><?php echo $mPrice+$vat?></span>
            									<em class="bb_price"><?php echo number_format($cPrice)?>원</em>
            						<?php 
            						}
            						?>
            					</div>
            				</a>
            			</li>
            			
    	<?php           
                        }        
				    }
			    }
            }
		}else{echo "<li style='width:90%;color:red;'>BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.</li>";}
		?>

		</ul>
	</div><!--//.lp_list -->
	</form>
</div><!--// .article -->

<?php echo $page_class->getPaging();?>



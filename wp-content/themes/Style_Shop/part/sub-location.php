		<!-- 서브 상단 로케이션 -->
        <?php 
		global $theme_shortname;
		if($_GET['bbseCat'] != "" || $_GET['bbseGoods'] != "") {
			if($_GET['bbseCat'] != "") {
				$bbseNav = $_GET['bbseCat'];
			}else{
				$cate = $wpdb->get_var("select goods_cat_list from bbse_commerce_goods where idx='".$_GET['bbseGoods']."'");
				$expCate = explode("|", $cate);
				$bbseNav = $expCate[1];
			}
			$navKind = "goods";
		}else{
			$navKind = "page";
		}

		if(is_category() && !$_GET['cat']) $_GET['cat'] =$cat;
		if(is_single() && !$_GET['p']) $_GET['p'] =get_the_ID();

		if($navKind == "goods"){
		?>
		<div class="location">
			<ul class="">
				<li><a href="<?php echo home_url()?>">HOME</a></li>
				<?php 
				switch($bbseNav) {
					case "recommend":
						$nav_title = get_option($theme_shortname."_goodsplace_title_1");//추천상품
						break;
					case "best":
						$nav_title = get_option($theme_shortname."_goodsplace_title_2");//베스트상품
						break;
					case "md":
						$nav_title = get_option($theme_shortname."_goodsplace_title_3");//MD기획상품
						break;
					case "new":
						$nav_title = get_option($theme_shortname."_goodsplace_title_4");//신상품;
						break;
					case "today":
						$nav_title = "오늘만특가";
						break;
					case "hot":
						$nav_title = "핫아이템";
						break;
					case "search":
						$nav_title = "상품검색";
						break;
					default :
						$nav_title = "";
						topCategorySelect($bbseNav);
						break;
				}
				if($nav_title) echo "<li><strong>".$nav_title."</strong></li>";
				?>
			</ul>
		</div><!--.location -->
		<?php 
		}else if($_GET['bbsePage']!="") { 
		?>
		<div class="location">
			<ul class="">
				<li><a href="<?php echo home_url()?>">HOME</a></li>
				<?php
					switch($_GET['bbsePage']) {
						case "review":
							$nav_title = get_option($theme_shortname."_goodsplace_title_5");//베스트 후기
							break;
						case "cart":
							$nav_title = "장바구니";
							break;
						case "order-agree":

							$nav_title = ($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData'])?"소셜(간편) 로그인 구매 동의":"비회원 구매 동의";
							break;
						case "order":
							$nav_title = "주문서작성";
							break;
						case "order-payment":
							$nav_title = "주문확인/결제";
							break;
						case "order-ok":
							$nav_title = "주문접수완료";
							break;
					}
					if($nav_title) echo "<li><strong>".$nav_title."</strong></li>";
				?>
			</ul>
		</div><!--.location -->
		<?php 
		}else if($_GET['bbseMy']!="") { 
		?>
		<div class="location">
			<ul class="">
				<li><a href="<?php echo home_url()?>">HOME</a></li>
				<?php if($_GET['bbseMy']!="mypage"){?><li><a href="<?php echo home_url();?>/?bbseMy=mypage">마이페이지</a></li><?php }?>
				<?php
					switch($_GET['bbseMy']) {
						case "mypage":
							$nav_title = "마이페이지";
							break;
						case "order-list":
						case "order-detail":
							$nav_title = "주문/배송조회";
							break;
						case "refund":
							$nav_title = "취소/반품신청조회";
							break;
						case "interest":
							$nav_title = "관심상품";
							break;
						case "coupon":
							$nav_title = "쿠폰내역";
							break;
/* 						case "point":
							$nav_title = "적립금내역";
							break; */
						case "man2man":
							$nav_title = "나의1:1문의";
							break;
						case "soldoutNotice":
							$nav_title = "품절상품 입고알림";
							break;
							
						case "myInven":
						    $nav_title = "재고관리";
						    break;
						case "inven-detail":
						    $nav_title = "재고관리 항목추가";
					}
					if($nav_title) echo "<li><strong>".$nav_title."</strong></li>";
				?>
			</ul>
		</div><!--.location -->
		<?php 
		}else if($_GET['cat']!="") { 
			$printPath=get_cat_name($_GET['cat']);
			if(!$printPath) $printPath="404 Page not found !";
		?>
			<div class="location">
				<ul class="">
					<li><a href="<?php echo home_url()?>">HOME</a></li>
					<li><strong><?php echo $printPath; ?></strong></li>
				</ul>
			</div><!--.location -->
		<?php 
		}else if($_GET['author']!="") { 
		?>
			<div class="location">
				<ul class="">
					<li><a href="<?php echo home_url()?>">HOME</a></li>
					<li><strong>작성자 검색</strong></li>
				</ul>
			</div><!--.location -->
		<?php 
		}else if($_GET['m']!="") { 
		?>
			<div class="location">
				<ul class="">
					<li><a href="<?php echo home_url()?>">HOME</a></li>
					<li><strong>작성일 검색</strong></li>
				</ul>
			</div><!--.location -->
		<?php 
		}else if($_GET['p']!="") { 
			$getPost = get_post($_GET['p']); 
			if($getPost->ID>'0') {
				$category = get_the_category(); 
				$printPath=$category[0]->cat_name;
			}
			else $printPath="404 Page not found !";
		?>
			<div class="location">
				<ul class="">
					<li><a href="<?php echo home_url()?>">HOME</a></li>
					<li><strong><?php echo $printPath;?></strong></li>
				</ul>
			</div><!--.location -->
		<?php }else{
			$current_page_info = get_queried_object();
			$page_navi_view = "";
			$current_page_id = $wp_query->get_queried_object_id();
			if(get_option('bbse_commerce_login_page') == $current_page_info->ID) {
				$page_navi_view = '
					<li>회원서비스</li>
					<li><strong>로그인</strong></li>
				';
			}else if(get_option($theme_shortname.'_join_page') == $current_page_info->ID) {
				/* if(!is_user_logged_in()){
					$page_navi_view = '
						<li>회원서비스</li>
						<li><strong>회원가입</strong></li>
					';
				}else{
					$page_navi_view = '
						<li>마이페이지</li>
						<li><strong>회원정보변경</strong></li>
					';
				} */
			    
			    //fixme 2024.10.02 상단 회원가입 hidden
			    if(is_user_logged_in()){
    			    $page_navi_view = '
    						<li>마이페이지</li>
    						<li><strong>회원정보변경</strong></li>
    					';
			    }
			}else if(get_option($theme_shortname.'_id_search_page') == $current_page_info->ID) {
				$page_navi_view = '
					<li>회원서비스</li>
					<li><strong>아이디찾기</strong></li>
				';
			}else if(get_option($theme_shortname.'_pass_search_page') == $current_page_info->ID) {
				$page_navi_view = '
					<li>회원서비스</li>
					<li><strong>비밀번호찾기</strong></li>
				';
			}else if(get_option($theme_shortname.'_delete_page') == $current_page_info->ID) {
			    //fixme 2024.10.02 회원탈퇴 hidden
/* 				$page_navi_view = '
					<li>회원서비스</li>
					<li><strong>회원탈퇴</strong></li>
				'; */
			}elseif($current_page_info->ID>'0'){
				$page_navi_view = '<li><strong>'.$current_page_info->post_title.'</strong></li>';
			}
			else $page_navi_view = "<li><strong>404 Page not found !</strong></li>";
		?>
		<div class="location">
			<ul class="">
				<li><a href="<?php echo home_url()?>">HOME</a></li>
				<?php echo $page_navi_view;?>
			</ul>
		</div><!--.location -->
		<?php }?>
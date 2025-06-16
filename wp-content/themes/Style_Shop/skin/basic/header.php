<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

	//exit;
	global $current_user,$theme_shortname,$currentSessionID;
	$screen_code = $wpdb->get_row("select screen_code from bbse_commerce_membership where user_id = '".$current_user->user_login."' and leave_yn != 1");
	wp_get_current_user();
	$page_setting['login_page'] = get_option($theme_shortname."_login_page");
	$page_setting['id_search_page'] = get_option($theme_shortname."_id_search_page");
	$page_setting['join_page'] = get_option($theme_shortname."_join_page");
	$page_setting['pass_search_page'] = get_option($theme_shortname."_pass_search_page");
	$page_setting['delete_page'] = get_option($theme_shortname."_delete_page");
	
	if(get_option($theme_shortname."_basic_hreflang_use")=='U'){
		$hrefLnag=(get_option($theme_shortname."_basic_hreflang"))?get_option($theme_shortname."_basic_hreflang"):"ko";
		$hrefLnag_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	}
	else $hrefLnag=$hrefLnag_url="";

	 if(is_user_logged_in() && plugin_active_check('BBSe_Commerce')){
		 $order_url = home_url()."/?bbseMy=order-list";
		$cartListCount = $wpdb->get_var("select count(*) from bbse_commerce_cart where cart_kind='C' and user_id='".$current_user->user_login."'");
	 }else{
		$order_url = get_permalink($page_setting['login_page']);
		
		$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사
		if($nPayData['guest_cart_use']=='on' && plugin_active_check('BBSe_Commerce')){ // 추가작업
			$cartListCount = $wpdb->get_var("select count(*) from bbse_commerce_cart where cart_kind='C' and user_id='".$_SERVER['REMOTE_ADDR']."'");
		}
		elseif($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
			if($_SESSION['snsLoginData']){
				$snsData=unserialize($_SESSION['snsLoginData']);
				$cartListCount = $wpdb->get_var("select count(*) from bbse_commerce_cart where cart_kind='C' and user_id='".$snsData['sns_id']."'");
				 $order_url = home_url()."/?bbseMy=order-list";
			}
			else $cartListCount = 0;
		}
		else $cartListCount = 0;
	}
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> -->
<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
<?php if(get_option($theme_shortname.'_basic_favorit_icon')){?>
<link href="<?php echo get_option($theme_shortname.'_basic_favorit_icon')?>" type="image/x-icon" rel="icon" />
<?php }?>
<!-- //<![CDATA[ -->
<?php wp_head(); ?>
<!-- //]]> -->
<meta content="user-scalable=yes, initial-scale = 1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width" name="viewport" />

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">

<!-- 개발과 연계되어 수정되어야 하는 스타일 값들 -->
<link rel='stylesheet' href='<?php bloginfo('template_url')?>/style-temp.css' type='text/css' media='all' />

<?php if($hrefLnag && $hrefLnag_url){?>
<link rel="alternate" hreflang="<?php echo $hrefLnag;?>" href="<?php echo $hrefLnag_url;?>" />
<?php }?>

<?php
$nPayData=bbse_nPay_check(); // 네이버 페이 및 비회원 장바구니 사용여부 검사
if($nPayData['naver_pay_use']=='on' && ($nPayData['naver_pay_type']=='real' || ($nPayData['naver_pay_type']=='test' && $_REQUEST['npayTest']==true))){
	if(substr(home_url(),0,11)=='http://www.')	$pathDomain=str_replace("http://www.","",home_url());
	else $pathDomain=str_replace("http://","",home_url());
?>
<script type="text/javascript" >//<![CDATA[
	// Account ID 적용
	if (!wcs_add) var wcs_add = {};
	wcs_add["wa"] = "<?php echo $nPayData['naver_pay_common_key'];?>"; //네이버 공통 인증키
	wcs.inflow("<?php echo $pathDomain;?>"); // 유입 추적 함수 호출
	wcs_do(); // 로그수집
//]]></script>
<?php }?>

<!-- 테마컬러 적용을 위한 스타일 -->
<?php get_template_part('part/color', 'set');?>
<style>
	#header #gnb > ul > li > a{
		font-size: <?php echo get_option($theme_shortname."_color_mainmenu_font_size"); ?>;
		color: <?php echo get_option($theme_shortname."_color_mainmenu_font"); ?>;
	}
	.head_wrap{
		background-color: <?php echo get_option($theme_shortname."_color_mainmenu_bg"); ?>;
	}
</style>
</head>
<body>
	<ul class="skipnavi">
		<li><a href='#gnb'>카테고리 바로가기</a></li>
		<li><a href='#container'>본문 바로가기</a></li>
	</ul>
<?php
//상단 배너 추가
$use_top_banner = get_option($theme_shortname.'_sub_use_top_banner');
if($use_top_banner == 'U'){
	echo '
		<div id="bbs_top_banner">
			<a href="'.get_option($theme_shortname.'_sub_top_banner_link_1').'" target="'.get_option($theme_shortname.'_sub_top_banner_link_1_window').'">
				<img src="'.get_option($theme_shortname.'_sub_top_banner_image_1').'" style="display: block;margin: 0 auto;" />
			</a>
			<a class="close_top_banner"><span class="dashicons dashicons-no-alt"></span></a>
		</div>
		<script>
			jQuery(".close_top_banner").click(function(){
				jQuery("#bbs_top_banner").hide();
			});
		</script>
	';
}
?>
	<div id="wrap">
    <!--N: includ header S -->
		<div class="mb_top_top">
			<a class="mb_logo" href="https://autonomouskr.shop" style="float:left;"><img src="http://autonomouskr.shop/wp-content/uploads/2024/07/header-logo.png" alt="오토기기" style="margin-top: 4px;"></a>
			<button class="mb_menu">보기</button>
		</div>
		
		<div class="mb_top">
			<!-- <button class="mb_menu">보기</button> -->
			<div id="searchArea">
				<button type="button" class="total_search toggle_search"><span>검색창 열기</span></button>
				<div class="search_box ">
				<form name="topSearchFrom" id="topSearchFrom" method="get">
					<input type="hidden" name="bbseCat" value="search">
					<div class="input_search">
						<input type="text" name="keyword" id="keyword" placeholder="검색어를 입력해주세요." />
						<!--label for="keyword" style="display:none;">검색어를 입력해주세요.</label-->
					</div>
					<button type="submit" id="pc_search_button" class="total_search"><span>검색</span></button>
				</form>
				</div>
				<a href="<?php echo home_url();?>/?bbsePage=cart" class="ic_cart"><span class="blind">장바구니</span><strong class="count"><?php echo $cartListCount; ?></strong></a>
			</div><!--//#searchArea -->
		</div>
		<div class="pc_wrap">
			<h1 class="logo_main">
				<?php if(get_option($theme_shortname."_basic_logo_type_top")=="image"){?>
				<a href="<?php echo BBSE_COMMERCE_SITE_URL?>"><img src="<?php echo get_option($theme_shortname."_basic_logo_img_top")?>" alt="<?php bloginfo('name');?>" /></a>
				<?php }else if(get_option($theme_shortname."_basic_logo_type_top")=="text"){?>
				<a href="<?php echo BBSE_COMMERCE_SITE_URL?>" style="color:<?php echo get_option($theme_shortname."_basic_logo_color_top")?>;font-size:<?php echo get_option($theme_shortname."_basic_logo_text_size_top")?>px;"><?php echo get_option($theme_shortname."_basic_logo_text_top")?></a>
				<?php }else{echo bloginfo('name');}?>
			</h1>
		</div>
		<div id="header">
			<div id="utill">
				<div class="utill_wrap">
					<?php if(!is_user_logged_in() && ($_SESSION['snsLogin']!='Y' || !$_SESSION['snsLoginData'])){?>
					<a href="<?php echo get_permalink($page_setting['login_page'])?>" title="로그인">로그인<?php if(bbse_sns_icon_view()){?> <img src="<?php bloginfo('template_url')?>/images/icon_sns_login.png" align="absmiddle" style="margin-top:-2px;" alt="소셜(간편) 로그인" /><?php }?></a>
					<!-- <a href="http://localhost/wp-content/themes/Style_Shop/skin/basic/member-login.php" title="로그인">로그인<?php if(bbse_sns_icon_view()){?> <img src="<?php bloginfo('template_url')?>/images/icon_sns_login.png" align="absmiddle" style="margin-top:-2px;" alt="소셜(간편) 로그인" /><?php }?></a> -->
<!-- 					<a href="<?php echo get_permalink($page_setting['join_page'])?>" title="회원가입">회원가입</a> -->
					<?php }else{?>
					<span class="user_info">
						<?php if (current_user_can('administrator')) {?>
								<a href="<?php echo admin_url();?>" title="관리자 바로가기"><strong>관리자</strong></a> (<?php echo $current_user->user_nicename?>)
							</span>
							<a href="<?php echo get_permalink($page_setting['join_page'])?>" title="정보수정">정보수정</a>
							<a href="<?php echo wp_logout_url(home_url()); ?>" title="로그아웃">로그아웃</a>
						<?php }else{
							if($current_user->user_nicename){
						?>
								<strong><?php echo $current_user->user_nicename;?></strong>님
							</span>
							<a href="<?php echo get_permalink($page_setting['join_page'])?>" title="정보수정">정보수정</a>
							<a href="<?php echo wp_logout_url(home_url()); ?>" title="로그아웃">로그아웃</a>
						<?php
							}
							else{
								$snsUser=unserialize($_SESSION['snsLoginData']);
						?>
								<strong><?php echo $snsUser['sns_name'];?></strong>님
							</span>
							<a href="<?php echo wp_logout_url(home_url()); ?>" title="로그아웃">로그아웃</a>
						<?php
							}
						}
						?>

					<?php }?>
					<a href="<?php echo $order_url; ?>">주문/배송조회</a>
					<div class="myp">
						<ul class="">
							<li><a href="<?php echo home_url();?>/?bbseMy=mypage" title="마이페이지">마이페이지</a><div class="arrtop">해더</div></li>
							<li><a href="<?php echo home_url();?>/?bbseMy=order-list" title="주문내역">주문/배송조회</a></li>
							<li><a href="<?php echo home_url();?>/?bbseMy=interest" title="관심상품">관심상품</a></li>
						<?php if($_SESSION['snsLogin']!='Y' || !$_SESSION['snsLoginData']){?>
<!-- 							<li><a href="<?php echo home_url();?>/?bbseMy=point" title="적립금">적립금내역</a></li> -->
						<?php }?>
							<li><a href="<?php echo home_url();?>/?bbseMy=coupon" title="쿠폰내역">쿠폰내역</a></li>
							<li><a href="<?php echo home_url();?>/?bbseMy=man2man" title="적립금">나의 1:1 문의</a></li>
						<?php
						$oCnfCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='order'");
						if($oCnfCnt>'0'){
							$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
							$orderInfo=unserialize($confData->config_data);
						}
						if($orderInfo['soldout_notice_use']=='on'){
						?>
							<li><a href="<?php echo home_url();?>/?bbseMy=soldoutNotice" title="품절상품 입고알림">상품 입고알림</a></li>
						<?php }?>
						
						<?php if($screen_code->screen_code == "GU"){?>
							<li><a href="<?php echo home_url();?>/?bbseMy=myInven" title="재고관리">재고관리</a></li>
						<?php }?>
						</ul>
					</div>
					<a href="<?php echo home_url();?>/?bbsePage=cart" title="장바구니">장바구니</a>
				</div>
			</div>
			<div class="head_wrap">
				<div class="inner">
					<div id="gnb">
					<?php 

					wp_nav_menu(
						array(
							'menu'         => 'main',
							'container'    => false,
							'menu_id'      => 'mainMenu',
							'menu_class'   => 'navi_common',
							'depth'        =>  3,
						)
					);

					?>
					</div>
				</div>
			</div>
		</div>

		<?php
		if(!is_user_logged_in() && ($_SESSION['snsLogin']!='Y' || !$_SESSION['snsLoginData'])){
		
		?>
		  
		<?php   
		}else{
        ?>
		<div id="allcategory">
			<button type="button" class="bb_open"><span>전체 카테고리 보기</span>
			<em style="font-size:14px;">상품보기
			<!-- <span>펼치기</span> -->
			</em></button>
			<div class="all-category">
				<div class="cate_bg"></div>
				<div class="bb_inner">
				<?php leftCategoryView('top');?>
				</div>
				<button type="button" class="bb_close"><span>닫기</span></button>
			</div>
		</div><!--//#allcategory -->
		
		<?php 
		
    		 if(wp_is_mobile()){
    			 if(get_option($theme_shortname."_display_use_mobile_icon")=='U' && get_option($theme_shortname."_display_use_mobile_icon_position")=='top'){
    				$mIconFlag=false;
    				for($z=1;$z<9;$z++){
    					if(get_option($theme_shortname."_display_mobile_icon_icon_".$z) && get_option($theme_shortname."_display_mobile_icon_title_".$z)){
    						$mIconFlag=true;
    						break;
    					}
    				}
    
    				if(get_option($theme_shortname."_display_use_mobile_icon_count")>'0' && $mIconFlag==true){
    					$skinname = get_option($theme_shortname."_sub_skin_name")?get_option($theme_shortname."_sub_skin_name"):"basic";
    					get_template_part('skin/'.$skinname.'/mobile_icon'); 
    				}
    			 }
    		 }
		}
		?>

		<?php
		if(!is_user_logged_in() && ($_SESSION['snsLogin']!='Y' || !$_SESSION['snsLoginData'])){
		    
		}else{
		if(get_option($theme_shortname."_layout_left_category")!="Y"){?>
		<div class="category_snav">
			<h2 class="blind">CATEGORIES</h2>
			<?php topCategoryView();?>
		</div> <!--//CATEGORY -->
		<?php 
		  }
		}?>
<!--N: includ header E -->
    <div id="container">
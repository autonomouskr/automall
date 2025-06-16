<?php
global $theme_shortname;
$pointColor = get_option($theme_shortname."_color_main_theme")?get_option($theme_shortname."_color_main_theme"):"#e22a40";
?>
<style type="text/css">
.skipnavi a:focus {background-color: <?php echo $pointColor?>;}

.bb_btn > span.c_custom,
.bb_btn > strong.c_custom,
.bb_btn > em.c_custom {color: <?php echo $pointColor?> !important;}
.bb_btn.cus_fill {border-color:<?php echo $pointColor?>;background-color: <?php echo $pointColor?>;}
.bb_btn.cus_solid {border-color:<?php echo $pointColor?>;}
.bb_btn.cus_solid > span,
.bb_btn.cus_solid > strong,
.bb_btn.cus_solid > em {color: <?php echo $pointColor?>;}

.posted-meta span.entry-author a,
#header #gnb > ul > li > a:hover,
#header #gnb > ul > li > a:focus,
#header #gnb > ul > li > a:active,
#allcategory .all-category .bb_inner ul > li > a,
.main_cont .main_section#mainBanner .carouselTab ul li.active a,
.main_cont .main_section.bb_customer .bb_custom .bb_cs_box .bb_cs_tel,
.location > ul > li > strong,
.srp_search .fl_sort_count strong,
.bb_btn > span.c_point, .bb_btn > strong.c_point, .bb_btn > em.c_point {color:<?php echo $pointColor?> !important;}

#searchArea .ic_cart .count,
#allcategory .bb_close,
#sidebar .sideRoller2 .roll-list .bb-control-nav li button.bb-active span,
.main_cont .main_section#mainBanner .bb-control-nav li button.bb-active span,
.main_cont .main_section.mainRoller2 .basic_list .bb-control-nav li button.bb-active span,
#quick .fav_box .fav_list ul li a .hover > span,
.price_slider_wrap #sliderPrice .ui-widget-header,
.page_cont .lp_listing ul li.active a,
.page_cont .lp_listing ul li.active a strong,
.paging .page .here,
.bb_cmt_star,
.bd_box .bb_section .bb_login_wrap .bb_submit,
.layer-container .layerBox .boxTitle {background-color:<?php echo $pointColor?>;}

#allcategory.active .bb_open:after,
#allcategory .all-category,
#quick .fav_box .fav_list ul li a .hover,
#footer,
.product_detail .detail_area .bb_thumbnail .bb_thumb_control ul li.active a {border-color:<?php echo $pointColor?>;}

.basic_tabs ul li.active {border-top-color:<?php echo $pointColor?>;}

.md_item .md_wrap .tab_nav li.active a {background-color:<?php echo $pointColor?>;border-color:<?php echo $pointColor?>;}

/* 좌측 메뉴 */
#sidebar > .side-nav > ul > li > a {
color:#666;
<?php echo (get_option($theme_shortname."_layout_left_category_bold")=='bold')?"font-weight:700;":"";?>
}
#sidebar > .side-nav.general > ul > li.active > a, #sidebar > .side-nav.general > ul > li:hover > a {background:<?php echo $pointColor?>;}

  #sidebar > .side-nav > ul > li.active > a,
  #sidebar > .side-nav > ul > li:hover > a,
  .payHow-title, .payHow-view  {color:<?php echo $pointColor?>}

/* 좌측 배너 */
#sidebar .sideRoller2 .side-banners .bb-control-nav li button.bb-active span {background-color:<?php echo $pointColor?>;}

/* 상품검색 */
.bb_search_result .bb_searh_bx .bb_search_field {border:5px solid <?php echo $pointColor?>;}
.bb_search_result .bb_searh_bx .bb_search_field .bb_search_submit {background-color:<?php echo $pointColor?>;}

/* 마이페이지 */
.myp_info_box .title_wrap {background-color:<?php echo $pointColor?>;}

/* jQuery UI  */
.ui-widget-header {background:<?php echo $pointColor?>;}
.price_slider_wrap .ui-widget-content {background:#f6f6f6;border:1px solid #C6C6C8;}
.ui-slider {border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-top-right-radius:0px;border-top-left-radius:0px;}
.price_slider_wrap .ui-slider-handle {border-bottom-right-radius:0px;border-bottom-left-radius:0px;border-top-right-radius:0px;border-top-left-radius:0px;}

/* 최근본 상품-삭제*/
#quick .fav_box .fav_list ul li .recent_remove{background-color:<?php echo $pointColor?>;}
#recent_list_count{color:<?php echo $pointColor?>;font-weight:bold;}

/* 마이페이지 */
.article .bb_dot_list .order-status-active {
  color:<?php echo $pointColor?>;
}
.article .bb_dot_list li:before {
  content: '▶ ';
  color:#fff;
}
.article .bb_dot_list li.order-status-active:before {
  content: '▶ ';
  color:<?php echo $pointColor?>;
}

.form-submit input[type="submit"]{color:#fff;background-color:<?php echo $pointColor?>;}
.img-type li .thumb .bg {background-color:<?php echo $pointColor?>;}
.paging-navigation .pagination.loop-pagination .current{border: 1px solid <?php echo $pointColor?>;background-color: <?php echo $pointColor?>;}
.list-type li .more a{color:<?php echo $pointColor?>;}

.entry-meta > span:first-child > a, .entry-meta > span:first-child > em{ color:<?php echo $pointColor?>;}
.entry-meta > span:first-child > a:hover, .entry-meta > span:first-child > a:focus, .entry-meta > span:first-child > a:active {border-bottom:1px solid <?php echo $pointColor?>;}
/*PC전용*/
@media all and (min-width: 1024px) {

  #utill {background-color:<?php echo $pointColor?>;}

  #utill .utill_wrap > .myp ul.active li a:hover,
  #utill .utill_wrap > .myp ul.active li a:focus,
  #utill .utill_wrap > .myp ul.active li a:active,
  ul.navi_common > li.active > a,
  ul.navi_common > li:hover > a,
  ul.navi_common > li:active > a {color:<?php echo $pointColor?>;}

  #utill .utill_wrap > .myp ul.active,
  #utill .utill_wrap > .myp ul.active li .arr,
  ul.navi_common > li.active:after,
  ul.navi_common > li:hover:after,
  ul.navi_common > li:active:after {border-color:<?php echo $pointColor?>;}
  #wrap h1{top:<?php echo (get_option($theme_shortname."_basic_logo_top_margin"))?get_option($theme_shortname."_basic_logo_top_margin"):"50";?>px;}
}

@media only screen and (max-width: 1024px) {
  /*1024 only*/
  #wrap h1 {top: -50px;}
  .layer-container.forPopUp.popupOn {display:none;}
  /*pre-color-set 1023 only*/
  #utill .utill_wrap a:hover,
  #utill .utill_wrap .user_info:hover,
  #utill .utill_wrap a:focus,
  #utill .utill_wrap .user_info:focus,
  #utill .utill_wrap a:active,
  #utill .utill_wrap .user_info:active,
  #utill .utill_wrap a > strong,
  #utill .utill_wrap .user_info > strong {color:<?php echo $pointColor?>;}

  #wrap,
  #allcategory .bb_open em span:after,
  #utill .utill_wrap .myp ul.active .arr {border-color:<?php echo $pointColor?>;}

  #allcategory .bb_open,
  .main_cont .main_section#mainBanner .carouselTab ul li.active a {background-color:<?php echo $pointColor?>;}

}
</style>
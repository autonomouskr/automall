<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once $includeURL[0]."/wp-load.php";

if(!current_user_can('manage_options') || !$_REQUEST['color_main_theme']  || !$_REQUEST['layout_left_category'] || !$_REQUEST['layout_left_price_search'] || !$_REQUEST['layout_left_today_sale'] || !$_REQUEST['layout_left_hot_item'] || !$_REQUEST['layout_left_bank_info'] || !$_REQUEST['layout_right_last_goods']){
	echo "fail";
}
else{
	update_option($theme_shortname."_color_main_theme", $_REQUEST['color_main_theme']);
	update_option($theme_shortname."_layout_left_category", $_REQUEST['layout_left_category']);
	update_option($theme_shortname."_layout_left_category_bold", $_REQUEST['layout_left_category_bold']);
	update_option($theme_shortname."_layout_left_price_search", $_REQUEST['layout_left_price_search']);
	update_option($theme_shortname."_layout_left_today_sale", $_REQUEST['layout_left_today_sale']);
	update_option($theme_shortname."_layout_left_hot_item", $_REQUEST['layout_left_hot_item']);
	update_option($theme_shortname."_layout_left_bank_info", $_REQUEST['layout_left_bank_info']);
	update_option($theme_shortname."_layout_right_last_goods", $_REQUEST['layout_right_last_goods']);
	echo "success";
}
exit;

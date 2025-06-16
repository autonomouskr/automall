<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$mode = $V['mode'];

if($mode == "option"){
    $goodsUniqueCode = $V['goodsUniqueCode'];
    $grows = $wpdb->get_var("select count(1) from bbse_commerce_goods where goods_code = '".$goodsUniqueCode."' and goods_display != 'trash'");
    $orows = $wpdb->get_var("select count(1) from bbse_commerce_goods_option where goods_option_item_unique_code = '".$goodsUniqueCode."' and goods_option_item_display != 'trash'");
    
    if($grows> 0 || $orows > 0){
        echo "exist";
    }
    else{
        echo "ok";
    }
}else{
    $goodsCode = $V['goodsCode'];
    $rows = $wpdb->get_var("select count(1) from bbse_commerce_goods where goods_code = '".$goodsCode."' and goods_display != 'trash'");
    $rows2 = $wpdb->get_var("select count(1) from tbl_inven where goods_code = '".$goodsCode."' and delete_yn != 'Y'");
    if($rows > 0 || $rows2 > 0) {
    	echo "exist";
    }else{
    	echo "ok"; 
    }
}
?>  
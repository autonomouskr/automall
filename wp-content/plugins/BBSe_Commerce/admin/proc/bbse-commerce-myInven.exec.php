<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";


$V = $_POST;

$cnt = 0;
$cnt2 = 0;
$cnt3 = 0;
for($i=0; $i<count($V['chkarr']); $i++){
    
    $timeStamp=current_time('timestamp');
    $timeStamp=date("Y-m-d H:i:s",$timeStamp);
    
    $goods_idx=$V['goodsIdxArr'][$i];
    $storage_code=$V['storageArr'][$i];
    
    $inven = $wpdb->get_results("select * From tbl_inven where goods_idx = '".$goods_idx."' and goods_option_title = '".$V['optionArr'][$i]."' and delete_yn != 'Y'");
    
    if(count($inven) > 0){
        $goodsCode = $inven[0]->goods_code;
    }else{
        $maxNumber = $wpdb->get_var("select max(goods_code) from  `autopole3144`.`tbl_inven`");
        $value = $maxNumber + 1;
        $goodsCode = str_pad($value, 5, "0", STR_PAD_LEFT); 
    }
    
    $orderDetail = $wpdb->get_row("select * From bbse_commerce_order_detail where idx = '".$V['chkarr'][$i]."'");
    
    $optBasic = array();
    
    $add = unserialize($orderDetail->goods_option_add);
    
    $optBasic['goods_option_title'][]=$V['optionArr'][$i];
    $optBasic['goods_option_overprice'][]="0";
    $optBasic['goods_option_count'][]="1";
    
    $goods_option_basic=serialize($optBasic);
    
    #$basic_option_title = $basic['goods_option_title'][0];
    
    #$goods_option_basic=serialize($basic); // Serialize 처리
    $goods_option_add=serialize($add); // Serialize 처리
    
    $check = $wpdb->get_var("select count(1) from tbl_inven where goods_idx = '".$goods_idx."' and delete_yn != 'Y' and goods_option_title = '".$optBasic['goods_option_title'][0]."' and storage_code = '".$V['storageArr'][0]."'");
    if($check > 0){
        echo "<script type='text/javascript'>alert('이미 재고관리 항목으로 등록된 제품입니다.');history.back();</script>";exit;
    }
    
    $sql = "select * from bbse_commerce_goods where idx = '".$goods_idx."' and goods_display != 'trash' ";
    $good = $wpdb->get_row($sql);
    
    if($good){
        
        /* $sql1 = "INSERT INTO autopole3144.tbl_inven
                (goods_code, manager_id, goods_name, current_count, notice_count, reg_date, delete_yn)
                VALUES('".$goodsCode."','".$V['userId']."','".$good->goods_name."', 0, 0, '".$timeStamp."', 'N');";
        $result = $wpdb->query($sql1);
        if($result > 0){
            $cnt++;
        }
        */
        
        /* $sql2 = "INSERT INTO autopole3144.tbl_my_inven
                (goods_code, manager_id, goods_idx, goods_name, current_count, notice_count, reg_date, delete_yn,goods_option_basic, goods_option_add,basic_option_title, storage_code)
                VALUES('".$goodsCode."','".$V['userId']."', '".$goods_idx."','".$good->goods_name."', 0, 0, '".$timeStamp."', 'N', '".$goods_option_basic."', '".$goods_option_add."','".$optBasic['goods_option_title'][0]."','".$storage_code."');";
        $result2 = $wpdb->query($sql2);
        if($result2 > 0){
            $cnt2++;
        }
        */
        $sql3 = "INSERT INTO autopole3144.tbl_inven
                ( goods_idx, goods_code, goods_name, goods_option_title, goods_option_add, goods_option_basic, storage_code, current_count, notice_count, reg_date, manager_id, delete_yn)
        VALUES('".$goods_idx."','".$goodsCode."','".$good->goods_name."', '".$V['optionArr'][$i]."', '".$goods_option_add."', '".$goods_option_basic."', '".$storage_code."', 0,0,'".$timeStamp."','".$V['userId']."','N')";
        
        $result3 = $wpdb->query($sql3);
        if($result3 > 0){
            $cnt3++;
        }
    }
    
}
//echo "success|||".$cnt2."|||";
echo "success|||".$cnt3."|||";
exit;

?>  

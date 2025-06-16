

<?php

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");
header( "Content-type: application/vnd.ms-excel; charset=ks_c_5601-1987" );

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$s_checkList=$_REQUEST['s_checkList'];

$result = $wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE idx IN ($s_checkList)");

$name = $wpdb->get_var("select DISTINCT name from bbse_commerce_membership bcm where user_id = '".$result[0]->user_id."'");

header( "Content-Disposition: attachment; filename= ".$result[0]->order_no."_".date("Ymd").".xls" );
header( "Content-Description: PHP4 Generated Data" );

$result_detail = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no IN ('".$result[0]->order_no."')");

$userClass = $wpdb->get_results("SELECT * FROM bbse_commerce_membership WHERE user_id = '".$result[0]->user_id."'"); 

?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.form.js'></script>
</head>
<body>
​​​​<table style="margin-top:15px;" class="table" width="300px"  cellpadding="0" cellspacing="0" border="1">
    ​​​​​​​​<tr>
    	<th colspan="8" style="font-size:50px; font-style: normal; font-weight: 900; text-decoration: underline;" >거래명세서<img style="float: right;" src="http://autonomouskr.shop/wp-content/uploads/2024/07/header-logo.png" /></th>
    ​​​​​​​​​​​​</tr>
	<tr>
    	<th colspan="4" rowspan="5"><span style="float:right; font-size: 30px;"><?php echo $name;?>&nbsp;&nbsp;귀중</span></th>
    	<th colspan="3">상호: (주)오토기기</th>
    	<th colspan="1" rowspan="5"><img style="float: right; padding-left:5px; "  width="65" src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/seal.png' /></th>
    ​​​​​​​​​​​​</tr>
    <tr>
    	<th colspan="3">代表理事  :  盧     成     得</th>
    </tr>
	<tr>
    	<th colspan="3">주    소 :  서울시 광진구 천호대로 690 5층</th>
    </tr>
    <tr>
    	<th colspan="3">전    화 :  02-2299-3406 </th>
    </tr>
    <tr>
    	<th colspan="3">팩    스  : 02-2291-4136 </th>
    </tr>
    <tr>
    	<th colspan="4"><span style="float:left; font-size: 20px;">거래일자</span></th>
    	<th  colspan="4"><?php echo date('Y-m-d H:m:s',$result[0]->order_date);?></th>
    </tr>
    <tr>
    	<th colspan="4"><span style="float:left; font-size: 20px;">거래번호</span></th>
    	<th colspan="4"><?php echo $result_detail[0]->order_no;?></th>
    </tr>
	<tr>
    	<th colspan="4"><span style="float:left; font-size: 20px;">거래제품</span></th>
    	<th colspan="4">3M</th>
    </tr>
    <tr>
    	<th colspan="4"><span style="float:left; font-size: 20px;">합계금액</span></th>
    	<th colspan="4"><?php echo number_format($result[0]->goods_total);?>원</th>
    </tr>
        <tr>
    	<th colspan="4"><span style="float:left; font-size: 20px;">할인금액</span></th>
    	<th colspan="4"><?php
        	$user_discount = $result[0]->user_discount;
        	$coupon_discount = $result[0]->coupon_discount;
        	$total_discount = $user_discount + $coupon_discount;
        	echo number_format($total_discount);?>원</th>
    	</tr>
        <tr>
    	<th colspan="4"><span style="float:left; font-size: 20px;">총합계금액</span></th>
    	<th colspan="4"><?php echo number_format($result[0]->cost_total);?>원</th>
    </tr>
    <tr>
    	<th colspan="1">NO.</th>
    	<th colspan="1">품명</th>
    	<th colspan="1">품번</th>
    	<th colspan="1">QTY</th>
    	<th colspan="1">U/PRICE</th>
    	<th colspan="1">VAT</th>
    	<th colspan="1">AMOUNT</th>
    	<th colspan="1">REMARK</th>
    </tr>
    <?php 
        if(count($result_detail) > '0'){
            
            foreach($result_detail as $i=>$gdata){
                
                $qty = 0;
                $price = 0;
                $amount = 0;
                $menberPrice = 0;
                $vvat = 0;
                
                $basicOpt=unserialize($gdata->goods_option_basic);
                $qty = $basicOpt['goods_option_count'][0];
                
                $goods_info2 = $wpdb->get_row("SELECT * FROM bbse_commerce_goods bcg  WHERE idx IN ($gdata->goods_idx)");
                $goodsPrice =unserialize($goods_info2->goods_member_price);
                
                for($j=0; $j<sizeof($goodsPrice); $j++){
                    if($goodsPrice['goods_member_level'][$j] == $userClass[0]->user_class){
                        $mPrice = $goodsPrice['goods_member_price'][$j];
                        $vat = $goodsPrice['goods_vat'][$j];
                        if($mPrice > '0'){
                            $menberPrice = $mPrice;
                            $vvat = $vat;
                            $price = $mPrice+$vat;
                        }
                    }
                }
                
                $amount = $price * $qty;
    ?>
                <tr>
                	<td colspan="1"><?php echo $i+1;?></td>
                	<td colspan="1"><?php echo $goods_info2->goods_name;?></td>
                	<td colspan="1"><?php echo $goods_info2->goods_unique_code;?></td>
                	<td colspan="1"><?php echo $qty;?>EA</td>
                	<td colspan="1"><?php echo number_format($mPrice);?>원</td>
                	<td colspan="1"><?php echo number_format($vvat);?>원</td>
                	<td colspan="1"><?php echo number_format($amount);?>원</td>
                	<td colspan="1">-</td>
                </tr>
    <?php
            }
        }else{
        
        }
    ?>
​​​​</table>

</body>
</html>

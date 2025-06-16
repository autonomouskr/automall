<?php
$query = $_SERVER['QUERY_STRING'];
$vars = array();
foreach(explode('&', $query) as $pair) {
	list($key, $value) = explode('=', $pair);
	$key = urldecode($key);
	$value = urldecode($value);
	$vars[$key][] = $value;
}

$itemIds = $vars['ITEM_ID'];
if (count($itemIds) < 1) {
	echo "ITEM_ID 는 필수입니다.";
	exit;
}

header('Content-Type: application/xml;charset=utf-8');
echo ('<?xml version="1.0" encoding="utf-8"?>');
?>
<response>
<?php
for($i=0;$i<sizeof($itemIds);$i++){
	$goods=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE goods_code='".$itemIds[$i]."'");
	if(!$goods->goods_code) continue;

	$id = $goods->goods_code;
	$name = $goods->goods_name;
	$description = $goods->goods_description;
	$price = $goods->goods_price;

	if($goods->goods_count_flag=='option_count'){
		$quantity = $wpdb->get_var("SELECT sum(goods_option_item_count) FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."'");
	}
	elseif($goods->goods_count_flag=='unlimit') $quantity = 1000;
	else $quantity = $goods->goods_count;

	$goodsUrl=home_url()."/?bbseGoods=".$goods->idx;

	$imageList=explode(",",$goods->goods_add_img);
	$goodsImage=$goodsThumb="";
	if($goods->goods_basic_img){
		$imageImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage4");
		$thumbImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage2");
		$goodsImage=$imageImg['0'];
		$goodsThumb=$thumbImg['0'];
	}
	else{
		if(sizeof($imageList)>'0'){
			for($zk=0;$zk<sizeof($imageList);$zk++){
				unset($imageImg);
				$imageImg = wp_get_attachment_image_src($imageList[$zk],"goodsimage4");
				if($imageImg['0']){
					$thumbImg = wp_get_attachment_image_src($imageList[$zk],"goodsimage2");
					$goodsImage=$imageImg['0'];
					$goodsThumb=$thumbImg['0'];
					break;
				}
			}
		}
	}

	if(!$goodsImage){
		$goodsImage=$goodsThumb=esc_url( get_template_directory_uri() )."/images/image_not_exist.png";
	}

	$goodsCate=bbse_commerce_nshop_category($goods->goods_cat_list);
?>
<item id="<?php echo $id;?>">
	<name><![CDATA[<?php echo $name;?>]]></name>
	<url><?php echo $goodsUrl;?></url>
	<description><![CDATA[<?php echo $description;?>]]></description>
	<image><?php echo urldecode($goodsImage);?></image>
	<thumb><?php echo urldecode($goodsThumb);?></thumb>
	<price><?php echo $price;?></price>
	<quantity><?php echo $quantity;?></quantity>
	<category>
	<?php if($goodsCate['idx_1']){?>
		<first id="<?php echo $goodsCate['idx_1'];?>"><?php echo str_replace("&","",$goodsCate['depth_1']);?></first>
	<?php 
		}
		if($goodsCate['idx_2']){?>
		<second id="<?php echo $goodsCate['idx_2'];?>"><?php echo str_replace("&","",$goodsCate['depth_2']);?></second>
	<?php 
		}
		if($goodsCate['idx_3']){?>
		<third id="<?php echo $goodsCate['idx_3'];?>"><?php echo str_replace("&","",$goodsCate['depth_3']);?></third>
	<?php }?>
	</category>
	<?php
	if($goods->goods_option_basic){
		$goodsOpt=unserialize($goods->goods_option_basic);
		for($t=1;$t<3;$t++){
			if($goodsOpt['goods_option_1_count']>'0' || $goodsOpt['goods_option_2_count']>'0'){
				 if($goodsOpt['goods_option_'.$t.'_count']>'0'){
	?>
					<options>
						<option name="<?php echo $goodsOpt['goods_option_'.$t.'_title'];?>">
						<?php for($p=0;$p<$goodsOpt['goods_option_'.$t.'_count'];$p++){?>
							<select><![CDATA[<?php echo $goodsOpt['goods_option_'.$t.'_item'][$p];?>]]></select>
						<?php }?>
						</option>
					</options>
	<?php
				 }
			}
		}
	}
	?>
</item>
<?php
}
echo "</response>";
?>
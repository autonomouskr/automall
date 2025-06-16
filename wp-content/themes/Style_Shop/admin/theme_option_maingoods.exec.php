<?php
/*
[�׸� ���� �� ���ǻ���]
1. ����������(Wordpress)�� ������Ʈ ����� ���� �׸�/�÷������� ���� �� �� �缳ġ �ϴ� ����Դϴ�.
   ������Ʈ �� ��� ���� ������ �ʱ�ȭ �ǹǷ� �׸��� �����Ͻô� ���, ���ϵ��׸�(Child Theme) ����� �̿��� �ֽñ� �ٶ��ϴ�.
2. ���ϵ��׸�(Child Theme)�� �̿��� ���� ��� : https://codex.wordpress.org/ko:Child_Themes
*/

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb, $theme_shortname;

$dTypeArray=Array("recommend","best","md","new","today","hot");

$V=$_POST;

if($V[$theme_shortname.'_maingoods_preOpen']!="") {
	update_option($theme_shortname."_maingoods_preOpen", $V[$theme_shortname.'_maingoods_preOpen']);
}

if($V['dType']=='md' && $V['dRemove']=='empty'){
	$sql="UPDATE bbse_commerce_display SET display_goods='' WHERE display_type='".$V['dType']."'";
	$wpdb->query($sql);

	echo "success";
	exit;
}
elseif(in_array($V['dType'],$dTypeArray) || ($V['dType']=='md' && $V['display_md_cnt']>'0')) {
	if($V['dType']=='md'){
		$tmp_md_list['display_md_cnt']=$V['display_md_cnt'];

		for($z=1;$z<=$V['display_md_cnt'];$z++){
			$tmp_md_list['display_md_title_'.$z]=$V['display_md_title_'.$z];
			$tmp_md_list['goods_md_list_'.$z]=$V['goods_md_list_'.$z];
		}

		${'goods_'.$V['dType'].'_list'}=serialize($tmp_md_list);
	}
	else{
	    if(sizeof($V['goods_'.$V['dType'].'_list'])>0){
	        //${'goods_'.$V['dType'].'_list'}=serialize($V['goods_'.$V['dType'].'_list']);
	        $goods_list_array['goods_uc_list']=$V['goods_'.$V['dType'].'_uc_list']; // 상품별 등록된 회원등급
	        $goods_list_array['goods_type_list']=$V['goods_'.$V['dType'].'_list']; // 상품별 등록된 회원등급
	    }
	    else {
	        $V['goods_'.$V['dType'].'_uc_list'] = "";
	        $V['goods_'.$V['dType'].'_list'] = "";
	    }
	    $goods_list=serialize($goods_list_array); // Serialize 처리
	}

	$cnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_display WHERE display_type='".$V['dType']."'"); 
	
	$result=false;

	if($cnt<='0'){
		//$sql="INSERT INTO bbse_commerce_display (display_type,display_goods) VALUES ('".$V['dType']."','".${'goods_'.$V['dType'].'_list'}."')";
	    $sql="INSERT INTO bbse_commerce_display (display_type,display_goods) VALUES ('".$V['dType']."','".$goods_list."')";
		$wpdb->query($sql);
		$idx = $wpdb->insert_id;
		if($idx>'0') $result=true;
	}
	else{
		//$sql="UPDATE bbse_commerce_display SET display_goods='".${'goods_'.$V['dType'].'_list'}."' WHERE display_type='".$V['dType']."'";
	    $sql="UPDATE bbse_commerce_display SET display_goods='".$goods_list."' WHERE display_type='".$V['dType']."'";
		$wpdb->query($sql);
		$result=true;
	}

	if($result){
		echo "success";
		exit;
	}
	else{
		echo "dbError";
		exit;
	}
}
else{
	echo "nonData";
	exit;
}
?>
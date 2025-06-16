<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb;

$V = $_POST;


if($V['tMode']=='changeStatus'){
	if(!$V['tIdx'] || !$V['oStatus']){
		echo "fail";
		exit;
	}

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_order WHERE idx='".$V['tIdx']."'");

	if($cnt<='0'){
		echo "notExistOrder";
		exit;
	}

	$oldStatus=$wpdb->get_var("SELECT order_status FROM bbse_commerce_order WHERE idx='".$V['tIdx']."'");

	$upQuery=bbse_commerce_get_update_query($V);
	$wpdb->query("UPDATE bbse_commerce_order SET ".$upQuery." WHERE idx='".$V['tIdx']."'");

	$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$V['tIdx']."'");

	if($oData->order_status=='PE' && $oData->order_stock_check=='n'){
		bbse_commerce_goods_stock_minus($oData->order_no); // 재고 개수 차감
	}
	elseif(($oData->order_status=='PR' || $oData->order_status=='CE' || $oData->order_status=='RE') && $oData->order_stock_check=='y'){
		bbse_commerce_goods_stock_plus($oData->order_no); // 재고 개수 복구
	}

	if($oData->order_status=='CE' || $oData->order_status=='RE' || $oData->order_status=='OE'){
		bbse_commerce_order_earn_check($oData->order_no); // 적립금 환불
	}
	
	if($oData->order_status=='OE'){
		bbse_commerce_auto_upgrade_user($oData->order_no); //자동등업처리
	}

	// 메일 문자 발송 (시작)
	if($oldStatus=='PR' && $oData->order_status=='PE'){ // 결제완료
		 $smsResult=bbse_commerce_sms_send('order-input', $oData->order_no);
		 $msResult=bbse_commerce_mail_send('order-input',$oData->order_no,'');
	}
	elseif(($oldStatus=='PE' || $oldStatus=='DR') && $oData->order_status=='DI'){ // 배송중
		 $smsResult=bbse_commerce_sms_send('order-shipment', $oData->order_no);
		 $msResult=bbse_commerce_mail_send('order-shipment',$oData->order_no,'');
	}
	elseif($oldStatus!='CE' && $oData->order_status=='CE'){ // 취소완료
		 $msResult=bbse_commerce_mail_send('order-cancel',$oData->order_no,'');
	}
	elseif($oldStatus!='RE' && $oData->order_status=='RE'){ // 반품완료
		 $msResult=bbse_commerce_mail_send('order-refund',$oData->order_no,'');
	}
	// 메일 문자 발송 (끝)

	if($V['oStatus']=='restore') $idx=$V['tIdx'];
	else $idx=$wpdb->get_var("SELECT idx FROM bbse_commerce_order WHERE idx='".$V['tIdx']."' AND order_status='".$V['oStatus']."'");

	if($idx>'0'){
		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}


elseif($V['tMode']=='changeReceive'){ 
	if(!$V['tIdx'] || !$V['receiveName'] || !$V['receiveZip'] || !$V['receiveAddr1']){
		echo "fail";
		exit;
	}

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_order WHERE idx='".$V['tIdx']."'");

	if($cnt<='0'){
		echo "notExistOrder";
		exit;
	}
	
	$changeTime=current_time('timestamp');
	$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$V['tIdx']."'");

	$upQuery="receive_name='".$V['receiveName']."',receive_phone='".$V['receivePhone']."',receive_hp='".$V['receiveHp']."',receive_zip='".$V['receiveZip']."',receive_addr1='".$V['receiveAddr1']."',receive_addr2='".$V['receiveAddr2']."'";
	$wpdb->query("UPDATE bbse_commerce_order SET ".$upQuery." WHERE idx='".$V['tIdx']."'");


	$oldReceiveAddr1=explode(" ",$oData->receive_addr1);
	$oAddr=trim($oldReceiveAddr1['0'])." ".trim($oldReceiveAddr1['1']);

	$newReceiveAddr1=explode(" ",$V['receiveAddr1']);
	$nAddr=trim($newReceiveAddr1['0'])." ".trim($newReceiveAddr1['1']);
	if($oAddr!=$nAddr){
		if($nAddr==$oData->delivery_add_addr){
			$deliveryAddChange="";
			$deliveryAddChangeDate="";
			$changeConfig="";
		}
		else{
			$deliveryAddChange=bbse_commerce_get_delivery_add($V['receiveAddr1']);
			$deliveryAddChangeDate=current_time('timestamp');
			$changeConfig=bbse_commerce_get_delivery_info();
		}
		$wpdb->query("UPDATE bbse_commerce_order SET change_config='".$changeConfig."',delivery_add_change='".$deliveryAddChange."',delivery_add_change_date='".$deliveryAddChangeDate."' WHERE idx='".$V['tIdx']."'");
	}

	echo "success";
	exit;
}
elseif($V['tMode']=='changeMemo'){ 
	if(!$V['tIdx']){
		echo "fail";
		exit;
	}

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_order WHERE idx='".$V['tIdx']."'");

	if($cnt<='0'){
		echo "notExistOrder";
		exit;
	}

	$oMemo=addslashes($V['oMemo']);
	$wpdb->query("UPDATE bbse_commerce_order SET admin_comment='".$oMemo."' WHERE idx='".$V['tIdx']."'");

	echo "success";
	exit;
}
elseif($V['tMode']=='chTrash' || $V['tStatus']=='empty-trash'){ // 일괄작업 && 휴지통
	if(!$V['tStatus'] || ($V['tStatus']!='trash' && $V['tStatus']!='restore' && $V['tStatus']!='empty-trash') || !$V['tData']){
		echo "fail";
		exit;
	}
	
	if($V['tData']=='empty'){
		$sql="`order_status`='TR'";
	}
	else{
		$checkIdx=explode(",",$V['tData']);
		$sql="";
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>0){
				if($sql) $sql .=" OR ";
				$sql .="idx='".$checkIdx[$i]."'";
			}
		}
	}

	if(!$sql){
		echo "fail";
		exit;
	}

	if($V['tStatus']=='empty-trash'){
		$sResult = $wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE ".$sql);
		$delSql_1=$delSql_2="";

		foreach($sResult as $i=>$sData) {
			if($delSql_1) $delSql_1 .=" OR ";
			$delSql_1 .="idx='".$sData->idx."'";

			if($delSql_2) $delSql_2 .=" OR ";
			$delSql_2 .="order_no='".$sData->order_no."'";
		}

		if($delSql_1){
			$res = $wpdb->query("DELETE FROM `bbse_commerce_order` WHERE ".$delSql_1);
		}

		if($delSql_2){
			$res2 = $wpdb->query("DELETE FROM `bbse_commerce_order_detail` WHERE ".$delSql_2);
		}
	}
	elseif($V['tStatus']=='trash'){
		$res = $wpdb->query("UPDATE `bbse_commerce_order` SET `order_status_pre`=`order_status`, `order_status`='TR' WHERE ".$sql);
	}
	elseif($V['tStatus']=='restore'){
		$res = $wpdb->query("UPDATE `bbse_commerce_order` SET `order_status`=`order_status_pre`, `order_status_pre`='' WHERE ".$sql);
	}

	echo "success";
	exit;
}elseif($V['tMode']=='trash'){ // 일괄작업 && 휴지통
    if(!$V['tStatus'] || ($V['tStatus']!='trash' && $V['tStatus']!='restore' && $V['tStatus']!='empty-trash') || !$V['tData']){
        echo "fail";
        exit;
    }
    
    if($V['tData']=='empty'){
        $sql="`order_status`='TR'";
    }
    else{
        $checkIdx=explode(",",$V['tData']);
        $sql="";
        for($i=0;$i<sizeof($checkIdx);$i++){
            if($checkIdx[$i]>0){
                if($sql) $sql .=" OR ";
                $sql .="idx='".$checkIdx[$i]."'";
            }
        }
    }
    
    if(!$sql){
        echo "fail";
        exit;
    }
    
    if($V['tStatus']=='empty-trash'){
        $sResult = $wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE ".$sql);
        $delSql_1=$delSql_2="";
        
        foreach($sResult as $i=>$sData) {
            if($delSql_1) $delSql_1 .=" OR ";
            $delSql_1 .="idx='".$sData->idx."'";
            
            if($delSql_2) $delSql_2 .=" OR ";
            $delSql_2 .="order_no='".$sData->order_no."'";
        }
        
        if($delSql_1){
            $res = $wpdb->query("DELETE FROM `bbse_commerce_order` WHERE ".$delSql_1);
        }
        
        if($delSql_2){
            $res2 = $wpdb->query("DELETE FROM `bbse_commerce_order_detail` WHERE ".$delSql_2);
        }
    }
    elseif($V['tStatus']=='trash'){
        $res = $wpdb->query("UPDATE `bbse_commerce_order` SET `order_status_pre`=`order_status`, `order_status`='TR' WHERE ".$sql);
    }
    elseif($V['tStatus']=='restore'){
        $res = $wpdb->query("UPDATE `bbse_commerce_order` SET `order_status`=`order_status_pre`, `order_status_pre`='' WHERE ".$sql);
    }
    
    echo "success";
    exit;
}else if($V['chTrash']!='chTrash' && $V['tMode']!='changeMemo' &&  $V['tMode']!='changeReceive' && $V['tMode']!='changeStatus' && $V['tMode']!='AR'){
    
    $checkIdx=explode(",",$V['tData']);
    $sql="";
    for($i=0;$i<sizeof($checkIdx);$i++){
        if($checkIdx[$i]>0){
            if($sql) $sql .=" OR ";
            $sql .="idx='".$checkIdx[$i]."'";
        }
    }
    
    if($V[tStatus] == 'EN' || $V[tStatus] == 'PW'){
        $res = $wpdb->query("UPDATE bbse_commerce_order SET pay_status='".$V['tStatus']."' WHERE ".$sql);
    }else{
        $res = $wpdb->query("UPDATE bbse_commerce_order SET order_status='".$V['tStatus']."', order_status_pre='' WHERE ".$sql);
    }
    echo "success";
    exit;
}else if($V['tMode']=='AR'){
    
    $idxs=$V['idxs'];
    $ars=$V['ars'];
    foreach($idxs as $i=>$idx) {
        $sql = "UPDATE `bbse_commerce_order` SET `accounts_receivable`= '".$ars[$i]."' WHERE idx = '".$idx."'";
        $ar = $wpdb->query("UPDATE `bbse_commerce_order` SET `accounts_receivable`= '".$ars[$i]."' WHERE idx = '".$idx."'");
        //$res = $wpdb->query("UPDATE bbse_commerce_order SET pay_status='AR' WHERE user_id = '".$user_id."'");
    }
    echo "success";
    exit;
    
}else{
	echo "nonData";
	exit;
}
?>
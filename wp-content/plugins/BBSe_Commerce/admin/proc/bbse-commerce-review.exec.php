<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb;

$V = $_POST;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

if($V['tMode']=='removeReview'){ // 문의글 삭제
	if(!$V['tIdx']){
		echo "fail";
		exit;
	}

	$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx='".$V['tIdx']."'");

	if($cnt2<='0'){
		echo "notExistReview";
		exit;
	}

	$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx='".$V['tIdx']."'");

	if($rData->r_attach_new){
		$delFile_path=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$rData->r_attach_new;
		@unlink($delFile_path);
	}

	$wpdb->query("DELETE FROM bbse_commerce_review WHERE idx='".$V['tIdx']."'");

	echo "success";
	exit;
}
elseif($V['tMode']=='removeBest'){
	if(!$V['tIdx']){
		echo "fail";
		exit;
	}

	$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx='".$V['tIdx']."' AND r_best='Y'");

	if($cnt2<='0'){
		echo "notExistReview";
		exit;
	}

	$res = $wpdb->query("UPDATE bbse_commerce_review SET r_best='N' WHERE idx='".$V['tIdx']."'");

	if($res){
		echo "success";
		exit;
	}
	else{
		echo "dbError";
		exit;
	}
}
elseif($V['tMode']=='bulkAction'){ // 문의글 삭제
	if(!$V['tData']){
		echo "fail";
		exit;
	}

	if($V['tStatus']=='approve'){
		$earn_point='0';
		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='earn'");
		$earnData=unserialize($confData->config_data);
		if($earnData['earn_review_use']=='on') $earn_point=$earnData['earn_review_point'];
		else{
			echo "notUseEarn";
			exit;
		}

		if($earn_point<='0'){
			echo "zeroEarn";
			exit;
		}

		$earn_mode='IN';
		$earn_type='review';
		$reg_date=current_time('timestamp');

		$uCnt='0';
		$checkIdx=explode(",",$V['tData']);
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>'0'){
				$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx='".$checkIdx[$i]."' AND r_earn_paid='N'");
				if($cnt2>'0'){
					$rData = $wpdb->get_row("SELECT * FROM bbse_commerce_review WHERE idx='".$checkIdx[$i]."'");
					if($rData->user_id){
						$mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$rData->user_id."'");

						$old_point=$mData->mileage;
						$new_point=$old_point+$earn_point;
						$user_id=$rData->user_id;
						$user_name=$rData->user_name;
						$etc_idx=$rData->idx;

						$res = $wpdb->query("UPDATE bbse_commerce_review SET r_earn_paid='P',r_earn_point='".$earn_point."' WHERE idx='".$checkIdx[$i]."'");
						$res2 = $wpdb->query("INSERT INTO bbse_commerce_earn_log (earn_mode,earn_type,earn_point,old_point,user_id,user_name,etc_idx,reg_date) VALUE('".$earn_mode."','".$earn_type."','".$earn_point."','".$old_point."','".$user_id."','".$user_name."','".$etc_idx."','".$reg_date."')");
						$res3 = $wpdb->query("UPDATE bbse_commerce_membership SET mileage='".$new_point."' WHERE user_id='".$user_id."'");
					}
					else{
						$res = $wpdb->query("UPDATE bbse_commerce_review SET r_earn_paid='P' WHERE idx='".$checkIdx[$i]."'");
					}

					$uCnt++;
				}
			}
		}

		if($uCnt>'0'){
			echo "success";
			exit;
		}
		else{
			echo "zeroApprove";
			exit;
		}
	}
	elseif($V['tStatus']=='remove'){
		$checkIdx=explode(",",$V['tData']);
		$dCnt='0';
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>'0'){
				$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx='".$checkIdx[$i]."'");
				if($cnt2>'0'){
					$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx='".$checkIdx[$i]."'");

					if($rData->r_attach_new){
						$delFile_path=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$rData->r_attach_new;
						@unlink($delFile_path);
					}

					$res = $wpdb->query("DELETE FROM bbse_commerce_review WHERE idx='".$checkIdx[$i]."'");
					if($res) $dCnt++;
				}
			}
		}

		if($dCnt>'0'){
			echo "success";
			exit;
		}
		else{
			echo "dbError";
			exit;
		}
	}
	elseif($V['tStatus']=='best'){
		$checkIdx=explode(",",$V['tData']);
		$sql="";
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>0){
				if($sql) $sql .=" OR ";
				$sql .="idx='".$checkIdx[$i]."'";
			}
		}

		$res = $wpdb->query("UPDATE bbse_commerce_review SET r_best='Y' WHERE ".$sql);

		if($res){
			echo "success";
			exit;
		}
		else{
			echo "zeroBest";
			exit;
		}
	}
	elseif($V['tStatus']=='removeBest'){
		$checkIdx=explode(",",$V['tData']);
		$sql="";
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>0){
				if($sql) $sql .=" OR ";
				$sql .="idx='".$checkIdx[$i]."'";
			}
		}

		$res = $wpdb->query("UPDATE bbse_commerce_review SET r_best='N' WHERE ".$sql);

		if($res){
			echo "success";
			exit;
		}
		else{
			echo "zeroBest";
			exit;
		}
	}
}
else{
	echo "nonData";
	exit;
}
?>
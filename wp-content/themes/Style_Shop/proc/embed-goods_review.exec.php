<?php
@session_start();
@header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$curUserPermision = bbse_get_current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

$currUserID=$current_user->user_login;
$Loginflag='member';
$currSnsIdx="";

if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsLoginData=unserialize($_SESSION['snsLoginData']);

		$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
		if($snsData->idx){
			$Loginflag='social';
			$currUserID=$snsLoginData['sns_id'];
			$currSnsIdx=$snsData->idx;
		}
	}
}

$user_id=($Loginflag=='member')?$currUserID:"";
$sns_id=($Loginflag=='social')?$currUserID:"";
$sns_idx=$currSnsIdx;

if($V['rMode']=="insert") {
	if($curUserPermision == "administrator" || !$currUserID){
		echo "PermisionError";
		exit;
	}

	if(!$V['r_subject'] || !$V['r_contents'] || !$V['rGoodsIdx'] || !$V['rGoodsName']){
		echo "DataError";
		exit;
	}

	$user_name=$V['rUserName'];
	$goods_idx=$V['rGoodsIdx'];
	$goods_name=$V['rGoodsName'];
	$r_value=$V['r_value'];

	$r_subject=addslashes(htmlspecialchars($V['r_subject']));
	$r_contents=addslashes(htmlspecialchars($V['r_contents']));
	$r_attach_org="";
	$r_attach_new="";
	$r_earn_paid="N";
	$r_earn_point="";


	$write_date=current_time('timestamp');

	$review_per_page=$V['review_per_page'];
	$page_block=$V['page_block'];
	$function_name=$V['function_name'];

	$inQuery="INSERT INTO bbse_commerce_review (
						user_id,
						sns_id,
						sns_idx,
						user_name,
						goods_idx,
						goods_name,
						r_value,
						r_subject,
						r_contents,
						r_attach_org,
						r_attach_new,
						r_earn_paid,
						r_earn_point,
						write_date
					) 
					VALUES (
						'".$user_id."',
						'".$sns_id."',
						'".$sns_idx."',
						'".$user_name."',
						'".$goods_idx."',
						'".$goods_name."',
						'".$r_value."',
						'".$r_subject."',
						'".$r_contents."',
						'".$r_attach_org."',
						'".$r_attach_new."',
						'".$r_earn_paid."',
						'".$r_earn_point."',
						'".$write_date."'
					 )";
	$wpdb->query($inQuery);
	$idx = $wpdb->insert_id;

	if($idx>'0'){
		if($_FILES['fileUpload']['tmp_name']){
			if(!is_uploaded_file($_FILES['fileUpload']['tmp_name'])){
				echo "errorFileName";
				exit;
			}

			$checkArr=Array("gif","GIF","jpg","JPG","png","PNG");
			$file_arr = explode(".", $_FILES['fileUpload']['name']);
			$file_type = strtolower($file_arr[count($file_arr)-1]);
			if(!in_array($file_type,$checkArr)) {
				echo "errorFileExtend";
				exit;
			}

			$r_attach_org=$_FILES['fileUpload']['name'];

			$r_attach_new="review_".$idx."_".$write_date.".".$file_type;

			if($r_attach_org && $r_attach_new){
				if(!@move_uploaded_file($_FILES['fileUpload']['tmp_name'],BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_attach_new)){ 
					echo "errorFileUpload";
					exit;
				}

				$wpdb->query("UPDATE bbse_commerce_review SET r_attach_org='".$r_attach_org."',r_attach_new='".$r_attach_new."' WHERE idx='".$idx."'");
			}
		}

		bbse_insert_social_login();// 소셜로그인 개인정보 저장
		$rtnStr=bbse_get_goods_review_list($function_name, $goods_idx, 1, $review_per_page, $page_block);

		echo "success|||".$rtnStr;
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
else if($V['rMode']=="modify") {
	if($curUserPermision == "administrator" || !$currUserID){
		echo "PermisionError";
		exit;
	}

	if(!$V['rIdx'] || !$V['r_subject'] || !$V['r_contents'] || !$V['rGoodsIdx'] || !$V['rGoodsName']){
		echo "DataError";
		exit;
	}

	$idx=$V['rIdx'];
	$goods_idx=$V['rGoodsIdx'];
	$r_value=$V['r_value'];

	$r_subject=addslashes(htmlspecialchars($V['r_subject']));
	$r_contents=addslashes(htmlspecialchars($V['r_contents']));

	$paged=$V['paged'];
	$review_per_page=$V['review_per_page'];
	$page_block=$V['page_block'];
	$function_name=$V['function_name'];
	$modify_date=current_time('timestamp');

	if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
		$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND r_earn_paid='N' AND r_earn_point<='0'"); // 총 Review 수
	}
	else{
		$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."' AND r_earn_paid='N' AND r_earn_point<='0'"); // 총 Review 수
	}

	if($reviewCnt>'0'){
		$upQuery="";

		if($_FILES['fileUpload']['tmp_name']){
			if(!is_uploaded_file($_FILES['fileUpload']['tmp_name'])){
				echo "errorFileName";
				exit;
			}

			$checkArr=Array("gif","GIF","jpg","JPG","png","PNG");
			$file_arr = explode(".", $_FILES['fileUpload']['name']);
			$file_type = strtolower($file_arr[count($file_arr)-1]);
			if(!in_array($file_type,$checkArr)) {
				echo "errorFileExtend";
				exit;
			}

			$r_attach_org=$_FILES['fileUpload']['name'];

			$r_attach_new="review_".$idx."_".$modify_date.".".$file_type;

			if($r_attach_org && $r_attach_new){
				if(!@move_uploaded_file($_FILES['fileUpload']['tmp_name'],BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$r_attach_new)){ 
					echo "errorFileUpload";
					exit;
				}

				if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
					$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND r_earn_paid='N' AND r_earn_point<='0'");
				}
				else{
					$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."' AND r_earn_paid='N' AND r_earn_point<='0'");
				}

				if($rData->r_attach_new){
					$delFile_path=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$rData->r_attach_new;
					@unlink($delFile_path);
				}

				$upQuery=",r_attach_org='".$r_attach_org."',r_attach_new='".$r_attach_new."'";
			}
		}
		else{
			if($V['attach_delete']=='delete'){
				if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
					$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND r_earn_paid='N' AND r_earn_point<='0'");
				}
				else{
					$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."' AND r_earn_paid='N' AND r_earn_point<='0'");
				}

				if($rData->r_attach_new){
					$delFile_path=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$rData->r_attach_new;
					@unlink($delFile_path);
				}

				$upQuery=",r_attach_org='',r_attach_new=''";
			}
		}

		if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
			$wpdb->query("UPDATE bbse_commerce_review SET r_value='".$r_value."', r_subject='".$r_subject."', r_contents='".$r_contents."'".$upQuery." WHERE idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND r_earn_paid='N' AND r_earn_point<='0'");
		}
		else{
			$wpdb->query("UPDATE bbse_commerce_review SET r_value='".$r_value."', r_subject='".$r_subject."', r_contents='".$r_contents."'".$upQuery." WHERE idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."' AND r_earn_paid='N' AND r_earn_point<='0'");
		}

		$rtnStr=bbse_get_goods_review_list($function_name, $goods_idx, $paged, $review_per_page, $page_block);

		echo "success|||".$rtnStr;
		exit;
	}
	else{
		echo "notExist";
		exit;
	}
}
else if($V['rMode']=="remove") {
	if($curUserPermision == "administrator" || !$currUserID){
		echo "PermisionError";
		exit;
	}

	if(!$V['tIdx'] || !$V['rGoodsIdx']){
		echo "DataError";
		exit;
	}

	$idx=$V['tIdx'];
	$goods_idx=$V['rGoodsIdx'];

	if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
		$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND r_earn_paid='N' AND r_earn_point<='0'"); // 총 Review 수
	}
	else{
		$reviewCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."' AND r_earn_paid='N' AND r_earn_point<='0'"); // 총 Review 수
	}
	
	if($reviewCnt>'0'){
		if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
			$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND r_earn_paid='N' AND r_earn_point<='0'");
		}
		else{
			$rData = $wpdb->get_row("SELECT r_attach_new FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."' AND r_earn_paid='N' AND r_earn_point<='0'");
		}
		
		if($rData->r_attach_new){
			$delFile_path=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$rData->r_attach_new;
			@unlink($delFile_path);
		}

		if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
			$wpdb->query("DELETE FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND r_earn_paid='N' AND r_earn_point<='0'");
		}
		else{
			$wpdb->query("DELETE FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."' AND r_earn_paid='N' AND r_earn_point<='0'");
		}
		
		echo "success";
		exit;
	}
	else{
		echo "notExist";
		exit;
	}
}
else if($V['rMode']=="getReview") {
	if($curUserPermision == "administrator" || !$currUserID){
		echo "PermisionError";
		exit;
	}

	if(!$V['tIdx'] || !$V['rGoodsIdx']){
		echo "DataError";
		exit;
	}

	$idx=$V['tIdx'];
	$goods_idx=$V['rGoodsIdx'];

	if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
		$data=$wpdb->get_row("SELECT * FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."'");
	}
	else{
		$data=$wpdb->get_row("SELECT * FROM bbse_commerce_review WHERE idx<>'' AND idx='".$idx."' AND goods_idx='".$goods_idx."' AND user_id='".$user_id."'");
	}
	
	if($data->idx > '0'){
		$rSubject=stripslashes($data->r_subject);
		$rContents=stripslashes($data->r_contents);

		echo "success|||".bbse_get_unhtmlspecialchars($rSubject)."|||".bbse_get_unhtmlspecialchars($rContents)."|||".trim($data->r_value)."|||".trim($data->r_attach_org);
		exit;
	}
	else{
		echo "notExist";
		exit;
	}
}
else if($V['rMode']=="paging") {
	if(!$V['rGoodsIdx'] || !$V['tPage']  || !$V['review_per_page'] || !$V['page_block'] || !$V['function_name']){
		echo "DataError";
		exit;
	}

	$goods_idx=$V['rGoodsIdx'];
	$paged=$V['tPage'];
	$review_per_page=$V['review_per_page'];
	$page_block=$V['page_block'];
	$function_name=$V['function_name'];

	$rtnStr=bbse_get_goods_review_list($function_name, $goods_idx, $paged, $review_per_page, $page_block);

	if($rtnStr){
		echo "success|||".$rtnStr;
		exit;
	}

}

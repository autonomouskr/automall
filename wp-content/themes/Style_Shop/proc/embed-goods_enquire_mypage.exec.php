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

if($V['tMode']=="modify") {
	if($curUserPermision == "administrator" || !$currUserID){
		echo "PermisionError";
		exit;
	}

	if(!$V['qIdx'] || !$V['q_subject'] || !$V['q_contents']){
		echo "DataError";
		exit;
	}

	$idx=$V['qIdx'];

	if($V['q_secret']=='on') $q_secret="on";
	else $q_secret="off";

	$q_subject=addslashes(htmlspecialchars($V['q_subject']));
	$q_contents=addslashes(htmlspecialchars($V['q_contents']));

	$paged=$V['paged'];
	$qna_per_page=$V['qna_per_page'];
	$page_block=$V['page_block'];
	$function_name=$V['function_name'];

	if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
		$qnaCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx='".$idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND q_status='ready' AND q_type='Q'"); // 총 Q&A 수
	}
	else{
		$qnaCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx='".$idx."' AND user_id='".$user_id."' AND q_status='ready' AND q_type='Q'"); // 총 Q&A 수
	}	
	
	if($qnaCnt>'0'){
		if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
			$wpdb->query("UPDATE bbse_commerce_qna SET q_secret='".$q_secret."', q_subject='".$q_subject."', q_contents='".$q_contents."' WHERE idx='".$idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND q_status='ready' AND q_type='Q'");
		}
		else{
			$wpdb->query("UPDATE bbse_commerce_qna SET q_secret='".$q_secret."', q_subject='".$q_subject."', q_contents='".$q_contents."' WHERE idx='".$idx."' AND user_id='".$user_id."' AND q_status='ready' AND q_type='Q'");
		}

		$rtnStr=bbse_get_goods_qna_mypage_list($function_name, $paged, $qna_per_page, $page_block);

		echo "success|||".$rtnStr;
		exit;
	}
	else{
		echo "notExist";
		exit;
	}
}
else if($V['tMode']=="remove") {

	if($curUserPermision == "administrator" || !$currUserID){
		echo "PermisionError";
		exit;
	}

	if(!$V['tIdx']){
		echo "DataError";
		exit;
	}

	$idx=$V['tIdx'];

	if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
		$qnaCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND idx='".$idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND q_status='ready' AND q_type='Q'"); // 총 Q&A 수
	}
	else{
		$qnaCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx<>'' AND idx='".$idx."' AND user_id='".$user_id."' AND q_status='ready' AND q_type='Q'"); // 총 Q&A 수
	}	
	
	if($qnaCnt>'0'){
		if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
			$wpdb->query("DELETE FROM bbse_commerce_qna WHERE idx<>'' AND idx='".$idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND q_status='ready' AND q_type='Q'");
		}
		else{
			$wpdb->query("DELETE FROM bbse_commerce_qna WHERE idx<>'' AND idx='".$idx."' AND user_id='".$user_id."' AND q_status='ready' AND q_type='Q'");
		}	

		echo "success";
		exit;
	}
	else{
		echo "notExist";
		exit;
	}
}
else if($V['tMode']=="getQnA") {
	if($curUserPermision == "administrator" || !$currUserID){
		echo "PermisionError";
		exit;
	}

	if(!$V['tIdx']){
		echo "DataError";
		exit;
	}

	$idx=$V['tIdx'];

	if($Loginflag=='social' && $sns_id && $sns_idx>'0'){
		$data=$wpdb->get_row("SELECT * FROM bbse_commerce_qna WHERE idx<>'' AND idx='".$idx."' AND user_id='' AND sns_id='".$sns_id."' AND sns_idx='".$sns_idx."' AND q_status='ready' AND q_type='Q'");
	}
	else{
		$data=$wpdb->get_row("SELECT * FROM bbse_commerce_qna WHERE idx<>'' AND idx='".$idx."' AND user_id='".$user_id."' AND q_status='ready' AND q_type='Q'");
	}	
	
	if($data->idx > '0'){
		$qSubject=stripslashes($data->q_subject);
		$qContents=stripslashes($data->q_contents);

		echo "success|||".bbse_get_unhtmlspecialchars($qSubject)."|||".bbse_get_unhtmlspecialchars($qContents)."|||".trim($data->q_secret);
		exit;
	}
	else{
		echo "notExist";
		exit;
	}
}
else if($V['tMode']=="paging") {
	if(!$V['tPage']  || !$V['qna_per_page'] || !$V['page_block'] || !$V['function_name']){
		echo "DataError";
		exit;
	}

	$paged=$V['tPage'];
	$qna_per_page=$V['qna_per_page'];
	$page_block=$V['page_block'];
	$function_name=$V['function_name'];

	$rtnStr=bbse_get_goods_qna_mypage_list($function_name, $paged, $qna_per_page, $page_block);

	if($rtnStr){
		echo "success|||".$rtnStr;
		exit;
	}

}

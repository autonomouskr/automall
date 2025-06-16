<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

global $wpdb;

$V = $_POST;

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

if($V['tMode']=='insert'){ 
	if(!$V['tIdx'] || !$V['tAnswer']){
		echo "fail";
		exit;
	}

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx='".$V['tIdx']."' AND q_type='Q'");

	if($cnt<='0'){
		echo "notExist";
		exit;
	}

	$data=$wpdb->get_row("SELECT * FROM bbse_commerce_qna WHERE idx='".$V['tIdx']."' AND q_type='Q'");

	$user_id=$current_user->user_login;
	$user_name=$current_user->user_login;
	$goods_idx=$data->goods_idx;
	$goods_name=addslashes($data->goods_name);
	$q_type="A";
	$q_parent=$V['tIdx'];
	$q_secret=$data->q_secret;
	$q_status="answer";
	$q_subject=$data->q_subject;
	$q_contents=addslashes(htmlspecialchars($V['tAnswer']));
	$write_date=current_time('timestamp');

	$inQuery="INSERT INTO bbse_commerce_qna (user_id, user_name, goods_idx, goods_name, q_type, q_parent, q_secret, q_status, q_subject, q_contents, write_date) VALUES ('".$user_id."', '".$user_name."', '".$goods_idx."', '".$goods_name."', '".$q_type."', '".$q_parent."', '".$q_secret."', '".$q_status."', '".$q_subject."', '".$q_contents."', '".$write_date."')";

	$wpdb->query($inQuery);
	$idx = $wpdb->insert_id;

	if($idx>'0'){
		$wpdb->query("UPDATE bbse_commerce_qna SET q_status='answer' WHERE idx='".$V['tIdx']."' AND q_type='Q'");

		echo "success";
		exit;
	}
	else{
		echo "DbError";
		exit;
	}
}
elseif($V['tMode']=='modify'){ 
	if(!$V['tIdx'] || !$V['aIdx'] || !$V['tAnswer']){
		echo "fail";
		exit;
	}

	$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx='".$V['tIdx']."' AND q_type='Q'");
	$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx='".$V['aIdx']."' AND q_parent='".$V['tIdx']."' AND q_type='A'");

	if($cnt<='0'){
		echo "notExistQuestion";
		exit;
	}
	elseif($cnt2<='0'){
		echo "notExistAnswer";
		exit;
	}

	$q_contents=addslashes(htmlspecialchars($V['tAnswer']));

	$wpdb->query("UPDATE bbse_commerce_qna SET q_contents='".$q_contents."' WHERE idx='".$V['aIdx']."' AND q_parent='".$V['tIdx']."' AND q_type='A'");

	echo "success";
	exit;
}
elseif($V['tMode']=='removeAnswer'){ // 답변삭제
	if(!$V['tIdx'] || !$V['aIdx'] || !$V['tAnswer']){
		echo "fail";
		exit;
	}

	$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx='".$V['aIdx']."' AND q_parent='".$V['tIdx']."' AND q_type='A'");

	if($cnt2<='0'){
		echo "notExistAnswer";
		exit;
	}

	$wpdb->query("DELETE FROM bbse_commerce_qna WHERE idx='".$V['aIdx']."' AND q_parent='".$V['tIdx']."' AND q_type='A'");
	$wpdb->query("UPDATE bbse_commerce_qna SET q_status='ready' WHERE idx='".$V['tIdx']."' AND q_type='Q'");

	echo "success";
	exit;
}
elseif($V['tMode']=='removeQuestion'){ // 문의글 삭제
	if(!$V['tIdx']){
		echo "fail";
		exit;
	}

	$cnt2=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_qna WHERE idx='".$V['tIdx']."' AND q_type='Q'");

	if($cnt2<='0'){
		echo "notExistQuestion";
		exit;
	}

	$wpdb->query("DELETE FROM bbse_commerce_qna WHERE (idx='".$V['tIdx']."' AND q_type='Q') OR (q_parent='".$V['tIdx']."' AND q_type='A')");

	echo "success";
	exit;
}
elseif($V['tMode']=='bulkAction'){ // 문의글 삭제
	if(!$V['tData']){
		echo "fail";
		exit;
	}

	if($V['tStatus']=='remove'){
		$checkIdx=explode(",",$V['tData']);
		$sql="";
		for($i=0;$i<sizeof($checkIdx);$i++){
			if($checkIdx[$i]>0){
				if($sql) $sql .=" OR ";
				$sql .="(idx='".$checkIdx[$i]."' AND q_type='Q') OR (q_parent='".$checkIdx[$i]."' AND q_type='A')";
			}
		}

		$res = $wpdb->query("DELETE FROM bbse_commerce_qna WHERE ".$sql);

		if($res){
			echo "success";
			exit;
		}
		else{
			echo "dbError";
			exit;
		}
	}
}
else{
	echo "nonData";
	exit;
}
?>
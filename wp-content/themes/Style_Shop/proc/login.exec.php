<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

//Set a cookie now to see if they are supported by the browser.
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if(SITECOOKIEPATH != COOKIEPATH)
	setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN);

if(!function_exists("json_encode")){ 
    function json_encode($a=false){ 
        if(is_null($a)) return 'null'; 
        if($a === false) return 'false'; 
        if($a === true) return 'true'; 
        if(is_scalar($a)){ 
            if(is_float($a)) return floatval(str_replace(",", ".", strval($a))); 
            if(is_string($a)){ 
                static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"')); 
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"'; 
            } else return $a; 
        } 
        $isList = true; 
        for($i=0, reset($a); $i<count($a); $i++, next($a)){ 
            if(key($a) !== $i){ 
                $isList = false; 
                break; 
            } 
        } 
        $result = array(); 
        if($isList){ 
            foreach($a as $v) $result[] = json_encode($v); 
            return '[' . join(',', $result) . ']'; 
        } else{ 
            foreach($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v); 
            return '{' . join(',', $result) . '}'; 
        } 
    } 
}

$V = $_GET;
$V['upass']=base64_decode($V['upass']);

$json_data = array();
if(empty($V['uid'])){echo $V['callback']."([";$json_data['result'] = "empty id";echo json_encode($json_data);echo "])";exit;}
if(empty($V['upass'])){echo $V['callback']."([";$json_data['result'] = "empty pass";echo json_encode($json_data);echo "])";exit;}

$bbse_user = $wpdb->get_var("select count(*) from bbse_commerce_membership where user_id='".$V['uid']."'");
$wp_user = $wpdb->get_var("select count(*) from ".$wpdb->users." where user_login='".$V['uid']."'");

if($bbse_user > 0 || $wp_user > 0){
	$bbse_login = $wpdb->get_var("select count(*) from bbse_commerce_membership where user_id='".$V['uid']."' and user_pass=password('".$V['upass']."') and leave_yn='0'"); // 탈퇴회원이 아닌 사용자
	$wp_login = apply_filters('authenticate', null, $V['uid'], $V['upass']);  // wp_users 테이블 검색

	if($wp_login->ID > 0 && $bbse_login>0){// 탈퇴회원이 아닌 사용자
		$creds = array();
		$creds['user_login'] = $V['uid'];
		$creds['user_password'] = $V['upass'];
		if(isset($V['remember'])) $creds['remember'] = true;
		$user = wp_signon($creds, false); 

		$currTime=current_time('timestamp');

		//$member = $wpdb->get_row("select user_no,name,birth,mileage from bbse_commerce_membership where user_id='".$V['uid']."'");
		$member = $wpdb->get_row("select user_no,name,birth,mileage,user_class from bbse_commerce_membership where user_id='".$V['uid']."'");

		if(plugin_active_check('BBSe_Commerce')) {
			$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='earn'");
			if($confData->config_data){
				$data=unserialize($confData->config_data);
			}

			//미사용 적립금 차감
			if($data['earn_reset_use']=='on'){
				$lastPointCnt = $wpdb->get_var("select count(*) from bbse_commerce_earn_log where user_id='".$V['uid']."' AND earn_mode='IN'");
				if($lastPointCnt>0){
					$lastPointDate = $wpdb->get_var("select reg_date from bbse_commerce_earn_log where user_id='".$V['uid']."' AND earn_mode='IN' order by idx desc limit 1");
					$lastYearDate = strtotime("-1 year", current_time('timestamp'));
					if($lastPointDate < $lastYearDate) {//마지막 적립 내역이 1년이 지났을경우 적립금 삭제
						$wpdb->query("insert into bbse_commerce_earn_log set earn_mode='OUT', earn_type='delete', earn_point='', old_point='".$member->mileage."', user_id='".$V['uid']."', user_name='".$member->name."',reg_date='".$currTime."'");
						$wpdb->query("update bbse_commerce_membership set mileage='0' where user_no='".$member->user_no."'");
					}
				}
			}

			//생일축하 적립금 : 생일기준 15일 전후(시작)
			if($data['earn_birth_use']=='on' && $data['earn_birth_point']>'0'){
				$memBirth=explode("-",$member->birth);
				if($memBirth['0']>0 && $memBirth['1']>0 && $memBirth['2']>0){ // 생일이 등록된 경우만 실행
					$bthTime_s=mktime('00','00','01',$memBirth['1'],$memBirth['2']-15,date("Y",$currTime));
					$bthTime_e=mktime('23','59','59',$memBirth['1'],$memBirth['2']+15,date("Y",$currTime));

					if($currTime>=$bthTime_s && $currTime<=$bthTime_e){
						$cnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_earn_log WHERE earn_mode='IN' AND earn_type='birth' AND user_id='".$V['uid']."' AND reg_date>='".$bthTime_s."'");

						if($cnt<='0'){
							$memInfo = $wpdb->get_row("SELECT user_no,name,mileage FROM bbse_commerce_membership WHERE user_id='".$V['uid']."'");
							$wpdb->query("INSERT INTO bbse_commerce_earn_log (earn_mode, earn_type, earn_point, old_point, user_id, user_name, reg_date) VALUES ('IN', 'birth', '".$data['earn_birth_point']."', '".$memInfo->mileage."' ,'".$V['uid']."', '".$memInfo->name."', '".$currTime."')");

							$newMileage=$memInfo->mileage+$data['earn_birth_point'];
							$wpdb->query("update bbse_commerce_membership set mileage='".$newMileage."' where user_no='".$memInfo->user_no."'");
						}
					}
				}
			}
			//생일축하 적립금(끝)
		}

		if(is_wp_error($user)){echo $V['callback']."([";$json_data['result'] = "login_fail";echo json_encode($json_data);echo "])";exit;}

		$wpdb->query("update bbse_commerce_membership set last_login='".$currTime."' where user_no='".$member->user_no."'");//최근 로그인 시간 저장

		// 소셜로그인 - 회원통합
		if($V['snsLogin']=='ok') bbse_integrate_social_login($member->user_no, $V['uid'], $currTime);

		bbse_cart_IP_to_ID($V['uid']); // 일반회원 : 장바구니 (IP -> ID, 네이버 페이)
		echo $V['callback']."([";$json_data['result'] = "success";echo json_encode($json_data);echo "])";exit;
	}
	elseif($wp_login->ID > 0){
		if($wp_login->roles[0]=="administrator"){
			$creds = array();
			$creds['user_login'] = $V['uid'];
			$creds['user_password'] = $V['upass'];
			if(isset($V['remember'])) $creds['remember'] = true;
			$user = wp_signon($creds, false); 

			if(is_wp_error($user)){echo $V['callback']."([";$json_data['result'] = "login_fail";echo json_encode($json_data);echo "])";exit;}

			// 소셜로그인 - 회원통합
			if($V['snsLogin']=='ok') bbse_integrate_social_login($member->user_no, $V['uid'], $currTime);

			bbse_cart_IP_to_ID($V['uid']); // 관리자 : 장바구니 (IP -> ID, 네이버 페이)
			echo $V['callback']."([";$json_data['result'] = "success";echo json_encode($json_data);echo "])";exit;
		}
		else{
			echo $V['callback']."([";$json_data['result'] = "login_fail";echo json_encode($json_data);echo "])";exit;
		}
	}
	else{
		echo $V['callback']."([";$json_data['result'] = "pass_fail";echo json_encode($json_data);echo "])";exit;
	}
}else{ 
	echo $V['callback']."([";$json_data['result'] = "id_fail";echo json_encode($json_data);echo "])";exit;
}
?>
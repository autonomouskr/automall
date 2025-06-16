<?php
$currentSessionID = $_POST['sess_id'];
session_id($currentSessionID);
session_start();

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';

$site_url_arr    = explode("/", home_url());
$site_domain_url = "http://".$site_url_arr[2];

header("Content-Type: text/html; charset=UTF-8");
//header("Access-Control-Allow-Origin:".$site_domain_url);
header("Access-Control-Allow-Origin:","localhost");
header("Access-Control-Allow-Credentials: true");
header("X-Content-Type-Options:nosniff");
header("X-XSS-Protection:1; mode=block");

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user     = wp_get_current_user();  // 현재 회원의 정보 추출

$V             = $_POST;
$cert_tmp_buff = explode("\n", base64_decode(trim($V['cert'])));
$cert_time     = base64_decode($cert_tmp_buff[0]);
$stime         = current_time('timestamp');
$limitime      = $stime - 3600;

if(empty($V['page']))         $V['page']         = 1;
if(empty($V['keyfield']))     $V['keyfield']     = NULL;
if(empty($V['keyword']))      $V['keyword']      = NULL;
if(empty($V['search_chk']))   $V['search_chk']   = NULL;
if(empty($V['cate']))         $V['cate']         = NULL;
if(empty($V['cno']))          $V['cno']          = NULL;
if(empty($V['reply_cname']))  $V['reply_cname']  = NULL;
if(empty($V['reply_cpass']))  $V['reply_cpass']  = NULL;
if(empty($V['reply_string'])) $V['reply_string'] = NULL;
if(empty($V['reply_cmemo']))  $V['reply_cmemo']  = NULL;

$tblBoard   = $wpdb->prefix.'bbse_board';
$tblComment = $wpdb->prefix.'bbse_'.$V['bname'].'_comment';

//cno 있으면
if(!empty($V['cno'])){
	//cno 체크
	passNumer($V['cno'], "not permission|||2");

	if(!empty($V['cmode'])){
		//댓글의 댓글
		if($V['cmode'] == "reply"){
			if(empty($V['bname'])){
				echo "fail|||2";
				exit;
			}

			if(empty($V['no'])){
				echo "fail|||2";
				exit;
			}else{
				passNumer($V['no'], "not permission|||2");
			}

			$prepare   = NULL;
			$prepare   = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE boardname = %s", array( $V['bname'] ) );
			$boardInfo = $wpdb->get_row( $prepare );

			$prepare   = NULL;
			$prepare   = $wpdb->prepare( "SELECT * FROM {$tblComment} WHERE no = %d", array( $V['cno'] ));
			$comment   = $wpdb->get_row( $prepare );

			if(empty($comment->no)){
				echo "fail|||2";
				exit;
			}

			if(($stime >= $cert_time && $limitime < $cert_time) && $V['mode'] == "view" && !empty($V['no'])){
				if($boardInfo->l_comment == "private"){
					if(!empty($boardInfo->l_comment)){
						$private_auth = false;
						$private_auth = bbse_private_user_check($boardInfo, "l_comment");
						if($private_auth == false && $curUserPermision != "administrator"){
							echo "not permission|||2";
							exit;
						}
					}
				}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_comment) ){
					echo "not permission|||2";
					exit;
				}

				if(empty($curUserPermision) || $curUserPermision == "all"){
					if(empty($V['reply_cname'])){
						echo "empty cname|||2";
						exit;
					}

					if(mb_strlen($V['reply_cname']) > 16){
						echo "long cname|||2";
						exit;
					}

					if(empty($V['reply_cpass'])){
						echo "empty cpass|||2";
						exit;
					}

					if(mb_strlen($V['reply_cpass']) > 16){
						echo "long cpass|||2";
						exit;
					}

					if($boardInfo->using_subAutoWtype != 'NO'){
						if(empty($V['reply_string'])){
							echo "empty string|||2";
							exit;
						}

						if(!$_SESSION['authKeySub'] || !$V['reply_string'] || $_SESSION['authKeySub'] != $V['reply_string']){
							echo "fail string|||2";
							exit;
						}
					}
					if(empty($V['reply_cmemo'])){
						echo "empty cmemo|||2";
						exit;
					}

					$memnum = 0;
					$writer = $V['reply_cname'];
					$pass   = "password('".$V['reply_cpass']."')";

				}else{
					if($boardInfo->using_subAutoWtype != 'NO'){
						if(empty($V['reply_string'])){
							echo "empty string|||2";
							echo "])";
							exit;
						}
						if(!$_SESSION['authKeySub'] || !$V['reply_string'] || $_SESSION['authKeySub'] != $V['reply_string']){
							echo "fail string|||2";
							exit;
						}
					}

					if(empty($V['reply_cmemo'])){
						echo "empty cmemo|||2";
						exit;
					}

					$memnum = $current_user->ID;
					$writer = $current_user->user_login;
					$pass   = '\''.esc_sql($current_user->user_pass).'\'';
				}

				$depth = $comment->depth + 1;


				$V['reply_cmemo'] = htmlentities($V['reply_cmemo'], ENT_QUOTES, 'UTF-8');
				$prepare = NULL;
				$prepare = $wpdb->prepare(
					"INSERT INTO {$tblComment}
						(`parent`, `comm_parent`, `memnum`, `writer`, `pass`, `content`, `ip`,  `depth`, `write_date`)
					values
						(%d, %d, %d, %s, {$pass}, %s, %s, %d, 'now()')",
					array(
						$V['no'],
						$comment->no,
						$memnum,
						$writer,
						$V['reply_cmemo'],
						$_SERVER['REMOTE_ADDR'],
						$depth
					)
				);
				$wpdb->query( $prepare );

				echo "success|||2";
				exit;

			}else{
				echo "fail|||2";
				exit;
			}
		//댓글편집
		}else if($V['cmode'] == "edit"){
			if(empty($V['bname'])){
				echo "fail|||3";
				exit;
			}

			if(empty($V['no'])){
				echo "fail|||3";
				exit;
			}else{
				passNumer($V['no'], "not permission|||2");
			}

			$prepare   = NULL;
			$prepare   = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE boardname = %s", array( $V['bname'] ) );
			$boardInfo = $wpdb->get_row( $prepare );

			$prepare = NULL;
			$prepare = $wpdb->prepare( "SELECT * FROM {$tblComment} WHERE no = %d", array( $V['cno'] ));
			$comment = $wpdb->get_row( $prepare );

			if(empty($comment->no)){
				echo "fail|||3";
				exit;
			}

			if(($stime >= $cert_time && $limitime < $cert_time) && $V['mode'] == "view" && !empty($V['no'])){
				if($boardInfo->l_comment == "private"){
					if(!empty($boardInfo->l_comment)){
						$private_auth = false;
						$private_auth = bbse_private_user_check($boardInfo, "l_comment");
						if($private_auth == false && $curUserPermision != "administrator"){
							echo "not permission|||3";
							exit;
						}
					}
				}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_comment) ){
					echo "not permission|||3";
					exit;
				}

				if($curUserPermision == "administrator"){
					if(empty($V['edit_cname'])){
						echo "empty cname|||3";
						exit;
					}

					if(mb_strlen($V['edit_cname']) > 16){
						echo "long cname|||3";
						exit;
					}
				}

				if($boardInfo->using_subAutoWtype != 'NO'){
					if(empty($V['edit_string'])){
						echo "empty string|||3";
						exit;
					}

					if(!$_SESSION['authKeySub'] || !$V['edit_string'] || $_SESSION['authKeySub'] != $V['edit_string']){
						echo "fail string|||3";
						exit;
					}
				}
				if(empty($V['edit_cmemo'])){
					echo "empty cmemo|||3";
					exit;
				}

				$add_fields = '';
				if(!empty($V['edit_cname'])) {
					$add_fields = $wpdb->prepare( ", `writer`= %s", $V['edit_cname'] );
				}

				$V['edit_cmemo'] = htmlentities($V['edit_cmemo'], ENT_QUOTES, 'UTF-8');
				$prepare = NULL;
				$prepare = $wpdb->prepare( "UPDATE `{$tblComment}` SET `content`= %s{$add_fields} WHERE `no`= %d ", array( $V['edit_cmemo'], $comment->no ) );
				$wpdb->query( $prepare );

				echo "success|||3";
				exit;

			}else{
				echo "fail|||3";
				exit;
			}
		}
	}

//댓글추가
}else{
	if(empty($V['bname'])){
		echo "fail|||1";
		exit;
	}

	if(empty($V['no'])){
		echo "fail|||1";
		exit;
	}else{
		passNumer($V['no'], "not permission|||2");
	}

	$prepare   = NULL;
	$prepare   = $wpdb->prepare( "SELECT * FROM {$tblBoard} WHERE boardname = %s", array( $V['bname'] ) );
	$boardInfo = $wpdb->get_row( $prepare );

	if(($stime >= $cert_time && $limitime < $cert_time) && $V['mode'] == "view" && !empty($V['no'])){
		if($boardInfo->l_comment == "private"){
			if(!empty($boardInfo->l_comment)){
				$private_auth = false;
				$private_auth = bbse_private_user_check($boardInfo, "l_comment");
				if($private_auth == false && $curUserPermision != "administrator"){
					echo "not permission|||1";
					exit;
				}
			}
		}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_comment) ){
			echo "not permission|||1";
			exit;
		}

		if(empty($curUserPermision) || $curUserPermision == "all"){
			if(empty($V['cname'])){
				echo "empty cname|||1";
				exit;
			}

			if(mb_strlen($V['cname']) > 16){
				echo "long cname|||1";
				exit;
			}

			if(empty($V['cpass'])){
				echo "empty cpass|||1";
				exit;
			}

			if(mb_strlen($V['cpass']) > 16){
				echo "long cpass|||1";
				exit;
			}

			if($boardInfo->using_subAutoWtype != 'NO'){
				if(empty($V['string'])){
					echo "empty string|||1";
					exit;
				}

				if(!$_SESSION['authKey'] || !$V['string'] || $_SESSION['authKey'] != $V['string']){
					echo "fail string|||1";
					exit;
				}
			}
			if(empty($V['cmemo'])){
				echo "empty cmemo|||1";
				exit;
			}

			$memnum = 0;
			$writer = $V['cname'];
			$pass   = "password('".$V['cpass']."')";

		}else{
			if($boardInfo->using_subAutoWtype != 'NO'){
				if(empty($V['string'])){
					echo "empty string|||1";
					exit;
				}

				if(!$_SESSION['authKey'] || !$V['string'] || $_SESSION['authKey'] != $V['string']){
					echo "fail string|||1";
					exit;
				}
			}
			if(empty($V['cmemo'])){
				echo "empty cmemo|||1";
				exit;
			}

			$memnum = $current_user->ID;
			$writer = $current_user->user_login;
			$pass   = '\''.esc_sql($current_user->user_pass).'\'';
		}

		$V['cmemo'] = htmlentities($V['cmemo'], ENT_QUOTES, 'UTF-8');
		$prepare = NULL;
		$prepare = $wpdb->prepare(
			"INSERT INTO {$tblComment}
				(`parent`, `comm_parent`, `memnum`, `writer`, `pass`, `content`, `ip`, `depth`, `write_date`)
			values
				(%d, 0, %d, %s, {$pass}, '{$V['cmemo']}', %s, 0, now())",
			array(
				$V['no'],
				$memnum,
				$writer,
				$_SERVER['REMOTE_ADDR']
			)
		);
		$wpdb->query( $prepare );

		$comm_parent = $wpdb->insert_id;
		$wpdb->query( "update {$tblComment} set comm_parent = '{$comm_parent}' where no = '{$comm_parent}'" );
		echo "success|||1";
		exit;

	}else{
		echo "fail|||1";
		exit;
	}
}
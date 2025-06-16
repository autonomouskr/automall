<?php
class BBSeBoard{
	var $row;
	var $totCount;
	var $bName;

	/*-------------------------------------------------------------------
	 * 관리자 start
	 ------------------------------------------------------------------*/
	// 게시판 리스트 추출
	public function get_board_list($start_pos="", $per_page=""){
		global $wpdb;

		if(isset($start_pos) && !empty($per_page)){
			$prepare   = NULL;
			$prepare   = $wpdb->prepare("SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`<>'' AND `board_no` is not null ORDER BY `board_no` DESC LIMIT %d, %d", $start_pos, $per_page);
			$this->row = $wpdb->get_results($prepare);
		}else{
			$this->row = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`<>'' AND `board_no` is not null ORDER BY `board_no` DESC");
		}
		return $this->row;
	}

	// 생성된 게시판 수 추출
	public function get_board_count(){
		global $wpdb;

		$this->totCount = $wpdb->get_var("SELECT count(*) FROM `".$wpdb->prefix."bbse_board` WHERE `board_no`<>'' AND `board_no` is not null ORDER BY `board_no` DESC");
		return $this->totCount;
	}
	/*-------------------------------------------------------------------
	 * 관리자 end
	 ------------------------------------------------------------------*/

	// 글목록 페이지
	public function get_list($bname=""){
		global $wpdb;



		$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
		$current_user     = wp_get_current_user();  // 현재 회원의 정보 추출

		if(!empty($_GET['nType'])) $_VAR = bbse_board_parameter_decryption($_GET['nType']);

		$page_id   = get_queried_object_id();
		$prepare   = NULL;
		$prepare   = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE `boardname`=%s",  array( $bname ) );
		$boardInfo = $wpdb->get_row( $prepare );
		$curUrl    = get_permalink();

		if(!empty($_GET['page_id'])) $link_add = "&";
		else $link_add = "?";

		$page_block = 10;

		if(empty($boardInfo->table_width)) $boardInfo->table_width = "100";
		if($boardInfo->table_width <= 100) $table_width = $boardInfo->table_width."%";
		else $table_width = $boardInfo->table_width."px";

		if(empty($boardInfo->table_align)) $boardInfo->table_align = "L";
		switch($boardInfo->table_align){
			case "C": $table_align = "margin:0 auto;";break;
			case "R": $table_align = "float:right;";break;
			case "L": default: $table_align = "float:left;";break;
		}

		if(empty($boardInfo->no_permission_url)){
			$no_permission_url = BBSE_BOARD_SITE_URL;
		}else{
			$no_permission_url = $boardInfo->no_permission_url;
		}

		if(empty($_VAR['mode']))       $_VAR['mode']       = NULL;
		if(empty($_VAR['search_chk'])) $_VAR['search_chk'] = NULL;
		if(empty($_VAR['keyfield']))   $_VAR['keyfield']   = NULL;
		if(empty($_VAR['keyword']))    $_VAR['keyword']    = NULL;
		if(empty($_VAR['cate']))       $_VAR['cate']       = NULL;
		if(empty($_VAR['no']))         $_VAR['no']         = NULL;
		if(empty($_VAR['ref']))        $_VAR['ref']        = NULL;
		if(empty($_VAR['page']))       $_VAR['page']       = 1;

		if(!empty($boardInfo->l_list)){
			if($boardInfo->l_list == "private"){
				if(!empty($boardInfo->user_list)){
					$private_auth = false;
					$private_auth = bbse_private_user_check($boardInfo, "l_list");
					if($private_auth == false && $curUserPermision != "administrator"){
						echo "
							<script type='text/javascript'>
							alert('게시판 목록보기 권한이 없습니다.');
							location.href = '".$no_permission_url."';
							</script>";
						return "";
					}
				}
			//}else if(($boardInfo->l_list == "author" && $curUserPermision == "all") || ($boardInfo->l_list == "administrator" && $curUserPermision != "administrator")){
			}else if(bbse_check_user_level() < bbse_role2level($boardInfo->l_list) ){
				echo '
					<script>
					alert("게시판 목록보기 권한이 없습니다.");
					location.href = "'.$no_permission_url.'";
					</script>';
				return "";
			}
		}

		$where = " where `movecheck`<>'0' ";

		if(!empty($_VAR['search_chk']) && $_VAR['search_chk'] == 1){
			$where .= " AND `".$_VAR['keyfield']."` LIKE '%".esc_sql($_VAR['keyword'])."%'";
		}
		if(!empty($_VAR['cate'])){
			$where .= " AND `category`='".esc_sql($_VAR['cate'])."'";
		}

		$total = $wpdb->get_var("SELECT count(*) FROM `".$wpdb->prefix."bbse_".$bname."_board` ".$where);

		$totalpage = ceil($total / $boardInfo->page_size);
		$startpos  = ($_VAR['page'] - 1) * $boardInfo->page_size;
		if($startpos > $total) $startpos = 0;
		if($_VAR['page'] > $totalpage) $_VAR['page'] = 1;

		$orderby = NULL;
		if($boardInfo->formtype == "1"){
			$orderby .= " ORDER BY `use_notice` DESC, `ref` DESC, `re_step` ASC LIMIT ".$startpos.", ".$boardInfo->page_size;

		}else{
			$orderby .= " ORDER BY `ref` DESC, `re_step` ASC LIMIT ".$startpos.", ".$boardInfo->page_size;
		}

		$result = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."bbse_".$bname."_board` ".$where.$orderby);

		if(!empty($boardInfo->category_list)){
			$category_arr = explode(",", $boardInfo->category_list);

			$select_category = "<select name=\"category\" onchange=\"location.href = this.value\">";
			$select_category .= "<option value=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'])."\">카테고리</option>";
			for($i = 0; $i < count($category_arr); $i++){
				$select_category .= "<option value=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $category_arr[$i])."\"";
				if($_VAR['cate'] == $category_arr[$i]){
					$select_category .= "selected";
				}
				$select_category .= ">".$category_arr[$i]."</option>";
			}
			$select_category .= "</select> &nbsp;";

		}else $select_category = NULL;

		// list 링크 정의
		$list_move = "<a href=\"javascript:;\" onclick=\"move_page('".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/move.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."', 'move');\" class=\"btn_big\">";
		$list_copy = "<a href=\"javascript:;\" onclick=\"move_page('".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/move.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."', 'copy');\" class=\"btn_big\">";
		$list_delete = "<a href=\"javascript:;\" onclick=\"del('".BBSE_BOARD_PLUGIN_WEB_URL."proc/delete.exec.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."');\" class=\"btn_big\">";

		if($_VAR['page'] == 1) $pagelink_first = "<a href=\"javascript:;\" onclick=\"void(0);\" class=\"pre_end\">";
		else $pagelink_first = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", "1", $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\" class=\"pre_end\">";

		$page_temp = floor(($_VAR['page'] - 1) / $page_block) * $page_block + 1;
		if($page_temp == 1){
			$pagelink_pre = '<a href="javascript:;" onclick="void(0);" class="pre">';
		}else{
			$n_page = $page_temp - $page_block;
			$pagelink_pre = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $n_page, $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\" class=\"pre\">";
		}
		$pagelink_view = NULL;
		for($intloop = 1; $intloop <= $page_block && $page_temp <= $totalpage; $intloop++){
			if($intloop == 1){
				$first_cls   = " class='fir'";
				$first_style = " style='border:none;'";
				$page_nbsp   = NULL;
			}else{
				$first_cls   = NULL;
				$first_style = NULL;
				$page_nbsp   = '&nbsp;';
			}
			if($page_temp == $_VAR['page']){
				$pagelink_view = $pagelink_view.$page_nbsp."<strong".$first_style.">".$page_temp."</strong>" ;
			}else{
				$pagelink_view = $pagelink_view.$page_nbsp."<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $page_temp, $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\"".$first_cls.">".$page_temp."</a>";
			}
			$page_temp = $page_temp + 1;
		}

		if($page_temp > $totalpage){
			$pagelink_next = "<a href=\"javascript:;\" onclick=\"void(0);\" class=\"next\">";
		}else{
			$pagelink_next = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $page_temp, $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\" class=\"next\">";
		}

		if($_VAR['page'] == $totalpage|| $totalpage == 0){
			$pagelink_last = "<a href=\"javascript:;\" onclick=\"void(0);\" class=\"next_end\">";
		}else{
			$pagelink_last = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], "", $totalpage, $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\" class=\"next_end\">";
		}

		$list_write = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'write', '', $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\" class=\"btn_big\">";
		$list_list = "<a href=\"".$curUrl."\">";

		if(is_dir(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname)){
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname.'/search.php');
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname.'/list_head.php');

			if($total > 0){
				$num = $total - (($_VAR['page'] - 1) * $boardInfo->page_size);
			}





			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname.'/list_main.php');
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname.'/list_foot.php');
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname.'/list_page.php');
		}else{
			echo "해당 스킨이 존재하지 않습니다.";
			return "";
		}

		return "";
	}

	// 글쓰기 페이지
	public function get_write($bname=""){
		global $wpdb, $currentSessionID;



		$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
		$current_user     = wp_get_current_user();  // 현재 회원의 정보 추출

		if(!empty($_GET['nType'])) $_VAR = bbse_board_parameter_decryption($_GET['nType']);

		$page_id   = get_queried_object_id();
		$prepare   = NULL;
		$prepare   = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE `boardname`=%s",  array( $bname ) );
		$boardInfo = $wpdb->get_row( $prepare );
		$curUrl = get_permalink();

		if(!empty($_GET['page_id'])) $link_add = "&";
		else $link_add = "?";

		if(empty($boardInfo->table_width)) $boardInfo->table_width = "100";
		if($boardInfo->table_width <= 100) $table_width = $boardInfo->table_width."%";
		else $table_width = $boardInfo->table_width."px";

		if(empty($boardInfo->table_align)) $boardInfo->table_align = "L";
		switch($boardInfo->table_align){
			case "C": $table_align = "margin:0 auto;";break;
			case "R": $table_align = "float:right;";break;
			case "L": default: $table_align = "float:left;";break;
		}

		if(empty($boardInfo->no_permission_url)){
			$no_permission_url = BBSE_BOARD_SITE_URL;
		}else{
			$no_permission_url = $boardInfo->no_permission_url;
		}

		if(empty($_VAR['mode']))       $_VAR['mode']       = NULL;
		if(empty($_VAR['search_chk'])) $_VAR['search_chk'] = NULL;
		if(empty($_VAR['keyfield']))   $_VAR['keyfield']   = NULL;
		if(empty($_VAR['keyword']))    $_VAR['keyword']    = NULL;
		if(empty($_VAR['cate']))       $_VAR['cate']       = NULL;
		if(empty($_VAR['no']))         $_VAR['no']         = NULL;
		if(empty($_VAR['ref']))        $_VAR['ref']        = NULL;
		if(empty($_VAR['page']))       $_VAR['page']       = 1;

		if(!empty($boardInfo->l_write)){
			if($boardInfo->l_write == "private"){
				if(!empty($boardInfo->user_list)){
					$private_auth = false;
					$private_auth = bbse_private_user_check($boardInfo, "l_write");
					if($private_auth == false && $curUserPermision != "administrator"){
						echo "
							<script type='text/javascript'>
							alert('게시판 글쓰기 권한이 없습니다.');
							location.href = '".$no_permission_url."';
							</script>";
						return "";
					}
				}
			//}else if(($boardInfo->l_write == "author" && $curUserPermision == "all") || ($boardInfo->l_write == "administrator" && $curUserPermision != "administrator")){
			}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_write) ){
				echo "
					<script type='text/javascript'>
					alert('게시판 글쓰기 권한이 없습니다.');
					location.href = '".$no_permission_url."';
					</script>";
				return "";
			}
		}

		// 수정
		if($_VAR['no'] > 0 && empty($_VAR['ref'])){
			$prepare = NULL;
			$prepare = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`=%d",  array( $_VAR['no'] ) );
			$brdData = $wpdb->get_row( $prepare );
			$tMode   = "modify";

		// 답글
		}else if($_VAR['no'] > 0 && $_VAR['ref'] > 0){
			if(!empty($boardInfo->use_reply) && $boardInfo->use_reply == 1){
				if($boardInfo->l_reply == "private"){
					if(!empty($boardInfo->user_list)){
						$private_auth = false;
						$private_auth = bbse_private_user_check($boardInfo, "l_reply");
						if($private_auth == false && $curUserPermision != "administrator"){
							echo "
								<script type='text/javascript'>
								alert('게시판 답글쓰기 권한이 없습니다.');
								location.href = '".$no_permission_url."';
								</script>";
							return "";
						}
					}
				//}else if(($boardInfo->l_reply == "author" && $curUserPermision == "all") || ($boardInfo->l_reply == "administrator" && $curUserPermision != "administrator")){
				}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_reply) ){
					echo "
						<script type='text/javascript'>
						alert('게시판 답글쓰기 권한이 없습니다.');
						location.href = '".$no_permission_url."';
						</script>";
					return "";
				}

				$prepare = NULL;
				$prepare = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`=%d",  array( $_VAR['no'] ) );
				$brdData = $wpdb->get_row( $prepare );
				$tMode = "reply";
			}else{
				echo "
					<script type='text/javascript'>
					alert('답글쓰기 설정이 사용안함 상태입니다.');
					location.href = '".$no_permission_url."';
					</script>";
				return "";
			}

		// 등록
		}else $tMode = "insert";

		if(!empty($boardInfo->category_list)){
			$category_arr = explode(",", $boardInfo->category_list);

			$select_category = "<select name=\"category\" id=\"category\" onchange=\"if(this.value != '') empty_error();\">";
			$select_category .= "<option value=\"\">카테고리</option>";
			for($i = 0; $i < count($category_arr); $i++){
				$select_category .= "<option value=\"".$category_arr[$i]."\"";

				if(!empty($brdData->category) && ($brdData->category == $category_arr[$i])){
					$select_category .= "selected";
				}

				$select_category .= ">".$category_arr[$i]."</option>";
			}
			$select_category .= "</select> &nbsp;";

		}else $select_category = NULL;

		$prvCnfData = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bbse_board_config`");

		if($tMode == "modify"){
			$writer = $brdData->writer;

			$title = $brdData->title;
			$content = $brdData->content;
			if($brdData->image_file) $view_image = "<p class=\"i_dsc\">".$brdData->image_file." <span style=\"color:#e46c0a;\">[삭제]</span> <input type=\"checkbox\" name=\"image_del\" value=\"1\" /></p>";
			else $view_image = NULL;
			if($brdData->file1) $view_file1 = "<p class=\"i_dsc\">".$brdData->file1." <span style=\"color:#e46c0a;\">[삭제]</span> <input type=\"checkbox\" name=\"file_del1\" value=\"1\" /></p>";
			else $view_file1 = NULL;
			if($brdData->file2) $view_file2 = "<p class=\"i_dsc\">".$brdData->file2." <span style=\"color:#e46c0a;\">[삭제]</span> <input type=\"checkbox\" name=\"file_del2\" value=\"1\" /></p>";
			else $view_file2 = NULL;

		}else if($tMode == "reply"){
			$writer = $current_user->user_login;
			$title = "[Re]".$brdData->title;
			$content = $brdData->content.PHP_EOL.'────────────────────────────────────────────────'.PHP_EOL.PHP_EOL;

		}else{
			$writer = $current_user->user_login;
		}

		/* SSL 처리 */
		$config = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bbse_board_config`");
		if((isset($config->use_ssl) && $config->use_ssl == 1) && !empty($config->ssl_domain)){
			$plugin_path_arr = explode("/", BBSE_BOARD_PLUGIN_ABS_PATH);
			$path_end = substr($config->ssl_domain, -1);
			if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
			else $ssl_domain = $config->ssl_domain;
			$action_url = "https://".$ssl_domain;
			if(empty($config->ssl_port)) $action_url .= ":443/";
			else $action_url .= ":".$config->ssl_port."/";
			$url_arr = explode("/", home_url());
			$url_arr_cnt = count($url_arr);
			if($url_arr_cnt >= 4){
				for($i = 3; $i < $url_arr_cnt; $i++) $action_url .= $url_arr[$i]."/";
			}
			$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
		}else{
			$action_url = BBSE_BOARD_PLUGIN_WEB_URL;
		}

		if(is_dir(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname)){
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'class/auth_txt.php');
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname.'/write.php');
		}else{
			echo "해당 스킨이 존재하지 않습니다.";
			return "";
		}

		return "";
	}

	// 글보기 페이지
	public function get_view($bname=""){
		global $wpdb, $currentSessionID;

		wp_register_script('kakao-link', BBSE_BOARD_PLUGIN_WEB_URL.'js/kakao.link.js', false, '2012');
		wp_enqueue_script('kakao-link');



		$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
		$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

		if(!empty($_GET['nType'])) $_VAR = bbse_board_parameter_decryption($_GET['nType']);

		$page_id   = get_queried_object_id();
		$prepare   = NULL;
		$prepare   = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE `boardname`=%s",  array( $bname ) );
		$boardInfo = $wpdb->get_row( $prepare );
		$curUrl    = get_permalink();

		if(!empty($_GET['page_id'])) $link_add = "&";
		else $link_add = "?";

		if(empty($boardInfo->table_width)) $boardInfo->table_width = "100";
		if($boardInfo->table_width <= 100) $table_width = $boardInfo->table_width."%";
		else $table_width = $boardInfo->table_width."px";

		if(empty($boardInfo->table_align)) $boardInfo->table_align = "L";
		switch($boardInfo->table_align){
			case "C": $table_align = "margin:0 auto;";break;
			case "R": $table_align = "float:right;";break;
			case "L": default: $table_align = "float:left;";break;
		}

		if(empty($boardInfo->no_permission_url)){
			$no_permission_url = BBSE_BOARD_SITE_URL;
		}else{
			$no_permission_url = $boardInfo->no_permission_url;
		}

		if(empty($_VAR['mode']))       $_VAR['mode']       = NULL;
		if(empty($_VAR['search_chk'])) $_VAR['search_chk'] = NULL;
		if(empty($_VAR['keyfield']))   $_VAR['keyfield']   = NULL;
		if(empty($_VAR['keyword']))    $_VAR['keyword']    = NULL;
		if(empty($_VAR['cate']))       $_VAR['cate']       = NULL;
		if(empty($_VAR['no']))         $_VAR['no']         = NULL;
		if(empty($_VAR['ref']))        $_VAR['ref']        = NULL;
		if(empty($_VAR['page']))       $_VAR['page']       = 1;

		/* 권한관련 처리 start */
		// 읽기권한
		if(!empty($boardInfo->l_read)){
			if($boardInfo->l_read == "private"){
				if(!empty($boardInfo->user_list)){
					$private_auth = false;
					$private_auth = bbse_private_user_check($boardInfo, "l_read");
					if($private_auth == false && $curUserPermision != "administrator"){
						echo "
							<script type='text/javascript'>
							alert('게시판 글보기 권한이 없습니다.');
							location.href = '".$no_permission_url."';
							</script>";
						return "";
					}
				}
			//}else if(($boardInfo->l_read == "author" && $curUserPermision == "all") || ($boardInfo->l_read == "administrator" && $curUserPermision != "administrator")){
			}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_read) ){
				echo "
					<script type='text/javascript'>
					alert('게시판 글보기 권한이 없습니다.');
					location.href = '".$no_permission_url."';
					</script>";
				return "";
			}
		}

		// 댓글작성 권한
		$comment_write_check = true;
		if(!empty($boardInfo->l_comment)){
			if($boardInfo->l_comment == "private"){
				if(!empty($boardInfo->user_list)){
					$private_auth = false;
					$private_auth = bbse_private_user_check($boardInfo, "l_comment");
					if($private_auth == false && $curUserPermision != "administrator"){
						$comment_write_check = false;
					}
				}
			//}else if(($boardInfo->l_comment == "author" && $curUserPermision == "all") || ($boardInfo->l_comment == "administrator" && $curUserPermision != "administrator")){
			}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_comment) ){
				$comment_write_check = false;
			}
		}
		/* 권한관련 처리 end */
		$prepare = NULL;
		$prepare = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`=%d",  array( $_VAR['no'] ) );
		$brdData = $wpdb->get_row( $prepare );

		$prepare = NULL;
		$prepare = $wpdb->prepare( "UPDATE `".$wpdb->prefix."bbse_".$bname."_board` SET `hit`=`hit`+1 WHERE `no`=%d",  array( $_VAR['no'] ) );
		$wpdb->query( $prepare );

		if(empty($brdData->no)){
			echo "해당 게시물이 존재하지 않습니다.";
			return "";
		}

		if((!empty($boardInfo->use_secret) && $boardInfo->use_secret == 1) && (!empty($brdData->use_secret) && $brdData->use_secret == 1) && empty($_POST['passcheck'])){
			// 비밀글(관리자가 && 지정사용자가 아닐때)
			$private_auth = false;
			$private_auth = bbse_private_user_check($boardInfo, "l_read");
			if ($private_auth == true && $boardInfo->ext_var1 == 1) $private_auth = true;
			else                                                    $private_auth = false;

			if( (empty($curUserPermision) || $curUserPermision != "administrator") && $private_auth == false ){
				if(!empty($brdData->listnum) && $brdData->listnum == 1){  // 원본글
					if(empty($brdData->memnum) || $brdData->memnum == 0){
						echo "
							<script type='text/javascript'>
							location.href = '".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/pass_check.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."';
							</script>";
						exit;
					}else{
						if($brdData->memnum != $current_user->ID){
							echo "
								<script type='text/javascript'>
								alert('해당글에 대한 보기 권한이 없습니다.');
								location.href = '".$no_permission_url."';
								</script>";
							return "";
						}
					}
				}else{  // 답글
					$prepare = NULL;
					$prepare = $wpdb->prepare( "SELECT `no`, `memnum` FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`=%d",  array( $brdData->ref ) );
					$ref     = $wpdb->get_row( $prepare, ARRAY_N);

					if(empty($ref[0])){
						echo "원본글이 삭제 되었거나 존재하지 않습니다.";
						return "";

					}else{
						if($ref[1] == 0){
							echo "
								<script type='text/javascript'>
								location.href = '".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/pass_check.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."';
								</script>";
							exit;

						}else{
							if($ref[1] != $current_user->ID){
								echo "
									<script type='text/javascript'>
									alert('해당글에 대한 보기 권한이 없습니다.');
									location.href = '".$no_permission_url."';
									</script>";
								return "";
							}
						}
					}
				}
			}
		}

		if(!empty($brdData->write_date)){
			$tmp1 = explode(" ", $brdData->write_date);
			$tmp2 = explode("-", $tmp1[0]);
			$tmp3 = explode(":", $tmp1[1]);
			$unique = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
		}

		if(!empty($brdData->re_level) && $brdData->re_level != 0) $pict = $unique."_re";
		else $pict = $brdData->ref."_";

		if(!empty($brdData->image_file)){
			$oFile = explode(".", $brdData->image_file);

			$imgpath = BBSE_BOARD_UPLOAD_BASE_PATH."bbse-board/".$boardInfo->board_no."_".$pict."image.".end($oFile);
			$imgname = BBSE_BOARD_UPLOAD_BASE_URL."bbse-board/".$boardInfo->board_no."_".$pict."image.".end($oFile);

			if(file_exists($imgpath)){
				$img_size = getimagesize($imgpath);
				$img_width = $img_size[0];
				$img_height = $img_size[1];

				if($img_width >= 550){
					$img_width = 550;
					$width_per = round(550 / $img_size[0], 2);

				}else{
					$img_width = $img_size[0];
					$width_per = 1;
				}
				$img_height = $img_height * $width_per;
				$filesize = floor(filesize($imgpath) / 1024 + 1);
				if(strtolower(end($oFile)) == "jpg" || strtolower(end($oFile)) == 'jpeg' || strtolower(end($oFile)) == 'gif' || strtolower(end($oFile)) == 'bmp' || strtolower(end($oFile)) == 'png'){
					if(!empty($brdData->image_file_alt)) $image_alt = " alt=\"".$brdData->image_file_alt."\"";
					else $image_alt = " alt=\"대표이미지\"";
					$image_file_result = "<p class=\"pgsbox0\"><a href=\"javascript:open_window('img_view', '".BBSE_BOARD_PLUGIN_WEB_URL."show.php?file=".$boardInfo->board_no."_".$pict."image.".end($oFile)."', 0, 0, 100, 100, 0, 0, 0, 0, 0);\" class=\"image_view\"".$image_alt."><img src='".$imgname."' border='0'".$image_alt." /></a></p>";
				}
				$image_download = "<a href=\"".BBSE_BOARD_PLUGIN_WEB_URL."proc/download.php?target=".$boardInfo->board_no."_".$pict."image\">".$brdData->image_file." (file size ".$filesize."KB)</a><br />";
			}else{
				$image_download = $brdData->image_file;
			}
		}

		if(!empty($brdData->file1)){
			$oFile1 = explode(".", $brdData->file1);

			$filepath1 = BBSE_BOARD_UPLOAD_BASE_PATH."bbse-board/".$boardInfo->board_no."_".$pict."1.".end($oFile1);
			$filename1 = BBSE_BOARD_UPLOAD_BASE_URL."bbse-board/".$boardInfo->board_no."_".$pict."1.".end($oFile1);

			if(file_exists($filepath1)){
				$img_size1 = getimagesize($filepath1);
				$img_width1 = $img_size1[0];
				$img_height1 = $img_size1[1];

				if($img_width1 >= 550){
					$img_width1 = 550;
					$width_per1 = round(550 / $img_size1[0], 2);

				}else{
					$img_width1 = $img_size1[0];
					$width_per1 = 1;
				}
				$img_height1 = $img_height1 * $width_per1;
				$filesize1 = floor(filesize($filepath1) / 1024 + 1);
				if(strtolower(end($oFile1)) == "jpg" || strtolower(end($oFile1)) == 'jpeg' || strtolower(end($oFile1)) == 'gif' || strtolower(end($oFile1)) == 'bmp' || strtolower(end($oFile1)) == 'png'){
					$view_file_result1 = "<p class=\"pgsbox0\"><a href=\"javascript:open_window('img_view', '".BBSE_BOARD_PLUGIN_WEB_URL."show.php?file=".$boardInfo->board_no."_".$pict."1.".end($oFile1)."', 0, 0, 100, 100, 0, 0, 0, 0, 0);\" class=\"image_view\"><img src='".$filename1."' border='0' alt='첨부파일1' /></a></p>";
				}
				$file_download1 = "<a href=\"".BBSE_BOARD_PLUGIN_WEB_URL."proc/download.php?target=".$boardInfo->board_no."_".$pict."1\">".$brdData->file1." (file size ".$filesize1."KB)</a><br />";
			}else{
				$file_download1 = $brdData->file1;
			}
		}

		if(!empty($brdData->file2)){
			$oFile2 = explode(".", $brdData->file2);

			$filepath2 = BBSE_BOARD_UPLOAD_BASE_PATH."bbse-board/".$boardInfo->board_no."_".$pict."2.".end($oFile2);
			$filename2 = BBSE_BOARD_UPLOAD_BASE_URL."bbse-board/".$boardInfo->board_no."_".$pict."2.".end($oFile2);

			if(file_exists($filepath2)){
				$img_size2 = getimagesize($filepath2);
				$img_width2 = $img_size2[0];
				$img_height2 = $img_size2[1];

				if($img_width2 >= 550){
					$img_width2 = 550;
					$width_per2 = round(550 / $img_size2[0], 2);

				}else{
					$img_width2 = $img_size2[0];
					$width_per2 = 1;
				}
				$img_height2 = $img_height2 * $width_per2;
				$filesize2 = floor(filesize($filepath2) / 1024 + 1);
				if(strtolower(end($oFile2)) == "jpg" || strtolower(end($oFile2)) == 'jpeg' || strtolower(end($oFile2)) == 'gif' || strtolower(end($oFile2)) == 'bmp' || strtolower(end($oFile2)) == 'png'){
					$view_file_result2 = "<p class=\"pgsbox0\"><a href=\"javascript:open_window('img_view', '".BBSE_BOARD_PLUGIN_WEB_URL."show.php?file=".$boardInfo->board_no."_".$pict."2.".end($oFile2)."', 0, 0, 100, 100, 0, 0, 0, 0, 0);\" class=\"image_view\"><img src='".$filename2."' border='0' alt='첨부파일2' /></a></p>";
				}
				$file_download2 = "<a href=\"".BBSE_BOARD_PLUGIN_WEB_URL."proc/download.php?target=".$boardInfo->board_no."_".$pict."2\">".$brdData->file2." (file size ".$filesize2."KB)</a><br />";
			}else{
				$file_download2 = $brdData->file2;
			}
		}

		$bbse_board_kakao_app_key = get_option("bbse_board_kakao_app_key");
		if(!empty($bbse_board_kakao_app_key)) $kakao_app_key = $bbse_board_kakao_app_key;
		else $kakao_app_key = NULL;

		$share_title    = str_replace(array("\r\n", "\r", "\n"), " ", strip_tags(addslashes($brdData->title)));
		$share_content  = str_replace(array("\r\n", "\r", "\n"), " ", strip_tags(addslashes($brdData->content)));
		$share_imageurl = NULL;
		if(!empty($filename1)){
			if(strtolower(end($oFile1)) == "jpg" || strtolower(end($oFile1)) == 'jpeg' || strtolower(end($oFile1)) == 'gif' || strtolower(end($oFile1)) == 'bmp' || strtolower(end($oFile1)) == 'png'){
				$share_imageurl = $filename1;
			}
		}
		if(!empty($filename2) && empty($share_imageurl)){
			if(strtolower(end($oFile2)) == "jpg" || strtolower(end($oFile2)) == 'jpeg' || strtolower(end($oFile2)) == 'gif' || strtolower(end($oFile2)) == 'bmp' || strtolower(end($oFile2)) == 'png'){
				$share_imageurl = $filename2;
			}
		}

		$content = $brdData->content;

		if($brdData->use_html == 0){
			$content = nl2br(htmlentities($content, ENT_QUOTES, "utf-8"));
		}else if($brdData->use_html == 1){
		}else if($brdData->use_html == 2){
			//$content = nl2br($content);
			$content = $content;
		}

		/**************************
		 * 링크 정의 start
		 *************************/
		# 수정 버튼
		if($curUserPermision == "administrator" || $brdData->memnum == $current_user->ID || $brdData->memnum == 0){
			$view_modify = "<a class=\"btn_big\" href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'write', $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">";
		}else{
			$view_modify = '<a style="display:none;">';
		}

		# 삭제 버튼
		if(!empty($curUserPermision) && $curUserPermision == "administrator"){
			$view_delete = "<iframe name=\"delete_hidden\" id=\"delete_hidden\" style=\"display:none;\"></iframe><a class=\"btn_big\" href=\"".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/delete_view.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\" target=\"delete_hidden\">";
		}else{
			// 작성자와 접속회원이 일치
			if((!empty($brdData->memnum) && $brdData->memnum != 0) && $brdData->memnum == $current_user->ID){
				$view_delete = "<iframe name=\"delete_hidden\" id=\"delete_hidden\" style=\"display:none;\"></iframe><a class=\"btn_big\" href=\"".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/delete_view.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\" target=\"delete_hidden\">";
			}else if((!empty($brdData->memnum) && $brdData->memnum != 0) && $brdData->memnum != $current_user->ID){
				$view_delete = '<a style="display:none;">';
			}

			// 비회원 작성글
			if($brdData->memnum == 0){
				$view_delete = "<a class=\"btn_big\" href=\"".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$boardInfo->skinname."/delete_view.php?page_id=".$page_id."&nType=".bbse_board_parameter_encryption($bname, $_VAR['mode'], $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">";
			}
		}

		# 답글 버튼
		if(!empty($boardInfo->use_reply) && $boardInfo->use_reply == 1){  // 답글 기능 사용함
			// 관리자
			if(!empty($curUserPermision) && $curUserPermision == "administrator"){
				$view_reply = "<a class=\"btn_big\" href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'write', $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'], $brdData->ref)."\">";

			// 회원 또는 비회원
			}else{
				if($boardInfo->l_reply == "private"){
					if(!empty($boardInfo->user_list)){
						$private_auth = false;
						$private_auth = bbse_private_user_check($boardInfo, "l_reply");
						if($private_auth == false){
							$view_reply = '<a style="display:none;">';
						}else{
							$view_reply = "<a class=\"btn_big\" href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'write', $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'], $brdData->ref)."\">";
						}
					}
				//}else if(($boardInfo->l_reply == "author" && $curUserPermision == "all") || $boardInfo->l_reply == "administrator"){
				}else if( bbse_check_user_level() < bbse_role2level($boardInfo->l_reply) ){
					$view_reply = '<a style="display:none;">';
				}else{
					$view_reply = "<a class=\"btn_big\" href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'write', $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'], $brdData->ref)."\">";
				}

				// 공지사항은 답글불가
				if(!empty($brdData->use_notice) && $brdData->use_notice == 1){
					$view_reply = '<a style="display:none;">';
				}
			}
		}else{
			$view_reply = '<a style="display:none;">';
		}

		# 글쓰기 버튼
		$view_write = "<a class=\"btn_big\" href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'write', '', $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">";

		if(!empty($curUserPermision) && $curUserPermision != 'all') $vmnum = $current_user->ID;
		else $vmnum = "0";

		# 목록 버튼
		$view_list = "<a class=\"btn_big\" href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'list', '', $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">";

		// 일반글
		if(empty($brdData->use_notice) || $brdData->use_notice == 0){
			$sql_common = NULL;
			if(!empty($_VAR['search_chk']) && $_VAR['search_chk'] == 1){
				$sql_common .= " AND `".$_VAR['keyfield']."` like '%".esc_sql($_VAR['keyword'])."%'";
			}

			if(!empty($boardInfo->use_category) && $boardInfo->use_category == 1){
				if(!empty($_VAR['cate'])) {
					$sql_common .= " AND `category`='".esc_sql($_VAR['cate'])."'";
				}
			}

			$maxnum  = $wpdb->get_var("SELECT MAX(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=0 AND `listnum`=1 AND `movecheck`!='0'".$sql_common);
			$minnum  = $wpdb->get_var("SELECT MIN(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=0 AND `listnum`=1 AND `movecheck`!='0'".$sql_common);
			$nextnum = $wpdb->get_var("SELECT MIN(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=0 AND `listnum`=1 AND `movecheck`!='0' AND `no`>".$brdData->ref.$sql_common);
			$prevnum = $wpdb->get_var("SELECT MAX(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=0 AND `listnum`=1 AND `movecheck`!='0' AND `no`<".$brdData->ref.$sql_common);
			$next    = $wpdb->get_row("SELECT `title`, `write_date` FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`='".$nextnum."'");
			$prev    = $wpdb->get_row("SELECT `title`, `write_date` FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`='".$prevnum."'");

			// 다음글 링크
			if($brdData->ref >= $maxnum){
				$nextlink = "다음글이 없습니다.";
				$next_wdate = NULL;
			}else{
				$nextlink = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'view', $nextnum, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">".$next->title."</a>";
				$next_wdate = $next->write_date;
			}

			// 이전글 링크
			if($brdData->ref <= $minnum){
				$prevlink = "이전글이 없습니다.";
				$prev_wdate = NULL;
			}else{
				$prevlink = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'view', $prevnum, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">".$prev->title."</a>";
				$prev_wdate = $prev->write_date;
			}

		// 공지사항
		}else if(!empty($brdData->use_notice) && $brdData->use_notice == 1){
			$maxnum  = $wpdb->get_var("SELECT MAX(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=1");
			$minnum  = $wpdb->get_var("SELECT MIN(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=1");
			$nextnum = $wpdb->get_var("SELECT MIN(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=1 AND `no`>".$_VAR['no']);
			$prevnum = $wpdb->get_var("SELECT MAX(`no`) FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `use_notice`=1 AND `no`<".$_VAR['no']);
			$next    = $wpdb->get_row("SELECT `title`, `write_date` FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`='".$nextnum."'");
			$prev    = $wpdb->get_row("SELECT `title`, `write_date` FROM `".$wpdb->prefix."bbse_".$bname."_board` WHERE `no`='".$prevnum."'");

			// 다음글 링크
			if($_VAR['no'] == $maxnum){
				$nextlink = "다음글이 없습니다.";
				$next_wdate = NULL;
			}else{
				$nextlink = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'view', $nextnum, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">".$next->title."</a>";
				$next_wdate = $next->write_date;
			}

			// 이전글 링크
			if($_VAR['no'] == $minnum){
				$prevlink = "이전글이 없습니다.";
				$prev_wdate = NULL;
			}else{
				$prevlink = "<a href=\"".$curUrl.$link_add."nType=".bbse_board_parameter_encryption($bname, 'view', $prevnum, $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate'])."\">".$prev->title."</a>";
				$prev_wdate = $prev->write_date;
			}
		}
		/**************************
		 * 링크 정의 end
		 *************************/

		/**************************
		 * 코멘트 관련 start
		 *************************/
		if(!empty($boardInfo->use_comment) && $boardInfo->use_comment == 1){
			$option = " WHERE `parent`='".$_VAR['no']."'";

			$comment_result = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."bbse_".$bname."_comment`".$option." ORDER BY `comm_parent` ASC, `depth` ASC, `no` ASC");
			$comment_cnt    = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."bbse_".$bname."_comment`".$option);
		}

		if(empty($comment_cnt) || $comment_cnt == 0){
			$comment_list = NULL;

		}else{
			$comment_list = "<div class='comment_list_box'>";
			$comment_list .= "<ul id='comment_list' class='comment_list'>";

			foreach($comment_result as $i => $list){
				// 대댓글 개수 확인
				$reply_cnt = $wpdb->get_var("SELECT COUNT(*) FROM `".$wpdb->prefix."bbse_".$bname."_comment` WHERE `comm_parent`='".$list->no."' AND `depth`>0");

				/* 댓글 삭제 */
				if(!empty($reply_cnt) && $reply_cnt > 0){
					$comment_del_link = "<a href=\"javascript:;\" onclick=\"comment_fail(jQuery(this), 'child');\">";

				}else{
					if($curUserPermision == "administrator"){
						$comment_del_link = "<a href=\"javascript:;\" onclick=\"comment_user_delete_check('".BBSE_BOARD_PLUGIN_WEB_URL."proc/delete_comment.exec.php', '".$list->no."', jQuery(this));\">";
					}else{
						// 비회원 작성 댓글
						if(empty($list->memnum) || $list->memnum == 0){
							$comment_del_link = "<a href=\"javascript:;\" onclick=\"pass_confirm(jQuery(this), '".$list->no."', 'delete');\">";
						// 회원 작성 댓글
						}else{
							// 작성자 본인
							if($list->memnum == $current_user->ID){
								$comment_del_link = "<a href=\"javascript:;\" onclick=\"comment_user_delete_check('".BBSE_BOARD_PLUGIN_WEB_URL."proc/delete_comment.exec.php', '".$list->no."', jQuery(this));\">";
							// 작성자 불일치
							}else{
								$comment_del_link = "<a href=\"javascript:;\" onclick=\"comment_fail(jQuery(this), 'delete');\">";
							}
						}
					}
				}

				/* 댓글 수정 */
				if($curUserPermision == "administrator"){
					$comment_edit_link = "<a href=\"javascript:;\" onclick=\"comment_edit1('".$list->no."', jQuery(this));\">";
				}else{
					// 비회원 작성 댓글
					if(empty($list->memnum) || $list->memnum == 0){
						$comment_edit_link = "<a href=\"javascript:;\" id=\"edit_a".$list->no."\" onclick=\"pass_confirm(jQuery(this), '".$list->no."', 'edit');\">";
					// 회원 작성 댓글
					}else{
						// 작성자 본인
						if($list->memnum == $current_user->ID){
							$comment_edit_link = "<a href=\"javascript:;\" onclick=\"comment_edit1('".$list->no."', jQuery(this));\">";
						// 작성자 불일치
						}else{
							$comment_edit_link = "<a href=\"javascript:;\" onclick=\"comment_fail(jQuery(this), 'edit');\">";
						}
					}
				}

				/* 상태별 댓글버튼 처리 */
				if(empty($list->depth) || $list->depth == 0){
					$comment_reply_link = "<a href=\"javascript:;\" onclick=\"comment_reply('".$list->no."', jQuery(this));\">";
					$add_cls = NULL;
				}else if($list->depth >= 1){
					$comment_reply_link = "<a href=\"javascript:;\" onclick=\"comment_fail(jQuery(this), 'reply');\">";
					$add_cls = " reply";
				}

				//$c_content = str_replace(chr(13), "<br />", $list->content);
				$c_content = nl2br($list->content);

				$comment_list .= "<li class='comm".$add_cls."'>";
				$comment_list .=	"<p class='name'>".$list->writer." <span class='date'>".$list->write_date."</span></p>";
				$comment_list .= "<p class='con'>".$c_content."</p>";

				$comment_list .= "<p class='opm'>";
				if($comment_write_check == true){
					$comment_list .= $comment_edit_link."수정</a> | ";
				}
				$comment_list .= $comment_del_link."삭제</a>";
				if($comment_write_check == true){
					$comment_list .= " | ".$comment_reply_link."댓글</a>";
				}
				$comment_list .= "</p>";
				$comment_list .= "</li>";
			}

			$comment_list .= "</ul>";
			$comment_list .= "</div>";
		}

		$cert_time   = current_time('timestamp');
		$cert_encode = base64_encode(base64_encode($cert_time)."\n");
		$cert        = "<input type='hidden' name='cert' value='$cert_encode' />";
		/**************************
		 * 코멘트 관련 end
		 *************************/

		/* SSL 처리 */
		$config = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bbse_board_config`");
		if(isset($config) &&  $config->use_ssl == 1 && !empty($config->ssl_domain)){
			$plugin_path_arr = explode("/", BBSE_BOARD_PLUGIN_ABS_PATH);
			$path_end = substr($config->ssl_domain, -1);
			if($path_end == "/") $ssl_domain = substr($config->ssl_domain, 0, -1);
			else $ssl_domain = $config->ssl_domain;
			$action_url = "https://".$ssl_domain;
			if(empty($config->ssl_port)) $action_url .= ":443/";
			else $action_url .= ":".$config->ssl_port."/";
			$url_arr     = explode("/", home_url());
			$url_arr_cnt = count($url_arr);
			if($url_arr_cnt >= 4){
				for($i = 3; $i < $url_arr_cnt; $i++) $action_url .= $url_arr[$i]."/";
			}
			$action_url .= $plugin_path_arr[count($plugin_path_arr) - 4]."/".$plugin_path_arr[count($plugin_path_arr) - 3]."/".$plugin_path_arr[count($plugin_path_arr) - 2]."/";
		}else{
			$action_url = BBSE_BOARD_PLUGIN_WEB_URL;
		}

		if(is_dir(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname)){
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH."class/auth_txt.php");
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH."class/auth_txt_sub.php");
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname."/view_main.php");
			if(!empty($boardInfo->use_comment) && $boardInfo->use_comment == 1){
				require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname."/comment.php");
			}
			require_once(BBSE_BOARD_PLUGIN_ABS_PATH.'skin/'.$boardInfo->skinname."/view_foot.php");
		}else{
			echo "해당 스킨이 존재하지 않습니다.";
			return "";
		}

		return "";
	}

	// SNS 공유 meta 태그 추가
	public function sns_meta_add(){
		global $wpdb;
		$curUrl = get_permalink();
		$imgfile_type = array("jpg", "gif", "png");

		if(!empty($_GET['nType'])){
			$_VAR = bbse_board_parameter_decryption($_GET['nType']);

			if(empty($_VAR['mode']))       $_VAR['mode']       = NULL;
			if(empty($_VAR['search_chk'])) $_VAR['search_chk'] = NULL;
			if(empty($_VAR['keyfield']))   $_VAR['keyfield']   = NULL;
			if(empty($_VAR['keyword']))    $_VAR['keyword']    = NULL;
			if(empty($_VAR['cate']))       $_VAR['cate']       = NULL;
			if(empty($_VAR['no']))         $_VAR['no']         = NULL;
			if(empty($_VAR['ref']))        $_VAR['ref']        = NULL;
			if(empty($_VAR['page']))       $_VAR['page']       = 1;

			if(!empty($_GET['page_id'])) $link_add = "&";
			else $link_add = "?";

			// 글보기 facebook 공유하기 관련 메타태그 추가
			if(!empty($_VAR['bname']) && !empty($_VAR['no'])){
				$share_link = $curUrl.$link_add."nType=".bbse_board_parameter_encryption($_VAR['bname'], 'view', $_VAR['no'], $_VAR['page'], $_VAR['keyfield'], $_VAR['keyword'], $_VAR['search_chk'], $_VAR['cate']);

				$prepare   = NULL;
				$prepare   = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_board` WHERE `boardname`=%s",  array( $_VAR['bname'] ) );
				$boardInfo = $wpdb->get_row( $prepare );

				$prepare = NULL;
				$prepare = $wpdb->prepare( "SELECT * FROM `".$wpdb->prefix."bbse_".$_VAR['bname']."_board` WHERE `no`=%d",  array( $_VAR['no'] ) );
				$rows    = $wpdb->get_row( $prepare );

				if(!empty($rows->write_date)){
					$tmp1   = explode(" ", $rows->write_date);
					$tmp2   = explode("-", $tmp1[0]);
					$tmp3   = explode(":", $tmp1[1]);
					$unique = mktime($tmp3[0], $tmp3[1], $tmp3[2], $tmp2[1], $tmp2[2], $tmp2[0]);
				}

				if(!empty($rows->re_level) && $rows->re_level != 0) $pict = $unique."_re";
				else $pict = $rows->ref."_";

				$output = "<meta property=\"og:type\" content=\"website\" />\n";
				if(!empty($rows->title)){
					$title = str_replace(array("\r\n", "\r", "\n"), "", strip_tags($rows->title));
					$title = cut_text($title, 30);
					$output .= "<meta property=\"og:title\" content=\"".$title."\" />\n";
				}

				if(!empty($boardInfo->formtype) && ($boardInfo->formtype == 2 || $boardInfo->formtype == 3)){
					if(!empty($rows->image_file)){
						$file_type = end(explode(".", $rows->image_file));
						if(in_array($file_type, $imgfile_type)){
							$output .= "<meta property=\"og:image\" content=\"".BBSE_BOARD_UPLOAD_BASE_URL."bbse-board/".$boardInfo->board_no."_".$pict."image.".$file_type."\" />\n";
						}
					}
				}else if(!empty($boardInfo->formtype) && $boardInfo->formtype == 1){
					if(!empty($rows->file1)){
						$file_type = end(explode(".", $rows->file1));
						if(in_array($file_type, $imgfile_type)){
							$output .= "<meta property=\"og:image\" content=\"".BBSE_BOARD_UPLOAD_BASE_URL."bbse-board/".$boardInfo->board_no."_".$pict."1.".$file_type."\" />\n";
						}
					}else{
						if(!empty($rows->file2)){
							$file_type = end(explode(".", $rows->file2));
							if(in_array($file_type, $imgfile_type)){
								$output .= "<meta property=\"og:image\" content=\"".BBSE_BOARD_UPLOAD_BASE_URL."bbse-board/".$boardInfo->board_no."_".$pict."2.".$file_type."\" />\n";
							}
						}
					}
				}

				if(!empty($rows->content)){
					$description = str_replace(array("\r\n", "\r", "\n"), " ", strip_tags($rows->content));
					$description = cut_text($description, 60);
					$output .= "<meta property=\"og:description\" content=\"".$description."\" /> \n";
				}
				if(!empty($share_link)){
					$output .= "<meta property=\"og:url\" content=\"".$share_link."\" /> \n";
				}
				echo $output;
			}
		}
		return "";
	}
}
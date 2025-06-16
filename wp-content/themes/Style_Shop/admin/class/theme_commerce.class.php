<?php
class BBSeThemeCommerce {
	var $row;
	var $total;
	var $orderby;
	var $order;

	//회원 목록
	public function getMemberList($val1="", $val2="", $qs="", $start_pos="", $per_page=""){
		global $wpdb;
		if(!empty($val1)) $this->orderby = $val1;
		else $this->orderby = "user_no";
		if(!empty($val2)) $this->order = $val2;
		else $this->order = "desc";
		if(!empty($qs)){
			$where = $qs;
		}else $where = "";
		if(isset($start_pos) && !empty($per_page) && empty($qs)){
			$sql = $wpdb->prepare("select * from bbse_commerce_membership where 1 ".$where." order by ".$this->orderby." ".$this->order." limit %d, %d", $start_pos, $per_page);
			$this->row = $wpdb->get_results($sql);
		}else{
			$this->row = $wpdb->get_results("select * from bbse_commerce_membership where 1 ".$where." order by ".$this->orderby." ".$this->order);
		}
		return $this->row;
	}
	// 회원 전체수
	public function getMemberCount($qs){
		global $wpdb;
		if($qs) $addQuery = " WHERE 1 ".$qs;
		$this->total = $wpdb->get_var("select count(*) from bbse_commerce_membership".$addQuery);
		return $this->total;
	}
	// 회원 상세정보
	public function getMemberView($no){
		global $wpdb;
		if(empty($no)) return;
		$this->row = $wpdb->get_row("select * from bbse_commerce_membership where user_no='".$no."'");
		return $this->row;
	}


	// 로그인 페이지
	public function loginPage($skin=""){
		global $current_user, $wpdb;
		if(empty($skin)) $skin = "basic";

		$config = $wpdb->get_row("select * from bbse_commerce_membership_config");
		$page_setting = array();
		$page_setting['login_page'] = get_option("bbse_commerce_login_page");
		$page_setting['id_search_page'] = get_option("bbse_commerce_id_search_page");
		$page_setting['join_page'] = get_option("bbse_commerce_join_page");
		$page_setting['pass_search_page'] = get_option("bbse_commerce_pass_search_page");
		$page_setting['delete_page'] = get_option("bbse_commerce_delete_page");		

		$referer = wp_get_referer();
		$parseURL = parse_url($referer);
		$urlQuery = $parseURL['query'];
		parse_str($urlQuery, $output);
		if($output['bbseGoods'] > 0 && $_POST['tMode']=="addCart") {
			$goods_info = base64_encode(serialize($_POST));
			$_GET['redirect_to'] = "/?bbsePage=order";
		}else if($output['bbseGoods'] > 0 && $_POST['tMode']!="addCart") {
			$_GET['redirect_to'] = $referer;
		}
		// 로그인 되어있을때..
		if(is_user_logged_in() && !$output['bbseGoods']){
			//$this->userLogout();
			echo "<script>location.href='".home_url()."';</script>";
			exit;
		}

		if(is_file(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-login.php")){
			$curUrl = BBSE_COMMERCE_SITE_URL;
			/* SSL 처리 */
			$use_ssl = get_option("bbse_commerce_ssl_enable");
			$ssl_domain = get_option("bbse_commerce_ssl_domain");
			$ssl_port = get_option("bbse_commerce_ssl_port");
			if($use_ssl == "U" && !empty($ssl_domain)){
				$parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
				$action_url = "https://".$ssl_domain;
				if(!empty($ssl_port)) $action_url .= ":".$ssl_port;
				$guest_action_url = $action_url;
				$action_url .= $parseurl['path'];
			}else{
				$guest_action_url = BBSE_COMMERCE_SITE_URL;
				$action_url = BBSE_COMMERCE_THEME_WEB_URL;
			}

			require_once(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-login.php");
		}else{
			echo "스킨 파일이 존재하지 않습니다.";
			return "";
		}
		return "";
	}

	// 로그아웃
	public function userLogout(){
		if(is_user_logged_in()){
			$redirect_url = BBSE_COMMERCE_SITE_URL;
	
			$logout = "<script type='text/javascript'>location.href = '".str_replace("&amp;", "&", wp_logout_url($redirect_url))."';</script>";
			echo $logout;
		}
		return "";
	}

	// 회원가입 페이지
	public function joinPage($skin=""){
		global $wpdb, $current_user;
		if(empty($skin)) $skin = "basic";
		$config = $wpdb->get_row("select * from bbse_commerce_membership_config limit 1");
		$page_setting = array();
		$page_setting['login_page'] = get_option("bbse_commerce_login_page");
		$page_setting['id_search_page'] = get_option("bbse_commerce_id_search_page");
		$page_setting['join_page'] = get_option("bbse_commerce_join_page");
		$page_setting['pass_search_page'] = get_option("bbse_commerce_pass_search_page");
		$page_setting['delete_page'] = get_option("bbse_commerce_delete_page");
		if(!empty($current_user->user_login)){
			$rows = $wpdb->get_row("select * from bbse_commerce_membership where user_id='".$current_user->user_login."'");
			if(empty($rows->user_id)){
				echo "일반회원이 아닙니다.";
				return "";
			}
		}
	
		if(is_file(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-join.php")){
			$curUrl = BBSE_COMMERCE_SITE_URL;
			/* SSL 처리 */
			$use_ssl = get_option("bbse_commerce_ssl_enable");
			$ssl_domain = get_option("bbse_commerce_ssl_domain");
			$ssl_port = get_option("bbse_commerce_ssl_port");
			if($use_ssl == "U" && !empty($ssl_domain)){
				$parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
				$action_url = "https://".$ssl_domain;
				if(!empty($ssl_port)) $action_url .= ":".$ssl_port;
				$action_url .= $parseurl['path'];
			}else{
				$action_url = BBSE_COMMERCE_THEME_WEB_URL;
			}
			if(empty($current_user->user_login)){
				require_once(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-join.php");
			}else{
				//require_once(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-modify.php");
				require_once(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-join.php");
			}
		}else{
			echo "스킨 파일이 존재하지 않습니다.";
			return "";
		}
		return "";
	}

	// 회원탈퇴 페이지
	public function deletePage($skin=""){
		global $current_user, $wpdb;
		if(empty($skin)) $skin = "basic";
		$config = $wpdb->get_row("select * from bbse_commerce_membership_config");

		if(!empty($current_user->user_login)){
			$rows = $wpdb->get_row("select * from bbse_commerce_membership where user_id='".$current_user->user_login."'");
			if(empty($rows->user_id)){
				echo "일반회원이 아닙니다.";
				return "";
			}
		}else{
			echo "로그인 후 이용해주세요.";
			return "";
		}

		if(is_file(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-leave.php")){
			$curUrl = BBSE_COMMERCE_SITE_URL;
			/* SSL 처리 */
			$use_ssl = get_option("bbse_commerce_ssl_enable");
			$ssl_domain = get_option("bbse_commerce_ssl_domain");
			$ssl_port = get_option("bbse_commerce_ssl_port");
			if($use_ssl == "U" && !empty($ssl_domain)){
				$parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
				$action_url = "https://".$ssl_domain;
				if(!empty($ssl_port)) $action_url .= ":".$ssl_port;
				$action_url .= $parseurl['path'];
			}else{
				$action_url = BBSE_COMMERCE_THEME_WEB_URL;
			}
			require_once(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-leave.php");
		}else{
			echo "스킨 파일이 존재하지 않습니다.";
			return "";
		}
		return "";
	}

	// 아이디 찾기
	public function idSearch($skin=""){
		global $current_user, $wpdb;
		if(empty($skin)) $skin = "basic";

		if(is_user_logged_in()){
			echo "로그인 상태입니다.";
			return "";
		}
		$config = $wpdb->get_row("select * from bbse_commerce_membership_config");

		if(is_file(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-find_id.php")){
			$curUrl = BBSE_COMMERCE_SITE_URL;
			/* SSL 처리 */
			$use_ssl = get_option("bbse_commerce_ssl_enable");
			$ssl_domain = get_option("bbse_commerce_ssl_domain");
			$ssl_port = get_option("bbse_commerce_ssl_port");
			if($use_ssl == "U" && !empty($ssl_domain)){
				$parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
				$action_url = "https://".$ssl_domain;
				if(!empty($ssl_port)) $action_url .= ":".$ssl_port;
				$action_url .= $parseurl['path'];
			}else{
				$action_url = BBSE_COMMERCE_THEME_WEB_URL;
			}
			require_once(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-find_id.php");
		}else{
			echo "스킨 파일이 존재하지 않습니다.";
			return "";
		}
		return "";
	}

	// 비밀번호 찾기
	public function passSearch($skin=""){
		global $current_user, $wpdb;
		if(empty($skin)) $skin = "basic";

		if(is_user_logged_in()){
			echo "로그인 상태입니다.";
			return "";
		}
		$config = $wpdb->get_row("select * from bbse_commerce_membership_config");

		if(is_file(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-find_pw.php")){
			$curUrl = BBSE_COMMERCE_SITE_URL;
			/* SSL 처리 */
			$use_ssl = get_option("bbse_commerce_ssl_enable");
			$ssl_domain = get_option("bbse_commerce_ssl_domain");
			$ssl_port = get_option("bbse_commerce_ssl_port");
			if($use_ssl == "U" && !empty($ssl_domain)){
				$parseurl = parse_url(BBSE_COMMERCE_THEME_WEB_URL);
				$action_url = "https://".$ssl_domain;
				if(!empty($ssl_port)) $action_url .= ":".$ssl_port;
				$action_url .= $parseurl['path'];
			}else{
				$action_url = BBSE_COMMERCE_THEME_WEB_URL;
			}
			require_once(BBSE_COMMERCE_THEME_ABS_PATH."/skin/".$skin."/member-find_pw.php");
		}else{
			echo "스킨 파일이 존재하지 않습니다.";
			return "";
		}
		return "";
	}

}

//문자 체크(Ascii) 
class strCheck{
	public $str;
	public $len = 0;
	
	public function init($s){
		if(!empty($s)){
			$this->str = trim($s);
			$this->len = strlen($s);
		}
	}
	
	# null 값인지 체크한다 [ 널값이면 : true / 아니면 : false ]
	public function isNull(){
		$result = false;
		$asciiNumber = Ord($this->str);
		if(empty($asciiNumber)) return true;
	return $result;
	}
	

	# 문자와 문자사이 공백이 있는지 체크 [ 공백 있으면 : true / 없으면 : false ]
	public function isSpace(){
		$result = false;
		$str_split	= split("[[:space:]]+",$this->str);
		$count = count($str_split);	
		for($i=0; $i<$count; $i++){
			if($i>0){
				$result = true;
				break;
			}
		}
	return $result;
	}
	
	# 연속적으로 똑같은 문자는 입력할 수 없다  [ 반복문자 max 이상이면 : true / 아니면 : false ]
	# ex : 010-111-1111,010-222-1111 형태제한
	# max = 3; // 반복문자 3개 "초과" 입력제한
	public function isSameRepeatString($max=3){
		$result = false;
		$sameCount = 0;
		$preAsciiNumber = 0;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if( ($preAsciiNumber == $asciiNumber) && ($preAsciiNumber>0) )
				$sameCount += 1;
			else
				$preAsciiNumber = $asciiNumber;
				
			if($sameCount==$max){
				$result = true;
				break;
			}
		}		
	return $result;
	}
	
	# 숫자인지 체크 [ 숫자면 : true / 아니면 : false ]
	# Ascii table = 48 ~ 57
	public function isNumber(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<47 || $asciiNumber>57){
				$result = false;
				break;
			}
		}
	return $result;
	}

	# 영문인지 체크 [ 영문이면 : true / 아니면 : false ]
	# Ascii table = 대문자[75~90], 소문자[97~122]
	public function isAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if(($asciiNumber>64 && $asciiNumber<91) || ($asciiNumber>96 && $asciiNumber<123)){}
			else{ $result = false; }
		}
	return $result;
	}

	# 영문이 대문자 인지체크 [ 대문자이면 : true / 아니면 : false ]
	# Ascii table = 대문자[75~90]
	public function isUpAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<65 || $asciiNumber>90){
				$result = false;
				break;
			}
		}
	return $result;
	}

	# 영문이 소문자 인지체크 [ 소문자면 : true / 아니면 : false ]
	# Ascii table = 소문자[97~122]
	public function isLowAlphabet(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<97 || $asciiNumber>122){
				$result = false;
				break;
			}
		}
	return $result;
	}
	
	# 한글인지 체크한다 [ 한글이면 : true / 아니면 : false ]
	# Ascii table = 128 > 
	public function isKorean(){
		$result = true;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<128){
				$result = false;
				break;
			}
		}
	return $result;
	}
	
	# 특수문자 입력여부 체크 [ 특수문자 찾으면 : true / 못찾으면 : false ]
	# allow = "-,_"; 허용시킬 
	# space 공백은 자동 제외
	public function isEtcString($allow){
		# 허용된 특수문자 키
		$allowArgs = array();
		$tmpArgs = (!empty($allow)) ? explode(',',$allow) : '';
		if(is_array($tmpArgs)){
			foreach($tmpArgs as $k => $v){
				$knumber = Ord($v);
				$allowArgs['s'.$knumber] = $v;
			}
		}
		
		$result = false;
		for($i=0; $i<$this->len; $i++){
			$asciiNumber = Ord($this->str[$i]);
			if(array_key_exists('s'.$asciiNumber, $allowArgs) === false){
				if( ($asciiNumber<48) && ($asciiNumber != 32) ){ $result = true; break; }
				else if($asciiNumber>57 && $asciiNumber<65){ $result = true; break; }
				else if($asciiNumber>90 && $asciiNumber<97){ $result = true; break; }
				else if($asciiNumber>122 && $asciiNumber<128){ $result = true; break; }
			}
		}
	return $result;
	}
	
	# 첫번째 문자가 영문인지 체크한다[ 찾으면 : true / 못찾으면 : false ]
	public function isFirstAlphabet(){
		$result = true;
		$asciiNumber = Ord($this->str[0]);
		if(($asciiNumber>64 && $asciiNumber<91) || ($asciiNumber>96 && $asciiNumber<123)){}
		else{ $result = false; }
	return $result;
	}
	
	# 문자길이 체크 한글/영문/숫자/특수문자/공백 전부포함
	# min : 최소길이 / max : 최대길이
	public function isStringLength($min,$max){
		$strCount = 0;
		for($i=0;$i<$this->len;$i++){
			$asciiNumber = Ord($this->str[$i]);
			if($asciiNumber<=127 && $asciiNumber>=0){ $strCount++; } 
			else if($asciiNumber<=223 && $asciiNumber>=194){ $strCount++; $i+1; }
			else if($asciiNumber<=239 && $asciiNumber>=224){ $strCount++; $i+2; }
			else if($asciiNumber<=244 && $asciiNumber>=240){ $strCount++; $i+3; }
		}
		
		if($strCount<$min) return false;
		else if($strCount>$max) return false;
		else return true;
	}
	
	# 두 문자가 서로 같은지 비교
	public function equals($s){
		$result = true;
		if(is_string($eStr)){ # 문자인지 체크
			if(strcmp($this->str, $s)) $result= false;
		}else{
			if($this->str != $s ) $result = false;
		}
	return $result;
	}
}

?>
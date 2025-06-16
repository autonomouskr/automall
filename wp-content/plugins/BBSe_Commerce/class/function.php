<?php
class BBSeCommerceMember {
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

		//if($start_pos!="" && $per_page!=""){

			/**** modify : 2015.02.25 (V.1.1.2 => V.1.1.3) : search error ****
			$sql = $wpdb->prepare("select * from bbse_commerce_membership where 1 ".$where." order by ".$this->orderby." ".$this->order." limit %d, %d", $start_pos, $per_page); // 
			$this->row = $wpdb->get_results($sql);
			**** modify : 2015.02.25 (V.1.1.2 => V.1.1.3) : search error ****/

			$this->row = $wpdb->get_results("select * from bbse_commerce_membership where 1 ".$where." order by ".$this->orderby." ".$this->order." limit ".$start_pos.", ".$per_page);

		//}else{
			//$this->row = $wpdb->get_results("select * from bbse_commerce_membership where 1 ".$where." order by ".$this->orderby." ".$this->order);
		//}
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
}

// 접속자 회원등급
if(!function_exists('bbse_get_current_user_level')){
	function bbse_get_current_user_level(){
		if(current_user_can('level_5')){  // 관리자
			return "administrator";
		
		}else if(current_user_can('level_0')){  // 로그인 회원
			return "author";
		}
		else return "all";  // 게스트
	}
}

/*-------------------------------------------------------------------
 * 관리자 페이지 생성 함수 start
 ------------------------------------------------------------------*/
// 대시보드
if(!function_exists('bbse_commerce_dashboard')){
	function bbse_commerce_dashboard(){
		global $wpdb,$orderStatus,$payHow;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-dashboard.php");
	}
}

// 상품카테고리관리
if(!function_exists('bbse_commerce_category')){
	function bbse_commerce_category(){
		global $wpdb;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		wp_enqueue_style('bbse-category-nestable-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/category-nestable.css');
		wp_enqueue_script('bbse-nestable-js', BBSE_COMMERCE_PLUGIN_WEB_URL.'js/jquery.nestable.js');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-category.php");
	}
}


// 상품관리
if(!function_exists('bbse_commerce_goods')){
	function bbse_commerce_goods(){
		global $wpdb;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-goods.php");

	}
}

// 상품등록
if(!function_exists('bbse_commerce_goods_add')){
	function bbse_commerce_goods_add(){
		global $wpdb;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		wp_enqueue_style('bbse-admin-lightBox',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/lightbox.css');
		wp_enqueue_script('bbse-admin-lightBox',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/lightbox.min.js',array('jquery'));
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-goods-add.php");
	}
}

// 상품순서변경
if(!function_exists('bbse_commerce_goods_order')){
	function bbse_commerce_goods_order(){
		global $wpdb;
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-goods-order.php");
	}
}
//쿠폰관리
if(!function_exists('bbse_commerce_coupon')){
	function bbse_commerce_coupon(){
		global $wpdb;
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-coupon.php");
	}
}

// 주문관리
if(!function_exists('bbse_commerce_order')){
	function bbse_commerce_order(){
		global $wpdb,$orderStatus,$payHow;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-order.php");
	}
}

// 통계정산관리
if(!function_exists('bbse_commerce_statistics')){
	function bbse_commerce_statistics(){
		global $wpdb;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-statistics.php");
	}
}

// 상점관리
if(!function_exists('bbse_commerce_config')){
	function bbse_commerce_config(){
		global $wpdb;
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-config.php");
	}
}

// 품절상품입고알림목록
if(!function_exists('bbse_commerce_soldout_notice')){
	function bbse_commerce_soldout_notice(){
		global $wpdb;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-soldout-notice.php");
	}
}

// Q&A
if(!function_exists('bbse_commerce_qna')){
	function bbse_commerce_qna(){
		global $wpdb;

        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-qna.php");
	}
}

// 상품후기
if(!function_exists('bbse_commerce_review')){
	function bbse_commerce_review(){
		global $wpdb;

        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-review.php");
	}
}

// 회원관리
if (!function_exists('bbse_commerce_member')) {
	function bbse_commerce_member($str) {
		global $wpdb;

        wp_enqueue_script('jquery-ui-core');
		wp_enqueue_style('bbse-admin-datepicker',BBSE_COMMERCE_PLUGIN_WEB_URL.'js/datepicker/smoothness/jquery-ui-1.10.4.custom.min.css');

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-member.php");
	}
}

// 대량 상품등록(CSV)
if(!function_exists('bbse_commerce_goods_dbinsert')){
	function bbse_commerce_goods_dbinsert(){
		global $wpdb;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-goods-dbinsert.php");
	}
}

// 회원 DB 이전
if(!function_exists('bbse_commerce_member_dbinsert')){
	function bbse_commerce_member_dbinsert(){
		global $wpdb;

		wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
		require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-member-dbinsert.php");
	}
}

// 입금확인
if(!function_exists('bbse_commerce_account')){
    function bbse_commerce_account(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-account.php");
    }
}

// 재고현황
if(!function_exists('bbse_commerce_invenState')){
    function bbse_commerce_invenState(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-invenState.php");
    }
}

// 입출고내역
if(!function_exists('bbse_commerce_invenInOut')){
    function bbse_commerce_invenInOut(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-invenInOut.php");
    }
}

// 제품관리
if(!function_exists('bbse_commerce_inven')){
    function bbse_commerce_inven(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-inven.php");
    }
}

// 창고관리
if(!function_exists('bbse_commerce_storage')){
    function bbse_commerce_storage(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-storage.php");
    }
}

// 일련번호관리
if(!function_exists('bbse_commerce_serial')){
    function bbse_commerce_serial(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-serial.php");
    }
}

// 더존코드관리
if(!function_exists('bbse_commerce_douzone')){
    function bbse_commerce_douzone(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-douzone.php");
    }
}

// 물류창고위치관리
if(!function_exists('bbse_commerce_location')){
    function bbse_commerce_location(){
        global $wpdb;
        
        wp_enqueue_style('bbse-admin-ui',BBSE_COMMERCE_PLUGIN_WEB_URL.'css/admin-style.css');
        require_once(BBSE_COMMERCE_PLUGIN_ABS_PATH."admin/bbse-commerce-location.php");
    }
}


// 상품 카테고리 관리 - 카테고리 JSON 배열 만들기 (관리자)
if(!function_exists('bbse_commerce_get_category_json')){
	function bbse_commerce_get_category_json(){
		global $wpdb;

		$rowCnt='1';
		$rtnStr="";
		$dCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`>'1'");

		$query = $wpdb->get_results("SELECT * FROM `bbse_commerce_category` WHERE `idx`>'1' ORDER BY `c_rank` ASC", ARRAY_A);
		foreach($query as $row){
			if($row['depth_3']>'0'){
				if($depth_3<='0') $rtnStr .=",\"children\":[";
				else $rtnStr .="},";

				$depth_3=$row['depth_3'];
				$rtnStr .="{\"id\":".$row['idx'];
			}
			else if($row['depth_2']>'0'){
				if($depth_3>'0') $rtnStr .="}]";

				if($depth_2<='0') $rtnStr .=",\"children\":[";
				else $rtnStr .="},";

				$depth_2=$row['depth_2'];
				$rtnStr .="{\"id\":".$row['idx'];
			}
			else if($row['depth_1']>'0'){
				if($depth_3>'0') $rtnStr .="}]";
				if($depth_2>'0') $rtnStr .="}]";

				if($rtnStr) $rtnStr .="},";
				 $rtnStr .="{\"id\":".$row['idx'];

				$depth_1=$row['depth_1'];
				$depth_2=$row['depth_2'];
				$depth_3=$row['depth_3'];
			}

			if($dCnt==$rowCnt){
				if($depth_3>'0' || $depth_2>'0') $rtnStr .="}]";

				$rtnStr=$rtnStr."}";
			}
			$rowCnt++;
		}
		
		$rtnStr="[". $rtnStr."]";
		return $rtnStr;
	}
}


//상품 카테고리 관리 - 카테고리 MARKUP 만들기 (관리자)
if(!function_exists('bbse_commerce_get_category_markup')){
	function bbse_commerce_get_category_markup(){
		global $wpdb;

		$rowCnt='1';
		$rtnStr="";
		$dCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`>'1'");

		$query = $wpdb->get_results("SELECT * FROM `bbse_commerce_category` WHERE `idx`>'1' ORDER BY `c_rank` ASC", ARRAY_A);
		foreach($query as $row){
			$scriptCname=addslashes(str_replace("\"","'",$row['c_name']));

			if($row['depth_3']>'0'){
				if($depth_3<='0') $rtnStr .="<ol class=\"dd-list\"><li class=\"dd-item\" data-id=\"".$row['idx']."\">";
				else $rtnStr .="</li><li class=\"dd-item\" data-id=\"".$row['idx']."\">";

				$depth_3=$row['depth_3'];
				$rtnStr .="<div class=\"dd-handle\">".$row['c_name']."</div>
									<div class=\"select_btn\">
										<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon5.png\" onclick=\"select_input('".$row['idx']."','".$scriptCname."','".$row['c_use']."','".$row['user_class']."');\" width=\"18\" height=\"18\" alt=\"선택\" title=\"선택\" style=\"cursor:pointer;\" />&nbsp;&nbsp;<a href=\"".esc_url( home_url( '/' ) )."?bbseCat=".$row['idx']."\" target=\"_blank\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon4.png\" width=\"18\" height=\"18\" alt=\"".$row['c_name']." 카테고리 미리보기\" title=\"".$row['c_name']." 카테고리 미리보기\" /></a>
									</div>";

				$depth_3=$row['depth_3'];
			}
			else if($row['depth_2']>'0'){
				if($depth_3>'0') $rtnStr .="</li></ol>";

				if($depth_2<='0') $rtnStr .="<ol class=\"dd-list\"><li class=\"dd-item\" data-id=\"".$row['idx']."\">";
				else $rtnStr .="</li><li class=\"dd-item\" data-id=\"".$row['idx']."\">";

				$depth_2=$row['depth_2'];
				$rtnStr .="<div class=\"dd-handle\">".$row['c_name']."</div>
									<div class=\"select_btn\">
										<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon5.png\" onclick=\"select_input('".$row['idx']."','".$scriptCname."','".$row['c_use']."','".$row['user_class']."');\" width=\"18\" height=\"18\" alt=\"선택\" title=\"선택\" style=\"cursor:pointer;\" />&nbsp;&nbsp;<a href=\"".esc_url( home_url( '/' ) )."?bbseCat=".$row['idx']."\" target=\"_blank\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon4.png\" width=\"18\" height=\"18\" alt=\"".$row['c_name']." 카테고리 미리보기\" title=\"".$row['c_name']." 카테고리 미리보기\" /></a>
									</div>";

				$depth_2=$row['depth_2'];
				$depth_3=$row['depth_3'];
			}
			else if($row['depth_1']>'0'){
				if($depth_3>'0') $rtnStr .="</li></ol></li></ol></li>";
				elseif($depth_2>'0') $rtnStr .="</li></ol></li>";
				elseif($rtnStr) $rtnStr .="</li>";

				 $rtnStr .="<li class=\"dd-item\" data-id=\"".$row['idx']."\">
									<div class=\"dd-handle\">".$row['c_name']."</div>
									<div class=\"select_btn\">
										<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon5.png\" onclick=\"select_input('".$row['idx']."','".$scriptCname."','".$row['c_use']."','".$row['user_class']."');\" width=\"18\" height=\"18\" alt=\"선택\" title=\"선택\" style=\"cursor:pointer;\" />&nbsp;&nbsp;<a href=\"".esc_url( home_url( '/' ) )."?bbseCat=".$row['idx']."\" target=\"_blank\"><img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/left_icon4.png\" width=\"18\" height=\"18\" alt=\"".$row['c_name']." 카테고리 미리보기\" title=\"".$row['c_name']." 카테고리 미리보기\" /></a>
									</div>";

				$depth_1=$row['depth_1'];
				$depth_2=$row['depth_2'];
				$depth_3=$row['depth_3'];
			}

			if($dCnt==$rowCnt){
				if($depth_3>'0') $rtnStr .="</li></ol></li></ol></li>";
				elseif($depth_2>'0') $rtnStr .="</li></ol></li>";
				else $rtnStr .="</li>";
			
			}
			$rowCnt++;
		}
			
		return $rtnStr;
	}
}


//상품등록 -  카테고리 MARKUP 만들기 (관리자)
if(!function_exists('bbse_commerce_get_category_markup_for_goods')){
	function bbse_commerce_get_category_markup_for_goods($tIdx=""){
		global $wpdb;

		if($tIdx) $catetIdx=explode("|",$tIdx);

		$rowCnt='1';
		$rtnStr="";
		$dCnt = $wpdb->get_var("SELECT count(*) FROM `bbse_commerce_category` WHERE `idx`>'1'");

		$query = $wpdb->get_results("SELECT * FROM `bbse_commerce_category` WHERE `idx`>'1' ORDER BY `c_rank` ASC", ARRAY_A);
		foreach($query as $row){
			$depth_checked="";

			if($tIdx){
				if($row['idx']>'0' && in_array($row['idx'],$catetIdx)) $depth_checked="checked='checked'";
			}

			if($row['c_use']!='Y') {
				$noneDisplayImg="<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_none_display.png\" align=\"absmiddle\" title=\"표시하지 않는 카테고리\" style=\"margin-left:5px;\" />";
				$noneDisplayStyle="style=\"color:#ED1C24\"";
			}
			else {
				$noneDisplayImg="";
				$noneDisplayStyle="";
			}

			if($row['depth_3']>'0'){
				if($depth_3<='0') $rtnStr .="<ol class=\"cat-list\"><li>";
				else $rtnStr .="</li><li>";

				$depth_3=$row['depth_3'];

				$rtnStr .="<input type=\"checkbox\" name=\"goods_cat_list[]\" value=\"".$row['idx']."\" ".$depth_checked." /><span  onClick=\"list_check('".$row['idx']."');\" ".$noneDisplayStyle."> ".$row['c_name'].$noneDisplayImg."</span>";

				$depth_3=$row['depth_3'];
			}
			else if($row['depth_2']>'0'){
				if($depth_3>'0') $rtnStr .="</li></ol>";

				if($depth_2<='0') $rtnStr .="<ol class=\"cat-list\"><li>";
				else $rtnStr .="</li><li>";

				$depth_2=$row['depth_2'];

				$rtnStr .="<input type=\"checkbox\" name=\"goods_cat_list[]\" value=\"".$row['idx']."\" ".$depth_checked." /><span  onClick=\"list_check('".$row['idx']."');\" ".$noneDisplayStyle."> ".$row['c_name'].$noneDisplayImg."</span>";

				$depth_2=$row['depth_2'];
				$depth_3=$row['depth_3'];
			}
			else if($row['depth_1']>'0'){
				if($depth_3>'0') $rtnStr .="</li></ol></li></ol></li>";
				elseif($depth_2>'0') $rtnStr .="</li></ol></li>";
				elseif($rtnStr) $rtnStr .="</li>";

				 $rtnStr .="<li>
									<input type=\"checkbox\" name=\"goods_cat_list[]\" value=\"".$row['idx']."\" ".$depth_checked." /><span  onClick=\"list_check('".$row['idx']."');\" ".$noneDisplayStyle."> ".$row['c_name'].$noneDisplayImg."</span>";

				$depth_1=$row['depth_1'];
				$depth_2=$row['depth_2'];
				$depth_3=$row['depth_3'];
			}

			if($dCnt==$rowCnt){
				if($depth_3>'0') $rtnStr .="</li></ol></li></ol></li>";
				elseif($depth_2>'0') $rtnStr .="</li></ol></li>";
				else $rtnStr .="</li>";
			
			}
			$rowCnt++;
		}
			
		return $rtnStr;
	}
}


if (!function_exists('bbse_commerce_nl2br_markup')) {
	function bbse_commerce_nl2br_markup($str) {
		$rtnStr=str_replace("<br>","<br />",nl2br(stripcslashes($str)));
		return $rtnStr;
	}
}

// 주문/배송지 변경 시 배송비 설정 저장
if (!function_exists('bbse_commerce_get_delivery_info')) {
	function bbse_commerce_get_delivery_info($sType=""){ // 관리자 배송지 변경 시 "change" 전달
		global $wpdb;

		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
		$deliveryData=unserialize($confData->config_data);

		if(!$sType){   // 사용자 주문 시 이용 (관리자 배송지 변경 시 이용 안함)
			$cnfData['delivery_charge_type']=$deliveryData['delivery_charge_type'];                 // 유료배송,무료배송
			$cnfData['delivery_charge_payment']=$deliveryData['delivery_charge_payment'];    // 선불결제, 후불결제
			$cnfData['delivery_charge']=$deliveryData['delivery_charge'];                                // 기본 배송비
			$cnfData['condition_free_use']=$deliveryData['condition_free_use'];                        // 조건부 배송 사용여부
			$cnfData['total_pay']=$deliveryData['total_pay'];                                                    // 무료배송 되는 총 판매금액
		}

		$cnfData['localCnt']=$deliveryData['localCnt'];                                                       // 지역별 배송비 설정 개수 (최대 5개)
		for($i=1;$i<=$deliveryData['localCnt'];$i++){
			$cnfData['local_charge_'.$i.'_use']=$deliveryData['local_charge_'.$i.'_use'];
			$cnfData['local_charge_pay_'.$i]=$deliveryData['local_charge_pay_'.$i];
			$cnfData['local_charge_list_'.$i.'_idx']=$deliveryData['local_charge_list_'.$i.'_idx'];
			$cnfData['local_charge_list_'.$i.'_name']=$deliveryData['local_charge_list_'.$i.'_name'];
		}

		$rtnData=serialize($cnfData);

		return $rtnData;
	}
}

// 상태에 따른 Select 목록 추출
if (!function_exists('bbse_commerce_get_order_status')) {
	function bbse_commerce_get_order_status($tIdx){
		global $wpdb,$orderStatus;

		$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$tIdx."'");

		$rtnStatus="";
		foreach($orderStatus as $key => $val){
			if($oData->order_status==$key) $statusSelected="selected=\"selected\"";
			else $statusSelected="";
			
			$rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			/*
			if($key=="CA" || $key=="CE" || $key=="RA" || $key=="RE" || $key=="TR") $statusSelected .=" class=\"emRed\"";

			if($oData->order_status=='PR'){
				if($key=='PR' || $key=='PE' || $key=='CE' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='PE'){
				if($key=='PR' || $key=='PE' || $key=='CA' || $key=='CE' || $key=='DR' || $key=='DI' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='CA'){
				if(($oData->order_status_pre=='PR' && $key=='PR') || ($oData->order_status_pre=='PE' && $key=='PE') || ($oData->order_status_pre=='DR' && $key=='DR') || $key=='CA' || $key=='CE' || $key=='TR'){
					$rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
				}
			}
			elseif($oData->order_status=='CE'){
				if($key=='CE' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='DR'){
				if($key=='CA' || $key=='CE' || $key=='DR' || $key=='DI' || $key=='RA' || $key=='RE' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='DI'){
				if($key=='DI' || $key=='RA' || $key=='RE' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='DE'){
				if($key=='DE' || $key=='RA' || $key=='RE' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='RA'){
				if(($oData->order_status_pre=='DR' && $key=='DR') || ($oData->order_status_pre=='DI' && $key=='DI') || ($oData->order_status_pre=='DE' && $key=='DE') || $key=='RA' || $key=='RE' || $key=='TR'){
					$rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
				}
			}
			elseif($oData->order_status=='RE'){
				if($key=='RE' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='OE'){
				if($key=='OE' || $key=='TR') $rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
			}
			elseif($oData->order_status=='TR'){
				if($key=='TR'){
					$rtnStatus .="<option ".$statusSelected." value=\"".$key."\">".$val."</option>";
					$rtnStatus .="<option value=\"restore\">이전상태로 복원</option>";
				}
			}*/
		}

		return $rtnStatus;
	}
}

// 상태 변경 일자 추출
if (!function_exists('bbse_commerce_get_date_by_order_status')) {
	function bbse_commerce_get_date_by_order_status($tIdx){
		global $wpdb;

		$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$tIdx."'");
		$rtnList="";

		if($oData->order_status=='PE') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
		elseif($oData->order_status=='CA'){
			if($oData->order_status_pre=='PE' && $oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th>	<td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			$rtnList .="	<tr><th>취소신청일</th><td>".date("Y.m.d H:i:s",$oData->refund_apply_date)."</td></tr>";
		}
		elseif($oData->order_status=='CE'){
			if($oData->order_status_pre=='PE' && $oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th>	<td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			if($oData->refund_apply_date>'0') $rtnList .="<tr><th>취소신청일</th><td>".date("Y.m.d H:i:s",$oData->refund_apply_date)."</td></tr>";
			$rtnList .="	<tr><th>취소완료일</th><td>".date("Y.m.d H:i:s",$oData->refund_end_date)."</td></tr>";
		}
		elseif($oData->order_status=='DR'){
			if($oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
		}
		elseif($oData->order_status=='DI'){
			if($oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			$rtnList .="<tr><th>배송중 처리일</th><td>".date("Y.m.d H:i:s",$oData->delivery_ing_date)."</td></tr>";
		}
		elseif($oData->order_status=='DE'){
			if($oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			if($oData->delivery_ing_date>'0') $rtnList .="<tr><th>배송중 처리일</th><td>".date("Y.m.d H:i:s",$oData->delivery_ing_date)."</td></tr>";
			$rtnList .="	<tr><th>배송완료일</th><td>".date("Y.m.d H:i:s",$oData->delivery_end_date)."</td></tr>";
		}
		elseif($oData->order_status=='OE'){
			if($oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			if($oData->delivery_ing_date>'0') $rtnList .="<tr><th>배송중 처리일</th><td>".date("Y.m.d H:i:s",$oData->delivery_ing_date)."</td></tr>";
			if($oData->delivery_end_date>'0') $rtnList .="<tr><th>배송완료일</th><td>".date("Y.m.d H:i:s",$oData->delivery_end_date)."</td></tr>";
			$rtnList .="<tr><th>구매확정일</th><td>".date("Y.m.d H:i:s",$oData->order_end_date)."</td></tr>";
		}
		elseif($oData->order_status=='RA'){
			if($oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			if($oData->order_status_pre=='DI' && $oData->delivery_ing_date>'0') $rtnList .="<tr><th>배송중 처리일</th><td>".date("Y.m.d H:i:s",$oData->delivery_ing_date)."</td></tr>";
			elseif($oData->order_status_pre=='DE' && $oData->delivery_end_date>'0') $rtnList .="<tr><th>배송완료일</th><td>".date("Y.m.d H:i:s",$oData->delivery_end_date)."</td></tr>";
			$rtnList .="<tr><th>반품신청일</th><td>".date("Y.m.d H:i:s",$oData->refund_apply_date)."</td></tr>";
		}
		elseif($oData->order_status=='RE'){
			if($oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			if($oData->order_status_pre=='DI' && $oData->delivery_ing_date>'0') $rtnList .="<tr><th>배송중 처리일</th><td>".date("Y.m.d H:i:s",$oData->delivery_ing_date)."</td>	</tr>";
			elseif($oData->order_status_pre=='DE' && $oData->delivery_end_date>'0') $rtnList .="<tr><th>배송완료일</th><td>".date("Y.m.d H:i:s",$oData->delivery_end_date)."</td></tr>";
			if($oData->refund_apply_date>'0') $rtnList .="<tr><th>반품신청일</th><td>".date("Y.m.d H:i:s",$oData->refund_apply_date)."</td></tr>";
			$rtnList .="<tr><th>반품완료일</th><td>".date("Y.m.d H:i:s",$oData->refund_end_date)."</td></tr>";
		}
		elseif($oData->order_status=='TR'){
			if($oData->input_date>'0') $rtnList .="<tr><th>결제완료일</th><td>".date("Y.m.d H:i:s",$oData->input_date)."</td></tr>";
			if($oData->order_status_pre=='CA' || $oData->order_status_pre=='CE'){ 
				if($oData->refund_apply_date>'0') $rtnList .="<tr><th>취소신청일</th><td>".date("Y.m.d H:i:s",$oData->refund_apply_date)."</td></tr>";
				if($oData->refund_end_date>'0') $rtnList .="<tr><th>취소완료일</th><td>".date("Y.m.d H:i:s",$oData->refund_end_date)."</td></tr>";
			}

			if($oData->delivery_ing_date>'0') $rtnList .="<tr><th>배송중 처리일</th><td>".date("Y.m.d H:i:s",$oData->delivery_ing_date)."</td>	</tr>";
			if($oData->delivery_end_date>'0') $rtnList .="<tr><th>배송완료일</th><td>".date("Y.m.d H:i:s",$oData->delivery_end_date)."</td></tr>";
			if( $oData->order_end_date>'0') $rtnList .="<tr><th>구매확정일</th><td>".date("Y.m.d H:i:s",$oData->order_end_date)."</td></tr>";

			if($oData->order_status_pre=='RA' || $oData->order_status_pre=='RE'){ 
				if($oData->refund_apply_date>'0') $rtnList .="<tr><th>반품신청일</th><td>".date("Y.m.d H:i:s",$oData->refund_apply_date)."</td></tr>";
				if($oData->refund_end_date>'0') $rtnList .="<tr><th>반품완료일</th><td>".date("Y.m.d H:i:s",$oData->refund_end_date)."</td></tr>";
			}
		}

		return $rtnList;
	}
}

// 상태변경에 따른 Query 추출
if (!function_exists('bbse_commerce_get_update_query')) {
	function bbse_commerce_get_update_query($req){
		global $wpdb;

		$change_date=current_time('timestamp');
		$rtnQuery="";

		$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE idx='".$req['tIdx']."'");

		if($req['oStatus']=="PR" || $req['oStatus']=="PE" || $req['oStatus']=="CA" || $req['oStatus']=="CE"){
			$rtnQuery .="order_status='".$req['oStatus']."'";
		}
		elseif($req['oStatus']=="TR"){
			$rtnQuery .="order_status_pre='".$oData->order_status."', order_status='TR'";
		}
		elseif($req['oStatus']=="restore"){
			$rtnQuery .="order_status='".$oData->order_status_pre."', order_status_pre=''";
		}
		else{
			$deliveryCompany=explode("|||",$req['deliveryCompany']);
			$rtnQuery .="order_status='".$req['oStatus']."', delivery_no='".$req['deliveryNo']."', delivery_company='".$deliveryCompany['0']."', delivery_url='".$deliveryCompany['1']."'";
		}

		if($oData->order_status!='PR' && $req['oStatus']=='PR'){
			if($oData->input_date>'0') $rtnQuery .=", input_date=''";
			if($oData->refund_reason) $rtnQuery .=", refund_reason=''";
			if($oData->refund_bank_info) $rtnQuery .=", refund_bank_info=''";
			if($oData->refund_fees>'0') $rtnQuery .=", refund_fees=''";
			if($oData->refund_total>'0') $rtnQuery .=", refund_total=''";
			if($oData->refund_apply_date>'0') $rtnQuery .=", refund_apply_date=''";

		}
		elseif($oData->order_status!='PE' && $req['oStatus']=='PE'){
			if($oData->delivery_ing_date>'0') $rtnQuery .=", delivery_ing_date=''";
			if($oData->delivery_end_date>'0') $rtnQuery .=", delivery_end_date=''";
			if($oData->refund_reason) $rtnQuery .=", refund_reason=''";
			if($oData->refund_bank_info) $rtnQuery .=", refund_bank_info=''";
			if($oData->refund_fees>'0') $rtnQuery .=", refund_fees=''";
			if($oData->refund_total>'0') $rtnQuery .=", refund_total=''";
			if($oData->refund_apply_date>'0') $rtnQuery .=", refund_apply_date=''";

			if(!$oData->input_date || $oData->input_date<='0') $rtnQuery .=", input_date='".$change_date."'";
		}
		elseif($oData->order_status!='CA' && $req['oStatus']=='CA'){
			if(!$oData->refund_apply_date || $oData->refund_apply_date<='0') $rtnQuery .=", refund_apply_date='".$change_date."'";
			$rtnQuery .=", refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
			if(!$oData->order_status_pre) $rtnQuery .=", order_status_pre='".$oData->order_status."'";
		}
		elseif($oData->order_status=='CA' && $req['oStatus']=='CA'){
			$rtnQuery .=", refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
		}
		elseif($oData->order_status!='CE' && $req['oStatus']=='CE'){
			if(!$oData->refund_end_date || $oData->refund_end_date<='0') $rtnQuery .=", refund_end_date='".$change_date."'";
			$rtnQuery .=", refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
			if(!$oData->order_status_pre) $rtnQuery .=", order_status_pre='".$oData->order_status."'";
		}
		elseif($oData->order_status=='CE' && $req['oStatus']=='CE'){
			$rtnQuery .=", refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
		}
		elseif($oData->order_status!='DR' && $req['oStatus']=='DR'){
			if($oData->delivery_ing_date>'0') $rtnQuery .=", delivery_ing_date=''";
			if($oData->delivery_end_date>'0') $rtnQuery .=", delivery_end_date=''";
			if($oData->refund_reason) $rtnQuery .=", refund_reason=''";
			if($oData->refund_bank_info) $rtnQuery .=", refund_bank_info=''";
			if($oData->refund_fees>'0') $rtnQuery .=", refund_fees=''";
			if($oData->refund_total>'0') $rtnQuery .=", refund_total=''";
			if($oData->refund_apply_date>'0') $rtnQuery .=", refund_apply_date=''";
		}
		elseif($oData->order_status!='DI' && $req['oStatus']=='DI'){
			if($oData->delivery_end_date>'0') $rtnQuery .=", delivery_end_date=''";
			if($oData->refund_reason) $rtnQuery .=", refund_reason=''";
			if($oData->refund_bank_info) $rtnQuery .=", refund_bank_info=''";
			if($oData->refund_fees>'0') $rtnQuery .=", refund_fees=''";
			if($oData->refund_total>'0') $rtnQuery .=", refund_total=''";
			if($oData->refund_apply_date>'0') $rtnQuery .=", refund_apply_date=''";

			if(!$oData->delivery_ing_date || $oData->delivery_ing_date<='0') $rtnQuery .=", delivery_ing_date='".$change_date."'";
		}
		elseif($oData->order_status!='DE' && $req['oStatus']=='DE'){
			if($oData->refund_reason) $rtnQuery .=", refund_reason=''";
			if($oData->refund_bank_info) $rtnQuery .=", refund_bank_info=''";
			if($oData->refund_fees>'0') $rtnQuery .=", refund_fees=''";
			if($oData->refund_total>'0') $rtnQuery .=", refund_total=''";
			if($oData->refund_apply_date>'0') $rtnQuery .=", refund_apply_date=''";

			if(!$oData->delivery_end_date || $oData->delivery_end_date<='0') $rtnQuery .=", delivery_end_date='".$change_date."'";
		}
		elseif($oData->order_status!='OE' && $req['oStatus']=='OE'){
			if($oData->refund_reason) $rtnQuery .=", refund_reason=''";
			if($oData->refund_bank_info) $rtnQuery .=", refund_bank_info=''";
			if($oData->refund_fees>'0') $rtnQuery .=", refund_fees=''";
			if($oData->refund_total>'0') $rtnQuery .=", refund_total=''";
			if($oData->refund_apply_date>'0') $rtnQuery .=", refund_apply_date=''";

			if(!$oData->order_end_date || $oData->order_end_date<='0') $rtnQuery .=", order_end_date='".$change_date."'";
		}
		elseif($oData->order_status!='RA' && $req['oStatus']=='RA'){
			if(!$oData->refund_apply_date || $oData->refund_apply_date<='0') $rtnQuery .=", refund_apply_date='".$change_date."'";
			$rtnQuery .=", order_status_pre='".$oData->order_status."', refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
		}
		elseif($oData->order_status=='RA' && $req['oStatus']=='RA'){
			$rtnQuery .=", refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
		}
		elseif($oData->order_status!='RE' && $req['oStatus']=='RE'){
			if(!$oData->refund_end_date || $oData->refund_end_date<='0') $rtnQuery .=", refund_end_date='".$change_date."'";
			$rtnQuery .=", refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
			if(!$oData->order_status_pre) $rtnQuery .=", order_status_pre='".$oData->order_status."'";
		}
		elseif($oData->order_status=='RE' && $req['oStatus']=='RE'){
			$rtnQuery .=", refund_reason='".addslashes($req['refundReason'])."', refund_bank_info='".$req['refundBankInfo']."', refund_fees='".$req['refundFees']."', refund_total='".$req['refundTotal']."'";
		}

		return $rtnQuery;
	}
}

// 주소에 따른 추가 배송비 계산
if (!function_exists('bbse_commerce_get_delivery_add')) {
	function bbse_commerce_get_delivery_add($tAddr1){
		global $wpdb;

		$tmpAddr=explode(" ",$tAddr1);
		$campAddr=$tmpAddr['0']." ".$tmpAddr['1'];

		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
		$deliveryData=unserialize($confData->config_data);

		if($deliveryData != null && $deliveryData != ""){
    		$rtnDeliveryPay='0';
    		for($i=1;$i<=$deliveryData['localCnt'];$i++){
    			if($deliveryData['local_charge_'.$i.'_use']=='on'){
    				$tSize=sizeof($deliveryData['local_charge_list_'.$i.'_name']);
    				for($j=0;$j<$tSize;$j++){
    					if($campAddr==trim($deliveryData['local_charge_list_'.$i.'_name'][$j])) return $deliveryData['local_charge_pay_'.$i];
    				}
    			}
    		}
		}
		else{
		    return 0;
		}
	}
}

//관리자용 워드프레스 페이징 처리
if(!function_exists("bbse_commerce_get_pagination")) {
	function bbse_commerce_get_pagination($paged, $total_pages, $add_args=false) {
		/*
		$paged : 현재 페이지
		$total_pages : 총 페이지
		$add_args : 추가 전달값(쿼리스트링)
		*/

		$paging = paginate_links( array(
			'base' => '%_%',
			'format' => '?paged=%#%',
			'current' => max( 1, $paged ),
			'total' => $total_pages,
			'mid_size' => 20,
			'add_args' => $add_args
		) );

		return "<div class=\"admin-pagination\">".$paging."</div>";
	}
}


//관리자용 페이징 처리
if(!function_exists("bbse_get_list_paging")) { 
	function bbse_get_list_paging($funcName, $listTotal, $paged=1, $perPage=10, $pageBlock=10){
		$resultPaging=$thisPaging=$firstPage=$lastPage=$preBlock=$nextBlock="";

		$totalPage = ceil($listTotal / $perPage);   // 총 페이지수
		$page_tmp = floor(($paged - 1) / $pageBlock) * $pageBlock + 1;

		if($paged>1) $firstPage = "onClick=\"".$funcName."(1);\""; //첫페이지
		else $firstPage = "";

		if($paged<$totalPage) $lastPage = "onClick=\"".$funcName."(".$totalPage.");\""; //마지막페이지
		else $lastPage = "";

		if($page_tmp!=1) $preBlock = "onClick=\"".$funcName."(".($page_tmp-$pageBlock).");\""; //이전페이지블럭
		else $preBlock="";

		for($intloop=1;$intloop<=$pageBlock && $page_tmp <= $totalPage;$intloop++){
			if($page_tmp == $paged){
				$thisPaging .="<button type=\"button\" class=\"here\"><span>".$page_tmp."</span></button>";
			}else{
				$thisPaging .="<button type=\"button\" onClick=\"".$funcName."(".$page_tmp.");\"><span>".$page_tmp."</span></button>";
			}	
			$page_tmp=$page_tmp+1;
		}

		if($page_tmp > $totalPage) $nextBlock=""; //다음페이지블럭
		else $nextBlock="onClick=\"".$funcName."(".($page_tmp).");\"";

		$resultPaging="<button type=\"button\" ".$firstPage." class=\"first\" title=\"처음페이지\"><span>&lt;&lt;</span></button>
							<button type=\"button\" ".$preBlock." class=\"prev\" title=\"이전블럭\"><span>&lt;</span></button>
							<span class=\"page\">
							".$thisPaging."
							</span>
							<button type=\"button\" ".$nextBlock." class=\"next\" title=\"다음블럭\"><span>&gt;</span></button>
							<button type=\"button\" ".$lastPage." class=\"last\" title=\"마지막페이지\"><span>&gt;&gt;</span></button>";
		return $resultPaging;
	}
}

//카드결제 시 카드사명 추출
if(!function_exists("bbse_commerce_get_card_name")) { 
	function bbse_commerce_get_card_name($tCardNo,$tCardKind){
		global $wpdb;

		if($tCardKind=='allthegate'){
			$listCard=Array("100"=>"BC","205"=>"우리","200"=>"KB국민","206"=>"씨티","300"=>"외환","207"=>"신세계한미","400"=>"삼성","208"=>"신협체크","500"=>"신한","301"=>"제주","800"=>"현대","302"=>"광주","900"=>"롯데","303"=>"전북","201"=>"NH","700"=>"해외JCB","310"=>"하나SK","801"=>"해외DINERS","110"=>"중국은련","901"=>"해외AMEX","202"=>"수협","1000"=>"해외VISA","203"=>"한미","1100"=>"해외MASTER");
		}
		elseif($tCardKind=='INIpay50'){
			$listCard=Array("01"=>"하나(외환)","03"=>"롯데","04"=>"현대","06"=>"국민","11"=>"비씨(BC)","12"=>"삼성","14"=>"신한","15"=>"한미","16"=>"NH","17"=>"하나 SK","21"=>"해외비자","22"=>"해외마스터","23"=>"JCB","24"=>"해외아멕스","25"=>"해외다이너스","34"=>"하나","41"=>"NH(농협)");
		}
		elseif($tCardKind=='lguplusXPay'){
			$listCard=Array(
			"11"=>"국민", "21"=>"외환", "30"=>"KDB산업체크", "31"=>"비씨", "32"=>"하나 SK", "33"=>"우리", "34"=>"수협", "35"=>"전북", "36"=>"씨티", "37"=>"우체국체크", "38"=>"새마을금고체크", "39"=>"저축은행체크", "41"=>"신한", "42"=>"제주", "46"=>"광주", "51"=>"삼성", "61"=>"현대", "62"=>"신협체크", "71"=>"롯데", "91"=>"NH", "3C"=>"중국은련", "4J"=>"해외JCB", "4M"=>"해외MASTER", "4V"=>"해외VISA", "6D"=>"해외DINERS", "6I"=>"해외DISCOVER");
		}
		elseif($tCardKind=='kakaopay'){
			$listCard=Array(
			"01"=>"비씨", "08"=>"롯데자사", "16"=>"하나SK", "26"=>"해외마스터", "34"=>"은련", "02"=>"국민", "11"=>"씨티,한미", "21"=>"광주", "27"=>"해외다이너스", "35"=>"새마을금고", "03"=>"외환", "12"=>"NH채움", "22"=>"전북", "28"=>"해외AMX", "36"=>"KDB산업", "04"=>"삼성", "13"=>"수협", "23"=>"제주", "29"=>"해외JCB", "06"=>"신한", "14"=>"신협", "24"=>"산은캐피탈", "32"=>"우체국", "07"=>"현대", "15"=>"우리", "25"=>"해외비자", "33"=>"저축은행");
		}

		return ($listCard[trim($tCardNo)])?$listCard[trim($tCardNo)]:"기타";
	}
}

//가상계좌 시 은행명 추출
if(!function_exists("bbse_commerce_get_vbank_name")) { 
	function bbse_commerce_get_vbank_name($tBankNo,$tBankKind){
		global $wpdb;

		if($tBankKind=='allthegate'){
			$listVbank=Array("39"=>"경남은행","34"=>"광주은행","04"=>"국민은행","03"=>"기업은행","11"=>"농협","31"=>"대구은행","32"=>"부산은행","02"=>"산업은행","45"=>"새마을금고","07"=>"수협","88"=>"싞한은행","48"=>"신협","05"=>"외홖은행","20"=>"우리은행","71"=>"우체국","37"=>"전북은행","35"=>"제주은행","81"=>"하나은행","27"=>"한국씨티은행","23"=>"SC은행","09"=>"동양증권","78"=>"신한금융투자증권","40"=>"삼성증권","30"=>"미래에셋증권","43"=>"한국투자증권","69"=>"한화증권");
		}
		elseif($tBankKind=='INIpay50'){
			$listVbank=Array("03"=>"기업은행","04"=>"국민은행","05"=>"외환은행","07"=>"수협중앙회","11"=>"농협중앙회","20"=>"우리은행","23"=>"SC제일은행","31"=>"대구은행","32"=>"부산은행","34"=>"광주은행","37"=>"전북은행","39"=>"경남은행","53"=>"한국씨티은행","71"=>"우체국","81"=>"하나은행","88"=>"통합신한은행 (신한","조흥은행)","D1"=>"동양종합금융증권","D2"=>"현대증권","D3"=>"미래에셋증권","D4"=>"한국투자증권","D5"=>"우리투자증권","D6"=>"하이투자증권","D7"=>"HMC투자증권","D8"=>"SK증권","D9"=>"대신증권","DA"=>"하나대투증권","DB"=>"굿모닝신한증권","DC"=>"동부증권","DD"=>"유진투자증권","DE"=>"메리츠증권","DF"=>"신영증권");
		}
		elseif($tBankKind=='lguplusXPay'){
			if(strlen($tBankNo)=='2'){
				$listVbank=Array("02"=>"산업은행", "03"=>"기업은행", "05"=>"외환은행", "06"=>"국민은행", "07"=>"수협", "11"=>"농협", "20"=>"우리은행", "23"=>"SC제일", "27"=>"한국씨티", "31"=>"대구은행", "32"=>"부산은행", "34"=>"광주은행", "35"=>"제주은행", "37"=>"전북은행", "39"=>"경남은행", "45"=>"새마을금고", "48"=>"신협", "71"=>"우체국", "81"=>"하나은행", "88"=>"신한은행", "26"=>"신한은행", "S0"=>"동양증권", "S1"=>"미래에셋", "S2"=>"신한금융투자", "S3"=>"삼성증권", "S6"=>"한국투자증권", "SG"=>"한화증권");
			}
			else{
				$listVbank=Array("002"=>"산업은행", "003"=>"기업은행", "005"=>"외환은행", "004"=>"국민은행", "007"=>"수협", "011"=>"농협", "020"=>"우리은행", "023"=>"SC제일", "027"=>"한국씨티", "031"=>"대구은행", "032"=>"부산은행", "034"=>"광주은행", "035"=>"제주은행", "037"=>"전북은행", "039"=>"경남은행", "045"=>"새마을금고", "048"=>"신협", "071"=>"우체국", "081"=>"하나은행", "088"=>"신한은행", "209"=>"동양증권", "230"=>"미래에셋", "278"=>"신한금융투자", "240"=>"삼성증권", "243"=>"한국투자증권", "269"=>"한화증권");
			}
		}

		return ($listVbank[trim($tBankNo)])?$listVbank[trim($tBankNo)]:"기타";
	}
}

// 주문 시/입금 시 재고 마이너스처리
if(!function_exists("bbse_commerce_goods_stock_minus")) { 
	function bbse_commerce_goods_stock_minus($orderNo){
		global $wpdb;

		$cnfData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
		$confData=unserialize($cnfData->config_data);
		$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE order_no='".$orderNo."' AND order_stock_check='n'");

		if(((!$confData['count_cutback'] || $confData['count_cutback']=='order') && ($oData->order_status=='PR' || $oData->order_status=='PE')) || ($confData['count_cutback']=='deposit' && $oData->order_status=='PE')){
			$result = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."'");
			foreach($result as $i=>$gData) {
				$goods=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$gData->goods_idx."'");
				if($goods->goods_count_flag!='unlimit'){
					$basicOpt=unserialize($gData->goods_option_basic);

					for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
						if($goods->goods_count_flag=='goods_count' && $basicOpt['goods_option_title'][$b]=="단일상품"){
							$wpdb->query("UPDATE bbse_commerce_goods SET goods_count=(goods_count-".$basicOpt['goods_option_count'][$b].") WHERE idx='".$goods->idx."'");
						}
						elseif($goods->goods_count_flag=='option_count' && $basicOpt['goods_option_title'][$b]!="단일상품"){
							$wpdb->query("UPDATE bbse_commerce_goods_option SET goods_option_item_count=(goods_option_item_count-".$basicOpt['goods_option_count'][$b].") WHERE goods_idx='".$goods->idx."' AND goods_option_title='".trim($basicOpt['goods_option_title'][$b])."'");
						}
					}
				}
			}

			$wpdb->query("UPDATE bbse_commerce_order SET order_stock_check='y' WHERE idx='".$oData->idx."' AND order_no='".$orderNo."' AND order_stock_check='n'");
		}
	}
}

// 입금대기/취소완료/반품완료 시 재고 플러스처리
if(!function_exists("bbse_commerce_goods_stock_plus")) { 
	function bbse_commerce_goods_stock_plus($orderNo){
		global $wpdb;

		$cnfData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
		$confData=unserialize($cnfData->config_data);
		$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE order_no='".$orderNo."' AND order_stock_check='y'");

		if(($confData['count_cutback']=='deposit' && $oData->order_status=='PR') || $oData->order_status=='CE' || $oData->order_status=='RE'){
			$result = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."'");
			foreach($result as $i=>$gData) {
				$goods=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$gData->goods_idx."'");
				if($goods->goods_count_flag!='unlimit'){
					$basicOpt=unserialize($gData->goods_option_basic);

					for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
						if($goods->goods_count_flag=='goods_count' && $basicOpt['goods_option_title'][$b]=="단일상품"){
							$wpdb->query("UPDATE bbse_commerce_goods SET goods_count=(goods_count+".$basicOpt['goods_option_count'][$b].") WHERE idx='".$goods->idx."'");
						}
						elseif($goods->goods_count_flag=='option_count' && $basicOpt['goods_option_title'][$b]!="단일상품"){
							$wpdb->query("UPDATE bbse_commerce_goods_option SET goods_option_item_count=(goods_option_item_count+".$basicOpt['goods_option_count'][$b].") WHERE goods_idx='".$goods->idx."' AND goods_option_title='".trim($basicOpt['goods_option_title'][$b])."'");
						}
					}
				}
			}
	
			if($confData['count_cutback']=='deposit' && $oData->order_status=='PR'){
				$wpdb->query("UPDATE bbse_commerce_order SET order_stock_check='n' WHERE idx='".$oData->idx."' AND order_no='".$orderNo."' AND order_stock_check='y'");
			}
		}
	}
}

// 취소/환불/구매확정 시 적립금 관리
if(!function_exists("bbse_commerce_order_earn_check")) { 
	function bbse_commerce_order_earn_check($orderNo){
		global $wpdb;

		$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE order_no='".$orderNo."'");
		$nowTime=current_time('timestamp');

		if($oData->user_id){
			if(($oData->order_status=='CE' || $oData->order_status=='RE') && $oData->use_earn>'0'){
				$inCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_earn_log WHERE (earn_type='cancel' OR earn_type='refund') AND etc_idx='".$orderNo."' AND user_id='".$oData->user_id."' AND earn_mode='IN'");
				$outCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_earn_log WHERE earn_type='order' AND etc_idx='".$orderNo."' AND user_id='".$oData->user_id."' AND earn_mode='OUT'");

				if($inCnt<='0' && $outCnt>'0'){
					$earnData=$wpdb->get_row("SELECT * FROM bbse_commerce_earn_log WHERE earn_type='order' AND etc_idx='".$orderNo."' AND user_id='".$oData->user_id."' AND earn_mode='OUT'");
					if($earnData->earn_point>'0'){
						$memData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$oData->user_id."'");
						if($oData->order_status=='CE') $earnType="cancel";
						else $earnType="refund";

						$wpdb->query("INSERT INTO bbse_commerce_earn_log (earn_mode,earn_type,earn_point,old_point,user_id,user_name,etc_idx,reg_date) VALUES ('IN','".$earnType."','".$earnData->earn_point."','".$memData->mileage."','".$memData->user_id."','".$memData->name."','".$orderNo."','".$nowTime."')");
						$earnLogIdx = $wpdb->insert_id;

						if($earnLogIdx) $wpdb->get_row("UPDATE bbse_commerce_membership SET mileage=(mileage+".$earnData->earn_point.") WHERE user_id='".$oData->user_id."'");
					}
				}
			}
			elseif($oData->order_status=='OE' && $oData->add_earn>'0'){
				$memData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$oData->user_id."'");


				$wpdb->query("INSERT INTO bbse_commerce_earn_log (earn_mode,earn_type,earn_point,old_point,user_id,user_name,etc_idx,reg_date) VALUES ('IN','order','".$oData->add_earn."','".$memData->mileage."','".$memData->user_id."','".$memData->name."','".$orderNo."','".$nowTime."')");
				$earnLogIdx = $wpdb->insert_id;

				if($earnLogIdx) $wpdb->get_row("UPDATE bbse_commerce_membership SET mileage=(mileage+".$oData->add_earn.") WHERE user_id='".$oData->user_id."'");
			}
		}
	}
}


// 배송완료/구매확정 처리
if(!function_exists("bbse_commerce_chk_order_status")) { 
	function bbse_commerce_chk_order_status(){
		global $wpdb;

		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
		if(sizeof($confData)>'0'){
			$chkData=unserialize($confData->config_data);
			$nowTime=current_time('timestamp');
			$toEndTime=mktime('23','59','59',date('m',$nowTime),date('d',$nowTime),date('Y',$nowTime));
			if(!$chkData['order_cancel_day'] || $chkData['order_cancel_day']<='0') $chkData['order_cancel_day']='3';
			if(!$chkData['delivery_end_day'] || $chkData['delivery_end_day']<='0') $chkData['delivery_end_day']='3';
			if(!$chkData['order_end_day'] || $chkData['order_end_day']<='0') $chkData['order_end_day']='7';

			$cancelDate=$toEndTime-($chkData['order_cancel_day']*24*60*60);          // 입금대기->취소완료 기준일
			$deliveryEndDate=$toEndTime-($chkData['delivery_end_day']*24*60*60);   // 배송중->배송완료 기준일
			$orderEndDate=$toEndTime-($chkData['order_end_day']*24*60*60);         // 배송완료->구매확정 기준일

			// 입금대기->취소완료
			$cancelCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order WHERE order_status='PR' AND order_date>'0' AND order_date<='".$cancelDate."'");
			if($cancelCnt>'0'){
				$prResult=$wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE order_status='PR' AND order_date>'0' AND order_date<='".$cancelDate."'");
				foreach($prResult as $i=>$prData) {
					$prOrderDate=$wpdb->get_var("SELECT order_date FROM bbse_commerce_order WHERE idx='".$prData->idx."'");
					$plusCancelDate=mktime('23','59','59',date('m',$prOrderDate),date('d',$prOrderDate)+$chkData['order_cancel_day'],date('Y',$prOrderDate));

					$wpdb->query("UPDATE bbse_commerce_order SET order_status='CE',refund_end_date='".$plusCancelDate."' WHERE idx='".$prData->idx."'");
					bbse_commerce_goods_stock_plus($prData->order_no); // 재고 복구
					bbse_commerce_order_earn_check($prData->order_no); // 적립금 환불 처리
					$msResult=bbse_commerce_mail_send('order-cancel',$prData->order_no,''); // 메일발송
				}
			}

			// 배송중->배송완료
			$deliveryEndCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order WHERE order_status='DI' AND delivery_ing_date>'0' AND delivery_ing_date<='".$deliveryEndDate."'");
			if($deliveryEndCnt>'0'){
				$deResult=$wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE order_status='DI' AND delivery_ing_date>'0' AND delivery_ing_date<='".$deliveryEndDate."'");
				foreach($deResult as $i=>$deData) {
					$deDeliveryIngDate=$wpdb->get_var("SELECT delivery_ing_date FROM bbse_commerce_order WHERE idx='".$deData->idx."'");
					$plusDeveryIngDate=mktime('23','59','59',date('m',$deDeliveryIngDate),date('d',$deDeliveryIngDate)+$chkData['delivery_end_day'],date('Y',$deDeliveryIngDate));

					$wpdb->query("UPDATE bbse_commerce_order SET order_status='DE',delivery_end_date='".$plusDeveryIngDate."' WHERE idx='".$deData->idx."'");
				}
			}

			// 배송완료->구매확정
			$orderEndCnt=$wpdb->get_var("SELECT count(idx) FROM bbse_commerce_order WHERE order_status='DE' AND delivery_end_date>'0' AND delivery_end_date<='".$orderEndDate."'");
			if($orderEndCnt>'0'){
				$oeResult=$wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE order_status='DE' AND delivery_end_date>'0' AND delivery_end_date<='".$orderEndDate."'");
				foreach($oeResult as $i=>$oeData) {
					$deDeliveryEndDate=$wpdb->get_var("SELECT delivery_end_date FROM bbse_commerce_order WHERE idx='".$oeData->idx."'");
					$plusOrderEndDate=mktime('23','59','59',date('m',$deDeliveryEndDate),date('d',$deDeliveryEndDate)+$chkData['order_end_day'],date('Y',$deDeliveryEndDate));
					if($plusOrderEndDate<=$nowTime){
						$wpdb->query("UPDATE bbse_commerce_order SET order_status='OE',order_end_date='".$plusOrderEndDate."' WHERE idx='".$oeData->idx."'");
						bbse_commerce_order_earn_check($oeData->order_no); // 적립금 처리
						bbse_commerce_auto_upgrade_user($oeData->order_no);//자동등업처리
					}
				}
			}
		}
	}
}

//자동등업
if(!function_exists("bbse_commerce_auto_upgrade_user")) { 
	function bbse_commerce_auto_upgrade_user($order_id){
		global $wpdb;
		$user_id = $wpdb->get_var('SELECT user_id FROM bbse_commerce_order WHERE order_no = "'.$order_id.'" ');
		if(!empty($user_id)){
			$user_total 	= $wpdb->get_var('SELECT SUM(cost_total) FROM  bbse_commerce_order WHERE user_id = "'.$user_id.'" AND order_status = "OE"');
			$user_total_cnt = $wpdb->get_var('SELECT COUNT(idx) FROM  bbse_commerce_order WHERE user_id = "'.$user_id.'" AND order_status = "OE"');
			
			//관리자는 건너뛰기
			$myclass = $wpdb->get_row('SELECT user_class,user_no FROM bbse_commerce_membership WHERE user_id = "'.$user_id.'"');
			if($myclass->user_class == 1){
				return false;
			}
			$class = $wpdb->get_results('SELECT * FROM bbse_commerce_membership_class WHERE auto_cnt > 0 OR auto_total > 0 AND no > "'.$myclass->user_class.'" ORDER BY no ASC');
			foreach ($class as $key => $value) {
				if($user_total >= $value->auto_total || $user_total_cnt >= $value->auto_cnt){
					$wpdb->query('UPDATE bbse_commerce_membership SET user_class="'.$value->no.'" WHERE user_id = "'.$user_id.'"');
				}
			}
		}
	}
}
		

// GET 파라미터 암호화
if(!function_exists('bbse_commerce_parameter_encryption')){
	function bbse_commerce_parameter_encryption($boardName, $mode, $no="", $page="", $keyfield="", $keyword="", $search_chk="", $category="", $ref="", $cno=""){
		$enCode = new BBSeSecretCode;
		$srcCode = "bname=".$boardName."&mode=".$mode."&no=".$no."&page=".$page."&keyfield=".$keyfield."&keyword=".$keyword."&search_chk=".$search_chk."&cate=".$category."&ref=".$ref."&cno=".$cno;
		$rstCode = $enCode->enc($srcCode);

		return base64_encode($rstCode);
		//return $srcCode;
	}
}


// 문자열 자르기
if(!function_exists('cut_text')){
	function cut_text($text, $text_count, $more_text="…") {
		$length = strlen($text);
		if($length <= $text_count) return $text;
		else return mb_substr($text, 0, $text_count, "UTF-8").$more_text;
	}
}

// ssl url 제거
if(!function_exists("remove_ssl_url")) {
	function remove_ssl_url($h_url) {
		global $theme_shortname;
		$p_url = parse_url($h_url);
		if($p_url['scheme'] == "https") {
			$return_url = "http://".$p_url['host'];
			if($p_url['port'] != "" && $p_url['port'] != get_option($theme_shortname."_ssl_port")) {
				$return_url .= ":".$p_url['port'].($p_url['path']!=""?$p_url['path']:"").($p_url['query']!=""?"?".$p_url['query']:"");
			}else{
				$return_url .= ($p_url['path']!=""?$p_url['path']:"").($p_url['query']!=""?"?".$p_url['query']:"");
			}
		}else{
			$return_url = $h_url;
		}
		return $return_url;
	}
}

// EZSMS SMS(문자)발송 
if(!function_exists('bbse_commerce_ezsms_send')){
	function bbse_commerce_ezsms_send($receiveNo, $callbackTel, $msgContents, $msgType, $sendType, $sendDate=""){ 
		/* 
		예) bbse_commerce_ezsms_send("010-1234-5678", "02-123-4567", "테스트 문자발송", "SMS", "D", "");

		receiveNo : 010-xxxx-xxxx or 010xxxxxxxx <= 수신자 전화번호 (콤마로 구분)
		callbackTel : 보내는 번호
		msgBox : 메세지 내용
		msgType : SMS or LMS
		sendType : D or R  (D : 즉시전송, R : 예약전송)
		sendDate : YYYYMMDDhhmmss (예약전송 시 예약전송 시간)
		*/
		$smsid = get_option('bbse_commerce_sms_id');
		$smskey = get_option('bbse_commerce_sms_key');

		$msg_len = strlen(iconv("UTF-8","EUC-KR",$msgContents));
		if($msg_len > 80) $msgType = "LMS";
		else $msgType = "SMS";

		if(!empty($smsid) && !empty($smskey)){
			$msgBox = iconv("UTF-8", "EUC-KR", $msgContents);
			$msgBox = base64_encode($msgBox);
			$msgType = $msgType;
			$sendType = $sendType;
			if($sendType == "D") $sendDate = "";
			$tmpRcvNo = explode(",", $receiveNo);
			$reqExp1 = "/^0([0-9]{2,3})-?([0-9]{3,4})-?([0-9]{4})$/";

			$receiveNo = "";
			for($i = 0; $i < count($tmpRcvNo); $i++){
				$chkNumber = str_replace(" " , "", $tmpRcvNo[$i]);
				if(trim($chkNumber) and preg_match($reqExp1, $chkNumber)){
					if($receiveNo) $receiveNo .= "|";
					$receiveNo .= $chkNumber;
				}
			}

			$domain = $_SERVER['HTTP_HOST'];
			$keydate = get_option('bbse_commerce_sms_keydate');
			$paramiters = array('smsid' => $smsid, 'smskey' => $smskey, 'msgBox' => $msgBox, 'msgType' => $msgType, 'sendType' => $sendType, 'sendDate' => $sendDate, 'callbackTel' => $callbackTel, 'receiveNo' => $receiveNo, 'domain' => $domain, 'keydate' => $keydate);
			$client = new soapclient5('http://api.ezsms.kr/clinic_soap_smssend.php', true);
			$result = $client->call('smssend', $paramiters);

			if($result){
				$tmpResult = explode("|||", $result);  // result : success|||남은포인트
				if($tmpResult['0'] == "success" or $tmpResult['0'] == "oversent" or $tmpResult['0'] == "shortage" or $tmpResult['0'] == "keyError") return $tmpResult['0'];
				else return false;
			}else return false;
		}else return "configError";  // 환경설정 에러
	}
}

if(!function_exists('bbse_commerce_sms_send')){
	function bbse_commerce_sms_send($smsType, $param) {
		global $wpdb;
		/*
		- smsType=join(회원가입), order-ready(입금대기), order-input(결제완료), order-shipment(배송중)
		- param : 회원아이디, 주문번호

		bbse_commerce_sms_send("join", 회원아이디); // 회원가입SMS
		bbse_commerce_sms_send("order-ready", 주문번호); // 입금대기SMS
		bbse_commerce_sms_send("order-input", 주문번호); // 결제완료SMS
		bbse_commerce_sms_send("order-shipment", 주문번호); // 배송중SMS
		*/
		$patten_vars = array("/\[user_id\]/", "/\[cp_name\]/", "/\[goods\]/", "/\[order_no\]/", "/\[delivery_name\]/", "/\[delivery_no\]/");
		$config = $wpdb->get_row("SELECT * FROM bbse_commerce_membership_config LIMIT 1");
		if($config->sms_use_yn == "Y") {
			switch($smsType) {
				case "join":
					$member = $wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$param."'");
					if($member->user_no) {
						$replace_vars = array($member->user_id, get_bloginfo('name'), "", "", "", "");
						if($config->sms_join_yn == "Y") {
							$userMsg = preg_replace($patten_vars, $replace_vars, $config->sms_join_msg);
							bbse_commerce_ezsms_send($member->hp, $config->sms_callback_tel, $userMsg, "SMS", "D", "");//회원
						}
						if($config->sms_join_admin_yn == "Y" && $config->sms_admin_tel!="") {
							$adminMsg = preg_replace($patten_vars, $replace_vars, $config->sms_join_admin_msg);
							bbse_commerce_ezsms_send($config->sms_admin_tel, $config->sms_callback_tel, $adminMsg, "SMS", "D", "");//관리자
						}
					}
					break;
				case "order-ready":
					$order = $wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE order_no='".$param."'");
					if($order->idx) {
						$goods = $wpdb->get_row("SELECT goods_name, COUNT(*) AS order_cnt FROM bbse_commerce_order_detail WHERE order_no='".$order->order_no."' GROUP BY order_no");
						$goods_name = $goods->goods_name.(($goods->order_cnt > 1)?" 외 ".($goods->order_cnt-1)."건":"");
						$replace_vars = array($order->user_id, get_bloginfo('name'), $goods_name, $order->order_no, $order->delivery_company, $order->delivery_no);
						if($config->sms_order_yn == "Y") {
							$userMsg = preg_replace($patten_vars, $replace_vars, $config->sms_order_msg);
							bbse_commerce_ezsms_send($order->order_hp, $config->sms_callback_tel, $userMsg, "SMS", "D", "");//회원
						}
						if($config->sms_order_admin_yn == "Y" && $config->sms_admin_tel!="") {
							$adminMsg = preg_replace($patten_vars, $replace_vars, $config->sms_order_admin_msg);
							bbse_commerce_ezsms_send($config->sms_admin_tel, $config->sms_callback_tel, $adminMsg, "SMS", "D", "");//관리자
						}
					}
					break;
				case "order-input":
					$order = $wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE order_no='".$param."'");
					if($order->idx) {
						$goods = $wpdb->get_row("SELECT goods_name, COUNT(*) AS order_cnt FROM bbse_commerce_order_detail WHERE order_no='".$order->order_no."' GROUP BY order_no");
						$goods_name = $goods->goods_name.(($goods->order_cnt > 1)?" 외 ".($goods->order_cnt-1)."건":"");
						$replace_vars = array($order->user_id, get_bloginfo('name'), $goods_name, $order->order_no, $order->delivery_company, $order->delivery_no);
						if($config->sms_pay_yn == "Y") {
							$userMsg = preg_replace($patten_vars, $replace_vars, $config->sms_pay_msg);
							bbse_commerce_ezsms_send($order->order_hp, $config->sms_callback_tel, $userMsg, "SMS", "D", "");//회원
						}
						if($config->sms_pay_admin_yn == "Y" && $config->sms_admin_tel!="") {
							$adminMsg = preg_replace($patten_vars, $replace_vars, $config->sms_pay_admin_msg);
							bbse_commerce_ezsms_send($config->sms_admin_tel, $config->sms_callback_tel, $adminMsg, "SMS", "D", "");//관리자
						}
					}
					break;
				case "order-shipment":
					$order = $wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE order_no='".$param."'");
					if($order->idx) {
						$goods = $wpdb->get_row("SELECT goods_name, COUNT(*) AS order_cnt FROM bbse_commerce_order_detail WHERE order_no='".$order->order_no."' GROUP BY order_no");
						$goods_name = $goods->goods_name.(($goods->order_cnt > 1)?" 외 ".($goods->order_cnt-1)."건":"");
						$replace_vars = array($order->user_id, get_bloginfo('name'), $goods_name, $order->order_no, $order->delivery_company, $order->delivery_no);
						if($config->sms_delivery_yn == "Y") {
							$userMsg = preg_replace($patten_vars, $replace_vars, $config->sms_delivery_msg);
							bbse_commerce_ezsms_send($order->order_hp, $config->sms_callback_tel, $userMsg, "SMS", "D", "");//회원
						}
						if($config->sms_delivery_admin_yn == "Y" && $config->sms_admin_tel!="") {
							$adminMsg = preg_replace($patten_vars, $replace_vars, $config->sms_delivery_admin_msg);
							bbse_commerce_ezsms_send($config->sms_admin_tel, $config->sms_callback_tel, $adminMsg, "SMS", "D", "");//관리자
						}
					}
					break;
			}
		}
	}
}


//메일 발송 (관리자 메일발송)
if(!function_exists('bbse_mail')){
	function bbse_mail($to, $to_name, $from, $from_name, $subject, $content, $upfile=''){
		$from_name = sanitize_text_field($from_name);
		$from = sanitize_email($from);

		$headers  = 'Content-Type: text/html; charset=utf-8; format=flowed'."\n";
		$headers .= 'From: ' . $from_name . ' <' . $from . '>' . "\r\n";
		$headers .= 'Reply-To: ' . $from_name . ' <' . $from . '>' . "\r\n";

		return wp_mail( apply_filters( 'et_contact_page_email_to', $to ), sprintf( '[%s] ' . sanitize_text_field( $subject ), $from_name ), $content, apply_filters( 'et_contact_page_headers', $headers, $from_name, $from ), $upfile );
	}
}

// 메일 내용 가져오기
if(!function_exists('bbse_commerce_get_mail_content')){
	function bbse_commerce_get_mail_content($mailType,$uniData,$etcData){
		global $wpdb, $payHow, $orderStatus;

		unset($rtnData);
		$mailFile=Array("join"=>"member_join.php","find-pw"=>"member_find_pw.php","order-ready"=>"order_ready.php","order-input"=>"order_input.php","order-shipment"=>"order_shipment.php","order-cancel"=>"order_cancel.php","order-refund"=>"order_refund.php");
		$mailUse=Array("join"=>"member_mail_use","find-pw"=>"findpw_mail_use","order-ready"=>"order_mail_use","order-input"=>"input_mail_use","order-shipment"=>"shipment_mail_use","order-cancel"=>"cancel_mail_use","order-refund"=>"refund_mail_use");
		$mailStatus=Array("order-ready"=>"PR","order-input"=>"PE","order-shipment"=>"DI","order-cancel"=>"CE","order-refund"=>"RE");

		$mailSubject=Array("join"=>"{성명}님 회원가입을 축하드립니다.","find-pw"=>"{성명}님 요청하신 임시비밀번호입니다.","order-ready"=>"{성명}님 주문이 정상적으로 접수되었습니다.","order-input"=>"{성명}님 입금이 정상적으로 처리되었습니다.","order-shipment"=>"{성명}님 주문하신 상품이 정상적으로 발송되었습니다.","order-cancel"=>"{성명}님 주문이 정상적으로 취소 완료되었습니다.","order-refund"=>"{성명}님 주문이 정상적으로 반품 완료되었습니다.");

		$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='mail'");
		$cnfUse=unserialize($confData->config_data);

		if($cnfUse['blog_olny_mail_join']=='Y') $mailFile["join"]="member_blog_join.php";
		if($cnfUse['blog_olny_mail_idpw']=='Y') $mailFile["find-pw"]="member_blog_find_pw.php";

		if($cnfUse[$mailUse[$mailType]]!='off'){
			$reqArray = wp_remote_get(remove_ssl_url(BBSE_COMMERCE_PLUGIN_WEB_URL)."mail/".$mailFile[$mailType]);
			$rtnData['message']=wp_remote_retrieve_body($reqArray);
			$rtnData['subject']=$mailSubject[$mailType];
			$rtnData['to']="";

			if($mailType=="join" || ($mailType=="find-pw" && $etcData)){ // 회원
				$mData=$wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$uniData."'");
				if($mData->user_no>'0'){
					$rtnData['to']=$mData->email;
					$rtnData['subject']=str_replace("{성명}",$mData->name,$rtnData['subject']);

					$rtnData['message']=str_replace("{성명}",$mData->name,$rtnData['message']);
					$rtnData['message']=str_replace("{사이트이름}",get_bloginfo('name'),$rtnData['message']);
					$rtnData['message']=str_replace("{마이페이지링크}",remove_ssl_url(get_bloginfo('url'))."?bbseMy=mypage",$rtnData['message']);
					$rtnData['message']=str_replace("{새비밀번호}",$etcData,$rtnData['message']);
				}
				else $rtnData['message']=$rtnData['subject']="";
			}
			elseif($uniData && ($mailType=="order-ready" || $mailType=="order-input" || $mailType=="order-shipment" || $mailType=="order-cancel" || $mailType=="order-refund")){ // 주문

				$repStr="";
				$oData=$wpdb->get_row("SELECT * FROM bbse_commerce_order WHERE order_no='".$uniData."' AND order_status='".$mailStatus[$mailType]."'");

				$telOrderInfo="";
				if(strlen($oData->order_phone)>'9') $telOrderInfo .="전화번호 : ".$oData->order_phone;
				if($telOrderInfo) $telOrderInfo .=" / ";
				if($oData->order_hp) $telOrderInfo .="휴대폰 : ".$oData->order_hp;

				$telReceiveInfo="";
				if(strlen($oData->receive_phone)>'9') $telReceiveInfo .="전화번호 : ".$oData->receive_phone;
				if($telReceiveInfo) $telReceiveInfo .=" / ";
				if($oData->receive_hp) $telReceiveInfo .="휴대폰 : ".$oData->receive_hp;

				if($oData->idx>'0'){

					$repStr .="<table summary=\"타이틀명- 주문정보\" style=\"margin-top:30px;width:100%;text-align:left;font-size:12px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
										<tbody>
											<tr>
												<th style=\"background:#ffffff;color:#2c2e36;font-size:14px;\" height=\"26\" valign=\"top\" align=\"left\">
													주문정보
												</th>
												<td style=\"background:#ffffff;color:#94959d;font-size:11px;letter-spacing:-1px;\" height=\"26\" align=\"right\"></td>
											</tr>
										</tbody>
										</table>
										<table summary=\"주문정보 테이블입니다.\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"100%;border-top:1px solid #2c2c2c;border-bottom:1px solid #d9d9de;font-size:12px;text-align:left\" width=\"100%\"><tbody>
										<tbody>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">주문일</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".date("Y.m.d H:i:s",$oData->order_date)."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">주문번호</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$oData->order_no."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">주문상태</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#ff0000;font-family:Verdana,Geneva,Tahoma,sans-serif;font-weight:bold;\">".$orderStatus[$oData->order_status]."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">주문하신 분</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$oData->order_name."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">연락처</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$telOrderInfo."</td>
											</tr>
										</tbody>
										</table>";

					if($mailType!="order-cancel"){
						$repStr .="<table summary=\"타이틀명- 배송정보\" style=\"margin-top:30px;width:100%;text-align:left;font-size:12px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
										<tbody>
											<tr>
												<th style=\"background:#ffffff;color:#2c2e36;font-size:14px;\" height=\"26\" valign=\"top\" align=\"left\">
													배송정보
												</th>
												<td style=\"background:#ffffff;color:#94959d;font-size:11px;letter-spacing:-1px;\" height=\"26\" align=\"right\"></td>
											</tr>
										</tbody>
										</table>
										<table summary=\"배송정보 테이블입니다.\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"100%;border-top:1px solid #2c2c2c;border-bottom:1px solid #d9d9de;font-size:12px;text-align:left\" width=\"100%\"><tbody>
										<tbody>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">받으시는 분</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$oData->receive_name."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">연락처</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$telReceiveInfo."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">주소</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">[".$oData->receive_zip."] ".$oData->receive_addr1." ".$oData->receive_addr2."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">남기실 말씀</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$oData->order_comment."</td>
											</tr>";

						if(($mailType=="order-shipment" || $mailType=="order-refund") && $oData->delivery_company && $oData->delivery_no){
							$repStr .="	<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">택배사 명</th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$oData->delivery_company." (송장번호: ".$oData->delivery_no." ) <a href=\"".$oData->delivery_url."\" style=\"display:inline-block;background:#645F5E;margin:0 3px;text-align:center;color:#fff;border:solid 1px #555555;padding:2px 12px 0;font-weight:bold;font-family:'돋움',dotum,sans-serif;font-size:11px;line-height:1;text-decoration:none;\" target=\"_blank\">배송조회 &gt;</a></td>
												</tr>";
						}

						$repStr .="		</tbody>
												</table>";
					}

					if(($mailType=="order-cancel" || $mailType=="order-refund") && $oData->refund_bank_info && $oData->refund_total && $oData->refund_end_date){
						if($mailType=="order-cancel") $titStr="취소";
						else $titStr="반품";
						$refundBankInfo=explode("|||",$oData->refund_bank_info);

						$repStr .="<table summary=\"타이틀명- 배송정보\" style=\"margin-top:30px;width:100%;text-align:left;font-size:12px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
										<tbody>
											<tr>
												<th style=\"background:#ffffff;color:#2c2e36;font-size:14px;\" height=\"26\" valign=\"top\" align=\"left\">
													".$titStr."내역
												</th>
												<td style=\"background:#ffffff;color:#94959d;font-size:11px;letter-spacing:-1px;\" height=\"26\" align=\"right\"></td>
											</tr>
										</tbody>
										</table>
										<table summary=\"배송정보 테이블입니다.\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"100%;border-top:1px solid #2c2c2c;border-bottom:1px solid #d9d9de;font-size:12px;text-align:left\" width=\"100%\"><tbody>
										<tbody>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">".$titStr."완료일</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".date("Y.m.d H:i:s",$oData->refund_end_date)."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">".$titStr."사유</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$oData->refund_reason."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">환불계좌</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$refundBankInfo['0']." / ".$refundBankInfo['1']." / ".$refundBankInfo['2']."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">환불내역</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">최종결제금액 ".number_format($oData->cost_total)."원 - 환불수수료 ".number_format($oData->refund_fees)."원=최종환불금액 ".number_format($oData->refund_total)."원</td>
											</tr>
										</thead>
										<tbody>";
					}

					$repStr .="	<table summary=\"타이틀명- 구매상품 정보\" style=\"margin-top:30px;width:100%;text-align:left;font-size:12px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
										<tbody>
											<tr>
												<th style=\"background:#ffffff;color:#2c2e36;font-size:14px;\" height=\"26\" valign=\"top\" align=\"left\">
													구매상품 정보
												</th>
												<td style=\"background:#ffffff;color:#94959d;font-size:11px;letter-spacing:-1px;\" height=\"26\" align=\"right\"></td>
											</tr>
										</tbody>
										</table>
										<table summary=\"구매상품 정보 테이블입니다.\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"100%;border-top:1px solid #2c2c2c;border-bottom:1px solid #d9d9de;font-size:12px;text-align:left\" width=\"100%\"><tbody>
										<thead>
											<tr>
												<th width=\"\" scope=\"col\" style=\"padding:10px 10px 8px 10px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;text-align:center;\">상품명</th>
												<th width=\"50\" scope=\"col\" style=\"padding:10px 10px 8px 10px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;text-align:center;\">수량</th>
												<th width=\"60\" scope=\"col\" style=\"padding:10px 10px 8px 10px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;text-align:center;\">적립금</th>
												<th width=\"70\" scope=\"col\" style=\"padding:10px 10px 8px 10px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;text-align:center;\">합계</th>
											</tr>
										</thead>
										<tbody>";

					$gResult  = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$oData->order_no."' ORDER BY idx ASC");
					foreach($gResult as $j=>$gData) {
						unset($basicOpt);
						unset($addOpt);
						unset($basicImg);

						if($gData->goods_basic_img) 	$basicImg = wp_get_attachment_image_src($gData->goods_basic_img);

						if(!$basicImg['0']){
							$goodsAddImg=$wpdb->get_var("SELECT goods_add_img FROM bbse_commerce_goods WHERE idx='".$gData->goods_idx."'"); 

							$imageList=explode(",",$goodsAddImg);
							if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
							else $basicImg['0']=remove_ssl_url(BBSE_COMMERCE_PLUGIN_WEB_URL)."images/image_not_exist.jpg";
						}

						$deliveryData=unserialize($oData->order_config);
						if($oData->delivery_total>'0'){ 
							$deveryAdvance=(!$deliveryData['delivery_charge_payment'] || $deliveryData['delivery_charge_payment']=='advance')?"<img src=\"".remove_ssl_url(BBSE_COMMERCE_PLUGIN_WEB_URL)."images/icon_delivery_payment_advance.png\" title=\"배송비 선불 결제\" /><br />":"<span class=\"emRed\"><br /><img src=\"".remove_ssl_url(BBSE_COMMERCE_PLUGIN_WEB_URL)."images/icon_delivery_payment_after.png\" title=\"배송비 후불 결제\" /></span><br />";
						}
						else $deveryAdvance="";
					
						$repStr .="	<tr>
												<td width=\"250\" style=\"padding:10px 10px 8px 10px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">
													<div style=\"display: table;\">
														<div style=\"display: table-cell;padding-right: 10px;\">
															<a href=\"".esc_url( remove_ssl_url(home_url( '/' )) )."?bbseGoods=".$gData->goods_idx."\"><img src=\"".$basicImg['0']."\" width=\"50\" height=\"50\" alt=\"상품 이미지\"></a>
														</div>
														<div style=\"width:220px;display: table-cell;padding-right: 10px;vertical-align: middle;\">
															<a href=\"".esc_url( remove_ssl_url(home_url( '/' )) )."?bbseGoods=".$gData->goods_idx."\" style=\"color:#424242;\">".$gData->goods_name."</a>
														</div>
													</div>
													<ul style=\"padding:0;font-size:11px;color:#888\">";

						$basicOpt=unserialize($gData->goods_option_basic);
						$basicCnt='0';
						for($b=0;$b<sizeof($basicOpt['goods_option_title']);$b++){
							if($basicOpt['goods_option_title'][$b]=="단일상품") $repStr .="<li style=\"padding:0;list-style:none;\">- ".$basicOpt['goods_option_count'][$b]."개</li>";
							else $repStr .="<li style=\"padding:0;list-style:none;\">- ".$basicOpt['goods_option_title'][$b]." (+ ".number_format($basicOpt['goods_option_overprice'][$b])."원) / ".number_format($gData->goods_price+$basicOpt['goods_option_overprice'][$b])."원 * ".$basicOpt['goods_option_count'][$b]."개</li>";

							$basicCnt +=$basicOpt['goods_option_count'][$b];
						}

						$addOpt=unserialize($gData->goods_option_add);
						$addCnt='0';
						if(sizeof($addOpt['goods_add_title'])>'0') $repStr .="<hr style=\"border:0px;height:1px;background:#efefef;\" />";
						for($a=0;$a<sizeof($addOpt['goods_add_title']);$a++){
							$repStr .="<li style=\"padding:0;list-style:none;\">- ".$addOpt['goods_add_title'][$a]." (".number_format($addOpt['goods_add_overprice'][$a])."원) / ".number_format($addOpt['goods_add_overprice'][$a])."원 * ".$addOpt['goods_add_count'][$a]."개</li>";

							$addCnt +=$addOpt['goods_add_count'][$a];
						}

						$repStr .="			</ul>
												</td>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif;text-align:center;\">".$basicCnt."/".$addCnt."</td>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif;text-align:center;\">".number_format($gData->goods_earn*$basicCnt)."원</td>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif;text-align:center;\"><strong>".number_format($gData->goods_basic_total+$gData->goods_add_total)."원</strong></td>
											</tr>";
					}

					$repStr .="	</tbody>
										</table>";

					$deliveryInfoStr="";
					$deliveryChargePayment="advance";
					if($oData->order_config){
						$repStr .="	<div style=\"text-align:left;font-family:'돋움',dotum,sans-serif;font-size:11px;padding:15px;margin:10px 0;border:1px solid #eee;background-color:#fefefe;color:#777777\">
												<strong>배송비 정책안내</strong> <br />";

						$deliveryData=unserialize($oData->order_config);

						if(!$deliveryData['delivery_charge_payment']) $deliveryChargePayment="advance";
						else $deliveryChargePayment=$deliveryData['delivery_charge_payment'];

						if(!$deliveryData['delivery_charge_type'] || $deliveryData['delivery_charge_type']=='free') $repStr .="-배송비 정책 : 무료배송 상품입니다.<br />";
						else{
							$repStr .="-배송비 정책 : 유료배송 상품입니다.<br />";
							 if($deliveryChargePayment=='advance') $repStr .="-기본배송비 : 선불 ".number_format($deliveryData['delivery_charge'])."원<br />";
							 else $repStr .="-기본배송비 : 후불 ".number_format($deliveryData['delivery_charge'])."원<br />";
						}

						if($deliveryData['delivery_charge_type']=='charge' && $deliveryData['condition_free_use']=='on' && $deliveryData['total_pay']>'0') $repStr .="-조건부 무료배송 : 주문 상품의 판매가 기준 총구매금액이 ".number_format($deliveryData['total_pay'])."원 이상인 경우 무료입니다.<br />";

						if(!$oData->delivery_add) $delivery_add='0';
						else $delivery_add=$oData->delivery_add;

						$repStr .="지역별 추가배송비 : ".number_format($delivery_add)."원 <span class=\"emOblique emBlue\">(".$oData->delivery_add_addr.")</span>
											</div>";
					}
					
					if($oData->ezpay_how=='EPN') $ezpayType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_paynow.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
					elseif($oData->ezpay_how=='EKA') $ezpayType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kakaopay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
					elseif($oData->ezpay_how=='EPC') $ezpayType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_payco.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
					elseif($oData->ezpay_how=='EKP') $ezpayType="간편결제 (<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."/images/ezpay_kpay.png' align='absmiddle' alt='".$payHow[$oData->ezpay_how]." 결제' style='height:20px;width:auto;margin-top:3px;' />)";
					else $ezpayType="";

					if($oData->pay_how=='B') $payType="무통장입금";
					elseif($oData->pay_how=='C') $payType="카드결제";
					elseif($oData->pay_how=='K') $payType="실시간계좌이체";
					elseif($oData->pay_how=='V') $payType="가상계좌";

					if($ezpayType) $payType=$ezpayType." - ".$payType;

					$repStr .="	<table summary=\"타이틀명- 결제정보\" style=\"margin-top:30px;width:100%;text-align:left;font-size:12px;\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
										<tbody>
											<tr>
												<th style=\"background:#ffffff;color:#2c2e36;font-size:14px;\" height=\"26\" valign=\"top\" align=\"left\">
													결제정보
												</th>
												<td style=\"background:#ffffff;color:#94959d;font-size:11px;letter-spacing:-1px;\" height=\"26\" align=\"right\"></td>
											</tr>
										</tbody>
										</table>
										<table summary=\"결제정보 테이블입니다.\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"100%;border-top:1px solid #2c2c2c;border-bottom:1px solid #d9d9de;font-size:12px;text-align:left\" width=\"100%\"><tbody>
										<tbody>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">총상품금액</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".number_format($oData->goods_total)."원</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">적립금사용</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">(-) ".number_format($oData->use_earn)."원</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">배송비</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">(+) ".number_format($oData->delivery_total)."원</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">최종결제금액</th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\"><strong style=\"color:#ff0000\">".number_format($oData->cost_total)."원</strong></td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제방법 </th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$payType."</td>
											</tr>";

					if($oData->pay_how=='B'){
						$imputName=($oData->input_name)?$oData->input_name:$oData->order_name;
						$bankInfo=unserialize($oData->pay_info);
						$repStr .="	<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">입금자명 </th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$imputName."</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">입금계좌 </th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$bankInfo['bank_name']." ".$bankInfo['bank_no']." (예금주 : ".$bankInfo['bank_owner'].")</td>
											</tr>";
					}
					else{
						// 결제모듈 설정
						$paymentConfig = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'");
						if(!$paymentConfig) {
							echo "<script>alert('관리자에서 결제모듈 설정을 먼저 해주세요');location.href='".home_url()."/';</script>";
							exit;
						}

						$payCFG = unserialize($paymentConfig);
						$pg_kind = ($payCFG['payment_agent'])?$payCFG['payment_agent']:"allthegate";

						if(!$oData->ezpay_how && ($oData->pay_how=='C' || $oData->pay_how=='K' || $oData->pay_how=='V') && $pg_kind=='allthegate'){ // 올더게이트
							$agsData=$wpdb->get_row("SELECT * FROM bbse_commerce_pg_agspay WHERE rOrdNo='".$oData->order_no."'");

							if($oData->pay_how=='C'){
								$cardName=bbse_commerce_get_card_name($agsData->rCardCd*1,"allthegate"); // 카드종류
								$appNo=$agsData->rApprNo;// 승인번호
								$dealNo=$agsData->rDealNo;//거래번호
								$applyTime=substr($agsData->rApprTm,0,4).".".substr($agsData->rApprTm,4,2).".".substr($agsData->rApprTm,6,2)." ".substr($agsData->rApprTm,8,2).":".substr($agsData->rApprTm,10,2).":".substr($agsData->rApprTm,12,2);

								if($agsData->ES_SENDNO) $escroNo=", 에스크로 주문번호 : ".$agsData->ES_SENDNO; // 에스크로 번호
								else $escroNo="";

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">결제(승인)완료 (".$cardName." 카드 , 승인번호 : ".$appNo.", 거래번호 : ".$dealNo.$escroNo.")</td>
												</tr>
												<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">승인완료 시간 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime."</td>
												</tr>";
							}
							elseif($oData->pay_how=='K'){
								if($agsData->ES_SENDNO) $escroNo="(에스크로 주문번호".$agsData->ES_SENDNO.")"; // 에스크로 번호
								else $escroNo="";

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">결제(이체)완료 (".$escroNo.")</td>
												</tr>";
							}
							elseif($oData->pay_how=='V'){
								$bankName=bbse_commerce_get_vbank_name($agsData->VIRTUAL_CENTERCD); // 가상계좌 은행명
								$bankNo=$agsData->rVirNo;

								if($agsData->ES_SENDNO) $escroNo=$agsData->ES_SENDNO; // 에스크로 번호
								else $escroNo="";
								$applyTime=substr($agsData->rApprTm,0,4).".".substr($agsData->rApprTm,4,2).".".substr($agsData->rApprTm,6,2)." ".substr($agsData->rApprTm,8,2).":".substr($agsData->rApprTm,10,2).":".substr($agsData->rApprTm,12,2);
								$endInputTime=mktime('23','59','59',substr($agsData->rApprTm,4,2),substr($agsData->rApprTm,6,2)+5,substr($agsData->rApprTm,0,4));

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$bankName." (계좌번호 : ".$bankNo.")</td>
												</tr>
												<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">가상계좌 발급일자 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime." (입금예정 기한 : ".date("Y.m.d H:i:s",$endInputTime).")</td>
												</tr>";
								 if($escroNo){
									$repStr .="<tr>
														<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">에스크로 주문번호 </th>
														<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$escroNo."</td>
													</tr>";
								 }
							}
							elseif($oData->pay_how=='H'){
								$telecomName=$agsData->rHP_COMPANY; // 통신사명(SKT,KTF,LGT)
								$mobileNO=$agsData->rHP_HANDPHONE; // 핸드폰 번호
								$mobileTID=$agsData->rHP_TID; // 핸드폰 결제 TID
								$applyTime=substr($agsData->rHP_DATE,0,4).".".substr($agsData->rHP_DATE,4,2).".".substr($agsData->rHP_DATE,6,2); // 핸드폰 결제일

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$telecomName." (휴대전화 : ".$mobileNO.", TID : ".$mobileTID.")</td>
												</tr>
												<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">승인완료 시간 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime."</td>
												</tr>";
							 }
						}
						elseif((!$oData->ezpay_how && ($oData->pay_how=='C' || $oData->pay_how=='K' || $oData->pay_how=='V') && $pg_kind=='INIpay50') || $oData->ezpay_how=='EKP'){ // 이니시스

							$iniData=$wpdb->get_row("SELECT * FROM bbse_commerce_pg_inicis WHERE MOID='".$oData->order_no."'");

							if($oData->pay_how=='C'){
								$cardName=bbse_commerce_get_card_name($iniData->CARD_Code,"INIpay50"); // 카드종류
								$appNo=$iniData->ApplNum;// 승인번호

								$applyTime=substr($iniData->ApplDate,0,4).".".substr($iniData->ApplDate,4,2).".".substr($iniData->ApplDate,6,2)." ".substr($iniData->ApplTime,0,2).":".substr($iniData->ApplTime,2,2).":".substr($iniData->ApplTime,4,2);

								$escroNo=""; // 에스크로 번호

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">결제(승인)완료 (".$cardName." 카드 , 승인번호 : ".$appNo.")</td>
												</tr>
												<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">승인완료 시간 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime."</td>
												</tr>";
							}
							elseif($oData->pay_how=='K'){
								if($pay_info->ACCT_BankCode!="") $bankName=bbse_commerce_get_vbank_name($iniData->ACCT_BankCode); 
								else $bankName="";

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">결제(이체)완료 (".$bankName.")</td>
												</tr>";
							}
							elseif($oData->pay_how=='V'){
								$bankName=bbse_commerce_get_vbank_name($iniData->VACT_BankCode); // 가상계좌 은행명
								$bankNo=$iniData->VACT_Num;
								$escroNo="";

								$applyTime=substr($iniData->ApplDate,0,4).".".substr($iniData->ApplDate,4,2).".".substr($iniData->ApplDate,6,2)." ".substr($iniData->ApplTime,0,2).":".substr($iniData->ApplTime,2,2).":".substr($iniData->ApplTime,4,2);
								$endInputTime=substr($iniData->VACT_Date,0,4).".".substr($iniData->VACT_Date,4,2).".".substr($iniData->VACT_Date,6,2);

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$bankName." (계좌번호 : ".$bankNo.")</td>
												</tr>
												<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">가상계좌 발급일자 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime." (입금예정 기한 : ".$endInputTime.")</td>
												</tr>";
								 if($escroNo){
									$repStr .="<tr>
														<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">에스크로 주문번호 </th>
														<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$escroNo."</td>
													</tr>";
								 }
							}
						}
						elseif((!$oData->ezpay_how && ($oData->pay_how=='C' || $oData->pay_how=='K' || $oData->pay_how=='V') && $pg_kind=='lguplusXPay') || $oData->ezpay_how=='EPN'){ // LG U+

							$xpayData=$wpdb->get_row("SELECT * FROM bbse_commerce_pg_uplus WHERE LGD_OID='".$oData->order_no."'");

							if($oData->pay_how=='C'){

								$cardName=$xpayData->LGD_FINANCENAME; // 카드종류
								$appNo=$xpayData->LGD_FINANCEAUTHNUM;// 승인번호

								$applyTime=substr($xpayData->LGD_PAYDATE,0,4).".".substr($xpayData->LGD_PAYDATE,4,2).".".substr($xpayData->LGD_PAYDATE,6,2)." ".substr($xpayData->LGD_PAYDATE,0,2).":".substr($xpayData->LGD_PAYDATE,2,2).":".substr($xpayData->LGD_PAYDATE,4,2);

								$escroNo=""; // 에스크로 번호

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">결제(승인)완료 (".$cardName." 카드 , 승인번호 : ".$appNo.")</td>
												</tr>
												<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">승인완료 시간 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime."</td>
												</tr>";
							}
							elseif($oData->pay_how=='K'){
								if($xpayData->LGD_FINANCENAME!="") $bankName=$xpayData->LGD_FINANCENAME; 
								else $bankName="";

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">결제(이체)완료 (".$bankName.")</td>
												</tr>";
							}
							elseif($oData->pay_how=='V'){
								$bankName=$xpayData->LGD_FINANCENAME; // 가상계좌 은행명
								$bankNo=$xpayData->LGD_ACCOUNTNUM;
								$escroNo="";

								$applyTime=substr($xpayData->LGD_PAYDATE,0,4).".".substr($xpayData->LGD_PAYDATE,4,2).".".substr($xpayData->LGD_PAYDATE,6,2)." ".substr($xpayData->LGD_PAYDATE,8,2).":".substr($xpayData->LGD_PAYDATE,10,2).":".substr($xpayData->LGD_PAYDATE,12,2);
								$endInputTime=substr($xpayData->LGD_CLOSEDATE,0,4).".".substr($xpayData->LGD_CLOSEDATE,4,2).".".substr($xpayData->LGD_CLOSEDATE,6,2)." ".substr($xpayData->LGD_CLOSEDATE,8,2).":".substr($xpayData->LGD_CLOSEDATE,10,2).":".substr($xpayData->LGD_CLOSEDATE,12,2);

								$repStr .="<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$bankName." (계좌번호 : ".$bankNo.", 입금자명 : ".$xpayData->LGD_PAYER.")</td>
												</tr>
												<tr>
													<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">가상계좌 발급일자 </th>
													<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime." (입금예정 기한 : ".$endInputTime.")</td>
												</tr>";
								 if($escroNo){
									$repStr .="<tr>
														<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">에스크로 주문번호 </th>
														<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$escroNo."</td>
													</tr>";
								 }
							}
						}
						elseif($oData->pay_how=='C' && $oData->ezpay_how=='EKA'){ // KakaoPat
							$kakaData=$wpdb->get_row("SELECT * FROM bbse_commerce_pg_kakaopay WHERE order_no='".$oData->order_no."'");
							$cardName=bbse_commerce_get_card_name($kakaData->cardCode,"kakaopay"); // 카드종류
							$appNo=$kakaData->authCode;// 승인번호

							$applyTime="20".substr($kakaData->authDate,0,2).".".substr($kakaData->authDate,2,2).".".substr($kakaData->authDate,4,2)." ".substr($kakaData->authDate,6,2).":".substr($kakaData->authDate,8,2).":".substr($kakaData->authDate,10,2);

							$escroNo=""; // 에스크로 번호

							$repStr .="<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">결제정보 </th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">결제(승인)완료 (".$cardName." 카드 , 승인번호 : ".$appNo.")</td>
											</tr>
											<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">승인완료 시간 </th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".$applyTime."</td>
											</tr>";

						}
					}

					$repStr .="		<tr>
												<th width=\"100\" valign=\"middle\" align=\"left\" style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;background-color:#f6f6f7;font-weight:normal;\">예상적립금 </th>
												<td style=\"padding:10px 10px 8px 20px;border-top:1px solid #DBDBDB;color:#000;font-family:Verdana,Geneva,Tahoma,sans-serif\">".number_format($oData->add_earn)."원</td>
											</tr>
										</tbody>
										</table>";

						$rtnData['to']=$oData->order_email;
						$rtnData['subject']=str_replace("{성명}",$oData->order_name,$rtnData['subject']);

						$rtnData['message']=str_replace("{성명}",$oData->order_name,$rtnData['message']);
						$rtnData['message']=str_replace("{사이트이름}",get_bloginfo('name'),$rtnData['message']);
						$rtnData['message']=str_replace("{마이페이지링크}",remove_ssl_url(get_bloginfo('url'))."?bbseMy=mypage",$rtnData['message']);
						$rtnData['message']=str_replace("{주문일자}",date("Y년 m월 d일",$oData->order_date),$rtnData['message']);
						$rtnData['message']=str_replace("{배송중일자시간}",date("Y-m-d H:i:s",$oData->delivery_ing_date),$rtnData['message']);
						$rtnData['message']=str_replace("{반품완료일자시간}",date("Y-m-d H:i:s",$oData->refund_end_date),$rtnData['message']);



						$rtnData['message']=str_replace("{주문정보내용}",$repStr,$rtnData['message']);
				}
				else $rtnData['message']=$rtnData['subject']="";
			}
		}

		return $rtnData;
	}
}

// 메일타입 재정의 : text/html
if(!function_exists('bbse_commerce_set_html_content_type')){
	function bbse_commerce_set_html_content_type() {
		return 'text/html';
	}
}

/* 메일발송
bbse_commerce_mail_send(Type,uniqueCode, tempPass) => 
 - Type=join(회원가입), find-pw(비밀번호 찾기), order-ready(입금대기), order-input(결제완료), order-shipment(배송중), order-cancel(취소완료), order-refund(반품완료);
 - uniqueCode :  회원아이디/주문번호
 - tempPass : 임시비밀번호
 - return 1, 0
*/
if(!function_exists('bbse_commerce_mail_send')){
	function bbse_commerce_mail_send($mailType,$uniData,$etcData){
		$headers = "From: ".get_bloginfo('name')." <".get_bloginfo('admin_email').">\r\n";   // 보내는 사람
		$mailData=bbse_commerce_get_mail_content($mailType,$uniData,$etcData);            // 메일내용
		$attachments="";                                                                                                  // 첨부파일

		if($mailData['to'] && $mailData['subject'] && $mailData['message']){
			add_filter( 'wp_mail_content_type', 'bbse_commerce_set_html_content_type' );
			$status = wp_mail( $mailData['to'], $mailData['subject'], $mailData['message'], $headers, $attachments );
			remove_filter( 'wp_mail_content_type', 'bbse_commerce_set_html_content_type' ); // 충돌을 방지하기 위해 콘텐츠 유형을 재설정 -- http://core.trac.wordpress.org/ticket/23578
		}

		return $status;
	}
}

if(!function_exists('bbse_commerce_csv_delete')){
	function bbse_commerce_csv_delete ($filename){ 
		$tdir=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce";

		if($dh = opendir ($tdir)) { 
			$files = Array(); 
			$in_files = Array(); 
			while($a_file = readdir ($dh)) { 
				if($a_file['0'] != '.' && $a_file['0'] != "..") {
					if (strtolower(substr($a_file, strrpos($a_file, '.') + 1)) == 'csv' && $filename!=$a_file){
						@unlink($tdir."/".$a_file);
					}

				} 
			} 
			closedir ($dh); 
		} 
	} 
}

// CSV 파일 검사
if(!function_exists('bbse_commerce_csv_check')){
	function bbse_commerce_csv_check($filename='', $delimiter=','){
		bbse_commerce_csv_delete($filename); // CSV 파일 삭제, 현재 파일 제외

		$tFile=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$filename;
		if(!file_exists($tFile) || !is_readable($tFile)){
			return FALSE;
		}

		$header = NULL;
		$cvsData = array();
		$rowCnt=0;
		if (($handle = fopen($tFile, 'r')) !== FALSE){
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE){
				if(!$header){
					$header = $row;
					$cvsData['fieldCnt'] = sizeof($header);
				}
				else $rowCnt++;
			}
			fclose($handle);
			$cvsData['rowCnt'] = $rowCnt;
		}
		return $cvsData;
	}
}

// CSV to Array
if(!function_exists('bbse_commerce_csv_to_array')){
	function bbse_commerce_csv_to_array($filename='', $delimiter=','){
		$tFile=BBSE_COMMERCE_UPLOAD_BASE_PATH."bbse-commerce/".$filename;
 		if(!file_exists($tFile) || !is_readable($tFile)){
			return FALSE;
		}

		$cvsData = array();
		$rowCnt=0;
		if (($handle = fopen($tFile, 'r')) !== FALSE){
			while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE){
				if($rowCnt>0){
					foreach($row as $key => $value){
						$row[$key]=iconv("euc-kr","utf-8",$value);
					}

					$cvsData[] = $row;
				}
				$rowCnt++;
			}
			fclose($handle);
		}
		return $cvsData;
	}
}

// DB insert (goods)
if(!function_exists('bbse_commerce_goods_array_in_db')){
	function bbse_commerce_goods_array_in_db($tData,$tCategory){
		global $wpdb;

		$goods_display="hidden";
		$goods_cat_list="|".$tCategory."|";
		$rtnArray=Array();

		$goods_update_date=current_time('timestamp');

		$errCnt_goods_name=0;
		$errCnt_zero_consumer_price=0;
		$errCnt_zero_goods_price=0;
		$errCnt_low_goods_price=0;
		$errCnt_null_goods_img=0;

		for($i=0;$i<sizeof($tData);$i++){
			$goods_reg_date=$goods_update_date+$i;

			$goods_code="";
			$goods_name=addslashes(trim($tData[$i]['0'])); // 상품명
			$goods_detail=addslashes(trim($tData[$i]['14'])); // 상품상세설명
			$goods_unique_code=trim($tData[$i]['1']); // 고유번호
			$goods_barcode=trim($tData[$i]['2']); // 바코드
			$goods_location_no=trim($tData[$i]['3']); // 위치정보
			$goods_company=addslashes(trim($tData[$i]['4'])); // 제조사
			$goods_company_display="view"; // 제조사 노출여부
			$goods_local=addslashes(trim($tData[$i]['5'])); // 원산지
			$goods_local_display="view"; // 원산지 노출여부

			$goods_count=trim($tData[$i]['6']); // 재고수량
			if($goods_count>0){
				$goods_count_flag="goods_count"; // 재고설정 (재고수량)
				$goods_count_view="on"; // 재고수량 노출
			}
			else{
				$goods_count="";
				$goods_count_flag="unlimit"; // 재고설정 (무제한)
				$goods_count_view=""; // 재고수량 노출
			}

			$goods_consumer_price=trim($tData[$i]['7']); // 소비자가
			$goods_price=trim($tData[$i]['8']); // 판매가		
			$goods_earn=trim($tData[$i]['9']); // 적립금
			if($goods_earn>0) $goods_earn_use="on"; // 적립금 사용여부
			else $goods_earn_use="off";
			$goods_img=trim($tData[$i]['10']); // 상품이미지

			$goods_seo_title=addslashes(trim($tData[$i]['11'])); // SEO 타이틀
			$goods_seo_description=addslashes(trim($tData[$i]['12'])); // SEO 설명
			$goods_seo_keyword=addslashes(trim($tData[$i]['13'])); // SEO 키워드

			if($goods_seo_title && $goods_seo_description && $goods_seo_keyword)  $goods_seo_use="on"; // SEO 사용여부
			else $goods_seo_use="off";

			if(!$goods_name){
				$errCnt_goods_name ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			if($goods_consumer_price<=0){
				$errCnt_zero_consumer_price ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			if($goods_price<=0){
				$errCnt_zero_goods_price ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			if($goods_consumer_price<$goods_price){
				$errCnt_low_goods_price ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			if(!$goods_img){
				$errCnt_null_goods_img ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			$inQuery="INSERT INTO bbse_commerce_goods (goods_code, goods_name, goods_display, goods_cat_list, goods_detail, goods_unique_code, goods_barcode, goods_location_no, goods_company, goods_company_display, goods_local, goods_local_display, goods_count, goods_count_flag, goods_count_view, goods_consumer_price, goods_price, goods_seo_use, goods_seo_title, goods_seo_description, goods_seo_keyword, goods_earn_use, goods_earn, goods_update_date, goods_reg_date) VALUES ('".$goods_code."', '".$goods_name."', '".$goods_display."', '".$goods_cat_list."', '".$goods_detail."', '".$goods_unique_code."', '".$goods_barcode."', '".$goods_location_no."', '".$goods_company."', '".$goods_company_display."', '".$goods_local."', '".$goods_local_display."', '".$goods_count."', '".$goods_count_flag."', '".$goods_count_view."', '".$goods_consumer_price."', '".$goods_price."', '".$goods_seo_use."', '".$goods_seo_title."', '".$goods_seo_description."', '".$goods_seo_keyword."', '".$goods_earn_use."', '".$goods_earn."', '".$goods_update_date."', '".$goods_reg_date."')";

			$wpdb->query($inQuery);
			$idx = $wpdb->insert_id;

			$goods_code=$goods_reg_date."-".$idx;
			$result=$wpdb->query("UPDATE bbse_commerce_goods SET goods_code='".$goods_code."' WHERE idx='".$idx."'");

			$tmpQuery="INSERT INTO bbse_commerce_csv_goods (goods_idx, goods_code, goods_img) VALUES ('".$idx."', '".$goods_code."', '".$goods_img."')";
			$wpdb->query($tmpQuery);
		}

		$rtnArray['errCnt_goods_name']=$errCnt_goods_name;
		$rtnArray['errCnt_zero_consumer_price']=$errCnt_zero_consumer_price;
		$rtnArray['errCnt_zero_goods_price']=$errCnt_zero_goods_price;
		$rtnArray['errCnt_low_goods_price']=$errCnt_low_goods_price;
		$rtnArray['errCnt_null_goods_img']=$errCnt_null_goods_img;

		return $rtnArray;
	}
}


// DB insert (stroage)
if(!function_exists('bbse_commerce_storage_array_in_db')){
    function bbse_commerce_storage_array_in_db($tData){
        global $wpdb;
        
        $reg_date=current_time('timestamp');
        
        $reg_date = date("Y-m-d H:m:s", $reg_date);
        
        $sidoEn=Array("서울"=>"SE","부산"=>"BS","대구"=>"DG","인천"=>"IC","광주"=>"GJ","대전"=>"DJ","울산"=>"US","세종"=>"SJ","경기"=>"GG","강원특별자치도"=>"GW","충북"=>"CB","충남"=>"CN","전북"=>"JB","전남"=>"JN","경북"=>"GB","경남"=>"GN","제주특별자치도"=>"JJ");
        $sigunguEn=Array("종로구"=>"DJ","중구"=>"DJ","용산구"=>"DY","성동구"=>"DS","광진구"=>"DG","동대문구"=>"DD","중랑구"=>"DJ","성북구"=>"DS","강북구"=>"DK","도봉구"=>"DB","노원구"=>"DN","은평구"=>"DE","서대문구"=>"DS","마포구"=>"DM","양천구"=>"DY","강서구"=>"DK","구로구"=>"DG"
            ,"금천구"=>"DG","영등포구"=>"DY","동작구"=>"DD","관악구"=>"DG","서초구"=>"DS","강남구"=>"DK","송파구"=>"DS","강동구"=>"DK","서구"=>"DS","동구"=>"DD","영도구"=>"DY","부산진구"=>"DB","동래구"=>"DD","남구"=>"DN","북구"=>"DB","해운대구"=>"DH","사하구"=>"DS","금정구"=>"DG"
            ,"연제구"=>"DY","수영구"=>"DS","사상구"=>"DS","기장군"=>"GK","수성구"=>"DS","달성군"=>"GD","남동구"=>"DN","부평구"=>"DB","계양구"=>"DG"
            ,"강화군"=>"DJ","옹진군"=>"DJ","광산구"=>"DJ","유성구"=>"DJ","대덕구"=>"DJ","울주군"=>"DJ","조치원읍"=>"DJ","연기면"=>"DJ","연동면"=>"DJ","부강면"=>"DJ","금남면"=>"DJ","달서구"=>"DJ","미추홀구"=>"DJ","연수구"=>"DJ","수원시"=>"SS","의정부시"=>"SU","안양시"=>"SA","성남시"=>"SN"
            ,"부천시"=>"SB","광명시"=>"SG","평택시"=>"SP","동두천시"=>"SD","안산시"=>"SA","고양시"=>"SG","과천시"=>"SC","구리시"=>"SG","남양주시"=>"SN","오산시"=>"SO","시흥시"=>"SH","군포시"=>"SG","의왕시"=>"SW","하남시"=>"SH","용인시"=>"SY","파주시"=>"SP","이천시"=>"SI","안성시"=>"SA"
            ,"김포시"=>"SK","화성시"=>"SH","광주시"=>"SG","양주시"=>"SY","포천시"=>"SP","여주시"=>"SY","춘천시"=>"SC","원주시"=>"SW","강릉시"=>"SK","동해시"=>"SD","태백시"=>"ST","속초시"=>"SS","삼척시"=>"SM","청주시"=>"SC","충주시"=>"SC","제천시"=>"SJ","천안시"=>"SC","공주시"=>"SG"
            ,"보령시"=>"SB","아산시"=>"SA","서산시"=>"SS","논산시"=>"SN","계룡시"=>"SK","전주시"=>"SJ","군산시"=>"SG","익산시"=>"SI","정읍시"=>"SJ","남원시"=>"SN","김제시"=>"SK","목포시"=>"SM","여수시"=>"SY","순천시"=>"SS","나주시"=>"SN","광양시"=>"SG","포항시"=>"SP","경주시"=>"SG"
            ,"김천시"=>"SK","안동시"=>"SA","구미시"=>"SG","영천시"=>"SY","상주시"=>"SS","문경시"=>"SM","경산시"=>"SG","창원시"=>"SC","진주시"=>"SJ","통영시"=>"ST","사천시"=>"SS","김해시"=>"SK","밀양시"=>"SM","거제시"=>"SG","양산시"=>"SY","영주시"=>"SY","당진시"=>"SD","서귀포시"=>"SS"
        );
        
        $rtnArray=Array();
        $errCnt_null_id=0;
        $errCnt_duplicate_id=0;
        $errCnt_null_managerId = 0;
        $errCnt_null_storageName = 0;
        //$errCnt_null_businessNumber = 0;
        $errCnt_null_zipcode = 0;
        $errCnt_null_addr1 = 0;
        $errCnt_null_addr2 = 0;
        
        for($i=0;$i<sizeof($tData);$i++){
            $storage_name=trim($tData[$i]['0']); // 창고명
            //$business_number=trim($tData[$i]['1']); // 사업자코드
            $zipcode=trim($tData[$i]['1']); // 우편번호
            $addr1=trim($tData[$i]['2']); // 주소1
            $addr2=trim($tData[$i]['3']); // 주소2
            $user_id=trim($tData[$i]['4']); // 담당자id
            
            $parts = explode(" ", $addr1);
            $sido = $sidoEn[$parts[0]];
            $sigugin = $sigunguEn[$parts[1]];
            
            
            //$storageCnt = $wpdb->get_var("SELECT count(*) FROM tbl_storage WHERE storage_code='".$storage_code	."' and business_number='".$business_number."'");
            
/*             if($storageCnt>0){
                $errCnt_duplicate_id ++;
                $rtnArray[]=$tData[$i];
                continue;
            } */
            
            if(!$storage_name){
                $errCnt_null_storageName ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
/*             if(!$business_number){
                $errCnt_null_businessNumber ++;
                $rtnArray[]=$tData[$i];
                continue;
            } */
            
            if(!$user_id){
                $errCnt_null_managerId ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            if(!$zipcode){
                $errCnt_null_zipcode++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            if(!$addr1){
                $errCnt_null_addr1++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            if(!$addr2){
                $errCnt_null_addr2++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            $inQuery="INSERT INTO tbl_storage (storage_name,user_id,zipcode,addr1,addr2,reg_date,delete_yn) VALUES ('".$storage_name."','".$user_id."', '".$zipcode."' , '".$addr1."', '".$addr2."', '".$reg_date."','N')";
            
            $wpdb->query($inQuery);
            $idx = $wpdb->insert_id;
            
            $storage_code=$sido."-".$sigugin."-".$idx;
            
            if($idx > '0'){
                $result=$wpdb->query("UPDATE `tbl_storage` SET storage_code = '".$storage_code."' WHERE idx='".$idx."'");
            }
        }
        
        $rtnArray['errCnt_null_id']=$errCnt_null_id;
        $rtnArray['errCnt_duplicate_id']=$errCnt_duplicate_id;
        $rtnArray['errCnt_null_storageName']=$errCnt_null_storageName;
        $rtnArray['errCnt_null_businessNumber']=$errCnt_null_businessNumber;
        $rtnArray['errCnt_null_zipcode']=$errCnt_null_zipcode;
        $rtnArray['errCnt_null_addr1']=$errCnt_null_addr1;
        $rtnArray['errCnt_null_addr2']=$errCnt_null_addr2;
        $rtnArray['errCnt_null_managerId']=$errCnt_null_managerId;
        
        return $rtnArray;
    }
}


function generateCode($number) {
    return str_pad($number, 5, "0", STR_PAD_LEFT);
}

// DB insert (inven)
if(!function_exists('bbse_commerce_inven_array_in_db')){
    function bbse_commerce_inven_array_in_db($tData){
        global $wpdb;
        
        $rtnArray=Array();
        $errCnt_null_id=0;
        $errCnt_duplicate_id=0;
        //$errCnt_null_goodsCode=0;
        $errCnt_null_currnetCount = 0;
        $errCnt_null_noticeCount = 0;
        //$errCnt_null_managerId = 0;
        $errCnt_null_storageCode = 0;
        
        $timeStamp=current_time('timestamp');
        
        $timeStamp=date("Y-m-d H:i:s",$timeStamp);
        
        for($i=0;$i<sizeof($tData);$i++){
            $goods_name=trim($tData[$i]['0']); // 제품명
            //$goods_code=trim($tData[$i]['1']); // 제품코드
            $douzone_code=trim($tData[$i]['1']); // 더존코드
            $goods_option_title=trim($tData[$i]['2']); // 더존코드
            $current_count=trim($tData[$i]['3']); // 현재수량
            $notice_count=trim($tData[$i]['4']); // 알림수량
            $storage_code=trim($tData[$i]['5']); // 창고코드
            //$manager_id=trim($tData[$i]['5']); // 담당자id
            
            if(!$goods_name){
                $errCnt_null_id ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            //$goodsCnt = $wpdb->get_var("SELECT count(*) FROM tbl_inven WHERE goods_code='".$goods_code	."' and storage_code='".$storage_code."'");
            
            /*if($goodsCnt>0){
                $errCnt_duplicate_id ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            */
            
            /*if(!$goods_code){
                $errCnt_null_goodsCode ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            */
             
            if($current_count < 0){
                $errCnt_null_currnetCount ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            if($notice_count < 0){
                $errCnt_null_noticeCount ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
/*             if(!$manager_id){
                $errCnt_null_managerId ++;
                $rtnArray[]=$tData[$i];
                continue;
            } */
            
            if(!$storage_code){
                $errCnt_null_storageCode ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            $maxNumber = $wpdb->get_var("select max(goods_code) from  `autopole3144`.`tbl_inven`");
            $goodsCode = generateCode($maxNumber + 1);
            $manager_id = $wpdb->get_var("select manager_id from tbl_storage where storage_code = '".$storage_code."' and delete_yn != 'N'");
            
            $goods_idx = 
            
            $inQuery="INSERT INTO tbl_inven (goods_code,goods_name,storage_code, current_count,notice_count,reg_date,manager_id,delete_yn,goods_option_title) VALUES ('".$goodsCode."', '".$goods_name."', '".$storage_code."', '".$current_count."', '".$notice_count."', '".$timeStamp."' , '".$manager_id."','N','".$goods_option_title."')";
            $wpdb->query($inQuery);
            $idx = $wpdb->insert_id;
            
            //더존코드 등록
            $sql = "INSERT INTO tbl_douzone_code (goods_douzone_code, goods_code, delete_yn, reg_date) VALUES ('".$douzone_code."','".$goodsCode."','N','".$timeStamp."')";
            $result = $wpdb->query($sql);
            
        }
        
        $rtnArray['errCnt_null_id']=$errCnt_null_id;
        $rtnArray['errCnt_duplicate_id']=$errCnt_duplicate_id;
        //$rtnArray['errCnt_null_goodsCode']=$errCnt_null_goodsCode;
        $rtnArray['errCnt_null_currnetCount']=$errCnt_null_currnetCount;
        $rtnArray['errCnt_null_noticeCount']=$errCnt_null_noticeCount;
        //$rtnArray['errCnt_null_managerId']=$errCnt_null_managerId;
        
        return $rtnArray;
    }
}

// DB insert (location)
if(!function_exists('bbse_commerce_location_array_in_db')){
    function bbse_commerce_location_array_in_db($tData){
        global $wpdb;
        
        $rtnArray=Array();
        $errCnt_null_id=0;
        $errCnt_duplicate_id=0;
        $errCnt_null_rack_code = 0;
        $errCnt_null_location_y = 0;
        $errCnt_null_location_x = 0;
        $errCnt_null_storageCode = 0;
        
        $timeStamp=current_time('timestamp');
        $timeStamp=date("Y-m-d H:i:s",$timeStamp);
        
        for($i=0;$i<sizeof($tData);$i++){
            $storage_code=trim($tData[$i]['0']); //창고코드
            $rack_code=trim($tData[$i]['1']); // 랙코드
            $location_x=trim($tData[$i]['2']); // 층
            $location_y=trim($tData[$i]['3']); // 높이
            
            $location = $wpdb->get_var("SELECT count(*) FROM tbl_locations WHERE rack_code='".$rack_code."' and location_x='".$location_x."' and location_y='".$location_y."' and storage_code = '".$storage_code."'");
            
            if($location>0){
                $errCnt_duplicate_id ++;
                 $rtnArray[]=$tData[$i];
                 continue;
            }
            
            if($rack_code < 0){
                $errCnt_null_rack_code ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            if($location_x < 0){
                $errCnt_null_location_x ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            if($location_y < 0){
                $errCnt_null_location_y ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            if(!$storage_code){
                $errCnt_null_storageCode ++;
                $rtnArray[]=$tData[$i];
                continue;
            }
            
            $inQuery="INSERT INTO tbl_locations (storage_code,rack_code,location_x, location_y,delete_yn,reg_date) VALUES ('".$storage_code."', '".$rack_code."', '".$location_x."', '".$location_y."', 'N', '".$timeStamp."')";
            $result = $wpdb->query($inQuery);
        }
        
        $rtnArray['errCnt_null_id']=$errCnt_null_id;
        $rtnArray['errCnt_duplicate_id']=$errCnt_duplicate_id;
        $rtnArray['$errCnt_null_rack_code']=$errCnt_null_rack_code;
        $rtnArray['$errCnt_null_location_y']=$errCnt_null_location_y;
        $rtnArray['$errCnt_null_location_x']=$errCnt_null_location_x;
        $rtnArray['$$errCnt_null_storageCode']=$errCnt_null_storageCode;
        
        return $rtnArray;
    }
}

// DB insert (member)
if(!function_exists('bbse_commerce_member_array_in_db')){
	function bbse_commerce_member_array_in_db($tData,$tPwType,$tPwDirect){
		global $wpdb;

		$user_class='2';
		$reg_date=current_time('timestamp');

		$rtnArray=Array();
		$errCnt_null_id=0;
		$errCnt_duplicate_id=0;
		$errCnt_null_name=0;
		$errCnt_null_password=0;
		$errCnt_long_password=0;

		for($i=0;$i<sizeof($tData);$i++){
			$user_id=trim($tData[$i]['0']); // 회원아이디
			$tmp_user_pass=trim($tData[$i]['1']); // 비밀번호
			$name=trim($tData[$i]['2']); // 이름
			$birth=trim($tData[$i]['11']); // 생년월일
			$sex=trim($tData[$i]['12']); // 성별
			$zipcode=trim($tData[$i]['8']); // 우편번호
			$addr1=trim($tData[$i]['9']); // 주소
			$addr2=trim($tData[$i]['10']); // 상세주소
			$email=trim($tData[$i]['3']); // 이메일
			$phone=trim($tData[$i]['5']); // 전화번호
			$hp=trim($tData[$i]['6']); // 핸드폰 번호
			$job=trim($tData[$i]['13']); // 직업
			$email_reception=trim($tData[$i]['4']); // 이메일 수신여부
			$sms_reception=trim($tData[$i]['7']); // SMS 수신여부
			$mileage=trim($tData[$i]['14']); // 적립금
			$admin_log=trim($tData[$i]['15']); // 관리자메모

			if(!$user_id){
				$errCnt_null_id ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			$memCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_membership WHERE user_id='".$user_id	."'");
			if($memCnt>0){
				$errCnt_duplicate_id ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			if(!$name){
				$errCnt_null_name ++;
				$rtnArray[]=$tData[$i];
				continue;
			}

			if($tPwType=='phone'){
				if(!$phone){
					$errCnt_null_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				elseif(strlen($phone)>16){
					$errCnt_long_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				$user_pass=$phone; // 전화번호
			}
			elseif($tPwType=='hp'){
				if(!$hp){
					$errCnt_null_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				elseif(strlen($hp)>16){
					$errCnt_long_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				$user_pass=$hp; // 휴대폰 번호
			}
			elseif($tPwType=='field'){
				if(!$tmp_user_pass){
					$errCnt_null_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				elseif(strlen($tmp_user_pass)>16){
					$errCnt_long_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				$user_pass=$tmp_user_pass; // 비밀번호
			}
			elseif($tPwType=='direct'){
				if(!$tPwDirect){
					$errCnt_null_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				elseif(strlen($tPwDirect)>16){
					$errCnt_long_password ++;
					$rtnArray[]=$tData[$i];
					continue;
				}
				$user_pass=$tPwDirect;
			}

			$inQuery="INSERT INTO bbse_commerce_membership (user_class, user_id, user_pass, name, birth, sex, zipcode, addr1, addr2, email, phone, hp, job, email_reception, sms_reception, mileage, reg_date, admin_log) VALUES ('".$user_class."', '".$user_id."', password('".$user_pass."'), '".$name."', '".$birth."', '".$sex."' , '".$zipcode."', '".$addr1."', '".$addr2."', '".$email."', '".$phone."', '".$hp."', '".$job."', '".$email_reception."', '".$sms_reception."', '".$mileage."', '".$reg_date."', '".$admin_log."')";

			$wpdb->query($inQuery);
			$idx = $wpdb->insert_id;

			if($idx){
				wp_create_user($user_id, $user_pass, $email); // 워드프레스 회원 정보 저장
			}
		}

		$rtnArray['errCnt_null_id']=$errCnt_null_id;
		$rtnArray['errCnt_duplicate_id']=$errCnt_duplicate_id;
		$rtnArray['errCnt_null_name']=$errCnt_null_name;
		$rtnArray['errCnt_null_password']=$errCnt_null_password;
		$rtnArray['errCnt_long_password']=$errCnt_long_password;

		return $rtnArray;
	}
}


// error data CSV download
if(!function_exists('bbse_commerce_array_to_csv_download')){
	function bbse_commerce_array_to_csv_download($array, $filename = "bbse_error.csv", $delimiter=",") {
		$f = fopen('php://memory', 'w'); 
		foreach ($array as $line) { 
			$cLine=Array();
			for($z=0;$z<sizeof($line);$z++){
				$cLine[]=iconv("utf-8","cp949",$line[$z]);
			}

			fputcsv($f, $cLine, $delimiter); 
		}
		fseek($f, 0);

		header('Content-Type: application/csv; charset=UTF-8\r\n');
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		fpassthru($f);
	}
}

// converting goods images
function bbse_commerce_external_image_sideload($file) {
	if ( !function_exists('media_handle_upload') ) {
	  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	  require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	}

	if(!empty($file)){
		$tmpFile=download_url( $file );

		preg_match('/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $file, $matches);
		$file_array['name'] = basename($matches[0]);
		$file_array['tmp_name'] = $tmpFile;

		if(is_wp_error($tmpFile)){
			@unlink($file_array['tmp_name']);
			$file_array['tmp_name'] = '';
			return false;
		}

		$desc = $file_array['name'];
		$id = media_handle_sideload($file_array, $desc);

		if(is_wp_error($id)){
			@unlink($file_array['tmp_name']);
			return false;
		}
		else{
			return $id;
		}
	}
}

if(!function_exists("bbse_commerce_goodsSoldoutCheck")) {
	function bbse_commerce_goodsSoldoutCheck($goods) {
		global $wpdb;

		if($goods->goods_display=='soldout' || ($goods->goods_count_flag=='goods_count' && $goods->goods_count <= 0)) {
			return true;
		}else if($goods->goods_count_flag=='option_count') {
			$optTotal_count = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_item_count>'0' AND goods_option_item_soldout<>'soldout' AND goods_option_item_display='view'");
			if($optTotal_count<='0') return true;
			else return false;
		}else{
			return false;
		}

	}
}

// 품절상품 입고알림 sms, email 발송
if(!function_exists("bbse_commerce_soldout_noticeSend")) {
	function bbse_commerce_soldout_noticeSend($gIdx){
		global $wpdb,$theme_shortname;
		$customPriceView=(get_option($theme_shortname."_config_goods_consumer_price_view"))?get_option($theme_shortname."_config_goods_consumer_price_view"):"U"; // 소비자가 노출여부

		$oCnfCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='order'");
		if($oCnfCnt>'0'){
			$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='order'");
			$orderInfo=unserialize($confData->config_data);
		}

		$gData = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$gIdx."'");    // 총 상품수

		if($gData->goods_name && $orderInfo['soldout_notice_use']=='on' && ($orderInfo['soldout_notice_sms']=='sms' || $orderInfo['soldout_notice_email']=='email')){
			$config = $wpdb->get_row("SELECT * FROM bbse_commerce_membership_config LIMIT 1");
			$send_date=current_time('timestamp');
			if($gData->goods_basic_img) $basicImg = wp_get_attachment_image_src($gData->goods_basic_img,"goodsimage2");
			else{
				$imageList=explode(",",$gData->goods_add_img);
				if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage2");
				else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
			}

			$goodsPrice=($gData->goods_consumer_price>$gData->goods_price && $customPriceView=='U')?"<span style='text-decoration:line-through;'>￦ ".number_format($gData->goods_consumer_price)."원</span><br /><span style='font-size:13px;font-weight:700;'>￦ ".number_format($gData->goods_price)."원</span>":"<span style='font-size:14px;font-weight:700;'>￦ ".number_format($gData->goods_price)."원</span>";
			$goodsTable="<table summary='결제정보 테이블입니다.' cellpadding='0' cellspacing='0' border='0' style='100%;border-top:1px solid #2c2c2c;border-bottom:1px solid #d9d9de;font-size:12px;text-align:left;' width='100%'><tbody>
									<tbody>
									<colgroup><col width='10%;'><col width='10%;'><col width=''><col width='25%'></colgroup>
									<tbody>
										<tr><th style='height:35px;background-color:#f6f6f7;font-weight:700;text-align:center;'>번호</th><th style='background-color:#f6f6f7;font-weight:700;text-align:center;'>이미지</th><th style='background-color:#f6f6f7;font-weight:700;text-align:center;'>상품명</th><th style='background-color:#f6f6f7;font-weight:700;text-align:center;'>가격</th></tr>
										<tr style='height:70px;'>
											<td style='text-align:center;'>1</td>
											<td style='text-align:center;'>
												<img src='".$basicImg['0']."' title='".$gData->goods_name."' style='margin-top: 10px;width: 100px;height: 100px;border: 1px solid #efefef;' />
											</td>
											<td><div style='margin:0 15px;'>[상품코드:".$gData->goods_code."]<br /><a href='".esc_url( home_url( '/' ) )."?bbseGoods=".$data->goods_idx."' target='_blank' style='text-decoration:none;color:#00A2E8;'>".$gData->goods_name."</a></div></td>
											<td style='text-align:center;'>".$goodsPrice."</td>
										</tr>
									</tbody>
								</table>";


			$result = $wpdb->get_results("SELECT * FROM bbse_commerce_soldout_notice WHERE goods_idx='".$gIdx."' AND ((hp<>'' AND sms_yn='N') OR (email<>'' AND email_yn='N'))");
			foreach($result as $nData) {
				$upSql="";
				$ntHp=trim(str_replace("-","",$nData->hp));
				$ntEmail=trim($nData->email);

				$mData = $wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$nData->user_id."'");
				
				$resName=($mData->name)?$mData->name:$nData->user_id;

				if($config->sms_use_yn=="Y" && $orderInfo['soldout_notice_sms']=='sms' && $ntHp && $nData->sms_yn=='N'){
					$userMsg=$resName."님 품절상품 입고알림을 신청하신 ".$gData->goods_name." 상품이 입고되었습니다. 감사합니다.\n".get_bloginfo('name');
					$smsRst=bbse_commerce_ezsms_send($ntHp, $config->sms_callback_tel, $userMsg, "SMS", "D", "");//회원

					if($smsRst=='success'){
						$upResult=$wpdb->query("UPDATE bbse_commerce_soldout_notice SET sms_yn='Y',sms_send_date='".$send_date."' WHERE idx='".$nData->idx."'");
					}
				}

				if($orderInfo['soldout_notice_email']=='email' && $ntEmail && $nData->email_yn=='N'){
					$reqArray = wp_remote_get(remove_ssl_url(BBSE_COMMERCE_PLUGIN_WEB_URL)."mail/soldout_notice.php");

					$headers = "From: ".get_bloginfo('name')." <".get_bloginfo('admin_email').">\r\n";   // 보내는 사람
					$attachments="";                                                                                                  // 첨부파일

					$mailData['to']=$ntEmail;
					$mailData['subject']=$resName."님 품절상품 입고알림을 신청하신 상품이 입고되었습니다.";
					$mailData['message']=wp_remote_retrieve_body($reqArray);
					$mailData['message']=str_replace("{성명}",$resName,$mailData['message']);
					$mailData['message']=str_replace("{사이트이름}",get_bloginfo('name'),$mailData['message']);
					$mailData['message']=str_replace("{마이페이지링크}",remove_ssl_url(get_bloginfo('url'))."?bbseMy=mypage",$mailData['message']);
					$mailData['message']=str_replace("{상품정보내용}",$goodsTable,$mailData['message']);

					if($mailData['to'] && $mailData['subject'] && $mailData['message']){
						add_filter( 'wp_mail_content_type', 'bbse_commerce_set_html_content_type' );
						$status = wp_mail( $mailData['to'], $mailData['subject'], $mailData['message'], $headers, $attachments );
						remove_filter( 'wp_mail_content_type', 'bbse_commerce_set_html_content_type' ); // 충돌을 방지하기 위해 콘텐츠 유형을 재설정 -- http://core.trac.wordpress.org/ticket/23578

						if($status){
							$upResult=$wpdb->query("UPDATE bbse_commerce_soldout_notice SET email_yn='Y',email_send_date='".$send_date."' WHERE idx='".$nData->idx."'");
						}
					}
				}
			}
		}
	}
}
if (!function_exists('getCategoryQuery')) {
	function getCategoryQuery($cate) {
		if(!$cate) return;
		global $wpdb;
		$linkUrl = home_url();
		if(!table_exists('bbse_commerce_category')) {echo "BBS e-Commerce 플러그인을 먼저 설치해주세요.";return;}
		
		$cateChk = $wpdb->get_row("select idx,depth_1,depth_2,depth_3,c_use from bbse_commerce_category where idx='".$cate."'");
		$depList = array();
		if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 > 0) {//소분류
			//$depList[] = "(find_in_set( '".$cateChk->idx."' , replace(goods_cat_list,'|',',')) > 0)";
		    $depList[] = "(find_in_set( '".$cateChk->idx."' , replace(replace(goods_cat_list, '|', ','),'$',',')) > 0)";
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 > 0 && $cateChk->depth_3 == 0) {//중분류
			//$depList[] = "(find_in_set( '".$cate."' , replace(goods_cat_list,'|',',')) > 0)";
		    $depList[] = "(find_in_set( '".$cate."' , replace(replace(goods_cat_list, '|', ','),'$',',')) > 0)";
			$depRes2 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 = '".$cateChk->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
			foreach($depRes2 as $i=>$dep2) {
				//$depList[] = "(find_in_set( '".$dep2->idx."' , replace(goods_cat_list,'|',',')) > 0)";
			    $depList[] = "(find_in_set( '".$dep2->idx."' , replace(replace(goods_cat_list, '|', ','),'$',',')) > 0)";
				$depRes3 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2='".$dep2->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
				foreach($depRes3 as $j=>$dep3) {
					//$depList[] = "(find_in_set( '".$dep3->idx."' , replace(goods_cat_list,'|',',')) > 0)";
				    $depList[] = "(find_in_set( '".$dep3->idx."' , replace(replace(goods_cat_list, '|', ','),'$',',')) > 0)";
				}
			}
		}else if($cateChk->depth_1 > 0 && $cateChk->depth_2 == 0 && $cateChk->depth_3 == 0) {//대분류
			//$depList[] = "(find_in_set( '".$cate."' , replace(goods_cat_list,'|',',')) > 0)";
		    $depList[] = "(find_in_set( '".$cate."' , replace(replace(goods_cat_list, '|', ','),'$',',')) > 0)";
			$depRes2 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2 > 0 and depth_3 = 0 and c_use='Y' order by c_rank asc");
			foreach($depRes2 as $i=>$dep2) {
				//$depList[] = "(find_in_set( '".$dep2->idx."' , replace(goods_cat_list,'|',',')) > 0)";
			    $depList[] = "(find_in_set( '".$dep2->idx."' , replace(replace(goods_cat_list, '|', ','),'$',',')) > 0)";
				$depRes3 = $wpdb->get_results("select idx,depth_1,depth_2,depth_3 from bbse_commerce_category where depth_1='".$cateChk->depth_1."' and depth_2='".$dep2->depth_2."' and depth_3 > 0 and c_use='Y' order by c_rank asc");
				foreach($depRes3 as $j=>$dep3) {
					//$depList[] = "(find_in_set( '".$dep3->idx."' , replace(goods_cat_list,'|',',')) > 0)";
				    $depList[] = "(find_in_set( '".$dep3->idx."' , replace(replace(goods_cat_list, '|', ','),'$',',')) > 0)";
				}
			}
		}
		return " ( ".implode(" or ", $depList)." ) ";
	}
}
if(!function_exists('table_exists')) {
	function table_exists($table) {
		global $wpdb;
		if(!$table) return false;

		$total = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.TABLES WHERE table_name='".$wpdb->prefix.$table."'");
		if($total>0) $tbName=$wpdb->prefix.$table;
		else $tbName=$table;

		$row = $wpdb->get_row("show tables like '".$tbName."'",ARRAY_N);
		if($row[0]) return 1;
		else return 0;
	}
}
?>
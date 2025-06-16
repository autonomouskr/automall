<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname,$current_user, $orderStatus;
wp_get_current_user();

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

$orderConfigData = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='order'");
$orderCFG = unserialize($orderConfigData);
$V = $_GET;

if(plugin_active_check('BBSe_Commerce')) bbse_commerce_chk_order_status(); // 취소완료, 배송완료, 구매확정 처리

/* Search Vars */
$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];//한 페이지에 표시될 목록수
$page = (count($_POST)>0 || !$_REQUEST['page'])?1:intval($_REQUEST['page']);//현재 페이지
$start_pos = ($page-1) * $per_page; //목록 시작 위치
$orderby = " ORDER BY order_date ";
$sort = "DESC";

/* Add Query */
if($_REQUEST['search_date1']) $search_date1 = strtotime($_REQUEST['search_date1']." 00:00:00");
if($_REQUEST['search_date2']) $search_date2 = strtotime($_REQUEST['search_date2']." 23:59:59");
if($_REQUEST['search_date1'] != "" && $_REQUEST['search_date2'] != "") {
	$addQuery = " AND order_date >= ".$search_date1." AND order_date <= ".$search_date2;
}else if($_REQUEST['search_date1'] != "" && $_REQUEST['search_date2'] == "") {
	$addQuery = " AND order_date >= ".$search_date1;
}else if($_REQUEST['search_date1'] == "" && $_REQUEST['search_date2'] != "") {
	$addQuery = " AND order_date <= ".$search_date2;
}
//$orderStatus=Array("PR"=>"입금대기","PE"=>"결제완료","DR"=>"배송준비","DI"=>"배송중","DE"=>"배송완료","OE"=>"구매확정","CA"=>"취소신청","CE"=>"취소완료","RA"=>"반품신청","RE"=>"반품완료","TR"=>"휴지통");

/* List Query  */
if($Loginflag=='member'){
	$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_order WHERE order_status IN ('CA', 'CE', 'RA', 'RE') AND user_id='".$currUserID."'".$addQuery); //총 목록수
	$total_pages = ceil($total / $per_page); //총 페이지수
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE order_status IN ('CA', 'CE', 'RA', 'RE') AND user_id='".$currUserID."'".$addQuery.$orderby.$sort." LIMIT ".$start_pos.", ".$per_page);
}
else if($Loginflag=='social' && $currSnsIdx>'0'){
	$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_order WHERE order_status IN ('CA', 'CE', 'RA', 'RE') AND user_id='' AND sns_id='".$currUserID."' AND sns_idx='".$currSnsIdx."'".$addQuery); //총 목록수
	$total_pages = ceil($total / $per_page); //총 페이지수
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_order WHERE order_status IN ('CA', 'CE', 'RA', 'RE') AND user_id='' AND sns_id='".$currUserID."' AND sns_idx='".$currSnsIdx."'".$addQuery.$orderby.$sort." LIMIT ".$start_pos.", ".$per_page);
}

/* Query String */
$add_args = array("per_page"=>$per_page,"search_date1"=>$search_date1, "search_date2"=>$search_date2);
$addQueryString =  "&page=".$page."&per_page=".$per_page."&search_date1=".$search_date1."&search_date2=".$search_date2;

/* 페이징 처리 정의 */	
$page_param = array();           
$page_param['page_row'] = $per_page;
$page_param['page_block'] = 10;      
$page_param['total_count'] = $total; 
$page_param['current_page'] = $page; 
$page_param['link_url'] = home_url()."/?bbseMy=".$V['bbseMy']."&".http_build_query($add_args);  
$page_class = new themePaging(); 
$page_class->initPaging($page_param); 

$search_today = date("Y-m-d");
?>
<script language="javascript">
	jQuery(document).ready(function() {
		jQuery('#s_keyword').keyup(function(e) {
			if (e.keyCode == 13) search_submit('');       
		});

		// 날짜(datepicker) initialize (1)
		jQuery.datepicker.regional['ko']= {
			closeText:'닫기',
			prevText:'이전달',
			nextText:'다음달',
			currentText:'오늘',
			monthNames:['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUM)','7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
			monthNamesShort:['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames:['일','월','화','수','목','금','토'],
			dayNamesShort:['일','월','화','수','목','금','토'],
			dayNamesMin:['일','월','화','수','목','금','토'],
			weekHeader:'Wk',
			dateFormat:'yy-mm-dd',
			firstDay:0,
			isRTL:false,
			showMonthAfterYear:true,
			yearSuffix:''
		};
		jQuery.datepicker.setDefaults(jQuery.datepicker.regional['ko']);
		jQuery(".datepicker").datepicker(jQuery.datepicker.regional["ko"]);
		jQuery('.datepicker').datepicker('option', {dateFormat:'yy-mm-dd'});
	});

	function getDateAdd(f,v,t){ //오늘날짜, 일차(과거:-)
		jQuery(".date-item").removeClass("cus_fill");
		jQuery(f).addClass("cus_fill");
		if(v){
			var str=new Array(); //배열
			var b=v.split("-"); //날짜를 - 구분자로 나누어 배열로 변환
			var c=new Date(b[0],b[1]-1,b[2]); //데이트객체 생성
			var d=c.valueOf()+1000*60*60*24*t; //t일후, (음수면 전일)의 타임스탬프를 얻는다
			var e=new Date(d); //의뢰한날의 데이트객체 생성
			var result="";
			str[str.length]=e.getFullYear(); //년

			if(e.getMonth()+1<10) str[str.length]="0"+(e.getMonth()+1); //월
			else str[str.length]=e.getMonth()+1; //월

			if(e.getDate()<10) str[str.length]="0"+e.getDate(); //일
			else str[str.length]=e.getDate(); //일
			
			result=str.join("-"); //배열을 - 구분자로 합쳐 스트링으로 변환

			jQuery("#search_date1").val(result);
			jQuery("#search_date2").val(v);
		}
		else{
			jQuery("#search_date1").val(result);
			jQuery("#search_date2").val(v);
		}
	}
</script>

		<h2 class="page_title">취소/반품신청조회</h2>
		<div class="article">
			<ul class="bb_dot_list">
				<li>취소신청 : 입금대기/결제완료/배송준비 주문은 취소신청이 가능합니다.</li>
				<!-- <li>반품신청 : 배송중/배송완료(배송완료 <?php echo $orderCFG['order_end_day']?>일 이내) 주문은 반품신청이 가능합니다.</li> -->
				<li>반품신청 : 관리담당자가 반품신청을 접수받고 있습니다. 관리자에게 문의 바랍니다.</li> 
				<li>구매확정 : 배송완료 후 <?php echo $orderCFG['order_end_day']?>일 이후까지 구매확정을  안 한 경우 구매확정 처리되며 구매확정 주문은 취소 및 반품이 불가합니다.</li>
			</ul>
		</div>

		<form name="orderListFrm" id="orderListFrm" method="get">
		<input type="hidden" name="bbseMy" id="bbseMy" value="refund">
		<div class="article bb_sch_cal">
			<dl>
				<dt>조회기간</dt>
				<dd>
					<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-7);"><span class="mid">1주일</span></button>
					<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-30);"><span class="mid">1개월</span></button>
					<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-90);"><span class="mid">3개월</span></button>
					<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-180);"><span class="mid">6개월</span></button>
					<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-365);"><span class="mid">1년</span></button><!--N: 선택은 .cus_fill 추가-->

					<div class="bbse_commerce_ui bb_cal_select">
						<label class="blind" for="searchFirstDate">시작 날짜</label><input type="text" class="datepicker" value="<?php echo $V['search_date1'];?>" name="search_date1" id="search_date1" style="width:80px;text-align:center;" readonly/>
						<div class="cal_wrap">
							<button type="button" class="icon_cal" onClick="jQuery('#search_date1').focus();"><span>시작 날짜 선택</span></button>
							<div class="cal_ly"><!--N: 달력 div 여기 안에 넣어주세요. --></div>
						</div>
						~
						<label class="blind" for="searchLastDate">마지막 날짜</label><input type="text" class="datepicker" value="<?php echo $V['search_date2'];?>" name="search_date2" id="search_date2" style="width:80px;text-align:center;" readonly/>
						<div class="cal_wrap">
							<button type="button" class="icon_cal" onClick="jQuery('#search_date2').focus();"><span>마지막 날짜 선택</span></button>
							<div class="cal_ly"><!--N: 달력 div 여기 안에 넣어주세요. --></div>
						</div>
						<button type="button" class="bb_btn shadow" id="mypage_order_search"><span class="mid">조회</span></button>
					</div>
				</dd>
			</dl>
		</div>

		<div class="fakeTable marginTop orderListTbl ">
			<ul class="header">
				<li>주문일/주문번호</li>
				<li>상품명</li>
				<li>결제금액</li>
				<li>진행상태</li>
			</ul>
			<?php
			if($total > 0) {
				foreach($result as $order){
			?>
			<ul>
				<li class="orderdInfoCell">
					<span class="orderedDate"><?php echo date("Y-m-d", $order->order_date); ?></span>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=refund-detail&ordno=<?php echo $order->order_no.$addQueryString;?>" class="order_num"><?php echo $order->order_no; ?></a>
					<?php if($order->order_status=="PR" || $order->order_status=="PE" || $order->order_status=="DR") {?>
					<button type="button" class="bb_btn shadow orderCancel_open"><span class="sml">주문취소</span></button>
					<?php }?>
				</li>
				<li class="goodsInfoCell">
				<?php
				$orderCnt = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_order_detail WHERE order_no='".$order->order_no."'");
				$detailResult = $wpdb->get_results("SELECT * FROM bbse_commerce_order_detail WHERE order_no='".$order->order_no."' ORDER BY idx ASC LIMIT 1");
				foreach($detailResult as $detail) {
					unset($basicOpt);
					unset($addOpt);
					unset($basicImg);

					if($detail->goods_basic_img) 	$basicImg = wp_get_attachment_image_src($detail->goods_basic_img);
					if(!$basicImg['0']){
						$goodsAddImg=$wpdb->get_var("SELECT goods_add_img FROM bbse_commerce_goods WHERE idx='".$detail->goods_idx."'"); 

						$imageList=explode(",",$goodsAddImg);
						if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
						else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
					}
				?>
					<div class="goodsBaseInfo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=refund-detail&ordno=<?php echo $order->order_no.$addQueryString;?>"><img src="<?php echo $basicImg['0'];?>" alt="<?php echo $detail->goods_name; ?>"></a>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=refund-detail&ordno=<?php echo $order->order_no.$addQueryString;?>" class="subj">
							<?php echo $detail->goods_name; ?><?php if($orderCnt>1){echo " 외 ".number_format($orderCnt-1)."건";}?>
						</a>
					</div>
				<?php 
				}
				?>
				</li>
				<li>
					<div class="mobile-cell-title">결제금액</div>
					<div class="cell-data paidAmount"><?php echo number_format($order->cost_total); ?>원</div>
				</li>
				<li class="orderStatusInfoCell">
				<?php
					//취소신청/취소완료/반품신청/반품완료
					$modStatusArr = array("CA", "CE", "RA", "RE");
					if( in_array($order->order_status, $modStatusArr) == true) {
						$statusClass = "class=\"status_cancel\"";
					}else{
						$statusClass = "";
					}
				?>
					<span class="orderStatus <?php echo $statusClass?>"><?php echo $orderStatus[$order->order_status];?></span>
					<?php if($order->order_status=="DI" || $order->order_status=="DE") {?>
					<button type="button" class="bb_btn cus_fill"><span class="sml">구매확정</span></button>
					<button type="button" class="bb_btn shadow openLayer" data-name="tracking" data-tracking="no^상품며엉 상품며엉 상품며엉 상품며엉 상품며엉 상품며엉 상품며엉 상품며엉 상품며엉^01234567"><span class="sml">배송조회</span></button>
<!-- 					<button type="button" class="bb_btn shadow recall_open"><span class="sml">반품신청</span></button> -->
					<?php }?>
				</li>
			</ul>
			<?php
				}
			}
			?>
		</div><!-- fakeTable -->
		</form>
		<?php if($total == 0) { ?><div class="nodata">주문정보가 존재하지 않습니다.</div><?php } ?>

		<div class="clearFloat"></div>

		<?php echo $page_class->getPaging();?>
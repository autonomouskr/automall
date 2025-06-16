<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

if(plugin_active_check('BBSe_Commerce')) {

	global $current_user, $theme_shortname, $earnType;
	wp_get_current_user();

	$search_today = date("Y-m-d");

	$V = $_GET;

	/* Search Vars */
	$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];//한 페이지에 표시될 목록수
	$page = (count($_POST)>0 || !$_REQUEST['page'])?1:intval($_REQUEST['page']);//현재 페이지
	$start_pos = ($page-1) * $per_page; //목록 시작 위치
	$orderby = " ORDER BY idx ";
	$sort = "DESC";

	/* Add Query */
	if($V['search_date1']) $search_date1 = strtotime($V['search_date1']." 00:00:00");
	if($V['search_date2']) $search_date2 = strtotime($V['search_date2']." 23:59:59");
	if($V['search_date1']!="" && $V['search_date2']!="") {
		$addQuery = " AND reg_date >= ".$search_date1." AND reg_date <= ".$search_date2;
	}else if($V['search_date1']!="" && $V['search_date2']=="") {
		$addQuery = " AND reg_date >= ".$search_date1;
	}else if($V['search_date1']=="" && $V['search_date2']!="") {
		$addQuery = " AND reg_date <= ".$search_date2;
	}

	/* List Query  */
	$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_earn_log WHERE user_id='".$current_user->user_login."'".$addQuery); //총 목록수
	$earnRow = $wpdb->get_results("SELECT * FROM bbse_commerce_earn_log WHERE user_id='".$current_user->user_login."'".$addQuery.$orderby.$sort." LIMIT ".$start_pos.", ".$per_page);
	$mileage = $wpdb->get_var("SELECT mileage FROM bbse_commerce_membership WHERE user_id='".$current_user->user_login."'");

	/* Query String */
	$add_args = array("per_page"=>$per_page,"search_date1"=>$V['search_date1'], "search_date2"=>$V['search_date2'], "sort_page"=>$sort_page);
	$curURL =  home_url()."/?bbseMy=".$V['bbseMy']."&per_page=".$per_page."&search_date1=".$V['search_date1']."&search_date2=".$V['search_date2'];

	/* 페이징 처리 정의 */	
	$page_param = array();           
	$page_param['page_row'] = $per_page;
	$page_param['page_block'] = 10;      
	$page_param['total_count'] = $total; 
	$page_param['current_page'] = $page; 
	$page_param['link_url'] = home_url()."/?bbseMy=".$V['bbseMy']."&".http_build_query($add_args);  
	$page_class = new themePaging(); 
	$page_class->initPaging($page_param); 

	$earnConfigData = $wpdb->get_var("select config_data from bbse_commerce_config where config_type='earn'");
	$earnCFG = unserialize($earnConfigData);
	

?>
<script language="javascript">
	jQuery(document).ready(function() {
		jQuery('#dateSearchBtn').click(function(e) {
			jQuery("#pointFrm").submit();
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
					<h2 class="page_title">적립금내역</h2>
					<div class="article">
						<ul class="bb_dot_list">
							<li>신규적립금: 회원가입 시 감사적립금 <?php echo number_format(intval($earnCFG['earn_member_point']));?>원을 지급해 드립니다.</li>
							<li>적립금이용: 적립금 <?php echo number_format($earnCFG['earn_hold_point']);?>원 이상 적립 후 상품구매 시 현금과 같이 사용하실 수 있습니다.</li>
							<li>적립금취소: 환불 시에는 적립된 금액이 취소됩니다.</li>
							<li>적립금소멸기간: 마지막 적립금 지급 이후 1년동안 사용하지 않은 적립금은 소멸됩니다.</li>
						</ul>
					</div>
					<form name="pointFrm" id="pointFrm" method="get">
					<input type="hidden" name="bbseMy" id="bbseMy" value="<?php echo $V['bbseMy']?>">
					<div class="article bb_sch_cal">
						<dl>
							<dt>조회기간</dt>
							<dd>
								<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-7);"><span class="mid">1주일</span></button>
								<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-30);"><span class="mid">1개월</span></button>
								<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-90);"><span class="mid">3개월</span></button>
								<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-180);"><span class="mid">6개월</span></button>
								<button type="button" class="bb_btn shadow date-item" onclick="getDateAdd(this,'<?php echo $search_today?>',-365);"><span class="mid">1년</span></button><!--N: 선택은 .cus_fill 추가-->

								<div class="bb_cal_select">
									<label class="blind" for="searchFirstDate">시작 날짜</label><input type="text" class="datepicker" name="search_date1" id="search_date1" style="width:80px;text-align:center;" value="<?php echo $V['search_date1']; ?>" readonly/>
									<div class="cal_wrap">
										<button type="button" class="icon_cal" onClick="jQuery('#search_date1').focus();"><span>시작 날짜 선택</span></button>
										<div class="cal_ly"><!--N: 달력 div 여기 안에 넣어주세요. --></div>
									</div>
									~
									<label class="blind" for="searchLastDate">마지막 날짜</label><input type="text" class="datepicker" name="search_date2" id="search_date2" style="width:80px;text-align:center;" value="<?php echo $V['search_date2']; ?>" readonly/>
									<div class="cal_wrap">
										<button type="button" class="icon_cal" onClick="jQuery('#search_date2').focus();"><span>마지막 날짜 선택</span></button>
										<div class="cal_ly"><!--N: 달력 div 여기 안에 넣어주세요. --></div>
									</div>
									<button type="button" id="dateSearchBtn" class="bb_btn shadow"><span class="mid">조회</span></button>
								</div>
							</dd>
						</dl>
					</div>

					<div class="article">

						<div class="fakeTable mileage">
							<ul class="header">
								<li>일자</li>
								<li>적립/차감 내용</li>
								<li>금액</li>
								<li>잔액</li>
							</ul>
							<ul class="totalBalance">
								<li>
									<div class="mobile-cell-title">현재잔액</div>
									<div class="cell-data"><?php echo number_format($mileage); ?>원</div>
								</li>
							</ul>
							<?php
							$earnMode = array("IN"=>"적립", "OUT"=>"차감");
							$earnSign = array("IN"=>"+", "OUT"=>"-");
							
							foreach($earnRow as $earn) {

								if($earn->earn_mode == "IN") {
									$current_point = $earn->old_point + $earn->earn_point;
								}else{
									$current_point = $earn->old_point - $earn->earn_point;
								}

							?>
							<ul>
								<li class="issueDate"><?php echo date("Y-m-d H:i:s",$earn->reg_date); ?></li>
								<li>
									<?php echo $earnType[$earn->earn_type]." ".$earnMode[$earn->earn_mode]; ?>
									<?php if($earn->earn_type=="order" || $earn->earn_type=="cancel"){?>
										(<?php 
											$orderCnt = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_order WHERE order_no='".$earn->etc_idx."'");
											if($orderCnt > 0) {
												echo "<a href='".home_url()."/?bbseMy=order-detail&ordno=".$earn->etc_idx."'>".$earn->etc_idx."</a>";
											}else{
												echo $earn->etc_idx;
											}
										?>)
									<?php }?>
								</li>
								<li class="issue">
									<div class="mobile-cell-title">금액</div>
									<div class="cell-data"><?php echo $earnSign[$earn->earn_mode]; ?> <?php echo number_format($earn->earn_point); ?>원</div>
								</li>
								<li class="balance">
									<div class="mobile-cell-title">잔액</div>
									<div class="cell-data"><?php echo number_format($current_point); ?>원</div>
								</li>
							</ul>
							<?php
							}
							?>
						</div>

						<?php if($total == 0) {?><div class="nodata">적립금 내역이 존재하지 않습니다.</div><?php }?>

					</div><!--//최근주문내역 -->

					<?php echo $page_class->getPaging();?>
<?php
}else{
	echo "BBS e-Commerce 플러그인 설치되지 않았거나 비활성화 상태입니다.";
}
?>
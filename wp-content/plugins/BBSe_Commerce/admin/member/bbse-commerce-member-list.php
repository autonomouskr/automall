<?php
/* Search Vars */
$page=$_REQUEST['page'];
$search1 = $_REQUEST['search1'];
$keyword = $_REQUEST['keyword'];
$search2 = $_REQUEST['search2'];
$user_class =  $_REQUEST['user_class'];
$search_date1 = $_REQUEST['search_date1'];
$search_date2 = $_REQUEST['search_date2'];
$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치
$orderby = $_REQUEST['orderby'];
$order = $_REQUEST['order'];
$cType = ($_REQUEST['cType'])?$_REQUEST['cType']:"list";

/* Add Query */
if($search1 && $keyword) $addQuery = " AND ".$search1." LIKE '%".$keyword."%' ";
else if(!$search1 && $keyword) $addQuery = " AND (name LIKE '%".$keyword."%' OR user_id LIKE '%".$keyword."%' OR email LIKE '%".$keyword."%' OR hp LIKE '%".$keyword."%') ";

if($search_date1 && $search_date2) {
	$start_date = strtotime($search_date1." 00:00:00");
	$end_date = strtotime($search_date2." 23:59:59");
	$addQuery .= " AND ".$search2.">='".$start_date."' AND ".$search2."<='".$end_date."' ";
}
if($user_class) $addQuery .= " AND user_class='".$user_class."' ";
$addQuery .= " AND leave_yn='0' ";
$total = $BBSeCommerceMember->getMemberCount($addQuery);
$total_pages = ceil($total / $per_page);
$result = $BBSeCommerceMember->getMemberList($orderby, $order, $addQuery, $start_pos, $per_page);

/* Query String */
$add_args = array("page"=>$page, "per_page"=>$per_page, "search1"=>$search1, "search2"=>$search2, "orderby"=>$orderby, "order"=>$order, "keyword"=>$keyword, "search_date1"=>$search_date1, "search_date2"=>$search_date2, "cType"=>$cType, "user_class"=>$user_class);
$queryString = http_build_query($add_args);

$search_today = date("Y-m-d");
?>
<script language="javascript">
jQuery(document).ready(function() {
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

	// 날짜(datepicker) initialize (2)
	jQuery(".datepicker").datepicker(jQuery.datepicker.regional["ko"]);
	jQuery('.datepicker').datepicker('option', {dateFormat:'yy-mm-dd'});
});
	
function checkExtend(frm, objName){
	var frm = eval('document.' + frm);
	var checkOk = 0;

	for(var i = 0; i < frm.elements.length; i++){
		if(frm.elements[i].name == objName && frm.elements[i].checked == true){
			checkOk += 1;
		}
	}	
	return checkOk;
}

function delete_list(tNo){
	var frm = document.join_list_frm;

	if(tNo > 0){
		if(confirm('해당 회원을 삭제하시겠습니까?   ')){
			frm.delNo.value = tNo;
			frm.mvrun.value = "del_proc";
			frm.action = 'admin.php?page=bbse_commerce_member';
			frm.submit();
		}
	}
}

function delete_check_list(sNo){
	var chkNum = 0;
	var frm = document.join_list_frm;
	var msg = "";var proc = "";

	if(eval("frm.tBatch" + sNo).value == -1){
		alert('일괄 작업을 선택해 주세요.   ');
		eval("frm.tBatch" + sNo).focus();
		return false;
	}

	chkNum = checkExtend('join_list_frm', 'check[]');

	if(eval("frm.tBatch" + sNo).value=="remove") {
		msg = "삭제";
		proc = "del_proc";
	}else if(eval("frm.tBatch" + sNo).value=="email") {
		msg = "메일발송";
		proc = "email_proc";
	}else if(eval("frm.tBatch" + sNo).value=="sms") {
		msg = "SMS발송";
		proc = "sms_proc";
	}

	if(chkNum <= 0){
		alert(msg + '하실 회원을 선택해 주세요.   ');
		return false;
	}

	if(confirm('선택하신 회원을 '+msg+'하시겠습니까?   ')){
		frm.mvrun.value = proc;
		frm.action = 'admin.php?page=bbse_commerce_member';
		frm.submit();
	}
}

function update_check_paymode(){
	var chkNum = 0;
	var frm = document.join_list_frm;
	var msg = "";
	var proc = "";
	
	var count = jQuery("input[name=check\\[\\]]:checked").size();
	var checkbox = jQuery("input[name=check\\[\\]]:checked");
	
	let userArr = [];
	
	
	if(eval("frm.tPayMode").value == -1){
		alert('변경하실 결제방식을 선택해주세요.   ');
		eval("frm.tPayMode" + sNo).focus();
		return false;
	}

	chkNum = checkExtend('join_list_frm', 'check[]');

	if(eval("frm.tPayMode").value=="monPay") {
		msg = "월말결제";
		proc = "monPay";
	}else if(eval("frm.tPayMode").value=="caseByPay") {
		msg = "건별결제";
		proc = "caseByPay";
	}

	if(chkNum <= 0){
		alert(msg + '하실 셀을 선택해 주세요.   ');
		return false;
	}

	if(confirm('선택하신 회원을 '+msg+'하시겠습니까?   ')){
		frm.mvrun.value = proc;
		frm.action = 'admin.php?page=bbse_commerce_member';
		frm.submit();
	}
	
}

function getDateAdd(v,t){ //오늘날짜, 일차(과거:-)
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
function searchSubmit() {
	jQuery("#join_list_frm").submit();
}


function userinfo_upload_excel(){
	var popupTitle="회원정보 엑셀업로드";

	var tbHeight = window.innerHeight * .65;
	var tbWidth = window.innerWidth * .30;
	tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/admin/bbse-commerce-popup-member-list-dbinsert.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-popup-member-list-dbinsert.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
	//thickbox_resize();
	return false;
}
	
//상품 등록 팝업 닫기
function remove_popup(){
	tb_remove();
}
</script>


				<form id="join_list_frm" name="join_list_frm" method="post">
				<input type="hidden" name="mvrun" id="mvrun" value="" />
				<input type="hidden" name="delNo" id="delNo" value="">
				<input type="hidden" name="add_args" id="add_args" value="<?php echo base64_encode(serialize($add_args))?>">
				
				<!-- Search content -->
				<table cellspacing="0" cellpadding="5" border="0" style="border:2px solid #4C99BA;width:100%;background-color:#f9f9f9;">
				<thead>
				<tr>
					<th scope="col" width="100" class="manage-column" style="border-bottom:1px dotted #cccccc;">회원검색</th>
					<td scope="col" class="manage-column" colspan="5" style="border-bottom:1px dotted #cccccc;">
						<select id="user_class" name="user_class" style="float:left">
							<option value="">등급검색</option>
							<?php
								$mclass_rlt = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class ORDER BY no ASC");
								$class_arr = array();
								foreach($mclass_rlt as $i => $mclass){
									$class_arr[$mclass->no] = $mclass->class_name;
									echo '<option value="'.$mclass->no.'"'.($user_class == $mclass->no? ' selected' : '').'>'.$mclass->class_name.'</option>';
								}
							?>
						</select>
						<select id='search1' name='search1' style="float:left">
							<option value="">통합검색</option>
							<option value="name" <?=($search1=="name")?"selected":""?>>이름</option>
							<option value="user_id" <?=($search1=="user_id")?"selected":""?>>아이디</option>
							<option value="email" <?=($search1=="email")?"selected":""?>>이메일</option>
							<option value="hp" <?=($search1=="hp")?"selected":""?>>핸드폰</option>
						</select>
						<p class="">
							<input type="text" id="keyword" name="keyword" value="<?=$keyword?>"/>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="col" width="100" class="manage-column">조회기간</th>
					<td scope="col" class="manage-column">
						<select id='search2' name='search2' style="width:100%;">
							<option value="reg_date" <?=($search2=="reg_date")?"selected":""?>>가입일</option>
							<option value="last_login" <?=($search2=="last_login")?"selected":""?>>최종 로그인일</option>
						</select>
					</td>
					<td scope="col" class="manage-column"  colspan="4">
						<input type="text" id="search_date1" name="search_date1" class="datepicker" value="<?php echo $search_date1?>" style="text-align:center;" size="9" readonly/>
						~
						<input type="text" id="search_date2" name="search_date2" class="datepicker" value="<?php echo $search_date2?>"  style="text-align:center;" size="9" readonly/>
						<input type="button" name="" class="button-secondary" value="오늘" onclick="getDateAdd('<?php echo $search_today?>',0);"/>
						<input type="button" name="" class="button-secondary" value="일주일" onclick="getDateAdd('<?php echo $search_today?>',-7);"/>
						<input type="button" name="" class="button-secondary" value="15일" onclick="getDateAdd('<?php echo $search_today?>',-15);"/>
						<input type="button" name="" class="button-secondary" value="한달" onclick="getDateAdd('<?php echo $search_today?>',-30);"/>
						<input type="button" name="" class="button-secondary" value="3개월" onclick="getDateAdd('<?php echo $search_today?>',-90);"/>
						<input type="button" name="" class="button-secondary" value="6개월" onclick="getDateAdd('<?php echo $search_today?>',-180);"/>
						<input type="button" name="" class="button-secondary" value="1년"   onclick="getDateAdd('<?php echo $search_today?>',-365);"/>
						&nbsp;&nbsp;
						<button type="button" class="button-bbse blue" onclick="searchSubmit();">조회하기</button>
						<button type="button" onclick="location.href='admin.php?page=bbse_commerce_member&mode=add';" class="button-bbse blue">회원추가</button>
<!-- 						<button type="button" onclick="userinfo_upload_excel();" class="button-bbse red">회원목록업로드</button> -->
						<button type="button" onclick="location.href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL.'/admin/bbse-commerce-member-download.php'; ?>';" class="button-bbse blue">회원DB다운로드</button>
					</td>
				</tr>
				</thead>
				</table>
				<!--// Search content -->

				<div class="clearfix" style="height:20px;"></div>

				<!-- 내용 start -->
				<div style="margin-bottom:30px;">
					<ul class='title-sub-desc'>
						<li class="current"><a title='전체 목록 보기' href="admin.php?page=bbse_commerce_member">전체(<?php echo number_format($total)?>)</a></li>
					</ul>
				</div>

				<div style="margin-bottom:70px;">
					<ul class='title-sub-desc none-content' style="float:left;">
						<li>
							<select name="tBatch1">
								<option value="-1">일괄 작업</option>
								<option value="remove">선택 삭제</option>
								<option value="sms">선택 SMS 발송</option>
								<option value="email">선택 이메일 발송</option>
							</select>
							<input type="button" name="btn_batch" id="doaction" class="button-secondary" value="적용" onclick="delete_check_list(1);">
						</li>
					</ul>
					<ul class='title-sub-desc none-content' style="float:left;">
						<li>
							<select name="tPayMode">
								<option value="-1">결제 방식</option>
								<option value="01">월말결제</option>
								<option value="02">건별결제</option>
							</select>
							<input type="button" name="btn_batch" id="doaction" class="button-secondary" value="적용" onclick="update_check_paymode();">
						</li>
					</ul>
					<ul class='title-sub-desc none-content' style="float:right;">
						<li style="text-align:right;">
							<select id="per_page" name="per_page" onchange="jQuery('#join_list_frm').submit();">
								<option value="10" <?=($per_page=="10")?"selected":""?>>10개</option>
								<option value="20" <?=($per_page=="20")?"selected":""?>>20개</option>
								<option value="40" <?=($per_page=="40")?"selected":""?>>40개</option>
								<option value="60" <?=($per_page=="60")?"selected":""?>>60개</option>
								<option value="80" <?=($per_page=="80")?"selected":""?>>80개</option>
							</select>
						</li>
					</ul>
				</div>


				<?php
				if(!empty($_GET['order'])){
					switch($_GET['order']){
						case "asc": $order = "desc";break;
						case "desc": $order = "asc";break;
						default: $order = "asc";break;
					}
				}else $order = "asc";
				if(!empty($_GET['orderby'])){
					switch($_GET['orderby']){
						case "user_no": 
							$sorted1 = "sorted";$sorted2 = "sortable";$sorted3 = "sortable";$sorted4 = "sortable";$sorted5 = "sortable";$sorted6 = "sortable";
							break;
						case "user_id": 
							$sorted1 = "sortable";$sorted2 = "sorted";$sorted3 = "sortable";$sorted4 = "sortable";$sorted5 = "sortable";$sorted6 = "sortable";
							break;
						case "name": 
							$sorted1 = "sortable";$sorted2 = "sortable";$sorted3 = "sorted";$sorted4 = "sortable";$sorted5 = "sortable";$sorted6 = "sortable";
							break;
						case "user_class": 
							$sorted1 = "sortable";$sorted2 = "sortable";$sorted3 = "sortable";$sorted4 = "sorted";$sorted5 = "sortable";$sorted6 = "sortable";
							break;
						case "last_login": 
							$sorted1 = "sortable";$sorted2 = "sortable";$sorted3 = "sortable";$sorted4 = "sortable";$sorted5 = "sorted";$sorted6 = "sortable";
							break;
						case "reg_date":
							$sorted1 = "sortable";$sorted2 = "sortable";$sorted3 = "sortable";$sorted4 = "sortable";$sorted5 = "sortable";$sorted6 = "sorted";
							break;
					}
				}else{ 
					$sorted1 = "sortable";$sorted2 = "sortable";$sorted3 = "sortable";$sorted4 = "sortable";$sorted5 = "sortable";$sorted6 = "sortable";
				}
				?>
				<table class="dataTbls normal-line-height collapse">
				<colgroup>
					<col style="text-align:center" width="20px"/><col style="text-align:center" width="25px" /><col style="text-align:center" width="80px" /><col style="text-align:center" width="100px" /><col style="text-align:center" width="100px" /><col width="100px" /><col style="text-align:center" width="90px" /><col style="text-align:center" width="75px" /><col width="100px" /><col width="100px" /><col width="75px" />
				</colgroup>
				<thead>
				<tr>
					<th scope="col" class="manage-column column-cb check-column" align="center">&nbsp;&nbsp;<input type="checkbox"></th>
					<th scope="col" class="manage-column <?php echo $sorted1?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=user_no&#038;order=<?php echo $order?>"><span>번호</span><span class=""></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted2?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=user_id&#038;order=<?php echo $order?>"><span>아이디</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted3?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=name&#038;order=<?php echo $order?>"><span>이름</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted4?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=user_class&#038;order=<?php echo $order?>"><span>등급</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column">이메일</th>
					<th scope="col" class="manage-column">휴대폰</th>
					<th scope="col" class="manage-column <?php echo $sorted7?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=reg_date&#038;order=<?php echo $order?>"><span>결제방식</span><span class=""></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted5?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=reg_date&#038;order=<?php echo $order?>"><span>가입일</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted6?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=last_login&#038;order=<?php echo $order?>"><span>최종로그인</span><span class=""></span></a></th>
					<th scope="col" class="manage-column">메일/문자</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<th scope="col" class="manage-column column-cb check-column">&nbsp;&nbsp;<input type="checkbox"></th>
					<th scope="col" class="manage-column <?php echo $sorted1?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=user_no&#038;order=<?php echo $order?>"><span>번호</span><span class=""></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted2?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=user_id&#038;order=<?php echo $order?>"><span>아이디</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted3?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=name&#038;order=<?php echo $order?>"><span>이름</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted4?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=user_class&#038;order=<?php echo $order?>"><span>등급</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column">이메일</th>
					<th scope="col" class="manage-column">휴대폰</th>
					<th scope="col" class="manage-column <?php echo $sorted7?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=reg_date&#038;order=<?php echo $order?>"><span>결제방식</span><span class=""></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted5?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=last_login&#038;order=<?php echo $order?>"><span>가입일</span><span class="sorting-indicator"></span></a></th>
					<th scope="col" class="manage-column <?php echo $sorted6?> <?php echo $order?>"><a href="<?php echo $_SERVER['PHP_SELF']?>?page=bbse_commerce_member&#038;orderby=reg_date&#038;order=<?php echo $order?>"><span>최종로그인</span><span class=""></span></a></th>
					<th scope="col" class="manage-column">메일/문자</th>
				</tr>
				</tfoot>
				<tbody id="the-list">
				<?php
				if($total <= 0){
				?>
				<tr valign="middle">
					<td colspan="10" align="center">회원 목록이 없습니다.</td>
				</tr>
				<?php
				}else{
					foreach($result as $i => $data){
						$num = ($total-$start_pos) - $i;
						if($i % 2 == 0) $alternate_class = "class=\"alternate\"";
						else $alternate_class = "";
				?>
				<tr <?php echo $alternate_class?> valign="top">
					<td style="padding-top:10px;text-align:center;" scope="row" class="check-column">&nbsp;&nbsp;<input type="checkbox" name="check[]" value="<?=$data->user_no?>"></td>
					<td style="padding-left:10px;text-align:center"><?php echo number_format($num)?></td>
					<td style="padding-left:10px;">
						<strong><a href="admin.php?page=bbse_commerce_member&cType=list<?php echo "&user_no=".$data->user_no?>&mode=edit&paged=<?php echo $paged?>&<?php echo $queryString?>" title="회원정보 편집하기"><?php echo stripslashes($data->user_id)?></a></strong>
						<div class="row-actions">
							<span class='edit'><a class='' title='이 회원에게 SMS' href="admin.php?page=bbse_commerce_member&cType=sms<?php echo "&mode=send&user_no=".$data->user_no?>">SMS</a> | </span>
							<span class='edit'><a class='' title='이 회원에게 메일' href="admin.php?page=bbse_commerce_member&cType=mail<?php echo "&user_no=".$data->user_no?>">메일</a> | </span>
							<span class='edit'><a class='' title="회원정보 편집하기" href="admin.php?page=bbse_commerce_member&cType=list<?php echo "&user_no=".$data->user_no?>&mode=edit&paged=<?php echo $paged?>&<?php echo $queryString?>" >편집</a> | </span>
							<span class='trash'><a class='' title='이 회원을 삭제' href="javascript:delete_list('<?=$data->user_no?>');">삭제</a></span>
						</div>	
					</td>
					<td style="padding-left:10px;"><?php echo stripslashes($data->name)?></td>
					<td style="padding-left:10px; "><?php echo $class_arr[$data->user_class]?></td>
					<td><?php echo $data->email?>&nbsp;</td>
					<td><?php echo $data->hp?></td>
					<td style="padding-left:10px; text-align: center;"><strong><span><?php if($data->payMode == '01'){ echo "월별결제"; }?></span><span style="color: red;"><?php if($data->payMode == '02'){echo "건별결제";}?></span></strong></td>
					<td style="padding-left:10px; "><?php echo date("Y-m-d H:i:s", $data->reg_date)?></td>
					<td style="padding-left:10px; "><?php echo ($data->last_login)?date("Y-m-d H:i:s", $data->last_login):"-"?></td>
					<td style="text-align:center;"><?php echo ($data->email_reception=="1")?"허용":"허용안함"?><br><?php echo ($data->sms_reception=="1")?"허용":"허용안함"?></td>
					
				</tr>
				<?php 
					}
				}
				?>
				</tbody>
				</table>
				<div style="margin-top:20px;">
					<ul class='title-sub-desc none-content'>
						<li>
							<select name="tBatch2">
								<option value="-1">일괄 작업</option>
								<option value="remove">선택 삭제</option>
								<option value="sms">선택 SMS 발송</option>
								<option value="email">선택 이메일 발송</option>
							</select>
							<input type="button" name="btn_batch" id="doaction" class="button-secondary" value="적용" onclick="delete_check_list(2);">
						</li>
					</ul>
				</div>

				<table align="center">
				<colgroup><col width=""></colgroup>
					<tr>
						<td>
							<?php echo bbse_commerce_get_pagination($paged, $total_pages, $add_args);?>
						</td>
					</tr>
				</table>
				</form>
				<!-- 내용 end -->

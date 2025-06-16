<?php
$search_today = date("Y-m-d");

$page=$_REQUEST['page'];
$s_status=$_REQUEST['s_status'];  // 전체, 승인대기, 승인완료
$s_best=$_REQUEST['s_best'];  // 베스트 상품평
$s_period_1=$_REQUEST['s_period_1'];
$s_period_2=$_REQUEST['s_period_2'];
$s_type=$_REQUEST['s_type'];  // 전체, 상품명, 이름, 아이디
$s_keyword=$_REQUEST['s_keyword'];

$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치

$viewIdx=$_REQUEST['viewIdx']; // 대시보드에서 넘어오는 값

unset($prepareParm);
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

		// 날짜(datepicker) initialize (2)
		jQuery(".datepicker").datepicker(jQuery.datepicker.regional["ko"]);
		jQuery('.datepicker').datepicker('option', {dateFormat:'yy-mm-dd'});

		var viewIdx="<?php echo $viewIdx;?>";
		if(viewIdx) view_answer(viewIdx);
	});

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

			jQuery("#s_period_1").val(result);
			jQuery("#s_period_2").val(v);
		}
		else{
			jQuery("#s_period_1").val(result);
			jQuery("#s_period_2").val(v);
		}
	}


	function checkAll(){
		if(jQuery("#check_all").is(":checked")) jQuery("input[name=check\\[\\]]").attr("checked",true);
		else jQuery("input[name=check\\[\\]]").attr("checked",false);
	}

	function search_submit(paged){
		var page="<?php echo $page;?>";
		var s_type=jQuery("#s_type").val();
		var s_best=jQuery("#s_best").val();
		var per_page=jQuery("#per_page").val();
		var s_keyword=jQuery("#s_keyword").val();
		var s_period_1=jQuery("#s_period_1").val();
		var s_period_2=jQuery("#s_period_2").val();
		var s_status=jQuery("#s_status").val();

		if(s_type && !s_keyword){
			alert('검색 키워드를 입력해 주세요.          ');
			jQuery("#s_keyword").focus();
			return;
		}

		var strPara="page="+page+"&per_page="+per_page;

		if(paged) strPara +="&paged="+paged;
		if(s_keyword) strPara +="&s_keyword="+s_keyword;
		if(s_period_1) strPara +="&s_period_1="+s_period_1;
		if(s_period_2) strPara +="&s_period_2="+s_period_2;
		if(s_type) strPara +="&s_type="+s_type;
		if(s_best) strPara +="&s_best="+s_best;
		if(s_status) strPara +="&s_status="+s_status;

		window.location.href ="admin.php?"+strPara;
	}

	function view_answer(tIdx){
		if (jQuery("#review-"+tIdx).css('display')=="none"){
			var listCnt = jQuery('input[name=check\\[\\]]').size();
			for(z=0;z<listCnt;z++){
				var tmpID=jQuery('input[name=check\\[\\]]').eq(z).val();
				if(tmpID!=tIdx) jQuery("#review-"+tmpID).hide();
			}
		}

		jQuery("#review-"+tIdx).toggle();
	}

	function review_submit(tMode,tIdx){
		var paged="<?php echo $paged;?>";

		if(confirm("상품평을 삭제하시겠습니까?    ")){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-review.exec.php', 
				data: {tMode:tMode, tIdx:tIdx}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('상품평을 정상적으로 삭제처리 되었습니다.   ');
						search_submit(paged);
					}
					else if(result=='DbError'){
						alert('[Error !] DB 오류 입니다.   ');
					}
					else if(result=='notExistReview'){
						alert('상품평이 존재하지 않습니다.   ');
					}
					else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}

	function review_cancel_best(tIdx){
		var paged="<?php echo $paged;?>";
		var tMode="removeBest";

		if(confirm("상품평을 베스트상품평에서 제외하시겠습니까?    ")){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-review.exec.php', 
				data: {tMode:tMode, tIdx:tIdx}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('상품평을 정상적으로 베스트상품평에서 제외하였습니다.   ');
						search_submit(paged);
					}
					else if(result=='DbError'){
						alert('[Error !] DB 오류 입니다.   ');
					}
					else if(result=='notExistReview'){
						alert('상품평이 존재하지 않습니다.   ');
					}
					else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}

	function view_list(sStatus){
		var goUrl="";
		var per_page=jQuery("#per_page").val();
		if(sStatus=='all') goUrl="admin.php?page=bbse_commerce_review&per_page="+per_page;
		else if(sStatus=='best') goUrl="admin.php?page=bbse_commerce_review&s_best="+sStatus+"&per_page="+per_page;
		else goUrl="admin.php?page=bbse_commerce_review&s_status="+sStatus+"&per_page="+per_page;
		window.location.href =goUrl;
	}

	function change_status(tStatus){
		var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
		var tData="";
		var tMode="bulkAction";
		var paged="<?php echo $paged;?>";
		var statusStr="";
		if(tStatus=='approve') statusStr="승인처리";
		else if(tStatus=='remove') statusStr="삭제처리";
		else if(tStatus=='best') statusStr="베스트 상품평 처리";
		else if(tStatus=='removeBest') statusStr="베스트상품평에서 제외";

		if(!tStatus){
			alert("일괄 작업을 선택해 주세요.     ");
			return;
		}
		if(chked<=0) {
			alert("일괄 작업을 실행 할 상품평을 선택해주세요.");
			return;
		}

		for(i=0;i<chked;i++){
			if(tData) tData +=",";
			tData +=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val();
		}

		if(confirm("선택된 상품평을 "+statusStr+"하시겠습니까?   ")){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-review.exec.php', 
				data: {tMode:tMode, tStatus:tStatus, tData:tData}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert('상품평을 정상적으로 '+statusStr+'하였습니다.   ');
						search_submit(paged);
					}
					else if(result=='notUseEarn'){
						alert('상품평 작성 적립금을 사용하지 않고있습니다.      ');
					}
					else if(result=='zeroEarn'){
						alert("상품평 작성 적립금이 '0'원 입니다.        ");
					}
					else if(result=='dbError'){
						alert('[Error] DB 오류 입니다.   ');
					}
					else if(result=='zeroApprove' || result=='zeroBest'){
						alert("총 '0'건의 상품평을 "+statusStr+"하였습니다.     ");
					}
					else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}

	function user_view(tData){
		var tbHeight = 268;
		var tbWidth=450;
		tb_show("회원정보 - "+tData, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-user-info.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	function social_view(tData){
		var tbHeight = 268;
		var tbWidth=450;
		tb_show("소셜로그인 정보", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-social-info.php?tData="+tData+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

</script>
<?php
$sOption="";
switch($s_status){
	case "" :
		$sOption .=" AND (r_earn_paid='P' OR r_earn_paid='N')";
	break;
	case "P" :
		$sOption .=" AND r_earn_paid='P'";
	break;
	case "N" :
		$sOption .=" AND r_earn_paid='N'";
	break;
	default:
		$sOption="";
	break;
}

switch($s_best){
	case "" :
		$sOption .=" AND (r_best='Y' OR r_best='N')";
	break;
	case "best" :
		$sOption .=" AND r_best='Y'";
	break;
	default:
	break;
}

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND write_date>='".$s_period_1_time."'";
}

if($s_period_2){
	$tmp_2_priod=explode("-",$s_period_2);
	$s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
	$sOption .=" AND write_date<='".$s_period_2_time."'";
}

if($s_keyword){
	switch($s_type){
		case "" :
			$sOption .=" AND (goods_name LIKE %s OR user_name LIKE %s OR user_id LIKE %s OR r_subject LIKE %s)";
			$prepareParm[]="%".like_escape($s_keyword)."%";
			$prepareParm[]="%".like_escape($s_keyword)."%";
			$prepareParm[]="%".like_escape($s_keyword)."%";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		case "r_subject" :
			$sOption .=" AND r_subject LIKE %s";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		case "goods_name" :
			$sOption .=" AND goods_name LIKE %s";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		case "user_name" :
			$sOption .=" AND user_name LIKE %s";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		case "user_id" :
			$sOption .=" AND user_id LIKE %s";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		default:
			$sOption="";
		break;
	}
}

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_review WHERE idx<>''".$sOption." ORDER BY idx DESC LIMIT %d, %d", $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_review WHERE idx<>''".$sOption, $prepareParm);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품문의 수
$total_pages = ceil($s_total / $per_page);   // 총 페이지수

$total_all = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>''");    // 총 상품평 수
$total_N = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND r_earn_paid='N'");    // 승인대기 상품평 수
$total_P = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND r_earn_paid='P'");    // 승인완료 상품평 수
$total_best = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_review WHERE idx<>'' AND r_best='Y'");    // 베스트 상품평 수

/* Query String */
$add_args = array("page"=>$page, "s_best"=>$s_best, "s_status"=>$s_status, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);

?>
<div class="wrap">
	<div style="margin-bottom:10px;">
		<h2>고객상품평</h2>
		<hr>
		<ul class='title-sub-desc'>
			<li <?php echo (!$s_status || $s_status=='all')?"class=\"current\"":"";?>><a title='전체 상품평 보기' href="javascript:view_list('all');">전체(<?php echo $total_all;?>)</a></li>
			<li <?php echo ($s_best=='best')?"class=\"current\"":"";?>><a title='승인대기 상품평 보기' href="javascript:view_list('best');">베스트 상품평(<?php echo $total_best;?>)</a></li>
			<li <?php echo ($s_best!='best' && $s_status=='P')?"class=\"current\"":"";?>><a title='승인대기 상품평 보기' href="javascript:view_list('P');">승인완료 상품평(<?php echo $total_P;?>)</a></li>
			<li <?php echo ($s_best!='best' && $s_status=='N')?"class=\"current\"":"";?>><a title='승인대기 상품평 보기' href="javascript:view_list('N');">승인대기 상품평(<?php echo $total_N;?>)</a></li>
		</ul>
	<div class="clearfix"></div>
	<?php
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='earn'");
	$earnData=unserialize($confData->config_data);
	if($earnData['earn_review_use']=='on') $earn_point=$earnData['earn_review_point'];
	?>
		<div class="borderBox" style="margin-top:20px;">
		  * 승인완료처리 : 승인처리 시 적립금<?php echo ($earnData['earn_review_use']=='on')?" ".number_format($earn_point)."원을 지급합니다.":"을 지급하지 않습니다.";?><br />
		  * 삭제 : 승인처리된 게시물은 삭제시 적립금이 차감되지않습니다.<br />
		  * 베스트상품평 : 최신 등록한 상품평부터 순차적으로 적용됩니다. (메인 노출시 최대12개까지 노출, 테마환경설정>메인상품설정>상품배치설정 기준)<br />
		</div>
	</div>

	<div class="clearfix"></div>

	<div class="borderBox-gray" style="min-height:40px;">
		<table border="0" width="100%" cellpadding="0" cellspacing="3">
			<colgroup><col width=""></colgroup>
			<tr>
				<td>조회기간 <input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;			
				<input type="button" name="" class="button" value="오늘" onclick="getDateAdd('<?=$search_today?>',0);"/>
				<input type="button" name="" class="button" value="일주일" onclick="getDateAdd('<?=$search_today?>',-7);"/>
				<input type="button" name="" class="button" value="15일" onclick="getDateAdd('<?=$search_today?>',-15);"/>
				<input type="button" name="" class="button" value="한달" onclick="getDateAdd('<?=$search_today?>',-30);"/>
				<input type="button" name="" class="button" value="3개월" onclick="getDateAdd('<?=$search_today?>',-90);"/>
				<input type="button" name="" class="button" value="6개월" onclick="getDateAdd('<?=$search_today?>',-180);"/>
				<input type="button" name="" class="button" value="1년"   onclick="getDateAdd('<?=$search_today?>',-365);"/>
				</td>
			</tr>
			<tr>
				<td>상태분류 
					<select name='s_status' id='s_status'>
						<option value='' <?php echo (!$s_status || $s_status=='all')?"selected='selected'":"";?>>상태분류</option>
						<option value='N' <?php echo ($s_status=='N')?"selected='selected'":"";?>>승인대기</option>
						<option value='P' <?php echo ($s_status=='P')?"selected='selected'":"";?>>승인완료</option>
					</select>&nbsp;&nbsp;
					<select name='s_type' id='s_type'>
						<option value='' <?php echo (!$s_type || $s_type=='all')?"selected='selected'":"";?>>통합검색</option>
						<option value='r_subject' <?php echo ($s_type=='r_subject')?"selected='selected'":"";?>>제목</option>
						<option value='goods_name' <?php echo ($s_type=='goods_name')?"selected='selected'":"";?>>상품명</option>
						<option value='user_name' <?php echo ($s_type=='user_name')?"selected='selected'":"";?>>이름</option>
						<option value='user_id' <?php echo ($s_type=='user_id')?"selected='selected'":"";?>>아이디</option>
					</select>&nbsp;&nbsp;
					<input type="text" name="s_keyword" id="s_keyword" value="<?php echo $s_keyword;?>" />&nbsp;&nbsp;
					<input type="hidden" name="s_best" id="s_best" value="<?php echo $s_best;?>" />
					<input type="submit" name="search-query-submit" id="search-query-submit" onClick="search_submit('');" class="button apply" value="검색"  />

				<?php if($s_period_1 || $s_period_2 || $s_best || $s_status || $s_type || $s_keyword){?>
					<button type="button"class="button-small blue" onClick="window.location.href ='admin.php?page=bbse_commerce_review';" style="float:right;height:27px;">전체보기</button>
				<?php }?>
				</td>
			</tr>
		</table>
	</div>

	<div class="clearfix"></div>
	<div style="margin-top:30px;">
		<ul class='title-sub-desc none-content'>
			<li>
				<select name='bulk_action' id='bulk_action'>
					<option value=''>일괄 작업</option>
				<?php if($s_status!='P'){?>
					<option value='approve'>선택 승인</option>
				<?php }?>
					<option value='remove'>선택 삭제</option>
				<?php if($s_best=='best'){?>
					<option value='removeBest'>선택 베스트 상품평 취소</option>
				<?php }else{?>
					<option value='best'>선택 베스트 상품평</option>
				<?php }?>
				</select>
				<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_status(jQuery('#bulk_action').val());" value="적용"  />
			</li>
		</ul>
		<ul class='title-sub-desc none-content' style="float:right;">
			<li>
				<select name='per_page' id='per_page' onChange="search_submit('');">
					<option <?php echo ($per_page=='2')?"selected='selected'":"";?> value='2'>2개씩 보기</option>
					<option <?php echo ($per_page=='3')?"selected='selected'":"";?> value='3'>3개씩 보기</option>
					<option <?php echo ($per_page=='4')?"selected='selected'":"";?> value='4'>4개씩 보기</option>
					<option <?php echo ($per_page=='5')?"selected='selected'":"";?> value='5'>5개씩 보기</option>
					<option <?php echo ($per_page=='10')?"selected='selected'":"";?> value='10'>10개씩 보기</option>
					<option <?php echo ($per_page=='20')?"selected='selected'":"";?> value='20'>20개씩 보기</option>
					<option <?php echo ($per_page=='30')?"selected='selected'":"";?> value='30'>30개씩 보기</option>
					<option <?php echo ($per_page=='40')?"selected='selected'":"";?> value='40'>40개씩 보기</option>
					<option <?php echo ($per_page=='50')?"selected='selected'":"";?> value='50'>50개씩 보기</option>
				</select>
			</li>
		</ul>
	</div>
	<div class="clearfix"></div>

	<div style="margin-top:20px;">
		<form name="goodsFrm" id="goodsFrm">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="50px;"><col width="70px;"><col width="7%;"><col width="25%"><col width=""><col width="8%;"><col width="10%"><col width="7%"><col width="140px"></colgroup>
				<tbody>
					<tr>
						<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
						<th>번호</th>
						<th>상태</th>
						<th>상품</th>
						<th>제목</th>
						<th>평점</th>
						<th>작성자</th>
						<th>등록일자</th>
						<th>관리</th>
					</tr>
			<?php
			if($s_total>'0'){
				foreach($result as $i=>$data) {
					$num = ($s_total-$start_pos) - $i; //번호
			?>
					<tr style="height:70px;">
						<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
						<td style="text-align:center;"><a href="javascript:view_answer(<?php echo $data->idx;?>);"><?php echo $num;?></a></td>
						<td style="text-align:center;">
							<?php echo ($data->r_earn_paid=='P')?"<button type=\"button\" class=\"button-small-fill green default-cursor\">승인완료</button><br /><font color=\"#2D8848\">(".number_format($data->r_earn_point)."원 적립)</font>":"<button type=\"button\" class=\"button-small-fill orange default-cursor\">승인대기</button>";?>
						</td>
						<td><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $data->goods_idx;?>" target="_blank"><?php echo $data->goods_name;?></a></td>
						<td><?php echo ($data->r_attach_new)?"<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_image_exitst.png\" />":"";?> <a href="javascript:view_answer(<?php echo $data->idx;?>);"><?php echo $data->r_subject;?></a><?php echo ($data->r_best=='Y')?"&nbsp;&nbsp;&nbsp;<button type=\"button\" class=\"button-small-fill red default-cursor\">베스트상품평</button>&nbsp;<img src=\"".BBSE_COMMERCE_PLUGIN_WEB_URL."images/btn_delete.png\" onClick=\"review_cancel_best('".$data->idx."');\" style=\"width:13px;height:13px;cursor:pointer;\" alt=\"베스트상품평 취소\" title=\"베스트상품평 취소\" /></span>":"";?></td>
						<td style="text-align:center;"><div style="width:83px;height:16px;text-align:left;"><span class="admin_cmt_star cmt<?php echo $data->r_value;?>">별점 <?php echo $data->r_value;?>점/5점</span></div></td>
						<td style="text-align:center;"><?php echo $data->user_name;?><br /><span style="color:#00A2E8;">(<span onClick="<?php echo ($data->user_id)?"user_view('".$data->user_id."')":"social_view('".$data->sns_idx."')";?>;" style="cursor:pointer;"><?php echo ($data->user_id)?$data->user_id:"소셜로그인";?></span>)</span></td>
						<td style="text-align:center;"><?php echo date("Y-m-d",$data->write_date);?></td>
						<td style="text-align:center;"><button type="button"class="button-small red" onClick="review_submit('removeReview',<?php echo $data->idx;?>,'');" style="height:25px;">삭제</button></td>
					</tr>
					<tr id="review-<?php echo $data->idx;?>" style="display:none;">
						<td></td>
						<td style="vertical-align:top;text-align:center;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-arrow-30-gray.png"></td>
						<td colspan="6">
							<table border="0" cellpadding="0" cellspacing="0" width="70%">
								<colgroup><col width=""><col width="140px"></colgroup>
								<tbody>
								<?php if($data->r_attach_new){?>
									<tr>
										<td style="border-bottom:0px;"><img src="<?php echo BBSE_COMMERCE_UPLOAD_BASE_URL."/bbse-commerce/".$data->r_attach_new;?>" alt="첨부이미지" style="max-width:100%;" /></td>
										<td style="border-bottom:0px;"></td>
									</tr>
								<?php }?>
									<tr>
										<td style="border-bottom:0px;"><?php echo bbse_commerce_nl2br_markup($data->r_contents);?></td>
										<td style="border-bottom:0px;"></td>
									</tr>
								</tbody>
							</table>
						</td>
						<td></td>
					</tr>
			<?php
				}
			}
			else{
			?>
					<tr>
						<td style="height:60px;text-align:center;" colspan="9">등록 된 상품평이 존재하지 않습니다.</td>
					</tr>
			<?php 
			}
			?>
				</tbody>
			</table>
		</div>
	</div>

	<div style="margin-top:20px;">
		<ul class='title-sub-desc none-content'>
			<li>
				<select name='bulk_action2' id='bulk_action2'>
					<option value=''>일괄 작업</option>
				<?php if($s_status!='P'){?>
					<option value='approve'>선택 승인</option>
				<?php }?>
					<option value='remove'>선택 삭제</option>
				<?php if($s_best=='best'){?>
					<option value='removeBest'>선택 베스트 상품평 취소</option>
				<?php }else{?>
					<option value='best'>선택 베스트 상품평</option>
				<?php }?>
				</select>
				<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_status(jQuery('#bulk_action2').val());" value="적용"  />
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
</div>

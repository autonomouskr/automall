<?php
$search_today = date("Y-m-d");

$page=$_REQUEST['page'];
$s_status=$_REQUEST['s_status'];  // 전체, 승인대기, 승인완료
$s_period_1=$_REQUEST['s_period_1'];
$s_period_2=$_REQUEST['s_period_2'];
$s_type=$_REQUEST['s_type'];  // 전체, 아이디, 전화번호, 이메일
$s_keyword=$_REQUEST['s_keyword'];

$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치

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
	});

	function checkAll(){
		if(jQuery("#check_all").is(":checked")) jQuery("input[name=check\\[\\]]").attr("checked",true);
		else jQuery("input[name=check\\[\\]]").attr("checked",false);
	}

	function search_submit(paged){
		var page="<?php echo $page;?>";
		var s_type=jQuery("#s_type").val();
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
		if(s_status) strPara +="&s_status="+s_status;

		window.location.href ="admin.php?"+strPara;
	}

	function notice_submit(tMode,tIdx){
		var paged="<?php echo $paged;?>";

		if(confirm("해당 '품절상품 일고알림' 목록을 삭제하시겠습니까?    ")){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-soldout-notice.exec.php', 
				data: {tMode:tMode, tIdx:tIdx}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert("'품절상품 일고알림' 목록을 정상적으로 삭제하였습니다.   ");
						search_submit(paged);
					}
					else if(result=='DbError'){
						alert("[Error !] DB 오류 입니다.   ");
					}
					else if(result=='notExistNotice'){
						alert("'품절상품 일고알림' 목록이 존재하지 않습니다.   ");
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
		if(sStatus=='all') goUrl="admin.php?page=bbse_commerce_soldout_notice&per_page="+per_page;
		else goUrl="admin.php?page=bbse_commerce_soldout_notice&s_status="+sStatus+"&per_page="+per_page;
		window.location.href =goUrl;
	}

	function change_status(tStatus){
		var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
		var tData="";
		var tMode="bulkAction";
		var paged="<?php echo $paged;?>";

		if(!tStatus){
			alert("일괄 작업을 선택해 주세요.     ");
			return;
		}
		if(chked<=0) {
			alert("일괄 작업을 실행 할 '품절상품 일고알림' 목록을 선택해주세요.");
			return;
		}

		for(i=0;i<chked;i++){
			if(tData) tData +=",";
			tData +=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val();
		}

		if(confirm("선택된 '품절상품 일고알림' 목록을 삭제하시겠습니까?   ")){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-soldout-notice.exec.php', 
				data: {tMode:tMode, tStatus:tStatus, tData:tData}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result=='success'){
						alert("'품절상품 일고알림' 목록을 정상적으로 삭제하였습니다.   ");
						search_submit(paged);
					}
					else if(result=='dbError'){
						alert("[Error] DB 오류 입니다.   ");
					}
					else if(result=='zeroApprove' || result=='zeroBest'){
						alert("총 '0'건의 '품절상품 일고알림' 목록을 삭제하였습니다.     ");
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
</script>
<?php
$sOption="";
switch($s_status){
	case "N" :
		$sOption .=" AND sms_yn='N' AND email_yn='N'";
	break;
	case "Y" :
		$sOption .=" AND (sms_yn='Y' OR email_yn='Y')";
	break;
	default:
		$sOption="";
	break;
}

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND reg_date>='".$s_period_1_time."'";
}

if($s_period_2){
	$tmp_2_priod=explode("-",$s_period_2);
	$s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
	$sOption .=" AND reg_date<='".$s_period_2_time."'";
}

if($s_keyword){
	switch($s_type){
		case "" :
			$sOption .=" AND (user_id LIKE %s OR hp LIKE %s OR email LIKE %s)";
			$prepareParm[]="%".like_escape($s_keyword)."%";
			$prepareParm[]="%".like_escape($s_keyword)."%";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		case "user_id" :
			$sOption .=" AND user_id LIKE %s";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		case "hp" :
			$sOption .=" AND hp LIKE %s";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		case "email" :
			$sOption .=" AND email LIKE %s";
			$prepareParm[]="%".like_escape($s_keyword)."%";
		break;
		default:
			$sOption="";
		break;
	}
}

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_soldout_notice WHERE idx<>''".$sOption." ORDER BY idx DESC LIMIT %d, %d", $prepareParm);
$result = $wpdb->get_results($sql);

$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE idx<>''".$sOption, $prepareParm);
$s_total = $wpdb->get_var($s_total_sql);    // 총 상품문의 수
$total_pages = ceil($s_total / $per_page);   // 총 페이지수

$total_all = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE idx<>''");    // 총 상품평 수
$total_N = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE idx<>'' AND sms_yn='N' AND email_yn='N'");    // 알림전 수
$total_Y = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_soldout_notice WHERE idx<>'' AND (sms_yn='Y' OR email_yn='Y')");    // 알림완료 수

/* Query String */
$add_args = array("page"=>$page, "s_status"=>$s_status, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_type"=>$s_type);

?>
<div class="wrap">
	<div style="margin-bottom:10px;">
		<h2>품절상품 입고알림 목록</h2>
		<hr>
		<ul class='title-sub-desc'>
			<li <?php echo (!$s_status || $s_status=='all')?"class=\"current\"":"";?>><a title='전체 목록 보기' href="javascript:view_list('all');">전체(<?php echo $total_all;?>)</a></li>
			<li <?php echo ($s_status=='N')?"class=\"current\"":"";?>><a title='알림전 목록 보기' href="javascript:view_list('N');">알림전(<?php echo $total_N;?>)</a></li>
			<li <?php echo ($s_status=='Y')?"class=\"current\"":"";?>><a title='알림완료 목록 보기' href="javascript:view_list('Y');">알림완료(<?php echo $total_Y;?>)</a></li>
		</ul>
	<div class="clearfix"></div>
	<?php
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='earn'");
	$eargData=unserialize($confData->config_data);
	if($eargData['earn_review_use']=='on') $earn_point=$eargData['earn_review_point'];
	?>
		<div class="borderBox" style="margin-top:20px;">
			<span class='emRed'>* 품절상품 입고알림 : 품절 된 상품이 판매가능 상대가 되었을때, 알림을 받기를 원하는 회원에게 SMS 및 E-mail로 상품입고를 알려주는 서비스입니다.</span><br />
			* 품절상품 입고알림 신청목록(상품, 회원정보)은 'BBS e-Commerce - 상점관리' 메뉴의 품절상품 입고알림에서 환경설정이 가능합니다.<br />
			&nbsp;&nbsp;(품절상품 입고알림(SMS 및 E-mail) 발송은 품절상품이 판매가능 상태로 변경 되는 시점에 자동 발송 됩니다.)<br />
		</div>
	</div>

	<div class="clearfix"></div>

	<div class="borderBox-gray" style="min-height:40px;">
		<table border="0" width="100%" cellpadding="0" cellspacing="3">
			<colgroup><col width=""></colgroup>
			<tr>
				<td>조회기간 <input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;상태분류 
					<select name='s_status' id='s_status'>
						<option value='' <?php echo (!$s_status || $s_status=='all')?"selected='selected'":"";?>>상태분류</option>
						<option value='N' <?php echo ($s_status=='N')?"selected='selected'":"";?>>알림전</option>
						<option value='Y' <?php echo ($s_status=='Y')?"selected='selected'":"";?>>알림완료</option>
					</select>&nbsp;&nbsp;
					<select name='s_type' id='s_type'>
						<option value='' <?php echo (!$s_type || $s_type=='all')?"selected='selected'":"";?>>통합검색</option>
						<option value='user_id' <?php echo ($s_type=='user_id')?"selected='selected'":"";?>>아이디</option>
						<option value='hp' <?php echo ($s_type=='hp')?"selected='selected'":"";?>>전화번호</option>
						<option value='email' <?php echo ($s_type=='email')?"selected='selected'":"";?>>이메일</option>
					</select>&nbsp;&nbsp;
					<input type="text" name="s_keyword" id="s_keyword" value="<?php echo $s_keyword;?>" />&nbsp;&nbsp;
					<input type="submit" name="search-query-submit" id="search-query-submit" onClick="search_submit('');" class="button apply" value="검색"  />

				<?php if($s_period_1 || $s_period_2 || $s_status || $s_type || $s_keyword){?>
					<button type="button"class="button-small blue" onClick="window.location.href ='admin.php?page=bbse_commerce_soldout_notice';" style="float:right;height:27px;">전체보기</button>
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
					<option value='remove'>선택 삭제</option>
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
				<colgroup><col width="50px;"><col width="70px;"><col width="7%;"><col width="10%"><col width=""><col width="13%"><col width="13%;"><col width="10%"><col width="7%"><col width="140px"></colgroup>
				<tbody>
					<tr>
						<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
						<th>번호</th>
						<th>상태</th>
						<th>이미지</th>
						<th>상품명</th>
						<th>휴대폰 알림 번호</th>
						<th>이메일 알림 주소</th>
						<th>작성자</th>
						<th>등록일자</th>
						<th>관리</th>
					</tr>
			<?php
			if($s_total>'0'){
				foreach($result as $i=>$data) {
					$num = ($s_total-$start_pos) - $i; //번호

					$gData = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$data->goods_idx."'");

					if($gData->goods_basic_img) $basicImg = wp_get_attachment_image_src($gData->goods_basic_img,"goodsimage2");
					else{
						$imageList=explode(",",$gData->goods_add_img);
						if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0'],"goodsimage2");
						else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
					}

					switch ($gData->goods_display){
						case "display":
							$gStatus="<button type=\"button\" class=\"button-small-fill green default-cursor\">&nbsp;&nbsp;노출&nbsp;&nbsp;</button>";
						break;
						case "hidden":
							$gStatus="<button type=\"button\" class=\"button-small-fill red default-cursor\"> 비노출 </button>";
						break;
						case "soldout":
							$gStatus="<button type=\"button\" class=\"button-small-fill green default-cursor\">&nbsp;&nbsp;노출&nbsp;&nbsp;</button>";
						break;
						case "copy":
							$gStatus="<button type=\"button\" class=\"button-small-fill blue default-cursor\">&nbsp;&nbsp;복사&nbsp;&nbsp;</button>";
						break;
						case "trash":
							$gStatus="<button type=\"button\" class=\"button-small-fill red default-cursor\"> 휴지통 </button>";
						break;
						default:
							$gStatus="";
						break;
					}

					$ntHp=trim(str_replace("-","",$data->hp));
					$ntEmail=trim($data->email);

					$mData = $wpdb->get_row("SELECT * FROM bbse_commerce_membership WHERE user_id='".$data->user_id."'");
					$soldout=bbse_commerce_goodsSoldoutCheck($gData);
			?>
					<tr style="height:70px;">
						<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
						<td style="text-align:center;"><?php echo $num;?></td>
						<td style="text-align:center;">
							<?php echo ($data->sms_yn=='Y' || $data->email_yn=='Y')?"<button type=\"button\" class=\"button-small-fill blue default-cursor\" style='margin-left:7px;'>알림완료</button>":"<button type=\"button\" class=\"button-small-fill orange default-cursor\" style='margin-left:7px;'>알림전</button>";?>
							<br /><?php echo $gStatus;?><br /><?php echo ($soldout)?"<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_soldout.png' />":"";?>
						</td>
						<td style="text-align:center;">
							<img src="<?php echo $basicImg['0']; ?>" title="<?php echo $gData->goods_name; ?>" class="list-goods-img" />
						</td>
						<td>[상품코드:<?php echo $gData->goods_code;?>]<br /><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $data->goods_idx;?>" target="_blank"><?php echo $gData->goods_name;?></a></td>
						<td style="text-align:center;"><?php echo ($ntHp)?$ntHp:"-";?> <?php if($ntHp){ echo ($data->sms_yn=='Y')?" <span style='color:#00A2E8;font-size:11px;font-weight:700;' title='SMS 알림완료'>(Y)</span>":" <span style='color:#ED1C24;font-size:11px;font-weight:700;' title='SMS 알림전'>(N)</span>"; }?></td>
						<td style="text-align:center;"><?php echo ($ntEmail)?$ntEmail:"-";?> <?php if($ntEmail){ echo ($data->email_yn=='Y')?" <span style='color:#00A2E8;font-size:11px;font-weight:700;' title='E-mail 알림완료'>(Y)</span>":" <span style='color:#ED1C24;font-size:11px;font-weight:700;' title='E-mail 알림전'>(N)</span>"; }?></td>
						<td style="text-align:center;"><?php echo ($mData->name)?$mData->name."<br />":"";?><span style="color:#00A2E8;">(<span onClick="<?php echo "user_view('".$data->user_id."')";?>;" style="cursor:pointer;"><?php echo $data->user_id;?></span>)</span></td>
						<td style="text-align:center;"><?php echo date("Y-m-d",$data->reg_date);?></td>
						<td style="text-align:center;"><button type="button"class="button-small red" onClick="notice_submit('removeNotice',<?php echo $data->idx;?>,'');" style="height:25px;">삭제</button></td>
					</tr>
			<?php
				}
			}
			else{
			?>
					<tr>
						<td style="height:128px;text-align:center;" colspan="10">등록 된 '품절상품 입고알림' 목록이 존재하지 않습니다.</td>
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
					<option value='remove'>선택 삭제</option>
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

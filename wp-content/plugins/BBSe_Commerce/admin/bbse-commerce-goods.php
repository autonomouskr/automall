<?php
$page=$_REQUEST['page'];
$s_list=(!$_REQUEST['s_list'])?"all":$_REQUEST['s_list'];  // 전체, 노출, 비노출, 노출품절, 휴지통
$s_icon=$_REQUEST['s_icon'];
$s_period_1=$_REQUEST['s_period_1'];
$s_period_2=$_REQUEST['s_period_2'];
$s_category=$_REQUEST['s_category'];
$s_keyword=$_REQUEST['s_keyword'];

$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치

$nvrCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='navershop'");

if($nvrCnt>'0'){
	$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='navershop'");
	$nvrData=unserialize($confData->config_data);
}

$nvrPayCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_config WHERE config_type='naverpay'");

if($nvrPayCnt>'0'){
	$confPayData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='naverpay'");
	$nvrPayData=unserialize($confPayData->config_data);
}
?>
<script language="javascript">
	jQuery(document).ready(function() {
		jQuery('#s_keyword').keyup(function(e) {
			if (e.keyCode == 13) search_submit();       
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

	function view_list(sList){
		var goUrl="";
		var per_page=jQuery("#per_page").val();
		if(sList=='all') goUrl="admin.php?page=bbse_commerce_goods&per_page="+per_page;
		else goUrl="admin.php?page=bbse_commerce_goods&s_list="+sList+"&per_page="+per_page;
		window.location.href =goUrl;
	}
	
	function search_submit(){
		var page="<?php echo $page;?>";
		var s_list="<?php echo $s_list;?>";
		var per_page=jQuery("#per_page").val();
		var s_keyword=jQuery("#s_keyword").val();
		var s_period_1=jQuery("#s_period_1").val();
		var s_period_2=jQuery("#s_period_2").val();
		var s_icon=jQuery("#s_icon").val();
		var s_category=jQuery("#s_category").val();

		var strPara="page="+page+"&s_list="+s_list+"&per_page="+per_page;

		if(s_keyword) strPara +="&s_keyword="+s_keyword;
		if(s_period_1) strPara +="&s_period_1="+s_period_1;
		if(s_period_2) strPara +="&s_period_2="+s_period_2;
		if(s_icon) strPara +="&s_icon="+s_icon;
		if(s_category) strPara +="&s_category="+s_category;

		window.location.href ="admin.php?"+strPara;
	}

	function change_status(tMode,tStatus,tData){
		if(!tStatus){
			alert("일괄 작업을 선택해 주세요.     ");
			return;
		}

		if(!tData){
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var s_list="<?php echo $s_list;?>";
			var staStr="";

			if(s_list=='trash') staStr="복원";
			else staStr="변경";

			for(i=0;i<chked;i++){
				if(tData) tData +=",";
				tData +=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val();
			}

			if(chked<=0 || !tData) {
				alert("일괄 작업을 실행 할 상품을 선택해주세요.");
				return;
			}

			if(tStatus=='display'){
				if(!confirm('선택한 상품을 “노출” 상태로 '+staStr+'하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='hidden'){
				if(!confirm('선택한 상품을 “비노출” 상태로 '+staStr+'하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='soldout'){
				if(!confirm('선택한 상품을 “노출품절” 상태로 '+staStr+'하시겠습니까?\n비노출로 변경하기전까지 “노출품절”로 노출됩니다.   ')){
					return;
				}
			}
			else if(tStatus=='trash'){
				if(!confirm('선택한 상품을 “휴지통”으로 이동하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='nshopin'){
				if(!confirm('해당 상품을 “네이버 지식쇼핑”에 추가하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='nshopout'){
				if(!confirm('해당 상품을 “네이버 지식쇼핑”에서 제외하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='npayin'){
				if(!confirm('해당 상품을 “네이버페이”에 추가하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='npayout'){
				if(!confirm('해당 상품을 “네이버페이”에서 제외하시겠습니까?   ')){
					return;
				}
			}
			else if(tStatus=='empty-trash'){
				if(!confirm('영구삭제 시 상품은 완전삭제되어 복구되지 않습니다.\n선택한 상품을 "영구적으로 삭제" 하시겠습니까?       ')){
					return;
				}
			}
		}
		else if(tStatus=='trash' && tData){
			if(!confirm('해당 상품을 “휴지통”으로 이동하시겠습니까?   ')){
				return;
			}
		}
		else if(tData=='empty'){
			if(!confirm('휴지통을 비울 시 상품은 완전삭제되어 복구되지 않습니다.\n휴지통을 비우시겠습니까?       ')){
				return;
			}
		}
		else if(tData && tStatus=='display'){
			if(!confirm('해당 상품을 “노출” 상태로 복원하시겠습니까?         ')){
				return;
			}
		}
		else if(tData && tStatus=='hidden'){
			if(!confirm('해당 상품을 “비노출” 상태로 복원하시겠습니까?      ')){
				return;
			}
		}
		else if(tData && tStatus=='soldout'){
			if(!confirm('해당 상품을 “노출품절” 상태로 복원하시겠습니까?\n비노출로 변경하기전까지 “노출품절”로 노출됩니다.   ')){
				return;
			}
		}
		else if(tData && tStatus=='empty-trash'){
			if(!confirm('영구삭제 시 상품은 완전삭제되어 복구되지 않습니다.\n해당 상품을 "영구적으로 삭제" 하시겠습니까?          ')){
				return;
			}
		}

		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-goods.exec.php', 
			data: {tMode:tMode, tStatus:tStatus, tData:tData}, 
			success: function(data){
				//alert(data);
				var result = data; 
				if(result=='success'){
					if(tStatus=='copy'){
						alert('상품이 정상적으로 복사되었습니다.   \n복사 목록에서 상품의 상태값을 수정하신 후 사용해 주세요.   ');
					}
					search_submit();
				}
			}, 
			error: function(data, status, err){
				alert("서버와의 통신이 실패했습니다.   ");
			}
		});	
	}

	function go_add_goods(){
		window.location.href ="admin.php?page=bbse_commerce_goods_add";
	}

	function checkAll(){
		if(jQuery("#check_all").is(":checked")) jQuery("input[name=check\\[\\]]").attr("checked",true);
		else jQuery("input[name=check\\[\\]]").attr("checked",false);
	}

	function option_view(tData,goodsName){
		var tbHeight = window.innerHeight * .85;
		tb_show("상품 옵션 - "+goodsName, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-option-view.php?tData="+tData+"&#38;height="+tbHeight+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}
</script>
<div class="wrap">
<?php
$sOption="";

switch($s_list){
	case "" :
	case "all" :
		$sOption .=" AND goods_display<>'trash' AND goods_display<>'copy'";
	break;
	case "display" :
		$sOption .=" AND (goods_display='display' OR goods_display='soldout')";
	break;
	case "hidden" :
		$sOption .=" AND goods_display='hidden'";
	break;
	case "soldout" :

	break;
	case "nshop" :
		$sOption .=" AND goods_naver_shop='on'";
	break;
	case "npay" :
		$sOption .=" AND goods_naver_pay='on'";
	break;
	case "trash" :
		$sOption .=" AND goods_display='trash'";
	break;
	case "copy" :
		$sOption .=" AND goods_display='copy'";
	break;
	default:
		$sOption="";
	break;
}

if($s_icon){
	switch($s_icon){
		case "none" :
			$sOption .=" AND goods_icon_new='' AND goods_icon_best=''";
		break;
		case "new" :
			$sOption .=" AND goods_icon_new='view' AND goods_icon_best=''";
		break;
		case "best" :
			$sOption .=" AND goods_icon_new='' AND goods_icon_best='view'";
		break;
		case "newnbest" :
			$sOption .=" AND goods_icon_new='view' AND goods_icon_best='view'";
		break;
		case "nshop" :
			$sOption .=" AND goods_naver_shop='on'";
		break;
		case "npay" :
			$sOption .=" AND goods_naver_pay='on'";
		break;
		default:
		break;
	}
}

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND goods_reg_date>='".$s_period_1_time."'";
}

if($s_period_2){
	$tmp_2_priod=explode("-",$s_period_2);
	$s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
	$sOption .=" AND goods_reg_date<='".$s_period_2_time."'";
}

if($s_category){
	$sOption .=" AND goods_cat_list LIKE %s";
	$prepareParm[]="%".like_escape("|".$s_category."|")."%";
}

if($s_keyword){
	$sOption .=" AND (goods_name LIKE %s OR goods_code LIKE %s OR goods_description LIKE %s OR goods_company LIKE %s OR goods_barcode LIKE %s OR goods_unique_code LIKE %s OR goods_location_no LIKE %s)";
	$prepareParm[]="%".like_escape($s_keyword)."%";
	$prepareParm[]="%".like_escape($s_keyword)."%";
	$prepareParm[]="%".like_escape($s_keyword)."%";
	$prepareParm[]="%".like_escape($s_keyword)."%";
	$prepareParm[]="%".like_escape($s_keyword)."%";
	$prepareParm[]="%".like_escape($s_keyword)."%";
	$prepareParm[]="%".like_escape($s_keyword)."%";
}

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

if($s_list=='soldout'){
	$sql  = $wpdb->prepare('
		SELECT	*
		FROM	bbse_commerce_goods LEFT JOIN bbse_commerce_goods_option 
		ON 		bbse_commerce_goods.idx = bbse_commerce_goods_option.goods_idx
		WHERE 	idx<>"" '.$sOption.' 
				AND	(
					bbse_commerce_goods.goods_display="soldout"
					OR (bbse_commerce_goods.goods_count_flag="goods_count" AND bbse_commerce_goods.goods_count<= 0)
					OR (bbse_commerce_goods.goods_count_flag="option_count" AND
						(
							bbse_commerce_goods_option.goods_option_item_count <="0" 
							OR bbse_commerce_goods_option.goods_option_item_soldout="soldout" 
						)
					)
				)
		GROUP BY bbse_commerce_goods.idx
		ORDER BY bbse_commerce_goods.idx DESC 
	LIMIT %d, %d', $prepareParm);
	$result = $wpdb->get_results($sql);
	//print_r($sql);
	$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM (SELECT * FROM bbse_commerce_goods WHERE idx<>''".$sOption." AND (goods_display='soldout' OR (goods_count_flag='goods_count' AND goods_count<='0') OR goods_count_flag='option_count')) A  INNER JOIN (SELECT goods_idx FROM bbse_commerce_goods_option WHERE goods_option_item_count<='0' OR goods_option_item_soldout='soldout' GROUP BY goods_idx) B ON A.idx=B.goods_idx", $prepareParm);
	$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수
}
else{
	$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_goods WHERE idx<>''".$sOption." ORDER BY idx DESC LIMIT %d, %d", $prepareParm);
	$result = $wpdb->get_results($sql);

	$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods WHERE idx<>''".$sOption, $prepareParm);
	$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수
}

$total_pages = ceil($s_total / $per_page);   // 총 페이지수

/* List Query  */
$total = count($wpdb->get_results("SELECT idx FROM bbse_commerce_goods WHERE idx<>'' AND goods_display<>'trash'"));    // 총 상품수

$total_display = count($wpdb->get_results("SELECT idx FROM bbse_commerce_goods WHERE idx<>'' AND (goods_display='display' OR goods_display='soldout')"));    // 노출 상품수
$total_hidden = count($wpdb->get_results("SELECT idx FROM bbse_commerce_goods WHERE idx<>'' AND goods_display='hidden'"));     // 비노출 상품수
$total_soldout = count($wpdb->get_results('SELECT	*
		FROM	bbse_commerce_goods LEFT JOIN bbse_commerce_goods_option 
		ON 		bbse_commerce_goods.idx = bbse_commerce_goods_option.goods_idx
		WHERE 	idx<>""
				AND	(
					bbse_commerce_goods.goods_display="soldout"
					OR (bbse_commerce_goods.goods_count_flag="goods_count" AND bbse_commerce_goods.goods_count<= 0)
					OR (bbse_commerce_goods.goods_count_flag="option_count" AND
						(
							bbse_commerce_goods_option.goods_option_item_count <="0" 
							OR bbse_commerce_goods_option.goods_option_item_soldout="soldout" 
						)
					)
				)
		GROUP BY bbse_commerce_goods.idx'));   // 노출품절 상품수
$total_nshop = count($wpdb->get_results("SELECT idx FROM bbse_commerce_goods WHERE idx<>'' AND goods_naver_shop='on'"));           // 네이버지식쇼핑 상품수
$total_npay = count($wpdb->get_results("SELECT idx FROM bbse_commerce_goods WHERE idx<>'' AND goods_naver_pay='on'"));           // 네이버페이 상품수
$total_copy = count($wpdb->get_results("SELECT idx FROM bbse_commerce_goods WHERE idx<>'' AND goods_display='copy'"));           // 복사 상품수
$total_trash = count($wpdb->get_results("SELECT idx FROM bbse_commerce_goods WHERE idx<>'' AND goods_display='trash'"));           // 휴지통 상품수

$cs_result = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class WHERE no>'2' AND use_sale='Y' ORDER BY no DESC");
/* Query String */
$add_args = array("page"=>$page, "s_list"=>$s_list, "per_page"=>$per_page, "s_keyword"=>$s_keyword, "s_period_1"=>$s_period_1, "s_period_2"=>$s_period_2, "s_icon"=>$s_icon, "s_category"=>$s_category);
?>
	<div style="margin-bottom:30px;">
		<h2>상품관리
			<button type="button" class="button-fill blue" onClick="go_add_goods();" style="margin-left:50px;"> 상품등록 </button>
		</h2>
		<hr>
		<ul class='title-sub-desc'>
			<li <?php echo ($s_list=='all')?"class=\"current\"":"";?>><a title='전체 상품 보기' href="javascript:view_list('all');">전체(<?php echo $total;?>)</a></li>
			<li <?php echo ($s_list=='display')?"class=\"current\"":"";?>><a title='노출 상품 보기' href="javascript:view_list('display');">노출(<?php echo $total_display;?>)</a></li>
			<li <?php echo ($s_list=='hidden')?"class=\"current\"":"";?>><a title='비노출 상품 보기' href="javascript:view_list('hidden');">비노출(<?php echo $total_hidden;?>)</a></li>
			<li <?php echo ($s_list=='soldout')?"class=\"current\"":"";?>><a title='노출품절 상품 보기' href="javascript:view_list('soldout');">노출품절(<?php echo $total_soldout;?>)</a></li>
			<li <?php echo ($s_list=='nshop')?"class=\"current\"":"";?>><a title='네이버지식쇼핑 상품 보기' href="javascript:view_list('nshop');">네이버지식쇼핑(<?php echo $total_nshop;?>)</a></li>
			<li <?php echo ($s_list=='npay')?"class=\"current\"":"";?>><a title='네이버페이 상품 보기' href="javascript:view_list('npay');">네이버페이(<?php echo $total_npay;?>)</a></li>
			<li <?php echo ($s_list=='copy')?"class=\"current\"":"";?>><a title='복사 상품 보기' href="javascript:view_list('copy');">복사(<?php echo $total_copy;?>)</a></li>
			<li <?php echo ($s_list=='trash')?"class=\"current\"":"";?>><a title='휴지통 상품 보기' href="javascript:view_list('trash');">휴지통(<?php echo $total_trash;?>)</a>
			<?php if($total_trash>0){?>&nbsp;&nbsp;<button type="button" class="button-small-fill orange" onClick="change_status('chStatus','empty-trash','empty');" align="absmiddle"> 비우기 </button><?php }?></li>
		</ul>
	</div>

	<div class="clearfix"></div>

	<div style="margin-top:60px;">
		<ul class='title-sub-desc none-content'>
			<li>
				<select name='bulk_action' id='bulk_action'>
					<option value=''>일괄 작업</option>
			<?php if($s_list=='trash'){?>
					<option value='display'>노출 상태로 복원</option>
					<option value='hidden'>비노출 상태로 복원</option>
					<option value='soldout'>노출품절 상태로 복원</option>
					<option value='empty-trash'>영구적으로 삭제하기</option>
			<?php }else{?>
					<option value='display'>노출</option>
					<option value='hidden'>비노출</option>
					<option value='soldout'>노출품절</option>
					<option value='trash'>휴지통</option>
				<?php if($nvrData['naver_shop_use']=='on'){?>
					<option value='nshopin'>네이버 지식쇼핑 추가</option>
					<option value='nshopout'>네이버 지식쇼핑 제외</option>
				<?php }?>
				<?php if($nvrPayData['naver_pay_use']=='on'){?>
					<option value='npayin'>네이버페이 추가</option>
					<option value='npayout'>네이버페이 제외</option>
				<?php }?>
			<?php }?>
				</select>
				<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_status('chStatus',jQuery('#bulk_action').val(),'');" value="적용"  />
			</li>
			<li>
				<select name='s_icon' id='s_icon'>
					<option <?php echo (!$s_icon)?"selected='selected'":"";?> value='' selected='selected'>아이콘 표시</option>
					<option <?php echo ($s_icon=='none')?"selected='selected'":"";?> value='none' class="hide-if-no-js">아이콘 없음</option>
					<option <?php echo ($s_icon=='new')?"selected='selected'":"";?> value='new' class="hide-if-no-js">신상품 아이콘</option>
					<option <?php echo ($s_icon=='best')?"selected='selected'":"";?> value='best'>베스트상품 아이콘</option>
					<option <?php echo ($s_icon=='newnbest')?"selected='selected'":"";?> value='newnbest'>신상품 + 베스트상품 아이콘</option>
					<option <?php echo ($s_icon=='nshop')?"selected='selected'":"";?> value='nshop'>네이버 지식쇼핑</option>
					<option <?php echo ($s_icon=='npay')?"selected='selected'":"";?> value='npay'>네이버페이</option>
				</select>
			</li>
			<li><input type="text" name="s_period_1" id="s_period_1" class="datepicker" value="<?php echo $s_period_1;?>" style="height: 28px;margin: 0 4px 0 0;width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_1').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;<input type="text" name="s_period_2" id="s_period_2" value="<?php echo $s_period_2;?>" class="datepicker" style="width:100px;cursor:pointer;background:#ffffff;text-align:center;" readonly />&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon-calendar.png" onClick="jQuery('#s_period_2').focus();" style="width:20px;height:20px;cursor:pointer;" align="absmiddle" />		
			</li>
			<li>
				<select name='s_category' id='s_category'>
					<option value=''>모든 카테고리 보기</option>
				<?php
					$cat_query = $wpdb->get_results("SELECT * FROM `bbse_commerce_category` WHERE `idx`>'1' ORDER BY `c_rank` ASC", ARRAY_A);
					foreach($cat_query as $c_row){
						if($c_row['depth_3']>'0'){
							if($s_category==$c_row['idx']) $cSelected=" selected='selected'";
							else $cSelected="";
							echo "<option value='".$c_row['idx']."'".$cSelected.">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- ".$c_row['c_name']."</option>";
						}
						else if($c_row['depth_2']>'0'){
							if($s_category==$c_row['idx']) $cSelected=" selected='selected'";
							else $cSelected="";
							echo "<option value='".$c_row['idx']."'".$cSelected.">&nbsp;&nbsp;- ".$c_row['c_name']."</option>";
						}
						else if($c_row['depth_1']>'0'){
							if($s_category==$c_row['idx']) $cSelected=" selected='selected'";
							else $cSelected="";
							echo "<option value='".$c_row['idx']."'".$cSelected.">".$c_row['c_name']."</option>";
						}
					}
				?>
				</select>
			</li>
			<li>
				<input type="text" name="s_keyword" id="s_keyword" value="<?php echo $s_keyword;?>" />
				<input type="submit" name="search-query-submit" id="search-query-submit" onClick="search_submit();" class="button apply" value="검색"  />
				<a href="<?php echo plugin_dir_url( __FILE__ ); ?>excel-product.php" target="_blank" class="button" id="product_excel_download">엑셀다운로드</a>
			</li>
		</ul>
		<ul class='title-sub-desc none-content' style="float:right;">
			<li>
				<select name='per_page' id='per_page' onChange="search_submit();">
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
		<input type="hidden" name="tMode" id="tMode" value="">
		<div style="width:100%;">
			<table class="dataTbls normal-line-height collapse">
				<colgroup><col width="4%"><col width="4%"><col width="9%"><col width="8%"><col width="25%"><col width="27%"><col width="5%"><col width="5%"><col width="5%"></colgroup>
				<tr>
					<th><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
					<th>번호</th>
					<th>상태</th>
					<th>이미지</th>
					<th>상품명</th>
					<th>소비자가/판매가</th>
					<th>카테고리</th>
					<th>재고수량</th>
					<th>등록일</th>
				</tr>
	<?php 
	if($s_total>'0'){
		
		$csTable=$wpdb->get_var("SHOW TABLES LIKE 'bbse_commerce_membership_class'");

		if($csTable=='bbse_commerce_membership_class'){
			$csCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_membership_class WHERE no>'2' AND use_sale='Y'"); // 회원레벨 사용여부 체크
		}

		foreach($result as $i=>$data) {
			$num = ($s_total-$start_pos) - $i; //번호
			switch ($data->goods_display){
				case "display":
					$gStatus="<button type=\"button\" class=\"button-small-fill green default-cursor\">&nbsp;&nbsp;노출&nbsp;&nbsp;</button>";
				break;
				case "hidden":
					$gStatus="<button type=\"button\" class=\"button-small-fill red default-cursor\"> 비노출 </button>";
				break;
				case "soldout":
					$gStatus="<button type=\"button\" class=\"button-small-fill green default-cursor\">&nbsp;&nbsp;노출&nbsp;&nbsp;</button><br><img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_soldout.png' />";
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

			$gDisplay="<br/>";
			if($data->goods_icon_new=='view') $gDisplay .="<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_new.png' />";
			if($gDisplay!="<br/>") $gDisplay .="&nbsp;";;
			if($data->goods_icon_best=='view') $gDisplay .="<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_best.png' />";

			if($gDisplay!="<br/>") $gNaverShop="<br/>";
			else $gNaverShop="";
			if($nvrData['naver_shop_use']=='on' && $data->goods_naver_shop=='on') $gNaverShop .="<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/naver_logo.png' />";

			if($gNaverShop!="<br/>") $gNaverPay="<br/>";
			else $gNaverPay="";
			if($nvrPayData['naver_pay_use']=='on' && $data->goods_naver_pay=='on') $gNaverPay .="<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_npay.png' />";

			$salePrice=round((1-($data->goods_price/$data->goods_consumer_price))*100,1);
			if($salePrice>100) $salePrice .="% ↑";
			else $salePrice .="% ↓";

			$memPrice=unserialize($data->goods_member_price);

			$cateList=explode("|",$data->goods_cat_list);
			$cateData = $wpdb->get_row("SELECT c_name FROM bbse_commerce_category WHERE idx='".$cateList['1']."'");
			$gCountFlag="";

			switch ($data->goods_count_flag){
				case "unlimit":
					$gCountFlag="무제한";
				break;
				case "goods_count":
					if($data->goods_count<='0') $gCountFlag="<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_soldout_01.png' />";
					else $gCountFlag=number_format($data->goods_count);
				break;
				case "option_count":
					$gCountFlag="옵션별 재고<br><img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_detail_view.png' onClick=\"option_view('".$data->idx."','".$data->goods_name."');\" style='cursor:pointer;' />";
					$tOption=unserialize($data->goods_option_basic);
					$soldoutFlagCount=false;
					$soldoutFlagCheck=false;

					if($tOption['goods_option_1_count']>'0' || $tOption['goods_option_2_count']>'0'){
						$optResult = $wpdb->get_results("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$data->idx."' ORDER BY goods_option_item_rank ASC");
						foreach($optResult as $i=>$optData) {
							if(!$optData->goods_option_item_count || $optData->goods_option_item_count<='0'){
								$soldoutFlagCount=true;
							}

							if($optData->goods_option_item_soldout=='soldout'){
								$soldoutFlagCheck=true;
							}

							if($soldoutFlagCount && $soldoutFlagCheck) break;
						}

						if($soldoutFlagCount) $gCountFlag .="<br><img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_soldout_02.png' />";
						if($soldoutFlagCheck) $gCountFlag .="<br><img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_soldout_03.png' />";
					}
				break;
				default:
					$gCountFlag="";
				break;
			}

			if($data->goods_basic_img) $basicImg = wp_get_attachment_image_src($data->goods_basic_img);
			else{
				$imageList=explode(",",$data->goods_add_img);
				if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
				else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
			}
	?>
				<tr>
					<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->idx;?>"></td>
					<td style="text-align:center;"><?php echo $num;?></td>
					<td style="text-align:center;"><?php echo $gStatus;?><?php echo $gDisplay;?><?php echo $gNaverShop;?><?php echo $gNaverPay;?></td>
					<td style="text-align:center;"><img src="<?php echo $basicImg['0'];?>" class="list-goods-img" /></td>
					<td>
						[상품코드:<?php echo $data->goods_code;?>]<br>
						<a href="admin.php?page=bbse_commerce_goods_add&tMode=modify&tData=<?php echo $data->idx;?>" target="_self" title="상품 편집"><?php echo $data->goods_name;?></a><br>
						<?php if($data->goods_option_basic){?>
							<button type="button" class="button-small-fill gray"> 옵션 </button><br>
						<?php }?>
						<div class="row-actions">

							<?php if($s_list=='trash'){?>
								<span class='edit'><a href="javascript:change_status('chStatus','display','<?php echo $data->idx;?>');" title='이 상품을 노출 상태로 복원'>노출 상태로 복원</a> | </span>
								<span class='edit'><a href="javascript:change_status('chStatus','hidden','<?php echo $data->idx;?>');" title='이 상품을 비노출 상태로 복원'>비노출 상태로 복원</a> | </span>
								<span class='edit'><a href="javascript:change_status('chStatus','soldout','<?php echo $data->idx;?>');" title='이 상품을 노출품절 상태로 복원'>노출품절 상태로 복원</a> | </span>
								<span class='trash'><a class='submitdelete' href="javascript:change_status('chStatus','empty-trash','<?php echo $data->idx;?>');" title='이 상품을 영구적으로 삭제'>영구적으로 삭제</a></span>
							<?php }else{?>
								<span class='edit'><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseGoods=<?php echo $data->idx;?>" target="_blank" title="이 상품 미리보기">미리보기</a> | </span>
								<span class='edit'><a href="admin.php?page=bbse_commerce_goods_add&tMode=modify&tData=<?php echo $data->idx;?>" target="_self" title="이 상품을 편집하기">편집</a> | </span>
								<span class='edit'><a href="javascript:change_status('chStatus','copy','<?php echo $data->idx;?>');" title="이 상품을 복사하기">복사</a> | </span>
								<span class='trash'><a class='submitdelete' href="javascript:change_status('chStatus','trash','<?php echo $data->idx;?>');" title='이 상품을 휴지통으로 이동'>휴지통</a></span>
							<?php }?>
						</div>
					</td>
					<td>
						<!--  <?php echo "￦".number_format($data->goods_consumer_price);?><br>
						<?php echo "￦".number_format($data->goods_price);?> (<?php echo $salePrice;?>)<br>-->
						<button type="button" class="button-small-fill gray default-cursor" title="적립금"> 적 </button><?php echo "￦".number_format($data->goods_earn);?><br>
						<!-- <?php 
                            if($csCnt>'0' && sizeof($memPrice['goods_member_price'])>'0'){
								if(!$memPrice['goods_member_price']['0']) $memPrice['goods_member_price']['0']=$data->goods_price;
						?>
							 <button type="button" class="button-small-fill orange default-cursor"> 특별회원가 </button><br>
					        <?php echo "￦".number_format($memPrice['goods_member_price']['0']);?> -->
							<?php
    							for($z=0;$z<sizeof($memPrice['goods_member_level']);$z++){
    							    for($i=0; $i<sizeof($cs_result);$i++){
    							        if($cs_result[$i]->no == $memPrice['goods_member_level'][$z]){
    							            if($memPrice['goods_member_price'][$z] != '0'){
    							                $userClassName=$cs_result[$i]->class_name;
            							        $tMemberPrice=$memPrice['goods_member_price'][$z];
            							        $tCumsumerPrice=$memPrice['goods_consumer_price'][$z];
            							        $tVat=$memPrice['goods_vat'][$z];
                            ?>
    							
    							<button type="button" class="button-small-fill orange default-cursor"> <?php echo $userClassName;?> </button>
    							소비자가:<?php echo "￦".number_format($tCumsumerPrice);?>
    							판매가격:<?php echo "￦".number_format($tMemberPrice);?>
    							부가세:<?php echo "￦".number_format($tVat);?>
    							<br>
    							<?php             }
    							        }
    							    }
    							?>
    						<?php  }
                            }
                        ?>
							            
							        
					</td>
					<td style="text-align:center;"><?php echo $cateData->c_name;?></td>
					<td style="text-align:center;"><?php echo $gCountFlag;?></td>
					<td style="text-align:center;"><?php echo date("Y.m.d",$data->goods_reg_date);?></td>
				</tr>
	<?php
		}
	}
	else{
	?>
				<tr>
					<td style="height:130px;text-align:center;" colspan="9">등록 된 상품이 존재하지 않습니다.</td>
				</tr>
	<?php 
	}
	?>
			</table>
		</div>

		<div style="margin-top:20px;">
			<ul class='title-sub-desc none-content'>
				<li>
					<select name='bulk_action2' id='bulk_action2'>
						<option value=''>일괄 작업</option>
				<?php if($s_list=='trash'){?>
						<option value='display'>노출 상태로 복원</option>
						<option value='hidden'>비노출 상태로 복원</option>
						<option value='soldout'>노출품절 상태로 복원</option>
						<option value='empty-trash'>영구적으로 삭제하기</option>
				<?php }else{?>
						<option value='display'>노출</option>
						<option value='hidden'>비노출</option>
						<option value='soldout'>노출품절</option>
						<option value='trash'>휴지통</option>
					<?php if($nvrData['naver_shop_use']=='on'){?>
						<option value='nshopin'>네이버 지식쇼핑 추가</option>
						<option value='nshopout'>네이버 지식쇼핑 제외</option>
					<?php }?>
					<?php if($nvrPayData['naver_pay_use']=='on'){?>
						<option value='npayin'>네이버페이 추가</option>
						<option value='npayout'>네이버페이 제외</option>
					<?php }?>
				<?php }?>
					</select>
					<input type="button" name="doaction" id="doaction" class="button apply" onClick="change_status('chStatus',jQuery('#bulk_action2').val(),'');" value="적용"  />
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
</div>

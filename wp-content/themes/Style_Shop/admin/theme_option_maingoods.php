<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_maingoods_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';

$dType=$_REQUEST['dType'];

$dTypeArray=array("recommend","best","md","new","today","hot");
$dTypeNameArray=array("추천 상품","베스트 상품","MD 기획 상품","신상품","오늘만 특가","핫아이템");
$useTypeArray=array("goodsplace_use_1","goodsplace_use_2","goodsplace_use_3","goodsplace_use_4","layout_left_today_sale","layout_left_hot_item");

if(!$dType){
	for($k=0;$k<6;$k++){
		if(get_option($theme_shortname."_".$useTypeArray[$k])=='Y'){
			$dType=$dTypeArray[$k];
			break;
		}
	}
}
?>

	<!-- contents 시작 -->
	<div id="content">
		<div class="tit">메인화면 설정 - 메인상품 진열 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_maingoods_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">진열대 현황</div>
		<div class="item">
<!-- ******************************************************************************************************************************************************* -->
<?php
if(!plugin_active_check('BBSe_Commerce')) {
?>
		<div class="borderBox">
			<div class="prd-desc">* BBS e-Commerce 플러그인이 설치되지 않았거나 비활성화 상태입니다.</div>
			<div class="prd-desc">* BBS e-Commerce 플러그인을 설치 및 활성화 상태로 설정해 주세요. </div>
			<div class="clearfix-display" style="height:10px;"></div>
		</div>
<?php
}
elseif(!$dType){
?>
		<div class="borderBox">
			<div class="prd-desc">* 추천 상품, 베스트 상품, MD 기획 상품, 신상품, 오늘만 특가, 핫아이템이 모두 '사용안함' 상태 입니다.</div>
			<div class="prd-desc">* 메인상품 진열을 위해서는 하나이상 '사용함' 상태로 설정해 주세요.</div>
			<div class="clearfix-display" style="height:10px;"></div>
		</div>
<?php
}
else{
	$total=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_display WHERE display_type='".$dType."'");
	if($total>0){
		$data=$wpdb->get_row("SELECT * FROM bbse_commerce_display WHERE display_type='".$dType."'");
	}

	switch($dType){
		case "recommend":
			$dType_msg="* 추천상품은 4개이상 등록을 권장합니다.";
		break;
		case "best":
			$dType_msg="* 베스트상품은 3개이상 등록을 권장합니다.";
		break;
		case "md":
			$dType_msg="* MD기획상품은 각 카테고리당 4개이상 등록을 권장합니다. (각 카테고리당 최대 12개 설정가능)";
		break;
		case "new":
			$dType_msg="* 산상품은 5개이상 등록을 권장합니다.";
		break;
		case "today":
			$dType_msg="* 오늘만특가는 1개이상 등록을 권장합니다.";
		break;
		case "hot":
			$dType_msg="* 핫아이템은 1개이상 등록을 권장합니다.";
		break;
		default:
			$dType_msg="* 추천상품은 4개이상 등록을 권장합니다.";
		break;
	}

	if($data->display_goods){
		$dsplList=unserialize($data->display_goods);
		if($dType=='md' && $dsplList['display_md_cnt']>'0') $dspCnt=$dsplList['display_md_cnt'];
		else $dspCnt='1';
	}
	else $dspCnt='1';
?>
		<div class="borderBox">
		  * 최신 배치 등록순으로 순차적으로 적용됩니다.<br />
		  * 테마환경설정>메인상품설정>상품배치설정에서 설정한 내용에 제한적으로 적용됩니다. (사용여부 및 표시 개수 등.)<br />
		</div>

		<div class="tabWrap">
		  <ul class="tabList">
		<?php 
		for($k=0;$k<6;$k++){
			if(get_option($theme_shortname."_".$useTypeArray[$k])=='Y'){
				if($dType==$dTypeArray[$k]) $activeClass="active";
				else $activeClass="";

				echo "<li class=\"".$activeClass."\"><a href=\"themes.php?page=functions.php&optTtpe=maingoods&dType=".$dTypeArray[$k]."\">".$dTypeNameArray[$k]."</a></li>";
			}
		}
		?>
		  </ul>
		</div>

		<div class="clearfix-display" style="height:20px;"></div>

		<div class="borderBox-info">
			<table class="dataNormalTbls">
				<colgroup><col width="150px;"><col width=""></colgroup>
				<tr>
					<td>페이지링크주소안내</td>
					<td>
						* 테마환경설정 : 메인화면설정>상품배치 설정 더보기 링크에 입력하여 등록하시면 됩니다.<br />
						&nbsp;&nbsp;- 추천상품 페이지 [<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=recommend]&nbsp;<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=recommend" target="_blank"><img src="<?php echo bloginfo('template_url')?>/admin/images/left_icon4.png" align="absmiddle" width="13" height="13" alt="추천상품 미리보기" title="추천상품 미리보기"></a><br />
						&nbsp;&nbsp;- 베스트상품 페이지 [<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=best]&nbsp;<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=best" target="_blank"><img src="<?php echo bloginfo('template_url')?>/admin/images/left_icon4.png" align="absmiddle" width="13" height="13" alt="베스트상품 미리보기" title="베스트상품 미리보기"></a><br />
						&nbsp;&nbsp;- MD 기획 상품 페이지 [<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=md]&nbsp;<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=md" target="_blank"><img src="<?php echo bloginfo('template_url')?>/admin/images/left_icon4.png" align="absmiddle" width="13" height="13" alt="MD 기획 상품 미리보기" title="MD 기획 상품 미리보기"></a><br />
						&nbsp;&nbsp;- 신상품 페이지 [<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=new]&nbsp;<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=new" target="_blank"><img src="<?php echo bloginfo('template_url')?>/admin/images/left_icon4.png" align="absmiddle" width="13" height="13" alt="신상품 미리보기" title="신상품 미리보기"></a><br />
						&nbsp;&nbsp;- 오늘만 특가 페이지 [<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=today]&nbsp;<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=today" target="_blank"><img src="<?php echo bloginfo('template_url')?>/admin/images/left_icon4.png" align="absmiddle" width="13" height="13" alt="오늘만 특가 미리보기" title="오늘만 특가 미리보기"></a><br />
						&nbsp;&nbsp;- 핫아이템 페이지 [<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=hot]&nbsp;<a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseCat=hot" target="_blank"><img src="<?php echo bloginfo('template_url')?>/admin/images/left_icon4.png" align="absmiddle" width="13" height="13" alt="핫아이템 미리보기" title="핫아이템 미리보기"></a><br />
					</td>
				</tr>
			</table>
		</div>
	<?php if($dType=='md'){?>
		 <div class="clearfix-display"></div>
		<div>
			<div style="margin-top:40px;float:left;">
				<button type="button" class="button-bbse blue" onClick="display_md_type_add();" style="width:190px;"> MD 기획상품 분류 추가 </button>
			</div>
			<div id='md-remove-all' style="margin-top:40px;float:left;margin-left:20px;<?php echo (!$data->display_goods || $dsplList['display_md_cnt']<='0')?"display:none;":"";?>">
				<button type="button" class="button-bbse red" onClick="md_remove_all();" style="width:130px;"> 전체 분류 삭제 </button>
			</div>
			 <div class="clearfix-display" style="height:80px;"></div>
			<div class="prd-desc"><?php echo $dType_msg;?></div>
		</div>
		 <div class="clearfix-display"></div>

		<div class="clearfix-display" style="height:40px;"></div>

		<form name="goodsFrm" id="goodsFrm">
		<input type="hidden" name="dType" id="dType" value="<?php echo $dType;?>" />
		<input type="hidden" name="display_md_cnt" id="display_md_cnt" value="<?php echo (!$dsplList['display_md_cnt'])?"0":$dsplList['display_md_cnt'];?>" />
		<input type='hidden' name='<?php echo $theme_shortname?>_maingoods_preOpen' id='<?php echo $theme_shortname?>_maingoods_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div id="display-md-list-total">
		<?php
		if($data->display_goods && $dsplList['display_md_cnt']>'0'){
			for($m=1;$m<=$dsplList['display_md_cnt'];$m++){
		?>
			<div id="md-list_<?php echo $m;?>">
				<div>
					<div style="float:left;">
						<span  class="titleH5"><span id="md_title_<?php echo $m;?>"><?php echo $m;?>.</span> 분류명 : </span><input type="text" name="display_md_title_<?php echo $m;?>" id="display_md_title_<?php echo $m;?>" value="<?php echo $dsplList['display_md_title_'.$m];?>" style="width:250px;" />
					</div>
					<div style="float:left;margin-left:10px;">
						<span id="md-btn-delete-<?php echo $m;?>" <?php echo ($dsplList['display_md_cnt']=='1')?"style='display:none;'":"";?>><button type="button"class="button-small red" onClick="goods_list_md_remove('<?php echo $m;?>');" style="height:27px;">분류삭제</button></span>
					</div>
					<div style="float:right;margin-right:10px;">
						<span id="md-btn-add-<?php echo $m;?>"><button type="button"class="button-small green" onClick="goods_list_popup('md','<?php echo $m;?>');" style="height:30px;">진열상품추가</button></span>
					</div>
				</div>
				<div class="clearfix-display"></div>
				<div class="clearfix-display" style="height:40px;"></div>
				<div class="borderBox-gray">
					<div class="goods-gallery-display">
						<ul id="goods-md-ul-list_<?php echo $m;?>">
					<?php
					if(sizeof($dsplList['goods_md_list_'.$m])){
						for($t=0;$t<sizeof($dsplList['goods_md_list_'.$m]);$t++){
							$tData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$dsplList['goods_md_list_'.$m][$t]."'");
							if($tData->goods_name){
								if($tData->goods_basic_img) $basicImg = wp_get_attachment_image_src($tData->goods_basic_img);
								else{
									$imageList=explode(",",$tData->goods_add_img);
									if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
									else $basicImg['0']=bloginfo('template_url')."/admin/images/image_not_exist.jpg";
								}
					?>
							<li id="goods_img_<?php echo $dType;?>_list_<?php echo $m;?>_<?php echo $tData->idx;?>" title="<?php echo $tData->goods_name;?>">
								<table style="margin-left:14px;padding:0px;">
									<tr>
										<td style="border-bottom:0px;padding:0px;">
											<div class="thumb">
												<img src="<?php echo $basicImg['0'];?>" alt="상품이미지" />
											</div>
										</td>
										<td style="border-bottom:0px;padding:0px;">
											<span id="popup-li-list_<?php echo $m;?>-<?php echo $tData->idx;?>"><img src="<?php echo bloginfo('template_url')?>/admin/images/btn_delete.png" onClick="goods_img_link_remove('<?php echo $dType;?>','<?php echo $tData->idx;?>','<?php echo $m;?>')" class="deleteBtn" alt="상품 삭제" title="상품 삭제" /></span>
										</td>
									</tr>
								</table>
								<div class="goodsname"><?php echo $tData->goods_name;?>
								<input type="hidden" name="goods_<?php echo $dType;?>_list_<?php echo $m;?>[]" value="<?php echo $tData->idx;?>" />
								<input type="hidden" name="goods_<?php echo $dType;?>_uc_list[]" value="<?php echo $tData['goods_uc_list'][$m];?>" />
								</div>
							</li>
					<?php
							}
						}
					}
					?>
						</ul>
					</div>
				</div>
				<div class="clearfix-display"></div>
			</div>
		<?php
			}
		}
		?>
		</div>
		</form>
	<?php }else{?>
		<div class="prd-desc"><?php echo $dType_msg;?></div>
		<div style="float:right;margin-right:10px;">
			<button type="button"class="button-small green" onClick="goods_list_popup('<?php echo $dType;?>','');" style="height:30px;">진열상품추가</button>
		</div>
		 <div class="clearfix-display"></div>

		<div class="clearfix-display" style="height:40px;"></div>
		
		<form name="goodsFrm" id="goodsFrm">
		<input type="hidden" name="dType" id="dType" value="<?php echo $dType;?>" />
		<input type='hidden' name='<?php echo $theme_shortname?>_maingoods_preOpen' id='<?php echo $theme_shortname?>_maingoods_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />

		<div class="borderBox-gray">
			<div class="goods-gallery-display">
				<ul id="goods-<?php echo $dType;?>-ul-list_1">
			<?php
			if($data->display_goods){
			    $ucList = $dsplList['goods_uc_list'];
			    for($i=0;$i<sizeof($ucList);$i++){
			        ?>
							<input type="hidden" name="goods_<?php echo $dType;?>_uc_list[]" value="<?php echo $ucList[$i];?>" />
						<?php
						}
						?>
						
			    <?php
				for($t=0;$t<sizeof($dsplList['goods_type_list']);$t++){
					$tData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$dsplList['goods_type_list'][$t]."'");
					if($tData->goods_name){
						if($tData->goods_basic_img) $basicImg = wp_get_attachment_image_src($tData->goods_basic_img);
						else{
							$imageList=explode(",",$tData->goods_add_img);
							if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
							else $basicImg['0']=bloginfo('template_url')."/admin/images/image_not_exist.jpg";
						}
			?>
					<li id="goods_img_<?php echo $dType;?>_list_<?php echo $tData->idx;?>" title="<?php echo $tData->goods_name;?>">
						<table style="margin-left:14px;padding:0px;">
							<tr>
								<td style="border-bottom:0px;padding:0px;">
									<div class="thumb">
										<img src="<?php echo $basicImg['0'];?>" alt="상품이미지" />
									</div>
								</td>
								<td style="border-bottom:0px;padding:0px;">
									<span id="popup-li-list-<?php echo $tData->idx;?>"><img src="<?php echo bloginfo('template_url')?>/admin/images/btn_delete.png" onClick="goods_img_link_remove('<?php echo $dType;?>','<?php echo $tData->idx;?>','')" class="deleteBtn" alt="상품 삭제" title="상품 삭제" /></span>
								</td>
							</tr>
						</table>
						
						<div class="goodsname"><?php echo $tData->goods_name;?>
						<input type="hidden" name="goods_<?php echo $dType;?>_list[]" value="<?php echo $tData->idx;?>" />
						</div>
					</li>
			<?php
					}
				}
			}
			?>
				</ul>
			</div>
		</div>
	<?php }?>

		</div>
      </li>
	</ul>
</div>

<div class="clearfix-display" style="height:20px;"></div>
<div id="btn_display_save" style="text-align:center;display:<?php echo ($dType=='md'&&$dsplList['display_md_cnt']<='0')?"none":"block";?>;">
	<button type="button" class="button-bbse blue" onClick="display_submit('<?php echo $dType;?>');" style="width:150px;"> 등록/저장 </button>
</div>

<div class="clearfix-display" style="height:40px;"></div>

<?php
}
?>
	</div>
<script language="javascript">
	jQuery(function () {
		var dType="<?php echo $dType;?>";
		var dspCnt="<?php echo $dspCnt;?>";

		for(i=1;i<=dspCnt;i++){
			jQuery("#goods-"+dType+"-ul-list_"+i).sortable({
				start: function (event, ui) {
						ui.item.toggleClass("move-highlight");
				},
				stop: function (event, ui) {
						ui.item.toggleClass("move-highlight");
				}
			});
			jQuery("#goods-"+dType+"-ul-list_"+i).disableSelection();
		}
	});

	// 상품 이미지 삭제 시
	function goods_img_link_remove(pTarget,tId,tNo){
		if(tNo && tNo>0) var tNoStr="_"+tNo;
		else var tNoStr="";

		jQuery("#goods_img_"+pTarget+"_list"+tNoStr+"_"+tId).remove();
	}

	// 상품 등록 팝업
	function goods_list_popup(pTarget,tNo){
		var popupTitle="";
		var chkList="";
		var ucList="";

		if(tNo && tNo>0) var tNoStr="_"+tNo;
		else var tNoStr="";

		var tCnt=jQuery("input[name=goods_"+pTarget+"_list"+tNoStr+"\\[\\]]").size();
		var tUcCnt=jQuery("input[name=goods_"+pTarget+"_uc_list"+tNoStr+"\\[\\]]").size();

		if(pTarget=='recommend') popupTitle="추천상품";
		else if(pTarget=='best') popupTitle="베스트상품";
		else if(pTarget=='md') popupTitle="MD기획상품";
		else if(pTarget=='new') popupTitle="신상품";
		else if(pTarget=='today') popupTitle="오틀만특가";
		else if(pTarget=='hot') popupTitle="핫아이템";

		if(pTarget=="md" && tCnt>=12){
			alert(popupTitle+"은 분류별 최대12개까지만 등록이 가능합니다.   ") ;
			return;
		}

		for(i=0;i<tCnt;i++){
			if(chkList) chkList +=",";
			chkList +=jQuery("input[name=goods_"+pTarget+"_list"+tNoStr+"\\[\\]]").eq(i).val();
		}
		
		for(let i=0;i<tUcCnt;i++){
			if(ucList) ucList +=",,";
			ucList +=jQuery("input[name=goods_"+pTarget+"_uc_list"+tNoStr+"\\[\\]]").eq(i).val();
		}
		
		var tbHeight = window.innerHeight * .85;
		var tbWidth = window.innerWidth * .50;
		tb_show("상품목록 ("+popupTitle+")", "<?php echo bloginfo('template_url')?>/admin/theme_option_maingoods-popup-goods-list.php?pTarget="+pTarget+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;tNo="+tNo+"&#38;chkList="+chkList+"&#38;ucList="+ucList+"&#38;TB_iframe=true");
		//tb_show("상품목록 ("+popupTitle+")", "http://localhost/wp-content/themes/Style_Shop/admin/theme_option_maingoods-popup-goods-list.php?pTarget="+pTarget+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;tNo="+tNo+"&#38;chkList="+chkList+"&#38;ucList="+ucList+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	// 상품 등록 팝업 닫기
	function remove_popup(){
		tb_remove();
	}

	function display_submit(dType){
	
		if(dType=='md'){
			var mdCnt=jQuery("#display_md_cnt").val();

			for(k=1;k<=mdCnt;k++){
				var listSize=jQuery("input[name=goods_md_list_"+k+"\\[\\]]").size();
				var typeName=jQuery("#display_md_title_"+k).val();

				if(!typeName){
					alert('분류 '+k+'의  분류명 을 입력해 주세요.        ');
					jQuery("#display_md_title_"+k).focus();
					return;
				}
				if(listSize<=0){
					alert('분류 '+k+'의 진열상품을 등록해 주세요.        ');
					return;
				}
			}
		}

		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: '<?php echo bloginfo('template_url')?>/admin/theme_option_maingoods.exec.php', 
			//url: 'http://localhost/wp-content/themes/Style_Shop/admin/theme_option_maingoods.exec.php',	
			data: jQuery("#goodsFrm").serialize(), 
			success: function(data){
				//alert(data);
				var result = data; 
				if(result == "success"){
				
					alert("상품진열 등록을 정상적으로 완료하였습니다.    ");
					window.location.href="themes.php?page=functions.php&optTtpe=maingoods&dType="+dType;
				}
				else if(result == "dbError"){
					alert("[Error] DB 오류 입니다.   ");
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

	function display_md_type_add(){
		var tmpCnt=jQuery("#display_md_cnt").val();
		var display_md_cnt="<?php echo $dsplList['display_md_cnt'];?>";
		cnt=parseInt(tmpCnt)+1;

		if(cnt>7){
			alert('분류 추가는 최대 7개까지 가능합니다.   ');
			return;
		}

		var str="";
		str +="<div id=\"md-list_"+cnt+"\">"
				+  " <div>"
				+  " <div style=\"float:left;\">"
				+  "    <span  class=\"titleH5\"><span id=\"md_title_"+cnt+"\">"+cnt+".</span> 분류명 : </span><input type=\"text\" name=\"display_md_title_"+cnt+"\" id=\"display_md_title_"+cnt+"\" value=\"\" style=\"width:250px;\" />"
				+  "  </div>"
				+  "  <div style=\"float:left;margin-left:10px;\">"
				+  "    <span id=\"md-btn-delete-"+cnt+"\"><button type=\"button\"class=\"button-small red\" onClick=\"goods_list_md_remove('"+cnt+"');\" style=\"height:27px;\">분류삭제</button></span>"
				+  "  </div>"
				+  "  <div style=\"float:right;margin-right:10px;\">"
				+  "    <span id=\"md-btn-add-"+cnt+"\"><button type=\"button\"class=\"button-small green\" onClick=\"goods_list_popup('md','"+cnt+"');\" style=\"height:30px;\">진열상품추가</button></span>"
				+  " </div>"
				+  "</div>"
				+  "<div class=\"clearfix-display\"></div>"
				+  "<div class=\"clearfix-display\" style=\"height:40px;\"></div>"
				+  "<div class=\"borderBox-gray\">"
				+  "  <div class=\"goods-gallery-display\">"
				+  "	   <ul id=\"goods-md-ul-list_"+cnt+"\">"
				+  "	   </ul>"
				+  "   </div>"
				+  "</div>"
				+  "<div class=\"clearfix-display\"></div>"
				+"</div>";

		jQuery("#display-md-list-total").append(str);
		jQuery("#display_md_cnt").val(cnt);
		init_md_move(cnt);
		if(cnt==1) jQuery("#btn_display_save").css("display","block");
		if(display_md_cnt>0 && cnt==2)  jQuery("#md-btn-delete-1").css("display","inline");

		if(jQuery("#md-remove-all").css("display")=='none') jQuery("#md-remove-all").css("display","block");
	}

	function init_md_move(cnt){
		jQuery("#goods-md-ul-list_"+cnt).sortable({
			start: function (event, ui) {
					ui.item.toggleClass("move-highlight");
			},
			stop: function (event, ui) {
					ui.item.toggleClass("move-highlight");
			}
		});
		jQuery("#goods-md-ul-list_"+cnt).disableSelection();
	}

	function goods_list_md_remove(tNo){
		var st=parseInt(tNo)+1;
		var tidx="";
		var tmpCnt=jQuery("#display_md_cnt").val();
		var display_md_cnt="<?php echo $dsplList['display_md_cnt'];?>";
		var liCnt=0;
		cnt=parseInt(tmpCnt)-1;

		if(!confirm('분류삭제를 하시면 해당 상품목록도 함께 삭제됩니다.      \n분류 '+tNo+'을 삭제하시겠습니까?   ')){
			return;
		}

		jQuery("#md-list_"+tNo).remove();

		for(j=st;j<8;j++){
			tidx=j-1;
			if(jQuery("#md-list_"+j).length > 0){
				jQuery("#md-list_"+j).attr("id","md-list_"+tidx);
				jQuery("#md_title_"+j).attr("id","md_title_"+tidx);
				jQuery("#md_title_"+tidx).html(tidx+".");
				jQuery("input[name=display_md_title_"+j+"]").attr("name","display_md_title_"+tidx);
				jQuery("#display_md_title_"+j).attr("id","display_md_title_"+tidx);
				jQuery("#md-btn-delete-"+j).attr("id","md-btn-delete-"+tidx);
				jQuery("#md-btn-delete-"+tidx).html("<button type=\"button\"class=\"button-small red\" onClick=\"goods_list_md_remove('"+tidx+"');\" style=\"height:27px;\">분류삭제</button>");
				jQuery("#md-btn-add-"+j).attr("id","md-btn-add-"+tidx);
				jQuery("#md-btn-add-"+tidx).html("<button type=\"button\"class=\"button-small green\" onClick=\"goods_list_popup('md','"+tidx+"');\" style=\"height:30px;\">진열상품추가</button>");
				jQuery("#goods-md-ul-list_"+j).attr("id","goods-md-ul-list_"+tidx);

				var liCnt=jQuery("input[name=goods_md_list_"+j+"\\[\\]]").length;
				for(k=0;k<liCnt;k++){
					var tVal=jQuery("input[name=goods_md_list_"+j+"\\[\\]]").eq(k).val();
					jQuery("#goods_img_md_list_"+j+"_"+tVal).attr("id","goods_img_md_list_"+tidx+"_"+tVal);
					jQuery("#popup-li-list_"+j+"-"+tVal).attr("id","popup-li-list_"+tidx+"-"+tVal);
					jQuery("#popup-li-list_"+tidx+"-"+tVal).html("<img src=\"<?php echo bloginfo('template_url')?>/admin/images/btn_delete.png\" onclick=\"goods_img_link_remove('md',"+tVal+",'"+tidx+"');\" class=\"deleteBtn\" alt=\"MD기획상품에서 삭제\" title=\"MD기획상품에서 삭제\">");
					jQuery("input[name=goods_md_list_"+j+"\\[\\]]").attr("name","goods_md_list_"+tidx+"[]");
				}
			}
		}

		jQuery("#display_md_cnt").val(cnt);
		if(cnt<=0) jQuery("#btn_display_save").css("display","none");

		if(display_md_cnt>0){
			if(cnt=='1') jQuery("#md-btn-delete-1").css("display","none");
			else jQuery("#md-btn-delete-1").css("display","inline");
		}
	}


	function md_remove_all(){
		var dType="<?php echo $dType;?>";

		if(confirm('전체 분류 삭제를 하시면 모든 상품목록도 함께 삭제됩니다.      \n전체 분류를 삭제하시겠습니까?   ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo bloginfo('template_url')?>/admin/theme_option_maingoods.exec.php', 
				data: {dType:dType,dRemove:"empty"}, 
				success: function(data){
					//alert(data);
					var result = data; 
					if(result == "success"){
						window.location.href="themes.php?page=functions.php&optTtpe=maingoods&dType="+dType;
					}
					else if(result == "dbError"){
						alert("[Error] DB 오류 입니다.   ");
					}
					else if(result!='success'){
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
	}
</script>
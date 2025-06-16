<?php

$tMode=$_REQUEST['tMode'];
$tData=$_REQUEST['tData'];

$gCountFlag=""; // 재고수량 체크 (modify)
if($tMode=='modify' && $tData){
	$total = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$tData."'");    // 총 상품수
	if($total>0){
		$data=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$tData."'");
		
		if($data->goods_count_flag=='option_count'){
			$tOption=unserialize($data->goods_option_basic);
			$cnt=sizeof($tOption['goods_option_title']);
			$soldoutFlag=false;

			for($i=0;$i<$cnt;$i++){
				if(!$tOption['goods_option_item_count'][$i] || $tOption['goods_option_item_count'][$i]<=0){
					$soldoutFlag=true;
					break;
				}
			}

			if($soldoutFlag) $gCountFlag .="<img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_none_display.png' align='absmiddle' /> <a href='#flag_goods_option_table'><img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_option_soldout.png' align='absmiddle' title='재고수량이 부족한 옵션이 존재합니다 !' /></a>";
		}
	}
	else{ 
		echo "<script language='javascript'>window.location.href ='admin.php?page=bbse_commerce_goods';</script>";
		exit;
	}
} 

$csTable=$wpdb->get_var("SHOW TABLES LIKE 'bbse_commerce_membership_class'");

$cpDisplay = $data->goods_cprice_display;
if($csTable=='bbse_commerce_membership_class'){
	$csCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_membership_class WHERE no>'2' AND use_sale='Y'"); // 회원레벨 사용여부 체크
}

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

$mclass_rlt = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class where use_sale <> 'N' ORDER BY no ASC;");
json_encode($mclass_rlt, JSON_FORCE_OBJECT);

$gcsTable=$wpdb->get_var("SHOW TABLES LIKE 'bbse_goods_user_grade_price'");

$goods_code =$data->goods_code;
if($gcsTable=='bbse_goods_user_grade_price'){
    //$gcsCnt = $wpdb->get_var("SELECT count(1) FROM bbse_goods_user_grade_price where goods_code = '$goods_code'");
    $gcsCnt = $wpdb->get_var("SELECT count(1) FROM bbse_commerce_goods where goods_code = '$goods_code'");
}

//$gdata = $wpdb->get_row("SELECT * FROM bbse_goods_user_grade_price where goods_code = '$goods_code'");
$gdata = $wpdb->get_row("SELECT * FROM bbse_commerce_goods where goods_code = '$goods_code'");

?>
<script type="text/javascript">


	//콘솔찍기 위해 넣음
	window.onload = function(){
		var str = <?php echo $depth1_code;?>
		console.log(str);
	}
	///콘솔찍기 위해 넣음
	
	// 숫자만 입력
	function check_number(){
		var key = event.keyCode;
		if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
			event.returnValue = false;
		}
	}

	// 카테고리 체크(시작)
	function list_check(cID){
		jQuery(".cat-list input[name=goods_cat_list\\[\\]][type=checkbox][value="+cID+"]").attr("checked",!jQuery(".cat-list input[name=goods_cat_list\\[\\]][type=checkbox][value="+cID+"]").attr('checked'));
	}

	// 추가항목 설정
	function goods_add_field(aType){
		var str="";
		var aCnt=jQuery("input[name=goods_add_field_title\\[\\]]").length;
		if(aType=='add'){
			nCnt=aCnt+1;
			if(nCnt<3){
				jQuery("#goods_add_msg_field").remove();
				str +="<div id=\"goods_add_"+nCnt+"_field\"><input type=\"text\" style=\"width:25%;color:#afafaf;\" name=\"goods_add_field_title[]\" id=\"goods_add_field_title[]\" onFocus=\"check_option_value(jQuery(this),'focus','항목명');\" onBlur=\"check_option_value(jQuery(this),'blur','항목명');\" value=\"\" placeholder=\"항목명\" />&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"text\" style=\"width:70%;color:#afafaf;\" name=\"goods_add_field_description[]\" id=\"goods_add_field_description[]\" onFocus=\"check_option_value(jQuery(this),'focus','내용');\" onBlur=\"check_option_value(jQuery(this),'blur','내용');\" value=\"\" placeholder=\"내용\" /></div>";

				jQuery("#goods_add_field").append(str);
			}
			else alert('추가항목은 최대 2개 만 설정이 가능합니다.    '); return;
		}
		else{
			if(aCnt>0){
				jQuery("#goods_add_"+aCnt+"_field").remove();
				if((aCnt-1)<=0) 	jQuery("#goods_add_field").append("<div id=\"goods_add_msg_field\" class=\"prd-desc\" style=\"margin-top:-7px;\">* 추가항목 설정을 원하시는 경우 + 버튼을 클릭해 주세요. (최대 2개 설정 가능)</div>");
			}
			else return;
		}
		
	}


	var createOn=true;

    // 옵션값 개수 선택 시 (상품 옵션 설정)
	function goods_option_item(gID,tID){
		var cnt=jQuery("#"+gID+"_count").val();
		var str="";
		var ist=jQuery("input[name="+gID+"_item\\[\\]]").length;

		var optCnt_1=jQuery("#"+tID+"_1_count").val();
		var optCnt_2=jQuery("#"+tID+"_2_count").val();

		if(cnt>0){
			if (jQuery("input[name="+gID+"_title]").length<=0){
				jQuery("#"+gID+"_title_label").html("<input type=\"text\" name=\""+gID+"_title\" id=\""+gID+"_title\" style=\"width:90%;\" />");
			}

			if(cnt<ist){
				for(i=ist;i>cnt;i--){
					jQuery("#"+gID+"_"+i+"_item").remove();
				}
			}
			else{
				for(i=ist;i<cnt;i++){

					if (jQuery("input[name="+gID+"_item\\[\\]]").length<=i){
						if(str) str +="<span id=\""+gID+"_"+(i+1)+"_item\"><br /><input type=\"text\" name=\""+gID+"_item[]\" id=\""+gID+"_item[]\" style=\"width:90%;\" onchange=\"create_btn_on(true);\" /></span>";
						else str +="<span id=\""+gID+"_"+(i+1)+"_item\"><input type=\"text\" name=\""+gID+"_item[]\" id=\""+gID+"_item[]\" style=\"width:90%;\" onchange=\"create_btn_on(true);\" /></span>";
					}
				}

				jQuery("#"+gID+"_item_label").append(str);
			}
		}
		else{
			jQuery("#"+gID+"_title_label").html("");
			jQuery("#"+gID+"_item_label").html("");

			if(optCnt_1<=0 && optCnt_2<=0){
				jQuery("#"+tID+"_table").html("");
				jQuery("#"+tID+"_table").css("display","none");
			}
		}

		create_btn_on(true); // 옵션 저장버튼 사용가능
	}

	// 옵션 저장 버튼 클릭 시 (상품 옵션설정)
	function create_goods_option(tID){
		var optCnt_1=jQuery("#"+tID+"_1_count").val();
		var optCnt_2=jQuery("#"+tID+"_2_count").val();
		var inpCnt_1=0;
		var inpCnt_2=0;
		var inpData_1="";
		var inpData_2="";
		var arraySTr="";
		var str="";
		var gFlag=jQuery("#goods_count_flag").val();
		var countStyle="";
		var countDisabled="";
		var countValue="";

		var checkedStyle="";
		var checkedDisabled="";

		if(gFlag!='option_count'){
			countDisabled="disabled";
			countStyle="background:#f4f4f4;color:#a2a2a2;";
			countValue="0";
			checkedDisabled="disabled";
			checkedStyle="background:#efefef;";
		}

		var rstArray = new Array();

		if(!createOn) return;

		if(optCnt_1<=0 && optCnt_2<=0){
			alert('상품 옵션의 옵션값 개수를 선택해 주세요.  ');
			return;
		}
		else if(optCnt_1>0 && !jQuery("#"+tID+"_1_title").val()){
			alert('상품 옵션의 첫번째 옵션명을 입력해 주세요.  ');
			jQuery("#"+tID+"_1_title").focus();
			return;
		}
		else if(optCnt_2>0 && !jQuery("#"+tID+"_2_title").val()){
			alert('상품 옵션의 두번째 옵션명을 입력해 주세요.  ');
			jQuery("#"+tID+"_2_title").focus();
			return;
		}

		if(optCnt_1>0){
			for(i=0;i<optCnt_1;i++){
				if(!jQuery("input[name="+tID+"_1_item\\[\\]]").eq(i).val() && inpCnt_1<=0) inpCnt_1=i+1;
			}

			if(inpCnt_1>0){
				alert('첫번째 상품 옵션의 옵션값을 모두 입력해 주세요.    ');
				jQuery("input[name="+tID+"_1_item\\[\\]]").eq(inpCnt_1-1).focus();
				return;
			}
		}

		if(optCnt_2>0){
			for(i=0;i<optCnt_2;i++){
				if(!jQuery("input[name="+tID+"_2_item\\[\\]]").eq(i).val() && inpCnt_2<=0) inpCnt_2=i+1;
			}

			if(inpCnt_2>0){
				alert('두번째 상품 옵션의 옵션값을 모두 입력해 주세요.    ');
				jQuery("input[name="+tID+"_2_item\\[\\]]").eq(inpCnt_2-1).focus();
				return;
			}
		}

		if(optCnt_1>0 && optCnt_2<=0){
			for(m=0;m<optCnt_1;m++){
				inpData_1=jQuery("input[name="+tID+"_1_item\\[\\]]").eq(m).val();
				rstArray.push(inpData_1);
			}
		}
		else if(optCnt_1<=0 && optCnt_2>0){
			for(n=0;n<optCnt_2;n++){
				inpData_2=jQuery("input[name="+tID+"_2_item\\[\\]]").eq(n).val();
				rstArray.push(inpData_2);
			}
		}
		else if(optCnt_1>0 && optCnt_2>0){
			for(m=0;m<optCnt_1;m++){
				inpData_1=jQuery("input[name="+tID+"_1_item\\[\\]]").eq(m).val();
				for(n=0;n<optCnt_2;n++){
					inpData_2=jQuery("input[name="+tID+"_2_item\\[\\]]").eq(n).val();
					rstArray.push(inpData_1+" / "+inpData_2);
				}
			}
		}

		if(rstArray.length>0){
			str +="<div class=\"clearfix\"></div>"
				+ "<div class=\"clearfix\" style=\"height:10px;\"></div>"
				+ "	<table id=\"goods_option_table_list\" class=\"dataTbls collapse\">"
				+ "		<colgroup><col width=\"34%\"><col width=\"15%\"><col width=\"15%\"><col width=\"20%\"><col width=\"8%\"><col width=\"8%\"></colgroup>"
				+ "		<tr><th>옵션값</th><th>추가가격</th><th>재고수량</th><th>고유번호</th><th>노출</th><th>품절</th></tr>";


			for(k=0;k<rstArray.length;k++){
				str += "		<tr>"
					+ "			<td style=\"text-align:center;\"><span>"+rstArray[k]+"</span><input type=\"hidden\" name=\""+tID+"_title[]\" id=\""+tID+"_title[]\" value=\""+rstArray[k]+"\" /></td>"
					+ "			<td style=\"text-align:center;\"><input type=\"text\" name=\""+tID+"_item_overprice[]\" id=\""+tID+"_item_overprice[]\" onkeydown=\"check_number();\" value=\"\" style=\"width:90%;color:#afafaf;text-align:center;ime-mode:disabled;\" onFocus=\"check_option_value(jQuery(this),'focus','추가가격');\" onBlur=\"check_option_value(jQuery(this),'blur','추가가격');\" placeholder=\"추가가격\" /></td>"
					+ "			<td style=\"text-align:center;\"><input type=\"text\" name=\""+tID+"_item_count[]\" id=\""+tID+"_item_count[]\" onkeydown=\"check_number();\" value=\""+countValue+"\" style=\""+countStyle+"width:90%;color:#afafaf;text-align:center;ime-mode:disabled;\" onFocus=\"check_option_value(jQuery(this),'focus','재고수량');\" onBlur=\"check_option_value(jQuery(this),'blur','재고수량');\" placeholder=\"재고수량\" "+countDisabled+" /></td>"
					+ "			<td style=\"text-align:center;\"><div><input type=\"text\" name=\""+tID+"_item_unique_code[]\" id=\""+tID+"_item_unique_code[]\" value=\"\" style=\"width:50%;color:#afafaf;text-align:center; float:left;\" onFocus=\"check_option_value(jQuery(this),'focus','고유번호');\" onBlur=\"check_option_value(jQuery(this),'blur','고유번호');\" placeholder=\"고유번호\" /></td>"
					+ "			<td style=\"text-align:center;\"><input type=\"checkbox\" name=\""+tID+"_item_temp_display[]\" id=\""+tID+"_item_temp_display[]\" value=\"view\" onClick=\"change_item_check('"+tID+"','display','view',"+k+");\" checked=\"checked\" style=\""+checkedStyle+"\" "+checkedDisabled+" /><input type=\"hidden\" name=\""+tID+"_item_display[]\" id=\""+tID+"_item_display[]\" value=\"view\" /></td>"
					+ "			<td style=\"text-align:center;\"><input type=\"checkbox\" name=\""+tID+"_item_temp_soldout[]\" id=\""+tID+"_item_temp_soldout[]\" value=\"soldout\" onClick=\"change_item_check('"+tID+"','soldout','soldout',"+k+");\" style=\""+checkedStyle+"\" "+checkedDisabled+" /><input type=\"hidden\" name=\""+tID+"_item_soldout[]\" id=\""+tID+"_item_soldout[]\" value=\"\" /></td>"
					+ "		</tr>";
			}

			str +="</table>";
		}

		if(str){
			jQuery("#"+tID+"_table").css("display","block");
			jQuery("#"+tID+"_table").html(str);
			create_btn_on(false); // 옵션 저장 버튼 사용불가
		}
	}

	// 옵션 아이템 (추가가격/재고수량)
	function check_option_value(tObj,tType,tStr){
		if(tType=='focus'){
			tObj.attr('placeholder','');tObj.css('color','#303030');
		}
		else if(tType=='blur'){
			if(!tObj.val()){tObj.attr('placeholder',tStr);tObj.css('color','#afafaf');}
		}
	}

	function create_btn_on(tVal){
		createOn=tVal;
	}


    // 옵션값 개수 선택 시 (추가 옵션 설정)
	function goods_add_option_item(gID){
		var cnt=jQuery("#"+gID+"_item_count").val();
		var item_str="";
		var overPrice_str="";
		var unique_str="";
		var display_str="";
		var soldout_str="";
		var itme_brStr="";
		var ist=jQuery("input[name="+gID+"_item\\[\\]]").length;

		if(cnt>0){
			if (jQuery("input[name="+gID+"_title]").length<=0){
				jQuery("#"+gID+"_title_label").html("<input type=\"text\" name=\""+gID+"_title\" id=\""+gID+"_title\" style=\"width:90%;\" />");
			}

			if(cnt<ist){
				for(i=ist;i>cnt;i--){
					jQuery("#"+gID+"_"+i+"_item").remove();
					jQuery("#"+gID+"_"+i+"_overprice").remove();
					jQuery("#"+gID+"_"+i+"_unique").remove();
					jQuery("#"+gID+"_"+i+"_display").remove();
					jQuery("#"+gID+"_"+i+"_soldout").remove();
				}
			}
			else{
				for(i=ist;i<cnt;i++){

					if (jQuery("input[name="+gID+"_item\\[\\]]").length<=i){
						if(i>0) itme_brStr="<br />";
						else itme_brStr="";

						item_str +="<span id=\""+gID+"_"+(i+1)+"_item\">"+itme_brStr+"<input type=\"text\" name=\""+gID+"_item[]\" id=\""+gID+"_item[]\" style=\"width:90%;\" /></span>";

						overPrice_str +="<span id=\""+gID+"_"+(i+1)+"_overprice\">"+itme_brStr+"<input type=\"text\" style=\"width:90%;color:#afafaf;text-align:center;ime-mode:disabled;\" name=\""+gID+"_item_overprice[]\" id=\""+gID+"_item_overprice[]\" onkeydown=\"check_number();\" onFocus=\"check_option_value(jQuery(this),'focus','가격');\" onBlur=\"check_option_value(jQuery(this),'blur','가격');\" value=\"\" placeholder=\"가격\" /></span>";

						unique_str +="<span id=\""+gID+"_"+(i+1)+"_unique\">"+itme_brStr+"<input type=\"text\" style=\"width:50%;color:#afafaf;text-align:center;ime-mode:disabled; float:left;\" name=\""+gID+"_item_unique_code[]\" id=\""+gID+"_item_unique_code[]\" onFocus=\"check_option_value(jQuery(this),'focus','고유번호');\" onBlur=\"check_option_value(jQuery(this),'blur','고유번호');\"  value=\"\" placeholder=\"고유번호\"  /></span>";

						display_str +="<span id=\""+gID+"_"+(i+1)+"_display\">"+itme_brStr+"<input type=\"checkbox\" name=\""+gID+"_item_temp_display[]\" id=\""+gID+"_item_temp_display[]\" value=\"view\" onClick=\"change_item_check('"+gID+"','display','view',"+i+");\" checked=\"checked\" /><input type=\"hidden\" name=\""+gID+"_item_display[]\" id=\""+gID+"_item_display[]\" value=\"view\" /></span>";

						soldout_str +="<span id=\""+gID+"_"+(i+1)+"_soldout\">"+itme_brStr+"<input type=\"checkbox\" name=\""+gID+"_item_temp_soldout[]\" id=\""+gID+"_item_temp_soldout[]\" value=\"soldout\" onClick=\"change_item_check('"+gID+"','soldout','soldout',"+i+");\" /><input type=\"hidden\" name=\""+gID+"_item_soldout[]\" id=\""+gID+"_item_soldout[]\" value=\"\" /></span>";
					}
				}

				jQuery("#"+gID+"_item_label").append(item_str);
				jQuery("#"+gID+"_overprice_label").append(overPrice_str);
				jQuery("#"+gID+"_unique_label").append(unique_str);
				jQuery("#"+gID+"_display_label").append(display_str);
				jQuery("#"+gID+"_soldout_label").append(soldout_str);
			}
		}
		else{
			jQuery("#"+gID+"_title_label").html("");
			jQuery("#"+gID+"_item_label").html("");
			jQuery("#"+gID+"_overprice_label").append("");
			jQuery("#"+gID+"_unique_label").append("");
			jQuery("#"+gID+"_display_label").append("");
			jQuery("#"+gID+"_soldout_label").append("");
		}
	}

	function addoption(usergradeArr, oCnt_new){
		let targetSel = document.getElementById("goods_option_add_usergrade_"+oCnt_new+"_list\"");
		if(targetSel.length === 1) {		
			usergradeArr.forEach(function(list, i) {			
				var opt = document.createElement('option');			
				opt.setAttribute('value', list.no)	;		
				opt.innerText = list.class_name;			
				targetSel.appendChild(opt);		
			})	
		}
	}
	
	// 추가 옵션 (추가/삭제)
	function goods_add_option(oType){
	
		var oCnt=jQuery("#goods_add_option_count").val();
		var str="";

		if(oType=='add'){
			var oCnt_new=parseInt(oCnt)+1;
			if(oCnt_new>4){
				alert('추가 옵션은 4개까지 등록이 가능합니다.    ');
				return;
			}

			str ="<div id=\"goods_add_"+oCnt_new+"_option\">"
				+ "<div class=\"clearfix\"></div>"
				+ "<div class=\"clearfix\" style=\"height:10px;\"></div>"
				+ "<table class=\"dataTbls overWhite collapse\">"
					+ "<colgroup><col width=\"24%\"><col width=\"\"></colgroup>"
					+ "<tr>"
					+ "	<th colspan=\"2\">추가 옵션  ("+oCnt_new+")</th>"
					+ "</tr>"
					+ "<tr>"
					+ "	<th>사용여부</th>"
					+ "	<td><input type=\"radio\" name=\"goods_add_"+oCnt_new+"_use\" id=\"goods_add_"+oCnt_new+"_use\" value=\"on\" checked=\"checked\" /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"goods_add_"+oCnt_new+"_use\" id=\"goods_add_"+oCnt_new+"_use\" value=\"off\" /> 사용안함</td>"
					+ "</tr>"
					+ "<tr>"
					+ "	<th>선택/필수 적용</th>"
					+ "	<td><input type=\"radio\" name=\"goods_add_"+oCnt_new+"_choice\" id=\"goods_add_"+oCnt_new+"_choice\" value=\"selection\" checked=\"checked\" /> 선택항목&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"radio\" name=\"goods_add_"+oCnt_new+"_choice\" id=\"goods_add_"+oCnt_new+"_choice\" value=\"required\" /> 필수항목</td>"
					+ "</tr>"
				+ "</table>"
				+ "<div class=\"clearfix\" style=\"height:10px;\"></div>"
				+ "<table class=\"dataTbls overWhite collapse\">"
					+ "<colgroup><col width=\"10%\"><col width=\"12%\"><col width=\"20%\"><col width=\"12%\"><col width=\"20%\"><col width=\"8%\"><col width=\"8%\"></colgroup>"
					+ "<tr>"
					+ "	<th>옵션값 개수</th><th>옵션명</th><th>옵션값</th><th>가격</th><th>상품코드</th><th>노출</th><th>품절</th>"
					+ "</tr>"
					+ "<tr>"
					+ "	<td class=\"txtCenter\">"
					+ "		<select name=\"goods_add_"+oCnt_new+"_item_count\" id=\"goods_add_"+oCnt_new+"_item_count\" onChange=\"goods_add_option_item('goods_add_"+oCnt_new+"');\">"
					+ "			<option value=\"\">선택</option>"
					+ "			<option value=\"1\">1</option>"
					+ "			<option value=\"2\">2</option>"
					+ "			<option value=\"3\">3</option>"
					+ "			<option value=\"4\">4</option>"
					+ "			<option value=\"5\">5</option>"
					+ "			<option value=\"6\">6</option>"
					+ "			<option value=\"7\">7</option>"
					+ "			<option value=\"8\">8</option>"
					+ "			<option value=\"9\">9</option>"
					+ "			<option value=\"10\">10</option>"
					+ "			<option value=\"11\">11</option>"
					+ "			<option value=\"12\">12</option>"
					+ "			<option value=\"13\">13</option>"
					+ "			<option value=\"14\">14</option>"
					+ "			<option value=\"15\">15</option>"
					+ "			<option value=\"16\">16</option>"
					+ "			<option value=\"17\">17</option>"
					+ "			<option value=\"18\">18</option>"
					+ "			<option value=\"19\">19</option>"
					+ "			<option value=\"20\">20</option>"
					+ "		</select>"
					+ "	</td>"
					+ "	<td class=\"txtCenter\">"
					+ "		<div id=\"goods_add_"+oCnt_new+"_title_label\"></div>"
					+ "	</td>"
					+ "	<td class=\"txtCenter\">"
					+ "		<div id=\"goods_add_"+oCnt_new+"_item_label\"></div>"
					+ "	</td>"
					+ "	<td class=\"txtCenter\">"
					+ "		<div id=\"goods_add_"+oCnt_new+"_overprice_label\"></div>"
					+ "	</td>"
					+ "	<td class=\"txtCenter\">"
					+ "		<div id=\"goods_add_"+oCnt_new+"_unique_label\"></div>"
					+ "	</td>"
					+ "	<td class=\"txtCenter\">"
					+ "		<div id=\"goods_add_"+oCnt_new+"_display_label\"></div>"
					+ "	</td>"
					+ "	<td class=\"txtCenter\">"
					+ "		<div id=\"goods_add_"+oCnt_new+"_soldout_label\"></div>"
					+ "	</td>"
					+ "</tr>"
				+ "</table>"
			+ "</div>";

			jQuery("#goods_add_msg_option").hide();
			jQuery("#goods_add_option").append(str);
			jQuery("#goods_add_option_count").val(oCnt_new);
		}
		else if(oType=='remove'){
			if(oCnt>0){
				var oCnt_new=parseInt(oCnt)-1;
				jQuery("#goods_add_"+oCnt+"_option").remove();
				jQuery("#goods_add_option_count").val(oCnt_new);

				if(oCnt_new==0)	 jQuery("#goods_add_msg_option").show();
			}
		}
	}

	jQuery(document).ready(function() {
		// 상품 이미지 등록 (시작)
		var uploadFormFlag=false;
		
		jQuery("#goods_add_img_upload").click(function() {
			jQuery(".lb-loader").remove();
			uploadFormFlag=true;
			tb_show('상품 이미지 등록', 'media-upload.php?type=image&#38;width=640&#38;height=450&#38;modal=false&#38;TB_iframe=true');
			return false;
		});

		jQuery("#insert-media-button").click(function(event) {    
			uploadFormFlag=false;
		});

		window.original_send_to_editor = window.send_to_editor;
		window.send_to_editor = function(html){
			if (uploadFormFlag) { // 상품등록 이미지 URL 처리
				var div = document.createElement('div');
				jQuery(div).html(html);
				var src = jQuery(div).find("img:first").attr("src");
				var imgID="";
				var viewImg=src;
				var zoomImg=src;
				var iCnt_new=parseInt(jQuery("#goods_add_img_count").val())+1;

				jQuery.ajax({
					type: 'post', 
					async: false, 
					url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-goods-add.exec.php', 
					//url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-goods-add.exec.php',
					data: {tMode:"imgUrl", tURL:src}, 
					success: function(data){
						//alert(data);
						var result = data.split("|||"); 

						if(result['0'] == "success"){
							imgID=result['1'];
							viewImg=result['2'];
							zoomImg=result['3'];
						}
					}
				});	

				var str="<li id=\"goods_img_add_list_"+iCnt_new+"\">"
							+"	<table style=\"margin-left:14px;padding:0px;\">"
							+"		<tr>"
							+"			<td class=\"tbClear\">"
							+"				<div class=\"thumb\">"
							+"					<input type=\"hidden\" name=\"goods_add_img[]\" value=\""+imgID+"\" />"
							+"					<img src=\""+viewImg+"\" id=\"goods_add_img_list_"+iCnt_new+"\" alt=\"상품이미지\" />"
							+"					<span class=\"bg\"></span>"
							+"					<span class=\"btn\">"
							+"						<a href=\""+zoomImg+"\" data-lightbox=\"list-set\" data-title=\"상품이미지 "+iCnt_new+"\" class=\"zoom\"><span>이미지보기</span></a>"
							+"					</span>"
							+"				</div>"
							+"			</td>"
							+"			<td style=\"border-bottom:0px;padding:0px;\"><img src=\"<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png\" onClick=\"goods_img_remove("+iCnt_new+");\" class=\"deleteBtn\" alt=\"이미지 제거\" title=\"이미지 제거\" /></td>"
							+"		</tr>"
							+"		<tr>"
							+"			<td class=\"tbClear\" style=\"padding-left:-25px;\"><input type=\"radio\" name=\"goods_basic_img\" id=\"goods_basic_img\" onClick=\"goods_img_basic_check("+iCnt_new+");\" value=\""+imgID+"\"  /></td>"
							+"			<td class=\"tbClear\"></td>"
							+"		</tr>"
							+"	</table>"
						+"</li>";

				jQuery("#goods_img_add_list").append(str);

				jQuery("#goods_add_img_count").val(iCnt_new);

				tb_remove();

				if(iCnt_new==1){
					goods_img_basic_check(1);
					jQuery("input[name=goods_basic_img]").first().attr("checked", true);
				}

			} else { // 에디터에 이미지 등록
				window.original_send_to_editor(html); 
			}
		};
		// 상품 이미지 등록 (끝)

	<?php if($tMode=='modify'){?>	
		create_btn_on(false); // 옵션 저장 버튼 사용불가
	<?php }?>
	});

	// 기본이미지(radio) 선택 시 이미지 테두리
	function goods_img_basic_check(tNo){
		var fCnt=jQuery("#goods_add_img_count").val();

		for(i=1;i<=fCnt;i++){
			if(jQuery("#goods_add_img_list_"+i).length){
				if(i==tNo)	jQuery("#goods_add_img_list_"+i).css("border","2px solid #FF7F27");
				else jQuery("#goods_add_img_list_"+i).css("border","2px solid #FFFFFF");
			}
		}
	}

	// 상품 이미지 삭제 시
	function goods_img_remove(iNo){
		jQuery("#goods_img_add_list_"+iNo).remove();
		
		if(jQuery("input[name=goods_basic_img]:checked").size()<=0 && jQuery("input[name=goods_basic_img]").size()>0){
			jQuery("input[name=goods_basic_img]").first().attr("checked", true);

			var fCnt=jQuery("#goods_add_img_count").val();

			for(i=0;i<=fCnt;i++){
				if(jQuery("#goods_add_img_list_"+i).length){
					jQuery("#goods_add_img_list_"+i).css("border","2px solid #FF7F27");
					break;
				}
			}
		}
	}

	// thickbox 팝업창 크기 조절
	function thickbox_resize() {
		jQuery(document).find('#TB_window').width( TB_WIDTH ).height( TB_HEIGHT );
	}

	// 라디오 버튼 선택 => display
	function goods_fields_onoff(tId,tVal){
		if(tVal=='on'){
			jQuery("#"+tId+"_item").show();
		}
		else jQuery("#"+tId+"_item").hide();
	}

	// 재고설정 - 수량
	function goods_count_change(){
		var gFlag=jQuery("#goods_count_flag").val();
		var oldFlag=jQuery("#goods_count_oldflag").val();

		if(oldFlag=='option_count' && (gFlag=='goods_count' || gFlag=='unlimit')){
			if(confirm("입력하신 옵션 재고수량이 초기화 됩니다.\n재고설정을 변경 하시겠습니까?          ")){
				jQuery("input[name=goods_option_item_count\\[\\]]").prop("disabled",true);
				jQuery("input[name=goods_option_item_count\\[\\]]").css("background","#f4f4f4");
				jQuery("input[name=goods_option_item_count\\[\\]]").css("color","#a2a2a2");

				jQuery("input[name=goods_option_item_temp_display\\[\\]]").prop("disabled",true);
				jQuery("input[name=goods_option_item_temp_display\\[\\]]").css("background","#dfdfdf");

				jQuery("input[name=goods_option_item_temp_soldout\\[\\]]").prop("disabled",true);
				jQuery("input[name=goods_option_item_temp_soldout\\[\\]]").css("background","#dfdfdf");
			}
			else{
				return;
			}
		}
		else if(gFlag=='option_count'){
			jQuery("input[name=goods_option_item_count\\[\\]]").prop("disabled",false);
			jQuery("input[name=goods_option_item_count\\[\\]]").css("background","#ffffff");
			jQuery("input[name=goods_option_item_count\\[\\]]").css("color","#52525f");

			jQuery("input[name=goods_option_item_temp_display\\[\\]]").prop("disabled",false);
			jQuery("input[name=goods_option_item_temp_display\\[\\]]").css("background","#ffffff");

			jQuery("input[name=goods_option_item_temp_soldout\\[\\]]").prop("disabled",false);
			jQuery("input[name=goods_option_item_temp_soldout\\[\\]]").css("background","#ffffff");
		}
		else{
			jQuery("input[name=goods_option_item_count\\[\\]]").prop("disabled",true);
			jQuery("input[name=goods_option_item_count\\[\\]]").css("background","#f4f4f4");
			jQuery("input[name=goods_option_item_count\\[\\]]").css("color","#a2a2a2");

			jQuery("input[name=goods_option_item_temp_display\\[\\]]").prop("disabled",true);
			jQuery("input[name=goods_option_item_temp_display\\[\\]]").css("background","#dfdfdf");

			jQuery("input[name=goods_option_item_temp_soldout\\[\\]]").prop("disabled",true);
			jQuery("input[name=goods_option_item_temp_soldout\\[\\]]").css("background","#dfdfdf");
		}

		if(gFlag=='goods_count'){
			jQuery("#goods_count_item").show();
			jQuery("#option_setting").hide();
		}
		else{
			jQuery("#goods_count_item").hide();
			jQuery("#option_setting").show();
		}

		if(gFlag=='goods_count' || gFlag=='option_count') jQuery("#goods_count_view_info").css("display","table-row");
		else jQuery("#goods_count_view_info").css("display","none");

		jQuery("#goods_count_oldflag").val(gFlag);
	}

	// 관심상품(추천/관련상품) 등록 팝업
	function goods_list_popup(pTarget){
		var popupTitle="";
		var tCnt=jQuery("input[name=goods_"+pTarget+"_list\\[\\]]").size();
		var chkList="";

		if(pTarget=='recommend') popupTitle="추천상품";
		else if(pTarget=='relation') popupTitle="관련상품";

		if(tCnt>=4){
			alert(popupTitle+"은 최대4개까지만 등록이 가능합니다.   ") ;
			return;
		}

		for(i=0;i<tCnt;i++){
			if(chkList) chkList +=",";
			chkList +=jQuery("input[name=goods_"+pTarget+"_list\\[\\]]").eq(i).val();
		}

		tb_show("상품목록 ("+popupTitle+")", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-goods-list.php?pTarget="+pTarget+"&#38;chkList="+chkList+"&#38;TB_iframe=true");
		//thickbox_resize();
		return false;
	}

	// 관심상품(추천/관련상품) 등록 팝업 닫기
	function remove_popup(){
		tb_remove();
	}

	// 연계상품 이미지 삭제 시
	function goods_img_link_remove(pTarget,tId){
		jQuery("#goods_img_"+pTarget+"_list_"+tId).remove();

		var tCnt=jQuery("input[name=goods_"+pTarget+"_list\\[\\]]").size();
		if(tCnt<=0) jQuery("#goods-"+pTarget).css("display","none");
	}

	// 연계상품 - on/off
	function goods_link_fields_onoff(pTarget,tVal){
		var tCnt=jQuery("input[name=goods_"+pTarget+"_list\\[\\]]").size();

		if(tVal=='on'){
			jQuery("#goods_"+pTarget+"_use_item").show();
			if(tCnt>0) jQuery("#goods-"+pTarget).show();
		}
		else{
			jQuery("#goods_"+pTarget+"_use_item").hide();
			jQuery("#goods-"+pTarget).hide();
		}
	}

	// 상품별 SEO설정 - on/off
	function goods_seo_click(tValue){
		if(tValue=='on'){
			jQuery("#seo_title").show();
			jQuery("#seo_description").show();
			jQuery("#seo_keyword").show();
		}
		else{
			jQuery("#seo_title").hide();
			jQuery("#seo_description").hide();
			jQuery("#seo_keyword").hide();
		}
	}

	function go_goods_page(gType){
		var goUrl="";
		if(gType=='list'){
			goUrl="admin.php?page=bbse_commerce_goods";
		}
		else if(gType=='modify'){
			var tData="<?php echo $tData;?>";
			goUrl="admin.php?page=bbse_commerce_goods_add&tMode=modify&tData="+tData;
		}

		window.location.href=goUrl;
	}

	//판매가 입력시 부가세 자동입력 10% 입력
	function onblur_mPrice(mpriceid, vatid){
		let mPrice = parseInt(jQuery("#"+mpriceid).val());
		let vat = parseInt(mPrice * (1/10));
		jQuery("#"+vatid).val(vat);
		
	}
	
	function goods_submit(tMode){
	    		
		let length = "<?php echo $csCnt;?>";
		let cPrice = 0;
		let mPrice = 0;
		
		for(let i = 0; i<length; i++){
			cPrice = parseInt(jQuery("#goods_consumer_price_"+i).val());
			category = parseInt(jQuery("#goods_cate_"+i).val());
			if(cPrice < 0 || cPrice == null || cPrice == ""){
				cPrice = 0; 
			}
			else{
				mPrice = parseInt(jQuery("#goods_member_price_"+i).val());
				if(mPrice > cPrice){
        			alert("판매가는 소비자가보다 작거나 같게 입력해주세요.     ");
        			jQuery("#goods_member_price_"+i).focus();
        			return;
				}
			}
			
			if(category == null || category == ""){
				alert("카테고리 선택은 필수 입니다.     ");
    			jQuery("#goods_cate_"+i).focus();
    			return;
			}
		}
		
		jQuery("#tMode").val(tMode);
		
		if(tMode=='insert') var modeStr="등록";
		else if(tMode=='modify') var modeStr="수정";

		switchEditors.go('goods_detail', 'tmce');

		var ed = tinyMCE.get('goods_detail');
		jQuery("#goods_detail").val(ed.getContent({format : 'raw'}));  // raw(비쥬얼) / text(텍스트)

    	
		if(!jQuery("#goods_name").val()){
			alert("상품명을 입력해 주세요.     ");
			var letee = jQuery("#goods_name").focus();
			return
		}
		
		var tmpDetail=jQuery("#goods_detail").val().replace('<p><br data-mce-bogus=\"1\"></p>', '');
		tmpDetail=tmpDetail.replace('<p><br></p>', '');
	
		/* if(tMode == 'insert'){ 
        	if(goodsFrm.check_flag.value!="ok") {
        		alert("상품코드 중복확인을 해주세요.");
        		return false;
        	}
    	}
    	*/
		if(!tmpDetail){
			alert("상품상세정보를 입력해 주세요.     ");
			tinyMCE.get('goods_detail').focus()
			return
		}
		if(!jQuery("#goods_company").val()){
			alert("제조사를 입력해 주세요.     ");
			jQuery("#goods_company").focus();
			return
		}
		if(!jQuery("#goods_local").val()){
			alert("원산지를 입력해 주세요.     ");
			jQuery("#goods_local").focus();
			return
		}
		
		/* if(!jQuery("#goods_consumer_price").val()){
			alert("소비자가를 입력해 주세요.     ");
			jQuery("#goods_consumer_price").focus();
			return
		}
		
		if(jQuery("#goods_consumer_price").val()<=0){
			alert("소비자가를 0보다 큰값으로 입력해 주세요.     ");
			jQuery("#goods_consumer_price").focus();
			return
		}
		if(eval(jQuery("#goods_consumer_price").val())<eval(jQuery("#goods_price").val())){
			alert("소비자가를 판매가 보다 크거나 같게 입력해 주세요.     ");
			jQuery("#goods_consumer_price").focus();
			return
		}
		*/
		if(jQuery("#goods_count_flag").val()=='goods_count' && jQuery("#goods_display").val()=='display' && (!jQuery("#goods_count").val() || jQuery("#goods_count").val()<=0)){ // option_count
			alert("재고수량을 입력해 주세요.     .\n재고수량이 '0' 인 경우\n노출여부를 '노출품절' 또는 '비노출' 로 변경하신 후 등록이 가능합니다.");
			jQuery("#goods_count").focus();
			return
		}

		var aCnt=jQuery("input[name=goods_add_field_title\\[\\]]").length;
		if(aCnt>0){
			for(a=0;a<aCnt;a++){
				if(!jQuery("input[name=goods_add_field_title\\[\\]]").eq(a).val()){
					alert("추가항목("+(a+1)+")의 항목명을 입력해 주세요.   ");
					jQuery("input[name=goods_add_field_title\\[\\]]").eq(a).focus();
					return;
				}
				if(!jQuery("input[name=goods_add_field_description\\[\\]]").eq(a).val()){
					alert("추가항목("+(a+1)+")의 내용을 입력해 주세요.   ");
					jQuery("input[name=goods_add_field_description\\[\\]]").eq(a).focus();
					return;
				}
			}
		}

		if(jQuery("#goods_count_flag").val()=='option_count' && jQuery("input[name=goods_option_title\\[\\]]").length<=0){
			alert("'옵션별 재고설정'의 경우 상품옵션이 필요합니다.\n상품 옵션을 등록해 주세요.   ");
			jQuery("#goods_option_1_count").focus();
			return;
		}

		var optCnt_1=jQuery("#goods_option_1_count").val();
		var optCnt_2=jQuery("#goods_option_2_count").val();
		if(optCnt_1>0 || optCnt_2>0){
			if(optCnt_1>0){
				if(!jQuery("#goods_option_1_title").val()){
					alert('상품 옵션의 첫번째 옵션명을 입력해 주세요.  ');
					jQuery("#goods_option_1_title").focus();
					return;
				}
				for(i=0;i<optCnt_1;i++){
					if(!jQuery("input[name=goods_option_1_item\\[\\]]").eq(i).val()){
						alert('첫번째 상품 옵션의 옵션값을 모두 입력해 주세요.    ');
						jQuery("input[name=goods_option_1_item\\[\\]]").eq(i).focus();
						return;
					}
				}
			}
			if(optCnt_2>0){
				if(!jQuery("#goods_option_2_title").val()){
					alert('상품 옵션의 두번째 옵션명을 입력해 주세요.  ');
					jQuery("#goods_option_2_title").focus();
					return;
				}
				for(i=0;i<optCnt_2;i++){
					if(!jQuery("input[name=goods_option_2_item\\[\\]]").eq(i).val()){
						alert('두번째 상품 옵션의 옵션값을 모두 입력해 주세요.    ');
						jQuery("input[name=goods_option_2_item\\[\\]]").eq(i).focus();
						return;
					}
				}
			}
			if(createOn){
				alert("옵션값이 변경되었습니다. '상품 옵션저장'을 실행해 주세요.   ");
				jQuery("#btn_good_option").focus();
				return;
			}
			if(jQuery("#goods_count_flag").val()=='option_count'){ 
				var optCnt=jQuery("input[name=goods_option_title\\[\\]]").length;
				var optCntFlag="";
				for(b=0;b<optCnt;b++){
					var tmpOptCnt=jQuery("input[name=goods_option_item_count\\[\\]]").eq(b).val();
					if(tmpOptCnt>0 && !jQuery("input[name=goods_option_item_temp_soldout\\[\\]]").eq(b).is(":checked")){
						optCntFlag="exist";
						break;
					}
				}

				if(!optCntFlag && jQuery("#goods_display").val()=='display'){
					alert("모든 옵션의 재고 수량이 '0'  또는 모두 '품절' 입니다.\n모든 옵션의 재고 수량이 '0' 또는 모두 '품절' 인 경우\n노출여부를 '노출품절' 또는 '비노출' 로 변경하신 후 등록이 가능합니다.");
					jQuery("#goods_display").focus();
					return;
				}

				var optViewFlag="";
				for(b=0;b<optCnt;b++){
					if(jQuery("input[name=goods_option_item_temp_display\\[\\]]").eq(b).is(":checked")){
						optViewFlag="exist";
						break;
					}
				}

				if(!optViewFlag && jQuery("#goods_display").val()=='display'){
					alert("모든 옵션이 노출 안함 입니다.\n모든 옵션이 노출 안함 인 경우\n노출여부를 '노출품절' 또는'비노출' 로 변경하신 후 등록이 가능합니다.");
					jQuery("#goods_display").focus();
					return;
				}
			}
		}

		var addCnt=jQuery("#goods_add_option_count").val();
		if(addCnt>0){
			for(d=1;d<=addCnt;d++){
				var tmpAddCnt=jQuery("#goods_add_"+d+"_item_count").val();
				if(!tmpAddCnt ||  tmpAddCnt<=0){
					alert("추가옵션의 옵션값 개수를 선택해 주세요.       ");
					jQuery("#goods_add_"+d+"_item_count").focus();
					return;
				}
				if(!jQuery("#goods_add_"+d+"_title").val()){
					alert("추가옵션의 옵션명을 입력해 주세요.       ");
					jQuery("#goods_add_"+d+"_title").focus();
					return;
				}

				for(j=0;j<tmpAddCnt;j++){
					if(!jQuery("input[name=goods_add_"+d+"_item\\[\\]]").eq(j).val()){
						alert("추가옵션의 옵션값을 입력해 주세요.       ");
						jQuery("input[name=goods_add_"+d+"_item\\[\\]]").eq(j).focus();
						return;
					}
				}

				for(j=0;j<tmpAddCnt;j++){
					if(!jQuery("input[name=goods_add_"+d+"_item_overprice\\[\\]]").eq(j).val()){
						jQuery("input[name=goods_add_"+d+"_item_overprice\\[\\]]").eq(j).val(0);
					}
				}
			}
		}

		if(jQuery("input[name=goods_recommend_use]").eq(0).is(":checked") && jQuery("input[name=goods_recommend_list\\[\\]]").length<=0){
			alert("추천상품을 선택해 주세요.          ")
			jQuery("input[name=goods_recommend_use]").eq(0).focus();
			return;
		}
		if(jQuery("input[name=goods_relation_use]").eq(0).is(":checked") && jQuery("input[name=goods_relation_list\\[\\]]").length<=0){
			alert("관련상품을 선택해 주세요.          ")
			jQuery("input[name=goods_relation_use\\[\\]]").eq(0).focus();
			return;
		}
		if(jQuery("input[name=goods_seo_use]").eq(0).is(":checked")){
			if(!jQuery("#goods_seo_title").val()){
				alert("SEO 타이틀을 입력해 주세요.          ")
				jQuery("#goods_seo_title").focus();
				return;
			}
			if(!jQuery("#goods_seo_description").val()){
				alert("SEO 설명을 입력해 주세요.          ")
				jQuery("#goods_seo_description").focus();
				return;
			}
			if(!jQuery("#goods_seo_keyword").val()){
				alert("SEO 키워드를 입력해 주세요.          ")
				jQuery("#goods_seo_keyword").focus();
				return;
			}
		}
		if(jQuery("input[name=goods_earn_use]").eq(0).is(":checked") && !jQuery("#goods_earn").val()){
			alert("적립금을 입력해 주세요.          ")
			jQuery("#goods_earn").focus();
			return;
		}
		if(jQuery("input[name=goods_delivery]").eq(2).is(":checked") && jQuery("#goods_delivery_price_arrival").val()<=0){
			alert("착불배송비를 입력해 주세요.          ")
			jQuery("#goods_delivery_price_arrival").focus();
			return;
		}
		if(jQuery("input[name=goods_delivery]").eq(3).is(":checked") && jQuery("#goods_delivery_price_fix").val()<=0){
			alert("고정배송비를 입력해 주세요.          ")
			jQuery("#goods_delivery_price_fix").focus();
			return;
		}
		if(jQuery("input[name=goods_delivery]").eq(4).is(":checked") && jQuery("#goods_delivery_price_count").val()<=0){
			alert("수량별 배송비를 입력해 주세요.          ")
			jQuery("#goods_delivery_price_count").focus();
			return;
		}
/*		if(jQuery("input[name=goods_cat_list\\[\\]]:checked").size()<=0){
			alert("상품 카테고리를 선택해 주세요.          ")
			jQuery("input[name=goods_cat_list\\[\\]]").eq(0).focus();
			return;
		}
		*/
 		if(jQuery("input[name=goods_add_img\\[\\]]").length<=0){
			alert("상품 이미지를 선택해 주세요.          ")
			jQuery("#goods_add_img_upload").focus();
			return;
		} 
		if(jQuery("input[name=goods_basic_img]:checked").size()<=0 && jQuery("input[name=goods_basic_img]").size()>0){
			jQuery("input[name=goods_basic_img]").first().attr("checked", true);
		}

		if(confirm('해당 상품을 '+modeStr+'하시겠습니까?       ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-goods-add.exec.php', 
				//url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-goods-add.exec.php',
				data: jQuery("#goodsFrm").serialize(), 
				success: function(data){
					//alert(data);
					var result = data.split("|||"); 

					if(result['0'] == "success"){
						if(tMode=='insert'){
							alert("상품등록을 정상적으로 완료하였습니다.    ");
							go_goods_page('list');
						}
						else if(tMode=='modify'){
							alert("상품수정을 정상적으로 완료하였습니다.    ");
							go_goods_page('modify');
						}
					}
					else if(result['0'] == "dbError"){
						alert("[Error] DB 오류 입니다.   ");
						jQuery("#category_name").focus();		
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

	function change_item_check(tId,tType,tValue,tNo){
		if(jQuery("input[name="+tId+"_item_temp_"+tType+"\\[\\]]").eq(tNo).is(":checked")){
			jQuery("input[name="+tId+"_item_"+tType+"\\[\\]]").eq(tNo).val(tValue);
		}
		else jQuery("input[name="+tId+"_item_"+tType+"\\[\\]]").eq(tNo).val("");
	}
	
	function checkGoodsCode(mode){
		
		let goodsCode = jQuery("#goods_code").val();
		let goodsUniqueCode = jQuery("#goods_option_item_unique_code\\[\\]").val();
		
		let data = {};
		if(mode == 'option'){
			/* if(goodsUniqueCode == "") {
    			alert("옵션 상품코드를 입력해주세요.");
    			jQuery("#goods_code").focus();
    			return;
			}
			
			if(goodsUniqueCode.length < 5){
				alert("옵션 상품코드는 5자리를 코드를 입력해주세요.");
    			return;
			}
			*/
			
			let optionCnt = jQuery("#goods_add_1_item_count").val();
			
			if(optionCnt > '0'){
				for(let i=0; i<optionCnt; i++){
					jQuery("#goods_add_"+i+"_item_unique_code").focus();
				}
			}
			
			data = {"goodsUniqueCode":goodsUniqueCode, "mode": mode}
			
		}else{
			/* if(goodsCode == "") {
    			alert("상품코드를 입력해주세요.");
    			return;
			}
			
			if(goodsCode.length < 5){
				alert("상품코드는 5자리를 코드를 입력해주세요.");
    			return;
			}
			*/
			
			data = {"goodsCode":goodsCode, "mode": mode}
		}
		
	   jQuery.ajax({
			type: 'post'
			, async: false
			, url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-goodsCode-check.php'
			//, url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-goodsCode-check.php'
			, data: data
    		, success: function(data){
    			var response = data.split("|||");
    			if(jQuery.trim(response[0]) == "ok"){
    				alert("사용가능한 상품코드 입니다.");
    				jQuery("#check_flag").val(response[0].trim());
    			}else if(jQuery.trim(response[0]) == "exist"){
    				alert("이미 등록된 상품코드 입니다.");
    				jQuery("#goods_code").select();
    			}else{
    				alert('서버와의 통신이 실패했습니다.');
    			}
    		}
    		, error: function(data, status, err){
    			alert('서버와의 통신이 실패했습니다.');
    		}
		});
		
	
	}
</script>

<div class="wrap">
	<div style="margin-bottom:30px;">
		<h2>상품등록</h2>
		<hr>
	</div>
	<form name="goodsFrm" id="goodsFrm">
	<input type="hidden" name="tMode" id="tMode" value="" />
	<input type="hidden" name="check_flag" id="check_flag" value="" />
	<input type="hidden" name="tData" id="tData" value="<?php echo ($tMode=='modify' && $tData)?$tData:"";?>" />
	<input type="hidden" name="goods_count_oldflag" id="goods_count_oldflag" value="<?php echo (!$data->goods_count_flag)?"unlimit":$data->goods_count_flag;?>" />
	<div style="float:left;width:65%;">
		<table class="dataTbls overWhite collapse">
			<colgroup><col width="24%"><col width=""></colgroup>
		<?php if($nvrData['naver_shop_use']=='on'){?>
			<tr>
				<th>네이버 지식쇼핑</th>
				<td><input type="checkbox" name="goods_naver_shop" id="goods_naver_shop" value="on" <?php echo ($tMode != 'modify' || ($tMode == 'modify' && $data->goods_naver_shop=='on'))?"checked=\"checked\"":"";?> /> 지식쇼핑 적용&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/naver_logo.png" align="absmiddle" /></td>
			</tr>
		<?php }?>
		<?php if($nvrPayData['naver_pay_use']=='on'){?>
			<tr>
				<th>네이버 페이</th>
				<td><input type="checkbox" name="goods_naver_pay" id="goods_naver_pay" value="on" <?php echo ($tMode != 'modify' || ($tMode == 'modify' && $data->goods_naver_pay=='on'))?"checked=\"checked\"":"";?> /> 네이버페이 적용&nbsp;&nbsp;&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/icon_npay.png" align="absmiddle" /></td>
			</tr>
		<?php }?>
			<tr>
				<th>아이콘표시</th>
				<td><input type="checkbox" name="goods_icon_new" id="goods_icon_new" value="view" <?php echo ($data->goods_icon_new=='view')?"checked=\"checked\"":"";?> /> 신상품 아이콘&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="goods_icon_best" id="goods_icon_best" value="view" <?php echo ($data->goods_icon_best=='view')?"checked=\"checked\"":"";?> /> 베스트상품 아이콘</td>
			</tr>
			<tr>
				<th>상품명</th>
				<td><input type="text" style="width:80%;" name="goods_name" id="goods_name" value="<?php echo $data->goods_name;?>" />&nbsp;<?php echo $gCountFlag;?></td>
			</tr>
			<tr>
				<th>간단설명</th>
				<td><textarea name="goods_description" id="goods_description" style="width:100%;height:50px;"><?php echo $data->goods_description;?></textarea></td>
			</tr>
		</table>

		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0; ">상품상세설명</div>

		<table class="dataTbls overWhite collapse">
			<colgroup><col width=""></colgroup>
			<tr>
				<td>
					<div style="margin:10px 0;min-height:100px;">
						<?php 
						wp_editor(html_entity_decode(str_replace("&nbsp;"," ",$data->goods_detail)), "goods_detail", $settings=array('textarea_name'=>'goods_detail', 'textarea_rows'=>'7')); 
						?> 
					</div>
				</td>
			</tr>
		</table>
		
		<!-- start 등급별 금액설정 추가 241022 -->
		
		<?php 
		if($gcsCnt>'0')$addOptUsergrade=unserialize($gdata->goods_member_price);

		if($addOptUsergrade['goods_member_price']>'0') $addOptUsergradeCnt=$addOptUsergrade['goods_member_price'];
		else $addOptUsergradeCnt='0';
		?>
		
		
		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0;">등급별 금액설정<span class="prd-desc">* 등급별 판매금액을 입력해주세요. 제품 미판매 등급인 경우 0 또는 빈 값을 입력해주세요.</span>
		<div style="float:right;">
			<input type="hidden" name=goods_option_add_usergrade_price_count id="goods_option_add_usergrade_price_count" value="<?php echo $addOptUsergradeCnt;?>" />
		</div>
		<table class="dataTbls overWhite collapse" id="goods_member_level_price_table">
			<colgroup><col width="18%"><col width="24%"><col width="20%"><col width="20%"><col width="18%"></colgroup>
			<tr>
				<th>등급</th><th>소비자가</th><th>판매가</th><th>부가세</th><th>카테고리</th>
			</tr>
		<?php
		//등급정보가져옴
		$cs_result = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class WHERE no>'2' AND use_sale='Y' ORDER BY no DESC");
        if($gcsCnt>'0'){
            //멤버별 판매가격 가져옴 
            if($gdata->goods_member_price) $memPrice=unserialize($gdata->goods_member_price);
        
            foreach($cs_result as $i => $csData){
                $tMemberPrice="";
                $tCumsumerPrice = "";
                $tVat = "";
                $tCate = "";
                for($z=0;$z<sizeof($memPrice['goods_member_level']);$z++){
                    if($memPrice['goods_member_level'][$z]==$csData->no){
                        $tMemberPrice=$memPrice['goods_member_price'][$z];
                        $tCumsumerPrice=$memPrice['goods_consumer_price'][$z];
                        $tVat=$memPrice['goods_vat'][$z];
                        $tCate=$memPrice['goods_cat_sub_list'][$z];
                    }
                }
        ?>		
				<div class="clearfix"></div>
				<tr id="goods_member_level_price_<?php echo $i?>">
				<td class="txtCenter">
					<div><?php echo $csData->class_name;?>
						<input type="hidden" name="goods_member_level[]" id="goods_member_level_<?php echo $i?>" value="<?php echo stripslashes($csData->no);?>" style="width:100px;" />
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_2_title_label">
						<input type="text" name="goods_consumer_price[]" id="goods_consumer_price_<?php echo $i?>" value="<?php echo $tCumsumerPrice;?>" style="width:100px;" />&nbsp;원
						<input type="checkbox" name="goods_cprice_display[]" id="goods_cprice_display" value="<?php echo stripslashes($csData->no);?>" 
						<?php
						$cdDatas = unserialize($cpDisplay);
						$arr = $cdDatas[goods_cprice_display];
						for($j=0;$j<sizeof($arr); $j++){
						    if($arr[$j]==$csData->no){
                                echo "checked=\"checked\"";
                            }
						}
						?> />
						숨김
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_3_title_label">
						<input type="text" name="goods_member_price[]" id="goods_member_price_<?php echo $i?>" value="<?php echo $tMemberPrice;?>" style="width:100px;" onblur="onblur_mPrice('goods_member_price_<?php echo $i;?>','goods_vat_<?php echo $i;?>');" onChange="onblur_mPrice('goods_member_price_<?php echo $i;?>','goods_vat_<?php echo $i;?>');" />&nbsp;원
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_4_title_label">
						<input type="text" name="goods_vat[]" id="goods_vat_<?php echo $i?>" value="<?php echo $tVat;?>" style="width:100px;" />&nbsp;원
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_5_title_label">
						<select name="goods_cat_sub_list[]" id="goods_cat_sub_list_<?php echo $i?>" value="<?php echo $tCate;?>" style="width:100px;">
		<?php
            $cate_list = $wpdb->get_results("select a.idx dep1_idx,b.idx dep2_idx,	a.depth_1,	b.depth_2,	b.depth_3,	a.c_name main_name,	b.c_name sub_name from	bbse_commerce_category a ,	(	select		depth_1 ,d.depth_2, d.depth_3, d.c_name,d.idx	from		bbse_commerce_category d	where		user_class = '" .$csData->no."'		and depth_2 <> 0	union 		select		c.depth_1, c.depth_2, c.depth_3, c.c_name,c.idx	from		bbse_commerce_category c	where		user_class = '" .$csData->no."'		and depth_3 <> 0	) b where	a.depth_2 = 0	and a.depth_1 = b.depth_1");
            if(is_null($cate_list) || sizeof($cate_list) == 0){
                echo '<option value="0$0$0">선택</option>';                
		    }else{
		        foreach($cate_list as $j => $row){
		            if($row->depth_1.'$'.$row->depth_2.'$'.$row->depth_3 == $tCate){
		                echo '<option selected=selected value="'.$row->depth_1.'$'.$row->depth_2.'$'.$row->depth_3.'">'.$row->main_name.'-'.$row->sub_name.'</option>';
		            }else{
		                echo '<option value="'.$row->depth_1.'$'.$row->depth_2.'$'.$row->depth_3.'">'.$row->main_name.'-'.$row->sub_name.'</option>';
		            }
		        }
		    }
		?>
    					</select>
					</div>
				</td>
			</tr>
		<?php
			}
        ?>
        <?php
        }else{
            foreach($cs_result as $i => $csData){ //회원등급리스트 
    	?>
    		<tr id="goods_member_level_price_<?php echo $i?>">
				<td class="txtCenter">
					<div><?php echo $csData->class_name;?>
						<input type="hidden" name="goods_member_level[]" id="goods_member_level_<?php echo $i?>" value="<?php echo stripslashes($csData->no);?>" style="width:100px;" />
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_2_title_label">
						<input type="text" name="goods_consumer_price[]" id="goods_consumer_price_<?php echo $i?>" value="0" style="width:100px;" />&nbsp;원
						<input type="checkbox" name="goods_cprice_display[]" id="goods_cprice_display" value="<?php echo stripslashes($csData->no);?>" 
						<?php
						$cdDatas = unserialize($cpDisplay);
						$arr = $cdDatas[goods_cprice_display];
						for($j=0;$j<sizeof($arr); $j++){
						    if($arr[$j]==$csData->no){
                                echo "checked=\"checked\"";
                            }
						}
						?> /> 
						숨김
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_3_title_label">
						<input type="text" name="goods_member_price[]" id="goods_member_price_<?php echo $i?>" value="0" style="width:100px;" />&nbsp;원
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_4_title_label">
						<input type="text" name="goods_vat[]" id="goods_vat_<?php echo $i?>" value="0" style="width:100px;" />&nbsp;원
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_add_usergrade_5_title_label">
						<select name="goods_cat_sub_list[]" id="goods_cat_sub_list_<?php echo $i?>" style="width:100px;">
		<?php
            $cate_list = $wpdb->get_results("select a.idx,	a.depth_1,	b.depth_2,	b.depth_3,	a.c_name main_name,	b.c_name sub_name , b.user_class user_class from	bbse_commerce_category a ,	(	select		depth_1 ,d.depth_2, d.depth_3, d.c_name	, d.user_class from bbse_commerce_category d	where		user_class = '" .$csData->no."'		and depth_2 <> 0	union 		select		c.depth_1, c.depth_2, c.depth_3, c.c_name, c.user_class	from		bbse_commerce_category c	where		user_class = '" .$csData->no."'		and depth_3 <> 0	) b where	a.depth_2 = 0	and a.depth_1 = b.depth_1");
            if($cate_list != null && $cate_list != ""){
                foreach($cate_list as $j => $row){
                    if($row->depth_1.'$'.$row->depth_2.'$'.$row->depth_3 == $tCate){
                        echo '<option selected=selected value="'.$row->depth_1.'$'.$row->depth_2.'$'.$row->depth_3.'">'.$row->main_name.'-'.$row->sub_name.'</option>';
                    }else{
                        echo '<option value="'.$row->depth_1.'$'.$row->depth_2.'$'.$row->depth_3.'">'.$row->main_name.'-'.$row->sub_name.'</option>';
                    }
                }
            }
		?>
    					</select>
					</div>
				</td>
			</tr>
		<?php
		  }
        }
    	?>
		</table>
		</div>
		
		<!-- end 등급별 금액설정 추가 241022  -->

		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0; ">기본설정</div>
		<table class="dataTbls overWhite collapse">
			<colgroup><col width="12%"><col width="12%"><col width=""></colgroup>
			<tr>
				<th colspan="2">상품코드</th>
				<td>
					<div id="goods_code_msg" class="prd-desc" style="margin-top:-7px;<?php echo ($data->goods_code)?"font-size:14px;font-weight:bold;":"";?>"><?php echo ($data->goods_code)?$data->goods_code:"* 상품등록 후 확인이 가능합니다.";?></div>
					<!-- <div><input type="text" name="goods_code" id="goods_code" maxlength ="5" minlength="5" value="<?php echo $data->goods_code;?>" style="width:300px;" />&nbsp;&nbsp;<button type="button" onclick="checkGoodsCode('essential');" class="button-small gray">중복확인</button></div> -->
				</td>
			</tr>
			<tr>
				<th colspan="2">고유번호</th>
				<td><input type="text" name="goods_unique_code" id="goods_unique_code" value="<?php echo $data->goods_unique_code;?>" style="width:300px;" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="goods_unique_code_display" id="goods_unique_code_display" value="view" <?php echo ($data->goods_unique_code_display=='view')?"checked=\"checked\"":"";?> /> 노출</td>
			</tr>
			<!-- <tr>
				<th colspan="2">더존코드</th>
				<td>
 					<?php $dzCode = $wpdb->get_row("select * from tbl_douzone_code where goods_code = '".$data->goods_code."'");?>
					<input type="text" name="goods_douzone_code" id="goods_douzone_code" value="<?php echo $dzCode->goods_douzone_code;?>" style="width:300px;" />
				</td>
			</tr> -->
			<tr>
				<th colspan="2">바코드</th>
				<td><input type="text" name="goods_barcode" id="goods_barcode" value="<?php echo $data->goods_barcode;?>" style="width:300px;" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="goods_barcode_display" id="goods_barcode_display" value="view" <?php echo ($data->goods_barcode_display=='view')?"checked=\"checked\"":"";?> /> 노출</td>
			</tr>
			<tr>
				<th colspan="2">위치정보</th>
				<td><input type="text" name="goods_location_no" id="goods_location_no" value="<?php echo $data->goods_location_no;?>" style="width:300px;" />&nbsp;&nbsp;<span class="prd-desc">* 상품의 물류창고 위치값으로 노출되지 않는 관리용 정보입니다.</div></td>
			</tr>
			<tr>
				<th colspan="2">제조사</th>
				<td><input type="text" name="goods_company" id="goods_company" value="<?php echo $data->goods_company;?>" style="width:300px;" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="goods_company_display" id="goods_company_display" value="view" <?php echo ($tMode!='modify' || $data->goods_company_display=='view')?"checked=\"checked\"":"";?> /> 노출</td>
			</tr>
			<tr>
				<th colspan="2">원산지</th>
				<td><input type="text" name="goods_local" id="goods_local" value="<?php echo $data->goods_local;?>" style="width:300px;" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="goods_local_display" id="goods_local_display" value="view" <?php echo ($tMode!='modify' || $data->goods_local_display=='view')?"checked=\"checked\"":"";?> /> 노출</td>
			</tr>
			<!--  <tr>
				<th colspan="2">소비자가</th>
				<td>
					<!-- <input type="text" name="goods_consumer_price" id="goods_consumer_price" value="<?php echo $data->goods_consumer_price;?>" onkeydown="check_number();" style="width:100px;ime-mode:disabled;" /> 원
					<input type="checkbox" name="goods_cprice_display" id="goods_cprice_display" value="view" <?php echo ($tMode!='modify' || $data->goods_cprice_display =='view')?"checked=\"checked\"":"";?> /> 숨김
				</td>
			</tr>
			-->
			<!--
			<tr>
				<th <?php echo ($csCnt >'0')?"rowspan=\"3\"":"rowspan=\"2\"";?>>판매가</th>
				<th>판매가</th>
				<td>
					<div><input type="text" name="goods_price" id="goods_price" onkeydown="check_number();" value="<?php echo $data->goods_price;?>" style="width:100px;ime-mode:disabled;" /> 원 (미입력 또는 0원 일때 소비자가로 적용, 부가세 포함가로 실제 결제 금액을 입력)</div>
				</td>
			</tr>
			<tr>
				<th>부가세</th>
				<td>
					<input type="text" name="goods_tax" id="goods_tax" onkeydown="check_number();" value="<?php echo $data->goods_tax;?>" style="width:100px;ime-mode:disabled;" /> 원
					<input type="checkbox" name="goods_tax_display" id="goods_tax_display" value="view" <?php echo ($tMode!='modify' || $data->goods_tax_display =='view')?"checked=\"checked\"":"";?> /> 노출
				</td>
			</tr>
			 -->
			 
			<tr>
				<th colspan="2">부가세</th>
				<td>
					<input type="checkbox" name="goods_tax_display" id="goods_tax_display" value="view" <?php echo ($tMode!='modify' || $data->goods_tax_display =='view')?"checked=\"checked\"":"";?> /> 노출
				</td>
			</tr>
		<!-- <?php
		if($csCnt >'0'){
		?>
			<tr>
				<th colspan="2">특별회원가</th>
				<td>
				<?php
				if($data->goods_member_price) $memPrice=unserialize($data->goods_member_price);

				$cs_result = $wpdb->get_results("SELECT * FROM bbse_commerce_membership_class WHERE no>'2' AND use_sale='Y' ORDER BY no DESC");
				foreach($cs_result as $i => $csData){
					$tMemberPrice="";
					for($zk=0;$zk<sizeof($memPrice['goods_member_level']);$zk++){
						if($memPrice['goods_member_level'][$zk]==$csData->no) $tMemberPrice=$memPrice['goods_member_price'][$zk];
					}
				?>
					<div style="float:left;width:100px;"><?php echo stripslashes($csData->class_name);?><input type="hidden" name="goods_member_level[]" id="goods_member_level[]" value="<?php echo $csData->no;?>" style="width:100px;" /></div><div style="float:left;"><input type="text" name="goods_member_price[]" id="goods_member_price[]" value="<?php echo $tMemberPrice;?>" onkeydown="check_number();" style="width:100px;ime-mode:disabled;" /> 원 (미입력 또는 0원 일때 소비자가로 적용)</div>
					<div class="clearfix"></div>
				<?php
				}
				?>
				</td>
			</tr>
		<?php
		}
		?>
		-->
			<tr>
				<th colspan="2">재고설정</th>
				<td>
					<select name="goods_count_flag" id="goods_count_flag" onChange="goods_count_change();">
						<option value="unlimit" <?php echo (!$data->goods_count_flag || $data->goods_count_flag=='unlimit')?"selected='selected'":"";?>>무제한</option>
						<option value="goods_count" <?php echo ($data->goods_count_flag=='goods_count')?"selected='selected'":"";?>>재고수량</option>
						<option value="option_count" <?php echo ($data->goods_count_flag=='option_count')?"selected='selected'":"";?>>상품옵션별 재고로 설정</option>
					</select>
					&nbsp;<span id="goods_count_item" style="display:<?php echo ($data->goods_count_flag=='goods_count')?"inline":"none";?>;"><input type="text" name="goods_count" id="goods_count" value="<?php echo $data->goods_count;?>" onkeydown="check_number();" style="width:100px;ime-mode:disabled;" /> 개<span class="prd-desc" style="margin-left:15px;">* 단일상품(옵션이 없는 상품)의 재고설정 방법입니다. (상품옵션 사용 불가)</span></span>
				</td>
			</tr>
			<tr id="goods_count_view_info" style="display:<?php echo ($data->goods_count_flag=='goods_count' || $data->goods_count_flag=='option_count')?"table-row":"none";?>;">
				<th colspan="2">재고수량 노출</th>
				<td>
					<input type="radio" name="goods_count_view" id="goods_count_view" value="on" <?php echo (!$data->goods_count_view || $data->goods_count_view=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="goods_count_view" id="goods_count_view"  value="off" <?php echo ($data->goods_count_view=='off')?"checked='checked'":"";?> /> 사용안함
				</td>
			</tr>
			<tr>
				<th colspan="2">1회 구매 가능 개수</th>
				<td>
					<div><input type="text" name="goods_max_cnt" id="goods_max_cnt" onkeydown="check_number();" value="<?php echo $data->max_cnt;?>" style="width:100px;ime-mode:disabled;" />(0이거나 미입력 시 적용안됨)</div>
				</td>
			</tr>
			<tr>
				<th colspan="2">개별배송비 설정</th>
				<td>
					<input type="text" name="goods_ship_price" id="goods_ship_price" onkeydown="check_number();" value="<?php echo $data->goods_ship_price;?>" style="width:100px;ime-mode:disabled;" /> 원
					<label><input type="checkbox" name="goods_ship_tf" id="goods_ship_tf" value="view" <?php echo ($tMode!='modify' || $data->goods_ship_tf =='view')?"checked=\"checked\"":"";?> /> 개별배송비 적용</label>
				</td>
			</tr>
			<tr>
				<th colspan="2">외부링크 설정</th>
				<td>
					<input type="text" name="goods_external_link" id="goods_external_link" value="<?php echo $data->goods_external_link;?>" style="width:400px;" />
					<label><input type="checkbox" name="goods_external_link_tf" id="goods_external_link_tf" value="view" <?php echo ($tMode!='modify' || $data->goods_external_link_tf =='view')?"checked=\"checked\"":"";?> /> 외부링크 적용</label>
					<p style="font-size: 11px;color: rgb(245, 85, 85);margin: 5px 0 0;">
						외부링크 적용 시 상품목록에서 상품 선택 시 상품상세페이지로 이동하지 않고 입력한 외부링크로 이동됩니다.
					</p>
				</td>
			</tr>
			<tr>
				<th colspan="2">구매 별도문의 설정</th>
				<td>
					<input type="text" name="goods_buy_inquiry" id="goods_buy_inquiry" value="<?php echo $data->goods_buy_inquiry;?>" style="width:400px;" />
					<label><input type="checkbox" name="goods_buy_inquiry_tf" id="goods_buy_inquiry_tf" value="view" <?php echo ($tMode!='modify' || $data->goods_buy_inquiry_tf =='view')?"checked=\"checked\"":"";?> /> 구매 별도문의 적용</label>
					<p style="font-size: 11px;color: rgb(245, 85, 85);margin: 5px 0 0;">
						구매 별도문의적용 시 상품상세페이지에 구매하기 버튼이 노출되지 않고 구매 별도문의내용만 노출됩니다.
					</p>
				</td>
			</tr>
			<tr>
				<th colspan="2">추가항목 <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_plus.png" onClick="goods_add_field('add');" style="cursor:pointer;" align="absmiddle" />&nbsp;&nbsp;<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_minus.png" onClick="goods_add_field('remove');" style="cursor:pointer;" align="absmiddle" /></th>
				<td>
					<div id="goods_add_field">
				<?php 
				if($data->goods_add_field){
					$addField=unserialize($data->goods_add_field);
					for($a=0;$a<sizeof($addField['goods_add_field_title']);$a++){
				?>
						<div id="goods_add_<?php echo ($a+1);?>_field">
							<input type="text" style="width:25%;" name="goods_add_field_title[]" id="goods_add_field_title[]" onFocus="check_option_value(jQuery(this),'focus','항목명');" onBlur="check_option_value(jQuery(this),'blur','항목명');" value="<?php echo $addField['goods_add_field_title'][$a];?>" placeholder="항목명" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" style="width:70%;" name="goods_add_field_description[]" id="goods_add_field_description[]" onFocus="check_option_value(jQuery(this),'focus','내용');" onBlur="check_option_value(jQuery(this),'blur','내용');" value="<?php echo $addField['goods_add_field_description'][$a];?>" placeholder="내용" />
						</div>
				<?php
					}
				}
				else{
				?>
						<div id="goods_add_msg_field" class="prd-desc" style="margin-top:-7px;">* 추가항목 설정을 원하시는 경우 + 버튼을 클릭해 주세요. (최대 2개 설정 가능)</div>
				<?php
				}
				?>
					</div>
				</td>
			</tr>
		</table>
		<div class="clearfix"></div>

	<span id="option_setting" style="display:<?php echo ($data->goods_count_flag!='goods_count')?"block":"none";?>;">
		<div class="titleH5" style="margin:20px 0 10px 0; ">옵션설정</div>
		<?php
		if($data->goods_option_basic){
			$basicOpt=unserialize($data->goods_option_basic);
			$basicOptCnt=$wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$tData."'");    // 총 상품수
		}
		?>
		<table class="dataTbls overWhite collapse">
			<colgroup><col width="25%"><col width="25%"><col width="50%"></colgroup>
			<tr>
				<th>옵션값 개수</th><th>옵션명</th><th>옵션값</th>
			</tr>
			<tr>
				<td class="txtCenter">
					<select name="goods_option_1_count" id="goods_option_1_count" onChange="goods_option_item('goods_option_1','goods_option');">
						<option value="">선택</option>
						<?php 
						for($i=1;$i<21;$i++){
							if($i==$basicOpt['goods_option_1_count']) $opt_1_checked="selected='selected'";
							else $opt_1_checked="";
							echo "<option value=\"".$i."\" ".$opt_1_checked.">".$i."</option>";
						}
						?>
					</select>
				</td>
				<td class="txtCenter">
					<div id="goods_option_1_title_label">
					<?php if($basicOpt['goods_option_1_title']){?>
						<input type="text" name="goods_option_1_title" id="goods_option_1_title" value="<?php echo $basicOpt['goods_option_1_title'];?>" style="width:90%;" />
					<?php }?>
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_1_item_label">
					<?php 
					if(sizeof($basicOpt['goods_option_1_item'])>'0'){
						for($p=0;$p<sizeof($basicOpt['goods_option_1_item']);$p++){
							if($p>0) $strBr="<br />";
							else $strBr="";
					?>
						<span id="goods_option_1_<?php echo ($p+1);?>_item"><?php echo $strBr;?><input type="text" name="goods_option_1_item[]" id="goods_option_1_item[]" style="width:90%;" onchange="create_btn_on(true);" value="<?php echo $basicOpt['goods_option_1_item'][$p];?>" /></span>
					<?php 
						}
					}
					?>
					</div>
				</td>
			</tr>
			<tr>
				<td class="txtCenter">
					<select name="goods_option_2_count" id="goods_option_2_count" onChange="goods_option_item('goods_option_2','goods_option');">
						<option value="">선택</option>
						<?php 
						for($i=1;$i<21;$i++){
							if($i==$basicOpt['goods_option_2_count']) $opt_2_checked="selected='selected'";
							else $opt_2_checked="";

							echo "<option value=\"".$i."\" ".$opt_2_checked.">".$i."</option>";
						}
						?>
					</select>
				</td>
				<td class="txtCenter">
					<div id="goods_option_2_title_label">
					<?php if($basicOpt['goods_option_2_title']){?>
						<input type="text" name="goods_option_2_title" id="goods_option_2_title" value="<?php echo $basicOpt['goods_option_2_title'];?>" style="width:90%;" />
					<?php }?>
					</div>
				</td>
				<td class="txtCenter">
					<div id="goods_option_2_item_label">
					<?php 
					if(sizeof($basicOpt['goods_option_2_item'])>'0'){
						for($p2=0;$p2<sizeof($basicOpt['goods_option_2_item']);$p2++){
							if($p2>0) $strBr="<br />";
							else $strBr="";
					?>
						<span id="goods_option_2_<?php echo ($p2+1);?>_item"><?php echo $strBr;?><input type="text" name="goods_option_2_item[]" id="goods_option_2_item[]" style="width:90%;" onchange="create_btn_on(true);" value="<?php echo $basicOpt['goods_option_2_item'][$p2];?>" /></span>
					<?php 
						}
					}
					?>
					</div>
				</td>
			</tr>
		</table>
		<div class="clearfix"></div>
		<div class="prd-desc" style="float:left;">* 옵션저장 후 옵션값 개수 및 옵션값 변경 시 옵션리스트는 초기화 됩니다.</div>
		<button type="button" class="button-bbse blue" id="btn_good_option" onClick="create_goods_option('goods_option');" style="float:right;margin-top:2px;"> 상품 옵션저장 </button>
		<a name="flag_goods_option_table"></a>
		<div id="goods_option_table" style="display:<?php echo ($basicOptCnt>'0')?"block":"none";?>;">
		<?php
		if($basicOptCnt>'0'){
		?>
			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>
			<table id="goods_option_table_list" class="dataTbls collapse">
				<colgroup><col width="25%"><col width="13%"><col width="9%"><col width="20%"><col width="8%"><col width="8%"></colgroup>
				<tr>
					<th>옵션값</th><th>추가가격</th><th>재고수량</th><th>고유번호</th><th>노출</th><th>품절</th>
					<!-- <th>옵션값</th><th>추가가격</th><th>재고수량</th><th>상품코드</th><th>더존코드</th><th>노출</th><th>품절</th> -->
				</tr>
			<?php
			$basicOptResult=$wpdb->get_results("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$tData."' ORDER BY goods_option_item_rank ASC");    // 총 상품수
			foreach($basicOptResult as $z=>$basicOptData) {
				$style_color=$countDisabled=$countStyle=$checkedDisabled=$checkedStyle="";

				if($data->goods_count_flag=='option_count'){
					if(!$basicOptData->goods_option_item_count || $basicOptData->goods_option_item_count<=0){
						$style_color="color:#ED1C24;";
					}
				}
				else{
					$countDisabled=$checkedDisabled="disabled";
					$countStyle="background:#f4f4f4;color:#a2a2a2;";

					$checkedStyle="style=\"background:#dfdfdf;\"";
				}
			?>
				<tr>
					<td style="text-align:center;">
						<span style="text-decoration:none;<?php echo $style_color;?>"><?php echo $basicOptData->goods_option_title;?></span>
						<input type="hidden" name="goods_option_title[]" id="goods_option_title[]" value="<?php echo $basicOptData->goods_option_title;?>" />
					</td>
					<td style="text-align:center;">
						<input type="text" name="goods_option_item_overprice[]" id="goods_option_item_overprice[]" onkeydown="check_number();" value="<?php echo $basicOptData->goods_option_item_overprice;?>" style="width:90%;text-align:center;ime-mode:disabled;" onFocus="check_option_value(jQuery(this),'focus','추가가격');" onBlur="check_option_value(jQuery(this),'blur','추가가격');" placeholder="추가가격" />
					</td>
					<td style="text-align:center;">
						<input type="text" name="goods_option_item_count[]" id="goods_option_item_count[]" onkeydown="check_number();" value="<?php echo $basicOptData->goods_option_item_count;?>" style="<?php echo $style_color.$countStyle;?>width:90%;;text-align:center;ime-mode:disabled;" onFocus="check_option_value(jQuery(this),'focus','재고수량');" onBlur="check_option_value(jQuery(this),'blur','재고수량');" placeholder="재고수량" <?php echo $countDisabled;?> />
					</td>
					<!-- <td style="text-align:center;">
						<input type="text" name="goods_option_item_unique_code[]" id="goods_option_item_unique_code[]" value="<?php echo $basicOptData->goods_option_item_unique_code;?>" style="width:50%;text-align:center;" onFocus="check_option_value(jQuery(this),'focus','상품코드');" onBlur="check_option_value(jQuery(this),'blur','상품코드');"  placeholder="상품코드"  maxlength="5" /><button type="button" class="button-small gray" style="float:right;" onclick="checkGoodsCode('option');">중복확인</button>
					</td>
					 -->
					 <td style="text-align:center;">
						<input type="text" name="goods_option_item_unique_code[]" id="goods_option_item_unique_code[]" value="<?php echo $basicOptData->goods_option_item_unique_code;?>" style="width:50%;text-align:center;" onFocus="check_option_value(jQuery(this),'focus','고유번호');" onBlur="check_option_value(jQuery(this),'blur','고유번호');"  placeholder="고유번호"/>
					</td>
					<!-- <td style="text-align:center;">
						<?php $uniDzCodes = $wpdb->get_results("select * from tbl_douzone_code where goods_code = '".$basicOptData->goods_option_item_unique_code."'");
						foreach ($uniDzCodes as $z=>$uniDzCode){
						?>
						<input type="text" name="goods_option_item_douzone_code[]" id="goods_option_item_douzone_code[]" value="<?php echo $uniDzCode->goods_douzone_code;?>" style="width:50%;text-align:center;" onFocus="check_option_value(jQuery(this),'focus','더존코드');" onBlur="check_option_value(jQuery(this),'blur','더존코드');"  placeholder="더존코드" />
						<?php 
						}
						?>
					</td>
					 -->
					<td style="text-align:center;">
						<input type="checkbox" name="goods_option_item_temp_display[]" id="goods_option_item_temp_display[]" value="view" onClick="change_item_check('goods_option','display','view',<?php echo $z;?>);" <?php echo ($basicOptData->goods_option_item_display=='view')?"checked='checked'":"";?> <?php echo $checkedStyle;?> <?php echo $checkedDisabled;?> />
						<input type="hidden" name="goods_option_item_display[]" id="goods_option_item_display[]" value="<?php echo $basicOptData->goods_option_item_display;?>" />
					</td>
					<td style="text-align:center;">
						<input type="checkbox" name="goods_option_item_temp_soldout[]" id="goods_option_item_temp_soldout[]" value="soldout" onClick="change_item_check('goods_option','soldout','soldout',<?php echo $z;?>);"  <?php echo ($basicOptData->goods_option_item_soldout=='soldout')?"checked='checked'":"";?> <?php echo $checkedStyle;?> <?php echo $checkedDisabled;?> />
						<input type="hidden" name="goods_option_item_soldout[]" id="goods_option_item_soldout[]" value="<?php echo $basicOptData->goods_option_item_soldout;?>" />
					</td>
				</tr>
			<?php
			}
			?>
			</table>
		<?php
		}
		?>
		</div>
	</span>

		<?php 
		if($data->goods_option_add) $addOpt=unserialize($data->goods_option_add);

		if($addOpt['goods_add_option_count']>'0') $addOptCnt=$addOpt['goods_add_option_count'];
		else $addOptCnt='0';
		?>

		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0; ">추가 옵션설정<div style="float:right;"><button type="button"class="button-small blue" onClick="goods_add_option('add');" style="height:25px;">추가</button>&nbsp;&nbsp;<button type="button"class="button-small red" onClick="goods_add_option('remove');" style="height:25px;">삭제</button><input type="hidden" name="goods_add_option_count" id="goods_add_option_count" value="<?php echo $addOptCnt;?>" /></div></div>
		<div class="clearfix"></div>

		<div id="goods_add_option">
		<?php
		if($addOptCnt>0){
			for($c=1;$c<=$addOptCnt;$c++){
				$itemCnt=sizeof($addOpt['goods_add_'.$c.'_item']);
		?>
			<div id="goods_add_<?php echo $c;?>_option">
				<div class="clearfix"></div>
				<div class="clearfix" style="height:10px;"></div>
				<table class="dataTbls overWhite collapse">
					<colgroup><col width="24%"><col width=""></colgroup>
					<tr>
						<th colspan="2">추가 옵션  (<?php echo $c;?>)</th>
					</tr>
					<tr>
						<th>사용여부</th>
						<td><input type="radio" name="goods_add_<?php echo $c;?>_use" id="goods_add_<?php echo $c;?>_use" value="on" <?php echo ($addOpt['goods_add_'.$c.'_use']=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="goods_add_<?php echo $c;?>_use" id="goods_add_<?php echo $c;?>_use" value="off" <?php echo ($addOpt['goods_add_'.$c.'_use']=='off')?"checked='checked'":"";?> /> 사용안함</td>
					</tr>
					<tr>
						<th>선택/필수 적용</th>
						<td><input type="radio" name="goods_add_<?php echo $c;?>_choice" id="goods_add_<?php echo $c;?>_choice" value="selection" <?php echo ($addOpt['goods_add_'.$c.'_choice']=='selection')?"checked='checked'":"";?> /> 선택항목&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="goods_add_<?php echo $c;?>_choice" id="goods_add_<?php echo $c;?>_choice" value="required" <?php echo ($addOpt['goods_add_'.$c.'_choice']=='required')?"checked='checked'":"";?> /> 필수항목</td>
					</tr>
				</table>
				<div class="clearfix" style="height:10px;"></div>
				<table class="dataTbls overWhite collapse">
					<colgroup><col width="15%"><col width="15%"><col width="26%"><col width="14%"><col width="14%"><col width="8%"><col width="8%"></colgroup>
					<tr>
						<th>옵션값 개수</th><th>옵션명</th><th>옵션값</th><th>가격</th><th>상품코드</th><th>노출</th><th>품절</th>
					</tr>
					<tr>
						<td class="txtCenter">
							<select name="goods_add_<?php echo $c;?>_item_count" id="goods_add_<?php echo $c;?>_item_count" onChange="goods_add_option_item('goods_add_<?php echo $c;?>');">
							<option value="">선택</option>
							<?php 
							for($i=1;$i<21;$i++){
								if($i==$addOpt['goods_add_'.$c.'_item_count']) $addOptSelected="selected='selected'";
								else $addOptSelected="";

								echo "<option value=\"".$i."\" ".$addOptSelected.">".$i."</option>";
							}
							?>
							</select>
						</td>
						<td class="txtCenter">
							<div id="goods_add_<?php echo $c;?>_title_label"><input type="text" name="goods_add_<?php echo $c;?>_title" id="goods_add_<?php echo $c;?>_title" value="<?php echo $addOpt['goods_add_'.$c.'_title'];?>" style="width:90%;" /></div>
						</td>
						<td class="txtCenter">
							<div id="goods_add_<?php echo $c;?>_item_label">
								<?php
								for($q=0;$q<$itemCnt;$q++){
									if($q>0) $strBr="<br />";
									else $strBr="";
								?>
									<span id="goods_add_<?php echo $c;?>_<?php echo ($q+1);?>_item"><?php echo $strBr;?><input type="text" name="goods_add_<?php echo $c;?>_item[]" id="goods_add_<?php echo $c;?>_item[]" value="<?php echo $addOpt['goods_add_'.$c.'_item'][$q];?>" style="width:90%;" /></span>
								<?php
								}
								?>
							</div>
						</td>
						<td class="txtCenter">
							<div id="goods_add_<?php echo $c;?>_overprice_label">
								<?php
								for($q2=0;$q2<$itemCnt;$q2++){
									if($q2>0) $strBr="<br />";
									else $strBr="";
								?>
									<span id="goods_add_<?php echo $c;?>_<?php echo ($q2+1);?>_overprice"><?php echo $strBr;?><input type="text" style="width:90%;text-align:center;ime-mode:disabled;" name="goods_add_<?php echo $c;?>_item_overprice[]" id="goods_add_<?php echo $c;?>_item_overprice[]" onkeydown="check_number();" onFocus="check_option_value(jQuery(this),'focus','가격');" onBlur="check_option_value(jQuery(this),'blur','가격');" value="<?php echo $addOpt['goods_add_'.$c.'_item_overprice'][$q2];?>" placeholder="가격" /></span>
								<?php
								}
								?>							
							</div>
						</td>
						<td class="txtCenter">
							<div id="goods_add_<?php echo $c;?>_unique_label">
								<?php
								for($q3=0;$q3<$itemCnt;$q3++){
									if($q3>0) $strBr="<br />";
									else $strBr="";
								?>
									<span id="goods_add_<?php echo $c;?>_<?php echo ($q3+1);?>_unique"><?php echo $strBr;?><input type="text" style="width:90%;text-align:center;ime-mode:disabled;" name="goods_add_<?php echo $c;?>_item_unique_code[]" id="goods_add_<?php echo $c;?>_item_unique_code[]" onFocus="check_option_value(jQuery(this),'focus','상품코드');" onBlur="check_option_value(jQuery(this),'blur','상품코드');"  value="<?php echo $addOpt['goods_add_'.$c.'_item_unique_code'][$q3];?>" placeholder="상품코드" /></span>
									
								<?php
								}
								?>		
							</div>
						</td>
						<td class="txtCenter">
							<div id="goods_add_<?php echo $c;?>_display_label">
								<?php
								for($q4=0;$q4<$itemCnt;$q4++){
									if($q4>0) $strBr="<br />";
									else $strBr="";
								?>
									<span id="goods_add_<?php echo $c;?>_<?php echo ($q4+1);?>_display"><?php echo $strBr;?><input type="checkbox" name="goods_add_<?php echo $c;?>_item_temp_display[]" id="goods_add_<?php echo $c;?>_item_temp_display[]" value="view" onClick="change_item_check('goods_add_<?php echo $c;?>','display','view','<?php echo $q4;?>');" <?php echo ($addOpt['goods_add_'.$c.'_item_display'][$q4]=='view')?"checked='checked'":"";?> /><input type="hidden" name="goods_add_<?php echo $c;?>_item_display[]" id="goods_add_<?php echo $c;?>_item_display[]" value="<?php echo $addOpt['goods_add_'.$c.'_item_display'][$q4];?>" /></span>
								<?php
								}
								?>		
							</div>
						</td>
						<td class="txtCenter">
							<div id="goods_add_<?php echo $c;?>_soldout_label">
								<?php
								for($q5=0;$q5<$itemCnt;$q5++){
									if($q5>0) $strBr="<br />";
									else $strBr="";
								?>
									<span id="goods_add_<?php echo $c;?>_<?php echo ($q5+1);?>_soldout"><?php echo $strBr;?><input type="checkbox" name="goods_add_<?php echo $c;?>_item_temp_soldout[]" id="goods_add_<?php echo $c;?>_item_temp_soldout[]" value="soldout" onClick="change_item_check('goods_add_<?php echo $c;?>','soldout','soldout','<?php echo $q5;?>');" <?php echo ($addOpt['goods_add_'.$c.'_item_soldout'][$q5]=='soldout')?"checked='checked'":"";?> /><input type="hidden" name="goods_add_<?php echo $c;?>_item_soldout[]" id="goods_add_<?php echo $c;?>_item_soldout[]" value="<?php echo $addOpt['goods_add_'.$c.'_item_soldout'][$q5];?>" /></span>
								<?php
								}
								?>								
							</div>
						</td>
					</tr>
				</table>
			</div>

		<?php
			}
		}
		else{
		?>
			<div id="goods_add_msg_option">
				<table class="dataTbls overWhite collapse">
					<colgroup><col width=""></colgroup>
					<tr>
						<td style="text-align:center;"><div class="prd-desc" style="margin-bottom:10px;">* 추가 옵션설정을 원하시는 경우 추가 버튼을 클릭해 주세요.</div></td>
					</tr>
				</table>
			</div>
		<?php
		}
		?>
		</div>
		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0; ">연계상품 설정</div>

		<table class="dataTbls overWhite collapse">
			<colgroup><col width="24%"><col width=""></colgroup>
			<tr>
				<th>추천상품</th>
				<td>
					<div><input type="radio" name="goods_recommend_use" onClick="goods_link_fields_onoff('recommend',this.value);" value="on" <?php echo ($data->goods_recommend_use=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="goods_recommend_use" onClick="goods_link_fields_onoff('recommend',this.value);" value="off" <?php echo ($tMode!='modify' || !$data->goods_recommend_use || $data->goods_recommend_use=='off')?"checked='checked'":"";?> /> 사용안함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="goods_recommend_use_item" style="display:<?php echo ($data->goods_recommend_use=='on')?"inline":"none";?>;"><button type="button"class="button-small red" onClick="goods_list_popup('recommend');" style="height:25px;">상품추가</button> (최대 4개 설정 가능)</span></div>
					<div id="goods-recommend" class="goods-gallery" style="display:<?php echo ($data->goods_recommend_use=='on')?"block":"none";?>;">
						<ul id="goods-recommend-ul-list">
					<?php
					if($data->goods_recommend_list){

						$recList=explode(",",$data->goods_recommend_list);
						for($r=0;$r<sizeof($recList);$r++){
							$rData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$recList[$r]."'");
							if($rData->goods_name){
								if($rData->goods_basic_img) $basicImg = wp_get_attachment_image_src($rData->goods_basic_img);
								else{
									$imageList=explode(",",$rData->goods_add_img);
									if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
									else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
								}
					?>
							<li id="goods_img_recommend_list_<?php echo $rData->idx;?>">
								<table style="margin-left:14px;padding:0px;">
									<tr>
										<td style="border-bottom:0px;padding:0px;">
											<div class="thumb">
												<img src="<?php echo $basicImg['0'];?>" alt="상품이미지" />
											</div>
										</td>
										<td style="border-bottom:0px;padding:0px;">
											<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png" onClick="goods_img_link_remove('recommend','<?php echo $rData->idx;?>')" class="deleteBtn" alt="추천상품에서 삭제" title="추천상품에서 삭제" />
										</td>
									</tr>
								</table>
								<div class="goodsname"><?php echo $rData->goods_name;?><input type="hidden" name="goods_recommend_list[]" value="<?php echo $rData->idx;?>" /></div>
							</li>
					<?php
							}
						}
					}
					?>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<th>관련상품</th>
				<td>
					<div><input type="radio" name="goods_relation_use" onClick="goods_link_fields_onoff('relation',this.value);" value="on" <?php echo ($data->goods_relation_use=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="goods_relation_use" onClick="goods_link_fields_onoff('relation',this.value);" value="off" <?php echo ($tMode!='modify' || !$data->goods_relation_use || $data->goods_relation_use=='off')?"checked='checked'":"";?> /> 사용안함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="goods_relation_use_item" style="display:<?php echo ($data->goods_relation_use=='on')?"inline":"none";?>;"><button type="button"class="button-small red" onClick="goods_list_popup('relation');" style="height:25px;">상품추가</button> (최대 4개 설정 가능)</span></div>
					<div id="goods-relation" class="goods-gallery" style="display: <?php echo ($data->goods_relation_use=='on')?"block":"none";?>;">
						<ul id="goods-relation-ul-list">
					<?php
					if($data->goods_relation_list){
						$relList=explode(",",$data->goods_relation_list);
						for($t=0;$t<sizeof($relList);$t++){
							$tData=$wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$relList[$t]."'");
							if($tData->goods_name){
								if($tData->goods_basic_img) $basicImg = wp_get_attachment_image_src($tData->goods_basic_img);
								else{
									$imageList=explode(",",$tData->goods_add_img);
									if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
									else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
								}
					?>
							<li id="goods_img_relation_list_<?php echo $tData->idx;?>">
								<table style="margin-left:14px;padding:0px;">
									<tr>
										<td style="border-bottom:0px;padding:0px;">
											<div class="thumb">
												<img src="<?php echo $basicImg['0'];?>" alt="상품이미지" />
											</div>
										</td>
										<td style="border-bottom:0px;padding:0px;">
											<img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png" onClick="goods_img_link_remove('relation','<?php echo $tData->idx;?>')" class="deleteBtn" alt="관련상품에서 삭제" title="관련상품에서 삭제" />
										</td>
									</tr>
								</table>
								<div class="goodsname"><?php echo $tData->goods_name;?><input type="hidden" name="goods_relation_list[]" value="<?php echo $tData->idx;?>" /></div>
							</li>
					<?php
							}
						}
					}
					?>
						</ul>
					</div>
				</td>
			</tr>
		</table>

		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0; ">상품별 SEO설정</div>

		<table class="dataTbls overWhite collapse">
			<colgroup><col width="24%"><col width=""></colgroup>
			<tr>
				<th>사용여부</th>
				<td><input type="radio" name="goods_seo_use" id="goods_seo_use" onClick="goods_seo_click(this.value);" value="on" <?php echo ($data->goods_seo_use=='on')?"checked='checked'":"";?> /> 사용함&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="goods_seo_use" id="goods_seo_use" onClick="goods_seo_click(this.value);" value="off" <?php echo ($tMode!='modify' || $data->goods_seo_use=='off')?"checked='checked'":"";?> /> 사용안함</td>
			</tr>
			<tr id="seo_title" <?php echo ($data->goods_seo_use=='on')?"":"style='display:none;'";?>>
				<th>타이틀</th>
				<td><input type="text" name="goods_seo_title" id="goods_seo_title" value="<?php echo $data->goods_seo_title;?>" style="width:100%;" /></td>
			</tr>
			<tr id="seo_description" <?php echo ($data->goods_seo_use=='on')?"":"style='display:none;'";?>>
				<th>설명</th>
				<td><input type="text" name="goods_seo_description" id="goods_seo_description" value="<?php echo $data->goods_seo_description;?>" style="width:100%;" /></td>
			</tr>
			<tr id="seo_keyword" <?php echo ($data->goods_seo_use=='on')?"":"style='display:none;'";?>>
				<th>키워드</th>
				<td><input type="text" name="goods_seo_keyword" id="goods_seo_keyword" value="<?php echo $data->goods_seo_keyword;?>" style="width:100%;" /></td>
			</tr>
		</table>

		<div class="clearfix"></div>
		<div class="titleH5" style="margin:20px 0 10px 0; ">상품별 적립금 설정</div>

		<table class="dataTbls overWhite collapse">
			<colgroup><col width="24%"><col width=""></colgroup>
			<tr>
				<th>개별 적립금</th>
				<td>
					<input type="radio" name="goods_earn_use" id="goods_earn_use" onClick="goods_fields_onoff(this.id,this.value);" value="on" <?php echo ($data->goods_earn_use=='on')?"checked='checked'":"";?> /> 사용&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="goods_earn_use" id="goods_earn_use" onClick="goods_fields_onoff(this.id,this.value);" value="off" <?php echo ($tMode!='modify' || $data->goods_earn_use=='off')?"checked='checked'":"";?> /> 사용안함
					<span id="goods_earn_use_item" style="display:<?php echo ($data->goods_earn_use=='on')?"inline":"none";?>;"><br />개별 적립금 <input type="text" name="goods_earn" id="goods_earn" value="<?php echo $data->goods_earn;?>" onkeydown="check_number();" style="width:100px;ime-mode:disabled;" /> 원 지급</span>
				</td>
			</tr>
		</table>

		<div style="margin:40px 0;text-align:center;">
			<button type="button" class="button-bbse blue" onClick="goods_submit('<?php echo ($tMode=='modify')?"modify":"insert";?>');" style="width:20%;"> <?php echo ($tMode=='modify')?"수정":"등록";?>/저장 </button>&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" class="button-bbse" onClick="go_goods_page('list');" style="width:20%;"> 목록보기 </button>
		</div>
	</div>

	<div style="padding-left:3%;float:left;width:32%;">
		<table class="dataTbls overWhite collapse">
			<colgroup><col width=""></colgroup>
			<tr>
				<th style="font-size:14px;text-align:center;">노출여부</th>
			</tr>
			<tr>
				<td>
					<div style="margin:20px 0;">
						<select name="goods_display" id="goods_display" style="width:100%;height:29px;line-height:29px;">
							<option value="display" <?php echo ($data->goods_display=='display')?"selected='selected'":"";?>>노출</option>
							<option value="hidden" <?php echo ($data->goods_display=='hidden')?"selected='selected'":"";?>>비노출</option>
							<option value="soldout" <?php echo ($data->goods_display=='soldout')?"selected='selected'":"";?>>노출품절</option>
						<?php if($data->goods_display=='copy'){?>
							<option value="copy" <?php echo ($data->goods_display=='copy')?"selected='selected'":"";?>>복사</option>
						<?php }?>
						<?php if($data->goods_display=='trash'){?>
							<option value="trash" <?php echo ($data->goods_display=='trash')?"selected='selected'":"";?>>휴지통</option>
						<?php }?>
						</select>
					</div>
					<div style="margin-bottom:20px;text-align:center;">
						<button type="button" class="button-bbse blue" onClick="goods_submit('<?php echo ($tMode=='modify')?"modify":"insert";?>');" style="width:30%;"> <?php echo ($tMode=='modify')?"수정":"등록";?>/저장 </button>&nbsp;&nbsp;<button type="button" class="button-bbse" onClick="go_goods_page('list');" style="width:30%;"> 목록보기 </button>
					</div>
				</td>
			</tr>
		</table>

<!-- 		<div class="clearfix" style="height:20px;"></div>

		<table class="dataTbls overWhite collapse">
			<colgroup><col width=""></colgroup>
			<tr>
				<th style="font-size:14px;text-align:center;">카테고리</th>
			</tr>
			<tr>
				<td style="margin:0;padding:0;">
					<div class="cat-top">
						<div class="cat-margin">
							<ol class="cat-list">
								<?php echo bbse_commerce_get_category_markup_for_goods($data->goods_cat_list);?>
							</ol>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td style="height:30px;line-height:15px;">
					<div class="prd-desc">* '상품카테고리관리' 메뉴에서 카테고리의 등록, 수정, 삭제, 순서 설정이 가능합니다.<br>&nbsp;&nbsp;&nbsp;&nbsp;붉은색 및 <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_none_display.png" align="absmiddle" title="표시하지 않는 카테고리" /> 아이콘은 사용자 화면에 표시하지 않는 카테고리 입니다.</div>
				</td>
			</tr>
		</table>
 -->

		<div class="clearfix" style="height:20px;"></div>

		<table class="dataTbls overWhite collapse">
			<colgroup><col width=""></colgroup>
			<tr>
				<th style="font-size:14px;text-align:center;">이미지 설정</th>
			</tr>
			<tr>
				<td>
					<div class="goods-gallery-right">
						<ul id="goods_img_add_list">
				<?php
					$iCnt_new='0';

					if($data->goods_add_img){
						$addImg=explode(",",$data->goods_add_img);
						for($d=0;$d<sizeof($addImg);$d++){
							$basicImg_checked="";
							$iCnt_new=$d+1;

							if($addImg[$d]){
								$prdImg=wp_get_attachment_image_src($addImg[$d]);
								$prdImgZoom=wp_get_attachment_image_src($addImg[$d], "goodsimage4");
							}
							else{
								$prdImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
								$prdImgZoom['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
							}

							if(!$data->goods_basic_img){
								if($d=='0') $basicImg_checked="checked='checked'";
							}
							elseif($data->goods_basic_img==$addImg[$d]) $basicImg_checked="checked='checked'";
				?>
							<li id="goods_img_add_list_<?php echo $iCnt_new;?>">
								<table style="margin-left:14px;padding:0px;">
									<tr>
										<td class="tbClear">
											<div class="thumb">
											<input type="hidden" name="goods_add_img[]" value="<?php echo $addImg[$d];?>" />
											<img src="<?php echo $prdImg['0'];?>" id="goods_add_img_list_<?php echo $iCnt_new;?>" alt="상품이미지" />
											<span class="bg"></span>
											<span class="btn">
											<a href="<?php echo $prdImgZoom['0'];?>" data-lightbox="list-set" data-title="상품이미지 <?php echo $iCnt_new;?>" class="zoom"><span>이미지보기</span></a>
											</span>
											</div>
										</td>
										<td style="border-bottom:0px;padding:0px;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png" onClick="goods_img_remove('<?php echo $iCnt_new;?>');" class="deleteBtn" alt="이미지 제거" title="이미지 제거" /></td>
									</tr>
									<tr>
										<td class="tbClear" style="padding-left:-25px;"><input type="radio" name="goods_basic_img" id="goods_basic_img" onClick="goods_img_basic_check('<?php echo $iCnt_new;?>');" value="<?php echo $addImg[$d];?>" <?php echo $basicImg_checked;?> /></td>
										<td class="tbClear"></td>
									</tr>
								</table>
							</li>
				<?php
						}
					}
				?>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<input type="hidden" name="goods_add_img_count" id="goods_add_img_count" value="<?php echo $iCnt_new;?>" />
					<button type="button" class="button-small blue" id="goods_add_img_upload" style="height:25px;">+ 이미지 추가</button>
					 <div class="prd-desc">* 상품이미지는 직접 업로드한 이미지를 사용 해 주세요. (URL 에서 등록 사용불가)</div>
					 <div class="prd-desc">* 대표이미를 선택하지 않은 경우 첫번째 이미지를 대표이미지로 사용합니다.</div>
					 <div class="prd-desc" style="color:#303030;">[테마명 : <?php echo BBSE_THEME_NAME;?>]<br />- 상품이미지는 <?php echo (strtolower(str_replace(" ","",BBSE_THEME_NAME))=='blogshop')?"가로x세로 2 : 3 비율의 이미지를 사용해 주세요. (가로 최소 520px 이상 권장)":"1 : 1 비율의 정사각형 이미지를 사용해 주세요. (가로 최소 420px 이상 권장)";?></div>
				</td>
			</tr>
		</table>
	</div>

	</form>
</div>


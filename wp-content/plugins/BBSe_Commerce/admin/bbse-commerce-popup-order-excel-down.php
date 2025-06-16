<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$s_period_1=$_REQUEST['s_period_1'];
$s_period_2=$_REQUEST['s_period_2'];
$s_type=$_REQUEST['s_type'];
$s_keyword=$_REQUEST['s_keyword'];
$s_list=$_REQUEST['s_list'];
$s_payMode=$_REQUEST['s_payMode'];

$sOption="";

if($s_list){
	if($s_list!='all') $sOption .=" AND order_status='".$s_list."'";
}

if($s_payMode){
    if($s_payMode!='all') $sOption .=" AND payMode='".$s_payMode."'";
}

if($s_period_1){
	$tmp_1_priod=explode("-",$s_period_1);
	$s_period_1_time=mktime('00','00','00',$tmp_1_priod['1'],$tmp_1_priod['2'],$tmp_1_priod['0']);
	$sOption .=" AND order_date>='".$s_period_1_time."'";
}

if($s_period_2){
	$tmp_2_priod=explode("-",$s_period_2);
	$s_period_2_time=mktime('23','59','59',$tmp_2_priod['1'],$tmp_2_priod['2'],$tmp_2_priod['0']);
	$sOption .=" AND order_date<='".$s_period_2_time."'";
}

if($s_keyword){
	if($s_type){
		$sOption .=" AND ".str_replace("O.","",$s_type)." LIKE %s";
		$prepareParm[]="%".like_escape($s_keyword)."%";
	}
	else{
		$sOption .=" AND (order_no LIKE %s OR order_name LIKE %s OR receive_name LIKE %s OR input_name LIKE %s OR user_id LIKE %s)";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
		$prepareParm[]="%".like_escape($s_keyword)."%";
	}
}

$prepareParm[]=$start_pos;
$prepareParm[]=$per_page;

$s_total_sql  = $wpdb->prepare("SELECT count(idx) FROM bbse_commerce_order WHERE idx<>''".$sOption, $prepareParm);
$total = $wpdb->get_var($s_total_sql);    // 총 상품수

$fields=Array("order_no"=>"주문번호","order_status"=>"주문상태","pay_how"=>"결제방법","use_earn"=>"적립금사용","cost_total"=>"결제금액","order_name"=>"주문자명","order_phone"=>"주문자 연락처","order_hp"=>"주문자 핸드폰","receive_name"=>"받으실분","order_addr"=>"배송주소","receive_phone"=>"연락처","receive_hp"=>"핸드폰","order_comment"=>"남기실말씀","order_detail"=>"주문상품","order_date"=>"주문일자","delivery_no"=>"송장번호");
$sType=Array("order_no"=>"주문번호","order_name"=>"주문자명","receive_name"=>"수령자명","input_name"=>"입금자명");

?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.form.js'></script>
</head>
<body>

<div class="wrap" style="font-size:12px;">
	<div class="tabWrap" style="margin: 15px 0 0 0;">
	  <ul class="tabList">
		<li id="tab_Order" class="active" style="width:250px;"><a href="javascript:showContainer('Order');">기본양식(주문내역 다운로드)</a></li>
		<li id="tab_Delivery" style="width:250px;"><a href="javascript:showContainer('Delivery');">배송(송장)등록 엑셀파일 다운로드</a></li>
	  </ul>
	  <div class="clearfix"></div>
	</div>


	<div id="excelOrder">
		<ul>
			<li>엑셀로 다운받고자 하는 컬럼에 체크하고 확인 버튼을 누르세요.
			<li>자료가 많을 경우 오랜 시간이 걸릴수도 있습니다.
		</ul>

		<div class="borderBox" style="margin:0 10px;padding:10px 10px;">
			<table width="97%" border="0" align="center" cellspacing="0" cellpadding="3">
				<colgroup><col width="28%"><col width="43%"><col width="29%"></colgroup>
				<tr>
					<td>주문상태 : <?php echo (!$s_list)?"전체":$orderStatus[$s_list];?></td>
					<td>검색기간 : <?php 
										if($s_period_1 && $s_period_2) echo $s_period_1." ~ ".$s_period_2;
										elseif($s_period_1) echo $s_period_1." ~ ";
										elseif($s_period_2) echo " ~ ".$s_period_2;
										else echo "-";
										?></td>
					<td>검색어 : <?php echo $sType[str_replace("O.","",$s_type)];?> <?php echo ($s_keyword)?$s_keyword:"-";?></td>
				</tr>
			</table>
		</div>

		<br />
		<div id="container_1" style="margin:0 10px;">
			<form name="excel_select_field_form" id="excel_select_field_form" method="post" action="./proc/bbse-commerce-order-excel-down.exec.php">
			<div style="margin-top:20px;height:30px;">
				<input type="checkbox" name="select" id="select_id" onclick="selectAll(this, 'selFieldName[]')" checked="checked" />
				<label for="select_id">전체 선택/해제</label>
				<div style="float:right;">검색한 자료 수 : <font color="red"><?php echo $total; ?></font>개</div>
			</div>
			<table class="dataTbls normal-line-height collapse">
			<?php
			$kCnt=0;
			foreach ($fields as $e_key => $e_val){
				if($kCnt%4 == 0) echo $e_key == 0 ? "<tr>" : "</tr><tr>";
			?>
				<td class="nowrap">
					<input type="checkbox" name="selFieldName[]" onClick="change_sort();" value="<?php echo $e_key; ?>" id="column<?php echo $e_key; ?>" checked="checked" />
					<label for="column<?php echo $e_key; ?>"><?php echo $e_val; ?></label>
				</td>
			<?php
				$kCnt++;
			}

			$fieldn = sizeof($fields) %4;
			if($fieldn != 0){
				for($k=4; $k>$fieldn; $k--){
					echo "<td></td>";
				}
				echo "</tr>";
			}
			?>
			</table>
			<br />
			<br />
			<table width="100%" border="0">
			<tr>
				<td>
					<div style="height:30px;">정렬 조건을 지정할 경우 엑셀로 다운받는데 오랜 시간이 걸릴 수 있습니다.</div>
					<table>
					<tr>
						<td height="25">정렬 조건 1.</td>
						<td>
							<span id="orderCondition1_list">
								<select name="orderCondition1">
								<option value="">없음</option>
								<?php
								foreach ($fields as $e_key => $e_val){
									echo "<option value='".$e_key."'>".$e_val."</option>";
								}
								?>
								</select>
							</span>
						</td>
						<td>
							<select name="order_orderCondition1">
							<option value="">오름차순
							<option value=" DESC">내림차순
							</select>
						</td>
					</tr>
					<tr>
						<td height="25">정렬 조건 2.</td>
						<td>
							<span id="orderCondition2_list">
								<select name="orderCondition2">
								<option value="">없음</option>
								<?php
								foreach ($fields as $e_key => $e_val){
									echo "<option value='".$e_key."'>".$e_val."</option>";
								}
								?>
								</select>
							</span>
						</td>
						<td>
							<select name="order_orderCondition2">
							<option value="">오름차순
							<option value=" DESC">내림차순
							</select>
						</td>
					</tr>
					<tr>
						<td height="25">정렬 조건 3.</td>
						<td>
							<span id="orderCondition3_list">
								<select name="orderCondition3">
								<option value="">없음</option>
								<?php
								foreach ($fields as $e_key => $e_val){
									echo "<option value='".$e_key."'>".$e_val."</option>";
								}
								?>
								</select>
							</span>
						</td>
						<td>
							<select name="order_orderCondition3">
							<option value="">오름차순
							<option value=" DESC">내림차순
							</select>
						</td>
					</tr>
					</table>
				</td>
				<td align="right" style="padding-right:5pt;">
					NO(번호) :
					<input type="radio" name="willContainNO" value="Y" checked> 표시함
					<input type="radio" name="willContainNO" value="N"> 표시 안함
					<br>
					엑셀 파일명 :
					<input type="text" class="text" name="fileName" size="15" value="" />
					.xls
				</td>
			</tr>
			</table>
			<div>
				<table width="100%">
				<tr>
					<td>
						<span id="excelDownMsg" style="display:none;">
						<marquee scrolldelay="120" style="color:red;">엑셀 자료를 추출중입니다. 다운로드 창이 나올 때까지 기다려 주세요.</marquee>
						</span>
					</td>
				</tr>
				</table>
				<br />
				<table width="100%">
				<tr>
					<td align="center">
						<span id="excelDownBtn"><button type="button" class="button-bbse blue" onClick="<?php echo ($total<='0')?"alert('엑셀로 다운로드 할 주문정보가 존재하지 않습니다.   ');":"excel_submit('Order');";?>" style="width:170px;"> 엑셀로 다운받기 </button></span>
					</td>
				</tr>
				</table>
			</div>
			<input type="hidden" name="s_period_1" value="<?php echo $s_period_1; ?>" />
			<input type="hidden" name="s_period_2" value="<?php echo $s_period_2; ?>" />
			<input type="hidden" name="s_type" value="<?php echo str_replace("O.","",$s_type); ?>" />
			<input type="hidden" name="s_keyword" value="<?php echo $s_keyword; ?>" />
			<input type="hidden" name="s_list" value="<?php echo $s_list; ?>" />
			</form>
		</div>
	</div><!--#excelOrder-->

	<div id="excelDelivery" style="display:none;">
	<?php if($s_list=='DR'){?>
		<div class="titleH5" style="margin:20px 0 10px 10px; ">1. 택배사 송장등록 엑셀 다운로드</div>

		<div class="borderBox" style="margin:0 10px;padding:10px 10px;">
			<table width="97%" border="0" align="center" cellspacing="0" cellpadding="3">
				<colgroup><col width="28%"><col width="43%"><col width="29%"></colgroup>
				<tr>
					<td>주문상태 : <?php echo (!$s_list)?"전체":$orderStatus[$s_list];?></td>
					<td>검색기간 : <?php 
										if($s_period_1 && $s_period_2) echo $s_period_1." ~ ".$s_period_2;
										elseif($s_period_1) echo $s_period_1." ~ ";
										elseif($s_period_2) echo " ~ ".$s_period_2;
										else echo "-";
										?></td>
					<td>검색어 : <?php echo $sType[str_replace("O.","",$s_type)];?> <?php echo ($s_keyword)?$s_keyword:"-";?></td>
				</tr>
			</table>
		</div>

		<br />
		<div class="borderBox" style="margin:0 10px;padding:10px 10px;">
			<table width="97%" border="0" align="center" cellspacing="0" cellpadding="3">
				<colgroup><col width="100%"></colgroup>
				<tr>
					<td>- 다운로드 받은 파일 목록을 이용해 택배사의 운송장 번호를 입력합니다.</td>
				</tr>
				<tr>
					<td>- 입력한 파일을 업로드하여 배송중 처리를 쉽게 하실 수 있습니다. </td>
				</tr>
			</table>
		</div>
		<br />
		<form name="excel_delivery_download_form" id="excel_delivery_download_form" method="post" action="./proc/bbse-commerce-order-excel-delivery-up-down.exec.php">
			<input type="hidden" name="tMode" value="Download" />
			<input type="hidden" name="s_period_1" value="<?php echo $s_period_1; ?>" />
			<input type="hidden" name="s_period_2" value="<?php echo $s_period_2; ?>" />
			<input type="hidden" name="s_type" value="<?php echo str_replace("O.","",$s_type); ?>" />
			<input type="hidden" name="s_keyword" value="<?php echo $s_keyword; ?>" />
			<input type="hidden" name="s_list" value="<?php echo $s_list; ?>" />
			<table width="97%">
				<colgroup><col><col width="200px"></colgroup>
				<tr>
					<td></td>
					<td align="center"><button type="button" class="button-bbse blue" onClick="<?php echo ($total<='0')?"alert('엑셀로 다운로드 할 주문정보가 존재하지 않습니다.   ');":"excel_submit('DeliveryDownload');";?>" style="width:170px;"> 엑셀양식 다운로드 </button></td>
				</tr>
			</table>
		</form>
	<?php }else{?>
		<div class="borderBox" style="margin:20px 10px;padding:10px 10px;">
			<table width="97%" border="0" align="center" cellspacing="0" cellpadding="3">
				<colgroup><col width="100%"></colgroup>
				<tr>
					<td>- 배송준비중인 주문목록의 엑셀양식을 다운로드 받으셨나요?</td>
				</tr>
				<tr>
					<td>&nbsp; => 팝업을 닫으신 후 주문목록 중 상단의 <span style="color:#0073aa;">'배송준비'</span> 를 클릭하신 후 다시 '엑셀파일 다운로드 / 송장등록' 버튼을 클릭하세요. &nbsp;&nbsp;<a href="http://manual.bbsecommerce.com/ec_6/#송장등록" target="_blank"><button type="button" class="button-small blue" style="height:25px;">자세히 보기</button></a></td>
				</tr>
			</table>
		</div>
	<?php }?>
		<div class="titleH5" style="margin:20px 0 10px 10px; "><?php echo ($s_list=='DR')?"2":"1";?>. 택배사 송장 엑셀 파일 일괄 등록</div>
		<div class="borderBox" style="margin:0 10px;padding:10px 10px;">
			<table width="97%" border="0" align="center" cellspacing="0" cellpadding="3">
				<colgroup><col width="100%"></colgroup>
				<tr>
					<td>- 송장번호가 기록된 엑셀파일 업로드 후 “처리상태 변경” 을 클릭하시면 일괄적으로 배송중 처리로 변경됩니다.</td>
				</tr>
				<tr>
					<td>- 배송 전 구매자의 취소요청이 접수될 수 있으니, 배송처리 전 반드시 상태를 확인해 주시기 바랍니다.  </td>
				</tr>
			</table>
		</div>
		<br />
		<form name="excel_delivery_upload_form" id="excel_delivery_upload_form" method="post" action="./proc/bbse-commerce-order-excel-delivery-up-down.exec.php" enctype="multipart/form-data">
			<input type="hidden" name="tMode" value="Upload" />
			<input type="hidden" name="s_period_1" value="<?php echo $s_period_1; ?>" />
			<input type="hidden" name="s_period_2" value="<?php echo $s_period_2; ?>" />
			<input type="hidden" name="s_type" value="<?php echo str_replace("O.","",$s_type); ?>" />
			<input type="hidden" name="s_keyword" value="<?php echo $s_keyword; ?>" />
			<input type="hidden" name="s_list" value="<?php echo $s_list; ?>" />

			<table width="97%" border="0" align="center" cellspacing="0" cellpadding="3">
				<colgroup><col><col width="200px"></colgroup>
				<tr>
					<td><input type="file" name="deliveryExcelFile" id="deliveryExcelFile" style="height:27px;width:100%;"></td>
					<td align="center"><button type="button" class="button-bbse red" onClick="excel_submit('DeliveryUpload');" style="width:170px;"> 처리상태 변경 </button></td>
					<td></td>
				</tr>
			</table>
		</form>
		<br />
		<div id="deliveryResult" style="border:1px solid #efefef;overflow:auto;height:<?php echo ($s_list=='DR')?"130":"350";?>px;width:95%;margin:0 10px;padding:10px;line-height:20px;">
			<strong>[택배사 송장 일괄 등록 결과]</strong><br />
		</div>
	</div>
</div>
</body>
</html>

<script type="text/javascript" language="javascript">
//<![CDATA[
function checkAll(){
}

function excel_submit(tFrm){
	if(tFrm=='Order'){
		var chked=jQuery("input[name=selFieldName\\[\\]]:checked").not(':disabled').size();

		if(chked<=0){
			alert("컬럼을 하나 이상 선택해 주십시오.");
			return;
		}

		jQuery("#excelDownMsg").css("display","inline");
		jQuery("#excelDownBtn").css("display","none");
		jQuery("#excel_select_field_form").submit();
	}
	else if(tFrm=='DeliveryDownload'){
		jQuery("#excel_delivery_download_form").submit();
	}
	else if(tFrm=='DeliveryUpload'){
		if(!jQuery('#deliveryExcelFile').val()){
			alert("택배사 송장 엑셀파일을 선택해 주세요.");
			return;
		}

		if(confirm("택배사 송장 엑셀 파일을 등록하시겠습니까?       ")){
			var jQfrm = jQuery('#excel_delivery_upload_form'); 

			jQfrm.ajaxForm({
				type:"POST",
				async:true,
				success:function(data, state){
					//alert(data);
					var result = data.split("|||"); 
					if(result[0] == "success"){
						jQuery("#deliveryResult").html(result[1]);

						alert("택배사 송장 엑셀파일 등록을 완료하였습니다.      ");
					}
					else if(result[0] == "DbError"){
						alert("[Error !] DB 오류가 발생하였습니다.     ");
					}
					else if(result[0] == "errorFileName"){
						alert("첨부파일의 이름이 올바르지 않습니다.     ");
					}
					else if(result[0] == "errorFileExtend"){
						alert("첨부파일은 'XLS/XLSX'만 업로드가 가능합니다.     ");
					}
					else if(result[0] == "errorFileUpload"){
						alert("첨부파일 업로드 중 오류가 발생하였습니다.     ");
					}
					else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}
			});	

			jQfrm.submit(); 
		}
	}
}

function change_sort(){
	var scrFields=new Array();
	scrFields=<?php echo json_encode($fields)?>;

	var optStr=tmpValue=orderCondition=tmpSelected="";
	var chked=jQuery("input[name=selFieldName\\[\\]]:checked").not(':disabled').size();

	for(j=1;j<4;j++){
		optStr="";
		for(var k=0; k<chked; k++){
			tmpValue=jQuery("input[name=selFieldName\\[\\]]:checked").not(':disabled').eq(k).val();
			if(jQuery("select[name=orderCondition"+j).val()==tmpValue) tmpSelected="selected='selected'";
			else tmpSelected="";
			optStr +="<option value='"+tmpValue+"' "+tmpSelected+">"+scrFields[tmpValue]+"</option>";
		}

		orderCondition="<select name=\"orderCondition"+j+"\"><option value=\"\">없음</option>"+optStr+"</select>";
		jQuery("#orderCondition"+j+"_list").html(orderCondition);
	}
}

function selectAll(obj, names){
	var names = eval("document.getElementsByName('"+names+"')");
	for(var k=0; k<names.length; k++){
		names[k].checked = (obj.checked == true) ? true: false;
	}
}

function showContainer(tObj){
	var sList="<?php echo $s_list;?>"
	if(tObj=="Order"){
		jQuery("#tab_Order").addClass("active");
		jQuery("#excelOrder").css("display","block");

		jQuery("#tab_Delivery").removeClass("active");
		jQuery("#excelDelivery").css("display","none");
	}
	else{
		jQuery("#tab_Delivery").addClass("active");
		jQuery("#excelDelivery").css("display","block");

		jQuery("#tab_Order").removeClass("active");
		jQuery("#excelOrder").css("display","none");
	}
}

//]]>
</script>
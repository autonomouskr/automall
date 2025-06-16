<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}


$pTarget=$_REQUEST['pTarget'];
$chkList=$_REQUEST['chkList'];
$TB_iframe=true;

$localKey=$_REQUEST['localKey'];

$comLocal=Array();
if($chkList) $comLocal=explode(",",$chkList);
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
</head>
<body>
	<script language="JavaScript">
		jQuery(document).ready(function() {
			jQuery("#localKey").focus();

			jQuery('body').keyup(function(e) {
				if (e.keyCode == 13) go_search();       
			});
		});

		function checkAll(){
			if(jQuery("#checkAll").is(":checked")) jQuery("input[name=check\\[\\]]").not(':disabled').prop("checked",true);
			else jQuery("input[name=check\\[\\]]").not(':disabled').prop("checked",false);
		}

		function go_search(){
			var localKey=jQuery("#localKey").val();
			var pTarget="<?php echo $pTarget;?>";
			var chkList="<?php echo $chkList;?>";

			if(!localKey){
				alert("시/군/구 명을 입력해 주세요.    ");
				jQuery("#localKey").focus();
				return;
			}
			else{
				var strPara="pTarget="+pTarget+"&chkList="+chkList;
				if(localKey) strPara +="&localKey="+localKey;

				strPara +="&TB_iframe=true";

				self.window.location.href ="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-local-list.php?"+strPara;
			}
		}

		function add_local(){
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var str=tId=tLocal="";
			var pTarget="<?php echo $pTarget;?>";

			if(pTarget=='recommend') popupTitle="추천상품";
			else if(pTarget=='relation') popupTitle="관련상품";

			if(chked<=0) {
				alert("지역별 배송료 정책 ("+pTarget+")에 추가할 지역을 선택해주세요.");
				return;
			}

			for(i=0;i<chked;i++){
				var tmpValue=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val().split("|||");
				tId=tmpValue[0];
				tLocal=tmpValue[1];
				if(tId && tLocal){
					str +="<li id=\"local_charge_list_"+pTarget+"_add"+tId+"\">"+tLocal+" <img src=\"<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png\" onClick=\"local_charge_remove("+pTarget+","+tId+");\" class=\"deleteBtn\" alt=\"이미지 제거\" title=\"이미지 제거\" /><input type=\"hidden\" name=\"local_charge_list_"+pTarget+"_idx[]\" value=\""+tId+"\" /><input type=\"hidden\" name=\"local_charge_list_"+pTarget+"_name[]\" value=\""+tLocal+"\" /></li>";
				}
			}

			jQuery(parent.document).contents().find("#local_charge_list_"+pTarget).append(str);
			parent.remove_popup();
		}
	</script>

	<div class="wrap">
		<div id="popup-goods-list">
			<div class="clearfix" style="height:30px;"></div>
			<table class="dataTbls collapse">
			<colgroup><col width=""><col width=""></colgroup>
				<tr>
					<td align="center"><input type="text" name="localKey" id="localKey" value="<?=$localKey?>" style="width:90%;" /></td>
					<td align="center"><button type="button"class="button-small blue" onClick="go_search();" style="height:25px;"> 검색</button></td>
				</tr>
				<tr>
			</table>
			<div style="margin:20px 0;width:100%;text-align:center;font-size:12px;">시/군/구 이름을 입력해 주세요. (ex. 속초시,옹진군,금천구)</div>
			<?php
			if($localKey) { // 검색어가 있을 경우 수행
			?>
			<table class="dataTbls collapse">
			<colgroup><col width="50px"><col width="70px"><col width=""></colgroup>
				<tr>
					<th><input type="checkbox" name="checkAll" id="checkAll" onClick="checkAll();" value="all" /></th>
					<th>번호</th>
					<th>지역명</th>
				</tr>
			<?php
				$zipline = file("./post_sigungu.dat");
				while(list($key, $val) = each($zipline)) {
					$varray = explode("|", $val);
					$string = $varray[1] . $varray[2];
					if(preg_match("/(".$localKey.")/", $string)) $zip[$key] = $val;
				}

				if(sizeof($zip)>0) {
					$i=sizeof($zip);

					while(list(, $value) = each($zip)) {
						$ziparray = explode("|", $value);

					if(in_array($ziparray['0'], $comLocal)) $disableTag="checked disabled";
					else $disableTag="";
				?>
					<tr>
						<td style="text-align:center;"><input type="checkbox" name="check[]" id="check[]" value="<?php echo $ziparray['0']."|||".$ziparray['1']." ".$ziparray['2'];?>" <?php echo $disableTag;?> /></td>
						<td style="text-align:center;"><?php echo $i;?></td>
						<td><?php echo $ziparray['1']." ".$ziparray['2'];?></td>
					</tr>
			<?php
						$i--;
					}
				} else {
			?>
					<tr>
						<td colspan="3" style="text-align:center;">검색 결과가 존재하지 않습니다.</td>
					</tr>

			<?php
				}
			?>
			</table>
			<?php
			}
			?>

			<div class="clearfix" style="height:30px;"></div>
			<?php if(sizeof($zip)>0) {?>
			<div style="margin:30px 0;text-align:center;"><button type="button" class="button-bbse red" onClick="add_local();" style="width:150px;"> 선택 지역 추가 </button></div>
			<?php }?>

		</div>
	</div>
</body>
</html>

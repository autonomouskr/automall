<?php
/*
 [테마 수정 시 주의사항]
 1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
 업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
 2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
 */

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$userClass=$_REQUEST['userClass'];

$result = $wpdb->get_results("select * from bbse_commerce_membership bcm where user_class = '".$userClass."'");
$memberCnt = $wpdb->get_var("select * from bbse_commerce_membership bcm where user_class = '".$userClass."'");


$per_page = (!$_REQUEST['per_page'])?10:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script language="javascript">

		function select_goods(){
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			
			if(chked<=0) {
				alert("추가할 회원을 선택해주세요.");
				return;
			}
			let str = "";
			for(i=0;i<chked;i++){
				tUserId=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val();
			}

			jQuery(parent.document).contents().find("#goods-user-list").append(str);
			parent.remove_popup();
		}

		function go_search(sType){
			var chkList="<?php echo $chkList;?>";
			var condition = tId=jQuery("select[name=searchCondition[]\\[\\]]:checked").val();

			if(condition=='bzNm'){
				var strPara="page=1&per_page="+per_page;
				if(chkList) strPara +="&chkList="+chkList;
			}
			else{
				var strPara="page="+page+"&per_page="+per_page;
				if(chkList) strPara +="&chkList="+chkList;
				var searchCondition=jQuery("#searchCondition").val();
				var users_search_name=jQuery("#users_search_name").val();

				if(searchCondition || users_search_name) strPara +="&searchCondition="+searchCondition+"&users_search_name="+users_search_name;
			}

			strPara +="&TB_iframe=true";

			window.location.href ="<?php echo BBSE_COMMERCE_THEME_WEB_URL?>/admin/theme_option_maingoods-popup-goods-list.php?"+strPara;
		}
	</script>

</head>
<body>
	<div class="wrap">
		<div id="popup-goods-list">
			<div class="clearfix" style="height:30px;"></div>
			<div style="float:left;font-size:12px;margin-left:5px;">
			<select name="searchCondition[]" id="searchCondition">
				<option value="bzNm">업체명</option>
				<option value="bzId">업체ID</option>
			</select>
			<input type="text" name="users_search_name" id="users_search_name" value="" />
			<button type="button" name="productSelect" id="productSelect" class="button-small red" onClick="go_search();" style="width:50px;height:25px;"> 검색 </button>
			</div>
			<div style="float:right;margin-right:5px;">
				<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="select_goods();" style="width:100px;"> 추가하기 </button>
			</div>

			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>

			<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="16%"><col width="38%"><col width="38"></colgroup>
				<tr>
					<th>선택</th>
					<th>업체명</th>
					<th>회원ID</th>
				</tr>
			<?php
			if($memberCnt>'0'){
				
				foreach($result as $i=>$data) {

				    if(in_array($data->user_id, $chkList)){
						$disableTag="checked disabled";
						$onClickTag="";
						$chkIdx = array_search($data->user_id, $chkList);
					}
					
			?>
				<tr>
					<td style="text-align:center;">
						<input type="hidden" name="pop_goods_name_<?php echo $data->user_id;?>" id="pop_goods_name_<?php echo $data->user_id;?>" value="<?php echo $data->user_id;?>" />
						<input type="checkbox" name="check[]" id="check[]" <?php echo $onClickTag;?> value="<?php echo $data->name?>,<?php echo $data->user_id?>" <?php echo $disableTag;?> />
					</td>
					<td style="text-align:center;"><?php echo $data->name?></td>
					<td style="text-align:center;"><?php echo $data->user_id?></td>
				</tr>
			<?php
				}
			}
			else{
			?>
				<tr>
					<td colspan="4" style="text-align:center;height:72px">* .</td>
				</tr>
			<?php
			}
			?>
			</table>
			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>
			<div style="float:right;margin-right:5px;">
				<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="select_goods();" style="width:100px;"> 추가하기 </button>
			</div>
			<div class="clearfix"></div>

			<table align="center">
			<colgroup><col width=""></colgroup>
				<tr>
					<td>
						<?php echo bbse_commerce_get_pagination($paged, $total_pages, $add_args);?>
					</td>
				</tr>
			</table>

			<div class="clearfix" style="height:30px;"></div>
		</div>
	</div>
</body>
</html>

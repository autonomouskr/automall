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

$chkList=$_REQUEST['chkList'];
$searhCon=$_REQUEST['searchCondition'];
$search=$_REQUEST['search'];
$TB_iframe=true;
$userClass=$_REQUEST['userClass'];
unset($prepareParm);

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
	
		function select_count(tFnc,tChk){
		
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var listValue="<?php echo $_REQUEST['chkList'];?>";
			var popupTitle="";
			var listCnt=0

			var listArray=listValue.split(",");
			for(j=0;j<listArray.length;j++){
				if(listArray[j]>0) listCnt++;
			}
			
		}

		function select_goods(){ 
		
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var checkValue=jQuery("input[name=check\\[\\]]:checked").val();
			var arr = checkValue.split(",");
			
			if(chked < 1) {
				alert("담당자을 선택해주세요.");
				return;
			}
            
            parent.document.getElementById("manager_id").value = arr[1];
            parent.document.getElementById("manager_name").value = arr[2];
            
			parent.remove_popup();
			
		}

		function go_search(sType){
		
			var page="<?php echo $page;?>";
			var per_page="<?php echo $per_page;?>";
			var userClass="<?php echo $userClass;?>";

			var condition = jQuery("select[name=searchCondition\\[\\]]").val();

			if(sType == 'total'){
				var strPara="page=1&per_page="+per_page+"&userClass="+userClass;
			}else{
				var strPara="page="+page+"&per_page="+per_page;
				var searchCondition=jQuery("#searchCondition").val();
				var search=jQuery("#users_search_name").val();

				if(searchCondition || search) strPara +="&searchCondition="+searchCondition+"&search="+search+"&userClass="+userClass;
			}

			strPara +="&TB_iframe=true";

			window.location.href ="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>/admin/bbse-commerce-manager-popup.php.?"+strPara;
			//window.location.href ="http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-manager-popup.php.?"+strPara;
		}
		
	</script>

</head>
<body>
	<div class="wrap">
		<div id="popup-goods-list">
			<div class="clearfix" style="height:30px;"></div>
			<div style="float:left;font-size:12px;margin-left:5px;">
			<select name="searchCondition[]" id="searchCondition">
			<?php $addQuery = ""; 
			
			if($userClass){
			    //$addQuery = " and user_class ='".$userClass."'";
			}
			
			if($searhCon == 'manager_id'){
			    $sOption .=" AND user_id LIKE '%".$search."%'";
			}
			
			if($searhCon == 'manager_name'){
			    $sOption .=" AND name LIKE '%".$search."%'";
			}
			
			$prepareParm[]=$start_pos;
			$prepareParm[]=$per_page;
			
			//$sql  = "SELECT * FROM autopole3144.bbse_commerce_membership WHERE leave_yn <>'1' and user_class = '1' ".$addQuery.$sOption." ORDER BY user_no DESC LIMIT ".$start_pos.", ".$per_page;
			$sql  = "SELECT * FROM autopole3144.bbse_commerce_membership WHERE leave_yn <>'1' ".$addQuery.$sOption." ORDER BY user_no DESC LIMIT ".$start_pos.", ".$per_page;
			$result = $wpdb->get_results($sql);
			
			$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM autopole3144.bbse_commerce_membership WHERE leave_yn<>'1' and user_class = '1' ".$addQuery.$sOption, $prepareParm);
			$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수
			$total_pages = ceil($s_total / $per_page);   // 총 페이지수
			
			/* Query String */
			$add_args = array("page"=>$page, "per_page"=>$per_page, "depth_1_category"=>$depth_1_category, "goods_search_name"=>$goods_search_name,"pTarget"=>$pTarget,"tNo"=>$tNo,"chkList"=>$chkList,"ucList"=>$ucList,"TB_iframe"=>$TB_iframe);
			?>
			<?php if($userClass != '1'){?>
				<option value="bzNm">업체명</option>
				<option value="bzId">업체ID</option>
			<?php }else{?>
				<option value="manager_id">아이디</option>
				<option value="manager_name">담당자명</option>
			<?php }?>
			</select>
			<input type="text" name="users_search_name" id="users_search_name" value="<?php echo $search;?>" />
			<button type="button" name="productSelect" id="productSelect" class="button-small red" onClick="go_search();" style="width:50px;height:25px;"> 검색 </button>
			<?php
 			if($searhCon || $search){
				echo "<button type=\"button\" name=\"productSelect\" id=\"productSelect\" class=\"button-small gray\" onClick=\"go_search('total');\" style=\"width:80px;height:25px;\"> 전체보기 </button>";
			}
			?>
			</div>

			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>

			<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="16%"><col width="38%"><col width="38"><col width="38"></colgroup>
				<tr>
					<th>선택</th>
					<th>업체명</th>
					<th>담당자명</th>
					<th>담당자ID</th>
				</tr>
			<?php
			if($s_total>'0'){
			    $chkList=explode(",",$_REQUEST['chkList']);
  			    foreach($result as $i=>$data) {

				    if(in_array($data->user_id, $chkList)){
						$disableTag="checked disabled";
						$onClickTag="";
						$chkIdx = array_search($data->user_id, $chkList);
					}
					else{
					    $disableTag="";
					    $onClickTag="onClick=\"select_count('checkbox',jQuery(this));\"";
					}
					
			?>
				<tr>
					<td style="text-align:center;">
						<input type="hidden" name="pop_goods_name_<?php echo $data->user_id;?>" id="pop_goods_name_<?php echo $data->user_id;?>" value="<?php echo $data->user_id;?>" />
						<input type="checkbox" name="check[]" id="check[]" <?php echo $onClickTag;?> value="<?php echo $data->name?>,<?php echo $data->user_id?>,<?php echo $data->manager_name?>" <?php echo $disableTag;?> />
					</td>
					<td style="text-align:center;"><?php echo $data->name?></td>
					<td style="text-align:center;"><?php echo $data->manager_name?></td>
					<td style="text-align:center;"><?php echo $data->user_id?></td>
				</tr>
			<?php
				}
			}
			else{
			?>
				<tr>
					<td colspan="4" style="text-align:center;height:72px">조회된 회원이 없습니다.</td>
				</tr>
			<?php
			}
			?>
			
			</table>
			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>
			<div style="float:right;margin-right:5px;">
				<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="select_goods();" style="width:100px;"> 선택 </button>
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


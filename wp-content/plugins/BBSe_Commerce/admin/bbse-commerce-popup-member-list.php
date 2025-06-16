<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$chkList=$_REQUEST['chkList'];
$TB_iframe=true;

$sOption="";
$member_search_name=$_REQUEST['member_search_name'];
unset($prepareParm);

$per_page = (!$_REQUEST['per_page'])?20:$_REQUEST['per_page'];  // 한 페이지에 표시될 목록수
$paged = (!$_REQUEST['paged'])?1:intval($_REQUEST['paged']);  // 현재 페이지
$start_pos = ($paged-1) * $per_page;  // 목록 시작 위치
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script language="javascript">
		function select_members(){
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var str=tId=tUrl=tName="";
			var strNo="<?php echo $chkList;?>";

			if(chked<=0) {
				alert("추가할 회원을 선택해주세요.");
				return;
			}

			for(i=0;i<chked;i++){
				tId=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val();
				
				if(strNo) strNo +=",";
				strNo +=tId;

				tMenID=jQuery("#pop_member_id_"+tId).val();
				tMenName=jQuery("#pop_member_name_"+tId).val();
				tMenEmail=jQuery("#pop_member_email_"+tId).val();

				str +="<tr valign='middle' id='display-item-"+tId+"'>"
					+"		<td align='center' style='width:150px;padding:3px;border-bottom:1px dotted #ddd;'>"+tMenID+"</td>"
					+"		<td align='center' style='width:150px;padding:3px;border-bottom:1px dotted #ddd;'>"+tMenName+"</td>"
					+"		<td align='enter' style='width:150px;padding:3px;border-bottom:1px dotted #ddd;'>"+tMenEmail+"</td>"
					+"		<td align='center' style='width:70px;padding:3px;border-bottom:1px dotted #ddd;'>"
					+"			<button type='button' class='fileButton button_s' onclick='displayDelete("+tId+");' style='cursor:pointer;width:40px;'>삭제</button>"
					+"			<input type='hidden' id='display_no_"+tId+"' name='display_no_"+tId+"' value='"+tId+"'>"
					+"		</td>"
					+"</tr>";
			}

			jQuery(parent.document).contents().find("#main_product_list").val(strNo);
			jQuery(parent.document).contents().find("#display-product-list").show();
			jQuery(parent.document).contents().find("#display-product-list-body").append(str);
			parent.remove_popup();
		}

		function go_search(sType){
			var page="<?php echo $page;?>";
			var per_page=jQuery("#per_page").val();
			var chkList="<?php echo $chkList;?>";
			if(sType=='total'){
				var strPara="page=1&per_page="+per_page;
				if(chkList) strPara +="&chkList="+chkList;
			}
			else{
				var strPara="page="+page+"&per_page="+per_page;
				if(chkList) strPara +="&chkList="+chkList;
				var depth_1_category=jQuery("#depth_1_category").val();
				var member_search_name=jQuery("#member_search_name").val();
				if(member_search_name) strPara +="&member_search_name="+member_search_name;
			}

			strPara +="&TB_iframe=true";

			window.location.href ="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-member-list.php?"+strPara;
		}

		
		function memberAllCheck(){
			if(jQuery("input:checkbox[name='allCheck']").is(":checked") == true){
				jQuery("input[name=check\\[\\]]").not(':disabled').attr("checked",true);
			}
			else{
				jQuery("input[name=check\\[\\]]").not(':disabled').attr("checked",false);		
			}
		}

	</script>

</head>
<body>
	<div class="wrap">
		<div id="popup-goods-list">
			<div class="clearfix" style="height:30px;"></div>
			<div style="float:left;font-size:12px;margin-left:5px;">
				<?php
				if($member_search_name){
					$sOption .=" AND user_id LIKE %s";
					$prepareParm[]="%".like_escape($member_search_name)."%";
				}
	
				$prepareParm[]=$start_pos;
				$prepareParm[]=$per_page;

				$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_membership WHERE user_no<>''".$sOption." ORDER BY user_no DESC LIMIT %d, %d", $prepareParm);
				$result = $wpdb->get_results($sql);

				$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_membership WHERE user_no<>''".$sOption, $prepareParm);
				$s_total = $wpdb->get_var($s_total_sql);    // 총 회원수
				$total_pages = ceil($s_total / $per_page);   // 총 페이지수

				/* Query String */
				$add_args = array("page"=>$page, "per_page"=>$per_page, "member_search_name"=>$member_search_name, "chkList"=>$chkList,"TB_iframe"=>$TB_iframe);
				?>

			<select name="per_page" id="per_page">
				<option value="20" <?php echo ($per_page=='20')?"selected='selected'":"";?>>20개</option>
				<option value="50" <?php echo ($per_page=='50')?"selected='selected'":"";?>>50개</option>
				<option value="100" <?php echo ($per_page=='100')?"selected='selected'":"";?>>100개</option>
				<option value="200" <?php echo ($per_page=='200')?"selected='selected'":"";?>>200개</option>
				<option value="300" <?php echo ($per_page=='300')?"selected='selected'":"";?>>300개</option>
				<option value="500" <?php echo ($per_page=='500')?"selected='selected'":"";?>>500개</option>
				<option value="1000" <?php echo ($per_page=='1000')?"selected='selected'":"";?>>1000개</option>
				<option value="2000" <?php echo ($per_page=='2000')?"selected='selected'":"";?>>2000개</option>
				<option value="3000" <?php echo ($per_page=='3000')?"selected='selected'":"";?>>3000개</option>
			</select>&nbsp;&nbsp;&nbsp;<input type="input" name="member_search_name" id="member_search_name" value="<?php echo $member_search_name;?>" /> <button type="button" name="productSelect" id="productSelect" class="button-small red" onClick="go_search('list');" style="width:50px;height:25px;"> 검색 </button>
			<?php
			if($member_search_name){
				echo "<button type=\"button\" name=\"productSelect\" id=\"productSelect\" class=\"button-small gray\" onClick=\"go_search('total');\" style=\"width:80px;height:25px;\"> 전체보기 </button>";
			}
			?>
			</div>
			<div style="float:right;margin-right:5px;">
				<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="select_members();" style="width:100px;"> 추가하기 </button>
			</div>

			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>

			<table class="dataTbls collapse">
			<colgroup><col width="7%"><col width="31%"><col width="31%"><col width="31%"></colgroup>
				<tr>
					<th><input type="checkbox" onClick="memberAllCheck();" name="allCheck" value="1"></th>
					<th>아이디</th>
					<th>이름</th>
					<th>이메일</th>
				</tr>
			<?php
			if($s_total>'0'){
				$chkList=explode(",",$_REQUEST['chkList']);

				foreach($result as $i=>$data) {
					if(in_array($data->user_no, $chkList)) $disableTag="checked disabled";
					else $disableTag="";
			?>
				<tr>
					<td style="text-align:center;">
						<input type="hidden" name="pop_member_id_<?php echo $data->idx;?>" id="pop_member_id_<?php echo $data->user_no;?>" value="<?php echo $data->user_id;?>" />
						<input type="hidden" name="pop_member_email_<?php echo $data->idx;?>" id="pop_member_email_<?php echo $data->user_no;?>" value="<?php echo $data->email;?>" />
						<input type="hidden" name="pop_member_name_<?php echo $data->idx;?>" id="pop_member_name_<?php echo $data->user_no;?>" value="<?php echo ($data->name)?$data->name:"-";?>" />
						<input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->user_no;?>" <?php echo $disableTag;?> />
					</td>
					<td style="text-align:center;"><?php echo $data->user_id;?></td>
					<td style="text-align:center;"><?php echo ($data->name)?$data->name:"-";?></td>
					<td style="text-align:center;"><?php echo $data->email;?></td>
				</tr>
			<?php
				}
			}
			else{
			?>
				<tr>
					<td colspan="4" style="text-align:center;height:72px">* 등록 된 회원이 존재하지 않습니다.</td>
				</tr>
			<?php
			}
			?>
			</table>
			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>
			<div style="float:right;margin-right:5px;">
				<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="select_members();" style="width:100px;"> 추가하기 </button>
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

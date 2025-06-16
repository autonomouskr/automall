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

$pTarget=$_REQUEST['pTarget'];
$chkList=$_REQUEST['chkList'];
$tNo=$_REQUEST['tNo'];
$ucList=$_REQUEST['ucList'];
$TB_iframe=true;

$sOption="";
$depth_1_category=$_REQUEST['depth_1_category'];
$goods_search_name=$_REQUEST['goods_search_name'];
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
		function select_count(pTarget,tFnc,tChk){
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var listValue="<?php echo $_REQUEST['chkList'];?>";
			var popupTitle="";
			var listCnt=0

			var listArray=listValue.split(",");
			for(j=0;j<listArray.length;j++){
				if(listArray[j]>0) listCnt++;
			}

			var totalCnt=chked+listCnt;

			if(pTarget=='recommend') popupTitle="추천상품";
			else if(pTarget=='best') popupTitle="베스트상품";
			else if(pTarget=='md') popupTitle="MD기획상품";
			else if(pTarget=='new') popupTitle="신상품";
			else if(pTarget=='today') popupTitle="오틀만특가";
			else if(pTarget=='hot') popupTitle="핫아이템";
			else popupTitle="포스트 상품 노출";

			if(pTarget=='md'){
				var addCnt=12-listCnt;

				if(totalCnt>12) {
					if(listCnt>=12) alert(popupTitle+"의 상품추가는 최대 12개까지만 가능합니다. ");
					else alert("추가할 상품을 "+addCnt+"개만 (전체 12개) 선택해주세요.");
					if(tFnc=='checkbox') tChk.attr("checked",false);
					return;
				}
			}else if(pTarget=='post'){
				var addCnt=10-listCnt;

				if(totalCnt>10) {
					if(listCnt>=10) alert(popupTitle+"의 상품추가는 최대 10개까지만 가능합니다. ");
					else alert("추가할 상품을 "+addCnt+"개만 (전체 10개) 선택해주세요.");
					if(tFnc=='checkbox') tChk.attr("checked",false);
					return;
				}
			}
		}

		function select_goods(pTarget){
			var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
			var str=tId=tUrl=tName="";
			var tNo="<?php echo $tNo;?>";
			
			if(tNo && tNo>0) var tNoStr="_"+tNo;
			else var tNoStr="";

			if(pTarget=='recommend') popupTitle="추천상품";
			else if(pTarget=='best') popupTitle="베스트상품";
			else if(pTarget=='md') popupTitle="MD기획상품";
			else if(pTarget=='new') popupTitle="신상품";
			else if(pTarget=='today') popupTitle="오틀만특가";
			else if(pTarget=='hot') popupTitle="핫아이템";
			else if(pTarget=='post') popupTitle="포스트 상품 노출";

			if(chked<=0) {
				alert(popupTitle+"에 추가할 상품을 선택해주세요.");
				return;
			}

			if(pTarget=="best" || pTarget=="md" || pTarget=="post") select_count(pTarget,'','');

			for(i=0;i<chked;i++){
				var tUC = "|";
				tId=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val();
				tUrl=jQuery("#pop_goods_image_"+tId).val();
				tName=jQuery("#pop_goods_name_"+tId).val();
				
				var userClassChked=jQuery("input[name=userClass_"+tId+"\\[\\]]:checked").not(':disabled').size();
				
				for(let j=0; j<userClassChked;j++){
					tUC += jQuery("input[name=userClass_"+tId+"\\[\\]]:checked").not(':disabled').eq(j).val();
					if(j == userClassChked-1){
						tUC+="|";
					}else{
						tUC+=",";
					}
				}
				str +="<li id=\"goods_img_"+pTarget+"_list"+tNoStr+"_"+tId+"\" title=\""+tName+"\">"
					+"	<table style=\"margin-left:14px;padding:0px;\">"
					+"		<tr>"
					+"			<td style=\"border-bottom:0px;padding:0px;\">"
					+"				<div class=\"thumb\">"
					+"					<img src=\""+tUrl+"\" alt=\"상품이미지\" />"
					+"				</div>"
					+"			</td>"
					+"			<td style=\"border-bottom:0px;padding:0px;\"><span id=\"popup-li-list"+tNoStr+"-"+tId+"\"><img src=\"<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/btn_delete.png\" onClick=\"goods_img_link_remove('"+pTarget+"',"+tId+",'"+tNo+"')\" class=\"deleteBtn\" alt=\""+popupTitle+"에서 삭제\" title=\""+popupTitle+"에서 삭제\" /></span></td>"
					+"		</tr>"
					+"	</table>"
					+"	<div class=\"goodsname\">"+tName+"<input type=\"hidden\" name=\"goods_"+pTarget+"_list"+tNoStr+"[]\" value=\""+tId+"\" /><input type=\"hidden\" name=\"goods_"+pTarget+"_uc_list[]\" value=\""+tUC+"\" /></div>"
					+"</li>";
			}

			if(!tNoStr) tNoStr="_1";
			
			jQuery(parent.document).contents().find("#goods-"+pTarget+"-ul-list"+tNoStr).append(str);
			parent.remove_popup();
		}

		function go_search(sType){
			var page="<?php echo $page;?>";
			var per_page="<?php echo $per_page;?>";
			var chkList="<?php echo $chkList;?>";
			var pTarget="<?php echo $pTarget;?>";
			var tNo="<?php echo $tNo;?>";
			var tUc="<?php echo $ucList;?>";

			if(sType=='total'){
				var strPara="page=1&per_page="+per_page;
				if(chkList) strPara +="&chkList="+chkList;
				if(pTarget) strPara +="&pTarget="+pTarget;
				if(tNo) strPara +="&tNo="+tNo;
			}
			else{
				var strPara="page="+page+"&per_page="+per_page;
				if(chkList) strPara +="&chkList="+chkList;
				if(pTarget) strPara +="&pTarget="+pTarget;
				if(tNo) strPara +="&tNo="+tNo;
				var depth_1_category=jQuery("#depth_1_category").val();
				var goods_search_name=jQuery("#goods_search_name").val();

				if(depth_1_category || goods_search_name) strPara +="&depth_1_category="+depth_1_category+"&goods_search_name="+goods_search_name;
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
			<select name="depth_1_category" id="depth_1_category">
				<option value="">1차 카테고리</option>
				<?php
				$addQuery = "";
				$cat_query = $wpdb->get_results("SELECT * FROM bbse_commerce_category WHERE idx<>'' AND idx<>'1' AND depth_1>0 AND depth_2<=0 AND depth_3<=0 AND c_use='Y' ORDER BY c_rank ASC", ARRAY_A);
				foreach($cat_query as $c_row){
					if($c_row['depth_1']==$depth_1_category) $cSelected=" selected='selected'";
					else $cSelected="";
					echo "<option value='".$c_row['depth_1']."'".$cSelected.">".$c_row['c_name']."</option>";
				}

				if($depth_1_category && $depth_1_category>'0'){
					$sOption .=" AND (";

					$cat2_query = $wpdb->get_results("SELECT * FROM bbse_commerce_category WHERE idx<>'' AND depth_1='".$depth_1_category."' AND depth_2>=0 ORDER BY c_rank ASC", ARRAY_A);
					foreach($cat2_query as $c_row2){
						if($sOption && $sOption !=" AND (") $sOption .=" OR ";
						$sOption .="goods_cat_list LIKE %s";
						//$prepareParm[]="%".like_escape("|".$c_row2['idx']."|")."%";
						$prepareParm[]="%".like_escape("".$c_row2['idx']."")."%";
					}

					$sOption .=")";
				}

				if($goods_search_name){
					$sOption .=" AND goods_name LIKE %s";
					$prepareParm[]="%".like_escape($goods_search_name)."%";
				}
	
				$prepareParm[]=$start_pos;
				$prepareParm[]=$per_page;

				$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_goods WHERE idx<>'' AND (goods_display='display' OR goods_display='soldout')".$sOption." ORDER BY idx DESC LIMIT %d, %d", $prepareParm);
				$result = $wpdb->get_results($sql);

				$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods WHERE idx<>'' AND (goods_display='display' OR goods_display='soldout')".$sOption, $prepareParm[0]);
				$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수
				$total_pages = ceil($s_total / $per_page);   // 총 페이지수

				/* Query String */
				$add_args = array("page"=>$page, "per_page"=>$per_page, "depth_1_category"=>$depth_1_category, "goods_search_name"=>$goods_search_name,"pTarget"=>$pTarget,"tNo"=>$tNo,"chkList"=>$chkList,"ucList"=>$ucList,"TB_iframe"=>$TB_iframe);
				?>
			</select>
			<input type="input" name="goods_search_name" id="goods_search_name" value="<?php echo $goods_search_name;?>" /> <button type="button" name="productSelect" id="productSelect" class="button-small red" onClick="go_search('list');" style="width:50px;height:25px;"> 검색 </button>
			<?php
			if($depth_1_category || $goods_search_name){
				echo "<button type=\"button\" name=\"productSelect\" id=\"productSelect\" class=\"button-small gray\" onClick=\"go_search('total');\" style=\"width:80px;height:25px;\"> 전체보기 </button>";
			}
			?>
			</div>
			<div style="float:right;margin-right:5px;">
				<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="select_goods('<?php echo $_REQUEST['pTarget']?>');" style="width:100px;"> 추가하기 </button>
			</div>

			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>

			<table class="dataTbls collapse">
			<colgroup><col width="7%"><col width="15%"><col width=""><col width="13%"></colgroup>
				<tr>
					<th>선택</th>
					<th>이미지</th>
					<th>상품명</th>
					<th>등록일</th>
					<th>회원등급</th>
				</tr>
			<?php
			if($s_total>'0'){
				$chkList=explode(",",$_REQUEST['chkList']);
				$ucList2=explode(",,",$_REQUEST['ucList']);
				
                $userClass = $wpdb->get_results("select * from bbse_commerce_membership_class bcmc where use_sale = 'Y'");
				foreach($result as $i=>$data) {
				    $memPrice=unserialize($data->goods_member_price);
					$soldoutFlag="";

					if(in_array($data->idx, $chkList)){
						$disableTag="checked disabled";
						$onClickTag="";
						$chkIdx = array_search($data->idx, $chkList);
						$ucList3 = explode(",", str_replace("|","",$ucList2[$chkIdx]));
					}
					else{
						$disableTag="";
						if($_REQUEST['pTarget']=='best' || $_REQUEST['pTarget']=='md' || $_REQUEST['pTarget']=='post'){
							$onClickTag="onClick=\"select_count('".$_REQUEST['pTarget']."','checkbox',jQuery(this));\"";
						}
						else{
							$onClickTag="";
						}
						$ucList3 = "";
					}

					if($data->goods_basic_img) $basicImg = wp_get_attachment_image_src($data->goods_basic_img);
					else{
						$imageList=explode(",",$data->goods_add_img);
						if(sizeof($imageList)>'0') $basicImg=wp_get_attachment_image_src($imageList['0']);
						else $basicImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
					}

					if($data->goods_display=='soldout' || ($data->goods_count_flag=='goods_count' && $data->goods_count <= '0')) $soldoutFlag=" <img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_soldout.png' align='absmiddle' title='품절된 상품입니다.' />";
					elseif($data->goods_count_flag=='option_count') {
						$optTotal_count = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$data->idx."' AND goods_option_item_count>'0' AND goods_option_item_soldout<>'soldout' AND goods_option_item_display='view'"); 
						if($optTotal_count<='0') $soldoutFlag=" <img src='".BBSE_COMMERCE_PLUGIN_WEB_URL."images/icon_soldout.png' align='absmiddle' title='품절된 상품입니다.' />";
					}
			?>
				<tr>
					<td style="text-align:center;">
						<input type="hidden" name="pop_goods_image_<?php echo $data->idx;?>" id="pop_goods_image_<?php echo $data->idx;?>" value="<?php echo $basicImg['0'];?>" />
						<input type="hidden" name="pop_goods_name_<?php echo $data->idx;?>" id="pop_goods_name_<?php echo $data->idx;?>" value="<?php echo $data->goods_name;?>" />
						<input type="checkbox" name="check[]" id="check[]" <?php echo $onClickTag;?> value="<?php echo $data->idx;?>" <?php echo $disableTag;?> />
					</td>
					<td style="text-align:center;"><img src="<?php echo $basicImg['0'];?>" style="width:50px;height:50px;border:1px solid #efefef;"></td>
					<td><?php echo $data->goods_name;?><?php echo $soldoutFlag;?></td>
					<td style="text-align:center;"><?php echo date("Y.m.d",$data->goods_reg_date);?></td>
					<td style="text-align:left;">
					
			<?php		
			
        			for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
        			    if($memPrice['goods_member_price'][$m] > '0'){
            			    for($i=0; $i<sizeof($userClass);$i++){
            			        if($memPrice['goods_member_level'][$m] == $userClass[$i]->no){
            			            $className = $userClass[$i]->class_name;
            			            $classNo = $userClass[$i]->no;
            			            if(in_array($classNo, $ucList3)){
            			                $isChecked = "checked disabled";
            			            }else{
            			                $isChecked = "";
            			            }
            ?>
            			           <label><input type="checkbox" name="userClass_<?php echo $data->idx;?>[]" id="userClass_<?php echo $data->idx;?>" style="align-items: left" value="<?php echo $classNo?>" <?php echo $isChecked;?>/>&nbsp;&nbsp;<?php echo $className?></label><br>
			<?php
            			        }
            			    }
        			    }
        			}
        			
            ?>
        			
					</td>
				</tr>
			<?php
				}
			}
			else{
			?>
				<tr>
					<td colspan="4" style="text-align:center;height:72px">* 등록 된 상품이 존재하지 않습니다.</td>
				</tr>
			<?php
			}
			?>
			</table>
			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>
			<div style="float:right;margin-right:5px;">
				<button type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="select_goods('<?php echo $_REQUEST['pTarget']?>');" style="width:100px;"> 추가하기 </button>
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

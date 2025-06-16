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
			
			if(chked < 1) {
				alert("제품을 선택해주세요.");
				return;
			}
			
			//let goods_code = "";
			let goods_name = "";
			let goods_idx = "";
			let goods_option_title = "";
			
			idx=jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(0).val();
			
			jQuery("input[name=check\\[\\]]:checked").each(function(){
                goods_name = jQuery(this).siblings('#goods_name').val();
                //goods_code = jQuery(this).siblings('#goods_code').val();
                goods_idx = jQuery(this).siblings('#goods_idx').val();
                goods_option_title = jQuery(this).siblings('#goods_option_title').val();
            }); 
            
            parent.document.getElementById("goods_name").value = goods_name;
            //parent.document.getElementById("goods_code").value = goods_code;
            parent.document.getElementById("goods_idx").value = goods_idx;
            parent.document.getElementById("goods_option_title").value = goods_option_title;
            
			parent.remove_popup();
		}

		function go_search(sType){
		
			var page="<?php echo $page;?>";
			var per_page="<?php echo $per_page;?>";

			var condition = jQuery("select[name=searchCondition\\[\\]]").val();

			if(sType == 'total'){
				var strPara="page=1&per_page="+per_page+"&userClass="+userClass;
			}else{
				var strPara="page="+page+"&per_page="+per_page;
				var searchCondition=jQuery("#searchCondition").val();
				var search=jQuery("#search_name").val();

				if(searchCondition || search) strPara +="&searchCondition="+searchCondition+"&search="+search;
			}

			strPara +="&TB_iframe=true";

			window.location.href ="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>/admin/bbse-commerce-inven-popup.php.?"+strPara;
			//window.location.href ="http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-inven-popup.php.?"+strPara;
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
			$sOption = "";
			
			if($searhCon == 'goods_name'){
			    $sOption .=" AND name LIKE %s";
			    $prepareParm[]="%".like_escape($search)."%";
			}
			
 			if($searhCon == 'goods_code'){
			    $sOption .=" AND code LIKE %s";
			    $prepareParm[]="%".like_escape($search)."%";
			}
			
			$prepareParm[]=$start_pos;
			$prepareParm[]=$per_page;
			
			#$sql = "select goods_code ,	goods_name from	bbse_commerce_goods bcg where goods_display != 'trash' union select	goods_option_item_unique_code ,	goods_option_title from	bbse_commerce_goods_option a, bbse_commerce_goods b where 1=1 ".$addQuery.$sOption." and b.goods_display != 'trash' and a.goods_idx = b.idx order by idx desc LIMIT %d, %d";
			//$sql  = $wpdb->prepare("select d.code, d.name, d.idx, d.option_name from (select idx as idx, goods_code  as code,	goods_name as name, '' as option_name from	bbse_commerce_goods bcg where goods_display != 'trash' union (select concat(goods_idx,'-',goods_option_title) as idx, '' as code, '' as name,  goods_option_title as option_name from bbse_commerce_goods_option a, bbse_commerce_goods b where 1 = 1 and b.goods_display != 'trash'	and a.goods_idx = b.idx	order by b.idx)) d where 1=1 ".$sOption." order by d.idx LIMIT %d, %d", $prepareParm);
			//$sql  = $wpdb->prepare("select idx as idx, goods_code  as code,	goods_name as name from	bbse_commerce_goods where goods_display != 'trash' ".$sOption." order by idx LIMIT %d, %d", $prepareParm);
			//$sql  = $wpdb->prepare("select distinct goods_code as goods_code, goods_name as goods_name, goods_option_title as goods_option_title, goods_idx as goods_idx from bbse_commerce_goods a LEFT JOIN bbse_commerce_goods_option b ON a.idx = b.goods_idx where a.goods_display != 'trash' ".$sOption." order by goods_code LIMIT %d, %d", $prepareParm);
			$sql  = $wpdb->prepare("select * from bbse_commerce_goods where goods_display != 'trash' ".$sOption." order by idx LIMIT %d, %d", $prepareParm);
			$result = $wpdb->get_results($sql);
			
			#$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods WHERE 1=1 ".$addQuery.$sOption , $prepareParm);
			//$s_total_sql  = $wpdb->prepare("select count(1) from (select idx as idx, goods_code  as code,	goods_name as name, '' as option_name from	bbse_commerce_goods bcg where goods_display != 'trash' union (select concat(goods_idx,'-',goods_option_title) as idx, '' as code, '' as name,  goods_option_title as option_name from	bbse_commerce_goods_option a, bbse_commerce_goods b where 1 = 1	and b.goods_display != 'trash'	and a.goods_idx = b.idx	order by b.idx)) d where 1=1 ".$sOption." order by d.idx" , $prepareParm);
			$s_total_sql  = $wpdb->prepare("select count(1) from ( select distinct goods_code, goods_name,		goods_option_title,		goods_idx	from		bbse_commerce_goods a	LEFT JOIN bbse_commerce_goods_option b ON		a.idx = b.goods_idx	where		a.goods_display != 'trash') a");
			$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수
			$total_pages = ceil($s_total / $per_page);   // 총 페이지수
			
			/* Query String */
			$add_args = array("page"=>$page, "per_page"=>$per_page, "TB_iframe"=>$TB_iframe);
			
			?>
				<option value="">전체</option>
				<option value="goods_name">제품명</option>
				<option value="goods_code">제풐코드</option>
			</select>
 			<input type="text" name="search_name" id="search_name" value="<?php echo $search;?>" />
			<button type="button" name="productSelect" id="productSelect" class="button-small  red" onClick="go_search('none');" style="width:50px;height:25px;"> 검색 </button>
			<?php
 			if($searhCon || $search){
				echo "<button type=\"button\" name=\"productSelect\" id=\"productSelect\" class=\"button-small gray\" onClick=\"go_search('total');\" style=\"width:80px;height:25px;\"> 전체보기 </button>";
			}
			?>
			</div>

			<div class="clearfix"></div>
			<div class="clearfix" style="height:10px;"></div>

			<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="15%"><col width="35%"><col width="25%"><col width="20%"></colgroup>
				<tr>
					<th>선택</th>
					<th>제품명</th>
					<th>옵션</th>
					<th>제품코드</th>
				</tr>
			<?php
 			if($s_total>'0'){
 			    $chkList=explode(",",$_REQUEST['chkList']);
  			    foreach($result as $i=>$data) {

/*  				    if(in_array($data->idx, $chkList)){
 						$disableTag="checked disabled";
						$onClickTag="";
					}
					else{
					    $disableTag="";
					    $onClickTag="onClick=\"select_count('checkbox',jQuery(this));\"";
					} */
					
			?>
    				<tr>
    					<td style="text-align:center;">
    						<input type="hidden" name="pop_goods_name_<?php echo $data->idx;?>" id="pop_goods_name_<?php echo $data->idx;?>" value="<?php echo $data->idx;?>" />
     						<input type="checkbox" name="check[]" id="check[]" <?php echo $onClickTag;?> value="<?php echo $data->idx?>" <?php echo $disableTag;?> />
    						<input type="hidden" name="goods_idx" id="goods_idx" value="<?php echo $data->idx?>" />
    						<input type="hidden" name="goods_name" id="goods_name" value="<?php echo $data->goods_name?>" />
    						<input type="hidden" name="goods_option_title" id="goods_option_title" value="<?php echo $option->goods_option_title;?>" />
    						<input type="hidden" name="goods_code" id="goods_code" value="<?php echo $data->goods_code?>" />
    					</td>
    					<td style="text-align:center;"><?php echo $data->goods_name;?></td>
    					<td style="text-align:center;"><?php echo "";?></td>
    					<td style="text-align:center;"><?php echo $data->goods_code;?></td>
    					
    				</tr>
    			<?php
                    $options = $wpdb->get_results("select * from bbse_commerce_goods_option where goods_idx='".$data->idx."'");
                    foreach($options as $j=>$option){
    			    ?>
    			    
    			    <tr>
    			    <td style="text-align:center;">
    			    <!-- <input type="hidden" name="pop_goods_name_<?php echo $option->goods_idx,"-",$option->goods_option_title;?>" id="pop_goods_name_<?php echo $option->goods_idx,"-",$option->goods_option_title;?>" value="<?php echo $option->goods_idx,"-",$option->goods_option_title;?>" /> -->
    			    <input type="hidden" name="pop_goods_name_<?php echo $option->goods_idx;?>" id="pop_goods_name_<?php echo $option->goods_idx;?>" value="<?php echo $option->goods_idx;?>" />
    			    <input type="checkbox" name="check[]" id="check[]" <?php echo $onClickTag;?> value="<?php echo $option->goods_idx,"-",$option->goods_option_title;?>" <?php echo $disableTag;?> />
    						<input type="hidden" name="goods_idx" id="goods_idx" value="<?php echo $option->goods_idx;?>" />
    						<input type="hidden" name="goods_name" id="goods_name" value="<?php echo $data->goods_name;?>" />
    						<input type="hidden" name="goods_option_title" id="goods_option_title" value="<?php echo $option->goods_option_title;?>" />
    						<input type="hidden" name="goods_code" id="goods_code" value="<?php echo $data->goods_code?>" />
    					</td>
    					<td style="text-align:center;"><?php echo $data->goods_name;?></td>
    					<td style="text-align:center;"><?php echo $option->goods_option_title;?></td>
    					<td style="text-align:center;"><?php echo $data->goods_code;?></td>
    				</tr>
    			    <?php 
    				}
  			    }
		    }
			else{
			?>
				<tr>
					<td colspan="4" style="text-align:center;height:72px">조회된 제품이 없습니다.</td>
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

<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname, $current_user, $orderStatus, $payHow;
wp_get_current_user();

$Loginflag='member';

if($_SESSION['snsLogin']=='Y' && $_SESSION['snsLoginData']){
	if($_SESSION['snsLoginData']){
		$snsLoginData=unserialize($_SESSION['snsLoginData']);

		$snsData=$wpdb->get_row("SELECT idx FROM bbse_commerce_social_login WHERE sns_type='".$snsLoginData['sns_type']."' AND sns_id='".$snsLoginData['sns_id']."' ORDER BY idx DESC LIMIT 1");
		if($snsData->idx){
			$Loginflag='social';
		}
	}
}

$V = $_REQUEST;

if(is_user_logged_in()) {
	$myInfo=bbse_get_user_information();
	$orderList = $wpdb->get_results("select order_no from bbse_commerce_order where user_id = '".$myInfo->user_id."' and order_status in ('DE','OE')");
	$storage = $wpdb->get_results("select * from tbl_storage where manager_id = '".$myInfo->user_id."' and delete_yn != 'Y'");
}else{
	if($Loginflag=='social'){
	    $myInfo=bbse_get_user_information();
	}
	else{
	    
	}
}
?>
<style>
    .btnAdd {
      background-color: #007BFF;   /* 밝은 파란색 */
      color: white;                /* 텍스트는 흰색 */
      border: none;
      padding: 6px 12px;
      font-size: 16px;
      border-radius: 8px;          /* 둥근 모서리 */
      cursor: pointer;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* 부드러운 그림자 */
    }
</style>


<div class="wrap">
	<div class="article">
		<div class="tb_dp_list mobileHidden">
    			<table>
    				<colgroup>
    					<col style="width:5%;" />
    					<col style="width:3%;" />
    					<col style="width:25%;" />
    					<col style="width:25%;" />
    				</colgroup>
    				<thead>
    					<tr>
    						<th scope="col"><input type="checkbox" name="check_all" id="check_all" onClick="checkAll();"></th>
    						<th>번호</th>
    						<th scope="col">제품명</th>
    						<th scope="col">옵션명</th>
    					</tr>
    				</thead>
    				<tbody>
    				<?php 
    				
    				if(sizeof($orderList) > '0'){
    				    foreach($orderList as $i=>$order){
    				        $list .= "'";
    				        $list .=$order->order_no;
    				        $list .= "'";
    				        if($i< sizeof($orderList)-1){
    				            $list .=",";
    				        }
    				    }
    				    
    				    $orderDetail = $wpdb->get_results("select * from bbse_commerce_order_detail where order_no in  (".$list.")");
			            $optionArray = [];
			            $goodsIdxArray = [];
			            $detailIdxArray = [];
				        foreach($orderDetail as $j=>$detail){
				            $optionBasic = unserialize($detail->goods_option_basic);
				            $optionAdd = unserialize($detail->goods_option_add);
				            foreach($optionBasic['goods_option_title'] as $k=>$option){
				                if(!in_array($option,$optionArray)){
				                    array_push($optionArray,$option);
				                    array_push($goodsIdxArray,$detail->goods_idx);
				                    array_push($detailIdxArray,$detail->idx);
				                }
				            }
				        }
                        
				        foreach($optionArray as $x=>$optionArr){
					?>
						<tr>
							<td style="text-align:center;" class="item">
    							<input type="hidden" id="user_id" name = "user_id" value = "<?php echo $myInfo->user_id;?>" />
    							<input type="hidden" id="goodsIdx[]" name = "goodsIdx[]" value = "<?php echo $goodsIdxArray[$x];?>" />
    							<input type="hidden" id="storage[]" name = "storage[]" value = "<?php echo $storage[0]->storage_code;?>" />
    							<input type="hidden" id="goods_option_title[]" name = "goods_option_title[]" value = "<?php echo $optionArr;?>" />
    							<input type="hidden" id="goods_option_overprice" name = "goods_option_overprice" value = "<?php echo $optionBasic['goods_option_overprice'][0];?>" />
    							<input type="hidden" id="goods_option_count" name = "goods_option_count" value = "<?php echo $optionBasic['goods_option_count'][0];?>" />
    							<input type="hidden" id="goods_add_title" name = "goods_add_title" value = "<?php echo $optionAdd['goods_add_title'][0];?>" />
    							<input type="hidden" id="goods_add_count" name = "goods_add_count" value = "<?php echo $optionAdd['goods_add_count'][0];?>" />
								<input type="checkbox" name="check[]" id="check[]" value="<?php echo $detailIdxArray[$x];?>">
							</td>
							<td><?php echo $x+1;?></td>
							<td><span name="goodsName" id="goodsName" value="echo $detail->goods_name;"><?php echo $detail->goods_name;?></span></td>
							<td><span name="optionBasic" id="optionBasic" value="<?php echo $optionArr;?>" /><?php echo $optionArr;?></span></td>
						</tr>
					<?php 
				        }
    				}else{?>
    				<tr style="text-align: center;"><td colspan="5">주문 제품이 존재하지 않습니다.</td></tr>
    				<?php }?>
    				</tbody>
    			</table>
    			
                <button type="submit" class="bb_btn shadow"  style="float:right; width: 100px; height: 30px; color:red; margin-top:10px;" onclick="addMyInven();">저장</button>
		</div>
	</div>
</div>
<br>
<script>
	
function checkAll(){
	
	if(jQuery("#check_all").is(":checked")){
		jQuery("input[name=check\\[\\]]").attr("checked",true);
	}		
	else{
		jQuery("input[name=check\\[\\]]").attr("checked",false);
	}
}

function searchSubmit() {
	jQuery("#inven_frm").submit();
}


function addMyInven(){

	let chkarr = [];
	let goodsIdxArr = [];
	let optionArr = [];
	let storageArr = [];
	
	var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
	
	for(i=0;i<chked;i++){
		chkarr.push(jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val());
	}
	
	if(chkarr.length < 1){
		alert("선택된 제품이 없습니다.");
		return;
	}
	
	const rows = document.querySelectorAll('.item');
    rows.forEach(row => {
        let checkbox = row.querySelector("input[name=check\\[\\]]");
        const textInput = row.querySelector("input[name=goodsIdx\\[\\]]");
        const textInput2 = row.querySelector("input[name=goods_option_title\\[\\]]");
        const textInput3 = row.querySelector("input[name=storage\\[\\]]");
        
        if (checkbox.checked) {
          goodsIdxArr.push(textInput.value);
          optionArr.push(textInput2.value);
          storageArr.push(textInput3.value);
        }
    });
    
	
	if(!confirm('선택한 제품을 재고관리 항목에 추가 하시겠습니까?')){
		return;
	}
	
	jQuery.ajax({
		type: 'post', 
		async: false, 
		//url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-myInven.exec.php',
		url: 'http://localhost/wp-content/plugins/BBSe_Commerce/admin/proc/bbse-commerce-myInven.exec.php',
		data: {chkarr:chkarr,userId:'<?php echo $myInfo->user_id;?>', goodsIdxArr:goodsIdxArr, optionArr:optionArr, storageArr:storageArr}, 
		success: function(data){
			var result = data.split("|||"); 
			if(result['0'] == "success"){
				alert(result['1']+ "건이 저장되었습니다.   ");
				window.location.href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=myInven";
			}else{
				alert("서버와의 통신이 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
}
	
</script> 
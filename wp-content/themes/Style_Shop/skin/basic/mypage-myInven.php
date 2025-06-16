<?php
/*
 [테마 수정 시 주의사항]
 1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
 업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
 2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
 */

$current_user = wp_get_current_user();  // 현재 회원의 정보 추출
$currUserID=$current_user->user_login;
$result = $wpdb->get_results("select * from tbl_inven where manager_id = '".$currUserID."' and delete_yn != 'Y'" );

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
    
    .btnDelete {
      background-color: gray;   /* 밝은 파란색 */
      color: white;                /* 텍스트는 흰색 */
      border: none;
      padding: 6px 12px;
      font-size: 16px;
      border-radius: 8px;          /* 둥근 모서리 */
      cursor: pointer;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* 부드러운 그림자 */
    }

    .btnOrder {
      background-color: gray;   /* 밝은 파란색 */
      color: white;                /* 텍스트는 흰색 */
      border: none;
      padding: 6px 12px;
      font-size: 14px;
      border-radius: 8px;          /* 둥근 모서리 */
      cursor: pointer;
      transition: background-color 0.3s ease;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* 부드러운 그림자 */
    }
    
    .btnAdd:hover {
      background-color: #0056b3;   /* 호버 시 더 짙은 파란색 */
    }

    .btnAdd:active {
      background-color: #004080;   /* 클릭 시 더 어두운 파란색 */
    }
    
    .btnDelete:hover {
      background-color: #0056b3;   /* 호버 시 더 짙은 파란색 */
    }

    .btnDelete:active {
      background-color: #004080;   /* 클릭 시 더 어두운 파란색 */
    }
      
    .btnOrder:hover {
      background-color: #0056b3;   /* 호버 시 더 짙은 파란색 */
    }

    .btnOrder:active {
      background-color: #004080;   /* 클릭 시 더 어두운 파란색 */
    }
    
    .btnSize{
        float:right; width: 100px; height: 30px;
    }
      
</style>

<script>

function locationChange(event){
	const selectedValue = event.target.value;
	$.ajax({
		url:'<?php echo BBSE_THEME_WEB_URL; ?>/myInven_proc.php',
		type: 'post',
		data:{
			"selectedValue":selectedValue
		},
		success : function(data){
		
			let result = JSON.parse(data);
			if(result.length > 0){
			
			}else{
			
			}
		}
	})
}

function checkAll(){
	
	if(jQuery("#check_all").is(":checked")){
		jQuery("input[name=check\\[\\]]").attr("checked",true);
	}		
	else{
		jQuery("input[name=check\\[\\]]").attr("checked",false);
	}
}

function go_cart(mode){
	
	let goods_idx = [];
	let basicOption = [];
	let orderNo = [];
	let orderIdx = [];
	
	var chked=jQuery("input[name=check\\[\\]]:checked").not(':disabled').size();
	
	for(i=0;i<chked;i++){
		goods_idx.push(jQuery("input[name=check\\[\\]]:checked").not(':disabled').eq(i).val());
	}
	
	if(goods_idx.length < 1){
		alert("선택된 제품이 없습니다.");
		return;
	}
	
	const rows = document.querySelectorAll('.item');
    rows.forEach(row => {
        let checkbox = row.querySelector("input[name=check\\[\\]]");
        const textInput = row.querySelector("input[name=basic_option_title\\[\\]]");
        const textInput2 = row.querySelector("input[name=order_no\\[\\]]");
        const textInput3 = row.querySelector("input[name=orderIdx\\[\\]]");
        
        if (checkbox.checked) {
          basicOption.push(textInput.value);
          orderNo.push(textInput2.value);
          orderIdx.push(textInput3.value);
        }
    });
    
	
	if(!confirm('선택한 제품을 장바구니에 담으시겠습니까?')){
		return;
	}
	
	let tMode= "addCart";
	
	var apiUrl=common_var.goods_template_url+"/proc/myInven-detail.exec.php";
	
	data = {"goods_idx":goods_idx, "basicOption":basicOption, "tMode":tMode, "sType":"cart", "orderNo":orderNo, "orderIdx":orderIdx, "userId":"<?php echo $currUserID;?>"}
   	jQuery.ajax({
		type: 'post'
		, async: false
		, url: apiUrl
		, data: data
		, success: function(data){
			var response = data.split("|||");
			if(jQuery.trim(response[0]) == "success"){
				if(confirm('상품이 장바구니에 저장되었습니다.   \n장바구니를 확인 하시겠습니까?')){
					window.location.href="<?php echo esc_url( home_url( '/' ) ); ?>?bbsePage=cart";
				}
				else{
					window.location.href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=myInven";
				}
			}else if(jQuery.trim(response[0]) == "exsitCart"){
				alert('장바구니에 담겨있는 상품이 있습니다.');
				location.reload();
			}else if(jQuery.trim(response[0])== "notExsitGoods"){
				alert('존재하지 않는 상품이 포함되어 있습니다.');
				location.reload();
			}else{
				alert('서버와의 통신이 실패했습니다.');
			}
		}
		, error: function(data, status, err){
			alert('서버와의 통신이 실패했습니다.');
		}
	});
}

function deleteMyInven(){

	let apiUrl=common_var.goods_template_url+"/proc/myInven-detail.exec.php";
	let chkarr = [];
	let invenIdxArr = [];
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
        const textInput = row.querySelector("input[name=invenIdx\\[\\]]");
        
        if (checkbox.checked) {
          invenIdxArr.push(textInput.value);
        }
    });
	
	if(!confirm("선택된 제품 " + chkarr.length + "건을 삭제하시겠습니까?")){
		return;
	}
	
	jQuery.ajax({
		type: 'post', 
		async: false, 
		//url: apiUrl,
		url: apiUrl,
		data: {chkarr:chkarr, invenIdxArr:invenIdxArr, tMode:"delete"}, 
		success: function(data){
			var result = data.split("|||"); 
			if(result['0'] == "success"){
				alert(result['1']+ "건이 삭제되었습니다.   ");
				window.location.href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=myInven";
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

let chkarr = [];

let notice = [];
function changeCheckbox(req, idx){

	if(!req.checked){
		let id = "#noticeCount_" + idx;
		jQuery(id).prop("disabled", true);
		value = jQuery(id).val();
		chkarr.pop(idx);
		//notice.pop(value);
        
	}else{
		let id = "#noticeCount_" + idx;
		jQuery(id).prop("disabled", false);
		value = jQuery(id).val();
		chkarr.push(idx);
		//notice.push(value); 
	}
}

function changeNoticeQuantity(){
	
	if(!confirm('알림 수량을 변경 하시겠습니까?')){
		return;
	}
	
	for(let i =0; i<chkarr.length; i++){
    	let id = "#noticeCount_" + chkarr[i];
    	value = jQuery(id).val();
    	notice.push(value); 
	}
			
	var apiUrl=common_var.goods_template_url+"/proc/myInven-detail.exec.php";
	
    data = {"idxs":chkarr, "notice":notice, "tMode":"Change"}
   	jQuery.ajax({
		type: 'post'
		, async: false
		, url: apiUrl
		, data: data
		, success: function(data){
			var response = data.split("|||");
			if(jQuery.trim(response[0]) == "success"){
				alert('수량이 변경되었습니다.');
				location.reload();
			}else{
				alert('서버와의 통신이 실패했습니다.');
			}
		}
		, error: function(data, status, err){
			alert('서버와의 통신이 실패했습니다.');
		}
	});		
}

function order(){
	
}

</script>
	<h2 class="page_title">재고현황</h2>
	<input type="hidden" name="sType" id="sType" value="1">
	<div>
		<div style="float:left;">
			<span>위치</span>
			<select>
				<option>위치</option>
			</select>
		</div>
		<div class="bb_btn_area">
			<button class="bb_btn shadow" style="float:right; width: 100px; height: 30px; color: blue" type="button" onclick="changeNoticeQuantity();">수량변경</button>
			<button class="bb_btn shadow" style="float:right; width: 100px; height: 30px;" type="button" onclick="deleteMyInven();">항목삭제</button>
			<button class="bb_btn shadow" style="float:right; width: 100px; height: 30px;" type="button"><a href="<?php echo esc_url( home_url( '/' ) ); ?>?bbseMy=inven-detail" style="color: red;">항목추가</a></button>
      	</div>
	</div>
			
	<form name="invenFrm" id="invenFrm">
	<div class="">
		<div class="tb_dp_list mobileHidden">
			<table>
				<caption>재고관리</caption>
				<colgroup>
					<col style="width:5%;" />
					<col style="width:28%;" />
					<col style="width:20%;" />
					<col style="width:5%;" />
					<col style="width:7%;" />
					<col style="width:10%;" />
					<col style="width:10%;" />
				</colgroup>
				<thead>
					<tr>
						<th scope="col">선택</th>
						<th scope="col">제품명</th>
						<th scope="col">옵션명</th>
						<th scope="col">알림설정</th>
						<th scope="col">재고수량</th>
						<th scope="col">주문상태</th>
						<th scope="col">주문</th>
					</tr>
				</thead>
				<tbody>
				<?php 
				if(sizeof($result) > '0'){
				    foreach($result as $i=>$data){
				        $currentOrderDetail = $wpdb->get_results("select * from bbse_commerce_order_detail where goods_idx = '".$data->goods_idx."'");
				        foreach ($currentOrderDetail as $j=>$detail){
				            $currentOrder = $wpdb->get_row("select * from bbse_commerce_order where order_no = '".$detail->order_no."' and order_status in ('DE','OE')"); //배송완료, 구매확정 건 오더
				            if($currentOrder != null){
				                break;
				            }
				        }
					?>
					<tr>
						<td style="text-align:center;" class="item">
							<input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->goods_idx;?>" onchange="changeCheckbox(this,'<?php echo $data->idx;?>');"/>
							<!--  <input type="checkbox" name="check[]" id="check[]" value="<?php echo $data->goods_idx;?>"/>-->
							<input type="hidden" name="invenIdx[]" id="invenIdx[]" value="<?php echo $data->idx;?>"/>
							<input type="hidden"  name="basic_option_title[]" id="basic_option_title[]" value="<?php echo $data->goods_option_title;?>"/>
							<input type="hidden"  name="order_no[]" id="order_no[]" value="<?php echo $detail->order_no;?>"/>
							<input type="hidden"  name="orderIdx[]" id="orderIdx[]" value="<?php echo $detail->idx;?>"/>
						</td>
						<td><?php echo $data->goods_name;?></td>
						<td><?php echo $data->goods_option_title;?></td>
						<td><input type="text" style="color: blue; width:30px; text-align: right;" id="noticeCount_<?php echo $data->idx;?>" value="<?php echo $data->notice_count;?>" disabled/></td>
						<td><span style="color: red;"><?php echo $data->current_count;?></span></td>
						<?php 
				    
						$newOrderList = $wpdb->get_results("select * from bbse_commerce_order where user_id ='".$currUserID."' and order_status in ('PR','PE','DR','DI')"); //입금대기 결제완료, 배송준비, 배송중 주문 건
						foreach ($newOrderList as $k=>$newOrder){
						    $newOrderDetailList = $wpdb->get_results("select * from bbse_commerce_order_detail where order_no ='".$newOrder->order_no."'"); //입금대기 결제완료, 배송준비, 배송중 주문 건
						    foreach ($newOrderDetailList as $z=>$newDetailOrder){
						        $option = unserialize($newDetailOrder->goods_option_basic);
						        if($data->goods_idx == $newDetailOrder->goods_idx){
						            $optionTitles = $option['goods_option_title'];
						            foreach ($optionTitles as $optiontitle){
						                if($optiontitle == $data->goods_option_title){
						            ?>
    							<td><?php $stauts = $newOrder->order_status;
    						          if($stauts == 'PR'){ echo "입금대기";}
    						          else if($stauts == 'PE'){ echo "결제완료";}
    						          else if($stauts == 'DR'){ echo "배송준비";}
    						          else if($stauts == 'DI'){ echo "배송중";}
    						          else { echo "-";}
    						            } 
						            }
						        }
						      ?></td>
								<?php 
						        }
						    }
						    ?>
    						<?php if($data->notice_count > $data->current_count && $stauts!='PR' && $stauts != 'PE' && $stauts!='DR' && $stauts !='DI' ){?>
    						<td><button type="button" class="bb_btn shadow" style="float:right; width: 100px; height: 30px;">주문</button></td>
    						<?php }else{?>
    						<td><span style="color:red; font-size:14px;">-</span></td>
    						<?php }?>
					<?php }?>						    
					</tr>
				<?php
				}else{?>
					<tr style="text-align: center;"><td colspan="6">알림 설정 중인 재고가 없습니다.</td></tr>
				<?php }?>
				</tbody>
			</table>
		</div>
		<div class="bb_btn_area">
			<button type="button" onClick="go_cart();" class="bb_btn cus_solid"><strong class="big">장바구니담기</strong></button>
      	</div>
	</div>
	</form>
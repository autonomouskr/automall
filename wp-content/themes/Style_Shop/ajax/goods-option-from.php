<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

global $theme_shortname,$current_user;

$cartType=$_REQUEST['cartType'];
$cartIdx=$_REQUEST['cartIdx'];

$cart = $wpdb->get_row("SELECT * FROM bbse_commerce_cart WHERE idx='".$cartIdx."' AND cart_kind='".$cartType."'");
$bbseGoods=$cart->goods_idx;

$goods = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$bbseGoods."'");

$soldout = goodsSoldoutCheck($goods); //품절체크

$imageList=explode(",",$goods->goods_add_img);

if($goods->goods_basic_img) {
	$basicBigImg = wp_get_attachment_image_src($goods->goods_basic_img,"goodsimage4");
	$basicFullImg = wp_get_attachment_image_src($goods->goods_basic_img,"full");
}
else{
	if(sizeof($imageList)>'0') $basicBigImg=wp_get_attachment_image_src($imageList['0'],"goodsimage3");
	else $basicBigImg['0']=BBSE_COMMERCE_PLUGIN_WEB_URL."images/image_not_exist.jpg";
}

if(is_user_logged_in()) {
	$myInfo=bbse_get_user_information();
	$memPrice=unserialize($goods->goods_member_price);
}

if($myInfo->user_id && $myInfo->user_class>2 && $myInfo->use_sale=='Y'){
	$salePrice=$goods->goods_price;
	for($m=0;$m<sizeof($memPrice['goods_member_price']);$m++){
	    if($memPrice['goods_member_level'][$m]==$myInfo->user_class) {
	        $goods_vat = $memPrice['goods_vat'][$m];
	        $salePrice=$memPrice['goods_member_price'][$m]+$goods_vat;
	        $cPrice = $memPrice['goods_consumer_price'][$m];
	        $savePrice=$cPrice-$salePrice;
	    }
	}
	
	$myClassName="<span class=\"special_tag\">".$myInfo->class_name."</span>";
}else{
	//$salePrice=$goods->goods_price;
	//$savePrice=$goods->goods_consumer_price-$goods->goods_price;
    $salePrice=0;
	$savePrice=0;
	$myClassName="";
}

$salePercent=round((1-($salePrice/$cPrice))*100,1);

$addFields=unserialize($goods->goods_add_field);

$optBasic=unserialize($goods->goods_option_basic);

$optAdd=unserialize($goods->goods_option_add);
$optAddFlag='0';
for($b=1;$b<=$optAdd['goods_add_option_count'];$b++){
	if($optAdd['goods_add_'.$b.'_use']=='on') $optAddFlag++;
}

$confData=$wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
$deliveryInfo=unserialize($confData->config_data);

$cartBasicOption=unserialize($cart->goods_option_basic);
$cartAddOption=unserialize($cart->goods_option_add);

if($cartType=='C'){
	$basicTotalPrice='0';
	$addTotalPrice='0';

	for($z=0;$z<sizeof($cartBasicOption['goods_option_title']);$z++){
		if(trim($cartBasicOption['goods_option_title'][$z])){
			$basicOptData = $wpdb->get_row("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title='".trim($cartBasicOption['goods_option_title'][$z])."'");

			$basicTotalPrice +=($salePrice+$basicOptData->goods_option_item_overprice)*$cartBasicOption['goods_option_count'][$z];
		}
	}

	for($k=0;$k<sizeof($cartAddOption['goods_add_title']);$k++){
		if(trim($cartAddOption['goods_add_title'][$k])){
			for($x=1;$x<=$optAdd['goods_add_option_count'];$x++){
				for($y=0;$y<$optAdd['goods_add_'.$x.'_item_count'];$y++){
					if($cartAddOption['goods_add_title'][$k]==$optAdd['goods_add_'.$x.'_item'][$y]){
						$addGoodsPrice=$optAdd['goods_add_'.$x.'_item_overprice'][$y];
					}
				}
			}
			$addTotalPrice +=$addGoodsPrice*$cartAddOption['goods_add_count'][$k];
		}
	}

	$totalCartPrice=$basicTotalPrice+$addTotalPrice;
}
else{
	 if($goods->goods_option_basic && ($optBasic['goods_option_1_count']>'0' || $optBasic['goods_option_2_count']>'0'))$totalCartPrice='0';
	 else $totalCartPrice=$salePrice;
}
?>
  <div class="layerBox">
    <div class="boxTitle">
      <?php echo ($cartType=='C')?"주문사항 추가/변경":"장바구니 상품옵션 선택";?>

      <button class="layerClose"><span>닫기</span></button>
    </div>
    <div class="boxContent">
      <div class="product_info flow">
<script language="javascript">

// 장바구니 팝업 옵션변경 (시작)
  var cart_basicOptChange = function(oCnt,tOpt,gIdx,oData){ // 첫번째 옵션 변경 : 옵션수(2,1), 옵션번호(1,2), 상품idx, 옵션값
	if(oCnt==2 && tOpt==1){
		var apiUrl=jQuery("#goods_template_url").val()+"/proc/order-cart.exec.php";
		var tMode="secondOption";
		if(oData){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: apiUrl, 
				data: {tMode:tMode, gIdx:gIdx, oData:oData}, 
				success: function(data){
					var result = data.split("|||"); 
					if(result[0] == "success"){
						jQuery('#basicOption_2_List').html(result[1]);
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
		else{
			jQuery('#basicOption_2_List').html("<select name=\"basicOption_2\" id=\"basicOption_2\"><option value=\"\">::: 옵션선택 :::</option></select>");
		}
	}
	else{
		if(oData){
			var goods_price=jQuery("#goods_price").val(); // 상품금액
			var goods_TotalPrice=jQuery("#goods_total_price").val(); // 총 합계금액(판매가+옵션금액+추가상품금액)
			var basic_optOverPrice=jQuery("#basicOption_"+tOpt+" option:selected").data('overprice'); // 선택한 옵션의 추가금액
			var basic_optCount=jQuery("#basicOption_"+tOpt+" option:selected").data('count'); // 선택한 옵션의 재고수량
			var basic_optStock="";
			if(jQuery("#goods_count_flag").val()=='option_count') basic_optStock=" data-stock=\""+basic_optCount+"\"";

			if(oCnt==2){
				var basic_optName=jQuery("#basicOption_1").val()+" / "+jQuery("#basicOption_2").val(); // 1차옵션/2차옵션
			}
			else{
				var basic_optName=jQuery("#basicOption_"+tOpt).val(); // 1차옵션/2차옵션
			}

			var goods_optTotal=parseInt(goods_price)+parseInt(basic_optOverPrice); // 판매금액 + 옵션금액
			var new_TotalPrice=parseInt(goods_TotalPrice)+parseInt(goods_optTotal); // 총 합계금액(기존 총 합계금액 + 선택금액(판매금액 + 옵션금액))
			var rndId=parseInt(Math.random()*Math.pow(10,16));
			if(jQuery("#sOpt_"+rndId).length>0) rndId=parseInt(Math.random()*Math.pow(10,17));

			var ist=jQuery("input[id=goods_option_title\\[\\]]").length;
			for(i=0;i<ist;i++){
				if(basic_optName==jQuery("input[id=goods_option_title\\[\\]]").eq(i).val()){
					alert("이미 선택 된 옵션입니다.   ");
					jQuery("#basicOption_"+tOpt).val("");
					return;
				}
			}

			var strList="<li class=\"add_opt\" id=\"sOpt_"+rndId+"\""+basic_optStock+"\">"
							+"	<div class=\"bb_opt_name\">"+basic_optName+" (+ "+GetCommaValue(basic_optOverPrice)+"원)<input type=\"hidden\" name=\"goods_basic_title[]\" id=\"goods_option_title[]\" value=\""+basic_optName+"\" /></div>"
							+"	<div class=\"bb_opt_price\">"
							+"	  <div class=\"bb_count\">"
							+"		<button type=\"button\" onClick=\"cart_changeCount('basic','minus','"+rndId+"',"+goods_optTotal+");\" class=\"bb_minus\"><span>수량감소</span></button>"
							+"		<button type=\"button\" onClick=\"cart_changeCount('basic','plus','"+rndId+"',"+goods_optTotal+");\" class=\"bb_plus\"><span>수량증가</span></button>"
							+"		<label class=\"blind\" for=\"dummyID380\">수량</label><input type=\"text\" name=\"goods_basic_count[]\" id=\"goods_basic_count[]\" value=\"1\" readonly />"
							+"	  </div>"
							+"	  <em id=\"sOpt_unit_"+rndId+"\" class=\"bb_pri\">"+GetCommaValue(goods_optTotal)+"원</em>"
							+"	  <button type=\"button\" class=\"bb_opt_del\" onClick=\"cart_option_remove('basic','2',"+rndId+","+goods_optTotal+");\"><span>선택옵션 삭제</span></button>"
							+"	</div>"
						  +"</li>";

			jQuery("#goods_basic_option_list").css("display","block");
			jQuery("#goods_basic_option_list").append(strList);

			jQuery("#goods_total_price").val(new_TotalPrice);

			if(jQuery("#view_total_price").css("display")=='none'){
				jQuery("#view_total_price").css("display","block");
			}
			jQuery("#view_total_price").html("총 합계금액 <strong>"+GetCommaValue(new_TotalPrice)+"</strong>원");
		}
	}
  }

  // 추가상품 선택
  var cart_addOptChange = function(tOpt,oData){ // 옵션번호(1,2), 옵션값
	if(oData){
		var goods_TotalPrice=jQuery("#goods_total_price").val(); // 총 합계금액
		var add_goodsPrice=jQuery("#addOption_"+tOpt+" option:selected").data('price'); // 선택한 추가상품의 금액
		var add_goodsName=jQuery("#addOption_"+tOpt).val(); // 추가상품명
		var oChoice=jQuery("#addOption_"+tOpt).data('choice');

		var new_TotalPrice=parseInt(goods_TotalPrice)+parseInt(add_goodsPrice); // 총 합계금액(기존 총 합계금액 + 선택금액(판매금액 + 옵션금액))

		var rndId=parseInt(Math.random()*Math.pow(10,16));
		if(jQuery("#sOpt_"+rndId).length>0) rndId=parseInt(Math.random()*Math.pow(10,17));

		var ist=jQuery("input[id=goods_add_title\\[\\]]").length;
		for(i=0;i<ist;i++){
			if(add_goodsName==jQuery("input[id=goods_add_title\\[\\]]").eq(i).val()){
				alert("이미 선택 된 추가상품입니다.   ");
				jQuery("#addOption_"+tOpt).val("");
				return;
			}
		}

		var strList="<li class=\"add_opt\" id=\"sOpt_"+rndId+"\" data-choice=\""+oChoice+"_"+tOpt+"\">"
						+"	<div class=\"bb_opt_name\">"+add_goodsName+" ("+GetCommaValue(add_goodsPrice)+"원)<input type=\"hidden\" name=\"goods_add_title[]\" id=\"goods_add_title[]\" value=\""+add_goodsName+"\" /><input type=\"hidden\" name=\"goods_add_price[]\" id=\"goods_add_price[]\" value=\""+add_goodsPrice+"\" /></div>"
						+"	<div class=\"bb_opt_price\">"
						+"	  <div class=\"bb_count\">"
						+"		<button type=\"button\" onClick=\"cart_changeCount('add','minus','"+rndId+"',"+add_goodsPrice+");\" class=\"bb_minus\"><span>수량감소</span></button>"
						+"		<button type=\"button\" onClick=\"cart_changeCount('add','plus','"+rndId+"',"+add_goodsPrice+");\" class=\"bb_plus\"><span>수량증가</span></button>"
						+"		<label class=\"blind\" for=\"dummyID380\">수량</label><input type=\"text\" name=\"goods_add_count[]\" id=\"goods_add_count[]\" value=\"1\" readonly />"
						+"	  </div>"
						+"	  <em id=\"sOpt_unit_"+rndId+"\" class=\"bb_pri\">"+GetCommaValue(add_goodsPrice)+"원</em>"
						+"	  <button type=\"button\" class=\"bb_opt_del\" onClick=\"cart_option_remove('add','"+tOpt+"',"+rndId+","+add_goodsPrice+");\"><span>선택옵션 삭제</span></button>"
						+"	</div>"
					  +"</li>";

		jQuery("#goods_add_option_list").css("display","block");
		jQuery("#goods_add_option_list").append(strList);

		jQuery("#goods_total_price").val(new_TotalPrice);

		if(jQuery("#view_total_price").css("display")=='none'){
			jQuery("#view_total_price").css("display","block");
		}
		jQuery("#view_total_price").html("총 합계금액 <strong>"+GetCommaValue(new_TotalPrice)+"</strong>원");
	}
  }

  // 옵션 삭제
  var cart_option_remove = function(oType,tOpt,rndId,gOptTotal){// 옵션번호(1,2), object ID, 차감금액
		var goods_TotalPrice=jQuery("#goods_total_price").val(); // 총 합계금액(판매가+옵션금액+추가상품금액)

		var optCount=jQuery("#sOpt_"+rndId+" input[id=goods_"+oType+"_count\\[\\]]").val();
		var new_TotalPrice=parseInt(goods_TotalPrice)-(parseInt(gOptTotal)*parseInt(optCount));
		jQuery("#sOpt_"+rndId).remove();

		if(jQuery("#sOpt_"+rndId).length<=0){
			if(new_TotalPrice<=0){
				jQuery("#goods_basic_option_list").html("");
				jQuery("#goods_basic_option_list").css("display","none");
				jQuery("#goods_add_option_list").html("");
				jQuery("#goods_add_option_list").css("display","none");

				jQuery("#view_total_price").html("");
				jQuery("#view_total_price").css("display","none");
				jQuery("#goods_total_price").val("0");

				for(i=1;i<3;i++){
					if(jQuery("#basicOption_"+i).length>0) jQuery("#basicOption_"+i).val("");
				}

				for(i=1;i<6;i++){
					if(jQuery("#addOption_"+i).length>0) jQuery("#addOption_"+i).val("");
				}
			}
			else{
				jQuery("#goods_total_price").val(new_TotalPrice);
				if(jQuery("#view_total_price").css("display")=='none'){
					jQuery("#view_total_price").css("display","block");
				}

				jQuery("#view_total_price").html("총 합계금액 <strong>"+GetCommaValue(new_TotalPrice)+"</strong>원");
			}

			jQuery("#"+oType+"Option_"+tOpt).val("");

			if(jQuery("#goods_basic_option_list > li").size()<=0){
				jQuery("#goods_basic_option_list").css("display","none");
				if(jQuery("#basicOption_1")) jQuery("#basicOption_1").val("");
				if(jQuery("#basicOption_2")) jQuery('#basicOption_2_List').html("<select name=\"basicOption_2\" id=\"basicOption_2\"><option value=\"\">::: 옵션선택 :::</option></select>");
			}

			if(jQuery("#goods_add_option_list > li").size()<=0){
				jQuery("#goods_add_option_list").css("display","none");
				for(i=1;i<6;i++){
					if(jQuery("#addOption_"+i)) jQuery("#addOption_"+i).val("");
				}
			}
		}
  }

  // 수량변경
  var cart_changeCount = function(cType,tOpt,rndId,gOptTotal){
		var goods_TotalPrice=jQuery("#goods_total_price").val();
		var optCount=jQuery("#sOpt_"+rndId+" input[id=goods_"+cType+"_count\\[\\]]").val();

		if(tOpt=="plus"){
			//총 수량이 1회 최대수량보다 크면 false
			var max_cnt = parseInt(jQuery('#max_cnt').val());
			if(max_cnt > 0){
				//현재 수량
				var total_cnt = 0;
				jQuery('input[name="goods_basic_count[]"]').each(function(){
					total_cnt += parseInt(jQuery(this).val());
				});
				console.log(total_cnt+'/'+max_cnt);
				if(total_cnt >= max_cnt){
					alert("1회 구매 가능 개수 초과");
					return false;	
				}
			}
			var new_TotalPrice=parseInt(goods_TotalPrice)+parseInt(gOptTotal);
			var new_optCount=parseInt(optCount)+1;

			if(cType=='basic' && jQuery("#goods_count_flag").val()!='unlimit'){
				var oStock=jQuery("#sOpt_"+rndId).data('stock');
				if(new_optCount>oStock){
					alert("수량을 재고수량("+oStock+"개) 이내로 선택해 주세요.     ");
					return;
				}
			}
		}
		else{
			var new_TotalPrice=parseInt(goods_TotalPrice)-parseInt(gOptTotal);
			var new_optCount=parseInt(optCount)-1;
		}

		if(new_optCount<='0' || new_TotalPrice<='0'){
			alert("주문 수량을 '1' 이상으로 선택해 주세요.    ");
			return;
		}

		jQuery("#sOpt_"+rndId+" input[id=goods_"+cType+"_count\\[\\]]").val(new_optCount);
		jQuery("#sOpt_unit_"+rndId).html(GetCommaValue(new_optCount*gOptTotal)+"원");
		jQuery("#goods_total_price").val(new_TotalPrice);

		if(jQuery("#view_total_price").css("display")=='none'){
			jQuery("#view_total_price").css("display","block");
		}

		jQuery("#view_total_price").html("총 합계금액 <strong>"+GetCommaValue(new_TotalPrice)+"</strong>원");
  }

  // 장바구니 변경
  var go_cart = function(sType){
	var goods_TotalPrice=jQuery("#goods_total_price").val();
	var oChoice="";
	var oTitle="";
	var liExist=false;
	var compStr="";
	var aCnt=jQuery("#goods_add_option_list > li").size();
	var apiUrl=jQuery("#goods_template_url").val()+"/proc/order-cart.exec.php";
	var homeUrl=jQuery("#home_url").val();
	var tMode="";

	if(((jQuery("#basicOption_1").length>0 || jQuery("#basicOption_2")>0) && jQuery("#goods_basic_option_list > li").size()<=0) || goods_TotalPrice<=0){
		alert("상품 옵션을 선택해 주세요.     ");
		return;
	}
	for(i=1;i<6;i++){
		oChoice=jQuery("#addOption_"+i).data('choice');
		oTitle=jQuery("#addOption_"+i).data('title');

		if(oChoice=="required"){
			liExist=false;
			compStr="required_"+i;
			for(j=0;j<aCnt;j++){
				if(compStr==jQuery("#goods_add_option_list > li").eq(j).data('choice')) liExist=true;
			}

			if(!liExist){
				alert("필수 추가상품("+oTitle+")을 선택해 주세요.         ");
				return;
			}
		}
	}

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: jQuery("#popCartFrm").serialize(), 
		success: function(data){
			//alert(data);
			var result = data.split("|||"); 

			if(result[0] == "success"){
				if(sType=='C'){
					window.location.href=homeUrl+"/?bbsePage=cart";
				}
				else if(sType=='W'){
					if(confirm('상품이 장바구니에 저장되었습니다.   \n장바구니를 확인 하시겠습니까?')){
						window.location.href=homeUrl+"/?bbsePage=cart";
					}
					else{
						parent.popup_remove_wishlist(jQuery("#cart_idx").val());
						layerClose();
					}
				}
			}
			else if(result[0] == "notExistGoods"){
				alert("존재하지 않는 상품정보입니다.   ");
			}
			else if(result[0] == "loginError"){
				alert("회원전용 서비스 입니다. 로그인 후 이용해 주세요.   ");
			}
			else if(result[0] == "existWishlist"){
				if(confirm('찜리스트에 존재하는 상품입니다.   \n찜리스트를 확인 하시겠습니까?')){
					window.location.href=homeUrl+"/?bbseMy=interest";
				}
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

// 장바구니 팝업 옵션변경 (끝)
</script>
	<!--  상위 템플릿 <div class="page_cont"></div> 사이에 불려감 -->
		  <form name="popCartFrm" id="popCartFrm">
		  <input type="hidden" name="tMode" id="tMode" value="<?php echo ($cartType=='W')?"addCart":"modifyCart";?>">
		  <input type="hidden" name="cart_idx" id="cart_idx" value="<?php echo $cart->idx;?>">
		  <input type="hidden" name="goods_idx" id="goods_idx" value="<?php echo $goods->idx;?>">
		  <input type="hidden" name="home_url" id="home_url" value="<?php echo home_url();?>" />
		  <input type="hidden" name="goods_template_url" id="goods_template_url" value="<?php echo bloginfo('template_url');?>" />
		  <input type="hidden" name="goods_count_flag" id="goods_count_flag" value="<?php echo $goods->goods_count_flag;?>" />
		  <input type="hidden" name="goods_price" id="goods_price" value="<?php echo $salePrice;?>" />
		  <input type="hidden" name="goods_total_price" id="goods_total_price" value="<?php echo $totalCartPrice;?>" />

			<table class="">
			  <caption>상품 정보 표</caption>
			  <colgroup>
				<col style="width:110px;">
				<col style="width:auto;">
			  </colgroup>
			  <tbody>
				<tr class="basic_info">
				  <td><img src="<?php echo $basicBigImg['0'];?>" alt="<?php echo $goods->goods_name;?> 상품 이미지" style="width: 90px;height: 90px;vertical-align:bottom;" /></td>
				  <td>
					<a href="<?php echo home_url()."/?bbseGoods=".$goods->idx; ?>" class="subj"><?php echo $goods->goods_name;?></a>
				  	<div class="bb_price_info">
						<span class="sale_per"><strong><?php echo $salePercent;?></strong>%</span>
						<del><?php echo number_format($cPrice)?></del>
						<strong class="bb_price"><?php echo number_format($salePrice)?><span>원</span></strong>
					</div>
				  </td>
				</tr>
			  <?php if($goods->goods_count_flag=='goods_count' && $goods->goods_count_view=='on'){?>
				<tr class="bottom_line">
				  <th scope="row">재고수량</th>
				  <td><?php echo number_format($goods->goods_count);?>개 남음</td>
				</tr>
			  <?php }?>
			<?php
			if($soldout){
			?>
			  </tbody>
			</table><!--//상품 정보 표 -->
			<div style="margin-top:50px;text-align:center;">
				<div class="bb_order_btn">
				  <button type="button" class="bb_btn cus_fill"><strong class="big">품절된 상품입니다.</strong></button>
				</div><!--//결제 관련 버튼 -->
			</div>

		<?php }else{?>
			  <?php if($goods->goods_option_basic && ($optBasic['goods_option_1_count']>'0' || $optBasic['goods_option_2_count']>'0')){?>
					<tr>
					  <th scope="col" colspan="2" class="s_title"><strong>옵션선택</strong></th>
					</tr>
				  <?php if($optBasic['goods_option_1_count']>'0' && $optBasic['goods_option_2_count']>'0'){?>
					<tr>
					  <th scope="row"><label for="bbOpt11"><?php echo $optBasic['goods_option_1_title'];?></label></th>
					  <td><input type="hidden" name="optCnt" id="optCnt" value="2" />
						<select name="basicOption_1" id="basicOption_1" onChange="cart_basicOptChange(2,1,'<?php echo $goods->idx;?>',this.value);">
							<option value="">::: 옵션선택 :::</option>
					  <?php
							for($p=0;$p<$optBasic['goods_option_1_count'];$p++){
								$optValue=$optStrFlalg="";
								$displayFlalg=true;

								if($goods->goods_count_flag=='option_count'){
									$optTotal_1_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s AND goods_option_item_display='view'", array(like_escape($optBasic['goods_option_1_item'][$p]." /")."%"))); 

									if($optTotal_1_count<='0') $displayFlalg=false;

									$optTotal_2_count = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s AND goods_option_item_count>'0' AND goods_option_item_soldout<>'soldout' AND goods_option_item_display='view'", array(like_escape($optBasic['goods_option_1_item'][$p]." /")."%"))); 

									if($optTotal_2_count<='0'){
										$optValue .="disabled";
										$optStrFlalg .=" | 품절";
									}
								}

								if($displayFlalg) echo "<option value=\"".$optBasic['goods_option_1_item'][$p]."\" ".$optValue.">".$optBasic['goods_option_1_item'][$p].$optStrFlalg."</option>";
							}
					  ?>
						</select>
					  </td>
					</tr>
					<tr>
					  <th scope="row"><label for="bbOpt12"><?php echo $optBasic['goods_option_2_title'];?></label></th>
					  <td>
						<span id="basicOption_2_List">
							<select name="basicOption_2" id="basicOption_2">
							  <option value="">::: 옵션선택 :::</option>
							</select>
						</span>
					  </td>
					</tr>
			  <?php
				  }
				  elseif($optBasic['goods_option_1_count']>'0'){
			  ?>
					<tr>
					  <th scope="row"><label for="bbOpt11"><?php echo $optBasic['goods_option_1_title'];?></label></th>
					  <td>
						<select name="basicOption_1" id="basicOption_1" onChange="cart_basicOptChange(1,1,'<?php echo $goods->idx;?>',this.value);">
							<option value="">::: 옵션선택 :::</option>
					  <?php
							for($p=0;$p<$optBasic['goods_option_1_count'];$p++){
								$optValue=$optStrFlalg=$optValue="";

								$optTotal_1_Data = $wpdb->get_row($wpdb->prepare("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s", array(like_escape($optBasic['goods_option_1_item'][$p])))); 

								if($optTotal_1_Data->goods_option_item_display=='view'){
									$optStrFlalg .=" (+".number_format($optTotal_1_Data->goods_option_item_overprice)."원)";

									if($goods->goods_count_flag=='option_count'){
										if($optTotal_1_Data->goods_option_item_count<='0' || $optTotal_1_Data->goods_option_item_soldout=='soldout'){
											$optValue .="disabled";
											$optStrFlalg .=" | 품절";
										}
										else{
											if($goods->goods_count_view=='on'){
												$optStrFlalg .=" | ".number_format($optTotal_1_Data->goods_option_item_count)."개 남음";
											}

											$optValue .="data-overprice=\"".$optTotal_1_Data->goods_option_item_overprice."\" data-count=\"".$optTotal_1_Data->goods_option_item_count."\"";
										}
									}
									else{
										$optValue .="data-overprice=\"".$optTotal_1_Data->goods_option_item_overprice."\"";
									}

									echo "<option value=\"".$optBasic['goods_option_1_item'][$p]."\" ".$optValue.">".$optBasic['goods_option_1_item'][$p].$optStrFlalg."</option>";
								}
							}
					  ?>
						</select>
					  </td>
					</tr>
			  <?php
				  }
				  elseif($optBasic['goods_option_2_count']>'0'){
			  ?>
					<tr>
					  <th scope="row"><label for="bbOpt11"><?php echo $optBasic['goods_option_2_title'];?></label></th>
					  <td>
						<select name="basicOption_2" id="basicOption_2" onChange="cart_basicOptChange(1,2,'<?php echo $goods->idx;?>',this.value);">
							<option value="">::: 옵션선택 :::</option>
					  <?php
							for($p=0;$p<$optBasic['goods_option_2_count'];$p++){
								$optValue=$optStrFlalg="";

								$optTotal_2_Data = $wpdb->get_row($wpdb->prepare("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title LIKE %s", array(like_escape($optBasic['goods_option_2_item'][$p])))); 

								if($optTotal_2_Data->goods_option_item_display=='view'){
									$optStrFlalg .=" (+".number_format($optTotal_2_Data->goods_option_item_overprice)."원)";

									if($goods->goods_count_flag=='option_count'){
										if($optTotal_2_Data->goods_option_item_count<='0' || $optTotal_2_Data->goods_option_item_soldout=='soldout'){
											$optValue .="disabled";
											$optStrFlalg .=" | 품절";
										}
										else{
											if($goods->goods_count_view=='on'){
												$optStrFlalg .=" | ".number_format($optTotal_2_Data->goods_option_item_count)."개 남음";
											}

											$optValue .="data-overprice=\"".$optTotal_2_Data->goods_option_item_overprice."\" data-count=\"".$optTotal_2_Data->goods_option_item_count."\"";
										}
									}
									else{
										$optValue .="data-overprice=\"".$optTotal_2_Data->goods_option_item_overprice."\"";
									}

									echo "<option value=\"".$optBasic['goods_option_2_item'][$p]."\" ".$optValue.">".$optBasic['goods_option_2_item'][$p].$optStrFlalg."</option>";
								}
							}
					  ?>
						</select>
					  </td>
					</tr>
			  <?php
				  }
			  }
			  ?>

			  <?php if($goods->goods_option_add && $optAdd['goods_add_option_count']>'0' && $optAddFlag>'0'){?>
					<tr>
					  <th scope="col" colspan="2" class="s_title"><strong>추가상품</strong></th>
					</tr>
				
					<?php
					for($t=1;$t<=$optAdd['goods_add_option_count'];$t++){
						  if($optAdd['goods_add_'.$t.'_use']=='on'){
					?>
							<tr>
							  <th scope="row"><label for="bbOpt21"><?php echo $optAdd['goods_add_'.$t.'_title'];?> <?php echo ($optAdd['goods_add_'.$t.'_choice']=='selection')?"(선택)":"(필수)";?></label></th>
							  <td>
								<select name="addOption_<?php echo $t;?>" id="addOption_<?php echo $t;?>" data-choice="<?php echo $optAdd['goods_add_'.$t.'_choice'];?>" data-title="<?php echo $optAdd['goods_add_'.$t.'_title'];?>" onChange="cart_addOptChange(<?php echo $t;?>,this.value);">
								  <option value="">::: 상품선택 :::</option>
								  <?php 
								  for($z=0;$z<$optAdd['goods_add_'.$t.'_item_count'];$z++){
									  $optAddValue=$optAddStrFlalg="";

									  $optAddStrFlalg .="(".number_format($optAdd['goods_add_'.$t.'_item_overprice'][$z])."원)";
									  if($optAdd['goods_add_'.$t.'_item_display'][$z]=='view'){
										  if($optAdd['goods_add_'.$t.'_item_soldout'][$z]=='soldout'){
											  $optAddValue .="disabled";
											  $optAddStrFlalg .=" | 품절";
										  }
										  else $optAddValue .="data-price=\"".$optAdd['goods_add_'.$t.'_item_overprice'][$z]."\"";

										  echo "<option value=\"".$optAdd['goods_add_'.$t.'_item'][$z]."\" ".$optAddValue.">".$optAdd['goods_add_'.$t.'_item'][$z].$optAddStrFlalg."</option>";
									  }
								  }
								  ?>
								</select> 
							  </td>
							</tr>						
			<?php
						  }
					}
			  }
			  ?>
					  </tbody>
					</table><!--//상품 정보 표 -->

					<h4 class="selected_item">선택한 상품</h4>

					<ul id="goods_basic_option_list" class="selected_opt_list" style="display:<?php echo ((!$goods->goods_option_basic || ($optBasic['goods_option_1_count']<='0' && $optBasic['goods_option_2_count']<='0')) || $cartType=='C')?"block":"none";?>">
					<?php 
					if(!$goods->goods_option_basic || ($optBasic['goods_option_1_count']<='0' && $optBasic['goods_option_2_count']<='0')){
						$rndId=rand(100000,999999);
						if($cartType=='C'){
							$totalPrice=$salePrice*$cartBasicOption['goods_option_count']['0'];
							$optSelCnt=$cartBasicOption['goods_option_count']['0'];
						}
						else{
							$totalPrice=$salePrice;
							$optSelCnt='1';
						}
					?>
					<li class="add_opt" id="sOpt_<?php echo $rndId;?>" data-stock="<?php echo $goods->goods_count;?>">
						<div class="bb_opt_name">상품수량<input type="hidden" name="goods_basic_title[]" id="goods_basic_title[]" value="단일상품" /></div>
						<div class="bb_opt_price">
						  <div class="bb_count">
							<button type="button" onClick="cart_changeCount('basic','minus','<?php echo $rndId;?>',<?php echo $salePrice;?>);" class="bb_minus"><span>수량감소</span></button>
							<button type="button" onClick="cart_changeCount('basic','plus','<?php echo $rndId;?>',<?php echo $salePrice;?>);" class="bb_plus"><span>수량증가</span></button>
							<label class="blind" for="dummyID380">수량</label><input type="text" name="goods_basic_count[]" id="goods_basic_count[]" value="<?php echo $optSelCnt;?>" readonly />
						  </div>
						  <em id="sOpt_unit_<?php echo $rndId;?>" class="bb_pri"><?php echo number_format($totalPrice);?>원</em>
						  <button type="button" class="bb_opt_del" onClick="alert('기본 상품수량은 삭제가 불가능합니다.     ');"><span>선택옵션 삭제</span></button>
						</div>
					  </li>
					<?php 
					}else{
						if($cartType=='C'){
							for($z=0;$z<sizeof($cartBasicOption['goods_option_title']);$z++){
								if(trim($cartBasicOption['goods_option_title'][$z])){
									$rndId=rand(1000000,9999999);
									$basicOptData = $wpdb->get_row("SELECT * FROM bbse_commerce_goods_option WHERE goods_idx='".$goods->idx."' AND goods_option_title='".trim($cartBasicOption['goods_option_title'][$z])."'");
									if($goods->goods_count_flag=='option_count') $basic_optStock=" data-stock=\"".$basicOptData->goods_option_item_count."\"";
									else $basic_optStock="";

									$basePrice=$salePrice+$basicOptData->goods_option_item_overprice;
									$totalPrice=$basePrice*$cartBasicOption['goods_option_count'][$z];
					?>
						<li class="add_opt" id="sOpt_<?php echo $rndId;?>"<?php echo $basic_optStock;?>>
							<div class="bb_opt_name"><?php echo trim($cartBasicOption['goods_option_title'][$z]);?> (+ <?php echo $basicOptData->goods_option_item_overprice;?>원)<input type="hidden" name="goods_basic_title[]" id="goods_option_title[]" value="<?php echo trim($cartBasicOption['goods_option_title'][$z]);?>" /></div>
							<div class="bb_opt_price">
							  <div class="bb_count">
								<button type="button" onClick="cart_changeCount('basic','minus','<?php echo $rndId;?>',<?php echo $basePrice;?>);" class="bb_minus"><span>수량감소</span></button>
								<button type="button" onClick="cart_changeCount('basic','plus','<?php echo $rndId;?>',<?php echo $basePrice;?>);" class="bb_plus"><span>수량증가</span></button>
								<label class="blind" for="dummyID380">수량</label><input type="text" name="goods_basic_count[]" id="goods_basic_count[]" value="<?php echo $cartBasicOption['goods_option_count'][$z];?>" readonly />
							  </div>
							  <em id="sOpt_unit_"+rndId+"" class="bb_pri"><?php echo number_format($totalPrice);?>원</em>
							  <button type="button" class="bb_opt_del" onClick="cart_option_remove('basic','2',<?php echo $rndId;?>,<?php echo $salePrice+$basicOptData->goods_option_item_overprice;?>);"><span>선택옵션 삭제</span></button>
							</div>
						 </li>
					<?php 
								}
							}
						}
					}
					?>
					</ul><!--//선택상품 리스트 -->
					<ul id="goods_add_option_list" class="selected_opt_list" style="display:<?php echo ($cartType=='C' && trim($cartAddOption['goods_add_title']['0']))?"block":"none";?>;">
					<?php 
					for($k=0;$k<sizeof($cartAddOption['goods_add_title']);$k++){
						if(trim($cartAddOption['goods_add_title'][$k])){
							$rndId=rand(10000000,99999999);
							$oChoice="";
							$tOpt="";
							$addGoodsPrice="";

							for($x=1;$x<=$optAdd['goods_add_option_count'];$x++){
								for($y=0;$y<$optAdd['goods_add_'.$x.'_item_count'];$y++){
									if($cartAddOption['goods_add_title'][$k]==$optAdd['goods_add_'.$x.'_item'][$y]){
										$oChoice=$optAdd['goods_add_'.$x.'_choice'];
										$tOpt=$x;
										$addGoodsPrice=$optAdd['goods_add_'.$x.'_item_overprice'][$y];
										break;
									}
								}
							}
							$totalPrice=$addGoodsPrice*$cartAddOption['goods_add_count'][$k];
					?>
						<li class="add_opt" id="sOpt_<?php echo $rndId;?>" data-choice="<?php echo $oChoice;?>_<?php echo $tOpt;?>">
							<div class="bb_opt_name"><?php echo trim($cartAddOption['goods_add_title'][$k]);?> (<?php echo number_format($addGoodsPrice);?>원)<input type="hidden" name="goods_add_title[]" id="goods_add_title[]" value="<?php echo trim($cartAddOption['goods_add_title'][$k]);?>" /><input type="hidden" name="goods_add_price[]" id="goods_add_price[]" value="<?php echo $addGoodsPrice;?>" /></div>
							<div class="bb_opt_price">
							  <div class="bb_count">
								<button type="button" onClick="cart_changeCount('add','minus','<?php echo $rndId;?>',<?php echo $addGoodsPrice;?>);" class="bb_minus"><span>수량감소</span></button>
								<button type="button" onClick="cart_changeCount('add','plus','<?php echo $rndId;?>',<?php echo $addGoodsPrice;?>);" class="bb_plus"><span>수량증가</span></button>
								<label class="blind" for="dummyID380">수량</label><input type="text" name="goods_add_count[]" id="goods_add_count[]" value="<?php echo $cartAddOption['goods_add_count'][$k];?>" readonly />
							  </div>
							  <em id="sOpt_unit_<?php echo $rndId;?>" class="bb_pri"><?php echo number_format($totalPrice);?>원</em>
							  <button type="button" class="bb_opt_del" onClick="cart_option_remove('add','<?php echo $tOpt;?>',<?php echo $rndId;?>,<?php echo $addGoodsPrice;?>);"><span>선택옵션 삭제</span></button>
							</div>
						  </li>
					<?php 
						}
					}
					?>
					</ul><!--//선택상품 리스트 -->
					<input type="hidden" name="max_cnt" id="max_cnt" value="<?php echo $goods->max_cnt; ?>" />
					<p class="bb_total_price" id="view_total_price" style="display:<?php echo ((!$goods->goods_option_basic || ($optBasic['goods_option_1_count']<='0' && $optBasic['goods_option_2_count']<='0')) || $cartType=='C')?"block":"none";?>;">
					  총 합계금액 <strong><?php echo number_format($totalCartPrice);?></strong>원
					</p><!--//총 금액-->
	<?php }?>
		  </form>

        <div class="bb_btn_area">
          <button type="button" class="bb_btn shadow" onclick="go_cart('<?php echo $cartType; ?>')"><strong class="mid c_point"><?php echo ($cartType=='W')?"장바구니 추가":"변경";?></strong></button>
          <button type="button" class="bb_btn shadow ly_close"><strong class="mid">취소</strong></button>
        </div>
      </div><!--//.product_info -->
    </div>
  </div><!-- /layerBox -->

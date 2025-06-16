jQuery(document).ready(function(){
	
	jQuery("input:text[validate-number='true']").bind('keyup keydown change', function() {
		jQuery(this).css("ime-mode", "disabled").val(jQuery(this).val().replace(/[^0-9]/gi,''));
	});

	jQuery("#order_frm").find('input:text').bind('keyup keydown change', (function() {
			var findMsg = jQuery(this).parent().find("#order_alert_msg");
			if(jQuery(this).val() != "") jQuery(findMsg).remove();
		})
	);

	jQuery("#orderAgreeConfirm").bind('click', function() {
		if(jQuery(this).prop("checked") == true) jQuery(this).parent().parent().find('#order_alert_msg').remove();
	});

	jQuery("input[name='pay_how']").bind('click', function () {

		var deviceType = jQuery("#deviceType").val();

		jQuery("#payment_bank_info").hide();

		var pgKind = jQuery(this).val();
		if(pgKind != "B") {

			if(deviceType == "mobile" || deviceType == "tablet") {
				if(pgKind == "C") {var pgExt = "card";var title = "신용카드";}
				else if(pgKind == "V") {var pgExt = "virtual";var title = "가상계좌";}
			}else{
				if(pgKind == "C") {var pgExt = "onlycardself";var title = "신용카드";}
				else if(pgKind == "K") {var pgExt = "onlyicheself";var title = "실시간 계좌이체";}
				else if(pgKind == "V") {var pgExt = "onlyvirtualself";var title = "가상계좌";}
			}

			if(jQuery("#payment_escrow_use").val() == "on") {//use escrow
				var Job = pgExt + "escrow";
			}else{
				var Job = pgExt + "normal";
			}

			jQuery("input[name='Job']").val(Job);
		}else{
			var title = "무통장입금";
			jQuery("#payment_bank_info").show();

		}
		jQuery(this).parent().parent().find("#order_alert_msg").remove();

		jQuery("#pay_how_view").html(title);

	});

	jQuery("input:radio[name='equal_memberinfo']").bind('click', function() {
		if(jQuery(this).val() == "equal") {

			jQuery.ajax({
				url: common_var.goods_template_url + "/proc/user.info.php"
				, type: 'post'
				, data: {user_id:jQuery("input[name='ord_userId']").val()}
				, success: function(response) {
					//alert(response);
					var result = response.split("|||");
					if(result[0] == "success") {
						var zip = result[2];
						var phone = result[5].split("-");
						var hp = result[6].split("-");
						jQuery("input[name='order_name']").val(result[1]);
						jQuery("input[name='order_zip']").val(zip);
						jQuery("input[name='order_addr1']").val(result[3]);
						jQuery("input[name='order_addr2']").val(result[4]);
						jQuery("input[name='order_phone1']").val(phone[0]);
						jQuery("input[name='order_phone2']").val(phone[1]);
						jQuery("input[name='order_phone3']").val(phone[2]);
						jQuery("input[name='order_hp1']").val(hp[0]);
						jQuery("input[name='order_hp2']").val(hp[1]);
						jQuery("input[name='order_hp3']").val(hp[2]);
						jQuery("input[name='order_email']").val(result[7]);
					}else{
						alert("잘못된 접근입니다.");
					}
				}
				, error: function(data, status, err) {
					alert('서버와의 통신이 실패했습니다.');
				  }
			});

		}else{
			jQuery("input[name='order_name']").val("");
			jQuery("input[name='order_zip']").val("");
			jQuery("input[name='order_addr1']").val("");
			jQuery("input[name='order_addr2']").val("");
			jQuery("input[name='order_phone1']").val("");
			jQuery("input[name='order_phone2']").val("");
			jQuery("input[name='order_phone3']").val("");
			jQuery("input[name='order_hp1']").val("");
			jQuery("input[name='order_hp2']").val("");
			jQuery("input[name='order_hp3']").val("");
			jQuery("input[name='order_email']").val("");
		}
		delivery_change('addrChang');
	});

	jQuery("input:radio[name='equal_orderinfo']").bind('click', function() {
		
		if(jQuery(this).val() == "equal") {
			jQuery("input[name='receive_name']").val(jQuery("input[name='order_name']").val());
			jQuery("input[name='receive_zip']").val(jQuery("input[name='order_zip']").val());
			jQuery("input[name='receive_addr1']").val(jQuery("input[name='order_addr1']").val());
			jQuery("input[name='receive_addr2']").val(jQuery("input[name='order_addr2']").val());
			jQuery("input[name='receive_phone1']").val(jQuery("input[name='order_phone1']").val());
			jQuery("input[name='receive_phone2']").val(jQuery("input[name='order_phone2']").val());
			jQuery("input[name='receive_phone3']").val(jQuery("input[name='order_phone3']").val());
			jQuery("input[name='receive_hp1']").val(jQuery("input[name='order_hp1']").val());
			jQuery("input[name='receive_hp2']").val(jQuery("input[name='order_hp2']").val());
			jQuery("input[name='receive_hp3']").val(jQuery("input[name='order_hp3']").val());
		}else{
			jQuery("input[name='receive_name']").val("");
			jQuery("input[name='receive_zip']").val("");
			jQuery("input[name='receive_addr1']").val("");
			jQuery("input[name='receive_addr2']").val("");
			jQuery("input[name='receive_phone1']").val("");
			jQuery("input[name='receive_phone2']").val("");
			jQuery("input[name='receive_phone3']").val("");
			jQuery("input[name='receive_hp1']").val("");
			jQuery("input[name='receive_hp2']").val("");
			jQuery("input[name='receive_hp3']").val("");
		}
		delivery_change('addrChang');
	});

	jQuery("#use_earn").bind('blur', function() {// 적립금 사용 단위 적용
		delivery_change();
		var earn_use_unit = parseInt(jQuery("#earn_use_unit").val());
		var use_mileage = jQuery(this).val();
		if(earn_use_unit && use_mileage.length > 2) {
			jQuery(this).val(Math.floor(use_mileage / earn_use_unit) * earn_use_unit);
		}
		mileage_apply();
	});

	jQuery("#use_all_earn").bind('click', function () {//적립금 모두 사용
		var mileage = parseInt(jQuery("#mileage").val());
		var earn_max_percent = parseInt(jQuery("#earn_max_percent").val());
		var earn_use_unit = parseInt(jQuery("#earn_use_unit").val());
		var goods_option_price = parseInt(jQuery("#goods_option_price").val());
		var goods_add_price = parseInt(jQuery("#goods_add_price").val());
		var delivery_price = parseInt(jQuery("#delivery_price").val());
		var delivery_add_price = parseInt(jQuery("#delivery_add_price").val());
		var total_price = goods_option_price + goods_add_price;// + (delivery_price + delivery_add_price)
		var usable_mileage = parseInt(total_price * (earn_max_percent/100));
		if(usable_mileage < mileage) {
			var real_use_mileage = usable_mileage;
		}else{
			var real_use_mileage = mileage;
		}
		real_use_mileage = Math.floor(real_use_mileage / earn_use_unit) * earn_use_unit;
		if(jQuery("#use_all_earn").is(":checked") == true) {
			jQuery("#use_earn").val(real_use_mileage);
		}else{
			jQuery("#use_earn").val("");
		}
		mileage_apply();
	});

	jQuery(".pay-action").click(function() {//결제하기
		var validateFlag = false;

		jQuery("#order_frm").find('#order_alert_msg').remove();
		if(mileage_apply() == false) return;

		var focusElement = "";
		jQuery("#order_frm").find('input:text').each(function (i,e) {

			if( (jQuery(e).attr("name") == "order_email" || jQuery(e).attr("name") == "receive_email") && ChkMail(jQuery(e).val()) == false) {
				/*if(jQuery(e).val() == "") var msg_title = jQuery(e).attr("title");
				else var msg_title = "이메일 형식이 올바르지 않습니다.";
				jQuery(e).parent().append("<div id='order_alert_msg' style='color:red;'>에러 : " + msg_title + "</div>");
				focusElement = e;
				validateFlag = true;
				return false;
				*/
			}else{

				if(jQuery(e).attr("name") != "use_earn") {
					if( (jQuery(e).attr("title") && jQuery(e).val() == "" && jQuery(e).attr("name") != "input_name") || (jQuery(e).attr("name") == "input_name" && jQuery("input:radio[name='pay_how']:checked").val() == "B" && jQuery(e).val() == "") ) {
						jQuery(e).parent().append("<div id='order_alert_msg' style='color:red;'>에러 : " + jQuery(e).attr("title") + "</div>");
						focusElement = e;
						validateFlag = true;
						return false;
					}
				}

			}
		});

		if(validateFlag == true) {
			jQuery(focusElement).focus();
			return;
		}

		if(jQuery("input:radio[name='pay_how']:checked").length == 0) {
			jQuery("#payhow_content").append("<div id='order_alert_msg' style='color:red'>에러 : 결제방법을 선택해주세요.</div>");
			jQuery("input:radio[name='pay_how']").eq(0).focus();
			return;
		}

		if(jQuery("#orderAgreeConfirm").is(":checked") == false) {
			jQuery(this).parent().append("<div id='order_alert_msg' style='color:red;padding-top:10px;'>에러 : 전자상거래 약관에 동의하셔야합니다.</div>");
			jQuery("#orderAgreeConfirm").focus();
			return;
		}

		if(jQuery("input:radio[name='pay_how']:checked").val() == "B") {//무통장입금 선택시
			jQuery("#quick").css("z-index", "-1");
			var preload = '<div id="paying_view" class="modal"><p class="pay-process"><img src="'+jQuery("#BBSE_COMMERCE_THEME_WEB_URL").val()+'/payment/allthegate/image/progress.gif"><br/><br/>처리중입니다. 잠시만 기다려주세요.</p></div>';
			jQuery("#content").append(preload);
			jQuery("#paying_view").modal({
				escapeClose: false,
				clickClose: false,
				showClose: false
			});

			setTimeout(function() {
				jQuery("#order_frm").attr("action",jQuery("#action_url").val()+"/proc/order.exec.php").submit();
			}, 1000);

		}else{//무통장 이외 결제수단 선택시
			if( jQuery("#deviceType").val() == "mobile" || jQuery("#deviceType").val() == "tablet") {

				jQuery.ajax({// 모바일용 임시 데이터 저장
					url: common_var.goods_template_url + "/proc/order-mobile.save.php"
					, type: 'post'
					, data: jQuery("#order_frm").serialize()
					, success: function(response) {
						result = response.split("|||");
						if(result[0] == "success") {
							jQuery("#mobile_Remark").val(result[1]);
							jQuery("#order_frm").attr("action", common_var.home_url + "/?bbsePage=order-payment");
							jQuery("#order_frm").submit();
						}else{
							alert("잘못된 접근입니다.");
						}
					}
					, error: function(data, status, err) {
						alert('주문중 오류가 발생하였습니다.');
					  }
				});

			}else{
				jQuery("#order_frm").attr("action", common_var.home_url + "/?bbsePage=order-payment");
				jQuery("#order_frm").submit();
			}
		}

	});

	jQuery(".back-action").click(function() {//이전페이지
		//history.back();
		location.href=common_var.home_url;
	});
	jQuery(document).keydown(function(e) {var element = e.target.nodeName.toLowerCase();if (element != 'input' && element != 'textarea') {if (e.keyCode === 8) {return false;}}});//backspace disable

	//주문결제 우측 레이어
	jQuery(window).scroll(function(){
		boxPositionFixer();
		jQuery(this).resize(function(){
			boxPositionFixer();
		});
	});
	jQuery("#payment_bank_info").hide();

});



var reset_price = function () {

	var goods_option_price = parseInt(jQuery("#goods_option_price").val());
	var goods_add_price = parseInt(jQuery("#goods_add_price").val());
	var delivery_price = parseInt(jQuery("#delivery_price").val());
	var delivery_add_price = parseInt(jQuery("#delivery_add_price").val());
	if(jQuery("#total_pay_unit").val()!="") {
		var total_pay_unit = parseInt(jQuery("#total_pay_unit").val());
		var total_pay_round = jQuery("#total_pay_round").val();
	}
	else {var total_pay_unit = "";var total_pay_round = "";}
	var total_delivery_price = delivery_price + delivery_add_price;
	var total_goods_earn_price = (goods_option_price + goods_add_price);
	//var total_price = goods_option_price + goods_add_price + total_delivery_price;
	var total_price = goods_option_price + goods_add_price + total_delivery_price;

	/* 적립금 설정 값 */
	var delivery_charge_type = jQuery("#delivery_charge_type").val();
	var condition_free_use = jQuery("#condition_free_use").val();
	var total_pay = parseInt(jQuery("#total_pay").val());
	var delivery_charge_payment = jQuery("#delivery_charge_payment").val();
	var delivery_charge = parseInt(jQuery("#delivery_charge").val());

	if(delivery_charge_type=='free' || (delivery_charge_type=='charge' && condition_free_use=='on' && total_goods_earn_price >= total_pay)){
		var delivery_tit = " (무료)";
		delivery_price = 0;
	}else{
		if(delivery_charge_payment == "advance") {
			var delivery_tit = " (선불)";
			delivery_price = delivery_charge;
			total_goods_earn_price += delivery_price;
		}else if(delivery_charge_payment == "deferred"){
			var delivery_tit = " (후불)";
			delivery_price = delivery_charge;
		}else{
			var delivery_tit = " (착불)";
		}
	}
	jQuery("#delivery_tit").html(delivery_tit);
	jQuery("#delivery_price").val(delivery_price);
	jQuery("#payview_use_earn_price").html("0");
	jQuery("#payview_total_goods_earn_price").html(setComma(goods_option_price+goods_add_price));
	if(total_pay_unit) {
		if(total_pay_round == "down") {
			jQuery("#payview_total_price").html(setComma(Math.floor(total_price / total_pay_unit) * total_pay_unit));
		}else if(total_pay_round == "up") {
			jQuery("#payview_total_price").html(setComma(Math.ceil(total_price / total_pay_unit) * total_pay_unit));
		}
	} else {
		jQuery("#payview_total_price").html(setComma(total_price));
	}
	jQuery("#payview_delivery_price").html(setComma(total_delivery_price));
}

var mileage_apply = function () {// 적립금 처리
	
	jQuery("#use_earn").parent().find("#order_alert_msg").remove();

	var goods_option_price = parseInt(jQuery("#goods_option_price").val());
	var goods_add_price = parseInt(jQuery("#goods_add_price").val());
	var delivery_price = parseInt(jQuery("#delivery_price").val());
	if(delivery_price == null || delivery_price == "" || isNaN(delivery_price)){
		delivery_price = 0;
	}
	var delivery_add_price = parseInt(jQuery("#delivery_add_price").val());
	if(jQuery("#total_pay_unit").val()!="") {
		var total_pay_unit = parseInt(jQuery("#total_pay_unit").val());
		var total_pay_round = jQuery("#total_pay_round").val();
	}
	else {var total_pay_unit = "";var total_pay_round = "";}
	var total_delivery_price = delivery_price + delivery_add_price;
	var total_goods_earn_price = (goods_option_price + goods_add_price);
	var coupon_discount = parseInt(jQuery("#coupon_discount").val());
	if(coupon_discount == null || coupon_discount == "" || isNaN(coupon_discount)){
		coupon_discount = 0;
	}
	
	/* 적립금 설정 값 */
	var delivery_charge_type = jQuery("#delivery_charge_type").val();
	var condition_free_use = jQuery("#condition_free_use").val();
	var total_pay = parseInt(jQuery("#total_pay").val());
	var delivery_charge_payment = jQuery("#delivery_charge_payment").val();
	var delivery_charge = parseInt(jQuery("#delivery_charge").val());
	if(delivery_charge_payment == "advance") {
		var total_price = goods_option_price + goods_add_price + total_delivery_price - coupon_discount;
	}else{
		var total_price = goods_option_price + goods_add_price - coupon_discount;
	}

	jQuery("#payview_use_earn_price").html(0);

	if(jQuery("#earn_pay_use").val() == "Y" && common_var.u != "") {

		var mileage = parseInt(jQuery("#mileage").val());
		if(jQuery("#use_earn").val() == "") {
			var use_mileage = 0;
		}else{
			var use_mileage = parseInt(jQuery("#use_earn").val());
		}
		var earn_hold_point = parseInt(jQuery("#earn_hold_point").val());
		var earn_order_pay = parseInt(jQuery("#earn_order_pay").val());
		var earn_min_point = parseInt(jQuery("#earn_min_point").val());
		var earn_max_percent = parseInt(jQuery("#earn_max_percent").val());
		var earn_use_unit = parseInt(jQuery("#earn_use_unit").val());
		var usable_mileage = total_goods_earn_price * (earn_max_percent/100);
		if(earn_use_unit) usable_mileage =Math.floor(usable_mileage / earn_use_unit) * earn_use_unit;
	
		if(use_mileage > 0) {
			if(mileage < use_mileage) {
				jQuery("#use_earn").parent().append("<div id='order_alert_msg' style='color:red;'>에러 : 보유 적립금을 초과하여 사용할수 없습니다.</div>");
				jQuery("#use_earn").val("");
				jQuery("#use_all_earn").prop("checked", false);
				reset_price();
				return false;
			}
			if(mileage < earn_hold_point) {
				jQuery("#use_earn").parent().append("<div id='order_alert_msg' style='color:red;'>에러 : 보유 적립금이 "+setComma(earn_hold_point)+"원 이상일 때 사용가능 합니다.</div>");
				jQuery("#use_earn").val("");
				jQuery("#use_all_earn").prop("checked", false);
				reset_price();
				return false;
			}
			if(total_price < earn_order_pay) {
				jQuery("#use_earn").parent().append("<div id='order_alert_msg' style='color:red;'>에러 : 주문 합계액이 "+setComma(earn_order_pay)+"원 이상일 때 사용가능 합니다.</div>");
				jQuery("#use_earn").val("");
				jQuery("#use_all_earn").prop("checked", false);
				reset_price();
				return false;
			}
			if(use_mileage < earn_min_point) {
				jQuery("#use_earn").parent().append("<div id='order_alert_msg' style='color:red;'>에러 : 적립금은 최소 "+setComma(earn_min_point)+"원 이상일 때 사용가능 합니다.</div>");
				jQuery("#use_earn").val("");
				jQuery("#use_all_earn").prop("checked", false);
				reset_price();
				return false;
			}
			if(use_mileage > usable_mileage) {
				jQuery("#use_earn").parent().append("<div id='order_alert_msg' style='color:red;'>에러 : 적립금은 최대 "+setComma(usable_mileage)+"원 까지 사용가능합니다.</div>");
				jQuery("#use_earn").val("");
				jQuery("#use_all_earn").prop("checked", false);
				reset_price();
				return false;
			}
		}

		if(delivery_charge_type=='free' || (delivery_charge_type=='charge' && condition_free_use=='on' && (total_goods_earn_price - use_mileage) >= total_pay)){
			delivery_price = 0;
			var delivery_tit = " (무료)";
		}else{
			if(delivery_charge_payment == "advance") {
				var delivery_tit = " (선불)";
				delivery_price = delivery_charge;
			}else if(delivery_charge_payment == "deferred"){
				var delivery_tit = " (후불)";
				delivery_price = delivery_charge;
			}else{
				var delivery_tit = " (착불)";
			}
		}

		jQuery("#payview_use_earn_price").html(setComma(use_mileage));
		jQuery("#payview_total_goods_earn_price").html(setComma(total_goods_earn_price - use_mileage));
		if(total_pay_unit) {
			if(total_pay_round == "down") {
				jQuery("#payview_total_price").html(setComma(Math.floor((total_price - use_mileage - payview_coupon_price) / total_pay_unit) * total_pay_unit));
			}else if(total_pay_round == "up") {
				jQuery("#payview_total_price").html(setComma(Math.ceil((total_price - use_mileage - payview_coupon_price) / total_pay_unit) * total_pay_unit));
			}
		} else {
			jQuery("#payview_total_price").html(setComma(total_price - use_mileage - payview_coupon_price));
		}
		jQuery("#payview_delivery_price").html(setComma(total_delivery_price));

	}else{

		if(delivery_charge_type=='free' || (delivery_charge_type=='charge' && condition_free_use=='on' && total_goods_earn_price >= total_pay)){
			delivery_price = 0;
		}else{
			if(delivery_charge_payment == "advance") {
				var delivery_tit = " (선불)";
				delivery_price = delivery_charge;
			}else if(delivery_charge_payment == "deferred"){
				var delivery_tit = " (후불)";
				delivery_price = delivery_charge;
			}else{
				var delivery_tit = " (착불)";
			}
		}

		jQuery("#payview_total_goods_earn_price").html(setComma(total_goods_earn_price));
		if(total_pay_unit) {
			if(total_pay_round == "down") {
				jQuery("#payview_total_price").html(setComma(Math.floor(total_price / total_pay_unit) * total_pay_unit));
			}else if(total_pay_round == "up") {
				jQuery("#payview_total_price").html(setComma(Math.ceil(total_price / total_pay_unit) * total_pay_unit));
			}
		} else {
			jQuery("#payview_total_price").html(setComma(total_price));
		}
		jQuery("#payview_delivery_price").html(setComma(total_delivery_price));

	}
	jQuery("#delivery_tit").html(delivery_tit);
	jQuery("#delivery_price").val(delivery_price);

	return true;

}
var delivery_change = function (chg) {// 주소변경시 배송비 계산
	
	jQuery.ajax({
		url: common_var.goods_template_url + "/proc/order.delivery.php"
		, type: 'post'
		, data: {receive_addr1:jQuery("#receive_addr1").val()}
		, success: function(response) {
			var result = response.split("|||");
			if(result[0] == "success") {
				jQuery("#delivery_add_price").val(result[1]);
				if(chg == "addrChang") mileage_apply();
			}else{
				alert("잘못된 접근입니다.");
			}
		}
		, error: function(data, status, err) {
			alert('서버와의 통신이 실패했습니다.');
		}
	});
}

var zipcode_search = function(fieldTitle){
	var add_qs = "";
	if(fieldTitle == "order") var add_qs = "&act=no";
	window.open(common_var.goods_template_url+"/zipcode.php?fieldTitle="+fieldTitle+add_qs, "zipcode_search", "width=400,height=400,scrollbars=yes");
}

var boxPositionFixer = function(){
	var boxOffSet  = jQuery("#paymentFixedArea").offset();
	var docScroll  = jQuery(document).scrollTop()-34;
	if (boxOffSet.top >= docScroll)
		jQuery('#paymentFixed').removeClass('pay_fixed');
	else
	{
		var leftPos = (jQuery(window).width()/2)+190;
		jQuery('#paymentFixed').css('left', leftPos).addClass('pay_fixed');
	}
}

if(jQuery("#deviceType").val() == "desktop") {
	var payChked=jQuery("input[name=pay_how").size();
	var upFlag=false;
	for(i=0;i<payChked;i++){
		if(jQuery("input[name=pay_how").eq(i).val()=='C' || jQuery("input[name=pay_how").eq(i).val()=='K' || jQuery("input[name=pay_how").eq(i).val()=='V'){
			upFlag=true;
		}
	}

	//if(upFlag) StartSmartUpdate();  // ActiveX 업데이트
}

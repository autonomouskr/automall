jQuery(function () {

	jQuery(document).ready(function(){
		jQuery('html').addClass('js');	//접근성용 class

		mbMenu();			        //모바일 메뉴
		myBtn();				      //모바일 마이페이지 버튼화
		allCategory();		    //전체카테고리 보기
		//gnbNav();			        //gnb, snv
		gnbDepth3();          //카테고리 메뉴 3차 깊이 컨트롤
		mobileCatMenuCtrl();  //카테고리 메뉴 PC&모바일 동작구분
		screenSizeCheck();    //스크린 크기 변경시 함수 재실행
		side_category_menu(); //사이드 카테고리 메뉴
		side_section_menu(); //사이드 메뉴
		scrollFixed();		    //최근본상품
		reCallBox();			    //반품신청
		orderCancelBox()      //취소신청
		orderConfirm()      //구매확정
		initTabs();			      //공통 탭
		toggleSearch();	      //모바일 검색 버튼
		lpControl();			    //lp location
		//commentList();	      //코멘트 리스트 형
		fileUploadForm();	    //파일 업로드
		//embedEditor();        //상품평/문의 에디터 열기
		snsShare();           //SNS 공유
		toolTipControl();     //툴팁 컨트롤

		initInputs('.search_box input[type=text], .bb_search_field input[type=text]');
		jQuery('.tab_nav').loopGallery({active: 1, group: 1});		//
		//jQuery('#favList').loopGallery({active: 3, group: 1});		//퀵 최근 본 상품
		jQuery('.bnn_list').gallery({active: 1, group: 1});			//
		jQuery('#specialList').gallery({active: 1, group: 1});		//
		jQuery('#hotList').gallery({active: 3, group: 3});			//

		//상품 상세 썸네일 하단
		jQuery('.bb_thumb_control').loopGallery({active: 5, group: 1});
		jQuery('.bb_thumbnail').sampleGallery();
		if (jQuery('.bb_thumb_control ul li').length < 5 + 1 ){
				jQuery('.bb_thumb_control .control_btn').hide();
		}

		//유동적 리스팅
		jQuery('#recmdList').loopGallery({active: 4, group: 1});
		if (jQuery('#recmdList ul li').length < 4 + 1 ){
				jQuery('#recmdList .control_btn').hide();
		}

		jQuery('#famsite_roll').loopGallery({active: 6, group: 1});
		if (jQuery('#famsite_roll ul li').length < 6 + 1 ){
				jQuery('#famsite_roll .control_btn').hide();
		}

		jQuery(".family-site-btn").click(function() {
			var splitSite = jQuery("#familySite").val().split("|");
			if(splitSite[1]=="" && "_self") {
				location.href = splitSite[0];
			}else{
				window.open(splitSite[0]);
			}
		});
		jQuery(".total_search").click(function() {
			if(jQuery("#keyword").val()) jQuery("#topSearchFrom").submit();
		});

		jQuery("#allChkSelect").click(function() {
			if(jQuery(this).is(":checked")) {
				jQuery("[id^='gidx_']").prop("checked", true);
			}else{
				jQuery("[id^='gidx_']").prop("checked", false);
			}
		});

		// 체크박스 : 전체선택
		jQuery("#allChkSelectNotice").click(function() {
			if(jQuery(this).is(":checked")) {
				jQuery("[id^='nidx_']").prop("checked", true);
			}else{
				jQuery("[id^='nidx_']").prop("checked", false);
			}
		});

		if(jQuery('body').width()>1024){
			$(".bb_thumb_control .slide-list .active > a > img").one("load", function() {
			// do stuff
			}).each(function() {
				$("#goods-img-zoom img.bb_thumbnail_big").one("load", function() {
				// do stuff
				}).each(function() {
					var imageHeight = $("#goods-img-zoom img.bb_thumbnail_big").height();
					if(imageHeight<=0) imageHeight=420;
					jQuery(".bb_thumbnail_big").elevateZoom({
						easing : true,
						lensBorderColour:"#a6a6a6",
						zoomWindowWidth:434, 
						zoomWindowHeight:imageHeight-8,
						zoomWindowFadeIn: 500, 
						zoomWindowFadeOut: 500, 
						lensFadeIn: 500, 
						lensFadeOut: 500,
						zoomWindowBgColour:"#fff",
						borderColour:"#e6e6e6",
						borderSize:4
				   });
				});
			});
		}

		jQuery("li .img_view img").hover(function(e){ 
			e.preventDefault();
			var firstImg = jQuery(this).data("firstimg");
			var secondImg = jQuery(this).data("secondimg");
			if(firstImg && secondImg){
				jQuery(this).attr("src",secondImg);
			}
		}, function(e){ 
			e.preventDefault();
			var firstImg = jQuery(this).data("firstimg");
			var secondImg = jQuery(this).data("secondimg");
			if(firstImg && secondImg){
				jQuery(this).attr("src",firstImg);
			}
		}); 

		jQuery("li img.bb_thumb").hover(function(e){ 
			e.preventDefault();
			var firstImg = jQuery(this).data("firstimg");
			var secondImg = jQuery(this).data("secondimg");
			if(firstImg && secondImg){
				jQuery(this).attr("src",secondImg);
			}
		}, function(e){ 
			e.preventDefault();
			var firstImg = jQuery(this).data("firstimg");
			var secondImg = jQuery(this).data("secondimg");
			if(firstImg && secondImg){
				jQuery(this).attr("src",firstImg);
			}
		}); 
	});


	//win resize
	if(window.addEventListener){
		window.addEventListener("resize", winResize, false);
	}else if(window.attachEvent){//IE
		window.attachEvent("onresize", winResize);
	}
	function winResize() {
		var winSize = jQuery('body').width();
		if (winSize > 1200) {
			jQuery('body').addClass('wide');
		} else if (winSize > 1200) {
			jQuery('body').addClass('trans');
			jQuery('body').removeClass('wide');
		}else {
			jQuery('body').removeClass('wide');
		}
	}

	//lp location
	function lpControl() {
		var lpWrap = $('.location ul li'),
			lpLayers = lpWrap.find('.sub_ly ul');
		lpWrap.each(function() {
			var trigerBtn = $(this).find('.sub_ly .bb_lp'),
				lpLayer = $(this).find('.sub_ly ul');

			if($(this).find('.sub_ly').length < 1) {
				$(this).addClass('nodepth');
			}
			trigerBtn.bind('click', function() {
				if (lpLayer.css('display') == 'none'){
					lpWrap.removeClass('active');
					$(this).parent().parent().addClass('active');
					lpLayers.hide();
					lpLayer.show();
				} else {
					lpLayer.hide();
					lpWrap.removeClass('active');
				}
			})
		})
	}

	//태블릿,모바일 햄버거 버튼
	function mbMenu() {
		var btn = $('.mb_menu'),
			mbWrap = $('#header'),
			ctWrap = $('#container'),
			mask = $('.content_mask');
		btn.bind('click', function() {
			if ($('html').hasClass('mobile_action')) {
				$('html').removeClass('mobile_action');
			} else {
				$('html').addClass('mobile_action');
			}
		})
		mask.bind('click', function() {
			$('html').removeClass('mobile_action');
		})
	};

	//toggle_search
	function toggleSearch() {
		var toggleBtn = $('.toggle_search'),
			toggleWrap = $('.search_box'),
			mb_top = $('.mb_top');
		toggleBtn.bind('click', function() {
			if (toggleWrap.css('display') == 'none'){
				mb_top.addClass('bb_open_search');
				jQuery(".input_search").css("display","inline-block");
				jQuery("#pc_search_button").css("display","none");
			} else {
				mb_top.removeClass('bb_open_search');
				jQuery(".input_search").css("display","none");
				jQuery("#pc_search_button").css("display","inline-block");
			}
		})
	}

	//상단 마이페이지
	function myBtn() {
		var btns = jQuery('.myp ul li div.arrtop'),
			lists = jQuery('.myp ul');
		btns.bind({
			'click focusin': function(e){
				e.preventDefault();
				lists.toggleClass('active');
			},
			'focusout': function(){
				lists.removeClass('active');
			}
		});
/*
		btns.bind('click focusin', function(e) {
			e.preventDefault();
			lists.toggleClass('active');
		})
		btns.bind('focusout', function() {
			lists.removeClass('active');
		})
*/
	};

	//전체 카테고리 보기
	function allCategory() {
		var btns = jQuery('#allcategory .bb_open'),
			closeBtn = jQuery('#allcategory .all-category .bb_close'),
			cate = jQuery('#allcategory');

		btns.bind('click', function() {
			if (cate.find('.all-category').css('display') == 'none') {
				cate.addClass('active');
			} else {
				cate.removeClass('active');
				//jQuery('#allcategory .all-category .bb_inner > ul > li > ul').css('display','none');
			}
		})
		closeBtn.bind('click focusout', function() {
			cate.removeClass('active');
			//jQuery('#allcategory .all-category .bb_inner > ul > li > ul').css('display','none');
			btns.focus();
		})
	};

	//gnb
	function cateNav() {
		var trunk = jQuery("#gnb > ul, .side-menu"),
			mBtn = jQuery("#gnb button");

		trunk.find("> li:last-child").prev().addClass('last-prev');
		trunk.find("li").each(function() {
			var branch = jQuery(this);
			if (branch.find('ul').length) {
				branch.addClass('has-child');
				branch.bind({
					'focusin click': function() {
						jQuery(this).addClass('active');
					},
					'focusout click': function() {
					jQuery(this).removeClass('active');
					},
				})
/*
				branch.bind('', function() {
					jQuery(this).addClass('active');

				}).bind('focusout click', function() {
					jQuery(this).removeClass('active');
				})
*/
			}
		})
		mBtn.click(function() {
			if (trunk.css('display') == 'none') {
				trunk.slideDown('fast');
			} else {
				trunk.removeAttr('style');
			}
		})
	}



	//gnb
  /*
	function gnbNav() {
		var trunk = jQuery(".navi_common, .all-category .bb_inner ul"),
		mBtn = jQuery("#gnb button");

		trunk.find("> li:last-child").prev().addClass('last-prev');
		trunk.find("li").each(function() {
			var branch = jQuery(this);
			if (branch.find('ul').length) {
				branch.addClass('menu-item-has-children');

				branch.bind('focusin click mouseenter', function() {
					jQuery(this).addClass('active');
				}).bind('focusout click mouseleave', function() {
					jQuery(this).removeClass('active');
				})
			}
		})
		mBtn.click(function() {
			if (trunk.css('display') == 'none') {
				trunk.slideDown('fast');
			} else {
				trunk.removeAttr('style');
			}
		})
	}*/


  // 3차 메뉴 by TheBits
  function gnbDepth3(){
	var device=jQuery("#allcategory .all-category .bb_inner > ul").data("device");
	if(device=='mobile' || jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children > span').length > 0){
		jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children > span').click(
		  function(){
			var menuIdx=jQuery(this).data("menu");
			var index = jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children').index(jQuery(".menu-item-"+menuIdx));
			jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children > ul').eq(index).css('display','block');
		  }
		);
	}
	else{
		jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children').hover(
		  function(){
			var index = jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children').index(jQuery(this));
			jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children > ul').eq(index).css('display','block');
		  },
		  function(){
			jQuery('#allcategory .all-category .bb_inner > ul > li > ul > li.menu-item-has-children > ul').css('display','none');
		  }
		);
	}
  }

  function mobileCatMenuCtrl()
  {
    var $screen = jQuery(window).width();
    if ($screen < 1023)
    {
	  var device=jQuery("#allcategory .all-category .bb_inner > ul").data("device");

	  if(device=='mobile' || jQuery('#allcategory .all-category .bb_inner > ul > li > span').length > 0){
		  jQuery('#allcategory .all-category .bb_inner > ul > li > span').click(function(){
			  var menuIdx=jQuery(this).data("menu");
			  var index = jQuery('#allcategory .all-category .bb_inner > ul > li').index(jQuery(".menu-item-"+menuIdx));
			  jQuery('#allcategory .all-category .bb_inner > ul > li > ul').eq(index).css('display','block');
			});
	  }
	  else{
	      jQuery('#allcategory .all-category .bb_inner > ul > li > ul').css('display','none');

		  jQuery('#allcategory .all-category .bb_inner > ul > li').hover(
			function(){
			  var index = jQuery('#allcategory .all-category .bb_inner > ul > li').index(jQuery(this));
			  jQuery('#allcategory .all-category .bb_inner > ul > li > ul').eq(index).css('display','block');
			},
			function(){
			  jQuery('#allcategory .all-category .bb_inner > ul > li > ul').css('display','none');
			}
		  )
	  }
    }
    else
    {
      jQuery('#allcategory .all-category .bb_inner > ul > li > ul').removeAttr('style').css('display','block');
      jQuery('#allcategory .all-category .bb_inner > ul > li').hover(
        function(){
          var index = jQuery('#allcategory .all-category .bb_inner > ul > li').index(jQuery(this));
          jQuery('#allcategory .all-category .bb_inner > ul > li > ul').eq(index).css('display','block');

        },
        function(){
          jQuery('#allcategory .all-category .bb_inner > ul > li > ul').css('display','block');
        }
      )
    }
  }

  function screenSizeCheck()
  {
    jQuery(window).resize(function(){
      mobileCatMenuCtrl();
    })
  }

  function side_category_menu(){
    jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children').hover(
      function(){
        var index1 = jQuery('#sidebar > .side-nav > ul > li').index(jQuery(this));
		if(jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children').eq(index1).hasClass('found-sub')) {
	        jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children > ul').eq(index1).css('display','block');
		}
        jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children').hover(
          function(){
            var index2 = jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children').index(jQuery(this));
            jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children > ul').eq(index2).css('display','block');
          },
          function(){
            jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children > ul').css('display','none');
          }
        )
      },
      function(){
        jQuery('#sidebar > .side-nav > ul > li.menu-item-has-children > ul').css('display','none');
      }
    );
  }


  function side_section_menu(){
    jQuery('.mypage-left-menu.side-nav > ul > li.menu-item-has-children').hover(
      function(){
        var index1 = jQuery('mypage-left-menu.side-nav > ul > li').index(jQuery(this));
        jQuery('.mypage-left-menu.side-nav > ul > li.menu-item-has-children > ul').eq(index1).css('display','block');

        jQuery('.mypage-left-menu.side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children').hover(
          function(){
            var index2 = jQuery('.mypage-left-menu.side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children').index(jQuery(this));
            jQuery('.mypage-left-menu.side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children > ul').eq(index2).css('display','block');
          },
          function(){
            jQuery('.mypage-left-menu.side-nav > ul > li.menu-item-has-children > ul > li.menu-item-has-children > ul').css('display','none');
          }
        )
      },
      function(){
        jQuery('.mypage-left-menu.side-nav > ul > li.menu-item-has-children > ul').css('display','none');
      }
    )
  }

	// quick menu
	function scrollFixed() {
		function fixDiv() {
		  var $cache = $('#quick');
		  if ($(window).scrollTop() > 110)
			$cache.css({'position': 'fixed', 'top': '10px'});
		  else
			$cache.css({'position': 'absolute', 'top': '0'});
		}
		$(window).scroll(fixDiv);
		fixDiv();
	}

  //환불 폼 컨트롤
  function reCallBox() {
	var triger = $('.recall_open'),
	reCallBox = $('.recall_box'),
	reCallBoxClose = reCallBox.find('.recall_close');
	reCallBox.hide();
	triger.bind('click', function() {
		if($('#page_kind').val() == "list") {
			var orderData = $(this).data("orderrefund").split("|||");
			$('#order_no').val(orderData[0]);
			$('#refund_order_no').html(orderData[0]);
			$('#refund_goods_name').html(orderData[1]);
		}
		if($('.recall_box').css("display") == "block") $('.recall_box').hide();
		if($('.orderCancel_box').css("display") == "block") $('.orderCancel_box').hide();
		if (reCallBox.css('display') == 'none') {
			reCallBox.slideDown('100',function(){
				var boxOffset = $('#refundBlock').offset();
				$('html, body').animate({scrollTop : (boxOffset.top-32)},{duration:'1200', easing:'swing', queue:false});
			});
		}
	});
	reCallBoxClose.bind('click', function() {
		if (reCallBox.css('display') == 'block') {
			reCallBox.slideUp('100');
		}
	});

	$('.orderRefund_submit').bind('click', function () {
		if(confirm("환불신청을 하시겠습니까?")) {
			if(common_var.u != "") {
				var send = {tMode:"order_refund", order_no:jQuery("#order_no").val(), cancel_info:jQuery("#orderRefundFrm").serialize()};
			}else{
				var send = {tMode:"order_refund", order_no:jQuery("#order_no").val(), order_name:jQuery("#order_name").val(), cancel_info:jQuery("#orderRefundFrm").serialize()};
			}
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: common_var.goods_template_url+"/proc/mypage-order.exec.php", 
				data: send,
				success: function(data){
					var result = data.split("|||"); 
					if(result[0] == "success"){
						if(common_var.u == "") {
							$('#orderRefundFrm').attr("action", common_var.home_url + "/?bbseMy=order-detail");
							$('#orderRefundFrm').append('<input type="hidden" name="order_no" id="order_no" value="'+jQuery("#order_no").val()+'">');
							$('#orderRefundFrm').append('<input type="hidden" name="order_name" id="order_name" value="'+jQuery("#order_name").val()+'">');
							$('#orderRefundFrm').submit();
						}else{
							if($('#page_kind').val() == "list") {
								location.href = common_var.home_url + "/?bbseMy=order-list" + jQuery("#addQueryString").val();
							}else{
								location.href = common_var.home_url + "/?bbseMy=order-detail&ordno=" + jQuery("#order_no").val() + jQuery("#addQueryString").val();
							}
						}
					}else if(result == "not") {
						alert("이미 환불처리 되었습니다.");
					}else if(result[0] == "error") {
						alert(result[1]);
					}else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});
		}
	});

  }

  //취소 폼 컨트롤
  function orderCancelBox() {
	var triger = $('.orderCancel_open'),
		orderCancelBox = $('.orderCancel_box'),
		orderCancelclose = orderCancelBox.find('.orderCancel_close');
	orderCancelBox.hide();
	triger.bind('click', function() {
		if($('#page_kind').val() == "list") {
			var orderData = $(this).data("ordercancel").split("|||");
			$('#order_no').val(orderData[0]);
			$('#cancel_order_no').html(orderData[0]);
			$('#cancel_goods_name').html(orderData[1]);
			if(orderData[2] == "PR") $("#refund_bank_info_view").hide();
			else $("#refund_bank_info_view").show();
		}

		
		if($('.recall_box').css("display") == "block") $('.recall_box').hide();
		if($('.orderCancel_box').css("display") == "block") $('.orderCancel_box').hide();
		if (orderCancelBox.css('display') == 'none') {
			orderCancelBox.slideDown('100',function(){
				var boxOffset = $('#cancelBlock').offset();
				$('html, body').animate({scrollTop : (boxOffset.top-32)},{duration:'1200', easing:'swing', queue:false});
			});
		}
	});
	orderCancelclose.bind('click', function() {
		if (orderCancelBox.css('display') == 'block') {
			orderCancelBox.slideUp('100');
		}
	});

	$('.orderCancel_submit').bind('click', function () {
		if(confirm("취소신청을 하시겠습니까?")) {
			if(common_var.u != "") {
				var send = {tMode:"order_cancel", order_no:jQuery("#order_no").val(), cancel_info:jQuery("#orderCancelFrm").serialize()};
			}else{
				var send = {tMode:"order_cancel", order_no:jQuery("#order_no").val(), order_name:jQuery("#order_name").val(), cancel_info:jQuery("#orderCancelFrm").serialize()};
			}
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: common_var.goods_template_url+"/proc/mypage-order.exec.php", 
				data: send,
				success: function(data){
					var result = data.split("|||"); 
					if(result[0] == "success"){
						if(common_var.u == "") {
							$('#orderCancelFrm').attr("action", common_var.home_url + "/?bbseMy=order-detail");
							$('#orderCancelFrm').append('<input type="hidden" name="order_no" id="order_no" value="'+jQuery("#order_no").val()+'">');
							$('#orderCancelFrm').append('<input type="hidden" name="order_name" id="order_name" value="'+jQuery("#order_name").val()+'">');
							$('#orderCancelFrm').submit();
						}else{
							if($('#page_kind').val() == "list") {
								location.href = common_var.home_url + "/?bbseMy=order-list" + jQuery("#addQueryString").val();
							}else{
								location.href = common_var.home_url + "/?bbseMy=order-detail&ordno=" + jQuery("#order_no").val() + jQuery("#addQueryString").val();
							}
						}
					}else if(result == "not") {
						alert("이미 취소되었습니다.");
					}else if(result[0] == "error") {
						alert(result[1]);
					}else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});
		}
	});

  }

  //구매확정 컨트롤
  function orderConfirm() {

	$('.order_confirm').bind('click', function () {
		if(confirm("구매확정 하시겠습니까?")) {
			if($('#page_kind').val() == "list") {
				var orderData = $(this).data("orderend").split("|||");
				$('#order_no').val(orderData[0]);
			}
			if(common_var.u != "") {
				var send = {tMode:"order_confirm", order_no:jQuery("#order_no").val(), cancel_info:jQuery("#orderCancelFrm").serialize()};
			}else{
				var send = {tMode:"order_confirm", order_no:jQuery("#order_no").val(), order_name:jQuery("#order_name").val(), cancel_info:jQuery("#orderCancelFrm").serialize()};
			}
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: common_var.goods_template_url+"/proc/mypage-order.exec.php",
				data: send,
				success: function(data){
					var result = data.split("|||"); 
					if(result[0] == "success"){
						if(common_var.u == "") {
							$('#orderCancelFrm').attr("action", common_var.home_url() + "/?bbseMy=order-detail");
							$('#orderCancelFrm').append('<input type="hidden" name="order_no" id="order_no" value="'+jQuery("#order_no").val()+'">');
							$('#orderCancelFrm').append('<input type="hidden" name="order_name" id="order_name" value="'+jQuery("#order_name").val()+'">');
							$('#orderCancelFrm').submit();
						}else{
							if($('#page_kind').val() == "list") {
								location.href = common_var.home_url + "/?bbseMy=order-list" + jQuery("#addQueryString").val();
							}else{
								location.href = common_var.home_url + "/?bbseMy=order-detail&ordno=" + jQuery("#order_no").val() + jQuery("#addQueryString").val();
							}
						}
					}else if(result == "not") {
						alert("이미 구매확정 처리되었습니다.");
					}else if(result[0] == "error") {
						alert(result[1]);
					}else{
						alert("서버와의 통신이 실패했습니다.   ");
					}
				}, 
				error: function(data, status, err){
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});
		}
	});

  }

	//input title
	function initInputs(selector) {
		var inputs = jQuery(selector);
		inputs.each(function () {
			var labels = jQuery(this).next('label');
			if (jQuery(this).val() == "") {
				labels.show();
			}
			if (jQuery(this).val() !== "") {
				labels.hide();
			}
			jQuery(this).bind("focusin", function () {
				if (jQuery(this).val() !== 0) {
					labels.hide();
				}
			});
			jQuery(this).bind("focusout", function () {
				if (jQuery(this).val().length == 0) {
					labels.show();
				}
			});
		});
	}

	//dimmed Layer


	// Sample Gallery
	jQuery.fn.sampleGallery = function () {
		return this.each(function () {
			var wrapper = jQuery(this),
			placeholder = wrapper.find('img.bb_thumbnail_big'),
			gallery = wrapper.find('.slide-list'),
			img_anchor = gallery.find('a');
			img_anchor.bind('click', function (e) {
				e.preventDefault();
				gallery.find('li').removeClass('active');
				jQuery(this).parent().addClass('active');
				var big_img = $(this).attr('href');
				placeholder.attr('src', big_img);
				placeholder.data('zoom-image',big_img);

				if(jQuery('body').width()>1024){
					 placeholder.load(function() {
						$(".zoomContainer").css("height", placeholder.height());

						jQuery(".bb_thumbnail_big").elevateZoom({ // elevatezoom jQuery plugin reset
							easing : true,
							lensBorderColour:"#a6a6a6",
							zoomWindowWidth:434, 
							zoomWindowHeight:placeholder.height()-8,
							zoomWindowFadeIn: 500, 
							zoomWindowFadeOut: 500, 
							lensFadeIn: 500, 
							lensFadeOut: 500,
							zoomWindowBgColour:"#fff",
							borderColour:"#e6e6e6",
							borderSize:4
					   });
				   });
				}
			});
		});
	};

	//gallery
	jQuery.fn.gallery = function (options) {
		var settings = jQuery.extend({
			active: 4,
			group: function () {
				return this.active;
			},
			index: {
				first: 0
			}
		}, options);
		return this.each(function () {
			var $this = jQuery(this),
				items = $this.find('ul > li'),
				items_length = items.length,
				countView = $this.find('.control_btn .count .view'),
				countMax= $this.find('.control_btn .count .max'),
				active_items, i;

			settings.group = (typeof settings.group === 'function') ? settings.group() : settings.group;
			settings.index.last = settings.index.last || (settings.active - 1);
			checkIndex();
			var cntMax = Math.ceil(items_length/settings.group);
			var cntView = Math.ceil(settings.index.first/settings.group);
			countMax.text(cntMax);
			countView.text(cntView + 1);

			init();

			function init() {
				var next = $this.find('.control_btn .next'),
					prev = $this.find('.control_btn .prev');

				setActiveItems();
				//items.hide();
				items.addClass('none');
				//jQuery(active_items).show();
				jQuery(active_items).removeClass('none');

				next.bind('click', function (e) {
					e.preventDefault();
					nextItems();
				});
				prev.bind('click', function (e) {
					e.preventDefault();
					prevItems();
				});
			}

			function checkIndex() {
				var remainder = (items_length % settings.active),
					first_limit = items_length - (remainder || settings.active);
				if (settings.index.first < 0) {
					settings.index.first = first_limit;
				}
				if (settings.index.first > first_limit) {
					settings.index.first = 0;
				}
				settings.index.last = settings.index.first + settings.active - 1;
				if ((settings.index.last >= items_length) || (settings.index.last < 0)) {
					settings.index.last = items_length - 1;
				}
			}

			function setActiveItems() {
				var re = /active-item-\d/g,
					i, j, len, class_name;
				active_items = [];
				for (i = settings.index.first; i <= settings.index.last; i++) {
					active_items.push(items[i]);
				}
				items.removeClass('first');
				items.removeClass('last');
				items.each(function () {
					class_name = this.className.match(re);
					jQuery(this).removeClass(class_name);
				});
				jQuery(active_items[0]).addClass('first');
				jQuery(active_items[(active_items.length - 1)]).addClass('last');
				for (j = 0, len = active_items.length; j < len; j++) {
					jQuery(active_items[j]).addClass('active-item-' + j);
				}
			}

			function nextItems() {
				if (!items_length) return false;
				settings.index.first = settings.index.first + settings.group;
				settings.index.last = settings.index.first + settings.active - 1;
				checkIndex();
				setActiveItems();
				//items.hide();
				items.addClass('none');
				//jQuery(active_items).show();
				jQuery(active_items).removeClass('none');
				countView.text(Math.ceil(settings.index.first/settings.group) + 1);
				//alert(settings.index.first);
				//alert(settings.active);
			}

			function prevItems() {
				if (!items_length) return false;
				settings.index.first = settings.index.first - settings.group;
				settings.index.last = settings.index.first + settings.active - 1;
				checkIndex();
				setActiveItems();
				//items.hide();
				items.addClass('none');
				//jQuery(active_items).show();
				jQuery(active_items).removeClass('none');
				countView.text(Math.ceil(settings.index.first/settings.group) + 1);
			}

			this.next = nextItems;
			this.prev = prevItems;
		});
	};

	//loopGallery
	jQuery.fn.loopGallery = function (options) {
		var settings = jQuery.extend({
			active: 3,
			group: function () {
				return this.active;
			}
		}, options);

		return this.each(function () {
			var $this = jQuery(this),
				item_list = $this.find('ul'),
				items, i_length, active_items, a_length;

			settings.group = (typeof settings.group === 'function') ? settings.group() : settings.group;

			init();

			function init() {
				var next = $this.find('.control_btn .next'),
					prev = $this.find('.control_btn .prev');

				setActiveItems();

				next.bind('click', function (e) {
					e.preventDefault();
					nextItems();
				});
				prev.bind('click', function (e) {
					e.preventDefault();
					prevItems();
				});
			}

			function setActiveItems() {
				items = item_list.find('> li');
				i_length = items.length;
				var last = (i_length < settings.active) ? i_length : settings.active,
					i;
				active_items = [];
				for (i = 0; i < last; i++) {
					active_items.push(items[i]);
				}
				a_length = active_items.length;
				items.removeClass('first');
				items.removeClass('last');
				jQuery(active_items[0]).addClass('first');
				jQuery(active_items[a_length - 1]).addClass('last');
				//items.hide();
				items.addClass('none');
				//jQuery(active_items).show();
				jQuery(active_items).removeClass('none');
				jQuery(active_items).addClass('active');
			}

			function nextItems() {
				if (i_length <= settings.active) return false;
				jQuery(items[0]).appendTo(item_list);

				items = item_list.find('> li');
				var tab_links = items.find('a');
				var cont_id = jQuery(tab_links.get(0)).attr('href');
				if(cont_id.indexOf('md')>'0'){
					jQuery(".md_wrap .tab-cont").css("display","none");
					jQuery(cont_id).css("display","block");
				}

				setActiveItems();
			}

			function prevItems() {
				if (i_length <= settings.active) return false;
				jQuery(items[i_length - 1]).prependTo(item_list);

				items = item_list.find('> li');
				var tab_links = items.find('a');
				var cont_id = jQuery(tab_links.get(0)).attr('href');
				if(cont_id.indexOf('md')>'0'){
					jQuery(".md_wrap .tab-cont").css("display","none");
					jQuery(cont_id).css("display","block");
				}

				setActiveItems();
			}
		});
	};


	/* comment list
	function commentList() {
		var cmtList = $('.bb_open_list')
			cmtSubj = cmtList.find('dt'),
			cmtAnswer = cmtList.find('dd');
		cmtList.each(function() {
			cmtAnswer.hide();
			cmtSubj.bind('click', function() {
				if ($(this).next().css('display') == "none"){
					$(this).next().show();
					resize_frame('detailCommentF');
				} else {
					$(this).next().hide();
				}

			})
		})
	}

  var embedEditor = function(){
    $('.view_cmtlayer').click(function(){
      var $index = $('.view_cmtlayer').index($(this));
      $('.cmt_write').eq($index).toggle();
    });

    $('.hideEditor').click(function(){
      var $index = $('.hideEditor').index($(this));
      $('.cmt_write').eq($index).toggle();
    });
    return false;
  }
	*/




/*
	//아이프레임 리사이즈
	function resize_frame(id) {
		var frm = document.getElementById("embeded-content");
		function resize() {
			frm.style.height = "auto"; // set default height for Opera
			contentHeight = frm.contentWindow.document.documentElement.scrollHeight;
			frm.style.height = contentHeight + 23 + "px"; // 23px for IE7
		}
		if (frm.addEventListener) {
			frm.addEventListener('load', resize, false);
		} else {
			frm.attachEvent('onload', resize);
		}
	}
*/
	//file upload
	function fileUploadForm() {
		if (jQuery('.file_wrap').length) {
			var file = jQuery('.file_wrap').find('input[type=file]'),
				inputs = jQuery('.file_wrap').find('.fake_upload');
			file.click(function(){
				jQuery(this).val("");
			});
			file.change(function(){
				inputs.text(jQuery(this).val());
				//alert(jQuery(this).val());
			});
		}
	}

	//top button
	jQuery('#quick .top').click(function() {
		jQuery('body,html').animate({scrollTop:0},300);
	});
	jQuery('#quick .down').click(function() {
		jQuery('body,html').animate({scrollTop:jQuery(document).height()},300);
	});

	//global tab
	jQuery.fn.tab = function () {
		return this.each(function () {
			var tab = this,
				tab_items = jQuery(tab).find('> li'),
				tab_links = tab_items.find('a'),
				link_len = tab_links.length,
				i, cont_id;
			this.contents = [];
			for (i = 0; i < link_len; i++) {
				cont_id = jQuery(tab_links.get(i)).attr('href');
				this.contents.push(jQuery(cont_id).get(0));
			}
			this.select = function (cont_index) {
				var selected_tab = jQuery(tab_items[cont_index]),
					selected_content = jQuery(tab.contents[cont_index]);
				tab_items.each(function () {
					var item = jQuery(this);
					item.removeClass('active');
					//item.addClass('off');
				});
				jQuery(tab.contents).each(function() {
					var content = jQuery(this);
					content.removeClass('active');
					//content.addClass('off');
					content.hide();
				});
				//selected_tab.removeClass('off');
				selected_tab.addClass('active');
				//selected_content.removeClass('off');
				//selected_content.addClass('on');
				selected_content.show();
			};
			this.init = function (tab_index) {
				tab_index = tab_index || 0;
				tab_links.bind('click', function (e) {
					e.preventDefault();
					tab.select(tab_links.index(this));
				});
				tab.select(tab_index);
			}
		});
	};
	function initTabs() {
		var tabs = jQuery('ul.tabs').tab();
		tabs.each(function () {
			this.init();
		});
	}

  // SNS 버튼 클릭 by TheBits
  var snsShare = function(){
    $('.share-btn').click(function(){
      var $sns   = $(this).data('sns');
      var $share = $(this).attr('href');

      switch($sns)
      {
        case 'url':
          var popOption = "";
        break;

        case 'twitter':
          var popOption = "width=400, height=360, resizable=no, scrollbars=no, status=no;";
        break;

        case 'facebook':
          var popOption = "width=400, height=360, resizable=no, scrollbars=no, status=no;";
        break;

        case 'hms':
          var left = (screen.width/2)-310;
          var top = (screen.height/2)-300;
          var popOption = "width=620, height=600, top="+top+", left="+left+", resizable=no, scrollbars=no, status=no;";
        break;
        case 'pinterest':
		  var $sUrl   = $(this).data('surl');
		  var $sImg = $(this).data('simg');
		  var $sTxt = $(this).data('stxt');
		  var popOption = "width=800, height=500, resizable=no, scrollbars=no, status=no;";
	    $share= "http://www.pinterest.com/pin/create/button/?url="+encodeURIComponent($sUrl)+"&media="+encodeURIComponent($sImg)+"&description="+encodeURIComponent($sTxt);
        break;
      }

	  var wp = window.open($share, 'share_'+$sns, popOption);
      if(wp){
        wp.focus();
      }
      return false;
    });
  }

  //툴팁 컨트롤
  var toolTipControl = function(){
    //클릭 이벤트
    $('.tooltipInfomation').click(function(){
      var $index       = $('.tooltipInfomation').index($(this));
      var $descClass   = $(this).data('descript_class');
      var offsetPoint  = $(this).offset();

      var $description = $(document).find($descClass).text();
      var $status      = $('.tooltipInfomation .tooltipContainer').eq($index).css('display');

      if ($status == 'none')
      {
        //위치보정
        if (offsetPoint.left < 80)
          $('.tooltipInfomation .tooltipContainer').eq($index).css('left','-50%');
        else if( $(document).width() - offsetPoint.left < 80)
          $('.tooltipInfomation .tooltipContainer').eq($index).css('right','-50%');

        $('.tooltipInfomation .tooltipContainer').eq($index).text($description);
        $('.tooltipInfomation .tooltipContainer').eq($index).stop(true,true).fadeIn('fast');
      }
      else
        $('.tooltipInfomation .tooltipContainer').eq($index).stop(true,true).fadeOut('fast');
    });
    //스크롤 이벤트
    $(window, document).scroll(function(){
      $('.tooltipInfomation .tooltipContainer').stop(true,true).css('display','none');
    });
  }

  jQuery("#mypage_order_search").bind("click", function() {
	jQuery("#orderListFrm").submit();
  });

}); //jQuery


  var search_button_check = function(){
		jQuery('.input_search').toggle();
		jQuery('.bb_search_result .bb_searh_bx .bb_search_field input[type=text] + label').css('display','inline');
  }

  var go_detail = function(goUrl){
		window.location.href=goUrl;
  }

 // Review 관련 (시작)
  var quickView_review = function(tIdx){
	view_review(tIdx);
	location.hash='bbProductDetail2';
	removeHash();
  }
  var removeHash = function(){
    history.pushState("", document.title, window.location.pathname + window.location.search);
  }


  var view_review = function(tIdx){
	if (jQuery("#review-"+tIdx).css('display')=="none"){
		var listCnt = jQuery('#review-list > dl > dd').size();
		for(z=0;z<listCnt;z++){
			var tmpID=jQuery('#review-list > dl > dd').eq(z).attr("id");
			if(tmpID!="review-"+tIdx) jQuery("#"+tmpID).hide();
		}
	}
	jQuery("#review-"+tIdx).toggle();
  }

  var clear_review = function(){
	jQuery("#r_subject").val("");
	jQuery("#r_contents").val("");
	jQuery("input:radio[name='r_value']").prop('checked', false);
	jQuery("input:file[id='fileUpload']").val('');
	jQuery('.file_wrap').find('.fake_upload').text("");

	jQuery("#attach_name").html("");
	jQuery("#attach_name").css("display","none");
  }

  var submit_review = function(){
	var jQfrm = jQuery('#reviewFrm'); 
	var rModeStr="";
	var qSubject=jQuery("#r_subject").val();
	var qContents=jQuery("#r_contents").val();
	var apiUrl=jQuery("#reviewFrm input[name=template_url]").val()+"/proc/embed-goods_review.exec.php";

	if(jQuery("#rMode").val()=="insert") rModeStr="등록";
	else rModeStr="수정";
	
	if(!qSubject){
		alert("상품평의 제목을 입력해 주세요.      ");
		jQuery("#r_subject").focus();
		return;
	}
	if(!jQuery("input:radio[name='r_value']").is(":checked")){
		alert("상품평의 별점을 선택해 주세요.      ");
		jQuery("input:radio[name='r_value']").eq(0).focus();
		return;
	}
	if(!qContents){
		alert("상품평의 내용을 입력해 주세요.      ");
		jQuery("#r_contents").focus();
		return;
	}
	if(jQuery('#review_login_flag').val()=='social' && jQuery('#rMode').val()=='insert' && jQuery("#reviewAtreeRow").css('display')=="table-row" && !jQuery("input[name='review_agree']").is(":checked")){
		alert("개인정보 수집 및 이용에 동의해 주세요.      ");
		jQuery("#review_agree").focus();
		return;
	}
	if(confirm("상품평을 "+rModeStr+"하시겠습니까?       ")){
		jQfrm.attr("action", apiUrl);
		jQfrm.ajaxForm({
			type:"POST",
			async:true,
			success:function(data, state){
				//alert(data);
				var result = data.split("|||"); 
				if(result[0] == "success"){
					alert("상품평 "+rModeStr+"을 완료하였습니다.      ");

					clear_review();
					jQuery("#itemReviewF").css("display","none");
					jQuery('#review-list > dl').html(result[1]);
					jQuery('#review-paging').html(result[2]);
					jQuery('#review-total-count').html("총 <strong>"+result[3]+"</strong>개가 있습니다.");

					if(jQuery("#rMode").val()=="insert") jQuery("#reviewFrm input[name=selected_page]").val("1");

					location.hash='bbProductDetail2';
				}
				else if(result[0] == "DbError"){
					alert("[Error !] DB 오류가 발생하였습니다.     ");
				}
				else if(result[0] == "errorFileName"){
					alert("첨부파일의 이름이 올바르지 않습니다.     ");
				}
				else if(result[0] == "errorFileExtend"){
					alert("첨부파일은 'JPG/GIF/PNG'만 업로드가 가능합니다.     ");
				}
				else if(result[0] == "errorFileUpload"){
					alert("첨부파일 업로드 중 오류가 발생하였습니다.     ");
				}
				else{
					alert("서버와의 통신이 실패했습니다.   ");
				}
			}
		});	

		jQfrm.submit(); 
	}
  }

  var review_write = function(rMode,tIdx){
	if(rMode=="insert"){
		jQuery("#reviewFrm input[name=paged]").val('1');
	}
	else{
		jQuery("#reviewFrm input[name=paged]").val(jQuery("#reviewFrm input[name=selected_page]").val());
	}

	if(jQuery("#itemReviewF").css("display")=="none"){
		clear_review();
		jQuery("#itemReviewF").css("display","block");
		location.hash='itemReviewF';
	}
	else{
		if(rMode=="insert"){
			jQuery("#reviewFrm input[name=paged]").val('1');
			if(jQuery("#rMode").val()=="insert"){
				clear_review();
				jQuery("#itemReviewF").css("display","none");
			}
			else{
				clear_review();
				location.hash='itemReviewF';
			}
		}
		else{
			location.hash='itemReviewF';
		}
	}

	jQuery("#rIdx").val(tIdx);
	jQuery("#rMode").val(rMode);
	if(jQuery('#review_login_flag').val()=='social' && rMode=='insert') jQuery("#reviewAtreeRow").css("display","table-row");
  }

  var review_hide = function(){
	jQuery("#itemReviewF").css("display","none");
	location.hash='bbProductDetail2';
  }

  var review_go_page = function(tPage){
	var rMode="paging";
	var rGoodsIdx=jQuery("#rGoodsIdx").val();
	var review_per_page=jQuery("#review_per_page").val();
	var page_block=jQuery("#reviewFrm input[name=page_block]").val();
	var function_name=jQuery("#reviewFrm input[name=function_name]").val();
	var apiUrl=jQuery("#reviewFrm input[name=template_url]").val()+"/proc/embed-goods_review.exec.php";

	clear_review();

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {rMode:rMode, rGoodsIdx:rGoodsIdx, tPage:tPage, review_per_page:review_per_page, page_block:page_block, function_name:function_name}, 
		success: function(data){
			//alert(data);
			var result = data.split("|||"); 

			if(result[0] == "success"){
				jQuery('#review-list > dl').html(result[1]);
				jQuery('#review-paging').html(result[2]);
				jQuery('#review-total-count').html("총 <strong>"+result[3]+"</strong>개가 있습니다.");
				jQuery("#reviewFrm input[name=selected_page]").val(tPage);
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

  var review_remove = function(tIdx){
	var rMode="remove";
	var rGoodsIdx=jQuery("#rGoodsIdx").val();
	var apiUrl=jQuery("#reviewFrm input[name=template_url]").val()+"/proc/embed-goods_review.exec.php";

	if(confirm("해당 상품문의 글을 삭제하시겠습니까?    ")){
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: {rMode:rMode, rGoodsIdx:rGoodsIdx, tIdx:tIdx}, 
			success: function(data){
				//alert(data);
				var result = data; 

				if(result == "success"){
					alert("해당 상품문의 글을 삭제하였습니다.   ");
					clear_review();
					jQuery("#itemReviewF").css("display","none");
					review_go_page(jQuery("#reviewFrm input[name=selected_page]").val());
				}
				else if(result=='PermisionError'){
					alert("접근권한이 없습니다.   ");
				}
				else if(result=='DataError'){
					alert("잘못된 데이터 입니다.   ");
				}
				else if(result=='notExist'){
					alert("상품문의 글이 존재하지 않습니다.   ");
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
  }

  var review_modify = function(tIdx){
	var rMode="getReview";
	var rGoodsIdx=jQuery("#rGoodsIdx").val();
	var apiUrl=jQuery("#reviewFrm input[name=template_url]").val()+"/proc/embed-goods_review.exec.php";

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {rMode:rMode, rGoodsIdx:rGoodsIdx, tIdx:tIdx}, 
		success: function(data){
			//alert(data);
			var result = data.split("|||"); 

			if(result[0] == "success"){
				if(jQuery('#review_login_flag').val()=='social') jQuery("#reviewAtreeRow").css("display","none");
				review_write("modify",tIdx);
				jQuery("#r_subject").val(result[1]);
				jQuery("#r_contents").val(result[2]);

				if(result[3]>0){
					jQuery("input:radio[name='r_value']:radio[value='"+result[3]+"']").prop("checked",true);
				}
				if(result[4]){
					jQuery("#attach_name").css("display","block");
					jQuery("#attach_name").html("이미지 : "+result[4]+"&nbsp;&nbsp;&nbsp;<input type='checkbox' name='attach_delete' value='delete' /><font color='#ED1C24'>삭제</font>");
				}
			}
			else if(result[0]=='PermisionError'){
				alert("접근권한이 없습니다.   ");
			}
			else if(result[0]=='DataError'){
				alert("잘못된 데이터 입니다.   ");
			}
			else if(result[0]=='notExist'){
				alert("상품문의 글이 존재하지 않습니다.   ");
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
// Review 관련 (끝)


// QnA 관련 (시작)
var view_answer = function(tIdx){
	if (jQuery("#qna-"+tIdx).css('display')=="none"){
		var listCnt = jQuery('#qna-list > dl > dd').size();
		for(z=0;z<listCnt;z++){
			var tmpID=jQuery('#qna-list > dl > dd').eq(z).attr("id");
			if(tmpID!="qna-"+tIdx) jQuery("#"+tmpID).hide();
		}
	}
	jQuery("#qna-"+tIdx).toggle();
};

var clear_qna = function(){
	jQuery("#q_subject").val("");
	jQuery("#q_contents").val("");
	jQuery("#q_secret").attr("checked",false);
};

var submit_qna = function(){
	var tModeStr="";
	var qSubject=jQuery("#q_subject").val();
	var qContents=jQuery("#q_contents").val();
	var apiUrl=jQuery("#qnaFrm input[name=template_url]").val()+"/proc/embed-goods_enquire.exec.php";

	if(jQuery("#qnaFrm input[name=tMode]").val()=="insert") tModeStr="등록";
	else tModeStr="수정";
	
	if(!qSubject){
		alert("상품문의 제목을 입력해 주세요.      ");
		jQuery("#q_subject").focus();
		return;
	}
	if(!qContents){
		alert("상품문의 내용을 입력해 주세요.      ");
		jQuery("#q_contents").focus();
		return;
	}
	if(jQuery('#qna_login_flag').val()=='social' && jQuery("#qnaFrm input[name=tMode]").val()=='insert' && jQuery("#qnaAtreeRow").css('display')=="table-row" && !jQuery("input[name='qna_agree']").is(":checked")){
		alert("개인정보 수집 및 이용에 동의해 주세요.      ");
		jQuery("#qna_agree").focus();
		return;
	}
	if(confirm("상품문의를 "+tModeStr+"하시겠습니까?       ")){
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: jQuery("#qnaFrm").serialize(), 
			success: function(data){
				//alert(data);
				var result = data.split("|||"); 

				if(result[0] == "success"){
					alert("상품문의 "+tModeStr+"을 완료하였습니다.      ");

					jQuery("#itemQuestionF").css("display","none");
					clear_qna();
					jQuery('#qna-list > dl').html(result[1]);
					jQuery('#qna-paging').html(result[2]);
					jQuery('#qna-total-count').html("총 <strong>"+result[3]+"</strong>개가 있습니다.");

					if(jQuery("#qnaFrm input[name=tMode]").val()=="insert") jQuery("#qnaFrm input[name=selected_page]").val("1");
					location.hash='bbProductDetail3';
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
};

var qna_write = function(tMode,tIdx){
	if(tMode=="insert"){
		jQuery("#qnaFrm input[name=paged]").val('1');
	}
	else{
		jQuery("#qnaFrm input[name=paged]").val(jQuery("#qnaFrm input[name=selected_page]").val());
	}

	if(jQuery("#itemQuestionF").css("display")=="none"){
		clear_qna();
		jQuery("#itemQuestionF").css("display","block");
		location.hash='itemQuestionF';
	}
	else{
		if(tMode=="insert"){
			jQuery("#qnaFrm input[name=paged]").val('1');
			if(jQuery("#qnaFrm input[name=tMode]").val()=="insert"){
				clear_qna();
				jQuery("#itemQuestionF").css("display","none");
			}
			else{
				clear_qna();
				location.hash='itemQuestionF';
			}
		}
		else{
			location.hash='itemQuestionF';
		}
	}

	jQuery("#qIdx").val(tIdx);
	jQuery("#qnaFrm input[name=tMode]").val(tMode);
	if(jQuery('#qna_login_flag').val()=='social' && tMode=='insert') jQuery("#qnaAtreeRow").css("display","table-row");
};

var qna_hide = function(){
	jQuery("#itemQuestionF").css("display","none");
	location.hash='bbProductDetail3';
};

var qna_go_page = function(tPage){
	var tMode="paging";
	var qGoodsIdx=jQuery("#qGoodsIdx").val();
	var qna_per_page=jQuery("#qna_per_page").val();
	var page_block=jQuery("#qnaFrm input[name=page_block]").val();
	var function_name=jQuery("#qnaFrm input[name=function_name]").val();
	var apiUrl=jQuery("#qnaFrm input[name=template_url]").val()+"/proc/embed-goods_enquire.exec.php";

	clear_qna();

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {tMode:tMode, qGoodsIdx:qGoodsIdx, tPage:tPage, qna_per_page:qna_per_page, page_block:page_block, function_name:function_name}, 
		success: function(data){
			//alert(data);
			var result = data.split("|||"); 

			if(result[0] == "success"){
				jQuery('#qna-list > dl').html(result[1]);
				jQuery('#qna-paging').html(result[2]);
				jQuery('#qna-total-count').html("총 <strong>"+result[3]+"</strong>개가 있습니다.");
				jQuery("#qnaFrm input[name=selected_page]").val(tPage);
			}
			else{
				alert("서버와의 통신이 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
};

var qna_remove = function(tIdx){
	var tMode="remove";
	var qGoodsIdx=jQuery("#qGoodsIdx").val();
	var apiUrl=jQuery("#qnaFrm input[name=template_url]").val()+"/proc/embed-goods_enquire.exec.php";

	if(confirm("해당 상품문의 글을 삭제하시겠습니까?    ")){
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: {tMode:tMode, qGoodsIdx:qGoodsIdx, tIdx:tIdx}, 
			success: function(data){
				//alert(data);
				var result = data; 

				if(result == "success"){
					alert("해당 상품문의 글을 삭제하였습니다.   ");
					jQuery("#itemQuestionF").css("display","none");
					clear_qna();
					qna_go_page(jQuery("#qnaFrm input[name=selected_page]").val());
				}
				else if(result=='PermisionError'){
					alert("접근권한이 없습니다.   ");
				}
				else if(result=='DataError'){
					alert("잘못된 데이터 입니다.   ");
				}
				else if(result=='notExist'){
					alert("상품문의 글이 존재하지 않습니다.   ");
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
};

var qna_modify = function(tIdx){
	var tMode="getQnA";
	var qGoodsIdx=jQuery("#qGoodsIdx").val();
	var apiUrl=jQuery("#qnaFrm input[name=template_url]").val()+"/proc/embed-goods_enquire.exec.php";

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {tMode:tMode, qGoodsIdx:qGoodsIdx, tIdx:tIdx}, 
		success: function(data){
			//alert(data);
			var result = data.split("|||"); 
			if(result[0] == "success"){
				if(jQuery('#qna_login_flag').val()=='social') jQuery("#qnaAtreeRow").css("display","none");
				qna_write("modify",tIdx);
				jQuery("#q_subject").val(result[1]);
				jQuery("#q_contents").val(result[2].replace(/\\/gi,""));
				if(result[3]=="on") jQuery("#q_secret").prop("checked",true);
				else jQuery("#q_secret").prop("checked",false);
			}
			else if(result[0]=='PermisionError'){
				alert("접근권한이 없습니다.   ");
			}
			else if(result[0]=='DataError'){
				alert("잘못된 데이터 입니다.   ");
			}
			else if(result[0]=='notExist'){
				alert("상품문의 글이 존재하지 않습니다.   ");
			}
			else{
				alert("서버와의 통신이 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
};

// QnA 관련 (끝)


//세자리 콤마
  var GetCommaValue = function(num) {
	var len, point, str;  
	num = num + "";  
	point = num.length % 3  
	len = num.length;  

	str = num.substring(0, point);  
	while (point < len) {  
		if (str != "") str += ",";  
		str += num.substring(point, point + 3);  
		point += 3;  
	}  
	return str;  
  }

// 상품상세 (시작)
  var detail_basicOptChange = function(oCnt,tOpt,gIdx,oData){ // 첫번째 옵션 변경 : 옵션수(2,1), 옵션번호(1,2), 상품idx, 옵션값
	if(oCnt==2 && tOpt==1){
		var apiUrl=jQuery("#goods_template_url").val()+"/proc/goods-detail.exec.php";
		var tMode="secondOption";
		if(oData){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: apiUrl, 
				data: {tMode:tMode, gIdx:gIdx, oData:oData}, 
				success: function(data){
					//alert(data);
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
							+"		<button type=\"button\" onClick=\"option_changeCount('basic','minus','"+rndId+"',"+goods_optTotal+");\" class=\"bb_minus\"><span>수량감소</span></button>"
							+"		<button type=\"button\" onClick=\"option_changeCount('basic','plus','"+rndId+"',"+goods_optTotal+");\" class=\"bb_plus\"><span>수량증가</span></button>"
							+"		<label class=\"blind\" for=\"dummyID380\">수량</label><input type=\"text\" name=\"goods_basic_count[]\" id=\"goods_basic_count[]\" value=\"1\" readonly />"
							+"	  </div>"
							+"	  <em id=\"sOpt_unit_"+rndId+"\" class=\"bb_pri\">"+GetCommaValue(goods_optTotal)+"원</em>"
							+"	  <button type=\"button\" class=\"bb_opt_del\" onClick=\"selected_option_remove('basic','2',"+rndId+","+goods_optTotal+");\"><span>선택옵션 삭제</span></button>"
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
  var detail_addOptChange = function(tOpt,oData){ // 옵션번호(1,2), 옵션값
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
						+"		<button type=\"button\" onClick=\"option_changeCount('add','minus','"+rndId+"',"+add_goodsPrice+");\" class=\"bb_minus\"><span>수량감소</span></button>"
						+"		<button type=\"button\" onClick=\"option_changeCount('add','plus','"+rndId+"',"+add_goodsPrice+");\" class=\"bb_plus\"><span>수량증가</span></button>"
						+"		<label class=\"blind\" for=\"dummyID380\">수량</label><input type=\"text\" name=\"goods_add_count[]\" id=\"goods_add_count[]\" value=\"1\" readonly />"
						+"	  </div>"
						+"	  <em id=\"sOpt_unit_"+rndId+"\" class=\"bb_pri\">"+GetCommaValue(add_goodsPrice)+"원</em>"
						+"	  <button type=\"button\" class=\"bb_opt_del\" onClick=\"selected_option_remove('add','"+tOpt+"',"+rndId+","+add_goodsPrice+");\"><span>선택옵션 삭제</span></button>"
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
  var selected_option_remove = function(oType,tOpt,rndId,gOptTotal){// 옵션번호(1,2), object ID, 차감금액
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
  var option_changeCount = function(cType,tOpt,rndId,gOptTotal){
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

		if(new_optCount<='0'){
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

  // 바로구매/장바구니/찜
  var go_buy = function(sType){
	var goods_TotalPrice=jQuery("#goods_total_price").val();
	var oChoice="";
	var oTitle="";
	var liExist=false;
	var compStr="";
	var aCnt=jQuery("#goods_add_option_list > li").size();
	var apiUrl=jQuery("#goods_template_url").val()+"/proc/goods-detail.exec.php";
	var homeUrl=jQuery("#home_url").val();


	if(sType!='wishlist'){
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
	}

	jQuery("#tMode").val("addCart");
	jQuery("#sType").val(sType);

	if(sType=='direct'){
		if(common_var.u =="" ) jQuery("#goodsFrm").attr("method","post").attr("action",common_var.login_page).submit();
		else jQuery("#goodsFrm").attr("method","post").attr("action",homeUrl+"/?bbsePage=order").submit();
	}
	else if(sType=='social-direct'){
		jQuery("#goodsFrm").attr("method","post").attr("action",homeUrl+"/?bbsePage=order-agree").submit();
	}
	else{
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: jQuery("#goodsFrm").serialize(), 
			success: function(data){
				//alert(data);
				var result = data.split("|||"); 

				if(result[0] == "success"){
					if(sType=='cart'){
						if(confirm('상품이 장바구니에 저장되었습니다.   \n장바구니를 확인 하시겠습니까?')){
							window.location.href=homeUrl+"/?bbsePage=cart";
						}
					}
					else if(sType=='wishlist'){
						if(confirm('상품이 찜리스트에 저장되었습니다.   \n찜리스트를 확인 하시겠습니까?')){
							window.location.href=homeUrl+"/?bbseMy=interest";
						}
					}
				}
				else if(result[0] == "notExistGoods"){
					alert("존재하지 않는 상품정보입니다.   ");
				}
				else if(result[0] == "loginError"){
					alert("회원전용 서비스 입니다. 로그인 후 이용해 주세요.   ");
					go_login();
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
  }

  // 네이버 페이 주문정보 연동(시작)
  var buy_detailNpay=function (){ // 상품상세
		var goods_TotalPrice=jQuery("#goods_total_price").val();
		var oChoice="";
		var oTitle="";
		var liExist=false;
		var compStr="";
		var aCnt=jQuery("#goods_add_option_list > li").size();
		var apiUrl=common_var.goods_template_url+"/npay/bbse-npay-order.exec.php";
		var frmId="goodsFrm";

		jQuery("input[data-npay='off']:checked").attr("checked", false);

		if(jQuery('#order_type').val()=="cart"){
			frmId="orderFrm";
			if(jQuery("input[name='gidx[]']:checked").length == 0) {
				alert("네이버페이로 주문할 상품을 선택해주세요.");
				return;
			}
		}
		else{
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
		}

		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: jQuery("#"+frmId).serialize(), 
			success: function(data){
				//alert(data);
				var result = data.split("|||"); 
				if(result[0] == "success" && result[1] && result[2]  && result[3]){
					jQuery("#nPay_order_orderId").val(result[1]);
					jQuery("#nPay_order_shopId").val(result[2]);
					jQuery("#nPay_order_totalPrice").val(result[3]);
					jQuery("#nPayOrderFrm").submit();
				}
				else if(result[0] == "emptyNpayId"){
					alert("네이버 페이 ID가 올바르지 않습니다.   ");
				}
				else if(result[0] == "emptyNpayKey"){
					alert("네이버 페이 가맹점 인증키가 올바르지 않습니다.   ");
				}
				else if(result[0] == "emptyGoods"){
					alert("상품정보가 올바르지 않습니다.   ");
				}
				else if(result[0] == "offNpay"){
					alert("네이버 페이 설정이 사용안함 상태입니다.   ");
				}
				else if(result[0] == "regFail"){
					alert(result[1]);
				}
				else{
					alert("서버와의 통신이 실패했습니다.   ");
				}
			}, 
			error: function(data, status, err){
				alert("서버와의 통신이 실패했습니다.   \n네이버페이 - 상점 정보를 확인해 주세요.   ");
			}
		});

		// 네이버 페이 주문서 페이지로 이동.
		return false;
  }

  var wishlist_nPay=function(url){
		// 네이버 페이로 찜 정보를 등록하는 가맹점 페이지 팝업 창 생성.
		// 해당 페이지에서 찜 정보 등록 후 네이버 페이 찜 페이지로 이동.
		var apiUrl=common_var.goods_template_url+"/npay/bbse-npay-wish.exec.php";
		var frmId="goodsFrm";
		var nPayWishUrl=jQuery("#nPayResult").data("wish");
		var agentType=jQuery("#nPayResult").data("agent");

		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: jQuery("#"+frmId).serialize(), 
			success: function(data){
				//alert(data);
				var result = data.split("|||"); 
				if(result[0] == "success" && result[1] && result[2]){
					if(agentType=='mobile'){
						document.location.href=nPayWishUrl+"?SHOP_ID="+result[1]+"&ITEM_ID="+result[2];
					}
					else{
						window.open(nPayWishUrl+"?SHOP_ID="+result[1]+"&ITEM_ID="+result[2],"","scrollbars=yes,width=460,height=517");
					}
				}
				else if(result[0] == "emptyNpayId"){
					alert("네이버 페이 ID가 올바르지 않습니다.   ");
				}
				else if(result[0] == "emptyNpayKey"){
					alert("네이버 페이 가맹점 인증키가 올바르지 않습니다.   ");
				}
				else if(result[0] == "emptyGoods"){
					alert("상품정보가 올바르지 않습니다.   ");
				}
				else if(result[0] == "offNpay"){
					alert("네이버 페이 설정이 사용안함 상태입니다.   ");
				}
				else if(result[0] == "regFail"){
					alert(result[1]);
				}
				else{
					alert("서버와의 통신이 실패했습니다.   ");
				}
			}, 
			error: function(data, status, err){
				alert("서버와의 통신이 실패했습니다.   \n네이버페이 - 상점 정보를 확인해 주세요.   ");
			}
		});

		return false;
  }

  var not_buy_nPay=function(){
		alert("죄송합니다. 네이버 페이로 구매가 불가한 상품입니다.");
		return false;
  }
  // 네이버 페이 주문정보 연동 (끝)

  // 바로구매/장바구니/찜
  var go_login = function(){
	var login_url=jQuery("#login_url").val();
	window.location.href=login_url;
  }

  // 품절상품 입고알림 신청
  var soldout_notice = function(tIdx,tMode){
	var apiUrl=jQuery("#goods_template_url").val()+"/proc/goods-soldout-notice.exec.php";
	if(confirm("해당 상품을 '품절상품 입고알림' 목록에 추가하시겠습니까?    ")){
		jQuery.ajax({
			type: 'post',
			async: false,
			url: apiUrl,
			data: {tMode:tMode, tIdx:tIdx},
			success: function(data){
				//alert(data);
				var result = data.split("|||");

				if(result[0] == "success"){
					if(tMode=="insertNotice"){
						alert("해상 상품을 '품절상품 입고알림' 목록에 추가하였습니다.   ");
					}
				}
				else if(result[0] == "existSoldoutNotice"){
					alert("이미 '품절상품 입고알림' 목록에 추가된 상품입니다.   ");
				}
				else if(result[0] == "loginError"){
					alert("회원전용 서비스 입니다. 로그인 후 이용해 주세요.       ");
				}
				else if(result[0] == "DataError"){
					alert("상품정보가 존재하지 않습니다.       ");
				}
				else if(result[0] == "notUseSoldoutNotice"){
					alert("'품절상품 입고알림' 기능을 사용할 수 없습니다.       ");
				}
				else if(result[0] == "memberInfoNull"){
					alert("회원정보의 휴대전화번호와 E-mail 정보가 존재하지 않습니다.       ");
				}
				else if(result[0] == "memberHpNull"){
					alert("회원정보의 휴대전화번호가 존재하지 않습니다.       ");
				}
				else if(result[0] == "memberEmailNull"){
					alert("회원정보의 E-mail 정보가 존재하지 않습니다.       ");
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
  }

// 품절상품 입고알림 삭제
  var remove_noticeList = function(tIdx){
	var apiUrl=jQuery("#goods_template_url").val()+"/proc/goods-soldout-notice.exec.php";
	if(tIdx){
		var tMode="removeNotice";
		var msgStr="해당";
	}
	else{
		var tMode="removeCheckNoticelist";
		var tIdx="";
		var chked=jQuery("input[name=nidx\\[\\]]:checked").not(':disabled').size();

		for(i=0;i<chked;i++){
			if(tIdx) tIdx +=",";
			tIdx +=jQuery("input[name=nidx\\[\\]]:checked").not(':disabled').eq(i).val();
		}

		if(chked<=0 || !tIdx) {
			alert("삭제할 '품절상품 알림목록'을 선택해주세요.");
			return;
		}
		var msgStr="선택하신";
	}

	if(confirm(msgStr+" '품절상품 알림목록'을 삭제하시겠습니까?    ")){
		jQuery.ajax({
			type: 'post',
			async: false,
			url: apiUrl,
			data: {tMode:tMode, tIdx:tIdx},
			success: function(data){
				//alert(data);
				var result = data.split("|||");

				if(result[0] == "success"){
					if(tMode=="removeNotice"){
						jQuery("#noticelist_"+tIdx).remove();
					}
					else{
						var nIdx=tIdx.split(",");
						for(i=0;i<nIdx.length;i++){
							jQuery("#noticelist_"+nIdx[i]).remove();
						}

						jQuery("#allChkSelectNotice").prop("checked",false);
					}

					var newChked=jQuery("input[name=nidx\\[\\]]").not(':disabled').size();
					if(newChked<='0'){
						jQuery("#emptyNoticelist").css("display","block");
					}

					jQuery("#noticelistTotal").html(result[1]);

					alert("'품절상품 알림목록'을 정상적으로 삭제하였습니다.   ");
				}
				else if(result[0] == "loginError"){
					alert("회원전용 서비스 입니다. 로그인 후 이용해주세요.   ");
				}
				else if(result[0] == "notExistNoticeist"){
					alert("품절상품 입고알림 정보가 존재하지 않습니다.       ");
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
  }

 // 상품상세 (끝)

// 관심상품(찜) (시작)
  var remove_wishlist = function(tIdx){
	var apiUrl=jQuery("#goods_template_url").val()+"/proc/mypage-interest.exec.php";
	if(tIdx){
		var tMode="removeWishlist";
		var msgStr="해당";
	}
	else{
		var tMode="removeCheckWishlist";
		var tIdx="";
		var chked=jQuery("input[name=gidx\\[\\]]:checked").not(':disabled').size();

		for(i=0;i<chked;i++){
			if(tIdx) tIdx +=",";
			tIdx +=jQuery("input[name=gidx\\[\\]]:checked").not(':disabled').eq(i).val();
		}

		if(chked<=0 || !tIdx) {
			alert("삭제할 관심상품(찜)을 선택해주세요.");
			return;
		}
		var msgStr="선택하신";
	}

	if(confirm(msgStr+" 상품을 관심상품(찜)에서 삭제하시겠습니까?    ")){
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: {tMode:tMode, tIdx:tIdx}, 
			success: function(data){
				//alert(data);
				var result = data.split("|||"); 

				if(result[0] == "success"){
					if(tMode=="removeWishlist"){
						jQuery("#wishlist_"+tIdx).remove();
					}
					else{
						var wIdx=tIdx.split(",");
						for(i=0;i<wIdx.length;i++){
							jQuery("#wishlist_"+wIdx[i]).remove();
						}

						jQuery("#allChkSelect").prop("checked",false);
					}

					var newChked=jQuery("input[name=gidx\\[\\]]").not(':disabled').size();
					if(newChked<='0'){
						jQuery("#emptyWishlist").css("display","block");
					}

					jQuery("#wishlistTotal").html(result[1]);
				}
				else if(result[0] == "notExistWishlist"){
					alert("해상 상품이 관심상품(찜) 목록에 존재하지 않습니다.   ");
				}
				else if(result[0] == "loginError"){
					alert("회원전용 서비스 입니다. 로그인 후 이용해주세요.   ");
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
  }

  var popup_remove_wishlist = function(tIdx){
	jQuery("#wishlist_"+tIdx).remove();

	var newChked=jQuery("input[name=gidx\\[\\]]").not(':disabled').size();
	if(newChked<='0'){
		jQuery("#emptyWishlist").css("display","block");
	}

	jQuery("#wishlistTotal").html(newChked);
  }

// 관심상품(찜) (끝)


// 마이페이지 QnA 관련 (시작)
var mypage_view_answer = function(tIdx){
	if (jQuery("#qna-"+tIdx).css('display')=="none"){
		var listCnt = jQuery('#qna-list > dl > dd').size();
		for(z=0;z<listCnt;z++){
			var tmpID=jQuery('#qna-list > dl > dd').eq(z).attr("id");
			if(tmpID!="qna-"+tIdx) jQuery("#"+tmpID).hide();
		}
	}
	//jQuery("#qna-"+tIdx).toggle();
	if ( jQuery("#qna-"+tIdx).css('display') == 'none' )
	{
		jQuery("#qna-"+tIdx).slideUp('fast');
		jQuery("#qna-"+tIdx).slideDown('fast');
	}
	else
	{
		jQuery("#qna-"+tIdx).slideUp('fast');
	}
};

var mypage_clear_qna = function(){
	jQuery("#q_subject").val("");
	jQuery("#q_contents").val("");
	jQuery("#q_secret").attr("checked",false);
};

var mypage_submit_qna = function(){
	var tModeStr="";
	var qSubject=jQuery("#q_subject").val();
	var qContents=jQuery("#q_contents").val();
	var apiUrl=jQuery("#template_url").val()+"/proc/embed-goods_enquire_mypage.exec.php";

	if(jQuery("#tMode").val()=="insert") tModeStr="등록";
	else tModeStr="수정";
	
	if(!qSubject){
		alert("상품문의 제목을 입력해 주세요.      ");
		jQuery("#q_subject").focus();
		return;
	}
	if(!qContents){
		alert("상품문의 내용을 입력해 주세요.      ");
		jQuery("#q_contents").focus();
		return;
	}
	if(confirm("상품문의를 "+tModeStr+"하시겠습니까?       ")){
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: jQuery("#qnaFrm").serialize(), 
			success: function(data){
				var result = data.split("|||"); 
				if(result[0] == "success"){
					alert("상품문의 "+tModeStr+"을 완료하였습니다.      ");
					jQuery("#itemQuestionF").css("display","none");
					mypage_clear_qna();
					jQuery('#qna-list > dl').html(result[1]);
					jQuery('#qna-paging').html(result[2]);
					jQuery('#qna-total-count').html("총 <strong>"+result[3]+"</strong>개가 있습니다.");

					if(jQuery("#tMode").val()=="insert") jQuery("#selected_page").val("1");
					location.hash='bbMan2man';
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
};

var mypage_qna_write = function(tMode,tIdx){
	if(tMode=="insert"){
		jQuery("#paged").val('1');
	}
	else{
		jQuery("#paged").val(jQuery("#selected_page").val());
	}

	if(jQuery("#itemQuestionF").css("display")=="none"){
		mypage_clear_qna();
		jQuery("#itemQuestionF").css("display","block");
		location.hash='itemQuestionF';
	}
	else{
		location.hash='itemQuestionF';
	}

	jQuery("#qIdx").val(tIdx);
	jQuery("#tMode").val(tMode);
};

var mypage_qna_hide = function(){
	jQuery("#itemQuestionF").css("display","none");
};

var mypage_qna_go_page = function(tPage){
	var tMode="paging";
	var qna_per_page=jQuery("#qna_per_page").val();
	var page_block=jQuery("#page_block").val();
	var function_name=jQuery("#function_name").val();
	var apiUrl=jQuery("#template_url").val()+"/proc/embed-goods_enquire_mypage.exec.php";

	mypage_clear_qna();

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {tMode:tMode, tPage:tPage, qna_per_page:qna_per_page, page_block:page_block, function_name:function_name}, 
		success: function(data){

			var result = data.split("|||"); 

			if(result[0] == "success"){
				jQuery('#qna-list > dl').html(result[1]);
				jQuery('#qna-paging').html(result[2]);
				jQuery('#qna-total-count').html("총 <strong>"+result[3]+"</strong>개가 있습니다.");
				jQuery("#selected_page").val(tPage);
			}
			else{
				alert("서버와의 통신이 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
};

var mypage_qna_remove = function(tIdx){
	var tMode="remove";
	var apiUrl=jQuery("#template_url").val()+"/proc/embed-goods_enquire_mypage.exec.php";

	if(confirm("해당 상품문의 글을 삭제하시겠습니까?    ")){
		jQuery.ajax({
			type: 'post', 
			async: false, 
			url: apiUrl, 
			data: {tMode:tMode, tIdx:tIdx}, 
			success: function(data){
				var result = data;
				if(result == "success"){
					alert("해당 상품문의 글을 삭제하였습니다.   ");
					mypage_qna_go_page(jQuery("#selected_page").val());
				}
				else if(result=='PermisionError'){
					alert("접근권한이 없습니다.   ");
				}
				else if(result=='DataError'){
					alert("잘못된 데이터 입니다.   ");
				}
				else if(result=='notExist'){
					alert("상품문의 글이 존재하지 않습니다.   ");
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
};

var mypage_qna_modify = function(tIdx){
	var tMode="getQnA";
	var apiUrl=jQuery("#template_url").val()+"/proc/embed-goods_enquire_mypage.exec.php";

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {tMode:tMode, tIdx:tIdx}, 
		success: function(data){

			var result = data.split("|||"); 

			if(result[0] == "success"){
				mypage_qna_write("modify",tIdx);
				jQuery("#q_subject").val(result[1]);
				jQuery("#q_contents").val(result[2].replace(/\\/gi,""));
				if(result[3]=="on") jQuery("#q_secret").prop("checked",true);
				else jQuery("#q_secret").prop("checked",false);
			}
			else if(result[0]=='PermisionError'){
				alert("접근권한이 없습니다.   ");
			}
			else if(result[0]=='DataError'){
				alert("잘못된 데이터 입니다.   ");
			}
			else if(result[0]=='notExist'){
				alert("상품문의 글이 존재하지 않습니다.   ");
			}
			else{
				alert("서버와의 통신이 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
};
// 마이페이지 QnA 관련 (끝)

//상품 찜하기 (시작)
var go_wishlist = function(gidx){
	var apiUrl=common_var.goods_template_url+"/proc/goods-detail.exec.php";
	var homeUrl=common_var.home_url;
	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {
			tMode: 'addCart',
			sType: 'wishlist',			
			goods_idx: gidx
		}, 
		success: function(data){

			var result = data.split("|||"); 

			if(result[0] == "success"){
				if(confirm('상품이 찜리스트에 저장되었습니다.   \n찜리스트를 확인 하시겠습니까?')){
					window.location.href=homeUrl+"/?bbseMy=interest";
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
//상품 찜하기 (끝)


var  shareKakaoTalk= function($label, $src, $text, $url){
  Kakao.Link.createTalkLinkButton({
	container : '.kakaotalk-link',
	label     : $label,
	image     : {
	  src    : $src,
	  width  : '300',
	  height : '200'
	},
	webButton : {
	  text : $text,
	  url  : $url
	}
  });
}//SHARE kakao talk

//SHARE kakao story
var shareKakaoStory = function($post, $title){
	Kakao.Story.open({
		url: $post,
		text: $title
	});
}//SHARE kakao story

/*
// 카카오톡 SNS 공유 연동
var shareKakaoTalk = function($key, $domain, $lable, $image, $msg){
  Kakao.init($key);
  Kakao.Link.createTalkLinkButton({
	container : '.kakaotalk-link',
	label     : $lable,
	image     : {
	  src    : $image,
	  width  : '300',
	  height : '200'
	},
	webButton : {
	  text : $msg,
	  url  : $domain
	}
  });
}

// 카카오스토리 SNS 공유 연동
var shareKakaoStory = function($post, $appID, $appName, $title, $desc, $thumbnail){
	kakao.link("story").send({
	 post    : $post,
	 appid   : $appID,
	 appver  : "1.0",
	 appname : $appName,
	 urlinfo : JSON.stringify({title:$title, desc:$desc, imageurl:[$thumbnail], type:"article"})
	});
}
*/
  //쿠키 저장
  function setCookie(name,value,expdate) {
	var expires = new Date();
	expires.setTime(expires.getTime() + 1000 * 3600 * 24 * 30);
	document.cookie = name + "=" + escape(value) +"; path=/; expires=" + expires.toGMTString();
  }

  //쿠키 가져오기
  function getCookie(keyname){
	cookies = document.cookie+';';
	index = cookies.indexOf(keyname,0);
	if(index!=-1){
		tmp = cookies.substring(index,cookies.length);
		searchidx1 = tmp.indexOf("=",0)+1;
        searchidx2 = tmp.indexOf(";",searchidx1);
		return (unescape(tmp.substring(searchidx1,searchidx2)));
	}
	else{
		return false;
	}
  }

// 소셜로그인 팝업
function socialLognPopup(tType,tWidth,tHeight){
	window.open(common_var.goods_template_url+'/snslogin/social-login-'+tType+'-popup.php', tType+'LoginAuth', 'width='+tWidth+', height='+tHeight+',  scrollbars=yes');
}

/*
 *	jQuery carouFredSel 6.2.1
 *	Demo's and documentation:
 *	caroufredsel.dev7studios.com
 *
 *	Copyright (c) 2013 Fred Heusschen
 *	www.frebsite.nl
 *
 *	Dual licensed under the MIT and GPL licenses.
 *	http://en.wikipedia.org/wiki/MIT_License
 *	http://en.wikipedia.org/wiki/GNU_General_Public_License
 */
(function($){function sc_setScroll(a,b,c){return"transition"==c.transition&&"swing"==b&&(b="ease"),{anims:[],duration:a,orgDuration:a,easing:b,startTime:getTime()}}function sc_startScroll(a,b){for(var c=0,d=a.anims.length;d>c;c++){var e=a.anims[c];e&&e[0][b.transition](e[1],a.duration,a.easing,e[2])}}function sc_stopScroll(a,b){is_boolean(b)||(b=!0),is_object(a.pre)&&sc_stopScroll(a.pre,b);for(var c=0,d=a.anims.length;d>c;c++){var e=a.anims[c];e[0].stop(!0),b&&(e[0].css(e[1]),is_function(e[2])&&e[2]())}is_object(a.post)&&sc_stopScroll(a.post,b)}function sc_afterScroll(a,b,c){switch(b&&b.remove(),c.fx){case"fade":case"crossfade":case"cover-fade":case"uncover-fade":a.css("opacity",1),a.css("filter","")}}function sc_fireCallbacks(a,b,c,d,e){if(b[c]&&b[c].call(a,d),e[c].length)for(var f=0,g=e[c].length;g>f;f++)e[c][f].call(a,d);return[]}function sc_fireQueue(a,b,c){return b.length&&(a.trigger(cf_e(b[0][0],c),b[0][1]),b.shift()),b}function sc_hideHiddenItems(a){a.each(function(){var a=$(this);a.data("_cfs_isHidden",a.is(":hidden")).hide()})}function sc_showHiddenItems(a){a&&a.each(function(){var a=$(this);a.data("_cfs_isHidden")||a.show()})}function sc_clearTimers(a){return a.auto&&clearTimeout(a.auto),a.progress&&clearInterval(a.progress),a}function sc_mapCallbackArguments(a,b,c,d,e,f,g){return{width:g.width,height:g.height,items:{old:a,skipped:b,visible:c},scroll:{items:d,direction:e,duration:f}}}function sc_getDuration(a,b,c,d){var e=a.duration;return"none"==a.fx?0:("auto"==e?e=b.scroll.duration/b.scroll.items*c:10>e&&(e=d/e),1>e?0:("fade"==a.fx&&(e/=2),Math.round(e)))}function nv_showNavi(a,b,c){var d=is_number(a.items.minimum)?a.items.minimum:a.items.visible+1;if("show"==b||"hide"==b)var e=b;else if(d>b){debug(c,"Not enough items ("+b+" total, "+d+" needed): Hiding navigation.");var e="hide"}else var e="show";var f="show"==e?"removeClass":"addClass",g=cf_c("hidden",c);a.auto.button&&a.auto.button[e]()[f](g),a.prev.button&&a.prev.button[e]()[f](g),a.next.button&&a.next.button[e]()[f](g),a.pagination.container&&a.pagination.container[e]()[f](g)}function nv_enableNavi(a,b,c){if(!a.circular&&!a.infinite){var d="removeClass"==b||"addClass"==b?b:!1,e=cf_c("disabled",c);if(a.auto.button&&d&&a.auto.button[d](e),a.prev.button){var f=d||0==b?"addClass":"removeClass";a.prev.button[f](e)}if(a.next.button){var f=d||b==a.items.visible?"addClass":"removeClass";a.next.button[f](e)}}}function go_getObject(a,b){return is_function(b)?b=b.call(a):is_undefined(b)&&(b={}),b}function go_getItemsObject(a,b){return b=go_getObject(a,b),is_number(b)?b={visible:b}:"variable"==b?b={visible:b,width:b,height:b}:is_object(b)||(b={}),b}function go_getScrollObject(a,b){return b=go_getObject(a,b),is_number(b)?b=50>=b?{items:b}:{duration:b}:is_string(b)?b={easing:b}:is_object(b)||(b={}),b}function go_getNaviObject(a,b){if(b=go_getObject(a,b),is_string(b)){var c=cf_getKeyCode(b);b=-1==c?$(b):c}return b}function go_getAutoObject(a,b){return b=go_getNaviObject(a,b),is_jquery(b)?b={button:b}:is_boolean(b)?b={play:b}:is_number(b)&&(b={timeoutDuration:b}),b.progress&&(is_string(b.progress)||is_jquery(b.progress))&&(b.progress={bar:b.progress}),b}function go_complementAutoObject(a,b){return is_function(b.button)&&(b.button=b.button.call(a)),is_string(b.button)&&(b.button=$(b.button)),is_boolean(b.play)||(b.play=!0),is_number(b.delay)||(b.delay=0),is_undefined(b.pauseOnEvent)&&(b.pauseOnEvent=!0),is_boolean(b.pauseOnResize)||(b.pauseOnResize=!0),is_number(b.timeoutDuration)||(b.timeoutDuration=10>b.duration?2500:5*b.duration),b.progress&&(is_function(b.progress.bar)&&(b.progress.bar=b.progress.bar.call(a)),is_string(b.progress.bar)&&(b.progress.bar=$(b.progress.bar)),b.progress.bar?(is_function(b.progress.updater)||(b.progress.updater=$.fn.carouFredSel.progressbarUpdater),is_number(b.progress.interval)||(b.progress.interval=50)):b.progress=!1),b}function go_getPrevNextObject(a,b){return b=go_getNaviObject(a,b),is_jquery(b)?b={button:b}:is_number(b)&&(b={key:b}),b}function go_complementPrevNextObject(a,b){return is_function(b.button)&&(b.button=b.button.call(a)),is_string(b.button)&&(b.button=$(b.button)),is_string(b.key)&&(b.key=cf_getKeyCode(b.key)),b}function go_getPaginationObject(a,b){return b=go_getNaviObject(a,b),is_jquery(b)?b={container:b}:is_boolean(b)&&(b={keys:b}),b}function go_complementPaginationObject(a,b){return is_function(b.container)&&(b.container=b.container.call(a)),is_string(b.container)&&(b.container=$(b.container)),is_number(b.items)||(b.items=!1),is_boolean(b.keys)||(b.keys=!1),is_function(b.anchorBuilder)||is_false(b.anchorBuilder)||(b.anchorBuilder=$.fn.carouFredSel.pageAnchorBuilder),is_number(b.deviation)||(b.deviation=0),b}function go_getSwipeObject(a,b){return is_function(b)&&(b=b.call(a)),is_undefined(b)&&(b={onTouch:!1}),is_true(b)?b={onTouch:b}:is_number(b)&&(b={items:b}),b}function go_complementSwipeObject(a,b){return is_boolean(b.onTouch)||(b.onTouch=!0),is_boolean(b.onMouse)||(b.onMouse=!1),is_object(b.options)||(b.options={}),is_boolean(b.options.triggerOnTouchEnd)||(b.options.triggerOnTouchEnd=!1),b}function go_getMousewheelObject(a,b){return is_function(b)&&(b=b.call(a)),is_true(b)?b={}:is_number(b)?b={items:b}:is_undefined(b)&&(b=!1),b}function go_complementMousewheelObject(a,b){return b}function gn_getItemIndex(a,b,c,d,e){if(is_string(a)&&(a=$(a,e)),is_object(a)&&(a=$(a,e)),is_jquery(a)?(a=e.children().index(a),is_boolean(c)||(c=!1)):is_boolean(c)||(c=!0),is_number(a)||(a=0),is_number(b)||(b=0),c&&(a+=d.first),a+=b,d.total>0){for(;a>=d.total;)a-=d.total;for(;0>a;)a+=d.total}return a}function gn_getVisibleItemsPrev(a,b,c){for(var d=0,e=0,f=c;f>=0;f--){var g=a.eq(f);if(d+=g.is(":visible")?g[b.d.outerWidth](!0):0,d>b.maxDimension)return e;0==f&&(f=a.length),e++}}function gn_getVisibleItemsPrevFilter(a,b,c){return gn_getItemsPrevFilter(a,b.items.filter,b.items.visibleConf.org,c)}function gn_getScrollItemsPrevFilter(a,b,c,d){return gn_getItemsPrevFilter(a,b.items.filter,d,c)}function gn_getItemsPrevFilter(a,b,c,d){for(var e=0,f=0,g=d,h=a.length;g>=0;g--){if(f++,f==h)return f;var i=a.eq(g);if(i.is(b)&&(e++,e==c))return f;0==g&&(g=h)}}function gn_getVisibleOrg(a,b){return b.items.visibleConf.org||a.children().slice(0,b.items.visible).filter(b.items.filter).length}function gn_getVisibleItemsNext(a,b,c){for(var d=0,e=0,f=c,g=a.length-1;g>=f;f++){var h=a.eq(f);if(d+=h.is(":visible")?h[b.d.outerWidth](!0):0,d>b.maxDimension)return e;if(e++,e==g+1)return e;f==g&&(f=-1)}}function gn_getVisibleItemsNextTestCircular(a,b,c,d){var e=gn_getVisibleItemsNext(a,b,c);return b.circular||c+e>d&&(e=d-c),e}function gn_getVisibleItemsNextFilter(a,b,c){return gn_getItemsNextFilter(a,b.items.filter,b.items.visibleConf.org,c,b.circular)}function gn_getScrollItemsNextFilter(a,b,c,d){return gn_getItemsNextFilter(a,b.items.filter,d+1,c,b.circular)-1}function gn_getItemsNextFilter(a,b,c,d){for(var f=0,g=0,h=d,i=a.length-1;i>=h;h++){if(g++,g>=i)return g;var j=a.eq(h);if(j.is(b)&&(f++,f==c))return g;h==i&&(h=-1)}}function gi_getCurrentItems(a,b){return a.slice(0,b.items.visible)}function gi_getOldItemsPrev(a,b,c){return a.slice(c,b.items.visibleConf.old+c)}function gi_getNewItemsPrev(a,b){return a.slice(0,b.items.visible)}function gi_getOldItemsNext(a,b){return a.slice(0,b.items.visibleConf.old)}function gi_getNewItemsNext(a,b,c){return a.slice(c,b.items.visible+c)}function sz_storeMargin(a,b,c){b.usePadding&&(is_string(c)||(c="_cfs_origCssMargin"),a.each(function(){var a=$(this),d=parseInt(a.css(b.d.marginRight),10);is_number(d)||(d=0),a.data(c,d)}))}function sz_resetMargin(a,b,c){if(b.usePadding){var d=is_boolean(c)?c:!1;is_number(c)||(c=0),sz_storeMargin(a,b,"_cfs_tempCssMargin"),a.each(function(){var a=$(this);a.css(b.d.marginRight,d?a.data("_cfs_tempCssMargin"):c+a.data("_cfs_origCssMargin"))})}}function sz_storeOrigCss(a){a.each(function(){var a=$(this);a.data("_cfs_origCss",a.attr("style")||"")})}function sz_restoreOrigCss(a){a.each(function(){var a=$(this);a.attr("style",a.data("_cfs_origCss")||"")})}function sz_setResponsiveSizes(a,b){var d=(a.items.visible,a.items[a.d.width]),e=a[a.d.height],f=is_percentage(e);b.each(function(){var b=$(this),c=d-ms_getPaddingBorderMargin(b,a,"Width");b[a.d.width](c),f&&b[a.d.height](ms_getPercentage(c,e))})}function sz_setSizes(a,b){var c=a.parent(),d=a.children(),e=gi_getCurrentItems(d,b),f=cf_mapWrapperSizes(ms_getSizes(e,b,!0),b,!1);if(c.css(f),b.usePadding){var g=b.padding,h=g[b.d[1]];b.align&&0>h&&(h=0);var i=e.last();i.css(b.d.marginRight,i.data("_cfs_origCssMargin")+h),a.css(b.d.top,g[b.d[0]]),a.css(b.d.left,g[b.d[3]])}return a.css(b.d.width,f[b.d.width]+2*ms_getTotalSize(d,b,"width")),a.css(b.d.height,ms_getLargestSize(d,b,"height")),f}function ms_getSizes(a,b,c){return[ms_getTotalSize(a,b,"width",c),ms_getLargestSize(a,b,"height",c)]}function ms_getLargestSize(a,b,c,d){return is_boolean(d)||(d=!1),is_number(b[b.d[c]])&&d?b[b.d[c]]:is_number(b.items[b.d[c]])?b.items[b.d[c]]:(c=c.toLowerCase().indexOf("width")>-1?"outerWidth":"outerHeight",ms_getTrueLargestSize(a,b,c))}function ms_getTrueLargestSize(a,b,c){for(var d=0,e=0,f=a.length;f>e;e++){var g=a.eq(e),h=g.is(":visible")?g[b.d[c]](!0):0;h>d&&(d=h)}return d}function ms_getTotalSize(a,b,c,d){if(is_boolean(d)||(d=!1),is_number(b[b.d[c]])&&d)return b[b.d[c]];if(is_number(b.items[b.d[c]]))return b.items[b.d[c]]*a.length;for(var e=c.toLowerCase().indexOf("width")>-1?"outerWidth":"outerHeight",f=0,g=0,h=a.length;h>g;g++){var i=a.eq(g);f+=i.is(":visible")?i[b.d[e]](!0):0}return f}function ms_getParentSize(a,b,c){var d=a.is(":visible");d&&a.hide();var e=a.parent()[b.d[c]]();return d&&a.show(),e}function ms_getMaxDimension(a,b){return is_number(a[a.d.width])?a[a.d.width]:b}function ms_hasVariableSizes(a,b,c){for(var d=!1,e=!1,f=0,g=a.length;g>f;f++){var h=a.eq(f),i=h.is(":visible")?h[b.d[c]](!0):0;d===!1?d=i:d!=i&&(e=!0),0==d&&(e=!0)}return e}function ms_getPaddingBorderMargin(a,b,c){return a[b.d["outer"+c]](!0)-a[b.d[c.toLowerCase()]]()}function ms_getPercentage(a,b){if(is_percentage(b)){if(b=parseInt(b.slice(0,-1),10),!is_number(b))return a;a*=b/100}return a}function cf_e(a,b,c,d,e){return is_boolean(c)||(c=!0),is_boolean(d)||(d=!0),is_boolean(e)||(e=!1),c&&(a=b.events.prefix+a),d&&(a=a+"."+b.events.namespace),d&&e&&(a+=b.serialNumber),a}function cf_c(a,b){return is_string(b.classnames[a])?b.classnames[a]:a}function cf_mapWrapperSizes(a,b,c){is_boolean(c)||(c=!0);var d=b.usePadding&&c?b.padding:[0,0,0,0],e={};return e[b.d.width]=a[0]+d[1]+d[3],e[b.d.height]=a[1]+d[0]+d[2],e}function cf_sortParams(a,b){for(var c=[],d=0,e=a.length;e>d;d++)for(var f=0,g=b.length;g>f;f++)if(b[f].indexOf(typeof a[d])>-1&&is_undefined(c[f])){c[f]=a[d];break}return c}function cf_getPadding(a){if(is_undefined(a))return[0,0,0,0];if(is_number(a))return[a,a,a,a];if(is_string(a)&&(a=a.split("px").join("").split("em").join("").split(" ")),!is_array(a))return[0,0,0,0];for(var b=0;4>b;b++)a[b]=parseInt(a[b],10);switch(a.length){case 0:return[0,0,0,0];case 1:return[a[0],a[0],a[0],a[0]];case 2:return[a[0],a[1],a[0],a[1]];case 3:return[a[0],a[1],a[2],a[1]];default:return[a[0],a[1],a[2],a[3]]}}function cf_getAlignPadding(a,b){var c=is_number(b[b.d.width])?Math.ceil(b[b.d.width]-ms_getTotalSize(a,b,"width")):0;switch(b.align){case"left":return[0,c];case"right":return[c,0];case"center":default:return[Math.ceil(c/2),Math.floor(c/2)]}}function cf_getDimensions(a){for(var b=[["width","innerWidth","outerWidth","height","innerHeight","outerHeight","left","top","marginRight",0,1,2,3],["height","innerHeight","outerHeight","width","innerWidth","outerWidth","top","left","marginBottom",3,2,1,0]],c=b[0].length,d="right"==a.direction||"left"==a.direction?0:1,e={},f=0;c>f;f++)e[b[0][f]]=b[d][f];return e}function cf_getAdjust(a,b,c,d){var e=a;if(is_function(c))e=c.call(d,e);else if(is_string(c)){var f=c.split("+"),g=c.split("-");if(g.length>f.length)var h=!0,i=g[0],j=g[1];else var h=!1,i=f[0],j=f[1];switch(i){case"even":e=1==a%2?a-1:a;break;case"odd":e=0==a%2?a-1:a;break;default:e=a}j=parseInt(j,10),is_number(j)&&(h&&(j=-j),e+=j)}return(!is_number(e)||1>e)&&(e=1),e}function cf_getItemsAdjust(a,b,c,d){return cf_getItemAdjustMinMax(cf_getAdjust(a,b,c,d),b.items.visibleConf)}function cf_getItemAdjustMinMax(a,b){return is_number(b.min)&&b.min>a&&(a=b.min),is_number(b.max)&&a>b.max&&(a=b.max),1>a&&(a=1),a}function cf_getSynchArr(a){is_array(a)||(a=[[a]]),is_array(a[0])||(a=[a]);for(var b=0,c=a.length;c>b;b++)is_string(a[b][0])&&(a[b][0]=$(a[b][0])),is_boolean(a[b][1])||(a[b][1]=!0),is_boolean(a[b][2])||(a[b][2]=!0),is_number(a[b][3])||(a[b][3]=0);return a}function cf_getKeyCode(a){return"right"==a?39:"left"==a?37:"up"==a?38:"down"==a?40:-1}function cf_setCookie(a,b,c){if(a){var d=b.triggerHandler(cf_e("currentPosition",c));$.fn.carouFredSel.cookie.set(a,d)}}function cf_getCookie(a){var b=$.fn.carouFredSel.cookie.get(a);return""==b?0:b}function in_mapCss(a,b){for(var c={},d=0,e=b.length;e>d;d++)c[b[d]]=a.css(b[d]);return c}function in_complementItems(a,b,c,d){return is_object(a.visibleConf)||(a.visibleConf={}),is_object(a.sizesConf)||(a.sizesConf={}),0==a.start&&is_number(d)&&(a.start=d),is_object(a.visible)?(a.visibleConf.min=a.visible.min,a.visibleConf.max=a.visible.max,a.visible=!1):is_string(a.visible)?("variable"==a.visible?a.visibleConf.variable=!0:a.visibleConf.adjust=a.visible,a.visible=!1):is_function(a.visible)&&(a.visibleConf.adjust=a.visible,a.visible=!1),is_string(a.filter)||(a.filter=c.filter(":hidden").length>0?":visible":"*"),a[b.d.width]||(b.responsive?(debug(!0,"Set a "+b.d.width+" for the items!"),a[b.d.width]=ms_getTrueLargestSize(c,b,"outerWidth")):a[b.d.width]=ms_hasVariableSizes(c,b,"outerWidth")?"variable":c[b.d.outerWidth](!0)),a[b.d.height]||(a[b.d.height]=ms_hasVariableSizes(c,b,"outerHeight")?"variable":c[b.d.outerHeight](!0)),a.sizesConf.width=a.width,a.sizesConf.height=a.height,a}function in_complementVisibleItems(a,b){return"variable"==a.items[a.d.width]&&(a.items.visibleConf.variable=!0),a.items.visibleConf.variable||(is_number(a[a.d.width])?a.items.visible=Math.floor(a[a.d.width]/a.items[a.d.width]):(a.items.visible=Math.floor(b/a.items[a.d.width]),a[a.d.width]=a.items.visible*a.items[a.d.width],a.items.visibleConf.adjust||(a.align=!1)),("Infinity"==a.items.visible||1>a.items.visible)&&(debug(!0,'Not a valid number of visible items: Set to "variable".'),a.items.visibleConf.variable=!0)),a}function in_complementPrimarySize(a,b,c){return"auto"==a&&(a=ms_getTrueLargestSize(c,b,"outerWidth")),a}function in_complementSecondarySize(a,b,c){return"auto"==a&&(a=ms_getTrueLargestSize(c,b,"outerHeight")),a||(a=b.items[b.d.height]),a}function in_getAlignPadding(a,b){var c=cf_getAlignPadding(gi_getCurrentItems(b,a),a);return a.padding[a.d[1]]=c[1],a.padding[a.d[3]]=c[0],a}function in_getResponsiveValues(a,b){var d=cf_getItemAdjustMinMax(Math.ceil(a[a.d.width]/a.items[a.d.width]),a.items.visibleConf);d>b.length&&(d=b.length);var e=Math.floor(a[a.d.width]/d);return a.items.visible=d,a.items[a.d.width]=e,a[a.d.width]=d*e,a}function bt_pauseOnHoverConfig(a){if(is_string(a))var b=a.indexOf("immediate")>-1?!0:!1,c=a.indexOf("resume")>-1?!0:!1;else var b=c=!1;return[b,c]}function bt_mousesheelNumber(a){return is_number(a)?a:null}function is_null(a){return null===a}function is_undefined(a){return is_null(a)||a===void 0||""===a||"undefined"===a}function is_array(a){return a instanceof Array}function is_jquery(a){return a instanceof jQuery}function is_object(a){return(a instanceof Object||"object"==typeof a)&&!is_null(a)&&!is_jquery(a)&&!is_array(a)&&!is_function(a)}function is_number(a){return(a instanceof Number||"number"==typeof a)&&!isNaN(a)}function is_string(a){return(a instanceof String||"string"==typeof a)&&!is_undefined(a)&&!is_true(a)&&!is_false(a)}function is_function(a){return a instanceof Function||"function"==typeof a}function is_boolean(a){return a instanceof Boolean||"boolean"==typeof a||is_true(a)||is_false(a)}function is_true(a){return a===!0||"true"===a}function is_false(a){return a===!1||"false"===a}function is_percentage(a){return is_string(a)&&"%"==a.slice(-1)}function getTime(){return(new Date).getTime()}function deprecated(a,b){debug(!0,a+" is DEPRECATED, support for it will be removed. Use "+b+" instead.")}function debug(a,b){if(!is_undefined(window.console)&&!is_undefined(window.console.log)){if(is_object(a)){var c=" ("+a.selector+")";a=a.debug}else var c="";if(!a)return!1;b=is_string(b)?"carouFredSel"+c+": "+b:["carouFredSel"+c+":",b],window.console.log(b)}return!1}$.fn.carouFredSel||($.fn.caroufredsel=$.fn.carouFredSel=function(options,configs){if(0==this.length)return debug(!0,'No element found for "'+this.selector+'".'),this;if(this.length>1)return this.each(function(){$(this).carouFredSel(options,configs)});var $cfs=this,$tt0=this[0],starting_position=!1;$cfs.data("_cfs_isCarousel")&&(starting_position=$cfs.triggerHandler("_cfs_triggerEvent","currentPosition"),$cfs.trigger("_cfs_triggerEvent",["destroy",!0]));var FN={};FN._init=function(a,b,c){a=go_getObject($tt0,a),a.items=go_getItemsObject($tt0,a.items),a.scroll=go_getScrollObject($tt0,a.scroll),a.auto=go_getAutoObject($tt0,a.auto),a.prev=go_getPrevNextObject($tt0,a.prev),a.next=go_getPrevNextObject($tt0,a.next),a.pagination=go_getPaginationObject($tt0,a.pagination),a.swipe=go_getSwipeObject($tt0,a.swipe),a.mousewheel=go_getMousewheelObject($tt0,a.mousewheel),b&&(opts_orig=$.extend(!0,{},$.fn.carouFredSel.defaults,a)),opts=$.extend(!0,{},$.fn.carouFredSel.defaults,a),opts.d=cf_getDimensions(opts),crsl.direction="up"==opts.direction||"left"==opts.direction?"next":"prev";var d=$cfs.children(),e=ms_getParentSize($wrp,opts,"width");if(is_true(opts.cookie)&&(opts.cookie="caroufredsel_cookie_"+conf.serialNumber),opts.maxDimension=ms_getMaxDimension(opts,e),opts.items=in_complementItems(opts.items,opts,d,c),opts[opts.d.width]=in_complementPrimarySize(opts[opts.d.width],opts,d),opts[opts.d.height]=in_complementSecondarySize(opts[opts.d.height],opts,d),opts.responsive&&(is_percentage(opts[opts.d.width])||(opts[opts.d.width]="100%")),is_percentage(opts[opts.d.width])&&(crsl.upDateOnWindowResize=!0,crsl.primarySizePercentage=opts[opts.d.width],opts[opts.d.width]=ms_getPercentage(e,crsl.primarySizePercentage),opts.items.visible||(opts.items.visibleConf.variable=!0)),opts.responsive?(opts.usePadding=!1,opts.padding=[0,0,0,0],opts.align=!1,opts.items.visibleConf.variable=!1):(opts.items.visible||(opts=in_complementVisibleItems(opts,e)),opts[opts.d.width]||(!opts.items.visibleConf.variable&&is_number(opts.items[opts.d.width])&&"*"==opts.items.filter?(opts[opts.d.width]=opts.items.visible*opts.items[opts.d.width],opts.align=!1):opts[opts.d.width]="variable"),is_undefined(opts.align)&&(opts.align=is_number(opts[opts.d.width])?"center":!1),opts.items.visibleConf.variable&&(opts.items.visible=gn_getVisibleItemsNext(d,opts,0))),"*"==opts.items.filter||opts.items.visibleConf.variable||(opts.items.visibleConf.org=opts.items.visible,opts.items.visible=gn_getVisibleItemsNextFilter(d,opts,0)),opts.items.visible=cf_getItemsAdjust(opts.items.visible,opts,opts.items.visibleConf.adjust,$tt0),opts.items.visibleConf.old=opts.items.visible,opts.responsive)opts.items.visibleConf.min||(opts.items.visibleConf.min=opts.items.visible),opts.items.visibleConf.max||(opts.items.visibleConf.max=opts.items.visible),opts=in_getResponsiveValues(opts,d,e);else switch(opts.padding=cf_getPadding(opts.padding),"top"==opts.align?opts.align="left":"bottom"==opts.align&&(opts.align="right"),opts.align){case"center":case"left":case"right":"variable"!=opts[opts.d.width]&&(opts=in_getAlignPadding(opts,d),opts.usePadding=!0);break;default:opts.align=!1,opts.usePadding=0==opts.padding[0]&&0==opts.padding[1]&&0==opts.padding[2]&&0==opts.padding[3]?!1:!0}is_number(opts.scroll.duration)||(opts.scroll.duration=500),is_undefined(opts.scroll.items)&&(opts.scroll.items=opts.responsive||opts.items.visibleConf.variable||"*"!=opts.items.filter?"visible":opts.items.visible),opts.auto=$.extend(!0,{},opts.scroll,opts.auto),opts.prev=$.extend(!0,{},opts.scroll,opts.prev),opts.next=$.extend(!0,{},opts.scroll,opts.next),opts.pagination=$.extend(!0,{},opts.scroll,opts.pagination),opts.auto=go_complementAutoObject($tt0,opts.auto),opts.prev=go_complementPrevNextObject($tt0,opts.prev),opts.next=go_complementPrevNextObject($tt0,opts.next),opts.pagination=go_complementPaginationObject($tt0,opts.pagination),opts.swipe=go_complementSwipeObject($tt0,opts.swipe),opts.mousewheel=go_complementMousewheelObject($tt0,opts.mousewheel),opts.synchronise&&(opts.synchronise=cf_getSynchArr(opts.synchronise)),opts.auto.onPauseStart&&(opts.auto.onTimeoutStart=opts.auto.onPauseStart,deprecated("auto.onPauseStart","auto.onTimeoutStart")),opts.auto.onPausePause&&(opts.auto.onTimeoutPause=opts.auto.onPausePause,deprecated("auto.onPausePause","auto.onTimeoutPause")),opts.auto.onPauseEnd&&(opts.auto.onTimeoutEnd=opts.auto.onPauseEnd,deprecated("auto.onPauseEnd","auto.onTimeoutEnd")),opts.auto.pauseDuration&&(opts.auto.timeoutDuration=opts.auto.pauseDuration,deprecated("auto.pauseDuration","auto.timeoutDuration"))},FN._build=function(){$cfs.data("_cfs_isCarousel",!0);var a=$cfs.children(),b=in_mapCss($cfs,["textAlign","float","position","top","right","bottom","left","zIndex","width","height","marginTop","marginRight","marginBottom","marginLeft"]),c="relative";switch(b.position){case"absolute":case"fixed":c=b.position}"parent"==conf.wrapper?sz_storeOrigCss($wrp):$wrp.css(b),$wrp.css({overflow:"hidden",position:c}),sz_storeOrigCss($cfs),$cfs.data("_cfs_origCssZindex",b.zIndex),$cfs.css({textAlign:"left","float":"none",position:"absolute",top:0,right:"auto",bottom:"auto",left:0,marginTop:0,marginRight:0,marginBottom:0,marginLeft:0}),sz_storeMargin(a,opts),sz_storeOrigCss(a),opts.responsive&&sz_setResponsiveSizes(opts,a)},FN._bind_events=function(){FN._unbind_events(),$cfs.bind(cf_e("stop",conf),function(a,b){return a.stopPropagation(),crsl.isStopped||opts.auto.button&&opts.auto.button.addClass(cf_c("stopped",conf)),crsl.isStopped=!0,opts.auto.play&&(opts.auto.play=!1,$cfs.trigger(cf_e("pause",conf),b)),!0}),$cfs.bind(cf_e("finish",conf),function(a){return a.stopPropagation(),crsl.isScrolling&&sc_stopScroll(scrl),!0}),$cfs.bind(cf_e("pause",conf),function(a,b,c){if(a.stopPropagation(),tmrs=sc_clearTimers(tmrs),b&&crsl.isScrolling){scrl.isStopped=!0;var d=getTime()-scrl.startTime;scrl.duration-=d,scrl.pre&&(scrl.pre.duration-=d),scrl.post&&(scrl.post.duration-=d),sc_stopScroll(scrl,!1)}if(crsl.isPaused||crsl.isScrolling||c&&(tmrs.timePassed+=getTime()-tmrs.startTime),crsl.isPaused||opts.auto.button&&opts.auto.button.addClass(cf_c("paused",conf)),crsl.isPaused=!0,opts.auto.onTimeoutPause){var e=opts.auto.timeoutDuration-tmrs.timePassed,f=100-Math.ceil(100*e/opts.auto.timeoutDuration);opts.auto.onTimeoutPause.call($tt0,f,e)}return!0}),$cfs.bind(cf_e("play",conf),function(a,b,c,d){a.stopPropagation(),tmrs=sc_clearTimers(tmrs);var e=[b,c,d],f=["string","number","boolean"],g=cf_sortParams(e,f);if(b=g[0],c=g[1],d=g[2],"prev"!=b&&"next"!=b&&(b=crsl.direction),is_number(c)||(c=0),is_boolean(d)||(d=!1),d&&(crsl.isStopped=!1,opts.auto.play=!0),!opts.auto.play)return a.stopImmediatePropagation(),debug(conf,"Carousel stopped: Not scrolling.");crsl.isPaused&&opts.auto.button&&(opts.auto.button.removeClass(cf_c("stopped",conf)),opts.auto.button.removeClass(cf_c("paused",conf))),crsl.isPaused=!1,tmrs.startTime=getTime();var h=opts.auto.timeoutDuration+c;return dur2=h-tmrs.timePassed,perc=100-Math.ceil(100*dur2/h),opts.auto.progress&&(tmrs.progress=setInterval(function(){var a=getTime()-tmrs.startTime+tmrs.timePassed,b=Math.ceil(100*a/h);opts.auto.progress.updater.call(opts.auto.progress.bar[0],b)},opts.auto.progress.interval)),tmrs.auto=setTimeout(function(){opts.auto.progress&&opts.auto.progress.updater.call(opts.auto.progress.bar[0],100),opts.auto.onTimeoutEnd&&opts.auto.onTimeoutEnd.call($tt0,perc,dur2),crsl.isScrolling?$cfs.trigger(cf_e("play",conf),b):$cfs.trigger(cf_e(b,conf),opts.auto)},dur2),opts.auto.onTimeoutStart&&opts.auto.onTimeoutStart.call($tt0,perc,dur2),!0}),$cfs.bind(cf_e("resume",conf),function(a){return a.stopPropagation(),scrl.isStopped?(scrl.isStopped=!1,crsl.isPaused=!1,crsl.isScrolling=!0,scrl.startTime=getTime(),sc_startScroll(scrl,conf)):$cfs.trigger(cf_e("play",conf)),!0}),$cfs.bind(cf_e("prev",conf)+" "+cf_e("next",conf),function(a,b,c,d,e){if(a.stopPropagation(),crsl.isStopped||$cfs.is(":hidden"))return a.stopImmediatePropagation(),debug(conf,"Carousel stopped or hidden: Not scrolling.");var f=is_number(opts.items.minimum)?opts.items.minimum:opts.items.visible+1;if(f>itms.total)return a.stopImmediatePropagation(),debug(conf,"Not enough items ("+itms.total+" total, "+f+" needed): Not scrolling.");var g=[b,c,d,e],h=["object","number/string","function","boolean"],i=cf_sortParams(g,h);b=i[0],c=i[1],d=i[2],e=i[3];var j=a.type.slice(conf.events.prefix.length);if(is_object(b)||(b={}),is_function(d)&&(b.onAfter=d),is_boolean(e)&&(b.queue=e),b=$.extend(!0,{},opts[j],b),b.conditions&&!b.conditions.call($tt0,j))return a.stopImmediatePropagation(),debug(conf,'Callback "conditions" returned false.');if(!is_number(c)){if("*"!=opts.items.filter)c="visible";else for(var k=[c,b.items,opts[j].items],i=0,l=k.length;l>i;i++)if(is_number(k[i])||"page"==k[i]||"visible"==k[i]){c=k[i];break}switch(c){case"page":return a.stopImmediatePropagation(),$cfs.triggerHandler(cf_e(j+"Page",conf),[b,d]);case"visible":opts.items.visibleConf.variable||"*"!=opts.items.filter||(c=opts.items.visible)}}if(scrl.isStopped)return $cfs.trigger(cf_e("resume",conf)),$cfs.trigger(cf_e("queue",conf),[j,[b,c,d]]),a.stopImmediatePropagation(),debug(conf,"Carousel resumed scrolling.");if(b.duration>0&&crsl.isScrolling)return b.queue&&("last"==b.queue&&(queu=[]),("first"!=b.queue||0==queu.length)&&$cfs.trigger(cf_e("queue",conf),[j,[b,c,d]])),a.stopImmediatePropagation(),debug(conf,"Carousel currently scrolling.");if(tmrs.timePassed=0,$cfs.trigger(cf_e("slide_"+j,conf),[b,c]),opts.synchronise)for(var m=opts.synchronise,n=[b,c],o=0,l=m.length;l>o;o++){var p=j;m[o][2]||(p="prev"==p?"next":"prev"),m[o][1]||(n[0]=m[o][0].triggerHandler("_cfs_triggerEvent",["configuration",p])),n[1]=c+m[o][3],m[o][0].trigger("_cfs_triggerEvent",["slide_"+p,n])}return!0}),$cfs.bind(cf_e("slide_prev",conf),function(a,b,c){a.stopPropagation();var d=$cfs.children();if(!opts.circular&&0==itms.first)return opts.infinite&&$cfs.trigger(cf_e("next",conf),itms.total-1),a.stopImmediatePropagation();if(sz_resetMargin(d,opts),!is_number(c)){if(opts.items.visibleConf.variable)c=gn_getVisibleItemsPrev(d,opts,itms.total-1);else if("*"!=opts.items.filter){var e=is_number(b.items)?b.items:gn_getVisibleOrg($cfs,opts);c=gn_getScrollItemsPrevFilter(d,opts,itms.total-1,e)}else c=opts.items.visible;c=cf_getAdjust(c,opts,b.items,$tt0)}if(opts.circular||itms.total-c<itms.first&&(c=itms.total-itms.first),opts.items.visibleConf.old=opts.items.visible,opts.items.visibleConf.variable){var f=cf_getItemsAdjust(gn_getVisibleItemsNext(d,opts,itms.total-c),opts,opts.items.visibleConf.adjust,$tt0);f>=opts.items.visible+c&&itms.total>c&&(c++,f=cf_getItemsAdjust(gn_getVisibleItemsNext(d,opts,itms.total-c),opts,opts.items.visibleConf.adjust,$tt0)),opts.items.visible=f}else if("*"!=opts.items.filter){var f=gn_getVisibleItemsNextFilter(d,opts,itms.total-c);opts.items.visible=cf_getItemsAdjust(f,opts,opts.items.visibleConf.adjust,$tt0)}if(sz_resetMargin(d,opts,!0),0==c)return a.stopImmediatePropagation(),debug(conf,"0 items to scroll: Not scrolling.");for(debug(conf,"Scrolling "+c+" items backward."),itms.first+=c;itms.first>=itms.total;)itms.first-=itms.total;opts.circular||(0==itms.first&&b.onEnd&&b.onEnd.call($tt0,"prev"),opts.infinite||nv_enableNavi(opts,itms.first,conf)),$cfs.children().slice(itms.total-c,itms.total).prependTo($cfs),itms.total<opts.items.visible+c&&$cfs.children().slice(0,opts.items.visible+c-itms.total).clone(!0).appendTo($cfs);var d=$cfs.children(),g=gi_getOldItemsPrev(d,opts,c),h=gi_getNewItemsPrev(d,opts),i=d.eq(c-1),j=g.last(),k=h.last();sz_resetMargin(d,opts);var l=0,m=0;if(opts.align){var n=cf_getAlignPadding(h,opts);l=n[0],m=n[1]}var o=0>l?opts.padding[opts.d[3]]:0,p=!1,q=$();if(c>opts.items.visible&&(q=d.slice(opts.items.visibleConf.old,c),"directscroll"==b.fx)){var r=opts.items[opts.d.width];p=q,i=k,sc_hideHiddenItems(p),opts.items[opts.d.width]="variable"}var s=!1,t=ms_getTotalSize(d.slice(0,c),opts,"width"),u=cf_mapWrapperSizes(ms_getSizes(h,opts,!0),opts,!opts.usePadding),v=0,w={},x={},y={},z={},A={},B={},C={},D=sc_getDuration(b,opts,c,t);switch(b.fx){case"cover":case"cover-fade":v=ms_getTotalSize(d.slice(0,opts.items.visible),opts,"width")}p&&(opts.items[opts.d.width]=r),sz_resetMargin(d,opts,!0),m>=0&&sz_resetMargin(j,opts,opts.padding[opts.d[1]]),l>=0&&sz_resetMargin(i,opts,opts.padding[opts.d[3]]),opts.align&&(opts.padding[opts.d[1]]=m,opts.padding[opts.d[3]]=l),B[opts.d.left]=-(t-o),C[opts.d.left]=-(v-o),x[opts.d.left]=u[opts.d.width];var E=function(){},F=function(){},G=function(){},H=function(){},I=function(){},J=function(){},K=function(){},L=function(){},M=function(){},N=function(){},O=function(){};switch(b.fx){case"crossfade":case"cover":case"cover-fade":case"uncover":case"uncover-fade":s=$cfs.clone(!0).appendTo($wrp)}switch(b.fx){case"crossfade":case"uncover":case"uncover-fade":s.children().slice(0,c).remove(),s.children().slice(opts.items.visibleConf.old).remove();break;case"cover":case"cover-fade":s.children().slice(opts.items.visible).remove(),s.css(C)}if($cfs.css(B),scrl=sc_setScroll(D,b.easing,conf),w[opts.d.left]=opts.usePadding?opts.padding[opts.d[3]]:0,("variable"==opts[opts.d.width]||"variable"==opts[opts.d.height])&&(E=function(){$wrp.css(u)},F=function(){scrl.anims.push([$wrp,u])}),opts.usePadding){switch(k.not(i).length&&(y[opts.d.marginRight]=i.data("_cfs_origCssMargin"),0>l?i.css(y):(K=function(){i.css(y)},L=function(){scrl.anims.push([i,y])})),b.fx){case"cover":case"cover-fade":s.children().eq(c-1).css(y)}k.not(j).length&&(z[opts.d.marginRight]=j.data("_cfs_origCssMargin"),G=function(){j.css(z)},H=function(){scrl.anims.push([j,z])}),m>=0&&(A[opts.d.marginRight]=k.data("_cfs_origCssMargin")+opts.padding[opts.d[1]],I=function(){k.css(A)},J=function(){scrl.anims.push([k,A])})}O=function(){$cfs.css(w)};var P=opts.items.visible+c-itms.total;N=function(){if(P>0&&($cfs.children().slice(itms.total).remove(),g=$($cfs.children().slice(itms.total-(opts.items.visible-P)).get().concat($cfs.children().slice(0,P).get()))),sc_showHiddenItems(p),opts.usePadding){var a=$cfs.children().eq(opts.items.visible+c-1);a.css(opts.d.marginRight,a.data("_cfs_origCssMargin"))}};var Q=sc_mapCallbackArguments(g,q,h,c,"prev",D,u);switch(M=function(){sc_afterScroll($cfs,s,b),crsl.isScrolling=!1,clbk.onAfter=sc_fireCallbacks($tt0,b,"onAfter",Q,clbk),queu=sc_fireQueue($cfs,queu,conf),crsl.isPaused||$cfs.trigger(cf_e("play",conf))},crsl.isScrolling=!0,tmrs=sc_clearTimers(tmrs),clbk.onBefore=sc_fireCallbacks($tt0,b,"onBefore",Q,clbk),b.fx){case"none":$cfs.css(w),E(),G(),I(),K(),O(),N(),M();break;case"fade":scrl.anims.push([$cfs,{opacity:0},function(){E(),G(),I(),K(),O(),N(),scrl=sc_setScroll(D,b.easing,conf),scrl.anims.push([$cfs,{opacity:1},M]),sc_startScroll(scrl,conf)}]);break;case"crossfade":$cfs.css({opacity:0}),scrl.anims.push([s,{opacity:0}]),scrl.anims.push([$cfs,{opacity:1},M]),F(),G(),I(),K(),O(),N();break;case"cover":scrl.anims.push([s,w,function(){G(),I(),K(),O(),N(),M()}]),F();break;case"cover-fade":scrl.anims.push([$cfs,{opacity:0}]),scrl.anims.push([s,w,function(){G(),I(),K(),O(),N(),M()}]),F();break;case"uncover":scrl.anims.push([s,x,M]),F(),G(),I(),K(),O(),N();break;case"uncover-fade":$cfs.css({opacity:0}),scrl.anims.push([$cfs,{opacity:1}]),scrl.anims.push([s,x,M]),F(),G(),I(),K(),O(),N();break;default:scrl.anims.push([$cfs,w,function(){N(),M()}]),F(),H(),J(),L()}return sc_startScroll(scrl,conf),cf_setCookie(opts.cookie,$cfs,conf),$cfs.trigger(cf_e("updatePageStatus",conf),[!1,u]),!0
}),$cfs.bind(cf_e("slide_next",conf),function(a,b,c){a.stopPropagation();var d=$cfs.children();if(!opts.circular&&itms.first==opts.items.visible)return opts.infinite&&$cfs.trigger(cf_e("prev",conf),itms.total-1),a.stopImmediatePropagation();if(sz_resetMargin(d,opts),!is_number(c)){if("*"!=opts.items.filter){var e=is_number(b.items)?b.items:gn_getVisibleOrg($cfs,opts);c=gn_getScrollItemsNextFilter(d,opts,0,e)}else c=opts.items.visible;c=cf_getAdjust(c,opts,b.items,$tt0)}var f=0==itms.first?itms.total:itms.first;if(!opts.circular){if(opts.items.visibleConf.variable)var g=gn_getVisibleItemsNext(d,opts,c),e=gn_getVisibleItemsPrev(d,opts,f-1);else var g=opts.items.visible,e=opts.items.visible;c+g>f&&(c=f-e)}if(opts.items.visibleConf.old=opts.items.visible,opts.items.visibleConf.variable){for(var g=cf_getItemsAdjust(gn_getVisibleItemsNextTestCircular(d,opts,c,f),opts,opts.items.visibleConf.adjust,$tt0);opts.items.visible-c>=g&&itms.total>c;)c++,g=cf_getItemsAdjust(gn_getVisibleItemsNextTestCircular(d,opts,c,f),opts,opts.items.visibleConf.adjust,$tt0);opts.items.visible=g}else if("*"!=opts.items.filter){var g=gn_getVisibleItemsNextFilter(d,opts,c);opts.items.visible=cf_getItemsAdjust(g,opts,opts.items.visibleConf.adjust,$tt0)}if(sz_resetMargin(d,opts,!0),0==c)return a.stopImmediatePropagation(),debug(conf,"0 items to scroll: Not scrolling.");for(debug(conf,"Scrolling "+c+" items forward."),itms.first-=c;0>itms.first;)itms.first+=itms.total;opts.circular||(itms.first==opts.items.visible&&b.onEnd&&b.onEnd.call($tt0,"next"),opts.infinite||nv_enableNavi(opts,itms.first,conf)),itms.total<opts.items.visible+c&&$cfs.children().slice(0,opts.items.visible+c-itms.total).clone(!0).appendTo($cfs);var d=$cfs.children(),h=gi_getOldItemsNext(d,opts),i=gi_getNewItemsNext(d,opts,c),j=d.eq(c-1),k=h.last(),l=i.last();sz_resetMargin(d,opts);var m=0,n=0;if(opts.align){var o=cf_getAlignPadding(i,opts);m=o[0],n=o[1]}var p=!1,q=$();if(c>opts.items.visibleConf.old&&(q=d.slice(opts.items.visibleConf.old,c),"directscroll"==b.fx)){var r=opts.items[opts.d.width];p=q,j=k,sc_hideHiddenItems(p),opts.items[opts.d.width]="variable"}var s=!1,t=ms_getTotalSize(d.slice(0,c),opts,"width"),u=cf_mapWrapperSizes(ms_getSizes(i,opts,!0),opts,!opts.usePadding),v=0,w={},x={},y={},z={},A={},B=sc_getDuration(b,opts,c,t);switch(b.fx){case"uncover":case"uncover-fade":v=ms_getTotalSize(d.slice(0,opts.items.visibleConf.old),opts,"width")}p&&(opts.items[opts.d.width]=r),opts.align&&0>opts.padding[opts.d[1]]&&(opts.padding[opts.d[1]]=0),sz_resetMargin(d,opts,!0),sz_resetMargin(k,opts,opts.padding[opts.d[1]]),opts.align&&(opts.padding[opts.d[1]]=n,opts.padding[opts.d[3]]=m),A[opts.d.left]=opts.usePadding?opts.padding[opts.d[3]]:0;var C=function(){},D=function(){},E=function(){},F=function(){},G=function(){},H=function(){},I=function(){},J=function(){},K=function(){};switch(b.fx){case"crossfade":case"cover":case"cover-fade":case"uncover":case"uncover-fade":s=$cfs.clone(!0).appendTo($wrp),s.children().slice(opts.items.visibleConf.old).remove()}switch(b.fx){case"crossfade":case"cover":case"cover-fade":$cfs.css("zIndex",1),s.css("zIndex",0)}if(scrl=sc_setScroll(B,b.easing,conf),w[opts.d.left]=-t,x[opts.d.left]=-v,0>m&&(w[opts.d.left]+=m),("variable"==opts[opts.d.width]||"variable"==opts[opts.d.height])&&(C=function(){$wrp.css(u)},D=function(){scrl.anims.push([$wrp,u])}),opts.usePadding){var L=l.data("_cfs_origCssMargin");n>=0&&(L+=opts.padding[opts.d[1]]),l.css(opts.d.marginRight,L),j.not(k).length&&(z[opts.d.marginRight]=k.data("_cfs_origCssMargin")),E=function(){k.css(z)},F=function(){scrl.anims.push([k,z])};var M=j.data("_cfs_origCssMargin");m>0&&(M+=opts.padding[opts.d[3]]),y[opts.d.marginRight]=M,G=function(){j.css(y)},H=function(){scrl.anims.push([j,y])}}K=function(){$cfs.css(A)};var N=opts.items.visible+c-itms.total;J=function(){N>0&&$cfs.children().slice(itms.total).remove();var a=$cfs.children().slice(0,c).appendTo($cfs).last();if(N>0&&(i=gi_getCurrentItems(d,opts)),sc_showHiddenItems(p),opts.usePadding){if(itms.total<opts.items.visible+c){var b=$cfs.children().eq(opts.items.visible-1);b.css(opts.d.marginRight,b.data("_cfs_origCssMargin")+opts.padding[opts.d[1]])}a.css(opts.d.marginRight,a.data("_cfs_origCssMargin"))}};var O=sc_mapCallbackArguments(h,q,i,c,"next",B,u);switch(I=function(){$cfs.css("zIndex",$cfs.data("_cfs_origCssZindex")),sc_afterScroll($cfs,s,b),crsl.isScrolling=!1,clbk.onAfter=sc_fireCallbacks($tt0,b,"onAfter",O,clbk),queu=sc_fireQueue($cfs,queu,conf),crsl.isPaused||$cfs.trigger(cf_e("play",conf))},crsl.isScrolling=!0,tmrs=sc_clearTimers(tmrs),clbk.onBefore=sc_fireCallbacks($tt0,b,"onBefore",O,clbk),b.fx){case"none":$cfs.css(w),C(),E(),G(),K(),J(),I();break;case"fade":scrl.anims.push([$cfs,{opacity:0},function(){C(),E(),G(),K(),J(),scrl=sc_setScroll(B,b.easing,conf),scrl.anims.push([$cfs,{opacity:1},I]),sc_startScroll(scrl,conf)}]);break;case"crossfade":$cfs.css({opacity:0}),scrl.anims.push([s,{opacity:0}]),scrl.anims.push([$cfs,{opacity:1},I]),D(),E(),G(),K(),J();break;case"cover":$cfs.css(opts.d.left,$wrp[opts.d.width]()),scrl.anims.push([$cfs,A,I]),D(),E(),G(),J();break;case"cover-fade":$cfs.css(opts.d.left,$wrp[opts.d.width]()),scrl.anims.push([s,{opacity:0}]),scrl.anims.push([$cfs,A,I]),D(),E(),G(),J();break;case"uncover":scrl.anims.push([s,x,I]),D(),E(),G(),K(),J();break;case"uncover-fade":$cfs.css({opacity:0}),scrl.anims.push([$cfs,{opacity:1}]),scrl.anims.push([s,x,I]),D(),E(),G(),K(),J();break;default:scrl.anims.push([$cfs,w,function(){K(),J(),I()}]),D(),F(),H()}return sc_startScroll(scrl,conf),cf_setCookie(opts.cookie,$cfs,conf),$cfs.trigger(cf_e("updatePageStatus",conf),[!1,u]),!0}),$cfs.bind(cf_e("slideTo",conf),function(a,b,c,d,e,f,g){a.stopPropagation();var h=[b,c,d,e,f,g],i=["string/number/object","number","boolean","object","string","function"],j=cf_sortParams(h,i);return e=j[3],f=j[4],g=j[5],b=gn_getItemIndex(j[0],j[1],j[2],itms,$cfs),0==b?!1:(is_object(e)||(e=!1),"prev"!=f&&"next"!=f&&(f=opts.circular?itms.total/2>=b?"next":"prev":0==itms.first||itms.first>b?"next":"prev"),"prev"==f&&(b=itms.total-b),$cfs.trigger(cf_e(f,conf),[e,b,g]),!0)}),$cfs.bind(cf_e("prevPage",conf),function(a,b,c){a.stopPropagation();var d=$cfs.triggerHandler(cf_e("currentPage",conf));return $cfs.triggerHandler(cf_e("slideToPage",conf),[d-1,b,"prev",c])}),$cfs.bind(cf_e("nextPage",conf),function(a,b,c){a.stopPropagation();var d=$cfs.triggerHandler(cf_e("currentPage",conf));return $cfs.triggerHandler(cf_e("slideToPage",conf),[d+1,b,"next",c])}),$cfs.bind(cf_e("slideToPage",conf),function(a,b,c,d,e){a.stopPropagation(),is_number(b)||(b=$cfs.triggerHandler(cf_e("currentPage",conf)));var f=opts.pagination.items||opts.items.visible,g=Math.ceil(itms.total/f)-1;return 0>b&&(b=g),b>g&&(b=0),$cfs.triggerHandler(cf_e("slideTo",conf),[b*f,0,!0,c,d,e])}),$cfs.bind(cf_e("jumpToStart",conf),function(a,b){if(a.stopPropagation(),b=b?gn_getItemIndex(b,0,!0,itms,$cfs):0,b+=itms.first,0!=b){if(itms.total>0)for(;b>itms.total;)b-=itms.total;$cfs.prepend($cfs.children().slice(b,itms.total))}return!0}),$cfs.bind(cf_e("synchronise",conf),function(a,b){if(a.stopPropagation(),b)b=cf_getSynchArr(b);else{if(!opts.synchronise)return debug(conf,"No carousel to synchronise.");b=opts.synchronise}for(var c=$cfs.triggerHandler(cf_e("currentPosition",conf)),d=!0,e=0,f=b.length;f>e;e++)b[e][0].triggerHandler(cf_e("slideTo",conf),[c,b[e][3],!0])||(d=!1);return d}),$cfs.bind(cf_e("queue",conf),function(a,b,c){return a.stopPropagation(),is_function(b)?b.call($tt0,queu):is_array(b)?queu=b:is_undefined(b)||queu.push([b,c]),queu}),$cfs.bind(cf_e("insertItem",conf),function(a,b,c,d,e){a.stopPropagation();var f=[b,c,d,e],g=["string/object","string/number/object","boolean","number"],h=cf_sortParams(f,g);if(b=h[0],c=h[1],d=h[2],e=h[3],is_object(b)&&!is_jquery(b)?b=$(b):is_string(b)&&(b=$(b)),!is_jquery(b)||0==b.length)return debug(conf,"Not a valid object.");is_undefined(c)&&(c="end"),sz_storeMargin(b,opts),sz_storeOrigCss(b);var i=c,j="before";"end"==c?d?(0==itms.first?(c=itms.total-1,j="after"):(c=itms.first,itms.first+=b.length),0>c&&(c=0)):(c=itms.total-1,j="after"):c=gn_getItemIndex(c,e,d,itms,$cfs);var k=$cfs.children().eq(c);return k.length?k[j](b):(debug(conf,"Correct insert-position not found! Appending item to the end."),$cfs.append(b)),"end"==i||d||itms.first>c&&(itms.first+=b.length),itms.total=$cfs.children().length,itms.first>=itms.total&&(itms.first-=itms.total),$cfs.trigger(cf_e("updateSizes",conf)),$cfs.trigger(cf_e("linkAnchors",conf)),!0}),$cfs.bind(cf_e("removeItem",conf),function(a,b,c,d){a.stopPropagation();var e=[b,c,d],f=["string/number/object","boolean","number"],g=cf_sortParams(e,f);if(b=g[0],c=g[1],d=g[2],b instanceof $&&b.length>1)return i=$(),b.each(function(){var e=$cfs.trigger(cf_e("removeItem",conf),[$(this),c,d]);e&&(i=i.add(e))}),i;if(is_undefined(b)||"end"==b)i=$cfs.children().last();else{b=gn_getItemIndex(b,d,c,itms,$cfs);var i=$cfs.children().eq(b);i.length&&itms.first>b&&(itms.first-=i.length)}return i&&i.length&&(i.detach(),itms.total=$cfs.children().length,$cfs.trigger(cf_e("updateSizes",conf))),i}),$cfs.bind(cf_e("onBefore",conf)+" "+cf_e("onAfter",conf),function(a,b){a.stopPropagation();var c=a.type.slice(conf.events.prefix.length);return is_array(b)&&(clbk[c]=b),is_function(b)&&clbk[c].push(b),clbk[c]}),$cfs.bind(cf_e("currentPosition",conf),function(a,b){if(a.stopPropagation(),0==itms.first)var c=0;else var c=itms.total-itms.first;return is_function(b)&&b.call($tt0,c),c}),$cfs.bind(cf_e("currentPage",conf),function(a,b){a.stopPropagation();var e,c=opts.pagination.items||opts.items.visible,d=Math.ceil(itms.total/c-1);return e=0==itms.first?0:itms.first<itms.total%c?0:itms.first!=c||opts.circular?Math.round((itms.total-itms.first)/c):d,0>e&&(e=0),e>d&&(e=d),is_function(b)&&b.call($tt0,e),e}),$cfs.bind(cf_e("currentVisible",conf),function(a,b){a.stopPropagation();var c=gi_getCurrentItems($cfs.children(),opts);return is_function(b)&&b.call($tt0,c),c}),$cfs.bind(cf_e("slice",conf),function(a,b,c,d){if(a.stopPropagation(),0==itms.total)return!1;var e=[b,c,d],f=["number","number","function"],g=cf_sortParams(e,f);if(b=is_number(g[0])?g[0]:0,c=is_number(g[1])?g[1]:itms.total,d=g[2],b+=itms.first,c+=itms.first,items.total>0){for(;b>itms.total;)b-=itms.total;for(;c>itms.total;)c-=itms.total;for(;0>b;)b+=itms.total;for(;0>c;)c+=itms.total}var i,h=$cfs.children();return i=c>b?h.slice(b,c):$(h.slice(b,itms.total).get().concat(h.slice(0,c).get())),is_function(d)&&d.call($tt0,i),i}),$cfs.bind(cf_e("isPaused",conf)+" "+cf_e("isStopped",conf)+" "+cf_e("isScrolling",conf),function(a,b){a.stopPropagation();var c=a.type.slice(conf.events.prefix.length),d=crsl[c];return is_function(b)&&b.call($tt0,d),d}),$cfs.bind(cf_e("configuration",conf),function(e,a,b,c){e.stopPropagation();var reInit=!1;if(is_function(a))a.call($tt0,opts);else if(is_object(a))opts_orig=$.extend(!0,{},opts_orig,a),b!==!1?reInit=!0:opts=$.extend(!0,{},opts,a);else if(!is_undefined(a))if(is_function(b)){var val=eval("opts."+a);is_undefined(val)&&(val=""),b.call($tt0,val)}else{if(is_undefined(b))return eval("opts."+a);"boolean"!=typeof c&&(c=!0),eval("opts_orig."+a+" = b"),c!==!1?reInit=!0:eval("opts."+a+" = b")}if(reInit){sz_resetMargin($cfs.children(),opts),FN._init(opts_orig),FN._bind_buttons();var sz=sz_setSizes($cfs,opts);$cfs.trigger(cf_e("updatePageStatus",conf),[!0,sz])}return opts}),$cfs.bind(cf_e("linkAnchors",conf),function(a,b,c){return a.stopPropagation(),is_undefined(b)?b=$("body"):is_string(b)&&(b=$(b)),is_jquery(b)&&0!=b.length?(is_string(c)||(c="a.caroufredsel"),b.find(c).each(function(){var a=this.hash||"";a.length>0&&-1!=$cfs.children().index($(a))&&$(this).unbind("click").click(function(b){b.preventDefault(),$cfs.trigger(cf_e("slideTo",conf),a)})}),!0):debug(conf,"Not a valid object.")}),$cfs.bind(cf_e("updatePageStatus",conf),function(a,b){if(a.stopPropagation(),opts.pagination.container){var d=opts.pagination.items||opts.items.visible,e=Math.ceil(itms.total/d);b&&(opts.pagination.anchorBuilder&&(opts.pagination.container.children().remove(),opts.pagination.container.each(function(){for(var a=0;e>a;a++){var b=$cfs.children().eq(gn_getItemIndex(a*d,0,!0,itms,$cfs));$(this).append(opts.pagination.anchorBuilder.call(b[0],a+1))}})),opts.pagination.container.each(function(){$(this).children().unbind(opts.pagination.event).each(function(a){$(this).bind(opts.pagination.event,function(b){b.preventDefault(),$cfs.trigger(cf_e("slideTo",conf),[a*d,-opts.pagination.deviation,!0,opts.pagination])})})}));var f=$cfs.triggerHandler(cf_e("currentPage",conf))+opts.pagination.deviation;return f>=e&&(f=0),0>f&&(f=e-1),opts.pagination.container.each(function(){$(this).children().removeClass(cf_c("selected",conf)).eq(f).addClass(cf_c("selected",conf))}),!0}}),$cfs.bind(cf_e("updateSizes",conf),function(){var b=opts.items.visible,c=$cfs.children(),d=ms_getParentSize($wrp,opts,"width");if(itms.total=c.length,crsl.primarySizePercentage?(opts.maxDimension=d,opts[opts.d.width]=ms_getPercentage(d,crsl.primarySizePercentage)):opts.maxDimension=ms_getMaxDimension(opts,d),opts.responsive?(opts.items.width=opts.items.sizesConf.width,opts.items.height=opts.items.sizesConf.height,opts=in_getResponsiveValues(opts,c,d),b=opts.items.visible,sz_setResponsiveSizes(opts,c)):opts.items.visibleConf.variable?b=gn_getVisibleItemsNext(c,opts,0):"*"!=opts.items.filter&&(b=gn_getVisibleItemsNextFilter(c,opts,0)),!opts.circular&&0!=itms.first&&b>itms.first){if(opts.items.visibleConf.variable)var e=gn_getVisibleItemsPrev(c,opts,itms.first)-itms.first;else if("*"!=opts.items.filter)var e=gn_getVisibleItemsPrevFilter(c,opts,itms.first)-itms.first;else var e=opts.items.visible-itms.first;debug(conf,"Preventing non-circular: sliding "+e+" items backward."),$cfs.trigger(cf_e("prev",conf),e)}opts.items.visible=cf_getItemsAdjust(b,opts,opts.items.visibleConf.adjust,$tt0),opts.items.visibleConf.old=opts.items.visible,opts=in_getAlignPadding(opts,c);var f=sz_setSizes($cfs,opts);return $cfs.trigger(cf_e("updatePageStatus",conf),[!0,f]),nv_showNavi(opts,itms.total,conf),nv_enableNavi(opts,itms.first,conf),f}),$cfs.bind(cf_e("destroy",conf),function(a,b){return a.stopPropagation(),tmrs=sc_clearTimers(tmrs),$cfs.data("_cfs_isCarousel",!1),$cfs.trigger(cf_e("finish",conf)),b&&$cfs.trigger(cf_e("jumpToStart",conf)),sz_restoreOrigCss($cfs.children()),sz_restoreOrigCss($cfs),FN._unbind_events(),FN._unbind_buttons(),"parent"==conf.wrapper?sz_restoreOrigCss($wrp):$wrp.replaceWith($cfs),!0}),$cfs.bind(cf_e("debug",conf),function(){return debug(conf,"Carousel width: "+opts.width),debug(conf,"Carousel height: "+opts.height),debug(conf,"Item widths: "+opts.items.width),debug(conf,"Item heights: "+opts.items.height),debug(conf,"Number of items visible: "+opts.items.visible),opts.auto.play&&debug(conf,"Number of items scrolled automatically: "+opts.auto.items),opts.prev.button&&debug(conf,"Number of items scrolled backward: "+opts.prev.items),opts.next.button&&debug(conf,"Number of items scrolled forward: "+opts.next.items),conf.debug}),$cfs.bind("_cfs_triggerEvent",function(a,b,c){return a.stopPropagation(),$cfs.triggerHandler(cf_e(b,conf),c)})},FN._unbind_events=function(){$cfs.unbind(cf_e("",conf)),$cfs.unbind(cf_e("",conf,!1)),$cfs.unbind("_cfs_triggerEvent")},FN._bind_buttons=function(){if(FN._unbind_buttons(),nv_showNavi(opts,itms.total,conf),nv_enableNavi(opts,itms.first,conf),opts.auto.pauseOnHover){var a=bt_pauseOnHoverConfig(opts.auto.pauseOnHover);$wrp.bind(cf_e("mouseenter",conf,!1),function(){$cfs.trigger(cf_e("pause",conf),a)}).bind(cf_e("mouseleave",conf,!1),function(){$cfs.trigger(cf_e("resume",conf))})}if(opts.auto.button&&opts.auto.button.bind(cf_e(opts.auto.event,conf,!1),function(a){a.preventDefault();var b=!1,c=null;crsl.isPaused?b="play":opts.auto.pauseOnEvent&&(b="pause",c=bt_pauseOnHoverConfig(opts.auto.pauseOnEvent)),b&&$cfs.trigger(cf_e(b,conf),c)}),opts.prev.button&&(opts.prev.button.bind(cf_e(opts.prev.event,conf,!1),function(a){a.preventDefault(),$cfs.trigger(cf_e("prev",conf))}),opts.prev.pauseOnHover)){var a=bt_pauseOnHoverConfig(opts.prev.pauseOnHover);opts.prev.button.bind(cf_e("mouseenter",conf,!1),function(){$cfs.trigger(cf_e("pause",conf),a)}).bind(cf_e("mouseleave",conf,!1),function(){$cfs.trigger(cf_e("resume",conf))})}if(opts.next.button&&(opts.next.button.bind(cf_e(opts.next.event,conf,!1),function(a){a.preventDefault(),$cfs.trigger(cf_e("next",conf))}),opts.next.pauseOnHover)){var a=bt_pauseOnHoverConfig(opts.next.pauseOnHover);opts.next.button.bind(cf_e("mouseenter",conf,!1),function(){$cfs.trigger(cf_e("pause",conf),a)}).bind(cf_e("mouseleave",conf,!1),function(){$cfs.trigger(cf_e("resume",conf))})}if(opts.pagination.container&&opts.pagination.pauseOnHover){var a=bt_pauseOnHoverConfig(opts.pagination.pauseOnHover);opts.pagination.container.bind(cf_e("mouseenter",conf,!1),function(){$cfs.trigger(cf_e("pause",conf),a)}).bind(cf_e("mouseleave",conf,!1),function(){$cfs.trigger(cf_e("resume",conf))})}if((opts.prev.key||opts.next.key)&&$(document).bind(cf_e("keyup",conf,!1,!0,!0),function(a){var b=a.keyCode;b==opts.next.key&&(a.preventDefault(),$cfs.trigger(cf_e("next",conf))),b==opts.prev.key&&(a.preventDefault(),$cfs.trigger(cf_e("prev",conf)))}),opts.pagination.keys&&$(document).bind(cf_e("keyup",conf,!1,!0,!0),function(a){var b=a.keyCode;b>=49&&58>b&&(b=(b-49)*opts.items.visible,itms.total>=b&&(a.preventDefault(),$cfs.trigger(cf_e("slideTo",conf),[b,0,!0,opts.pagination])))}),$.fn.swipe){var b="ontouchstart"in window;if(b&&opts.swipe.onTouch||!b&&opts.swipe.onMouse){var c=$.extend(!0,{},opts.prev,opts.swipe),d=$.extend(!0,{},opts.next,opts.swipe),e=function(){$cfs.trigger(cf_e("prev",conf),[c])},f=function(){$cfs.trigger(cf_e("next",conf),[d])};switch(opts.direction){case"up":case"down":opts.swipe.options.swipeUp=f,opts.swipe.options.swipeDown=e;break;default:opts.swipe.options.swipeLeft=f,opts.swipe.options.swipeRight=e}crsl.swipe&&$cfs.swipe("destroy"),$wrp.swipe(opts.swipe.options),$wrp.css("cursor","move"),crsl.swipe=!0}}if($.fn.mousewheel&&opts.mousewheel){var g=$.extend(!0,{},opts.prev,opts.mousewheel),h=$.extend(!0,{},opts.next,opts.mousewheel);crsl.mousewheel&&$wrp.unbind(cf_e("mousewheel",conf,!1)),$wrp.bind(cf_e("mousewheel",conf,!1),function(a,b){a.preventDefault(),b>0?$cfs.trigger(cf_e("prev",conf),[g]):$cfs.trigger(cf_e("next",conf),[h])}),crsl.mousewheel=!0}if(opts.auto.play&&$cfs.trigger(cf_e("play",conf),opts.auto.delay),crsl.upDateOnWindowResize){var i=function(){$cfs.trigger(cf_e("finish",conf)),opts.auto.pauseOnResize&&!crsl.isPaused&&$cfs.trigger(cf_e("play",conf)),sz_resetMargin($cfs.children(),opts),$cfs.trigger(cf_e("updateSizes",conf))},j=$(window),k=null;if($.debounce&&"debounce"==conf.onWindowResize)k=$.debounce(200,i);else if($.throttle&&"throttle"==conf.onWindowResize)k=$.throttle(300,i);else{var l=0,m=0;k=function(){var a=j.width(),b=j.height();(a!=l||b!=m)&&(i(),l=a,m=b)}}j.bind(cf_e("resize",conf,!1,!0,!0),k)}},FN._unbind_buttons=function(){var b=(cf_e("",conf),cf_e("",conf,!1));ns3=cf_e("",conf,!1,!0,!0),$(document).unbind(ns3),$(window).unbind(ns3),$wrp.unbind(b),opts.auto.button&&opts.auto.button.unbind(b),opts.prev.button&&opts.prev.button.unbind(b),opts.next.button&&opts.next.button.unbind(b),opts.pagination.container&&(opts.pagination.container.unbind(b),opts.pagination.anchorBuilder&&opts.pagination.container.children().remove()),crsl.swipe&&($cfs.swipe("destroy"),$wrp.css("cursor","default"),crsl.swipe=!1),crsl.mousewheel&&(crsl.mousewheel=!1),nv_showNavi(opts,"hide",conf),nv_enableNavi(opts,"removeClass",conf)},is_boolean(configs)&&(configs={debug:configs});var crsl={direction:"next",isPaused:!0,isScrolling:!1,isStopped:!1,mousewheel:!1,swipe:!1},itms={total:$cfs.children().length,first:0},tmrs={auto:null,progress:null,startTime:getTime(),timePassed:0},scrl={isStopped:!1,duration:0,startTime:0,easing:"",anims:[]},clbk={onBefore:[],onAfter:[]},queu=[],conf=$.extend(!0,{},$.fn.carouFredSel.configs,configs),opts={},opts_orig=$.extend(!0,{},options),$wrp="parent"==conf.wrapper?$cfs.parent():$cfs.wrap("<"+conf.wrapper.element+' class="'+conf.wrapper.classname+'" />').parent();if(conf.selector=$cfs.selector,conf.serialNumber=$.fn.carouFredSel.serialNumber++,conf.transition=conf.transition&&$.fn.transition?"transition":"animate",FN._init(opts_orig,!0,starting_position),FN._build(),FN._bind_events(),FN._bind_buttons(),is_array(opts.items.start))var start_arr=opts.items.start;else{var start_arr=[];0!=opts.items.start&&start_arr.push(opts.items.start)}if(opts.cookie&&start_arr.unshift(parseInt(cf_getCookie(opts.cookie),10)),start_arr.length>0)for(var a=0,l=start_arr.length;l>a;a++){var s=start_arr[a];if(0!=s){if(s===!0){if(s=window.location.hash,1>s.length)continue}else"random"===s&&(s=Math.floor(Math.random()*itms.total));if($cfs.triggerHandler(cf_e("slideTo",conf),[s,0,!0,{fx:"none"}]))break}}var siz=sz_setSizes($cfs,opts),itm=gi_getCurrentItems($cfs.children(),opts);return opts.onCreate&&opts.onCreate.call($tt0,{width:siz.width,height:siz.height,items:itm}),$cfs.trigger(cf_e("updatePageStatus",conf),[!0,siz]),$cfs.trigger(cf_e("linkAnchors",conf)),conf.debug&&$cfs.trigger(cf_e("debug",conf)),$cfs},$.fn.carouFredSel.serialNumber=1,$.fn.carouFredSel.defaults={synchronise:!1,infinite:!0,circular:!0,responsive:!1,direction:"left",items:{start:0},scroll:{easing:"swing",duration:500,pauseOnHover:!1,event:"click",queue:!1}},$.fn.carouFredSel.configs={debug:!1,transition:!1,onWindowResize:"throttle",events:{prefix:"",namespace:"cfs"},wrapper:{element:"div",classname:"caroufredsel_wrapper"},classnames:{}},$.fn.carouFredSel.pageAnchorBuilder=function(a){return'<a href="#"><span>'+a+"</span></a>"},$.fn.carouFredSel.progressbarUpdater=function(a){$(this).css("width",a+"%")},$.fn.carouFredSel.cookie={get:function(a){a+="=";for(var b=document.cookie.split(";"),c=0,d=b.length;d>c;c++){for(var e=b[c];" "==e.charAt(0);)e=e.slice(1);if(0==e.indexOf(a))return e.slice(a.length)}return 0},set:function(a,b,c){var d="";if(c){var e=new Date;e.setTime(e.getTime()+1e3*60*60*24*c),d="; expires="+e.toGMTString()}document.cookie=a+"="+b+d+"; path=/"},remove:function(a){$.fn.carouFredSel.cookie.set(a,"",-1)}},$.extend($.easing,{quadratic:function(a){var b=a*a;return a*(-b*a+4*b-6*a+4)},cubic:function(a){return a*(4*a*a-9*a+6)},elastic:function(a){var b=a*a;return a*(33*b*b-106*b*a+126*b-67*a+15)}}))})(jQuery);

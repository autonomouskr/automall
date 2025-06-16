<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $deviceType;
$current_time = current_time('timestamp');
?>
<!-- ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 레이어 팝업 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<script type="text/javascript">
//레이어 높이 조정
var layerHeightForScroll = function($takeHeight){
	var $dHeight = $(window).height();
	if ($dHeight < 700) //모바일만
	$('.flow').css('height',$dHeight-$takeHeight);
}
//레이어 닫기
var layerClose = function (id){
	$(id).parent().parent().removeClass('popupOn').hide();
	if($("[id^='layerPopupName']").filter('.popupOn').length == 0) {
		$('.layer-container').hide();
		//$('html, body').css('overflow','auto');//레이어 닫고 꼭 스크롤바 다시 활성해줘야함..
	}
}
//레이어 닫기 이벤트 그랩
var layerCloseControl = function(){
	$('.layerClose').click(function(){ layerClose(this); });
}
//제품옵션 AJAX
var goodsOptionChanger = function($data){
	$.ajax({
		type  : "post",
		dataType : 'html',
		async : false,
		url   : '<?php bloginfo('template_url')?>/ajax/goods-option-from.php',
		data  : {
			cartType : $data[0],
			cartIdx : $data[1]
		},
		success: function(text){
			var result = text; // 결과 DATA

			if (result == 'FAIL'){

				return false;

			}else{

				//$(result).appendTo('.layer-container.forAjax');//HTML추가
				$('.layer-container.forAjax').html(result);//HTML추가

				layerHeightForScroll(50); // 높이 설정(스크롤바)
				layerCloseControl();      // 닫기 이벤트 준비
				$(window).resize(function(){ layerHeightForScroll(50); }); // 동적 높이 설정(스크롤바) -> 모바일 가로/세로 위해

				$('.layer-container.forAjax').css('display','block');      // 레이어 띄움
				//$(html).css('overflow','hidden');                  // 메인창 스크롤바 숨김
				$('.ly_close').click(function(){ layerClose(this); }); //닫기 이벤트 준비

			}
		},
		error: function(){
			alert("서버와의 통신이 실패했습니다.");
		}
	});
}

var Cookie =
{
	cookie_arr : null,

	set : function (name,value,options)
	{
		options = options || {};

		this.cookie_arr = [escape(name) + '=' + escape(value)];

		//-- expires
		if (options.expires)
		{
			if( typeof options.expires === 'object' && options.expires instanceof Date )
			{
				var date = options.expires;
				var expires = "expires=" + date.toUTCString();
				this.cookie_arr.push (expires);
			}
		} else if (options.expires_day) {
			this.set_expires_date (options.expires_day , 24*60*60);
		} else if (options.expires_hour) {
			this.set_expires_date (options.expires_hour , 60*60);
		}

		//-- domain
		if (options.domain)
		{
			var domain = "domain=" + options.domain;
			this.cookie_arr.push (domain);
		}

		//-- path
		if (options.path)
		{
			var path = 'path=' + options.path;
			this.cookie_arr.push (path);
		}

		//-- secure
		if( options.secure === true )
		{
			var secure = 'secure';
			this.cookie_arr.push (secure);
		}

		document.cookie = this.cookie_arr.join('; ');
		//console.log (this.cookie_arr.join('; '));
	},

	get : function (name)
	{
		var nameEQ = escape(name) + "=";
		var ca = document.cookie.split(';');

		for(var i=0;i < ca.length;i++)
		{
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length,c.length));
		}
		return null;
	},

	del : function (name , options)
	{
		options = options || {};
		options.expires_day = -1;
		this.set ( name , '' , options );
	},

	set_expires_date : function (expires , time)
	{
		var date = new Date();
		date.setTime(date.getTime()+(expires*time*1000));
		var expires = "expires=" + date.toUTCString();
		this.cookie_arr.push (expires);
	}
};


/* 레이어창 컨트롤 */
$(document).ready(function(){

	$('.openLayer').click(function(){

		var $layerType = $(this).data('name');
		switch ($layerType)
		{
			//제품옵션수정
			case 'goodsOptionChanger':
			{
				var $dataIDs   = $(this).data('ids');
				var $dataArray = $dataIDs.split('^');
				goodsOptionChanger( $dataArray );
			}
			break;

			//화물 추적
			case 'tracking':
			{
				var $trackingDataSet = $(this).data('tracking');
				var $trackingData    = $trackingDataSet.split('^');
				$('.trackingTbl .orderNo').text($trackingData[0]);
				$('.trackingTbl .goodsName').text($trackingData[1]);
				$('.trackingTbl .delivery').text($trackingData[2]);
				if($trackingData[4]=="") $trackingData[4] = "#";
				var linkCheckStr = $trackingData[4].substr($trackingData[4].length - 1, 1);
				var linkDelivery="";
				if(linkCheckStr=="=") linkDelivery=$trackingData[4]+$trackingData[3];
				else linkDelivery=$trackingData[4];

				jQuery('.trackingTbl .trackingNo').html($trackingData[3] + "&nbsp;&nbsp;&nbsp;<button class=\"bb_btn cus_fill w150\" id='deliveryCheck' onclick=\"window.open('"+linkDelivery+"');\" type=\"button\" style='padding:0 5px;line-height:18px;'><strong style='width:80px;'>배송조회</strong></button>");

				layerHeightForScroll(50); // 높이 설정(스크롤바)
				layerCloseControl();      // 닫기 이벤트 준비
				$(window).resize(function(){ layerHeightForScroll(50); }); // 동적 높이 설정(스크롤바) -> 모바일 가로/세로 위해
				$('.layer-container.tracking').css('display','block');      // 레이어 띄움
				$('.layer-container .layerBox').css('display', 'block');
			}
			break;
		}
	});

	layerCloseControl();

	//오늘 하루 보지 않기
	$('input[name=noMoreShow]').change(function(){
		var $name = $(this).val();
		var check = Cookie.get ($name);

		if ( check != 'oneDayNoMore' || !check )
		{
			Cookie.set ($name , 'oneDayNoMore' , {
				expires_day : 1,
			});

			$("#"+$name).removeClass('popupOn').hide();
			if($("[id^='layerPopupName']").filter('.popupOn').length == 0) {
				$(this).parents('.layer-container.forPopUp.popupOn').removeClass('popupOn');
			}
		}
	});

<?php
	 if($deviceType == "desktop") {
		//윈도우 팝업
		$winPopCnt = $wpdb->get_var("select count(*) from bbse_commerce_config where config_data REGEXP '.*\"popup_use\";s:[0-9]+:\"on\".*' and config_data REGEXP '.*\"popup_window\";s:[0-9]+:\"window\".*'");
		if ( is_front_page() && !get_query_var('bbseCat') && !get_query_var('bbseGoods') && !get_query_var('bbsePage') && !get_query_var('bbseMy') && plugin_active_check('BBSe_Commerce') ){
			if($winPopCnt > 0) {
				$popupRes = $wpdb->get_results("select * from bbse_commerce_config where config_data REGEXP '.*\"popup_use\";s:[0-9]+:\"on\".*' and config_data REGEXP '.*\"popup_window\";s:[0-9]+:\"window\".*' and config_type='popup' order by idx asc");
				$skinname = $wpdb->get_var("select skinname from bbse_commerce_membership_config");	
				foreach($popupRes as $i=>$popup) {
					$pop = unserialize($popup->config_data);
					if($current_time >= $pop['s_period_1'] && $current_time <= $pop['s_period_2']) {
					?>
						if ( getCookie ('winPopupName<?php echo $popup->idx; ?>') != 'oneDayNoMore' ) {
						//if ( Cookie.get ('winPopupName<?php echo $popup->idx; ?>') != 'oneDayNoMore' ) {
							window.open('<?php echo BBSE_COMMERCE_THEME_WEB_URL."/skin/".$skinname."/popup-win.php?pidx=".$popup->idx; ?>','winPopup<?php echo $popup->idx; ?>','width=<?php echo $pop['popup_width']; ?>px,height=<?php echo $pop['popup_height']; ?>px,top=<?php echo $pop['popup_top']; ?>px,left=<?php echo $pop['popup_left']; ?>px,scrollbars=<?php echo $pop['popup_scrollbar']; ?>,resizeable=no');
						}
					<?php
					}
				}
			}
		}
	}
?>



});

</script>
<div class="layer-container forAjax"></div><!-- /layer-container Ajax용-->
<!-- /layer-container  일반PopUp용-->
<?php 
if ( $deviceType == "desktop" && is_front_page() && !get_query_var('bbseCat') && !get_query_var('bbseGoods') && !get_query_var('bbsePage') && !get_query_var('bbseMy') && plugin_active_check('BBSe_Commerce') ){
	//레이어 팝업
	$layerPopCnt = $wpdb->get_var("select count(*) from bbse_commerce_config where config_data REGEXP '.*\"popup_use\";s:[0-9]+:\"on\".*' and config_data REGEXP '.*\"popup_window\";s:[0-9]+:\"layer\".*'");
	$popupRes = $wpdb->get_results("select * from bbse_commerce_config where config_data REGEXP '.*\"popup_use\";s:[0-9]+:\"on\".*' and config_data REGEXP '.*\"popup_window\";s:[0-9]+:\"layer\".*' and config_type='popup' order by idx asc");
	$oneday_hide_cnt = 0;
	foreach($popupRes as $i=>$popup) {
		$pop = unserialize($popup->config_data);
		if($_COOKIE['layerPopupName'.$popup->idx] == 'oneDayNoMore' || ($current_time < $pop['s_period_1'] || $current_time > $pop['s_period_2'])) $oneday_hide_cnt ++;
	}
?>
<div class="layer-container forPopUp <?php echo ($layerPopCnt > 0 && $oneday_hide_cnt < $layerPopCnt)?"popupOn":""; ?>">
<?php 
	foreach($popupRes as $i=>$popup) {
		$pop = unserialize($popup->config_data);
		$pop_style = "";
		if($current_time >= $pop['s_period_1'] && $current_time <= $pop['s_period_2']) {
			$pop_style .= ($pop['popup_width']!="")?"width:".$pop['popup_width']."px;":"";
			$pop_style .= ($pop['popup_height']!="")?"height:".$pop['popup_height']."px;":"";
			$pop_style .= ($pop['popup_top']!="")?"top:".$pop['popup_top']."px;":"";
			$pop_style .= ($pop['popup_left']!="")?"left:".$pop['popup_left']."px;":"";
			$pop_style .= ($_COOKIE['layerPopupName'.$popup->idx] == 'oneDayNoMore') ? "display:none;" : "";
?>
	<div id="layerPopupName<?php echo $popup->idx; ?>" class="layerBox <?php echo $_COOKIE['layerPopupName'.$popup->idx] == 'oneDayNoMore'? '' : 'popupOn'?>" style="margin:0;<?php echo $pop_style; ?>">
		<div class="boxTitle">
			<?php echo $pop['popup_title']; ?>
			<button class="layerClose"><span>닫기</span></button>
		</div>

		<div class="boxContent" style="width:<?php echo $pop['popup_width']?>px;height:<?php echo $pop['popup_height']-70?>px;overflow-y:auto;"><?php echo $popup->config_editor; ?></div>

		<div class="noMoreShow">
			<input type="checkbox" name="noMoreShow" id="noMoreShow" class="" value="layerPopupName<?php echo $popup->idx?>"> <label for="noMoreShow">오늘은 더 보고 싶지 않아요</label>
		</div>
	</div>
<?php
		}
	}
?>
  </div>
<?php
}
?>


<div class="layer-container tracking">
  <div class="layerBox">
    <div class="boxTitle">
      배송정보
      <button class="layerClose"><span>닫기</span></button>
    </div>
    <div class="boxContent">
      <div class="flow">
        <div class="trackingNotice">배송조회를 하시려면 송장번호를 클릭하세요</div>
        <table class="trackingTbl">
          <caption>배송조회</caption>
          <colgroup>
            <col style="width:25%;">
            <col style="width:auto;">
          </colgroup>
          <tbody>
            <tr>
              <th scope="row">상품명</th>
              <td class="goodsName"></td>
            </tr>
            <tr>
              <th scope="row">주문번호</th>
              <td class="orderNo"></td>
            </tr>
            <tr>
              <th scope="row">택배사</th>
              <td class="delivery"></td>
            </tr>
            <tr>
              <th scope="row">송장번호</th>
              <td class="trackingNo"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div><!-- /layer-container -->

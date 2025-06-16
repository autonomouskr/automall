<?php
global $theme_shortname;
//$wpdb->get_results("select * from bbse_commerce_cart");
?>

<script language="javascript">
var remove_recent = function(rIdx,remoteIp){
	var tMode="removeRecent"
	var apiUrl=common_var.goods_template_url+"/proc/index-banner-right.exec.php";

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: apiUrl, 
		data: {tMode:tMode, rIdx:rIdx, remoteIp:remoteIp}, 
		success: function(data){
			var result = data.split("|||"); 
			if(result[0] == "success"){
				jQuery('#recent_list_count').text("("+result[1]+")");
				jQuery('#resentGoodsList').html(result[2]);
			}
			else if(result[0] == "emptyRecent"){
				jQuery('#resentGoodsList').html("");
				jQuery('#recent_list_count').text("(0)");
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
</script>
			<div id="quick">
				<?php if(get_option($theme_shortname."_layout_right_last_goods")=="Y") {?>
				<div class="fav_box">
					<?php 
						$rCnt = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_recent WHERE remote_ip='".$_SERVER['REMOTE_ADDR']."'");
					?>
					<h2>최근본 상품<span id="recent_list_count">(<?php echo $rCnt;?>)</span></h2>
					<div id="favList" class="fav_list">
						<ul id="resentGoodsList" class="" style="min-height:25px;">
							<?php echo bbse_commerce_get_recent_view_goods($_SERVER['REMOTE_ADDR']);?>
						</ul>
					</div>
				</div><!--//최근본 상품-->
				<?php }?>

				<?php if(get_option($theme_shortname."_display_use_right_banner")=="U"){?>
				<div class="bnn_box">
					<h2>광고 배너</h2>
					<div class="bnn_list">
				<?php
					if(get_option($theme_shortname."_display_use_right_rolling_banner")=='N'){							
							for($rightBannerCnt=0;$rightBannerCnt<=get_option($theme_shortname."_display_use_right_banner_count");$rightBannerCnt++) {
								if(get_option($theme_shortname."_display_right_banner_use_".$rightBannerCnt)=="Y" && get_option($theme_shortname."_display_right_banner_img_".$rightBannerCnt)!=""){
							?>
							<div style="margin:2px 0;"><a href="<?php echo get_option($theme_shortname."_display_right_banner_url_".$rightBannerCnt)?get_option($theme_shortname."_display_right_banner_url_".$rightBannerCnt):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_display_right_banner_url_".$rightBannerCnt."_window")?>"><img src="<?php echo get_option($theme_shortname."_display_right_banner_img_".$rightBannerCnt)?>" alt="오른쪽 배너" /></a></div>
							<?php
								}
							}
					}
					else{
					?>
						<ul class="">
							<?php
							for($rightBannerCnt=0;$rightBannerCnt<=get_option($theme_shortname."_display_use_right_banner_count");$rightBannerCnt++) {
								if(get_option($theme_shortname."_display_right_banner_use_".$rightBannerCnt)=="Y" && get_option($theme_shortname."_display_right_banner_img_".$rightBannerCnt)!=""){
							?>
							<li><a href="<?php echo get_option($theme_shortname."_display_right_banner_url_".$rightBannerCnt)?get_option($theme_shortname."_display_right_banner_url_".$rightBannerCnt):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_display_right_banner_url_".$rightBannerCnt."_window")?>"><img src="<?php echo get_option($theme_shortname."_display_right_banner_img_".$rightBannerCnt)?>" alt="오른쪽 배너" /></a></li>
							<?php
								}
							}
							?>
						</ul>
						<p class="control_btn">
							<button type ="button" class="prev"><span>이전</span></button>
							<button type ="button" class="next"><span>다음</span></button>
							<span class="count">
								<strong class="view">1</strong> / <span class="max">3</span>
							</span>
						</p>
					<?php
					}
					?>
					</div>
				</div><!--//배너 영역 -->
				<?php }?>
				<div class="top-down_box">
					<a class="top" title="페이지 상단으로 가기">페이지 상단으로 가기</a>
					<a class="down" title="페이지 하단으로 가기">페이지 하단으로 가기</a>
				</div><!--//최근본 상품-->


			</div><!--// #quick -->

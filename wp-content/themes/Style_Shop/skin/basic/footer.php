<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
?>
		<div class="content_mask"></div>

    <?php
	global $current_user,$theme_shortname, $deviceType;
	$skinname = get_option($theme_shortname."_sub_skin_name")?get_option($theme_shortname."_sub_skin_name"):"basic";

    #우측 스카이 배너
    //get_template_part('part/index', 'banner-right');
	get_template_part('part/global', 'skyBanner');
    ?>
  </div><!-- //container -->

<!--N: includ footer S -->
<?php 
if(wp_is_mobile()){
	$footerPadding="20px";
}
else{
	if(get_option($theme_shortname."_basic_use_bottom_banner")=="U") $footerPadding="100px";
	else  $footerPadding="40px";
}
?>
  <div id="footer" style="padding:20px 0 <?php echo $footerPadding;?>">
    <div class="foot">
      <ul class="foot_menu">
		<li>&nbsp;</li>
		<?php if(get_option($theme_shortname."_basic_footer_quick_link_1")!=""){?>
		<li><a href="<?php echo get_option($theme_shortname."_basic_footer_quick_link_1_url")?get_option($theme_shortname."_basic_footer_quick_link_1_url"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_footer_quick_link_1_window")?>"><?php echo get_option($theme_shortname."_basic_footer_quick_link_1")?></a></li>
		<?php }?>
		<?php if(get_option($theme_shortname."_basic_footer_quick_link_2")!=""){?>
		<li><a href="<?php echo get_option($theme_shortname."_basic_footer_quick_link_2_url")?get_option($theme_shortname."_basic_footer_quick_link_2_url"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_footer_quick_link_2_window")?>"><?php echo get_option($theme_shortname."_basic_footer_quick_link_2")?></a></li>
		<?php }?>
		<?php if(get_option($theme_shortname."_basic_footer_quick_link_3")!=""){?>
		<li><a href="<?php echo get_option($theme_shortname."_basic_footer_quick_link_3_url")?get_option($theme_shortname."_basic_footer_quick_link_3_url"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_footer_quick_link_3_window")?>"><?php echo get_option($theme_shortname."_basic_footer_quick_link_3")?></a></li>
		<?php }?>
		<?php if(get_option($theme_shortname."_basic_footer_quick_link_4")!=""){?>
		<li><a href="<?php echo get_option($theme_shortname."_basic_footer_quick_link_4_url")?get_option($theme_shortname."_basic_footer_quick_link_4_url"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_footer_quick_link_4_window")?>"><?php echo get_option($theme_shortname."_basic_footer_quick_link_4")?></a></li>
		<?php }?>
		<?php if(get_option($theme_shortname."_basic_footer_quick_link_5")!=""){?>
		<li><a href="<?php echo get_option($theme_shortname."_basic_footer_quick_link_5_url")?get_option($theme_shortname."_basic_footer_quick_link_5_url"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_footer_quick_link_5_window")?>"><?php echo get_option($theme_shortname."_basic_footer_quick_link_5")?></a></li>
		<?php }?>
      </ul>
      <div class="family_site">
		<?php if(get_option($theme_shortname."_basic_use_footer_family_site")=="U"){?>
        <label for="familySite">관계사바로가기</label>
        <select name="" id="familySite">
          <option value="">관계사바로가기</option>
		  <?php
			for($familySiteCnt=0;$familySiteCnt<=get_option($theme_shortname."_basic_use_footer_family_site_count");$familySiteCnt++) {
				if(get_option($theme_shortname."_basic_footer_family_site_".$familySiteCnt)!=""){
			?>
				<option value="<?php echo get_option($theme_shortname."_basic_footer_family_site_".$familySiteCnt."_url")."|".get_option($theme_shortname."_basic_footer_family_site_".$familySiteCnt."_window")?>"><img src="<?php echo get_option($theme_shortname."_display_right_banner_img_".$familySiteCnt)?>" alt="<?php echo get_option($theme_shortname."_basic_footer_family_site_".$familySiteCnt)?>"><?php echo get_option($theme_shortname."_basic_footer_family_site_".$familySiteCnt)?></option>
			<?php
				}
			}
		  ?>
        </select>
        <button type="button" class="bb_btn gradient family-site-btn"><span class="mid t_sml">이동</span></button>
		<?php }?>
      </div>
      <dl class="sns_list">
        <dt>
			<?php if(get_option($theme_shortname."_basic_logo_type_bottom")=="image"){?>
			<a href="<?php echo BBSE_COMMERCE_SITE_URL?>"><img src="<?php echo get_option($theme_shortname."_basic_logo_img_bottom")?>" alt="<?php bloginfo('name');?>" /></a>
			<?php }else if(get_option($theme_shortname."_basic_logo_type_bottom")=="text"){?>
			<a href="<?php echo BBSE_COMMERCE_SITE_URL?>" style="color:<?php echo get_option($theme_shortname."_basic_logo_color_bottom")?>;font-size:<?php echo get_option($theme_shortname."_basic_logo_text_size_bottom")?>px;"><?php echo get_option($theme_shortname."_basic_logo_text_bottom")?></a>
			<?php }else{echo bloginfo('name');}?>
		</dt>

		<?php
		if(get_option($theme_shortname.'_sns_twitter_enable')  == 'U' ||
		get_option($theme_shortname.'_sns_facebook_enable') == 'U' ||
		get_option($theme_shortname.'_sns_google_enable')   == 'U' ){
		?>
			<?php if(get_option($theme_shortname.'_sns_facebook_enable')=='U'){?>
			<dd class="facebook"><a href="<?php echo get_option($theme_shortname.'_sns_facebook_url')?>" target="_blank" title="새창열림">facebook</a></dd>
			<?php }//endif?>

			<?php if(get_option($theme_shortname.'_sns_twitter_enable')=='U'){?>
			<dd class="twitter"><a href="<?php echo get_option($theme_shortname.'_sns_twitter_url')?>" target="_blank" title="새창열림">twitter</a></dd>
			<?php }//endif?>

			<?php if(get_option($theme_shortname.'_sns_google_enable')=='U'){?>
			<dd class="google"><a href="<?php echo get_option($theme_shortname.'_sns_google_url')?>" target="_blank" title="새창열림">google+</a></dd>
			<?php }//endif?>
		<?php }//endif ?>

      </dl>
      <address class="company_info">
		<?php if(get_option($theme_shortname."_basic_footer")!="") {echo nl2br(stripslashes(get_option($theme_shortname."_basic_footer")));}?>
      </address>
      <p class="copyright">
	  <?php if(get_option($theme_shortname."_basic_footer_copyright")!="") {echo nl2br(stripslashes(get_option($theme_shortname."_basic_footer_copyright")));}?>
      </p>
		<?php 
		$lguplusCnt='0';
		if(get_option($theme_shortname."_basic_use_bottom_banner")=="U"){
		?>
      <div id="famsite_roll" class="foot_mark">
        <ul class="slides">
		<?php
			for($bottomBannerCnt=0;$bottomBannerCnt<=get_option($theme_shortname."_basic_use_bottom_banner_count");$bottomBannerCnt++) {
				if(get_option($theme_shortname."_basic_bottom_banner_img_".$bottomBannerCnt)!=""){

					if(get_option($theme_shortname."_basic_bottom_banner_type_".$bottomBannerCnt)=="lguplus"){
						$lguplusCnt ++;
			?>
				<li>
					<img src="<?php echo get_option($theme_shortname."_basic_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="goValidEscrow('<?php echo trim(str_replace(" ","",get_option($theme_shortname."_basic_bottom_banner_mallid_".$bottomBannerCnt)));?>');" style="cursor:pointer;" />
				</li>
			<?php
					}
					elseif(get_option($theme_shortname."_basic_bottom_banner_type_".$bottomBannerCnt)=="inicis"){
			?>
				<li>
					<img src="<?php echo get_option($theme_shortname."_basic_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="window.open('https://mark.inicis.com/mark/escrow_popup.php?mid=<?php echo trim(str_replace(" ","",get_option($theme_shortname."_basic_bottom_banner_mallid_".$bottomBannerCnt)));?>','mark','scrollbars=no,resizable=no,width=565,height=683');" style="cursor:pointer;" />
				</li>
			<?php
					}
					elseif(get_option($theme_shortname."_basic_bottom_banner_type_".$bottomBannerCnt)=="allthegate"){
			?>
				<li>
					<img src="<?php echo get_option($theme_shortname."_basic_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="window.open('http://www.allthegate.com/hyosung/paysafe/escrow_check.jsp?service_id=<?php echo trim(str_replace(" ","",get_option($theme_shortname."_basic_bottom_banner_mallid_".$bottomBannerCnt)));?>&biz_no=<?php echo trim(str_replace("-","",str_replace(" ","",get_option($theme_shortname."_basic_bottom_banner_businessno_".$bottomBannerCnt))));?>','allthegate_window','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=410,height=320')" style="cursor:pointer;" />
				</li>
			<?php
					}
					elseif(get_option($theme_shortname."_basic_bottom_banner_type_".$bottomBannerCnt)=="fairtrade"){
			?>
				<li>
					<img src="<?php echo get_option($theme_shortname."_basic_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" onClick="window.open('http://www.ftc.go.kr/info/bizinfo/communicationViewPopup.jsp?wrkr_no=<?php echo trim(str_replace("-","",str_replace(" ","",get_option($theme_shortname."_basic_bottom_banner_businessno_".$bottomBannerCnt))))?>','fairtrade_window','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,width=735,height=632')" style="cursor:pointer;" />
				</li>
			<?php
					}
					else{
			?>
				<li>
					<a href="<?php echo get_option($theme_shortname."_basic_bottom_banner_url_".$bottomBannerCnt)?get_option($theme_shortname."_basic_bottom_banner_url_".$bottomBannerCnt):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_bottom_banner_url_".$bottomBannerCnt."_window")?>"><img src="<?php echo get_option($theme_shortname."_basic_bottom_banner_img_".$bottomBannerCnt)?>" alt="하단배너<?php echo $bottomBannerCnt?>" /></a>
				</li>
			<?php
					}
				}
			}
		?>
        </ul>
        <p class="control_btn">
          <button type ="button" class="prev"><i class="fa fa-chevron-left"></i><span>이전</span></button>
          <button type ="button" class="next"><i class="fa fa-chevron-right"></i><span>다음</span></button>
        </p>
      </div>
		<?php
		}
		?>
    </div>
  </div>
<!--N: includ footer E -->
</div><!--//#wrap -->
 <?php
 if($lguplusCnt>'0'){echo "<script language = \"javascript\" src = \"https://pgweb.dacom.net/WEB_SERVER/js/escrowValid.js\"></script>";} // LG U+ 인증마크 팝업

 if(wp_is_mobile()){
	 if(get_option($theme_shortname."_display_use_mobile_icon")=='U' && get_option($theme_shortname."_display_use_mobile_icon_position")=='bottom'){
		$mIconFlag=false;
		for($z=1;$z<9;$z++){
			if(get_option($theme_shortname."_display_mobile_icon_icon_".$z) && get_option($theme_shortname."_display_mobile_icon_title_".$z)){
				$mIconFlag=true;
				break;
			}
		}

		if(get_option($theme_shortname."_display_use_mobile_icon_count")>'0' && $mIconFlag==true){
			echo "<div class='mobileIconFooterMargin' style='width:100%;height:0;'></div>";
			echo "<div class='goOnTop'><a href='#top' title='TOP'><i class='fa fa-chevron-up'></i></a></div>";
			get_template_part('skin/'.$skinname.'/mobile_icon'); 
		}
		else{
			echo "<div class='goOnTop'><a href='#top' title='TOP'><i class='fa fa-chevron-up'></i></a></div>";
		}
	 }
	 else{
		echo "<div class='goOnTop'><a href='#top' title='TOP'><i class='fa fa-chevron-up'></i></a></div>";
	 }
 }

 wp_footer(); 
 ?>
<?php get_template_part('skin/'.$skinname.'/popup'); ?>
<?php
if (get_option($theme_shortname."_google_use_analytics")=='U' || get_option($theme_shortname."_naver_analytics_use"))
  get_template_part('part/global', 'analytics');
?>
</body>
</html>
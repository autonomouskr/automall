				<!-- 1개짜리 배너 -->
				<?php global $theme_shortname?>
				<?php if(get_option($theme_shortname.'_display_use_bottom_banner')=="U"){?>
		        <div class="main_section main_bnn">
					<?php if(get_option($theme_shortname."_display_middle_banner_image_1")!="") {?>
					<a href="<?php echo get_option($theme_shortname."_display_bottom_banner_link_1")?get_option($theme_shortname."_display_bottom_banner_link_1"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_display_bottom_banner_link_1_window")?>"><img src="<?php echo get_option($theme_shortname."_display_bottom_banner_image_1")?>" alt="하단배너" /></a>
					<?php }?>
				</div><!--//광고 배너-->
				<?php }?>
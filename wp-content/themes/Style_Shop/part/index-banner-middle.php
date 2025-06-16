				<!-- 중간 배너 -->
				<?php global $theme_shortname?>
				<?php if(get_option($theme_shortname.'_display_use_middle_banner')=="U"){?>
		        <div class="main_section banner-two-column">
					<?php if(get_option($theme_shortname."_display_middle_banner_image_1")!="") {?>
					<a href="<?php echo get_option($theme_shortname."_display_middle_banner_link_1")?get_option($theme_shortname."_display_middle_banner_link_1"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_display_middle_banner_link_1_window")?>"><img src="<?php echo get_option($theme_shortname."_display_middle_banner_image_1")?>" alt="중간배너1" /></a>
					<?php }?>
					<?php if(get_option($theme_shortname."_display_middle_banner_image_2")!="") {?>
					<a href="<?php echo get_option($theme_shortname."_display_middle_banner_link_2")?get_option($theme_shortname."_display_middle_banner_link_2"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_display_middle_banner_link_2_window")?>"><img src="<?php echo get_option($theme_shortname."_display_middle_banner_image_2")?>" alt="중간배너2" /></a>
					<?php }?>
				</div><!--//중간 배너-->
				<?php }?>
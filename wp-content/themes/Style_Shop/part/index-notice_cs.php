				<!-- 공지사항 최근글과 CS센터 -->
				<?php global $theme_shortname?>
				<?php if(get_option($theme_shortname."_display_use_cs_center")=="U"){?>
				<div class="main_section bb_customer">
					<div class="bb_notice">
						<?php if(function_exists('bbse_board_parameter_encryption')){?>
						<h3 class="lv3_title"><?php echo get_option($theme_shortname."_display_cs_center_board_title")?></h3>
						<ul>
						<?php
							if(get_option($theme_shortname."_display_cs_center_board_type")=="B") {
								$display_cs_center_board_content = get_option($theme_shortname."_display_cs_center_board_content");

								if($display_cs_center_board_content){
									$total_1 = $wpdb->get_var("SELECT COUNT(*) FROM information_schema.TABLES WHERE table_name='".$wpdb->prefix."bbse_".$display_cs_center_board_content."_board'");
									if($total_1>0) $tbName_1=$wpdb->prefix."bbse_".$display_cs_center_board_content."_board";
									else $tbName_1="bbse_".$display_cs_center_board_content."_board";

									$brd_res = $wpdb->get_results("select no, title, write_date from ".$tbName_1." order by no desc limit 3");

									foreach($brd_res as $i=>$brd) {
										$w_year = substr($brd->write_date, 0, 4);
										$w_month = substr($brd->write_date, 5, 2);
										$w_day = substr($brd->write_date, 8, 2);
										$w_hour = substr($brd->write_date, 11, 2);
										$w_minute = substr($brd->write_date, 14, 2);
										if((current_time('timestamp') - mktime($w_hour, $w_minute, 0, $w_month, $w_day, $w_year)) / 60 / 60 / 24 < 1){
											$new_icon = '<span class="bb_new">New</span>';
										}else{
											$new_icon = '';
										}
										$curUrl = get_permalink(get_option($theme_shortname."_display_cs_center_board_page"));
										$purl = parse_url($curUrl);
										if(!$purl['query']) $link_add = "?";
										else $link_add = "&";
										$title = cut_text($brd->title, 20);
										$tit_link = $curUrl.$link_add."nType=".bbse_board_parameter_encryption($display_cs_center_board_content, 'view', $brd->no);
										echo '<li><a href="'.$tit_link.'">'.$title."&nbsp;".$new_icon.'</a></li>';
									}
								}
							}else if(get_option($theme_shortname."_display_cs_center_board_type")=="C") {
								
								$args = array( 'posts_per_page' => 3, 'category' => 3 , 'order' => 'DESC');
								$brd_res = get_posts($args);
								foreach($brd_res as $i=>$brd) {
									$w_year = substr($brd->post_date, 0, 4);
									$w_month = substr($brd->post_date, 5, 2);
									$w_day = substr($brd->post_date, 8, 2);
									$w_hour = substr($brd->post_date, 11, 2);
									$w_minute = substr($brd->post_date, 14, 2);
									if((current_time('timestamp') - mktime($w_hour, $w_minute, 0, $w_month, $w_day, $w_year)) / 60 / 60 / 24 < 1){
										$new_icon = '<span class="bb_new">New</span>';
									}else{
										$new_icon = '';
									}
									$title = cut_text($brd->post_title, 20);
									echo '<li><a href="'.$brd->guid.'">'.$title."&nbsp;".$new_icon.'</a></li>';
								}
							}
						
						?>
						</ul>
						<?php }else{echo "BBSe_Board  플러그인 설치되지 않았거나 비활성화 상태입니다.";}?>
					</div>
					<div class="bb_custom">
						<div class="bb_cs_box">
							<h3 class="lv3_title"><?php echo get_option($theme_shortname."_display_cs_center_title")?></h3>
							<p class="bb_cs_tel">
								<strong><?php echo get_option($theme_shortname."_display_cs_center_phone")?></strong>
							</p>
							<p class="bb_time">
							<?php echo nl2br(get_option($theme_shortname."_display_cs_center_content"))?>
							</p>
						</div>
						<ul class="bb_cs_menu">
						<?php
							for($quickLinkCnt=0;$quickLinkCnt<=get_option($theme_shortname."_display_option_menu_count");$quickLinkCnt++) {
								if(get_option($theme_shortname."_display_option_menu_title_".$quickLinkCnt)!=""){
						?>
							<li><a href="<?php echo get_option($theme_shortname."_display_option_menu_link_".$quickLinkCnt)?get_option($theme_shortname."_display_option_menu_link_".$quickLinkCnt):"#"?>" target="<?=get_option($theme_shortname."_display_option_menu_link_".$quickLinkCnt."_window")?>"><?php echo get_option($theme_shortname."_display_option_menu_title_".$quickLinkCnt)?></a></li>
						<?php
								}
							}
						?>
						</ul>
					</div>
				</div><!--//고객센터 -->
				<?php }?>
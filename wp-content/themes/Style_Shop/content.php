<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
global $theme_shortname;
?>
					<?php if(get_the_ID()>'0' && is_single()){?>
						<div class="page_cont">
							<h2 class="post_title"><?php the_title();?></h2>
						</div>
						<header class="entry-header blind">
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
							<div class="entry-meta blind">
							  <span class="entry-author author vcard"><a class="url fn n" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID')))?>"><?php the_author()?></a></span>
							  <span class="entry-date updated" ><a href="<?php echo esc_url( site_url().'/?m='.get_the_date('Ymd'))?>" rel="bookmark"><?php echo esc_html(get_the_date('Y.m.d'))?></a></span>
							</div>
						</header>
					<?php }?>

					<?php if(is_single() && get_option($theme_shortname."_sub_google_cse_use")=='U' && get_option($theme_shortname."_sub_google_cse_postUse")=='U' && get_option($theme_shortname."_sub_google_cse_code")){?>
					  <div id="bbseGoogleCse" style="float:<?php echo (get_option($theme_shortname."_sub_google_cse_align"))?get_option($theme_shortname."_sub_google_cse_align"):"right";?>;width:280px;margin-right:5px;">
						<style>
						#bbseGoogleCse .cse .gsc-control-cse, .gsc-control-cse{padding:1em 0 0 0;}
						</style>
						<?php echo stripslashes(get_option($theme_shortname."_sub_google_cse_code"));?>
					  </div>
					  <div style="display:block;clear:both;height:1px;content:'';"></div>
					<?php }?>

						<div id="<?php if(stripos(get_the_content(), '[bbse_') !== false){echo "BBSE-CONTENT";}else{echo "POST-CONTENT";}?>" class="entry-content">
							<?php 
							the_content();
							if((stripos(get_the_content(), '[naver_maps') !== false || stripos(get_the_content(), '[daum_maps') !== false) && (!get_option($theme_shortname.'_map_addr_info_view') || get_option($theme_shortname.'_map_addr_info_view')=='U')){
								echo "<div id=\"gmaps-box\" class=\"info-block\">
								<p>주소 :  <span>".get_option($theme_shortname.'_map_daum_addr')."<br />".get_option($theme_shortname.'_map_daum_infomation')."</span></p>
								</div>";
							}
							?>
						</div>
						<?php 
						if(bbse_check_view_comment(get_the_ID()) || is_single()){
							bbse_counting_hit($post->ID);
						?>
						<p class="share-sns">
							<?php 
								global $theme_shortname; 
								if(get_option($theme_shortname.'_sns_share_twitter')=='U'){?>
									<a class="share-btn twitter" data-sns="twitter" href="//twitter.com/share?url=<?php echo get_permalink(get_the_ID())?>&amp;text=<?php echo single_post_title()?>" target="_blank" title="트위터에 공유하기"><img src="<?php echo get_template_directory_uri()?>/images/icon_twitter.png" alt="twitter" /></a>
							<?php
								}
								if(get_option($theme_shortname.'_sns_share_facebook')=='U'){?>
								<a class="share-btn facebook" data-sns="facebook" href="//www.facebook.com/sharer.php?u=<?php echo get_permalink(get_the_ID())?>&amp;p[title]=<?php echo single_post_title()?>" target="_blank" title="페이스북에 공유하기" >페이스북에 공유하기</a>
							<?php
								}
								if(get_option($theme_shortname.'_sns_share_hms')=='U'){?>
								<a class="share-btn hms" data-sns="hms" href="//hyper-message.com/hmslink/sendurl?url=<?php echo get_permalink(get_the_ID())?>" target="_blank" title="HMS 공유하기">HMS 공유하기</a>
							<?php }	
								if(get_option($theme_shortname.'_sns_share_googleplus')  == 'U'){?>
								<a class="share-btn googleplus" href="https://plus.google.com/share?url=<?php echo home_url(); ?><?php echo $_SERVER['REQUEST_URI']?>&t=<?php single_post_title(); ?>" target="_blank" title="구글플러스에 공유하기">구글플러스</a>
							<?php }
								$the_thumbnail = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID(), 'thumbnail') );; // 특성 이미지
								if(!$the_thumbnail) $the_thumbnail = bbse_post_first_image();                       // 본문 첫 이미지

								if(get_option($theme_shortname.'_sns_share_pinterest')  == 'U'){?>
								<a class="share-btn pinterest" data-sns="pinterest" data-surl="<?php echo home_url(); ?><?php echo $_SERVER['REQUEST_URI']?>" data-simg="<?php echo $the_thumbnail;?>" data-stxt="<?php single_post_title(); ?>" href="#" title="핀터레스트에 공유하기">핀터레스트</a>
							<?php }?>

						<?php if(wp_is_mobile()){?>
							<?php if(get_option($theme_shortname.'_sns_share_kTalk')=='U' && get_option($theme_shortname.'_sns_share_kakao_js_appkey')){?>
							  <a class="mobile kakaotalk kakaotalk-link" href="javascript:;" data-key="<?php echo get_option($theme_shortname.'_sns_share_kakao_js_appkey')?>" data-domain="<?php echo get_permalink(get_the_ID());?>" data-lable="<?php bloginfo('name'); ?>" data-image="<?php echo bbse_post_first_image()?>" data-msg="<?php echo get_the_title();?>" title="카카오톡 공유하기">카카오톡 공유하기</a>
							  <?php }
							  if(get_option($theme_shortname.'_sns_share_kStory')=='U'){?>
							  <a class="mobile kakaostory" href="javascript:onclick=shareKakaoStory('<?php echo home_url(); ?><?php echo $_SERVER['REQUEST_URI']?>', '<?php echo get_the_title();?>');" title="카카오스토리 공유하기">카카오스토리 공유하기</a>
							  <?php }?>
						<?php }?>

							<?php if(wp_is_mobile() && get_option($theme_shortname.'_sns_share_kTalk')=='U' && get_option($theme_shortname.'_sns_share_kakao_js_appkey')){?>
							<script>
								jQuery(document).ready(function(){
									if (jQuery('.kakaotalk-link').size() > 0){
									  var $key    = jQuery('.kakaotalk-link').data('key');
									  var $domain = jQuery('.kakaotalk-link').data('domain');
									  var $lable  = jQuery('.kakaotalk-link').data('lable');
									  var $image  = jQuery('.kakaotalk-link').data('image');
									  var $msg    = jQuery('.kakaotalk-link').data('msg');

									  Kakao.init($key);
									  shareKakaoTalk($lable, $image, $msg, $domain);
									}
								});
							</script>
							<?php }?>
						</p>
						<dl class="tag-label">
							<dd>
							  <?php
							  $tag_list = get_the_tag_list( '', ', ' );
							  if ( $tag_list ) { ?>
								<div class="tag-links"><span class="tags-label">TAG</span><?php echo $tag_list; ?></div>
							  <?php } ?>
							</dd>
						</dl>
						<p class="entry-footer entry-meta">
							<?php bbse_the_posted_on()?>
						</p>

						<?php bbse_post_goods_display($post->ID); //관련 상품 노출 ?>

						<?php }?>

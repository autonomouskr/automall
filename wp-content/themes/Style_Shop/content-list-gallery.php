<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
?>
			<div id="post-<?php the_ID(); ?>" <?php post_class('img-type'); ?>>
							<ul>
              <?php
              if (have_posts())
              {
                while ( have_posts() )
                {
                  the_post();
                  $the_thumbnail   = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID(), 'thumbnail') );; // 특성 이미지
                  $first_img       = bbse_post_first_image();                       // 본문 첫 이미지

				  $liBoxUrl="";
				  if($the_thumbnail){
					  $thumbnail = '<img class="thumbnail" src="'.$the_thumbnail.'" alt="'.get_the_title().'" />';
					  $liBoxUrl=$the_thumbnail;
				  }
                  elseif ($first_img &&  $first_img != '/images/default.jpg'){
                    $thumbnail = '<img class="thumbnail" src="'.$first_img.'" alt="'.get_the_title().'" />';
					$liBoxUrl=$first_img;
				  }
                  else $thumbnail=false;
              ?>
								<li>
                <?php if ($thumbnail){?>
									<div class="thumb">
                    <?php echo $thumbnail?>
										<span class="bg"></span>
										<span class="btn">
											<a href="<?php echo $liBoxUrl;?>" data-lightbox="list-set" data-title="<?php the_title_attribute();?> <a href='<?php the_permalink();?>' title='[<?php the_title_attribute();?>] 자세히보기'><img src='<?php echo bloginfo('template_url');?>/lightbox/img/lightbox_link.png' alt='<?php the_title_attribute();?> 링크'></a>" class="zoom"><span>이미지보기</span></a>
											<a href="<?php the_permalink();?>" class="link"><span>상세보기</span></a>
										</span>
									</div>
                <?php }?>
									<div class="gallery-list-title">
										<a href="<?php the_permalink();?>" title="<?php the_title_attribute();?>"><?php echo get_the_title();?></a>
									</div>
									<em> <?php if (get_the_category_list()) printf('<span>%1$s</span>', get_the_category_list( ', ' ));?></em>
									<div class="text">
										<?php bbse_the_excerpt(250)?>
									</div>
								</li>
              <?php
                } //endwhile
              } //endif
              wp_reset_query();
              ?>
							</ul>
						</div>
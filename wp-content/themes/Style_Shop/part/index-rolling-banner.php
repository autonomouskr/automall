				<!-- 메인페이지 목마 -->
				<!-- SIZE : 860px by 420px -->
				<?php global $theme_shortname?>
				<div id="mainBanner" class="main_section slide_banner">
					<ul class="slides">
						<?php if(get_option($theme_shortname."_basic_main_slide_use_1")=="U" && get_option($theme_shortname."_basic_main_slide_img_1")!=""){?>
						<li>
							<a href="<?php echo get_option($theme_shortname."_basic_main_slide_url_1")?get_option($theme_shortname."_basic_main_slide_url_1"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_main_slide_url_1_window")?>"><img src="<?php echo get_option($theme_shortname."_basic_main_slide_img_1")?>" alt="<?php echo get_option($theme_shortname."_basic_main_slide_excerpt_1")?>" /></a>
						</li>
						<?php }?>
						<?php if(get_option($theme_shortname."_basic_main_slide_use_2")=="U" && get_option($theme_shortname."_basic_main_slide_img_2")!=""){?>
						<li>
							<a href="<?php echo get_option($theme_shortname."_basic_main_slide_url_2")?get_option($theme_shortname."_basic_main_slide_url_2"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_main_slide_url_2_window")?>"><img src="<?php echo get_option($theme_shortname."_basic_main_slide_img_2")?>" alt="<?php echo get_option($theme_shortname."_basic_main_slide_excerpt_2")?>" /></a>
						</li>
						<?php }?>
						<?php if(get_option($theme_shortname."_basic_main_slide_use_3")=="U" && get_option($theme_shortname."_basic_main_slide_img_3")!=""){?>
						<li>
							<a href="<?php echo get_option($theme_shortname."_basic_main_slide_url_3")?get_option($theme_shortname."_basic_main_slide_url_3"):"javascript:void(0);"?>" target="<?php echo get_option($theme_shortname."_basic_main_slide_url_3_window")?>"><img src="<?php echo get_option($theme_shortname."_basic_main_slide_img_3")?>" alt="<?php echo get_option($theme_shortname."_basic_main_slide_excerpt_3")?>" /></a>
						</li>
						<?php }?>
					</ul>
					<div class="carouselTab">
						<div class="notchSlider"></div>
						<ul>
							<?php if(get_option($theme_shortname."_basic_main_slide_use_1")=="U" && get_option($theme_shortname."_basic_main_slide_img_1")!=""){?>
							<li><a href="javascript:void();"><?php echo get_option($theme_shortname."_basic_main_slide_excerpt_1")?></a></li>
							<?php }?>
							<?php if(get_option($theme_shortname."_basic_main_slide_use_2")=="U" && get_option($theme_shortname."_basic_main_slide_img_2")!=""){?>
							<li><a href="javascript:void();"><?php echo get_option($theme_shortname."_basic_main_slide_excerpt_2")?></a></li>
							<?php }?>
							<?php if(get_option($theme_shortname."_basic_main_slide_use_3")=="U" && get_option($theme_shortname."_basic_main_slide_img_3")!=""){?>
							<li><a href="javascript:void();"><?php echo get_option($theme_shortname."_basic_main_slide_excerpt_3")?></a></li>
							<?php }?>
						</ul>
					</div>

					<script type="text/javascript">
					$(document).ready(function(){
						if($('#mainBanner .slides li').size() > 0) {
							$('.carouselTab ul li').eq(0).addClass('active');
							$('#mainBanner').flexslider({
								animation: "slide",
								animationLoop: true,
								slideshow: true,
								controlNav: true,
								directionNav: true,
								manualControls: ".carouselTab li",
								itemWidth: 860,
								itemMargin: 0,
								minItems: 1,
								maxItems: 8,
								start: function(){
									notchPosition('static');
								},
								after: function(){
									notchSlider();
								}
							});
						}
					});

					// 메인 슬라이더 노치 슬라이딩
					var notchSlider = function(){
						//슬라이드 이동 후 활성 슬라이드 판별 및 탭 이동
						var $slideCount     = $('#mainBanner .slides li').size();

						var chkValue        = false;
						var nextIndex       = false;

						// 마음이 불편한 코드
						var $nowSlideMatrix = $('#mainBanner .slides').css('transform');
						var $arrMatrix      = $nowSlideMatrix.split(',');
						if ( /^matrix\(/gi.test($nowSlideMatrix)   == true && chkValue == false ) { var matrixValue = $arrMatrix[4]*-1;  chkValue = true; } //Standard
						if ( /^matrix3d\(/gi.test($nowSlideMatrix) == true && chkValue == false ) { var matrixValue = $arrMatrix[12]*-1; chkValue = true; } //MSIE 10, 11
						if ( chkValue == false )   { var matrixValue = $('#mainBanner .slides').css('margin-left').replace('px', '')*-1; chkValue = true; } //MSIE 9

						if ( chkValue == true )
						{
							nextIndex = matrixValue/860;

							$('.carouselTab ul li').removeClass('active',function(){
								$('.carouselTab ul li').eq(nextIndex).addClass('active');
							});
							notchPosition('animate');
						}
						else // 에러라고 판단되면 노치와/활성화 클래스 삭제
						{
							$('.notchSlider').remove();
							$('.carouselTab ul li').removeClass('active');
						}
					}
					// 노치 위치 선정하고 이동하기
					var notchPosition = function(type){
						var $activePosition = $('.carouselTab ul li.active').position();               //위치
						var $activeCenter   = $('.carouselTab ul li.active a').width()/2;              //중심점
						var $leftPosition   = $activePosition.left+$activeCenter*1;                    //최종중심점

						if (type == 'static')
							$('.notchSlider').css('left',$leftPosition).fadeIn('slow');                  //활성화
						else if (type == 'animate')
							$('.notchSlider').stop(true,true).animate({left:$leftPosition},250,'swing'); //애니메이션
					}
					</script>
					</div><!--//메인 배너 -->
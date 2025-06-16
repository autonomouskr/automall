<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/
?>
	<!-- N: 테스트용입니다. 실제 개발시에는 포함되지 않습니다.-->
	<div id="testControl">
	<div class="ui-cotrol">
		<button class="ui-toggle" type="button" title="메뉴보기"></button>
		<div class="ui-wrap">
		<h2>빠른설정</h2>
		<ul class="">
			<li>
				<dl>
					<dt>테마색상 <span style='font-weight:normal;font-size:11px;color:#C46181;'>(기본값 : #C46181)</span></dt>
					<dd class="height30px">
						<?php $id_str = get_option($theme_shortname."_color_main_theme") ? get_option($theme_shortname."_color_main_theme") : $baseColor;?>
						<input type='text' name='color_main_theme' id='color_main_theme' class='colpick' value='<?php echo (get_option($theme_shortname."_color_main_theme"))?get_option($theme_shortname."_color_main_theme"):"#C46181";?>' style='width:60px;height:20px;text-align:right;'>
					</dd>
				</dl>
			</li>
			<li>
				<dl>
					<dt>왼쪽 쇼핑 카테고리 
						<span class="optCheck" data-use="<?php echo  get_option($theme_shortname."_layout_left_category");?>" data-container="layout_left_category" data-target="layoutLeftCategory_bold" style="cursor:pointer">
							<img src="<?php echo bloginfo('template_url')?>/images/switch_<?php echo (get_option($theme_shortname."_layout_left_category"))?get_option($theme_shortname."_layout_left_category"):"N";?>.png" alt="왼쪽 쇼핑 카테고리"/>
						</span>
						<input type="hidden" name="layout_left_category" id="layout_left_category" value="<?php echo get_option($theme_shortname."_layout_left_category")?>"  />
					</dt>
					<dd>
						<div id="layoutLeftCategory_bold" style="height:25px;display:<?php echo (get_option($theme_shortname."_layout_left_category")=='Y')?"block":"none";?>;">- 카테고리 글씨 : <input type="checkbox" name="layout_left_category_bold" id="layout_left_category_bold" value="bold" <?php echo (get_option($theme_shortname."_layout_left_category_bold")=='bold')?"checked='checked'":"";?>>진하게 표시</div>
					</dd>
				</dl>
			</li>
			<li>
				<dl>
					<dt>가격검색 
						<span class="optCheck" data-use="<?php echo  get_option($theme_shortname."_layout_left_price_search");?>" data-container="layout_left_price_search" data-target="" style="cursor:pointer">
							<img src="<?php echo bloginfo('template_url')?>/images/switch_<?php echo (get_option($theme_shortname."_layout_left_price_search"))?get_option($theme_shortname."_layout_left_price_search"):"N";?>.png" alt="가격검색" />
						</span>
						<input type="hidden" name="layout_left_price_search" id="layout_left_price_search" value="<?php echo get_option($theme_shortname."_layout_left_price_search")?>"  />					
					</dt>
				</dl>
			</li>
			<li>
				<dl>
					<dt>오늘만의 특가 
						<span class="optCheck" data-use="<?php echo  get_option($theme_shortname."_layout_left_today_sale");?>" data-container="layout_left_today_sale" data-target="" style="cursor:pointer">
							<img src="<?php echo bloginfo('template_url')?>/images/switch_<?php echo (get_option($theme_shortname."_layout_left_today_sale"))?get_option($theme_shortname."_layout_left_today_sale"):"N";?>.png" alt="오늘만의 특가" />
						</span>
						<input type="hidden" name="layout_left_today_sale" id="layout_left_today_sale" value="<?php echo get_option($theme_shortname."_layout_left_today_sale")?>"  />					
					</dt>
				</dl>
			</li>
			<li>
				<dl>
					<dt>핫아이템 
						<span class="optCheck" data-use="<?php echo  get_option($theme_shortname."_layout_left_hot_item");?>" data-container="layout_left_hot_item" data-target="" style="cursor:pointer">
							<img src="<?php echo bloginfo('template_url')?>/images/switch_<?php echo (get_option($theme_shortname."_layout_left_hot_item"))?get_option($theme_shortname."_layout_left_hot_item"):"N";?>.png" alt="핫아이템" />
						</span>
						<input type="hidden" name="layout_left_hot_item" id="layout_left_hot_item" value="<?php echo get_option($theme_shortname."_layout_left_hot_item")?>"  />					
					</dt>
				</dl>
			</li>
			<li>
				<dl>
					<dt>입금계좌정보 
						<span class="optCheck" data-use="<?php echo  get_option($theme_shortname."_layout_left_bank_info");?>" data-container="layout_left_bank_info" data-target="" style="cursor:pointer">
							<img src="<?php echo bloginfo('template_url')?>/images/switch_<?php echo (get_option($theme_shortname."_layout_left_bank_info"))?get_option($theme_shortname."_layout_left_bank_info"):"N";?>.png" alt="입금계좌정보" />
						</span>
						<input type="hidden" name="layout_left_bank_info" id="layout_left_bank_info" value="<?php echo get_option($theme_shortname."_layout_left_bank_info")?>"  />					
					</dt>
				</dl>
			</li>
			<li>
				<dl>
					<dt>최근 본 상품 
						<span class="optCheck" data-use="<?php echo  get_option($theme_shortname."_layout_right_last_goods");?>" data-container="layout_right_last_goods" data-target="" style="cursor:pointer">
							<img src="<?php echo bloginfo('template_url')?>/images/switch_<?php echo (get_option($theme_shortname."_layout_right_last_goods"))?get_option($theme_shortname."_layout_right_last_goods"):"N";?>.png" alt="최근 본 상품" />
						</span>
						<input type="hidden" name="layout_right_last_goods" id="layout_right_last_goods" value="<?php echo get_option($theme_shortname."_layout_right_last_goods")?>"  />					
					</dt>
				</dl>
			</li>

			<li>
				<button type="button" class="ui-submit"><span>저장</span></button>
			</li>
		</ul>
		</div>
		<style type="text/css">
			.optCheck{float:right;margin-right:20px;}
			.optCheck img{margin-top:-5px;}
			.ui-cotrol {position:fixed;top:50px;right:-230px;z-index:900;width:268px;
				transition:all .3s;
				-webkit-transition:all .3s;
				-moz-transition:all .3s;
				-o-transition:all .3s;
			}
			.ui-cotrol.open {right:0;}
			.ui-cotrol .ui-toggle {display:block;float:left;width:38px;height:36px;text-indent:-999px;background:#000 url(<?php bloginfo('template_url'); ?>/images/_gear.png) 50% 50% no-repeat;
				border-top-left-radius:5px;
				-webkit-border-top-left-radius:5px;
				-moz-border-top-left-radius:5px;
				-o-border-top-left-radius:5px;
				border-bottom-left-radius:5px;
				-webkit-border-bottom-left-radius:5px;
				-moz-border-bottom-left-radius:5px;
				-o-border-bottom-left-radius:5px;
			}
			.ui-cotrol .ui-wrap {float:right;width:230px;}
			.ui-cotrol .ui-wrap h2 {display:block;height:36px;line-height:36px;text-align:center;color:#fff;background-color:#000;font-size:15px;font-weight:700;}
			.ui-cotrol .ui-wrap > ul {border-left:1px solid #BFBFBF;border-bottom:1px solid #919191;background-color:#EFF0EF;
				border-bottom-left-radius:5px;
				-webkit-border-bottom-left-radius:5px;
				-moz-border-bottom-left-radius:5px;
				-o-border-bottom-left-radius:5px;
				box-shadow:0 1px 0 #777;
				-webkit-box-shadow:0 1px 0 #777;
				-moz-box-shadow:0 1px 0 #777;
				-o-box-shadow:0 1px 0 #777;
			}
			.ui-cotrol .ui-wrap > ul > li:nth-child(even) {background-color:#FFF;}
			.ui-cotrol .ui-wrap > ul > li:last-child {padding:15px 15px 20px;
				border-bottom-left-radius:5px;
				-webkit-border-bottom-left-radius:5px;
				-moz-border-bottom-left-radius:5px;
				-o-border-bottom-left-radius:5px;
			}
			.ui-cotrol .ui-wrap > ul > li .ui-submit {width:197px;height:27px;border:1px solid #242424;color:#fff;font-weight:700;font-size:12px;text-align:center;
				border-radius:5px;
				-webkit-border-radius:5px;
				-moz-border-radius:5px;
				-o-border-radius:5px;
				box-shadow:0 1px 1px #E5E5E5;
				-webkit-box-shadow:0 1px 1px #E5E5E5;
				-moz-box-shadow:0 1px 1px #E5E5E5;
				-o-box-shadow:0 1px 1px #E5E5E5;
				background: #444444; /* Old browsers */
				background: -moz-linear-gradient(top,  #444 0%, #2c2c2c 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#444), color-stop(100%,#2c2c2c)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top,  #444 0%,#2c2c2c 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top,  #444 0%,#2c2c2c 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top,  #444 0%,#2c2c2c 100%); /* IE10+ */
				background: linear-gradient(to bottom,  #444 0%,#2c2c2c 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#444', endColorstr='#2c2c2c',GradientType=0 ); /* IE6-9 */
			}
			.ui-cotrol .ui-wrap > ul > li .ui-submit span {display:inline-block;background: url(<?php bloginfo('template_url'); ?>/images/_check.png) -15px -11px no-repeat;padding-left:16px;}
			.ui-cotrol .ui-wrap ul dl {padding:10px;}
			.ui-cotrol .ui-wrap > ul li dl dt {/*padding-bottom:10px;*/padding-top:10px;margin-left:10px;height:27px;color:#666;font-size:13px;font-weight:700;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd {margin-left:20px;height:auto;overflow:visible;width:197px;/*min-height:27px;*/} /* 2014-05-08  */
			.ui-cotrol .ui-wrap > ul > li > dl > dd:nth-child(2) {height:auto;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd.hover {position:relative;height:auto;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd.hover .select-list{position:absolute;top:0;left:0;width:100%;z-index:9999;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd > ul {min-height:27px;background: url(<?php bloginfo('template_url'); ?>/images/_selectbox.png) 0 0 no-repeat;}
			.ui-cotrol .ui-wrap > ul > li > dl dd > ul > li {display:none;height:27px;}
			.ui-cotrol .ui-wrap > ul > li > dl dd.hover > ul > li {
				height:26px;
				border:1px solid #AEAEAE;
				background-color:#FFF;
				margin-top:-1px;
			}
			.ui-cotrol .ui-wrap > ul > li > dl dd.hover > ul > li:hover{
				background-color:#f6f6f6;
			}
			.ui-cotrol .ui-wrap > ul > li > dl dd.hover > ul > li:first-child {
				border-top-left-radius:5px;
				border-top-right-radius:5px;
			}
			.ui-cotrol .ui-wrap > ul > li > dl dd.hover > ul > li:last-child {
				border-bottom-left-radius:5px;
				border-bottom-right-radius:5px;
			}
			.ui-cotrol .ui-wrap > ul > li > dl dd > ul > li button {width:100%;text-align:left;height:25px;line-height:23px;font-size:11px;color:#000;text-indent:20px;font-weight:700;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd .active {display:block;}
			/* .ui-cotrol .ui-wrap > ul > li > dl > dd:after {content:'';position:absolute;top:10px;right:10px;display:block;width:6px;height:6px;border:3px solid transparent;border-top:3px solid #868585;} */
			
			.ui-cotrol .ui-wrap > ul > li > dl > dd dl {padding:10px 0 0 0;overflow:hidden;width:210px;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd.hover dl {padding:37px 0 0 0;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd dl dd {float:left;display:block;margin:0 8px 7px 1px;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd dl dd .bg-img-btn {position:relative;border:1px solid #fff;background-color:#F3F3F3;padding:5px 5px 15px 5px;
				border-radius:4px;
				-webkit-border-radius:4px;
				-moz-border-radius:4px;
				-o-border-radius:4px;
				box-shadow:inset 2px 2px 2px #aaa;
				-webkit-box-shadow:inset 2px 2px 2px #aaa;
				-moz-box-shadow:inset 2px 2px 2px #aaa;
				-o-box-shadow:inset 2px 2px 2px #aaa;
			}
			.ui-cotrol .ui-wrap > ul > li > dl > dd dl dd .bg-img-btn img{width:20px;height:20px;}
			.ui-cotrol .ui-wrap > ul > li > dl > dd dl dd .bg-img-btn:after {position:absolute;bottom:6px;left:13px;display:block;content:'';width:5px;height:5px;background-color:#DDE2E2;
				border-radius:50%;
				-webkit-border-radius:50%;
				-moz-border-radius:50%;
				-o-border-radius:50%;
				box-shadow:inset 1px 1px 2px #ccc;
				-webkit-box-shadow:inset 1px 1px 2px #ccc;
				-moz-box-shadow:inset 1px 1px 2px #ccc;
				-o-box-shadow:inset 1px 1px 2px #ccc;
			}
			.ui-cotrol .ui-wrap > ul > li > dl > dd dl dd .bg-img-btn.active {border:1px solid #AEAEAE;background-color:#FFF;
				box-shadow:inset -1px -1px 2px #ccc;
				-webkit-box-shadow:inset -1px -1px 2px #ccc;
				-moz-box-shadow:inset -1px -1px 2px #ccc;
				-o-box-shadow:inset -1px -1px 2px #ccc;
			}
			.ui-cotrol .ui-wrap > ul > li > dl > dd dl dd .bg-img-btn.active:after {background-color:#111;
				box-shadow:inset 1px 1px 2px #999;
				-webkit-box-shadow:inset 1px 1px 2px #999;
				-moz-box-shadow:inset 1px 1px 2px #999;
				-o-box-shadow:inset 1px 1px 2px #999;
			}
			.ui-cotrol .ui-wrap > ul > li > dl > dd.height30px{height:30px;margin-left:20px;}

			.layout-cotrol {position:fixed;top:0;right:0;z-index:900;width:310px;border-left:2px solid #2F2F2F;border-bottom:2px solid #2F2F2F;background-color:#ccc;color:#111;padding:10px;border-bottom-left-radius:10px;-webkit-transition: all 0.3s ease-out;-moz-transition: all 0.3s ease-out;-o-transition: all 0.3s ease-out;-ms-transition: all 0.3s ease-out;transition: all 0.3s ease-out;opacity:0;}
			.layout-cotrol h2 {font-weight:700;font-size:16px;}
			.layout-cotrol:hover {-webkit-transition: all 0.3s ease-out;-moz-transition: all 0.3s ease-out;-o-transition: all 0.3s ease-out;-ms-transition: all 0.3s ease-out;transition: all 0.3s ease-out;opacity:1;}
			.layout-cotrol button {border:1px solid #333;display:inline-block;padding:5px 8px;border-radius:5px;background-color:#F6F6F6;margin:2px;}
			.layout-cotrol button.active {border:1px solid red;background-color:#FFF;}
		</style>
		<script type="text/javascript">
			var site_type="<?php echo get_option($theme_shortname.'_basic_site_type')?>";
			var site_background="<?php echo get_option($theme_shortname.'_basic_site_background')?>";
			var sidebar_position="<?php echo (!get_option($theme_shortname.'_sub_sidebar_position'))?"left":get_option($theme_shortname.'_sub_sidebar_position')?>";

			jQuery(document).ready(function() {

	jQuery('.colpick').each( function() {
		jQuery('.colpick').minicolors({
			control: jQuery(this).attr('data-control') || 'hue',
			defaultValue: jQuery(this).attr('data-defaultValue') || '',
			inline: jQuery(this).attr('data-inline') === 'true',
			letterCase: jQuery(this).attr('data-letterCase') || 'lowercase',
			opacity: jQuery(this).attr('data-opacity'),
			position: jQuery(this).attr('data-position') || 'bottom left',
			change: function(hex, opacity) {
				var log;
				try {
					log = hex ? hex : 'transparent';
					if( opacity ) log += ', ' + opacity;
					console.log(log);
				} catch(e) {}
			},
			theme: 'default'
		});

	});


			    //사용함/사용안함
			    jQuery('span.optCheck').click(function(){
				  var $status    = jQuery(this).data('use');
				  var $container = jQuery(this).data('container');
				  var $target    = jQuery(this).data('target');
				  var $type      = jQuery(this).data('type');

				  if ($status == 'Y'){// 활성이면 비활성시키고 TR 감춤
				    jQuery('#'+$container).val('N');
				    jQuery(this).data('use','N');
				    var $btn = jQuery(this).find('img').attr('src').replace("Y", "N");
				    jQuery(this).find('img').attr('src', $btn);
				    if( jQuery('#'+$target)) jQuery('#'+$target).css('display','none');
				  }
				  else if ($status == 'N'){ // 비활성이면 활성시키고 TR 보여줌
				    jQuery('#'+$container).val('Y');
				    jQuery(this).data('use','Y');
				    var $btn = jQuery(this).find('img').attr('src').replace("N.","Y.");
				    jQuery(this).find('img').attr('src', $btn);

				    if( jQuery('#'+$target)){
					  if ( $type = 'div' ) jQuery('#'+$target).css('display','block');
					  else 	jQuery('#'+$target).css('display','table-row');
				    }
				  }
			    });

				jQuery('.ui-cotrol .ui-toggle').bind('click', function() {
					jQuery('.ui-cotrol').toggleClass('open');
				})

				jQuery('.ui-cotrol button.ui-submit').bind('click', function() {
					var color_main_theme=jQuery("#color_main_theme").val();
					var layout_left_category=jQuery("#layout_left_category").val();
					var layout_left_category_bold=jQuery(':checkbox[name="layout_left_category_bold"]:checked').val();
					if(layout_left_category_bold==undefined) layout_left_category_bold='';
					var layout_left_price_search=jQuery("#layout_left_price_search").val();
					var layout_left_today_sale=jQuery("#layout_left_today_sale").val();
					var layout_left_hot_item=jQuery("#layout_left_hot_item").val();
					var layout_left_bank_info=jQuery("#layout_left_bank_info").val();
					var layout_right_last_goods=jQuery("#layout_right_last_goods").val();

					if(confirm('현재 설정을 저장하시겠습니까?    ')){
						var tUrl=common_var.goods_template_url+"/proc/scroll_option_control.exec.php";

						jQuery.ajax({
							type: "post",
							async: false,
							url: tUrl,
							data: {color_main_theme:color_main_theme, layout_left_category:layout_left_category, layout_left_category_bold:layout_left_category_bold, layout_left_price_search:layout_left_price_search, layout_left_today_sale:layout_left_today_sale, layout_left_hot_item:layout_left_hot_item, layout_left_bank_info:layout_left_bank_info, layout_right_last_goods:layout_right_last_goods},
							success: function(data){
								var result = data;
								//alert(result);
								if(result=='success'){ 
									jQuery('.ui-cotrol').toggleClass('open');
									location.reload();
								}
								else{
									alert("서버와의 통신이 실패했습니다.");
								}
							},
							error: function(data, status, err){
								alert("서버와의 통신이 실패했습니다.");
							}
						});
					}
				})
			})
		</script>
	</div>
  </div>
  <!-- N: 테스트용입니다. 실제 개발시에는 포함되지 않습니다.-->
<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_sub_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>

  <!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='sub' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='upload_target_img' id='upload_target_img' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_sub_preOpen' id='<?php echo $theme_shortname?>_sub_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">서브화면 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_sub_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">

<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use   = get_option($theme_shortname."_sub_goods_view_use_left_sidebar")=='U'?'yes':'no';
		$show = get_option($theme_shortname."_sub_goods_view_use_left_sidebar")=='U'?'':'style="display:none"';
      ?>
	<li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">사이드바 설정</div>
		<div class="item">
			<div class="slideItemsTitle">사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_sub_goods_view_use_left_sidebar" data-target="basic_use_bottom_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_sub_goods_view_use_left_sidebar" id="<?php echo $theme_shortname?>_sub_goods_view_use_left_sidebar" value='<?php echo get_option($theme_shortname."_sub_goods_view_use_left_sidebar")?>'  />
			</div>
			<span class="desc2">상품 상세보기 화면에 사이드바 사용을 설정합니다.</span><br/>
			<span class="desc2">사이드바 사용 시 메인환면 설정 > 레이아웃 설정에서 설정한 레이아웃이 적용됩니다.</span>
		</div>
	</li>
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">스킨설정</div>
		<div class="item">
			<span class="desc2">회원 관련 페이지 (로그인, 아이디찾기, 비밀번호찾기, 회원가입/수정, 회원탈퇴)</span><br/>
			<span class="desc2">주문관련 페이지 (장바구니, 관심상품, 주문서 작성, 주문완료)</span><br/>
			<!-- <span class="desc2">마이페이지 (주문/배송조회, 취소/반품신청조회, 쿠폰내역, 적립금내역, 나의1:1문의)</span> -->
			<span class="desc2">마이페이지 (주문/배송조회, 취소/반품신청조회, 쿠폰내역, 나의1:1문의, 재고관리)</span>
			</span>
			<dl>
				<dt>스킨</dt>
				<dd>
					<select name="<?php echo $theme_shortname?>_sub_skin_name" id="<?php echo $theme_shortname?>_sub_skin_name" onchange="btype_check('skinname');" style="height:22px;">
						<?php
						$skin_path = BBSE_COMMERCE_THEME_ABS_PATH."/skin/"; 
						$files = array(); 
						
						if($dh = opendir($skin_path)){ 
							while(($read = readdir($dh)) !== false){
								if(is_dir($path.$read)) continue; 
								$files[] = $read; 
							} 
							closedir($dh); 
						} 
						sort($files); 

						foreach($files as $name){ 
							if($name == $data->skinname) $sSelect = "selected style='color:#ff0000;'";
							else $sSelect = "";
							echo "<option value='$name' $sSelect>$name</option>";
						}
						?>
					</select>
					<!--button type="button" class="button_s" onclick="preview();">미리보기</button-->
				</dd>
			</dl>
			<!-- <dl>
				<dt>주색</dt>
				<dd>
					<?php $baseColor = '#C46181'?>
					<?php $id_str = get_option($theme_shortname."_sub_skin_color") ? get_option($theme_shortname."_sub_skin_color") : $baseColor;?>
					<input type='text' name='<?php echo $theme_shortname?>_sub_skin_color' id='<?php echo $theme_shortname?>_sub_skin_color' class='colpick' value='<?php echo $id_str?>' style='width:80px;height:20px;text-align:right;'>

				</dd>
			</dl> -->
		</div>
	</li>
<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use    = get_option($theme_shortname."_sub_use_category_banner")=='U'?'yes':'no';
        $button = get_option($theme_shortname."_sub_use_category_banner")=='U'?'사용중':'비활성됨';
        $show   = get_option($theme_shortname."_sub_use_category_banner")=='U'?'':'style="display:none"';
      ?>
      <li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"> 카테고리 배너 설정</div>
		<div class="item">
			<div class="slideItemsTitle">사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_sub_use_category_banner" data-target="sub_use_category_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_sub_use_category_banner" id="<?php echo $theme_shortname?>_sub_use_category_banner" value='<?php echo get_option($theme_shortname."_sub_use_category_banner")?>'  />
			</div>
			<div class="sub_use_category_banner" <?php echo $show?>>
			<span class="desc2">이미지 사이즈는 가로 860xp 입니다. (세로 제약은 없음)</span><br />
			  <dl>
				<dt>이미지</dt>
				<dd>
				  <input id='<?php echo $theme_shortname?>_sub_category_banner_image_1' name='<?php echo $theme_shortname?>_sub_category_banner_image_1' type='text' value='<?php echo get_option($theme_shortname."_sub_category_banner_image_1")?>' style='width:70%;' />&nbsp;<input class='button-secondary' onClick="upload_img('sub_category_banner_image_1');" type='button' value='찾아보기' style='height:23px;font-size:11px;' />
				  <span style="display:inline-block;">
				  <?php
					if(get_option($theme_shortname."_sub_category_banner_image_1"))
					{
					  echo "<a href='".get_option($theme_shortname."_sub_category_banner_image_1")."' data-lightbox='sub_category_banner_image_1'><img src='".get_option($theme_shortname."_sub_category_banner_image_1")."' class='fileimg'></a>";
					}
				  ?>
				  </span>
				</dd>
			  </dl>
			  <dl>
				<dt>링크</dt>
				<dd>
				  <input type='text' name='<?php echo $theme_shortname?>_sub_category_banner_link_1' id='<?php echo $theme_shortname?>_sub_category_banner_link_1' value='<?php echo get_option($theme_shortname."_sub_category_banner_link_1")?>' style='width:70%;'>&nbsp;
				  <input type="radio" name="<?php echo $theme_shortname?>_sub_category_banner_link_1_window" id="<?php echo $theme_shortname?>_sub_category_banner_link_1_window_self" value='_self' <?php echo (get_option($theme_shortname."_sub_category_banner_link_1_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sub_category_banner_link_1_window_self">현재창</label>
				  <input type="radio" name="<?php echo $theme_shortname?>_sub_category_banner_link_1_window" id="<?php echo $theme_shortname?>_sub_category_banner_link_1_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_sub_category_banner_link_1_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_sub_category_banner_link_1_window_blank">새창</label>
				</dd>
			  </dl>
			</div>
		</div>
      </li><!-- /메인 하단배너 설정 -->
<!-- ******************************************************************************************************************************************************* -->
	<li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"> 리스트형 카테고리</div>
		<div class="item">
			<input type="hidden" name="<?php echo $theme_shortname?>_sub_category_view_list" id="<?php echo $theme_shortname?>_sub_category_view_list" value="<?php echo get_option($theme_shortname."_sub_category_view_list")?>">
			<span class="desc2">리스트형으로 표시 될 카테고리를 선택해 주세요.</span>
			<dl>
			  <dt>카테고리 선택</dt>
			  <dd>
				<?php
				$cateData = bbse_get_custom_list('category');

				for($z=0;$z<sizeof($cateData);$z++){
					$viewGalleryCategory=explode(",",get_option($theme_shortname."_sub_category_view_gallery"));
					$viewListCategory=explode(",",get_option($theme_shortname."_sub_category_view_list"));

					if(in_array($cateData[$z]['id'],$viewListCategory)) $cateCheck="checked";
					else $cateCheck="";

					if(in_array($cateData[$z]['id'],$viewGalleryCategory)) $gateDisabled="disabled";
					else $gateDisabled="";

				?>
					<input type="checkbox" name="category_view_list[]" id="category_view_list[<?php echo $z?>]" onClick="if(this.checked){view_category('list','on','<?php echo $cateData[$z]['id']?>');}else{view_category('list','off','<?php echo $cateData[$z]['id']?>');}" value="<?php echo $cateData[$z]['id']?>" <?php echo $cateCheck?> <?php echo $gateDisabled?> style='border:0px;'><label for="category_view_list[<?php echo $z?>]"><?php echo $cateData[$z]['name']?></label>
				<?php }?>
			  </dd>
			</dl>
		</div>
	</li>
<!-- ******************************************************************************************************************************************************* -->
	<li class="group">
		<div class="itemHeader <?php echo $preOpen2?>"> 갤러리형 카테고리</div>
		<div class="item">
			<input type="hidden" name="<?php echo $theme_shortname?>_sub_category_view_gallery" id="<?php echo $theme_shortname?>_sub_category_view_gallery" value="<?php echo get_option($theme_shortname."_sub_category_view_gallery")?>">
			<span class="desc2">갤러리형으로 표시 될 카테고리를 선택해 주세요.</span>
			<dl>
			  <dt>카테고리 선택</dt>
			  <dd>
				<?php
				for($z=0;$z<sizeof($cateData);$z++){
					$viewGalleryCategory=explode(",",get_option($theme_shortname."_sub_category_view_gallery"));
					$viewListCategory=explode(",",get_option($theme_shortname."_sub_category_view_list"));
					if(in_array($cateData[$z]['id'],$viewGalleryCategory))$cateCheck="checked";
					else $cateCheck="";

					if(in_array($cateData[$z]['id'],$viewListCategory)) $gateDisabled="disabled";
					else $gateDisabled="";
				?>
					<input type="checkbox" name="category_view_gallery[]" id="category_view_gallery[<?php echo $z?>]" onClick="if(this.checked){view_category('gallery','on','<?php echo $cateData[$z]['id']?>');}else{view_category('gallery','off','<?php echo $cateData[$z]['id']?>');}" value="<?php echo $cateData[$z]['id']?>" <?php echo $cateCheck?> <?php echo $gateDisabled?> style='border:0px;'><label for="category_view_gallery[<?php echo $z?>]"><?php echo $cateData[$z]['name']?></label>
				<?php }?>
			  </dd>
			</dl>
		</div>
	</li>
<!-- ******************************************************************************************************************************************************* -->
      <?php
        $use   = get_option($theme_shortname."_goodsrelation_use")=='U'?'yes':'no';
		$show = get_option($theme_shortname."_goodsrelation_use")=='U'?'':'style="display:none"';
      ?>
	<li class="group">
		<div class="itemHeader <?php echo $preOpen2?>">POST 관련상품 노출</div>
		<div class="item">
			<div class="slideItemsTitle">사용여부&nbsp;&nbsp;
				<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_goodsrelation_use" data-target="basic_use_bottom_banner" style="cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				<input type="hidden" name="<?php echo $theme_shortname?>_goodsrelation_use" id="<?php echo $theme_shortname?>_goodsrelation_use" value='<?php echo get_option($theme_shortname."_goodsrelation_use")?>'  />
			</div>
			<span class="desc2">포스트 작성시 관련 상품을 노출여부를 설정합니다.</span><br/>
		</div>
	</li>

<!-- ******************************************************************************************************************************************************* -->
     <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">
            <div class="headerWrap">
              <div class="title">구글 맞춤검색 설정</div>
            </div>
		</div>
		<div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p>(*) [구글 맞춤검색] 에서 맞춤검색의 사이트 등록 및 환경을 설정하세요 (<a href="https://cse.google.co.kr/" target="_blank">https://cse.google.co.kr/</a>)</p>
				<p>(*) 구글 맞춤검색 관리 페이지의 설정-세부정보 항목-'코드가져오기' 버튼을 클릭하여 나타나는 코드를 아래의 '맞춤검색 코드' 부분에 입력합니다.</p>
				<p>(*) 구글 맞춤검색 은 글(포스트) 상세페이지 상단에 노출됩니다.</p>
				<p>(*) 구글 맞춤검색은 글(포스트) 상세페이지 상단에 노출 되며, 글(포스트) 별 노출 여부를 설정할 수 있습니다.</p>
			</div>
			<br />
			<dl style="border-bottom: 0px;">
			<?php
			$use    = get_option($theme_shortname."_sub_google_cse_use")=='U'?'yes':'no';
			$show   = get_option($theme_shortname."_sub_google_cse_use")=='U'?'':'style="display:none"';
			?>
			  <dt>사용여부</td>
				<dd>
					<span class="useCheck" data-use="<?php echo $use?>" data-container="<?php echo $theme_shortname?>_sub_google_cse_use" data-target="googleCES_use" style="margin-left:35px;cursor:pointer"><img src="<?php echo bloginfo('template_url')?>/admin/images/switch_<?php echo $use?>.png" /></span>
				  <input type="hidden" name="<?php echo $theme_shortname?>_sub_google_cse_use" id="<?php echo $theme_shortname?>_sub_google_cse_use" value='<?php echo get_option($theme_shortname."_sub_google_cse_use");?>' style="cursor:pointer" />
			  </dd>
			</dl>
			<div class="googleCES_use" <?php echo $show;?>>
				<dl>
					<dt>맞춤검색 노출</dt>
					<dd>
						<input type="checkbox" name="<?php echo $theme_shortname?>_sub_google_cse_postUse" id="<?php echo $theme_shortname?>_sub_google_cse_postUse" value="U" <?php echo (get_option($theme_shortname."_sub_google_cse_postUse")=='U')?"checked='checked'":"";?> />글(포스트)
						
				<?php if(plugin_active_check('BBSe_Commerce')){?>						
						&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?php echo $theme_shortname?>_sub_google_cse_goodsUse" id="<?php echo $theme_shortname?>_sub_google_cse_goodsUse" value="U" <?php echo (get_option($theme_shortname."_sub_google_cse_goodsUse")=='U')?"checked='checked'":"";?> />상품상세
				<?php }?>
					</dd>
				</dl>

				<dl>
					<dt>맞춤검색 코드</dt>
					<dd>
						<textarea type="textarea" name="<?php echo $theme_shortname?>_sub_google_cse_code" id="<?php echo $theme_shortname?>_sub_google_cse_code" style="width:100%;height:220px;" ><?php echo stripcslashes(get_option($theme_shortname."_sub_google_cse_code"));?></textarea>
					</dd>
				</dl>
				<dl>
					<dt>맞춤검색 바(Bar) 위치</dt>
					<dd>
						<select name="<?php echo $theme_shortname?>_sub_google_cse_align" id="<?php echo $theme_shortname?>_sub_google_cse_align">
							<option value="left" <?php echo (get_option($theme_shortname."_sub_google_cse_align")=='left')?"selected='selected'":"";?>>왼쪽</option>
							<option value="right" <?php echo (!get_option($theme_shortname."_sub_google_cse_align") || get_option($theme_shortname."_sub_google_cse_align")=='right')?"selected='selected'":"";?>>오른쪽</option>
						</select>
					</dd>
				</dl>
			</div>
		</div>
      </li><!-- /구글 맞춤검색 설정-->


<!-- ******************************************************************************************************************************************************* -->
	<!--li>
		<div class="itemsMainTitle">댓글 활성화 페이지&nbsp;&nbsp;&nbsp;</div>
        <span class="desc2">댓글 기능이 적용될 페이지 입니다. (쇼핑 카케고리에서 제공되지 않습니다.)</span>
        <dl>
			<dt>페이지 선택</dt>
			<dd>
			<?php
				$pageData = bbse_get_custom_list('page');
				$displayViewPage = explode(",",get_option($theme_shortname."_sub_comment_enable_page"));
				foreach ($pageData as $k=>$v){
					$pageCheck = in_array($v['id'], $displayViewPage) ? 'checked="checked"':'';
			?>
	            <span style="display:inline-block;padding-right:10px;">
					<input type="checkbox" name="chk_view_page[]" id="chk_view_page[<?php echo $k?>]" class="chk_view_page" onClick="check_category('chk_view_page','<?php echo $theme_shortname?>_sub_comment_enable_page');" value="<?php echo $v['id']?>" <?php echo $pageCheck?> style='border:0px;'><label for="chk_view_page[<?php echo $k?>]"><?php echo $v['name']?></label>
				</span>
			<?php }?>
          </dd>
        </dl>
	</li-->
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('sub');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('sub');">초기화</button>
		</div>
	</div>
	</form>
	<!-- //contents 끝 -->


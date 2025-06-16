<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_goodsplace_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='seo' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_goodsplace_preOpen' id='<?php echo $theme_shortname?>_goodsplace_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">메인화면 설정 - 상품배치 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_goodsplace_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">추천상품</div>
		<div class="item">
			<div class="slideItemsTitle">
				<span style="font-size:12px;font-weight:normal;">배치순위</span>
				<select name="<?php echo $theme_shortname?>_goodsplace_sort_1" id="<?php echo $theme_shortname?>_goodsplace_sort_1" style="height:22px;">
				<?php
					for($i=1;$i<=5;$i++) {
						$goodsplace_sort_1=get_option($theme_shortname."_goodsplace_sort_1")?get_option($theme_shortname."_goodsplace_sort_1"):1;
						echo "<option value=\"".$i."\"".(($goodsplace_sort_1==$i)?"selected":"").">".$i."</option>";
					}
				?>
				</select>
			</div>
			<dl>
				<dt>사용여부</dt>
				<dd>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_1" id="<?php echo $theme_shortname?>_goodsplace_use_1" value='Y' <?php echo (get_option($theme_shortname."_goodsplace_use_1")=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_1">사용</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_1" id="<?php echo $theme_shortname?>_goodsplace_use_1" value='N' <?php echo (get_option($theme_shortname."_goodsplace_use_1")=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_1">사용안함</label>
				</dd>
			</dl>
			<dl>
				<dt>명칭변경</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_title_1' id='<?php echo $theme_shortname?>_goodsplace_title_1' value='<?php echo get_option($theme_shortname."_goodsplace_title_1")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>부가설명</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_description_1' id='<?php echo $theme_shortname?>_goodsplace_description_1' value='<?php echo get_option($theme_shortname."_goodsplace_description_1")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>더보기 링크 URL</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_url_1' id='<?php echo $theme_shortname?>_goodsplace_url_1' value='<?php echo get_option($theme_shortname."_goodsplace_url_1")?>' style='width:70%;'/>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_url_1_window" id="<?php echo $theme_shortname?>_goodsplace_url_1_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_right_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_url_1_window_self">현재창</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_url_1_window" id="<?php echo $theme_shortname?>_goodsplace_url_1_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_goodsplace_url_1_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_url_1_window_blank">새창</label>
				</dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">베스트상품</div>
		<div class="item">
			<div class="slideItemsTitle">
				<span style="font-size:12px;font-weight:normal;">배치순위</span>
				<select name="<?php echo $theme_shortname?>_goodsplace_sort_2" id="<?php echo $theme_shortname?>_goodsplace_sort_2" style="height:22px;">
				<?php
					for($i=1;$i<=5;$i++) {
						$goodsplace_sort_2=get_option($theme_shortname."_goodsplace_sort_2")?get_option($theme_shortname."_goodsplace_sort_2"):2;
						echo "<option value=\"".$i."\"".(($goodsplace_sort_2==$i)?"selected":"").">".$i."</option>";
					}
				?>
				</select>
			</div>
			<dl>
				<dt>사용여부</dt>
				<dd>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_2" id="<?php echo $theme_shortname?>_goodsplace_use_2_1" value='Y' <?php echo (get_option($theme_shortname."_goodsplace_use_2")=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_2_1">사용</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_2" id="<?php echo $theme_shortname?>_goodsplace_use_2_2" value='N' <?php echo (get_option($theme_shortname."_goodsplace_use_2")=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_2_2">사용안함</label>
				</dd>
			</dl>
			<dl>
				<dt>명칭변경</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_title_2' id='<?php echo $theme_shortname?>_goodsplace_title_2' value='<?php echo get_option($theme_shortname."_goodsplace_title_2")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>부가설명</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_description_2' id='<?php echo $theme_shortname?>_goodsplace_description_2' value='<?php echo get_option($theme_shortname."_goodsplace_description_2")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>더보기 링크 URL</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_url_2' id='<?php echo $theme_shortname?>_goodsplace_url_2' value='<?php echo get_option($theme_shortname."_goodsplace_url_2")?>' style='width:70%;'/>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_url_2_window" id="<?php echo $theme_shortname?>_goodsplace_url_2_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_right_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_url_2_window_self">현재창</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_url_2_window" id="<?php echo $theme_shortname?>_goodsplace_url_2_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_goodsplace_url_2_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_url_2_window_blank">새창</label>
				</dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">MD기획상품</div>
		<div class="item">
			<div class="slideItemsTitle">
				<span style="font-size:12px;font-weight:normal;">배치순위</span>
				<select name="<?php echo $theme_shortname?>_goodsplace_sort_3" id="<?php echo $theme_shortname?>_goodsplace_sort_3" style="height:22px;">
				<?php
					for($i=1;$i<=5;$i++) {
						$goodsplace_sort_3=get_option($theme_shortname."_goodsplace_sort_3")?get_option($theme_shortname."_goodsplace_sort_3"):3;
						echo "<option value=\"".$i."\"".(($goodsplace_sort_3==$i)?"selected":"").">".$i."</option>";
					}
				?>
				</select>
			</div>
			<dl>
				<dt>사용여부</dt>
				<dd>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_3" id="<?php echo $theme_shortname?>_goodsplace_use_3_1" value='Y' <?php echo (get_option($theme_shortname."_goodsplace_use_3")=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_3_1">사용</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_3" id="<?php echo $theme_shortname?>_goodsplace_use_3_2" value='N' <?php echo (get_option($theme_shortname."_goodsplace_use_3")=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_3_2">사용안함</label>
				</dd>
			</dl>
			<dl>
				<dt>명칭변경</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_title_3' id='<?php echo $theme_shortname?>_goodsplace_title_3' value='<?php echo get_option($theme_shortname."_goodsplace_title_3")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>부가설명</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_description_3' id='<?php echo $theme_shortname?>_goodsplace_description_3' value='<?php echo get_option($theme_shortname."_goodsplace_description_3")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>표시개수</dt>
				<dd>
					<select name="<?php echo $theme_shortname?>_goodsplace_line_3" id="<?php echo $theme_shortname?>_goodsplace_line_3" style="height:22px;">
					<?php
						for($i=1;$i<=4;$i++) {
							$goodsplace_line_3=get_option($theme_shortname."_goodsplace_line_3")?get_option($theme_shortname."_goodsplace_line_3"):1;
							echo "<option value=\"".$i."\"".(($goodsplace_line_3==$i)?"selected":"").">".$i."줄</option>";
						}
					?>
					</select>
				</dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">신상품</div>
		<div class="item">
			<div class="slideItemsTitle">
				<span style="font-size:12px;font-weight:normal;">배치순위</span>
				<select name="<?php echo $theme_shortname?>_goodsplace_sort_4" id="<?php echo $theme_shortname?>_goodsplace_sort_4" style="height:22px;">
				<?php
					for($i=1;$i<=5;$i++) {
						$goodsplace_sort_4=get_option($theme_shortname."_goodsplace_sort_4")?get_option($theme_shortname."_goodsplace_sort_4"):4;
						echo "<option value=\"".$i."\"".(($goodsplace_sort_4==$i)?"selected":"").">".$i."</option>";
					}
				?>
				</select>
			</div>
			<dl>
				<dt>사용여부</dt>
				<dd>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_4" id="<?php echo $theme_shortname?>_goodsplace_use_4_1" value='Y' <?php echo (get_option($theme_shortname."_goodsplace_use_4")=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_4_1">사용</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_4" id="<?php echo $theme_shortname?>_goodsplace_use_4_2" value='N' <?php echo (get_option($theme_shortname."_goodsplace_use_4")=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_4_2">사용안함</label>
				</dd>
			</dl>
			<dl>
				<dt>명칭변경</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_title_4' id='<?php echo $theme_shortname?>_goodsplace_title_4' value='<?php echo get_option($theme_shortname."_goodsplace_title_4")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>부가설명</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_description_4' id='<?php echo $theme_shortname?>_goodsplace_description_4' value='<?php echo get_option($theme_shortname."_goodsplace_description_4")?>' style='width:70%;'>
				</dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
      <li class="group">
	    <div class="itemHeader <?php echo $preOpen2?>">베스트 상품평</div>
		<div class="item">
			<div class="slideItemsTitle">
				<span style="font-size:12px;font-weight:normal;">배치순위</span>
				<select name="<?php echo $theme_shortname?>_goodsplace_sort_5" id="<?php echo $theme_shortname?>_goodsplace_sort_5" style="height:22px;">
				<?php
					for($i=1;$i<=5;$i++) {
						$goodsplace_sort_5=get_option($theme_shortname."_goodsplace_sort_5")?get_option($theme_shortname."_goodsplace_sort_5"):5;
						echo "<option value=\"".$i."\"".(($goodsplace_sort_5==$i)?"selected":"").">".$i."</option>";
					}
				?>
				</select>
			</div>
			<dl>
				<dt>사용여부</dt>
				<dd>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_5" id="<?php echo $theme_shortname?>_goodsplace_use_5_1" value='Y' <?php echo (get_option($theme_shortname."_goodsplace_use_5")=='Y')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_5_1">사용</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_use_5" id="<?php echo $theme_shortname?>_goodsplace_use_5_2" value='N' <?php echo (get_option($theme_shortname."_goodsplace_use_5")=='N')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_use_5_2">사용안함</label>
				</dd>
			</dl>
			<dl>
				<dt>명칭변경</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_title_5' id='<?php echo $theme_shortname?>_goodsplace_title_5' value='<?php echo get_option($theme_shortname."_goodsplace_title_5")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>부가설명</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_description_5' id='<?php echo $theme_shortname?>_goodsplace_description_5' value='<?php echo get_option($theme_shortname."_goodsplace_description_5")?>' style='width:70%;'>
				</dd>
			</dl>
			<dl>
				<dt>표시개수</dt>
				<dd>
					<select name="<?php echo $theme_shortname?>_goodsplace_line_5" id="<?php echo $theme_shortname?>_goodsplace_line_5" style="height:22px;">
					<?php
						for($i=1;$i<=4;$i++) {
							$goodsplace_line_5=get_option($theme_shortname."_goodsplace_line_5")?get_option($theme_shortname."_goodsplace_line_5"):1;
							echo "<option value=\"".$i."\"".(($goodsplace_line_5==$i)?"selected":"").">".$i."줄</option>";
						}
					?>
					</select>
				</dd>
			</dl>
			<dl>
				<dt>더보기 링크 URL</dt>
				<dd>
					<input type='text' name='<?php echo $theme_shortname?>_goodsplace_url_5' id='<?php echo $theme_shortname?>_goodsplace_url_5' value='<?php echo get_option($theme_shortname."_goodsplace_url_5")?>' style='width:70%;'/>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_url_5_window" id="<?php echo $theme_shortname?>_goodsplace_url_5_window_self" value='_self' <?php echo (get_option($theme_shortname."_display_right_banner_url_".$i."_window")!='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_url_5_window_self">현재창</label>
					<input type="radio" name="<?php echo $theme_shortname?>_goodsplace_url_5_window" id="<?php echo $theme_shortname?>_goodsplace_url_5_window_blank" value='_blank' <?php echo (get_option($theme_shortname."_goodsplace_url_5_window")=='_blank')?'checked="checked"':''?> style='border:0px;'><label for="<?php echo $theme_shortname?>_goodsplace_url_5_window_blank">새창</label>
				</dd>
			</dl>
		</div>
      </li>
<!-- ******************************************************************************************************************************************************* -->
    </ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('goodsplace');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('goodsplace');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->


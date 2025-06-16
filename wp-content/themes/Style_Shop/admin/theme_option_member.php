<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_member_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>

	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='member' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_member_preOpen' id='<?php echo $theme_shortname?>_member_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">기능 설정 - 회원가입/약관/방침 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_member_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">이용약관</div>
          <div class="item">
            <ul class="descBox">
              <li><span class="shortCode">[theme_member_agreement type='agree' width='100%' height='700px' /]</span></li>
              <li class="desc">이용약관 페이지를 생성하신 후 위의 페이지 Shortcode를 복사/붙여넣기 해 주세요. &nbsp;<a href="<?php admin_url()?>post-new.php?post_type=page" class="infoBtn">페이지 만들기</a></li>
            </ul>
            <dl>
              <dt>약관내용</dt>
              <dd>
                <textarea name="<?php echo $theme_shortname?>_member_agreement" id="<?php echo $theme_shortname?>_member_agreement" rows="5" cols="" style="width:100%;"><?php echo stripslashes(get_option($theme_shortname."_member_agreement"))?></textarea>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">개인정보취급방침</div>
          <div class="item">
            <ul class="descBox">
              <li><span class="shortCode">[theme_member_agreement type='private' width='100%' height='700px' /]</span></li>
              <li class="desc">개인정보취급방침 페이지를 생성하신 후 위의 페이지 Shortcode를 복사/붙여넣기 해 주세요. &nbsp;<a href="<?php admin_url()?>post-new.php?post_type=page" class="infoBtn">페이지 만들기</a></li>
            </ul>
            <dl>
              <dt>약관내용</dt>
              <dd>
                <textarea name="<?php echo $theme_shortname?>_member_private_1" id="<?php echo $theme_shortname?>_member_private_1" rows="5" cols="" style="width:100%;"><?php echo stripslashes(get_option($theme_shortname."_member_private_1"))?></textarea>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">이메일 무단수집 거부</div>
          <div class="item">
            <ul class="descBox">
              <li><span class="shortCode">[theme_member_agreement type='email' width='100%' height='700px' /]</span></li>
              <li class="desc">이메일 무단수집 거부 페이지를 생성하신 후 위의 페이지 Shortcode를 복사/붙여넣기 해 주세요. &nbsp;<a href="<?php admin_url()?>post-new.php?post_type=page" class="infoBtn">페이지 만들기</a></li>
            </ul>
            <dl>
              <dt>약관내용</dt>
              <dd>
                <textarea name="<?php echo $theme_shortname?>_member_email_reject" id="<?php echo $theme_shortname?>_member_email_reject" rows="5" cols="" style="width:100%;"><?php echo stripslashes(get_option($theme_shortname."_member_email_reject"))?></textarea>
              </dd>
            </dl>
          </div>
        </li>
 <!-- ******************************************************************************************************************************************************* -->
<?php if(plugin_active_check('BBSe_Commerce')) {?>
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">전자금융거래 이용약관</div>
          <div class="item">
			<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
				<p class="desc2">(*) 사이트 내에서 금융거래가 이루어지지 않는 경우, 전자금융거래 이용약관을 '표시하지 않음'으로 설정할 수 있습니다.</p>
			</div>
            <ul class="descBox">
              <li><span class="shortCode">[theme_member_agreement type='order' width='100%' height='700px' /]</span></li>
              <li class="desc">전자금융거래 이용약관 페이지를 생성하신 후 위의 페이지 Shortcode를 복사/붙여넣기 해 주세요. &nbsp;<a href="<?php admin_url()?>post-new.php?post_type=page" class="infoBtn">페이지 만들기</a></li>
            </ul>
            <dl>
              <dt>회원가입 시 표시여부</dt>
              <dd>
                <input type="radio" name="<?php echo $theme_shortname?>_member_agreement_order_view_yn" id="<?php echo $theme_shortname?>_member_agreement_order_view_yn" value="U" <?php echo(!get_option($theme_shortname."_member_agreement_order_view_yn") || get_option($theme_shortname."_member_agreement_order_view_yn")=='U')?"checked='checked'":"";?> />표시함&nbsp;&nbsp;&nbsp;<input type="radio" name="<?php echo $theme_shortname?>_member_agreement_order_view_yn" id="<?php echo $theme_shortname?>_member_agreement_order_view_yn" value="N" <?php echo(get_option($theme_shortname."_member_agreement_order_view_yn")=='N')?"checked='checked'":"";?> />표시하지 않음
              </dd>
            </dl>
            <dl>
              <dt>비회원구매 시 표시여부</dt>
              <dd>
                <input type="radio" name="<?php echo $theme_shortname?>_member_agreement_nonmember_view_yn" id="<?php echo $theme_shortname?>_member_agreement_nonmember_view_yn" value="U" <?php echo(!get_option($theme_shortname."_member_agreement_nonmember_view_yn") || get_option($theme_shortname."_member_agreement_nonmember_view_yn")=='U')?"checked='checked'":"";?> />표시함&nbsp;&nbsp;&nbsp;<input type="radio" name="<?php echo $theme_shortname?>_member_agreement_nonmember_view_yn" id="<?php echo $theme_shortname?>_member_agreement_nonmember_view_yn" value="N" <?php echo(get_option($theme_shortname."_member_agreement_nonmember_view_yn")=='N')?"checked='checked'":"";?> />표시하지 않음
              </dd>
            </dl>
            <dl>
              <dt>약관내용</dt>
              <dd>
                <textarea name="<?php echo $theme_shortname?>_member_agreement_order" id="<?php echo $theme_shortname?>_member_agreement_order" rows="5" cols="" style="width:100%;"><?php echo stripslashes(get_option($theme_shortname."_member_agreement_order"))?></textarea>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
<?php }?>
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">개인정보 수집 및 이용에 대한 안내</div>
          <div class="item">
            <ul class="descBox">
              <li class="desc">비회원 구매시 화면에 노출됩니다.</li>
            </ul>
            <dl>
              <dt>약관내용</dt>
              <dd>
                <textarea name="<?php echo $theme_shortname?>_member_private_2" id="<?php echo $theme_shortname?>_member_private_2" rows="5" cols="" style="width:100%;"><?php echo stripslashes(get_option($theme_shortname."_member_private_2"))?></textarea>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
<?php if(plugin_active_check('BBSe_Commerce')) {?>
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">회원탈퇴 약관</div>
          <div class="item">
            <ul class="descBox">
              <li><span class="shortCode">[theme_member_agreement type='leave' width='100%' height='700px' /]</span></li>
              <li class="desc">회원탈퇴 약관 페이지를 생성하신 후 위의 페이지 Shortcode를 복사/붙여넣기 해 주세요. &nbsp;<a href="<?php admin_url()?>post-new.php?post_type=page" class="infoBtn">페이지 만들기</a></li>
            </ul>
            <dl>
              <dt>약관내용</dt>
              <dd>
                <textarea name="<?php echo $theme_shortname?>_member_agreement_leave" id="<?php echo $theme_shortname?>_member_agreement_leave" rows="5" cols="" style="width:100%;"><?php echo stripslashes(get_option($theme_shortname."_member_agreement_leave"))?></textarea>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->
<?php }?>
    </ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('member');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('member');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->


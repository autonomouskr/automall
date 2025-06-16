<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$preOpenValue = get_option($theme_shortname."_memberpage_preOpen");
$preOpen1 = $preOpenValue == 'U'?'preOpen':'';
$preOpen2 = $preOpenValue == 'U'?'opened':'';
?>
	<!-- contents 시작 -->
	<div id="content">
		<form id='optionForm' method='post' enctype='multipart/form-data'>
		<input type='hidden' name='option_type' id='option_type' value='memberpage' />
		<input type='hidden' name='action' id='action' value='' />
		<input type='hidden' name='<?php echo $theme_shortname?>_memberpage_preOpen' id='<?php echo $theme_shortname?>_memberpage_preOpen' value='<?php echo $preOpenValue?$preOpenValue:'N'?>' />
		<div class="tit">기능 설정 - 약관/방침 페이지 설정 <span class="all_preOpen infoBtn" data-target="<?php echo $theme_shortname?>_memberpage_preOpen" title="기본 상태를 열림 또는 닫힘 상태로 설정 할 수 있습니다."><?php echo $preOpenValue=='U'?'모두 닫아두기':'모두 열어두기'?></span></div>

<div class="accordionWrap">
	<div style="margin:5px;padding:10px;border:1px solid #4c99ba;background-color:#fdfdfd;">
		<p>(*) 회원서비스(회원가입)의 좌측메뉴 및 각 약관의 '자세히보기' 클릭 시 해당 페이지로 연결됩니다.<br />
		(*) 기능 설정 - 회원가입/약관/방침 입력 및 페이지를 생성하신 후 해당 페이지를 항목에 맞게 연결해 주세요.</p>
	</div>
    <ul class="accordion <?php echo $preOpen1?>">
<!-- ******************************************************************************************************************************************************* -->
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">이용약관</div>
          <div class="item">
            <dl>
              <dt>적용 페이지</dt>
              <dd>
				<?php
				$pageData = get_custom_list('page');
				$agreement_page = get_option($theme_shortname.'_memberpage_agreement');
				?>
				<select name="<?php echo $theme_shortname;?>_memberpage_agreement" id="<?php echo $theme_shortname;?>_memberpage_agreement">
					<option value=""<?php if(empty($agreement_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($agreement_page) && ($pageData[$z]['id'] == $agreement_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php 
					}
					?>
				</select>
              </dd>
            </dl>
          </div>
        </li>
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">개인정보취급방침</div>
          <div class="item">
            <dl>
              <dt>적용 페이지</dt>
              <dd>
				<?php
				$private_page = get_option($theme_shortname.'_memberpage_private_1');
				?>
				<select name="<?php echo $theme_shortname;?>_memberpage_private_1" id="<?php echo $theme_shortname;?>_memberpage_private_1">
					<option value=""<?php if(empty($private_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($private_page) && ($pageData[$z]['id'] == $private_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php 
					}
					?>
				</select>
              </dd>
            </dl>
          </div>
        </li>
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">전자금융거래 이용약관</div>
          <div class="item">
            <dl>
              <dt>적용 페이지</dt>
              <dd>
				<?php
				$order_page = get_option($theme_shortname.'_memberpage_agreement_order');
				?>
				<select name="<?php echo $theme_shortname;?>_memberpage_agreement_order" id="<?php echo $theme_shortname;?>_memberpage_agreement_order">
					<option value=""<?php if(empty($order_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($order_page) && ($pageData[$z]['id'] == $order_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php 
					}
					?>
				</select>
              </dd>
            </dl>
          </div>
        </li>
        <li class="group">
          <div class="itemHeader <?php echo $preOpen2?>">회원탈퇴 약관</div>
          <div class="item">
            <dl>
              <dt>적용 페이지</dt>
              <dd>
				<?php
				$leave_page = get_option($theme_shortname.'_memberpage_agreement_leave');
				?>
				<select name="<?php echo $theme_shortname;?>_memberpage_agreement_leave" id="<?php echo $theme_shortname;?>_memberpage_agreement_leave">
					<option value=""<?php if(empty($leave_page)) echo " selected";?>>페이지 선택</option>
					<?php 
					for($z = 0; $z < sizeof($pageData); $z++){
					if(!empty($leave_page) && ($pageData[$z]['id'] == $leave_page)) $pageSelect = " selected";
					else $pageSelect = "";
					?>
					<option value="<?php echo $pageData[$z]['id']?>"<?php echo $pageSelect?>><?php echo $pageData[$z]['name']?></option>
					<?php	
					}
					?>
				</select>
              </dd>
            </dl>
          </div>
        </li>
<!-- ******************************************************************************************************************************************************* -->

    </ul>
</div>

		<div class="btn">
			<button type="submit" class="b _c1" onClick="save_submit('memberpage');">저장</button>
			<button type="button" class="_c1" onClick="reset_submit('memberpage');">초기화</button>
		</div>
		</form>
	</div>
	<!-- //contents 끝 -->


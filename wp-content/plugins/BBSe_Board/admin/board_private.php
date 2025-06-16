<script language="javascript">
function private_submit(dbType, action_url){
	var frm = document.privateFrm;

	if(dbType == 'insert') dbTypeName = "등록";
	else dbTypeName = "수정";

	if(!jQuery("#cnf_contents").val()){
		alert("개인정보수집안내 내용을 입력해주세요.");
		jQuery("#cnf_contents").focus();
		return false;
	}
	if(frm.use_private[0].checked == false && frm.use_private[1].checked == false){
		alert("사용여부를 선택해주세요.");
		return false;
	}
	if(confirm("개인정보수집안내를 " + dbTypeName + "하시겠습니까?")){
		frm.action = action_url;
		frm.submit();
	}
}
</script>
<div class="wrap">
	<?php
	if(!empty($sMode)){
		echo '<div id="message" class="updated fade"><p><strong>개인정보수집안내를 정상적으로 저장하였습니다.</strong></p></div>';
	}
	?>
	<div id="bbse_box">
		<div class="inner">
			<div class="guide_top">
				<span class="tl"></span><span class="tr"></span><span class="manual_btn"><a href="http://manual.bbsetheme.com/bbse-board" target="_blank"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>images/btn_manual.png" /></a></span>
				<a href="#"><span class="logo">BBS</span><span class="logo_board">e-Board</span><span class="logo_version"><?php echo BBSE_BOARD_VER?></span></a>
			</div>
			<div id="content">
				<!-- 내용 start -->
				<form name="privateFrm" method="post">
				<input type="hidden" name="no" value="<?php echo $data->no?>">
				<div class="tit">개인정보수집안내</div>
				<ul class="form_ul1">
					<li>
						<div class="form_stitle">1. 개인정보수집안내</div>
						<div class="form_content">
							<textarea name="cnf_contents" id="cnf_contents" style="width:650px;height:320px;"><?php if(!empty($data->cnf_contents)) echo $data->cnf_contents; else{ echo "* 개인정보의 수집 및 이용목적 *
  1. BBS e-Board는 다음과 같은 목적을 위하여 개인정보를 수집하고 있습니다.
    (각 수집항목별 수집목적제시)
    - 성명, 아이디, 비밀번호 : 회원제 서비스 이용에 따른 본인 식별 절차에 이용
    - 이메일주소, 전화번호. (뉴스레터 수신여부) :
      고지사항 전달, 본인 의사 확인, 불만 처리 등 원활한 의사소통 경로의 확보, 새로운 서비스, 신상품이나 이벤트 정보 등 최신 정보 의 안내
    - 은행계좌정보, 신용카드정보 : 유료 정보 이용에 대한 요금 결제
    - 주소, 전화번호 : 청구서, 경품과 쇼핑 물품 배송에 대한 정확한 배송지의 확보
    - 성별, 생년월일, 주소 : 인구통계학적분석(이용장의 연령별, 성별, 지역별 통계분석)
    - 그 외 선택항목 : 개인맞춤 서비스를 제공하기 위한 자료
  2. 단, 이용자의 기본적 인권 침해의 우려가 있는 민감한 개인정보(인종 및 민족, 사상 및 신조, 출신지 및 본적지, 정치적 성향 및 범죄기록, 건강상태 및 성생활 등)는 수집하지 않습니다.

* 수집하는 개인정보 항목 *
   BBS e-Board는 별도의 회원가입 절차 없이 대부분의 컨텐츠에 자유롭게 접근할 수 있습니다.
   BBS e-Board의 회원제 서비스를 이용하시고자 할 경우 다음의 정보를 입력해주셔야 하며 선택항목을 입력하시지 않았다 하여 서비스 이용에 제한은 없습니다.
  1. 회원가입 시 수집하는 개인정보의 범위
    - 필수항목 : 아이핀번호, 성명, 희망 ID, 비밀번호, 비밀번호 찾기 질문, 비밀번호 찾기 답변, 이메일주소(뉴스레터 수신여부), 주소, 전화번호, 성별, 생년월일
    - 선택항목 : 회사명, 사업자등록번호, 업태, 종목, 닉네임, 휴대폰번호, 팩스번호, 직업, 홈페이지 주소
  2. 유료 정보 이용 시 수집하는 개인정보의 범위
    - 본인확인 정보로써 ID와 비밀번호, 성명, 아이핀번호
    - 결제방법에 따라
     · 거래 은행명, 계좌번호, 거래자 성명
     · 신용카드종류, 카드번호, 유효기한, 비밀번호, 할부기간
     · 휴대폰번호
    - 배송에 필요한 정보로써 보내는 사람과 받는 사람의 성명과 주소, 우편번호, 연락처 등
  3. 개인정보 수집방법 : 홈페이지

* 개인정보의 보유기간 및 이용기간 *
  1. 귀하의 개인정보는 다음과 같이 개인정보의 수집목적 또는 제공받은 목적이 달성되면 파기됩니다. 단, 상법 등 관련법령의 규정에 의하여 다음과 같이 거래 관련 권리 의무 관계의 확인 등을 이유로 일정기간 보유하여야 할 필요가 있을 경우에는 일정기간 보유합니다.
    - 회원가입정보 : 회원가입을 탈퇴하거나 회원에서 제명된 경우에 특별한 사유에 사안에 대하여 사전에 보유목적, 기간 및 보유하는 개인정보 항목을 명시하여 동의를 구합니다.
    - 계약 또는 청약철회 등에 관한 기록 : 5년
    - 대금결제 및 재화 등의 공급에 관한 기록 : 5년
    - 소비자의 불만 또는 분쟁처리에 관한 기록 : 3년
  2. 귀하의 동의를 받아 보유하고 있는 거래 정보 등을 귀하께서 열람을 요구하는 경우 BBS e-Board는 지체 없이 그 열람 · 확인 할 수 있도록 조치합니다.";}?></textarea>
							<div style="padding-top:10px;">
								<input type="radio" name="use_private" value="Y"<?php if(empty($data->use_private) || $data->use_private == "Y") echo " checked"?> style='border:0px;' /> 사용 &nbsp;
								<input type="radio" name="use_private" value="N"<?php if(!empty($data->use_private) && $data->use_private == "N") echo " checked"?> style='border:0px;' /> 사용안함 &nbsp;
							</div>
						</div>
					</li>
				</ul>
				<div class="btn">
					<button type="button" class="b _c1" onclick="private_submit('<?php if(empty($data->cnf_contents)) echo "insert"; else echo "modify";?>','<?php echo BBSE_BOARD_PRIVATE_URL?>');">저장</button>
				</div>
				<!-- 내용 end -->
			</div>
			<div class="guide_bottom"><span class="lb"></span><span class="rb"></span></div>
		</div>
	</div>
	<?php global $noticeBoxComment; echo $noticeBoxComment;?>
</div>
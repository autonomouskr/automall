<?php $config = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bbse_board_config`");?>
<script type="text/javascript">
function write_check(action_url){
	var frm = document.write_frm;

	if(confirm("정보를 수정하시겠습니까?")){
		frm.action = action_url;
		frm.submit();
	}
}

jQuery(document).ready(function(){
	jQuery(".only_num").css("ime-mode", "disabled").keypress(function(event){
		if(event.which && (event.which < 48 || event.which > 57)){
			event.preventDefault();
        }
    }).keyup(function(){
		if(jQuery(this).val() != null && jQuery(this).val() != ""){
            jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ""));
        }
    });
});
</script>
<div class="wrap">
	<?php
	if(!empty($edit_mode) && $edit_mode == "edit"){
		echo '<div id="message" class="updated fade"><p><strong>정보를 정상적으로 저장하였습니다.</strong></p></div>';
	}
	?>
	<div id="bbse_box">
		<div class="inner">
			<div class="guide_top">
				<span class="tl"></span><span class="tr"></span><span class="manual_btn"><a href="http://manual.bbsetheme.com/bbse-board" target="_blank"><img src="<?php echo BBSE_BOARD_PLUGIN_WEB_URL?>images/btn_manual.png" /></a></span>
				<a href="#"><span class="logo">BBS</span><span class="logo_board">e-Board</span><span class="logo_version"><?php echo BBSE_BOARD_VER?></span></a>
			</div>
			<div id="content">
				<form method="post" name="write_frm" enctype="multipart/form-data">
				<div class="tit">SSL(보안서버)설정</div>
				<ul class="form_ul1">
					<li>
						<div class="form_stitle">
							2012년 8월 1일부터 보안서버 인증서 의무화 시행으로 개인정보를 취급하는 모든 웹사이트에 설치 의무화 되고,<br />
							위반 시 최대 3,000만원의 과태료가 부과됩니다.
						</div>
						<div class="form_content">
							<dl class="dl12">
								<dt style="width:160px;">&nbsp;&nbsp;&nbsp;&nbsp;사용설정</dt>
								<dd style="height:28px;">
									<input type="checkbox" name="use_ssl" id="use_ssl" value="1"<?php if($config->use_ssl == 1) echo " checked"?> /> 사용함
								</dd>
							</dl>
							<dl class="dl12">
								<dt style="width:160px;">&nbsp;&nbsp;&nbsp;&nbsp;적용 도메인</dt>
								<dd style="height:90px;">
									http:// <input type="text" name="ssl_domain" id="ssl_domain" style="width:350px;" value="<?php echo $config->ssl_domain?>" /><br />
									적용 도메인과 SSL 적용 도메인이 다를 경우, 오류가 발생합니다.<br />
									SSL 도메인의 기본 포트번호는 '443' 입니다.<br />
									http://서버접속주소/~계정아이디 형식은 적용되지 않습니다.
								</dd>
							</dl>
							<dl class="dl12">
								<dt style="width:160px;">&nbsp;&nbsp;&nbsp;&nbsp;포트번호</dt>
								<dd style="height:28px;">
									<input type="text" name="ssl_port" id="ssl_port" style="width:120px;" value="<?php echo $config->ssl_port?>" class="only_num" />
								</dd>
							</dl>
						</div>
					</li>
				</ul>
				<div class="btn">
					<button type="button" class="b _c1" onclick="write_check('<?php echo $EDIT_CONFIG_URL?>');">저장</button>
				</div>
				</form>
			</div>
			<div class="guide_bottom"><span class="lb"></span><span class="rb"></span></div>
		</div>
	</div>
	<?php global $noticeBoxComment; echo $noticeBoxComment;?>
</div>
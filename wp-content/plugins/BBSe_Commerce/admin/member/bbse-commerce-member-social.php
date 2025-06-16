<script language="javascript">
function resetForm(action_url){
	if(confirm("소셜(간편)로그인 환경설정을 초기화 하시겠습니까?")){
		jQuery("#tMode").val("reset");
		jQuery("#frmInsert").action = action_url;
		jQuery("#frmInsert").submit();
	}
}

function integration_use(tType){
	if(tType=='Y'){
		jQuery("#rowIntegrationInfo").css("display","table-row");
	}
	else{
		jQuery("#rowIntegrationInfo").css("display","none");
	}
}

function period_view(vType){
	if(vType=='P'){
		jQuery("#integration_period").show();
	}
	else{
		jQuery("#integration_period").hide();
	}
}

function submitForm(action_url){
	if(jQuery("input[id='naver_use_yn']").eq(0).is(":checked")){
		if(!jQuery("#naver_client_id").val()){
			alert("네이버 Client ID를 입력해 주세요.       ");
			jQuery("#naver_client_id").focus();
			return;
		}
		if(!jQuery("#naver_client_secret").val()){
			alert("네이버 Client Secret을 입력해 주세요.       ");
			jQuery("#naver_client_secret").focus();
			return;
		}
	}
	if(jQuery("input[id='facebook_use_yn']").eq(0).is(":checked")){
		if(!jQuery("#facebook_app_id").val()){
			alert("페이스북 App ID를 입력해 주세요.       ");
			jQuery("#facebook_app_id").focus();
			return;
		}
		if(!jQuery("#facebook_app_secret").val()){
			alert("페이스북 App Secret을 입력해 주세요.       ");
			jQuery("#facebook_app_secret").focus();
			return;
		}
	}
	if(jQuery("input[id='google_use_yn']").eq(0).is(":checked")){
		if(!jQuery("#google_client_id").val()){
			alert("구글 클라이언트 ID를 입력해 주세요.       ");
			jQuery("#google_client_id").focus();
			return;
		}
		if(!jQuery("#google_client_secret").val()){
			alert("클라이언트 보안 비밀을 입력해 주세요.       ");
			jQuery("#google_client_secret").focus();
			return;
		}
	}
	if(jQuery("input[id='daum_use_yn']").eq(0).is(":checked")){
		if(!jQuery("#daum_client_id").val()){
			alert("다음 Client ID를 입력해 주세요.       ");
			jQuery("#daum_client_id").focus();
			return;
		}
		if(!jQuery("#daum_client_secret").val()){
			alert("Client Secret을 입력해 주세요.       ");
			jQuery("#daum_client_secret").focus();
			return;
		}
	}
	if(jQuery("input[id='kakao_use_yn']").eq(0).is(":checked")){
		if(!jQuery("#kakao_rest_api_key").val()){
			alert("카카오 REST API 키를 입력해 주세요.       ");
			jQuery("#kakao_rest_api_key").focus();
			return;
		}
	}
	if(jQuery("input[id='twitter_use_yn']").eq(0).is(":checked")){
		if(!jQuery("#twitter_api_key").val()){
			alert("트위터 Consumer Key를 입력해 주세요.       ");
			jQuery("#twitter_api_key").focus();
			return;
		}
		if(!jQuery("#twitter_api_secret").val()){
			alert("트위터 Consumer Secret을 입력해 주세요.       ");
			jQuery("#twitter_api_secret").focus();
			return;
		}
	}

	if(confirm("소셜(간편)로그인 환경설정을 저장 하시겠습니까?")){
		jQuery("#tMode").val("save");
		jQuery("#frmInsert").action = action_url;
		jQuery("#frmInsert").submit();
	}
}
</script>
<div>
	<?php
	if($saveFlag == 'save') echo '<div id="message" class="updated fade"><p><strong>소셜(간편)로그인 설정을 저장하였습니다.</strong></p></div>';
	elseif($saveFlag == 'reset') echo '<div id="message" class="updated fade"><p><strong>소셜(간편)로그인 설정을 초기화 하였습니다.</strong></p></div>';
	?>
	<div class="titleH5" style="margin:20px 0 10px 0; ">소셜(간편)로그인</div>
	<!--본문 내용(시작)-->

	<div class="borderBox" style="line-height:20px;">
		* 소셜(간편)로그인은 설정상태가 사용함인 경우 만 사이트의 로그인 페이지에 노출되며, 노출 순서는 네이버 - 페이스북 - 구글 - 카카오 - 트위터 순입니다. (순서변경 불가)<br />
		- 소셜(간편)로그인 한 정보와 기존 회원정보를 비교하여(이메일 주소), 이메일 주소가 동일한 경우 회원통합 안내가 진행됩니다. (회원통합 : 예/아니오)<br />
		- 회원통합이 이루어지지 않은 소셜(간편)로그인 사용자는 회원관련 기능(적립금 지급, 적립금내역, 회원정보 등)을 사용 할 수 없습니다.<br />
		- 회원통합 기능의 사용여부 및 회원통합 시 아니오를 선택한 고객에게 다시 회원통합 여부(예/아니오)를 묻는 기간의 주기를 '회원통합 노출주기'에서 설정이 가능합니다.<br />
		- SNS 아이콘 표시는 화면 상단의 로그인 옆(우측)에 SNS 라는 아이콘이 표시됩니다.
	</div>

	<form name="frmInsert" id="frmInsert" method="post">
	<input type="hidden" name="tMode" id="tMode" value="">

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2">소셜(간편)로그인 환경설정</th>
		</tr>
		<tr>
			<th>회원통합 사용여부</th>
			<td>
				<input type='radio' name='integration_use_yn' id='integration_use_yn' onClick="integration_use('Y');" value='Y' style="border:0px;"<?php if(empty($config['integration']['integration_use_yn']) || $config['integration']['integration_use_yn'] == "Y") echo " checked"?>> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='integration_use_yn' id='integration_use_yn' onClick="integration_use('N');" value='N' style="border:0px;"<?php if($config['integration']['integration_use_yn'] == "N") echo " checked"?>> 사용안함			
			</td>
		</tr>
		<tr id="rowIntegrationInfo" style="display:<?php echo (empty($config['integration']['integration_use_yn']) || $config['integration']['integration_use_yn'] == "Y")?"table-row":"none";?>;">
			<th>회원통합 노출주기</th>
			<td>
				<input type='radio' name='integration_view_type' id='integration_view_type' onClick="period_view('A');" value='A' style="border:0px;"<?php if(empty($config['integration']['integration_view_type']) || $config['integration']['integration_view_type'] == "A") echo " checked"?>> 항상&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type='radio' name='integration_view_type' id='integration_view_type' onClick="period_view('P');" value='P' style="border:0px;"<?php if($config['integration']['integration_view_type'] == "P") echo " checked"?>> 기간&nbsp;&nbsp;&nbsp;
				<select name="integration_period" id="integration_period" style="display:<?php echo ($config['integration']['integration_view_type'] == "P")?"inline":"none";?>;">
					<option value="1"<?php echo ($config['integration']['integration_period'] == "1")?" selected='selected'":"";?>>1개월</option>
					<option value="3"<?php echo ($config['integration']['integration_period'] == "3")?" selected='selected'":"";?>>3개월</option>
					<option value="6"<?php echo ($config['integration']['integration_period'] == "6")?" selected='selected'":"";?>>6개월</option>
					<option value="12"<?php echo ($config['integration']['integration_period'] == "12")?" selected='selected'":"";?>>12개월</option>
				</select>	
			</td>
		</tr>
		<tr>
			<th>SNS 아이콘 표시여부</th>
			<td>
				<input type='radio' name='sns_icon_use_yn' id='sns_icon_use_yn' value='Y' style="border:0px;"<?php if(empty($config['integration']['sns_icon_use_yn']) || $config['integration']['sns_icon_use_yn'] == "Y") echo " checked"?>> 표시함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='sns_icon_use_yn' id='sns_icon_use_yn' value='N' style="border:0px;"<?php if($config['integration']['sns_icon_use_yn'] == "N") echo " checked"?>> 표시안함			
			</td>
		</tr>
	</table>
	<br />	<br />

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/social_login_naver.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="네이버 아이디로 로그인"/>네이버 아이디로 로그인</th>
		</tr>
		<tr>
			<th>네이버 사용여부</th>
			<td>
				<input type='radio' name='naver_use_yn' id='naver_use_yn' value='Y' style="border:0px;"<?php if($config['naver']['naver_use_yn'] == "Y") echo " checked"?>> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='naver_use_yn' id='naver_use_yn' value='N' style="border:0px;"<?php if(empty($config['naver']['naver_use_yn']) || $config['naver']['naver_use_yn'] == "N") echo " checked"?>> 사용안함			
			</td>
		</tr>
		<tr>
			<th>Client ID</th>
			<td>
				<input type="text" name="naver_client_id" id="naver_client_id" style="width:500px;" value="<?php echo $config['naver']['naver_client_id'];?>">
			</td>
		</tr>
		<tr>
			<th>Client Secret</th>
			<td>
				<input type="text" name="naver_client_secret" id="naver_client_secret" style="width:500px;" value="<?php echo $config['naver']['naver_client_secret'];?>">
			</td>
		</tr>
		<tr>
			<th>PC웹</th>
			<td>
				서비스 URL : <?php echo home_url();?><br />
				Callback URL : <?php echo BBSE_THEME_WEB_URL;?>/snslogin/social-login-naver-callback.php<br />
			</td>
		</tr>
		<tr>
			<th>Mobile웹</th>
			<td>
				서비스 URL : <?php echo home_url();?><br />
				Callback URL : <?php echo BBSE_THEME_WEB_URL;?>/snslogin/social-login-naver-callback.php<br />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div class="titleH6" style="margin:10px 0 5px 0;">[네이버 아이디로 로그인 안내]</div>
					- 어플리케이션 생성/관리 : <a href="https://nid.naver.com/devcenter/main.nhn" target="_blank" title="네이버 아이디로 로그인 바로가기">https://nid.naver.com/devcenter/main.nhn <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/left_icon4.png" align="absmiddle" style="height:13px;width:auto;margin-right:10px;" alt="네이버 어플리케이션 생성/관리"/></a><br />
					- 서비스 URL 및 Callback URL은 내 애플리케이션 - [연동할 어플리케이션 선택] - 서비스 환경의 web을 선택하신 후 입력해 주세요.<br />
					- 서비스 URL 및 Callback URL의 도메인에 www가 포함어 있지 않은 경우(서브 도메인 또는 www를 제외한 채 URL을 입력한 경우), 네이버 아이디로 로그인이 정상적으로 동작하지 않을 수 있습니다.<br />
					- 내 애플리케이션 -> [연동할 어플리케이션 선택] -> 일반 페이지의 "API 상태"를 "서비스 적용"으로 선택하신 후 사용해 주세요.
				</div>
			</td>
		</tr>
	</table>
	<br />	<br />

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/social_login_facebook.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="페이스북 로그인"/>페이스북 로그인</th>
		</tr>
		<tr>
			<th>페이스북 사용여부</th>
			<td>
				<input type='radio' name='facebook_use_yn' id='facebook_use_yn' value='Y' style="border:0px;"<?php if($config['facebook']['facebook_use_yn'] == "Y") echo " checked"?>> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='facebook_use_yn' id='facebook_use_yn' value='N' style="border:0px;"<?php if(empty($config['facebook']['facebook_use_yn']) || $config['facebook']['facebook_use_yn'] == "N") echo " checked"?>> 사용안함			
			</td>
		</tr>
		<tr>
			<th>App ID</th>
			<td>
				<input type="text" name="facebook_app_id" id="facebook_app_id" style="width:500px;" value="<?php echo $config['facebook']['facebook_app_id'];?>">
			</td>
		</tr>
		<tr>
			<th>App Secret</th>
			<td>
				<input type="text" name="facebook_app_secret" id="facebook_app_secret" style="width:500px;" value="<?php echo $config['facebook']['facebook_app_secret'];?>">
			</td>
		</tr>
		<tr>
			<th>Website</th>
			<td>
				Site URL : <?php echo home_url();?><br />
				Callback URL : <?php echo BBSE_THEME_WEB_URL;?>/snslogin/social-login-facebook-callback.php<br />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div class="titleH6" style="margin:10px 0 5px 0;">[페이스북 소셜(간편)로그인 안내]</div>
					- 앱 생성/관리 : <a href="https://developers.facebook.com/apps" target="_blank" title="페이스북 소셜(간편)로그인 바로가기">https://developers.facebook.com/apps <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/left_icon4.png" align="absmiddle" style="height:13px;width:auto;margin-right:10px;" alt="페이스북 앱 생성/관리"/></a><br />
					- Site URL은 My App - [연동할 앱 선택] - Settings  메뉴 페이지에서 Add Platform 클릭 - Website 선택하신 후 설정해 주세요.<br />
				</div>
			</td>
		</tr>
	</table>
	<br />	<br />


	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/social_login_google.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="구글 로그인"/>구글 로그인</th>
		</tr>
		<tr>
			<th>구글 사용여부</th>
			<td>
				<input type='radio' name='google_use_yn' id='google_use_yn' value='Y' style="border:0px;"<?php if($config['google']['google_use_yn'] == "Y") echo " checked"?>> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='google_use_yn' id='google_use_yn' value='N' style="border:0px;"<?php if(empty($config['google']['google_use_yn']) || $config['google']['google_use_yn'] == "N") echo " checked"?>> 사용안함			
			</td>
		</tr>
		<tr>
			<th>클라이언트 ID</th>
			<td>
				<input type="text" name="google_client_id" id="google_client_id" style="width:500px;" value="<?php echo $config['google']['google_client_id'];?>">
			</td>
		</tr>
		<tr>
			<th>클라이언트 보안 비밀</th>
			<td>
				<input type="text" name="google_client_secret" id="google_client_secret" style="width:500px;" value="<?php echo $config['google']['google_client_secret'];?>">
			</td>
		</tr>
		<tr>
			<th>웹 애플리케이션</th>
			<td>
				승인된 자바스크립트 원본 : <?php echo home_url();?><br />
				승인된 리디렉션 URI : <?php echo BBSE_THEME_WEB_URL;?>/snslogin/social-login-google-callback.php<br />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div class="titleH6" style="margin:10px 0 5px 0;">[구글 소셜(간편)로그인 안내]</div>
					- 프로젝트 생성/관리 : <a href="https://code.google.com/apis/console/ " target="_blank" title="구글 로그인 바로가기">https://code.google.com/apis/console/ <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/left_icon4.png" align="absmiddle" style="height:13px;width:auto;margin-right:10px;" alt="구글 프로젝트 생성/관리"/></a><br />
					- 승인된 자바스크립트 원본 및 승인된 리디렉션 URI은 연동할 어플리케이션의 - OAuth 2.0 클라이언트 ID 생성 과정 중에 입력란이 나타납니다.<br />
				</div>
			</td>
		</tr>
	</table>
	<br />	<br />

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/social_login_daum.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="다음 로그인"/>다음 로그인</th>
		</tr>
		<tr>
			<th>다음 사용여부</th>
			<td>
				<input type='radio' name='daum_use_yn' id='daum_use_yn' value='Y' style="border:0px;"<?php if($config['daum']['daum_use_yn'] == "Y") echo " checked"?>> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='daum_use_yn' id='daum_use_yn' value='N' style="border:0px;"<?php if(empty($config['daum']['daum_use_yn']) || $config['daum']['daum_use_yn'] == "N") echo " checked"?>> 사용안함			
			</td>
		</tr>
		<tr>
			<th>Client ID</th>
			<td>
				<input type="text" name="daum_client_id" id="daum_client_id" style="width:500px;" value="<?php echo $config['daum']['daum_client_id'];?>">
			</td>
		</tr>
		<tr>
			<th>Client Secret</th>
			<td>
				<input type="text" name="daum_client_secret" id="daum_client_secret" style="width:500px;" value="<?php echo $config['daum']['daum_client_secret'];?>">
			</td>
		</tr>
		<tr>
			<th>플랫폼(웹)</th>
			<td>
				플랫폼(Referer) : <?php echo home_url();?> (웹브라우저)<br />
				Callback URL : <?php echo BBSE_THEME_WEB_URL;?>/snslogin/social-login-daum-callback.php<br />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div class="titleH6" style="margin:10px 0 5px 0;">[다음 소셜(간편)로그인 안내]</div>
					- 앱 생성/관리 : <a href="https://developers.daum.net/console/affiliate" target="_blank" title="다음 로그인 바로가기">https://developers.daum.net/console/affiliate <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/left_icon4.png" align="absmiddle" style="height:13px;width:auto;margin-right:10px;" alt="다음 앱 생성/관리"/></a><br />
					- 플랫폼 및 Callback URL는 [앱 만들기] - API 키- OAuth + 선택 - 웹브라우저 탭을 선택하신 후 입력해 주세요.<br />
				</div>
			</td>
		</tr>
	</table>
	<br />	<br />

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/social_login_kakao.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="카카오 로그인"/>카카오 로그인</th>
		</tr>
		<tr>
			<th>카카오 사용여부</th>
			<td>
				<input type='radio' name='kakao_use_yn' id='kakao_use_yn' value='Y' style="border:0px;"<?php if($config['kakao']['kakao_use_yn'] == "Y") echo " checked"?>> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='kakao_use_yn' id='kakao_use_yn' value='N' style="border:0px;"<?php if(empty($config['kakao']['kakao_use_yn']) || $config['kakao']['kakao_use_yn'] == "N") echo " checked"?>> 사용안함			
			</td>
		</tr>
		<tr>
			<th>REST API 키</th>
			<td>
				<input type="text" name="kakao_rest_api_key" id="kakao_rest_api_key" style="width:500px;" value="<?php echo $config['kakao']['kakao_rest_api_key'];?>">
			</td>
		</tr>
		<tr>
			<th>플랫폼(웹)</th>
			<td>
				사이트 도메인 : <?php echo home_url();?><br />
				Redirect Path : <?php echo str_replace(home_url(),"",BBSE_THEME_WEB_URL);?>/snslogin/social-login-kakao-callback.php<br />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div class="titleH6" style="margin:10px 0 5px 0;">[카카오 소셜(간편)로그인 안내]</div>
					- 앱 생성/관리 : <a href="https://developers.kakao.com/apps" target="_blank" title="카카오 로그인 바로가기">https://developers.kakao.com/apps <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/left_icon4.png" align="absmiddle" style="height:13px;width:auto;margin-right:10px;" alt="카카오 앱 생성/관리"/></a><br />
					- 사이트 도메인 및 Redirect Path는 내 애플리케이션 - [연동할 어플리케이션 선택] - 설정 - 일반 메뉴 페이지의 플랫폼 추가 - 웹을 선택하신 후 입력해 주세요.<br />
				</div>
			</td>
		</tr>
	</table>
	<br />	<br />

	<table class="dataTbls overWhite collapse">
		<colgroup><col width="15%"><col width=""></colgroup>
		<tr>
			<th colspan="2"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/social_login_twitter.png" align="absmiddle" style="height:25px;width:auto;margin-right:10px;" alt="트위터 로그인"/>트위터 로그인</th>
		</tr>
		<tr>
			<th>트위터 사용여부</th>
			<td>
				<input type='radio' name='twitter_use_yn' id='twitter_use_yn' value='Y' style="border:0px;"<?php if($config['twitter']['twitter_use_yn'] == "Y") echo " checked"?>> 사용함&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='radio' name='twitter_use_yn' id='twitter_use_yn' value='N' style="border:0px;"<?php if(empty($config['twitter']['twitter_use_yn']) || $config['twitter']['twitter_use_yn'] == "N") echo " checked"?>> 사용안함			
			</td>
		</tr>
		<tr>
			<th>Consumer Key (API Key)</th>
			<td>
				<input type="text" name="twitter_api_key" id="twitter_api_key" style="width:500px;" value="<?php echo $config['twitter']['twitter_api_key'];?>">
			</td>
		</tr>
		<tr>
			<th>Consumer Secret (API Secret)</th>
			<td>
				<input type="text" name="twitter_api_secret" id="twitter_api_secret" style="width:500px;" value="<?php echo $config['twitter']['twitter_api_secret'];?>">
			</td>
		</tr>
		<tr>
			<th>Application Details</th>
			<td>
				Website : <?php echo home_url();?><br />
				Callback URL : <?php echo BBSE_THEME_WEB_URL;?>/snslogin/social-login-twitter-callback.php<br />
			</td>
		</tr>
		<tr>
			<td colspan="2" style="background: #f0f0f0;border-bottom:0px;line-height:20px;">
				<div class="borderBox" style="margin:0;padding:10px;">
					<div class="titleH6" style="margin:10px 0 5px 0;">[트위터 소셜(간편)로그인 안내]</div>
					- 앱 생성/관리 : <a href="https://apps.twitter.com/" target="_blank" title="트위터 로그인 바로가기">https://apps.twitter.com/ <img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/left_icon4.png" align="absmiddle" style="height:13px;width:auto;margin-right:10px;" alt="트위터 앱 생성/관리"/></a><br />
					- Website 및 Callback URL은 [연동할 어플리케이션 선택] - Settings 탭을 선택하신 후 입력해 주세요.<br />
				</div>
			</td>
		</tr>
	</table>

	</form>
	<br />

	<div id="btn_display_save" style="margin:40px 0;text-align:center;">
		<button type="button" class="button-bbse blue" onclick="submitForm('admin.php?page=bbse_commerce_member&cType=social'); return false;" style="width:150px;"> 저장 </button>
		<button type="button" class="button-bbse blue" onclick="resetForm('admin.php?page=bbse_commerce_member&cType=social'); return false;" style="width:150px;"> 초기화 </button>
	</div>
	<div class="clearfix" style="height:20px;"></div>


	<!--본문 내용(끝)-->

</div>

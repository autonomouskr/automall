<?php
if(!$_REQUEST['mType']) $mType='step01';
else $mType=$_REQUEST['mType'];
?>
	<div class="borderBox">
		<a href="https://autonomouskr.shop/wp-content/uploads/bbse-commerce/bbse_member_csv_sample.zip"><button type="button" class="button-small red" style="height:25px;margin-left:50px;float:right;">회원 DB 이전 CSV 샘플 다운로드</button></a>
		 * 회원 DB 이전(CSV)은 BBS e-Commerce의 DB 환경에 맞도록 CSV 파일을 작성하신 후 업로드를 통해 회원 정보를 일괄적으로 등록하는 작업을 진행합니다.<br/>
		- DB 이전에 필요한 파일 형식은 CSV 형식만을 지원하며, Excel 또는 Text 형식의 파일은 CSV 파일로 변환 하신 후 사용이 가능합니다.<br/>
		* <span class='emRed'>회원 DB 이전(CSV) 메뉴얼 바로가기 : </span><a href="http://www.bbsecommerce.com/commerce-plugin-manual/" target="_blank"><span style='color:#4C99BA;text-decoration:none;'>http://www.bbsecommerce.com/commerce-plugin-manual/</span></a>
	</div>

	<div class="clearfix" style="margin-top:30px"></div>

	<div class="memberStep01">
		<form name="dbUploadFrm" id="dbUploadFrm" method="post" action="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>admin/proc/bbse-commerce-member-dbinsert.exec.php" enctype="multipart/form-data">
			<input type="hidden" name="mType" id="mType" value="step01" />
			<div class="titleH5" style="margin:20px 0 10px 0; ">STEP 1. 회원 정보 CSV 파일 업로드 <span class="stepUseCheck" id="stepUseCheck-01" data-use="yes" data-target="memberStep01-contents" style="cursor:pointer;margin-left:30px;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/switch_yes.png" align="absmiddle" /></span></div>

			<div class="memberStep01-contents">
				<table class="dataTbls overWhite collapse">
					<colgroup><col width="15%"><col width=""></colgroup>
					<tr>
						<th>파일선택</th>
						<td><input type="file" name="dbFile" id="dbFile" value="" style="width:70%;" /></td>
					</tr>
				</table>

				<div class="clearfix"></div>
			</form>
			<div id="btn_csv_upload" style="margin:40px 0;text-align:center;">
				<button type="button" class="button-bbse blue" onClick="csvUpload();" style="width:150px;"> 파일 업로드 </button>
			</div>

			<div class="clearfix" style="margin-top:30px"></div>
			<div id="csvUploadMsg" class="borderBox" style="background-color:#ffffff;line-height:30px;">
				회원 정보 파일(.csv)을 업로드 해 주세요.
			</div>
		</div>
	</div>

	<div class="memberStep02" style="display:none;">
		<div class="clearfix" style="margin-top:30px;"></div>
		<div class="titleH5" style="margin:20px 0 10px 0;text-align:left;">STEP 2. 회원 정보 데이터 저장 <span class="stepUseCheck" id="stepUseCheck-02" data-use="yes" data-target="memberStep02-contents" style="cursor:pointer;margin-left:64px;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/switch_yes.png" align="absmiddle" /></span></div>

		<div class="memberStep02-contents">
			<form name="dbInsertFrm" id="dbInsertFrm" method="post" action="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>admin/proc/bbse-commerce-member-dbinsert.exec.php">
				<input type="hidden" name="mType" id="mType" value="step02" />
				<input type="hidden" name="cvsOriginalFile" id="cvsOriginalFile" value="" />
				<input type="hidden" name="cvsUploadFile" id="cvsUploadFile" value="" />
				<input type="hidden" name="dbTotalRow" id="dbTotalRow" value="" />

				<div class="clearfix" style="margin-top:30px;"></div>
				<table class="dataTbls overWhite collapse">
					<colgroup><col width="15%"><col width=""></colgroup>
					<tr>
						<th>비밀번호 설정</th>
						<td style="text-align:left;">
							<select name='pwType' id='pwType' onChange="pwTypeChange();">
								<option value=''>비밀번호 선택</option>
								<option value='phone'>CSV 전화번호</option>
								<option value='hp'>CSV 휴대전화번호</option>
								<option value='field'>CSV 비밀번호</option>
								<option value='direct'>직접입력</option>
							</select>
							<span id="pwDirectInput" style="margin-left:30px;display:none;">비밀번호 : <input type="text" name="pwDirect" id="pwDirect" value="" style="width:150px;" maxlength='16'/><span class='emRed' style="font-size:11px;margin-left:10px;">* 16자 이내로 입력해 주세요.</span></span>
							<span id="pwCsvMessage" style="margin-left:30px;display:none;">* CSV 파일의 <span id="pwTxtMsg"></span> 열(Column)을 회원의 비밀번호로 사용합니다.</span>
						</td>
					</tr>
				</table>
				<div class="clearfix" style="margin-top:30px;"></div>
				<div id="uploadResultArea" class="borderBox" style="background-color:#ffffff;line-height:30px;text-align:left;">
				</div>

				<div id="uploadResultMsg" style="margin-top:30px;height:25px;text-align:center;display:none;"><span class='emRed'>[데이터 저장중] 잠시만 기다려 주세요 !</span></div>

				<div class="clearfix" style="margin-top:30px;text-align:center;"></div>
				<div style="text-align:center;">
					<button type="button" class="button-bbse blue" onClick="csvInsert();" style="width:150px;"> 데이터 저장 </button>
				</div>
			</form>
		</div>
	</div>
	<div class="memberStep03" style="display:none;">
		<div class="clearfix" style="margin-top:30px;"></div>
		<div class="titleH5" style="margin:20px 0 10px 0;text-align:left;">STEP 3. 데이터 저장 결과 <span class="stepUseCheck" id="stepUseCheck-03" data-use="yes" data-target="memberStep03-contents" style="cursor:pointer;margin-left:64px;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/switch_yes.png" align="absmiddle" /></span></div>
		<div class="memberStep03-contents">
			<div class="clearfix" style="margin-top:30px"></div>
			<div id="dbCsvResultArea" class="borderBox" style="background-color:#ffffff;line-height:30px;">
			</div>
			<form name="csvMakeFrm" id="csvMakeFrm" method="post" action="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>admin/proc/bbse-commerce-member-dbinsert.exec.php">
				<input type="hidden" name="mType" id="mType" value="failCsvMake" />
				<input type="hidden" name="cvsMakeOriginalFile" id="cvsMakeOriginalFile" value="" />
				<textarea name="failData" id="failData" style="display:none;"></textarea>
			</form>

		</div>
	</div>

	<script>
	//콤마찍기
	function str_comma(str) {
		str = String(str);
		return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
	}

	jQuery(document).ready(function() {
		jQuery("#dbUploadFrm").ajaxForm();

	  //사용함/사용안함
	  jQuery('span.stepUseCheck').click(function(){
		var $status    = jQuery(this).data('use');
		var $target    = jQuery(this).data('target');
		if ($status == 'yes'){// 활성이면 비활성시키고 TR 감춤
			jQuery(this).data('use','no');
			var $btn = jQuery(this).find('img').attr('src').replace("yes", "no");
			jQuery(this).find('img').attr('src', $btn);
			jQuery('.'+$target).css('display','none');
		}
		else if ($status == 'no'){ // 비활성이면 활성시키고 TR 보여줌
			jQuery(this).data('use','yes');
			var $btn = jQuery(this).find('img').attr('src').replace("no.","yes.");
			jQuery(this).find('img').attr('src', $btn);
			jQuery('.'+$target).css('display','block');
		}
	  });
	});

	function pwTypeChange(){
		var pwTitle="";

		if(!jQuery("#pwType").val()){
			jQuery("#pwDirectInput").hide();
			jQuery("#pwCsvMessage").hide();
		}
		else if(jQuery("#pwType").val()=='direct'){
			jQuery("#pwDirectInput").show();
			jQuery("#pwCsvMessage").hide();
		}
		else{
			jQuery("#pwDirectInput").hide();

			if(jQuery("#pwType").val()=='phone') pwTitle="전화번호";
			else if(jQuery("#pwType").val()=='hp') pwTitle="휴대전화번호";
			else if(jQuery("#pwType").val()=='field') pwTitle="비밀번호";
			jQuery("#pwTxtMsg").text(pwTitle);
			jQuery("#pwCsvMessage").show();
		}
	}

	function csvInitialize(){
		jQuery(".memberStep02").hide();
		jQuery(".memberStep03").hide();
		jQuery("#pwType").val("");
		jQuery("#uploadResultArea").html("");
		jQuery("#dbCsvResultArea").html("");
		jQuery("#stepUseCheck-02").data('use','no');
		jQuery("#stepUseCheck-02").click();
		jQuery("#stepUseCheck-03").data('use','no');
		jQuery("#stepUseCheck-03").click();
	}

	function csvUpload() {
		if(!jQuery("#dbFile").val()){
			alert("업로드 할 파일(csv)을 선택해 주세요.        ");
			return;
		}

		csvInitialize();

		jQuery("#dbUploadFrm").ajaxSubmit({
			statusCode: {			
			  400: function() {
				alert("파일 업로드에 실패하였습니다.       ");
			  },			
			  500: function() {
				alert("파일 업로드에 실패하였습니다.       ");
			  }
			},						
			success: function(data) {
				//alert(data);
				var result=data.split("|||");
				if(result['0']=='success'){
					var remd=0;
					var idFlag=100;

					jQuery("#cvsOriginalFile").val(result['1']);
					jQuery("#cvsUploadFile").val(result['2']);
					jQuery("#uploadResultArea").html("<strong>1. 파일 명 : </strong>"+result['1']+"<br /><strong>2. 회원 수 : </strong>"+str_comma(result['3'])+"개<br /><strong>3. 필드 수 : </strong>"+str_comma(result['4'])+ "개<br />");
					jQuery("#dbFile").val("");
					jQuery("#dbTotalRow").val(result['3']);
					jQuery("#csvUploadMsg").hide();
					jQuery("#stepUseCheck-01").data('use','yes');
					jQuery("#stepUseCheck-01").click();
					jQuery(".memberStep02-contents").show();
					jQuery(".memberStep02").show();
				}
				else if(result['0']=='errorFileName'){
					alert("파일 업로드에 실패하였습니다.       ");
				}
				else if(result['0']=='errorFileExtend'){
					alert(".csv 파일 만 업로드가 가능합니다.       ");
				}
				else if(result['0']=='errorFileUpload'){
					alert("파일 업로드에 실패하였습니다.       ");
				}
				else if(result['0']=='errorFieldCount'){
					alert("CSV 파일의 열(Column) 개수가 올바르지 않습니다.       ");
				}
			}
		});
	}

	function csvInsert() {
		var msgStr="";
		var pwTitle="";
		var btnFailDown="";

		if(!jQuery("#cvsUploadFile").val()){
			alert("회원 정보 파일(csv)이 존재하지 않습니다.        ");
			return;
		}
		if(!jQuery("#pwType").val()){
			alert("회원 비밀번호를 선택해 주세요.        ");
			jQuery("#pwType").focus();
			return;
		}
		if(jQuery("#pwType").val()=='direct'){
			if(!jQuery("#pwDirect").val()){
				alert("회원 비밀번호를 입력해 주세요.        ");
				jQuery("#pwDirect").focus();
				return;
			}
			if(jQuery("#pwDirect").val().length>16){
				alert("회원 비밀번호를 16자 이내로 입력해 주세요.        ");
				jQuery("#pwDirect").focus();
				return;
			}
		}

		jQuery("#uploadResultMsg").show();

		if(confirm('해당 회원 정보 파일(csv)을 저장하시겠습니까?       ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-member-dbinsert.exec.php', 
				data: jQuery("#dbInsertFrm").serialize(), 
				success: function(data){
					//alert(data);
					jQuery('#uploadResultMsg').hide();
					var result=data.split("|||");
					if(result['0']=='success'){
						jQuery("#cvsOriginalFile").val("");
						jQuery("#cvsUploadFile").val("");
						jQuery("#pwType").val("");

						alert("회원 DB 이전(CSV)을 완료하였습니다.       ");

						msgStr +="* 형식에 맞지 않는 회원정보는 '오류 데이터'로 DB(데이터베이스)에 저장되지 않습니다.<br />";
						msgStr +="* '오류 데이터 CSV 다운로드'를 클릭하시면 저장에 실패 한 데이터를 CSV 파일로 다운로드 받으실 수 있습니다.<br />";
						msgStr +="&nbsp;&nbsp;다운 받으 신 오류 데이터를 수정하신 후 회원 DB 이전을 재 시도해 주세요.<br /><br /></div>";

						msgStr +="<strong>1. 파일 명 : </strong>"+result['1']+"<br />";

						var pwType=result['2'].split(",");
						if(pwType['1']=='phone') pwTitle="전화번호";
						else if(pwType['1']=='hp') pwTitle="휴대전화번호";
						else if(pwType['1']=='field') pwTitle="비밀번호";
						else if(pwType['1']=='direct'){
							pwTitle="직접입력 / "+pwType['2'];
						}

						msgStr +="<strong>2. 비밀번호 설정 : </strong>"+pwTitle+"<br />";
						msgStr +="<strong>3. 전체 회원 수 : </strong>"+str_comma(result['3'])+" 건<br />";
						msgStr +="<strong>4. 저장 완료 회원 수 : </strong>"+str_comma(result['4'])+" 건<br />";
						
						if(result['5']>0) btnFailDown="<button type=\"button\" class=\"button-small red\" onclick=\"fail_data_download();\" style=\"height:25px;margin-left:50px;\">오류 데이터 CSV 다운로드</button>";
						msgStr +="<span class='emRed'><strong>5. 저장 실패 회원 수 : </strong>"+str_comma(result['5'])+" 건</span>"+btnFailDown+"<br />";
						
						if(result['5']>0){
							var failCount=result['6'].split("|");
							if(failCount['0']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 회원 아이디 없음 : "+str_comma(failCount['0'])+" 건</span> (회원 아이디 입력)<br />";
							if(failCount['1']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 회원아이디 중복 : "+str_comma(failCount['1'])+" 건</span> (중복 제거)<br />";
							if(failCount['2']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 회원명 없음 : "+str_comma(failCount['2'])+" 건</span> (회원명 입력)<br />";
							if(failCount['3']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 비밀번호 없음 : "+str_comma(failCount['3'])+" 건</span> ("+pwTitle+" 입력)<br />";
							if(failCount['4']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 비밀번호 길이 초과 : "+str_comma(failCount['4'])+" 건</span> ("+pwTitle+" 16자 이내로 입력)<br />";

							jQuery("#cvsMakeOriginalFile").val(result['1']);
							jQuery("#failData").val(result['7']);
						}

						jQuery("#dbCsvResultArea").html(msgStr);
						jQuery("#stepUseCheck-02").data('use','yes');
						jQuery("#stepUseCheck-02").click();
						jQuery(".memberStep03-contents").show();
						jQuery(".memberStep03").show();
					}
				}, 
				error: function(data, status, err){
					jQuery('#uploadResultMsg').hide();
					alert("서버와의 통신이 실패했습니다.   ");
				}
			});	
		}
		else jQuery('#uploadResultMsg').hide();
	}

	function fail_data_download() {
		jQuery("#csvMakeFrm").submit();
	}

	</script>

	<div class="clearfix" style="height:20px;"></div>

<?php
/*
 [테마 수정 시 주의사항]
 1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
 업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
 2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
 */

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

?>

<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
	<script language="javascript">
	
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

	function csvInitialize(){
		jQuery(".goodsStep02").hide();
		jQuery(".goodsStep03").hide();
		jQuery("#dbCategory").val("");
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
					jQuery("#uploadResultArea").html("<strong>1. 파일 명 : </strong>"+result['1']+"<br /><strong>2. 상품 수 : </strong>"+str_comma(result['3'])+"개<br /><strong>3. 필드 수 : </strong>"+str_comma(result['4'])+ "개<br />");
					jQuery("#dbFile").val("");
					jQuery("#dbTotalRow").val(result['3']);
					jQuery("#csvUploadMsg").hide();
					jQuery("#stepUseCheck-01").data('use','yes');
					jQuery("#stepUseCheck-01").click();
					jQuery(".goodsStep02-contents").show();
					jQuery(".goodsStep02").show();
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
		var btnFailDown="";

		if(!jQuery("#cvsUploadFile").val()){
			alert("상품 파일(csv)이 존재하지 않습니다.        ");
			return;
		}
		if(!jQuery("#dbCategory").val()){
			alert("상품을 저장할 카테고리를 선택해 주세요.        ");
			jQuery("#dbCategory").focus();
			return;
		}
		
		jQuery("#uploadResultMsg").show();

		if(confirm('해당 상품 파일(csv)을 저장하시겠습니까?       ')){
			jQuery.ajax({
				type: 'post', 
				async: false, 
				url: '<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/proc/bbse-commerce-popup-member-list-dbinsert.exec.php', 
				data: jQuery("#dbInsertFrm").serialize(), 
				success: function(data){
					//alert(data);
					jQuery('#uploadResultMsg').hide();
					var result=data.split("|||");
					if(result['0']=='success'){
						jQuery("#cvsOriginalFile").val("");
						jQuery("#cvsUploadFile").val("");
						jQuery("#dbCategory").val("");

						alert("대량 상품등록(CSV)을 완료하였습니다.       \n이미지 변환 작업을 진행해 주세요.       ");

						msgStr +="<div style='line-height:20px;'><span class='emRed'>* 대량 상품등록(CSV)을 완료하였습니다. 이미지 변환 작업을 진행해 주세요.</span><br />";
						msgStr +="* 형식에 맞지 않는 상품정보는 '오류 데이터'로 DB(데이터베이스)에 저장되지 않습니다.<br />";
						msgStr +="* '오류 데이터 CSV 다운로드'를 클릭하시면 저장에 실패 한 데이터를 CSV 파일로 다운로드 받으실 수 있습니다.<br />";
						msgStr +="&nbsp;&nbsp;다운 받으 신 오류 데이터를 수정하신 후 상품등록을 재 시도해 주세요.<br /><br /></div>";

						msgStr +="<strong>1. 파일 명 : </strong>"+result['1']+"<br />";
						msgStr +="<strong>2. 카테고리 명 : </strong>"+result['2']+"<br />";
						msgStr +="<strong>3. 전체 상품 수 : </strong>"+str_comma(result['3'])+" 건<br />";
						msgStr +="<strong>4. 저장 완료 상품 수 : </strong>"+str_comma(result['4'])+" 건<br />";

						if(btnFailDown>0) btnFailDown="<button type=\"button\" class=\"button-small red\" onclick=\"fail_data_download();\" style=\"height:25px;margin-left:50px;\">오류 데이터 CSV 다운로드</button>";
						msgStr +="<span class='emRed'><strong>5. 저장 실패 상품 수 : </strong>"+str_comma(result['5'])+" 건</span>"+btnFailDown+"<br />";
						
						if(result['5']>0){
							var failCount=result['6'].split("|");
							if(failCount['0']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 상품명 없음 : "+str_comma(failCount['0'])+" 건</span> (상품명 입력)<br />";
							if(failCount['1']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 소비자가 0원 : "+str_comma(failCount['1'])+" 건</span> (0원 이상의 가격 입력)<br />";
							if(failCount['2']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 판매가 0원 : "+str_comma(failCount['2'])+" 건</span> (0원 이상의 가격 입력)<br />";
							if(failCount['3']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 판매가>소비자가 : "+str_comma(failCount['3'])+" 건</span> (판매가를 소비자가보다 작게 입력)<br />";
							if(failCount['4']>0) msgStr +="&nbsp;&nbsp;<span class='emRed'>- 상품이미지 없음 : "+str_comma(failCount['4'])+" 건</span> (상품이미지 입력)<br />";

							jQuery("#cvsMakeOriginalFile").val(result['1']);
							jQuery("#failData").val(result['7']);
						}

						jQuery("#dbCsvResultArea").html(msgStr);
						jQuery("#stepUseCheck-02").data('use','yes');
						jQuery("#stepUseCheck-02").click();
						jQuery(".goodsStep03-contents").show();
						jQuery(".goodsStep03").show();
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

</head>
<body>
	<div class="borderBox">
	<div class="wrap">
		<a href="https://license.bbsetheme.com/download_zip/bbse_member_csv_sample.zip"><button type="button" class="button-small red" style="height:25px;margin-left:50px;float:right;">일괄 회원 등록 CSV 샘플 다운로드</button></a>
		<div class="clearfix" style="margin-top:30px"></div>

    	<div class="goodsStep01">
    		<form name="dbUploadFrm" id="dbUploadFrm" method="post" action="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>admin/proc/bbse-commerce-member-list-dbinsert.exec.php" enctype="multipart/form-data">
    			<input type="hidden" name="mType" id="mType" value="step01" />
    			<div class="titleH5" style="margin:20px 0 10px 0; ">STEP 1. 회원 CSV 파일 업로드 
    			<span class="stepUseCheck" id="stepUseCheck-01" data-use="yes" data-target="goodsStep01-contents" style="cursor:pointer;margin-left:30px;"></span></div>
    
    			<div class="goodsStep01-contents">
    				<table class="dataTbls overWhite collapse">
    					<colgroup><col width="15%"><col width=""></colgroup>
    					<tr>
    						<th>파일선택</th>
    						<td><input type="file" name="dbFile" id="dbFile" value="" style="width:70%;" /></td>
    					</tr>
    				</table>
    			</div>
			<div class="clearfix"></div>
			</form>
			<div id="btn_csv_upload" style="margin:40px 0;text-align:center;">
				<button type="button" class="button-bbse blue" onClick="csvUpload();" style="width:150px;"> 파일 업로드 </button>
			</div>

			<div class="clearfix" style="margin-top:30px"></div>
			<div id="csvUploadMsg" class="borderBox" style="background-color:#ffffff;line-height:30px;">
				회원 파일(.csv)을 업로드 해 주세요.
			</div>
		</div>
	</div>

	<div class="wrap">
    	<div class="goodsStep03" style="display:none;">
    		<div class="clearfix" style="margin-top:30px;"></div>
    		<div class="titleH5" style="margin:20px 0 10px 0;text-align:left;">STEP 2. 데이터 저장 결과 <span class="stepUseCheck" id="stepUseCheck-03" data-use="yes" data-target="goodsStep03-contents" style="cursor:pointer;margin-left:64px;"><img src="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>/images/switch_yes.png" align="absmiddle" /></span></div>
    		<div class="goodsStep03-contents">
    			<div class="clearfix" style="margin-top:30px"></div>
    			<div id="dbCsvResultArea" class="borderBox" style="background-color:#ffffff;line-height:30px;">
    			</div>
    			<form name="csvMakeFrm" id="csvMakeFrm" method="post" action="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>admin/proc/bbse-commerce-member-list-dbinsert.exec.php">
    				<input type="hidden" name="mType" id="mType" value="failCsvMake" />
    				<input type="hidden" name="cvsMakeOriginalFile" id="cvsMakeOriginalFile" value="" />
    				<textarea name="failData" id="failData" style="display:none;"></textarea>
    			</form>
    
    		</div>
    	</div>
	</div>
	</div>
</body>
</html>

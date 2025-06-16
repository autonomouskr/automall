<?php 

	if($_POST['member_list']!="") {
		$member_list = unserialize(base64_decode($_POST['member_list']));
		if(count($member_list) > 0){
			$view_user_no = @implode(',', $member_list);
		}
	}

?>
<script language="javascript">
function checkForNumber(){
	var key = event.keyCode;
	if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
		event.returnValue = false;
	}
}
jQuery(document).ready(function() {
	function initInputs() {
		var inputs = jQuery('input');
		inputs.each(function () {
			var title = jQuery(this).attr("title"),
				value = jQuery(this).val();
			if (!value) {
				jQuery(this).val(title);
			}
			jQuery(this).bind("focusin", function (e) {
				if (jQuery(this).val() == title)
					jQuery(this).val("");
			});
			jQuery(this).bind("focusout", function (e) {
				if (jQuery(this).val().length == 0)
					jQuery(this).val(title);
			});
		});
	}
	initInputs();

	jQuery(".fileButton").click(function() {
		jQuery("#custom_upfile").click();
	});
	jQuery("#custom_upfile").change(function() {
		var splitPath = jQuery("#custom_upfile").val().split("\\");
		jQuery("#view_upfile").val(splitPath[splitPath.length-1]);
	});
	jQuery("#cate1").change(function() {
		if(jQuery("#cate1").val()=="") {
			jQuery("#cate2_view").html("");
		}else{
			jQuery.post("member_search",{sMode:"search", cate1:jQuery("#cate1").val()},
				function(data, status) {
					var res = data.split("|");
					if(res[0] == "ok"){
						jQuery("#cate2_view").html(res[1]);
					}
			});
		}
	});
});
function displayDelete(no) {

	var displayList = jQuery("#main_product_list").val();
	var splitList = displayList.split(",");
	var chgList = "";

	for(i=0;i<splitList.length;i++){
		if(splitList[i]!=no) {
			chgList += splitList[i]+",";
		}
	}
	jQuery("#main_product_list").val(chgList.substring(0,chgList.length-1));
	jQuery("#display-item-"+no).remove();
	if(jQuery("[id^='display_no_']").size() == 0) {
		jQuery("#display-product-list").hide();
	}
}
function memberDisplay() {
	var chked = jQuery("[id='check[]']").filter(":checked");
	if(chked.length==0) {
		alert("추가할 대상을 선택해주세요.");
		return;
	}

	var product = "";
	var addCheckMsg = "";
	var addCheckFlag = true;
	var addCount = 0;
	var addProduct = "";
	jQuery(chked).each(function (i,e) {

		addCheckFlag = true;
		jQuery("[id^='display_no_']").each(function (i2,e2) {
			if(jQuery(e).val()==jQuery(e2).val()) {
				addCheckFlag = false;
				addCheckMsg += jQuery("#user_id_"+jQuery(e).val()).val() + " 회원은 이미 추가되어 있습니다.\n";
			}
		});
		var prevSize = (jQuery("[id^='display_no_']").length+1);
		if(addCheckFlag == true) {
			lineClass = "";
			product += "<tr "+lineClass+"valign=\"middle\" id='display-item-"+jQuery(e).val()+"'>";
			product += "<td align='center' style='width:150px;padding:3px;border-bottom:1px dotted #ddd;'>"+jQuery("#user_id_"+jQuery(e).val()).val()+"</td>";
			product += "<td align='center' style='width:150px;padding:3px;border-bottom:1px dotted #ddd;'>"+jQuery("#name_"+jQuery(e).val()).val()+"</td>";
			product += "<td align='center' style='width:150px;padding:3px;border-bottom:1px dotted #ddd;'>"+jQuery("#email_"+jQuery(e).val()).val()+"</td>";
			product += "<td align='center' style='width:70px;padding:3px;border-bottom:1px dotted #ddd;'><input type=\"button\" class=\"button-secondary\" onclick=\"displayDelete("+jQuery(e).val()+");\" value=\"삭제\" /><input type='hidden' id='display_no_"+jQuery(e).val()+"' name='display_no_"+jQuery(e).val()+"' value='"+jQuery(e).val()+"'></td>";
			product += "</tr>";
			addProduct += jQuery(e).val()+",";
			addCount++;
		}
	});

	if(addCheckMsg) {
		alert(addCheckMsg);
		return;
	}

	jQuery("#display-product-list").show();
	if(jQuery("#main_product_list").val()!="") {
		jQuery("#main_product_list").val(jQuery("#main_product_list").val()+","+(addProduct.substring(0,addProduct.length-1)));
	}else{
		jQuery("#main_product_list").val(jQuery("#main_product_list").val()+(addProduct.substring(0,addProduct.length-1)));
	}
	if(jQuery("[id^='display_no_']").size() == 0) {
		jQuery("#display-product-list").show();
	}
	jQuery("#display-product-list-body").append(product);
	tb_remove();

}
function emailProc() {
	var cnt = jQuery("[id^='display_no_']").size();
	if(cnt==0) {
		alert("발송 대상을 추가해주세요.");
		return;
	}
	if(jQuery("#mail_title").val()=="" || jQuery("#mail_title").val()=="메일 제목"){
		alert("메일 제목을 입력해주세요.");
		jQuery("#mail_title").focus();
		return;
	}

	var ed = tinyMCE.get('mail_content');
	jQuery("#mail_content").val(ed.getContent({format : 'raw'}));  // raw(비쥬얼) / text(텍스트)

	if(ed.getContent()=="") {
		alert("메일 내용을 입력해주세요.");
		return;
	}
	if(confirm("메일을 발송하시겠습니까?")) {
		jQuery("#mailForm").submit();
	}
	return;

}

// 회원 등록 팝업
function member_list_popup(){
	var chkList=jQuery("#main_product_list").val();
	tb_show("회원목록", "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-member-list.php?chkList="+chkList+"&#38;TB_iframe=true");
}
</script>
<div>

	<div class="borderBox">
		* 자동 메일설정은 BBS e-Commerce > 상점관리 > 자동메일 설정에서 설정하실 수 있습니다. <a href="admin.php?page=bbse_commerce_config&cType=mail" class="add-new-h2">자동메일설정</a>
	</div>

	<table class="dataTbls overWhite collapse">
		<colgroup><col width=""></colgroup>
		<tr>
			<th align="left">개별메일발송 <a href="javascript:member_list_popup();" class="add-new-h2" title="회원목록">발송대상추가</a><th>
		</tr>
		<tr>
			<th>
				<form id="mailForm" name="mailForm" method="post" enctype="multipart/form-data">
				<input type="hidden" id="mvrun" name="mvrun" value="email_proc">
				<input type="text" name="main_product_list" id="main_product_list" style="width:750px;display:none;" value="<?php echo $_GET['user_no'] ? $_GET['user_no'] : $view_user_no?>" readonly/>
				<?php if($_GET['user_no'] || count($member_list) > 0){$member_view="";}else{$member_view="none";}?>
				<table class="wp-list-table widefat fixed posts" border="0" cellspacing="0" style='width:750px;display:<?php echo $member_view;?>;' id="display-product-list">
					<thead style="background-color:#F0F0F0;">
					<tr style="height:30px;">
						<th scope="col" class="manage-column" style='border-bottom:1px solid #bbb;width:150px;text-align:center;'>아이디</th>
						<th scope="col" class='manage-column' style='border-bottom:1px solid #bbb;width:150px;text-align:center;'>이름</th>
						<th scope="col" class='manage-column' style='border-bottom:1px solid #bbb;text-align:center;'>이메일</th>
						<th scope="col" class='manage-column' style='border-bottom:1px solid #bbb;text-align:center;cursor:pointer;width:40px;'>삭제</th>
					</tr>
					</thead>
					
					<tbody id="display-product-list-body">
					<?php
					if($_GET['user_no']){
						$basic_user = $wpdb->get_row("SELECT user_id, name, email FROM bbse_commerce_membership WHERE user_no='".$_GET['user_no']."'");
					?>
					<tr valign="middle" id="display-item-<?php echo $_GET['user_no']?>">
						<td align="center" style="width:150px;padding:3px;border-bottom:1px dotted #ddd;"><?php echo $basic_user->user_id?></td>
						<td align="center" style="width:150px;padding:3px;border-bottom:1px dotted #ddd;"><?php echo $basic_user->name?></td>
						<td align="center" style="width:150px;padding:3px;border-bottom:1px dotted #ddd;"><?php echo $basic_user->email?></td>
						<td align="center" style="width:70px;padding:3px;border-bottom:1px dotted #ddd;">
							<button type="button" class="fileButton button_s" onclick="displayDelete(<?php echo $_GET['user_no']?>);" style="cursor:pointer;width:40px;">삭제</button>
							<input type="hidden" id="display_no_<?php echo $_GET['user_no']?>" name="display_no_<?php echo $_GET['user_no']?>" value="<?php echo $_GET['user_no']?>">
						</td>
					</tr>
					<?php
					}
					if(count($member_list) > 0){
						for($i=0 ; $i<count($member_list) ; $i++){
							$user_no = $member_list[$i];

							$basic_user = $wpdb->get_row("SELECT user_id, name, email FROM bbse_commerce_membership WHERE user_no='".$user_no."'");
					?>
					<tr valign="middle" id="display-item-<?php echo $user_no?>">
						<td align="center" style="width:150px;padding:3px;border-bottom:1px dotted #ddd;"><?php echo $basic_user->user_id?></td>
						<td align="center" style="width:150px;padding:3px;border-bottom:1px dotted #ddd;"><?php echo $basic_user->name?></td>
						<td align="center" style="width:150px;padding:3px;border-bottom:1px dotted #ddd;"><?php echo $basic_user->email?></td>
						<td align="center" style="width:70px;padding:3px;border-bottom:1px dotted #ddd;">
							<button type="button" class="fileButton button_s" onclick="displayDelete(<?php echo $user_no?>);">삭제</button>
							<input type="hidden" id="display_no_<?php echo $user_no?>" name="display_no_<?php echo $user_no?>" value="<?php echo $user_no?>">
						</td>
					</tr>
					<?php
						}
					}
					?>
					</tbody>
				</table>

				<!-- 메일내용 -->
				<table style="font-family:돋움,dotum,applegothic,sans-serif;background-color:#ffffff;border:1px solid #ddd;margin:20px auto;" border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td align="left">
							<div style="background:#313a4a;overflow:hidden;text-align:left;">
							<table style="text-align:left;letter-spacing:-1px;font-size:11px;border-bottom:0px solid #707070" border="0" cellspacing="0" cellpadding="0" width="100%">
								<tr>
									<td style="letter-spacing:-1px;font-family:나눔고딕,굴림,gulim,돋움,dotum;font-size:25px;line-height:36px;font-weight:bold;color:#f8f8f4;text-align:left;" height="50" valign="middle"><input type="text" id="mail_title" name="mail_title" value="" style="letter-spacing:-1px;font-family:나눔고딕,굴림,gulim,돋움,dotum;font-size:25px;line-height:36px;font-weight:bold;color:#999999;text-align:left;height:45px;width:80%;" title="메일 제목"> <input type="button" name="" id="search-submit" class="button" value="발송하기" style="height:45px;" onclick="emailProc();"/>
										
									</td>
								</tr>
							</table>
							</div>

							<table style="width:100%;text-align:left;" border="0" cellspacing="0" cellpadding="0" align="left">
							<tr>
							<td valign="top">
							<?php
							$editor_args = array(
								'quicktags' => false,
								'media_buttons' => true
							);
							wp_editor("", 'mail_content', $editor_args);
							?> </td>
							</tr>
							<tr>
								<td>
									첨부파일
									<input type='text' name="view_upfile" id="view_upfile" style='width:430px;' readonly data-validation="required"/>
									<input type='file' name="custom_upfile" id="custom_upfile" value="" style="display:none;">
									<input type="button" class="fileButton button-secondary" value="찾아보기"/>
								</td>
							</tr>
							</table>

						</td>
					</tr>
				</table>
				<!-- //메일내용 -->
				
				</form>
			</th>
		</tr>
	</table>
	
</div>



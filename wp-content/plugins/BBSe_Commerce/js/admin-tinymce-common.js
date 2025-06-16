function bbse_commerce_tinymce_popup_remove(){
	parent.tinyMCE.activeEditor.execCommand("bbseCommerceTinymcePopupRemove");
}

// 숫자만 입력
function opCheck_number(){
	var key = event.keyCode;
	if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
		event.returnValue = false;
	}
}

function shortcode_make(){
	var selectOption=jQuery("#selectOption").val();
	var procUrl=jQuery("#pluginUrl").val()+'admin/proc/bbse-commerce-tinymce-make-shortcode.exec.php';

	if(selectOption=='youtube'){
		if(!jQuery("#op_url").val()){
			alert("유튜브 동영상의 URL을 입력해 주세요.   ");
			jQuery("#op_url").focus();
			return false;
		}
		if(!jQuery("#op_width").val()){
			alert("동영상의 가로 크기를 입력해 주세요.   ");
			jQuery("#op_width").focus();
			return false;
		}
	}

	jQuery.ajax({
		type: 'post', 
		async: false, 
		url: procUrl, 
		data: jQuery("#sc_opFrm").serialize(), 
		success: function(data){
			//alert(data);
			var result = data.split("|||"); 
			if(result[0]=='success'){
				//if (typeof scnShortcodeMeta != "undefined") {
				parent.tinyMCE.activeEditor.execCommand("mceInsertContent", false, result[1]);
				bbse_commerce_tinymce_popup_remove();
				//}
			}
			else{
				alert("Shortcode 생성에 실패했습니다.   ");
			}
		}, 
		error: function(data, status, err){
			alert("서버와의 통신이 실패했습니다.   ");
		}
	});	
}

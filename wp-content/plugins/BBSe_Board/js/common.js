// 팝업 띄우기
function open_window(name, url, left, top, width, height, toolbar, menubar, statusbar, scrollbar, resizable){
	toolbar_str = toolbar?'yes':'no';
	menubar_str = menubar?'yes':'no';
	statusbar_str = statusbar?'yes':'no';
	scrollbar_str = scrollbar?'yes':'no';
	resizable_str = resizable?'yes':'no';
	window.open(url, name, 'left=' + left + ',top=' + top + ',width=' + width + ',height=' + height + ',toolbar=' + toolbar_str + ',menubar=' + menubar_str + ',status=' + statusbar_str + ',scrollbars=' + scrollbar_str + ',resizable=' + resizable_str);
}

// html 사용여부
function check_use_html(obj){
	var c_n;
	if(!obj.checked){
		obj.value = 1;
	
	}else{
		c_n = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
		if(c_n) obj.value = 1;
		else obj.value = 2;
	}
}

// 게시물 이동팝업 띄우기
function move_open(url){
	var i, chked = 0;
	for(i = 0; i < document.getElementsByName("check[]").length; i++){
		if(document.getElementsByName("check[]")[i].type == 'checkbox'){
			if(document.getElementsByName("check[]")[i].checked){
				chked = 1;
			}
		}
	}

	if(chked){
		checkvalue = '';
		for(i = 0; i < document.getElementsByName("check[]").length; i++){
			if(document.getElementsByName("check[]")[i].type == 'checkbox'){
				if(document.getElementsByName("check[]")[i].checked){
					checkvalue = document.getElementsByName("check[]")[i].value + '_' + checkvalue;
				}
			}
		}	
		window.open(url + "&check=" + checkvalue, "move_popup", "left=0,top=0,width=380,height=180,toolbar=no,menubar=no,status=no,scrollbars=no,resizable=no");
	}else{	
		alert("이동시킬 게시물을 선택해주세요.");
	}
}


// 게시물 이동/복사
function move_page(url, mode){
	var i, chked = 0;
	if(mode == "move") var mode_txt = "이동";
	else if(mode == "copy") var mode_txt = "복사";
	for(i = 0; i < document.getElementsByName("check[]").length; i++){
		if(document.getElementsByName("check[]")[i].type == 'checkbox'){
			if(document.getElementsByName("check[]")[i].checked){
				chked = 1;
			}
		}
	}

	if(chked){
		checkvalue = '';
		for(i = 0; i < document.getElementsByName("check[]").length; i++){
			if(document.getElementsByName("check[]")[i].type == 'checkbox'){
				if(document.getElementsByName("check[]")[i].checked){
					checkvalue = document.getElementsByName("check[]")[i].value + '_' + checkvalue;
				}
			}
		}	
		location.href = url + "&check=" + checkvalue + "&mode=" + mode;
	}else{	
		alert(mode_txt + "할 게시물을 선택해주세요.");
	}
}

// 전체선택/ 해제
var AllorNothing = false;
function allselect(){
	if(AllorNothing == false){
		AllorNothing = true;
	
	}else{
		AllorNothing = false;
	}

	if(document.getElementsByName("check[]").length > 0){
		if(document.getElementsByName("check[]").length >= 1) {
			for(i = 0; i < document.getElementsByName("check[]").length; i++){
				document.getElementsByName("check[]")[i].checked = AllorNothing;
			}
		
		}else{
			document.getElementsByName("check[]")[i].checked = AllorNothing;
		}
	}
}

// 주석 사용할 수 없게 하기
function tag_check(a){
	var searchfrm = a.value;
	searchfrm = " " + searchfrm;
	var search = "\<\!\-\-";
	find1 = 0; find2 = 0;
	if(searchfrm != ""){
		while(find1 >= 0){
			find1 = searchfrm.indexOf(search, find2);
			if(find1 > 0){
				alert("\<\!\-\- 는 사용할 수 없습니다!");
				find2 = find1 + 1;
				a.value = "";
				return false;
			}
		}
	}
}

// 게시물 삭제
function del(url){
	var i, j = 0, k = 0;
	for(i = 0; i < document.getElementsByName("check[]").length; i++){
		if(document.getElementsByName("check[]")[i].checked) k++;
	}
	if(k < 1){
		alert("삭제하실 게시물을 선택해 주세요");
		return false;
	
	}else{
		if(confirm("삭제하시겠습니까?")){
			document.list_frm.action = url;      
			document.list_frm.submit(); 
			return true;
		}else{
			return false;
		}
	}
}
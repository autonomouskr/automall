// 숫자만 입력
function checkForNumber(){
	var key = event.keyCode;
	if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
		event.returnValue = false;
	}
}

// 주석사용 방지
function tag_check(a){
	var searchfrm = a.value;
	searchfrm = " " + searchfrm;
	var search = "\<\!\-\-";
	find1 = 0;
	find2 = 0;
	
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

// 공백 체크
function ChkSpace(strValue){
	if(strValue.indexOf(" ") >= 0){
		return true;
	}else{
		return false;
	}
}

// 한글 체크
function ChkHan(strValue){
	for(i = 0; i < strValue.length; i++){
		var a = strValue.charCodeAt(i);
		if(a > 128){
			return true;
		}else{
			return false;
		}
	}
}

// 메일 형식 체크
function ChkMail(strValue){
	if(ChkSpace(strValue)){
		return false;
	}else if(strValue.indexOf("/") != -1 || strValue.indexOf(";") != -1 || ChkHan(strValue)){
		return false;
	}else if((strValue.length != 0) && (strValue.search(/(\S+)@(\S+)\.(\S+)/) == -1)){
		return false;
	}else{ 
		return true;
	}
}

// 숫자 콤마 추가
function setComma(n) {
	var reg = /(^[+-]?\d+)(\d{3})/;
	n += '';
	while (reg.test(n))
	n = n.replace(reg, '$1' + ',' + '$2');

	return n;
}

<?php 
    $config = $wpdb->get_row("select * from `bbse_commerce_membership_config`");
    $storage_code = $_REQUEST['storage_code'];
?>

<style>
    .check-container::before{
        content: "✔";
        color:red;
        font-size: 20px;
        font-weight:bold;
        margin-right: 8px;
    }
</style>

<script language="javascript">

function checkForNumber(){
	var key = event.keyCode;
	if(!(key == 8 || key == 9 || key == 13 || key == 46 || key == 144 || (key >= 48 && key <= 57) || (key >= 96 && key <= 105) || key == 190)){
		event.returnValue = false;
	}
}

function zipcode_search(){
	window.open("<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>zipcode.php", "zipcode_search", "width=400,height=400,scrollbars=yes");
}

<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){ /* Daum 우편번호 API */?>
    function openDaumPostcode(){
    	new daum.Postcode({
    		oncomplete: function(data){
    			if(data.userSelectedType === 'R'){
                    // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
                    // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                    var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
                    var extraRoadAddr = ''; // 도로명 조합형 주소 변수
    
                    // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                    // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                    if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                        extraRoadAddr += data.bname;
                    }
                    // 건물명이 있고, 공동주택일 경우 추가한다.
                    if(data.buildingName !== '' && data.apartment === 'Y'){
                       extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                    }
                    // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                    if(extraRoadAddr !== ''){
                        extraRoadAddr = ' (' + extraRoadAddr + ')';
                    }
                    // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
                    if(fullRoadAddr !== ''){
                        fullRoadAddr += extraRoadAddr;
                    }
    
    				document.getElementById('zipcode').value = data.zonecode;
    				document.getElementById('addr1').value = fullRoadAddr;
    			}
    			else{
    				document.getElementById('zipcode').value = data.postcode1+"-"+data.postcode2;
    				document.getElementById('addr1').value = data.jibunAddress;
    			}
    
    			document.getElementById('addr2').focus();
    		}
    	}).open();
    }
<?php }?>

function save_storage(action_url){

	var frm = document.storage_frm;
	<?php if($_REQUEST['mode']=="add"){?>
	
    	if(frm.storage_name.value == "") {
    		alert("창고명을 입력해주세요.");
    		frm.storage_name.focus();
    		return false; 
    	}
    	
    	if(frm.zipcode.value == "") {
    		alert("우편번호를 입력해주세요.");
    		frm.zipcode.focus();
    		return false; 
    	}
    	
    	if(frm.addr1.value == "") {
    		alert("주소를 입력해주세요.");
    		frm.addr1.focus();
    		return false; 
    	}
    	
    	if(frm.addr2.value == "") {
    		alert("주소를 입력해주세요.");
    		frm.addr1.focus();
    		return false; 
    	}
    	
    	if(frm.manager_id.value == "") {
    		alert("담당자 ID 입력해주세요.");
    		frm.manager_id.focus();
    		return false; 
    	}
    	
    	if(frm.manager_name.value == "") {
    		alert("담당자 이름 입력해주세요.");
    		frm.manager_name.focus();
    		return false; 
    	}
/*     	
    	if(!frm.hp_1.value){
    		alert("휴대전화번호를 입력해주세요.");
    		frm.hp_1.focus();
    		return false;
    	}
    	
    	if(!frm.hp_2.value){
    		alert("휴대전화번호를 입력해주세요.");
    		frm.hp_2.focus();
    		return false;
    	}
    	
    	if(!frm.hp_3.value){
    		alert("휴대전화번호를 입력해주세요.");
    		frm.hp_3.focus();
    		return false;
    	} 
*/
	<?php }?>
	
	<?php if($_REQUEST['mode']=="edit"){?>
	if(confirm("정보를 수정하시겠습니까?")){
		frm.action = action_url;
		frm.submit();
	}
	<?php }else{?>
		frm.action = action_url;
		frm.submit();
	<?php }?>
	
}

function closeDaumPostcode() {
    jQuery("#commerceZipcodeLayer").css("display","none");
}

function search_manager(){
	var userClass = 1;
	var popupTitle="담당자조회";
	var tbHeight = window.innerHeight * .60;
	var tbWidth = window.innerWidth * .35;
	tb_show(popupTitle, "<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL ?>admin/bbse-commerce-manager-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;userClass="+userClass+"&#38;TB_iframe=true");
	//tb_show(popupTitle, "http://localhost/wp-content/plugins/BBSe_Commerce/admin/bbse-commerce-manager-popup.php?popupTitle="+popupTitle+"&#38;height="+tbHeight+"&#38;width="+tbWidth+"&#38;userClass="+userClass+"&#38;TB_iframe=true");
	
	return false;
	
}
	
</script>
	
<?php

if($_REQUEST['mode']=="add") {
	$mode_tit = "창고추가";
	$strun = "add_proc";
	$button_name = "등록";
}else{
	$mode_tit = "창고정보";
	$strun = "edit_proc";
	$button_name = "수정";
	$result = $wpdb->get_row("select * from autopole3144.tbl_storage where storage_code = '".$storage_code."'");
}

$USER_ACTION_URL = "admin.php?page=bbse_commerce_storage&mode=".$_REQUEST['mode'];

?>
	<div class="wrap">
		<div id="popup-register-storage">
			<form method="post" id="storage_frm" name="storage_frm" enctype="multipart/form-data">
			<div class="titleH5" style="margin:20px 0 10px 0; "><?php echo $mode_tit?>
				<button style="float:right;" type="button" name="productSelect" id="productSelect" class="button-bbse blue" onClick="location.href='admin.php?page=bbse_commerce_storage&mode=list'" style="width:100px;"> 목록</button>
			</div>
			<br>
			<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']?>" />
			<input type="hidden" name="strun" id="strun" value="<?php echo $strun?>" />
    			<table class="dataTbls normal-line-height collapse">
            		<tr>
            			<th>사업자번호</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="user_id" id="user_id" style="width:180px;" value="<?php echo $result->user_id;?>" readonly />
						<?php }else{?>
							<input type="text" name="user_id" id="user_id" style="width:180px;" value="" />
						<?php }?>
            			</td> 
            		</tr>
            		<tr>
            			<th>창고명</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="storage_name" id="storage_name" style="width:180px;" value="<?php echo $result->storage_name;?>" />
						<?php }else{?>
							<input type="text" name="storage_name" id="storage_name" style="width:180px;" value="" />
						<?php }?>
            			</td>
            		</tr>
            		<tr>
            			<th>창고코드</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="storage_code" id="storage_code" style="width:180px;" value="<?php echo $result->storage_code;?>" readonly />
						<?php }else{?>
							<input type="text" name="storage_code" id="storage_code" style="width:180px;" placeholder="자동생성" value="" readonly/>
						<?php }?>
            			</td>
            		</tr>
            		<tr>
            			<th>우편번호</th>
            			<td>
            				<?php
            				if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */
            					$zipOpenScript = "openDaumPostcode();";
            				}else{
            					$zipOpenScript = "zipcode_search();";
            				}
            				?>
            				
            			<?php if($_REQUEST['mode']=="edit"){?>
            				<input type="text" name="zipcode" id="zipcode" style="width:180px;" value="<?php echo $result->zipcode;?>" readonly />
						<?php }else{?>
							<input type="text" name="zipcode" id="zipcode" style="width:70px;text-align:center;" value="" readonly />
						<?php }?>
            				<a href="javascript:;" onclick="<?php echo $zipOpenScript;?>" hidefocus="true"><button type="button" class="button-small gray" style="height:25px;">우편번호찾기</button></a></span>
            			</td>
            		</tr>
            		<tr>
            			<th>주소</th>
            			<td>
            				<input type="hidden" name="use_zipcode_api" value="1" /> <!--도로명주소검색 사용(v1.4.9 late)-->
            				<input type="hidden" name="zipcode_api_module" value="2" /><!--도로명주소검색 시 Daum 만 사용(v1.4.9 late)-->
            				
            			<?php if($_REQUEST['mode']=="edit"){?>
							<input type="text" name="addr1" id="addr1" style="width:350px;" value="<?php echo $result->addr1;?>" readonly /><br />
            				<input type="text" name="addr2" id="addr2" style="width:350px;" value="<?php echo $result->addr2;?>" />
						<?php }else{?>
							<input type="text" name="addr1" id="addr1" style="width:350px;" value="" readonly /><br />
            				<input type="text" name="addr2" id="addr2" style="width:350px;" value="" />
						<?php }?>

            			</td>
            		</tr>
					<tr>
            			<th>창고담당자</th>
            			<td>
            			<?php if($_REQUEST['mode']=="edit"){
            			    $manager = $wpdb->get_row("select * from bbse_commerce_membership where user_id ='".$result->manager_id."'");
            			    ?>
            				<input type="text" name="manager_id" id="manager_id" style="width:150px;" value="<?php echo $manager->user_id;?>" readonly/><input type="text" name="manager_name" id="manager_name" style="width:180px;" value="<?php echo $manager->manager_name;?>" readonly/>
						<?php }else{?>
							<input type="text" name="manager_id" id="manager_id" style="width:150px;" value="" readonly/><input type="text" name="manager_name" id="manager_name" style="width:180px;" value="" readonly />
						<?php }?>
							<button type="button" class="button-small green" onClick="search_manager();" style="height: 30px;">담당자검색</button>
            			</td>
            		</tr>
<!--             		<tr>
            			<th>전화번호</th>
            			<td>
            			
            			<?php if($_REQUEST['mode']=="edit"){
            			          $phoneArr = explode("-", $result->phone);
            			    ?>
							<input type="text" name="phone_1" id="phone_1" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phoneArr[0];?>" /> - 
            				<input type="text" name="phone_2" id="phone_2" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phoneArr[1];?>" /> - 
            				<input type="text" name="phone_3" id="phone_3" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $phoneArr[2];?>" />
						<?php }else{?>
            				<input type="text" name="phone_1" id="phone_1" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="" /> - 
            				<input type="text" name="phone_2" id="phone_2" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="" /> - 
            		 		<input type="text" name="phone_3" id="phone_3" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="" />
						<?php }?>

            			</td>
            		</tr>
            		<tr>
            			<th>담당자핸드폰</th>
            			<td>
            			
            			<?php if($_REQUEST['mode']=="edit"){
                                  $hpArr = explode("-", $result->manager_hp);
            			    ?>
							<input type="text" name="hp_1" id="hp_1" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hpArr[0];?>" /> - 
            				<input type="text" name="hp_2" id="hp_2" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hpArr[1];?>" /> - 
            				<input type="text" name="hp_3" id="hp_3" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="<?php echo $hpArr[2];?>" />
						<?php }else{?>
            				<input type="text" name="hp_1" id="hp_1" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="" /> - 
            				<input type="text" name="hp_2" id="hp_2" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="" /> - 
            				<input type="text" name="hp_3" id="hp_3" style="width:80px;" maxlength="4" onkeydown="checkForNumber();" value="" />
						<?php }?>
            			</td>
            		</tr>
            		
-->
            		<?php if($_REQUEST['mode']=="edit"){?>
            		<tr>
            			<th>등록일자</th>
            			<td>
            				<?php echo $result->reg_date?>
            			</td>
            		</tr>
            		<tr>
            			<th>수정일자</th>
            			<td>
            				<?php echo $result->update_date?>
            			</td>
            		</tr>
            		<?php }?>
    			</table>
			</form>
			<div class="clearfix" style="height:20px;"></div>

			<div style="text-align: center;">
				
				<button style="float:right;" type="button" name="productSelect" id="productSelect" class="button-bbse red" onClick="save_storage('<?php echo $USER_ACTION_URL?>');" style="width:100px;"> <?php echo $button_name;?> </button>
			</div>
		</div>
	</div>
<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 2){  /* Daum 우편번호 API */?>
<div id="commerceZipcodeLayer" style="display:none;border:5px solid;position:fixed;width:320px;height:500px;left:50%;margin-left:-155px;top:50%;margin-top:-235px;overflow:hidden;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="cursor:pointer;position:absolute;right:-3px;top:-3px" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>
<script src="https://ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js"></script><!--https-->
<script>
    function closeDaumPostcode() {
        jQuery("#commerceZipcodeLayer").css("display","none");
    }
</script> 
<?php }?>

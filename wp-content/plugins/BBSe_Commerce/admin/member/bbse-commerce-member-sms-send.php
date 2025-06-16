<style>
.board_add {background:#fff;padding:15px;}
.board_add td {border-bottom:1px dotted #ccc;padding:10px 0;}
.board_add_title{padding:10px !important;color:#11568b;background:#f7f7f7;border-top:3px solid #8fa6c6;border-bottom:1px solid #ddd !important;}
.wrap #order_list_view { display:none; }
.wrap div.class_bg { display:none; position:absolute; top:0px; width:100%; height:100%; background-color:#000; opacity:0.1; filter:alpha(opacity:'10'); z-index:500; }
.wrap form.class_tbl { position:absolute; top:0px; width:100%; z-index:501; }
.wrap form.class_tbl #order_list_view { margin:0px auto; margin-top:30px; width:500px; border:3px solid #eeeeee; }
.mid_area {width:710px;vertical-align:top;float:left;}
.phone_msg {position:relative;width:710px;height:277px;background:url(<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/phone_bg2.jpg) no-repeat;}
.phone_msg .phone_in {padding:18px 40px;color:#fff;}
.phone_msg .mm {zoom:1;margin-top:1px;padding-top:1px\9;}
.phone_msg .mm li.bytes {width:154px;line-height:49px;vertical-align:bottom;font-family:arial;}
.phone_msg #cur_bytes {padding:0 0 0 36px;font-size:17px;color:#ff6262;}
.phone_msg .mm li {float:left;}
.phone_msg .mm li a {display:block;width:57px;height:43px;}
.phone_msg .mm li span {display:block;width:57px;height:43px;}
.phone_msg .mm li.ph4 a:hover {background:url(<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/bt_ph2.gif) no-repeat;}
.phone_msg .mm li.ph5 {width:60px;}
.phone_msg .mm li.ph5 a:hover {background:url(<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>images/bt_ph5.gif) no-repeat;}
.phone_msg .texta {resize:none;height:170px;overflow-y:scroll;background-color:transparent;width:195px;margin:0px;padding:0px;line-height:25px;border:0;resize:none;font-size:12px;}
.phone_msg .phone_in {zoom:1;}
.phone_msg .phone_in .smsleft {padding-left:10px;width:212px;float:left;}
.phone_msg .phone_in .right {position:relative;float:left;margin-left:10px\9;font-weight:bold;line-height:23px;letter-spacing:-1px;width:210px;}
.phone_msg .right .texti {background-color:transparent;color:black;border:0;font-size:11px;}
.phone_msg .right .p11 {font-weight:normal;font-size:11px;padding:3px 3px 0 3px;color:#eee;}
.phone_msg .times {position:absolute;top:103px;line-height:16px;font-size:11px;letter-spacing:-1px;}
.times {text-align:left;width:110px;}
.times select {font-size:11px;letter-spacing:-1px;font-family:arial;}
.times label {color:#7c8184;font-weight:normal;}
.phone_msg .right .texta2 {overflow-y:scroll;height:92px;padding:0;margin:0;line-height:23px;vertical-align:top;background-color:transparent;color:black;border:0;resize:none;font-size:12px;}
#div_recommend_time {display:none;background:#aab7ca;position:relative;top:3px;left:0px;z-index:1;width:282px;border:solid 1px #72839c;padding:3px}
.imgmsg {width:110px !important;overflow:hidden;margin-bottom:10px;}
.phone_msg .right2 {position:absolute;width:156px;height:153px;right:40px;top:5px;}
</style>
<script language="javascript">
function special_str(addText){ 
	var cursor; 
	var el=document.getElementById('msg');
	if(el.value=='여기에 내용을 입력하세요 :)'){
		el.value="";
	}
	if(el.selectionStart || el.selectionStart == '0'){ //ff
		if(el.selectionStart > 0){
			cursor = el.selectionStart;  
			el.value = el.value.substring(0,el.selectionStart) + addText + el.value.substring(el.selectionStart,el.value.length); 
		}
		else if(el.selectionStart == '0'){ 
			cursor = el.selectionStart;  
			el.value = el.value.substring(el.selectionStart,el.value.length) + addText; 
		}
	}
	else if(document.selection){ //ie 
		el.focus();
		var selRange = document.selection.createRange(); 
		selRange.text = addText; 
	}
	CheckByte(document.XForm.msg);
}

function checkKeyCode(ch_type){    
	var key = event.keyCode;  
	if(ch_type=='inEnter'){
		if(!(key==8 || key==13 || key==37 || key==39 || key==46 || key==144 || (key>=48&&key<=57) || (key>=96&&key<=105) || key==109 || key==189)){
			event.returnValue = false;
		} 
	}
	else{
		if(!(key==8 || key==37 || key==39 || key==46 || key==144 || (key>=48&&key<=57) || (key>=96&&key<=105) || key==109 || key==189)){
			event.returnValue = false;
		} 
	}
}

function CheckByte(obj)
{
	var msgVal = obj.value;
	var bytesLen = 0;
	var curMsgLen = '';
	var curBytesLen = 0;
	var realVal = '';
	var realLen = 0;
	var msgTypeLimit = 80;

	if(msgVal=="여기에 내용을 입력하세요 :)") msgVal="";

	for(var i = 0; i < msgVal.length; i++)
	{
		var oneChar = msgVal.charAt(i);

		if ( oneChar == '\n' )
			bytesLen = bytesLen;
		else if ( escape(oneChar).length > 4 || oneChar == "°" )
			bytesLen += 2;
		else if ( oneChar != '\r' || oneChar != '\n' )
			bytesLen++;

		if ( bytesLen <= msgTypeLimit ) {
			curMsgLen = i + 1;
			curBytesLen = bytesLen;
		}
		
	}

	if (bytesLen > msgTypeLimit) {
		alert(msgTypeLimit + "bytes 이상의 메세지는 전송하실 수 없습니다.");
		document.XForm.msg.value = msgVal.substr(0, curMsgLen);		   // 초과 입력시 초과된 만큼 잘라줌
		realLen = curBytesLen;
	}
	else
		realLen = bytesLen;

	window.cur_bytes.innerText   = realLen;
	window.limit_bytes.innerText = msgTypeLimit;
}

function memberDisplay() {
	var chked = jQuery("[id='check[]']").filter(":checked");
	if(chked.length==0) {
		alert("추가할 회원을 선택해주세요.");
		return;
	}

	var addCheckMsg = "";
	var addCheckFlag = true;
	var addCount = 0;
	var phoneList = "";
	var rcvNumber = jQuery("#rcvNumber").val();
	var arrNumber;
	jQuery(chked).each(function (i,e) {
		addCheckFlag = true;

		arrNumber = rcvNumber.split("\n");
		for(var n=0;n<arrNumber.length;n++) {
			if(arrNumber[n]!="" && arrNumber[n]==jQuery(e).val().replace(/-/g,"")) {
				addCheckFlag = false;
				addCheckMsg += jQuery(e).val() + " 번호는 이미 추가되어 있습니다.\n";
			}
		}
		if(addCheckFlag == true) {
			phoneList += jQuery(e).val().replace(/-/g,"") + "\n";
			addCount++;
		}
	});

	if(addCheckMsg) {
		alert(addCheckMsg);
		return;
	}
	jQuery("#rcvNumber").val(phoneList);
	tb_remove();

}
function smsProc() {
	<?php if($config['sms_use_yn']=="Y"){?>
	if(jQuery("#rcvNumber").val()=="" || jQuery("#rcvNumber").val()=="여기에 내용을 입력하세요 :)") {
		alert("내용을 입력해주세요.");
		jQuery("#msg").focus();
		return;
	}else{
		jQuery("#mode").val("send");
		jQuery("#smsForm").submit();
	}
	<?php }else{?>
		alert("환경설정에서 SMS 사용 여부를 설정해주세요.");
	<?php }?>
}
</script>
<div>
	<?php
	if($_GET['user_no']){
		$user_no = $_GET['user_no'];
		$send_phone = $wpdb->get_var("SELECT hp FROM bbse_commerce_membership WHERE user_no='".$user_no."'");
	}
	if($_POST['member_list']!="") {
		$member_list = unserialize(base64_decode($_POST['member_list']));
		if(count($member_list) > 0){
			$send_phone = array();
			for($i=0 ; $i<count($member_list) ; $i++){
				$user_no = $member_list[$i];
				$hp = $wpdb->get_var("SELECT hp FROM bbse_commerce_membership WHERE user_no='".$user_no."'");
				if($hp) $send_phone[] = $hp;
			}
			$send_phone = @implode("\n", $send_phone);
		}
	}
	$adminTel = $config['sms_callback_tel'];
	if($V['mvrun'] == 'send_proc') echo '<div id="message" class="updated fade"><p><strong>'.$alertMsg.'</strong></p></div>';
	?>

	<div class="titleH5" style="margin:20px 0 10px 0; ">개별SMS발송</div>

	<div class="borderBox">
		* 자동 SMS 발송은 SMS(문자) 자동설정에서 설정하실 수 있습니다. <a href="admin.php?page=bbse_commerce_member&cType=sms" class="add-new-h2">SMS(문자)자동설정</a>
	</div>


	<div class="phone_msg">
		<form id="smsForm" name="smsForm" method="POST" action="admin.php?page=bbse_commerce_member&cType=sms&mode=send">
		<input type="hidden" id="mvrun" name="mvrun" value="send_proc">
		<div class="phone_in">
			<ul class="mm">
				<li class="bytes"><span id="cur_bytes">0</span></strong> / <span id="limit_bytes">80</span> Bytes</li>
				<li class="ph1"><span></span></li>
				<li class="ph2"><span></span></li>
				<li class="ph3"><span></span></li>
				<li class="ph4"><a href="#TB_inline?width=600&height=550&inlineId=modal-member-list" class="thickbox" title="회원목록"></a></li>
				<li class="ph5"><a href="javascript:smsProc();" title="전송"></a></li>
			</ul>

			<div class="smsleft" style="position:relative;left:-160px;">
				<div style="width:150px;margin:0px 0 0 3px;">
				<textarea id="msg" name="msg" cols="55" wrap="soft" maxlength="80" class="texta" onKeyUp="CheckByte(this)" onFocus="CheckByte(this)" onclick="if(this.value=='여기에 내용을 입력하세요 :)'){this.value='';CheckByte(this);}" :onBlur="if(this.value==''){this.value='여기에 내용을 입력하세요 :)';}" tabindex="1">여기에 내용을 입력하세요 :)</textarea>
				</div>
			</div> <!--//left -->

			<div class="right">
				<div style="height:24px;padding-top:3px;">
					<span style="position:relative;left:-155px;vertical-align:top;">발신번호</span>&nbsp; &nbsp; &nbsp;
					<input type="text" id="sndNumber" name="sndNumber" size="17" maxlength="13" class="texti" value="<?php echo $adminTel?>" onKeyDown="checkKeyCode('');" tabindex="2" style="position:relative;top:-4px;left:-160px;">
				</div>
				<div>
					<span style="position:relative;left:-155px;vertical-align:top;">수신번호</span> <span style="position:relative;left:-155px;" class="p11">(아래칸 이동시 엔터를 치세요)</span>
					<div style="position:relative;left:-100px;"><textarea id="rcvNumber" name="rcvNumber" rows="6" cols="20" class="texta2" onKeyDown="checkKeyCode('inEnter');" tabindex="3"><?=$send_phone?></textarea></div>
				</div>
				
			</div><!--// right -->

			<div class="right2">
				<a href="javascript:onClick=special_str('·');"><font color="#ffffff">·</font></a> <a href="javascript:onClick=special_str('。');"><font color="#ffffff">。</font></a>
				<a href="javascript:onClick=special_str('#');"><font color="#ffffff">#</a></a> <a href="javascript:onClick=special_str('&');"><font color="#ffffff">&</font></a> 
				<a href="javascript:onClick=special_str('*');"><font color="#ffffff">*</font></a> <a href="javascript:onClick=special_str('@');"><font color="#ffffff">@</font></a> 
				<a href="javascript:onClick=special_str('§');"><font color="#ffffff">§</font></a> <a href="javascript:onClick=special_str('※');"><font color="#ffffff">※</font></a> 
				<a href="javascript:onClick=special_str('☆');"><font color="#ffffff">☆</font></a> <a href="javascript:onClick=special_str('★');"><font color="#ffffff">★</font></a> 
				<a href="javascript:onClick=special_str('○');"><font color="#ffffff">○</font></a> <a href="javascript:onClick=special_str('●');"><font color="#ffffff">●</font></a> 
				<a href="javascript:onClick=special_str('◎');"><font color="#ffffff">◎</font></a> <a href="javascript:onClick=special_str('◇');"><font color="#ffffff">◇</font></a> 
				<a href="javascript:onClick=special_str('◆');"><font color="#ffffff">◆</font></a> <a href="javascript:onClick=special_str('□');"><font color="#ffffff">□</font></a> 
				<a href="javascript:onClick=special_str('■');"><font color="#ffffff">■</font></a> <a href="javascript:onClick=special_str('△');"><font color="#ffffff">△</font></a> 
				<a href="javascript:onClick=special_str('▲');"><font color="#ffffff">▲</font></a> <a href="javascript:onClick=special_str('▽');"><font color="#ffffff">▽</font></a> 
				<a href="javascript:onClick=special_str('▼');"><font color="#ffffff">▼</font></a> <a href="javascript:onClick=special_str('◁');"><font color="#ffffff">◁</font></a> 
				<a href="javascript:onClick=special_str('◀');"><font color="#ffffff">◀</font></a> <a href="javascript:onClick=special_str('▷');"><font color="#ffffff">▷</font></a> 
				<a href="javascript:onClick=special_str('▶');"><font color="#ffffff">▶</font></a> <a href="javascript:onClick=special_str('♤');"><font color="#ffffff">♤</font></a> 
				<a href="javascript:onClick=special_str('♠');"><font color="#ffffff">♠</font></a> <a href="javascript:onClick=special_str('♡');"><font color="#ffffff">♡</font></a> 
				<a href="javascript:onClick=special_str('♥');"><font color="#ffffff">♥ </font></a><a href="javascript:onClick=special_str('♧');"><font color="#ffffff">♧</font></a> 
				<a href="javascript:onClick=special_str('♣');"><font color="#ffffff">♣</font></a> <a href="javascript:onClick=special_str('⊙');"><font color="#ffffff">⊙</font></a> 
				<a href="javascript:onClick=special_str('◈');"><font color="#ffffff">◈</font></a> <a href="javascript:onClick=special_str('▣');"><font color="#ffffff">▣</font></a> 
				<a href="javascript:onClick=special_str('◐');"><font color="#ffffff">◐</font></a> <a href="javascript:onClick=special_str('◑');"><font color="#ffffff">◑</font></a> 
				<a href="javascript:onClick=special_str('▒');"><font color="#ffffff">▒</font></a> <a href="javascript:onClick=special_str('▤');"><font color="#ffffff">▤</font></a> 
				<a href="javascript:onClick=special_str('▥');"><font color="#ffffff">▥</font></a> <a href="javascript:onClick=special_str('▨');"><font color="#ffffff">▨</font></a> 
				<a href="javascript:onClick=special_str('▧');"><font color="#ffffff">▧</font></a> <a href="javascript:onClick=special_str('▦');"><font color="#ffffff">▦</font></a> 
				<a href="javascript:onClick=special_str('▩');"><font color="#ffffff">▩</font></a> <a href="javascript:onClick=special_str('♨');"><font color="#ffffff">♨</font></a> 
				<a href="javascript:onClick=special_str('☏');"><font color="#ffffff">☏</font></a> <a href="javascript:onClick=special_str('☎');"><font color="#ffffff">☎</font></a> 
				<a href="javascript:onClick=special_str('☜');"><font color="#ffffff">☜</font></a> <a href="javascript:onClick=special_str('☞');"><font color="#ffffff">☞</font></a> 
				<a href="javascript:onClick=special_str('¶');"><font color="#ffffff">¶</font></a> <a href="javascript:onClick=special_str('†');"><font color="#ffffff">†</font></a> 
				<a href="javascript:onClick=special_str('‡');"><font color="#ffffff">‡</font></a> <a href="javascript:onClick=special_str('↕');"><font color="#ffffff">↕</font></a> 
				<a href="javascript:onClick=special_str('↗');"><font color="#ffffff">↗</font></a> <a href="javascript:onClick=special_str('↙');"><font color="#ffffff">↙</font></a> 
				<a href="javascript:onClick=special_str('↖');"><font color="#ffffff">↖</font></a> <a href="javascript:onClick=special_str('↘');"><font color="#ffffff">↘</font></a> 
				<a href="javascript:onClick=special_str('♭');"><font color="#ffffff">♭</font></a> <a href="javascript:onClick=special_str('♩');"><font color="#ffffff">♩</font></a> 
				<a href="javascript:onClick=special_str('♪');"><font color="#ffffff">♪</font></a> <a href="javascript:onClick=special_str('♬');"><font color="#ffffff">♬</font></a> 
				<a href="javascript:onClick=special_str('㉿');"><font color="#ffffff">㉿</font></a> <a href="javascript:onClick=special_str('㈜');"><font color="#ffffff">㈜</font></a> 
				<a href="javascript:onClick=special_str('№');"><font color="#ffffff">№</font></a> <a href="javascript:onClick=special_str('㏇');"><font color="#ffffff">㏇</font></a> 
				<a href="javascript:onClick=special_str('™');"><font color="#ffffff">™</font></a> <a href="javascript:onClick=special_str('㏂');"><font color="#ffffff">㏂</font></a> 
				<a href="javascript:onClick=special_str('㏘');"><font color="#ffffff">㏘</font></a> <a href="javascript:onClick=special_str('℡');"><font color="#ffffff">℡</font></a> 
				<a href="javascript:onClick=special_str('€');"><font color="#ffffff">€</font></a> <a href="javascript:onClick=special_str('®');"><font color="#ffffff">®</font></a> 
				<a href="javascript:onClick=special_str('ㆀ');"><font color="#ffffff">ㆀ</font></a> <a href="javascript:onClick=special_str('『');"><font color="#ffffff">『</font></a> 
				<a href="javascript:onClick=special_str('』');"><font color="#ffffff">』</font></a> <a href="javascript:onClick=special_str('≪');"><font color="#ffffff">≪</font></a> 
				<a href="javascript:onClick=special_str('≫');"><font color="#ffffff">≫</font></a> <a href="javascript:onClick=special_str('∪');"><font color="#ffffff">∪</font></a> 
				<a href="javascript:onClick=special_str('∩');"><font color="#ffffff">∩</font></a> <a href="javascript:onClick=special_str('＄');"><font color="#ffffff">＄</font></a> 
				<a href="javascript:onClick=special_str('θ');"><font color="#ffffff">θ</font></a> <a href="javascript:onClick=special_str('α');"><font color="#ffffff">α</font></a> 
				<a href="javascript:onClick=special_str('Ω');"><font color="#ffffff">Ω</font></a> <a href="javascript:onClick=special_str('Ψ');"><font color="#ffffff">Ψ</font></a> 
				<a href="javascript:onClick=special_str('Σ');"><font color="#ffffff">Σ</font></a> <a href="javascript:onClick=special_str('ω');"><font color="#ffffff">ω</font></a>
				<br />(클릭 하셔서 이용하세요)
			</div>

		</div><!--// phone_in -->
	</div><!--// phone_msg -->


</div>


<div id="modal-member-list" style="display:none;">

	<div style="float:left;"><h3>회원을 선택해주세요</h3></div>
	<div style="float:right;">
		<input type="button" name="productSelect" id="productSelect" class="button-primary" value="&nbsp;&nbsp;&nbsp;&nbsp;추가하기&nbsp;&nbsp;&nbsp;&nbsp;" onclick="memberDisplay();">
	</div>

	<br>

	<table class="wp-list-table widefat fixed posts" cellspacing="0">
	<thead>
	<tr>
		<th scope="col" width="50" class="manage-column">번호</th>
		<th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th>
		<th scope="col" width="100" class="manage-column">아이디</th>
		<th scope="col" width="150" class="manage-column">이름</th>
		<th scope="col" class='manage-column'>핸드폰</th>

	</tr>
	</thead>
	<tbody id="the-list">
	<?
	$total = $wpdb->get_var("SELECT COUNT(*) FROM bbse_commerce_membership WHERE sms_reception='1'"); //총 목록수
	$result = $wpdb->get_results("SELECT * FROM bbse_commerce_membership WHERE sms_reception='1' ORDER BY user_no DESC");
	if(count($result) > 0) {
		foreach($result as $i=>$data) {
			$num = $total-$i; //목록 번호
			if($i % 2 == 0) $alternate_class = "class=\"alternate\"";
			else $alternate_class = "";

	?>
	<tr <?php echo $alternate_class?> valign="middle">
		<td><?=$num?></td>
		<th scope="row" class="check-column"><input type="checkbox" id="check[]" name="check[]" value="<?=$data->hp?>"></th>
		<td><?=$data->user_id?></td>
		<td><?=$data->name?></td>
		<td><?=$data->hp?></td>
	</tr>
	<?
			}
		}else{
			echo "<tr><td colspan='5' align='center'>조회된 목록이 없습니다</td></tr>";
		}
	?>
	</tbody>
	</table>

</div>
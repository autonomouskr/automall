<?php
set_time_limit(0);
?>
<div>
	<div class="titleH5" style="margin:20px 0 10px 0; ">메일발송 결과</div>

	<table class="dataTbls overWhite collapse">
		<colgroup><col width=""></colgroup>
		<tr>
			<th align="left">발송이 완료될까지 기다려주세요.<th>
		</tr>
		<tr>
			<th>

<?php
$V = $_POST;
if($V['mvrun']!="email_proc"){
	echo "잘못된 접근입니다.";
}else if(!$V['mail_content']){
	echo "메일 내용을 입력해주세요.";
}else{
	$mlist = explode(",",$V['main_product_list']);
	$listCount = count($mlist);
	$okCount = 0;
	$failCount = 0;
	$results = $wpdb->get_results("SELECT * FROM bbse_commerce_membership WHERE user_no in (".$V['main_product_list'].")");
	$result_str = "";

	flush();
	ob_flush();
	?>
		<table style="font-family:돋움,dotum,applegothic,sans-serif;background-color:#ffffff;border:1px solid #ddd;margin:20px auto;" border="0" cellspacing="0" cellpadding="0" width="100%">
		<thead>
		<tr style="height:30px;">
			<th scope="col" width="150" class="manage-column" style="text-align:center;">아이디</th>
			<th scope="col" width="200" class="manage-column" style="text-align:center;">이름</th>
			<th scope="col" class='manage-column' style="text-align:center;">이메일</th>
			<th scope="col" width="150" class='manage-column' style="text-align:center;">결과</th>

		</tr>
		</thead>
		<tbody id="the-list">
	<?php
	/* 첨부파일 */
	$attrfile = @move_uploaded_file($_FILES['custom_upfile']['tmp_name'], WP_CONTENT_DIR.'/uploads/'.$_FILES['custom_upfile']['name']);
	foreach($results as $i=>$m) {
		$subject = $V['mail_title'];
		$email_content = stripslashes($V['mail_content']);
		$to_name = $m->name;
		$to_email = $m->email;
		$from_name = get_bloginfo('name');
		$from_email = get_bloginfo('admin_email');

		$ret = bbse_mail($to_email, $to_name, $from_email, $from_name, $subject, nl2br($email_content), WP_CONTENT_DIR.'/uploads/'.$_FILES['custom_upfile']['name']);
		if($ret) {
			$msg = "<span style='color:blue;'>발송성공</span>";
		}else{
			$msg = "<span style='color:red;'>발송실패</span>";
		}
		if($i % 2 == 0) $alternate_class = "class=\"alternate\"";
		else $alternate_class = "";	
		echo '<tr '.$alternate_class.' valign="middle"><td>'.$m->user_id.'</td><td>'.$m->name.'</td><td>'.$m->email.'</td><td>'.$msg.'</td></tr>';

		flush();
		ob_flush();
		ob_end_flush();
		usleep(100);

	}

	/* 첨부파일 */
	@unlink(WP_CONTENT_DIR.'/uploads/'.$_FILES['custom_upfile']['name']);
?>
	<tr><td colspan="4" align="center" style="font-weight:bold;color:red;font-size:15px;border-top:1px solid #e1e1e1;">발송 완료</td></tr>
<?php
}
?>
	</tbody>
	</table>
	<br>


			
			</th>
		</tr>
	</table>
</div>
<?php
	@session_start();
	@header("Content-Type: text/html; charset=UTF-8");

	$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
	include $includeURL[0]."/wp-load.php";
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>NICE평가정보 - CheckPlus 본인인증 처리결과</title>
	<script type="text/javascript" src="<?php echo includes_url()?>js/jquery/jquery.js"></script>
	<script>
	jQuery(document).ready(function() {
		alert("본인인증에 실패하였습니다.");
		self.close();
	});
	</script>
</head>
<body>
</body>
</html>
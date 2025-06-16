<!DOCTYPE html>
<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
if($_REQUEST['licenseKey']){
	echo "<script language='javascript'>parent.parent.theme_license_key_insert('".$_REQUEST['licenseKey']."');</script>";
}?>
</head>
<body>
</body>
</html>
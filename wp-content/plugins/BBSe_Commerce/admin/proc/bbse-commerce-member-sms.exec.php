<?
$tMode = $_POST['tMode'];
if($tMode == 'keygen'){  // SMS 인증키 받기
	require_once('../../lib/nusoap.php');

	$smsid = $_POST['smsid'];
	$smspwd = $_POST['smspwd'];
	$smsdomain = $_POST['smsdomain'];

	$paramiters = array('smsid' => $smsid, 'smspwd' => $smspwd, 'smsdomain' => $smsdomain);
	$client = new soapclient5('http://api.ezsms.kr/clinic_soap_smskeygen.php', true);

	$result = $client->call('keygen', $paramiters);

	if($result){
		$tmpResult = explode("|||", $result);
		if($tmpResult['0'] == "success" or $tmpResult['0'] == "loginError"){
			echo $result;
			exit;
		}else{
			echo "fail";
			exit;
		}
	}else{
		echo "fail";
		exit;
	}

}elseif($tMode == 'smsPointCheck'){
	require_once('../../lib/nusoap.php');

	$sms_id = $_POST['sms_id'];
	$sms_key = $_POST['sms_key'];

	$paramiters = array('sms_id' => $sms_id, 'sms_key' => $sms_key);
	$client = new soapclient5('http://api.ezsms.kr/clinic_soap_checkPoint.php', true);

	$result = $client->call('checkPoint', $paramiters);

	if($result){
		$tmpResult = explode("|||", $result);
		if($tmpResult['0'] == "success" or $tmpResult['0'] == "loginError"){
			echo $result;
			exit;
		}else{
			echo "fail";
			exit;
		}
	}else{
		echo "fail";
		exit;
	}
}else{
	echo "nonMode";
	exit;
}
?>
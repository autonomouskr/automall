<?php
include( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
header("Content-Type:text/html; charset=utf-8;"); 
/*
****************************************************************************************
* <인증 결과 파라미터>
****************************************************************************************
*/
$authResultCode = $_POST['AuthResultCode'];		// 인증결과 : 0000(성공)
$authResultMsg = $_POST['AuthResultMsg'];		// 인증결과 메시지
$nextAppURL = $_POST['NextAppURL'];				// 승인 요청 URL
$txTid = $_POST['TxTid'];						// 거래 ID
$authToken = $_POST['AuthToken'];				// 인증 TOKEN
$payMethod = $_POST['PayMethod'];				// 결제수단
$mid = $_POST['MID'];							// 상점 아이디
$moid = $_POST['Moid'];							// 상점 주문번호
$amt = $_POST['Amt'];							// 결제 금액
$reqReserved = $_POST['ReqReserved'];			// 상점 예약필드
$netCancelURL = $_POST['NetCancelURL'];			// 망취소 요청 URL
	
/*
****************************************************************************************
* <승인 결과 파라미터 정의>
* 샘플페이지에서는 승인 결과 파라미터 중 일부만 예시되어 있으며, 
* 추가적으로 사용하실 파라미터는 연동메뉴얼을 참고하세요.
****************************************************************************************
*/
$response = "";
global $wpdb;
$paymentConfig = unserialize($wpdb->get_var("select config_data from bbse_commerce_config where config_type='payment'"));

if($authResultCode === "0000"){
	/*
	****************************************************************************************
	* <해쉬암호화> (수정하지 마세요)
	* SHA-256 해쉬암호화는 거래 위변조를 막기위한 방법입니다. 
	****************************************************************************************
	*/	
	$ediDate = date("YmdHis");
	//$merchantKey = "G5OWRhY0dfhdx7pq5IcGakRFShC05czmhAI/Oe9u48g5gsF4mNpJ55ygZWgFa4xgCyi3ZUnKNp3GNQwNjBglnQ=="; // 상점키
	
	$merchantKey = $paymentConfig['payment_sign_key'];
	$signData = bin2hex(hash('sha256', $authToken . $mid . $amt . $ediDate . $merchantKey, true));

	try{
		$data = Array(
			'TID' 		=> $txTid,
			'AuthToken' => $authToken,
			'MID' 		=> $mid,
			'Amt' 		=> $amt,
			'EdiDate' 	=> $ediDate,
			'SignData' 	=> $signData,
			'CharSet' 	=> 'utf-8'
		);		
		$response = reqPost($data, $nextAppURL); //승인 호출
		//jsonRespDump($response); //response json dump example
		//승인결과에따라 주문완료페이지로 이동
		//print_r($response);
		$res = $response;
		$response = json_decode($response,TRUE);
		$r_code = $response['ResultCode'];
		$r_msg = $response['ResultMsg'];
		//print_r($response['ResultCode']);
		if(in_array($response['ResultCode'],array(3001,4000,4100,7001))):
			$pay_how = '';
			if($response['PayMethod'] == 'CARD'){
				$pay_how = 'C';
			}
			else if($response['PayMethod'] == 'BANK'){
				$pay_how = 'K';
			}
			else if($response['PayMethod'] == 'VBANK'){
				$pay_how = 'V';
			}
			//javascript:iniStd_ing.submit();
		?>
		<body onload="javascript:iniStd_ing.submit();">
				<form name="iniStd_ing" method="post" action="<?php echo get_template_directory_uri(); ?>/proc/order.exec.php">
				<!--공통 부분-->
					<input type="hidden" name="TID" value="<?php echo $response['TID'];?>" /><!--거래 번호-->
					<input type="hidden" name="PayMethod" value="<?php echo $response['PayMethod'];?>" /><!--결제방법(지불수단)-->
					<input type="hidden" name="ResultCode" value="<?php echo $response['ResultCode'];?>"><!-- 결과코드 -->
					<input type="hidden" name="ResultMsg" value="<?php echo  $response['ResultMsg'];?>">		<!-- 결과내용 -->
					<input type="hidden" name="TotPrice" value="<?php echo intval($response["Amt"]);?>" />		<!-- 결제완료금액 -->
					<input type="hidden" name="MOID" value="<?php echo $response['Moid'];?>" />		<!-- 상점주문번호 -->
				
				<!-- 추가 전달 변수 -->
					<input type="hidden" name="ini_buyername" value="<?php echo $response['BuyerName'];?>" />		<!-- 주문자명 -->
					<input type="hidden" name='pay_how' id='pay_how' value='<?php echo $pay_how;?>' />	<!-- 결제방법 -->
					<input type="hidden" name="orderList" value="<?php echo base64_decode($reqReserved);?>"><!-- 상품목록 -->
					<input type="hidden" name="res" value="<?php echo base64_encode($res); ?>">
					<input type="hidden" name="nicepg" value="NICE">
				</form>
			</body>
		<?php
		else:
			$data = Array(
				'TID' => $txTid,
				'AuthToken' => $authToken,
				'MID' => $mid,
				'Amt' => $amt,
				'EdiDate' => $ediDate,
				'SignData' => $signData,
				'NetCancel' => '1',
				'CharSet' => 'utf-8'
			);
			$response = reqPost($data, $netCancelURL); //예외 발생시 망취소 진행
			echo '
				<script>
					alert("결제에 실패했습니다. 관리자에게 문의하세요.('.$r_code.': '.$r_msg.')");
					history.go(-1);
				</script>
			';
		endif;
	}catch(Exception $e){
		$e->getMessage();
		$data = Array(
			'TID' => $txTid,
			'AuthToken' => $authToken,
			'MID' => $mid,
			'Amt' => $amt,
			'EdiDate' => $ediDate,
			'SignData' => $signData,
			'NetCancel' => '1',
			'CharSet' => 'utf-8'
		);
		$response = reqPost($data, $netCancelURL); //예외 발생시 망취소 진행
		//jsonRespDump($response); //response json dump example
		echo '
			<script>
				alert("결제에 실패했습니다. 관리자에게 문의하세요.('.$r_code.': '.$r_msg.')");
				history.go(-1);
			</script>
		';
	}	
	
}else{
	//인증 실패 하는 경우 결과코드, 메시지
	$ResultCode = $authResultCode; 	
	$ResultMsg = $authResultMsg;
	echo '
		<script>
			alert("결제에 실패했습니다. 관리자에게 문의하세요.('.$ResultCode.': '.$ResultMsg.')");
			history.go(-1);
		</script>
	';
}

// API CALL foreach 예시
function jsonRespDump($resp){
	$respArr = json_decode($resp);
	foreach ( $respArr as $key => $value ){
		if($key == "Data"){
			echo decryptDump ($value, $merchantKey)."<br />";
		}else{
			echo "$key=". $value."<br />";
		}
	}
}

//Post api call
function reqPost(Array $data, $url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);					//connection timeout 15 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));	//POST data
	curl_setopt($ch, CURLOPT_POST, true);
	$response = curl_exec($ch);
	curl_close($ch);	 
	return $response;
}

?>
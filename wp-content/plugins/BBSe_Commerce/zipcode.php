<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL[0]."/wp-load.php";

$V = $_POST;

$config = $wpdb->get_row("select * from `bbse_commerce_membership_config`");

if($V['search_type'] == "dong" && !empty($V['keyword1'])) $V['keyword'] = $V['keyword1'];
if(($V['search_type'] == "road" || $V['search_type'] == "post") && !empty($V['keyword2'])) $V['keyword'] = $V['keyword2'];

if($_REQUEST['fieldTitle']!="") {
	$zipScript = "orderadd";
}else{
	$zipScript = "joinadd";
}

if(!empty($V['keyword'])){
	if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 1 && !empty($config->zipcode_api_key)){
		$urlString = 'serviceKey='.$config->zipcode_api_key.'&searchSe='.$V['search_type'].'&srchwrd='.urlencode($V['keyword']);
		$requestURL = 'http://openapi.epost.go.kr/postal/retrieveNewAdressService/retrieveNewAdressService/getNewAddressList';
		$url = parse_url($requestURL);
		
		$host = $url['host'];
		$path = $url['path'];

		$out = "GET {$path}?{$urlString}";

		$fp = @fsockopen($host, 80);
		if(!is_resource($fp)){
			$res = '도로명 주소를 검색할 수 없습니다.<br />API KEY 유효성을 확인해주세요.';
		
		}else{
			$out .= " HTTP/1.1\r\n";
			$out .= "HOST: {$host}\r\n";
			$out .= "Connection:close\r\n\r\n";
			fwrite($fp, $out);

			$httpResponse = '';
			while(!feof($fp)){
				$httpResponse .= fgets($fp, 51200);
			}
			fclose($fp);

			// 검색값이 존재하는지 체크
			preg_match_all("/<successYN>(.*)<\/successYN>/iU", $httpResponse, $searchResult);
			$searchResult = $searchResult[1][0];

			if($searchResult != 'Y'){
				$res = '검색된 결과가 없습니다.';
			
			}else{
				// 우편번호 추출하기
				preg_match_all("/<zipNo>(.+?)<\/zipNo>/i", $httpResponse, $resultZip);
				$resultZip = $resultZip[1];
				// 도로명 주소 추출하기
				preg_match_all("/<lnmAdres>(.+?)<\/lnmAdres>/i", $httpResponse, $resultAddress1);
				$resultAddress1 = $resultAddress1[1];
				// 주소 추출하기
				preg_match_all("/<rnAdres>(.+?)<\/rnAdres>/i", $httpResponse, $resultAddress2);
				$resultAddress2 = $resultAddress2[1];
				
				$res = '우편번호를 선택 후 나머지 주소를 입력해 주세요.';
				for($i = 0; $i < sizeof($resultZip); $i++){
					$post_code[] = '
						<li>
							<a href="javascript:;" onclick="'.$zipScript.'(\''.$resultZip[$i].'\', \''.$resultAddress1[$i].'\',\''.$_REQUEST['fieldTitle'].'\')">
								<span class="left">('.$resultZip[$i].') '.$resultAddress2[$i].'<br />'.$resultAddress1[$i].'</span>
							</a>
						</li>';
				}
			}
			unset($fp, $domain, $port, $error, $errstr, $serviceKey, $urlString, $url, $httpResponse, $searchResult, $resultZip, $resultAddress1, $resultAddress2);
		}
	
	}else{
		$zipline = file("./postcode.dat");
		while(list($key, $val) = each($zipline)){
			$varray = explode("|", $val);
			$string = $varray[4].$varray[5];
			if(preg_match("/(".$V['keyword'].")/", $string)) $zip[$key] = $val;
		}

		$i = 0;
		if(sizeof($zip) > 0){
			$res = '우편번호를 선택 후 나머지 주소를 입력해 주세요.';
			while(list(, $value) = each($zip)){
				$ziparray = explode("|", $value);
				$address[$i] = $ziparray[2]." ".$ziparray[3]." ".$ziparray[4];
				if(preg_match("/~/", trim($ziparray[5]))){
					$addr4[$i] = "";
				}else{
					$addr4[$i] = trim($ziparray[5]);
				}
				$view_addre[$i] = trim($ziparray[5]);
				$zipcode1[$i] = substr($ziparray[1], 0, 3);
				$zipcode2[$i] = substr($ziparray[1], 4, 3);

				$post_code[] = '
					<li>
						<a href="javascript:;" onclick="'.$zipScript.'(\''.$zipcode1[$i].'-'.$zipcode2[$i].'\', \''.$address[$i].' '.$view_addre[$i].'\',\''.$_REQUEST['fieldTitle'].'\')">
							<span class="left">('.$zipcode1[$i].'-'.$zipcode2[$i].') '.$address[$i].' '.$view_addre[$i].'</span>
						</a>
					</li>';
			}
			sort($post_code);  // 배열을 거꾸로..
		
		}else{
			$res = '검색된 결과가 없습니다.';
		}
	}
}

$search_type = $_POST['search_type']?$_POST['search_type']:$_GET['search_type'];
if($search_type == '') $search_type = 'dong';

if($search_type == "dong") $now_tab = 1;
else $now_tab = 2;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko-KR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='viewport' content='width=device-width' />
<title>우편번호찾기</title>
<link rel="stylesheet" type="text/css" href="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>css/zipcode.css" />
<script type="text/javascript" src="<?php echo includes_url()?>js/jquery/jquery.js"></script>
<script type="text/javascript">
var now_tab = 1;
// 지번 / 도로명주소 탭선택
function tab_move(num){
	jQuery("#tab_" + now_tab).hide();
	jQuery("#tab_" + num).show();
	jQuery("#tab_menu_" + now_tab).removeClass("on");
	jQuery("#tab_menu_" + num).addClass("on");
	if(num == 1){  // 지번주소
		jQuery("#search_type").val("dong");
		<?php if(empty($V['keyword'])){?>
		jQuery("#keyword1").focus();
		<?php }?>
	}else if(num == 2){  // 도로명주소
		if(jQuery("input:radio[name=sub_type]:checked").val() == 1){
			jQuery("#search_type").val("road");
		}else if(jQuery("input:radio[name=sub_type]:checked").val() == 2){
			jQuery("#search_type").val("post");
		}
		<?php if(empty($V['keyword'])){?>
		jQuery("#keyword2").focus();
		<?php }?>
	}
	now_tab = num;
}

// 검색 서브밋
function check(){
	var type = jQuery("#search_type").val();
	var search = "";
	
	if(type == "dong") search = "동과 번지";
	else if(type == "road") search = "도로명과 건물번호";
	else if(type == "post") search = "우편번호";

	if(type == "dong"){
		if(jQuery("#keyword1").val() == ""){
			jQuery("#error_box1").show();
			jQuery("#error_box1").html("에러 : " + search + "를 입력해주세요.");
			jQuery("#keyword1").focus();
			return false;
		}
	}else{
		if(jQuery("#keyword2").val() == ""){
			jQuery("#error_box2").show();
			jQuery("#error_box2").html("에러 : " + search + "를 입력해주세요.");
			jQuery("#keyword2").focus();
			return false;
		}
	}
	document.zipcode.submit();
}

// 오프너 삽입
function joinadd(zipcode, addr1,fieldTitle){
	var zip = zipcode.split("-");
	jQuery(opener.document).find("#zipcode1").val(zip[0]);
	jQuery(opener.document).find("#zipcode2").val(zip[1]);
	jQuery(opener.document).find("#addr1").val(addr1);
	jQuery(opener.document).find("#error_addr").hide();
	jQuery(opener.document).find("#addr2").focus();
	self.close();
}
function orderadd(zipcode, addr1, fieldTitle){
	var zip = zipcode.split("-");
	jQuery(opener.document).find("#"+fieldTitle+"_zip1").val(zip[0]);
	jQuery(opener.document).find("#"+fieldTitle+"_zip2").val(zip[1]);
	jQuery(opener.document).find("#"+fieldTitle+"_addr1").val(addr1);
	jQuery(opener.document).find("#"+fieldTitle+"_addr2").focus();
	self.close();
}

// 도로명 / 우편번호검색 선택
function sub_type_move(){
	jQuery('#error_box2').empty().hide();
	if(jQuery("input:radio[name=sub_type]:checked").val() == 1){
		jQuery("#search_type").val("road");
		jQuery('#subtab_1').show();
		jQuery('#subtab_2').hide();
	}else if(jQuery("input:radio[name=sub_type]:checked").val() == 2){
		jQuery("#search_type").val("post");
		jQuery('#subtab_1').hide();
		jQuery('#subtab_2').show();
	}
}

window.onload = function(){
	tab_move(<?php echo $now_tab?>);
	<?php if($now_tab == 2){?>
	sub_type_move();
	<?php }?>
}
</script>
</head>
<body style='margin:0;padding:0'>
<div id="bbse_membership">
	<form name="zipcode" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
	<input type="hidden" name="search_type" id="search_type" value="<?php echo $search_type?>" />
	<input type="hidden" name="fieldTitle" id="fieldTitle" value="<?php echo $_REQUEST['fieldTitle']?>" />
	<div class="zipcode">
		<h1>우편번호찾기</h1>
		<ul class="<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 1 && !empty($config->zipcode_api_key)) echo "tab_menu"; else echo "tab_menu_one";?>">
			 <li id="tab_menu_1" onclick="tab_move(1);" class="on">지번주소</li>
			 <?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 1 && !empty($config->zipcode_api_key)){?>
			 <li id="tab_menu_2" onclick="tab_move(2);" class="last">도로명주소</li>
			 <?php }?>
		</ul>
		<div class="clear">
			<!-- 지번으로 찾기 -->
			<div id="tab_1"<?php if($search_type != "dong"){?> style="display:none;"<?php }?>>
				<div class="box1">
					<label><input type="text" name="keyword1" id="keyword1" value="<?php echo $V['keyword1']?>" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_box1').empty().hide();}if(event.keyCode == 13) check();" /></label><a href="javascript:;" class="btn_d zip_btn1" onclick="check();">검색</a>
				</div>
				<span id="error_box1" class="error_box" style="display:none;"></span>
				<span class="info1">
					<?php if($config->use_zipcode_api == 1 && $config->zipcode_api_module == 1 && !empty($config->zipcode_api_key)){?>동(읍/면/리) 다음에 한칸 띄우고 번지수를 입력하세요. <br />(예: 남대문로5가 84-4, 고척동 76-406)
					<?php }else{?>동을 입력하세요. (예: 압구정동, 호계동)<?php }?>
				</span>
				<?php if($V['search_type'] == "dong"){?>
				<span class="info2"><?php echo $res?></span>
				<?php if(count($post_code) > 0){?>
				<div class="info3">
					<p>검색결과</p>
					<ul>
						<?php for($i = 0; $i < count($post_code); $i++) echo $post_code[$i];?>
					</ul>
				</div>
				<?php }?>
				<?php }?>
			</div>

			<!-- 도로명, 우편번호로 찾기 -->
			<div id="tab_2"<?php if($search_type == "dong"){?> style="display:none;"<?php }?>>
				<div class="box1">
					<div>
						<label><input type="radio" name="sub_type" id="sub_type1" value="1" class="radio" onclick="sub_type_move();"<?php if(empty($V['sub_type']) || $V['sub_type'] == 1) echo " checked";?> /> 도로명검색</label>&nbsp;
						<label><input type="radio" name="sub_type" id="sub_type2" value="2" class="radio" onclick="sub_type_move();"<?php if($V['sub_type'] == 2) echo " checked";?> /> 우편번호검색</label>
						<label><input type="text" name="keyword2" id="keyword2" value="<?php echo $V['keyword2']?>" onkeydown="if(jQuery(this).val() != ''){jQuery('#error_box2').empty().hide();}if(event.keyCode == 13) check();" /></label><a href="javascript:;" class="btn_d zip_btn2" onclick="check();">검색</a>
					</div>
				</div>
				<span id="error_box2" class="error_box" style="display:none;"></span>
				<span id="subtab_1" class="info1"<?php if($V['sub_type'] == 2) echo "style='display:none;'";?>>도로명 다음에 한칸 띄우고 건물번호를 입력하세요. <br />(예: 세종대로 10, 중앙로 10-1)</span>
				<span id="subtab_2" class="info1"<?php if(empty($V['sub_type']) || $V['sub_type'] == 1) echo "style='display:none;'";?>>우편번호를 입력하세요. <br />(예: 100-801, 152-826)</span>
				<?php if($V['search_type'] == "road" || $V['search_type'] == "post"){?>
				<span class="info2"><?php echo $res?></span>
				<?php if(count($post_code) > 0){?>
				<div class="info4">
					<p>검색결과</p>
					<ul>
						<?php for($i = 0; $i < count($post_code); $i++) echo $post_code[$i];?>
					</ul>
				</div>
				<?php }?>
				<?php }?>
			</div>
		</div>
	</div>
	</form>
</div>
</body>
</html>
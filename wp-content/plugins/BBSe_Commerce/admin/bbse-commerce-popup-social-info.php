<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
</head>
<body>
<?php
	$sData=$wpdb->get_row("SELECT * FROM bbse_commerce_social_login WHERE idx='".$_REQUEST['tData']."'");

		switch($sData->sns_type){
			case "naver" : 
				$snsId=explode("@",$sData->sns_id);
				$snsUrl="http://blog.naver.com/".$snsId['0'];
				break;
			case "facebook" : 
				$snsUrl="https://www.facebook.com/".$sData->sns_id;
				break;
			case "google" : 
				$snsUrl="https://plus.google.com/".$sData->sns_id."/posts";
				break;
			case "twitter" : 
				$snsId=explode("-",$sData->sns_id);
				$snsUrl="https://twitter.com/".$snsId['1'];
				break;			
			default :
				$snsUrl="";
				break;

		}

?>

	<div class="wrap">
		<div class="clearfix" style="height:4px;"></div>
		<table class="dataTbls normal-line-height collapse">
			<colgroup><col width="100px;"><col width=""></colgroup>
			<tr>
				<th>SNS 아이디 : </th>
				<td><?php echo $sData->sns_id;?></td>
			</tr>
			<tr>
				<th>이름 : </th>
				<td><?php echo ($sData->sns_name)?$sData->sns_name:"-";?></td>
			</tr>
			<tr>
				<th>SNS 명 : </th>
				<td>
					<?php echo strtoupper($sData->sns_type);?>
					<?php if($snsUrl){?>
						&nbsp;<a href="<?php echo $snsUrl;?>" target="_blank" title="<?php echo strtoupper($sData->sns_type);?>"><img src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>images/icon_social_view.png' align='absmiddle' style='width:70px;height:14px;cursor:pointer;' /></a>
					<?php }?>
				</td>
			</tr>
			<tr>
				<th>이메일 : </th>
				<td><?php echo ($sData->sns_email)?$sData->sns_email:"-";?></td>
			</tr>
			<tr>
				<th>성별 : </th>
				<td>
					<?php 
					if($sData->sns_gender) echo ($sData->sns_gender=='M')?"남자":"여자";
					else echo "-";
					?>
				</td>
			</tr>
			<tr>
				<th>로그인 일자 : </th>
				<td><?php echo ($sData->login_date)?date("Y-m-d H:i:s",$sData->login_date):"-";?></td>
			</tr>
			<tr>
				<th>회원통합 : </th>
				<td><?php echo ($sData->integrate_yn=='Y')?"통합완료(".$sData->member_id.")":"<span style='color:#ED1C24;'>통합전</span>";?></td>
			</tr>
		</table>
	</div>
</body>
</html>

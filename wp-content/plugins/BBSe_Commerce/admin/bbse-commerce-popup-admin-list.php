<?php
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

if(!stristr($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST'])){echo "nonData";exit;}

$tbWidth=$_REQUEST['tbWidth'];
$tbHeight=$_REQUEST['tbHeight'];
$existAdmin=$_REQUEST['existAdmin'];
$TB_iframe=true;

$searchType=$_REQUEST['searchType'];
$searchKey=$_REQUEST['searchKey'];

$adminArray=Array();
if($existAdmin) $adminArray=explode(",",$existAdmin);

unset($prepareParm);
?>
<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' id='bbse-commerce-admin-ui-css'  href='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>css/admin-style.css' type='text/css' media='all' />
	<script type='text/javascript' src='<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL;?>js/jquery.min.js'></script>
</head>
<body>
	<script language="JavaScript">
		jQuery(document).ready(function() {
			jQuery("#searchKey").focus();

			jQuery('body').keyup(function(e) {
				if (e.keyCode == 13) go_search();       
			});
		});

		function go_search(){
			var searchType=jQuery("#searchType").val();
			var searchKey=jQuery("#searchKey").val();
			var tbWidth="<?php echo $tbWidth;?>";
			var tbHeight="<?php echo $tbHeight;?>";
			var existAdmin="<?php echo $existAdmin;?>";

			if(!searchKey){
				alert("이름 또는 아이디를 입력해 주세요.    ");
				jQuery("#searchKey").focus();
				return;
			}
			else{
				var strPara="existAdmin="+existAdmin;
				if(tbWidth) strPara +="&width="+tbWidth;
				if(tbHeight) strPara +="&height="+tbHeight;
				if(searchType) strPara +="&searchType="+searchType;
				if(searchKey) strPara +="&searchKey="+searchKey;
				strPara +="&TB_iframe=true";

				self.window.location.href ="<?php echo BBSE_COMMERCE_PLUGIN_WEB_URL?>admin/bbse-commerce-popup-admin-list.php?"+strPara;
			}
		}

		function add_admin(adminId,adminName){
			jQuery(parent.document).contents().find("#admin_id").val(adminId);
			jQuery(parent.document).contents().find("#admin_name").val(adminName);
			parent.remove_popup();
		}
	</script>

	<div class="wrap">
		<div id="popup-goods-list">
			<div class="clearfix" style="height:30px;"></div>
			<div style="padding:0 0 20px 30px;font-size:12px;">- 관리자 등급 회원만 나타납니다.<br />- 회원등급 변경 및 관리는 회원관리에서 변경가능합니다.</div>
			<table class="dataTbls collapse">
			<colgroup><col width=""><col width=""><col width=""></colgroup>
				<tr>
					<td align="center">
						<select name="searchType" id="searchType" style="width:100px;"><option value="all" <?php echo (!$searchType || $searchType=='all')?"selected='selected'":"";?>>선택</option><option value="name" <?php echo ($searchType=='name')?"selected='selected'":"";?>>이름</option><option value="user_id" <?php echo ($searchType=='user_id')?"selected='selected'":"";?>>아이디</option></select>
					</td>
					<td align="center">
						<input type="text" name="searchKey" id="searchKey" value="<?=$searchKey?>" style="width:90%;" />
					</td>
					<td align="center">
						<button type="button"class="button-small blue" onClick="go_search();" style="height:25px;"> 검색</button>
					</td>
				</tr>
			</table>
			<?php
			if($searchType) { // 검색어가 있을 경우 수행
			?>
			<table class="dataTbls collapse">
			<colgroup><col width="50px"><col width=""><col width=""><col width=""></colgroup>
				<tr>
					<th>번호</th>
					<th>이름</th>
					<th>아이디</th>
					<th>선택</th>
				</tr>
			<?php
			
				$sOption="";
				if($searchKey){
					if($searchType && $searchType!='all'){
						$sOption .=" AND ".$searchType." LIKE %s";
						$prepareParm[]="%".like_escape($searchKey)."%";
					}
					else{
						$sOption .=" AND (user_id LIKE %s OR name LIKE %s)";
						$prepareParm[]="%".like_escape($searchKey)."%";
						$prepareParm[]="%".like_escape($searchKey)."%";
					}
				}

				$s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_membership WHERE user_class='1'".$sOption, $prepareParm);
				$s_total = $wpdb->get_var($s_total_sql);    // 총 상품수

				if($s_total>'0') {
					$sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_membership WHERE user_class='1'".$sOption." ORDER BY user_no ASC", $prepareParm);
					$result = $wpdb->get_results($sql);

					$i='0';
					foreach($result as $i=>$data) {
						$num = $s_total-$i; //번호
				?>
					<tr>
						<td style="text-align:center;"><?php echo $num;?></td>
						<td style="text-align:center;"><?php echo $data->name;?></td>
						<td style="text-align:center;"><?php echo $data->user_id;?></td>
						<td style="text-align:center;">
							<?php if(in_array($data->user_id,$adminArray)){?>
								<button type="button"class="button-small gray" style="height:25px;">사용</button>
							<?php }else{?>
								<button type="button"class="button-small red" onClick="add_admin('<?php echo $data->user_id;?>','<?php echo $data->name;?>');" style="height:25px;">선택</button>
							<?php }?>
						</td>
					</tr>
			<?php
						$i--;
					}
				} else {
			?>
					<tr>
						<td colspan="4" style="text-align:center;">검색 결과가 존재하지 않습니다.</td>
					</tr>

			<?php
				}
			?>
			</table>
			<?php
			}else{
			    $s_total_sql  = $wpdb->prepare("SELECT count(*) FROM bbse_commerce_membership WHERE user_class='1'".$sOption, $prepareParm);
			    $s_total = $wpdb->get_var($s_total_sql);    // 총 상품수
			    
			    
			    ?>
			<table class="dataTbls collapse">
			<colgroup><col width="50px"><col width=""><col width=""><col width=""></colgroup>
				<tr>
					<th>번호</th>
					<th>이름</th>
					<th>아이디</th>
					<th>선택</th>
				</tr>
			<?php
			
			    if($s_total>'0') {
			        $sql  = $wpdb->prepare("SELECT * FROM bbse_commerce_membership WHERE user_class='1'".$sOption." ORDER BY user_no ASC", $prepareParm);
			        $result = $wpdb->get_results($sql);
			        
			        $i='0';
			        foreach($result as $i=>$data) {
			            $num = $s_total-$i; //번호
			            ?>
					<tr>
						<td style="text-align:center;"><?php echo $num;?></td>
						<td style="text-align:center;"><?php echo $data->name;?></td>
						<td style="text-align:center;"><?php echo $data->user_id;?></td>
						<td style="text-align:center;">
							<?php if(in_array($data->user_id,$adminArray)){?>
								<button type="button"class="button-small gray" style="height:25px;">사용</button>
							<?php }else{?>
								<button type="button"class="button-small red" onClick="add_admin('<?php echo $data->user_id;?>','<?php echo $data->name;?>');" style="height:25px;">선택</button>
							<?php }?>
						</td>
					</tr>

			<?php
						$i--;
					}
				} else {
			?>	
					<tr>
						<td colspan="4" style="text-align:center;">검색 결과가 존재하지 않습니다.</td>
					</tr>

			<?php
				}
			}
			?>
			</table>
		</div>
	</div>
</body>
</html>

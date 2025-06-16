<?php
session_start();
$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include $includeURL['0'].'/wp-load.php';
header("Content-Type: text/html; charset=UTF-8");

$curUserPermision = current_user_level();  // 현재 회원의 레벨 검사
$current_user = wp_get_current_user();  // 현재 회원의 정보 추출

$V = $_POST;

if(empty($V['bname']))      { echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>"; exit; }
if(empty($V['no']))         { echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>"; exit; }
if(empty($V['skin']))       { echo "<script type='text/javascript'>alert('정상적인 접근이 아닙니다.');history.back();</script>"; exit; }
if(empty($V['page']))       $V['page']       = 1;
if(empty($V['keyfield']))   $V['keyfield']   = NULL;
if(empty($V['keyword']))    $V['keyword']    = NULL;
if(empty($V['search_chk'])) $V['search_chk'] = NULL;
if(empty($V['mode']))       $V['mode']       = NULL;
if(empty($V['cate']))       $V['cate']       = NULL;

$action_url = get_permalink($V['page_id']);

if(preg_match("/\bpage_id\b/", $action_url)){
	$link_add = "&amp;";
} else {
	$link_add = "?";
}

$tblBname = $wpdb->prefix.'bbse_'.$V['bname'].'_board';

$prepare = NULL;
$prepare = $wpdb->prepare( "SELECT * FROM {$tblBname} WHERE `no`=%d", array( $V['no'] ) );
$board   = $wpdb->get_row( $prepare );

if(empty($board->no)){echo "<script type='text/javascript'>alert('해당 게시물이 존재하지 않습니다.');history.back();</script>";}

if(empty($V['pwd'])){
	echo "
		<script type='text/javascript'>
		location.href = '".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$V['skin']."/pass_check.php?page_id=".$V['page_id']."&nType=".bbse_board_parameter_encryption($V['bname'], $V['mode'], $V['no'], $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])."&wrong=1';
		</script>";
	exit;

}else{
	$prepare    =  NULL;
	$prepare    = $wpdb->prepare( "SELECT password( %s )", array( $V['pwd'] ) );
	$write_pass = $wpdb->get_var( $prepare );

	if($write_pass == $board->pass){
?>
<html>
<body onload="document.pass_frm.submit();">
<form name="pass_frm" method="post" action="<?php echo $action_url.$link_add?>nType=<?php echo bbse_board_parameter_encryption($V['bname'], "view", $V['no'], $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])?>">
<input type="hidden" name="passcheck" value="1" />
</form>
</body>
</html>
<?php
	}else{
		echo "
			<script type='text/javascript'>
			location.href = '".BBSE_BOARD_PLUGIN_WEB_URL."skin/".$V['skin']."/pass_check.php?page_id=".$V['page_id']."&nType=".bbse_board_parameter_encryption($V['bname'], $V['mode'], $V['no'], $V['page'], $V['keyfield'], $V['keyword'], $V['search_chk'], $V['cate'])."&wrong=2';
			</script>";
		exit;
	}
}
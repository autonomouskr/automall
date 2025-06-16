<?php
session_start();
header("Content-Type: text/html; charset=UTF-8");

$includeURL = explode(DIRECTORY_SEPARATOR.'wp-content', dirname(__FILE__).DIRECTORY_SEPARATOR);
include_once $includeURL[0]."/wp-load.php";
if ( !is_user_logged_in() )
{
  echo 'DENIED';
  exit;
}

global $theme_shortname;

  /**
	 * 티스토리로 부터 받은 code와 함께 access_token 요청을 합니다.
	 */
	$authorization_code = $_REQUEST['code'];

	$client_id     = get_option($theme_shortname."_tistory_clientid");
	$client_secret = get_option($theme_shortname."_tistory_skey");
  $redirect_uri  = get_option($theme_shortname."_tistory_callback");
	$grant_type    = 'authorization_code';

	$url = 'https://www.tistory.com/oauth/access_token/?code=' . $authorization_code . '&client_id=' . $client_id .
			'&client_secret=' . $client_secret . '&redirect_uri=' . urlencode($redirect_uri) . '&grant_type=' . $grant_type;

  $response = wp_remote_get($url);
  if(is_wp_error($response))
    return false;
  else
  {
    $returnStr = wp_remote_retrieve_body($response);
    $exploded  = explode('&', $returnStr);
    if ($exploded[0] == 'error=invalid_request')
    {
      $error   = explode('=', $exploded[1]);
      update_option($theme_shortname."_tistory_accesstoken", '');
    }
    else
    {
      $token   = explode('=',$returnStr);
      update_option($theme_shortname."_tistory_accesstoken", $token[1]);
    }
?>
<!DOCTYPE html>
<html lang="ko-KR">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php if (!$error && $token[1]){?>
<script>
  opener.location.reload(true);
  self.close();
</script>
<?php }else{?>
<div style="word-break:wrap">에러 :  <?php echo $error[1]?></div>
<?php }?>
</body>
</html>
<?php
  }
<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

if (!function_exists('check_theme_update')) {
	add_filter('pre_set_site_transient_update_themes', 'check_theme_update');

	function check_theme_update($checked_data) {
		global $wp_version,$update_url;
		
		if (empty($checked_data->checked))
			return $checked_data;
		
		$theme_base = get_template();            // 테마 디렉토리명
		$info=wp_get_theme($theme_base);         // 테마 정보

		$request = array(
			'slug' => trim($info['Name']),       // 테마 명
			'version' => trim($info['Version']),  // 테마 버전
			'license_key'=>bbse_theme_get_license_key(),  /*** for License Key ***/
			'blog_home'=>BBSE_BLOG_HOME,                 /*** for License Key ***/
			'blog_server_ip'=>BBSE_BLOG_SERVER_IP,       /*** for License Key ***/
			'site_name'=>BBSE_BLOG_NAME                    /*** for License Key ***/
		);
		
		// UPDATE 검사
		$send_for_check = array(
			'body' => array(
				'action' => 'theme_update', 
				'request' => serialize($request),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);

		$raw_response = wp_remote_post($update_url, $send_for_check);

		if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)){ // 정상 Check인 경우
			$response = unserialize($raw_response['body']);
		}
			
		// 외모-테마의 screenshot 레이어에 업데이트 표시
		if (!empty($response)) $checked_data->response[$theme_base] = $response;

		return $checked_data;
	}

	/*** for License Key ***/
	function check_theme_license($theme_name,$license_key,$blog_home,$blog_server_ip,$site_name,$license_status,$payment_agent,$payment_id){
		global $update_url;

		$request = array(
			'slug' => trim($theme_name),       // 테마 명
			'license_key'=>$license_key,
			'blog_home'=>$blog_home, 
			'blog_server_ip'=>$blog_server_ip, 
			'site_name'=>$site_name,
			'payment_agent'=>$payment_agent,
			'payment_id'=>$payment_id
		);
		
		// UPDATE 검사
		$send_for_check = array(
			'body' => array(
				'action' => 'theme_license', 
				'request' => serialize($request),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);

		$raw_response = wp_remote_post($update_url, $send_for_check);

		try{
			if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)){ // 정상 Check인 경우
				$response = unserialize($raw_response['body']);
				$rtn_body=($response ->result==='true' || $response ->result==='false')?$response ->result:$license_status; // Network failure, return the previous license status value
				return $rtn_body;
			}
			return $license_status;  // Network failure, return the previous license status value
		}
		catch(Exception $e){
			return $license_status;  // Network failure, return the previous license status value
		}
    }
	/*** for License Key ***/

	/*
	if (is_admin()){
		$current = get_transient($theme_base);
	}
	*/
}
?>
<?php
/********** 외부 블로그 연동 (시작) **********/
// 관리자  글목록에 네이버 연동 ID 칼럼을 추가합니다.
if (!function_exists('bbse_posts_external_blog_id_label')) {
	function bbse_posts_external_blog_id_label($defaults){
		global $theme_shortname;
		if(get_option($theme_shortname."_naver_use_webmaster")=='U' || get_option($theme_shortname."_naver_blog_api_use")=='U' || get_option($theme_shortname."_tistory_use")=='U'){
			$defaults['external_blog_id'] = '외부 POST 등록';
		}
		return $defaults;
	}
}
add_filter('manage_posts_columns', 'bbse_posts_external_blog_id_label');

if (!function_exists('bbse_posts_external_blog_id_view')) {
	function bbse_posts_external_blog_id_view($column_name, $id){
		global $theme_shortname;
		  $rtnStr="";
		  if($column_name == 'external_blog_id'){
			if(get_option($theme_shortname."_naver_blog_api_use")=='U'){
				$nPost_id = bbse_get_posts_external_blog_id(get_the_ID());
				if($nPost_id == "등록안됨") {
					$rtnStr .='<span style="color:#AEAEAE;">· 네이버 블로그 : 미등록</span>';
				}else{
					$rtnStr .= '<span style="color:#0074a2;">· 네이버 블로그 : <a href="http://blog.naver.com/'.get_option($theme_shortname."_naver_blog_api_id").'/'.$nPost_id.'" target="_blank">등록</a></span>';
				}
			}

			if(get_option($theme_shortname."_naver_use_webmaster")=='U'){
				if($rtnStr) $rtnStr .="<br />";
				$type = get_post_meta( $id, 'nsyndiRequestType', true );
				$date = get_post_meta( $id, 'nsyndiUpdateDate', true );
				if($type) $rtnStr .= '<span style="color:#0074a2;">· 네이버 신디 : 등록</span>';
				else $rtnStr .= '<span style="color:#AEAEAE;">· 네이버 신디 : 미등록</span>';
			}

			if(get_option($theme_shortname."_tistory_use")=='U'){
				if($rtnStr) $rtnStr .="<br />";
				$link = get_post_meta( $id, 'tistoryPostUrl', true );
				$tdate = get_post_meta( $id, 'tistoryUpdateDate', true );
				if ($link) $rtnStr .= '<span style="color:#0074a2;">· 티스토리 : <a href="'.$link.'" target="_blank">등록</a></span>';
				else $rtnStr .= '<span style="color:#AEAEAE;">· 티스토리 : 미등록</span>';
			}
			echo $rtnStr;
		  }
	}
}
add_action('manage_posts_custom_column', 'bbse_posts_external_blog_id_view',5,2);

// 외부 포스트 ID 표시.
if (!function_exists('bbse_get_posts_external_blog_id')) {
	function bbse_get_posts_external_blog_id($postID){
	  $nPost_id = get_post_meta($postID, 'external_blog_id', true);
	  if($nPost_id=='') return "";
	  else return $nPost_id;
	}

	function bbse_the_posts_external_blog_id($postID){
	  echo bbse_get_posts_external_blog_id($postID);
	}
}

// 외부 카테고리 저장.
if (!function_exists('bbse_get_posts_external_blog_category')) {
	function bbse_get_posts_external_blog_category($postID){
	  $nPost_cat = get_post_meta($postID, 'blog_category', true);
	  if($nPost_cat=='') return "";
	  else return $nPost_cat;
	}

	function bbse_the_posts_external_blog_category($postID){
	  echo bbse_get_posts_external_blog_category($postID);
	}
}

// 포스트 등록 시 external_blog_id 필드 추가 & 외부 포스트 등록
function bbse_add_custom_external_id_field() {
	global $post, $saveFlag, $theme_shortname;
	$nPost_id = bbse_get_posts_external_blog_id($post->ID);
	if($nPost_id==''){
		delete_post_meta($post->ID, 'external_blog_id');
		add_post_meta($post->ID, 'external_blog_id', '등록안됨');
	}

	$nPost_cat = bbse_get_posts_external_blog_category($post->ID);
	if($nPost_cat==''){
		delete_post_meta($post->ID, 'blog_category');
		add_post_meta($post->ID, 'blog_category', '');
	}

	if ( get_option($theme_shortname."_naver_blog_api_use")=='U' && isset($_POST['enabled']) && get_post_status ($post->ID)=='publish' &&  !$saveFlag) {
		bbse_publish_post_to_external($post->ID);
		$saveFlag='1';
	}
}
add_action( 'save_post', 'bbse_add_custom_external_id_field' );


function bbse_externalblog_add_post_meta_boxes() {
	global $theme_shortname;
	if(get_option($theme_shortname."_naver_blog_api_use")=='U' && get_option($theme_shortname."_naver_blog_api_url") && get_option($theme_shortname."_naver_blog_api_id") && get_option($theme_shortname."_naver_blog_api_pw")){
		add_meta_box('bbse-externalblog-meta', __('네이버 블로그 자동등록', 'bbse_meta_box_external_blog'), 'bbse_meta_box_external_blog', 'post', 'normal', 'high');
	}
}
add_action( 'add_meta_boxes', 'bbse_externalblog_add_post_meta_boxes' );

function bbse_meta_box_external_blog( $post, $box ) {
	global $theme_shortname;
	if(get_option($theme_shortname."_naver_blog_api_use")=='U') $useChecked='checked="checked"';
	else $useChecked='';

	if(get_option($theme_shortname."_naver_blog_api_origin")=='U'){
		$originChecked_view='checked="checked"';
		$originChecked_hide='';
	}
	else{
		$originChecked_view='';
		$originChecked_hide='checked="checked"';
	}

	$nPost_id = bbse_get_posts_external_blog_id($post->ID);
	if($nPost_id && $nPost_id!='등록안됨' && $nPost_id!='0'){
		$useTitle="수정";
		$blogLink='네이버 블로그 : <a href="http://blog.naver.com/'.get_option($theme_shortname."_naver_blog_api_id").'/'.$nPost_id.'" target="_blank">http://blog.naver.com/'.get_option($theme_shortname."_naver_blog_api_id").'/'.$nPost_id.'</a>';
	}
	else{
		$useTitle="등록";
		$blogLink='';
	}

	$nPost_cat = bbse_get_posts_external_blog_category($post->ID);
	$strCatList='<select name="blog_category"><option value="">기본 카테고리</option>';

	if(trim(get_option($theme_shortname."_naver_blog_api_category"))) {
		$cateList=explode(",",get_option($theme_shortname."_naver_blog_api_category"));
	}
	elseif(get_option($theme_shortname."_naver_blog_api_url") && get_option($theme_shortname."_naver_blog_api_id") && get_option($theme_shortname."_naver_blog_api_pw")){
		$rtnCateList=bbse_get_blog_category(get_option($theme_shortname."_naver_blog_api_url"),get_option($theme_shortname."_naver_blog_api_id"),get_option($theme_shortname."_naver_blog_api_pw"));
		$cateList=explode(",",$rtnCateList);

		update_option($theme_shortname."_naver_blog_api_category", $rtnCateList);
		update_option($theme_shortname."_naver_blog_api_category_update_date", current_time('timestamp'));
	}

	$strCnt=0;
	for($z=0;$z<sizeof($cateList);$z++){
		if(trim($cateList[$z])){
			if(trim($cateList[$z])==$nPost_cat) $catSelected="selected";
			else $catSelected="";
			$strCatList .='<option value="'.trim($cateList[$z]).'" '.$catSelected.'>'.trim($cateList[$z]).'</option>';
			$strCnt++;
		}
	}

	$strCatList .='</select>';

	echo '
      <table width="100%">
				<tbody>
					<tr valign="top">
						<td style="width:125px;"><label for="enabled">자동'.$useTitle.' 사용여부 : </label></td>
						<td><input name="enabled" type="checkbox" id="enabled" value="1" '.$useChecked.'/> 사용</td>
						<td>'.$blogLink.'</td>
					</tr>
					<tr valign="top">
						<td style="width:125px;">카테고리 : </td>
						<td colspan="2">
							<label>'.$strCatList.'</label>
						</td>
					</tr>
					<tr valign="top">
						<td style="width:125px;">출처 표시 여부 : </td>
						<td colspan="2">
							<label for="origin_enable"><input name="origin_enable" type="radio" id="origin_enable" value="1" '.$originChecked_view.'/>표시함</label>
							<label for="origin_disable"><input name="origin_enable" type="radio" id="origin_disable" value="0" '.$originChecked_hide.' />표시하지 않음</label>
						</td>
					</tr>
				</tbody>
			</table>';
}

if (!function_exists('bbse_publish_post_to_external')) {
    function bbse_publish_post_to_external($post_id) {
		global $theme_shortname;

		$tPost=get_post($post_id);

		$title= $tPost->post_title;
		$description= wpautop(iconv_substr(do_shortcode($tPost->post_content), 0, mb_strlen($tPost->post_content), 'utf-8'));

		if($_POST['origin_enable']){
			$description = $description.'<br/> [출처 : <a href='.get_permalink().'> '.get_option("blogname").' 원문 보기 </a> ]';
		}

		$g_blog_url = get_option($theme_shortname."_naver_blog_api_url");
		$user_id = get_option($theme_shortname."_naver_blog_api_id");
		$blogid = get_option($theme_shortname."_naver_blog_api_id");
		$password = get_option($theme_shortname."_naver_blog_api_pw");
		$publish = true;

		$nPost_id = bbse_get_posts_external_blog_id($post_id);

		if($nPost_id=='' || $nPost_id=='등록안됨' || $nPost_id=='0') {
			$metaWeblogType="metaWeblog.newPost";
			$metaWeblogParam=$user_id; // 신규 등록인 경우 -> USER ID 전송
		}
		else{
			$metaWeblogType="metaWeblog.editPost";
			$metaWeblogParam=$nPost_id; // 수정인 경우 -> 네이버 POST ID 전송
		}

		$tags = wp_get_post_tags( $post_id);
		$tagsParam='';

		if(sizeof($tags)>'0'){
			foreach ( $tags as $tag ) {
				if($tagsParam) $tagsParam .=",";
				$tagsParam .=$tag->name;
			}
		}

		$str='<?xml version="1.0" encoding="utf-8"?>
					<methodCall>
						<methodName>'.$metaWeblogType.'</methodName>
						<params>
							<param><value><string>'.$metaWeblogParam.'</string></value></param>
							<param><value><string>'.$blogid.'</string></value></param>
							<param><value><string>'.$password.'</string></value></param>
							<param>
								<value>
									<struct>
										<member><name>title</name><value><string><![CDATA['.$title.']]></string></value></member>
										<member><name>description</name><value><string><![CDATA['.$description.']]></string></value></member>
										<member><name>categories</name><value><array><data><value>'.$_POST['blog_category'].'</value></data></array></value></member>
										<member><name>tags</name><value><string>'.$tagsParam.'</string></value></member>
									</struct>
								</value>
							</param>
							<param><value><boolean>'.$publish.'</boolean></value></param>
						</params>
					</methodCall>';

		$response = wp_remote_post( $g_blog_url, array('timeout' => 10, 'headers' => array('Accept-Encoding' => '','content-type' => 'text/xml; charset=utf8'), 'sslverify' => false, 'body'=>$str) );
		$str_body = wp_remote_retrieve_body($response);
		$obj_xml    = @simplexml_load_string($str_body);

		if($metaWeblogType=='metaWeblog.newPost') $externalPostId=(string)$obj_xml->params->param->value[0];
		else $externalPostId=(string)$obj_xml->params->param->value->boolean[0];

		if($externalPostId){
			if($nPost_id=='등록안됨' || $nPost_id=='0'){
				update_post_meta($post_id, 'external_blog_id', $externalPostId);
			}

			update_post_meta($post_id, 'blog_category', trim($_POST['blog_category']));
		}

	}
}

// 포스트 영구적으로 삭제 시 외부 포스트 삭제
if (!function_exists('bbse_delete_external_blog')) {
    function bbse_delete_external_blog($tPostID="") {
		global $post, $theme_shortname;

		if(!$tPostID) $tPostID=$post->ID;

		if (get_option($theme_shortname."_naver_blog_api_use")=='U'){
			$g_blog_url = get_option($theme_shortname."_naver_blog_api_url");
			$user_id = get_option($theme_shortname."_naver_blog_api_id");
			$blogid = get_option($theme_shortname."_naver_blog_api_id");
			$password = get_option($theme_shortname."_naver_blog_api_pw");
			$publish = true;
			$nPost_id = bbse_get_posts_external_blog_id($tPostID);


			if($nPost_id && $nPost_id!='등록안됨' && $nPost_id!='0'){
				$str='<?xml version="1.0" encoding="utf-8"?>
							<methodCall>
								<methodName>blogger.deletePost</methodName>
								<params>
									<param><value><string>'.$user_id.'</string></value></param>
									<param><value><string>'.$nPost_id.'</string></value></param>
									<param><value><string>'.$blogid.'</string></value></param>
									<param><value><string>'.$password.'</string></value></param>
									<param><value><boolean>'.$publish.'</boolean></value></param>
								</params>
							</methodCall>';
				$response = wp_remote_post( $g_blog_url, array('timeout' => 10, 'headers' => array('Accept-Encoding' => '','content-type' => 'text/xml; charset=utf8'), 'sslverify' => false, 'body'=>$str) );
				$str_body = wp_remote_retrieve_body($response);
				$obj_xml    = @simplexml_load_string($str_body);
				$delResult=(string)$obj_xml->params->param->value->boolean[0];
				if($delResult=='1'){
					update_post_meta($tPostID, 'external_blog_id', '등록안됨');
					update_post_meta($tPostID, 'blog_category', '');
				}
			}
		}
	}
	//add_action( 'delete_post', 'bbse_delete_external_blog' ); // 휴지통
	add_action( 'trashed_post', 'bbse_delete_external_blog' ); // BULK ACTIONS 휴지통으로 이동
}

// BULK ACTION (휴지통으로 이동)
if (!function_exists('bbse_custom_bulk_actions')) {
	function bbse_custom_bulk_actions(){
		if($_REQUEST['action']=='trash' && $_REQUEST['post_type']=='post'){
			for($p=0;$p<sizeof($_REQUEST['post']);$p++){
				bbse_delete_external_blog($_REQUEST['post'][$p]);
			}
		}
	}
	add_action('check_admin_referer','bbse_custom_bulk_actions');
}

// 외부 블로그에서 카테고리 목록 가져오기
if (!function_exists('bbse_get_blog_category')) {
    function bbse_get_blog_category($g_blog_url,$blogid,$password) {
		global $post, $theme_shortname;

		if($g_blog_url && $blogid && $password){
			$str='<methodCall>
						<methodName>metaWeblog.getCategories</methodName>
						<params>
							<param>
								<value>
									<string>'.$blogid.'</string>
								</value>
							</param>
							<param>
								<value>'.$blogid.'</value>
							</param>
							<param>
								<value>
									<string>'.$password.'</string>
								</value>
							</param>
						</params>
					</methodCall>';

			$response = wp_remote_post( $g_blog_url, array('timeout' => 10, 'headers' => array('Accept-Encoding' => '','content-type' => 'text/xml; charset=utf8'), 'sslverify' => false, 'body'=>$str) );
			$str_body = wp_remote_retrieve_body($response);
			$obj_xml    = @simplexml_load_string($str_body);

			$tmpList=$obj_xml->params->param->value->array ->data->value;

			$cateList="";

			for($i=0;$i<sizeof($tmpList);$i++){
				if($cateList) $cateList .=",";
				$cateList .=$tmpList[$i]->struct->member[1]->value;
			}

			return $cateList;
		}
		else return "";
	}
}


// 외부 블로그 USER 정보 가져오기
if (!function_exists('bbse_get_blog_user')) {
    function bbse_get_blog_user($g_blog_url,$blogid,$password) {
		global $post, $theme_shortname;

		if($g_blog_url && $blogid && $password){
			return true; // 2018.04.25 부터 통신 불가 => https://section.blog.naver.com/Notice.nhn?docId=10000000000030660878
			$str='<methodCall>
						<methodName>metaWeblog.getRecentPosts</methodName>
						<params>
							<param>
								<value>
									<string>'.$blogid.'</string>
								</value>
							</param>
							<param>
								<value>'.$blogid.'</value>
							</param>
							<param>
								<value>
									<string>'.$password.'</string>
								</value>
							</param>
							<param>
								<value>
									<int>1</int>
								</value>
							</param>
						</params>
					</methodCall>';

			$response = wp_remote_post( $g_blog_url, array('timeout' => 10, 'headers' => array('Accept-Encoding' => '','content-type' => 'text/xml; charset=utf8'), 'sslverify' => false, 'body'=>$str) );
			$str_body = wp_remote_retrieve_body($response);
			$obj_xml    = @simplexml_load_string($str_body);

			if($obj_xml && sizeof($obj_xml -> fault)=='0') return true;
			else	return false;
		}
		else return false;
	}
}
/********** 외부 블로그 연동 (끝) **********/
?>
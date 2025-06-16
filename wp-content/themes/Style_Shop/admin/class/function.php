<?php
// GET 파라미터 암호화
if(!function_exists('bbse_commerce_parameter_encryption')){
	function bbse_commerce_parameter_encryption($boardName, $mode, $no="", $page="", $keyfield="", $keyword="", $search_chk="", $category="", $ref="", $cno=""){
		$enCode = new BBSeThemeSecretCode;
		$srcCode = "bname=".$boardName."&mode=".$mode."&no=".$no."&page=".$page."&keyfield=".$keyfield."&keyword=".$keyword."&search_chk=".$search_chk."&cate=".$category."&ref=".$ref."&cno=".$cno;
		$rstCode = $enCode->enc($srcCode);

		return base64_encode($rstCode);
		//return $srcCode;
	}
}

// GET 파라미터 복호화
if(!function_exists('bbse_commerce_parameter_decryption')){
	function bbse_commerce_parameter_decryption($destCode){
		$deCode = new BBSeThemeSecretCode;
		$aCode = $deCode->dec(base64_decode($destCode));

		$bCode = explode("&", $aCode);
		$rtnCode = Array();

		for($i = 0; $i < count($bCode); $i++){
			$cCode = explode("=", $bCode[$i]);
			if(count($cCode) == 2){
				$rtnCode[$cCode[0]] = $cCode[1];
			}
		}
		return $rtnCode;
	}
}

// GET 파라미터 암호화
if(!function_exists('bbse_commerce_parameter_encryption')){
	function bbse_commerce_parameter_encryption($boardName, $mode, $no="", $page="", $keyfield="", $keyword="", $search_chk="", $category="", $ref="", $cno=""){
		$enCode = new BBSeThemeSecretCode;
		$srcCode = "bname=".$boardName."&mode=".$mode."&no=".$no."&page=".$page."&keyfield=".$keyfield."&keyword=".$keyword."&search_chk=".$search_chk."&cate=".$category."&ref=".$ref."&cno=".$cno;
		$rstCode = $enCode->enc($srcCode);

		return base64_encode($rstCode);
		//return $srcCode;
	}
}



// 문자열 자르기
if(!function_exists('cut_text')){
	function cut_text($text, $text_count, $more_text="…") {
		$length = strlen($text);
		if($length <= $text_count) return $text;
		else return mb_substr($text, 0, $text_count, "UTF-8").$more_text;
	}
}

if(!function_exists("get_custom_link_url")){
	function get_custom_link_url($cData){
		unset($customOut);
		if($cData){
			preg_match_all("/<a[^>]*href=[\"']?([^>\"']+)[\"']?[^>]*>/i", $cData, $customOut, PREG_PATTERN_ORDER);
		}
		return $customOut[1][0];
	}
}

if(!function_exists('get_category_by_url')){
	function get_category_by_url($url){
		$category_ids = get_all_category_ids();
		foreach($category_ids as $tCatID){
			if(get_category_link($tCatID) == $url) return $tCatID;
		}
		return false;
	}
}

// 워드프레스 카테고리/페이지 리스트 정보 가져오기
if(!function_exists("get_custom_list")){
	function get_custom_list($tType){
		switch($tType){
			case "category" :
				$args = array(
					'show_option_all' => '',
					'orderby' => 'name',
					'order' => 'ASC',
					'style' => 'none',
					'hide_empty' => 0,
					'use_desc_for_title' => 1,
					'hierarchical' => 1,
					'title_li' => __( 'Categories' ),
					'show_option_none' => __('No categories'),
					'number' => null,
					'echo' => 0,  // 화면출력:1
					'taxonomy' => 'category',
					'walker' => null
				);

				$get_wp_cate = wp_list_categories($args);
				$list_cate = explode("\n", $get_wp_cate);

				$cate_data = Array();
				$cate_cnt = '0';
				for($s = 0; $s < sizeof($list_cate); $s++){
					$getCustomLink = "";
					if(strip_tags($list_cate[$s])){
						$getCustomLink = get_custom_link_url($list_cate[$s]);
						if($getCustomLink){
							$tmp_cateId = explode("cat=", $getCustomLink);
							$cate_data[$cate_cnt]['id'] = $tmp_cateId['1'];
							$cate_data[$cate_cnt]['link'] = $getCustomLink;
							$cate_data[$cate_cnt]['name'] = strip_tags($list_cate[$s]);

							if(!$cate_data[$cate_cnt]['id'] && $tmpCategory){
								$tmpCategory = get_category_by_url($cate_data[$cate_cnt]['link']);
								$cate_data[$cate_cnt]['id'] = $tmpCategory;
							}

							$cate_cnt++;
						}
					}
				}
				return $cate_data;
				break;
			case "page" :
				$args = array(
					'authors' => '',
					'child_of' => 0,
					'date_format' => get_option('date_format'),
					'depth' => 0,
					'echo' => 0,
					'post_type' => 'page',
					'post_status' => 'publish',
					'sort_column' => 'menu_order, post_title',
					'title_li' => '', 
					'walker' => ''
				);

				$get_wp_page = wp_list_pages($args);
				$list_page = explode("\n", $get_wp_page);

				$page_data = Array();
				$page_cnt = '0';

				for($s = 0; $s < sizeof($list_page); $s++){
					$getCustomLink = "";
					if(strip_tags($list_page[$s])){
						$getCustomLink = get_custom_link_url($list_page[$s]);
						if($getCustomLink){
							$tmp_pageId = explode("page_id=", $getCustomLink);
							$page_data[$page_cnt]['id'] = $tmp_pageId['1'];
							$page_data[$page_cnt]['link'] = $getCustomLink;
							$page_data[$page_cnt]['name'] = strip_tags($list_page[$s]);
							if(!$page_data[$page_cnt]['id']) $page_data[$page_cnt]['id'] = url_to_postid($page_data[$page_cnt]['link']);
							$page_cnt++;
						}
					}
				}
				return $page_data;
				break;
			default : 
				break;
		}
	}
}

if(!function_exists("CreatePaging")){
	function CreatePaging($paged, $total_pages, $add_args=false){
		/**
		 * $paged : 현재 페이지
		 * $total_pages : 총 페이지
		 * $add_args : 추가 전달값(쿼리스트링)
		 */

		$paging_css = "
			<style>
			.pagination{
				clear:both;
				padding:20px 0;
				position:relative;
				font-size:11px;
				line-height:13px;
			}
			.pagination span, .pagination a{
				display:block;
				float:left;
				margin: 2px 2px 2px 0;
				padding:6px 9px 5px 9px;
				text-decoration:none;
				width:auto;
				color:#fff !important;
				background: #6d6d6d !important;
			}
			.pagination a:hover{
				color:#fff !important;
				background: #3279bb !important;
			}
			.pagination .current{
				padding:6px 9px 5px 9px;
				background: #3279bb !important;
				color:#fff !important;
			}
			</style>";
		
		$paging = paginate_links(array(
			'base' => '%_%',
			'format' => '?paged=%#%',
			'current' => max( 1, $paged ),
			'total' => $total_pages,
			'mid_size' => 20,
			'add_args' => $add_args
		));

		return $paging_css."<div class=\"pagination\">".$paging."</div>";
	}
}
?>
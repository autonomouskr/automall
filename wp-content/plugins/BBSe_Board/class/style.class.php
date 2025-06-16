<?php
class BBSeBoardStyle{
	var $board_name;

	function __construct(){
		add_action('wp', array($this, 'pre_detect_shortcode'));
	}

	function pre_detect_shortcode(){
		global $wpdb, $wp_query;
		$post    = $wp_query->posts;

		$content = '';
		if (isset($post[0])) $content = strip_tags($post[0]->post_content);
		preg_match_all("/\[[^]]+\]/", $content, $check_content);

		for($i = 0; $i < count($check_content[0]); $i++){
			$tmpData = str_replace("[", "", $check_content[0][$i]);
			$tmpData = str_replace("]", "", $tmpData);

			$bData = shortcode_parse_atts($tmpData);

			if( is_array($bData) && in_array("bbse_board", $bData)){
				$this->board_name = $bData['bname'];
				add_action('wp_enqueue_scripts', array($this, 'add_skin_style'));
				break;
			}
		}
	}

	function add_skin_style(){
		global $wpdb;
		$boardInfo = $wpdb->get_row("select * from `".$wpdb->prefix."bbse_board` where `boardname`='".$this->board_name."'");

		// v.1.0.0 초기버전의 일반형 기본 스킨일때 bbs_gray 기본 스킨으로 변경
		if($boardInfo->skinname == "bbs"){
			$wpdb->query("update `".$wpdb->prefix."bbse_board` set `skinname`='bbs_gray' where `board_no`='".$boardInfo->board_no."'");
			$boardInfo = $wpdb->get_row("select * from `".$wpdb->prefix."bbse_board` where `board_no`='".$boardInfo->board_no."'");
		}

		if(!empty($boardInfo->skinname)){
			wp_register_style('bbse-board-style', BBSE_BOARD_PLUGIN_WEB_URL.'skin/'.$boardInfo->skinname.'/style.css');
			wp_enqueue_style('bbse-board-style');
		}
	}
}
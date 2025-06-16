<?php
class BBSeCommerceThemeStyle{
	function __construct(){
		add_action('wp', array($this, 'pre_detect_shortcode'));
	}

	function pre_detect_shortcode(){
		add_action('wp_enqueue_scripts', array($this, 'add_skin_style'));
	}

	function add_skin_style(){
		global $wpdb;
	}	
}
?>
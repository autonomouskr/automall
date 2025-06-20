<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$current_time = current_time('timestamp');

if (current_user_can('administrator'))  require_once('scroll_option_control.php');  // manage_options or administrator

$skinname = get_option($theme_shortname."_sub_skin_name")?get_option($theme_shortname."_sub_skin_name"):"basic";
get_template_part('skin/'.$skinname.'/footer');
?>

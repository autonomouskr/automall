<?php
/*
[�׸� ���� �� ���ǻ���]
1. ����������(Wordpress)�� ������Ʈ ����� ���� �׸�/�÷������� ���� �� �� �缳ġ �ϴ� ����Դϴ�.
   ������Ʈ �� ��� ���� ������ �ʱ�ȭ �ǹǷ� �׸��� �����Ͻô� ���, ���ϵ��׸�(Child Theme) ����� �̿��� �ֽñ� �ٶ��ϴ�.
2. ���ϵ��׸�(Child Theme)�� �̿��� ���� ��� : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$current_time = current_time('timestamp');

if (current_user_can('administrator'))  require_once('scroll_option_control.php');  // manage_options or administrator

$skinname = get_option($theme_shortname."_sub_skin_name")?get_option($theme_shortname."_sub_skin_name"):"basic";
get_template_part('skin/'.$skinname.'/footer');
?>

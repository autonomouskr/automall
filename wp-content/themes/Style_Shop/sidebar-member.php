<?php
/*
[�׸� ���� �� ���ǻ���]
1. ����������(Wordpress)�� ������Ʈ ����� ���� �׸�/�÷������� ���� �� �� �缳ġ �ϴ� ����Դϴ�.
   ������Ʈ �� ��� ���� ������ �ʱ�ȭ �ǹǷ� �׸��� �����Ͻô� ���, ���ϵ��׸�(Child Theme) ����� �̿��� �ֽñ� �ٶ��ϴ�.
2. ���ϵ��׸�(Child Theme)�� �̿��� ���� ��� : https://codex.wordpress.org/ko:Child_Themes
*/

global $theme_shortname;
$skinname = get_option($theme_shortname."_sub_skin_name")?get_option($theme_shortname."_sub_skin_name"):"basic";
get_template_part('skin/'.$skinname.'/sidebar','member');
?>

<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

 global $theme_shortname;

if ( post_password_required() ) {
	return;
}

$fields =  array(
  'author' => '<div class="writer-information-box">
                <p class="writer_info">
                  <input type="text" name="author" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="22" placeholder="이름'.( $req ? '(*)':'').'" /><label for="author"><small>&nbsp;이름'. ( $req ? '(*)':'').'</small></label>
                </p>',

  'email'  => ' <p class="writer_info">
                  <input type="text" name="email" id="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="22" placeholder="이메일'.( $req ? '(*)':'').'" /><label for="email"><small>&nbsp;이메일'. ( $req ? '(*)':'').'</small></label>
                </p>
              </div>',
/*
  'url'    => '<p><input type="text" name="url" id="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="22" tabindex="3" placeholder="'.__('Website', 'Destro').'" />&nbsp;<label for="url"><small>'.__('Website', 'Destro').'</small></label></p>'*/
);

if ( !$user_ID ) {
	$privateText='
    <p class="comment-form-comment">
      <label for="private" style="display: none;">개인정보취급방침</label>
      <textarea id="private" name="private" style="height:100px;" readonly>'.stripslashes(get_option($theme_shortname."_member_private_2")).'</textarea>
    </p>
    <p class="comment-form-comment">
      <input type="checkbox" name="private_ok" id="private_ok" value="Y" style="border:0px;" />
      <label for="private_ok"> 개인정보 수집 및 이용에 동의합니다.</label>
    </p>
    <div id="comment_msg"></div>';
}
else $privateText='<div id="comment_msg"></div>';

$args = array(
  'fields' => $fields,
  'comment_notes_before' => '<p class="comment-notes">이메일은 공개되지 않습니다. 필수 입력창은 * 로 표시되어 있습니다. </p>',

  'comment_notes_after'  => '<p class="form-allowed-tags">다음의 <abbr title="HyperText Markup Language">HTML</abbr> 태그와 속성을 사용할 수 있습니다:  <code>&lt;a href=&quot;&quot; title=&quot;&quot;&gt; &lt;abbr title=&quot;&quot;&gt; &lt;acronym title=&quot;&quot;&gt; &lt;b&gt; &lt;blockquote cite=&quot;&quot;&gt; &lt;cite&gt; &lt;code&gt; &lt;del datetime=&quot;&quot;&gt; &lt;em&gt; &lt;i&gt; &lt;q cite=&quot;&quot;&gt; &lt;strike&gt; &lt;strong&gt; </code></p>'.$privateText,

  'cancel_reply_link'=> '작성취소',

  'title_reply'=>'코멘트 쓰기',

  'title_reply_to'=>'%s님 에게 코멘트 남기기',

  'label_submit'=>'Post Comment'
);
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>

	<h2 class="comments-title">
		<?php printf('&ldquo;%2$s&rdquo;에 %1$s개의 코멘트 ', number_format_i18n( get_comments_number() ), get_the_title() );?>
	</h2>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text">댓글 페이징</h1>
		<div class="nav-previous"><?php previous_comments_link( '&lt;&lt;&nbsp;이전 댓글' ); ?></div>
		<div class="nav-next"><?php next_comments_link('최근 댓글&nbsp;&gt;&gt;' ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php endif; // Check for comment navigation. ?>

  <div class="comment-list">
	<ol>
		<?php
    $commentArgs = array(
      'walker'            => new bbse_walker_comments_list,
      'max_depth'         => '2',
      'style'             => 'ol',
      'callback'          => bbse_list_comments_callback,
      'type'              => 'all',
      'reply_text'        => 'Reply',
      'page'              => '',
      'per_page'          => '',
      'avatar_size'       => 85,
      'reverse_top_level' => null,
      'reverse_children'  => '',
      'format'            => 'xhtml',
    );
  	wp_list_comments($commentArgs);
		?>
	</ol><!-- .comment-list -->
  </div>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text">댓글 페이징</h1>
    <div class="nav-previous"><?php previous_comments_link( '&lt;&lt;&nbsp;이전 댓글' ); ?></div>
		<div class="nav-next"><?php next_comments_link('최근 댓글&nbsp;&gt;&gt;' ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php if ( ! comments_open() ) : ?>
	<p class="no-comments">댓글을 추가 할 수 없습니다.</p>
	<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php comment_form( $args ); ?>

</div><!-- #comments -->

<script language="javascript">
jQuery("#commentform .form-submit #submit").click(function() {
	var ssl_enable="<?php echo get_option($theme_shortname.'_ssl_enable')?>";
	var ssl_domain="<?php echo get_option($theme_shortname.'_ssl_domain')?>";
	var ssl_port="<?php echo get_option($theme_shortname.'_ssl_port')?>";
	var action_url="";
	if(ssl_enable=='U' && ssl_domain){
		if(ssl_port) action_url="https://"+ssl_domain+":"+ssl_port+"/"+"wp-comments-post.php";
		else action_url="https://"+ssl_domain+"/"+"wp-comments-post.php";
	}
<?php if ( !$user_ID ) : ?>
	if(!jQuery("#author").val()){
		comments_notice('이름을 입력해 주세요.');
		return false;
	}
	if(!jQuery("#email").val()){
		comments_notice('이메일을 입력해 주세요.');
		return false;
	}
	if(!jQuery("#comment").val()){
		comments_notice('내용을 입력해 주세요.');
		return false;
	}
	if(!jQuery("#private_ok").is(":checked")){
		comments_notice('개인정보 수집 및 이용에 동의가 필요합니다.');
		return false;
	}
	else{
		if(action_url)	jQuery("#commentform").attr("action", action_url);
		jQuery("#commentform").submit();
	}
<?php else : ?>
	if(!jQuery("#comment").val()){
		comments_notice('내용을 입력해 주세요.');
		return false;
	}
	else{
		jQuery('#comment_msg').css("display","none");
		if(action_url)	jQuery("#commentform").attr("action", action_url);
		jQuery("#commentform").submit();
	}
<?php endif; ?>
});

function comments_notice(tStr){
		jQuery('#comment_msg').css("display","block");
		jQuery('#comment_msg').html(tStr);
}
</script>
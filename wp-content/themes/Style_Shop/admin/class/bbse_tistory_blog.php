<?php
class BBSE_TISTORYBLOG
{
  public function __construct() {
    //글쓰기 메타바스 추가
    add_action( 'add_meta_boxes', array($this, 'bbse_tistory_add_meta_box') );
    //저장/수정
    add_action('publish_post', array($this, 'bbse_tistory_save_meta_box_data') );
  }

  public function bbse_tistory_add_meta_box() {
    global $theme_shortname;
    add_meta_box('bbse-tistory-meta', '티스토리', array($this, 'bbse_tistory_callback'), 'post', 'normal', 'high');
  }


  public function bbse_tistory_callback( $post, $box ) {
    global $theme_shortname;
    wp_nonce_field( 'bbse_tistory_meta_box', 'bbse_tistory_meta_box_nonce' );
    $visibilityA       = array('비공개','보호','공개','발행',);
    $tmp               = json_decode(get_option($theme_shortname."_tistory_category"));
    $categoryA         = $tmp->category;

    $tistoryAPIUse     = get_post_meta( $post->ID, 'tistoryAPIUse',     true );
    $tistoryVisibility = get_post_meta( $post->ID, 'tistoryVisibility', true );
    $tistoryCategory   = get_post_meta( $post->ID, 'tistoryCategory',   true );
    $tistoryPostSource = get_post_meta( $post->ID, 'tistoryPostSource', true );
    $tistoryPostId     = get_post_meta( $post->ID, 'tistoryPostId',     true );
    $tistoryPostUrl    = get_post_meta( $post->ID, 'tistoryPostUrl',    true );

    if ($tistoryPostUrl)
    {
      $tistoryAPIType  = 'modify';
      $str1            = '수정';
    }
    else
    {
      $tistoryAPIType  = 'write';
      $str1            = '등록';
    }
  ?>
  <input name="tistoryAPIType" type="hidden" value="<?php echo $tistoryAPIType?>">
  <table width="100%">
  <tbody>
  <tr>
    <td style="width:125px;">동시<?php echo $str1?></td>
    <td>
      <input name="tistoryAPIUse" type="checkbox" value="on" <?php echo $tistoryAPIUse=='on' || $tistoryPostUrl ?' checked="checked"':''?> >티스토리도 같이 <?php echo $str1?>합니다.
    </td>
  </tr>
  <tr>
    <td style="width:125px;">포스트 가시성</td>
    <td>
      <select name="tistoryVisibility">
        <?php foreach($visibilityA as $k=>$v){?>
        <option value="<?php echo $k?>"<?php echo $tistoryVisibility==$k?' selected="selected"':''?>><?php echo $v?></option>
        <?php }?>
      </select>
    </td>
  </tr>
  <tr>
    <td style="width:125px;">대상 카테고리</td>
    <td>
      <select name="tistoryCategory">
        <option value="0">분류없음</option>
        <?php
        foreach($categoryA as $k=>$v){ $blt='';
          if ($v->parent) $blt = '┗ ';
        ?>
        <option value="<?php echo $v->id?>"<?php echo $tistoryCategory==$v->id?' selected="selected"':''?>><?php echo $blt.$v->name?></option>
        <?php }?>
      </select>
    </td>
  </tr>
  <tr>
    <td style="width:125px;">출처표시</td>
    <td>
      <input name="tistoryPostSource" type="checkbox" value="on" <?php echo $tistoryPostSource=='on'?' checked="checked"':''?> >출처를 표시합니다.
    </td>
  </tr>
  <?php if ($tistoryPostUrl){?>
  <tr>
    <td style="width:125px;">TISTORY</td>
    <td><a href="<?php echo $tistoryPostUrl?>" target="_blank" title="바로보기"><?php echo $tistoryPostUrl?></a></td>
  </tr>
  <?php }?>
  </tbody>
  </table>
  <?php
  }

  public function bbse_tistory_save_meta_box_data( $post_id ) {
    global $theme_shortname;

    if ( !isset( $_POST['bbse_tistory_meta_box_nonce'] ) )                                     return;
    if ( !wp_verify_nonce( $_POST['bbse_tistory_meta_box_nonce'], 'bbse_tistory_meta_box' ) )  return;
    if ( !isset( $_POST['tistoryAPIUse'] ) )                                                   return;
    if ( !isset( $_POST['tistoryVisibility'] ) && !isset( $_POST['tistoryCategory'] ) )        return;

    // Sanitize user input.
    $myPost   = get_post($post_id);
    $postData = wpautop(iconv_substr(do_shortcode($myPost->post_content), 0, mb_strlen($myPost->post_content), 'utf-8'));

    if($_POST['origin_enable']){
      $postData .= '<br /> [출처 : <a href='.get_permalink().'> '.get_option("blogname").' 원문 보기 </a> ]';
    }

    $tagObj = wp_get_post_tags($post_id);
    if ($tagObj)
    {
      foreach($tagObj as $v) $tagArr[] = $v->name;
      $tag = implode(',',$tagArr);
    }

    $requestURL  = 'https://www.tistory.com/apis/post/'.$_POST['tistoryAPIType'];
    $headers = array();
    $body    = array(
      'targetUrl'    => get_option($theme_shortname."_tistory_url"),
      'title'        => $myPost->post_title,
      'visibility'   => sanitize_text_field( $_POST['tistoryVisibility']),
      'category'     => sanitize_text_field( $_POST['tistoryCategory']),
      'content'      => $postData,
      'tag'          => $tag,
      'output'       => 'json',
    );
    $cookies = array();

    if ($_POST['tistoryAPIType'] == 'modify')
      $body['postId'] = get_post_meta( $post_id, 'tistoryPostId', true );

    $body['access_token'] = get_option($theme_shortname."_tistory_accesstoken");

    $param   = array(
      'method'        => 'POST',
      'timeout'       => 30,
      'redirection'   => 5,
      'httpversion'   => '1.0',
      'blocking'      => true,
      'headers'       => $headers,
      'cookies'       => $cookies,
      'body'          => $body
    );
    $response         = wp_remote_post($requestURL, $param);
    $response_code    = wp_remote_retrieve_response_code( $response );
    $response_message = wp_remote_retrieve_response_message( $response );

    if ( 200 != $response_code && ! empty( $response_message ) ) {
      $ERROR = new WP_Error( $response_code, $response_message );
    } elseif ( 200 != $response_code ) {
      $ERROR = new WP_Error( $response_code, 'Unknown error occurred' );
    } else {
      date_default_timezone_set ( get_option('timezone_string') );
      $returnStr = wp_remote_retrieve_body($response);
      $decoded   = json_decode($returnStr);
      if ($decoded->tistory->status == 200)
      {
        $tistoryVisibility = sanitize_text_field( $_POST['tistoryVisibility'] );
        $tistoryCategory   = sanitize_text_field( $_POST['tistoryCategory'] );
        $tistoryPostSource = sanitize_text_field( $_POST['tistoryPostSource'] );
        $tistoryPostId     = sanitize_text_field( $decoded->tistory->postId );
        $tistoryPostUrl    = esc_url(sanitize_text_field( $decoded->tistory->url ));
        $tistoryUpdateDate = sanitize_text_field(strftime("%Y-%m-%d %H:%M:%S", current_time('timestamp')));

        // Update the meta field in the database.
        update_post_meta( $post_id, 'tistoryVisibility', $tistoryVisibility);
        update_post_meta( $post_id, 'tistoryCategory',   $tistoryCategory);
        update_post_meta( $post_id, 'tistoryPostSource', $tistoryPostSource);
        update_post_meta( $post_id, 'tistoryPostId',     $tistoryPostId);
        update_post_meta( $post_id, 'tistoryPostUrl',    $tistoryPostUrl);
        update_post_meta( $post_id, 'tistoryUpdateDate', $tistoryUpdateDate);
      }
    }
  }
} //BBSE_NAVERSYNDICATION

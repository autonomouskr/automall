<?php
class BBSE_NAVERSYNDICATION
{
  public function __construct() {
    global $theme_shortname;
    if (get_option($theme_shortname.'_naver_use_webmaster') == 'U' && get_option($theme_shortname.'_naver_syndiToken'))
    {
      add_filter('query_vars',        array($this, 'bbse_RegisterQueryVars'), 1, 1);
      add_filter('template_redirect', array($this, 'bbse_redirect'),          1, 0);

      //글쓰기 메타박스 추가
      add_action( 'add_meta_boxes', array($this, 'bbse_nsyndi_meta_box') );

      //저장/수정
      add_action('post_updated' ,   array($this, 'updateNotice'), 10,1);
    }
    //삭제
    add_action('trashed_post' ,   array($this, 'deleteNotice'), 10,1);
  }

  public function bbse_RegisterQueryVars($vars) {
    array_push($vars, 'nsyndi');
    return $vars;
  }

  public function bbse_redirect(){
    global $wp_query;
    if(!empty($wp_query->query_vars["nsyndi"])) {
      $wp_query->is_404 = false;
      $wp_query->is_feed = true;
      $this->makeXML($wp_query->query_vars["nsyndi"]);
    }
  }

  public function makeXML($var)
  {
    if (!$var) return false;
    $type    = explode('-',$var);
    $jobtype = $type[0];
    if ($jobtype)
    {
      $R = get_post($type[1], ARRAY_A);
      if (isset($R)) {
        date_default_timezone_set ( get_option('timezone_string') );
        $entry['id']             = get_permalink($type[1]);
        $entry['title']          = $R['post_title'];
        $entry['author']['name'] = get_the_author_meta('display_name',$R['post_author']);
        $entry['author']['url']  = home_url().'/?author='.$R['post_author'];
        $entry['updated']        = strftime("%Y-%m-%dT%H:%M:%S+09:00", strtotime($R['post_modified']));
        $entry['published']      = strftime("%Y-%m-%dT%H:%M:%S+09:00", strtotime($R['post_date_gmt']));
        $entry['link']           = $R['guid'];
        $entry['content']        = $R['post_content'];
        $entry['summary']        = str_replace(array("\r","\n"), array("",""), $R['post_excerpt']?$R['post_excerpt']:bbse_get_excerpt_board($R['post_content'],50));
        $tCats                   = get_the_category_list( ',', '', $R['ID'] );
        $entry['category']       = strip_tags($tCats);
        $catsArr                 = explode(',',$tCats);
        $output                  = preg_match_all('/<a href=\"(http[^\'"]+)\" rel="category">([^\'"]+)<\/a>/i', $tCats, $match);
        $catCount                = count($match[0]);
        $viaStr                  = array();
        $catStr                  = array();
        $entryStr                = '';
        if (isset($match))
        {
          foreach($match[1] as $k=>$v)
          {
            $cid     = explode('=',$v);
            $viaStr[$k] = '  <link rel="via" href="'.$v.'" title="'.$match[2][$k].'" />';
            $catStr[$k] = '  <category term="'.$this->get_cat_slug($cid[1]).'" label="'.$match[2][$k].'" />';
          }
        }
        $xmlWrap['open']  = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL.'<feed xmlns="http://webmastertool.naver.com">';
        $xmlWrap['close'] = '</feed>';

        if (empty($viaStr)) $viaStr[] = '  <link rel="via" href="'.$entry['id'].'" title="'.$entry['title'].'" />';

        $siteStr = '
        <id>'.home_url().'</id>
        <title>'.get_option('blogname').'</title>
        <author>
          <name>webmaster</name>
          <email>'.get_option( 'admin_email' ).'</email>
        </author>
        <updated>'.strftime("%Y-%m-%dT%H:%M:%S+09:00", strtotime(get_lastpostmodified())).'</updated>
        <link rel="site" href="'.home_url().'" title="'.get_option('blogname').'" />
        ';

        if ($jobtype == 'update'){
          $entryStr = '
          <entry>
            <id>'.$entry['id'].'</id>
            <title><![CDATA['.$entry['title'].']]></title>
            <author>
              <name>'.$entry['author']['name'].'</name>
              <url>'.$entry['author']['url'].'</url>
            </author>
            <updated>'.$entry['updated'].'</updated>
            <published>'.$entry['published'].'</published>
          '.implode(PHP_EOL, $viaStr).'
            <content type="html"><![CDATA['.$entry['content'].']]></content>
            <summary type="text">
              <![CDATA['.strip_tags($entry['summary']).']]>
            </summary>
          '.implode(PHP_EOL, $catStr).'
          </entry>
          ';
        }elseif ($jobtype == 'delete'){
          $entryStr = ' <deleted-entry ref="'.$entry['id'].'" when="'.strftime("%Y-%m-%dT%H:%M:%S+09:00", strtotime(get_lastpostmodified())).'" />';
        }

        $XMLDATA = $xmlWrap['open'].$siteStr.$entryStr.$xmlWrap['close'];
        header('Content-Type: text/xml; charset=utf-8', true);
        echo $XMLDATA;
        exit;
      }
      else
        return false;
    } elseif ($type[0] == 'delete') {

    }
  }

  public function get_cat_slug($cat_id) {
    $cat_id   = (int) $cat_id;
    $category = &get_category($cat_id);
    return $category->slug;
  }

  public function bbse_nsyndi_meta_box() {
    add_meta_box('bbse-nsyndi-post-meta', '네이버 신디케이션', array($this,'bbse_nsyndi_callback'), 'post', 'normal', 'high');
    add_meta_box('bbse-nsyndi-page-meta', '네이버 신디케이션', array($this,'bbse_nsyndi_callback'), 'page', 'normal', 'high');
  }

  public function bbse_nsyndi_callback( $post, $box ) {
    wp_nonce_field( 'bbse_nsyndi_meta_box', 'bbse_nsyndi_meta_box_nonce' );
    $nsyndiUse           = get_post_meta( $post->ID, 'nsyndiUse',           true );
    $nsyndiRequestType   = get_post_meta( $post->ID, 'nsyndiRequestType',   true );
    $nsyndiResultCode    = get_post_meta( $post->ID, 'nsyndiResultCode',    true );
    $nsyndiresultMessage = get_post_meta( $post->ID, 'nsyndiresultMessage', true );
    $nsyndiUpdateDate    = get_post_meta( $post->ID, 'nsyndiUpdateDate',    true );
  ?>
  <table width="100%">
  <tr>
    <td style="width:125px;">신디케이션 사용</td>
    <td>
      <input type="checkbox" name="nsyndi_use" value="y" id="usey" <?php echo $nsyndiUse=='y'?' checked="checked"':''?> /> <label for="usey">사용합니다.</label>&nbsp;&nbsp;&nbsp;
    </td>
  </tr>
  <tr>
    <td style="width:125px;">신디케이션 상태</td>
    <td>
      <?php if ($nsyndiUpdateDate){?>
      <?php echo $nsyndiRequestType?> : <?php echo $nsyndiUpdateDate?><br>
      [code : <?php echo $nsyndiResultCode?>] <?php echo $nsyndiresultMessage?>
      <?php }else{?>
      아직 핑을 보낸 기록이 없습니다.
      <?php }?>
    </td>
  </tr>
  </table>
  <?php
  }
  //send update ping
  public function updateNotice($post_id){
    $this->sendPing($post_id, 'UPDATE');
  }

  //send delete ping
  public function deleteNotice($post_id){
    $this->sendPing($post_id, 'DELETE');
  }

  protected function sendPing($post_id, $jobtype){
    global $theme_shortname;

    switch($jobtype)
    {
      case 'UPDATE':
      {
        if ( !isset( $_POST['nsyndi_use'] ) || $_POST['nsyndi_use'] != 'y' )  return;
        if ( !isset( $_POST['bbse_nsyndi_meta_box_nonce'] ) )  return;
        if ( !wp_verify_nonce( $_POST['bbse_nsyndi_meta_box_nonce'], 'bbse_nsyndi_meta_box' ) ) return;
        if ( !get_option($theme_shortname.'_naver_syndiToken')) return false;
      }
      break;

      case 'DELETE':
      {
        if ( !get_post_meta( $post_id, 'nsyndiResultCode', true ) ) return;
      }
      break;
    }

    $requestURL  = 'https://apis.naver.com/crawl/nsyndi/v2';
    $headers = array(
      'Host'          => 'apis.naver.com',
      'Pragma'        => 'no-cache',
      'Accept'        => '*/*',
      'Authorization' => 'Bearer '.get_option($theme_shortname.'_naver_syndiToken'),
      'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
      'User-agent'    => 'WordPress/'.get_bloginfo('version').'; '.home_url(),
    );

    $body    = array(
      'ping_url'      => esc_url( home_url().'/index.php/?nsyndi='.strtolower($jobtype).'-'.$post_id ),
    );

    $cookies = array();

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
      $resultCode    = $response_code;
      $resultMessage = '[HTTP] '.$response_message;
    } elseif ( 200 != $response_code ) {
      $resultCode    = $response_code;
      $resultMessage = '[HTTP] Unknown error occurred';
    } else {
      date_default_timezone_set ( get_option('timezone_string') );
      $bodyStr = wp_remote_retrieve_body($response);
      $XML     = simplexml_load_string($bodyStr, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
      if ($XML->message == 'OK' && $XML->receipt_number) $receipt_number = ' : '.$XML->receipt_number;
      $resultCode    = $response_code;
      $resultMessage = '[NAVER] {'.$XML->error_code.'} '.$XML->message.$receipt_number;
    }

    update_post_meta( $post_id, 'nsyndiUse',           sanitize_text_field('y'));
    update_post_meta( $post_id, 'nsyndiRequestType',   sanitize_text_field($jobtype));
    update_post_meta( $post_id, 'nsyndiResultCode',    sanitize_text_field($resultCode));
    update_post_meta( $post_id, 'nsyndiresultMessage', sanitize_text_field($resultMessage));
    update_post_meta( $post_id, 'nsyndiUpdateDate',    sanitize_text_field(strftime("%Y-%m-%d %H:%M:%S", current_time('timestamp'))));

  }
} //BBSE_NAVERSYNDICATION
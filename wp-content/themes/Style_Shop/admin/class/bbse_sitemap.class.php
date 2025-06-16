<?php
/*
[테마 수정 시 주의사항]
1. 워드프레스(Wordpress)의 업데이트 방식은 기존 테마/플러그인을 삭제 한 후 재설치 하는 방식입니다.
   업데이트 시 모든 수정 사항이 초기화 되므로 테마를 수정하시는 경우, 차일드테마(Child Theme) 방식을 이용해 주시기 바랍니다.
2. 차일드테마(Child Theme)를 이용한 수정 방법 : https://codex.wordpress.org/ko:Child_Themes
*/

class BBSE_SITEMAP
{
  private $DATA;
  private $SITEMAP;
  private $XMLDATA;
  private $FILECOUNT = 0;

  //초기화
  public function __construct() {
    global $theme_shortname;
    add_filter('rewrite_rules_array', array($this, 'AddRewriteRules'),        1, 1);
    add_filter('query_vars',          array($this, 'bbse_RegisterQueryVars'), 1, 1);
    add_filter('template_redirect',   array($this, 'bbse_redirect'),          1, 0);

    //저장/수정
    if ( get_option($theme_shortname.'_sitemap_usefile') == 'yes' ){
      add_action('post_updated' ,     array($this, 'tryWriteMapFile'));
    }
  }

  //주소 규칙
	public function AddRewriteRules($wpRules) {
		$newRules = array('sitemap\.xml$' => 'index.php?bbsesitemap=base');
		return array_merge($newRules, $wpRules);
	}

  //주소 쿼리 변수
  public function bbse_RegisterQueryVars($vars) {
    array_push($vars, 'bbsesitemap');
    return $vars;
  }

  //페이지 리다이렉트
  public function bbse_redirect(){
    global $wp_query;
    if(!empty($wp_query->query_vars["bbsesitemap"])) {
      $wp_query->is_404 = false;
      $wp_query->is_feed = true;
      $this->bbse_makeSitemap($wp_query->query_vars["bbsesitemap"]);
    }
  }

  //사이트맵 생성
  public function bbse_makeSitemap($str){
    if ($str) $mode = explode('-',$str);

    switch($mode[0])
    {
      case 'post':
        $this->getPosts('post', $mode[1]);
      break;

      case 'page':
        $this->getPosts('page', $mode[1]);
      break;

      case 'goods':
        $this->getPosts('goods', $mode[1]);
      break;

      case 'author':
        $this->getAuthor();
      break;

      case 'front':
        $this->getFront();
      break;

      default:
      case 'base':
        $this->getBase($mode[1]);
      break;

    }
  }

  //정보수집
  private function getBase($mode = '')
  {
    global $wpdb;
    $now = current_time('mysql', true);
    $postType = array('post','page','goods');

    foreach($postType as $ptype)
    {

	  if($ptype=='goods'){
		  if(plugin_active_check('BBSe_Commerce') == true){
			  $archives = $wpdb->get_results("
					SELECT DISTINCT
					  YEAR(from_unixtime(goods_reg_date)) AS `year`,
					  MONTH(from_unixtime(goods_reg_date)) AS `month`,
					  MAX(from_unixtime(goods_reg_date)) AS last_mod,
					  count(idx) AS posts
					FROM
					  bbse_commerce_goods
					WHERE
						  idx <> ''
					  AND  (goods_display='display' OR goods_display='soldout')
					GROUP BY
					  YEAR(from_unixtime(goods_reg_date)),
					  MONTH(from_unixtime(goods_reg_date))
					ORDER BY
					  goods_reg_date DESC
				");
		  }
	  }
	  else{
		  $archives = $wpdb->get_results("
			SELECT DISTINCT
			  YEAR(post_date_gmt) AS `year`,
			  MONTH(post_date_gmt) AS `month`,
			  MAX(post_modified_gmt) AS last_mod,
			  count(ID) AS posts
			FROM
			  {$wpdb->posts}
			WHERE
				  post_date_gmt < '$now'
			  AND post_status = 'publish'
			  AND post_type = '{$ptype}'
			GROUP BY
			  YEAR(post_date_gmt),
			  MONTH(post_date_gmt)
			ORDER BY
			  post_date_gmt DESC
		  ");
	  }

	  if($archives) {
		foreach($archives as $archive) {
		  $key = $archive->year.sprintf('%02d',$archive->month);
		  $this->DATA[$ptype][$key]['last_mod'] = $archive->last_mod.PHP_EOL;
		}
	  }
    }
    if ($mode == 'file')
      $this->buildSitemap('file');
    else
      $this->buildSitemap('base');
  }

  //포스트,페이지
  private function getPosts($ptype, $needle)
  {
    global $wpdb;
    $frequency = array('post'=>'weekly', 'page'=>'monthly', 'goods'=>'weekly');
	if(preg_match('/^([0-9]{4})([0-9]{2})$/', $needle, $matches)) {
	  $year  = $matches[1];
	  $month = $matches[2];

	  if($ptype=='goods'){
		  if(plugin_active_check('BBSe_Commerce') == true){

			  $query = "
				SELECT
				  idx,
				  goods_reg_date
				FROM
				  bbse_commerce_goods
				WHERE
				  idx<>''
				  AND (goods_display='display' OR goods_display='soldout')
				  AND YEAR(from_unixtime(goods_reg_date)) = %d
				  AND MONTH(from_unixtime(goods_reg_date)) = %d
				ORDER BY
				  goods_reg_date DESC
			  ";
			  $prepared = $wpdb->prepare($query, $year, $month);
			  $goods    = $wpdb->get_results($prepared);
			  $this->DATA = array();
			  foreach($goods as $k=>$v)
			  {
				$this->DATA[$v->idx]['url'] = home_url()."/?bbseGoods=".$v->idx;
				$this->DATA[$v->idx]['date'] = $this->strToGmtDateTime(date("Y-m-d H:i:s",$v->goods_reg_date));
				$this->DATA[$v->idx]['priority'] = $this->priorityCalc($ptype, $goods, 0);
				$this->DATA[$v->idx]['frequency'] = $frequency[$ptype];
			  }
		  }
	  }
	  else{
		  $query = "
			SELECT
			  ID,
			  post_author,
			  post_modified_gmt,
			  comment_count
			FROM
			  {$wpdb->posts}
			WHERE
			  post_password = ''
			  AND post_type = '{$ptype}'
			  AND post_status = 'publish'
			  AND YEAR(post_date_gmt) = %d
			  AND MONTH(post_date_gmt) = %d
			ORDER BY
			  post_date_gmt DESC
		  ";
		  $prepared = $wpdb->prepare($query, $year, $month);
		  $posts    = $wpdb->get_results($prepared);
		  $this->DATA = array();
		  foreach($posts as $k=>$v)
		  {
			$this->DATA[$v->ID]['url']       = get_permalink($v->ID);
			$this->DATA[$v->ID]['date']      = $this->strToGmtDateTime($v->post_modified_gmt);
			$this->DATA[$v->ID]['priority']  = $this->priorityCalc($ptype, $posts, $v->comment_count);
			$this->DATA[$v->ID]['frequency'] = $frequency[$ptype];

		  }
	  }

	  $this->buildSitemap($ptype);
    }
  }

  //작성자
  private function getAuthor($needle)
  {
  }

  //프론트
  private function getFront()
  {
    $this->buildSitemap('front');
  }

  //사이트맵 코드 빌딩
  private function buildSitemap($makeType)
  {
    $lastDateTime = get_lastpostmodified('gmt');
    $homeUrl      = home_url();

    switch ($makeType)
    {
      case 'post':
      case 'page':
      case 'goods':
      {
        $this->SITEMAP = array();
        foreach($this->DATA as $k=>$v){
          $this->SITEMAP[]  =
            '  <url>'.PHP_EOL
           .'    <loc>'.$v['url'].'</loc>'.PHP_EOL
           .'    <lastmod>'.$v['date'].'</lastmod>'.PHP_EOL
           .'    <changefreq>'.$v['frequency'].'</changefreq>'.PHP_EOL
           .'    <priority>'.$v['priority'].'</priority>'.PHP_EOL
           .'  </url>';
        }
      }
      break;

      case 'front':
      {
        $this->SITEMAP[]  =
          '  <url>'.PHP_EOL
		     .'    <loc>'.$homeUrl.'</loc>'.PHP_EOL
		     .'    <lastmod>'.$this->strToGmtDateTime($lastDateTime).'</lastmod>'.PHP_EOL
		     .'    <changefreq>daily</changefreq>'.PHP_EOL
		     .'    <priority>1.0</priority>'.PHP_EOL
	       .'  </url>';
      }
      break;


      //default:
      case 'file':
      case 'base':
      {
        if ( $this->FILECOUNT < 1)
        {
          $sitemapURL  = $homeUrl.'/index.php?bbsesitemap=front';
          $this->SITEMAP[]  =
           '  <sitemap>'.PHP_EOL
          .'    <loc>'.$sitemapURL.'</loc>'.PHP_EOL
          .'    <lastmod>'.$this->strToGmtDateTime($lastDateTime).'</lastmod>'.PHP_EOL
          .'  </sitemap>';
          foreach($this->DATA as $type=>$data){
            foreach($data as $k=>$v)
            {
              $sitemapURL  = $homeUrl.'/index.php?bbsesitemap='.$type.'-'.$k;
              $this->SITEMAP[]  =
               '  <sitemap>'.PHP_EOL
              .'    <loc>'.$sitemapURL.'</loc>'.PHP_EOL
              .'    <lastmod>'.$this->strToGmtDateTime($v['last_mod']).'</lastmod>'.PHP_EOL
              .'  </sitemap>';

            }
          }
          $this->FILECOUNT++;
        }
      }
      break;
    }
    $this->finalResult($makeType);
  }

  //표준시 리턴
	private function strToGmtDateTime($dateTime) {
    if (get_option('gmt_offset') == 0)
      return strftime("%Y-%m-%dT%H:%M:%S+00:00", strtotime($dateTime));
    else
      return gmstrftime("%Y-%m-%dT%H:%M:%S+00:00", strtotime($dateTime));
   }

  //중요도 가중치 산정
  private function priorityCalc($type, $dataSet, $target) {
    $minMax = array('min'=>0.2, 'max'=>1);
    switch ($type)
    {
      case 'post':
      {
        $totalComment = 0;
        foreach ($dataSet as $k=> $v) $totalComment += $v->comment_count;
        if ($totalComment <=0 || $target<=0) $myRatio = $minMax['min'];
        else $myRatio = round(($target * 100 / $totalComment) / 100, 1);
      }
      break;
      case 'page':
      {
        $myRatio = 0.6;
      }
      case 'goods':
      {
        $myRatio = 0.8;
      }
      break;
    }

    return $myRatio <= 0 ? $minMax['min'] : $myRatio;
  }

  //홈경로
  private function getHomePath() {
    $home_path = '';
    if (ABSPATH) {
      $home_path = ABSPATH;
    } else{
      $home_path = str_replace( $_SERVER["PHP_SELF"], '', $_SERVER["SCRIPT_FILENAME"] );
    }
    return $home_path;
  }

  //파일작성시도
  public function tryWriteMapFile()
  {
    $this->bbse_makeSitemap('base-file');
  }

  //결과 표시, 파일로 만들거나 바로보여주거나
  private function finalResult($makeType){
$this->makeXML($makeType);
if ($makeType == 'file')
{
if (is_writable($this->getHomePath())){
$sitemapFP = @fopen($this->getHomePath().'sitemap.xml', 'w');
@fwrite($sitemapFP, $this->XMLDATA, mb_strlen($this->XMLDATA));
@fclose($sitemapFP);
}
}else{
ob_start();
ob_end_clean();
header('Content-Type: text/xml; charset=utf-8');
echo $this->XMLDATA;
exit;
}
  }

  //XML 문서 작성
  private function makeXML($makeType){
$this->XMLDATA = <<<XML
<?xml version="1.0" encoding="UTF-8"?>

XML;

    if ($makeType == 'base' || $makeType == 'file'){
$this->XMLDATA .= <<<XML
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

XML;

$this->XMLDATA .= implode(PHP_EOL, $this->SITEMAP).PHP_EOL;

$this->XMLDATA .= <<<XML
</sitemapindex>

XML;
    } else {

$this->XMLDATA .= <<<XML
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

XML;

$this->XMLDATA .= implode(PHP_EOL, $this->SITEMAP).PHP_EOL;

$this->XMLDATA .= <<<XML
</urlset>

XML;
    }
  }
}
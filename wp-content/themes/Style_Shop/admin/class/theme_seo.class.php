<?php
class Theme_MySEOClass{
	public $seo_type;
	public $seo_sitename;
	public $seo_title;
	public $seo_image;
	public $seo_currency;
	public $seo_goods_price;
	public $seo_goods_status;
	public $seo_author;
	public $seo_url;
	public $seo_description;
	public $seo_keywords;
	public $seo_published_time;
	public $seo_modified_time;
	public $seo_sections;
	public $seo_tags;
	public $seo_bbseGoods;
	public $seo_product;

	public function __construct(){
		add_filter( 'language_attributes', array( $this, 'add_og_ns' ) );
		add_action('wp', array($this,'pre_detect_shortcode'));
	}

	public function pre_detect_shortcode(){
		global $theme_shortname;
		$this->seo_type        = 'product';
		$this->seo_sitename    = get_bloginfo('name');
		$this->seo_keywords    = get_option($theme_shortname."_seo_keywords")    ? get_option($theme_shortname."_seo_keywords")    : '';
		$this->seo_description = get_option($theme_shortname."_seo_description") ? get_option($theme_shortname."_seo_description") : get_bloginfo('description');
		$this->seo_bbseGoods   = get_query_var('bbseGoods');

		if($this->seo_bbseGoods > '0'){
			global $wp;
			global $wpdb;

			$deliveryConfDataResource = $wpdb->get_row("SELECT * FROM bbse_commerce_config WHERE config_type='delivery'");
			$deliveryConfData         = unserialize($deliveryConfDataResource->config_data);
			$cagoriesResource         = $wpdb->get_results("SELECT idx, c_name FROM bbse_commerce_category");

			$goodsCnt                 = $wpdb->get_var("SELECT count(*) FROM bbse_commerce_goods WHERE idx='".$wp->query_vars['bbseGoods']."' AND (goods_display='display' OR goods_display='soldout')");

			if($goodsCnt > '0'){
				$goods   = $wpdb->get_row("SELECT * FROM bbse_commerce_goods WHERE idx='".$wp->query_vars['bbseGoods']."'");
				$expCate = explode("|", $goods->goods_cat_list);
				$currentCategoiesArray = array();
				foreach($expCate as $categoryId){
					foreach($cagoriesResource as $key=>$c_info){
						if ( $categoryId == $c_info->idx){
							$currentCategoiesArray[] = $c_info->c_name;
						}
					}
				}
				if($goods->goods_basic_img) {
					$basicBigImg = wp_get_attachment_image_src($goods->goods_basic_img, 'goodsimage8');
				} else {
					if(sizeof($imageList)>'0') $basicBigImg      = wp_get_attachment_image_src($imageList['0'], 'goodsimage8');
					else                       $basicBigImg['0'] = BBSE_COMMERCE_PLUGIN_WEB_URL.'images/image_not_exist.jpg';
				}

				$this->seo_title = ($goods->goods_seo_use=='on' && $goods->goods_seo_title) ? esc_html(strip_tags(stripslashes($goods->goods_seo_title))) : esc_html(strip_tags(stripslashes($goods->goods_name)));
				$this->seo_keywords = ($goods->goods_seo_use=='on' && $goods->goods_seo_keyword) ? esc_html(strip_tags(stripslashes($goods->goods_seo_keyword))) : esc_html(strip_tags(stripslashes($goods->goods_name)));
				$this->seo_description = ($goods->goods_seo_use=='on' && $goods->goods_seo_description) ? esc_html(strip_tags(stripslashes($goods->goods_seo_description))) : esc_html(strip_tags(stripslashes($goods->goods_description)));
				$this->seo_image = $basicBigImg['0'];
				$this->seo_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				$this->seo_currency='KRW';
				$this->seo_goods_price=$goods->goods_price;
				$this->seo_goods_status=($goods->goods_display=='soldout')?'out of stock':'instock';

				$this->seo_product['product:condition']               = ''; //'new', 'refurbished', 'used'
				$this->seo_product['product:product_link']            = $this->seo_url;
				$this->seo_product['product:availability']            = ($goods->goods_count_flag == 'unlimit') ? 'instock' : $goods->goods_count;
				$this->seo_product['product:brand']                   = ($goods->goods_company_display == 'view') ? $goods->goods_company : '';
				$this->seo_product['product:category']                = (!empty($currentCategoiesArray)) ? implode(',', $currentCategoiesArray) : '';
				$this->seo_product['product:ean']                     = ($goods->goods_barcode_display == 'view') ? $goods->goods_barcode : '';
				$this->seo_product['product:mfr_part_no']             = $goods->goods_code;
				$this->seo_product['product:original_price:amount']   = $goods->goods_consumer_price; //정상가
				$this->seo_product['product:original_price:currency'] = 'KRW';
				$this->seo_product['product:price:amount']            = $goods->goods_price; //판매가
				$this->seo_product['product:price:currency']          = 'KRW';

				if ($goods->goods_consumer_price > $goods->goods_price){
					$this->seo_product['product:sale_price:amount']       = $goods->goods_price; //할인가
					$this->seo_product['product:sale_price:currency']     = 'KRW';
				}
				$this->seo_product['product:shipping_cost:amount']    = ($deliveryConfData['delivery_charge_type'] != 'free') ? $deliveryConfData['delivery_charge'] : 0;//배송비
				$this->seo_product['product:shipping_cost:currency']  = 'KRW';
			}
		} else {
			if ( is_singular() ){
				global $post;

				$this->seo_tags = array();
				$this->seo_type = 'article';

				if (have_posts())
				{
				  while(have_posts())
				  {
					the_post();

					if ( isset($post) && !empty($post)){
						$postTags = wp_get_post_tags($post->ID);
						if ($postTags) {
							foreach($postTags as $tag) {
								$this->seo_tags[] = $tag->name;
							}
						}
						$thisDescription          = str_replace(array("\n\r", "\n", "\r"), "", $this->bbse_get_excerpt_board( $post->post_content, 250 ));
						$this->seo_title          = get_the_title();
						$this->seo_author         = get_user_by('ID', $post->post_author)->data->user_nicename; //get_the_author();
						$this->seo_description    = preg_replace('/(\[.+\])/', '', $thisDescription);
						$this->seo_image          = $this->bbse_post_first_image();
						$this->seo_url            = strip_tags(get_permalink($post->ID));

						//echo "시간 : ".$post->post_date_gmt;
						//exit;
						$this->seo_published_time = $this->strToGmtDateTime($post->post_date_gmt);
						$this->seo_modified_time  = $this->strToGmtDateTime($post->post_modified_gmt);

					}
				  }
			    }
			} else {
				if ( is_home() && is_front_page() ){
					$this->seo_type = 'website';
					$this->seo_url  = home_url();
				} else {
					global $wp;
					$this->seo_url  = home_url(add_query_arg(array(),$wp->request));

					if (is_category())   { $this->seo_sections = array( get_cat_name( get_query_var('cat') ) ); }
					elseif (is_tag())    { $this->seo_sections = array( single_tag_title('', false) ); }
					elseif (is_day())    { $this->seo_sections = array( get_the_time("m. j, Y") ); }
					elseif (is_month())  { $this->seo_sections = array( get_the_time("m, Y") ); }
					elseif (is_year())   { $this->seo_sections = array( get_the_time("Y") ); }
					elseif (is_author()) { $this->seo_sections = array( get_the_author_meta('display_name') ); }
					elseif (is_search() && get_search_query()) { $this->seo_sections = array( get_search_query() ); }
					else                 { $this->seo_sections = array(); }
				}
			}
		}
		add_action('wp_head', array($this,'add_meta_tags'));
	}

	public function bbse_get_excerpt_board( $string, $length ) {
		if ( defined('ENT_IGNORE') ) $content = html_entity_decode(strip_tags($string), ENT_QUOTES | ENT_IGNORE, "UTF-8");
		else                         $content = html_entity_decode(strip_tags($string), ENT_QUOTES, "UTF-8");

		if ( mb_strlen($content) > $length )
			return stripslashes(esc_html(mb_substr($content,0,$length).'...'));
		else
			return stripslashes(esc_html($content));
	}

	public function strToGmtDateTime($dateTime) {
    return gmstrftime("%Y-%m-%dT%H:%M:%S+00:00", strtotime($dateTime));
  }


	public function bbse_post_first_image($size = '', $icon = false, $attr = ''){
		global $postData;
		if ($postData) $post = $postData;
		else global $post;

		$thumbNailImageHtml = '';
		$thumbNailImageSrc  = '';
		ob_start();;
		ob_end_clean();
		if (get_the_post_thumbnail($post->ID)) {
		  $thumbNailImageHtml = get_the_post_thumbnail($post->ID, $size, $attr);
		} else {
		  $output = preg_match_all('/wp-image-([0-9]+)*/i', $post->post_content, $uploaded_image);
		  if (isset($uploaded_image[1][0]))
			$thumbNailImageHtml =  wp_get_attachment_image($uploaded_image[1][0], $size, $icon, $attr);

		  if (empty($uploaded_image[1][0]) || !$thumbNailImageHtml){
			$output  = preg_match_all('/^\[gallery ids\=\"+(([0-9]+),?)*\"/i', $post->post_content, $gallery_image_temp);

			if (isset($gallery_image_temp[0][0])){
				$output = preg_match_all('/([0-9]+),?/i', $gallery_image_temp[0][0], $gallery_image);
				$thumbNailImageHtml = wp_get_attachment_image($gallery_image[1][0] , $size, $icon, $attr);
			}

			if (empty($gallery_image_temp[0][0]) || !$thumbNailImageHtml){
				$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $linked_image);
				if (isset($linked_image[1][0])){
					$style = '';
					if (isset($size) && is_array($size)) $style = 'style="width:'.$size[0].'px;height:'.$size[1].'px;"';
					$thumbNailImageHtml = '<img src="'.$linked_image[1][0].'" alt="'.htmlspecialchars($post->post_title).'" '.$style.' />';
				}
			}
		  }
		}

		if ($size == ''){
		  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $thumbNailImageHtml, $willReturn);
		  return isset($willReturn[1][0])?$willReturn[1][0]:false;
		} else {
		  return $thumbNailImageHtml;
		}
	}

	public function add_og_ns( $lang ){
		if ( is_home() && is_front_page() )  {
			$nameSpace = '#';
			if ($this->seo_bbseGoods > '0') {
				$nameSpace = '# product: http://ogp.me/ns/product#';
			}
		} else {
			$nameSpace = '# article: http://ogp.me/ns/article#';
		}

		return $lang.' prefix="og: http://ogp.me/ns'.$nameSpace.'"';
	}
	public function add_meta_tags() {
		global $theme_shortname;
		$output  = PHP_EOL;

		if(!$this->seo_url) $this->seo_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		if ($this->seo_url && !is_singular() )  $output .= '<link rel="canonical" href="'.$this->seo_url.'" />'.PHP_EOL;

		/* standard data */
		if (is_singular()){
			$output .= '<meta name="author" content="'.$this->seo_author.'" />'.PHP_EOL;
		}
		$output .= '<meta name="keywords" content="'.$this->seo_keywords.'" />'.PHP_EOL;
		$output .= '<meta name="description" content="'.esc_attr($this->seo_description).'" />'.PHP_EOL;

		/* Twitter Card data */
		if($this->seo_bbseGoods > '0') $output .= '<meta name="twitter:card" content="product" />'.PHP_EOL;
		else $output .= '<meta name="twitter:card" content="summary" />'.PHP_EOL;

		$output .= '<meta name="twitter:domain" content="'.home_url().'" />'.PHP_EOL;
		$output .= '<meta name="twitter:site" content="@'.$this->seo_sitename.'" />'.PHP_EOL;
		if (is_singular() || $this->seo_bbseGoods > '0'){
			$output .= '<meta name="twitter:title" content="'.esc_attr($this->seo_title).'" />'.PHP_EOL;
		}
		$output .= '<meta name="twitter:description" content="'.esc_attr($this->seo_description).'" />'.PHP_EOL;
		if (is_singular()){
			$output .= '<meta name="twitter:creator" content="@'.$this->seo_author.'">'.PHP_EOL;
			if( $this->seo_image) $output .= '<meta name="twitter:image" content="'.esc_url($this->seo_image).'" />'.PHP_EOL;
		}
		if($this->seo_image && $this->seo_bbseGoods > '0'){
			$output .= '<meta name="twitter:image" content="'.esc_url($this->seo_image).'" />'.PHP_EOL;
		}

		if($this->seo_bbseGoods > '0'){
			$output .= '<meta name="twitter:label1" content="price">'.PHP_EOL;
			$output .= '<meta name="twitter:data1" content="'.$this->seo_goods_price.'">'.PHP_EOL;
			$output .= '<meta name="twitter:label2" content="currency">'.PHP_EOL;
			$output .= '<meta name="twitter:data2" content="'.$this->seo_currency.'">'.PHP_EOL;
			$output .= '<meta name="twitter:label3" content="availability">'.PHP_EOL;
			$output .= '<meta name="twitter:data3" content="'.$this->seo_goods_status.'">'.PHP_EOL;
		}

    /*Open Graph data*/
		if (!is_singular() && (!$this->seo_bbseGoods || $this->seo_bbseGoods)){
			if($this->seo_title) $output .= '<meta property="og:title" content="'.esc_attr($this->seo_title).'" />'.PHP_EOL;
			else{
				if(get_option($theme_shortname."_seo_title")) $output .= '<meta property="og:title" content="'.esc_attr(get_option($theme_shortname."_seo_title")).'" />'.PHP_EOL;
				else $output .= '<meta property="og:title" content="'.esc_attr(get_bloginfo('name')).'" />'.PHP_EOL;
			}
		}

		$output .= '<meta property="og:locale" content="'. str_replace("-", "_", get_bloginfo('language')).'" />'.PHP_EOL;
		$output .= '<meta property="og:site_name" content="'.$this->seo_sitename.'" />'.PHP_EOL;
		$output .= '<meta property="og:type" content="'.$this->seo_type.'" />'.PHP_EOL;
		$output .= '<meta property="og:url" content="'.$this->seo_url.'" />'.PHP_EOL;
		$output .= '<meta property="og:description" content="'.esc_attr($this->seo_description).'" />'.PHP_EOL;

		if ( is_singular() || $this->seo_bbseGoods > '0'){
			if( $this->seo_title ) $output .= '<meta property="og:title" content="'.esc_attr($this->seo_title).'" />'.PHP_EOL;
			if( $this->seo_image ) $output .= '<meta property="og:image" content="'.esc_url($this->seo_image).'" />'.PHP_EOL;
			if($this->seo_goods_price>'0') $output .= '<meta property="og:price:amount" content="'.$this->seo_goods_price.'" />'.PHP_EOL;
			if($this->seo_currency) $output .= '<meta property="og:price:currency" content="'.$this->seo_currency.'" />'.PHP_EOL;
			if($this->seo_goods_status) $output .= '<meta property="og:availability" content="'.$this->seo_goods_status.'" />'.PHP_EOL;
		}
		if ( is_singular() ){
			if( $this->seo_published_time ) $output .= '<meta property="article:published_time" content="'.$this->seo_published_time.'" />'.PHP_EOL;
			if( $this->seo_modified_time )  $output .= '<meta property="article:modified_time" content="'.$this->seo_modified_time.'" />'.PHP_EOL
			                                          .'<meta property="og:updated_time" content="'.$this->seo_modified_time.'" />'.PHP_EOL;

			if ($this->seo_sections) {
				foreach($this->seo_sections as $section) {
					$output .= '<meta property="'.$this->seo_type.':section" content="'.$section.'" />'.PHP_EOL;
				}
			}
			if ($this->seo_tags){
				foreach($this->seo_tags as $tag){
					$output .= '<meta property="article:tag" content="'.$tag.'" />'.PHP_EOL;
				}
			}
		} else {
			if ($this->seo_sections) {
				foreach($this->seo_sections as $section) {
					$output .= '<meta property="'.$this->seo_type.':section" content="'.$section.'" />'.PHP_EOL;
				}
			}
		}

		if( $this->seo_image && $this->seo_bbseGoods > '0' && !empty($this->seo_product)){
			foreach($this->seo_product as $key=>$value){
				if ($value){
					$output .= '<meta property="'.$key.'" content="'.$value.'" />'.PHP_EOL;
				}
			}

		}
		echo PHP_EOL.'<!-- SITE SEO START-->'.$output.'<!-- SITE SEO END -->'.PHP_EOL.PHP_EOL;
	}
}
/*END OF CLASS*/
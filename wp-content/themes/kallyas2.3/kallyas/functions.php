<?php

/*--------------------------------------------------------------------------------------------------

	File: functions.php

	Description: This is the main functions file for this theme
	Plesae be carefull when editing this file

--------------------------------------------------------------------------------------------------*/

	
	define( 'THEMENAME', 'Theme' ); 
	define( 'OPTIONS', 'zn_kallyas_options' ); 
 	define( 'THEME_VERSION', '2.3' ); 
 	define( 'HOME_URL',  get_home_url('/'));
	$get_stylesheet_directory = get_stylesheet_directory();
	$get_stylesheet_directory_uri = get_stylesheet_directory_uri();
	$get_template_directory_uri = get_template_directory_uri();

	if( ! defined('ADMIN_PATH' ) ) 	 { 	define( 'ADMIN_PATH', $get_stylesheet_directory.'/admin/' ); }
	if( ! defined('ADMIN_DIR' ) ) 	 { 	define( 'ADMIN_DIR', $get_stylesheet_directory_uri . '/admin/'); }
	if( ! defined('THEME_DIR' ) ) 	 { 	define( 'THEME_DIR', $get_stylesheet_directory_uri ); }
	if( ! defined('MASTER_THEME_DIR' ) ) 	 { 	define( 'MASTER_THEME_DIR', $get_template_directory_uri ); }
	if( ! defined('IMAGES_URL' ) ) 	 { 	define( 'IMAGES_URL', $get_template_directory_uri .'/images' ); }
	if( ! defined('ADMIN_IMAGES_DIR' ) ) 	 { 	define( 'ADMIN_IMAGES_DIR', $get_template_directory_uri . '/admin/images'); }
	if( ! defined('ADMIN_MASTER_ADMIN_DIR' ) ) 	 { 	define( 'ADMIN_MASTER_ADMIN_DIR', $get_template_directory_uri . '/admin/'); }

/*--------------------------------------------------------------------------------------------------
	Load all options inside $data to be used in the theme
--------------------------------------------------------------------------------------------------*/
	
	global $data;
	$data = get_option(OPTIONS);

/*--------------------------------------------------------------------------------------------------
	Load all files needed for this theme
--------------------------------------------------------------------------------------------------*/

	
	if (!is_admin()) {
		locate_template(array('admin/framework/function-vt-resize.php'), true, false);
		locate_template(array('template-helpers/content-page.php'), true, false);
		locate_template(array('template-helpers/shortcodes.php'), true, false);
		locate_template(array('woocommerce/zn-woocommerce-init.php'), true, false);
	}
	else{
		include_once(locate_template(array('admin/zn-init.php'), false));
	}

/*--------------------------------------------------------------------------------------------------
	Load theme's widgets
--------------------------------------------------------------------------------------------------*/

		locate_template(array('widgets/widget-blog-categories.php'), true, false);
		locate_template(array('widgets/widget-archive.php'), true, false);
		locate_template(array('widgets/widget-menu.php'), true, false);
		locate_template(array('widgets/widget-twitter.php'), true, false);
		locate_template(array('widgets/widget-contact-details.php'), true, false);
		locate_template(array('widgets/widget-mailchimp.php'), true, false);
		locate_template(array('widgets/widget-tag-cloud.php'), true, false);
		locate_template(array('widgets/widget-latest_posts.php'), true, false);
		locate_template(array('widgets/widget-social_buttons.php'), true, false);
		locate_template(array('widgets/widget-flickr.php'), true, false);


/*--------------------------------------------------------------------------------------------------
	ADD WOOCOMMERCE SUPPORT
--------------------------------------------------------------------------------------------------*/
	add_theme_support( 'woocommerce' );

/*--------------------------------------------------------------------------------------------------
	Add theme support for featured image
--------------------------------------------------------------------------------------------------*/

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );

	/*--------------------------------------------------------------------------------------------------
		Shortcodes fixer
	--------------------------------------------------------------------------------------------------*/
	if ( ! function_exists( 'shortcode_empty_paragraph_fix' ) ) {
		function shortcode_empty_paragraph_fix($content){   
			$array = array (
				'<p>[' => '[', 
				']</p>' => ']', 
				']<br />' => ']'
			);

			$content = strtr($content, $array);
			return $content;
		}
	}

	add_filter('the_content', 'shortcode_empty_paragraph_fix');
	
/*--------------------------------------------------------------------------------------------------
	GET THE MEDIA ID FROM URL
--------------------------------------------------------------------------------------------------*/
	function pn_get_attachment_id_from_url( $attachment_url = '' ) {
 
	global $wpdb;
	$attachment_id = false;
 
	// If there is no url, return.
	if ( '' == $attachment_url )
		return;
 
	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();
 
	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
 
		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
 
		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
 
		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
 
	}
 
	return $attachment_id;
}
/*--------------------------------------------------------------------------------------------------
	Check if we are on the taxonomy archive page. We will display all items if it is selected
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_portfolio_taxonomy_pagination' ) ) {
	function zn_portfolio_taxonomy_pagination( $query ) {

		global $data;
		if( !empty ( $data['portfolio_per_page'] ) ) {

			if ( (  is_tax('project_category') && $query->is_main_query() ) ||  ( is_post_type_archive('portfolio') && $data['portfolio_style'] == 'portfolio_sortable' ) ) {
				set_query_var( 'posts_per_page', $data['portfolio_per_page'] );
			}

		}
		if( !empty ( $data['portfolio_per_page_show'] ) ) {

			if (  ( is_tax('project_category') && $query->is_main_query() ) ||  ( is_post_type_archive('portfolio') && $data['portfolio_style'] != 'portfolio_sortable' ) ) { 
				set_query_var( 'showposts', $data['portfolio_per_page_show'] );
			}

		}

	}
}

add_action( 'pre_get_posts', 'zn_portfolio_taxonomy_pagination' );

/*--------------------------------------------------------------------------------------------------
	Show the page builder elements based on area
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_get_template_from_area' ) ) {
	function zn_get_template_from_area ($area,$post_id,$meta_fields)
	{

		if ( isset ( $meta_fields[$area] ) )
		{
		
			global $post;
			$GLOBALS['meta_fields'] = $meta_fields;
			
			if ( $area == 'content_main_area' || $area == 'content_grey_area' || $area == 'content_bottom_area') {
				$sizes = array (
					"four"=>"0.25", 
					"one-third"=>"0.33", 
					"eight"=>"0.5", 
					"two-thirds"=>"0.66", 
					"twelve"=>"0.75", 
					"sixteen"=>"1", 
				);
			
				$size = 0;
				$i = 0;
			
				$all_elements = count ($meta_fields[$area]);
				$no_margin = '';
				 
				foreach ( $meta_fields[$area] as $options ) 
				{
				
					if ($all_elements == 1 && $options['dynamic_element_type'] == '_shop_features'){
						$no_margin = 'shop-features';
					}
					
					if ( $size == '0' ) {
						
					//	echo $options['_sizer'];
						//echo 'row_opened';
						echo '<div class="row '.$no_margin.'">';
						
					}
					
					$options['dynamic_element_type']($options);
					
					if ( isset ( $options['_sizer'] ) ) {
						$size +=  $sizes[$options['_sizer']];
					}
					
					$i++;
					
					if ($size == '1' || $size == '0.99' || $size == '0.91' || $size == '0.88' || $all_elements == $i ) {
						
						echo '</div>';
					//	echo $size;
					//	echo 'row_closed';
						$size = 0;
					}

				}
			
			}
			else {
				foreach ($meta_fields[$area] as $options ) 
				{
					$options['dynamic_element_type']($options);
				}
			}
			
		}
	}
}
	
/*--------------------------------------------------------------------------------------------------
	Load/set the theme translation files
	Translations can be filed in the /languages/ directory
--------------------------------------------------------------------------------------------------*/

	load_theme_textdomain( THEMENAME, get_template_directory() . '/languages' );
  
/*--------------------------------------------------------------------------------------------------
	Get the page number
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'get_page_number' ) ) {
	function get_page_number() {
		if ( get_query_var('paged') ) {
			print ' | ' . __( 'Page ' , THEMENAME) . get_query_var('paged');
		}
	} 
}

/*--------------------------------------------------------------------------------------------------
	Wrap post images in HoverBorder Class
--------------------------------------------------------------------------------------------------*/
add_filter('the_content', 'zn_wrap_images');
if ( ! function_exists( 'zn_wrap_images' ) ) {
	function zn_wrap_images ($content)
	{	global $post;
		$pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)><img(.*?)class=('|\")(.*?)(.*?)(alignleft|alignright|aligncenter|alignnone)(.*?)('|\")(.*?)>/i";
		$replacement = '<a$1href=$2$3.$4$5 class="hoverBorder $11"$6><img class="$10$12"$14>';
		$content = preg_replace($pattern, $replacement, $content);
		//$content = str_replace("%LIGHTID%", $post->ID, $content);
		return $content;
	}
}

/*--------------------------------------------------------------------------------------------------
	Calculate proper size
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_get_size' ) ) {
	function zn_get_size( $size ) {
		global $data;
		$span_sizes = array (
			"four"=>"span3", 
			"one-third"=>"span4", 
			"span5"=>"span5", 
			"eight"=>"span6", 
			"two-thirds"=>"span8", 
			"twelve"=>"span9", 
			"sixteen"=>"span12", 
			"portfolio_sortable" => 'portfolio_sortable'
		);
		

		// 1170 LAYOUT
		if ($data['zn_width'] == '1170') {

			$image_width = array (
				"four"=>"270", 
				"one-third"=>"370", 
				"span5"=>"470", 
				"eight"=>"570", 
				"two-thirds"=>"770", 
				"twelve"=>"870", 
				"sixteen"=>"1170", 
				"span2"=>"170", 
				"span3"=>"270", 
				"span4"=>"370", 
				"span5"=>"470", 
				"span6"=>"570", 
				"span7"=>"670", 
				"span8"=>"770", 
				"span9"=>"870", 
				"span10"=>"970", 
				"span11"=>"1070", 
				"span12"=>"1170",
				"portfolio_sortable" => '380'
			);


		}
		// 960 LAYOUT
		elseif ( $data['zn_width'] == '960' ) {

			$image_width = array (
				"four"=>"220", // DONE
				"one-third"=>"370", 
				"eight"=>"460", // DONE
				"two-thirds"=>"770", 
				"twelve"=>"870", 
				"sixteen"=>"960", // DONE
				"span3"=>"220", // DONE 
				"span4"=>"300", // DONE 
				"span5"=>"460", 
				"span6"=>"460", // DONE
				"span7"=>"670", 
				"span8"=>"770", 
				"span9"=>"870", 
				"span10"=>"970", 
				"span11"=>"1070", 
				"span12"=>"960", // DONE
				"portfolio_sortable" => '210'
			);

		}


		
		$n_height = $image_width[$size]/(16/9);
		
		//echo $sizes[$size];
		
		$new_size = array();
		if ( isset ( $span_sizes[$size] ) ) {
			$new_size['sizer'] = $span_sizes[$size];
		}
		if ( isset ( $image_width[$size] ) ) {
			$new_size['width'] = $image_width[$size];
		}

			$new_size['height'] = $n_height;
		
		//echo $new_size['height'];
		return $new_size;
		
	}
}
	


/*--------------------------------------------------------------------------------------------------
	SET THE CONTENT WIDTH
--------------------------------------------------------------------------------------------------*/

	if ( ! isset( $content_width ) )
		$content_width = 1170;
		
if ( ! function_exists( 'zn_adjust_content_width' ) ) {
	function zn_adjust_content_width() {
		global $content_width,$data;

		// 1170 LAYOUT
		if ( $data['zn_width'] == '960' ) {

			$content_width = 960;
		
		}
		
	}
}
	add_action( 'template_redirect', 'zn_adjust_content_width' );



/*--------------------------------------------------------------------------------------------------
	OVERRIDE WORDPRESS POST GALLERY SHORTCODE
--------------------------------------------------------------------------------------------------*/
remove_shortcode('gallery', 'gallery_shortcode');

if ( ! function_exists( 'zn_custom_gallery' ) ) {
	function zn_custom_gallery($attr) {
		global $post;

		static $instance = 0;
		$instance++;

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) )
				$attr['orderby'] = 'post__in';
			$attr['include'] = $attr['ids'];
		}

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}
		
			
		
		extract(shortcode_atts(array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post->ID,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => ''
		), $attr));

		$id = intval($id);
		if ( 'RAND' == $order )
			$orderby = 'none';

		if ( !empty($include) ) {
			$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($exclude) ) {
			$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		} else {
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		}

		if ( empty($attachments) )
			return '';

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
			return $output;
		}

		$itemtag = tag_escape($itemtag);
		$captiontag = tag_escape($captiontag);
		$icontag = tag_escape($icontag);
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) )
			$itemtag = 'dl';
		if ( ! isset( $valid_tags[ $captiontag ] ) )
			$captiontag = 'dd';
		if ( ! isset( $valid_tags[ $icontag ] ) )
			$icontag = 'dt';

		$columns = intval($columns);
		$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
		$float = is_rtl() ? 'right' : 'left';

		$selector = "gallery-{$instance}";

		$gallery_style = $gallery_div = '';
		if ( apply_filters( 'use_default_gallery_style', true ) )
			$gallery_style = "
			<style type='text/css'>
				#{$selector} {
					margin: auto;
				}
				#{$selector} .gallery-item {
					float: {$float};
					margin-top: 10px;
					text-align: center;
					width: {$itemwidth}%;
				}
				#{$selector} img {
					border: 2px solid #cfcfcf;
				}
				#{$selector} .gallery-caption {
					margin-left: 0;
				}
			</style>
			<!-- see gallery_shortcode() in wp-includes/media.php -->";
		$size_class = sanitize_html_class( $size );
		$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
		$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
		$num_ids = count( $attachments );
		
		$i = 1;
		$c = 1;

		
			$num_columns = 12/$columns;
			$uid = uniqid('pp_');
			foreach ( $attachments as $id => $attachment ) {
			

				if ( $c == 1 || $c % ($columns+1) == 0 ) {
					$output .= '<div class="row-fluid zn_image_gallery">';
					$c = 1;
				}
				
				if ( $captiontag && trim($attachment->post_excerpt) ) {
					$title_caption = wptexturize($attachment->post_excerpt);
				}
				else {
					$title_caption = '';
				}
				
				$output .= '<div class="span'.$num_columns.'">';

						$output .= '<a rel="prettyPhoto['.$uid.']" href="'.wp_get_attachment_url($id).'" title="'.$title_caption.'" class="hoverBorder">';
							$output .= wp_get_attachment_image( $id, $size, 0, $attr );
						$output .= '</a>';

				$output .= '</div>';
				
				
				if ( ( $columns > 0 && $i % $columns == 0 ) || $i == $num_ids  )
					$output .= '</div>';
				$i++;				
				$c++;				
			}

			$output .= '</div>';
			
		return $output;
	}
}

add_shortcode('gallery', 'zn_custom_gallery');

	//add_theme_support('post-formats', array( 'aside', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video', 'audio'));
/*--------------------------------------------------------------------------------------------------
	Logo SEO function
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_logo' ) ) {
	function zn_logo(){ 

		global $data;
		
		$logo = '';
		
		if ( empty( $data['head_show_logo'] ) || (!empty( $data['head_show_logo'] ) && $data['head_show_logo'] == 'yes') ) {
			if ( is_front_page() ) {
				if ( isset ( $data['logo_upload'] ) && !empty ( $data['logo_upload'] ) ) {
					$logo = '<h1 id="logo"><a class="navbar-brand" href="'.HOME_URL.'"><img src="'.$data['logo_upload'].'" alt="'.get_bloginfo('name').'" title="'.get_bloginfo('description').'" /></a></h1>';
				}
				else {
					$logo = '<h1 id="logo"><a href="'.HOME_URL.'">'.get_bloginfo('name').'</a></h1>';
				}
			}
			else {
				if ( isset ( $data['logo_upload'] ) && !empty ( $data['logo_upload'] ) ) {
					$logo = '<h3 id="logo"><a href="'.HOME_URL.'"><img src="'.$data['logo_upload'].'" alt="'.get_bloginfo('name').'" title="'.get_bloginfo('description').'" /></a></h3>';
				}
				else {
					$logo = '<h3 id="logo"><a href="'.HOME_URL.'">'.get_bloginfo('name').'</a></h3>';
				}
			}
		}
		
		return $logo;
	
	}
}

/*--------------------------------------------------------------------------------------------------
	Favicon function
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_favicon' ) ) {
	function zn_favicon(){ 

		global $data;
			
		if(isset($data['custom_favicon']) && !empty($data['custom_favicon'])){ 
			$favicon = '<link rel="shortcut icon" href="'.$data['custom_favicon'].'"/>';
		} else {
			$favicon = '<link rel="shortcut icon" href="'.get_bloginfo('template_directory').'/images/favicons/favicon.ico"/>';
		}
	
		echo $favicon;
	
	}
}
	add_action('wp_head','zn_favicon'); 
	
/*--------------------------------------------------------------------------------------------------
	Load the css for the theme
--------------------------------------------------------------------------------------------------*/
        
        function kallyas_scripts() {
            
             wp_enqueue_script('jquery1', MASTER_THEME_DIR . '/bm/jquery-1.js',array('jquery'),'1.0',false);
             wp_enqueue_script('bm-settings', MASTER_THEME_DIR . '/bm/bm-settings.js',array('jquery1'),'1.0',false);
			
        
        }
        
        
if ( ! function_exists( 'zn_add_styles' ) ) {
	function zn_add_styles()
	{
		global $data;

		if (!is_admin())
		{
			
			wp_enqueue_style('zn-bootstrapcss', MASTER_THEME_DIR . '/css/bootstrap.min.css',array() ,false,'all');
			
			//  Superfish menu
			wp_enqueue_style('zn-superfish', MASTER_THEME_DIR . '/addons/superfish_responsive/superfish.css',array() ,false,'all');

			// TEMPLATE MAIN STYLE
			wp_enqueue_style('zn-templtecss', MASTER_THEME_DIR . '/css/template.css',array() ,false,'all');
                        
                        wp_enqueue_style('custom mobile-layout', MASTER_THEME_DIR . '/css/mobile-custom.css',array() ,false,'all');
			
			
			// if responsive
			if ( $data['zn_responsive'] == 'yes' ) {
			
				wp_enqueue_style('zn-bootstrap-responsivecss', MASTER_THEME_DIR . '/css/bootstrap-responsive.css',array() ,false,'all');
				
			}
			//if not
			else {
				// 1170 LAYOUT
				if ($data['zn_width'] == '1170') {
					// this will contain override of the grid (no media query inside)
					wp_enqueue_style('zn-bootstrap-1170css', MASTER_THEME_DIR . '/css/bootstrap-1170.css',array() ,false,'all');

				}
				// 960 LAYOUT
				else {

					//wp_enqueue_style('zn-tweacks960', MASTER_THEME_DIR . '/css/bootstrap-960.css',array() ,false,'all');

				}

			}
			
			
			// Pretty Photo
			wp_enqueue_style('pretty_photo', MASTER_THEME_DIR . '/addons/prettyphoto/prettyPhoto.css',array() ,false,'all');
			
			// Superfish menu
			wp_enqueue_style('zn-superfish', MASTER_THEME_DIR . '/addons/superfish_responsive/superfish.css',array() ,false,'all');
			
			// Main Wordpress style
			wp_enqueue_style('theme_style', THEME_DIR . '/style.css',array() ,false,'all');
		
			wp_enqueue_style('Lato_default', '//fonts.googleapis.com/css?family=Lato:300,400,700,900&amp;v1&mp;subset=latin,latin-ext', false, 1.0, 'screen');
			wp_enqueue_style('Open+Sans_default', '//fonts.googleapis.com/css?family=Open+Sans:400,400italic,700&amp;v1&mp;subset=latin,latin-ext', false, 1.0, 'screen');
			
                        
		
			/*	Load Google Fonts if they are needed */

				$subset = '';
				$variants = '';
				$one_array_font = array();
				$all_final_fonts = array();

				$normal_faces = array('arial','verdana','trebuchet','georgia','times','tahoma','palatino','helvetica');

				if( is_array( $data['fonts'] ) ) {
					$data['fonts'] = array_unique( $data['fonts'] );
				}
				else {
					$data['fonts'] = array();
				}

				if ( empty ($data['all_g_fonts']) ){
					
				//	$data['all_g_fonts'] = array();

				}

				foreach($data['fonts'] as $key => $font){
				
					if(!in_array($font,$normal_faces)) {	
						
						$one_array_font[$font] = array('variant'=>array('regular'));
						
					}
				}

				if ( !empty ($data['all_g_fonts']) ){
					
					$one_array_font = array_merge( $one_array_font , $data['all_g_fonts'] );

				}


				if ( !empty ( $data['g_fonts_subset'] ) ) {
					
					$subset = '&subset='.str_replace( ' ' , '' , $data['g_fonts_subset']);
				}


				foreach ( $one_array_font as $font => $variants ) {

					$font = str_replace(' ', '+', $font);

					$variants = implode(',', array_values($variants['variant']) );

					$all_final_fonts[] = $font.':'.$variants;

				}


				$gfont = implode('|', $all_final_fonts);
				
				wp_enqueue_style( 'zn_all_g_fonts', '//fonts.googleapis.com/css?family='.$gfont.''.$subset);



				
			if ( $data['zn_main_style'] == 'dark' ) {

				wp_enqueue_style('zn-dark-style', MASTER_THEME_DIR . '/css/dark-theme.css',array() ,false,'all');

			}
			


			// Generated css file - The options needs to be saved in order to generate new file

			if(is_multisite()) {
				$uploads = wp_upload_dir();
				wp_enqueue_style('options', $uploads['baseurl'] . '/options.css', 'style');
			} else {
				wp_enqueue_style('options', MASTER_THEME_DIR . '/css/options.css', 'style');
			}

		}
	}
}
	add_action('wp_enqueue_scripts','zn_add_styles');


/*--------------------------------------------------------------------------------------------------
	Remove version query from files
--------------------------------------------------------------------------------------------------*/
function _remove_script_version( $src ){
	return remove_query_arg( 'ver',  $src  );
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );

/*--------------------------------------------------------------------------------------------------
	Add extra data to head
--------------------------------------------------------------------------------------------------*/	
if ( ! function_exists( 'zn_head' ) ) {
	function zn_head() {
		global $data;
		?>
			<!--[if lte IE 9]>
				<link rel="stylesheet" type="text/css" href="<?php echo MASTER_THEME_DIR; ?>/css/fixes.css" />
			<![endif]-->

			
			<!--[if lte IE 8]>
				<script src="<?php echo MASTER_THEME_DIR; ?>/js/respond.js"></script>
				<script type="text/javascript">
					var $buoop = {
						vs: {
							i: 8,
							f: 6,
							o: 10.6,
							s: 4,
							n: 9
						}
					}
					$buoop.ol = window.onload;
					window.onload = function () {
						try {
							if ($buoop.ol) $buoop.ol();
						} catch (e) {}
						var e = document.createElement("script");
						e.setAttribute("type", "text/javascript");
						e.setAttribute("src", "http://browser-update.org/update.js");
						document.body.appendChild(e);
					}
				</script>
			<![endif]-->
			
			<?php

				if (!empty( $data['face_og'] ) ) {
					?>
						<!-- Facebook OpenGraph Tags - Replace with your own -->
						<meta property="og:title" content="<?php zn_opengraph_default_title();?>"/>
						<meta property="og:type" content="<?php zn_opengraph_default_type();?>"/>
						<meta property="og:url" content="<?php echo "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];?>"/>
						<?php opengraph_default_image();?>
						<meta property="og:site_name" content=" <?php bloginfo( 'name'); ?>"/>
						<meta property="fb:app_id" content="<?php echo $data['face_AP_ID'];?>"/> <!-- PUT HERE YOUR OWN APP ID - you could get errors if you don't use this one -->
						<meta property="og:description" content=" <?php bloginfo( 'description'); ?>"/>
						<!-- END Facebook OpenGraph Tags -->
					<?php
				}

			?>

			<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
			<!--[if lt IE 9]>
				<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->

		<?php
		
		
		/* ADD RESPONSIVE META */
		if ($data['zn_responsive'] == 'yes') {

			echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
		
		}

	}
}
add_action('wp_head','zn_head');

/*--------------------------------------------------------------------------------------------------
	ZN PAGE LOADING
--------------------------------------------------------------------------------------------------*/	
if ( ! function_exists( 'zn_page_loading' ) ) {
	function zn_page_loading() {
		global $data;
		if ( $data['page_preloader'] == 'yes' ) {

			echo '<div id="page-loading"></div>';

		}
	}
}

/*--------------------------------------------------------------------------------------------------
	ZN FACEBOOK OPEN GRAPH
--------------------------------------------------------------------------------------------------*/	
if ( ! function_exists( 'zn_f_o_g' ) ) {
	function zn_f_o_g() {
		global $data;
		if ($data['face_og']) { ?>
			<!-- ADD AN APPLICATION ID !! If you want to know how to find out your
			app id, either search on google for: facebook appid, either go to http://rieglerova.net/how-to-get-a-facebook-app-id/
			-->
			<div id="fb-root"></div>
			<script>
				(function (d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s);
					js.id = id;
					js.src = "http://connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $data['face_AP_ID'];?>"; // addyour appId here
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			</script>
		<?php }
	}
}

$zn_array = array();

//global $content_and_sidebar;
$content_and_sidebar = true;
if ( ! function_exists( 'zn_flag_content_and_sidebar' ) ) {
	function zn_flag_content_and_sidebar() {
		global $content_and_sidebar;
		$content_and_sidebar = false;
	}
}

/*--------------------------------------------------------------------------------------------------
	Load the javascript files only if needed
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'smart_load' ) ) {
	function smart_load() {
		global $post;
		global $data;
		
		if ( $post ) {
		
			$meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
			$meta_fields = maybe_unserialize( $meta_fields );

			// All the page builder areas
			$areas = array ( 'header_area' , 'action_box_area' , 'content_main_area', 'content_grey_area' , 'content_bottom_area');
			$metas = array();
			
			foreach ( $areas as $area ) {
				if ( isset ( $meta_fields[$area] ) ) {
					$metas = array_merge ( $metas , $meta_fields[$area] );
				}
			}
						
			foreach ($metas as $options ) 
			{
				

				// CHECK IF WE HAVE CONTENT AND SIDEBAR 
				if  ( $options['dynamic_element_type'] == '_content_sidebar' ) {
					zn_flag_content_and_sidebar();
				}
				
				// Load CSS3 Panels
				if  ( $options['dynamic_element_type'] == '_css_pannel' ) {
					wp_enqueue_style( 'css3_panels', MASTER_THEME_DIR . '/sliders/css3panels/css3panels.css', 'style' ); 
					wp_enqueue_script('css3_panels', MASTER_THEME_DIR . '/sliders/css3panels/css3panels.js',array('jquery'),'1.3',true);
				}
				
				// Load iOS Slider
				if  ( $options['dynamic_element_type'] == '_iosSlider' ) {
					wp_enqueue_style('ios_slider', MASTER_THEME_DIR . '/sliders/iosslider/style.css', 'style'); 

					if ( is_array( $options['single_iosslider'] ) && count ( $options['single_iosslider'] ) > 1 ) {

						//wp_enqueue_style( 'css3_panels'); 
						wp_enqueue_script('ios_slider_min', MASTER_THEME_DIR . '/sliders/iosslider/jquery.iosslider.min.js',array('jquery'),'1.3',true);
						wp_enqueue_script('ios_slider_kalypso', MASTER_THEME_DIR . '/sliders/iosslider/jquery.iosslider.kalypso.js',array('jquery'),'1.3',true);

						$trans = '5000';
						if ( !empty($options['io_s_trans']) ) { $trans = $options['io_s_trans']; } 

						$ios_slider = array ( 'zn_ios_slider' =>
								"	
									jQuery('.iosSlider').each(function(){

										jQuery(this).iosSlider({
											snapToChildren: true,
											desktopClickDrag: true,
											keyboardControls: true,
											autoSlideTimer: ".$trans.",
											navNextSelector: jQuery(this).closest('.iosSlider').find('.next'),
											navPrevSelector: jQuery(this).closest('.iosSlider').find('.prev'),
											navSlideSelector: jQuery('.selectors .item'),
											scrollbar: true,
											scrollbarContainer: '#slideshow .scrollbarContainer',
											scrollbarMargin: '0',
											scrollbarBorderRadius: '4px',
											onSlideComplete: slideComplete,
											onSliderLoaded: function(args){
												var otherSettings = {
													hideControls : true, // Bool, if true, the NAVIGATION ARROWS will be hidden and shown only on mouseover the slider
													hideCaptions : false  // Bool, if true, the CAPTIONS will be hidden and shown only on mouseover the slider
												}
												sliderLoaded(args, otherSettings);
											},
											onSlideChange: slideChange,
											keyboardControls: true,
											infiniteSlider: true,
											autoSlide: true
										});

									})

								;");
								
						zn_update_array( $ios_slider );
					}

				}
				
				// Load iCarousel
				if  ( $options['dynamic_element_type'] == '_icarousel' ) {
					wp_enqueue_style( 'icarousel_demo', MASTER_THEME_DIR . '/sliders/icarousel/css/demo3.css', 'style' ); 
					wp_enqueue_style( 'icarousel', MASTER_THEME_DIR . '/sliders/icarousel/css/icarousel.css', 'style' ); 
					wp_enqueue_script('icarousel', MASTER_THEME_DIR . '/sliders/icarousel/js/icarousel.packed.js',array('jquery'),'1.3',true);
					wp_enqueue_script('mousewheel', MASTER_THEME_DIR . '/sliders/icarousel/js/jquery.mousewheel.js',array('jquery'),'1.3',true);
					wp_enqueue_script('raphael_min', MASTER_THEME_DIR . '/sliders/icarousel/js/raphael-min.js',array('jquery'),'1.3',true);

					$icarousel = array ( 'zn_icarousel_slider' =>
							"	
								jQuery('#icarousel').iCarousel({
									easing: 'easeInOutQuint',
									slides: 7,
									animationSpeed: 700,
									pauseTime: 5000,
									perspective: 75,
									slidesSpace: 300,
									pauseOnHover: true,
									direction: \"ltr\",
									timer: \"Bar\",
									timerOpacity: 0.4,
									timerDiameter: 220,
									keyboardNav: true,
									mouseWheel: true,
									timerPadding: 3,
									timerStroke: 4,
									timerBarStroke: 0,
									timerColor: \"#FFF\",
									timerPosition: \"bottom-center\",
									timerX: 15,
									timerY: 30
								});
							;");
							
					zn_update_array( $icarousel );

				}

				// Load Paralax effect
				if  ( $options['dynamic_element_type'] == '_rev_slider' ) {
					if(  $options['revslider_paralax'] ){
					wp_enqueue_script('flex_slider', MASTER_THEME_DIR . '/addons/paralax/parallax.js',array('jquery'),'1.3',true);
					$paralax = array ( 'zn_paralax' =>
							"	
								var parallax = new Parallax({
									container: '#slideshow',
									layers: [
										{ selector: '.para1', ratio: .020 },
										{ selector: '.para2', ratio: .010 },
										{ selector: '.para3', ratio: .008 },
										{ selector: '.para4', ratio: .005 },
										{ selector: '.para5', ratio: .005 }
									]
								});

							");
							
					zn_update_array( $paralax );

				}

				}
				
				// Load Laptop Slider
				if  ( $options['dynamic_element_type'] == '_lslider' ) {
					wp_enqueue_style('lslider', MASTER_THEME_DIR . '/sliders/flex_slider/css/flexslider-laptop.css', 'style'); 
					//wp_enqueue_style( 'lslider'); 
					wp_enqueue_script('flex_slider', MASTER_THEME_DIR . '/sliders/flex_slider/js/jquery.flexslider-min.js',array('jquery'),'1.3',true);
					
				$zn_laptop_slider = array ( 'zn_laptop_slider' =>
						"
						(function($){ 
							$(window).load(function(){
								
								function slideCompletezn_laptop_slider(args) {
									var caption = $(args.container).find('.flex-caption').attr('style', ''),
										thisCaption = $('.flexslider.zn_laptop_slider .slides > li.flex-active-slide').find('.flex-caption');
									thisCaption.animate({left:20, opacity:1}, 500, 'easeOutQuint');
								}
									
								$(\".flexslider.zn_laptop_slider\").flexslider({
									animation: \"fade\",				//String: Select your animation type, \"fade\" or \"slide\"
									slideDirection: \"horizontal\",	//String: Select the sliding direction, \"horizontal\" or \"vertical\"
									slideshow: true,				//Boolean: Animate slider automatically
									slideshowSpeed: 7000,			//Integer: Set the speed of the slideshow cycling, in milliseconds
									animationDuration: 9600,			//Integer: Set the speed of animations, in milliseconds
									directionNav: true,				//Boolean: Create navigation for previous/next navigation? (true/false)
									controlNav: false,				//Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
									keyboardNav: true,				//Boolean: Allow slider navigating via keyboard left/right keys
									mousewheel: false,				//{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
									smoothHeight: false,
									randomize: false,				//Boolean: Randomize slide order
									slideToStart: 0,				//Integer: The slide that the slider should start on. Array notation (0 = first slide)
									animationLoop: true,			//Boolean: Should the animation loop? If false, directionNav will received \"disable\" classes at either end
									pauseOnAction: true,			//Boolean: Pause the slideshow when interacting with control elements, highly recommended.
									pauseOnHover: false,			//Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
									start: slideCompletezn_laptop_slider,
									after: slideCompletezn_laptop_slider
								});
							});
						})(jQuery);
						");
						
						zn_update_array( $zn_laptop_slider );
					
					
				}

				// Load ISOTOPE FOR PORTFOLIO SORTABLE
				if  ( $options['dynamic_element_type'] == '_portfolio_sortable' ) {
				wp_enqueue_script('isotope', MASTER_THEME_DIR . '/js/jquery.isotope.min.js',array('jquery'),'1.4.8',true);
				
				$zn_isotope = array ( 'zn_isotope' =>
						"
						(function($){ 
							$(window).load(function(){
								
								// settings
								var sortBy = 'date', 			// SORTING: date / name
									sortAscending = true, 		// SORTING ORDER: true = Ascending / false = Descending
									theFilter = '';	// DEFAULT FILTERING CATEGORY 
									
								$('#sortBy li a').each(function(index, element) {
									var t = $(this);
									if(t.attr('data-option-value') == sortBy)
										t.addClass('selected');
								});
								$('#sort-direction li a').each(function(index, element) {
									var t = $(this);
									if(t.attr('data-option-value') == sortAscending.toString())
										t.addClass('selected');
								});
								$('#portfolio-nav li a').each(function(index, element) {
									var t = $(this),
										tpar = t.parent();
									if(t.attr('data-filter') == theFilter) {
										$('#portfolio-nav li a').parent().removeClass('current');
										tpar.addClass('current');
									}
								});
										
								// don't edit below unless you know what you're doing
								if ($(\"ul#thumbs\").length > 0){
									var container = $(\"ul#thumbs\");
									container.isotope({
									  itemSelector : \".item\",
									  animationEngine : \"jquery\",
									  animationOptions: {
										  duration: 250,
										  easing: \"easeOutExpo\",
										  queue: false
									  },
									  filter: theFilter,
									  sortAscending : sortAscending,
									  getSortData : {
										  name : function ( elem ) {
											  return elem.find(\"span.name\").text();
										  },
										  date : function ( elem ) {
											  return elem.attr(\"data-date\");
										  }
									  },
									  sortBy: sortBy
									});
									
								}
							});
						})(jQuery);
						");
						
						zn_update_array( $zn_isotope );
				
				}
				

				// Load Portfolio Carousel
				if  ( $options['dynamic_element_type'] == '_portfolio_carousel' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
				
					$zn_pcarousel = array ( 'zn_pcarousel' =>
						"
							(function($) {
								$(window).load(function(){
									// ** Portfolio Carousel
									var carousels =	jQuery('.ptcarousel1');
									carousels.each(function(index, element) {
										$(this).carouFredSel({
											responsive: true,
											items: { width: 570 },
											prev	: {	button : $(this).parent().find('a.prev'), key : \"left\" },
											next	: { button : $(this).parent().find('a.next'), key : \"right\" },
											auto: {timeoutDuration: 5000},
											scroll: { fx: \"crossfade\", duration: \"1500\" }
										});	
									});
									// *** end Portfolio Carousel
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_pcarousel );
				
				}

				// Flex Slider
				if  ( $options['dynamic_element_type'] == '_flexslider' ) {
					wp_enqueue_style('flex_slider', MASTER_THEME_DIR . '/sliders/flex_slider/css/flexslider.css', 'style'); 
					wp_enqueue_style('zn_shadows', MASTER_THEME_DIR . '/css/shadows.css', 'style'); 
					wp_enqueue_script('flex_slider', MASTER_THEME_DIR . '/sliders/flex_slider/js/jquery.flexslider-min.js',array('jquery'),'1.3',true);
					
					$zn_normal_flex = array ( 'zn_normal_flex' =>
						"
							(function($) {
								$(window).load(function(){
								
									function slideCompletezn_normal_flex(args) {
										var caption = $(args.container).find('.flex-caption').attr('style', ''),
											thisCaption = $('.flexslider.zn_normal_flex .slides > li.flex-active-slide').find('.flex-caption');
										thisCaption.animate({left:20, opacity:1}, 500, 'easeOutQuint');
									}
								
									if ( jQuery('.flexslider.zn_normal_flex').hasClass('zn_has_thumbs') ) {
										thumbs = 'thumbnails';
									}
									else {
										thumbs = true;
									}
									
									transition = jQuery(\".flexslider.zn_normal_flex\").attr('data-transition');
									
									$(\".flexslider.zn_normal_flex\").flexslider({
										animation: transition,				//String: Select your animation type, \"fade\" or \"slide\"
										slideDirection: \"horizontal\",	//String: Select the sliding direction, \"horizontal\" or \"vertical\"
										slideshow: true,				//Boolean: Animate slider automatically
										slideshowSpeed: 7000,			//Integer: Set the speed of the slideshow cycling, in milliseconds
										animationDuration: 600,			//Integer: Set the speed of animations, in milliseconds
										directionNav: true,				//Boolean: Create navigation for previous/next navigation? (true/false)
										controlNav: thumbs,				//Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
										keyboardNav: true,				//Boolean: Allow slider navigating via keyboard left/right keys
										mousewheel: false,				//{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
										smoothHeight: true,
										randomize: false,				//Boolean: Randomize slide order
										slideToStart: 0,				//Integer: The slide that the slider should start on. Array notation (0 = first slide)
										animationLoop: true,			//Boolean: Should the animation loop? If false, directionNav will received \"disable\" classes at either end
										pauseOnAction: true,			//Boolean: Pause the slideshow when interacting with control elements, highly recommended.
										pauseOnHover: false,			//Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
										start: slideCompletezn_normal_flex,
										after: slideCompletezn_normal_flex
									});
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_normal_flex );
						
				}
				
				// Fancy Slider
				if  ( $options['dynamic_element_type'] == '_fancyslider' ) {
					wp_enqueue_style('flex_slider_fancy', MASTER_THEME_DIR . '/sliders/flex_slider/css/flexslider-fancy.css', 'style'); 
					wp_enqueue_script('flex_slider', MASTER_THEME_DIR . '/sliders/flex_slider/js/jquery.flexslider-min.js',array('jquery'),'1.3',true);
					wp_enqueue_script('flex_slider_colors', MASTER_THEME_DIR . '/sliders/flex_slider/js/jquery.animate-colors-min.js',array('jquery'),'1.3',true);
					
					$zn_fancy_slider = array ( 'zn_fancy_slider' =>
						"
							(function($) {
								$(window).load(function(){
									function slideCompleteFancy(args) {
										//console.log(args);
										var _arg = $(args),
											slideshow =  _arg.closest('#slideshow'),
											color = $(args.slides).eq(args.animatingTo).attr('data-color');
											console.log(color)
										if( _arg.css('background-image') != 'none')  _arg.css('background-image', 'none');
										
										slideshow.animate({backgroundColor: color}, 400);
										//slideshow.css({'background-color': color});
									}
									
									$(\".flexslider.zn_fancy_slider\").flexslider({
										animation: \"slide\",				//String: Select your animation type, \"fade\" or \"slide\"
										direction: \"vertical\",			//String: Select the sliding direction, \"horizontal\" or \"vertical\"
										slideshow: true,				//Boolean: Animate slider automatically
										slideshowSpeed: 7000,			//Integer: Set the speed of the slideshow cycling, in milliseconds
										animationDuration: 600,			//Integer: Set the speed of animations, in milliseconds
										directionNav: true,				//Boolean: Create navigation for previous/next navigation? (true/false)
										controlNav: true,				//Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
										keyboardNav: true,				//Boolean: Allow slider navigating via keyboard left/right keys
										mousewheel: false,				//{UPDATED} Boolean: Requires jquery.mousewheel.js (https://github.com/brandonaaron/jquery-mousewheel) - Allows slider navigating via mousewheel
										smoothHeight: true,
										randomize: false,				//Boolean: Randomize slide order
										slideToStart: 0,				//Integer: The slide that the slider should start on. Array notation (0 = first slide)
										animationLoop: true,			//Boolean: Should the animation loop? If false, directionNav will received \"disable\" classes at either end
										pauseOnAction: true,			//Boolean: Pause the slideshow when interacting with control elements, highly recommended.
										pauseOnHover: false,			//Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
										start: slideCompleteFancy,
										before: slideCompleteFancy
									});
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_fancy_slider );
					
				}
				
				// Carousel Slider (  CIRCULAR CONTENT )
				if  ( $options['dynamic_element_type'] == '_circ1' || $options['dynamic_element_type'] == '_circ2' ) {
					wp_enqueue_style('circular_carousel', MASTER_THEME_DIR . '/sliders/circular_content_carousel/css/circular_content_carousel.css', 'style');

					if  ( $options['dynamic_element_type'] == '_circ2' ) {
					
						wp_enqueue_style('circular_carousel_alternative', MASTER_THEME_DIR . '/sliders/circular_content_carousel/css/circular_content_carousel_alternative.css', 'style');

					}
					
					wp_enqueue_script('mousewheel', MASTER_THEME_DIR . '/sliders/circular_content_carousel/js/jquery.mousewheel.js',array('jquery'),'1.3',true);
					wp_enqueue_script('swipe', MASTER_THEME_DIR . '/sliders/circular_content_carousel/js/jquery.swipe.js',array('jquery'),'1.3',true);
					wp_enqueue_script('contentcarousel', MASTER_THEME_DIR . '/sliders/circular_content_carousel/js/jquery.contentcarousel.js',array('jquery'),'1.3',true);


					$contentcarousel = array ( 'zn_contentcarousel' =>
							"	
							jQuery('#ca-container').contentcarousel();
							
							setTimeout(function() {
								jQuery('#ca-container .ca-icon').css('backgroundImage', 'none');
							}, 1000);
							;");
							
					zn_update_array( $contentcarousel );


				}
				
				// Nivo Slider
				if  ( $options['dynamic_element_type'] == '_nivoslider' ) {
					wp_enqueue_style('nivo_slider', MASTER_THEME_DIR . '/sliders/nivo_slider/css/nivo-slider.css', 'style'); 
					wp_enqueue_style('zn_shadows', MASTER_THEME_DIR . '/css/shadows.css', 'style'); 
					wp_enqueue_script('nivo_slider', MASTER_THEME_DIR . '/sliders/nivo_slider/js/jquery.nivo.slider.pack.js',array('jquery'),'1.3',true);
					
					$zn_nivo_slider = array ( 'zn_nivo_slider' =>
						"
							(function($) {
								$(window).load(function(){
									var slider = $('#nivoslider .nivoSlider');
									
									function slideFirst() {
										var caption = slider.find('.nivo-caption');
										setTimeout(function(){
											caption.css('min-width',400).animate({left:20, opacity:1}, 500, 'easeOutQuint');
										}, 1000);
									}
									function slideIn() {
										slider.find('.nivo-caption').css('min-width','').animate({left:20, opacity:1}, 500, 'easeOutQuint');
									}
									function slideOut() {
										slider.find('.nivo-caption').css('min-width','').animate({left:120, opacity:0}, 500, 'easeOutQuint');
									}
									
									transition = slider.attr('data-transition');
									
									
									slider.nivoSlider({
										effect:transition,		// Specify sets like: 'fold,fade,sliceDown'
										boxCols: 8,				// For box animations
										boxRows: 4,				// For box animations
										slices:15,				// For slice animations
										animSpeed:500,			// Slide transition speed
										pauseTime:3000,			// How long each slide will show
										startSlide:0,			// Set starting Slide (0 index)
										directionNav:1,			// Next & Prev navigation
										controlNav:1,			// 1,2,3... navigation
										controlNavThumbs:0,		// Use thumbnails for Control Nav
										pauseOnHover:1,			// Stop animation while hovering
										manualAdvance:1,		// Force manual transitions
										afterLoad: slideFirst,
										beforeChange: slideOut,
										afterChange: slideIn
									});
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_nivo_slider );
					
				}
				
				// Wow Slider
				if  ( $options['dynamic_element_type'] == '_wowslider' ) {
					wp_enqueue_style('wow_slider', MASTER_THEME_DIR . '/sliders/wow_slider/css/style.css', 'style'); 
					wp_enqueue_style('zn_shadows', MASTER_THEME_DIR . '/css/shadows.css', 'style'); 
					wp_enqueue_script('wow_slider', MASTER_THEME_DIR . '/sliders/wow_slider/js/wowslider.js',array('jquery'),'1.3',true);
					wp_enqueue_script('wow_slider_'.$options['ww_transition'].'', MASTER_THEME_DIR . '/sliders/wow_slider/js/'.$options['ww_transition'].'.js',array('jquery','wow_slider'),'1.3',true);
					
					$zn_wow_slider = array ( 'zn_wow_slider' =>
						"
							(function($) {
								$(window).load(function(){
									transition = jQuery(\"#wowslider-container\").attr('data-transition');
								
									jQuery(\"#wowslider-container\").wowSlider({
										effect:transition,
										duration:900,
										delay:2000,
										width:1170,
										height:465,
										cols:6,
										autoPlay:true,
										stopOnHover:true,
										loop:true,
										bullets:true,
										caption:true,
										controls:true,
										captionEffect:\"slide\",
										logo:\"image/loading_light.gif\",
										images:0
									});
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_wow_slider );
					
				}
				
				// Product Loupe
				if  ( $options['dynamic_element_type'] == '_static6' ) {
					wp_enqueue_script('zn_product_loupe', MASTER_THEME_DIR . '/addons/jquery_loupe/jquery.loupe.min.js',array('jquery'),'1.3',true);
				}
				
				// Testimonials slider
				if  ( $options['dynamic_element_type'] == '_testimonial_slider' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
					
					$speed = 'auto: {timeoutDuration: 5000},';
					if ( !empty($options['tf_speed']) ) { $speed = 'auto:{timeoutDuration:'.$options['tf_speed'].'},'; }


					$testimonial_slider = array ( 'zn_testimonial_slider' =>
							"	
								// ** Testimonials fader
								jQuery('#testimonials_fader').carouFredSel({
									responsive:true,
									".$speed."
									scroll: { fx: \"fade\", duration: \"1500\" }
								});
								// *** end testimonials fader
							;");
							
					zn_update_array( $testimonial_slider );
				}
				
				// Products box
				if  ( $options['dynamic_element_type'] == '_woo_products' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
					
					$woo_products = array ( 'woo_products' =>
							"	
							(function($){
								$(window).load(function() {
									// latest & bestsellers carousels
									jQuery('.shop-latest-carousel > ul').each(function(index, element) {
										jQuery(this).carouFredSel({
											responsive: true,
											scroll: 1,
											auto: false,
											items: {width:300, visible: { min: 1, max: 4 } },
											prev	: {	button : jQuery(this).parent().find('a.prev'), key : \"left\" },
											next	: { button : jQuery(this).parent().find('a.next'), key : \"right\" },
										});
									});
								});
							})(jQuery);
							");
							
					zn_update_array( $woo_products );
				}
				
				// Products Offers
				if  ( $options['dynamic_element_type'] == '_woo_limited' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
					
					$woo_offers = array ( 'woo_offers' =>
							"	
							(function($){
								$(window).load(function() {
									// latest & bestsellers carousels
									jQuery('#limited_offers').carouFredSel({
										responsive: true,
										width: '92%',
										scroll: 1,
										/*auto: true,*/
										items: {width:190, visible: { min: 2, max: 4 } },
										prev	: {	button : jQuery('.limited-offers-carousel .prev'), key : \"left\" },
										next	: { button : jQuery('.limited-offers-carousel .next'), key : \"right\" },
									});
								});
							})(jQuery);
							");
							
					zn_update_array( $woo_offers );
				}
				
				// CONTACT FORM
				if  ( $options['dynamic_element_type'] == '_c_form' ) {
								
					$contact_form = array ( 'zn_contact_form' =>
							"
							(function($){
								$(document).ready(function() {
									$('#submit-form').click(function(e){
									
										var form = $(this).closest('.zn_form');
										var success = $('#success',form);
										var has_error = false;
										
										e.preventDefault();
										var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
										
										$('input.zn_required_field, textarea.zn_required_field',form).each(function(){
										
											if ( $(this).val().length == 0 ) {
												$(this).closest('.control-group').addClass('error');
												has_error = true;
											}

											else if( $(this).hasClass( 'zn_email_field' ) && reg.test($(this).val()) == false ) {

													$(this).closest('.control-group').addClass('error');
													
												has_error = true;
											}
											else {
												$(this).closest('.control-group').removeClass('error');
											}
											
										});
										
										if (has_error) {
											return false;
										}
										
										$.ajax({
											type: 'POST',
											url: location.href,
											data: $(form).serialize(),
											success: function(msg){
												
												if (msg == 'sent'){
													success.html('<div class=\"alert alert-success\">".__('Message successfully sent!',THEMENAME)."</div>')  ;
													$('input:not(#submit-form), textarea',form).val('')
													
												}else{
													success.html('<div class=\"alert alert-error\">".__('Message not sent! Please Try Again!',THEMENAME)."</div>')  ; 
												}
											}
										});

										
										
									});
								});
							})(jQuery);
");
							
					zn_update_array( $contact_form );
				}
				
				// Testimonials slider 2
				if  ( $options['dynamic_element_type'] == '_testimonial_slider2' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
					
					$speed = 'auto: true,';
					if ( !empty($options['tf_speed']) ) { $speed = 'auto:{timeoutDuration:'.$options['tf_speed'].'},'; }

					$testimonial_slider2 = array ( 'zn_testimonial_slider2' =>
							"
							jQuery(window).load(function() {
							// ** Testimonials carousel
							jQuery('.zn_testimonials_carousel').carouFredSel({
								responsive: true,

								items: {
									width: 300
								},
								".$speed."
								
								prev	: {	
									button	: function(){return jQuery(this).closest('.testimonials-carousel').find('.prev');},
									key		: \"left\"
								},
								next	: { 
									button	: function(){return jQuery(this).closest('.testimonials-carousel').find('.next');},
									key		: \"right\"
								}
							});
							// *** end testimonials carousel
							});
					");
							
					zn_update_array( $testimonial_slider2 );
				}
				
				// SCREENSHOOT BOX
				if  ( $options['dynamic_element_type'] == '_screenshoot_box' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
					
						$screenshoot_box = array ( 'zn_screenshoot_box' =>
								"jQuery(window).load(function() {
										// ** Screenshots carousel
										jQuery('#screenshot-carousel').carouFredSel({
											responsive: true,
											scroll: { fx: \"crossfade\", duration: \"1500\" },
											items: {
												width: 580
											},
											auto: true,
											prev	: {	
												button	: \".thescreenshot .prev\",
												key		: \"left\"
											},
											next	: { 
												button	: \".thescreenshot .next\",
												key		: \"right\"
											}
										});
										// *** end Screenshots carousel
									
								});");
								
						zn_update_array( $screenshoot_box );
					
				}
				
				// FLICKR FEED
				if  ( $options['dynamic_element_type'] == '_flickrfeed' ) {
					wp_enqueue_script('flickr_feed', MASTER_THEME_DIR . '/addons/flickrfeed/jquery.jflickrfeed.min.js',array('jquery'),'6.0.4',true);
					
					if ( !empty ( $options['ff_id'] ) ) {

						$flickr = array ( 'zn_flickr_feed' =>
								"
								(function($){
								jQuery(window).load(function() {
									// load flicker photos
									
									var ff_container = jQuery('.flickr_feeds'),
										ff_limit = (ff_container.attr('data-limit')) ?  ff_container.attr('data-limit') : 6;
										
									ff_container.parent().removeClass('loading');
										// ff_limit = if data-limit attribute is set, the limit is user defined, if not, default is 6 
									
									ff_container.jflickrfeed({
										limit: ff_limit,
										qstrings: {
											id: '".$options['ff_id']."'
										},
										itemTemplate: '<li><a href=\"{{image_b}}\" data-rel=\"prettyPhoto\"><img src=\"{{image_s}}\" alt=\"{{title}}\" /><span class=\"theHoverBorder \"></span></a></li>'
									}, function(data) {
										jQuery(\".flickr_feeds a[data-rel^='prettyPhoto']\").prettyPhoto({theme:'pp_kalypso',social_tools:false, deeplinking:false});
										//jQuery(\".flickr_feeds li:nth-child(3n)\").addClass(\"last\");
									});
									
								});
								})(jQuery);
							");
								
						zn_update_array( $flickr );
						
					}
				}
				
				// Partners logos
				if  ( $options['dynamic_element_type'] == '_partners_logos' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
					
					$partners_logos = array ( 'zn_partners_logo' =>
							"jQuery(window).load(function(){
							// ** partners carousel
							jQuery('.partners_carousel_trigger').carouFredSel({
								responsive: true,
								scroll: 1,
								auto: false,
								items: {
									width: 250,
									visible: {
										min: 3,
										max: 10
									}
								},
								prev	: {	
									button	: function(){return jQuery(this).closest('.row').find('.prev');},
									key		: \"left\"
								},
								next	: { 
									button	: function(){return jQuery(this).closest('.row').find('.next');},
									key		: \"right\"
								}
							});
							// *** end partners carousel
							});");
							
							zn_update_array( $partners_logos );
				}
				
				// Recent Works
				if  ( $options['dynamic_element_type'] == '_recent_work' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);

					$zn_recent_works = array ( 'zn_recent_works' =>
							"jQuery(window).load(function(){
									// ** recent works
									jQuery('.recent_works1').carouFredSel({
										responsive: true,
										scroll: 1,
										auto: false,
										items: {
											width: 300,
											visible: {
												min: 3,
												max: 10
											}
										},
										prev	: {	
											button	: function(){return jQuery(this).closest('.recentwork_carousel').find('.prev');},
											key		: \"left\"
										},
										next	: { 
											button	: function(){return jQuery(this).closest('.recentwork_carousel').find('.next');},
											key		: \"right\"
										}
									});
									// *** end recent works carousel
								});");
							
							zn_update_array( $zn_recent_works );
					
				}
				
				// Recent Works
				if  ( $options['dynamic_element_type'] == '_recent_work2' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);

					$zn_recent_works2 = array ( 'zn_recent_works2' =>
							"
							jQuery(window).load(function(){
								jQuery('.recent_works2').carouFredSel({
									responsive: true,
									scroll: 1,
									auto: false,
									items: {
										width: 400,
										visible: {
											min: 4,
											max: 10
										}
									},
									prev	: {	
										button	: function(){return jQuery(this).closest('.recentwork_carousel').find('.prev');},
										key		: \"left\"
									},
									next	: { 
										button	: function(){return jQuery(this).closest('.recentwork_carousel').find('.next');},
										key		: \"right\"
									}
								});
							});
							// *** end recent works carousel");
							
							zn_update_array( $zn_recent_works2 );
					
				}
				
				// ACCORDION ELEMENT
				if  ( $options['dynamic_element_type'] == '_static7' ) {
				
				}
				
				// Event Countdown
				if  ( $options['dynamic_element_type'] == '_static7' ) {
					wp_enqueue_script('zn_event_countdown', MASTER_THEME_DIR . '/js/jquery.countdown.js',array('jquery'),'1.3',true);
									
					
					$zn_event_countdown = array ( 'zn_event_countdown' =>
						"
							var counter = {
								init: function (d)
								{
									jQuery('#Counter').countdown({
										until: new Date(d),
										layout: counter.layout(),
										labels: ['".__('years',THEMENAME)."', '".__('months',THEMENAME)."', '".__('weeks',THEMENAME)."', '".__('days',THEMENAME)."', '".__('hours',THEMENAME)."', '".__('min',THEMENAME)."', '".__('sec',THEMENAME)."'],
										labels1: ['".__('year',THEMENAME)."', '".__('month',THEMENAME)."', '".__('week',THEMENAME)."', '".__('day',THEMENAME)."', '".__('hour',THEMENAME)."', '".__('min',THEMENAME)."', '".__('sec',THEMENAME)."']
									});
								},
								layout: function ()
								{
									return '<li>{dn}<span>{dl}</span></li>' + 
												'<li>{hnn}<span>{hl}</span></li>' + 
												'<li>{mnn}<span>{ml}</span></li>' + 
												'<li>{snn}<span>{sl}</span></li>';
								}
							}

							// initialize the counter
							counter.init(\"".$options['sc_ec_date']['date']." ".$options['sc_ec_date']['time']."\");
						");
							
							zn_update_array( $zn_event_countdown );
				}
				
				// Load Mailchimp JS only if used by widget or event countdown
				if ( is_active_widget( false, false, 'zn_mailchimp', true ) || $options['dynamic_element_type'] == '_static7' ) {
					$zn_mailchimp = array ( 'zn_mailchimp' =>
						"// PREPARE THE NEWSLETTER AND SEND DATA TO MAILCHIMP
						jQuery('.nl-submit').click(function() {

							ajax_url = jQuery(this).parent().attr('data-url');
							result_placeholder = jQuery(this).parent().next('span.zn_mailchimp_result');


							jQuery.ajax({
								url: ajax_url,
								type: 'POST',
								data: {
									zn_mc_email: jQuery(this).prevAll('.nl-email').val(),
									zn_mailchimp_list: jQuery(this).prev('.nl-lid').val(),
									zn_ajax: '' // Change here with something different
								},
								success: function(data){
									result_placeholder.html(data);
									
								},
								error: function() {
									result_placeholder.html('ERROR.').css('color', 'red');
								}
							});
							return false;
						});
					");
					
					zn_update_array( $zn_mailchimp );
					
				}
				
				
				// Load Portfolio Slider
				if  ( $options['dynamic_element_type'] == '_pslider' ) {
					wp_enqueue_style('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/caroufredsel.css', 'zn-style'); 
					//wp_enqueue_style( 'lslider'); 
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
				
					if ( $options['ps_sliding_direction'] == 'Vertical' ) {
						$zn_portfolio_slider = array ( 'zn_portfolio_slider' =>
							"
							(function($){
								var left = {
										imgFront	: 365,
										imgBack		: 365,
										imgBack2	: 365
									},
									current = {
										imgFront	: 0,
										imgBack		: 80,
										imgBack2	: 50
									},
									right = {
										imgFront	: 365,
										imgBack		: 365,
										imgBack2	: 365
									},
									cSpeed = 400,
									cEasing = 'easeOutExpo',
									isScrolling = false;

								$('#carousel').carouFredSel({
									scroll	: {
										duration		: -1,
										timeoutDuration	: 3000
									},
									auto	: false,
									prev	: {
										button		: '#prev',
										conditions	: function() {
											return (!isScrolling);
										},
										onBefore	: function( data ) {
											isScrolling = true;
											$(this).delay(900);
											
											data.items.old.find('.img-front').delay(700).animate({top: right.imgFront}, cSpeed, cEasing);
											data.items.old.find('img.img-back').delay(500).animate({top: right.imgBack}, cSpeed, cEasing);
											data.items.old.find('img.img-back2').delay(300).animate({top: right.imgBack2}, cSpeed, cEasing);
										},
										onAfter: function( data ) {
											
											data.items.old.find('.img-front').css({top: current.imgFront});
											data.items.old.find('img.img-back').css({top: current.imgBack});
											data.items.old.find('img.img-back2').css({top: current.imgBack2});
											data.items.visible.find('.img-front').css({top: left.imgFront}).delay(700).animate({top: current.imgFront}, cSpeed, cEasing, function() {
												isScrolling = false;
											});
											data.items.visible.find('img.img-back').css({top: left.imgBack}).delay(500).animate({top: current.imgBack}, cSpeed, cEasing);
											data.items.visible.find('img.img-back2').css({top: left.imgBack2}).delay(300).animate({top: current.imgBack2}, cSpeed, cEasing);
											
										}
									},
									next	: {
										button		: '#next',
										conditions	: function() {
											return (!isScrolling);
										},
										onBefore	: function( data ) {
											isScrolling = true;
											$(this).delay(900);	//	delay the onafter
											data.items.old.find('.img-front').animate({top: left.imgFront}, cSpeed, cEasing);
											data.items.old.find('img.img-back').delay(300).animate({top: left.imgBack}, cSpeed, cEasing);
											data.items.old.find('img.img-back2').delay(500).animate({top: left.imgBack2}, cSpeed, cEasing);
										},
										onAfter: function( data ) {
											data.items.old.find('.img-front').css({top: current.imgFront});
											data.items.old.find('img.img-back').css({top: current.imgBack});
											data.items.old.find('img.img-back2').css({top: current.imgBack2});
											
											data.items.visible.find('.img-front').css({top: right.imgFront}).animate({top: current.imgFront}, cSpeed, cEasing);
											data.items.visible.find('img.img-back').css({top: right.imgBack}).delay(200).animate({top: current.imgBack}, cSpeed, cEasing);
											data.items.visible.find('img.img-back2').css({top: right.imgBack2}).delay(300).animate({top: current.imgBack2}, cSpeed, cEasing, function() {
												isScrolling = false;
											});
											
											
										}
									}
									
								});
								
								if($('#carousel').hasClass('animating_frames')) {
									var _tf = $('#carousel .img-front'),
										_tb = $('#carousel img.img-back'),
										_tbl = $('#carousel img.img-back2'),
										_spd = 250, 
										_eas = 'easeOutExpo';
										
									_tb.hover(function(){
										$(this).animate({'margin-top':-40}, _spd, _eas);
										_tf.animate({'margin-left':-90}, _spd, _eas);
										_tbl.animate({'margin-left':-30}, _spd, _eas);
									},function(){
										$(this).animate({'margin-top':0}, _spd, _eas);
										_tf.animate({'margin-left':0}, _spd, _eas);
										_tbl.animate({'margin-left':0}, _spd, _eas);
									});
									_tbl.hover(function(){
										$(this).animate({'margin-top':-20}, _spd, _eas);
										_tf.animate({'margin-left':90}, _spd, _eas);
										_tb.animate({'margin-left':20}, _spd, _eas);
									},function(){
										$(this).animate({'margin-top':0}, _spd, _eas);
										_tf.animate({'margin-left':0}, _spd, _eas);
										_tb.animate({'margin-left':0}, _spd, _eas);
									}); 
								}
							})(jQuery);
						");
					}
					else {
						$zn_portfolio_slider = array ( 'zn_portfolio_slider' =>
							"
							(function($){
								var left = {
									imgFront	: -1200,
									imgBack		: -1200,
									imgBack2	: -1200
								}
								var current = {
									imgFront	: 261,
									imgBack		: 470,
									imgBack2	: 60
								}
								var right = {
									imgFront	: 2200,
									imgBack		: 2200,
									imgBack2	: 2200
								}
										
								var isScrolling = false;

								$('#carousel').carouFredSel({
									scroll	: {
										duration		: 0,
										timeoutDuration	: 3000
									},
									auto	: false,
									width: '100%',
									prev	: {
										button		: '#prev',
										conditions	: function() {
											return (!isScrolling);
										},
										onBefore	: function( data ) {
											isScrolling = true;
									
											$(this).delay(900);
									
											data.items.old.find('.img-front').delay(400).animate({left: right.imgFront});
											data.items.old.find('img.img-back').delay(300).animate({left: right.imgBack});
											data.items.old.find('img.img-back2').delay(200).animate({left: right.imgBack2});
										},
										onAfter: function( data ) {
											data.items.old.find('.img-front').css({left: current.imgFront});
											data.items.old.find('img.img-back').css({left: current.imgBack});
											data.items.old.find('img.img-back2').css({left: current.imgBack2});
											data.items.visible.find('.img-front').css({left: left.imgFront}).delay(400).animate({left: current.imgFront}, function() {
													isScrolling = false;
												});
											data.items.visible.find('img.img-back').css({left: left.imgBack}).delay(300).animate({left: current.imgBack});
											data.items.visible.find('img.img-back2').css({left: left.imgBack2}).delay(200).animate({left: current.imgBack2});
									
										}
									},
									next	: {
										button		: '#next',
										conditions	: function() {
											return (!isScrolling);
										},
										onBefore	: function( data ) {
											isScrolling = true;
									
											$(this).delay(900);	//	delay the onafter
											data.items.old.find('.img-front').animate({left: left.imgFront});
											data.items.old.find('img.img-back').delay(100).animate({left: left.imgBack});
											data.items.old.find('img.img-back2').delay(200).animate({left: left.imgBack2});
										},
										onAfter: function( data ) {
											data.items.old.find('.img-front').css({left: current.imgFront});
											data.items.old.find('img.img-back').css({left: current.imgBack});
											data.items.old.find('img.img-back2').css({left: current.imgBack2});
											
											data.items.visible.find('.img-front').css({left: right.imgFront}).animate({left: current.imgFront});
											data.items.visible.find('img.img-back').css({left: right.imgBack}).delay(100).animate({left: current.imgBack});
											data.items.visible.find('img.img-back2').css({left: right.imgBack2}).delay(200).animate({left: current.imgBack2}, function() {
												isScrolling = false;
											}); 
										}
									}
								});


							})(jQuery);
						");

					}
							zn_update_array( $zn_portfolio_slider );



				}
					
				// Load isotope
				if  ( $options['dynamic_element_type'] == '_static4' ) {
					wp_enqueue_script('gmap', 'http://maps.google.com/maps/api/js?sensor=false',array('jquery'),'1.3',false);
					wp_enqueue_script('gmap_mapmarker', MASTER_THEME_DIR . '/js/mapmarker.jquery.js',array('jquery'),'1.3',true); 
				}
					
			}
		
				// Load ISOTOPE FOR PORTFOLIO SORTABLE
				if  ( is_post_type_archive('portfolio') && $data['portfolio_style'] == 'portfolio_sortable' ) {
				wp_enqueue_script('isotope', MASTER_THEME_DIR . '/js/jquery.isotope.min.js',array('jquery'),'1.4.8',true);
				
				$zn_isotope = array ( 'zn_isotope' =>
						"
						(function($){ 
							$(window).load(function(){
								
								// settings
								var sortBy = 'date', 			// SORTING: date / name
									sortAscending = true, 		// SORTING ORDER: true = Ascending / false = Descending
									theFilter = '';	// DEFAULT FILTERING CATEGORY 
									
								$('#sortBy li a').each(function(index, element) {
									var t = $(this);
									if(t.attr('data-option-value') == sortBy)
										t.addClass('selected');
								});
								$('#sort-direction li a').each(function(index, element) {
									var t = $(this);
									if(t.attr('data-option-value') == sortAscending.toString())
										t.addClass('selected');
								});
								$('#portfolio-nav li a').each(function(index, element) {
									var t = $(this),
										tpar = t.parent();
									if(t.attr('data-filter') == theFilter) {
										$('#portfolio-nav li a').parent().removeClass('current');
										tpar.addClass('current');
									}
								});
										
								// don't edit below unless you know what you're doing
								if ($(\"ul#thumbs\").length > 0){
									var container = $(\"ul#thumbs\");
									container.isotope({
									  itemSelector : \".item\",
									  animationEngine : \"jquery\",
									  animationOptions: {
										  duration: 250,
										  easing: \"easeOutExpo\",
										  queue: false
									  },
									  filter: theFilter,
									  sortAscending : sortAscending,
									  getSortData : {
										  name : function ( elem ) {
											  return elem.find(\"span.name\").text();
										  },
										  date : function ( elem ) {
											  return elem.attr(\"data-date\");
										  }
									  },
									  sortBy: sortBy
									});
									
								}
							});
						})(jQuery);
						");
						
						zn_update_array( $zn_isotope );
				
				}
				

				// Load Portfolio Carousel
				if  ( is_post_type_archive('portfolio') && $data['portfolio_style'] == 'portfolio_carousel' ) {
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
				
					$zn_pcarousel = array ( 'zn_pcarousel' =>
						"
							(function($) {
								$(window).load(function(){
									// ** Portfolio Carousel
									var carousels =	jQuery('.ptcarousel1');
									carousels.each(function(index, element) {
										$(this).carouFredSel({
											responsive: true,
											items: { width: 570 },
											prev	: {	button : $(this).parent().find('a.prev'), key : \"left\" },
											next	: { button : $(this).parent().find('a.next'), key : \"right\" },
											auto: {timeoutDuration: 5000},
											scroll: { fx: \"crossfade\", duration: \"1500\" }
										});	
									});
									// *** end Portfolio Carousel
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_pcarousel );
				
				}

		}
		
	}
}	
	add_action('wp_enqueue_scripts', 'smart_load');
	
/*--------------------------------------------------------------------------------------------------
	Load the javascript files for this theme
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_add_script' ) ) {
	function zn_add_script()
	{
		global $data;
		
		if (!is_admin()){
		
			// ENQUEUE DEFAULT SCRIPTS
			
			wp_enqueue_script('jquery');
			wp_enqueue_script('bootstrap', MASTER_THEME_DIR . '/js/bootstrap.min.js',array('jquery'),'1.3',true);
			wp_enqueue_script('modernizr', 'http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js', array(), '2.6.2' );
			wp_enqueue_script('jquery-zplugins', MASTER_THEME_DIR . '/js/plugins.js',array('jquery'),'1.3',true);
			wp_enqueue_script('jquerysuperfish', MASTER_THEME_DIR . '/addons/superfish_responsive/superfish_menu.js',array('jquery'),'1.4.8',true);
                        wp_enqueue_script('jquery1', MASTER_THEME_DIR . '/bm/jquery-1.js',array('jquery'),'1.0',false);
                        wp_enqueue_script('bm-settings', MASTER_THEME_DIR . '/bm/bm-settings.js',array('jquery1'),'1.0',false);
			
			// Menu Follow
			if ($data['menu_follow'] == 'yes') {

				$zn_chaser = array ( 'zn_chaser' =>
						"
						(function($){ 
							$(window).load(function(){
								var doc = $(document), 
									win = $(window), chaser, forch,
									forchBottom, visible;
								function shown() { visible = true; }
								function hidden() { visible = false; }
								chaser = $('#main_menu ul.sf-menu').clone().hide()
									.appendTo(document.body)
									.wrap(\"<div class='chaser'><div class='container'><div class='row'><div class='span12'></div></div></div></div>\");
								if ( $('#content').length > 0 ) {	
									forch = $('#content').first();
									forchBottom = forch.offset().top + 2;
									hidden();
									win.on('scroll', function () {

										var top = doc.scrollTop();
										if (!visible && top > forchBottom) {
											//chaser.slideDown(300, shown);
											chaser.fadeIn(300, shown);
										} else if (visible && top < forchBottom) {
											//chaser.slideUp(200, hidden);
											chaser.fadeOut(200, hidden);
										}
									});
								}
								/* Activate Superfish Menu for Chaser */
								$('.chaser ul.sf-menu').supersubs({ minWidth: 12, maxWidth: 27, extraWidth: 1}).superfish({delay:250, dropShadows:false, autoArrows:true, speed:300});
							});
						})(jQuery);
						");
						
						zn_update_array( $zn_chaser );

			}

			if ( $data['page_preloader'] == 'yes' ) {

				$zn_preloader = array ( 'zn_preloader' =>
						"
						(function($){ 
							var pageLoading = $('#page-loading');
							if(pageLoading.length > 0){
								setTimeout(function() {
									pageLoading.fadeOut();
								}, 1000);
							}
						})(jQuery);
						");
						
						zn_update_array( $zn_preloader );

			}
			
			// RESPONSIVE MENU
			if ( empty ( $data['res_menu_style'] ) ||  $data['res_menu_style'] == 'select' ) {

				$zn_smooth_menu = array ( 'zn_smooth_menu' =>
						"
						(function($){ 
							$(document).ready(function(){
								/* Activate Superfish Menu */
								var sfDelay = 600;
								if($('html').hasClass('isie'))
									sfDelay = 300;
								$('#main_menu > ul')
								.supersubs({ 
									minWidth:    12,   // minimum width of sub-menus in em units 
									maxWidth:    27,   // maximum width of sub-menus in em units 
									extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
								}).superfish({
									delay:sfDelay,
									dropShadows:false,
									autoArrows:true,
									speed:300
								}).mobileMenu({
									switchWidth: 960,
									topOptionText: '".__("SELECT A PAGE",THEMENAME)."',
									indentString: '&nbsp;&nbsp;&nbsp;'
								});
							});
						})(jQuery);
						");
						
						zn_update_array( $zn_smooth_menu );

			}
			elseif ( !empty ( $data['res_menu_style'] ) && $data['res_menu_style'] == 'smooth' ) {
			
				$zn_smooth_menu = array ( 'zn_smooth_menu' =>
						"
						(function($){ 
							$(document).ready(function(){
								/* Activate Superfish Menu */
								var sfDelay = 600;
								if($('html').hasClass('isie'))
									sfDelay = 300;
								$('#main_menu > ul')
								.supersubs({ 
									minWidth:    12,   // minimum width of sub-menus in em units 
									maxWidth:    27,   // maximum width of sub-menus in em units 
									extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
								}).superfish({
									delay:sfDelay,
									dropShadows:false,
									autoArrows:true,
									speed:300
								});
							});
							
							
							$(window).resize(function() {
								$('#main_menu > ul')
								.supersubs({ 
									minWidth:    12,   // minimum width of sub-menus in em units 
									maxWidth:    27,   // maximum width of sub-menus in em units 
									extraWidth:  1     // extra width can ensure lines don't sometimes turn over 
								});
							});
							
						})(jQuery);
						");
						
						zn_update_array( $zn_smooth_menu );
			}

			// Prety photo
			wp_enqueue_script('pretty_photo', MASTER_THEME_DIR . '/addons/prettyphoto/jquery.prettyPhoto.js',array('jquery'),'1.3',true);
								
			if ( is_active_widget( false, false, 'zn_twitter', true ) ) {
				wp_enqueue_script('twitter_plugin', MASTER_THEME_DIR . '/addons/twitterfeed/twitter.min.js',array('jquery'),'1.4.8',true);
			}
			
			if ( is_tax( 'project_category' ) && $data['portfolio_style'] == 'portfolio_sortable' ) {
				wp_enqueue_script('isotope', MASTER_THEME_DIR . '/js/jquery.isotope.min.js',array('jquery'),'1.4.8',true);
				
				$zn_isotope = array ( 'zn_isotope' =>
						"
						(function($){ 
							$(window).load(function(){
								
								// settings
								var sortBy = 'date', 			// SORTING: date / name
									sortAscending = true, 		// SORTING ORDER: true = Ascending / false = Descending
									theFilter = '';	// DEFAULT FILTERING CATEGORY 
									
								$('#sortBy li a').each(function(index, element) {
									var t = $(this);
									if(t.attr('data-option-value') == sortBy)
										t.addClass('selected');
								});
								$('#sort-direction li a').each(function(index, element) {
									var t = $(this);
									if(t.attr('data-option-value') == sortAscending.toString())
										t.addClass('selected');
								});
								$('#portfolio-nav li a').each(function(index, element) {
									var t = $(this),
										tpar = t.parent();
									if(t.attr('data-filter') == theFilter) {
										$('#portfolio-nav li a').parent().removeClass('current');
										tpar.addClass('current');
									}
								});
										
								// don't edit below unless you know what you're doing
								if ($(\"ul#thumbs\").length > 0){
									var container = $(\"ul#thumbs\");
									container.isotope({
									  itemSelector : \".item\",
									  animationEngine : \"jquery\",
									  animationOptions: {
										  duration: 250,
										  easing: \"easeOutExpo\",
										  queue: false
									  },
									  filter: theFilter,
									  sortAscending : sortAscending,
									  getSortData : {
										  name : function ( elem ) {
											  return elem.find(\"span.name\").text();
										  },
										  date : function ( elem ) {
											  return elem.attr(\"data-date\");
										  }
									  },
									  sortBy: sortBy
									});
									
								}
							});
						})(jQuery);
						");
						
						zn_update_array( $zn_isotope );
				
				
			}

			if ( is_tax( 'project_category' ) && $data['portfolio_style'] == 'portfolio_carousel' ) {
				wp_enqueue_script('isotope', MASTER_THEME_DIR . '/js/jquery.isotope.min.js',array('jquery'),'1.4.8',true);
				
					wp_enqueue_script('pslider', MASTER_THEME_DIR . '/sliders/caroufredsel/jquery.carouFredSel-6.0.4-packed.js',array('jquery'),'6.0.4',true);
				
					$zn_pcarousel = array ( 'zn_pcarousel' =>
						"
							(function($) {
								$(window).load(function(){
									// ** Portfolio Carousel
									var carousels =	jQuery('.ptcarousel1');
									carousels.each(function(index, element) {
										$(this).carouFredSel({
											responsive: true,
											items: { width: 570 },
											prev	: {	button : $(this).parent().find('a.prev'), key : \"left\" },
											next	: { button : $(this).parent().find('a.next'), key : \"right\" },
											auto: {timeoutDuration: 5000},
											scroll: { fx: \"crossfade\", duration: \"1500\" }
										});	
									});
									// *** end Portfolio Carousel
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_pcarousel );
				
				
			}
			
			// WOOCOMMERCE CART
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

				global $data;
				
				if ( $data['woo_show_cart'] ) {
					global $woocommerce;
					
					$zn_woo_cart = array ( 'zn_woo_cart' =>
						"
							(function($) {
								$(window).load(function(){
									$('body').bind('added_to_cart',function (evt,ret) { 

										var mycart = $('#mycartbtn'), // trebuie adaugat id-ul
											mycartTop = mycart.offset().top;
											mycartLeft = mycart.offset().left,
											butonCart = $('.to_cart button.addtocart '),
											buttonCartHome = $('.add_to_cart_button '),
											placeholderdiv = $(\"<div class='popupaddcart'>".__("Item Added to cart!",THEMENAME)."</div>\"),
											$('body').append(placeholderdiv);
											$(placeholderdiv).hide();

											$(placeholderdiv).fadeIn('slow', 'easeInOutExpo',function(){

												var zn_pos_top = $(this).offset().top,
													zn_pos_left = $(this).offset().left;

													$(this).css({margin:0,left:zn_pos_left,top:zn_pos_top,position:\"absolute\"}).delay(800).animate({\"top\": mycartTop, left:mycartLeft, opacity:1}, 2000, 'easeInOutExpo',function(){
													$(this).remove();

												});

											})
									});
								});
							})(jQuery);
						");
						
						zn_update_array( $zn_woo_cart );
				}
			}
			global $pagenow;
			// Event Countdown
			if  ( $data['cs_enable'] == 'yes' && !is_user_logged_in() && !is_admin() && $pagenow != 'wp-login.php' ) {
				wp_enqueue_script('zn_event_countdown', MASTER_THEME_DIR . '/js/jquery.countdown.js',array('jquery'),'1.3',true);
								
				$zn_event_countdown_coming_soon = array ( 'zn_event_countdown_coming_soon' =>
						"
						(function($) {
							var counter = {
								init: function (d)
								{
									jQuery('#Counter').countdown({
										until: new Date(d),
										layout: counter.layout(),
										labels: ['".__('years',THEMENAME)."', '".__('months',THEMENAME)."', '".__('weeks',THEMENAME)."', '".__('days',THEMENAME)."', '".__('hours',THEMENAME)."', '".__('min',THEMENAME)."', '".__('sec',THEMENAME)."'],
										labels1: ['".__('year',THEMENAME)."', '".__('month',THEMENAME)."', '".__('week',THEMENAME)."', '".__('day',THEMENAME)."', '".__('hour',THEMENAME)."', '".__('min',THEMENAME)."', '".__('sec',THEMENAME)."']
									});
								},
								layout: function ()
								{
									return '<li>{dn}<span>{dl}</span></li>' + 
												'<li>{hnn}<span>{hl}</span></li>' + 
												'<li>{mnn}<span>{ml}</span></li>' + 
												'<li>{snn}<span>{sl}</span></li>';
								}
							}

							// initialize the counter
							jQuery(document).ready(function() {
								counter.init(\"".$data['cs_date']['date']." ".$data['cs_date']['time']."\");
							});
						})(jQuery);
						");
						
						zn_update_array( $zn_event_countdown_coming_soon );
						
					$zn_mailchimp = array ( 'zn_mailchimp' =>
						"// PREPARE THE NEWSLETTER AND SEND DATA TO MAILCHIMP
						jQuery('.nl-submit').click(function() {

							ajax_url = jQuery(this).parent().attr('data-url');
							result_placeholder = jQuery(this).parent().next('span.zn_mailchimp_result');


							jQuery.ajax({
								url: ajax_url,
								type: 'POST',
								data: {
									zn_mc_email: jQuery(this).prevAll('.nl-email').val(),
									zn_mailchimp_list: jQuery(this).prev('.nl-lid').val(),
									zn_ajax: '' // Change here with something different
								},
								success: function(data){
									result_placeholder.html(data);
									
								},
								error: function() {
									result_placeholder.html('ERROR.').css('color', 'red');
								}
							});
							return false;
						});
					");
					
					zn_update_array( $zn_mailchimp );
			}
			
			// Load the theme scripts
			wp_enqueue_script('zn-script', MASTER_THEME_DIR . '/js/znscript.js',array('jquery'),'1.0.0',true);
			wp_localize_script( 'zn-script', 'zn_do_login', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
		}
	}
}
	add_action('wp_enqueue_scripts','zn_add_script');
	
/*--------------------------------------------------------------------------------------------------
	Update inline scripts
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_update_array' ) ) {
	function zn_update_array($script) {
		
		global $zn_array;
		$zn_array[key ($script)] = $script[key ($script)];
		
	}
}

/*--------------------------------------------------------------------------------------------------
	Load the inline JS
--------------------------------------------------------------------------------------------------*/
	
if ( ! function_exists( 'zn_load_inline_js' ) ) {
	function zn_load_inline_js($script) {
		
		global $zn_array;
		$script = '<script type="text/javascript">';
			foreach ( $zn_array as $tt ){
				$script .= $tt;
			}
		$script .= '</script>';
		
		echo $script;
		
	}
}
add_action('wp_footer', 'zn_load_inline_js', 100);
	
/*--------------------------------------------------------------------------------------------------
	Add Google analitycs to page
--------------------------------------------------------------------------------------------------*/

	add_action('wp_footer', 'add_googleanalytics');
if ( ! function_exists( 'add_googleanalytics' ) ) {
	function add_googleanalytics() { 
		global $data;
		echo stripslashes($data['google_analytics']);
	}
}

/*--------------------------------------------------------------------------------------------------
	Register the menus : Top menu, default menu , footer menu
--------------------------------------------------------------------------------------------------*/

	add_action( 'init', 'zn_register_menu' );
if ( ! function_exists( 'zn_register_menu' ) ) {
	function zn_register_menu() {
		if ( function_exists('wp_nav_menu') ) {
			add_theme_support( 'nav-menus' );
			register_nav_menus( array(
			'main_navigation' => esc_html__( 'Main Navigation', THEMENAME ),
			) );
		}
	}
}

/*--------------------------------------------------------------------------------------------------
	Show the menu by location
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_show_nav' ) ) {
	function zn_show_nav($location) {

		if($location == 'header_navigation') 
		{
			$menu_class = 'topnav navRight ';
		}
		elseif($location == 'main_navigation')
		{
			$menu_class = 'sf-menu nav clearfix';
		}

		$menu = cache_wp_nav_menu(
					$location, 
					array( 'theme_location' => $location,
						'link_before'=> '',
						'link_after' => '',
						'container' => '',
						'menu_class'      => $menu_class,
						'fallback_cb' => '',
						'echo' => false)
					);
		echo $menu;
	}
}

/*--------------------------------------------------------------------------------------------------
	MENU CACHING FUNCTION - NEED OPTION IN ADMIN FOR THIS
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'cache_wp_nav_menu' ) ) {
	function cache_wp_nav_menu( $location = false ,$args = false, $prime_cache = false, $skip_cache = false ) {
		
		global $data;

		if ( empty( $data['cache_menu'] ) || (!empty ( $data['cache_menu'] ) &&  $data['cache_menu'] == 'no') ) {

			$args[ 'echo' ] = true;
			return wp_nav_menu( $args );
			
		}

		/* Sanitize function arguments */
		$echo = (bool) $args[ 'echo' ];
		$args[ 'echo' ] = false;

		$queried_object = (int) get_queried_object_id();
		if ( ! $queried_object )
			$queried_object = 0;

		$cache_key = md5( serialize( $args ) . '-' . $queried_object .'-'.$location );

		(array)$saved_menu = get_transient( 'zn_menu_transient' ); 
		$menu = isset($saved_menu[$cache_key]) ? true : false ;
		
		if( $skip_cache === true || false === $menu  ) {
			$saved_menu[$cache_key] = wp_nav_menu( $args );

			set_transient( 'zn_menu_transient', $saved_menu, 86400 );
		}


		if( $echo )
			echo $saved_menu[$cache_key];
		else
			return $saved_menu[$cache_key];
	}

}
function hh_update_nav_menu_objects( $menu_id = null, $menu_data = null ) {
	delete_transient( 'zn_menu_transient' );
}
add_action( 'wp_update_nav_menu', 'hh_update_nav_menu_objects' );

/*--------------------------------------------------------------------------------------------------
	Add specific css class to active item
--------------------------------------------------------------------------------------------------*/
add_filter( 'nav_menu_css_class', 'zn_active_item_classes', 10, 2 );
if ( ! function_exists( 'zn_active_item_classes' ) ) {
	function zn_active_item_classes($classes = array(), $menu_item = false){

		if(in_array('current-menu-item', $menu_item->classes) || in_array('current-menu-ancestor', $menu_item->classes) ){
			$classes[] = 'active';
		}

		return $classes;
	}
}
	



/*--------------------------------------------------------------------------------------------------
	Load video iframe from link
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'get_video_from_link' ) ) {
	function get_video_from_link($string,$css = null , $width = '425px' , $height = '239px' ) {
		
		// Save old string in case no video is provided
		$old_string = $string;
		$video_url = parse_url($string);
		
		if ( $video_url['host'] == 'www.youtube.com' || $video_url['host'] == 'youtube.com' ) {
			
			preg_match('#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#', $string, $matches);
			$string = '<iframe class="'.$css.'" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$matches[0].'?iv_load_policy=3&enablejsapi=0&wmode=transparent&modestbranding=1&rel=0&showinfo=0&feature=player_embedded" frameborder="0" allowfullscreen></iframe>';
		
		}
		else {
			$string = preg_replace('#http://(www\.)?vimeo\.com/([^ ?\n/]+)((\?|/).*?(\n|\s))?#i', '<iframe class="youtube-player '.$css.'" type="text/html" src="http://player.vimeo.com/video/$2" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>', $string);
		}
		
		// If no video link was provided return the full link
		if ( $string != $old_string ) {
			return $string;
		}
		else {
			return;
		}
	}
}

/*--------------------------------------------------------------------------------------------------
	Comments display function
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_comment' ) ) {
	function zn_comment($comment, $args, $depth) {

		$GLOBALS['comment'] = $comment; ?>
		
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
			<div id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
				<?php echo get_avatar($comment,$size='50' ); ?>
				<?php printf(__('<cite class="fn">%s</cite>',THEMENAME), get_comment_author_link()) ?> <?php echo __("says :",THEMENAME); ?> <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
			</div>
			
		<?php if ($comment->comment_approved == '0') : ?>
			<em><?php _e('Your comment is awaiting moderation.',THEMENAME) ?></em>
			<br />
		<?php endif; ?>
		 
		<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars(get_comment_link( $comment->comment_ID )) ?>">
		<?php printf(__('%1$s at %2$s',THEMENAME), get_comment_date(),get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)',THEMENAME),'  ','') ?></div>
		 
		<?php comment_text() ?><div class="zn-separator sep_normal zn-margin-d"></div>
		<?php if($args['max_depth']!=$depth) { ?>
		<?php } ?>
		</div>
		<?php
	}
}
	
/*--------------------------------------------------------------------------------------------------
	Sidebar Functions
--------------------------------------------------------------------------------------------------*/

	if ( function_exists('register_sidebar') ) {
		register_sidebar(array(
			'name' => 'Default Sidebar',
			'id' => 'defaultsidebar',
			'description' => esc_html__('This is the default sidebar. You can choose from the theme\'s options page where the widgets from this sidebar will be shown.',THEMENAME),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle title">',
			'after_title' => '</h3>'
		));
	}

	if ( function_exists('register_sidebar') ) {
		register_sidebar(array(
			'name' => 'Hidden Panel Sidebar',
			'id' => 'hiddenpannelsidebar',
			'description' => esc_html__('This is the sidebar for the hidden panel in the header. You can choose from the theme\'s options page where the widgets from this sidebar will be shown.',THEMENAME),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle title">',
			'after_title' => '</h3>'
		));
	}
	
/*--------------------------------------------------------------------------------------------------
	Hidden pannel Sidebar
--------------------------------------------------------------------------------------------------*/
if ( !function_exists('zn_hidden_pannel') ) {
	function zn_hidden_pannel(){
		
		if ( is_active_sidebar( 'hiddenpannelsidebar' ) ) {
	
			?>
				<div class="support_panel" id="sliding_panel">
					<div class="container">
						<?php if ( !dynamic_sidebar('hiddenpannelsidebar') ) : endif; ;?>
					</div>
				</div><!-- end support panel -->
			<?php
		}
	}
}


/*--------------------------------------------------------------------------------------------------
	Footer sidebars
--------------------------------------------------------------------------------------------------*/

	if ( isset ( $data['footer_row1_widget_positions'] ) && !empty ( $data['footer_row1_widget_positions'] ) ) {

			
		$f_row1 = key( json_decode ( stripslashes ( $data['footer_row1_widget_positions'] ) ) );

		if ( function_exists('register_sidebars') ) {	

			if ( $f_row1 > 1 ) {

				$args = array(
					'name'          => 'Footer row 1 - widget %d',
					'id'            => "znfooter",
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title' => '<h3 class="widgettitle title m_title">',
					'after_title' => '</h3>' ); 

				register_sidebars($f_row1 ,$args);
			}
			else{
				$args = array(
					'name'          => 'Footer row 1 - widget 1',
					'id'            => "znfooter",
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title' => '<h3 class="widgettitle title m_title">',
					'after_title' => '</h3>' ); 
				register_sidebars( 1,$args );
			}
			
		}

	}
	
	if ( isset ( $data['footer_row2_widget_positions'] ) && !empty ( $data['footer_row2_widget_positions'] ) ) {

		$f_row1 = key( json_decode ( stripslashes ( $data['footer_row2_widget_positions'] ) ) );

		if ( function_exists('register_sidebars') ) {	

			if ( $f_row1 > 1 ) {

				$args = array(
					'name'          => 'Footer row 2 - widget %d',
					'id'            => "znfooter",
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title' => '<h3 class="widgettitle title m_title">',
					'after_title' => '</h3>' ); 

				register_sidebars($f_row1 ,$args);
			}
			else{
				$args = array(
					'name'          => 'Footer row 2 - widget 1',
					'id'            => "znfooter",
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title' => '<h3 class="widgettitle title m_title">',
					'after_title' => '</h3>' ); 
				register_sidebars( 1,$args );
			}
			
		}

	}
	
/*--------------------------------------------------------------------------------------------------
	Dynamic Sidebars Function
--------------------------------------------------------------------------------------------------*/
	
	if ( isset ( $data['sidebar_generator'] ) && is_array ($data['sidebar_generator']) ) {
	
		$sidebars = $data['sidebar_generator'];
	
		foreach ($data['sidebar_generator'] as $sidebar) {
			if ( $sidebar['sidebar_name'] ) {
				register_sidebar(array(
					'name'=>$sidebar['sidebar_name'],
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title' => '<h3 class="widgettitle title">',
					'after_title' => '</h3>'
				));
			}
		}
	}





/*--------------------------------------------------------------------------------------------------
	Modify the search form
--------------------------------------------------------------------------------------------------*/

	add_filter('get_search_form', 'zn_search_form');
if ( ! function_exists( 'zn_search_form' ) ) {	 
	function zn_search_form($form) {
	$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
		<div class="zn_search_container">
		<input title="' . __('Search for...',THEMENAME) . '" class="zn-inline-text" type="text" value="' . get_search_query() . '" name="s" id="s" />
		<input class="square_button search_submit" type="submit" id="searchsubmit" value="a" /><div class="clear"></div>
		</div>
		</form>';


		$form =	'			<div class="search">';
		$form .=	'				<form id="searchform" action="' . home_url( '/' ) . '" method="get">';
		$form .=	'					<input id="s" name="s" maxlength="20" class="inputbox" type="text" size="20" value="'. __( 'SEARCH ...', THEMENAME ).'" onblur="if (this.value==\'\') this.value=\''. __( 'SEARCH ...', THEMENAME ).'\';" onfocus="if (this.value==\''. __( 'SEARCH ...', THEMENAME ).'\') this.value=\'\';" />';
		$form .=	'					<input type="submit" id="searchsubmit" value="go" class="icon-search"/>';
		$form .=	'				</form>';
		$form .=	'			</div>';
 
		
		return $form;
	}
}

// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');
if ( ! function_exists( 'woocommerce_header_add_to_cart_fragment' ) ) {	
	function woocommerce_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		
		ob_start();
		
		?>
		<span class="cart_details"><?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count,THEMENAME), $woocommerce->cart->cart_contents_count);?> <?php _e("Total of",THEMENAME);?> <?php echo $woocommerce->cart->get_cart_total(); ?> <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart',THEMENAME); ?>" class="checkout"><?php _e("Checkout",THEMENAME);?> <span class="icon-chevron-right"></span></a></span>
		<?php
		
		$fragments['span.cart_details'] = ob_get_clean();
		
		return $fragments;
		
	}
}



/*--------------------------------------------------------------------------------------------------
	GET current page URL
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'current_page_url' ) ) {	
	function current_page_url() {
		$pageURL = 'http';
		if( isset($_SERVER["HTTPS"]) ) {
			if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
}
	
/*--------------------------------------------------------------------------------------------------
	Remove rel attribute from the category list for HTML validation purposes
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'remove_category_rel' ) ) {	
	function remove_category_rel($result) {
		$result = str_replace('rel="category tag"', '', $result);
		$result = str_replace('rel="category"', '', $result);
		return $result;
	}
}
	add_filter('the_category', 'remove_category_rel');
	add_filter('wp_list_categories', 'remove_category_rel');

	
/*--------------------------------------------------------------------------------------------------
	Blog Excerpt Functions
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'custom_excerpt_length' ) ) {	
	function custom_excerpt_length( $length ) {
		
		global $data;
		
		if ( !empty ( $data['blog_excerpt_limit'] ) ) { 
			$excerpt = $data['blog_excerpt_limit']; 
		}
		else {
			$excerpt = 55;
		}
		
		return $excerpt;
	}
}

if ( ! function_exists( 'clear_excerpt_more' ) ) {	
	function clear_excerpt_more( $more ) {
		return '';
	}
}

	add_filter('excerpt_more', 'clear_excerpt_more');
	add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

if ( ! function_exists( 'zn_limit_content' ) ) {	
	function zn_limit_content($string, $word_limit)
	{
		$words = explode(" ",$string);
		return implode(" ",array_splice($words,0,$word_limit));
	}
}


/*--------------------------------------------------------------------------------------------------
	Pagination Functions
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_pagination' ) ) {	
	function zn_pagination($pages = '', $range = 2)
	{  
		$showitems = ($range * 2)+1;  

		global $paged;
		if(empty($paged)) $paged = 1;

		if($pages == '')
		{
			global $wp_query;
			$pages = $wp_query->max_num_pages;
			
			if(!$pages)	{	$pages = 1;	}
			
		}   

		if(1 != $pages)
		{
		//__( 'Published in', THEMENAME )
			echo "<div class='pagination'>";
			echo '<ul>';
			
			if(1 == $paged) {
				echo '<li class="pagination-start"><span class="pagenav">'.__( 'Start', THEMENAME ).'</span></li>';
				echo '<li class="pagination-prev"><span class="pagenav">'.__( 'Prev', THEMENAME ).'</span></li>';
			}
			else {
				echo '<li class="pagination-start"><a href="'.get_pagenum_link(1).'">'.__( 'Start', THEMENAME ).'</a></li>';
				echo '<li class="pagination-prev"><a href="'.get_pagenum_link($paged-1).'">'.__( 'Prev', THEMENAME ).'</a></li>';
			}

			for ($i=1; $i <= $pages; $i++)
			{
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
				{
					//echo ($paged == $i)? "<span class='current zn_default_color_active'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive zn_default_color' >".$i."</a>";
					echo ($paged == $i)? '<li><span class="pagenav">'.$i.'</span></li>':'<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
				}
			}
			
			if($paged < $pages ) {
				echo '<li class="pagination-next"><a href="'.get_pagenum_link($paged+1).'">'.__( 'Next', THEMENAME ).'</a></li>';
				echo '<li class="pagination-end"><a href="'.get_pagenum_link($pages).'">'.__( 'End', THEMENAME ).'</a></li>';
			}
			else {
				echo '<li class="pagination-next"><span class="pagenav">'.__( 'Next', THEMENAME ).'</span></li>';
				echo '<li class="pagination-end"><span class="pagenav">'.__( 'End', THEMENAME ).'</span></li>';
			}
			
			echo '</ul>';
			echo '<div class="clear"></div>';
			echo ''.__( 'Page', THEMENAME ).' '.$paged.' '.__( 'of', THEMENAME ).' '.$pages.'';
			echo "</div>\n";
		}
	}
}

/*--------------------------------------------------------------------------------------------------
	Portfolio Post Type
--------------------------------------------------------------------------------------------------*/

	add_action('init','zn_portfolio_post_type');
if ( ! function_exists( 'zn_portfolio_post_type' ) ) {		
	function zn_portfolio_post_type() {
	
		$permalinks = get_option( 'zn_permalinks' );

		if (!empty( $permalinks['port_item'] )) {
			$slug = array('slug'=>$permalinks['port_item']);
		}
		else {
			$slug = true;
		}

		$labels = array(
		    'name' => 'Portfolios',
		    'singular_name' => 'Portfolio Item',
		    'add_new' => 'Add New Portfolio Item',
		    'all_items' => 'All Portfolio Items',
		    'add_new_item' => 'Add New Portfolio',
		    'edit_item' => 'Edit Portfolio Item',
		    'new_item' => 'New Portfolio Item',
		    'view_item' => 'View Portfolio Item',
		    'search_items' => 'Search Portfolio Items',
		    'not_found' =>  'No Portfolio Items found',
		    'not_found_in_trash' => 'No Portfolio Items found in trash',
		    'parent_item_colon' => 'Parent Portfolio:',
		    'menu_name' => 'Portfolio Items'
		);
		
		$args = array(
			'labels' => $labels,
			'description' => "",
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_nav_menus' => true, 
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'menu_position' => 100,
			'menu_icon' => ADMIN_IMAGES_DIR.'/portfolio.png',
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title','editor'),
			'has_archive' => true,
			'rewrite' => $slug,
			'query_var' => true,
			'can_export' => true
		); 
		
		register_post_type('portfolio',$args);
	}
}	
	// Flush Rewrite rules
if ( ! function_exists( 'zn_rewrite_flush' ) ) {	
	function zn_rewrite_flush() {
		flush_rewrite_rules();
	}
}
	
	add_action( 'after_switch_theme', 'zn_rewrite_flush' );	

/*--------------------------------------------------------------------------------------------------
	Portfolio Post Taxonomy
--------------------------------------------------------------------------------------------------*/

	add_action( 'init', 'zn_portfolio_category', 0 );
if ( ! function_exists( 'zn_portfolio_category' ) ) {	
	function zn_portfolio_category() 
	{

		$permalinks = get_option( 'zn_permalinks' );

		if (!empty( $permalinks['port_tax'] )) {
			$slug = array('slug'=>$permalinks['port_tax']);
		}
		else {
			$slug = true;
		}


	  // Add new taxonomy, make it hierarchical (like categories)
	  $labels = array(
		'name' => __( 'Categories',THEMENAME),
		'singular_name' => __( 'Category',THEMENAME ),
		'search_items' =>  __( 'Search Categories',THEMENAME ),
		'all_items' => __( 'All Categories',THEMENAME ),
		'parent_item' => __( 'Parent Category',THEMENAME ),
		'parent_item_colon' => __( 'Parent Category:',THEMENAME ),
		'edit_item' => __( 'Edit Category',THEMENAME ), 
		'update_item' => __( 'Update Category',THEMENAME ),
		'add_new_item' => __( 'Add New Category',THEMENAME ),
		'new_item_name' => __( 'New Category Name',THEMENAME ),
		'menu_name' => __( 'Portfolio categories',THEMENAME ),
	  ); 	

	  register_taxonomy('project_category','portfolio', array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => $slug,
	  ));
	}
}
/*--------------------------------------------------------------------------------------------------
	Breadcrumbs
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_breadcrumbs' ) ) {					
	function zn_breadcrumbs() {

		$delimiter = '&raquo;'; 
		$home = __('Home',THEMENAME); 
		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$before = '<span class="current">'; // tag before the current crumb
		$after = '</span>'; // tag after the current crumb

		if ( get_option('woocommerce_prepend_shop_page_to_urls') == "yes" && woocommerce_get_page_id( 'shop' ) && get_option( 'page_on_front' ) !== woocommerce_get_page_id( 'shop' ) )
			$prepend =  '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_permalink( woocommerce_get_page_id('shop') ) . '">' . get_the_title( woocommerce_get_page_id('shop') ) . '</a></li>';
		else
			$prepend = '';
		
		
		global $post, $data, $wp_query;
		$homeLink = home_url();

		if ( is_front_page() ) {
 
			echo '<ul xmlns:v="http://rdf.data-vocabulary.org/#" class="breadcrumbs fixclear"><li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . $homeLink . '">' . $home . '</a></li></ul>';
 
		}
		elseif ( is_home() ) {
		
			if ( function_exists ('icl_t') )
			{
				$title = icl_t(THEMENAME, 'Archive Page Title', do_shortcode(stripslashes($data['archive_page_title'])));
			}
			else
			{
				$title = do_shortcode(stripslashes($data['archive_page_title']));
			}
			
		
			echo '<ul xmlns:v="http://rdf.data-vocabulary.org/#" class="breadcrumbs fixclear"><li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . $homeLink . '">' . $home . '</a></li><li>'.$title.'</li></ul>';
		}
		else {
 
			echo '<ul xmlns:v="http://rdf.data-vocabulary.org/#" class="breadcrumbs fixclear"><li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . $homeLink . '">' . $home . '</a></li>';
 
			if ( is_category() ) {
			
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) $cats = get_category_parents($thisCat->parent, TRUE, '|zn_preg|');

				$cats = get_category_parents($thisCat, TRUE, '|zn_preg|');
				
				$cats = explode ( '|zn_preg|',$cats );
				
				foreach ( $cats as $s_cat ) {
					if ( !empty ( $s_cat ) ) {
						$s_cat = str_replace ( '<a', '<a rel="v:url" property="v:title" ' , $s_cat );
						echo '<li typeof="v:Breadcrumb">'.$s_cat.'</li>';
					}
				}
				
				echo '<li>'. __("Archive from category ",THEMENAME).'"' . single_cat_title('', false) . '"</li>';
		 
			} 
			elseif ( is_tax('product_cat') ) {
			
			echo $prepend;
			
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

			$parents = array();
			$parent = $term->parent;
			while ( $parent ) {
				$parents[] = $parent;
				$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
				$parent = $new_parent->parent;
			}

			if ( ! empty( $parents ) ) {
				$parents = array_reverse( $parents );
				foreach ( $parents as $parent ) {
					$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
					echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title"  href="' . get_term_link( $item->slug, 'product_cat' ) . '">' . $item->name . '</a></li>';
				}
			}

			$queried_object = $wp_query->get_queried_object();
			echo '<li>'. $queried_object->name . '</li>';

			}
			elseif ( is_tax('project_category') || is_post_type_archive('portfolio') ) {

				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

				if ( !empty($term->parent) ) {

					$parents = array();
					$parent = $term->parent;
					while ( $parent ) {
						$parents[] = $parent;
						$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
						$parent = $new_parent->parent;
					}

					if ( ! empty( $parents ) ) {
						$parents = array_reverse( $parents );
						foreach ( $parents as $parent ) {
							$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
							echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title"  href="' . get_term_link( $item->slug, 'product_cat' ) . '">' . $item->name . '</a></li>';
						}
					}

				}


				$queried_object = $wp_query->get_queried_object();
				echo '<li>'. $queried_object->name . '</li>';

			}
			elseif ( is_tax('documentation_category') ) {

				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

				$parents = array();
				$parent = $term->parent;
				while ( $parent ) {
					$parents[] = $parent;
					$new_parent = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ) );
					$parent = $new_parent->parent;
				}

				if ( ! empty( $parents ) ) {
					$parents = array_reverse( $parents );
					foreach ( $parents as $parent ) {
						$item = get_term_by( 'id', $parent, get_query_var( 'taxonomy' ));
						echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title"  href="' . get_term_link( $item->slug, 'documentation_category' ) . '">' . $item->name . '</a></li>';
					}
				}

				$queried_object = $wp_query->get_queried_object();
				echo '<li>'. $queried_object->name . '</li>';

			}
			elseif ( is_search() ) {
				echo '<li>'. __("Search results for ",THEMENAME).'"' . get_search_query() . '"</li>';
 
			} elseif ( is_day() ) {
				echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title"  href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
				echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title"  href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a></li>';
				echo '<li>'.get_the_time('d').'</li>';
 
			} elseif ( is_month() ) {
				echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title"  href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a></li>';
				echo '<li>'.get_the_time('F').'</li>';

			} elseif ( is_year() ) {
				echo '<li>'.get_the_time('Y').'</li>';
			}
			elseif ( is_post_type_archive('product') && get_option('page_on_front') !== woocommerce_get_page_id('shop') ) {

					$_name = woocommerce_get_page_id( 'shop' ) ? get_the_title( woocommerce_get_page_id( 'shop' ) ) : ucwords( get_option( 'woocommerce_shop_slug' ) );

					if ( is_search() ) {

						echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_post_type_archive_link('product') . '">' . $_name . '</a></li><li>'. __('Search results for &ldquo;', THEMENAME) . get_search_query() . '</li>';

					} elseif ( is_paged() ) {

						echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_post_type_archive_link('product') . '">' . $_name . '</a></li>';

					} else {
					
						echo '<li>' . $_name . '</li>';

					}

			}
			elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() == 'portfolio' ) {
					
					// Show category name
					$cats = get_the_term_list($post->ID, 'project_category', ' ', '|zn_preg|', '|zn_preg|');
					$cats = explode ( '|zn_preg|',$cats );
					
					if ( !empty ( $cats['0'] ) ) {
						
							$s_cat = str_replace ( '<a', '<a rel="v:url" property="v:title" ' , $cats['0'] );
							echo '<li typeof="v:Breadcrumb">'.$s_cat.'</li>';
					} 
					
					// Show post name
					echo '<li>' . get_the_title() . '</li>';
				}
				elseif ( get_post_type() == 'product' ) {

					echo $prepend;

					if ( $terms = wp_get_object_terms( $post->ID, 'product_cat' ) ) {
						$term = current( $terms );
						$parents = array();
						$parent = $term->parent;
						
						while ( $parent ) {
							$parents[] = $parent;
							$new_parent = get_term_by( 'id', $parent, 'product_cat' );
							$parent = $new_parent->parent;
						}
						
						if ( ! empty( $parents ) ) {
							$parents = array_reverse($parents);
							foreach ( $parents as $parent ) {
								$item = get_term_by( 'id', $parent, 'product_cat');
								echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_term_link( $item->slug, 'product_cat' ) . '">' . $item->name . '</a></li>';
							}
						}
						
						echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_term_link( $term->slug, 'product_cat' ) . '">' . $term->name . '</a></li>';
					
					}

					echo '<li>'. get_the_title() . '</li>';

				}
				elseif ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					//print_r($slug);
					echo '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></li>';
					if ($showCurrent == 1) echo '<li>'.get_the_title().'</li>';
				} else {

					
					// Show category name
					$cat = get_the_category(); $cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, '|zn_preg|');
					
					$cats = explode ( '|zn_preg|',$cats );
					foreach ( $cats as $s_cat ) {
						if ( !empty ( $s_cat ) ) {
							$s_cat = str_replace ( '<a', '<a rel="v:url" property="v:title" ' , $s_cat );
							echo '<li typeof="v:Breadcrumb">'.$s_cat.'</li>';
						}
					}
					// Show post name
					echo '<li>' . get_the_title() . '</li>';
				}
 
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object(get_post_type());
				if ( !empty ( $post_type->labels->singular_name ) ) {
					echo '<li>'.$post_type->labels->singular_name . '</li>';
				}
 
			} elseif ( is_attachment() ) {
				$parent = get_post($post->post_parent);
				//print_r($parent);

					$cat = get_the_category($parent->ID); 
				if ( !empty($cat) ) {
					$cat = $cat[0];
					echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
					echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>';
					echo '<li>' . get_the_title() .'</li>';
				}
				else {
					echo '<li>' . get_the_title() .'</li>';
				}
 
			} elseif ( is_page() && !is_subpage() ) {
				if ($showCurrent == 1) echo '<li>'. get_the_title() . '</li>';
 
			} elseif ( is_page() && is_subpage() ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<li typeof="v:Breadcrumb"><a rel="v:url" property="v:title" href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a></li>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					//if ($i != count($breadcrumbs)-1) echo ' ' . $delimiter . ' ';
				}
				if ($showCurrent == 1) echo  '<li>' . get_the_title() . '</li>';
 
			} elseif ( is_tag() ) {
				echo '<li>'. __("Posts tagged ",THEMENAME).'"'.single_tag_title('', false).'"</li>';
				
 
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				echo '<li>'. __("Articles posted by ",THEMENAME) . $userdata->display_name .'</li>';
 
			} elseif ( is_404() ) {
				echo '<li>'. __("Error 404 ",THEMENAME) .'</li>';
			}
 
			if ( get_query_var('paged') ) {
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
				echo '<li>'.__('Page',THEMENAME) . ' ' . get_query_var('paged').'</li>';
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
			}
 
			echo '</ul>';
 
		}
	} 
}
/*--------------------------------------------------------------------------------------------------
	Check if this is a subpage
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'is_subpage' ) ) {			
	function is_subpage() {
		global $post;                              // load details about this page

		if ( is_page() && $post->post_parent ) {   // test to see if the page has a parent
			return $post->post_parent;             // return the ID of the parent post

		} else {                                   // there is no parent so ...
			return false;                          // ... the answer to the question is false
		}
	}
}


/*--------------------------------------------------------------------------------------------------
	Login Form
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_login_form' ) ) {	
	function zn_login_form () {
		// CHECK IF USER IS LOGGED IN
		global $data, $wp;
		if ( is_user_logged_in() || !$data['head_show_login'] ) {
			return '';
		}


		$current_url = home_url( $wp->request ) ;

		?>
		
	<div class="login_register_stuff hide"><!-- Login/Register Modal forms - hidded by default to be opened through modal -->
		<div id="login_panel">
			<div class="inner-container login-panel">
			 <?php // wp_login_form(  ); ?> 
				<h3 class="m_title"><?php _e("SIGN IN YOUR ACCOUNT TO HAVE ACCESS TO DIFFERENT FEATURES",THEMENAME);?></h3>
					
				<form id="login_form" name="login_form" method="post" class="zn_form_login" action="<?php echo site_url('wp-login.php', 'login_post') ?>">
				
					<?php if( get_option('users_can_register') ) { ?>
						<a href="#" class="create_account" onClick="ppOpen('#register_panel', '280');"><?php _e("CREATE ACCOUNT",THEMENAME);?></a>
					<?php } ?>
					
					<input type="text" id="username" name="log" class="inputbox" placeholder="<?php _e("Username",THEMENAME);?>">
					<input type="password" id="password" name="pwd" class="inputbox" placeholder="<?php _e("Password",THEMENAME);?>">
					<?php do_action('login_form');?>
					<label class="zn_remember"><input type="checkbox" name="rememberme" id="rememberme" value="forever"><?php _e(" Remember Me",THEMENAME);?></label>
					<input type="submit" id="login" name="submit_button" class="zn_sub_button" value="<?php _e("LOG IN",THEMENAME);?>">
					<input type="hidden" value="login" class="" name="zn_form_action">
					<input type="hidden" value="zn_do_login" class="" name="action">
					<input type="hidden" value="<?php echo $current_url; ?>" class="zn_login_redirect" name="submit">
					<div class="links"><a href="#" onClick="ppOpen('#forgot_panel', '350');"><?php _e("FORGOT YOUR PASSWORD?",THEMENAME);?></a></div>
				</form>

			</div>
		</div><!-- end login panel -->

		<?php if( get_option('users_can_register') ) { ?>
			<div id="register_panel">
				<div class="inner-container register-panel">
					<h3 class="m_title"><?php _e("CREATE ACCOUNT",THEMENAME);?></h3>
					<form id="register_form" name="login_form" method="post" class="zn_form_login" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>">
						<p>
							<input type="text" id="reg-username" name="user_login" class="inputbox" placeholder="<?php _e("Username",THEMENAME);?>">
						</p>
						<p>
							<input type="text" id="reg-email" name="user_email" class="inputbox" placeholder="<?php _e("Your email",THEMENAME);?>">
						</p>
						<p>
							<input type="text" id="reg-pass" name="user_password" class="inputbox" placeholder="<?php _e("Your password",THEMENAME);?>">
						</p>
						<p>
							<input type="text" id="reg-pass" name="user_password2" class="inputbox" placeholder="<?php _e("Verify password",THEMENAME);?>">
						</p>
						<p>
							<input type="submit" id="signup" name="submit" class="zn_sub_button" value="<?php _e("CREATE MY ACCOUNT",THEMENAME);?>">
						</p>
						<input type="hidden" value="register" class="" name="zn_form_action">
						<input type="hidden" value="zn_do_login" class="" name="action">
						<input type="hidden" value="<?php echo $current_url; ?>" class="zn_login_redirect" name="submit">
						<div class="links"><a href="#" onClick="ppOpen('#login_panel', '800');"><?php _e("ALREADY HAVE AN ACCOUNT?",THEMENAME);?></a></div>
					</form>
					
				</div>
			</div><!-- end register panel -->
		<?php } ?>
		
		<div id="forgot_panel">
			<div class="inner-container forgot-panel">
				<h3 class="m_title"><?php _e("FORGOT YOUR DETAILS?",THEMENAME);?></h3>
				<form id="forgot_form" name="login_form" method="post" class="zn_form_lost_pass" action="<?php echo site_url('wp-login.php?action=lostpassword', 'login_post') ?>">
					<p>
						<input type="text" id="forgot-email" name="user_login" class="inputbox" placeholder="<?php _e("Username or E-mail",THEMENAME);?>">
					</p>
					<p>
						<input type="submit" id="recover" name="submit" class="zn_sub_button" value="<?php _e("SEND MY DETAILS!",THEMENAME);?>">
					</p>
					<div class="links"><a href="#" onClick="ppOpen('#login_panel', '800');"><?php _e("AAH, WAIT, I REMEMBER NOW!",THEMENAME);?></a></div>
				</form>
				
			</div>
		</div><!-- end register panel -->
	</div><!-- end login register stuff -->
		
		<?php
		
		
	}
}	
	

	
/*--------------------------------------------------------------------------------------------------
	Login Form - Stop redirecting if ajax is used
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_stop_redirecting' ) ) {	
    function zn_stop_redirecting($redirect_to, $request,$user) {
		if(!empty ( $_POST['ajax_login'] ) ) {
			return;
		}
		else {
			return $redirect_to;
		}
    }
}
	add_filter("login_redirect", "zn_stop_redirecting", 10, 3);
	
/*--------------------------------------------------------------------------------------------------
	New User registration EMAIL
--------------------------------------------------------------------------------------------------*/
/*
if ( !function_exists('wp_new_user_notification') ) { 
	function wp_new_user_notification($user_id, $plaintext_pass) {
		$user = new WP_User($user_id);

		$user_login = stripslashes($user->user_login);
		$user_email = stripslashes($user->user_email);
		
		$email_subject = "Welcome to ".get_bloginfo('name')." ".$user_login."!";
		
		ob_start();

		include("email_header.php");
		
		?>
		
		<p><?php _e("A very special welcome to you, ",THEMENAME);?><?php echo $user_login ?>. <?php _e("Thank you for joining , ",THEMENAME); echo get_bloginfo('name');?></p>
		
		<p>
			<?php _e("Your password is ",THEMENAME);?> <strong style="color:orange"><?php echo $plaintext_pass ?></strong> <br>
			<?php _e("Please keep it secret and keep it safe!",THEMENAME);?>
		</p>
		
		<p>
			<?php _e("We hope you enjoy your stay at MyAwesomeSite.com. If you have any problems, questions, opinions, praise, 
			comments, suggestions, please feel free to contact us at any time",THEMENAME);?>
		</p>
		
		
		<?php
		include("email_footer.php");
		
		$message = ob_get_contents();
		ob_end_clean();

		wp_mail($user_email, $email_subject, $message);

	}
}

*/


/*--------------------------------------------------------------------------------------------------
	Facebook Open Graph functions
--------------------------------------------------------------------------------------------------*/
/* DEFAULT TITLE*/
if ( ! function_exists( 'zn_opengraph_default_title' ) ) {	
	function zn_opengraph_default_title() {

		global $post;
		
		if (is_home() || is_front_page() ) {
			$title = get_bloginfo( 'name' );
		} else {
			$title = get_the_title();
		}
		  
		  echo $title;
	}	
}
if ( ! function_exists( 'zn_opengraph_default_type' ) ) {	
	function zn_opengraph_default_type() {

		if ( is_singular( array('post', 'page', 'aside', 'status') ) ) {
			$type = 'article';
		} else if ( is_author() ) {
			$type = 'profile';
		} else {
			$type = 'blog';
		}
	  
	  echo $type;
	}
}

if ( ! function_exists( 'opengraph_default_image' ) ) {
	function opengraph_default_image(  ) {
		$image = array();
		 if ( is_singular() ) {
			global $post;

			$images = array();

			if ( has_post_thumbnail($post->ID) ) {
				$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium');
				$images[] = $thumbnail_src['0'];
			}

			$content = $post->post_content;
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
		
			if ( $output !== FALSE ) {
		
				foreach ( $matches[1] as $match ) {
					// If the image path is relative, add the site url to the beginning
					if ( ! preg_match('/^https?:\/\//', $match ) ) {
						// Remove any starting slash with ltrim() and add one to the end of site_url()
						$match = site_url( '/' ) . ltrim( $match, '/' );
					}
					$images[] = $match;
				}

			}

			foreach( $images as $image ) {
				
				if ($image) {
					//$image[] = $thumbnail[0];
					echo '<meta property="og:image" content="'.$image.'"/>';
				}
			}
		}
	}
}
	




/*--------------------------------------------------------------------------------------------------
	CHECK FOR IS COMING SOON PAGE
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_coming_soon_page' ) ) {
	function zn_coming_soon_page() {
			
		global $data,$pagenow;		
	
		if ( $data['cs_enable'] == 'yes' && !is_user_logged_in() && !is_admin() && $pagenow != 'wp-login.php'  )
		{
			get_template_part('coming_soon_page');
			exit();
		}
		
	}
}

add_action('init', 'zn_coming_soon_page',26);


/*--------------------------------------------------------------------------------------------------
	Add Ajax status to page 
--------------------------------------------------------------------------------------------------*/	

if ( ! function_exists( 'zn_has_ajax' ) ) {
	function zn_has_ajax() {
		global $has_ajax;
		if ( isset ($_POST['cform_submit']) ) {


			global $post;
			global $data;
			
			if ( $post ) {
			
				$meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
				$meta_fields = maybe_unserialize( $meta_fields );

				// All the page builder areas
				$areas = array ( 'header_area' , 'action_box_area' , 'content_main_area', 'content_grey_area' , 'content_bottom_area');
				$metas = array();
				
				foreach ( $areas as $area ) {
					if ( isset ( $meta_fields[$area] ) ) {
						$metas = array_merge ( $metas , $meta_fields[$area] );
					}
				}
					

				foreach ($metas as $options ) 
				{
				// CONTACT FORM
				if  ( $options['dynamic_element_type'] == '_c_form' ) {
					if ( isset ($_POST['cform_submit']) ) {
				
						$body = '';
						$name = '';
						$email = '';
						$headers = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
						
						foreach ( $options['zn_cf_fields'] as $field ) {
							
							$field_name = preg_replace('~[\W\s]~', '_', $field['zn_cf_name']);
							// Compoese the mail
							if ( isset ( $_POST[$field_name] ) ) { 
							
								$body .= $field['zn_cf_name'] .' : '.$_POST[$field_name] ."<br />" ;
								if ( $field['zn_cf_f_email'] ) {
									$email = $_POST[$field_name];
								}
								if ( $field['zn_cf_f_name'] ) {
									$name = $_POST[$field_name];
								}
							}

						}
						
							if ( !empty ( $name ) ) {
								$headers .= "From: \"" . $name . "\" \r\n";
							}
							if ( !empty ( $email ) ) {
								$headers .= "Reply-To: " .  $email . "\r\n";
							}


							if ( mail ($options['zn_cf_email_address'],$options['zn_cf_button_subject'],$body,$headers) ){
								echo 'sent';
							}
							else {
								echo 'mail_not_sent';
							}
							
					}
				}
	
				}

			}

			
			//wp_enqueue_scripts();
			exit();
		}
	}
}
	add_action('get_header', 'zn_has_ajax');
	
	
/*--------------------------------------------------------------------------------------------------
*
	THEME UPDATE 1.1 
	1.1 - Added new menu position for the header
*
--------------------------------------------------------------------------------------------------*/

/*--------------------------------------------------------------------------------------------------
	New TOP NAVIGATION
--------------------------------------------------------------------------------------------------*/
	add_action( 'init', 'zn_register_menu2' );
if ( ! function_exists( 'zn_register_menu2' ) ) {
	function zn_register_menu2() {
		if ( function_exists('wp_nav_menu') ) {
			add_theme_support( 'nav-menus' );
			register_nav_menus( array(
				'header_navigation' => esc_html__( 'Header Navigation', THEMENAME ),
			) );
		}
	}
}

/*--------------------------------------------------------------------------------------------------
	Check for boxed layout or full
--------------------------------------------------------------------------------------------------*/	
	
	// Add specific CSS class by filter
	add_filter('body_class','zn_body_class_names');
if ( ! function_exists( 'zn_body_class_names' ) ) {
	function zn_body_class_names($classes) {
		global $data;
		if ( (!empty ( $data['zn_boxed_layout'] ) && $data['zn_boxed_layout'] == 'yes' ) || ( is_front_page() && !empty( $data['zn_home_boxed_layout'] ) && $data['zn_home_boxed_layout'] =='yes' )) {

			$classes[] = 'boxed';
			
		}
		
		if ( is_front_page() && !empty( $data['zn_home_boxed_layout'] ) && $data['zn_home_boxed_layout'] =='no' ){
			$classes = array_diff($classes, array("boxed"));
		}
		
		if ($data['zn_width'] == '1170' &&  $data['zn_responsive'] == 'yes') {

			$classes[] = 'res1170';
		
		}
		if ($data['zn_width'] == '960') {

			$classes[] = 'res960';
		
		}

		if ( !empty($data['zn_slider_header']) && $data['zn_slider_header'] == 'yes')  {
			$classes[] = 'slider_after_header';
		}

		return $classes;
	}
}

/*--------------------------------------------------------------------------------------------------
	Add AFTER BODY ACTIONS
--------------------------------------------------------------------------------------------------*/	
add_action( 'zn_after_body', 'zn_add_page_loading',10 );
if ( !function_exists('zn_add_page_loading') ) {
	function zn_add_page_loading(){
		echo zn_page_loading();
	}
}

/* SUPPORT PANEL */
add_action( 'zn_after_body', 'zn_add_hidden_panel',10 );
if ( !function_exists('zn_add_hidden_panel') ) {
	function zn_add_hidden_panel(){
		echo zn_hidden_pannel();
	}
}

/* LOGIN FORM */
add_action( 'zn_after_body', 'zn_add_login_form',10 );
if ( !function_exists('zn_add_login_form') ) {
	function zn_add_login_form(){
		echo zn_login_form();
	}
}

/* OPEN GRAPH */
add_action( 'zn_after_body', 'zn_add_open_graph',10 );
if ( !function_exists('zn_add_open_graph') ) {
	function zn_add_open_graph(){
		echo zn_f_o_g();
	}
}

/*--------------------------------------------------------------------------------------------------
	Add MENU to top area
--------------------------------------------------------------------------------------------------*/	
add_action( 'zn_head_right_area', 'zn_add_navigation',40 );
if ( !function_exists('zn_add_navigation') ) {
	function zn_add_navigation(){
		echo  zn_show_nav('header_navigation');
	}
}

/*--------------------------------------------------------------------------------------------------
	Add Right content to action
--------------------------------------------------------------------------------------------------*/	
add_action( 'zn_head_right_area', 'zn_hidden_pannel_link',30 );
if ( !function_exists('zn_hidden_pannel_link') ) {
	function zn_hidden_pannel_link(){
		
		if ( is_active_sidebar( 'hiddenpannelsidebar' ) ) {
	
			?>
			<ul class="topnav navRight">
				<li><a href="#" id="open_sliding_panel">
						<span class="icon-remove-circle icon-white"></span> <?php _e('SUPPORT',THEMENAME);?>
					</a>
				</li>
			</ul>
			<?php
		}
	}
}

/*--------------------------------------------------------------------------------------------------
	Login Form - Login/logout text
--------------------------------------------------------------------------------------------------*/
add_action( 'zn_head_right_area', 'zn_login_text',20 );

if ( ! function_exists( 'zn_login_text' ) ) {	
	function zn_login_text() {
		global $data;
		// CHECK IF OPTION IS ENABLED
		if ( !$data['head_show_login'] ) {
			return '';
		}
		if ( is_user_logged_in() ) {
			 echo '<ul class="topnav navRight"><li><a href="'.wp_logout_url( HOME_URL ).'">'.__("LOGOUT",THEMENAME).'</a></li></ul>';
		}
		else {
			echo '<ul class="topnav navRight"><li><a href="#login_panel" data-rel="prettyPhoto[login_panel]">'.__("LOGIN",THEMENAME).'</a></li></ul>';
		}
	}
}	

/*--------------------------------------------------------------------------------------------------
	SHOW HEADER SOCIAL ICONS
--------------------------------------------------------------------------------------------------*/
add_action( 'zn_head_right_area', 'zn_header_social_icons',10 );

if ( ! function_exists( 'zn_header_social_icons' ) ) {	
	function zn_header_social_icons() {
		global $data;
			if ( isset( $data['header_social_icons'] ) && is_array( $data['header_social_icons'] ) && !empty ( $data['header_social_icons'][0]['header_social_icon'] ) ) {
			
				$icon_class = '';
				
				
				if( $data['header_which_icons_set'] == 'colored' ) { 
					$icon_class = 'colored';
				}
				
				echo '<ul class="social-icons '.$icon_class.' topnav navRight">';
										
					foreach ( $data['header_social_icons'] as $key=>$icon ){
					
						$link = '';
						$target = '';
					
						if ( isset ( $icon['header_social_link'] ) && is_array ( $icon['header_social_link'] )) {
							$link = $icon['header_social_link']['url'];
							$target = 'target="'.$icon['header_social_link']['target'].'"';
						}
						
					
						echo '<li class="'.$icon['header_social_icon'].'"><a href="'.$link.'" '.$target.'>'.$icon['header_social_title'].'</a></li>';
					}
					
				echo '</ul>';
				
			}
	}
}

/*--------------------------------------------------------------------------------------------------
	Add WOOCOMMERCE CART LINK
--------------------------------------------------------------------------------------------------*/	
add_action( 'zn_head_right_area', 'zn_woocomerce_cart',2 );

if ( ! function_exists( 'zn_woocomerce_cart' ) ) {	
	function zn_woocomerce_cart () {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

			global $data;
			
			if ( $data['woo_show_cart'] ) {
				global $woocommerce;
				?>
				<ul class="topnav navLeft">
					<li class="drop">
						<a id="mycartbtn" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart',THEMENAME); ?>">
							<?php _e( "MY CART",THEMENAME);?>
						</a>
						<div class="pPanel">
							<div class="inner">	<span class="cart_details"><?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count,THEMENAME), $woocommerce->cart->cart_contents_count);?>, <?php _e("Total of",THEMENAME);?> <?php echo $woocommerce->cart->get_cart_total(); ?> <a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart',THEMENAME); ?>" class="checkout"><?php _e("Checkout",THEMENAME);?> <span class="icon-chevron-right"></span>
								</a>
								</span>
							</div>
						</div>
					</li>
				</ul>
				<?php
			}
		}
	}
}

/*--------------------------------------------------------------------------------------------------
	WPML language switcher
--------------------------------------------------------------------------------------------------*/
add_action( 'zn_head_right_area', 'zn_wpml_language_switcher',3 );

if ( ! function_exists( 'zn_wpml_language_switcher' ) ) {	
	function zn_wpml_language_switcher () {
	
		if ( function_exists('icl_get_languages')) {
		global $data;
		
		if ( $data['head_show_flags'] ) {
			echo '<ul class="topnav navLeft">';
			echo '<li class="languages drop"><a href="#"><span class="icon-globe icon-white"></span> '.__("LANGUAGES",THEMENAME).'</a>';
			echo '<div class="pPanel">';
			echo '<ul class="inner">';
			
				$languages = icl_get_languages('skip_missing=0');
					if(1 < count($languages)){
						foreach($languages as $l){
							$active = '';
							$icon = '';
							
							if ( $l['active'] ){
								$active = 'active';
								$icon = '<span class="icon-ok"></span></a></li>';
							}
							echo '<li class="'.$active.'"><a href="'.$l['url'].'">'.$l['native_name'].' '.$icon.'';
							
						}
					
				}
			echo '</ul>';
			echo '</div>';
			echo '</li>';
			echo '</ul>';
		}
		}

		
	}
}

/*--------------------------------------------------------------------------------------------------
	Select first image in post
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'echo_first_image' ) ) {	
function echo_first_image(  ) {
	global $post;
	$id = $post->ID;
        $attachments = get_children( array(	'post_parent' => $id,
											'numberposts' => 1,
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'order' => 'DESC',
											'orderby' => 'menu_order date')
											);

		// Search for and get the post attachment
		if ( ! empty( $attachments ) ) {
			$counter = -1;
			foreach ( $attachments as $att_id => $attachment ) {
				$counter++;
				
				if ( $counter < 0 )
					continue;

					$src = wp_get_attachment_url( $att_id );
					return $src;
					

			}

		// Get the first img tag from content
		} else {
			//print_r($post->post_content);
			$first_img = '';
			$post = get_post( $id );
			ob_start();
			ob_end_clean();
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
			if ( !empty($matches[1][0]) && basename($matches[1][0]) != 'trans.gif') {

				// Save Image URL
				return esc_url( $matches[1][0] );

			}
			elseif (!empty($matches[1][1])) {
				return esc_url( $matches[1][1] );
			}

		}
		return;
}
}
	
/*--------------------------------------------------------------------------------------------------
	Documentation Post Type
--------------------------------------------------------------------------------------------------*/

	add_action('init','zn_documentation_post_type');
if ( ! function_exists( 'zn_documentation_post_type' ) ) {		
	function zn_documentation_post_type() {
	

		$permalinks = get_option( 'zn_permalinks' );

		if (!empty( $permalinks['doc_item'] )) {
			$slug = array('slug'=>$permalinks['doc_item']);
		}
		else {
			$slug = true;
		}


		$labels = array(
		    'name' => 'Documentation',
		    'singular_name' => 'Documentation Item',
		    'add_new' => 'Add New Documentation Item',
		    'all_items' => 'All Documentation Items',
		    'add_new_item' => 'Add New Documentation',
		    'edit_item' => 'Edit Documentation Item',
		    'new_item' => 'New Documentation Item',
		    'view_item' => 'View Documentation Item',
		    'search_items' => 'Search Documentation Items',
		    'not_found' =>  'No Documentation Items found',
		    'not_found_in_trash' => 'No Documentation Items found in trash',
		    'parent_item_colon' => 'Parent Documentation:',
		    'menu_name' => 'Documentation Items'
		);
		
		$args = array(
			'labels' => $labels,
			'description' => "",
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_nav_menus' => true, 
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'menu_position' => 100,
			'menu_icon' => ADMIN_IMAGES_DIR.'/portfolio.png',
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => array('title','editor'),
			'has_archive' => true,
			'rewrite' => $slug,
			'query_var' => true,
			'can_export' => true
		); 
		
		register_post_type('documentation',$args);
	}
}	

/*--------------------------------------------------------------------------------------------------
	Documentation Post Taxonomy
--------------------------------------------------------------------------------------------------*/

	add_action( 'init', 'zn_documentation_category', 0 );
if ( ! function_exists( 'zn_documentation_category' ) ) {	
	function zn_documentation_category() 
	{

		$permalinks = get_option( 'zn_permalinks' );

		if (!empty( $permalinks['doc_tax'] )) {
			$slug = array('slug'=>$permalinks['doc_tax']);
		}
		else {
			$slug = true;
		}
		
	  // Add new taxonomy, make it hierarchical (like categories)
	  $labels = array(
		'name' => __( 'Categories',THEMENAME),
		'singular_name' => __( 'Category',THEMENAME ),
		'search_items' =>  __( 'Search Categories',THEMENAME ),
		'all_items' => __( 'All Categories',THEMENAME ),
		'parent_item' => __( 'Parent Category',THEMENAME ),
		'parent_item_colon' => __( 'Parent Category:',THEMENAME ),
		'edit_item' => __( 'Edit Category',THEMENAME ), 
		'update_item' => __( 'Update Category',THEMENAME ),
		'add_new_item' => __( 'Add New Category',THEMENAME ),
		'new_item_name' => __( 'New Category Name',THEMENAME ),
		'menu_name' => __( 'Documentation categories',THEMENAME ),
	  ); 	

	  register_taxonomy('documentation_category','documentation', array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => $slug,
	  ));
	}
}

/*--------------------------------------------------------------------------------------------------
	LOGIN SYSTEM
--------------------------------------------------------------------------------------------------*/
if ( ! function_exists( 'zn_do_login' ) ) {		
	function zn_do_login()
	{

		if ( $_POST['zn_form_action'] == 'login' ) {
			$user = wp_signon();
			if ( is_wp_error($user) ) {
			   echo '<div id="login_error">'.$user->get_error_message().'</div>';
			   die();
			}
			else{
				echo 'success';
				die();
			}
		}
		elseif( $_POST['zn_form_action'] == 'register' ){

			$zn_error = false;
			$zn_error_message = array();

			if ( !empty( $_POST['user_login'] ) ) {
				if ( username_exists( $_POST['user_login'] ) ){	
					$zn_error = true;
					$zn_error_message[] = __('The username already exists',THEMENAME);
				}
				else {
					$username = $_POST['user_login'];
				}
				
			}
			else {
				$zn_error = true;
				$zn_error_message[] = __('Please enter an username',THEMENAME);
			}

			if ( !empty( $_POST['user_password'] ) ) {
				$password = $_POST['user_password'];
			}
			else {
				$zn_error = true;
				$zn_error_message[] = __('Please enter a password',THEMENAME);
			}

			if ( ( empty( $_POST['user_password'] ) && empty( $_POST['user_password2'] ) ) || $_POST['user_password'] != $_POST['user_password2'] ) {
				$zn_error = true;
				$zn_error_message[] = __('Passwords do not match',THEMENAME);
			}


			if ( !empty( $_POST['user_email'] ) ) {

				if( !email_exists( $_POST['user_email'] )) {
					if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
						$zn_error = true;
						$zn_error_message[] = __('Please enter a valid EMAIL address',THEMENAME);
					}
					else{
						$email = $_POST['user_email'];
					}
				    
				}
				else {
					$zn_error = true;
					$zn_error_message[] = __('This email address has already been used',THEMENAME);
				}
				
			}
			else {
				$zn_error = true;
				$zn_error_message[] = __('Please enter an email address',THEMENAME);
			}




			if ( $zn_error ){
				echo '<div id="login_error">';
				foreach ( $zn_error_message as $error) {
					echo $error.'<br />';
				}
				echo '</div>';
				
				die();
			}
			else {
				$user_data = array(
	                'ID' => '',
	                'user_pass' => $password,
	                'user_login' => $username,
	                'display_name' => $username,
	                'user_email' => $email,
	                'role' => get_option('default_role') // Use default role or another role, e.g. 'editor'
	            );
	            $user_id = wp_insert_user( $user_data );
	            wp_new_user_notification( $user_id, $password );

	            echo '<div id="login_error">'.__('Your account has been created.',THEMENAME).'</div>';
	            die();

			}
			

		}
		elseif( $_POST['zn_form_action'] == 'reset_pass' ){
			echo do_action('login_form', 'resetpass');
		}

	}
}

add_action("wp_ajax_nopriv_zn_do_login", "zn_do_login");
add_action("wp_ajax_zn_do_login", "zn_do_login");


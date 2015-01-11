<?php
/**
 * Plugin Name: Rio Portfolio
 * Plugin URI: http://riosis.com/themes/rio-portfolio/
 * Description: A Portfolio management plugin that allows you to manage Image, Link and Video type Portfolio items with jquery filtration option
 * Version: 2.0
 * Author: Riosis Web Team
 * Author URI: http://web.riosis.com
 */
 ?>
<?php
/*
/*-----------------------------------------------------------------------------------*/
/*	Add Theme Support
/*-----------------------------------------------------------------------------------*/

add_theme_support( 'post-formats', array('image','link','video') );
add_post_type_support( 'portfolio', 'post-formats' );
/*
|--------------------------------------------------------------------------
| FILTERS
|--------------------------------------------------------------------------
*/

//include portfolio single template from plugin direcotory
add_filter( "single_template", "get_portfolio_type_template" ) ;
add_filter( 'archive_template', 'get_portfolio_archive_template' ) ;
/*
/*
|--------------------------------------------------------------------------
| Custom post type  'portfolios'
|--------------------------------------------------------------------------
*/
//custom post type for news..
function codex_custom_portfolio() {
  $labels = array(
    'name' => 'Portfolio',
    'singular_name' => 'Portfolio',
    'add_new' => 'Add New',
    'add_new_item' => 'Add New',
    'edit_item' => 'Edit Portfolio',
    'new_item' => 'New Portfolio',
    'all_items' => 'Portfolio',
    'view_item' => 'View Portfolio',
    'search_items' => 'Search Portfolio',
    'not_found' =>  'No Portfolio found',
    'not_found_in_trash' => 'No Portfolio found in Trash', 
    'parent_item_colon' => '',
    'menu_name' => 'Portfolio'
  );
  //switch menu image based on version
	if ( defined( 'MP6' ) && MP6 || version_compare( get_bloginfo( 'version' ), '3.8-dev', '>=' ) ) {
		$icon_url = 'dashicons-format-gallery';
	}
	else
	{
		$icon_url = plugin_dir_url(__FILE__).'/img/portfolio.png';
	}
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => array( 'slug' => 'portfolio' ),
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
	'menu_icon' => $icon_url,
    'supports' => array( 'title', 'editor','thumbnail')
  ); 
	
  register_post_type( 'portfolio', $args );
  add_post_type_support( 'portfolio', 'post-formats', array( 'gallery', 'video' ) );
  register_taxonomy("portfolio-categories", array("portfolio"), array("hierarchical" => true,'show_admin_column'   => true, "label" => "Portfolio Categories", "singular_label" => "Portfolio Categories", "rewrite" => array( 'slug' => 'portfolio' )));
  
}
add_action( 'init', 'codex_custom_portfolio' );


//for adding metabox to Portfolio post...
add_action( 'admin_init', 'fun_add_portfolio_metaBox' );

function fun_add_portfolio_metaBox() {

    add_meta_box( 'portfolio_post_metabox',
        'Post Format',
        'fun_potrfolio_metabox_display',
        'portfolio', 'normal', 'high'
    );
}

function fun_potrfolio_metabox_display( $portfolio_post ) 
{
	$portfolio_image = get_post_meta($portfolio_post->ID,'portfolio_image', true );
	$portfolio_link_url = get_post_meta($portfolio_post->ID,'portfolio_link_url', true );
	$portfolio_link_image = get_post_meta($portfolio_post->ID,'portfolio_link_image', true );
	$portfolio_link_target = get_post_meta($portfolio_post->ID,'portfolio_link_target', true );
	$portfolio_video_provider = get_post_meta($portfolio_post->ID,'portfolio_video_provider', true );
	$portfolio_video_id = get_post_meta($portfolio_post->ID,'portfolio_video_id', true );
	$portfolio_video_image = get_post_meta($portfolio_post->ID,'portfolio_video_image', true );
	$portfolio_post_order = get_post_meta($portfolio_post->ID,'portfolio_post_order', true );
	$post_format = get_post_format($portfolio_post->ID );
?>

<div class="inside">
  <h2 class="nav-tab-wrapper" style="padding:0px !important;"> <a class='nav-tab <?php if(empty($post_format) || $post_format == 'image'){?>nav-tab-active<?php } ?>' href='javascript:void(0);' id="image_tab">Image</a> <a class='nav-tab <?php if($post_format == 'link'){?>nav-tab-active<?php } ?>' href='javascript:void(0);' id="link_tab">Link</a> <a class='nav-tab <?php if($post_format == 'video'){?>nav-tab-active<?php } ?>' href='javascript:void(0);' id="video_tab">Video</a></h2>
  <!-- image post format postbox starts here..-->
  <div class="wrap <?php if($post_format == 'image' || $post_format == ''){}else{?>hide<?php } ?>" id="image_content">
    <div class="postbox theme-post-block">
      <div class="inside"> 
        <!--<h4>sdfsdfdsf</h4>-->
        
        <p><strong>Image :</strong></p>
        <p>Display an image with your post</p>
        <p>
          <input type="text" name="portfolio_image" value="<?php echo $portfolio_image;?>" id="portfolio_image" class="regular-text full-widthPr" style="vertical-align:middle; width:100%; margin-bottom:5px;" >
          <br />
          <input name="upload" type="button" class="button button-primary btn_upload" id="portfolio_image_button" value="Upload">
        </p>
        <p id="portfolio_image_show">
          <?php if(!empty($portfolio_image)){?>
          <a href="javascript:void(0);" id="del_video_img" class="delete_image"><img src="<?php echo plugins_url();?>/rio-portfolio/img/delete.png" alt="delete" title="delete" /></a>
          <img src="<?php echo $portfolio_image;?>" alt="portfolio Image" width="150" height="100" />
          <?php } ?>
        </p>
      </div>
    </div>
  </div>
  <!-- image post format postbox end here..--> 
  <!-- link post format postbox end here..-->
  <div class="wrap <?php if($post_format != 'link'){?>hide<?php } ?>" id="link_content">
    <div class="postbox theme-post-block">
      <div class="inside"> 
        <!--<h4>sdfsdfdsf</h4>-->
        <p><strong>Link URL: </strong></p>
        <p>Enter the url for the link</p>
        <p>
          <input type="text" name="portfolio_link_url" value="<?php echo $portfolio_link_url;?>" id="port_link_url" class="regular-text" style="vertical-align:middle; width:100%" >
        </p>
        <p><strong>Link Target: </strong></p>
        <p>
        	<input type="checkbox" name="portfolio_link_target" value="yes" <?php if($portfolio_link_target == 'yes'){?> checked="checked" <?php } ?> />&nbsp;&nbsp;<small>Tick the check box, If u need to open link in new tab</small>
        </p>
        <p><strong>Link Image :</strong></p>
        <p>Display an image with your post</p>
        <p>
          <input type="text" name="portfolio_link_image" value="<?php echo $portfolio_link_image;?>" id="portfolio_link_image" class="regular-text full-widthPr" style="vertical-align:middle; width:100%; margin-bottom:5px;" >
          <br />
          <input name="upload" type="button" class="button button-primary btn_upload" id="portfolio_link_image_button" value="Upload">
        </p>
        <p id="portfolio_link_image_show">
          <?php if(!empty($portfolio_link_image)){?>
          <a href="javascript:void(0);" id="del_link_img" class="delete_image"><img src="<?php echo plugins_url();?>/rio-portfolio/img/delete.png" alt="delete" title="delete" /></a>
          <img src="<?php echo $portfolio_link_image;?>" alt="portfolio link image" width="150" height="100" />
          <?php } ?>
        </p>
      </div>
    </div>
  </div>
  <!-- Link post format postbox end here..--> 
  <!-- videopost format postbox end here..-->
  <div class="wrap <?php if($post_format != 'video'){?>hide<?php } ?>" id="video_content">
    <div class="postbox theme-post-block">
      <div class="inside"> 
        <!--<h4>sdfsdfdsf</h4>-->
        
        <p><strong>Video provider: </strong></p>
        <p>Select the provider of your video</p>
        <p>
          <select name="portfolio_video_provider" id="portfolio_video_provider" class="widther" style="width:100%;margin-bottom:5px;">
            <option value="">-Select-</option>
            <option <?php if(!empty($portfolio_video_provider) && $portfolio_video_provider == 'youtube') { echo 'selected="selected"'; }?> value="youtube">YouTube</option>
            <option <?php if(!empty($portfolio_video_provider) && $portfolio_video_provider == 'vimeo') { echo 'selected="selected"'; }?> value="vimeo">Vimeo</option>
            <option <?php if(!empty($portfolio_video_provider) && $portfolio_video_provider == 'dailymotion') { echo 'selected="selected"'; }?> value="dailymotion">Dailymotion</option>
          </select>
        </p>
        <p><strong>Video ID: </strong></p>
        <p>Enter the ID of your video</p>
        <p>
          <input size="40" value="<?php echo $portfolio_video_id;?>" name="portfolio_video_id" type="text" class="widther" style="width:100%;margin-bottom:5px;" />
        </p>
        <p><strong>Video Poster: </strong></p>
        <p>Upload an image for the video</p>
        <p>
          <input type="text" name="portfolio_video_image" value="<?php echo $portfolio_video_image;?>" id="portfolio_video_image" class="regular-text" style="vertical-align:middle;width:100%;margin-bottom:5px;" >
          <br />
          <input name="upload" type="button" class="button button-primary btn_upload" id="portfolio_video_image_button" value="Upload">
        </p>
        <p id="portfolio_video_image_show">&nbsp;
          <?php if(!empty($portfolio_video_image)){?>
          <a href="javascript:void(0);" id="del_video_img" class="delete_image"><img src="<?php echo plugins_url();?>/rio-portfolio/img/delete.png" alt="delete" title="delete" /></a>
          <img src="<?php echo $portfolio_video_image;?>" alt="portfolio Image" width="150" height="100" />
          <?php } ?>
        </p>
      </div>
    </div>
  </div>
  <!-- Video post format postbox end here..--> 
  <!-- post order..-->
  <div class="postbox theme-post-block">
    <div class="inside"> 
      <!--<h4>sdfsdfdsf</h4>-->
      
      <p><strong>Post order of the post</strong></p>
      <p>
        <input type="text" name="portfolio_post_order" value="<?php if(!empty($portfolio_post_order)){echo $portfolio_post_order;}else{ echo 1;}?>" id="portfolio_post_order" class="regular-text" style="vertical-align:middle; width:100%;margin-bottom:5px;" >
      </p>
    </div>
  </div>
</div>
<?php } 
//for inserting values into postmeta table..
add_action( 'save_post','save_portfolio_meta_values', 10, 2 ); 
function save_portfolio_meta_values( $portfolio_postId,$portfolio_posts ) 
{
	if ( $portfolio_posts->post_type == 'portfolio' ) 
	{
		if($_POST)
		{
			
				update_post_meta( $portfolio_postId, 'portfolio_image',$_POST['portfolio_image']);
				
				update_post_meta( $portfolio_postId, 'portfolio_link_url',$_POST['portfolio_link_url']);
			
				update_post_meta( $portfolio_postId, 'portfolio_link_image',$_POST['portfolio_link_image']);
				
				update_post_meta( $portfolio_postId, 'portfolio_link_target',$_POST['portfolio_link_target']);
			
				update_post_meta( $portfolio_postId, 'portfolio_video_provider',$_POST['portfolio_video_provider']);
			
				update_post_meta( $portfolio_postId, 'portfolio_video_id',$_POST['portfolio_video_id']);
			
				update_post_meta( $portfolio_postId, 'portfolio_video_image',$_POST['portfolio_video_image']);

				update_post_meta( $portfolio_postId, 'portfolio_post_order',$_POST['portfolio_post_order']);

		}
	}
}

/*
|--------------------------------------------------------------------------
| Settings page
|--------------------------------------------------------------------------
*/

//settings page
function reg_portfolio_set_submenu(){
//for registering submenu settings under video gallery post type..
add_submenu_page(
    'edit.php?post_type=portfolio',
    'Settings', /*page title*/
    'Settings', /*menu title*/
    'manage_options', /*roles and capabiliyt needed*/
    'portfolio_settings',
    'portfolio_settings_fun' /*replace with your own function*/
);
}

add_action( 'admin_menu', 'reg_portfolio_set_submenu' );
function portfolio_settings_fun(){
	
	if(!empty($_POST['hid_portfolio_page']) && $_POST['hid_portfolio_page'] == 'true' )
	{
		/*..portfolio settings starts from here...*/
			
			$portfolio_count = stripslashes($_POST['portfolio_count']);//posts per page
			$portfolio_post_order = stripslashes($_POST['portfolio_post_order']);//post order
			$portfolio_orderby = stripslashes($_POST['portfolio_orderby']);//portfolio order by
			
			$portfolio_link_target = stripslashes($_POST['portfolio_link_target']);//portfolio link target 
			
			$portfolio_thumb_width = stripslashes($_POST['portfolio_thumb_width']);//thumbnail width
			$portfolio_thumb_height = stripslashes($_POST['portfolio_thumb_height']);//thumbnail height
			
			$data = array('portfolio_count' => $portfolio_count,'portfolio_post_order' => $portfolio_post_order, 'portfolio_orderby' =>$portfolio_orderby,'portfolio_thumb_width' => $portfolio_thumb_width,'portfolio_thumb_height' => $portfolio_thumb_height,'portfolio_link_target' => $portfolio_link_target);
			//update serialized array into a common option.
			$updated_portfolio = update_option('portfolio_settings',$data);
		
		/*..portfolio settings ends here...*/
	}
	
	//get portfolio_settings options in serialized format...
	$data_results = get_option('portfolio_settings');
	
	$portfolio_count = $data_results['portfolio_count'];
	$portfolio_post_order = $data_results['portfolio_post_order'];
	$portfolio_orderby = $data_results['portfolio_orderby'];
	
	$portfolio_thumb_width = $data_results['portfolio_thumb_width'];
	$portfolio_thumb_height = $data_results['portfolio_thumb_height'];
	
	$portfolio_link_target = $data_results['portfolio_link_target'];
	?>
<div class="wrap">
  <div id="icon-edit" class="icon32">&nbsp;</div>
  <h2>Portfolio Settings</h2>
  <br/>
  <?php if(!empty($updated_portfolio)) {?>
  <div class="updated below-h2" id="message">
    <p>Options updated.</p>
  </div>
  <?php }?>
  <form action="" method="post" name="frm_portfolio_settings">
    <!-- Basic Settings post box starts here..-->
    <div class="postbox">
      <div class="inside">
        <table>
          <tr>
            <th align="left">Basic Settings</th>
          </tr>
        </table>
        <table>
          <tr>
            <td align="right" width="174px">Number of Posts</td>
            <td>:</td>
            <td><input size="8px" type="text" name="portfolio_count" id="portfolio_count" value="<?php if(!empty($portfolio_count)) { echo $portfolio_count;} else { echo '12';}?>"></td>
            <td class="desc">&nbsp;&nbsp;Number of posts display (Default display is 10).</td>
          </tr>
        </table>
        <table>
          <tr>
            <td align="right" width="174px">Enable post order</td>
            <td>:</td>
            <td><input type="checkbox" value="1" id="portfolio_post_order" name="portfolio_post_order" <?php if(!empty($portfolio_post_order)) { echo 'checked="checked"';}?>></td>
            <td class="desc">&nbsp;&nbsp;If enabled, videos will display according to the post order entered.</td>
          </tr>
        </table>
        <table id="tbl_portfolio_postorder_settings">
          <tr>
            <td width="190px">&nbsp;</td>
            <td><input type="radio" <?php if(!empty($portfolio_orderby) && $portfolio_orderby == 'asc') { echo 'checked="checked"';} elseif(empty($portfolio_orderby)) {echo 'checked="checked"';}?> value="asc" name="portfolio_orderby" >
              &nbsp;Ascending</td>
            <td><input type="radio" <?php if(!empty($portfolio_orderby) && $portfolio_orderby == 'desc') { echo 'checked="checked"';}?> value="desc" name="portfolio_orderby">
              &nbsp;Descending</td>
            <td class="desc">&nbsp;Portfolio will displays ascending or descending order.</td>
          </tr>
        </table>
      </div>
    </div>
    <!-- Layout post box starts here..-->
    <div class="postbox">
      <div class="inside">
        <table>
          <tr>
            <th align="left" class="desc">Thumbnail Settings</th>
          </tr>
          <tr>
            <td align="right">Portfolio thumbnail size</td>
            <td>:</td>
            <td>Width : &nbsp;
              <input size="8px" type="text" name="portfolio_thumb_width" id="portfolio_thumb_width" value="<?php if(!empty($portfolio_thumb_width)) { echo $portfolio_thumb_width;}else{ echo 300;}?>"></td>
            <td>Height : &nbsp;
              <input size="8px" type="text" id="portfolio_thumb_height" name="portfolio_thumb_height" value="<?php if(!empty($portfolio_thumb_height)) { echo $portfolio_thumb_height;}else{ echo 180;}?>"></td>
            <td class="desc">&nbsp;The image width and height in pixels.</td>
          </tr>
        </table>
      </div>
    </div>
    <!-- Layout post box ends here..-->
    <div class="postbox">
      <div class="inside">
        <table>
          <tr>
            <th align="left">Open Portfolio links in</th>
          </tr>
          <tr>
            <td ><input type="radio" <?php if(!empty($portfolio_link_target) && $portfolio_link_target == 'popup') { echo 'checked="checked"';} elseif(empty($portfolio_link_target)) {echo 'checked="checked"';}?> value="popup" name="portfolio_link_target" >
              &nbsp;Popup</td>
            <td><input type="radio" <?php if(!empty($portfolio_link_target) && $portfolio_link_target == 'tab') { echo 'checked="checked"';}?> value="tab" name="portfolio_link_target">
              &nbsp;Hyperlink</td>
            <td class="desc">&nbsp;(This is not effected the Link Post type.)</td>
          </tr>
        </table>
      </div>
    </div>
    <!-- link type settings ends here..-->
    <div class="postbox">
      <div class="inside">
        <table>
          <tr>
            <th align="left">Short codes</th>
          </tr>
          <tr>
            <td ><input size="50" type="text" onfocus="this.select();" onMouseUp="return false" id="shortcodeDisplay" readonly value='[rio-portfolio]'></td>
            <td class="desc">&nbsp;&nbsp;Use this shortcode in your page.</td>
          </tr>
        </table>
      </div>
    </div>
    <!-- Short code post box ends here..-->
    <table>
      <tr>
        <td class="spaciuosCells"><input type="hidden" name="hid_portfolio_page" value="true">
          <input name="portfolio_submit"  type="submit" value="Update options" class="widther button-primary" /></td>
      </tr>
    </table>
  </form>
</div>
<?php	
}
?>
<?php
//for generating shortcode for portfolio
function fun_portfolio_shortcode() 
{
	$rio_portfolio_content='';// initializing the varaible for storing whole shortcode content

	$obj = get_post_type_object( 'portfolio' );
	$singular_name = $obj->labels->singular_name;

	//settings values
	//get portfolio_settings options in serialized format...
	$data_results = get_option('portfolio_settings');

	$portfolio_count = $data_results['portfolio_count'];
	if(empty($portfolio_count))//setting default
	{
		$portfolio_count = 12;
	}
	$portfolio_post_order = $data_results['portfolio_post_order'];
	$portfolio_orderby = $data_results['portfolio_orderby'];
	if(empty($portfolio_orderby))//setting default
	{
		$portfolio_orderby = 'asc';
	}
	// Set the page to be pagination
	$paged = get_query_var('paged') ? get_query_var('paged') : 1;
	if(!empty($portfolio_post_order) && $portfolio_post_order == 1) //with post order
	{
		$query_post_args = array(
		'post_type' => 'portfolio',
		'posts_per_page' => $portfolio_count,
		'meta_key' => 'portfolio_post_order',
		'orderby' => 'meta_value_num',
		'order' => $portfolio_orderby,
		'paged' => $paged
		);
	}
	else //without post order
	{
		
		$query_post_args = array(
		'post_type' => 'portfolio',
		'posts_per_page' => $portfolio_count,
		'paged' => $paged
		);
	
	}
	
  $rio_portfolio_content.='<div id="portfolio-container" class="portfolio-container">
  <ul class="filter">
    
    ';
  
//  <li class="active"><a style="display:block;  padding:10px; " href="javascript:void(0)" class="all">All</a></li>
//    <li class=""><a style="display:block;  padding:10px;" href="javascript:void(0)" class="home-generators">Home Generators</a></li>
//    <li class=""><a style="display:block;  padding:10px;" href="javascript:void(0)" class="commercial-generators">Commercial Generators</a></li>
//    <li class=""><a style="display:block;  padding:10px; " href="javascript:void(0)" class="industrial-generators">Industrial Generators</a></li>
    
	// Get the taxonomy
	$args = array(
	'orderby'       => 'name', 
	'order'         => 'ASC',
	'hide_empty'    => false,
	'slug'          => '', 
	'parent'         => '',
	'hierarchical'  => true,
	);
	$terms = get_terms('portfolio-categories', $args);
	
	// set a count to the amount of categories in our taxonomy
	global $count;
	$count = count($terms); 
	
	// set a count value to 0
	$i=0;
	
	// test if the count has any categories
	if ($count > 0) {
		
		// break each of the categories into individual elements
		foreach ($terms as $term) {
			
			// increase the count by 1
			$i++;
			
			// rewrite the output for each category
//			$term_list .= '<li><a href="javascript:void(0)" class="'. $term->slug .'">' . $term->name . '</a></li>';
			
			// if count is equal to i then output blank
			if ($count != $i)
			{
				$term_list .= '';
			}
			else 
			{
				$term_list .= '';
			}
		}
		
		// print out each of the categories in our new format
		$rio_portfolio_content.= $term_list;
	}

 $rio_portfolio_content.=' </ul>
  <ul class="filterable-grid">';
   
	// Query Out Database
	$wpbp = new WP_Query($query_post_args); 
	
	// Begin The Loop
	if ($wpbp->have_posts()) : while ($wpbp->have_posts()) : $wpbp->the_post(); 
	  
	//get portfolio_settings options in serialized format...
	$data_results = get_option('portfolio_settings');
	
	$portfolio_link_target = $data_results['portfolio_link_target'];
	
	if(empty($portfolio_link_target))
	{
		$portfolio_link_target='popup';
	}
	
	$portfolio_thumb_width = $data_results['portfolio_thumb_width'];
	
	if(empty($portfolio_thumb_width)) { $portfolio_thumb_width='300'; }
	
	$portfolio_thumb_height = $data_results['portfolio_thumb_height'];
	
	if(empty($portfolio_thumb_height)) { $portfolio_thumb_height='180'; }
	
	$terms = get_the_terms( $post->ID, 'portfolio-categories' );
	 

if(get_post_format() == 'image')//image section
{
	global $count;
	// Get The Taxonomy 'Filter' Categories
	$large_image = get_post_meta(get_the_ID(),'portfolio_image', true );
	if(empty($large_image))
	{
		$large_image=plugins_url().'/rio-portfolio/img/default-image.jpg';
	}
	$large_image =timthumb_fix_portfolio($large_image);
	
    $rio_portfolio_content.='<li data-id="id-'.$count.'" data-type="';if(!empty($terms) && is_wp_error( $terms )){ foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)); } } $rio_portfolio_content.= '" class="';if(!empty($terms) && is_wp_error( $terms )){foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)). '-item'; }} $rio_portfolio_content.= 'images-item" style="height:'.$portfolio_thumb_height.'px; width: '.$portfolio_thumb_width.'px;">';
       // Output the featured image
      $rio_portfolio_content.='<a '; if($portfolio_link_target == 'popup'){ $rio_portfolio_content.='rel="prettyPhoto[gallery]"  href="'.$large_image.'"'; }else{ $rio_portfolio_content.='href="'.get_permalink($post-ID).'"'; } $rio_portfolio_content.=' title="'.get_the_title().'"><p>'.get_the_title().'</p></a>
      <img src="'.plugins_url().'/rio-portfolio/scripts/timthumb.php?src='.$large_image.'&amp;w='.$portfolio_thumb_width.'&amp;h='.$portfolio_thumb_height.'&amp;zc=1" alt="'.get_the_title().'" width="'.$portfolio_thumb_width.'" height="'. $portfolio_thumb_height.'"> </li>';
	//image section end here
		}
		elseif(get_post_format() == 'video')//video section
		{
			$portfolio_video_provider = get_post_meta(get_the_ID(),'portfolio_video_provider', true );
			
			$portfolio_video_id = get_post_meta(get_the_ID(),'portfolio_video_id', true );
			
			$portfolio_video_image = get_post_meta(get_the_ID(),'portfolio_video_image', true );
			
			$rio_portfolio_content.='<li data-id="id-'.$count.'" data-type="'; if(!empty($terms)){ foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } } $rio_portfolio_content.='" class="'; if(!empty($terms) && is_wp_error( $terms )){foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)). '-item'; }} $rio_portfolio_content.='videos-item" style="height:'.$portfolio_thumb_height.'px; width: '.$portfolio_thumb_width.'px;">';
			
		  // Output the featured image 
		  if($portfolio_video_provider == 'youtube')
		  {
			$rio_portfolio_content.='<a ';if($portfolio_link_target == 'popup'){ $rio_portfolio_content.='href="http://www.youtube.com/watch?v='.$portfolio_video_id.'"  rel="prettyPhoto"'; }else{ $rio_portfolio_content.=' href="'. get_permalink($post-ID).'"'; } $rio_portfolio_content.=' title="'.get_the_title().'">';
			
		  }elseif($portfolio_video_provider == 'vimeo'){
			  
			$rio_portfolio_content.='<a ';if($portfolio_link_target == 'popup'){ $rio_portfolio_content.=' href="http://vimeo.com/'. $portfolio_video_id.'"  rel="prettyPhoto" '; }else{ $rio_portfolio_content.='href="'.get_permalink($post-ID).'"'; } $rio_portfolio_content.=' title="'.get_the_title().'">';
			
		  }elseif($portfolio_video_provider == 'dailymotion'){
		  
			$rio_portfolio_content.='<a '; if($portfolio_link_target == 'popup'){ $rio_portfolio_content.='href="http://www.dailymotion.com/embed/video/'.$portfolio_video_id.'?iframe=true&width=500&height=344"  rel="prettyPhoto" ';}else{ $rio_portfolio_content.='href="'.get_permalink($post-ID).'"';}$rio_portfolio_content.=' title="'.get_the_title().'">';
			
		  }else{
			  
			$rio_portfolio_content.='<a ';if($portfolio_link_target == 'popup'){$rio_portfolio_content.=' rel="prettyPhoto[gallery]"  href="'.plugins_url().'/rio-portfolio/img/default-video.jpg"'; }else{ $rio_portfolio_content.=' href="'.get_permalink($post-ID).'" '; } $rio_portfolio_content.='title="'.get_the_title().'">';
			
		  }
	  
		 $rio_portfolio_content.=' <p> '.get_the_title().'</p></a>';
	 
		if(empty($portfolio_video_image))//if not upload the video thumb then
			{
				if(!empty($portfolio_video_provider) && !empty($portfolio_video_id))
				{
					if($portfolio_video_provider == 'youtube')
					{
						$portfolio_video_image='http://img.youtube.com/vi/'.$portfolio_video_id.'/0.jpg';
					}
					elseif($portfolio_video_provider == 'vimeo')
					{
						$hash = unserialize(@file_get_contents("http://vimeo.com/api/v2/video/$portfolio_video_id.php"));
						$portfolio_video_image=$hash[0]['thumbnail_medium'];
					}
					elseif($portfolio_video_provider == 'dailymotion')
					{
						$portfolio_video_image='http://www.dailymotion.com/thumbnail/video/$portfolio_video_id';
					}
					
                    $rio_portfolio_content.='<img src="'.$portfolio_video_image.'" width="'.$portfolio_thumb_width.'" height="'.$portfolio_thumb_height.'" alt="'.get_the_title().'">';
					
            	}
				else
				{
					
					$portfolio_video_image=	plugins_url().'/rio-portfolio/img/default-video.jpg';
					$portfolio_video_image=timthumb_fix_portfolio($portfolio_video_image);
				
                    $rio_portfolio_content.='<img src="'.plugins_url().'/rio-portfolio/scripts/timthumb.php?src='.$portfolio_video_image.'&amp;w='.$portfolio_thumb_width.'&amp;h='.$portfolio_thumb_height.'&amp;zc=1" width="'. $portfolio_thumb_width.'" height="'.$portfolio_thumb_height.'" alt="'.get_the_title().'">';
           
				}
				
			}
			else
			{ 
			
				$portfolio_video_image=timthumb_fix_portfolio($portfolio_video_image);
			
				$rio_portfolio_content.='<img src="'.plugins_url().'/rio-portfolio/scripts/timthumb.php?src='. $portfolio_video_image.'&amp;w='.$portfolio_thumb_width.'&amp;h='.$portfolio_thumb_height.'&amp;zc=1" width="'. $portfolio_thumb_width.'" height="'.$portfolio_thumb_height.'" alt="'.get_the_title().'">';
      		 }
			 
    $rio_portfolio_content.='</li>';
	//video section end here
	
	}
	elseif(get_post_format() == 'link')//link section
	{
		$portfolio_link_title=get_the_title();
		
		$portfolio_link_url = get_post_meta(get_the_ID(),'portfolio_link_url', true );
		
		$portfolio_link_image = get_post_meta(get_the_ID(),'portfolio_link_image', true );
		
		$portfolio_link_target = get_post_meta(get_the_ID(),'portfolio_link_target', true );
		//assign default image when there is no link image
		if(empty($portfolio_link_image))
		{
			$portfolio_link_image=plugins_url().'/rio-portfolio/img/default-link.jpg';
		}
		$portfolio_link_image=timthumb_fix_portfolio($portfolio_link_image);

        $rio_portfolio_content.='<li data-id="id-'.$count.'" data-type="'; if(!empty($terms)){ foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; }} $rio_portfolio_content.='" class="'; if(!empty($terms) && is_wp_error( $terms )){foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)). '-item'; }}  $rio_portfolio_content.=' hyperlinks-item" style="height:'.$portfolio_thumb_height.'px; width: '.$portfolio_thumb_width.'px;">';
		
      // Output the featured image
      $rio_portfolio_content.='<a  href="'.portfolio_url($portfolio_link_url).'"';if(!empty($portfolio_link_target) && $portfolio_link_target = 'yes'){ $rio_portfolio_content.=' target="_blank"';} $rio_portfolio_content.='> <p>'. $portfolio_link_title.'</p></a> 
      <img src="'. plugins_url().'/rio-portfolio/scripts/timthumb.php?src='.$portfolio_link_image.'&amp;w='. $portfolio_thumb_width.'&amp;h='. $portfolio_thumb_height.'&amp;zc=1" alt="'.get_the_title().'" width="'. $portfolio_thumb_width.'" height="'.$portfolio_thumb_height.'"> </li>';
	//link end here
	
	}
	else //standard
	{
		
		$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
		$large_image = $large_image[0];
		if(empty($large_image))
		{
		$large_image=plugins_url().'/rio-portfolio/img/default-image.jpg';
		} 
		$large_image=timthumb_fix_portfolio($large_image);
		$little_excerpt = substr(get_the_excerpt(),0,200);
    	$rio_portfolio_content.=  ' <div class="images-item" style=" border:1px solid #d8d8d8; padding:3px; border-radius:3px; height:320px; width:350px; background:#fff; float:left;margin-left:30px; margin-bottom:30px;"  data-id="id-'.$count.'" data-type="'; if(!empty($terms)){ foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } } $rio_portfolio_content.='" class="images-item" style="height:'.$portfolio_thumb_height.'px; width: '.$portfolio_thumb_width.'px;"      ><li style="width:100%;" data-id="id-'.$count.'" data-type="'; if(!empty($terms)){ foreach ($terms as $term) { $rio_portfolio_content.= strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } } $rio_portfolio_content.='" class="images-item" style="height:'.$portfolio_thumb_height.'px; width: '.$portfolio_thumb_width.'px;">';
       // Output the featured image
       $rio_portfolio_content.='<a ';if($portfolio_link_target == 'popup'){ $rio_portfolio_content.=' rel="prettyPhoto[gallery]" href="'.$large_image.'"'; }else{ $rio_portfolio_content.=' href="'.get_permalink($post-ID).'"';} $rio_portfolio_content.=' > <p>'.get_the_title().'</p></a> 
      <img src="'.plugins_url().'/rio-portfolio/scripts/timthumb.php?src='.$large_image.'&amp;w='.$portfolio_thumb_width.'&amp;h='.$portfolio_thumb_height.'&amp;zc=1" alt="'.get_the_title().'" width="100%" height="'. $portfolio_thumb_height.'"> </li> <a  ';if($portfolio_link_target == 'popup'){ $rio_portfolio_content.=' rel="prettyPhoto[thumbs]" href="'.$large_image.'"'; }else{ $rio_portfolio_content.=' href="'.get_permalink($post-ID).'"';} $rio_portfolio_content.='  href="'.  get_the_permalink().'"> <h4>'.get_the_title(). '</h4></a><br><p style="margin-top:-20px; font-family: \'Open Sans\' ">'. '<strong>Customer Testimonial :  </strong>'. $little_excerpt.' </p></div>';
    
	}
	//standard  post type end here
	
	$count++; // Increase the count by 1 
    endwhile; endif; // END the Wordpress Loop 
    wp_reset_query(); // Reset the Query Loop
	
  $rio_portfolio_content.='</ul>
  <div class="clearFixer">&nbsp;</div>
</div>
<div class="clearFixer">&nbsp;</div>
<p class="pagination">';

$big = 999999999; // need an unlikely integer
$rio_portfolio_content.= paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1, get_query_var('paged') ),
	'total' => $wpbp->max_num_pages
) );
$rio_portfolio_content.='</p><div class="clearFixer">&nbsp;</div>';
#content END -->

//return the content
return $rio_portfolio_content;

}
add_shortcode('rio-portfolio', 'fun_portfolio_shortcode');
?>
<?php
function rio_portfolio_register_scripts()
{
/*
|--------------------------------------------------------------------------
| Hook css and js to wp_head
|--------------------------------------------------------------------------
*/
 //for enabling style.
 $css_path = plugins_url().'/rio-portfolio/css/portfolio.css';
	 wp_register_style( 'portfolio-custom-style', $css_path);
	 wp_enqueue_style( 'portfolio-custom-style' );
	 //for enable scripts.
	 if (!wp_script_is( 'portfolio-custom-script.js', 'enqueued' )) 
	 {
		wp_register_script('portfolio-scripts', plugins_url() . '/rio-portfolio/js/portfolio-custom-script.js', array('jquery'),false,true);
		wp_enqueue_script('portfolio-scripts');
	 }
	 if (!is_admin()) 
	 {
		if (!wp_script_is( 'jquery.min.js', 'enqueued' )) 
		 {
			wp_register_script('jquery-min', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', 'jquery',false,true);
			wp_enqueue_script('jquery-min');
		 }
		 if (!wp_script_is( 'jquery.quicksand.js', 'enqueued' )) 
		 {
			wp_register_script('quicksand',plugins_url() . '/rio-portfolio/js/jquery.quicksand.js', 'jquery',false,true);
			wp_enqueue_script('quicksand');
		 }
		 if (!wp_script_is( 'jquery.easing.1.3.js', 'enqueued' )) 
		 {	 
			wp_register_script('easing', plugins_url() . '/rio-portfolio/js/jquery.easing.1.3.js', 'jquery',false,true);
			wp_enqueue_script('easing');
		 }
		 if (!wp_script_is( 'jquery.prettyPhoto.js', 'enqueued' )) 
		 {
			wp_register_script('prettyPhoto', plugins_url() . '/rio-portfolio/js/jquery.prettyPhoto.js', 'jquery',false,true);
			wp_enqueue_script('prettyPhoto');
		 }
	 }
}
	
add_action('init', 'rio_portfolio_register_scripts');
/*
|--------------------------------------------------------------------------
| Include custom template from plugin directory
|--------------------------------------------------------------------------
*/
//single 
function get_portfolio_type_template($single_template) {
 global $post;

 if ($post->post_type == 'portfolio') {
      $single_template = dirname( __FILE__ ) . '/template/single-portfolio.php';
 }
 return $single_template;
}
//archive
function get_portfolio_archive_template($single_template) {
 global $post;

 if ($post->post_type == 'portfolio') {
      $single_template = dirname( __FILE__ ) . '/template/archive-portfolio.php';
 }
 return $single_template;
}
/*
|--------------------------------------------------------------------------
| short content function
|--------------------------------------------------------------------------
*/
function portfolio_short_content($num) {
$limit = $num+1;
	$content = str_split(get_the_content());
	$length = count($content);
	if ($length>=$num)
	{
		$content = array_slice( $content, 0, $num);
		$content = implode("",$content)."...";
		echo $content;
	}
	else
	{
		the_content();
	} 
} 
/*
|--------------------------------------------------------------------------
| adding http:// to url
|--------------------------------------------------------------------------
*/
function portfolio_url($url)
{
	if (strpos($url,'http://') === false){
		return 'http://'.$url;
	}
	else
	{
		return $url;
	}
}
/*
|--------------------------------------------------------------------------
| Help Section
|--------------------------------------------------------------------------
*/
function rio_portfolio_help($contextual_help, $screen_id, $screen) {
$CurrentPostType = $screen->post_type; //returns post type..
if ($CurrentPostType == 'portfolio') {

		

		// Add my_help_tab if current screen is video-gallery

		$screen->add_help_tab( array(

			'id'	=> 'about_video_gallery',

			'title'	=> __('Overview'),

			'content'	=> portfolio_overview()

		) );

		

		// Add category

		$screen->add_help_tab(array(

		   'id' => 'video_gallery_category',

		   'title' => 'Add a Category',

		   'content' => portfolio_add_category()

		));
		
		// Add POST

		$screen->add_help_tab(array(

		   'id' => 'video_gallery_post',

		   'title' => 'Add a Post',

		   'content' => portfolio_add_post()

		));
		

		// Settings

		$screen->add_help_tab(array(

		   'id' => 'settings_video',

		   'title' => 'Settings',

		   'content' => portfolio_settings()

		));
		
		// Youtube Video ID

		$screen->add_help_tab(array(

		   'id' => 'getting_youtube_id',

		   'title' => 'Get Youtube Id',

		   'content' => get_portfolio_youtube_id()

		));
		// Vimeo Video ID

		$screen->add_help_tab(array(

		   'id' => 'getting_vimeo_id',

		   'title' => 'Get Vimeo Id',

		   'content' => get_portfolio_vimeo_id()

		));
		// Vimeo Video ID

		$screen->add_help_tab(array(

		   'id' => 'getting_dialymotion_id',

		   'title' => 'Get Dailymotion Id',

		   'content' => get_portfolio_dialymotion_id()

		));



	}
}
add_filter('contextual_help', 'rio_portfolio_help', 10, 3);
?>
<?php function portfolio_overview(){ 
$output = '<h3>Portfolio</h3>
<p>This is the Portfolio section, here you can manage your Portfolio posts and do basic actions such as Add, Edit, View, Trash and Setting up your portfolio</p>
<h3>Managing Portfolio</h3>
<ul>
	<li>
		<strong>Creating a new Portfolio post:</strong>
		Click on the "Add New" button on the top of the page to create a new Portfolio post
		<p>Here, you need to add the following information</p>
		<ul>
			<li><strong>Post Title:</strong> This is the title for your video post</li>
			<li><strong>Post Content:</strong> This is the content for your video post</li>
			<li><strong>Portfolio Post Format:</strong> This section has the following field
			<ol>
				<li><strong>Standard:</strong>Your normal, average, everyday blog post. This is the default styling for your theme</li> 
				<li><strong>Image:</strong> These posts highlight your images</li>
				<li><strong>Video:</strong>Just like Image posts these posts highlight your videos</li>
				<li><strong>Link:</strong> A link to another site</li> 	
			</ol>
			</li>
			<li><strong>Portfolio Options:</strong> This section has the following field
			<ol>
			<li><strong>Image:</strong> If Post format is `image` then this section execute
			<ol>
				<li><strong>Upload Image:</strong>Upload an image for portfolio</li> 
			</ol>
			</li>
			<li><strong>Link:</strong> If Post format is `link` then this section execute
			<ol>
				<li><strong>Link Url:</strong>Target url of the link</li> 
				<li><strong>Link Image:</strong>Upload an image for your link</li> 
			</ol>
			</li>
			<li><strong>Video:</strong> If Post format is `video` then this section execute
			<ol>
				<li><strong>video Provider:</strong>Select the provider of your video like Youtube, Vimeo or Dailymotion</li> 
				<li><strong>Video ID:</strong> ID of your video</li> 
				<li><strong>Video Poster:</strong>Upload an image for the video</li> 
			</ol>
			</li>
				<li><strong>Post Order:</strong>Post order specifies the ordering for this post. The post will show in ascending order corresponding to the numerical value of this field</li> 	
			</ol>
			</li>
		</ul>
	</li>
	<li>
		<strong>Modifying a portfolio post:</strong>
		Click on the name of the Portfolio post or the "Edit" button to jump to the edit page
	</li>
	<li>
		<strong>Adding Portfolio Categories:</strong>
		If you want to post a Portfolio under a specific category, you need to add a Portfolio category through "Portfolio Categories" option
	</li>
	<li>
		<strong>Settings of Video Gallery:</strong>
		The complete Portfolio settings are under "Settings" options. Here you can manage the following options
        <ul>
        	<li><strong>Basic Settings:</strong> Here you can manage Number of videos display and Enable post order options </li>
        	<li><strong>Thumbnail Settings: </strong>
            You can setup width and height of the Portfolio thumbnails</li>
			<li><strong>Open Portfolio links in: </strong>
            You can setup portfolio link is popup or hyperlink</li>
        	<li><strong>Short codes:</strong> You can place your Portfolio into pages and posts with the shortcodes. To insert the portfolio shortcode, edit a page or post and insert its shortcode into the WordPress text editor 
            <p>Please note that you have to update your video options before leaving from the settings page</p>
            </li>
        </ul>
	</li>
</ul>';
return $output;
}?>
<?php function portfolio_add_category(){ 
$output = '<h3>Add a Portfolio Category</h3>
<p>
	<ul>
    <li> For adding Portfolio categories: Click on “Portfolio Categories” link</li>
  </ul>
  <p><img src="'.plugins_url().'/rio-portfolio/help/images/Rio-portfolio-add-category.png" alt="" /><p>
</p>';
return $output;
}?>
<?php function portfolio_add_post(){ 
$output = '<h3>Add a Portfolio Post</h3>
<p>
	<ul>
    <li>For adding Portfolio post: Click on “Add new” link</li>
  </ul>
  <p><img src="'.plugins_url().'/rio-portfolio/help/images/Rio-portfolio-add.png" alt="" /><p>
</p>
<p>
<ul>
    <li> Enter the Portfolio title, content , post format, Post format details and Post Order</li>
  </ul>
	 <p><img src="'.plugins_url().'/rio-portfolio/help/images/Rio-portfolio-add-new.png" alt="" /></p>
</p>';
return $output;
}?>
<?php function portfolio_settings(){ 
$output = '<h3>Portfolio Settings</h3>
<p>
	<ul>
    <li> Click on “Settings” link</li>
  </ul>
  <p>Here you can manage the following options<p>
</p>
<p>
<ul>
        	<li><strong>Basic Settings:</strong> Here you can manage Number of videos display and Enable post order options</li>
        	<li><strong>Thumbnail Settings: </strong>
            You can setup width and height of the portfolio thumbnails</li>
			<li><strong>Open Portfolio links in: </strong>
            You can setup portfolio link is popup or hyperlink</li>
        	<li><strong>Short codes:</strong> You can place your portfolio into pages and posts with the shortcodes. To insert the Portfolio shortcode, edit a page or post and insert its shortcode into the WordPress text editor. 
            <p>Please note that you have to update your video options before leaving from the settings page.</p>
            </li>
        </ul>
	 <p><img src="'.plugins_url().'/rio-portfolio/help/images/Rio-portfolio-settings-page.png" alt="" /></p>
	 
</p>
';
return $output;
}?>
<?php function get_portfolio_youtube_id(){ 
$output = '<h3>Getting Video Id</h3>
<p><img src="'.plugins_url().'/rio-portfolio/help/images/get-youtube-video-id.png" alt="" /><p>
';
return $output;
}?>
<?php function get_portfolio_vimeo_id(){ 
$output = '<h3>Getting Vimeo Video Id</h3>
  <p><img src="'.plugins_url().'/rio-portfolio/help/images/get-vimeo-id-1.png" alt="" /><p>
  <p><img src="'.plugins_url().'/rio-portfolio/help/images/get-vimeo-id-2.png" alt="" /><p>
';
return $output;
}?>
<?php function get_portfolio_dialymotion_id(){ 
$output = '<h3>Getting Dialymotion Video Id</h3>
<p><img src="'.plugins_url().'/rio-portfolio/help/images/get-dialymotion-id.png" alt="" /><p>
';
return $output;
}
/* TIMTHUMB FIX FOR MULTISITE BEGIN */
function timthumb_fix_portfolio( $image )
{
	if (is_multisite())
	{
		global $blog_id;
		if (isset($blog_id) && $blog_id > 1)
		{
			return $image =  str_replace(get_site_url(), get_site_url(1), $image);
		}
		else
		{
			return $image;
		}
	}
	else
	{
		return $image;
	}
}
/*
USAGE EX : <?php $image[0] =  timthumb_fix_portfolio( $image[0] ); ?>
<img src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo $image[0]; ?>&amp;w=300&amp;h=150&amp;zc=1" />
*/
/* TIMTHUMB FIX FOR MULTISITE ENDS */

?>
<?php get_header(); ?>
<?php $obj = get_post_type_object( 'portfolio' );
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
	
?>

<div id="portfolio-container" class="portfolio-container">
  <h1><?php echo $singular_name;?></h1>
  <ul class="filter">
    <li><strong>Filter:</strong></li>
    <li class="active"><a href="javascript:void(0)" class="all">All</a></li>
    <?php
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
			$term_list .= '<li><a href="javascript:void(0)" class="'. $term->slug .'">' . $term->name . '</a></li>';
			
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
		echo $term_list;
	}
?>
  </ul>
  <ul class="filterable-grid">
    <?php 
		// Query Out Database
		$wpbp = new WP_Query($query_post_args); 
	?>
    <?php
		// Begin The Loop
		if ($wpbp->have_posts()) : while ($wpbp->have_posts()) : $wpbp->the_post(); 
	?>
    <?php  
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

$terms = get_the_terms( get_the_ID(), 'portfolio-categories' ); 

if(get_post_format() == 'image')//image section
{
	global $count;
	
	$large_image = get_post_meta(get_the_ID(),'portfolio_image', true ); 
	
	if(empty($large_image))
	{
		$large_image=plugins_url().'/rio-portfolio/img/default-image.jpg';
	}
	$large_image=timthumb_fix_portfolio($large_image);
	//Apply a data-id for unique indentity, 
	//and loop through the taxonomy and assign the terms to the portfolio item to a data-type,
	// which will be referenced when writing our Quicksand Script
	?>
    <li data-id="id-<?php echo $count; ?>" data-type="<?php if(!empty($terms)){ foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } } ?>" class="images-item" style="height:<?php echo $portfolio_thumb_height;?>px; width: <?php echo $portfolio_thumb_width;?>px;">
      <?php // Output the featured image ?>
      <a <?php if($portfolio_link_target == 'popup'){?> rel="prettyPhoto[gallery]"  href="<?php echo $large_image ?>"<?php }else{?> href="<?php the_permalink();?>" <?php }?> title="<?php the_title();?>">
      <p><?php echo get_the_title(); ?></p>
      </a> <img src="<?php echo plugins_url();?>/rio-portfolio/scripts/timthumb.php?src=<?php echo $large_image; ?>&amp;w=<?php echo $portfolio_thumb_width;?>&amp;h=<?php echo $portfolio_thumb_height;?>&amp;zc=1" alt="<?php the_title(); ?>" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>"> </li>
    <?php 	
	
	//image section end here
		}elseif(get_post_format() == 'video')//video section
		{
			$portfolio_video_provider = get_post_meta(get_the_ID(),'portfolio_video_provider', true );
			
			$portfolio_video_id = get_post_meta(get_the_ID(),'portfolio_video_id', true );
			
			$portfolio_video_image = get_post_meta(get_the_ID(),'portfolio_video_image', true );
			
			?>
    <li data-id="id-<?php echo $count; ?>" data-type="<?php if(!empty($terms)){ foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } } ?>" class="videos-item" style="height:<?php echo $portfolio_thumb_height;?>px; width: <?php echo $portfolio_thumb_width;?>px;">
      <?php // Output the featured image ?>
      <?php if($portfolio_video_provider == 'youtube')
			{?>
      <a <?php if($portfolio_link_target == 'popup'){?> href="http://www.youtube.com/watch?v=<?php echo $portfolio_video_id;?>"  rel="prettyPhoto"<?php }else{ ?> href="<?php the_permalink();?>" <?php } ?> title="<?php the_title();?>">
      <?php }elseif($portfolio_video_provider == 'vimeo'){?>
      <a <?php if($portfolio_link_target == 'popup'){?> href="http://vimeo.com/<?php echo $portfolio_video_id;?>"  rel="prettyPhoto" <?php }else{ ?> href="<?php the_permalink();?>" <?php } ?> title="<?php the_title();?>">
      <?php }elseif($portfolio_video_provider == 'dailymotion'){?>
      <a <?php if($portfolio_link_target == 'popup'){?> href="http://www.dailymotion.com/embed/video/<?php echo $portfolio_video_id;?>?iframe=true&width=500&height=344"  rel="prettyPhoto" <?php }else{?>href="<?php the_permalink();?>"<?php }?> title="<?php the_title();?>">
      <?php } 
	  else {?>
      <a <?php if($portfolio_link_target == 'popup'){?> rel="prettyPhoto[gallery]"  href="<?php echo plugins_url();?>/rio-portfolio/img/default-video.jpg"<?php }else{?> href="<?php the_permalink();?>" <?php }?> title="">
      <?php  }?>
      <p><?php echo get_the_title(); ?></p>
      </a>
      <?php 
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
					}?>
					<img src="<?php echo $portfolio_video_image; ?>" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>">
                    <?php
	 			}else{
				$portfolio_video_image=	plugins_url().'/rio-portfolio/img/default-video.jpg';
				$portfolio_video_image=timthumb_fix_portfolio($portfolio_video_image);?>
                <img src="<?php echo plugins_url();?>/rio-portfolio/scripts/timthumb.php?src=<?php echo $portfolio_video_image; ?>&amp;w=<?php echo $portfolio_thumb_width;?>&amp;h=<?php echo $portfolio_thumb_height;?>&amp;zc=1" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>">
                <?php
				}?>
      <?php 
			}else{ 
			$portfolio_video_image=timthumb_fix_portfolio($portfolio_video_image);
			?>
      <img src="<?php echo plugins_url();?>/rio-portfolio/scripts/timthumb.php?src=<?php echo $portfolio_video_image; ?>&amp;w=<?php echo $portfolio_thumb_width;?>&amp;h=<?php echo $portfolio_thumb_height;?>&amp;zc=1" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>">
      <?php } ?>
    </li>
    <?php 
		//video section end here
		}elseif(get_post_format() == 'link')//link section
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
			?>
    <li data-id="id-<?php echo $count; ?>" data-type="<?php if(!empty($terms)){ foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } } ?>" class="hyperlinks-item" style="height:<?php echo $portfolio_thumb_height;?>px; width: <?php echo $portfolio_thumb_width;?>px;">
      <?php // Output the featured image ?>
      <a  href="<?php echo portfolio_url($portfolio_link_url); ?>" <?php if(!empty($portfolio_link_target) && $portfolio_link_target = 'yes'){?> target="_blank" <?php } ?>>
      <p><?php echo get_the_title(); ?></p>
      </a> <img src="<?php echo plugins_url();?>/rio-portfolio/scripts/timthumb.php?src=<?php echo $portfolio_link_image; ?>&amp;w=<?php echo $portfolio_thumb_width;?>&amp;h=<?php echo $portfolio_thumb_height;?>&amp;zc=1" alt="<?php the_title(); ?>" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>"> </li>
    <?php 
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
		?>
    <li data-id="id-<?php echo $count; ?>" data-type="<?php if(!empty($terms)){ foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } } ?>" class="images-item" style="height:<?php echo $portfolio_thumb_height;?>px; width: <?php echo $portfolio_thumb_width;?>px;"> <a <?php if($portfolio_link_target == 'popup'){?> rel="prettyPhoto[gallery]" href="<?php echo $large_image ?>" <?php }else{?> href="<?php the_permalink();?>" <?php } ?> >
      <p><?php echo get_the_title(); ?></p>
      </a> <img src="<?php echo plugins_url();?>/rio-portfolio/scripts/timthumb.php?src=<?php echo $large_image; ?>&amp;w=<?php echo $portfolio_thumb_width;?>&amp;h=<?php echo $portfolio_thumb_height;?>&amp;zc=1" alt="<?php the_title(); ?>" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>"> </li>
    <?php }
	//standard  post type end here
	?>
    <?php $count++; // Increase the count by 1 ?>
    <?php endwhile; endif; // END the Wordpress Loop ?>
    <?php wp_reset_query(); // Reset the Query Loop?>
  </ul>
  <div class="clearFixer">&nbsp;</div>
</div>
<div class="clearFixer">&nbsp;</div>
<p class="pagination">
  <?php
$big = 999999999; // need an unlikely integer
echo paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1, get_query_var('paged') ),
	'total' => $wpbp->max_num_pages
) ); 
?>
</p>
<div class="clearFixer">&nbsp;</div>
<!-- #content END -->
<?php get_footer(); ?>
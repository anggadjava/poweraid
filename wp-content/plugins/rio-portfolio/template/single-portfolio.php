<?php get_header(); the_post(); ?>
<?php
$current_cat = get_taxonomy('portfolio-categories');
$obj = get_post_type_object('portfolio');
$singular_name = $obj->labels->singular_name;
 $post_ID =  $post->ID;
?>
<section id="content-area"> 
      <!-- //Left column -->
      <div class="left-column" itemscope itemtype="http://schema.org/article">
          <h2><?php the_title(); ?> </h2>
        <figure>
        <?php 
		if(get_post_format($post_ID) == 'image')//image section
		{
			$large_image = get_post_meta(get_the_ID(),'portfolio_image', true );
			if(!empty($large_image))
			{
			$size = getimagesize($large_image);
			}
			if($size[0] < 300)
			{?>
            <img src="<?php echo $large_image; ?>" alt="<?php the_title(); ?>">
			<?php
            }
			else
			{
			?>
			 <img src="<?php echo plugins_url();?>/rio-portfolio/scripts/timthumb.php?src=<?php echo $large_image; ?>&amp;w=300&amp;h=200&amp;zc=1" alt="<?php the_title(); ?>" width="300" height="200">
        <?php
			}
		}elseif(get_post_format($post_ID) == 'video')//video section
		{
			$portfolio_video_provider = get_post_meta(get_the_ID(),'portfolio_video_provider', true );
			$portfolio_video_id = get_post_meta(get_the_ID(),'portfolio_video_id', true );
			$portfolio_video_image = get_post_meta(get_the_ID(),'portfolio_video_image', true );
			
			if(!empty($portfolio_video_provider) && $portfolio_video_provider == 'youtube') {?>
<iframe width="500" height="350" src="//www.youtube.com/embed/<?php echo $portfolio_video_id;?>" frameborder="0" allowfullscreen></iframe>
            <?php } else if(!empty($portfolio_video_provider) && $portfolio_video_provider == 'vimeo') {?>
            <iframe src="//player.vimeo.com/video/<?php echo $portfolio_video_id;?>" width="500" height="350"></iframe>
            <?php } else if(!empty($portfolio_video_provider) && $portfolio_video_provider == 'dailymotion') {?>
            <iframe frameborder="0" width="500" height="350" src="http://www.dailymotion.com/embed/video/<?php echo $portfolio_video_id;?>"></iframe>
			<?php }?>
		<?php
       }
	   elseif(get_post_format($post_ID) == 'link')//video section
	   {
			$portfolio_link_title = get_post_meta(get_the_ID(),'portfolio_link_title', true );
			$portfolio_link_url = get_post_meta(get_the_ID(),'portfolio_link_url', true );
        ?>
            <a href="<?php echo $portfolio_link_url ?>" target="_blank">
            <h1><?php echo $portfolio_link_title;?></h1>
            </a>
            
        <?php
		}
		else //standard
		{
        	$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id($post_ID), 'fullsize', false, '' ); 
			$large_image = $large_image[0]; 
			if(!empty($large_image))
			{
			$size = getimagesize($large_image);
			}
			if($size[0] < 300)
			{?>
            <img src="<?php echo $large_image; ?>" alt="<?php the_title(); ?>">
			<?php
            }
			else
			{
			?>
			<img src="<?php echo plugins_url();?>/rio-portfolio/scripts/timthumb.php?src=<?php echo $large_image; ?>&amp;w=300&amp;h=200&amp;zc=1" alt="<?php the_title(); ?>" width="300" height="200">
        <?php
			}
		}
		?>
          <figcaption><span><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago';?></span></figcaption>
        </figure>
        <article>
         
          <?php the_content(); ?>
            
            
            
        </article>
      </div>
      
      
      
      
      
      <!-- //sidebar -->

    </section>
<?php get_footer(); ?>
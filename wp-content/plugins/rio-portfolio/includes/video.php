<?php
/*-----------------------------------------------------------------------------------*/
/*	Post type: Video
/*-----------------------------------------------------------------------------------*/
?>
<?php
/*-----------------------------------------------------------------------------------*/
/*	Video Variables
/*-----------------------------------------------------------------------------------*/
//get portfolio_settings options in serialized format...
$portfolio_settings = get_option('portfolio_settings');

$data_results = unserialize($portfolio_settings); //unserialize the $video_settings array..
$portfolio_thumb_width = $data_results['portfolio_thumb_width'];
$portfolio_thumb_height = $data_results['portfolio_thumb_height'];
$portfolio_link_target = $data_results['portfolio_link_target'];
if(empty($portfolio_link_target))
{
	$portfolio_link_target='popup';
}

global $count;

$portfolio_video_provider = get_post_meta(get_the_ID(),'portfolio_video_provider', true );
$portfolio_video_id = get_post_meta(get_the_ID(),'portfolio_video_id', true );
$portfolio_video_image = get_post_meta(get_the_ID(),'portfolio_video_image', true );

?>		
<?php 

						// Get The Taxonomy 'Filter' Categories
						$terms = get_the_terms( get_the_ID(), 'portfolio-categories' ); 
					?>
							<?php
								//Apply a data-id for unique indentity, 
								//and loop through the taxonomy and assign the terms to the portfolio item to a data-type,
								// which will be referenced when writing our Quicksand Script
							?>
							<li data-id="id-<?php echo $count; ?>" data-type="<?php foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } ?>">
								
										
										<?php // Output the featured image ?>
                                        <?php if($portfolio_video_provider == 'youtube')
										{?>
										<a <?php if($portfolio_link_target == 'popup'){?> href="http://www.youtube.com/watch?v=<?php echo $portfolio_video_id;?>"  rel="prettyPhoto"<?php }else{ ?> href="<?php the_permalink();?>" <?php } ?> title="">
                                        <?php }elseif($portfolio_video_provider == 'vimeo'){?>
                                        <a <?php if($portfolio_link_target == 'popup'){?> href="http://vimeo.com/<?php echo $portfolio_video_id;?>"  rel="prettyPhoto" <?php }else{ ?> href="<?php the_permalink();?>" <?php } ?> title="">
                                        <?php }elseif($portfolio_video_provider == 'dailymotion'){?>
                                        
                                        <a <?php if($portfolio_link_target == 'popup'){?> href="http://www.dailymotion.com/embed/video/<?php echo $portfolio_video_id;?>?iframe=true&width=500&height=344"  rel="prettyPhoto" <?php }else{?>href="<?php the_permalink();?>"<?php }?> title="">
                                        <?php } ?>
                                        <?php if(!empty($portfolio_thumb_width) || !empty($portfolio_thumb_height))
										{?>
											<img src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo $portfolio_video_image; ?>&amp;w=<?php echo $portfolio_thumb_width;?>&amp;h=<?php echo $portfolio_thumb_height;?>&amp;zc=1" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>">
                                            <?php }else{?>
                                            <img src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo $portfolio_video_image; ?>&amp;w=295&amp;h=150&amp;zc=1" width="295" height="150">
                                            <?php }?>
                                            </a>									
									
									<?php // Output the title of each portfolio item ?>
									<p><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></p>
									
							</li>
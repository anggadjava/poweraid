<?php
/*-----------------------------------------------------------------------------------*/
/*	Post type: Standard
/*-----------------------------------------------------------------------------------*/
?>
<?php 
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
						// Get The Taxonomy 'Filter' Categories
						$terms = get_the_terms( get_the_ID(), 'portfolio-categories' ); 
					?>
					
					<?php 
					$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
					$large_image = $large_image[0]; 
					?>
					
							<?php
								//Apply a data-id for unique indentity, 
								//and loop through the taxonomy and assign the terms to the portfolio item to a data-type,
								// which will be referenced when writing our Quicksand Script
							?>
							<li data-id="id-<?php echo $count; ?>" data-type="<?php foreach ($terms as $term) { echo strtolower(preg_replace('/\s+/', '-', $term->name)). ' '; } ?>">
								
									<?php 
										// Check if wordpress supports featured images, and if so output the thumbnail
										if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) : 
									?>
										
										<?php // Output the featured image ?>
										<a <?php if($portfolio_link_target == 'popup'){?> rel="prettyPhoto[gallery]" href="<?php echo $large_image ?>" <?php }else{?> href="<?php the_permalink();?>" <?php } ?> >
										
										<?php if(!empty($portfolio_thumb_width) || !empty($portfolio_thumb_height))
										{?>
                                        <img src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo $large_image; ?>&amp;w=<?php echo $portfolio_thumb_width;?>&amp;h=<?php echo $portfolio_thumb_height;?>&amp;zc=1" alt="<?php the_title(); ?>" width="<?php echo $portfolio_thumb_width;?>" height="<?php echo $portfolio_thumb_height;?>">
                                        <?php }else{?>
                                        <img src="<?php bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo $large_image; ?>&amp;w=295&amp;h=150&amp;zc=1" alt="<?php the_title(); ?>" width="295" height="150">
                                        <?php } ?>
                                        </a>									
																			
									<?php endif; ?>	
									
									<?php // Output the title of each portfolio item ?>
									<p><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></p>
									
							</li>
<?php
/*-----------------------------------------------------------------------------------*/
/*	Post type: Link
/*-----------------------------------------------------------------------------------*/
//get portfolio_settings options in serialized format...
$portfolio_settings = get_option('portfolio_settings');

$data_results = unserialize($portfolio_settings); //unserialize the $video_settings array..
$portfolio_link_target = $data_results['portfolio_link_target'];
if(empty($portfolio_link_target))
{
	$portfolio_link_target='popup';
}
$portfolio_link_title = get_post_meta(get_the_ID(),'portfolio_link_title', true );
$portfolio_link_url = get_post_meta(get_the_ID(),'portfolio_link_url', true );
global $count;
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
										<a href="<?php echo $portfolio_link_url ?>">
										<h1><?php echo $portfolio_link_title;?></h1>
										
                                        </a>									
										<?php the_content();?>									
										
									<?php // Output the title of each portfolio item ?>
									<p><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></p>
									
							</li>
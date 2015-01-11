<?php get_header(); ?>




<section id="content">
	<div class="container">
		<div id="mainbody">
					
<?php 
	$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

	global $data;

	// We have SORTABLE PORTFOLIO :)
	if( $data['portfolio_style'] == 'portfolio_sortable' ) {
?>

			<div class="row hg-portfolio ">
				<div class="span12">
					
					<?php if ( !empty( $term->name ) ): ?>
						<div class="archive-description">
							<h1 class="page-title"><?php echo apply_filters( 'the_title', $term->name ); ?></h1>
						</div>
					<?php endif; ?>

					<?php if ( !empty( $term->description ) ): ?>
						<div class="archive-description">
							<?php echo esc_html($term->description); ?>
						</div>
					<?php endif; ?>
					

					
				<div class="hg-portfolio-sortable">
				
					<div id="sorting" class="fixclear">

						<span class="sortTitle"> <?php _e("Sort By:",THEMENAME);?> </span>
						<ul id="sortBy" class="option-set " data-option-key="sortBy" data-default="">
							<li><a href="#sortBy=name" data-option-value="name"><?php _e("Name",THEMENAME);?></a></li>
							<li><a href="#sortBy=date" data-option-value="date"><?php _e("Date",THEMENAME);?></a></li>
						</ul>
						
						<span class="sortTitle"> <?php _e("Direction:",THEMENAME);?> </span>
						<ul id="sort-direction" class="option-set " data-option-key="sortAscending">
							<li><a href="#sortAscending=true" data-option-value="true"><?php _e("ASC",THEMENAME);?></a></li>
							<li><a href="#sortAscending=false" data-option-value="false"><?php _e("DESC",THEMENAME);?></a></li>
						</ul>
						
					</div><!-- end sorting toolbar -->

					<ul id="portfolio-nav" class="fixclear">
						<li class="current"><a href="#" data-filter="*"><?php _e("All",THEMENAME);?></a></li>
							<?php
								$args = array( 
									//'include'=> $options['portfolio_categories'],
									);
								$terms = get_terms('project_category',$args);
								//	print_r($terms);
								foreach ( $terms as $term ) {
								   echo '<li><a href="#" data-filter=".'.strtolower(str_replace(' ','_',$term->name)).'">'.$term->name.'</a></li>';
								}
								
							?>

					</ul><!-- end nav toolbar -->

					<div class="clear"></div>
				
					<ul id="thumbs" class="fixclear">
					
						<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
						
							<li class="item <?php echo strtolower(str_replace('|zn_sep|',' ',str_replace(' ','_',strip_tags ( get_the_term_list( $post->ID, 'project_category', '', '|zn_sep|', '' ) ) ) ) );?> even" data-date="<?php the_time('U'); ?>">
								<div class="inner-item">
								
								<?php
								
									// Get post meta information
									$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
									$post_meta_fields = maybe_unserialize( $post_meta_fields );
									
										if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
											

											// COMBINED 											
											if ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) && !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
												
												$size = zn_get_size( 'portfolio_sortable' );

												$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],$size['height'] , true );
												
												if ( !empty( $data['zn_link_portfolio'] ) && $data['zn_link_portfolio'] ==  'yes' ){
													echo '<a data-type="video" href="'.get_permalink().'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';
												}
												else {
													echo '<a data-rel="prettyPhoto" data-type="video" href="'.$post_meta_fields['port_media']['0']['port_media_video_comb'].'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';
												}

														

											}
											// VIDEO
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
												
													$size = zn_get_size( 'portfolio_sortable' );
													echo get_video_from_link( $post_meta_fields['port_media']['0']['port_media_video_comb'] , '' , $size['width'] , $size['height'] );
														
											}
											// IMAGE 											
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) ) {
										
												$size = zn_get_size( 'portfolio_sortable' );

												$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],$size['height'] , true );
												
												if ( !empty( $data['zn_link_portfolio'] ) && $data['zn_link_portfolio'] ==  'yes' ){
													echo '<a data-type="image" href="'.get_permalink().'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';
												}
												else {
													echo '<a data-rel="prettyPhoto" data-type="image" href="'.$post_meta_fields['port_media']['0']['port_media_image_comb'].'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';
												}



											}	
										}
								?>
								

									<h4 class="title">
										<a href="<?php the_permalink(); ?>"><span class="name"><?php the_title(); ?></span></a>
									</h4>
									<span class="moduleDesc">
										<?php 

											$excerpt = get_the_excerpt();
											$excerpt = strip_shortcodes($excerpt);
											$excerpt = strip_tags($excerpt);
											$the_str = substr($excerpt, 0, 116);
											echo $the_str.'...';

										?>
									</span>
									<div class="clear"></div>
								</div><!-- end ITEM (.inner-item) -->
							</li>
						<?php endwhile; ?>
						<?php endif; ?>
					
						
					</ul><!-- end items list -->
				
				</div><!-- end Portfolio page -->
				</div>
					</div><!-- end row -->

<?php } 
		elseif ( $data['portfolio_style'] == 'portfolio_carousel' ) {
			?>
				<div class="row">
						<div class="span12">
							<div class="row hg-portfolio-carousel">

							<?php 
								$i = 1;
								if ( have_posts() ): while ( have_posts() ): the_post(); 
								// Get post meta information
								$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
								$post_meta_fields = maybe_unserialize( $post_meta_fields );
							?>
								<div class="span6">
									<div class="ptcontent">
										<h3 class="title">
											<a href="<?php the_permalink(); ?>"><span class="name"><?php the_title(); ?></span></a>
										</h3>
										<div class="pt-cat-desc">
											<?php
												if ( preg_match('/<!--more(.*?)?-->/', $post->post_content) ) {
													
													the_content('');
												}
												else {
													
													the_excerpt();
												}
											?>
										</div><!-- end item desc -->
										<div class="itemLinks">
											<?php
												if ( !empty ( $post_meta_fields['sp_link']['url'] ) ) {
													echo '<span><a href="'.$post_meta_fields['sp_link']['url'].'" target="'.$post_meta_fields['sp_link']['target'].'" >'.__("Live Preview: ",THEMENAME).'<strong>'.$post_meta_fields['sp_link']['url'].'</strong></a></span>';
												}
											?>
											<span class="seemore">
												<a href="<?php the_permalink(); ?>" ><?php _e('See more &rarr;',THEMENAME);?></a>
											</span>
										</div><!-- end item links -->
									</div><!-- end item content -->
								</div>
								<div class="span6">
									<div class="ptcarousel">
										<div class="controls">
											<a href="#" class="prev"><span class="icon-chevron-left icon-white"></span></a>
											<a href="#" class="next"><span class="icon-chevron-right icon-white"></span></a>
										</div>
										<ul class="ptcarousel1">
										<?php

											if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
											
										
											
												foreach ( $post_meta_fields['port_media'] as $media ) {
													// COMBINED
													if ( !empty ( $media['port_media_image_comb'] ) && !empty ( $media['port_media_video_comb'] ) ) {
														echo '<li>';
															echo '<a href="'.$media['port_media_video_comb'].'" rel="prettyPhoto" data-type="image">';
																$size = zn_get_size( 'eight' );
																$image = vt_resize( '', $media['port_media_image_comb'] , $size['width'],$size['height'] , true );
																echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
															echo '</a>';
														echo '</li>';
														}			
														// IMAGE								
														elseif ( !empty ( $media['port_media_image_comb'] ) ) {
														echo '<li>';
															echo '<a href="'.$media['port_media_image_comb'].'" data-type="image" rel="prettyPhoto">';
																	$size = zn_get_size( 'eight' );
																$image = vt_resize( '', $media['port_media_image_comb'] , $size['width'],$size['height'] , true );
																echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
															echo '</a>';
														echo '</li>';
														}		
														// VIDEO									
														elseif ( !empty ( $media['port_media_video_comb'] ) ) {
															echo '<li>';

															$size = zn_get_size( 'eight' );
															echo get_video_from_link( $media['port_media_video_comb'] , '' , $size['width'] , $size['height'] );

															echo '</li>';
														}
					
												}

											}


										?>
										</ul>
									</div><!-- end ptcarousel -->
								</div>


								<?php
									if ( $i % $data['portfolio_per_page_show'] != 0 ) {

										echo '<div class="span12"><hr class="bs-docs-separator"></div>';

									}

									$i++;
								?>


							<?php endwhile; ?>
							<?php endif; ?>
								
							
							</div><!-- end portfolio layout -->
							<?php
								echo '<div class="clear"></div>';
								echo '<div class="span12" >';
									zn_pagination(); 
									wp_reset_query();
								echo '</div>';

								

							?>
						</div>
						</div>
	<?php 
	} 
	elseif ( $data['portfolio_style'] == 'portfolio_category' ) {

			if ( $data['ports_num_columns'] == '1' ) {
				echo '<div class="span12">';

				// Start the loop
				while( have_posts() ){
					
					the_post(); 
					
					// Get post meta information
					$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
					$post_meta_fields = maybe_unserialize( $post_meta_fields );
					



					echo '<div class="row">';
					echo '<div class="span6">';
					echo '<div class="img-intro">';
					
						if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
							
							// COMBINED 											
							if ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) && !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
								
										$size = zn_get_size( 'eight' );

										$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],$size['height'] , true );
										echo '<a data-rel="prettyPhoto" data-type="video" href="'.$post_meta_fields['port_media']['0']['port_media_video_comb'].'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';

							}
							// VIDEO
							elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
								
									$size = zn_get_size( 'eight' );
									echo get_video_from_link( $post_meta_fields['port_media']['0']['port_media_video_comb'] , '' , $size['width'] , $size['height'] );
										
							}
							// IMAGE 											
							elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) ) {
						
								$size = zn_get_size( 'eight' );

								$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],$size['height'] , true );
								echo '<a data-rel="prettyPhoto" data-type="image" href="'.$post_meta_fields['port_media']['0']['port_media_image_comb'].'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';

							}	
						}

					echo '</div><!-- img intro -->';
					echo '</div>';
						

					echo '<div class="span6">';
					echo '<h3 class="title">';
					echo '<a href="'.get_permalink().'" >'.get_the_title().'</a>';
					echo '</h3>';
					echo '<div class="pt-cat-desc">';

						the_content();

					echo '</div><!-- pt cat desc -->';
					echo '</div>';
					echo '</div><!-- end row -->';
					
					
				}

				echo '<div class="row zn_content_no_margin">';
				echo '<div class="span12">';
					zn_pagination(); 
					wp_reset_query();
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
			else {

				$proper_size = 12/$data['ports_num_columns'];
				$i = 1;
				echo '<div class="span12">';
				echo '<div class="row zn_content_no_margin">';

				// Start the loop
				while( have_posts() ){
					
					the_post(); 
					
					// Get post meta information
					$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
					$post_meta_fields = maybe_unserialize( $post_meta_fields );
					
					echo '<div class="span'.$proper_size.'">';
					echo '<div class="img-intro">';
					

						if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
							
							// COMBINED 											
							if ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) && !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
								
										$size = zn_get_size( 'span'.$proper_size );

										$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],$size['height'] , true );
										echo '<a data-rel="prettyPhoto" data-type="video" href="'.$post_meta_fields['port_media']['0']['port_media_video_comb'].'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';

							}
							// VIDEO
							elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
								
									$size = zn_get_size( 'span'.$proper_size );
									echo get_video_from_link( $post_meta_fields['port_media']['0']['port_media_video_comb'] , '' , $size['width'] , $size['height'] );
										
							}
							// IMAGE 											
							elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) ) {
						
								$size = zn_get_size( 'span'.$proper_size );

								$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],$size['height'] , true );
								echo '<a data-rel="prettyPhoto" data-type="image" href="'.$post_meta_fields['port_media']['0']['port_media_image_comb'].'" class="hoverLink" ><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" /></a>';

							}	
						}

						
					echo '</div><!-- img intro -->';
					
						

					
					echo '<h3 class="title">';
					echo '<a href="'.get_permalink().'" >'.get_the_title().'</a>';
					echo '</h3>';
					echo '<div class="pt-cat-desc">';

						if ( preg_match('/<!--more(.*?)?-->/', $post->post_content) ) {
							
							the_content('');
						}
						else {
							
							the_excerpt();
						}

					echo '</div><!-- pt cat desc -->';
					echo '</div>';

					if ( $i % $data['ports_num_columns'] == 0 && $i % $data['portfolio_per_page_show'] != 0 ) {

						echo '<div class="row"><div class="span12"><hr class="bs-docs-separator"></div></div>';

					}

					$i++;
					
				}

				echo '<div class="clear"></div>';

				echo '<div class="row"></div>';
				echo '<div class="span12" >';
					zn_pagination(); 
					wp_reset_query();
				echo '</div>';

				echo '</div><!-- end row -->';
				echo '</div>';
			}
	}

	?>






					
				</div><!-- end mainbody -->
				
			</div><!-- end container -->
		</section><!-- end #content -->

<?php get_footer(); ?>
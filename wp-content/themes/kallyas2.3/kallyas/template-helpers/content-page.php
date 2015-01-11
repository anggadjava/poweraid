<?php

/*--------------------------------------------------------------------------------------------------
	Content and Sidebar
--------------------------------------------------------------------------------------------------*/
	function _content_sidebar( $options )
	{

		global $post,$data;
		$element_size = zn_get_size( $options['_sizer'] );


		// WE HAVE NORMAL POST TYPE
		if ( 'post' == get_post_type() ) {
		?>
		<div class="span12">
		<div class="container">
			
			<div id="mainbody">
				
				<div class="row">
				<?php while(have_posts()) : the_post(); 

					// GET POST OPTIONS 
					$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
					$post_meta_fields = maybe_unserialize( $post_meta_fields );
					
					$image = '';
					
					// Create the featured image html
					if ( has_post_thumbnail( $post->ID ) ) {
					
						$thumb = get_post_thumbnail_id($post->ID) ;
						$f_image = wp_get_attachment_url($thumb) ;
						if (  $f_image  ) {
						
							$feature_image = wp_get_attachment_url( $thumb );
							$image = vt_resize( '', $f_image  , 280,187 , true );
							$image = '<a data-rel="prettyPhoto" href="'.$feature_image.'" class="hoverBorder pull-left" style="margin-right: 20px;margin-bottom:4px;"><img class="shadow" src="'.$image['url'].'" alt=""/></a>';
							
						}

					}
					
					// Here will check if sidebar is enabled
					$content_css = 'span12'; 
					$sidebar_css = ''; 
					$has_sidebar = false;
					$mainbody_css = '';
					
					// WE CHECK IF THIS IS NOT A PAGE FROM OUR THEME	
					if ( empty ( $post_meta_fields['page_layout'] ) || empty ( $post_meta_fields['sidebar_select'] ) ) {
						if ( $data['default_sidebar_position'] == 'left_sidebar' ) {
							$content_css = 'span9 zn_float_right zn_content';
							$sidebar_css = 'sidebar-left';
							$has_sidebar = true;
							$mainbody_css = 'zn_has_sidebar';
						}
						elseif ( $data['default_sidebar_position'] == 'right_sidebar' ) {
							$content_css = 'span9 zn_content';
							$sidebar_css = 'sidebar-right';
							$has_sidebar = true;
							$mainbody_css = 'zn_has_sidebar';
						}
					}	
					// WE CHECK IF WE HAVE LEFT SIDEBAR
					elseif ( $post_meta_fields['page_layout'] == 'left_sidebar' || ( $post_meta_fields['page_layout'] == 'default' && !empty ( $data['default_sidebar_position'] ) && $data['default_sidebar_position'] == 'left_sidebar' )   )
					{
						$content_css = 'span9 zn_float_right zn_content';
						$sidebar_css = 'sidebar-left';
						$has_sidebar = true;
						$mainbody_css = 'zn_has_sidebar';
					}
					// WE CHECK IF WE HAVE RIGHT SIDEBAR
					elseif ( $post_meta_fields['page_layout'] == 'right_sidebar' || ( $post_meta_fields['page_layout'] == 'default' && !empty ( $data['default_sidebar_position'] ) && $data['default_sidebar_position'] == 'right_sidebar' )  )
					{
						$content_css = 'span9 zn_content';
						$sidebar_css = 'sidebar-right ';
						$has_sidebar = true;
						$mainbody_css = 'zn_has_sidebar';
					}	
				
				?>
				
					<div class="<?php echo $content_css; ?> post-<?php the_ID(); ?>">
				
						<h1 class="page-title"><?php the_title();?></h1>

						<div class="itemView clearfix eBlog">

							<div class="itemHeader">
								<div class="post_details">
									<span class="itemAuthor"><?php echo __("by ", THEMENAME);?><?php the_author_posts_link(); ?></span>
									<span class="infSep"> / </span>
									<span class="itemDateCreated"><span class="icon-calendar"></span> <?php the_time('l, d F Y');?></span>
									<span class="infSep"> / </span>
									<span class="itemCommentsBlock"></span>
									<span class="itemCategory"><span class="icon-folder-close"></span> <?php echo __( 'Published in ', THEMENAME );?></span><?php the_category(", ");  ?>
								</div>
							</div><!-- end itemheader -->

							<div class="itemBody">
								<!-- Blog Image -->
								<?php echo $image; ?>
								
								<!-- Blog Content -->
								<?php the_content(); ?>
								
							</div><!-- end item body -->
							<div class="clear"></div>

						<?php
						
						wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', THEMENAME ) . '</span>', 'after' => '</div>' ) );
						
							if ( !empty($post_meta_fields['show_social']) && $post_meta_fields['show_social'] == 'show' || ( !empty($post_meta_fields['show_social']) &&  $post_meta_fields['show_social'] == 'default' && $data['show_social'] == 'show' ) ) {
							
							
						?>
							
							
							
								<!-- Social sharing -->
								<div class="itemSocialSharing clearfix">

									<!-- Twitter Button -->
									<div class="itemTwitterButton">
										<a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
										<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
									</div>

									<!-- Facebook Button -->
									<div class="itemFacebookButton">
										<div id="fb-root"></div>
										<div class="fb-like" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false"></div>
									</div>

									<!-- Google +1 Button -->
									<div class="itemGooglePlusOneButton">	
										<script type="text/javascript">
										(function() {
										var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
										po.src = 'https://apis.google.com/js/plusone.js';
										var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
										})();
										</script>
										<div class="g-plusone" data-size="medium"></div>
									</div>

									<div class="clear"></div>
								</div><!-- end social sharing -->
							<?php
							}
						?>

							<?php	
								if (has_tag()) { 
								?>
									<!-- TAGS -->
									<div class="itemTagsBlock">
										<span><?php echo __( 'Tagged under:', THEMENAME );?></span>
										<?php the_tags('');?>
										<div class="clear"></div>
									</div><!-- end tags blocks -->
							<?php	
								}	
								?>	
														
							<div class="clear"></div>
								
						<!-- DISQUS comments block -->
						<div class="disqusForm">
							<?php comments_template(); ?>
						</div>
						<div class="clear"></div>
						<!-- end DISQUS comments block -->

						</div>
						<!-- End Item Layout -->
						
						
						
					</div>
					
					<?php 
					endwhile;
					
					// START SIDEBAR OPTIONS
					// WE CHECK IF THIS IS NOT A PAGE FROM THE THEME
					if ( empty ( $post_meta_fields['page_layout'] ) || empty ( $post_meta_fields['sidebar_select'] ) ) {
						if ( $data['default_sidebar_position'] == 'left_sidebar' || $data['default_sidebar_position'] == 'right_sidebar' ) {
							echo '<div class="span3">';
								echo '<div id="sidebar" class="sidebar '.$sidebar_css.'">';
									if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($data['single_sidebar']) ) : endif;
								echo '</div>';
							echo '</div>';
						}
					}
					// WE CHECK IF WE HAVE A SIDEBAR SET IN PAGE OPTIONS
					elseif ( ( ( $post_meta_fields['page_layout'] == 'left_sidebar' || $post_meta_fields['page_layout'] == 'right_sidebar' ) && $post_meta_fields['sidebar_select'] != 'default' ) || (  $post_meta_fields['page_layout'] == 'default' && $data['default_sidebar_position'] != 'no_sidebar' && $post_meta_fields['sidebar_select'] != 'default' ) ) 
					{ 
							
								echo '<div class="span3">';
									echo '<div id="sidebar" class="sidebar '. $sidebar_css.'">';
										if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( $post_meta_fields['sidebar_select'] ) ) : endif;
									echo '</div>';
								echo '</div>';
					}
					// WE CHECK IF WE HAVE A SIDEBAR SET FROM THEME'S OPTIONS
					elseif ( $post_meta_fields['page_layout'] == 'default' && $data['default_sidebar_position'] != 'no_sidebar' && $post_meta_fields['sidebar_select'] == 'default' || ( ( $post_meta_fields['page_layout'] == 'left_sidebar' || $post_meta_fields['page_layout'] == 'right_sidebar' ) && $post_meta_fields['sidebar_select'] == 'default' ) ) {
						echo '<div class="span3">';
							echo '<div id="sidebar" class="sidebar '.$sidebar_css.'">';
								if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($data['single_sidebar']) ) : endif;
							echo '</div>';
						echo '</div>';
					}
					
					?>
				</div><!-- end row -->
				
			</div><!-- end mainbody -->
			
		</div><!-- end container -->
		</div><!-- end span12 -->


		<?php

		}
		elseif ( 'page' == get_post_type() ) {
	
			global $post,$data,$meta_fields;

			// Here will check if sidebar is enabled
			$content_css = 'span12'; 
			$sidebar_css = ''; 
			$has_sidebar = false;
			$mainbody_css = '';
				
			// WE CHECK IF THIS IS NOT A PAGE FROM OUR THEME	
			if ( empty ( $meta_fields['page_layout'] ) || empty ( $meta_fields['sidebar_select'] ) ) {
				if ( $data['page_sidebar_position'] == 'left_sidebar' ) {
					$content_css = 'span9 zn_float_right zn_content';
					$sidebar_css = 'sidebar-left';
					$has_sidebar = true;
					$mainbody_css = 'zn_has_sidebar';
				}
				elseif ( $data['page_sidebar_position'] == 'right_sidebar' ) {
					$content_css = 'span9 zn_content';
					$sidebar_css = 'sidebar-right';
					$has_sidebar = true;
					$mainbody_css = 'zn_has_sidebar';
				}
			}	
			// WE CHECK IF WE HAVE LEFT SIDEBAR
			elseif ( $meta_fields['page_layout'] == 'left_sidebar' || ( $meta_fields['page_layout'] == 'default' && !empty ( $data['page_sidebar_position'] ) && $data['page_sidebar_position'] == 'left_sidebar' )   )
			{
				$content_css = 'span9 zn_float_right zn_content';
				$sidebar_css = 'sidebar-left';
				$has_sidebar = true;
				$mainbody_css = 'zn_has_sidebar';
			}
			// WE CHECK IF WE HAVE RIGHT SIDEBAR
			elseif ( $meta_fields['page_layout'] == 'right_sidebar' || ( $meta_fields['page_layout'] == 'default' && !empty ( $data['page_sidebar_position'] ) && $data['page_sidebar_position'] == 'right_sidebar' )  )
			{
				$content_css = 'span9 zn_content';
				$sidebar_css = 'sidebar-right ';
				$has_sidebar = true;
				$mainbody_css = 'zn_has_sidebar';
			}

			while (have_posts()) : the_post();
			
			$content = get_the_content();
			$content = apply_filters('the_content', $content);
			if ( !empty($content) || ( isset ( $meta_fields['page_title_show'] ) && $meta_fields['page_title_show'] == 'yes' ) ) {

				echo '<div class="span12">';
				$row_margin = 'zn_content_no_margin';
			
				if ( get_the_content() || $has_sidebar ) {
					$row_margin = '';
				}
			
				echo '<div class="container">';
				
				echo '<div class="mainbody '.$mainbody_css.'">';
				
						echo '<div class="row '.$row_margin.'">';
						
							echo '<div class="'.$content_css.'">';
						
								// TITLE CHECK
								if ( isset ( $meta_fields['page_title_show'] ) && $meta_fields['page_title_show'] == 'yes' ) {
									echo '<h1 class="page-title">'.get_the_title().'</h1>';
								}
								
								// PAGE CONTENT
								the_content();

								if ( !empty($data['zn_enable_page_comments']) && $data['zn_enable_page_comments'] == 'yes'  ) {
									?>
									<!-- DISQUS comments block -->
									<div class="disqusForm">
										<?php comments_template(); ?>
									</div>
									<div class="clear"></div>
									<!-- end DISQUS comments block -->
									<?php
								}

							echo '</div>';

							
							// START SIDEBAR OPTIONS
							// WE CHECK IF THIS IS NOT A PAGE FROM THE THEME
							if ( empty ( $meta_fields['page_layout'] ) || empty ( $meta_fields['sidebar_select'] ) ) {
								if ( $data['page_sidebar_position'] == 'left_sidebar' || $data['page_sidebar_position'] == 'right_sidebar' ) {
									echo '<div class="span3">';
										echo '<div id="sidebar" class="sidebar '.$sidebar_css.'">';
											if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($data['page_sidebar']) ) : endif;
										echo '</div>';
									echo '</div>';
								}
							}
							// WE CHECK IF WE HAVE A SIDEBAR SET IN PAGE OPTIONS
							elseif ( ( ( $meta_fields['page_layout'] == 'left_sidebar' || $meta_fields['page_layout'] == 'right_sidebar' ) && $meta_fields['sidebar_select'] != 'default' ) || (  $meta_fields['page_layout'] == 'default' && $data['page_sidebar_position'] != 'no_sidebar' && $meta_fields['sidebar_select'] != 'default' ) ) 
							{ 
									
										echo '<div class="span3">';
											echo '<div id="sidebar" class="sidebar '. $sidebar_css.'">';
												if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( $meta_fields['sidebar_select'] ) ) : endif;
											echo '</div>';
										echo '</div>';
							}
							// WE CHECK IF WE HAVE A SIDEBAR SET FROM THEME'S OPTIONS
							elseif ( $meta_fields['page_layout'] == 'default' && $data['page_sidebar_position'] != 'no_sidebar' && $meta_fields['sidebar_select'] == 'default' || ( ( $meta_fields['page_layout'] == 'left_sidebar' || $meta_fields['page_layout'] == 'right_sidebar' ) && $meta_fields['sidebar_select'] == 'default' ) ) {
								echo '<div class="span3">';
									echo '<div id="sidebar" class="sidebar '.$sidebar_css.'">';
										if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($data['page_sidebar']) ) : endif;
									echo '</div>';
								echo '</div>';
							}
			

						echo '</div>';
				
					echo '</div>';
					
				echo '</div>';
				echo '</div>';
				
				}
			endwhile;
		}
		elseif( 'portfolio' == get_post_type() ) {
			global $post,$data;
			?>
			<div class="span12">
			<div class="container">
				
				<div id="mainbody">
					
					<div class="row">
						<div class="span12">
						
							<?php while(have_posts()) : the_post(); 
							
								// GET POST OPTIONS 
								$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
								$post_meta_fields = maybe_unserialize( $post_meta_fields );
									
								// Sidebar check 
								$has_sidebar = false;
									
								// TITLE CHECK
								if ( isset ( $post_meta_fields['page_title_show'] ) && $post_meta_fields['page_title_show'] == 'yes' ) {
									echo '<h1 class="page-title">'.get_the_title().'</h1>';
								}
									
							?>

							<div class="hg-portfolio-item row">
                                
                                <div class="text span7">
									<?php the_content(''); ?>
                                </div><!-- end text -->
								
                                <div class="img-full span5">
								
								<?php

									if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
										
										$all_media = count( $post_meta_fields['port_media'] );
										if ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) && !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
											echo '<a href="'.$post_meta_fields['port_media']['0']['port_media_video_comb'].'" rel="prettyPhoto" class="hoverBorder">';
													$size = zn_get_size( 'span5' );
													$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],'' , true );
													echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
													echo '</a>';
													unset( $post_meta_fields['port_media']['0'] );
											}										
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) ) {
												echo '<a href="'.$post_meta_fields['port_media']['0']['port_media_image_comb'].'" rel="prettyPhoto" class="hoverBorder">';
													$size = zn_get_size( 'span5');
													$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],'' , true );
													echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
												echo '</a>';
												unset( $post_meta_fields['port_media']['0'] );										
											}										
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
											$size = zn_get_size( 'span5');
											echo get_video_from_link( $post_meta_fields['port_media']['0']['port_media_video_comb'] , '' , $size['width'] , $size['height'] );
													unset( $post_meta_fields['port_media']['0'] );										}	
									}
								?>	
                                    <div class="clear"></div>
                                
								<?php
									if ( !empty ( $post_meta_fields['sp_link']['url'] ) || !empty ( $post_meta_fields['sp_col'] ) ) { 
									
										echo '<div class="itemLinks">';
									
											if ( !empty ( $post_meta_fields['sp_link']['url'] ) ) {
												echo '<p><a href="'.$post_meta_fields['sp_link']['url'].'" target="'.$post_meta_fields['sp_link']['target'].'" >'.__("Live Preview: ",THEMENAME).'<strong>'.$post_meta_fields['sp_link']['url'].'</strong></a></p>';
											}
											
											if ( !empty ( $post_meta_fields['sp_col'] ) ) {
												echo '<p>'.__("Our collaborators: ",THEMENAME).'<strong>'.$post_meta_fields['sp_col'].'</strong></p>';
											}
											
											echo '<p>'.__("Category: ",THEMENAME).'<strong>'. get_the_term_list( $post->ID, 'project_category', '', ' , ', '' ) .'</strong></p>';
											
										echo '</div>';
										
									}
								?>
                                
								<?php 
									if ( !empty ( $post_meta_fields['sp_show_social'] ) && $post_meta_fields['sp_show_social'] == 'yes' ) {
								?>
                                    <div class="itemSocialSharing fixclear">
                                        
                                        <!-- Twitter Button -->
                                        <div class="itemTwitterButton">
                                            <a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a>
                                            <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                                        </div>
                                        
                                        <!-- Facebook Button -->
                                        <div class="itemFacebookButton">
                                            <div id="fb-root"></div>
                                            <script type="text/javascript">
                                                (function(d, s, id) {
                                                var js, fjs = d.getElementsByTagName(s)[0];
                                                if (d.getElementById(id)) {return;}
                                                js = d.createElement(s); js.id = id;
                                                js.src = "http://connect.facebook.net/en_US/all.js#appId=177111755694317&xfbml=1";
                                                fjs.parentNode.insertBefore(js, fjs);
                                                }(document, 'script', 'facebook-jssdk'));
                                            </script>
                                            <div class="fb-like" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false"></div>
                                        </div>
                                        
                                        <!-- Google +1 Button -->
                                        <div class="itemGooglePlusOneButton">	
                                            <g:plusone size="medium"></g:plusone>
                                            <script type="text/javascript">
                                                (function() {
                                                window.___gcfg = {lang: 'en'}; // Define button default language here
                                                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                                po.src = 'https://apis.google.com/js/plusone.js';
                                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                                                })();
                                            </script>
                                        </div>
                                        
                                        <div class="clr"></div>
                                    </div><!-- social links -->
                                
								<?php
									}
								?>
								
                                </div><!-- right side -->


                                <div class="clear"></div>

								<?php
									if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
									
										echo '<div class="zn_other_images">';
									
										foreach ( $post_meta_fields['port_media'] as $media ) {
											if ( !empty ( $media['port_media_image_comb'] ) && !empty ( $media['port_media_video_comb'] ) ) {
												echo '<div class="span3">';
													echo '<a href="'.$media['port_media_video_comb'].'" rel="prettyPhoto" class="hoverBorder">';

															$size = zn_get_size( 'four' );
														$image = vt_resize( '', $media['port_media_image_comb'] , $size['width'],'202' , true );
														echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
													echo '</a>';
												echo '</div>';
												}											elseif ( !empty ( $media['port_media_image_comb'] ) ) {
												echo '<div class="span3">';
													echo '<a href="'.$media['port_media_image_comb'].'" rel="prettyPhoto" class="hoverBorder">';

															$size = zn_get_size( 'four');
														$image = vt_resize( '', $media['port_media_image_comb'] , $size['width'],'202' , true );
														echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
													echo '</a>';
												echo '</div>';
												}											elseif ( !empty ( $media['port_media_video_comb'] ) ) {
												echo '<div class="span3">';

													$size = zn_get_size( 'four');
													echo get_video_from_link( $media['port_media_video_comb'] , '' , $size['width'] , 202 );

													echo '</div>';
}
			
										}
										
										echo '<div class="clear"></div>';
										echo '</div>';
									}
								?>
								
                            
                            </div><!-- end Portfolio page -->
							
							<?php endwhile; wp_reset_query(); ?>
							
						</div>
					</div><!-- end row -->
					
				</div><!-- end mainbody -->
				
			</div><!-- end container -->
			</div><!-- end container -->
			<?php
		}
	}

/*--------------------------------------------------------------------------------------------------
	Sidebar
--------------------------------------------------------------------------------------------------*/
	function _zn_sidebar( $options )
	{
		$element_size = zn_get_size( $options['_sizer'] );

		$sidebar_css = '';

		if ( $options['sidebar_bg'] == 'no' ) {
			$sidebar_css = 'no_bg';
		}

	?>

		<div class="<?php echo $element_size['sizer']; ?>">
			<?php
				echo '<div id="sidebar" class="sidebar '.$sidebar_css.'">';
					if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( $options['sidebar_select'] ) ) : endif;
				echo '</div>';
			?>
		</div>
	<?php
	}


/*--------------------------------------------------------------------------------------------------
	Limited Offers
--------------------------------------------------------------------------------------------------*/
	function _photo_gallery( $options )
	{
		$element_size = zn_get_size( $options['_sizer'] );
	?>

		<div class="<?php echo $element_size['sizer']; ?>">
			<div class="row zn_image_gallery">
			
				<?php

					if ( !empty ( $options['single_photo_gallery'] ) && is_array($options['single_photo_gallery']) ) {

						$id = uniqid('pp_');
						foreach ($options['single_photo_gallery'] as $image) {
							echo '<div class="span2">';

								if ( !empty ( $image['spg_image'] ) && !empty( $image['spg_video'] ) ) {

									$size = zn_get_size( 'span2' );
									$image_resized = vt_resize( '', $image['spg_image'] , $size['width'],$size['height'] , true );

									echo '<a rel="prettyPhoto['.$id.']" href="'.$image['spg_video'].'" title="'.$image['spg_title'].'" class="hoverBorder">';
										echo '<img alt="" src="'.$image_resized['url'].'" width="'.$image_resized['width'].'" height="'.$image_resized['height'].'">';
									echo '</a>';

								}
								elseif ( !empty ( $image['spg_image'] ) ){
									$size = zn_get_size( 'span2' );
									$image_resized = vt_resize( '', $image['spg_image'] , $size['width'],$size['height'] , true );
									echo '<a rel="prettyPhoto['.$id.']" href="'.$image['spg_image'].'" title="'.$image['spg_title'].'" class="hoverBorder">';
										echo '<img alt="" src="'.$image_resized['url'].'" width="'.$image_resized['width'].'" height="'.$image_resized['height'].'">';
									echo '</a>';

								}



							echo '</div>';
						}

					}

				?>
			

			</div>
		</div>

	<?php
	}

/*--------------------------------------------------------------------------------------------------
	Limited Offers
--------------------------------------------------------------------------------------------------*/
	function _shop_features( $options )
	{
		
	?>

		<div class="span12">
			<div class="row shop-features">
			
				<?php
					if ( !empty ( $options['sf_title'] ) ) {
						echo '<div class="span3">';
							echo '<h3 class="title">'.$options['sf_title'].'</h3>';
						echo '</div>';
					}
					
					if ( isset ( $options['sf_single'] ) && is_array ( $options['sf_single'] ) ) {
						
						foreach ( $options['sf_single'] as $single ) {
							
							echo '<div class="span3">';
								$link_start = '';
								$link_end = '';
							
							if ( !empty ( $single['lp_link']['url'] ) ) {
								$link_start = '<a href="'.$single['lp_link']['url'].'" target="'.$single['lp_link']['target'].'">';
								$link_end = '</a>';
							}
							
								echo $link_start;
								echo '<div class="shop-feature">';
								
									if ( !empty ( $single['lp_single_logo'] ) ) {
										echo '<img src="'.$single['lp_single_logo'].'" alt="">';
									}
								
									if ( !empty ( $single['lp_single_line1'] ) ) {
										echo '<h4>'.$single['lp_single_line1'].'</h4>';
									}
								
									if ( !empty ( $single['lp_single_line2'] ) ) {
										echo '<h5>'.$single['lp_single_line2'].'</h5>';
									}
								
								echo '</div><!-- end shop feature -->';
								echo $link_end;
								
							echo '</div>';
							
						}
						
					}
					
				?>
			

			</div>
		</div>

	<?php
	}

/*--------------------------------------------------------------------------------------------------
	Limited Offers
--------------------------------------------------------------------------------------------------*/
	function _video_box( $options )
	{
		$element_size = zn_get_size( $options['_sizer'] );
	?>
		<div class="<?php echo $element_size['sizer']; ?>">
		
		<?php
			
			if ( !empty ( $options['vb_video_image'] ) && !empty ( $options['vb_video_url'] ) ) {
				
				echo '<div class="adbox video">';
					$image = vt_resize( '', $options['vb_video_image'] , $element_size['width'],'' , true );
					echo '<img src="'.$image['url'].'" alt="">';
						echo '<div class="video_trigger_container">';
							echo '<a class="playVideo" data-rel="prettyPhoto" href="'.$options['vb_video_url'].'?width=80%&amp;height=80%"></a>';
							echo $options['vb_video_title'];
						echo '</div>';
				echo '</div>';
				
			}
			else {
				
				if ( !empty ( $options['vb_video_url'] ) ) {
					echo get_video_from_link($options['vb_video_url'], '',$element_size['width'],$element_size['height']);
				}
				
			}
		
		?>

		</div>
	<?php
	}

/*--------------------------------------------------------------------------------------------------
	Limited Offers
--------------------------------------------------------------------------------------------------*/
	function _woo_limited( $options )
	{
		
		global $woocommerce;

		if ( empty($woocommerce) ) { return; }

		$element_size = zn_get_size( $options['_sizer'] );

	?>
		<div class="<?php echo $element_size['sizer'];?>">
		
		<?php
			if ( !empty ( $options['woo_lo_title'] ) ) {
				echo '<h3 class="m_title">'.$options['woo_lo_title'].'</h3>';
			}
		?>
		
			
			<div class="limited-offers-carousel fixclear">
				<ul id="limited_offers">
				
					<?php
					
					// Get products on sale
					if ( false === ( $product_ids_on_sale = get_transient( 'wc_products_onsale' ) ) ) {

						$meta_query = array();

						$meta_query[] = array(
							'key' => '_sale_price',
							'value' 	=> 0,
							'compare' 	=> '>',
							'type'		=> 'NUMERIC'
						);

						$on_sale = get_posts(array(
							'post_type' 		=> array('product', 'product_variation'),
							'posts_per_page' 	=> -1,
							'post_status' 		=> 'publish',
							'meta_query' 		=> $meta_query,
							'fields' 			=> 'id=>parent'
						));

						$product_ids 	= array_keys( $on_sale );
						$parent_ids		= array_values( $on_sale );

						// Check for scheduled sales which have not started
						foreach ( $product_ids as $key => $id )
							if ( get_post_meta( $id, '_sale_price_dates_from', true ) > current_time('timestamp') )
								unset( $product_ids[ $key ] );

						$product_ids_on_sale = array_unique( array_merge( $product_ids, $parent_ids ) );

						set_transient( 'wc_products_onsale', $product_ids_on_sale );

					}
					
					
					$product_ids_on_sale[] = 0;

					$meta_query = array();
					$meta_query[] = $woocommerce->query->visibility_meta_query();
					$meta_query[] = $woocommerce->query->stock_status_meta_query();
				
					if ( empty ( $options['woo_categories'] ) ) { $options['woo_categories'] = ''; }
				
					$query_args = array('posts_per_page' => $options['prods_per_page'],
						'tax_query' => array (
											array (
												'taxonomy' => 'product_cat',
												'field' => 'id',
												'terms' => $options['woo_categories']
												)
										),
						'no_found_rows' => 1, 
						'post_status' => 'publish', 
						'post_type' => 'product',
						'orderby' 		=> 'date',
						'order' 		=> 'ASC',
						'meta_query' 	=> $meta_query,
						'post__in'		=> $product_ids_on_sale
					);	

					
					$r = new WP_Query($query_args);
					

					if ($r->have_posts()) {
					
						while ($r->have_posts()) {
							$r->the_post();
							global $product, $data; 
							//echo $product->product_type;
							if ( $product->product_type == 'variable' ) {

								$old_price = $product->min_variation_regular_price;
								$new_price = $product->min_variation_price;
							} else {
								
								$old_price = $product->regular_price;
								$new_price = $product->sale_price;
							}

							
							$reduced = 0;
							if ( $old_price != 0 ) {
								$reduced = round(100-($new_price*100)/$old_price,0);
							}
							
							echo '<li class="product-list-item" data-discount="'.$reduced.'%">';
							//echo $product->product_type;
								do_action( 'woocommerce_before_shop_loop_item_title' );
								echo '<h5><a href="'.get_permalink().'">'.get_the_title().'</a></h5>';
								echo '<h6 class="price">'.$product->get_price_html().'</h6>';
							echo '</li>';
							
						}
					}
					wp_reset_query();
					?>

				</ul>
				<div class="controls">
					<a href="#" class="prev"><span class="icon-chevron-left"></span></a>
					<a href="#" class="next"><span class="icon-chevron-right"></span></a>
				</div>
			</div>
			<!-- end limited offers carousel -->
		</div>
	<?php
	}


/*--------------------------------------------------------------------------------------------------
	Products Presentation
--------------------------------------------------------------------------------------------------*/
	function _woo_products( $options )
	{
	

		global $woocommerce;
		if ( empty($woocommerce) ) { return; }
		$element_size = zn_get_size( $options['_sizer'] );

	?>
		<div class="<?php echo $element_size['sizer'];?>">
			<div class="shop-latest">
			
				<div class="tabbable">
				
					<ul class="nav fixclear">
					
					<?php
						$css = '';
						if ( $options['woo_lp_prod'] ) {
							echo '<li class="active"><a href="#tabpan1" data-toggle="tab">'.__("LATEST PRODUCTS",THEMENAME).'</a></li>';
						}
						else {
							$css = 'active';
						}						
						if ( $options['woo_bs_prod'] ) {
							echo '<li class="'.$css.'"><a href="#tabpan2" data-toggle="tab">'.__("BEST SELLING PRODUCTS",THEMENAME).'</a></li>';
						}
					?>
					</ul>
					
				<?php
				if ( $options['woo_lp_prod'] ) {
				?>
					<div class="tab-content">
						<div class="tab-pane active" id="tabpan1">
						
							<div class="shop-latest-carousel">
								<ul id="latest_products">
								<?php
										
									$query_args = array('posts_per_page' => $options['prods_per_page'],
														'tax_query' => array (
																			array (
																				'taxonomy' => 'product_cat',
																				'field' => 'id',
																				'terms' => $options['woo_categories']
																				)
																		),
														'no_found_rows' => 1, 
														'post_status' => 'publish', 
														'post_type' => 'product' 
													);	

									
									$r = new WP_Query($query_args);
									
									if ($r->have_posts()) {
									
										while ($r->have_posts()) {
											$r->the_post();
											global $product, $data; 
											
											/* CHECK STOCK */
											if ( ! $product->is_in_stock() ) { 

												$zlink = '<a href="'. apply_filters( 'out_of_stock_add_to_cart_url', get_permalink( $product->id ) ).'" class="">'. apply_filters( 'out_of_stock_add_to_cart_text', __( 'Read More', THEMENAME ) ).'</a>';
											}	
											else { ?>

											<?php

												switch ( $product->product_type ) {
													case "variable" :
														$link 	= apply_filters( 'variable_add_to_cart_url', get_permalink( $product->id ) );
														$label 	= apply_filters( 'variable_add_to_cart_text', __('Select options', THEMENAME) );
													break;
													case "grouped" :
														$link 	= apply_filters( 'grouped_add_to_cart_url', get_permalink( $product->id ) );
														$label 	= apply_filters( 'grouped_add_to_cart_text', __('View options', THEMENAME) );
													break;
													case "external" :
														$link 	= apply_filters( 'external_add_to_cart_url', get_permalink( $product->id ) );
														$label 	= apply_filters( 'external_add_to_cart_text', __('Read More', THEMENAME) );
													break;
													default :
														$link 	= apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) );
														$label 	= apply_filters( 'add_to_cart_text', __('ADD TO CART', THEMENAME) );
													break;
												}
												
												$zlink = '<a href="'. $link .'" rel="nofollow" data-product_id="'.$product->id.'" class="add_to_cart_button product_type_'.$product->product_type.'">'. $label.'</a>';
											}
											$new_badge = '';
											
											/* CHECK BADGE */
											if ( $data['woo_new_badge'] == 1 ) {
												
												$now = time();
												$diff = (get_the_time('U') > $now) ? get_the_time('U') - $now : $now - get_the_time('U');
												$val = floor($diff/86400);
												$days = floor(get_the_time('U')/(86400));
												
												if ( $data['woo_new_badge_days'] >= $val ) {
													$new_badge = '<span class="znew">'.__('NEW!', THEMENAME).'</span>';
												}

											} 
											/* CHECK ON SALE */
											$on_sale = '';
											if ($product->is_on_sale()) : 

												$on_sale = '<span class="zonsale">'.__('SALE!', THEMENAME).'</span>'; 

											endif; 
										?>
											<li>
											
												<div class="product-list-item ">
													<span class="hover"></span>
													<div class="zn_badge_container">
														<?php echo $on_sale;?>
														<?php echo $new_badge;?>
													</div>
													<?php
														/**
														 * woocommerce_before_shop_loop_item_title hook
														 * @hooked woocommerce_template_loop_product_thumbnail - 10
														 */
													do_action( 'woocommerce_before_shop_loop_item_title' );
													?>
													<div class="details fixclear">
														<h3><a href="<?php echo get_permalink();?>"><?php the_title(); ?></a></h3>
														<?php echo apply_filters( 'woocommerce_short_description', get_the_excerpt() ) ?>
														<div class="actions">
															<?php if ( empty( $data['woo_catalog_mode'] ) || ( !empty( $data['woo_catalog_mode'] ) && $data['woo_catalog_mode'] == 'no' ) ) {  echo $zlink; } ?>
															<a href="<?php echo get_permalink();?>"><?php _e("MORE INFO",THEMENAME);?></a>
														</div>

														
														<?php if ($price_html = $product->get_price_html()) : ?>
															<div class="price"><?php echo $price_html; ?></div>
														<?php endif; ?>
														

													</div>
												</div><!-- end product-item -->
											
											</li>
										<?php

										}
									
									}
									
									// Reset the global $the_post as this query will have stomped on it
									wp_reset_query();
									
								?>

								</ul><!-- shop product list -->
								<div class="controls">
									<a href="#" class="prev"><span class="icon-chevron-left"></span></a>
									<a href="#" class="next"><span class="icon-chevron-right"></span></a>
								</div>
								<div class="clear"></div>
							</div><!--end shop-latest-carousel -->

						</div><!-- end tab pane -->
					<?php
					}
					?>
					<?php
					if ( $options['woo_bs_prod'] ) {
					?>
						<div class="tab-pane <?php echo $css;?>" id="tabpan2">
						
							<div class="shop-latest-carousel">
								<ul id="bestselling_products">
								<?php
								
									$query_args = array('posts_per_page' => $options['prods_per_page'],
														'tax_query' => array (
																			array (
																				'taxonomy' => 'product_cat',
																				'field' => 'id',
																				'terms' => $options['woo_categories']
																				)
																		),
														'no_found_rows' => 1, 
														'post_status' => 'publish', 
														'post_type' => 'product' , 
														'meta_key' => 'total_sales',
														'orderby' => 'meta_value'
													);
									
									
									$r = new WP_Query($query_args);
									
									if ($r->have_posts()) {
									
										while ($r->have_posts()) {
											$r->the_post();
											global $product, $data; 
											
											/* CHECK STOCK */
											if ( ! $product->is_in_stock() ) { 

												$zlink = '<a href="'. apply_filters( 'out_of_stock_add_to_cart_url', get_permalink( $product->id ) ).'" class="">'. apply_filters( 'out_of_stock_add_to_cart_text', __( 'Read More', THEMENAME ) ).'</a>';
											}	
											else { ?>

											<?php

												switch ( $product->product_type ) {
													case "variable" :
														$link 	= apply_filters( 'variable_add_to_cart_url', get_permalink( $product->id ) );
														$label 	= apply_filters( 'variable_add_to_cart_text', __('Select options', THEMENAME) );
													break;
													case "grouped" :
														$link 	= apply_filters( 'grouped_add_to_cart_url', get_permalink( $product->id ) );
														$label 	= apply_filters( 'grouped_add_to_cart_text', __('View options', THEMENAME) );
													break;
													case "external" :
														$link 	= apply_filters( 'external_add_to_cart_url', get_permalink( $product->id ) );
														$label 	= apply_filters( 'external_add_to_cart_text', __('Read More', THEMENAME) );
													break;
													default :
														$link 	= apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) );
														$label 	= apply_filters( 'add_to_cart_text', __('ADD TO CART', THEMENAME) );
													break;
												}
												
												$zlink = '<a href="'. $link .'" rel="nofollow" data-product_id="'.$product->id.'" class="add_to_cart_button product_type_'.$product->product_type.'">'. $label.'</a>';
											}
											
											/* CHECK BADGE */
											if ( $data['woo_new_badge'] ) {
											
												$now = time();
												$diff = (get_the_time('U') > $now) ? get_the_time('U') - $now : $now - get_the_time('U');
												$val = floor($diff/86400);
												$days = floor(get_the_time('U')/(86400));
												
												if ( $data['woo_new_badge_days'] >= $val ) {
													$new_badge = '<span class="znew">'.__('NEW!', THEMENAME).'</span>';
												}

											} 
											/* CHECK ON SALE */
											$on_sale = '';
											if ($product->is_on_sale()) : 

												$on_sale = '<span class="zonsale">'.__('SALE!', THEMENAME).'</span>'; 

											endif; 
										?>
											<li>
											
												<div class="product-list-item ">
													<span class="hover"></span>
													<div class="zn_badge_container">
														<?php echo $on_sale;?>
														<?php echo $new_badge;?>
													</div>
													<?php
														/**
														 * woocommerce_before_shop_loop_item_title hook
														 * @hooked woocommerce_template_loop_product_thumbnail - 10
														 */
													do_action( 'woocommerce_before_shop_loop_item_title' );
													?>
													<div class="details fixclear">
														<h3><a href="<?php echo get_permalink();?>"><?php the_title(); ?></a></h3>
														<?php echo apply_filters( 'woocommerce_short_description', get_the_excerpt() ) ?>
														<div class="actions">
															<?php if ( empty( $data['woo_catalog_mode'] ) || ( !empty( $data['woo_catalog_mode'] ) && $data['woo_catalog_mode'] == 'no' ) ) {  echo $zlink; } ?>
															<a href="<?php echo get_permalink();?>"><?php _e("MORE INFO",THEMENAME);?></a>
														</div>

														
														<?php if ($price_html = $product->get_price_html()) : ?>
															<div class="price"><?php echo $price_html; ?></div>
														<?php endif; ?>
														

													</div>
												</div><!-- end product-item -->
											
											</li>
										<?php

										}
									
									}
									
									// Reset the global $the_post as this query will have stomped on it
									wp_reset_query();
									
								?>
								</ul><!-- shop product list -->
								<div class="controls">
									<a href="#" class="prev"><span class="icon-chevron-left"></span></a>
									<a href="#" class="next"><span class="icon-chevron-right"></span></a>
								</div>
								<div class="clear"></div>
							</div><!--end shop-latest-carousel -->

						</div><!-- end tab pane -->
					<?php
					}
					?>	

					</div><!-- /.tab-content -->
				</div><!-- /.tabbable -->

			</div><!-- end shop latest -->
		</div>
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Services Element
--------------------------------------------------------------------------------------------------*/

	function _service_box2( $options )
	{ 
	
		echo '<div class="span12">';
		echo '<div class="row-fluid no-space services_box style2">';
		
			if ( !empty ( $options['single_service_elem'] ) && is_array ( $options['single_service_elem'] ) ) {
				
				foreach ( $options['single_service_elem'] as $sb ) {
					
					echo '<div class="span4">';
					echo '<div class="box fixclear">';
					
						// TITLE ICON
						if ( !empty ( $sb['sbe_image'] ) ) {
							echo '<div class="icon"><img src="'.$sb['sbe_image'].'" alt=""></div>';
						}
						
						// TITLE 
						if ( !empty ( $sb['sbe_title'] ) ) {
							echo '<h4 class="title">'.$sb['sbe_title'].'</h4>';
						}
						
						// Services list 
						if ( !empty ( $sb['sbe_services'] ) ) {
						
							echo '<ul class="list">';
						
							$textAr = explode("\n", $sb['sbe_services'] );
							foreach ($textAr as $index=>$line) {
								echo '<li>'.$line.'</li>';
							} 
							
							echo '</ul>';
						}

						// Content 
						if ( !empty ( $sb['sbe_content'] ) ) {
							echo '<div class="text">'.$sb['sbe_content'].'</div>';
						}
					
					
					echo '</div><!-- end box -->';
					echo '</div>';
					
				}
				
			}
		
		echo '</div><!-- end row // services_box -->';
		echo '</div>';
	
	
	?>

			<script type="text/javascript">
				(function($){
					$(".services_box.style2 .box").hover(function() {
						var _t = $(this),
							lis = _t.find('li');
						_t.find('.text').stop().hide();
						lis.stop().css({ opacity: 0, marginTop:10});
						_t.find('.list').stop().show();
						lis.each(function(i) {
							duration = i * 50 + 250;
							delay = i * 250;
							$(this).delay(delay).stop().animate({opacity: 1, marginTop:0}, {queue: false, duration:duration, easing:"easeOutExpo"});
						});
					},function() {
						var _t = $(this);
						_t.find('.text').stop().show();
						_t.find('.list').stop().hide();
					});	
				})(jQuery);
			</script>
		
	
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Image Gallery
--------------------------------------------------------------------------------------------------*/
 
	function _image_gallery( $options )
	{
	
		$element_size = zn_get_size( 'one-third' );
	
		if ( !empty ( $options['ig_title'] ) ) {
			echo '<div class="span12">';
			echo '<h4 class="smallm_title centered bigger"><span>'.$options['ig_title'].'</span></h4>';
			echo '</div>';
		}
	
		if ( !empty ( $options['single_ig'] ) && is_array( $options['single_ig'] ) ) {
			
			foreach ( $options['single_ig'] as $image_arr ) {
				
				if ( !empty ( $image_arr['sig_image'] ) ) {
			
					$image = vt_resize( '', $image_arr['sig_image']  , $element_size['width'],'' , true );
			
					echo '<div class="span4">';
						echo '<a href="'.$image_arr['sig_image'].'" class="hoverBorder" rel="prettyPhoto"><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt=""></a>';
					echo '</div>';
					
				}

			}
			
		}
		
		echo '<div class="clear"></div>';
	
	

	}
	
/*--------------------------------------------------------------------------------------------------
	Images Box 2
--------------------------------------------------------------------------------------------------*/
	function _image_box2( $options )
	{
	
		if ( !empty ( $options['image_box_title'] ) ) {
		
			echo '<div class="span12 offer-banners">';
			echo '<h3 class="m_title">'.$options['image_box_title'].'</h3>';
			echo '</div>';
		}
	
		if ( !empty ( $options['ib2_single'] ) && is_array( $options['ib2_single'] ) ) {
			
			foreach ( $options['ib2_single'] as $simage ) {
				
				if ( !empty ( $simage['ib2_image'] ) ) {
				
					echo '<div class="'.$simage['ib2_width'].' offer-banners">';
					
						$element_size = zn_get_size( $simage['ib2_width'] );
						$image = vt_resize( '', $simage['ib2_image']  , $element_size['width'],'' , true );
						$link_start = '<a href="#" class="hoverBorder">';
						$link_end = '</a>';
						
						if ( !empty ( $simage['ib2_link']['url'] ) ) {
							$link_start = '<a href="'.$simage['ib2_link']['url'].'" class="hoverBorder">';
							$link_end = '</a>';
						}
						
						echo $link_start;
							
							echo '<img src="'.$image['url'].'" height="'.$image['height'].'" width="'.$image['width'].'" alt="">';
						
						echo $link_end;
					
					echo '</div>';
					
				}
			
			}
			
		}

	}
	
	
	
/*--------------------------------------------------------------------------------------------------
	Team Box
--------------------------------------------------------------------------------------------------*/
 
	function _team_box( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		$c_title = '';
	
		echo '<div class="'.$element_size['sizer'].'">';
		
			echo '<div class="team_member">';
			
				$link_start = '<a href="#" >';
				$link_end = '</a>';
				$image = '';
					
				if ( !empty ( $options['teb_link']['url'] ) ) {
					$link_start = '<a href="'.$options['teb_link']['url'].'" target="'.$options['teb_link']['target'].'" class="grayHover">';
					$link_end = '</a>';
				}
				
				if ( !empty ( $options['teb_image'] ) ) {
				
					$image = vt_resize( '', $options['teb_image']  , 270,270 , true );
					$image = '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt=""/>';
					
				}
				
				// IMAGE
				echo $link_start;
					echo $image;
				echo $link_end;
				
				// NAME AND POSITION
				echo '<h4>'.$options['teb_name'].'</h4>';
				echo '<h6>'.$options['teb_position'].'</h6>';
			
				echo '<div class="details">';
	
				// DESCRIPTION
				if ( !empty ( $options['teb_desc'] ) ) {
					echo '<div class="desc">';
					
						if (preg_match('%(<p[^>]*>.*?</p>)%i', $options['teb_desc'], $regs)) {
							echo $options['teb_desc'];
						} else {
							echo '<p>'.$options['teb_desc'].'</p>';
						}
					
					echo '</div>';
				}
					
				// SOCIAL ICONS
				if ( !empty ( $options['single_team_social'] ) && is_array ( $options['single_team_social'] ) && !empty( $options['single_team_social'][0]['teb_social_icon'] ) ) {
				
					echo '<ul class="social-icons colored fixclear">';
					
					foreach ( $options['single_team_social'] as $icon ) {
						
						
						echo '<li class="'.$icon['teb_social_icon'].'"><a href="'.$icon['teb_social_link']['url'].'" target="'.$icon['teb_social_link']['target'].'">'.$icon['teb_social_title'].'</a></li>';
						
					}
					
					echo '</ul>';
					
				}
					
					
				echo '</div><!-- end details -->';
			
			echo '</div><!-- end team_member -->';
								
		echo '</div>';							



	}
	
/*--------------------------------------------------------------------------------------------------
	Circle Title Box
--------------------------------------------------------------------------------------------------*/
 
	function _circle_title_box( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		$c_title = '';
	
		echo '<div class="'.$element_size['sizer'].'">';
		
			// TITLE
			if ( !empty ( $options['ctb_circle_title'] ) ) {
				$c_title = '<span>'.$options['ctb_circle_title'].'</span> ';
			}
		
			// TITLE
			if ( !empty ( $options['ctb_main_title'] ) ) {
				echo '<h4 class="circle_title">'.$c_title.''.$options['ctb_main_title'].'</h4>';
			}

			// CONTENT
			if ( !empty ( $options['ctb_content'] ) ) {
				if (preg_match('%(<p[^>]*>.*?</p>)%i', $options['ctb_content'], $regs)) {
					echo $options['ctb_content'];
				} else {
					echo '<p>'.$options['ctb_content'].'</p>';
				}
			}
			
		echo '</div>';
		
		


	}
	
	
/*--------------------------------------------------------------------------------------------------
	Info Box
--------------------------------------------------------------------------------------------------*/
 
	function _infobox( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		
		// LINK
		$link = '';
		if ( !empty ( $options['ib_button_text'] ) && !empty ( $options['ib_button_link']['url'] ) ) {
			$link = '<a href="'.$options['ib_button_link']['url'].'" target="'.$options['ib_button_link']['target'].'" class="btn btn-large btn-inverse">'.$options['ib_button_text'].'</a>';
		}
		
		
		echo '<div class="'.$element_size['sizer'].'">';
			
			echo '<div class="'.$options['ib_style'].'">';
				
				if ( $options['ib_style'] == 'infobox2' ) { echo $link; }
				
				// TITLE
				if ( !empty ( $options['ib_title'] ) ) {
					echo '<h3 class="m_title">'.$options['ib_title'].'</h3>';
				}
				
				// SUBTITLE
				if ( !empty ( $options['ib_subtitle'] ) ) {
					if (preg_match('%(<p[^>]*>.*?</p>)%i', $options['ib_subtitle'], $regs)) {
						echo $options['ib_subtitle'];
					} else {
						echo '<p>'.$options['ib_subtitle'].'</p>';
					}
				}
				
				if ( $options['ib_style'] == 'infobox1' ) { echo $link; }

			echo '</div>';
			
		echo '</div>';

	}
	
/*--------------------------------------------------------------------------------------------------
	TEXT BOX
--------------------------------------------------------------------------------------------------*/
 
	function _text_box( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		echo '<div class="'.$element_size['sizer'].'">';
		if ( $options['stb_style'] == 'style2' && !empty($options['stb_title'])) {
			echo '<h3>'.$options['stb_title'].'</h3>';
		}
		elseif($options['stb_style'] == 'style1' && !empty($options['stb_title'])) {
			echo '<h4 class="m_title">'.$options['stb_title'].'</h4>';
		}
		
		if ( !empty ( $options['stb_content'] ) )  {
			if (preg_match('%(<[^>]*>.*?</)%i', $options['stb_content'], $regs)) {
				echo do_shortcode ( $options['stb_content'] );
			} else {
				echo '<p>'.do_shortcode ( $options['stb_content'] ).'</p>';
			}
		}	

			
		echo '</div>';
	}
	
/*--------------------------------------------------------------------------------------------------
	HISTORIC EVENTS
--------------------------------------------------------------------------------------------------*/
 
	function _historic( $options )
	{
	
		echo '<div class="span12 timeline_bar">';
		echo '<div class="row">';
		
			echo '<div class="span12 end_timeline"><span>'.$options['he_start'].'</span></div>';
		
			if ( !empty ( $options['historic_single'] ) && is_array ( $options['historic_single'] ) ) {
			
				$i=1;
			
				foreach ( $options['historic_single'] as $event ) {
				
					$pos = '<div class="span6">';
					
					if ( $i % 2 != 0 ) {
						$pos = '<div class="span6 offset6" data-align="right">';
					}
				
					echo $pos;
						echo '<div class="timeline_box">';
						
							echo '<div class="date">'.$event['she_event_date'].'</div>';
							echo '<h4 class="htitle">'.$event['she_event_name'].'</h4>';
							
							if (preg_match('%(<p[^>]*>.*?</p>)%i', $event['she_event_desc'], $regs)) {
								echo do_shortcode ( $event['she_event_desc'] );
							} else {
								echo '<p>'.do_shortcode ( $event['she_event_desc'] ).'</p>';
							}
						
						echo '</div><!-- end timeline box -->';
					echo '</div>';
					
					$i++;
				}
				
			}
			
		echo '<div class="span12 end_timeline">';
			echo '<span>'.__("PRESENT",THEMENAME).'</span>';
		echo '</div>';
		
		echo '</div><!-- end timeline bar -->';
		echo '</div>';

	}
/*--------------------------------------------------------------------------------------------------
	HORIZONTAL TABS
--------------------------------------------------------------------------------------------------*/
 
	function _tabs( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		echo '<div class="'.$element_size['sizer'].'">';
		

			
				echo '<div class="tabbable tabs_style1">';
					
					if ( !empty ( $options['single_horizontal_tab'] ) && is_array( $options['single_horizontal_tab'] ) ) {
					
						echo '<ul class="nav fixclear">';
					
						$i = 1;
						$content = '';
						
						foreach ( $options['single_horizontal_tab'] as $tab ) {
							
							$cls = '';

							if ( $i == 1 ) {
								$cls = 'active';
							}

							$uniq_name = $i . uniqid();

							// Tab Handle
							echo '<li class="'.$cls.'">';
								echo '<a href="#tabs_i2-pane'.$uniq_name.'" data-toggle="tab">';

									echo $tab['vts_tab_title'];
								echo '</a>';
								
							echo '</li>';
							
							// TAB CONTENT
							$content .= '<div class="tab-pane '.$cls.'" id="tabs_i2-pane'.$uniq_name.'">';
							
								$content .= '<h4>'.$tab['vts_tab_c_title'].'</h4>';
							
								if (preg_match('%(<p[^>]*>.*?</p>)%i', $tab['vts_tab_c_content'], $regs)) {
									$content .= $tab['vts_tab_c_content'];
								} else {
									$content .= '<p>'.$tab['vts_tab_c_content'].'</p>';
								}
							
							$content .= '</div>';
					
							$i++;
						}
						
						echo '</ul>';
						
						echo '<div class="tab-content">';
						
							echo do_shortcode($content);
						
						echo '</div>';
					}
					

					
				echo '</div>';
				
		
		echo '</div>';
		
	}
	
/*--------------------------------------------------------------------------------------------------
	VERTICAL TABS
--------------------------------------------------------------------------------------------------*/
 
	function _vertical_tabs( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		echo '<div class="'.$element_size['sizer'].'">';
		
			echo '<div class="vertical_tabs">';
			
				echo '<div class="tabbable">';
					
					if ( !empty ( $options['single_vertical_tab'] ) && is_array( $options['single_vertical_tab'] ) ) {
					
						echo '<ul class="nav fixclear">';
					
						$i = 1;
						$content = '';
						
						foreach ( $options['single_vertical_tab'] as $tab ) {
							
							$cls = '';
							$icon = '';
							
							if ( $i == 1 ) {
								$cls = 'active';
							}
							
							$uniq_name = $i . uniqid();

							// ICON CHECK
							if ( !empty ( $tab['vts_tab_icon'] ) ) {
								$icon = '<span><span class="'.$tab['vts_tab_icon'].' icon-white"></span></span>';
							}
						
							// Tab Handle
							echo '<li class="'.$cls.'">';
								echo '<a href="#tabs_v2-pane'.$uniq_name.'" data-toggle="tab">';
									echo $icon;
									echo $tab['vts_tab_title'];
								echo '</a>';
								
							echo '</li>';
							
							// TAB CONTENT
							$content .= '<div class="tab-pane '.$cls.'" id="tabs_v2-pane'.$uniq_name.'">';
							
								$content .= '<h4>'.$tab['vts_tab_c_title'].'</h4>';
							
								if (preg_match('%(<p[^>]*>.*?</p>)%i', $tab['vts_tab_c_content'], $regs)) {
									$content .= $tab['vts_tab_c_content'];
								} else {
									$content .= '<p>'.$tab['vts_tab_c_content'].'</p>';
								}
							
							$content .= '</div>';
					
							$i++;
						}
						
						echo '</ul>';
						
						echo '<div class="tab-content">';
						
							echo do_shortcode($content);
						
						echo '</div>';
					}
					

					
				echo '</div>';
				
			echo '</div>';
		
		echo '</div>';
		
	}
	
/*--------------------------------------------------------------------------------------------------
	KEYWORDS ELEMENT
--------------------------------------------------------------------------------------------------*/
 
	function _keyword( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );

		if ( !empty ( $options['kw_content'] ) ) {
			echo '<div class="'.$element_size['sizer'].'">';
			echo '<div class="keywordbox">'.$options['kw_content'].'</div>';
			echo '</div>';
		}

	}
	
/*--------------------------------------------------------------------------------------------------
	Partners Logos Element
--------------------------------------------------------------------------------------------------*/
 
	function _partners_logos( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		preg_match('|\d+|', $element_size['sizer'] , $new_size);
		$new_size = $new_size[0]-2;
	?>
		<div class="span2 partners_carousel">
		<?php
			if ( !empty ( $options['pl_title'] ) && $options['pl_title_style'] == 'style1' ) {
				echo '<h5 class="title"><span>'.$options['pl_title'].'</span></h5>';
			}
			elseif ( !empty ( $options['pl_title'] ) && $options['pl_title_style'] == 'style2' ) {
				echo '<h4 class="m_title"><span>'.$options['pl_title'].'</span></h4>';
			}
		?>
			<div class="controls">
				<a href="#" class="prev"><span class="icon-chevron-left"></span></a>
				<a href="#" class="next"><span class="icon-chevron-right"></span></a>
			</div>
		</div>
		<div class="span<?php echo $new_size;?> partners_carousel">
			<ul id="partners_carousel" class="fixclear partners_carousel_trigger">
			
				<?php
					if ( !empty ( $options['partners_single'] ) && is_array ( $options['partners_single'] ) ) {
					
						foreach ( $options['partners_single'] as $partner ) {
						
							$link_start = '<a href="#">';
							$link_end = '</a>';
							
							
							
							if ( !empty ( $partner['lp_single_logo'] ) ) {
							
								echo '<li>';
							
								if ( !empty ( $partner['lp_link']['url'] ) ) {
									$link_start = '<a href="'.$partner['lp_link']['url'].'" target="'.$partner['lp_link']['target'].'">';
									$link_end = '</a>';
								}
								
								echo $link_start;
								
									echo '<img src="'.$partner['lp_single_logo'].'" alt="" />';
									
								echo $link_end;
								
								echo '</li>';

							}

						}

					}
				?>
			
			</ul>
		</div>
	<?php
	}
	
	
/*--------------------------------------------------------------------------------------------------
	Stats Box
--------------------------------------------------------------------------------------------------*/
 
	function _infobox2 ( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		echo '<div class="'.$element_size['sizer'].'">';
		
			echo '<div class="info-text">';
				echo $options['ib2_title'];
			echo '</div>';
			
		echo '</div>';

	}
	
/*--------------------------------------------------------------------------------------------------
	Stats Box
--------------------------------------------------------------------------------------------------*/
 
	function _stats_box ( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		echo '<div class="'.$element_size['sizer'].' zn_stats_box">';
		
			echo '<div class="row zn_content_no_margin">';
				echo '<div class="'.$element_size['sizer'].' zn_stats_box">';
				
					echo '<h3 class="mb_title"><span class="'.$options['vts_tab_icon'].' icon-dark"></span> '.$options['stb_title'].'</h3>';
				
				echo '</div>';
			echo '</div>';
		
			if ( !empty ( $options['single_stats'] ) && is_array ( $options['single_stats'] ) ) {
				echo '<div class="row zn_content_no_margin">';
				foreach ( $options['single_stats'] as $stat ) {
					echo '<div class="span3">';
					
						echo '<div class="statbox">';
						
							if ( !empty ( $stat['sb_icon'] ) ) {
								echo '<img src="'.$stat['sb_icon'].'" alt="">';
							}
							
							echo '<h4>'.$stat['sb_title'].'</h4>';
							
							echo '<h6>'.$stat['sb_content'].'</h6>';
						
						echo '</div>';
					
					echo '</div>';
					
				}
				echo '</div>';
			}
		
		echo '</div>';

	}
	
/*--------------------------------------------------------------------------------------------------
	Hover Box
--------------------------------------------------------------------------------------------------*/
 
	function _hover_box ( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
		$content = '';
		
		$cls = 'margin-top:10px;';
		

		echo '<div class="'.$element_size['sizer'].'">';
		echo '<a href="'.$options['hb_link']['url'].'" target="'.$options['hb_link']['target'].'" class="hover-box '.$options['hb_align'].' fixclear">';
		
		if ( !empty ( $options['hb_icon'] ) ) {
			echo '<img src="'.$options['hb_icon'].'" alt="">';
		}
		
		if ( !empty ( $options['hb_desc'] ) ) {
			$content = '<p>'.$options['hb_desc'].'</p>';
			$cls = '';
		}		
		echo '<h3 style="'.$cls.'">'.$options['hb_title'].'</h3>';
		echo '<h4>'.$options['hb_subtitle'].'</h4>';
		
		echo $content;
		
		echo '</a>';
		echo '</div>';

	}
	
/*--------------------------------------------------------------------------------------------------
	Latest Posts 4
--------------------------------------------------------------------------------------------------*/
 
	function _latest_posts4 ( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
	?>
	
							
		<div class="<?php echo $element_size['sizer'];?>">
			<div class="latest_posts default-style">
			
				<div class="row zn_content_no_margin">
					<div class="<?php echo $element_size['sizer'];?>">
						<h3 class="m_title">
							<?php echo $options['lp_title'];?>
						</h3>
					</div>
				</div>
				<div class="row zn_content_no_margin">
				<?php
				
					wp_reset_query();
					global $post;
					//print_r($options['lp_blog_categories']) ;
					// Check what categories were selected..if any 
					if ( !empty ( $options['lp_blog_categories'] ) ) {
						$blog_category = implode(',',$options['lp_blog_categories']);
					}
					else {
						$blog_category = '0';
					}
					
					// HOW MANY POSTS
					if ( isset ( $options['lp_num_posts'] ) ) {
						$num_posts = $options['lp_num_posts'];
					}
					else {
						$num_posts = '3';
					}
					
					// Start the query
					query_posts( array ( 'posts_per_page' => $num_posts , 'cat' => $blog_category ) );
					
					// GET THE NUMBER OF TOTAL POSTS RETURNED
					global $wp_query;
					
					// Start the loop
					while( have_posts() ){

						the_post(); 
						
						echo '<div class="span4 post">';	

							$image = '';
							// Create the featured image html
							if ( has_post_thumbnail( $post->ID ) ) {
							
								$thumb = get_post_thumbnail_id($post->ID) ;
								$f_image = wp_get_attachment_url($thumb) ;
								if ( !empty ( $f_image ) ) {
								
									$feature_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
									$image = vt_resize( '', $f_image  , 370,200 , true );
									$image = '<a href="'.get_permalink().'" class="hoverBorder plus"><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt=""/><h6>'.__("Read more",THEMENAME).' +</h6></a>';
									
								}
							

							}

							echo $image;
						
							echo '<em>';
								the_time('d F Y');
								echo ' '.__("By",THEMENAME);
								echo ' '.get_the_author();
								echo ' '.__("in",THEMENAME).' ';
								the_category(", ");
							echo '</em>';

							echo '<h3 class="m_title"><a href="'.get_permalink().'">'.get_the_title().'</a></h3>';
							
							
						echo '</div>';				
					}
					wp_reset_query();
					
				?>
				</div>
			</div><!-- end // latest posts style 2 -->
		</div>

	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Latest Posts 3
--------------------------------------------------------------------------------------------------*/
 
	function _latest_posts3 ( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
	?>
	
							
		<div class="<?php echo $element_size['sizer'];?>">
			<div class=" latest_posts style2">
				<h3 class="m_title"><?php echo $options['lp_title'];?></h3>
				<?php
					if ( !empty( $options['lp_blog_page'] ) ) {
						echo '<a href="'.$options['lp_blog_page'].'" class="viewall">'. __("VIEW ALL",THEMENAME).' -</a>';
					}
				?>
				<ul class="posts">
				<?php
				
					global $post;
				
					// Check what categories were selected..if any 
					if ( isset ( $options['lp_blog_categories'] ) ) {
						$blog_category = implode(',',$options['lp_blog_categories']);
					}
					else {
						$blog_category = '';
					}
					
					// HOW MANY POSTS
					if ( isset ( $options['lp_num_posts'] ) ) {
						$num_posts = $options['lp_num_posts'];
					}
					else {
						$num_posts = '2';
					}
					
					// Start the query
					query_posts( array ( 'posts_per_page' => $num_posts , 'cat' => $blog_category ) );
					
					// GET THE NUMBER OF TOTAL POSTS RETURNED
					global $wp_query;
					
					// Start the loop
					while( have_posts() ){

						the_post(); 
						
						echo '<li class="post">';	

							echo '<div class="details">';
							echo '<span class="date">';
								the_time('d/m/Y');
							echo '</span>';
							echo '<span class="cat">'.__( 'in ', THEMENAME );
								the_category(", ");
							echo '</span>';
							echo '</div>';
							
							// TITLE						
							echo '<h4 class="title"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';	
							
							// TEXT
							echo '<div class="text">';
								$excerpt = get_the_excerpt();
								$excerpt = strip_shortcodes($excerpt);
								$excerpt = strip_tags($excerpt);
								$the_str = mb_substr($excerpt, 0, 350);
								echo $the_str.'...';
								
							echo '</div>';
							
						echo '</li>';				
					}
					wp_reset_query();
					
				?>

				</ul>
			</div><!-- end // latest posts style 2 -->
		</div>

	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Latest Posts 2
--------------------------------------------------------------------------------------------------*/
 
	function _latest_posts2 ( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
	?>
	
							
		<div class="<?php echo $element_size['sizer'];?>">
			<div class=" latest_posts style3">
				<h3 class="m_title"><?php echo $options['lp_title'];?></h3>
				<?php
					if ( !empty( $options['lp_blog_page'] ) ) {
						echo '<a href="'.$options['lp_blog_page'].'" class="viewall">'. __("VIEW ALL",THEMENAME).' -</a>';
					}
				?>
				<ul class="posts">
				<?php
				
					global $post;
				
					// Check what categories were selected..if any 
					if ( isset ( $options['lp_blog_categories'] ) ) {
						$blog_category = implode(',',$options['lp_blog_categories']);
					}
					else {
						$blog_category = '';
					}
					
					// HOW MANY POSTS
					if ( isset ( $options['lp_num_posts'] ) ) {
						$num_posts = $options['lp_num_posts'];
					}
					else {
						$num_posts = '2';
					}
					
					// Start the query
					query_posts( array ( 'posts_per_page' => $num_posts , 'cat' => $blog_category ) );
					
					// GET THE NUMBER OF TOTAL POSTS RETURNED
					global $wp_query;
					
					// Start the loop
					while( have_posts() ){

						the_post(); 
						
						echo '<li class="post">';	

							$image = '';
							// Create the featured image html
							if ( has_post_thumbnail( $post->ID ) ) {
							
								$thumb = get_post_thumbnail_id($post->ID) ;
								$f_image = wp_get_attachment_url($thumb) ;
								if ( !empty ( $f_image ) ) {
									$feature_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
									$image = vt_resize( '', $f_image , 54,54 , true );
									$image = '<a href="'.get_permalink().'" class="hoverBorder pull-left"><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt=""/></a>';
									
								}
							

							}
							
							// IMAGE
							echo $image;
							
							// TITLE
							echo '<h4 class="title"><a href="'.get_permalink().'">'.get_the_title().'</a></h4>';	
							
							// TEXT
							echo '<div class="text">';
								$excerpt = get_the_excerpt();
								$excerpt = strip_shortcodes($excerpt);
								$excerpt = strip_tags($excerpt);
								$the_str = mb_substr($excerpt, 0, 95);
								echo $the_str.'...';
								
							echo '</div>';
							
						echo '</li>';				
					}
					wp_reset_query();
					
				?>

				</ul>
			</div><!-- end // latest posts style 2 -->
		</div>

	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Latest Posts Element
--------------------------------------------------------------------------------------------------*/
 
	function _latest_posts( $options )
	{
	?>
	
					<div class="span12">
						<div class="latest_posts acc-style">
							<h3 class="m_title"><?php echo $options['lp_title'];?></h3>
						<?php
							if ( !empty( $options['lp_blog_page'] ) ) {
								echo '<a href="'.$options['lp_blog_page'].'" class="viewall">'. __("VIEW ALL",THEMENAME).' -</a>';
							}
						?>
							
							
							<div class="css3accordion">
								<ul>
								
								<?php

									global $post;
									
									// Check what categories were selected..if any 
									if ( isset ( $options['lp_blog_categories'] ) ) {
										$blog_category = implode(',',$options['lp_blog_categories']);
									}
									else {
										$blog_category = '';
									}
									
									// Start the query
									query_posts( array ( 'posts_per_page' => 3 , 'cat' => $blog_category ) );
									
									// GET THE NUMBER OF TOTAL POSTS RETURNED
									global $wp_query;
									$num_posts = $wp_query->post_count;
									
									$i = 0;
									$cls = '';
									
									// Start the loop
									while( have_posts() ){
									
										$i++;
										
										the_post(); 
										
										if  ( $i == $num_posts ) {
											$cls = 'class="last"';
										}
										
										echo '<li '.$cls.'>';
										
											echo '<div class="inner-acc">';
											
												$image = '';
												// Create the featured image html
												if ( has_post_thumbnail( $post->ID ) ) {
													
													$thumb = get_post_thumbnail_id($post->ID) ;
													$f_image = wp_get_attachment_url($thumb) ;
													if ( !empty ( $f_image ) ) {
													
														$feature_image = wp_get_attachment_url( $thumb );
														$image = vt_resize( '', $f_image  , 370,200 , true );
														$image = '<a href="'.get_permalink().'" class="thumb hoverBorder plus"><img class="shadow" src="'.$image['url'].'" alt=""/></a>';
														
													}
													

												}
												echo $image;
												
												echo '<div class="content">';
												
													echo '<em>'.get_the_time('d F Y').' '.__("by",THEMENAME).' '.get_the_author().', '.__("in",THEMENAME).' ';
														
														$all_cats = count(get_the_category());
														$z = 1;
														foreach((get_the_category()) as $category) {
															echo $category->cat_name;
															if ( $all_cats != $z ) { echo ','; }
															$z++;
														}
													echo '</em>';
													
													echo '<h5 class="m_title"><a href="'.get_permalink().'">'.get_the_title().'</a></h5>';
													
													// TEXT
													echo '<div class="text">';
														$excerpt = get_the_excerpt();
														$excerpt = strip_shortcodes($excerpt);
														$excerpt = strip_tags($excerpt);
														$the_str = mb_substr($excerpt, 0, 80);
														echo $the_str.'...';
														
													echo '</div>';
													
													echo '<a href="'.get_permalink().'">'.__("READ MORE",THEMENAME).' +</a>';
													
												echo '</div>';
												
											echo '</div>';
										
										echo '</li>';
										
									}
									wp_reset_query();
								?>

								</ul>
							</div><!-- end CSS3 Accordion -->
						</div><!-- end acc-style -->
					</div>
	
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	FEATURES BOX
--------------------------------------------------------------------------------------------------*/
 
	function _features_element( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		echo '<div class="'.$element_size['sizer'].'">';	
	
		if ( $options['fb_style'] == 'style2' )  {
		
			echo '<div class="row noMargin">';
				// TITLE
				if ( !empty( $options['fb_title'] ) ) {
					echo '<div class="'.$element_size['sizer'].'">';
						echo '<h4 class="smallm_title"><span>'.$options['fb_title'].'</span></h4>';
					echo '</div>';
				}
				
				// SECONDARY TITLE AND CONTENT
				if ( !empty ( $options['fb_stitle'] ) || !empty ( $options['fb_desc'] )){
					echo '<div class="span3">';
						if ( !empty ( $options['fb_stitle'] ) ) { echo '<p><strong>'.$options['fb_stitle'].'</strong></p>';}
						if ( !empty ( $options['fb_desc'] ) ) { echo '<p><em>'.$options['fb_desc'].'</em></p>';}
						
					echo '</div>';
				}
				
				// FEATURES
				if ( isset ( $options['features_single'] ) && is_array ( $options['features_single'] ) ) {
				
				
				
					foreach ( $options['features_single'] as $feature ) {
					
					$image = '';
					
						echo '<div class="span3 feature_box default_style">';
							echo '<div class="box">';
								if ( !empty ( $feature['fb_single_icon'] ) ) {
									$image = '<img src="'.$feature['fb_single_icon'].'" alt="">';
								}
								
								if ( !empty ( $feature['fb_single_title'] ) ) {
									echo '<h4 class="title">'.$image.''.$feature['fb_single_title'].'</h4>';
								}
								
								if ( !empty ( $feature['fb_single_desc'] ) ) {
									echo '<p>'.$feature['fb_single_desc'].'</p>';
								}
								
							echo '</div>';
						echo '</div>';
					}
				
				}
			
			echo '</div>';
		}
		elseif ( $options['fb_style'] == 'style1' ) {
		
			echo '<div class="row noMargin">';
			
				if ( !empty( $options['fb_title'] ) ) {
					echo '<div class="'.$element_size['sizer'].'">';
						echo '<h4 class="smallm_title centered bigger"><span>'.$options['fb_title'].'</span></h4>';
					echo '</div>';
				}
			
				// SECONDARY TITLE AND CONTENT
				if ( !empty ( $options['fb_stitle'] ) || !empty ( $options['fb_desc'] )){
					echo '<div class="span3">';
						if ( !empty ( $options['fb_stitle'] ) ) { echo '<p><strong>'.$options['fb_stitle'].'</strong></p>';}
						if ( !empty ( $options['fb_desc'] ) ) { echo '<p><em>'.$options['fb_desc'].'</em></p>';}
						
					echo '</div>';
				}
			
				if ( isset ( $options['features_single'] ) && is_array ( $options['features_single'] ) ) {
					foreach ( $options['features_single'] as $feature ) {
						echo '<div class="span3 feature_box style2">';
							echo '<div class="box">';
								if ( !empty ( $feature['fb_single_icon'] ) ) {
									echo '<span class="icon"><img src="'.$feature['fb_single_icon'].'" alt=""></span>';
								}
								
								if ( !empty ( $feature['fb_single_title'] ) ) {
									echo '<h4 class="title">'.$feature['fb_single_title'].'</h4>';
								}
								
								if ( !empty ( $feature['fb_single_desc'] ) ) {
									echo '<p>'.$feature['fb_single_desc'].'</p>';
								}
								
							echo '</div>';
						echo '</div>';
					}
				}
			echo '</div>';
			
		}
		elseif ( $options['fb_style'] == 'style3' ) {
			echo '<div class="row noMargin">';
				// TITLE
				if ( !empty( $options['fb_title'] ) ) {
					echo '<div class="'.$element_size['sizer'].'">';
						echo '<h3 class="m_title">'.$options['fb_title'].'</h3>';
					echo '</div>';
				}
				
				// SECONDARY TITLE AND CONTENT
				if ( !empty ( $options['fb_stitle'] ) || !empty ( $options['fb_desc'] )){
					echo '<div class="span3">';
						if ( !empty ( $options['fb_stitle'] ) ) { echo '<p><strong>'.$options['fb_stitle'].'</strong></p>';}
						if ( !empty ( $options['fb_desc'] ) ) { echo '<p><em>'.$options['fb_desc'].'</em></p>';}
						
					echo '</div>';
				}
				
				// FEATURES
				if ( isset ( $options['features_single'] ) && is_array ( $options['features_single'] ) ) {
				
				
				
					foreach ( $options['features_single'] as $feature ) {
					
					$image = '';
					
						echo '<div class="span3 feature_box default_style">';
							echo '<div class="box">';
								if ( !empty ( $feature['fb_single_icon'] ) ) {
									$image = '<img src="'.$feature['fb_single_icon'].'" alt="">';
								}
								
								if ( !empty ( $feature['fb_single_title'] ) ) {
									echo '<h4 class="title">'.$image.''.$feature['fb_single_title'].'</h4>';
								}
								
								if ( !empty ( $feature['fb_single_desc'] ) ) {
									echo '<p>'.$feature['fb_single_desc'].'</p>';
								}
								
							echo '</div>';
						echo '</div>';
					}
				
				}
			
			echo '</div>';
		}
		echo '</div>';
	}
	
/*--------------------------------------------------------------------------------------------------
	Call Out Banner
--------------------------------------------------------------------------------------------------*/
 
	function _call_banner( $options )
	{

			$button = false;
			$div_size = 'span12';
			
			if ( !empty ( $options['cab_button_text'] ) && !empty ( $options['cab_button_link']['url'] ) ) {
				$button = true;
				$div_size = 'span10';
			}
		
			if ( !empty ( $options['cab_main_title'] ) || !empty ( $options['cab_sec_title'] ) ) {
				
				echo '<div class="'.$div_size.'">';
			
				if ( !empty ( $options['cab_main_title'] ) ) {
					echo '<h3 class="m_title" style="margin-top:25px;">'.$options['cab_main_title'].'</h3>';
				}
				
				if ( !empty ( $options['cab_sec_title'] ) ) {
					echo '<p>'.$options['cab_sec_title'].'</p>';
				}
				
				echo '</div>';
				
			}
			
			if ( $button ) {
				echo '<div class="span2">';
				
					echo '<a href="'.$options['cab_button_link']['url'].'" class="circlehover with-symbol" data-size="" data-position="top-left" data-align="right" target="'.$options['cab_button_link']['target'].'">';
						echo '<span class="text">'.$options['cab_button_text'].'</span>';
						if ( !empty ( $options['cab_button_image'] ) ) {
							echo '<span class="symbol"><img src="'.$options['cab_button_image'].'" alt=""></span>';
						}
						else {
							echo '<span class="symbol"><img src="'.MASTER_THEME_DIR.'/images/ok.png" alt=""></span>';
						}
					echo '</a>';
				echo '</div>';
			}

	}
	
	
/*--------------------------------------------------------------------------------------------------
	Action BOX
--------------------------------------------------------------------------------------------------*/
 
	function _action_box($options)
	{
		?>
			<div id="action_box" data-arrowpos="center">
				<div class="container">
					<div class="row">
						
						<?php 
							// Title
							if ( !empty ( $options['page_ac_title'] ) ) 
							{ 
								echo '<div class="span8">';
								echo '<h4 class="text">'.$options['page_ac_title'].'</h4>';
								echo '</div>';
							}
							
							// LINK
							if ( isset ( $options['page_ac_b_link']['url'] ) && !empty ( $options['page_ac_b_link']['url'] ) && !empty ( $options['page_ac_b_text'] ) )
							{
								echo '<div class="span4 align-center">';
								echo '<a class="btn" href="'.$options['page_ac_b_link']['url'].'" target="'.$options['page_ac_b_link']['target'].'">'.$options['page_ac_b_text'].'</a>';
								echo '</div>';
							}
							
						?>

					</div>
				</div>
			</div><!-- end action box -->
		<?php
	}

/*--------------------------------------------------------------------------------------------------
	Action BOX TEXT
--------------------------------------------------------------------------------------------------*/
 
	function _action_box_text($options)
	{
		?>
			<div id="action_box" data-arrowpos="center">
				<div class="container">
					<div class="row">
						
						<?php 
							// Title
							if ( !empty ( $options['page_ac_title'] ) ) 
							{ 
								echo '<div class="span12">';
								echo '<h4 class="text">'.$options['page_ac_title'].'</h4>';
								echo '</div>';
							}
							
						?>

					</div>
				</div>
			</div><!-- end action box -->
		<?php
	}

	
	
/*--------------------------------------------------------------------------------------------------
	Flickr Feed
--------------------------------------------------------------------------------------------------*/
 
	function _flickrfeed($options)
	{
		
		$element_size = zn_get_size( $options['_sizer'] );

		if ( !empty ( $options['ff_images'] ) ) {
			$images_tl = $options['ff_images'];
		}
		else {
			$images_tl = 8;
		}
		
		$image_size = '';
		if ( $options['ff_image_size'] == 'small' ) {
			$image_size = 'data-size="small"';
		}
		
		echo '<div class="'.$element_size['sizer'] .'">';
		echo '<div class="flickrfeed">';
		echo '<h3 class="m_title">'.$options['ff_title'].'</h3>';
		echo '<ul class="flickr_feeds fixclear" data-limit="'.$images_tl.'" '.$image_size.'></ul>';
		echo '</div><!-- end // flickrfeed -->';
		echo '</div>';

	}
	
/*--------------------------------------------------------------------------------------------------
	TESTIMONIALS SLIDER
--------------------------------------------------------------------------------------------------*/
 
	function _testimonial_slider2( $options )
	{
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		$new_size = $element_size['sizer'];
		
		echo '<div class="'.$element_size['sizer'].'">';
		
			echo '<div class="testimonials-carousel">';
			
				if ( !empty ( $options['tf_title'] ) ) {
					echo '<h3 class="m_title">'.$options['tf_title'].'</h3>';
				}
			
				echo '<div class="controls">';
					echo '<a href="#" class="prev"><span class="icon-chevron-left"></span></a>';
					echo '<a href="#" class="next"><span class="icon-chevron-right"></span></a>';
				echo '</div>';
			
				if ( !empty ( $options['testimonials_slider_single'] ) && is_array ( $options['testimonials_slider_single'] ) ) {
				
					echo '<ul id="testimonials_carousel" class="zn_testimonials_carousel fixclear">';
				
					foreach ( $options['testimonials_slider_single'] as $test ) {
						if ( !empty ( $test['tf_single_test'] ) ) {
							echo '<li>';
							
								echo '<blockquote>'.$test['tf_single_test'].'</blockquote>';
								echo '<h5>'.$test['tf_single_author'].'</h5>';
							
							echo '</li>';
						}
						
					}
					
					echo '</ul>';
					
				}
			
			echo '</div>';
		
		echo '</div>';

	}
	
/*--------------------------------------------------------------------------------------------------
	TESTIMONIALS FADER Box
--------------------------------------------------------------------------------------------------*/
 
	function _testimonial_slider($options)
	{
		
		$element_size = zn_get_size( $options['_sizer'] );
		
		$new_size = $element_size['sizer'];
		
		if ( !empty ( $options['tf_title'] ) || !empty ( $options['tf_desc'] ) ) {
			preg_match('|\d+|', $element_size['sizer'] , $new_size);
			$new_size = $new_size[0]-3;
			$new_size = 'span'.$new_size;
			
			echo '<div class="span3 testimonials_fader">';
			
				if ( !empty ( $options['tf_title'] ) ) {
					echo '<h3 class="m_title">'.$options['tf_title'].'</h3>';
				}
				
				if ( !empty ( $options['tf_desc'] ) ) {
					echo '<p>'.$options['tf_desc'].'</p>';
				}
			
			echo '</div>';

		}
		
		echo '<div class="'.$new_size.' testimonials_fader">';
			echo '<ul id="testimonials_fader" class="fixclear">';
				foreach ( $options['testimonials_single'] as $test ) {
					echo '<li>';
					
						echo '<blockquote>'.$test['tf_single_test'].'</blockquote>';
						echo '<h6>'.$test['tf_single_author'].'</h6>';
					
					echo '</li>';
				}
		echo '</div>';
		echo '</ul>';

	}
	
/*--------------------------------------------------------------------------------------------------
	Service Box
--------------------------------------------------------------------------------------------------*/
 
	function _service_box($options)
	{
		
		$element_size = zn_get_size( $options['_sizer'] );
	?>
		<div class="<?php echo $element_size['sizer'];?> services_box">
			<div class="box fixclear">
			
				<?php
				
					// Image
					if ( !empty ( $options['service_box_image'] ) ) 
					{ 
					
						echo  '<div class="icon"><img src="'.$options['service_box_image'].'" alt=""></div>';

					}
				
					// Title
					if ( !empty ( $options['service_box_title'] ) ) 
					{ 
						echo '<h4 class="title">'.$options['service_box_title'].'</h4>';
					}
					
					// FEATURES LIST
					if ( !empty ( $options['service_box_features'] ) ) {
					
						echo '<ul class="list-style1">';
					
							$textAr = explode("\n", $options['service_box_features']);
							foreach ($textAr as $index=>$line) {
								echo '<li>'.$line.'</li>';
							} 
					
						echo '</ul>';
					}
					
				?>

			</div><!-- end box -->
		</div>
	<?php
	
	
	}


/*--------------------------------------------------------------------------------------------------
	Portfolio Category
--------------------------------------------------------------------------------------------------*/
 
	function _portfolio_category( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		wp_reset_query();
		global $post;
		
		$posts_per_page = isset($options['ports_per_page']) ? $options['ports_per_page'] : '4'; // how many posts
		

		$query = array(
			'post_type' => 'portfolio',
			'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1,
			'posts_per_page' =>  $posts_per_page,
			'tax_query' => array(
				array(
					'taxonomy' => 'project_category',
					'field' => 'id',
					'terms' =>  $options['portfolio_categories']
				)
			),
			'showposts' => $options['ports_per_page_visible']
		);



		// Start the query
		query_posts( $query );

			if ( $options['ports_num_columns'] == '1' ) {
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

				$proper_size = 12/$options['ports_num_columns'];
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

					if ( $i % $options['ports_num_columns'] == 0 && $i % $options['ports_per_page_visible'] != 0 ) {

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


/*--------------------------------------------------------------------------------------------------
	Portfolio Sortable
--------------------------------------------------------------------------------------------------*/
 
	function _portfolio_sortable( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		wp_reset_query();
		global $post,$data;

		
		$posts_per_page = isset($options['ports_per_page']) ? $options['ports_per_page'] : '4'; // how many posts

		$query = array(
			'post_type' => 'portfolio',
			'tax_query' => array(
				array(
					'taxonomy' => 'project_category',
					'field' => 'id',
					'terms' =>  $options['portfolio_categories']
				)
			),
			'posts_per_page' =>  $posts_per_page,
		);



		// Start the query
		query_posts( $query );


		?>
			<div class="span12">
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
									'include'=> $options['portfolio_categories'],
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
											$the_str = mb_substr($excerpt, 0, 116);
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
		<?php

	}	


/*--------------------------------------------------------------------------------------------------
	Portfolio Carousel Layout
--------------------------------------------------------------------------------------------------*/
 
	function _portfolio_carousel( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		wp_reset_query();
		global $post;

		
		$posts_per_page = isset($options['ports_per_page']) ? $options['ports_per_page'] : '4'; // how many posts

		$query = array(
			'post_type' => 'portfolio',
			'paged' => (get_query_var('paged')) ? get_query_var('paged') : 1,
			'posts_per_page' =>  $posts_per_page,
			'tax_query' => array(
				array(
					'taxonomy' => 'project_category',
					'field' => 'id',
					'terms' =>  $options['portfolio_categories']
				)
			),
			'showposts' => $options['ports_per_page_visible']
		);




		// Start the query
		query_posts( $query );
		$i = 1;

		?>
						<div class="span12">
							<div class="row hg-portfolio-carousel">

							<?php if ( have_posts() ): while ( have_posts() ): the_post(); 
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
																$size = zn_get_size( 'eight');
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
									if ( $i % $options['ports_per_page_visible'] != 0 ) {

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
		<?php

	}	


/*--------------------------------------------------------------------------------------------------
	Recent Works 2
--------------------------------------------------------------------------------------------------*/
 
	function _recent_work2( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		?>
		
				<div class="recentwork_carousel style2 <?php echo $element_size['sizer'];?>">
					<?php
						// ELEMENT TITLE
						if ( !empty ( $options['rw_title'] ) ) {
							echo '<h3 class="m_title">'.$options['rw_title'].'</h3>';
						}
						
					?>
						<div class="controls">
							<a href="#" class="prev"><span class="icon-chevron-left"></span></a>
						<?php
							// PORTFOLIO PAGE LINK
							if ( !empty ( $options['rw_port_link'] ) ) {
								echo '<a href="'.$options['rw_port_link'].'" class="complete"><span class="icon-th"></span></a>';
							}
						?>
							<a href="#" class="next"><span class="icon-chevron-right"></span></a>
						</div>
						

						<ul id="recent_works2" class="fixclear recent_works2">
						
						<?php
							wp_reset_query();
							global $post;
							
							$posts_per_page = isset($options['ports_per_page']) ? $options['ports_per_page'] : '4'; // how many posts
							
							// Start the query
							query_posts( 
								array ( 
									'post_type' => 'portfolio' , 
									'posts_per_page' => $posts_per_page , 
									'tax_query' => array(
											array(
												'taxonomy' => 'project_category',
												'field' => 'id',
												'terms' =>  $options['portfolio_categories']
											)
										), 
									) 
								);
									
							// Start the loop
							while( have_posts() ){
								
								the_post(); 
								
								// Get post meta information
								$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
								$post_meta_fields = maybe_unserialize( $post_meta_fields );
								
								echo '<li>';
								
									echo '<a href="'.get_permalink().'">';
										
										if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
											
											// IMAGE 											
											if ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) ) {
												
													
														$size = zn_get_size( 'four' );

														$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],'169' , true );
														echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';

												
												
											}
											// VIDEO
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
												

													$size = zn_get_size( 'four' );
													echo get_video_from_link( $post_meta_fields['port_media']['0']['port_media_video_comb'] , '' , $size['width'] , '169' );
														
												
											}
												
										}
										
										echo '<div class="details">';
										echo '<span class="plus">+</span>';
										
										// GET THE POST TITLE
										echo '<h4>'.get_the_title().'</h4>';								
										
										// GET ALL POST CATEGORIES
										echo '<span>'.strip_tags ( get_the_term_list( $post->ID, 'project_category', '', ' , ', '' ) ).'</span>';
										echo '</div>';
										
									echo '</a>';
								
								echo '</li>';
							}
							
						?>

						</ul>
				</div><!-- end row // recentworks_carousel default-style -->
		
		<?php
	}	
	
/*--------------------------------------------------------------------------------------------------
	Recent Works 1
--------------------------------------------------------------------------------------------------*/
 
	function _recent_work( $options )
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		?>
		
				<div class="recentwork_carousel default-style">
					<div class="span3">
					
					<?php
						// ELEMENT TITLE
						if ( !empty ( $options['rw_title'] ) ) {
							echo '<h3 class="m_title">'.$options['rw_title'].'</h3>';
						}
						
						// ELEMENT DESCRIPTION
						if ( !empty ( $options['rw_desc'] ) ) {
							echo '<p>'.$options['rw_desc'].'</p>';
						}
						
					?>
						<div class="controls">
							<a href="#" class="prev"><span class="icon-chevron-left"></span></a>
						<?php
							// PORTFOLIO PAGE LINK
							if ( !empty ( $options['rw_port_link'] ) ) {
								echo '<a href="'.$options['rw_port_link'].'" class="complete"><span class="icon-th"></span></a>';
							}
						?>
							<a href="#" class="next"><span class="icon-chevron-right"></span></a>
						</div>
					</div>
					<div class="span9">
						<ul id="recent_works1" class="recent_works1 fixclear">
						
						<?php
							wp_reset_query();
							global $post;
	
							$posts_per_page = isset($options['ports_per_page']) ? $options['ports_per_page'] : '4'; // how many posts
							
							if ( empty ( $options['portfolio_categories'] ) ) { $options['portfolio_categories'] = ''; }
							
							// Start the query
							query_posts( 
								array ( 
									'post_type' => 'portfolio' , 
									'posts_per_page' => $posts_per_page ,
									'tax_query' => array(
										array(
											'taxonomy' => 'project_category',
											'field' => 'id',
											'terms' =>  $options['portfolio_categories']
										)
									),
									 ) 
								);
									
							// Start the loop
							while( have_posts() ){
								
								the_post(); 
								
								// Get post meta information
								$post_meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
								$post_meta_fields = maybe_unserialize( $post_meta_fields );
								
								echo '<li>';
								
									echo '<a href="'.get_permalink().'">';
										
										if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
											
											// IMAGE 											
											if ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) ) {
												
													echo '<span class="hover">';
													
														$size = zn_get_size( 'four' );

														$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],'169' , true );
														echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
														echo '<span class="hov"></span>';
													echo '</span>';
												
												
											}
											// VIDEO
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
												

													$size = zn_get_size( 'four' );
													echo get_video_from_link( $post_meta_fields['port_media']['0']['port_media_video_comb'] , '' , $size['width'] , '169' );
														
												
											}
												
										}
										
										echo '<div class="details">';
										echo '	<span class="bg"></span>';
										
										// GET THE POST TITLE
										echo '	<h4>'.get_the_title().'</h4>';								
										
										// GET ALL POST CATEGORIES
										echo '	<span>'.strip_tags ( get_the_term_list( $post->ID, 'project_category', '', ' , ', '' ) ).'</span>';
										echo '</div>';
										
									echo '</a>';
								
								echo '</li>';
							}
							
						?>

						</ul>
					</div>
				</div><!-- end row // recentworks_carousel default-style -->
		
		<?php
	}	
	
/*--------------------------------------------------------------------------------------------------
	Screenshoots Box
--------------------------------------------------------------------------------------------------*/
 
	function _screenshoot_box( $options )
	{

		?>
		<div class="span12">
			<div class="screenshot-box fixclear">
				<div class="thescreenshot">
					<div class="controls"><a href="#" class="prev"></a><a href="#" class="next"></a></div>
					<ul id="screenshot-carousel" class="fixclear">
					<?php
						if ( !empty ( $options['ssb_imag_single'] ) && is_array ( $options['ssb_imag_single'] ) ) {
							foreach ( $options['ssb_imag_single'] as $image ) {
								
								$image = vt_resize( '',$image['ssb_single_screenshoot'] ,'580','328', true );
								echo '<li><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt=""></li>';
								
							}
						}
					?>

					</ul>
				</div>
				<div class="left-side">
		
					<h3 class="title"><?php echo $options['ssb_title'];?></h3>
					
					<?php
						if ( !empty ( $options['ssb_feat_single'] ) && is_array ( $options['ssb_feat_single'] ) ) {
							
							echo '<ul class="features">';
							
								foreach ( $options['ssb_feat_single'] as $feat ) {
									echo '<li>';
										// FEATURE TITLE
										if ( !empty ( $feat['ssb_single_title'] ) ) {
											echo '<h4>'.$feat['ssb_single_title'].'</h4>';
										}
										// FEATURE DESC
										if ( !empty ( $feat['ssb_single_desc'] ) ) {
											echo '<span>'.$feat['ssb_single_desc'].'</span>';
										}
									
									echo '</li>';
								}
							
							echo '</ul>';
							
						}
						
						// BUTTON LINK
						if ( !empty ( $options['ssb_link_text'] ) && !empty ( $options['ssb_button_link']['url'] ) ) {
							echo '<a href="'.$options['ssb_button_link']['url'].'" target="'.$options['ssb_button_link']['target'].'" class="btn btn-large btn-flat redbtn">'.$options['ssb_link_text'].'</a>';
						}
						
					?>

					
					
					
				</div>
				
			</div><!-- end screenshot-box -->
		</div>
		<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Features Element 2
--------------------------------------------------------------------------------------------------*/
 
	function _features_element2( $options )
	{
	
		if ( !empty ( $options['fb_title'] ) ) {
			echo '<div class="span12 feature_box style3">';
			echo '<h4 class="smallm_title centered bigger"><span>'.$options['fb_title'].'</span></h4>';
			echo '</div>';
		}
	
		if ( !empty ( $options['features_single2'] ) && is_array( $options['features_single2'] ) ) {
			
			foreach ( $options['features_single2'] as $feat ) {
				
				echo '<div class="span3 feature_box style3">';
				
					echo '<div class="box">';
					
						echo '<h4 class="title">'.$feat['fb_single_title'].'</h4>';
						echo '<span class="icon '.$feat['fb_single_icon'].'"></span>';
						echo '<p>'.$feat['fb_single_desc'].'</p>';
					
					echo '</div><!-- end box -->';
				
				echo '</div>';
				
			}
			
		}

	}
	
/*--------------------------------------------------------------------------------------------------
	Testimonial ELEMENT
--------------------------------------------------------------------------------------------------*/
 
	function _testimonial_box($options)
	{

		
		$element_size = zn_get_size( $options['_sizer'] );
		
		$style = 'light';
		$align = "left";
		if ( $options['tb_style'] == 'style2' ){
			$style = 'dark';
			$align = "top";
		}
		
		?>
			<div class="<?php echo $element_size['sizer'];?>" >
				<div class="testimonial_box" data-align="<?php echo $align; ?>" data-theme="<?php echo $style; ?>">
				<div class="details">
				<?php
					
					if ( !empty ( $options['tb_author_logo'] ) ) {
						$image = vt_resize( '',$options['tb_author_logo'] ,'60','60', true );
						echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="">';
					
					}
					
					if ( !empty ( $options['tb_author'] ) || !empty ( $options['tb_author_com'] ) ) {
						echo '<h6>';
						
						if ( !empty ( $options['tb_author'] ) ) { echo '<strong>'.$options['tb_author'].'</strong>'; }
						if ( !empty ( $options['tb_author_com'] ) ) { echo $options['tb_author_com']; }
						
						echo '</h6>';
					}
					
				?>
				</div>
				<?php
					if ( !empty ( $options['tb_author_quote'] ) ) { echo '<blockquote>'. $options['tb_author_quote'] .'</blockquote>'; }
				?>
				
			</div>
			</div>
		<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Accordion
--------------------------------------------------------------------------------------------------*/
 
	function _accordion($options)
	{

		
		$element_size = zn_get_size( $options['_sizer'] );
		
		?>
			<div class="<?php echo $element_size['sizer'];?>">
			
			<?php
				if ( !empty ( $options['acc_title'] ) && $options['acc_style'] == 'default-style' ) {
					echo '<h3 class="m_title">'.$options['acc_title'].'</h3>';
				}
				elseif ( !empty ( $options['acc_title'] ) && ( $options['acc_style'] == 'style2' || $options['acc_style'] == 'style3' ) ) {
					echo '<h3>'.$options['acc_title'].'</h3>';
				}
				
				$acc_id = 1;
				$uniq = uniqid();
				
				if ( isset ( $options['accordion_single'] ) && is_array( $options['accordion_single'] ) ) {
					foreach ( $options['accordion_single'] as $acc ) {
						echo '<div class="acc-group '.$options['acc_style'].'">';
							echo '<button data-toggle="collapse" data-target="#acc'.$uniq.''.$acc_id.'" class="collapsed">'.$acc['acc_single_title'].'</button>';
							echo '<div id="acc'.$uniq.''.$acc_id.'" class="collapse in">';
								
								echo '<div class="content">';
								
										if (preg_match('%(<p[^>]*>.*?</p>)%i', $acc['acc_single_desc'], $regs)) {
											echo $acc['acc_single_desc'];
										} else {
											echo '<p>'.$acc['acc_single_desc'].'</p>';
										}
										
								echo '</div>';	
								
							echo '</div>';
						echo '</div>';
						
						$acc_id++;
					}
				}
				
			?>

				<!-- end // accordion texts  -->
			</div>
		
		<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	CONTACT FORM ELEMENT
--------------------------------------------------------------------------------------------------*/
	function _c_form($options)
	{	 
		

			require_once(TEMPLATEPATH . '/template-helpers/contact_form.php');
			
			
	}
	
/*--------------------------------------------------------------------------------------------------
	STEPS BOX 3
--------------------------------------------------------------------------------------------------*/
 
	function _step_box3( $options )
	{
	
		if ( !empty ( $options['stp_title'] ) ) {
			echo '<div class="span12">';
			echo '<h3 class="m_title">'.$options['stp_title'].'</h3>';
			echo '</div>';
		}
	
		if ( !empty ( $options['steps_single3'] ) && is_array ( $options['steps_single3'] ) ) {
		
			$i = 1;
			$all = count( $options['steps_single3'] );
			$cls = '';
			
			foreach ( $options['steps_single3'] as $step ) {
			
				if ( $i % 2 != 0 ) {
					$align = 'left';
				}
				else {
					$align = 'right';
				}
				
				if ( $i == $all ) {
					$cls = 'last';
				}
				  
				echo '<div class="process_box span12 '.$cls.'" data-align="'.$align.'">';
			
					echo '<div class="number"><span>';
						
						if ( $i < 10 ) {
							echo '0'.$i;
						}
						else {
							echo $i;
						}
					
					echo '</span></div>';
			
					echo '<div class="content">';
						// STEP CONTENT
						if ( !empty ( $step['stp_single_desc'] ) ) {
							if (preg_match('%(<p[^>]*>.*?</p>)%i', $step['stp_single_desc'], $regs)) {
								echo $step['stp_single_desc'];
							} else {
								echo '<p>'.$step['stp_single_desc'].'</p>';
							}
						}
					echo '</div>';
					echo '<div class="clear"></div>';
				echo '</div>';

				$i++;
				
			}
		}

	}
	
/*--------------------------------------------------------------------------------------------------
	STEPS BOX 2
--------------------------------------------------------------------------------------------------*/
 
	function _step_box2( $options )
	{
	
		if ( !empty ( $options['stp_title'] ) ) {
			echo '<div class="span12">';
			echo '<h3 class="m_title">'.$options['stp_title'].'</h3>';
			echo '</div>';
		}
	
		if ( !empty ( $options['steps_single2'] ) && is_array ( $options['steps_single2'] ) ) {
		
			
		
			foreach ( $options['steps_single2'] as $step ) {
			
				$ok = '';
				$image = '';
			
				if ( $step['stp_single_ok'] == 'yes' ) {
					$ok = 'ok';
					$image = '<img src="'.MASTER_THEME_DIR.'/images/ok.png" alt="">';
				}
			
				echo '<div class="span4">';
					
					echo '<div class="gobox '.$ok.'">';
					
					echo $image;	
						
					if ( !empty ( $step['stp_single_title'] ) ) {
						echo '<h4>'.$step['stp_single_title'].'</h4>';
					}
					
					if ( !empty ( $step['stp_single_link']['url'] ) ) {
						echo '<a class="zn_step_link" href="'.$step['stp_single_link']['url'].'" target="'.$step['stp_single_link']['target'].'"></a>';
					}					

					

					if ( !empty ( $step['stp_single_desc'] ) ) {
						echo '<p>'.$step['stp_single_desc'].'</p>';
					}

					echo '</div>';
					
				echo '</div>';

				
			}
		}

	}
	
/*--------------------------------------------------------------------------------------------------
	STEPS BOX
--------------------------------------------------------------------------------------------------*/
 
	function _step_box($options)
	{
	?>
		<div class="span12">
			<div class="process_steps fixclear">
				<div class="step intro">
				<?php
					if ( !empty ( $options['stp_title'] ) || !empty ( $options['stp_subtitle'] ) ) {
						
						echo '<h3>';
						// TITLE
						if ( !empty ( $options['stp_title'] ) ) {
							echo $options['stp_title'];
						}
						// TITLE
						if ( !empty ( $options['stp_subtitle'] ) ) {
							echo '<strong>'.$options['stp_subtitle'].'</strong>';
						}
						echo '</h3>';

					}
					
					// CONTENT
					if ( !empty ( $options['stp_desc'] ) ) {
						if (preg_match('%(<p[^>]*>.*?</p>)%i', $options['stp_desc'], $regs)) {
							echo $options['stp_desc'];
						} else {
							echo '<p>'.$options['stp_desc'].'</p>';
						}
					}
					
					if ( !empty (  $options['stp_text_link'] ) && !empty ( $options['stp_link']['url'] ) ) {
						echo '<a href="'.$options['stp_link']['url'].'" target="'.$options['stp_link']['target'].'">'.$options['stp_text_link'].' +</a>';
					}
					
				?>
					
					
				</div><!-- end step -->
				
				<?php
					if ( !empty ( $options['steps_single'] ) && is_array ( $options['steps_single'] ) ) {
					
						$i = 1;
					
						foreach ( $options['steps_single'] as $step ) {
						
							echo '<div class="step step'.$i.'">';
								
								$animation = $step['stp_single_anim'];
								
								// ICON AND ANIMATION
								if ( !empty ( $step['stp_single_icon'] ) ) {
									echo '<div class="icon" data-animation="'.$animation.'">';
										echo '<img src="'.$step['stp_single_icon'].'" alt="">';
									echo '</div>';
								}
								
								// STEP TITLE
								if ( !empty ( $step['stp_single_title'] ) ) {
									echo '<h3>'.$step['stp_single_title'].'</h3>';
								}
								
								// STEP CONTENT
								if ( !empty ( $step['stp_single_desc'] ) ) {
									if (preg_match('%(<p[^>]*>.*?</p>)%i', $step['stp_single_desc'], $regs)) {
										echo $step['stp_single_desc'];
									} else {
										echo '<p>'.$step['stp_single_desc'].'</p>';
									}
								}
								
							echo '</div>';

							
							if ( $i == 3 ) { $i = 0;}
							$i++;
							
						}
					}
				?>
				
			</div>

		</div>
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Image Box
--------------------------------------------------------------------------------------------------*/
 
	function _image_box($options)
	{
	
		
		$element_size = zn_get_size( $options['_sizer'] );
	
		$link_start = '<span class="zn_image_box_cont">';
		$link_end = '</span>';
		$image = '';
		$title = '';
		$text = '';
		$link_text = '';
	
		// Title
		if ( !empty ( $options['image_box_title'] ) ) 
		{ 
			$title = '<h3 class="m_title">'.$options['image_box_title'].'</h3>';
		}
	
		// TEXT
		if ( !empty ( $options['image_box_text'] ) ) 
		{ 
			$text = $options['image_box_text'];
		}
		
		// READ MORE TEXT
		if ( !empty ( $options['image_box_link_text'] ) ) 
		{ 
			$link_text = '<h6>'.$options['image_box_link_text'].'</h6>';
		}
		
	
		// STYLE 2 - IMAGE IS ON THE RIGHT
		if ( !empty ( $options['image_box_style'] ) && $options['image_box_style'] == 'style2'  ) 
		{ 
			$zn_style = 'imgboxes_style1 zn_ib_style2';
			
			// IMAGE
			if ( !empty ( $options['image_box_image'] ) ) {
				$image = vt_resize( '',$options['image_box_image'] , '220','156', true );
				$image = '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="" title="'.$options['image_box_title'].'">';
			}
			

			if ( !empty($options['image_box_link']['url']) ) {
				$link_start = '<a class="hoverBorder alignright" href="'.$options['image_box_link']['url'].'" target="'.$options['image_box_link']['target'].'">';
				$link_end = '</a>';
			}
			else {
				$link_start = '<span class="zn_image_box_cont alignright">';
				$link_end = '</span>';
			}

			
			echo '<div class="'.$element_size['sizer'].' box image-boxes '.$zn_style.'">';
		
				echo $title;

					echo $link_start;
					
						echo $image;
						
					echo $link_end;

				echo $text;

			echo '</div><!-- end span -->';

		}
		// STYLE 3 - CONTENT IS OVER IMAGE
		elseif ( !empty ( $options['image_box_style'] ) && $options['image_box_style'] == 'style3'  ) {
			$zn_style = 'imgboxes_style2';
			// IMAGE
			if ( !empty ( $options['image_box_image'] ) ) {
				$image = vt_resize( '',$options['image_box_image'] , $element_size['width'],'', true );
				$image = '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="" title="'.$options['image_box_title'].'">';
			}
			
			if ( !empty($options['image_box_link']['url']) ) {
				$link_start = '<a class="slidingDetails" href="'.$options['image_box_link']['url'].'" target="'.$options['image_box_link']['target'].'">';
				$link_end = '</a>';
			}
			else {
				$link_start = '<a class="slidingDetails" href="javascript:;" target="'.$options['image_box_link']['target'].'">';
				$link_end = '</a>';
			}

			// Title
			if ( !empty ( $options['image_box_title'] ) ) 
			{ 
				$title = '<h4>'.$options['image_box_title'].'</h4>';
			}
			
			echo '<div class="'.$element_size['sizer'].' box image-boxes '.$zn_style.'">';
		
				echo $link_start;
				
					echo $image;
					
					echo '<div class="details">';
					
						echo $title;
						echo $text;
						
					echo '</div>';
					
				echo $link_end;

			echo '</div><!-- end span -->';

			
		}
		// STYLE 1 - IMAGE WITH READ MORE BUTTON OVER IT
		else {
			$zn_style = 'imgboxes_style1';
			$link_start = '<span>';
			$link_end = '</span>';


			// IMAGE
			if ( !empty ( $options['image_box_image'] ) ) {
				$image = vt_resize( '',$options['image_box_image'] , $element_size['width'],'', true );
				$image = '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="" title="'.$options['image_box_title'].'">';
			}
			
			if ( !empty($options['image_box_link']['url']) ) {
				$link_start = '<a class="hoverBorder" href="'.$options['image_box_link']['url'].'" target="'.$options['image_box_link']['target'].'">';
				$link_end = $link_text.'</a>';
			}


			echo '<div class="'.$element_size['sizer'].' box image-boxes '.$zn_style.'">';
		
				echo $link_start;
				echo "<p style=\"text-align:center;\">{$title}</p>";
				echo $image;
					
				echo $link_end;

				
				echo "<br>".$text;
				
			echo '</div><!-- end span -->';

		}
	

		

	}
	
/*--------------------------------------------------------------------------------------------------
	3D CUTE SLIDER
--------------------------------------------------------------------------------------------------*/
	function _cute_slider($options)
	{
		global $meta_fields;
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}

	?>
		<div id="slideshow" class="<?php echo $style; ?>">
			<div class="bgback"></div>
			
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
				<div class="container zn_slideshow">
					<?php echo do_shortcode('[cuteslider id="'.$options['cuteslider_id'].'"]'); ?>
				</div>
			<div class="zn_header_bottom_style"></div>
		</div><!-- end page_header -->
	<?php	
	}

/*--------------------------------------------------------------------------------------------------
	REVOLLUTION SLIDER
--------------------------------------------------------------------------------------------------*/
	function _rev_slider($options)
	{
		global $meta_fields;
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}

	?>
		<div id="slideshow" class="<?php echo $style; ?> portfolio_devices zn_slideshow">
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>

				
					<?php echo do_shortcode('[rev_slider '.$options['revslider_id'].']'); ?>
				

			<div class="zn_header_bottom_style"></div>
		</div><!-- end page_header -->
	<?php	
	}

/*--------------------------------------------------------------------------------------------------
	Document Header
--------------------------------------------------------------------------------------------------*/
	function _zn_doc_header($options)
	{

		if ( !empty ( $options['hm_header_style'] ) ) { 
			$style = 'uh_'.$options['hm_header_style'];
		} else { 
			$style = '';
		}

		?>
			<div id="page_header" class="<?php echo $style; ?> zn_documentation_page" >
				<div class="bgback"></div>
				
				<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
				<div class="container">
					<div class="row">
						<div class="span12">
							<div class="zn_doc_search">
								<form method="get" id="" action="<?php echo home_url(); ?>">
									<label class="screen-reader-text" for="s"><?php _e("Search for:",THEMENAME); ?></label>
									<input type="text" value="" name="s" id="s" placeholder="<?php _e("Search the Documentation",THEMENAME); ?>">
									<input type="submit" id="searchsubmit" class="btn" value="Search">
									<input type="hidden" name="post_type" value="documentation">
								</form>
							</div>
						</div>
					</div><!-- end row -->
				</div>
				
				<div class="zn_header_bottom_style"></div>
			</div><!-- end page_header -->
		<?php	
	}

/*--------------------------------------------------------------------------------------------------
	Documentation
--------------------------------------------------------------------------------------------------*/
	function _zn_documentation($options)
	{

		 $categories = get_terms( 'documentation_category', array(
								 	'orderby'    => 'name',
								 	'order' => 'ASC',
								 	'hide_empty' => 0,
								 	'show_count ' => 1
								 ) );
		$limit = '6';
		if ( !empty($options['doc_num_items']) ) {
			$limit = $options['doc_num_items'];
		}

		foreach ($categories as $category) {
			echo '<div class="span6">';
				echo '<h3><a href="'.get_term_link( $category->slug, 'documentation_category' ).'">'.$category->name.' ('.$category->count.')</a></h3>';

				$args = array(
					'post_type'     => 'documentation',
					'post_status'   => 'publish',
					'posts_per_page' => $limit,
					'documentation_category'	=> $category->slug
				);

				$zn_doc = new WP_Query( $args );

				echo '<ol>';

					while( $zn_doc->have_posts() ): $zn_doc->the_post();

					global $post;

						echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';

					endwhile; // end loop

				echo "</ol>";

			echo '</div>';
		}
	}

	
/*--------------------------------------------------------------------------------------------------
	Custom Header Layout
--------------------------------------------------------------------------------------------------*/
	function _header_module($options)
	{
		global $meta_fields;			$height='';
	
		if ( !empty ( $options['hm_header_style'] ) && $options['hm_header_style'] != 'zn_def_header_style'  ) { 
			$style = 'uh_'.$options['hm_header_style'];
		} elseif( !empty ( $options['hm_header_style'] ) && $options['hm_header_style'] == 'zn_def_header_style' ){
			$style = 'zn_def_header_style';
		} 
		else { 
			$style = '';
		}
		if ( !empty ( $options['hm_header_height'] ) ) {			$height='style="height:'.$options['hm_header_height'].'px;min-height:'.$options['hm_header_height'].'px""';		}

	?>
		<div id="page_header" class="<?php echo $style; ?> bottom-shadow" <?php echo $height;?>>
			<div class="bgback"></div>
			
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
			
			
			
			<div class="container">
				<div class="row">
					<div class="span6">
					
						<?php 
						
							// Breadcrumbs check
							if ( isset ( $options['hm_header_bread'] ) && !empty ( $options['hm_header_bread'] ) ) {
								zn_breadcrumbs();
							}
							
							// Date check
							if ( isset ( $options['hm_header_date'] ) && !empty ( $options['hm_header_date'] ) ) {
								echo '<span id="current-date">'.date_i18n(get_option('date_format') ,strtotime(date("l M d, Y"))).'</span>';
							}
							
						?>
						
					</div>
					<div class="span6">
						<div class="header-titles">
							<?php
							
							// Title check
							if ( isset ( $options['hm_header_title'] ) && !empty ( $options['hm_header_title'] ) ) {
								if ( isset ( $meta_fields['page_title'] ) && !empty ( $meta_fields['page_title'] ) ) {

									echo '<h2>'.do_shortcode( stripslashes( $meta_fields['page_title'] ) ).'</h2>';
							
								}
								else {
									echo '<h2>'.get_the_title().'</h2>';
								}
							}

							?> 
							<?php
							
							// Subtitle check
							if ( isset ( $options['hm_header_subtitle'] ) && !empty ( $options['hm_header_subtitle'] ) ) {
								if ( isset ( $meta_fields['page_subtitle'] ) && !empty ( $meta_fields['page_subtitle'] ) ) {

									echo '<h4>'.do_shortcode( stripslashes( $meta_fields['page_subtitle'] ) ).'</h4>';
									
								}
							}

							?>

						</div>
					</div>
				</div><!-- end row -->
			</div>
			
			<div class="zn_header_bottom_style"></div>
		</div><!-- end page_header -->
	<?php	
	}

/*--------------------------------------------------------------------------------------------------
	iOS Slider
--------------------------------------------------------------------------------------------------*/
 
	function _iosSlider($options)
	{
	
		if ( isset ( $options['io_header_style'] ) && !empty ( $options['io_header_style'] ) ) { 
			$style = 'uh_'.$options['io_header_style'];
		} else { 
			$style = '';
		}
	
		if ( isset ( $options['io_s_fade'] ) && !empty ( $options['io_s_fade'] ) ) { 
			$faded = 'faded';
		} else { 
			$faded = '';
		}
		
		if ( isset ( $options['io_s_scroll'] ) && !empty ( $options['io_s_scroll'] ) ) { 
			$scroll = 'slider_fixedd';
		} else { 
			$scroll = '';
		}
		
		if ( !empty ( $options['io_s_width'] ) ) { 
			$padded = 'notPadded';
			$fluid_start = '<div class="fluidHeight"><div class="sliderContainer ">';
			$fluid_end = '</div></div>';
			$wid_fixed = 'fixed';
		} else { 
			$padded = '';
			$fluid_start = '';
			$fluid_end = '';
			$wid_fixed = '';
		}
	
		if ( !empty ( $options['io_s_s_height'] ) && empty ( $options['io_s_width'] ) ) {

			$s_height = 'style="padding:0 0 '.$options['io_s_s_height'].'% 0;overflow:hidden;"';

		}
		else {
			$s_height = '';
		}


	?>
		<div id="slideshow" class="<?php echo $style; ?> <?php echo $faded; ?> <?php echo $padded; ?> <?php echo $scroll; ?> " <?php echo $s_height; ?>>
        	
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
			
			<?php echo $fluid_start; ?>
			
			<div class = "iosSlider <?php echo $faded; ?> <?php echo $wid_fixed; ?> zn_slideshow container">
			
				<div class="slider">
					
				<?php
					if ( isset ( $options['single_iosslider'] ) && is_array ( $options['single_iosslider'] ) ) {
					
						$thumbs = '';
						$i = 0;
						$bullets = '';
						
						foreach ( $options['single_iosslider'] as $slide ) {
							
							if ( $i == 0 ) { $slide_num = 'first selected'; } else { $slide_num = ''; }
							
							$c_style = 'style1';
							$c_pos = '';
							
							
							$bullets .= '<div class="item '.$slide_num.'"></div>';
							
							echo '<div class = "item">';
							
								$img_link_start = '';
								$img_link_end = '';

								if ( !empty ( $slide['io_slide_link']['url'] ) && !empty($slide['io_slide_link_image']) && $slide['io_slide_link_image'] == 'yes' ) {
									$img_link_start = '<a class="zn_slide_image_link" href="'.$slide['io_slide_link']['url'].'" target="'.$slide['io_slide_link']['target'].'">';
									$img_link_end = '</a>';
								}

								// Slide Image
								if ( !empty ( $slide['io_slide_image'] )  ) {
								
									if ( $options['io_s_width'] ) {
									
										$image = vt_resize( '',$slide['io_slide_image'] , '1170','', true );
										echo $img_link_start.'<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="">'.$img_link_end;
										
									}else {
										echo $img_link_start.'<img src="'.$slide['io_slide_image'].'" alt="" class="img-responsive">'.$img_link_end;
									}
									
									
									if ( isset ( $options['io_s_navigation'] ) && $options['io_s_navigation'] == 'thumbs' ) {
										
										$image = vt_resize( '',$slide['io_slide_image'] , '150','60', true );
										$thumbs .= '<div class="item '.$slide_num.'"><img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" /></div>';
									}
									
								}
								
								// Slide Caption Style
								if ( isset ( $slide['io_slide_caption_style'] ) && !empty ( $slide['io_slide_caption_style'] ) ) {
									$c_style = $slide['io_slide_caption_style'];
								}
								
								// Slide Caption Position
								if ( isset ( $slide['io_slide_caption_pos'] ) && !empty ( $slide['io_slide_caption_pos'] ) ) {
									$c_pos = $slide['io_slide_caption_pos'];
								}
								
								echo '<div class="caption '.$c_style.' '.$c_pos.'">';
									
									

									// Slide Main TITLE
									if ( isset ( $slide['io_slide_m_title'] ) && !empty ( $slide['io_slide_m_title'] ) ) {
										echo '<h2 class="main_title">'.$slide['io_slide_m_title'].'</h2>';
									}
									
									// Slide BIG TITLE
									if ( isset ( $slide['io_slide_b_title'] ) && !empty ( $slide['io_slide_b_title'] ) ) {
										echo '<h3 class="title_big">'.$slide['io_slide_b_title'].'</h3>';
									}
									
									if ( !empty ( $slide['io_slide_link']['url'] ) && $slide['io_slide_caption_style'] != 'style3' )
									{
										echo '<a class="more" href="'.$slide['io_slide_link']['url'].'" target="'.$slide['io_slide_link']['target'].'"><img src="'.MASTER_THEME_DIR.'/sliders/iosslider/arr01.png" alt=""></a>';
									}
									
									// Slide SMALL TITLE
									if ( isset ( $slide['io_slide_s_title'] ) && !empty ( $slide['io_slide_s_title'] ) ) {
										echo '<h4 class="title_small">'.$slide['io_slide_s_title'].'</h4>';
									}
								echo $img_link_start .  $img_link_end;
								
								echo '</div>';
							
							echo '</div><!-- end item -->';
							
							$i++;
													
						}
					
					}
					

					echo '</div>';


					if ( count($options['single_iosslider']) > 1 ) {
						
						echo '<div class="prev"><div class="btn-label">'.__('PREV',THEMENAME).'</div></div>';
						echo '<div class="next"><div class="btn-label">'.__('NEXT',THEMENAME).'</div></div>';

						if ( !$options['io_s_width'] && $options['io_s_navigation'] == 'thumbs' ) {
							
								?>
									<div class="selectorsBlock thumbs">
										<a href="#" class="thumbTrayButton"><span class="icon-minus icon-white"></span></a>
										<div class="selectors">
											<?php echo $thumbs;?>
										</div>
									</div>
								<?php
							
						}

						echo '</div><!-- end iosSlider -->';

						if ( $options['io_s_width'] || $options['io_s_navigation'] != 'thumbs' ) {
								?>
									<div class="selectorsBlock bullets">
										<div class="selectors">
											<?php echo $bullets;?>
										</div>
									</div>
								<?php
						}

					}
					else {
						echo '</div><!-- end iosSlider -->';
					}

				?>


				
			
            <div class="scrollbarContainer"></div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
			<?php echo $fluid_end; ?>
			
        </div><!-- end slideshow -->
		
		<div class="zn_fixed_slider_fill"></div>
	<?php
	}
	
	
/*--------------------------------------------------------------------------------------------------
	CSS3 Panels
--------------------------------------------------------------------------------------------------*/

	function _css_pannel($options)
	{
	?>
        <div id="slideshow" class="">
			
			<div id="css3panels" style="height:<?php echo $options['css_height'];?>px">
				<div id="loading"></div>
								
				<?php
					//print_r($options);
					if ( isset ( $options['single_css_panel'] ) && is_array ( $options['single_css_panel'] ) ) {
						
						$i = 1;
						$all = count ( $options['single_css_panel'] );
						
						
						
						foreach ( $options['single_css_panel'] as $panel ) {
							
							$width = 100/$all;
							
							$panel_position ='';
							$style = '';
							
							if ( $i == 1 ) {
								$first = 'first';
								$width = $width+3;
								
							} else { $first =''; }
							
							if ( $i == $all ) {
								$last = 'last';
								$width = $width+4;
								
							} else { $last =''; }
							
							echo '<div class="panel panel'.$i.' '.$first.' '.$last.'" style="'.$style.'width:'.$width.'%;">';
								echo '<div class="inner-panel">';
									
									// Panel Image
									if ( isset ( $panel['panel_image'] ) && !empty ( $panel['panel_image'] ) ) {
										echo '<img src="'.$panel['panel_image'].'" alt="" class="main_image">';
									}
									
									// Panel Position
									if ( isset ( $panel['panel_title_position'] ) && !empty ( $panel['panel_title_position'] ) ) {
										$panel_position = $panel['panel_title_position'];
									}
									
									// Panel Title
									if ( isset ( $panel['panel_title'] ) && !empty ( $panel['panel_title'] ) ){
										echo '<div class="caption '.$panel_position.'">';
											echo '<h3 class="title">'.$panel['panel_title'].'</h3>';
										echo '</div>';
									}
									
								echo '</div>';
							echo '</div>';
							
							$i++;
						}
						
					}
					
				?>

			</div><!-- end panels -->
			<div class="clear"></div>
			
        </div><!-- end slideshow -->
	<?php
	}

/*--------------------------------------------------------------------------------------------------
	Fancy Slider
--------------------------------------------------------------------------------------------------*/
 
	function _fancyslider($options)
	{	
	?>
	
        <div id="slideshow" class="">
			
			<div class="container">
			
				<div class="flexslider zn_fancy_slider">
					<ul class="slides">
					<?php
							if ( isset ( $options['single_fancy'] ) && is_array ( $options['single_fancy'] ) ) {
								
								foreach ( $options['single_fancy'] as $slide ) {
								
									$link_start = '';
									$link_end = '';

									
									if ( isset ( $slide['ww_slide_link']['url'] ) && !empty ( $slide['ww_slide_link']['url'] ) )
									{
										// Set defaults 
										$link_start = '<a class="link" href="'.$slide['ww_slide_link']['url'].'" target="'.$slide['ww_slide_link']['target'].'">';
										$link_end = '</a>';
									
									}
								
									echo '<li data-color="'.$slide['ww_slide_color'].'">';
									
										echo $link_start;
										
											if ( isset ( $slide['ww_slide_image'] ) && !empty ( $slide['ww_slide_image'] ) ) {
											
												$image = vt_resize( '',$slide['ww_slide_image'] , '940','', true );
												echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" />';
													
											}
										
										echo $link_end;
									
									echo '</li>';
								
								}
								
							}
					?>
					
					
						
					</ul>
				</div><!-- end #flexslider -->
				
			</div>	
			<div class="shadowUP"></div>
			<div class="shadowUP"></div>
        </div><!-- end slideshow -->	
	
	<?php
	}

/*--------------------------------------------------------------------------------------------------
	CIRCULAR CONTENT STYLE 1
--------------------------------------------------------------------------------------------------*/
 
	function _circ2($options)
	{
	
		global $data;

		$responsive_check = 'zn_responsive';
		if ( $data['zn_responsive'] == 'no' ) {
			$responsive_check = 'zn_not_responsive';
		}

		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow"  class="<?php echo $style; ?>">
		
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="container zn_slideshow <?php echo $responsive_check; ?>">

				<div id="ca-container" class="ca-container">
                    <div class="ca-wrapper">
                    <?php
						
						if ( isset ( $options['single_circ2'] ) && is_array ( $options['single_circ2'] ) ) {
								
							$i = 1;
							$thumbs = '';
							
							foreach ( $options['single_circ2'] as $slide ) {					

								echo '<div class="ca-item ca-item-'.$i.'">';
							
									echo '<div class="ca-item-main">';
									
										if ( isset ( $slide['ww_slide_image'] ) && !empty ( $slide['ww_slide_image'] ) ) {
										
											echo '<div class="ca-icon">';
												$image = vt_resize( '',$slide['ww_slide_image'] , '376','440', true );
												$bg = 'style="background-image:url('.$image['url'].');"';
												
											echo '</div>';	
										}
									
										echo '<div class="background" '.$bg.'></div><!-- background color -->';
									

									
										// TITle
										if ( isset ( $slide['ww_slide_title'] ) && !empty ( $slide['ww_slide_title'] ) ) {
										
											if ( isset ( $slide['ww_slide_title_left'] ) && !empty ( $slide['ww_slide_title_left'] ) ) {
												$title_pos_l = 'left:'.$slide['ww_slide_title_left'].'px;';
											}
											
											if ( isset ( $slide['ww_slide_title_top'] ) && !empty ( $slide['ww_slide_title_top'] ) ) {
												$title_pos_t = 'top:'.$slide['ww_slide_title_top'].'px;';
											}
										
											echo '<div class="title_circle" style="'.$title_pos_t.' '.$title_pos_l.'" data-size="'.$slide['ww_slide_title_size'].'">';
												echo '<span>'.$slide['ww_slide_title'].'</span>';
											echo '</div>';
										}
									
										// DESC
										if ( isset ( $slide['ww_slide_desc'] ) && !empty ( $slide['ww_slide_desc'] ) ) {
											echo '<h4>'. $slide['ww_slide_desc'].'</h4>';
										}
										
										// DESC
										if ( isset ( $slide['ww_slide_read_text'] ) && !empty ( $slide['ww_slide_read_text'] ) ) {
											echo '<a href="#" class="ca-more">'.$slide['ww_slide_read_text'].' <span class="icon-chevron-right icon-white"></span></a>';
										}
							
										// Bottom Title
										if ( isset ( $slide['ww_slide_bottom_title'] ) && !empty ( $slide['ww_slide_bottom_title'] ) ) {
											echo '<span class="ca-starting">'. $slide['ww_slide_bottom_title'].'</span>';
										}
									
									echo '</div>';
									
									echo '<div class="ca-content-wrapper">';
										echo '<div class="ca-content">';
									
											// Content Title
											if ( isset ( $slide['ww_slide_content_title'] ) && !empty ( $slide['ww_slide_content_title'] ) ) {
												echo '<h6>'.$slide['ww_slide_content_title'].'</h6>';
											}
											
											echo '<a href="#" class="ca-close"><span class="icon-remove"></span></a>';
									
											// Content description
											if ( isset ( $slide['ww_slide_desc_full'] ) && !empty ( $slide['ww_slide_desc_full'] ) ) {
												echo '<div class="ca-content-text">';
												
													echo $slide['ww_slide_desc_full'];
												
												echo '</div>';
											}
									
											// Link
											if ( isset ( $slide['ww_slide_read_text_content'] ) && !empty ( $slide['ww_slide_read_text_content'] ) && isset ( $slide['ww_slide_link']['url'] ) && !empty ( $slide['ww_slide_link']['url'] ) ) {
												echo '<a href="'.$slide['ww_slide_link']['url'].'" target="'.$slide['ww_slide_link']['target'].'">'.$slide['ww_slide_read_text_content'].'</a>';
											}
									
										echo '</div>';
									echo '</div>';
									

							
								echo '</div><!-- end ca-item -->';
							
								$i++;
							
							}
						}
					?>

                    </div><!-- end ca-wrapper -->
                </div><!-- end circular content carousel -->

			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}

	
/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Video Background
--------------------------------------------------------------------------------------------------*/
 
	function _static8($options)
	{
	?>
        <div id="slideshow" class="gradient">
        		
                <div class="video-container">
				
				<?php
					
					if ( $options['sc_vb_video_type'] == 'self' ) { 
						echo '<video autoplay class="video" loop id="the-video"> ';
							
							if ( !empty ( $options['sc_vb_sh_video1'] ) ) { 
								echo  '<source src="'.$options['sc_vb_sh_video1'].'"/> ';
							} 	
							
							if ( !empty ( $options['sc_vb_sh_video2'] ) ) { 
								echo  '<source src="'.$options['sc_vb_sh_video2'].'" type=\'video/ogg; codecs="theora, vorbis"\'/> ';
							} 
							
							if ( !empty ( $options['sc_vb_sh_video_cover'] ) ) { 
								echo  '<img src="'.$options['sc_vb_sh_video_cover'].'"> ';
							} 
							
						echo '</video>';
					}
					elseif ( $options['sc_vb_video_type'] == 'iframe' && !empty ( $options['sc_vb_embed'] ) ){
						echo  get_video_from_link( $options['sc_vb_embed'] ,'' , '400','600');
					}
					
					echo '<div class="captions">';
					
						if ( !empty ( $options['sc_vb_line1'] ) ) { 
							echo  '<span class="line">'.do_shortcode($options['sc_vb_line1']).'</span>';
						} 
					
						if ( !empty ( $options['sc_vb_line2'] ) ) { 
							echo  '<span class="line">'.do_shortcode($options['sc_vb_line2']).'</span>';
						} 
					
					echo '</div>';
					
					
				?>
				
                </div>
                
        </div><!-- end slideshow -->
	<?php
	}

/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Simple text
--------------------------------------------------------------------------------------------------*/
 
	function _static9($options)
	{
	?>
        <div id="slideshow" class="nobg">

                <div class="container">
                	<div class="static-content simple">
                    	
						<div class="row">
							<div class="span12">
								<?php

									echo do_shortcode($options['sc_sc']);

									if (!empty( $options['sc_button_text'] ) && !empty( $options['sc_button_link']['url'] ) ) {
										

										echo '<a href="'.$options['sc_button_link']['url'].'" target="'.$options['sc_button_link']['target'].'" class="btn btn-large btn-flat">'.$options['sc_button_text'].'</a><span class="line"></span>';

									}

								?>
                               
                                
							</div>
						</div><!-- end row -->
                    </div><!-- end static content -->
                </div>

        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Text Pop
--------------------------------------------------------------------------------------------------*/
 
	function _static5($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="zn_slideshow">
                <div class="container">
                	<div class="static-content textpop-style">
					<?php
					
						echo '<div class="texts">';
						// Line 1
						if ( !empty ( $options['sc_pop_line1'] ) ) {
							echo '<span class="line1">'.$options['sc_pop_line1'].'</span>';
						}
						
						// Line 2
						if ( !empty ( $options['sc_pop_line2'] ) ) {
							echo '<span class="line2">'.$options['sc_pop_line2'].'</span>';
						}
						
						// Line 3
						if ( !empty ( $options['sc_pop_line3'] ) ) {
							echo '<span class="line3">'.$options['sc_pop_line3'].'</span>';
						}
						
						// Line 4
						if ( !empty ( $options['sc_pop_line4'] ) ) {
							echo '<span class="line4">'.$options['sc_pop_line4'].'</span>';
						}
						
						echo '</div>';

						// BUTTON
						if ( $options['ww_slide_m_button'] || $options['ww_slide_l_text'] ) {
							echo '<div class="info_pop animated fadeBoxIn" data-arrow="top">';
								
								if ( $options['ww_slide_l_text'] && isset ( $options['ww_slide_link']['url'] ) && !empty ( $options['ww_slide_link']['url'] ) ) {
									echo '<a class="buyit" href="'.$options['ww_slide_link']['url'].'" target="'.$options['ww_slide_link']['target'].'">'.$options['ww_slide_l_text'].'</a>';
								}
							
								// BUTTON LEFT TEXT
								if ( isset ( $options['ww_slide_m_button'] ) && !empty ( $options['ww_slide_m_button'] ) ) {
									echo '<h5 class="text">'.$options['ww_slide_m_button'].'</h5>';
								}
								
								echo '<div class="clear"></div>';
							echo '</div>';
						}

					?>
                    </div><!-- end static-content -->
                </div>
			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Maps
--------------------------------------------------------------------------------------------------*/
 
	function _static4($options)
	{

		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}

	?>
        <div id="slideshow" class="<?php echo $style; ?>">
	        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>

            <div class="static-content maps-style">
				<div id="google_map" style="width:100%; height:<?php echo $options['sc_map_height'];?>px;"></div><!-- map container -->
				<ul id="map_controls">
					<li><a id="zoom_in"><span class="icon-plus icon-white"></span></a></li>
					<li><a id="zoom_out"><span class="icon-minus icon-white"></span></a></li>
					<li><a id="reset"><span class="icon-refresh icon-white"></span></a></li>
	            </ul>
				<?php
						// BUTTON
						if ( $options['ww_slide_m_button'] || $options['ww_slide_l_text'] ) {
							echo '<div class="info_pop" data-arrow="top">';
								
								if ( $options['ww_slide_l_text'] && isset ( $options['ww_slide_link']['url'] ) && !empty ( $options['ww_slide_link']['url'] ) ) {
									echo '<a class="buyit" href="'.$options['ww_slide_link']['url'].'" target="'.$options['ww_slide_link']['target'].'">'.$options['ww_slide_l_text'].'</a>';
								}
							
								// BUTTON LEFT TEXT
								if ( isset ( $options['ww_slide_m_button'] ) && !empty ( $options['ww_slide_m_button'] ) ) {
									echo '<h5 class="text">'.$options['ww_slide_m_button'].'</h5>';
								}
								
								echo '<div class="clear"></div>';
							echo '</div>';
						}
				?>
 
			</div>
           
			
    <script type="text/javascript">
	(function($){
		$(document).ready(function() {
		<?php if ( !empty ( $options['sc_map_icon'] ) ) { ?>
			var myMarkers = {
				"markers": [
					{
						"latitude": "<?php echo $options['sc_map_latitude'];?>",		// latitude
						"longitude":"<?php echo $options['sc_map_longitude'];?>",		// longitude
						"icon": "<?php echo $options['sc_map_icon'];?>"	// Pin icon
					}
				]
			};
		<?php } ?>

			
			
			$("#google_map").mapmarker({
				zoom : <?php echo $options['sc_map_zoom'];?>,							// Zoom
				center: "<?php echo $options['sc_map_latitude'];?>,<?php echo $options['sc_map_longitude'];?>",		// Center of map
				type: "<?php echo $options['sc_map_type'];?>",					// Map Type
				controls: "HORIZONTAL_BAR",			// Controls style
				dragging:<?php if ( !empty($options['sc_map_dragg']) ) { echo $options['sc_map_dragg']; } else { echo '0';}?>,							// Allow dragging?
				mousewheel:<?php if ( !empty($options['sc_map_zooming_mousewheel']) ) { echo $options['sc_map_zooming_mousewheel']; } else { echo '0';}?>,	// Allow zooming with mousewheel
				<?php if ( !empty ( $options['sc_map_icon'] ) ) { echo 'markers: myMarkers,';} ?>		
				styling: 0,							// Bool - do you want to style the map?
				featureType:"all",
				visibility: "on",
				elementType:"geometry",
				hue:"#00AAFF",
				saturation:-100,
				lightness:0,
				gamma:1,
				navigation_control:0
				/*
				To play with the map colors and styles you can try this tool right here http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html
				*/
			});
		});
	})(jQuery);
	</script>
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Video
--------------------------------------------------------------------------------------------------*/
 
	function _static3($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}

		if ( !empty($options['ww_height'])) {
			$height = 'style="height:'.$options['ww_height'].'px;"';
		}
	
	?>
        <div id="slideshow" <?php echo $height;?> class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="zn_slideshow">
                <div class="container">
                	<div class="static-content video-style">
					<?php
						// TITLE
						if ( isset ( $options['ww_slide_title'] ) && !empty ( $options['ww_slide_title'] ) ) {
							echo '<h3 class="centered">'.do_shortcode($options['ww_slide_title']).'</h3>';
						}
						
						// VIDEO
						if ( isset ( $options['ww_slide_video'] ) && !empty ( $options['ww_slide_video'] ) ) {
							
							echo '<div class="video_trigger_container">';
								echo '<a class="playVideo" data-rel="prettyPhoto" href="'.$options['ww_slide_video'].'"></a>';
								echo $options['ww_slide_video_text'];
							echo '</div>';
							
							
						}
						
					?>
                    </div><!-- end static-content -->
                </div>
			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}

/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - TEXT AND REGISTER
--------------------------------------------------------------------------------------------------*/
 
	function _static10($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
                <div class="container zn_slideshow">
                	<div class="static-content default-style with-login">
                    	
						<div class="row">
							<div class="span7">
								<?php
									if (!empty($options['ww_slide_title'])) {
										echo '<h2>'.do_shortcode( $options['ww_slide_title'] ).'</h2>';
									}

									if (!empty( $options['ww_slide_subtitle'] )) {
										echo '<h3>'.do_shortcode( $options['ww_slide_subtitle'] ).'</h3>';
									}

									if ( !empty( $options['ww_slide_m_button'] ) && !empty ( $options['ww_slide_l_text'] ) && !empty($options['ww_slide_link']['url']) ) {

										echo '<div class="info_pop animated fadeBoxIn left" data-arrow="top">';
										echo '<a href="'.$options['ww_slide_link']['url'].'" target="'.$options['ww_slide_link']['url'].'" class="buyit">'.$options['ww_slide_l_text'].'</a>';
										echo '<h5 class="text">'.$options['ww_slide_m_button'].'</h5>';
										echo '<div class="clear"></div>';
										echo '</div>';

									}

								?>

							</div>
							<div class="span5">
								<div class="fancy_register_form">
                                	<h4 style="text-align:center"><?php _e("Create <strong>your account</strong> now",THEMENAME);?></h4>
										<form id="register_form_static" name="login_form" method="post" class="zn_form_login" action="<?php echo site_url('wp-login.php?action=register', 'login_post') ?>">
	                                    	<div>
	                                        	<label for="reg-username"><?php _e("Username",THEMENAME);?></label>
	                                            <input type="text" id="reg-username" name="user_login" class="inputbox" required placeholder="<?php _e("Username",THEMENAME);?>">
	                                        </div>
	                                        <div>
	                                        	<label for="reg-email"><?php _e("Email",THEMENAME);?></label>
	                                            <input type="email" id="reg-email" name="user_email" class="inputbox required" placeholder="<?php _e("Your email",THEMENAME);?>">
	                                        </div>
	                                        <div>
	                                        	<label for="reg-pass"><?php _e("Your password",THEMENAME);?></label>
	                                            <input type="password" id="reg-pass" name="user_password" class="inputbox" required placeholder="<?php _e("Your password",THEMENAME);?>">
	                                        </div>
	                                        <div>
	                                        	<label for="reg-pass2"><?php _e("Verify password",THEMENAME);?></label>
	                                            <input type="password" id="reg-pass2" name="user_password2" class="inputbox" required placeholder="<?php _e("Verify password",THEMENAME);?>">
	                                        </div>
											<div style="margin-bottom:0;">
												<input type="submit" id="signup" name="submit" class="zn_sub_button btn btn-danger"  value="<?php _e("REGISTER",THEMENAME);?>">
											</div>
											<input type="hidden" value="register" class="" name="zn_form_action">
											<input type="hidden" value="zn_do_login" class="" name="action">
											<input type="hidden" value="<?php echo $_SERVER['PHP_SELF'] ?>" class="zn_login_redirect" name="submit">
											<div class="links"></div>
										</form>
                                </div>
							</div>
						</div><!-- end row -->
                        
                    </div><!-- end static content -->
                </div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}

/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - TEXT AND VIDEO
--------------------------------------------------------------------------------------------------*/
 
	function _static11($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
                <div class="container zn_slideshow">
                	<div class="static-content default-style with-login">
                    	
						<div class="row">
							<div class="span7">
								<?php
									if (!empty($options['ww_slide_title'])) {
										echo '<h2>'.do_shortcode( $options['ww_slide_title'] ).'</h2>';
									}

									if (!empty( $options['ww_slide_subtitle'] )) {
										echo '<h3>'.do_shortcode( $options['ww_slide_subtitle'] ).'</h3>';
									}

									if ( !empty( $options['ww_slide_m_button'] ) && !empty ( $options['ww_slide_l_text'] ) && !empty($options['ww_slide_link']['url']) ) {

										echo '<div class="info_pop animated fadeBoxIn left" data-arrow="top">';
										echo '<a href="'.$options['ww_slide_link']['url'].'" target="'.$options['ww_slide_link']['url'].'" class="buyit">'.$options['ww_slide_l_text'].'</a>';
										echo '<h5 class="text">'.$options['ww_slide_m_button'].'</h5>';
										echo '<div class="clear"></div>';
										echo '</div>';

									}

								?>

							</div>
							<div class="span5">
								<?php
									// Text
									if ( isset ( $options['sc_ec_vid_desc'] ) && !empty ( $options['sc_ec_vid_desc'] ) ) {
										echo '<h5 style="text-align:right;">'.$options['sc_ec_vid_desc'].'</h5>';
									}
									
								
								
									// VIDEO
									if ( isset ( $options['sc_ec_vime'] ) && !empty ( $options['sc_ec_vime'] ) ) {
										echo get_video_from_link ( $options['sc_ec_vime'] ,'black_border full_width' ,'520px','270px');
									}
								?>
							</div>
						</div><!-- end row -->
                        
                    </div><!-- end static content -->
                </div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}


/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Event Countdown
--------------------------------------------------------------------------------------------------*/
 
	function _static7($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="zn_slideshow">
                <div class="container">
                	<div class="static-content event-style">
                        <div class="row">
                        	<div class="span7">
							<?php 
								// TITLE
								if ( isset ( $options['sc_ec_title'] ) && !empty ( $options['sc_ec_title'] ) ) {
									echo '<h3>'.do_shortcode($options['sc_ec_title']).'</h3>';
								}
							?>

								
								<div class="ud_counter">
                                    <ul id="Counter">
                                        <li>0<span>day</span></li>
                                        <li>00<span>hours</span></li>
                                        <li>00<span>min</span></li>
                                        <li>00<span>sec</span></li>
                                    </ul>
									<?php echo '<span class="till_lauch"><img src="'.MASTER_THEME_DIR.'/images/rocket.png"></span>'; ?>
                                </div><!-- end counter -->

								
								
								<?php
								
								if ( !empty ( $options['sc_ec_mlid'] ) ) {
								
									echo '<div class="mail_when_ready">';
									echo	'		<form method="post" class="newsletter_subscribe newsletter-signup" data-url="'.trailingslashit(home_url()).'" name="newsletter_form">';
									echo	'			<input type="text" name="zn_mc_email" class="nl-email" value="" placeholder="'.__("your.address@email.com",THEMENAME).'" />';
									echo	'			<input type="hidden" name="zn_list_class" class="nl-lid" value="'.$options['sc_ec_mlid'].'" />';
									echo	'			<input type="submit" name="submit" class="nl-submit" value="'.__("JOIN US",THEMENAME).'" />';
									echo	'		</form>';
									echo 	'<span class="zn_mailchimp_result"></span>';
									echo 	'</div>';
									
								}
									
								if ( !empty ( $options['sc_ec_mlid'] ) && isset( $options['single_ec_social'] ) && is_array( $options['single_ec_social'] ) ) {
									echo 	'<span class="or">'.__("-or stay connected: ",THEMENAME).'</span>';
								}
									
								if ( isset( $options['single_ec_social'] ) && is_array( $options['single_ec_social'] ) ) {
								
									$icon_class = '';
									
									
									if( $options['sc_ec_social_color'] == 'colored' ) { 
										$icon_class = 'colored';
									}
									
									echo '<ul class="social-icons '.$icon_class.' fixclear">';
										
										foreach ( $options['single_ec_social'] as $key=>$icon ){
										
											$link = '';
											$target = '';
										
											if ( isset ( $icon['sc_ec_social_link'] ) && is_array ( $icon['sc_ec_social_link'] )) {
												$link = $icon['sc_ec_social_link']['url'];
												$target = 'target="'.$icon['sc_ec_social_link']['target'].'"';
											}
											
										
											echo '<li class="'.$icon['sc_ec_social_icon'].'"><a href="'.$link.'" '.$target.'>'.$icon['sc_ec_social_title'].'</a></li>';
										}
										
									echo '</ul>';
									
								}
									
								?>

                                <div class="clear"></div>
                                
                            </div>
							
							<?php
							
								echo '<div class="span5">';
							
								// Text
								if ( isset ( $options['sc_ec_vid_desc'] ) && !empty ( $options['sc_ec_vid_desc'] ) ) {
									echo '<h5 style="text-align:right;">'.$options['sc_ec_vid_desc'].'</h5>';
								}
								
							
							
								// VIDEO
								if ( isset ( $options['sc_ec_vime'] ) && !empty ( $options['sc_ec_vime'] ) ) {
									echo get_video_from_link ( $options['sc_ec_vime'] ,'black_border full_width' ,'520px','270px');
								}
								
								echo '</div>';
							?>
							
                            
                            	
				
                            
                        </div>
                    </div><!-- end static content / event style -->
                </div>
			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Boxes
--------------------------------------------------------------------------------------------------*/
 
	function _static2($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="zn_slideshow">
                <div class="container">
                	<div class="static-content boxes-style">
                    <?php
						// TITLE
						if ( isset ( $options['ww_slide_title'] ) && !empty ( $options['ww_slide_title'] ) ) {
							echo '<h3 class="centered">'.do_shortcode($options['ww_slide_title']).'</h3>';
						}
						
						echo '<div class="boxes row">';
						
						if ( !empty ( $options['ww_box1_title'] ) || !empty ( $options['ww_box1_image'] ) || !empty ( $options['ww_box1_desc'] ) ) {
						
							echo '<div class="span4">';
								echo '<div class="info_pop">';
								
									if ( !empty ( $options['ww_box1_title'] ) || !empty ( $options['ww_box1_image'] ) ) {
										echo '<h4 class="title">';
										
										if ( !empty ( $options['ww_box1_image'] ) ) {
											echo '<img src="'.$options['ww_box1_image'].'" alt="'.$options['ww_box1_title'].'"/>';
										}
										if ( !empty ( $options['ww_box1_title'] ) ) {
											echo $options['ww_box1_title'];
										}

										echo '</h4>';
									}
									
									if ( !empty ( $options['ww_box1_desc'] ) ) {
										echo '<p>'.$options['ww_box1_desc'].'</p>';
									}
								
								echo '</div>';
							echo '</div>';
							
						}
						
						if ( !empty ( $options['ww_box2_title'] ) || !empty ( $options['ww_box2_image'] ) || !empty ( $options['ww_box2_desc'] ) ) {
						
							echo '<div class="span4">';
								echo '<div class="info_pop">';
								
									if ( !empty ( $options['ww_box2_title'] ) || !empty ( $options['ww_box2_image'] ) ) {
										echo '<h4 class="title">';
										
										if ( !empty ( $options['ww_box2_image'] ) ) {
											echo '<img src="'.$options['ww_box2_image'].'" alt="'.$options['ww_box2_title'].'"/>';
										}
										if ( !empty ( $options['ww_box2_title'] ) ) {
											echo $options['ww_box2_title'];
										}

										echo '</h4>';
									}
									
									if ( !empty ( $options['ww_box2_desc'] ) ) {
										echo '<p>'.$options['ww_box2_desc'].'</p>';
									}
								
								echo '</div>';
							echo '</div>';
							
						}
						
						if ( !empty ( $options['ww_box3_title'] ) || !empty ( $options['ww_box3_image'] ) || !empty ( $options['ww_box3_desc'] ) ) {
						
							echo '<div class="span4">';
								echo '<div class="info_pop">';
								
									if ( !empty ( $options['ww_box3_title'] ) || !empty ( $options['ww_box3_image'] ) ) {
										echo '<h4 class="title">';
										
										if ( !empty ( $options['ww_box3_image'] ) ) {
											echo '<img src="'.$options['ww_box3_image'].'" alt="'.$options['ww_box3_title'].'"/>';
										}
										if ( !empty ( $options['ww_box3_title'] ) ) {
											echo $options['ww_box3_title'];
										}

										echo '</h4>';
									}
									
									if ( !empty ( $options['ww_box3_desc'] ) ) {
										echo '<p>'.$options['ww_box3_desc'].'</p>';
									}
								
								echo '</div>';
							echo '</div>';
							
						}
						
						echo '</div>';
						
					?>

                        <script type="text/javascript">
						(function($){
                        	var boxes = $('.static-content .boxes');
							boxes.children().hover(function () {
								var _t = $(this);
								_t.animate({'margin-top':-10}, {duration:500, queue:false, easing:'easeOutExpo'});
								_t.siblings().animate({opacity:0.4}, {duration:500, queue:false, easing:'easeOutExpo'});
							},
							function () {
								var _t = $(this);
								_t.animate({'margin-top':0}, {duration:400, queue:false, easing:'easeOutExpo'});
								_t.siblings().animate({opacity:1}, {duration:400, queue:false, easing:'easeOutExpo'});
							});
						})(jQuery);
                        </script>
                    </div>
                </div>
			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Product Loupe
--------------------------------------------------------------------------------------------------*/
 
	function _static6($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
                <div class="container zn_slideshow">
                	<div class="static-content productzoom-style">
                        <div class="row">
                            <div class="span5">
							<?php
								// TITLE
								if ( !empty ( $options['sc_title'] ) ) {
									echo '<h3>'.do_shortcode( $options['sc_title'] ).'</h3>';
								}
							
								// FEATURES LIST
								if ( !empty ( $options['sc_lp_features'] ) ) {
								
									echo '<ul class="features">';
								
										$textAr = explode("\n", $options['sc_lp_features']);
										foreach ($textAr as $index=>$line) {
											echo '<li><span class="icon-ok icon-white"></span> '.$line.'</li>';
										} 
								
									echo '</ul>';
								}
							
								// First Button
								if ( !empty ( $options['sc_lp_button1'] ) && isset ( $options['sc_lp_button1_link']['url'] ) && !empty ( $options['sc_lp_button1_link']['url'] )  ) {
									echo '<a target="'.$options['sc_lp_button1_link']['target'].'" href="'.$options['sc_lp_button1_link']['url'].'" class="'.$options['sc_lp_button1_style'].' btn-large"><span class="'.$options['sc_lp_button1_icon'].' '.$options['sc_lp_button1_icon_style'].'"></span> '.$options['sc_lp_button1'].'</a> ';
								}
							
								if ( !empty ( $options['sc_lp_button1'] ) && !empty ( $options['sc_2p_button1'] ) && !empty ( $options['sc_bt_text'] ) ) {
									echo '<span class="or">'.$options['sc_bt_text'].'</span> ';
								}
								
								// Second Button
								if ( !empty ( $options['sc_2p_button1'] ) && isset ( $options['sc_lp_button2_link']['url'] ) && !empty ( $options['sc_lp_button2_link']['url'] )  ) {
									echo '<a target="'.$options['sc_lp_button2_link']['target'].'" href="'.$options['sc_lp_button2_link']['url'].'" class="'.$options['sc_lp_button2_style'].' btn-large"><span class="'.$options['sc_lp_button2_icon'].' '.$options['sc_lp_button2_icon_style'].'"></span> '.$options['sc_2p_button1'].'</a> ';
								}
							
								echo '</div>';
							
								// IMAGE
								if ( isset ( $options['sc_lp_image'] ) && !empty ( $options['sc_lp_image'] ) ) {
								
									echo '<div class="span7">';
										echo '<div id="screenshot">';
											echo '<div class="image">';
												
												$image = vt_resize( '',$options['sc_lp_image'] , '620','390', true );
												echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" data-full="'.$options['sc_lp_image'].'" />';
												
												echo '<div class="loupe"></div>';	
											echo '</div>';	
										echo '</div>';	
									echo '</div>';	
								}
							
							
							?>

                        	

                        </div><!-- end row -->
                    </div>
                </div>
				
				<div class="zn_header_bottom_style"></div><!-- header bottom style -->

        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	STATIC CONTENT - Default
--------------------------------------------------------------------------------------------------*/
 
	function _static1($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
        
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="zn_slideshow">
                <div class="container">
                	<div class="static-content default-style">
					<?php
						// TITLE
						if ( isset ( $options['ww_slide_title'] ) && !empty ( $options['ww_slide_title'] ) ) {
							echo '<h2 class="centered">'.do_shortcode($options['ww_slide_title']).'</h2>';
						}
						
						// SUBTITLE
						if ( isset ( $options['ww_slide_subtitle'] ) && !empty ( $options['ww_slide_subtitle'] ) ) {
							echo '<h3 class="centered">'.do_shortcode($options['ww_slide_subtitle']).'</h3>';
						}
						
						// BUTTON
						if ( $options['ww_slide_m_button'] || $options['ww_slide_l_text'] ) {
							echo '<div class="info_pop animated fadeBoxIn" data-arrow="top">';
								
								if ( $options['ww_slide_l_text'] && isset ( $options['ww_slide_link']['url'] ) && !empty ( $options['ww_slide_link']['url'] ) ) {
									echo '<a class="buyit" href="'.$options['ww_slide_link']['url'].'" target="'.$options['ww_slide_link']['target'].'">'.$options['ww_slide_l_text'].'</a>';
								}
							
								// BUTTON LEFT TEXT
								if ( isset ( $options['ww_slide_m_button'] ) && !empty ( $options['ww_slide_m_button'] ) ) {
									echo '<h5 class="text">'.$options['ww_slide_m_button'].'</h5>';
								}
								
								echo '<div class="clear"></div>';
							echo '</div>';
						}
						
					?>
					
                        
                    </div>
                </div>
			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	CIRCULAR CONTENT STYLE 1
--------------------------------------------------------------------------------------------------*/
 
	function _circ1($options)
	{
	
		global $data;

		$responsive_check = 'zn_responsive';
		if ( $data['zn_responsive'] == 'no' ) {
			$responsive_check = 'zn_not_responsive';
		}
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow"  class="<?php echo $style; ?>">
		
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="container zn_slideshow <?php echo $responsive_check; ?>">

				<div id="ca-container" class="ca-container">
                    <div class="ca-wrapper">
                    <?php
						
						if ( isset ( $options['single_circ1'] ) && is_array ( $options['single_circ1'] ) ) {
								
							$i = 1;
							$thumbs = '';
							
							foreach ( $options['single_circ1'] as $slide ) {					

								echo '<div class="ca-item ca-item-'.$i.'">';
							
									echo '<div class="ca-item-main">';
									
										echo '<div class="background"></div><!-- background color -->';
									
										if ( isset ( $slide['ww_slide_image'] ) && !empty ( $slide['ww_slide_image'] ) ) {
										
											echo '<div class="ca-icon">';
												$image = vt_resize( '',$slide['ww_slide_image'] , '336','200', true );
												echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" />';
											echo '</div>';	
										}
									
										// TITle
										if ( isset ( $slide['ww_slide_title'] ) && !empty ( $slide['ww_slide_title'] ) ) {
											echo '<h3>'. $slide['ww_slide_title'].'</h3>';
										}
									
										// DESC
										if ( isset ( $slide['ww_slide_desc'] ) && !empty ( $slide['ww_slide_desc'] ) ) {
											echo '<h4>'. $slide['ww_slide_desc'].'</h4>';
										}
										
										// DESC
										if ( isset ( $slide['ww_slide_read_text'] ) && !empty ( $slide['ww_slide_read_text'] ) ) {
											echo '<a href="#" class="ca-more">'.$slide['ww_slide_read_text'].' <span class="icon-chevron-right icon-white"></span></a>';
										}								
										// Bottom Title
										if ( isset ( $slide['ww_slide_bottom_title'] ) && !empty ( $slide['ww_slide_bottom_title'] ) ) {
											echo '<span class="ca-starting">'. $slide['ww_slide_bottom_title'].'</span>';
										}
									
									echo '</div>';
									
									echo '<div class="ca-content-wrapper">';
										echo '<div class="ca-content">';
									
											// Content Title
											if ( isset ( $slide['ww_slide_content_title'] ) && !empty ( $slide['ww_slide_content_title'] ) ) {
												echo '<h6>'.$slide['ww_slide_content_title'].'</h6>';
											}
											
											echo '<a href="#" class="ca-close"><span class="icon-remove"></span></a>';
									
											// Content description
											if ( isset ( $slide['ww_slide_desc_full'] ) && !empty ( $slide['ww_slide_desc_full'] ) ) {
												echo '<div class="ca-content-text">';
												
													echo $slide['ww_slide_desc_full'];
												
												echo '</div>';
											}
									
											// Link
											if ( isset ( $slide['ww_slide_read_text_content'] ) && !empty ( $slide['ww_slide_read_text_content'] ) && isset ( $slide['ww_slide_link']['url'] ) && !empty ( $slide['ww_slide_link']['url'] ) ) {
												echo '<a href="'.$slide['ww_slide_link']['url'].'" target="'.$slide['ww_slide_link']['target'].'">'.$slide['ww_slide_read_text_content'].'</a>';
											}
									
										echo '</div>';
									echo '</div>';
									

							
								echo '</div><!-- end ca-item -->';
							
								$i++;
							
							}
						}
					?>

                    </div><!-- end ca-wrapper -->
                </div><!-- end circular content carousel -->

			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
	
/*--------------------------------------------------------------------------------------------------
	Wow Slider
--------------------------------------------------------------------------------------------------*/
 
	function _wowslider($options)
	{
	
		if ( isset ( $options['ww_header_style'] ) && !empty ( $options['ww_header_style'] ) ) { 
			$style = 'uh_'.$options['ww_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
			
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
			
			<div class="container zn_slideshow">

                <div id="wowslider-container" class="drop-shadow <?php echo $options['ww_shadow'];?>"" data-transition="<?php echo $options['ww_transition'];?>">
                    <div class="ws_images">
                        <ul>
						<?php
						
							if ( isset ( $options['single_wow'] ) && is_array ( $options['single_wow'] ) ) {
								
								$i = 0;
								$thumbs = '';
								
								foreach ( $options['single_wow'] as $slide ) {
								
									$link_start = '';
									$link_end = '';
									$title = '';
									
									if ( isset ( $slide['ww_slide_link']['url'] ) && !empty ( $slide['ww_slide_link']['url'] ) )
									{
										// Set defaults 
										$link_start = '<a class="link" href="'.$slide['ww_slide_link']['url'].'" target="'.$slide['ww_slide_link']['target'].'">';
										$link_end = '</a>';
									
									}
									
									if ( isset ( $slide['ww_slide_title'] ) && !empty ( $slide['ww_slide_title'] ) ) {
										$title = $slide['ww_slide_title'];
									}
									
									echo '<li>';
									echo $link_start;
									
										if ( isset ( $slide['ww_slide_image'] ) && !empty ( $slide['ww_slide_image'] ) ) {
										
											$image = vt_resize( '',$slide['ww_slide_image'] , '1170','', true );
											echo '<img id="wows1_'.$i.'" title="'.$title.'" src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" />';
												
											$image_thumb = vt_resize( '',$slide['ww_slide_image'] , '150','60', true );
											$thumbs .= '<a href="#" title="slide'.$i.'"><img src="'.$image_thumb['url'].'" />'.$i.'</a>';
												
										}
									
									echo $link_end;
									echo '</li>';
									$i++;
								
								}
								
							}
						?>
						
                        </ul>
                    </div><!-- end ws_images -->
                
                    <div class="ws_bullets">
                        <div>
							<?php echo $thumbs; ?>
                        </div>
                    </div><!-- end ws-bullets -->

                </div><!-- end #wow slider -->
                
			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Nivo Slider
--------------------------------------------------------------------------------------------------*/
 
	function _nivoslider($options)
	{
	
		if ( isset ( $options['nv_header_style'] ) && !empty ( $options['nv_header_style'] ) ) { 
			$style = 'uh_'.$options['nv_header_style'];
		} else { 
			$style = '';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
		
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
		
			<div class="container zn_slideshow">

                <div id="nivoslider" class="nivoContainer ">
                    <div class="nivoSlider drop-shadow <?php echo $options['nv_shadow'];?>" data-transition="<?php echo $options['nv_transition'];?>">
					<?php
					
						if ( isset ( $options['single_nivo'] ) && is_array ( $options['single_nivo'] ) ) {
							
							foreach ( $options['single_nivo'] as $slide ) {
								
								$link_start = '';
								$link_end = '';
								$title = '';
								
								if ( isset ( $slide['nv_slide_link']['url'] ) && !empty ( $slide['nv_slide_link']['url'] ) )
								{
									// Set defaults 
									$link_start = '<a class="link" href="'.$slide['nv_slide_link']['url'].'" target="'.$slide['nv_slide_link']['target'].'">';
									$link_end = '</a>';
								
								}
								
								if ( isset ( $slide['nv_slide_title'] ) && !empty ( $slide['nv_slide_title'] ) ) {
									$title = $slide['nv_slide_title'];
								}
								
								echo $link_start;
								
									if ( isset ( $slide['nv_slide_image'] ) && !empty ( $slide['nv_slide_image'] ) ) {
									
										$image = vt_resize( '',$slide['nv_slide_image'] , '1170','', true );
										echo '<img title="'.$title.'" src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" />';
																				
									}
								
								echo $link_end;
								
								
							}
							
						}
					
					?>
					
                        
                    </div>
                </div><!-- end #nivoslider -->
                
			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	Flex Slider
--------------------------------------------------------------------------------------------------*/
 
	function _flexslider($options)
	{
	
		if ( isset ( $options['fs_header_style'] ) && !empty ( $options['fs_header_style'] ) ) { 
			$style = 'uh_'.$options['fs_header_style'];
		} else { 
			$style = '';
		}
		
		if ( $options['fs_show_thumbs'] ) { 
			$thumbs = 'zn_has_thumbs';
		} 
		else {
			$thumbs = '';
		}
	
	?>
        <div id="slideshow" class="notPadded <?php echo $style; ?>">
			<div class="bgback"></div>
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
			<div class="container zn_slideshow">

                    <div data-transition="<?php echo $options['fs_transition'];?>" class="flexslider showOnMouseover zn_normal_flex <?php echo $thumbs; ?> drop-shadow <?php echo $options['fs_shadow'];?>">
                        <ul class="slides">
						<?php
							if ( isset ( $options['single_flex'] ) && is_array ( $options['single_flex'] ) ) {
								
								foreach ( $options['single_flex'] as $slide ) {
								
									$thumb = '';
									$link_start = '';
									$link_end = '';
								
									if ( isset ( $slide['fs_slide_link']['url'] ) && !empty ( $slide['fs_slide_link']['url'] ) )
									{
										// Set defaults 
										$link_start = '<a class="slide" href="'.$slide['fs_slide_link']['url'].'" target="'.$slide['fs_slide_link']['target'].'">';
										$link_end = '</a>';
									
									}
										
									if ( isset ( $slide['fs_slide_image'] ) && !empty ( $slide['fs_slide_image'] ) ) {
									
										$image = vt_resize( '',$slide['fs_slide_image'] , '1170','', true );
										$full_image = '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" />';
										
										
										if ( $options['fs_show_thumbs'] ) { 
											$small_thumb = vt_resize( '',$slide['fs_slide_image'] , '150','60', true );
											$thumb = 'data-thumb="'.$small_thumb['url'].'"';
										} 
										
									}
										


								
									echo '<li '.$thumb.'>';
										echo $link_start;
										

											echo $full_image;
								
										echo $link_end;
										
										if ( isset ( $slide['fs_slide_title'] ) && !empty ( $slide['fs_slide_title'] ) ) {
											echo '<h2 class="flex-caption">'.$slide['fs_slide_title'].'</h2>';
										}
										
									echo '</li>';
								}
								
							}
						?>

                        </ul>
                    </div><!-- end #flexslider -->

			</div>
			
			<div class="zn_header_bottom_style"></div><!-- header bottom style -->
			
        </div><!-- end slideshow -->
	<?php
	}
	
/*--------------------------------------------------------------------------------------------------
	iCarousel
--------------------------------------------------------------------------------------------------*/

	function _icarousel($options)
	{
	

		if ( isset ( $options['ic_header_style'] ) && !empty ( $options['ic_header_style'] ) && $options['ic_header_style'] != 'zn_def_header_style' ) { 
			$style = 'uh_'.$options['ic_header_style'];
		} else { 
			$style = 'zn_def_header_style';
		}
	
	?>
        <div id="slideshow" class="<?php echo $style; ?>">
			<div class="bgback"></div>
			<div class="carousel-container">
				<div id="icarousel">
				<?php
					if ( isset ( $options['single_icarousel'] ) && is_array ( $options['single_icarousel'] ) ) {
						

						foreach ( $options['single_icarousel'] as $slide ) {
							
							$link_start = '<a href="#" class="slide">';
							$link_end = '</a>';
							$slide_class = 'class="slide"';
						
							if ( isset ( $slide['ic_slide_link']['url'] ) && !empty ( $slide['ic_slide_link']['url'] ) )
							{
								// Set defaults 
								$link_start = '<a class="slide" href="'.$slide['ic_slide_link']['url'].'" target="'.$slide['ic_slide_link']['target'].'">';
								$link_end = '</a>';
								$slide_class = '';
							}
							
							echo $link_start;
							
								if ( isset ( $slide['ic_slide_image'] ) && !empty ( $slide['ic_slide_image'] ) ) {
								
									$image = vt_resize( '',$slide['ic_slide_image'] , '480','360', true );
									echo '<img '.$slide_class.' src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" />';
									
								}
								
								if ( isset ( $slide['ic_slide_title'] ) && !empty ( $slide['ic_slide_title'] ) ) {
									echo '<h5><span>'.$slide['ic_slide_title'].'</span></h5>';
								}
								
							echo $link_end;
							
							
						}
						
					}
				?>
				
				</div>
			</div>
			
        </div><!-- end slideshow -->
	<?php
	}
	
	
/*--------------------------------------------------------------------------------------------------
	Laptop Slider
--------------------------------------------------------------------------------------------------*/

	function _lslider($options)
	{	
	
		if ( isset ( $options['ls_header_style'] ) && !empty ( $options['ls_header_style'] ) && $options['ls_header_style'] != 'zn_def_header_style' ) { 
			$style = 'uh_'.$options['ls_header_style'];
		} else { 
			$style = 'zn_def_header_style';
		}
		
		if ( isset ( $options['ls_slider_desc'] ) && !empty ( $options['ls_slider_desc'] ) ) { 
			$slider_desc = '<h3 class="centered">'.do_shortcode($options['ls_slider_desc']).'</h3>';
		} else { 
			$slider_desc = '';
		}
	
	?>
        <div id="slideshow" class="gradient noGlare <?php echo $style; ?>">
			<div class="bgback"></div>
			<div class="laptop-slider-wrapper">
				<div class="container">
				
					<?php echo $slider_desc;?>
					
					<div class="laptop-mask">
						<div class="flexslider zn_laptop_slider">
							<ul class="slides">
							
								<?php
									if ( isset ( $options['single_lslides'] ) && is_array ( $options['single_lslides'] ) ) {	
										foreach ( $options['single_lslides'] as $slide ) {
										
											$link_start = '';
											$link_end = '';
											$caption = '';
											$alt = '';

											
											if ( isset ( $slide['ls_slide_link']['url'] ) && !empty ( $slide['ls_slide_link']['url'] ) )
											{
											
												// Link
												$link_start = '<a class="link" href="'.$slide['ls_slide_link']['url'].'" target="'.$slide['ls_slide_link']['target'].'">';
												$link_end = '</a>';
												
											}
											
											if ( isset ( $slide['ls_slide_title'] ) && !empty ( $slide['ls_slide_title'] ) )
											{
											
												// Caption
												$caption = '<h2 class="flex-caption">'.$slide['ls_slide_title'].'</h2>';
												$alt = $slide['ls_slide_title'];
												
											}
											
											echo '<li>';
											
												echo $link_start;
												
													if ( isset ( $slide['ls_slide_image'] ) && !empty ( $slide['ls_slide_image'] ) ) {
													
														$image = vt_resize( '',$slide['ls_slide_image'] , '607','380', true );
														echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.$alt.'" />';
														
													}

													echo $caption;
												
												echo $link_end;
											
											echo '</li>';
											
										
										}
										
									}
								?>

							</ul>
                    </div><!-- end #flexslider -->
					</div><!-- laptop mask -->
					
				</div>	
            </div>
        </div><!-- end slideshow -->
	<?php
	}
	
	
/*--------------------------------------------------------------------------------------------------
	Portfolio Slider
--------------------------------------------------------------------------------------------------*/

	function _pslider($options)
	{	
		
		$title = '';
		$link = '';
	
	
		if ( isset ( $options['ps_header_style'] ) && !empty ( $options['ps_header_style'] ) && $options['ps_header_style'] != 'zn_def_header_style' ) { 
			$style = 'uh_'.$options['ps_header_style'];
		} else { 
			$style = 'zn_def_header_style';
		}
		
		if ( isset ( $options['ps_slider_desc'] ) && !empty ( $options['ps_slider_desc'] ) ) { 
			$slider_desc = '<h3 class="centered">'.do_shortcode($options['ps_slider_desc']).'</h3>';
		} else { 
			$slider_desc = '';
		}
	
		if ($options['ps_sliding_direction'] == 'Horizontal'){
			$hclass='horizontal-mode';
			$container_start = '';
			$container_end = '';
		}
		else{
			$hclass='';
			$container_start = '<div class="container">';
			$container_end = '</div>';
		}


	?>
	
        <div id="slideshow" class="gradient <?php echo $style; ?>">
			<div class="bgback"></div>
			
			<div data-images="<?php echo IMAGES_URL; ?>/" id="sparkles"></div>
			
			<div class="portfolio-slider-frames <?php echo $hclass;?> zn_slideshow">
                <?php echo $container_start;?>
				
					<?php echo $slider_desc;?>
                   
                    <div id="carousel-wrapper">
                        <div id="carousel" class="animating_frames">
						<?php
							if ( isset ( $options['single_pslides'] ) && is_array ( $options['single_pslides'] ) ) {	
								foreach ( $options['single_pslides'] as $slide ) {
								
									echo '<div>';
									
										if ( isset ( $slide['ps_slide_title'] ) && !empty ( $slide['ps_slide_title'] ) ) {
											$title = '<span class="project_title">'.$slide['ps_slide_title'].'</span>';
										}
									
										if ( isset ( $slide['ps_slide_link']['url'] ) && !empty ( $slide['ps_slide_link']['url'] ) )
										{
											$link = '<a class="project_url" href="'.$slide['ps_slide_link']['url'].'" target="'.$slide['ps_slide_link']['target'].'">'.$slide['ps_slide_link']['url'].'</a>';
										}
									
										// Front Image
										if ( isset ( $slide['ps_slide_image1'] ) && !empty ( $slide['ps_slide_image1'] ) ) {
											
											echo '<div class="img-front">';
											
												echo $title;
												echo $link;
												$image = vt_resize( '',$slide['ps_slide_image1'] , '460','320', true );
												echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="" />';
												
											echo '</div>';
										}
								
										// Right Image
										if ( isset ( $slide['ps_slide_image3'] ) && !empty ( $slide['ps_slide_image3'] ) ) {

												$image = vt_resize( '',$slide['ps_slide_image3'] , '460','320', true );
												echo '<img class="img-back" src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="" />';

										}
									
										// Left Image
										if ( isset ( $slide['ps_slide_image2'] ) && !empty ( $slide['ps_slide_image2'] ) ) {

												$image = vt_resize( '',$slide['ps_slide_image2'] , '460','320', true );
												echo '<img class="img-back2" src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="" />';

										}
									echo '</div>';
								
								}
							}
						?>
						</div>	
                        <a id="prev" href="#"><span class="icon-chevron-left icon-white"></span></a>
                        <a id="next" href="#"><span class="icon-chevron-right icon-white"></span></a>
                    </div><!-- end Carousel wrapper -->
                    
                <?php echo $container_end;?>
            </div>
            <div class="zn_header_bottom_style"></div>
        </div><!-- end slideshow -->
	
	<?php
	}
	
	

















	
	
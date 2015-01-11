<?php get_header(); 

	// GET GLOBALS
	global $content_and_sidebar;

	// GET THE METAFIELDS
	$meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
	$meta_fields = maybe_unserialize( $meta_fields );

/*--------------------------------------------------------------------------------------------------
	ACTION BOX AREA
--------------------------------------------------------------------------------------------------*/
	zn_get_template_from_area ('action_box_area',$post->ID,$meta_fields);



?>
		<section id="content">
			<?php if ( $content_and_sidebar ) { ?>
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
								/*
echo '<pre>';print_r($post_meta_fields['port_media']);echo '</pre>';*/
									if ( !empty ( $post_meta_fields['port_media'] ) && is_array( $post_meta_fields['port_media'] ) ) {
										
										$all_media = count( $post_meta_fields['port_media'] );
										if ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) && !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
											echo '<a href="'.$post_meta_fields['port_media']['0']['port_media_video_comb'].'" rel="prettyPhoto" class="hoverBorder">';
													$size = zn_get_size( 'span5',$has_sidebar );
													$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],'' , true );
													echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
													echo '</a>';
													unset( $post_meta_fields['port_media']['0'] );
											}										
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_image_comb'] ) ) {
												echo '<a href="'.$post_meta_fields['port_media']['0']['port_media_image_comb'].'" rel="prettyPhoto" class="hoverBorder">';
													$size = zn_get_size( 'span5',$has_sidebar );
													$image = vt_resize( '', $post_meta_fields['port_media']['0']['port_media_image_comb'] , $size['width'],'' , true );
													echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
												echo '</a>';
												unset( $post_meta_fields['port_media']['0'] );										
											}										
											elseif ( !empty ( $post_meta_fields['port_media']['0']['port_media_video_comb'] ) ) {
											$size = zn_get_size( 'span5',$has_sidebar );
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

															$size = zn_get_size( 'four',$has_sidebar );
														$image = vt_resize( '', $media['port_media_image_comb'] , $size['width'],'202' , true );
														echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
													echo '</a>';
												echo '</div>';
												}											elseif ( !empty ( $media['port_media_image_comb'] ) ) {
												echo '<div class="span3">';
													echo '<a href="'.$media['port_media_image_comb'].'" rel="prettyPhoto" class="hoverBorder">';

															$size = zn_get_size( 'four',$has_sidebar );
														$image = vt_resize( '', $media['port_media_image_comb'] , $size['width'],'202' , true );
														echo '<img src="'.$image['url'].'" width="'.$image['width'].'" height="'.$image['height'].'" alt="'.get_the_title().'" />';
													echo '</a>';
												echo '</div>';
												}											elseif ( !empty ( $media['port_media_video_comb'] ) ) {
												echo '<div class="span3">';

													$size = zn_get_size( 'four',$has_sidebar );
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
		<?php } ?>
<?php

/*--------------------------------------------------------------------------------------------------
	START CONTENT AREA 
--------------------------------------------------------------------------------------------------*/
	if ( isset ( $meta_fields['content_main_area'] ) && is_array ( $meta_fields['content_main_area'] ) ) {
		echo '<div class="container">';
			zn_get_template_from_area ('content_main_area',$post->ID,$meta_fields);
		echo '</div>';
	}

/*--------------------------------------------------------------------------------------------------
	START GRAY AREA
--------------------------------------------------------------------------------------------------*/
				
	$cls = '';
	if ( !isset ( $meta_fields['content_bottom_area'] ) || !is_array ( $meta_fields['content_bottom_area'] ) ) {
		$cls = 'noMargin';
	}

	if ( isset ( $meta_fields['content_grey_area'] ) && is_array ( $meta_fields['content_grey_area'] ) ) {
	echo '<div class="gray-area '.$cls.'">';
		echo '<div class="container">';
		
			zn_get_template_from_area ('content_grey_area',$post->ID,$meta_fields);
		
		echo '</div>';
	echo '</div>';
	}
				

		
		
/*--------------------------------------------------------------------------------------------------
	START BOTTOM AREA
--------------------------------------------------------------------------------------------------*/
		

	if ( isset ( $meta_fields['content_bottom_area'] ) && is_array ( $meta_fields['content_bottom_area'] ) ) {
		echo '<div class="container">';
			zn_get_template_from_area ('content_bottom_area',$post->ID,$meta_fields);
		echo '</div>';
	}
?>

		</section><!-- end #content -->
			

<?php get_footer(); ?>
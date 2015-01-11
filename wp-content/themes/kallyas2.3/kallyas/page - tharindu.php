<?php get_header(); ?>

<?php
/**
 * Template Name: Page with No Sidebar
 */
	// GET GLOBALS
	global $content_and_sidebar;

	$meta_fields = get_post_meta($post->ID, 'zn_meta_elements', true);
	$meta_fields = maybe_unserialize( $meta_fields );
/*--------------------------------------------------------------------------------------------------
	ACTION BOX AREA
--------------------------------------------------------------------------------------------------*/

	if ( isset ( $meta_fields['action_box_area'] ) && is_array ( $meta_fields['action_box_area'] ) ) {
		zn_get_template_from_area ('action_box_area',$post->ID,$meta_fields);
	}

/*--------------------------------------------------------------------------------------------------
	CONTENT AREA
--------------------------------------------------------------------------------------------------*/
	
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
	
	echo '<section id="content">';

	if ( $content_and_sidebar ) { 

		while (have_posts()) : the_post();
		
		$content = get_the_content();
		$content = apply_filters('the_content', $content);
		if ( !empty($content) || ( isset ( $meta_fields['page_title_show'] ) && $meta_fields['page_title_show'] == 'yes' ) ) {

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
			}
		endwhile;
	}
	
/*--------------------------------------------------------------------------------------------------
	START CONTENT AREA 
--------------------------------------------------------------------------------------------------*/
	if ( isset ( $meta_fields['content_main_area'] ) && is_array ( $meta_fields['content_main_area'] ) ) {
		echo '<div class="container">';
			zn_get_template_from_area ('content_main_area',$post->ID,$meta_fields);
		echo '</div>';
	}

?>
                                                                

                                                                
                                                                <?php
        
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
				

?>
                                                                
<style>
    #genbg{
        height: 500px;
    }
    
    @media screen and (max-width:900px){
         #genbg{
        height: 300px;
    }
    }
</style>
        
<div style="width:100%;  background: url('http://kohler.scene7.com/is/image/KPS/ddd01935_576'); background-size: cover; left: 0; right: 0;" id="genbg">
     <h1 style="font-size: 45px; text-align: center; color:#fff; margin-top: 30px;"> <br> <br>Portable or Home Generators</h1>
    <h1 style="font-size: 25px; text-align: center;color:#fff">When it comes to backup power, you have two choices.</h1>
  
     <h1 style="font-size: 35px; text-align: center;color:#fff">Here's the scoop:</h1>
     <div style="width:100%; height:auto; margin: 0 auto; max-width: 1170px; margin-top: 100px;">
         <style>
             
             .list{
                 width:45%; 
                 height: 400px;  
                 float: left; 
                 margin-left: 20px;
             }
             
             
             @media screen and (max-width:900px){
        .gens{
            display: none;
        }
        
        .list {
            width: 90%;
            height: auto;
            margin-left: 30px;
            margin-top: 40px;
            float: none;
            }
            
            .list h1,h2,h3{
                text-align: center;
            }
            
            
    }
    
    @media screen and (min-width:901px) and (max-width:1130px){
        #midgen{
            display: none;
        }
    }
         </style>
    <div style="width:300px; height: 300px; background: url('http://kohler.scene7.com/is/image/KPS/HP-GEN101-7-1?$Icons$') no-repeat;  float: left;margin-left: 60px;" class="gens" ></div>
    <div style="width:180px; height: 300px; background: url('http://www.kohlergenerators.com/kpsResi/common/images/stub-images/or-circle.png') no-repeat; float: left;margin-left: 60px;"  class="gens" id="midgen"></div>
    <div  style="width:450px; height: 300px; background: url('http://kohler.scene7.com/is/image/KPS/aab57820_rgb_262?$Icons$') no-repeat; float: left;margin-left: 60px;" class="gens"></div>
    <div style="height: 1px"></div>
<a style="display:block; height:50px; width:40px; background:#fff; float:right; margin-right:-40px; margin-top:100px; box-sizing:border-box; padding:6px;" href="#co3" rel="m_PageScroll2id"><p style="font-size:12px;margin:0 auto;">More</p><i class="icon-chevron-down" style="margin-left:6px;"></i></a>                                                              

     </div>
    

</div>
 
 <div style="width:100%; height:auto; margin: 0 auto; max-width: 1170px; margin-top: 20px;">
     <div style="" class="list">
         <h3>Portable</h3>
         
                  

         <ul style="font-size:17px; list-style-image: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/c.png');">
<li>Designed for job sites, camping, tailgating and other mobile activities</li>
<br>
<li>Powers individual items, like plug-in appliances and lamps with extension cords</li>
<br>
<li>Runs on gasoline or diesel </li>

</ul>

     </div>
     <div class="list">
          <h3>Home</h3>
         <ul style="font-size:17px; list-style-image: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/c.png');">
<li>Ideal for backing up your home</li>
<br>
<li>Automatically starts and restores power in seconds -- whether you're home or away</li>
<br>
<li>Powers your home, including critical hard-wired systems like AC, heat, sump pumps, well pumps, security systems and large appliances </li>
<br>
<li>No refueling -- runs on your home's natural or LP gas</li>
<br>
<li>Delivers high-quality power -- won't harm your electronics <div id="co3"></div></li>
<br>
</ul>
     </div>
     <div class="clear-fix" style="clear:both;line-height: 1px;"></div>
     
</div>  

<style>
    #vid{
        height:650px;
        width:100%;
    }
    
    
    
    @media screen and (min-width:1500px) and (max-width:1900px){
        #vid{
            height:850px;
        }
    }
</style> 

<!--[if IE]>
<style type="text/css">
  #vid{
width:1170px;
display: block;
margin: 0 auto;
}
</style>
<![endif]-->

<!--<div style=" margin-bottom: 40px; background: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/zab34200_788-1170x614.jpg') no-repeat; background-size: cover;   left: 0; right: 0; " id="vid">
 <h1 style="font-size: 45px; text-align: center; color:#fff; margin-top: 30px;"> <br> <br>How it Works</h1>
    <h1 style="font-size: 25px; text-align: center;color:#fff; width:50%; margin: 0 auto;">Good news: when the power goes out, you won’t lift a finger. Home improvement expert Ed Del Grande shows you how.</h1>
  
    
</div>    -->
<iframe width="100%" height="800px" src="//www.youtube.com/embed/XzzFQ03apw0?autoplay=0&showinfo=0&controls=0" frameborder="0" allowfullscreen></iframe>

                                                                <?php
		
/*--------------------------------------------------------------------------------------------------
	START BOTTOM AREA
--------------------------------------------------------------------------------------------------*/
		

	if ( isset ( $meta_fields['content_bottom_area'] ) && is_array ( $meta_fields['content_bottom_area'] ) ) {
		echo '<div class="container">';
			zn_get_template_from_area ('content_bottom_area',$post->ID,$meta_fields);
		echo '</div>';
	}
			
		
		
	echo '</section><!-- end #content -->';
	
	
?>
				
<?php get_footer(); ?>
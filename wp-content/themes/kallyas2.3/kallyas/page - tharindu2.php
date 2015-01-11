<?php get_header(); ?>

<?php
/**
 * Template Name: About Commercial Generators
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
        
<div style="width:100%;  background: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/zaa56945_5961.jpg'); background-size: cover; left: 0; right: 0;" id="genbg">
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
           <a style="display:block; height:50px; width:40px; background:#fff; float:right; margin-right:-40px; margin-top:-140px; box-sizing:border-box; padding:6px;" href="#co3" rel="m_PageScroll2id"><p style="font-size:12px;margin:0 auto;">More</p><i class="icon-chevron-down" style="margin-left:6px;"></i></a>
    <div style="width:300px; height: 300px; background: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/zab22395_rgb_262.png') no-repeat;  float: left;margin-left: 60px;" class="gens" ></div>
    <div style="width:180px; height: 300px; background: url('http://www.kohlergenerators.com/kpsResi/common/images/stub-images/or-circle.png') no-repeat; float: left;margin-left: 60px;"  class="gens" id="midgen"></div>
    <div  style="width:450px; height: 300px; background: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/aab57804_rgb_177.png') no-repeat; float: left;margin-left: 60px;" class="gens"></div>
    <div style="height: 1px"></div>
     </div>
   

</div>
                                                                
<div id="co3"></div>
 <div style="width:100%; height:auto; margin: 0 auto; max-width: 1170px; margin-top: 0;">
     <div style="" class="list">
         <h3>Portable</h3>
         <ul style="font-size:17px; line-height: 27px; list-style-image: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/c.png');">
<li>Designed for powering remote locations, sporting events and concerts</li>
<br>
<li>Manual power transfer once generator is on site (building requires pre-wired generator connection panel)</li>
<br>
<li>Can power critical hard-wired systems like A/C, heat, computers, security systems, refrigerators and freezers with generator connection panel</li>
<br>
<li>Runs on diesel fuel</li>
</ul>
     </div>
     <div class="list">
          <h3>Home</h3>
         <ul style="font-size:17px; list-style-image: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/c.png');">
<li>Ideal for backing up your business</li>
<br>
<li>Automatically restores power in seconds - whether you're open or closed</li>
<br>
<li>Powers your business, including critical hard-wired systems like A/C, heat, computers, security systems, refrigerators, freezers, and more</li>
<br>
<li>No refueling - runs on your small business's natural or LP gas</li>

<br>
</ul>
     </div>
     <div class="clear-fix" style="clear:both;line-height: 1px;"></div>
<a style="display:block; height:50px; width:40px; background:rgba(69, 54, 54, 0.32); float:right; margin-right:-40px; margin-top:-140px; box-sizing:border-box; padding:6px;" href="#co4" rel="m_PageScroll2id"><p style="font-size:12px;margin:0 auto;">More</p><i class="icon-chevron-down" style="margin-left:6px;"></i></a> 
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
    
    .list2{
            width:50%;
            float: right;
            margin-bottom: 40px;
            margin-left: 30px;
            height: auto;
        }
    
    @media screen and (max-width:900px){
        .list2{
            width:90%;
            float: none;
            margin-bottom: 40px;
            margin-left: 30px;
            height: auto;
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

<div id="co4" style="width:100%; height: 600px; margin: 0 auto; background: url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/aab57361_rgb_574.jpg'); background-size:cover;">
<br>
<div  class="list2">
<br> <br> <br>
    <h3 style="font-size: 35px; text-align: left; color:#fff">Who needs one?</h3>
    <h1 style="font-size: 25px; text-align: left;;color:#fff">Today backup power is important for every small business owner.</h1>
<p style="font-size:20px; line-height:30px; color:#fff;">These are just a few of the benefits:</p>
<br>
<ul style="padding: 0 20px;
margin: 0px 0px 10px 25px;
color: #fff;
line-height: 30px; font-size:16px; list-style-image:url('http://geniusamigos.com/mystuff/poweraid/wp-content/uploads/2014/10/white-check-bullet.png');">
	<li>
		Protects your revenue stream and your inventory during an outage</li><br>
	<li>
		Keeps the lights, AC and heat on</li><br>
	<li>
		Powers your communications, computers and security system</li><br>
	<li>
		Keeps your customers coming back and encourages new customers to count on you</li><br>
</ul>


  </div>
</div>   <br> <br> <br> 
<a style="display:block; height:50px; width:40px; background:rgba(69, 54, 54, 0.32); float:right; margin-right:-40px; margin-top:-140px; box-sizing:border-box; padding:6px;" href="#co5" rel="m_PageScroll2id"><p style="font-size:12px;margin:0 auto;">More</p><i class="icon-chevron-down" style="margin-left:6px;"></i></a>


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
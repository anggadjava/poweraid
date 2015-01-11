<?php

global $data;

?>	
<?php
	if ( empty( $data['footer_show'] ) || (!empty( $data['footer_show'] ) && $data['footer_show'] == 'yes') ) { ?>
	<footer id="footer">

		<div class="container">

			<?php	
				if ( !empty ( $data['footer_row1_widget_positions'] ) ) {
				
					if ( (!empty ( $data['footer_row1_show'] ) && $data['footer_row1_show'] == 'yes' ) || empty ( $data['footer_row1_show'] ) ) {

						echo '<div style="margin-bottom:0px;" class="row">';
					
						$number_of_columns = 	key( json_decode ( stripslashes ( $data['footer_row1_widget_positions'] ) ) );
						$columns_array = 		json_decode ( stripslashes ( $data['footer_row1_widget_positions'] ),true );
					
						for ($i = 1; $i <= $number_of_columns; $i++) {
							echo '<div class="span'.$columns_array[$number_of_columns][0][$i-1].'">';
								if ( !dynamic_sidebar('Footer row 1 - widget '.$i.'') ) : endif; 
							echo '</div>';
						}
						
						echo '</div><!-- end row -->';

					}
					
				}

				
			?>	
			
			
			</div><!-- end row -->
                       
		</div>
                
                





                
	</footer>
	<?php } ?>
    </div><!-- end page_wrapper -->
	
    <a href="#" id="totop"><?php echo __('TOP', THEMENAME ); ?></a> <?php // Translate ?>



<?php wp_footer(); ?>
</body>
</html>


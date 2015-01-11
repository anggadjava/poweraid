<?php
/**
 * Navigation Menu widget class
 *
 * @since 3.0.0
 */
 class ZN_Twitter_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'description' => __('Use this widget to add a twitter widget to your site.',THEMENAME) );
		parent::__construct( 'zn_twitter', __('['.THEMENAME.'] Twitter Widget',THEMENAME), $widget_ops );
	}

	function widget($args, $instance) {

		$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];

		echo "<script type=\"text/javascript\">
				jQuery(document).ready(function(e) {
					  getTwitters('twitterFeed', { 
						  id: '".$instance['twitter_username']."', //ADD IN YOUR OWN TWITTER ID HERE
						  count: 1, 
						  enableLinks: true, 
						  ignoreReplies: true, 
						  clearContents: true,
						  template: '<a href=\"http://twitter.com/%user_screen_name%/statuses/%id_str%/\" class=\"twTime\"><span>%time%</span></a> \"%text%\"',
						  timeout: 10,
						  onTimeout: function () {
							  this.innerHTML = '". __('There seems to be a problem with loading the tweets. Please refresh or try again later.',THEMENAME)."';
						  }
					  });
				});</script>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"http://platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>
			";
		
		
		echo	'<div class="twitter-feed">';
		echo	'<div class="tweets" id="twitterFeed"><small>Please wait whilst our latest tweets load</small></div>';
		echo	'<a href="https://twitter.com/'.$instance['twitter_username'].'" class="twitter-follow-button" data-show-count="false">Follow @'.$instance['twitter_username'].'</a>';
		echo	'</div><!-- end twitter-feed -->';
		
		
		echo $args['after_widget'];
		
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['twitter_username'] =  stripslashes($new_instance['twitter_username']);
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$twitter_username = isset( $instance['twitter_username'] ) ? $instance['twitter_username'] : '';

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:',THEMENAME) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('twitter_username'); ?>"><?php _e('Twitter Username:',THEMENAME) ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('twitter_username'); ?>" name="<?php echo $this->get_field_name('twitter_username'); ?>" value="<?php echo $twitter_username; ?>" />
		</p>
		<?php
	}
}


add_action( 'widgets_init', create_function( '', 'register_widget( "ZN_Twitter_Widget" );' ) );

?>
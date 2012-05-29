<?php
/*
Plugin Name: Fresh Promo
Plugin URI: http://freshplugs.com/promo
Description: WordPress 3 widget for adding a single, linked featured post image with optional call to action button.
Version: 0.9.0
Author: Fresh Plugs
Author URI: http://freshplugs.com

Fresh Promo is released under the Creative Commons 0 license.
http://creativecommons.org/publicdomain/zero/1.0/
*/

class Fresh_Promo extends WP_Widget {

	function __construct() {
	
		parent::__construct( 'fresh_promo', __('Fresh Promo'), array( 'description' => 'A linked post or page featured image with optional call to action.' ) );
	
	}
	
	function form($instance) {
	
		$defaults = array( 'pid' => '', 'size' => 'full', 'action' => '', 'anchor' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p><label for="<?php echo $this->get_field_id('pid'); ?>">Post or Page ID:</label>
		<input type="text" id="<?php echo $this->get_field_id('pid'); ?>" name="<?php echo $this->get_field_name('pid'); ?>" placeholder="Required" size="4" value="<?php echo $instance['pid']; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('size'); ?>">Image Size:</label>
		<select id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
			<!-- TODO: Get image size options and validate size exists. -->
			<option <?php if ( $instance['size'] == 'thumbnail' ) echo 'selected'; ?> value="thumbnail">Thumbnail</option>
			<option <?php if ( $instance['size'] == 'medium' ) echo 'selected'; ?> value="medium">Medium</option>
			<option <?php if ( $instance['size'] == 'large' ) echo 'selected'; ?> value="large">Large</option>
			<option <?php if ( $instance['size'] == 'full' ) echo 'selected'; ?> value="full">Full</option>
		</select></p>
		
		<p><label for="<?php echo $this->get_field_id('action'); ?>">Call to Action:</label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('action'); ?>" name="<?php echo $this->get_field_name('action'); ?>" placeholder="Optional" value="<?php echo $instance['action']; ?>" /></p>
		
		<p><label for="<?php echo $this->get_field_id('anchor'); ?>">Custom Link:</label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('anchor'); ?>" name="<?php echo $this->get_field_name('anchor'); ?>" placeholder="<?php bloginfo('url'); ?>" value="<?php echo $instance['anchor']; ?>" /></p>
		
<?php }
	
	function update($new, $old) {
	
		// Merge with existing data
		$instance = wp_parse_args( $new, $old );
		return $instance;
	
	}
	
	function widget($args, $instance) {
	
		// Get defaults from functions.php (e.g. $before_widget)
		extract( $args );
		
		// Create promo object + set variable for post thumbnail
		$promo = get_post( $instance['pid'] );
		$anchor = ( ! empty( $instance['anchor'] ) ) ? get_bloginfo('url').$instance['anchor'] : get_permalink( $instance['pid'] );
		$image = get_the_post_thumbnail( $promo->ID, $instance['size'] );
		
		// Output actual widget
		echo $before_widget; ?><a href="<?php echo $anchor; ?>"><?php if ( ! empty( $image ) ) echo $image; else echo '<img alt="Featured image is not set." src="#" />';
if ( ! empty( $instance['action'] ) ) { ?><p class="action"><?php echo $instance['action']; ?></p><?php } ?></a><?php echo $after_widget;
	
	}

} add_action( 'widgets_init', create_function( '', 'register_widget("Fresh_Promo");' ) );

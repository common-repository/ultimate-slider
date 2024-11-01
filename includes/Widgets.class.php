<?php

class ewdusWidgetManager {

	public function __construct() {

		add_action( 'widgets_init', array( $this, 'register_display_slider_widget' ) );
	}

	public function register_display_slider_widget() {

		return register_widget( 'ewdusDisplaySliderWidget' );
	}

}

class ewdusDisplaySliderWidget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		parent::__construct(
			'ewd_us_display_slider', // Base ID
			__( 'Ultimate Slider', 'ultimate-slider' ), // Name
			array( 'description' => __( 'Add a slider to any widgetized area.', 'ultimate-slider' ), ) // Args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];
		echo do_shortcode( "[ultimate-slider posts='". $instance['post_count'] . "' slider_type='". $instance['slider_type'] . "' category='". $instance['category'] . "' carousel_mode='". $instance['carousel_mode'] . "' slide_indicators='". $instance['slide_indicators'] . "']" );
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		$post_count = ! empty( $instance['post_count'] ) ? $instance['post_count'] : __( 'Number of Slides', 'ultimate-slider' );
		$slider_type = ! empty( $instance['slider_type'] ) ? $instance['slider_type'] : __( 'Slider Type', 'ultimate-slider' );
		$category = ! empty( $instance['category'] ) ? $instance['category'] : __( 'Category', 'ultimate-slider' );
		$carousel_mode = ! empty( $instance['carousel_mode'] ) ? $instance['carousel_mode'] : __( 'Carousel Mode', 'ultimate-slider' );
		$slide_indicators = ! empty( $instance['slide_indicators'] ) ? $instance['slide_indicators'] : __( 'Slide Indicators', 'ultimate-slider' );
		?>

		<p>
		<label for="<?php echo $this->get_field_id( 'post_count' ); ?>"><?php _e( 'Number of Slides:', 'ultimate-slider' ); ?></label> 
		<select class="widefat" id="<?php echo $this->get_field_id( 'post_count' ); ?>" name="<?php echo $this->get_field_name( 'post_count' ); ?>" >
			<option value="-1" <?php if ( $post_count == "-1" ) { echo "selected='selected'"; } ?> >All</option>
			<?php for ($i=1; $i<=10; $i++) { ?>
				<option value='<?php echo $i;?>' <?php if ( $post_count == $i ) { echo "selected='selected'"; } ?> ><?php echo $i; ?></option>
			<?php } ?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'slider_type' ); ?>"><?php _e( 'Slider Type:', 'ultimate-slider' ); ?></label> 
		<select class="widefat ewd-us-widget-slider-type" id="<?php echo $this->get_field_id( 'slider_type' ); ?>" name="<?php echo $this->get_field_name( 'slider_type' ); ?>" >
			<option value="" <?php if ( $slider_type == "" ) { echo "selected='selected'"; } ?> >Standard (Uses Slides You Create)</option>
			<option value="woocommerce" <?php if ( $slider_type == "woocommerce" ) { echo "selected='selected'"; } ?> >WooCommerce (Product Slides)</option>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php _e( 'Category:', 'ultimate-slider' ); ?></label> 
		<select class="widefat ewd-us-widget-category" id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>" >
			<option value="" <?php if ( $category == "" ) { echo "selected='selected'"; } ?> >All</option>
		</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'carousel_mode' ); ?>"><?php _e( 'Carousel Mode:', 'ultimate-slider' ); ?></label> 
			<select class="widefat ewd-us-widget-carousel" id="<?php echo $this->get_field_id( 'carousel_mode' ); ?>" name="<?php echo $this->get_field_name( 'carousel_mode' ); ?>" >
				<option value="No" <?php if ( $carousel_mode == "No" ) { echo "selected='selected'"; } ?> >No</option>
				<option value="Yes" <?php if ( $carousel_mode == "Yes" ) { echo "selected='selected'"; } ?> >Yes</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'slide_indicators' ); ?>"><?php _e( 'Slide Indicators:', 'ultimate-slider' ); ?></label> 
			<select class="widefat ewd-us-widget-slide-indicators" id="<?php echo $this->get_field_id( 'slide_indicators' ); ?>" name="<?php echo $this->get_field_name( 'slide_indicators' ); ?>" >
				<option value="Dots" <?php if ( $slide_indicators == "No" ) { echo "selected='selected'"; } ?> >Dots</option>
				<option value="Thumbnails" <?php if ( $slide_indicators == "Yes" ) { echo "selected='selected'"; } ?> >Thumbnails</option>
				<option value="Side Thumbnails" <?php if ( $slide_indicators == "Side Thumbnails" ) { echo "selected='selected'"; } ?> >Side Thumbnails</option>
				<option value="None" <?php if ( $slide_indicators == "None" ) { echo "selected='selected'"; } ?> >None</option>
			</select>
		</p>

		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['post_count'] = ( ! empty( $new_instance['post_count'] ) ) ? strip_tags( $new_instance['post_count'] ) : '';
		$instance['slider_type'] = ( ! empty( $new_instance['slider_type'] ) ) ? strip_tags( $new_instance['slider_type'] ) : '';
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? strip_tags( $new_instance['category'] ) : '';
		$instance['carousel_mode'] = ( ! empty( $new_instance['carousel_mode'] ) ) ? strip_tags( $new_instance['carousel_mode'] ) : '';
		$instance['slide_indicators'] = ( ! empty( $new_instance['slide_indicators'] ) ) ? strip_tags( $new_instance['slide_indicators'] ) : '';

		return $instance;
	}
}





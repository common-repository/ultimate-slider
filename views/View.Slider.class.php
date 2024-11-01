<?php

/**
 * Class to display a slider on the front end.
 *
 * @since 2.0.0
 */
class ewdusViewSlider extends ewdusView {

	// slides to display
	public $slides = array();

	// shortcode attributes
	public $slider_type;
	public $post__in_string;
	public $posts;
	public $category;
	public $carousel;
	public $slide_indicators;
	public $timer_bar;

	/**
	 * Define the the slides for this slider
	 *
	 * @since 2.0.0
	 */
	public function get_slides() { 

		if ( $this->slider_type == 'woocommerce' ) { 

			$args = array(
				'posts_per_page' => $posts,
				'post_type' => 'product',
				'product_cat' => $category,
				'orderby' => 'rand',
			);

			$slider_query = new WP_Query( $args );

			while ( $slider_query->have_posts() ) : $slider_query->the_post();
				
				$post = get_post();

				$this->slides[] = new ewdusViewSlideWooCommerce( array( 'post' => $post ) );

			endwhile;

			wp_reset_query();
		}

		elseif ( $this->slider_type == 'upcp' ) { 

			$args = array(
				'posts_per_page' => $this->posts,
				'post_type' => 'upcp_product',
				'product_cat' => $this->category,
				'orderby' => 'rand'
			);

			$slider_query = new WP_Query( $args );

			while ( $slider_query->have_posts() ) : $slider_query->the_post();
				
				$post = get_post();

				$this->slides[] = new ewdusViewSlideUPCP( array( 'post' => $post ) );

			endwhile;

			wp_reset_query();
		}

		elseif ( $this->slider_type == 'urp' ) {

			$post__in = explode( ',', $this->post__in_string );

			$args = array(
				'posts_per_page' => $this->posts,
				'post_type' => 'urp_review',
				'post__in' => $post__in
			);

			$slider_query = new WP_Query( $args );

			while ( $slider_query->have_posts() ) : $slider_query->the_post();
				
				$post = get_post();

				$this->slides[] = new ewdusViewSlideURP( array( 'post' => $post ) );

			endwhile;

			wp_reset_query();

		}

		elseif ( $this->post__in_string != '' ) {

			$post__in = explode( ',', $this->post__in_string );

			$args = array(
				'posts' => $this->posts,
				'post_type' => 'attachment',
				'post_status' => array( 'publish', 'inherit' ),
				'post__in' => $post__in
			);

			$slider_query = new WP_Query( $args );

			while ( $slider_query->have_posts() ) : $slider_query->the_post();
				
				$post = get_post();

				$this->slides[] = new ewdusViewSlide( array( 'post' => $post ) );

			endwhile;

			wp_reset_query();

		}

		else {
			
			$args = array(
				'posts_per_page' => $this->posts,
				'post_type' => EWD_US_SLIDER_POST_TYPE,
				'ultimate_slider_categories' => $this->category,
				'meta_key' => 'EWD_US_Slide_Order',
				'orderby' => 'meta_value_num',
				'order' => 'ASC',
			);

			$slider_query = new WP_Query( $args );

			while ( $slider_query->have_posts() ) : $slider_query->the_post();
				
				$post = get_post();

				$content_type = get_post_meta( $post->ID, "EWD_US_Content_Type", true );

				if ( $content_type == 'upcp_product' ) { 

					$this->slides[] = new ewdusViewSlideUPCP( array( 'post' => $post ) ); 
				}
				elseif ( $content_type == 'woocommerce_product' ) { 

					$this->slides[] = new ewdusViewSlideWooCommerce( array( 'post' => $post ) ); 
				}
				elseif ( $content_type == 'urp_review' ) { 

					$this->slides[] = new ewdusViewSlideURP( array( 'post' => $post ) ); 
				}
				else { 

					$this->slides[] = new ewdusViewSlide( array( 'post' => $post ) ); 
				}

			endwhile;

			wp_reset_query();

		}

		$this->slides = apply_filters( 'ewd_us_slides', $this->slides );

	}

	/**
	 * Render the view and enqueue required stylesheets
	 * @since 2.0.0
	 */
	public function render() {
		global $ewd_us_controller;

		$this->get_slides();
		if ( ! count( $this->slides ) ) {
			return;
		}

		// Set attribute-alterable options
		$this->set_slider_options();

		// Add any dependent stylesheets or javascript
		$this->enqueue_assets();

		// Add css classes to the slider
		$this->classes = $this->slider_classes();

		ob_start();
		$this->add_custom_styling();
		$template = $this->find_template( 'slider' );
		if ( $template ) {
			include( $template );
		}
		$output = ob_get_clean();

		return apply_filters( 'ewd_us_slider_output', $output, $this );
	}

	/**
	 * Print the slides in the slider
	 *
	 * @since 2.0.0
	 */
	public function print_slides() {

		$output = '';

		foreach ( $this->slides as $slide_count => $slide ) {

			$slide->set_slide_count( $slide_count );
			$output .= $slide->render();
		}

		return $output;
	}

	/**
	 * Print the arrows that increment and decrement slides in the slider
	 *
	 * @since 2.0.0
	 */
	public function print_slide_arrows() {

		$template = $this->find_template( 'slide-arrows' );
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the indicators (dots, thumbnails, etc.) for slides in the slider
	 *
	 * @since 2.0.0
	 */
	public function print_slide_indicators() {
		global $ewd_us_controller;

		if ( $ewd_us_controller->settings->get_setting( 'slide-indicators') == 'dots' ) { $template = $this->find_template( 'slide-indicators-dots' ); }
		elseif ( $ewd_us_controller->settings->get_setting( 'slide-indicators') == 'thumbnails' ) { $template = $this->find_template( 'slide-indicators-thumbnails' ); }
		elseif ( $ewd_us_controller->settings->get_setting( 'slide-indicators') == 'sidethumbnails' ) { $template = $this->find_template( 'slide-indicators-sidethumbnails' ); }
		else { $template = false; }
		
		if ( $template ) {
			include( $template );
		}
	}

	/**
	 * Print the selected arrow letter (or default)
	 *
	 * @since 2.0.0
	 */
	public function print_selected_arrow( $direction = 'left' ) {
		global $ewd_us_controller;

		return $direction == 'left' ? $ewd_us_controller->settings->get_setting( 'arrow' ) : chr( ord( $ewd_us_controller->settings->get_setting( 'arrow' ) ) + 1 );
	}

	/**
	 * Print the class for the selected arrow background shape
	 *
	 * @since 2.0.0
	 */
	public function print_arrow_shape_class() {
		global $ewd_us_controller;

		return 'ewd-us-arrow-background-shape-' . $ewd_us_controller->settings->get_setting( 'arrow-background-shape' );
	}

	/**
	 * Add in default options if not overwritten by shortcode attributes
	 *
	 * @since 2.0.0
	 */
	public function set_slider_options() {
		global $ewd_us_controller;
		
		$this->timer_bar = $this->timer_bar == 'No' ? false : $ewd_us_controller->settings->get_setting( 'timer-bar' );
		$this->carousel = $this->carousel == 'Yes' ? true : $ewd_us_controller->settings->get_setting( 'carousel' );
		$this->slide_indicators = $this->slide_indicators ? strtolower( $this->slide_indicators ) : $ewd_us_controller->settings->get_setting( 'slide-indicators' );
	}

	/**
	 * Get the initial slider css classes
	 * @since 2.0.0
	 */
	public function slider_classes( $classes = array() ) {
		global $ewd_us_controller;

		$classes = array_merge(
			$classes,
			array(
				'ewd-us-slider',
				'ewd-us-slider-slide-indicators-' . $ewd_us_controller->settings->get_setting( 'slide-indicators' ),
			)
		);

		foreach ( $ewd_us_controller->settings->get_setting( 'hide-from-slider' ) as $slider_item ) { $classes[] = 'ewd-us-slider-hide-' . $slider_item; }
		foreach ( $ewd_us_controller->settings->get_setting( 'hide-on-mobile' ) as $slider_item ) { $classes[] = 'ewd-us-slider-mobile-hide-' . $slider_item; }

		if ( $this->carousel ) { $classes[] = 'ewd-us-carousel'; }

		return apply_filters( 'ewd_us_slider_classes', $classes, $this );
	}

	/**
	 * Enqueue the necessary CSS and JS files
	 * @since 2.0.0
	 */
	public function enqueue_assets() {
		global $ewd_us_controller;

		wp_enqueue_script( 'jquery-ui-core' );
	    wp_enqueue_script( 'jquery-effects-slide' );
	    wp_enqueue_script( 'ewd-us-js' );
	    wp_enqueue_script( 'iframe-clicks' );
	
	    $aspect_fraction = ewd_us_get_aspect_fraction( $ewd_us_controller->settings->get_setting( 'aspect-ratio' ) );
	    $mobile_aspect_fraction = ewd_us_get_aspect_fraction( $ewd_us_controller->settings->get_setting( 'mobile-aspect-ratio' ) );
	
	    $slider_data = array( 
	    	'autoplay_slideshow' 		=> $ewd_us_controller->settings->get_setting( 'autoplay-slideshow' ),
	        'autoplay_delay' 			=> substr( $ewd_us_controller->settings->get_setting( 'autoplay-delay' ), 0, strpos( $ewd_us_controller->settings->get_setting( 'autoplay-delay' ), '_' ) ),
	        'autoplay_interval' 		=> substr( $ewd_us_controller->settings->get_setting( 'autoplay-interval' ), 0, strpos( $ewd_us_controller->settings->get_setting( 'autoplay-delay' ), '_' ) ),
	        'slide_transition_effect' 	=> $ewd_us_controller->settings->get_setting( 'slide-transition-effect' ),
	        'transition_time' 			=> substr( $ewd_us_controller->settings->get_setting( 'transition-time' ), 0, strpos( $ewd_us_controller->settings->get_setting( 'autoplay-delay' ), '_' ) ),
	        'aspect_ratio' 				=> $aspect_fraction,
	        'mobile_aspect_ratio' 		=> $mobile_aspect_fraction,
	        'slider_carousel' 			=> $ewd_us_controller->settings->get_setting( 'carousel' ),
	        'carousel_columns' 			=> $ewd_us_controller->settings->get_setting( 'carousel-columns' ),
	        'carousel_link_to_full' 	=> $ewd_us_controller->settings->get_setting( 'carousel-link-to-full' ),
	        'carousel_advance' 			=> $ewd_us_controller->settings->get_setting( 'carousel-advance' ),
	        'title_animate' 			=> $ewd_us_controller->settings->get_setting( 'title-animate' ),
	        'lightbox' 					=> $ewd_us_controller->settings->get_setting( 'lightbox' ),
	        'timer_bar' 				=> $ewd_us_controller->settings->get_setting( 'timer-bar' ),
	        'force_full_width' 			=> $ewd_us_controller->settings->get_setting( 'force-full-width' ),
	        'autoplay_pause_hover' 		=> $ewd_us_controller->settings->get_setting( 'autoplay-pause-hover' )
	    );
	
	    $ewd_us_controller->add_front_end_php_data( 'ewd-us-js', 'ewd_us_php_data', $slider_data );
	
	    wp_enqueue_style( 'ewd-us-css' );
	
	    if ( $ewd_us_controller->settings->get_setting( 'lightbox' ) ) { 
	
	    	wp_enqueue_script( 'ultimate-lightbox' ); 
	
	    	wp_enqueue_style( 'ewd-ulb-main' );
	    }
	}

}

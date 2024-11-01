<?php

/**
 * Class to display a slide in the slider on the front end.
 *
 * @since 2.0.0
 */
class ewdusViewSlideWooCommerce extends ewdusViewSlide {

	/**
	 * Get the content (image, title, etc.) of the slide
	 *
	 * @since 2.0.0
	 */
	public function get_slide_content() {

		if ( empty( $this->post ) or empty( $this->post->ID ) ) { return; }

		$wc_product_id = get_post_meta( $this->post->ID, "EWD_US_WC_Product_ID", true );

		// If this is a slide that has been assigned a product, load the product
		if ( $wc_product_id ) {

			$post = get_post( $wc_product_id );

			if ( ! $post ) { return; }

			$this->title 				= $post->post_title;
			$this->filtered_content 	= str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $post->post_content ) );
			$this->post_thumbnail_id 	= get_post_thumbnail_id( $post->ID );
			$this->image_url 			= $this->post_thumbnail_id ? wp_get_attachment_url( $this->post_thumbnail_id ) : $this->image_url;
		}
		else {

			parent::get_slide_content();
		}

	}

	/**
	 * Get the initial slide css classes
	 * @since 2.0.0
	 */
	public function slide_classes( $classes = array() ) {
		
		$parent_classes = parent::slide_classes();

		$classes = array_merge(
			$classes,
			$parent_classes,
			array(
				'woocommerce_slide'
			)
		);

		return apply_filters( 'us_slide_classes', $classes, $this );
	}

}

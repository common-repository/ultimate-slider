<?php

/**
 * Class to display a slide in the slider on the front end.
 *
 * @since 2.0.0
 */
class ewdusViewSlideURP extends ewdusViewSlide {

	/**
	 * Get the content (image, title, etc.) of the slide
	 *
	 * @since 2.0.0
	 */
	public function get_slide_content() { 

		if ( empty( $this->post ) or empty( $this->post->ID ) ) { return; }

		parent::get_slide_content();

		if ( ! function_exists( 'EWD_URP_Get_Score_Graphic' ) ) { return; }

		$this->title = $this->title . "<div class='ewd-us-urp-score'>" . EWD_URP_Get_Score_Graphic( get_post( $this->post->ID ) ) . "</div>";
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
				'urp_slide'
			)
		);

		return apply_filters( 'us_slide_classes', $classes, $this );
	}

}

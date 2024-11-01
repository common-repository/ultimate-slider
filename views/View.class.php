<?php

/**
 * Base class for any view requested on the front end.
 *
 * @since 2.0.0
 */
class ewdusView extends ewdusBase {

	/**
	 * Post type to render
	 */
	public $post_type = null;

	/**
	 * Map types of content to the template which will render them
	 */
	public $content_map = array(
		'title'							 => 'content/title',
	);

	// Locations that should be searched for templates
	public $template_dirs;

	// Default labels, used a fallbacks if no admin inputted label exists
	public $label_defaults = array();

	// The classes that should be added to the main form div
	public $classes;

	/**
	 * Initialize the class
	 * @since 2.0.0
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );
		
		// Filter the content map so addons can customize what and how content
		// is output. Filters are specific to each view, so for this base view
		// you would use the filter 'ewd_us_content_map_ewdusView'
		$this->content_map = apply_filters( 'ewd_us_content_map_' . get_class( $this ), $this->content_map );

	}

	/**
	 * Render the view and enqueue required stylesheets
	 *
	 * @note This function should always be overridden by an extending class
	 * @since 2.0.0
	 */
	public function render() {

		$this->set_error(
			array( 
				'type'		=> 'render() called on wrong class'
			)
		);
	}

	/**
	 * Load a template file for views
	 *
	 * First, it looks in the current theme's /ewd-us-templates/ directory. Then it
	 * will check a parent theme's /ewd-us-templates/ directory. If nothing is found
	 * there, it will retrieve the template from the plugin directory.

	 * @since 2.0.0
	 * @param string template Type of template to load (eg - slider, slide)
	 */
	function find_template( $template ) {

		$this->template_dirs = array(
			get_stylesheet_directory() . '/' . EWD_US_TEMPLATE_DIR . '/',
			get_template_directory() . '/' . EWD_US_TEMPLATE_DIR . '/',
			EWD_US_PLUGIN_DIR . '/' . EWD_US_TEMPLATE_DIR . '/'
		);
		
		$this->template_dirs = apply_filters( 'ewd_us_template_directories', $this->template_dirs );

		foreach ( $this->template_dirs as $dir ) {
			if ( file_exists( $dir . $template . '.php' ) ) {
				return $dir . $template . '.php';
			}
		}

		return false;
	}

	/**
	 * Enqueue stylesheets
	 */
	public function enqueue_assets() {

		//enqueue assets here
	}

	public function add_custom_styling() {
		global $ewd_us_controller;

		echo '<style>';
			if ( $ewd_us_controller->settings->get_setting( 'styling-slide-title-font' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-title { font-family: \'' . $ewd_us_controller->settings->get_setting( 'styling-slide-title-font' ) . '\' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-slide-title-font-size' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-title { font-size: ' . $ewd_us_controller->settings->get_setting( 'styling-slide-title-font-size' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-slide-title-font-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-title { color: ' . $ewd_us_controller->settings->get_setting( 'styling-slide-title-font-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-slide-text-font' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-excerpt { font-family: \'' . $ewd_us_controller->settings->get_setting( 'styling-slide-text-font' ) . '\' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-slide-text-font-size' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-excerpt { font-size: ' . $ewd_us_controller->settings->get_setting( 'styling-slide-text-font-size' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-slide-text-font-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-excerpt, .ewd-us-slide .ewd-us-slide-excerpt p { color: ' . $ewd_us_controller->settings->get_setting( 'styling-slide-text-font-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-font' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a { font-family: \'' . $ewd_us_controller->settings->get_setting( 'styling-button-font' ) . '\' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-font-size' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a { font-size: ' . $ewd_us_controller->settings->get_setting( 'styling-button-font-size' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-background-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a { background: ' . $ewd_us_controller->settings->get_setting( 'styling-button-background-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-border-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a { border-color: ' . $ewd_us_controller->settings->get_setting( 'styling-button-border-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-text-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a { color: ' . $ewd_us_controller->settings->get_setting( 'styling-button-text-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-background-hover-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a:hover { background: ' . $ewd_us_controller->settings->get_setting( 'styling-button-background-hover-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-border-hover-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a:hover { border-color: ' . $ewd_us_controller->settings->get_setting( 'styling-button-border-hover-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-button-text-hover-color' ) != '' ) { echo '.ewd-us-slide .ewd-us-slide-buttons li a:hover { color: ' . $ewd_us_controller->settings->get_setting( 'styling-button-text-hover-color' ) . 'px !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-arrow-color' ) != '' ) { echo '.ewd-us-slider .ewd-us-nav-arrow .ewd-us-slider-icon { color: ' . $ewd_us_controller->settings->get_setting( 'styling-arrow-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-arrow-font-size' ) != '' ) { echo '.ewd-us-slider .ewd-us-nav-arrow .ewd-us-slider-icon { font-size: ' . $ewd_us_controller->settings->get_setting( 'styling-arrow-font-size' ) . 'px !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-arrow-line-height' ) != '' ) { echo '.ewd-us-slider .ewd-us-nav-arrow .ewd-us-slider-icon { line-height: ' . $ewd_us_controller->settings->get_setting( 'styling-arrow-line-height' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-arrow-background-size' ) != '' ) { echo '.ewd-us-slider .ewd-us-nav-arrow .ewd-us-arrow-div { width: ' . $ewd_us_controller->settings->get_setting( 'styling-arrow-background-size' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-arrow-background-color' ) != '' ) { echo '.ewd-us-slider .ewd-us-nav-arrow .ewd-us-arrow-div { background: ' . $ewd_us_controller->settings->get_setting( 'styling-arrow-background-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-clickable-area-background-color' ) != '' ) { echo '.ewd-us-slider .ewd-us-nav-arrow { background: ' . $ewd_us_controller->settings->get_setting( 'styling-clickable-area-background-color' ) . ' !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'styling-clickable-area-size' ) != '' ) { echo '.ewd-us-slider .ewd-us-nav-arrow { width: ' . $ewd_us_controller->settings->get_setting( 'styling-clickable-area-size' ) . ' !important; }'; }
						
			if ( $ewd_us_controller->settings->get_setting( 'timer-bar' ) == 'top' ) { echo '#ewd-us-timer-bar { top: 0; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'timer-bar' ) == 'bottom' ) { echo '#ewd-us-timer-bar { bottom: 0; }'; }

			if ( $ewd_us_controller->settings->get_setting( 'youtube-show-content' ) ) { echo '.ewd-us-video .ewd-us-slide-text { display: block !important; }'; }
			if ( $ewd_us_controller->settings->get_setting( 'youtube-video-opacity' ) != '' ) { echo '.ewd-us-video { opacity: ' . $ewd_us_controller->settings->get_setting( 'youtube-video-opacity' ) . ' !important; }'; }

			if ( is_admin_bar_showing() ) {
				echo ".lg-outer { margin-top: 32px; } \n";
				echo ".lg-outer .lg-thumb-outer { bottom: 32px; } \n";
				echo "@media screen and (max-width: 782px) { \n";
				echo ".lg-outer { margin-top: 46px; } \n";
				echo ".lg-outer .lg-thumb-outer { bottom: 46px; } \n";
				echo "} \n";
			}

		echo  '</style>';
	}

}

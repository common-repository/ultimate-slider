<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdusSettings' ) ) {
/**
 * Class to handle configurable settings for Ultimate Slider
 * @since 1.0.0
 */
class ewdusSettings {

	/**
	 * Default values for settings
	 * @since 1.0.0
	 */
	public $defaults = array();

	/**
	 * Stored values for settings
	 * @since 1.0.0
	 */
	public $settings = array();

	public function __construct() {

		add_action( 'init', array( $this, 'set_defaults' ) );

		add_action( 'init', array( $this, 'load_settings_panel' ) );
	}

	/**
	 * Load the plugin's default settings
	 * @since 1.0.0
	 */
	public function set_defaults() {

		$this->defaults = array(

			'timer-bar'						=> 'bottom',
			'autoplay-delay'				=> 6,
			'autoplay-interval'				=> 6,
			'transition-time'				=> 1,
			'aspect-ratio'					=> '16_7',
			'mobile-aspect-ratio'			=> '16_7',
			'arrow'							=> 'a',
			'hide-from-slider'				=> array(),
			'hide-on-mobile'				=> array(),
		);

		$this->defaults = apply_filters( 'ewd_us_defaults', $this->defaults, $this );
	}

	/**
	 * Get a setting's value or fallback to a default if one exists
	 * @since 1.0.0
	 */
	public function get_setting( $setting ) { 

		if ( empty( $this->settings ) ) {
			$this->settings = get_option( 'ewd-us-settings' );
		}
		
		if ( ! empty( $this->settings[ $setting ] ) or isset( $this->settings[ $setting ] ) ) {
			return apply_filters( 'ewd-us-settings-' . $setting, $this->settings[ $setting ] );
		}

		if ( ! empty( $this->defaults[ $setting ] ) or isset( $this->defaults[ $setting ] ) ) { 
			return apply_filters( 'ewd-us-settings-' . $setting, $this->defaults[ $setting ] );
		}

		return apply_filters( 'ewd-us-settings-' . $setting, null );
	}

	/**
	 * Set a setting to a particular value
	 * @since 1.0.0
	 */
	public function set_setting( $setting, $value ) {

		$this->settings[ $setting ] = $value;
	}

	/**
	 * Save all settings, to be used with set_setting
	 * @since 1.0.0
	 */
	public function save_settings() {
		
		update_option( 'ewd-us-settings', $this->settings );
	}

	/**
	 * Load the admin settings page
	 * @since 1.0.0
	 * @sa https://github.com/NateWr/simple-admin-pages
	 */
	public function load_settings_panel() {
		global $ewd_us_controller;

		require_once( EWD_US_PLUGIN_DIR . '/lib/simple-admin-pages/simple-admin-pages.php' );
		$sap = sap_initialize_library(
			$args = array(
				'version'       => '2.6.19',
				'lib_url'       => EWD_US_PLUGIN_URL . '/lib/simple-admin-pages/',
				'theme'			=> 'purple',
			)
		);

		$sap->add_page(
			'submenu',
			array(
				'id'            => 'ewd-us-settings',
				'title'         => __( 'Settings', 'ultimate-slider' ),
				'menu_title'    => __( 'Settings', 'ultimate-slider' ),
				'parent_menu'	=> 'edit.php?post_type=ultimate_slider',
				'description'   => '',
				'capability'    => 'manage_options',
				'default_tab'   => 'ewd-us-basic-tab',
			)
		);

		$sap->add_section(
			'ewd-us-settings',
			array(
				'id'            => 'ewd-us-basic-tab',
				'title'         => __( 'Basic', 'ultimate-slider' ),
				'is_tab'		=> true,
			)
		);

		$sap->add_section(
			'ewd-us-settings',
			array(
				'id'            => 'ewd-us-basic-options',
				'title'         => __( 'Basic Options', 'ultimate-slider' ),
				'tab'	        => 'ewd-us-basic-tab',
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'warningtip',
			array(
				'id'			=> 'shortcodes-reminder',
				'title'			=> __( 'REMINDER:', 'ultimate-slider' ),
				'placeholder'	=> __( 'To display the slider, place the [ultimate-slider] shortcode on a page' )
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'textarea',
			array(
				'id'			=> 'custom-css',
				'title'			=> __( 'Custom CSS', 'ultimate-slider' ),
				'description'	=> __( 'You can add custom CSS styles to your slider in the box above.', 'ultimate-slider' ),			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'toggle',
			array(
				'id'			=> 'autoplay-slideshow',
				'title'			=> __( 'Autoplay Slideshow', 'ultimate-slider' ),
				'description'	=> __( 'Should the slider automatically toggle through slides?', 'ultimate-slider' )
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'count',
			array(
				'id'			=> 'autoplay-delay',
				'title'			=> __( 'Autoplay Delay', 'ultimate-slider' ),
				'description'	=> __( 'If autoplay is on, how many seconds should the timer wait before starting the slideshow?', 'ultimate-slider' ),
				'default'		=> $this->defaults['autoplay-delay'],
				'blank_option'	=> false,
				'min_value'		=> 1,
				'max_value'		=> 60,
				'increment'		=> 1,
				'conditional_on'		=> 'autoplay-slideshow',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'count',
			array(
				'id'			=> 'autoplay-interval',
				'title'			=> __( 'Autoplay Interval', 'ultimate-slider' ),
				'description'	=> __( 'If autoplay is on, how many seconds should the slideshow wait between each slide?', 'ultimate-slider' ),
				'default'		=> $this->defaults['autoplay-interval'],
				'blank_option'	=> false,
				'min_value'		=> 1,
				'max_value'		=> 60,
				'increment'		=> 1,
				'conditional_on'		=> 'autoplay-slideshow',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'toggle',
			array(
				'id'			=> 'autoplay-pause-hover',
				'title'			=> __( 'Pause Autoplay on Hover', 'ultimate-slider' ),
				'description'	=> __( 'Should the slider autoplay automatically pause when you hover over it?', 'ultimate-slider' ),
				'conditional_on'		=> 'autoplay-slideshow',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'count',
			array(
				'id'			=> 'transition-time',
				'title'			=> __( 'Slide Transition Time', 'ultimate-slider' ),
				'description'	=> __( 'How many seconds should each transition take to complete?', 'ultimate-slider' ),
				'default'		=> $this->defaults['transition-time'],
				'blank_option'	=> false,
				'min_value'		=> 1,
				'max_value'		=> 10,
				'increment'		=> 1
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'select',
			array(
				'id'            => 'aspect-ratio',
				'title'         => __( 'Aspect Ratio', 'ultimate-slider' ),
				'description'   => '',
				'blank_option'	=> false,
				'default' 		=> $this->defaults['aspect-ratio'],
				'options'       => array(
					'3_1'			=> '3:1',
					'16_7' 			=> '16:7' . __( '(default)', 'ultimate-slider' ),
					'2_1'			=> '2:1',
					'16_9'			=> '16:9',
					'3_2'			=> '3:2',
					'4_3'			=> '4:3',
					'1_1'			=> '1:1',
				)
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'toggle',
			array(
				'id'			=> 'carousel',
				'title'			=> __( 'Carousel', 'ultimate-slider' ),
				'description'	=> __( 'Display a carousel slider instead of the default. The "Slide Transition Effect" setting has to be set to "Default".', 'ultimate-slider' )
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'radio',
			array(
				'id'			=> 'carousel-columns',
				'title'			=> __( 'Carousel Columns', 'ultimate-slider' ),
				'description'	=> __( 'Set the number of slides that should be displayed at once in carousel mode', 'ultimate-slider' ),
				'options'		=> array(
					2			=> 2,
					3			=> 3,
					4			=> 4
				),
				'conditional_on'		=> 'carousel',
				'conditional_on_value'	=> true
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'radio',
			array(
				'id'			=> 'timer-bar',
				'title'			=> __( 'Timer Bar', 'ultimate-slider' ),
				'description'	=> __( 'Display a timer bar at the top or bottom of your slider.', 'ultimate-slider' ),
				'options'		=> array(
					'top'			=> __( 'Top', 'ultimate-slider' ),
					'bottom'		=> __( 'Bottom', 'ultimate-slider' ),
					'off'			=> __( 'Off', 'ultimate-slider' )
				)
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'radio',
			array(
				'id'			=> 'slide-indicators',
				'title'			=> __( 'Slide Indicators', 'ultimate-slider' ),
				'description'	=> __( 'Display navigation controls to jump between slides.', 'ultimate-slider' ),
				'options'		=> array(
					'none'				=> __( 'None', 'ultimate-slider' ),
					'dots'				=> __( 'Dots', 'ultimate-slider' ),
					'thumbnails'		=> __( 'Thumbnails', 'ultimate-slider' ),
					'sidethumbnails'	=> __( 'Side Thumbnails', 'ultimate-slider' )
				)
			)
		);

		$sap->add_setting(
			'ewd-us-settings',
			'ewd-us-basic-options',
			'radio',
			array(
				'id'			=> 'link-action',
				'title'			=> __( 'Button Link Action', 'ultimate-slider' ),
				'description'	=> __( 'Should button links open in the same or new windows? "Smart" opens external links in new windows and links on your site in the same window.', 'ultimate-slider' ),
				'options'		=> array(
					'same'			=> __( 'Same Window', 'ultimate-slider' ),
					'new'			=> __( 'New Window', 'ultimate-slider' ),
					'smart'			=> __( 'Smart', 'ultimate-slider' )
				)
			)
		);

		/**
	     * Premium options preview only
	     */
	    // "Premium" Tab
	    $sap->add_section(
	      'ewd-us-settings',
	      array(
	        'id'     => 'ewd-us-premium-tab',
	        'title'  => __( 'Premium', 'ultimate-slider' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'premium' )
	      )
	    );
	    $sap->add_section(
	      'ewd-us-settings',
	      array(
	        'id'       => 'ewd-us-premium-tab-body',
	        'tab'      => 'ewd-us-premium-tab',
	        'callback' => $this->premium_info( 'premium' )
	      )
	    );
	
	    // "Styling" Tab
	    $sap->add_section(
	      'ewd-us-settings',
	      array(
	        'id'     => 'ewd-us-styling-tab',
	        'title'  => __( 'Styling', 'ultimate-slider' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'styling' )
	      )
	    );
	    $sap->add_section(
	      'ewd-us-settings',
	      array(
	        'id'       => 'ewd-us-styling-tab-body',
	        'tab'      => 'ewd-us-styling-tab',
	        'callback' => $this->premium_info( 'styling' )
	      )
	    );

	    // "Controls" Tab
	    $sap->add_section(
	      'ewd-us-settings',
	      array(
	        'id'     => 'ewd-us-controls-tab',
	        'title'  => __( 'Controls', 'ultimate-slider' ),
	        'is_tab' => true,
	        'show_submit_button' => $this->show_submit_button( 'controls' )
	      )
	    );
	    $sap->add_section(
	      'ewd-us-settings',
	      array(
	        'id'       => 'ewd-us-controls-tab-body',
	        'tab'      => 'ewd-us-controls-tab',
	        'callback' => $this->premium_info( 'controls' )
	      )
	    );

		$sap = apply_filters( 'ewd_us_settings_page', $sap, $this );

		$sap->add_admin_menus();

	}

	public function show_submit_button( $permission_type = '' ) {
		global $ewd_us_controller;

		if ( $ewd_us_controller->permissions->check_permission( $permission_type ) ) {
			return true;
		}

		return false;
	}

	public function premium_info( $section_and_perm_type ) {
		global $ewd_us_controller;

		$is_premium_user = $ewd_us_controller->permissions->check_permission( $section_and_perm_type );
		$is_helper_installed = defined( 'EWDPH_PLUGIN_FNAME' ) && is_plugin_active( EWDPH_PLUGIN_FNAME );

		if ( $is_premium_user || $is_helper_installed ) {
			return false;
		}

		$content = '';

		$premium_features = '
			<p><strong>' . __( 'The premium version also gives you access to the following features:', 'ultimate-slider' ) . '</strong></p>
			<ul class="ewd-us-dashboard-new-footer-one-benefits">
				<li>' . __( 'Integrated Lightbox Effect', 'ultimate-slider' ) . '</li>
				<li>' . __( 'Advanced Styling Options', 'ultimate-slider' ) . '</li>
				<li>' . __( 'Advanced Control Options', 'ultimate-slider' ) . '</li>
				<li>' . __( 'WooCommerce integration', 'ultimate-slider' ) . '</li>
				<li>' . __( 'Add Watermarks', 'ultimate-slider' ) . '</li>
				<li>' . __( 'Email Support', 'ultimate-slider' ) . '</li>
			</ul>
			<div class="ewd-us-dashboard-new-footer-one-buttons">
				<a class="ewd-us-dashboard-new-upgrade-button" href="https://www.etoilewebdesign.com/license-payment/?Selected=US&Quantity=1&utm_source=us_settings&utm_content=' . $section_and_perm_type . '" target="_blank">' . __( 'UPGRADE NOW', 'ultimate-slider' ) . '</a>
			</div>
		';

		switch ( $section_and_perm_type ) {

			case 'premium':

				$content = '
					<div class="ewd-us-settings-preview">
						<h2>' . __( 'Premium', 'ultimate-slider' ) . '<span>' . __( 'Premium', 'ultimate-slider' ) . '</span></h2>
						<p>' . __( 'The premium options let you change the slide transition and title effects, set the aspect ratio, hide specific slider elements (overall or just on mobile), enable a lightbox and video autoplay, and more.', 'ultimate-slider' ) . '</p>
						<div class="ewd-us-settings-preview-images">
							<img src="' . EWD_US_PLUGIN_URL . '/assets/img/premium-screenshots/premium1.png" alt="US premium screenshot one">
							<img src="' . EWD_US_PLUGIN_URL . '/assets/img/premium-screenshots/premium2.png" alt="US premium screenshot two">
						</div>
						' . $premium_features . '
					</div>
				';

				break;

			case 'styling':

				$content = '
					<div class="ewd-us-settings-preview">
						<h2>' . __( 'Styling', 'ultimate-slider' ) . '<span>' . __( 'Premium', 'ultimate-slider' ) . '</span></h2>
						<p>' . __( 'The styling options let you modify the color, font size and font family of the various elements found in the slider.', 'ultimate-slider' ) . '</p>
						<div class="ewd-us-settings-preview-images">
							<img src="' . EWD_US_PLUGIN_URL . '/assets/img/premium-screenshots/styling.png" alt="US styling screenshot">
						</div>
						' . $premium_features . '
					</div>
				';

				break;

			case 'controls':

				$content = '
					<div class="ewd-us-settings-preview">
						<h2>' . __( 'Controls', 'ultimate-slider' ) . '<span>' . __( 'Premium', 'ultimate-slider' ) . '</span></h2>
						<p>' . __( 'The control options let you choose and customize the icon set you want to use for the slider controls. ', 'ultimate-slider' ) . '</p>
						<div class="ewd-us-settings-preview-images">
							<img src="' . EWD_US_PLUGIN_URL . '/assets/img/premium-screenshots/controls.png" alt="US controls screenshot">
						</div>
						' . $premium_features . '
					</div>
				';

				break;
		}

		return function() use ( $content ) {

			echo wp_kses_post( $content );
		};
	}

}
} // endif;

<?php
/*
Plugin Name: Slider Ultimate
Plugin URI: http://www.EtoileWebDesign.com/plugins/
Description: Add a responsive slider to any page with a block shortcode. Multiple slide effects. WooCommerce slider integration.
Author: Etoile Web Design
Author URI: http://www.EtoileWebDesign.com/plugins/
Terms and Conditions: http://www.etoilewebdesign.com/plugin-terms-and-conditions/
Text Domain: ultimate-slider
Version: 2.2.6
*/

if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'ewdusInit' ) ) {
class ewdusInit {

	// pointers to classes used by the plugin, where needed
	public $cpts;
	public $permissions;
	public $settings;

	// Any data that needs to be passed from PHP to our JS files 
	public $front_end_php_js_data = array();

	/**
	 * Initialize the plugin and register hooks
	 */
	public function __construct() {

		self::constants();
		self::includes();
		self::instantiate();
		self::wp_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function constants() {

		define( 'EWD_US_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'EWD_US_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'EWD_US_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
		define( 'EWD_US_TEMPLATE_DIR', 'ewd-us-templates' );
		define( 'EWD_US_VERSION', '2.2.6' );

		define( 'EWD_US_SLIDER_POST_TYPE', 'ultimate_slider' );
		define( 'EWD_US_SLIDER_CATEGORY_TAXONOMY', 'ultimate_slider_categories' );
		define( 'EWD_US_SLIDER_TAG_TAXONOMY', 'ultimate_slider_tags' );
	}

	/**
	 * Include necessary classes.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function includes() {

		require_once( EWD_US_PLUGIN_DIR . '/includes/AboutUs.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/Blocks.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/CustomPostTypes.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/Dashboard.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/DeactivationSurvey.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/Helper.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/InstallationWalkthrough.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/Permissions.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/ReviewAsk.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/Settings.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/template-functions.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/Widgets.class.php' );
		require_once( EWD_US_PLUGIN_DIR . '/includes/WooCommerceIntegration.class.php' );
	}

	/**
	 * Spin up instances of our plugin classes.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function instantiate() {

		new ewdusDashboard();
		new ewdusDeactivationSurvey();
		new ewdusInstallationWalkthrough();
		new ewdusReviewAsk();

		$this->cpts 		= new ewdusCustomPostTypes();
		$this->permissions 	= new ewdusPermissions();
		$this->settings 	= new ewdusSettings();

		new ewdusBlocks();
		new ewdusWidgetManager();

		if ( $this->settings->get_setting( 'wc-product-image-slider' ) ) {
			new ewdusWooCommerceIntegration();
		}

		new ewdusAboutUs();
	}

	/**
	 * Run walk-through, load assets, add links to plugin listing, etc.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function wp_hooks() {

		register_activation_hook( __FILE__, 	array( $this, 'run_walkthrough' ) );
		register_activation_hook( __FILE__, 	array( $this, 'convert_options' ) );

		add_action( 'init',			        	array( $this, 'load_view_files' ) );

		add_action( 'plugins_loaded',        	array( $this, 'load_textdomain' ) );

		add_action( 'admin_notices', 			array( $this, 'display_header_area' ) );
		add_action( 'admin_notices', 			array( $this, 'maybe_display_helper_notice' ) );

		add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_admin_assets' ), 10, 1 );
		add_action( 'wp_enqueue_scripts', 		array( $this, 'register_assets' ) );
		add_action( 'wp_head',					'ewd_add_frontend_ajax_url' );
		add_action( 'wp_footer', 				array( $this, 'assets_footer' ), 2 );

		add_filter( 'plugin_action_links',		array( $this, 'plugin_action_links' ), 10, 2);

		add_action( 'wp_ajax_ewd_us_hide_helper_notice', array( $this, 'hide_helper_notice' ) );
	}

	/**
	 * Run the options conversion function on update if necessary
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	public function convert_options() {
		
		require_once( EWD_US_PLUGIN_DIR . '/includes/BackwardsCompatibility.class.php' );
		new ewdusBackwardsCompatibility();
	}

	/**
	 * Load files needed for views
	 * @since 2.0.0
	 * @note Can be filtered to add new classes as needed
	 */
	public function load_view_files() {
	
		$files = array(
			EWD_US_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
		);
	
		$files = apply_filters( 'ewd_us_load_view_files', $files );
	
		foreach( $files as $file ) {
			require_once( $file );
		}
	
	}

	/**
	 * Load the plugin textdomain for localisation
	 * @since 2.0.0
	 */
	public function load_textdomain() {
		
		load_plugin_textdomain( 'ultimate-slider', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Set a transient so that the walk-through gets run
	 * @since 2.0.0
	 */
	public function run_walkthrough() {

		set_transient( 'ewd-us-getting-started', true, 30 );
	} 

	/**
	 * Enqueue the admin-only CSS and Javascript
	 * @since 2.0.0
	 */
	public function enqueue_admin_assets( $hook ) {
		global $post;

		wp_enqueue_script( 'ewd-us-helper-notice', EWD_US_PLUGIN_URL . '/assets/js/ewd-us-helper-install-notice.js', array( 'jquery' ), EWD_US_VERSION, true );
		wp_localize_script(
			'ewd-us-helper-notice',
			'ewd_us_helper_notice',
			array( 'nonce' => wp_create_nonce( 'ewd-us-helper-notice' ) )
		);

		wp_enqueue_style( 'ewd-us-helper-notice', EWD_US_PLUGIN_URL . '/assets/css/ewd-us-helper-install-notice.css', array(), EWD_US_VERSION );

		$screen = get_current_screen();

		$candidates = array(
			EWD_US_SLIDER_POST_TYPE,

			'ultimate_slider_page_ewd-us-settings',

			'widgets.php',
		);

   		// Return if not ultimate_slider post_type, we're not on a post-type page, or we're not on the settings or widget pages
		if ( ! in_array( $hook, $candidates )
			and ( empty( $screen->post_type ) or ! in_array ( $screen->post_type, $candidates ) )
			and ! in_array( $screen->id, $candidates )
		) {
			return;
		}
           
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'ewd-us-admin-js', EWD_US_PLUGIN_URL . '/assets/js/ewd-us-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), EWD_US_VERSION, true );

		wp_enqueue_style( 'ewd-us-admin-css', EWD_US_PLUGIN_URL . '/assets/css/ewd-us-admin.css', EWD_US_VERSION );

		$settings = array(
			'nonce' => wp_create_nonce( 'ewd-us-admin-js' ),
		);

		wp_localize_script( 'ewd-us-admin-js', 'ewd_us_admin_php_data', $settings );
	}

	/**
	 * Register the front-end CSS and Javascript for the slider
	 * @since 2.0.0
	 */
	function register_assets() {
		global $ewd_us_controller;

    	wp_register_script( 'ewd-us-js', EWD_US_PLUGIN_URL . '/assets/js/ewd-us.js', array( 'jquery', 'jquery-ui-core', 'jquery-effects-slide' ), EWD_US_VERSION, true );
    	wp_register_script( 'iframe-clicks', EWD_US_PLUGIN_URL . '/assets/js/jquery.iframetracker.js' , array( 'jquery' ), EWD_US_VERSION, true );

    	wp_register_style( 'ewd-us-css', EWD_US_PLUGIN_URL . '/assets/css/ewd-us.css', EWD_US_VERSION );
    	
    	if ( $ewd_us_controller->settings->get_setting( 'lightbox' ) ) {

    	    wp_register_script( 'ultimate-lightbox', EWD_US_PLUGIN_URL . '/assets/js/ultimate-lightbox.js', array( 'jquery' ), EWD_US_VERSION, true );

    	    wp_register_style( 'ewd-ulb-main', EWD_US_PLUGIN_URL . '/assets/css/ewd-ulb-main.css', EWD_US_VERSION );
    	}
	}

	/**
	 * Print out any PHP data needed for our JS to work correctly
	 * @since 2.1.0
	 */
	public function assets_footer() {

		if ( empty( $this->front_end_php_js_data ) ) { return; }

		$print_variables = array();

		foreach ( (array) $this->front_end_php_js_data as $variable => $values ) {

			if ( empty( $values ) ) { continue; }

			$print_variables[ $variable ] = ewdusHelper::escape_js_recursive( $values );
		}

		foreach ( $print_variables as $variable => $values ) {

			echo "<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n";
			echo 'var ' . esc_attr( $variable ) . ' = ' . wp_json_encode( $values ) . "\n";
			echo "/* ]]> */\n";
			echo "</script>\n";
		}
	}

	/**
	 * Adds a variable to be passed to our front-end JS
	 * @since 2.1.0
	 */
	public function add_front_end_php_data( $handle, $variable, $data ) {

		$this->front_end_php_js_data[ $variable ] = $data;
	}

	/**
	 * Returns the corresponding front-end JS variable if it exists, otherwise an empty array
	 * @since 2.1.0
	 */
	public function get_front_end_php_data( $handle, $variable ) {

		return ! empty( $this->front_end_php_js_data[ $variable ] ) ? $this->front_end_php_js_data[ $variable ] : array();
	}

	/**
	 * Add links to the plugin listing on the installed plugins page
	 * @since 2.0.0
	 */
	public function plugin_action_links( $links, $plugin ) {
		global $ewd_us_controller;
		
		if ( $plugin == EWD_US_PLUGIN_FNAME ) {

			if ( ! $ewd_us_controller->permissions->check_permission( 'premium' ) ) {

				array_unshift( $links, '<a class="ewd-us-plugin-page-upgrade-link" href="https://www.etoilewebdesign.com/license-payment/?Selected=US&Quantity=1&utm_source=wp_admin_plugins_page" title="' . __( 'Try Premium', 'ultimate-slider' ) . '" target="_blank">' . __( 'Try Premium', 'ultimate-slider' ) . '</a>' );
			}

			$links['settings'] = '<a href="admin.php?page=ewd-us-settings" title="' . __( 'Head to the settings page for Ultimate Slider', 'ultimate-slider' ) . '">' . __( 'Settings', 'ultimate-slider' ) . '</a>';
		}

		return $links;

	}

	/**
	 * Adds in a menu bar for the plugin
	 * @since 2.0.0
	 */
	public function display_header_area() {
		global $ewd_us_controller;

		$screen = get_current_screen();
		
		if ( empty( $screen->parent_file ) or $screen->parent_file != 'edit.php?post_type=ultimate_slider' ) { return; }
		
		if ( ! $ewd_us_controller->permissions->check_permission( 'styling' ) or get_option( 'EWD_US_Trial_Happening' ) == 'Yes' ) {
			?>
			<div class="ewd-us-dashboard-new-upgrade-banner">
				<div class="ewd-us-dashboard-banner-icon"></div>
				<div class="ewd-us-dashboard-banner-buttons">
					<a class="ewd-us-dashboard-new-upgrade-button" href="https://www.etoilewebdesign.com/license-payment/?Selected=US&Quantity=1&utm_source=us_admin&utm_content=banner" target="_blank">UPGRADE NOW</a>
				</div>
				<div class="ewd-us-dashboard-banner-text">
					<div class="ewd-us-dashboard-banner-title">
						GET FULL ACCESS WITH OUR PREMIUM VERSION
					</div>
					<div class="ewd-us-dashboard-banner-brief">
						Slide and title animations/effects, modify the controls, WooCommerce integration, advanced styling options and more!
					</div>
				</div>
			</div>
			<?php
		}
		
		?>
		<div class="ewd-us-admin-header-menu">
			<h2 class="nav-tab-wrapper">
			<a id="ewd-us-dash-mobile-menu-open" href="#" class="menu-tab nav-tab"><?php _e("MENU", 'ultimate-slider'); ?><span id="ewd-us-dash-mobile-menu-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-us-dash-mobile-menu-up-caret">&nbsp;&nbsp;&#9650;</span></a>
			<a id="dashboard-menu" href='admin.php?page=ewd-us-dashboard' class="menu-tab nav-tab <?php if ( $screen->id == 'ultimate_slider_ewd-us-dashboard' ) {echo 'nav-tab-active';}?>"><?php _e("Dashboard", 'ultimate-slider'); ?></a>
			<a id="slides-menu" href='edit.php?post_type=ultimate_slider' class="menu-tab nav-tab <?php if ( $screen->id == 'edit-ultimate_slider' ) {echo 'nav-tab-active';}?>"><?php _e("Slides", 'ultimate-slider'); ?></a>
			<a id="options-menu" href='edit.php?post_type=ultimate_slider&page=ewd-us-settings' class="menu-tab nav-tab <?php if ( $screen->id == ' ultimate_slider_page_ewd-us-settings' ) {echo 'nav-tab-active';}?>"><?php _e("Settings", 'ultimate-slider'); ?></a>
			</h2>
		</div>
		<?php
	}

	public function maybe_display_helper_notice() {
		global $ewd_us_controller;

		if ( empty( $ewd_us_controller->permissions->check_permission( 'premium' ) ) ) { return; }

		if ( is_plugin_active( 'ewd-premium-helper/ewd-premium-helper.php' ) ) { return; }

		if ( get_transient( 'ewd-helper-notice-dismissed' ) ) { return; }

		?>

		<div class='notice notice-error is-dismissible ewd-us-helper-install-notice'>
			
			<div class='ewd-us-helper-install-notice-img'>
				<img src='<?php echo EWD_US_PLUGIN_URL . '/lib/simple-admin-pages/img/options-asset-exclamation.png' ; ?>' />
			</div>

			<div class='ewd-us-helper-install-notice-txt'>
				<?php _e( 'You\'re using the Ultimate Slider premium version, but the premium helper plugin is not active.', 'ultimate-slider' ); ?>
				<br />
				<?php echo sprintf( __( 'Please re-activate the helper plugin, or <a target=\'_blank\' href=\'%s\'>download and install it</a> if the plugin is no longer installed to ensure continued access to the premium features of the plugin.', 'ultimate-slider' ), 'https://www.etoilewebdesign.com/2021/12/11/requiring-premium-helper-plugin/' ); ?>
			</div>

			<div class='ewd-us-clear'></div>

		</div>

		<?php 
	}

	public function hide_helper_notice() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-us-helper-notice', 'nonce' ) or ! current_user_can( 'manage_options' ) ) {
			
			wp_send_json_error(
				array(
					'error' => 'loggedout',
					'msg' => sprintf( __( 'You have been logged out. Please %slogin again%s.', 'ultimate-slider' ), '<a href="' . wp_login_url( admin_url( 'admin.php?page=ewd-us-dashboard' ) ) . '">', '</a>' ),
				)
			);
		}

		set_transient( 'ewd-helper-notice-dismissed', true, 3600*24*7 );

		die();
	}

}
} // endif;

global $ewd_us_controller;
$ewd_us_controller = new ewdusInit();

do_action( 'ewd_us_initialized' );
<?php

/**
 * Class to handle everything related to the walk-through that runs on plugin activation
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

class ewdusInstallationWalkthrough {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_install_screen' ) );
		add_action( 'admin_head', array( $this, 'hide_install_screen_menu_item' ) );
		add_action( 'admin_init', array( $this, 'redirect' ), 9999 );

		add_action( 'admin_head', array( $this, 'admin_enqueue' ) );

		add_action( 'wp_ajax_us_welcome_add_slide', array( $this, 'create_slide' ) );
		add_action( 'wp_ajax_us_welcome_add_slider_page', array( $this, 'add_slider_page' ) );
		add_action( 'wp_ajax_us_welcome_set_options', array( $this, 'set_options' ) );
	}

	/**
	 * On activation, redirect the user if they haven't used the plugin before
	 * @since 2.0.0
	 */
	public function redirect() {
		if ( ! get_transient( 'ewd-us-getting-started' ) ) 
			return;

		delete_transient( 'ewd-us-getting-started' );

		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
			return;

		if ( ! empty( get_posts( array( 'post_type' => EWD_US_SLIDER_POST_TYPE ) ) ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=ewd-us-getting-started' ) );
		exit;
	}

	/**
	 * Create the installation admin page
	 * @since 2.0.0
	 */
	public function register_install_screen() {

		add_dashboard_page(
			esc_html__( 'Ultimate Slider - Welcome!', 'ultimate-slider' ),
			esc_html__( 'Ultimate Slider - Welcome!', 'ultimate-slider' ),
			'manage_options',
			'ewd-us-getting-started',
			array( $this, 'display_install_screen' )
		);
	}

	/**
	 * Hide the installation admin page from the WordPress sidebar menu
	 * @since 2.0.0
	 */
	public function hide_install_screen_menu_item() {

		remove_submenu_page( 'index.php', 'ewd-us-getting-started' );
	}

	/**
	 * Create a new slide (image, title, description)
	 * @since 2.0.0
	 */
	public function create_slide() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-us-getting-started', 'nonce' ) ) {
			
			ewdusHelper::admin_nopriv_ajax();
		}

		$post_id = wp_insert_post( array(
			'post_title' => isset( $_POST['slide_title'] ) ? sanitize_text_field( $_POST['slide_title'] ) : '',
			'post_content' => isset( $_POST['slide_description'] ) ? '<!-- wp:paragraph --><p>' . sanitize_textarea_field( $_POST['slide_description'] ) . '</p><!-- /wp:paragraph -->' : '',
			'post_status' => 'publish',
			'post_type' => 'ultimate_slider'
		) );
	
		if ( $post_id ) {

			update_post_meta( $post_id, "EWD_US_Slide_Order", 999 );

			$attachment_id = isset( $_POST['slide_image'] ) ? attachment_url_to_postid( esc_url_raw( $_POST['slide_image'] ) ) : false;
			if ( $attachment_id ) {

				set_post_thumbnail( $post_id, $attachment_id );
			}
		}

		exit();
	}

	/**
	 * Add in a page with the slider shortcode 
	 * @since 2.0.0
	 */
	public function add_slider_page() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-us-getting-started', 'nonce' ) ) {
			
			ewdusHelper::admin_nopriv_ajax();
		}

		$slider_page = wp_insert_post( array(
			'post_title' => isset( $_POST['slider_page_title'] ) ? sanitize_text_field( $_POST['slider_page_title'] ) : '',
			'post_content' => '<!-- wp:paragraph --><p> [ultimate-slider] </p><!-- /wp:paragraph -->',
			'post_status' => 'publish',
			'post_type' => 'page'
		) );
	
	    exit();
	}

	/**
	 * Set a number of key options selected during the walk-through process
	 * @since 2.0.0
	 */
	public function set_options() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-us-getting-started', 'nonce' ) ) {
			
			ewdusHelper::admin_nopriv_ajax();
		}

		$ewd_us_options = get_option( 'ewd-us-settings' );

		if ( isset( $_POST['autoplay_slideshow'] ) ) { $ewd_us_options['autoplay-slideshow'] = $_POST['autoplay_slideshow'] == 'true' ? 1 : 0; }
		if ( isset( $_POST['aspect_ratio'] ) ) { $ewd_us_options['aspect-ratio'] = sanitize_text_field( $_POST['aspect_ratio'] ); }
		if ( isset( $_POST['carousel'] ) ) { $ewd_us_options['carousel'] = $_POST['carousel'] == 'true' ? 1 : 0; }
		if ( isset( $_POST['slide_indicators'] ) ) { $ewd_us_options['slide-indicators'] = sanitize_text_field( $_POST['slide_indicators'] ); }
		if ( isset( $_POST['timer_bar'] ) ) { $ewd_us_options['timer-bar'] = sanitize_text_field( $_POST['timer_bar'] ); }

		update_option( 'ewd-us-settings', $ewd_us_options );
	
	    exit();
	}

	/**
	 * Enqueue the admin assets necessary to run the walk-through and display it nicely
	 * @since 2.0.0
	 */
	public function admin_enqueue() {

		if ( ! isset( $_GET['page'] ) or $_GET['page'] != 'ewd-us-getting-started' ) { return; }

		wp_enqueue_style( 'ewd-us-admin-css', EWD_US_PLUGIN_URL . '/assets/css/admin.css', array(), EWD_US_VERSION );
		wp_enqueue_style( 'ewd-us-sap-admin-css', EWD_US_PLUGIN_URL . '/lib/simple-admin-pages/css/admin.css', array(), EWD_US_VERSION );
		wp_enqueue_style( 'ewd-us-welcome-screen', EWD_US_PLUGIN_URL . '/assets/css/ewd-us-welcome-screen.css', array(), EWD_US_VERSION );
		wp_enqueue_style( 'ewd-us-admin-settings-css', EWD_US_PLUGIN_URL . '/lib/simple-admin-pages/css/admin-settings.css', array(), EWD_US_VERSION );
		
		wp_enqueue_script( 'ewd-us-getting-started', EWD_US_PLUGIN_URL . '/assets/js/ewd-us-welcome-screen.js', array( 'jquery' ), EWD_US_VERSION );
		wp_enqueue_script( 'ewd-us-admin-settings-js', EWD_US_PLUGIN_URL . '/lib/simple-admin-pages/js/admin-settings.js', array( 'jquery' ), EWD_US_VERSION );
		wp_enqueue_script( 'ewd-us-admin-spectrum-js', EWD_US_PLUGIN_URL . '/lib/simple-admin-pages/js/spectrum.js', array( 'jquery' ), EWD_US_VERSION );

		wp_localize_script(
			'ewd-us-getting-started',
			'ewd_us_getting_started',
			array(
				'nonce' => wp_create_nonce( 'ewd-us-getting-started' )
			)
		);
	}

	/**
	 * Output the HTML of the walk-through screen
	 * @since 2.0.0
	 */
	public function display_install_screen() { ?>

		<div class='ewd-us-welcome-screen'>
			
			<div class='ewd-us-welcome-screen-header'>
				<h1><?php _e( 'Welcome to the Ultimate Slider Plugin', 'ultimate-slider' ); ?></h1>
				<p><?php _e( 'Thanks for choosing the Ultimate Slider! The following will help you get started with the plugin, by choosing which images the slider should be displayed for as well as the look of the slider.', 'ultimate-slider' ); ?></p>
			</div>

			<div class='ewd-us-welcome-screen-box ewd-us-welcome-screen-add_slides ewd-us-welcome-screen-open' data-screen='add_slides'>
				<h2><?php _e( '1. Add Slides', 'ultimate-slider' ); ?></h2>
				<div class='ewd-us-welcome-screen-box-content'>
					<table class='form-table ewd-us-welcome-screen-created-slides'>
						<tr class='ewd-us-welcome-screen-add-slide-image ewd-us-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Slide Image', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option ewd-us-welcome-screen-image-preview-container'>
								<div class='ewd-us-hidden ewd-us-welcome-screen-image-preview'>
									<img>
								</div>
								<input type='hidden' name='slide_image_url'>
								<input id="welcome_slide_image_button" class="button" type="button" value="Upload Image">
							</td>
						</tr>
						<tr class='ewd-us-welcome-screen-add-slide-title ewd-us-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Slide Title', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<input type='text'>
							</td>
						</tr>
						<tr class='ewd-us-welcome-screen-add-slide-description ewd-us-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Description', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<textarea></textarea>
							</td>
						</tr>
						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-us-welcome-screen-add-slide-button'><?php _e( 'Add Slide', 'ultimate-slider' ); ?></div>
							</td>
						</tr>
						<tr></tr>
						<tr>
							<td colspan="2">
								<h3><?php _e( 'Created Slides', 'ultimate-slider' ); ?></h3>
								<table class="ewd-us-welcome-screen-show-created-slides">
									<tr>
										<th class="ewd-us-welcome-screen-show-created-slides-image"><?php _e( 'Image', 'ultimate-slider' ); ?></th>
										<th class="ewd-us-welcome-screen-show-created-slides-title"><?php _e( 'Name', 'ultimate-slider' ); ?></th>
										<th class="ewd-us-welcome-screen-show-created-slides-description"><?php _e( 'Description', 'ultimate-slider' ); ?></th>
									</tr>
								</table>
							</td>
						</tr>
					</table>

					<div class="ewd-us-welcome-clear"></div>
					<div class='ewd-us-welcome-screen-next-button' data-nextaction='slider_page'><?php _e( 'Next Step', 'ultimate-slider' ); ?></div>
					<div class='clear'></div>
				</div>
			</div>

			<div class='ewd-us-welcome-screen-box ewd-us-welcome-screen-slider_page' data-screen='slider_page'>
				<h2><?php _e( '2. Add a Slider Page', 'ultimate-slider' ); ?></h2>
				<div class='ewd-us-welcome-screen-box-content'>
				<p><?php _e( 'You can create a dedicated page for your slider below, or skip this step and add your slider to a page you\'ve already created manually.', 'ultimate-slider' ); ?></p>
					<table class='form-table ewd-us-welcome-screen-menu-page'>
						<tr class='ewd-us-welcome-screen-add-slider-page-name ewd-us-welcome-screen-box-content-divs'>
							<th scope='row'><?php _e( 'Page Title', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<input type='text' value='Slider'>
							</td>
						</tr>
						<tr>
							<th scope='row'></th>
							<td>
								<div class='ewd-us-welcome-screen-add-slider-page-button' data-nextaction='options'><?php _e( 'Create Page', 'ultimate-slider' ); ?></div>
							</td>
						</tr>
					</table>

					<div class="ewd-us-welcome-clear"></div>
					<div class='ewd-us-welcome-screen-next-button' data-nextaction='options'><?php _e( 'Next Step', 'ultimate-slider' ); ?></div>
					<div class='ewd-us-welcome-screen-previous-button' data-previousaction='add_slides'><?php _e( 'Previous Step', 'ultimate-slider' ); ?></div>
					<div class='clear'></div>
				</div>
			</div>

			<div class='ewd-us-welcome-screen-box ewd-us-welcome-screen-options' data-screen='options'>
				<h2><?php _e( '3. Key Options', 'ultimate-slider' ); ?></h2>
				<div class='ewd-us-welcome-screen-box-content'>
					<table class="form-table">
						<tr>
							<th scope='row'><?php _e( 'Autoplay Slides', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<fieldset>
									<div class="sap-admin-hide-radios">
										<input type='checkbox' name='autoplay_slideshow' value='1'>
									</div>
									<label class="sap-admin-switch">
										<input type="checkbox" class="sap-admin-option-toggle" data-inputname="autoplay_slideshow" checked="checked">
										<span class="sap-admin-switch-slider round"></span>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr>
							<th scope='row'><?php _e( 'Show Timer Bar', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<fieldset>
									<label title='Top' class='sap-admin-input-container'><input type='radio' name='timer_bar' value='top'><span class='sap-admin-radio-button'></span> <span><?php _e( 'Top', 'ultimate-slider' )?></span></label><br>		
									<label title='Bottom' class='sap-admin-input-container'><input type='radio' name='timer_bar' value='bottom' checked><span class='sap-admin-radio-button'></span> <span><?php _e( 'Bottom', 'ultimate-slider' )?></span></label><br>			
									<label title='Off' class='sap-admin-input-container'><input type='radio' name='timer_bar' value='off'><span class='sap-admin-radio-button'></span> <span><?php _e( 'Off', 'ultimate-slider' )?></span></label><br>
								</fieldset>
							</td>
						</tr>

						<tr>
							<th scope='row'><?php _e( 'Carousel Mode', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<fieldset>
									<div class="sap-admin-hide-radios">
										<input type='checkbox' name='carousel' value='1'>
									</div>
									<label class="sap-admin-switch">
										<input type="checkbox" class="sap-admin-option-toggle" data-inputname="carousel" checked="checked">
										<span class="sap-admin-switch-slider round"></span>
									</label>
								</fieldset>
							</td>
						</tr>

						<tr>
							<th scope='row'><?php _e( 'Aspect Ratio', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<select name='aspect_ratio'>
									<option value='3_1'>3:1</option>
									<option value='16_7' selected>16:7</option>
									<option value='2_1'>2:1</option>
									<option value='16_9'>16:9</option>
									<option value='3_2'>3:2</option>
									<option value='4_3'>4:3</option>
									<option value='1_1'>1:1</option>
								</select>
							</td>
						</tr>

						<tr>
							<th scope='row'><?php _e( 'Slide Indicators', 'ultimate-slider' ); ?></th>
							<td class='ewd-us-welcome-screen-option'>
								<fieldset>
									<label title='None' class='sap-admin-input-container'><input type='radio' name='slide_indicators' value='none'><span class='sap-admin-radio-button'></span> <span><?php _e( 'None', 'ultimate-slider' )?></span></label><br>		
									<label title='Dots' class='sap-admin-input-container'><input type='radio' name='slide_indicators' value='dots' checked><span class='sap-admin-radio-button'></span> <span><?php _e( 'Dots', 'ultimate-slider' )?></span></label><br>			
									<label title='Thumbnails' class='sap-admin-input-container'><input type='radio' name='slide_indicators' value='thumbnails'><span class='sap-admin-radio-button'></span> <span><?php _e( 'Thumbnails', 'ultimate-slider' )?></span></label><br>
									<label title='Side Thumbnails' class='sap-admin-input-container'><input type='radio' name='slide_indicators' value='sidethumbnails'><span class='sap-admin-radio-button'></span> <span><?php _e( 'Side Thumbnails', 'ultimate-slider' )?></span></label><br>
								</fieldset>
							</td>
						</tr>
					</table>

					<div class='ewd-us-welcome-screen-save-options-button'><?php _e( 'Save Options', 'ultimate-slider' ); ?></div>
					<div class="ewd-us-welcome-clear"></div>
					<div class='ewd-us-welcome-screen-previous-button' data-previousaction='add_slider'><?php _e( 'Previous Step', 'ultimate-slider' ); ?></div>
					<div class='ewd-us-welcome-screen-finish-button'><a href='admin.php?page=ewd-us-settings'><?php _e('Finish', 'ultimate-slider'); ?></a></div>
					<div class='clear'></div>
				</div>
			</div>
		
			<div class='ewd-us-welcome-screen-skip-container'>
				<a href='admin.php?page=ewd-us-settings'><div class='ewd-us-welcome-screen-skip-button'><?php _e( 'Skip Setup', 'ultimate-slider' ); ?></div></a>
			</div>
		</div>

	<?php }
}


?>
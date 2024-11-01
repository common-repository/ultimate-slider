<?php
/**
 * Class to create the 'About Us' submenu
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewdusAboutUs' ) ) {
class ewdusAboutUs {

	public function __construct() {

		add_action( 'wp_ajax_ewd_us_send_feature_suggestion', array( $this, 'send_feature_suggestion' ) );

		add_action( 'admin_menu', array( $this, 'register_menu_screen' ) );
	}

	/**
	 * Adds About Us submenu page
	 * @since 2.2.0
	 */
	public function register_menu_screen() {
		global $ewd_us_controller;

		add_submenu_page(
			'edit.php?post_type=ultimate_slider', 
			esc_html__( 'About Us', 'ultimate-slider' ),
			esc_html__( 'About Us', 'ultimate-slider' ),
			'manage_options',
			'ewd-us-about-us',
			array( $this, 'display_admin_screen' )
		);
	}

	/**
	 * Displays the About Us page
	 * @since 2.2.0
	 */
	public function display_admin_screen() { ?>

		<div class='ewd-us-about-us-logo'>
			<img src='<?php echo plugins_url( "../assets/img/ewd_new_logo_purple2.png", __FILE__ ); ?>'>
		</div>

		<div class='ewd-us-about-us-tabs'>

			<ul id='ewd-us-about-us-tabs-menu'>

				<li class='ewd-us-about-us-tab-menu-item ewd-us-tab-selected' data-tab='who_we_are'>
					<?php _e( 'Who We Are', 'ultimate-slider' ); ?>
				</li>

				<li class='ewd-us-about-us-tab-menu-item' data-tab='lite_vs_premium'>
					<?php _e( 'Lite vs. Premium', 'ultimate-slider' ); ?>
				</li>

				<li class='ewd-us-about-us-tab-menu-item' data-tab='getting_started'>
					<?php _e( 'Getting Started', 'ultimate-slider' ); ?>
				</li>

				<li class='ewd-us-about-us-tab-menu-item' data-tab='suggest_feature'>
					<?php _e( 'Suggest a Feature', 'ultimate-slider' ); ?>
				</li>

			</ul>

			<div class='ewd-us-about-us-tab' data-tab='who_we_are'>

				<p>
					<strong>Founded in 2014, Etoile Web Design is a leading WordPress plugin development company. </strong>
					Privately owned and located in Canada, our growing business is expanding in size and scope. 
					We have more than 50,000 active users across the world, over 2,000,000 total downloads, and our client based is steadily increasing every day. 
					Our reliable WordPress plugins bring a tremendous amount of value to our users by offering them solutions that are designed to be simple to maintain and easy to use. 
					Our plugins, like the <a href='https://www.etoilewebdesign.com/plugins/ultimate-product-catalog/?utm_source=admin_about_us' target='_blank'>Ultimate Product Catalog</a>, <a href='https://www.etoilewebdesign.com/plugins/order-tracking/?utm_source=admin_about_us' target='_blank'>Order Status Tracking</a>, <a href='https://www.etoilewebdesign.com/plugins/ultimate-faq/?utm_source=admin_about_us' target='_blank'>Ultimate FAQs</a> and <a href='https://www.etoilewebdesign.com/plugins/ultimate-reviews/?utm_source=admin_about_us' target='_blank'>Ultimate Reviews</a> are rich in features, highly customizable and responsive. 
					We provide expert support to all of our customers and believe in being a part of their success stories.
				</p>

				<p>
					Our current team consists of web developers, marketing associates, digital designers and product support associates. 
					As a small business, we are able to offer our team flexible work schedules, significant autonomy and a challenging environment where creative people can flourish.
				</p>

			</div>

			<div class='ewd-us-about-us-tab ewd-us-hidden' data-tab='lite_vs_premium'>

				<p><?php _e( 'The premium version of the plugin comes with additional features that let you extend the functionality of the slider. These include slide transitions and animations, arrow and icon customization, lightbox and watermark options, and more.', 'ultimate-slider' ); ?></p>

				<p><?php _e( 'Turn on the included <strong>WooCommerce integration</strong> to automatically convert the images on your WooCommerce product pages into a slider. There are also options to automatically pull WooCommerce product images and data into a slider that can be placed anywhere on your site.', 'ultimate-slider' ); ?></p>

				<p><em><?php _e( 'The following table provides a comparison of the lite and premium versions.', 'ultimate-slider' ); ?></em></p>

				<div class='ewd-us-about-us-premium-table'>
					<div class='ewd-us-about-us-premium-table-head'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Feature', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Lite Version', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Premium Version', 'ultimate-slider' ); ?></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Create unlimited slides', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Add unlimited sliders to your site', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Gutenberg block and shortcode to add slider', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Drag and drop slide ordering', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Add YouTube videos add slides', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Autoplay, interval and delay time options', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Carousel mode', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Custom CSS styling option', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Slide navigation thumbnails and dots', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Automatically convert WooCommerce product images into sliders', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Automatically pull WooCommerce product images and data into any slide', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Multiple icon sets and styling options for the arrows', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Lightbox option to create scrolling galleries with the carousel feature', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Multiple slide transition effects', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Title animations for your slides', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Add watermarks to your slides', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
					<div class='ewd-us-about-us-premium-table-body'>
						<div class='ewd-us-about-us-premium-table-cell'><?php _e( 'Advanced styling options', 'ultimate-slider' ); ?></div>
						<div class='ewd-us-about-us-premium-table-cell'></div>
						<div class='ewd-us-about-us-premium-table-cell'><img src="<?php echo plugins_url( '../assets/img/dash-asset-checkmark.png', __FILE__ ); ?>"></div>
					</div>
				</div>

				<?php printf( __( '<a href="%s" target="_blank" class="ewd-us-about-us-tab-button ewd-us-about-us-tab-button-purchase">Buy Premium Version</a>', 'ultimate-slider' ), 'https://www.etoilewebdesign.com/license-payment/?Selected=US&Quantity=1&utm_source=admin_about_us' ); ?>
				
			</div>

			<div class='ewd-us-about-us-tab ewd-us-hidden' data-tab='getting_started'>

				<p><?php _e( 'The walk-though that ran when you first activated the plugin offers a quick way to get started with setting it up. If you would like to run through it again, just click the button below', 'ultimate-slider' ); ?></p>

				<?php printf( __( '<a href="%s" class="ewd-us-about-us-tab-button ewd-us-about-us-tab-button-walkthrough">Re-Run Walk-Through</a>', 'ultimate-slider' ), admin_url( '?page=ewd-us-getting-started' ) ); ?>

				<p><?php _e( 'We also have a series of video tutorials that cover the available settings as well as key features of the plugin.', 'ultimate-slider' ); ?></p>

				<?php printf( __( '<a href="%s" target="_blank" class="ewd-us-about-us-tab-button ewd-us-about-us-tab-button-youtube">YouTube Playlist</a>', 'ultimate-slider' ), 'https://www.youtube.com/playlist?list=PLEndQUuhlvSpW6V_RrWQ0Wxh_OFmu96Mm' ); ?>

				
			</div>

			<div class='ewd-us-about-us-tab ewd-us-hidden' data-tab='suggest_feature'>

				<div class='ewd-us-about-us-feature-suggestion'>

					<p><?php _e( 'You can use the form below to let us know about a feature suggestion you might have.', 'ultimate-slider' ); ?></p>

					<textarea placeholder="<?php _e( 'Please describe your feature idea...', 'ultimate-slider' ); ?>"></textarea>
					
					<br>
					
					<input type="email" name="feature_suggestion_email_address" placeholder="<?php _e( 'Email Address', 'ultimate-slider' ); ?>">
				
				</div>
				
				<div class='ewd-us-about-us-tab-button ewd-us-about-us-send-feature-suggestion'>Send Feature Suggestion</div>
				
			</div>

		</div>

	<?php }

	/**
	 * Sends the feature suggestions submitted via the About Us page
	 * @since 2.2.0
	 */
	public function send_feature_suggestion() {
		global $ewd_us_controller;
		
		if (
			! check_ajax_referer( 'ewd-us-admin-js', 'nonce' ) 
			|| 
			! current_user_can( 'manage_options' )
		) {
			ewdusHelper::admin_nopriv_ajax();
		}

		$headers = 'Content-type: text/html;charset=utf-8' . "\r\n";  
	    $feedback = sanitize_text_field( $_POST['feature_suggestion'] );
		$feedback .= '<br /><br />Email Address: ';
	  	$feedback .=  sanitize_email( $_POST['email_address'] );
	
	  	wp_mail( 'contact@etoilewebdesign.com', 'US Feature Suggestion', $feedback, $headers );
	
	  	die();
	} 

}
} // endif;
<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdusDashboard' ) ) {
/**
 * Class to handle plugin dashboard
 *
 * @since 2.0.0
 */
class ewdusDashboard {

	public $message;
	public $status = true;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_dashboard_to_menu' ), 99 );

		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_ajax_ewd_us_hide_upgrade_box', array($this, 'hide_upgrade_box') );
		add_action( 'wp_ajax_ewd_us_display_upgrade_box', array($this, 'display_upgrade_box') );
	}

	public function add_dashboard_to_menu() {
		global $menu, $submenu;

		add_submenu_page( 
			'edit.php?post_type=ultimate_slider', 
			'Dashboard', 
			'Dashboard', 
			'manage_options', 
			'ewd-us-dashboard', 
			array($this, 'display_dashboard_screen') 
		);

		// Create a new sub-menu in the order that we want
		$new_submenu = array();
		$menu_item_count = 3;

		if ( ! isset( $submenu['edit.php?post_type=ultimate_slider'] ) or  ! is_array($submenu['edit.php?post_type=ultimate_slider']) ) { return; }
		
		foreach ( $submenu['edit.php?post_type=ultimate_slider'] as $key => $sub_item ) {
			if ( $sub_item[0] == 'Dashboard' ) { $new_submenu[0] = $sub_item; }
			elseif ( $sub_item[0] == 'Settings' ) { $new_submenu[ sizeof($submenu) ] = $sub_item; }
			else {
				$new_submenu[$menu_item_count] = $sub_item;
				$menu_item_count++;
			}
		}
		ksort($new_submenu);
		
		$submenu['edit.php?post_type=ultimate_slider'] = $new_submenu;
		
		if ( isset( $dashboard_key ) ) {
			$submenu['edit.php?post_type=ultimate_slider'][0] = $submenu['edit.php?post_type=ultimate_slider'][$dashboard_key];
			unset($submenu['edit.php?post_type=ultimate_slider'][$dashboard_key]);
		}
	}

	// Enqueues the admin script so that our hacky sub-menu opening function can run
	public function enqueue_scripts() {
		global $admin_page_hooks;

		$currentScreen = get_current_screen();
		if ( $currentScreen->id == $admin_page_hooks['edit.php?post_type=ultimate_slider'] . '_page_ewd-us-dashboard' ) {
			wp_enqueue_style( 'ewd-us-admin-css', EWD_US_PLUGIN_URL . '/assets/css/ewd-us-admin.css', array(), EWD_US_VERSION );
			wp_enqueue_script( 'ewd-us-admin-js', EWD_US_PLUGIN_URL . '/assets/js/ewd-us-admin.js', array( 'jquery' ), EWD_US_VERSION, true );
		}
	}

	public function display_dashboard_screen() { 
		global $ewd_us_controller;

		$permission = $ewd_us_controller->permissions->check_permission( 'styling' );

		?>

		<div id="ewd-us-dashboard-content-area">

			<div id="ewd-us-dashboard-content-left">
		
				<?php if ( ! $permission or get_option("EWD_US_Trial_Happening") == "Yes" ) {
					$premium_info = '<div class="ewd-us-dashboard-new-widget-box ewd-widget-box-full">';
					$premium_info .= '<div class="ewd-us-dashboard-new-widget-box-top">';
					$premium_info .= sprintf( __( '<a href="%s" target="_blank">Visit our website</a> to learn how to upgrade to premium.'), 'https://www.etoilewebdesign.com/premium-upgrade-instructions/?utm_source=us_dashboard&utm_content=visit_our_site_link' );
					$premium_info .= '</div>';
					$premium_info .= '</div>';

					$premium_info = apply_filters( 'ewd_dashboard_top', $premium_info, 'US', 'https://www.etoilewebdesign.com/license-payment/?Selected=US&Quantity=1' );

					echo $premium_info;
				} ?>
		
				<div class="ewd-us-dashboard-new-widget-box ewd-widget-box-full" id="ewd-us-dashboard-support-widget-box">
					<div class="ewd-us-dashboard-new-widget-box-top">Get Support<span id="ewd-us-dash-mobile-support-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-us-dash-mobile-support-up-caret">&nbsp;&nbsp;&#9650;</span></div>
					<div class="ewd-us-dashboard-new-widget-box-bottom">
						<ul class="ewd-us-dashboard-support-widgets">
							<li>
								<a href="https://www.youtube.com/playlist?list=PLEndQUuhlvSpW6V_RrWQ0Wxh_OFmu96Mm" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-youtube.png', __FILE__ ); ?>">
									<div class="ewd-us-dashboard-support-widgets-text">YouTube Tutorials</div>
								</a>
							</li>
							<li>
								<a href="https://wordpress.org/plugins/ultimate-slider/#faq" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-faqs.png', __FILE__ ); ?>">
									<div class="ewd-us-dashboard-support-widgets-text">Plugin FAQs</div>
								</a>
							</li>
							<li>
								<a href="https://www.etoilewebdesign.com/support-center/?Plugin=US&Type=FAQs&utm_source=us_dashboard&utm_content=icons_documentation" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-documentation.png', __FILE__ ); ?>">
									<div class="ewd-us-dashboard-support-widgets-text">Documentation</div>
								</a>
							</li>
							<li>
								<a href="https://www.etoilewebdesign.com/support-center/?utm_source=us_dashboard&utm_content=icons_get_support" target="_blank">
									<img src="<?php echo plugins_url( '../assets/img/ewd-support-icon-forum.png', __FILE__ ); ?>">
									<div class="ewd-us-dashboard-support-widgets-text">Get Support</div>
								</a>
							</li>
						</ul>
					</div>
				</div>
		
				<?php /*
				<div class="ewd-us-dashboard-new-widget-box ewd-widget-box-full" id="ewd-us-dashboard-optional-table">
					<div class="ewd-us-dashboard-new-widget-box-top">Bookings Summary<span id="ewd-us-dash-optional-table-down-caret">&nbsp;&nbsp;&#9660;</span><span id="ewd-us-dash-optional-table-up-caret">&nbsp;&nbsp;&#9650;</span></div>
					<div class="ewd-us-dashboard-new-widget-box-bottom">
						<table class='ewd-us-overview-table wp-list-table widefat fixed striped posts'>
							<thead>
								<tr>
									<th><?php _e("Date", 'ultimate-slider'); ?></th>
									<th><?php _e("Party", 'ultimate-slider'); ?></th>
									<th><?php _e("Name", 'ultimate-slider'); ?></th>
									<th><?php _e("Status", 'ultimate-slider'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									require_once( EWD_US_PLUGIN_DIR . '/includes/Query.class.php' );
									$query = new rtbQuery( array() );
									$query->prepare_args();

									$bookings = $query->get_bookings();
		
									if (sizeOf($bookings) == 0) {echo "<tr><td colspan='4'>" . __("No bookings to display yet. Create a booking for it to be displayed here.", 'ultimate-slider') . "</td></tr>";}
									else {
										foreach ($bookings as $booking) { 
										?>

											<tr>
												<td><?php echo $booking->date; ?></td>
												<td><?php echo $booking->party; ?></td>
												<td><?php echo $booking->name; ?></td>
												<td><?php echo $booking->post_status; ?></td>
											</tr>
										<?php }
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				*/ ?>
		
				<?php /*<div class="ewd-us-dashboard-new-widget-box ewd-widget-box-full">
					<div class="ewd-us-dashboard-new-widget-box-top">What People Are Saying</div>
					<div class="ewd-us-dashboard-new-widget-box-bottom">
						<ul class="ewd-us-dashboard-testimonials">
							<?php $randomTestimonial = rand(0,2);
							if($randomTestimonial == 0){ ?>
								<li id="ewd-us-dashboard-testimonial-one">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="ewd-us-dashboard-testimonial-title">"Awesome. Just Awesome."</div>
									<div class="ewd-us-dashboard-testimonial-author">- @shizart</div>
									<div class="ewd-us-dashboard-testimonial-text">Thanks for this very well-made plugin. This works so well out of the box, I barely had to do ANYTHING to create an amazing FAQ accordion display... <a href="https://wordpress.org/support/topic/awesome-just-awesome-11/" target="_blank">read more</a></div>
								</li>
							<?php }
							if($randomTestimonial == 1){ ?>
								<li id="ewd-us-dashboard-testimonial-two">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="ewd-us-dashboard-testimonial-title">"Absolutely perfect with great support"</div>
									<div class="ewd-us-dashboard-testimonial-author">- @isaac85</div>
									<div class="ewd-us-dashboard-testimonial-text">I tried several different FAQ plugins and this is by far the prettiest and easiest to use... <a href="https://wordpress.org/support/topic/absolutely-perfect-with-great-support/" target="_blank">read more</a></div>
								</li>
							<?php }
							if($randomTestimonial == 2){ ?>
								<li id="ewd-us-dashboard-testimonial-three">
									<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
									<div class="ewd-us-dashboard-testimonial-title">"Perfect FAQ Plugin"</div>
									<div class="ewd-us-dashboard-testimonial-author">- @muti-wp</div>
									<div class="ewd-us-dashboard-testimonial-text">Works great! Easy to configure and to use. Thanks! <a href="https://wordpress.org/support/topic/perfect-faq-plugin/" target="_blank">read more</a></div>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div> */ ?>
		
				<?php /* if($hideReview != 'Yes' and $Ask_Review_Date < time()){ ?>
					<div class="ewd-us-dashboard-new-widget-box ewd-widget-box-one-third">
						<div class="ewd-us-dashboard-new-widget-box-top">Leave a review</div>
						<div class="ewd-us-dashboard-new-widget-box-bottom">
							<div class="ewd-us-dashboard-review-ask">
								<img src="<?php echo plugins_url( '../assets/img/dash-asset-stars.png', __FILE__ ); ?>">
								<div class="ewd-us-dashboard-review-ask-text">If you enjoy this plugin and have a minute, please consider leaving a 5-star review. Thank you!</div>
								<a href="https://wordpress.org/plugins/ultimate-faqs/#reviews" class="ewd-us-dashboard-review-ask-button">LEAVE A REVIEW</a>
								<form action="admin.php?page=EWD-UFAQ-Options" method="post">
									<input type="hidden" name="hide_ufaq_review_box_hidden" value="Yes">
									<input type="submit" name="hide_ufaq_review_box_submit" class="ewd-us-dashboard-review-ask-dismiss" value="I've already left a review">
								</form>
							</div>
						</div>
					</div>
				<?php } */ ?>
		
				<?php if ( ! $permission or get_option("EWD_US_Trial_Happening") == "Yes" ) { ?>
					<div class="ewd-us-dashboard-new-widget-box ewd-widget-box-full" id="ewd-us-dashboard-guarantee-widget-box">
						<div class="ewd-us-dashboard-new-widget-box-top">
							<div class="ewd-us-dashboard-guarantee">
								<div class="ewd-us-dashboard-guarantee-title">14-Day 100% Money-Back Guarantee</div>
								<div class="ewd-us-dashboard-guarantee-text">If you're not 100% satisfied with the premium version of our plugin - no problem. You have 14 days to receive a FULL REFUND. We're certain you won't need it, though.</div>
							</div>
						</div>
					</div>
				<?php } ?>
		
			</div> <!-- left -->
		
			<div id="ewd-us-dashboard-content-right">
		
				<?php if ( ! $permission or get_option("EWD_US_Trial_Happening") == "Yes" ) { ?>
					<div class="ewd-us-dashboard-new-widget-box ewd-widget-box-full" id="ewd-us-dashboard-get-premium-widget-box">
						<div class="ewd-us-dashboard-new-widget-box-top">Get Premium</div>

						<?php if ( get_option( "EWD_US_Trial_Happening" ) == "Yes" ) { do_action( 'ewd_trial_happening', 'US' ); } ?>

						<div class="ewd-us-dashboard-new-widget-box-bottom">
							<div class="ewd-us-dashboard-get-premium-widget-features-title"<?php echo ( ( get_option("EWD_US_Trial_Happening") == "Yes" ) ? "style='padding-top: 20px;'" : ""); ?>>GET FULL ACCESS WITH OUR PREMIUM VERSION AND GET:</div>
							<ul class="ewd-us-dashboard-get-premium-widget-features">
								<li>Integrated Lightbox Effect</li>
								<li>Advanced Styling Options</li>
								<li>Advanced Control Options</li>
								<li>WooCommerce integration</li>
								<li>Add Watermarks</li>
								<li>+ More</li>
							</ul>
							<a href="https://www.etoilewebdesign.com/license-payment/?Selected=US&Quantity=1&utm_source=us_dashboard&utm_content=sidebar_upgrade" class="ewd-us-dashboard-get-premium-widget-button" target="_blank">UPGRADE NOW</a>
							
							<?php if ( ! get_option("EWD_US_Trial_Happening") ) { 
								$trial_info = sprintf( __( '<a href="%s" target="_blank">Visit our website</a> to learn how to get a free 7-day trial of the premium plugin.'), 'https://www.etoilewebdesign.com/premium-upgrade-instructions/?utm_source=us_dashboard&utm_content=sidebar_visit_our_site_link' );		

								echo apply_filters( 'ewd_trial_button', $trial_info, 'US' );
							} ?>
				</div>
					</div>
				<?php } ?>
		
				<!-- <div class="ewd-us-dashboard-new-widget-box ewd-widget-box-full">
					<div class="ewd-us-dashboard-new-widget-box-top">Other Plugins by Etoile</div>
					<div class="ewd-us-dashboard-new-widget-box-bottom">
						<ul class="ewd-us-dashboard-other-plugins">
							<li>
								<a href="https://wordpress.org/plugins/ultimate-product-catalogue/" target="_blank"><img src="<?php echo plugins_url( '../images/ewd-upcp-icon.png', __FILE__ ); ?>"></a>
								<div class="ewd-us-dashboard-other-plugins-text">
									<div class="ewd-us-dashboard-other-plugins-title">Product Catalog</div>
									<div class="ewd-us-dashboard-other-plugins-blurb">Enables you to display your business's products in a clean and efficient manner.</div>
								</div>
							</li>
							<li>
								<a href="https://wordpress.org/plugins/ultimate-reviews/" target="_blank"><img src="<?php echo plugins_url( '../images/ewd-urp-icon.png', __FILE__ ); ?>"></a>
								<div class="ewd-us-dashboard-other-plugins-text">
									<div class="ewd-us-dashboard-other-plugins-title">Ultimate Reviews</div>
									<div class="ewd-us-dashboard-other-plugins-blurb">Let visitors submit reviews and display them right in the tabbed page layout!</div>
								</div>
							</li>
						</ul>
					</div>
				</div> -->
		
			</div> <!-- right -->	
		
		</div> <!-- us-dashboard-content-area -->
		
		<?php if ( ! $permission or get_option("EWD_US_Trial_Happening") == "Yes" ) { ?>
			<div id="ewd-us-dashboard-new-footer-one">
				<div class="ewd-us-dashboard-new-footer-one-inside">
					<div class="ewd-us-dashboard-new-footer-one-left">
						<div class="ewd-us-dashboard-new-footer-one-title">What's Included in Our Premium Version?</div>
						<ul class="ewd-us-dashboard-new-footer-one-benefits">
							<li>Integrated Lightbox Effect</li>
							<li>Advanced Styling Options</li>
							<li>Advanced Control Options</li>
							<li>WooCommerce integration</li>
							<li>Add Watermarks</li>
						</ul>
					</div>
					<div class="ewd-us-dashboard-new-footer-one-buttons">
						<a class="ewd-us-dashboard-new-upgrade-button" href="https://www.etoilewebdesign.com/license-payment/?Selected=US&Quantity=1&utm_source=us_dashboard&utm_content=footer_upgrade" target="_blank">UPGRADE NOW</a>
					</div>
				</div>
			</div> <!-- us-dashboard-new-footer-one -->
		<?php } ?>	
		<div id="ewd-us-dashboard-new-footer-two">
			<div class="ewd-us-dashboard-new-footer-two-inside">
				<img src="<?php echo plugins_url( '../assets/img/ewd-logo-white.png', __FILE__ ); ?>" class="ewd-us-dashboard-new-footer-two-icon">
				<div class="ewd-us-dashboard-new-footer-two-blurb">
					At Etoile Web Design, we build reliable, easy-to-use WordPress plugins with a modern look. Rich in features, highly customizable and responsive, plugins by Etoile Web Design can be used as out-of-the-box solutions and can also be adapted to your specific requirements.
				</div>
				<ul class="ewd-us-dashboard-new-footer-two-menu">
					<li>SOCIAL</li>
					<li><a href="https://www.facebook.com/EtoileWebDesign/" target="_blank">Facebook</a></li>
					<li><a href="https://twitter.com/EtoileWebDesign" target="_blank">Twitter</a></li>
					<li><a href="https://www.etoilewebdesign.com/category/blog/?utm_source=us_dashboard&utm_content=footer_blog" target="_blank">Blog</a></li>
				</ul>
				<ul class="ewd-us-dashboard-new-footer-two-menu">
					<li>SUPPORT</li>
					<li><a href="https://www.youtube.com/playlist?list=PLEndQUuhlvSpW6V_RrWQ0Wxh_OFmu96Mm" target="_blank">YouTube Tutorials</a></li>
					<li><a href="https://www.etoilewebdesign.com/support-center/?Plugin=US&Type=FAQs&utm_source=us_dashboard&utm_content=footer_documentation" target="_blank">Documentation</a></li>
					<li><a href="https://www.etoilewebdesign.com/support-center/?utm_source=us_dashboard&utm_content=footer_get_support" target="_blank">Get Support</a></li>
					<li><a href="https://wordpress.org/plugins/ultimate-slider/#faq" target="_blank">FAQs</a></li>
				</ul>
			</div>
		</div> <!-- ewd-us-dashboard-new-footer-two -->
		
	<?php }

	public function display_notice() {
		if ( $this->status ) {
			echo "<div class='updated'><p>" . $this->message . "</p></div>";
		}
		else {
			echo "<div class='error'><p>" . $this->message . "</p></div>";
		}
	}
}
} // endif

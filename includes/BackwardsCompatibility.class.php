<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdusBackwardsCompatibility' ) ) {
/**
 * Class to handle transforming the plugin settings from the 
 * previous style (individual options) to the new one (options array)
 *
 * @since 2.0.0
 */
class ewdusBackwardsCompatibility {

	public function __construct() {
		
		if ( empty( get_option( 'ewd-us-settings' ) ) and get_option( 'EWD_US_Full_Version' ) ) { $this->run_backwards_compat(); }
	}

	public function run_backwards_compat() {

		$settings = array(
			'custom-css' 								=> get_option( 'EWD_US_Custom_CSS' ),
			'autoplay-slideshow' 						=> get_option( 'EWD_US_Autoplay_Slideshow' ) == 'Yes' ? true : false,
			'autoplay-delay' 							=> get_option( 'EWD_US_Autoplay_Delay' ) . '_undefined',
			'autoplay-interval' 						=> get_option( 'EWD_US_Autoplay_Interval' ) . '_undefined',
			'autoplay-pause-hover'						=> get_option( 'EWD_US_Autoplay_Pause_Hover' ) == 'Yes' ? true : false,
			'transition-time'							=> get_option( 'EWD_US_Transition_Time' ) . '_undefined',
			'aspect-ratio'								=> get_option( 'EWD_US_Aspect_Ratio' ),
			'carousel'									=> get_option( 'EWD_US_Carousel' ) == 'Yes' ? true : false,
			'carousel-columns'							=> get_option( 'EWD_US_Carousel_Columns' ),
			'carousel-link-to-full'						=> get_option( 'EWD_US_Carousel_Link_To_Full' ) == 'Yes' ? true : false,
			'carousel-advance'							=> strtolower( get_option( 'EWD_US_Carousel_Advance' ) ),
			'show-tinymce'								=> get_option( 'EWD_US_Show_TinyMCE' ) == 'Yes' ? true : false,
			'timer-bar'									=> strtolower( get_option( 'EWD_US_Timer_Bar' ) ),
			'slide-indicators'							=> strtolower( get_option( 'EWD_US_Slide_Indicators' ) ),
			'link-action'								=> strtolower( get_option( 'EWD_US_Link_Action' ) ),
			'slide-transition-effect'					=> get_option( 'EWD_US_Slide_Transition_Effect' ),
			'wc-product-image-slider'					=> get_option( 'EWD_US_WC_Product_Image_Slider' ) == 'Yes' ? true : false,
			'mobile-aspect-ratio'						=> get_option( 'EWD_US_Mobile_Aspect_Ratio' ),
			'hide-from-slider'							=> get_option( 'EWD_US_Hide_From_Slider' ) ? get_option( 'EWD_US_Hide_From_Slider' ) : array(),
			'hide-on-mobile'							=> get_option( 'EWD_US_Hide_On_Mobile' ) ? get_option( 'EWD_US_Hide_On_Mobile' ) : array(),
			'mobile-link-to-full'						=> get_option( 'EWD_US_Mobile_Link_To_Full' ) == 'Yes' ? true : false,
			'title-animate'								=> strtolower( get_option( 'EWD_US_Title_Animate' ) ),
			'force-full-width'							=> get_option( 'EWD_US_Force_Full_Width' ) == 'Yes' ? true : false,
			'add-watermark'								=> get_option( 'EWD_US_Add_Watermark' ) == 'Yes' ? true : false,
			'lightbox'									=> get_option( 'EWD_US_Lightbox' ) == 'Yes' ? true : false,
			'styling-slide-title-font'					=> get_option( 'EWD_us_Slide_Title_Font' ),
			'styling-slide-title-font-size'				=> get_option( 'EWD_us_Slide_Title_Font_Size' ),
			'styling-slide-title-font-color'			=> get_option( 'EWD_us_Slide_Title_Font_Color' ),
			'styling-slide-text-font'					=> get_option( 'EWD_us_Slide_Text_Font' ),
			'styling-slide-text-font-size'				=> get_option( 'EWD_us_Slide_Text_Font_Size' ),
			'styling-slide-text-font-color'				=> get_option( 'EWD_us_Slide_Text_Font_Color' ),
			'styling-button-font'						=> get_option( 'EWD_us_Button_Font' ),
			'styling-button-font-size'					=> get_option( 'EWD_us_Button_Font_Size' ),
			'styling-button-background-color'			=> get_option( 'EWD_us_Button_Background_Color' ),			
			'styling-button-border-color'				=> get_option( 'EWD_us_Button_Border_Color' ),
			'styling-button-text-color'					=> get_option( 'EWD_us_Button_Text_Color' ),
			'styling-button-background-hover-color'		=> get_option( 'EWD_us_Button_Background_Hover_Color' ),
			'styling-button-border-hover-color'			=> get_option( 'EWD_us_Button_Border_Hover_Color' ),
			'styling-button-text-hover-color'			=> get_option( 'EWD_us_Button_Text_Hover_Color' ),
			'arrow'										=> get_option( 'EWD_us_Arrow' ) ? get_option( 'EWD_us_Arrow' )  : 'a',
			'arrow-background-shape'					=> strtolower( get_option( 'EWD_us_Arrow_Background_Shape' ) ),
			'styling-arrow-color'						=> get_option( 'EWD_us_Arrow_Color' ),
			'styling-arrow-font-size'					=> get_option( 'EWD_us_Arrow_Font_Size' ),
			'styling-arrow-background-color'			=> get_option( 'EWD_us_Arrow_Background_Color' ),
			'styling-arrow-background-size'				=> get_option( 'EWD_us_Arrow_Background_Size' ),
			'styling-clickable-area-background-color'	=> get_option( 'EWD_us_Clickable_Area_Background_Color' ),
			'styling-clickable-area-size'				=> get_option( 'EWD_us_Clickable_Area_Size' ),
			'styling-arrow-line-height'					=> get_option( 'EWD_us_Arrow_Line_Height' )
		);

		add_option( 'ewd-us-review-ask-time', get_option( 'EWD_US_Ask_Review_Date' ) );
		add_option( 'ewd-us-installation-time', get_option( 'EWD_US_Install_Time' ) );

		update_option( 'ewd-us-permission-level', get_option( 'EWD_US_Full_Version' ) == 'Yes' ? 2 : 1 );
		
		update_option( 'ewd-us-settings', $settings );
	}
}

}
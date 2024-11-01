<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ewdusBlocks' ) ) {
/**
 * Class to handle plugin Gutenberg blocks
 *
 * @since 2.0.0
 */
class ewdusBlocks {

	public function __construct() {

		add_action( 'init', array( $this, 'add_slider_block' ) );
		
		add_filter( 'block_categories_all', array( $this, 'add_block_category' ) );
	}

	/**
	 * Add the Gutenberg block to the list of available blocks
	 * @since 2.0.0
	 */
	public function add_slider_block() {

		if ( ! function_exists( 'render_block_core_block' ) ) { return; }

		$this->enqueue_assets();   

		$args = array(
			'attributes'      => array(
				'category' => array(
					'type' => 'string',
				),
				'posts' => array(
					'type' => 'string',
				),
				'slider_type' => array(
					'type' => 'string',
				),
				'carousel_mode' => array(
					'type' => 'string',
				),
				'slide_indicators' => array(
					'type' => 'string',
				),
			),
			'editor_script'   => 'ewd-us-blocks-js',
			'editor_style'  => 'ewd-us-blocks-css',
			'render_callback' => 'ewd_us_slider_shortcode',
		);

		register_block_type( 'ultimate-slider/ewd-us-slider-block', $args );
	}

	/**
	 * Create a new category of blocks to hold our block
	 * @since 2.0.0
	 */
	public function add_block_category( $categories ) {
		
		$categories[] = array(
			'slug'  => 'ewd-us-blocks',
			'title' => __( 'Ultimate Slider', 'ultimate-slider' ),
		);

		return $categories;
	}

	/**
	 * Register the necessary JS and CSS to display the block in the editor
	 * @since 2.0.0
	 */
	public function enqueue_assets() {

		wp_register_script( 'ewd-us-blocks-js', EWD_US_PLUGIN_URL . '/assets/js/ewd-us-blocks.js', array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ), EWD_US_VERSION );
		wp_register_style( 'ewd-us-blocks-css', EWD_US_PLUGIN_URL . '/assets/css/ewd-us-blocks.css', array( 'wp-edit-blocks' ), EWD_US_VERSION );
	}
}

}
<?php

/**
 * Class to replace the main WooCommerce product image with a slider
 * that contains all of the product images
 */

if ( !defined( 'ABSPATH' ) )
	exit;

class ewdusWooCommerceIntegration {


	public function __construct() {

		add_filter( 'woocommerce_locate_template', array( $this, 'add_plugin_template' ), 1, 3 );
	}

	public function add_plugin_template( $template, $template_name, $template_path ) {
		global $woocommerce;

     	$_template = $template;

     	if ( ! $template_path ) {

        	$template_path = $woocommerce->template_url;
     	}
 
     	$plugin_path  = EWD_US_PLUGIN_DIR  . '/woocommerce-templates/';

    	if ( file_exists( $plugin_path . $template_name ) ) {
    	
    		$template = $plugin_path . $template_name;
    	}
 
		if ( ! $template  ) {

			$paths = array(
				$template_path . $template_name,
				$template_name
			);

			$template = locate_template( $paths );
		}
 

   		return $template;
	}
}
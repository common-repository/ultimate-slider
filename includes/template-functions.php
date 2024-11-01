<?php

/**
 * Create a shortcode to display a slider
 * @since 2.0.0
 */
function ewd_us_slider_shortcode( $atts ) {

	// Define shortcode attributes
	$slider_atts = array(
		'slider_type' => null,
		'post__in_string' => '',
		'posts' => -1,
		'category' => false,
		'carousel' => false,
		'slide_indicators' => null,
		'timer_bar' => null
	);

	// Create filter so addons can modify the accepted attributes
	$slider_atts = apply_filters( 'ewd_us_slider_shortcode_atts', $slider_atts );

	// Extract the shortcode attributes
	$args = shortcode_atts( $slider_atts, $atts );

	// Render menu
	ewd_us_load_view_files();
	$slider = new ewdusViewSlider( $args );

	return $slider->render();
}
add_shortcode( 'ultimate-slider', 'ewd_us_slider_shortcode' );

function ewd_us_load_view_files() {

	$files = array(
		EWD_US_PLUGIN_DIR . '/views/Base.class.php' // This will load all default classes
	);

	$files = apply_filters( 'ewd_us_load_view_files', $files );

	foreach( $files as $file ) {
		require_once( $file );
	}

}

function ewd_us_create_watermarked_image( $image_url ) {
	
	$upload_dir = wp_upload_dir();
	$plugin_upload_path = $upload_dir['basedir'] . "/ultimate-slider/";

	wp_mkdir_p( $plugin_upload_path );

	$path_parts = pathinfo( $image_url );

	$image_file_path = $plugin_upload_path . $path_parts['filename'] . '_watermarked.png';

	$aspect_ratio_fraction = ewd_us_get_aspect_ratio_fraction();

	$image_string = file_get_contents( $image_url );

	$stamp = imagecreatefrompng( EWD_US_PLUGIN_URL . '/assets/img/star-watermark.png' );
	$image = imagecreatefromstring( $image_string );

	$height = intval( imagesy( $image ) );
	$width = intval( imagesx( $image ) );

	if ( $width < 2500 ) {
		$stamp_width = round( $width / 10 );
		$stamp_height = $stamp_width;
		$scaled_stamp = imagescale( $stamp, $stamp_width, $stamp_height );
	}
	else {
		$scaled_stamp = $stamp;
	}

	$margin_right = 10;
	$margin_bottom = intval( $height - ( $width * $aspect_ratio_fraction ) + 10 );
	$sx = intval( imagesx( $scaled_stamp ) );
	$sy = intval( imagesy( $scaled_stamp ) );
	$posx = $width -$sx - $margin_right;
	$posy = $height - $sy - $margin_bottom;
	 
	imagecopy( $image, $scaled_stamp, $posx, $posy, 0, 0, $sx, $sy );

	imagepng( $image, $image_file_path );

	imagedestroy( $image );
}

function ewd_us_get_aspect_ratio_fraction() {
	global $ewd_us_controller;

	$aspect_ratio = $ewd_us_controller->settings->get_setting( 'aspect-ratio' );
	
	if ($aspect_ratio == "3_1") {$aspect_ratio_fraction = .333333333;}
	elseif ($aspect_ratio == "2_1") {$aspect_ratio_fraction = .5;}
	elseif ($aspect_ratio == "16_9") {$aspect_ratio_fraction = .5625;}
	elseif ($aspect_ratio == "3_2") {$aspect_ratio_fraction = .666666666;}
	elseif ($aspect_ratio == "4_3") {$aspect_ratio_fraction = .75;}
	elseif ($aspect_ratio == "1_1") {$aspect_ratio_fraction = 1;}
	else {$aspect_ratio_fraction = .444444444;}

	return $aspect_ratio_fraction;
}

if ( ! function_exists( 'ewd_us_get_aspect_fraction' ) ) {
function ewd_us_get_aspect_fraction( $aspect_ratio ) {

    if ($aspect_ratio == "3_1") {$aspect_fraction = .333333333;}
    if ($aspect_ratio == "16_7") {$aspect_fraction = .4375;}
    if ($aspect_ratio == "2_1") {$aspect_fraction = .5;}
    if ($aspect_ratio == "16_9") {$aspect_fraction = .5625;}
    if ($aspect_ratio == "3_2") {$aspect_fraction = .666666667;}
    if ($aspect_ratio == "4_3") {$aspect_fraction = .75;}
    if ($aspect_ratio == "1_1") {$aspect_fraction = 1;}

    return $aspect_fraction;
}
}

if ( ! function_exists( 'ewd_hex_to_rgb' ) ) {
function ewd_hex_to_rgb( $hex ) {

	$hex = str_replace("#", "", $hex);

	// return if the string isn't a color code
	if ( strlen( $hex ) !== 3 and strlen( $hex ) !== 6 ) { return '0,0,0'; }

	if(strlen($hex) == 3) {
		$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
		$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
		$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
	} else {
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
	}

	$rgb = $r . ", " . $g . ", " . $b;
  
	return $rgb;
}
}

if ( ! function_exists( 'ewd_format_classes' ) ) {
function ewd_format_classes( $classes ) {

	if ( count( $classes ) ) {
		return ' class="' . join( ' ', $classes ) . '"';
	}
}
}

if ( ! function_exists( 'ewd_add_frontend_ajax_url' ) ) {
function ewd_add_frontend_ajax_url() { ?>
    
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
<?php }
}
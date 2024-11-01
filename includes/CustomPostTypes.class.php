<?php
/**
 * Class to handle all custom post type definitions for Ultimate Slider
 */

if ( !defined( 'ABSPATH' ) )
	exit;

if ( !class_exists( 'ewdusCustomPostTypes' ) ) {
class ewdusCustomPostTypes {

	public $nonce;

	public function __construct() {

		// Call when plugin is initialized on every page load
		add_action( 'admin_init', 		array( $this, 'create_nonce' ) );
		add_action( 'init', 			array( $this, 'load_cpts' ) );

		// Handle metaboxes
		add_action( 'add_meta_boxes', 	array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', 		array( $this, 'save_meta' ) );

		// Add columns and filters to the admin list of slides
		add_action( 'restrict_manage_posts', 							array( $this, 'filter_by_category' ) );
		add_filter( 'parse_query', 										array( $this, 'convert_slide_category_to_taxonomy_term_in_query' ) );
		add_filter( 'manage_ultimate_slider_posts_columns' , 			array( $this, 'slide_table_columns_display_order' ) );
		add_action( 'manage_ultimate_slider_posts_custom_column', 		array( $this, 'display_slide_columns_content' ), 10, 2 );
		add_filter( 'manage_edit-ultimate_slider_sortable_columns', 	array( $this, 'register_post_column_sortables' ) );
		add_filter( 'posts_clauses', 									array( $this, 'orderby_categories_column' ), 10, 2 );
		add_filter( 'manage_edit-ultimate_slider_categories_columns', 	array( $this, 'register_slide_category_table_columns' ) );
		add_filter( 'manage_edit-ultimate_slider_categories_columns', 	array( $this, 'slide_categories_table_columns_display_order' ) );
		add_filter( 'manage_ultimate_slider_categories_custom_column', 	array( $this, 'display_category_columns_content' ), 10, 3 );
		add_action( 'pre_get_posts',									array( $this, 'orderby_order_column' ) );

		add_action( 'wp_ajax_ewd_us_get_post_ids', array( $this, 'get_all_post_ids' ) );
		add_action( 'wp_ajax_ewd_us_slides_update_order', array( $this, 'update_slides_order' ) );
	}

	/**
	 * Initialize custom post types
	 * @since 2.0.0
	 */
	public function load_cpts() {
		global $ewd_us_controller;

		// Define the slide custom post type
		$args = array(
			'labels' => array(
				'name' 					=> __( 'Ultimate Slider',           'ultimate-slider' ),
				'singular_name' 		=> __( 'Slide',                   	'ultimate-slider' ),
				'menu_name'         	=> __( 'Ultimate Slider',          	'ultimate-slider' ),
				'name_admin_bar'    	=> __( 'Slides',                   	'ultimate-slider' ),
				'add_new'           	=> __( 'Add New',                 	'ultimate-slider' ),
				'add_new_item' 			=> __( 'Add New Slide',            	'ultimate-slider' ),
				'edit_item'         	=> __( 'Edit Slide',               	'ultimate-slider' ),
				'new_item'          	=> __( 'New Slide',                	'ultimate-slider' ),
				'view_item'         	=> __( 'View Slide',               	'ultimate-slider' ),
				'search_items'      	=> __( 'Search Slides',            	'ultimate-slider' ),
				'not_found'         	=> __( 'No slides found',          	'ultimate-slider' ),
				'not_found_in_trash'	=> __( 'No slides found in trash', 	'ultimate-slider' ),
				'all_items'         	=> __( 'All Slides',               	'ultimate-slider' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_icon' => 'dashicons-slides',
			'rewrite' => array( 
				'slug' => 'ultimate-slider' 
			),
			'supports' => array(
				'title', 
				'editor', 
				'thumbnail', 
				'revisions', 
				'page-attributes'
			),
			'show_in_rest' => true,
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_us_slider_args', $args );

		// Add an action so addons can hook in before the post type is registered
		do_action( 'ewd_us_slider_pre_register' );

		// Register the post type
		register_post_type( EWD_US_SLIDER_POST_TYPE, $args );

		// Add an action so addons can hook in after the post type is registered
		do_action( 'ewd_us_slider_post_register' );


		// Define the slide category taxonomy
		$args = array(
			'labels' => array(
				'name' 				=> __( 'Slider Categories',			'ultimate-slider' ),
				'singular_name' 	=> __( 'Slider Category',			'ultimate-slider' ),
				'search_items' 		=> __( 'Search Slider Categories', 	'ultimate-slider' ),
				'all_items' 		=> __( 'All Slider Categories', 	'ultimate-slider' ),
				'parent_item' 		=> __( 'Parent Slider Category', 	'ultimate-slider' ),
				'parent_item_colon' => __( 'Parent Slider Category:', 	'ultimate-slider' ),
				'edit_item' 		=> __( 'Edit Slider Category', 		'ultimate-slider' ),
				'update_item' 		=> __( 'Update Slider Category', 	'ultimate-slider' ),
				'add_new_item' 		=> __( 'Add New Slider Category', 	'ultimate-slider' ),
				'new_item_name' 	=> __( 'New Slider Category Name', 	'ultimate-slider' ),
				'menu_name' 		=> __( 'Slider Categories', 		'ultimate-slider' ),
            ),
			'public' => true,
            'hierarchical' => true,
            'show_in_rest' => true
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_us_slider_category_args', $args );

		register_taxonomy( EWD_US_SLIDER_CATEGORY_TAXONOMY, EWD_US_SLIDER_POST_TYPE, $args );


		// Define the slide tag taxonomy
		$args = array(
			'labels' => array(
                'name' 			=> __( 'Slider Tags',	'ultimate-slider' ),
                'singular_name' => __( 'Slider Tag',	'ultimate-slider' ),
            ),
			'public' => true,
            'hierarchical' => false,
            'show_in_rest' => true
		);

		// Create filter so addons can modify the arguments
		$args = apply_filters( 'ewd_us_slider_tag_args', $args );

		register_taxonomy( EWD_US_SLIDER_TAG_TAXONOMY, EWD_US_SLIDER_POST_TYPE, $args );
	}

	/**
	 * Generate a nonce for secure saving of metadata
	 * @since 2.0.0
	 */
	public function create_nonce() {

		$this->nonce = wp_create_nonce( basename( __FILE__ ) );
	}

	/**
	 * Add in new columns for the ultimate_slider type
	 * @since 2.0.0
	 */
	public function add_meta_boxes() {

		$meta_boxes = array(

			// Add in the slide meta information
			'slide_meta' => array (
				'id'		=>	'slide_meta',
				'title'		=> esc_html__( 'Slide Options', 'ultimate-slider' ),
				'callback'	=> array( $this, 'show_slide_meta' ),
				'post_type'	=> EWD_US_SLIDER_POST_TYPE,
				'context'	=> 'normal',
				'priority'	=> 'high'
			),

			// Add in a link to the documentation for the plugin
			'us_meta_need_help' => array (
				'id'		=>	'ewd_us_meta_need_help',
				'title'		=> esc_html__( 'Need Help?', 'ultimate-slider' ),
				'callback'	=> array( $this, 'show_need_help_meta' ),
				'post_type'	=> EWD_US_SLIDER_POST_TYPE,
				'context'	=> 'side',
				'priority'	=> 'high'
			),
		);

		// Create filter so addons can modify the metaboxes
		$meta_boxes = apply_filters( 'ewd_us_meta_boxes', $meta_boxes );

		// Create the metaboxes
		foreach ( $meta_boxes as $meta_box ) {
			add_meta_box(
				$meta_box['id'],
				$meta_box['title'],
				$meta_box['callback'],
				$meta_box['post_type'],
				$meta_box['context'],
				$meta_box['priority']
			);
		}
	}

	/**
	 * Add in a link to the plugin documentation
	 * @since 2.0.0
	 */
	public function show_slide_meta( $post ) { 
		global $ewd_us_controller;

	
		$content_type = get_post_meta( $post->ID, "EWD_US_Content_Type", true );
		$upcp_product_id = get_post_meta( $post->ID, "EWD_US_UPCP_Product_ID", true );
		$wc_product_id = get_post_meta( $post->ID, "EWD_US_WC_Product_ID", true );
		$max_title_chars = get_post_meta( $post->ID, "EWD_US_Max_Title_Chars", true );
		$max_body_chars = get_post_meta( $post->ID, "EWD_US_Max_Body_Chars", true );
		$image_type = get_post_meta( $post->ID, "EWD_US_Image_Type", true );
		$youtube_url = get_post_meta( $post->ID, "EWD_US_YouTube_URL", true );
		$buttons = is_array( get_post_meta( $post->ID, "EWD_US_Buttons", true ) ) ? get_post_meta( $post->ID, "EWD_US_Buttons", true ) : array();
	
		$upcp_products = post_type_exists( 'upcp_product' ) ? get_posts( array( 'post_type' => 'upcp_product', 'posts_per_page' => -1 ) ) : array();
		$wc_products = post_type_exists( 'product' ) ? get_posts( array( 'post_type' => 'product', 'posts_per_page' => -1 ) ) : array();
	
		$post_links = $this->get_all_post_ids( 'objects' );

		?>
	
		<input type="hidden" name="ewd_us_nonce" value="<?php echo $this->nonce; ?>">
	
		<div class='ewd-us-meta-menu'>

			<div class='ewd-us-meta-menu-item meta-menu-tab-active' id='Menu_Content'><?php _e( 'Content', 'ultimate-slider' ); ?></div>
			<div class='ewd-us-meta-menu-item' id='Menu_Buttons'><?php _e( 'Buttons', 'ultimate-slider' ); ?></div>
			<div class='ewd-us-meta-menu-item' id='Menu_Images'><?php _e( 'Image', 'ultimate-slider' ); ?></div>

		</div>
	
		<div class='ewd-us-meta-body' id='Body_Content'>

			<h3><?php _e( 'Content Type', 'ultimate-slider' ); ?></h3>

			<div class='ewd-us-meta-option-radio'>
				<input type='radio' name='content_type' value='current_post' <?php echo ( ( $content_type == 'current_post' or $content_type == '' ) ? 'checked' : '' ); ?> />
			</div>

			<div class='ewd-us-meta-option-explanation'>
				<?php _e( 'Use this post\'s content', 'ultimate-slider' ); ?>
			</div>

			<div class='ewd-us-meta-option-radio'>
				<input type='radio' name='content_type' value='upcp_product' <?php echo ( $content_type == 'upcp_product' ? 'checked' : '' ); ?> <?php echo ( empty( $upcp_products ) ? 'disabled' : '' ); ?> />
			</div>
		
			<div class='ewd-us-meta-option-explanation'>
				<?php _e( 'Use UPCP product content', 'ultimate-slider' ); ?>
				<?php if (! empty( $upcp_products ) ) { ?>

					<select name='upcp_products'>
						<?php foreach ( $upcp_products as $product ) { ?>

							<option value='<?php echo $product->ID; ?>' <?php echo ( ( $content_type == "upcp_product" and $product->ID == $upcp_product_id ) ? 'selected' : '' ); ?> >
								<?php echo esc_html( $product->post_title ); ?>
							</option>
						<?php } ?>
					</select>
				<?php } ?>
			</div>
			
			<div class='ewd-us-meta-option-radio'>
				<input type='radio' name='content_type' value='woocommerce_product' <?php echo ( $content_type == 'woocommerce_product' ? 'checked' : '' ); ?> <?php echo ( empty( $wc_products ) ? 'disabled' : '' ); ?> />
			</div>

			<div class='ewd-us-meta-option-explanation'>
				<?php echo _e( 'Use WooCommerce product content', 'ultimate-slider' ); ?>
				<?php if ( ! empty( $wc_products ) ) { ?>
					
					<select name='wc_products'>
						<?php foreach ($wc_products as $product) { ?>

							<option value='<?php echo $product->ID; ?>' <?php echo ( ( $content_type == "woocommerce_product" and $product->ID == $wc_product_id ) ? 'selected' : '' ); ?> >
								<?php echo esc_html( $product->post_title ); ?>
							</option>
						<?php } ?>
					</select>
				<?php } ?>
			</div>
	
			<div class='ewd-us-meta-divider'></div>
	
			<div class='ewd-us-meta-sinle-line'>
				<?php _e( 'Max Title Characters', 'ultimate-slider' ); ?>: 
				<input type='text' name='max_title_chars' value='<?php echo esc_attr( $max_title_chars ); ?>' />
			</div>

			<div class='ewd-us-meta-sinle-line'>
				<?php _e( 'Max Body Characters', 'ultimate-slider' ); ?>:
				<input type='text' name='max_body_chars' value='<?php esc_attr( $max_body_chars ); ?>' />
			</div>
		</div>
	
		<div class='ewd-us-meta-body ewd-us-hidden' id='Body_Buttons'>

			<h3><?php _e( 'Buttons', 'ultimate-slider' ); ?></h3>

			<table id='ewd-us-buttons-list-table'>
				<tr>
					<th></th>
					<th><?php _e( 'Text', 'ultimate-slider' ); ?></th>
					<th><?php _e( 'Link Type', 'ultimate-slider' ); ?></th>
					<th><?php _e( 'Custom Link', 'ultimate-slider' ); ?></th>
				</tr>
		
				<?php foreach ( $buttons as $count => $button ) { ?>
					<tr id='ewd-us-button-list-item-<?php echo $count; ?>'>
						<td>
							<a class='ewd-us-delete-button-list-item' data-buttonid='<?php echo $count; ?>'><?php _e( 'Delete', 'ultimate-slider' ); ?></a>
						</td>
						<td>
							<input type='text' name='Button_List_<?php echo $count; ?>_Text' value='<?php echo esc_attr( $button['Text'] ); ?>'/>
						</td>
						<td>
							<select name='Button_List_<?php echo $count; ?>_Post_ID' class='ewd-us-post-select' id='ewd-us-post-select-<?php echo $count; ?>'>
								<option value='0'><?php _e( 'Custom Link', 'ultimate-slider' ); ?></option>
								<?php foreach ($post_links as $post) { ?>
									
									<option value='<?php echo esc_attr( $post->ID ); ?>' <?php echo ( $post->ID == $button['Post_ID'] ? 'selected' : '' ); ?> >
										<?php echo esc_html( $post->post_title ); ?>
									</option>
								<?php } ?>
							</select>
						</td>
						<td>
							<input type='text' name='Button_List_<?php echo $count; ?>_Custom_Link' value='<?php echo esc_attr( $button['Custom_Link'] ); ?>' id='ewd-us-post-link-<?php echo $count; ?>' />
						</td>
					</tr>
				<?php } ?> 
				
				<tr>
					<td colspan='4'>
						<a class='ewd-us-add-button-list-item' data-nextid='<?php echo sizeof( $buttons ); ?>'><?php _e( 'Add', 'ultimate-slider' ); ?></a>
					</td>
				</tr>
			</table>

		</div>
	
		<div class='ewd-us-meta-body ewd-us-hidden' id='Body_Images'>

			<h3><?php _e( 'Image Options', 'ultimate-slider' ); ?></h3>

			<div class='ewd-us-meta-option-radio'>
				<input type='radio' class='ewd-us-image-radio' name='use_image' value='featured' <?php echo ( ( $image_type == 'featured' or $image_type == '' ) ? 'checked' : '' ); ?> />
			</div>

			<div class='ewd-us-meta-option-explanation'>
				<?php _e( 'Use post\'s featured image', 'ultimate-slider' ); ?>
			</div>

			<?php if ( $ewd_us_controller->permissions->check_permission( 'youtube' ) ) { ?>
				<div class='ewd-us-meta-option-radio'>
					<input type='radio' class='ewd-us-image-radio' name='use_image' value='youtube_video' <?php echo ( $image_type == 'youtube_video' ? 'checked' : '' ); ?> />
				</div>
				<div class='ewd-us-meta-option-explanation'>
					<?php _e( 'Use YouTube URL', 'ultimate-slider' ); ?>:
				</div>
				<div class='ewd-us-meta-option-radio'>
					<input type='text' id='ewd-us-youtube-url' name='youtube_url' value='<?php echo esc_attr( $youtube_url ); ?>' <?php echo ( $image_type != 'youtube_video' ? 'disabled' : '' ); ?> />
				</div>
			<?php } else { ?>
				<div class='ewd-us-upgrade'>
					<a href="http://www.etoilewebdesign.com/plugins/ultimate-slider/"><?php _e( 'Upgrade to the full version', 'ultimate-slider' ); ?></a> <?php _e( 'to be create YouTube video slides.', 'ultimate-slider' ); ?>
				</div>
			<?php } ?>
		</div>
	
		<div class='ewd-us-clear'></div>

	<?php } 

	/**
	 * Add in a link to the plugin documentation
	 * @since 2.0.0
	 */
	public function show_need_help_meta() { ?>
    
    	<div class='ewd-us-need-help-box'>
    		<div class='ewd-us-need-help-text'>Visit our Support Center for documentation and tutorials</div>
    	    <a class='ewd-us-need-help-button' href='https://www.etoilewebdesign.com/support-center/?Plugin=US' target='_blank'>GET SUPPORT</a>
    	</div>

	<?php }

	/**
	 * Save the metabox data for each slide
	 * @since 2.0.0
	 */
	public function save_meta( $post_id ) {
		global $ewd_us_controller;

		// Verify nonce
		if ( ! isset( $_POST['ewd_us_nonce'] ) || ! wp_verify_nonce( $_POST['ewd_us_nonce'], basename( __FILE__ ) ) ) {

			return $post_id;
		}

		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {

			return $post_id;
		}

		// Check permissions
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, 'EWD_US_Content_Type', sanitize_text_field( $_POST['content_type'] ) );
		if ( $_POST['content_type'] == 'upcp_product' ) { update_post_meta( $post_id, 'EWD_US_UPCP_Product_ID', intval( $_POST['upcp_products'] ) ); }
		if ( $_POST['content_type'] == 'woocommerce_product' ) { update_post_meta( $post_id, 'EWD_US_WC_Product_ID', intval( $_POST['wc_products'] ) ); }
		update_post_meta( $post_id, 'EWD_US_Max_Title_Chars', sanitize_text_field( $_POST['max_title_chars'] ) );
		update_post_meta( $post_id, 'EWD_US_Max_Body_Chars', sanitize_text_field( $_POST['max_body_chars'] ) );
	
		$counter = 0;
		$buttons = array();
		while ( $counter < 30 ) {
			
			if ( isset( $_POST['Button_List_' . $counter . '_Text'] ) ) {
				
				$prefix = 'Button_List_' . $counter;
				$button = array();
			
				$button['Text'] = sanitize_text_field( $_POST[$prefix . '_Text'] );
				$button['Post_ID'] = sanitize_text_field( $_POST[$prefix . '_Post_ID'] );
				$button['Custom_Link'] = esc_url_raw( $_POST[$prefix . '_Custom_Link'] );
	
				$buttons[] = $button; 
			}
			$counter++;
		}
		update_post_meta( $post_id, 'EWD_US_Buttons', $buttons );
	
		update_post_meta( $post_id, 'EWD_US_Image_Type', sanitize_text_field( $_POST['use_image'] ) );
		if ( ! empty( $_POST['youtube_url'] ) ) { update_post_meta( $post_id, 'EWD_US_YouTube_URL', sanitize_url( $_POST['youtube_url'] ) ); }
	
		if ( get_post_meta($post_id, "EWD_US_Slide_Order", true) == "" ) { update_post_meta( $post_id, "EWD_US_Slide_Order", 999 ); }
	}

	/**
	 * Set display order of slide table columns
	 * @since 2.0.0
	 */
	public function slide_table_columns_display_order( $columns ) {
		
		return array(
			'cb' 			=> __( 'Select', 'ultimate-slider' ),
			'us_thumbnail' 	=> __( 'Slide', 'ultimate-slider' ),
			'title' 		=> __( 'Title', 'ultimate-slider' ),
			'us_categories' => __( 'Categories', 'ultimate-slider' ),
			'us_menu_order' => __( 'Order', 'ultimate-slider' ),
			'date' 			=> __( 'Date', 'ultimate-slider' )
		);
	}

	/**
	 * Set the content for the thumbnail and category columns
	 * @since 2.0.0
	 */
	public function display_slide_columns_content ( $column_name, $post_id ) {
		
		if ( $column_name == 'us_categories' ) {

			echo $this->get_slide_categories( $post_id );
		}

		if ( $column_name == 'us_thumbnail' ) {

			$slide_image_source = $this->get_slide_image_source( $post_id );
			echo '<img src="' . $slide_image_source . '" class="ewd-us-admin-table-thumbnail" />';
		}

		if ( $column_name == 'us_menu_order' ) {
			
			echo get_post_meta( $post_id, "EWD_US_Slide_Order", true );
		}
	}

	/**
	 * Register the categories column as being sortable
	 * @since 2.0.0
	 */
	public function register_post_column_sortables( $column ) {
	    
	    $column['us_categories'] = 'us_categories';
	    
	    return $column;
	}

	/**
	 * Get the image source for a slide
	 * @since 2.0.0
	 */
	public function get_slide_image_source( $post_id ) {

		$content_type 		= get_post_meta( $post_id, "EWD_US_Content_Type", true );
		$upcp_product_id 	= get_post_meta( $post_id, "EWD_US_UPCP_Product_ID", true );
	
		$image_url = '';

		if ( $content_type == 'upcp_product' and class_exists( 'UPCP_Product' ) ) {

			$product = new UPCP_Product( array( 'ID' => $upcp_product_id ) );
			$image_url = $product->Get_Field_Value( 'Item_Photo_URL' );
		}
		else {

			$post_thumbnail_id = get_post_thumbnail_id( $post_id );
			$image_url = wp_get_attachment_url( $post_thumbnail_id );
		}
	
		return $image_url;
	}

	/**
	 * Adjust the wp_query if the orderby clause is us_categories
	 * @since 2.0.0
	 */
	public function orderby_categories_column( $clauses, $wp_query ) {
		global $wpdb;

		if ( isset( $wp_query->query['orderby'] ) and $wp_query->query['orderby'] == 'us_categories' ) {
			
			$clauses['join'] .= <<<SQL
				LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
				LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
				LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;

			$clauses['where']  .= " AND (taxonomy = 'ultimate_slider_categories' OR taxonomy IS NULL) ";
			$clauses['groupby'] = " object_id ";
			$clauses['orderby'] = " GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
			
			if ( strtoupper( $wp_query->get( 'order' ) ) == 'ASC' ) {
			    $clauses['orderby'] .= 'ASC';
			} else {
			    $clauses['orderby'] .= 'DESC';
			}
		}

		return $clauses;
	}

	/**
	 * Retrieve a comma separated list of ultimate_slider_categories for a slide
	 * @since 2.0.0
	 */
	public function get_slide_categories( $post_id ) {
		
		return get_the_term_list( $post_id, EWD_US_SLIDER_CATEGORY_TAXONOMY, '', ', ' ) . PHP_EOL;
	}

	/**
	 * Allow filtering by ultimate_slider_category
	 * @since 2.0.0
	 */
	public function filter_by_category( ) {
	    global $typenow;
	    global $wp_query;
	    
	    if ( $typenow == 'ultimate_slider' ) {

	        $taxonomy = EWD_US_SLIDER_CATEGORY_TAXONOMY;
	        $us_category_taxonomy = get_taxonomy( $taxonomy );

	        wp_dropdown_categories(
	        	array(
	        	    'show_option_all' =>  __( 'Show All ' . $us_category_taxonomy->label, 'ultimate-slider' ),
	        	    'taxonomy'        =>  $taxonomy,
	        	    'name'            =>  EWD_US_SLIDER_CATEGORY_TAXONOMY,
	        	    'orderby'         =>  'name',
	        	    'selected'        =>  isset( $wp_query->query['term'] )? $wp_query->query['term'] : "",
	        	    'hierarchical'    =>  true,
	        	    'depth'           =>  3,
	        	    'show_count'      =>  true, // Show # listings in parens
	        	    'hide_empty'      =>  true,
	        	)
	        );
	    }
	}

	/**
	 *
	 * @since 2.0.0
	 */
	public function convert_slide_category_to_taxonomy_term_in_query( $query ) {
	    global $pagenow;

	    $taxonomy = EWD_US_SLIDER_CATEGORY_TAXONOMY;
	    $q_vars = &$query->query_vars;

	    if ( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == EWD_US_SLIDER_POST_TYPE && isset( $q_vars[$taxonomy] ) && is_numeric( $q_vars[$taxonomy] ) && $q_vars[$taxonomy] != 0 ) {
	        
	        $term = get_term_by( 'id', $q_vars[$taxonomy], $taxonomy );
	        $q_vars[$taxonomy] = $term->slug;
	    }
	}

	/**
	 * Add in new columns for the ultimate_slider_categories table
	 * @since 2.0.0
	 */
	public function register_slide_category_table_columns($columns){

		$columns['us_category_shortcode'] = __( 'Shortcode', 'ultimate-slider' );

		return $columns;
	}

	/**
	 * Set display order of slide categories table columns
	 * @since 2.0.0
	 */
	public function slide_categories_table_columns_display_order( $columns ) {

		return array(
			'name' 					=> __( 'Name', 'ultimate-slider' ),
			'description' 			=> __( 'Description', 'ultimate-slider' ),
			'us_category_shortcode' => __( 'Shortcode', 'ultimate-slider' ),
			'posts' 				=> __( 'Count', 'ultimate-slider' ),
		);
	}

	/**
	 * Set the content for the shortcode column
	 * @since 2.0.0
	 */
	public function display_category_columns_content( $content, $column_name, $term_id ) {

		if ( $column_name == 'us_category_shortcode' ) {

			$term = get_term( $term_id, EWD_US_SLIDER_CATEGORY_TAXONOMY );
			$slug = $term->slug;
			$content .= "[ultimate-slider category='" . $slug . "']";
		}

		return $content;
	}

	/**
	 * Adjust the orderby if none specified to display in user-set slide order
	 * @since 2.0.0
	 */
	public function orderby_order_column( $query ) {

		if ( is_admin() and isset( $_GET['post_type'] ) and $_GET['post_type'] == EWD_US_SLIDER_POST_TYPE and ! isset( $_GET['orderby'] ) ) {

			$query->set( 'meta_key', 'EWD_US_Slide_Order' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
	    }
	}

	/**
	 * Get ID/name pairs for all posts and pages
	 * @since 2.0.0
	 */
	public function get_all_post_ids( $return = 'ajax' ) {

		if ( $return == 'ajax'
			and ! check_ajax_referer( 'ewd-us-admin-js', 'nonce' )
		) {

			ewdusHelper::admin_nopriv_ajax();
		}

		$args = array(
		    'post_type'    		=> array( 'page', 'post' ),
		    'orderby'      		=> 'menu_order',
		    'posts_per_page'	=> 200,
		);

		$query = new WP_Query ( $args );
		$posts = $query->posts;
	
		if ( $return == 'objects' ) { return $posts; }
	
		$return_pairs = array();
		foreach ( $posts as $post ) {

			$return_pair[ 'ID' ] = $post->ID;
			$return_pair[ 'Name' ] = $post->post_title;
	
			$return_pairs[] = $return_pair;
		}
	
		echo json_encode( $return_pairs );
	}

	/**
	 * Update the display order when slides are dragged and dropped
	 * @since 2.0.0
	 */
	public function update_slides_order() {

		// Authenticate request
		if ( ! check_ajax_referer( 'ewd-us-admin-js', 'nonce' ) ) {
			
			ewdusHelper::admin_nopriv_ajax();
		}

		$post_ids = json_decode( stripslashes( $_POST['IDs'] ) );

		$post_ids = is_array( $post_ids ) ? array_map( 'intval', $post_ids ) : array();

		if ( ! is_array( $post_ids ) ) { $post_ids = array(); }

		foreach ( $post_ids as $order => $post_id ) {

			update_post_meta( intval( $post_id ), 'EWD_US_Slide_Order', intval( $order ) );
		}
	}
}
} // endif;

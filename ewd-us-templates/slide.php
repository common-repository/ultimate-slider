<?php global $ewd_us_controller; ?>

<li <?php echo ewd_format_classes( $this->classes ); ?> id="ewd-us-slide-<?php echo esc_attr( $this->slide_count ); ?>" <?php echo $this->print_lightbox_data(); ?> >
				
	<img src="<?php echo esc_attr( $this->image_url ); ?>">
	<div class="ewd-us-slide-text">
		
		<div class="ewd-us-slide-title"><?php echo esc_html( $this->title ); ?></div>
		<div class="clear"></div>
		
		<div class="ewd-us-slide-excerpt"><?php echo wp_kses_post( $this->filtered_content ); ?></div>
		<div class="clear"></div>

		<ul class="ewd-us-slide-buttons">

			<?php foreach ( $this->buttons as $button ) { ?>
				
				<li>
					<a class="ewd-us-slide-button" href="<?php echo esc_attr( $this->get_button_link( $button ) ); ?>" <?php echo esc_attr( $this->get_button_link_target_text( $button ) ); ?> ><?php echo esc_html( strtoupper( $button['Text'] ) ); ?></a>
				</li>
			<?php } ?>

		</ul>
	
	</div> 

	<?php if ( $ewd_us_controller->settings->get_setting( 'carousel' ) and $ewd_us_controller->settings->get_setting( 'carousel-link-to-full' ) ) { ?> 
		
		<a href="' . get_the_permalink( $this->post->ID ) . '" class="ewd-us-carousel-link-to-full"></a>
	<?php } ?>

	<?php if ( $ewd_us_controller->settings->get_setting( 'mobile-link-to-full' ) ) { ?> 
		
		<a href="' . get_the_permalink( $this->post->ID ) . '" class="ewd-us-mobile-link-to-full"></a>
	<?php } ?>
</li>
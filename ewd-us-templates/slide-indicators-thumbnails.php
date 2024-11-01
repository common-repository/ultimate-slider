<?php foreach ( $this->slides as $slide_counter => $slide ) { ?>
	<div class="ewd-us-slider-control-thumbnail" data-slidenumber="<?php echo esc_attr( $slide_counter ); ?>">
		<img src="<?php echo esc_attr( $slide->image_url ); ?>" class="ewd-us-slider-control-thumbnail-img" />
	</div>
<?php } ?>
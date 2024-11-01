<div class="clear"></div>
<div class="ewd-us-slider-section">

	<div <?php echo ewd_format_classes( $this->classes ); ?> >

		<?php if ( $this->timer_bar != 'off' ) { ?> 
		
			<div id="ewd-us-timer-bar">
				<div id="ewd-us-inner-timer-bar"></div>
			</div>
		<?php } ?>

		<ul class="ewd-us-slider-window">
		
			<?php echo $this->print_slides(); ?>
	
		</ul>

		<?php echo $this->print_slide_arrows(); ?>

	</div>

	<?php echo $this->print_slide_indicators(); ?>


</div>

<div class="clear"></div>
jQuery(document).ready(function() {
	jQuery( '.ewd-us-welcome-screen-box h2' ).on( 'click', function() {
		var section = jQuery( this ).parent().data( 'screen' );
		us_toggle_section( section );
	});

	jQuery( '.ewd-us-welcome-screen-next-button' ).on( 'click', function() {
		var section = jQuery( this ).data( 'nextaction' );
		us_toggle_section( section );
	});

	jQuery( '.ewd-us-welcome-screen-previous-button' ).on( 'click', function() {
		var section = jQuery( this ).data( 'previousaction' );
		us_toggle_section( section );
	});

	jQuery( '.ewd-us-welcome-screen-add-slide-button' ).on( 'click', function() {

		jQuery( '.ewd-us-welcome-screen-show-created-slides' ).show();

		var slide_title = jQuery('.ewd-us-welcome-screen-add-slide-title input').val();
		var slide_image = jQuery('.ewd-us-welcome-screen-add-slide-image input[name="slide_image_url"]').val();
		var slide_description = jQuery('.ewd-us-welcome-screen-add-slide-description textarea').val();

		jQuery( '.ewd-us-welcome-screen-add-slide-title input' ).val( '' );
		jQuery( '.ewd-us-welcome-screen-image-preview' ).addClass( 'ewd-us-hidden' );
		jQuery( '.ewd-us-welcome-screen-add-slide-image input[name="slide_image_url"]' ).val( '' );
		jQuery( '.ewd-us-welcome-screen-add-slide-description textarea' ).val( '' );

		var params = {
			slide_title: slide_title,
			slide_image: slide_image,
			slide_description: slide_description,
			nonce: ewd_us_getting_started.nonce,
			action: 'us_welcome_add_slide'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {

			var HTML = '<tr class="ewd-us-welcome-screen-slide">';
			HTML += '<td class="us-welcome-screen-slide-image"><img src="' + slide_image + '"></td>';
			HTML += '<td class="us-welcome-screen-slide-title">' + slide_title + '</td>';
			HTML += '<td class="us-welcome-screen-slide-description">' + slide_description + '</td>';
			HTML += '</tr>';
			
			jQuery( '.ewd-us-welcome-screen-show-created-slides' ).append(HTML);
		});
	});

	jQuery( '.ewd-us-welcome-screen-add-slider-page-button').on( 'click', function() {

		var slider_page_title = jQuery( '.ewd-us-welcome-screen-add-slider-page-name input' ).val();

		var params = {
			slider_page_title: slider_page_title,
			nonce: ewd_us_getting_started.nonce,
			action: 'us_welcome_add_slider_page'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function(response) {} );

		var section = jQuery(this).data( 'nextaction' );

		us_toggle_section( section );
	});

	jQuery( '.ewd-us-welcome-screen-save-options-button' ).on( 'click', function() {
		
		var autoplay_slideshow = jQuery( 'input[name="autoplay_slideshow"]' ).is( ':checked' );
		var timer_bar = jQuery( 'input[name="timer_bar"]:checked' ).val();
		var carousel = jQuery( 'input[name="carousel"]' ).is( ':checked' );
		var aspect_ratio = jQuery( 'select[name="aspect_ratio"]' ).val();
		var slide_indicators = jQuery( 'input[name="slide_indicators"]:checked' ).val();

		var params = {
			autoplay_slideshow: autoplay_slideshow,
			timer_bar: timer_bar,
			carousel: carousel,
			aspect_ratio: aspect_ratio,
			slide_indicators: slide_indicators,
			nonce: ewd_us_getting_started.nonce,
			action: 'us_welcome_set_options'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function( response ) {} );
	});
 
    var custom_uploader;
 
    jQuery( '#welcome_slide_image_button' ).on( 'click', function( event ) {
 
        event.preventDefault();
 
        //If the uploader object has already been created, reopen the dialog
        if ( custom_uploader ) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on( 'select', function() {
            attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
            jQuery( 'input[name="slide_image_url"]' ).val( attachment.url );
            jQuery( '.ewd-us-welcome-screen-image-preview img' ).attr( 'src', attachment.url );
            jQuery( '.ewd-us-welcome-screen-image-preview' ).removeClass( 'ewd-us-hidden' );
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });

});

function us_toggle_section(page) {
	jQuery('.ewd-us-welcome-screen-box').removeClass('ewd-us-welcome-screen-open');
	jQuery('.ewd-us-welcome-screen-' + page).addClass('ewd-us-welcome-screen-open');
}
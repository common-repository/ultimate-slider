jQuery( document ).ready( function( $ ) {

	jQuery( '.ewd-us-main-dashboard-review-ask' ).css( 'display', 'block' );

	jQuery(document).on( 'click', '.ewd-us-main-dashboard-review-ask .notice-dismiss', function( event ) {

  		var params = {
			ask_review_time: '7',
			nonce: ewd_us_review_ask.nonce,
			action: 'ewd_us_hide_review_ask'
		};

		var data = jQuery.param( params );

    	jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-us-review-ask-yes' ).on( 'click', function() {

		jQuery( '.ewd-us-review-ask-feedback-text' ).removeClass( 'ewd-us-hidden' );
		jQuery( '.ewd-us-review-ask-starting-text' ).addClass( 'ewd-us-hidden' );

		jQuery( '.ewd-us-review-ask-no-thanks' ).removeClass( 'ewd-us-hidden' );
		jQuery( '.ewd-us-review-ask-review' ).removeClass( 'ewd-us-hidden' );

		jQuery( '.ewd-us-review-ask-not-really' ).addClass( 'ewd-us-hidden' );
		jQuery( '.ewd-us-review-ask-yes' ).addClass( 'ewd-us-hidden' );

		var params = {
			ask_review_time: '7',
			nonce: ewd_us_review_ask.nonce,
			action: 'ewd_us_hide_review_ask'
		};

		var data = jQuery.param( params );

    jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-us-review-ask-not-really' ).on( 'click', function() {
		jQuery( '.ewd-us-review-ask-review-text' ).removeClass( 'ewd-us-hidden' );
		jQuery( '.ewd-us-review-ask-starting-text' ).addClass( 'ewd-us-hidden' );

		jQuery( '.ewd-us-review-ask-feedback-form' ).removeClass( 'ewd-us-hidden' );
		jQuery( '.ewd-us-review-ask-actions' ).addClass( 'ewd-us-hidden' );

		var params = {
			ask_review_time: '1000',
			nonce: ewd_us_review_ask.nonce,
			action: 'ewd_us_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-us-review-ask-no-thanks' ).on( 'click', function() {

		var params = {
			ask_review_time: '1000',
			nonce: ewd_us_review_ask.nonce,
			action: 'ewd_us_hide_review_ask'
		};

		var data = jQuery.param( params );

    jQuery.post( ajaxurl, data, function() {} );

    jQuery( '.ewd-us-main-dashboard-review-ask' ).css( 'display', 'none' );
	});

	jQuery( '.ewd-us-review-ask-review' ).on( 'click', function() {

		jQuery( '.ewd-us-review-ask-feedback-text' ).addClass( 'ewd-us-hidden' );
		jQuery( '.ewd-us-review-ask-thank-you-text' ).removeClass( 'ewd-us-hidden' );

		var params = {
			ask_review_time: '1000',
			nonce: ewd_us_review_ask.nonce,
			action: 'ewd_us_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-us-review-ask-send-feedback' ).on( 'click', function() {

		var feedback = jQuery( '.ewd-us-review-ask-feedback-explanation textarea' ).val();
		var email_address = jQuery( '.ewd-us-review-ask-feedback-explanation input[name="feedback_email_address"]' ).val();

		var params = {
			feedback: feedback,
			email_address: email_address,
			nonce: ewd_us_review_ask.nonce,
			action: 'ewd_us_send_feedback'
		};

		var data = jQuery.param( params );
    
    jQuery.post( ajaxurl, data, function() {} );

    var params = {
			ask_review_time: '1000',
			nonce: ewd_us_review_ask.nonce,
			action: 'ewd_us_hide_review_ask'
		};

		var data = jQuery.param( params );

    jQuery.post( ajaxurl, data, function() {} );

    jQuery( '.ewd-us-review-ask-feedback-form' ).addClass( 'ewd-us-hidden' );
    jQuery( '.ewd-us-review-ask-review-text' ).addClass( 'ewd-us-hidden' );
    jQuery( '.ewd-us-review-ask-thank-you-text' ).removeClass( 'ewd-us-hidden' );
	});
});
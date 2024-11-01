jQuery( document ).ready( function( $ ) {

  jQuery(document).on( 'click', '.ewd-us-helper-install-notice .notice-dismiss', function( event ) {
    var data = jQuery.param({
      action: 'ewd_us_hide_helper_notice',
      nonce: ewd_us_helper_notice.nonce
    });

    jQuery.post( ajaxurl, data, function() {} );
  });
});
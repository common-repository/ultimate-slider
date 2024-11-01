jQuery(function() {
    wireReorderList();
});

function wireReorderList() {
    jQuery(".wp-list-table tbody").sortable({
    	stop: function( event, ui ) {saveOrderClick(); }
    }).disableSelection();
}

function saveOrderClick() {
    // ----- Retrieve the li items inside our sortable list
    var items = jQuery(".wp-list-table tbody tr");

    var linkIDs = [items.size()];
    var index = 0;

    // ----- Iterate through each li, extracting the ID embedded as an attribute
    items.each(
        function(intIndex) {
            linkIDs[intIndex] = jQuery(this).attr("id").substring(5);
            jQuery(this).find('.menu_order').html(intIndex);
            index++;
        });

    var params = {
        IDs: JSON.stringify( linkIDs ),
        nonce: ewd_us_admin_php_data.nonce,
        action: 'ewd_us_slides_update_order'
    };

    var data = jQuery.param( params );

    jQuery.post(ajaxurl, data, function(response) {

        jQuery( 'td.column-us_menu_order' ).each( function( index, el ) {
            jQuery( this ).html( index );
        });
    });

    //$get("<%=txtExampleItemsOrder.ClientID %>").value = linkIDs.join(",");
}

function ShowOptionTab(TabName) {
    jQuery(".ewd-us-option-set").each(function() {
        jQuery(this).addClass("ewd-us-hidden");
    });
    jQuery("#"+TabName).removeClass("ewd-us-hidden");

    jQuery(".options-subnav-tab").each(function() {
        jQuery(this).removeClass("options-subnav-tab-active");
    });
    jQuery("#"+TabName+"_Menu").addClass("options-subnav-tab-active");
    jQuery('input[name="Display_Tab"]').val(TabName);
}

jQuery(document).ready(function(){
    jQuery('.ewd-us-meta-menu-item').on('click', function() {
        var ID = jQuery(this).attr('id');
        var ID_Base = ID.substring(5);

        jQuery(".ewd-us-meta-body").each(function() {
            jQuery(this).addClass("ewd-us-hidden");
        });
        jQuery('#Body_'+ID_Base).removeClass("ewd-us-hidden");
    
        jQuery(".ewd-us-meta-menu-item").each(function() {
            jQuery(this).removeClass("meta-menu-tab-active");
        });
        jQuery(this).addClass("meta-menu-tab-active");
    });
});

jQuery(document).ready(function() {
    SetButtonDeleteHandlers();
    SetButtonDisableHandlers();

    jQuery('.ewd-us-add-button-list-item').on('click', function(event) {
        var ID = jQuery(this).data('nextid');

        var HTML = "<tr id='ewd-us-button-list-item-" + ID + "'>";
        HTML += "<td><a class='ewd-us-delete-button-list-item' data-buttonid='" + ID + "'>Delete</a></td>";
        HTML += "<td><input type='text' name='Button_List_" + ID + "_Text'></td>";
        HTML += "<td><select name='Button_List_" + ID + "_Post_ID' class='ewd-us-post-select' id='ewd-us-post-select-" + ID + "'><option value='0'>Custom Link</option></select></td>";
        HTML += "<td><input type='text' name='Button_List_" + ID + "_Custom_Link' id='ewd-us-post-link-" + ID + "'></td>";
        HTML += "</tr>";

        //jQuery('table > tr#ewd-uasp-add-reminder').before(HTML);
        jQuery('#ewd-us-buttons-list-table tr:last').before(HTML);

        AJAXPostIDs("#ewd-us-post-select-" + ID);

        ID++;
        jQuery(this).data('nextid', ID); //updates but doesn't show in DOM

        SetButtonDeleteHandlers();
        SetButtonDisableHandlers();

        event.preventDefault();
    });
});

function SetButtonDeleteHandlers() {
    jQuery('.ewd-us-delete-button-list-item').on('click', function(event) {
        var ID = jQuery(this).data('buttonid');
        var tr = jQuery('#ewd-us-button-list-item-'+ID);

        tr.fadeOut(400, function(){
            tr.remove();
        });

        event.preventDefault();
    });
}

function SetButtonDisableHandlers() {
    jQuery('.ewd-us-post-select').on('change', function() {
        var Full_ID = jQuery(this).attr('id');
        var ID = Full_ID.substring(19);

        if (jQuery(this).val() != 0) {
            jQuery('#ewd-us-post-link-'+ID).prop('disabled', true);
        }
        else {
            jQuery('#ewd-us-post-link-'+ID).prop('disabled', false);
        }
    })
}

jQuery(document).ready(function() {
    jQuery('.ewd-us-image-radio').on('change', function() {
        if (jQuery(this).val() == "youtube_video") {jQuery('#ewd-us-youtube-url').prop('disabled', false);}
        else {jQuery('#ewd-us-youtube-url').prop('disabled', true);}
    });
});

function AJAXPostIDs(selectID) {

    var params = {
        nonce: ewd_us_admin_php_data.nonce,
        action: 'ewd_us_get_post_ids'
    };

    var data = jQuery.param( params );

    jQuery.post(ajaxurl, data, function(response) {
        response = response.substring(0, response.length - 1);
        var posts_array = jQuery.parseJSON(response);
        jQuery(posts_array).each(function(index, post) {
            //console.log('post', post);
            jQuery(selectID).append("<option value='"+post.ID+"'>"+post.Name+"</option>");
        });
    });
}

jQuery(document).ready(function() {
    jQuery('.ewd-us-widget-slider-type').each(function() {
        var id = jQuery(this).attr('id');
        var widget_id = id.slice(0,-11);
        var slider_type = jQuery(this).val();

        EWD_US_Fill_Widget_Categories_Select(widget_id, slider_type);
    });

    jQuery('.ewd-us-widget-slider-type').on('change', function() {
        var id = jQuery(this).attr('id');
        var widget_id = id.slice(0,-11);
        var slider_type = jQuery(this).val();
alert("Type: " + slider_type); alert("Widget ID: " + widget_id);
        EWD_US_Fill_Widget_Categories_Select(widget_id, slider_type);
    });
});

function EWD_US_Fill_Widget_Categories_Select(widget_id, slider_type) {
    jQuery('#'+widget_id+'category').find('option').remove().end();

    jQuery('#'+widget_id+'category').append('<option value="">All</option>');

    if (slider_type == 'woocommerce') {
        if (typeof woocommerce_categories !== 'undefined' && woocommerce_categories !== null) {
            jQuery(woocommerce_categories).each(function(index, el) {
                jQuery('#'+widget_id+'category').append('<option value="'+el.Category_Slug+'">'+el.Category_Name+'</option>');
            });
        }
    }
    else if (slider_type == 'upcp') {
        if (typeof upcp_categories !== 'undefined' && upcp_categories !== null) {
            jQuery(upcp_categories).each(function(index, el) {
                jQuery('#'+widget_id+'category').append('<option value="'+el.Category_Slug+'">'+el.Category_Name+'</option>');
            });
        }
    }
    else {
        if (typeof slider_categories !== 'undefined' && slider_categories !== null) {
            jQuery(slider_categories).each(function(index, el) {
                console.log(el);
                jQuery('#'+widget_id+'category').append('<option value="'+el.Category_Slug+'">'+el.Category_Name+'</option>');
            });
        }
    }
}


/*NEW DASHBOARD MOBILE MENU AND WIDGET TOGGLING*/
jQuery(document).ready(function($){
	$('#ewd-us-dash-mobile-menu-open').click(function(){
		$('.ewd-us-admin-header-menu .nav-tab:nth-of-type(1n+2)').toggle();
		$('#ewd-us-dash-mobile-menu-up-caret').toggle();
		$('#ewd-us-dash-mobile-menu-down-caret').toggle();
		return false;
	});

	$(function(){
		$(window).resize(function(){
			if($(window).width() > 800){
				$('.ewd-us-admin-header-menu .nav-tab:nth-of-type(1n+2)').show();
			}
			else{
				$('.ewd-us-admin-header-menu .nav-tab:nth-of-type(1n+2)').hide();
				$('#ewd-us-dash-mobile-menu-up-caret').hide();
				$('#ewd-us-dash-mobile-menu-down-caret').show();
			}
		}).resize();
	});	

	$('#ewd-us-dashboard-support-widget-box .ewd-us-dashboard-new-widget-box-top').click(function(){
		$('#ewd-us-dashboard-support-widget-box .ewd-us-dashboard-new-widget-box-bottom').toggle();
		$('#ewd-us-dash-mobile-support-up-caret').toggle();
		$('#ewd-us-dash-mobile-support-down-caret').toggle();
	});

	$('#ewd-us-dashboard-optional-table .ewd-us-dashboard-new-widget-box-top').click(function(){
		$('#ewd-us-dashboard-optional-table .ewd-us-dashboard-new-widget-box-bottom').toggle();
		$('#ewd-us-dash-optional-table-up-caret').toggle();
		$('#ewd-us-dash-optional-table-down-caret').toggle();
	});

});

// About Us Page
jQuery( document ).ready( function( $ ) {

	jQuery( '.ewd-us-about-us-tab-menu-item' ).on( 'click', function() {

		jQuery( '.ewd-us-about-us-tab-menu-item' ).removeClass( 'ewd-us-tab-selected' );
		jQuery( '.ewd-us-about-us-tab' ).addClass( 'ewd-us-hidden' );

		var tab = jQuery( this ).data( 'tab' );

		jQuery( this ).addClass( 'ewd-us-tab-selected' );
		jQuery( '.ewd-us-about-us-tab[data-tab="' + tab + '"]' ).removeClass( 'ewd-us-hidden' );
	} );

	jQuery( '.ewd-us-about-us-send-feature-suggestion' ).on( 'click', function() {

		var feature_suggestion = jQuery( '.ewd-us-about-us-feature-suggestion textarea' ).val();
		var email_address = jQuery( '.ewd-us-about-us-feature-suggestion input[name="feature_suggestion_email_address"]' ).val();
	
		var params = {};

		params.nonce  				= ewd_us_admin_php_data.nonce;
		params.action 				= 'ewd_us_send_feature_suggestion';
		params.feature_suggestion	= feature_suggestion;
		params.email_address 		= email_address;

		var data = jQuery.param( params );
		jQuery.post( ajaxurl, data, function() {} );

		jQuery( '.ewd-us-about-us-feature-suggestion' ).prepend( '<p>Thank you, your feature suggestion has been submitted.' );
	} );
} );


//SETTINGS PREVIEW SCREENS

jQuery( document ).ready( function() {

	jQuery( '.ewd-us-settings-preview' ).prevAll( 'h2' ).hide();
	jQuery( '.ewd-us-settings-preview' ).prevAll( '.sap-tutorial-toggle' ).hide();
	jQuery( '.ewd-us-settings-preview .sap-tutorial-toggle' ).hide();
});

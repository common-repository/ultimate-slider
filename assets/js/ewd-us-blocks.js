var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.serverSideRender,
	TextControl = wp.components.TextControl,
	SelectControl = wp.components.SelectControl,
	InspectorControls = wp.blockEditor.InspectorControls;

registerBlockType( 'ultimate-slider/ewd-us-slider-block', {
	title: 'Display Slider',
	icon: 'slides',
	category: 'ewd-us-blocks',
	attributes: {
		category: { type: 'string' },
		posts: { type: 'posts' },
		slider_type: { type: 'string' },
		carousel_mode: { type: 'string' },
		slide_indicators: { type: 'string' },
	},

	edit: function( props ) {
		var returnString = [];
		returnString.push(
			el( InspectorControls, {},
				el( TextControl, {
					label: 'Category',
					value: props.attributes.category,
					onChange: ( value ) => { props.setAttributes( { category: value } ); },
				} ),
				el( TextControl, {
					label: 'Number of Slides',
					value: props.attributes.posts,
					onChange: ( value ) => { props.setAttributes( { posts: value } ); },
				} ),
				el( SelectControl, {
					label: 'Type of Slider',
					value: props.attributes.slider_type,
					options: [ {value: 'regular', label: 'Regular'}, {value: 'woocommerce', label: 'WooCommerce'}, {value: 'upcp', label: 'Ultimate Product Catalog'}, {value: 'urp', label: 'Ultimate Reviews'} ],
					onChange: ( value ) => { props.setAttributes( { slider_type: value } ); },
				} ),
				el( SelectControl, {
					label: 'Carousel Mode',
					value: props.attributes.carousel_mode,
					options: [ {value: 'No', label: 'No'}, {value: 'Yes', label: 'Yes'} ],
					onChange: ( value ) => { props.setAttributes( { carousel_mode: value } ); },
				} ),
				el( SelectControl, {
					label: 'Slide Indicators',
					value: props.attributes.slide_indicators,
					options: [ {value: 'Dots', label: 'Dots'}, {value: 'Thumbnails', label: 'Thumbnails'}, {value: 'Side Thumbnails', label: 'Side Thumbnails'}, {value: 'None', label: 'None'} ],
					onChange: ( value ) => { props.setAttributes( { slide_indicators: value } ); },
				} ),
			),
		);
		returnString.push( el( 'div', { class: 'ewd-us-admin-block ewd-us-admin-block-display-slider' }, 'Ultimate Slider' ) );
		return returnString;
	},

	save: function() {
		return null;
	},
} );


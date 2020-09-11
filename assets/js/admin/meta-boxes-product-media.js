/* global jQuery */
jQuery( function( $ ) {
	'use strict';

	/**
	 * Variations actions
	 */
	const f9mediaproductMetaBoxesProductVariationsActions = {

		/**
		 * Initialize variations actions
		 */
		init() {
			$( document.body ).on( 'woocommerce_added_attribute', this.attribute_added );
		},

		/**
		 * Run actions when added a attribute
		 */
		attribute_added() {
			const $wrapper = $( 'button.add_attribute' ).closest( '#product_attributes' );
			const $attributes = $wrapper.find( '.product_attributes' );
			const productType = $( 'select#product-type' ).val();
			if ( 'image' === productType || 'video' === productType ) {
				$attributes.find( '.enable_variation' ).show();
			}
		},
	};

	f9mediaproductMetaBoxesProductVariationsActions.init();
} );

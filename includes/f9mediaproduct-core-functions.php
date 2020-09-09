<?php
/**
 * F9mediaproduct Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package F9mediaproduct\Functions
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds in product type selector.
 *
 * @param array $selector_options Selector options.
 * @since 1.0.0
 * @return array
 */
function f9mediaproduct_product_type_selector( $selector_options ) {
	$remove_defaults = apply_filters(
		'f9mediaproduct_remove_default_types',
		get_option( 'f9mediaproduct_remove_default_types', false )
	);
	if ( $remove_defaults ) {
		$selector_options = array();
	}
	$types = (array) f9mediaproduct_types();
	foreach ( $types as $type ) {
		$selector_options[ $type ] = sprintf(
			/* translators: Product type */
			__( '%s product', 'f9mediaproduct' ),
			f9mediaproduct_label( $type )
		);
	}
	return $selector_options;
}
add_filter( 'product_type_selector', 'f9mediaproduct_product_type_selector', 9 );

/**
 * Get media product types.
 *
 * @return array
 */
function f9mediaproduct_types() {
	$types = (array) apply_filters(
		'f9mediaproduct_types',
		array(
			'image',
			'video',
		)
	);
	foreach ( $types as &$type ) {
		$type = str_replace( '-', '_', sanitize_title( $type ) );
	}
	return array_unique( array_filter( $types ) );
}

/**
 * Get media product types classnames.
 *
 * @return array
 */
function f9mediaproduct_classnames() {
	$types = f9mediaproduct_types();

	$classnames = array();
	foreach ( $types as $type ) {
		$classname = 'F9_WC_Product_' . ucfirst( $type );

		if ( class_exists( $classname ) ) {
			$classnames[ $type ] = $classname;
		}
	}
	return $classnames;
}

/**
 * Get media product type labels.
 *
 * @return array
 */
function f9mediaproduct_labels() {
	$labels = (array) apply_filters(
		'f9mediaproduct_labels',
		array(
			'image' => array(
				'singular' => _n( 'Image', 'Images', 1, 'f9mediaproduct' ),
				'plural'   => _n( 'Image', 'Images', 2, 'f9mediaproduct' ),
			),
			'video' => array(
				'singular' => _n( 'Video', 'Videos', 1, 'f9mediaproduct' ),
				'plural'   => _n( 'Video', 'Videos', 2, 'f9mediaproduct' ),
			),
		)
	);
	return array_filter( $labels );
}

/**
 * Get product type label.
 *
 * @param string $type Product type.
 * @return string
 */
function f9mediaproduct_label( $type ) {
	$labels = f9mediaproduct_labels();
	if ( isset( $labels[ $type ] ) ) {
		if ( isset( $labels[ $type ]['singular'] ) ) {
			$label = $labels[ $type ]['singular'];
		} else {
			$label = $labels[ $type ];
		}
	}
	if ( ! isset( $label ) || ! is_string( $label ) ) {
		$label = ucfirst( str_replace( '_', ' ', $type ) );
	}
	return $label;
}

/**
 * Filter default product type.
 *
 * @param  string  $type        Product type.
 * @param  integer $product_id Product id.
 * @return type
 */
function f9mediaproduct_product_type_query( $type, $product_id ) {
	if ( false === $type ) {
		$types = array_keys( wc_get_product_types() );
		if ( 'simple' !== current( $types ) ) {
			$type = current( $types );
		}
	}
	return $type;
}
add_filter( 'woocommerce_product_type_query', 'f9mediaproduct_product_type_query', 10, 2 );

/**
 * Product classname.
 *
 * @param  string  $classname    Classname.
 * @param  string  $product_type Product type.
 * @param  string  $post_type    Post type.
 * @param  integer $product_id   Product id.
 * @return string
 */
function f9mediaproduct_product_class( $classname, $product_type, $post_type, $product_id ) {
	$classnames = f9mediaproduct_classnames();
	if ( isset( $classnames[ $product_type ] ) ) {
		$classname = $classnames[ $product_type ];
	}
	return $classname;
}
add_filter( 'woocommerce_product_class', 'f9mediaproduct_product_class', 10, 4 );

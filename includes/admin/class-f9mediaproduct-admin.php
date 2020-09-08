<?php
/**
 * F9mediaproduct Admin
 *
 * @class    F9mediaproduct_Admin
 * @package  F9mediaproduct/Admin
 * @version  1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9mediaproduct_Admin class.
 */
class F9mediaproduct_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'selfd_register', array( $this, 'register_selfdirectory' ) );
		add_filter( 'product_type_selector', array( $this, 'product_type_selector' ), 9 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		// Self Directory.
		$filename = F9MEDIAPRODUCT_ABSPATH . 'includes/libs/selfd/class-selfdirectory.php';
		if ( file_exists( $filename ) ) {
			include_once $filename;
		}
		include_once dirname( __FILE__ ) . '/class-f9mediaproduct-admin-post-types.php';
		include_once dirname( __FILE__ ) . '/class-f9mediaproduct-admin-assets.php';
	}

	/**
	 * Use Selfd to updates.
	 */
	public function register_selfdirectory() {
		selfd( F9MEDIAPRODUCT_PLUGIN_FILE );
	}

	/**
	 * Adds in product type selector.
	 *
	 * @param array $selector_options Selector options.
	 * @since 1.0.0
	 * @return array
	 */
	public function product_type_selector( $selector_options ) {
		$remove_defaults = apply_filters(
			'f9mediaproduct_remove_default_types',
			get_option( 'f9mediaproduct_remove_default_types', true )
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
}

return new F9mediaproduct_Admin();

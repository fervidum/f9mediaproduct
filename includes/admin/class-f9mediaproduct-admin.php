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
}

return new F9mediaproduct_Admin();

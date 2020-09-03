<?php
/**
 * Post Types Admin
 *
 * @package F9mediaproduct/admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'F9mediaproduct_Admin_Post_Types', false ) ) {
	new F9mediaproduct_Admin_Post_Types();
	return;
}

/**
 * F9mediaproduct_Admin_Post_Types Class.
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class F9mediaproduct_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		include_once dirname( __FILE__ ) . '/class-f9mediaproduct-admin-meta-boxes.php';
		include_once dirname( __FILE__ ) . '/meta-boxes/class-f9mediaproduct-meta-box-product-data.php';
	}
}

new F9mediaproduct_Admin_Post_Types();

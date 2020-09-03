<?php
/**
 * F9mediaproduct Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types).
 *
 * @package F9mediaproduct/Admin/Meta Boxes
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9mediaproduct_Admin_Meta_Boxes.
 */
class F9mediaproduct_Admin_Meta_Boxes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_product_data_tabs', 'F9mediaproduct_Meta_Box_Product_Data::product_data_tabs', 20, 2 );
	}
}

new F9mediaproduct_Admin_Meta_Boxes();

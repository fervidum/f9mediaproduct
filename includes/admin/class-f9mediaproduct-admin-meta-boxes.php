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
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 40 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 50 );

		add_filter(
			'woocommerce_product_data_tabs',
			'F9mediaproduct_Meta_Box_Product_Data::product_data_tabs'
		);
		add_filter(
			'f9mediaproduct_admin_meta_boxes_views_html_product_data',
			'F9mediaproduct_Meta_Box_Product_Data::view_html',
			10,
			2
		);
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {
		// Products.
		add_meta_box( 'woocommerce-product-data', __( 'Product data', 'woocommerce' ), 'F9mediaproduct_Meta_Box_Product_Data::output', 'product', 'normal', 'high' );
	}

	/**
	 * Remove meta boxes.
	 */
	public function remove_meta_boxes() {

		// Products.
		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		remove_meta_box( 'woocommerce-product-data', 'product', 'normal', 'high' );
		remove_meta_box( 'woocommerce-product-images', 'product', 'side', 'low' );
	}
}

new F9mediaproduct_Admin_Meta_Boxes();

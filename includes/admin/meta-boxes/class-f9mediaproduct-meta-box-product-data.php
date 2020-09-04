<?php
/**
 * Product Data
 *
 * Displays the product data box, tabbed, with several panels covering price, stock etc.
 *
 * @package F9mediaproduct/Admin/Meta Boxes
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9mediaproduct_Meta_Box_Product_Data Class.
 */
class F9mediaproduct_Meta_Box_Product_Data {


	/**
	 * Show tab content/settings.
	 */
	public static function output_tabs() {
		global $post, $thepostid, $product_object;

		include 'views/html-product-data-inventory.php';
	}

	/**
	 * Return array of tabs to show.
	 *
	 * @return array
	 */
	public static function product_data_tabs( $tabs ) {
		$types = f9mediaproduct_types();
		if ( $types ) {
			foreach ( $tabs as $key => &$tab ) {
				if ( ! isset( $tab['class'] ) ) {
					$tab['class'] = array();
				}
				foreach ( $types as $type ) {
					$tab['class'][] = "hide_if_$type";
				}
			}
		}

		$classes = array();
		foreach ( $types as $type ) {
			$classes[] = "show_if_$type";
		}

		$tabs = array_merge(
			$tabs,
			apply_filters(
				'f9mediaproduct_product_data_tabs',
				array(
					'inventory'      => array(
						'label'    => __( 'Inventory', 'woocommerce' ),
						'target'   => 'f9mediaproduct_inventory_product_data',
						'class'    => $classes,
						'priority' => 10,
					),
				)
			)
		);

		return $tabs;
	}
}

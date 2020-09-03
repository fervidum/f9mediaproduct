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
				foreach( $types as $type ) {
					$tab['class'][] = "hide_if_$type";
				}
			}
		}

		return $tabs;
	}
}

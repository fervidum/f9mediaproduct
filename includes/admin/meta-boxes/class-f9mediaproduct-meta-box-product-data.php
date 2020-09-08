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
		include 'views/html-product-data-attributes.php';
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

		// printj( $tabs );
		// die;
		$tabs = array_merge(
			$tabs,
			apply_filters(
				'f9mediaproduct_product_data_tabs',
				array(
					'f9mediaproduct_inventory' => array(
						'label'    => __( 'Inventory', 'woocommerce' ),
						'target'   => 'f9mediaproduct_inventory_product_data',
						'class'    => $classes,
						'priority' => 10,
					),
					'f9mediaproduct_attribute' => array(
						'label'    => __( 'Attributes', 'woocommerce' ),
						'target'   => 'f9mediaproduct_product_attributes',
						'class'    => $classes,
						'priority' => 20,
					),
					'f9mediaproduct_variations' => array(
						'label'    => __( 'Variations', 'woocommerce' ),
						'target'   => 'f9mediaproduct_variable_product_options',
						'class'    => $classes,
						'priority' => 30,
					),
				)
			)
		);

		return $tabs;
	}

	/**
	 * Filter callback for finding variation attributes.
	 *
	 * @param  WC_Product_Attribute $attribute Product attribute.
	 * @return bool
	 */
	private static function filter_variation_attributes( $attribute ) {
		return true === $attribute->get_variation();
	}

	/**
	 * Show options for the variable product type.
	 */
	public static function output_variations() {
		global $post, $wpdb, $product_object;

		$variation_attributes   = array_filter( $product_object->get_attributes(), array( __CLASS__, 'filter_variation_attributes' ) );
		$default_attributes     = $product_object->get_default_attributes();
		$variations_count       = absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_count', $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation' AND post_status IN ('publish', 'private')", $post->ID ) ), $post->ID ) );
		$variations_per_page    = absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) );
		$variations_total_pages = ceil( $variations_count / $variations_per_page );

		include 'views/html-product-data-variations.php';
	}
}

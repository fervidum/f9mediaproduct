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
	 * Output the metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function output( $post ) {
		global $thepostid, $product_object;

		$thepostid      = $post->ID;
		$product_object = $thepostid ? wc_get_product( $thepostid ) : new WC_Product();

		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );

		include WC_ABSPATH . '/includes/admin/meta-boxes/views/html-product-data-panel.php';
	}

	/**
	 * Show tab content/settings.
	 */
	private static function output_tabs() {
		global $post, $thepostid, $product_object;

		$views = array(
			'general',
			'inventory',
			'shipping',
			'linked-products',
			'attributes',
			'advanced',
		);

		$wc_meta_boxes_path = WC_ABSPATH . '/includes/admin/meta-boxes/';

		foreach ( $views as $view ) {
			ob_start();
			include $wc_meta_boxes_path . "views/html-product-data-$view.php";
			echo apply_filters(
				'f9mediaproduct_admin_meta_boxes_views_html_product_data',
				ob_get_clean(),
				$view
			);
		}
	}

	/**
	 * Return array of product type options.
	 *
	 * @return array
	 */
	private static function get_product_type_options() {
		return apply_filters(
			'product_type_options',
			array(
				'virtual'      => array(
					'id'            => '_virtual',
					'wrapper_class' => 'show_if_simple',
					'label'         => __( 'Virtual', 'woocommerce' ),
					'description'   => __( 'Virtual products are intangible and are not shipped.', 'woocommerce' ),
					'default'       => 'no',
				),
				'downloadable' => array(
					'id'            => '_downloadable',
					'wrapper_class' => 'show_if_simple',
					'label'         => __( 'Downloadable', 'woocommerce' ),
					'description'   => __( 'Downloadable products give access to a file upon purchase.', 'woocommerce' ),
					'default'       => 'no',
				),
			)
		);
	}

	/**
	 * Return array of tabs to show.
	 *
	 * @return array
	 */
	private static function get_product_data_tabs() {
		$tabs = apply_filters(
			'woocommerce_product_data_tabs',
			array(
				'general'        => array(
					'label'    => __( 'General', 'woocommerce' ),
					'target'   => 'general_product_data',
					'class'    => array( 'hide_if_grouped' ),
					'priority' => 10,
				),
				'inventory'      => array(
					'label'    => __( 'Inventory', 'woocommerce' ),
					'target'   => 'inventory_product_data',
					'class'    => array( 'show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external' ),
					'priority' => 20,
				),
				'shipping'       => array(
					'label'    => __( 'Shipping', 'woocommerce' ),
					'target'   => 'shipping_product_data',
					'class'    => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external' ),
					'priority' => 30,
				),
				'linked_product' => array(
					'label'    => __( 'Linked Products', 'woocommerce' ),
					'target'   => 'linked_product_data',
					'class'    => array(),
					'priority' => 40,
				),
				'attribute'      => array(
					'label'    => __( 'Attributes', 'woocommerce' ),
					'target'   => 'product_attributes',
					'class'    => array(),
					'priority' => 50,
				),
				'variations'     => array(
					'label'    => __( 'Variations', 'woocommerce' ),
					'target'   => 'variable_product_options',
					'class'    => array( 'variations_tab', 'show_if_variable' ),
					'priority' => 60,
				),
				'advanced'       => array(
					'label'    => __( 'Advanced', 'woocommerce' ),
					'target'   => 'advanced_product_data',
					'class'    => array(),
					'priority' => 70,
				),
			)
		);

		// Sort tabs based on priority.
		uasort( $tabs, array( __CLASS__, 'product_data_tabs_sort' ) );

		return $tabs;
	}

	/**
	 * Callback to sort product data tabs on priority.
	 *
	 * @since 3.1.0
	 * @param int $a First item.
	 * @param int $b Second item.
	 *
	 * @return bool
	 */
	private static function product_data_tabs_sort( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return -1;
		}

		if ( $a['priority'] === $b['priority'] ) {
			return 0;
		}

		return $a['priority'] < $b['priority'] ? -1 : 1;
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

		include WC_ABSPATH . '/includes/admin/meta-boxes/views/html-product-data-variations.php';
	}

	/**
	 * Return array of tabs to show.
	 *
	 * @param  array $tabs Current tabs.
	 * @return array
	 */
	public static function product_data_tabs( $tabs ) {
		$types = f9mediaproduct_types();

		$shows = array(
			'inventory',
			'attribute',
			'variations',
		);

		if ( $types ) {
			foreach ( $tabs as $key => &$tab ) {
				if ( in_array( $key, $shows, true ) ) {
					continue;
				}
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

		foreach ( $shows as $show ) {
			$tabs[ $show ]['class'] = array_merge(
				$tabs[ $show ]['class'],
				$classes
			);
		}

		return $tabs;
	}

	/**
	 * Product meta box product data view html.
	 *
	 * @param  string $html HTML content.
	 * @param  string $view View.
	 * @return string
	 */
	public static function view_html( $html, $view ) {
		$types = f9mediaproduct_types();
		$hides = array();
		$shows = array();
		foreach ( $types as $type ) {
			$hides[] = "hide_if_$type";
			$shows[] = "show_if_$type";
		}
		$hides = implode( ' ', $hides );
		$html  = preg_replace(
			'/(hide_if_variable)([\s"])/',
			'$1 ' . $hides . '$2',
			$html
		);
		$shows = implode( ' ', $shows );
		if ( ! in_array( $view, array( 'inventory' ), true ) ) {
			$html = preg_replace(
				'/(show_if_variable)([\s"])/',
				'$1 ' . $shows . '$2',
				$html
			);
		}
		return $html;
	}
}

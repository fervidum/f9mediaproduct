<?php
/**
 * F9mediaproduct F9mediaproduct_AJAX. AJAX Event Handlers.
 *
 * @class   F9mediaproduct_AJAX
 * @package F9mediaproduct/Classes
 */

defined( 'ABSPATH' ) || exit;

/**
 * F9mediaproduct_AJAX class.
 */
class F9mediaproduct_AJAX {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		self::overwrite_wc_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax) orverwrite WC AJAX.
	 */
	public static function overwrite_wc_ajax_events() {
		$ajax_events = array(
			'add_attribute',
			'add_variation',
			'link_all_variations',
			'load_variations',
		);

		foreach ( $ajax_events as $ajax_event ) {
			remove_action( 'wp_ajax_woocommerce_' . $ajax_event, array( 'WC_AJAX', $ajax_event ) );
			add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
		}
	}

	/**
	 * Add an attribute row.
	 */
	public static function add_attribute() {
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ! isset( $_POST['taxonomy'], $_POST['i'] ) ) {
			wp_die( -1 );
		}

		$i             = absint( $_POST['i'] );
		$metabox_class = array();
		$attribute     = new WC_Product_Attribute();

		$attribute->set_id( wc_attribute_taxonomy_id_by_name( sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) ) );
		$attribute->set_name( sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) );
		$attribute->set_visible( apply_filters( 'woocommerce_attribute_default_visibility', 1 ) );
		$attribute->set_variation( apply_filters( 'woocommerce_attribute_default_is_variation', 0 ) );

		if ( $attribute->is_taxonomy() ) {
			$metabox_class[] = 'taxonomy';
			$metabox_class[] = $attribute->get_name();
		}

		$wc_meta_boxes_path = WC_ABSPATH . '/includes/admin/meta-boxes/';

		ob_start();
		include $wc_meta_boxes_path . 'views/html-product-attribute.php';
		echo apply_filters(
			'f9mediaproduct_admin_meta_boxes_views_html_product_data',
			ob_get_clean(),
			'attribute'
		);
		wp_die();
	}

	/**
	 * Add variation via ajax function.
	 */
	public static function add_variation() {
		check_ajax_referer( 'add-variation', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ! isset( $_POST['post_id'], $_POST['loop'] ) ) {
			wp_die( -1 );
		}

		global $post; // Set $post global so its available, like within the admin screens.

		$product_id     = intval( $_POST['post_id'] );
		$post           = get_post( $product_id ); // phpcs:ignore
		$loop           = intval( $_POST['loop'] );
		$product_object = wc_get_product( $product_id );
		if ( ! $product_object ) {
			$product_object   = wc_get_product_object( 'variable', $product_id ); // Forces type to variable in case product is unsaved.
		}
		$variation_object = wc_get_product_object( 'variation' );
		$variation_object->set_parent_id( $product_id );
		$variation_object->set_attributes( array_fill_keys( array_map( 'sanitize_title', array_keys( $product_object->get_variation_attributes() ) ), '' ) );
		if ( in_array( $product_object->get_type(), array( 'image', 'video' ), true ) ) {
			$variation_object->set_virtual( 'yes' );
			$variation_object->set_downloadable( 'yes' );
		}
		$variation_id   = $variation_object->save();
		$variation      = get_post( $variation_id );
		$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.

		ob_start();
		include 'admin/meta-boxes/views/html-variation-admin.php';
		echo apply_filters(
			'f9mediaproduct_admin_meta_boxes_views_html_product_data',
			ob_get_clean(),
			'variation-admin'
		);
		wp_die();
	}

	/**
	 * Link all variations via ajax function.
	 */
	public static function link_all_variations() {
		check_ajax_referer( 'link-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) ) {
			wp_die( -1 );
		}

		wc_maybe_define_constant( 'WC_MAX_LINKED_VARIATIONS', 50 );
		wc_set_time_limit( 0 );

		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_die();
		}

		$product    = wc_get_product( $post_id );
		$data_store = $product->get_data_store();

		if ( ! is_callable( array( $data_store, 'create_all_product_variations' ) ) ) {
			wp_die();
		}

		echo esc_html( $data_store->create_all_product_variations( $product, WC_MAX_LINKED_VARIATIONS ) );

		$data_store->sort_all_product_variations( $product->get_id() );
		wp_die();
	}

	/**
	 * Load variations via AJAX.
	 */
	public static function load_variations() {
		ob_start();

		check_ajax_referer( 'load-variations', 'security' );

		if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) ) {
			wp_die( -1 );
		}

		// Set $post global so its available, like within the admin screens.
		global $post;

		$loop           = 0;
		$product_id     = absint( $_POST['product_id'] );
		$post           = get_post( $product_id ); // phpcs:ignore
		$product_object = wc_get_product( $product_id );
		$per_page       = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
		$page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
		$variations     = wc_get_products(
			array(
				'status'  => array( 'private', 'publish' ),
				'type'    => 'variation',
				'parent'  => $product_id,
				'limit'   => $per_page,
				'page'    => $page,
				'orderby' => array(
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				),
				'return'  => 'objects',
			)
		);

		if ( $variations ) {
			wc_render_invalid_variation_notice( $product_object );

			foreach ( $variations as $variation_object ) {
				$variation_id   = $variation_object->get_id();
				$variation      = get_post( $variation_id );
				$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.
				ob_start();
				include 'admin/meta-boxes/views/html-variation-admin.php';
				echo apply_filters(
					'f9mediaproduct_admin_meta_boxes_views_html_product_data',
					ob_get_clean(),
					'variation-admin'
				);
				$loop++;
			}
		}
		wp_die();
	}

}

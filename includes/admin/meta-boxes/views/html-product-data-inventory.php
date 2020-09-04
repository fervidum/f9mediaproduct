<?php
/**
 * Displays the inventory tab in the product data meta box.
 *
 * @package F9mediaproduct\Admin
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="f9mediaproduct_inventory_product_data" class="panel woocommerce_options_panel hidden">

	<div class="options_group">
		<?php
		if ( wc_product_sku_enabled() ) {
			woocommerce_wp_text_input(
				array(
					'id'          => '_sku',
					'value'       => $product_object->get_sku( 'edit' ),
					'label'       => '<abbr title="' . esc_attr__( 'Stock Keeping Unit', 'woocommerce' ) . '">' . esc_html__( 'SKU', 'woocommerce' ) . '</abbr>',
					'desc_tip'    => true,
					'description' => __( 'SKU refers to a Stock-keeping unit, a unique identifier for each distinct product and service that can be purchased.', 'woocommerce' ),
				)
			);
		}

		do_action( 'woocommerce_product_options_sku' );

		do_action( 'woocommerce_product_options_stock_status' );
		?>
	</div>

	<?php do_action( 'woocommerce_product_options_inventory_product_data' ); ?>
</div>

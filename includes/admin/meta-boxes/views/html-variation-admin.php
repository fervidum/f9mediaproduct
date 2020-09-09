<?php
/**
 * Outputs a variation for editing.
 *
 * @package WooCommerce\Admin
 * @var int $variation_id
 * @var WP_POST $variation
 * @var WC_Product_Variation $variation_object
 * @var array $variation_data array of variation data @deprecated 4.4.0.
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="woocommerce_variation wc-metabox closed">
	<h3>
		<a href="#" class="remove_variation delete" rel="<?php echo esc_attr( $variation_id ); ?>"><?php esc_html_e( 'Remove', 'woocommerce' ); ?></a>
		<div class="handlediv" aria-label="<?php esc_attr_e( 'Click to toggle', 'woocommerce' ); ?>"></div>
		<div class="tips sort" data-tip="<?php esc_attr_e( 'Drag and drop, or click to set admin variation order', 'woocommerce' ); ?>"></div>
		<strong>#<?php echo esc_html( $variation_id ); ?> </strong>
		<?php
		$attribute_values = $variation_object->get_attributes( 'edit' );

		foreach ( $product_object->get_attributes( 'edit' ) as $attribute ) {
			if ( ! $attribute->get_variation() ) {
				continue;
			}
			$selected_value = isset( $attribute_values[ sanitize_title( $attribute->get_name() ) ] ) ? $attribute_values[ sanitize_title( $attribute->get_name() ) ] : '';
			?>
			<select name="attribute_<?php echo esc_attr( sanitize_title( $attribute->get_name() ) . "[{$loop}]" ); ?>">
				<option value="">
					<?php
					/* translators: %s: attribute label */
					printf( esc_html__( 'Any %s&hellip;', 'woocommerce' ), wc_attribute_label( $attribute->get_name() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</option>
				<?php if ( $attribute->is_taxonomy() ) : ?>
					<?php foreach ( $attribute->get_terms() as $option ) : ?>
						<option <?php selected( $selected_value, $option->slug ); ?> value="<?php echo esc_attr( $option->slug ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option->name, $option, $attribute->get_name(), $product_object ) ); ?></option>
					<?php endforeach; ?>
				<?php else : ?>
					<?php foreach ( $attribute->get_options() as $option ) : ?>
						<option <?php selected( $selected_value, $option ); ?> value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute->get_name(), $product_object ) ); ?></option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<?php
		}
		?>
		<input type="hidden" name="variable_post_id[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $variation_id ); ?>" />
		<input type="hidden" class="variation_menu_order" name="variation_menu_order[<?php echo esc_attr( $loop ); ?>]" value="<?php echo esc_attr( $variation_object->get_menu_order( 'edit' ) ); ?>" />

		<?php
		/**
		 * Variations header action.
		 *
		 * @since 3.6.0
		 *
		 * @param WP_Post $variation Post data.
		 */
		do_action( 'woocommerce_variation_header', $variation );
		?>
	</h3>
	<div class="woocommerce_variable_attributes wc-metabox-content" style="display: none;">
		<div class="data">
			<p class="form-row form-row-first upload_image">
				<a href="#" class="upload_image_button tips <?php echo $variation_object->get_image_id( 'edit' ) ? 'remove' : ''; ?>" data-tip="<?php echo $variation_object->get_image_id( 'edit' ) ? esc_attr__( 'Remove this image', 'woocommerce' ) : esc_attr__( 'Upload an image', 'woocommerce' ); ?>" rel="<?php echo esc_attr( $variation_id ); ?>">
					<img src="<?php echo $variation_object->get_image_id( 'edit' ) ? esc_url( wp_get_attachment_thumb_url( $variation_object->get_image_id( 'edit' ) ) ) : esc_url( wc_placeholder_img_src() ); ?>" /><input type="hidden" name="upload_image_id[<?php echo esc_attr( $loop ); ?>]" class="upload_image_id" value="<?php echo esc_attr( $variation_object->get_image_id( 'edit' ) ); ?>" />
				</a>
			</p>
			<?php
			$label = sprintf(
				/* translators: %s: currency symbol */
				__( 'Regular price (%s)', 'woocommerce' ),
				get_woocommerce_currency_symbol()
			);

			woocommerce_wp_text_input(
				array(
					'id'            => "variable_regular_price_{$loop}",
					'name'          => "variable_regular_price[{$loop}]",
					'value'         => wc_format_localized_price( $variation_object->get_regular_price( 'edit' ) ),
					'label'         => $label,
					'data_type'     => 'price',
					'wrapper_class' => 'variable_pricing form-row form-row-last',
					'placeholder'   => __( 'Variation price (required)', 'woocommerce' ),
				)
			);
			?>
			<input type="hidden" name="variable_enabled[<?php echo esc_attr( $loop ); ?>]" <?php checked( in_array( $variation_object->get_status( 'edit' ), array( 'publish', false ), true ), true ); ?> />
			<input type="hidden" name="variable_is_downloadable[<?php echo esc_attr( $loop ); ?>]" <?php checked( $variation_object->get_downloadable( 'edit' ), true ); ?> />
			<input type="hidden" name="variable_is_virtual[<?php echo esc_attr( $loop ); ?>]" <?php checked( $variation_object->get_virtual( 'edit' ), true ); ?> />
			<?php do_action( 'woocommerce_product_after_variable_attributes', $loop, $variation_data, $variation ); ?>
		</div>
	</div>
</div>

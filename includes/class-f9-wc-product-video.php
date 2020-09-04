<?php
/**
 * Video Product
 *
 * The F9 WooCommerce product class handles individual product data.
 *
 * @version 1.0.0
 * @package WooCommerce/Classes/Products
 */

defined( 'ABSPATH' ) || exit;

/**
 * Video product class.
 */
class F9_WC_Product_Video extends WC_Product_Variable {

	/**
	 * Initialize video product.
	 *
	 * @param WC_Product|int $product Product instance or ID.
	 */
	public function __construct( $product = 0 ) {
		$this->data = array_merge(
			$this->data,
			array(
				'virtual'           => true,
				'downloadable'      => true,
				'sold_individually' => true,
			)
		);
		parent::__construct( $product );
	}

	/**
	 * Stores product data.
	 *
	 * @var array
	 */
	protected $extra_data = array(
		'virtual'      => true,
		'downloadable' => true,
	);

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type() {
		return 'video';
	}

	/**
	 * Get virtual.
	 *
	 * @since  1.0.0
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return bool
	 */
	public function get_virtual( $context = 'view' ) {
		return $this->get_prop( 'virtual', $context );
	}
}

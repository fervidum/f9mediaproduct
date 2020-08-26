<?php
/**
 * Plugin Name: F9mediaproduct
 * Plugin URI: https://fervidum.github.io/f9mediaproduct/
 * Description: WooCommerce Media Product type add product types to images and videos.
 * Version: 1.0.0-alpha
 * Author: Fervidum
 * Author URI: https://fervidum.github.io/
 * Text Domain: f9mediaproduct
 * Domain Path: /languages/
 * Requires at least: 5.2
 * Requires PHP: 7.0
 *
 * Directory: https://fervidum.github.io/f9mediaproduct/
 *
 * @package F9mediaproduct
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'F9MEDIAPRODUCT_PLUGIN_FILE' ) ) {
	define( 'F9MEDIAPRODUCT_PLUGIN_FILE', __FILE__ );
}

// Include the main F9mediaproduct class.
if ( ! class_exists( 'F9mediaproduct', false ) ) {
	include_once dirname( F9MEDIAPRODUCT_PLUGIN_FILE ) . '/includes/class-f9mediaproduct.php';
}

/**
 * Returns the main instance of F9mediaproduct.
 *
 * @since  1.0.0
 * @return F9mediaproduct
 */
function f9mediaproduct() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return F9mediaproduct::instance();
}

f9mediaproduct();

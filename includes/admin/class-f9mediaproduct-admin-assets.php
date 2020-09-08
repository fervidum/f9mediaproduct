<?php
/**
 * Load assets
 *
 * @package F9mediaproduct/Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'F9mediaproduct_Admin_Assets', false ) ) :

	/**
	 * F9mediaproduct_Admin_Assets Class.
	 */
	class F9mediaproduct_Admin_Assets {

		/**
		 * Hook in tabs.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}

		/**
		 * Enqueue styles.
		 */
		public function admin_styles() {
			global $wp_scripts;

			$version   = F9MEDIAPRODUCT_VERSION;
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			// Register admin styles.
			wp_register_style( 'f9mediaproduct_admin_styles', f9mediaproduct()->plugin_url() . '/assets/css/f9mediaproduct-admin.css', array(), $version );

			// Admin styles for WC pages only.
			if ( function_exists( 'wc_get_screen_ids' ) && in_array( $screen_id, wc_get_screen_ids() ) ) {
				wp_enqueue_style( 'f9mediaproduct_admin_styles' );
			}
		}


		/**
		 * Enqueue scripts.
		 */
		public function admin_scripts() {
			global $wp_query, $post;

			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';
			$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$version   = F9MEDIAPRODUCT_VERSION;

			// Register scripts.
			wp_register_script( 'f9mediaproduct_admin', f9mediaproduct()->plugin_url() . '/assets/js/admin/f9mediaproduct_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $version );

			// WooCommerce admin pages.
			if ( function_exists( 'wc_get_screen_ids' ) && in_array( $screen_id, wc_get_screen_ids() ) ) {
				wp_enqueue_script( 'f9mediaproduct_admin' );

				$params = array();

				wp_localize_script( 'f9mediaproduct_admin', 'f9mediaproduct_admin', $params );
			}

		}

	}

endif;

return new F9mediaproduct_Admin_Assets();

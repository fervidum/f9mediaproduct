<?php
/**
 * F9mediaproduct setup
 *
 * @package F9mediaproduct
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main F9mediaproduct Class.
 *
 * @class F9mediaproduct
 */
final class F9mediaproduct {

	/**
	 * F9mediaproduct version.
	 *
	 * @var string
	 */
	public $version = '1.0.0-alpha';

	/**
	 * The single instance of the class.
	 *
	 * @var F9mediaproduct
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main F9mediaproduct Instance.
	 *
	 * Ensures only one instance of F9mediaproduct is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see f9mediaproduct()
	 * @return F9mediaproduct - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * F9mediaproduct Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'woocommerce_init', array( $this, 'include_product_classes' ) );
		add_action( 'woocommerce_init', 'F9mediaproduct_AJAX::init' );
	}

	/**
	 * Define F9media_product Constants.
	 */
	private function define_constants() {
		$this->define( 'F9MEDIAPRODUCT_ABSPATH', dirname( F9MEDIAPRODUCT_PLUGIN_FILE ) . '/' );
		$this->define( 'F9MEDIAPRODUCT_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Returns true if the request is a non-legacy REST API request.
	 *
	 * Legacy REST requests should still run some extra code for backwards compatibility.
	 *
	 * @todo: replace this function once core WP function is available: https://core.trac.wordpress.org/ticket/42061.
	 *
	 * @return bool
	 */
	public function is_rest_api_request() {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		return apply_filters( 'f9media_product_is_rest_api_request', $is_rest_api_request );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request();
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Core classes.
		 */
		include_once F9MEDIAPRODUCT_ABSPATH . 'includes/f9mediaproduct-core-functions.php';
		include_once F9MEDIAPRODUCT_ABSPATH . 'includes/class-f9mediaproduct-ajax.php';

		if ( $this->is_request( 'admin' ) ) {
			include_once F9MEDIAPRODUCT_ABSPATH . 'includes/admin/class-f9mediaproduct-admin.php';
		}
	}

	/**
	 * Include Product classes.
	 */
	public function include_product_classes() {
		$types   = f9mediaproduct_types();
		$classes = array();
		foreach ( $types as $type ) {
			$classes[] = F9MEDIAPRODUCT_ABSPATH . "includes/class-f9-wc-product-$type.php";
		}
		$classes = apply_filters( 'f9mediaproduct_product_class_files', $classes );
		foreach ( $classes as $class ) {
			if ( file_exists( $class ) ) {
				require $class;
			}
		}
	}

	/**
	 * Init F9mediaproduct when WordPress Initialises.
	 */
	public function init() {
		// Before init action.
		do_action( 'before_f9mediaproduct_init' );

		// Set up localisation.
		$this->load_plugin_textdomain();

		// Init action.
		do_action( 'f9mediaproduct_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/f9mediaproduct/f9mediaproduct-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/f9mediaproduct-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			// @todo Remove when start supporting WP 5.0 or later.
			$locale = is_admin() ? get_user_locale() : get_locale();
		}

		$locale = apply_filters( 'plugin_locale', $locale, 'f9mediaproduct' );

		unload_textdomain( 'f9mediaproduct' );
		load_textdomain( 'f9mediaproduct', WP_LANG_DIR . '/f9mediaproduct/f9mediaproduct-' . $locale . '.mo' );
		load_plugin_textdomain( 'f9mediaproduct', false, plugin_basename( dirname( F9MEDIAPRODUCT_PLUGIN_FILE ) ) . '/languages' );
		load_textdomain( 'f9mediaproduct', dirname( F9MEDIAPRODUCT_PLUGIN_FILE ) . '/languages/' . $locale . '.mo' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', F9MEDIAPRODUCT_PLUGIN_FILE ) );
	}
}

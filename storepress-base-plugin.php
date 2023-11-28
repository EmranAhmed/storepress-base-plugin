<?php
	/**
	 *  StorePress Base Plugin
	 *
	 * @package    StorePress
	 * @subpackage Base
	 *
	 * @wordpress-plugin
	 * Plugin Name:       StorePress Base Plugin
	 * Plugin URI:        https://storepress.com/storepress-base-plugin/
	 * Description:       A starter WordPress plugin scaffold which comes pre-configured for block development, admin dashboard with settings and standard plugin code.
	 * Version:           1.0.0
	 * Requires at least: 6.3
	 * Requires PHP:      7.4
	 * Author:            Emran Ahmed.
	 * Author URI:        https://storepress.com/
	 * Text Domain:       storepress-base-plugin
	 * License:           GPL v3 or later
	 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
	 * Domain Path:       /languages
	 */

	/**
	 * Bootstrap the plugin.
	 */

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\Base\Plugin;
	use Automattic\WooCommerce\Utilities\FeaturesUtil;


if ( ! defined( 'STOREPRESS_BASE_PLUGIN_FILE' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_FILE', __FILE__ );
}


	// Include the Plugin class.
if ( ! class_exists( '\StorePress\Base\Plugin' ) ) {
	require_once __DIR__ . '/includes/Plugin.php';
}

	/**
	 * The main function that returns the Plugin class
	 *
	 * @return Plugin
	 * @since 1.0.0
	 */
function storepress_base_plugin(): Plugin {
	// Include the main class.

	/**
	 * If plugin dependent with woocommerce.
	 *
	 * @example:
	 * if ( ! class_exists( 'WooCommerce', false ) ) {
	 * return false;
	 * }
	 */

	/**
	 * If plugin has extended version
	 *
	 * @example:
	 * if ( function_exists( 'storepress_base_plugin_pro' ) ) {
	 * return storepress_base_plugin_pro();
	 * }
	 */

	return Plugin::instance();
}

	// Get the plugin running.
	add_action( 'plugins_loaded', 'storepress_base_plugin' );

	/**
	 * Declare compatibility with custom order tables for WooCommerce.
	 *
	 * @example:
	 *
	 * add_action('before_woocommerce_init', function () {
	 * if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
	 * FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	 * }
	 * });
	 */

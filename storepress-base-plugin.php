<?php
	/**
	 *  StorePress Base Plugin
	 *
	 * @package StorePress
	 *
	 * @wordpress-plugin
	 * Plugin Name:       StorePress Base Plugin
	 * Plugin URI:        https://storepress.com
	 * Description:       A starter WordPress plugin scaffold which comes pre-configured for block development, admin dashboard with settings and standard plugin code.
	 * Version:           1.0.0
	 * Requires at least: 6.3
	 * Requires PHP:      7.4
	 * Author:            Emran Ahmed.
	 * Author URI:        https://storepress.com
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


if ( ! defined( 'STOREPRESS_BASE_PLUGIN_VERSION' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_VERSION', '1.0.0' );
}

if ( ! defined( 'STOREPRESS_BASE_PLUGIN_FILE' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'STOREPRESS_BASE_PLUGIN_TEXT_DOMAIN' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_TEXT_DOMAIN', 'storepress-base-plugin' );
}

	// Include the main class.
if ( ! class_exists( '\StorePress\Base\Plugin' ) ) {
	require_once __DIR__ . '/includes/Plugin.php';
}

	/**
	 * Plugin class init
	 *
	 * @return Plugin|false
	 */
function storepress_base_plugin() {
	// Include the main class.

	if ( ! class_exists( 'WooCommerce', false ) ) {
		return false;
	}

	if ( function_exists( 'storepress_base_plugin_pro' ) ) {
		return storepress_base_plugin_pro();
	}

	return Plugin::instance();
}

	add_action( 'plugins_loaded', 'storepress_base_plugin' );

	// Declare compatibility with custom order tables for WooCommerce.
	add_action(
		'before_woocommerce_init',
		function () {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}
	);

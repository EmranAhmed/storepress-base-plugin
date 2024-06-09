<?php
	/**
	 *  StorePress Base Plugin
	 *
	 * @package    StorePress/Base
	 *
	 * @wordpress-plugin
	 * Plugin Name:       StorePress Base Plugin
	 * Plugin URI:        https://storepress.com/storepress-base-plugin/
	 * Description:       A starter WordPress plugin scaffold which comes pre-configured for block development, admin dashboard with settings and standard plugin code.
	 * Version:           1.0.0
	 * Requires at least: 6.4
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


if ( ! defined( 'StorePress\Base\__FILE__' ) ) {
	define( 'StorePress\Base\__FILE__', __FILE__ );
}

	// Include the Plugin class.
if ( ! class_exists( '\StorePress\Base\Plugin' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/includes/Plugin.php';
}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @since 1.0.0
	 */
function storepress_base_plugin_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'StorePress Base Plugin requires WooCommerce to be installed and active. You can download %s here.', 'storepress-base-plugin' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

	/**
	 * The main function that returns the Plugin class
	 *
	 * @return Plugin
	 * @since 1.0.0
	 */
function storepress_base_plugin(): Plugin {

	load_plugin_textdomain( 'storepress-base-plugin', false, plugin_dir_path( __FILE__ ) . 'languages' );
	// Include the main class.

	/**
	 * If plugin dependent with woocommerce.
	 *
	 * @example:
	 * if ( ! class_exists( 'WooCommerce' ) ) {
	 * add_action( 'admin_notices', 'storepress_base_plugin_missing_wc_notice' );
	 * return;
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

<?php
	/**
	 *  StorePress Base Plugin
	 *
	 * @package StorePress
	 * @author  StorePress
	 *
	 * @wordpress-plugin
	 * Plugin Name:       StorePress Base Plugin
	 * Plugin URI:        https://storepress.com
	 * Description:       A starter WordPress plugin scaffold which comes pre-configured for block development, admin dashboard with settings and standard plugin code.
	 * Version:           1.0.0
	 * Requires at least: 6.3
	 * Requires PHP:      8.0
	 * Author:            Emran Ahmed.
	 * Author URI:        https://smalltowndev.com
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

if ( ! defined( 'STOREPRESS_BASE_PLUGIN_VERSION' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_VERSION', '1.0.0' );
}

if ( ! defined( 'STOREPRESS_BASE_PLUGIN_FILE' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_FILE', __FILE__ );
}

	/**
	 * Bootstrap the plugin.
	 */
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';


if ( class_exists( 'StorePress\Base\Plugin' ) ) {
	/**
	 * Plugin class init
	 */
	function storepress_base_plugin_init() {
		// Include the main class.

		return Plugin::instance();
	}

	add_action( 'plugins_loaded', 'storepress_base_plugin_init' );

}

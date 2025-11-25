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

declare( strict_types=1 );

/**
 * Bootstrap the plugin.
 */

defined( 'ABSPATH' ) || die( 'Keep Silent' );

use StorePress\Base\Plugin;

if ( ! defined( 'STOREPRESS_BASE_PLUGIN_FILE' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_FILE', __FILE__ );
}

/**
 * The function that always returns the same instance to ensure only one instance exists in the global scope at any time.
 *
 * @return Plugin
 * @since 1.0.0
 */
function storepress_base_plugin(): Plugin {
	// Include the Plugin class.
	if ( ! class_exists( '\StorePress\Base\Plugin' ) ) {
		require_once plugin_dir_path( __FILE__ ) . '/includes/Plugin.php';
	}

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

/**
 * Init
 *
 * @return void
 * @since 1.0.0
 */
function storepress_base_plugin_init() {
	storepress_base_plugin();
}

// Get the plugin running.
add_action( 'plugins_loaded', 'storepress_base_plugin_init' );

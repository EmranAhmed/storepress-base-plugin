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
use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'STOREPRESS_BASE_PLUGIN_FILE' ) ) {
	define( 'STOREPRESS_BASE_PLUGIN_FILE', __FILE__ );
}

// Include the Plugin class.
if ( ! class_exists( '\StorePress\Base\Plugin' ) ) {
	require_once plugin_dir_path( __FILE__ ) . '/includes/Plugin.php';
}

/**
 * WooCommerce fallback notice.
 *
 * @return void
 * @since 1.0.0
 */
function storepress_base_plugin_missing_wc_notice() {
	$text = esc_html__( 'WooCommerce', 'storepress-base-plugin' );
	if ( current_user_can( 'install_plugins' ) ) {
		$plugin_args = array(
			'tab'       => 'plugin-information',
			'plugin'    => 'woocommerce',
			'TB_iframe' => 'true',
			'width'     => '640',
			'height'    => '500',
		);

		$link = add_query_arg( $plugin_args, admin_url( 'plugin-install.php' ) );

		$message = __( '<strong>StorePress Base Plugin</strong> is an add-on of ', 'storepress-base-plugin' );

		printf( '<div class="notice notice-error"><p>%1$s <a class="thickbox open-plugin-details-modal" href="%2$s"><strong>%3$s</strong></a></p></div>', wp_kses_post( $message ), esc_url( $link ), esc_html( $text ) );
	} else {
		/* translators: %1$s WooCommerce, %2$s WooCommerce download URL link. */
		$message = sprintf( esc_html__( 'StorePress Base Plugin requires %1$s to be installed and active. You can download %2$s here.', 'storepress-base-plugin' ), esc_html( $text ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>' );

		printf( '<div class="error"><p><strong>%s</strong></p></div>', wp_kses_post( $message ) );
	}
}

/**
 * The function that always returns the same instance to ensure only one instance exists in the global scope at any time.
 *
 * @return Plugin
 * @since 1.0.0
 */
function storepress_base_plugin(): Plugin {
	return Plugin::instance();
}

/**
 * Init
 *
 * @return void
 */
function storepress_base_plugin_init() {
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

	storepress_base_plugin();
}

// Get the plugin running.
add_action( 'plugins_loaded', 'storepress_base_plugin_init' );

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

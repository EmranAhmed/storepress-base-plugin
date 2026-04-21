<?php
	/**
	 * Utility Functions.
	 *
	 * @package      StorePress/Base
	 * @since        1.0.0
	 * @version      1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\Base\Containers\Container;

	/**
	 * Returns the plugin service container instance.
	 *
	 * @return Container
	 * @since 1.0.0
	 */
function get_container(): Container {
	return Container::instance();
}

	/**
	 * Returns the plugin main file path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
function get_plugin_file(): string {
	return constant( 'STOREPRESS_BASE_PLUGIN_FILE' );
}

	/**
	 * Returns the pro plugin main file path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
function get_pro_plugin_file(): string {

	if ( defined( 'STOREPRESS_BASE_PLUGIN_PRO_FILE' ) ) {
		return constant( 'STOREPRESS_BASE_PLUGIN_PRO_FILE' );
	}

	return 'storepress-base-plugin-pro/storepress-base-plugin-pro.php';
}

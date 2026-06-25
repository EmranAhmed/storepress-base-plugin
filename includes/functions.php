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

	use StorePress\Base\Integrations\Container;
	use StorePress\Base\Features\Blocks;
	use StorePress\Base\Services\Settings;

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
	 * Returns the Blocks service from the DI container.
	 *
	 * @return Blocks
	 * @since  1.0.0
	 * @see    get_container()
	 */
function get_blocks(): Blocks {
	return get_container()->get( Blocks::class );
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

	/**
	 * Get Settings.
	 *
	 * @return Settings
	 */
function get_settings(): Settings {
	return get_container()->get( Settings::class );
}

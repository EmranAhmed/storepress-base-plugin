<?php
	/**
	 * Plugin File Trait.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	namespace StorePress\Base\Traits;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use function StorePress\Base\get_plugin_file;
	use function StorePress\Base\get_pro_plugin_file;

	/**
	 * Provides plugin file path accessor.
	 */
trait PluginFileTrait {

	/**
	 * Returns the main plugin file path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function plugin_file(): string {
		return get_plugin_file();
	}
}

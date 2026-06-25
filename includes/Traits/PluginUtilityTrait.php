<?php
	/**
	 * Plugin File Trait.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\Traits;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Traits\HelperMethodsTrait;
	use StorePress\AdminUtils\Traits\PluginCommonTrait;
	use StorePress\Base\Integrations\Container;
	use StorePress\Base\Services\BlockSupport;
	use StorePress\Base\Services\Settings;
	use function StorePress\Base\get_plugin_file;
	use function StorePress\Base\get_container;

	/**
	 * Provides plugin file path, DI container, settings, and logging utilities.
	 *
	 * @name PluginUtilityTrait
	 */
trait PluginUtilityTrait {

	use PluginCommonTrait;
	use HelperMethodsTrait;

	/**
	 * Returns the main plugin file path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function plugin_file(): string {
		return get_plugin_file();
	}

	// =====================================================================
	// Container Access Methods
	// =====================================================================

	/**
	 * Returns the plugin's DI container instance.
	 *
	 * @return  Container
	 * @since   1.0.0
	 * @see     plugin_file()
	 * @example $this->get_container()->get( Settings::class );
	 */
	public function get_container(): Container {
		return get_container();
	}

	/**
	 * Returns the plugin settings service.
	 *
	 * @return Settings
	 * @since  1.0.0
	 * @see    get_container()
	 */
	public function get_settings(): Settings {
		return $this->get_container()->get( Settings::class );
	}

	/**
	 * Returns Block Support.
	 *
	 * @param array<string, mixed> $attributes Block attributes array.
	 *
	 * @return BlockSupport
	 * @since  1.0.0
	 */
	public function get_block_support( array $attributes ): BlockSupport {
		return $this->get_container()->get( BlockSupport::class )( $attributes );
	}

	// =====================================================================
	// Logging
	// =====================================================================

	/**
	 * Whether logging is enabled via plugin settings.
	 *
	 * @return bool
	 * @since  1.0.0
	 * @see    get_settings()
	 */
	public function is_log_enabled(): bool {
		return $this->string_to_boolean( $this->get_settings()->get_option( 'is_log_enabled', 'no' ) );
	}
}

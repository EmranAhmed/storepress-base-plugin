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

	use StorePress\AdminUtils\Traits\PluginCommonTrait;
	use StorePress\Base\Containers\Container;
	use function StorePress\Base\get_plugin_file;
	use function StorePress\Base\get_container;

	/**
	 * Provides plugin file path accessor.
	 */
trait PluginUtilityTrait {

	use PluginCommonTrait;

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

	// =====================================================================
	// Logging
	// =====================================================================

	/**
	 * Writes a log entry via WooCommerce logger when WP_DEBUG is enabled.
	 *
	 * @param string                            $title   log title.
	 * @param array<string|int, mixed>|string[] $message log message.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function wc_log( string $title, array $message = array() ): void {
		// If WooCommerce Installed.
		if ( ! function_exists( 'wc_get_logger' ) ) {
			return;
		}

		if ( defined( 'WP_DEBUG' ) && true === constant( 'WP_DEBUG' ) ) {
			$context = array(
				'source' => dirname( plugin_basename( $this->get_plugin_file() ) ),
			);

			wc_get_logger()->info( $title, array_merge( $message, $context ) );
		}
	}

	/**
	 * Returns the WooCommerce log file URL for this plugin.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_log_file_url(): string {

		$query_args = array(
			'page'     => 'wc-status',
			'tab'      => 'logs',
			'log_file' => sprintf( '%s-%s.log', dirname( plugin_basename( $this->get_plugin_file() ) ), sanitize_file_name( wp_hash( dirname( plugin_basename( $this->get_plugin_file() ) ) ) ) ),
		);

		return add_query_arg( $query_args, admin_url( 'admin.php' ) );
	}
}

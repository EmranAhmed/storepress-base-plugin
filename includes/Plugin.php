<?php
	/**
	 * Main Plugin Class File.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use Automattic\WooCommerce\Utilities\FeaturesUtil;
	use StorePress\Base\ServiceProviders\BlocksServiceProvider;
	use StorePress\Base\ServiceProviders\ProCompatibilityServiceProvider;
	use StorePress\Base\ServiceProviders\ServiceProviders;
	use StorePress\Base\ServiceProviders\SettingsServiceProvider;
	use StorePress\Base\ServiceProviders\DeactivationServiceProvider;
	use StorePress\Base\ServiceProviders\UpdaterServiceProvider;

	/**
	 * Class Plugin.
	 *
	 * @name Plugin
	 */
class Plugin {

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Returns the singleton instance.
	 *
	 * @return self
	 * @since 1.0.0
	 */
	public static function instance(): self {
		static $instance = null;

		return $instance ??= new self();
	}

	/**
	 * Bootstraps includes, hooks, and init.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->includes();

		$this->hooks();

		$this->init();
	}

	/**
	 * Loads vendor autoloader and utility functions.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function includes(): void {
		$vendor_path = untrailingslashit( plugin_dir_path( $this->get_plugin_file() ) ) . '/vendor';

		if ( file_exists( $vendor_path . '/autoload_packages.php' ) ) {
			require_once $vendor_path . '/autoload_packages.php';
		}

		require_once __DIR__ . '/functions.php';
	}

	/**
	 * Registers WordPress hooks.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function hooks(): void {
		// Declare HPOS compatibility before WooCommerce initializes.
		add_action( 'before_woocommerce_init', array( $this, 'custom_order_tables_compatibility' ) );
	}

	/**
	 * Initializes plugin service providers.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init(): void {
		$this->service_providers();
	}

	/**
	 * Returns the absolute path to the plugin main file.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_plugin_file(): string {
		return constant( 'STOREPRESS_BASE_PLUGIN_FILE' );
	}

	/**
	 * Returns registered service provider class names.
	 *
	 * @return array<class-string, class-string>
	 * @since 1.0.0
	 */
	public function get_service_providers(): array {
		return array(
			UpdaterServiceProvider::class          => UpdaterServiceProvider::class,
			DeactivationServiceProvider::class     => DeactivationServiceProvider::class,
			SettingsServiceProvider::class         => SettingsServiceProvider::class,
			ProCompatibilityServiceProvider::class => ProCompatibilityServiceProvider::class,
			BlocksServiceProvider::class           => BlocksServiceProvider::class,
		);
	}

	/**
	 * Boots all registered service providers.
	 *
	 * @return ServiceProviders
	 * @since 1.0.0
	 * @see   ServiceProviders::instance()
	 */
	public function service_providers(): ServiceProviders {
		return ServiceProviders::instance( $this->get_service_providers() );
	}

	// =====================================================================
	// Hook Callbacks
	// =====================================================================

	/**
	 * Declare compatibility with custom order tables for WooCommerce.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function custom_order_tables_compatibility(): void {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', $this->get_plugin_file() );
		}
	}
}

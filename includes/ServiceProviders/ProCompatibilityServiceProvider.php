<?php
	/**
	 * Pro Compatibility Service Provider.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\ServiceProviders;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractServiceProvider;
	use StorePress\AdminUtils\Traits\SingletonTrait;
	use StorePress\Base\Containers\Container;
	use StorePress\Base\Integrations\ProPluginInCompatibility;

	/**
	 * Binds the ProPluginInCompatibility service into the container.
	 *
	 * @name ProCompatibilityServiceProvider
	 */
class ProCompatibilityServiceProvider extends AbstractServiceProvider {

	use SingletonTrait;

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Returns the plugin service container.
	 *
	 * @return Container
	 * @since 1.0.0
	 */
	public function get_container(): Container {
		return Container::instance();
	}

	/**
	 * Binds the ProPluginInCompatibility service into the container.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register(): void {

		$this->get_container()->register(
			ProPluginInCompatibility::class,
			function () {
				return ProPluginInCompatibility::instance();
			}
		);
	}

	/**
	 * Boots the ProPluginInCompatibility service (disabled by default).
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function boot(): void {
		$this->get_container()->get( ProPluginInCompatibility::class );
	}
}

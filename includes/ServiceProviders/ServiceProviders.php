<?php
	/**
	 * Service Providers Bootstrap.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\ServiceProviders;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Traits\SingletonTrait;

	/**
	 * Registers and boots all plugin service providers.
	 *
	 * @name ServiceProviders
	 */
class ServiceProviders {

	use SingletonTrait;

	/**
	 * Service providers.
	 *
	 * @var array<class-string, class-string>
	 */
	protected array $service_providers = array();

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Stores and immediately boots the given service providers.
	 *
	 * @param array<class-string, class-string> $service_providers Service providers.
	 *
	 * @since 1.0.0
	 */
	public function __construct( array $service_providers ) {
		$this->service_providers = $service_providers;
		$this->init();
	}

	// =====================================================================
	// Service Provider Registration Methods
	// =====================================================================

	/**
	 * Returns the registered service provider class names.
	 *
	 * @return array<class-string, class-string> Service providers.
	 * @since 1.0.0
	 */
	public function get_providers(): array {
		return $this->service_providers;
	}

	/**
	 * Instantiates, registers, and boots each service provider.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function init(): void {
		$providers = $this->get_providers();


		foreach ( $providers as $provider ) {
			$provider::instance();
			$provider::instance()->register();
			$provider::instance()->boot();
		}
	}
}

<?php
	/**
	 * Deactivation Service Provider.
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
	use StorePress\Base\Integrations\DeactivationFeedback;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Binds and boots the DeactivationFeedback service.
	 *
	 * @name DeactivationServiceProvider
	 */
class DeactivationServiceProvider extends AbstractServiceProvider {

	use SingletonTrait;
	use PluginUtilityTrait;

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Binds the DeactivationFeedback service into the container.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register(): void {

		$this->get_container()->register(
			DeactivationFeedback::class,
			function () {
				return DeactivationFeedback::instance();
			} 
		);
	}

	/**
	 * Resolves and activates the DeactivationFeedback service.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function boot(): void {
		$this->get_container()->get( DeactivationFeedback::class );
	}
}

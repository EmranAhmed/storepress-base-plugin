<?php
	/**
	 * Blocks Service Provider.
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
	use StorePress\Base\Services\Blocks;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Binds and boots the Blocks service.
	 *
	 * @name BlocksServiceProvider
	 */
class BlocksServiceProvider extends AbstractServiceProvider {

	use SingletonTrait;
	use PluginUtilityTrait;

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Binds the Blocks service into the container.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function register(): void {

		$this->get_container()->register(
			Blocks::class,
			function () {
				return Blocks::instance();
			}
		);
	}

	/**
	 * Resolves and activates the Blocks service.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function boot(): void {
		$this->get_container()->get( Blocks::class );
	}
}

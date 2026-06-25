<?php
	/**
	 * Additional Admin Menu Service Provider.
	 *
	 * @package StorePress\Base\ServiceProviders
	 * @since   1.0.0
	 * @version 1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\ServiceProviders;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractServiceProvider;
	use StorePress\AdminUtils\Traits\SingletonTrait;
	use StorePress\Base\Adapters\AdditionalAdminMenu;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Registers and boots the AdditionalAdminMenu service.
	 *
	 * @name AdditionalAdminMenuServiceProvider
	 * @method static AdditionalAdminMenuServiceProvider instance()
	 */
class AdditionalAdminMenuServiceProvider extends AbstractServiceProvider {

	use SingletonTrait;
	use PluginUtilityTrait;

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Binds AdditionalAdminMenu into the container.
	 *
	 * @return void
	 * @since  1.0.0
	 * @see    boot()
	 */
	public function register(): void {

		$this->get_container()->register(
			AdditionalAdminMenu::class,
			function () {
				return AdditionalAdminMenu::instance();
			}
		);
	}

	/**
	 * Resolves and activates the AdditionalAdminMenu service.
	 *
	 * @return void
	 * @since  1.0.0
	 * @see    register()
	 */
	public function boot(): void {
		$this->get_container()->get( AdditionalAdminMenu::class );
	}
}

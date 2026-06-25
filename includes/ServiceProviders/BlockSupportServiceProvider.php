<?php
	/**
	 * Block Support Service Provider Class File.
	 *
	 * Registers the BlockSupports factory into the DI container for on-demand
	 * instantiation with per-block attribute sets.
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
	use StorePress\Base\Services\BlockSupport;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Service provider for the BlockSupports factory.
	 *
	 * Binds a factory closure for {@see BlockSupports} so callers can resolve
	 * a new instance per block by passing an attributes array.
	 *
	 * @name    BlockSupportServiceProvider
	 * @package StorePress/Image_Hotspot_Blocks_Pro
	 * @since   1.0.0
	 *
	 * @phpstan-use SingletonTrait<BlockSupportServiceProvider>
	 *
	 * @example BlockSupportServiceProvider::instance()->register();
	 * @example $factory = $container->get( BlockSupports::class ); $supports = $factory( $attributes );
	 */
class BlockSupportServiceProvider extends AbstractServiceProvider {

	use SingletonTrait;
	use PluginUtilityTrait;

	// =====================================================================
	// Service Lifecycle Methods
	// =====================================================================

	/**
	 * Registers the BlockSupport factory closure into the DI container.
	 *
	 * The resolved value is a callable: `fn( array $attributes ): BlockSupport`.
	 *
	 * @since  1.0.0
	 * @return void
	 * @see    boot()
	 */
	public function register(): void {

		$this->get_container()->register(
			BlockSupport::class,
			function () {
				return static function ( array $attributes ): BlockSupport {
					return new BlockSupport( $attributes );
				};
			}
		);
	}

	/**
	 * Booting.
	 *
	 * @return void
	 */
	public function boot(): void {}
}

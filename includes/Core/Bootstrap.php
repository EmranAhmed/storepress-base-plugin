<?php
	/**
	 * Service Providers Bootstrap.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\Core;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\ServiceProviders\ServiceProviderLoader;
	use StorePress\AdminUtils\Traits\SingletonTrait;

	/**
	 * Registers and boots all plugin service providers.
	 *
	 * @name Bootstrap
	 */
class Bootstrap extends ServiceProviderLoader {

	use SingletonTrait;
}

<?php
	/**
	 * Service Container.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\Containers;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\ServiceContainers\ServiceContainer;
	use StorePress\AdminUtils\Traits\SingletonTrait;

	/**
	 * Plugin service container.
	 *
	 * @name Container
	 */
class Container extends ServiceContainer {
	use SingletonTrait;
}

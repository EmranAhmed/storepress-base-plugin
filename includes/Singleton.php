<?php
/**
 * Common Instance for Classes.
 *
 * @package      StorePress/Base
 * @since        1.0.0
 * @version      1.0.0
 */

declare( strict_types=1 );

namespace StorePress\Base;

defined( 'ABSPATH' ) || die( 'Keep Silent' );

trait Singleton {
	/**
	 * Return singleton instance of Class.
	 * The instance will be created if it does not exist yet.
	 *
	 * @param mixed ...$args Optional arguments to pass to the class constructor on the first call.
	 *
	 * @return self The single instance of the class.
	 * @since 1.0.0
	 */
	public static function instance( ...$args ): self {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new self( ...$args );
		}

		return $instance;
	}
}

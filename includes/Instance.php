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

trait Instance {
	/**
	 * Return singleton instance of Class.
	 * The instance will be created if it does not exist yet.
	 *
	 * @return self The main instance.
	 * @since 1.0.0
	 */
	public static function instance(): self {
		static $instance = null;
		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}
}

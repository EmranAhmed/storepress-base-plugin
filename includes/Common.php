<?php
/**
 * Common Methods for Classes.
 *
 * @package      StorePress/Base
 * @since        1.0.0
 * @version      1.0.0
 */

declare( strict_types=1 );

namespace StorePress\Base;

defined( 'ABSPATH' ) || die( 'Keep Silent' );

use StorePress\AdminUtils\Common as AdminUtilsCommon;

trait Common {
	use AdminUtilsCommon;
}

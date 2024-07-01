<?php
/**
 * Uninstall Plugin
 *
 * Deletes all plugin settings.
 *
 * @package    StorePress/Base
 * @since      1.0.0
 */

declare( strict_types=1 );

namespace StorePress\Base;

/**
 * This file describes all uninstall logic.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$storepress_settings = get_option( 'plugin_settings' );

// Delete all settings.
if ( is_array( $storepress_settings )
	&& isset( $storepress_settings['plugin_settings']['remove_on_uninstall'] )
) {
	delete_option( 'plugin_settings' );
}

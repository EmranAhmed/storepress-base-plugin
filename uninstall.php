<?php
	/**
	 * Uninstall Plugin
	 *
	 * Deletes all plugin settings.
	 *
	 * @package    StorePress
	 * @subpackage Base
	 * @since      1.0.0
	 */

	namespace StorePress\Base;

	// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

	// Load main plugin file.
	require_once 'storepress-base-plugin.php';

	global $wpdb;

	$storepress_settings = get_option( 'plugin_settings' );

	// Delete all Block Visibility settings.
if ( $storepress_settings['plugin_settings']['remove_on_uninstall'] ) {
	delete_option( 'plugin_settings' );
}
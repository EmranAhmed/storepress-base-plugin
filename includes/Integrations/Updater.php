<?php
	/**
	 * Plugin Updater Integration.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\Integrations;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractUpdater;
	use StorePress\AdminUtils\Traits\SingletonTrait;
	use StorePress\Base\Services\Settings;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Handles plugin update checks and rollback via the StorePress update server.
	 *
	 * @name Updater
	 */
class Updater extends AbstractUpdater {

	use SingletonTrait;
	use PluginUtilityTrait;

	/**
	 * Returns the plugin license key.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function license_key(): string {
		return $this->get_container()->get( Settings::class )->get_option( 'license', '' );
	}

	/**
	 * Returns the product ID on the update server.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function product_id(): int {
		return 123450;
	}

	/**
	 * Returns the update server endpoint path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function update_server_path(): string {
		return '/wp-json/plugins/v1/check-update';
	}

	/**
	 * Returns localized UI strings for the updater/rollback UI.
	 *
	 * @return array<string, string>
	 * @since 1.0.0
	 */
	public function localize_strings(): array {

		$name = $this->get_plugin_name();

		return array(
			'license_key_empty_message'     => esc_html__( 'License key is not available.', 'storepress-base-plugin' ),
			'check_update_link_text'        => esc_html__( 'Check Update', 'storepress-base-plugin' ),
			'rollback_changelog_title'      => esc_html__( 'Changelog', 'storepress-base-plugin' ),
			'rollback_action_running'       => esc_html__( 'Rolling back', 'storepress-base-plugin' ),
			/* translators: %s: Plugin name. */
			'rollback_action_button'        => sprintf( esc_html__( 'Rollback %s', 'storepress-base-plugin' ), $name ),
			'rollback_cancel_button'        => esc_html__( 'Cancel', 'storepress-base-plugin' ),
			'rollback_current_version'      => esc_html__( 'Current version', 'storepress-base-plugin' ),
			/* translators: %s: Time elapsed (e.g. "2 hours"). */
			'rollback_last_updated'         => esc_html__( 'Last updated %s ago.', 'storepress-base-plugin' ),
			/* translators: %s: Plugin name. */
			'rollback_view_changelog'       => sprintf( esc_html__( 'View Changelog for %s', 'storepress-base-plugin' ), $name ),
			/* translators: %s: Plugin name. */
			'rollback_page_title'           => sprintf( esc_html__( 'Rollback Plugin %s', 'storepress-base-plugin' ), $name ),
			'rollback_link_text'            => esc_html__( 'Rollback', 'storepress-base-plugin' ),
			'rollback_failed'               => esc_html__( 'Rollback failed.', 'storepress-base-plugin' ),
			/* translators: 1: Plugin name, 2: Version number. */
			'rollback_success'              => esc_html__( 'Rollback success: %1$s rolled back to version %2$s.', 'storepress-base-plugin' ),
			'rollback_plugin_not_available' => esc_html__( 'Plugin is not available.', 'storepress-base-plugin' ),
			'rollback_no_access'            => esc_html__( 'Sorry, you are not allowed to rollback plugins for this site.', 'storepress-base-plugin' ),
			/* translators: %s: Plugin name. */
			'rollback_not_available'        => esc_html__( 'Rollback is not available for plugin: %s', 'storepress-base-plugin' ),
			'rollback_no_target_version'    => esc_html__( 'Plugin version not selected.', 'storepress-base-plugin' ),
		);
	}
}

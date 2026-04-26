<?php
	/**
	 * Plugin Updater Integration.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

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
		return $this->get_container()->get( Settings::class )->get_option( 'license' );
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
		return '/storepress-admin-utils/wp-json/plugin-updater/v1/check-update';
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
			'license_key_empty_message'     => 'License key is not available.',
			'check_update_link_text'        => 'Check Update',
			'rollback_changelog_title'      => 'Changelog',
			'rollback_action_running'       => 'Rolling back',
			'rollback_action_button'        => sprintf( 'Rollback %s', $name ),
			'rollback_cancel_button'        => 'Cancel',
			'rollback_current_version'      => 'Current version',
			'rollback_last_updated'         => 'Last updated %s ago.',
			'rollback_view_changelog'       => sprintf( 'View Changelog for %s', $name ),
			'rollback_page_title'           => sprintf( 'Rollback Plugin %s', $name ),
			'rollback_link_text'            => 'Rollback',
			'rollback_failed'               => 'Rollback failed.',
			'rollback_success'              => 'Rollback success: %s rolled back to version %s.',
			'rollback_plugin_not_available' => 'Plugin is not available.',
			'rollback_no_access'            => 'Sorry, you are not allowed to rollback plugins for this site.',
			'rollback_not_available'        => 'Rollback is not available for plugin: %s',
			'rollback_no_target_version'    => 'Plugin version not selected.',
		);
	}

	/**
	 * Returns additional arguments sent to the update server.
	 *
	 * @return array<string, mixed>
	 * @since 1.0.0
	 */
	public function additional_request_args(): array {
		return array( 'domain' => $this->get_client_hostname() );
	}
}

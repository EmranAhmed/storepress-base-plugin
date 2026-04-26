<?php
	/**
	 * Admin Settings Page Integration.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	namespace StorePress\Base\Integrations;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractSettings;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Registers and renders the plugin admin settings page.
	 *
	 * @name AdminPage
	 */
class AdminPage extends AbstractSettings {

	use PluginUtilityTrait;

	/**
	 * Returns the top-level admin menu title.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_menu_title(): string {
		return 'StorePress';
	}

	/**
	 * Returns the HTML page title.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_page_title(): string {
		return sprintf( '%s - Settings', $this->get_plugin_name() );
	}

	/**
	 * Returns the submenu page title shown in the admin menu.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_page_menu_title(): string {
		return sprintf( '%s', $this->get_plugin_name() );
	}

	/**
	 * Returns the unique settings page slug/id.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function settings_id(): string {
		return 'plugin-b-settings';
	}

	/**
	 * Renders the default settings sidebar template.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function get_default_sidebar(): void {

		include_once $this->templates_path() . '/settings-sidebar.php';
	}

	/**
	 * Returns localized UI strings for the settings page JS.
	 *
	 * @return array<string, string>
	 * @since 1.0.0
	 */
	public function localize_strings(): array {
		return array(
			'unsaved_warning_text'            => 'The changes you made will be lost if you navigate away from this page.',
			'reset_warning_text'              => 'Are you sure to reset?',
			'reset_button_text'               => 'Reset All',
			'settings_nav_label_text'         => 'Secondary menu',
			'settings_link_text'              => 'Settings',
			'settings_error_message_text'     => 'Settings not saved',
			'settings_updated_message_text'   => 'Settings Saved',
			'settings_deleted_message_text'   => 'Settings Reset',
			'settings_tab_not_available_text' => 'Settings Tab is not available.',
		);
	}
}

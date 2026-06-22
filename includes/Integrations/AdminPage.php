<?php
	/**
	 * Admin Settings Page Integration.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	declare( strict_types=1 );

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
		return esc_html__( 'StorePress', 'storepress-base-plugin' );
	}

	/**
	 * Returns the HTML page title.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_page_title(): string {
		return sprintf(
			/* translators: %s: Plugin name. */
			esc_html__( '%s - Settings', 'storepress-base-plugin' ),
			$this->get_plugin_name()
		);
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
		return sprintf( '%s_settings', str_ireplace( '-', '_', $this->get_plugin_slug() ) );
	}

	/**
	 * Renders the default settings sidebar template.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function get_default_sidebar(): void {

		$this->load_template( 'settings-sidebar.php', array( 'instance' => $this ), true );
	}

	/**
	 * Returns localized UI strings for the settings page JS.
	 *
	 * @return array<string, string>
	 * @since 1.0.0
	 */
	public function localize_strings(): array {
		return array(
			'unsaved_warning_text'            => esc_html__( 'The changes you made will be lost if you navigate away from this page.', 'storepress-base-plugin' ),
			'reset_warning_text'              => esc_html__( 'Are you sure to reset?', 'storepress-base-plugin' ),
			'reset_button_text'               => esc_html__( 'Reset All', 'storepress-base-plugin' ),
			'settings_link_text'              => esc_html__( 'Settings', 'storepress-base-plugin' ),
			'settings_error_message_text'     => esc_html__( 'Settings not saved', 'storepress-base-plugin' ),
			'settings_updated_message_text'   => esc_html__( 'Settings Saved', 'storepress-base-plugin' ),
			'settings_deleted_message_text'   => esc_html__( 'Settings Reset', 'storepress-base-plugin' ),
			'settings_tab_not_available_text' => esc_html__( 'Settings Tab is not available.', 'storepress-base-plugin' ),
			'method_called_before_init'       => esc_html__( 'This method should not be called before init.', 'storepress-base-plugin' ),
		);
	}
}

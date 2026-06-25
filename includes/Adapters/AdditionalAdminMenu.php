<?php
	/**
	 * Additional Admin Menu Integration.
	 *
	 * @package StorePress\Base\Integrations
	 * @since   1.0.0
	 * @version 1.0.0
	 */

	declare( strict_types=1 );

	namespace StorePress\Base\Adapters;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractAdminMenu;
	use StorePress\AdminUtils\Traits\SingletonTrait;
	use StorePress\Base\Traits\PluginUtilityTrait;

	/**
	 * Additional admin menu page for the StorePress Base plugin.
	 *
	 * @name AdditionalAdminMenu
	 * @method static AdditionalAdminMenu instance()
	 */
class AdditionalAdminMenu extends AbstractAdminMenu {

	use SingletonTrait;
	use PluginUtilityTrait;

	/**
	 * Page slug used to identify this admin page.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function get_page_slug(): string {
		return 'additional';
	}

	/**
	 * Browser tab / page heading title.
	 *
	 * @return string
	 * @since  1.0.0
	 * @see    get_page_menu_title()
	 */
	public function get_page_title(): string {
		return esc_html__( 'Additional Admin Menu', 'storepress-base-plugin' );
	}

	/**
	 * WordPress admin sidebar menu label.
	 *
	 * @return string
	 * @since  1.0.0
	 * @see    get_page_title()
	 */
	public function get_page_menu_title(): string {
		return esc_html__( 'Additional Menu', 'storepress-base-plugin' );
	}

	/**
	 * Outputs the HTML content for this admin page.
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function render(): void {

		echo '<div class="wrap"><h2>' . esc_html( get_admin_page_title() ) . '</h2></div>';
	}
}

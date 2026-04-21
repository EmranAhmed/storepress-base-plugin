<?php
	/**
	 * Pro Plugin Incompatibility Integration.
	 *
	 * @package    StorePress/Base
	 * @since      1.0.0
	 * @version    1.0.0
	 */

	namespace StorePress\Base\Integrations;

	defined( 'ABSPATH' ) || die( 'Keep Silent' );

	use StorePress\AdminUtils\Abstracts\AbstractProPluginInCompatibility;
	use StorePress\AdminUtils\Traits\SingletonTrait;
	use function StorePress\Base\get_pro_plugin_file;

	/**
	 * Detects and handles incompatible pro plugin versions.
	 *
	 * @name ProPluginInCompatibility
	 */
class ProPluginInCompatibility extends AbstractProPluginInCompatibility {

	use SingletonTrait;

	/**
	 * Returns the minimum compatible pro plugin version.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function compatible_version(): string {
		return '2.0.0';
	}

	/**
	 * Returns the incompatibility notice format string.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function localize_notice_format(): string {
		// translators: 1: Extended Plugin Name. 2: Extended Plugin Version. 3: Extended Plugin Compatible Version.
		return 'You are using an incompatible version of <strong>%1$s - (%2$s)</strong>. Please upgrade to version <strong>%3$s</strong> or upper.';
	}

	/**
	 * Returns the pro plugin main file path.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function pro_plugin_file(): string {
		return get_pro_plugin_file();
	}
}
